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
            "223683755089383",
            "223354655089379",
            "22350255151252",
            "22350255151251",
            "22346355151417",
            "22335755206287",
            "22333255151422",
            "22332355151242",
            "22331155208388",
            "22331155151432",
            "22328855151425",
            "22328355151250",
            "22327155151234",
            "22327155149310",
            "22327055206383",
            "22326955151249",
            "22325755151244",
            "22324555149309",
            "22322355422211",
            "22322355422209",
            "22322355151426",
            "22322355151424",
            "22322355151423",
            "22322355151232",
            "22322355089385",
            "22321055151433",
            "22320555151431",
            "22320255206598",
            "22320255151429",
            "22320255151421",
            "22320255151416",
            "22320255151246",
            "22320255151240",
            "22320255151237",
            "22320255089382",
            "22320155151427",
            "22318655151243",
            "22318255151418",
            "22317955206289",
            "22317455151428",
            "22317455151419",
            "22317455151245",
            "22317455089380",
            "22317255422210",
            "22314455422207",
            "22314455149308",
            "22313955151430",
            "22311055206643",
            "22310755151238",
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
                ShipmentChargesController::return($shipment->id);
            }else if($shipment->shipper_status_id == 14){
                $type = 0;
            }
            $shipment->refresh();
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
            if($payable) {
                AdminFinanceController::add_adjustment($shipment->id, $payable, 'Shipment Adjustment Charges', 15);
            }

        }
    }
}
