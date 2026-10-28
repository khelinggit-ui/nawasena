<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class MasterPencairanMekar extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * Bootstrap the model and assign nomor_debitur on creation.
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->nomor_debitur)) {
                $datePart = now()->format('dmY'); // e.g., 23082026
                $prefix = "Deb-{$datePart}";
                // Find last nomor_debitur with same prefix
                $last = self::where('nomor_debitur', 'like', "{$prefix}%")
                    ->orderBy('nomor_debitur', 'desc')
                    ->value('nomor_debitur');
                $next = 1;
                if ($last) {
                    $lastNumber = intval(substr($last, strlen($prefix)));
                    $next = $lastNumber + 1;
                }
                $model->nomor_debitur = $prefix . str_pad($next, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    protected $table = 'master_pencairan_mekar';

     public $timestamps = true;

     protected $guarded = ['id', 'created_at', 'updated_at', 'is_lunas'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('foto_ktp')
             ->singleFile();

        $this->addMediaCollection('ktp_pasangan')
             ->singleFile();

        $this->addMediaCollection('foto_bpjs')
             ->singleFile();

        $this->addMediaCollection('selfie_ktp')
             ->singleFile();

        $this->addMediaCollection('kartu_keluarga')
             ->singleFile();

        $this->addMediaCollection('privy_id_file')
             ->singleFile();

        $this->addMediaCollection('id_card')
             ->singleFile();

        $this->addMediaCollection('slip_gaji')
             ->singleFile();

        $this->addMediaCollection('buku_nikah')
             ->singleFile();

        $this->addMediaCollection('dokumen_pendukung');

        $this->addMediaCollection('mutasi_bank');
    }

    public function pembayaranAngsurans()
    {
        return $this->hasMany(PembayaranAngsuran::class, 'id_debitur', 'id');
    }

    public function marketing()
    {
        return $this->belongsTo(User::class, 'marketing_id');
    }

    public function spv()
    {
        return $this->belongsTo(User::class, 'spv_id');
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }
     public function kantor()
     {
          return $this->belongsTo(Kantor::class, 'kantor_id');
     }

    public function histories()
    {
        return $this->hasMany(DebiturHistory::class, 'debitur_id', 'id')->orderBy('created_at', 'asc');
    }

     public function previousLoan()
     {
          return $this->belongsTo(self::class, 'previous_loan_id');
     }

     public function renewalLoans()
     {
          return $this->hasMany(self::class, 'previous_loan_id');
     }
}