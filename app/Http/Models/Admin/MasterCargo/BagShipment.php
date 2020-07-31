<?php

namespace App\Http\Models\Admin\MasterCargo;

use Illuminate\Database\Eloquent\Model;

class BagShipment extends Model
{
    public function shipment() {
        return $this->belongsTo('App\Http\Models\Shipment');
    }

    public function bag() {
        return $this->belongsTo('App\Http\Models\Admin\MasterCargo\Bag', 'bag_id', 'id');
    }
}
