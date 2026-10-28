<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappOptOut extends Model
{
    protected $fillable = [
        'phone_number',
        'reason',
        'source',
        'created_by',
    ];

    public static function isOptedOut(?string $phoneNumber): bool
    {
        if ($phoneNumber === null || $phoneNumber === '') {
            return false;
        }

        return static::query()->where('phone_number', $phoneNumber)->exists();
    }
}
