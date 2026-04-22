<?php

namespace App\Http\Models;

use App\Http\Models\Admin\GlobalSettings;
use Illuminate\Database\Eloquent\Model;
use App\Models\NegativePayableAllowShipperZeroCod;


class PendingPayment extends Model
{
	public function pending_payment_shipments() {
		return $this->hasMany('App\Http\Models\PendingPaymentShipment');
	}
    public function pending_payment_shipments_arrival() {
		return $this->hasMany('App\Http\Models\PendingPaymentShipment','pending_payment_id','id')->where('type',3);
	}

    public function pending_payment_calculation(){
        return $this->hasOne('App\Http\Models\PendingPaymentCalculation');
    }

	public function shipper() {
		return $this->belongsTo('App\Http\Models\Shipper\User', 'user_id', 'id');
	}

    static function check_negative_payable($user_id,$account_type){
        if($account_type != 2) {
            $check_payable_setting = GlobalSettings::where('type', 'negative_payable_limit')->first();
            if (!empty($check_payable_setting)) {
                $setting_value = $check_payable_setting->setting_value;
                $check = self::whereHas('pending_payment_calculation', function ($query) use ($setting_value) {
                    $query->where('payable', '<', $setting_value);
                })->where('user_id', $user_id);

                if ($check->exists()) {
                    if (NegativePayableAllowShipperZeroCod::isAllowed($user_id)) {
                        return true;
                    }
                    return false;
                } else {
                    return true;
                }

            } else {
                return true;
            }
        }else{
             return true;
        }
    }

    static function negative_payable_check($user_id,$account_type){
        if ($account_type == 2) return false;

        $setting = GlobalSettings::where('type', 'negative_payable_limit')->first();
        if (!$setting) return false;

        $limit = $setting->setting_value;

        return self::where('user_id', $user_id)
            ->whereHas('pending_payment_calculation', function ($q) use ($limit) {
                $q->where('payable', '<', $limit);
            })
            ->exists();
        
    }


    public static function current_payable_value(int $user_id): ?float
    {
        $pending = self::query()
            ->where('user_id', $user_id)
            ->whereHas('pending_payment_calculation')
            ->with(['pending_payment_calculation' => function ($q) {
                $q->select('*');
            }])
            ->latest('id')
            ->first();

        if (!$pending || !$pending->pending_payment_calculation) {
            return null;
        }

        return (float) $pending->pending_payment_calculation->payable;
    }

    /**
     * NEW RULE (Bulk/Excel):
     * - If payable is below configured negative_payable_limit, allow ONLY when:
     *      total_cod >= abs(current_payable)
     * - Keep NegativePayableAllowShipperZeroCod override.
     */
    public static function check_negative_payable_cod(int $user_id, int $account_type, float $total_cod = 0): bool
    {
        // If the account type is 2, allow booking immediately
        if ($account_type == 2) {
            return true;
        }

        // Additional override permission (zero COD allowed in some cases)
        if (NegativePayableAllowShipperZeroCod::isAllowed($user_id)) {
            return true;
        }

        // Get the current payable value
        $payable = self::current_payable_value($user_id);

        // If there's no payable value (null), allow booking
        if ($payable === null) {
            return true;
        }

        // If the payable is positive or zero, allow booking immediately
        if ((float) $payable >= 0) {
            return true;
        }

        // If the payable is negative, calculate the required COD to cover it
        $required = abs((float) $payable);

        // Check if the COD amount is enough to cover the negative payable
        return (float) $total_cod >= (float) $required;
    }
}
