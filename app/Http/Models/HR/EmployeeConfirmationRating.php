<?php

namespace App\Http\Models\HR;

use Illuminate\Database\Eloquent\Model;

class EmployeeConfirmationRating extends Model
{
    public function employee_confirmation() {
        return $this->belongsTo('App\Http\Models\HR\EmployeeConfirmation');
    }

}
