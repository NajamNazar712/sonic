<?php

namespace App\Http\Models\Admin\Retail;

use Illuminate\Database\Eloquent\Model;

class RetailShipment extends Model
{
    public function product() {
        return $this->belongsTo('App\Http\Models\Product', 'product_type_id', 'id');
    }
    public function payment_mode() {
        return $this->belongsTo('App\Http\Models\Admin\Retail\RetailPaymentMode', 'payment_mode_id', 'id');
    }
    public function shipping_modes() {
        return $this->belongsTo('App\Http\Models\Admin\Retail\RetailShippingMode', 'shipping_mode', 'id');
    }
    public function shipper() {
        return $this->belongsTo('App\Http\Models\Admin\Retail\RetailShipperInfo', 'shipper_account_no', 'id');
    }
}
