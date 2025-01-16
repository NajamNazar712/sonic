<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class RetailUserFamilyInformation extends Model
{
    protected $table = 'retail_user_family_informations';
    protected $fillable = [
        'retail_user_id',
        'family_member_name',
        'family_member_type',
        'retail_user_trax_id',
        'salary',
        'agreement_start_date',
    ];
}
