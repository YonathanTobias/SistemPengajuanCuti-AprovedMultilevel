<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuti extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_tracking',
        'pegawai_id',
        'jenis_cuti',
        'tanggal_mulai',
        'tanggal_selesai',
        'jumlah_hari',
        'alasan',
        'alamat_cuti',
        'no_hp_cuti',
        'file_pendukung',
        'status',
        'catatan_kadiv',
        'kadiv_approved_at',
        'catatan_hrd',
        'hrd_approved_at',
        'catatan_ketua',
        'ketua_approved_at',
        'rejected_by',
        'catatan_penolakan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'kadiv_approved_at' => 'datetime',
        'hrd_approved_at' => 'datetime',
        'ketua_approved_at' => 'datetime',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}
