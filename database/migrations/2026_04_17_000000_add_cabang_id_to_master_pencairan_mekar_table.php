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
            // Add cabang_id foreign key
            if (!Schema::hasColumn('master_pencairan_mekar', 'cabang_id')) {
                $table->unsignedBigInteger('cabang_id')->nullable()->after('cabang');
                $table->foreign('cabang_id')
                    ->references('id')
                    ->on('cabangs')
                    ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_pencairan_mekar', function (Blueprint $table) {
            if (Schema::hasColumn('master_pencairan_mekar', 'cabang_id')) {
                $table->dropForeign(['cabang_id']);
                $table->dropColumn('cabang_id');
            }
        });
    }
};
