<?php
namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
   public function index(Request $request)
    {
        // 1. Ambil semua jenis kategori
        $categories = Category::all();
        
        // 2. Ambil semua data partner (TAMBAHKAN INI)
        $partners = \App\Models\Partner::all();

        // 3. Buat kueri dasar untuk mengambil event
        $query = Event::with('category')
                      ->where('date', '>=', now())
                      ->orderBy('date', 'asc');

        // 4. Filter query jika url memiliki parameter pencarian spesifik
        if ($request->has('category') && $request->category != '') {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // 5. Eksekusi query
        $events = $query->get();

        // 6. KIRIM KE VIEW (TAMBAHKAN 'partners' DI SINI)
        return view('welcome', compact('events', 'categories', 'partners'));
    }
}
