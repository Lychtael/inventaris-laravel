<?php

namespace App\Jobs;

use App\Models\Barang;
use App\Models\JenisBarang;
use App\Models\SumberBarang;
use App\Models\LogAktivitas;
use App\Models\User; 
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage; 

class ProcessCsvImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $user;
    protected $headerMap;

    /**
     * Create a new job instance.
     * Kita simpan path file dan siapa yang mengupload
     */
    public function __construct(string $filePath, User $user)
    {
        $this->filePath = $filePath;
        $this->user = $user;
        
        // Kamus mapping dari controller
        $this->headerMap = [
            'nama barang' => 'nama_barang', 'nama_barang' => 'nama_barang', 'nama' => 'nama_barang',
            'qty' => 'qty', 'kuantitas' => 'qty', 'jumlah' => 'qty',
            'satuan' => 'satuan', 'jenis' => 'jenis', 'kategori' => 'jenis',
            'sumber' => 'sumber', 'asal' => 'sumber',
            'keterangan' => 'keterangan', 'ket' => 'keterangan'
        ];
    }

    /**
     * Execute the job.
     * Ini adalah logika impor dari BarangController
     */
    public function handle(): void
    {
        // Dapatkan path absolut dari storage
        $fullPath = Storage::path($this->filePath);

        $handle = fopen($fullPath, "r");
        if ($handle === false) {
            // Jika gagal, hapus file dan hentikan
            Storage::delete($this->filePath);
            return; 
        }

        try {
            $headers_raw = fgetcsv($handle, 1000, ",");
            if (!$headers_raw) {
                 fclose($handle);
                 Storage::delete($this->filePath);
                 return;
            }
            
            $headers = [];
            foreach ($headers_raw as $h) {
                $key = strtolower(trim($h));
                $headers[] = $this->headerMap[$key] ?? $key;
            }

            $rowNumber = 1;

            DB::beginTransaction();
            
            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $rowNumber++;
                if (count($row) != count($headers)) {
                    continue; // Lewati baris yang kolomnya tidak cocok
                }
                $data = array_combine($headers, $row);

                $nama_barang = $data['nama_barang'] ?? null;
                $jumlah = $data['qty'] ?? 0;
                $satuan = $data['satuan'] ?? null;
                $nama_jenis = $data['jenis'] ?? null;
                $nama_sumber = $data['sumber'] ?? null;
                $keterangan = $data['keterangan'] ?? null;
                
                if (empty($nama_barang) || empty($satuan) || empty($nama_jenis) || empty($nama_sumber)) {
                     continue; // Lewati baris yang datanya tidak lengkap
                }

                $jenis = JenisBarang::firstOrCreate(['nama_jenis' => trim($nama_jenis)]);
                $sumber = SumberBarang::firstOrCreate(['nama_sumber' => trim($nama_sumber)]);

                Barang::create([
                    'nama_barang' => $nama_barang, 'jumlah' => (int)$jumlah, 'satuan' => $satuan,
                    'id_jenis' => $jenis->id, 'id_sumber' => $sumber->id, 'keterangan' => $keterangan,
                ]);
            }

            // -- LOGGING --
            // Kita gunakan $this->user->id, BUKAN Auth::id()
            LogAktivitas::create([
                'id_pengguna' => $this->user->id,
                'aksi' => 'IMPORT',
                'tabel' => 'barang',
                'keterangan' => 'Mengimpor data dari file CSV. ' . ($rowNumber - 1) . ' baris diproses.'
            ]);
            
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            // Di sini Anda bisa menambahkan log error jika mau
            // Log::error('Import CSV Gagal: ' . $e->getMessage());
        } finally {
            fclose($handle);
            // PENTING: Hapus file CSV setelah selesai diproses
            Storage::delete($this->filePath);
        }
    }
}