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
            if (!Schema::hasColumn('master_pencairan_mekar', 'pengajuan_plafond_awal')) {
                $table->decimal('pengajuan_plafond_awal', 15, 2)->nullable()->after('pengajuan_plafond');
            }
            if (!Schema::hasColumn('master_pencairan_mekar', 'status_penurunan_plafond')) {
                $table->string('status_penurunan_plafond')->nullable()->after('status_verifikasi_data');
            }
            if (!Schema::hasColumn('master_pencairan_mekar', 'alasan_penurunan')) {
                $table->text('alasan_penurunan')->nullable()->after('status_penurunan_plafond');
            }
            if (!Schema::hasColumn('master_pencairan_mekar', 'status_takeout')) {
                $table->string('status_takeout')->nullable()->after('alasan_penurunan');
            }
            if (!Schema::hasColumn('master_pencairan_mekar', 'keterangan_takeout')) {
                $table->text('keterangan_takeout')->nullable()->after('status_takeout');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_pencairan_mekar', function (Blueprint $table) {
            $columns = [
                'pengajuan_plafond_awal',
                'status_penurunan_plafond',
                'alasan_penurunan',
                'status_takeout',
                'keterangan_takeout',
            ];
            foreach ($columns as $col) {
                if (Schema::hasColumn('master_pencairan_mekar', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
