<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromotorDashboardController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CheckInController; 
use App\Http\Controllers\AttendeeController; 
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - SPECTIX MASTER
|--------------------------------------------------------------------------
*/

// --- PINTU DARURAT (Manual Auth agar tidak 404) ---
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

// ==========================================
// 1. AREA PUBLIK
// ==========================================
Route::get('/', [TicketController::class, 'index'])->name('home');
Route::get('/event/{id}', [TicketController::class, 'show'])->name('event.show');

Route::post('/checkout/prepare', [TicketController::class, 'prepareCheckout'])->name('checkout.prepare');
Route::get('/checkout/form', [TicketController::class, 'checkoutForm'])->name('checkout.form');

Route::post('/checkout/voucher/check', [TicketController::class, 'checkVoucher'])->name('checkout.voucher.check');
Route::post('/checkout/process', [TicketController::class, 'processPayment'])->name('checkout.process');
Route::get('/invoice/{order_number}', [TicketController::class, 'invoice'])->name('checkout.invoice');

// ==========================================
// 2. AREA PRIVAT (Auth)
// ==========================================
Route::middleware(['auth'])->group(function () {

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // --- KHUSUS PROMOTOR ---
    Route::middleware(['role:promotor'])->group(function () {
        Route::get('/promotor/event/{id}/edit', [TicketController::class, 'edit'])->name('promotor.event.edit'); 
        Route::put('/promotor/event/{id}', [TicketController::class, 'update'])->name('promotor.event.update');    
        Route::get('/promotor/id-card-studio', function() { return view('promotor.idcard'); })->name('promotor.idcard');
        Route::get('/dashboard', fn() => redirect()->route('promotor.dashboard'))->name('dashboard');
        Route::get('/promotor/dashboard', [PromotorDashboardController::class, 'index'])->name('promotor.dashboard');
        Route::post('/promotor/event/toggle/{id}', [PromotorDashboardController::class, 'toggleStatus'])->name('promotor.event.toggle');
        Route::delete('/promotor/event/delete/{id}', [PromotorDashboardController::class, 'destroy'])->name('promotor.event.delete');
        
        // ATTENDEES & EXPORT EXCEL
        Route::get('/promotor/attendees', [AttendeeController::class, 'index'])->name('promotor.attendees');
        Route::get('/promotor/attendees/export', [AttendeeController::class, 'export'])->name('promotor.attendees.export');
        Route::post('/promotor/attendees/guest', [AttendeeController::class, 'storeGuest'])->name('promotor.guest');
        Route::post('/promotor/attendees/resend/{id}', [AttendeeController::class, 'resendTicket'])->name('promotor.resend');
        Route::post('/promotor/attendees/category', [AttendeeController::class, 'storeCategory'])->name('promotor.category.store');
        Route::post('/promotor/attendees/staff', [AttendeeController::class, 'storeStaff'])->name('promotor.staff.store');

        Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
        Route::post('/tickets/store', [TicketController::class, 'store'])->name('tickets.store');
    });

    // --- KHUSUS SCANNER ---
    Route::get('/promotor/scanner', [CheckInController::class, 'index'])->name('promotor.scanner'); 
    Route::post('/promotor/scanner/validate', [CheckInController::class, 'validateTicket'])->name('checkin.validate'); 

    // --- VOUCHER ---
    Route::get('/promotor/vouchers', [VoucherController::class, 'index'])->name('promotor.vouchers');
    Route::post('/promotor/vouchers', [VoucherController::class, 'store'])->name('promotor.vouchers.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Tetap panggil file auth asli di bawah sebagai cadangan (kalau filenya ada)
if (file_exists(__DIR__.'/auth.php')) {
    require __DIR__.'/auth.php';
}