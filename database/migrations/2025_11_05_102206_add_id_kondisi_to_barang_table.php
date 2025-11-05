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
            // 1. Tambahkan kolom foreign key baru
            $table->foreignId('id_kondisi')
                  ->nullable() // Boleh null dulu agar data lama tidak error
                  ->constrained('kondisi') // Terhubung ke tabel 'kondisi'
                  ->after('id_sumber'); // Posisikan setelah 'id_sumber' (opsional)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            // 1. Hapus foreign key constraint dulu
            $table->dropForeign(['id_kondisi']);
            // 2. Hapus kolomnya
            $table->dropColumn('id_kondisi');
        });
    }
};