<?php

namespace Database\Seeders;

use App\CronDonePayment;
use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Controllers\Webhook\InitialChargesWebhookController;
use App\Http\Models\Shipment;
use Illuminate\Database\Seeder;
use \App\ShipmentsArchieve;
use Carbon\Carbon;

class UpdateArrivalChargesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $startDate =  Carbon::now()->subDays(2)->format('Y-m-d 00:00:00');
        $endDate = Carbon::now()->format('Y-m-d 23:59:59');

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
                    AdminFinanceController::update_payment($shipment_id, 3);
                }
            }
        }
    }
}
