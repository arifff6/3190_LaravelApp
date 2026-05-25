@extends('layout.admin')

@section('title', 'Kelola Partner - Admin')
@section('page_title', 'Kelola Partner')
@section('page_subtitle', 'Atur daftar mitra dan sponsor acara Anda.')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <form action="{{ route('admin.partners.index') }}" method="GET" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" 
            placeholder="Cari partner..." 
            class="px-5 py-3 rounded-2xl border border-slate-200 outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 transition">
        
        <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-2xl font-bold hover:bg-indigo-700 transition">
            Cari
        </button>
    </form>

    <a href="{{ route('admin.partners.create') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-2xl font-bold hover:bg-indigo-700 transition">
        + Tambah Baru
    </a>
</div>

<div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 text-slate-400 uppercase text-[10px] font-black tracking-widest">
                <tr>
                    <th class="px-8 py-4">No</th>
                    <th class="px-8 py-4">Logo</th>
                    <th class="px-8 py-4">Nama Partner</th>
                    <th class="px-8 py-4 text-center">Aksi</th>
                </tr>
            </thead>
           <tbody class="divide-y border-t">
    @forelse($partners as $index => $partner)
    <tr class="hover:bg-slate-50/50 transition">
        <td class="px-8 py-6 font-bold text-slate-400">{{ $index + 1 }}</td>
        <td class="px-8 py-6">
            <div class="w-16 h-16 rounded-xl overflow-hidden shadow-sm border border-slate-100 bg-slate-50">
                <img src="{{ Storage::url($partner->logo_url) }}" class="w-full h-full object-contain">
            </div>
        </td>
        <td class="px-8 py-6 font-black text-slate-800">{{ $partner->name }}</td>
        <td class="px-8 py-6">
            <div class="flex justify-center gap-2">
                <a href="{{ route('admin.partners.edit', $partner->id) }}" class="p-2.5 bg-indigo-50 text-indigo-600 rounded-xl hover:bg-indigo-600 hover:text-white transition shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 00-2 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </a>
                <form action="{{ route('admin.partners.destroy', $partner->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="p-2.5 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-600 hover:text-white transition shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </form>
            </div>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="4" class="px-8 py-12 text-center text-slate-500">
            @if(request('search'))
                Tidak ada partner yang ditemukan dengan kata kunci "{{ request('search') }}".
            @else
                Belum ada partner yang ditambahkan.
            @endif
        </td>
    </tr>
    @endforelse
</tbody>
        </table>
    </div>
</div>
@endsection