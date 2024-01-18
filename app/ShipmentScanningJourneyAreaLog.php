<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShipmentScanningJourneyAreaLog extends Model
{

     protected $fillable = [
        'shipment_id',
        'shipment_scanning_journey_id',
        'hub_id',
        'area_id',
        'admin_id',
        'rider_id',
        'location_status',
        'status',
    ];
    public function shipment_scanning_journey(){
        return $this->belongsTo('App\Http\Models\ShipmentScanningJourney','shipment_scanning_journey_id','id');
    }

    public function city_area(){
        return $this->belongsTo('App\Http\Models\CityArea','area_id','id');
    }
}
