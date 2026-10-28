<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cabang;

class CabangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cabangs = [
            'CITRA RAYA 1',
            'CITRA RAYA 2',
            'CITY MARKET',
            'PURWAKARTA',
            'PURWOKERTO',
            'SERANG',
            'BEKASI',
            'PUSAT',
        ];

        foreach ($cabangs as $cabangName) {
            Cabang::updateOrCreate(
                ['nama_cabang' => $cabangName],
                [
                    'status' => 'Active',
                ]
            );
        }
    }
}
