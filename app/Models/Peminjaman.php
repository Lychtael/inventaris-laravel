<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';
    public $timestamps = true;

    protected $fillable = [
        'id_barang',
        'id_user_peminjam',
        'peminjam_eksternal',
        'tanggal_pinjam',
        'tanggal_kembali',
        'keterangan',
        'status_pinjam' // (Dipinjam / Dikembalikan)
    ];

    /**
     * Relasi ke 1 unit Aset (Barang)
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    /**
     * Relasi ke User Peminjam (jika internal)
     */
    public function userPeminjam()
    {
        return $this->belongsTo(User::class, 'id_user_peminjam');
    }
}