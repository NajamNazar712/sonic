<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class PendingCashCollectionAgingReport extends Model
{
    public function city() {
        return $this->belongsTo('App\Http\Models\City', 'hub_id', 'id');
    }
    public function zone() {
        return $this->belongsTo('App\Http\Models\Zone', 'zone_id', 'id');
    }
}
