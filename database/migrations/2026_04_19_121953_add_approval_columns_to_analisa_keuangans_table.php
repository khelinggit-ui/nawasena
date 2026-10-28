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
            $table->text('marketing_note')->nullable();
            $table->string('marketing_status')->nullable();
            
            $table->text('analis_note')->nullable();
            $table->string('analis_status')->nullable();
            
            $table->text('kadivbisnis_note')->nullable();
            $table->string('kadivbisnis_status')->nullable();
            
            $table->text('pengelola_note')->nullable();
            $table->string('pengelola_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analisa_keuangans', function (Blueprint $table) {
            $table->dropColumn([
                'marketing_note', 'marketing_status',
                'analis_note', 'analis_status',
                'kadivbisnis_note', 'kadivbisnis_status',
                'pengelola_note', 'pengelola_status'
            ]);
        });
    }
};
