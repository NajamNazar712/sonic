<?php

namespace App\Observers;

use App\Http\Models\Shipment;
use App\Http\Models\WalletUser;
use App\Jobs\CODAmountChangeSendToWallet;
use Illuminate\Support\Str;
use App\Http\Traits\FinSurgentLogTrait;
use App\Http\Controllers\Admins\AdminFinanceController;
use Log;
class ShipmentAmountChangeObserver
{
    use FinSurgentLogTrait;

    /**
     * Handle the Shipment "updated" event.
     *
     * @param  \App\Models\Shipment  $shipment
     * @return void
     */
    public function updated(Shipment $shipment)
    {
        if (!$shipment->wasChanged('amount') || $shipment->shipper_status_id == 1) {
           
            return; // Only run if cod_amount has changed
        }
        
        if(WalletUser::where('user_id', $shipment->user_id)->where('substitute_user_id', 0)->exists()) {

            $log_bid = AdminFinanceController::isWalletLogUpdated($shipment->id);
            if(!$log_bid) {
                $requestPayload = [
                    "shipmentId" => $shipment->id,
                    "wallet_id" => $shipment->user->wallet->wallet_id,
                    "client_id" => $shipment->user->id, 
                    "reference_id" => (string) Str::uuid(), 
                    "shipment_id" => $shipment->tracking_number, 
                    "amount" => $shipment->amount, 
                    "order_created_date" => $shipment->created_at,
                ]; 
                $this->arrival_shipment_logs($requestPayload, null, $shipment->id);
            } 

            $data = [
                'shipment_id' => $shipment->id,
                'tracking_number' => $shipment->tracking_number,
                'client_id' => $shipment->user->id,
                'wallet_id' => $shipment->user->wallet->wallet_id,
                'amount' => $shipment->amount
            ];
            CODAmountChangeSendToWallet::dispatch($data);
        }
    }

}
