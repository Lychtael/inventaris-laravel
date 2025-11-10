<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Peminjaman extends Model
{
    use HasFactory;
    protected $table = 'peminjaman';
    public $timestamps = true; // Sesuai migrasi baru
    protected $fillable = [
        'id_barang',
        'id_user_peminjam',
        'peminjam_eksternal',
        'tanggal_pinjam',
        'tanggal_kembali',
        'keterangan',
        'status_pinjam'
    ];

    public function barang() {
        return $this->belongsTo(Barang::class, 'id_barang');
    }
    public function userPeminjam() {
        return $this->belongsTo(User::class, 'id_user_peminjam');
    }
}