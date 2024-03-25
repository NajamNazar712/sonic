<?php

namespace App\Http\Models\Admin\Logistic;

use Illuminate\Database\Eloquent\Model;

class TraxLogisticBooking extends Model
{


    public function getHandlingInstAttribute(){
        return $this->attributes['handling_inst'] ?? "N/A";
    }
}
