<?php

namespace App\Http\Controllers\Admins;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DisputeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    public function dispute_index(){
        return view('admin.dispute.index');
    }

}
