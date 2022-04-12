<?php

namespace App\Http\Controllers\Admins;

use App\DailyVisit;
use App\Http\Models\Admin\Admin;
use App\Http\Models\DailyVisitLeadStatus;
use App\Http\Models\Shipper\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AdminDailyVisitController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }
    public function daily_visit_index(){
        $lead_statuses = DailyVisitLeadStatus::get(['id', 'name']);
        $shippers = User::where('status', 3)->get(['id', 'name','poc','address','email','phone']);
        return view('admin.daily_visit.index')->with(['lead_statuses' => $lead_statuses,'shippers'=>$shippers]);
    }

    public function daily_visit_store(Request $request){
        if($request->company_name != null && $request->customer_name != null && $request->customer_address != null && $request->phone_no != null && $request->email_address != null && $request->lead_status != null && $request->feedback != null && $request->latitude != null && $request->longitude != null && $request->shipper != null){

            $daily_visit = new DailyVisit();
            $daily_visit->shipper_id = $request->shipper;
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

    public static function daily_visit_sales_report($from,$to,$admin_id,$admin_name)
    {
        $visits = DB::connection('reports')->table('daily_visits')
            ->leftjoin('crm_request_ratings as crr','crr.id','daily_visits.rating_id')
            ->leftjoin('daily_visit_lead_statuses as dvls','dvls.id','daily_visits.lead_status_id')
            ->select(['daily_visits.created_at','daily_visits.company_name','daily_visits.customer_name','daily_visits.customer_address','daily_visits.phone_no','daily_visits.email','daily_visits.feedback','daily_visits.comment','crr.name as rating','dvls.name as lead_status'])
            ->where('daily_visits.admin_id',$admin_id)
            ->whereBetween('daily_visits.created_at', [$from, $to])
            ->get();

        if($visits->count() > 0) {
            $details[] = ['S. No.', 'Visit Date Time', 'Company Name', 'Customer Name', 'Customer Address', 'Phone Number', 'Email Address', 'Lead Status', 'Meeting Feedback', 'Shipper Rating', 'Shipper Feedback'];

            $serial_number = 1;
            foreach ($visits as $visit) {
                $row = array();
                $row[] = $serial_number;
                $row[] = $visit->created_at;
                $row[] = $visit->company_name;
                $row[] = $visit->customer_name;
                $row[] = $visit->customer_address;
                $row[] = $visit->phone_no;
                $row[] = $visit->email;
                $row[] = $visit->lead_status;
                $row[] = $visit->feedback;
                $row[] = $visit->rating;
                $row[] = $visit->comment;

                $details[] = $row;
                $serial_number++;
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet()->fromArray($details);

            $sheet->setTitle('Weekly Sales Visit Report');
            $writer = new Xlsx($spreadsheet);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="weekly_sales_visit_report.xlsx"');
            header('Cache-Control: max-age=0');

            $from_file_name = Carbon::parse($from)->format('Y_m_d');
            $to_file_name = Carbon::parse($to)->format('Y_m_d');
            $time_string = Carbon::now()->toTimeString();
            $time_string = Carbon::parse($time_string)->format('h_i_s');

            $file_name_without_path = "reports/weekly_sales_visit_report_" . $admin_name . '_' . $from_file_name . '_' . $to_file_name . '_' . $time_string . ".xlsx";
            $file_name = public_path() . '/' . $file_name_without_path;

            $writer->save($file_name);

            return url('/') . '/' . $file_name_without_path;
        }
        else{
            return null;
        }
    }

}
