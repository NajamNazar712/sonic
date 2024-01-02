<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentScanningJourney extends Model
{
    public function screen_location() {
        return $this->belongsTo('App\Http\Models\ShipmentScanningScreenLocation', 'screen_location_id', 'id');
    }

    public function admin() {
        return $this->belongsTo('App\Http\Models\Admin\Admin', 'admin_id', 'id');
    }

    public function user() {
        return $this->belongsTo('App\Http\Models\Shipper\User', 'user_id', 'id');
    }

    public function rider() {
        return $this->belongsTo('App\Http\Models\Rider', 'admin_id', 'id');
    }

    public function scanning_area_logs() {
        return $this->hasOne('App\ShipmentScanningJourneyAreaLog', 'shipment_scanning_journey_id', 'id');
    }
}
