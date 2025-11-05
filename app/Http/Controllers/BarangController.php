<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessCsvImport;
use Illuminate\Support\Facades\Storage;
use App\Models\Barang;
use App\Models\JenisBarang;
use App\Models\SumberBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;
use App\Models\Kondisi;

class BarangController extends Controller
{
    /**
     * Menampilkan daftar barang dengan filter dan pagination.
     */
    public function index(Request $request)
    {
        $limit = $request->get('limit', 25);
        // Tambahkan 'kondisi' di sini
        $query = Barang::with(['jenis', 'sumber', 'kondisi']); 
    
        if ($request->filled('jenis')) {
            $query->where('id_jenis', $request->jenis);
        }
        if ($request->filled('sumber')) {
            $query->where('id_sumber', $request->sumber);
        }
    
        $barang = $query->orderBy('id', 'desc')->paginate($limit)->withQueryString();
        $jenis_list = JenisBarang::orderBy('nama_jenis', 'asc')->get();
        $sumber_list = SumberBarang::orderBy('nama_sumber', 'asc')->get();
    
        return view('barang.index', [
            'barang' => $barang,
            'jenis_list' => $jenis_list,
            'sumber_list' => $sumber_list,
            'current_filters' => $request->only(['jenis', 'sumber']),
            'judul' => 'Daftar Barang'
        ]);
    }

    /**
     * Menampilkan form untuk menambah barang baru.
     */
    public function create()
    {
        $jenis = JenisBarang::orderBy('nama_jenis', 'asc')->get();
        $sumber = SumberBarang::orderBy('nama_sumber', 'asc')->get();
        $kondisi = Kondisi::orderBy('nama_kondisi', 'asc')->get(); // <-- TAMBAHKAN INI
    
        return view('barang.create', [
            'jenis' => $jenis,
            'sumber' => $sumber,
            'kondisi' => $kondisi, // <-- TAMBAHKAN INI
            'judul' => 'Tambah Barang'
        ]);
    }

    /**
     * Menyimpan data barang baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:100',
            'jumlah' => 'required|integer|min:1',
            'satuan' => 'required|string|max:20',
            'id_jenis' => 'required|exists:jenis_barang,id',
            'id_sumber' => 'required|exists:sumber_barang,id',
            'id_kondisi' => 'required|exists:kondisi,id', // <-- TAMBAHKAN INI
            'keterangan' => 'nullable|string',
        ]);

        $barang = Barang::create($validated);

        // -- LOGGING --
        LogAktivitas::create([
            'id_pengguna' => Auth::id(),
            'aksi' => 'TAMBAH',
            'tabel' => 'barang',
            'keterangan' => 'Menambah barang baru: ' . $barang->nama_barang
        ]);

        return redirect()->route('barang.index')
                         ->with('success', 'Data Barang berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail satu barang.
     */
    public function show(Barang $barang)
    {
        $barang->load(['jenis', 'sumber']);
        return view('barang.detail', [
            'barang' => $barang,
            'judul' => 'Detail Barang'
        ]);
    }

    /**
     * Menampilkan form untuk mengedit barang.
     */
    public function edit(Barang $barang)
    {
        $jenis = JenisBarang::orderBy('nama_jenis', 'asc')->get();
        $sumber = SumberBarang::orderBy('nama_sumber', 'asc')->get();
        $kondisi = Kondisi::orderBy('nama_kondisi', 'asc')->get(); // <-- TAMBAHKAN INI
    
        return view('barang.edit', [
            'barang' => $barang,
            'jenis' => $jenis,
            'sumber' => $sumber,
            'kondisi' => $kondisi, // <-- TAMBAHKAN INI
            'judul' => 'Edit Barang'
        ]);
    }

    /**
     * Memperbarui data barang di database.
     */
    public function update(Request $request, Barang $barang)
    {
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:100',
            'jumlah' => 'required|integer|min:0',
            'satuan' => 'required|string|max:20',
            'id_jenis' => 'required|exists:jenis_barang,id',
            'id_sumber' => 'required|exists:sumber_barang,id',
            'id_kondisi' => 'required|exists:kondisi,id', // <-- TAMBAHKAN INI
            'keterangan' => 'nullable|string',
        ]);

        $barang->update($validated);

        // -- LOGGING --
        LogAktivitas::create([
            'id_pengguna' => Auth::id(),
            'aksi' => 'UBAH',
            'tabel' => 'barang',
            'keterangan' => 'Mengubah data barang: ' . $barang->nama_barang
        ]);

        return redirect()->route('barang.index')
                         ->with('success', 'Data Barang berhasil diubah.');
    }

    /**
     * Menghapus data barang dari database.
     */
    public function destroy(Barang $barang)
    {
        $namaBarang = $barang->nama_barang;
        
        try {
            $barang->delete();
            
            // -- LOGGING --
            LogAktivitas::create([
                'id_pengguna' => Auth::id(),
                'aksi' => 'HAPUS',
                'tabel' => 'barang',
                'keterangan' => 'Menghapus barang: ' . $namaBarang
            ]);

            return redirect()->route('barang.index')
                             ->with('success', 'Data Barang berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('barang.index')
                             ->with('error', 'Data Barang gagal dihapus. Mungkin sedang dipinjam atau terkait data lain.');
        }
    }

    /**
     * Mencari barang untuk AJAX search.
     */
    public function cari(Request $request)
    {
        $keyword = $request->input('keyword');
        $barang = Barang::with(['jenis', 'sumber'])
                ->where('nama_barang', 'LIKE', "%$keyword%")
                ->orderBy('id', 'desc')
                ->get();
        return view('barang._search_results', ['barang' => $barang]);
    }

    /**
     * Menampilkan form upload CSV.
     */
    public function importCsvForm()
    {
        return view('barang.import', [
            'judul' => 'Import Barang dari CSV'
        ]);
    }

    /**
     * Memproses file CSV yang di-upload.
     */
    public function importCsv(Request $request)
    {
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt']);

        // 1. Simpan file ke storage (misal: storage/app/imports)
        // Kita simpan path filenya
        $path = $request->file('csv_file')->store('imports');

        // 2. Dapatkan user yang sedang login
        $user = Auth::user();

        // 3. "Lempar" tugas ini ke background job
        ProcessCsvImport::dispatch($path, $user);

        // 4. Langsung kembalikan ke user tanpa menunggu
        return redirect()->route('barang.index')
                        ->with('success', 'Import Berhasil. Data sedang diproses di background dan akan segera muncul.');
    }

    /**
     * Mengekspor data barang ke file CSV.
     */
    public function exportCsv()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="data_barang_' . date('Y-m-d') . '.csv"',
        ];
        $data_barang = Barang::with(['jenis', 'sumber'])->get();

        $callback = function() use ($data_barang) {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Nama Barang', 'Kuantitas', 'Satuan', 'Jenis', 'Sumber', 'Keterangan']);
            foreach ($data_barang as $barang) {
                fputcsv($output, [
                    $barang->nama_barang, $barang->jumlah, $barang->satuan,
                    $barang->jenis->nama_jenis ?? '-', $barang->sumber->nama_sumber ?? '-',
                    $barang->keterangan
                ]);
            }
            fclose($output);
        };
        return new StreamedResponse($callback, 200, $headers);
    }
}