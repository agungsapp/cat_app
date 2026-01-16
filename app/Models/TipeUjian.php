<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Casts\Attribute;

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

    protected function mode(): Attribute
    {
        return Attribute::make(
            get: fn($value) => str($value)->replace('_', ' ')->title(),
        );
    }
}
