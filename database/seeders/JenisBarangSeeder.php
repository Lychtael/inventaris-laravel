<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class JenisBarangSeeder extends Seeder {
    public function run(): void {
        DB::table('jenis_barang')->insert([
            ['nama_jenis' => 'Elektronik'],
            ['nama_jenis' => 'Mebel'],
            ['nama_jenis' => 'Habis Pakai (ATK)'],
            ['nama_jenis' => 'Kendaraan'],
        ]);
    }
}