<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VehicleController extends Controller
{
    // 1. Mengambil semua data kendaraan (GET)
    public function index()
    {
        // Gunakan with('user') agar data pemilik (kurir) ikut diload
        $vehicles = Vehicle::with('user')->latest()->get();
        
        return response()->json([
            'status' => 'success',
            'data'   => $vehicles
        ], 200);
    }

    // 2. Menyimpan kendaraan baru (POST)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kendaraan' => 'required|string|max:255',
            'plat_nomor'     => 'required|string|max:50|unique:vehicles',
            'status'         => 'nullable|string|in:Tersedia,Sedang Digunakan,Perbaikan'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $vehicle = Vehicle::create([
                'nama_kendaraan' => $request->nama_kendaraan,
                'plat_nomor'     => $request->plat_nomor,
                'status'         => $request->status ?? 'Tersedia'
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Kendaraan berhasil ditambahkan',
                'data'    => $vehicle
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menambahkan kendaraan: ' . $e->getMessage()
            ], 500);
        }
    }

    // 3. Menampilkan detail satu kendaraan (GET by ID)
    public function show($id)
    {
        $vehicle = Vehicle::find($id);
        if (!$vehicle) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kendaraan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $vehicle
        ], 200);
    }

    // 4. Mengupdate data kendaraan (PUT)
    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::find($id);
        if (!$vehicle) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kendaraan tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_kendaraan' => 'sometimes|required|string|max:255',
            // Unik melompati id dirinya sendiri agar plat nomor tidak bentrok saat di-update
            'plat_nomor'     => 'sometimes|required|string|max:50|unique:vehicles,plat_nomor,' . $id,
            'status'         => 'sometimes|required|string|in:Tersedia,Sedang Digunakan,Perbaikan'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $vehicle->update($request->all());
            return response()->json([
                'status'  => 'success',
                'message' => 'Kendaraan berhasil diupdate',
                'data'    => $vehicle
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mengupdate kendaraan: ' . $e->getMessage()
            ], 500);
        }
    }

    // 5. Menghapus data kendaraan (DELETE)
    public function destroy($id)
    {
        $vehicle = Vehicle::find($id);
        if (!$vehicle) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kendaraan tidak ditemukan'
            ], 404);
        }

        try {
            $vehicle->delete();
            return response()->json([
                'status'  => 'success',
                'message' => 'Kendaraan berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menghapus kendaraan: ' . $e->getMessage()
            ], 500);
        }
    }
}