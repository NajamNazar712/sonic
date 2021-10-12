<?php

namespace App\Http\Models\Admin\Retail;

use Illuminate\Database\Eloquent\Model;

class RetailTraxCenter extends Model
{
    public function pickup_address() {
        return $this->belongsTo('App\Http\Models\Shipper\UserShippingInfo', 'pickup_address_id', 'id');
    }
}
