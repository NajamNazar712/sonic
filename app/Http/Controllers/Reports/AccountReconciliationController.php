<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Models\AccountReconciliation;
use App\Http\Models\Admin\SalePersonTag;
use App\Http\Models\Shipper\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AccountReconciliationController extends Controller
{

    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    static public function reconciliation_old(){
        $months = array();
        $data = array();
        $start = new Carbon('first day of October 2018', 'Asia/Karachi');
        $end = Carbon::now()->subMonth(2)->endOfMonth();

        do
        {
            $months[$start->format('m-Y')] = $start->toDateString();
        } while ($start->addMonth() <= $end);

        $users = User::where('status', '>', 2)->get();
        foreach ($users as $user) {
            foreach ($months as $month){
                $received = 0;
                $revenue = 0;
                $date = Carbon::parse($month);
                $received_shipments = DB::connection('reports')->table('shipments')->whereExists(function ($query) use ($date) {
                    $query->from('shipments_journey')
                        ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                        ->whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->where('shipper_status_id', 2);
                })->where('shipments.user_id', $user->id)->count();
                $data[$user->id][$month]['shipments'] = $received_shipments;

                $revenue = DB::connection('reports')->table('shipments')->whereExists(function ($query) use ($date) {
                    $query->from('shipments_journey')
                        ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                        ->whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->where('shipper_status_id', 2);
                })->where('shipments.user_id', $user->id)->sum(DB::connection('reports')->raw('IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0) + IFNULL(packaging_material_charges,0) + IFNULL(intercept_charges,0) + IFNULL(nsa_osa_charges,0) + IFNULL(packaging_charges,0)'));
                $data[$user->id][$month]['revenue'] = $revenue;
            }

        }
        foreach($data as $shipper => $month_data){
            foreach ($month_data as $month => $count){
                if(!AccountReconciliation::where('user_id', $shipper)->whereDate('month', $month)->exists()) {
                    $account = new AccountReconciliation();
                    $account->user_id = $shipper;
                    $account->month = $month;
                    $account->shipments = $count['shipments'];
                    $account->revenue = $count['revenue'];
                    $account->save();
                }
            }
        }
    }

    static public function reconciliation_current(){

        $months = array();
        $data = array();
        $start = Carbon::now()->subMonth(1)->startOfMonth();
        $end = Carbon::now()->startOfMonth();
        do
        {
            $months[$start->format('m-Y')] = $start->toDateString();
        } while ($start->addMonth() <= $end);

        $users = User::where('status', '>', 2)->get();
        foreach ($users as $user) {
            foreach ($months as $month){
                $received = 0;
                $revenue = 0;
                $date = Carbon::parse($month);
                $received_shipments = DB::connection('reports')->table('shipments')->whereExists(function ($query) use ($date) {
                    $query->from('shipments_journey')
                        ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                        ->whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->where('shipper_status_id', 2);
                })->where('shipments.user_id', $user->id)->count();
                $data[$user->id][$month]['shipments'] = $received_shipments;

                $revenue = DB::connection('reports')->table('shipments')->whereExists(function ($query) use ($date) {
                    $query->from('shipments_journey')
                        ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                        ->whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->where('shipper_status_id', 2);
                })->where('shipments.user_id', $user->id)->sum(DB::connection('reports')->raw('IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0) + IFNULL(packaging_material_charges,0) + IFNULL(intercept_charges,0) + IFNULL(nsa_osa_charges,0) + IFNULL(packaging_charges,0)'));
                $data[$user->id][$month]['revenue'] = $revenue;
            }

        }
        foreach($data as $shipper => $month_data){
            foreach ($month_data as $month => $count){
                $account_recon = AccountReconciliation::where('user_id', $shipper)->whereDate('month', $month);
                if($account_recon->exists()) {
                    $account = $account_recon->first();
                    $account->shipments = $count['shipments'];
                    $account->revenue = $count['revenue'];
                    $account->save();
                }else{
                    $account = new AccountReconciliation();
                    $account->user_id = $shipper;
                    $account->month = $month;
                    $account->shipments = $count['shipments'];
                    $account->revenue = $count['revenue'];
                    $account->save();
                }
            }
        }
    }

    public function account_reconciliation_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),201);
        return view('admin.reports.account_reconciliation');
    }

    public function account_reconciliation_export_to_excel(Request $request){
        $date = $request->date;
        if($date != null){
            $from_date = new Carbon('first day of October 2018', 'Asia/Karachi');
            $to_date = Carbon::parse($date);
            return self::account_reconciliation_create($from_date, $to_date);
        }
    }

    static public function account_reconciliation_create($from_date, $to_date){

        $data = array();
        $shippers = array();
        $months = array();

        do
        {
            $months[] = $from_date->format('M Y');
        } while ($from_date->addMonth() <= $to_date);


        $users = User::where('status', '>', 2)->get();
        foreach ($users as $user){
            $reconciliation_data = AccountReconciliation::where('user_id', $user->id)->where('month', '<=', $to_date);
            if($reconciliation_data->exists()){
                $reconciliation_data = $reconciliation_data->get();
                if($user->status == 3){
                    $shippers[$user->id]['status'] = 'Active';
                }else if($user->status == 4){
                    if($user->blacklist == 1){
                        $shippers[$user->id]['status'] = 'Blocked';
                    }else{
                        $shippers[$user->id]['status'] = 'Disable';
                    }
                }
                $sale_person = '';
                $sale_person_name = '';
                $sale_person = SalePersonTag::where('user_id', $user->id)->where('status', 0)->first();
                if($sale_person){
                    $sale_person_name = $sale_person->sales_person->name;
                }
                $shippers[$user->id]['sale_person'] = $sale_person_name;
                $origin = $user->city->name;
                $shippers[$user->id]['origin'] = $origin;
                $shippers[$user->id]['account_no'] = $user->id;
                $shippers[$user->id]['name'] = $user->name;
                foreach ($reconciliation_data as $key => $account_data){
                    $shippers[$user->id]['month'][$account_data->month]['shipments'] = $account_data->shipments;
                    $shippers[$user->id]['month'][$account_data->month]['revenue'] = $account_data->revenue;
                }

            }
        }

        $data['header'] = ['S No.', 'Account Status', 'Sales Person', 'Origin', 'Account No.', 'Client Name'];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $style =[
            'font' =>['bold' => true],
            'alignment' =>['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => array(
                'outline' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                    'color' => array('argb' => '000000'),
                ),
            ),
        ];
        $sheet->fromArray($data['header'],NULL,'A1');
        $cellIndexcol1 = 7;
        $cellIndexcol2 = 8;
        foreach ($months as $month){
            $cellIndex1 = Coordinate::stringFromColumnIndex($cellIndexcol1);
            $cellIndex2 = Coordinate::stringFromColumnIndex($cellIndexcol2);
            $cell_sub_header_shipment = $cellIndex1.'2';
            $cell_sub_header_revenue = $cellIndex2.'2';
            $cellIndex_header1 = $cellIndex1 . '1';
            $cellIndex_header2 = $cellIndex2 . '1';
            $sheet->mergeCells("$cellIndex_header1:$cellIndex_header2");
            $sheet->setCellValue($cellIndex_header1, $month);
            $sheet->setCellValue($cell_sub_header_shipment, 'Shipments');
            $sheet->setCellValue($cell_sub_header_revenue, 'Revenue');
            $cellIndexcol1 += 2;
            $cellIndexcol2 += 2;
        }

        $serial = 1;
        $row = 3;
        foreach ($shippers as $shipper){
            $sheet->setCellValue('A'.$row, $serial);
            $sheet->setCellValue('B'.$row, $shipper['status']);
            $sheet->setCellValue('C'.$row, $shipper['sale_person']);
            $sheet->setCellValue('D'.$row, $shipper['origin']);
            $sheet->setCellValue('E'.$row, $shipper['account_no']);
            $sheet->setCellValue('F'.$row, $shipper['name']);
            $shipment_cell = 7;
            $revenue_cell = 8;
            foreach ($shipper['month'] as $month){
                $col_shipment = Coordinate::stringFromColumnIndex($shipment_cell);
                $col_revenue = Coordinate::stringFromColumnIndex($revenue_cell);

                $sheet->setCellValue($col_shipment.$row, $month['shipments']);
                $sheet->setCellValue($col_revenue.$row, $month['revenue']);
                $shipment_cell += 2;
                $revenue_cell += 2;
            }
            $serial++;
            $row++;


        }

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="account_reconciliation_report.xlsx"');
        header('Cache-Control: max-age=0');
        $file_name = "reports/account_reconciliation_report".Auth::id().".xlsx";
        $writer->save("$file_name");
        return response()->json(['success'=>1,'file'=>'account_reconciliation_report.xlsx']);

    }

    public function account_reconciliation_download(Request $request){
        ActivityTrailController::createActivityTrailLog(Auth::id(),202);
        $file_name = "/reports/account_reconciliation_report".Auth::id().".xlsx";
        $file = public_path().$file_name;
        $headers = array('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',);
        return Response::download($file, 'account_reconciliation_report.xlsx',$headers);
    }
}
