<?php

namespace App\Http\Controllers;

use App\Models\JenisBarang;
use Illuminate\Http\Request;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;

class JenisBarangController extends Controller
{
    /**
     * Menampilkan daftar jenis barang.
     */
    public function index()
    {
        $data['jenis_barang'] = JenisBarang::orderBy('nama_jenis', 'asc')->get();
        $data['judul'] = 'Jenis Barang';
        return view('jenisbarang.index', $data);
    }

    /**
     * Menyimpan jenis barang baru (dari modal tambah).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_jenis' => 'required|string|max:100|unique:jenis_barang,nama_jenis'
        ]);

        JenisBarang::create($validated);
        
        // -- LOGGING --
        LogAktivitas::create([
            'id_pengguna' => Auth::id(),
            'aksi' => 'TAMBAH',
            'tabel' => 'jenis_barang',
            'keterangan' => 'Menambah jenis baru: ' . $validated['nama_jenis']
        ]);

        return redirect()->route('jenisbarang.index')
                         ->with('success', 'Jenis Barang berhasil ditambahkan.');
    }

    /**
     * Mengambil data untuk modal edit (AJAX).
     */
    public function getUbah(Request $request)
    {
        $jenisBarang = JenisBarang::find($request->id);
        if ($jenisBarang) {
            return response()->json($jenisBarang);
        }
        return response()->json(['error' => 'Data not found'], 404);
    }

    /**
     * Memperbarui jenis barang (dari modal edit).
     */
    public function update(Request $request, JenisBarang $jenisBarang)
    {
        $validated = $request->validate([
            'nama_jenis' => 'required|string|max:100|unique:jenis_barang,nama_jenis,' . $jenisBarang->id
        ]);

        $jenisBarang->update($validated);

        // -- LOGGING --
        LogAktivitas::create([
            'id_pengguna' => Auth::id(),
            'aksi' => 'UBAH',
            'tabel' => 'jenis_barang',
            'keterangan' => 'Mengubah jenis barang: ' . $validated['nama_jenis']
        ]);

        return redirect()->route('jenisbarang.index')
                         ->with('success', 'Jenis Barang berhasil diubah.');
    }

    /**
     * Menghapus jenis barang.
     */
    public function destroy(JenisBarang $jenisBarang)
    {
        $namaJenis = $jenisBarang->nama_jenis; // Simpan nama untuk log
        try {
            $jenisBarang->delete();
            
            // -- LOGGING --
            LogAktivitas::create([
                'id_pengguna' => Auth::id(),
                'aksi' => 'HAPUS',
                'tabel' => 'jenis_barang',
                'keterangan' => 'Menghapus jenis barang: ' . $namaJenis
            ]);

            return redirect()->route('jenisbarang.index')
                             ->with('success', 'Jenis Barang berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('jenisbarang.index')
                             ->with('error', 'Gagal menghapus. Jenis barang ini mungkin sedang digunakan oleh data barang.');
        }
    }
}