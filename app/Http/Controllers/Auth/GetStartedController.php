<?php

namespace App\Http\Controllers\Auth;

use App\Http\Models\Admin\Lead\Lead;
use App\Http\Models\City;
use App\Http\Models\Reference;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
class GetStartedController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function index(){
        $cities = City::select('id', 'name')->where('status', 1)->where('pickup', 1)->get();
        $references = Reference::all();
        $services = DB::table('service_list')->get();
        return view('client.auth.get-started')->with(['cities' => $cities, 'references' => $references, 'services' => $services]);
    }

    public function getstarted_submit(Request $request){

        $email = $request->email;
        $phone = $request->phone;
        if(Lead::where(['phone_number' => $phone, 'email_address' => $email])->exists()){
            return redirect()->back()->with('error', 'Duplicate entry - You have already provided this Number or Email previously. Kindly fill new details. Thanks');
        }

        $max_lead_id = Lead::max('lead_id');
        $max_lead_id = $max_lead_id + 1;
        $new_lead = new Lead();
        $new_lead->lead_id = $max_lead_id;
        $new_lead->contact_person = $request->name;
        $new_lead->city_id = $request->city;
        $new_lead->territory_id = $request->territory;
        $new_lead->territory_area_id = $request->area;
        $new_lead->phone_number = $phone;
        $new_lead->email_address = $email;
        $new_lead->requested_date = Carbon::now();
        $new_lead->message = $request->message;
        $new_lead->reference_id = $request->reference;
        $new_lead->service_id = $request->service;
        $new_lead->save();
        return redirect()->route('cod.getstarted.success');
    }

    public function getstarted_success(){
        return view('client.get_started_success');
    }
}
