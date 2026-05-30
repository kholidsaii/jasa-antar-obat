<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PackageController extends Controller
{
    // Mengambil semua paket beserta data customer-nya (Eager Loading)
    public function index()
    {
        $packages = Package::with('customer')->latest()->get();
        
        return response()->json([
            'status'  => 'success',
            'message' => 'Data paket berhasil diambil',
            'data'    => $packages
        ], 200);
    }

    // Menyimpan paket baru
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id'       => 'required|exists:customers,id',
            'deskripsi_pesanan' => 'required|string',
            'status_pengiriman' => 'nullable|in:Pesanan diverifikasi,Pengemasan,Menunggu Driver,Diperjalanan,Terkirim',
            'status_pembayaran' => 'nullable|in:Belum Lunas,Lunas'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $package = Package::create([
                'customer_id'       => $request->customer_id,
                'deskripsi_pesanan' => $request->deskripsi_pesanan,
                'status_pengiriman' => $request->status_pengiriman ?? 'Pesanan diverifikasi',
                'status_pembayaran' => $request->status_pembayaran ?? 'Belum Lunas',
            ]);

            // Load data customer agar response utuh
            $package->load('customer');

            return response()->json([
                'status'  => 'success',
                'message' => 'Paket berhasil ditambahkan',
                'data'    => $package
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menambahkan paket: ' . $e->getMessage()
            ], 500);
        }
    }

    // Menampilkan detail satu paket beserta customer dan info penugasannya
    public function show($id)
    {
        $package = Package::with(['customer', 'work'])->find($id);

        if (!$package) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Paket tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Detail paket berhasil diambil',
            'data'    => $package
        ], 200);
    }

    // Mengupdate data paket (misal: update status pengiriman)
    public function update(Request $request, $id)
    {
        $package = Package::find($id);

        if (!$package) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Paket tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'customer_id'       => 'sometimes|required|exists:customers,id',
            'deskripsi_pesanan' => 'sometimes|required|string',
            'status_pengiriman' => 'sometimes|required|in:Pesanan diverifikasi,Pengemasan,Menunggu Driver,Diperjalanan,Terkirim',
            'status_pembayaran' => 'sometimes|required|in:Belum Lunas,Lunas'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $package->update($request->all());
            $package->load('customer');

            return response()->json([
                'status'  => 'success',
                'message' => 'Paket berhasil diupdate',
                'data'    => $package
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mengupdate paket: ' . $e->getMessage()
            ], 500);
        }
    }

    // Menghapus paket
    public function destroy($id)
    {
        $package = Package::find($id);

        if (!$package) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Paket tidak ditemukan'
            ], 404);
        }

        try {
            $package->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Paket berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menghapus paket: ' . $e->getMessage()
            ], 500);
        }
    }
}