<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'id_peran', // <-- INI TAMBAHAN WAJIB
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relasi: Satu User memiliki satu Peran
     */
    public function peran()
    {
        return $this->belongsTo(Peran::class, 'id_peran');
    }

    /**
     * Relasi: Satu User memiliki banyak LogAktivitas
     */
    public function logAktivitas()
    {
        // 'id_pengguna' adalah foreign key di tabel log
        return $this->hasMany(LogAktivitas::class, 'id_pengguna');
    }
}