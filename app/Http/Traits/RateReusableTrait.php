<?php

namespace App\Http\Traits;

use App\CorporateDefaultShipmentReturnDiscountCharges;
use App\CorporateDefaultZeroCodDiscountCharges;
use App\CorporateShipmentReturnDiscountCharges;
use App\CorporateZeroCodDiscountCharges;
use App\HistoryCorporateDefaultShipmentReturnDiscountCharges;
use App\HistoryCorporateDefaultZeroCodDiscountCharges;
use App\HistoryCorporateShipmentReturnDiscountCharges;
use App\HistoryCorporateZeroCodDiscountCharges;
use App\HistoryShipmentReturnDiscountCharges;
use App\HistoryZeroCodDiscountCharges;
use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Controllers\Admins\ShipmentChargesController;
use App\PendingCorporateDefaultShipmentReturnDiscountCharges;
use App\PendingCorporateDefaultZeroCodDiscountCharges;
use App\PendingCorporateShipmentReturnDiscountCharges;
use App\PendingCorporateZeroCodDiscountCharges;
use App\PendingShipmentReturnDiscountCharges;
use App\PendingZeroCodDiscountCharges;
use App\ShipmentReturnDiscountCharges;
use App\ZeroCodDiscountCharges;
use Carbon\Carbon;
use Illuminate\Http\Request;

trait RateReusableTrait
{
    static function discounted_cod_and_return(Request $request,$id,$type,$fetch_from,$operation = 'dump'){

        if ($fetch_from == 'corporate') {
            $zero_cod_p = PendingCorporateZeroCodDiscountCharges::class;
            $return_discount_p = PendingCorporateShipmentReturnDiscountCharges::class;

            $zero_cod = CorporateZeroCodDiscountCharges::class;
            $return_discount = CorporateShipmentReturnDiscountCharges::class;

            $zero_cod_h = HistoryCorporateZeroCodDiscountCharges::class;
            $return_discount_h = HistoryCorporateShipmentReturnDiscountCharges::class;

        } else if ($fetch_from == 'corporate_default') {
            $zero_cod_p = PendingCorporateDefaultZeroCodDiscountCharges::class;
            $return_discount_p = PendingCorporateDefaultShipmentReturnDiscountCharges::class;

            $zero_cod = CorporateDefaultZeroCodDiscountCharges::class;
            $return_discount = CorporateDefaultShipmentReturnDiscountCharges::class;

            $zero_cod_h = HistoryCorporateDefaultZeroCodDiscountCharges::class;
            $return_discount_h = HistoryCorporateDefaultShipmentReturnDiscountCharges::class;

        } else {
            $zero_cod_p = PendingZeroCodDiscountCharges::class;
            $return_discount_p = PendingShipmentReturnDiscountCharges::class;

            $zero_cod = ZeroCodDiscountCharges::class;
            $return_discount = ShipmentReturnDiscountCharges::class;

            $zero_cod_h = HistoryZeroCodDiscountCharges::class;
            $return_discount_h = HistoryShipmentReturnDiscountCharges::class;
        }

        if($type ==0) { //history
            self:: discount_cod_return_operation($request,$id,$operation,$zero_cod,$return_discount,$zero_cod_h,$return_discount_h,$type);
        }
        if ($type == 1) {
            self:: discount_cod_return_operation($request,$id,$operation,$zero_cod_p,$return_discount_p,null,null,$type);
        } elseif ($type ==2) {
            self:: discount_cod_return_operation($request,$id,$operation,$zero_cod,$return_discount,null,null,$type);
        }
        else if($type ==3) { //history
            self:: discount_cod_return_operation($request,$id,$operation,$zero_cod_p,$return_discount_p,$zero_cod,$return_discount,$type);
        }
    }

