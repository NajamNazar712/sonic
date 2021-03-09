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
        $riders = Rider::where('status', 1)->get();
        $admins = Admin::where('status', 1)->get();
        $roles=['Admin','Rider'];
        $roles = collect($roles);
        return view('admin.human_resource.allusers')->with(['riders' => $riders, 'admins' => $admins, 'roles' => $roles]);
    }


    public function all_user_ajax()
    {
        $admins = Admin::where('status', 1)->select('id', 'name', 'cnic', 'phone_number', 'trax_id', 'created_at')->get();
        $riders = Rider::where('status', 1)->select('id', 'name', 'cnic', 'phone', 'trax_id', 'created_at')->get();
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
                $user['created_at'] = $rider->created_at;
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
                $user['created_at'] = $admin->created_at;
                $users[] = $user;
                $users = collect($users);
            }
        }

        return Datatables::of($users)
            ->make(true);
    }
}
