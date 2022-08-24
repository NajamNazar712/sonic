<?php

namespace App\Http\Controllers\Admins;

use App\DailyVisit;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Admin;
use App\Http\Models\CRM\CrmRequestRating;
use App\Http\Models\DailyVisitLeadStatus;
use App\Http\Models\Shipper\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yajra\Datatables\Datatables;

class AdminDailyVisitController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function daily_visit_index()
    {
        $daily_visit = null;
        $lead_statuses = DailyVisitLeadStatus::get(['id', 'name']);
        $shippers = User::where('status', 3)->get(['id', 'name', 'poc', 'address', 'email', 'phone']);
        return view('admin.daily_visit.index')->with(['lead_statuses' => $lead_statuses, 'shippers' => $shippers, 'daily_visit' => $daily_visit]);
    }

    public function daily_visit_store(Request $request)
    {
        $request->validate([
            'upload_bc_image' => ['mimes:png,jpeg,jpg', 'max:2048'],
            'upload_l_image' => ['mimes:png,jpeg,jpg', 'max:2048'],
        ],
        [
            'upload_bc_image.mimes' => 'Business Card Image must be a file of type: png,jpeg,jpg',
            'upload_l_image.mimes' => 'Location Image must be a file of type: png,jpeg,jpg',
            'upload_bc_image.max' => 'Business Card Image Size must not exceed 2 MB (2048 KB)',
            'upload_l_image.max' => 'Location Image Size must not exceed 2 MB (2048 KB)',
        ]);
        if ($request->company_name != null && $request->customer_name != null && $request->customer_address != null && $request->phone_no != null && $request->email_address != null && $request->lead_status != null && $request->feedback != null && $request->latitude != null && $request->longitude != null && $request->shipper != null) {

            if($request->daily_visit_id != null){
                $daily_visit = DailyVisit::where('id', $request->daily_visit_id);
                if($daily_visit->exists()){
                    $daily_visit = $daily_visit->first();
                    $daily_visit->updated_by = Auth::id();
                }else{
                    return redirect()->back()->with('error', 'Invalid Daily Visit ID');
                }
            } else{
                $daily_visit = new DailyVisit();
                $daily_visit->admin_id = Auth::id();
            }
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
            $daily_visit->save();

            if ($request->hasFile('upload_bc_image')) {
                if($daily_visit->business_card_image != null){
                    Storage::disk('public')->delete($daily_visit->business_card_image);
                }
                $filename = 'daily_visit_bc_' . $daily_visit->id . '.png';

                $file = $request->file('upload_bc_image');

                Storage::disk('public')->putFileAs('daily_visit\business_card', $file, $filename);

                $daily_visit->business_card_image = $filename;
                $daily_visit->save();
            }

            if ($request->hasFile('upload_l_image')) {
                if($daily_visit->location_image != null){
                    Storage::disk('public')->delete($daily_visit->location_image);
                }
                $filename = 'daily_visit_l_' . $daily_visit->id . '.png';

                $file = $request->file('upload_l_image');

                Storage::disk('public')->putFileAs('daily_visit\location', $file, $filename);

                $daily_visit->location_image = $filename;
                $daily_visit->save();
            }

            return redirect()->back()->with('success', 'Form submitted successfully');
        } else {
            return redirect()->back()->with('error', 'Incomplete Information!');
        }
    }

    public function business_card($business_card)
    {
        $url = Storage::url('daily_visit/business_card/' . $business_card);

        return view('admin.daily_visit.view_daily_visit_photo')->with(['url' => $url]);
    }

    public function location_photo($location_photo)
    {
        $url = Storage::url('daily_visit/location/' . $location_photo);

        return view('admin.daily_visit.view_daily_visit_photo')->with(['url' => $url]);
    }

    public static function daily_visit_sales_report($from, $to, $admin_id, $admin_name)
    {
        $visits = DB::connection('reports')->table('daily_visits')
            ->leftjoin('crm_request_ratings as crr', 'crr.id', 'daily_visits.rating_id')
            ->leftjoin('daily_visit_lead_statuses as dvls', 'dvls.id', 'daily_visits.lead_status_id')
            ->select(['daily_visits.created_at', 'daily_visits.company_name', 'daily_visits.customer_name', 'daily_visits.customer_address', 'daily_visits.phone_no', 'daily_visits.email', 'daily_visits.feedback', 'daily_visits.comment', 'crr.name as rating', 'dvls.name as lead_status'])
            ->where('daily_visits.admin_id', $admin_id)
            ->whereBetween('daily_visits.created_at', [$from, $to])
            ->get();

        if ($visits->count() > 0) {
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
        } else {
            return null;
        }
    }

    public function daily_visit_edit($id)
    {
        $daily_visit = DailyVisit::find($id);
        if ($daily_visit) {
            $lead_statuses = DailyVisitLeadStatus::get(['id', 'name']);
            $shippers = User::where('status', 3)->get(['id', 'name', 'poc', 'address', 'email', 'phone']);
            return view('admin.daily_visit.index')->with(['lead_statuses' => $lead_statuses, 'shippers' => $shippers, 'daily_visit' => $daily_visit]);
        } else {
            return redirect()->back()->with('error', 'Invalid Daily Visit ID');
        }
    }

    public function daily_visit_list_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 579);
        $admins = Admin::where('admins.status', 1)
            ->leftjoin('employee_designations as ed', 'admins.designation_id', 'ed.id')
            ->where('ed.department_id', 7);
        if (session('role_id') != 1 && (!in_array(session('id'), session('sale_users_bypass')))) {
            $admins = $admins->where('admins.id', Auth::id());
        }
        $admins = $admins->get(['admins.id', 'admins.name']);
        $ratings = CrmRequestRating::all();
        return view('admin.daily_visit.daily_visit_index')->with(['admins' => $admins, 'ratings' => $ratings]);
    }

    public function daily_visit_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 580);
        }
        $daily_visit = DailyVisit::join('daily_visit_lead_statuses as dvls', 'dvls.id', '=', 'daily_visits.lead_status_id')
            ->leftjoin('admins as a', 'a.id', '=', 'daily_visits.admin_id')
            ->leftjoin('cities as c', 'c.id', '=', 'a.default_hub_id')
            ->leftjoin('zones as z', 'z.id', '=', 'c.zone_id')
            ->leftjoin('crm_request_ratings as rate', 'rate.id', 'daily_visits.rating_id')
            ->leftjoin('admins as ua', 'ua.id', 'daily_visits.updated_by')
            ->select('daily_visits.id as daily_visit_id', 'a.name as admin', 'daily_visits.company_name as company_name', 'daily_visits.customer_name as customer_name', 'daily_visits.customer_address as customer_address', 'daily_visits.phone_no as phone_no', 'daily_visits.email as email', 'dvls.name as lead_status', 'daily_visits.feedback as feedback', 'daily_visits.latitude as latitude', 'daily_visits.longitude as longitude', 'daily_visits.created_at as created_at', 'daily_visits.business_card_image as business_card_image', 'daily_visits.location_image as location_image', 'c.name as city', 'z.name as zone', 'rate.name as rating_text', 'daily_visits.comment as rating_comment', 'rate.code as rating', 'ua.name as updated_by');

        if (session('role_id') != 1 && (!in_array(session('id'), session('sale_users_bypass')))) {
            $daily_visit = $daily_visit->where('daily_visits.admin_id', Auth::id());
        }

        $datatables = Datatables::of($daily_visit)
            ->editColumn('b_c_photo', function ($dvr) {
                $image = '';
                if ($dvr->business_card_image != null) {
                    $image .= '<div class="text-center"><button type="button" class="btn btn-primary btn-sm"><a class="white" href=' . route('admin.daily_visit.business_card', [$dvr->business_card_image]) . ' target="_blank">View</a></button></div>';
                    return $image;
                } else {
                    return '-';
                }
            })
            ->editColumn('l_photo', function ($dvr) {
                $image = '';
                if ($dvr->location_image != null) {
                    $image .= '<div class="text-center"><button type="button" class="btn btn-primary btn-sm"><a class="white" href=' . route('admin.daily_visit.location_photo', [$dvr->location_image]) . ' target="_blank">View</a></button></div>';
                    return $image;
                } else {
                    return '-';
                }
            })
            ->editColumn('location', function ($dvr) {
                $location = '<div class="text-center">';
                if ($dvr->latitude != null && $dvr->longitude != null) {
                    $location .= '<button type="button" class="btn btn-primary btn-sm"><a class="white" href="http://www.google.com/maps/place/' . $dvr->latitude . ',' . $dvr->longitude . '" target="_blank"><i class="la la-map-marker align-middle"></i></a></button>';
                    return $location;
                } else {
                    return '-';
                }
            })
            ->addColumn("action", function ($result) {
                if (session('role_id') == 1 || in_array(792, session('permissions'))) {
                    $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';
                    if (session('role_id') == 1 || in_array(792, session('permissions'))) {
                        $route = route("admin.daily_visit.screen.edit", $result->daily_visit_id);
                        $dropdown .= '<a href="' . $route . '"><button class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1"> Edit</div></div></button></a>';
                    }
                    $dropdown .= '
                </div>
              </div>
            ';
                    return $dropdown;
                } else {
                    return '';
                }
            });


        //AdminUser Filter
        if ($team_member = $request->get('team_member')) {
            $datatables->where('a.id', $team_member);
        }

        if ($rating = $request->get('rating')) {
            $datatables->where('daily_visits.rating_id', $rating);
        }
        //VisitDate filter
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatables->whereBetween('daily_visits.created_at', [$from, $to]);
        }

        return $datatables->make(true);
    }


}
