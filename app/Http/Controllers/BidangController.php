<?php

namespace App\Http\Controllers;

use App\Models\Bidang;
use App\Models\Dinas; // <-- Kita butuh ini untuk dropdown
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BidangController extends Controller
{
    /**
     * Menampilkan daftar semua bidang.
     */
    public function index()
    {
        return view('bidang.index', [
            // Eager load relasi 'dinas' agar tidak N+1 query
            'bidang' => Bidang::with('dinas')->orderBy('nama_bidang', 'asc')->get(),
            'judul' => 'Kelola Bidang'
        ]);
    }

    /**
     * Menampilkan form untuk membuat bidang baru.
     */
    public function create()
    {
        return view('bidang.create', [
            'dinas' => Dinas::orderBy('nama_dinas', 'asc')->get(), // Kirim daftar dinas
            'judul' => 'Tambah Bidang Baru'
        ]);
    }

    /**
     * Menyimpan bidang baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_bidang' => 'required|string|max:150',
            'id_dinas' => 'required|exists:dinas,id', // Validasi dropdown
        ]);

        $bidang = Bidang::create($validated);

        LogAktivitas::create([
            'id_pengguna' => Auth::id(),
            'aksi' => 'TAMBAH',
            'tabel' => 'bidang',
            'keterangan' => 'Menambah bidang baru: ' . $bidang->nama_bidang . ' (Dinas: ' . $bidang->dinas->nama_dinas . ')'
        ]);

        return redirect()->route('bidang.index')
                         ->with('success', 'Bidang baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit bidang.
     */
    public function edit(Bidang $bidang)
    {
        return view('bidang.edit', [
            'bidang' => $bidang,
            'dinas' => Dinas::orderBy('nama_dinas', 'asc')->get(), // Kirim daftar dinas
            'judul' => 'Edit Bidang'
        ]);
    }

    /**
     * Memperbarui bidang di database.
     */
    public function update(Request $request, Bidang $bidang)
    {
        $validated = $request->validate([
            'nama_bidang' => 'required|string|max:150',
            'id_dinas' => 'required|exists:dinas,id', // Validasi dropdown
        ]);

        $bidang->update($validated);

        LogAktivitas::create([
            'id_pengguna' => Auth::id(),
            'aksi' => 'UBAH',
            'tabel' => 'bidang',
            'keterangan' => 'Mengubah nama bidang: ' . $bidang->nama_bidang
        ]);

        return redirect()->route('bidang.index')
                         ->with('success', 'Data bidang berhasil diubah.');
    }

    /**
     * Menghapus bidang dari database.
     */
    public function destroy(Bidang $bidang)
    {
        // Cek apakah bidang masih dipakai oleh barang
        // (Berkat perbaikan model kita, $bidang->barang() sekarang berfungsi)
        if ($bidang->barang()->count() > 0) {
            return redirect()->route('bidang.index')
                             ->with('error', 'Gagal: Bidang ini masih digunakan oleh data aset.');
        }

        $namaBidang = $bidang->nama_bidang;
        $bidang->delete();

        LogAktivitas::create([
            'id_pengguna' => Auth::id(),
            'aksi' => 'HAPUS',
            'tabel' => 'bidang',
            'keterangan' => 'Menghapus bidang: ' . $namaBidang
        ]);

        return redirect()->route('bidang.index')
                         ->with('success', 'Bidang berhasil dihapus.');
    }
}