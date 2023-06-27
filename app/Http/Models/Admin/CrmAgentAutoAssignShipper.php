<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class CrmAgentAutoAssignShipper extends Model
{
    public function shipper_keys() {
        return $this->belongsTo('App\Http\Models\Shipper\User', 'shipper_key_id', 'id');
    }
}
