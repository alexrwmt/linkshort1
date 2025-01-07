<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Url extends Model
{
    protected $fillable = [
        'title',
        'original_url',
        'short_code',
        'views'
    ];

    public static function generateUniqueShortCode(): string
    {
        do {
            $code = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6);
        } while (static::where('short_code', $code)->exists());

        return $code;
    }
} 