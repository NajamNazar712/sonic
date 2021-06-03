<?php

namespace App\Http\Models\Admin\MasterCargo;

use Illuminate\Database\Eloquent\Model;

class MasterCargo extends Model
{
    protected $table = 'master_cargoes';

    public function master_bags() {
        return $this->hasMany('App\Http\Models\Admin\MasterCargo\MasterCargoBag')->orderBy('bag_id');
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
        return $this->belongsTo('App\Http\Models\Admin\MasterCargo\MasterCargoStatus', 'status_id', 'id');
    }

    public function sender() {
        return $this->belongsTo('App\Http\Models\Admin\Admin', 'created_by', 'id');
    }

    public function receiver() {
        return $this->belongsTo('App\Http\Models\Admin\Admin', 'received_by', 'id');
    }

    public function route_management() {
        return $this->belongsTo('App\Http\Models\Admin\RouteManagement');
    }

    public function fleet() {
        return $this->belongsTo('App\Http\Models\Admin\Fleet');
    }
}
