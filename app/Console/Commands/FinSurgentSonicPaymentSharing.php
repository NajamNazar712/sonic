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
use App\Http\Controllers\Admins\AdminFinanceController;
use App\ShipmentAdditionalCharges;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Http\Traits\FinSurgentLogTrait;
use DB;

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

        $date = Carbon::now()->subHours(2)->toDateTimeString();
        $pending_payment_wallet_users = PendingPayment::join('wallet_users as u', function ($join) use ($date) {
            $join->on('u.user_id', '=', 'pending_payments.user_id')
                ->where('u.substitute_user_id', '0')
                ->where('u.created_at', '<=', $date);
        })->select(['pending_payments.*', 'u.wallet_id', 'u.user_id as user_id'])->get();

       
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

            foreach ($pending_payment_shipment_ids as $pending_payment_shipment) {
                if ($pending_payment_shipment) {

                    $shipment = Shipment::find($pending_payment_shipment->shipment_id);
                    if (!empty($shipment)) {
                        $this->updatePaymentBeforeLog($shipment, $pending_payment_shipment);

                        $requestPayload = [
                            "client_id" => $pending_payment->user_id,
                            "wallet_id" => $pending_payment->wallet_id,
                            "reference_id" => (string)Str::uuid(),
                            "shipment_id" => $shipment->tracking_number,
                            "amount" => $shipment->amount,
                            "order_created_date" => $shipment->created_at,
                            // "charges" => [
                            //     'weight_charges' => floatval($shipment->weight_charges),
                            //     'fuel_surcharge' => floatval($shipment->fuel_surcharge),
                            //     'faf_charges' => floatval($faf_charges),
                            //     'arrival_charges_gst' => floatval($gst),
                            //     'arrival_sms_charges' => floatval($pending_payment_shipment->sms_charges)
                            // ] // removed after new requierment
                        ];
                        $this->arrival_shipment_logs($requestPayload, $pending_payment_shipment);
                    }
                }
            }
        }
        return Command::SUCCESS;
    }
}