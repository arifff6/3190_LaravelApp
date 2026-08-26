<?php

use Illuminate\Support\Facades\Route;

// Import Controller User
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CheckoutController;

// Import Controller Admin
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController as EventAdminController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PartnerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('welcome');

// RUTE USER AREA (Publik - Dapat diakses Pembeli)
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
Route::get('/my-ticket', [EventController::class, 'ticket'])->name('ticket');

// Rute Checkout & Pembayaran Pembeli (Publik)
Route::get('/event/checkout/{event}', [CheckoutController::class, 'create'])->name('checkout.create');
Route::post('/event/checkout/{event}', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/payment/{order_id}', [CheckoutController::class, 'payment'])->name('checkout.payment');
Route::get('/success/{order_id}', [CheckoutController::class, 'success'])->name('checkout.success');

// REVISI: Mengubah post menjadi match agar tidak memicu error 405 Method Not Allowed
Route::match(['get', 'post'], '/midtrans/callback', [\App\Http\Controllers\MidtransWebhookController::class, 'handle']);


// RUTE ADMIN AREA
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/', [DashboardController::class, 'index']);
        
        // Resource Controller untuk Manajemen Admin
        Route::resource('events', EventAdminController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('partners', PartnerController::class);
        
        Route::resource('transactions', TransactionController::class);
    });
    
});
