<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Calls the sibling ulip-apis proxy for container/FASTag data (see
 * docs/adr/0003-ulip-container-fastag-polling.md). Deliberately uses
 * Laravel's Http facade rather than raw Guzzle (unlike APIClub) so calls
 * can be intercepted with Http::fake() - APIClub's `new Client()` cannot be.
 */
class UlipProxyClient
{
    public function fetchFastag(string $vehicleNumber): ?array
    {
        return $this->call('fastag', ['vehicle_number' => $vehicleNumber]);
    }

    public function fetchContainer(string $containerNumber): ?array
    {
        return $this->call('container', ['container_number' => $containerNumber]);
    }

    private function call(string $endpoint, array $payload): ?array
    {
        $baseUrl = rtrim(config('services.ulip_proxy.base_url'), '/');

        $response = Http::asJson()
            ->timeout(15)
            ->post("{$baseUrl}/{$endpoint}", $payload);

        if (!$response->successful() || $response->json('success') !== true) {
            Log::warning('ulip_proxy call failed', [
                'endpoint' => $endpoint,
                'payload' => $payload,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        return $response->json('data');
    }
}
