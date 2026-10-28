<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterPencairanMekar;
use Carbon\Carbon;

class BackfillNomorDebiturSeeder extends Seeder
{
    /**
     * Generate nomor_debitur untuk data debitur yang sudah ada.
     *
     * Format: Deb-[tgl_create][tahun_create][nomor_urut]
     * Contoh: Deb-23102026001
     */
    public function run(): void
    {
        $sequenceCache = [];

        MasterPencairanMekar::query()
            ->orderBy('id')
            ->chunkById(100, function ($debiturs) use (&$sequenceCache) {
                foreach ($debiturs as $debitur) {
                    $createdAt = $debitur->created_at
                        ? Carbon::parse($debitur->created_at)
                        : Carbon::now();

                    $prefix = 'Deb-' . $createdAt->format('dmY');

                    if (!array_key_exists($prefix, $sequenceCache)) {
                        $last = MasterPencairanMekar::query()
                            ->where('nomor_debitur', 'like', $prefix . '%')
                            ->orderBy('nomor_debitur', 'desc')
                            ->value('nomor_debitur');

                        $sequenceCache[$prefix] = $last
                            ? (int) substr($last, strlen($prefix))
                            : 0;
                    }

                    if (empty($debitur->nomor_debitur)) {
                        $sequenceCache[$prefix]++;
                        $debitur->nomor_debitur = $prefix . str_pad($sequenceCache[$prefix], 3, '0', STR_PAD_LEFT);
                        $debitur->saveQuietly();
                    } else {
                        $existingNumber = (int) substr($debitur->nomor_debitur, strlen($prefix));
                        $sequenceCache[$prefix] = max($sequenceCache[$prefix], $existingNumber);
                    }
                }
            });
    }
}
