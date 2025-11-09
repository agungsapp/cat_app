<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    protected $fillable = [
        'topik_id',
        'judul',
        'urutan',
    ];

    public function topik()
    {
        return $this->belongsTo(Topik::class);
    }

    public function submateris()
    {
        return $this->hasMany(Submateri::class)->orderBy('urutan');
    }
}
