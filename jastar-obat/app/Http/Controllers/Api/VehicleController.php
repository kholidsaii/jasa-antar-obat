<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with('user')->latest()->get();
        return response()->json(['status' => 'success', 'data' => $vehicles], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kendaraan'    => 'required|string|max:255',
            'plat_nomor'        => 'required|string|max:50|unique:vehicles',
            'jenis_kepemilikan' => 'required|string|in:Pribadi,Kantor',
            'user_id'           => 'nullable|exists:users,id',
            'status'            => 'nullable|string|in:Tersedia,Sedang Digunakan,Perbaikan'
        ]);

        if ($validator->fails()) return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);

        try {
            $vehicle = Vehicle::create([
                'user_id'           => $request->user_id,
                'nama_kendaraan'    => $request->nama_kendaraan,
                'plat_nomor'        => $request->plat_nomor,
                'jenis_kepemilikan' => $request->jenis_kepemilikan,
                'status'            => $request->status ?? 'Tersedia'
            ]);

            return response()->json(['status' => 'success', 'data' => $vehicle], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::find($id);
        if (!$vehicle) return response()->json(['status' => 'error', 'message' => 'Kendaraan tidak ditemukan'], 404);

        $validator = Validator::make($request->all(), [
            'nama_kendaraan'    => 'sometimes|required|string|max:255',
            'plat_nomor'        => 'sometimes|required|string|max:50|unique:vehicles,plat_nomor,' . $id,
            'jenis_kepemilikan' => 'sometimes|required|string|in:Pribadi,Kantor',
            'user_id'           => 'nullable|exists:users,id',
            'status'            => 'sometimes|required|string|in:Tersedia,Sedang Digunakan,Perbaikan'
        ]);

        if ($validator->fails()) return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);

        try {
            $vehicle->update($request->all());
            return response()->json(['status' => 'success', 'data' => $vehicle], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $vehicle = Vehicle::find($id);
        if (!$vehicle) return response()->json(['status' => 'error', 'message' => 'Tidak ditemukan'], 404);

        try {
            $vehicle->delete();
            return response()->json(['status' => 'success', 'message' => 'Berhasil dihapus'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}