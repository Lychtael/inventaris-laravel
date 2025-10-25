<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // <-- Penting untuk Transaksi
use Carbon\Carbon; // <-- Penting untuk manipulasi tanggal
// use App\Models\LogAktivitas; // (Opsional: untuk logging)
// use Illuminate\Support\Facades\Auth; // (Opsional: untuk logging)

class PeminjamanController extends Controller
{
    /**
     * Menampilkan daftar semua peminjaman.
     * Diterjemahkan dari: PeminjamanController.php -> index()
     * dan Peminjaman_model.php -> getAllPeminjaman()
     */
    public function index()
    {
        // Eager load relasi 'barang'
        // Order by sama seperti di model lama
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
     * Diterjemahkan dari: PeminjamanController.php -> create()
     */
    public function create()
    {
        // Ambil barang yang stoknya masih ada
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
     * Diterjemahkan dari: PeminjamanController.php -> store()
     * dan Peminjaman_model.php -> tambahDataPeminjaman() & updateStokBarang()
     */
    public function store(Request $request)
    {
        // 1. Validasi dasar
        $validated = $request->validate([
            'id_barang' => 'required|exists:barang,id',
            'peminjam' => 'required|string|max:100',
            'jumlah_dipinjam' => 'required|integer|min:1',
            'tanggal_pinjam' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        // 2. Validasi stok manual (dari PeminjamanController.php lama)
        $barang = Barang::find($validated['id_barang']);
        if ($barang->jumlah < $validated['jumlah_dipinjam']) {
            // Redirect kembali dengan error spesifik
            return redirect()->back()
                             ->withInput() // Mengembalikan input sebelumnya
                             ->with('error', 'Stok barang tidak mencukupi. Stok tersisa: ' . $barang->jumlah);
        }

        // 3. Gunakan Transaksi Database
        // Ini memastikan jika salah satu query gagal, semua akan dibatalkan
        try {
            DB::beginTransaction();

            // Buat data peminjaman
            Peminjaman::create([
                'id_barang' => $validated['id_barang'],
                'peminjam' => $validated['peminjam'],
                'jumlah_dipinjam' => $validated['jumlah_dipinjam'],
                'tanggal_pinjam' => $validated['tanggal_pinjam'],
                'keterangan' => $validated['keterangan'],
                'status' => 'dipinjam' // Default status
            ]);

            // Kurangi stok barang (dari updateStokBarang('kurang'))
            $barang->decrement('jumlah', $validated['jumlah_dipinjam']);
            
            DB::commit(); // Sukses, simpan semua perubahan

            // TODO: Logging
            // LogAktivitas::create([... 'aksi' => 'PINJAM' ...]);

            return redirect()->route('peminjaman.index')
                             ->with('success', 'Data Peminjaman berhasil dicatat.');

        } catch (\Exception $e) {
            DB::rollBack(); // Gagal, batalkan semua perubahan
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Gagal mencatat peminjaman: ' . $e->getMessage());
        }
    }

    /**
     * Menandai barang sebagai 'dikembalikan' dan menambah stok.
     * Diterjemahkan dari: PeminjamanController.php -> kembali()
     * dan Peminjaman_model.php -> kembalikanBarang() & updateStokBarang()
     */
    public function kembali(Peminjaman $peminjaman)
    {
        // Gunakan Route Model Binding untuk langsung mendapatkan data $peminjaman
        
        // Cek jika barang sudah dikembalikan
        if ($peminjaman->status == 'dikembalikan') {
             return redirect()->route('peminjaman.index')
                             ->with('error', 'Barang ini sudah dikembalikan.');
        }

        // Gunakan Transaksi Database
        try {
            DB::beginTransaction();

            // 1. Update status peminjaman
            $peminjaman->update([
                'status' => 'dikembalikan',
                'tanggal_kembali' => Carbon::today() // Set tanggal hari ini
            ]);

            // 2. Tambah stok barang (dari updateStokBarang('tambah'))
            // Kita bisa akses relasi barang via $peminjaman->barang
            $peminjaman->barang->increment('jumlah', $peminjaman->jumlah_dipinjam);

            DB::commit(); // Sukses

            // TODO: Logging
            // LogAktivitas::create([... 'aksi' => 'KEMBALI' ...]);

            return redirect()->route('peminjaman.index')
                             ->with('success', 'Data Pengembalian berhasil dicatat.');

        } catch (\Exception $e) {
            DB::rollBack(); // Gagal
            return redirect()->route('peminjaman.index')
                             ->with('error', 'Gagal memproses pengembalian: ' . $e->getMessage());
        }
    }
}