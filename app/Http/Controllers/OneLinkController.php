<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\OneLinkService;
use App\Http\Models\RiderDelivery;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Admin\DeliveryNote;
use Illuminate\Support\Facades\Validator;

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

    public function generateDQRCMerchant($rider_id = null, $shipment_id = null, $cod_amount = null, $latitude = null, $longitude = null)
    {
        $data = [
            "merchantDetails" => [
                "dbaName" => "Sonic",
                "merchantName" => "Trax Online (Pvt.) Ltd.",
                "iban" => "PK94AIIN0000102514490014",
                "bankBic" => "EGIB",
                "merchantCategoryCode" => "4215",
                "merchantID" => "854710236963454",
                "postalAddress" => [
                    "townName" => "KARACHI",
                    "subDept" => $rider_id ?? "96010001",
                    "addressLine" => "Plot 105, Sector 7-A, Mehran Town, Korangi, Karachi"
                ],
                "contactDetails" => [
                    "phoneNo" => "+92-2138772222",
                    "mobileNo" => "",
                    "email" => "info@trax.pk",
                    "dept" => "Head office",
                    "website" => "www.trax.pk",
                    "merchantChannelId" => "400" 
                ],
                "geoLocation" => [
                    "lat" => $latitude,
                    "long" => $longitude
                ]
            ],
            "payerDetails" => [
                "additionalRequiredDetails" => "AME",
                "identificationDetails" => [
                    "loyaltyNo" => "SOME LOYALTY NUMB",
                    "customerLabel" => "SOME CUST LABEL"
                ]
            ],
            "paymentDetails" => [
                "executionDateTime" => now()->toIso8601String(),
                "expiryDateTime" => now()->addMinutes(40)->toIso8601String(),
                "instructedAmount" => $cod_amount,
                "transactionType" => "014"
            ],
            "info" => [
                "stan" => Str::upper(Str::random(6)), 
                "rrn" => str_pad($shipment_id, 12, '0', STR_PAD_LEFT),
            ]
        ];
    
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

    public function verifyDeliveredShipmentDQRCMerchant(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'delivery_note_id' => 'required|integer|exists:rider_deliveries,delivery_note_id',
            'shipment_id'      => 'required|integer|exists:rider_deliveries,shipment_id',
            'rider_id'         => 'required|integer|exists:riders,id',
            'latitude'         => 'required|numeric|between:-90,90',
            'longitude'        => 'required|numeric|between:-180,180',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], 422);
        }
    
        $delivered_shipment = RiderDelivery::where('delivery_note_id', $request->delivery_note_id)
            ->where('delivered_status', 1)
            ->where('shipment_id', $request->shipment_id)
            ->exists();
    
        if ($delivered_shipment) {
            return $this->generateDQRCMerchant($request->rider_id, $request->shipment_id, $request->delivery_note_id, $request->latitude, $request->longitude);
        }
    
        return response()->json(['message' => 'No matching delivered shipments found'], 404);
    }
    
    
}
