<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Tambahkan ini
use Illuminate\Database\Eloquent\Model;
class SumberBarang extends Model
{
    use HasFactory; // Tambahkan ini
    protected $table = 'sumber_barang';
    public $timestamps = true; // Ganti dari logic lama
    protected $fillable = ['nama_sumber'];

    public function barang() {
        return $this->hasMany(Barang::class, 'id_sumber');
    }
}