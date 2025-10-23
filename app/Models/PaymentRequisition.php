<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentRequisition extends Model
{
    use HasFactory;
     protected $fillable = [
        'invoice_id',
        'payee_name',
        'ntn_cnic',
        'bank_id',
        'bank_title',
        'iban',
        'amount',
        'account_of_id',
        'related_department_id',
        'status',
        'description',
        'document1',
        'document2',
        'document3',
        'document4',
    ];
}
