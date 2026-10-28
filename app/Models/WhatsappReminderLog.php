<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappReminderLog extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'reminder_date' => 'date',
        'sent_at' => 'datetime',
        'meta' => 'array',
    ];

    public function debitur()
    {
        return $this->belongsTo(MasterPencairanMekar::class, 'debitur_id');
    }

    public function payment()
    {
        return $this->belongsTo(PembayaranAngsuran::class, 'pembayaran_angsuran_id');
    }
}
