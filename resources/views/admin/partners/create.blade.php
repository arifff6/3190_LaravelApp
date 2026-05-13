@extends('layout.admin')

@section('title', 'Tambah Partner - Admin')

@section('page_title', 'Tambah Partner Baru')
@section('page_subtitle', 'Isi data partner untuk ditambahkan ke daftar mitra.' )

@section('content')
<div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-10">
    <form action="{{ route('admin.partners.store') }}" method="POST" class="space-y-8">
        @csrf

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-3 uppercase tracking-wide">Nama Partner</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                class="w-full rounded-3xl border border-slate-200 px-6 py-4 text-slate-800 shadow-sm focus:border-indigo-600 focus:outline-none" />
            @error('name')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-3 uppercase tracking-wide">Logo URL</label>
            <input list="logo-options" type="url" name="logo_url" value="{{ old('logo_url', 'https://placehold.co/200x200') }}" required
                class="w-full rounded-3xl border border-slate-200 px-6 py-4 text-slate-800 shadow-sm focus:border-indigo-600 focus:outline-none" />
            <datalist id="logo-options">
                <option value="https://placehold.co/200x200?text=Partner+1"></option>
                <option value="https://placehold.co/200x200?text=Partner+2"></option>
                <option value="https://placehold.co/200x200?text=Partner+3"></option>
            </datalist>
            @error('logo_url')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end gap-4">
            <a href="{{ route('admin.partners.index') }}" class="inline-flex items-center px-6 py-3 rounded-2xl border border-slate-200 text-slate-700 font-bold hover:bg-slate-50 transition">
                Batal
            </a>
            <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-2xl font-bold hover:bg-indigo-700 transition">
                Simpan Partner
            </button>
        </div>
    </form>
</div>
@endsection