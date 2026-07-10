<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\Admin\KamarController;
use App\Http\Controllers\Admin\PenghuniController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\TagihanController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\PengaturanController;

Route::get('/', function () {
    return redirect()->route('login');
});


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
        Route::resource('booking', BookingController::class)->except(['create','edit','show']);
        Route::resource('tagihan', TagihanController::class)->except(['create','edit','show']);
        Route::get('/laporan', [LaporanController::class,'index'])->name('laporan');
        Route::get('/pengaturan', [PengaturanController::class,'index'])->name('pengaturan');
    });


Route::prefix('pengunjung')->middleware(['auth','role:pengunjung'])->name('pengunjung.')->group(function () {

        Route::get('/dashboard', [DashboardController::class,'pengunjung'])->name('dashboard');
        Route::get('/booking', [BookingController::class,'pengunjung'])->name('booking');
        Route::get('/tagihan', [TagihanController::class,'pengunjung'])->name('tagihan');
        Route::get('/profil', [AuthController::class,'profilPengunjung'])->name('profil');
});


Route::prefix('penghuni')->middleware(['auth','role:penghuni'])->name('penghuni.')->group(function () {

        Route::get('/dashboard', [DashboardController::class,'penghuni'])->name('dashboard');
        Route::get('/tagihan', [TagihanController::class,'penghuni'])->name('tagihan');
        Route::get('/profil', [AuthController::class,'profilPenghuni'])->name('profil');

});