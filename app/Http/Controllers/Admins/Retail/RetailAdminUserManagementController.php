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
use App\Http\Models\Product;
use App\Http\Models\RetailFranchiseProductPercentage;
use App\Http\Models\Admin\Retail\RetailShippingMode;
use Illuminate\Support\Facades\Storage;
use App\Http\Models\RetailFranchiseProductAttachment;
use App\Http\Models\RetailFranchiseCharge;
use App\Http\Models\RetailFranchiseCommission;
use App\Http\Models\RetailUserCommission;
use App\Http\Models\Admin\Retail\RetailShipment;
use Illuminate\Support\Facades\DB;
use App\Http\Models\TraxCenterAttachment;
use App\Http\Models\RetailUserFamilyInformation;
use App\Http\Models\RetailUserProductPercentage;
use App\Http\Models\RetailUserAttachment;
use App\Http\Models\RetailUserHistory;
use App\Http\Models\TotalSumFranchiseCommission;
use App\Http\Models\TotalSumRetailTraxCenter;
use App\Http\Models\BanksList;
use App\Http\Models\Admin\AdminRole;
use App\Http\Models\Admin\AdminDepartment;
use App\Http\Models\Admin\Admin;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\RetailLog;

class RetailAdminUserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin')->except('cancel');
        $this->middleware('Permission');
    }

    public static function add_user($name, $password, $phone_number, $hub, $cnic, $address, $category, $category_id, $familyMemberNames, $traxId, $retailShippingModeNames, $productPercentages, $attachment_1, $attachment_2, $attachment_3, $attachment_4, $attachment_5, $file_1, $file_2, $file_3, $file_4, $file_5, $joining_date)
    {
        $user = new RetailUser();
        $user->trax_id = $traxId;
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

        // retail user histroy
        if ($category == 2){
            $trax_center = RetailTraxCenter::where('id', $category_id)->first();
            if ($trax_center){
                $data = [
                    'retail_user_id' => $user->id,
                    'trax_center_id' => $category_id,
                    'trax_center_name' => $trax_center->name,
                    'trax_center_code' => $trax_center->code,
                    'joining_date' => $joining_date,
                ];
                RetailUserHistory::create($data);
            }
        }

        // retail user commission
        $retailShippingModeNames = is_array($retailShippingModeNames) ? $retailShippingModeNames : [];
        $productPercentages = is_array($productPercentages) ? $productPercentages : [];
        $retailShippingModes = RetailShippingMode::whereIn('name', $retailShippingModeNames)->get();
        $matchingRetailShippingModeIds = $retailShippingModes->pluck('id')->toArray();

        foreach ($retailShippingModeNames as $key => $retailShippingModeName){
            $retailShippingModeName = ($retailShippingModeName !== null) ? $retailShippingModeName : null;
            $productPercentage = ($productPercentages[$key] !== null) ? $productPercentages[$key] : null;
            $retailShippingModeId = $matchingRetailShippingModeIds[$key] ?? null;

            $retail_user_product_percentage = new RetailUserProductPercentage();
            $retail_user_product_percentage->retail_user_id = $user->id;
            $retail_user_product_percentage->retail_shipping_mode_id = $retailShippingModeId;
            $retail_user_product_percentage->product_percentage = $productPercentage;
            $retail_user_product_percentage->created_by = Auth::id();
            $retail_user_product_percentage->save();
        }

        // retail user family info
        foreach ($familyMemberNames as $key => $familyMemberName) {
            $family_member_type = null;
            if ($key === 0) {
                $family_member_type = 3; // Father
            } elseif ($key === 1) {
                $family_member_type = 4; // Mother
            } elseif ($key === 2) {
                $family_member_type = 1; // Spouse
            } 
            elseif ($key == 3) {
                $family_member_type = 5; // Spouse DOB
            } else {
                $family_member_type = 2; // children
            }
            $retail_user_family_information = new RetailUserFamilyInformation();
            $retail_user_family_information->retail_user_id = $user->id;
            $retail_user_family_information->family_member_name = $familyMemberName;
            $retail_user_family_information->family_member_type = $family_member_type;
            $retail_user_family_information->agreement_start_date = $joining_date;
            $retail_user_family_information->save();
        }

        $retail_user_attachment = new RetailUserAttachment();
        $retail_user_attachment->retail_user_id = $user->id;
        $baseDirectory = 'retail_user_attachments';

        if (!Storage::disk('public')->exists($baseDirectory)) {
            Storage::disk('public')->makeDirectory($baseDirectory);
        }
        for ($i = 1; $i <= 5; $i++) {
            $file = ${"file_" . $i};
            if ($file) {
                $filename = 'attachment_' . $i . '_' . Carbon::now()->format('YmdHis') . '.' . $file->getClientOriginalExtension();
                $attachmentDirectory = $baseDirectory . '/attachment_' . $i;
                if (!Storage::disk('public')->exists($attachmentDirectory)) {
                    Storage::disk('public')->makeDirectory($attachmentDirectory);
                }
                Storage::disk('public')->putFileAs($attachmentDirectory, $file, $filename);                
                $retail_user_attachment->{'attachment_' . $i} = $attachmentDirectory . '/' . $filename;
            }
        }
        $retail_user_attachment->save();

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
        $products = Product::orderBy('product_name')->get();
        $product_percentage = RetailFranchiseProductPercentage::get();
        // $shipping_modes = RetailShippingMode::where('business_category_id',1)->get();
        $shipping_modes = RetailShippingMode::get();
        $bank_list = BanksList::get();
        return view('admin.retail.franchise.index')->with(['hubs' => $hubs, 'products' => $products, 'product_percentage' => $product_percentage, 'shipping_modes' => $shipping_modes, 'bank_list' => $bank_list]);
    }

    public function franchise_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 378);
        }

        $franchise = RetailFranchise::join('admins as a', 'a.id', '=', 'retail_franchises.updated_by')
            ->join('cities as c', 'c.id', '=', 'retail_franchises.default_hub')
            ->select('retail_franchises.id', 'retail_franchises.name', 'retail_franchises.phone_no', 'retail_franchises.email', 'retail_franchises.cnic', 'c.name as default_hub', 'c.id as default_hub_id', 'a.name as updated_by', 'retail_franchises.status', 'retail_franchises.code', 'retail_franchises.location_latitude', 'retail_franchises.location_longitude', 'retail_franchises.created_at as created', 'retail_franchises.updated_at as updated', 'retail_franchises.discount','retail_franchises.insurance');

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
                    
                    // if ($data->discount != Null) {
                    //     $dropdown .= '<button type="button" class="dropdown-item edit_discount"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Edit Discount</div></button>';
                    // } else {
                    //     $dropdown .= '<button type="button" class="dropdown-item add_discount"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Add Discount</div></button>';
                    // }
                    
                    $dropdown .= '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Edit</div></button>';
            
                    // Adding Excel export dropdown item
                    $dropdown .= '
                            <a href="' . route("admin.retail.franchise.franchise_details_excel_sheet", ["id" => $data->id]) . '" class="text-dark">
                                <div class="row no-gutters align-items-center ml-2">
                                    <div class="col-2">
                                        <i class="la la-file-excel-o"></i>
                                    </div>
                                    <div class="col-9" style="margin: 6px 0px 9px 5px;">
                                        Excel
                                    </div>
                                </div>
                            </a>
                            ';
                            $dropdown  .= '<button type="button" class="dropdown-item view_logs">
                            <div class="row no-gutters align-items-center">
                                <div class="col-2">
                                    <i class="ft-edit"></i>
                                </div>
                                <div class="col-9 offset-1">
                                    View Logs
                                </div>
                            </div>
                        </button>';
            
                    $dropdown .= '</div></div>';
                    
                    return $dropdown;
                } else {
                    return '';
                }
            })->rawColumns(['action','location']);
            
        return $datatables->make(true);
    }

    public function franchise_enable_disable(Request $request)
    {
        $franchise = RetailFranchise::find($request->id);
        if ($request->status == 1) {
            $franchise->status = 1;
            $franchise->updated_by = Auth::id();
            $changedFields[] = 'Franchise Status' . ': ' . ($franchise->getOriginal('status') == 1 ? 'Active' : 'In-Active') . ' -> ' . ($franchise->status == 1  ? 'Active' : 'In-Active');
            self::retail_logs(Auth::id(), $changedFields, $franchise->id, $screen_name = 'Retail Franchise');
            $franchise->save();

            return response()->json(['status' => 1, 'success' => 'Franchise Enabled Successfully']);
        } elseif ($request->status == 0) {
            $franchise->status = 0;
            $franchise->updated_by = Auth::id();
            $changedFields[] = 'Franchise Status' . ': ' . ($franchise->getOriginal('status') == 1 ? 'Active' : 'In-Active') . ' -> ' . ($franchise->status == 1  ? 'Active' : 'In-Active');
            self::retail_logs(Auth::id(), $changedFields, $franchise->id, $screen_name = 'Retail Franchise');

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
        $request->validate([
            'attachment_1' => 'required|mimes:jpeg,png,jpg,pdf,doc,docx|max:2048',
            // 'franchise_deduction' => 'required|numeric',
            'franchise_withholding' => 'required|numeric',
            'name' => 'required',
            'phone_number' => 'required',
            'email' => 'required',
            'cnic' => 'required',
            'hub' => 'required',
            'lat' => 'required',
            'long' => 'required',
            'insurance' => 'required',
            'retail_shipping_mode_id' => 'required',
            'security_deposit' => 'required',
            'license_fees' => 'required',
            'bank_id' => 'required',
            'security_cheque_number' => 'required',
            'license_cheque_number' => 'required',
        ]);
        
        $admin = $request->user();
        $date = Carbon::now()->format('Y_m_d');
        $hub_count = RetailFranchise::where('default_hub', $request->hub)->count() + 1;
        $cnic_active_status = $request->cnic_status == "on" ? 1 : 0;

        $franchise = new RetailFranchise();
        $franchise->name = $request->name;
        $franchise->phone_no = $request->phone_number;
        $franchise->email = $request->email;
        $franchise->cnic = $request->cnic;
        $franchise->cnic_status = $cnic_active_status;
        $franchise->default_hub = $request->hub;
        $franchise->location_latitude = $request->lat;
        $franchise->location_longitude = $request->long;
        $franchise->discount = $request->discount;
        $franchise->updated_by = Auth::id();
        $franchise->insurance = $request->insurance;
        $franchise->status = 1;
        $franchise->save();

        $hub_name = City::find($request->hub)->name;
        $hub_code = substr($hub_name, 0, 3);
        $hub_code = strtoupper($hub_code);
        $code = 'FR-' . $hub_code . '-' . str_pad($hub_count, 3, 0, STR_PAD_LEFT);

        $franchise->code = $code;
        $franchise->save();

        $retailShippingModeNames = json_decode($request->retail_shipping_mode_id, true);
        $productPercentages = json_decode($request->product_percentage, true);
        
        $retailShippingModeNames = is_array($retailShippingModeNames) ? $retailShippingModeNames : [];
        $productPercentages = is_array($productPercentages) ? $productPercentages : [];
        
        // Fetch retail shipping modes matching the names
        $retailShippingModes = RetailShippingMode::whereIn('name', $retailShippingModeNames)->get();
        
        // Store the IDs of matching retail shipping modes in an array
        $matchingRetailShippingModeIds = $retailShippingModes->pluck('id')->toArray();
        
        foreach ($retailShippingModeNames as $key => $retailShippingModeName) {
            // Handle null values in the arrays
            $retailShippingModeName = ($retailShippingModeName !== null) ? $retailShippingModeName : null;
            $productPercentage = ($productPercentages[$key] !== null) ? $productPercentages[$key] : null;
        
            // Retrieve the corresponding retail shipping mode ID from the array
            $retailShippingModeId = $matchingRetailShippingModeIds[$key] ?? null;
            $franchiseRetailProduct = new RetailFranchiseProductPercentage();
            $franchiseRetailProduct->franchise_id = $franchise->id;
            $franchiseRetailProduct->retail_shipping_mode_id = $retailShippingModeId;
            $franchiseRetailProduct->product_percentage = $productPercentage;
            $franchiseRetailProduct->created_by = $admin->id;
            $franchiseRetailProduct->save();
        }

        $franchise_product_charges = new RetailFranchiseCharge();
        $franchise_product_charges->franchise_id = $franchise->id;
        $franchise_product_charges->franchise_gst = $request->franchise_gst;
        $franchise_product_charges->franchise_withholding = $request->franchise_withholding;
        $franchise_product_charges->franchise_deduction = $request->franchise_deduction;
        $franchise_product_charges->security_deposit = $request->security_deposit;
        $franchise_product_charges->license_fees = $request->license_fees;
        $franchise_product_charges->bank_id = $request->bank_id;
        $bank_name = BanksList::where('id', $request->bank_id)->first()->name;
        $franchise_product_charges->bank_name = $bank_name;
        $franchise_product_charges->security_cheque_number = $request->security_cheque_number;
        $franchise_product_charges->license_cheque_number = $request->license_cheque_number;
        $franchise_product_charges->save();

        $franchise_retail_product_attachment = new RetailFranchiseProductAttachment();
        $franchise_retail_product_attachment->franchise_id = $franchise->id;
        if ($request->hasFile('attachment_1')) {
            $file = $request->file('attachment_1');
            $filename = 'attachment_1_' . $date . '_' . Carbon::now()->format('His') . $file->getClientOriginalExtension();
            Storage::disk('public')->putFileAs('franchise_product_attachment_1/' . Carbon::now()->format('His') . '_', $file, $filename);
            $franchise_retail_product_attachment->attachment_1 = $filename;
        } 
        if ($request->hasFile('attachment_2')) {
            $file = $request->file('attachment_2');
            $filename = 'attachment_2_' . $date . '_' . Carbon::now()->format('His') . $file->getClientOriginalExtension();
            Storage::disk('public')->putFileAs('franchise_product_attachment_2/' . Carbon::now()->format('His') . '_', $file, $filename);
            $franchise_retail_product_attachment->attachment_2 = $filename;
        }
        if ($request->hasFile('attachment_3')) {
            $file = $request->file('attachment_3');
            $filename = 'attachment_3_' . $date . '_' . Carbon::now()->format('His') . $file->getClientOriginalExtension();
            Storage::disk('public')->putFileAs('franchise_product_attachment_3/' . Carbon::now()->format('His') . '_', $file, $filename);
            $franchise_retail_product_attachment->attachment_3 = $filename;
        }
        if ($request->hasFile('attachment_4')) {
            $file = $request->file('attachment_4');
            $filename = 'attachment_4_' . $date . '_' . Carbon::now()->format('His') . $file->getClientOriginalExtension();
            Storage::disk('public')->putFileAs('franchise_product_attachment_4/' . Carbon::now()->format('His') . '_', $file, $filename);
            $franchise_retail_product_attachment->attachment_4 = $filename;
        }
        if ($request->hasFile('attachment_5')) {
            $file = $request->file('attachment_5');
            $filename = 'attachment_5_' . $date . '_' . Carbon::now()->format('His') . $file->getClientOriginalExtension();
            Storage::disk('public')->putFileAs('franchise_product_attachment_5/' . Carbon::now()->format('His') . '_', $file, $filename);
            $franchise_retail_product_attachment->attachment_5 = $filename;
        }
        $franchise_retail_product_attachment->save();

        $setting = GlobalSettings::where('type', 'retail_store')->first();
        $shipper_user_id = $setting->setting_value;

        $pickup_address_id = $this->add_pickup_address($shipper_user_id, $franchise->name . ' - ' . $hub_name, $franchise->name, $franchise->phone_no, $franchise->email, $franchise->default_hub, 0, $franchise->location_latitude, $franchise->location_longitude);

        $franchise->pickup_address_id = $pickup_address_id;
        $franchise->save();
        return redirect()->back()->with('success', 'Franchise Added Successfully!');
    }

    public function franchise_edit(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'franchise_deduction' => 'required|numeric',
            'franchise_withholding' => 'required|numeric',
            'name' => 'required',
            'phone_number' => 'required',
            'email' => 'required',
            'cnic' => 'required',
            'lat' => 'required',
            'long' => 'required',
            'edit_insurance' => 'required',
            'retail_shipping_mode_id' => 'required',
            'security_deposit' => 'required',
            'license_fees' => 'required',
            'bank_id' => 'required',
            'security_cheque_number' => 'required',
            'license_cheque_number' => 'required',
        ]);
        $date = Carbon::now()->format('Y_m_d');
        $admin = $request->user();
        $id = $request->franchise_id;
        $existing_franchise = RetailUser::where('name', $request->name)->where('category_id', '!=', $id);
        if (!$existing_franchise->exists()) {
            $cnic_active_status = $request->cnic_status == "on" ? 1 : 0;
            $franchise = RetailFranchise::find($request->franchise_id);
            $franchise->name = $request->name;
            $franchise->phone_no = $request->phone_number;
            $franchise->email = $request->email;
            $franchise->cnic = $request->cnic;
            $franchise->cnic_status = $cnic_active_status;
            $franchise->location_latitude = $request->lat;
            $franchise->location_longitude = $request->long;
            $franchise->discount = $request->discount;
            $franchise->insurance = $request->edit_insurance;
            $franchise->updated_by = Auth::id();
            

            $changedFields = [];
            $fieldNames = [
              'cnic' => 'CNIC Status',
              'name' => 'Name',
              'phone_no' => 'Phone no',
              'cnic' => 'CNIC',
              'email' => 'Email',
              'location_latitude' => 'Location Latitude',
              'location_longitude' => 'Location Longitude',
              'discount' => 'Discount',
              'insurance' => 'Insurance',
              'cnic_status' => 'CNIC Status'
            ];
    
            foreach ($fieldNames as $field => $fieldName) {
                $originalValue = trim($franchise->getOriginal($field));
                $currentValue = trim($franchise->$field);
                if ($franchise->isDirty($field) && $originalValue !== $currentValue) {
                    if($field == 'cnic_status') {
                        $changedFields[] = $fieldName . ': ' . ($originalValue == 1 ? 'Enable' : 'Disable') . ' -> ' . ($currentValue == 1 ? 'Enable' : 'Disable');
                    } else {
                        $changedFields[] = $fieldName . ': ' . $originalValue . ' -> ' . $currentValue;
                    }
                }
            }

            $franchise->save();

            $existingPercentages = RetailFranchiseProductPercentage::where('franchise_id', $franchise->id)->get()->keyBy('retail_shipping_mode_id');

            $new_names = json_decode($request->input('retail_shipping_mode_id'));
            $new_percentages = json_decode($request->input('product_percentage'));
            
            $new_ids = [];
            foreach ($new_names as $name) {
                $mode = RetailShippingMode::where('name', $name)->first();
                if ($mode) {
                    $new_ids[] = $mode->id;
                }
            }
            
            foreach ($existingPercentages as $old_id => $old) {
                $modeName = RetailShippingMode::find($old_id)->name;
                
                if (!in_array($old_id, $new_ids)) {
                    $changedFields[] = "{$modeName}: {$old->product_percentage}% -> removed";
                } else {                    
                    $index = array_search($old_id, $new_ids);
                    $new_percentage = $new_percentages[$index];
                    if ($old->product_percentage != $new_percentage) {
                        $changedFields[] = "{$modeName}: {$old->product_percentage}% -> {$new_percentage}%";
                    }
                }
            }

            RetailFranchiseProductPercentage::where('franchise_id', $franchise->id)->delete();

            foreach ($new_ids as $key => $id) {
                RetailFranchiseProductPercentage::create([
                    'franchise_id' => $franchise->id,
                    'retail_shipping_mode_id' => $id,
                    'product_percentage' => $new_percentages[$key],
                    'updated_by' => $admin->id,
                ]);
            }
            // $retail_franchise_product_percentage = RetailFranchiseProductPercentage::where('franchise_id', $franchise->id)->get();
            // if ($retail_franchise_product_percentage->isNotEmpty()) {
            //     RetailFranchiseProductPercentage::where('franchise_id', $franchise->id)->delete();
            // }
            // $new_retail_shipping_mode_names = json_decode($request->input('retail_shipping_mode_id'));
            // $new_product_percentages = json_decode($request->input('product_percentage'));

            // $new_retail_shipping_mode_ids = [];
            // foreach ($new_retail_shipping_mode_names as $name) {
            //     $retailShippingMode = RetailShippingMode::where('name', $name)->first();
            //     if ($retailShippingMode) {
            //         $new_retail_shipping_mode_ids[] = $retailShippingMode->id;
            //     }
            // }

            // // Insert or update data
            // foreach ($new_retail_shipping_mode_ids as $key => $retail_shipping_mode_id) {
            //     $retail_franchise_product_percentage = new RetailFranchiseProductPercentage();
            //     $retail_franchise_product_percentage->franchise_id = $franchise->id;
            //     $retail_franchise_product_percentage->retail_shipping_mode_id = $retail_shipping_mode_id;
            //     $retail_franchise_product_percentage->product_percentage = $new_product_percentages[$key];
            //     $retail_franchise_product_percentage->updated_by = $admin->id;
            //     $retail_franchise_product_percentage->save();
            // }

            //areeb old code
            // $franchise_retail_product_charges = RetailFranchiseCharge::where('franchise_id', $franchise->id)->first();
            // if ($franchise_retail_product_charges != null){
            //     $franchise_retail_product_charges->delete();
            //     $new_charges = new RetailFranchiseCharge();
            //     $new_charges->franchise_id = $franchise->id;
            //     $new_charges->franchise_gst = $request->franchise_gst;
            //     $new_charges->franchise_withholding = $request->franchise_withholding;
            //     $new_charges->franchise_deduction = $request->franchise_deduction;
            //     $new_charges->security_deposit = $request->security_deposit;
            //     $new_charges->license_fees = $request->license_fees;
            //     $new_charges->bank_id = $request->bank_id;
            //     $bank_name = BanksList::where('id', $request->bank_id)->first()->name;
            //     $new_charges->bank_name = $bank_name;
            //     $new_charges->security_cheque_number = $request->security_cheque_number;
            //     $new_charges->license_cheque_number = $request->license_cheque_number;
            //     $new_charges->save();
            // } else {
            //     $new_charges = new RetailFranchiseCharge();
            //     $new_charges->franchise_id = $franchise->id;
            //     $new_charges->franchise_gst = $request->franchise_gst;
            //     $new_charges->franchise_withholding = $request->franchise_withholding;
            //     $new_charges->franchise_deduction = $request->franchise_deduction;
            //     $new_charges->security_deposit = $request->security_deposit;
            //     $new_charges->license_fees = $request->license_fees;
            //     $new_charges->bank_id = $request->bank_id;
            //     $bank_name = BanksList::where('id', $request->bank_id)->first()->name;
            //     $new_charges->bank_name = $bank_name;
            //     $new_charges->security_cheque_number = $request->security_cheque_number;
            //     $new_charges->license_cheque_number = $request->license_cheque_number;
            //     $new_charges->save();
            // }

            $franchise_retail_product_charges = RetailFranchiseCharge::where('franchise_id', $franchise->id)->first();

            if ($franchise_retail_product_charges) {
                // Update existing record
                $franchise_retail_product_charges->franchise_id = $franchise->id;
                $franchise_retail_product_charges->franchise_gst = $request->franchise_gst;
                $franchise_retail_product_charges->franchise_withholding = $request->franchise_withholding;
                $franchise_retail_product_charges->franchise_deduction = $request->franchise_deduction;
                $franchise_retail_product_charges->security_deposit = $request->security_deposit;
                $franchise_retail_product_charges->license_fees = $request->license_fees;
                $franchise_retail_product_charges->bank_id = $request->bank_id;
                $bank = BanksList::find($request->bank_id);
                $franchise_retail_product_charges->bank_name = $bank ? $bank->name : null;
                $franchise_retail_product_charges->security_cheque_number = $request->security_cheque_number;
                $franchise_retail_product_charges->license_cheque_number = $request->license_cheque_number;
                
                $fieldNames = [
                    'franchise_gst' => 'Franchise GST',
                    'franchise_withholding' => 'Franchise Withholding',
                    'franchise_deduction' => 'Franchise Deduction',
                    'security_deposit' => 'Security Deposit',
                    'license_fees' => 'License Fees',
                    'bank_name' => 'Bank',
                    'security_cheque_number' => 'Security Cheque No',
                    'license_cheque_number' => 'License Cheque No'
                ];

                foreach ($fieldNames as $field => $fieldName) {
                    $originalValue = trim($franchise_retail_product_charges->getOriginal($field));
                    $currentValue = trim($franchise_retail_product_charges->$field);
                    if ($franchise_retail_product_charges->isDirty($field) && $originalValue !== $currentValue) {
                        $changedFields[] = $fieldName . ': ' . $originalValue . ' -> ' . $currentValue;
                    }
                }
                $franchise_retail_product_charges->save();
            } else {
                // Create new record
                $new_charges = new RetailFranchiseCharge();
                $new_charges->franchise_id = $franchise->id;
                $new_charges->franchise_gst = $request->franchise_gst;
                $new_charges->franchise_withholding = $request->franchise_withholding;
                $new_charges->franchise_deduction = $request->franchise_deduction;
                $new_charges->security_deposit = $request->security_deposit;
                $new_charges->license_fees = $request->license_fees;
                $new_charges->bank_id = $request->bank_id;
                $bank = BanksList::find($request->bank_id);
                $new_charges->bank_name = $bank ? $bank->name : null;
                $new_charges->security_cheque_number = $request->security_cheque_number;
                $new_charges->license_cheque_number = $request->license_cheque_number;
                $new_charges->save();
            }

            $franchise_retail_product_attachment_edit = RetailFranchiseProductAttachment::where('franchise_id', $franchise->id)->first();
            if ($franchise_retail_product_attachment_edit != null) {
                // Loop through each attachment
                for ($i = 1; $i <= 5; $i++) {
                    $attachment_name = 'attachment_' . $i;
                    if ($request->hasFile($attachment_name)) {
                        // Delete old attachment if it exists
                        $old_attachment = $franchise_retail_product_attachment_edit->$attachment_name;
                        if ($old_attachment) {
                            $terms = [
                                '1' => 'Franchise Agreement',
                                '2' => 'Cheque Images',
                                '3' => 'Location Images',
                                '4' => 'Miscellaneous',
                                '5' => 'Attachment 5'
                            ];
                            $changedFields[] = $terms[$i] . ' ' . 'Changed at' . ' ' .  now()->toDateTimeString();
                            Storage::disk('public')->delete('franchise_product_attachment_' . $i . '/' . $old_attachment);
                        }
                        // Store new attachment
                        $file = $request->file($attachment_name);
                        $filename = 'attachment_' . $i . '_' . $date . '_' . Carbon::now()->format('His') . '.' . $file->getClientOriginalExtension();
                        Storage::disk('public')->putFileAs('franchise_product_attachment_' . $i, $file, $filename);
                        // Update attachment field in the database
                        $franchise_retail_product_attachment_edit->$attachment_name = $filename;
                    }
                }
            } else {
                // Create a new record if none exists
                $franchise_retail_product_attachment_edit = new RetailFranchiseProductAttachment();
                $franchise_retail_product_attachment_edit->franchise_id = $franchise->id;
                // Loop through each attachment
                for ($i = 1; $i <= 5; $i++) {
                    $attachment_name = 'attachment_' . $i;
                    if ($request->hasFile($attachment_name)) {
                        // Store new attachment
                        $file = $request->file($attachment_name);
                        $filename = 'attachment_' . $i . '_' . $date . '_' . Carbon::now()->format('His') . '.' . $file->getClientOriginalExtension();
                        Storage::disk('public')->putFileAs('franchise_product_attachment_' . $i, $file, $filename);
                        // Update attachment field in the database
                        $franchise_retail_product_attachment_edit->$attachment_name = $filename;
                    }
                }
            }
            $franchise_retail_product_attachment_edit->updated_by = $admin->id;
            $franchise_retail_product_attachment_edit->save();
            if(!empty($changedFields)) {
                self::retail_logs(Auth::id(), $changedFields, $franchise->id, $screen_name = 'Retail Franchise');
            }
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

    public function retail_product_percentage(Request $request){
        $franchiseId = $request->franchise_id;
        $retail_franchise_product_percentage = RetailFranchiseProductPercentage::where('franchise_id', $franchiseId)->get();
        $data = [];
        foreach ($retail_franchise_product_percentage as $percentage) {
            $selectedOption = RetailShippingMode::find($percentage->retail_shipping_mode_id)->name;
            $data[] = [
                'selected_option' => $selectedOption,
                'product_percentage' => $percentage->product_percentage,
            ];
        }
        return response()->json(['data' => $data]);
    }

    public function retail_product_charges(Request $request){
        $franchiseId = $request->franchise_id;
        $retail_franchise_product_percentage = RetailFranchiseCharge::where('franchise_id', $franchiseId)->first();
        
        if ($retail_franchise_product_percentage != null){
            $data = $retail_franchise_product_percentage;
        } else {
            $data = null;
        }
        
        return response()->json(['data' => $data]);
    }

    public function retail_product_attachments(Request $request){
        $franchiseId = $request->franchise_id;
        $retail_franchise_product_attachment = RetailFranchiseProductAttachment::where('franchise_id', $franchiseId)->first();
        if ($retail_franchise_product_attachment != null){
            $data = $retail_franchise_product_attachment;
        } else {
            $data = null;
        }
        
        return response()->json(['data' => $data]);
    }

    public function franchise_commission_view(){
        // $franchises = RetailUser::where('category', 1)->get();
        $franchises = RetailFranchise::get();
        $finance_department = AdminDepartment::where('id', 4)->first();
        $admin_roles = AdminRole::where('department_id', $finance_department->id)->get();
        $allowed_users = Admin::whereIn('role_id', $admin_roles->pluck('id'))->get();
        return view('admin.retail.commission.franchise_wise', [
            'franchises' => $franchises,
            'allowed_users' => $allowed_users
        ]);
    }



    public function user_commission_invoice_print(Request $request)
    {
        $trax_user = $request->trax_users;
        $data = explode(', ', $trax_user);
        $retail_commissions = RetailUserCommission::whereIn('id', $data)->get();
    
        $html = '';
        $html .= '<!doctype html>';
        $html .= '<html lang="en">';
        $html .= '<head>';
        $html .= '<meta charset="utf-8">';
        $html .= '<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">';
        $html .= '<title>Invoice</title>';
        
        $html .= '<style>';
        $html .= file_get_contents(public_path('app-assets/css/bootstrap.min.css'));
        $html .= '</style>';
        
        $html .= '<style>';
        $html .= '@page{size:A4 portrait; margin-top: 12rem; margin-bottom: 2rem; margin-left: 0rem; margin-right: 0rem;}*{-webkit-print-color-adjust:exact!important;color-adjust:exact!important}body{background:none!important;color:#09262e!important;font-size:0.7rem!important}hr{border-top:1px dashed #000}table.table-bordered{page-break-inside:avoid}table.table-bordered thead tr th, table.table-bordered tbody tr td{border:1px solid #09262e!important}.color.primary{background:#c8c8c8!important}.color.secondary{background:#ebebeb!important}.border{border:1px solid #09262e!important}.summary{page-break-inside:avoid}.shipments_summary{page-break-before:always}';
        $html .= '</style>';
        
        $html .= '<style>';
        $html .= '@page{margin-top: 1rem; margin-bottom: 1rem;}.summary_header .header{width: 10%;}.summary_header .heading{width: 15%;}.summary_footer .footer{width: 75%;}';
        $html .= '</style>';
        
        $html .= '</head>';
        $html .= '<body style="padding:98px;">';
    
        $grouped_data = [];
        foreach ($retail_commissions as $record) {
            $grouped_data[$record->retail_user_name][] = $record;
        }
    
        foreach ($grouped_data as $franchise_name => $records) {
            $monthNumber = $records[0]->month;
            $monthNames = [
                '01' => 'January',
                '02' => 'February',
                '03' => 'March',
                '04' => 'April',
                '05' => 'May',
                '06' => 'June',
                '07' => 'July',
                '08' => 'August',
                '09' => 'September',
                '10' => 'October',
                '11' => 'November',
                '12' => 'December',
            ];
            $monthName = isset($monthNames[$monthNumber]) ? $monthNames[$monthNumber] : '';

            // Start the main container for a franchise
            $html .= '<div class="row align-items-start justify-content-between summary my-4">';
            $html .= '<div class="col-12">';
            $html .= '<table class="table table-sm table-bordered border">';
            $html .= '<tbody>';

            $html .= '<tr>';
            $html .= '<td class="text-center align-middle"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto"></td>';
            $html .= '<td class="text-center align-middle color primary"><strong>User Details</strong></td>';
            $html .= '<td class="text-center align-middle color secondary">Created at ' . $records[0]->created_at . '</br> by ' . ucfirst(Auth::user()->name) . '</td>';
            $html .= '<td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>';
            $html .= '</tr>';
            
            // Franchise details
            $html .= '<tr><td>Retail User Name:</td><td>' . $franchise_name . '</td></tr>';
            $html .= '<tr><td>Address:</td><td>' . $records[0]->franchise_address . '</td></tr>';
            $html .= '<tr><td>Code:</td><td>' . $records[0]->franchise_code . '</td></tr>';
            $html .= '<tr><td>CNIC:</td><td>' . $records[0]->trax_center_cnic . '</td></tr>';
            $html .= '<tr><td>Phone #:</td><td>' . $records[0]->trax_center_phone . '</td></tr>';
            $html .= '<tr><td><strong>Payment Month:</strong></td><td>' . $monthName . '</td></tr>';
            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';
            $html .= '</div>';
        
            // Start the table for product details
            $html .= '<div class="row align-items-start justify-content-between summary">';
            $html .= '<div class="col-12">';
            $html .= '<table class="table table-sm table-bordered border">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th class="color primary">Product</th>';
            $html .= '<th class="color primary">Approved Percentage (Commission)</th>';
            $html .= '<th class="color primary">Shipments</th>';
            $html .= '<th class="color primary">Total Charges</th>';
            $html .= '<th class="color primary">GST</th>';
            $html .= '<th class="color primary">Weight Charges</th>';
            $html .= '<th class="color primary">Commission</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';
    
            // Product records
            $total_shipments = 0;
            $total_charges = 0;
            $total_gst = 0;
            $total_weight_charges = 0;
            $total_commission = 0;
    
            foreach ($records as $record) {
                $data = TotalSumRetailTraxCenter::where('retail_user_id', $record->franchise_id)->first();

                $html .= '<tr>';
                $html .= '<td>' . $record->retail_shipping_mode_name . '</td>';
                $html .= '<td>' . ($record->commission ?? '0') . '%</td>';
                $html .= '<td>' . $record->number_of_shipments . '</td>';
                $html .= '<td>' . number_format(round($record->total_charges)) . '</td>';
                $html .= '<td>' . number_format(round($record->franchise_gst_amount)) . '</td>';
                $html .= '<td>' . number_format(round($record->weight_charges)) . '</td>';
                $html .= '<td>' . number_format(round($record->net_commission)) . '</td>';
                $html .= '</tr>';
    
                // Summing up totals
                $total_shipments += $record->number_of_shipments;
                $total_charges += $record->total_charges;
                $total_gst += $record->franchise_gst_amount;
                $total_weight_charges += $record->weight_charges;
                $total_commission += $record->net_commission;
            }
    
            // Totals row
            $html .= '<tr>';
            $html .= '<td class="text-center" colspan="2"><strong>Total</strong</td>';
            $html .= '<td><strong>' . $total_shipments . '</strong</td>';
            $html .= '<td><strong>' . number_format(round($total_charges)) . '</strong></td>';
            $html .= '<td><strong>' . number_format(round($total_gst)) . '</strong></td>';
            $html .= '<td><strong>' . number_format(round($total_weight_charges)) . '</strong></td>';
            $html .= '<td><strong>' . number_format(round($total_commission)) . '</strong></td>';
            $html .= '</tr>';
    
            $gross_commission = $total_commission;
    
            // Gross commission row
            $html .= '<tr>';
            $html .= '<td class="text-center" colspan="6"><strong>Gross Commission</strong></td>';
            $html .= '<td><strong>' . number_format(round($gross_commission)) . '</strong></td>';
            $html .= '</tr>';
            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';
            $html .= '</div>';
        
            // // Third table: Deposits
            // $html .= '<div class="row align-items-start justify-content-between summary">';
            // $html .= '<div class="col-12">';
            // $html .= '<table class="table table-sm table-bordered border">';
            // $html .= '<thead>';
            // $html .= '<tr>';
            // $html .= '<th class="color primary">Deposits</th>';
            // $html .= '<th class="color primary">Amount</th>';
            // $html .= '<th class="color primary">Bank Name</th>';
            // $html .= '<th class="color primary">Cheque #</th>';
            // $html .= '</tr>';
            // $html .= '</thead>';
            // $html .= '<tbody>';
            // $html .= '<tr><td>Security Deposit</td><td>' . $franchise_charges->security_deposit . '</td><td>' . $franchise_charges->bank_name . '</td><td>' . $franchise_charges->security_cheque_number . '</td></tr>';
            // $html .= '<tr><td>License Fees</td><td>' . $franchise_charges->license_fees . '</td><td>' . $franchise_charges->bank_name . '</td><td>' . $franchise_charges->license_cheque_number . '</td></tr>';
            // $html .= '</tbody>';
            // $html .= '</table>';
            // $html .= '</div>';
            // $html .= '</div>';
        
            // // Fourth table: Pending Sales
            // $html .= '<div class="row align-items-start justify-content-between summary">';
            // $html .= '<div class="col-3">';
            // $html .= '<table class="table table-sm table-bordered border">';
            // $html .= '<tbody>';
            // $html .= '<tr>';
            // $html .= '<td class="w-50" style="height: 3rem; padding-top: 1rem;">Pending Sales:</td>';
            // $html .= '<td class="w-100" style="text-align: center;padding: 1rem 0rem 0rem 0rem;"></td>';
            // $html .= '</tr>';
            // $html .= '</tbody>';
            // $html .= '</table>';
            // $html .= '</div>';
            // $html .= '</div>';
    
            // // Prepared by and Checked by
            // $html .= '<div class="row align-items-start justify-content-between summary col-6">';
            // $html .= '<div class="col-6 d-flex justify-content-between">';
            // $html .= '<strong>Prepared By:</strong>';
            // $html .= '<strong>Checked By:</strong>';
            // $html .= '</div>';
            // $html .= '<div class="col-6 d-flex justify-content-between" style="padding-left:40px;">';
            // $html .= '<strong>Verified By:</strong>';
            // $html .= '<strong>Approved By:</strong>';
            // $html .= '</div>';
            // $html .= '</div>';
    
            // $html .= '<div class="row col-6">';
            // $html .= '<div class="col-6"><div class="w-100"><strong><hr></strong></div></div>';
            // $html .= '<div class="col-6" style="padding-left: 40px;"><div style="width: 16.3rem;"><strong><hr></strong></div></div>';
            // $html .= '</div>';
    
            // $html .= '<div class="row align-items-start justify-content-between summary col-6">';
            // $html .= '<div class="col-6 d-flex justify-content-between">';
            // $html .= '<strong>Retail Team</strong>';
            // $html .= '<strong>Finance Team</strong>';
            // $html .= '</div>';
            // $html .= '<div class="col-6 d-flex justify-content-between" style="padding-left: 40px;">';
            // $html .= '<strong>Head of Retail</strong>';
            // $html .= '<strong>COO</strong>';
            // $html .= '</div>';
            // $html .= '</div>';
    
            // // Empty tables
            // $html .= '<div class="row align-items-start summary">';
            // $html .= '<div class="col-3">';
            // $html .= '<table class="table table-sm table-bordered border" style="margin: 0px 0px 0px 12px;">';
            // $html .= '<tbody>';
            // $html .= '<tr>';
            // $html .= '<td class="w-50" style="height: 3rem; padding-top: 1rem;"></td>';
            // $html .= '<td class="w-100" style="text-align: center;padding: 1rem 0rem 0rem 0rem;"></td>';
            // $html .= '</tr>';
            // $html .= '</tbody>';
            // $html .= '</table>';
            // $html .= '</div>';
    
            // $html .= '<div class="col-3">';
            // $html .= '<table class="table table-sm table-bordered border" style="margin: 0px 0px 0px 12px;">';
            // $html .= '<tbody>';
            // $html .= '<tr>';
            // $html .= '<td class="w-50" style="height: 3rem; padding-top: 1rem;"></td>';
            // $html .= '<td class="w-100" style="text-align: center;padding: 1rem 0rem 0rem 0rem;"></td>';
            // $html .= '</tr>';
            // $html .= '</tbody>';
            // $html .= '</table>';
            // $html .= '</div>';
            // $html .= '</div>';
    
            // Disclaimer after empty tables with page break
            $html .= '<div class="my-2 text-center font-italic"><strong>Disclaimer:</strong> This is a system generated invoice. No signature required.</div>';
            $html .= '<div style="page-break-after: always;"></div>';
        }

        $html .= '</body>';
        $html .= '</html>';
        return $html;
    }

    public function franchise_commission_invoice_print(Request $request)
    {
        $franchise_code = $request->franchise_code;
        $franchise = explode(', ', $franchise_code);
        $franchise_names = RetailFranchiseCommission::whereIn('id', $franchise)->get();

        $html = '';
        $html .= '<!doctype html>';
        $html .= '<html lang="en">';
        $html .= '<head>';
        $html .= '<meta charset="utf-8">';
        $html .= '<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">';
        $html .= '<title>Invoice</title>';
        
        $html .= '<style>';
        $html .= file_get_contents(public_path('app-assets/css/bootstrap.min.css'));
        $html .= '</style>';
        
        $html .= '<style>';
        $html .= '@page{size:A4 portrait; margin-top: 12rem; margin-bottom: 2rem; margin-left: 0rem; margin-right: 0rem;}*{-webkit-print-color-adjust:exact!important;color-adjust:exact!important}body{background:none!important;color:#09262e!important;font-size:0.7rem!important}hr{border-top:1px dashed #000}table.table-bordered{page-break-inside:avoid}table.table-bordered thead tr th, table.table-bordered tbody tr td{border:1px solid #09262e!important}.color.primary{background:#c8c8c8!important}.color.secondary{background:#ebebeb!important}.border{border:1px solid #09262e!important}.summary{page-break-inside:avoid}.shipments_summary{page-break-before:always}';
        $html .= '</style>';
        
        $html .= '<style>';
        $html .= '@page{margin-top: 1rem; margin-bottom: 1rem;}.summary_header .header{width: 10%;}.summary_header .heading{width: 15%;}.summary_footer .footer{width: 75%;}';
        $html .= '</style>';
        
        $html .= '</head>';
        $html .= '<body>';
        
        $html .= '<div>';
        $html .= '<div class="p-1">';
        
        $html .= '<div>';
        $html .= '<div class="p-1">';
        
        $html .= '<div>';
        $html .= '<div class="p-1">';
        $html .= '<div>';
        $html .= '<div class="p-1">';
        $html .= '<div>';
        $html .= '<div class="p-1">';
        $html .= '<div>';
        $html .= '<div class="p-1">';
    
        $grouped_data = [];
        foreach ($franchise_names as $data) {
            $grouped_data[$data->franchise_name][] = $data;
        }

        foreach ($grouped_data as $franchise_name => $records) {
            $franchise_charges = RetailFranchiseCharge::where('franchise_id', $records[0]->franchise_id)->first();

            $monthNumber = $records[0]->month;
            $monthNames = [
                '01' => 'January',
                '02' => 'February',
                '03' => 'March',
                '04' => 'April',
                '05' => 'May',
                '06' => 'June',
                '07' => 'July',
                '08' => 'August',
                '09' => 'September',
                '10' => 'October',
                '11' => 'November',
                '12' => 'December',
            ];
            $monthName = isset($monthNames[$monthNumber]) ? $monthNames[$monthNumber] : '';

            // Start the main container for a franchise
            $html .= '<div class="row align-items-start justify-content-between summary my-4">';
            $html .= '<div class="col-12">';
            $html .= '<table class="table table-sm table-bordered border">';
            $html .= '<tbody>';

            $html .= '<tr>';
            $html .= '<td class="text-center align-middle"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto"></td>';
            $html .= '<td class="text-center align-middle color primary"><strong>Franchsie Details</strong></td>';
            $html .= '<td class="text-center align-middle color secondary">Created at ' . $records[0]->created_at . '</br> by ' . ucfirst(Auth::user()->name) . '</td>';
            $html .= '<td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>';
            $html .= '</tr>';
            
            
            // Franchise details
            $html .= '<tr><td>Franchise Name:</td><td>' . $franchise_name . '</td></tr>';
            $html .= '<tr><td>Address:</td><td>' . $records[0]->franchise_address . '</td></tr>';
            $html .= '<tr><td>Code:</td><td>' . $records[0]->franchise_code . '</td></tr>';
            $html .= '<tr><td>CNIC:</td><td>' . $records[0]->franchise_cnic . '</td></tr>';
            $html .= '<tr><td>Phone #</td><td>' . $records[0]->franchise_phone . '</td></tr>';
            $html .= '<tr><td><strong>Payment Month:</strong></td><td>' . $monthName . '</td></tr>';
            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';
            $html .= '</div>';

            // Start the table for product details
            $html .= '<div class="row align-items-start justify-content-between summary">';
            $html .= '<div class="col-12">';
            $html .= '<table class="table table-sm table-bordered border">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th class="color primary">Product</th>';
            $html .= '<th class="color primary">Approved Percentage (Commission)</th>';
            $html .= '<th class="color primary">Shipments</th>';
            $html .= '<th class="color primary">Total Charges</th>';
            $html .= '<th class="color primary">GST</th>';
            $html .= '<th class="color primary">Weight Charges</th>';
            $html .= '<th class="color primary">Commission</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            // Product records
            $total_shipments = 0;
            $total_charges = 0;
            $total_gst = 0;
            $total_weight_charges = 0;
            $total_commission = 0;

            foreach ($records as $record) {
                $data = TotalSumFranchiseCommission::where('franchise_id', $record->franchise_id)->first();

                $html .= '<tr>';
                $html .= '<td>' . $record->retail_shipping_mode_name . '</td>';
                $html .= '<td>' . ($record->product_percentage ?? '0') . '%</td>';
                $html .= '<td>' . $record->number_of_shipments . '</td>';
                $html .= '<td>' . number_format(round($record->total_charges)) . '</td>';
                $html .= '<td>' . number_format(round($record->franchise_gst_amount)) . '</td>';
                $html .= '<td>' . number_format(round($record->weight_charges)) . '</td>';
                $html .= '<td>' . number_format(round($record->commission)) . '</td>';
                $html .= '</tr>';

                $total_shipments += $record->number_of_shipments;
                $total_charges += $record->total_charges;
                $total_gst += $record->franchise_gst_amount;
                $total_weight_charges += $record->weight_charges;
                $total_commission += $record->commission;
            }

            // Totals row
            $html .= '<tr>';
            $html .= '<td class="text-center" colspan="2"><strong>Total</strong></td>';
            $html .= '<td><strong>' . $total_shipments . '</strong></td>';
            $html .= '<td><strong>' . number_format(round($total_charges)) . '</strong></td>';
            $html .= '<td><strong>' . number_format(round($total_gst)) . '</strong></td>';
            $html .= '<td><strong>' . number_format(round( $total_weight_charges)) . '</strong></td>';
            $html .= '<td><strong>' . number_format(round($total_commission)) . '</strong></td>';
            $html .= '</tr>';

            // $withholding_amount = $data->withholding_amount;
            // $deduction_amount = $data->deduction_amount;

            $withholding_amount = ($record->franchise_withholding_percentage / 100) * $total_commission;
            $deduction_amount = ($record->deduction_percentage / 100) * $total_commission;
            $gross_commission = $total_commission - ($withholding_amount + $deduction_amount);

            // Withholding tax row
            $html .= '<tr>';
            $html .= '<td class="text-center" colspan="6"><strong>Withholding Income Tax ' . ($data->withholding_tax_percent ?? 0) . '%</strong></td>';
            $html .= '<td><strong>' . number_format(round($withholding_amount)) . '</strong></td>';
            $html .= '</tr>';

            // Deduction GST tax row
            $html .= '<tr>';
            $html .= '<td class="text-center" colspan="6"><strong>Deduction ' . ($data->commission_gst_deduction_percent ?? 0) . '</strong></td>';
            $html .= '<td><strong>' . number_format(round($deduction_amount)) . '</strong></td>';
            $html .= '</tr>';

            // Gross commission row
            $html .= '<tr>';
            $html .= '<td class="text-center" colspan="6"><strong>Gross Commission</strong></td>';
            $html .= '<td><strong>' . number_format(round($gross_commission)) . '</strong></td>';
            $html .= '</tr>';
            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';
            $html .= '</div>';

            // // Third table: Deposits
            // $html .= '<div class="row align-items-start justify-content-between summary">';
            // $html .= '<div class="col-12">';
            // $html .= '<table class="table table-sm table-bordered border">';
            // $html .= '<thead>';
            // $html .= '<tr>';
            // $html .= '<th class="color primary">Deposits</th>';
            // $html .= '<th class="color primary">Amount</th>';
            // $html .= '<th class="color primary">Bank Name</th>';
            // $html .= '<th class="color primary">Cheque #</th>';
            // $html .= '</tr>';
            // $html .= '</thead>';
            // $html .= '<tbody>';
            // $html .= '<tr><td>Security Deposit</td><td>' . ($franchise_charges->security_deposit ?? 0) . '</td><td>' . ($franchise_charges->bank_name ?? '') . '</td><td>' . ($franchise_charges->security_cheque_number ?? '') . '</td></tr>';
            // $html .= '<tr><td>License Fees</td><td>' . ($franchise_charges->license_fees ?? 0) . '</td><td>' . ($franchise_charges->bank_name ?? '') . '</td><td>' . ($franchise_charges->license_cheque_number ?? '') . '</td></tr>';
            // $html .= '</tbody>';
            // $html .= '</table>';
            // $html .= '</div>';
            // $html .= '</div>';


            // // Fourth table: Pending Sales
            // $html .= '<div class="row align-items-start justify-content-between summary">';
            // $html .= '<div class="col-3">';
            // $html .= '<table class="table table-sm table-bordered border">';
            // $html .= '<tbody>';
            // $html .= '<tr>';
            // $html .= '<td class="w-50" style="height: 3rem; padding-top: 1rem;">Pending Sales:</td>';
            // $html .= '<td class="w-100" style="text-align: center;padding: 1rem 0rem 0rem 0rem;"></td>';
            // $html .= '</tr>';
            // $html .= '</tbody>';
            // $html .= '</table>';
            // $html .= '</div>';
            // $html .= '</div>';

            // // Prepared by and Checked by
            // $html .= '<div class="row align-items-start justify-content-between summary col-6">';
            // $html .= '<div class="col-6 d-flex justify-content-between">';
            // $html .= '<strong>Prepared By:</strong>';
            // $html .= '<strong>Checked By:</strong>';
            // $html .= '</div>';
            // $html .= '<div class="col-6 d-flex justify-content-between" style="padding-left:40px;">';
            // $html .= '<strong>Verified By:</strong>';
            // $html .= '<strong>Approved By:</strong>';
            // $html .= '</div>';
            // $html .= '</div>';

            // $html .= '<div class="row col-6">';
            // $html .= '<div class="col-6"><div class="w-100"><strong><hr></strong></div></div>';
            // $html .= '<div class="col-6" style="padding-left: 40px;"><div style="width: 16.3rem;"><strong><hr></strong></div></div>';
            // $html .= '</div>';

            // $html .= '<div class="row align-items-start justify-content-between summary col-6">';
            // $html .= '<div class="col-6 d-flex justify-content-between">';
            // $html .= '<strong>Retail Team</strong>';
            // $html .= '<strong>Finance Team</strong>';
            // $html .= '</div>';
            // $html .= '<div class="col-6 d-flex justify-content-between" style="padding-left: 40px;">';
            // $html .= '<strong>Head of Retail</strong>';
            // $html .= '<strong>COO</strong>';
            // $html .= '</div>';
            // $html .= '</div>';

            // // Empty tables
            // $html .= '<div class="row align-items-start summary">';
            // $html .= '<div class="col-3">';
            // $html .= '<table class="table table-sm table-bordered border" style="margin: 0px 0px 0px 12px;">';
            // $html .= '<tbody>';
            // $html .= '<tr>';
            // $html .= '<td class="w-50" style="height: 3rem; padding-top';
            // $html .= '<td class="w-50" style="height: 3rem; padding-top: 1rem;"></td>';
            // $html .= '<td class="w-100" style="text-align: center; padding: 1rem 0rem 0rem 0rem;"></td>';
            // $html .= '</tr>';
            // $html .= '</tbody>';
            // $html .= '</table>';
            // $html .= '</div>';

            // $html .= '<div class="col-3">';
            // $html .= '<table class="table table-sm table-bordered border" style="margin: 0px 0px 0px 12px;">';
            // $html .= '<tbody>';
            // $html .= '<tr>';
            // $html .= '<td class="w-50" style="height: 3rem; padding-top: 1rem;"></td>';
            // $html .= '<td class="w-100" style="text-align: center; padding: 1rem 0rem 0rem 0rem;"></td>';
            // $html .= '</tr>';
            // $html .= '</tbody>';
            // $html .= '</table>';
            // $html .= '</div>';
            // $html .= '</div>';
            
            // Disclaimer after empty tables with page break
            $html .= '<div class="my-2 text-center font-italic"><strong>Disclaimer:</strong> This is a system generated invoice. No signature required.</div>';
            $html .= '<div style="page-break-after: always;"></div>';
        }

        $html .= '</div>';
        $html .= '</div>';

        $html .= '</body>';
        $html .= '</html>';
        return $html;
    }

    public function franchise_commission_view_ajax_list(Request $request)
    {
        $month = $request->month;
        $franchise = $request->franchise;
        $paid_status = $request->paid_status;
        $query = RetailFranchiseCommission::where('month', $month);
        if (!empty($franchise)) {
            $query->where('franchise_id', $franchise);
        }
        if (!empty($paid_status) || $paid_status == '0') {
            $query->where('is_paid', $paid_status);
        }
        $retail_franchise_commission = $query->get();
        $results = $retail_franchise_commission;
        return response()->json([
            'data' => $results,
        ]);
    }
    

    public function user_commission_view(){
        $franchises = RetailUser::where('category', 2)->get();
        $finance_department = AdminDepartment::where('id', 4)->first();
        $admin_roles = AdminRole::where('department_id', $finance_department->id)->get();
        $allowed_users = Admin::whereIn('role_id', $admin_roles->pluck('id'))->get();
        return view('admin.retail.commission.user_wise', [
            'franchises' => $franchises,
            'allowed_users' => $allowed_users
        ]);
    }

    public function user_commission_view_ajax_list(Request $request)
    {
        $month = $request->month;
        $franchise = $request->franchise;
        $paid_status = $request->paid_status;
        $query = RetailUserCommission::where('month', $month);
        if (!empty($franchise)) {
            $query->where('retail_user_id', $franchise);
        }
        if (!empty($paid_status) || $paid_status == '0') {
            $query->where('is_paid', $paid_status);
        }
        $retail_trax_center_commission = $query->get();
        $results = $retail_trax_center_commission;
        return response()->json([
            'data' => $results,
        ]);
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
            ->select('retail_trax_centers.id', 'retail_trax_centers.name', 'retail_trax_centers.phone_no', 'retail_trax_centers.email', 'retail_trax_centers.cnic', 'c.name as default_hub', 'c.id as default_hub_id', 'a.name as updated_by', 'retail_trax_centers.status', 'retail_trax_centers.code', 'retail_trax_centers.location_latitude', 'retail_trax_centers.location_longitude', 'retail_trax_centers.created_at as created', 'retail_trax_centers.updated_at as updated','retail_trax_centers.discount','retail_trax_centers.insurance');

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
                        $dropdown .= '<button type="button" class="dropdown-item enable">
                            <div class="row no-gutters align-items-center">
                                <div class="col-2"><i class="ft-x-circle"></i></div>
                                <div class="col-9 offset-1">Enable</div>
                            </div>
                        </button>';
                    } else {
                        $dropdown .= '<button type="button" class="dropdown-item disable">
                            <div class="row no-gutters align-items-center">
                                <div class="col-2"><i class="ft-x-circle"></i></div>
                                <div class="col-9 offset-1">Disable</div>
                            </div>
                        </button>';
                    }
            
                    $dropdown .= '<button type="button" class="dropdown-item edit">
                        <div class="row no-gutters align-items-center">
                            <div class="col-2"><i class="ft-x-circle"></i></div>
                            <div class="col-9 offset-1">Edit</div>
                        </div>
                    </button>';
                    $dropdown .= '<button type="button" class="dropdown-item view_logs">
                            <div class="row no-gutters align-items-center">
                                <div class="col-2">
                                    <i class="ft-edit"></i>
                                </div>
                                <div class="col-9 offset-1">
                                    View Logs
                                </div>
                            </div>
                        </button>';

                    // Adding Excel export dropdown item
                    $dropdown .= '
                            <a href="' . route("admin.retail.trax_center.trax_center_details_excel_sheet", ["id" => $data->id]) . '" class="text-dark">
                                <div class="row no-gutters align-items-center ml-2">
                                    <div class="col-2">
                                        <i class="la la-file-excel-o"></i>
                                    </div>
                                    <div class="col-9" style="margin: 6px 0px 9px 5px;">
                                        Excel
                                    </div>
                                </div>
                            </a>
                            ';

                    $dropdown .= '</div></div>';
            
                    return $dropdown;
                } else {
                    return '';
                }
            })->rawColumns(['action','location']);
        return $datatables->make(true);
    }

    public function trax_center_enable_disable(Request $request)
    {
        $trax_center = RetailTraxCenter::find($request->id);
        if ($request->status == 1) {
            $trax_center->status = 1;
            $trax_center->updated_by = Auth::id();

            $changedFields[] = 'Center Status' . ': ' . ($trax_center->getOriginal('status') == 1 ? 'Active' : 'In-Active') . ' -> ' . ($trax_center->status == 1  ? 'Active' : 'In-Active');
            self::retail_logs(Auth::id(), $changedFields, $trax_center->id, $screen_name = 'Retail Center');
            $trax_center->save();

            return response()->json(['status' => 1, 'success' => 'Trax Center Enabled Successfully']);
        } elseif ($request->status == 0) {
            $trax_center->status = 0;
            $changedFields[] = 'Center Status' . ': ' . ($trax_center->getOriginal('status') == 1 ? 'Active' : 'In-Active') . ' -> ' . ($trax_center->status == 1  ? 'Active' : 'In-Active');
            self::retail_logs(Auth::id(), $changedFields, $trax_center->id, $screen_name = 'Retail Center');
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
        $request->validate([
            'attachment_1' => 'required|mimes:jpeg,png,jpg,pdf,doc,docx|max:2048',
        ]);
        $date = Carbon::now()->format('Y_m_d');
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
        $trax_center->insurance = $request->insurance;
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

        $trax_center_attachment = new TraxCenterAttachment();
        $trax_center_attachment->retail_trax_center_id = $trax_center->id;
        $trax_center_attachment->advance_amount = (int) str_replace(',', '', $request->advance_amount);
        $trax_center_attachment->rental = (int) str_replace(',', '', $request->rental);
        $trax_center_attachment->landlord_name = $request->landlord_name;
        $trax_center_attachment->landlord_contact_number = $request->landlord_contact_number;
        $trax_center_attachment->shop_address = $request->shop_address;
        $trax_center_attachment->agreement_start_date = $request->agreement_start_date;
        $trax_center_attachment->agreement_end_date = $request->agreement_end_date;

        for ($i = 1; $i <= 5; $i++) {
            if ($request->hasFile('attachment_' . $i)) {
                $file = $request->file('attachment_' . $i);
                $fileName = $file->getClientOriginalName() . '_' . $date . '_' . Carbon::now()->format('His');
                $folderName = 'trax_center_attachment_' . $i;
                $filePath = $file->storeAs('trax_center_attachments/' . $folderName, $fileName, 'public');
                $trax_center_attachment->{'attachment_' . $i} = $fileName;
            }
        }

        $trax_center_attachment->save();
        return redirect()->back()->with('success', 'Trax Center Added Successfully!');
    }

    public function trax_center_edit(Request $request)
    {
        $date = Carbon::now()->format('Y_m_d');
        $existing_trax_center = RetailTraxCenter::where('name', $request->name)->where('id', '!=', $request->trax_center_id);
        $changedFields = [];
        if (!$existing_trax_center->exists()) {
            $trax_center = RetailTraxCenter::find($request->trax_center_id);

            $trax_center->name = $request->name;
            $trax_center->phone_no = $request->phone_number;
            $trax_center->email = $request->email;
            $trax_center->cnic = $request->cnic;
            $trax_center->location_latitude = $request->lat;
            $trax_center->location_longitude = $request->long;
            $trax_center->discount = $request->discount;
            $trax_center->insurance = $request->edit_insurance;
            $trax_center->updated_by = Auth::id();

            $fieldNames = [
                'name' => 'Name',
                'phone_no' => 'Phone No',
                'email' => 'Email',
                'cnic' => 'CNIC',
                'location_latitude' => 'Latitude',
                'location_longitude' => 'Longitude',
                'discount' => 'Discount',
                'insurance' => 'Insurance'
            ];

            foreach ($fieldNames as $field => $fieldName) {
                $originalValue = trim($trax_center->getOriginal($field));
                $currentValue = trim($trax_center->$field);
                if ($trax_center->isDirty($field) && $originalValue !== $currentValue) {
                    $changedFields[] = $fieldName . ': ' . $originalValue . ' -> ' . $currentValue;
                }
            }
            $trax_center->save();

            $trax_center_attachment = TraxCenterAttachment::where('retail_trax_center_id', $request->trax_center_id)->first();
            if ($trax_center_attachment != null) {
                $trax_center_attachment->advance_amount = (int) str_replace(',', '', $request->advance_amount);
                $trax_center_attachment->rental = (int) str_replace(',', '', $request->rental);
                $trax_center_attachment->landlord_name = $request->landlord_name;
                $trax_center_attachment->landlord_contact_number = $request->landlord_contact_number;
                $trax_center_attachment->shop_address = $request->shop_address;
                if ($request->agreement_start_date != null){
                    $trax_center_attachment->agreement_start_date = $request->agreement_start_date;
                }
                if ($request->agreement_end_date != null){
                    $trax_center_attachment->agreement_end_date = $request->agreement_end_date;
                }

                $fieldNames = [
                    'advance_amount' => 'Advance Amount',
                    'rental' => 'Rental',
                    'landlord_name' => 'Landlord Name',
                    'landlord_contact_number' => 'Landlord Contact Number',
                    'location_latitude' => 'Latitude',
                    'shop_address' => 'Shop Address',
                    'agreement_start_date' => 'Agreement Start Date',
                    'agreement_end_date' => 'Agreement End Date'
                ];
    
                foreach ($fieldNames as $field => $fieldName) {
                    $originalValue = trim($trax_center_attachment->getOriginal($field));
                    $currentValue = trim($trax_center_attachment->$field);
                    if ($trax_center_attachment->isDirty($field) && $originalValue !== $currentValue) {
                        $changedFields[] = $fieldName . ': ' . $originalValue . ' -> ' . $currentValue;
                    }
                }
                // Handle file uploads
                for ($i = 1; $i <= 5; $i++) {
                    $attachment_name = 'attachment_' . $i;
                    if ($request->hasFile($attachment_name)) {
                        $file = $request->file($attachment_name);
                        $fileName = $file->getClientOriginalName() . '_' . $date . '_' . Carbon::now()->format('His');
                        $folderName = 'trax_center_attachment_' . $i;
                        $filePath = $file->storeAs('trax_center_attachments' . DIRECTORY_SEPARATOR . $folderName, $fileName, 'public');
                        $trax_center_attachment->{$attachment_name} = $fileName;

                        $terms = [
                            '1' => 'Agreement File',
                            '2' => 'Landlord CNIC Front',
                            '3' => 'Landlord CNIC Back',
                            '4' => 'Location Pictures',
                            '5' => 'Attachment 5'
                        ];
                        $changedFields[] = $terms[$i] . ' ' . 'Changed at' . ' ' .  now()->toDateTimeString();
                    }
                }
            
                // Save the changes
                $trax_center_attachment->save();
            } else {
                $new_trax_center_attachments = new TraxCenterAttachment();
                $new_trax_center_attachments->retail_trax_center_id = $request->trax_center_id;
                $new_trax_center_attachments->advance_amount = $request->advance_amount;
                $new_trax_center_attachments->rental = $request->rental;
                $new_trax_center_attachments->landlord_name = $request->landlord_name;
                $new_trax_center_attachments->landlord_contact_number = $request->landlord_contact_number;
                $new_trax_center_attachments->shop_address = $request->shop_address;
                $new_trax_center_attachments->agreement_start_date = $request->agreement_start_date;
                $new_trax_center_attachments->agreement_end_date = $request->agreement_end_date;

                for ($i = 1; $i <= 5; $i++) {
                    $attachment_name = 'attachment_' . $i;
                    if ($request->hasFile($attachment_name)) {
                        $file = $request->file($attachment_name);
                        $fileName = $file->getClientOriginalName() . '_' . $date . '_' . Carbon::now()->format('His');
                        $folderName = 'trax_center_attachment_' . $i;
                        $filePath = $file->storeAs('trax_center_attachments' . DIRECTORY_SEPARATOR . $folderName, $fileName, 'public');
                        $new_trax_center_attachments->{$attachment_name} = $fileName;
                    }
                }
                $new_trax_center_attachments->save();
            }

            if(!empty($changedFields)) {
                self::retail_logs(Auth::id(), $changedFields, $trax_center->id, $screen_name = 'Retail Center');
            }
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

    public function trax_center_edit_attachment(Request $request){
        $trax_center_id = $request->trax_center_id;
        $trax_center_attachment = TraxCenterAttachment::where('retail_trax_center_id', $trax_center_id)->first();
        return response()->json([
            'data' => $trax_center_attachment
        ]);
    }

    public function user_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 372);
        $trax_centers = RetailTraxCenter::where('status', 1)->get();
        $franchises = RetailFranchise::where('status', 1)->get();
        $shipping_modes = RetailShippingMode::where('business_category_id',1)->get();
        return view('admin.retail.users.index')->with(['trax_centers' => $trax_centers, 'franchises' => $franchises, 'shipping_modes' => $shipping_modes]);
    }

    public function user_edit($id)
    {
        $retail_user_family_names = [];
        $retail_user_salary = [];
        $agreement_start_date = null;
        $retail_user = RetailUser::find($id);
        $retail_user_id = $retail_user->id;
        $trax_centers = RetailTraxCenter::where('status', 1)->get();
        $franchises = RetailFranchise::where('status', 1)->get();
        $shipping_modes = RetailShippingMode::where('business_category_id',1)->get();
        $retail_user_family_names_query = RetailUserFamilyInformation::where('retail_user_id', $retail_user_id);
        $old_trax_center = RetailUserHistory::where('retail_user_id', $id);

        if ($retail_user_family_names_query->exists()) {
            $retail_user_family_names_query = $retail_user_family_names_query->get();
            $retail_user_family_names = $retail_user_family_names_query->pluck('family_member_name')->toArray();
            $retail_user_salary = $retail_user_family_names_query->pluck('salary')->toArray();
            $agreement_start_date = $retail_user_family_names_query->first()->agreement_start_date;
        }
        $jsonAgreementStartDate = $agreement_start_date ? json_encode($agreement_start_date) : null;
        return view('admin.retail.users.edit')->with([
            'retail_user' => $retail_user, 
            'trax_centers' => $trax_centers, 
            'franchises' => $franchises, 
            'shipping_modes' => $shipping_modes, 
            'retail_user_id' => $retail_user_id, 
            'retail_user_family_names' => $retail_user_family_names,
            'retail_user_salary' => $retail_user_salary,
            'jsonAgreementStartDate' => $jsonAgreementStartDate
        ]);
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
            ->Join('zones as z', 'z.id', '=', 'c.zone_id')
            ->select('retail_users.id', 'retail_users.trax_id', 'retail_users.name',
             'retail_users.phone_no', 'retail_users.cnic', 'retail_users.address',
              'retail_users.category', 'retail_users.created_at as created', 'retail_users.updated_at as updated',
               'retail_users.status', 'c.name as city', 'c.id as city_id', 'h.name as hub', 'h.id as hub_id',
               'z.name as zone',  
               'ac.name as created_by', 
               'au.name as updated_by');

            //    dd($franchise->get()->toArray());

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

                    $dropdown .= '<button type="button" class="dropdown-item view_logs">
                            <div class="row no-gutters align-items-center">
                                <div class="col-2">
                                    <i class="ft-edit"></i>
                                </div>
                                <div class="col-9 offset-1">
                                    View Logs
                                </div>
                            </div>
                        </button>';

                    if ($data->category != 1){
                        $dropdown .= '
                            <a href="' . route("admin.retail.users.retail_history", ["id" => $data->id]) . '" class="text-dark" target="_blank">
                                <div class="row no-gutters align-items-center ml-2">
                                    <div class="col-2">
                                        <i class="ft-x-circle"></i>
                                    </div>
                                    <div class="col-9" style="margin: 6px 0px 9px 5px;">
                                        History
                                    </div>
                                </div>
                            </a>
                        ';
                    }  
                    if ($data->category == 2)
                    {
                        $dropdown .= '
                            <a href="' . route("admin.retail.users.retail_user_excel_sheet", ["id" => $data->id]) . '" class="text-dark">
                                <div class="row no-gutters align-items-center ml-2">
                                    <div class="col-2">
                                        <i class="la la-file-excel-o"></i>
                                    </div>
                                    <div class="col-9" style="margin: 6px 0px 9px 5px;">
                                        Excel
                                    </div>
                                </div>
                            </a>
                            ';
                    }
                    if ($data->category == 1)
                    {
                        $dropdown .= '
                            <a href="' . route("admin.retail.users.franchise_excel_sheet", ["id" => $data->id]) . '" class="text-dark">
                                <div class="row no-gutters align-items-center ml-2">
                                    <div class="col-2">
                                        <i class="la la-file-excel-o"></i>
                                    </div>
                                    <div class="col-9" style="margin: 6px 0px 9px 5px;">
                                        Excel
                                    </div>
                                </div>
                            </a>
                            ';
                    }

                    //$dropdown .= '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Edit</div></button>';
                    $dropdown .= '
                    </div>
                  </div>
          ';
                    return $dropdown;
                } else {
                    return '';
                }
            })->rawColumns(['action']);
        return $datatables->make(true);
    }

    public function user_enable_disable(Request $request)
    {
        $user = RetailUser::find($request->id);
        if ($request->status == 1) {
            if ($user->store->status == 1) {
                $user->status = 1;
                $user->updated_by = Auth::id();

                $changedFields[] = 'User Status' . ': ' . ($user->getOriginal('status') == 1 ? 'Active' : 'In-Active') . ' -> ' . ($user->status == 1  ? 'Active' : 'In-Active');
                self::retail_logs(Auth::id(), $changedFields, $user->id, $screen_name = 'Retail User');
                $user->save();
                
                return response()->json(['status' => 1, 'success' => 'User Enabled Successfully']);
            } else {
                return response()->json(['status' => 0, 'error' => 'User Store is Disabled']);
            }
        } elseif ($request->status == 0) {
            $user->status = 0;
            $user->updated_by = Auth::id();
            $changedFields[] = 'User Status' . ': ' . ($user->getOriginal('status') == 1 ? 'Active' : 'In-Active') . ' -> ' . ($user->status == 1  ? 'Active' : 'In-Active');
            self::retail_logs(Auth::id(), $changedFields, $user->id, $screen_name = 'Retail User');

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
            $request->validate([
                'attachment_1' => 'required|mimes:jpeg,png,jpg,pdf,doc,docx|max:2048',
                'name' => 'required',
                'phone_number' => 'required',
                'password' => 'required',
                'cnic' => 'required',
                'address' => 'required',
                'store' => 'required',
                'trax_id' => [
                    'nullable',
                    'required_unless:store,1',
                    'regex:/^[0-9]*$/',
                ],
            ]);

            $retailShippingModeNames = json_decode($request->retail_shipping_mode_id, true);
            $productPercentages = json_decode($request->product_percentage, true);
            $familyMemberNames = $request->family_member_name;
            $joining_date = $request->agreement_start_date;
            $traxId = $request->trax_id;
            $attachment_1 = $request->hasFile('attachment_1');
            $attachment_2 = $request->hasFile('attachment_2');
            $attachment_3 = $request->hasFile('attachment_3');
            $attachment_4 = $request->hasFile('attachment_4');
            $attachment_5 = $request->hasFile('attachment_5');
            $file_1 = $request->file('attachment_1');
            $file_2 = $request->file('attachment_2');
            $file_3 = $request->file('attachment_3');
            $file_4 = $request->file('attachment_4');
            $file_5 = $request->file('attachment_5');

            $this->add_user($request->name, $password, $request->phone_number, $store->default_hub, $request->cnic, $request->address, $request->store, $store->id, $familyMemberNames, $traxId, $retailShippingModeNames, $productPercentages, $attachment_1, $attachment_2, $attachment_3, $attachment_4, $attachment_5, $file_1, $file_2, $file_3, $file_4, $file_5, $joining_date);

            return redirect()->back()->with('success', 'Retail User Added Successfully!');
        } else {
            return redirect()->back()->with('success', 'Same Retail User already exists!');
        }
    }

    public function retail_user_percentage(Request $request){
        $franchiseId = $request->retail_user_id;
        $retail_franchise_product_percentage = RetailUserProductPercentage::where('retail_user_id', $franchiseId)->get();
        $data = [];
        foreach ($retail_franchise_product_percentage as $percentage) {
            $selectedOption = RetailShippingMode::find($percentage->retail_shipping_mode_id)->name;
            $data[] = [
                'selected_option' => $selectedOption,
                'product_percentage' => $percentage->product_percentage,
            ];
        }
        return response()->json(['data' => $data]);
    }

    public function user_update(Request $request, $id)
    {
        $request->validate([
            'trax_id' => [
                'nullable',
                'required_unless:store,1',
                'regex:/^[0-9]*$/',
            ],
        ]);
        $retail_user = RetailUser::find($id);
        $admin = $request->user();
        $retail_user->name = $request->name;
        $retail_user->category = $request->store;
        if ($request->store == 1) {
            $retail_user->category_id = $request->franchise;
        } else {
            $retail_user->category_id = $request->trax_center;
        }
        if (!empty($request->password)){
            $retail_user->password = Hash::make($request->password);
        }
        $retail_user->phone_no = $request->phone_number;
        $retail_user->cnic = $request->cnic;
        $retail_user->address = $request->address;
        $retail_user->updated_by = Auth::id();
        $retail_user->trax_id = $request->trax_id;

        $changedFields = [];
        $fieldNames = [
          'name' => 'Name',
          'category' => 'Category',
          'phone_no' => 'Phone no',
          'cnic' => 'CNIC',
          'address' => 'Address',
          'trax_id' => 'Trax ID',
          'category_id' => 'Category ID'
        ];

        foreach ($fieldNames as $field => $fieldName) {
            $originalValue = trim($retail_user->getOriginal($field));
            $currentValue = trim($retail_user->$field);
            if ($retail_user->isDirty($field) && $originalValue !== $currentValue) {
                if($field == 'category_id'){
                    if($request->store == 1) {
                        $old = RetailFranchise::find($originalValue);
                        $new = RetailFranchise::find($currentValue);
                        $fieldName = 'Franchise';
                    }else{
                        $old = RetailTraxCenter::find($originalValue);
                        $new = RetailTraxCenter::find($currentValue);
                        $fieldName = 'Trax Center';
                    }
                    $changedFields[] = $fieldName . ': ' . $old->name . ' -> ' . $new->name;
                }else {
                    $changedFields[] = $fieldName . ': ' . $originalValue . ' -> ' . $currentValue;
                }
            }
        }
        
        $retail_user->save();
        
        // if ($retail_user->category == 2){
        //     // retail user history
        //     $retail_user_history = RetailUserHistory::where('retail_user_id', $id)->first();
        //     $trax_center = RetailTraxCenter::where('code', $retail_user->store->code)->first();
        //     $new_trax_center_code = $trax_center->code;
        //     $new_trax_center_name = $trax_center->name;
        //     if ($retail_user_history){
        //         $old_trax_center_code = $retail_user_history->trax_center_code;
        //         if ($old_trax_center_code != $new_trax_center_code){
        //             $data = [
        //                 'retail_user_id' => $retail_user->id,
        //                 'trax_center_id' => $trax_center->id,
        //                 'trax_center_name' => $new_trax_center_name,
        //                 'trax_center_code' => $new_trax_center_code,
        //                 'joining_date' => $request->agreement_start_date,
        //             ];
        //             RetailUserHistory::create($data);
        //             $retail_user_history->update(['last_date' => $request->agreement_start_date]);
        //         }
        //     } else {
        //         $data = [
        //             'retail_user_id' => $retail_user->id,
        //             'trax_center_id' => $trax_center->id,
        //             'trax_center_name' => $new_trax_center_name,
        //             'trax_center_code' => $new_trax_center_code,
        //             'joining_date' => $request->agreement_start_date,
        //         ];
        //         RetailUserHistory::create($data);
        //     }
        // }


        if ($retail_user->category == 2) {
            // retail user history
            $retail_user_history = RetailUserHistory::where('retail_user_id', $id)->latest()->first();
            $trax_center = RetailTraxCenter::where('code', $retail_user->store->code)->first();
        
            if ($trax_center) {
                $new_trax_center_code = $trax_center->code;
                $new_trax_center_name = $trax_center->name;
        
                if ($retail_user_history) {
                    $old_trax_center_code = $retail_user_history->trax_center_code;
        
                    if ($old_trax_center_code != $new_trax_center_code) {
                        // Create a new entry with the new Trax center code and name
                        $data = [
                            'retail_user_id' => $retail_user->id,
                            'trax_center_id' => $trax_center->id,
                            'trax_center_name' => $new_trax_center_name,
                            'trax_center_code' => $new_trax_center_code,
                            'joining_date' => $request->agreement_start_date,
                        ];
                        RetailUserHistory::create($data);
        
                        // Update the last_date of the old history record
                        $retail_user_history->update(['last_date' => $request->agreement_start_date]);
                    }
                } else {
                    // No previous history, create the first entry
                    $data = [
                        'retail_user_id' => $retail_user->id,
                        'trax_center_id' => $trax_center->id,
                        'trax_center_name' => $new_trax_center_name,
                        'trax_center_code' => $new_trax_center_code,
                        'joining_date' => $request->agreement_start_date,
                    ];
                    RetailUserHistory::create($data);
                }
            }
        }

        // retail user commission
        $retailShippingModeNames = json_decode($request->retail_shipping_mode_id, true);
        $productPercentages = json_decode($request->product_percentage, true);
        
        $retailShippingModeNames = is_array($retailShippingModeNames) ? $retailShippingModeNames : [];
        $productPercentages = is_array($productPercentages) ? $productPercentages : [];
        
        $retailShippingModes = RetailShippingMode::whereIn('name', $retailShippingModeNames)->get();
        $matchingRetailShippingModeIds = $retailShippingModes->pluck('id')->toArray();
        
        $retail_user_old_product_percentages = RetailUserProductPercentage::where('retail_user_id', $retail_user->id)->get();
        
        if ($request->has('retail_shipping_mode_id')) {
            // Delete old records not present in the new request
            foreach ($retail_user_old_product_percentages as $oldPercentage) {
                if (!in_array($oldPercentage->retail_shipping_mode_id, $matchingRetailShippingModeIds)) {
                    $shippingMode =RetailShippingMode::find($oldPercentage->retail_shipping_mode_id);
                    //dd($retailShippingModes);
                    $shippingModeName = $shippingMode->name ?? 'Unknown';
                    $logEntry = "{$shippingModeName}: {$oldPercentage->product_percentage}";
                    $changedFields[] = $logEntry;
                    $oldPercentage->delete();
                }
            }
        
            // Update or create new records
            foreach ($retailShippingModeNames as $key => $retailShippingModeName) {
                $retailShippingModeId = $matchingRetailShippingModeIds[$key] ?? null;
                $productPercentage = $productPercentages[$key] ?? null;
        
                $existingRecord = $retail_user_old_product_percentages->firstWhere('retail_shipping_mode_id', $retailShippingModeId);
        
                if ($existingRecord) {
                    if ($existingRecord->product_percentage != $productPercentage) {
                        $existingRecord->product_percentage = $productPercentage;
                        $existingRecord->save();
                    }
                } else {
                    $retail_user_product_percentage = new RetailUserProductPercentage();
                    $retail_user_product_percentage->retail_user_id = $retail_user->id;
                    $retail_user_product_percentage->retail_shipping_mode_id = $retailShippingModeId;
                    $retail_user_product_percentage->product_percentage = $productPercentage;
                    $retail_user_product_percentage->created_by = Auth::id();
                    $retail_user_product_percentage->save();
                }
            }
        }

        // retail user family info
        $familyMemberNames = $request->family_member_name;
        
        $familyTypeMap = [
            1 => 'Spouse',
            2 => 'Children',
            3 => 'Father',
            4 => 'Mother',
            5 => 'Spouse DOB'
        ];
        
        $familyInfo = RetailUserFamilyInformation::where('retail_user_id', $retail_user->id)
        ->whereNotNull('family_member_name')
        ->get(['family_member_name', 'salary', 'agreement_start_date', 'family_member_type']);
    
        // Add salary and agreement date once (from first record)
        if ($familyInfo->isNotEmpty()) {
            $first = $familyInfo->first();
        
            if ($first->salary) {
                $changedFields[] = 'Salary: ' . $first->salary;
            }
        
            if ($first->agreement_start_date) {
                $changedFields[] = 'Agreement Start Date: ' . $first->agreement_start_date;
            }
        }
        
        // Add each family member
        foreach ($familyInfo as $item) {
            $type = $familyTypeMap[$item->family_member_type] ?? $item->family_member_type;
            $changedFields[] = "{$type}: {$item->family_member_name}";
        }
    
        RetailUserFamilyInformation::where('retail_user_id', $retail_user->id)->delete();
        foreach ($familyMemberNames as $key => $familyMemberName) {
            // Delete existing records for the retail user only if new family member information is present
                $family_member_type = null;
                if ($key == 0) {
                    $family_member_type = 3; // Father
                } elseif ($key == 1) {
                    $family_member_type = 4; // Mother
                } elseif ($key == 2) {
                    $family_member_type = 1; // Spouse
                } elseif ($key == 3) {
                    $family_member_type = 5; // Spouse DOB
                } else {
                    $family_member_type = 2; // Children
                }

                $family_member_new_data = new RetailUserFamilyInformation();
                $family_member_new_data->retail_user_id = $retail_user->id;
                $family_member_new_data->family_member_name = $familyMemberName;
                $family_member_new_data->family_member_type = $family_member_type;
                $family_member_new_data->salary = $request->salary;
                $family_member_new_data->agreement_start_date = $request->agreement_start_date;
                $family_member_new_data->save();
        }

        $baseDirectory = 'retail_user_attachments';
        if (!Storage::disk('public')->exists($baseDirectory)) {
            Storage::disk('public')->makeDirectory($baseDirectory);
        }
        $old_attachments = RetailUserAttachment::where('retail_user_id', $retail_user->id)->first();
        if ($old_attachments) {
            // Update existing attachments
            for ($i = 1; $i <= 5; $i++) {
                $attachment_name = 'attachment_' . $i;
                if ($request->hasFile($attachment_name)) {
                    $file = $request->file($attachment_name);
                    $filename = 'attachment_' . $i . '_' . Carbon::now()->format('YmdHis') . '.' . $file->getClientOriginalExtension();
                    
                    // Delete old attachment if it exists
                    $old_attachment = $old_attachments->$attachment_name;
                    if ($old_attachment) {
                        $terms = [
                            '1' => 'Employee Form',
                            '2' => 'CNIC Front Image',
                            '3' => 'CNIC Back Image',
                            '4' => 'Profile Picture',
                            '5' => 'Attachment 5'
                        ];
                        $changedFields[] = $terms[$i] . ' ' . 'Changed at' . ' ' .  now()->toDateTimeString();
                        Storage::disk('public')->delete($old_attachment);
                    }
                    
                    // Store new attachment
                    $attachmentDirectory = $baseDirectory . '/' . $attachment_name;
                    Storage::disk('public')->putFileAs($attachmentDirectory, $file, $filename);
                    
                    // Update attachment field in the database
                    $old_attachments->$attachment_name = $attachmentDirectory . '/' . $filename;
                }
            }
            $old_attachments->updated_by = $admin->id;
            $old_attachments->save();
        } else {
            // Create new attachments
            $new_attachments = new RetailUserAttachment();
            $new_attachments->retail_user_id = $retail_user->id;
            
            for ($i = 1; $i <= 5; $i++) {
                $attachment_name = 'attachment_' . $i;
                if ($request->hasFile($attachment_name)) {
                    $file = $request->file($attachment_name);
                    $filename = 'attachment_' . $i . '_' . Carbon::now()->format('YmdHis') . '.' . $file->getClientOriginalExtension();
                    
                    // Store new attachment
                    $attachmentDirectory = $baseDirectory . '/' . $attachment_name;
                    Storage::disk('public')->putFileAs($attachmentDirectory, $file, $filename);
                    
                    // Update attachment field in the database
                    $new_attachments->$attachment_name = $attachmentDirectory . '/' . $filename;
                }
            }
            $new_attachments->updated_by = $admin->id;
            $new_attachments->save();
        }
        if(!empty($changedFields)) {
            self::retail_logs(Auth::id(), $changedFields, $retail_user->id, $screen_name = 'Retail User');
        }
        
        return redirect()->back()->with('success', 'Retail User Updated Successfully!');
    }

    public function retail_user_attachments(Request $request){
        $retailUserId = $request->retail_user_id;
        $retail_user_attachment = RetailUserAttachment::where('retail_user_id', $retailUserId)->first();
        if ($retail_user_attachment != null){
            $data = $retail_user_attachment;
        } else {
            $data = null;
        }
        
        return response()->json(['data' => $data]);
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

            'trax_box_2_range_up.*' => 'required|numeric',
            'trax_box_2_range_down.*' => 'required|numeric',
            'trax_box_2_wc.*' => 'required|numeric',
            'trax_box_2_sz.*' => 'required|numeric',
            'trax_box_2_dz.*' => 'required|numeric',

            'trax_box_5_range_up.*' => 'required|numeric',
            'trax_box_5_range_down.*' => 'required|numeric',
            'trax_box_5_wc.*' => 'required|numeric',
            'trax_box_5_sz.*' => 'required|numeric',
            'trax_box_5_dz.*' => 'required|numeric',

            'trax_box_10_range_up.*' => 'required|numeric',
            'trax_box_10_range_down.*' => 'required|numeric',
            'trax_box_10_wc.*' => 'required|numeric',
            'trax_box_10_sz.*' => 'required|numeric',
            'trax_box_10_dz.*' => 'required|numeric',

            'trax_box_15_range_up.*' => 'required|numeric',
            'trax_box_15_range_down.*' => 'required|numeric',
            'trax_box_15_wc.*' => 'required|numeric',
            'trax_box_15_sz.*' => 'required|numeric',
            'trax_box_15_dz.*' => 'required|numeric',

            'trax_box_20_range_up.*' => 'required|numeric',
            'trax_box_20_range_down.*' => 'required|numeric',
            'trax_box_20_wc.*' => 'required|numeric',
            'trax_box_20_sz.*' => 'required|numeric',
            'trax_box_20_dz.*' => 'required|numeric',

            'trax_box_30_range_up.*' => 'required|numeric',
            'trax_box_30_range_down.*' => 'required|numeric',
            'trax_box_30_wc.*' => 'required|numeric',
            'trax_box_30_sz.*' => 'required|numeric',
            'trax_box_30_dz.*' => 'required|numeric',
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


        $flyer = RetailStandardRates::where('shipping_mode_id', 6)->get();

        if ($flyer->isEmpty()) {

            foreach ($request->flyer_range_up as $index => $flyer_range_up) {

                $retail = new RetailStandardRates();
                $retail->range_up = $request->flyer_range_up[$index];
                $retail->range_down = $request->flyer_range_down[$index];
                $retail->shipping_mode_id = 6;
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


        $hdocs = RetailStandardRates::where('shipping_mode_id', 7)->get();

        if ($hdocs->isEmpty()) {

            foreach ($request->hdocs_range_up as $index => $hdocs_range_up) {

                $retail = new RetailStandardRates();
                $retail->range_up = $request->hdocs_range_up[$index];
                $retail->range_down = $request->hdocs_range_down[$index];
                $retail->shipping_mode_id = 7;
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


        $trax_box_2kg = RetailStandardRates::where('shipping_mode_id', 5)->where('trax_box_id',1)->get();
        $trax_box_5kg = RetailStandardRates::where('shipping_mode_id', 5)->where('trax_box_id',2)->get();
        $trax_box_10kg = RetailStandardRates::where('shipping_mode_id', 5)->where('trax_box_id',3)->get();
        $trax_box_15kg = RetailStandardRates::where('shipping_mode_id', 5)->where('trax_box_id',4)->get();
        $trax_box_20kg = RetailStandardRates::where('shipping_mode_id', 5)->where('trax_box_id',5)->get();
        $trax_box_30kg = RetailStandardRates::where('shipping_mode_id', 5)->where('trax_box_id',6)->get();

       /* if ($trax_box->isEmpty()) {

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
        }*/

        if ($trax_box_2kg->isEmpty()) {

            foreach ($request->trax_box_2_range_up as $index => $trax_box_2_range_up) {

                $retail = new RetailStandardRates();
                $retail->range_up = $request->trax_box_2_range_up[$index];
                $retail->range_down = $request->trax_box_2_range_down[$index];
                $retail->shipping_mode_id = 5;
                if (isset($request->trax_box_2_kg_range[$index])) {
                    $retail->kg_range = $request->trax_box_2_kg_range[$index];
                    $retail->weight_addition = 1;
                } else {
                    $retail->kg_range = 0;
                    $retail->weight_addition = 0;
                }
                $retail->trax_box_id = 1;
                $retail->within_city = $request->trax_box_2_wc[$index];
                $retail->same_zone = $request->trax_box_2_sz[$index];
                $retail->different_zone = $request->trax_box_2_dz[$index];
                $retail->save();
            }
        }

        if ($trax_box_5kg->isEmpty()) {

            foreach ($request->trax_box_5_range_up as $index => $trax_box_5_range_up) {

                $retail = new RetailStandardRates();
                $retail->range_up = $request->trax_box_5_range_up[$index];
                $retail->range_down = $request->trax_box_5_range_down[$index];
                $retail->shipping_mode_id = 5;
                if (isset($request->trax_box_5_kg_range[$index])) {
                    $retail->kg_range = $request->trax_box_5_kg_range[$index];
                    $retail->weight_addition = 1;
                } else {
                    $retail->kg_range = 0;
                    $retail->weight_addition = 0;
                }
                $retail->trax_box_id = 2;
                $retail->within_city = $request->trax_box_5_wc[$index];
                $retail->same_zone = $request->trax_box_5_sz[$index];
                $retail->different_zone = $request->trax_box_5_dz[$index];
                $retail->save();
            }
        }

        if ($trax_box_10kg->isEmpty()) {

            foreach ($request->trax_box_10_range_up as $index => $trax_box_10_range_up) {

                $retail = new RetailStandardRates();
                $retail->range_up = $request->trax_box_10_range_up[$index];
                $retail->range_down = $request->trax_box_10_range_down[$index];
                $retail->shipping_mode_id = 5;
                if (isset($request->trax_box_10_kg_range[$index])) {
                    $retail->kg_range = $request->trax_box_10_kg_range[$index];
                    $retail->weight_addition = 1;
                } else {
                    $retail->kg_range = 0;
                    $retail->weight_addition = 0;
                }
                $retail->trax_box_id = 3;
                $retail->within_city = $request->trax_box_10_wc[$index];
                $retail->same_zone = $request->trax_box_10_sz[$index];
                $retail->different_zone = $request->trax_box_10_dz[$index];
                $retail->save();
            }
        }

        if ($trax_box_15kg->isEmpty()) {

            foreach ($request->trax_box_15_range_up as $index => $trax_box_15_range_up) {

                $retail = new RetailStandardRates();
                $retail->range_up = $request->trax_box_15_range_up[$index];
                $retail->range_down = $request->trax_box_15_range_down[$index];
                $retail->shipping_mode_id = 5;
                if (isset($request->trax_box_15_kg_range[$index])) {
                    $retail->kg_range = $request->trax_box_15_kg_range[$index];
                    $retail->weight_addition = 1;
                } else {
                    $retail->kg_range = 0;
                    $retail->weight_addition = 0;
                }
                $retail->trax_box_id = 4;
                $retail->within_city = $request->trax_box_15_wc[$index];
                $retail->same_zone = $request->trax_box_15_sz[$index];
                $retail->different_zone = $request->trax_box_15_dz[$index];
                $retail->save();
            }
        }

        if ($trax_box_20kg->isEmpty()) {

            foreach ($request->trax_box_20_range_up as $index => $trax_box_20_range_up) {

                $retail = new RetailStandardRates();
                $retail->range_up = $request->trax_box_20_range_up[$index];
                $retail->range_down = $request->trax_box_20_range_down[$index];
                $retail->shipping_mode_id = 5;
                if (isset($request->trax_box_20_kg_range[$index])) {
                    $retail->kg_range = $request->trax_box_20_kg_range[$index];
                    $retail->weight_addition = 1;
                } else {
                    $retail->kg_range = 0;
                    $retail->weight_addition = 0;
                }
                $retail->trax_box_id = 5;
                $retail->within_city = $request->trax_box_20_wc[$index];
                $retail->same_zone = $request->trax_box_20_sz[$index];
                $retail->different_zone = $request->trax_box_20_dz[$index];
                $retail->save();
            }
        }

        if ($trax_box_30kg->isEmpty()) {

            foreach ($request->trax_box_30_range_up as $index => $trax_box_30_range_up) {

                $retail = new RetailStandardRates();
                $retail->range_up = $request->trax_box_30_range_up[$index];
                $retail->range_down = $request->trax_box_30_range_down[$index];
                $retail->shipping_mode_id = 5;
                if (isset($request->trax_box_30_kg_range[$index])) {
                    $retail->kg_range = $request->trax_box_30_kg_range[$index];
                    $retail->weight_addition = 1;
                } else {
                    $retail->kg_range = 0;
                    $retail->weight_addition = 0;
                }
                $retail->trax_box_id = 6;
                $retail->within_city = $request->trax_box_30_wc[$index];
                $retail->same_zone = $request->trax_box_30_sz[$index];
                $retail->different_zone = $request->trax_box_30_dz[$index];
                $retail->save();
            }
        }

        return redirect()->route('admin.retail.rates.edit')->with('success', 'Rates added');
    }

    public function standard_rates_edit()
    {
        $saver_plus = RetailStandardRates::where('shipping_mode_id', 1)->get();
        $rush_data = RetailStandardRates::where('shipping_mode_id', 2)->get();
        $cod_data = RetailStandardRates::where('shipping_mode_id', 3)->get();
        $swift = RetailStandardRates::where('shipping_mode_id', 4)->get();
        $flyers = RetailStandardRates::where('shipping_mode_id', 6)->get();
        $hard_docs = RetailStandardRates::where('shipping_mode_id', 7)->get();
      /*  $trax_box = RetailStandardRates::join('retail_trax_boxes as rtb', 'rtb.id', '=', 'retail_standard_rates.trax_box_id')->where('shipping_mode_id', 5)->whereNotNull('retail_standard_rates.trax_box_id')->select(['retail_standard_rates.range_up', 'retail_standard_rates.range_down', 'retail_standard_rates.weight_addition as weight_addition', 'retail_standard_rates.shipping_mode_id', 'retail_standard_rates.trax_box_id', 'retail_standard_rates.kg_range', 'retail_standard_rates.within_city', 'retail_standard_rates.same_zone', 'retail_standard_rates.different_zone', 'rtb.name as weight'])->get();*/
        $trax_box_2kg = RetailStandardRates::where('shipping_mode_id', 5)->where('trax_box_id',1)->get();
        $trax_box_5kg = RetailStandardRates::where('shipping_mode_id', 5)->where('trax_box_id',2)->get();
        $trax_box_10kg = RetailStandardRates::where('shipping_mode_id', 5)->where('trax_box_id',3)->get();
        $trax_box_15kg = RetailStandardRates::where('shipping_mode_id', 5)->where('trax_box_id',4)->get();
        $trax_box_20kg = RetailStandardRates::where('shipping_mode_id', 5)->where('trax_box_id',5)->get();
        $trax_box_30kg = RetailStandardRates::where('shipping_mode_id', 5)->where('trax_box_id',6)->get();

        return view('admin.retail.users.rates.edit', compact('saver_plus', 'swift', 'rush_data', 'cod_data', 'flyers', 'hard_docs', 'trax_box_2kg', 'trax_box_5kg', 'trax_box_10kg', 'trax_box_15kg', 'trax_box_20kg', 'trax_box_30kg'));
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

            'trax_box_2_range_up.*.required' => 'The trax box for 2kg range up field is required.',
            'trax_box_2_range_up.*.numeric.*' => 'The trax box for 2kg range up field must be numeric or decimal.',
            'trax_box_2_range_down.*.required' => 'The trax box for 2kg range down field is required.',
            'trax_box_2_range_down.*.numeric.*' => 'The trax box for 2kg range down field must be numeric or decimal.',
            'trax_box_2_wc.*.required' => 'The trax box for 2kg within city field is required.',
            'trax_box_2_wc.*.numeric.*' => 'The trax box for 2kg within city field must be numeric or decimal.',
            'trax_box_2_sz.*.required' => 'The trax box for 2kg same zone field is required.',
            'trax_box_2_sz.*.numeric.*' => 'The trax box for 2kg same zone field must be numeric or decimal.',
            'trax_box_2_dz.*.required' => 'The trax box for 2kg different zone field is required.',
            'trax_box_2_dz.*.numeric.*' => 'The trax box for 2kg different zone field must be numeric or decimal.',

            'trax_box_5_range_up.*.required' => 'The trax box for 5kg range up field is required.',
            'trax_box_5_range_up.*.numeric.*' => 'The trax box for 5kg range up field must be numeric or decimal.',
            'trax_box_5_range_down.*.required' => 'The trax box for 5kg range down field is required.',
            'trax_box_5_range_down.*.numeric.*' => 'The trax box for 5kg range down field must be numeric or decimal.',
            'trax_box_5_wc.*.required' => 'The trax box for 5kg within city field is required.',
            'trax_box_5_wc.*.numeric.*' => 'The trax box for 5kg within city field must be numeric or decimal.',
            'trax_box_5_sz.*.required' => 'The trax box for 5kg same zone field is required.',
            'trax_box_5_sz.*.numeric.*' => 'The trax box for 5kg same zone field must be numeric or decimal.',
            'trax_box_5_dz.*.required' => 'The trax box for 5kg different zone field is required.',
            'trax_box_5_dz.*.numeric.*' => 'The trax box for 5kg different zone field must be numeric or decimal.',

            'trax_box_10_range_up.*.required' => 'The trax box for 10kg range up field is required.',
            'trax_box_10_range_up.*.numeric.*' => 'The trax box for 10kg range up field must be numeric or decimal.',
            'trax_box_10_range_down.*.required' => 'The trax box for 10kg range down field is required.',
            'trax_box_10_range_down.*.numeric.*' => 'The trax box for 10kg range down field must be numeric or decimal.',
            'trax_box_10_wc.*.required' => 'The trax box for 10kg within city field is required.',
            'trax_box_10_wc.*.numeric.*' => 'The trax box for 10kg within city field must be numeric or decimal.',
            'trax_box_10_sz.*.required' => 'The trax box for 10kg same zone field is required.',
            'trax_box_10_sz.*.numeric.*' => 'The trax box for 10kg same zone field must be numeric or decimal.',
            'trax_box_10_dz.*.required' => 'The trax box for 10kg different zone field is required.',
            'trax_box_10_dz.*.numeric.*' => 'The trax box for 10kg different zone field must be numeric or decimal.',

            'trax_box_15_range_up.*.required' => 'The trax box for 15kg range up field is required.',
            'trax_box_15_range_up.*.numeric.*' => 'The trax box for 15kg range up field must be numeric or decimal.',
            'trax_box_15_range_down.*.required' => 'The trax box for 15kg range down field is required.',
            'trax_box_15_range_down.*.numeric.*' => 'The trax box for 15kg range down field must be numeric or decimal.',
            'trax_box_15_wc.*.required' => 'The trax box for 15kg within city field is required.',
            'trax_box_15_wc.*.numeric.*' => 'The trax box for 15kg within city field must be numeric or decimal.',
            'trax_box_15_sz.*.required' => 'The trax box for 15kg same zone field is required.',
            'trax_box_15_sz.*.numeric.*' => 'The trax box for 15kg same zone field must be numeric or decimal.',
            'trax_box_15_dz.*.required' => 'The trax box for 15kg different zone field is required.',
            'trax_box_15_dz.*.numeric.*' => 'The trax box for 15kg different zone field must be numeric or decimal.',

            'trax_box_20_range_up.*.required' => 'The trax box for 20kg range up field is required.',
            'trax_box_20_range_up.*.numeric.*' => 'The trax box for 20kg range up field must be numeric or decimal.',
            'trax_box_20_range_down.*.required' => 'The trax box for 20kg range down field is required.',
            'trax_box_20_range_down.*.numeric.*' => 'The trax box for 20kg range down field must be numeric or decimal.',
            'trax_box_20_wc.*.required' => 'The trax box for 20kg within city field is required.',
            'trax_box_20_wc.*.numeric.*' => 'The trax box for 20kg within city field must be numeric or decimal.',
            'trax_box_20_sz.*.required' => 'The trax box for 20kg same zone field is required.',
            'trax_box_20_sz.*.numeric.*' => 'The trax box for 20kg same zone field must be numeric or decimal.',
            'trax_box_20_dz.*.required' => 'The trax box for 20kg different zone field is required.',
            'trax_box_20_dz.*.numeric.*' => 'The trax box for 20kg different zone field must be numeric or decimal.',

            'trax_box_30_range_up.*.required' => 'The trax box for 30kg range up field is required.',
            'trax_box_30_range_up.*.numeric.*' => 'The trax box for 30kg range up field must be numeric or decimal.',
            'trax_box_30_range_down.*.required' => 'The trax box for 30kg range down field is required.',
            'trax_box_30_range_down.*.numeric.*' => 'The trax box for 30kg range down field must be numeric or decimal.',
            'trax_box_30_wc.*.required' => 'The trax box for 30kg within city field is required.',
            'trax_box_30_wc.*.numeric.*' => 'The trax box for 30kg within city field must be numeric or decimal.',
            'trax_box_30_sz.*.required' => 'The trax box for 30kg same zone field is required.',
            'trax_box_30_sz.*.numeric.*' => 'The trax box for 30kg same zone field must be numeric or decimal.',
            'trax_box_30_dz.*.required' => 'The trax box for 30kg different zone field is required.',
            'trax_box_30_dz.*.numeric.*' => 'The trax box for 30kg different zone field must be numeric or decimal.',
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

            'trax_box_2kg_range_up.*' => 'required|numeric',
            'trax_box_2kg_range_down.*' => 'required|numeric',
            'trax_box_2kg_wc.*' => 'required|numeric',
            'trax_box_2kg_sz.*' => 'required|numeric',
            'trax_box_2kg_dz.*' => 'required|numeric',

            'trax_box_5kg_range_up.*' => 'required|numeric',
            'trax_box_5kg_range_down.*' => 'required|numeric',
            'trax_box_5kg_wc.*' => 'required|numeric',
            'trax_box_5kg_sz.*' => 'required|numeric',
            'trax_box_5kg_dz.*' => 'required|numeric',

            'trax_box_10kg_range_up.*' => 'required|numeric',
            'trax_box_10kg_range_down.*' => 'required|numeric',
            'trax_box_10kg_wc.*' => 'required|numeric',
            'trax_box_10kg_sz.*' => 'required|numeric',
            'trax_box_10kg_dz.*' => 'required|numeric',

            'trax_box_15kg_range_up.*' => 'required|numeric',
            'trax_box_15kg_range_down.*' => 'required|numeric',
            'trax_box_15kg_wc.*' => 'required|numeric',
            'trax_box_15kg_sz.*' => 'required|numeric',
            'trax_box_15kg_dz.*' => 'required|numeric',

            'trax_box_20kg_range_up.*' => 'required|numeric',
            'trax_box_20kg_range_down.*' => 'required|numeric',
            'trax_box_20kg_wc.*' => 'required|numeric',
            'trax_box_20kg_sz.*' => 'required|numeric',
            'trax_box_20kg_dz.*' => 'required|numeric',

            'trax_box_30kg_range_up.*' => 'required|numeric',
            'trax_box_30kg_range_down.*' => 'required|numeric',
            'trax_box_30kg_wc.*' => 'required|numeric',
            'trax_box_30kg_sz.*' => 'required|numeric',
            'trax_box_30kg_dz.*' => 'required|numeric',
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

        RetailStandardRates::where('shipping_mode_id', 6)->delete();


        foreach ($request->flyer_range_up as $index => $flyer_range_up) {

            $retail = new RetailStandardRates();
            $retail->range_up = $request->flyer_range_up[$index];
            $retail->range_down = $request->flyer_range_down[$index];
            $retail->shipping_mode_id = 6;
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

        RetailStandardRates::where('shipping_mode_id', 7)->delete();

        foreach ($request->hdocs_range_up as $index => $hdocs_range_up) {

            $retail = new RetailStandardRates();
            $retail->range_up = $request->hdocs_range_up[$index];
            $retail->range_down = $request->hdocs_range_down[$index];
            $retail->shipping_mode_id = 7;
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

        RetailStandardRates::where('shipping_mode_id', 5)->delete();

        foreach ($request->trax_box_2_range_up as $index => $trax_box_2_range_up) {

            $retail = new RetailStandardRates();
            $retail->range_up = $request->trax_box_2_range_up[$index];
            $retail->range_down = $request->trax_box_2_range_down[$index];
            $retail->shipping_mode_id = 5;
            if (isset($request->trax_box_2_kg_range[$index])) {
                $retail->kg_range = $request->trax_box_2_kg_range[$index];
                $retail->weight_addition = 1;
            } else {
                $retail->kg_range = 0;
                $retail->weight_addition = 0;
            }
            $retail->trax_box_id = 1;
            $retail->within_city = $request->trax_box_2_wc[$index];
            $retail->same_zone = $request->trax_box_2_sz[$index];
            $retail->different_zone = $request->trax_box_2_dz[$index];
            $retail->save();
        }

        foreach ($request->trax_box_5_range_up as $index => $trax_box_5_range_up) {

            $retail = new RetailStandardRates();
            $retail->range_up = $request->trax_box_5_range_up[$index];
            $retail->range_down = $request->trax_box_5_range_down[$index];
            $retail->shipping_mode_id = 5;
            if (isset($request->trax_box_5_kg_range[$index])) {
                $retail->kg_range = $request->trax_box_5_kg_range[$index];
                $retail->weight_addition = 1;
            } else {
                $retail->kg_range = 0;
                $retail->weight_addition = 0;
            }
            $retail->trax_box_id = 2;
            $retail->within_city = $request->trax_box_5_wc[$index];
            $retail->same_zone = $request->trax_box_5_sz[$index];
            $retail->different_zone = $request->trax_box_5_dz[$index];
            $retail->save();
        }

        foreach ($request->trax_box_10_range_up as $index => $trax_box_10_range_up) {

            $retail = new RetailStandardRates();
            $retail->range_up = $request->trax_box_10_range_up[$index];
            $retail->range_down = $request->trax_box_10_range_down[$index];
            $retail->shipping_mode_id = 5;
            if (isset($request->trax_box_10_kg_range[$index])) {
                $retail->kg_range = $request->trax_box_10_kg_range[$index];
                $retail->weight_addition = 1;
            } else {
                $retail->kg_range = 0;
                $retail->weight_addition = 0;
            }
            $retail->trax_box_id = 3;
            $retail->within_city = $request->trax_box_10_wc[$index];
            $retail->same_zone = $request->trax_box_10_sz[$index];
            $retail->different_zone = $request->trax_box_10_dz[$index];
            $retail->save();
        }


        foreach ($request->trax_box_15_range_up as $index => $trax_box_15_range_up) {

            $retail = new RetailStandardRates();
            $retail->range_up = $request->trax_box_15_range_up[$index];
            $retail->range_down = $request->trax_box_15_range_down[$index];
            $retail->shipping_mode_id = 5;
            if (isset($request->trax_box_15_kg_range[$index])) {
                $retail->kg_range = $request->trax_box_15_kg_range[$index];
                $retail->weight_addition = 1;
            } else {
                $retail->kg_range = 0;
                $retail->weight_addition = 0;
            }
            $retail->trax_box_id = 4;
            $retail->within_city = $request->trax_box_15_wc[$index];
            $retail->same_zone = $request->trax_box_15_sz[$index];
            $retail->different_zone = $request->trax_box_15_dz[$index];
            $retail->save();
        }

        foreach ($request->trax_box_20_range_up as $index => $trax_box_20_range_up) {

            $retail = new RetailStandardRates();
            $retail->range_up = $request->trax_box_20_range_up[$index];
            $retail->range_down = $request->trax_box_20_range_down[$index];
            $retail->shipping_mode_id = 5;
            if (isset($request->trax_box_20_kg_range[$index])) {
                $retail->kg_range = $request->trax_box_20_kg_range[$index];
                $retail->weight_addition = 1;
            } else {
                $retail->kg_range = 0;
                $retail->weight_addition = 0;
            }
            $retail->trax_box_id = 5;
            $retail->within_city = $request->trax_box_20_wc[$index];
            $retail->same_zone = $request->trax_box_20_sz[$index];
            $retail->different_zone = $request->trax_box_20_dz[$index];
            $retail->save();
        }

        foreach ($request->trax_box_30_range_up as $index => $trax_box_30_range_up) {

            $retail = new RetailStandardRates();
            $retail->range_up = $request->trax_box_30_range_up[$index];
            $retail->range_down = $request->trax_box_30_range_down[$index];
            $retail->shipping_mode_id = 5;
            if (isset($request->trax_box_30_kg_range[$index])) {
                $retail->kg_range = $request->trax_box_30_kg_range[$index];
                $retail->weight_addition = 1;
            } else {
                $retail->kg_range = 0;
                $retail->weight_addition = 0;
            }
            $retail->trax_box_id = 6;
            $retail->within_city = $request->trax_box_30_wc[$index];
            $retail->same_zone = $request->trax_box_30_sz[$index];
            $retail->different_zone = $request->trax_box_30_dz[$index];
            $retail->save();
        }



        return redirect()->route('admin.retail.rates.edit')->with('success', 'Rates Updated');


    }

    public function retail_history($id){
        $retail_user_history = RetailUserHistory::where('retail_user_id', $id)->get();
        $retail_user_data = RetailUser::whereIn('id', $retail_user_history->pluck('retail_user_id'))->first();

        if ($retail_user_data != null) {
            $retail_user = $retail_user_data->name;
        } else {
            $retail_user = RetailUser::where('id', $id)->first()->name;
        }

        return view('admin.retail.users.history', [
            'retail_user_history' => $retail_user_history,
            'retail_user' => $retail_user
        ]);
    }

    public function retail_user_excel_sheet($id)
    {
        $user = RetailUser::where('id', $id)
            ->where('category', 2)
            ->first();

        $retail_center = RetailTraxCenter::where('id', $user->category_id)->first();
        $retail_user_commissions = RetailUserProductPercentage::where('retail_user_id', $user->id)->get();
        $city = City::where('id', $user->city_id)->first();
        $retail_shipping_modes = RetailShippingMode::whereIn('id', $retail_user_commissions->pluck('retail_shipping_mode_id'))->pluck('name');
        $family_info = RetailUserFamilyInformation::where('retail_user_id', $user->id)->get();
        if ($family_info->isNotEmpty()) {
            $spouse_dob = $family_info[3]->family_member_name;
        } else {
            $spouse_dob = '';
        }
        $userDetails = [
            [
                'Retail Center',
                'Retail User',
                'Phone',
                'CNIC',
                'Address',
                'City'
            ]
        ];

        $familyDetails = [
            [
                'Joining Date',
                'Salary',
                'Family Members',
                'Spouse Date of Birth'
            ]
        ];

        $commissionDetailsWithName = [
            [
                'Product',
                'Commission Percentage',
            ]
        ];

        $userDetails[] = [
            $retail_center->name,
            $user->name,
            $user->phone_no,
            $user->cnic,
            $user->address, 
            $city->name,
        ];

        $isFirstFamilyMember = true;
        foreach ($family_info as $index => $family) {
            if ($index === 3) {
                // Skip adding the spouse's date of birth here
                continue;
            }

            if ($isFirstFamilyMember) {
                $familyDetails[] = [
                    $family->agreement_start_date,
                    $family->salary,
                    $family->family_member_name,
                    $spouse_dob
                ];
                $isFirstFamilyMember = false;
            } else {
                $familyDetails[] = [
                    '',
                    '',
                    $family->family_member_name,
                ];
            }
        }

        foreach ($retail_user_commissions as $index => $commission) {
            $commissionDetailsWithName[] = [
                $retail_shipping_modes[$index] ?? '',
                $commission->product_percentage . '%',
            ];
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // user details
        $currentRow = 1;
        foreach ($userDetails as $index => $detail) {
            $sheet->fromArray($detail, null, 'A' . ($currentRow + $index));
        }

        // family details
        $currentRow += count($userDetails) + 1;
        foreach ($familyDetails as $index => $detail) {
            $sheet->fromArray($detail, null, 'A' . ($currentRow + $index));
        }

        // commission details with names
        $currentRow += count($familyDetails) + 1;
        foreach ($commissionDetailsWithName as $index => $detail) {
            $sheet->fromArray($detail, null, 'A' . ($currentRow + $index));
        }

        $spreadsheet->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $user->name . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    public function franchise_excel_sheet($id)
    {
        $user = RetailUser::where('id', $id)
            ->where('category', 1)
            ->first();
        $franchise = RetailFranchise::where('id', $user->category_id)->first();
        $city = City::where('id', $user->city_id)->first();
        $agreement_date = RetailUserFamilyInformation::where('retail_user_id', $user->id)->first();
        $family_info = RetailUserFamilyInformation::where('retail_user_id', $user->id)->get();

        $userDetails = [
            [
                'Franchise',
                'Franchise User',
                'Phone',
                'CNIC',
                'Address',
                'City',
                'Agreement Start Date'
            ]
        ];
    
        $familyDetails = [
            [
                'Family Members',
            ]
        ];
    
        $userDetails[] = [
            $franchise->name,
            $user->name,
            $user->phone_no,
            $user->cnic,
            $user->address, 
            $city->name,
            $agreement_date ? $agreement_date->agreement_start_date : ''
        ];
    
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        // user details
        $currentRow = 1;
        foreach ($userDetails as $index => $detail) {
            $sheet->fromArray($detail, null, 'A' . ($currentRow + $index));
        }
    
        // family members
        $currentRow += count($userDetails) + 1;
        $familyCount = 0;
        foreach ($family_info as $index => $family) {
            if ($familyCount >= 2) {
                break;
            }
    
            $familyDetails[] = [
                $family->family_member_name,
            ];
            $familyCount++;
        }
    
        foreach ($familyDetails as $index => $detail) {
            $sheet->fromArray($detail, null, 'A' . ($currentRow + $index));
        }
    
        $spreadsheet->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $user->name . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
    

    public function franchise_details_excel_sheet($id)
    {
        $retail_franchise = RetailFranchise::where('id', $id)->first() ?? new RetailFranchise();
        $retail_franchise_commission = RetailFranchiseProductPercentage::where('franchise_id', $retail_franchise->id)->get();
        $retail_franchise_charges = RetailFranchiseCharge::where('franchise_id', $retail_franchise->id)->first() ?? new RetailFranchiseCharge();
        $retail_shipping_modes = RetailShippingMode::whereIn('id', $retail_franchise_commission->pluck('retail_shipping_mode_id'))->pluck('name')->toArray();
        $default_hub = City::where('hub_id', $retail_franchise->default_hub)->first();

        $franchiseDetails = [
            [
                'Franchise Name',
                'Phone number',
                'Email',
                'CNIC',
                'Hub',
                'Latitude',
                'Longitude',
                'Insurance',
                'Discount',
                'Withholding Tax',
                'Commission GST Deduction',
            ],
            [
                $retail_franchise->name,
                $retail_franchise->phone_no,
                $retail_franchise->email,
                $retail_franchise->cnic,
                $default_hub->name,
                $retail_franchise->location_latitude,
                $retail_franchise->location_longitude,
                ($retail_franchise->insurance ?? '0') . '%',
                ($retail_franchise->discount ?? '0') . '%',
                $retail_franchise_charges->franchise_withholding ? $retail_franchise_charges->franchise_withholding . '%' : '0%',
                $retail_franchise_charges->franchise_deduction ? $retail_franchise_charges->franchise_deduction . '%' : '0%',
            ],
        ];
        

        $commissionDetails = [
            [
                'Product',
                'Product Commission(%)'
            ]
        ];
        
        foreach ($retail_franchise_commission as $index => $commission) {
            $commissionDetails[] = [
                $retail_shipping_modes[$index] ?? '',
                $commission->product_percentage . '%',
            ];
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // franchise details
        $currentRow = 1;
        foreach ($franchiseDetails as $index => $detail) {
            $sheet->fromArray($detail, null, 'A' . ($currentRow + $index));
        }

        // commission details
        $currentRow += count($franchiseDetails) + 1;
        foreach ($commissionDetails as $index => $detail) {
            $sheet->fromArray($detail, null, 'A' . ($currentRow + $index));
        }

        $spreadsheet->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $retail_franchise->name . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    public function trax_center_details_excel_sheet($id)
    {
        $trax_center = RetailTraxCenter::where('id', $id)->first() ?? new RetailTraxCenter();
        $trax_center_charges = TraxCenterAttachment::where('retail_trax_center_id', $trax_center->id)->first() ?? new RetailFranchiseCharge();
        $default_hub = City::where('hub_id', $trax_center->default_hub)->first();

        // dd($trax_center, $trax_center_charges);

        $franchiseDetails = [
            [
                'Franchise Name',
                'Phone number',
                'Email',
                'CNIC',
                'Hub',
                'Latitude',
                'Longitude',
                'Insurance',
                'Discount',
                'Advance Amount',
                'Rental Amount',
                'Landlord Name',
                'Landlord Contact Number',
                'Shop Address',
                'Agreement Start Date',
                'Agreement End Date',
            ],
            [
                $trax_center->name,
                $trax_center->phone_no,
                $trax_center->email,
                $trax_center->cnic,
                $default_hub->name,
                $trax_center->location_latitude,
                $trax_center->location_longitude,
                ($trax_center->insurance ?? '0') . '%',
                ($trax_center->discount ?? '0') . '%',
                $trax_center_charges->advance_amount,
                $trax_center_charges->rental,
                $trax_center_charges->landlord_name,
                $trax_center_charges->landlord_contact_number,
                $trax_center_charges->shop_address,
                $trax_center_charges->agreement_start_date,
                $trax_center_charges->agreement_end_date,
            ],
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // franchise details
        $currentRow = 1;
        foreach ($franchiseDetails as $index => $detail) {
            $sheet->fromArray($detail, null, 'A' . ($currentRow + $index));
        }

        $spreadsheet->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $trax_center->name . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    public function show_commission(Request $request) 
    {
        $request->validate([
            'id' => 'required'
        ]);
        $ids = explode(',', $request->id);
        $ids = array_map('trim', $ids);
        $data = RetailFranchiseCommission::whereIn('id', $ids)->get();
        return response()->json([
            'data' => $data
        ]);
    }

    public function commission_payment(Request $request) 
    {
        $request->validate([
            'id' => 'required'
        ]);
        $ids = explode(',', $request->id);
        $ids = array_map('trim', $ids);
        $data = RetailFranchiseCommission::whereIn('id', $ids)->get();
        if ($data->isEmpty()) {
            return response()->json([
                'error' => 'No data found for the provided IDs.'
            ], 404);
        }
        $franchiseIds = $data->pluck('franchise_id')->unique();
        if ($franchiseIds->count() > 1) {
            return response()->json([
                'error' => 'Selected IDs do not belong to the same franchise.',
                'status' => 1
            ], 400);
        }
        foreach($data as $record){
            if ($record->is_paid == 1){
                return response()->json([
                    'error' => 'Payment for this franchise has already been made.',
                    'status' => 2,
                    'error_data' => $record->franchise_name
                ], 400);
            }
            $record->is_paid = 1;
            $record->save();
        }
        return response()->json([
            'data' => $data
        ]);
    }

    public function show_retail_commission(Request $request) 
    {
        $request->validate([
            'id' => 'required'
        ]);
        $ids = explode(',', $request->id);
        $ids = array_map('trim', $ids);
        $data = RetailUserCommission::whereIn('id', $ids)->get();
        return response()->json([
            'data' => $data
        ]);
    }

    public function retail_commission_payment(Request $request) 
    {
        $request->validate([
            'id' => 'required'
        ]);
        $ids = explode(',', $request->id);
        $ids = array_map('trim', $ids);
        $data = RetailUserCommission::whereIn('id', $ids)->get();
        if ($data->isEmpty()) {
            return response()->json([
                'error' => 'No data found for the provided IDs.'
            ], 404);
        }
        $franchiseIds = $data->pluck('franchise_id')->unique();
        if ($franchiseIds->count() > 1) {
            return response()->json([
                'error' => 'Selected data does not belong to the same User.',
                'status' => 1
            ], 400);
        }
        foreach($data as $record){
            if ($record->is_paid == 1){
                return response()->json([
                    'error' => 'Payment for this User has already been made.',
                    'status' => 2,
                    'error_data' => $record->trax_center_name
                ], 400);
            }
            $record->is_paid = 1;
            $record->save();
        }
        return response()->json([
            'data' => $data
        ]);
    }

    public function cnic_status(Request $request){
        $franchiseId = $request->franchise_id;
        $retail_franchise_cnic_status = RetailFranchise::where('id', $franchiseId)
        ->select('cnic_status')
        ->first();
        if ($retail_franchise_cnic_status != null){
            $data = $retail_franchise_cnic_status;
        } else {
            $data = null;
        }
        
        return response()->json(['data' => $data]);
    }

    public function view_logs(Request $request){
        $logs = RetailLog::leftJoin('admins as changed_by', 'retail_logs.changed_by_id', '=', 'changed_by.id')->where('retail_logs.changed_in_record_id', $request->id)->where('retail_logs.screen_name', $request->screen_name)->orderBy('retail_logs.created_at', 'desc')->select('retail_logs.*', 'changed_by.name')->get();
        if ($logs->isNotEmpty()) {
            return response()->json([
                'status' => 0,
                'logs' => $logs
            ]);
        } else {
            return response()->json([
                'status' => 1,
                'message' => 'No logs found..!'
            ]);
        }
    }

    public static function retail_logs($changed_by_id, $data, $changed_in_record_id ,  $screen_name) {
        $record = new RetailLog;
        $record->changed_by_id = $changed_by_id;
        $record->data = implode(', ', $data);
        $record->changed_in_record_id = $changed_in_record_id;
        $record->screen_name = $screen_name;
        $record->save();
    }
}