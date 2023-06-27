<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdvancePettyCashStatementDetail extends Model
{
    public function petty_cash_statement() {
        return $this->belongsTo('App\Http\Models\Admin\AdvancePettyCashStatement','petty_cash_id','id');
    }
    public function heads(){
        return $this->belongsTo('App\Http\Models\Admin\PettyCashAccountHead', 'account_head_id', 'id');
    }
    public function titles(){
        return $this->belongsTo('App\Http\Models\Admin\PettyCashAccountTitle', 'account_title_id', 'id');
    }
    public function location() {
        return $this->belongsTo('App\Http\Models\City', 'hub_id', 'id');
    }


    public function zone() {
        return $this->belongsTo('App\Http\Models\Zone', 'zone_id', 'id');
    }


    public function city() {
        return $this->belongsTo('App\Http\Models\City', 'city_id', 'id');
    }

    public function employee() {
        return $this->belongsTo('App\Http\Models\Admin\Admin', 'employee_id', 'id');
    }
}
