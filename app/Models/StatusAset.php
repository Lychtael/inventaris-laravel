<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusAset extends Model
{
    // Beri tahu Laravel nama tabel yang benar
    protected $table = 'status_aset';
    
    public $timestamps = true;

    protected $fillable = ['nama_status'];
}