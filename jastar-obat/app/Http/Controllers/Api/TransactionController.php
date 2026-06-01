<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::latest()->get();
        return response()->json(['status' => 'success', 'data' => $transactions], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tipe'              => 'required|in:Uang Masuk,Uang Keluar,Mutasi,Mutasi Masuk,Mutasi Keluar',
            'metode_pembayaran' => 'required_unless:tipe,Mutasi|string|nullable',
            'dari_rekening'     => 'required_if:tipe,Mutasi|string|nullable',
            'ke_rekening'       => 'required_if:tipe,Mutasi|string|nullable',
            'nominal'           => 'required|numeric|min:0',
            'deskripsi'         => 'required|string',
        ]);

        if ($validator->fails()) return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);

        DB::beginTransaction();
        try {
            if ($request->tipe === 'Mutasi') {
                // Catat mutasi secara bersih dengan tipe mutasi native masing-masing dompet
                Transaction::create([
                    'tipe'              => 'Mutasi Keluar',
                    'metode_pembayaran' => $request->dari_rekening,
                    'nominal'           => $request->nominal,
                    'deskripsi'         => '[MUTASI] ' . $request->deskripsi
                ]);
                
                Transaction::create([
                    'tipe'              => 'Mutasi Masuk',
                    'metode_pembayaran' => $request->ke_rekening,
                    'nominal'           => $request->nominal,
                    'deskripsi'         => '[MUTASI] ' . $request->deskripsi
                ]);
            } else {
                // Transaksi Operasional Biasa
                Transaction::create($request->all());
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Transaksi tercatat'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $transaction = Transaction::find($id);
        if (!$transaction) return response()->json(['status' => 'error', 'message' => 'Transaksi tidak ditemukan'], 404);

        try {
            $transaction->update($request->all());
            return response()->json(['status' => 'success', 'data' => $transaction], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $transaction = Transaction::find($id);
        if (!$transaction) return response()->json(['status' => 'error', 'message' => 'Not found'], 404);

        $transaction->delete();
        return response()->json(['status' => 'success'], 200);
    }
}