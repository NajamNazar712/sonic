<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeoCodesAssignDestinationHubs extends Model
{
    use HasFactory;

    protected $fillable = ['destination_hub_id'];
}
