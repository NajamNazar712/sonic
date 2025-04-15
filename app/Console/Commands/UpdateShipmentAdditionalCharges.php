<?php

namespace App\Console\Commands;

use App\Http\Models\DonePaymentShipment;
use App\Http\Models\PendingPaymentShipment;
use App\ShipmentAdditionalCharges;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateShipmentAdditionalCharges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:shipment_additional_charges';

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
        $startDate =  Carbon::now()->startOfYear()->format('Y-m-d 00:00:00');
        $endDate = Carbon::now()->format('Y-m-d 23:59:59');
        $total_array = PendingPaymentShipment::
            join('shipments','shipments.id','pending_payment_shipments.shipment_id')
            ->join('wallet_users','wallet_users.user_id','shipments.user_id')
            ->leftjoin('shipment_additional_charges','shipment_additional_charges.shipment_id','pending_payment_shipments.shipment_id')
            ->whereNull('shipment_additional_charges.id')->pluck('pending_payment_shipments.shipment_id')->toArray();

        $total_array2 = DonePaymentShipment::
             join('shipments','shipments.id','done_payment_shipments.shipment_id')
            ->join('done_payments','done_payments.id','done_payment_shipments.done_payment_id')
            ->join('wallet_users','wallet_users.user_id','shipments.user_id')
            ->leftjoin('shipment_additional_charges','shipment_additional_charges.shipment_id','done_payment_shipments.shipment_id')
            ->whereNull('shipment_additional_charges.id')
            ->whereBetween('done_payments.updated_at',[$startDate,$endDate])
            ->pluck('done_payment_shipments.shipment_id')
            ->toArray();

        $array_merge = array_unique(array_merge($total_array,$total_array2));

        if(count($total_array) > 0){
            ShipmentAdditionalCharges::additional_charges_apply($total_array);
        }

    }
}
