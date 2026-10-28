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
        Schema::create('debitur_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('debitur_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('status_tahap', 100);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('debitur_id')->references('id')->on('master_pencairan_mekar')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debitur_histories');
    }
};
