<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterPencairanMekar;
use App\Models\Cabang;

class MasterPencairanMekarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Path to CSV file - you can store it in storage/app/csv/ or any accessible location
        $csvPath = storage_path('app/csv/MASTER PENCAIRAN MEKAR - Copy of Master.csv');
        
        if (!file_exists($csvPath)) {
            $this->command->error("CSV file not found at: {$csvPath}");
            $this->command->info("Please place the CSV file at: storage/app/csv/MASTER PENCAIRAN MEKAR - Copy of Master.csv");
            return;
        }

        // Read CSV file with proper encoding
        $file = fopen($csvPath, 'r');
        if (!$file) {
            $this->command->error("Cannot open CSV file");
            return;
        }

        // Read header row
        $headers = fgetcsv($file, 5000);
        if (!$headers) {
            $this->command->error("CSV file is empty or invalid");
            fclose($file);
            return;
        }

        // Create header mapping
        $headerMap = array_flip($headers);
        
        $count = 0;
        $errors = 0;

        // Read data rows
        while (($row = fgetcsv($file, 5000)) !== false) {
            if (count($row) < count($headers)) {
                $row = array_pad($row, count($headers), null);
            }

            // Map CSV columns to variables
            $rowData = array_combine($headers, $row);

            // Extract data
            $nik = $this->getValue($rowData, 'NOMOR KTP');
            $namaPeminjam = $this->getValue($rowData, 'NAMA PEMINJAM');
            $cabangNama = $this->getValue($rowData, 'CABANG');
            
            // Skip if no name
            if (empty($namaPeminjam)) {
                continue;
            }

            // Get cabang_id from cabang name
            $cabangId = null;
            if (!empty($cabangNama)) {
                $cabang = Cabang::where('nama_cabang', $cabangNama)->first();
                $cabangId = $cabang ? $cabang->id : null;
            }

            // Parse date in format YYYY-MM-DD
            $tanggalAkad = $this->parseDate($this->getValue($rowData, 'TANGGAL AKAD KREDIT'));

            $data = [
                'no_kontrak' => $this->getValue($rowData, 'NOMOR PINJAMAN'),
                'nama_debitur' => $namaPeminjam,
                'nik' => $nik,
                'email' => $this->getValue($rowData, 'EMAIL'),
                'nomor_hp' => $this->getValue($rowData, 'NOMOR HANDPHONE'),
                'nomor_identitas' => $nik,
                'alamat' => $this->getValue($rowData, 'ALAMAT (KTP)'),
                'kota' => $this->getValue($rowData, 'KOTA/KABUPATEN (KTP)'),
                'provinsi' => $this->getValue($rowData, 'PROVINSI (KTP)'),
                'status' => 'active',
                'nominal_pinjaman' => $this->parseNominal($this->getValue($rowData, 'NOMINAL PINJAMAN')),
                'tenor_bulan' => $this->parseInteger($this->getValue($rowData, 'TENOR (BULAN)')),
                'marketing' => $this->getValue($rowData, 'MARKETING'),
                'cabang' => $cabangNama,
                'cabang_id' => $cabangId,
                'perusahaan' => $this->getValue($rowData, 'NAMA PERUSAHAAN'),
                'tanggal_akad_kredit' => $tanggalAkad,
            ];

            try {
                // Use updateOrCreate to avoid duplicate key errors
                MasterPencairanMekar::updateOrCreate(
                    ['nik' => $data['nik']],
                    $data
                );
                $count++;
                
                if ($count % 10 == 0) {
                    $this->command->info("Processed {$count} records...");
                }
            } catch (\Exception $e) {
                $errors++;
                $this->command->warn("Error processing: {$namaPeminjam} - " . $e->getMessage());
            }
        }

        fclose($file);

        $this->command->info("✓ Successfully imported/updated {$count} records.");
        if ($errors > 0) {
            $this->command->warn("⚠ {$errors} errors encountered during import.");
        }
    }

    /**
     * Get value from row array, handling null/empty values
     */
    private function getValue($row, $key)
    {
        if (!isset($row[$key])) {
            return null;
        }
        
        $value = trim($row[$key]);
        return empty($value) ? null : $value;
    }

    /**
     * Parse numeric value
     */
    private function parseNominal($value)
    {
        if (empty($value)) {
            return 0;
        }

        // Remove dots (thousand separator) and replace comma with dot (decimal)
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);
        
        return (float)$value;
    }

    /**
     * Parse integer value
     */
    private function parseInteger($value)
    {
        if (empty($value)) {
            return null;
        }
        
        return (int)$value;
    }

    /**
     * Parse date value (accepts YYYY-MM-DD format)
     */
    private function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

        $value = trim($value);
        
        try {
            // Already in Y-m-d format
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                return $value;
            }
            
            // Try parsing with strtotime
            $timestamp = strtotime($value);
            return $timestamp ? date('Y-m-d', $timestamp) : null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
