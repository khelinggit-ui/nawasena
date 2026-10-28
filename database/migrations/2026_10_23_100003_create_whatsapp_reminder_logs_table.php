<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_reminder_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('debitur_id')->index();
            $table->unsignedBigInteger('pembayaran_angsuran_id')->nullable()->index();
            $table->integer('installment_number')->nullable();
            $table->string('reminder_type', 20)->index();
            $table->date('reminder_date');
            $table->string('phone_number', 20)->nullable()->index();
            $table->text('message')->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->string('provider_message_id')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['debitur_id', 'reminder_date']);
            $table->index(['phone_number', 'reminder_date']);
            $table->unique(
                ['debitur_id', 'installment_number', 'reminder_type'],
                'wa_reminder_unique_milestone'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_reminder_logs');
    }
};
