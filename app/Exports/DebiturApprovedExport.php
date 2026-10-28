<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class DebiturApprovedExport extends DefaultValueBinder implements FromArray, WithHeadings, WithColumnFormatting, WithCustomValueBinder
{
    public function __construct(private array $debiturs)
    {
    }

    public function array(): array
    {
        return array_map(function (array $debitur, int $index): array {
            return [
                $index + 1,
                $debitur['nama_peminjam'] ?? '',
                (string) ($debitur['nomor_ktp'] ?? ''),
                $debitur['nomor_debitur'] ?? '',
                $this->formatDate($debitur['created_at'] ?? null),
                $this->formatDate($debitur['updated_at'] ?? null),
                $debitur['batch'] ?? '',
                $debitur['marketing'] ?? '',
                $debitur['nama_cabang'] ?? '',
                $debitur['unit'] ?? '',
                $debitur['email'] ?? '',
                $debitur['nomor_handphone'] ?? '',
                $debitur['tenor_bulan'] ?? '',
                $debitur['spv'] ?? '',
                $debitur['nominal_pinjaman'] ?? 0,
                $debitur['status_tahap'] ?? '',
                ($debitur['status_pinjaman'] ?? 'aktif') === 'lunas' ? 'Lunas' : 'Aktif',
            ];
        }, $this->debiturs, array_keys($this->debiturs));
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Debitur',
            'NIK',
            'Nomor Debitur',
            'Tanggal Dibuat',
            'Tanggal Update',
            'Batch',
            'Marketing',
            'Cabang',
            'Unit',
            'Email',
            'Handphone',
            'Tenor',
            'SPV',
            'Nominal Pinjaman',
            'Status Approval',
            'Status Pinjaman',
        ];
    }

    public function columnFormats(): array
    {
        return ['C' => NumberFormat::FORMAT_TEXT];
    }

    public function bindValue(Cell $cell, $value): bool
    {
        if ($cell->getColumn() === 'C') {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    private function formatDate(?string $value): string
    {
        return $value ? Carbon::parse($value)->format('d-m-Y') : '';
    }
}