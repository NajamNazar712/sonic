<?php

namespace App\http\Models\Admin\Lead;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    public function sales_person(){
        return $this->belongsTo('App\Http\Models\Admin\Admin', 'sale_person_id', 'id');
    }
    public function reference_person(){
        return $this->belongsTo('App\Http\Models\Admin\Admin', 'reference_person_id', 'id');
    }
    public function admin(){
        return $this->belongsTo('App\Http\Models\Admin\Admin', 'updated_by', 'id');
    }
    public function status() {
        return $this->belongsTo('App\Http\Models\Admin\Lead\LeadStatus', 'status_id', 'id');
    }
    public function city() {
        return $this->belongsTo('App\Http\Models\City', 'city_id', 'id');
    }
}
