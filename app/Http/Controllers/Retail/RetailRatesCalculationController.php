<?php

namespace App\Http\Controllers\Retail;

use App\Http\Controllers\Controller;
use App\Http\Models\City;
use App\Http\Models\InternationalDhlZone;
use App\Http\Models\InternationalStandardRetailRates;
use App\Http\Models\RetailStandardRates;
use App\Http\Models\ZoneClassCity;
use Illuminate\Http\Request;

class RetailRatesCalculationController extends Controller
{
    static public function rates($shipping_mode_id, $business_category_id, $pickup_city_id, $destination_id, $trax_box_id, $discount, $weight){
        if($discount == null || $discount == ''){
            $discount = 0;
        }
        else{
            $discount = $discount / 100;
        }
        $charges = 0;
        $discount_amount = 0;
        $charges_with_discount = 0;
        if($business_category_id == 1){
            $pickup_city = City::find($pickup_city_id);
            if($shipping_mode_id != 5){
                $weight_charges = RetailStandardRates::where('shipping_mode_id', $shipping_mode_id)->where('range_up', '<=', $weight)->where('range_down', '>=', $weight);
                if($weight_charges->exists()){
                    $weight_charges = $weight_charges->first();
                    if($weight_charges->weight_addition == 1){
                        $multiplier = (intval($weight - $weight_charges->range_up) / $weight_charges->kg_range) + 1;
                    }
                    else{
                        $multiplier = 1;
                    }
                    if($shipping_mode_id == 1){
                        $zone_class = ZoneClassCity::where('city_id', $pickup_city_id)->where('zone_id', $pickup_city->zone_id)->first();
                        if($zone_class->class == 0){
                            $charges =  intval($weight_charges->zone_a) * $multiplier;
                        }
                        elseif ($zone_class->class == 1){
                            $charges = intval($weight_charges->zone_b) * $multiplier;
                        }
                        elseif ($zone_class->class == 2){
                            $charges = intval($weight_charges->zone_c) * $multiplier;
                        }
                        elseif ($zone_class->class == 3){
                            $charges = intval($weight_charges->zone_d) * $multiplier;
                        }
                    }
                    else{
                        $consignee_city = City::find($destination_id);
                        if($pickup_city->id == $consignee_city->id){
                            $charges = intval($weight_charges->within_city) * $multiplier;
                        }
                        elseif ($pickup_city->zone_id == $consignee_city->zone_id){
                            $charges = intval($weight_charges->same_zone) * $multiplier;
                        }
                        else{
                            $charges = intval($weight_charges->different_zone) * $multiplier;
                        }
                    }
                }
            }
            else{
                $weight_charges = RetailStandardRates::where('shipping_mode_id', $shipping_mode_id)->where('trax_box_id', $trax_box_id)->where('range_up', '<=', $weight)->where('range_down', '>=', $weight);
                if($weight_charges->exists()){
                    $weight_charges = $weight_charges->first();

                    if($weight_charges->weight_addition == 1){
                        $multiplier = (intval($weight - $weight_charges->range_up) / $weight_charges->kg_range) + 1;
                    }
                    else{
                        $multiplier = 1;
                    }
                    $consignee_city = City::find($destination_id);
                    if($pickup_city->id == $consignee_city->id){
                        $charges = intval($weight_charges->within_city) * $multiplier;
                    }
                    elseif ($pickup_city->zone_id == $consignee_city->zone_id){
                        $charges = intval($weight_charges->same_zone) * $multiplier;
                    }
                    else{
                        $charges = intval($weight_charges->different_zone) * $multiplier;
                    }
                }
            }
            $discount_amount = $charges * $discount;
            $charges_with_discount = $charges - $discount_amount;
        }
        elseif ($business_category_id == 2){
            $weight_charge = InternationalStandardRetailRates::where('shipping_mode_id', $shipping_mode_id)->where('range_up', '<=', $weight)->where('range_down', '>=', $weight);
            if($weight_charge->exists()) {
                $weight_charge = $weight_charge->first();
                $consignee_city = City::find($destination_id);
                $zone_id = $consignee_city->zone_id;
                $international_zone = InternationalDhlZone::where('zone_id', $zone_id)->first();
                $zone = $international_zone->zone_name;
                $zone_id = 'zone_'.$zone;
                $charges = $weight_charge[$zone_id];
                $discount_amount = $charges * $discount;
                $charges_with_discount = $charges - $discount_amount;
            }
        }
        $rates['charges'] = $charges;
        $rates['discount_amount'] = $discount_amount;
        $rates['charges_with_discount'] = $charges_with_discount;

        return $rates;
    }

}
