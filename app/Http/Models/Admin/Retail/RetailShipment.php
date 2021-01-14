<?php

namespace App\http\Models\Admin\Retail;

use Illuminate\Database\Eloquent\Model;

class RetailShipment extends Model
{
    public function product() {
        return $this->belongsTo('App\Http\Models\Product', 'product_type_id', 'id');
    }
}
