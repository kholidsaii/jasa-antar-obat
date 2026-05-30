<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Transaction; // Import model Transaction
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
            'status_pengiriman' => 'nullable|in:Pesanan diverifikasi,Pengemasan,Menunggu Driver,Diperjalanan,Terkirim',
            'status_pembayaran' => 'nullable|in:Belum Lunas,Lunas',
            'metode_pembayaran' => 'nullable|string' // Validasi baru
        ]);

        if ($validator->fails()) return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);

        try {
            $package = Package::create([
                'customer_id'       => $request->customer_id,
                'deskripsi_pesanan' => $request->deskripsi_pesanan,
                'status_pengiriman' => $request->status_pengiriman ?? 'Pesanan diverifikasi',
                'status_pembayaran' => $request->status_pembayaran ?? 'Belum Lunas',
                'metode_pembayaran' => $request->metode_pembayaran ?? 'Tunai / Cash', // Set default
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

        if (!$package) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Paket tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'customer_id'       => 'sometimes|required|exists:customers,id',
            'deskripsi_pesanan' => 'sometimes|required|string',
            'status_pengiriman' => 'sometimes|required',
            'status_pembayaran' => 'sometimes|required',
            'metode_pembayaran' => 'sometimes|required'
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
            // Load relasi customer untuk mendapatkan koordinat lat & lng pasien
            $package->load('customer');

            // OTOMATISASI TRANSAKSI BIAYA TOTAL (ONGKIR + ADMIN)
            $isLunas = $package->status_pembayaran === 'Lunas';
            $isTerkirim = $package->status_pengiriman === 'Terkirim';

            if ($isLunas && $isTerkirim) {
                $deskripsi = 'Pendapatan Paket #PKT-' . str_pad($package->id, 4, '0', STR_PAD_LEFT);
                
                // Mencegah double input data transaksi
                $transactionExists = \App\Models\Transaction::where('deskripsi', $deskripsi)->exists();
                
                if (!$transactionExists) {
                    // Default nominal jika koordinat tidak ditemukan (Tarif dasar minimal + Admin)
                    $nominal = 20000 + 1500; 

                    if ($package->customer && $package->customer->lat && $package->customer->lng) {
                        try {
                            // Titik awal koordinat RSPPN Bintaro
                            $lngAwal = 106.764780;
                            $latAwal = -6.271362;
                            $lngTujuan = floatval($package->customer->lng);
                            $latTujuan = floatval($package->customer->lat);
                            
                            // Hitung jarak rute jalan menggunakan OSRM API secara real-time di backend
                            $osrmUrl = "https://router.project-osrm.org/route/v1/driving/{$lngAwal},{$latAwal};{$lngTujuan},{$latTujuan}?overview=false";
                            $response = \Illuminate\Support\Facades\Http::timeout(5)->get($osrmUrl);
                            
                            if ($response->successful() && $response->json('code') === 'Ok') {
                                $distanceMeters = $response->json('routes.0.distance');
                                $jarakKm = $distanceMeters / 1000;
                                
                                // Terapkan aturan pembulatan dan struktur tarif yang sama
                                if ($jarakKm <= 5.0) {
                                    $hargaOngkir = 20000;
                                } else {
                                    $jarakDibulatkan = ceil($jarakKm); // Pembulatan ke atas (misal 5.1 atau 6.2 menjadi 6 atau 7)
                                    $extraKm = $jarakDibulatkan - 5;
                                    $hargaOngkir = 20000 + ($extraKm * 5000);
                                }
                                
                                // Total nominal akhir: Tarif Jarak + Biaya Admin Rp 1.500
                                $nominal = $hargaOngkir + 1500;
                            }
                        } catch (\Exception $e) {
                            // Jika API OSRM mengalami limit/timeout, gunakan harga minimal default
                        }
                    }

                    // Buat pencatatan uang masuk ke buku besar transaksi
                    \App\Models\Transaction::create([
                        'deskripsi'         => $deskripsi,
                        'nominal'           => $nominal,
                        'tipe'              => 'Uang Masuk',
                        'metode_pembayaran' => $package->metode_pembayaran ?? 'Tunai / Cash'
                    ]);
                }
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
            $work = \App\Models\Work::where('package_id', $id)->first();
            if ($work && $work->vehicle_id) {
                \App\Models\Vehicle::where('id', $work->vehicle_id)->update(['status' => 'Tersedia']);
            }
            $package->delete();
            return response()->json(['status' => 'success', 'message' => 'Dihapus'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}