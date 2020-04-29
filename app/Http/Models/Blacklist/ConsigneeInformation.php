<?php

namespace App\Http\Models\Blacklist;

use Illuminate\Database\Eloquent\Model;

class ConsigneeInformation extends Model
{
    public function consignee_city() {
        return $this->belongsTo('App\Http\Models\City', 'city_id', 'id');
    }

    public function blacklisted_consignee(){
        return $this->hasOne('App\Http\Models\Blacklist\BlacklistedConsignee', 'consignee_information_id', 'id');
    }
}
