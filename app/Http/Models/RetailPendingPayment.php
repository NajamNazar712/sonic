<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class RetailPendingPayment extends Model
{
    public function shipper() {
        return $this->belongsTo('App\Http\Models\Admin\Retail\RetailShipperInfo', 'user_id', 'id');
    }
}
