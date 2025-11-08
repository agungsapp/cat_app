<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilUjian extends Model
{
    protected $fillable = ['user_id', 'sesi_ujian_id', 'mulai_at', 'selesai_at', 'skor'];
    protected $casts = ['mulai_at' => 'datetime', 'selesai_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function sesiUjian()
    {
        return $this->belongsTo(SesiUjian::class);
    }
    public function jawaban()
    {
        return $this->hasMany(JawabanPeserta::class);
    }
}
