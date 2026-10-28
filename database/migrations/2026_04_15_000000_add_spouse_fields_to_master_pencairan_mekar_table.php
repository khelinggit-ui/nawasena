<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_pencairan_mekar', function (Blueprint $table) {
            if (!Schema::hasColumn('master_pencairan_mekar', 'nama_pasangan')) {
                $table->string('nama_pasangan')->nullable();
            }

            if (!Schema::hasColumn('master_pencairan_mekar', 'nik_pasangan')) {
                $table->string('nik_pasangan')->nullable();
            }

            if (!Schema::hasColumn('master_pencairan_mekar', 'nomor_hp_pasangan')) {
                $table->string('nomor_hp_pasangan', 50)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master_pencairan_mekar', function (Blueprint $table) {
            if (Schema::hasColumn('master_pencairan_mekar', 'nomor_hp_pasangan')) {
                $table->dropColumn('nomor_hp_pasangan');
            }
            if (Schema::hasColumn('master_pencairan_mekar', 'nik_pasangan')) {
                $table->dropColumn('nik_pasangan');
            }
            if (Schema::hasColumn('master_pencairan_mekar', 'nama_pasangan')) {
                $table->dropColumn('nama_pasangan');
            }
        });
    }
};
