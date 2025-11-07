<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    // Beri tahu Laravel nama tabel yang benar
    protected $table = 'lokasi';
    
    // Migrasi baru kita pakai timestamps() standar
    public $timestamps = true;

    protected $fillable = ['nama_lokasi', 'penanggung_jawab'];
}