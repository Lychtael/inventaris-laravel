<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogAktivitas extends Model
{
    use HasFactory;

    protected $table = 'log_aktivitas';
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = null;

    // ++ TAMBAHKAN BLOK INI ++
    /**
     * Beri tahu Laravel untuk mengubah kolom ini menjadi obyek Tanggal (Carbon)
     */
    protected $casts = [
        'dibuat_pada' => 'datetime',
    ];
    // ++ BATAS TAMBAHAN ++


    protected $fillable = [
        'id_pengguna',
        'aksi',
        'tabel',
        'keterangan',
    ];

    public function pengguna()
    {
        return $this->belongsTo(User::class, 'id_pengguna');
    }
}