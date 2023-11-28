<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Http\Models\Admin\CorporateDefaultDiscountWeightCharge;
use App\Http\Models\Admin\FtlRequestAdditionalCost;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\StandardFuelSurcharge;
use App\Http\Models\Admin\WalkInInternationalStandardWeightCharge;
use App\Http\Models\Admin\WalkInInternationalStandardWeightChargeHub;
use App\Http\Models\Admin\WalkinShipmentWeightCharges;
use App\Http\Models\CorporateDefaultBookingTypeCharge;
use App\Http\Models\CorporateDefaultCashHandlingCharge;
use App\Http\Models\CorporateDefaultDiscountCharge;
use App\Http\Models\CorporateDefaultFuelSurcharge;
use App\Http\Models\CorporateDefaultInsuranceCharge;
use App\Http\Models\CorporateDefaultRateStatus;
use App\Http\Models\CorporateDefaultReturnCharge;
use App\Http\Models\CorporateDefaultWeightCharge;
use App\Http\Models\CorporateReturnChargeZoneWise;
use App\Http\Models\CorporateWeightChargeZoneWise;
use App\Http\Models\DiscountWeightCharge;
use App\Http\Models\InternationalDhlZone;
use App\Http\Models\InternationalRatesCashHandlingCharges;
use App\Http\Models\InternationalRatesDiscountCharges;
use App\Http\Models\InternationalRatesHub;
use App\Http\Models\InternationalRatesInsuranceCharges;
use App\Http\Models\InternationalRatesReturnCharges;
use App\Http\Models\InternationalRatesStatus;
use App\Http\Models\InternationalRatesWeightCharges;
use App\Http\Models\InternationalStandardDhlRate;
use App\Http\Models\InternationalUserRate;
use App\Http\Models\InternationalUsersCreditLimit;
use App\Http\Models\Rates\InternationalEconomyRate;
use App\Http\Models\Rates\InternationalEconomyRateStatus;
use App\Http\Models\Shipment;

use App\Http\Models\RateStatus;
use App\Http\Models\Shipper\User;
use App\Http\Models\WeightCharge;
use App\Http\Models\CashHandlingCharge;
use App\Http\Models\InsuranceCharge;
use App\Http\Models\ReturnCharge;
use App\Http\Models\FuelSurcharge;
use App\Http\Models\BookingTypeCharges;
use App\Http\Models\DiscountCharge;

use App\Http\Models\CorporateRateStatus;
use App\Http\Models\CorporateMinChargeableWeight;
use App\Http\Models\CorporateWeightCharge;
use App\Http\Models\CorporateCashHandlingCharge;
use App\Http\Models\CorporateInsuranceCharge;
use App\Http\Models\CorporateReturnCharge;
use App\Http\Models\CorporateFuelSurcharge;
use App\Http\Models\CorporateBookingTypeCharge;
use App\Http\Models\CorporateDiscountCharge;

use App\Http\Models\Admin\WalkInStandardWeightCharge;

