<?php

namespace App\Http\Controllers\Admins\Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Settings\GeneralSetting;
use Carbon\Carbon;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Admins\ActivityTrailController;
use Illuminate\Support\Facades\Auth;



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
                return redirect()->back()->with(['status'=>0, 'success'=>'Shipper Cap update successfully']);

             } else {
                 return redirect()->back()->with(['status'=>1, 'error'=>'Shipper Cap not exists']);
             }

        }
    }
    
}
