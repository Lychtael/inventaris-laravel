<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class SumberBarangSeeder extends Seeder {
    public function run(): void {
        DB::table('sumber_barang')->insertOrIgnore([
            ['nama_sumber' => 'Pembelian', 'created_at' => now(), 'updated_at' => now()],
            ['nama_sumber' => 'Hibah', 'created_at' => now(), 'updated_at' => now()],
            ['nama_sumber' => 'Bantuan Pusat', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}