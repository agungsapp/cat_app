<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submateri extends Model
{
    protected $fillable = [
        'materi_id',
        'judul',
        'urutan',
    ];

    public function materi()
    {
        return $this->belongsTo(Materi::class);
    }

    public function kontens()
    {
        return $this->hasMany(Konten::class)->orderBy('urutan');
    }
}
