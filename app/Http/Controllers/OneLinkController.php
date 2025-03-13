<?php

namespace App\Http\Controllers;

use App\Services\OneLinkService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OneLinkController extends Controller
{
    protected $oneLinkService;

    public function __construct(OneLinkService $oneLinkService)
    {
        $this->oneLinkService = $oneLinkService;
    }

    private function logRequest($endpoint, $requestData = null, $responseData = null, $status = 'pending')
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

    private function logError($message)
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

    public function generateDQRCMerchant(Request $request): JsonResponse
    {
        $data = $request->all();
        $endpoint = 'generateDQRCMerchant';

        try {
            $response = $this->oneLinkService->generateDQRCMerchant($data);

            $status = isset($response['error']) ? 'error' : 'success';
            $this->logRequest($endpoint, $data, $response, $status);

            return response()->json($response);
        } catch (\Exception $e) {
            $this->logError($e->getMessage());
            return response()->json(['error' => 'Exception occurred', 'details' => $e->getMessage()], 500);
        }
    }
}
