<?php

namespace App\Services\Marco;

use App\Models\ApiHandshakeLog;
use Illuminate\Support\Facades\Http;

class MarcoSkuService  extends MarcoBaseService
{
    

    public function createSku(array $payload): array
    {
        // 1️⃣ Before-handshake log
        $log = ApiHandshakeLog::create([
            'service_name'    => 'marco',
            'endpoint'        => '/api/user/v1/sku/skuCreate',
            'method'          => 'POST',
            'reference_id'    => $payload['sku_id'] ?? null,
            'request_payload' => $payload,
            'status'          => 'pending',
        ]);

        try {
            // 2️⃣ API Call with X-API-KEY header
            $response = Http::withHeaders($this->headers())->withBody(
                json_encode($payload),       // raw JSON
                'application/json'
            )->post($this->baseUrl . 'api/user/v1/sku/skuCreate');
            // 3️⃣ After-handshake log
            $log->update([
                'response_payload' => $response->json(),
                'response_status'  => $response->status(),
                'status'           => $response->successful() ? 'success' : 'failed',
            ]);

            return $response->successful()
                ? ['success' => true, 'data' => $response->json()]
                : ['success' => false, 'message' => $response->json()['message'] ?? 'Marco API failed'];
        } catch (\Throwable $e) {
            // 4️⃣ Exception log
            $log->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Marco service unreachable',
            ];
        }
    }

    
}
