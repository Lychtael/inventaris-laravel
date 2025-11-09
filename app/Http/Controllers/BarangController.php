<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\JenisBarang;
use App\Models\SumberBarang;
use App\Models\Kondisi;
use App\Models\Dinas; // <-- BARU
use App\Models\Bidang; // <-- BARU
use App\Models\StatusAset;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Jobs\ProcessAsetImport; 
use Illuminate\Support\Facades\Storage;
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
        $query = Barang::with(['dinas', 'bidang', 'jenis', 'sumber', 'kondisi', 'statusAset']);

        // Filter Multi-Dinas
        if ($request->filled('id_dinas')) {
            $query->where('id_dinas', $request->id_dinas);
        }
        if ($request->filled('id_bidang')) {
            $query->where('id_bidang', $request->id_bidang);
        }
        
        $barang = $query->orderBy('id', 'desc')->paginate($limit)->withQueryString();
        
        // Ambil semua data master untuk filter
        $dinas_list = Dinas::orderBy('nama_dinas', 'asc')->get();
        $bidang_list = Bidang::orderBy('nama_bidang', 'asc')->get();
        // (Kita bisa buat $bidang_list dinamis nanti)

        return view('barang.index', [
            'barang' => $barang,
            'dinas_list' => $dinas_list,
            'bidang_list' => $bidang_list,
            'current_filters' => $request->only(['id_dinas', 'id_bidang']),
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
            'dinas' => Dinas::orderBy('nama_dinas', 'asc')->get(),
            'bidang' => Bidang::orderBy('nama_bidang', 'asc')->get(),
            'jenis' => JenisBarang::orderBy('nama_jenis', 'asc')->get(),
            'sumber' => SumberBarang::orderBy('nama_sumber', 'asc')->get(),
            'kondisi' => Kondisi::orderBy('nama_kondisi', 'asc')->get(),
            'status_aset' => StatusAset::orderBy('nama_status', 'asc')->get(),
            'judul' => 'Tambah Aset Baru'
        ];
        
        return view('barang.create', $data);
    }

    /**
     * Menyimpan ASET baru ke database (logika FINAL).
     */
    public function store(Request $request)
    {
        // Validasi baru (LENGKAP)
        $validated = $request->validate([
            // Grup 1: Relasi (Dropdown)
            'id_dinas' => 'required|exists:dinas,id',
            'id_bidang' => 'required|exists:bidang,id',
            'id_jenis' => 'required|exists:jenis_barang,id',
            'id_sumber' => 'required|exists:sumber_barang,id',
            'id_kondisi' => 'required|exists:kondisi,id',
            'id_status_aset' => 'required|exists:status_aset,id',
            
            // Grup 2: Data Teks (Manual)
            'nama_barang' => 'required|string|max:255',
            'kode_barang' => 'nullable|string|max:100',
            'register' => 'nullable|string|max:100',
            'merk_type' => 'nullable|string|max:100',
            'nomor_spek' => 'nullable|string|max:100',
            'bahan' => 'nullable|string|max:100',
            'ukuran' => 'nullable|string|max:100',
            'satuan' => 'nullable|string|max:50',
            'lokasi' => 'nullable|string|max:255',
            'pengguna' => 'nullable|string|max:150',
            'keterangan' => 'nullable|string',

            // Grup 3: Data Angka (Manual)
            'tahun_pembelian' => 'nullable|integer|min:1900|max:'.(date('Y') + 1),
            'harga' => 'nullable|numeric|min:0',
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
        $barang->load(['dinas', 'bidang', 'jenis', 'sumber', 'kondisi', 'statusAset']);
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
            'dinas' => Dinas::orderBy('nama_dinas', 'asc')->get(),
            'bidang' => Bidang::orderBy('nama_bidang', 'asc')->get(),
            'jenis' => JenisBarang::orderBy('nama_jenis', 'asc')->get(),
            'sumber' => SumberBarang::orderBy('nama_sumber', 'asc')->get(),
            'kondisi' => Kondisi::orderBy('nama_kondisi', 'asc')->get(),
            'status_aset' => StatusAset::orderBy('nama_status', 'asc')->get(),
            'judul' => 'Edit Aset'
        ];
        
        return view('barang.edit', $data);
    }

    /**
     * Memperbarui data ASET di database (logika FINAL).
     */
    public function update(Request $request, Barang $barang)
    {
        // Validasi baru (LENGKAP)
        $validated = $request->validate([
            // Grup 1: Relasi (Dropdown)
            'id_dinas' => 'required|exists:dinas,id',
            'id_bidang' => 'required|exists:bidang,id',
            'id_jenis' => 'required|exists:jenis_barang,id',
            'id_sumber' => 'required|exists:sumber_barang,id',
            'id_kondisi' => 'required|exists:kondisi,id',
            'id_status_aset' => 'required|exists:status_aset,id',
            
            // Grup 2: Data Teks (Manual)
            'nama_barang' => 'required|string|max:255',
            'kode_barang' => 'nullable|string|max:100',
            'register' => 'nullable|string|max:100',
            'merk_type' => 'nullable|string|max:100',
            'nomor_spek' => 'nullable|string|max:100',
            'bahan' => 'nullable|string|max:100',
            'ukuran' => 'nullable|string|max:100',
            'satuan' => 'nullable|string|max:50',
            'lokasi' => 'nullable|string|max:255',
            'pengguna' => 'nullable|string|max:150',
            'keterangan' => 'nullable|string',

            // Grup 3: Data Angka (Manual)
            'tahun_pembelian' => 'nullable|integer|min:1900|max:'.(date('Y') + 1),
            'harga' => 'nullable|numeric|min:0',
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
            // Cek dulu apakah barang sedang dipinjam
            if ($barang->peminjaman()->where('status_pinjam', 'Dipinjam')->count() > 0) {
                 return redirect()->route('barang.index')
                             ->with('error', 'Gagal: Aset ini sedang dalam status dipinjam.');
            }
            
            $barang->delete();
            
            LogAktivitas::create([
                'id_pengguna' => Auth::id(),
                'aksi' => 'HAPUS',
                'tabel' => 'barang',
                'keterangan' => 'Menghapus aset: ' . $namaBarang
            ]);

            return redirect()->route('barang.index')
                             ->with('success', 'Data Aset berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('barang.index')
                             ->with('error', 'Data Aset gagal dihapus. Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Menampilkan form upload CSV (LOGIKA BARU DENGAN FILTER DINAS/BIDANG).
     */
    public function importCsvForm()
    {
        return view('barang.import', [
            'dinas' => Dinas::orderBy('nama_dinas', 'asc')->get(),
            'bidang' => Bidang::orderBy('nama_bidang', 'asc')->get(),
            'judul' => 'Import Aset dari CSV'
        ]);
    }
    /**
     * Memproses file CSV yang di-upload (LOGIKA BARU DENGAN DINAS/BIDANG).
     */
    public function importCsv(Request $request)
    {
        // 1. Validasi file DAN dropdown
        $validated = $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt,xlsx,xls',
            'id_dinas' => 'required|exists:dinas,id',
            'id_bidang' => 'required|exists:bidang,id',
        ]);

        // 2. Simpan file ke storage
        $path = $request->file('csv_file')->store('imports');

        // 3. Dapatkan user yang sedang login
        $user = Auth::user();

        // 4. "Lempar" tugas ini ke background job DENGAN data Dinas/Bidang
        ProcessAsetImport::dispatch(
            $path, 
            $user,
            $validated['id_dinas'],
            $validated['id_bidang']
        ); 

        // 5. Langsung kembalikan ke user tanpa menunggu
        return redirect()->route('barang.index')
                        ->with('success', 'Import Berhasil. Data sedang diproses di background dan akan segera muncul.');
    }
    /**
     * Mengekspor data aset ke file CSV (LOGIKA ASET BARU DENGAN FILTER).
     */
    public function exportCsv(Request $request) // Tambahkan Request $request
    {
        $fileName = 'data_aset_inventaris_' . date('Y-m-d') . '.csv';

        // Ambil query dasar, SAMA PERSIS DENGAN METHOD INDEX()
        $query = Barang::with(['dinas', 'bidang', 'jenis', 'sumber', 'kondisi', 'statusAset']);

        // Terapkan filter Dinas/Bidang (SAMA PERSIS DENGAN METHOD INDEX())
        if ($request->filled('id_dinas')) {
            $query->where('id_dinas', $request->id_dinas);
        }
        if ($request->filled('id_bidang')) {
            $query->where('id_bidang', $request->id_bidang);
        }
        
        $data_barang = $query->orderBy('id', 'asc')->get(); // Ambil semua data yang terfilter

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function() use ($data_barang) {
            $output = fopen('php://output', 'w');
            
            // Tulis Header Kolom (SESUAI TABEL LENGKAP KITA)
            fputcsv($output, [
                'ID',
                'Nama Barang / Jenis',
                'Dinas Pemilik',
                'Bidang Pemilik',
                'Kode Barang',
                'Register',
                'Merk / Type',
                'No. Spek (Sertifikat/Pabrik/Dll)',
                'Bahan',
                'Tahun Pembelian',
                'Ukuran (P,S,D)',
                'Satuan',
                'Harga (Rp)',
                'Lokasi (Teks)',
                'Kondisi',
                'Status Aset',
                'Pengguna',
                'Keterangan',
                'Tgl Input',
            ]);

            // Tulis Data Aset
            foreach ($data_barang as $barang) {
                fputcsv($output, [
                    $barang->id,
                    $barang->nama_barang,
                    $barang->dinas->nama_dinas ?? 'N/A',
                    $barang->bidang->nama_bidang ?? 'N/A',
                    $barang->kode_barang,
                    $barang->register,
                    $barang->merk_type,
                    $barang->nomor_spek,
                    $barang->bahan,
                    $barang->tahun_pembelian,
                    $barang->ukuran,
                    $barang->satuan,
                    $barang->harga,
                    $barang->lokasi,
                    $barang->kondisi->nama_kondisi ?? 'N/A',
                    $barang->statusAset->nama_status ?? 'N/A',
                    $barang->pengguna,
                    $barang->keterangan,
                    $barang->created_at->format('Y-m-d'),
                ]);
            }
            fclose($output);
        };

        // Kembalikan response sebagai file download
        return new StreamedResponse($callback, 200, $headers);
    }
}