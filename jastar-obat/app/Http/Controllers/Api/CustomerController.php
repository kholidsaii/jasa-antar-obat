<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::latest()->get();
        return response()->json(['status' => 'success', 'message' => 'Data customer berhasil diambil', 'data' => $customers], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama'          => 'required|string|max:255',
            'no_telp'       => 'required|string|max:20',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'umur'          => 'nullable|integer',
            'kota'          => 'nullable|string',
            'kecamatan'     => 'nullable|string',
            'kelurahan'     => 'nullable|string',
            'rt'            => 'nullable|string',
            'rw'            => 'nullable|string',
            'alamat'        => 'required|string', 
            'detail_alamat' => 'nullable|string',
            'lat'           => 'nullable|string', 
            'lng'           => 'nullable|string', 
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        try {
            $customer = Customer::create($request->all());
            return response()->json(['status' => 'success', 'message' => 'Customer berhasil ditambahkan', 'data' => $customer], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal menambah data: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $customer = Customer::find($id);
        if (!$customer) return response()->json(['status' => 'error', 'message' => 'Customer tidak ditemukan'], 404);
        return response()->json(['status' => 'success', 'data' => $customer], 200);
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::find($id);
        if (!$customer) return response()->json(['status' => 'error', 'message' => 'Customer tidak ditemukan'], 404);

        $validator = Validator::make($request->all(), [
            'nama'          => 'sometimes|required|string|max:255',
            'no_telp'       => 'sometimes|required|string|max:20',
            'jenis_kelamin' => 'sometimes|required|in:Laki-laki,Perempuan',
            'umur'          => 'nullable|integer',
            'kota'          => 'nullable|string',
            'kecamatan'     => 'nullable|string',
            'kelurahan'     => 'nullable|string',
            'rt'            => 'nullable|string',
            'rw'            => 'nullable|string',
            'alamat'        => 'sometimes|required|string',
            'detail_alamat' => 'nullable|string',
            'lat'           => 'nullable|string', 
            'lng'           => 'nullable|string', 
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        try {
            $customer->update($request->all());
            return response()->json(['status' => 'success', 'message' => 'Customer berhasil diupdate', 'data' => $customer], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal mengupdate data: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $customer = Customer::find($id);
        if (!$customer) return response()->json(['status' => 'error', 'message' => 'Customer tidak ditemukan'], 404);

        try {
            $customer->delete();
            return response()->json(['status' => 'success', 'message' => 'Customer berhasil dihapus'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data: ' . $e->getMessage()], 500);
        }
    }
}