<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TipeUjian extends Model
{
    protected $fillable = ['nama', 'slug', 'max_attempt', 'mode'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->slug = Str::slug($model->nama);
        });
    }
}
