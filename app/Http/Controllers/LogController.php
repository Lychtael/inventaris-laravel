<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class LogController extends Controller
{
    /**
     * Menampilkan halaman riwayat aktivitas.
     * Diterjemahkan dari LogController.php -> index()
     * dan Log_model.php -> getAllLog()
     */
    public function index()
    {
        // Ambil semua log, urutkan dari yang terbaru
        // 'with('pengguna')' akan otomatis mengambil data user terkait
        // berkat relasi yang kita buat di model.
        $log = LogAktivitas::with('pengguna')
                            ->orderBy('dibuat_pada', 'desc')
                            ->get();

        return view('log.index', [
            'log' => $log,
            'judul' => 'Riwayat Aktivitas'
        ]);
    }
}