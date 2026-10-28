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
            if (!Schema::hasColumn('master_pencairan_mekar', 'pendapatan_per_bulan')) {
                $table->decimal('pendapatan_per_bulan', 15, 2)->nullable()->comment('Gaji / Penghasilan Per Bulan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_pencairan_mekar', function (Blueprint $table) {
            if (Schema::hasColumn('master_pencairan_mekar', 'pendapatan_per_bulan')) {
                $table->dropColumn('pendapatan_per_bulan');
            }
        });
    }
};
