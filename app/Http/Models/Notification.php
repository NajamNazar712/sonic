<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['name','type_id','subject','body'];
}