use App\Http\Models\City;
use App\Http\Models\Zone;
use App\Http\Models\ZoneClassCity;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ShipmentChargesController extends Controller
{
    static public function calculate_weight($account_type_id, $user_id, $shipping_mode_id, $same_day_timing_id, $walk_in_delivery_type_id, $weight, $origin_city_id, $origin_city_zone_id, $destination_city_id, $booking_type_id, $amount) {
        if ($user_id == 7762 && $shipping_mode_id == 1) {
            if ($origin_city_id == $destination_city_id) {
                $type_of_charges = 0;
            }
            else {
                $type_of_charges = 1;
            }

            if ($booking_type_id == 5 || $amount == 0) {
                if ($type_of_charges == 0) {
                    $charges = 37.5;
                }
                else {
                    $charges = 50;
                }
            }
            else {
                if ($type_of_charges == 0) {
                    $charges = 75;
                }
                else {
                    $charges = 100;
                }
            }

            $chargeable_weight = 1;

            if ($weight > 1) {
                $chargeable_weight = ceil($weight / 0.5) * 0.5;

                $multiplier = ceil(($weight - 1) / 0.5);

                if ($booking_type_id == 5 || $amount == 0) {
                    if ($type_of_charges == 0) {
                        $charges += (17.5 * $multiplier);
                    }
                    else {
                        $charges += (22.5 * $multiplier);
                    }
                }
                else {
                    if ($type_of_charges == 0) {
                        $charges += (35 * $multiplier);
                    }
                    else {
                        $charges += (45 * $multiplier);
                    }
                }
            }

            $result = array();

            $result['weight_charges'] = $charges;
            $result['chargeable_weight'] = $chargeable_weight;

            return $result;
        }

        $rate_type_id = User::find($user_id)->corporate_rate_type_id;

        $discount_weight_charge = false;
        if ($account_type_id == 1) {
            $rate_status = RateStatus::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id)->where('status', 1);
        }
        else {
            if($rate_type_id == 1 || $rate_type_id == 2 ){
                $rate_status = CorporateRateStatus::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id)->where('status', 1);
            }
            else{
                $rate_status = CorporateDefaultRateStatus::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id)->where('status', 1);
            }
        }

        if ($rate_status->exists()) {
            if ($account_type_id == 1) {
                $weight_charge = DiscountWeightCharge::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id)->where('range_up', '<=', $weight)->where('range_down', '>=', $weight)->where('destination_id',$destination_city_id);
                if($weight_charge->doesntExist()) {
                    $weight_charge = WeightCharge::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id)->where('range_up', '<=', $weight)->where('range_down', '>=', $weight);
                }
                else{
                    $discount_weight_charge = true;
                }
            }
            else {
                if( $rate_type_id != 3 ){
                    $min_chargeable_weight = CorporateMinChargeableWeight::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id)->where('delivery_type_id', $walk_in_delivery_type_id);

                    if ($min_chargeable_weight->exists()) {
                        $min_chargeable_weight = $min_chargeable_weight->first();

                        $min_chargeable_weight = $min_chargeable_weight->min_chargeable_weight;

                        if ($weight < $min_chargeable_weight) {
                            $weight = $min_chargeable_weight;
                        }
                    }
                }


                if($rate_type_id == 1){
                    $weight_charge = CorporateWeightCharge::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id)->where('delivery_type_id', $walk_in_delivery_type_id)->where('range_up', '<=', $weight)->where('range_down', '>=', $weight);
                }
                else if($rate_type_id == 2){
                    $weight_charge = CorporateWeightChargeZoneWise::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id)->where('delivery_type_id', $walk_in_delivery_type_id)->where('range_up', '<=', $weight)->where('range_down', '>=', $weight);
                }
                else{
                    $weight_charge = CorporateDefaultDiscountWeightCharge::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id)->where('range_up', '<=', $weight)->where('range_down', '>=', $weight)->where('destination_id',$destination_city_id);
                    if($weight_charge->doesntExist()) {
                        $weight_charge = CorporateDefaultWeightCharge::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id)->where('range_up', '<=', $weight)->where('range_down', '>=', $weight);
                    }
                    else{
                        $discount_weight_charge = true;
                    }

                }
            }

            if ($weight_charge->exists()) {
                $weight_charge = $weight_charge->first();

                $base = FALSE;
                if ($account_type_id == 2 && $rate_type_id != 3) {
                    $base_weight_charge = CorporateWeightCharge::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id)->where('delivery_type_id', $walk_in_delivery_type_id)->where('id', '<', $weight_charge->id)->where('base', 1)->orderBy('id', 'DESC');

                    if ($base_weight_charge->exists()) {
                        $base_weight_charge = $base_weight_charge->first();

                        $base = TRUE;
                    }
                }

                $today = Carbon::today();

                if(!$discount_weight_charge) {
                    if ($account_type_id == 1) {
                        $discount_charge = DiscountCharge::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
                    } else {
                        if ($rate_type_id == 3) {
                            $discount_charge = CorporateDefaultDiscountCharge::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
                        } else {
                            $discount_charge = CorporateDiscountCharge::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
                        }
                    }

                    if ($discount_charge->exists()) {
                        $discount_charge = $discount_charge->first();

                        $discount = $discount_charge->weight;
                    } else {
                        $discount = 0;
                    }
                }
                else{
                    $discount = 0;
                }
                $class = 0;
                $zone_wise = 1; // same zone
                if ($shipping_mode_id == 4) {
                    if ($same_day_timing_id == 1) {
                        $type_of_charges = 0;
                    }
                    else {
                        $type_of_charges = 1;
                    }
                }
                else {
                    if ($origin_city_id == $destination_city_id) {
                        $type_of_charges = 0;
                    }
                    else {
                        $type_of_charges = 1;
                        if($rate_type_id == null || $rate_type_id == 1 || $rate_type_id == 3){
                            $zone_class_city = ZoneClassCity::where('zone_id', $origin_city_zone_id)->where('city_id', $destination_city_id);

                            if ($shipping_mode_id == 2 || $shipping_mode_id == 3) {
                                $zone_class_city = $zone_class_city->where('zone_classification_id', 2);
                            }
                            else {
                                $zone_class_city = $zone_class_city->where('zone_classification_id', 1);
                            }

                            if ($zone_class_city->exists()) {
                                $zone_class_city = $zone_class_city->first();

                                $class = $zone_class_city->class;
                            }
                        }
                        else{
                            $destination_zone_id = City::find($destination_city_id)->zone_id;
                            if($destination_zone_id == $origin_city_zone_id){
                                $zone_wise = 1;
                            }else{
                                $zone_wise = 2;
                            }
                        }
                    }
                }

                if ($weight_charge->weight_addition == 0 || ($account_type_id == 2 && $rate_type_id != 3)) {
                    if ($type_of_charges == 0) {
                        if($rate_type_id == null || $rate_type_id == 1 || $rate_type_id == 3){
                            $charges = $weight_charge->local_or_6hr;
                        }
                        else{
                            $charges = $weight_charge->local;
                        }
                    } else {
                        if(!$discount_weight_charge) {
                            if ($rate_type_id == null || $rate_type_id == 1 || $rate_type_id == 3) {
                                if ($class == 1) {
                                    if (strpos($weight_charge->national_charges_class_1, '%') !== FALSE) {
                                        $charges = ((floatval(str_replace('%', '', $weight_charge->national_charges_class_1)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0;
                                    } else {
                                        $charges = intval($weight_charge->national_charges_class_1);
                                    }
                                } else if ($class == 2) {
                                    if (strpos($weight_charge->national_charges_class_2, '%') !== FALSE) {
                                        $charges = ((floatval(str_replace('%', '', $weight_charge->national_charges_class_2)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0;
                                    } else {
                                        $charges = intval($weight_charge->national_charges_class_2);
                                    }
                                } else if ($class == 3) {
                                    if (strpos($weight_charge->national_charges_class_3, '%') !== FALSE) {
                                        $charges = ((floatval(str_replace('%', '', $weight_charge->national_charges_class_3)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0;
                                    } else {
                                        $charges = intval($weight_charge->national_charges_class_3);
                                    }
                                } else {
                                    $charges = $weight_charge->national_charges_class_0;
                                }
                            } else {
                                if ($zone_wise == 1) {
                                    if (strpos($weight_charge->same_zone, '%') !== FALSE) {
                                        $charges = ((floatval(str_replace('%', '', $weight_charge->same_zone)) / 100) * $weight_charge->local) + $weight_charge->local;
                                    } else {
                                        $charges = intval($weight_charge->same_zone);
                                    }
                                } else {
                                    if (strpos($weight_charge->different_zone, '%') !== FALSE) {
                                        $charges = ((floatval(str_replace('%', '', $weight_charge->different_zone)) / 100) * $weight_charge->local) + $weight_charge->local;
                                    } else {
                                        $charges = intval($weight_charge->different_zone);
                                    }
                                }
                            }
                        }
                        else{
                            $charges = $weight_charge->local_or_6hr;
                        }
                    }

                    if ($account_type_id == 2 && $rate_type_id != 3) {
                        if ($base) {
                            $weight_difference = $weight - $base_weight_charge->range_down;

                            if ($weight_difference > 0) {
                                $charges = $charges * (ROUND($weight_difference, 0));
                            }
                            else {
                                $charges = 0;
                            }

                            if ($type_of_charges == 0) {
                                if($rate_type_id == null || $rate_type_id == 1){
                                    $charges += $base_weight_charge->local_or_6hr;
                                }
                                else{
                                    $charges += $weight_charge->local;
                                }
                            } else {
                                if($rate_type_id == null || $rate_type_id == 1 || $rate_type_id == 3){
                                    if ($class == 1) {
                                        if (strpos($base_weight_charge->national_charges_class_1, '%') !== FALSE) {
                                            $charges += ((floatval(str_replace('%', '', $base_weight_charge->national_charges_class_1)) / 100) * $base_weight_charge->national_charges_class_0) + $base_weight_charge->national_charges_class_0;
                                        } else {
                                            $charges += intval($base_weight_charge->national_charges_class_1);
                                        }
                                    } else if ($class == 2) {
                                        if (strpos($base_weight_charge->national_charges_class_2, '%') !== FALSE) {
                                            $charges += ((floatval(str_replace('%', '', $base_weight_charge->national_charges_class_2)) / 100) * $base_weight_charge->national_charges_class_0) + $base_weight_charge->national_charges_class_0;
                                        } else {
                                            $charges += intval($base_weight_charge->national_charges_class_2);
                                        }
                                    } else if ($class == 3) {
                                        if (strpos($base_weight_charge->national_charges_class_3, '%') !== FALSE) {
                                            $charges += ((floatval(str_replace('%', '', $base_weight_charge->national_charges_class_3)) / 100) * $base_weight_charge->national_charges_class_0) + $base_weight_charge->national_charges_class_0;
                                        } else {
                                            $charges += intval($base_weight_charge->national_charges_class_3);
                                        }
                                    } else {
                                        $charges += $base_weight_charge->national_charges_class_0;
                                    }
                                }
                                else{
                                    if($zone_wise == 1){
                                        if (strpos($base_weight_charge->same_zone, '%') !== FALSE) {
                                            $charges += ((floatval(str_replace('%', '', $base_weight_charge->same_zone)) / 100) * $base_weight_charge->local) + $base_weight_charge->local;
                                        } else {
                                            $charges += intval($base_weight_charge->same_zone);
                                        }
                                    }
                                    else{
                                        if (strpos($base_weight_charge->different_zone, '%') !== FALSE) {
                                            $charges += ((floatval(str_replace('%', '', $base_weight_charge->different_zone)) / 100) * $base_weight_charge->local) + $base_weight_charge->local;
                                        } else {
                                            $charges += intval($base_weight_charge->different_zone);
                                        }
                                    }
                                }

                            }
                        }
                        else {
                            $charges = $charges * ROUND($weight, 0);
                        }
                    }

                    if (strpos($discount, '%') !== FALSE) {
                        $discount = (floatval(str_replace('%', '', $discount)) / 100) * $charges;
                    }
                    else {
                        $discount = floatval($discount);
                    }

                    $result = array();

                    if ($charges < $discount) {
                        $result['weight_charges'] = ROUND($charges, 2, PHP_ROUND_HALF_DOWN);
                    }
                    else {
                        $result['weight_charges'] = ROUND(($charges - $discount), 2, PHP_ROUND_HALF_DOWN);
                    }

                    if ($weight > 1) {
                        $result['chargeable_weight'] = (CEIL($weight * 2) / 2);
                    }
                    else {
                        $result['chargeable_weight'] = $weight;
                    }

                    return $result;
                }
                else {
                    $multiplier = (intval($weight - $weight_charge->range_up) / $weight_charge->spkg) + 1;

                    if ($type_of_charges == 0) {
                        $charges = ($weight_charge->local_or_6hr * $multiplier);
                    }
                    else {
                        if(!$discount_weight_charge) {
                            if ($class == 1) {
                                if (strpos($weight_charge->national_charges_class_1, '%') !== FALSE) {
                                    $charges = (((floatval(str_replace('%', '', $weight_charge->national_charges_class_1)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0) * $multiplier;
                                } else {
                                    $charges = intval($weight_charge->national_charges_class_1) * $multiplier;
                                }
                            } else if ($class == 2) {
                                if (strpos($weight_charge->national_charges_class_2, '%') !== FALSE) {
                                    $charges = (((floatval(str_replace('%', '', $weight_charge->national_charges_class_2)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0) * $multiplier;
                                } else {
                                    $charges = intval($weight_charge->national_charges_class_2) * $multiplier;
                                }
                            } else if ($class == 3) {
                                if (strpos($weight_charge->national_charges_class_3, '%') !== FALSE) {
                                    $charges = (((floatval(str_replace('%', '', $weight_charge->national_charges_class_3)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0) * $multiplier;
                                } else {
                                    $charges = intval($weight_charge->national_charges_class_3) * $multiplier;
                                }
                            } else {
                                $charges = ($weight_charge->national_charges_class_0 * $multiplier);
                            }
                        }
                        else{
                            $charges = ($weight_charge->local_or_6hr * $multiplier);
                        }

                    }

                    $result = array();

                    $result['chargeable_weight'] = (CEIL(($weight_charge->spkg * (intval($weight / $weight_charge->spkg) + 1)) * 2) / 2);

                    $previous = TRUE;


                    while ($previous) {
                        if($rate_type_id == 3){
                            if(!$discount_weight_charge) {
                                $weight_charge = CorporateDefaultWeightCharge::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id)->where('id', '<', $weight_charge->id)->orderBy('id', 'desc');
                            }else{
                                $weight_charge = CorporateDefaultDiscountWeightCharge::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id)->where('destination_id',$destination_city_id)->where('id', '<', $weight_charge->id)->orderBy('id', 'desc');
                            }
                        }
                        else{
                            if(!$discount_weight_charge) {
                                $weight_charge = WeightCharge::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id)->where('id', '<', $weight_charge->id)->orderBy('id', 'desc');
                            }
                            else{
                                $weight_charge = DiscountWeightCharge::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id)->where('destination_id',$destination_city_id)->where('id', '<', $weight_charge->id)->orderBy('id', 'desc');
                            }
                        }

                        if ($weight_charge->exists()) {
                            $weight_charge = $weight_charge->first();

                            if ($weight_charge->weight_addition == 0) {
                                if ($type_of_charges == 0) {
                                    $charges += $weight_charge->local_or_6hr;
                                }
                                else {
                                    if(!$discount_weight_charge) {
                                        if ($class == 1) {
                                            if (strpos($weight_charge->national_charges_class_1, '%') !== FALSE) {
                                                $charges += ((floatval(str_replace('%', '', $weight_charge->national_charges_class_1)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0;
                                            } else {
                                                $charges += intval($weight_charge->national_charges_class_1);
                                            }
                                        } else if ($class == 2) {
                                            if (strpos($weight_charge->national_charges_class_2, '%') !== FALSE) {
                                                $charges += ((floatval(str_replace('%', '', $weight_charge->national_charges_class_2)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0;
                                            } else {
                                                $charges += intval($weight_charge->national_charges_class_2);
                                            }
                                        } else if ($class == 3) {
                                            if (strpos($weight_charge->national_charges_class_3, '%') !== FALSE) {
                                                $charges += ((floatval(str_replace('%', '', $weight_charge->national_charges_class_3)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0;
                                            } else {
                                                $charges += intval($weight_charge->national_charges_class_3);
                                            }
                                        } else {
                                            $charges += $weight_charge->national_charges_class_0;
                                        }
                                    }
                                    else{
                                        $charges += $weight_charge->local_or_6hr;
                                    }
                                }

                                $previous = FALSE;
                            }
                            else {
                                $multiplier = (intval($weight_charge->range_down - $weight_charge->range_up) / $weight_charge->spkg) + 1;

                                if ($type_of_charges == 0) {
                                    $charges += ($weight_charge->local_or_6hr * $multiplier);
                                }
                                else {
                                    if(!$discount_weight_charge) {
                                        if ($class == 1) {
                                            if (strpos($weight_charge->national_charges_class_1, '%') !== FALSE) {
                                                $charges += (((floatval(str_replace('%', '', $weight_charge->national_charges_class_1)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0) * $multiplier;
                                            } else {
                                                $charges += intval($weight_charge->national_charges_class_1) * $multiplier;
                                            }
                                        } else if ($class == 2) {
                                            if (strpos($weight_charge->national_charges_class_2, '%') !== FALSE) {
                                                $charges += (((floatval(str_replace('%', '', $weight_charge->national_charges_class_2)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0) * $multiplier;
                                            } else {
                                                $charges += intval($weight_charge->national_charges_class_2) * $multiplier;
                                            }
                                        } else if ($class == 3) {
                                            if (strpos($weight_charge->national_charges_class_3, '%') !== FALSE) {
                                                $charges += (((floatval(str_replace('%', '', $weight_charge->national_charges_class_3)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0) * $multiplier;
                                            } else {
                                                $charges += intval($weight_charge->national_charges_class_3) * $multiplier;
                                            }
                                        } else {
                                            $charges += ($weight_charge->national_charges_class_0 * $multiplier);
                                        }
                                    }
                                    else{
                                        $charges += ($weight_charge->local_or_6hr * $multiplier);
                                    }
                                }
                            }
                        }
                        else {
                            $previous = FALSE;
                        }
                    }



                    if (strpos($discount, '%') !== FALSE) {
                        $discount = (floatval(str_replace('%', '', $discount)) / 100) * $charges;
                    }
                    else {
                        $discount = floatval($discount);
                    }

                    if ($charges < $discount) {
                        $result['weight_charges'] = ROUND($charges, 2, PHP_ROUND_HALF_DOWN);
                    }
                    else {
                        $result['weight_charges'] = ROUND(($charges - $discount), 2, PHP_ROUND_HALF_DOWN);
                    }

                    return $result;
                }
            }
            else {
                return FALSE;
            }
        }
        else {
            return FALSE;
        }
    }

    static public function calculate_international_weight($margin, $weight, $zone){

        $weight_charge = InternationalStandardDhlRate::where('range_up', '<=', $weight)->where('range_down', '>=', $weight);
        if($weight_charge->exists()){
            $weight_charge = $weight_charge->first();
            $today = Carbon::today();

            if ($margin > 0) {
                $discount = $margin;
            }
            else {
                $discount = 0;
            }
            if ($weight_charge->weight_addition == 0) {
                $zone_id = 'zone_'.$zone;
                $charges = $weight_charge[$zone_id];

                $discount = (100 + $margin) / 100;
                $charges = $discount * $charges;


                $result = array();

                $exchange_rate_charges = 0;
                $exchange_rate = GlobalSettings::where('type', 'international_exchange_rate');
                if($exchange_rate->exists()){
                    $exchange_rate = $exchange_rate->first();
                    $exchange_rate_charges = (float)$exchange_rate->text;
                }


                $charges = $charges * $exchange_rate_charges;
                $result['weight_charges'] = ROUND($charges, 2, PHP_ROUND_HALF_DOWN);



                if ($weight > 1) {
                    $result['chargeable_weight'] = (CEIL($weight * 2) / 2);
                }
                else {
                    $result['chargeable_weight'] = $weight;
                }

                return $result;
            }
            else {
                $multiplier = (intval($weight - $weight_charge->range_up) / $weight_charge->spkg) + 1;
                $zone_id = 'zone_'.$zone;
                $charges = ($weight_charge[$zone_id] * $multiplier);

                $result = array();

                $result['chargeable_weight'] = (CEIL(($weight_charge->spkg * (intval($weight / $weight_charge->spkg) + 1)) * 2) / 2);

                $previous = TRUE;

                while ($previous) {
                    $weight_charge = InternationalStandardDhlRate::where('id', '<', $weight_charge->id)->orderBy('id', 'desc');

                    if ($weight_charge->exists()) {
                        $weight_charge = $weight_charge->first();

                        if ($weight_charge->weight_addition == 0) {
                            $charges += $weight_charge[$zone_id];

                            $previous = FALSE;
                        }
                        else {
                            $multiplier = (intval($weight_charge->range_down - $weight_charge->range_up) / $weight_charge->spkg) + 1;

                            $charges += ($weight_charge[$zone_id] * $multiplier);

                        }
                    }
                    else {
                        $previous = FALSE;
                    }
                }

                $discount = (100 + $margin) / 100;
                $charges = $discount * $charges;

                $exchange_rate_charges = 0;
                $exchange_rate = GlobalSettings::where('type', 'international_exchange_rate');
                if($exchange_rate->exists()){
                    $exchange_rate = $exchange_rate->first();
                    $exchange_rate_charges = (float)$exchange_rate->text;;
                }


                $charges = $charges * $exchange_rate_charges;
                $result['weight_charges'] = ROUND($charges, 2, PHP_ROUND_HALF_DOWN);


                return $result;
            }

        }
    }

    static public function calculate_international_economic_weight($weight,$economic_rate)
    {
        if ($economic_rate->weight_addition == 0) {
            $charges = $economic_rate->flat_charges;

            $result = array();

            $result['weight_charges'] = ROUND($charges, 2, PHP_ROUND_HALF_DOWN);



            if ($weight > 1) {
                $result['chargeable_weight'] = (CEIL($weight * 2) / 2);
            }
            else {
                $result['chargeable_weight'] = $weight;
            }

            return $result;
        }
        else {
            $multiplier = (intval($weight - $economic_rate->range_up) / $economic_rate->kg_range) + 1;
            $charges = ($economic_rate->flat_charges * $multiplier);

            $result = array();

            $result['chargeable_weight'] = (CEIL(($economic_rate->kg_range * (intval($weight / $economic_rate->kg_range) + 1)) * 2) / 2);

            $previous = TRUE;

            $rate_id = $economic_rate->id;
            while ($previous) {
                $weight_charge = InternationalEconomyRate::where('id', '<', $rate_id)
                    ->where('zone_id',$economic_rate->zone_id)
                    ->where('user_id',$economic_rate->user_id)
                    ->orderBy('id', 'desc');


                if ($weight_charge->exists()) {
                    $weight_charge = $weight_charge->first();
                    $rate_id = $weight_charge->id;

                    if ($weight_charge->weight_addition == 0) {
                        $charges += $weight_charge->flat_charges;

                        $previous = FALSE;
                    }
                    else {
                        $multiplier = (intval($weight_charge->range_down - $weight_charge->range_up) / $weight_charge->kg_range) + 1;

                        $charges += ($weight_charge->flat_charges * $multiplier);

                    }
                }
                else {
                    $previous = FALSE;
                }
            }


            $result['weight_charges'] = ROUND($charges, 2, PHP_ROUND_HALF_DOWN);


            return $result;
        }
    }

    static public function weight($id) {
        $shipment = Shipment::find($id);
        if($shipment->business_category_id == 1){
            $result = self::calculate_weight($shipment->user->account_type_id, $shipment->user_id, $shipment->shipping_mode_id, $shipment->same_day_timing_id, $shipment->walk_in_delivery_type_id, $shipment->actual_weight , $shipment->pickup_address->city_id, $shipment->pickup_address->city->zone_id, $shipment->consignee_city_id, $shipment->booking_type_id, $shipment->amount);
        }
        else{
            $dhl_check = true;

            $zone_id = $shipment->consignee_city->zone_id;
            $international_economy_rate = InternationalEconomyRate::where('user_id',$shipment->user_id)
                ->where('zone_id',$zone_id)
                ->where('range_up', '<=', $shipment->actual_weight )
                ->where('range_down', '>=', $shipment->actual_weight );

            if($international_economy_rate->exists())
            {
                $international_economy_rate = $international_economy_rate->first();
                $result = self::calculate_international_economic_weight($shipment->actual_weight ,$international_economy_rate);
                $dhl_check = false;

                if($result){
                    self::international_credit_usage($shipment->user_id, $result['weight_charges']);
                }
            }


            if($dhl_check) {
                $international_rate = InternationalUserRate::where('user_id', $shipment->user_id);
                if ($international_rate->exists()) {
                    $international_rate = $international_rate->first();
                    $zone_id = $shipment->consignee_city->zone_id;

                    $margin = 0;
                    $international_zone = InternationalDhlZone::where('zone_id', $zone_id)->first();
                    if ($international_zone) {
                        $international_zone_id = $international_zone->zone_name;
                        switch ($international_zone_id) {
                            case 1:
                                $margin = $international_rate->margin_1;
                                break;
                            case 2:
                                $margin = $international_rate->margin_2;
                                break;
                            case 3:
                                $margin = $international_rate->margin_3;
                                break;
                            case 4:
                                $margin = $international_rate->margin_4;
                                break;
                            case 5:
                                $margin = $international_rate->margin_5;
                                break;
                            case 6:
                                $margin = $international_rate->margin_6;
                                break;
                            case 7:
                                $margin = $international_rate->margin_7;
                                break;
                            case 8:
                                $margin = $international_rate->margin_8;
                                break;
                            case 9:
                                $margin = $international_rate->margin_9;
                                break;
                            case 10:
                                $margin = $international_rate->margin_10;
                                break;
                            case 11:
                                $margin = $international_rate->margin_11;
                                break;
                            default:
                                $margin = 0;
                                break;
                        }
                        $result = self::calculate_international_weight($margin, $shipment->actual_weight , $international_zone->zone_name);

                        if($result){
                            self::international_credit_usage($shipment->user_id, $result['weight_charges']);
                        }
                    } else {
                        $result = false;
                    }
                } else {
                    $result = false;
                }
            }
        }

        if ($result) {
            
           
            if($shipment->booking_type_id == 6){
                $other_amount = FtlRequestAdditionalCost::where('ftl_request_id',$shipment->ftl->id)->sum('amount');
                $calc_total = ((($shipment->ftl->freight_charges/$shipment->ftl->weight)*$shipment->actual_weight ));
                $shipment->weight_charges = $calc_total;
                $shipment->chargeable_weight = $result['chargeable_weight'];

            }else{
                $shipment->weight_charges = $result['weight_charges'];
                $shipment->chargeable_weight = $result['chargeable_weight'];
            }
            $shipment->save();
        }
    }

    static public function calculate_cash_handling($account_type_id, $user_id, $shipping_mode_id, $amount) {
        $rate_type_id = User::find($user_id)->corporate_rate_type_id;
        if ($account_type_id == 1) {
            $rate_status = RateStatus::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id)->where('cash_handling_charges', 1)->where('status', 1);
        }
        else{
            if($rate_type_id == 3){
                $rate_status = CorporateDefaultRateStatus::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id)->where('cash_handling_charges', 1)->where('status', 1);
            }
            else{
                $rate_status = CorporateRateStatus::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id)->where('cash_handling_charges', 1)->where('status', 1);
            }
        }

        if ($rate_status->exists()) {
            if ($account_type_id == 1) {
                $cash_handling_charge = CashHandlingCharge::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id)->where('range_up', '<=', $amount)->where('range_down', '>=', $amount);
            }
            else {
                if($rate_type_id == 3){
                    $cash_handling_charge = CorporateDefaultCashHandlingCharge::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id)->where('range_up', '<=', $amount)->where('range_down', '>=', $amount);
                }
                else{
                    $cash_handling_charge = CorporateCashHandlingCharge::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id)->where('range_up', '<=', $amount)->where('range_down', '>=', $amount);
                }
            }

            if ($cash_handling_charge->exists()) {
                $cash_handling_charge = $cash_handling_charge->first();

                $today = Carbon::today();

                if ($account_type_id == 1) {
                    $discount_charge = DiscountCharge::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
                }
                else {
                    if($rate_type_id == 3){
                        $discount_charge = CorporateDefaultDiscountCharge::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
                    }
                    else{
                        $discount_charge = CorporateDiscountCharge::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
                    }
                }

                if ($discount_charge->exists()) {
                    $discount_charge = $discount_charge->first();

                    $discount = $discount_charge->cash;
                }
                else {
                    $discount = 0;
                }

                $result = array();

                $charges = $cash_handling_charge->charges;

                if ($charges != 0) {
                    if (strpos($charges, '%') !== FALSE) {
                        $charges = (floatval(str_replace('%', '', $charges)) / 100) * $amount;
                    }
                    else {
                        $charges = floatval($charges);
                    }

                    if (strpos($discount, '%') !== FALSE) {
                        $discount = (floatval(str_replace('%', '', $discount)) / 100) * $charges;
                    }
                    else {
                        $discount = floatval($discount);
                    }

                    if ($charges < $discount) {
                        $result['cash_handling_charges'] = ROUND($charges, 2, PHP_ROUND_HALF_DOWN);
                    }
                    else {
                        $result['cash_handling_charges'] = ROUND(($charges - $discount), 2, PHP_ROUND_HALF_DOWN);
                    }
                }
                else {
                    $result['cash_handling_charges'] = 0;
                }

                return $result;
            }
            else {
                return FALSE;
            }
        }
        else {
            return FALSE;
        }
    }

    static public function calculate_international_cash_handling($user_id, $box_id, $amount) {

        $rate_status = InternationalRatesStatus::where('user_id', $user_id)->where('box_id', $box_id)->where('cash_handling_charges', 1)->where('status', 1);
        if ($rate_status->exists()) {

            $cash_handling_charge = InternationalRatesCashHandlingCharges::where('user_id', $user_id)->where('box_id', $box_id)->where('range_up', '<=', $amount)->where('range_down', '>=', $amount);

            if ($cash_handling_charge->exists()) {
                $cash_handling_charge = $cash_handling_charge->first();

                $today = Carbon::today();

                $discount_charge = InternationalRatesDiscountCharges::where('user_id', $user_id)->where('box_id', $box_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);


                if ($discount_charge->exists()) {
                    $discount_charge = $discount_charge->first();

                    $discount = $discount_charge->cash;
                }
                else {
                    $discount = 0;
                }

                $result = array();

                $charges = $cash_handling_charge->charges;

                if ($charges != 0) {
                    if (strpos($charges, '%') !== FALSE) {
                        $charges = (floatval(str_replace('%', '', $charges)) / 100) * $amount;
                    }
                    else {
                        $charges = floatval($charges);
                    }

                    if (strpos($discount, '%') !== FALSE) {
                        $discount = (floatval(str_replace('%', '', $discount)) / 100) * $charges;
                    }
                    else {
                        $discount = floatval($discount);
                    }

                    if ($charges < $discount) {
                        $result['cash_handling_charges'] = ROUND($charges, 2, PHP_ROUND_HALF_DOWN);
                    }
                    else {
                        $result['cash_handling_charges'] = ROUND(($charges - $discount), 2, PHP_ROUND_HALF_DOWN);
                    }
                }
                else {
                    $result['cash_handling_charges'] = 0;
                }

                return $result;
            }
            else {
                return FALSE;
            }
        }
        else {
            return FALSE;
        }
    }

    static public function cash_handling($id) {
        $shipment = Shipment::find($id);
        if($shipment->business_category_id == 1){
            $result = self::calculate_cash_handling($shipment->user->account_type_id, $shipment->user_id, $shipment->shipping_mode_id, $shipment->amount);
        }
        else{
            $box_id = self::international_box_id($shipment->user_id, $shipment->consignee_city_id);
            if($box_id != null){
                $result = self::calculate_international_cash_handling($shipment->user_id, $box_id, $shipment->amount);
            }else{
                $result = false;
            }
        }

        if ($result) {
            $shipment->cash_handling_charges = $result['cash_handling_charges'];

            $shipment->save();
        }
    }

    static public function insurance($id) {
        $shipment = Shipment::find($id);
        $rate_type_id = $shipment->user->corporate_rate_type_id;
        if ($shipment->amount != 0) {
            if ($shipment->business_category_id == 1) {
                $account_type_id = $shipment->user->account_type_id;

                if ($account_type_id == 1) {
                    $rate_status = RateStatus::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('insurance_charges', 1)->where('status', 1);
                } else {
                    if($rate_type_id == 3){
                        $rate_status = CorporateDefaultRateStatus::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('insurance_charges', 1)->where('status', 1);
                    }
                    else{
                        $rate_status = CorporateRateStatus::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('insurance_charges', 1)->where('status', 1);
                    }
                }

                if ($rate_status->exists()) {
                    $charges = 0;

                    foreach ($shipment->items as $item) {
                        $price = $item->price;

                        if ($item->insurance == 1) {
                            if ($account_type_id == 1) {
                                $insurance_charge = InsuranceCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('range_up', '<=', $price)->where('range_down', '>=', $price);
                            } else {
                                if($rate_type_id == 3){
                                    $insurance_charge = CorporateDefaultInsuranceCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('range_up', '<=', $price)->where('range_down', '>=', $price);
                                }
                                else{
                                    $insurance_charge = CorporateInsuranceCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('range_up', '<=', $price)->where('range_down', '>=', $price);
                                }
                            }

                            if ($insurance_charge->exists()) {
                                $insurance_charge = $insurance_charge->first();

                                $today = Carbon::today();

                                if ($account_type_id == 1) {
                                    $discount_charge = DiscountCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
                                } else {
                                    if($rate_type_id == 3){
                                        $discount_charge = CorporateDefaultDiscountCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
                                    }
                                    else{
                                        $discount_charge = CorporateDiscountCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
                                    }
                                }

                                if ($discount_charge->exists()) {
                                    $discount_charge = $discount_charge->first();

                                    $discount = $discount_charge->insurance;
                                } else {
                                    $discount = 0;
                                }

                                $item_charges = $insurance_charge->charges;

                                if (strpos($item_charges, '%') !== FALSE) {
                                    $charges += (floatval(str_replace('%', '', $item_charges)) / 100) * $price;
                                } else {
                                    $charges += floatval($item_charges);
                                }
                            }
                        }
                    }

                    if ($charges != 0) {
                        if (strpos($discount, '%') !== FALSE) {
                            $discount = (floatval(str_replace('%', '', $discount)) / 100) * $charges;
                        } else {
                            $discount = floatval($discount);
                        }

                        if ($charges < $discount) {
                            $shipment->insurance_charges = ROUND($charges, 2, PHP_ROUND_HALF_DOWN);
                        } else {
                            $shipment->insurance_charges = ROUND(($charges - $discount), 2, PHP_ROUND_HALF_DOWN);
                        }

                        $shipment->save();
                    }
                }

            } else {
                $box_id = self::international_box_id($shipment->user_id, $shipment->consignee_city_id);
                if($box_id != null){
                    $rate_status = InternationalRatesStatus::where('user_id', $shipment->user_id)->where('box_id', $box_id)->where('insurance_charges', 1)->where('status', 1);
                    if ($rate_status->exists()) {
                        $charges = 0;

                        foreach ($shipment->items as $item) {
                            $price = $item->price;

                            if ($item->insurance == 1) {
                                $insurance_charge = InternationalRatesInsuranceCharges::where('user_id', $shipment->user_id)->where('box_id', $box_id)->where('range_up', '<=', $price)->where('range_down', '>=', $price);


                                if ($insurance_charge->exists()) {
                                    $insurance_charge = $insurance_charge->first();

                                    $today = Carbon::today();


                                    $discount_charge = InternationalRatesDiscountCharges::where('user_id', $shipment->user_id)->where('box_id',
                                        $box_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);


                                    if ($discount_charge->exists()) {
                                        $discount_charge = $discount_charge->first();

                                        $discount = $discount_charge->insurance;
                                    } else {
                                        $discount = 0;
                                    }

                                    $item_charges = $insurance_charge->charges;

                                    if (strpos($item_charges, '%') !== FALSE) {
                                        $charges += (floatval(str_replace('%', '', $item_charges)) / 100) * $price;
                                    } else {
                                        $charges += floatval($item_charges);
                                    }
                                }
                            }
                        }

                        if ($charges != 0) {
                            if (strpos($discount, '%') !== FALSE) {
                                $discount = (floatval(str_replace('%', '', $discount)) / 100) * $charges;
                            } else {
                                $discount = floatval($discount);
                            }

                            if ($charges < $discount) {
                                $shipment->insurance_charges = ROUND($charges, 2, PHP_ROUND_HALF_DOWN);
                            } else {
                                $shipment->insurance_charges = ROUND(($charges - $discount), 2, PHP_ROUND_HALF_DOWN);
                            }

                            $shipment->save();
                        }
                    }
                }
            }
        }
    }

    static public function return($id) {
        $shipment = Shipment::find($id);
        if($shipment->business_category_id == 1){
            $account_type_id = $shipment->user->account_type_id;
            $rate_type_id = $shipment->user->corporate_rate_type_id;
            $zone_wise = 1;
            if ($account_type_id == 1) {
                $rate_status = RateStatus::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('return_charges', 1)->where('status', 1);
            }
            else {
                if($rate_type_id != 3){
                    $rate_status = CorporateRateStatus::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('return_charges', 1)->where('status', 1);
                }
                else{
                    $rate_status = CorporateDefaultRateStatus::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('return_charges', 1)->where('status', 1);
                }
            }

            if ($rate_status->exists()) {
                if ($account_type_id == 1) {
                    $return_charge = ReturnCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id);
                }
                else {
                    if($rate_type_id == null || $rate_type_id == 1){
                        $return_charge = CorporateReturnCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id);
                    }
                    else if($rate_type_id == 2){
                        $return_charge = CorporateReturnChargeZoneWise::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id);
                    }
                    else{
                        $return_charge = CorporateDefaultReturnCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id);
                    }
                }

                $today = Carbon::today();

                if ($account_type_id == 1) {
                    $discount_charge = DiscountCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
                }
                else {
                    if($rate_type_id == 3){
                        $discount_charge = CorporateDefaultDiscountCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);}
                    else{
                        $discount_charge = CorporateDiscountCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
                    }
                }

                if ($discount_charge->exists()) {
                    $discount_charge = $discount_charge->first();

                    $discount = $discount_charge->return;
                }
                else {
                    $discount = 0;
                }

                if ($return_charge->exists()) {
                    $return_charge = $return_charge->first();

                    $class = 0;

                    if ($shipment->shipping_mode_id == 4) {
                        if ($shipment->same_day_timing_id == 1) {
                            $type_of_charges = 0;
                        }
                        else {
                            $type_of_charges = 1;
                        }
                    }
                    else {
                        if ($shipment->pickup_address->city_id == $shipment->consignee_city_id) {
                            $type_of_charges = 0;
                        }
                        else {
                            $type_of_charges = 1;

                            if($rate_type_id == null || $rate_type_id == 1 || $rate_type_id == 3){
                                $zone_class_city = ZoneClassCity::where('zone_id', $shipment->pickup_address->city->zone_id)->where('city_id', $shipment->consignee_city_id);

                                if ($shipment->shipping_mode_id == 2 || $shipment->shipping_mode_id == 3) {
                                    $zone_class_city = $zone_class_city->where('zone_classification_id', 2);
                                }
                                else {
                                    $zone_class_city = $zone_class_city->where('zone_classification_id', 1);
                                }

                                if ($zone_class_city->exists()) {
                                    $zone_class_city = $zone_class_city->first();

                                    $class = $zone_class_city->class;
                                }
                            }
                            else{
                                $consignee_zone_id = City::find($shipment->consignee_city_id)->zone_id;
                                $destination_zone_id = $shipment->pickup_address->city->zone_id;
                                if($destination_zone_id == $consignee_zone_id){
                                    $zone_wise = 1;
                                }else{
                                    $zone_wise = 2;
                                }
                            }
                        }
                    }

                    if ($type_of_charges == 0) {
                        $charges = $return_charge->local;
                    }
                    else {
                        if($rate_type_id == null || $rate_type_id == 1 || $rate_type_id == 3){
                            if ($class == 1) {
                                if (strpos($return_charge->national_charges_class_1, '%') !== FALSE) {
                                    $charges = ((floatval(str_replace('%', '', $return_charge->national_charges_class_1)) / 100) * $return_charge->national_charges_class_0) + $return_charge->national_charges_class_0;
                                }
                                else {
                                    $charges = intval($return_charge->national_charges_class_1);
                                }
                            }
                            else if ($class == 2) {
                                if (strpos($return_charge->national_charges_class_2, '%') !== FALSE) {
                                    $charges = ((floatval(str_replace('%', '', $return_charge->national_charges_class_2)) / 100) * $return_charge->national_charges_class_0) + $return_charge->national_charges_class_0;
                                }
                                else {
                                    $charges = intval($return_charge->national_charges_class_2);
                                }
                            }
                            else if ($class == 3) {
                                if (strpos($return_charge->national_charges_class_3, '%') !== FALSE) {
                                    $charges = ((floatval(str_replace('%', '', $return_charge->national_charges_class_3)) / 100) * $return_charge->national_charges_class_0) + $return_charge->national_charges_class_0;
                                }
                                else {
                                    $charges = intval($return_charge->national_charges_class_3);
                                }
                            }
                            else {
                                $charges = $return_charge->national_charges_class_0;
                            }
                        }
                        else{
                            if($zone_wise == 1){
                                if (strpos($return_charge->same_zone, '%') !== FALSE) {
                                    $charges = ((floatval(str_replace('%', '', $return_charge->same_zone)) / 100) * $return_charge->local) + $return_charge->local;
                                }
                                else {
                                    $charges = intval($return_charge->same_zone);
                                }
                            }
                            else{
                                if (strpos($return_charge->different_zone, '%') !== FALSE) {
                                    $charges = ((floatval(str_replace('%', '', $return_charge->different_zone)) / 100) * $return_charge->local) + $return_charge->local;
                                }
                                else {
                                    $charges = intval($return_charge->different_zone);
                                }
                            }
                        }

                    }

                    if (strpos($discount, '%') !== FALSE) {
                        $discount = (floatval(str_replace('%', '', $discount)) / 100) * $charges;
                    }
                    else {
                        $discount = floatval($discount);
                    }

                    if ($charges < $discount) {
                        $shipment->return_charges = ROUND($charges, 2, PHP_ROUND_HALF_DOWN);
                    }
                    else {
                        $shipment->return_charges = ROUND(($charges - $discount), 2, PHP_ROUND_HALF_DOWN);
                    }

                    $shipment->save();
                }
            }
        }
        else{
            $box_id = self::international_box_id($shipment->user_id, $shipment->consignee_city_id);
            if($box_id != null){
                $rate_status = InternationalRatesStatus::where('user_id', $shipment->user_id)->where('box_id', $box_id)->where('return_charges', 1)->where('status', 1);
                if($rate_status->exists()){
                    $return_charge = InternationalRatesReturnCharges::where('user_id', $shipment->user_id)->where('box_id', $box_id);
                    $today = Carbon::today();

                    $discount_charge = InternationalRatesDiscountCharges::where('user_id', $shipment->user_id)->where('box_id', $box_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
                    if ($discount_charge->exists()) {
                        $discount_charge = $discount_charge->first();

                        $discount = $discount_charge->return;
                    }
                    else {
                        $discount = 0;
                    }

                    if ($return_charge->exists()) {
                        $return_charge = $return_charge->first();

                        $charges = $return_charge->local;


                        if (strpos($discount, '%') !== FALSE) {
                            $discount = (floatval(str_replace('%', '', $discount)) / 100) * $charges;
                        }
                        else {
                            $discount = floatval($discount);
                        }

                        if ($charges < $discount) {
                            $shipment->return_charges = ROUND($charges, 2, PHP_ROUND_HALF_DOWN);
                        }
                        else {
                            $shipment->return_charges = ROUND(($charges - $discount), 2, PHP_ROUND_HALF_DOWN);
                        }

                        $shipment->save();
                    }
                }
            }
        }

    }

    static public function calculate_fuel_surcharge($account_type_id, $user_id, $shipping_mode_id, $weight_charges) {

        $rate_type_id = User::find($user_id)->corporate_rate_type_id;
        if ($account_type_id == 1) {
            $rate_status = RateStatus::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id)->where('fuel_charges', 1)->where('status', 1);
        }
        else {
            if($rate_type_id == 3){
                $rate_status = CorporateDefaultRateStatus::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id)->where('fuel_charges', 1)->where('status', 1);
            }
            else{
                $rate_status = CorporateRateStatus::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id)->where('fuel_charges', 1)->where('status', 1);
            }
        }

        if ($rate_status->exists()) {
            if ($account_type_id == 1) {
                $fuel_charge = FuelSurcharge::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id);
            }
            else {
                if($rate_type_id == 3){
                    $fuel_charge = CorporateDefaultFuelSurcharge::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id);
                }
                else{
                    $fuel_charge = CorporateFuelSurcharge::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id);
                }
            }

            if ($fuel_charge->exists()) {
                $fuel_charge = $fuel_charge->first();

                $result = array();

                $result['fuel_surcharge'] = ROUND((($fuel_charge->fuel_surcharge / 100) * $weight_charges), 2, PHP_ROUND_HALF_DOWN);

                return $result;
            }
        }
        else {
            return FALSE;
        }
    }

    static public function fuel_surcharge($id) {
        $shipment = Shipment::find($id);

        $result = self::calculate_fuel_surcharge($shipment->user->account_type_id, $shipment->user_id, $shipment->shipping_mode_id, $shipment->weight_charges);

        if ($result) {
            $shipment->fuel_surcharge = $result['fuel_surcharge'];

            $shipment->save();
        }
    }

    static public function replacement($id) {
        $shipment = Shipment::find($id);
        if($shipment->business_category_id == 2){
            return false;
        }
        $account_type_id = $shipment->user->account_type_id;
        $rate_type_id = $shipment->user->corporate_rate_type_id;


        if ($account_type_id == 1) {
            $booking_type_charge = BookingTypeCharges::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id);
        }
        else {
            if($rate_type_id == 3){
                $booking_type_charge = CorporateDefaultBookingTypeCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id);
            }
            else{
                $booking_type_charge = CorporateBookingTypeCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id);
            }
        }

        if ($booking_type_charge->exists()) {
            $booking_type_charge = $booking_type_charge->first();

            $replacement_multiplier = ($booking_type_charge->replacement_charges / 100);

            $weight = ($shipment->replacement_weight) ? $shipment->replacement_weight : 0.15;

            if ($shipment->user_id == 7762 && $shipment->shipping_mode_id == 1) {
                if ($shipment->pickup_address->city_id == $shipment->consignee_city_id) {
                    $type_of_charges = 0;
                }
                else {
                    $type_of_charges = 1;
                }

                if ($shipment->amount == 0) {
                    if ($type_of_charges == 0) {
                        $charges = 37.5;
                    }
                    else {
                        $charges = 50;
                    }
                }
                else {
                    if ($type_of_charges == 0) {
                        $charges = 75;
                    }
                    else {
                        $charges = 100;
                    }
                }

                if ($weight > 1) {
                    $multiplier = ceil(($weight - 1) / 0.5);

                    if ($shipment->amount == 0) {
                        if ($type_of_charges == 0) {
                            $charges += (17.5 * $multiplier);
                        }
                        else {
                            $charges += (22.5 * $multiplier);
                        }
                    }
                    else {
                        if ($type_of_charges == 0) {
                            $charges += (35 * $multiplier);
                        }
                        else {
                            $charges += (45 * $multiplier);
                        }
                    }
                }

                $shipment->replacement_charges = ($charges * $replacement_multiplier);

                $shipment->save();

                return;
            }

            if ($account_type_id == 1) {
                $weight_charge = WeightCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('range_up', '<=', $weight)->where('range_down', '>=', $weight);
            }
            else {
                if($rate_type_id == 1){
                    $weight_charge = CorporateWeightCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('delivery_type_id', $shipment->walk_in_delivery_type_id)->where('range_up', '<=', $weight)->where('range_down', '>=', $weight);
                }
                else if($rate_type_id == 2){
                    $weight_charge = CorporateWeightChargeZoneWise::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('delivery_type_id', $shipment->walk_in_delivery_type_id)->where('range_up', '<=', $weight)->where('range_down', '>=', $weight);
                }
                else{
                    $weight_charge = CorporateDefaultWeightCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('range_up', '<=', $weight)->where('range_down', '>=', $weight);
                }

            }

            if ($weight_charge->exists()) {
                $weight_charge = $weight_charge->first();

                $today = Carbon::today();

                if ($account_type_id == 1) {
                    $discount_charge = DiscountCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
                }
                else {
                    if($rate_type_id == 3){
                        $discount_charge = CorporateDefaultDiscountCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
                    }
                    else{
                        $discount_charge = CorporateDiscountCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
                    }
                }

                if ($discount_charge->exists()) {
                    $discount_charge = $discount_charge->first();

                    $discount = $discount_charge->weight;
                }
                else {
                    $discount = 0;
                }

                $class = 0;
                $zone_wise = 1;
                if ($shipment->shipping_mode_id == 4) {
                    if ($shipment->same_day_timing_id == 1) {
                        $type_of_charges = 0;
                    }
                    else {
                        $type_of_charges = 1;
                    }
                }
                else {
                    if ($shipment->pickup_address->city_id == $shipment->consignee_city_id) {
                        $type_of_charges = 0;
                    }
                    else {
                        $type_of_charges = 1;

                        if($rate_type_id == null || $rate_type_id == 1 || $rate_type_id == 3){
                            $zone_class_city = ZoneClassCity::where('zone_id', $shipment->pickup_address->city->zone_id)->where('city_id', $shipment->consignee_city_id);

                            if ($shipment->shipping_mode_id == 2 || $shipment->shipping_mode_id == 3) {
                                $zone_class_city = $zone_class_city->where('zone_classification_id', 2);
                            }
                            else {
                                $zone_class_city = $zone_class_city->where('zone_classification_id', 1);
                            }

                            if ($zone_class_city->exists()) {
                                $zone_class_city = $zone_class_city->first();

                                $class = $zone_class_city->class;
                            }
                        }
                        else{
                            $destination_zone_id = $shipment->consignee_city_id->zone_id;
                            $origin_city_zone_id = $shipment->pickup_address->city->zone_id;
                            if($destination_zone_id == $origin_city_zone_id){
                                $zone_wise = 1;
                            }else{
                                $zone_wise = 2;
                            }
                        }
                    }
                }

                if ($weight_charge->weight_addition == 0 || $account_type_id == 2) {
                    if ($type_of_charges == 0) {
                        if($rate_type_id == null || $rate_type_id == 1 || $rate_type_id == 3){
                            $charges = $weight_charge->local_or_6hr;
                        }
                        else{
                            $charges = $weight_charge->local;
                        }
                    }
                    else {
                        if($rate_type_id == null || $rate_type_id == 1 || $rate_type_id == 3){
                            if ($class == 1) {
                                if (strpos($weight_charge->national_charges_class_1, '%') !== FALSE) {
                                    $charges = ((floatval(str_replace('%', '', $weight_charge->national_charges_class_1)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0;
                                }
                                else {
                                    $charges = intval($weight_charge->national_charges_class_1);
                                }
                            }
                            else if ($class == 2) {
                                if (strpos($weight_charge->national_charges_class_2, '%') !== FALSE) {
                                    $charges = ((floatval(str_replace('%', '', $weight_charge->national_charges_class_2)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0;
                                }
                                else {
                                    $charges = intval($weight_charge->national_charges_class_2);
                                }
                            }
                            else if ($class == 3) {
                                if (strpos($weight_charge->national_charges_class_3, '%') !== FALSE) {
                                    $charges = ((floatval(str_replace('%', '', $weight_charge->national_charges_class_3)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0;
                                }
                                else {
                                    $charges = intval($weight_charge->national_charges_class_3);
                                }
                            }
                            else {
                                $charges = $weight_charge->national_charges_class_0;
                            }
                        }
                        else{
                            if($zone_wise == 1){
                                if (strpos($weight_charge->same_zone, '%') !== FALSE) {
                                    $charges = ((floatval(str_replace('%', '', $weight_charge->same_zone)) / 100) * $weight_charge->local) + $weight_charge->local;
                                } else {
                                    $charges = intval($weight_charge->same_zone);
                                }
                            }
                            else{
                                if (strpos($weight_charge->different_zone, '%') !== FALSE) {
                                    $charges = ((floatval(str_replace('%', '', $weight_charge->different_zone)) / 100) * $weight_charge->local) + $weight_charge->local;
                                } else {
                                    $charges = intval($weight_charge->different_zone);
                                }
                            }
                        }

                    }

                    if ($account_type_id == 2) {
                        $charges = $charges * ROUND($shipment->actual_weight , 0);
                    }

                    $charges = ($charges * $replacement_multiplier);

                    if (strpos($discount, '%') !== FALSE) {
                        $discount = (floatval(str_replace('%', '', $discount)) / 100) * $charges;
                    }
                    else {
                        $discount = floatval($discount);
                    }

                    if ($charges < $discount) {
                        $shipment->replacement_charges = ROUND($charges, 2, PHP_ROUND_HALF_DOWN);
                    }
                    else {
                        $shipment->replacement_charges = ROUND(($charges - $discount), 2, PHP_ROUND_HALF_DOWN);
                    }

                    $shipment->save();
                }
                else {
                    $multiplier = (intval($weight - $weight_charge->range_up) / $weight_charge->spkg) + 1;

                    if ($type_of_charges == 0) {
                        if($rate_type_id == null || $rate_type_id == 1 || $rate_type_id == 3){
                            $charges = ($weight_charge->local_or_6hr * $multiplier);
                        }
                        else{
                            $charges = ($weight_charge->local * $multiplier);
                        }
                    }
                    else {
                        if($rate_type_id == null || $rate_type_id == 1 || $rate_type_id == 3){
                            if ($class == 1) {
                                if (strpos($weight_charge->national_charges_class_1, '%') !== FALSE) {
                                    $charges = (((floatval(str_replace('%', '', $weight_charge->national_charges_class_1)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0) * $multiplier;
                                }
                                else {
                                    $charges = intval($weight_charge->national_charges_class_1) * $multiplier;
                                }
                            }
                            else if ($class == 2) {
                                if (strpos($weight_charge->national_charges_class_2, '%') !== FALSE) {
                                    $charges = (((floatval(str_replace('%', '', $weight_charge->national_charges_class_2)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0) * $multiplier;
                                }
                                else {
                                    $charges = intval($weight_charge->national_charges_class_2) * $multiplier;
                                }
                            }
                            else if ($class == 3) {
                                if (strpos($weight_charge->national_charges_class_3, '%') !== FALSE) {
                                    $charges = (((floatval(str_replace('%', '', $weight_charge->national_charges_class_3)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0) * $multiplier;
                                }
                                else {
                                    $charges = intval($weight_charge->national_charges_class_3) * $multiplier;
                                }
                            }
                            else {
                                $charges = ($weight_charge->national_charges_class_0 * $multiplier);
                            }
                        }
                        else{
                            if($zone_wise == 1){
                                if (strpos($weight_charge->same_zone, '%') !== FALSE) {
                                    $charges = (((floatval(str_replace('%', '', $weight_charge->same_zone)) / 100) * $weight_charge->local) + $weight_charge->local) * $multiplier;
                                }
                                else {
                                    $charges = intval($weight_charge->same_zone) * $multiplier;
                                }
                            }
                            else{
                                if (strpos($weight_charge->different_zone, '%') !== FALSE) {
                                    $charges = (((floatval(str_replace('%', '', $weight_charge->different_zone)) / 100) * $weight_charge->local) + $weight_charge->local) * $multiplier;
                                }
                                else {
                                    $charges = intval($weight_charge->different_zone) * $multiplier;
                                }
                            }
                        }

                    }

                    $previous = TRUE;

                    while ($previous) {
                        if($rate_type_id == 3){
                            $weight_charge = CorporateDefaultWeightCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('id', '<', $weight_charge->id)->orderBy('id', 'desc');
                        }
                        else{
                            $weight_charge = WeightCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('id', '<', $weight_charge->id)->orderBy('id', 'desc');
                        }

                        if ($weight_charge->exists()) {
                            $weight_charge = $weight_charge->first();

                            if ($weight_charge->weight_addition == 0) {
                                if ($type_of_charges == 0) {
                                    $charges += $weight_charge->local_or_6hr;
                                }
                                else {
                                    if ($class == 1) {
                                        if (strpos($weight_charge->national_charges_class_1, '%') !== FALSE) {
                                            $charges += ((floatval(str_replace('%', '', $weight_charge->national_charges_class_1)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0;
                                        }
                                        else {
                                            $charges += intval($weight_charge->national_charges_class_1);
                                        }
                                    }
                                    else if ($class == 2) {
                                        if (strpos($weight_charge->national_charges_class_2, '%') !== FALSE) {
                                            $charges += ((floatval(str_replace('%', '', $weight_charge->national_charges_class_2)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0;
                                        }
                                        else {
                                            $charges += intval($weight_charge->national_charges_class_2);
                                        }
                                    }
                                    else if ($class == 3) {
                                        if (strpos($weight_charge->national_charges_class_3, '%') !== FALSE) {
                                            $charges += ((floatval(str_replace('%', '', $weight_charge->national_charges_class_3)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0;
                                        }
                                        else {
                                            $charges += intval($weight_charge->national_charges_class_3);
                                        }
                                    }
                                    else {
                                        $charges += $weight_charge->national_charges_class_0;
                                    }
                                }

                                $previous = FALSE;
                            }
                            else {
                                $multiplier = (intval($weight_charge->range_down - $weight_charge->range_up) / $weight_charge->spkg) + 1;

                                if ($type_of_charges == 0) {
                                    $charges += ($weight_charge->local_or_6hr * $multiplier);
                                }
                                else {
                                    if ($class == 1) {
                                        if (strpos($weight_charge->national_charges_class_1, '%') !== FALSE) {
                                            $charges += (((floatval(str_replace('%', '', $weight_charge->national_charges_class_1)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0) * $multiplier;
                                        }
                                        else {
                                            $charges += intval($weight_charge->national_charges_class_1) * $multiplier;
                                        }
                                    }
                                    else if ($class == 2) {
                                        if (strpos($weight_charge->national_charges_class_2, '%') !== FALSE) {
                                            $charges += (((floatval(str_replace('%', '', $weight_charge->national_charges_class_2)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0) * $multiplier;
                                        }
                                        else {
                                            $charges += intval($weight_charge->national_charges_class_2) * $multiplier;
                                        }
                                    }
                                    else if ($class == 3) {
                                        if (strpos($weight_charge->national_charges_class_3, '%') !== FALSE) {
                                            $charges += (((floatval(str_replace('%', '', $weight_charge->national_charges_class_3)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0) * $multiplier;
                                        }
                                        else {
                                            $charges += intval($weight_charge->national_charges_class_3) * $multiplier;
                                        }
                                    }
                                    else {
                                        $charges += ($weight_charge->national_charges_class_0 * $multiplier);
                                    }
                                }
                            }
                        }
                        else {
                            $previous = FALSE;
                        }
                    }

                    if (strpos($discount, '%') !== FALSE) {
                        $discount = (floatval(str_replace('%', '', $discount)) / 100) * $charges;
                    }
                    else {
                        $discount = floatval($discount);
                    }

                    $charges = ($charges * $replacement_multiplier);

                    if ($charges < $discount) {
                        $shipment->replacement_charges = ROUND($charges, 2, PHP_ROUND_HALF_DOWN);
                    }
                    else {
                        $shipment->replacement_charges = ROUND(($charges - $discount), 2, PHP_ROUND_HALF_DOWN);
                    }

                    $shipment->save();
                }
            }
        }
    }

    static public function try_and_buy($id) {
        $shipment = Shipment::find($id);
        if($shipment->business_category_id == 2){
            return false;
        }
        $account_type_id = $shipment->user->account_type_id;
         $rate_type_id =  $shipment->user->corporate_rate_type_id;
        if ($account_type_id == 1) {
            $booking_type_charge = BookingTypeCharges::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id);
        }
        else {
            if($rate_type_id != 3){
                $booking_type_charge = CorporateBookingTypeCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id);
            }
            else{
                $booking_type_charge = CorporateDefaultBookingTypeCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id);
            }
        }

        if ($booking_type_charge->exists()) {
            $booking_type_charge = $booking_type_charge->first();

            $today = Carbon::today();

            if ($account_type_id == 1) {
                $discount_charge = DiscountCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
            }
            else {
                if($rate_type_id != 3){
                    $discount_charge = CorporateDiscountCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
                }
                else{
                    $discount_charge = CorporateDefaultDiscountCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
                }
            }

            if ($discount_charge->exists()) {
                $discount_charge = $discount_charge->first();

                $discount = $discount_charge->weight;
            }
            else {
                $discount = 0;
            }

            $try_and_buy_multiplier = ($booking_type_charge->try_and_buy_charges / 100);

            $charges = ($shipment->weight_charges * $try_and_buy_multiplier);

            if (strpos($discount, '%') !== FALSE) {
                $discount = (floatval(str_replace('%', '', $discount)) / 100) * $charges;
            }
            else {
                $discount = floatval($discount);
            }

            if ($charges < $discount) {
                $shipment->try_and_buy_charges = ROUND($charges, 2, PHP_ROUND_HALF_DOWN);
            }
            else {
                $shipment->try_and_buy_charges = ROUND(($charges - $discount), 2, PHP_ROUND_HALF_DOWN);
            }

            $shipment->save();
        }
    }

    static public function packaging_material($id, $type, $charges) {
        $shipment = Shipment::find($id);
        if($shipment->business_category_id == 2){
            return false;
        }
        $account_type_id = $shipment->user->account_type_id;
        $rate_type_id =  $shipment->user->corporate_rate_type_id;
        $today = Carbon::today();

        if ($account_type_id == 1) {
            $discount_charge = DiscountCharge::where('user_id', $shipment->user_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
        }
        else {
            if($rate_type_id != 3){
                $discount_charge = CorporateDiscountCharge::where('user_id', $shipment->user_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
            }
            else{
                $discount_charge = CorporateDefaultDiscountCharge::where('user_id', $shipment->user_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
            }
        }

        if ($discount_charge->exists()) {
            $discount_charge = $discount_charge->first();

            $discount = $discount_charge->packaging;

            if (strpos($discount, '%') !== FALSE) {
                $discount = (floatval(str_replace('%', '', $discount)) / 100) * $charges;
            }
            else {
                $discount = floatval($discount);
            }
        }
        else {
            $discount = 0;
        }

        if ($charges < $discount) {
            $charges = ROUND($charges, 2, PHP_ROUND_HALF_DOWN);
        }
        else {
            $charges = ROUND(($charges - $discount), 2, PHP_ROUND_HALF_DOWN);
        }

        if ($type == 1) {
            $shipment->amount = $charges;
        }

        $shipment->packaging_material_charges = $charges;

        $shipment->save();
    }

    static public function walk_in_return($id) {
        $shipment = Shipment::find($id);
        if($shipment->business_category_id == 1){
            $settings = WalkInStandardWeightCharge::where(['shipping_mode_id' => $shipment->shipping_mode_id, 'delivery_type_id' => $shipment->walk_in_delivery_type_id])->first();

            $class = 0;

            if ($shipment->shipping_mode_id == 4) {
                if ($shipment->same_day_timing_id == 1) {
                    $type_of_charges = 0;
                }
                else {
                    $type_of_charges = 1;
                }
            }
            else {
                if ($shipment->pickup_address->city_id == $shipment->consignee_city_id) {
                    $type_of_charges = 0;
                }
                else {
                    $type_of_charges = 1;

                    $zone_class_city = ZoneClassCity::where('zone_id', $shipment->pickup_address->city->zone_id)->where('city_id', $shipment->consignee_city_id);

                    if ($shipment->shipping_mode_id == 2 || $shipment->shipping_mode_id == 3) {
                        $zone_class_city = $zone_class_city->where('zone_classification_id', 2);
                    }
                    else {
                        $zone_class_city = $zone_class_city->where('zone_classification_id', 1);
                    }

                    if ($zone_class_city->exists()) {
                        $zone_class_city = $zone_class_city->first();

                        $class = $zone_class_city->class;
                    }
                }
            }

            if ($type_of_charges == 0) {
                $percentage = $settings['local'];
            }
            else {
                if ($class == 1) {
                    $percentage = $settings['national_charges_class_1'];
                }
                else if ($class == 2) {
                    $percentage = $settings['national_charges_class_2'];
                }
                else if ($class == 3) {
                    $percentage = $settings['national_charges_class_3'];
                }
                else {
                    $percentage = $settings['national_charges_class_0'];
                }
            }
        }
        else{
            $walk_in_international_hub = WalkInInternationalStandardWeightChargeHub::where('hub_id', $shipment->consignee_city->hub->id)->first();
            $settings = WalkInInternationalStandardWeightCharge::find($walk_in_international_hub->international_charges_id);

            if($shipment->delivery_type == 1){
                $percentage = $settings->door_return_charges;
            }
            else{
                $percentage = $settings->hub_return_charges;
            }
        }

        $charges = ROUND(($shipment->weight_charges * ($percentage / 100)), 2, PHP_ROUND_HALF_DOWN);

        $shipment->return_charges = $charges;

        if($shipment->charges_mode_id == 1) {
            $total_amount = $shipment->received_amount + $charges;

            $shipment->amount = $total_amount;
            $shipment->received_amount = $total_amount;
        }
        else {
            $total_amount = $shipment->amount + $charges;

            $shipment->amount = $total_amount;
            $shipment->received_amount = $total_amount;
        }

        $shipment->save();
    }

    static public function intercept($id, $previous_consignee_city_id, $new_consignee_city_id) {
        $shipment = Shipment::find($id);
        if($shipment->business_category_id == 2){
            return false;
        }
        $account_type_id = $shipment->user->account_type_id;
        $rate_type_id =  $shipment->user->corporate_rate_type_id;
        if ($account_type_id == 1) {
            $rate_status = RateStatus::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('status', 1);
        }
        else {
            if($rate_type_id != 3){
                $rate_status = CorporateRateStatus::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('status', 1);
            }
            else{
                $rate_status = CorporateDefaultRateStatus::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('status', 1);
            }
        }

        if ($rate_status->exists()) {
            $weight = $shipment->actual_weight ;

            if ($account_type_id == 1) {
                $weight_charge = WeightCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('range_up', '<=', $weight)->where('range_down', '>=', $weight);
            }
            else {
                if($rate_type_id != 3){
                    $weight_charge = CorporateWeightCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('delivery_type_id', $shipment->walk_in_delivery_type_id)->where('range_up', '<=', $weight)->where('range_down', '>=', $weight);
                }
                else{
                    $weight_charge = CorporateDefaultWeightCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('range_up', '<=', $weight)->where('range_down', '>=', $weight);

                }

            }

            if ($weight_charge->exists()) {
                $weight_charge = $weight_charge->first();

                $base = FALSE;
                if ($account_type_id == 2 && $rate_type_id != 3) {
                    $base_weight_charge = CorporateWeightCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('delivery_type_id', $shipment->walk_in_delivery_type_id)->where('id', '<', $weight_charge->id)->where('base', 1)->orderBy('id', 'DESC');

                    if ($base_weight_charge->exists()) {
                        $base_weight_charge = $base_weight_charge->first();

                        $base = TRUE;
                    }
                }

                $today = Carbon::today();

                if ($account_type_id == 1) {
                    $discount_charge = DiscountCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
                }
                else {
                    if($rate_type_id != 3){
                        $discount_charge = CorporateDiscountCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
                    }
                    else{
                        $discount_charge = CorporateDefaultDiscountCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
                    }

                }

                if ($discount_charge->exists()) {
                    $discount_charge = $discount_charge->first();

                    $discount = $discount_charge->weight;
                }
                else {
                    $discount = 0;
                }

                $class = 0;
                $zone_wise = 1; // same zone
                if ($shipment->shipping_mode_id == 4) {
                    if ($shipment->same_day_timing_id == 1) {
                        $type_of_charges = 0;
                    }
                    else {
                        $type_of_charges = 1;
                    }
                }
                else {
                    if ($previous_consignee_city_id == $new_consignee_city_id) {
                        $type_of_charges = 0;
                    }
                    else {
                        $type_of_charges = 1;
                        if($rate_type_id == null || $rate_type_id == 1 || $rate_type_id == 3){
                            $previous_consignee_city = City::find($previous_consignee_city_id);

                            $zone_class_city = ZoneClassCity::where('zone_id', $previous_consignee_city->zone_id)->where('city_id', $new_consignee_city_id);

                            if ($shipment->shipping_mode_id == 2 || $shipment->shipping_mode_id == 3) {
                                $zone_class_city = $zone_class_city->where('zone_classification_id', 2);
                            }
                            else {
                                $zone_class_city = $zone_class_city->where('zone_classification_id', 1);
                            }

                            if ($zone_class_city->exists()) {
                                $zone_class_city = $zone_class_city->first();

                                $class = $zone_class_city->class;
                            }
                        }
                        else{
                            $destination_zone_id = City::find($previous_consignee_city_id)->zone_id;
                            $previous_destination_zone_id = City::find($new_consignee_city_id)->zone_id;
                            if($destination_zone_id == $previous_destination_zone_id){
                                $zone_wise = 1;
                            }else{
                                $zone_wise = 2;
                            }
                        }

                    }
                }

                if ($weight_charge->weight_addition == 0 || ($account_type_id == 2 && $rate_type_id != 3)) {
                    if ($type_of_charges == 0) {
                        if($rate_type_id == null || $rate_type_id == 1 || $rate_type_id == 3){
                            $charges = $weight_charge->local_or_6hr;
                        }
                        else{
                            $charges = $weight_charge->local;
                        }
                    }
                    else {
                        if($rate_type_id == null || $rate_type_id == 1 || $rate_type_id == 3){
                            if ($class == 1) {
                                if (strpos($weight_charge->national_charges_class_1, '%') !== FALSE) {
                                    $charges = ((floatval(str_replace('%', '', $weight_charge->national_charges_class_1)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0;
                                } else {
                                    $charges = intval($weight_charge->national_charges_class_1);
                                }
                            } else if ($class == 2) {
                                if (strpos($weight_charge->national_charges_class_2, '%') !== FALSE) {
                                    $charges = ((floatval(str_replace('%', '', $weight_charge->national_charges_class_2)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0;
                                } else {
                                    $charges = intval($weight_charge->national_charges_class_2);
                                }
                            } else if ($class == 3) {
                                if (strpos($weight_charge->national_charges_class_3, '%') !== FALSE) {
                                    $charges = ((floatval(str_replace('%', '', $weight_charge->national_charges_class_3)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0;
                                } else {
                                    $charges = intval($weight_charge->national_charges_class_3);
                                }
                            } else {
                                $charges = $weight_charge->national_charges_class_0;
                            }
                        }
                        else{
                            if($zone_wise == 1){
                                if (strpos($weight_charge->same_zone, '%') !== FALSE) {
                                    $charges = ((floatval(str_replace('%', '', $weight_charge->same_zone)) / 100) * $weight_charge->local) + $weight_charge->local;
                                } else {
                                    $charges = intval($weight_charge->same_zone);
                                }
                            }
                            else{
                                if (strpos($weight_charge->different_zone, '%') !== FALSE) {
                                    $charges = ((floatval(str_replace('%', '', $weight_charge->different_zone)) / 100) * $weight_charge->local) + $weight_charge->local;
                                } else {
                                    $charges = intval($weight_charge->different_zone);
                                }
                            }
                        }
                    }


                    if ($account_type_id == 2 && $rate_type_id != 3) {
                        if ($base) {
                            $weight_difference = $weight - $base_weight_charge->range_down;

                            if ($weight_difference > 0) {
                                $charges = $charges * (ROUND($weight_difference, 0));
                            }
                            else {
                                $charges = 0;
                            }

                            if ($type_of_charges == 0) {
                                if($rate_type_id == null || $rate_type_id == 1){
                                    $charges += $base_weight_charge->local_or_6hr;
                                }
                                else{
                                    $charges += $weight_charge->local;
                                }
                            } else {
                                if($rate_type_id == null || $rate_type_id == 1 || $rate_type_id == 3){
                                    if ($class == 1) {
                                        if (strpos($base_weight_charge->national_charges_class_1, '%') !== FALSE) {
                                            $charges += ((floatval(str_replace('%', '', $base_weight_charge->national_charges_class_1)) / 100) * $base_weight_charge->national_charges_class_0) + $base_weight_charge->national_charges_class_0;
                                        } else {
                                            $charges += intval($base_weight_charge->national_charges_class_1);
                                        }
                                    } else if ($class == 2) {
                                        if (strpos($base_weight_charge->national_charges_class_2, '%') !== FALSE) {
                                            $charges += ((floatval(str_replace('%', '', $base_weight_charge->national_charges_class_2)) / 100) * $base_weight_charge->national_charges_class_0) + $base_weight_charge->national_charges_class_0;
                                        } else {
                                            $charges += intval($base_weight_charge->national_charges_class_2);
                                        }
                                    } else if ($class == 3) {
                                        if (strpos($base_weight_charge->national_charges_class_3, '%') !== FALSE) {
                                            $charges += ((floatval(str_replace('%', '', $base_weight_charge->national_charges_class_3)) / 100) * $base_weight_charge->national_charges_class_0) + $base_weight_charge->national_charges_class_0;
                                        } else {
                                            $charges += intval($base_weight_charge->national_charges_class_3);
                                        }
                                    } else {
                                        $charges += $base_weight_charge->national_charges_class_0;
                                    }
                                }
                                else{
                                    if($zone_wise == 1){
                                        if (strpos($base_weight_charge->same_zone, '%') !== FALSE) {
                                            $charges += ((floatval(str_replace('%', '', $base_weight_charge->same_zone)) / 100) * $base_weight_charge->local) + $base_weight_charge->local;
                                        } else {
                                            $charges += intval($base_weight_charge->same_zone);
                                        }
                                    }
                                    else{
                                        if (strpos($base_weight_charge->different_zone, '%') !== FALSE) {
                                            $charges += ((floatval(str_replace('%', '', $base_weight_charge->different_zone)) / 100) * $base_weight_charge->local) + $base_weight_charge->local;
                                        } else {
                                            $charges += intval($base_weight_charge->different_zone);
                                        }
                                    }
                                }

                            }
                        }
                        else {
                            $charges = $charges * ROUND($weight, 0);
                        }
                    }

                    if (strpos($discount, '%') !== FALSE) {
                        $discount = (floatval(str_replace('%', '', $discount)) / 100) * $charges;
                    }
                    else {
                        $discount = floatval($discount);
                    }

                    if ($charges < $discount) {
                        $shipment->intercept_charges = ROUND($charges, 2, PHP_ROUND_HALF_DOWN);
                    }
                    else {
                        $shipment->intercept_charges = ROUND(($charges - $discount), 2, PHP_ROUND_HALF_DOWN);
                    }

                    $shipment->save();
                }
                else {
                    $multiplier = (intval($weight - $weight_charge->range_up) / $weight_charge->spkg) + 1;

                    if ($type_of_charges == 0) {
                        $charges = ($weight_charge->local_or_6hr * $multiplier);
                    }
                    else {
                        if ($class == 1) {
                            if (strpos($weight_charge->national_charges_class_1, '%') !== FALSE) {
                                $charges = (((floatval(str_replace('%', '', $weight_charge->national_charges_class_1)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0) * $multiplier;
                            }
                            else {
                                $charges = intval($weight_charge->national_charges_class_1) * $multiplier;
                            }
                        }
                        else if ($class == 2) {
                            if (strpos($weight_charge->national_charges_class_2, '%') !== FALSE) {
                                $charges = (((floatval(str_replace('%', '', $weight_charge->national_charges_class_2)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0) * $multiplier;
                            }
                            else {
                                $charges = intval($weight_charge->national_charges_class_2) * $multiplier;
                            }
                        }
                        else if ($class == 3) {
                            if (strpos($weight_charge->national_charges_class_3, '%') !== FALSE) {
                                $charges = (((floatval(str_replace('%', '', $weight_charge->national_charges_class_3)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0) * $multiplier;
                            }
                            else {
                                $charges = intval($weight_charge->national_charges_class_3) * $multiplier;
                            }
                        }
                        else {
                            $charges = ($weight_charge->national_charges_class_0 * $multiplier);
                        }
                    }

                    $previous = TRUE;

                    while ($previous) {
                        if($rate_type_id == 3){
                            $weight_charge = CorporateDefaultWeightCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('id', '<', $weight_charge->id)->orderBy('id', 'desc');
                        }
                        else{
                            $weight_charge = WeightCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('id', '<', $weight_charge->id)->orderBy('id', 'desc');
                        }


                        if ($weight_charge->exists()) {
                            $weight_charge = $weight_charge->first();

                            if ($weight_charge->weight_addition == 0) {
                                if ($type_of_charges == 0) {
                                    $charges += $weight_charge->local_or_6hr;
                                }
                                else {
                                    if ($class == 1) {
                                        if (strpos($weight_charge->national_charges_class_1, '%') !== FALSE) {
                                            $charges += ((floatval(str_replace('%', '', $weight_charge->national_charges_class_1)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0;
                                        }
                                        else {
                                            $charges += intval($weight_charge->national_charges_class_1);
                                        }
                                    }
                                    else if ($class == 2) {
                                        if (strpos($weight_charge->national_charges_class_2, '%') !== FALSE) {
                                            $charges += ((floatval(str_replace('%', '', $weight_charge->national_charges_class_2)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0;
                                        }
                                        else {
                                            $charges += intval($weight_charge->national_charges_class_2);
                                        }
                                    }
                                    else if ($class == 3) {
                                        if (strpos($weight_charge->national_charges_class_3, '%') !== FALSE) {
                                            $charges += ((floatval(str_replace('%', '', $weight_charge->national_charges_class_3)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0;
                                        }
                                        else {
                                            $charges += intval($weight_charge->national_charges_class_3);
                                        }
                                    }
                                    else {
                                        $charges += $weight_charge->national_charges_class_0;
                                    }
                                }

                                $previous = FALSE;
                            }
                            else {
                                $multiplier = (intval($weight_charge->range_down - $weight_charge->range_up) / $weight_charge->spkg) + 1;

                                if ($type_of_charges == 0) {
                                    $charges += ($weight_charge->local_or_6hr * $multiplier);
                                }
                                else {
                                    if ($class == 1) {
                                        if (strpos($weight_charge->national_charges_class_1, '%') !== FALSE) {
                                            $charges += (((floatval(str_replace('%', '', $weight_charge->national_charges_class_1)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0) * $multiplier;
                                        }
                                        else {
                                            $charges += intval($weight_charge->national_charges_class_1) * $multiplier;
                                        }
                                    }
                                    else if ($class == 2) {
                                        if (strpos($weight_charge->national_charges_class_2, '%') !== FALSE) {
                                            $charges += (((floatval(str_replace('%', '', $weight_charge->national_charges_class_2)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0) * $multiplier;
                                        }
                                        else {
                                            $charges += intval($weight_charge->national_charges_class_2) * $multiplier;
                                        }
                                    }
                                    else if ($class == 3) {
                                        if (strpos($weight_charge->national_charges_class_3, '%') !== FALSE) {
                                            $charges += (((floatval(str_replace('%', '', $weight_charge->national_charges_class_3)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0) * $multiplier;
                                        }
                                        else {
                                            $charges += intval($weight_charge->national_charges_class_3) * $multiplier;
                                        }
                                    }
                                    else {
                                        $charges += ($weight_charge->national_charges_class_0 * $multiplier);
                                    }
                                }
                            }
                        }
                        else {
                            $previous = FALSE;
                        }
                    }

                    if (strpos($discount, '%') !== FALSE) {
                        $discount = (floatval(str_replace('%', '', $discount)) / 100) * $charges;
                    }
                    else {
                        $discount = floatval($discount);
                    }

                    if ($charges < $discount) {
                        $shipment->intercept_charges = ROUND($charges, 2, PHP_ROUND_HALF_DOWN);
                    }
                    else {
                        $shipment->intercept_charges = ROUND(($charges - $discount), 2, PHP_ROUND_HALF_DOWN);
                    }

                    $shipment->save();
                }
            }
        }
    }

    static public function nsa_osa_charges($id) {
        $shipment = Shipment::find($id);

        if ($shipment->nsa_osa_status == 1) {
            $shipment->nsa_osa_charges = $shipment->nsa_osa_estimated_charges;

            $shipment->save();
        }
    }

    static public  function walkin_weight($shipment_id){
        $shipment = Shipment::find($shipment_id);
        if($shipment){
            $charges_per_kg = 0;
            $actual_weight = $shipment->actual_weight ;
            $walkin_charges = WalkinShipmentWeightCharges::where('shipment_id', $shipment_id);
            if($walkin_charges->exists()){
                $walkin_charges = $walkin_charges->first();
                $charges_per_kg = $walkin_charges->charges_per_kg;
                $weight_charges = ROUND(($actual_weight * $charges_per_kg), 0, PHP_ROUND_HALF_DOWN);
                $fuel = StandardFuelSurcharge::where('shipping_mode_id',$shipment->shipping_mode_id)->first();
                $fuel_surcharge = ROUND(($fuel['fuel_surcharge']/100)*($weight_charges), 0, PHP_ROUND_HALF_DOWN);
                $city = City::where('id',$shipment->pickup_city_id)->first();
                $zone = Zone::where('id',$city['zone_id'])->first();
                $gst = ROUND(($zone['gst']*($weight_charges + $fuel_surcharge)), 0, PHP_ROUND_HALF_DOWN);

                if($shipment->charges_mode_id == 1) {
                    $receivable = ROUND(($fuel_surcharge + $weight_charges + $gst), 0, PHP_ROUND_HALF_DOWN);

                    $amount = 0;

                    $r_amount = $receivable;
                }
                else{
                    $receivable = ROUND(($fuel_surcharge + $weight_charges + $gst), 0, PHP_ROUND_HALF_DOWN);

                    $amount = $receivable;

                    $r_amount = NULL;
                }
                $shipment->chargeable_weight = $actual_weight;
                $shipment->weight_charges = $weight_charges;
                $shipment->fuel_surcharge = $fuel_surcharge;
                $shipment->gst = $gst;
                $shipment->amount = $amount;
                $shipment->received_amount = $r_amount;
                $shipment->save();
            }

        }

    }

    static public function international_box_id($user_id, $city_id){
        $city = City::find($city_id);
        $international_hub_id = $city->hub_id;
        $box_id = NULL;
        $international_rate_hub = InternationalRatesHub::where('user_id', $user_id)->where('hub_id', $international_hub_id)->select('box_id');
        if($international_rate_hub->exists()){
            $international_rate_hub = $international_rate_hub->first();
            $box_id = $international_rate_hub->box_id;
        }
        return $box_id;
    }

    static public function international_fuel_surcharge($id) {
        $shipment = Shipment::find($id);

        $fuel_surcharge = GlobalSettings::where('type', 'international_fuel_surcharge');
        if($fuel_surcharge->exists()){
            $fuel_surcharge = $fuel_surcharge->first();
            $fuel_charge = (float)$fuel_surcharge->text;
            $result = array();

            $result['fuel_surcharge'] = ROUND((($fuel_charge / 100) * $shipment->weight_charges), 2, PHP_ROUND_HALF_DOWN);
            if ($result) {
                $shipment->fuel_surcharge = $result['fuel_surcharge'];

                $shipment->save();
                self::international_credit_usage($shipment->user_id, $result['fuel_surcharge']);
            }
        }

    }

    static public function international_credit_usage($user_id, $amount){
        $credit_user = InternationalUsersCreditLimit::where('user_id', $user_id);
        if($credit_user->exists()){
            $credit_user = $credit_user->first();
            $credit_user->limit_usage = $credit_user->limit_usage + $amount;
            $credit_user->save();
        }
    }
}
