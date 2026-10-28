<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use Illuminate\Database\Seeder;

class JabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jabatans = [
            'SPV',
            'Marketing',
            'Admin Kredit',
            'Analis',
            'Kadiv',
            'Pengelola',
            'Mitra',
        ];

        foreach ($jabatans as $nama) {
            Jabatan::firstOrCreate(['nama_jabatan' => $nama]);
        }
    }
}
