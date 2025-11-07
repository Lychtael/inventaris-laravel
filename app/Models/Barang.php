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
     * Kolom yang boleh diisi (sesuai migrasi baru)
     * Kolom 'jumlah' dan 'satuan' HILANG
     */
    protected $fillable = [
        'nama_barang', 
        'kode_barang', 
        'register', 
        'merk_type',
        'tahun_pembelian',
        'harga',
        'keterangan',
        'id_jenis', 
        'id_sumber', 
        'id_kondisi',
        'id_lokasi',
        'id_status_aset'
    ];

    // === RELASI BARU (BELONGS TO) ===

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