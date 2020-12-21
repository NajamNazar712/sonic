<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class MonthClosing extends Model
{
    protected $fillable = ['shipment_id','status_id','added_by','closing_date','closing_type_id','updated_by','closing_updated_at','remarks'];
    public function responsibles() {
        return $this->hasMany('App\Http\Models\Admin\MonthClosingResponsible');
    }
}
