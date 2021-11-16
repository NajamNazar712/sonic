<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class SalesTerritoryAdmin extends Model
{
    public function territory() {
        return $this->belongsTo('App\Http\Models\Admin\SalesTerritory', 'territory_id', 'id');
    }
    public function sale_designation() {
        return $this->belongsTo('App\Http\Models\Admin\SalesDesignation', 'designation_id', 'id');
    }
    public function user() {
        return $this->belongsTo('App\Http\Models\Admin\Admin', 'admin_id', 'id');
    }
}
