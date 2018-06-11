<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class HubInfo extends Model
{
    public function cities(){
        return $this->hasMany('App\Http\Models\CityInfo','hub_info_id');
    }
}
