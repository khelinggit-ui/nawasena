<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('analisa_keuangans', function (Blueprint $table) {
            $table->string('marketing_approved_by')->nullable()->after('marketing_status');
            $table->string('analis_approved_by')->nullable()->after('analis_status');
            $table->string('kadivbisnis_approved_by')->nullable()->after('kadivbisnis_status');
            $table->string('pengelola_approved_by')->nullable()->after('pengelola_status');
        });
    }

    public function down(): void
    {
        Schema::table('analisa_keuangans', function (Blueprint $table) {
            $table->dropColumn(['marketing_approved_by', 'analis_approved_by', 'kadivbisnis_approved_by', 'pengelola_approved_by']);
        });
    }
};
