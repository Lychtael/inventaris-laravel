<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // Import Hash

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Panggil semua Seeder Master
        // PASTIKAN URUTANNYA BENAR: Dinas dulu, baru Bidang
        $this->call([
            PeranSeeder::class,
            DinasSeeder::class,     // <-- BARU
            BidangSeeder::class,    // <-- BARU
            JenisBarangSeeder::class,
            SumberBarangSeeder::class,
            KondisiSeeder::class,
            StatusAsetSeeder::class,
        ]);

        // 2. Buat User Admin default
        // (Kita gunakan updateOrInsert agar aman dijalankan berkali-kali)
        User::updateOrInsert(
            ['email' => 'admin@gmail.com'], // Cari berdasarkan email
            [
                'name' => 'Administrator',
                'password' => Hash::make('password123'),
                'id_peran' => 1, // ID 1 = Administrator dari PeranSeeder
            ]
        );

        // 3. (Opsional) Buat User Biasa untuk tes
        User::updateOrInsert(
            ['email' => 'user@gmail.com'],
            [
                'name' => 'User Biasa',
                'password' => Hash::make('password123'),
                'id_peran' => 2, // ID 2 = Pengguna dari PeranSeeder
            ]
        );
    }
}