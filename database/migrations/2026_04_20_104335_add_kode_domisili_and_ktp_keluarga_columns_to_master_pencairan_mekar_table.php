<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_pencairan_mekar', function (Blueprint $table) {
            // Domisili kode columns
            $table->string('kode_provinsi_domisili', 10)->nullable()->after('provinsi_domisili');
            $table->string('kode_kabupaten_domisili', 10)->nullable()->after('kota_kabupaten_domisili');
            $table->string('kode_kecamatan_domisili', 10)->nullable()->after('kecamatan_domisili');
            $table->string('kode_kelurahan_domisili', 15)->nullable()->after('kelurahan_domisili');

            // KTP Keluarga kode columns
            $table->string('kode_provinsi_ktp_keluarga', 10)->nullable()->after('provinsi_ktp_keluarga');
            $table->string('kode_kabupaten_ktp_keluarga', 10)->nullable()->after('kota_kabupaten_ktp_keluarga');
            $table->string('kode_kecamatan_ktp_keluarga', 10)->nullable()->after('kecamatan_ktp_keluarga');
            $table->string('kode_kelurahan_ktp_keluarga', 15)->nullable()->after('kelurahan_ktp_keluarga');
        });
    }

    public function down(): void
    {
        Schema::table('master_pencairan_mekar', function (Blueprint $table) {
            $table->dropColumn([
                'kode_provinsi_domisili',
                'kode_kabupaten_domisili',
                'kode_kecamatan_domisili',
                'kode_kelurahan_domisili',
                'kode_provinsi_ktp_keluarga',
                'kode_kabupaten_ktp_keluarga',
                'kode_kecamatan_ktp_keluarga',
                'kode_kelurahan_ktp_keluarga',
            ]);
        });
    }
};
