<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kondisi extends Model
{
    // ++ TAMBAHKAN BARIS INI ++
    /**
     * Beri tahu Laravel nama tabel yang benar (singular)
     */
    protected $table = 'kondisi';

    // Kolom yang boleh diisi
    protected $fillable = ['nama_kondisi'];

    /**
     * Relasi: Satu Kondisi dimiliki oleh banyak Barang
     */
    public function barang()
    {
        return $this->hasMany(Barang::class, 'id_kondisi');
    }
}