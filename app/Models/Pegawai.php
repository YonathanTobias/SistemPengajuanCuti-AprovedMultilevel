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
        'saldo_lembur', // Disimpan dalam satuan MENIT untuk presisi tinggi (misal: 80 menit = 1 jam 20 menit)
    ];

    protected $appends = [
        'saldo_lembur_formatted',
    ];

    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }

    public function cutis()
    {
        return $this->hasMany(Cuti::class);
    }

    public function lemburs()
    {
        return $this->hasMany(Lembur::class);
    }

    /**
     * Format saldo lembur menjadi Jam & Menit yang mudah dibaca
     */
    public function getSaldoLemburFormattedAttribute(): string
    {
        $totalMenit = (int) $this->saldo_lembur;
        if ($totalMenit <= 0) {
            return '0 Menit';
        }

        $jam = floor($totalMenit / 60);
        $menit = $totalMenit % 60;

        if ($jam > 0 && $menit > 0) {
            return "{$jam} Jam {$menit} Menit";
        } elseif ($jam > 0) {
            return "{$jam} Jam";
        } else {
            return "{$menit} Menit";
        }
    }
}
