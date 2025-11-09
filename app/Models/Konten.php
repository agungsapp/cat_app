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

    // App/Models/Konten.php
    public function getYouTubeThumbnail()
    {
        if ($this->tipe !== 'video') return asset('images/no-image.jpg');

        $url = $this->file_path;
        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $match);
        $videoId = $match[1] ?? null;

        return $videoId ? "https://img.youtube.com/vi/{$videoId}/0.jpg" : asset('images/no-image.jpg');
    }
}
