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

    public function generateDQRCMerchant($rider_id, $shipment_id, $cod_amount, $latitude, $longitude)
    {
        if (!$rider_id || !$shipment_id || !$cod_amount || !$latitude || !$longitude) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }

        $data = [
            "merchantDetails" => [
                "dbaName" => "Sonic",
                "merchantName" => "Trax Online (Pvt.) Ltd.",
                "iban" => "PK94AIIN0000102514490014",
                "bankBic" => "AIIN",
                "merchantCategoryCode" => "4215",
                "merchantID" => "854710236963454",
                "postalAddress" => [
                    "townName" => "KARACHI",
                    "subDept" => (string) $rider_id,
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
                    "lat" => (string) $latitude, 
                    "longt" => (string) $longitude 
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
                "transactionType" => "064" 
            ],
            "info" => [
                "stan" => strtoupper(Str::random(6)), 
                "rrn" => str_pad((string) $shipment_id, 12, '0', STR_PAD_LEFT) 
            ]
        ];
        

        try {
            $response = $this->oneLinkService->generateDQRCMerchant($data);
            $status = isset($response['error']) ? 'error' : 'success';

            $this->oneLinkService->logRequest('generateDQRCMerchant', $data, $response, $status);

            return response()->json([
                'success' => $status === 'success',
                'data' => $response
            ]);
        } catch (\Exception $e) {
            $this->oneLinkService->logError($e->getMessage());
            return response()->json(['error' => 'Exception occurred', 'details' => $e->getMessage()], 500);
        }
    }

    public function verifyDeliveredShipmentDQRCMerchant(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'delivery_note_id' => 'required|integer|exists:rider_deliveries,delivery_note_id',
            'shipment_id'      => 'required|integer|exists:rider_deliveries,shipment_id',
            'cod_amount'        => 'required|numeric',
            'rider_id'         => 'required|integer|exists:riders,id',
            'latitude'         => 'required|numeric|between:-90,90',
            'longitude'        => 'required|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], 422);
        }

        $delivered_shipment = RiderDelivery::where([
            'delivery_note_id' => $request->delivery_note_id,
            'shipment_id'      => $request->shipment_id,
            'delivered_status' => 1
        ])->exists();

        if ($delivered_shipment) {
            return $this->generateDQRCMerchant(
                $request->rider_id,
                $request->shipment_id,
                $request->cod_amount,
                $request->latitude,
                $request->longitude
            );
        }

        return response()->json(['error' => 'No matching delivered shipments found'], 404);
    }

    public function notifyMerchant(Request $request)
    {
        $requestData = $request->validate([
            'info' => 'required|array',
        ]);

        $response = $this->oneLinkService->notifyMerchant($requestData['info']);

        return response()->json($response);
    }

    public function paymentNotification(Request $request)
    {
        $requestData = $request->validate([
            'info' => 'required|array',
        ]);

        $response = $this->oneLinkService->paymentNotification($requestData['info']);

        return response()->json($response);
    }
}
