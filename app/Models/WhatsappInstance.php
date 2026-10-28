<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappInstance extends Model
{
    protected $fillable = [
        'instance_name',
        'phone_number',
        'status',
        'api_key',
    ];
}
