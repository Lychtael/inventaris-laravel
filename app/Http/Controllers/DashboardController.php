<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\LogAktivitas;
use App\Models\Kondisi;
use App\Models\StatusAset;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard (LOGIKA ASET BARU - FIX)
     */
    public function index()
    {
        // === 1. AMBIL ID DATA MASTER (SESUAI CSV) ===
        
        // Ambil ID untuk Kondisi "Baik", "KB", "RB"
        $kondisiBaikId = Kondisi::where('nama_kondisi', 'Baik')->value('id');
        $kondisiRusakIds = Kondisi::whereIn('nama_kondisi', ['KB', 'RB'])->pluck('id');

        // Ambil ID untuk Status "Dipinjam"
        $statusDipinjamId = StatusAset::where('nama_status', 'Dipinjam')->value('id');

        
        // === 2. DATA KARTU STATISTIK (LOGIKA BARU: MENGHITUNG BARIS/COUNT) ===
        
        // Total Aset (HANYA yang kondisinya "Baik")
        $totalBarangBaik = Barang::where('id_kondisi', $kondisiBaikId)->count();

        // Total Aset yang statusnya "Dipinjam"
        $totalDipinjam = Barang::where('id_status_aset', $statusDipinjamId)->count();
        
        // Total Aset yang kondisinya "Rusak" (KB + RB)
        $totalRusak = Barang::whereIn('id_kondisi', $kondisiRusakIds)->count();

        
        // === 3. DATA GRAFIK PIE CHART (LOGIKA BARU: MENGHITUNG BARIS/COUNT) ===
        $dataGrafik = Barang::join('kondisi', 'barang.id_kondisi', '=', 'kondisi.id')
            ->select('kondisi.nama_kondisi', DB::raw('COUNT(barang.id) as total')) // Ganti SUM() menjadi COUNT()
            ->groupBy('kondisi.nama_kondisi')
            ->get();

        $chartLabels = $dataGrafik->pluck('nama_kondisi');
        $chartData = $dataGrafik->pluck('total');

        
        // === 4. DATA LOG AKTIVITAS (INI TIDAK BERUBAH) ===
        $logAktivitas = LogAktivitas::with('pengguna') 
                                    ->latest('dibuat_pada') // Gunakan 'dibuat_pada'
                                    ->take(5) 
                                    ->get();

        
        // === 5. KIRIM SEMUA DATA KE VIEW ===
        return view('dashboard', [
            'totalBarangBaik' => $totalBarangBaik,
            'totalDipinjam' => $totalDipinjam,
            'totalRusak' => $totalRusak,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'logAktivitas' => $logAktivitas,
        ]);
    }
}