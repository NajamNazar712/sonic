<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminRole;
use App\Http\Models\Admin\AdminDepartment;
use App\Http\Models\Admin\CargoManifest\CargoManifestBagShipments;
use App\Http\Models\Admin\Lead\Lead;
use App\Http\Models\CRM\CrmRequestTaggingTypes;
use App\Http\Models\CRM\CrmRequestChannel;
use App\Http\Models\CRM\CrmRequestCaseNatureType;
use App\Http\Models\CRM\CrmRequestCaseNature;
use App\Http\Models\CRM\CrmClosedReasonStatus;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\CRM\CrmRequestStatusHistory;
use App\Http\Models\CRM\CrmSettings;
use App\Http\Models\CRM\CrmTatHolidays;
use App\Http\Models\CRM\CrmRequestTagging;
use App\Http\Models\CRM\CrmRequestStatus;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\City;
use App\Http\Models\Zone;
use App\Http\Models\ShipmentsJourney;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Connection;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Admins\ActivityTrailController;



class CRMDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function crm_dashboard_index(){
        // ActivityTrailController::createActivityTrailLog(Auth::id(),312);
        $case_natures = CrmRequestCaseNature::select('id', 'name')->get();
        $case_nature_types = CrmRequestCaseNatureType::select('id', 'type')->get();
        $crm_request_statuses = CrmRequestStatus::select('id', 'name')->get();
        $shipment_status = ShipmentStatus::select('id', 'name')->get();
        $channels = CrmRequestChannel::select('id', 'channel')->get();
        $agents = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id')
            ->where('admin_roles.department_id',3)->get();
        $admins = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id' )
            ->select('a.id as id', 'a.name as name')
            ->where('a.status', 1)
            ->whereNotIn('admin_roles.department_id', [1,3])->get();
        $types = CrmRequestTaggingTypes::get();
        $departments = AdminDepartment::whereNotIn('id', [1,3])->get();
        $hubs = City::where('hub', 1)->get();
        $zones = Zone::where('status', 1)->get();
        $closed_reason_statuses  = CrmClosedReasonStatus::all();
        $shippers = DB::connection('reports')->table('users')->whereIn('status', [3, 4])->select('id', 'name')->get();
        // $case_natures = DB::connection('reports')->table('crm_request_case_nature')->select('id', 'name')->get();
        // $case_nature_types = DB::connection('reports')->table('crm_request_case_nature_types')->select('id', 'type')->get();
        $statuses = DB::connection('reports')->table('crm_request_statuses')->select('id', 'name')->whereNotIn('id', [6, 7])->get();
        $shipping_modes = DB::connection('reports')->table('shipping_modes')->get(['id', 'mode']);
        $shipment_status = ShipmentStatus::select('id','name')->get();
        


        //From Admin Leads 
        // $today = Carbon::now()->endOfDay();
        // $thirtyDays = Carbon::now()->subDays(58)->startOfDay();

        // $leads['total'] = Lead::whereBetween('requested_date', [$thirtyDays, $today]);
        // $leads['received'] = Lead::whereBetween('requested_date', [$thirtyDays, $today])->where('status_id', 1);
        // $leads['in_process'] = Lead::whereIn('status_id', [2, 5, 6, 7, 8])->whereBetween('requested_date', [$thirtyDays, $today]);
        // $leads['in_process_for_activation'] = Lead::where('status_id', 9)->whereBetween('requested_date', [$thirtyDays, $today]);
        // $leads['dead_leads'] = Lead::whereIn('status_id', [3, 4, 10, 11, 13])->whereBetween('requested_date', [$thirtyDays, $today]);
        // $leads['accounts_activated'] = Lead::where('status_id', 12)->whereBetween('requested_date', [$thirtyDays, $today]);
        // $leads['dormant'] = Lead::where('status_id', 14)->whereBetween('requested_date',[$thirtyDays,$today]);
        
        // if (session('role_id') != 1) {
        //     $leads['total'] = $leads['total']->join('cities as c', 'c.id', '=', 'leads.city_id')->whereIn('c.hub_id', session('hubs'));
        //     $leads['received'] = $leads['received']->join('cities as c', 'c.id', '=', 'leads.city_id')->whereIn('c.hub_id', session('hubs'));
        //     $leads['in_process'] = $leads['in_process']->join('cities as c', 'c.id', '=', 'leads.city_id')->whereIn('c.hub_id', session('hubs'));
        //     $leads['in_process_for_activation'] = $leads['in_process_for_activation']->join('cities as c', 'c.id', '=', 'leads.city_id')->whereIn('c.hub_id', session('hubs'));
        //     $leads['dead_leads'] = $leads['dead_leads']->join('cities as c', 'c.id', '=', 'leads.city_id')->whereIn('c.hub_id', session('hubs'));
        //     $leads['accounts_activated'] = $leads['accounts_activated']->join('cities as c', 'c.id', '=', 'leads.city_id')->whereIn('c.hub_id', session('hubs'));
        //     $leads['dormant'] = $leads['dormant']->join('cities as c', 'c.id', '=', 'leads.city_id')->whereIn('c.hub_id', session('hubs'));
        // }
        $leads['total'] = "3";
        $leads['received'] = "3";
        $leads['received_percentage'] = "3";
        $leads['in_process'] = "3";
        $leads['in_process_percentage'] = "3";
        $leads['in_process_for_activation'] = "3";
        // $leads['in_process_percentage'] = "3";
        $leads['in_process_for_activation_percentage'] = "3";
        $leads['dormant'] = "3";

