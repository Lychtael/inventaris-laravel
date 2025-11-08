<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;
    
    protected $table = 'barang';
    
    // Kita pakai timestamps() standar
    public $timestamps = true; 

    /**
     * Kolom yang boleh diisi (sesuai migrasi LENGKAP)
     * Kita tambahkan 5 kolom baru di sini
     */
    protected $fillable = [
        'nama_barang', 
        'kode_barang', 
        'register', 
        'merk_type',
        'nomor_spek',       // <-- KOLOM BARU
        'bahan',            // <-- KOLOM BARU
        'tahun_pembelian',
        'ukuran',           // <-- KOLOM BARU
        'satuan',           // <-- KOLOM BARU (YANG KEMBALI)
        'harga',
        'keterangan',
        'id_jenis', 
        'id_sumber', 
        'id_kondisi',
        'id_lokasi',
        'pengguna',         // <-- KOLOM BARU
        'id_status_aset'
    ];

    // === RELASI (BELONGS TO) ===

    public function jenis() {
        return $this->belongsTo(JenisBarang::class, 'id_jenis');
    }

    public function sumber() {
        return $this->belongsTo(SumberBarang::class, 'id_sumber');
    }
    
    public function kondisi() {
        return $this->belongsTo(Kondisi::class, 'id_kondisi');
    }

    public function lokasi() {
        return $this->belongsTo(Lokasi::class, 'id_lokasi');
    }

    public function statusAset() {
        return $this->belongsTo(StatusAset::class, 'id_status_aset');
    }
}