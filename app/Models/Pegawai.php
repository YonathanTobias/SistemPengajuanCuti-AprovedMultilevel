<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $fillable = [
        'nip',
        'nama',
        'divisi_id',
        'jabatan',
        'email',
        'no_hp',
        'jatah_cuti',
        'sisa_cuti',
    ];

    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }

    public function cutis()
    {
        return $this->hasMany(Cuti::class);
    }
}
