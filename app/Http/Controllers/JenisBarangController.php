<?php

namespace App\Http\Controllers;

use App\Models\JenisBarang;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JenisBarangController extends Controller
{
    /**
     * Menampilkan daftar jenis barang.
     */
    public function index()
    {
        return view('jenis-barang.index', [
            'jenis_barang' => JenisBarang::orderBy('nama_jenis', 'asc')->get(),
            'judul' => 'Kelola Jenis Barang'
        ]);
    }

    /**
     * Menampilkan form untuk membuat jenis baru.
     */
    public function create()
    {
        return view('jenis-barang.create', [
            'judul' => 'Tambah Jenis Barang Baru'
        ]);
    }

    /**
     * Menyimpan jenis baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_jenis' => 'required|string|max:100|unique:jenis_barang,nama_jenis'
        ]);

        $jenis = JenisBarang::create($validated);
        
        LogAktivitas::create([
            'id_pengguna' => Auth::id(),
            'aksi' => 'TAMBAH',
            'tabel' => 'jenis_barang',
            'keterangan' => 'Menambah jenis baru: ' . $jenis->nama_jenis
        ]);

        return redirect()->route('jenisbarang.index')
                         ->with('success', 'Jenis Barang berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit jenis.
     */
    public function edit(JenisBarang $jenisBarang)
    {
        return view('jenis-barang.edit', [
            'jenisBarang' => $jenisBarang,
            'judul' => 'Edit Jenis Barang'
        ]);
    }

    /**
     * Memperbarui jenis barang.
     */
    public function update(Request $request, JenisBarang $jenisBarang)
    {
        $validated = $request->validate([
            'nama_jenis' => 'required|string|max:100|unique:jenis_barang,nama_jenis,' . $jenisBarang->id
        ]);

        $jenisBarang->update($validated);

        LogAktivitas::create([
            'id_pengguna' => Auth::id(),
            'aksi' => 'UBAH',
            'tabel' => 'jenis_barang',
            'keterangan' => 'Mengubah jenis barang: ' . $jenisBarang->nama_jenis
        ]);

        return redirect()->route('jenisbarang.index')
                         ->with('success', 'Jenis Barang berhasil diubah.');
    }

    /**
     * Menghapus jenis barang.
     */
    public function destroy(JenisBarang $jenisBarang)
    {
        // Cek apakah masih dipakai oleh barang
        if ($jenisBarang->barang()->count() > 0) {
            return redirect()->route('jenisbarang.index')
                             ->with('error', 'Gagal: Jenis ini masih digunakan oleh data aset.');
        }

        $namaJenis = $jenisBarang->nama_jenis;
        $jenisBarang->delete();
        
        LogAktivitas::create([
            'id_pengguna' => Auth::id(),
            'aksi' => 'HAPUS',
            'tabel' => 'jenis_barang',
            'keterangan' => 'Menghapus jenis barang: ' . $namaJenis
        ]);

        return redirect()->route('jenisbarang.index')
                         ->with('success', 'Jenis Barang berhasil dihapus.');
    }
}