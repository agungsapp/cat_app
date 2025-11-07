<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SesiUjian extends Model
{
    protected $fillable = [
        'judul',
        'deskripsi',
        'tipe_ujian_id',
        'durasi_menit',
        'is_active',
        'waktu_mulai',
        'waktu_selesai',
    ];

    protected $table = 'sesi_ujian';

    protected $fillable = [
        'judul',
        'deskripsi',
        'tipe_ujian_id',
        'durasi_menit',
        'is_active',
        'waktu_mulai',
        'waktu_selesai'
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function tipeUjian()
    {
        return $this->belongsTo(TipeUjian::class);
    }

    public function soal()
    {
        return $this->belongsToMany(Soal::class, 'sesi_soal')
            ->withPivot('id')
            ->withTimestamps();
    }
}
