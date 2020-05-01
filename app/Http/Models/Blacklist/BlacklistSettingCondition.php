<?php

namespace App\Http\Models\Blacklist;

use Illuminate\Database\Eloquent\Model;

class BlacklistSettingCondition extends Model
{
    public function condition() {
        return $this->belongsTo('App\Http\Models\Blacklist\BlacklistCondition', 'blacklist_condition_id');
    }

    public function logic() {
        return $this->belongsTo('App\Http\Models\Blacklist\BlacklistLogic', 'blacklist_logic_id');
    }

    public function range() {
        return $this->belongsTo('App\Http\Models\Blacklist\BlacklistShipmentRange', 'blacklist_shipment_range_id');
    }

    public function operation() {
        return $this->belongsTo('App\Http\Models\Blacklist\BlacklistOperation', 'blacklist_operation_id');
    }
}
