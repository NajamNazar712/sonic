<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminRole;
use App\Http\Models\Admin\AdminDepartment;
use App\Http\Models\Admin\CargoManifest\CargoManifestBagShipments;
use App\Http\Models\Admin\Lead\Lead;
use App\Http\Models\CRM\CrmRequestTaggingTypes;
use App\Http\Models\CRM\CrmRequestFeedback;
use App\Http\Models\CRM\CrmRequestChannel;
use App\Http\Models\CRM\CrmRequestCaseNatureType;
use App\Http\Models\CRM\CrmRequestCaseNature;
use App\Http\Models\CRM\CrmClosedReasonStatus;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\CRM\CrmRequestStatusHistory;
use App\Http\Models\CRM\CrmSettings;
use App\Http\Models\CRM\CrmTatHolidays;
use App\Http\Models\CRM\CrmRequestTagging;
use App\Http\Models\CRM\CrmRequestTaggingHistory;
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
use App\Http\Models\Shipment;

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
        $crm_request_statuses = CrmRequestStatus::whereNotIn('id',[5])->select('id', 'name')->get();
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
        $statuses = DB::connection('reports')->table('crm_request_statuses')->select('id', 'name')->whereNotIn('id', [6, 7])->get();
        $shipping_modes = DB::connection('reports')->table('shipping_modes')->get(['id', 'mode']);
        $shipment_status = ShipmentStatus::select('id','name')->get();
        


        // From Admin Leads 
        $today = Carbon::now()->endOfDay();
        $thirtyDays = Carbon::now()->subDays(30)->startOfDay();
        $numberOfDays = $thirtyDays->diffInDays($today);
        
        if(in_array(session('role_id'),[1,32,6,37,51,83,90]))
        {
            $crm_feedback = CrmRequestFeedback::
            whereBetween('created_at',[$thirtyDays,$today])->
            pluck('crm_request_id');

            $crm['total'] = CrmRequest::get();
            // whereBetween('created_at', [$thirtyDays, $today]);
            
            //For Closer Rate
            $crm_total = $crm['total']->whereNotIn('id', $crm_feedback)->count();
            //End For Closer Rate

            $crm['launched'] = CrmRequest::
            whereBetween('created_at', [$thirtyDays, $today])->
            where('status_id', 1);
            $crm['in_process'] = CrmRequest::
            whereBetween('created_at', [$thirtyDays, $today])->
            where('status_id', 2);
            $crm['resolved'] = CrmRequest::
            whereBetween('created_at', [$thirtyDays, $today])->
            where('status_id', 3);
            $crm['closed'] = CrmRequest::
            whereBetween('created_at', [$thirtyDays, $today])->
            where('status_id', 4);
            $crm['closed_rate_avg'] = CrmRequest::
            whereBetween('created_at', [$thirtyDays, $today])->
            where('status_id', 4);
            $crm['valid'] = CrmRequest::
            whereBetween('created_at', [$thirtyDays, $today])->
            where('status_id', 6);
            $crm['in_valid'] = CrmRequest::
            whereBetween('created_at', [$thirtyDays, $today])->
            where('status_id', 7);
           
        }else
        {
            $crm_feedback = CrmRequestFeedback::
            whereBetween('created_at',[$thirtyDays,$today])->
            pluck('crm_request_id');

            // dd(auth()->user()->id);
            $crm['total'] = CrmRequest::get();
            // whereBetween('created_at', [$thirtyDays, $today]);

            //For Closer Rate
            $crm_total = $crm['total']->whereNotIn('id', $crm_feedback)->count();
            //End For Closer Rate

            $crm['launched'] = CrmRequest::
            whereBetween('created_at', [$thirtyDays, $today])->
            where('status_id', 1)->where('agent_id',auth()->user()->id);
            $crm['in_process'] = CrmRequest::
            whereBetween('created_at', [$thirtyDays, $today])->
            where('status_id', 2)->where('agent_id',auth()->user()->id);
            $crm['resolved'] = CrmRequest::
            whereBetween('created_at', [$thirtyDays, $today])->
            where('status_id', 3)->where('agent_id',auth()->user()->id);
            $crm['closed'] = CrmRequest::
            whereBetween('created_at', [$thirtyDays, $today])->
            where('status_id', 4)->where('agent_id',auth()->user()->id);
            $crm['closed_rate_avg'] = CrmRequest::
            whereBetween('created_at', [$thirtyDays, $today])->
            where('status_id', 4)->where('agent_id',auth()->user()->id);
            $crm['valid'] = CrmRequestStatusHistory::
            whereBetween('created_at', [$thirtyDays, $today])->
            where('status_id', 6)->where('agent_id',auth()->user()->id);
            $crm['in_valid'] = CrmRequestStatusHistory::
            whereBetween('created_at', [$thirtyDays, $today])->
            where('status_id', 7)->where('agent_id',auth()->user()->id);
            $crm_feedback = CrmRequestFeedback::whereBetween('created_at',[$thirtyDays,$today])->where('agent_id',auth()->user()->id)->pluck('crm_request_id');
        }

        // if (session('role_id') != 1) {
        //     //$crm['launched'] = $crm['launched']->join('cities as c', 'c.id', '=', 'leads.city_id')->whereIn('c.hub_id', session('hubs'));
        //     // $leads['received'] = $leads['received']->join('cities as c', 'c.id', '=', 'leads.city_id')->whereIn('c.hub_id', session('hubs'));
        //     // $leads['in_process'] = $leads['in_process']->join('cities as c', 'c.id', '=', 'leads.city_id')->whereIn('c.hub_id', session('hubs'));
        //     // $leads['in_process_for_activation'] = $leads['in_process_for_activation']->join('cities as c', 'c.id', '=', 'leads.city_id')->whereIn('c.hub_id', session('hubs'));
        //     // $leads['dead_leads'] = $leads['dead_leads']->join('cities as c', 'c.id', '=', 'leads.city_id')->whereIn('c.hub_id', session('hubs'));
        //     // $leads['accounts_activated'] = $leads['accounts_activated']->join('cities as c', 'c.id', '=', 'leads.city_id')->whereIn('c.hub_id', session('hubs'));
        //     // $leads['dormant'] = $leads['dormant']->join('cities as c', 'c.id', '=', 'leads.city_id')->whereIn('c.hub_id', session('hubs'));
        // }
        // if (session('department_id') == 7) {
        //     if (!in_array(session('id'), session('sale_users_bypass')) && session('role_id') != 44 && session('role_id') != 60) {
        //         // $leads['total'] = $leads['total']->where('leads.sale_person_id', Auth::id());
        //         // $leads['received'] = $leads['received']->where('leads.sale_person_id', Auth::id());
        //         // $leads['in_process'] = $leads['in_process']->where('leads.sale_person_id', Auth::id());
        //         // $leads['in_process_for_activation'] = $leads['in_process_for_activation']->where('leads.sale_person_id', Auth::id());
        //         // $leads['dead_leads'] = $leads['dead_leads']->where('leads.sale_person_id', Auth::id());
        //         // $leads['accounts_activated'] = $leads['accounts_activated']->where('leads.sale_person_id', Auth::id());
        //         // $leads['dormant'] = $leads['dormant']->where('leads.sale_person_id', Auth::id());
        //     }
        // } 


        $crm['total'] = $crm['total']->count();
        $crm['launched'] = $crm['launched']->count();
        $crm['in_process'] = $crm['in_process']->count();
        $crm['resolved'] = $crm['resolved']->count();
        $crm['closed'] = $crm['closed']->count();
        $crm['valid'] = $crm['valid']->count();
        $crm['in_valid'] = $crm['in_valid']->count();
        
        $shipments = Shipment::where('shipper_status_id',2)->
        whereBetween('created_at', [$thirtyDays, $today])->
        count();
        
       
        $crm['closed_rate'] = $crm_total !== 0 ? $crm['closed']/$crm_total : 0;
        $crm['in_process_ratio'] = $shipments !== 0 ? $crm_total/$shipments : 0;
        
        $crm['in_valid_percentage'] = "0";
        $crm['closed_rate_percentage'] = "0";
        $crm['in_process_ratio_percentage'] = "0";
        if ($crm['total'] > 0) {
            $crm['in_valid_percentage'] = round(($crm['in_valid'] / $crm['total']) * 100, 2);
            $crm['closed_rate_percentage'] = $crm_total !== 0 ? round(($crm['closed_rate']) * 100, 2)  : 0;
            $crm['in_process_ratio_percentage'] =  $shipments !== 0 ? round(($crm['in_process_ratio']) * 100, 2) : 0;
        }
       
        $leads['in_process_for_activation'] = "3";
        $leads['in_process_for_activation_percentage'] = "3";
        $leads['dormant'] = "3";

        $crm['launched'] = number_format($crm['launched']);
        $crm['in_process'] = number_format($crm['in_process']);
        $crm['resolved'] = number_format($crm['resolved']);
        $crm['closed'] = number_format($crm['closed']);
        $crm['valid'] = number_format($crm['valid']);
        $crm['in_valid'] = number_format($crm['in_valid']);
        $crm['closed_rate'] = number_format($crm['closed_rate']);
        $crm['in_process_ratio'] = number_format($crm['in_process_ratio']);


        $cities = City::where('status', 1)->select('id', 'name')->get();

        $dates['current'] = Carbon::now();
        $dates['old_date'] = Carbon::now()->subDays(58);

        return view('admin.crm.dashboard')->with(['case_natures' => $case_natures, 'case_nature_types' => $case_nature_types,'statuses' => $statuses, 'shipping_modes' => $shipping_modes, 'channels' => $channels, 'agents' => $agents, 'shipment_status' => $shipment_status, 'types' => $types, 'admins' => $admins, 'departments' => $departments, 'hubs' => $hubs, 'zones' => $zones, 'closed_reason_statuses' => $closed_reason_statuses,'dates' => $dates,'cities' => $cities,'crm_request_statuses' => $crm_request_statuses,'crm' => $crm]);
    }

    public function crm_dashboard_list(Request $request){
       
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),660);
        }

        $today = Carbon::now()->endOfDay();
        $thirtyDays = Carbon::now()->subDays(30)->startOfDay();
        $card_feedback = CrmRequestFeedback::
        whereBetween('created_at',[$thirtyDays,$today])->
        pluck('crm_request_id');
        $dashboard_list = CrmRequest::leftjoin('crm_request_case_nature as crcn', 'crcn.id', '=', 'crm_requests.case_nature_id')
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
        ->leftjoin('cities as dc', 'dc.id', '=', 's.consignee_city_id')
        ->leftjoin('zones as ocz', 'ocz.id', '=', 'oc.zone_id')
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
        ->leftjoin('shipping_modes as sm','sm.id','=','s.shipping_mode_id')
        ->leftjoin('admins as ad1','spt.admin_id','=','ad1.id')
        ->leftjoin('users as us','us.id','=','s.user_id')
        ->leftjoin('segments as seg','us.segment_id','seg.id')
        ->leftjoin('sale_tier_tags as stt','stt.user_id', '=','s.user_id')
        ->leftjoin('admins as ad2','ad2.id','=','stt.kam')
        ->leftjoin('admins as a1', 'a1.id', '=', 'crm_requests.agent_id')
        ->leftjoin('crm_request_statuses as crs', 'crs.id', '=', 'crm_requests.status_id')
        ->select('sj.created_at as arrival','crm_requests.id as id', 's.tracking_number as tracking_number', 'crcn.name as case_nature', 'crcnt.type as case_nature_type', 'crc.channel as channel', 'ad.name as agent', 'a.name as name', 'u.name as shipper', 'su.name as sub_shipper', 'cu.name as consignee_users', 'ru.name as retail_users', 'crm_requests.launched_by as launched_added_by', 'crm_requests.created_at as created_at', 'crm_requests.description as description','crm_requests.description as descr','at.name as tagged_admin', 'adp.name as tagged_department', 'crt.crm_request_tagging_type_id as crm_request_tagging_type_id', 'ss.name as status','ss.id as shipment_status_id', 'user.name as shipper_name', 'oc.name as origin','och.name as origin_hub','ocz.name as origin_zone', 'dc.name as destination', 'dh.name as hub', 'crt.crm_request_tagging_type_id as tagged_type', 'res.created_at as valid_date', 'ccs.comment as last_comment', 'ccs.created_at as last_comment_date', 'ccs.comment_by as last_comment_by', 'accs.name as last_comment_admin', 'uccs.name as last_comment_shipper', 'crm_requests.launched_by_id', 'res.created_at as agent_assigned_date', 'resby.name as agent_assigned_by', 'crth.created_at as tagged_date', 'z.name as zone','crsh.created_at as reopen_date','crm_requests.address as address', 'crm_requests.address_latitude as address_latitude','crm_requests.address_longitude as address_longitude','at.id as tagged_admin_id', 'crm_requests.case_nature_id','crm_requests.shipment_id','sts.status as star_status','crm_requests.updated_at as last_status_date','sm.mode as shipping_mode','ad1.name as sale_person','ad2.name as kae','seg.name as segment','sj.updated_at as arrival_date','s.updated_at as last_status_today','s.amount as cod_value','crs.name as crm_request_status','crs.id as crm_request_status_id')
        ->whereNotIn('crm_requests.id', $card_feedback)
        ->whereNotIn('crs.id',[5])
        ->groupBy('crm_requests.id');
            $current_date = Carbon::now();
        if ((!in_array(session('role_id'), [1, 4, 6])) && (!in_array(179, session('permissions')) && !in_array(201, session('permissions')))) {
            $dashboard_list = $dashboard_list->where(function ($query) {
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
            $dashboard_list = $dashboard_list->where('at.id', Auth::id());
        }
        else if (session('department_id') == 7){
            if(!in_array(session('id'), session('sale_users_bypass'))){
                $dashboard_list = $dashboard_list->where('spt.admin_id', Auth::id());
            }
        }
        $datatables = Datatables::of($dashboard_list)
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
            })
            ->addColumn('shipper_category', function($requests){
                if($requests->kae != null){
                    return 'Key Account';
                }else{
                    return 'Non-Key Account';
                }
            });
        if ($tracking_numbers = $request->get('tracking_numbers')) {
            $datatables->whereIn('s.tracking_number', explode(',', $tracking_numbers));
        }
        if ($origin = $request->get('search_origin')) {
            $datatables->where('oc.id', '=', $origin);
        }
        if ($zone = $request->get('search_zone')) {
            $datatables->where('z.id', '=', $zone);
        }
        if ($destination = $request->get('search_destination')) {
            $datatables->where('dc.id', '=', $destination);
        }
        if ($case_nature = $request->get('search_case_nature')) {
            $datatables->where('crcn.id', '=', $case_nature);
        }
        if ($case_nature_type = $request->get('search_case_nature_type')) {
            $datatables->where('crcnt.id', '=', $case_nature_type);
        }
        if ($agent = $request->get('search_agent')) {
            $datatables->where('a1.id', '=', $agent);
        }
        if ($shipment_status = $request->get('search_shipment_status')) {
            $datatables->where('s.shipper_status_id', '=', $shipment_status);
        }
        if ($avg_tat = $request->get('avg_tat')) {
            $datatables->where('crm_requests.status_id', '=', $avg_tat);
        }
        if ($request->get('from_date') && $request->get('to_date')) {
            $from = $request->get('from_date');
            $to = $request->get('to_date');
            $datatables->whereBetween('crm_requests.created_at', [$from, $to]);
        }
        else
        {
            $datatables->whereBetween('crm_requests.created_at', [$thirtyDays, $today]);
        }
        if ($search_request = $request->get('search_request')) {
            if ($search_request == 1) {
                $search_statuses = [1];
            } elseif ($search_request == 2) {
                $search_statuses = [2];
            } elseif ($search_request == 3) {
                $search_statuses = [3];
            } elseif ($search_request == 4) {
                $search_statuses = [4];
            } elseif ($search_request == 6) {
                $search_statuses = [6];
            } elseif ($search_request == 7) {
                $search_statuses = [7];
            } else {
                $search_statuses = [1, 2, 3, 4, 6, 7];
            }
            $datatables->whereIn('crm_requests.status_id', $search_statuses);
        }
       if($request->get('star_shipper_filter') == 1)
        {
            $datatables->where('sts.status',1);
        }
        return $datatables->make(true);
    }

    public function card_data(Request $request){
            $today = Carbon::now()->endOfDay();
            $thirtyDays = Carbon::now()->subDays(30)->startOfDay();

            // $card_data['total'] = CrmRequest::
            // // whereBetween('created_at', [$thirtyDays, $today])
            // get();
            $card_data['total'] = CrmRequest::get();

            //Launched
            $card_data['launched'] = CrmRequest::where('crm_requests.status_id', 1);
            // whereBetween('created_at', [$thirtyDays, $today]);
            if($agent_id = $request->get('agent_id'))
            {
                $card_data['launched'] = self::agent($card_data['launched'],$agent_id);
            }
            if (($from = $request->get('from_date')) && ($to = $request->get('to_date')))
            {   
                $card_data['launched'] = self::dates($card_data['launched'],$from,$to);
            }
            else
            {
                $card_data['launched']->whereBetween('created_at', [$thirtyDays, $today]);
            } 
            if ($origin = $request->get('search_origin'))
            {   
                $card_data['launched'] = self::origin($card_data['launched'],$origin);
            } 
            if ($destination = $request->get('search_destination'))
            {   
                $card_data['launched'] = self::destination($card_data['launched'],$destination);
            } 
            if ($zone = $request->get('search_zone'))
            {   
                $card_data['launched']= self::zone($card_data['launched'],$zone);
            }
            if ($search_case_nature = $request->get('search_case_nature'))
            {   
                $card_data['launched'] = self::case_nature($card_data['launched'],$search_case_nature);
            } 
            if ($search_case_nature_type = $request->get('search_case_nature_type'))
            {   
                // dd($search_case_nature_type);
                $card_data['launched'] = self::case_nature_type($card_data['launched'],$search_case_nature_type);
            }
            if ($shipment_status = $request->get('search_shipment_status'))
            {   
                $card_data['launched'] = self::shipment_status($card_data['launched'],$shipment_status);
            }
            if ($avg_tat = $request->get('avg_tat'))
            {   
                $card_data['launched'] = self::avg_tat($card_data['launched'],$avg_tat);
            }  

            //In_process
            $card_data['in_process'] = CrmRequest::where('crm_requests.status_id', 2);
            // ->whereBetween('created_at', [$thirtyDays, $today])
            if($agent_id = $request->get('agent_id'))
            {
                $card_data['in_process'] = self::agent($card_data['in_process'],$agent_id);
            }
            if (($from = $request->get('from_date')) && ($to = $request->get('to_date')))
            {   
                $card_data['in_process'] = self::dates($card_data['in_process'],$from,$to);
            }
            else
            {
                $card_data['in_process']->whereBetween('created_at', [$thirtyDays, $today]);
            }
            if ($origin = $request->get('search_origin'))
            {   
                $card_data['in_process'] = self::origin($card_data['in_process'],$origin);
            }
            if ($destination = $request->get('search_destination'))
            {   
                $card_data['in_process'] = self::destination($card_data['in_process'],$destination);
            }
            if ($zone = $request->get('search_zone'))
            {   
                $card_data['in_process']= self::zone($card_data['in_process'],$zone);
            }
            if ($search_case_nature = $request->get('search_case_nature'))
            {   
                $card_data['in_process'] = self::case_nature($card_data['in_process'],$search_case_nature);
            }
            if ($search_case_nature_type = $request->get('search_case_nature_type'))
            {   
                $card_data['in_process'] = self::case_nature_type($card_data['in_process'],$search_case_nature_type);
            }     
            if ($shipment_status = $request->get('search_shipment_status'))
            {   
                $card_data['in_process'] = self::shipment_status($card_data['in_process'],$shipment_status);
            }
            if ($avg_tat = $request->get('avg_tat'))
            {   
                $card_data['in_process'] = self::avg_tat($card_data['in_process'],$avg_tat);
            } 
            
            //Resolved
            $card_data['resolved'] = CrmRequest::where('crm_requests.status_id', 3);
            // whereBetween('created_at', [$thirtyDays, $today])->
            if($agent_id = $request->get('agent_id'))
            {
                $card_data['resolved'] = self::agent($card_data['resolved'],$agent_id);
            }
            if (($from = $request->get('from_date')) && ($to = $request->get('to_date')))
            {
                $card_data['resolved'] = self::dates($card_data['resolved'],$from,$to);
            }
            else
            {
                $card_data['resolved']->whereBetween('created_at', [$thirtyDays, $today]);
            }
            if ($origin = $request->get('search_origin'))
            {  
                $card_data['resolved'] = self::origin($card_data['resolved'],$origin); 
            }
            if ($destination = $request->get('search_destination'))
            {   
                $card_data['resolved'] = self::destination($card_data['resolved'],$destination);
            }
            if ($zone = $request->get('search_zone'))
            {   
                $card_data['resolved'] = self::zone($card_data['resolved'],$zone);
            }
            if ($search_case_nature = $request->get('search_case_nature'))
            {   
                $card_data['resolved'] = self::case_nature($card_data['resolved'],$search_case_nature);
            } 
            if ($search_case_nature_type = $request->get('search_case_nature_type'))
            {   
                $card_data['resolved'] = self::case_nature_type($card_data['resolved'],$search_case_nature_type);
            } 
            if ($shipment_status = $request->get('search_shipment_status'))
            {   
                $card_data['resolved'] = self::shipment_status($card_data['resolved'],$shipment_status);
            }
            if ($avg_tat = $request->get('avg_tat'))
            {   
                $card_data['resolved'] = self::avg_tat($card_data['resolved'],$avg_tat);
            }      

            //Closed
            $card_data['closed'] = CrmRequest::where('crm_requests.status_id', 4);
            // whereBetween('created_at', [$thirtyDays, $today])->
            
            if($agent_id = $request->get('agent_id'))
            {
                $card_data['closed'] = self::agent($card_data['closed'],$agent_id);
            }
            
           

            if (($from = $request->get('from_date')) && ($to = $request->get('to_date'))) {
                $card_data['closed'] = self::dates($card_data['closed'], $from, $to);
            
                $card_feedback = CrmRequestFeedback::whereBetween('created_at', [$from, $to])->pluck('crm_request_id')->toArray();
            
                $card_total = $card_data['total']->filter(function ($item) use ($from, $to, $card_feedback) {
                    return $item->created_at >= $from && $item->created_at <= $to && !in_array($item->id, $card_feedback);
                })->count();
                //dd(  $card_data['closed']->count(),$card_total);
                $card_data['closed_rate'] = $card_total !== 0 ? $card_data['closed']->count() / $card_total : 0;
                //dd( $card_data['closed_rate']);
            } else {
                $thirtyDaysAgo = now()->subDays(30);
                $card_data['closed'] = self::dates($card_data['closed'], $thirtyDaysAgo, now());
            
                $card_feedback = CrmRequestFeedback::whereBetween('created_at', [$thirtyDaysAgo, now()])->pluck('crm_request_id')->toArray();
            
                $card_total = $card_data['total']->filter(function ($item) use ($thirtyDaysAgo, $card_feedback) {
                    return $item->created_at >= $thirtyDaysAgo && $item->created_at <= now() && !in_array($item->id, $card_feedback);
                })->count();
                $card_data['closed_rate'] = $card_total !== 0 ? $card_data['closed']->count() / $card_total : 0;
            }
            // if (($from = $request->get('from_date')) && ($to = $request->get('to_date')))
            // {   
            //     $card_data['closed'] = self::dates($card_data['closed'],$from,$to);

            //     $card_feedback = CrmRequestFeedback::
            //     whereBetween('created_at',[$from,$to])->
            //     pluck('crm_request_id');

            //     dd($card_data['total']->whereBetweem('created_at',[$from,$to])->count());
            //     $card_total = $card_data['total']->whereBetweem('created_at',[$from,$to])->whereNotIn('id', $card_feedback)->count();
            //     $card_data['closed_rate'] = $card_total !== 0 ?  $card_data['closed']->count()/$card_total : 0;
            // }
            // else
            // {
            //     $card_data['closed']->whereBetween('created_at', [$thirtyDays, $today]);
            // }
            if ($origin = $request->get('search_origin'))
            {   
                $card_data['closed'] = self::origin($card_data['closed'],$origin);
            }
            if ($destination = $request->get('search_destination'))
            {   
                $card_data['closed'] = self::destination($card_data['closed'],$destination);
            }
            if ($zone = $request->get('search_zone'))
            {   
                $card_data['closed']= self::zone($card_data['closed'],$zone);
            }
            if ($search_case_nature = $request->get('search_case_nature'))
            {   
                $card_data['closed'] = self::case_nature($card_data['closed'],$search_case_nature);
            }
            if ($search_case_nature_type = $request->get('search_case_nature_type'))
            {   
                $card_data['closed'] = self::case_nature_type($card_data['closed'],$search_case_nature_type);
            }    
            if ($shipment_status = $request->get('search_shipment_status'))
            {   
                $card_data['closed'] = self::shipment_status($card_data['closed'],$shipment_status);
            }
            if ($avg_tat = $request->get('avg_tat'))
            {   
                $card_data['closed'] = self::avg_tat($card_data['closed'],$avg_tat);
            }  

            //Valid
            $card_data['valid'] = CrmRequestStatusHistory::where('crm_request_status_histories.status_id', 6);
            // whereBetween('created_at', [$thirtyDays, $today])->
            if($agent_id = $request->get('agent_id'))
            {
                $card_data['valid'] = self::agent($card_data['valid'],$agent_id);
            }
            if (($from = $request->get('from_date')) && ($to = $request->get('to_date')))
            {   
                $card_data['valid'] = self::dates($card_data['valid'],$from,$to);
            }
            else
            {
                $card_data['valid']->whereBetween('created_at', [$thirtyDays, $today]);
            }
            if ($origin = $request->get('search_origin'))
            {   
                $card_data['valid'] = self::origin($card_data['valid'],$origin);
            }
            if ($destination = $request->get('search_destination'))
            {   
                $card_data['valid'] = self::destination($card_data['valid'],$destination);
            }
            if ($zone = $request->get('search_zone'))
            {   
                $card_data['valid']= self::zone($card_data['valid'],$zone);
            }
            if ($search_case_nature = $request->get('search_case_nature'))
            {   
                $card_data['valid'] = self::case_nature($card_data['valid'],$search_case_nature);
            }
            if ($search_case_nature_type = $request->get('search_case_nature_type'))
            {   
                $card_data['valid'] = self::case_nature_type($card_data['valid'],$search_case_nature_type);
            }    
            if ($shipment_status = $request->get('search_shipment_status'))
            {   
                $card_data['valid'] = self::shipment_status($card_data['valid'],$shipment_status);
            }
            if ($avg_tat = $request->get('avg_tat'))
            {   
                $card_data['valid'] = self::avg_tat($card_data['valid'],$avg_tat);
            }  

            //InValid
            $card_data['in_valid'] = CrmRequestStatusHistory::where('crm_request_status_histories.status_id', 7);
            // whereBetween('created_at', [$thirtyDays, $today])->
            if($agent_id = $request->get('agent_id'))
            {
                $card_data['in_valid'] = self::agent($card_data['in_valid'],$agent_id);
            }
            if (($from = $request->get('from_date')) && ($to = $request->get('to_date')))
            {   
                $card_data['in_valid'] = self::dates($card_data['in_valid'],$from,$to);
            }
            else
            {
                $card_data['in_valid']->whereBetween('created_at', [$thirtyDays, $today]);
            }
            if ($origin = $request->get('search_origin'))
            {   
                $card_data['in_valid'] = self::origin($card_data['in_valid'],$origin);
            }
            if ($destination = $request->get('search_destination'))
            {   
                $card_data['in_valid'] = self::destination($card_data['in_valid'],$destination);
            }
            if ($zone = $request->get('search_zone'))
            {   
                $card_data['in_valid'] = self::zone($card_data['in_valid'],$zone);
            }
            if ($search_case_nature = $request->get('search_case_nature'))
            {   
                $card_data['in_valid'] = self::case_nature($card_data['in_valid'],$search_case_nature);
            }
            if ($search_case_nature_type = $request->get('search_case_nature_type'))
            {   
               $card_data['in_valid'] = self::case_nature_type($card_data['in_valid'],$search_case_nature_type);
            }    
            if ($shipment_status = $request->get('search_shipment_status'))
            {   
                $card_data['in_valid'] = self::shipment_status($card_data['in_valid'],$shipment_status);
            } 
            if ($avg_tat = $request->get('avg_tat'))
            {   
                $card_data['in_valid'] = self::avg_tat($card_data['in_valid'],$avg_tat);
            } 

            $card_data['launched'] = $card_data['launched']->count();
            $card_data['in_process'] = $card_data['in_process']->count();
            $card_data['resolved'] = $card_data['resolved']->count();
            $card_data['closed'] = $card_data['closed']->count();
            $card_data['valid'] = $card_data['valid']->count();
            $card_data['in_valid'] = $card_data['in_valid']->count();
            $card_data['in_valid_percentage'] = "0";
            $card_data['closed_rate_percentage'] = "0";
            $card_data['in_process_ratio_percentage'] = "0";

            

            if (($from = $request->get('from_date')) && ($to = $request->get('to_date')))
            {   
                
                $today = Carbon::now()->endOfDay();
                $thirtyDays = Carbon::now()->subDays(30)->startOfDay();
                $stop_date = Carbon::createFromFormat('Y-m-d', $to)->endOfDay()->toDateTimeString();
                $card_feedback = CrmRequestFeedback::
                whereBetween('created_at',[$from,$to])->
                pluck('crm_request_id');
                $card_total = $card_data['total']->whereNotIn('id', $card_feedback)->count();
                $shipments = Shipment::where('shipper_status_id',2)->whereBetween('created_at', [$from, $stop_date])->count();
                

                $card_data['in_process_ratio'] = $shipments !== 0 ? $card_total/$shipments : 0;
                $card_data['total'] = $card_data['total']->count();
                if ($card_data['total'] > 0) {
                    // dd($card_data['closed_rate'],$card_total);
                    $card_data['in_valid_percentage'] = round(($card_data['in_valid'] / $card_data['total']) * 100, 2);
                    $card_data['closed_rate_percentage'] = $card_total !== 0
                    ? round(($card_data['closed_rate']) * 100, 2)
                    : 0;
                    $card_data['in_process_ratio_percentage'] = $shipments !== 0
                    ? round(($card_data['in_process_ratio']) * 100, 2)
                    : 0;
                }
            }
            

            $card_data['launched'] = number_format($card_data['launched']);
            $card_data['in_process'] = number_format($card_data['in_process']);
            $card_data['resolved'] = number_format($card_data['resolved']);
            $card_data['closed'] = number_format($card_data['closed']);
            $card_data['valid'] = number_format($card_data['valid']);
            $card_data['in_valid'] = number_format($card_data['in_valid']);
            $card_data['closed_rate'] = $card_data['closed_rate'];
            $card_data['in_process_ratio'] = number_format($card_data['in_process_ratio']);
            $card_data['in_process_ratio_percentage'] = $card_data['in_process_ratio_percentage'];

            return response()->json(['status' => 1, 'card_data' => $card_data]);
        // }else{
        //     return response()->json(['status' => 0]);
        // }
    }
    // Cards Filter Functions
    static function agent($query,$id)
    {
        return $query->where('agent_id',$id);
    }
    static function dates($query,$from,$to)
    {
        $stop_date = Carbon::createFromFormat('Y-m-d', $to)->endOfDay()->toDateTimeString();
        return $query->whereBetween('created_at', [$from, $stop_date]);
    }
    static function destination($query,$id)
    {
       return $query->leftjoin('shipments as s', 's.id', '=', 'crm_requests.shipment_id')
       ->leftjoin('cities as dc', 'dc.id', '=', 's.consignee_city_id')->where('dc.id', '=', $id);
    }
    static function origin($query,$id)
    {
       return $query->leftjoin('shipments as s', 's.id', '=', 'crm_requests.shipment_id')
       ->leftjoin('user_shipping_infos AS usi', 's.pickup_address_id', '=', 'usi.id')
       ->leftjoin('cities as oc', 'oc.id', '=', 'usi.city_id')->where('oc.id', '=', $id);
    }
    static function zone($query,$id)
    {
        return $query->leftjoin('shipments as s', 's.id', '=', 'crm_requests.shipment_id')
        ->leftjoin('cities as dc', 'dc.id', '=', 's.consignee_city_id')
        ->leftjoin('zones as z', 'z.id', '=', 'dc.zone_id')->where('z.id', '=', $id);
    }
    static function case_nature($query,$id)
    {
        return $query->leftjoin('crm_request_case_nature as crcn', 'crcn.id', '=', 'crm_requests.case_nature_id')
        ->where('crcn.id', '=', $id);
    }
    static function case_nature_type($query,$id)
    {
        return $query->leftjoin('crm_request_case_nature_types as crcnt', 'crcnt.id', '=', 'crm_requests.case_nature_type_id')
        ->where('crcnt.id', '=', $id);
    }
    static function shipment_status($query,$id)
    {
        return $query->leftjoin('shipments as s', 's.id', '=', 'crm_requests.shipment_id')
        ->where('s.shipper_status_id', '=', $id);
    }
    static function avg_tat($query,$id)
    {
        return $query->where('crm_requests.status_id', '=', $id);
    }

}
