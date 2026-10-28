<?php

namespace App\Exports;

use App\Models\MasterPencairanMekar;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class DebiturMitraExport implements FromCollection, WithHeadings, WithColumnFormatting
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return MasterPencairanMekar::where('status_tahap', 'pending_mitra')
            ->get()
            ->map(function ($debitur) {
                return [
                    'tipe_peminjam' => $debitur->tipe_peminjam ?? '',
                    'nama_peminjam' => $debitur->nama_peminjam ?? '',
                    'email' => $debitur->email ?? '',
                    'nomor_handphone' => $debitur->nomor_handphone ?? '',
                    'jenis_kelamin' => $debitur->jenis_kelamin ?? '',
                    'tempat_lahir' => $debitur->tempat_lahir ?? '',
                    'tanggal_lahir' => $debitur->tanggal_lahir ?? '',
                    'nomor_ktp' => $debitur->nomor_ktp ?? '',
                    'nama_ibu_kandung' => $debitur->nama_ibu_kandung ?? '',
                    'agama' => $debitur->agama ?? '',
                    'pendidikan' => $debitur->pendidikan ?? '',
                    'status_perkawinan' => $debitur->status_perkawinan ?? '',
                    'nomor_anggota' => $debitur->nomor_anggota ?? '',
                    'nama_bank' => $debitur->nama_bank ?? '',
                    'nama_pemilik_rekening' => $debitur->nama_pemilik_rekening ?? '',
                    'nomer_rekening' => $debitur->nomer_rekening ?? '',
                    'jenis_pekerjaan' => $debitur->jenis_pekerjaan ?? '',
                    'pekerjaan_offline' => '', // Placeholder untuk PEKERJAAN Offline
                    'bidang_pekerjaan' => $debitur->bidang_pekerjaan ?? '',
                    'pengalaman_kerja_tahun' => $debitur->pengalaman_kerjatahun ?? '',
                    'nama_perusahaan' => $debitur->nama_perusahaan ?? '',
                    'jabatan_posisi' => $debitur->jabatan_posisi ?? '',
                    'alamat_perusahaan' => $debitur->alamat_perusahaan ?? '',
                    'pendapatan_per_bulan' => $debitur->pendapatan_per_bulan ?? 0,
                    'pengeluaran_per_bulan' => $debitur->pengeluaran_per_bulan ?? 0,
                    'hutang_per_bulan' => $debitur->hutang_per_bulan ?? 0,
                    'pekerjaan_lain' => $debitur->pekerjaan_lain ?? '',
                    'jenis_pekerjaan_lain' => $debitur->jenis_pekerjaan_lain ?? '',
                    'bidang_pekerjaan_lain' => $debitur->bidang_pekerjaan_lain ?? '',
                    'pengalaman_kerja_lain_tahun' => $debitur->pengalaman_kerja_lain_tahun ?? '',
                    'pendapatan_lain_rupiah' => $debitur->pendapatan_lain_rupiah ?? 0,
                    'nama_perusahaan_lain' => $debitur->nama_perusahaan_lain ?? '',
                    'jabatan_posisi_lain' => $debitur->jabatan_posisi_lain ?? '',
                    'alamat_perusahaan_lain' => $debitur->alamat_perusahaan_lain ?? '',
                    'alamat_ktp' => $debitur->alamat_ktp ?? '',
                    'rt_ktp' => $debitur->rt_ktp ?? '',
                    'rw_ktp' => $debitur->rw_ktp ?? '',
                    'kelurahan_ktp' => $debitur->kelurahan_ktp ?? '',
                    'kecamatan_ktp' => $debitur->kecamatan_ktp ?? '',
                    'kota_kabupaten_ktp' => $debitur->kota_kabupaten_ktp ?? '',
                    'provinsi_ktp' => $debitur->provinsi_ktp ?? '',
                    'kode_pos_ktp' => $debitur->kode_pos_ktp ?? '',
                    'alamat_domisili' => $debitur->alamat_domisili ?? '',
                    'rt_domisili' => $debitur->rt_domisili ?? '',
                    'rw_domisili' => $debitur->rw_domisili ?? '',
                    'kelurahan_domisili' => $debitur->kelurahan_domisili ?? '',
                    'kecamatan_domisili' => $debitur->kecamatan_domisili ?? '',
                    'kota_kabupaten_domisili' => $debitur->kota_kabupaten_domisili ?? '',
                    'provinsi_domisili' => $debitur->provinsi_domisili ?? '',
                    'kode_pos_domisili' => $debitur->kode_pos_domisili ?? '',
                    'status_kepemilikan_rumah' => $debitur->status_kepemilikan_rumah ?? '',
                    'nama_keluarga_tidak_serumah' => $debitur->nama_keluarga_tidak_serumah ?? '',
                    'hubungan_keluarga' => $debitur->hubungan_keluarga ?? '',
                    'no_handphone_keluarga' => $debitur->no_handphone_keluarga ?? '',
                    'alamat_ktp_keluarga' => $debitur->alamat_ktp_keluarga ?? '',
                    'provinsi_ktp_keluarga' => $debitur->provinsi_ktp_keluarga ?? '',
                    'kota_kabupaten_ktp_keluarga' => $debitur->kota_kabupaten_ktp_keluarga ?? '',
                    'kecamatan_ktp_keluarga' => $debitur->kecamatan_ktp_keluarga ?? '',
                    'kelurahan_ktp_keluarga' => $debitur->kelurahan_ktp_keluarga ?? '',
                    'kode_pos_ktp_keluarga' => $debitur->kode_pos_ktp_keluarga ?? '',
                    'nomor_debitur' => $debitur->nomor_debitur ?? '',
                    'nominal_pinjaman' => $debitur->nominal_pinjaman ?? 0,
                    'tenor_bulan' => $debitur->tenor_bulan ?? '',
                    'bunga_persen' => $debitur->bunga_persen ?? 0,
                    'suku_bunga' => $debitur->suku_bunga ?? '',
                    'tanggal_akad_kredit' => $debitur->tanggal_akad_kredit ?? '',
                    'tanggal_angsuran_pertama_dibayar' => $debitur->tanggal_angsuran_pertama_dibayar ?? '',
                    'tanggal_jatuh_tempo' => $debitur->tanggal_jatuh_tempo ?? '',
                    'jenis_pinjaman' => '', // Placeholder
                    'tujuan_pinjaman' => '', // Placeholder
                    'sektor_tujuan_pinjaman' => '', // Placeholder
                    'metode_angsuran' => $debitur->metode_angsuran ?? 'MONTHLY',
                    'status_pinjaman' => $debitur->status_pinjaman ?? 'SPECIAL',
                    'jenis_jaminan_agunan' => $debitur->jenis_jaminan_agunan ?? 'NONE',
                    'nilai_jaminan' => $debitur->nilai_jaminan ?? 0,
                    'nomor_jaminan' => $debitur->nomor_jaminan ?? '',
                    'jenis_jaminan_2' => $debitur->jenis_jaminan_2 ?? '',
                    'nilai_jaminan_2_rupiah' => $debitur->nilai_jaminan_2_rupiah ?? 0,
                    'nilai_tunggakan_rupiah' => $debitur->nilai_tunggakan_rupiah ?? 0,
                    'nama_perwakilan_pic' => $debitur->nama_perwakilan_pic ?? '',
                    'identitas_perwakilan_ktp' => $debitur->identitas_perwakilan_ktp ?? '',
                    'nomor_npwp' => $debitur->nomor_npwp ?? '',
                    'nomor_legalitas' => $debitur->nomor_legalitas ?? '',
                    'total_aset_perusahaan' => $debitur->total_aset_perusahaan ?? 0,
                    'jenis_badan_hukum' => $debitur->jenis_badan_hukum ?? '',
                    'nominal_asuransi_rupiah' => $debitur->nominal_asuransi_rupiah ?? 0,
                    'privy_id' => $debitur->privy_id ?? '',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'TIPE PEMINJAM',
            'NAMA PEMINJAM',
            'EMAIL',
            'NOMOR HANDPHONE',
            'JENIS KELAMIN',
            'TEMPAT LAHIR',
            'TANGGAL LAHIR',
            'NOMOR KTP',
            'NAMA IBU KANDUNG',
            'AGAMA',
            'PENDIDIKAN',
            'STATUS PERKAWINAN',
            'NOMOR ANGGOTA',
            'NAMA BANK',
            'NAMA PEMILIK REKENING',
            'NOMER REKENING',
            'PEKERJAAN',
            'Offline',
            'BIDANG PEKERJAAN',
            'PENGALAMAN KERJA(TAHUN)',
            'NAMA PERUSAHAAN',
            'JABATAN/POSISI',
            'ALAMAT PERUSAHAAN',
            'PENDAPATAN PER BULAN (RUPIAH)',
            'PENGELUARAN PER BULAN (RUPIAH)',
            'HUTANG PER BULAN (RUPIAH)',
            'PEKERJAAN LAIN',
            'JENIS PEKERJAAN LAIN',
            'BIDANG PEKERJAAN LAIN',
            'PENGALAMAN KERJA LAIN(TAHUN)',
            'PENDAPATAN LAIN (RUPIAH)',
            'NAMA PERUSAHAAN LAIN',
            'JABATAN/POSISI LAIN',
            'ALAMAT PERUSAHAAN LAIN',
            'ALAMAT (KTP)',
            'RT (KTP)',
            'RW (KTP)',
            'KELURAHAN (KTP)',
            'KECAMATAN (KTP)',
            'KOTA/KABUPATEN (KTP)',
            'PROVINSI (KTP)',
            'KODE POS (KTP)',
            'ALAMAT (DOMISILI)',
            'RT (DOMISILI)',
            'RW (DOMISILI)',
            'KELURAHAN (DOMISILI)',
            'KECAMATAN (DOMISILI)',
            'KOTA/KABUPATEN (DOMISILI)',
            'PROVINSI (DOMISILI)',
            'KODE POS (DOMISILI)',
            'STATUS KEPEMILIKAN RUMAH',
            'NAMA KELUARGA (TIDAK SERUMAH)',
            'HUBUNGAN KELUARGA',
            'NO. HANDPHONE KELUARGA',
            'ALAMAT (KTP KELUARGA)',
            'PROVINSI (KTP KELUARGA)',
            'KOTA/KABUPATEN (KTP KELUARGA)',
            'KECAMATAN (KTP KELUARGA)',
            'KELURAHAN (KTP KELUARGA)',
            'KODE POS (KTP KELUARGA)',
            'NOMOR DEBITUR',
            'NOMINAL PINJAMAN (RUPIAH)',
            'TENOR (BULAN)',
            'BUNGA(%)',
            'SUKU BUNGA',
            'TANGGAL AKAD KREDIT',
            'TANGGAL ANGSURAN PERTAMA DIBAYAR',
            'TANGGAL JATUH TEMPO',
            'JENIS PINJAMAN',
            'TUJUAN PINJAMAN',
            'SEKTOR TUJUAN PINJAMAN',
            'METODE ANGSURAN',
            'STATUS PINJAMAN',
            'JENIS JAMINAN/AGUNAN',
            'NILAI JAMINAN',
            'NOMOR JAMINAN',
            'JENIS JAMINAN 2',
            'NILAI JAMINAN 2 (RUPIAH)',
            'NILAI TUNGGAKAN (RUPIAH)',
            'NAMA PERWAKILAN (PIC)',
            'IDENTITAS PERWAKILAN (KTP)',
            'NOMOR NPWP (KHUSUS INVOICE FINANCING)',
            'NOMOR LEGALITAS',
            'TOTAL ASET PERUSAHAAN',
            'JENIS BADAN HUKUM',
            'NOMINAL ASURANSI (RUPIAH)',
            'PRIVY ID',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'H' => '@',                              // NOMOR KTP (Text format)
            'X' => NumberFormat::FORMAT_NUMBER,      // PENDAPATAN PER BULAN (RUPIAH)
            'Y' => NumberFormat::FORMAT_NUMBER,      // PENGELUARAN PER BULAN (RUPIAH)
            'Z' => NumberFormat::FORMAT_NUMBER,      // HUTANG PER BULAN (RUPIAH)
        ];
    }
}
