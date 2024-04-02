<?php

namespace App\Http\Models\Admin\Logistic;

use Illuminate\Database\Eloquent\Model;

class TraxBookingPiece extends Model
{
    protected $fillable=['booking_id','from_pieces','to_pieces','quantity','user_type','created_by','updated_by'];

}
