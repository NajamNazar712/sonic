<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\NonServiceArea;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GlobalSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }
    public function pickup_index(){
        $settings = GlobalSettings::where('type','=','pickup_weight')->first();
        return view('admin.settings.pickup')->with('settings',$settings);
    }
    public function add_pickup_weight(Request $request){

        if($request->isMethod('post')){
            $result = GlobalSettings::create([
                'setting_value'=>$request->pickup_weight,
                'type'=>'pickup_weight'
            ]);
            if($result){
                return redirect()->back()->with('success','Pickup request weight updated');
            }
        }else{
           $record = GlobalSettings::where('type','pickup_weight')->get();
            $result = GlobalSettings::where('id',$record[0]->id)->update([
                'setting_value'=>$request->pickup_weight,
                'type'=>'pickup_weight'
            ]);
            if($result){
                return redirect()->back()->with('success','Pickup request weight updated');
            }
        }
    }

    public function shipment_cancellation_cut_off_days_index() {
        $settings = GlobalSettings::where('type', 'shipment_cancellation_cut_off_days')->first();

        return view('admin.settings.shipment_cancellation_cut_off_days')->with('settings', $settings);
    }


    public function shipment_cancellation_cut_off_days_store(Request $request) {
        $settings = GlobalSettings::where('type', 'shipment_cancellation_cut_off_days')->first();

        $settings->setting_value = $request->shipment_cancellation_cut_off_days;

        $settings->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }
    public function auto_account_disabled_days_index() {
        $settings = GlobalSettings::where('type', 'auto_account_disabled_days')->first();

        return view('admin.settings.auto_account_disabled_days')->with('settings', $settings);
    }
    public function auto_account_disabled_days_store(Request $request) {
        $settings = GlobalSettings::where('type', 'auto_account_disabled_days')->first();

        $settings->setting_value = $request->auto_account_disabled_days;

        $settings->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }
    public function non_service_area_index() {
    return view('admin.settings.non_service_area');
    }

    public function non_service_area_store(Request $request) {
        $nsa = NonServiceArea::where('name',$request->non_service_area)->first();
        if($nsa['name'] == $request->non_service_area)
        {
            return redirect()->back()->with('error', 'Non Service Area Is Already Updated!');
        }
        else
        {
            $create=NonServiceArea::create(['name' => $request->non_service_area]);
            return redirect()->back()->with('success', 'Non Service Area Updated!');
        }
    }

    public function daily_pickup_sales_cron_index(){
        $settings = GlobalSettings::where('type', 'daily_pickup_sales_cron_time')->first();

        return view('admin.settings.daily_pickup_sales_cron_time')->with('settings', $settings);
    }
    public function daily_pickup_sales_cron_store(Request $request) {
        $settings = GlobalSettings::where('type', 'daily_pickup_sales_cron_time')->first();

        $settings->setting_value = $request->daily_pickup_sales_cron_time;

        $settings->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }


    public function ticker_index() {
        $admin_ticker = NULL;
        $shipper_ticker = NULL;

        $settings = GlobalSettings::where('type', 'admin_ticker');

        if ($settings->exists()) {
            $settings = $settings->first();

            $ticker = $settings->text;

            if (!empty($ticker)) {
                $admin_ticker = $ticker;
            }
        }

        $settings = GlobalSettings::where('type', 'shipper_ticker');

        if ($settings->exists()) {
            $settings = $settings->first();

            $ticker = $settings->text;

            if (!empty($ticker)) {
                $shipper_ticker = $ticker;
            }
        }

        return view('admin.settings.ticker')->with(['admin_ticker' => $admin_ticker, 'shipper_ticker' => $shipper_ticker]);
    }

    public function ticker_store(Request $request) {
        $settings = GlobalSettings::where('type', 'admin_ticker');

        if ($settings->exists()) {
            $settings = $settings->first();
        }
        else {
            $settings = new GlobalSettings();

            $settings->type = 'admin_ticker';
        }

        $settings->text = $request->admin_ticker;

        $settings->save();

        $settings = GlobalSettings::where('type', 'shipper_ticker');

        if ($settings->exists()) {
            $settings = $settings->first();
        }
        else {
            $settings = new GlobalSettings();

            $settings->type = 'shipper_ticker';
        }

        $settings->text = $request->shipper_ticker;

        $settings->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }
}
