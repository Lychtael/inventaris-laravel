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
        Schema::create('barang', function (Blueprint $table) {
            $table->id();
            $table->string('nama_barang', 100);
            $table->integer('jumlah');
            $table->string('satuan', 20);
            $table->text('keterangan')->nullable();
            
            // Foreign keys
            $table->foreignId('id_jenis')->nullable()->constrained('jenis_barang');
            $table->foreignId('id_sumber')->nullable()->constrained('sumber_barang');
            $table->timestamp('dibuat_pada')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};
