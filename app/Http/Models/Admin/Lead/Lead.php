<?php

namespace App\Http\Models\Admin\Lead;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    public function sales_person()
    {
        return $this->belongsTo('App\Http\Models\Admin\Admin', 'sale_person_id', 'id');
    }
    public function reference_person()
    {
        return $this->belongsTo('App\Http\Models\Admin\Admin', 'reference_person_id', 'id');
    }
    public function admin()
    {
        return $this->belongsTo('App\Http\Models\Admin\Admin', 'updated_by', 'id');
    }
    public function status()
    {
        return $this->belongsTo('App\Http\Models\Admin\Lead\LeadStatus', 'status_id', 'id');
    }
    public function city()
    {
        return $this->belongsTo('App\Http\Models\City', 'city_id', 'id');
    }

    public function territory()
    {
        return $this->belongsTo('App\Http\Models\Admin\Territory', 'territory_id', 'id');
    }
    public function area_territoy()
    {
        return $this->belongsTo('App\Http\Models\Admin\AreaTerritory', 'territory_area_id', 'id');
    }

    public function service()
    {
        return $this->belongsTo('App\Http\Models\ServiceList', 'service_id', 'id');
    }

    public function lead_reference()
    {
        return $this->belongsTo('App\Http\Models\Admin\LeadReference', 'reference_id', 'id');
    }
}
