<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Models\Shipment;
use App\Services\OneLinkService;
use App\Models\OneLinkTransaction;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Admin\DeliveryNote;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\JsonResponseException;



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
        $date_time_now = Carbon::now()->format('Y-m-d H:i:s');
        $check_transaction = OneLinkTransaction::with('one_link_log')
            ->where('shipment_id', $shipment_id)
            ->where('status', 'pending')
            ->where('expiry_time', '>', $date_time_now)
            ->latest()
            ->first();

        if (!empty($check_transaction)) {
            $existing_log = json_decode($check_transaction->one_link_log->response_data, true);
            $expiry_time = Carbon::parse($check_transaction->expiry_time)->format('Y-m-d\TH:i:s');
            $remaining_seconds = now()->diffInSeconds($expiry_time, false); // false = future is positive, past is negative

            return response()->json([
                'success' => 'success',
                'status' => 0,
                'data' => $existing_log,
                'expiryDateTime' => $expiry_time,
                'remainingTimeInSeconds' => $remaining_seconds,
                'remainingTimeHuman' => now()->diffForHumans($expiry_time, [
                    'parts' => 2, 'short' => true, 'syntax' => CarbonInterface::DIFF_RELATIVE_TO_NOW
                ]),
            ]);
        } else {
            $execution_time = now()->format('Y-m-d\TH:i:s');
            $expiry_time = now()->addHours(1)->format('Y-m-d\TH:i:s');
            $remaining_seconds = now()->diffInSeconds($expiry_time, false); // false = future is positive, past is negative

            $data = [
                "merchantDetails" => [
                    "dbaName" => "Sonic",
                    "merchantName" => "TRAX ONLINE PRIVATE LIMITED",
                    "iban" => "PK15ALFH5692005002464647",
                    "bankBic" => "ALFH",
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
                    "executionDateTime" => $execution_time,
                    "expiryDateTime" => $expiry_time,
                    "instructedAmount" => $cod_amount,
                    "transactionType" => "064"
                ],
                "info" => [
                    "stan" => strtoupper(Str::random(6)),
                    "rrn" => str_pad((string)$shipment_id, 12, '0', STR_PAD_LEFT)
                ]
            ];

            try {
                $response = $this->oneLinkService->generateDQRCMerchant($data);
                $status = isset($response['error']) ? 'error' : 'success';
                if (isset($response['details']['responseCode']) && $response['details']['responseCode'] == '00') {
                    $response_id = isset($response['response_id']) ? $response['response_id'] : 0;
                    OneLinkTransaction::create([
                        'rrn' => $response['details']['info']['rrn'],
                        'stan' => $response['details']['info']['stan'],
                        'shipment_id' => $shipment_id,
                        'expiry_time' => $expiry_time,
                        'status' => 'pending',
                        'log_id' => $response_id,

                    ]);
                }

                return response()->json([
                    'success' => $status === 'success',
                    'status' => isset($response['details']['responseCode']) && $response['details']['responseCode'] == '00' ? 0 : 1,
                    'data' => $response['details'],
                    'expiryDateTime' => $data['paymentDetails']['expiryDateTime'],
                    'remainingTimeInSeconds' => $remaining_seconds,
                    'remainingTimeHuman' => now()->diffForHumans($expiry_time, [
                        'parts' => 2, 'short' => true, 'syntax' => CarbonInterface::DIFF_RELATIVE_TO_NOW
                    ]),

                ]);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Exception occurred', 'details' => $e->getMessage()], 500);
            }
        }
    }

    public function verifyDeliveredShipmentDQRCMerchant(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'delivery_note_id' => 'required|integer|exists:delivery_notes,id',
            'shipment_id'      => 'required|integer|exists:delivery_note_shipments,shipment_id',
            'cod_amount'       => 'required|string',
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
                (float) $request->cod_amount,
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
    
        $rrn =  $data['info']['rrn'];
        $stan = $data['info']['stan'];
        $subDept = $data['messageInfo']['subDept'];
    
        $existingTransaction = OneLinkTransaction::where([
            'rrn' => $rrn,
            'stan' => $stan
        ])->first();
    
        if ($existingTransaction) {
            try {
                DB::transaction(function () use ($existingTransaction, $data, $natureId, $rrn, $subDept) {
                    $updateData = [
                        'date_time' => $data['info']['dateTime'] ?? now(),
                        'merchant_id' => $data['messageInfo']['merchantID'],
                        'sub_dept' => $subDept,
                        'status' => $data['messageInfo']['status'] ?? 'pending',
                        'nature_id' => $natureId
                    ];
    
                    if ($natureId === 1) {
                        $updateData = array_merge($updateData, [
                            'message_id' => $data['messageInfo']['originalMessageId'],
                            'original_rrn' => $data['messageInfo']['originalRRN'],
                            'original_stan' => $data['messageInfo']['originalStan'],
                            'original_rtp_id' => $data['messageInfo']['originalRtpId']
                        ]);
                    } else {
                        $updateData = array_merge($updateData, [
                            'message_id' => $data['messageInfo']['messageId'],
                            'original_rrn' => $data['messageInfo']['originalRRN'],
                            'original_stan' => $data['messageInfo']['originalStan'],
                            'original_rtp_id' => $data['messageInfo']['originalRtpId'],
                            'original_instructed_amount' => $data['messageInfo']['originalInstructedAmount'] ?? 0,
                            'net_amount' => $data['messageInfo']['netAmount'] ?? 0,
                            'iban' => $data['senderInfo']['iban'],
                            'account_title' => $data['senderInfo']['accountTitle'],
                            'longitude' => $data['senderInfo']['longitude'],
                            'latitude' => $data['senderInfo']['latitude'],
                        ]);
                    }
    
                    $existingTransaction->update($updateData);
    
                    if ($natureId == 2) {
                        $shipment = Shipment::find($rrn);
                        if ($shipment) {
                            $shipment->update([
                                'received_amount' => $data['messageInfo']['originalInstructedAmount'] ?? 0
                            ]);
                        }
    
                        $delivery_note = DeliveryNote::find($subDept);
                        if ($delivery_note) {
                            $delivery_note->increment('one_link_payment_count');
    
                            NotificationsController::app_notification(19, $delivery_note->rider_id, 2, $delivery_note->rider_id, $rrn);
                            NotificationsController::send(185, $delivery_note->rider_id, $rrn);
                        } else {
                            throw new JsonResponseException(response()->json([
                                'status' => false,
                                'message' => 'Delivery Note not found.',
                                'sub_dept' => $subDept
                            ], 404));
                        }
                    }
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
    
            } catch (JsonResponseException $e) {
                $response = $e->response->getData(true);
                $status = $e->response->status();
                $this->oneLinkService->logRequest($logType, $data, $response, $status);
                return $e->response;
    
            } catch (\Exception $e) {
                $response = [
                    "responseCode" => "99",
                    "responseDesc" => $e->getMessage(),
                ];
                $status = 500;
                $this->oneLinkService->logRequest($logType, $data, $response, $status);
                return response()->json($response, $status);
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
        $data['data'] = json_encode($request->all(), true);
        $data['paymentNotification'] = '1';
        Log::channel('code_test_log')->info($data);
        $rules = [
            'info' => 'required|array',
            'messageInfo' => 'required|array',
            'messageInfo.rrn' => 'required|string|exists:one_link_transactions,rrn',
            'messageInfo.stan' => 'required|string|exists:one_link_transactions,stan',
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
        $data['data'] = json_encode($request->all(), true);
        $data['paymentNotification'] = '1';
        Log::channel('code_test_log')->info($data);
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
            'messageInfo.originalInstructedAmount' => 'required|string',
            'messageInfo.netAmount' => 'required|string',
            'senderInfo.iban' => 'required|string',
            'senderInfo.accountTitle' => 'required|string',
            'senderInfo.longitude' => 'nullable|string',
            'senderInfo.latitude' => 'nullable|string',
        ];
        return $this->processTransaction($request, 'paymentNotification', $rules, 2);
    }
}
