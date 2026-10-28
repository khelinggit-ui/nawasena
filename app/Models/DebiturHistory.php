<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DebiturHistory extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::created(function (DebiturHistory $history): void {
            $timestamps = static::query()
                ->where('debitur_id', $history->debitur_id)
                ->selectRaw('MIN(created_at) as created_at, MAX(created_at) as updated_at')
                ->first();

            DB::table('master_pencairan_mekar')
                ->where('id', $history->debitur_id)
                ->update([
                    'created_at' => $timestamps->created_at,
                    'updated_at' => $timestamps->updated_at,
                ]);
        });
    }

    protected $fillable = [
        'debitur_id',
        'user_id',
        'revised_by_user_id',
        'status_tahap',
        'revisi_dari_tahap',
        'catatan',
        'catatan_spv',
        'catatan_analis',
        'catatan_risk',
        'catatan_mitra',
    ];

    public function debitur()
    {
        return $this->belongsTo(MasterPencairanMekar::class, 'debitur_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function revisedByUser()
    {
        return $this->belongsTo(User::class, 'revised_by_user_id');
    }
}
