<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class CityInfo extends Model
{
   public function users(){
       return $this->hasMany('App\Http\Models\Shipper\User','city_code');
   }
   public function pickups(){
       return $this->belongsToMany('App\Http\Models\PickupType','city_pickup','city_code','pickup_type_id');
   }
   public function hubs(){
       return $this->belongsTo('App\Http\Models\HubInfo','hub_info_id');
   }
}
