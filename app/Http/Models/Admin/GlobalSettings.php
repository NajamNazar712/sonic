<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class GlobalSettings extends Model
{
    protected $fillable = [
        'pickup_weight_threshold','type'
    ];
}
