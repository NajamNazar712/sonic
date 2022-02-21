<?php

namespace App\Http\Controllers\Admins\Retail;

use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\Retail\RetailFranchise;
use App\Http\Models\Admin\Retail\RetailTraxCenter;
use App\Http\Models\Admin\Retail\RetailUser;
use App\Http\Models\City;
use App\Http\Models\Shipper\UserShippingInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class RetailAdminUserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin')->except('cancel');

        $this->middleware('Permission');
    }

    public static function add_user($name, $password, $phone_number, $hub, $cnic, $address, $category, $category_id)
    {
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

    public static function add_pickup_address($user_id, $address, $person_of_contact, $phone_number, $email_address, $city_id, $default, $location_latitude, $location_longitude)
    {
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

    public function franchise_index()
    {    ActivityTrailController::createActivityTrailLog(Auth::id(),377);
        $hubs = City::where('hub', 1)->where('status', 1)->where('business_category_id', 1)->get();
        return view('admin.retail.franchise.index')->with(['hubs' => $hubs]);
    }

    public function franchise_list(Request $request)
    {
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),378);
        }

        $franchise = RetailFranchise::join('admins as a', 'a.id', '=', 'retail_franchises.updated_by')
            ->join('cities as c', 'c.id', '=', 'retail_franchises.default_hub')
            ->select('retail_franchises.id', 'retail_franchises.name', 'retail_franchises.phone_no', 'retail_franchises.email', 'retail_franchises.cnic', 'c.name as default_hub', 'c.id as default_hub_id', 'a.name as updated_by', 'retail_franchises.status', 'retail_franchises.code', 'retail_franchises.location_latitude', 'retail_franchises.location_longitude', 'retail_franchises.created_at', 'retail_franchises.updated_at','retail_franchises.discount');

        $datatables = Datatables::of($franchise)
            ->editColumn('status', function ($data) {
                if ($data->status == 1) {
                    return 'Active';
                } else {
                    return 'In-Active';
                }
            })
            ->addColumn('location', function ($data) {
                $location = '<div class="text-center">';
                if ($data->latitude != null && $data->longitude != null) {
                    $location .= '<button type="button" class="btn btn-primary btn-sm"><a class="white" href="http://www.google.com/maps/place/' . $data->location_latitude . ',' . $data->location_longitude . '" target="_blank"><i class="la la-map-marker align-middle"></i></a></button>';
                    return $location;
                } else {
                    return '-';
                }
            })
            ->addColumn('action', function ($data) {
                if (session('role_id') == 1 || in_array(436, session('permissions'))) {
                    $dropdown = '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">';
                    if ($data->status == 0) {
                        $dropdown .= '<button type="button" class="dropdown-item enable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
                    } else {
                        $dropdown .= '<button type="button" class="dropdown-item disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';
                    }
                    if ($data->discount != Null) {
                        $dropdown .= '<button type="button" class="dropdown-item edit_discount"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Edit Discount</div></button>';
                    } else {
                        $dropdown .= '<button type="button" class="dropdown-item add_discount"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Add Discount</div></button>';
                    }
                    $dropdown .= '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Edit</div></button>
                    </div>
                  </div>
          ';
                    return $dropdown;
                } else {
                    return '';
                }
            });
        return $datatables->make(true);
    }
    public function franchise_enable_disable(Request $request)
    {
        $franchise = RetailFranchise::find($request->id);
        if ($request->status == 1) {
            $franchise->status = 1;
            $franchise->updated_by = Auth::id();
            $franchise->save();

            return response()->json(['status' => 1, 'success' => 'Franchise Enabled Successfully']);
        } elseif ($request->status == 0) {
            $franchise->status = 0;
            $franchise->updated_by = Auth::id();
            $franchise->save();

            $franchise_users = RetailUser::where('category', 1)->where('category_id', $franchise->id)->where('status', 1);
            if ($franchise_users->exists()) {
                $franchise_users = $franchise_users->get();
                foreach ($franchise_users as $franchise_user) {
                    $franchise_user->status = 0;
                    $franchise_user->updated_by = Auth::id();
                    $franchise_user->save();
                }
            }
            return response()->json(['status' => 1, 'success' => 'Franchise and its Users Disabled Successfully']);
        }
    }

    public function franchise_add(Request $request)
    {
        $hub_count = RetailFranchise::where('default_hub', $request->hub)->count() + 1;

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

        $setting = GlobalSettings::where('type', 'retail_store')->first();
        $shipper_user_id = $setting->setting_value;

        $pickup_address_id = $this->add_pickup_address($shipper_user_id, $franchise->name . ' - ' . $hub_name, $franchise->name, $franchise->phone_no, $franchise->email, $franchise->default_hub, 0, $franchise->location_latitude, $franchise->location_longitude);

        $franchise->pickup_address_id = $pickup_address_id;
        $franchise->save();

        return redirect()->back()->with('success', 'Franchise Added Successfully!');
    }

    public function franchise_edit(Request $request)
    {
        $id = $request->franchise_id;
        $existing_franchise = RetailUser::where('name', $request->name)->where('category_id', '!=', $id);
        if (!$existing_franchise->exists()) {
            $franchise = RetailFranchise::find($request->franchise_id);

            $franchise->name = $request->name;
            $franchise->phone_no = $request->phone_number;
            $franchise->email = $request->email;
            $franchise->cnic = $request->cnic;
            $franchise->location_latitude = $request->lat;
            $franchise->location_longitude = $request->long;
            $franchise->updated_by = Auth::id();
            $franchise->save();

            return redirect()->back()->with('success', 'Franchise Updated Successfully!');
        } else {
            return redirect()->back()->with('error', 'Name must be unique!');
        }
    }

    public function franchise_name(Request $request)
    {
        if ($request->filled('name')) {
            $franchise = RetailFranchise::where('name', $request->input('name'));

            if (!$franchise->exists()) {
                return 'true';
            } else {
                return 'false';
            }
        } else {
            return 'false';
        }
    }

    public function trax_center_index()
    {   ActivityTrailController::createActivityTrailLog(Auth::id(),379);
        $hubs = City::where('hub', 1)->where('status', 1)->where('business_category_id', 1)->get();
        return view('admin.retail.trax_center.index')->with(['hubs' => $hubs]);
    }

    public function trax_center_list(Request $request)
    {
        if($request->get('excel') && $request->get('excel') == true)
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(),380);
    }
        $trax_center = RetailTraxCenter::join('admins as a', 'a.id', '=', 'retail_trax_centers.updated_by')
            ->join('cities as c', 'c.id', '=', 'retail_trax_centers.default_hub')
            ->select('retail_trax_centers.id', 'retail_trax_centers.name', 'retail_trax_centers.phone_no', 'retail_trax_centers.email', 'retail_trax_centers.cnic', 'c.name as default_hub', 'c.id as default_hub_id', 'a.name as updated_by', 'retail_trax_centers.status', 'retail_trax_centers.code', 'retail_trax_centers.location_latitude', 'retail_trax_centers.location_longitude', 'retail_trax_centers.created_at', 'retail_trax_centers.updated_at');

        $datatables = Datatables::of($trax_center)
            ->editColumn('status', function ($data) {
                if ($data->status == 1) {
                    return 'Active';
                } else {
                    return 'In-Active';
                }
            })
            ->addColumn('location', function ($data) {
                $location = '<div class="text-center">';
                if ($data->latitude != null && $data->longitude != null) {
                    $location .= '<button type="button" class="btn btn-primary btn-sm"><a class="white" href="http://www.google.com/maps/place/' . $data->location_latitude . ',' . $data->location_longitude . '" target="_blank"><i class="la la-map-marker align-middle"></i></a></button>';
                    return $location;
                } else {
                    return '-';
                }
            })
            ->addColumn('action', function ($data) {
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
                } else {
                    return '';
                }
            });
        return $datatables->make(true);
    }
    public function trax_center_enable_disable(Request $request)
    {
        $trax_center = RetailTraxCenter::find($request->id);
        if ($request->status == 1) {
            $trax_center->status = 1;
            $trax_center->updated_by = Auth::id();
            $trax_center->save();

            return response()->json(['status' => 1, 'success' => 'Trax Center Enabled Successfully']);
        } elseif ($request->status == 0) {
            $trax_center->status = 0;
            $trax_center->updated_by = Auth::id();
            $trax_center->save();

            $trax_center_users = RetailUser::where('category', 2)->where('category_id', $trax_center->id)->where('status', 1);
            if ($trax_center_users->exists()) {
                $trax_center_users = $trax_center_users->get();
                foreach ($trax_center_users as $trax_center_user) {
                    $trax_center_user->status = 0;
                    $trax_center_user->updated_by = Auth::id();
                    $trax_center_user->save();
                }
            }
            return response()->json(['status' => 1, 'success' => 'Trax Center and its Users Disabled Successfully']);
        }
    }

    public function trax_center_add(Request $request)
    {
        $hub_count = RetailTraxCenter::where('default_hub', $request->hub)->count() + 1;

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
        $code = 'TC-' . $hub_code . '-' . str_pad($hub_count, 3, 0, STR_PAD_LEFT);

        $trax_center->code = $code;
        $trax_center->save();

        $setting = GlobalSettings::where('type', 'retail_store')->first();
        $shipper_user_id = $setting->setting_value;

        $pickup_address_id = $this->add_pickup_address($shipper_user_id, $trax_center->name . ' - ' . $hub_name, $trax_center->name, $trax_center->phone_no, $trax_center->email, $trax_center->default_hub, 0, $trax_center->location_latitude, $trax_center->location_longitude);
        $trax_center->pickup_address_id = $pickup_address_id;
        $trax_center->save();
        return redirect()->back()->with('success', 'Trax Center Added Successfully!');
    }

    public function trax_center_edit(Request $request)
    {
        $existing_trax_center = RetailTraxCenter::where('name', $request->name)->where('id', '!=', $request->trax_center_id);
        if (!$existing_trax_center->exists()) {
            $trax_center = RetailTraxCenter::find($request->trax_center_id);

            $trax_center->name = $request->name;
            $trax_center->phone_no = $request->phone_number;
            $trax_center->email = $request->email;
            $trax_center->cnic = $request->cnic;
            $trax_center->location_latitude = $request->lat;
            $trax_center->location_longitude = $request->long;
            $trax_center->updated_by = Auth::id();
            $trax_center->save();

            return redirect()->back()->with('success', 'Trax Center Updated Successfully!');
        } else {
            return redirect()->back()->with('error', 'Trax Center Name must be unique!');
        }
    }

    public function trax_center_name(Request $request)
    {
        if ($request->filled('name')) {
            $trax_center = RetailTraxCenter::where('name', $request->input('name'));

            if (!$trax_center->exists()) {
                return 'true';
            } else {
                return 'false';
            }
        } else {
            return 'false';
        }
    }

    public function user_index()
    {   ActivityTrailController::createActivityTrailLog(Auth::id(),372);
        $trax_centers = RetailTraxCenter::where('status', 1)->get();
        $franchises = RetailFranchise::where('status', 1)->get();
        return view('admin.retail.users.index')->with(['trax_centers' => $trax_centers, 'franchises' => $franchises]);
    }

    public function user_edit($id)
    {
        $retail_user = RetailUser::find($id);
        $trax_centers = RetailTraxCenter::where('status', 1)->get();
        $franchises = RetailFranchise::where('status', 1)->get();
        return view('admin.retail.users.edit')->with(['retail_user' => $retail_user,'trax_centers' => $trax_centers , 'franchises' => $franchises]);
    }

    public function user_list(Request $request)
    {
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),373);
        }
        $franchise = RetailUser::leftjoin('retail_franchises as rf', 'rf.id', '=', 'retail_users.category_id')
            ->leftjoin('retail_trax_centers as rtc', 'rtc.id', '=', 'retail_users.category_id')
            ->join('admins as ac', 'ac.id', '=', 'retail_users.created_by')
            ->join('admins as au', 'au.id', '=', 'retail_users.updated_by')
            ->join('cities as c', 'c.id', '=', 'retail_users.city_id')
            ->join('cities as h', 'h.id', '=', 'retail_users.hub_id')
            ->select('retail_users.id', 'retail_users.trax_id', 'retail_users.name', 'retail_users.phone_no', 'retail_users.cnic', 'retail_users.address', 'retail_users.category', 'retail_users.created_at', 'retail_users.updated_at', 'retail_users.status', 'c.name as city', 'h.name as hub', 'ac.name as created_by', 'au.name as updated_by');

        $datatables = Datatables::of($franchise)
            ->editColumn('status', function ($data) {
                if ($data->status == 1) {
                    return 'Active';
                } else {
                    return 'In-Active';
                }
            })
            ->editColumn('category', function ($data) {
                if ($data->category == 1) {
                    return 'Franchise';
                } else {
                    return 'Trax Owned';
                }
            })
