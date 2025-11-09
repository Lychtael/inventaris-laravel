<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Dinas; // Kita butuh ini
class BidangSeeder extends Seeder {
    public function run(): void {
        // Ambil ID Diskominfo
        $dinasDiskominfo = Dinas::where('nama_dinas', 'Diskominfo')->first();

        if ($dinasDiskominfo) {
            DB::table('bidang')->insertOrIgnore([
                ['id_dinas' => $dinasDiskominfo->id, 'nama_bidang' => 'Bidang TIK', 'created_at' => now(), 'updated_at' => now()],
                ['id_dinas' => $dinasDiskominfo->id, 'nama_bidang' => 'Bidang Aptika', 'created_at' => now(), 'updated_at' => now()],
                ['id_dinas' => $dinasDiskominfo->id, 'nama_bidang' => 'Sekretariat', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }
}