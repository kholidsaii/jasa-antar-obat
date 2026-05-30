<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::latest()->get();
        return response()->json([
            'status' => 'success',
            'data'   => $transactions
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tipe'              => 'required|in:Uang Masuk,Uang Keluar',
            'metode_pembayaran' => 'required|string',
            'nominal'           => 'required|numeric|min:0',
            'deskripsi'         => 'required|string',
            'work_id'           => 'nullable|exists:works,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        try {
            $transaction = Transaction::create($request->all());
            return response()->json(['status' => 'success', 'data' => $transaction], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // METHOD BARU: Untuk Update (Edit) Transaksi
    public function update(Request $request, $id)
    {
        $transaction = Transaction::find($id);
        if (!$transaction) return response()->json(['status' => 'error', 'message' => 'Transaksi tidak ditemukan'], 404);

        $validator = Validator::make($request->all(), [
            'tipe'              => 'sometimes|required|in:Uang Masuk,Uang Keluar',
            'metode_pembayaran' => 'sometimes|required|string',
            'nominal'           => 'sometimes|required|numeric|min:0',
            'deskripsi'         => 'sometimes|required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        try {
            $transaction->update($request->all());
            return response()->json(['status' => 'success', 'message' => 'Diupdate', 'data' => $transaction], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $transaction = Transaction::find($id);
        if (!$transaction) return response()->json(['status' => 'error', 'message' => 'Not found'], 404);

        $transaction->delete();
        return response()->json(['status' => 'success', 'message' => 'Deleted successfully'], 200);
    }
}