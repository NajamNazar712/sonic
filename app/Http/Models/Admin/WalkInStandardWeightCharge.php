<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class WalkInStandardWeightCharge extends Model
{
    //
    public $timestamps = FALSE;
    protected $fillable = [
        'shipping_mode_id', 'delivery_type_id', 'actual_weight', 'chargeable_weight_local', 'chargeable_weight_charges_class_0', 'chargeable_weight_charges_class_1', 'chargeable_weight_charges_class_2', 'chargeable_weight_charges_class_3', 'local', 'national_charges_class_0', 'national_charges_class_1', 'national_charges_class_2', 'national_charges_class_3'
    ];
}
