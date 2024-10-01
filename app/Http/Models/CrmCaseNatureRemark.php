<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class CrmCaseNatureRemark extends Model
{
    protected $fillable = [
        'case_nature_id',
        'remarks'
    ];
}
