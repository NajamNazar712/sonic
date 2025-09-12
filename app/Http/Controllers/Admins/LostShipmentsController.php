<?php

namespace App\Http\Controllers\Admins;

use Carbon\Carbon;
use App\Http\Models\Rider;
use App\UserLostShipmentHub;
use Illuminate\Http\Request;
use App\Http\Models\Shipment;
use Illuminate\Validation\Rule;
use App\Http\Models\Admin\Admin;
use App\Http\Models\BookingType;
use App\Http\Models\HR\Employee;
use App\LostShipmentResponsible;
use App\LostShipmentStatusCount;
use Yajra\Datatables\Datatables;
use App\Http\Models\ShippingMode;
use Illuminate\Support\Facades\DB;
use App\Http\Models\ShipmentStatus;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\CargoConsignment;
use App\Http\Models\ShipmentsJourney;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Http\Models\ShipmentStatusReason;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\Admin\LostShipmentAdmin;
use App\Http\Models\ManifestBagLostShipment;
use App\Http\Models\CargoConsignmentShipment;
use App\Http\Models\PackagingMaterialRequest;
use App\Http\Models\Admin\LostShipmentShipper;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\CargoManifest\ManifestBag;
use App\Http\Models\PackagingMaterialRequestHistory;
use App\Http\Models\Admin\CargoManifest\CargoManifest;
use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Models\Admin\CargoManifest\CargoManifestBag;
use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Controllers\CargoManifestBagJourneyController;
use App\Http\Controllers\ShipmentScanningJourneyController;
use App\Http\Controllers\Admins\CheckDisputeShipmentsController;
use App\Http\Models\Admin\CargoManifest\CargoManifestBagJourney;
use App\Http\Models\Admin\CargoManifest\CargoManifestBagShipments;

class LostShipmentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }
    public function lost_shipments_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),48);
        $shipment_status = ShipmentStatus::select('id', 'name')->get();
        $shipping_mode = ShippingMode::all();
        $service_type = BookingType::all();
        $return_confirm_reasons = ShipmentStatusReason::whereIn('id', [2, 5, 8, 9, 10, 12, 19, 20, 34, 38, 39, 40, 41, 42])->select('id', 'name')->get();

        $responsible_hubs = UserLostShipmentHub::where('admin_id', session('id'))->get()->pluck('hub_id')->toArray();
        $admin_responsible_hubs = implode(',', $responsible_hubs);

        $shipments = Shipment::join('shipments_journey', function ($join) use ($responsible_hubs, $admin_responsible_hubs) {
            if (session('role_id') != 1 && count($responsible_hubs) > 0) {
                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                ->where('shipments_journey.id', '=', DB::raw("(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id AND shipments_journey.city_id IN ($admin_responsible_hubs))"));
            } else {
                $join->where('shipments_journey.id', '=', DB::raw("(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)"));
            }
        })
        ->where('shipments.shipper_status_id', 18);
        
        // COUNT(*) as total,
        $shipmentsCounts = $shipments
            ->selectRaw('
                SUM(IF(shipments_journey.verification = 1, 1, 0)) as approved,
                SUM(IF(shipments_journey.verification = 0, 1, 0)) as pending
            ')
            ->first();
        
        // $lost_shipments = $shipmentsCounts->total;
        // $total_of_approved_shipments = $shipmentsCounts->approved;
        $total_of_pending_shipments = $shipmentsCounts->pending;
        // $total_of_rejected_shipments = LostShipmentStatusCount::sum('rejection_count');
        
        
        $today = Carbon::now()->endOfDay();
        $thirtyDays = Carbon::now()->subDays(30)->startOfDay();
        $thirtyOneDays = Carbon::now()->subDays(31)->startOfDay();

        $total_of_rejected_shipments = LostShipmentStatusCount::where('updated_at','>=', $thirtyOneDays)->sum('rejection_count');
        $total_approval = LostShipmentStatusCount::where('updated_at','>=', $thirtyOneDays)->sum('approval_count');
        $lost_shipments = $total_of_pending_shipments + $total_of_rejected_shipments + $total_approval;

        return view('admin.lost.index')->with(['shipment_status' => $shipment_status, 'shipping_mode' => $shipping_mode, 'service_type' => $service_type, 'return_confirm_reasons' => $return_confirm_reasons,'lost_shipments'=>$lost_shipments, 'total_of_approved_shipments'=>$total_approval, 'total_of_pending_shipments'=>$total_of_pending_shipments, 'total_of_rejected_shipments'=> $total_of_rejected_shipments, 'today' => $today, 'thirtyday' => $thirtyDays]);
        
    }

    // static public function updateLostShipmentApproval($shipment_id, $fieldToUpdate, $clearedValue) {
    //     $lost_shipment_approval = LostShipmentStatusCount::where('shipment_id', $shipment_id);
        
    //     if(!$lost_shipment_approval->exists()) {
    //         LostShipmentStatusCount::create(['shipment_id'=> $shipment_id, $fieldToUpdate => 1, 'cleared' => $clearedValue]);
    //     } else {
    //         $lost_shipment_approval = $lost_shipment_approval->first();
    //         $field_value = $lost_shipment_approval->$fieldToUpdate;
    //         $lost_shipment_approval->update([
    //             $fieldToUpdate => in_array(944, session('permission')) ?  $field_value + 1 : $field_value,
    //             'cleared' => $clearedValue
    //         ]);
    //     }
    // }

    static public function updateLostShipmentApproval($shipment_id, $fieldToUpdate, $clearedValue) {
        $lost_shipment_approval = LostShipmentStatusCount::where('shipment_id', $shipment_id)->first();
        
        if(!$lost_shipment_approval) {
            LostShipmentStatusCount::create(['shipment_id' => $shipment_id, $fieldToUpdate => 1, 'cleared' => $clearedValue]);
        } else {
            $field_value = $lost_shipment_approval->$fieldToUpdate;
            $newValue = in_array(944, session('permissions')) ? $field_value + 1 : $field_value;
            $lost_shipment_approval->update([
                $fieldToUpdate => $newValue,
                'cleared' => $clearedValue
            ]);
        }
    }
    
    public function lost_shipments_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),108);
        }
            $responsible_hubs = UserLostShipmentHub::where('admin_id', session('id'))->get()->pluck('hub_id')->toArray();
            $admin_responsible_hubs = implode(',', $responsible_hubs);

            $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
                ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
                ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
                ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
                ->join('cities as h', 'dc.hub_id', '=', 'h.id')
                ->leftJoin('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
                ->leftJoin('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
                ->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
                ->join('shipments_journey', function ($join) use ($responsible_hubs, $admin_responsible_hubs) {
                    if (session('role_id') != 1 && count($responsible_hubs) > 0) {
                        $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                        ->where('shipments_journey.id', '=', DB::raw("(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id AND shipments_journey.city_id IN ($admin_responsible_hubs))"));
                    } else {
                        $join->where('shipments_journey.id', '=', DB::raw("(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)"));
                    }
                })
                ->join('cities as ci', 'shipments_journey.city_id', '=', 'ci.id')
                ->join('zones as zo', 'zo.id', '=', 'ci.zone_id')
                ->leftJoin('admins as ad', 'ad.id', '=', 'shipments_journey.admin_id')
                ->leftJoin('shipments_journey as sj', function ($join) {
                    $join->on('sj.shipment_id', '=', 'shipments.id')
                        ->where('sj.id', '=',
                            DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
                })
                ->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'shipments_journey.status_reason_id')
                ->leftjoin('lost_shipment_responsibles as lsr', 'lsr.shipment_id', '=', 'shipments.id')
                ->leftJoin('lost_shipment_status_counts as lssc', 'lssc.shipment_id', '=', 'shipments.id')
                ->leftJoin('lost_shipment_status_counts as lssc_new', 'lssc_new.shipment_id', '=', 'shipments.id')

                ->select('shipments.id as shId', 'shipments.tracking_number as tracking_number_link', 'shipments.tracking_number','shipments.user_id as shipper_id', 'u.name as shipper', 'oc.name as origin', 'dc.name as destination', 'h.name as hub', 'shipments.consignee_name', 'shipments.consignee_phone_number_1 as phone', 'shipments.consignee_address', 'shipments.amount', 'sm.mode as shipping_mode', 'bt.booking_type as service_type', 'ss.name as status', 'ssr.name as reason', 'shipments_journey.remarks as remarks', 'shipments_journey.created_at as status_date', 'shipments_journey.created_at as current_status_date', 'sj.created_at as arrival','shipments.payment_status_id', 'shipments.booking_type_id', 'usi.poc', 'shipments_journey.reference_1_id as reference','ad.name as marked_by', 'lsr.shipment_id as responsible_person_shipment','shipments_journey.updated_at as marked_at','lssc.approval_count as approval','lssc.cleared as cleared','lssc.shipment_id as shipment_cleared', 'shipments_journey.verification as verification', 'lssc_new.shipment_id as null_shipment', 'ci.name as last_hub_name', 'zo.name as last_zone_name', 'shipments.parcel_value','shipments_journey.status_reason_id as status_reason_id')
                ->where('shipments.shipper_status_id', 18)->groupBy('shipments.id');
            if(session('role_id') != 1){
                $check_lost_shipments_admins = LostShipmentAdmin::where('admin_id', Auth::id());
                $lost_shipments_shippers_id = LostShipmentShipper::pluck('user_id')->toArray();
                if(!empty($lost_shipments_shippers_id)){
                    if($check_lost_shipments_admins->exists()){
                            $shipments = $shipments->whereIn('shipments.user_id', $lost_shipments_shippers_id);
                    }
                    else{
                            $shipments = $shipments->whereNotIn('shipments.user_id', $lost_shipments_shippers_id);
                    }
                }
            }

            if ($request->get('search_total_lost_shipments') === "1") {
                $shipments;
            }

            if ($request->get('search_total_lost_shipments') === "1") {
                $shipments;
            }   
            
            if ($tracking_numbers = $request->get('tracking_numbers')) {
                $shipments->whereIn('shipments.tracking_number', explode(',', $tracking_numbers));
            }

            if ($request->get('search_total_lost_pending_shipments') === "3") {
                $shipments->where('shipments_journey.verification', 0);
            }

            if (isset($request->search_lost_status) && $request->search_lost_status == '0') {
                $shipments->where('shipments_journey.verification', 0);
            }

            if (isset($request->search_lost_status) && $request->search_lost_status == '1') {
                $shipments->where('shipments_journey.verification', 1);
            }
            
            if ($request->get('search_from') && $request->get('search_to')) {
                $from = $request->get('search_from');
                $to = $request->get('search_to');
                $shipments->whereBetween('shipments_journey.updated_at',[$from, $to]);
            }

            return Datatables::of($shipments)
                ->editColumn('tracking_number_link', function ($shipments) {
                    $route = route('admin.tracking.index');
                    return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
                })
                
                ->editColumn('arrival', function ($shipments) {
                    if ($shipments->arrival) {
                        return $shipments->arrival;
                    } else {
                        return " - ";
                    }
                })
                ->editColumn('reference', function ($shipments) {
                    if ($shipments->reference) {
                        return str_pad($shipments->reference, 6, '0', STR_PAD_LEFT);
                    } else {
                        return " - ";
                    }
                })
               ->editColumn('remarks', function ($shipments) {
                    if (!empty($shipments?->remarks)) {
                        return $shipments->remarks;
                    } 
                    elseif ($shipments?->status_reason_id != 90 && empty($shipments?->remarks)) {
                        $fallback = ShipmentsJourney::where([
                            'shipment_id' => $shipments?->shId,
                            'status_reason_id' => 90
                        ])->latest()->first();

                        return $fallback?->remarks ?? '-';
                    } else {
                        return "-";
                    }
                })
                ->editColumn('amount', function($shipment){
                    return number_format($shipment->amount);
                })
                ->editColumn('parcel_value', function($shipment){
                    return number_format($shipment->parcel_value);
                })
                ->editColumn('shipper', function ($shipment) {
                    if ($shipment->booking_type_id == 4) {
                        return $shipment->shipper .' (' . $shipment->poc . ')';
                    }
                    else {
                        return $shipment->shipper;
                    }
                })
                ->addColumn('aging',function ($shipment){
                    if(session('role_id') == 1) {
                        return 0;
                    }
                    else {
                        if (LostShipmentShipper::where('user_id', $shipment->shipper_id)->exists()) {
                            return 0;
                        }
                        else {
                            $today = Carbon::now();
                            return $today->diffInDays($shipment->status_date);
                        }
                    }
                })
                ->editColumn('responsible_person_shipment', function ($shipment) {
                    if ($shipment->responsible_person_shipment != 0) {
                        $responsible_person_shipment = LostShipmentResponsible::where('shipment_id', $shipment->responsible_person_shipment)
                        ->where('updated_at', function($query) use ($shipment) {
                            $query->selectRaw('MAX(updated_at)')
                                ->from('lost_shipment_responsibles')
                                ->where('shipment_id', $shipment->responsible_person_shipment);
                        })
                        ->count();
                        return '<button class="btn btn-sm btn-outline-info align-middle responsible_person_shipment" data-shipment-id="' . $shipment->responsible_person_shipment . '">' . $responsible_person_shipment . '</button>';
                    } else {
                        return 0;
                    }
                })

                // new responsible persons searching filter
                ->filterColumn('responsible_person_shipment', function($query, $keyword) {
                    $trimmedKeyword = trim($keyword);
                    if ($trimmedKeyword !== '') {
                        if (is_numeric($trimmedKeyword)) {
                            $keyword = (int) $trimmedKeyword;
                            $query->whereRaw("(
                                SELECT COUNT(*)
                                FROM lost_shipment_responsibles ls
                                WHERE ls.shipment_id = shipments.id
                                AND ls.updated_at = (
                                    SELECT MAX(updated_at)
                                    FROM lost_shipment_responsibles
                                    WHERE lost_shipment_responsibles.shipment_id = shipments.id
                                )
                            ) = ?", [$keyword]);
                        } else {
                            // If a non-numeric string is entered, force false condition to return no rows
                            $query->whereRaw('1 = 0');
                        }
                    }
                })

                ->addColumn('excel_responsible_person_id', function ($shipment) {
                    $trax_ids = [];
                    $responsible_person_shipments = LostShipmentResponsible::where('shipment_id', $shipment->responsible_person_shipment)
                        ->where('updated_at', function($query) use ($shipment) {
                            $query->selectRaw('MAX(updated_at)')
                                ->from('lost_shipment_responsibles')
                                ->where('shipment_id', $shipment->responsible_person_shipment);
                        })
                        ->groupBy('user_id')
                        ->get();
                    foreach($responsible_person_shipments as $responsible_person_shipment){
                        if($responsible_person_shipment->user_type == 1){
                            $admin = Admin::find($responsible_person_shipment->user_id)->trax_id;
                            $trax_ids[] = $admin;
                        }else{
                            $rider = Rider::find($responsible_person_shipment->user_id)->trax_id;
                            $trax_ids[] = $rider;
                        }
                    }
                    $trax_ids = implode(' ,', $trax_ids);
                    return $trax_ids;

                })

                ->addColumn('excel_responsible_person_name', function ($shipment) {
                    $names = [];
                    $responsible_person_shipments = LostShipmentResponsible::where('shipment_id', $shipment->responsible_person_shipment)
                    ->where('updated_at', function($query) use ($shipment) {
                        $query->selectRaw('MAX(updated_at)')
                            ->from('lost_shipment_responsibles')
                            ->where('shipment_id', $shipment->responsible_person_shipment);
                    })
                    ->groupBy('user_id')
                    ->get();
                    foreach($responsible_person_shipments as $responsible_person_shipment){
                        if($responsible_person_shipment->user_type == 1){
                            $admin = Admin::find($responsible_person_shipment->user_id)->name;
                            $names[] = $admin;
                        }else{
                            $rider = Rider::find($responsible_person_shipment->user_id)->name;
                            $names[] = $rider;
                        }
                    }
                    $names = implode(' ,', $names);
                    return $names;

                })
                ->addColumn('excel_responsible_person_type', function ($shipment) {
                    $types = [];
                    $responsible_person_shipments = LostShipmentResponsible::where('shipment_id', $shipment->responsible_person_shipment)
                    ->where('updated_at', function($query) use ($shipment) {
                        $query->selectRaw('MAX(updated_at)')
                            ->from('lost_shipment_responsibles')
                            ->where('shipment_id', $shipment->responsible_person_shipment);
                    })
                    ->groupBy('user_id')
                    ->get();
                    foreach($responsible_person_shipments as $responsible_person_shipment){
                        if($responsible_person_shipment->user_type == 1){
                            $admin = Admin::find($responsible_person_shipment->user_id)->employee->employee_type->name;
                            $types[] = $admin;
                        }else{
                            $rider = Rider::find($responsible_person_shipment->user_id)->employee->employee_type->name;
                            $types[] = $rider;
                        }
                    }
                    $types = implode(' ,', $types);
                    return $types;

                })
                ->addColumn('excel_responsible_person_status', function ($shipment) {
                    $status = [];
                    $responsible_person_shipments = LostShipmentResponsible::where('shipment_id', $shipment->responsible_person_shipment)
                    ->where('updated_at', function($query) use ($shipment) {
                        $query->selectRaw('MAX(updated_at)')
                            ->from('lost_shipment_responsibles')
                            ->where('shipment_id', $shipment->responsible_person_shipment);
                    })
                    ->groupBy('user_id')
                    ->get();
                    foreach($responsible_person_shipments as $responsible_person_shipment){
                        if($responsible_person_shipment->user_type == 1){
                            $admin = Admin::find($responsible_person_shipment->user_id)->employee->employee_status->name;
                            $status[] = $admin;
                        }else{
                            $rider = Rider::find($responsible_person_shipment->user_id)->employee->employee_status->name;
                            $status[] = $rider;
                        }
                    }
                    $status = implode(' ,', $status);
                    return $status;
                })
                ->addColumn('permission',function ($shipment){
                    if (in_array(944, session('permissions'))){
                        return 944;
                    }
                })
                ->addColumn('lost_confirmation_status', function($shipment){
                    if(($shipment->approval >= 1 && $shipment->cleared === 1) || $shipment->verification == 1){
                        return 'Approved';
                    }else if($shipment->approval >= 0 && $shipment->cleared === 0){
                        return 'Pending';
                    }
                })

                // Searching feature for status
                ->filterColumn('lost_confirmation_status', function($query, $keyword) {
                    $keyword = strtolower($keyword);
                    if (str_contains('approved', $keyword)) {
                        $query->where(function($q) {
                            $q->where('lssc.approval_count', '>=', 1)
                            ->where('lssc.cleared', 1)
                            ->orWhere('shipments_journey.verification', 1);
                        });
                    } elseif (str_contains('pending', $keyword)) {
                        $query->where(function($q) {
                            $q->where('lssc.approval_count', '>=', 0)
                                ->where('lssc.cleared', 0);
                        });
                    } 
                    // Return no results if no match
                    else {
                        $query->whereRaw('1 = 0');
                    }
                })

                // Ordering for confirm status
                ->orderColumn('lost_confirmation_status', function($query, $order) {
                    $direction = strtoupper($order) === 'ASC' ? 'asc' : 'desc';
                    $query->orderByRaw("CASE
                            WHEN (lssc.approval_count >= 1 AND lssc.cleared = 1) OR shipments_journey.verification = 1 THEN 1
                            WHEN lssc.approval_count >= 0 AND lssc.cleared = 0 THEN 0
                            ELSE 2
                        END $direction")
                    ->orderBy('shipments.id', $direction);
                })

                ->addColumn('action', function ($shipment) {
                    if (session('role_id') == 1 || in_array(944, session('permissions'))) {

                        $dropdown = '
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                            <div class="dropdown-menu dropdown-menu-sm accounts">
                        ';
                
                        if($shipment->verification != 1){
                            $dropdown .= '<button type="button" class="dropdown-item approve" data-id="' . $shipment->shId . '"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-minus-circle"></i></div><div class="col-9 offset-1">Approve Lost Shipment</div></div></button>';
                        }

                        if($shipment->verification == 0){
                            $dropdown .= '<button type="button" class="dropdown-item reject" data-id="' . $shipment->shId . '"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-minus-circle"></i></div><div class="col-9 offset-1 reject">Reject Lost Shipment</div></div></button>';
                        }
                        $dropdown .= '
                            </div>
                        </div>
                        ';
                    
                        return $dropdown;
                    }
                    
                    return '-';

                })
                ->filterColumn('u.name', function ($query, $keyword) {
                    $query->where(function ($sub_query) use ($keyword) {
                        $sub_query->where('shipments.booking_type_id', '!=', 4)
                            ->where('u.name', 'like', '%' . $keyword . '%');
                    })
                        ->orWhere(function ($sub_query) use ($keyword) {
                            $sub_query->where('shipments.booking_type_id', '=', 4)
                                ->where('usi.poc', 'like', '%' . $keyword . '%');
                        });
                })
                ->orderColumn('u.name', 'u.name $1, usi.poc $1')
                ->filterColumn('status', function ($query, $keyword) {

                    if ($keyword != '') {
                        $query->where('ss.id', $keyword);
                    } else {
                        $query->whereRaw('false');
                    }
                })
                ->filterColumn('shipping_mode', function ($query, $keyword) {

                    if ($keyword != '') {
                        $query->where('sm.id', $keyword);
                    } else {
                        $query->whereRaw('false');
                    }
                })
                ->filterColumn('service_type', function ($query, $keyword) {

                    if ($keyword != '') {
                        $query->where('bt.id', $keyword);
                    } else {
                        $query->whereRaw('false');
                    }
                })

                ->addColumn('old_responsible_person', function($shipment){
                    if ($shipment->responsible_person_shipment != 0) {
                        $old_responsible_person_shipment = LostShipmentResponsible::where('shipment_id', $shipment->responsible_person_shipment)
                        ->where('updated_at', '<', function($query) use ($shipment) {
                            $query->selectRaw('MAX(updated_at)')
                                ->from('lost_shipment_responsibles')
                                ->where('lost_shipment_responsibles.shipment_id', $shipment->responsible_person_shipment);
                        })
                        ->count();
                        if ($old_responsible_person_shipment == 0){
                            return 0;
                        } else {
                            return '<button class="btn btn-sm btn-outline-info align-middle old_responsible_person" data-shipment-id="' . $shipment->responsible_person_shipment . '">' . $old_responsible_person_shipment . '</button>';
                        }
                    } else {
                        return 0;
                    }
                })

                ->filterColumn('old_responsible_person', function($query, $keyword) {
                    $trimmedKeyword = trim($keyword);
                    if ($trimmedKeyword !== '') {
                        if (is_numeric($trimmedKeyword)) {
                            $keyword = (int) $trimmedKeyword;
                            $query->whereRaw("(
                                SELECT COUNT(*) 
                                FROM lost_shipment_responsibles ls
                                WHERE ls.shipment_id = shipments.id 
                                AND ls.updated_at < (
                                    SELECT MAX(updated_at)
                                    FROM lost_shipment_responsibles
                                    WHERE lost_shipment_responsibles.shipment_id = shipments.id
                                )
                            ) = ?", [$keyword]);
                        } else {
                            // If a non-numeric string is entered, return false data
                            $query->whereRaw('1 = 0');
                        }
                    }
                })     
                
                ->orderColumn('old_responsible_person', function($query, $order) {
                    $query->orderByRaw("(
                        SELECT COUNT(*) 
                        FROM lost_shipment_responsibles ls
                        WHERE ls.shipment_id = shipments.id 
                        AND ls.updated_at < (
                            SELECT MAX(updated_at)
                            FROM lost_shipment_responsibles
                            WHERE lost_shipment_responsibles.shipment_id = shipments.id
                        )
                    ) $order");
                })

                ->rawColumns(['tracking_number_link', 'responsible_person_shipment', 'action', 'old_responsible_person'])
                ->make(true);
    }
    public function shipment_confirm_status(Request $request){ //update to status 20 for confirm and 13 for re-attempt

        $shipment_ids = $request->shipment_ids;
            foreach ($shipment_ids as $shipment){
                
                $parcel = Shipment::find($shipment);
                $dispute_check = CheckDisputeShipmentsController::check($parcel->id);
                if(!$dispute_check){
                    return ['status' => 0, 'error' => 'Shipment is in Dispute! For further assistance, please contact QA (CX)'];
                }
                if($parcel->shipper_status_id == 18) {
                    
                    $lost_shipments_shippers = LostShipmentShipper::where('user_id',$parcel->user_id);
                    if($lost_shipments_shippers->exists()){

                        $lost_shipments_admins = LostShipmentAdmin::where('admin_id',Auth::id());
                        if($lost_shipments_admins->exists()){
//                    if (!$parcel->packaging_material_request) {
                        Shipment::where('id', $shipment)->update(['shipper_status_id' => 20, 'consignee_status_id' => 20]);
                        ShipmentChargesController::return ($shipment);

                        ShipmentsJourneyController::add($shipment, 20, 20, $request->reason, NULL, NULL, Auth::id());

                        AdminFinanceController::add_payment($shipment, 1);
//                    } else {
//
//                        Shipment::where('id', $shipment)->update(['shipper_status_id' => 17, 'consignee_status_id' => 17]);
//                        ShipmentsJourneyController::add($shipment, 17, 17, NULL, NULL, NULL, Auth::id());
//                    }
                        }

                    }else{
//                    if (!$parcel->packaging_material_request) {
                        Shipment::where('id', $shipment)->update(['shipper_status_id' => 20, 'consignee_status_id' => 20]);
                        ShipmentChargesController::return ($shipment);
                        ShipmentsJourneyController::add($shipment, 20, 20, $request->reason, NULL, NULL, Auth::id());

                        AdminFinanceController::add_payment($shipment, 1);
//                    } else {
//
//                        Shipment::where('id', $shipment)->update(['shipper_status_id' => 17, 'consignee_status_id' => 17]);
//                        ShipmentsJourneyController::add($shipment, 17, 17, NULL, NULL, NULL, Auth::id());
//                    }
                    }

                    $this->updateLostShipmentApproval($shipment, 'rejection_count', 1);


                }
            }
            return ['status'=>1,'success'=>"Shipment successfully updated as ( Return Confirm )"];
    }
    public function shipment_reattempt_status(Request $request){ //update to status 20 for confirm and 13 for re-attempt

        $shipment_ids = $request->shipment_ids;

        foreach ($shipment_ids as $shipment){
                $parcel = Shipment::find($shipment);

                $dispute_check = CheckDisputeShipmentsController::check($parcel->id);
                if(!$dispute_check){
                    return ['status' => 0, 'error' => 'Shipment is in Dispute! For further assistance, please contact QA (CX)'];
                }
                if($parcel->shipper_status_id == 18) {

                    $lost_shipments_shippers = LostShipmentShipper::where('user_id',$parcel->user_id);

                    if($lost_shipments_shippers->exists()){

                        $lost_shipments_admins = LostShipmentAdmin::where('admin_id',Auth::id());
                        if($lost_shipments_admins->exists()){
                            Shipment::where('id', $shipment)->update(['shipper_status_id' => 13, 'consignee_status_id' => 13]);
                            ShipmentsJourneyController::add($shipment, 13, 13, NULL, $request->remarks, NULL, Auth::id());
                            if($parcel->packaging_material_request == 1) {
                                $packaging_material_shipment = PackagingMaterialRequest::where('tracking_number', $parcel->tracking_number)->first();
                                if ($packaging_material_shipment != null) {
                                    $packaging_material_shipment->status_id = 3;
                                    $packaging_material_shipment->save();
        
                                    $packaging_request_history = new PackagingMaterialRequestHistory();
                                    $packaging_request_history->packaging_material_request_id = $packaging_material_shipment->id;
                                    $packaging_request_history->status = 3;
                                    $packaging_request_history->updated_by = \Illuminate\Support\Facades\Auth::id();
                                    $packaging_request_history->save();

                                }
                            }
                        }
                    }else{
                        Shipment::where('id', $shipment)->update(['shipper_status_id' => 13, 'consignee_status_id' => 13]);
                        ShipmentsJourneyController::add($shipment, 13, 13, NULL, $request->remarks, NULL, Auth::id());
                        if($parcel->packaging_material_request == 1) {
                            $packaging_material_shipment = PackagingMaterialRequest::where('tracking_number', $parcel->tracking_number)->first();
                            if ($packaging_material_shipment != null) {
                                $packaging_material_shipment->status_id = 3;
                                $packaging_material_shipment->save();
                                
                                $packaging_request_history = new PackagingMaterialRequestHistory();
                                $packaging_request_history->packaging_material_request_id = $packaging_material_shipment->id;
                                $packaging_request_history->status = 3;
                                $packaging_request_history->updated_by = \Illuminate\Support\Facades\Auth::id();
                                $packaging_request_history->save();
                            }
                        }
                    }
                    
                    $this->updateLostShipmentApproval($shipment, 'rejection_count', 1);
                    
                }
            }
            return ['status'=>1,'success'=>"Shipment successfully updated as ( Re-Attempt )"];


    }

    public function lost_add_index(){
        $employees = Employee::all();
        return view('admin.lost.add_shipments')->with('employees', $employees);
    }
    public function get_shipment_info(Request $request)
    {
            $shipment_status_for_bags = array(3,21,26,32,49);
            $status_array = array(1, 5, 14, 17, 21, 23, 25, 26, 28, 30, 31, 32, 34, 36, 37, 38, 50, 51, 56, 60, 61);
            $tracking_number = $request->tracking_number;
            if ($tracking_number != '') {
                $shipment = Shipment::where('tracking_number', $tracking_number)->whereNotIn('shipper_status_id', $status_array);
                if ($shipment->exists()) {
                    $data = array();
                    $shipment = $shipment->first();
                    $dispute_check = CheckDisputeShipmentsController::check($shipment->id);
                    if(!$dispute_check){
                        return ['status' => 0, 'error' => 'Shipment is in Dispute! For further assistance, please contact QA (CX)'];
                    }
                    $journey=  ShipmentsJourney::where('shipment_id',$shipment->id)->latest('id')->first();
                    if($journey)
                    {
                        $verification = $journey->verification;
                        if($verification == 0)
                        {
                            return response()->json(['status' => 0, 'error' => 'Shipment is already marked lost!']);
                        }

                    }
                    if($shipment->shipper_status_id == 5)
                    {
                        return response()->json(['status' => 0, 'error' => 'Shipment is Out for Delivery !']);
                    }

                    if($shipment->shipper_status_id != 18) {

                        $cargo_manifest_bag_shipments = CargoManifestBagShipments::where('shipment_id', $shipment->id);
                        if($cargo_manifest_bag_shipments->exists()){
                            $cargo_manifest_bag_shipments = $cargo_manifest_bag_shipments->latest()->first();
                            $bag = CargoManifestBag::find($cargo_manifest_bag_shipments->cargo_manifest_bag_id);
                            if ($bag && !in_array($bag->status_id, [7, 8])) {
                                if(ManifestBagLostShipment::where('bag_id',$bag->id)->where('shipment_id',$shipment->id)->exists()){
                                    return response()->json(['status' => 0, 'error' => 'Shipment already marked lost for the current bag']);
                                }
                            }
                        }

                        $data['id'] = $shipment->id;
                        $data['tracking_number'] = $shipment->tracking_number;
                        $data['shipper_name'] = $shipment->user->name.' (' . $shipment->pickup_address->poc . ')';
                        $data['origin'] = $shipment->consignee_city->name;
                        $data['destination'] = $shipment->pickup_address->city->name;
                        $data['hub'] = $shipment->pickup_address->city->hub_city->name;
                        $data['amount'] = number_format($shipment->amount);
                        $data['parcel_value'] = number_format($shipment->parcel_value);
                        $data['remarks'] = '<input class="form-control form-control-sm remarks" id="remarks[' . $shipment->id. ']" name="remarks[' . $shipment->id. ']"  placeholder="Enter Remarks">';
                        $data['mode'] = $shipment->shipping_mode->mode;
                        $data['service_type'] = $shipment->booking_type->booking_type;
                        $data['action_button'] = '<div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm accounts">
                        <button type="button" class="dropdown-item add_lost_responsible" data-id="' . $shipment->id . '" data-toggle="modal">
                        <div class="row no-gutters align-items-center">
                                    <div class="col-2"><i class="ft-minus-circle"></i></div>
                                    <div class="col-9 offset-1">Add Lost Responsible</div>
                                </div>
                            </button>
                        </div>
                    </div>
                    
                    ';

                        ShipmentScanningJourneyController::add($shipment->id ,11,1,Auth::id(),NULL,NULL,NULL,NULL, session('latitude'), session('longitude'), NULL);
                        return response()->json(['status' => 1, 'details' => $data]);
                    }
                    else
                        {
                            return response()->json(['status' => 0, 'error' => 'Shipment already added to lost shipments!']);

                        }
                } else {
                    return response()->json(['status' => 0, 'error' => 'Shipment can not be added to lost!']);
                }

            }
    }
    public function add_lost_shipments(Request $request){
        dd($request->all());
        $passing_status_array = array(1,14,17,18,25,31,38);
        $shipment_status_for_bags = array(3,21,26,32,49);
        $shipments = explode(',', $request->shipment_ids);
        $remarks = $request->remarks;
        $lost_shipments_array = array();
        $lostShipmentsTime = array();

        if(!empty($shipments)){
            foreach ($shipments as $shipment) {
                $shipment_details = Shipment::where('id', $shipment)->whereNotIn('shipper_status_id', $passing_status_array);
                if ($shipment_details->exists()) {
                    $shipment_details = $shipment_details->first();

                    $journey=  ShipmentsJourney::where('shipment_id',$shipment_details->id)->latest('id')->first();
                    if($journey)
                    {
                        $verification = $journey->verification;
                        if($verification == 0)
                        {
                            return response()->json(['status' => 0, 'error' => 'Shipment is unverified!']);
                        }
                    }

                    if(in_array($shipment_details->shipper_status_id,$shipment_status_for_bags)){
                        //shipment receive huyi phr lost laga (only for manifest)
                        $cargo_manifest_bag_shipments = CargoManifestBagShipments::where('shipment_id', $shipment_details->id);
                        if($cargo_manifest_bag_shipments->exists()){
                            $cargo_manifest_bag_shipments = $cargo_manifest_bag_shipments->latest()->first();
                            $bag = CargoManifestBag::find($cargo_manifest_bag_shipments->cargo_manifest_bag_id);
                            if($bag->status_id != 7){
                                ManifestBagLostShipment::create(['bag_id' => $bag->id,'shipment_id' => $shipment_details->id]);
                                $bag->lost_shipments++;
                                $bag->save();

                                $bag_total_shipments = $bag->shipments;
                                $total_shipments = $bag->lost_shipments +  $bag->received_shipments;

                                if($bag_total_shipments == $total_shipments){
                                    foreach($bag->shipment as $shipment){
                                        $bag_shipment = CargoManifestBagShipments::where('shipment_id',$shipment->shipment_id)->where('status',0)->first();
                                        if($bag_shipment){
                                            $bag_shipment->status = 1;
                                            $bag_shipment->save();
                                        }
                                    }
                                    $bag->status_id = 7;
                                    $bag->completed = 1;
                                    $bag->short_received_shipments = 0;
                                    $bag->received_shipments = $total_shipments;
                                    $bag->receiver_id = 346; //global_admin
                                    $bag->save();

                                    $manifest = ManifestBag::where('cargo_manifest_bag_id',$bag->id)->latest()->first();
                                    if($manifest){
                                        $manifest->status = 1;
                                        $manifest->save();

                                        $cargo_manifest = CargoManifest::find($manifest->cargo_manifest_id);

                                        $total_manifest_bags = $cargo_manifest->bags;
                                        $total_received_manifest_bags = ManifestBag::where('cargo_manifest_id',$manifest->cargo_manifest_id)->where('status',1)->count();

                                        CargoManifestBagJourneyController::add($bag->id, $bag->seal_number, $bag->status_id, 346, $manifest->cargo_manifest_id);

                                        if($total_manifest_bags == $total_received_manifest_bags){
                                            $cargo_manifest->status_id = 2;
                                            $cargo_manifest->received_by = 346;
                                            $cargo_manifest->received_bags = $total_received_manifest_bags;
                                            $cargo_manifest->save();
                                        }
                                    }
                                }
                                else{
                                    //pehle lost laga phr baaqi ki shipment receive huyi (only for manifest)
                                    $bag_shipment = CargoManifestBagShipments::where('shipment_id',$shipment_details->id)->where('status',0)->first();
                                    if($bag_shipment){
                                        $bag_shipment->status = 1;
                                        $bag_shipment->save();
                                    }
                                }
                            }
                        }
                    }


                    $shipment_details->shipper_status_id = 18;
                    $shipment_status_reason_for_shipment_lost_id = DB::table('shipment_status_reason')->where('name', '=','Shipment Lost - Requested')->first()->id;

                    $shipment_details->save();
                    ShipmentsJourneyController::add($shipment_details->id, 18, NULL, $shipment_status_reason_for_shipment_lost_id, $remarks[$shipment_details->id],NULL,Auth::id(), NULL, NULL, 0);

                    $lostShipmentTime = ShipmentsJourney::where([
                        'shipment_id' => $shipment_details->id,
                        'shipper_status_id' => 18,
                    ])->latest()->first();

                    $lostShipmentsTime[$shipment_details->id] = !empty($lostShipmentTime->created_at) ? $lostShipmentTime->created_at : now();

                    $lost_shipments_array[] = $shipment;

                    //Pending Count For Lost Pending
                    $this->updateLostShipmentApproval($shipment_details->id, 'lost_count', 0);

                }
            }

            if(count($lost_shipments_array) > 0){
                NotificationsController::send(150, $lost_shipments_array);
            }


            $traxIdArray = json_decode($request->trax_id, true);
            $LostShipmentResponsible = [];
            
            if(isset($traxIdArray)){
                foreach ($traxIdArray as $key => $values) {
                    $LostShipmentResponsible[$key] = implode(', ', array_column($values, 'value'));
                }  

                if(count($LostShipmentResponsible) > 0){
                    $this->LostShipmentResponsible($LostShipmentResponsible, $lostShipmentsTime);
                }
            }

            return redirect()->back()->with(['success' => 'Shipment(s) has been added to Lost!']);

        }
        else{
            return redirect()->back()->with(['error' => 'Shipment not selected!']);
        }
    }
    
    // Heading: N/A
    // Siderbar:  N/A
    // URL: admin/delivery/lost/add/bulk/lost/shipments
    // Description: This function is used to upload excel file for bulk lost shipments.
    public function bulk_lost_shipments(Request $request)
    {
        // $employee = Employee::where('trax_id' , $request->excel_employee_value)->first();
        $status_array = array(1, 5, 11, 14, 17, 21, 23, 25, 26, 28, 30, 31, 32, 34, 36, 37, 38, 49, 50, 51, 56, 60, 61);
        $names = [
            'tracking_number' => 'Tracking Number',
        ];

        $messages = [
            'required' => ':attribute is Required.',
            'integer' => ':attribute must be an Integer.',
        ];
        $rules = [
            'tracking_number' => ['required', 'max:300',
            'integer', Rule::exists('shipments', 'tracking_number')->where(function ($query)use($status_array) {
                $query->whereNotIn('shipper_status_id', $status_array);
            })],
        ];
        $fields = [0 => 'tracking_number'];
        if ($file = $request->file('excel')) {
            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();
            $trackingNumbers = array();

            $header = ['Tracking Number'];

            if (isset($spreadsheet)) {
                $header_correct = TRUE;
                foreach ($spreadsheet[0] as $index => $header_value) {

                    if ($index == 1) {
                    } elseif (!isset($header[$index]) || $header_value != $header[$index]) {
                        $header_correct = FALSE;

                        break;
                    }
                }


                if (!$header_correct) {
                    
                    return response()->json(['status' => 2, 'error' => 'Invalid Columns, Kindly follow the Template provided']);
                } else {
                    unset($spreadsheet[0]);
                }
            }

            if (!empty($spreadsheet) || !isset($spreadsheet)) {
                $rows = array();
                
                foreach ($spreadsheet as $spreadsheet_row) {
                    $row = array();
                    
                    foreach ($spreadsheet_row as $key => $value) {
                        if($value != null)
                        {
                            if (!in_array($value, $trackingNumbers)) {
                                $trackingNumbers[] = $value;
                                $row[$fields[$key]] = $value;
                            }
                        }
                       
                    }

                    $rows[] = $row;
                }
                $rows = array_filter($rows);
                
                unset($spreadsheet);
                $errors = array();
                    
                foreach ($rows as $key => $row) {
                    $row_id = $key + 2;
                    $validate = Validator::make($row, $rules, $messages);
                    
                    $validate->setAttributeNames($names);

                    if ($validate->fails()) {
                        
                        $errors['Row #' . $row_id] = $validate->errors()->all();
                    }
                }
                $tracking_numbers = array();
                $data = array();
                $error = array();

                foreach ($rows as $key => $row) {
                    $row_id = $key + 2;
                    $tracking = trim($row['tracking_number']);
                    $shipment = Shipment::where('tracking_number', $tracking)
                    ->whereNotIn('shipper_status_id', $status_array)->first();
                    if ($shipment) {
                        $dispute_check = CheckDisputeShipmentsController::check($shipment->id);
                        if(!$dispute_check){
                            $error[$row_id]['tracking_number'] = $tracking;
                            $error[$row_id]['error_msg'] = "Shipment is in Dispute! For further assistance, please contact QA (CX)";

                            continue;
                        }
                        $journey=  ShipmentsJourney::where('shipment_id',$shipment->id)->latest('id')->first();
                        if($journey){
                            $verification = $journey->verification;
                            if($verification == 0){
                                $error[$row_id]['tracking_number'] = $tracking;
                                $error[$row_id]['error_msg'] = "Shipment is unverified!";

                                continue;
                            }
                        }
                        if($shipment->shipper_status_id == 5){
                            $error[$row_id]['tracking_number'] = $tracking;
                            $error[$row_id]['error_msg'] = "Shipment is Out for Delivery!";

                            continue;
                        }
                        if($shipment->shipper_status_id != 18) {
                            $cargo_manifest_bag_shipments = CargoManifestBagShipments::where('shipment_id', $shipment->id);
                            if($cargo_manifest_bag_shipments->exists()){
                                $cargo_manifest_bag_shipments = $cargo_manifest_bag_shipments->latest()->first();
                                $bag = CargoManifestBag::find($cargo_manifest_bag_shipments->cargo_manifest_bag_id);
                                if ($bag && !in_array($bag->status_id, [7, 8])) {
                                    if(ManifestBagLostShipment::where('bag_id',$bag->id)->where('shipment_id',$shipment->id)->exists()){
                                        $error[$row_id]['tracking_number'] = $tracking;
                                        $error[$row_id]['error_msg'] = "Shipment already marked lost for the current bag!";

                                        continue;
                                    }
                                }
                            }
                        }
                        else{
                            $error[$row_id]['tracking_number'] = $tracking;
                            $error[$row_id]['error_msg'] = "Shipment already added to lost shipments!";

                            continue;
                        }
                        $data[$shipment->id]['id'] = $shipment->id;
                        $data[$shipment->id]['tracking_number'] = $shipment->tracking_number;
                        $data[$shipment->id]['shipper_name'] = $shipment->user->name.' (' . $shipment->pickup_address->poc . ')';
                        $data[$shipment->id]['origin'] = $shipment->consignee_city->name;
                        $data[$shipment->id]['destination'] = $shipment->pickup_address->city->name;
                        $data[$shipment->id]['hub'] = $shipment->pickup_address->city->hub_city->name;
                        $data[$shipment->id]['amount'] = number_format($shipment->amount);
                        $data[$shipment->id]['parcel_value'] = number_format($shipment->parcel_value);
                        $data[$shipment->id]['mode'] = $shipment->shipping_mode->mode;
                        $data[$shipment->id]['remarks'] = '
                            <div class="remarks-cell" data-shipment-id="'.$shipment->id.'">
                                <input class="form-control form-control-sm remarks" 
                                    data-shipment-id="'.$shipment->id.'" 
                                    name="remarks['.$shipment->id.']" 
                                    placeholder="Enter Remarks">
                            </div>';

                        $data[$shipment->id]['service_type'] = $shipment->booking_type->booking_type;
                        $data[$shipment->id]['service_type'] = $shipment->booking_type->booking_type;
                        $data[$shipment->id]['action_button'] = '<div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm accounts">
                        <button type="button" class="dropdown-item add_lost_responsible" data-id="' . $shipment->id . '" data-toggle="modal">
                        <div class="row no-gutters align-items-center">
                                    <div class="col-2"><i class="ft-minus-circle"></i></div>
                                    <div class="col-9 offset-1">Add Lost Responsible</div>
                                </div>
                            </button>
                        </div>
                    </div>
                    
                    ';

                        $tracking_numbers['Row #' . $row_id] = $tracking;
                        ShipmentScanningJourneyController::add($shipment->id ,11,1,Auth::id(),NULL,NULL,NULL,NULL, session('latitude'), session('longitude'), NULL);

                    }
                    else
                    {
                        $error[$row_id]['tracking_number'] = $tracking;
                        $error[$row_id]['error_msg'] = "These Tracking Numbers are Invalid";

                        continue;
                    }
                }
                // If no shipment is added to lost shipments, return only the array of lost shipments' tracking numbers
                return response()->json(['status' => 1, 'details' => $data, 'error_count' => count($error), 'error' => $error]);
            } else {
                return response()->json(['status' => 3, 'error' => 'No Shipments in File']);
            }
        }
        else {
            return response()->json(['status' => 3, 'error' => 'File Not Found!']);
        }
    }

    public function shipment_approve_status(Request $request){
        // stop approving lost shipments after first approval
        $lost_shipments = Shipment::whereIn('shipments.id', $request->shipment_ids)
            ->join('lost_shipment_status_counts', 'shipments.id', '=', 'lost_shipment_status_counts.shipment_id')
            ->where('shipments.shipper_status_id', 18)
            ->select([
                'shipments.id as shipment_id',
                'lost_shipment_status_counts.cleared as cleared'
            ])
            ->get();
            
        if ($lost_shipments->isNotEmpty() && $lost_shipments->contains('cleared', 1)) {
            $clearedShipments = $lost_shipments->filter(function ($shipment) {
                return $shipment->cleared == 1;
            })->pluck('shipment_id');

            return response()->json([
                'status' => 2, 
                'error' => 'The following shipment IDs are already marked approved: ' . $clearedShipments->join(', ')
            ]);
        }

        foreach ($request->shipment_ids as $shipment_id) {
            $cargo_manifest_bag_shipments = CargoManifestBagShipments::where('shipment_id', $shipment_id);
            if($cargo_manifest_bag_shipments->exists()){
                $cargo_manifest_bag_shipments = $cargo_manifest_bag_shipments->latest()->first();
                $bag = CargoManifestBag::find($cargo_manifest_bag_shipments->cargo_manifest_bag_id);
                if($bag->status_id != 7){
                    $bag->status_id = 8;
                    $bag->save();
                }
            }

            $latest_shipment_journey = ShipmentsJourney::where('shipment_id', $shipment_id)->where('status_reason_id', 90)->latest()->first();

            ShipmentsJourneyController::add($shipment_id, 18, NULL, NULL, $latest_shipment_journey?->remarks, NULL, Auth::id(), NULL, NULL, $request->approve);
            $this->updateLostShipmentApproval($shipment_id, 'approval_count', 1);
        }
        return response()->json(['status' => 1, 'success' => 'Shipment Has Been Approved To Lost !!']);

    }
    public static function LostShipmentResponsible($LostShipmentResponsible, $lostShipmentsTime) {
        foreach ($LostShipmentResponsible as $shipment_id => $value) {
            $shipment_responsibles = explode(',', ltrim($value));
            $shipment_responsibles = array_map('trim', $shipment_responsibles);
            foreach ($shipment_responsibles as $traxId) {
                $rider = Rider::where('trax_id', $traxId)->first();
                $admin = Admin::where('trax_id', $traxId)->first();
    
                if ($admin != null) {
                    $id = $admin->id;
                    $user_type = 1;
                } 
                
                if ($rider != null) {
                    $id = $rider->id;
                    $user_type = 2;
                } 
    
                $LostShipmentResponsible = new LostShipmentResponsible;
                $LostShipmentResponsible->shipment_id = $shipment_id;
                $LostShipmentResponsible->user_id = $id;
                $LostShipmentResponsible->user_type = $user_type;
                $LostShipmentResponsible->created_at = isset($lostShipmentsTime[$shipment_id]) ? $lostShipmentsTime[$shipment_id] : now();
                $LostShipmentResponsible->updated_at = isset($lostShipmentsTime[$shipment_id]) ? $lostShipmentsTime[$shipment_id] : now();
                $LostShipmentResponsible->save();
            }
        }
    }

    public function lost_responsible_list(Request $request){
        $shipment_id = $request->shipment_id;
        $details = [];
        $latest_lost_responsible_shipments = LostShipmentResponsible::whereIn('id', function($query) use ($shipment_id, $request) {
            $query->selectRaw('MAX(id)')
                    ->from('lost_shipment_responsibles')
                    ->where('shipment_id', $shipment_id) ;
        
            if(isset($request->updated_at)){
                $query->whereBetween('updated_at', [date('Y-m-d H:i:s', strtotime($request->updated_at)), date('Y-m-d H:i:s', strtotime($request->updated_at) + 10)]);
            }
        
            $query->groupBy('user_id');
        })->get();
        
        foreach($latest_lost_responsible_shipments as $key => $lost_responsible_shipment){
            if($lost_responsible_shipment->user_type == 1){
                $admin = Admin::find($lost_responsible_shipment->user_id);
                $details[$key]['trax_id'] = $admin->trax_id;
                $details[$key]['name'] = $admin->name;
                $details[$key]['type'] = $admin->employee->employee_type->name;
                $details[$key]['status'] = $admin->employee->employee_status->name;
                $details[$key]['marked_at'] = Carbon::parse($lost_responsible_shipment->updated_at)->format('Y-m-d H:i:s');


            }else{
                $rider = Rider::find($lost_responsible_shipment->user_id);
                $details[$key]['trax_id'] = $rider->trax_id;
                $details[$key]['name'] = $rider->name;
                $details[$key]['type'] = $rider->employee->employee_type->name;
                $details[$key]['status'] = $rider->employee->employee_status->name;
                $details[$key]['marked_at'] = Carbon::parse($lost_responsible_shipment->updated_at)->format('Y-m-d H:i:s');
            }
        }

        return response()->json(['status' => 1, 'details'=> $details]);

    }
    public function lost_data(Request $request)
    {
        $details = [
            // 'total' => 0,
            'total_of_approved_shipments' => 0,
            'total_of_pending_shipments' => 0,
        ];

        $responsible_hubs = UserLostShipmentHub::where('admin_id', session('id'))->get()->pluck('hub_id')->toArray();
        $admin_responsible_hubs = implode(',', $responsible_hubs);


        $shipments = Shipment::join('shipments_journey', function ($join) use ($responsible_hubs, $admin_responsible_hubs) {
            if (session('role_id') != 1 && count($responsible_hubs) > 0) {
                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                ->where('shipments_journey.id', '=', DB::raw("(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id AND shipments_journey.city_id IN ($admin_responsible_hubs))"));
            } else {
                $join->where('shipments_journey.id', '=', DB::raw("(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)"));
            }
        })
        ->where('shipments.shipper_status_id', 18)
        ->whereBetween('shipments_journey.updated_at', [$request->from_date, $request->to_date]);
    
            // COUNT(*) as total,
        $shipmentsCounts = $shipments
            ->selectRaw('
                SUM(IF(shipments_journey.verification = 1, 1, 0)) as approved,
                SUM(IF(shipments_journey.verification = 0, 1, 0)) as pending
            ')
            ->first();


        $total_rejections = LostShipmentStatusCount::whereBetween('updated_at', [$request->from_date, $request->to_date])->sum('rejection_count');
        $total_approval = LostShipmentStatusCount::whereBetween('updated_at', [$request->from_date, $request->to_date])->sum('approval_count');
          
        // $details['total'] = $shipmentsCounts->total;
        $details['total_of_approved_shipments'] = $total_approval;
        $details['total_of_pending_shipments'] = $shipmentsCounts->pending;
        $details['total_rejections'] = $total_rejections;


        return response()->json(['status' => 1, 'details' => $details]);
    }

    // For lost shipment screen new responsible
    public function lost_shipment_responsible_list(Request $request){
        $shipment_id = $request->shipment_id;
        $details = [];
        $latest_lost_responsible_shipments = LostShipmentResponsible::where('shipment_id', $shipment_id)
        ->where('updated_at', function($query) use ($shipment_id) {
            $query->selectRaw('MAX(updated_at)')
                ->from('lost_shipment_responsibles')
                ->where('shipment_id', $shipment_id);
        })
        ->orderBy('updated_at', 'desc')
        ->get();


        
        foreach($latest_lost_responsible_shipments as $key => $lost_responsible_shipment){
            if($lost_responsible_shipment->user_type == 1){
                $admin = Admin::find($lost_responsible_shipment->user_id);
                $details[$key]['trax_id'] = $admin->trax_id;
                $details[$key]['name'] = $admin->name;
                $details[$key]['type'] = $admin->employee->employee_type->name;
                $details[$key]['status'] = $admin->employee->employee_status->name;
                $details[$key]['marked_at'] = Carbon::parse($lost_responsible_shipment->updated_at)->format('Y-m-d H:i:s');


            }else{
                $rider = Rider::find($lost_responsible_shipment->user_id);
                $details[$key]['trax_id'] = $rider->trax_id;
                $details[$key]['name'] = $rider->name;
                $details[$key]['type'] = $rider->employee->employee_type->name;
                $details[$key]['status'] = $rider->employee->employee_status->name;
                $details[$key]['marked_at'] = Carbon::parse($lost_responsible_shipment->updated_at)->format('Y-m-d H:i:s');
            }
        }

        return response()->json(['status' => 1, 'details'=> $details]);

    }

    // For lost shipment screen old responsible
    public function old_lost_shipment_responsible_list(Request $request){
        $shipment_id = $request->shipment_id;
        $details = [];
        $old_lost_responsible_shipments = LostShipmentResponsible::where('shipment_id', $shipment_id)
        ->where('updated_at', '<', function($query) use ($shipment_id) {
            $query->selectRaw('MAX(updated_at)')
                ->from('lost_shipment_responsibles')
                ->where('shipment_id', $shipment_id);
        })
        ->orderBy('updated_at', 'desc')
        ->get();

        foreach($old_lost_responsible_shipments as $key => $lost_responsible_shipment){
            if($lost_responsible_shipment->user_type == 1){
                $admin = Admin::find($lost_responsible_shipment->user_id);
                $details[$key]['trax_id'] = $admin->trax_id;
                $details[$key]['name'] = $admin->name;
                $details[$key]['type'] = $admin->employee->employee_type->name;
                $details[$key]['status'] = $admin->employee->employee_status->name;
                $details[$key]['marked_at'] = Carbon::parse($lost_responsible_shipment->updated_at)->format('Y-m-d H:i:s');


            }else{
                $rider = Rider::find($lost_responsible_shipment->user_id);
                $details[$key]['trax_id'] = $rider->trax_id;
                $details[$key]['name'] = $rider->name;
                $details[$key]['type'] = $rider->employee->employee_type->name;
                $details[$key]['status'] = $rider->employee->employee_status->name;
                $details[$key]['marked_at'] = Carbon::parse($lost_responsible_shipment->updated_at)->format('Y-m-d H:i:s');
            }
        }

        return response()->json(['status' => 1, 'details'=> $details]);

    }

}
