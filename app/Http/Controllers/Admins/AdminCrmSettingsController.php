<?php

namespace App\Http\Controllers\Admins;

use App\http\Models\CRM\CrmSettings;
use App\http\Models\CRM\CrmTatHolidays;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;

class AdminCrmSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }
    public function crm_cut_off_time_and_holidays_index(){
        $from = CrmSettings::where('name','TAT Cut-Off Time From')->first();
        $to = CrmSettings::where('name','TAT Cut-Off Time To')->first();

        $cut_off_time_from = $from->setting_value;
        $cut_off_time_to = $to->setting_value;
        return view('admin.settings.crm_cut_off_time_and_holidays')->with(['cut_off_time_from' => $cut_off_time_from, 'cut_off_time_to' => $cut_off_time_to]);
    }
    public function crm_cut_off_time_and_holidays_update(Request $request){
        $from = $request->cut_off_time_from;
        $to = $request->cut_off_time_to;
        if($from == $to){
            return redirect()->back()->with('error', 'Cutt-Off time can\'t be same');
        }
        else{
            $cut_off_time_from = CrmSettings::where('name','TAT Cut-Off Time From')->first();
            $cut_off_time_from->setting_value = $from;
            $cut_off_time_from->save();

            $cut_off_time_to = CrmSettings::where('name','TAT Cut-Off Time To')->first();
            $cut_off_time_to->setting_value = $to;
            $cut_off_time_to->save();

            return redirect()->back()->with('success', 'Cut-Off time is Updated Successfully!');
        }
    }
    public function crm_cut_off_time_and_holidays_list(){
        $holidays = CrmTatHolidays::leftjoin('admins as a', 'a.id', '=', 'crm_tat_holidays.created_by')
            ->select('crm_tat_holidays.reason as reason', 'crm_tat_holidays.holiday as holiday', 'crm_tat_holidays.created_at as created_at', 'a.name as created_by');

        return Datatables::of($holidays)
            ->make(true);
    }
    public function crm_cut_off_time_and_holidays_add(Request $request){
        $holiday_date = $request->holiday_date;
        $holiday_reason = $request->holiday_reason;
        $existing_holiday = CrmTatHolidays::where('holiday', $holiday_date);
        if ($existing_holiday->exists()){
            return ['status' => 0, 'error' => 'Holiday is already marked on the selected date!'];
        }
        else{
            $new_holiday = new CrmTatHolidays();
            $new_holiday->holiday = $holiday_date;
            $new_holiday->reason = $holiday_reason;
            $new_holiday->created_by = Auth::id();
            $new_holiday->save();
            return ['status' => 1, 'success' => 'Holiday added successfully!'];
        }
    }
}
