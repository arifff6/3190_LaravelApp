<?php
namespace App\Http\Controllers\Admin; // Harus ada \Admin-nya!

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Transaction;


class DashboardController extends Controller
{
    public function index()
    {
        $totalRevenue = Transaction::whereIn('status', ['settlement', 'success'])->sum('total_price');
        
        // 2. Menghitung Berapa orang tamu yang tiketnya sudah Lunas
        $ticketsSold = Transaction::whereIn('status', ['settlement', 'success'])->count();
        
        // 3. Menghitung Jumlah Acara Mendatang yang aktif diselenggarakan
        $activeEvents = Event::where('date', '>=', now())->count();
        
        // 4. Menghitung Transaksi Ngadat (Status belum dibayar pelanggan / Expired)
        $pendingOrders = Transaction::where('status', 'pending')->count();
        
        // 5. Menyertakan 5 daftar riwayat pesanan (History) paling mutakhir di panel
        $recentTransactions = Transaction::with('event')->latest()->take(5)->get();

        return view('admin.dashboard', compact('totalRevenue', 'ticketsSold', 'activeEvents', 'pendingOrders', 'recentTransactions'));
    }


    public function transactions()
    {
        // Memanggil file: resources/views/admin/transactions.blade.php
        return view('admin.transactions');
    }
}