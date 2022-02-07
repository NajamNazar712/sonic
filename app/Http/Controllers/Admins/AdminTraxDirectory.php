<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Admin;
use App\Http\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;

class AdminTraxDirectory extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }
    public function index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(),52);
        $hubs=City::select('id','name')->where('hub',1)->get();
        return view('admin.trax_directory.index')->with(['hubs'=>$hubs]);
    }

    public function list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 112);
        }

        $admin = Admin::join('employees as e', 'admins.trax_id', '=', 'e.trax_id')
            ->join('employee_designations as d', 'd.id', '=', 'admins.designation_id')
            ->join('admin_departments as ad', 'd.department_id', '=', 'ad.id')
            ->join('cities as c', 'c.id', '=', 'e.city_id')
            ->select('e.trax_id as trax_id', 'e.name as name', 'e.official_email as email', 'e.phone_number as phone', 'd.name as designation', 'ad.name as department_name', 'c.name as city', 'e.official_phone_number as official_phone_number')
            ->where('admins.status', 1)
            ->where('admins.role_id', '!=', 1);
        if($request->search_name){
            $admin = $admin->where('e.name', 'like','%' . $request->search_name . '%');
        }
        if($request->search_phone_number){
            $admin = $admin->where('e.phone_number', substr_replace($request->input('search_phone_number'), '-', 4, 0))
                ->orwhere('e.official_phone_number', substr_replace($request->input('search_phone_number'), '-', 4, 0));
        }
        if($request->search_trax_id){
            $admin = $admin->where('e.trax_id', $request->search_trax_id);
        }
        $datatable = Datatables::of($admin)
            ->addColumn('phone_number', function ($user) {
                if ($user->official_phone_number != null) {
                    return $user->official_phone_number;
                } else {
                    return $user->phone;
                }
            });

        return $datatable->make(true);
    }
}
