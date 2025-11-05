<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class JenisBarang extends Model
{
    protected $table = 'jenis_barang';
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = null;
    protected $fillable = ['nama_jenis'];
}