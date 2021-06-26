<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;

class AdminOTPController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function admin_otp_index(){
        return view('admin.otp.admin');
    }

    public function admin_otp_list(Request $request){
        $admins = Admin::select('admins.id as id', 'admins.name as name', 'admins.otp as otp', 'admins.last_login_attempt')
            ->where('admins.status', 1)
            ->whereNotNull('admins.otp');
        if(session('role_id') != 1){
            $admins = $admins->join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->where('ar.department_id', session('department_id'));
        }
        $datatable = Datatables::of($admins);
        return $datatable->make(true);
    }
}
