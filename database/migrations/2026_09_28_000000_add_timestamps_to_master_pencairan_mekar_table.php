<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_pencairan_mekar', function (Blueprint $table) {
            if (!Schema::hasColumn('master_pencairan_mekar', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }

            if (!Schema::hasColumn('master_pencairan_mekar', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });

        DB::table('debitur_histories')
            ->select('debitur_id')
            ->selectRaw('MIN(created_at) as first_created_at, MAX(created_at) as last_updated_at')
            ->groupBy('debitur_id')
            ->orderBy('debitur_id')
            ->chunk(500, function ($histories): void {
                foreach ($histories as $history) {
                    DB::table('master_pencairan_mekar')
                        ->where('id', $history->debitur_id)
                        ->update([
                            'created_at' => $history->first_created_at,
                            'updated_at' => $history->last_updated_at,
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('master_pencairan_mekar', function (Blueprint $table) {
            if (Schema::hasColumn('master_pencairan_mekar', 'updated_at')) {
                $table->dropColumn('updated_at');
            }

            if (Schema::hasColumn('master_pencairan_mekar', 'created_at')) {
                $table->dropColumn('created_at');
            }
        });
    }
};