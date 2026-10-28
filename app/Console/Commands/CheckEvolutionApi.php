<?php

namespace App\Console\Commands;

use App\Services\EvolutionApiService;
use Illuminate\Console\Command;
use Throwable;

class CheckEvolutionApi extends Command
{
    protected $signature = 'whatsapp:check-evolution';

    protected $description = 'Cek koneksi dan autentikasi ke server Evolution API';

    public function handle(EvolutionApiService $service): int
    {
        $url = (string) config('services.evolution.url');
        $key = (string) config('services.evolution.key');
        $configCached = app()->configurationIsCached();

        $this->line('URL          : '.($url !== '' ? $url : '(kosong)'));
        $this->line('API key      : '.$this->maskKey($key));
        $this->line('Config cache : '.($configCached ? 'AKTIF' : 'tidak aktif'));

        if (trim($url) === '' || trim($key) === '') {
            $this->error('EVOLUTION_API_URL / EVOLUTION_API_GLOBAL_KEY belum diisi di .env.');

            if ($configCached) {
                $this->warn('Config sedang di-cache. Jalankan: php artisan optimize:clear');
            }

            return self::FAILURE;
        }

        try {
            $response = $service->fetchInstances();
        } catch (Throwable $e) {
            $this->error('Tidak dapat terhubung ke server Evolution API: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->line('HTTP status  : '.$response->status());

        if ($response->successful()) {
            $this->info('Koneksi dan API key VALID.');

            return self::SUCCESS;
        }

        if (in_array($response->status(), [401, 403], true)) {
            $message = $response->json('message') ?? $response->body();
            $this->error('Autentikasi gagal ('.$response->status().'): '.$message);
            $this->warn('Samakan EVOLUTION_API_GLOBAL_KEY dengan AUTHENTICATION_API_KEY pada .env server Evolution API.');

            if ($configCached) {
                $this->warn('Config sedang di-cache. Jalankan: php artisan optimize:clear lalu config:cache ulang.');
            }

            return self::FAILURE;
        }

        $this->error('Respons tidak terduga: '.substr($response->body(), 0, 300));

        return self::FAILURE;
    }

    private function maskKey(string $key): string
    {
        $key = trim($key);

        if ($key === '') {
            return '(kosong)';
        }

        if (strlen($key) <= 4) {
            return str_repeat('*', strlen($key)).' (panjang '.strlen($key).')';
        }

        return substr($key, 0, 2)
            .str_repeat('*', strlen($key) - 4)
            .substr($key, -2)
            .' (panjang '.strlen($key).')';
    }
}
