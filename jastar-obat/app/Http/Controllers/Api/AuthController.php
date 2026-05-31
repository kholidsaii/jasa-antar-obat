<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = User::where('email', $request->email)->firstOrFail();
            
            // Update status menjadi ONLINE
            $user->update(['is_online' => true]);
            
            // Hapus token lama jika ingin membatasi 1 device, atau biarkan menumpuk
            $user->tokens()->delete(); 

            // Buat token baru
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Login berhasil',
                'data' => [
                    'user' => $user,
                    'token' => $token
                ]
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Email atau Password salah'
        ], 401);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        // Update status menjadi OFFLINE sebelum token dihapus
        if ($user) {
            $user->update(['is_online' => false]);
        }

        // Hapus token saat ini
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil'
        ], 200);
    }
}