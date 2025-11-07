<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SumberBarang extends Model
{
    protected $table = 'sumber_barang';
    public $timestamps = true;
    protected $fillable = ['nama_sumber'];
}