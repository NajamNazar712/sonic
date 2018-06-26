<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class StationDepositNote extends Model
{
    protected $fillable = [
      'hub_id','dncc_count','sdn_delivered_shipments','sdn_amount','sdn_expense','sdn_net_amount','deposited_by','banks_list_id'
    ];
    protected $table = 'station_deposit_notes';
    public function hub(){
            return $this->belongsTo('App\Http\Models\City', 'hub_id', 'id');
    }
    public function admin(){
        return $this->belongsTo('App\Http\Models\Admin\Admin','deposited_by','id');
    }
    public function bank(){
        return $this->belongsTo('App\Http\Models\BanksList','banks_list_id','id');
    }
}
