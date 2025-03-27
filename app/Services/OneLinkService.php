<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class OneLinkService
{
    protected $baseUrl;
    protected $clientId;
    protected $clientSecret;
    protected $scope;
    protected $token;

    public function __construct()
    {
        $this->baseUrl      = env('ONE_LINK_URL_SANDBOX_M');
        $this->clientId     = env('ONE_LINK_CLIENT_ID_SANDBOX_M');
        $this->clientSecret = env('ONE_LINK_SECRET_SANDBOX_M');
        $this->scope        = env('ONE_LINK_SCOPE', '1LinkApi');
        $this->token        = $this->getAccessToken();
    }

    public function logRequest($endpoint, $requestData = null, $responseData = null, $status = 'pending')
    {
        DB::table('one_link_api_logs')->insert([
            'endpoint'      => $endpoint,
            'request_data'  => json_encode($requestData),
            'response_data' => json_encode($responseData),
            'status'        => $status,
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

    public function getAccessToken()
    {
        $url = $this->baseUrl . '/oauth2/token';

        try {
            $response = Http::asForm()->post($url, [
                'grant_type'    => 'client_credentials',
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
                'scope'         => $this->scope,
            ]);

            if ($response->successful()) {
                $this->logRequest('OAuth2 Token', [], $response->json(), 'success');
                return $response->json()['access_token'] ?? null;
            }

            $this->logRequest('OAuth2 Token', [], $response->body(), 'error');
            return null;
        } catch (\Exception $e) {
            $this->logError($e->getMessage());
            return null;
        }
    }

    public function generateDQRCMerchant(array $data)
    {
        return $this->sendRequest('/1Link/generateDQRCMerchant', 'generateDQRCMerchant', $data);
    }

    protected function sendRequest($endpoint, $logLabel, array $data)
    {
        if (!$this->token) {
            $this->logError('Missing Access Token.');
            return ['error' => 'Failed to retrieve access token'];
        }

        $url = $this->baseUrl . $endpoint;

        try {
            $response = Http::withToken($this->token)
                ->withHeaders([
                    'X-IBM-Client-Id' => $this->clientId,
                    'Accept'          => 'application/json',
                    'Content-Type'    => 'application/json',
                ])
                ->post($url, $data);

            if ($response->successful()) {
                $this->logRequest($logLabel, $data, $response->json(), 'success');
                return $response->json();
            }

            $this->logRequest($logLabel, $data, $response->body(), 'error');
            return ['error' => 'Failed to process request', 'details' => $response->body()];
        } catch (\Exception $e) {
            $this->logError($e->getMessage());
            return ['error' => 'Exception occurred', 'details' => $e->getMessage()];
        }
    }
}
