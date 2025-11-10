<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class StatusAset extends Model
{
    use HasFactory;
    protected $table = 'status_aset';
    public $timestamps = true;
    protected $fillable = ['nama_status'];

    public function barang() {
        return $this->hasMany(Barang::class, 'id_status_aset');
    }
}