<?php

namespace App\Http\Controllers\Retail;

use App\Http\Controllers\Controller;
use App\Http\Models\City;
use App\Http\Models\InternationalDhlZone;
use App\Http\Models\InternationalEconomyStandardRetailRate;
use App\Http\Models\InternationalStandardRetailRates;
use App\Http\Models\RetailStandardRates;
use App\Http\Models\Zone;
use App\Http\Models\ZoneCitiesGst;
use App\Http\Models\ZoneClassCity;
use Illuminate\Http\Request;
use App\Models\ParentProduct;
use App\Http\Models\Product;

class RetailRatesCalculationController extends Controller
{
    static public function rates($shipping_mode_id, $business_category_id, $pickup_city_id, $destination_id, $trax_box_id, $discount, $weight, $insurance_amount, $packaging, $product_id = null, $cod = null, $flyer_count = 0, $charged_sms = 0)
    {
        if ($discount == null || $discount == '') {
            $discount = 0;
        } else {
            $discount = round($discount / 100, 2);
        }

        $charges = 0;
        $discount_amount = 0;
        $charges_with_discount = 0;
        $remaining_weight = 0;
        $multiplier = 1;
        $round_additional_weight = 0;
        $total_charges = 0;
        $gst_amount = 0;
        $packaging_and_insurance_charges = 0;
        $charges_without_gst = 0;
        $charges_with_discount_and_gst = 0;
        $wht = 0;
        $cod_sst = 0;
        $flyer_without_gst = 0;
        $sms_charges = 0;
        if ($business_category_id == 1) {
            $destination_city = City::find($destination_id);
            $pickup_city = City::find($pickup_city_id);
            if ($shipping_mode_id != 5) {
                $weight_charges = RetailStandardRates::where('shipping_mode_id', $shipping_mode_id)->where('range_up', '<=', $weight)->where('range_down', '>=', $weight);
                if ($weight_charges->exists()) {
                    $weight_charges = $weight_charges->first();
                    if ($weight_charges->weight_addition == 1) {
                        $charges = RetailStandardRates::where('shipping_mode_id', $shipping_mode_id)->where('id', '<', $weight_charges->id)->orderby('id', 'desc')->first();
                        if ($charges) {
                            $remaining_weight = $weight - $charges->range_down;
                        }
                    }
                    if ($remaining_weight > 0) {
                        $additional_weight = $remaining_weight;
                        $round_additional_weight = round($additional_weight);
                        if ($additional_weight > $round_additional_weight) {
                            $round_additional_weight = $round_additional_weight + $weight_charges->kg_range;
                        }
                    }
                    if ($shipping_mode_id == 1) {
                        $zone_class = ZoneClassCity::where('city_id', $destination_id)->where('zone_id', $destination_city->zone_id)->latest()->first();
                        if ($zone_class->class == 0) {
                            if ($remaining_weight > 0) {
                                $additional_charges = intval($round_additional_weight * $weight_charges->zone_a);
                                $charges = intval($charges->zone_a) + $additional_charges;
                            } else {
                                $charges = intval($weight_charges->zone_a);
                            }
                        } elseif ($zone_class->class == 1) {
                            if ($remaining_weight > 0) {
                                $additional_charges = intval($round_additional_weight * $weight_charges->zone_b);
                                $charges = intval($charges->zone_b) + $additional_charges;
                            } else {
                                $charges = intval($weight_charges->zone_b);
                            }
                        } elseif ($zone_class->class == 2) {
                            if ($remaining_weight > 0) {
                                $additional_charges = intval($round_additional_weight * $weight_charges->zone_c);
                                $charges = intval($charges->zone_c) + $additional_charges;
                            } else {
                                $charges = intval($weight_charges->zone_c);
                            }
                        } elseif ($zone_class->class == 3) {
                            if ($remaining_weight > 0) {
                                $additional_charges = intval($round_additional_weight * $weight_charges->zone_d);
                                $charges = intval($charges->zone_d) + $additional_charges;
                            } else {
                                $charges = intval($weight_charges->zone_d);
                            }
                        }
                    } else {
                        $consignee_city = City::find($destination_id);
                        $consignee_zone = Zone::find($consignee_city->zone_id);
                        $pickup_zone = Zone::find($pickup_city->zone_id);

                        if ($pickup_city->id == $consignee_city->id) {
                            if ($remaining_weight > 0) {
                                $additional_charges = intval($round_additional_weight * $weight_charges->within_city);
                                $charges = intval($charges->within_city) + $additional_charges;
                            } else {
                                $charges = intval($weight_charges->within_city);
                            }
                        } elseif ($pickup_zone->zone_region->region_id == $consignee_zone->zone_region->region_id) {
                            if ($remaining_weight > 0) {
                                $additional_charges = intval($round_additional_weight * $weight_charges->same_zone);
                                $charges = intval($charges->same_zone) + $additional_charges;
                            } else {
                                $charges = intval($weight_charges->same_zone);
                            }
                        } else {
                            if ($remaining_weight > 0) {
                                $additional_charges = intval($round_additional_weight * $weight_charges->different_zone);
                                $charges = intval($charges->different_zone) + $additional_charges;
                            } else {
                                $charges = intval($weight_charges->different_zone);
                            }
                        }
                    }
                }
            } else {
                $weight_charges = RetailStandardRates::where('shipping_mode_id', $shipping_mode_id)->where('trax_box_id', $trax_box_id)->where('range_up', '<=', $weight)->where('range_down', '>=', $weight);
                if ($weight_charges->exists()) {
                    $weight_charges = $weight_charges->first();
                    if ($weight_charges->weight_addition == 1) {
                        // = (intval($weight - $weight_charges->range_up) / $weight_charges->kg_range) + 1;
                        $charges = RetailStandardRates::where('shipping_mode_id', $shipping_mode_id)->where('trax_box_id', $trax_box_id)->where('id', '<', $weight_charges->id)->orderby('id', 'desc')->first();
                        if ($charges) {
                            $remaining_weight = $weight - $charges->range_down;
                        }

                    }

                    if ($remaining_weight > 0) {
                        $additional_weight = $remaining_weight;
                        $round_additional_weight = round($additional_weight);
                        if ($additional_weight > $round_additional_weight) {
                            $round_additional_weight = $round_additional_weight + $weight_charges->kg_range;
                        }
                    }
                    $consignee_city = City::find($destination_id);
                    $consignee_zone = Zone::find($consignee_city->zone_id);
                    $pickup_zone = Zone::find($pickup_city->zone_id);
                    if ($pickup_city->id == $consignee_city->id) {

                        if ($remaining_weight > 0) {
                            $additional_charges = intval($round_additional_weight * $weight_charges->within_city);
                            $charges = intval($charges->within_city) + $additional_charges;
                        } else {
                            $charges = intval($weight_charges->within_city);
                        }
                    } elseif ($pickup_zone->zone_region->region_id == $consignee_zone->zone_region->region_id) {
                        if ($remaining_weight > 0) {
                            $additional_charges = intval($round_additional_weight * $weight_charges->same_zone);
                            $charges = intval($charges->same_zone) + $additional_charges;
                        } else {
                            $charges = intval($weight_charges->same_zone);
                        }
                    } else {

                        if ($remaining_weight > 0) {
                            $additional_charges = intval($round_additional_weight * $weight_charges->different_zone);
                            $charges = intval($charges->different_zone) + $additional_charges;
                        } else {
                            $charges = intval($weight_charges->different_zone);
                        }
                    }
                }
            }

            $zone_city_gst = ZoneCitiesGst::where('zone_id',$pickup_city->zone?->id)->where('city_id',$pickup_city->id);
            if ($zone_city_gst->exists())
            {
                $zone_city_gst = $zone_city_gst->first();
                $gst = 1 + $zone_city_gst->gst;
            }
            else
            {
                $gst = 1 + $pickup_city->zone?->gst;
            }
            $parent_product_id = Product::where('id', $product_id)->value('parent_product_id');
            //dd($product_id);
            if($parent_product_id && $shipping_mode_id == 3 && $cod > 0) {
                
                $cod = str_replace(',', '', $cod);
                $cod = intval($cod);
                $tax_percentage  = ParentProduct::where('id', $parent_product_id)->value('tax_percentage');
                if($tax_percentage != 0) {
                    
                    $wht  = ($cod * floatval($tax_percentage)) / 100;
                    //return $tax_amount;
                }

                $sst_percentage  = ParentProduct::where('id', $parent_product_id)->value('sst_percentage');
                if($sst_percentage != 0) {
                    $cod_sst  = ($cod * floatval($sst_percentage)) / 100;
                    // return $cod_sst;
                }
            }

            $charges_without_gst = round($charges / $gst, 2); //
            $flyer_without_gst = $flyer_count * 35;
            $sms_charges = $charged_sms == 1 ? 25 : 0;
            $gst_base = $charges_without_gst + $flyer_without_gst + $sms_charges;
            $gst_amount = round(($gst_base * $gst) - $gst_base, 2);
            //$gst_amount = round($charges - $charges_without_gst, 2);
            $discount_amount = ($discount > 0) ? round($charges_without_gst * $discount, 2) : 0;
            $charges_with_discount = round($charges_without_gst - $discount_amount, 2);
            //$charges_with_discount_and_gst = $charges_with_discount + $gst_amount;
            $charges_with_discount_and_gst = $charges_with_discount + $flyer_without_gst + $gst_amount + $sms_charges;
            $packaging_and_insurance_charges = $insurance_amount + $packaging;
            $total_charges = round($charges_with_discount_and_gst + $packaging_and_insurance_charges, 0, PHP_ROUND_HALF_UP);
        } elseif ($business_category_id == 2) {

            $InternationalEconomyStandardRetailRateCheck = false;

            if ($shipping_mode_id == 8) {
                $shipping_mode_id = 1;
            } else if ($shipping_mode_id == 9) {
                $shipping_mode_id = 2;
            } else if ($shipping_mode_id == 11) {
                $InternationalEconomyStandardRetailRateCheck = true;
                $shipping_mode_id = 2;
            } else {
                $shipping_mode_id = 3;
            }

            if($InternationalEconomyStandardRetailRateCheck)
            {
                $weight_charge = InternationalEconomyStandardRetailRate::where('shipping_mode_id', $shipping_mode_id)->where('range_up', '<=', $weight)->where('range_down', '>=', $weight);    
            }
            else
            {
                $weight_charge = InternationalStandardRetailRates::where('shipping_mode_id', $shipping_mode_id)->where('range_up', '<=', $weight)->where('range_down', '>=', $weight);
            }
            if ($weight_charge->exists()) {
                $weight_charge = $weight_charge->first();
                $consignee_city = City::find($destination_id);
                $zone_id = $consignee_city->zone_id;
                $international_zone = InternationalDhlZone::where('zone_id', $zone_id)->first();

                if ($international_zone) {
                    $zone = $international_zone->zone_name;
                    $zone_id = 'zone_' . $zone;
                    $charges = $weight_charge[$zone_id];

                    $city = City::find($pickup_city_id);
                    $zone_city_gst = ZoneCitiesGst::where('zone_id',$city->zone?->id)->where('city_id',$city->id);
                    if ($zone_city_gst->exists())
                    {
                        $zone_city_gst = $zone_city_gst->first();
                        $gst = 1 + $zone_city_gst->gst;
                    }
                    else
                    {
                        $gst = 1 + $city->zone?->gst;
                    }
                    // $gst_charges = round($charges * $gst,2);
                    // $charges = round($charges - $gst_charges,2);

                    // $discount_amount = round($charges * $discount,2);
                    // $charges_with_discount = round($charges - $discount_amount,2);
                    // $packaging_and_insurance_charges = $insurance_amount + $packaging;
                    // $total_charges = round($charges_with_discount + $gst_charges + $packaging_and_insurance_charges,0,PHP_ROUND_HALF_UP);

                    $charges_without_gst = round($charges / $gst, 2); //
                    $gst_amount = round($charges - $charges_without_gst, 2);
                    $discount_amount = ($discount > 0) ? round($charges_without_gst * $discount, 2) : 0;
                    $charges_with_discount = round($charges_without_gst - $discount_amount, 2);
                    $charges_with_discount_and_gst = $charges_with_discount + $gst_amount;
                    $packaging_and_insurance_charges = $insurance_amount + $packaging;
                    $total_charges = round($charges_with_discount_and_gst + $packaging_and_insurance_charges, 0, PHP_ROUND_HALF_UP);

                }
            }
        }

        $rates['charges'] = $charges_without_gst;
        $rates['discount_amount'] = $discount_amount;
        $rates['charges_without_gst'] = $charges_without_gst;
        $rates['insurance_amount'] = $insurance_amount;
        $rates['packaging_charges'] = $packaging;
        $rates['charges_with_discount'] = $charges_without_gst - $discount_amount;
        $rates['gst_charges'] = $gst_amount;
        $rates['packaging_and_insurance_charges'] = $packaging_and_insurance_charges;
        $rates['total_charges'] = $total_charges;
        $rates['wht'] = $wht;
        $rates['cod_sst'] = $cod_sst;
        $rates['flyer_without_gst'] = $flyer_without_gst;
        $rates['sms_charges'] = $sms_charges;
    

        return $rates;
    }
}
