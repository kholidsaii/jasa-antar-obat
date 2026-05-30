<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Work;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WorkController extends Controller
{
    public function index()
    {
        // Mengambil data penugasan lengkap dengan detail relasinya
        $works = Work::with(['package.customer', 'courier', 'vehicle'])->latest()->get();
        
        return response()->json([
            'status' => 'success',
            'data'   => $works
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'package_id'   => 'required|exists:packages,id',
            'user_id'      => 'required|exists:users,id',
            'vehicle_id'   => 'required|exists:vehicles,id',
            'harga_ongkos' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        try {
            // 1. Buat Penugasan Baru
            $work = Work::create($request->all());

            // 2. Update status paket menjadi "Menunggu Driver" atau "Diperjalanan"
            $package = Package::find($request->package_id);
            if ($package) {
                $package->update(['status_pengiriman' => 'Menunggu Driver']);
            }

            // 3. Load relasi agar response utuh untuk frontend
            $work->load(['package.customer', 'courier', 'vehicle']);

            return response()->json([
                'status'  => 'success',
                'message' => 'Pekerjaan berhasil ditugaskan',
                'data'    => $work
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $work = Work::find($id);
        
        if (!$work) {
            return response()->json(['status' => 'error', 'message' => 'Pekerjaan tidak ditemukan'], 404);
        }

        try {
            // Kembalikan status paket jika tugas dibatalkan
            $package = Package::find($work->package_id);
            if ($package) {
                $package->update(['status_pengiriman' => 'Pesanan diverifikasi']);
            }

            $work->delete();

            return response()->json(['status' => 'success', 'message' => 'Pekerjaan berhasil dibatalkan'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}