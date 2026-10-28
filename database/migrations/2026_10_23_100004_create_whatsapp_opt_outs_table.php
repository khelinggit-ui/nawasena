<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_opt_outs', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number', 20)->unique();
            $table->text('reason')->nullable();
            $table->string('source', 20)->default('manual');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_opt_outs');
    }
};
