<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Controllers\Shippers\ShipperShipmentBookController;
use App\Http\Models\Shipment;
use App\ShipmentAdditionalCharges;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class TestEmailCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email_check {payment_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description ';

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function handle()
    {
        if ($this->hasArgument('payment_id')) {
            $paymentId = explode(',', $this->argument('payment_id'));
            $records = DB::table('done_payment_shipments')
                ->whereIn('done_payment_id', $paymentId)
                ->select('id', 'wht', 'cod_sst', 'payable', 'shipment_id','type','charges','gst','done_payment_id')
                ->get();

            foreach ($records as $record) {
                if ($record->type == 0) {
                    $shipment_id = $record->shipment_id;
                    $shipment = Shipment::find($shipment_id);
                    $amount = $shipment->amount;
                    $get_wallet_charges_if_applicable = ShipmentAdditionalCharges::get_wallet_charges_if_applicable($shipment_id);
                    if ($get_wallet_charges_if_applicable) {
                        $wallet_charges = ShipmentAdditionalCharges::fetch_wallet_charges($shipment_id);
                    } else {
                        $wallet_charges = 0;
                    }

                    $wht = 0;
                    $cod_sst = 0;
                    $charges = $record->charges;
                    $gst = $record->gst;
                    if (in_array($shipment->shipper_status_id, [14, 30, 31, 36, 37]) && $shipment->amount > 0) {
                        $wht = AdminFinanceController::wht($shipment->user_id, $shipment->amount, $shipment->packaging_material_request, $shipment->id);
                        $cod_sst = AdminFinanceController::cod_sst($shipment->user_id, $shipment->amount, $shipment->packaging_material_request, $shipment->id);
                    }

                    $payable = $amount - ($charges + $gst + $wallet_charges + $wht + $cod_sst);
                    $currentPayable = $record->payable;
                    DB::table('done_payment_shipments')
                        ->where('id', $record->id)
                        ->update(['payable' =>$payable, 'cod_sst' => $cod_sst, 'wht' => $wht]);

                    DB::select('CALL update_done_payment_statistics(?)', $record->done_payment_id);
                }


            }

        }

    }


}
