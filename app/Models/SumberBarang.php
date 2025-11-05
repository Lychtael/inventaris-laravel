<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SumberBarang extends Model
{
    protected $table = 'sumber_barang';
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = null;
    protected $fillable = ['nama_sumber'];
}