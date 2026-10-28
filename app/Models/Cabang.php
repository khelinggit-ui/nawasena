<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_cabang',
        'spv_cabang',
        'target_cabang',
        'pencapaian_bulan_lalu',
        'status',
    ];
}
