<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeoCodesAssignSubSegments extends Model
{
    use HasFactory;

    protected $fillable = ['sub_segment_id'];
}
