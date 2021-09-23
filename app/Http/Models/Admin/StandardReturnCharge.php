<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class StandardReturnCharge extends Model
{
    protected $fillable = ['local','national_charges_class_0','national_charges_class_1','national_charges_class_2','national_charges_class_3'];
}
