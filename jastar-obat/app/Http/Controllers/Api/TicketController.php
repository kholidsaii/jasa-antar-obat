<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketChat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    // 1. Tampilkan semua tiket beserta isi chat-nya
    public function index()
    {
        $tickets = Ticket::with('chats')->latest()->get();
        return response()->json(['status' => 'success', 'data' => $tickets], 200);
    }

    // 2. Buat tiket baru (Otomatis mengirim chat pertama sebagai deskripsi)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject'     => 'required|string',
            'category'    => 'required|string',
            'reporter'    => 'required|string',
            'description' => 'required|string', // Isi pesan awal
        ]);

        if ($validator->fails()) return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);

        DB::beginTransaction();
        try {
            // Buat Tiket Induk
            $ticket = Ticket::create([
                'subject'  => $request->subject,
                'category' => $request->category,
                'resi'     => $request->resi,
                'reporter' => $request->reporter,
                'status'   => 'Open'
            ]);

            // Masukkan pesan pertama dari pelapor
            $ticket->chats()->create([
                'role'        => 'user',
                'sender_name' => $request->reporter,
                'message'     => $request->description
            ]);

            DB::commit();
            return response()->json(['status' => 'success', 'data' => $ticket->load('chats')], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // 3. Balas Chat di tiket yang sudah ada
    public function reply(Request $request, $id)
    {
        $ticket = Ticket::find($id);
        if (!$ticket) return response()->json(['status' => 'error', 'message' => 'Tiket tidak ditemukan'], 404);

        if ($ticket->status === 'Resolved') {
            return response()->json(['status' => 'error', 'message' => 'Tiket sudah ditutup, tidak bisa dibalas'], 403);
        }

        try {
            $ticket->chats()->create([
                'role'        => $request->role, // 'admin' atau 'user'
                'sender_name' => $request->sender_name,
                'message'     => $request->message
            ]);

            // Jika Admin membalas tiket baru (Open), otomatis ubah statusnya jadi Progress
            if ($ticket->status === 'Open' && $request->role === 'admin') {
                $ticket->update(['status' => 'Progress']);
            }

            return response()->json(['status' => 'success', 'data' => $ticket->load('chats')], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // 4. Ubah status tiket (misal jadi 'Resolved')
    public function updateStatus(Request $request, $id)
    {
        $ticket = Ticket::find($id);
        if (!$ticket) return response()->json(['status' => 'error', 'message' => 'Tiket tidak ditemukan'], 404);

        $ticket->update(['status' => $request->status]);
        return response()->json(['status' => 'success', 'message' => 'Status berhasil diubah'], 200);
    }
}