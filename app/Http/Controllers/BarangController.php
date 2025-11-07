<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\JenisBarang;
use App\Models\SumberBarang;
use App\Models\Kondisi;
use App\Models\Lokasi;
use App\Models\StatusAset;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Jobs\ProcessAsetImport;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BarangController extends Controller
{
    /**
     * Menampilkan daftar ASET (logika baru)
     */
    public function index(Request $request)
    {
        $limit = $request->get('limit', 25);
        
        // Eager load semua relasi baru
        $query = Barang::with(['jenis', 'sumber', 'kondisi', 'lokasi', 'statusAset']);

        // Filter baru (opsional, bisa kita tambahkan di view nanti)
        if ($request->filled('id_jenis')) {
            $query->where('id_jenis', $request->id_jenis);
        }
        if ($request->filled('id_sumber')) {
            $query->where('id_sumber', $request->id_sumber);
        }
        if ($request->filled('id_kondisi')) {
            $query->where('id_kondisi', $request->id_kondisi);
        }
        if ($request->filled('id_lokasi')) {
            $query->where('id_lokasi', $request->id_lokasi);
        }
        if ($request->filled('id_status_aset')) {
            $query->where('id_status_aset', $request->id_status_aset);
        }
        
        $barang = $query->orderBy('id', 'desc')->paginate($limit)->withQueryString();
        
        // Ambil semua data master untuk filter
        $jenis_list = JenisBarang::orderBy('nama_jenis', 'asc')->get();
        $sumber_list = SumberBarang::orderBy('nama_sumber', 'asc')->get();
        $kondisi_list = Kondisi::orderBy('nama_kondisi', 'asc')->get();
        $lokasi_list = Lokasi::orderBy('nama_lokasi', 'asc')->get();
        $status_aset_list = StatusAset::orderBy('nama_status', 'asc')->get();

        return view('barang.index', [
            'barang' => $barang,
            'jenis_list' => $jenis_list,
            'sumber_list' => $sumber_list,
            'kondisi_list' => $kondisi_list,
            'lokasi_list' => $lokasi_list,
            'status_aset_list' => $status_aset_list,
            'current_filters' => $request->only(['id_jenis', 'id_sumber', 'id_kondisi', 'id_lokasi', 'id_status_aset']),
            'judul' => 'Daftar Aset Barang'
        ]);
    }

    /**
     * Menampilkan form untuk menambah ASET baru.
     */
    public function create()
    {
        // Ambil semua data master untuk dropdown form
        $data = [
            'jenis' => JenisBarang::orderBy('nama_jenis', 'asc')->get(),
            'sumber' => SumberBarang::orderBy('nama_sumber', 'asc')->get(),
            'kondisi' => Kondisi::orderBy('nama_kondisi', 'asc')->get(),
            'lokasi' => Lokasi::orderBy('nama_lokasi', 'asc')->get(),
            'status_aset' => StatusAset::orderBy('nama_status', 'asc')->get(),
            'judul' => 'Tambah Aset'
        ];
        
        return view('barang.create', $data);
    }

    /**
     * Menyimpan ASET baru ke database (logika baru).
     */
    public function store(Request $request)
    {
        // Validasi baru (tanpa 'jumlah' dan 'satuan')
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kode_barang' => 'nullable|string|max:100',
            'register' => 'nullable|string|max:100',
            'merk_type' => 'nullable|string|max:100',
            'tahun_pembelian' => 'nullable|integer|min:1900|max:'.date('Y'),
            'harga' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'id_jenis' => 'required|exists:jenis_barang,id',
            'id_sumber' => 'required|exists:sumber_barang,id',
            'id_kondisi' => 'required|exists:kondisi,id',
            'id_lokasi' => 'required|exists:lokasi,id',
            'id_status_aset' => 'required|exists:status_aset,id',
        ]);

        $barang = Barang::create($validated);

        // -- LOGGING --
        LogAktivitas::create([
            'id_pengguna' => Auth::id(),
            'aksi' => 'TAMBAH',
            'tabel' => 'barang',
            'keterangan' => 'Menambah aset baru: ' . $barang->nama_barang . ' (Reg: ' . $barang->register . ')'
        ]);

        return redirect()->route('barang.index')
                         ->with('success', 'Data Aset berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail satu ASET.
     */
    public function show(Barang $barang)
    {
        // Load semua relasi
        $barang->load(['jenis', 'sumber', 'kondisi', 'lokasi', 'statusAset']);
        return view('barang.detail', [
            'barang' => $barang,
            'judul' => 'Detail Aset'
        ]);
    }

    /**
     * Menampilkan form untuk mengedit ASET.
     */
    public function edit(Barang $barang)
    {
        // Ambil semua data master untuk dropdown form
        $data = [
            'barang' => $barang,
            'jenis' => JenisBarang::orderBy('nama_jenis', 'asc')->get(),
            'sumber' => SumberBarang::orderBy('nama_sumber', 'asc')->get(),
            'kondisi' => Kondisi::orderBy('nama_kondisi', 'asc')->get(),
            'lokasi' => Lokasi::orderBy('nama_lokasi', 'asc')->get(),
            'status_aset' => StatusAset::orderBy('nama_status', 'asc')->get(),
            'judul' => 'Edit Aset'
        ];
        
        return view('barang.edit', $data);
    }

    /**
     * Memperbarui data ASET di database (logika baru).
     */
    public function update(Request $request, Barang $barang)
    {
        // Validasi baru (tanpa 'jumlah' dan 'satuan')
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kode_barang' => 'nullable|string|max:100',
            'register' => 'nullable|string|max:100',
            'merk_type' => 'nullable|string|max:100',
            'tahun_pembelian' => 'nullable|integer|min:1900|max:'.date('Y'),
            'harga' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'id_jenis' => 'required|exists:jenis_barang,id',
            'id_sumber' => 'required|exists:sumber_barang,id',
            'id_kondisi' => 'required|exists:kondisi,id',
            'id_lokasi' => 'required|exists:lokasi,id',
            'id_status_aset' => 'required|exists:status_aset,id',
        ]);

        $barang->update($validated);

        // -- LOGGING --
        LogAktivitas::create([
            'id_pengguna' => Auth::id(),
            'aksi' => 'UBAH',
            'tabel' => 'barang',
            'keterangan' => 'Mengubah data aset: ' . $barang->nama_barang . ' (Reg: ' . $barang->register . ')'
        ]);

        return redirect()->route('barang.index')
                         ->with('success', 'Data Aset berhasil diubah.');
    }

    /**
     * Menghapus data ASET dari database.
     */
    public function destroy(Barang $barang)
    {
        $namaBarang = $barang->nama_barang . ' (Reg: ' . $barang->register . ')';
        
        try {
            $barang->delete();
            
            // -- LOGGING --
            LogAktivitas::create([
                'id_pengguna' => Auth::id(),
                'aksi' => 'HAPUS',
                'tabel' => 'barang',
                'keterangan' => 'Menghapus aset: ' . $namaBarang
            ]);

            return redirect()->route('barang.index')
                             ->with('success', 'Data Aset berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Kita perlu mengubah pesan error ini, mungkin terkait peminjaman
            return redirect()->route('barang.index')
                             ->with('error', 'Data Aset gagal dihapus. Mungkin terkait data peminjaman.');
        }
    }

    /**
     * Mencari aset untuk AJAX search (LOGIKA ASET BARU).
     */
    public function cari(Request $request)
    {
        $keyword = $request->input('keyword');

        // Cari berdasarkan: nama, kode, register, atau merk
        $barang = Barang::with(['jenis', 'sumber', 'kondisi', 'lokasi', 'statusAset'])
            ->where(function($query) use ($keyword) {
                $query->where('nama_barang', 'LIKE', "%$keyword%")
                      ->orWhere('kode_barang', 'LIKE', "%$keyword%")
                      ->orWhere('register', 'LIKE', "%$keyword%")
                      ->orWhere('merk_type', 'LIKE', "%$keyword%");
            })
            ->orderBy('nama_barang', 'asc')
            ->get(); // Ambil sebagai Collection (bukan Paginator)

        // Kembalikan view partial (body tabel)
        // Pastikan nama file partial Anda benar
        return view('barang._tabel_aset', ['barang' => $barang]);
    }

    /**
     * Menampilkan form upload CSV.
     */
    public function importCsvForm()
    {
        return view('barang.import', [
            'judul' => 'Import Aset dari CSV'
        ]);
    }

    /**
     * Memproses file CSV yang di-upload (versi ASINKRON).
     */
    public function importCsv(Request $request)
    {
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt']);

        // 1. Simpan file ke storage (misal: storage/app/imports)
        $path = $request->file('csv_file')->store('imports');

        // 2. Dapatkan user yang sedang login
        $user = Auth::user();

        // 3. "Lempar" tugas ini ke background job
        // Pastikan Anda sudah membuat file job baru (ProcessAsetImport)
        // Jika file job Anda namanya masih ProcessCsvImport, gunakan itu
        ProcessAsetImport::dispatch($path, $user); 
        // ATAU: ProcessCsvImport::dispatch($path, $user);

        // 4. Langsung kembalikan ke user tanpa menunggu
        return redirect()->route('barang.index')
                        ->with('success', 'Import Berhasil. Data sedang diproses di background dan akan segera muncul.');
    }

    /**
     * Mengekspor data aset ke file CSV (LOGIKA ASET BARU).
     */
    public function exportCsv()
    {
        $fileName = 'data_aset_inventaris_' . date('Y-m-d') . '.csv';

        // Ambil semua data aset dengan relasinya
        $data_barang = Barang::with(['jenis', 'sumber', 'kondisi', 'lokasi', 'statusAset'])->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function() use ($data_barang) {
            $output = fopen('php://output', 'w');
            
            // Tulis Header Kolom
            fputcsv($output, [
                'Nama Barang',
                'Kode Barang',
                'Register',
                'Merk/Type',
                'Tahun Pembelian',
                'Harga',
                'Jenis',
                'Sumber Perolehan',
                'Kondisi',
                'Lokasi',
                'Status Aset',
                'Keterangan'
            ]);

            // Tulis Data Aset
            foreach ($data_barang as $barang) {
                fputcsv($output, [
                    $barang->nama_barang,
                    $barang->kode_barang,
                    $barang->register,
                    $barang->merk_type,
                    $barang->tahun_pembelian,
                    $barang->harga,
                    $barang->jenis->nama_jenis ?? 'N/A',
                    $barang->sumber->nama_sumber ?? 'N/A',
                    $barang->kondisi->nama_kondisi ?? 'N/A',
                    $barang->lokasi->nama_lokasi ?? 'N/A',
                    $barang->statusAset->nama_status ?? 'N/A',
                    $barang->keterangan
                ]);
            }
            fclose($output);
        };

        // Kembalikan response sebagai file download
        return new StreamedResponse($callback, 200, $headers);
    }
}