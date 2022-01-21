<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class CorporateUserPackagingInvoiceLog extends Model
{
    protected $fillable = ['packaging_invoice_id','user_id','admin_id','status','rate_type_id'];
}
