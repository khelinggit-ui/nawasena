<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_pencairan_mekar', function (Blueprint $table) {
            // Status Tahap Workflow
            if (!Schema::hasColumn('master_pencairan_mekar', 'status_tahap')) {
                $table->enum('status_tahap', [
                    'pending_spv', 
                    'pending_adminkredit', 
                    'pending_analis', 
                    'pending_risk', 
                    'pending_mitra', 
                    'approved', 
                    'revisi', 
                    'reject'
                ])->default('pending_spv')->nullable();
            }

            // Tahap 1: Marketing
            $this->safeAddColumn($table, 'jenis_pekerjaan', 'string');
            $this->safeAddColumn($table, 'bidang_pekerjaan', 'string');
            $this->safeAddColumn($table, 'pengalaman_kerja_tahun', 'integer');
            $this->safeAddColumn($table, 'kode_kelurahan', 'string');
            $this->safeAddColumn($table, 'kode_kecamatan', 'string');
            $this->safeAddColumn($table, 'kode_kabupaten', 'string');
            $this->safeAddColumn($table, 'kode_provinsi', 'string');
            $this->safeAddColumn($table, 'privy_id', 'string');
            $this->safeAddColumn($table, 'nomor_npwp', 'string');

            // Tahap 2: Admin Kredit
            $this->safeAddColumn($table, 'tipe_peminjam', 'string');
            $this->safeAddColumn($table, 'nomor_anggota', 'string');
            $this->safeAddColumn($table, 'pengeluaran_per_bulan', 'decimal', 15, 2);
            $this->safeAddColumn($table, 'hutang_per_bulan', 'decimal', 15, 2);
            $this->safeAddColumn($table, 'pekerjaan_lain', 'string');
            $this->safeAddColumn($table, 'jenis_pekerjaan_lain', 'string');
            $this->safeAddColumn($table, 'bidang_pekerjaan_lain', 'string');
            $this->safeAddColumn($table, 'pengalaman_kerja_lain_tahun', 'integer');
            $this->safeAddColumn($table, 'pendapatan_lain_rupiah', 'decimal', 15, 2);
            $this->safeAddColumn($table, 'nama_perusahaan_lain', 'string');
            $this->safeAddColumn($table, 'jabatan_posisi_lain', 'string');
            $this->safeAddColumn($table, 'alamat_perusahaan_lain', 'text');

            // Tahap 3: Analis
            $this->safeAddColumn($table, 'bunga_persen', 'decimal', 5, 2);
            $this->safeAddColumn($table, 'suku_bunga', 'string');
            $this->safeAddColumn($table, 'tanggal_akad_kredit', 'date');
            $this->safeAddColumn($table, 'tanggal_angsuran_pertama_dibayar', 'date');

            // Tahap 3: Risk
            $this->safeAddColumn($table, 'status_pinjaman', 'string');
            $this->safeAddColumn($table, 'jenis_jaminan_agunan', 'string');
            $this->safeAddColumn($table, 'nilai_jaminan', 'decimal', 15, 2);
            $this->safeAddColumn($table, 'nomor_jaminan', 'string');
            $this->safeAddColumn($table, 'jenis_jaminan_2', 'string');
            $this->safeAddColumn($table, 'nilai_jaminan_2_rupiah', 'decimal', 15, 2);
            $this->safeAddColumn($table, 'nilai_tunggakan_rupiah', 'decimal', 15, 2);
            $this->safeAddColumn($table, 'nama_perwakilan_pic', 'string');
            $this->safeAddColumn($table, 'identitas_perwakilan_ktp', 'string');
            $this->safeAddColumn($table, 'nomor_legalitas', 'string');
            $this->safeAddColumn($table, 'total_aset_perusahaan', 'decimal', 15, 2);
            $this->safeAddColumn($table, 'jenis_badan_hukum', 'string');
            $this->safeAddColumn($table, 'nominal_asuransi_rupiah', 'decimal', 15, 2);
        });
    }

    private function safeAddColumn($table, $columnName, $type, $length = null, $places = null)
    {
        if (!Schema::hasColumn('master_pencairan_mekar', $columnName)) {
            if ($type == 'decimal') {
                $table->$type($columnName, $length, $places)->nullable();
            } elseif ($type == 'string') {
                $table->$type($columnName, 255)->nullable();
            } else {
                $table->$type($columnName)->nullable();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master_pencairan_mekar', function (Blueprint $table) {
            $table->dropColumn([
                'status_tahap',
                'jenis_pekerjaan', 'bidang_pekerjaan', 'pengalaman_kerja_tahun', 'kode_kelurahan',
                'kode_kecamatan', 'kode_kabupaten', 'kode_provinsi', 'privy_id', 'nomor_npwp',
                'tipe_peminjam', 'nomor_anggota', 'pengeluaran_per_bulan', 'hutang_per_bulan',
                'pekerjaan_lain', 'jenis_pekerjaan_lain', 'bidang_pekerjaan_lain',
                'pengalaman_kerja_lain_tahun', 'pendapatan_lain_rupiah', 'nama_perusahaan_lain',
                'jabatan_posisi_lain', 'alamat_perusahaan_lain', 'bunga_persen', 'suku_bunga',
                'tanggal_akad_kredit', 'tanggal_angsuran_pertama_dibayar', 'status_pinjaman',
                'jenis_jaminan_agunan', 'nilai_jaminan', 'nomor_jaminan', 'jenis_jaminan_2',
                'nilai_jaminan_2_rupiah', 'nilai_tunggakan_rupiah', 'nama_perwakilan_pic',
                'identitas_perwakilan_ktp', 'nomor_legalitas', 'total_aset_perusahaan',
                'jenis_badan_hukum', 'nominal_asuransi_rupiah'
            ]);
        });
    }
};
