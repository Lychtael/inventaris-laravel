<?php

namespace App\Jobs;

use App\Models\Barang;
use App\Models\JenisBarang;
use App\Models\SumberBarang;
use App\Models\Kondisi;
use App\Models\Lokasi;
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

class ProcessAsetImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $user;

    /**
     * Create a new job instance.
     */
    public function __construct(string $filePath, User $user)
    {
        $this->filePath = $filePath;
        $this->user = $user;
    }

    /**
     * Helper function untuk mendeteksi delimiter CSV (koma vs semicolon)
     */
    private function detectDelimiter($fileHandle)
    {
        // Baca 2 baris pertama untuk menebak
        $line1 = fgets($fileHandle);
        $line2 = fgets($fileHandle);
        // Kembali ke awal file
        rewind($fileHandle); 

        $commaCount = substr_count($line1, ',') + substr_count($line2, ',');
        $semicolonCount = substr_count($line1, ';') + substr_count($line2, ';');

        // Jika semicolon lebih banyak, pakai itu. Defaultnya koma.
        return $semicolonCount > $commaCount ? ';' : ',';
    }

    /**
     * Execute the job. (VERSI FINAL - BACA DENGAN INDEX KOLOM TETAP)
     */
    public function handle(): void
    {
        $fullPath = Storage::path($this->filePath);
        $fileHandle = fopen($fullPath, "r");

        if ($fileHandle === false) {
            Log::error('Import CSV Gagal: File tidak bisa dibuka.', ['path' => $fullPath]);
            Storage::delete($this->filePath);
            return;
        }

        // === LANGKAH BARU: DETEKSI DELIMITER ===
        $delimiter = $this->detectDelimiter($fileHandle);
        // ======================================

        DB::beginTransaction();
        try {
            // Lewati 15 baris header sampah (termasuk row 13, 14, 15)
            // Sesuai analisis CSV, data asli dimulai di baris 16
            for ($i = 0; $i < 15; $i++) {
                fgetcsv($fileHandle, 2000, $delimiter); 
            }
            
            $totalAsetDibuat = 0;
            $barisCsvKe = 15; // Kita mulai baca data dari baris 16

            // Baca sisa file (datanya)
            while (($row = fgetcsv($fileHandle, 2000, $delimiter)) !== FALSE) {
                $barisCsvKe++;

                // Lewati baris kosong
                if (count($row) <= 1 && empty($row[0])) {
                    continue;
                }
                
                // === LOGIKA BARU: BACA DENGAN INDEX (SESUAI ANALISIS KAMU) ===
                //
                // 0 = Urut
                $kode_barang = trim($row[1] ?? null);
                $register = trim($row[2] ?? null);
                $nama_barang_dan_jenis = trim($row[3] ?? null); // Nama / Jenis Barang
                $merk_type = trim($row[4] ?? null);
                // 5 = No. Sertifikat, 6 = Bahan (kosong di CSV)
                $asal_perolehan = trim($row[7] ?? null);
                $tahun_pembelian_str = trim($row[8] ?? null);
                $tahun_pembelian = !empty($tahun_pembelian_str) ? (int)$tahun_pembelian_str : null;
                // 9 = Ukuran, 10 = Satuan (kosong di CSV)
                $keadaan_barang = trim($row[11] ?? null); // B/KB/RB
                $jumlah_str = trim($row[12] ?? 1); // "JUMLAH - Barang"
                $jumlah = !empty($jumlah_str) ? (int)$jumlah_str : 1; // Default 1 jika kolom jumlah kosong
                $harga_str = trim($row[13] ?? 0); // "JUMLAH - Harga"
                $harga = !empty($harga_str) ? (float)str_replace(['.', ','], '', $harga_str) : 0; // Hapus titik/koma dari harga
                $keterangan = trim($row[14] ?? null);
                $lokasi_csv = trim($row[15] ?? null);
                // 16 = Penanggung Jawab, 17 = Pengguna (kosong di CSV)
                $status_csv = trim($row[18] ?? 'Tersedia'); // Default "Tersedia"
                // ======================================
                
                // Validasi data wajib (sesuai CSV)
                if (empty($nama_barang_dan_jenis) || empty($asal_perolehan) || empty($keadaan_barang)) {
                    Log::warning("Import CSV: Lewati baris {$barisCsvKe}, data wajib (nama, asal, kondisi) kosong.");
                    continue; // Lewati baris
                }

                // Cari or create ID master
                $jenis_id = JenisBarang::firstOrCreate(['nama_jenis' => $nama_barang_dan_jenis])->id; 
                $sumber_id = SumberBarang::firstOrCreate(['nama_sumber' => $asal_perolehan])->id;
                $kondisi_id = Kondisi::firstOrCreate(['nama_kondisi' => $keadaan_barang])->id;
                $status_id = StatusAset::firstOrCreate(['nama_status' => $status_csv])->id;

                $lokasi_id = null;
                if (!empty($lokasi_csv)) {
                    $lokasi_id = Lokasi::firstOrCreate(['nama_lokasi' => $lokasi_csv])->id;
                }

                // LOGIKA UTAMA: Loop berdasarkan 'JUMLAH'
                for ($i = 0; $i < $jumlah; $i++) {
                    Barang::create([
                        'nama_barang' => $nama_barang_dan_jenis,
                        'kode_barang' => $kode_barang,
                        'register' => str_pad($register + $i, 6, '0', STR_PAD_LEFT), // Buat register 000001, 000002, dst.
                        'merk_type' => $merk_type,
                        'tahun_pembelian' => $tahun_pembelian,
                        'harga' => $harga / $jumlah, // Bagi harga total dengan jumlah unit (jika harga diisi per grup)
                        'keterangan' => $keterangan,
                        'id_jenis' => $jenis_id,
                        'id_sumber' => $sumber_id,
                        'id_kondisi' => $kondisi_id,
                        'id_lokasi' => $lokasi_id,
                        'id_status_aset' => $status_id,
                    ]);
                    $totalAsetDibuat++;
                }
            }

            LogAktivitas::create([
                'id_pengguna' => $this->user->id,
                'aksi' => 'IMPORT',
                'tabel' => 'barang',
                'keterangan' => "Berhasil mengimpor {$totalAsetDibuat} unit aset dari file CSV."
            ]);
            
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import CSV Gagal Total: ' . $e->getMessage(), ['user_id' => $this->user->id, 'file' => $this->filePath, 'line' => $e->getLine()]);
            LogAktivitas::create([
                'id_pengguna' => $this->user->id,
                'aksi' => 'GAGAL IMPORT',
                'tabel' => 'barang',
                'keterangan' => 'Gagal total impor CSV: ' . $e->getMessage()
            ]);
        } finally {
            fclose($fileHandle);
            Storage::delete($this->filePath); 
        }
    }
}