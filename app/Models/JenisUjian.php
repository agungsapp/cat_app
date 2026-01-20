<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class JenisUjian extends Model
{
    protected $fillable = ['kode', 'nama', 'tipe_penilaian', 'bobot_per_soal'];

    protected static function booted(): void
    {
        static::creating(function (JenisUjian $jenisUjian) {
            // dump('Event creating terpanggil untuk: ' . $jenisUjian->nama);
            if ($jenisUjian->isDirty('nama')) {
                $jenisUjian->kode = self::generateInitials($jenisUjian->nama);
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('nama')) {
                $model->kode = self::generateInitials($model->nama);
            }
        });
    }
    public static function generateInitials($string)
    {
        return collect(explode(' ', $string))->map(fn($word) => Str::upper(Str::substr($word, 0, 1)))->implode('');
    }
}
