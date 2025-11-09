<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMateriProgress extends Model
{
    protected $table = 'user_materi_progress';

    protected $fillable = [
        'user_id',
        'konten_id',
        'is_completed',
        'completed_at'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function konten()
    {
        return $this->belongsTo(Konten::class);
    }

    // Tandai selesai (dipanggil saat user buka konten)
    public static function markCompleted($userId, $kontenId)
    {
        self::updateOrCreate(
            ['user_id' => $userId, 'konten_id' => $kontenId],
            ['is_completed' => true, 'completed_at' => now()]
        );
    }

    // Cek sudah selesai?
    public static function isCompleted($userId, $kontenId)
    {
        return self::where('user_id', $userId)
            ->where('konten_id', $kontenId)
            ->where('is_completed', true)
            ->exists();
    }
}
