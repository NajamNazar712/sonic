<?php

namespace App\Http\Traits;

use App\Http\Models\DonePayment;
use App\Http\Models\PendingPayment;
use App\Http\Models\DonePaymentShipment;
use App\Http\Models\DonePaymentCalculation;
use App\Http\Models\PendingPaymentShipment;
use App\Http\Models\PendingPaymentCalculation;
use App\Http\Controllers\FingaIntegrationController;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Models\UserIbftCharge;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentServicesCharges;
use App\Http\Controllers\AdminFinanceController;
use App\ShipmentAdditionalCharges;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;


trait FinSurgentLogTrait 
{


    static function arrival_shipment_logs($requestPayload, $pending_payment_shipment) {

        try {

            $api = env('FINGA_URL');
            $token = FingaIntegrationController::getToken($api);

            if($token) {
                $response = Http::withHeaders([
                    'accept' => 'application/json',
                    'Authorization' => "Bearer " . $token,
                
                ])->post($api.'transactions/log/payment', $requestPayload);
    
                FingaIntegrationController::apiLog('log-request', 1, $requestPayload ,$pending_payment_shipment->shipment_id);
    
                if($response->successful()) { 
                    
                    $body = $response->getBody();
                    $body = json_decode($body);
    
                    FingaIntegrationController::apiLog('log-response', 'success', $body ,$pending_payment_shipment->shipment_id);
    
                    FinjaLogSettlementRecord::where('shipment_id',$pending_payment_shipment->shipment_id)->update(['wallet_log_updated' => true, 'wallet_log_updated_at' => Carbon::now()]);

                    FinjaLogSettlementRecord::updateOrCreate(
                        // Condition to find the record
                        ['shipment_id' => $pending_payment_shipment->shipment_id],
                        // Data to update or create
                        [
                            'shipment_id' => $pending_payment_shipment->shipment_id,
                            'wallet_log_updated' => true,
                            'wallet_log_updated_at' => Carbon::now(),
                        ]
                    );
    
                } else {
                    $body = $response->getBody();
                    $body = json_decode($body);
                    FingaIntegrationController::apiLog('log-response', 'error', $body ,$pending_payment_shipment->shipment_id);
                }
            }

        } catch (\Throwable $th) {
            // Log exception details
            $errorBody = [
                'error' => $th->getMessage(),
                'code' => $th->getCode()
            ];
            FingaIntegrationController::apiLog('log-response', 'exception', $errorBody, $pending_payment_shipment->shipment_id);
        }

    }

}