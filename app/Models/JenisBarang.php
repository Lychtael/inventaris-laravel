<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class JenisBarang extends Model
{
    protected $table = 'jenis_barang';
    public $timestamps = true;
    protected $fillable = ['nama_jenis'];
}