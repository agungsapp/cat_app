<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Konten extends Model
{
    protected $fillable = [
        'submateri_id',
        'tipe',
        'isi',
        'file_path',
        'urutan',
    ];

    public function submateri()
    {
        return $this->belongsTo(Submateri::class);
    }
}
