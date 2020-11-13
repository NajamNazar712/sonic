<?php

namespace App\Http\Models\WMS;

use Illuminate\Database\Eloquent\Model;

class WmsInvoiceStorage extends Model
{
    public function storage_type(){
        return $this->belongsTo('App\Http\Models\WMS\WmsStorageType', 'storage_type_id');
    }
}
