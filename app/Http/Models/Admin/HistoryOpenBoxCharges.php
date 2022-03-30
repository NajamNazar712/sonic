<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class HistoryOpenBoxCharges extends Model
{
    protected $fillable = ['user_id','shipping_mode_id','charges'];
}
