<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class CorporateUserPackagingInvoice extends Model
{
    protected $fillable = ['user_id','admin_id','status','time','rate_type_id'];
}
