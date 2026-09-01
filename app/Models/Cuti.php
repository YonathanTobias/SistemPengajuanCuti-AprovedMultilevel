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
        'tahun_cuti',
        'jumlah_hari',
        'jumlah_jam',
        'jumlah_menit',
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
        'tahun_cuti' => 'integer',
        'jumlah_jam' => 'integer',
        'jumlah_menit' => 'integer',
        'kadiv_approved_at' => 'datetime',
        'hrd_approved_at' => 'datetime',
        'ketua_approved_at' => 'datetime',
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
        if ($this->jumlah_hari > 0) {
            return "{$this->jumlah_hari} Hari";
        }

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

    /**
     * Scope untuk memfilter cuti berdasarkan tahun periode/anggaran
     * (Mendukung cuti kelonggaran awal Januari yang masuk ke tahun sebelumnya)
     */
    public function scopeForYear($query, $year)
    {
        return $query->where(function ($q) use ($year) {
            $q->where('tahun_cuti', $year)
              ->orWhere(function ($sub) use ($year) {
                  $sub->whereNull('tahun_cuti')
                      ->whereYear('tanggal_mulai', $year);
              });
        });
    }
}
