<?php

namespace App\Http\Models\WMS;

use Illuminate\Database\Eloquent\Model;

class WmsPicklist extends Model
{
    public function items() {
        return $this->hasMany('App\Http\Models\WMS\WmsPicklistItem', 'picklist_id', 'id');
    }
    public function picker(){
        return $this->belongsTo('App\Http\Models\WMS\WmsPicker', 'picker_id', 'id');
    }
}
