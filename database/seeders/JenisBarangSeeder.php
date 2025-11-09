<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class JenisBarangSeeder extends Seeder {
    public function run(): void {
        DB::table('jenis_barang')->insertOrIgnore([
            ['nama_jenis' => 'Elektronik', 'created_at' => now(), 'updated_at' => now()],
            ['nama_jenis' => 'Mebel', 'created_at' => now(), 'updated_at' => now()],
            ['nama_jenis' => 'Habis Pakai (ATK)', 'created_at' => now(), 'updated_at' => now()],
            ['nama_jenis' => 'Kendaraan', 'created_at' => now(), 'updated_at' => now()],
            ['nama_jenis' => 'Televisi', 'created_at' => now(), 'updated_at' => now()], // Dari CSV
        ]);
    }
}