<?php

namespace App\Http\Controllers;
use App\Http\Models\Rider;

use Illuminate\Http\Request;

class abcController extends Controller
{
    public static function index(){
        $data = Rider::get();
        return $data;

    
    }
}
