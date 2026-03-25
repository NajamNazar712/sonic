<?php

namespace App\Http\Controllers\Admins;

use DB;
use Auth;
use Exception;
use Carbon\Carbon;
use App\Http\Models\City;
use Illuminate\Support\Str;
use App\LeadProgressSetting;
use Illuminate\Http\Request;
use App\Http\Models\Admin\Admin;
use App\Http\Models\ServiceList;
use Yajra\Datatables\Datatables;
use App\Http\Models\Shipper\User;
use App\Http\Models\WeightCharge;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Lead\Lead;
use App\Http\Models\Admin\Territory;
use App\Models\Admin\Lead\LeadReason;
use App\Http\Models\Admin\Lead\LeadLog;
use App\Http\Models\Admin\Lead\PamLead;
use Illuminate\Support\Facades\Storage;
use App\Http\Models\Admin\AreaTerritory;
use App\Http\Models\Admin\LeadReference;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\Admin\Lead\LeadRemark;
use App\Http\Models\Admin\Lead\LeadStatus;
use App\Http\Models\Admin\Lead\LeadTagging;
use App\Http\Models\Admin\Lead\PamLeadItem;
use App\Http\Models\Admin\Lead\EditLeadLogs;
use App\Models\Admin\Lead\LeadCallStatusLog;
use App\Http\Models\Admin\Lead\LeadNotification;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\Admins\ActivityTrailController;

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
        $lead_statuses = LeadStatus::whereNotIn('id', [1, 12, 14, 4, 10, 5, 7])->get();
        $services = DB::table('service_list')->get();
        $today = Carbon::now()->endOfDay();
        $thirtyDays = Carbon::now()->subDays(58)->startOfDay();

        $leads['total'] = Lead::whereBetween('requested_date', [$thirtyDays, $today]);
        $leads['received'] = Lead::whereBetween('requested_date', [$thirtyDays, $today])->where('status_id', 1);
        $leads['in_process'] = Lead::whereIn('status_id', [2, 5, 6, 7, 8])->whereBetween('requested_date', [$thirtyDays, $today]);
        $leads['in_process_for_activation'] = Lead::where('status_id', 9)->whereBetween('requested_date', [$thirtyDays, $today]);
        $leads['dead_leads'] = Lead::whereIn('status_id', [3, 4, 10, 11, 13])->whereBetween('requested_date', [$thirtyDays, $today]);
        $leads['accounts_activated'] = Lead::where('status_id', 12)->whereBetween('requested_date', [$thirtyDays, $today]);
        $leads['dormant'] = Lead::where('status_id', 14)->whereBetween('requested_date', [$thirtyDays, $today]);

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

        $cities = City::where('status', 1)->select('id', 'name')->get();

        $dates['current'] = Carbon::now();
        $dates['old_date'] = Carbon::now()->subDays(58);
        $lead_references = LeadReference::select('id', 'name')->get();

        return view('admin.leads.index')->with(['sale_name' => $salesperson, 'services' => $services, 'statuses' => $statuses, 'lead_statuses' => $lead_statuses, 'leads' => $leads, 'cities' => $cities, 'dates' => $dates, 'lead_references' => $lead_references]);
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
            ->leftjoin('riders as rp', 'rp.id', '=', 'leads.reference_person_id')
            ->leftjoin('lead_statuses as ls', 'ls.id', '=', 'leads.status_id')

            ->leftjoin('users as u', 'u.lead_id', '=', 'leads.id')

            ->leftjoin('lead_references as lr', 'lr.id', '=', 'leads.reference_id')
            ->leftjoin('admins as ub', 'ub.id', '=', 'leads.updated_by')
            ->leftjoin('service_list as sl', 'sl.id', '=', 'leads.service_id')
            ->leftjoin('lead_reasons as lsr', 'lsr.id', '=', 'leads.reason')
            ->select('leads.id as lead_id', 'leads.id as leadid', 'leads.contact_person', 'leads.phone_number', 'leads.email_address', 'leads.requested_date', 'leads.message', 'leads.status_id', 'ls.name as status', 'ub.name as updated_by', 'sp.name as sale_person', 'rp.name as reference_person', 'rp.trax_id as rider_id', 'c.name as city', 't.name as territory', 'at.name as area', 'leads.sale_person_updated_at', 'lr.name as lead_reference', 'leads.updated_at as updated', 'sl.name as service', 'leads.brand as brand', 'leads.company as company', 'lsr.name as reason_id', 'leads.sale_person_updated_at as sale_person_tagged_time', 'leads.call_status as call_status_name', 'leads.expected_shipments as expected_shipments', 'u.brand_name as brand_name', 'leads.via_channel as via_channel', 'u.status as user_status', 'u.id as user_id', 'leads.activation_code as activation_code', 'u.blacklist as blacklist', 'u.status as user_status', 'lr.id as lead_reference_id');
        // ->OrderByDesc('leads.requested_date');
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
                $search_statuses = [2, 5, 6, 7, 8];
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
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $leads->whereBetween('leads.requested_date', [$from, $to]);
        }
        return Datatables::of($leads)
            ->filterColumn('status', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $keywordInt = (int) $keyword;
                    if (!in_array($keywordInt, [11, 12])) {
                        $q->where(function ($inner) use ($keywordInt) {
                            $inner->where(function ($query) use ($keywordInt) {
                                $query->where('leads.status_id', $keywordInt)
                                    ->whereNotExists(function ($subQuery) {
                                        $subQuery->select(DB::raw(1))
                                            ->from('users')
                                            ->whereColumn('users.email', 'leads.email_address');
                                    });
                            })->orWhere(function ($query) use ($keywordInt) {
                                $query->where('leads.status_id', $keywordInt)
                                    ->whereExists(function ($subQuery) {
                                        $subQuery->select(DB::raw(1))
                                            ->from('users')
                                            ->whereColumn('users.email', 'leads.email_address')
                                            ->where('users.blacklist', '!=', 1)
                                            ->where('users.status', '!=', 3);
                                    });
                            });
                        });
                    }
                    if ($keywordInt === 11) {
                      $q->where(function ($check) {
                            $check->where('leads.status_id', 11)
                                ->orWhereExists(function ($subQuery) {
                                    $subQuery->select(DB::raw(1))
                                            ->from('users')
                                            ->whereColumn('users.lead_id', 'leads.id')
                                            ->where('users.blacklist', 1);
                                });
                        });
                    } 
                    
                    if ($keywordInt === 12) {
                        $q->where(function ($q) {
                            $q->where('leads.status_id', 12)
                            ->orWhereExists(function ($subQuery) {
                                $subQuery->select(DB::raw(1))
                                        ->from('users')
                                        ->whereColumn('users.lead_id', 'leads.id')
                                        ->where('users.status', 3);
                            });
                        });
                    }               

                });
            })

            ->editColumn('reference_person', function ($query) {
                if ($query->rider_id) {
                    return $query->rider_id . '-' . $query->reference_person;
                }
                return '-';
            })
            ->filterColumn('reference_person', function ($query, $keyword) {
                if ($keyword != '') {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('rp.trax_id', 'like', "%{$keyword}%")
                            ->orWhere('rp.name', 'like', "%{$keyword}%");
                    });
                }
            })
            ->addColumn('aging', function ($lead) {
                $days = Carbon::now()->diffInDays($lead->requested_date);
                if ($days == 0) {
                    return "-";
                } else {
                    return $days . ' d';
                }
            })
            ->addColumn('sale_person_tagged_aging', function ($lead) {
                $days = Carbon::now()->diffInDays($lead->sale_person_tagged_time);
                if ($days == 0) {
                    return "-";
                } else {
                    return $days . '  d';
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
            ->editColumn('via_channel', function ($lead) {
                return isset($lead->via_channel) ?  $lead->via_channel : '-';
            })
            ->editColumn('user_status', function ($lead) {
                $user = User::find($lead->user_id);

                $statusLabels = [
                    0 => 'Request Received',
                    1 => 'Rates Added',
                    2 => 'Pending for Activation',
                    5 => 'Rates Rejected',
                ];

                if (!$user) {
                    return '-';
                }

                return $statusLabels[$user->status] ?? '-';
            })

            ->addColumn("lead_progress", function ($user) {
                $user = User::find($user->user_id);
                if (isset($user->lead_id)) {
                    $weight_charges = WeightCharge::where('user_id', $user->user_id);

                    $description = '-';

                    if (($user->on_board_status < 1 && $user->created_at > '2024-06-13 00:00:00')) {
                        $lead_progress_setting = LeadProgressSetting::find(1);
                        $percentage = $lead_progress_setting->percent;

                        $description = "Account is $percentage% completed";
                    } else if (($weight_charges->exists() || $user->request_custom_quotation == 1) && !isset($user->rates_added_by)) {
                        $lead_progress_setting = LeadProgressSetting::find(2);
                        $percentage = $lead_progress_setting->percent;

                        $description = "Account is $percentage% completed";
                    } else if (isset($user->rates_added_by) && $user->documents_status != 2) {
                        $lead_progress_setting = LeadProgressSetting::find(3);
                        $percentage = $lead_progress_setting->percent;

                        $description = "Account is $percentage% completed";
                    } else if ($user->documents_status == 2 && $user->status != 3) {
                        $lead_progress_setting = LeadProgressSetting::find(4);
                        $percentage = $lead_progress_setting->percent;

                        $description = "Account is $percentage% completed";
                    } else if ($user->status == 3) {
                        $lead_progress_setting = LeadProgressSetting::find(5);
                        $percentage = $lead_progress_setting->percent;

                        $description = "Account is activated";
                    }

                    return $description;
                } else {
                    return '-';
                }
            })

            ->filterColumn('user_status', function ($query, $keyword) {
                $keyword = strtolower($keyword);

                if ($keyword == 0 || $keyword == 1 || $keyword == 2 || $keyword == 5) {
                    $query->where('u.status', '=', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })

            // ->filterColumn('lead_account_status', function($query, $keyword) {
            //     if (strtolower($keyword) === '1') {
            //         $query->whereIn('email_address', function($q) {
            //             $q->select('email')
            //               ->from('users');
            //         });
            //     } elseif (strtolower($keyword) === '0') {
            //         $query->whereNotIn('email_address', function($q) {
            //             $q->select('email')
            //               ->from('users');
            //         });
            //     }
            // })

            ->editColumn('status', function ($lead) {
                $user = User::where('email', $lead->email_address)->first();

                if ($lead->status_id == 11 || ($user && $user->blacklist == 1)) {
                    return 'Blocked';
                }

                if ($lead->status_id == 12 || ($user && $user->status == 3)) {
                    return 'Account Activated';
                }

                return $lead->status;
            })


            ->addColumn('lead_account_link', function ($lead) {
                if (!empty($lead->lead_id) && !empty($lead->activation_code)) {
                    return route('cod.signup', ['id' => $lead->lead_id, 'token' => $lead->activation_code]);
                }
                return '-';
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

                if ((session('role_id') == 1 || in_array(870, session('permissions'))) && $lead->call_status == 'no') {
                    $dropdown .= '<button type="button"  class="dropdown-item call_status" data-id = ' . $lead->lead_id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Make A Call</div></button>';
                }
                if ((session('role_id') == 1 || in_array(871, session('permissions'))) && $lead->call_status == 'yes') {
                    $dropdown .= '<button type="button"  class="dropdown-item call_status"  data-id = ' . $lead->lead_id . ' ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">End A Call</div></button>';
                }

                if ($lead->via_channel == 'Sonic' || !isset($lead->via_channel)) {
                    if ((session('role_id') == 1 || (in_array(696, session('permissions')) && (!in_array($lead->status_id, [9, 12]))))) {
                        $dropdown .= '<button type="button"  class="dropdown-item edit" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';
                    }
                }

                if (!User::where('email', $lead->email_address)->exists()) {
                    $dropdown .= '<button type="button" class="dropdown-item mail_trigger" data-lead_id="' . $lead->lead_id . '" data-email="' . $lead->email_address . '">
                                <div class="row no-gutters align-items-center">
                                    <div class="col-2"><i class="ft-mail"></i></div>
                                    <div class="col-9 offset-1">Send Mail</div>
                                </div>
                            </button>';
                }


                if ($lead->via_channel == 'Sonic' || !isset($lead->via_channel)) {
                    if ((session('role_id') == 1)) {
                        $dropdown .= '
                        <button type="button" class="dropdown-item view_logs">
                            <div class="row no-gutters align-items-center">
                                <div class="col-2">
                                    <i class="ft-edit"></i>
                                </div>
                                <div class="col-9 offset-1">
                                    View Logs
                                </div>
                            </div>
                        </button>';
                    }
                }



                return $dropdown;
            })->rawColumns(['lead_id_link', 'action'])->make(true);
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
            ->select(['pam_leads.created_at as lead_created_at', 'pam_leads.id as id', 'pam_leads.lead_id as lead_id', 'pam_leads.name as name', 'pam_leads.phone as phone', 'pam_leads.location_type as category', 'pam_leads.case_type as case', 'pam_leads.video_link as video_link', 'pam_leads.images as images', 'o.name as origin', 'd.name as destination', DB::raw('(select count(id) from pam_lead_items as pli where pli.lead_id = pam_leads.id) as item_count')]);

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
                    foreach ($images as $image) {
                        $exists = Storage::disk('public')->exists($image);
                        if ($exists) {
                            $route = Storage::disk('public')->url($image);
                        } else {
                            $route = Storage::disk('s3')->temporaryUrl('sonic-archive/'.$image, now()->addMinutes(5));
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
            ->rawColumns(['item_count_button', 'images_link_btn', 'video_link_btn', 'action'])
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
        $leads['in_process'] = Lead::whereIn('status_id', [2, 5, 6, 7, 8])->whereBetween('requested_date', [$from, $to]);
        $leads['in_process_for_activation'] = Lead::whereIn('status_id', [9])->whereBetween('requested_date', [$from, $to]);
        $leads['dead_leads'] = Lead::whereIn('status_id', [3, 4, 10, 11, 13])->whereBetween('requested_date', [$from, $to]);
        $leads['accounts_activated'] = Lead::where('status_id', 12)->whereBetween('requested_date', [$from, $to]);
        $leads['dormant'] = Lead::where('status_id', 14)->whereBetween('requested_date', [$from, $to]);

        if ($origin = $request->get('search_origin')) {
            $leads['total'] = $leads['total']->where('city_id', $origin);
            $leads['received'] = $leads['received']->where('city_id', $origin);
            $leads['in_process'] = $leads['in_process']->where('city_id', $origin);
            $leads['in_process_for_activation'] = $leads['in_process_for_activation']->where('city_id', $origin);
            $leads['dead_leads'] = $leads['dead_leads']->where('city_id', $origin);
            $leads['accounts_activated'] = $leads['accounts_activated']->where('city_id', $origin);
            $leads['dormant'] = $leads['dormant']->where('city_id', $origin);
        }
        if ($sale_person = $request->get('search_sale_person')) {
            $leads['total'] = $leads['total']->where('sale_person_id', $sale_person);
            $leads['received'] = $leads['received']->where('sale_person_id', $sale_person);
            $leads['in_process'] = $leads['in_process']->where('sale_person_id', $sale_person);
            $leads['in_process_for_activation'] = $leads['in_process_for_activation']->where('sale_person_id', $sale_person);
            $leads['dead_leads'] = $leads['dead_leads']->where('sale_person_id', $sale_person);
            $leads['accounts_activated'] = $leads['accounts_activated']->where('sale_person_id', $sale_person);
            $leads['dormant'] = $leads['dormant']->where('sale_person_id', $sale_person);
        }
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
            if (!in_array(session('id'), session('sale_users_bypass'))) {
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
        $leads['dead_leads_percentage'] = 0;
        $leads['accounts_activated_percentage'] = 0;
        $leads['in_process_for_activation_percentage'] = 0;
        if ($leads['total'] > 0) {
            if (is_numeric($leads['received'])) {
                $leads['received_percentage'] = round(($leads['received'] / $leads['total']) * 100, 2);
            }
            if (is_numeric($leads['in_process'])) {
                $leads['in_process_percentage'] = round(($leads['in_process'] / $leads['total']) * 100, 2);
            }
            if (is_numeric($leads['in_process_for_activation_percentage'])) {
                $leads['in_process_for_activation_percentage'] = round(($leads['in_process_for_activation'] / $leads['total']) * 100, 2);
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
        $leads['in_process_for_activation'] = number_format($leads['in_process_for_activation']);
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

        if ($status == 9 && !$lead->sale_person_id) {
            return response()->json(['status' => 0, 'error' => 'Without salesperson tagging, the status In Process for Activation will not be updated on Add Lead entries in lead management.']);
        }

        if ($status != NULL) {
            if ($lead) {
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

                //lead 2nd phase part
                $token = str::random(8);
                if ($lead->activation_code == '') {
                    $lead->activation_code = $token;
                    $lead->save();
                }

                //for old leads of company_name not set for new lead revamp
                if ($lead->company_name == '') {
                    $lead->company_name = $lead->company ?? '';
                }

                if ($status == 9 && $lead->email_status != 1 && $lead->activation_code != '') {
                    $lead->email_status = 1;
                    $lead->via_channel = 'Sonic';
                    NotificationsController::send(230, $lead->id);
                }
                //

                $lead->save();

                if ($lead->sale_person_id != NULL) {
                    // if ($status == 9 && $lead->created_at < '2024-07-22 00:00:00')  {
                    //     NotificationsController::send(113, $lead);
                    // } elseif()

                    if ($status == 2) {
                        LeadTaggingController::notification_unresponsive($lead->id);
                    }
                }
                if ($status == 11) {
                    $detail = array();
                    $detail['name'] = $lead->contact_person;
                    $detail['contact_number'] = $lead->phone_number;
                    $reason = LeadReason::where('id', $lead->reason)->select('name')->first();
                    $detail['reason'] = $reason->name;
                    NotificationsController::send(181, $detail);
                }

                return response()->json(['status' => 1, 'success' => 'Status updated Successfully!']);
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

                if ($status == 9 && !$lead->sale_person_id) {
                    return response()->json(['status' => 0, 'error' => 'Without salesperson tagging, the status In Prcess for Activation will not be updated on Add Lead entres in lead management']);
                }

                if ($lead) {
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

                    //lead 2nd phase part
                    $token = str::random(8);
                    if ($lead->activation_code == '') {
                        $lead->activation_code = $token;
                        $lead->save();
                    }

                    //for old leads of company_name not set for new lead revamp
                    if ($lead->company_name == '') {
                        $lead->company_name = $lead->company ?? '';
                    }

                    if ($status == 9 && $lead->email_status != 1 && $lead->activation_code != '') {
                        $lead->email_status = 1;
                        $lead->via_channel = 'Sonic';
                        NotificationsController::send(230, $lead->id);
                    }
                    //

                    $lead->save();

                    if ($lead->sale_person_id != NULL) {
                        // if ($status == 9 && $lead->created_at < '2024-07-22 00:00:00')  {
                        //     NotificationsController::send(113, $lead);
                        // } elseif()

                        if ($status == 2) {
                            LeadTaggingController::notification_unresponsive($lead->id);
                        }
                    }

                    if ($status == 11) {
                        $detail = array();
                        $detail['name'] = $lead->contact_person;
                        $detail['contact_number'] = $lead->phone_number;
                        $reason1 = LeadReason::where('id', $lead->reason)->select('name')->first();
                        $detail['reason'] = $reason1->name;
                        NotificationsController::send(181, $detail);
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
        $input = $request->all();

        $reference_person = ($input['reference_person'] === 'NaN')
            ? null
            : $input['reference_person'];

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
                    $lead->reference_person_id = empty($reference_person) ?  $lead->reference_person_id : $reference_person;
                }
              
                $lead->updated_by = Auth::id();
                $lead->sale_person_updated_at = Carbon::now();
                $lead->status_id = 15;
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

    public function lead_reasons(Request $request)
    {
        $lead_reason = LeadReason::join('lead_status_reasons as lsr', 'lsr.reason_id', 'lead_reasons.id')
            ->select('lead_reasons.id as id', 'lead_reasons.name as name')
            ->where('lsr.status_id', $request->status_id);
        if ($lead_reason->exists()) {
            $lead_reason = $lead_reason->get();
            return response()->json(['status' => 1, 'lead_reasons' => $lead_reason]);
        } else {
            return response()->json(['status' => 0, 'error' => 'Reason not found!']);
        }
    }

    public function info(Request $request)
    {
        $lead_id = $request->lead_id;

        $lead = Lead::find($lead_id);
        if ($lead_id) {
            $details = array();
            $details['city'] = '';
            $details['city_id'] = NULL;
            $details['territory'] = '';
            $details['area'] = '';
            if ($lead->city_id) {
                $details['city'] = $lead->city->name;
                $details['city_id'] = $lead->city_id;
            }

            if ($lead->territory_id) {
                $details['territory'] = Territory::find($lead->territory_id)->name;
                $details['territory_id'] = $lead->territory_id;
            }
            if ($lead->territory_area_id) {
                $details['area'] = AreaTerritory::find($lead->territory_area_id)->name;
                $details['area_id'] = $lead->territory_area_id;
            }

            $details['reference'] = '';
            $details['reference_id'] = '';
            if ($lead->reference_id) {
                $details['reference'] = LeadReference::find($lead->reference_id)->name;
                $details['reference_id'] = $lead->reference_id;
            }


            $details['phone_number'] = $lead->phone_number;
            $details['email_address'] = $lead->email_address;
            $details['brand'] = $lead->brand;
            $details['company'] = $lead->company;
            $details['service_id'] = $lead->service_id;

            return response()->json(['status' => 0, 'details' => $details]);
        } else {
            return response()->json(['status' => 1, 'error' => 'Invalid Lead ID!']);
        }
    }

    public function edit_service_list(Request $request)
    {
        $lead_service = Lead::where('id', $request->edit_lead_id)->first();
        $service_id = $lead_service->service_id;
        $service_name = ServiceList::where('id', $service_id)->first();
        return response()->json([
            'service_name' => $service_name
        ]);
    }

    public function edit(Request $request)
    {
        $lead_id = $request->edit_lead_id;
        if ($lead_id) {
            $lead = Lead::find($lead_id);
            if ($lead) {
                $lead->city_id = $request->city_id;
                $lead->territory_id = $request->territory_id;
                $lead->territory_area_id = $request->territory_area_id;
                $lead->phone_number = $request->phone_number;
                $lead->email_address = $request->email_address;
                $lead->brand = $request->brand;
                $lead->company = $request->company;
                $lead->reference_id = $request->edit_reference_id;
                $lead->status_id = 15;
                $lead->service_id = $request->service_id;

                try {
                    // maintain logs when editing leads
                    $changedFields = [];
                    $fieldNames = [
                        'city_id' => 'City',
                        'territory_id' => 'Territory',
                        'territory_area_id' => 'Territory Area',
                        'phone_number' => 'Phone Number',
                        'email_address' => 'Email Address',
                        'brand' => 'Brand',
                        'company' => 'Company',
                        'service_id' => 'Service',
                        'edit_reference_id' => 'Reference ID',
                    ];

                    // foreach ($fieldNames as $field => $fieldName) {
                    //     if ($lead->isDirty($field)) {;
                    //         $changedFields[] = $fieldName;
                    //     }
                    // }
                    foreach ($fieldNames as $field => $fieldName) {
                        if ($lead->isDirty($field)) {
                            $changedFields[] = $fieldName . ': ' . $lead->getOriginal($field) . ' -> ' . $lead->$field;
                        }
                    }

                    EditLeadLogs::create([
                        'lead_id' => $lead->id,
                        'trax_id' => Auth::user()->trax_id,
                        'admin_name' => Auth::user()->name,
                        'edited_fields' => implode(', ', $changedFields)
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error creating log entry: ' . $e->getMessage());
                }

                $lead->save();

                LeadTaggingController::auto_tagging($lead->id, Auth::id());
                return redirect()->back()->with('success', 'Lead Edited successfully!');
            }
            return redirect()->back()->with('error', 'Lead not found!');
        }
        return redirect()->back()->with('error', 'Something went wrong!');
    }

    public function add(Request $request)
    {
        try {
            $token = str::random(8);
            $new_lead = new Lead();
            $new_lead->contact_person = $request->contact_person;
            $new_lead->phone_number = $request->phone_number;
            $new_lead->email_address = $request->email_address;
            $new_lead->city_id = $request->city_id;
            $new_lead->requested_date = Carbon::now();
            $new_lead->service_id = $request->service_id;
            $new_lead->reference_id = $request->reference_id;
            $new_lead->territory_id = $request->territory_id;
            $new_lead->territory_area_id = $request->territory_area_id;
            $new_lead->brand = $request->brand;
            $new_lead->company = $request->company;
            $new_lead->company_name = $request->company;
            $new_lead->expected_shipments = $request->expected_shipments;
            $new_lead->average_shipment_per_week = $request->expected_shipments;
            $new_lead->status_id = 1;
            $new_lead->activation_code = $token;
            $new_lead->via_channel = 'Sonic';
            $new_lead->updated_by = Auth::id();
            $new_lead->save();

            return redirect()->back()->with('success', 'Lead added successfully');
        } catch (Exception $e) {

            return $e->getMessage();
        }
    }

    public function call_status_change(Request $request)
    {
        $validations = [
            'id' => 'required',
        ];

        $validate = Validator::make($request->all(), $validations);

        if ($validate->fails()) {
            return response()->json(['status' => 0, 'errors' => $validate->errors()]);
        } else {
            $id = $request->id;
            $lead = Lead::find($id);


            $lead_status  = LeadStatus::where('id', $lead->status_id);


            if ($lead_status->exists()) {
                $lead_status = $lead->status->name;
            } else {
                $lead_status = '-';
            }

            if ($lead->call_status == 'no') {
                $lead->call_status = 'yes';

                $log = new LeadCallStatusLog();

                $log->lead_id = $lead->id;
                $log->admin = Auth::id();
                $log->ip = $request->ip();
                $log->lead_status  = $lead_status;
                $log->call_status = 'yes';
                $log->created_at = Carbon::now();

                $lead->save();
                $log->save();

                return response()->json(['status' => 1]);
            } else {
                $log = new LeadCallStatusLog();

                $lead->call_status = 'no';

                $log->lead_id = $lead->id;
                $log->admin = Auth::id();
                $log->ip = $request->ip();
                $log->lead_status  = $lead_status;
                $log->call_status = 'no';
                $log->created_at = Carbon::now();

                $lead->save();
                $log->save();

                return response()->json(['status' => 1]);
            }
        }
    }

    public function checkLead(Request $request)
    {
        $id = $request->lead_id;

        $lead = Lead::find($id);

        $phoneNumberExists = false;
        $emailAddressExists = false;
        $companyExists = false;

        // for edit Validation
        if ($id) {
            if ($lead->phone_number != $request->phone) {
                $phoneNumberExists = Lead::where('phone_number', $request->phone)->exists();
            }

            if ($lead->email_address != $request->email) {
                $emailAddressExists = Lead::where('email_address', $request->email)->exists();
            }

            if ($lead->company != $request->company) {
                $companyExists = Lead::where('company', $request->company)->exists();
            }

            // for Add Validation
        } else {
            $phoneNumberExists = User::where('phone', $request->phone)->exists();
            if (!$phoneNumberExists) {
                $phoneNumberExists = Lead::where('phone_number', $request->phone)->exists();
            }

            $emailAddressExists = User::where('email', $request->email)->exists();
            if (!$emailAddressExists) {
                $emailAddressExists = Lead::where('email_address', $request->email)->exists();
            }

            $companyExists = User::where('name', $request->company)->exists();
            if (!$companyExists) {
                $companyExists = Lead::where('company', $request->company)->exists();
            }
        }

        return response()->json([
            'phone_number_exists' => $phoneNumberExists,
            'email_address_exists' => $emailAddressExists,
            'company_exists' => $companyExists,
        ]);
    }

    public function view_logs(Request $request)
    {
        $logs = EditLeadLogs::where('lead_id', $request->lead_id)->orderBy('created_at', 'desc')->get();
        if ($logs->isNotEmpty()) {
            return response()->json([
                'status' => 0,
                'logs' => $logs
            ]);
        } else {
            return response()->json([
                'status' => 1,
                'message' => 'No logs found for this lead.'
            ]);
        }
    }

    public function send_mail(Request $request)
    {
        if (!User::where('email', $request->email)->exists()) {
            NotificationsController::send(230, $request->lead_id, Carbon::today());
            return response()->json(['status' => 'success', 'message' => 'Mail sent successfully.']);
        }

        return response()->json(['status' => 'error', 'message' => 'This email is already registered.']);
    }
}
