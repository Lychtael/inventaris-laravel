<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\JenisBarang;
use App\Models\SumberBarang;
// use App\Models\LogAktivitas; // (Opsional: jika Anda membuat model Log)
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse; // Diperlukan untuk Export CSV

class BarangController extends Controller
{
    /**
     * Menampilkan daftar barang dengan filter dan pagination.
     * Diterjemahkan dari: BarangController.php -> index()
     * dan Barang_model.php -> getAllBarang()
     */
    public function index(Request $request)
    {
        $limit = $request->get('limit', 25); // Ambil dari request atau default 25
        
        // Mulai query dengan eager loading (mengambil relasi)
        $query = Barang::with(['jenis', 'sumber']);

        // Terapkan filter jika ada
        if ($request->filled('jenis')) {
            $query->where('id_jenis', $request->jenis);
        }
        if ($request->filled('sumber')) {
            $query->where('id_sumber', $request->sumber);
        }
        
        // Ambil data dengan pagination
        $barang = $query->orderBy('id', 'desc')->paginate($limit)->withQueryString();

        // Data untuk filter dropdowns
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
     * Diterjemahkan dari: BarangController.php -> tambah()
     */
    public function create()
    {
        $jenis = JenisBarang::orderBy('nama_jenis', 'asc')->get();
        $sumber = SumberBarang::orderBy('nama_sumber', 'asc')->get();
        
        return view('barang.create', [
            'jenis' => $jenis,
            'sumber' => $sumber,
            'judul' => 'Tambah Barang'
        ]);
    }

    /**
     * Menyimpan data barang baru ke database.
     * Diterjemahkan dari: BarangController.php -> store()
     */
    public function store(Request $request)
    {
        // Validasi Laravel (menggantikan pengecekan 'empty' manual)
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:100',
            'jumlah' => 'required|integer|min:1',
            'satuan' => 'required|string|max:20',
            'id_jenis' => 'required|exists:jenis_barang,id',
            'id_sumber' => 'required|exists:sumber_barang,id',
            'keterangan' => 'nullable|string',
        ]);

        // Buat barang
        $barang = Barang::create($validated);

        // TODO: Implementasi Log Model (menggantikan Log_model.php)
        // LogAktivitas::create([
        //     'id_pengguna' => Auth::id(),
        //     'aksi' => 'TAMBAH',
        //     'tabel' => 'barang',
        //     'keterangan' => 'Menambah barang baru: ' . $barang->nama_barang
        // ]);

        // Redirect dengan flash message (menggantikan Flasher.php)
        return redirect()->route('barang.index')
                         ->with('success', 'Data Barang berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail satu barang.
     * Diterjemahkan dari: BarangController.php -> detail()
     */
    public function show(Barang $barang)
    {
        // $barang sudah otomatis di-fetch oleh Route Model Binding
        // Kita hanya perlu load relasinya untuk ditampilkan
        $barang->load(['jenis', 'sumber']);
        
        return view('barang.detail', [
            'barang' => $barang,
            'judul' => 'Detail Barang'
        ]);
    }

    /**
     * Menampilkan form untuk mengedit barang.
     * Diterjemahkan dari: BarangController.php -> edit()
     */
    public function edit(Barang $barang)
    {
        $jenis = JenisBarang::orderBy('nama_jenis', 'asc')->get();
        $sumber = SumberBarang::orderBy('nama_sumber', 'asc')->get();
        
        return view('barang.edit', [
            'barang' => $barang,
            'jenis' => $jenis,
            'sumber' => $sumber,
            'judul' => 'Edit Barang'
        ]);
    }

    /**
     * Memperbarui data barang di database.
     * Diterjemahkan dari: BarangController.php -> update()
     */
    public function update(Request $request, Barang $barang)
    {
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:100',
            'jumlah' => 'required|integer|min:0', // Boleh 0 saat update
            'satuan' => 'required|string|max:20',
            'id_jenis' => 'required|exists:jenis_barang,id',
            'id_sumber' => 'required|exists:sumber_barang,id',
            'keterangan' => 'nullable|string',
        ]);

        $barang->update($validated);

        // TODO: Implementasi Log Model
        // LogAktivitas::create([ ... 'aksi' => 'UBAH' ... ]);

