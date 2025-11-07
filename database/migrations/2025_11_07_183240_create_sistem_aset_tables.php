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
        // === TABEL PENGGUNA & PERAN (SAMA SEPERTI SEBELUMNYA) ===
        Schema::create('peran', function (Blueprint $table) {
            $table->id();
            $table->string('nama_peran', 50)->unique();
            $table->timestamps();
        });

        // Modifikasi tabel users bawaan untuk menambah 'id_peran'
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('id_peran')->after('id')->default(1)->constrained('peran');
        });

        // === TABEL MASTER INVENTARIS (SESUAI CSV) ===
        Schema::create('jenis_barang', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jenis', 100)->unique();
            $table->timestamps();
        });

        Schema::create('sumber_barang', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sumber', 100)->unique(); // Sesuai "Asal/Cara Perolehan"
            $table->timestamps();
        });

        Schema::create('kondisi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kondisi', 50)->unique(); // B, KB, RB
            $table->timestamps();
        });

        Schema::create('lokasi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lokasi', 100)->unique(); // Sesuai "Lokasi"
            $table->string('penanggung_jawab')->nullable(); // Sesuai "Penanggung Jawab Ruangan"
            $table->timestamps();
        });

        Schema::create('status_aset', function (Blueprint $table) {
            $table->id();
            $table->string('nama_status', 50)->unique(); // Sesuai "Status" (Hilang, Tersedia, Dipinjam, Dihapuskan)
            $table->timestamps();
        });

        // === TABEL UTAMA: BARANG (SEKARANG MENJADI ASET) ===
        Schema::create('barang', function (Blueprint $table) {
            $table->id();
            $table->string('nama_barang'); // Sesuai "Nama / Jenis Barang"
            $table->string('kode_barang')->nullable(); // Sesuai "Kode Barang"
            $table->string('register')->nullable(); // Sesuai "Register"
            
            $table->string('merk_type')->nullable(); // Sesuai "Merk/Type"
            $table->year('tahun_pembelian')->nullable(); // Sesuai "Tahun Pembelian"
            $table->decimal('harga', 15, 2)->nullable()->default(0); // Sesuai "Harga"
            $table->text('keterangan')->nullable();
            
            // Foreign Keys ke Tabel Master
            $table->foreignId('id_jenis')->nullable()->constrained('jenis_barang');
            $table->foreignId('id_sumber')->nullable()->constrained('sumber_barang');
            $table->foreignId('id_kondisi')->nullable()->constrained('kondisi');
            $table->foreignId('id_lokasi')->nullable()->constrained('lokasi');
            $table->foreignId('id_status_aset')->nullable()->constrained('status_aset');

            $table->timestamps(); // Menggantikan 'dibuat_pada'
        });

        // === TABEL TRANSAKSI: PEMINJAMAN (LOGIKA BARU) ===
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            // Tidak perlu 'jumlah' karena kita meminjam 1 unit aset
            $table->foreignId('id_barang')->constrained('barang'); // ID Aset yang dipinjam
            $table->foreignId('id_user_peminjam')->nullable()->constrained('users'); // Jika peminjam adalah user internal
            $table->string('peminjam_eksternal')->nullable(); // Jika peminjam orang luar
            
            $table->date('tanggal_pinjam');
            $table->date('tanggal_kembali')->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status_pinjam', ['Dipinjam', 'Dikembalikan'])->default('Dipinjam');
            $table->timestamps();
        });

        // === TABEL LOG (SAMA SEPERTI SEBELUMNYA) ===
        Schema::create('log_aktivitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pengguna')->nullable()->constrained('users');
            $table->string('aksi', 50);
            $table->string('tabel', 50);
            $table->text('keterangan');
            $table->timestamp('dibuat_pada')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus dalam urutan terbalik
        Schema::dropIfExists('log_aktivitas');
        Schema::dropIfExists('peminjaman');
        Schema::dropIfExists('barang');
        Schema::dropIfExists('status_aset');
        Schema::dropIfExists('lokasi');
        Schema::dropIfExists('kondisi');
        Schema::dropIfExists('sumber_barang');
        Schema::dropIfExists('jenis_barang');
        
        // Hapus foreign key 'id_peran' dari 'users'
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['id_peran']);
            $table->dropColumn('id_peran');
        });
        
        Schema::dropIfExists('peran');
    }
};