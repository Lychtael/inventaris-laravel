<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class LokasiSeeder extends Seeder {
    public function run(): void {
        DB::table('lokasi')->insert([
            ['nama_lokasi' => 'Ruang Kepala Dinas', 'penanggung_jawab' => 'Sekretaris'],
            ['nama_lokasi' => 'Ruang Sekretariat', 'penanggung_jawab' => 'Sekretaris'],
            ['nama_lokasi' => 'Gudang Aset', 'penanggung_jawab' => 'Pengurus Barang'],
        ]);
    }
}