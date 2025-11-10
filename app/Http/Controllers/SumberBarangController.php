<?php

namespace App\Http\Controllers;

use App\Models\SumberBarang;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SumberBarangController extends Controller
{
    /**
     * Menampilkan daftar sumber barang.
     */
    public function index()
    {
        return view('sumber-barang.index', [
            'sumber_barang' => SumberBarang::orderBy('nama_sumber', 'asc')->get(),
            'judul' => 'Kelola Sumber Barang'
        ]);
    }

    /**
     * Menampilkan form untuk membuat sumber baru.
     */
    public function create()
    {
        return view('sumber-barang.create', [
            'judul' => 'Tambah Sumber Barang Baru'
        ]);
    }

    /**
     * Menyimpan sumber baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_sumber' => 'required|string|max:100|unique:sumber_barang,nama_sumber'
        ]);

        $sumber = SumberBarang::create($validated);
        
        LogAktivitas::create([
            'id_pengguna' => Auth::id(),
            'aksi' => 'TAMBAH',
            'tabel' => 'sumber_barang',
            'keterangan' => 'Menambah sumber baru: ' . $sumber->nama_sumber
        ]);

        return redirect()->route('sumberbarang.index')
                         ->with('success', 'Sumber Barang berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit sumber.
     */
    public function edit(SumberBarang $sumberBarang)
    {
        return view('sumber-barang.edit', [
            'sumberBarang' => $sumberBarang,
            'judul' => 'Edit Sumber Barang'
        ]);
    }

    /**
     * Memperbarui sumber barang.
     */
    public function update(Request $request, SumberBarang $sumberBarang)
    {
        $validated = $request->validate([
            'nama_sumber' => 'required|string|max:100|unique:sumber_barang,nama_sumber,' . $sumberBarang->id
        ]);

        $sumberBarang->update($validated);

        LogAktivitas::create([
            'id_pengguna' => Auth::id(),
            'aksi' => 'UBAH',
            'tabel' => 'sumber_barang',
            'keterangan' => 'Mengubah sumber barang: ' . $sumberBarang->nama_sumber
        ]);

        return redirect()->route('sumberbarang.index')
                         ->with('success', 'Sumber Barang berhasil diubah.');
    }

    /**
     * Menghapus sumber barang.
     */
    public function destroy(SumberBarang $sumberBarang)
    {
        // Cek apakah masih dipakai oleh barang
        if ($sumberBarang->barang()->count() > 0) {
            return redirect()->route('sumberbarang.index')
                             ->with('error', 'Gagal: Sumber ini masih digunakan oleh data aset.');
        }
        
        $namaSumber = $sumberBarang->nama_sumber;
        $sumberBarang->delete();
        
        LogAktivitas::create([
            'id_pengguna' => Auth::id(),
            'aksi' => 'HAPUS',
            'tabel' => 'sumber_barang',
            'keterangan' => 'Menghapus sumber barang: ' . $namaSumber
        ]);

        return redirect()->route('sumberbarang.index')
                         ->with('success', 'Sumber Barang berhasil dihapus.');
    }
}