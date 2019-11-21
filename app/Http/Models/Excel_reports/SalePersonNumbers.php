<?php

namespace App\Http\Models\Excel_reports;

use Illuminate\Database\Eloquent\Model;

class SalePersonNumbers extends Model
{
    public function sales_person(){
        return $this->belongsTo('App\Http\Models\Admin\Admin', 'admin_id', 'id');
    }
}
