<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShipmentAdditionalCharges extends Model
{
    static function fetch_faf_charges($shipment_id){
        $shipment_additional_charges = ShipmentAdditionalCharges::where('shipment_id', $shipment_id)->latest()->first();
        if(!empty($shipment_additional_charges)){
            $faf_charges = $shipment_additional_charges->faf_charges;
        }else{
            $faf_charges = 0;
        }
        return $faf_charges;
    }
    static function apply_faf_charges($shipment_id,$payment_type){
        $shipment_additional_charges = ShipmentAdditionalCharges::where('shipment_id', $shipment_id)->latest()->first();
        if(!empty($shipment_additional_charges)){
            $shipment_additional_charges->payment_type = $payment_type;
            $shipment_additional_charges->save();
            $faf_charges = $shipment_additional_charges->faf_charges;
        }else{
            $faf_charges = 0;
        }
        return $faf_charges;
    }

    static function additional_charges_apply($shipment_ids,$arrival = false ,$zero_cod = false,$return_discount =false){
        foreach ($shipment_ids as $shipment_id) {
            $shipment_Additional_charges = ShipmentAdditionalCharges::where('shipment_id', $shipment_id);
            $shipment_Additional_charges = ($shipment_Additional_charges->exists()) ? $shipment_Additional_charges->first() : new ShipmentAdditionalCharges();
            $shipment_Additional_charges->shipment_id = $shipment_id;
            if ($arrival) {
                $shipment_Additional_charges->arrival_charges_applied = 1;
            }
            if ($zero_cod) {
                $shipment_Additional_charges->zero_cod_discount_applied = 1;
            }
            if ($return_discount) {
                $shipment_Additional_charges->return_cod_discount_applied = 1;
            }
            $shipment_Additional_charges->faf_charges = isset($shipment_Additional_charges->faf_charges) ? $shipment_Additional_charges->faf_charges : 0;
            $shipment_Additional_charges->save();
        }
    }

    static function check_additional_charges($shipment_id,$arrival = false ,$zero_cod = false,$return_discount =false){
        $shipment_additional_charges = ShipmentAdditionalCharges::where('shipment_id', $shipment_id);
        if($shipment_additional_charges->exists()){
            $shipment_additional_charges = $shipment_additional_charges->first();
            if($arrival) {
                $charges = $shipment_additional_charges->arrival_charges_applied;
            }
            if($zero_cod) {
                $charges = $shipment_additional_charges->zero_cod_discount_applied;
            }
            if($return_discount) {
                $charges = $shipment_additional_charges->return_cod_discount_applied;
            }
        }else{
            $charges = 0;
        }
        return $charges;
    }
}
