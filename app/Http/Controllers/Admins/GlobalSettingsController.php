<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\GlobalSettings;
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
}
