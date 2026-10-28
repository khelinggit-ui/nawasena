<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_pencairan_mekar', function (Blueprint $table) {
            if (Schema::hasColumn('master_pencairan_mekar', 'status_debitur')) {
                $table->dropColumn('status_debitur');
            }
        });
    }

    public function down(): void
    {
        Schema::table('master_pencairan_mekar', function (Blueprint $table) {
            if (!Schema::hasColumn('master_pencairan_mekar', 'status_debitur')) {
                $table->string('status_debitur', 20)->default('aktif');
            }
        });

        DB::table('master_pencairan_mekar')
            ->where('is_lunas', true)
            ->update(['status_debitur' => 'wo_lunas']);
    }
};