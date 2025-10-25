<?php
namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Logika dari DashboardController.php
        // Diterjemahkan ke Eloquent

        $total_barang = Barang::count(); // Asumsi total unit/jenis
        $barang_habis = Barang::where('jumlah', 0)->count();

        // groupingBy menggantikan GROUP BY SQL
        $barang_by_jenis = Barang::with('jenis') // Eager load relasi
                                ->join('jenis_barang', 'barang.id_jenis', '=', 'jenis_barang.id')
                                ->select('jenis_barang.nama_jenis', DB::raw('count(barang.id) as jumlah'))
                                ->groupBy('jenis_barang.nama_jenis')
                                ->get();

        $barang_by_sumber = Barang::with('sumber')
                                ->join('sumber_barang', 'barang.id_sumber', '=', 'sumber_barang.id')
                                ->select('sumber_barang.nama_sumber', DB::raw('count(barang.id) as jumlah'))
                                ->groupBy('sumber_barang.nama_sumber')
                                ->get();

        return view('dashboard', [
            'total_barang' => $total_barang,
            'barang_habis' => $barang_habis,
            'barang_by_jenis' => $barang_by_jenis,
            'barang_by_sumber' => $barang_by_sumber,
        ]);
    }
}