<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LostShipmentResponsible extends Model
{
    protected $fillable = ['shipment_id', 'user_id', 'user_type'];
}