        return redirect()->route('barang.index')
                         ->with('success', 'Data Barang berhasil diubah.');
    }

    /**
     * Menghapus data barang dari database.
     * Diterjemahkan dari: BarangController.php -> hapus()
     */
    public function destroy(Barang $barang)
    {
        $namaBarang = $barang->nama_barang; // Simpan nama untuk log
        
        try {
            $barang->delete();
            
            // TODO: Implementasi Log Model
            // LogAktivitas::create([ ... 'aksi' => 'HAPUS' ... ]);

            return redirect()->route('barang.index')
                             ->with('success', 'Data Barang berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Menangani jika barang tidak bisa dihapus (misal, karena foreign key di peminjaman)
            return redirect()->route('barang.index')
                             ->with('error', 'Data Barang gagal dihapus. Mungkin sedang dipinjam atau terkait data lain.');
        }
    }

    /**
     * Mencari barang untuk AJAX search.
     * Diterjemahkan dari: BarangController.php -> cari()
     * dan Barang_model.php -> cariDataBarang()
     */
    public function cari(Request $request)
    {
        $keyword = $request->input('keyword');
        
        $barang = Barang::with(['jenis', 'sumber'])
                ->where('nama_barang', 'LIKE', "%$keyword%")
                ->orderBy('id', 'desc')
                ->get();
        
        // Kembalikan view partial, bukan HTML mentah
        return view('barang._search_results', ['barang' => $barang]);
    }

    /**
     * Menampilkan form upload CSV.
     * Diterjemahkan dari: BarangController.php -> importCsvForm()
     */
    public function importCsvForm()
    {
        return view('barang.import', [
            'judul' => 'Import Barang dari CSV'
        ]);
    }

    /**
     * Memproses file CSV yang di-upload.
     * Diterjemahkan dari: BarangController.php -> importCsv()
     * dan Barang_model.php -> importFromCsv()
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('csv_file');
        
        // Validasi header yang fleksibel
        $headerMap = [
            'nama barang' => 'nama_barang',
            'nama_barang' => 'nama_barang',
            'nama'        => 'nama_barang',
            'qty'         => 'qty',
            'kuantitas'   => 'qty',
            'jumlah'      => 'qty',
            'satuan'      => 'satuan',
            'jenis'       => 'jenis',
            'kategori'    => 'jenis',
            'sumber'      => 'sumber',
            'asal'        => 'sumber',
            'keterangan'  => 'keterangan',
            'ket'         => 'keterangan'
        ];

        $handle = fopen($file->getPathname(), "r");
        if ($handle === false) {
            return redirect()->back()->with('error', 'Tidak bisa membaca file CSV.');
        }

        // Ambil header dan normalisasi
        $headers_raw = fgetcsv($handle, 1000, ",");
        if (!$headers_raw) {
             fclose($handle);
            return redirect()->back()->with('error', 'File CSV kosong atau header tidak valid.');
        }
        
        $headers = [];
        foreach ($headers_raw as $h) {
            $key = strtolower(trim($h));
            $headers[] = $headerMap[$key] ?? $key;
        }

        $errors = [];
        $rowNumber = 1;

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $rowNumber++;
                if (count($row) != count($headers)) {
                    $errors[] = "Baris #{$rowNumber}: Jumlah kolom tidak sesuai.";
                    continue;
                }
                $data = array_combine($headers, $row);

                // Normalisasi dan validasi data
                $nama_barang = $data['nama_barang'] ?? null;
                $jumlah = $data['qty'] ?? 0;
                $satuan = $data['satuan'] ?? null;
                $nama_jenis = $data['jenis'] ?? null;
                $nama_sumber = $data['sumber'] ?? null;
                $keterangan = $data['keterangan'] ?? null;
                
                if (empty($nama_barang) || empty($satuan) || empty($nama_jenis) || empty($nama_sumber)) {
                     $errors[] = "Baris #{$rowNumber}: Data (nama_barang, satuan, jenis, sumber) tidak boleh kosong.";
                     continue;
                }

                // Gunakan firstOrCreate (pengganti get_or_create_id dari model lama)
                $jenis = JenisBarang::firstOrCreate(['nama_jenis' => trim($nama_jenis)]);
                $sumber = SumberBarang::firstOrCreate(['nama_sumber' => trim($nama_sumber)]);

                // Simpan barang
                Barang::create([
                    'nama_barang' => $nama_barang,
                    'jumlah' => (int)$jumlah,
                    'satuan' => $satuan,
                    'id_jenis' => $jenis->id,
                    'id_sumber' => $sumber->id,
                    'keterangan' => $keterangan,
                ]);
            }

            if (!empty($errors)) {
                DB::rollBack();
                // Kirim error ke session (menggantikan $_SESSION['csv_import_errors'])
                return redirect()->back()->with('csv_import_errors', $errors);
            }

            DB::commit();
            
            // TODO: Implementasi Log Model
            // LogAktivitas::create([ ... 'aksi' => 'IMPORT' ... ]);
            
            return redirect()->route('barang.index')->with('success', 'Import Berhasil. Semua data dari CSV telah ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi error saat import: ' . $e->getMessage());
        } finally {
            fclose($handle);
        }
    }

    /**
     * Mengekspor data barang ke file CSV.
     * Diterjemahkan dari: BarangController.php -> exportCsv()
     * dan Barang_model.php -> getAllBarangWithDetails()
     */
    public function exportCsv()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="data_barang_' . date('Y-m-d') . '.csv"',
        ];

        // Ambil data (sesuai getAllBarangWithDetails)
        $data_barang = Barang::with(['jenis', 'sumber'])->get();

        $callback = function() use ($data_barang) {
            $output = fopen('php://output', 'w');
            
            // Tulis header kolom
            fputcsv($output, ['Nama Barang', 'Kuantitas', 'Satuan', 'Jenis', 'Sumber', 'Keterangan']);

            // Tulis data barang
            foreach ($data_barang as $barang) {
                fputcsv($output, [
                    $barang->nama_barang,
                    $barang->jumlah,
                    $barang->satuan,
                    $barang->jenis->nama_jenis ?? '-', // Akses relasi
                    $barang->sumber->nama_sumber ?? '-', // Akses relasi
                    $barang->keterangan
                ]);
            }
            fclose($output);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}