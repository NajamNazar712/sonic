<?php

use App\Http\Models\CorporateDefaultFuelSurcharge;
use App\Http\Models\CorporateDefaultHistoryFuelSurcharge;
use App\Http\Models\CorporateDefaultRateStatus;
use App\Http\Models\CorporateFuelSurcharge;
use App\Http\Models\CorporateRateStatus;
use App\Http\Models\FuelSurcharge;
use App\Http\Models\Rates\HistoryCorporateFuelSurcharge;
use App\Http\Models\Rates\HistoryFuelSurcharge;
use App\Http\Models\RateStatus;
use App\Http\Models\Shipper\User;
use App\Http\Models\ShippingMode;
use Illuminate\Database\Seeder;

class UpdateFSC16022023Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $include_ids = [];

        $fuel_factor_user_ids = [];

        $shipping_modes_user_ids = [];

        foreach ($include_ids as $index => $user_id) {
            $user = User::find($user_id);
            if($user){
                if ($user->account_type_id == 1) {
                    $rate_status = RateStatus::where('user_id', $user->id)->where('shipping_mode_id', $shipping_modes_user_ids[$index]);
                } else {
                    if ($user->corporate_rate_type_id != 3 && $user->new_rate_type_id == null) {
                        $rate_status = CorporateRateStatus::where('user_id', $user->id)->where('shipping_mode_id', $shipping_modes_user_ids[$index]);
                    } else {
                        $rate_status = CorporateDefaultRateStatus::where('user_id', $user->id)->where('shipping_mode_id', $shipping_modes_user_ids[$index]);
                    }
                }

                if ($rate_status->exists()) {
                    $rate_status = $rate_status->first();

                    if ($user->account_type_id == 1) {
                        $fuel_surcharge_history = new HistoryFuelSurcharge();
                    } else {
                        if ($user->corporate_rate_type_id != 3 && $user->new_rate_type_id == null) {
                            $fuel_surcharge_history = new HistoryCorporateFuelSurcharge();
                        } else {
                            $fuel_surcharge_history = new CorporateDefaultHistoryFuelSurcharge();
                        }
                    }

                    if ($user->account_type_id == 1) {
                        $fuel_surcharge = FuelSurcharge::where('user_id', $user->id)->where('shipping_mode_id', $shipping_modes_user_ids[$index]);
                    } else {
                        if ($user->corporate_rate_type_id != 3 && $user->new_rate_type_id == null) {
                            $fuel_surcharge = CorporateFuelSurcharge::where('user_id', $user->id)->where('shipping_mode_id', $shipping_modes_user_ids[$index]);
                        } else {
                            $fuel_surcharge = CorporateDefaultFuelSurcharge::where('user_id', $user->id)->where('shipping_mode_id', $shipping_modes_user_ids[$index]);
                        }
                    }
                    if ($fuel_surcharge->exists()) {
                        $fuel_surcharge = $fuel_surcharge->first();


                        $fuel_surcharge_history->user_id = $user->id;
                        $fuel_surcharge_history->shipping_mode_id = $rate_status->shipping_mode_id;
                        $fuel_surcharge_history->fuel_surcharge = $fuel_surcharge->fuel_surcharge;
                        $fuel_surcharge_history->save();

                        if ($rate_status->fuel_charges == 1) {
                            $update_fuel_surcharge = $fuel_factor_user_ids[$index];
                        } else {
                            $update_fuel_surcharge =  $fuel_factor_user_ids[$index];

                            $rate_status->fuel_charges = 1;
                            $rate_status->save();
                        }

                        if ($update_fuel_surcharge >= 0) {
                            $fuel_surcharge->fuel_surcharge = $update_fuel_surcharge;
                        } else {
                            $fuel_surcharge->fuel_surcharge = 0;
                        }
                        $fuel_surcharge->save();

                    } else {
                        if ($fuel_factor_user_ids[$index] < 0) {
                            $fuel_factor_user_ids[$index] = 0;
                        }

                        $rate_status->fuel_charges = 1;
                        $rate_status->save();
                        if ($user->account_type_id == 1) {
                            $fuel_surcharge = new FuelSurcharge();
                        } else {
                            if ($user->corporate_rate_type_id != 3 && $user->new_rate_type_id == null) {
                                $fuel_surcharge = new CorporateFuelSurcharge();
                            } else {
                                $fuel_surcharge = new CorporateDefaultFuelSurcharge();
                            }
                        }
                        $fuel_surcharge->user_id = $user->id;
                        $fuel_surcharge->shipping_mode_id = $shipping_modes_user_ids[$index];
                        $fuel_surcharge->fuel_surcharge = $fuel_factor_user_ids[$index];
                        $fuel_surcharge->save();
                    }
                }
            }
        }
    }
}