//            ->addColumn('location', function ($data){
        //                $location = '<div class="text-center">';
        //                if($data->latitude != null && $data->longitude != null){
        //                    $location .= '<button type="button" class="btn btn-primary btn-sm"><a class="white" href="http://www.google.com/maps/place/' . $data->location_latitude . ',' . $data->location_longitude . '" target="_blank"><i class="la la-map-marker align-middle"></i></a></button>';
        //                    return $location;
        //                }
        //                else{
        //                    return '-';
        //                }
        //            })
            ->addColumn('action', function ($data) {
                if (session('role_id') == 1 || in_array(475, session('permissions'))) {
                    $dropdown = '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">';
                    if ($data->status == 0) {
                        $dropdown .= '<button type="button" class="dropdown-item enable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
                    } else {
                        $dropdown .= '<button type="button" class="dropdown-item disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';
                    }
                    $dropdown .= '<button type="button" class="dropdown-item" data-target-id=' . $data->id . ' rel="editretailuser" data-toggle="modal" data-target="#editRetailUser"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit</div></button>';

//                    $dropdown .= '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Edit</div></button>
                    //          ';
                    $dropdown .= '
                    </div>
                  </div>
          ';
                    return $dropdown;
                } else {
                    return '';
                }
            });
        return $datatables->make(true);
    }
    public function user_enable_disable(Request $request)
    {
        $user = RetailUser::find($request->id);
        if ($request->status == 1) {
            if ($user->store->status == 1) {
                $user->status = 1;
                $user->updated_by = Auth::id();
                $user->save();

                return response()->json(['status' => 1, 'success' => 'User Enabled Successfully']);
            } else {
                return response()->json(['status' => 0, 'error' => 'User Store is Disabled']);
            }
        } elseif ($request->status == 0) {
            $user->status = 0;
            $user->updated_by = Auth::id();
            $user->save();
            return response()->json(['status' => 1, 'success' => 'User Disabled Successfully']);
        } else {
            return response()->json(['status' => 0, 'error' => 'Invalid Request']);
        }
    }

    public function user_add(Request $request)
    {
        $retail_user = RetailUser::where('name', $request->name);

        if (!$retail_user->exists()) {
            $password = $request->password;
            if ($request->store == 1) {
                $store = RetailFranchise::find($request->franchise);
            } else {
                $store = RetailTraxCenter::find($request->trax_center);
            }

            $this->add_user($request->name, $password, $request->phone_number, $store->default_hub, $request->cnic, $request->address, $request->store, $store->id);

            return redirect()->back()->with('success', 'Retail User Added Successfully!');
        } else {
            return redirect()->back()->with('success', 'Same Retail User already exists!');
        }
    }

    public function user_update(Request $request, $id)
    {
        $retail_user = RetailUser::find($id);
        $retail_user->name = $request->name;
        $retail_user->category = $request->store;
        if($request->store == 1){
            $retail_user->category_id = $request->franchise;
        }
        else{
            $retail_user->category_id = $request->trax_center;
        }
        $retail_user->password = Hash::make($request->password);
        $retail_user->phone_no = $request->phone_number;
        $retail_user->cnic = $request->cnic;
        $retail_user->address = $request->address;
        $retail_user->updated_by = Auth::id();
        $retail_user->save();

        return redirect()->back()->with('success', 'Retail User Updated Successfully!');

    }
    public function user_name(Request $request)
    {
        if ($request->filled('name')) {
            $email = RetailUser::where('name', $request->input('name'));

            if (!$email->exists()) {
                return 'true';
            } else {
                return 'false';
            }
        } else {
            return 'false';
        }
    }

    public function user_edit_name(Request $request, $id)
    {
        if ($request->filled('name')) {
            $user = RetailUser::where('name', $request->input('name'));

            if (!$user->exists()) {
                return 'true';
            } else {
                $user_name = RetailUser::find($id);
                if ($user_name->name == $request->input('name')) {
                    return 'true';

                } else {
                    return 'false';
                }
            }
        } else {
            return 'false';
        }
    }
}
