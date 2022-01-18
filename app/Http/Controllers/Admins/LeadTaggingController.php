<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Lead\Lead;
use App\Http\Models\Admin\Lead\LeadTagging;
use App\Http\Models\Admin\Lead\LeadTaggingHistory;
use Carbon\Carbon;

class LeadTaggingController extends Controller
{
    

    static public function auto_tagging($lead_id,$admin_id){
        $lead = Lead::find($lead_id);
        $sales_person = LeadTagging::where('city_id',$lead->city_id)->where('service_id',$lead->service_id)->where('status',1)->orderBy('count','desc')->get()->first();
        $lead->sale_person_id = $sales_person->sale_person_id;
        $lead->updated_by = $admin_id;
        $lead->sale_person_updated_at = Carbon::now();
        $lead->save();
        $sales_person->count += 1;
        $sales_person->save();

        LeadTaggingHistory::create([
            'sale_person_id' => $sales_person->id,
            'lead_id' => $lead->id,
            'status' => 1,
        ]);
        
    }
}
