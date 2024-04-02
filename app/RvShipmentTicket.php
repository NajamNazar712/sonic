<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RvShipmentTicket extends Model
{
    use SoftDeletes;

    protected $guarded = [];
}
