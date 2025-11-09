<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;
    
    protected $table = 'barang';
    
    // Kita pakai timestamps() standar, bukan 'dibuat_pada' lagi
    public $timestamps = true; 

    /**
     * Kolom yang boleh diisi (sesuai migrasi LENGKAP)
     * Sesuai analisismu: (Grup 1 Dropdown, Grup 2 Manual)
     */
    protected $fillable = [
        // GRUP 1: Relasi (Dropdown)
        'id_dinas',
        'id_bidang',
        'id_jenis', 
        'id_sumber', 
        'id_kondisi',
        'id_status_aset',
        
        // GRUP 2: Data Unik (Input Manual Teks)
        'nama_barang', 
        'kode_barang', 
        'register', 
        'merk_type',
        'nomor_spek',
        'bahan',
        'ukuran',
        'satuan',
        'lokasi', // (Kolom Teks Biasa)
        'pengguna',
        'keterangan',

        // GRUP 3: Data Unik (Input Manual Angka)
        'tahun_pembelian',
        'harga',
    ];

    // === RELASI BARU (BELONGS TO) ===

    public function dinas() {
        return $this->belongsTo(Dinas::class, 'id_dinas');
    }

    public function bidang() {
        return $this->belongsTo(Bidang::class, 'id_bidang');
    }

    public function jenis() {
        return $this->belongsTo(JenisBarang::class, 'id_jenis');
    }

    public function sumber() {
        return $this->belongsTo(SumberBarang::class, 'id_sumber');
    }
    
    public function kondisi() {
        return $this->belongsTo(Kondisi::class, 'id_kondisi');
    }

    public function statusAset() {
        return $this->belongsTo(StatusAset::class, 'id_status_aset');
    }
}