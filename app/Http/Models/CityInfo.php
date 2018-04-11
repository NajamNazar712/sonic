<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class CityInfo extends Model
{
   public function users(){
       return $this->hasMany('App\Http\Models\Shipper\User','city_code');
   }
}
