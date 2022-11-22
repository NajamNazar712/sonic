<?php

namespace App\Http\Models\HR;

use Illuminate\Database\Eloquent\Model;

class EmployeeConfirmation extends Model
{
    public function employee_confirmation_rating() {
        return $this->hasOne('App\Http\Models\HR\EmployeeConfirmationRating');
    }
}
