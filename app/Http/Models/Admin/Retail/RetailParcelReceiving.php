<?php

namespace App\http\Models\Admin\Retail;

use Illuminate\Database\Eloquent\Model;

class RetailParcelReceiving extends Model
{
    public function user() {
        return $this->belongsTo('App\Http\Models\Admin\Retail\RetailUser', 'retail_user_id', 'id');
    }

    public function shipments() {
        return $this->hasMany('App\Http\Models\Admin\Retail\RetailParcelReceivingShipment', 'parcel_receiving_id', 'id');
    }
}
