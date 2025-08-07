<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeoCodeApiCount extends Model
{
    use HasFactory;
    protected $table = 'geo_code_api_counts';

    protected $fillable = [
        'api_count',
    ];
}
