<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class InternationalShipmentExtraServiceCharges extends Model
{
    protected  $fillable = ['shipment_id','amount','added_by'];
}
