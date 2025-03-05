<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Controllers\FingaIntegrationController;
use App\Http\Models\PendingPayment;
use App\Http\Models\PendingPaymentShipment;
use App\Http\Models\Shipment;
use App\Jobs\ShipmentStatusSharingWithWallet;
use App\Jobs\WalletSignUpLPendingRecordLogs;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use DB;
use App\Http\Traits\FinSurgentLogTrait;
class WalletLogForcePush extends Command
{
    use FinSurgentLogTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rerun:wallet_log_re_push';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Re-run WalletLog in case failed job for a specific ID';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $donePayments = DB::table('done_payment_shipments')
            ->select(
                'done_payment_shipments.*',
                'u.wallet_id as wallet_user_id',
                'sac.wallet_log_updated'
                ,'shipments.user_id'
                ,'shipments.tracking_number'
                ,'shipments.amount as shipment_amount'
                ,'shipments.created_at as shipment_date'
                ,'shipments.shipper_status_id'
//                DB::raw('(SELECT MAX(dps.done_payment_id)
//                  FROM done_payment_shipments AS dps
//                  WHERE dps.done_payment_id = done_payment_shipments.done_payment_id
//                  AND dps.wallet_action_bid = 3) AS payment_id')
            )
            ->join('done_payments', 'done_payments.id', '=', 'done_payment_shipments.done_payment_id')
            ->join('shipments','shipments.id','done_payment_shipments.shipment_id')
            ->leftJoin('finja_log_settlement_records as sac', 'done_payment_shipments.shipment_id', '=', 'sac.shipment_id')
            ->leftjoin('wallet_users as u', function ($join) {
                $join->on('u.user_id', '=', 'shipments.user_id')
                    ->where('u.substitute_user_id', '0');
            })
            ->where('done_payments.is_wallet_payment', 1)
//            ->where('done_payments.id', 1561172)
            ->havingRaw('COALESCE(sac.wallet_log_updated, NULL) IS NULL')
            ->get();
        $api = config('app.FINGA_URL');
        $token = FingaIntegrationController::getToken($api);
        $token_time = Carbon::now();
        foreach ($donePayments as $done_payment_shipment) {

            if ($token_time->diffInMinutes(Carbon::now()) >= 4) {
                $token = FingaIntegrationController::getToken($api);
                $token_time = Carbon::now(); // Update the token time
            }
            if ($done_payment_shipment->type == 3) {
                $log_bid = AdminFinanceController::isWalletLogUpdated($done_payment_shipment->shipment_id);
                if (!$log_bid) {
                    $requestPayload = [
                        "wallet_id" => $done_payment_shipment->wallet_user_id,
                        "client_id" => $done_payment_shipment->user_id,
                        "reference_id" => (string)Str::uuid(),
                        "shipment_id" => $done_payment_shipment->tracking_number,
                        "amount" =>  floatval($done_payment_shipment->shipment_amount),
                        "order_created_date" => $done_payment_shipment->shipment_date
                    ];
                    $this->arrival_shipment_logs($requestPayload, $done_payment_shipment->shipment_id, null, $token);
                }

            } elseif (in_array($done_payment_shipment->type, [0, 1])) {
                $log_bid = AdminFinanceController::isWalletLogUpdated($done_payment_shipment->shipment_id);
                if (!$log_bid) {
                    $requestPayload = [
                        "wallet_id" => $done_payment_shipment->wallet_user_id,
                        "client_id" => $done_payment_shipment->user_id,
                        "reference_id" => (string)Str::uuid(),
                        "shipment_id" => $done_payment_shipment->tracking_number,
                        "amount" =>  floatval($done_payment_shipment->shipment_amount),
                        "order_created_date" => $done_payment_shipment->shipment_date
                    ];
                    $this->arrival_shipment_logs($requestPayload, $done_payment_shipment->shipment_id, null, $token);
                }
            } elseif ($done_payment_shipment->type == 2) {
                $log_bid = AdminFinanceController::isWalletLogUpdated($done_payment_shipment->shipment_id);
                if (!$log_bid) {

                    $status_array = [14, 25, 31, 38, 37, 18, 20];
                    if (in_array($done_payment_shipment->shipper_status_id, $status_array)) {
                        $cod_amount = 0;
                    } else {
                        $cod_amount = $done_payment_shipment->amount;
                    }
                    $requestPayload = [
                        "wallet_id" => $done_payment_shipment->wallet_user_id,
                        "client_id" => $done_payment_shipment->user_id,
                        "reference_id" => (string)Str::uuid(),
                        "shipment_id" => $done_payment_shipment->tracking_number,
                        "amount" =>  $cod_amount,
                        "order_created_date" => $done_payment_shipment->shipment_date
                    ];
                    $this->arrival_shipment_logs($requestPayload, $done_payment_shipment->shipment_id, null, $token);
                }
            }

            $log_sent = AdminFinanceController::isWalletLogUpdated($done_payment_shipment->shipment_id);
            if (in_array($done_payment_shipment->shipper_status_id, [5, 8, 13, 14, 18, 20, 36, 37, 30, 21, 22, 23, 24, 25, 44, 47, 48, 57, 60, 75, 76, 77]) && $log_sent && $done_payment_shipment->type != 2) { // exclude shipment of adjustment to send current status because it may send change cod amount also
                $data = [
                    'tracking_number' => $done_payment_shipment->tracking_number,
                    'status' => $done_payment_shipment->shipper_status_id,
                    'shipment_id' => $done_payment_shipment->shipment_id
                ];
                ShipmentStatusSharingWithWallet::dispatch($data, 1, $token);
            }
        }

    }
}
