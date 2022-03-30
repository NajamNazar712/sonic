<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class DonePayment extends Model
{
	public function done_payment_shipments() {
		return $this->hasMany('App\Http\Models\DonePaymentShipment');
	}


    public function shipper() {
        return $this->belongsTo('App\Http\Models\Shipper\User', 'user_id', 'id');
    }

	public function VisionSoftCodPaymentClear() {

        return $this->belongsTo('App\Http\Models\Admin\VisionSoft\VisionSoftCodPaymentClear', 'id', 'payment_id')
            ->where('status',1)
            ->orderBy('id','desc');
	}

    public function done_payment_calculation(){
        return $this->hasOne('App\Http\Models\DonePaymentCalculation');
    }

	public function shipper_bank() {
		return $this->belongsTo('App\Http\Models\Shipper\UserBankInfo', 'user_bank_info_id', 'id');
	}

	public function company_bank() {
		return $this->belongsTo('App\Http\Models\BanksList', 'company_bank_id', 'id');
	}
    public function payment_status() {
        return $this->belongsTo('App\Http\Models\ShipmentPaymentStatus', 'status', 'id');
    }
}
