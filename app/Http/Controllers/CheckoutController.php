<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Menampilkan Form Halaman Pemesanan (Guest Checkout)
     */
    public function create(Event $event)
    {
        // Mengambil daftar kategori untuk keperluan menu footer/navbar layout
        $categories = Category::all();

        return view('checkout.create', compact('event', 'categories'));
    }

    /**
     * Memproses Validasi dan Menyimpan Data Transaksi Baru ke Database
     */
    public function store(Request $request, Event $event)
    {
        // 1. Validasi Input Kredensial Pelanggan
        $request->validate([
            'customer_name'  => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
        ]);

        // 2. Cegah Check-out Jika Tiket Sudah Habis
        if ($event->stock <= 0) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Mohon maaf, tiket untuk acara ini sudah habis.'], 422);
            }
            return back()->with('error', 'Mohon maaf, tiket untuk acara ini sudah habis.');
        }

        // 3. Generate Kode TRX / Order ID Unik (Sesuai Teori 10.3.2)
        $orderId = 'TRX-' . time() . '-' . strtoupper(Str::random(5));
        $totalPrice = $event->price + 5000; // Harga tiket + Biaya Layanan Rp 5.000

        // 4. Merekam Log Transaksi Baru ke Database dengan Status Awal 'Pending'
        $transaction = Transaction::create([
            'event_id'       => $event->id,
            'order_id'       => $orderId,
            'customer_name'  => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'total_price'    => $totalPrice,
            'status'         => 'Pending', 
        ]);

        // 5. Kurangi stok tiket event sebanyak 1 secara otomatis
        $event->decrement('stock');

        // --- INTEGRASI SNAP MIDTRANS ---
        
        // Konfigurasi Kredensial Environment Midtrans
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = false; // Mode Sandbox!
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        // Susun Paket Array Data Transaksi
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $totalPrice,
            ],
            'customer_details' => [
                'first_name' => $request->customer_name,
                'email' => $request->customer_email,
                'phone' => $request->customer_phone,
            ],
        ];

        try {
            // Perintah Tembak Generate Snap Token
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            
            // Update rekaman kita bahwa transaksi terkait sudah memiliki id token pelunasan
            $transaction->update(['snap_token' => $snapToken]);
            
            // Jika dikirim lewat AJAX (Fetch), kembalikan data JSON berupa snap_token dan info transaksi
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'order_id' => $orderId,
                    'customer_name' => $request->customer_name,
                    'snap_token' => $snapToken
                ]);
            }
            
            // Redirect ke halaman antarmuka pembayaran final pelanggan jika non-AJAX
            return redirect()->route('checkout.payment', $transaction->order_id);
            
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Gagal memproses pembayaran jaringan: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Gagal memproses pembayaran jaringan: ' . $e->getMessage());
        }
    }

    public function payment($order_id)
    {
         // Mengambil daftar kategori untuk keperluan menu footer
         $categories = \App\Models\Category::all();

         $transaction = Transaction::with('event')->where('order_id', $order_id)->firstOrFail();
         return view('checkout.payment', compact('transaction','categories'));
    }

       public function success($order_id)
    {
        // Mengambil daftar kategori untuk keperluan menu footer
        $categories = \App\Models\Category::all();

        $transaction = Transaction::with('event')->where('order_id', $order_id)->firstOrFail();
        
        // Konfigurasi Midtrans untuk mengecek status transaksi langsung ke API
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        try {
            // Mengecek status pesanan secara mandiri (Bypass)
            $status = \Midtrans\Transaction::status($order_id);
            
            if ($status) {
                // Mengambil nilai status transaksi
                $trx_status = is_array($status) ? ($status['transaction_status'] ?? '') : ($status->transaction_status ?? '');
                
                // Jika API Midtrans mengonfirmasi bahwa transaksi telah berhasil (settlement / capture)
                if (in_array($trx_status, ['settlement', 'capture'])) {
                    // Hanya lakukan update jika status di database lokal masih 'pending' (indikasi Webhook tidak masuk)
                    if (strtolower($transaction->status) === 'pending') {
                        $transaction->update(['status' => 'success']);
                        
                        if ($transaction->event && $transaction->event->stock > 0) {
                            $transaction->event->stock = $transaction->event->stock - 1;
                            $transaction->event->save();
                            
                            try {
                                \Illuminate\Support\Facades\Mail::to($transaction->customer_email)
                                    ->send(new \App\Mail\EventTicketMail($transaction));
                            } catch (\Exception $e) {
                                \Log::error('Gagal mengirim email E-Ticket secara manual (Bypass): ' . $e->getMessage());
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Jika terjadi error dari API Midtrans (transaksi tidak valid), kembalikan ke beranda
            return redirect()->route('home')->with('error', 'Transaksi tidak ditemukan atau gagal diproses oleh sistem pembayaran.');
        }

        return view('checkout.success', compact('transaction', 'categories'));
    }
}
