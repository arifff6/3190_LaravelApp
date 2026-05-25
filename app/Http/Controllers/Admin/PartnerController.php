<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PartnerController extends Controller
{
    public function index(Request $request)
    {
       // 1. Mulai query dari model
    $query = \App\Models\Partner::query();

    // 2. Cek apakah ada input 'search' dari form
    if ($request->has('search') && $request->search != '') {
        $query->where('name', 'LIKE', '%' . $request->search . '%');
    }

    // 3. Ambil data dengan urutan terbaru
    $partners = $query->latest()->get();

    // 4. Kirim data ke view
    return view('admin.partners.index', compact('partners'));
    }

    public function create()
    {
        return view('admin.partners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'logo' => 'required|image|mimes:png,jpg,jpeg|max:2048'
        ]);

        $data = $request->all();
        // Proses Upload File
        $data['logo_url'] = $request->file('logo')->store('partners', 'public');

        Partner::create($data);
        return redirect()->route('admin.partners.index')->with('success', 'Partner berhasil ditambah!');
    }

    public function edit(Partner $partner)
    {
        return view('admin.partners.edit', compact('partner'));
    }

    public function update(Request $request, Partner $partner)
    {
        $request->validate([
            'name' => 'required',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('logo')) {
            // Hapus logo lama
            Storage::disk('public')->delete($partner->logo_url);
            // Simpan logo baru
            $data['logo_url'] = $request->file('logo')->store('partners', 'public');
        }

        $partner->update($data);
        return redirect()->route('admin.partners.index')->with('success', 'Partner berhasil diupdate!');
    }

    public function destroy(Partner $partner)
    {
        Storage::disk('public')->delete($partner->logo_url);
        $partner->delete();
        return redirect()->route('admin.partners.index')->with('success', 'Partner berhasil dihapus!');
    }
}