<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::latest()->get();
        return response()->json([
            'status' => 'success',
            'data'   => $vehicles
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kendaraan' => 'required|string|max:255',
            'plat_nomor'     => 'required|string|max:50|unique:vehicles',
            'status'         => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        try {
            $vehicle = Vehicle::create([
                'nama_kendaraan' => $request->nama_kendaraan,
                'plat_nomor'     => $request->plat_nomor,
                'status'         => $request->status ?? 'Tersedia'
            ]);

            return response()->json(['status' => 'success', 'data' => $vehicle], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $vehicle = Vehicle::find($id);
        if (!$vehicle) return response()->json(['status' => 'error', 'message' => 'Not found'], 404);

        $vehicle->delete();
        return response()->json(['status' => 'success', 'message' => 'Deleted successfully'], 200);
    }
}