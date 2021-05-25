<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\NotificationsController;
use App\http\Models\Runner;
use App\http\Models\RunnerDetail;
use App\http\Models\RunnerDetailTime;
use App\http\Models\RunnerJunction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Models\Admin\Fleet;
use App\Http\Models\Admin\MasterCargo\MasterCargo;
use App\Http\Models\Admin\RouteManagement;
use Illuminate\Support\Facades\DB;

class AdminRunnerController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),17);
        $existing_runners = RunnerDetail::where('status',0)->pluck('runner_id')->toArray();
        $runners = Runner::where('status', 1)->whereNotIn('id', $existing_runners)->get();
        return view('admin.runner.index')->with(['runners' => $runners]);
    }
    public function list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),77);
        }
        $runner_details = RunnerDetail::join('runners as r', 'r.id', '=', 'runner_details.runner_id')
            ->join('admins as a', 'a.id', '=', 'runner_details.created_by')
            ->select('runner_details.id', 'r.name as runner', 'runner_details.driver_name', 'runner_details.vehicle_no', 'runner_details.contact_no', 'runner_details.created_at', 'a.name as created_by','runner_details.status as status')
         /*   ->where('runner_details.status', 0)*/;
        $datatable = Datatables::of($runner_details)
            ->editColumn('status',function($runner_details){
                if($runner_details->status == 0){
                    return 'Update';
                }
                else{
                    return 'Complete';
                }
            })
            ->addColumn('action', function ($runner_details){
                $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';
                if($runner_details->status == 0){
                    $dropdown .= '<button type="button" class="dropdown-item update" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Update</div></button>';
                }
                $dropdown .= '<button type="button" class="dropdown-item details" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-clipboard"></i></div><div class="col-9 offset-1">View Details</div></button>';

                return $dropdown;
            });
        return $datatable->make(true);
    }
    static public function runner_report($runner_detail_id, $text){
        $runner_detail = RunnerDetail::find($runner_detail_id);
        $runner_detail_time = RunnerDetailTime::where('runner_detail_id', $runner_detail->id)->get();
        $current_day = Carbon::now()->dayOfWeek;
        $week_days = [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday'
        ];
        $today = $week_days[$current_day];
        $runner_detail_array = array();
        $runner_detail_array[] = ['list' => 'Driver Name:', 'data' => $runner_detail->driver_name, 'info' => Carbon::now()->format('d F Y')];
        $runner_detail_array[] = ['list' => 'Vehicle:', 'data' => $runner_detail->vehicle_no, 'info' => ''];
        $runner_detail_array[] = ['list' => 'Contact:', 'data' => $runner_detail->contact_no, 'info' => $runner_detail->runner->name];
        $runner_detail_array[] = ['list' => 'Day:', 'data' => $today, 'info' => $text];
        $runner_detail_array[] = ['list' => '', 'data' => '', 'info' => ''];
        $runner_detail_array[] = ['list' => '', 'data' => '', 'info' => ''];
        $runner_detail_array['header'] = ['Origin', 'TO', 'Destination', 'Departure Date Time', 'Arrival Date Time', 'Duration', 'Stay Time','Comment'];
        $runner_detail_array[] = ['Origin' => '', 'TO' => '', 'Destination' => '', 'Departure Date Time' => '', 'Arrival Date Time' => '', 'Duration' => '', 'Stay Time' => '','Comment' => ''];
        $hours = 0;
        $minutes = 0;
        $seconds = 0;
        $total_dur_hours = 0;
        $total_minutes = 0;
        $total_second = 0;
        $count = 10;
        foreach ($runner_detail_time as $detail_time){
            $finish_date = $detail_time->arrival_date . ' ' . $detail_time->arrival_time;
            $start_date = $detail_time->departure_date . ' ' . $detail_time->departure_time;
            $finish_date = Carbon::parse($finish_date);
            $start_date = Carbon::parse($start_date);
            $difference = $finish_date->diffInSeconds($start_date);
            $dur_hours = floor($difference / 3600);
            $dur_minutes = floor($difference / 60 % 60);
            $dur_sec = floor($difference % 60);
            if($dur_hours == 0){
                $dur_hours = '00';
            }
            elseif ($dur_hours < 10 && $dur_hours > 0){
                $dur_hours = '0' . $dur_hours;
            }
            if($dur_minutes == 0){
                $dur_minutes = '00';
            }
            elseif ($dur_minutes < 10 && $dur_minutes > 0){
                $dur_minutes = '0' . $dur_minutes;
            }
            if($dur_sec == 0){
                $dur_sec = '00';
            }
            elseif ($dur_sec < 10 && $dur_sec > 0){
                $dur_sec = '0' . $dur_sec;
            }
            $duration_time = $dur_hours . ':' . $dur_minutes . ':' . $dur_sec;
            if($detail_time->stay_time != null){
                $time = explode(':', $detail_time->stay_time);
                $hours = $hours + (int)$time[0];
                $minutes = $minutes + (int)$time[1];
                $seconds = $seconds + (int)$time[2];
            }
            $total_dur_hours = $total_dur_hours + (int)$dur_hours;
            $total_minutes = $total_minutes + (int)$dur_minutes;
            $total_second = $total_second + (int)$dur_sec;
            $comment = $detail_time->comment;

            $runner_detail_array[] = ['Origin' => $detail_time->origin_hub->name, 'TO' => '-> -> ->', 'Destination' => $detail_time->destination_hub->name, 'Departure Date Time' => $detail_time->departure_date . ' ' .  $detail_time->departure_time, 'Arrival Date Time' => $detail_time->arrival_date . ' ' .  $detail_time->arrival_time, 'Duration' => $duration_time, 'Stay Time' => $detail_time->stay_time,'Comment' => $comment];
            $count++;
        }
        $stay_second = ($seconds % 60);
        $stay_minutes = ($minutes + ($seconds / 60));
        $stay_hour = ($hours + ($stay_minutes / 60));
        $stay_minutes = ($stay_minutes % 60);
        $stay_hour = floor($stay_hour);
        $total_seconds = $stay_hour . ' Hrs ' . $stay_minutes . ' Min ' . $stay_second . ' Sec';
        $dur_second = ($total_second % 60);
        $dur_minutes = ($total_minutes + ($total_second / 60));
        $dur_hour = ($total_dur_hours + ($dur_minutes / 60));
        $dur_minutes = ($dur_minutes % 60);
        $dur_hour = floor($dur_hour);
        $total_duration = $dur_hour . ' Hrs ' . $dur_minutes . ' Min ' . $dur_second . ' Sec';
        $total_run_hours = $stay_hour + $dur_hour;
        $total_run_minutes = $dur_minutes + $stay_minutes;
        $total_run_seconds = $dur_second + $stay_second;
        $tot_second = ($total_run_seconds % 60);
        $tot_minutes = ($total_run_minutes + ($total_run_seconds / 60));
        $tot_hour = ($total_run_hours + ($tot_minutes / 60));
        $tot_minutes = ($tot_minutes % 60);
        $tot_hour = floor($tot_hour);
        $total_run = $tot_hour . ' Hrs ' . $tot_minutes . ' Min ' . $tot_second . ' Sec';


        $runner_detail_array[] = ['Origin' => '', 'TO' => '', 'Destination' => '', 'Departure Time' => '', 'Arrival Time' => '', 'Duration' => $total_duration, 'Stay Time' => $total_seconds];
        $runner_detail_array[] = ['Origin' => '', 'TO' => '', 'Destination' => '', 'Departure Time' => '', 'Arrival Time' => '', 'Duration' => '', 'Stay Time' => ''];
        $runner_detail_array[] = ['Origin' => '', 'TO' => '', 'Destination' => '', 'Departure Time' => '', 'Arrival Time' => '', 'Duration' => 'Total Run:', 'Stay Time' => $total_run];
        $cell_s = [
            'font' => ['bold' => true],
            'alignment' =>['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => array(
                'outline' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                    'color' => array('argb' => '000000'),
                ),
            )
        ];

        $cell_st = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['bottom' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getDefaultColumnDimension()->setWidth(20);
        $sheet->fromArray($runner_detail_array, NULL, 'A2', true);
        $sheet->getStyle("A2:H5")->applyFromArray($cell_s);
        $sheet->getStyle("A2:H5")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D3D3D3');
        $sheet->mergeCells('C2:H2');
        $sheet->mergeCells('C3:H3');
        $sheet->mergeCells('C4:H4');
        $sheet->mergeCells('C5:H5');
        $new_count = $count + 2;
        $sheet->getStyle("F".$count.":G".$count)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D3D3D3');
        $sheet->getStyle("F".$new_count.":G".$new_count)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D3D3D3');
        $sheet->getStyle("A8:H8")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D3D3D3');
        $sheet->getStyle("A8:H8")->applyFromArray($cell_st);
        $date_file_name = Carbon::today()->format('Y_m_d');
        $sheet->setTitle('Runner Report ' . $date_file_name);
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="runner_report.xlsx"');
        header('Cache-Control: max-age=0');
        $file_name_without_path = "reports/runner_detail_report_". $runner_detail->id . '_' . $date_file_name . ".xlsx";
        $file_name = public_path() . "/reports/runner_detail_report_". $runner_detail->id . '_' . $date_file_name . ".xlsx";
        $writer->save($file_name);
        return $file_name_without_path;
    }
    public function runner_details_add_index(Request $request){
        $runner = Runner::find($request->runner);
        $junctions = RunnerJunction::join('cities as c', 'c.id', '=', 'runner_junctions.junction_id')
            ->select('c.id as city_id', 'c.name as city_name', 'runner_junctions.order as order')
            ->where('runner_junctions.runner_id', $runner->id)
            ->orderBy('runner_junctions.order', 'asc')
            ->get();
        $last_junction = RunnerJunction::select('runner_junctions.junction_id')->where('runner_junctions.runner_id', $runner->id)
            ->orderBy('runner_junctions.order', 'desc')->first();
        $last_junction = $last_junction->junction_id;
        return view('admin.runner.add')->with(['runner' => $runner, 'junctions' => $junctions, 'last_junction' => $last_junction]);
    }
    public function runner_details_add_submit(Request $request){
        $runner_detail = new RunnerDetail();
        $runner_detail->runner_id = $request->id;
        $runner_detail->driver_name = $request->driver_name;
        $runner_detail->vehicle_no = $request->vehicle_number;
        $runner_detail->contact_no = $request->contact_number;
        $runner_detail->status = 0;
        $runner_detail->created_by = Auth::id();
        $runner_detail->save();
        $text = '';
        $seconds = 0;
        foreach ($request->origin as $index => $origin) {
            $stay_time = null;
            $forward_index = $index + 1;
            if(array_key_exists($forward_index, $request->origin)){
                $start_date = str_replace("00:00:00", $request->arrival_time[$index], $request->arrival_date[$index]);
                $finish_date = str_replace("00:00:00", $request->departure_time[$forward_index], $request->departure_date[$forward_index]);
                $finish_date = Carbon::parse($finish_date);
                $start_date = Carbon::parse($start_date);
                $difference = $finish_date->diffInSeconds($start_date);
                $seconds = $seconds + $difference;

                $sec_hours = floor($difference / 3600);
                $sec_minutes = floor($difference / 60 % 60);
                $sec_sec = floor($difference % 60);
                $stay_time = $sec_hours . ':' . $sec_minutes . ':' . $sec_sec;
            }
            else{
                $seconds = 0;
            }
            $runner_detail_time = new RunnerDetailTime();
            $runner_detail_time->runner_detail_id = $runner_detail->id;
            $runner_detail_time->origin = $origin;
            $runner_detail_time->destination = $request->destination[$index];
            $runner_detail_time->departure_time = $request->departure_time[$index];
            $runner_detail_time->departure_date = $request->departure_date[$index];
            $runner_detail_time->arrival_date = $request->arrival_date[$index];
            $runner_detail_time->arrival_time = $request->arrival_time[$index];
            $runner_detail_time->comment = $request->comment[$index];
            $runner_detail_time->stay_time = $stay_time;
            $runner_detail_time->save();


            if($index == 1){
                $text = $runner_detail_time->origin_hub->name . ' TO ';
            }
            if(array_key_exists($forward_index, $request->origin)){
                $text .= $runner_detail_time->destination_hub->name . ' & ';
            }
            else{
                $text .= $runner_detail_time->destination_hub->name;
            }
        }
        if($request->status == 0){
            return redirect()->route('admin.runner.index')->with('success', 'Runner On Route updated successfully!');
        }
        else{
            $runner_detail->status = 1;
            $runner_detail->completed_by = Auth::id();
            $runner_detail->save();
            $path = $this->runner_report($runner_detail->id, $text);
            NotificationsController::send(88, $runner_detail->id, url('/') . '/' . $path);
            return redirect()->route('admin.runner.index')->with('success', 'Runner On Route completed successfully!');
        }
    }
    public function runner_details_edit_index(Request $request){
        $runner_detail = RunnerDetail::find($request->id);
        $runner_detail_time = RunnerDetailTime::join('cities as oc', 'oc.id', '=', 'runner_detail_times.origin')
        ->join('cities as dc', 'dc.id', '=', 'runner_detail_times.destination')
        ->select('runner_detail_times.id', 'runner_detail_times.origin', 'runner_detail_times.destination', 'runner_detail_times.departure_date', 'runner_detail_times.arrival_date', 'runner_detail_times.departure_time', 'runner_detail_times.arrival_time', 'oc.name as origin_name', 'dc.name as destination_name','runner_detail_times.comment as comment')->where('runner_detail_id', $request->id)->get();
        $runner = Runner::find($runner_detail->runner_id);
        $junctions = RunnerJunction::join('cities as c', 'c.id', '=', 'runner_junctions.junction_id')
            ->select('c.id as city_id', 'c.name as city_name', 'runner_junctions.order as order')
            ->where('runner_junctions.runner_id', $runner->id)
            ->orderBy('runner_junctions.order', 'asc')
            ->get();
        $last_junction = RunnerJunction::select('runner_junctions.junction_id')->where('runner_junctions.runner_id', $runner->id)
            ->orderBy('runner_junctions.order', 'desc')->first();
        $last_junction = $last_junction->junction_id;
        return view('admin.runner.edit')->with(['runner' => $runner, 'junctions' => $junctions, 'last_junction' => $last_junction, 'runner_detail' => $runner_detail, 'runner_detail_time' => $runner_detail_time]);
    }
    public function runner_details_edit_submit(Request $request){
        $runner_detail = RunnerDetail::find($request->id);
        $text = '';
        $seconds = 0;
        RunnerDetailTime::where('runner_detail_id', $runner_detail->id)->delete();
        foreach ($request->origin as $index => $origin) {
            $stay_time = null;
            $forward_index = $index + 1;
            if(array_key_exists($forward_index, $request->origin)){
                $start_date = str_replace("00:00:00", $request->arrival_time[$index], $request->arrival_date[$index]);
                $finish_date = str_replace("00:00:00", $request->departure_time[$forward_index], $request->departure_date[$forward_index]);
                $finish_date = Carbon::parse($finish_date);
                $start_date = Carbon::parse($start_date);
                $difference = $finish_date->diffInSeconds($start_date);
                $seconds = $seconds + $difference;

                $sec_hours = floor($difference / 3600);
                $sec_minutes = floor($difference / 60 % 60);
                $sec_sec = floor($difference % 60);
                $stay_time = $sec_hours . ':' . $sec_minutes . ':' . $sec_sec;
            }
            else{
                $seconds = 0;
            }
            $runner_detail_time = new RunnerDetailTime();
            $runner_detail_time->runner_detail_id = $runner_detail->id;
            $runner_detail_time->origin = $origin;
            $runner_detail_time->destination = $request->destination[$index];
            $runner_detail_time->departure_time = $request->departure_time[$index];
            $runner_detail_time->departure_date = $request->departure_date[$index];
            $runner_detail_time->arrival_date = $request->arrival_date[$index];
            $runner_detail_time->arrival_time = $request->arrival_time[$index];
            $runner_detail_time->comment = $request->comment[$index];
            $runner_detail_time->stay_time = $stay_time;
            $runner_detail_time->save();


            if($index == 1){
                $text = $runner_detail_time->origin_hub->name . ' TO ';
            }
            if(array_key_exists($forward_index, $request->origin)){
                $text .= $runner_detail_time->destination_hub->name . ' & ';
            }
            else{
                $text .= $runner_detail_time->destination_hub->name;
            }
        }
        if($request->status == 0){
            return redirect()->route('admin.runner.index')->with('success', 'Runner On Route updated successfully!');
        }
        else{
            $runner_detail->status = 1;
            $runner_detail->completed_by = Auth::id();
            $runner_detail->save();
            $path = $this->runner_report($runner_detail->id, $text);
            NotificationsController::send(88, $runner_detail->id, url('/') . '/' . $path);
            return redirect()->route('admin.runner.index')->with('success', 'Runner On Route completed successfully!');
        }
    }
    public function runner_details_view(Request $request){
        $runner_detail_id = $request->id;
        $runner_detail_time = RunnerDetailTime::join('cities as c','c.id','=','runner_detail_times.origin')->join('cities as ci','ci.id','=','runner_detail_times.destination')->where('runner_detail_times.runner_detail_id',$runner_detail_id)->select('c.name as origin','ci.name as destination','runner_detail_times.departure_date as departure_date','runner_detail_times.departure_time as departure_time','runner_detail_times.arrival_date as arrival_date','runner_detail_times.arrival_time as arrival_time','runner_detail_times.stay_time as stay_time','runner_detail_times.comment as comment')->get();
        $details = array();
        if($runner_detail_time){
            foreach($runner_detail_time as $runner_details){
                $details[] = $runner_details;
            }
            return response()->json(['runner_detail_times' => $details]);
        }
    }

    public function vehicle_in_transit(){

        $route_managements = RouteManagement::where('status',1)->get();
        // $route_managements = DB::table('route_managements')->where('status',1)->get();
        $fleets = Fleet::where('status',1)->get();
        // $fleets = DB::table('fleets')->where('status',1)->get();
        return view('admin.runner.in_transit')->with(['route_managements'=>$route_managements,'fleets'=>$fleets]);
    }

    public function vehicle_in_transit_list(Request $request){
        $master_cargo = MasterCargo::leftjoin('cities as or', 'master_cargoes.origin_hub_id', '=', 'or.id')
        ->leftjoin('cities as des','master_cargoes.destination_hub_id','=','des.id')
        ->leftjoin('route_managements as rm','master_cargoes.route_management_id','=','rm.id')
        ->leftjoin('fleets as f', 'master_cargoes.fleet_id','=','f.id')
        ->leftjoin('transport_mode_vendors as tmv', 'master_cargoes.transport_mode_vendor_id', '=', 'tmv.id')
        ->select('or.name as origin','des.name as destination','master_cargoes.id','master_cargoes.driver_name', 'master_cargoes.bags','tmv.name as vendor','rm.route_title as route_title','f.reg_number as vehicle');
        
         $datatables = Datatables::of($master_cargo)
            ->addColumn('bags',function ($master_cargo){
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $master_cargo->bags . '</button>';
          

            });

            if($route_managemnt = $request->get('search_route_managemnt')){
                $datatables->where('rm.id', '=', $route_managemnt);
            }
    
            if($fleet = $request->get('search_fleet')){
                $datatables->where('f.id', '=', $fleet);
            }

            $datatables->where('f.id', '<>', 0)->where('rm.id','<>',0);

        return $datatables->make(true);
            
    }
}
