@extends('layout.app')

@section('title', $event->title)

@section('content')
<main class="max-w-7xl mx-auto px-6 py-12">
    {{-- Breadcrumb / Kategori --}}
    <div class="mb-6">
        <span class="inline-block px-4 py-1.5 bg-indigo-50 text-indigo-600 rounded-full text-xs font-bold uppercase tracking-wider">
            {{ $event->category->name ?? 'Tanpa Kategori' }}
        </span>
    </div>

    {{-- Main Detail Card --}}
    <div class="flex flex-col lg:flex-row gap-12 items-start">
        {{-- Sisi Kiri: Poster Event --}}
        <div class="w-full lg:w-1/3 flex-shrink-0">
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-4 overflow-hidden aspect-3/4">
                <img src="{{ ($event->poster_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($event->poster_path))
                             ? asset('storage/' . $event->poster_path)
                             : 'https://placehold.co' }}" 
                     alt="{{ $event->title }}" 
                     class="w-full h-full object-cover rounded-3xl shadow-md">
            </div>
        </div>

        {{-- Sisi Kanan: Informasi Utama & Deskripsi --}}
        <div class="flex-1 space-y-8 w-full">
            <div>
                <h1 class="text-4xl md:text-5xl font-black text-slate-800 uppercase tracking-tight mb-4">
                    {{ $event->title }}
                </h1>
                
                {{-- Detail Waktu dan Lokasi --}}
                <div class="flex flex-wrap gap-6 text-slate-500 font-medium text-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>
                            @if($event->date instanceof \Carbon\Carbon)
                                {{ $event->date->format('d M Y, H:i') }}
                            @else
                                {{ \Carbon\Carbon::parse($event->date)->format('d M Y, H:i') }}
                            @endif
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="uppercase">AMIKOM</span>
                    </div>
                </div>
            </div>

            {{-- Komponen Deskripsi Event --}}
            <div class="border-t pt-6">
                <h3 class="text-xl font-bold text-slate-800 mb-3">Deskripsi Event</h3>
                <div class="text-slate-600 leading-relaxed font-medium">
                    {{ $event->description ?? 'Tidak ada deskripsi untuk event ini.' }}
                </div>
            </div>

            {{-- Komponen Kotak Ungu Harga & Tombol Pesan --}}
            <div class="bg-indigo-600 rounded-[2.5rem] p-8 md:p-10 text-white flex flex-col md:flex-row justify-between items-center gap-6 shadow-xl shadow-indigo-100">
                <div>
                    <p class="text-indigo-200 text-xs font-bold uppercase tracking-wider mb-1">Harga Tiket</p>
                    <h2 class="text-3xl md:text-4xl font-black">
                        {{ $event->price == 0 ? 'Gratis' : 'Rp ' . number_format($event->price, 0, ',', '.') }}<span class="text-base font-normal text-indigo-200"> / orang</span>
                    </h2>
                    <p class="text-xs text-indigo-200 mt-2 font-medium flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Sisa stok: <span class="font-bold underline">{{ $event->stock }} Tiket lagi!</span>
                    </p>
                </div>

                {{-- Tombol Pesan Sekarang yang sudah terhubung ke CheckoutController --}}
                @if($event->stock > 0)
                    <a href="{{ route('checkout.create', $event->id) }}" class="w-full md:w-auto px-8 py-4 bg-white text-indigo-600 rounded-2xl font-black text-center shadow-lg hover:bg-slate-50 transition active:scale-95 duration-150">
                        Pesan Sekarang
                    </a>
                @else
                    <button disabled class="w-full md:w-auto px-8 py-4 bg-slate-300 text-slate-500 rounded-2xl font-black text-center cursor-not-allowed">
                        Tiket Habis
                    </button>
                @endif
            </div>

            {{-- Kebijakan Tiket --}}
            <div class="pt-4">
                <h4 class="font-bold text-sm text-slate-800 mb-1">Kebijakan Tiket</h4>
                <p class="text-xs text-slate-400 font-medium">E-Ticket akan dikirimkan otomatis setelah pembayaran berhasil dilakukan.</p>
            </div>
        </div>
    </div>
</main>
@endsection
