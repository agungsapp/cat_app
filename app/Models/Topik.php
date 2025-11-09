<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topik extends Model
{
    protected $fillable = [
        'nama_topik',
        'urutan',
    ];

    public function materis()
    {
        return $this->hasMany(Materi::class)->orderBy('urutan');
    }
}
