<?php

namespace App\Jobs;

use App\Http\Controllers\FingaIntegrationController;
use App\ShipmentsArchieve;
use Carbon\Carbon;
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
use App\Jobs\ShipmentStatusSharingWithWallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
        $this->queue = 'wallet_signup_pending_record_log';
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
        $pending_payment_ids = PendingPayment::with('pending_payment_shipments')->join('wallet_users as u', function ($join) {
            $join->on('u.user_id', '=', 'pending_payments.user_id')
               ->where('u.substitute_user_id', '0');
       })->where('pending_payments.user_id', $user_id)
        ->pluck('pending_payments.id');

        $api = config('app.FINGA_URL');
        // $token = FingaIntegrationController::getToken($api);
        // $token_time = Carbon::now();
        foreach ($pending_payment_ids as $pending_payment){
            
            $pending_payment_shipments = PendingPaymentShipment::leftJoin('finja_log_settlement_records as sac', 'pending_payment_shipments.shipment_id', '=', 'sac.shipment_id')
            ->where('pending_payment_shipments.pending_payment_id', $pending_payment)
            ->whereIn('pending_payment_shipments.type', [0,1,2,3])
            ->where(function ($query) {
                $query->whereNull('sac.id') 
                    ->orWhere('sac.wallet_log_updated', 0);
            })
            ->select(['pending_payment_shipments.*'])
            ->get();
            $pending_payment_shipments->chunk(50)->each(function ($chunkedShipments) use($api,$user_id,$pending_payment) {
                foreach ($chunkedShipments as $pending_payment_shipment) {
                    // if ($token_time->diffInMinutes(Carbon::now()) >= 4) {
                    //     $token = FingaIntegrationController::getToken($api);
                    //     $token_time = Carbon::now();
                    // }
                    $token = FingaIntegrationController::getToken($api);

                    $shipment = Shipment::leftJoin('wallet_users as u', function ($join) {
                        $join->on('u.user_id', '=', 'shipments.user_id')
                            ->where('u.substitute_user_id', '0');
                    })
                        ->where('shipments.id', $pending_payment_shipment->shipment_id)
                        ->select('shipments.*', 'u.wallet_id as wallet_user_id')
                        ->first();

                    if (empty($shipment)) {
                        $shipment = ShipmentsArchieve::leftJoin('wallet_users as u', function ($join) {
                            $join->on('u.user_id', '=', 'shipments_archive.user_id')
                                ->where('u.substitute_user_id', '0');
                        })
                        ->where('shipments_archive.id', $pending_payment_shipment->shipment_id)
                        ->select('shipments_archive.*', 'u.wallet_id as wallet_user_id')
                        ->first();

                        if (!empty($shipment)) {
                            $data = $shipment->toArray();

                            // Filter only the columns that exist in `shipments` table
                            $columns = Schema::getColumnListing('shipments');
                            $filteredData = collect($data)->only($columns)->toArray();

                            DB::table('shipments')->insert($filteredData);
                        }
                    }
                    if ($pending_payment_shipment->type == 3) {
                        $log_bid = AdminFinanceController::isWalletLogUpdated($pending_payment_shipment->shipment_id);
                        if (!$log_bid) {
                            $requestPayload = [
                                "wallet_id" => $shipment->wallet_user_id,
                                "client_id" => $user_id,
                                "reference_id" => (string)Str::uuid(),
                                "shipment_id" => $shipment->tracking_number,
                                "amount" => $shipment->amount,
                                "order_created_date" => $shipment->created_at,
                                // "charges" => [
                                //     'weight_charges' =>  floatval($shipment->weight_charges),
                                //     'fuel_surcharge' =>  floatval($shipment->fuel_surcharge),
                                //     'faf_charges' => $shipment->faf_charges_data ? floatval($shipment->faf_charges_data->faf_charges) : 0,
                                //     'arrival_charges_gst' => floatval($pending_payment_shipment->gst),
                                //     'arrival_sms_charges' => floatval($pending_payment_shipment->sms_charges)
                                // ]remvoed as per new requirement
                            ];
                            $this->arrival_shipment_logs($requestPayload, $shipment->id, null, $token);
                        }

                    } elseif (in_array($pending_payment_shipment->type, [0, 1])) {
                        $log_bid = AdminFinanceController::isWalletLogUpdated($pending_payment_shipment->shipment_id);
                        if (!$log_bid) {
                            $requestPayload = [
                                "wallet_id" => $shipment->wallet_user_id,
                                "client_id" => $shipment->user_id,
                                "reference_id" => (string)Str::uuid(),
                                "shipment_id" => $shipment->tracking_number,
                                "amount" => $shipment->amount,
                                "order_created_date" => $shipment->created_at,
                                // "charges" => [
                                //     'weight_charges' =>  0
                                // ] remvoed as per new requirement
                            ];
                            $this->arrival_shipment_logs($requestPayload, $shipment->id, null, $token);
                        }
                    } elseif ($pending_payment_shipment->type == 2) {
                        $log_bid = AdminFinanceController::isWalletLogUpdated($pending_payment_shipment->shipment_id);
                        if (!$log_bid) {

                            $status_array = [14, 25, 31, 38, 37, 18, 20];
                            if (in_array($shipment->shipper_status_id, $status_array)) {
                                $cod_amount = 0;
                            } else {
                                $cod_amount = $shipment->amount;
                            }
                            $requestPayload = [
                                "wallet_id" => $shipment->wallet_user_id,
                                "client_id" => $shipment->user_id,
                                "reference_id" => (string)Str::uuid(),
                                "shipment_id" => $shipment->tracking_number,
                                "amount" => $cod_amount,
                                "order_created_date" => $shipment->created_at,
                                // "charges" => [
                                //     'weight_charges' =>  0
                                // ] remvoed as per new requirement
                            ];
                            $this->arrival_shipment_logs($requestPayload, $shipment->id, null, $token);
                        }
                    }

                    $log_sent = AdminFinanceController::isWalletLogUpdated($shipment->id);
                    if (in_array($shipment->shipper_status_id, [5, 8, 13, 14, 18, 20, 36, 37, 30, 21, 22, 23, 24, 25, 44, 47, 48, 57, 60, 75, 76, 77]) && $log_sent && $pending_payment_shipment->type != 2) { // exclude shipment of adjustment to send current status because it may send change cod amount also
                        $data = [
                            'tracking_number' => $shipment->tracking_number,
                            'status' => $shipment->shipper_status_id,
                            'shipment_id' => $shipment->id
                        ];
                        ShipmentStatusSharingWithWallet::dispatch($data, 1, $token);
                    }
                }
                sleep(20);
            });
        }
    }
}
