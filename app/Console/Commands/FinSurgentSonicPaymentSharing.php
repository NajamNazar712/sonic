<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
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

class FinSurgentSonicPaymentSharing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fingsurgent:sonic-payment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $pending_payment_wallet_users = PendingPayment::join('users as u', 'pending_payments.user_id', '=', 'u.id')->whereNotNull('u.wallet_id')->select(['pending_payments.*', 'u.wallet_id', 'u.id as user_id'])->get();
       
        foreach($pending_payment_wallet_users as $pending_payment) {
            
            $pending_payment_shipment_ids = PendingPaymentShipment::leftjoin('shipment_additional_charges as sac', 'pending_payment_shipments.shipment_id', '=', 'sac.shipment_id' )->where('pending_payment_shipments.pending_payment_id', $pending_payment->id)->where('pending_payment_shipments.type', 3)-where('sac.wallet_log_updated', 1)->select(['pending_payment_shipments.*'])->get();

            foreach ($pending_payment_shipment_ids as $pending_payment_shipment) {
               
                if ($pending_payment_shipment) {

                    $shipment = Shipment::find($pending_payment_shipment->shipment_id);

                    $charges = $shipment->weight_charges + $shipment->fuel_surcharge + $faf_charges;

                    if($charges != $pending_payment_shipment->charges) {

                        if ($shipment->business_category_id == 1) { 

                            $new_gst = ROUND(($charges * AdminFinanceController::gst($shipment->pickup_address->city->zone_id, $shipment->pickup_address->city_id)), 2, PHP_ROUND_HALF_DOWN);

                        } else {
                            $new_gst = ROUND(($charges * AdminFinanceController::international_gst()), 2, PHP_ROUND_HALF_DOWN);
                        }
                        
                        $payable = $charges + $new_gst;
            
                        if(!empty($payable) ) {
                            PendingPaymentShipment::where('id',  $pending_payment_shipment->id)->update(['charges' => $charges, 'gst' => $new_gst, 'payable' => $payable]);
                        }

                    }
                        
                    $api = env('FINGA_URL');
                    $token = FingaIntegrationController::getToken($api);
                    
                    if($token) {
                        
                        $requestPayload = [
                            "client_id" => $pending_payment->user_id,
                            "wallet_id" => $pending_payment->wallet_id,
                            "reference_id" => $pending_payment_shipment->id,
                            "shipment_id" =>  $pending_payment_shipment->shipment_id,
                            "amount" => $shipment->amount,
                            "charges" => [
                                'arrival_charges' => $shipment->weight_charges,
                                'fuel_surcharge' => $shipment->fuel_surcharge,
                                'faf_charges' => $faf_charges
                            ]
                        ];
            
                        $response = Http::withHeaders([
                            'accept' => 'application/json',
                            'Authorization' => "Bearer " . $token,
                        
                        ])->post($api.'transactions/log/payment', $requestPayload);

                        FingaIntegrationController::apiLog('log-request', 1, $requestPayload ,$pending_payment_shipment->shipment_id);

                        if($response->successful()) { 
                            
                            $body = $response->getBody();
                            $body = json_decode($body);

                            FingaIntegrationController::apiLog('log-response', 'success', $body ,$pending_payment_shipment->shipment_id);

                            ShipmentAdditionalCharges::where('shipment_id',$pending_payment_shipment->shipment_id)->update(['wallet_log_updated' => true, 'wallet_log_updated_at' => Carbon::now()]);

                        } else {
                            $body = $response->getBody();
                            $body = json_decode($body);
                            FingaIntegrationController::apiLog('log-response', 'error', $body ,$pending_payment_shipment->shipment_id);
                        }
                    }
                }
            }
        }
        
        return Command::SUCCESS;
    }
}