        $cities = City::where('status', 1)->select('id', 'name')->get();

        $salesperson = Admin::join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.name', 'admins.id'])->where('status', 1)->where('ar.department_id', 7)->get();


        $dates['current'] = Carbon::now();
        $dates['old_date'] = Carbon::now()->subDays(58);

        return view('admin.crm.dashboard')->with(['shippers' => $shippers, 'case_natures' => $case_natures, 'case_nature_types' => $case_nature_types,'statuses' => $statuses, 'shipping_modes' => $shipping_modes, 'channels' => $channels, 'agents' => $agents, 'shipment_status' => $shipment_status, 'types' => $types, 'admins' => $admins, 'departments' => $departments, 'hubs' => $hubs, 'zones' => $zones, 'closed_reason_statuses' => $closed_reason_statuses,'leads' => $leads,'dates' => $dates,'cities' => $cities, 'sale_name' => $salesperson ,'crm_request_statuses' => $crm_request_statuses]);
    }

    public function crm_dashboard_list(Request $request){
       
        if($request->get('excel') && $request->get('excel') == true)
        {
            // ActivityTrailController::createActivityTrailLog(Auth::id(),313);
        }
        $in_process_request = CrmRequest::leftjoin('crm_request_case_nature as crcn', 'crcn.id', '=', 'crm_requests.case_nature_id')
            ->leftjoin('crm_request_case_nature_types as crcnt', 'crcnt.id', '=', 'crm_requests.case_nature_type_id')
            ->leftjoin('crm_request_channels as crc', 'crc.id', '=', 'crm_requests.channel_id')
            ->leftjoin('admins as ad', 'ad.id', '=', 'crm_requests.agent_id')
            ->leftjoin('admins as a', function ($join) {
                $join->on('a.id', '=', 'crm_requests.launched_by_id')
                    ->where('crm_requests.launched_by', '=', DB::raw(0));
            })
            ->leftjoin('users as u', function ($join) {
                $join->on('u.id', '=', 'crm_requests.launched_by_id')
                    ->where('crm_requests.launched_by', '=', DB::raw(1));
            })
            ->leftjoin('substitute_users as su', function ($join) {
                $join->on('su.id', '=', 'crm_requests.launched_by_id')
                    ->where('crm_requests.launched_by', '=', DB::raw(2));
            })
            ->leftjoin('retail_users as ru', function ($join) {
                $join->on('ru.id', '=', 'crm_requests.launched_by_id')
                    ->where('crm_requests.launched_by', '=', DB::raw(3));
            })
            ->leftjoin('consignee_users as cu', function ($join) {
                $join->on('cu.id', '=', 'crm_requests.launched_by_id')
                    ->where('crm_requests.launched_by', '=', DB::raw(4));
            })
            ->leftjoin('shipments as s', 's.id', '=', 'crm_requests.shipment_id')
            ->leftjoin('shipments_journey as sj', function ($join){
                $join->on('sj.shipment_id', '=', 's.id')
                ->where('sj.shipper_status_id',2);
            })
            ->leftjoin('shipment_status as ss', 'ss.id', '=', 's.shipper_status_id')
            ->leftjoin('users as user', 'user.id', '=', 'crm_requests.shipper_id')
            ->leftjoin('user_shipping_infos AS usi', 's.pickup_address_id', '=', 'usi.id')
            ->leftjoin('cities as oc', 'oc.id', '=', 'usi.city_id')
            ->leftjoin('cities as och', 'och.id', '=', 'oc.hub_id')
            ->leftjoin('zones as ocz', 'ocz.id', '=', 'oc.zone_id')
            ->leftjoin('cities as dc', 'dc.id', '=', 's.consignee_city_id')
            ->leftjoin('cities as dh', 'dh.id', '=', 'dc.hub_id')
            ->leftjoin('zones as z', 'z.id', '=', 'dc.zone_id')
            ->leftjoin('crm_request_taggings as crt', 'crt.crm_request_id', '=', 'crm_requests.id')
            ->leftjoin('crm_request_tagging_histories as crth', function ($join) {
                $join->on('crth.crm_request_id', '=', 'crm_requests.id')
                    ->where('crth.id','=',
                        DB::raw('(select max(id) from crm_request_tagging_histories where crm_request_tagging_histories.crm_request_id = crm_requests.id)'));
            })
            ->leftjoin('admin_departments as adp', 'adp.id', '=', 'crt.tagged_id')
            ->leftjoin('admins as at', 'at.id', '=', 'crt.tagged_id')
            ->leftjoin('sale_person_tags as spt', function($join) {
                $join->on('spt.user_id', '=', 's.user_id')
                    ->where('spt.status', '=', 0);
            })
            ->leftjoin('crm_request_status_histories as res', function ($join) {
                $join->on('res.crm_request_id', '=', 'crm_requests.id')
                    ->where('res.id','=',
                        DB::raw('(select max(id) from crm_request_status_histories where crm_request_status_histories.crm_request_id = crm_requests.id and crm_request_status_histories.status_id = 2)'));
            })
            ->leftjoin('crm_request_agent_histories as resa', function ($join) {
                $join->on('resa.crm_request_id', '=', 'crm_requests.id')
                    ->where('resa.id','=',
                        DB::raw('(select max(id) from crm_request_agent_histories where crm_request_agent_histories.crm_request_id = crm_requests.id and crm_request_agent_histories.agent_id = crm_requests.agent_id)'));
            })
            ->leftjoin('admins as resby', 'resby.id', '=', 'resa.assigned_by')
            ->leftjoin('crm_comments as ccs', function($join){
                $join->on('ccs.crm_request_id', '=', 'crm_requests.id')
                    ->where('ccs.id', '=', DB::raw('(select max(id) from crm_comments where crm_comments.crm_request_id = crm_requests.id)'));
            })
            ->leftjoin('admins as accs', 'accs.id', '=', 'ccs.comment_by_id')
            ->leftjoin('users as uccs', 'uccs.id', '=', 'ccs.comment_by_id')
            ->leftjoin('crm_request_escalation_taggings as cret', 'cret.crm_request_id', '=', 'crm_requests.id')
            ->leftjoin('crm_request_status_histories as crsh', function($join){
                $join->on('crsh.crm_request_id', '=', 'crm_requests.id')
                    ->where('crsh.created_at', '=', DB::raw('(select max(created_at) from crm_request_status_histories where crm_request_status_histories.crm_request_id = crm_requests.id and crm_request_status_histories.status_id = 5)'));
            })
            ->leftjoin('star_shippers as sts','sts.user_id','=','u.id')
            ->join('shipping_modes as sm','sm.id','=','s.shipping_mode_id')
            ->leftjoin('admins as ad1','spt.admin_id','=','ad1.id')
            
            ->leftjoin('users as us','us.id','=','s.user_id')
            ->leftjoin('segments as seg','us.segment_id','seg.id')
            ->leftjoin('sale_tier_tags as stt','stt.user_id', '=','s.user_id')
            ->leftjoin('admins as ad2','ad2.id','=','stt.kam')
            // ->leftJoin('shipments_journey as sj1', function ($join) {
            //     $join->on('sj1.shipment_id', '=', 'crm_requests.shipment_id')
            //         ->where(
            //             'sj1.id',
            //             '=',
            //             DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = crm_requests.shipment_id and shipments_journey.shipper_status_id = 2)')
            //         );
            // })
            ->select('sj.created_at as arrival','crm_requests.id as id', 's.tracking_number as tracking_number', 'crcn.name as case_nature', 'crcnt.type as case_nature_type', 'crc.channel as channel', 'ad.name as agent', 'a.name as name', 'u.name as shipper', 'su.name as sub_shipper', 'cu.name as consignee_users', 'ru.name as retail_users', 'crm_requests.launched_by as launched_added_by', 'crm_requests.created_at as created_at', 'crm_requests.description as description','crm_requests.description as descr','at.name as tagged_admin', 'adp.name as tagged_department', 'crt.crm_request_tagging_type_id as crm_request_tagging_type_id', 'ss.name as status','ss.id as shipment_status_id', 'user.name as shipper_name', 'oc.name as origin','och.name as origin_hub','ocz.name as origin_zone', 'dc.name as destination', 'dh.name as hub', 'crt.crm_request_tagging_type_id as tagged_type', 'res.created_at as valid_date', 'ccs.comment as last_comment', 'ccs.created_at as last_comment_date', 'ccs.comment_by as last_comment_by', 'accs.name as last_comment_admin', 'uccs.name as last_comment_shipper', 'crm_requests.launched_by_id', 'res.created_at as agent_assigned_date', 'resby.name as agent_assigned_by', 'crth.created_at as tagged_date', 'z.name as zone','crsh.created_at as reopen_date','crm_requests.address as address', 'crm_requests.address_latitude as address_latitude','crm_requests.address_longitude as address_longitude','at.id as tagged_admin_id', 'crm_requests.case_nature_id','crm_requests.shipment_id','sts.status as star_status','crm_requests.updated_at as last_status_date','sm.mode as shipping_mode','ad1.name as sale_person','ad2.name as kae','seg.name as segment','sj.updated_at as arrival_date','s.updated_at as last_status_today')
            ->where('crm_requests.status_id', 2)
            ->groupBy('crm_requests.id');
            // ->get();
            // dd($in_process_request);
            $current_date = Carbon::now();
        if ((!in_array(session('role_id'), [1, 4, 6])) && (!in_array(179, session('permissions')) && !in_array(201, session('permissions')))) {
            $in_process_request = $in_process_request->where(function ($query) {
                $query->where(function ($sub_query) {
                    $sub_query->where('crm_requests.agent_id', Auth::id())
                        ->orWhere(function ($sub_query) {
                            $sub_query->where('crm_requests.launched_by', 0)
                                ->where('crm_requests.launched_by_id', Auth::id());
                        });
                })
                ->orWhere(function ($sub_query) {
                    $sub_query->where('spt.admin_id', '=', Auth::id());
                })
                ->orWhere(function ($sub_query) {
                    $sub_query->where('crt.crm_request_tagging_type_id', 2)
                        ->where('crt.tagged_id', '=', Auth::id());
                })
                ->orWhere(function ($sub_query) {
                    $sub_query->where('crt.crm_request_tagging_type_id', 1)
                        ->where('adp.id', '=', session('department_id'))
                        ->where(function ($sub_sub_query) {
                            $sub_sub_query->whereIn('oc.hub_id', session('hubs'))
                                ->orWhereIn('dc.hub_id', session('hubs'))
                                ->orWhereIn('crt.hub_id', session('hubs'));
                        });
                })
                ->orWhere(function ($sub_query) {
                    $sub_query->where('cret.role_id', '=', session('role_id'))
                        ->where(function ($sub_sub_query) {
                            $sub_sub_query->whereNull('cret.hub_id')
                                ->orWhereNotNull('cret.hub_id')
                                ->whereIn('cret.hub_id', session('hubs'));
                        });
                })
                ->orWhere(function ($sub_query) {
                    if(in_array(session('role_id'), [8, 9 ,10])){
                        $sub_query->whereIn('oc.hub_id', session('hubs'))
                            ->orWhereIn('dc.hub_id', session('hubs'));
                    }
                });
            });
        }
        else if (in_array(session('role_id'), [67, 43])){
            $in_process_request = $in_process_request->where('at.id', Auth::id());
        }
        else if (session('department_id') == 7){
            if(!in_array(session('id'), session('sale_users_bypass'))){
                $in_process_request = $in_process_request->where('spt.admin_id', Auth::id());
            }
        }
        $datatables = Datatables::of($in_process_request)
            ->setRowAttr([
                'class' => function ($shipments) {
                    $ret = '';
                    if ($shipments->star_status == 1) {
                        $ret = 'star_shippers';
                    }

                    if($shipments->case_nature_id == 4){
                        $temp_date_created = Carbon::parse($shipments->created_at)->format("Y-m-d 00:00:00");
                        $date_created = Carbon::parse($temp_date_created);
                        $today = Carbon::today();
                        $workint_days = $date_created->diffInDaysFiltered(function(Carbon $date) {
                            return !$date->isWeekend();
                        }, $today);

                        if ($workint_days > 10) {
                            if ($shipments->star_status == 1) {
                                return 'star_shipper_bold highalert_row';
                            }
                            else{
                                return 'star_shippers';
                            }
                        }
                    }
                    else{
                        return $ret .'';
                    }
                }

            ])
            ->addColumn('id_padded', function ($requests) {
                return str_pad($requests->id, 6, '0', STR_PAD_LEFT);
            })
            ->addColumn('id_padded_link', function ($requests) {
                if ($requests->star_status == 1)
                {
                    return '<u><a href=' . route('admin.crm.request.details', ['id' => $requests->id]) . ' target="_blank"><i class="star_shippers_icon"></i>' . str_pad($requests->id, 6, '0', STR_PAD_LEFT). '</a></u>';
                }
                else
                {
                    return '<u><a href=' . route('admin.crm.request.details', ['id' => $requests->id]) . ' target="_blank">' . str_pad($requests->id, 6, '0', STR_PAD_LEFT). '</a></u>';
                }

            })
            ->addColumn('tagged', function ($requests) {
                if($requests->tagged_type == 1){
                    return 'Department';
                }
                else if($requests->tagged_type == 2){
                    return 'Admin';
                }
                else{
                    return '-';
                }
            })
            ->addColumn('tracking_number_hyperlink', function ($requests) {
                return '<u><a href=' . route('admin.tracking.index') . '?tracking_number=' . $requests->tracking_number . ' class="tracking" target="_blank">' . $requests->tracking_number . '</a></u>';
            })
            ->editColumn('descr',function($request){
                return strip_tags($request->description);
            })
            ->addColumn('added_by', function($requests){
                if($requests->launched_added_by == 0) {
                    return 'Admin';
                }
                else if($requests->launched_added_by == 1) {
                    return 'Shipper';
                }
                else if($requests->launched_added_by == 2) {
                    return 'Shipper Substitute User';
                }
                else if($requests->launched_added_by == 3){
                    return 'Retail';
                }
                else if($requests->launched_added_by == 4){
                    return 'Consignee';
                }
                return $requests->launched_added_by;
            })
            ->addColumn('current_tat', function ($requests){
                if($requests->created_at){
                    Carbon::setWeekendDays([
                        Carbon::SUNDAY,
                    ]);

                    $re_open_count = CrmRequestStatusHistory::where('crm_request_id', $requests->id)->where('status_id' ,5)->latest('id');
                    if($re_open_count->exists()){
                        $re_open_count = $re_open_count->first();

                        $launched = Carbon::parse($re_open_count->created_at);
                        $last_closed = CrmRequestStatusHistory::where('crm_request_id', $requests->id)->where('status_id' ,4)->where('created_at', '>=', $re_open_count->created_at)->first();
                        if($last_closed){
                            $current = $last_closed->created_at;
                        }
                        else{
                            $current = Carbon::now();
                        }
                        $time_format = 'H:i';
                        $time_from = CrmSettings::where('name','TAT Cut-Off Time From')->first();
                        $time_to = CrmSettings::where('name','TAT Cut-Off Time To')->first();
                        $to_formatted = date($time_format, strtotime($time_to->setting_value));
                        $cut_off_check = $requests->created_at->format($time_format);
                        $additional_tat = $current->diffInWeekdays($launched);
                        $current_tat = $additional_tat;
                        $launched_check = $launched->toDateString();
                        $current_check = $current->toDateString();
                        if($launched_check <= $current_check){
                            if($to_formatted < $cut_off_check){
                                $after_cut_off = $current_tat - 1;
                                $current_tat = $after_cut_off;
                            }
                        }
                        $holidays = CrmTatHolidays::whereBetween('holiday', [$launched, $current])->get();
                        foreach($holidays as $holiday){
                            $holiday_formatted = date('Y-m-d H:i:s', strtotime($holiday->holiday));
                            $holiday_formatted_check = date('Y-m-d', strtotime($holiday->holiday));
                            $launched_formatted_check = date('Y-m-d', strtotime($launched));
                            if($launched < $holiday_formatted || $current > $holiday_formatted){
                                if($holiday_formatted_check == $launched_formatted_check){
                                    if($to_formatted < $cut_off_check){
                                        $after_cut_off = $current_tat + 1;
                                        $current_tat = $after_cut_off;
                                    }
                                }
                                $after_holidays = $current_tat - 1;
                                $current_tat = $after_holidays;
                            }
                        }
                    }
                    else {
                        $launched = Carbon::parse($requests->created_at)->startOfDay();
                        $first_closed = CrmRequestStatusHistory::where('crm_request_id', $requests->id)->where('status_id' ,4)->first();
                        if($first_closed){
                            $current = $first_closed->created_at;
                        }
                        else{
                            $current = Carbon::now();
                        }
                        $time_format = 'H:i';
                        $time_from = CrmSettings::where('name','TAT Cut-Off Time From')->first();
                        $time_to = CrmSettings::where('name','TAT Cut-Off Time To')->first();
                        $to_formatted = date($time_format, strtotime($time_to->setting_value));
                        $cut_off_check = $requests->created_at->format($time_format);
                        $current_tat = $current->diffInWeekdays($launched);

                        $launched_check = $launched->toDateString();
                        $current_check = $current->toDateString();
                        if($launched_check <= $current_check){
                            if($to_formatted < $cut_off_check){
                                $after_cut_off = $current_tat - 1;
                                $current_tat = $after_cut_off;
                            }
                        }
                        $holidays = CrmTatHolidays::whereBetween('holiday', [$launched, $current])->get();
                        foreach($holidays as $holiday){
                            $holiday_formatted = date('Y-m-d H:i:s', strtotime($holiday->holiday));
                            $holiday_formatted_check = date('Y-m-d', strtotime($holiday->holiday));
                            $launched_formatted_check = date('Y-m-d', strtotime($launched));
                            if($launched < $holiday_formatted || $current > $holiday_formatted){
                                if($holiday_formatted_check == $launched_formatted_check){
                                    if($to_formatted < $cut_off_check){
                                        $after_cut_off = $current_tat + 1;
                                        $current_tat = $after_cut_off;
                                    }
                                }
                                $after_holidays = $current_tat - 1;
                                $current_tat = $after_holidays;
                            }
                        }
                    }

                    return $current_tat;
                }
                return "-";
            })
            ->editColumn('tagged_to', function($requests){
                if($requests->crm_request_tagging_type_id == 1) {
                    return $requests->tagged_department;
                }
                else if($requests->crm_request_tagging_type_id == 2) {
                    return $requests->tagged_admin;
                }
                else{
                    return '-';
                }
            })
            ->filterColumn('status',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('ss.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('tagged_to',function ($query,$keyword){
                if ($keyword != '') {
                    $query->where(function($sub_query) use ($keyword) {
                        $sub_query->where('crt.crm_request_tagging_type_id', '=', 1)
                            ->where('adp.name', 'like', '%' . $keyword . '%');
                    })
                    ->orWhere(function($sub_query) use ($keyword) {
                        $sub_query->where('crt.crm_request_tagging_type_id', '=', 2)
                            ->where('at.name', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->orderColumn('tagged_to', DB::raw('IF (crt.crm_request_tagging_type_id = 1, adp.name, IF (crt.crm_request_tagging_type_id = 2, at.name, ""))') . ' $1')
            ->editColumn('agent', function ($requests){
                if($requests->agent == null){
                    return '-';
                }
                else{
                    return $requests->agent;
                }
            })
            ->addColumn('launched_by_name', function ($requests){
                $name = '';
                if($requests->launched_added_by == 0){
                    $name = $requests->name;
                }
                else if($requests->launched_added_by == 1){
                    $name = $requests->shipper;
                }else if($requests->launched_added_by == 2){
                    $name = $requests->sub_shipper;
                }else if($requests->launched_added_by == 3){
                    $name = $requests->retail_user;
                }else if($requests->launched_added_by == 4){
                    $name = $requests->consignee_user;
                }
                return $name;
            })
            ->editColumn('last_comment_date', function($requests){
                if($requests->last_comment_date != null){
                    return $requests->last_comment_date;
                }
                else{
                    return '-';
                }
            })
            ->filterColumn('launched_by_name', function($query, $keyword) {
                $keyword = strtolower($keyword);

                if ($keyword != '') {
                    $query->where(function ($sub_query) use ($keyword) {
                        $sub_query->where('crm_requests.launched_by', '=', 0)
                            ->where('a.name', 'like', '%' . $keyword . '%');
                    })
                        ->orWhere(function ($sub_query) use ($keyword) {
                            $sub_query->where('crm_requests.launched_by', '=', 1)
                                ->where('u.name', 'like', '%' . $keyword . '%');
                        })
                        ->orWhere(function ($sub_query) use ($keyword) {
                            $sub_query->where('crm_requests.launched_by', '=', 2)
                                ->where('su.name', 'like', '%' . $keyword . '%');
                        })
                        ->orWhere(function ($sub_query) use ($keyword) {
                            $sub_query->where('crm_requests.launched_by', '=', 3)
                                ->where('cu.name', 'like', '%' . $keyword . '%');
                        });

                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->orderColumn('launched_by_name', DB::raw('IF (crm_requests.launched_by = 0, a.name, IF (crm_requests.launched_by = 1, u.name, IF (crm_requests.launched_by = 2, su.name, "")))') . ' $1')
            ->filterColumn('case_nature_type',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('crcnt.id','=',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->editColumn('last_comment_name', function($requests){
                if($requests->last_comment_by == 0){
                    return $requests->last_comment_admin;
                }
                else if($requests->last_comment_by == 1){
                    return $requests->last_comment_shipper;
                }
                else{
                    return '-';
                }
            })
            ->filterColumn('last_comment_name', function($query, $keyword) {
                $keyword = strtolower($keyword);

                if ($keyword != '') {
                    $query->where(function ($sub_query) use ($keyword) {
                        $sub_query->where('ccs.comment_by', '=', 0)
                            ->where('accs.name', 'like', '%' . $keyword . '%');
                    })
                        ->orWhere(function ($sub_query) use ($keyword) {
                            $sub_query->where('ccs.comment_by', '=', 1)
                                ->where('uccs.name', 'like', '%' . $keyword . '%');
                        });
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->orderColumn('last_comment_name', DB::raw('IF (ccs.comment_by = 0, accs.name, IF (ccs.comment_by = 1, uccs.name, ""))') . ' $1')
        /*  ->addColumn('special_request', function ($shipments){
                return '<div class="text-center">
                                <button type="button" class="btn btn-primary btn-sm"><a class="white" ><i class="la la-dollar align-middle"></i></a></button>
                        </div>';
            })*/
            ->editColumn('last_comment', function($requests){
                if($requests->last_comment != null){
                    return $requests->last_comment;
                }
                else{
                    return '-';
                }
            })
            ->addColumn('tagged_to_manual', function($requests){
                $crm_tagging = CrmRequestTagging::where('crm_request_id',$requests->id)->whereIn('crm_request_tagging_type_id', [1,2])->get()->first();
                if($crm_tagging){
                    if($crm_tagging->crm_request_tagging_type_id == 1){
                        
                        $tagged_name = AdminDepartment::find($crm_tagging->tagged_id)->name;
                        return $tagged_name;
                    }elseif($crm_tagging->crm_request_tagging_type_id == 2){
                        $tagged_name = Admin::find($crm_tagging->tagged_id)->name;
                        return $tagged_name;

                    }else{
                        return '-';
                    }
                }else{
                    return '-';
                }
            })
            ->filterColumn('tagged_to_manual',function ($query,$keyword){
                if ($keyword != '') {
                    $query->where(function($sub_query) use ($keyword) {
                        $sub_query->where('crt.crm_request_tagging_type_id', '=', 1)
                            ->where('adp.name', 'like', '%' . $keyword . '%');
                    })
                    ->orWhere(function($sub_query) use ($keyword) {
                        $sub_query->where('crt.crm_request_tagging_type_id', '=', 2)
                            ->where('at.name', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->orderColumn('tagged_to_manual', DB::raw('IF (crt.crm_request_tagging_type_id = 1, adp.name, IF (crt.crm_request_tagging_type_id = 2, at.name, ""))') . ' $1')
            ->addColumn('tagged_to_kae', function($requests){
                $crm_tagging = CrmRequestTagging::where('crm_request_id',$requests->id)->where('crm_request_tagging_type_id',4)->get()->first();
                if($crm_tagging){
                    $admin = Admin::find($crm_tagging->tagged_id);
                    if($admin){
                        return $admin->name;

                    }else{

                        return '-';
                    }
                }else{
                    return '-';
                }

            })
            ->filterColumn('tagged_to_kae',function ($query,$keyword){
                if ($keyword != '') {
                    $query->where(function($sub_query) use ($keyword) {
                        $sub_query->where('crt.crm_request_tagging_type_id', '=', 4)
                            ->where('at.name', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->orderColumn('tagged_to_kae', DB::raw('IF (crt.crm_request_tagging_type_id = 4, at.name, "")') . ' $1')
            
            ->addColumn('tagged_to_operation', function($requests){
                $crm_tagging = CrmRequestTagging::where('crm_request_id',$requests->id)->where('crm_request_tagging_type_id',5)->get()->first();
                if($crm_tagging){
                    $admin = Admin::find($crm_tagging->tagged_id);
                    if($admin){
                        return $admin->name;

                    }else{

                        return '-';
                    }
                }else{
                    return '-';
                }
            })
            ->filterColumn('tagged_to_operation',function ($query,$keyword){
                if ($keyword != '') {
                    $query->where(function($sub_query) use ($keyword) {
                        $sub_query->where('crt.crm_request_tagging_type_id', '=', 5)
                            ->where('at.name', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->orderColumn('tagged_to_operation', DB::raw('IF (crt.crm_request_tagging_type_id = 5, at.name, "")') . ' $1')
            ->addColumn('action', function($requests) {
                $route = route('admin.crm.request.details', ['id' => $requests->id]);
                    $dropdown = '
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">';
                    $dropdown .= '<button onclick="window.open(\'' . $route . '\', \'_tab\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Details</div></button>';
                    $dropdown .= '</div>
                </div>
                ';

                    return $dropdown;
            })
            ->addColumn('responsible_hub', function ($requests) {
                $responsible_hub = "";
                $status = $requests->shipment_status_id;
                $shipment_id = $requests->shipment_id;
                $origin_hub = $requests->origin_hub;
                $destination_hub = $requests->hub;
                $shipment_journey = ShipmentsJourney::whereIn('shipper_status_id',[11,12,20,21,22])
                    ->where('shipment_id',$shipment_id);
                if($shipment_journey->exists()){
                    $shipment_journey = $shipment_journey->pluck('shipper_status_id')->toArray();
                    if(in_array(11,$shipment_journey) &&  in_array(12,$shipment_journey) ) {
                        $temp = $destination_hub;
                        $destination_hub = $origin_hub;
                        $origin_hub = $temp;
                    }
                }
                if($status == 1 || $status == 2 || $status == 17 || $status == 22){
                    $responsible_hub = $origin_hub;
                }
                elseif($status == 3 || $status == 21){
                    $manifest_bag = CargoManifestBagShipments::
                    leftjoin('cargo_manifest_bags as cmb','cmb.id','=','cargo_manifest_bag_shipments.cargo_manifest_bag_id')
                        ->leftjoin('cities as c','c.id','=','cmb.origin_hub_id')
                        ->leftjoin('cities as cd','cd.id','=','cmb.destination_hub_id')
                        ->leftjoin('cities as chi','chi.id','=','cmb.current_hub_id')
                        ->where('cargo_manifest_bag_shipments.shipment_id',$shipment_id)
                        ->select(['cargo_manifest_bag_shipments.id','cmb.status_id','c.name as origin_hub','cd.name as destination_hub','chi.name as curren_hub_origin'])
                        ->orderby('cargo_manifest_bag_shipments.id','desc');
                    if($manifest_bag->exists()){
                        $manifest_bag = $manifest_bag->first();
                        if ($manifest_bag->status_id == 0) {  //bag created
                            $responsible_hub = $manifest_bag->origin_hub;
                        }
                        elseif ($manifest_bag->status_id == 1) {  //bag created
                            $responsible_hub = $manifest_bag->origin_hub;
                        }elseif ($manifest_bag->status_id == 3) { // bag Received at junction
                            $responsible_hub = $manifest_bag->curren_hub_origin;
                        }
                        elseif ($manifest_bag->status_id == 2 || $manifest_bag->status_id == 4 || $manifest_bag->status_id == 5 || $manifest_bag->status_id == 7) {
                            $responsible_hub = $manifest_bag->destination_hub;
                        } elseif ($manifest_bag->status_id == 9) {
                            $responsible_hub = $manifest_bag->curren_hub_origin;
                        }
                    }
                }
                elseif($status == 4 || $status == 12 || $status == 20|| $status == 24 || $status == 11 || $status == 8){
                    $responsible_hub = $destination_hub;
                }
                else{
                    $responsible_hub = "-";
                }
                return $responsible_hub;
            })
            ->addColumn('responsible_zone', function ($requests) {
                $responsible_zone = "";
                $status = $requests->shipment_status_id;
                $shipment_id = $requests->shipment_id;

                $origin_zone = $requests->origin_zone;
                $destination_zone = $requests->zone;
                $shipment_journey = ShipmentsJourney::whereIn('shipper_status_id',[11,12,20,21,22])
                    ->where('shipment_id',$shipment_id);
                if($shipment_journey->exists()){
                    $shipment_journey = $shipment_journey->pluck('shipper_status_id')->toArray();
                    if(in_array(11,$shipment_journey) &&  in_array(12,$shipment_journey) ) {
                        $temp = $destination_zone;
                        $destination_zone = $origin_zone;
                        $origin_zone = $temp;
                    }
                }

                if($status == 1 || $status == 2 || $status == 17 || $status == 22){
                    $responsible_zone = $origin_zone;
                }
                elseif($status == 3 || $status == 21){
                    $manifest_bag = CargoManifestBagShipments::
                    leftjoin('cargo_manifest_bags as cmb','cmb.id','=','cargo_manifest_bag_shipments.cargo_manifest_bag_id')
                        ->leftjoin('cities as c','c.id','=','cmb.origin_hub_id')
                        ->leftjoin('zones as cz','cz.id','=','c.zone_id')
                        ->leftjoin('cities as cd','cd.id','=','cmb.destination_hub_id')
                        ->leftjoin('zones as cdz','cdz.id','=','cd.zone_id')
                        ->leftjoin('cities as chi','chi.id','=','cmb.current_hub_id')
                        ->leftjoin('zones as chiz','chiz.id','=','chi.zone_id')
                        ->where('cargo_manifest_bag_shipments.shipment_id',$shipment_id)
                        ->select(['cargo_manifest_bag_shipments.id','cmb.status_id','cz.name as origin_zone','cdz.name as destination_zone','chiz.name as curren_zone_origin'])
                        ->orderby('cargo_manifest_bag_shipments.id','desc');
                    if($manifest_bag->exists()){
                        $manifest_bag = $manifest_bag->first();
                        if ($manifest_bag->status_id == 0) {  //bag created
                            $responsible_zone = $manifest_bag->origin_zone;
                        }
                        elseif ($manifest_bag->status_id == 1) {  //bag created
                            $responsible_zone = $manifest_bag->origin_zone;
                        }elseif ($manifest_bag->status_id == 3) { // bag Received at junction
                            $responsible_zone = $manifest_bag->curren_zone_origin ;
                        }
                        elseif ($manifest_bag->status_id == 2 || $manifest_bag->status_id == 4 || $manifest_bag->status_id == 5 || $manifest_bag->status_id == 7) {
                            $responsible_zone = $manifest_bag->destination_zone;
                        } elseif ($manifest_bag->status_id == 9) {
                            $responsible_zone = $manifest_bag->curren_zone_origin;
                        }
                    }
                }
                elseif($status == 4 ||  $status == 12 || $status == 20|| $status == 24 || $status == 11 || $status == 8){
                    $responsible_zone = $destination_zone;
                }else{
                    $responsible_zone = '-';
                }
                return $responsible_zone;
            })->addColumn('arrival_today', function ($request)use($current_date) {
                return ($request->arrival_date && $current_date) ? with((new Carbon($request->arrival_date, 'UTC'))->diffInWeekendDays($current_date) - (new Carbon($request->arrival_date, 'UTC'))->diffInDaysFiltered(function (Carbon $date) {
                    $date->isSunday();
                }, $current_date)) : '-';
            })
            ->addColumn('last_status_today', function ($request)use($current_date) {
                return ($request->last_status_today && $current_date) ? with((new Carbon($request->last_status_today, 'UTC'))->diffInWeekendDays($current_date) - (new Carbon($request->last_status_today, 'UTC'))->diffInDaysFiltered(function (Carbon $date) {
                    $date->isSunday();
                }, $current_date)) : '-';
            });

        if ($tracking_numbers = $request->get('tracking_numbers')) {
            $datatables->whereIn('s.tracking_number', explode(',', $tracking_numbers));
        }

        if($request->get('star_shipper_filter') == 1)
        {
            $datatables->where('sts.status',1);
        }

        return $datatables->make(true);
    }
}
