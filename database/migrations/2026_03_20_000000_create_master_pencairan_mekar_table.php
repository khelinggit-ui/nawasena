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
        Schema::create('master_pencairan_mekar', function (Blueprint $table) {
            $table->id();
            $table->string('no_kontrak')->unique()->nullable();
            $table->string('nama_debitur')->nullable();
            $table->string('nik')->nullable()->unique();
            $table->string('email')->nullable();
            $table->string('nomor_hp')->nullable();
            $table->string('nomor_identitas')->nullable();
            $table->text('alamat')->nullable();
            $table->string('kota')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('status')->default('active')->nullable();
            $table->decimal('nominal_pinjaman', 15, 2)->nullable();
            $table->integer('tenor_bulan')->nullable();
            $table->string('marketing')->nullable();
            $table->string('cabang')->nullable();
            $table->string('perusahaan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_pencairan_mekar');
    }
};
