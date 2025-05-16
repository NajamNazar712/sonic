<?php

namespace App\Console\Commands;

use App\Jobs\WalletSettlementFromDonePayments;
use Carbon\Carbon;
use Illuminate\Console\Command;
use DB;
use App\Jobs\WalletBulkSettlementFromDonePayments;

class RerunWalletSettlement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rerun_wallet_settlement';

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
        $startTime = Carbon::now()->subDays(10)->format('Y-m-d H:i:s');
        $endTime = Carbon::now()->subHours(6)->format('Y-m-d H:i:s');

        $donePayments = DB::table('done_payment_shipments')
            ->leftJoin('done_payments', 'done_payments.id', '=', 'done_payment_shipments.done_payment_id')
            ->leftJoin('shipments as s', 's.id', '=', 'done_payment_shipments.shipment_id')
            ->leftJoin('finja_log_settlement_records as sac', 'done_payment_shipments.shipment_id', '=', 'sac.shipment_id')
            ->where('done_payments.status', 3)
            ->where('done_payments.is_wallet_payment', 1)
            ->whereBetween('done_payments.updated_at', [$startTime,$endTime])
            ->whereIn('done_payment_shipments.wallet_action_bid', [0, 1, 2])
            ->groupBy('done_payment_shipments.done_payment_id')
            ->select('done_payments.*')
            ->get();

        $donePayments->chunk(5)->each(function ($chunkedShipments)  {
            foreach ($chunkedShipments as $value){
                //WalletSettlementFromDonePayments::dispatch($value->id,  346);
                WalletBulkSettlementFromDonePayments::dispatch($value->id,  346);
            }

        });


    }
}
