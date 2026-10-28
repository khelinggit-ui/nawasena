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
            $table->decimal('pengajuan_plafond', 15, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_pencairan_mekar', function (Blueprint $table) {
            $table->dropColumn('pengajuan_plafond');
        });
    }
};
