<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Rider;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class AdminHumanResourseController extends Controller
{
    //
    public function __construct() {
        $this->middleware('auth:admin');
        $this->middleware('Permission');
    }

    public function download_docs(){
        return view('admin.human_resource.download_docs');
    }

    public function allusers(){

        $riders = Rider::where('status',1)->get();
        $admins = Admin::where('status',1)->get();
       
        return view('admin.human_resource.allusers')->with(['riders'=>$riders,'admins'=>$admins]);

    }

    public function all_riders(){

        $data = Rider::where('status',1)->select('name', 'cnic','phone','address','created_at')->get();
            return Datatables::of($data)
                    ->make(true);
    }

    public function all_admins(){

        $data = Admin::where('status',1)->select('name', 'email','phone_number','cnic','designation','created_at')->get();
            return Datatables::of($data)
                    ->make(true);
    }
}
