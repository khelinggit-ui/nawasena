<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalisaKeuangan extends Model
{
    use HasFactory;

    protected $table = 'analisa_keuangans';

    protected $guarded = ['id'];

    protected $casts = [
        'detail_hutang' => 'array',
        'tanggal_mpp' => 'date',
    ];

    public function debitur()
    {
        return $this->belongsTo(MasterPencairanMekar::class, 'debitur_id', 'id');
    }

    public function marketing()
    {
        return $this->belongsTo(User::class, 'marketing_id');
    }

    public function spv()
    {
        return $this->belongsTo(User::class, 'spv_id');
    }
}
