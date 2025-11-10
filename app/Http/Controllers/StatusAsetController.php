<?php

namespace App\Http\Controllers;

use App\Models\StatusAset;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatusAsetController extends Controller
{
    /**
     * Menampilkan daftar semua status aset.
     */
    public function index()
    {
        return view('status-aset.index', [
            'status_aset' => StatusAset::orderBy('nama_status', 'asc')->get(),
            'judul' => 'Kelola Status Aset'
        ]);
    }

    /**
     * Menampilkan form untuk membuat status baru.
     */
    public function create()
    {
        return view('status-aset.create', [
            'judul' => 'Tambah Status Aset Baru'
        ]);
    }

    /**
     * Menyimpan status baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_status' => 'required|string|max:50|unique:status_aset,nama_status',
        ]);

        $statusAset = StatusAset::create($validated);

        LogAktivitas::create([
            'id_pengguna' => Auth::id(),
            'aksi' => 'TAMBAH',
            'tabel' => 'status_aset',
            'keterangan' => 'Menambah status aset baru: ' . $statusAset->nama_status
        ]);

        return redirect()->route('status-aset.index')
                         ->with('success', 'Status aset baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit status.
     */
    public function edit(StatusAset $statusAset)
    {
        // Ganti nama variabel agar tidak konflik di URL
        // (Laravel otomatis mengirim $status_aset)
        return view('status-aset.edit', [
            'statusAset' => $statusAset,
            'judul' => 'Edit Status Aset'
        ]);
    }

    /**
     * Memperbarui status di database.
     */
    public function update(Request $request, StatusAset $statusAset)
    {
        $validated = $request->validate([
            'nama_status' => 'required|string|max:50|unique:status_aset,nama_status,' . $statusAset->id,
        ]);

        $statusAset->update($validated);

        LogAktivitas::create([
            'id_pengguna' => Auth::id(),
            'aksi' => 'UBAH',
            'tabel' => 'status_aset',
            'keterangan' => 'Mengubah status aset: ' . $statusAset->nama_status
        ]);

        return redirect()->route('status-aset.index')
                         ->with('success', 'Data status aset berhasil diubah.');
    }

    /**
     * Menghapus status dari database.
     */
    public function destroy(StatusAset $statusAset)
    {
        // Cek apakah status masih dipakai oleh barang
        if ($statusAset->barang()->count() > 0) {
            return redirect()->route('status-aset.index')
                             ->with('error', 'Gagal: Status ini masih digunakan oleh data aset.');
        }

        $namaStatus = $statusAset->nama_status;
        $statusAset->delete();

        LogAktivitas::create([
            'id_pengguna' => Auth::id(),
            'aksi' => 'HAPUS',
            'tabel' => 'status_aset',
            'keterangan' => 'Menghapus status aset: ' . $namaStatus
        ]);

        return redirect()->route('status-aset.index')
                         ->with('success', 'Status aset berhasil dihapus.');
    }
}