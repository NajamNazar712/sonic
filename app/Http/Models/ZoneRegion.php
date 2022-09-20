<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ZoneRegion extends Model
{
    public function zone() {
        return $this->belongsTo('App\Http\Models\Zone');
    }
    public function region() {
        return $this->belongsTo('App\Http\Models\Region');
    }
}
