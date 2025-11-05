<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoalOpsi extends Model
{
    protected $fillable = [
        'soal_id',
        'label',
        'teks',
        'media_type',
        'media_path',
        'is_correct',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];
}
