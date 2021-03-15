<?php

namespace App\http\Models\Admin\Retail;

use Illuminate\Database\Eloquent\Model;

class RetailCashDeposit extends Model
{

    public function user() {
        return $this->belongsTo('App\Http\Models\Admin\Retail\RetailUser', 'retail_user_id', 'id');
    }

    public function shipments() {
        return $this->hasMany('App\Http\Models\Admin\Retail\RetailCashDepositShipment', 'cash_deposit_id', 'id');
    }
}
