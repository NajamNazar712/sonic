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

use Carbon\Carbon;

class ShipmentChargesController extends Controller
{
    static public function weight($id) {
        $shipment = Shipment::find($id);

        $rate_status = RateStatus::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('status', 1);

        if ($rate_status->exists()) {
            $weight = $shipment->actual_weight;

            $weight_charge = WeightCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('range_up', '<=', $weight)->where('range_down', '>=', $weight);

            if ($weight_charge->exists()) {
                $weight_charge = $weight_charge->first();

                $today = Carbon::today();

                $discount_charge = DiscountCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);

                if ($discount_charge->exists()) {
                    $discount_charge = $discount_charge->first();

                    $discount = $discount_charge->weight;
                }
                else {
                    $discount = 0;
                }

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
                    }
                }

                if ($weight_charge->weight_addition == 0) {
                    if ($type_of_charges == 0) {
                        $charges = $weight_charge->local_or_6hr;
                    }
                    else {
                        $charges = $weight_charge->national_or_sameday;
                    }

                    if ($charges < $discount) {
                        $shipment->weight_charges = $charges;
                    }
                    else {
                        $shipment->weight_charges = $charges - $discount;
                    }

                    $shipment->save();
                }
                else {
                    $multiplier = (intval($weight - $weight_charge->range_up) / $weight_charge->spkg) + 1;

                    if ($type_of_charges == 0) {
                        $charges = ($weight_charge->local_or_6hr * $multiplier);
                    }
                    else {
                        $charges = ($weight_charge->national_or_sameday * $multiplier);
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
                                    $charges += $weight_charge->national_or_sameday;
                                }

                                $previous = FALSE;
                            }
                            else {
                                $multiplier = (intval($weight - $weight_charge->range_up) / $weight_charge->spkg) + 1;

                                if ($type_of_charges == 0) {
                                    $charges += ($weight_charge->local_or_6hr * $multiplier);
                                }
                                else {
                                    $charges += ($weight_charge->national_or_sameday * $multiplier);
                                }
                            }
                        }
                        else {
                            $previous = FALSE;
                        }
                    }

                    if ($charges < $discount) {
                        $shipment->weight_charges = $charges;
                    }
                    else {
                        $shipment->weight_charges = $charges - $discount;
                    }

                    $shipment->save();
                }
            }
        }
    }

    static public function cash_handling($id) {
        $shipment = Shipment::find($id);

        if ($shipment->amount != 0) {
            $rate_status = RateStatus::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('cash_handling_charges', 1)->where('status', 1);

            if ($rate_status->exists()) {
                $amount = $shipment->amount;

                $cash_handling_charge = CashHandlingCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('range_up', '<=', $amount)->where('range_down', '>=', $amount);

                if ($cash_handling_charge->exists()) {
                    $cash_handling_charge = $cash_handling_charge->first();

                    $today = Carbon::today();

                    $discount_charge = DiscountCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);

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

                    if ($charges < $discount) {
                        $shipment->cash_handling_charges = $charges;
                    }
                    else {
                        $shipment->cash_handling_charges = $charges - $discount;
                    }

                    $shipment->save();
                }
            }
        }
    }

    static public function insurance($id) {
        $shipment = Shipment::find($id);

        if ($shipment->amount != 0) {
            $rate_status = RateStatus::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('insurance_charges', 1)->where('status', 1);

            if ($rate_status->exists()) {
                $charges = 0;

                foreach ($shipment->items as $item) {
                    $price = $item->price;

                    if ($item->insurance == 1) {
                        $insurance_charge = InsuranceCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('range_up', '<=', $price)->where('range_down', '>=', $price);

                        if ($insurance_charge->exists()) {
                            $insurance_charge = $insurance_charge->first();

                            $today = Carbon::today();

                            $discount_charge = DiscountCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);

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
                    if ($charges < $discount) {
                        $shipment->insurance_charges = $charges;
                    }
                    else {
                        $shipment->insurance_charges = $charges - $discount;
                    }

                    $shipment->save();
                }
            }
        }
    }

    static public function return($id) {
        $shipment = Shipment::find($id);

        $rate_status = RateStatus::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('return_charges', 1)->where('status', 1);

        if ($rate_status->exists()) {
            $return_charge = ReturnCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id);

            $today = Carbon::today();

            $discount_charge = DiscountCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);

            if ($discount_charge->exists()) {
                $discount_charge = $discount_charge->first();

                $discount = $discount_charge->return;
            }
            else {
                $discount = 0;
            }

            if ($return_charge->exists()) {
                $return_charge = $return_charge->first();

                if ($shipment->pickup_address->city_id == $shipment->consignee_city_id) {
                    $charges = $return_charge->local;
                }
                else {
                    $charges = $return_charge->national;
                }

                if ($charges < $discount) {
                    $shipment->return_charges = $charges;
                }
                else {
                    $shipment->return_charges = $charges - $discount;
                }

                $shipment->save();
            }
        }
    }

    static public function fuel_surcharge($id) {
        $shipment = Shipment::find($id);

        $rate_status = RateStatus::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('fuel_charges', 1)->where('status', 1);

        if ($rate_status->exists()) {
            $fuel_charge = FuelSurcharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id);

            if ($fuel_charge->exists()) {
                $fuel_charge = $fuel_charge->first();

                $shipment->fuel_surcharge = ($fuel_charge->fuel_surcharge / 100) * $shipment->weight_charges;

                $shipment->save();
            }
        }
    }

    static public function replacement($id) {
        $shipment = Shipment::find($id);

        $booking_type_charge = BookingTypeCharges::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id);

        if ($booking_type_charge->exists()) {
            $booking_type_charge = $booking_type_charge->first();

            $replacement_multiplier = ($booking_type_charge->replacement_charges / 100);

            $weight = ($shipment->replacement_weight) ? $shipment->replacement_weight : 0.15;

            $weight_charge = WeightCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->where('range_up', '<=', $weight)->where('range_down', '>=', $weight);

            if ($weight_charge->exists()) {
                $weight_charge = $weight_charge->first();

                $today = Carbon::today();

                $discount_charge = DiscountCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);

                if ($discount_charge->exists()) {
                    $discount_charge = $discount_charge->first();

                    $discount = $discount_charge->weight;
                }
                else {
                    $discount = 0;
                }

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
                    }
                }

                if ($weight_charge->weight_addition == 0) {
                    if ($type_of_charges == 0) {
                        $charges = $weight_charge->local_or_6hr;
                    }
                    else {
                        $charges = $weight_charge->national_or_sameday;
                    }

                    if ($charges < $discount) {
                        $shipment->replacement_charges = ($charges * $replacement_multiplier);
                    }
                    else {
                        $shipment->replacement_charges = ($charges * $replacement_multiplier) - $discount;
                    }

                    $shipment->save();
                }
                else {
                    $multiplier = (intval($weight - $weight_charge->range_up) / $weight_charge->spkg) + 1;

                    if ($type_of_charges == 0) {
                        $charges = ($weight_charge->local_or_6hr * $multiplier);
                    }
                    else {
                        $charges = ($weight_charge->national_or_sameday * $multiplier);
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
                                    $charges += $weight_charge->national_or_sameday;
                                }

                                $previous = FALSE;
                            }
                            else {
                                $multiplier = (intval($weight - $weight_charge->range_up) / $weight_charge->spkg) + 1;

                                if ($type_of_charges == 0) {
                                    $charges += ($weight_charge->local_or_6hr * $multiplier);
                                }
                                else {
                                    $charges += ($weight_charge->national_or_sameday * $multiplier);
                                }
                            }
                        }
                        else {
                            $previous = FALSE;
                        }
                    }

                    if ($charges < $discount) {
                        $shipment->replacement_charges = ($charges * $replacement_multiplier);
                    }
                    else {
                        $shipment->replacement_charges = ($charges * $replacement_multiplier) - $discount;
                    }

                    $shipment->save();
                }
            }
        }
    }

    static public function try_and_buy($id) {
        $shipment = Shipment::find($id);

        $booking_type_charge = BookingTypeCharges::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id);

        if ($booking_type_charge->exists()) {
            $booking_type_charge = $booking_type_charge->first();

            $today = Carbon::today();

            $discount_charge = DiscountCharge::where('user_id', $shipment->user_id)->where('shipping_mode_id', $shipment->shipping_mode_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);

            if ($discount_charge->exists()) {
                $discount_charge = $discount_charge->first();

                $discount = $discount_charge->weight;
            }
            else {
                $discount = 0;
            }

            $try_and_buy_multiplier = ($booking_type_charge->try_and_buy_charges / 100);

            $charges = ($shipment->weight_charges * $try_and_buy_multiplier);

            if ($charges < $discount) {
                $shipment->try_and_buy_charges = $charges;
            }
            else {
                $shipment->try_and_buy_charges = $charges - $discount;
            }

            $shipment->save();
        }
    }
}