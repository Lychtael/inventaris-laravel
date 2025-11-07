<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\JenisBarangController;
use App\Http\Controllers\SumberBarangController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\StatusAsetController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {

    // == RUTE UNTUK SEMUA USER (YANG SUDAH LOGIN) ==
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (bawaan Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Rute Barang
    Route::post('/barang/cari', [BarangController::class, 'cari'])->name('barang.cari');
    Route::get('/barang/export-csv', [BarangController::class, 'exportCsv'])->name('barang.exportCsv');
    Route::get('/barang/import-csv', [BarangController::class, 'importCsvForm'])->name('barang.importCsvForm');
    Route::post('/barang/import-csv', [BarangController::class, 'importCsv'])->name('barang.importCsv');
    Route::resource('barang', BarangController::class);

    // Rute Peminjaman
    Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
    Route::get('/peminjaman/create', [PeminjamanController::class, 'create'])->name('peminjaman.create');
    Route::post('/peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
    Route::post('/peminjaman/{peminjaman}/kembali', [PeminjamanController::class, 'kembali'])->name('peminjaman.kembali');


    // == RUTE KHUSUS ADMIN (id_peran = 1) ==
    Route::middleware(['cek.peran:1'])->group(function () {

        // Rute Jenis Barang
        Route::resource('jenisbarang', JenisBarangController::class);

        // Rute Sumber Barang
        Route::resource('sumberbarang', SumberBarangController::class);

        // Rute Lokasi
        Route::resource('lokasi', LokasiController::class);

        // ++ TAMBAHKAN RUTE BARU INI ++
        Route::resource('status-aset', StatusAsetController::class);

        // Rute Log Aktivitas
        Route::get('/log', [LogController::class, 'index'])->name('log.index');

        // ++ TAMBAHKAN RUTE BARU INI ++
        Route::resource('lokasi', LokasiController::class);
        // (Kita juga akan tambahkan 'status-aset' di sini nanti)

    });
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
require __DIR__.'/auth.php';
