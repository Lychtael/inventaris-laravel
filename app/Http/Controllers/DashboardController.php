<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\LogAktivitas;
use App\Models\Kondisi;
use App\Models\StatusAset;
use App\Models\Dinas;
use App\Models\Bidang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard (LOGIKA ASET BARU - FIX)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $selectedDinasId = $request->input('id_dinas');
        $selectedBidangId = $request->input('id_bidang');

        $asetQuery = Barang::query();
        $logQuery = LogAktivitas::query();

        if ($user->id_peran == 1 && $selectedDinasId) {
            $asetQuery->where('id_dinas', $selectedDinasId);
            if ($selectedBidangId) {
                $asetQuery->where('id_bidang', $selectedBidangId);
            }
        } elseif ($user->id_peran != 1) {
            // (Logika filter user biasa jika diperlukan)
        }

        // === 1. PERBAIKAN LOGIKA PENGAMBILAN ID ===
        
        // Cari ID untuk semua kondisi "Baik"
        $kondisiBaikIds = Kondisi::where('nama_kondisi', 'Baik (B)')
                                 ->orWhere('nama_kondisi', 'Baik')
                                 ->pluck('id');

        // Cari ID HANYA untuk kondisi "Rusak"
        $kondisiRusakIds = Kondisi::where('nama_kondisi', 'Kurang Baik (KB)')
                                  ->orWhere('nama_kondisi', 'KB')
                                  ->orWhere('nama_kondisi', 'Rusak Berat (RB)')
                                  ->orWhere('nama_kondisi', 'RB')
                                  ->pluck('id');

        $statusDipinjamId = StatusAset::where('nama_status', 'Dipinjam')->value('id');

        
        // === 2. DATA KARTU STATISTIK (FIX) ===
        
        // Total Aset (HANYA yang kondisinya "Baik")
        $totalBarangBaik = (clone $asetQuery)->whereIn('id_kondisi', $kondisiBaikIds)->count();

        // Total Aset yang statusnya "Dipinjam"
        $totalDipinjam = (clone $asetQuery)->where('id_status_aset', $statusDipinjamId)->count();
        
        // Total Aset yang kondisinya "Rusak" (KB + RB)
        $totalRusak = (clone $asetQuery)->whereIn('id_kondisi', $kondisiRusakIds)->count();

        
        // === 3. DATA GRAFIK PIE CHART (Tidak berubah, ini sudah benar) ===
        $dataGrafik = (clone $asetQuery)
            ->join('kondisi', 'barang.id_kondisi', '=', 'kondisi.id')
            ->select('kondisi.nama_kondisi', DB::raw('COUNT(barang.id) as total'))
            ->groupBy('kondisi.nama_kondisi')
            ->get();

        $chartLabels = $dataGrafik->pluck('nama_kondisi');
        $chartData = $dataGrafik->pluck('total');

        
        // === 4. DATA LOG AKTIVITAS (Tidak berubah) ===
        $logAktivitas = $logQuery->with('pengguna') 
                                ->latest('dibuat_pada') 
                                ->take(5) 
                                ->get();
        
        // === 5. DATA UNTUK FILTER (Tidak berubah) ===
        $dinasList = Dinas::orderBy('nama_dinas', 'asc')->get();
        $bidangList = collect(); 
        if ($selectedDinasId) {
            $bidangList = Bidang::where('id_dinas', $selectedDinasId)->orderBy('nama_bidang', 'asc')->get();
        }

        
        // === 6. KIRIM SEMUA DATA KE VIEW ===
        return view('dashboard', [
            'totalBarangBaik' => $totalBarangBaik,
            'totalDipinjam' => $totalDipinjam,
            'totalRusak' => $totalRusak,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'logAktivitas' => $logAktivitas,
            'dinasList' => $dinasList,
            'bidangList' => $bidangList,
            'selectedDinasId' => $selectedDinasId,
            'selectedBidangId' => $selectedBidangId,
        ]);
    }
}