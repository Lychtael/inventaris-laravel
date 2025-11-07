<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class KondisiSeeder extends Seeder {
    public function run(): void {
        DB::table('kondisi')->insert([
            ['nama_kondisi' => 'Baik (B)'],
            ['nama_kondisi' => 'Kurang Baik (KB)'],
            ['nama_kondisi' => 'Rusak Berat (RB)'],
        ]);
    }
}