<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class RetailPickupNote extends Model
{
    public function hub(){
        return $this->belongsTo('App\Http\Models\City','hub_id');
    }
}
