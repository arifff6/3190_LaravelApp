@extends('layout.app')

@section('title', 'Formulir Checkout - ' . $event->title)

@section('content')
<main class="max-w-3xl mx-auto px-6 py-12">
    <h2 class="text-3xl font-black mb-2">Formulir Pemesanan Tiket</h2>
    <p class="text-slate-500 mb-8">Silakan isi data diri Anda untuk memesan tiket event: <strong>{{ $event->title }}</strong></p>

    <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
        {{-- Menggunakan ID formulir yang sangat spesifik --}}
        <form id="form-kirim-pembayaran" action="{{ route('checkout.store', $event->id) }}" method="POST">
            @csrf
            
            <div class="space-y-6">
                <div>
                    <label class="block font-bold text-sm mb-2 text-slate-700">Nama Lengkap</label>
                    <input type="text" id="input_nama" name="customer_name" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-bold text-sm mb-2 text-slate-700">Alamat Email</label>
                    <input type="email" id="input_email" name="customer_email" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-bold text-sm mb-2 text-slate-700">Nomor Telepon</label>
                    <input type="text" id="input_telp" name="customer_phone" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="pt-6 border-t flex justify-between items-center">
                    <div>
                        <p class="text-xs text-slate-400 font-bold uppercase">Total Tagihan</p>
                        <p class="text-xl font-black text-indigo-600">Rp {{ number_format($event->price + 5000, 0, ',', '.') }}</p>
                    </div>
                    
                    {{-- Menggunakan event onclick langsung di dalam tag HTML agar tidak bisa diblokir oleh library eksternal --}}
                    <button type="button" onclick="eksekusiSubmitForm()" class="px-8 py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-100">
                        Bayar Sekarang
                    </button>
                </div>
            </div>
        </form>
    </div>
</main>

{{-- SCRIPT JAVASCRIPT DILETAKKAN LANGSUNG DI SINI TANPA BLOK STACK/PUSH --}}
<script>
    function eksekusiSubmitForm() {
        console.log("Tombol Bayar Sekarang berhasil mendeteksi ketukan!");

        var nama = document.getElementById('input_nama').value.trim();
        var email = document.getElementById('input_email').value.trim();
        var telp = document.getElementById('input_telp').value.trim();

        // Validasi internal via Javascript pop-up alert
        if (nama === "" || email === "" || telp === "") {
            alert("Harap lengkapi semua kolom data diri terlebih dahulu!");
            return false;
        }

        alert("Data lengkap! Menghubungkan ke sistem server Laravel...");
        
        // Perintah mutlak memaksa formulir mengirimkan data ke web.php
        document.getElementById('form-kirim-pembayaran').submit();
    }
</script>
@endsection
