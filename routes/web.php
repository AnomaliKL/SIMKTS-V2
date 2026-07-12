<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\KamarController;
use App\Http\Controllers\Admin\PenghuniController;
use App\Http\Controllers\Admin\TagihanController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Penghuni\DashboardController as PenghuniDashboardController;
use App\Http\Controllers\Penghuni\TagihanController as PenghuniTagihanController;
use App\Http\Controller\Penghuni\ProfileController;

    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/kamar/{id}', [HomeController::class, 'detail'])->name('kamar.detail');


    Route::middleware('guest')->group(function () {

        // Login
        Route::get('/login', [AuthController::class, 'index'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.process');

        // Register
        Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [AuthController::class, 'register'])->name('register.process');

    });


    Route::middleware('auth')->group(function () {

        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    });

    Route::prefix('admin')->middleware(['auth','role:admin'])->name('admin.')->group(function () {

        Route::get('/dashboard', [DashboardController::class,'admin'])->name('dashboard');
        Route::resource('kamar', KamarController::class)->except(['create','edit','show']);

        Route::resource('penghuni', PenghuniController::class)->except(['create','edit','show']);
        Route::post('penghuni/{id}/checkout',[PenghuniController::class,'checkout'])->name('penghuni.checkout');

        Route::get('/booking', [AdminBookingController::class, 'index'])->name('booking.index');
        Route::post('/booking/{id}/approve', [AdminBookingController::class, 'approve'])->name('booking.approve');
        Route::post('/booking/{id}/reject', [AdminBookingController::class, 'reject'])->name('booking.reject');

        Route::get('/tagihan',[TagihanController::class, 'index'])->name('tagihan.index');
        Route::post('/tagihan/generate',[TagihanController::class, 'generate'])->name('tagihan.generate');
        Route::patch('/tagihan/{id}/validate',[TagihanController::class,'validatePayment'])->name('tagihan.validate');
        Route::patch('/tagihan/{id}/reject',[TagihanController::class,'rejectPayment'])->name('tagihan.reject');
        Route::delete('/tagihan/{id}',[TagihanController::class, 'destroy'])->name('tagihan.destroy');
        
        Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan');
        Route::post('/pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');

        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
        Route::get('/laporan/cetak',[LaporanController::class,'cetak'])->name('laporan.cetak');
    });


    Route::prefix('pengunjung')->middleware(['auth', 'role:pengunjung'])->name('pengunjung.')->group(function () {

        Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
        Route::get('/booking-saya', [BookingController::class, 'index'])->name('booking.index');
    });

    Route::prefix('penghuni')->middleware(['auth','role:penghuni'])->name('penghuni.')->group(function () {

        Route::get('/dashboard',[PenghuniDashboardController::class,'index'])->name('dashboard');
        Route::post('/tagihan/upload', [PenghuniTagihanController::class, 'upload'])->name('tagihan.upload');
        Route::patch('/profile',[ProfileController::class, 'update'])->name('profile.update');
});