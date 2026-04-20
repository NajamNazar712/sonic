<?php

namespace App\Http\Controllers\Admins\Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Settings\GeneralSetting;
use Auth;
use Carbon\Carbon;
use App\Http\Models\Shipper\User;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Admins\ActivityTrailController;
use App\Models\NegativePayableAllowShipperZeroCod;
use App\Models\NegativePayableAllowShipperZeroCodLogs;
use App\Http\Models\Admin\GlobalSettings;


class GeneralSettingController extends Controller
{


   

    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function ticker_index()
    {
        $admin_ticker = [
            'description' => '',
            'start_date' => '',
            'start_time' =>  '',
            'start_time_formatted'=> '',
            'end_date' => '',
            'end_time' => '',
            'end_time_formatted'=> ''
        ];
        $shipper_ticker = [
            'description' => '',
            'start_date' => '',
            'start_time' =>  '',
            'start_time_formatted'=> '',
            'end_date' => '',
            'end_time' => '',
            'end_time_formatted'=> ''
        ];

        $settings = GeneralSetting::where('type', 'admin_ticker');

        if ($settings->exists()) {
            $settings = $settings->first();

            $startformated = $this->formatDateTime($settings->start_date);
            $endformated = $this->formatDateTime($settings->end_date);

            $admin_ticker = [
                'description' => $settings->description,
                'start_date' => $startformated['date'],
                'start_time' =>  $startformated['time'],
                'start_time_formatted'=>  $startformated['time_formatted'],
                'end_date' => $endformated['date'],
                'end_time' => $endformated['time'],
                'end_time_formatted'=> $endformated['time_formatted']
            ];
        }

        $settings = GeneralSetting::where('type', 'shipper_ticker');


        if ($settings->exists()) {
            $settings = $settings->first();

            $startformated = $this->formatDateTime($settings->start_date);
            $endformated = $this->formatDateTime($settings->end_date);

                $shipper_ticker = [
                    'description' => $settings->description,
                    'start_date' => $startformated['date'],
                    'start_time' =>  $startformated['time'],
                    'start_time_formatted'=>  $startformated['time_formatted'],
                    'end_date' => $endformated['date'],
                    'end_time' => $endformated['time'],
                    'end_time_formatted'=> $endformated['time_formatted']
                ];


        }
       
       
        return view('admin.settings.ticker')->with(['admin_ticker' => $admin_ticker,'shipper_ticker' => $shipper_ticker]);
    }

    function formatDateTime($date)
    {   
        if(!is_null($date))
        {
            $date= Carbon::parse($date);
            $formattedDate = $date->format('Y-m-d');
            $formattedTime = $date->minute(0)->format('H:i');
            $formattedTime12H = $date->minute(0)->format('g:i A');

            return [
                'date' => $formattedDate,
                'time' => $formattedTime,
                'time_formatted' => $formattedTime12H,
            ];
        }
        return [
              'date' => null,
              'time' => null,
              'time_formatted' => null,
        ];
       
    }

