<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    public function city(){
        return $this->belongsTo('App\Http\Models\City');
    }
}
