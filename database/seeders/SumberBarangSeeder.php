<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SumberBarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sumber_barang')->insertOrIgnore([
            ['nama_sumber' => 'Hibah', 'dibuat_pada' => now()],
            ['nama_sumber' => 'Beli (APBD)', 'dibuat_pada' => now()],
            ['nama_sumber' => 'Bantuan Pusat', 'dibuat_pada' => now()],
            ['nama_sumber' => 'Sumbangan Pihak ke-3', 'dibuat_pada' => now()],
        ]);
    }
}