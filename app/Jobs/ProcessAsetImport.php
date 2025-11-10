<?php

namespace App\Jobs;

// (Tambahkan semua 'use' statement yang diperlukan di sini)
use App\Models\Barang;
use App\Models\JenisBarang;
use App\Models\SumberBarang;
use App\Models\Kondisi;
// use App\Models\Lokasi; // <-- HAPUS BARIS INI
use App\Models\StatusAset;
use App\Models\LogAktivitas;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel; // <-- Pakai Maatwebsite/Excel

class ProcessAsetImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $user;
    protected $id_dinas;
    protected $id_bidang;

    /**
     * Create a new job instance. (Konstruktor kita sudah benar)
     */
    public function __construct(string $filePath, User $user, int $id_dinas, int $id_bidang)
    {
        $this->filePath = $filePath;
        $this->user = $user;
        $this->id_dinas = $id_dinas;
        $this->id_bidang = $id_bidang;
    }

    /**
     * Execute the job. (VERSI FINAL - TANPA TABEL LOKASI)
     */
    public function handle(): void
    {
        $fullPath = Storage::path($this->filePath);

        DB::beginTransaction();
        try {
            // === LOGIKA BARU: BACA DENGAN EXCEL LIBRARY ===
            $rows = Excel::toArray(new \StdClass, $fullPath)[0]; // Ambil sheet pertama [0]
            
            $totalAsetDibuat = 0;
            $barisCsvKe = 0; // Index array dimulai dari 0

            // Baca semua baris dari file Excel
            foreach ($rows as $index => $row) {
                $barisCsvKe = $index + 1;

                // Lewati 15 baris header sampah (index 0 s/d 14)
                if ($index < 15) {
                    continue; 
                }

                // Lewati baris kosong
                if (count($row) <= 1 && empty($row[0])) {
                    continue;
                }
                
                // === LOGIKA BACA DENGAN INDEX (SESUAI CSV) ===
                $kode_barang = trim($row[1] ?? null);
                $register = trim($row[2] ?? null);
                $nama_barang_dan_jenis = trim($row[3] ?? null); 
                $merk_type = trim($row[4] ?? null);
                $nomor_spek = trim($row[5] ?? null);
                $bahan = trim($row[6] ?? null);
                $asal_perolehan = trim($row[7] ?? null);
                $tahun_pembelian_str = trim($row[8] ?? null);
                if (is_numeric($tahun_pembelian_str) && $tahun_pembelian_str > 40000) {
                     $tahun_pembelian = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tahun_pembelian_str)->format('Y');
                } else {
                     $tahun_pembelian = !empty($tahun_pembelian_str) ? (int)$tahun_pembelian_str : null;
                }
                $ukuran = trim($row[9] ?? null); 
                $satuan = trim($row[10] ?? null);
                $keadaan_barang = trim($row[11] ?? null); // B/KB/RB
                $jumlah_str = trim($row[12] ?? 1); 
                $jumlah = !empty($jumlah_str) ? (int)$jumlah_str : 1; 
                $harga_str = trim($row[13] ?? 0);
                $harga_cleaned = str_replace(['Rp', ' ', '.'], '', $harga_str);
                $harga_cleaned = str_replace(',', '.', $harga_cleaned);
                $harga = !empty($harga_cleaned) ? (float)$harga_cleaned : 0;
                $keterangan = trim($row[14] ?? null);
                $lokasi_csv = trim($row[15] ?? null); // (Simpan sebagai Teks)
                $pengguna = trim($row[17] ?? null);
                $status_csv = trim($row[18] ?? 'Tersedia'); 
                // ======================================
                
                // Validasi data wajib
                if (empty($nama_barang_dan_jenis) || empty($asal_perolehan) || empty($keadaan_barang)) {
                    Log::warning("Import Excel: Lewati baris {$barisCsvKe}, data wajib (nama, asal, kondisi) kosong.");
                    continue; 
                }

                // Cari or create ID master (KECUALI LOKASI)
                $jenis_id = JenisBarang::firstOrCreate(['nama_jenis' => $nama_barang_dan_jenis])->id; 
                $sumber_id = SumberBarang::firstOrCreate(['nama_sumber' => $asal_perolehan])->id;
                $kondisi_id = Kondisi::firstOrCreate(['nama_kondisi' => $keadaan_barang])->id;
                $status_id = StatusAset::firstOrCreate(['nama_status' => $status_csv])->id;

                // === LOGIKA LOKASI (YANG BENAR) ===
                // Kita TIDAK pakai $lokasi_id
                // Kita simpan $lokasi_csv (teks) apa adanya
                // ================================

                // LOGIKA UTAMA: Loop berdasarkan 'JUMLAH'
                for ($i = 0; $i < $jumlah; $i++) {
                    Barang::create([
                        // Data dari Dropdown
                        'id_dinas' => $this->id_dinas,
                        'id_bidang' => $this->id_bidang,
                        
                        // Data dari Tabel Master
                        'id_jenis' => $jenis_id,
                        'id_sumber' => $sumber_id,
                        'id_kondisi' => $kondisi_id,
                        'id_status_aset' => $status_id,

                        // Data Teks Manual dari CSV
                        'nama_barang' => $nama_barang_dan_jenis,
                        'kode_barang' => $kode_barang,
                        'register' => str_pad($register + $i, 6, '0', STR_PAD_LEFT), 
                        'merk_type' => $merk_type,
                        'nomor_spek' => $nomor_spek,
                        'bahan' => $bahan,
                        'ukuran' => $ukuran,
                        'satuan' => $satuan,
                        'lokasi' => $lokasi_csv, // <-- SIMPAN SEBAGAI TEKS
                        'pengguna' => $pengguna,
                        'keterangan' => $keterangan,
                        
                        // Data Angka Manual dari CSV
                        'tahun_pembelian' => $tahun_pembelian,
                        'harga' => ($jumlah > 0) ? ($harga / $jumlah) : 0, 
                    ]);
                    $totalAsetDibuat++;
                }
            } // end foreach

            LogAktivitas::create([
                'id_pengguna' => $this->user->id,
                'aksi' => 'IMPORT',
                'tabel' => 'barang',
                'keterangan' => "Berhasil mengimpor {$totalAsetDibuat} unit aset dari file Excel/CSV."
            ]);
            
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import Excel Gagal Total: ' . $e->getMessage(), ['user_id' => $this->user->id, 'file' => $this->filePath, 'line' => $e->getLine()]);
            LogAktivitas::create([
                'id_pengguna' => $this->user->id,
                'aksi' => 'GAGAL IMPORT',
                'tabel' => 'barang',
                'keterangan' => 'Gagal total impor Excel/CSV: ' . $e->getMessage() . ' di baris ' . $e->getLine()
            ]);
        } finally {
            // Hapus file
            Storage::delete($this->filePath); 
        }
    }
}