<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Console\Helper\Table;

class AdvancePettyCashStatement extends Model
{
    public function petty_cash_statement_details()
    {
        return $this->hasMany('App\Http\Models\Admin\AdvancePettyCashStatementDetail');
    }
    public function hub()
    {
        return $this->belongsTo('App\Http\Models\City', 'hub_id', 'id');
    }
    public function shipment()
    {
        return $this->belongsTo('App\Http\Models\Shipment');
    }
    public function zone()
    {
        return $this->belongsTo('App\Http\Models\Zone', 'zone_id', 'id');
    }
    public function sdn()
    {
        return $this->belongsTo('App\Http\Models\Admin\StationDepositNote', 'sdn_id');
    }
}
