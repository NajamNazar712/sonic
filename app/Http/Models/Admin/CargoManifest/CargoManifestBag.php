<?php

namespace App\Http\Models\Admin\CargoManifest;

use Illuminate\Database\Eloquent\Model;

class CargoManifestBag extends Model
{
    public function shipment() {
        return $this->hasMany('App\Http\Models\Admin\CargoManifest\CargoManifestBagShipments')->orderBy('shipment_id');
    }

    public function origin_hub() {
        return $this->belongsTo('App\Http\Models\City', 'origin_hub_id', 'id');
    }

    public function destination_hub() {
        return $this->belongsTo('App\Http\Models\City', 'destination_hub_id', 'id');
    }

    public function current_hub() {
        return $this->belongsTo('App\Http\Models\City', 'current_hub_id', 'id');
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
        return $this->belongsTo('App\Http\Models\Admin\CargoManifest\CargoManifestBagStatus', 'status_id', 'id');
    }

}
