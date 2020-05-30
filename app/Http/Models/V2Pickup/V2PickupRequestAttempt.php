<?php

namespace App\Http\Models\V2Pickup;

use Illuminate\Database\Eloquent\Model;

class V2PickupRequestAttempt extends Model
{
    public function reason() {
        return $this->belongsTo('App\Http\Models\V2Pickup\V2PickupRequestNotPickReason', 'reason_id', 'id');
    }
}
