<?php

namespace App\Http\Models\Admin\Logistic;

use Illuminate\Database\Eloquent\Model;

class TraxItemRefernce extends Model
{
    protected $fillable=['cn_number','width','height','length','weight','no_piece','type','created_by','updated_by'];
}
