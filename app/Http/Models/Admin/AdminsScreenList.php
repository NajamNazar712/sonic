<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class AdminsScreenList extends Model
{
    protected $table = 'admins_screen_list';

    protected $fillable = ['url','name','permission_id'];
}
