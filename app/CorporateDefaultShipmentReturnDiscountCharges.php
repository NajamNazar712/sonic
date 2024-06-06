<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CorporateDefaultShipmentReturnDiscountCharges extends Model
{
    protected $table = 'cor_def_shipment_return_discount_charges';

    protected $fillable = [
        'user_id','shipping_mode_id','return_discount_per'
    ];
}
