<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Barang;
use App\Models\StatusAset; // <-- PENTING
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // <-- PENTING
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PeminjamanController extends Controller
{
    // ID Status Aset (agar tidak hardcode)
    private $statusTersediaId;
    private $statusDipinjamId;

    public function __construct()
    {
        // Ambil ID status dari DB saat controller dimuat
        $this->statusTersediaId = StatusAset::where('nama_status', 'Tersedia')->value('id');
        $this->statusDipinjamId = StatusAset::where('nama_status', 'Dipinjam')->value('id');
    }

    /**
     * Menampilkan daftar semua peminjaman (Aset per unit).
     */
    public function index()
    {
        $peminjaman = Peminjaman::with(['barang', 'userPeminjam']) // Eager load relasi baru
                                ->orderBy('tanggal_pinjam', 'desc')
                                ->orderBy('status_pinjam', 'asc')
                                ->get();

        return view('peminjaman.index', [
            'peminjaman' => $peminjaman,
            'judul' => 'Data Peminjaman Aset'
        ]);
    }

    /**
     * Menampilkan form untuk mencatat peminjaman baru (Aset per unit).
     */
    public function create()
    {
        // Ambil HANYA barang yang statusnya "Tersedia"
        $barangTersedia = Barang::where('id_status_aset', $this->statusTersediaId)
                                ->orderBy('nama_barang', 'asc')
                                ->get();
        
        return view('peminjaman.create', [
            'barang' => $barangTersedia, // Kirim daftar barang yang tersedia
            'judul' => 'Catat Peminjaman Aset'
        ]);
    }

    /**
     * Menyimpan data peminjaman baru dan MENGUBAH STATUS ASET.
     */
    public function store(Request $request)
    {
        // Validasi baru (tanpa 'jumlah_dipinjam')
        $validated = $request->validate([
            'id_barang' => 'required|exists:barang,id',
            'peminjam_eksternal' => 'required|string|max:100', // Kita asumsikan peminjam eksternal
            'tanggal_pinjam' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        // Cek ulang apakah barang masih tersedia (mencegah double booking)
        $barang = Barang::find($validated['id_barang']);
        if ($barang->id_status_aset != $this->statusTersediaId) {
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Gagal: Aset ini sudah tidak tersedia atau sedang dipinjam.');
        }

        try {
            DB::beginTransaction();

            // 1. Catat di tabel peminjaman
            Peminjaman::create([
                'id_barang' => $validated['id_barang'],
                'peminjam_eksternal' => $validated['peminjam_eksternal'],
                'id_user_peminjam' => null, // (Bisa diisi Auth::id() jika peminjam internal)
                'tanggal_pinjam' => $validated['tanggal_pinjam'],
                'keterangan' => $validated['keterangan'],
                'status_pinjam' => 'Dipinjam'
            ]);

            // 2. Ubah status aset di tabel barang
            $barang->update([
                'id_status_aset' => $this->statusDipinjamId
            ]);
            
            // -- LOGGING --
            $keterangan_log = "Mencatat peminjaman aset '" . $barang->nama_barang . " (Reg: " . $barang->register . ")' oleh " . $validated['peminjam_eksternal'];
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
     * Menandai barang sebagai 'dikembalikan' dan MENGUBAH STATUS ASET.
     */
    public function kembali(Peminjaman $peminjaman)
    {
        if ($peminjaman->status_pinjam == 'Dikembalikan') {
             return redirect()->route('peminjaman.index')
                             ->with('error', 'Aset ini statusnya sudah dikembalikan.');
        }

        try {
            DB::beginTransaction();

            // 1. Update status di tabel peminjaman
            $peminjaman->update([
                'status_pinjam' => 'Dikembalikan',
                'tanggal_kembali' => Carbon::today()
            ]);

            // 2. Update status aset di tabel barang
            $peminjaman->barang()->update([
                'id_status_aset' => $this->statusTersediaId
            ]);
            
            // -- LOGGING --
            $keterangan_log = "Mencatat pengembalian aset '" . $peminjaman->barang->nama_barang . " (Reg: " . $peminjaman->barang->register . ")' oleh " . $peminjaman->peminjam_eksternal;
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