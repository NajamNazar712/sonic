<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Controllers\Webhook\InitialChargesWebhookController;
use App\ShipmentAdditionalCharges;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Models\Shipment;
class UpdateArrivalChargesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:zero_arrival_charges';

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

        $startDate =  Carbon::now()->subDay(2)->format('Y-m-d 05:30:00');
        $endDate = Carbon::now()->format('Y-m-d 23:59:59');

        $shipments = Shipment::leftJoin('shipment_additional_charges', 'shipment_additional_charges.shipment_id', '=', 'shipments.id')
            ->leftJoin('users', 'users.id', '=', 'shipments.user_id')
            ->leftJoin('pending_payment_shipments', function($join) {
                $join->on('pending_payment_shipments.shipment_id', '=', 'shipments.id')
                    ->where('pending_payment_shipments.type', 3);
            })
            ->whereBetween('pending_payment_shipments.created_at', [$startDate, $endDate])
            ->where('users.account_type_id', 1)
            ->where('pending_payment_shipments.type', 3)

            ->where(function ($query){
                $query->whereNull('shipment_additional_charges.shipment_id');
//                    ->orWhere('shipment_additional_charges.faf_charges', 0);
            })
            ->whereNotNull('pending_payment_shipments.id')
            ->pluck('pending_payment_shipments.shipment_id')->toArray();


        $shipments2 = Shipment::leftJoin('shipment_additional_charges', 'shipment_additional_charges.shipment_id', '=', 'shipments.id')
            ->leftJoin('users', 'users.id', '=', 'shipments.user_id')
            ->leftJoin('pending_invoice_shipments', 'pending_invoice_shipments.shipment_id', '=', 'shipments.id')
            ->whereBetween('pending_invoice_shipments.created_at', [$startDate, $endDate])
            ->where('users.account_type_id', 2)
            ->where('pending_invoice_shipments.type', 3)
            ->where(function ($query){
                $query->whereNull('shipment_additional_charges.shipment_id');
//                    ->orWhere('shipment_additional_charges.faf_charges', 0);
            })
            ->whereNotNull('pending_invoice_shipments.id')
            ->pluck('pending_invoice_shipments.shipment_id')->toArray();


        $total_array = array_unique(array_merge($shipments,$shipments2));

        if(count($total_array) > 0){
            ShipmentAdditionalCharges::additional_charges_apply($total_array,true);
        }


        $data = Shipment::select('shipments.id','shipments.shipper_status_id','shipments.booking_type_id','shipments.shipment_type','shipments.packaging_material_request','shipments.business_category_id','shipments.walk_in_status')
            ->join('pending_payment_shipments', 'pending_payment_shipments.shipment_id', '=', 'shipments.id')
            ->join('users', 'users.id', '=', 'shipments.user_id')
            ->where('pending_payment_shipments.type', 3)
            ->whereBetween('pending_payment_shipments.created_at', [$startDate, $endDate])
            ->where('users.account_type_id', 1)
            ->whereIn('shipments.shipper_status_id',[2,5,3,8,14])
            ->where('pending_payment_shipments.payable', 0)->get();
        foreach ($data as $shipment){
            if($shipment){
                $shipment_id = $shipment->id;
                if ($shipment->booking_type_id != 4) {

//                    $shipment_data = Shipment::find($shipment_id);
//                    if ($shipment_data->packaging_material_request == 0 && $shipment_data->shipment_type == 1) {
//                        if ($shipment_data->booking_type_id == 4) {
//                            ShipmentChargesController::walkin_weight($shipment_id);
//                        } else {
//                            ShipmentChargesController::weight($shipment_id);
//                            if($shipment_data->booking_type_id == 5){
//                                ShipmentChargesController::reverse_pickup($shipment_id);
//                            }
//                            if ($shipment_data->business_category_id == 1) {
//                                ShipmentChargesController::cash_handling($shipment_id);
//                                ShipmentChargesController::insurance($shipment_id);
//                                ShipmentChargesController::fuel_surcharge($shipment_id);
//                                ShipmentChargesController::faf_charges($shipment_id);
//                            } else {
//                                ShipmentChargesController::international_fuel_surcharge($shipment_id);
//                                ShipmentChargesController::international_faf_charges($shipment_id);
//                            }
//                        }
//                    }

                    AdminFinanceController::update_payment($shipment_id, 3);
                }
            }
        }
    }
}
