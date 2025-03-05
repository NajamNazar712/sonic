<?php

namespace App\Console\Commands;

use App\Http\Controllers\FingaIntegrationController;
use App\Http\Models\Shipment;
use App\Jobs\WalletSignUpLPendingRecordLogs;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class WalletLogForcePush extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rerun:wallet_log_re_push {id}';

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
        $api = config('app.FINGA_URL');
        $token = FingaIntegrationController::getToken($api);
        $token_time = Carbon::now();
        $shipment_id = $this->argument('id');
        $shipment_log_not_sent = Shipment::leftJoin('finja_log_settlement_records as sac', 'shipments.id', '=', 'sac.shipment_id')
            ->join('wallet_users as u', function ($join) {
                $join->on('u.user_id', '=', 'shipments.user_id')
                    ->where('u.substitute_user_id', '0');
            })
            ->where(function ($query) {
                $query->whereNull('sac.id')
                    ->orWhere('sac.wallet_log_updated', 0);
            })->where('shipments.id', $shipment_id)
            ->select(['shipments.*', 'u.wallet_id'])
            ->first();

        if(!empty($shipment_log_not_sent)){
            $logPayload = [
                "client_id" => $shipment_log_not_sent->user_id,
                "wallet_id" => $shipment_log_not_sent->wallet_id,
                "reference_id" => (string)Str::uuid(),
                "shipment_id" => $shipment_log_not_sent->tracking_number,
                "amount" => $shipment_log_not_sent->amount,
                "order_created_date" => $shipment_log_not_sent->created_at,
            ];
            $this->arrival_shipment_logs($logPayload, null,$shipment_log_not_sent->id,$token);
        }
    }
}
