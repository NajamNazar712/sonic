<?php

namespace App\Http\Controllers\Admins;

use App\DailyVisit;
use App\Http\Models\DailyVisitLeadStatus;
use App\Http\Models\Shipper\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminDailyVisitController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }
    public function daily_visit_index(){
        $lead_statuses = DailyVisitLeadStatus::get(['id', 'name']);
        $users = User::where('status', 3)->get(['id', 'name']);
        return view('admin.daily_visit.index')->with(['lead_statuses' => $lead_statuses, 'users' => $users]);
    }

    public function daily_visit_store(Request $request){
        if($request->company_name != null && $request->customer_name != null && $request->customer_address != null && $request->phone_no != null && $request->email_address != null && $request->lead_status != null && $request->feedback != null && $request->latitude != null && $request->longitude != null){

            $daily_visit = new DailyVisit();
            $daily_visit->company_name = $request->company_name;
            $daily_visit->customer_name = $request->customer_name;
            $daily_visit->customer_address = $request->customer_address;
            $daily_visit->phone_no = $request->phone_no;
            $daily_visit->email = $request->email_address;
            $daily_visit->lead_status_id = $request->lead_status;
            $daily_visit->feedback = $request->feedback;
            $daily_visit->latitude = $request->latitude;
            $daily_visit->longitude = $request->longitude;
            $daily_visit->admin_id = Auth::id();
            $daily_visit->save();

            if ($request->hasFile('upload_bc_image')) {
                $filename = 'daily_visit_bc_' . $daily_visit->id . '.png';

                $file = $request->file('upload_bc_image');

                Storage::disk('public')->putFileAs('daily_visit\business_card', $file, $filename);

                $daily_visit->business_card_image = $filename;
                $daily_visit->save();
            }

            if ($request->hasFile('upload_l_image')) {
                $filename = 'daily_visit_l_' . $daily_visit->id . '.png';

                $file = $request->file('upload_l_image');

                Storage::disk('public')->putFileAs('daily_visit\location', $file, $filename);

                $daily_visit->location_image = $filename;
                $daily_visit->save();
            }

            return redirect()->back()->with('success', 'Form submitted successfully');
        }
        else{
            return redirect()->back()->with('error', 'Incomplete Information!');
        }
    }

    public function business_card($business_card){
        $url = Storage::url('daily_visit/business_card/' . $business_card);

        return view('admin.daily_visit.view_daily_visit_photo')->with(['url' => $url]);
    }
    public function location_photo($location_photo){
        $url = Storage::url('daily_visit/location/' . $location_photo);

        return view('admin.daily_visit.view_daily_visit_photo')->with(['url' => $url]);
    }
}
