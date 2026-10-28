<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ParameterProduk;

class ParameterProdukSeeder extends Seeder
{
    public function run(): void
    {
        ParameterProduk::create([
            'nama_produk' => 'Kredit Usaha Rakyat (KUR)',
            'tenor_bulan' => 36,
            'bunga_persen' => 6.00,
            'suku_bunga' => 'Flat',
        ]);

        ParameterProduk::create([
            'nama_produk' => 'Kredit Kendaraan Bermotor (KKB)',
            'tenor_bulan' => 60,
            'bunga_persen' => 8.50,
            'suku_bunga' => 'Flat',
        ]);

        ParameterProduk::create([
            'nama_produk' => 'Kredit Pemilikan Rumah (KPR)',
            'tenor_bulan' => 120,
            'bunga_persen' => 10.00,
            'suku_bunga' => 'Efektif',
        ]);
        
        ParameterProduk::create([
            'nama_produk' => 'Kredit Multiguna',
            'tenor_bulan' => 48,
            'bunga_persen' => 12.00,
            'suku_bunga' => 'Flat',
        ]);
    }
}
