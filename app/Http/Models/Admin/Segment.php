<?php


namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Segment extends Model
{
    // public function sub_segments(){
    //     return $this->hasMany('App\Http\Models\Admin\SubCategorySegment');
    // }

    public function subCategorySegment(){
        return $this->hasMany('App\Http\Models\SubCategorySegment');
    }
}
