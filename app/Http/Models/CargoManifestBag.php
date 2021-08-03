<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CargoManifestBag extends Model
{
    public function shipment() {
        return $this->hasMany('App\Http\Models\CargoManifestBagShipments')->orderBy('shipment_id');
    }

    public function origin_hub() {
        return $this->belongsTo('App\Http\Models\City', 'origin_hub_id', 'id');
    }

    public function destination_hub() {
        return $this->belongsTo('App\Http\Models\City', 'destination_hub_id', 'id');
    }


    public function shipping_mode() {
        return $this->belongsTo('App\Http\Models\ShippingMode');
    }

    public function transport_mode() {
        return $this->belongsTo('App\Http\Models\TransportMode');
    }

    /*public function transport_mode_vendor() {
        return $this->belongsTo('App\Http\Models\TransportModeVendor');
    }*/

    public function status() {
        return $this->belongsTo('App\Http\Models\CargoManifestBagStatus', 'status_id', 'id');
    }
}
