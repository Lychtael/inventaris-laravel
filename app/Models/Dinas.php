<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dinas extends Model
{
    use HasFactory;
    
    protected $table = 'dinas';
    public $timestamps = true; // Sesuai migrasi baru
    protected $fillable = ['nama_dinas'];

    /**
     * Relasi: Satu Dinas memiliki banyak Bidang
     */
    public function bidang()
    {
        return $this->hasMany(Bidang::class, 'id_dinas');
    }
}