    static function discount_cod_return_operation(Request $request,$id,$operation = 'dump',$zero_cod = null,$return_discount = null,$zero_cod_action = null ,$return_discount_action= null,$type=null){
        if($operation == 'dump') {
            if ($request->has('sameday_main_switch') && $request->sameday_main_switch == 'on') {
                if ($request->has('sameday_zero_cod_switch') && $request->sameday_zero_cod_switch == 'on') {

                    $zero_cod_query = $zero_cod::where('user_id', $id)->where('shipping_mode_id',4);
                    if ($zero_cod_query->exists()) {
                        $zero_cod::where('user_id', $id)->where('shipping_mode_id',4)->update([
                            'shipping_mode_id' => 4,
                            'cod_discount_per' => $request->sameday_cod_discount_per
                        ]);
                    } else {
                        $zero_cod::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'cod_discount_per' => $request->sameday_cod_discount_per
                        ]);
                    }
                }

                if ($request->has('sameday_return_discount_switch') && $request->sameday_return_discount_switch == 'on') {
                    $return_discount_query = $return_discount::where('user_id', $id)->where('shipping_mode_id',4);

                    if ($return_discount_query->exists()) {
                        $return_discount::where('user_id', $id)->where('shipping_mode_id',4)->update([
                            'shipping_mode_id' => 4,
                            'return_discount_per' => $request->sameday_return_discount_per
                        ]);
                    } else {
                        $return_discount::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'return_discount_per' => $request->sameday_return_discount_per
                        ]);
                    }
                }
            }
            if ($request->has('detain_main_switch') && $request->detain_main_switch == 'on') {
                $zero_cod_query = $zero_cod::where('user_id', $id)->where('shipping_mode_id',3);
                if ($request->has('detain_zero_cod_switch') && $request->detain_zero_cod_switch == 'on') {
                    if ($zero_cod_query->exists()) {
                        $zero_cod::where('user_id', $id)->where('shipping_mode_id',3)->update([
                            'shipping_mode_id' => 3,
                            'cod_discount_per' => $request->detain_cod_discount_per
                        ]);
                    } else {
                        $zero_cod::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'cod_discount_per' => $request->detain_cod_discount_per
                        ]);
                    }
                }

                if ($request->has('detain_return_discount_switch') && $request->detain_return_discount_switch == 'on') {
                    $return_discount_query = $return_discount::where('user_id', $id)->where('shipping_mode_id',3);
                    if ($return_discount_query->exists()) {
                        $return_discount::where('user_id', $id)->where('shipping_mode_id',3)->update([
                            'shipping_mode_id' => 3,
                            'return_discount_per' => $request->detain_return_discount_per
                        ]);
                    } else {
                        $return_discount::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'return_discount_per' => $request->detain_return_discount_per
                        ]);
                    }
                }

            }
            if ($request->has('ol_main_switch') && $request->ol_main_switch == 'on') {
                if ($request->has('ol_zero_cod_switch') && $request->ol_zero_cod_switch == 'on') {
                    $zero_cod_query = $zero_cod::where('user_id', $id)->where('shipping_mode_id',2);
                    if ($zero_cod_query->exists()) {
                        $zero_cod::where('user_id', $id)->where('shipping_mode_id',2)->update([
                            'shipping_mode_id' => 2,
                            'cod_discount_per' => $request->ol_cod_discount_per
                        ]);
                    } else {
                        $zero_cod::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'cod_discount_per' => $request->ol_cod_discount_per
                        ]);
                    }
                }
                if ($request->has('ol_return_discount_switch') && $request->ol_return_discount_switch == 'on') {

                    $return_discount_query = $return_discount::where('user_id', $id)->where('shipping_mode_id',2);

                    if ($return_discount_query->exists()) {
                        $return_discount::where('user_id', $id)->where('shipping_mode_id',2)->update([
                            'shipping_mode_id' => 2,
                            'return_discount_per' => $request->ol_return_discount_per
                        ]);
                    } else {
                        $return_discount::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'return_discount_per' => $request->ol_return_discount_per
                        ]);
                    }
                }
            }
            if ($request->has('on_main_switch') && $request->on_main_switch == 'on') {
                if ($request->has('on_zero_cod_switch') && $request->on_zero_cod_switch == 'on') {
                    $zero_cod_query = $zero_cod::where('user_id', $id)->where('shipping_mode_id',1);
                    if ($zero_cod_query->exists()) {
                        $zero_cod::where('user_id', $id)->where('shipping_mode_id',1)->update([
                            'shipping_mode_id' => 1,
                            'cod_discount_per' => $request->on_cod_discount_per
                        ]);
                    } else {
                        $zero_cod::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'cod_discount_per' => $request->on_cod_discount_per
                        ]);
                    }
                }
                if ($request->has('on_return_discount_switch') && $request->on_return_discount_switch == 'on') {
                    $return_discount_query = $return_discount::where('user_id', $id)->where('shipping_mode_id',1);
                    if ($return_discount_query->exists()) {
                        $return_discount::where('user_id', $id)->where('shipping_mode_id',1)->update([
                            'shipping_mode_id' => 1,
                            'return_discount_per' => $request->on_return_discount_per
                        ]);
                    } else {
                        $return_discount::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'return_discount_per' => $request->on_return_discount_per
                        ]);
                    }
                }
            }
        }elseif($operation == 'delete') {
            $zero_cod::where('user_id' ,$id)->delete();
            $return_discount::where('user_id' ,$id)->delete();
        }
        else if($operation == 'fetch_and_dump') {
            $zero_cod_query = $zero_cod::where('user_id', $id)->get();
            $return_discount_query = $return_discount::where('user_id', $id)->get();

            if(count($zero_cod_query) > 0) {
                foreach ($zero_cod_query as $value) {
                    $zero_cod_action::create([
                        'user_id' => $id,
                        'shipping_mode_id' => $value->shipping_mode_id,
                        'cod_discount_per' => $value->cod_discount_per
                    ]);

                }
            }
            if(count($return_discount_query) > 0) {
                foreach ($return_discount_query as $value) {
                    $return_discount_action::create([
                        'user_id' => $id,
                        'shipping_mode_id' =>  $value->shipping_mode_id,
                        'return_discount_per' => $value->return_discount_per
                    ]);
                }
            }
        }
    }

    static function arrival_chagres (Request $request,$parcel){
        if ($parcel) {
            $shipment = $parcel->id;
            if ($parcel->booking_type_id == 2) {
                ShipmentChargesController::replacement($shipment);
            } else if ($parcel->booking_type_id == 3) {
                ShipmentChargesController::try_and_buy($shipment);
            }

            if ($parcel->booking_type_id != 4) {
                if (($parcel->packaging_material_request == 1 && $parcel->packaging_material_charges != '') || $parcel->packaging_material_request == 0) {
                    if($parcel->shipment_type == 1) {
                        AdminFinanceController::add_payment($shipment, 3, $parcel);
                    }
                }
            }

        }
    }
}
