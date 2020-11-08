<?php

namespace App\Http\Models\WMS;

use Illuminate\Database\Eloquent\Model;

class WmsStoreRequests extends Model
{
    protected $table = 'wms_store_requests';

    public function items() {
		return $this->hasMany('App\Http\Models\WMS\WmsStoreRequestProducts', 'request_id', 'id');
	}

    public function shipper(){
        return $this->belongsTo('App\Http\Models\Customer\User', 'user_id', 'id');
    }
    public function pickup_address() {
		return $this->belongsTo('App\Http\Models\Customer\UserShippingInfo', 'pickup_address_id');
	}
    public function warehouse_pickup_address() {
		return $this->belongsTo('App\Http\Models\Customer\UserShippingInfo', 'warehouse_pickup_address_id');
	}
    public function product_barcode() {
		return $this->hasMany('App\Http\Models\WMS\WmsProductBarcode');
	}
}
