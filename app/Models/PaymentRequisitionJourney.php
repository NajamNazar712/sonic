<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentRequisitionJourney extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_requisition_id',
        'changed_by',
        'status',
        'remarks',
    ];
}
