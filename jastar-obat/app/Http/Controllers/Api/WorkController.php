<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Work;
use App\Models\Package;
use App\Models\Vehicle; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class WorkController extends Controller
{
    public function index()
    {
        $works = Work::with(['package.customer', 'courier', 'vehicle'])->latest()->get();
        return response()->json(['status' => 'success', 'data' => $works], 200);
    }

    public function store(Request $request)
    {
        // Validasi sekarang menggunakan array package_ids
        $validator = Validator::make($request->all(), [
            'package_ids'   => 'required|array|min:1',
            'package_ids.*' => 'exists:packages,id',
            'user_id'      => 'required|exists:users,id',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $vehicle = Vehicle::where('user_id', $request->user_id)->first();
            if (!$vehicle) {
                return response()->json(['status' => 'error', 'message' => 'Kurir ini tidak memiliki kendaraan'], 422);
            }

            $createdWorks = [];
            
            // Looping sebanyak paket yang dicentang
            foreach ($request->package_ids as $packageId) {
                $work = Work::create([
                    'package_id'   => $packageId,
                    'user_id'      => $request->user_id,
                    'vehicle_id'   => $vehicle->id,
                    // harga_ongkos dihilangkan
                ]);
                
                // Ubah status paket
                Package::where('id', $packageId)->update(['status_pengiriman' => 'Menunggu Driver']);
                $createdWorks[] = $work;
            }

            // Ubah status kendaraan
            $vehicle->update(['status' => 'Sedang Digunakan']);

            DB::commit();
            return response()->json([
                'status'  => 'success', 
                'message' => 'Berhasil menugaskan ' . count($createdWorks) . ' paket ke kurir.'
            ], 201);

        } catch (\Exception $e) { 
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500); 
        }
    }

    public function update(Request $request, $id)
    {
        $work = Work::with('package')->find($id);
        if (!$work) return response()->json(['status' => 'error', 'message' => 'Pekerjaan tidak ditemukan'], 404);

        // --- MENYELESAIKAN TUGAS ---
        if ($request->has('action') && $request->action === 'complete') {
            try {
                if ($work->package) $work->package->update(['status_pengiriman' => 'Terkirim']);
                
                // Cek jika kurir ini tidak punya tugas lain yang belum selesai, bebaskan kendaraannya
                $pendingWorks = Work::where('user_id', $work->user_id)
                    ->whereHas('package', function($q) {
                        $q->where('status_pengiriman', '!=', 'Terkirim');
                    })->count();

                if ($pendingWorks === 0 && $work->vehicle_id) {
                    Vehicle::where('id', $work->vehicle_id)->update(['status' => 'Tersedia']);
                }

                return response()->json(['status' => 'success', 'message' => 'Tugas Selesai!']);
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
        }

        // --- GANTI KURIR ---
        $validator = Validator::make($request->all(), ['user_id' => 'sometimes|required|exists:users,id']);
        if ($validator->fails()) return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);

        try {
            if ($request->has('user_id') && $work->user_id != $request->user_id) {
                $newVehicle = Vehicle::where('user_id', $request->user_id)->first();
                if (!$newVehicle) throw new \Exception('Kurir pengganti tidak memiliki kendaraan.');

                $oldVehicleId = $work->vehicle_id;
                $work->update(['user_id' => $request->user_id, 'vehicle_id' => $newVehicle->id]);

                // Bebaskan kendaraan lama JIKA tidak ada tugas lain, gunakan kendaraan baru
                $oldVehiclePending = Work::where('vehicle_id', $oldVehicleId)->count();
                if ($oldVehiclePending === 0 && $oldVehicleId) {
                    Vehicle::where('id', $oldVehicleId)->update(['status' => 'Tersedia']);
                }
                Vehicle::where('id', $newVehicle->id)->update(['status' => 'Sedang Digunakan']);
            }
            return response()->json(['status' => 'success', 'data' => $work], 200);
        } catch (\Exception $e) { return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500); }
    }

    public function destroy($id)
    {
        $work = Work::find($id);
        if (!$work) return response()->json(['status' => 'error', 'message' => 'Pekerjaan tidak ditemukan'], 404);

        try {
            $vehicleId = $work->vehicle_id;
            $userId = $work->user_id;

            if ($work->package) $work->package->update(['status_pengiriman' => 'Pesanan diverifikasi']);
            $work->delete();

            // Cek apakah kurir masih punya tugas lain, jika tidak, set motor jadi Tersedia
            $remainingWorks = Work::where('user_id', $userId)->count();
            if ($remainingWorks === 0 && $vehicleId) {
                Vehicle::where('id', $vehicleId)->update(['status' => 'Tersedia']);
            }

            return response()->json(['status' => 'success', 'message' => 'Pekerjaan dibatalkan.'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}