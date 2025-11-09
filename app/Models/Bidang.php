<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bidang extends Model
{
    use HasFactory;
    
    protected $table = 'bidang';
    public $timestamps = true; // Sesuai migrasi baru
    protected $fillable = ['id_dinas', 'nama_bidang'];

    /**
     * Relasi: Satu Bidang dimiliki oleh satu Dinas
     */
    public function dinas()
    {
        return $this->belongsTo(Dinas::class, 'id_dinas');
    }
}