<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'kantor_id')) {
                $table->unsignedBigInteger('kantor_id')->nullable()->after('cabang_id');
            }
        });

        Schema::table('master_pencairan_mekar', function (Blueprint $table) {
            if (!Schema::hasColumn('master_pencairan_mekar', 'kantor_id')) {
                $table->unsignedBigInteger('kantor_id')->nullable()->after('cabang_id');
            }
        });

        $kantorByUnit = DB::table('kantors')->pluck('id', 'unit');

        if (Schema::hasColumn('users', 'unit')) {
            DB::table('users')->select('id', 'unit')->whereNotNull('unit')->orderBy('id')->chunkById(100, function ($users) use ($kantorByUnit) {
                foreach ($users as $user) {
                    $kantorId = $kantorByUnit[$user->unit] ?? null;
                    if ($kantorId) {
                        DB::table('users')->where('id', $user->id)->update(['kantor_id' => $kantorId]);
                    }
                }
            });
        }

        if (Schema::hasColumn('master_pencairan_mekar', 'unit')) {
            DB::table('master_pencairan_mekar')->select('id', 'unit')->whereNotNull('unit')->orderBy('id')->chunkById(100, function ($debiturs) use ($kantorByUnit) {
                foreach ($debiturs as $debitur) {
                    $kantorId = $kantorByUnit[$debitur->unit] ?? null;
                    if ($kantorId) {
                        DB::table('master_pencairan_mekar')->where('id', $debitur->id)->update(['kantor_id' => $kantorId]);
                    }
                }
            });
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'unit')) {
                $table->dropColumn('unit');
            }
        });

        Schema::table('master_pencairan_mekar', function (Blueprint $table) {
            if (Schema::hasColumn('master_pencairan_mekar', 'unit')) {
                $table->dropColumn('unit');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'unit')) {
                $table->string('unit')->nullable()->after('cabang_id');
            }
        });

        Schema::table('master_pencairan_mekar', function (Blueprint $table) {
            if (!Schema::hasColumn('master_pencairan_mekar', 'unit')) {
                $table->string('unit')->nullable()->after('cabang_id');
            }
        });

        $kantorData = DB::table('kantors')->select('id', 'unit')->get()->keyBy('id');

        DB::table('users')->select('id', 'kantor_id')->whereNotNull('kantor_id')->orderBy('id')->chunkById(100, function ($users) use ($kantorData) {
            foreach ($users as $user) {
                $unit = $kantorData[$user->kantor_id]->unit ?? null;
                if ($unit) {
                    DB::table('users')->where('id', $user->id)->update(['unit' => $unit]);
                }
            }
        });

        DB::table('master_pencairan_mekar')->select('id', 'kantor_id')->whereNotNull('kantor_id')->orderBy('id')->chunkById(100, function ($debiturs) use ($kantorData) {
            foreach ($debiturs as $debitur) {
                $unit = $kantorData[$debitur->kantor_id]->unit ?? null;
                if ($unit) {
                    DB::table('master_pencairan_mekar')->where('id', $debitur->id)->update(['unit' => $unit]);
                }
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'kantor_id')) {
                $table->dropColumn('kantor_id');
            }
        });

        Schema::table('master_pencairan_mekar', function (Blueprint $table) {
            if (Schema::hasColumn('master_pencairan_mekar', 'kantor_id')) {
                $table->dropColumn('kantor_id');
            }
        });
    }
};
