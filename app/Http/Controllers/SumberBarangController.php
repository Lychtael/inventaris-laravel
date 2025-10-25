<?php

namespace App\Http\Controllers;

use App\Models\SumberBarang;
use Illuminate\Http\Request;
// use App\Models\LogAktivitas; // (Opsional)
// use Illuminate\Support\Facades\Auth; // (Opsional)

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
     * Diterjemahkan dari: SumberBarangController.php -> tambah()
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_sumber' => 'required|string|max:100|unique:sumber_barang,nama_sumber'
        ]);

        SumberBarang::create($validated);
        
        // TODO: Logging
        // LogAktivitas::create([... 'keterangan' => 'Menambah sumber baru: ...']);

        return redirect()->route('sumberbarang.index')
                         ->with('success', 'Sumber Barang berhasil ditambahkan.');
    }

    /**
     * Mengambil data untuk modal edit (AJAX).
     * Diterjemahkan dari: SumberBarangController.php -> getUbah()
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
     * Diterjemahkan dari: SumberBarangController.php -> ubah()
     */
    public function update(Request $request, SumberBarang $sumberBarang)
    {
        // $sumberBarang sudah di-fetch otomatis
        $validated = $request->validate([
            'nama_sumber' => 'required|string|max:100|unique:sumber_barang,nama_sumber,' . $sumberBarang->id
        ]);

        $sumberBarang->update($validated);

        // TODO: Logging
        // LogAktivitas::create([... 'aksi' => 'UBAH' ...]);

        return redirect()->route('sumberbarang.index')
                         ->with('success', 'Sumber Barang berhasil diubah.');
    }

    /**
     * Menghapus sumber barang.
     * Diterjemahkan dari: SumberBarangController.php -> hapus()
     */
    public function destroy(SumberBarang $sumberBarang)
    {
        try {
            $sumberBarang->delete();
            
            // TODO: Logging
            // LogAktivitas::create([... 'aksi' => 'HAPUS' ...]);

            return redirect()->route('sumberbarang.index')
                             ->with('success', 'Sumber Barang berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('sumberbarang.index')
                             ->with('error', 'Gagal menghapus. Sumber barang ini mungkin sedang digunakan oleh data barang.');
        }
    }
}