<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Import User
use Illuminate\Support\Facades\Hash; // Import Hash

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Panggil semua Seeder Master
        $this->call([
            PeranSeeder::class,
            JenisBarangSeeder::class,
            SumberBarangSeeder::class,
            KondisiSeeder::class,
            LokasiSeeder::class,
            StatusAsetSeeder::class,
        ]);

        // 2. Buat User Admin default
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password123'),
            'id_peran' => 1, // ID 1 = Administrator
        ]);
    }
}