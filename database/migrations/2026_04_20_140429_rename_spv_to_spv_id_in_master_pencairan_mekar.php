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
            $table->dropColumn('spv');
        });

        Schema::table('master_pencairan_mekar', function (Blueprint $table) {
            $table->unsignedBigInteger('spv_id')->nullable()->after('marketing_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_pencairan_mekar', function (Blueprint $table) {
            $table->dropColumn('spv_id');
        });

        Schema::table('master_pencairan_mekar', function (Blueprint $table) {
            $table->string('spv')->nullable()->after('marketing_id');
        });
    }
};
