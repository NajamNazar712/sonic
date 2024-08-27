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
}
