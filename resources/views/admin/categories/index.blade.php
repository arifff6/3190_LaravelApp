@extends('layout.admin')

@section('title', 'Kelola Kategori - Admin')
@section('page_title', 'Kelola Kategori')
@section('page_subtitle', 'Atur kategori acara Anda di sini.')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <form action="{{ route('admin.categories.index') }}" method="GET" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" 
            placeholder="Cari kategori..." 
            class="px-5 py-3 rounded-2xl border border-slate-200 outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 transition">
        
        <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-2xl font-bold hover:bg-indigo-700 transition">
            Cari
        </button>
    </form>

    <a href="{{ route('admin.categories.create') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-2xl font-bold hover:bg-indigo-700 transition">
        + Tambah Baru
    </a>
</div>

<div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 text-slate-400 uppercase text-[10px] font-black tracking-widest">
                <tr>
                    <th class="px-8 py-4">No</th>
                    <th class="px-8 py-4">Nama Kategori</th>
                    <th class="px-8 py-4 text-center">Aksi</th>
                </tr>
            </thead>
             <tbody class="divide-y border-t">
    {{-- GANTI @foreach DENGAN @forelse --}}
    @forelse($categories as $index => $category)
        <tr class="hover:bg-slate-50/50 transition">
            <td class="px-8 py-6 font-bold text-slate-400">{{ $index + 1 }}</td>
            <td class="px-8 py-6 font-black text-slate-800">{{ $category->name }}</td>
            <td class="px-8 py-6">
                <div class="flex justify-center gap-2">
                    <a href="{{ route('admin.categories.edit', $category->id) }}" 
                       class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 p-2 rounded-lg transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </a>

                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="text-red-600 hover:text-red-900 bg-red-50 p-2 rounded-lg transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
    @empty
        {{-- BAGIAN INI MUNCUL JIKA DATA KOSONG ATAU SEARCH TIDAK KETEMU --}}
        <tr>
            <td colspan="3" class="px-8 py-12 text-center text-slate-500">
                @if(request('search'))
                    Tidak ada kategori yang ditemukan dengan kata kunci "{{ request('search') }}".
                @else
                    Belum ada kategori yang ditambahkan.
                @endif
            </td>
        </tr>
    @endforelse
</tbody>
        </table>
    </div>
</div>
@endsection