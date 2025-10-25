<?php

namespace App\Http\Controllers;

use App\Models\JenisBarang;
use Illuminate\Http\Request;
// use App\Models\LogAktivitas; // (Opsional: untuk logging)
// use Illuminate\Support\Facades\Auth; // (Opsional: untuk logging)

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
     * Diterjemahkan dari: JenisBarangController.php -> tambah()
     */
    public function store(Request $request)
    {
        // Validasi (menggantikan 'empty(trim(...))')
        $validated = $request->validate([
            'nama_jenis' => 'required|string|max:100|unique:jenis_barang,nama_jenis'
        ]);

        JenisBarang::create($validated);
        
        // TODO: Logging
        // LogAktivitas::create([
        //     'id_pengguna' => Auth::id(),
        //     'aksi' => 'TAMBAH',
        //     'tabel' => 'jenis_barang',
        //     'keterangan' => 'Menambah jenis baru: ' . $validated['nama_jenis']
        // ]);

        return redirect()->route('jenisbarang.index')
                         ->with('success', 'Jenis Barang berhasil ditambahkan.');
    }

    /**
     * Mengambil data untuk modal edit (AJAX).
     * Diterjemahkan dari: JenisBarangController.php -> getUbah()
     */
    public function getUbah(Request $request)
    {
        // Pastikan Anda telah membuat Model JenisBarang
        $jenisBarang = JenisBarang::find($request->id);
        if ($jenisBarang) {
            return response()->json($jenisBarang);
        }
        return response()->json(['error' => 'Data not found'], 404);
    }

    /**
     * Memperbarui jenis barang (dari modal edit).
     * Diterjemahkan dari: JenisBarangController.php -> ubah()
     */
    public function update(Request $request, JenisBarang $jenisBarang)
    {
        // $jenisBarang sudah di-fetch otomatis oleh Laravel
        $validated = $request->validate([
            // unique:nama_tabel,nama_kolom,id_yang_dikecualikan
            'nama_jenis' => 'required|string|max:100|unique:jenis_barang,nama_jenis,' . $jenisBarang->id
        ]);

        $jenisBarang->update($validated);

        // TODO: Logging
        // LogAktivitas::create([... 'aksi' => 'UBAH' ...]);

        return redirect()->route('jenisbarang.index')
                         ->with('success', 'Jenis Barang berhasil diubah.');
    }

    /**
     * Menghapus jenis barang.
     * Diterjemahkan dari: JenisBarangController.php -> hapus()
     */
    public function destroy(JenisBarang $jenisBarang)
    {
        try {
            // $jenisBarang sudah di-fetch otomatis
            $jenisBarang->delete();
            
            // TODO: Logging
            // LogAktivitas::create([... 'aksi' => 'HAPUS' ...]);

            return redirect()->route('jenisbarang.index')
                             ->with('success', 'Jenis Barang berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Menangani jika jenis terikat dengan data barang
            return redirect()->route('jenisbarang.index')
                             ->with('error', 'Gagal menghapus. Jenis barang ini mungkin sedang digunakan oleh data barang.');
        }
    }
}