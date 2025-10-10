<?php

namespace App\Http\Traits;

use App\Http\Models\DonePayment;
use App\Http\Models\PendingPayment;
use App\Http\Models\DonePaymentShipment;
use App\Http\Models\DonePaymentCalculation;
use App\Http\Models\PendingPaymentShipment;
use App\Http\Models\PendingPaymentCalculation;
use App\Http\Controllers\FingaIntegrationController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Models\UserIbftCharge;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentServicesCharges;
use App\Http\Controllers\Admins\AdminFinanceController;
use App\ShipmentAdditionalCharges;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Models\FinjaLogSettlementRecord;


trait FinSurgentLogTrait
{


    static function arrival_shipment_logs($requestPayload, $pending_payment_shipment, $shipment_id = null, $token2 = null)
    {

        try {

            if (!empty($shipment_id)) {
                $shipmentId = $shipment_id;
            } else if (is_object($pending_payment_shipment)) {
                $shipmentId = $pending_payment_shipment->shipment_id;
            } elseif (is_int($pending_payment_shipment)) {
                $shipmentId = $pending_payment_shipment;
            }

            $api = config('app.FINGA_URL');
            if (!empty($token2)) {
                $token = $token2;
            } else {
                $token = FingaIntegrationController::getToken($api);
            }
            if (empty($token)) {
                $token = FingaIntegrationController::getToken($api);
            }

            if ($token) {
                FingaIntegrationController::apiLog(3, 1, $requestPayload, $shipmentId);
                $response = Http::withHeaders([
                    'accept' => 'application/json',
                    'Authorization' => "Bearer " . $token,

                ])->post($api . 'transactions/log/payment', $requestPayload);

                if ($response->successful()) {

                    $body = $response->getBody();
                    $body = json_decode($body);

                    FingaIntegrationController::apiLog(4, 'success', $body, $shipmentId);

                    FinjaLogSettlementRecord::updateOrCreate(
                    // Condition to find the record
                        ['shipment_id' => $shipmentId],
                        // Data to update or create
                        [
                            'shipment_id' => $shipmentId,
                            'wallet_log_updated' => true,
                            'wallet_log_updated_at' => Carbon::now(),
                            'logged_cod_charges' => $requestPayload['amount']
                        ]
                    );

                } else {
                    $body = $response->getBody();
                    $body = json_decode($body, true);

                    if (isset($body['status']) && $body['status'] === 'error' && (str_contains($body['message'] ?? '', 'Transaction logged already.') || str_contains($body['error'] ?? '', 'duplicate key value violates unique constraint'))) {
                        FingaIntegrationController::apiLog(4, 'success (duplicate ignored)', $body, $shipmentId);

                        FinjaLogSettlementRecord::updateOrCreate(
                            ['shipment_id' => $shipmentId],
                            [
                                'shipment_id' => $shipmentId,
                                'wallet_log_updated' => true,
                                'wallet_log_updated_at' => Carbon::now(),
                                'logged_cod_charges' => $requestPayload['amount']
                            ]
                        );

                    } else {
                        FingaIntegrationController::apiLog(4, 'error', $body, $shipmentId);
                    }
                }
            }

        } catch (\Throwable $th) {
            // Log exception details
            $errorBody = [
                'error' => $th->getMessage(),
                'code' => $th->getCode()
            ];
            FingaIntegrationController::apiLog(4, 'exception', $errorBody, $shipmentId);
        }

    }

    static function updatePaymentBeforeLog($shipment, $pending_payment_shipment)
    {
        if (empty($shipment)) {
            $shipment = Shipment::find($pending_payment_shipment->shipment_id);
        }
        $faf_charges = ShipmentAdditionalCharges::fetch_faf_charges($pending_payment_shipment->shipment_id);
        $charges = $shipment->weight_charges + $shipment->fuel_surcharge + $faf_charges;
        $gst = $pending_payment_shipment->gst;
        $pending_payment_id = 0;
        if ($charges != $pending_payment_shipment->charges) {
            if ($shipment->business_category_id == 1) {
                $gst = ROUND(($charges * AdminFinanceController::gst($shipment->pickup_address->city->zone_id, $shipment->pickup_address->city_id)), 2, PHP_ROUND_HALF_DOWN);
            } else {
                $gst = ROUND(($charges * AdminFinanceController::international_gst()), 2, PHP_ROUND_HALF_DOWN);
            }
            $payable = 0 - ($charges + $gst);
            $pending_payment_id = $pending_payment_shipment->pending_payment_id;
            if (!empty($payable)) {
                PendingPaymentShipment::where('id', $pending_payment_shipment->id)->update(['charges' => $charges, 'gst' => $gst, 'payable' => $payable]);
            }
        }

        if ($pending_payment_id != 0) {
            $results = DB::select('CALL update_pending_payment_statistics(?)', [$pending_payment_id]);
        }
    }


    static function arrival_shipment_logs_bulk($requestPayload)
    {

        $batch_id = (string)Str::uuid();
        try {

            $api = config('app.FINGA_URL');
            $token = FingaIntegrationController::getToken($api);

            if ($token) {
                foreach ($requestPayload as $key => $payload) {
                    FingaIntegrationController::apiLog(17, 1, $payload, $key, null, $batch_id);
                }
                $response = Http::withHeaders([
                    'accept' => 'application/json',
                    'Authorization' => "Bearer " . $token,

                ])->connectTimeout(120)->timeout(120)->post($api . 'transactions/log/payment/bulk', $requestPayload);

                if ($response->successful()) {

                    $body = $response->getBody();
                    $body = json_decode($body, true);

                    foreach ($body as $b) {
                        if (isset($b['shipment_id'])) {
                            $tracking_number = $b['shipment_id'];
                            $shipment = Shipment::where('tracking_number', $tracking_number)->first();
                            $shipmentId = $shipment->id;

                            if ($b['status'] == 'success' || ($b['status'] == 'error' && str_contains($b['message'], 'Transaction logged already.'))) {
                                FingaIntegrationController::apiLog(18, $b['status'], $b, $shipmentId, null, $batch_id);
                                FinjaLogSettlementRecord::updateOrCreate(
                                // Condition to find the record
                                    ['shipment_id' => $shipmentId],
                                    // Data to update or create
                                    [
                                        'shipment_id' => $shipmentId,
                                        'wallet_log_updated' => true,
                                        'wallet_log_updated_at' => Carbon::now(),
                                        'logged_cod_charges' => $requestPayload[$shipmentId]['amount'] ?? $shipment->amount
                                    ]
                                );
                            } else {
                                FingaIntegrationController::apiLog(18, 'error', $b, $shipmentId, null, $batch_id);
                            }
                        }
                    }
                } else {

                    $body = $response->getBody();
                    $body = json_decode($body);
                    FingaIntegrationController::apiLog(18, 'error', $body, null, null, $batch_id);
                }
            }

        } catch (\Throwable $th) {
            // Log exception details
            $errorBody = [
                'error' => $th->getMessage(),
                'code' => $th->getCode()
            ];
            FingaIntegrationController::apiLog(18, 'exception', $errorBody, null, null, $batch_id);
        }

    }

}