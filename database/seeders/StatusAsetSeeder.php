<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class StatusAsetSeeder extends Seeder {
    public function run(): void {
        DB::table('status_aset')->insertOrIgnore([
            ['nama_status' => 'Tersedia', 'created_at' => now(), 'updated_at' => now()],
            ['nama_status' => 'Dipinjam', 'created_at' => now(), 'updated_at' => now()],
            ['nama_status' => 'Hilang', 'created_at' => now(), 'updated_at' => now()],
            ['nama_status' => 'Dihapuskan', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}