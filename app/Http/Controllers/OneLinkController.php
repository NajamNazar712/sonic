<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\OneLinkApiLog;
use App\Services\OneLinkService;
use App\Http\Models\RiderDelivery;
use App\Models\OneLinkTransaction;
use Illuminate\Support\Facades\DB;
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
                "executionDateTime" => now()->format('Y-m-d\TH:i:s'),
                "expiryDateTime" => now()->addMinutes(40)->format('Y-m-d\TH:i:s'),
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
            return response()->json([
                'success' => $status === 'success',
                'data' => $response
            ]);
        } catch (\Exception $e) {
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

    protected function validateRequest(Request $request, array $rules, string $logType)
    {
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $response = [
                'responseCode' => '01',
                'responseDesc' => 'Validation Failed',
                'errors' => $validator->errors()
            ];
            $status = 422;
            $this->oneLinkService->logRequest($logType, $request->all(), $response, $status);
            return response()->json($response, $status);
        }
        return null;
    }

    protected function processTransaction(Request $request, string $logType, array $rules, int $natureId)
    {
        $validationResponse = $this->validateRequest($request, $rules, $logType);
        if ($validationResponse) {
            return $validationResponse;
        }

        $data = $request->all();
        $rrn = $data['info']['rrn'];
        $stan = $data['info']['stan'];

        $existingLog = OneLinkApiLog::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(response_data, '$.info.rrn')) = ?", [$rrn])
            ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(response_data, '$.info.stan')) = ?", [$stan])
            ->first();

        if ($existingLog) {
            DB::transaction(function () use ($data, $rrn, $stan, $natureId) {
                OneLinkTransaction::create(array_merge([
                    'rrn' => $rrn,
                    'stan' => $stan,
                    'date_time' => $data['info']['dateTime'],
                    'merchant_id' => $data['messageInfo']['merchantID'],
                    'sub_dept' => $data['messageInfo']['subDept'] ?? null,
                    'status' => $data['messageInfo']['status'],
                    'nature_id' => $natureId
                ], $natureId === 1 ? [
                    'message_id' => $data['messageInfo']['originalMessageId'],
                    'original_rrn' => $data['messageInfo']['originalRRN'],
                    'original_stan' => $data['messageInfo']['originalStan'],
                    'original_rtp_id' => $data['messageInfo']['originalRtpId']
                ] : [
                    'message_id' => $data['messageInfo']['messageId'],
                    'original_rrn' => $data['messageInfo']['originalRRN'],
                    'original_stan' => $data['messageInfo']['originalStan'],
                    'original_rtp_id' => $data['messageInfo']['originalRtpId'],
                    'original_instructed_amount' => $data['messageInfo']['originalInstructedAmount'] ?? null,
                    'net_amount' => $data['messageInfo']['netAmount'] ?? null,
                    'iban' => $data['senderInfo']['iban'],
                    'account_title' => $data['senderInfo']['accountTitle'],
                    'longitude' => $data['senderInfo']['longitude'] ?? null,
                    'latitude' => $data['senderInfo']['latitude'] ?? null,
                ]));
            });

            $response = [
                "responseCode" => "00",
                "responseDesc" => "Processed OK",
                "info" => [
                    "rrn" => $rrn,
                    "stan" => $stan,
                    "messageId" => $data['messageInfo']['messageId'] ?? $data['messageInfo']['originalMessageId'],
                    "merchantID" => $data['messageInfo']['merchantID'],
                    "subDept" => $data['messageInfo']['subDept'] ?? null
                ]
            ];
            $status = 200;
        } else {
            $response = [
                "responseCode" => "01",
                "responseDesc" => "NOT FOUND",
                "info" => [
                    "rrn" => $rrn,
                    "stan" => $stan,
                    "messageId" => null,
                    "rtpId" => null,
                    "merchantID" => null,
                    "subDept" => null
                ]
            ];
            $status = 404;
        }

        $this->oneLinkService->logRequest($logType, $data, $response, $status);
        return response()->json($response, $status);
    }

    public function notifyMerchant(Request $request)
    {
        $rules = [
            'info' => 'required|array',
            'messageInfo' => 'required|array',
            'info.rrn' => 'required|string',
            'info.stan' => 'required|string',
            'info.dateTime' => 'required|string',
            'messageInfo.originalRRN' => 'required|string',
            'messageInfo.originalStan' => 'required|string',
            'messageInfo.originalMessageId' => 'required|string',
            'messageInfo.originalRtpId' => 'required|string',
            'messageInfo.merchantID' => 'required|string',
            'messageInfo.subDept' => 'required|string',
            'messageInfo.status' => 'required|string',
        ];
        return $this->processTransaction($request, 'notifyMerchant', $rules, 1);
    }

    public function paymentNotification(Request $request)
    {
        $rules = [
            'info' => 'required|array',
            'messageInfo' => 'required|array',
            'senderInfo' => 'required|array',
            'info.rrn' => 'required|string',
            'info.stan' => 'required|string',
            'info.dateTime' => 'required|string',
            'messageInfo.messageId' => 'required|string',
            'messageInfo.originalRRN' => 'required|string',
            'messageInfo.originalStan' => 'required|string',
            'messageInfo.originalRtpId' => 'required|string',
            'messageInfo.merchantID' => 'required|string',
            'messageInfo.subDept' => 'required|string',
            'messageInfo.status' => 'required|string',
            'messageInfo.originalInstructedAmount' => 'nullable|numeric',
            'messageInfo.netAmount' => 'nullable|numeric',
            'senderInfo.iban' => 'required|string',
            'senderInfo.accountTitle' => 'required|string',
            'senderInfo.longitude' => 'nullable|string',
            'senderInfo.latitude' => 'nullable|string',
        ];
        return $this->processTransaction($request, 'paymentNotification', $rules, 2);
    }

}
