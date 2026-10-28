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
        Schema::table('debitur_histories', function (Blueprint $table) {
            // Kolom untuk tracking siapa yang memberikan revisi
            $table->string('revisi_dari_tahap', 100)->nullable()->after('status_tahap');
            $table->unsignedBigInteger('revised_by_user_id')->nullable()->after('user_id');
            
            // Kolom catatan per role untuk menyimpan alasan revisi
            $table->text('catatan_spv')->nullable()->after('catatan');
            $table->text('catatan_analis')->nullable()->after('catatan_spv');
            $table->text('catatan_risk')->nullable()->after('catatan_analis');
            $table->text('catatan_mitra')->nullable()->after('catatan_risk');
            
            // Foreign key untuk revised_by_user_id
            $table->foreign('revised_by_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('debitur_histories', function (Blueprint $table) {
            $table->dropForeign(['revised_by_user_id']);
            $table->dropColumn([
                'revisi_dari_tahap',
                'revised_by_user_id',
                'catatan_spv',
                'catatan_analis',
                'catatan_risk',
                'catatan_mitra'
            ]);
        });
    }
};
