<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Models\Shipment;
use App\Models\OneLinkApiLog;
use App\Services\OneLinkService;
use App\Http\Models\RiderDelivery;
use App\Models\OneLinkTransaction;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Admin\DeliveryNote;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\Admin\DeliveryNoteShipment;

class OneLinkController extends Controller
{
    protected $oneLinkService;

    public function __construct(OneLinkService $oneLinkService)
    {
        $this->oneLinkService = $oneLinkService;
    }

    public function generateDQRCMerchant($delivery_note_id, $shipment_id, $cod_amount, $latitude, $longitude)
    {
        if (!$delivery_note_id || !$shipment_id || !$cod_amount || !$latitude || !$longitude) {
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
                    "subDept" => (string) $delivery_note_id,
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
            
            if (isset($response['responseCode']) && $response['responseCode'] == '00') {
                OneLinkTransaction::create([
                    'rrn' => $response['info']['rrn'] ?? null,
                    'stan' => $response['info']['stan'] ?? null,
                    'status' => 'pending',
                ]);
            }
        
            return response()->json([
                'success' => $status === 'success',
                'status' => isset($response['responseCode']) && $response['responseCode'] == '00' ? 0 : 1,
                'data' => $response
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Exception occurred', 'details' => $e->getMessage()], 500);
        }
    }

    public function verifyDeliveredShipmentDQRCMerchant(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'delivery_note_id' => 'required|integer|exists:delivery_notes,id',
            'shipment_id'      => 'required|integer|exists:delivery_note_shipments,shipment_id',
            'cod_amount'       => 'required|numeric',
            'latitude'         => 'required|numeric|between:-90,90',
            'longitude'        => 'required|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], 422);
        }

        $delivery_note = DeliveryNote::where('id', $request->delivery_note_id)
            ->where('status', 0)
            ->where('pending_status', 0)
            ->first();

        $delivery_note_shipments = false;

        if ($delivery_note) {
            $delivery_note_shipments = $delivery_note->delivery_note_shipments
                ->where('shipment_id', $request->shipment_id)
                ->count() > 0;
        }

        if ($delivery_note && $delivery_note_shipments) {
            return $this->generateDQRCMerchant(
                $request->delivery_note_id,
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
        $rrn = (int) $data['info']['rrn'];
        $stan = (int) $data['info']['stan'];
        $subDept = (int) $data['messageInfo']['subDept'];
    
        $existingTransaction = OneLinkTransaction::where([
            'rrn' => $rrn,
            'stan' => $stan
        ])->first();
    
        if ($existingTransaction) {
            DB::transaction(function () use ($existingTransaction, $data, $natureId) {
                $existingTransaction->update(array_merge([
                    'date_time' => $data['info']['dateTime'] ?? $existingTransaction->date_time,
                    'merchant_id' => $data['messageInfo']['merchantID'] ?? $existingTransaction->merchant_id,
                    'sub_dept' => $data['messageInfo']['subDept'] ?? $existingTransaction->sub_dept,
                    'status' => $data['messageInfo']['status'] ?? $existingTransaction->status,
                    'nature_id' => $natureId
                ], $natureId === 1 ? [
                    'message_id' => $data['messageInfo']['originalMessageId'] ?? $existingTransaction->message_id,
                    'original_rrn' => $data['messageInfo']['originalRRN'] ?? $existingTransaction->original_rrn,
                    'original_stan' => $data['messageInfo']['originalStan'] ?? $existingTransaction->original_stan,
                    'original_rtp_id' => $data['messageInfo']['originalRtpId'] ?? $existingTransaction->original_rtp_id
                ] : [
                    'message_id' => $data['messageInfo']['messageId'] ?? $existingTransaction->message_id,
                    'original_rrn' => $data['messageInfo']['originalRRN'] ?? $existingTransaction->original_rrn,
                    'original_stan' => $data['messageInfo']['originalStan'] ?? $existingTransaction->original_stan,
                    'original_rtp_id' => $data['messageInfo']['originalRtpId'] ?? $existingTransaction->original_rtp_id,
                    'original_instructed_amount' => $data['messageInfo']['originalInstructedAmount'] ?? $existingTransaction->original_instructed_amount,
                    'net_amount' => $data['messageInfo']['netAmount'] ?? $existingTransaction->net_amount,
                    'iban' => $data['senderInfo']['iban'] ?? $existingTransaction->iban,
                    'account_title' => $data['senderInfo']['accountTitle'] ?? $existingTransaction->account_title,
                    'longitude' => $data['senderInfo']['longitude'] ?? $existingTransaction->longitude,
                    'latitude' => $data['senderInfo']['latitude'] ?? $existingTransaction->latitude,
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
                    "subDept" => $subDept
                ]
            ];
    
            $status = 200;
    
            if ($natureId == 2) {
                $shipment = Shipment::find($rrn);
                if ($shipment) {
                    $shipment->update(['received_amount' => $data['messageInfo']['originalInstructedAmount'] ?? 0]);
                }
    
                $delivery_note = DeliveryNote::find($subDept);
                if ($delivery_note) {
                    $delivery_note->increment('one_link_payment_count');
                }
            }
        } else {
            $response = [
                "responseCode" => "01",
                "responseDesc" => "NOT FOUND",
                "info" => [
                    "rrn" => $rrn,
                    "stan" => $stan,
                    "messageId" => null,
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
            'info.rrn' => 'required|string|exists:one_link_transactions,rrn',
            'info.stan' => 'required|string|exists:one_link_transactions,stan',
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
            'info.rrn' => 'required|string|exists:one_link_transactions,rrn',
            'info.stan' => 'required|string|exists:one_link_transactions,stan',
            'info.dateTime' => 'required|string',
            'messageInfo.messageId' => 'required|string',
            'messageInfo.originalRRN' => 'required|string',
            'messageInfo.originalStan' => 'required|string',
            'messageInfo.originalRtpId' => 'required|string',
            'messageInfo.merchantID' => 'required|string',
            'messageInfo.subDept' => 'required|string',
            'messageInfo.status' => 'required|string',
            'messageInfo.originalInstructedAmount' => 'nullable|string',
            'messageInfo.netAmount' => 'nullable|string',
            'senderInfo.iban' => 'required|string',
            'senderInfo.accountTitle' => 'required|string',
            'senderInfo.longitude' => 'nullable|string',
            'senderInfo.latitude' => 'nullable|string',
        ];
        return $this->processTransaction($request, 'paymentNotification', $rules, 2);
    }
}
