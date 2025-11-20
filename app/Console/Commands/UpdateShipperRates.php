<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Controllers\Webhook\InitialChargesWebhookController;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentServicesCharges;
use App\Models\CorporateUserOnDeliveredInvoice;
use App\ShipmentAdditionalCharges;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class UpdateShipperRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shipper:update_rates_and_charges';

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
        $array = [
            "22338156017243",
            "22338155739627",
            "22331556193719",
            "22331556022493",
            "22329255666080",
            "22329255663323",
            "22328855636243",
            "22328456679216",
            "22328455728222",
            "22328356613601",
            "22328355569373",
            "22327256169045",
            "22327156500864",
            "22327156207270",
            "22325156508144",
            "22325155905009",
            "22325155748248",
            "22324156196606",
            "22323856153798",
            "22323755992220",
            "22322356652303",
            "22322356513079",
            "22322356344458",
            "22322356235225",
            "22322356169197",
            "22322356108080",
            "22322356013615",
            "22322355747959",
            "22321055739967",
            "22320256513108",
            "22320256383530",
            "22320255938362",
            "22320255861551",
            "22320255737137",
            "22320255580085",
            "22320255569124",
            "22318456383111",
            "22318456180770",
            "22317456518177",
            "22317456363646",
            "22317456196573",
            "22317456129343",
            "22317456030294",
            "22317455775585",
            "22317455771235",
            "22317455747804",
            "22317256661997",
            "22317256637491",
            "22317256013675",
            "22317255855730",
            "22317255795140",
            "22315956486004",
            "22315956485981",
            "22314456169036",
            "22314456013509",
            "22314456013254",
            "22314456012974",
            "22314456012949",
            "22314455751381",
            "22314455670395",
            "22314455670353",
            "22311956028792",
            "22310155766226",
        ];
        $shipments = Shipment::whereIn('tracking_number',$array)->where('packaging_material_request',0)->get();

        foreach ($shipments as $ship){
            $shipment = Shipment::find($ship->id);
            $shipment_id = $shipment->id;
            if ($shipment->packaging_material_request == 0 && $shipment->shipment_type == 1) {
                if ($shipment->booking_type_id == 4) {
                    ShipmentChargesController::walkin_weight($shipment_id);
                } else {
                    ShipmentChargesController::weight($shipment_id);
                    if($shipment->booking_type_id == 5){
                        ShipmentChargesController::reverse_pickup($shipment_id);
                    }
                    if ($shipment->business_category_id == 1) {
                        ShipmentChargesController::cash_handling($shipment_id);
                        ShipmentChargesController::insurance($shipment_id);
                        ShipmentChargesController::fuel_surcharge($shipment_id);
                        ShipmentChargesController::faf_charges($shipment_id);
                    } else {
                        ShipmentChargesController::international_fuel_surcharge($shipment_id);
                        ShipmentChargesController::international_faf_charges($shipment_id);
                    }
                }

                if ($shipment->walk_in_status == 0) {
                    InitialChargesWebhookController::webhook_subscription($shipment_id);
                }
            }

            $type = 3;
            if($shipment->shipper_status_id == 25){
                $type = 1;
            }else if($shipment->shipper_status_id == 14){
                $type = 0;
            }

            if (empty($shipment)) {
                $shipment = Shipment::find($shipment_id);
            }

            $delivered_invoice_users = CorporateUserOnDeliveredInvoice::where('status', 1)
                ->pluck('user_id')
                ->toArray();

            if(in_array($type, [3,1]) && in_array($shipment->user_id , $delivered_invoice_users)) {
                return;
            }

            $faf_charges = ShipmentAdditionalCharges::fetch_faf_charges($shipment_id);
            $check_arrival = ShipmentAdditionalCharges::check_additional_charges($shipment_id,true);
            $service_charges = ShipmentServicesCharges::where('shipment_id', $shipment_id);
            $get_wallet_charges_if_applicable = ShipmentAdditionalCharges::get_wallet_charges_if_applicable($shipment_id);
            if($get_wallet_charges_if_applicable) {
                $wallet_charges = ShipmentAdditionalCharges::fetch_wallet_charges($shipment_id);
            }else{
                $wallet_charges = 0;
            }
            $transaction_id =  (string) Str::uuid();
            if ($service_charges->exists()) {
                $service_charges = $service_charges->first();
                $service_charges = $service_charges->reverse_pickup_charges;
            } else {
                $service_charges = 0;
            }

            $crs = false;

            $amount = 0;
            if ($shipment->shipment_type == 1) {
                if (!$shipment->packaging_material_request) {
                    if ($type == 0) {
                        $charges = $shipment->weight_charges + $shipment->cash_handling_charges + $shipment->insurance_charges + $shipment->fuel_surcharge + $shipment->replacement_charges + $shipment->try_and_buy_charges + $shipment->intercept_charges + $shipment->nsa_osa_charges + $shipment->esc_charges + $service_charges + $faf_charges;

                        if ($shipment->business_category_id == 1) {
                            $gst = ROUND(($charges * AdminFinanceController::gst($shipment->pickup_address->city->zone_id, $shipment->pickup_address->city->id)), 2, PHP_ROUND_HALF_DOWN);
                        } else {
                            $gst = ROUND(($charges * AdminFinanceController::international_gst()), 2, PHP_ROUND_HALF_DOWN);
                        }
                        $wht = 0;
                        $cod_sst = 0;
                        if (in_array($shipment->shipper_status_id, [14, 31, 36, 37]) && $shipment->amount > 0) {
                            $wht = AdminFinanceController::wht($shipment->user_id, $shipment->amount, $shipment->packaging_material_request, $shipment->id);
                            $cod_sst = AdminFinanceController::cod_sst($shipment->user_id, $shipment->amount, $shipment->packaging_material_request, $shipment->id);
                        }

                        $payable = $amount - ($charges + $gst);
                    } else if ($type == 3) {
                        $charges = $shipment->weight_charges + $shipment->fuel_surcharge + $faf_charges;
                        if ($shipment->business_category_id == 1) {
                            $gst = ROUND(($charges * AdminFinanceController::gst($shipment->pickup_address->city->zone_id, $shipment->pickup_address->city->id)), 2, PHP_ROUND_HALF_DOWN);
                        } else {
                            $gst = ROUND(($charges * AdminFinanceController::international_gst()), 2, PHP_ROUND_HALF_DOWN);
                        }

                        $wht = 0;
                        $cod_sst = 0;
                        $payable = 0 - ($charges + $gst);
                        $amount = 0;
                    } else {
                        $amount = 0;
                        $charges = $shipment->weight_charges + $shipment->insurance_charges + $shipment->return_charges + $shipment->fuel_surcharge + $shipment->intercept_charges + $shipment->nsa_osa_charges + $faf_charges;

                        if ($shipment->business_category_id == 1) {
                            $gst = ROUND(($charges * AdminFinanceController::gst($shipment->pickup_address->city->zone_id, $shipment->pickup_address->city->id)), 2, PHP_ROUND_HALF_DOWN);
                        } else {
                            $gst = ROUND(($charges * AdminFinanceController::international_gst()), 2, PHP_ROUND_HALF_DOWN);
                        }


                        $wht = 0;
                        $cod_sst = 0;
                        $payable = 0 - ($charges + $gst);
                    }
                } else {
                    $charges = $shipment->packaging_material_charges;
                    $gst = 0;

                    $wht = 0;
                    $cod_sst = 0;

                    $payable = $amount - ($charges + $gst + $wht + $cod_sst);
                }
            }

            AdminFinanceController::add_adjustment($shipment->id, $payable, 'Shipment Adjustment Charges', 15);


        }
    }
}
