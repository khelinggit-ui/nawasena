<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranAngsuran extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_debitur',
        'urutan_bulan',
        'nominal_bayar',
        'status', // 0 = belum lunas, 1 = lunas, 2 = sebagian
    ];
}
