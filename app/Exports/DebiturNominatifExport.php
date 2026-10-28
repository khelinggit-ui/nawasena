<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class DebiturNominatifExport extends DefaultValueBinder implements FromArray, WithHeadings, WithColumnFormatting, WithCustomValueBinder
{
    private const DEBITUR_FIELDS = [
        'Batch' => 'batch',
        'Tipe Peminjam' => 'tipe_peminjam',
        'Nama Debitur' => 'nama_peminjam',
        'Email' => 'email',
        'Nomor Handphone' => 'nomor_handphone',
        'Jenis Kelamin' => 'jenis_kelamin',
        'Tempat Lahir' => 'tempat_lahir',
        'Tanggal Lahir' => 'tanggal_lahir',
        'NIK' => 'nomor_ktp',
        'Nomor Debitur' => 'nomor_debitur',
        'Nama Ibu Kandung' => 'nama_ibu_kandung',
        'Agama' => 'agama',
        'Pendidikan' => 'pendidikan',
        'Status Perkawinan' => 'status_perkawinan',
        'Nomor Anggota' => 'nomor_anggota',
        'Nama Bank' => 'nama_bank',
        'Nama Pemilik Rekening' => 'nama_pemilik_rekening',
        'Nomor Rekening' => 'nomer_rekening',
        'Pekerjaan' => 'pekerjaan',
        'Jenis Pekerjaan' => 'jenis_pekerjaan',
        'Bidang Pekerjaan' => 'bidang_pekerjaan',
        'Pengalaman Kerja' => 'pengalaman_kerja_tahun',
        'Nama Perusahaan' => 'nama_perusahaan',
        'Jabatan Posisi' => 'jabatan_posisi',
        'Nama Keluarga Tidak Serumah' => 'nama_keluarga_tidak_serumah',
        'Hubungan Keluarga' => 'hubungan_keluarga',
        'No Handphone Keluarga' => 'no_handphone_keluarga',
        'Alamat KTP Keluarga' => 'alamat_ktp_keluarga',
        'Nominal Pinjaman' => 'nominal_pinjaman',
        'Tenor Bulan' => 'tenor_bulan',
        'Persentase Bunga' => 'bungapersen',
        'Suku Bunga' => 'suku_bunga',
        'Tanggal Akad Kredit' => 'tanggal_akad_kredit',
        'Tanggal Angsuran Pertama Dibayar' => 'tanggal_angsuran_pertama_dibayar',
        'Tanggal Jatuh Tempo' => 'tanggal_jatuh_tempo',
        'Nomor NUP' => 'nomor_nup',
        'Pengajuan Plafond' => 'pengajuan_plafond',
        'Nomor SPK' => 'nomor_spk',
    ];

    private const TEXT_FIELDS = [
        'nomor_handphone',
        'nomor_ktp',
        'nomor_debitur',
        'nomer_rekening',
        'no_handphone_keluarga',
        'nomor_nup',
        'nomor_spk',
    ];

    private array $textColumns;

    public function __construct(private Collection $rows)
    {
        $this->textColumns = [];
        foreach (array_keys(self::DEBITUR_FIELDS) as $index => $heading) {
            $field = self::DEBITUR_FIELDS[$heading];
            if (in_array($field, self::TEXT_FIELDS, true)) {
                $this->textColumns[] = Coordinate::stringFromColumnIndex($index + 1);
            }
        }
    }

    public function array(): array
    {
        return $this->rows->map(function (array $row): array {
            $debitur = $row['debitur'];
            $summary = $row['summary'];
            $nominalPinjaman = (float) ($debitur->nominal_pinjaman ?? 0);
            $tenor = max((int) ($debitur->tenor_bulan ?? 1), 1);
            $bungaPersen = (float) ($debitur->bungapersen ?? $debitur->bunga_persen ?? 0);
            $totalBunga = $nominalPinjaman * ($bungaPersen / 100) * $tenor;
            $data = [];

            foreach (self::DEBITUR_FIELDS as $heading => $field) {
                $value = $debitur->getAttribute($field);
                if ($field === 'nama_peminjam' && empty($value)) {
                    $value = $debitur->nama_debitur;
                } elseif ($field === 'pekerjaan' && empty($value)) {
                    $value = $debitur->pekerjaan_lain;
                } elseif ($field === 'bungapersen' && $value === null) {
                    $value = $debitur->bunga_persen;
                } elseif ($field === 'nomor_nup' && empty($value)) {
                    $value = $debitur->no_nup;
                }
                $data[] = $value;
            }

            return array_merge($data, [
                optional($debitur->marketing)->name,
                optional($debitur->spv)->name,
                optional($debitur->cabang)->nama_cabang,
                optional($debitur->kantor)->nama_kantor,
                $nominalPinjaman,
                $nominalPinjaman / $tenor,
                $nominalPinjaman * ($bungaPersen / 100),
                $totalBunga,
                $summary['total_jasa_terhitung'],
                $summary['total_denda'],
                $summary['remaining_balance'],
                max($summary['total_utang_berjalan'] - $summary['total_paid'] + $summary['total_denda'], 0),
                $summary['bulan_menunggak'],
                $summary['total_hari_telat'],
                $summary['total_paid'],
                'Aktif',
            ]);
        })->all();
    }

    public function headings(): array
    {
        return array_merge(array_keys(self::DEBITUR_FIELDS), [
            'Marketing',
            'SPV',
            'Cabang',
            'Unit',
            'Plafon',
            'Pokok per Bulan',
            'Bunga per Bulan',
            'Total Bunga',
            'Total Jasa',
            'Denda',
            'Outstanding Pokok',
            'Outstanding Total',
            'Jumlah Bulan Menunggak',
            'Jumlah Hari Telat',
            'Angsuran Masuk',
            'Status Pinjaman',
        ]);
    }

    public function columnFormats(): array
    {
        $formats = [];
        foreach ($this->textColumns as $column) {
            $formats[$column] = NumberFormat::FORMAT_TEXT;
        }

        return $formats;
    }

    public function bindValue(Cell $cell, $value): bool
    {
        if (in_array($cell->getColumn(), $this->textColumns, true)) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }
}
