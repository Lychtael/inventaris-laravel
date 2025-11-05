<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kondisi; // <-- Import model Kondisi
use Illuminate\Support\Facades\DB; // <-- Import DB

class KondisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kita pakai DB::table->insert() agar bisa dijalankan ulang
        // tanpa error 'unique constraint' jika datanya sudah ada.
        // firstOrCreate() juga bisa, tapi ini lebih jelas untuk seeder.

        DB::table('kondisi')->insertOrIgnore([
            [
                'nama_kondisi' => 'Baik',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kondisi' => 'Rusak Ringan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kondisi' => 'Rusak Berat',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}