<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShipmentAdditionalCharges extends Model
{
    static function fetch_faf_charges($shipment_id){
        $shipment_additional_charges = ShipmentAdditionalCharges::where('shipment_id', $shipment_id);
        if($shipment_additional_charges->exists()){
            $shipment_additional_charges = $shipment_additional_charges->first();
            $faf_charges = $shipment_additional_charges->faf_charges;
        }else{
            $faf_charges = 0;
        }
        return $faf_charges;
    }
}
