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
    }
    public function pickup_index(){
        $settings = GlobalSettings::all();
        return view('admin.settings.pickup')->with('settings',$settings);
    }
    public function add_pickup_weight(Request $request){

//        $result = GlobalSettings::updateOrCreate(['pickup_weight_threshold'=>$request->pickup_weight,'type'=>'pickup_weight']);
//        if($result){
//            return redirect()->back()->with('success','Pickup request weight added/updated');
//        }
        if($request->isMethod('post')){
            $result = GlobalSettings::create([
                'pickup_weight_threshold'=>$request->pickup_weight,
                'type'=>'pickup_weight'
            ]);
            if($result){
                return redirect()->back()->with('success','Pickup request weight updated');
            }
        }else{
           $record = GlobalSettings::where('type','pickup_weight')->get();
            $result = GlobalSettings::where('id',$record[0]->id)->update([
                'pickup_weight_threshold'=>$request->pickup_weight,
                'type'=>'pickup_weight'
            ]);
            if($result){
                return redirect()->back()->with('success','Pickup request weight updated');
            }
        }
    }
}
