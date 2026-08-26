<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lembur extends Model
{
    use HasFactory;

    protected $fillable = [
        'pegawai_id',
        'kode_tracking',
        'tanggal_lembur',
        'jumlah_jam',
        'jumlah_menit',
        'kegiatan',
        'file_bukti',
        'status',
        'catatan_penolakan',
    ];

    protected $casts = [
        'tanggal_lembur' => 'date',
        'jumlah_jam' => 'integer',
        'jumlah_menit' => 'integer',
    ];

    protected $appends = [
        'durasi_formatted',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function getDurasiFormattedAttribute(): string
    {
        $totalMenit = (int) ($this->jumlah_menit ?: ($this->jumlah_jam * 60));
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
