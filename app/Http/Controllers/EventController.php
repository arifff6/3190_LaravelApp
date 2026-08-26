<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Category;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Menampilkan detail event.
     * DISESUAIKAN dengan memperbaiki error RelationNotFoundException.
     */
    public function show(Event $event)
    {
        // Me-load relasi category saja (karena user tidak ada di model Event)
        $event->load(['category']);

        // Mengambil daftar kategori untuk keperluan menu navigasi/footer
        $categories = Category::all();

        // Mengarahkan ke file view yang sesuai
        return view('layout.event-detail', compact('categories', 'event'));
    }

    /**
     * Mengarahkan Pengguna ke Halaman Pemesanan (Checkout)
     */
    public function checkout($id)
    {
        // Logika checkout Anda di sini
    }
}