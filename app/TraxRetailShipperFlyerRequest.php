<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TraxRetailShipperFlyerRequest extends Model
{
    

    protected $table = 'trax_retail_shipper_flyer_requests';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'retail_shipper_id',
        'type_size_id',
        'trax_centre_id',
        'qty',
        'amount',
        'status',
        'trax_centre_name_verification_id'
    ];
}
