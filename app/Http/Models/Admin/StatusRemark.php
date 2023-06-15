<?php

namespace App\Http\Models\Admin;

use App\Http\Models\Shipment;
use Illuminate\Database\Eloquent\Model;

class StatusRemark extends Model
{
   //
   protected $fillable = ['shipment_id','call_finding_id','sub_status_call_finding_id','call_to_id'];

   public function subStatusCallFinding()
   {
       return $this->belongsTo(SubStatusCallFinding::class, 'sub_status_call_finding_id');
   }
   public function shipmentid()
   {
       return $this->belongsTo(Shipment::class, 'shipment_id');
   }

}
