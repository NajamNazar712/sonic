<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Traits\FinSurgentLogTrait;
use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Models\Shipment;
use App\Http\Models\PendingPayment;
use App\Http\Models\PendingPaymentShipment;
use Illuminate\Support\Str;


class WalletSignUpLPendingRecordLogs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels , FinSurgentLogTrait;
    protected $data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        //
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user_id = $this->data;
        $pending_payment_ids = PendingPayment::join('wallet_users as u', 'pending_payments.user_id', '=', 'u.user_id')->where('pending_payments.user_id', $user_id)
        ->pluck('pending_payments.id');
        
        foreach ($pending_payment_ids as $pending_payment ){
            
            $pending_payment_shipments = PendingPaymentShipment::leftJoin('finja_log_settlement_records as sac', 'pending_payment_shipments.shipment_id', '=', 'sac.shipment_id')
            ->where('pending_payment_shipments.pending_payment_id', $pending_payment)
            ->whereIn('pending_payment_shipments.type', [0,1,3])
            ->where(function ($query) {
                $query->whereNull('sac.id') 
                    ->orWhere('sac.wallet_log_updated', 0);
            })
            ->select(['pending_payment_shipments.*'])
            ->get();
            foreach($pending_payment_shipments as $pending_payment_shipment) {
                $shipment = Shipment::join('wallet_users', 'shipments.user_id', '=', 'wallet_users.user_id')
                ->where('shipments.id', $pending_payment_shipment->shipment_id)
                ->select('shipments.*', 'wallet_users.wallet_id as wallet_user_id')
                ->first();
                if($pending_payment_shipment->type == 3) {
                    $log_bid =  AdminFinanceController::isWalletLogUpdated($pending_payment_shipment->shipment_id);
                    if(!$log_bid) {
                        $requestPayload = [
                            "wallet_id" => $shipment->wallet_user_id,
                            "client_id" =>  $user_id, 
                            "reference_id" => (string) Str::uuid(), 
                            "shipment_id" => $shipment->tracking_number, 
                            "amount" => $shipment->amount,
                            "order_created_date" => $shipment->created_at,
                            "charges" => [
                                'weight_charges' =>  intval($shipment->weight_charges),
                                'fuel_surcharge' =>  intval($shipment->fuel_surcharge),
                                'faf_charges' => $shipment->faf_charges_data ? intval($shipment->faf_charges_data->faf_charges) : 0,
                                'arrival_charges_gst' => intval($pending_payment_shipment->gst),
                                'arrival_sms_charges' => intval($pending_payment_shipment->sms_charges)
                            ]
                        ]; 
                        $this->arrival_shipment_logs($requestPayload,  $shipment->id);
                    }
        
                } elseif(in_array($pending_payment_shipment->type, [0, 1, 2])) {
                    $log_bid = AdminFinanceController::isWalletLogUpdated($pending_payment_shipment->shipment_id);
                    if(!$log_bid) {
                        $requestPayload = [
                            "wallet_id" =>$shipment->wallet_id,
                            "client_id" => $shipment->user_id, 
                            "reference_id" => (string) Str::uuid(), 
                            "shipment_id" => $shipment->tracking_number, 
                            "amount" => $shipment->amount, 
                            "order_created_date" => $shipment->created_at,
                            "charges" => [
                                'weight_charges' =>  0
                            ]
                        ];
                        $this->arrival_shipment_logs($requestPayload,  $shipment->id);
                    } 
                }
            }
        }
    }
}
