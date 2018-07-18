<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class PackagingStockHistory extends Model
{
    protected $fillable = [
        'admin_id','small_flyers','medium_flyers','large_flyers','boxes','entry_type','hub_id','reference_number'
    ];
    public function hub(){
        return $this->belongsTo('App\Http\Models\City','hub_id');
    }
}
