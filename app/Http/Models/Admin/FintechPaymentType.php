<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class FintechPaymentType extends Model
{
    protected $table      = "fintech_payment_types";
    protected $connection = "mysql";
}
