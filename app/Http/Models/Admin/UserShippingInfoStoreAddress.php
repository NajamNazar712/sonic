<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class UserShippingInfoStoreAddress extends Model
{
    protected $fillable = [
        'user_id',
        'user_shipping_infos_id',
        'shipper_store_id',
        'status',
    ];
}
