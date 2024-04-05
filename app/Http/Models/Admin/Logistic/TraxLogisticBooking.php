<?php

namespace App\Http\Models\Admin\Logistic;

use Illuminate\Database\Eloquent\Model;

class TraxLogisticBooking extends Model
{

    protected $fillable=['shipper_id','booking_date','cn_number','product_id','service_id','destination_id','shipper_reference','consignee_name','total_pieces','consignee_phone_1','total_dense_weight','total_volumetric_weight','user_type','created_by','updated_by'];

    public function getHandlingInstAttribute(){
        return $this->attributes['handling_inst'] ?? "N/A";
    }
}
