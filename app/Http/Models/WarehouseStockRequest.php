<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseStockRequest extends Model
{
    //Request Fulfilled by
    public function request_send_by(){
        return $this->belongsTo('App\Http\Models\Warehouse\Warehouse', 'send_by', 'id');
    }
    //Requested by
    public function request_requested_by(){
        return $this->belongsTo('App\Http\Models\Warehouse\Warehouse', 'requested_by', 'id');
    }

    public function stock_request_details(){
        return $this->hasMany('App\Http\Models\WarehouseStockRequestDetail', 'request_id', 'id');
    }


}
