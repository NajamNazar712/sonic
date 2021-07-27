<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\False_;

class FleetVendor extends Model
{
    public $timestamps = FALSE;

    protected $fillable = ['name'];
}
