<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class StatusAsetSeeder extends Seeder {
    public function run(): void {
        DB::table('status_aset')->insert([
            ['nama_status' => 'Tersedia'],
            ['nama_status' => 'Dipinjam'],
            ['nama_status' => 'Hilang'],
            ['nama_status' => 'Dihapuskan'],
        ]);
    }
}