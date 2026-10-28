<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_pencairan_mekar', function (Blueprint $table) {
            if (!Schema::hasColumn('master_pencairan_mekar', 'previous_loan_id')) {
                $table->unsignedBigInteger('previous_loan_id')->nullable()->after('id');
                $table->index('previous_loan_id', 'mpm_previous_loan_id_idx');
            }
        });
    }

    public function down(): void
    {
        Schema::table('master_pencairan_mekar', function (Blueprint $table) {
            if (Schema::hasColumn('master_pencairan_mekar', 'previous_loan_id')) {
                $table->dropIndex('mpm_previous_loan_id_idx');
                $table->dropColumn('previous_loan_id');
            }
        });
    }
};
