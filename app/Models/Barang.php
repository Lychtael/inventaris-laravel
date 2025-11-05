<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;
    
    protected $table = 'barang'; 
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = null;
    public $timestamps = false; // Ini harusnya 'true' jika Anda pakai 'dibuat_pada'

    // Kolom yang boleh diisi massal
    protected $fillable = [
        'nama_barang', 
        'jumlah', 
        'satuan', 
        'id_jenis', 
        'id_sumber', 
        'keterangan',
        'id_kondisi' // <-- TAMBAHKAN INI
    ];

    // Relasi ke JenisBarang
    public function jenis() {
        return $this->belongsTo(JenisBarang::class, 'id_jenis');
    }

    // Relasi ke SumberBarang
    public function sumber() {
        return $this->belongsTo(SumberBarang::class, 'id_sumber');
    }
    
    // ++ TAMBAHKAN RELASI BARU INI ++
    /**
     * Relasi ke Kondisi
     */
    public function kondisi() {
        return $this->belongsTo(Kondisi::class, 'id_kondisi');
    }
}