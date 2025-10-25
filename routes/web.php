<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\JenisBarangController;
use App\Http\Controllers\SumberBarangController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (bawaan Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Rute untuk pencarian AJAX (dari BarangController->cari())
    Route::post('/barang/cari', [BarangController::class, 'cari'])->name('barang.cari');
    // Rute untuk Export CSV (dari BarangController->exportCsv())
    Route::get('/barang/export-csv', [BarangController::class, 'exportCsv'])->name('barang.exportCsv');
    // Rute untuk menampilkan form import (dari BarangController->importCsvForm())
    Route::get('/barang/import-csv', [BarangController::class, 'importCsvForm'])->name('barang.importCsvForm');
    // Rute untuk memproses file import (dari BarangController->importCsv())
    Route::post('/barang/import-csv', [BarangController::class, 'importCsv'])->name('barang.importCsv');
    // Rute resource untuk operasi CRUD standar
    Route::resource('barang', BarangController::class);

    // Rute kustom untuk AJAX 'getUbah' 
    Route::post('/jenisbarang/getubah', [JenisBarangController::class, 'getUbah'])->name('jenisbarang.getubah');
    // Hanya gunakan rute resource yang kita perlukan
    Route::resource('jenisbarang', JenisBarangController::class)->only([
        'index', 'store', 'update', 'destroy'
    ]);

    // Rute kustom untuk AJAX 'getUbah' 
    Route::post('/sumberbarang/getubah', [SumberBarangController::class, 'getUbah'])->name('sumberbarang.getubah');
    // Hanya gunakan rute resource yang kita perlukan
    Route::resource('sumberbarang', SumberBarangController::class)->only([
        'index', 'store', 'update', 'destroy'
    ]);


});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
require __DIR__.'/auth.php';
