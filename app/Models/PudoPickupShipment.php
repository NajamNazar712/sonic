<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PudoPickupShipment extends Model
{
    use HasFactory;

    public function store() {
        if($this->retail_type == 1){
            return $this->belongsTo('App\Http\Models\Admin\Retail\RetailFranchise', 'retail_store_id', 'id');
        }
        else{
            return $this->belongsTo('App\Http\Models\Admin\Retail\RetailTraxCenter', 'retail_store_id', 'id');
        }
    }
}
