<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Kondisi extends Model
{
    use HasFactory;
    protected $table = 'kondisi';
    public $timestamps = true;
    protected $fillable = ['nama_kondisi'];

    public function barang() {
        return $this->hasMany(Barang::class, 'id_kondisi');
    }
}