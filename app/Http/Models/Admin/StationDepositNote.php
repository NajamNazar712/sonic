<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class StationDepositNote extends Model
{
    protected $fillable = [
      'hub_id','dncc_count','sdn_delivered_shipments','sdn_amount','sdn_expense','sdn_net_amount','deposited_by','banks_list_id','sdn_type'
    ];
    
    protected $table = 'station_deposit_notes';
    protected $casts = [
        'created_at' => "datetime:Y-m-d H:i:s",
    ];
    public function hub(){
            return $this->belongsTo('App\Http\Models\City', 'hub_id', 'id');
    }
    public function admin(){
        return $this->belongsTo('App\Http\Models\Admin\Admin','deposited_by','id');
    }
    public function bank(){
        return $this->belongsTo('App\Http\Models\BanksList','banks_list_id','id');
    }
    public function delivery_notes_list(){
        return $this->hasMany('App\Http\Models\Admin\DeliveryNoteStationDepositNote');
    }
    public function deposit_note_slips() {
        return $this->hasMany('App\Http\Models\Admin\StationDepositNoteSlip', 'station_deposit_note_id');
    }
    public function pickup_notes_list(){
        return $this->hasMany('App\Http\Models\Admin\PickupNoteStationDepositNote');
    }
}
