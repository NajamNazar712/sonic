<?php

namespace App\Http\Models\Admin\MasterCargo;

use Illuminate\Database\Eloquent\Model;

class Bag extends Model
{
    public function shipment() {
        return $this->hasMany('App\Http\Models\Admin\MasterCargo\BagShipment')->orderBy('shipment_id');
    }

    public function origin_hub() {
        return $this->belongsTo('App\Http\Models\City', 'origin_hub_id', 'id');
    }

    public function destination_hub() {
        return $this->belongsTo('App\Http\Models\City', 'destination_hub_id', 'id');
    }

    public function junction_hub_1() {
        return $this->belongsTo('App\Http\Models\City', 'junction_hub_1_id', 'id');
    }

    public function junction_hub_2() {
        return $this->belongsTo('App\Http\Models\City', 'junction_hub_2_id', 'id');
    }

    public function shipping_mode() {
        return $this->belongsTo('App\Http\Models\ShippingMode');
    }

    public function transport_mode() {
        return $this->belongsTo('App\Http\Models\TransportMode');
    }

    public function transport_mode_vendor() {
        return $this->belongsTo('App\Http\Models\TransportModeVendor');
    }

    public function status() {
        return $this->belongsTo('App\Http\Models\Admin\MasterCargo\BagStatus', 'status_id', 'id');
    }
}
