<?php

namespace App\Http\Models\WMS;

use Illuminate\Database\Eloquent\Model;

class WmsProductBarcode extends Model
{
    protected $fillable = ['storage_advice_id', 'storage_date'];
    public function store_request(){
        return $this->belongsTo('App\Http\Models\WMS\WmsStoreRequests','store_request_id');
    }

    public function product(){
        return $this->belongsTo('App\Http\Models\WMS\WmsProduct');
    }
    public function leaf(){
        return $this->belongsTo('App\Http\Models\WMS\WmsFacilityStorageType', 'leaf_id', 'id');
    }
    public function shipment(){
        return $this->belongsTo('App\Http\Models\Shipment', 'shipment_id', 'id');
    }
}
