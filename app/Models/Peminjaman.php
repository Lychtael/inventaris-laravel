<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Peminjaman extends Model
{
    protected $table = 'peminjaman';
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = null;
    protected $fillable = [
        'id_barang', 'peminjam', 'jumlah_dipinjam',
        'tanggal_pinjam', 'keterangan', 'status', 'tanggal_kembali'
    ];
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }
}