    public function ticker_store(Request $request)
    {

       
       
        $settings = GeneralSetting::where('type', 'admin_ticker');

        if ($settings->exists()) {
            $settings = $settings->first();
        } else {
            $settings = new GeneralSetting();

            $settings->type = 'admin_ticker';
        }

        $start_date = null;
        $end_date = null;
        if(!is_null( $request->admin_start_date) && !is_null( $request->admin_start_time))
        {
            $start_date =Carbon::parse(( $request->admin_start_date . ' ' . $request->admin_start_time))->format('Y-m-d H:i:s');
        }

        if(!is_null( $request->admin_end_date) && !is_null( $request->admin_end_time))
        {
            $end_date =Carbon::parse(( $request->admin_end_date . ' ' . $request->admin_end_time))->format('Y-m-d H:i:s');
        }

        $settings->description = ($request->admin_ticker) ? $request->admin_ticker : '';
        $settings->start_date =  $start_date ;
        $settings->end_date = $end_date ;


        $settings->save();

        $settings = GeneralSetting::where('type', 'shipper_ticker');

        if ($settings->exists()) {
            $settings = $settings->first();
        } else {
            $settings = new GeneralSetting();

            $settings->type = 'shipper_ticker';
        }

        
        $start_date = null;
        $end_date = null;
        if(!is_null( $request->shipper_start_date) && !is_null( $request->shipper_start_time))
        {
            $start_date =Carbon::parse(( $request->shipper_start_date_formatted . ' ' . $request->shipper_start_time))->format('Y-m-d H:i:s');
        }

        if(!is_null( $request->shipper_end_date) && !is_null( $request->shipper_end_time))
        {
            $end_date =Carbon::parse(( $request->shipper_end_date_formatted . ' ' . $request->shipper_end_time))->format('Y-m-d H:i:s');
        }

        $settings->description = $request->shipper_ticker;
        $settings->start_date = $start_date;
        $settings->end_date = $end_date;

        
        $settings->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }
    public function mms_excel_booking_setting_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 747);
        $users= User::where('status',3)->where('blacklist' ,0 )->where('account_type_id',2)->select('id' , 'name')->get();
        $settings = GeneralSetting::where('type', 'mms_excel_booking_setting');
        $mms_excel_booking_setting = array();
        if ($settings->exists())
        {
            $settings = $settings->first();
            $mms_excel_booking_setting = array_map('intval',explode(',' , $settings->description));
        }
        return view('admin.settings.mms_excel_booking_shippers')->with(['users' => $users , 'mms_excel_booking_setting' =>$mms_excel_booking_setting]);
    }
    
    public function mms_excel_booking_setting_store(Request $request)
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 748);
        if ($request->has('users') && count($request->users) > 0) {
            $users = implode(',', $request->users);
        } else{
            $users = null;
        }
        $settings = GeneralSetting::where('type', 'mms_excel_booking_setting');
    
        if ($settings->exists()) {
            $settings = $settings->first();
        } else {
            $settings = new GeneralSetting();
    
            $settings->type = 'mms_excel_booking_setting';
        }
            $settings->description = $users;
            $settings->save();
            
        return redirect()->back()->with('success', 'Settings Updated!');
    }

    function shipper_cap_index()
    {
         ActivityTrailController::createActivityTrailLog(Auth::id(), 752);
        return view('admin.settings.add_shipper_cap');
    }

    function shipper_cap_list()
    {
        $shipper_cap = GeneralSetting::select('id','type','setting_value','description')->where('type','shipper_cap');
        $datatables = Datatables::of($shipper_cap)
           ->addColumn('action', function ($shipper_cap){
                    $dropdown = '<div class="btn-group">
                                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                                <div class="dropdown-menu dropdown-menu-sm">
                                     <button type="button" class="dropdown-item edit_shipper_cap_btn" data-target-id='.$shipper_cap->id.'><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit</div></button>
                                </div></div>';

                    

                    return $dropdown;
           });
        return $datatables->make(true);
    }
 	// public function mobile_check_index() {

    //         $shipper_ids = null;

    //         $shippers = User::select(['id','name'])->where('status',3)->get();

    //         $setting = GeneralSetting::where('type', 'shipper_mobile_check');
    //         if($setting->exists()) {
    //             $setting = $setting->first();
    //             $shipper_ids = explode(',',$setting->setting_value);
    //         } 

    //     return view("admin.settings.shipper.mobile_number_check")->with(['shippers' => $shippers,'shipper_ids' => $shipper_ids]);
    // }

    // public function mobile_check_store(Request $request) {

    //     $setting = GeneralSetting::where('type', 'shipper_mobile_check');
    //     if($setting->exists()) 
    //     {
    //         $setting =  $setting->first();
    //     } else {
    //         $setting = new GeneralSetting();
    //         $setting->type = 'shipper_mobile_check';
    //     }

    //     $shipper_ids = implode(',',$request->shipper_ids);
    //     $setting->setting_value =  $shipper_ids;
    //     $setting->save();
    //     return redirect()->back()->with('success', 'Settings Updated!');

       

      
    // }    

    function shipper_cap_store(Request $request)
    {
       try {

            $shipper_cap = new GeneralSetting();
            $shipper_cap->type = 'shipper_cap';
            $shipper_cap->setting_value = $request->shipper_cap_limit;
            $shipper_cap->description = 'Shipper Paybal Cap Setting';
            $shipper_cap->save();
            return redirect()->back()->with(['status'=>0, 'success'=>'Shipper Cap Inserted successfully']);


       } catch (\Exception $th) {
            return redirect()->back()->with(['status' => 1,  'error' => 'Unable to Insert Shipper Cap']);
       }
        
    }

    function shipper_cap_edit(Request $request)
    {
        if($request->shipper_cap_id)
        {
            $shipper_cap_id = $request->shipper_cap_id;
            $shipper_cap = GeneralSetting::where('id',$shipper_cap_id)->where('type','shipper_cap');
            if($shipper_cap->exists())
            {
                 $shipper_cap = $shipper_cap->first();
                 return response()->json(['status'=>0, 'shipper_cap'=>$shipper_cap]);

            } else {
                 return response()->json(['status'=>1, 'error'=>'Shipper Cap not exists']);
            }


        }
        else {
            return response()->json(['status'=>1, 'error'=>'Something went wrong']);
        }
    }

    function shipper_cap_update(Request $request)
    {

        $validate = Validator::make($request->all(),[
            'edit_shipper_cap_id' => ['required','integer'],
            'edit_shipper_cap_limit' => ['required', 'integer']
        ]);

       if ($validate->fails()) {
            return redirect()->back()->with(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        }else {
             $shipper_cap_id = $request->edit_shipper_cap_id;
             $shipper_cap = GeneralSetting::where('id',$shipper_cap_id)->where('type','shipper_cap');

             if($shipper_cap->exists())
             {
                $shipper_cap = $shipper_cap->first();
                $shipper_cap->setting_value = $request->edit_shipper_cap_limit;
                $shipper_cap->save();
                return redirect()->back()->with(['status'=>0, 'success'=>'Shipper Cap updated successfully']);

             } else {
                 return redirect()->back()->with(['status'=>1, 'error'=>'Shipper Cap not exists']);
             }

        }
    }

    public function zero_cod_shippers_index()
    {
        $shippers = User::where('status', '=', 3)
            ->where('blacklist', 0)
            ->where('account_type_id', 1)
            ->get();
        return view('admin.settings.negative_payable_allow_booking.index')->with(['shippers' => $shippers]);
    }


    public function  zero_cod_shippers_list()
    {
        $zero_cod_shippers = NegativePayableAllowShipperZeroCod::join('users as u', 'negative_payable_allow_shipper_zero_cod.user_id', '=', 'u.id')
        ->leftJoin('admins as a', 'a.id', 'negative_payable_allow_shipper_zero_cod.added_by')
        ->select('u.name as shipper_name', 'negative_payable_allow_shipper_zero_cod.id as id', 'negative_payable_allow_shipper_zero_cod.created_at as created', 'a.name as added_by');

        $datatables = Datatables::of($zero_cod_shippers)
            ->addColumn('action', function ($zero_cod_shippers) {
                if (session('role_id') == 1) {

                    $dropdown = '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                    ';

                    $dropdown .= ' <button type="button" class="dropdown-item remove"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-minus-circle"></i></div><div class="col-9 offset-1">Remove</div></button>';

                    $dropdown .= '</div></div>';
                    return $dropdown;
                } else {
                    return '';
                }
            });
        return $datatables->make(true);
    }

    public function zero_cod_shippers_add(Request $request)
    {
        $shipper_id = $request->input('shipper_id');

        if (empty($shipper_id)) {
            return redirect()->back()->with('error', 'Shipper is required!');
        }

        // Check if shipper already exists
        $exists = NegativePayableAllowShipperZeroCod::where('user_id', $shipper_id)->first();

        if ($exists) {
            return redirect()->back()->with('error', 'Shipper already exists!');
        }
        // Add new shipper
        $newShipper = NegativePayableAllowShipperZeroCod::create([
            'user_id' => $shipper_id,
            'added_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Shipper added successfully!');
    }

    public function zero_cod_shippers_remove(Request $request)
    {
        $shipper = NegativePayableAllowShipperZeroCod::find($request->id);

        if (!$shipper) {
            return redirect()->back()->with('error', 'Shipper not found!');
        }

        // Log the removal with actual added time
        NegativePayableAllowShipperZeroCodLogs::create([
            'user_id'  => $shipper->user_id,
            'added_by' => $shipper->added_by,
            'added_at' =>  $shipper->created_at,
            'removed_by' => auth()->id(),
        ]);

        $shipper->delete();

        return redirect()->back()->with('success', 'Shipper removed and logged successfully!');
    }

    public function zero_cod_shippers_logs_index() 
    {
        return view('admin.settings.negative_payable_allow_booking.logs');
    }

    public function  zero_cod_shippers_logs_list()
    {
        $zero_cod_shippers = NegativePayableAllowShipperZeroCodLogs::join('users as u', 'negative_payable_allow_shipper_zero_cod_logs.user_id', '=', 'u.id')
        ->leftJoin('admins as a', 'a.id', 'negative_payable_allow_shipper_zero_cod_logs.added_by')
        ->leftJoin('admins as r', 'r.id', 'negative_payable_allow_shipper_zero_cod_logs.removed_by')
        ->select('u.name as shipper_name', 'negative_payable_allow_shipper_zero_cod_logs.id as id','negative_payable_allow_shipper_zero_cod_logs.created_at as removed', 'a.name as added_by', 'r.name as removed_by', 'negative_payable_allow_shipper_zero_cod_logs.added_at as created');

        $datatables = Datatables::of($zero_cod_shippers);
        return $datatables->make(true);
    }


    public function t_payment_exclude_shippers_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 847);
        $users = User::select('users.id', 'users.name')
        ->leftjoin('wallet_users as wu', 'wu.user_id', 'users.id')
        ->where('users.status',3 )
        ->where('blacklist', 0)
        ->whereNull('wu.user_id')
        ->get();

        $settings = GlobalSettings::where('type', 't_payment_exclude_shippers');
        $value = array();
        if ($settings->exists()) {
            $settings = $settings->first();
            $value = array_map('intval', explode(',', $settings->text));
        }
        return view('admin.settings.t_payment')->with(['users' => $users, 't_payment_exclude_shippers' => $value]);
    }

    public function t_payment_exclude_shippers_store(Request $request)
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 848);
        if ($request->has('users')) {

            if (count($request->users) > 0) {
                $users = implode(',', $request->users);

                $settings = GlobalSettings::where('type', 't_payment_exclude_shippers');

                if ($settings->exists()) {
                    $settings = $settings->first();
                } else {
                    $settings = new GlobalSettings();

                    $settings->type = 't_payment_exclude_shippers';
                    $settings->setting_value = 1;
                }
                $settings->text = $users;
                $settings->save();
            }
            return redirect()->back()->with('success', 'Settings Updated!');
        } else {
            return redirect()->back()->with('error', 'No shippers selected!');
        }
    }

    
}
