<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('analisa_keuangans', function (Blueprint $table) {
            $table->unsignedBigInteger('spv_id')->nullable()->after('spv_marketing');
            $table->decimal('usulan_plafond', 15, 2)->nullable()->after('pengajuan_plafond');
            $table->decimal('nilai_iir', 8, 2)->nullable()->after('total_angsuran_pengajuan');
            $table->decimal('nilai_ltv', 8, 2)->nullable()->after('nilai_iir');
        });
    }

    public function down(): void
    {
        Schema::table('analisa_keuangans', function (Blueprint $table) {
            $table->dropColumn(['spv_id', 'usulan_plafond', 'nilai_iir', 'nilai_ltv']);
        });
    }
};
