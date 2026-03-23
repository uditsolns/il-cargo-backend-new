<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class APIClub
{
    const SERVICE = 'APIClub';
    const RC = "/rc_info";
    const DL = "/fetch_dl";
    const SEND_OTP = "/aadhaar_v2/send_otp";
    const SUBMIT_OTP = "/aadhaar_v2/submit_otp";

    public static function sendRequest(string $endpoint, array $data): ?JsonResponse
    {
        $logger      = app(ApiUsageLogger::class);
        $requestedAt = Carbon::now();
        $startTime   = hrtime(true); // nanosecond precision

        try {
            $client = new Client();
            $response = $client->request('POST', config("apiClub.base_url") . $endpoint, [
                'body' => json_encode($data),  // Encode data properly to JSON format
                'headers' => [
                    'Referer' => 'docs.apiclub.in',
                    'accept' => 'application/json',
                    'content-type' => 'application/json',
                    'x-api-key' => config("apiClub.api_key"),
                ],
            ]);

            $latencyMs    = self::elapsedMs((int) $startTime);
            $responseBody = json_decode($response->getBody(), true);

            // Log::info('APIClub response', ['endpoint' => $endpoint, 'body' => $responseBody]);

            $logger->log(
                self::SERVICE,
                $endpoint,
                $data,
                $response->getStatusCode(),
                true,
                self::summarize($responseBody),
                $latencyMs,
                $requestedAt,
            );

            return response()->json($responseBody, $responseBody['code'] ?? $response->getStatusCode());

        } catch (ClientException $e) {
            $latencyMs    = self::elapsedMs((int) $startTime);
            $responseBody = json_decode($e->getResponse()->getBody(), true);

            Log::warning('APIClub client error', ['endpoint' => $endpoint, 'error' => $responseBody]);

            $logger->log(
                self::SERVICE,
                $endpoint,
                $data,
                $e->getCode(),
                false,
                self::summarize($responseBody),
                $latencyMs,
                $requestedAt,
            );

            return response()->json($responseBody, $e->getCode());

        } catch (GuzzleException $e) {
            $latencyMs = self::elapsedMs((int) $startTime);

            Log::error('APIClub guzzle error', ['endpoint' => $endpoint, 'message' => $e->getMessage()]);

            $logger->log(
                self::SERVICE,
                $endpoint,
                $data,
                $e->getCode() ?: 503,
                false,
                ['error' => $e->getMessage()],
                $latencyMs,
                $requestedAt,
            );

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 503);

        } catch (\Exception $e) {
            $latencyMs = self::elapsedMs((int) $startTime);

            Log::error('APIClub unexpected error', ['endpoint' => $endpoint, 'message' => $e->getMessage()]);

            $logger->log(
                self::SERVICE,
                $endpoint,
                $data,
                500,
                false,
                ['error' => $e->getMessage()],
                $latencyMs,
                $requestedAt,
            );

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Convert hrtime nanoseconds to milliseconds.
     */
    private static function elapsedMs(int $startTime): int
    {
        return (int) ((hrtime(true) - $startTime) / 1000000);
    }

    /**
     * Store only the fields we care about from the response —
     * not the full payload — to keep storage lean.
     */
    private static function summarize(?array $responseBody): array
    {
        if (empty($responseBody)) {
            return [];
        }

        return array_filter([
            'code'    => $responseBody['code'] ?? null,
            'status'  => $responseBody['status'] ?? null,
            'message' => $responseBody['message'] ?? null,
            'request_id' => $responseBody['request_id'] ?? null,
        ]);
    }
}
