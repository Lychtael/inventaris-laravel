<?php

namespace App\Http\Controllers;

use App\Models\Dinas;
use App\Models\LogAktivitas; // Untuk logging
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Untuk logging

class DinasController extends Controller
{
    /**
     * Menampilkan daftar semua dinas.
     */
    public function index()
    {
        return view('dinas.index', [
            'dinas' => Dinas::orderBy('nama_dinas', 'asc')->get(),
            'judul' => 'Kelola Dinas'
        ]);
    }

    /**
     * Menampilkan form untuk membuat dinas baru.
     */
    public function create()
    {
        return view('dinas.create', [
            'judul' => 'Tambah Dinas Baru'
        ]);
    }

    /**
     * Menyimpan dinas baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_dinas' => 'required|string|max:150|unique:dinas,nama_dinas',
        ]);

        $dinas = Dinas::create($validated);

        LogAktivitas::create([
            'id_pengguna' => Auth::id(),
            'aksi' => 'TAMBAH',
            'tabel' => 'dinas',
            'keterangan' => 'Menambah dinas baru: ' . $dinas->nama_dinas
        ]);

        return redirect()->route('dinas.index')
                         ->with('success', 'Dinas baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit dinas.
     */
    public function edit(Dinas $dina) // Variabel $dina (singular dari dinas)
    {
        return view('dinas.edit', [
            'dinas' => $dina,
            'judul' => 'Edit Dinas'
        ]);
    }

    /**
     * Memperbarui dinas di database.
     */
    public function update(Request $request, Dinas $dina)
    {
        $validated = $request->validate([
            'nama_dinas' => 'required|string|max:150|unique:dinas,nama_dinas,' . $dina->id,
        ]);

        $dina->update($validated);

        LogAktivitas::create([
            'id_pengguna' => Auth::id(),
            'aksi' => 'UBAH',
            'tabel' => 'dinas',
            'keterangan' => 'Mengubah nama dinas: ' . $dina->nama_dinas
        ]);

        return redirect()->route('dinas.index')
                         ->with('success', 'Data dinas berhasil diubah.');
    }

    /**
     * Menghapus dinas dari database.
     */
    public function destroy(Dinas $dina)
    {
        // Cek apakah dinas masih dipakai oleh bidang
        if ($dina->bidang()->count() > 0) {
            return redirect()->route('dinas.index')
                             ->with('error', 'Gagal: Dinas ini masih memiliki Bidang. Hapus dulu semua bidang di dalamnya.');
        }

        $namaDinas = $dina->nama_dinas;
        $dina->delete();

        LogAktivitas::create([
            'id_pengguna' => Auth::id(),
            'aksi' => 'HAPUS',
            'tabel' => 'dinas',
            'keterangan' => 'Menghapus dinas: ' . $namaDinas
        ]);

        return redirect()->route('dinas.index')
                         ->with('success', 'Dinas berhasil dihapus.');
    }
}