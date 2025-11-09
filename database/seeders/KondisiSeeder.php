<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class KondisiSeeder extends Seeder {
    public function run(): void {
        DB::table('kondisi')->insertOrIgnore([
            ['nama_kondisi' => 'Baik (B)', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kondisi' => 'Kurang Baik (KB)', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kondisi' => 'Rusak Berat (RB)', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kondisi' => 'Baik', 'created_at' => now(), 'updated_at' => now()], // Data dari CSV lama
            ['nama_kondisi' => 'KB', 'created_at' => now(), 'updated_at' => now()],   // Data dari CSV lama
            ['nama_kondisi' => 'RB', 'created_at' => now(), 'updated_at' => now()],   // Data dari CSV lama
        ]);
    }
}