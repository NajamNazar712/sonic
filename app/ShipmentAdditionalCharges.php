<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class ShipmentAdditionalCharges extends Model
{

    protected $fillable = [
        'shipment_id',
        'faf_charges',
        'arrival_charges_applied',
        'zero_cod_discount_applied',
        'return_cod_discount_applied',
        'payment_type',
    ];
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
            // Use updateOrCreate to simplify the process
            ShipmentAdditionalCharges::updateOrCreate(
                ['shipment_id' => $shipment_id], // The condition to check if the record exists
                array_merge(
                    ['faf_charges' => DB::raw('IFNULL(faf_charges, 0)')], // Ensure faf_charges is set to 0 if null
                    $arrival ? ['arrival_charges_applied' => 1] : [],
                    $zero_cod ? ['zero_cod_discount_applied' => 1] : [],
                    $return_discount ? ['return_cod_discount_applied' => 1] : []
                )
            );
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
