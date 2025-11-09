<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bidang extends Model
{
    use HasFactory;
    
    protected $table = 'bidang';
    public $timestamps = true;
    protected $fillable = ['id_dinas', 'nama_bidang'];

    /**
     * Relasi: Satu Bidang dimiliki oleh satu Dinas
     */
    public function dinas()
    {
        return $this->belongsTo(Dinas::class, 'id_dinas');
    }

    /**
     * ++ TAMBAHKAN RELASI INI ++
     * Relasi: Satu Bidang memiliki banyak Barang
     */
    public function barang()
    {
        return $this->hasMany(Barang::class, 'id_bidang');
    }
}