<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class ReplacementToRegularLog extends Model
{
    protected $fillable = [
        'shipment_id','updated_by','replacement_charges','product_type_id','item_description','item_quantity','item_price','insurance','type'
    ];

}
