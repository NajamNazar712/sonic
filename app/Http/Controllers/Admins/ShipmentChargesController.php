<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;

use App\Http\Models\Shipment;

use App\Http\Models\RateStatus;
use App\Http\Models\WeightCharge;
use App\Http\Models\CashHandlingCharge;
use App\Http\Models\InsuranceCharge;
use App\Http\Models\ReturnCharge;
use App\Http\Models\FuelSurcharge;
use App\Http\Models\BookingTypeCharges;
use App\Http\Models\DiscountCharge;

use App\Http\Models\CorporateRateStatus;
use App\Http\Models\CorporateWeightCharge;
use App\Http\Models\CorporateCashHandlingCharge;
use App\Http\Models\CorporateInsuranceCharge;
use App\Http\Models\CorporateReturnCharge;
use App\Http\Models\CorporateFuelSurcharge;
use App\Http\Models\CorporateBookingTypeCharge;
use App\Http\Models\CorporateDiscountCharge;

use App\Http\Models\Admin\WalkInStandardWeightCharge;

use App\Http\Models\City;
use App\Http\Models\ZoneClassCity;

use Carbon\Carbon;

class ShipmentChargesController extends Controller
{
    static public function weight($id) {
        $shipment = Shipment::find($id);

        $account_type_id = $shipment->user->account_type_id;

        if ($account_type_id == 1) {
            $rate_status = RateStatus::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('status', 1);
        }
        else {
            $rate_status = CorporateRateStatus::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('status', 1);
        }

        if ($rate_status->exists()) {
            $weight = $shipment->actual_weight;

            if ($account_type_id == 1) {
                $weight_charge = WeightCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('range_up', '<=', $weight)->where('range_down', '>=', $weight);
            }
            else {
                $weight_charge = CorporateWeightCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('delivery_type_id', $shipment->walk_in_delivery_type_id)->where('range_up', '<=', $weight)->where('range_down', '>=', $weight);
            }

            if ($weight_charge->exists()) {
                $weight_charge = $weight_charge->first();

                $today = Carbon::today();

                if ($account_type_id == 1) {
                    $discount_charge = DiscountCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
                }
                else {
                    $discount_charge = CorporateDiscountCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
                }

                if ($discount_charge->exists()) {
                    $discount_charge = $discount_charge->first();

                    $discount = $discount_charge->weight;
                }
                else {
                    $discount = 0;
                }

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

                        if ($zone_class_city) {
                            $zone_class_city = $zone_class_city->first();

                            $class = $zone_class_city->class;
                        }
                    }
                }

                if ($weight_charge->weight_addition == 0 || $account_type_id == 2) {
                    if ($type_of_charges == 0) {
                        $charges = $weight_charge->local_or_6hr;
                    }
                    else {
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

                    if ($account_type_id == 2) {
                        $charges = $charges * ROUND($shipment->actual_weight, 0);
                    }

                    if ($charges < $discount) {
                        $shipment->weight_charges = ROUND($charges, 0, PHP_ROUND_HALF_DOWN);
                    }
                    else {
                        $shipment->weight_charges = ROUND(($charges - $discount), 0, PHP_ROUND_HALF_DOWN);
                    }

                    $shipment->chargeable_weight = ROUND($shipment->actual_weight, 0);

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

                    $shipment->chargeable_weight = $weight_charge->spkg * (intval($weight / $weight_charge->spkg) + 1);

                    $previous = TRUE;

                    while ($previous) {
                        $weight_charge = WeightCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('id', '<', $weight_charge->id)->orderBy('id', 'desc');

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
                        $shipment->weight_charges = ROUND($charges, 0, PHP_ROUND_HALF_DOWN);
                    }
                    else {
                        $shipment->weight_charges = ROUND(($charges - $discount), 0, PHP_ROUND_HALF_DOWN);
                    }

                    $shipment->save();
                }
            }
        }
    }

    static public function cash_handling($id) {
        $shipment = Shipment::find($id);

        $account_type_id = $shipment->user->account_type_id;

        if ($shipment->amount != 0) {
            if ($account_type_id == 1) {
                $rate_status = RateStatus::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('cash_handling_charges', 1)->where('status', 1);
            }
            else {
                $rate_status = CorporateRateStatus::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('cash_handling_charges', 1)->where('status', 1);
            }

            if ($rate_status->exists()) {
                $amount = $shipment->amount;

                if ($account_type_id == 1) {
                    $cash_handling_charge = CashHandlingCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('range_up', '<=', $amount)->where('range_down', '>=', $amount);
                }
                else {
                    $cash_handling_charge = CorporateCashHandlingCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('range_up', '<=', $amount)->where('range_down', '>=', $amount);
                }

                if ($cash_handling_charge->exists()) {
                    $cash_handling_charge = $cash_handling_charge->first();

                    $today = Carbon::today();

                    if ($account_type_id == 1) {
                        $discount_charge = DiscountCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
                    }
                    else {
                        $discount_charge = CorporateDiscountCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
                    }

                    if ($discount_charge->exists()) {
                        $discount_charge = $discount_charge->first();

                        $discount = $discount_charge->cash;
                    }
                    else {
                        $discount = 0;
                    }

                    $charges = $cash_handling_charge->charges;

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
                        $shipment->cash_handling_charges = ROUND($charges, 0, PHP_ROUND_HALF_DOWN);
                    }
                    else {
                        $shipment->cash_handling_charges = ROUND(($charges - $discount), 0, PHP_ROUND_HALF_DOWN);
                    }

                    $shipment->save();
                }
            }
        }
        else {
            $shipment->cash_handling_charges = 0;

            $shipment->save();
        }
    }

    static public function insurance($id) {
        $shipment = Shipment::find($id);

        $account_type_id = $shipment->user->account_type_id;

        if ($shipment->amount != 0) {
            if ($account_type_id == 1) {
                $rate_status = RateStatus::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('insurance_charges', 1)->where('status', 1);
            }
            else {
                $rate_status = CorporateRateStatus::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('insurance_charges', 1)->where('status', 1);
            }

            if ($rate_status->exists()) {
                $charges = 0;

                foreach ($shipment->items as $item) {
                    $price = $item->price;

                    if ($item->insurance == 1) {
                        if ($account_type_id == 1) {
                            $insurance_charge = InsuranceCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('range_up', '<=', $price)->where('range_down', '>=', $price);
                        }
                        else {
                            $insurance_charge = CorporateInsuranceCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('range_up', '<=', $price)->where('range_down', '>=', $price);
                        }

                        if ($insurance_charge->exists()) {
                            $insurance_charge = $insurance_charge->first();

                            $today = Carbon::today();

                            if ($account_type_id == 1) {
                                $discount_charge = DiscountCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
                            }
                            else {
                                $discount_charge = CorporateDiscountCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
                            }

                            if ($discount_charge->exists()) {
                                $discount_charge = $discount_charge->first();

                                $discount = $discount_charge->insurance;
                            }
                            else {
                                $discount = 0;
                            }

                            $item_charges = $insurance_charge->charges;

                            if (strpos($item_charges, '%') !== FALSE) {
                                $charges += (floatval(str_replace('%', '', $item_charges)) / 100) * $price;
                            }
                            else {
                                $charges += floatval($item_charges);
                            }
                        }
                    }
                }

                if ($charges != 0) {
                    if (strpos($discount, '%') !== FALSE) {
                        $discount = (floatval(str_replace('%', '', $discount)) / 100) * $charges;
                    }
                    else {
                        $discount = floatval($discount);
                    }

                    if ($charges < $discount) {
                        $shipment->insurance_charges = ROUND($charges, 0, PHP_ROUND_HALF_DOWN);
                    }
                    else {
                        $shipment->insurance_charges = ROUND(($charges - $discount), 0, PHP_ROUND_HALF_DOWN);
                    }

                    $shipment->save();
                }
            }
        }
    }

    static public function return($id) {
        $shipment = Shipment::find($id);

        $account_type_id = $shipment->user->account_type_id;

        if ($account_type_id == 1) {
            $rate_status = RateStatus::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('return_charges', 1)->where('status', 1);
        }
        else {
            $rate_status = CorporateRateStatus::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('return_charges', 1)->where('status', 1);
        }

        if ($rate_status->exists()) {
            if ($account_type_id == 1) {
                $return_charge = ReturnCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id);
            }
            else {
                $return_charge = CorporateReturnCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id);
            }

            $today = Carbon::today();

            if ($account_type_id == 1) {
                $discount_charge = DiscountCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
            }
            else {
                $discount_charge = CorporateDiscountCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
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

                        $zone_class_city = ZoneClassCity::where('zone_id', $shipment->pickup_address->city->zone_id)->where('city_id', $shipment->consignee_city_id);

                        if ($shipment->shipping_mode_id == 2 || $shipment->shipping_mode_id == 3) {
                            $zone_class_city = $zone_class_city->where('zone_classification_id', 2);
                        }
                        else {
                            $zone_class_city = $zone_class_city->where('zone_classification_id', 1);
                        }

                        if ($zone_class_city) {
                            $zone_class_city = $zone_class_city->first();

                            $class = $zone_class_city->class;
                        }
                    }
                }

                if ($type_of_charges == 0) {
                    $charges = $return_charge->local;
                }
                else {
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

                if (strpos($discount, '%') !== FALSE) {
                    $discount = (floatval(str_replace('%', '', $discount)) / 100) * $charges;
                }
                else {
                    $discount = floatval($discount);
                }

                if ($charges < $discount) {
                    $shipment->return_charges = ROUND($charges, 0, PHP_ROUND_HALF_DOWN);
                }
                else {
                    $shipment->return_charges = ROUND(($charges - $discount), 0, PHP_ROUND_HALF_DOWN);
                }

                $shipment->save();
            }
        }
    }

    static public function fuel_surcharge($id) {
        $shipment = Shipment::find($id);

        $account_type_id = $shipment->user->account_type_id;

        if ($account_type_id == 1) {
            $rate_status = RateStatus::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('fuel_charges', 1)->where('status', 1);
        }
        else {
            $rate_status = CorporateRateStatus::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('fuel_charges', 1)->where('status', 1);
        }

        if ($rate_status->exists()) {
            if ($account_type_id == 1) {
                $fuel_charge = FuelSurcharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id);
            }
            else {
                $fuel_charge = CorporateFuelSurcharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id);
            }

            if ($fuel_charge->exists()) {
                $fuel_charge = $fuel_charge->first();

                $shipment->fuel_surcharge = ROUND((($fuel_charge->fuel_surcharge / 100) * $shipment->weight_charges), 0, PHP_ROUND_HALF_DOWN);

                $shipment->save();
            }
        }
    }

    static public function replacement($id) {
        $shipment = Shipment::find($id);

        $account_type_id = $shipment->user->account_type_id;

        if ($account_type_id == 1) {
            $booking_type_charge = BookingTypeCharges::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id);
        }
        else {
            $booking_type_charge = CorporateBookingTypeCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id);
        }

        if ($booking_type_charge->exists()) {
            $booking_type_charge = $booking_type_charge->first();

            $replacement_multiplier = ($booking_type_charge->replacement_charges / 100);

            $weight = ($shipment->replacement_weight) ? $shipment->replacement_weight : 0.15;

            if ($account_type_id == 1) {
                $weight_charge = WeightCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('range_up', '<=', $weight)->where('range_down', '>=', $weight);
            }
            else {
                $weight_charge = CorporateWeightCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('delivery_type_id', $shipment->walk_in_delivery_type_id)->where('range_up', '<=', $weight)->where('range_down', '>=', $weight);
            }

            if ($weight_charge->exists()) {
                $weight_charge = $weight_charge->first();

                $today = Carbon::today();

                if ($account_type_id == 1) {
                    $discount_charge = DiscountCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
                }
                else {
                    $discount_charge = CorporateDiscountCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
                }

                if ($discount_charge->exists()) {
                    $discount_charge = $discount_charge->first();

                    $discount = $discount_charge->weight;
                }
                else {
                    $discount = 0;
                }

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

                        if ($zone_class_city) {
                            $zone_class_city = $zone_class_city->first();

                            $class = $zone_class_city->class;
                        }
                    }
                }

                if ($weight_charge->weight_addition == 0 || $account_type_id == 2) {
                    if ($type_of_charges == 0) {
                        $charges = $weight_charge->local_or_6hr;
                    }
                    else {
                        if ($class == 1) {
                            if (strpos($weight_charge->national_charges_class_1, '%') !== FALSE) {
                                $charges = (floatval(str_replace('%', '', $weight_charge->national_charges_class_1)) / 100) * $weight_charge->national_charges_class_0;
                            }
                            else {
                                $charges = intval($weight_charge->national_charges_class_1);
                            }
                        }
                        else if ($class == 2) {
                            if (strpos($weight_charge->national_charges_class_2, '%') !== FALSE) {
                                $charges = (floatval(str_replace('%', '', $weight_charge->national_charges_class_2)) / 100) * $weight_charge->national_charges_class_0;
                            }
                            else {
                                $charges = intval($weight_charge->national_charges_class_2);
                            }
                        }
                        else if ($class == 3) {
                            if (strpos($weight_charge->national_charges_class_3, '%') !== FALSE) {
                                $charges = (floatval(str_replace('%', '', $weight_charge->national_charges_class_3)) / 100) * $weight_charge->national_charges_class_0;
                            }
                            else {
                                $charges = intval($weight_charge->national_charges_class_3);
                            }
                        }
                        else {
                            $charges = $weight_charge->national_charges_class_0;
                        }
                    }

                    if ($account_type_id == 2) {
                        $charges = $charges * ROUND($shipment->actual_weight, 0);
                    }

                    $charges = ($charges * $replacement_multiplier);

                    if ($charges < $discount) {
                        $shipment->replacement_charges = ROUND($charges, 0, PHP_ROUND_HALF_DOWN);
                    }
                    else {
                        $shipment->replacement_charges = ROUND(($charges - $discount), 0, PHP_ROUND_HALF_DOWN);
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
                                $charges = ((floatval(str_replace('%', '', $weight_charge->national_charges_class_1)) / 100) * $weight_charge->national_charges_class_0) * $multiplier;
                            }
                            else {
                                $charges = intval($weight_charge->national_charges_class_1) * $multiplier;
                            }
                        }
                        else if ($class == 2) {
                            if (strpos($weight_charge->national_charges_class_2, '%') !== FALSE) {
                                $charges = ((floatval(str_replace('%', '', $weight_charge->national_charges_class_2)) / 100) * $weight_charge->national_charges_class_0) * $multiplier;
                            }
                            else {
                                $charges = intval($weight_charge->national_charges_class_2) * $multiplier;
                            }
                        }
                        else if ($class == 3) {
                            if (strpos($weight_charge->national_charges_class_3, '%') !== FALSE) {
                                $charges = ((floatval(str_replace('%', '', $weight_charge->national_charges_class_3)) / 100) * $weight_charge->national_charges_class_0) * $multiplier;
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
                        $weight_charge = WeightCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('id', '<', $weight_charge->id)->orderBy('id', 'desc');

                        if ($weight_charge->exists()) {
                            $weight_charge = $weight_charge->first();

                            if ($weight_charge->weight_addition == 0) {
                                if ($type_of_charges == 0) {
                                    $charges += $weight_charge->local_or_6hr;
                                }
                                else {
                                    if ($class == 1) {
                                        if (strpos($weight_charge->national_charges_class_1, '%') !== FALSE) {
                                            $charges += (floatval(str_replace('%', '', $weight_charge->national_charges_class_1)) / 100) * $weight_charge->national_charges_class_0;
                                        }
                                        else {
                                            $charges += intval($weight_charge->national_charges_class_1);
                                        }
                                    }
                                    else if ($class == 2) {
                                        if (strpos($weight_charge->national_charges_class_2, '%') !== FALSE) {
                                            $charges += (floatval(str_replace('%', '', $weight_charge->national_charges_class_2)) / 100) * $weight_charge->national_charges_class_0;
                                        }
                                        else {
                                            $charges += intval($weight_charge->national_charges_class_2);
                                        }
                                    }
                                    else if ($class == 3) {
                                        if (strpos($weight_charge->national_charges_class_3, '%') !== FALSE) {
                                            $charges += (floatval(str_replace('%', '', $weight_charge->national_charges_class_3)) / 100) * $weight_charge->national_charges_class_0;
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
                                            $charges += ((floatval(str_replace('%', '', $weight_charge->national_charges_class_1)) / 100) * $weight_charge->national_charges_class_0) * $multiplier;
                                        }
                                        else {
                                            $charges += intval($weight_charge->national_charges_class_1) * $multiplier;
                                        }
                                    }
                                    else if ($class == 2) {
                                        if (strpos($weight_charge->national_charges_class_2, '%') !== FALSE) {
                                            $charges += ((floatval(str_replace('%', '', $weight_charge->national_charges_class_2)) / 100) * $weight_charge->national_charges_class_0) * $multiplier;
                                        }
                                        else {
                                            $charges += intval($weight_charge->national_charges_class_2) * $multiplier;
                                        }
                                    }
                                    else if ($class == 3) {
                                        if (strpos($weight_charge->national_charges_class_3, '%') !== FALSE) {
                                            $charges += ((floatval(str_replace('%', '', $weight_charge->national_charges_class_3)) / 100) * $weight_charge->national_charges_class_0) * $multiplier;
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
                        $shipment->replacement_charges = ROUND($charges, 0, PHP_ROUND_HALF_DOWN);
                    }
                    else {
                        $shipment->replacement_charges = ROUND(($charges - $discount), 0, PHP_ROUND_HALF_DOWN);
                    }

                    $shipment->save();
                }
            }
        }
    }

    static public function try_and_buy($id) {
        $shipment = Shipment::find($id);

        $account_type_id = $shipment->user->account_type_id;

        if ($account_type_id == 1) {
            $booking_type_charge = BookingTypeCharges::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id);
        }
        else {
            $booking_type_charge = CorporateBookingTypeCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id);
        }

        if ($booking_type_charge->exists()) {
            $booking_type_charge = $booking_type_charge->first();

            $today = Carbon::today();

            if ($account_type_id == 1) {
                $discount_charge = DiscountCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
            }
            else {
                $discount_charge = CorporateDiscountCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
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
                $shipment->try_and_buy_charges = ROUND($charges, 0, PHP_ROUND_HALF_DOWN);
            }
            else {
                $shipment->try_and_buy_charges = ROUND(($charges - $discount), 0, PHP_ROUND_HALF_DOWN);
            }

            $shipment->save();
        }
    }

    static public function packaging_material($id, $type, $charges) {
        $shipment = Shipment::find($id);

        $account_type_id = $shipment->user->account_type_id;

        $today = Carbon::today();

        if ($account_type_id == 1) {
            $discount_charge = DiscountCharge::where('user_id', $shipment->user_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
        }
        else {
            $discount_charge = CorporateDiscountCharge::where('user_id', $shipment->user_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
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
            $charges = ROUND($charges, 0, PHP_ROUND_HALF_DOWN);
        }
        else {
            $charges = ROUND(($charges - $discount), 0, PHP_ROUND_HALF_DOWN);
        }

        if ($type == 1) {
            $shipment->amount = $charges;
        }

        $shipment->packaging_material_charges = $charges;

        $shipment->save();
    }

    static public function walk_in_return($id) {
        $shipment = Shipment::find($id);

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

                if ($zone_class_city) {
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

        $charges = ROUND(($shipment->weight_charges * ($percentage / 100)), 0, PHP_ROUND_HALF_DOWN);

        $shipment->return_charges = $charges;

        $shipment->amount = $shipment->amount + $charges;

        $shipment->received_amount = $shipment->amount + $charges;

        $shipment->save();
    }

    static public function intercept($id, $previous_consignee_city_id, $new_consignee_city_id) {
        $shipment = Shipment::find($id);

        $account_type_id = $shipment->user->account_type_id;

        if ($account_type_id == 1) {
            $rate_status = RateStatus::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('status', 1);
        }
        else {
            $rate_status = CorporateRateStatus::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('status', 1);
        }

        if ($rate_status->exists()) {
            $weight = $shipment->actual_weight;

            if ($account_type_id == 1) {
                $weight_charge = WeightCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('range_up', '<=', $weight)->where('range_down', '>=', $weight);
            }
            else {
                $weight_charge = CorporateWeightCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('delivery_type_id', $shipment->walk_in_delivery_type_id)->where('range_up', '<=', $weight)->where('range_down', '>=', $weight);
            }

            if ($weight_charge->exists()) {
                $weight_charge = $weight_charge->first();

                $today = Carbon::today();

                if ($account_type_id == 1) {
                    $discount_charge = DiscountCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
                }
                else {
                    $discount_charge = CorporateDiscountCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
                }

                if ($discount_charge->exists()) {
                    $discount_charge = $discount_charge->first();

                    $discount = $discount_charge->weight;
                }
                else {
                    $discount = 0;
                }

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
                    if ($previous_consignee_city_id == $new_consignee_city_id) {
                        $type_of_charges = 0;
                    }
                    else {
                        $type_of_charges = 1;

                        $previous_consignee_city = City::find($previous_consignee_city_id);

                        $zone_class_city = ZoneClassCity::where('zone_id', $previous_consignee_city->zone_id)->where('city_id', $new_consignee_city_id);

                        if ($shipment->shipping_mode_id == 2 || $shipment->shipping_mode_id == 3) {
                            $zone_class_city = $zone_class_city->where('zone_classification_id', 2);
                        }
                        else {
                            $zone_class_city = $zone_class_city->where('zone_classification_id', 1);
                        }

                        if ($zone_class_city) {
                            $zone_class_city = $zone_class_city->first();

                            $class = $zone_class_city->class;
                        }
                    }
                }

                if ($weight_charge->weight_addition == 0 || $account_type_id == 2) {
                    if ($type_of_charges == 0) {
                        $charges = $weight_charge->local_or_6hr;
                    }
                    else {
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

                    if ($account_type_id == 2) {
                        $charges = $charges * ROUND($shipment->actual_weight, 0);
                    }

                    if ($charges < $discount) {
                        $shipment->intercept_charges = ROUND($charges, 0, PHP_ROUND_HALF_DOWN);
                    }
                    else {
                        $shipment->intercept_charges = ROUND(($charges - $discount), 0, PHP_ROUND_HALF_DOWN);
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
                        $weight_charge = WeightCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('id', '<', $weight_charge->id)->orderBy('id', 'desc');

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
                        $shipment->intercept_charges = ROUND($charges, 0, PHP_ROUND_HALF_DOWN);
                    }
                    else {
                        $shipment->intercept_charges = ROUND(($charges - $discount), 0, PHP_ROUND_HALF_DOWN);
                    }

                    $shipment->save();
                }
            }
        }
    }
}
