<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class FtlRequest extends Model
{
    public function additional_cost()
    {
        return $this->hasMany(FtlRequestAdditionalCost::class);
    }

    public function origin() {
        return $this->belongsTo('App\Http\Models\City', 'origin_id', 'id');
    }

    public function destination() {
        return $this->belongsTo('App\Http\Models\City', 'destination_id', 'id');
    }

    public function shipment() {
        return $this->belongsTo('App\Http\Models\Shipment', 'shipment_id', 'id');
    }
}
