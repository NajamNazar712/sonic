<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class RiderCategory extends Model
{
    public function rider(){
        return $this->hasMany('App\Http\Models\Rider');
    }
}
