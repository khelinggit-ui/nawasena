<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappBotSetting extends Model
{
    protected $fillable = [
        'instance_name',
        'is_active',
        'timezone',
        'send_window_start',
        'send_window_end',
        'max_per_day',
        'delay_min_seconds',
        'delay_max_seconds',
        'active_days',
        'reminder_rules',
        'branch_ids',
        'opt_out_enabled',
        'contact_cs',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'opt_out_enabled' => 'boolean',
        'active_days' => 'array',
        'reminder_rules' => 'array',
        'branch_ids' => 'array',
    ];

    public static function current(): ?self
    {
        return static::query()->latest('id')->first();
    }

    public function instance()
    {
        return $this->belongsTo(WhatsappInstance::class, 'instance_name', 'instance_name');
    }
}
