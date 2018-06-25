<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class StationDepositNote extends Model
{
    protected $fillable = [
      'hub_id','dncc_count','sdn_delivered_shipments','sdn_amount','sdn_expense','sdn_net_amount','deposited_by','banks_list_id'
    ];
    protected $table = 'station_deposit_notes';
}
