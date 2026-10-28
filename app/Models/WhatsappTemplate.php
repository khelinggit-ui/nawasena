<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappTemplate extends Model
{
    protected $fillable = [
        'key',
        'name',
        'body',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function bodyFor(string $key, ?string $default = null): ?string
    {
        $template = static::query()
            ->where('key', $key)
            ->where('is_active', true)
            ->first();

        return $template?->body ?? $default;
    }
}
