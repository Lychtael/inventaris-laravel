<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class SumberBarangSeeder extends Seeder {
    public function run(): void {
        DB::table('sumber_barang')->insert([
            ['nama_sumber' => 'Pembelian'],
            ['nama_sumber' => 'Hibah'],
            ['nama_sumber' => 'Bantuan Pusat'],
        ]);
    }
}