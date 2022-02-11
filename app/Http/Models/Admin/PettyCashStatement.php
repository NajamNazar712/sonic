<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class PettyCashStatement extends Model
{
    protected $table = 'petty_cash_statements';

    public function petty_cash_statement_details(){
        return $this->hasMany('App\Http\Models\Admin\PettyCashStatementDetail');
    }
    public function hub() {
        return $this->belongsTo('App\Http\Models\City', 'hub_id', 'id');
    }
    public function shipment(){
        return $this->belongsTo('App\Http\Models\Shipment');
    }
    public function zone() {
        return $this->belongsTo('App\Http\Models\Zone', 'zone_id', 'id');
    }
    public function sdn()
    {
        return $this->belongsTo('App\Http\Models\Admin\StationDepositNote','sdn_id');
    }

}
