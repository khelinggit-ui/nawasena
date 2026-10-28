<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('analisa_keuangans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('debitur_id');
            $table->string('nomor_mpp')->nullable();
            $table->date('tanggal_mpp')->nullable();
            $table->string('jenis_fasilitas')->nullable();
            $table->unsignedBigInteger('cabang_id')->nullable();
            $table->unsignedBigInteger('marketing_id')->nullable();
            $table->string('spv_marketing')->nullable();
            $table->decimal('pengajuan_plafond', 15, 2)->nullable();
            $table->decimal('bunga_persen', 5, 2)->nullable();
            $table->string('suku_bunga')->nullable();
            $table->string('jaminan')->nullable();
            $table->string('kode_jaminan')->nullable();
            $table->decimal('saldo_bpjs', 15, 2)->nullable();
            $table->string('jenis_produk')->nullable();
            $table->decimal('total_penghasilan_bersih', 18, 2)->nullable();
            $table->json('detail_hutang')->nullable();
            $table->decimal('total_angsuran_hutang', 18, 2)->nullable();
            $table->decimal('total_angsuran_pengajuan', 18, 2)->nullable();
            $table->timestamps();

            // Foreign keys can be added if the referenced tables exist with exact types
            // For now just defining the column to avoid strict constraints errors if tables are MyISAM or different structure
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analisa_keuangans');
    }
};
