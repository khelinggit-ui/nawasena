<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_pencairan_mekar', function (Blueprint $table) {
            if (!Schema::hasColumn('master_pencairan_mekar', 'is_lunas')) {
                $table->boolean('is_lunas')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('master_pencairan_mekar', function (Blueprint $table) {
            if (Schema::hasColumn('master_pencairan_mekar', 'is_lunas')) {
                $table->dropColumn('is_lunas');
            }
        });
    }
};