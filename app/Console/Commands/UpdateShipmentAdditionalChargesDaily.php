<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Models\Shipment;
use App\ShipmentAdditionalCharges;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateShipmentAdditionalChargesDaily extends Command
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
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $startDate =  Carbon::now()->subDays(1)->format('Y-m-d 00:00:00');
        $endDate = Carbon::now()->format('Y-m-d 23:59:59');


        $shipments = Shipment::leftJoin('shipment_additional_charges', 'shipment_additional_charges.shipment_id', '=', 'shipments.id')
            ->leftJoin('users', 'users.id', '=', 'shipments.user_id')
            ->leftJoin('pending_payment_shipments', function($join) {
                $join->on('pending_payment_shipments.shipment_id', '=', 'shipments.id')
                    ->where('pending_payment_shipments.type', 3);
            })
            ->whereBetween('shipments.created_at', [$startDate, $endDate])
            ->where('users.account_type_id', 1)
            ->whereNull('shipment_additional_charges.shipment_id')
            ->whereNotNull('pending_payment_shipments.id')
            ->pluck('pending_payment_shipments.shipment_id')->toArray();



        $shipments2 = Shipment::leftJoin('shipment_additional_charges', 'shipment_additional_charges.shipment_id', '=', 'shipments.id')
            ->leftJoin('users', 'users.id', '=', 'shipments.user_id')
            ->leftJoin('pending_invoice_shipments', 'pending_invoice_shipments.shipment_id', '=', 'shipments.id')
            ->whereBetween('shipments.created_at', [$startDate, $endDate])
            ->where('users.account_type_id', 2)
            ->where('pending_invoice_shipments.type', 3)
            ->whereNull('shipment_additional_charges.shipment_id')
            ->whereNotNull('pending_invoice_shipments.id')
            ->pluck('pending_invoice_shipments.shipment_id')->toArray();


        $total_array = array_unique(array_merge($shipments,$shipments2));

        if(count($total_array) > 0){
            ShipmentAdditionalCharges::additional_charges_apply($total_array,true);
        }


    }
}
