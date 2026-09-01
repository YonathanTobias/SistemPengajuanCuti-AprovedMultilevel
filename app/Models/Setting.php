<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value'];

    /**
     * Ambil nilai pengaturan berdasarkan key
     */
    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Simpan atau perbarui nilai pengaturan
     */
    public static function set(string $key, $value)
    {
        return static::updateOrCreate(['key' => $key], ['value' => (string) $value]);
    }

    /**
     * Cek apakah fitur Simpanan Lembur aktif atau nonaktif
     * Default: FALSE (Nonaktif untuk uji coba cuti murni)
     */
    public static function isLemburEnabled(): bool
    {
        $val = static::get('feature_lembur');
        if ($val !== null) {
            return filter_var($val, FILTER_VALIDATE_BOOLEAN);
        }
        return filter_var(env('FEATURE_LEMBUR_ENABLED', false), FILTER_VALIDATE_BOOLEAN);
    }
}
