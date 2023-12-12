<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShipmentScanningJourneyAreaLog extends Model
{
    public function shipment_scanning_journey(){
        return $this->belongsTo('App\Http\Models\ShipmentScanningJourney','shipment_scanning_journey_id','id');
    }

    public function city_area(){
        return $this->belongsTo('App\Http\Models\CityArea','area_id','id');
    }
}
