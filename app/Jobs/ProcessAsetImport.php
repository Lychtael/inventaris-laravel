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
use Illuminate\Support\Facades\Log; // Untuk error logging

class ProcessAsetImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $user;
    protected $headerMap;

    /**
     * Create a new job instance.
     */
    public function __construct(string $filePath, User $user)
    {
        $this->filePath = $filePath;
        $this->user = $user;

        // KAMUS MAPPING (Header CSV -> key)
        // Ini adalah bagian paling penting
        $this->headerMap = [
            'kode barang' => 'kode_barang',
            'register' => 'register',
            'nama / jenis barang' => 'nama_barang',
            'merk/ type' => 'merk_type',
            'merk/type' => 'merk_type',
            'asal/cara perolehan barang' => 'asal_perolehan',
            'tahun pem- belian' => 'tahun_pembelian', // CSV punya newline
            'tahun pembelian' => 'tahun_pembelian',
            'keadaan barang (b/kb/rb)' => 'keadaan_barang',
            'jumlah - barang' => 'jumlah_barang',
            'jumlah' => 'jumlah_barang',
            'harga' => 'harga',
            'keterangan' => 'keterangan',
            'lokasi' => 'lokasi',
            'status' => 'status',
        ];
    }

    /**
     * Execute the job.
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

        DB::beginTransaction();
        try {
            // Lewati 12 baris header sampah di CSV
            for ($i = 0; $i < 12; $i++) {
                fgetcsv($fileHandle); 
            }
            
            // Baris ke-13 adalah header yang kita inginkan
            $headers_raw = fgetcsv($fileHandle, 2000, ",");
            if (!$headers_raw) {
                throw new \Exception('Header CSV tidak valid atau file kosong.');
            }

            // Baris ke-14 (1,2,3,4...) & ke-15 (spesifikasi barang) kita lewati
            fgetcsv($fileHandle);
            fgetcsv($fileHandle);

            // Petakan header
            $headers = [];
            foreach ($headers_raw as $h) {
                $key = strtolower(trim(str_replace("\n", " ", $h))); // Bersihkan header
                $headers[] = $this->headerMap[$key] ?? $key;
            }

            $totalAsetDibuat = 0;
            $barisCsvKe = 15; // Kita mulai baca data dari baris ~16

            // Baca sisa file (datanya)
            while (($row = fgetcsv($fileHandle, 2000, ",")) !== FALSE) {
                $barisCsvKe++;
                if (count($row) != count($headers)) {
                    Log::warning("Import CSV: Lewati baris {$barisCsvKe}, jumlah kolom tidak cocok.");
                    continue;
                }
                
                $data = array_combine($headers, $row);

                // Ambil data dari CSV
                $nama_barang = $data['nama_barang'] ?? null;
                if (empty($nama_barang)) continue; // Lewati baris kosong

                $jumlah = (int)($data['jumlah_barang'] ?? 1);
                $register_start = (int)($data['register'] ?? 0);
                
                // Cari atau buat ID dari tabel master
                $jenis_id = JenisBarang::firstOrCreate(['nama_jenis' => trim($data['nama_barang'])])->id; // Asumsi 'Nama / Jenis Barang' adalah Jenis
                $sumber_id = SumberBarang::firstOrCreate(['nama_sumber' => trim($data['asal_perolehan'])])->id;
                $kondisi_id = Kondisi::firstOrCreate(['nama_kondisi' => trim($data['keadaan_barang'])])->id;
                $lokasi_id = Lokasi::firstOrCreate(['nama_lokasi' => trim($data['lokasi'])])->id;
                $status_id = StatusAset::where('nama_status', 'Tersedia')->value('id'); // Default "Tersedia"
                
                if (!empty($data['status'])) {
                    $status_id = StatusAset::firstOrCreate(['nama_status' => trim($data['status'])])->id;
                }

                // LOGIKA UTAMA: Loop berdasarkan 'JUMLAH'
                for ($i = 0; $i < $jumlah; $i++) {
                    Barang::create([
                        'nama_barang' => trim($nama_barang),
                        'kode_barang' => trim($data['kode_barang']),
                        'register' => str_pad($register_start + $i, 6, '0', STR_PAD_LEFT), // Buat register 000001, 000002, dst.
                        'merk_type' => trim($data['merk_type']),
                        'tahun_pembelian' => (int)($data['tahun_pembelian'] ?? null),
                        'harga' => (float)($data['harga'] ?? 0),
                        'keterangan' => trim($data['keterangan']),
                        'id_jenis' => $jenis_id,
                        'id_sumber' => $sumber_id,
                        'id_kondisi' => $kondisi_id,
                        'id_lokasi' => $lokasi_id,
                        'id_status_aset' => $status_id,
                    ]);
                    $totalAsetDibuat++;
                }
            }

            // Jika semua sukses, catat log
            LogAktivitas::create([
                'id_pengguna' => $this->user->id,
                'aksi' => 'IMPORT',
                'tabel' => 'barang',
                'keterangan' => "Berhasil mengimpor {$totalAsetDibuat} unit aset dari file CSV."
            ]);
            
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            // Catat error jika gagal
            Log::error('Import CSV Gagal Total: ' . $e->getMessage(), ['user_id' => $this->user->id]);
            LogAktivitas::create([
                'id_pengguna' => $this->user->id,
                'aksi' => 'GAGAL IMPORT',
                'tabel' => 'barang',
                'keterangan' => 'Gagal total impor CSV: ' . $e->getMessage()
            ]);
        } finally {
            fclose($fileHandle);
            Storage::delete($this->filePath); // Hapus file CSV setelah selesai
        }
    }
}