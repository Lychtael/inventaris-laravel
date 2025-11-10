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
        // === 1. TABEL MASTER (GRUP 1: DROPDOWN) ===
        
        // Tabel Peran (Untuk Admin/User)
        Schema::create('peran', function (Blueprint $table) {
            $table->id();
            $table->string('nama_peran', 50)->unique();
            $table->timestamps();
        });

        // Modifikasi tabel users bawaan untuk menambah 'id_peran'
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('id_peran')->after('id')->default(1)->constrained('peran');
        });

        // Tabel Master Dinas (BARU)
        Schema::create('dinas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_dinas', 150)->unique();
            $table->timestamps();
        });

        // Tabel Master Bidang (BARU, terhubung ke Dinas)
        Schema::create('bidang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_dinas')->constrained('dinas');
            $table->string('nama_bidang', 150);
            $table->timestamps();
        });

        // Tabel Master Kategori Aset (Dropdown)
        Schema::create('jenis_barang', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jenis', 100)->unique();
            $table->timestamps();
        });

        Schema::create('sumber_barang', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sumber', 100)->unique(); // Asal Perolehan
            $table->timestamps();
        });

        Schema::create('kondisi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kondisi', 50)->unique(); // B/KB/RB
            $table->timestamps();
        });

        // Tabel Status (Logika Inti: Tersedia, Dipinjam, Hilang)
        Schema::create('status_aset', function (Blueprint $table) {
            $table->id();
            $table->string('nama_status', 50)->unique(); 
            $table->timestamps();
        });

        // === 2. TABEL UTAMA: ASET (BARANG) ===
        // Sesuai 100% dengan CSV + Desain Hybrid-mu
        Schema::create('barang', function (Blueprint $table) {
            $table->id();
            
            // GRUP 1: Relasi (Dropdown)
            $table->foreignId('id_dinas')->nullable()->constrained('dinas');
            $table->foreignId('id_bidang')->nullable()->constrained('bidang');
            $table->foreignId('id_jenis')->nullable()->constrained('jenis_barang');
            $table->foreignId('id_sumber')->nullable()->constrained('sumber_barang');
            $table->foreignId('id_kondisi')->nullable()->constrained('kondisi');
            $table->foreignId('id_status_aset')->nullable()->constrained('status_aset');

            // GRUP 2: Data Unik (Input Manual Teks)
            $table->string('nama_barang'); // (Nama / Jenis Barang)
            $table->string('kode_barang')->nullable();
            $table->string('register')->nullable();
            $table->string('merk_type')->nullable();
            $table->string('nomor_spek')->nullable(); // (No. Sertifikat/Pabrik/dll)
            $table->string('bahan')->nullable();
            $table->string('ukuran')->nullable(); // (P, S, D)
            $table->string('satuan')->nullable();
            $table->string('lokasi')->nullable(); // (Kolom Teks Biasa)
            $table->string('pengguna')->nullable();
            $table->text('keterangan')->nullable();

            // GRUP 3: Data Unik (Input Manual Angka)
            $table->year('tahun_pembelian')->nullable();
            $table->decimal('harga', 15, 2)->nullable()->default(0);

            $table->timestamps();
        });

        // === 3. TABEL TRANSAKSI (Peminjaman & Log) ===
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_barang')->constrained('barang'); // ID Aset yang dipinjam
            $table->foreignId('id_user_peminjam')->nullable()->constrained('users'); 
            $table->string('peminjam_eksternal')->nullable(); 
            $table->date('tanggal_pinjam');
            $table->date('tanggal_kembali')->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status_pinjam', ['Dipinjam', 'Dikembalikan'])->default('Dipinjam');
            $table->timestamps();
        });

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
        Schema::dropIfExists('kondisi');
        Schema::dropIfExists('sumber_barang');
        Schema::dropIfExists('jenis_barang');
        Schema::dropIfExists('bidang');
        Schema::dropIfExists('dinas');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['id_peran']);
            $table->dropColumn('id_peran');
        });
        
        Schema::dropIfExists('peran');
    }
};