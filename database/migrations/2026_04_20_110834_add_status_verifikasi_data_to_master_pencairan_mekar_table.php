<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_pencairan_mekar', function (Blueprint $table) {
            $table->string('status_verifikasi_data')->nullable()->after('status_tahap');
        });
    }

    public function down(): void
    {
        Schema::table('master_pencairan_mekar', function (Blueprint $table) {
            $table->dropColumn('status_verifikasi_data');
        });
    }
};
