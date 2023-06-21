<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class CrmAgentAutoAssignSNKey extends Model
{
    public function shipper_non_keys() {
        return $this->belongsTo('App\Http\Models\Shipper\User', 'shipper_non_key_id', 'id');
    }
}
