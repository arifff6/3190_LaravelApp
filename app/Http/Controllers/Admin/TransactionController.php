<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request; // <-- Pastikan ini ada untuk proses update nanti

class TransactionController extends Controller
{
    public function index()
    {
        // Mengambil transaksi terbaru dengan pembatasan 20 baris/halaman
        $transactions = Transaction::with('event')->latest()->paginate(20);
        return view('admin.transactions.index', compact('transactions'));
    }

    // --- 1. FUNGSI EDIT (UNTUK MENAMPILKAN HALAMAN EDIT) ---
    public function edit($id)
    {
        // Ambil data transaksi berdasarkan ID
        $transaction = Transaction::findOrFail($id);
        
        // Ambil data event juga jika di form edit butuh pilihan event (opsional)
        // $events = \App\Models\Event::all(); 

        return view('admin.transactions.edit', compact('transaction'));
    }

    // --- 2. FUNGSI UPDATE (UNTUK MENYIMPAN PERUBAHAN DATA) ---
    public function update(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        // Validasi data (Sesuaikan field-nya dengan database/modul kamu)
        $request->validate([
            'status' => 'required', // Contoh jika ingin ubah status transaksi seperti 'pending'/'success'
        ]);

        // Update data transaksi
        $transaction->update([
            'status' => $request->status,
        ]);

        return redirect()->route('admin.transactions.index')->with('success', 'Data transaksi berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();

        return redirect()->route('admin.transactions.index')->with('success', 'Data transaksi berhasil dihapus!');
    }
}