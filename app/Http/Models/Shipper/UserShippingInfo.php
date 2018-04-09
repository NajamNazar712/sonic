<?php

namespace App\Http\Models\Shipper;

use Illuminate\Database\Eloquent\Model;

class UserShippingInfo extends Model
{
    protected $fillable = [
        'user_id', 'pickup_address','poc','phone','email',
    ];
}
