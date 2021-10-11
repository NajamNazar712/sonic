<?php

namespace App\Http\Models\Admin\Retail;

use Illuminate\Database\Eloquent\Model;

class RetailFranchise extends Model
{
    public function pickup_address() {
        return $this->belongsTo('App\Http\Models\Shipper\UserShippingInfo', 'pickup_address_id', 'id');
    }
}
