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
            if (!Schema::hasColumn('master_pencairan_mekar', 'kartu_keluarga')) {
                $table->string('kartu_keluarga')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_pencairan_mekar', function (Blueprint $table) {
            if (Schema::hasColumn('master_pencairan_mekar', 'kartu_keluarga')) {
                $table->dropColumn('kartu_keluarga');
            }
        });
    }
};
