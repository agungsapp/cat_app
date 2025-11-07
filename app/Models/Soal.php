<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    use HasFactory;

    protected $fillable = [
        'jenis_id',
        'pertanyaan_text',
        'media_type',
        'media_path',
        'skor',
    ];

    protected $casts = [
        'skor' => 'integer',
    ];

    // Relasi ke JenisUjian
    public function jenis()
    {
        return $this->belongsTo(JenisUjian::class, 'jenis_id');
    }

    // Relasi ke SoalOpsi
    public function opsi()
    {
        return $this->hasMany(SoalOpsi::class, 'soal_id')->orderBy('label');
    }

    // Helper: ambil jawaban benar
    public function jawabanBenar()
    {
        return $this->opsi()->where('is_correct', true)->first();
    }
}
