<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    // READ: Menampilkan daftar kategori
    public function index(Request $request)
    {
       // 1. Mulai query dari model
    $query = \App\Models\Category::query();

    // 2. Cek apakah ada input 'search' dari form
    if ($request->has('search') && $request->search != '') {
        $query->where('name', 'LIKE', '%' . $request->search . '%');
    }

    // 3. Ambil data dengan urutan terbaru
    $categories = $query->latest()->get();

    // 4. Kirim data ke view
    return view('admin.categories.index', compact('categories'));
    }

    // CREATE: Menampilkan form tambah
    public function create()
    {
        return view('admin.categories.create');
    }

    // STORE: Menyimpan data baru
    public function store(Request $request)
    {
       $request->validate(['name' => 'required|unique:categories|max:255']);

    // Gabungkan data request dengan slug yang digenerate otomatis
    $data = $request->all();
    $data['slug'] = Str::slug($request->name);

    Category::create($data);
    
    return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil ditambah!');
    }

    // EDIT: Menampilkan form edit
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    // UPDATE: Menyimpan perubahan
    public function update(Request $request, Category $category)
    {
        $request->validate(['name' => 'required|max:255']);
        $category->update($request->all());
        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diupdate!');
    }

    // DELETE: Menghapus data
    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus!');
    }
}