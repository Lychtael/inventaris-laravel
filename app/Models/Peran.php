<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peran extends Model
{
    // Beri tahu Laravel nama tabel yang benar
    protected $table = 'peran';

    // Migrasi ini menggunakan timestamps() standar
    public $timestamps = true; 

    // Kolom yang boleh diisi
    protected $fillable = ['nama_peran'];
}