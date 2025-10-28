<?php

namespace App\Http\Controllers;

use App\Models\SumberBarang;
use Illuminate\Http\Request;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;

class SumberBarangController extends Controller
{
    /**
     * Menampilkan daftar sumber barang.
     */
    public function index()
    {
        $data['sumber_barang'] = SumberBarang::orderBy('nama_sumber', 'asc')->get();
        $data['judul'] = 'Sumber Barang';
        return view('sumberbarang.index', $data);
    }

    /**
     * Menyimpan sumber barang baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_sumber' => 'required|string|max:100|unique:sumber_barang,nama_sumber'
        ]);

        SumberBarang::create($validated);
        
        // -- LOGGING --
        LogAktivitas::create([
            'id_pengguna' => Auth::id(),
            'aksi' => 'TAMBAH',
            'tabel' => 'sumber_barang',
            'keterangan' => 'Menambah sumber baru: ' . $validated['nama_sumber']
        ]);

        return redirect()->route('sumberbarang.index')
                         ->with('success', 'Sumber Barang berhasil ditambahkan.');
    }

    /**
     * Mengambil data untuk modal edit (AJAX).
     */
    public function getUbah(Request $request)
    {
        $sumberBarang = SumberBarang::find($request->id);
        if ($sumberBarang) {
            return response()->json($sumberBarang);
        }
        return response()->json(['error' => 'Data not found'], 404);
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

        // -- LOGGING --
        LogAktivitas::create([
            'id_pengguna' => Auth::id(),
            'aksi' => 'UBAH',
            'tabel' => 'sumber_barang',
            'keterangan' => 'Mengubah sumber barang: ' . $validated['nama_sumber']
        ]);

        return redirect()->route('sumberbarang.index')
                         ->with('success', 'Sumber Barang berhasil diubah.');
    }

    /**
     * Menghapus sumber barang.
     */
    public function destroy(SumberBarang $sumberBarang)
    {
        $namaSumber = $sumberBarang->nama_sumber; // Simpan nama untuk log
        try {
            $sumberBarang->delete();
            
            // -- LOGGING --
            LogAktivitas::create([
                'id_pengguna' => Auth::id(),
                'aksi' => 'HAPUS',
                'tabel' => 'sumber_barang',
                'keterangan' => 'Menghapus sumber barang: ' . $namaSumber
            ]);

            return redirect()->route('sumberbarang.index')
                             ->with('success', 'Sumber Barang berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('sumberbarang.index')
                             ->with('error', 'Gagal menghapus. Sumber barang ini mungkin sedang digunakan oleh data barang.');
        }
    }
}