<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\HR\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Rider;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

use Auth;

class AdminHumanResourseController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('Permission');
    }

    public function download_docs()
    {
        return view('admin.human_resource.download_docs');
    }
    public function allusers()
    {

        // $riders = Rider::where('status', 1)->get();
        //         $admins = Admin::where('status', 1)->get();
        $roles = ['Admin', 'Rider'];
        $roles = collect($roles);
        return view('admin.human_resource.allusers')->with(['roles' => $roles]);
    }
    public function all_user_ajax()
    {


        $assigned_hubs = session('hubs');
        if (session('role_id') != 1) {
            if (count($assigned_hubs) > 0) {
                // $riders = Rider::where('status',1)->where('rider_type_id',1)->whereIn('city_id', $assigned_hubs)->get();
                $riders = Rider::join('cities', 'riders.city_id', '=', 'cities.id')
                    ->join('cities as c', 'cities.hub_id', '=', 'c.id')
                    ->select('c.name as hub', 'riders.id', 'riders.name', 'riders.trax_id', 'riders.phone', 'riders.cnic', 'riders.created_at as created_at')
                    ->where('rider_type_id', 1)
                    ->where('riders.status', 1)
                    ->whereIn('cities.hub_id', $assigned_hubs)->get();


                $admins = Admin::whereIn('default_hub_id', $assigned_hubs)->where('status', 1)->get();

                $users = array();
                if (count($riders) > 0) {
                    foreach ($riders as $rider) {
                        $user = array();
                        $user['id'] = $rider->id;
                        $user['name'] = $rider->name;
                        $user['cnic'] = $rider->cnic;
                        $user['phone'] = $rider->phone;
                        $user['trax_id'] = $rider->trax_id;
                        $user['role'] = 'Rider';
                        if ($rider->created_at) {
                            $user['created_at'] = date_format($rider->created_at, "Y/m/d H:i:s");
                        } else {
                            $user['created_at'] = $rider->created_at;
                        }
                        $users[] = $user;
                        $users = collect($users);
                    }
                }
                if (count($admins) > 0) {
                    foreach ($admins as $admin) {
                        $user = array();
                        $user['id'] = $admin->id;
                        $user['name'] = $admin->name;
                        $user['cnic'] = $admin->cnic;
                        $user['phone'] = $admin->phone_number;
                        $user['trax_id'] = $admin->trax_id;
                        $user['role'] = 'Admin';
                        if ($admin->created_at) {
                            $user['created_at'] = date_format($admin->created_at, "Y/m/d H:i:s");
                        } else {
                            $user['created_at'] = $admin->created_at;
                        }

                        $users[] = $user;
                        $users = collect($users);
                    }
                }

                return Datatables::of($users)
                    ->make(true);
            } else {
                $user = array();
                $user['id'] = NULL;
                $user['name'] = '';
                $user['cnic'] = '';
                $user['phone'] = '';
                $user['trax_id'] = '';
                $user['role'] = '';
                $user['created_at'] = '';
                $users[] = $user;
                $users = collect($users);
                return Datatables::of($users)
                    ->make(true);
            }
        } else {
           
            $riders = Rider::where('status', 1)->where('rider_type_id', 1)->get();

            $admins = Admin::where('status', 1)->get();

            $users = array();
            if (count($riders) > 0) {
                foreach ($riders as $rider) {
                    $user = array();
                    $user['id'] = $rider->id;
                    $user['name'] = $rider->name;
                    $user['cnic'] = $rider->cnic;
                    $user['phone'] = $rider->phone;
                    $user['trax_id'] = $rider->trax_id;
                    $user['role'] = 'Rider';
                    if ($rider->created_at) {
                        $user['created_at'] = date_format($rider->created_at, "Y/m/d H:i:s");
                    } else {
                        $user['created_at'] = $rider->created_at;
                    }
                    $users[] = $user;
                    $users = collect($users);
                }
            }
            if (count($admins) > 0) {
                foreach ($admins as $admin) {
                    $user = array();
                    $user['id'] = $admin->id;
                    $user['name'] = $admin->name;
                    $user['cnic'] = $admin->cnic;
                    $user['phone'] = $admin->phone_number;
                    $user['trax_id'] = $admin->trax_id;
                    $user['role'] = 'Admin';
                    if ($admin->created_at) {
                        $user['created_at'] = date_format($admin->created_at, "Y/m/d H:i:s");
                    } else {
                        $user['created_at'] = $admin->created_at;
                    }

                    $users[] = $user;
                    $users = collect($users);
                }
            }

            return Datatables::of($users)
                ->make(true);
        }
    }

    public function employee_directory_index(){
        return view('admin.human_resource.employee_directory.index');
    }

    public function employee_directory_list(Request $request){
        $employees = Employee::join('cities', 'employees.city_id', '=', 'cities.id')
            ->join('employee_genders as eg','eg.id','=','employees.employee_gender_id')
            ->join('employee_types as et','et.id','=','employees.employee_type_id')
            ->join('employee_request_statuses as ers','ers.id','=','employees.request_status_id')
            ->join('employee_statuses as es','es.id','=','employees.status_id')

            ->select(['employees.id as employee_id', 'employees.name as employee_name', 'cities.name as city' ,'employees.trax_id', 'eg.name as gender', 'employees.cnic', 'employees.phone_number', 'et.name as employee_type','ers.name as request_status', 'es.name as status', 'employees.created_at as requested_at']);

        if (session('role_id') != 1) {
            $employees = $employees->whereIn('cities.hub_id', session('hubs'));
        }

        return Datatables::of($employees)
            ->addColumn('id_padded', function ($user) {
                return str_pad($user->employee_id, 6, '0', STR_PAD_LEFT);
            })
            ->filterColumn('users.id', function ($query, $keyword) {
                return $query->where('users.id', '=', $keyword);
            })
            ->addColumn("action", function ($result) {
                $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

                $dropdown .= '<button type="button" class="dropdown-item" data-target-id="' . $result->id . '" data-toggle="modal" data-target="#BankInfoModal"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Bank Info</div></button>';

                $dropdown .= '
                </div>
              </div>
            ';

                return $dropdown;
            })
            ->make(true);
    }
    public function employee_directory_store(Request $request){
        return $request;
    }
}
