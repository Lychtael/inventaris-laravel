<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogAktivitas extends Model
{
    use HasFactory;

    // Tentukan nama tabel yang digunakan
    protected $table = 'log_aktivitas';

    // Laravel HANYA mengelola 'created_at' dan 'updated_at'
    // Karena tabel lama Anda hanya punya 'dibuat_pada', kita atur seperti ini:
    const CREATED_AT = 'dibuat_pada'; // beri tahu Laravel 'created_at' itu 'dibuat_pada'
    const UPDATED_AT = null; // kita tidak punya 'updated_at'

    /**
     * Kolom yang boleh diisi secara massal (Mass Assignable).
     * Ini PENTING untuk metode create()
     */
    protected $fillable = [
        'id_pengguna',
        'aksi',
        'tabel',
        'keterangan',
    ];

    /**
     * Definisikan relasi 'belongsTo' ke model Pengguna (User).
     * Asumsi: Model User/Pengguna Anda adalah App\Models\User (bawaan Breeze).
     *
     * Ini menggantikan JOIN manual di Log_model.php lama.
     * 'id_pengguna' adalah foreign key di tabel log_aktivitas.
     * 'id' adalah primary key di tabel users.
     */
    public function pengguna()
    {
        // Jika model pengguna Anda namanya 'Pengguna', ganti 'User::class'
        // menjadi 'Pengguna::class'
        return $this->belongsTo(User::class, 'id_pengguna', 'id');
    }
}