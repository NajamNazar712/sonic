<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentRequisitionApproval extends Model
{
    use HasFactory;

    protected $fillable = ['payment_requisition_id', 'approver_role_id', 'dept_id', 'level', 'status', 'approved_by', 'approved_at', 'remarks'];
}
