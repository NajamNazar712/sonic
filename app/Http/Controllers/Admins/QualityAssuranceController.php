<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Admin;

class QualityAssuranceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');

    }

    public function cx_training_index(){

        ActivityTrailController::createActivityTrailLog(Auth::id(),516);
        
        $agents = Admin::whereIn('role_id', [50, 49, 37.29, 28, 26, 21, 74, 13, 37])->where('status', 1)->get();

        return view('admin.qa.cx_training.index',compact('agents'));
    }
}
