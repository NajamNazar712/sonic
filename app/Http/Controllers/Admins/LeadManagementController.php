<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\Lead\Lead;
use App\Http\Models\Admin\Lead\LeadLog;
use App\Http\Models\Admin\Lead\LeadRemark;
use App\Http\Models\Admin\Lead\LeadStatus;
use App\Http\Models\Admin\Lead\PamLead;
use App\Http\Models\Admin\Lead\PamLeadItem;
use App\Http\Models\Admin\Territory;
use App\Http\Models\City;
use App\Models\Admin\Lead\LeadReason;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Lead\LeadNotification;
use App\Http\Models\Admin\Lead\LeadTagging;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\Datatables\Datatables;
use DB;

class LeadManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 4);
        $salesperson = Admin::join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.name', 'admins.id'])->where('status', 1)->where('ar.department_id', 7)->get();
        $statuses = LeadStatus::all();
        $lead_statuses = LeadStatus::whereNotIn('id', [1, 12])->get();
        $services = DB::table('service_list')->get();
        $today = Carbon::now()->endOfDay();
        $thirtyDays = Carbon::now()->subDays(58)->startOfDay();

        $leads['total'] = Lead::whereBetween('requested_date', [$thirtyDays, $today]);
        $leads['received'] = Lead::whereBetween('requested_date', [$thirtyDays, $today])->where('status_id', 1);
        $leads['in_process'] = Lead::whereIn('status_id', [2, 5, 6, 7, 8, 9])->whereBetween('requested_date', [$thirtyDays, $today]);
        $leads['in_process_for_activation'] = Lead::where('status_id', 9)->whereBetween('requested_date', [$thirtyDays, $today]);
        $leads['dead_leads'] = Lead::whereIn('status_id', [3, 4, 10, 11, 13])->whereBetween('requested_date', [$thirtyDays, $today]);
        $leads['accounts_activated'] = Lead::where('status_id', 12)->whereBetween('requested_date', [$thirtyDays, $today]);
        $leads['dormant'] = Lead::where('status_id', 14)->whereBetween('requested_date',[$thirtyDays,$today]);

        if (session('role_id') != 1) {
            $leads['total'] = $leads['total']->join('cities as c', 'c.id', '=', 'leads.city_id')->whereIn('c.hub_id', session('hubs'));
            $leads['received'] = $leads['received']->join('cities as c', 'c.id', '=', 'leads.city_id')->whereIn('c.hub_id', session('hubs'));
            $leads['in_process'] = $leads['in_process']->join('cities as c', 'c.id', '=', 'leads.city_id')->whereIn('c.hub_id', session('hubs'));
            $leads['in_process_for_activation'] = $leads['in_process_for_activation']->join('cities as c', 'c.id', '=', 'leads.city_id')->whereIn('c.hub_id', session('hubs'));
            $leads['dead_leads'] = $leads['dead_leads']->join('cities as c', 'c.id', '=', 'leads.city_id')->whereIn('c.hub_id', session('hubs'));
            $leads['accounts_activated'] = $leads['accounts_activated']->join('cities as c', 'c.id', '=', 'leads.city_id')->whereIn('c.hub_id', session('hubs'));
            $leads['dormant'] = $leads['dormant']->join('cities as c', 'c.id', '=', 'leads.city_id')->whereIn('c.hub_id', session('hubs'));
        }
        if (session('department_id') == 7) {
            if (!in_array(session('id'), session('sale_users_bypass')) && session('role_id') != 44 && session('role_id') != 60) {
                $leads['total'] = $leads['total']->where('leads.sale_person_id', Auth::id());
                $leads['received'] = $leads['received']->where('leads.sale_person_id', Auth::id());
                $leads['in_process'] = $leads['in_process']->where('leads.sale_person_id', Auth::id());
                $leads['in_process_for_activation'] = $leads['in_process_for_activation']->where('leads.sale_person_id', Auth::id());
                $leads['dead_leads'] = $leads['dead_leads']->where('leads.sale_person_id', Auth::id());
                $leads['accounts_activated'] = $leads['accounts_activated']->where('leads.sale_person_id', Auth::id());
                $leads['dormant'] = $leads['dormant']->where('leads.sale_person_id', Auth::id());
            }
        }

        $ratio_leads = $leads['total'];
        if ($ratio_leads->exists()) {
            $ratio_leads = $ratio_leads->get();
            $dead_days = 0;
            $dead_count = 0;
            $active_days = 0;
            $active_count = 0;
            foreach ($ratio_leads as $ratio_lead) {
                if (in_array($ratio_lead->status_id, [3, 4, 10, 11, 13])) {
                    $last_log = LeadLog::where('lead_id', $ratio_lead->id)->whereIn('status_id', [3, 4, 10, 11, 13])->orderBy('id', 'DESC');
                    if ($last_log->exists()) {
                        $last_log = $last_log->first();
                        $last_date = Carbon::parse($last_log->created_at);
                        $dead_days = $dead_days + $last_date->diffInDays($ratio_lead->requested_date);
                    }
                    $dead_count++;
                }
                if (in_array($ratio_lead->status_id, [12])) {
                    $last_log = LeadLog::where('lead_id', $ratio_lead->id)->whereIn('status_id', [12])->orderBy('id', 'DESC');
                    if ($last_log->exists()) {
                        $last_log = $last_log->first();
                        $last_date = Carbon::parse($last_log->created_at);
                        $active_days = $active_days + $last_date->diffInDays($ratio_lead->requested_date);
                    }
                    $active_count++;
                }
            }
            if ($dead_count > 0) {
                $leads['dead_leads_ratio'] = round($dead_days / $dead_count, 2);
            } else {
                $leads['dead_leads_ratio'] = 0;
            }

            if ($active_count > 0) {
                $leads['active_leads_ratio'] = round($active_days / $active_count, 2);
            } else {
                $leads['active_leads_ratio'] = 0;
            }
        } else {
            $leads['dead_leads_ratio'] = 0;
            $leads['active_leads_ratio'] = 0;
        }


        $leads['total'] = $leads['total']->count();
        $leads['received'] = $leads['received']->count();
        $leads['in_process'] = $leads['in_process']->count();
        $leads['in_process_for_activation'] = $leads['in_process_for_activation']->count();
        $leads['dead_leads'] = $leads['dead_leads']->count();
        $leads['accounts_activated'] = $leads['accounts_activated']->count();
        $leads['dormant'] = $leads['dormant']->count();

        $leads['received_percentage'] = 0;
        $leads['in_process_percentage'] = 0;
        $leads['in_process_for_activation_percentage'] = 0;
        $leads['dead_leads_percentage'] = 0;
        $leads['accounts_activated_percentage'] = 0;
        if ($leads['total'] > 0) {
            $leads['received_percentage'] = round(($leads['received'] / $leads['total']) * 100, 2);
            $leads['in_process_percentage'] = round(($leads['in_process'] / $leads['total']) * 100, 2);
            $leads['in_process_for_activation_percentage'] = round(($leads['in_process_for_activation'] / $leads['total']) * 100, 2);
            $leads['dead_leads_percentage'] = round(($leads['dead_leads'] / $leads['total']) * 100, 2);
            $leads['accounts_activated_percentage'] = round(($leads['accounts_activated'] / $leads['total']) * 100, 2);
        }

        $leads['total'] = number_format($leads['total']);
        $leads['received'] = number_format($leads['received']);
        $leads['in_process'] = number_format($leads['in_process']);
        $leads['in_process_for_activation'] = number_format($leads['in_process_for_activation']);
        $leads['dead_leads'] = number_format($leads['dead_leads']);
        $leads['accounts_activated'] = number_format($leads['accounts_activated']);
        $leads['dormant'] = number_format($leads['dormant']);

        $cities = City::select('id', 'name')->get();

        $dates['current'] = Carbon::now();
        $dates['old_date'] = Carbon::now()->subDays(58);
        return view('admin.leads.index')->with(['sale_name' => $salesperson, 'services' => $services, 'statuses' => $statuses, 'lead_statuses' => $lead_statuses, 'leads' => $leads, 'cities' => $cities, 'dates' => $dates]);
    }

    public function list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 64);
        }
        $leads = Lead::join('cities as c', 'c.id', '=', 'leads.city_id')
            ->leftjoin('territories as t', 't.id', '=', 'leads.territory_id')
            ->leftjoin('area_territories as at', 'at.id', '=', 'leads.territory_area_id')
            ->leftjoin('admins as sp', 'sp.id', '=', 'leads.sale_person_id')
            ->leftjoin('admins as rp', 'rp.id', '=', 'leads.reference_person_id')
            ->leftjoin('lead_statuses as ls', 'ls.id', '=', 'leads.status_id')
            ->leftjoin('lead_references as lr', 'lr.id', '=', 'leads.reference_id')
            ->leftjoin('admins as ub', 'ub.id', '=', 'leads.updated_by')
            ->leftjoin('service_list as sl', 'sl.id', '=', 'leads.service_id')
            ->leftjoin('lead_reasons as lsr', 'lsr.id', '=', 'leads.reason')
            ->select('leads.id as lead_id', 'leads.id as leadid', 'leads.contact_person', 'leads.phone_number', 'leads.email_address', 'leads.requested_date', 'leads.message', 'leads.status_id', 'ls.name as status', 'ub.name as updated_by', 'sp.name as sale_person', 'rp.name as reference_person', 'c.name as city', 't.name as territory', 'at.name as area', 'leads.sale_person_updated_at', 'lr.name as lead_reference', 'leads.updated_at', 'sl.name as service', 'leads.brand as brand', 'leads.company as company', 'lsr.name as reason_id');

        if (session('role_id') != 1) {
            $leads = $leads->whereIn('c.hub_id', session('hubs'));
        }
        if (session('department_id') == 7) {
            if (!in_array(session('id'), session('sale_users_bypass')) && session('role_id') != 44 && session('role_id') != 60) {
                $leads = $leads->where('leads.sale_person_id', Auth::id());
            }
        }

        if ($origin = $request->get('search_origin')) {
            $leads->where('leads.city_id', '=', $origin);
        }
        if ($sale_person = $request->get('search_sale_person')) {
            $leads->where('leads.sale_person_id', '=', $sale_person);
        }
        if ($statistics = $request->get('search_statistics')) {
            if ($statistics == 1) {
                $search_statuses = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14];
            } elseif ($statistics == 2) {
                $search_statuses = [1];
            } elseif ($statistics == 3) {
                $search_statuses = [2, 5, 6, 7, 8, 9];
            } elseif ($statistics == 4) {
                $search_statuses = [3, 4, 10, 11, 13];
            } elseif ($statistics == 5) {
                $search_statuses = [12];
            } elseif ($statistics == 6) {
                $search_statuses = [9];
            } elseif ($statistics == 7) {
                $search_statuses = [14];
            } else {
                $search_statuses = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14];
            }
            $leads->whereIn('leads.status_id', $search_statuses);
        }
        /*if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $leads->whereBetween('leads.requested_date', [$from, $to]);
        }*/
        return Datatables::of($leads)
            ->filterColumn('status', function ($query, $keyword) {
                if ($keyword != '') {
                    $query->where('leads.status_id', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn('aging', function ($lead) {
                $days = Carbon::now()->diffInDays($lead->requested_date);
                if ($days == 0) {
                    return "-";
                } else {
                    return $days;
                }
            })
            ->editColumn('reason_id', function ($lead) {
                if ($lead->reason_id) {
                    return $lead->reason_id;
                } else {
                    return "-";
                }
            })
            ->addColumn('lead_id_link', function ($lead) {
                $route = route('admin.leads.view_remarks', ['id' => $lead->lead_id]);
                return "<u><a href='{$route}\' class='leads' target='_blank'>" . str_pad($lead->lead_id, 3, '0', STR_PAD_LEFT) . "</a></u>";
                //return $lead->lead_id;
            })
            ->addColumn('action', function ($lead) {
                $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';
                if ((session('role_id') == 1 || in_array(419, session('permissions'))) && $lead->status_id != 12) {
                    $dropdown .= '<button type="button"  class="dropdown-item update" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Update</div></button>';
                }

                if (session('role_id') == 1 || in_array(420, session('permissions'))) {
                    $dropdown .= '<button type="button"  class="dropdown-item forward_lead" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Forward Lead</div></button>';
                }

//                $dropdown .= '<button type="button"  class="dropdown-item add_remarks" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Add Remarks</div></button>';
                $dropdown .= '<button onclick="window.open(\'' . route('admin.leads.view_remarks', ['id' => $lead->lead_id]) . '\')" type="button" class="dropdown-item view_remarks" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Remarks</div></button>';

                if ((session('role_id') == 1 || in_array(696, session('permissions')))) {
                    $dropdown .= '<button type="button"  class="dropdown-item edit" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';
                }

                return $dropdown;
            })->make(true);
    }

    public function pam_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 489);
        return view('admin.leads.pam_index');
    }

    public function pam_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 490);
        }

        $leads = PamLead::leftjoin('cities as o', 'o.id', '=', 'pam_leads.origin_id')
            ->leftjoin('cities as d', 'd.id', '=', 'pam_leads.destination_id')
            ->select(['pam_leads.created_at as lead_created_at','pam_leads.id as id', 'pam_leads.lead_id as lead_id', 'pam_leads.name as name', 'pam_leads.phone as phone', 'pam_leads.location_type as category', 'pam_leads.case_type as case', 'pam_leads.video_link as video_link', 'pam_leads.images as images', 'o.name as origin', 'd.name as destination', DB::raw('(select count(id) from pam_lead_items as pli where pli.lead_id = pam_leads.id) as item_count')]);

        if (session('role_id') != 1) {
            $leads = $leads->whereIn('o.hub_id', session('hubs'));
        }

        return Datatables::of($leads)
            ->editColumn('category', function ($lead) {
                if ($lead->category == 1) {
                    return "Home Shifting";
                }

                return "Office Shifting";
            })
            ->editColumn('case', function ($lead) {
                if ($lead->case == 1) {
                    return "Both";
                } else if ($lead->case == 2) {
                    return "Packing";
                } else {
                    return "Unpacking";
                }
            })
            ->addColumn('video_link_btn', function ($lead) {
                if ($lead->video_link != null) {
                    return '<a target="_blank" class="btn btn-sm btn-outline-info align-middle" href="' . $lead->video_link . '"><i class="la la-lg la-file-video-o align-middle"></i> <span class="align-middle">View Video</span></a>';
                }

                return "-";
            })
            ->addColumn('images_link_btn', function ($lead) {
                if ($lead->images != null) {
                    $images = explode('|', $lead->images);
                    $html = "";
                    foreach ($images as $image)
                    {
                        $exists = Storage::disk('public')->exists($image);
                        if ($exists) {
                            $route = Storage::disk('public')->url($image);
                        }
                        else{
                            $route = Storage::disk('s3')->temporaryUrl($image, now()->addMinutes(5));
                        }
                        $html .= '<a target="_blank" class="btn btn-sm btn-outline-info align-middle" href="' . $route . '"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View Image</span></a><br>';
                    }
                    return $html;
                }
                return "-";
            })
            ->addColumn('images_links', function ($lead) {
                if ($lead->images != null) {
                    $images = explode('|', $lead->images);
                    $html = "";
                    foreach ($images as $image) {
                        $html .= $image . "  ";
                    }
                    return $html;
                }
                return "-";
            })
            ->addColumn('item_count_button', function ($lead) {
                return "<button class='btn btn-sm btn-outline-info align-middle show_lead_items'>" . $lead->item_count . "</button>";
            })
            ->make(true);
    }

    public function pam_items(Request $request)
    {
        if (!$request->has('id')) {
            return response()->json(['status' => 0, 'error' => 'Lead ID is required']);
        }

        $items = PamLeadItem::where('lead_id', $request->id);

        if ($items->doesntExist()) {
            return response()->json(['status' => 0, 'error' => 'Invalid Lead ID']);
        }

        $items = $items->leftjoin('pam_items as pi', 'pi.id', 'pam_lead_items.item_id')
            ->select(['pam_lead_items.*', 'pi.name as item_name'])
            ->get();

        return response()->json(['status' => 1, 'items' => $items]);
    }

    public function lead_statistics(Request $request)
    {
        $from = $request->search_date_from;
        $to = $request->search_date_to;

        $leads['total'] = Lead::whereBetween('requested_date', [$from, $to]);
        $leads['received'] = Lead::whereBetween('requested_date', [$from, $to])->where('status_id', 1);
        $leads['in_process'] = Lead::whereIn('status_id', [2, 5, 6, 7, 8, 9])->whereBetween('requested_date', [$from, $to]);
        $leads['dead_leads'] = Lead::whereIn('status_id', [3, 4, 10, 11, 13])->whereBetween('requested_date', [$from, $to]);
        $leads['accounts_activated'] = Lead::where('status_id', 12)->whereBetween('requested_date', [$from, $to]);
        $leads['dormant'] = Lead::where('status_id', 14)->whereBetween('requested_date', [$from, $to]);

        if ($origin = $request->get('search_origin')) {
            $leads['total'] = $leads['total']->where('city_id', $origin);
            $leads['received'] = $leads['received']->where('city_id', $origin);
            $leads['in_process'] = $leads['in_process']->where('city_id', $origin);
            $leads['dead_leads'] = $leads['dead_leads']->where('city_id', $origin);
            $leads['accounts_activated'] = $leads['accounts_activated']->where('city_id', $origin);
            $leads['dormant'] = $leads['dormant']->where('city_id', $origin);
        }
        if ($sale_person = $request->get('search_sale_person')) {
            $leads['total'] = $leads['total']->where('sale_person_id', $sale_person);
            $leads['received'] = $leads['received']->where('sale_person_id', $sale_person);
            $leads['in_process'] = $leads['in_process']->where('sale_person_id', $sale_person);
            $leads['dead_leads'] = $leads['dead_leads']->where('sale_person_id', $sale_person);
            $leads['accounts_activated'] = $leads['accounts_activated']->where('sale_person_id', $sale_person);
            $leads['dormant'] = $leads['dormant']->where('sale_person_id', $sale_person);
        }
        if (session('role_id') != 1) {
            $leads['total'] = $leads['total']->join('cities as c', 'c.id', '=', 'leads.city_id')->whereIn('c.hub_id', session('hubs'));
            $leads['received'] = $leads['received']->join('cities as c', 'c.id', '=', 'leads.city_id')->whereIn('c.hub_id', session('hubs'));
            $leads['in_process'] = $leads['in_process']->join('cities as c', 'c.id', '=', 'leads.city_id')->whereIn('c.hub_id', session('hubs'));
            $leads['dead_leads'] = $leads['dead_leads']->join('cities as c', 'c.id', '=', 'leads.city_id')->whereIn('c.hub_id', session('hubs'));
            $leads['accounts_activated'] = $leads['accounts_activated']->join('cities as c', 'c.id', '=', 'leads.city_id')->whereIn('c.hub_id', session('hubs'));
            $leads['dormant'] = $leads['dormant']->join('cities as c', 'c.id', '=', 'leads.city_id')->whereIn('c.hub_id', session('hubs'));
        }
        if (session('department_id') == 7) {
            if (!in_array(session('id'), session('sale_users_bypass'))) {
                $leads['total'] = $leads['total']->where('leads.sale_person_id', Auth::id());
                $leads['received'] = $leads['received']->where('leads.sale_person_id', Auth::id());
                $leads['in_process'] = $leads['in_process']->where('leads.sale_person_id', Auth::id());
                $leads['dead_leads'] = $leads['dead_leads']->where('leads.sale_person_id', Auth::id());
                $leads['accounts_activated'] = $leads['accounts_activated']->where('leads.sale_person_id', Auth::id());
                $leads['dormant'] = $leads['dormant']->where('leads.sale_person_id', Auth::id());
            }
        }

        $ratio_leads = $leads['total'];
        if ($ratio_leads->exists()) {
            $ratio_leads = $ratio_leads->get();
            $dead_days = 0;
            $dead_count = 0;
            $active_days = 0;
            $active_count = 0;
            foreach ($ratio_leads as $ratio_lead) {
                if (in_array($ratio_lead->status_id, [3, 4, 10, 11, 13])) {
                    $last_log = LeadLog::where('lead_id', $ratio_lead->id)->whereIn('status_id', [3, 4, 10, 11, 13])->orderBy('id', 'DESC');
                    if ($last_log->exists()) {
                        $last_log = $last_log->first();
                        $last_date = Carbon::parse($last_log->created_at);
                        $dead_days = $dead_days + $last_date->diffInDays($ratio_lead->requested_date);
                    }
                    $dead_count++;
                }
                if (in_array($ratio_lead->status_id, [12])) {
                    $last_log = LeadLog::where('lead_id', $ratio_lead->id)->whereIn('status_id', [12])->orderBy('id', 'DESC');
                    if ($last_log->exists()) {
                        $last_log = $last_log->first();
                        $last_date = Carbon::parse($last_log->created_at);
                        $active_days = $active_days + $last_date->diffInDays($ratio_lead->requested_date);
                    }
                    $active_count++;
                }
            }
            if ($dead_count > 0) {
                $leads['dead_leads_ratio'] = round($dead_days / $dead_count, 2);
            } else {
                $leads['dead_leads_ratio'] = 0;
            }

            if ($active_count > 0) {
                $leads['active_leads_ratio'] = round($active_days / $active_count, 2);
            } else {
                $leads['active_leads_ratio'] = 0;
            }
        } else {
            $leads['dead_leads_ratio'] = 0;
            $leads['active_leads_ratio'] = 0;
        }


        $leads['total'] = $leads['total']->count();
        $leads['received'] = $leads['received']->count();
        $leads['in_process'] = $leads['in_process']->count();
        $leads['dead_leads'] = $leads['dead_leads']->count();
        $leads['accounts_activated'] = $leads['accounts_activated']->count();
        $leads['dormant'] = $leads['dormant']->count();

        $leads['received_percentage'] = 0;
        $leads['in_process_percentage'] = 0;
        $leads['dead_leads_percentage'] = 0;
        $leads['accounts_activated_percentage'] = 0;
        if ($leads['total'] > 0) {
            if (is_numeric($leads['received'])) {
                $leads['received_percentage'] = round(($leads['received'] / $leads['total']) * 100, 2);
            }
            if (is_numeric($leads['in_process'])) {
                $leads['in_process_percentage'] = round(($leads['in_process'] / $leads['total']) * 100, 2);
            }
            if (is_numeric($leads['dead_leads'])) {
                $leads['dead_leads_percentage'] = round(($leads['dead_leads'] / $leads['total']) * 100, 2);
            }
            if (is_numeric($leads['accounts_activated'])) {
                $leads['accounts_activated_percentage'] = round(($leads['accounts_activated'] / $leads['total']) * 100, 2);
            }
        }

        $leads['total'] = number_format($leads['total']);
        $leads['received'] = number_format($leads['received']);
        $leads['in_process'] = number_format($leads['in_process']);
        $leads['dead_leads'] = number_format($leads['dead_leads']);
        $leads['accounts_activated'] = number_format($leads['accounts_activated']);
        $leads['dormant'] = number_format($leads['dormant']);

        return response()->json(['status' => 1, 'leads' => $leads]);
    }

    public function add_status(Request $request)
    {
        $lead_id = $request->lead_id;
        if ($request->reason)
            $reason = $request->reason;
        else
            $reason = NULL;

        $lead = Lead::find($lead_id);
        $status = $request->status;

        if ($status != NULL) {
            if ($lead) {
                if ($lead->sale_person_id != null) {
                    $lead_log = new LeadLog();
                    $lead_log->lead_id = $lead->id;
                    $lead_log->prev_status_id = $lead->status_id;
                    $lead_log->status_id = $status;
                    $lead_log->reason = $reason;
                    $lead_log->sale_person_id = $lead->sale_person_id;
                    if ($lead->reference_person_id == NULL) {
                        $lead_log->reference_person_id = Auth::id();
                    } else {
                        $lead_log->reference_person_id = $lead->reference_person_id;
                    }
                    $lead_log->updated_by = Auth::id();
                    $lead_log->save();

                    $lead->status_id = $status;
                    $lead->reason = $reason;
                    $lead->updated_by = Auth::id();
                    $lead->save();

                    if ($status == 9) {
                        NotificationsController::send(113, $lead);
                    } elseif ($status == 2) {
                        LeadTaggingController::notification_unresponsive($lead->id);
                    }

                    return response()->json(['status' => 1, 'success' => 'Status updated Successfully!']);
                } else {
                    return response()->json(['status' => 0, 'error' => 'Sale Person Not Selected!']);
                }
            } else {
                return response()->json(['status' => 0, 'error' => 'Lead not found!']);
            }
        } else {
            return response()->json(['status' => 0, 'error' => 'Invalid Status!']);
        }
    }

    public function add_bulk_status(Request $request)
    {
        $lead_ids = $request->lead_id;
        if ($request->reason)
            $reason = $request->reason;
        else
            $reason = NULL;
        $status = $request->status;

        if ($status != Null) {
            foreach ($lead_ids as $lead) {
                $lead = Lead::find($lead);
                if ($lead) {
                    if ($lead->sale_person_id != null) {
                        $lead_log = new LeadLog();
                        $lead_log->lead_id = $lead->id;
                        $lead_log->prev_status_id = $lead->status_id;
                        $lead_log->status_id = $status;
                        $lead_log->reason = $reason;
                        $lead_log->sale_person_id = $lead->sale_person_id;
                        if ($lead->reference_person_id == NULL) {
                            $lead_log->reference_person_id = Auth::id();
                        } else {
                            $lead_log->reference_person_id = $lead->reference_person_id;
                        }
                        $lead_log->updated_by = Auth::id();
                        $lead_log->save();
                            $lead->status_id = $status;
                            $lead->reason = $reason;
                            $lead->updated_by = Auth::id();
                            $lead->save();

                        if ($status == 9) {
                            NotificationsController::send(113, $lead);
                        } elseif ($status == 2) {
                            LeadTaggingController::notification_unresponsive($lead->id);
                        }

//                        return response()->json(['status' => 1, 'success' => 'Status updated Successfully!']);
                    } else {
                        return response()->json(['status' => 0, 'error' => 'Sale Person Not Selected!']);
                    }
                } else {
                    return response()->json(['status' => 0, 'error' => 'Lead not found!']);
                }
//

            }
            return response()->json(['status' => 1, 'success' => 'Status updated Successfully!']);
        } else {
            return response()->json(['status' => 0, 'error' => 'Invalid Status!']);
        }
    }

    public function add_remarks(Request $request)
    {
        $lead_id = $request->lead_id;
        $lead = Lead::find($lead_id);
        $remarks = $request->remarks;
        if ($remarks != NULL) {
            $lead_remarks = new LeadRemark();
            $lead_remarks->lead_id = $lead->id;
            $lead_remarks->remarks = $remarks;
            $lead_remarks->updated_by = Auth::id();
            $lead_remarks->save();
            NotificationsController::app_notification(15, $lead->sale_person_id, 1, $lead_id);
            return response()->json(['status' => 1, 'success' => 'Remarks added Successfully!']);
        } else {
            return response()->json(['status' => 0, 'error' => 'Invalid Remarks!']);
        }
    }

    public function view_remarks_index($id)
    {
        $lead_id = $id;
        $lead = Lead::find($lead_id);
        if ($lead) {
            $lead_remarks = LeadRemark::where('lead_id', $lead_id);
            $details = array();
            if ($lead_remarks->exists()) {
                $lead_remarks = $lead_remarks->get();
                foreach ($lead_remarks as $remark) {
                    $details[] = $remark;
                }
            }
            $log_history = array();
            $lead_logs = LeadLog::where('lead_id', $lead_id);
            if ($lead_logs->exists()) {
                $lead_logs = $lead_logs->get();

                foreach ($lead_logs as $log) {
                    $detail['lead_id'] = str_pad($lead->id, 3, '0', STR_PAD_LEFT);
                    $detail['contact_person'] = $lead->contact_person;
                    $detail['phone_number'] = $lead->phone_number;
                    if ($log->sale_person_id != null) {
                        $detail['sales_person'] = $log->sales_person->name;
                    } else {
                        $detail['sales_person'] = '-';
                    }
                    if ($log->reference_person_id != null) {
                        $detail['reference_person'] = $log->reference_person->name;
                    } else {
                        $detail['reference_person'] = '-';
                    }
                    $detail['status'] = $log->status->name;
                    if ($log->updated_by == 7) {
                        $detail['updated_by'] = 'Trax.pk';
                    } else {
                        $detail['updated_by'] = $log->admin->name;
                    }
                    $detail['updated_at'] = Carbon::parse($log->updated_at)->toDateTimeString();

                    $log_history[] = $detail;
                }
            }

            return view('admin.leads.remarks')->with(['lead' => $lead, 'details' => $details, 'log_history' => $log_history]);
        } else {
            return redirect()->back()->with('error', 'Lead not found!');
        }

    }

    public function tag_sale_person_forward_lead(Request $request)
    {
        $lead_ids = $request->lead_ids;
        $sale_person = $request->sale_person;
        if ($request->has('reference_person')) {
            $reference_person = $request->reference_person;
        } else {
            $reference_person = null;
        }
        $leads = Lead::whereIn('id', $lead_ids);
        if ($leads->exists()) {
            $leads = $leads->get();
            foreach ($leads as $lead) {
                //autotagging
                $lead_tagging = LeadTagging::where('sale_person_id', $lead->sale_person_id);
                if ($lead_tagging->exists()) {
                    $lead_tagging = $lead_tagging->get()->first();
                    if ($lead_tagging->count > 0) {

                        $lead_tagging->count = $lead_tagging->count - 1;
                    }
                }
                //autotagging end
                $lead->sale_person_id = $sale_person;
                if ($request->has('reference_person')) {
                    $lead->reference_person_id = $reference_person;
                }
                $lead->updated_by = Auth::id();
                $lead->sale_person_updated_at = Carbon::now();
                $lead->save();
                NotificationsController::app_notification(14, $lead->sale_person_id, 1, $lead->id);
            }
            NotificationsController::send(204, $leads, $sale_person);
            return response()->json(['status' => 1, 'success' => 'Lead(s) Updated Successfully!']);
        } else {
            return response()->json(['status' => 0, 'error' => 'Lead(s) Does\'nt exist!']);
        }
    }

    public function upload_attachment(Request $request)
    {
        if ($request->hasFile('upload_attachment')) {
            $lead = Lead::find($request->lead_id);
            $filename = 'lead_attachment_' . $lead->id . '.png';

            $file = $request->file('upload_attachment');

            Storage::disk('public')->putFileAs('leads\attachment', $file, $filename);

            $lead->attachment = $filename;
            $lead->save();
            return redirect()->back()->with('success', 'Image Uploaded Successfully');
        } else {
            return redirect()->back()->with('error', 'Incomplete Information!');
        }
    }

    public function view_attachment($id)
    {
        $lead = Lead::find($id);
        $file = $lead->attachment;
        $url = Storage::url('leads/attachment/' . $file);
        return view('admin.leads.attachment_view')->with(['url' => $url]);
    }

    public function lead_reasons(Request $request){
        $lead_reason = LeadReason::join('lead_status_reasons as lsr', 'lsr.reason_id', 'lead_reasons.id')
            ->select('lead_reasons.id as id', 'lead_reasons.name as name')
            ->where('lsr.status_id', $request->status_id);
        if($lead_reason->exists()) {
            $lead_reason = $lead_reason->get();
            return response()->json(['status' => 1, 'lead_reasons' => $lead_reason]);
        }else{
            return response()->json(['status' => 0, 'error' => 'Reason not found!']);
        }
    }

    public function info(Request $request){
        $lead_id = $request->lead_id;

        $lead = Lead::find($lead_id);
        if($lead_id){
            $details = array();
            $details['city'] = '';
            $details['territory'] = '';
            $details['area'] = '';
            if($lead->city_id){
                $details['city'] = $lead->city->name;
            }

            if($lead->territory_id){
                $details['territory'] = Territory::find($lead->territory_id)->name;
            }
            if($lead->terrirory_area_id){
                $details['area'] = Territory::find($lead->terrirory_area_id)->name;
            }

            $details['phone_number'] = $lead->phone_number;
            $details['email_address'] = $lead->email_address;
            $details['brand'] = $lead->brand;
            $details['company'] = $lead->company;
            return response()->json(['status' => 0, 'details' => $details]);
        }
        else{
           return response()->json(['status' => 1, 'error' => 'Invalid Lead ID!']);
        }
    }

    public function edit(Request $request){

        $lead_id = $request->edit_lead_id;
        if($lead_id){
            $lead = Lead::find($lead_id);
            if($lead){
                $lead->city_id = $request->city_id;
                $lead->territory_id = $request->territory_id;
                $lead->territory_area_id = $request->territory_area_id;
                $lead->phone_number = $request->phone_number;
                $lead->email_address = $request->email_address;
                $lead->brand = $request->brand;
                $lead->company = $request->company;
                $lead->status_id = 15;
                $lead->save();

                LeadTaggingController::auto_tagging($lead->id,386);
                return redirect()->back()->with('success', 'Lead Edited successfully!');
            }
            return redirect()->back()->with('error', 'Lead not found!');
        }
        return redirect()->back()->with('error', 'Something went wrong!');
    }
}