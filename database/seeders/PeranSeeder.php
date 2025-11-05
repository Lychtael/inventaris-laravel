<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Peran; // Import model

class PeranSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('peran')->insertOrIgnore([
            [
                'nama_peran' => 'Administrator',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_peran' => 'Pengguna',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}