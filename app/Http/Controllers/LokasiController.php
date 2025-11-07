<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;
use App\Models\LogAktivitas; // Untuk logging
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Untuk logging

class LokasiController extends Controller
{
    /**
     * Menampilkan daftar semua lokasi.
     */
    public function index()
    {
        return view('lokasi.index', [
            'lokasi' => Lokasi::orderBy('nama_lokasi', 'asc')->get(),
            'judul' => 'Kelola Lokasi/Ruangan'
        ]);
    }

    /**
     * Menampilkan form untuk membuat lokasi baru.
     */
    public function create()
    {
        return view('lokasi.create', [
            'judul' => 'Tambah Lokasi Baru'
        ]);
    }

    /**
     * Menyimpan lokasi baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lokasi' => 'required|string|max:100|unique:lokasi,nama_lokasi',
            'penanggung_jawab' => 'nullable|string|max:255',
        ]);

        $lokasi = Lokasi::create($validated);

        LogAktivitas::create([
            'id_pengguna' => Auth::id(),
            'aksi' => 'TAMBAH',
            'tabel' => 'lokasi',
            'keterangan' => 'Menambah lokasi baru: ' . $lokasi->nama_lokasi
        ]);

        return redirect()->route('lokasi.index')
                         ->with('success', 'Lokasi baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit lokasi.
     */
    public function edit(Lokasi $lokasi)
    {
        return view('lokasi.edit', [
            'lokasi' => $lokasi,
            'judul' => 'Edit Lokasi'
        ]);
    }

    /**
     * Memperbarui lokasi di database.
     */
    public function update(Request $request, Lokasi $lokasi)
    {
        $validated = $request->validate([
            'nama_lokasi' => 'required|string|max:100|unique:lokasi,nama_lokasi,' . $lokasi->id,
            'penanggung_jawab' => 'nullable|string|max:255',
        ]);

        $lokasi->update($validated);

        LogAktivitas::create([
            'id_pengguna' => Auth::id(),
            'aksi' => 'UBAH',
            'tabel' => 'lokasi',
            'keterangan' => 'Mengubah lokasi: ' . $lokasi->nama_lokasi
        ]);

        return redirect()->route('lokasi.index')
                         ->with('success', 'Data lokasi berhasil diubah.');
    }

    /**
     * Menghapus lokasi dari database.
     */
    public function destroy(Lokasi $lokasi)
    {
        // Cek apakah lokasi masih dipakai oleh barang
        if ($lokasi->barang()->count() > 0) {
            return redirect()->route('lokasi.index')
                             ->with('error', 'Gagal: Lokasi ini masih digunakan oleh data aset.');
        }

        $namaLokasi = $lokasi->nama_lokasi;
        $lokasi->delete();

        LogAktivitas::create([
            'id_pengguna' => Auth::id(),
            'aksi' => 'HAPUS',
            'tabel' => 'lokasi',
            'keterangan' => 'Menghapus lokasi: ' . $namaLokasi
        ]);

        return redirect()->route('lokasi.index')
                         ->with('success', 'Lokasi berhasil dihapus.');
    }
}