<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_bot_settings', function (Blueprint $table) {
            $table->id();
            $table->string('instance_name')->nullable()->index();
            $table->boolean('is_active')->default(false);
            $table->string('timezone', 64)->default('Asia/Jakarta');
            $table->time('send_window_start')->default('09:00:00');
            $table->time('send_window_end')->default('16:00:00');
            $table->unsignedInteger('max_per_day')->default(50);
            $table->unsignedInteger('delay_min_seconds')->default(20);
            $table->unsignedInteger('delay_max_seconds')->default(30);
            $table->json('active_days')->nullable();
            $table->json('reminder_rules')->nullable();
            $table->json('branch_ids')->nullable();
            $table->boolean('opt_out_enabled')->default(false);
            $table->string('contact_cs', 100)->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_account_number', 50)->nullable();
            $table->string('bank_account_name', 150)->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_bot_settings');
    }
};
