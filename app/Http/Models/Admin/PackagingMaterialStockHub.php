<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class PackagingMaterialStockHub extends Model
{
    public $timestamps = false;
    protected $fillable = ['hub_id','small_flyers','medium_flyers','large_flyers','boxes'];

}
