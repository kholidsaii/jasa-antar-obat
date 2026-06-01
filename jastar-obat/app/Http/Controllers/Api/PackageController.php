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
            'status_pengiriman' => 'nullable|string',
            'status_pembayaran' => 'nullable|string',
            'metode_pembayaran' => 'nullable|string',
            'jarak_km'          => 'nullable|numeric',
            'total_harga'       => 'nullable|numeric' // Admin bisa kirim custom harga
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

        try {
            $package->update($request->all());
            $package->load('customer');

            if ($package->status_pengiriman === 'Dibatalkan') {
                $work = Work::where('package_id', $package->id)->first();
                if ($work) {
                    if ($work->vehicle_id) Vehicle::where('id', $work->vehicle_id)->update(['status' => 'Tersedia']);
                    $work->delete();
                }
            }

            // --- SINKRONISASI BUKU KAS GANDA ---
            $isLunas = $package->status_pembayaran === 'Lunas';
            $isTerkirim = $package->status_pengiriman === 'Terkirim';
            $kodeUnik = '#PKT-' . str_pad($package->id, 4, '0', STR_PAD_LEFT);
            $nominalTotal = $package->total_harga ?? 0;
            $metode = $package->metode_pembayaran ?? 'Tunai / Cash (Sistem)';

            if ($isLunas && $isTerkirim) {
                Transaction::where('deskripsi', 'LIKE', "%{$kodeUnik}%")->delete();

                if (strtolower($metode) === 'gratis / amal') {
                    Transaction::create([
                        'deskripsi'         => 'Sedekah / Gratis Paket ' . $kodeUnik,
                        'nominal'           => $nominalTotal,
                        'tipe'              => 'Uang Masuk',
                        'metode_pembayaran' => 'Gratis / Amal'
                    ]);
                } else {
                    $biayaAdmin = 1500;
                    $biayaDasar = max(0, $nominalTotal - $biayaAdmin);

                    // AUTO-ROUTING TRANSFER: Jika Transfer, uang langsung masuk ke Rek Budi (Real).
                    // Jika Cash/QRIS, uang tertahan di tangan kurir (Sistem).
                    $metodeDasar = str_contains(strtolower($metode), 'transfer') 
                                   ? 'Rek. Bank Budi (Operasional)' 
                                   : $metode;

                    // Transaksi 1: Biaya Dasar (Pendapatan Operasional)
                    Transaction::create([
                        'deskripsi'         => 'Pendapatan Dasar ' . $kodeUnik,
                        'nominal'           => $biayaDasar,
                        'tipe'              => 'Uang Masuk',
                        'metode_pembayaran' => $metodeDasar
                    ]);

                    // Transaksi 2: Biaya Admin (Sistem - menunggu dimutasi ke Rek Syamil)
                    Transaction::create([
                        'deskripsi'         => 'Biaya Admin ' . $kodeUnik,
                        'nominal'           => $biayaAdmin,
                        'tipe'              => 'Uang Masuk',
                        'metode_pembayaran' => 'Biaya Admin (Sistem)'
                    ]);
                }
            } else {
                Transaction::where('deskripsi', 'LIKE', "%{$kodeUnik}%")->delete();
            }

            return response()->json(['status' => 'success', 'data' => $package], 200);

        } catch (\Exception $e) {
            return response()->json(['status'  => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $package = Package::find($id);
        if (!$package) return response()->json(['status' => 'error', 'message' => 'Paket tidak ditemukan'], 404);

        try {
            $work = Work::where('package_id', $id)->first();
            if ($work && $work->vehicle_id) Vehicle::where('id', $work->vehicle_id)->update(['status' => 'Tersedia']);
            
            $kodeUnik = '#PKT-' . str_pad($id, 4, '0', STR_PAD_LEFT);
            Transaction::where('deskripsi', 'LIKE', "%{$kodeUnik}%")->delete();

            $package->delete();
            return response()->json(['status' => 'success', 'message' => 'Dihapus'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}