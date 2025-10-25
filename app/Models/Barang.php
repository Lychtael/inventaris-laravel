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
    public $timestamps = false; 

    // Kolom yang boleh diisi massal
    protected $fillable = [
        'nama_barang', 
        'jumlah', 
        'satuan', 
        'id_jenis', 
        'id_sumber', 
        'keterangan'
    ];

    // Relasi ke JenisBarang (menggantikan JOIN manual)
    public function jenis() {
        return $this->belongsTo(JenisBarang::class, 'id_jenis');
    }

    // Relasi ke SumberBarang
    public function sumber() {
        return $this->belongsTo(SumberBarang::class, 'id_sumber');
    }
}