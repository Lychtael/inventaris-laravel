<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class PeranSeeder extends Seeder {
    public function run(): void {
        DB::table('peran')->insert([
            ['nama_peran' => 'Administrator'],
            ['nama_peran' => 'Pengguna'],
        ]);
    }
}