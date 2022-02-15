<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class SMS extends Model
{
    protected $table = 'sms';

    protected  $fillable = ['to','body','status','otp'];
}
