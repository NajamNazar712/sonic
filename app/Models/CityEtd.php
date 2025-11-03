<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CityEtd extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'from_etd_city_id',
        'to_etd_city_id',
        'etd_range',
        'etd_label',
    ];
}
