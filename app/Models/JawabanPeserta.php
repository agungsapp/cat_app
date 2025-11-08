<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JawabanPeserta extends Model
{
    protected $fillable = ['hasil_ujian_id', 'soal_id', 'opsi_id', 'benar'];

    public function hasilUjian()
    {
        return $this->belongsTo(HasilUjian::class);
    }
    public function soal()
    {
        return $this->belongsTo(Soal::class);
    }
    public function opsi()
    {
        return $this->belongsTo(SoalOpsi::class);
    }
}
