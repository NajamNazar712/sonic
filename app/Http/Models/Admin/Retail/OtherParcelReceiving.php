<?php

namespace App\Http\Models\Admin\Retail;

use Illuminate\Database\Eloquent\Model;

class OtherParcelReceiving extends Model
{
    public function user() {
        return $this->belongsTo('App\Http\Models\Admin\Retail\RetailUser', 'retail_user_id', 'id');
    }

    public function shipments() {
        return $this->hasMany('App\Http\Models\Admin\Retail\OtherParcelReceivingShipment', 'other_parcel_receiving_id', 'id');
    }
}
