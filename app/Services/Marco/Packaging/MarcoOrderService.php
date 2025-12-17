<?php

namespace App\Services\Marco\Packaging;

use Illuminate\Support\Facades\Http;
use App\Models\ApiHandshakeLog;
use App\Services\Marco\MarcoBaseService;

class MarcoOrderService extends MarcoBaseService
{
    public function bookPackagingOrder(array $payload): array
    {
        $log = ApiHandshakeLog::create([
            'service_name'    => 'marco',
            'endpoint'        => '/api/user/v1/order/packaging',
            'method'          => 'POST',
            'reference_id'    => $payload['shipper_order_id'],
            'request_payload' => $payload,
            'status'          => 'pending',
        ]);
        $response = Http::withHeaders($this->headers())
            ->timeout(30)
            ->post(
                $this->endpoint('api/user/v1/order/packaging'),
                $payload
            );
        $log->update([
            'response_payload' => $response->json(),
            'response_status'  => $response->status(),
            'status'           => $response->successful() ? 'success' : 'failed',
        ]);

        return [
            'status' => $response->successful() ? 'success' : 'failed',
            'data'   => $response->json(),
        ];
    }
}
