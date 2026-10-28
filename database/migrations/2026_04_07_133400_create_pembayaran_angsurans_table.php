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
        Schema::create('pembayaran_angsurans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_debitur'); // Atau string sesuai ID debitur
            $table->integer('urutan_bulan');
            $table->decimal('nominal_bayar', 15, 2)->default(0);
            $table->integer('status')->default(0)->comment('0 = belum lunas, 1 = Lunas, 2 = Sebagian'); // default 0 jika belum
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_angsurans');
    }
};
