<?php

namespace App\Http\Controllers\admins\settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Settings\GeneralSetting;
use Carbon\Carbon;
use App\Http\Models\Shipper\User;

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
    
}
