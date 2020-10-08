<?php

namespace App\Http\Models\WMS;

use Illuminate\Database\Eloquent\Model;

class WmsInvoice extends Model
{
    public function shipper() {
        return $this->belongsTo('App\Http\Models\Customer\User', 'user_id', 'id');
    }
}
