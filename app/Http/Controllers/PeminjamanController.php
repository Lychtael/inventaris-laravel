<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;

class PeminjamanController extends Controller
{
    /**
     * Menampilkan daftar semua peminjaman.
     */
    public function index()
    {
        $peminjaman = Peminjaman::with('barang')
                                ->orderBy('tanggal_pinjam', 'desc')
                                ->orderBy('status', 'asc')
                                ->get();

        return view('peminjaman.index', [
            'peminjaman' => $peminjaman,
            'judul' => 'Data Peminjaman'
        ]);
    }

    /**
     * Menampilkan form untuk mencatat peminjaman baru.
     */
    public function create()
    {
        $barang = Barang::where('jumlah', '>', 0)
                        ->orderBy('nama_barang', 'asc')
                        ->get();
        
        return view('peminjaman.create', [
            'barang' => $barang,
            'judul' => 'Catat Peminjaman'
        ]);
    }

    /**
     * Menyimpan data peminjaman baru dan mengurangi stok.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_barang' => 'required|exists:barang,id',
            'peminjam' => 'required|string|max:100',
            'jumlah_dipinjam' => 'required|integer|min:1',
            'tanggal_pinjam' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        $barang = Barang::find($validated['id_barang']);
        if ($barang->jumlah < $validated['jumlah_dipinjam']) {
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Stok barang tidak mencukupi. Stok tersisa: ' . $barang->jumlah);
        }

        try {
            DB::beginTransaction();

            Peminjaman::create([
                'id_barang' => $validated['id_barang'],
                'peminjam' => $validated['peminjam'],
                'jumlah_dipinjam' => $validated['jumlah_dipinjam'],
                'tanggal_pinjam' => $validated['tanggal_pinjam'],
                'keterangan' => $validated['keterangan'],
                'status' => 'dipinjam'
            ]);

            $barang->decrement('jumlah', $validated['jumlah_dipinjam']);
            
            // -- LOGGING (Meniru format lama) --
            $keterangan_log = "Mencatat peminjaman '" . $barang->nama_barang . "' oleh " . $validated['peminjam'];
            LogAktivitas::create([
                'id_pengguna' => Auth::id(),
                'aksi' => 'PINJAM',
                'tabel' => 'peminjaman',
                'keterangan' => $keterangan_log
            ]);
            
            DB::commit();

            return redirect()->route('peminjaman.index')
                             ->with('success', 'Data Peminjaman berhasil dicatat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Gagal mencatat peminjaman: ' . $e->getMessage());
        }
    }

    /**
     * Menandai barang sebagai 'dikembalikan' dan menambah stok.
     */
    public function kembali(Peminjaman $peminjaman)
    {
        if ($peminjaman->status == 'dikembalikan') {
             return redirect()->route('peminjaman.index')
                             ->with('error', 'Barang ini sudah dikembalikan.');
        }

        try {
            DB::beginTransaction();

            $peminjaman->update([
                'status' => 'dikembalikan',
                'tanggal_kembali' => Carbon::today()
            ]);

            $peminjaman->barang->increment('jumlah', $peminjaman->jumlah_dipinjam);
            
            // -- LOGGING (Meniru format lama) --
            $keterangan_log = "Mencatat pengembalian '" . $peminjaman->barang->nama_barang . "' oleh " . $peminjaman->peminjam;
            LogAktivitas::create([
                'id_pengguna' => Auth::id(),
                'aksi' => 'KEMBALI',
                'tabel' => 'peminjaman',
                'keterangan' => $keterangan_log
            ]);

            DB::commit();

            return redirect()->route('peminjaman.index')
                             ->with('success', 'Data Pengembalian berhasil dicatat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('peminjaman.index')
                             ->with('error', 'Gagal memproses pengembalian: ' . $e->getMessage());
        }
    }
}