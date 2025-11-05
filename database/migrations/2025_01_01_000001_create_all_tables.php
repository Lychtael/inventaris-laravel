<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Master
        Schema::create('jenis_barang', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jenis', 100);
            $table->timestamp('dibuat_pada')->useCurrent();
        });

        Schema::create('sumber_barang', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sumber', 100);
            $table->timestamp('dibuat_pada')->useCurrent();
        });

        // Tabel Barang (tergantung master)
        Schema::create('barang', function (Blueprint $table) {
            $table->id();
            $table->string('nama_barang', 100);
            $table->integer('jumlah');
            $table->string('satuan', 20);
            $table->foreignId('id_jenis')->nullable()->constrained('jenis_barang');
            $table->foreignId('id_sumber')->nullable()->constrained('sumber_barang');
            $table->text('keterangan')->nullable();
            $table->timestamp('dibuat_pada')->useCurrent();
        });

        // Tabel Peminjaman (tergantung barang)
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_barang')->constrained('barang');
            $table->string('peminjam', 100);
            $table->integer('jumlah_dipinjam');
            $table->date('tanggal_pinjam');
            $table->date('tanggal_kembali')->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status', ['dipinjam', 'dikembalikan'])->default('dipinjam');
            $table->timestamp('dibuat_pada')->useCurrent();
        });

        // Tabel Log (sesuai Controller)
        Schema::create('log_aktivitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pengguna')->nullable()->constrained('users'); // Terhubung ke 'users'
            $table->string('aksi', 50);
            $table->string('tabel', 50);
            $table->text('keterangan');
            $table->timestamp('dibuat_pada')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_aktivitas');
        Schema::dropIfExists('peminjaman');
        Schema::dropIfExists('barang');
        Schema::dropIfExists('sumber_barang');
        Schema::dropIfExists('jenis_barang');
    }
};