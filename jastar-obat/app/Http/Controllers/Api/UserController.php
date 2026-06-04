<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('vehicle')
            // Menghitung paket yang SEDANG DIANTAR (Aktif)
            ->withCount(['works' => function ($query) {
                $query->whereHas('package', function ($q) {
                    $q->whereNotIn('status_pengiriman', ['Terkirim', 'Dibatalkan']);
                });
            }])
            // Menghitung TOTAL RIWAYAT paket yang sudah SELESAI diantar (All-time)
            ->withCount(['works as history_count' => function ($query) {
                $query->whereHas('package', function ($q) {
                    $q->where('status_pengiriman', 'Terkirim');
                });
            }])
            ->latest()->get();

        return response()->json(['status' => 'success', 'data' => $users], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'       => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users',
            'role'       => 'required|string|in:superadmin,admin,farmasi,kurir',
            'no_telepon' => 'nullable|string|max:20',
            'foto'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $data = $request->only(['name', 'email', 'role', 'no_telepon']);
            $data['password'] = Hash::make('password123');
            $data['is_online'] = false; 

            if ($request->hasFile('foto')) {
                $path = $request->file('foto')->store('fotos', 'public');
                $data['foto'] = $path;
            }

            $user = User::create($data);
            $user->works_count = 0; 
            $user->history_count = 0;

            DB::commit();
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
            'name'       => 'sometimes|required|string|max:255',
            'email'      => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'role'       => 'sometimes|required|string|in:superadmin,admin,farmasi,kurir',
            'no_telepon' => 'nullable|string|max:20',
            'password'   => 'nullable|string|min:6',
            'foto'       => 'nullable|image|mimes:jpeg,png,jpg|max:10240'
        ]);

        if ($validator->fails()) return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);

        try {
            $data = $request->only(['name', 'email', 'role', 'no_telepon']);
            
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            if ($request->hasFile('foto')) {
                if ($user->foto) Storage::disk('public')->delete($user->foto);
                $data['foto'] = $request->file('foto')->store('fotos', 'public');
            }

            $user->update($data);
            return response()->json(['status' => 'success', 'data' => $user], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);

        DB::beginTransaction();
        try {
            if ($user->foto) Storage::disk('public')->delete($user->foto);
            Vehicle::where('user_id', $user->id)->delete();
            $user->delete();
            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'User berhasil dihapus'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Gagal menghapus user.'], 500);
        }
    }
}