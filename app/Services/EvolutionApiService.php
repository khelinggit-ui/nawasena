<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class EvolutionApiService
{
    protected string $baseUrl;

    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(trim((string) config('services.evolution.url')), '/');
        $this->apiKey = trim((string) config('services.evolution.key'));
    }

    protected function client(): PendingRequest
    {
        return Http::withHeaders(['apikey' => $this->apiKey])
            ->acceptJson()
            ->timeout(15);
    }

    public function createInstance(string $instanceName): Response
    {
        return $this->client()->post("{$this->baseUrl}/instance/create", [
            'instanceName' => $instanceName,
            'integration' => 'WHATSAPP-BAILEYS',
            'qrcode' => true,
        ]);
    }

    public function fetchInstances(): Response
    {
        return $this->client()->get("{$this->baseUrl}/instance/fetchInstances");
    }

    public function connectInstance(string $instanceName): Response
    {
        return $this->client()->get("{$this->baseUrl}/instance/connect/{$instanceName}");
    }

    public function getConnectionState(string $instanceName): Response
    {
        return $this->client()->get("{$this->baseUrl}/instance/connectionState/{$instanceName}");
    }

    public function sendText(string $instanceName, string $number, string $text): Response
    {
        return $this->client()->post("{$this->baseUrl}/message/sendText/{$instanceName}", [
            'number' => $number,
            'text' => $text,
        ]);
    }

    public function logoutInstance(string $instanceName): Response
    {
        return $this->client()->delete("{$this->baseUrl}/instance/logout/{$instanceName}");
    }

    public function deleteInstance(string $instanceName): Response
    {
        return $this->client()->delete("{$this->baseUrl}/instance/delete/{$instanceName}");
    }

    public function findWebhook(string $instanceName): Response
    {
        return $this->client()->get("{$this->baseUrl}/webhook/find/{$instanceName}");
    }

    public function setWebhook(string $instanceName, array $webhook): Response
    {
        return $this->client()->post("{$this->baseUrl}/webhook/set/{$instanceName}", [
            'webhook' => $webhook,
        ]);
    }
}
