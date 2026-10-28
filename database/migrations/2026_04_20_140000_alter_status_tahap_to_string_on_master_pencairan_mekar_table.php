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
        Schema::table('master_pencairan_mekar', function (Blueprint $table) {
            // Ubah kolom status_tahap dari ENUM menjadi STRING
            $table->string('status_tahap', 100)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_pencairan_mekar', function (Blueprint $table) {
            // Kembalikan ke enum jika perlu (opsional, sesuaikan dengan enum awal)
            $table->enum('status_tahap', [
                'pending_spv',
                'pending_adminkredit',
                'pending_analis',
                'pending_risk',
                'pending_mitra',
                'approved',
                'revisi',
                'reject'
            ])->default('pending_spv')->nullable()->change();
        });
    }
};
