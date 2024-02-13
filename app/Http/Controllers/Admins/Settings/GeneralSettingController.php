<?php

namespace App\Http\Controllers\admins\settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\admins\ActivityTrailController;
use App\Http\Models\Admin\Settings\GeneralSetting;
use App\Http\Models\Shipper\User;
use Auth;
use Carbon\Carbon;

class GeneralSettingController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function ticker_index()
    {
        $admin_ticker = null;
        $shipper_ticker = null;

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
    {   if(!is_null($date))
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
        $users= User::where('status',3)->where('blacklist' ,0 )->select('id' , 'name')->get();
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


    
}
