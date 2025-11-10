<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class DinasSeeder extends Seeder {
    public function run(): void {
        DB::table('dinas')->insertOrIgnore([
            ['nama_dinas' => 'Diskominfo', 'created_at' => now(), 'updated_at' => now()],
            ['nama_dinas' => 'Dinas Pendidikan', 'created_at' => now(), 'updated_at' => now()],
            ['nama_dinas' => 'Dinas Kesehatan', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}