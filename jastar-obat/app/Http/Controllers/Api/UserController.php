<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vehicle; // Fix 1: Import model Vehicle
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; // Fix 2: Import Facade DB

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('vehicle')->withCount(['works' => function ($query) {
            $query->whereHas('package', function ($q) {
                $q->where('status_pengiriman', '!=', 'Terkirim');
            });
        }])->latest()->get();

        return response()->json(['status' => 'success', 'data' => $users], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'           => 'required|string|max:255',
            'email'          => 'required|string|email|max:255|unique:users',
            'password'       => 'required|string|min:6',
            'nama_kendaraan' => 'required|string|max:255',
            'plat_nomor'     => 'required|string|max:50|unique:vehicles',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            // 1. Buat Kurir
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // 2. Buat Kendaraan yang menempel pada Kurir (1 Karyawan = 1 Motor)
            Vehicle::create([
                'user_id'        => $user->id,
                'nama_kendaraan' => $request->nama_kendaraan,
                'plat_nomor'     => $request->plat_nomor,
                'status'         => 'Tersedia'
            ]);

            DB::commit();
            $user->works_count = 0; 
            
            // Opsional: Load relasi vehicle agar data balikan (response) lengkap
            $user->load('vehicle');

            return response()->json(['status' => 'success', 'data' => $user], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);

        $validator = Validator::make($request->all(), [
            'name'  => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        try {
            $data = $request->only(['name', 'email']);
            
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            $user->loadCount(['works' => function ($query) {
                $query->whereHas('package', function ($q) {
                    $q->where('status_pengiriman', '!=', 'Terkirim');
                });
            }]);

            return response()->json(['status' => 'success', 'data' => $user], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);
        }

        DB::beginTransaction();
        try {
            // Fix 3: Hapus kendaraan yang terkait terlebih dahulu sebelum menghapus kurir
            // Ini untuk mencegah data kendaraan 'yatim' (orphaned) jika database tidak menggunakan onDelete('cascade')
            Vehicle::where('user_id', $user->id)->delete();
            
            $user->delete();
            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'User dan kendaraan berhasil dihapus'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Gagal menghapus user. Pastikan tidak ada transaksi aktif yang mengikat.'], 500);
        }
    }
}