<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; 
use App\Models\Partner;
use App\Models\Category; 

use App\Models\Event; // Jika ingin menampilkan event juga

class WelcomeController extends Controller
{
    public function index(Request $request)
    {
        $partners = Partner::all(); // Ambil semua data partner
        // $events = Event::latest()->take(6)->get(); // Opsional
        
        return view('welcome', compact('partners'));
    }
}