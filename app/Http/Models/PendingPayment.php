<?php

namespace App\Http\Models;

use App\Http\Models\Admin\GlobalSettings;
use Illuminate\Database\Eloquent\Model;

class PendingPayment extends Model
{
	public function pending_payment_shipments() {
		return $this->hasMany('App\Http\Models\PendingPaymentShipment');
	}

    public function pending_payment_calculation(){
        return $this->hasOne('App\Http\Models\PendingPaymentCalculation');
    }

	public function shipper() {
		return $this->belongsTo('App\Http\Models\Shipper\User', 'user_id', 'id');
	}

    static function check_negative_payable($user_id){
        $check_payable_setting = GlobalSettings::where('type', 'negative_payable_limit')->first();
            if(!empty($check_payable_setting)) {
                $setting_value = $check_payable_setting->setting_value;
                $check = self::whereHas('pending_payment_calculation', function ($query) use ($setting_value) {
                    $query->where('payable', '<', $setting_value);
                })->where('user_id', $user_id);

            if($check->exists()) {
                return false;
            }else{
                return true;
            }

        }else{
            return true;
        }
    }
}
