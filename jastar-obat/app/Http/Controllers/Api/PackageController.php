<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Transaction; 
use App\Models\Work;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::with('customer')->latest()->get();
        return response()->json(['status' => 'success', 'data' => $packages], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id'       => 'required|exists:customers,id',
            'deskripsi_pesanan' => 'required|string',
            'status_pengiriman' => 'nullable|in:Pesanan diverifikasi,Pengemasan,Menunggu Driver,Diperjalanan,Terkirim,Dibatalkan',
            'status_pembayaran' => 'nullable|in:Belum Lunas,Lunas',
            'metode_pembayaran' => 'nullable|string',
            'jarak_km'          => 'nullable|numeric',
            'total_harga'       => 'nullable|numeric'
        ]);

        if ($validator->fails()) return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);

        try {
            $package = Package::create([
                'customer_id'       => $request->customer_id,
                'deskripsi_pesanan' => $request->deskripsi_pesanan,
                'status_pengiriman' => $request->status_pengiriman ?? 'Pesanan diverifikasi',
                'status_pembayaran' => $request->status_pembayaran ?? 'Belum Lunas',
                'metode_pembayaran' => $request->metode_pembayaran ?? 'Tunai / Cash',
                'jarak_km'          => $request->jarak_km,
                'total_harga'       => $request->total_harga
            ]);

            $package->load('customer');
            return response()->json(['status' => 'success', 'data' => $package], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $package = Package::with(['customer', 'work'])->find($id);
        if (!$package) return response()->json(['status' => 'error', 'message' => 'Paket tidak ditemukan'], 404);
        return response()->json(['status' => 'success', 'data' => $package], 200);
    }

    public function update(Request $request, $id)
    {
        $package = Package::find($id);
        if (!$package) return response()->json(['status' => 'error', 'message' => 'Paket tidak ditemukan'], 404);

        $validator = Validator::make($request->all(), [
            'customer_id'       => 'sometimes|required|exists:customers,id',
            'deskripsi_pesanan' => 'sometimes|required|string',
            'status_pengiriman' => 'sometimes|required',
            'status_pembayaran' => 'sometimes|required',
            'metode_pembayaran' => 'sometimes|required'
        ]);

        if ($validator->fails()) return response()->json(['status' => 'error', 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);

        try {
            $package->update($request->all());
            $package->load('customer');

            // LOGIKA PEMBATALAN: Hapus Work dan Bebaskan Kendaraan
            if ($package->status_pengiriman === 'Dibatalkan') {
                $work = Work::where('package_id', $package->id)->first();
                if ($work) {
                    if ($work->vehicle_id) Vehicle::where('id', $work->vehicle_id)->update(['status' => 'Tersedia']);
                    $work->delete();
                }
            }

            // SINKRONISASI TRANSAKSI KEUANGAN DENGAN STATUS PAKET
            $isLunas = $package->status_pembayaran === 'Lunas';
            $isTerkirim = $package->status_pengiriman === 'Terkirim';
            $deskripsi = 'Pendapatan Paket #PKT-' . str_pad($package->id, 4, '0', STR_PAD_LEFT);
            $nominal = $package->total_harga ?? 0;

            if ($isLunas && $isTerkirim) {
                // Jika valid (Lunas & Terkirim), Cek apakah transaksi sudah ada
                $trx = Transaction::where('deskripsi', $deskripsi)->first();
                
                if (!$trx) {
                    // Buat Baru
                    Transaction::create([
                        'deskripsi'         => $deskripsi,
                        'nominal'           => $nominal,
                        'tipe'              => 'Uang Masuk',
                        'metode_pembayaran' => $package->metode_pembayaran ?? 'Tunai / Cash'
                    ]);
                } else {
                    // Update yang sudah ada (jaga-jaga user mengubah harga/metode di paket)
                    $trx->update([
                        'nominal'           => $nominal,
                        'metode_pembayaran' => $package->metode_pembayaran ?? 'Tunai / Cash'
                    ]);
                }
            } else {
                // Jika status paket dikembalikan menjadi belum lunas / dibatalkan, hapus dari buku kas!
                Transaction::where('deskripsi', $deskripsi)->delete();
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Paket berhasil diupdate dan transaksi telah disesuaikan',
                'data'    => $package
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mengupdate paket: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $package = Package::find($id);
        if (!$package) return response()->json(['status' => 'error', 'message' => 'Paket tidak ditemukan'], 404);

        try {
            // Hapus Work & Lepaskan Kendaraan
            $work = Work::where('package_id', $id)->first();
            if ($work && $work->vehicle_id) {
                Vehicle::where('id', $work->vehicle_id)->update(['status' => 'Tersedia']);
            }
            
            // HAPUS TRANSAKSI KEUANGAN YANG BERKAITAN (Sinkronisasi)
            $deskripsi = 'Pendapatan Paket #PKT-' . str_pad($id, 4, '0', STR_PAD_LEFT);
            Transaction::where('deskripsi', $deskripsi)->delete();

            // Hapus Paket
            $package->delete();

            return response()->json(['status' => 'success', 'message' => 'Dihapus'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}