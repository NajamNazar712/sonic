<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Client\PendingRequest;

class OneLinkService
{
    /** @var string */
    protected $baseUrl;            // e.g. https://onelinktunnel.sonic.pk/ol
    protected $clientId;
    protected $clientSecret;
    protected $scope;
    protected $verifySsl;          // bool
    protected $timeout;            // seconds
    protected $retries;            // int attempts
    protected $retryDelayMs;       // milliseconds
    protected $token;

    public function __construct()
    {
        // Point to the TUNNEL base so requests ride the IPsec path.
        // For prod, just switch env to ONE_LINK_URL_PROD (same shape).
        $this->baseUrl      = env('ONE_LINK_BASE_URL', env('ONE_LINK_URL_SANDBOX_M', 'https://onelinktunnel.sonic.pk/ol/onelink/production'));
        $this->clientId     = env('ONE_LINK_CLIENT_ID', env('ONE_LINK_CLIENT_ID_SANDBOX_M','7e1320f3627079431658e4dfacab4056'));
        $this->clientSecret = env('ONE_LINK_SECRET',    env('ONE_LINK_SECRET_SANDBOX_M','ed02b938add93a952c326560faa6a84a'));
        $this->scope        = env('ONE_LINK_SCOPE', '1LinkApi');

        // Networking / resiliency
        $this->verifySsl    = (bool) env('ONE_LINK_VERIFY_SSL', false); // false is OK for pilot through tunnel
        $this->timeout      = (int) env('ONE_LINK_TIMEOUT', 15);        // seconds
        $this->retries      = (int) env('ONE_LINK_RETRIES', 2);
        $this->retryDelayMs = (int) env('ONE_LINK_RETRY_DELAY_MS', 300);

        $this->token        = $this->getAccessToken();
    }

    /**
     * Build a preconfigured HTTP client.
     */
    protected function http(): PendingRequest
    {
        // baseUrl/timeout/retry are first-class on Laravel's HTTP client
        // (see docs). TLS verify can be toggled as needed during pilot.
        return Http::baseUrl($this->baseUrl)     // e.g. https://onelinktunnel.sonic.pk/ol
        ->timeout($this->timeout)           // seconds
        ->retry($this->retries, $this->retryDelayMs) // attempts, delay(ms)
        ->withOptions(['verify' => $this->verifySsl]); // trust on pilot or set CA later
    }

    public function logRequest($endpoint, $requestData = null, $responseData = null, $status = 'pending')
    {
        return DB::table('one_link_api_logs')->insertGetId([
            'endpoint'      => $endpoint,
            'request_data'  => $requestData ? json_encode($requestData) : null,
            'response_data' => is_array($responseData) ? json_encode($responseData) : $responseData,
            'status'        => $status,
            'shipment_id'   => isset($requestData['info']['rrn']) ? (int) $requestData['info']['rrn'] : null,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }

    public function logError($message)
    {
        DB::table('one_link_api_logs')->insert([
            'endpoint'      => 'N/A',
            'request_data'  => null,
            'response_data' => $message,
            'status'        => 'error',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }

    /**
     * OAuth2 client_credentials
     * Hits: {BASE}/oauth2/token
     */
    public function getAccessToken()
    {
        try {
            $response = $this->http()
                ->asForm() // application/x-www-form-urlencoded
                ->post('/oauth2/token', [
                    'grant_type'    => 'client_credentials',
                    'client_id'     => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'scope'         => $this->scope,
                ]);

            if ($response->successful()) {
                $json = $response->json();
                $this->logRequest('OAuth2 Token', [], $json, 'success');
                return $json['access_token'] ?? null;
            }

            $this->logRequest('OAuth2 Token', [], $response->body(), 'error');
            return null;
        } catch (\Throwable $e) {
            $this->logError('OAuth2 Exception: '.$e->getMessage());
            return null;
        }
    }

    /**
     * DQRC Generate
     * POST {BASE}/1Link/generateDQRCMerchant
     */
    public function generateDQRCMerchant(array $data)
    {
        return $this->sendRequest('/1Link/generateDQRCMerchant', 'generateDQRCMerchant', $data);
    }

    /**
     * Generic POST sender with logging.
     */
    protected function sendRequest(string $endpoint, string $logLabel, array $data)
    {
        if (!$this->token) {
            $this->logError('Missing Access Token.');
            return ['error' => 'Failed to retrieve access token'];
        }

        try {
            $response = $this->http()
                ->withToken($this->token) // Authorization: Bearer ...
                ->withHeaders([
                    'X-IBM-Client-Id' => $this->clientId,
                    'Accept'          => 'application/json',
                    'Content-Type'    => 'application/json',
                ])
                ->post($endpoint, $data);

            if ($response->successful()) {
                $json         = $response->json();
                $responseId   = $this->logRequest($logLabel, $data, $json, 'success');
                return ['success' => true, 'details' => $json, 'response_id' => $responseId];
            }

            $this->logRequest($logLabel, $data, $response->body(), 'error');
            return ['error' => 'Failed to process request', 'details' => $response->body()];
        } catch (\Throwable $e) {
            $this->logError($logLabel.' Exception: '.$e->getMessage());
            return ['error' => 'Exception occurred', 'details' => $e->getMessage()];
        }
    }
}
