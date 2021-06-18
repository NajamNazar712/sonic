<?php

namespace App\Http\Models\Admin\Retail;

use Illuminate\Database\Eloquent\Model;

class OtherRetailShipment extends Model
{
    public function shipment() {
        return $this->hasMany('App\Http\Models\Shipment');
    }
}
