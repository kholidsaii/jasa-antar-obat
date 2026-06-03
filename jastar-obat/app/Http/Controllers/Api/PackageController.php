<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Customer;
use App\Models\Transaction; 
use App\Models\Work;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\PackageHistory;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::with('customer')->latest()->get();
        return response()->json(['status' => 'success', 'data' => $packages], 200);
    }

    public function store(Request $request)
    {
        // Validasi Alur Baru
        $validator = Validator::make($request->all(), [
            'nama'              => 'required|string',
            'no_telp'           => 'required|string',
            'alamat'            => 'required|string',
            'no_struk'          => 'required|string',
            'foto_struk'        => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // Maks 5MB
            'metode_pembayaran' => 'required|string',
            'jarak_km'          => 'required|numeric',
            'total_harga'       => 'required|numeric',
                'waktu_pengantaran' => 'nullable|string' // <--- Validasi untuk waktu pengantaran
        ]);

        if ($validator->fails()) return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);

        try {
            // 1. Cek apakah customer sudah ada berdasarkan No Telp, jika belum buat baru otomatis
            $customer = Customer::firstOrCreate(
                ['no_telp' => $request->no_telp],
                [
                    'nama' => $request->nama, 
                    'alamat' => $request->alamat, 
                    'jenis_kelamin' => 'Laki-laki'
                ]
            );

            // 2. Jika ada perubahan alamat dari customer lama, update alamatnya
            if ($customer->alamat !== $request->alamat) {
                $customer->update(['alamat' => $request->alamat]);
            }

            // 3. Proses Upload Foto Struk
            $fotoPath = null;
            if ($request->hasFile('foto_struk')) {
                $fotoPath = $request->file('foto_struk')->store('struk_antrian', 'public');
            }

            // 4. Buat Paket dengan Status Awal Baru
            $package = Package::create([
                'customer_id'       => $customer->id,
                'no_struk'          => $request->no_struk,
                'deskripsi_pesanan' => 'Resep Obat (Via Struk Antrian)',
                'status_pengiriman' => '1. Verifikasi Jastar', // Sesuai flow catatan
                'status_pembayaran' => 'Lunas', // Karena lewat Payment Gateway / Kasir di awal
                'metode_pembayaran' => $request->metode_pembayaran,
                'jarak_km'          => $request->jarak_km,
                'total_harga'       => $request->total_harga,
                'foto_struk'        => $fotoPath,
                'waktu_pengantaran' => $request->waktu_pengantaran // <
            ]);
            

            // TAMBAHAN: Catat Riwayat Pertama Kali Paket Dibuat
            \App\Models\PackageHistory::create([
                'package_id'        => $package->id,
                'status_pengiriman' => '1. Verifikasi Jastar',
                'keterangan'        => 'Pesanan baru dibuat dan lunas.'
            ]);

            // 5. AUTO-SINKRONISASI BUKU KAS (Karena langsung bayar di depan)
            // Format baru: #PKT-0005-12345
            $kodeUnik = '#PKT-' . str_pad($package->id, 4, '0', STR_PAD_LEFT) . '-' . $package->no_struk;
            $biayaAdmin = 1500;
            $biayaDasar = max(0, $package->total_harga - $biayaAdmin);
            
            $metode = str_contains(strtolower($package->metode_pembayaran), 'transfer') 
                      ? 'Rek. Bank Budi (Operasional)' 
                      : $package->metode_pembayaran;

            Transaction::create([
                'deskripsi'         => 'Pendapatan Dasar ' . $kodeUnik,
                'nominal'           => $biayaDasar,
                'tipe'              => 'Uang Masuk',
                'metode_pembayaran' => $metode
            ]);

            Transaction::create([
                'deskripsi'         => 'Biaya Admin ' . $kodeUnik,
                'nominal'           => $biayaAdmin,
                'tipe'              => 'Uang Masuk',
                'metode_pembayaran' => 'Biaya Admin (Sistem)'
            ]);

            $package->load('customer');
            return response()->json(['status' => 'success', 'data' => $package], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

   public function update(Request $request, $id)
    {
        $package = Package::find($id);
        if (!$package) return response()->json(['status' => 'error', 'message' => 'Paket tidak ditemukan'], 404);

        try {
            // CATAT STATUS LAMA SEBELUM DIUPDATE
            $statusLama = $package->status_pengiriman;

            $package->update($request->all());
            $package->load('customer');

            // 1. JIKA STATUS BERUBAH, CATAT KE HISTORY (Tracking berurut)
            if ($request->has('status_pengiriman') && $statusLama !== $request->status_pengiriman) {
                \App\Models\PackageHistory::create([
                    'package_id'        => $package->id,
                    'status_pengiriman' => $request->status_pengiriman,
                    'keterangan'        => 'Status diupdate secara manual'
                ]);
            }

            // 2. AUTO-SINKRONISASI BUKU KAS (Jika harga / metode pembayaran direvisi)
            if ($request->has('total_harga') || $request->has('metode_pembayaran')) {
                // Gunakan LIKE pattern agar tetap cocok meski no_struk berubah
                $kodeUnikBase = '#PKT-' . str_pad($package->id, 4, '0', STR_PAD_LEFT);
                
                $biayaAdmin = 1500;
                $biayaDasar = max(0, $package->total_harga - $biayaAdmin);
                
                $metode = str_contains(strtolower($package->metode_pembayaran), 'transfer') 
                          ? 'Rek. Bank Budi (Operasional)' 
                          : $package->metode_pembayaran;

                // Update Transaksi Pendapatan Dasar
                Transaction::where('deskripsi', 'LIKE', "Pendapatan Dasar {$kodeUnikBase}%")->update([
                    'nominal' => $biayaDasar,
                    'metode_pembayaran' => $metode
                ]);

                // Update Transaksi Biaya Admin
                Transaction::where('deskripsi', 'LIKE', "Biaya Admin {$kodeUnikBase}%")->update([
                    'nominal' => $biayaAdmin
                ]);
            }

            // 3. JIKA DIBATALKAN (CANCEL)
            if ($package->status_pengiriman === '9. Cancel / Pending') {
                $work = Work::where('package_id', $package->id)->first();
                if ($work) {
                    if ($work->vehicle_id) Vehicle::where('id', $work->vehicle_id)->update(['status' => 'Tersedia']);
                    $work->delete();
                }
                
                // Cabut total dari buku kas jika dicancel
                $kodeUnikDelete = '#PKT-' . str_pad($package->id, 4, '0', STR_PAD_LEFT);
                Transaction::where('deskripsi', 'LIKE', "%{$kodeUnikDelete}%")->delete();
            }

            return response()->json(['status' => 'success', 'data' => $package], 200);

        } catch (\Exception $e) {
            return response()->json(['status'  => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        // PERBAIKAN: Panggil relasi histories, serta detail kurir dan kendaraannya
        $package = Package::with(['customer', 'work.user', 'work.vehicle', 'histories'])->find($id);
        
        if (!$package) return response()->json(['status' => 'error', 'message' => 'Paket tidak ditemukan'], 404);
        return response()->json(['status' => 'success', 'data' => $package], 200);
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