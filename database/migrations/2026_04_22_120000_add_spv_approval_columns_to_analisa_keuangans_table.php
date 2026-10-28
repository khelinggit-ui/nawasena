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
        Schema::table('analisa_keuangans', function (Blueprint $table) {
            if (!Schema::hasColumn('analisa_keuangans', 'spv_note')) {
                $table->text('spv_note')->nullable()->after('marketing_note');
            }
            if (!Schema::hasColumn('analisa_keuangans', 'spv_status')) {
                $table->string('spv_status')->nullable()->after('marketing_status');
            }
            if (!Schema::hasColumn('analisa_keuangans', 'spv_approved_by')) {
                $table->string('spv_approved_by')->nullable()->after('spv_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analisa_keuangans', function (Blueprint $table) {
            $dropColumns = [];
            if (Schema::hasColumn('analisa_keuangans', 'spv_note')) {
                $dropColumns[] = 'spv_note';
            }
            if (Schema::hasColumn('analisa_keuangans', 'spv_status')) {
                $dropColumns[] = 'spv_status';
            }
            if (Schema::hasColumn('analisa_keuangans', 'spv_approved_by')) {
                $dropColumns[] = 'spv_approved_by';
            }

            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
