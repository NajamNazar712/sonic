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
    static public function rates($shipping_mode_id, $business_category_id, $pickup_city_id, $destination_id, $trax_box_id, $discount, $weight,$cod = NULL){
        if($discount == null || $discount == ''){
            $discount = 0;
        }
        else{
            $discount = round($discount / 100,2);
        }
        
        $charges = 0;
        $discount_amount = 0;
        $charges_with_discount = 0;
        $remaining_weight = 0;
        $multiplier = 1;
        $round_additional_weight = 0;
        $total_charges = 0;
        $gst_charges = 0;

       
        if($business_category_id == 1){
            $destination_city = City::find($destination_id);
            $pickup_city = City::find($pickup_city_id);
            if($shipping_mode_id != 5){
                $weight_charges = RetailStandardRates::where('shipping_mode_id', $shipping_mode_id)->where('range_up', '<=', $weight)->where('range_down', '>=', $weight);
                if($weight_charges->exists()){
                    $weight_charges = $weight_charges->first();
                    if($weight_charges->weight_addition == 1){
                        $charges = RetailStandardRates::where('shipping_mode_id',$shipping_mode_id)->where('id', '<', $weight_charges->id)->orderby('id','desc')->first();
                        if($charges){
                         $remaining_weight = $weight - $charges->range_down;
                        }
                    }
                    if($remaining_weight > 0){
                        $additional_weight = $remaining_weight;
                        $round_additional_weight = round($additional_weight);
                        if($additional_weight > $round_additional_weight){
                            $round_additional_weight = $round_additional_weight + $weight_charges->kg_range;
                        }
                    }
                    if($shipping_mode_id == 1){
                        $zone_class = ZoneClassCity::where('city_id', $destination_id)->where('zone_id', $destination_city->zone_id)->first();
                        if($zone_class->class == 0){
                            if($remaining_weight > 0){
                                $additional_charges = intval($round_additional_weight * $weight_charges->zone_a);
                                $charges = intval($charges->zone_a) + $additional_charges;
                            }
                            else{
                                $charges = intval($weight_charges->zone_a);
                            }
                        }
                        elseif ($zone_class->class == 1){
                            if($remaining_weight > 0){
                                $additional_charges = intval($round_additional_weight * $weight_charges->zone_b);
                                $charges = intval($charges->zone_b) + $additional_charges;
                            }
                            else{
                                $charges = intval($weight_charges->zone_b);
                            }
                        }
                        elseif ($zone_class->class == 2){
                            if($remaining_weight > 0){
                                $additional_charges = intval($round_additional_weight * $weight_charges->zone_c);
                                $charges = intval($charges->zone_c) + $additional_charges;
                            }
                            else{
                                $charges = intval($weight_charges->zone_c);
                            }
                        }
                        elseif ($zone_class->class == 3){
                            if($remaining_weight > 0){
                                $additional_charges = intval($round_additional_weight * $weight_charges->zone_d);
                                $charges = intval($charges->zone_d) + $additional_charges;
                            }
                            else{
                                $charges = intval($weight_charges->zone_d);
                            }
                        }
                    }
                    else{
                        $consignee_city = City::find($destination_id);
                        if($pickup_city->id == $consignee_city->id){
                            if($remaining_weight > 0){
                                $additional_charges = intval($round_additional_weight * $weight_charges->within_city);
                                $charges = intval($charges->within_city) + $additional_charges;
                            }
                            else{
                                $charges = intval($weight_charges->within_city);
                            }
                        }
                        elseif ($pickup_city->zone_id == $consignee_city->zone_id){
                            if($remaining_weight > 0){
                                $additional_charges = intval($round_additional_weight * $weight_charges->same_zone);
                                $charges = intval($charges->same_zone) + $additional_charges;
                            }
                            else{
                                $charges = intval($weight_charges->same_zone);
                            }
                        }
                        else{
                            if($remaining_weight > 0){
                                $additional_charges = intval($round_additional_weight * $weight_charges->different_zone);
                                $charges = intval($charges->different_zone) + $additional_charges;
                            }
                            else{
                                $charges = intval($weight_charges->different_zone);
                            }
                        }
                    }
                }
            }
            else{
                $weight_charges = RetailStandardRates::where('shipping_mode_id', $shipping_mode_id)->where('trax_box_id', $trax_box_id)->where('range_up', '<=', $weight)->where('range_down', '>=', $weight);
                if($weight_charges->exists()){
                    $weight_charges = $weight_charges->first();
                    if($weight_charges->weight_addition == 1){
                        // = (intval($weight - $weight_charges->range_up) / $weight_charges->kg_range) + 1;
                        $charges = RetailStandardRates::where('shipping_mode_id',$shipping_mode_id)->where('trax_box_id', $trax_box_id)->where('id', '<', $weight_charges->id)->orderby('id','desc')->first();
                        if($charges){
                            $remaining_weight = $weight - $charges->range_down;
                        }

                    }

                    if($remaining_weight > 0){
                        $additional_weight = $remaining_weight;
                        $round_additional_weight = round($additional_weight);
                        if($additional_weight > $round_additional_weight){
                            $round_additional_weight = $round_additional_weight + $weight_charges->kg_range;
                        }
                    }
                    $consignee_city = City::find($destination_id);
                    if($pickup_city->id == $consignee_city->id){

                        if($remaining_weight > 0){
                            $additional_charges = intval($round_additional_weight * $weight_charges->within_city);
                            $charges = intval($charges->within_city) + $additional_charges;
                        }
                        else{
                            $charges = intval($weight_charges->within_city);
                        }
                    }
                    elseif ($pickup_city->zone_id == $consignee_city->zone_id){
                        if($remaining_weight > 0){
                            $additional_charges = intval($round_additional_weight * $weight_charges->same_zone);
                            $charges = intval($charges->same_zone) + $additional_charges;
                        }
                        else{
                            $charges = intval($weight_charges->same_zone);
                        }
                    }
                    else{
                       
                        if($remaining_weight > 0){
                            $additional_charges = intval($round_additional_weight * $weight_charges->different_zone);
                            $charges = intval($charges->different_zone) + $additional_charges;
                        }
                        else{
                            $charges = intval($weight_charges->different_zone);
                        }
                    }
                }
            }

            $gst = $pickup_city->zone->gst;
            $gst_charges = round($charges * $gst,2);
            $charges = round($charges - $gst_charges,2);

            $discount_amount = round($charges * $discount,2);
            $charges_with_discount = round($charges - $discount_amount,2);

            $total_charges = round($charges_with_discount + $gst_charges,0,PHP_ROUND_HALF_UP);
           /* if($cod != null){
                $total_charges = $total_charges + intval($cod);
            }*/
        }
        elseif ($business_category_id == 2){

            if($shipping_mode_id == 8){
                $shipping_mode_id = 1;
            }
            else if($shipping_mode_id == 9){
                $shipping_mode_id = 2;
            }
            else{
                $shipping_mode_id = 3;
            }

            $weight_charge = InternationalStandardRetailRates::where('shipping_mode_id', $shipping_mode_id)->where('range_up', '<=', $weight)->where('range_down', '>=', $weight);
            if($weight_charge->exists()) {
                $weight_charge = $weight_charge->first();
                $consignee_city = City::find($destination_id);
                $zone_id = $consignee_city->zone_id;
                $international_zone = InternationalDhlZone::where('zone_id', $zone_id)->first();

                if($international_zone){
                    $zone = $international_zone->zone_name;
                    $zone_id = 'zone_'.$zone;
                    $charges = $weight_charge[$zone_id];

                    $city = City::find($pickup_city_id);
                    $gst = $city->zone->gst;
                    $gst_charges = round($charges * $gst,2);
                    $charges = round($charges - $gst_charges,2);

                    $discount_amount = round($charges * $discount,2);
                    $charges_with_discount = round($charges - $discount_amount,2);

                    $total_charges = round($charges_with_discount + $gst_charges,0,PHP_ROUND_HALF_UP);
                   /* if($cod != null){
                        $total_charges = $total_charges + intval($cod);
                    }*/
                    
                }
            }
        }
        $rates['charges'] = $charges;
        $rates['discount_amount'] = $discount_amount;
        $rates['charges_with_discount'] = $charges_with_discount;
        $rates['gst_charges'] = $gst_charges;
        $rates['total_charges'] = $total_charges;

        return $rates;
    }

}
