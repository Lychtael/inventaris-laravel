<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\LogAktivitas;
use App\Models\Kondisi;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard dengan data statistik.
     */
    public function index()
    {
        // === 1. DATA KARTU STATISTIK ===
        
        // Ambil ID kondisi dari database (agar tidak hardcode)
        $kondisiBaikId = Kondisi::where('nama_kondisi', 'Baik')->value('id');
        $kondisiRusakIds = Kondisi::where('nama_kondisi', '!=', 'Baik')->pluck('id');

        // Hitung total barang yang kondisinya "Baik"
        $totalBarangBaik = Barang::where('id_kondisi', $kondisiBaikId)->sum('jumlah');

        // Hitung total barang yang sedang "dipinjam"
        $totalDipinjam = Peminjaman::where('status', 'dipinjam')->sum('jumlah_dipinjam');
        
        // Hitung total barang yang kondisinya "Rusak" (Ringan + Berat)
        $totalRusak = Barang::whereIn('id_kondisi', $kondisiRusakIds)->sum('jumlah');

        
        // === 2. DATA GRAFIK PIE CHART ===
        $dataGrafik = Barang::join('kondisi', 'barang.id_kondisi', '=', 'kondisi.id')
            ->select('kondisi.nama_kondisi', DB::raw('SUM(barang.jumlah) as total'))
            ->groupBy('kondisi.nama_kondisi')
            ->get();

        $chartLabels = $dataGrafik->pluck('nama_kondisi');
        $chartData = $dataGrafik->pluck('total');

        
        // === 3. DATA LOG AKTIVITAS TERBARU ===
        $logAktivitas = LogAktivitas::with('pengguna')
        ->latest('dibuat_pada') // <-- Jadi 'latest('dibuat_pada')'
        ->take(5)
        ->get();

        
        // === 4. KIRIM SEMUA DATA KE VIEW ===
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