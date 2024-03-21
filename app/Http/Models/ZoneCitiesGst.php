<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ZoneCitiesGst extends Model
{
    protected $fillable = ['zone_id','city_id','gst','updated_by'];
}
