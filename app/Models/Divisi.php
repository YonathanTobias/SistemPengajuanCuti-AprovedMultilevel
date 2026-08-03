<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_divisi',
        'nama_divisi',
        'deskripsi',
    ];

    public function pegawais()
    {
        return $this->hasMany(Pegawai::class);
    }

    public function kadivUser()
    {
        return $this->hasOne(User::class)->where('role', 'kadiv');
    }
}
