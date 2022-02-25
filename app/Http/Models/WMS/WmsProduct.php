<?php

namespace App\Http\Models\WMS;

use Illuminate\Database\Eloquent\Model;

class WmsProduct extends Model
{
    public function product_barcode(){
        return $this->hasMany('App\Http\Models\WMS\WmsProductBarcode', 'product_id');
    }
	public function shipper()
    {
        return $this->belongsTo('App\Http\Models\Shipper\User', 'user_id', 'id');
    }

    public function product_picking(){
        return $this->hasMany('App\Http\Models\WMS\WmsPendingPicking', 'product_id');
    }
    public function category(){
        return $this->belongsTo('App\Http\Models\WMS\WmsProductCategory', 'category_id', 'id');
    }
}
