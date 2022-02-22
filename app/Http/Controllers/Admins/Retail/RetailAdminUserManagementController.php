<?php

namespace App\Http\Controllers\Admins\Retail;

use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\Retail\RetailFranchise;
use App\Http\Models\Admin\Retail\RetailTraxCenter;
use App\Http\Models\Admin\Retail\RetailUser;
use App\Http\Models\City;
use App\Http\Models\RetailStandardRates;
use App\Http\Models\Shipper\UserShippingInfo;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 377);
        $hubs = City::where('hub', 1)->where('status', 1)->where('business_category_id', 1)->get();
        return view('admin.retail.franchise.index')->with(['hubs' => $hubs]);
    }

    public function franchise_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 378);
        }

        $franchise = RetailFranchise::join('admins as a', 'a.id', '=', 'retail_franchises.updated_by')
            ->join('cities as c', 'c.id', '=', 'retail_franchises.default_hub')
            ->select('retail_franchises.id', 'retail_franchises.name', 'retail_franchises.phone_no', 'retail_franchises.email', 'retail_franchises.cnic', 'c.name as default_hub', 'c.id as default_hub_id', 'a.name as updated_by', 'retail_franchises.status', 'retail_franchises.code', 'retail_franchises.location_latitude', 'retail_franchises.location_longitude', 'retail_franchises.created_at', 'retail_franchises.updated_at', 'retail_franchises.discount');

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
        $franchise->discount = $request->discount;
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
            $franchise->discount = $request->discount;
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
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 379);
        $hubs = City::where('hub', 1)->where('status', 1)->where('business_category_id', 1)->get();
        return view('admin.retail.trax_center.index')->with(['hubs' => $hubs]);
    }

    public function trax_center_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 380);
        }
        $trax_center = RetailTraxCenter::join('admins as a', 'a.id', '=', 'retail_trax_centers.updated_by')
            ->join('cities as c', 'c.id', '=', 'retail_trax_centers.default_hub')
            ->select('retail_trax_centers.id', 'retail_trax_centers.name', 'retail_trax_centers.phone_no', 'retail_trax_centers.email', 'retail_trax_centers.cnic', 'c.name as default_hub', 'c.id as default_hub_id', 'a.name as updated_by', 'retail_trax_centers.status', 'retail_trax_centers.code', 'retail_trax_centers.location_latitude', 'retail_trax_centers.location_longitude', 'retail_trax_centers.created_at', 'retail_trax_centers.updated_at','retail_trax_centers.discount');

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
        $trax_center->discount = $request->discount;
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
            $trax_center->discount = $request->discount;
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
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 372);
        $trax_centers = RetailTraxCenter::where('status', 1)->get();
        $franchises = RetailFranchise::where('status', 1)->get();
        return view('admin.retail.users.index')->with(['trax_centers' => $trax_centers, 'franchises' => $franchises]);
    }

    public function user_edit($id)
    {
        $retail_user = RetailUser::find($id);
        $trax_centers = RetailTraxCenter::where('status', 1)->get();
        $franchises = RetailFranchise::where('status', 1)->get();
        return view('admin.retail.users.edit')->with(['retail_user' => $retail_user, 'trax_centers' => $trax_centers, 'franchises' => $franchises]);
    }

    public function user_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 373);
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
        if ($request->store == 1) {
            $retail_user->category_id = $request->franchise;
        } else {
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

    public function add_standard_rates()
    {
        $rates = RetailStandardRates::all();
        if ($rates->isEmpty()) {
            return view('admin.retail.users.rates.add');
        } else {
            return redirect()->route('admin.retail.rates.edit');
        }
    }

    public function standard_rates_submit(Request $request)
    {
        //dd($request);
        $messages = [
            'saver_plus_range_up.*.required' => 'The saver plus range up field is required.',
            'saver_plus_range_up.*.numeric.*' => 'The saver plus range up field must be numeric or decimal.',
            'saver_plus_range_down.*.required' => 'The saver plus range down field is required.',
            'saver_plus_range_down.*.numeric.*' => 'The saver plus range down field must be numeric or decimal.',
            'saver_plus_kg_range.*.required' => 'The saver plus kg range  field is required.',
            'saver_plus_kg_range.*.numeric.*' => 'The saver plus kg range  field must be numeric or decimal.',
            'saver_plus_zone_a.*.required' => 'The saver plus zone a field is required.',
            'saver_plus_zone_a.*.numeric.*' => 'The saver plus zone a field must be numeric or decimal.',
            'saver_plus_zone_b.*.required' => 'The saver plus zone b field is required.',
            'saver_plus_zone_b.*.numeric.*' => 'The saver plus zone b field must be numeric or decimal.',
            'saver_plus_zone_c.*.required' => 'The saver plus zone c field is required.',
            'saver_plus_zone_c.*.numeric.*' => 'The saver plus zone c field must be numeric or decimal.',
            'saver_plus_zone_d.*.required' => 'The saver plus zone d field is required.',
            'saver_plus_zone_d.*.numeric.*' => 'The saver plus zone d field must be numeric or decimal.',

            'rush_range_up.*.required' => 'The rush range up field is required.',
            'rush_range_up.*.numeric.*' => 'The rush range up field must be numeric or decimal.',
            'rush_range_down.*.required' => 'The rush range down field is required.',
            'rush_range_down.*.numeric.*' => 'The rush range down field must be numeric or decimal.',
            'rush_kg_range.*.required' => 'The rush kg range field is required.',
            'rush_kg_range.*.numeric.*' => 'The saver plus kg range  field must be numeric or decimal.',
            'rush_wc.*.required' => 'The rush within city field is required.',
            'rush_wc.*.numeric.*' => 'The rush within city field must be numeric or decimal.',
            'rush_sz.*.required' => 'The rush same zone field is required.',
            'rush_sz.*.numeric.*' => 'The rush same zone field must be numeric or decimal.',
            'rush_dz.*.required' => 'The rush different zone field is required.',
            'rush_dz.*.numeric.*' => 'The rush different zone field must be numeric or decimal.',

            'cod_range_up.*.required' => 'The cod range up field is required.',
            'cod_range_up.*.numeric.*' => 'The cod range up field must be numeric or decimal.',
            'cod_range_down.*.required' => 'The cod range down field is required.',
            'cod_range_down.*.numeric.*' => 'The cod range down field must be numeric or decimal.',
            'cod_kg_range.*.required' => 'The cod kg range field is required.',
            'cod_kg_range.*.numeric.*' => 'The cod plus kg range  field must be numeric or decimal.',
            'cod_wc.*.required' => 'The cod within city field is required.',
            'cod_wc.*.numeric.*' => 'The cod within city field must be numeric or decimal.',
            'cod_sz.*.required' => 'The cod same zone field is required.',
            'cod_sz.*.numeric.*' => 'The cod same zone field must be numeric or decimal.',
            'cod_dz.*.required' => 'The cod different zone field is required.',
            'cod_dz.*.numeric.*' => 'The cod different zone field must be numeric or decimal.',

            'swift_range_up.*.required' => 'The swift range up field is required.',
            'swift_range_up.*.numeric.*' => 'The swift range up field must be numeric or decimal.',
            'swift_range_down.*.required' => 'The swift range down field is required.',
            'swift_range_down.*.numeric.*' => 'The swift range down field must be numeric or decimal.',
            'swift_kg_range.*.required' => 'The swift kg range field is required.',
            'swift_kg_range.*.numeric.*' => 'The swift plus kg range  field must be numeric or decimal.',
            'swift_wc.*.required' => 'The swift within city field is required.',
            'swift_wc.*.numeric.*' => 'The swift within city field must be numeric or decimal.',
            'swift_sz.*.required' => 'The swift same zone field is required.',
            'swift_sz.*.numeric.*' => 'The swift same zone field must be numeric or decimal.',
            'swift_dz.*.required' => 'The swift different zone field is required.',
            'swift_dz.*.numeric.*' => 'The swift different zone field must be numeric or decimal.',

            'flyer_range_up.*.required' => 'The flyer range up field is required.',
            'flyer_range_up.*.numeric.*' => 'The flyer range up field must be numeric or decimal.',
            'flyer_range_down.*.required' => 'The flyer range down field is required.',
            'flyer_range_down.*.numeric.*' => 'The flyer range down field must be numeric or decimal.',
            'flyer_kg_range.*.required' => 'The flyer kg range field is required.',
            'flyer_kg_range.*.numeric.*' => 'The flyer plus kg range  field must be numeric or decimal.',
            'flyer_wc.*.required' => 'The flyer within city field is required.',
            'flyer_wc.*.numeric.*' => 'The flyer within city field must be numeric or decimal.',
            'flyer_sz.*.required' => 'The flyer same zone field is required.',
            'flyer_sz.*.numeric.*' => 'The flyer same zone field must be numeric or decimal.',
            'flyer_dz.*.required' => 'The flyer different zone field is required.',
            'flyer_dz.*.numeric.*' => 'The flyer different zone field must be numeric or decimal.',

            'hdocs_range_up.*.required' => 'The hard docs range up field is required.',
            'hdocs_range_up.*.numeric.*' => 'The hard docs range up field must be numeric or decimal.',
            'hdocs_range_down.*.required' => 'The hard docs range down field is required.',
            'hdocs_range_down.*.numeric.*' => 'The hard docs range down field must be numeric or decimal.',
            'hdocs_kg_range.*.required' => 'The hard docs kg range field is required.',
            'hdocs_kg_range.*.numeric.*' => 'The hard docs plus kg range  field must be numeric or decimal.',
            'hdocs_wc.*.required' => 'The hard docs within city field is required.',
            'hdocs_wc.*.numeric.*' => 'The hard docs within city field must be numeric or decimal.',
            'hdocs_sz.*.required' => 'The hard docs same zone field is required.',
            'hdocs_sz.*.numeric.*' => 'The hard docs same zone field must be numeric or decimal.',
            'hdocs_dz.*.required' => 'The hard docs different zone field is required.',
            'hdocs_dz.*.numeric.*' => 'The hard docs different zone field must be numeric or decimal.',

            'trax_box_2_range_up.required' => 'The trax box for 2kg range up field is required.',
            'trax_box_2_range_up.numeric.*' => 'The trax box for 2kg range up field must be numeric or decimal.',
            'trax_box_2_range_down.required' => 'The trax box for 2kg range down field is required.',
            'trax_box_2_range_down.numeric.*' => 'The trax box for 2kg range down field must be numeric or decimal.',
            'trax_box_2_wc.required' => 'The trax box for 2kg within city field is required.',
            'trax_box_2_wc.numeric.*' => 'The trax box for 2kg within city field must be numeric or decimal.',
            'trax_box_2_sz.required' => 'The trax box for 2kg same zone field is required.',
            'trax_box_2_sz.numeric.*' => 'The trax box for 2kg same zone field must be numeric or decimal.',
            'trax_box_2_dz.required' => 'The trax box for 2kg different zone field is required.',
            'trax_box_2_dz.numeric.*' => 'The trax box for 2kg different zone field must be numeric or decimal.',

            'trax_box_5_range_up.required' => 'The trax box for 5kg range up field is required.',
            'trax_box_5_range_up.numeric.*' => 'The trax box for 5kg range up field must be numeric or decimal.',
            'trax_box_5_range_down.required' => 'The trax box for 5kg range down field is required.',
            'trax_box_5_range_down.numeric.*' => 'The trax box for 5kg range down field must be numeric or decimal.',
            'trax_box_5_wc.required' => 'The trax box for 5kg within city field is required.',
            'trax_box_5_wc.numeric.*' => 'The trax box for 5kg within city field must be numeric or decimal.',
            'trax_box_5_sz.required' => 'The trax box for 5kg same zone field is required.',
            'trax_box_5_sz.numeric.*' => 'The trax box for 5kg same zone field must be numeric or decimal.',
            'trax_box_5_dz.required' => 'The trax box for 5kg different zone field is required.',
            'trax_box_5_dz.numeric.*' => 'The trax box for 5kg different zone field must be numeric or decimal.',

            'trax_box_10_range_up.required' => 'The trax box for 10kg range up field is required.',
            'trax_box_10_range_up.numeric.*' => 'The trax box for 10kg range up field must be numeric or decimal.',
            'trax_box_10_range_down.required' => 'The trax box for 10kg range down field is required.',
            'trax_box_10_range_down.numeric.*' => 'The trax box for 10kg range down field must be numeric or decimal.',
            'trax_box_10_wc.required' => 'The trax box for 10kg within city field is required.',
            'trax_box_10_wc.numeric.*' => 'The trax box for 10kg within city field must be numeric or decimal.',
            'trax_box_10_sz.required' => 'The trax box for 10kg same zone field is required.',
            'trax_box_10_sz.numeric.*' => 'The trax box for 10kg same zone field must be numeric or decimal.',
            'trax_box_10_dz.required' => 'The trax box for 10kg different zone field is required.',
            'trax_box_10_dz.numeric.*' => 'The trax box for 10kg different zone field must be numeric or decimal.',

            'trax_box_15_range_up.required' => 'The trax box for 15kg range up field is required.',
            'trax_box_15_range_up.numeric.*' => 'The trax box for 15kg range up field must be numeric or decimal.',
            'trax_box_15_range_down.required' => 'The trax box for 15kg range down field is required.',
            'trax_box_15_range_down.numeric.*' => 'The trax box for 15kg range down field must be numeric or decimal.',
            'trax_box_15_wc.required' => 'The trax box for 15kg within city field is required.',
            'trax_box_15_wc.numeric.*' => 'The trax box for 15kg within city field must be numeric or decimal.',
            'trax_box_15_sz.required' => 'The trax box for 15kg same zone field is required.',
            'trax_box_15_sz.numeric.*' => 'The trax box for 15kg same zone field must be numeric or decimal.',
            'trax_box_15_dz.required' => 'The trax box for 15kg different zone field is required.',
            'trax_box_15_dz.numeric.*' => 'The trax box for 15kg different zone field must be numeric or decimal.',

            'trax_box_20_range_up.required' => 'The trax box for 20kg range up field is required.',
            'trax_box_20_range_up.numeric.*' => 'The trax box for 20kg range up field must be numeric or decimal.',
            'trax_box_20_range_down.required' => 'The trax box for 20kg range down field is required.',
            'trax_box_20_range_down.numeric.*' => 'The trax box for 20kg range down field must be numeric or decimal.',
            'trax_box_20_wc.required' => 'The trax box for 20kg within city field is required.',
            'trax_box_20_wc.numeric.*' => 'The trax box for 20kg within city field must be numeric or decimal.',
            'trax_box_20_sz.required' => 'The trax box for 20kg same zone field is required.',
            'trax_box_20_sz.numeric.*' => 'The trax box for 20kg same zone field must be numeric or decimal.',
            'trax_box_20_dz.required' => 'The trax box for 20kg different zone field is required.',
            'trax_box_20_dz.numeric.*' => 'The trax box for 20kg different zone field must be numeric or decimal.',

            'trax_box_30_range_up.required' => 'The trax box for 30kg range up field is required.',
            'trax_box_30_range_up.numeric.*' => 'The trax box for 30kg range up field must be numeric or decimal.',
            'trax_box_30_range_down.required' => 'The trax box for 30kg range down field is required.',
            'trax_box_30_range_down.numeric.*' => 'The trax box for 30kg range down field must be numeric or decimal.',
            'trax_box_30_wc.required' => 'The trax box for 30kg within city field is required.',
            'trax_box_30_wc.numeric.*' => 'The trax box for 30kg within city field must be numeric or decimal.',
            'trax_box_30_sz.required' => 'The trax box for 30kg same zone field is required.',
            'trax_box_30_sz.numeric.*' => 'The trax box for 30kg same zone field must be numeric or decimal.',
            'trax_box_30_dz.required' => 'The trax box for 30kg different zone field is required.',
            'trax_box_30_dz.numeric.*' => 'The trax box for 30kg different zone field must be numeric or decimal.',
        ];

        $validations = array();
        $saver_plus_validations = array();
        $rush_validations = array();
        $cod_validations = array();
        $swift_validations = array();
        $flyer_validations = array();
        $hdocs_validations = array();
        $trax_box_validations = array();

        $saver_plus_validations = [

            'saver_plus_range_up.*' => 'required|numeric',
            'saver_plus_range_down.*' => 'required|numeric',
            'saver_plus_zone_a.*' => 'required|numeric',
            'saver_plus_zone_b.*' => 'required|numeric',
            'saver_plus_zone_c.*' => 'required|numeric',
            'saver_plus_zone_d.*' => 'required|numeric',

        ];

        $rush_validations = [

            'rush_range_up.*' => 'required|numeric',
            'rush_range_down.*' => 'required|numeric',
            'rush_wc.*' => 'required|numeric',
            'rush_sz.*' => 'required|numeric',
            'rush_dz.*' => 'required|numeric',

        ];

        $cod_validations = [

            'cod_range_up.*' => 'required|numeric',
            'cod_range_down.*' => 'required|numeric',
            'cod_wc.*' => 'required|numeric',
            'cod_sz.*' => 'required|numeric',
            'cod_dz.*' => 'required|numeric',

        ];

        $swift_validations = [

            'swift_range_up.*' => 'required|numeric',
            'swift_range_down.*' => 'required|numeric',
            'swift_wc.*' => 'required|numeric',
            'swift_sz.*' => 'required|numeric',
            'swift_dz.*' => 'required|numeric',

        ];

        $flyer_validations = [

            'flyer_range_up.*' => 'required|numeric',
            'flyer_range_down.*' => 'required|numeric',
            'flyer_wc.*' => 'required|numeric',
            'flyer_sz.*' => 'required|numeric',
            'flyer_dz.*' => 'required|numeric',

        ];

        $hdocs_validations = [

            'hdocs_range_up.*' => 'required|numeric',
            'hdocs_range_down.*' => 'required|numeric',
            'hdocs_wc.*' => 'required|numeric',
            'hdocs_sz.*' => 'required|numeric',
            'hdocs_dz.*' => 'required|numeric',

        ];

        $trax_box_validations = [

            'trax_box_2_range_up' => 'required|numeric',
            'trax_box_2_range_down' => 'required|numeric',
            'trax_box_2_wc' => 'required|numeric',
            'trax_box_2_sz' => 'required|numeric',
            'trax_box_2_dz' => 'required|numeric',

            'trax_box_5_range_up' => 'required|numeric',
            'trax_box_5_range_down' => 'required|numeric',
            'trax_box_5_wc' => 'required|numeric',
            'trax_box_5_sz' => 'required|numeric',
            'trax_box_5_dz' => 'required|numeric',

            'trax_box_10_range_up' => 'required|numeric',
            'trax_box_10_range_down' => 'required|numeric',
            'trax_box_10_wc' => 'required|numeric',
            'trax_box_10_sz' => 'required|numeric',
            'trax_box_10_dz' => 'required|numeric',

            'trax_box_15_range_up' => 'required|numeric',
            'trax_box_15_range_down' => 'required|numeric',
            'trax_box_15_wc' => 'required|numeric',
            'trax_box_15_sz' => 'required|numeric',
            'trax_box_15_dz' => 'required|numeric',

            'trax_box_20_range_up' => 'required|numeric',
            'trax_box_20_range_down' => 'required|numeric',
            'trax_box_20_wc' => 'required|numeric',
            'trax_box_20_sz' => 'required|numeric',
            'trax_box_20_dz' => 'required|numeric',

            'trax_box_30_range_up' => 'required|numeric',
            'trax_box_30_range_down' => 'required|numeric',
            'trax_box_30_wc' => 'required|numeric',
            'trax_box_30_sz' => 'required|numeric',
            'trax_box_30_dz' => 'required|numeric',
        ];


        $validations = array_merge($saver_plus_validations, $rush_validations, $cod_validations, $swift_validations, $flyer_validations, $hdocs_validations, $trax_box_validations);

        $validate = Validator::make($request->all(), $validations, $messages);

        if ($validate->fails()) {
            return redirect()->back()
                ->withErrors($validate)
                ->withInput();
        }


        $saverPlus = RetailStandardRates::where('shipping_mode_id', 1)->get();

        if ($saverPlus->isEmpty()) {
            foreach ($request->saver_plus_range_up as $index => $saver_plus_range_up) {

                $retail = new RetailStandardRates();
                $retail->range_up = $request->saver_plus_range_up[$index];
                $retail->range_down = $request->saver_plus_range_down[$index];
                $retail->shipping_mode_id = 1;
                if (isset($request->saver_plus_kg_range[$index])) {
                    $retail->kg_range = $request->saver_plus_kg_range[$index];
                    $retail->weight_addition = 1;
                } else {
                    $retail->kg_range = 0;
                    $retail->weight_addition = 0;
                }
                $retail->zone_a = $request->saver_plus_zone_a[$index];
                $retail->zone_b = $request->saver_plus_zone_b[$index];
                $retail->zone_c = $request->saver_plus_zone_c[$index];
                $retail->zone_d = $request->saver_plus_zone_d[$index];
                $retail->save();
            }

        }


        $rush = RetailStandardRates::where('shipping_mode_id', 2)->get();

        if ($rush->isEmpty()) {

            foreach ($request->rush_range_up as $index => $rush_range_up) {

                $retail = new RetailStandardRates();
                $retail->range_up = $request->rush_range_up[$index];
                $retail->range_down = $request->rush_range_down[$index];
                $retail->shipping_mode_id = 2;
                if (isset($request->rush_kg_range[$index])) {
                    $retail->kg_range = $request->rush_kg_range[$index];
                    $retail->weight_addition = 1;
                } else {
                    $retail->kg_range = 0;
                    $retail->weight_addition = 0;
                }
                $retail->within_city = $request->rush_wc[$index];
                $retail->same_zone = $request->rush_sz[$index];
                $retail->different_zone = $request->rush_dz[$index];
                $retail->save();
            }

        }


        $cod = RetailStandardRates::where('shipping_mode_id', 3)->get();

        if ($cod->isEmpty()) {

            foreach ($request->cod_range_up as $index => $cod_range_up) {

                $retail = new RetailStandardRates();
                $retail->range_up = $request->cod_range_up[$index];
                $retail->range_down = $request->cod_range_down[$index];
                $retail->shipping_mode_id = 3;
                if (isset($request->cod_kg_range[$index])) {
                    $retail->kg_range = $request->cod_kg_range[$index];
                    $retail->weight_addition = 1;
                } else {
                    $retail->kg_range = 0;
                    $retail->weight_addition = 0;
                }
                $retail->within_city = $request->cod_wc[$index];
                $retail->same_zone = $request->cod_sz[$index];
                $retail->different_zone = $request->cod_dz[$index];
                $retail->save();
            }

        }


        $swift = RetailStandardRates::where('shipping_mode_id', 4)->get();

        if ($swift->isEmpty()) {

            foreach ($request->swift_range_up as $index => $swift_range_up) {

                $retail = new RetailStandardRates();
                $retail->range_up = $request->swift_range_up[$index];
                $retail->range_down = $request->swift_range_down[$index];
                $retail->shipping_mode_id = 4;
                if (isset($request->swift_kg_range[$index])) {
                    $retail->kg_range = $request->swift_kg_range[$index];
                    $retail->weight_addition = 1;
                } else {
                    $retail->kg_range = 0;
                    $retail->weight_addition = 0;
                }
                $retail->within_city = $request->swift_wc[$index];
                $retail->same_zone = $request->swift_sz[$index];
                $retail->different_zone = $request->swift_dz[$index];
                $retail->save();
            }

        }


        $flyer = RetailStandardRates::where('shipping_mode_id', 5)->get();

        if ($flyer->isEmpty()) {

            foreach ($request->flyer_range_up as $index => $flyer_range_up) {

                $retail = new RetailStandardRates();
                $retail->range_up = $request->flyer_range_up[$index];
                $retail->range_down = $request->flyer_range_down[$index];
                $retail->shipping_mode_id = 5;
                if (isset($request->flyer_kg_range[$index])) {
                    $retail->kg_range = $request->flyer_kg_range[$index];
                    $retail->weight_addition = 1;
                } else {
                    $retail->kg_range = 0;
                    $retail->weight_addition = 0;
                }
                $retail->within_city = $request->flyer_wc[$index];
                $retail->same_zone = $request->flyer_sz[$index];
                $retail->different_zone = $request->flyer_dz[$index];
                $retail->save();
            }

        }


        $hdocs = RetailStandardRates::where('shipping_mode_id', 6)->get();

        if ($hdocs->isEmpty()) {

            foreach ($request->hdocs_range_up as $index => $hdocs_range_up) {

                $retail = new RetailStandardRates();
                $retail->range_up = $request->hdocs_range_up[$index];
                $retail->range_down = $request->hdocs_range_down[$index];
                $retail->shipping_mode_id = 6;
                if (isset($request->hdocs_kg_range[$index])) {
                    $retail->kg_range = $request->hdocs_kg_range[$index];
                    $retail->weight_addition = 1;
                } else {
                    $retail->kg_range = 0;
                    $retail->weight_addition = 0;
                }
                $retail->within_city = $request->hdocs_wc[$index];
                $retail->same_zone = $request->hdocs_sz[$index];
                $retail->different_zone = $request->hdocs_dz[$index];
                $retail->save();
            }
        }


        $trax_box = RetailStandardRates::where('shipping_mode_id', 7)->get();

        if ($trax_box->isEmpty()) {

            $now = Carbon::now();
            $data = [
                [
                    'range_up' => $request->trax_box_2_range_up,
                    'range_down' => $request->trax_box_2_range_down,
                    'shipping_mode_id' => 7,
                    'kg_range' => ($request->trax_box_2_weight) ? $request->trax_box_2_weight : 0,
                    'weight_addition' => ($request->trax_box_2_switch == 'on') ? 1 : 0,
                    'within_city' => $request->trax_box_2_wc,
                    'same_zone' => $request->trax_box_2_sz,
                    'different_zone' => $request->trax_box_2_dz,
                    'trax_box_id' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'range_up' => $request->trax_box_5range_up,
                    'range_down' => $request->trax_box_5_range_down,
                    'shipping_mode_id' => 7,
                    'kg_range' => ($request->trax_box_5_weight) ? $request->trax_box_5_weight : 0,
                    'weight_addition' => ($request->trax_box_5_switch == 'on') ? 1 : 0,
                    'within_city' => $request->trax_box_5_wc,
                    'same_zone' => $request->trax_box_5_sz,
                    'different_zone' => $request->trax_box_5_dz,
                    'trax_box_id' => 2,
                    'created_at' => $now,
                    'updated_at' => $now,

                ],
                [
                    'range_up' => $request->trax_box_10_range_up,
                    'range_down' => $request->trax_box_10_range_down,
                    'shipping_mode_id' => 7,
                    'kg_range' => ($request->trax_box_10_weight) ? $request->trax_box_10_weight : 0,
                    'weight_addition' => ($request->trax_box_10_switch == 'on') ? 1 : 0,
                    'within_city' => $request->trax_box_10_wc,
                    'same_zone' => $request->trax_box_10_sz,
                    'different_zone' => $request->trax_box_10_dz,
                    'trax_box_id' => 3,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'range_up' => $request->trax_box_15_range_up,
                    'range_down' => $request->trax_box_15_range_down,
                    'shipping_mode_id' => 7,
                    'kg_range' => ($request->trax_box_15_weight) ? $request->trax_box_15_weight : 0,
                    'weight_addition' => ($request->trax_box_15_switch == 'on') ? 1 : 0,
                    'within_city' => $request->trax_box_15_wc,
                    'same_zone' => $request->trax_box_15_sz,
                    'different_zone' => $request->trax_box_15_dz,
                    'trax_box_id' => 4,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'range_up' => $request->trax_box_20_range_up,
                    'range_down' => $request->trax_box_20_range_down,
                    'shipping_mode_id' => 7,
                    'kg_range' => ($request->trax_box_20_weight) ? $request->trax_box_20_weight : 0,
                    'weight_addition' => ($request->trax_box_20_switch == 'on') ? 1 : 0,
                    'within_city' => $request->trax_box_20_wc,
                    'same_zone' => $request->trax_box_20_sz,
                    'different_zone' => $request->trax_box_20_dz,
                    'trax_box_id' => 5,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'range_up' => $request->trax_box_30kg_range_up,
                    'range_down' => $request->trax_box_30_range_down,
                    'shipping_mode_id' => 7,
                    'kg_range' => ($request->trax_box_30_weight) ? $request->trax_box_30_weight : 0,
                    'weight_addition' => ($request->trax_box_30_switch == 'on') ? 1 : 0,
                    'within_city' => $request->trax_box_30_wc,
                    'same_zone' => $request->trax_box_30_sz,
                    'different_zone' => $request->trax_box_30_dz,
                    'trax_box_id' => 6,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            ];
            RetailStandardRates::insert($data);
        }

        return redirect()->route('admin.retail.rates.edit')->with('success', 'Rates added');
    }

    public function standard_rates_edit()
    {
        $saver_plus = RetailStandardRates::where('shipping_mode_id', 1)->get();
        $rush_data = RetailStandardRates::where('shipping_mode_id', 2)->get();
        $cod_data = RetailStandardRates::where('shipping_mode_id', 3)->get();
        $swift = RetailStandardRates::where('shipping_mode_id', 4)->get();
        $flyers = RetailStandardRates::where('shipping_mode_id', 5)->get();
        $hard_docs = RetailStandardRates::where('shipping_mode_id', 6)->get();
        $trax_box = RetailStandardRates::join('retail_trax_boxes as rtb', 'rtb.id', '=', 'retail_standard_rates.trax_box_id')->where('shipping_mode_id', 7)->whereNotNull('retail_standard_rates.trax_box_id')->select(['retail_standard_rates.range_up', 'retail_standard_rates.range_down', 'retail_standard_rates.weight_addition as weight_addition', 'retail_standard_rates.shipping_mode_id', 'retail_standard_rates.trax_box_id', 'retail_standard_rates.kg_range', 'retail_standard_rates.within_city', 'retail_standard_rates.same_zone', 'retail_standard_rates.different_zone', 'rtb.name as weight'])->get();
        //dd($trax_box);
        return view('admin.retail.users.rates.edit', compact('saver_plus', 'swift', 'rush_data', 'cod_data', 'flyers', 'hard_docs', 'trax_box'));
    }

    public function standard_rates_update(Request $request)
    {

        //dd($request);
        $messages = [
            'saver_plus_range_up.*.required' => 'The saver plus range up field is required.',
            'saver_plus_range_up.*.numeric.*' => 'The saver plus range up field must be numeric or decimal.',
            'saver_plus_range_down.*.required' => 'The saver plus range down field is required.',
            'saver_plus_range_down.*.numeric.*' => 'The saver plus range down field must be numeric or decimal.',
            'saver_plus_kg_range.*.required' => 'The saver plus kg range  field is required.',
            'saver_plus_kg_range.*.numeric.*' => 'The saver plus kg range  field must be numeric or decimal.',
            'saver_plus_zone_a.*.required' => 'The saver plus zone a field is required.',
            'saver_plus_zone_a.*.numeric.*' => 'The saver plus zone a field must be numeric or decimal.',
            'saver_plus_zone_b.*.required' => 'The saver plus zone b field is required.',
            'saver_plus_zone_b.*.numeric.*' => 'The saver plus zone b field must be numeric or decimal.',
            'saver_plus_zone_c.*.required' => 'The saver plus zone c field is required.',
            'saver_plus_zone_c.*.numeric.*' => 'The saver plus zone c field must be numeric or decimal.',
            'saver_plus_zone_d.*.required' => 'The saver plus zone d field is required.',
            'saver_plus_zone_d.*.numeric.*' => 'The saver plus zone d field must be numeric or decimal.',

            'rush_range_up.*.required' => 'The rush range up field is required.',
            'rush_range_up.*.numeric.*' => 'The rush range up field must be numeric or decimal.',
            'rush_range_down.*.required' => 'The rush range down field is required.',
            'rush_range_down.*.numeric.*' => 'The rush range down field must be numeric or decimal.',
            'rush_kg_range.*.required' => 'The rush kg range field is required.',
            'rush_kg_range.*.numeric.*' => 'The saver plus kg range  field must be numeric or decimal.',
            'rush_wc.*.required' => 'The rush within city field is required.',
            'rush_wc.*.numeric.*' => 'The rush within city field must be numeric or decimal.',
            'rush_sz.*.required' => 'The rush same zone field is required.',
            'rush_sz.*.numeric.*' => 'The rush same zone field must be numeric or decimal.',
            'rush_dz.*.required' => 'The rush different zone field is required.',
            'rush_dz.*.numeric.*' => 'The rush different zone field must be numeric or decimal.',

            'cod_range_up.*.required' => 'The cod range up field is required.',
            'cod_range_up.*.numeric.*' => 'The cod range up field must be numeric or decimal.',
            'cod_range_down.*.required' => 'The cod range down field is required.',
            'cod_range_down.*.numeric.*' => 'The cod range down field must be numeric or decimal.',
            'cod_kg_range.*.required' => 'The cod kg range field is required.',
            'cod_kg_range.*.numeric.*' => 'The cod plus kg range  field must be numeric or decimal.',
            'cod_wc.*.required' => 'The cod within city field is required.',
            'cod_wc.*.numeric.*' => 'The cod within city field must be numeric or decimal.',
            'cod_sz.*.required' => 'The cod same zone field is required.',
            'cod_sz.*.numeric.*' => 'The cod same zone field must be numeric or decimal.',
            'cod_dz.*.required' => 'The cod different zone field is required.',
            'cod_dz.*.numeric.*' => 'The cod different zone field must be numeric or decimal.',

            'swift_range_up.*.required' => 'The swift range up field is required.',
            'swift_range_up.*.numeric.*' => 'The swift range up field must be numeric or decimal.',
            'swift_range_down.*.required' => 'The swift range down field is required.',
            'swift_range_down.*.numeric.*' => 'The swift range down field must be numeric or decimal.',
            'swift_kg_range.*.required' => 'The swift kg range field is required.',
            'swift_kg_range.*.numeric.*' => 'The swift plus kg range  field must be numeric or decimal.',
            'swift_wc.*.required' => 'The swift within city field is required.',
            'swift_wc.*.numeric.*' => 'The swift within city field must be numeric or decimal.',
            'swift_sz.*.required' => 'The swift same zone field is required.',
            'swift_sz.*.numeric.*' => 'The swift same zone field must be numeric or decimal.',
            'swift_dz.*.required' => 'The swift different zone field is required.',
            'swift_dz.*.numeric.*' => 'The swift different zone field must be numeric or decimal.',

            'flyer_range_up.*.required' => 'The flyer range up field is required.',
            'flyer_range_up.*.numeric.*' => 'The flyer range up field must be numeric or decimal.',
            'flyer_range_down.*.required' => 'The flyer range down field is required.',
            'flyer_range_down.*.numeric.*' => 'The flyer range down field must be numeric or decimal.',
            'flyer_kg_range.*.required' => 'The flyer kg range field is required.',
            'flyer_kg_range.*.numeric.*' => 'The flyer plus kg range  field must be numeric or decimal.',
            'flyer_wc.*.required' => 'The flyer within city field is required.',
            'flyer_wc.*.numeric.*' => 'The flyer within city field must be numeric or decimal.',
            'flyer_sz.*.required' => 'The flyer same zone field is required.',
            'flyer_sz.*.numeric.*' => 'The flyer same zone field must be numeric or decimal.',
            'flyer_dz.*.required' => 'The flyer different zone field is required.',
            'flyer_dz.*.numeric.*' => 'The flyer different zone field must be numeric or decimal.',

            'hdocs_range_up.*.required' => 'The hard docs range up field is required.',
            'hdocs_range_up.*.numeric.*' => 'The hard docs range up field must be numeric or decimal.',
            'hdocs_range_down.*.required' => 'The hard docs range down field is required.',
            'hdocs_range_down.*.numeric.*' => 'The hard docs range down field must be numeric or decimal.',
            'hdocs_kg_range.*.required' => 'The hard docs kg range field is required.',
            'hdocs_kg_range.*.numeric.*' => 'The hard docs plus kg range  field must be numeric or decimal.',
            'hdocs_wc.*.required' => 'The hard docs within city field is required.',
            'hdocs_wc.*.numeric.*' => 'The hard docs within city field must be numeric or decimal.',
            'hdocs_sz.*.required' => 'The hard docs same zone field is required.',
            'hdocs_sz.*.numeric.*' => 'The hard docs same zone field must be numeric or decimal.',
            'hdocs_dz.*.required' => 'The hard docs different zone field is required.',
            'hdocs_dz.*.numeric.*' => 'The hard docs different zone field must be numeric or decimal.',

            'trax_box_2_range_up.required' => 'The trax box for 2kg range up field is required.',
            'trax_box_2_range_up.numeric.*' => 'The trax box for 2kg range up field must be numeric or decimal.',
            'trax_box_2_range_down.required' => 'The trax box for 2kg range down field is required.',
            'trax_box_2_range_down.numeric.*' => 'The trax box for 2kg range down field must be numeric or decimal.',
            'trax_box_2_wc.required' => 'The trax box for 2kg within city field is required.',
            'trax_box_2_wc.numeric.*' => 'The trax box for 2kg within city field must be numeric or decimal.',
            'trax_box_2_sz.required' => 'The trax box for 2kg same zone field is required.',
            'trax_box_2_sz.numeric.*' => 'The trax box for 2kg same zone field must be numeric or decimal.',
            'trax_box_2_dz.required' => 'The trax box for 2kg different zone field is required.',
            'trax_box_2_dz.numeric.*' => 'The trax box for 2kg different zone field must be numeric or decimal.',

            'trax_box_5_range_up.required' => 'The trax box for 5kg range up field is required.',
            'trax_box_5_range_up.numeric.*' => 'The trax box for 5kg range up field must be numeric or decimal.',
            'trax_box_5_range_down.required' => 'The trax box for 5kg range down field is required.',
            'trax_box_5_range_down.numeric.*' => 'The trax box for 5kg range down field must be numeric or decimal.',
            'trax_box_5_wc.required' => 'The trax box for 5kg within city field is required.',
            'trax_box_5_wc.numeric.*' => 'The trax box for 5kg within city field must be numeric or decimal.',
            'trax_box_5_sz.required' => 'The trax box for 5kg same zone field is required.',
            'trax_box_5_sz.numeric.*' => 'The trax box for 5kg same zone field must be numeric or decimal.',
            'trax_box_5_dz.required' => 'The trax box for 5kg different zone field is required.',
            'trax_box_5_dz.numeric.*' => 'The trax box for 5kg different zone field must be numeric or decimal.',

            'trax_box_10_range_up.required' => 'The trax box for 10kg range up field is required.',
            'trax_box_10_range_up.numeric.*' => 'The trax box for 10kg range up field must be numeric or decimal.',
            'trax_box_10_range_down.required' => 'The trax box for 10kg range down field is required.',
            'trax_box_10_range_down.numeric.*' => 'The trax box for 10kg range down field must be numeric or decimal.',
            'trax_box_10_wc.required' => 'The trax box for 10kg within city field is required.',
            'trax_box_10_wc.numeric.*' => 'The trax box for 10kg within city field must be numeric or decimal.',
            'trax_box_10_sz.required' => 'The trax box for 10kg same zone field is required.',
            'trax_box_10_sz.numeric.*' => 'The trax box for 10kg same zone field must be numeric or decimal.',
            'trax_box_10_dz.required' => 'The trax box for 10kg different zone field is required.',
            'trax_box_10_dz.numeric.*' => 'The trax box for 10kg different zone field must be numeric or decimal.',

            'trax_box_15_range_up.required' => 'The trax box for 15kg range up field is required.',
            'trax_box_15_range_up.numeric.*' => 'The trax box for 15kg range up field must be numeric or decimal.',
            'trax_box_15_range_down.required' => 'The trax box for 15kg range down field is required.',
            'trax_box_15_range_down.numeric.*' => 'The trax box for 15kg range down field must be numeric or decimal.',
            'trax_box_15_wc.required' => 'The trax box for 15kg within city field is required.',
            'trax_box_15_wc.numeric.*' => 'The trax box for 15kg within city field must be numeric or decimal.',
            'trax_box_15_sz.required' => 'The trax box for 15kg same zone field is required.',
            'trax_box_15_sz.numeric.*' => 'The trax box for 15kg same zone field must be numeric or decimal.',
            'trax_box_15_dz.required' => 'The trax box for 15kg different zone field is required.',
            'trax_box_15_dz.numeric.*' => 'The trax box for 15kg different zone field must be numeric or decimal.',

            'trax_box_20_range_up.required' => 'The trax box for 20kg range up field is required.',
            'trax_box_20_range_up.numeric.*' => 'The trax box for 20kg range up field must be numeric or decimal.',
            'trax_box_20_range_down.required' => 'The trax box for 20kg range down field is required.',
            'trax_box_20_range_down.numeric.*' => 'The trax box for 20kg range down field must be numeric or decimal.',
            'trax_box_20_wc.required' => 'The trax box for 20kg within city field is required.',
            'trax_box_20_wc.numeric.*' => 'The trax box for 20kg within city field must be numeric or decimal.',
            'trax_box_20_sz.required' => 'The trax box for 20kg same zone field is required.',
            'trax_box_20_sz.numeric.*' => 'The trax box for 20kg same zone field must be numeric or decimal.',
            'trax_box_20_dz.required' => 'The trax box for 20kg different zone field is required.',
            'trax_box_20_dz.numeric.*' => 'The trax box for 20kg different zone field must be numeric or decimal.',

            'trax_box_30_range_up.required' => 'The trax box for 30kg range up field is required.',
            'trax_box_30_range_up.numeric.*' => 'The trax box for 30kg range up field must be numeric or decimal.',
            'trax_box_30_range_down.required' => 'The trax box for 30kg range down field is required.',
            'trax_box_30_range_down.numeric.*' => 'The trax box for 30kg range down field must be numeric or decimal.',
            'trax_box_30_wc.required' => 'The trax box for 30kg within city field is required.',
            'trax_box_30_wc.numeric.*' => 'The trax box for 30kg within city field must be numeric or decimal.',
            'trax_box_30_sz.required' => 'The trax box for 30kg same zone field is required.',
            'trax_box_30_sz.numeric.*' => 'The trax box for 30kg same zone field must be numeric or decimal.',
            'trax_box_30_dz.required' => 'The trax box for 30kg different zone field is required.',
            'trax_box_30_dz.numeric.*' => 'The trax box for 30kg different zone field must be numeric or decimal.',
        ];

        $validations = array();
        $saver_plus_validations = array();
        $rush_validations = array();
        $cod_validations = array();
        $swift_validations = array();
        $flyer_validations = array();
        $hdocs_validations = array();
        $trax_box_validations = array();


        $saver_plus_validations = [

            'saver_plus_range_up.*' => 'required|numeric',
            'saver_plus_range_down.*' => 'required|numeric',
            'saver_plus_kg_range.*' => 'required|numeric',
            'saver_plus_zone_a.*' => 'required|numeric',
            'saver_plus_zone_b.*' => 'required|numeric',
            'saver_plus_zone_c.*' => 'required|numeric',
            'saver_plus_zone_d.*' => 'required|numeric',

        ];

        $rush_validations = [

            'rush_range_up.*' => 'required|numeric',
            'rush_range_down.*' => 'required|numeric',
            'rush_kg_range.*' => 'required|numeric',
            'rush_wc.*' => 'required|numeric',
            'rush_sz.*' => 'required|numeric',
            'rush_dz.*' => 'required|numeric',

        ];

        $cod_validations = [

            'cod_range_up.*' => 'required|numeric',
            'cod_range_down.*' => 'required|numeric',
            'cod_kg_range.*' => 'required|numeric',
            'cod_wc.*' => 'required|numeric',
            'cod_sz.*' => 'required|numeric',
            'cod_dz.*' => 'required|numeric',

        ];

        $swift_validations = [

            'swift_range_up.*' => 'required|numeric',
            'swift_range_down.*' => 'required|numeric',
            'swift_kg_range.*' => 'required|numeric',
            'swift_wc.*' => 'required|numeric',
            'swift_sz.*' => 'required|numeric',
            'swift_dz.*' => 'required|numeric',

        ];

        $flyer_validations = [

            'flyer_range_up.*' => 'required|numeric',
            'flyer_range_down.*' => 'required|numeric',
            'flyer_kg_range.*' => 'required|numeric',
            'flyer_wc.*' => 'required|numeric',
            'flyer_sz.*' => 'required|numeric',
            'flyer_dz.*' => 'required|numeric',

        ];

        $hdocs_validations = [

            'hdocs_range_up.*' => 'required|numeric',
            'hdocs_range_down.*' => 'required|numeric',
            'hdocs_kg_range.*' => 'required|numeric',
            'hdocs_wc.*' => 'required|numeric',
            'hdocs_sz.*' => 'required|numeric',
            'hdocs_dz.*' => 'required|numeric',

        ];

        $trax_box_validations = [

            'trax_box_2kg_range_up' => 'required|numeric',
            'trax_box_2kg_range_down' => 'required|numeric',
            'trax_box_2kg_wc' => 'required|numeric',
            'trax_box_2kg_sz' => 'required|numeric',
            'trax_box_2kg_dz' => 'required|numeric',

            'trax_box_5kg_range_up' => 'required|numeric',
            'trax_box_5kg_range_down' => 'required|numeric',
            'trax_box_5kg_wc' => 'required|numeric',
            'trax_box_5kg_sz' => 'required|numeric',
            'trax_box_5kg_dz' => 'required|numeric',

            'trax_box_10kg_range_up' => 'required|numeric',
            'trax_box_10kg_range_down' => 'required|numeric',
            'trax_box_10kg_wc' => 'required|numeric',
            'trax_box_10kg_sz' => 'required|numeric',
            'trax_box_10kg_dz' => 'required|numeric',

            'trax_box_15kg_range_up' => 'required|numeric',
            'trax_box_15kg_range_down' => 'required|numeric',
            'trax_box_15kg_wc' => 'required|numeric',
            'trax_box_15kg_sz' => 'required|numeric',
            'trax_box_15kg_dz' => 'required|numeric',

            'trax_box_20kg_range_up' => 'required|numeric',
            'trax_box_20kg_range_down' => 'required|numeric',
            'trax_box_20kg_wc' => 'required|numeric',
            'trax_box_20kg_sz' => 'required|numeric',
            'trax_box_20kg_dz' => 'required|numeric',

            'trax_box_30kg_range_up' => 'required|numeric',
            'trax_box_30kg_range_down' => 'required|numeric',
            'trax_box_30kg_wc' => 'required|numeric',
            'trax_box_30kg_sz' => 'required|numeric',
            'trax_box_30kg_dz' => 'required|numeric',
        ];
        //}


        $validations = array_merge($saver_plus_validations, $rush_validations, $cod_validations, $swift_validations, $flyer_validations, $hdocs_validations, $trax_box_validations);

        $validate = Validator::make($request->all(), $validations, $messages);

        if ($validate->fails()) {
            return redirect()->back()
                ->withErrors($validate)
                ->withInput();
        }


        RetailStandardRates::where('shipping_mode_id', 1)->delete();

        foreach ($request->saver_plus_range_up as $index => $saver_plus_range_up) {

            $retail = new RetailStandardRates();
            $retail->range_up = $request->saver_plus_range_up[$index];
            $retail->range_down = $request->saver_plus_range_down[$index];
            $retail->shipping_mode_id = 1;
            if (isset($request->saver_plus_kg_range[$index])) {
                $retail->kg_range = $request->saver_plus_kg_range[$index];
                $retail->weight_addition = 1;
            } else {
                $retail->kg_range = 0;
                $retail->weight_addition = 0;
            }
            $retail->zone_a = $request->saver_plus_zone_a[$index];
            $retail->zone_b = $request->saver_plus_zone_b[$index];
            $retail->zone_c = $request->saver_plus_zone_c[$index];
            $retail->zone_d = $request->saver_plus_zone_d[$index];
            $retail->save();

        }

        RetailStandardRates::where('shipping_mode_id', 2)->delete();

        foreach ($request->rush_range_up as $index => $rush_range_up) {
            $retail = new RetailStandardRates();
            $retail->range_up = $request->rush_range_up[$index];
            $retail->range_down = $request->rush_range_down[$index];
            $retail->shipping_mode_id = 2;
            if (isset($request->rush_kg_range[$index])) {
                $retail->kg_range = $request->rush_kg_range[$index];
                $retail->weight_addition = 1;
            } else {
                $retail->kg_range = 0;
                $retail->weight_addition = 0;
            }
            $retail->within_city = $request->rush_wc[$index];
            $retail->same_zone = $request->rush_sz[$index];
            $retail->different_zone = $request->rush_dz[$index];
            $retail->save();
        }

        RetailStandardRates::where('shipping_mode_id', 3)->delete();

        foreach ($request->cod_range_up as $index => $cod_range_up) {
            $retail = new RetailStandardRates();
            $retail->range_up = $request->cod_range_up[$index];
            $retail->range_down = $request->cod_range_down[$index];
            $retail->shipping_mode_id = 3;
            if (isset($request->cod_kg_range[$index])) {
                $retail->kg_range = $request->cod_kg_range[$index];
                $retail->weight_addition = 1;
            } else {
                $retail->kg_range = 0;
                $retail->weight_addition = 0;
            }
            $retail->within_city = $request->cod_wc[$index];
            $retail->same_zone = $request->cod_sz[$index];
            $retail->different_zone = $request->cod_dz[$index];
            $retail->save();

        }

        RetailStandardRates::where('shipping_mode_id', 4)->delete();

        foreach ($request->swift_range_up as $index => $swift_range_up) {

            $retail = new RetailStandardRates();
            $retail->range_up = $request->swift_range_up[$index];
            $retail->range_down = $request->swift_range_down[$index];
            $retail->shipping_mode_id = 4;
            if (isset($request->swift_kg_range[$index])) {
                $retail->kg_range = $request->swift_kg_range[$index];
                $retail->weight_addition = 1;
            } else {
                $retail->kg_range = 0;
                $retail->weight_addition = 0;
            }
            $retail->within_city = $request->swift_wc[$index];
            $retail->same_zone = $request->swift_sz[$index];
            $retail->different_zone = $request->swift_dz[$index];
            $retail->save();
        }

        RetailStandardRates::where('shipping_mode_id', 5)->delete();


        foreach ($request->flyer_range_up as $index => $flyer_range_up) {

            $retail = new RetailStandardRates();
            $retail->range_up = $request->flyer_range_up[$index];
            $retail->range_down = $request->flyer_range_down[$index];
            $retail->shipping_mode_id = 5;
            if (isset($request->flyer_kg_range[$index])) {
                $retail->kg_range = $request->flyer_kg_range[$index];
                $retail->weight_addition = 1;
            } else {
                $retail->kg_range = 0;
                $retail->weight_addition = 0;
            }
            $retail->within_city = $request->flyer_wc[$index];
            $retail->same_zone = $request->flyer_sz[$index];
            $retail->different_zone = $request->flyer_dz[$index];
            $retail->save();
        }

        RetailStandardRates::where('shipping_mode_id', 6)->delete();

        foreach ($request->flyer_range_up as $index => $flyer_range_up) {

            $retail = new RetailStandardRates();
            $retail->range_up = $request->hdocs_range_up[$index];
            $retail->range_down = $request->hdocs_range_down[$index];
            $retail->shipping_mode_id = 6;
            if (isset($request->hdocs_kg_range[$index])) {
                $retail->kg_range = $request->hdocs_kg_range[$index];
                $retail->weight_addition = 1;
            } else {
                $retail->kg_range = 0;
                $retail->weight_addition = 0;
            }
            $retail->within_city = $request->hdocs_wc[$index];
            $retail->same_zone = $request->hdocs_sz[$index];
            $retail->different_zone = $request->hdocs_dz[$index];
            $retail->save();
        }

        RetailStandardRates::where('shipping_mode_id', 7)->delete();

        $now = Carbon::now();
        $data = [
            [
                'range_up' => $request->trax_box_2kg_range_up,
                'range_down' => $request->trax_box_2kg_range_down,
                'shipping_mode_id' => 7,
                'kg_range' => ($request->trax_box_2kg_weight) ? $request->trax_box_2_weight : 0,
                'weight_addition' => ($request->trax_box_2kg_switch == 'on') ? 1 : 0,
                'within_city' => $request->trax_box_2kg_wc,
                'same_zone' => $request->trax_box_2kg_sz,
                'different_zone' => $request->trax_box_2kg_dz,
                'trax_box_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'range_up' => $request->trax_box_5kg_range_up,
                'range_down' => $request->trax_box_5kg_range_down,
                'shipping_mode_id' => 7,
                'kg_range' => ($request->trax_box_5kg_weight) ? $request->trax_box_2_weight : 0,
                'weight_addition' => ($request->trax_box_5kg_switch == 'on') ? 1 : 0,
                'within_city' => $request->trax_box_5kg_wc,
                'same_zone' => $request->trax_box_5kg_sz,
                'different_zone' => $request->trax_box_5kg_dz,
                'trax_box_id' => 2,
                'created_at' => $now,
                'updated_at' => $now,

            ],
            [
                'range_up' => $request->trax_box_10kg_range_up,
                'range_down' => $request->trax_box_10kg_range_down,
                'shipping_mode_id' => 7,
                'kg_range' => ($request->trax_box_10kg_weight) ? $request->trax_box_2_weight : 0,
                'weight_addition' => ($request->trax_box_10kg_switch == 'on') ? 1 : 0,
                'within_city' => $request->trax_box_10kg_wc,
                'same_zone' => $request->trax_box_10kg_sz,
                'different_zone' => $request->trax_box_10kg_dz,
                'trax_box_id' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'range_up' => $request->trax_box_15kg_range_up,
                'range_down' => $request->trax_box_15kg_range_down,
                'shipping_mode_id' => 7,
                'kg_range' => ($request->trax_box_15kg_weight) ? $request->trax_box_2_weight : 0,
                'weight_addition' => ($request->trax_box_15kg_switch == 'on') ? 1 : 0,
                'within_city' => $request->trax_box_15kg_wc,
                'same_zone' => $request->trax_box_15kg_sz,
                'different_zone' => $request->trax_box_15kg_dz,
                'trax_box_id' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'range_up' => $request->trax_box_20kg_range_up,
                'range_down' => $request->trax_box_20kg_range_down,
                'shipping_mode_id' => 7,
                'kg_range' => ($request->trax_box_20kg_weight) ? $request->trax_box_2_weight : 0,
                'weight_addition' => ($request->trax_box_20kg_switch == 'on') ? 1 : 0,
                'within_city' => $request->trax_box_20kg_wc,
                'same_zone' => $request->trax_box_20kg_sz,
                'different_zone' => $request->trax_box_20kg_dz,
                'trax_box_id' => 5,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'range_up' => $request->trax_box_30kg_range_up,
                'range_down' => $request->trax_box_30kg_range_down,
                'shipping_mode_id' => 7,
                'kg_range' => ($request->trax_box_30kg_weight) ? $request->trax_box_2_weight : 0,
                'weight_addition' => ($request->trax_box_30kg_switch == 'on') ? 1 : 0,
                'within_city' => $request->trax_box_30kg_wc,
                'same_zone' => $request->trax_box_30kg_sz,
                'different_zone' => $request->trax_box_30kg_dz,
                'trax_box_id' => 6,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ];
        RetailStandardRates::insert($data);

        return redirect()->route('admin.retail.rates.edit')->with('success', 'Rates Updated');


    }
}
