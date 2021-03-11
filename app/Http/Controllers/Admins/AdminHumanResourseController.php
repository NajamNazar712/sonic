<?php

namespace App\Http\Controllers\Admins;

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
        $roles=['Admin','Rider'];
        $roles = collect($roles);
        return view('admin.human_resource.allusers')->with(['roles' => $roles]);
    }


    public function all_user_ajax()
    {

        $assigned_hubs = session('hubs');

        // if(session('role_id') != 1){
            $riders = Rider::where([['status' => 1, 'rider_type_id' =>1]])->whereIn('city_id', $assigned_hubs)->get();
            $admins = Admin::whereIn('default_hub_id', $assigned_hubs)->where('status', 1)->get();

        // }

        if(count(Auth::user()->hubs)>0){
            foreach (Auth::user()->hubs as $hub){
                $total_hubs[] = $hub->hub_id;
            }
            $riders = Rider::where([['status', 1],['rider_type_id',1]])->whereIn('city_id', $total_hubs)->distinct()->get();
            $admins = Admin::leftjoin('admin_hubs as ah', 'ah.admin_id', '=', 'admins.id')->whereIn('ah.hub_id', $total_hubs)->where('admins.status', 1)->groupBy('admins.id')->get();
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
                    if($rider->created_at){
                        $user['created_at'] = date_format($rider->created_at,"Y/m/d H:i:s");
                    }else{
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
                    if($admin->created_at){
                        $user['created_at'] = date_format($admin->created_at,"Y/m/d H:i:s");
                    }else{
                        $user['created_at'] = $admin->created_at;
                    }
                    
                    $users[] = $user;
                    $users = collect($users);
                }
            }
    
            return Datatables::of($users)
            ->make(true);  
        }else{
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
        
        
        

       


        // if(count(Auth::user()->hubs)>0){
        //     $riders = Rider::where([
        //         ['status', 1],
        //         ['city_id', Auth::user()->hubs->id],
        //     ])->get();
        //     $admins = Admin::where([
        //         ['status', 1],
        //         ['default_hub_id', Auth::user()->hubs->id],
        //     ])->get();
        // }else{
        //     $riders = Rider::where([
        //         ['status', 1],
        //         ['city_id', NULL],
        //     ])->get();
        //     $admins = Admin::where([
        //         ['status', 1],
        //         ['default_hub_id', NULL],
        //     ])->get();
        // }
        // $riders = Rider::where('status', 1)->get();
        //         $admins = Admin::where('status', 1)->get();
        // $admins = Admin::where('status', 1)->select('id', 'name', 'cnic', 'phone_number', 'trax_id', 'created_at')->get();
        // $riders = Rider::where('status', 1)->select('id', 'name', 'cnic', 'phone', 'trax_id', 'created_at')->get();
      
    }
}
