<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorporateUserOnDeliveredInvoice extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','admin_id','status','rate_type_id'];
}
