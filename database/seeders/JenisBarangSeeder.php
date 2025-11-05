<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisBarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Menggunakan insertOrIgnore agar aman dijalankan berkali-kali
        DB::table('jenis_barang')->insertOrIgnore([
            ['nama_jenis' => 'Elektronik', 'dibuat_pada' => now()],
            ['nama_jenis' => 'Habis Pakai (ATK)', 'dibuat_pada' => now()],
            ['nama_jenis' => 'Mebel', 'dibuat_pada' => now()],
            ['nama_jenis' => 'Kendaraan', 'dibuat_pada' => now()],
            ['nama_jenis' => 'Lain-lain', 'dibuat_pada' => now()],
        ]);
    }
}