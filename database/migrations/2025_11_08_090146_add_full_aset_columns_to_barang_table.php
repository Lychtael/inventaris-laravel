<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            // Menambahkan kolom baru sesuai CSV
            
            // Kolom Spek (No. Sertifikat/Pabrik/dll - Index 5)
            $table->string('nomor_spek')->nullable()->after('merk_type');
            
            // Kolom Bahan (Index 6)
            $table->string('bahan')->nullable()->after('nomor_spek');
            
            // Kolom Ukuran (Index 9)
            $table->string('ukuran')->nullable()->after('tahun_pembelian');
            
            // Kolom Satuan (Index 10)
            $table->string('satuan')->nullable()->after('ukuran');
            
            // Kolom Pengguna (Index 17)
            $table->string('pengguna')->nullable()->after('id_lokasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            // Hapus kolom jika migrasi di-rollback
            $table->dropColumn([
                'nomor_spek',
                'bahan',
                'ukuran',
                'satuan',
                'pengguna'
            ]);
        });
    }
};