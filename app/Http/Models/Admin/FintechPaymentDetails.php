<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class FintechPaymentDetails extends Model
{
    protected $connection = 'mysql';
    protected $table = 'fintech_payment_details';
}
