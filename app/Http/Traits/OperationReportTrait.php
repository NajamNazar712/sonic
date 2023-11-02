<?php

namespace App\Http\Traits;

use Carbon\Carbon;
use PHPExcel_Style_Fill;
use App\Http\Models\Rider;
use App\Http\Models\Admin\Admin;
use Illuminate\Support\Facades\DB;
use App\Http\Models\ShipmentStatus;
use Illuminate\Support\Facades\Storage;
use App\Http\Models\ShipmentStatusReason;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Http\Controllers\NotificationsController;

trait OperationReportTrait{
    public function operations_performance_export_to_excel_automated($from, $to, $id, $mode)
    {
        $connection = 'reports';
        $to = Carbon::parse($to)->addDay()->toDateString();
        $shipments =  DB::connection($connection)->table('shipments')->join('users as u', 'u.id', 'shipments.user_id')
            ->join('sub_category_segments as segments', 'segments.id', 'u.sub_segment_id')
            ->leftJoin('shipping_modes as sm', 'sm.id', 'shipments.shipping_mode_id')
            ->leftJoin('booking_types as bt', 'bt.id', 'shipments.booking_type_id')
            ->leftJoin('business_categories as bc', 'bc.id', 'shipments.business_category_id')
            ->leftJoin('shipment_items as si', 'si.shipment_id', 'shipments.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', 'usi.id') // usi for user shipping infos
            ->join('cities AS oc', 'usi.city_id', 'oc.id') // oc for origin city
            ->join('cities as och', 'oc.hub_id', 'och.id') // och for origin city hub
            ->leftjoin('zones as ocz', 'ocz.id', 'oc.zone_id') // ocz for origin city zone
            ->join('cities AS dc', 'shipments.consignee_city_id', 'dc.id') // dc for destination city
            ->join('cities as dch', 'dc.hub_id', 'dch.id') // dch for destination city hub
            ->leftjoin('zones as dcz', 'dcz.id', 'dc.zone_id') // dcz for destination city zone
            ->leftJoin('shipments_journey as sja', function ($join) use ($connection) { // only fetch max arrival
                $join->on('sja.shipment_id', 'shipments.id')
                    ->where(
                        'sja.id',
                        '=',
                        DB::connection($connection)->raw("(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)")
                    );
            })
            ->select( 
                'shipments.id as shipment_id', 
                'shipments.tracking_number',  
                'u.id as account_no', 
                'u.name as shipper', 
                'segments.name as sub_segment', 
                'shipments.order_id as order_id', 
                'sm.mode as shipping_mode',
                'bt.booking_type as service_type',
                'bc.name as category',
                'si.description as description',
                'oc.name as origin',
                'och.name as origin_hub',
                'ocz.name as origin_zone',
                'dc.name as destination', 
                'dch.name as destination_hub',
                'dcz.name as destination_zone',
                'sja.created_at as arrival_date',
                'shipments.actual_weight as weight',
                'si.quantity as quantity'
            );
            if($mode == 'test'){
                $shipments->whereDate('sja.created_at', Carbon::today())
                ->where('u.id', 24032);
            }else{
                $shipments->whereBetween('sja.created_at', [$from, $to]);
            }
            
            $shipments = $shipments->get();

            $data = [];

            if(count($shipments) > 0)
            {
                foreach ($shipments as $key => $shipment) {
                    $data[$key]['s_no'] =$key+1;
                    $data[$key]['tracking_number'] = $shipment->tracking_number;
                    $data[$key]['account_no'] = $shipment->account_no;
                    $data[$key]['shipper'] = $shipment->shipper;
                    $data[$key]['sub_segment'] = $shipment->sub_segment;
                    $data[$key]['order_id'] = $shipment->order_id;
                    $data[$key]['origin'] = $shipment->origin;
                    $data[$key]['origin_hub'] = $shipment->origin_hub;
                    $data[$key]['origin_zone'] = $shipment->origin_zone;
                    $data[$key]['destination'] = $shipment->destination;
                    $data[$key]['destination_hub'] = $shipment->destination_hub;
                    $data[$key]['destination_zone'] = $shipment->destination_zone;
                    $data[$key]['shipping_mode'] = $shipment->shipping_mode;
                    $data[$key]['service_type'] = $shipment->service_type;
                    $data[$key]['category'] = $shipment->category;
                    $data[$key]['description'] = $shipment->description;
                    $data[$key]['arrival_date'] = $shipment->arrival_date;
                    $data[$key]['quantity'] = $shipment->quantity;
                    $data[$key]['weight'] = $shipment->weight;
                    $data[$key]['first_admin_trax_id'] = '-';
                    $data[$key]['first_admin_name'] = '-';
                    $data[$key]['first_rider_trax_id'] = '-';
                    $data[$key]['first_rider_name'] = '-';
                    $data[$key]['first_status_hub'] = '-';
                    $data[$key]['first_status'] = '-';
                    $data[$key]['first_reason'] = '-';
                    $data[$key]['first_status_date'] = '-';
                    $data[$key]['current_admin_trax_id'] = '-';
                    $data[$key]['current_admin_name'] = '-';
                    $data[$key]['current_rider_trax_id'] = '-';
                    $data[$key]['current_rider_name'] = '-';
                    $data[$key]['current_status_hub'] = '-';
                    $data[$key]['current_status'] =  '-';
                    $data[$key]['current_reason'] = '-';
                    $data[$key]['current_remarks'] = '-';
                    $data[$key]['current_status_date'] = '-';
                    $data[$key]['total_attempt'] = '-';
                    $data[$key]['return_reason'] = '-';
                    $data[$key]['transit_date'] = '-';
                    $data[$key]['transit_status'] = '-';
                    $data[$key]['arrived_at_destination_date'] = '-';
                    $data[$key]['rcp_confirm_date'] = '-';
                    $data[$key]['first_attempt_lead_days'] = '-';
                    $data[$key]['transit_lead_days'] = '-';
                    $data[$key]['last_status_lead_days'] = '-';
    
                    $shipment_statuses = ShipmentStatus::where('status',1)->pluck('name','id')->toArray();
    
                    $journey = DB::connection($connection)->table('shipments_journey')->where('shipment_id', $shipment->shipment_id)->latest()->first();
    
                    $first_status_journey = DB::connection($connection)->table('shipments_journey')->where('shipment_id', $shipment->shipment_id)->where(
                        'id', '>',
                        DB::connection($connection)->raw("(select min(id) from shipments_journey where shipment_id = $shipment->shipment_id and shipments_journey.shipper_status_id = 5)")
                    )->first();
    
                    if($first_status_journey){
    
                        if($first_status_journey->admin_id != null)
                        {
                            $admin = Admin::find($first_status_journey->admin_id);
                            if($admin)
                            {
                                $data[$key]['first_status_hub'] = $admin->city->hub_city->name ?? '-' ;
                            }
    
                            $data[$key]['first_admin_name'] = $admin->name ?? '-';
                            $data[$key]['first_admin_trax_id'] = $admin->trax_id ?? '-';
    
                        }
                        else if($first_status_journey->rider_id != null){
    
                            $rider = Rider::find($first_status_journey->rider_id);
                            if($rider)
                            {
                                $data[$key]['first_status_hub'] = $rider->city->hub_city->name ?? '-';
                            }
    
                            $data[$key]['first_rider_name'] = $rider->name ?? '-';
                            $data[$key]['first_rider_trax_id'] = $rider->trax_id ?? '-';
                        }
                       
    
                        $data[$key]['first_status'] =  $shipment_statuses[$first_status_journey->shipper_status_id] ?? '-';
                        $firstReasonId = $first_status_journey->status_reason_id;
                        $shipmentStatusReason = ShipmentStatusReason::find($firstReasonId);
                        $firstReason = $shipmentStatusReason ? $shipmentStatusReason->name : '-';
                        $data[$key]['first_reason'] = $firstReason;
                        $data[$key]['first_status_date'] = $first_status_journey->created_at;
                        
                    }
    
                    $current_status_journey = DB::connection($connection)->table('shipments_journey')->where('shipment_id', $shipment->shipment_id)->where(
                        'id',
                        DB::connection($connection)->raw("(select max(id) from shipments_journey where shipment_id = $shipment->shipment_id)")
                    )->first();
    
                    if($current_status_journey){
    
                        if($current_status_journey->admin_id != null)
                        {
                            $admin = Admin::find($current_status_journey->admin_id);
                            if($admin)
                            {
                                $data[$key]['current_status_hub'] = $admin->city->hub_city->name ?? '-' ;
                            }
    
                            $data[$key]['first_admin_name'] = $admin->name ?? '-';
                            $data[$key]['first_admin_trax_id'] = $admin->trax_id ?? '-';
    
                        }
                        else if($current_status_journey->rider_id != null){
    
                            $rider = Rider::find($current_status_journey->rider_id);
                            if($rider)
                            {
                                $data[$key]['current_status_hub'] = $rider->city->hub_city->name ?? '-';
                            }
    
                            $data[$key]['current_rider_name'] = $rider->name ?? '-';
                            $data[$key]['current_rider_trax_id'] = $rider->trax_id ?? '-';
                        }
    
                        $data[$key]['current_status'] =  $shipment_statuses[$current_status_journey->shipper_status_id] ?? '-';
                        $currentReasonId = $current_status_journey->status_reason_id;
                        $shipmentStatusReason = ShipmentStatusReason::find($currentReasonId);
                        
                        if ($shipmentStatusReason) {
                            $currentReason = $shipmentStatusReason->name;
                        } else {
                            $currentReason = '-';
                        }
                        
                        $data[$key]['current_reason'] = $currentReason;
                        
                        $data[$key]['current_remarks'] = $current_status_journey->remarks;
                        $data[$key]['current_status_date'] = $current_status_journey->created_at;
                    }
    
                    $total_attempts = DB::connection($connection)->table('shipments_journey')->where('shipment_id',$shipment->shipment_id)
                        ->where(function ($query) {
    
                            $query->where(function ($query2) {
                                $query2->whereIn('shipper_status_id', [14,25,30,31])
                                ->whereNull('status_reason_id');
                            });
    
                            // shipper_status_id in (14,25,30,31) and status_reason_id is null
    
                            $query->orWhere(function ($query3) {
                                $query3->where('shipper_status_id', 56)
                                ->whereIn('status_reason_id',[31,32,33]);
                            });
    
                            // shipper_status_id = 56 and status_reason_id in (31,32,33)
    
                            $query->orWhere(function ($query4) {
                                $query4->where('shipper_status_id', 8)
                                ->whereIn('status_reason_id',[1,3,4,6,13,28,60,63]);
                            });
                            
                            // shipper_status_id = 8 and status_reason_id in (1,3,4,6,13,28,60,63)
                            
                            $query->orWhere(function ($query5) {
                                $query5->where('shipper_status_id', 9)
                                ->where('status_reason_id',18);
                            });    
                            $query->orWhere(function ($query6) {
                                $query6->where('shipper_status_id', 12)
                                ->whereIn('status_reason_id',[1,3,4,5,6,7,8,19]);
                            });
    
                            // shipper_status_id = 12 and status_reason_id in (1,3,4,5,6,7,8,19)
    
                            $query->orWhere(function ($query7) {
                                $query7->where('shipper_status_id', 24)
                                ->whereIn('status_reason_id',[6,8,30,64]);
                            });
    
                            // shipper_status_id = 24 and status_reason_id in (6,8,30,64)
    
                            $query->orWhere(function ($query8) {
                                $query8->where('shipper_status_id', 48)
                                ->where('status_reason_id',18);
                            });
    
                            // shipper_status_id = 48 and status_reason_id = 18
    
                            $query->orWhere(function ($query9) {
                                $query9->where('shipper_status_id', 60)
                                ->whereIn('status_reason_id',[81,82,84,85,86]);
                            });
    
                            // shipper_status_id = 60 and status_reason_id in (81,82,84,85,86)
                            
                        })
                        ->where('verification',1);
    
                        
    
                    if($total_attempts->exists())
                    {
                        $total_attempts_count = count($total_attempts->get());
                        $total_attempts_first_attempt = $total_attempts->first();
    
                        $data[$key]['total_attempt'] = $total_attempts_count;
    
                        $arrival = Carbon::parse($shipment->arrival_date)->startOfDay(); 
                        $first_attempt_status = Carbon::parse($total_attempts_first_attempt->created_at)->endOfDay(); 
                        $first_attempt_status_days = $arrival->diffInDays($first_attempt_status);
                        
                        if ($first_attempt_status_days == 0) {
                            $data[$key]['first_attempt_lead_days'] = "-";
                        } else {
                            $data[$key]['first_attempt_lead_days'] = $first_attempt_status_days;
                        }
                        }
    
                    $return_reason = DB::connection($connection)->table('shipments_journey')->where('shipment_id', $shipment->shipment_id)->where(
                        'id',
                        DB::connection($connection)->raw("(select max(id) from shipments_journey where shipment_id = $shipment->shipment_id and shipper_status_id in (20,21,22,23,24,25,47,48,60))")
                    )->first();
                    
                    if($return_reason)
                    {
                        $returnReasonId = $return_reason->status_reason_id;

                        if ($returnReasonId) {
                            $shipmentStatusReason = ShipmentStatusReason::find($returnReasonId);
                            $returnReason = $shipmentStatusReason ? $shipmentStatusReason->name : '-';
                        } else {
                            $returnReason = '-';
                        }
                        
                        $data[$key]['return_reason'] = $returnReason;
                    }
    
                    $transit_journey = DB::connection($connection)->table('shipments_journey')->where('shipment_id', $shipment->shipment_id)->where(
                        'id',
                        DB::connection($connection)->raw("(select max(id) from shipments_journey where shipment_id = $shipment->shipment_id and shipper_status_id in (3,21,26,32))")
                    )->first();
    
                    if($transit_journey)
                    {
                        $data[$key]['transit_date'] = $transit_journey->created_at;
                        $data[$key]['transit_status'] = $shipment_statuses[$transit_journey->shipper_status_id];
                    }
    
                    $arrived_at_destination_journey = DB::connection($connection)->table('shipments_journey')->where('shipment_id', $shipment->shipment_id)->where(
                        'id',
                        DB::connection($connection)->raw("(select max(id) from shipments_journey where shipment_id = $shipment->shipment_id and shipper_status_id = 4)")
                    )->first();
    
                    if($arrived_at_destination_journey)
                    {
                        $data[$key]['arrived_at_destination_date'] = $arrived_at_destination_journey->created_at;
                    }
    
                    $rcpconfirm_date_journey = DB::connection($connection)->table('shipments_journey')->where('shipment_id', $shipment->shipment_id)->where(
                        'id',
                        DB::connection($connection)->raw("(select max(id) from shipments_journey where shipment_id = $shipment->shipment_id and shipper_status_id in (13,20))")
                    )->first();
    
                    if($rcpconfirm_date_journey)
                    {
                        $data[$key]['rcp_confirm_date'] = $rcpconfirm_date_journey->created_at;
                    }
    
                    if($transit_journey && $shipment->arrival_date != null)
                    {
                        $arrival = Carbon::parse($shipment->arrival_date)->startOfDay(); 
                        $transit = Carbon::parse($transit_journey->created_at)->endOfDay(); 
                        $transit_lead_days = $arrival->diffInDays($transit);
                        
                        if ($transit_lead_days == 0) {
                            $data[$key]['transit_lead_days'] = "-";
                        } else {
                            $data[$key]['transit_lead_days'] = $transit_lead_days;
                        }
                    }
    
                    if($current_status_journey && $shipment->arrival_date != null)
                    {
                        $arrival = Carbon::parse($shipment->arrival_date)->startOfDay(); 
                        $last_status = Carbon::parse($current_status_journey->created_at)->endOfDay(); 
                        $last_status_days = $arrival->diffInDays($last_status);
                        
                        if ($last_status_days == 0) {
                            $data[$key]['last_status_lead_days'] = "-";
                        } else {
                            $data[$key]['last_status_lead_days'] = $last_status_days;
                        }
                    }
                }

                $data_header[0] = ['S. No.', 'Tracking No.', 'Account No.', 'Shipper', 'Sub Segment', 'Order ID', 'Origin', 'Origin Hub', 'Origin Zone', 'Destination', 'Destination Hub', 'Destination Zone', 'Shipping Mode', 'Service Type', 'Category', 'Description', 'Arrival Date', 'Quantity', 'Weight', 'First Admin Trax ID', 'First Admin', 'First Rider Trax ID', 'First Rider', 'First Status Hub', 'First Status', 'First Reason', 'First Status Date', 'Current Admin Trax ID', 'Current Admin', 'Current Rider Trax ID', 'Current Rider', 'Current Status Hub', 'Current Status', 'Current Reason', 'Current Remark', 'Current Status Date', 'Total Attempt', 'Return Reason', 'Tansit Date', 'Transit Status', 'Arrived at Destination Date', 'RCP Confirm Date', 'First Attempt Lead Days', 'Transit Lead Days', 'Last Status Lead Days'];
                
                $data = array_merge($data_header, $data);
    
                // creating excel header fonts
                $spreadsheet = new Spreadsheet();
                $cell_st = [
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                    'borders' => array(
                        'allBorders' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => array('argb' => '000000'),
                        )
                    ),
                ];
                
                // creating excel header format
                $sheet = $spreadsheet->getActiveSheet();
    
                $sheet->getDefaultColumnDimension()->setWidth(20);
                $sheet->getStyle('A1:AT1')
                    ->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('CECECE');
                $sheet->getStyle('A1:AT1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('A1:AT1')->applyFromArray($cell_st);
                $sheet->fromArray($data, NULL, 'A1', true);
                $sheet->getStyle('C1')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);
                $sheet->getColumnDimension('A','G')->setWidth(10);
                $sheet->getColumnDimension('C')->setWidth(30);
                
                // writing data into excel
                $writer = new Xlsx($spreadsheet);
                $to = Carbon::parse($to)->subDay();
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="operations_performance_report.xlsx"');
                header('Cache-Control: max-age=0');
                $date_file_name = Carbon::parse($from)->format('Y_m_d') . "_to_". Carbon::parse($to)->format('Y_m_d');
                $time_string = Carbon::now()->toTimeString();
                $time_string = Carbon::parse($time_string)->format('h_i_s');
                $file_name_without_path = "operations_performance_report_" . $date_file_name  . ".xlsx";
                $file_name = public_path() . '/storage/OperationReports/' . $file_name_without_path;  
                Storage::disk('public')->put($file_name_without_path, file_get_contents($file_name));
                $writer->save($file_name);
                if($id == 224){
                    NotificationsController::send(224, $file_name_without_path, $file_name);
                }else{
                    NotificationsController::send(225, $file_name_without_path, $file_name);
                }
            }
            else{
                return false;
            }
        }
}

?>