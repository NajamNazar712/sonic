<?php

namespace App\Http\Controllers\Admins\Retail;

use App\Http\Models\Admin\GlobalSettings;
use App\http\Models\Admin\Retail\RetailFranchise;
use App\http\Models\Admin\Retail\RetailTraxCenter;
use App\http\Models\Admin\Retail\RetailUser;
use App\Http\Models\City;
use App\Http\Models\Shipper\UserShippingInfo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class RetailAdminUserManagementController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin')->except('cancel');

        $this->middleware('Permission');
    }

    public static function add_user($name, $password, $phone_number, $hub, $cnic, $address, $category, $category_id){
        $user = new RetailUser();
//        $user->trax_id = null;
        $user->city_id = $hub;
        $user->hub_id = $hub;
        $user->name = $name;
        $user->password = Hash::make($password);
        $user->phone_no = $phone_number;
        $user->cnic = $cnic;
        $user->address = $address;
        $user->category = $category;
        $user->category_id = $category_id;
        $user->status = 1;
        $user->created_by = Auth::id();
        $user->updated_by = Auth::id();
        $user->save();

        return $user->id;
    }

    static public function add_pickup_address($user_id, $address, $person_of_contact, $phone_number, $email_address, $city_id, $default, $location_latitude, $location_longitude) {
        $user_shipping_info = new UserShippingInfo();

        $user_shipping_info->user_id = $user_id;
        $user_shipping_info->pickup_address = $address;
        $user_shipping_info->poc = $person_of_contact;
        $user_shipping_info->phone = $phone_number;
        $user_shipping_info->email = $email_address;
        $user_shipping_info->city_id = $city_id;
        $user_shipping_info->default_address = $default;
        $user_shipping_info->location_latitude = $location_latitude;
        $user_shipping_info->location_longitude = $location_longitude;

        $user_shipping_info->save();

        return $user_shipping_info->id;
    }

    public function franchise_index(){
        $hubs = City::where('hub', 1)->where('status', 1)->where('business_category_id', 1)->get();
        return view('admin.retail.franchise.index')->with(['hubs' => $hubs]);
    }

    public function franchise_list(){
        $franchise = RetailFranchise::join('admins as a', 'a.id', '=', 'retail_franchises.updated_by')
            ->join('cities as c', 'c.id', '=', 'retail_franchises.default_hub')
            ->select('retail_franchises.id', 'retail_franchises.name', 'retail_franchises.phone_no', 'retail_franchises.email', 'retail_franchises.cnic', 'c.name as default_hub', 'c.id as default_hub_id', 'a.name as updated_by', 'retail_franchises.status', 'retail_franchises.code', 'retail_franchises.location_latitude', 'retail_franchises.location_longitude', 'retail_franchises.created_at', 'retail_franchises.updated_at');

        $datatables = Datatables::of($franchise)
            ->editColumn('status', function ($data){
                if($data->status == 1){
                    return 'Active';
                }
                else{
                    return 'In-Active';
                }
            })
            ->addColumn('location', function ($data){
                $location = '<div class="text-center">';
                if($data->latitude != null && $data->longitude != null){
                    $location .= '<button type="button" class="btn btn-primary btn-sm"><a class="white" href="http://www.google.com/maps/place/' . $data->location_latitude . ',' . $data->location_longitude . '" target="_blank"><i class="la la-map-marker align-middle"></i></a></button>';
                    return $location;
                }
                else{
                    return '-';
                }
            })
            ->addColumn('action', function($data) {
                if (session('role_id') == 1 || in_array(436, session('permissions'))) {
                    $dropdown = '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">';
                    if ($data->status == 0) {
                        $dropdown .= '<button type="button" class="dropdown-item enable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
                    } else {
                        $dropdown .= '<button type="button" class="dropdown-item disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';
                    }
                    $dropdown .= '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Edit</div></button>
                    </div>
                  </div>
          ';
                    return $dropdown;
                }
                else{
                    return '';
                    }
            });
        return $datatables->make(true);
    }
    public function franchise_enable_disable(Request $request){
        $franchise = RetailFranchise::find($request->id);
        if($request->status == 1){
            $franchise->status = 1;
            $franchise->updated_by = Auth::id();
            $franchise->save();

            $franchise_users = RetailUser::where('category', 1)->where('category_id', $franchise->id)->where('status', 0);
            if($franchise_users->exists()){
                $franchise_users = $franchise_users->get();
                foreach ($franchise_users as $franchise_user){
                    $franchise_user->status = 1;
                    $franchise_user->updated_by = Auth::id();
                    $franchise_user->save();
                }
            }

            return response()->json(['status' => 1, 'success' => 'Franchise Enabled Successfully']);
        }
        elseif ($request->status == 0){
            $franchise->status = 0;
            $franchise->updated_by = Auth::id();
            $franchise->save();

            $franchise_users = RetailUser::where('category', 1)->where('category_id', $franchise->id)->where('status', 1);
            if($franchise_users->exists()){
                $franchise_users = $franchise_users->get();
                foreach ($franchise_users as $franchise_user){
                    $franchise_user->status = 0;
                    $franchise_user->updated_by = Auth::id();
                    $franchise_user->save();
                }
            }
            return response()->json(['status' => 1, 'success' => 'Franchise and its Users Disabled Successfully']);
        }
    }

    public function franchise_add(Request $request){
        $hub_count = RetailFranchise::where('default_hub', $request->hub)->count() + 1;

        $password = $request->password;
        $franchise = new RetailFranchise();
        $franchise->name = $request->name;
        $franchise->phone_no = $request->phone_number;
        $franchise->email = $request->email;
        $franchise->cnic = $request->cnic;
        $franchise->default_hub = $request->hub;
        $franchise->location_latitude = $request->lat;
        $franchise->location_longitude = $request->long;
        $franchise->updated_by = Auth::id();
        $franchise->status = 1;
        $franchise->save();

        $hub_name = City::find($request->hub)->name;
        $hub_code = substr($hub_name, 0, 3);
        $hub_code = strtoupper($hub_code);
        $code = 'FR-' . $hub_code . '-' . str_pad($hub_count, 3, 0, STR_PAD_LEFT);

        $franchise->code = $code;
        $franchise->save();

        $user_id = $this->add_user($franchise->name, $password, $franchise->phone_no, $franchise->default_hub, $franchise->cnic, null, 1, $franchise->id);

        $setting = GlobalSettings::where('type', 'retail_store')->first();
        $shipper_user_id = $setting->setting_value;

        $pickup_address_id = $this->add_pickup_address($shipper_user_id, $franchise->name . ' - ' . $hub_name, $franchise->name, $franchise->phone_no, $franchise->email, $franchise->default_hub, 0, $franchise->location_latitude, $franchise->location_longitude);

        $franchise->user_id = $user_id;
        $franchise->pickup_address_id = $pickup_address_id;
        $franchise->save();

        return redirect()->back()->with('success', 'Franchise Added Successfully!');
    }

    public function franchise_edit(Request $request){
        $id = $request->franchise_id;
        $retail_user = RetailUser::where('name', $request->name)
            ->where(function ($sub_query) use ($id){
                $sub_query->where(function ($sub_sub_query) use ($id){
                    $sub_sub_query->where('category', 1)
                        ->where('category_id', '!=', $id);
                })->orWhere('category', 2);
            });
        if(!$retail_user->exists()) {
            $franchise = RetailFranchise::find($request->franchise_id);

            $franchise->name = $request->name;
            $franchise->phone_no = $request->phone_number;
            $franchise->email = $request->email;
            $franchise->cnic = $request->cnic;
            $franchise->location_latitude = $request->lat;
            $franchise->location_longitude = $request->long;
            $franchise->updated_by = Auth::id();
            $franchise->save();

            $user = RetailUser::find($franchise->user_id);
            if($user){
                $user->name = $request->name;
                if($request->password != null){
                        $user->password = Hash::make($request->password);
                }
                $user->save();
            }


            return redirect()->back()->with('success', 'Franchise Updated Successfully!');
        }
        else{
            return redirect()->back()->with('error', 'Name must be unique!');
        }
    }

    public function trax_center_index(){
        $hubs = City::where('hub', 1)->where('status', 1)->where('business_category_id', 1)->get();
        return view('admin.retail.trax_center.index')->with(['hubs' => $hubs]);
    }

    public function trax_center_list(){
        $trax_center = RetailTraxCenter::join('admins as a', 'a.id', '=', 'retail_trax_centers.updated_by')
            ->join('cities as c', 'c.id', '=', 'retail_trax_centers.default_hub')
            ->select('retail_trax_centers.id', 'retail_trax_centers.name', 'retail_trax_centers.phone_no', 'retail_trax_centers.email', 'retail_trax_centers.cnic', 'c.name as default_hub', 'c.id as default_hub_id', 'a.name as updated_by', 'retail_trax_centers.status', 'retail_trax_centers.code', 'retail_trax_centers.location_latitude', 'retail_trax_centers.location_longitude', 'retail_trax_centers.created_at', 'retail_trax_centers.updated_at');

        $datatables = Datatables::of($trax_center)
            ->editColumn('status', function ($data){
                if($data->status == 1){
                    return 'Active';
                }
                else{
                    return 'In-Active';
                }
            })
            ->addColumn('location', function ($data){
                $location = '<div class="text-center">';
                if($data->latitude != null && $data->longitude != null){
                    $location .= '<button type="button" class="btn btn-primary btn-sm"><a class="white" href="http://www.google.com/maps/place/' . $data->location_latitude . ',' . $data->location_longitude . '" target="_blank"><i class="la la-map-marker align-middle"></i></a></button>';
                    return $location;
                }
                else{
                    return '-';
                }
            })
            ->addColumn('action', function($data) {
                if (session('role_id') == 1 || in_array(435, session('permissions'))) {
                    $dropdown = '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">';
                    if ($data->status == 0) {
                        $dropdown .= '<button type="button" class="dropdown-item enable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
                    } else {
                        $dropdown .= '<button type="button" class="dropdown-item disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';
                    }
                    $dropdown .= '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Edit</div></button>
                    </div>
                  </div>
          ';
                    return $dropdown;
                }
                else{
                    return '';
                }
            });
        return $datatables->make(true);
    }
    public function trax_center_enable_disable(Request $request){
        $trax_center = RetailTraxCenter::find($request->id);
        if($request->status == 1){
            $trax_center->status = 1;
            $trax_center->updated_by = Auth::id();
            $trax_center->save();
            $trax_center_users = RetailUser::where('category', 2)->where('category_id', $trax_center->id)->where('status', 0);
            if($trax_center_users->exists()){
                $trax_center_users = $trax_center_users->get();
                foreach ($trax_center_users as $trax_center_user){
                    $trax_center_user->status = 1;
                    $trax_center_user->updated_by = Auth::id();
                    $trax_center_user->save();
                }
            }

            return response()->json(['status' => 1, 'success' => 'Trax Center Enabled Successfully']);
        }
        elseif ($request->status == 0){
            $trax_center->status = 0;
            $trax_center->updated_by = Auth::id();
            $trax_center->save();

            $trax_center_users = RetailUser::where('category', 2)->where('category_id', $trax_center->id)->where('status', 1);
            if($trax_center_users->exists()){
                $trax_center_users = $trax_center_users->get();
                foreach ($trax_center_users as $trax_center_user){
                    $trax_center_user->status = 0;
                    $trax_center_user->updated_by = Auth::id();
                    $trax_center_user->save();
                }
            }
            return response()->json(['status' => 1, 'success' => 'Trax Center and its Users Disabled Successfully']);
        }
    }

    public function trax_center_add(Request $request){
        $hub_count = RetailTraxCenter::where('default_hub', $request->hub)->count() + 1;

        $password = $request->password;
        $trax_center = new RetailTraxCenter();
        $trax_center->name = $request->name;
        $trax_center->phone_no = $request->phone_number;
        $trax_center->email = $request->email;
        $trax_center->cnic = $request->cnic;
        $trax_center->default_hub = $request->hub;
        $trax_center->location_latitude = $request->lat;
        $trax_center->location_longitude = $request->long;
        $trax_center->updated_by = Auth::id();
        $trax_center->status = 1;
        $trax_center->save();

        $hub_name = City::find($request->hub)->name;
        $hub_code = substr($hub_name, 0, 3);
        $hub_code = strtoupper($hub_code);
        $code = 'TC-' . $hub_code. '-' . str_pad($hub_count, 3, 0, STR_PAD_LEFT);

        $trax_center->code = $code;
        $trax_center->save();

        $user_id = $this->add_user($trax_center->name, $password, $trax_center->phone_no, $trax_center->default_hub, $trax_center->cnic, null, 2, $trax_center->id);

        $setting = GlobalSettings::where('type', 'retail_store')->first();
        $shipper_user_id = $setting->setting_value;

        $pickup_address_id = $this->add_pickup_address($shipper_user_id, $trax_center->name . ' - ' . $hub_name, $trax_center->name, $trax_center->phone_no, $trax_center->email, $trax_center->default_hub, 0, $trax_center->location_latitude, $trax_center->location_longitude);
        $trax_center->pickup_address_id = $pickup_address_id;

        $trax_center->user_id = $user_id;
        $trax_center->save();
        return redirect()->back()->with('success', 'Trax Center Added Successfully!');
    }

    public function trax_center_edit(Request $request){
        $id = $request->trax_center_id;
        $retail_user = RetailUser::where('name', $request->name)
            ->where(function ($sub_query) use ($id){
                $sub_query->where(function ($sub_query) use ($id){
                    $sub_query->where('category', 2)
                        ->where('category_id', '!=', $id);
                })->orWhere('category', 1);
            });
        if(!$retail_user->exists()){
            $trax_center = RetailTraxCenter::find($request->trax_center_id);

            $trax_center->name = $request->name;
            $trax_center->phone_no = $request->phone_number;
            $trax_center->email = $request->email;
            $trax_center->cnic = $request->cnic;
            $trax_center->location_latitude = $request->lat;
            $trax_center->location_longitude = $request->long;
            $trax_center->updated_by = Auth::id();
            $trax_center->save();

            $user = RetailUser::find($trax_center->user_id);
            if($user){
                $user->name = $request->name;
                if($request->password != null){
                    $user->password = Hash::make($request->password);
                }
                $user->save();
            }

            return redirect()->back()->with('success', 'Trax Center Updated Successfully!');
        }
        else{
            return redirect()->back()->with('error', 'Name must be unique!');
        }
    }
    public function user_name(Request $request) {
        if ($request->filled('name')) {
            $email = RetailUser::where('name', $request->input('name'));

            if (!$email->exists()) {
                return 'true';
            }
            else {
                return 'false';
            }
        }
        else {
            return 'false';
        }
    }
}
