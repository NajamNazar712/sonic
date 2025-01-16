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
use Illuminate\Support\Facades\Http;
use App\Http\Traits\FinSurgentLogTrait;

class FinSurgentSonicPaymentSharing extends Command
{
    use FinSurgentLogTrait;
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
        $pending_payment_wallet_users = PendingPayment::join('wallet_users as u', 'pending_payments.user_id', '=', 'u.user_id')->select(['pending_payments.*', 'u.wallet_id', 'u.user_id as user_id'])->get();
       
        foreach($pending_payment_wallet_users as $pending_payment) {
            
            $pending_payment_shipment_ids = PendingPaymentShipment::leftJoin('finja_log_settlement_records as sac', 'pending_payment_shipments.shipment_id', '=', 'sac.shipment_id')
            ->where('pending_payment_shipments.pending_payment_id', $pending_payment->id)
            ->where('pending_payment_shipments.type', 3)
            ->where(function ($query) {
                $query->whereNull('sac.id') 
                    ->orWhere('sac.wallet_log_updated', 0);
            })
            ->select(['pending_payment_shipments.*'])
            ->get();

            $pending_payment_id = array();
            foreach ($pending_payment_shipment_ids as $pending_payment_shipment) {
                if ($pending_payment_shipment) {

                    $shipment = Shipment::find($pending_payment_shipment->shipment_id);
                    $faf_charges = ShipmentAdditionalCharges::fetch_faf_charges($pending_payment_shipment->shipment_id);
                    $charges = $shipment->weight_charges + $shipment->fuel_surcharge + $faf_charges;
                    $gst = $pending_payment_shipment->gst;
                    if($charges != $pending_payment_shipment->charges) {

                        if ($shipment->business_category_id == 1) { 

                            $gst = ROUND(($charges * AdminFinanceController::gst($shipment->pickup_address->city->zone_id, $shipment->pickup_address->city_id)), 2, PHP_ROUND_HALF_DOWN);

                        } else {
                            $gst = ROUND(($charges * AdminFinanceController::international_gst()), 2, PHP_ROUND_HALF_DOWN);
                        }
                        
                        $payable = $charges + $gst;
                        $pending_payment_id[$pending_payment_shipment->pending_payment_id] = $pending_payment_shipment->pending_payment_id;
                        if(!empty($payable) ) {
                            PendingPaymentShipment::where('id',  $pending_payment_shipment->id)->update(['charges' => $charges, 'gst' => $gst, 'payable' => $payable]);
                        }
                    }
                        $requestPayload = [
                            "shipmentId" => $shipment->id,
                            "client_id" => $pending_payment->user_id,
                            "wallet_id" => $pending_payment->wallet_id,
                            "reference_id" => (string) Str::uuid(),
                            "shipment_id" =>  $shipment->tracking_number,
                            "amount" => $shipment->amount,
                            "order_created_date" => $shipment->created_at,
                            "charges" => [
                                'arrival_charges' =>  intval($shipment->weight_charges),
                                'fuel_surcharge' =>  intval($shipment->fuel_surcharge),
                                'faf_charges' => intval($faf_charges),
                                'arrival_charges_gst' => intval($gst),
                                'arrival_sms_charges' => intval($pending_payment_shipment->sms_charges)
                            ]
                        ];
                        $this->arrival_shipment_logs($requestPayload, $pending_payment_shipment);
                }
            }

            if(count($pending_payment_id) > 0) {
                $pendingPaymentsCalc = DB::table('pending_payment_shipments')
                ->select(
                    'pending_payment_shipments.pending_payment_id',
                    'pending_payment_calculations.payable',
                    DB::raw('SUM(pending_payment_shipments.amount) as total_amount'),
                    DB::raw('SUM(pending_payment_shipments.charges) as total_charges'),
                    DB::raw('SUM(pending_payment_shipments.gst) as total_gst'),
                    DB::raw('SUM(pending_payment_shipments.sms_charges) as total_sms_charges'),
                    DB::raw('SUM(pending_payment_shipments.payable) as total_payable'),
                    DB::raw('SUM(pending_payment_shipments.wht) as total_wht')
                )
                ->join('pending_payment_calculations', 'pending_payment_shipments.pending_payment_id', '=', 'pending_payment_calculations.pending_payment_id')
                ->join('pending_payments', 'pending_payment_calculations.pending_payment_id', '=', 'pending_payments.id')
                ->whereIn('pending_payment_calculations.pending_payment_id', $pending_payment->id)
                ->groupBy('pending_payment_shipments.pending_payment_id')
                ->havingRaw('SUM(pending_payment_shipments.payable) != pending_payment_calculations.payable')
                ->get();

                foreach ($pendingPaymentsCalc as $payment) {
                    DB::table('pending_payment_calculations')
                        ->where('pending_payment_id', $payment->pending_payment_id)
                        ->update([
                            'amount' => $payment->total_amount,
                            'charges' => $payment->total_charges,
                            'gst' => $payment->total_gst,
                            'sms_charges' => $payment->total_sms_charges,
                            'payable' => $payment->total_payable,
                            'wht' => $payment->total_wht,
                        ]);
                }
            } 
        }
        return Command::SUCCESS;
    }
}