<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\City;
use App\Http\Models\International\Wholesale\WholesaleShipment;
use App\Http\Models\InternationalDhlZone;
use App\Http\Models\InternationalStandardDhlRate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InternationalWholesaleChargesController extends Controller
{
    static public function weight($destination_id, $weight) {

        $zone_id = City::find($destination_id)->zone_id;


        $international_zone = InternationalDhlZone::where('zone_id', $zone_id)->first();
        if ($international_zone) {
            $international_zone_id = $international_zone->zone_name;
        }
        else{
            return false;
        }

        $weight_charge = InternationalStandardDhlRate::where('range_up', '<=', $weight)->where('range_down', '>=', $weight);
        if($weight_charge->exists()){
            $weight_charge = $weight_charge->first();
            $today = Carbon::today();
            $courier_charges = 0;
            if ($weight_charge->weight_addition == 0) {
                $zone_id = 'zone_'.$international_zone_id;
                $charges = $weight_charge[$zone_id];


                $exchange_rate_charges = 0;
                $exchange_rate = GlobalSettings::where('type', 'international_exchange_rate');
                if($exchange_rate->exists()){
                    $exchange_rate = $exchange_rate->first();
                    $exchange_rate_charges = (float)$exchange_rate->text;
                }


                $charges = $charges * $exchange_rate_charges;
                $courier_charges = ROUND($charges, 2, PHP_ROUND_HALF_DOWN);


                return $courier_charges;
            }
            else {
                $multiplier = (intval($weight - $weight_charge->range_up) / $weight_charge->spkg) + 1;
                $zone_id = 'zone_'.$international_zone_id;
                $charges = ($weight_charge[$zone_id] * $multiplier);


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

                $exchange_rate_charges = 0;
                $exchange_rate = GlobalSettings::where('type', 'international_exchange_rate');
                if($exchange_rate->exists()){
                    $exchange_rate = $exchange_rate->first();
                    $exchange_rate_charges = (float)$exchange_rate->text;;
                }


                $charges = $charges * $exchange_rate_charges;
                $courier_charges = ROUND($charges, 2, PHP_ROUND_HALF_DOWN);


                return $courier_charges;
            }

        }
    }
}
