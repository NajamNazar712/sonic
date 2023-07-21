<?php

namespace App\Http\Controllers\Admins;

use App\EmployeeAdditionalDay;
use DB;
use Auth;
use Exception;
use Carbon\Carbon;
use App\Http\Models\City;
use Illuminate\Http\Request;
use App\Http\Models\Admin\Admin;
use App\Http\Models\HR\Employee;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Http\Models\RvAgentAssignHub;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\Admins\ActivityTrailController;

class TeamLeadDashboardController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }


    // Heading: Team Leads Management
    // Sidebar: Team Lead
    // URL: team_lead
    // Description: this method is used for Index Page And Get HUbs With Priority.
    public function team_lead_index()
    {
        $hubs = City::where('status', '1')
            ->where('business_category_id', '1')
            ->where('hub', '1')
            ->select('id', 'name')
            ->get();

        $sortedHubs = $hubs->sortBy(function ($hub) {
            $agentAssignHub = $hub->agentAssignHub->first();
            return $agentAssignHub ? $agentAssignHub->priority : PHP_INT_MAX;
        });

        $employee_additional_days = EmployeeAdditionalDay::get();
        
        return view('admin.leads.team_lead')->with(['hubs' => $sortedHubs, 'employee_additional_days'=>$employee_additional_days]);
    }

    // Heading: N/A
    // Sidebar: N/A
    // URL: team_lead/list
    // Description: this method is used for Listing Employees.
    public function team_lead_list(Request $request)

    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 117);
        }
        $employees = Employee::join('cities', 'employees.city_id', '=', 'cities.id')
            ->leftjoin('employees as lm', 'lm.id', 'employees.line_manager_id')
            ->join('employee_genders as eg', 'eg.id', '=', 'employees.employee_gender_id')
            ->leftjoin('admin_departments as ads', 'ads.id', '=', 'employees.department_id')
            ->leftjoin('admins as staff', 'staff.trax_id', '=', 'employees.trax_id')

            ->leftjoin('riders as r', 'r.trax_id', '=', 'employees.trax_id')
            ->leftjoin('rider_requests as rr', 'rr.id', '=', 'employees.rider_request_id')
            ->leftjoin('rider_types as rr_rt', 'rr_rt.id', '=', 'rr.rider_type_id')
            ->leftjoin('rider_types as r_rt', 'r_rt.id', '=', 'r.rider_type_id')
            ->leftjoin('rider_types as er_rt', 'er_rt.id', '=', 'employees.rider_type_id')
            ->leftjoin('staff_categories as est', 'est.id', '=', 'employees.staff_category_id')
            ->join('employee_types as et', 'et.id', '=', 'employees.employee_type_id')
            ->join('employee_request_statuses as ers', 'ers.id', '=', 'employees.request_status_id')
            ->leftjoin('rider_main_categories as rmc', 'rmc.id', '=', 'employees.rider_main_category')
            ->leftjoin('employee_designations as ed', 'ed.id', '=', 'employees.designation_id')
            ->join('employee_statuses as es', 'es.id', '=', 'employees.status_id')
            ->leftjoin('employees as r_emp', 'r_emp.id', '=', 'employees.replacement_employee_id')
            ->leftjoin('employee_bank_informations as eb', function ($join) {
                $join->on('eb.employee_id', '=', 'employees.id')
                    ->where('eb.id', '=', \Illuminate\Support\Facades\DB::raw('(select max(id) from employee_bank_informations where employee_bank_informations.employee_id = employees.id)'));
            })
            ->leftjoin('zones as ez', 'ez.id', '=', 'employees.zone_id')
            ->leftJoin('rv_agent_assign_hubs as rvab', function ($join) {
                $join->on('rvab.agent_id', '=', 'staff.id')
                    ->groupBy('rvab.city_id')
                    ->havingRaw('COUNT(DISTINCT rvab.agent_id) > 1');
            })



            ->select(['r.name as check_if_rider_present_bit', 'r.ccd as ccd', 'r.rider_category_id as category_id', 'r.route_id as route_id', 'r.operation_rider_id as operation_id', 'r.blacklist as blacklist_rider', 'rr_rt.id as inactive_rider_type_id', 'rr_rt.name as inactive_rider_type', 'r_rt.id as active_rider_type_id', 'r_rt.name as active_rider_type', 'employees.id as employee_id', 'employees.name as employee_name', 'employees.city_id as city_id', 'cities.name as city', 'employees.trax_id', 'employees.request_status_id', 'employees.status_id as status_id', 'employees.employee_type_id', 'eg.name as gender', 'employees.cnic', 'employees.phone_number', 'et.name as employee_type', 'ers.name as request_status', 'es.name as status', 'employees.created_at as requested_at', 'employees.pin as pin', 'employees.address as address', 'employees.guardian_name as father_name', 'ads.name as department_name', 'employees.shift_id as shift_id', 'employees.first_inactive', 'employees.rider_sub_category as rider_sub_category', 'employees.rider_main_category as rider_main_category_id', 'er_rt.name as rider_type', 'est.name as staff_category', 'employees.staff_category_id', 'employees.joining_date', 'rmc.name as rider_main_category', 'employees.rider_type_id as rider_type_id', 'ed.name as designation', 'r.id as rider_id', 'staff.id as staff_id', 'eb.iban as iban', 'ez.id as zone_id', 'ez.name as zone_name', 'r.incentive_amount', 'employees.is_line_manager', 'lm.name as line_manager', 'employees.line_manager_id', 'employees.last_working_date as last_working_date', 'employees.official_email as official_email', 'r_emp.trax_id as r_trax_id', 'r_emp.name as r_name', 'employees.confirmation_status', 'employees.old_trax_id as old_trax_id', 'employees.remarks as remarks', 'staff.id as sid', \Illuminate\Support\Facades\DB::raw('GROUP_CONCAT(rvab.city_id ORDER BY rvab.priority) as rv_city')])
            ->where('employees.staff_category_id', 3)
            ->where('employees.line_manager_id', Auth::id())

            ->groupBy('staff.id')
            ->where(function ($q) {
                $q->where('r.blacklist', '=', 0)
                    ->orWhere('r.blacklist', '=', null);
            });

        $environment = config('app.env');

        if ($environment == 'staging') {
            $employees = $employees->orderBy('employees.updated_at', 'desc');
        }

        if (session('role_id') != 1) {
            $employees = $employees->whereIn('cities.hub_id', session('hubs'));
        }

        if ($line_manager = $request->get('search_line_manager')) {
            $employees = $employees->where('employees.line_manager_id', $line_manager);
        }
        if ($filter_line_manager = $request->get('filter_line_manager')) {
            if ($filter_line_manager == 1) {
                $employees = $employees->where('employees.is_line_manager', 1);
            }
        }


        $datatable = Datatables::of($employees)
            ->setRowAttr([
                'class' => function ($employee) {
                    if ($employee->is_line_manager == 1) {
                        return "is_line_manager";
                    }
                },
            ])
            ->addColumn('id_padded', function ($user) {
                return str_pad($user->employee_id, 6, '0', STR_PAD_LEFT);
            })
            ->filterColumn('users.id', function ($query, $keyword) {
                return $query->where('users.id', '=', $keyword);
            })
            ->filterColumn('et.name', function ($query, $keyword) {
                if ($keyword == 1) {
                    return $query->where('employees.staff_category_id', '=', 1);
                } elseif ($keyword == 2) {
                    return $query->where('et.name', '=', 'Rider')
                        ->where(function ($q) {
                            $q->where('employees.rider_type_id', 1)
                                ->orwhere('r_rt.id', 1);
                        });
                } elseif ($keyword == 3) {
                    return $query->where('et.name', '=', 'Rider')
                        ->where(function ($q) {
                            $q->where('employees.rider_type_id', 2)
                                ->orwhere('r_rt.id', 2);
                        });
                }
                if ($keyword == 4) {
                    return $query->where('employees.staff_category_id', '=', 2);
                }

                return null;
            })
            ->addColumn('employee_hub', function ($user) {
                return City::where('id', $user->city_id)->first()->hub_city->name ?? "";
            })
            ->addColumn('employee_designation', function ($user) {
                return ($user->designation) ? $user->designation : '-';
            })
            ->editColumn('employee_type', function ($user) {
                if ($user->employee_type_id == 1) {

                    if ($user->staff_category_id == 2) {
                        return "Intern";
                    } else {
                        return "Staff";
                    }
                } else {

                    $type = $user->employee_type;

                    if ($user->rider_type != null) {
                        $type .= ' - ' . $user->rider_type;
                    }

                    return $type;
                }
            })
            ->editColumn('employee_name', function ($user) {
                return $user->employee_name;
            })
            ->filterColumn('ads.name', function ($query, $keyword) {

                if ($keyword != '') {
                    $query->where('ads.name', "like", "%" . $keyword . "%");
                } else {
                    $query->whereRaw('false');
                }
            })
            ->editColumn('confirmation_status', function ($user) {
                if ($user->confirmation_status == 1) {
                    return 'Permanent';
                } elseif ($user->confirmation_status == 2) {
                    return 'Probation';
                } else {
                    return '-';
                }
            })
            ->addColumn("action", function ($result) {
                if (session('role_id') == 1 || in_array(652, session('permissions'))) {
                    $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';
                    if ($result->staff_category_id == 3) {
                        if ((session('role_id') == 1 || in_array(session('permissions')))) {

                            $dropdown .= '<button type="button" class="dropdown-item assign_hub" data-id="' . $result->sid . '" data-city="' . $result->rv_city . '"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Assign Hub</div></div></button>';

                         
                        }

                        if ($result->status_id == 1) {
                            if (session('role_id') == 1 || in_array(652, session('permissions'))) {
                                $dropdown .= '<button type="button" class="dropdown-item deactivate_staff" data-id=' . $result->employee_id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">De-Activate Staff</div></button>';
                            }

                            if (session('role_id') == 1 || in_array(652, session('permissions'))) {
                                $dropdown .= '<button type="button" class="dropdown-item add_additional_days" data-ename='.$result->employee_name.' data-id=' . $result->employee_id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Add Additional Days</div></button>';
                            }

                        }


                        if ($result->status_id == 2) {
                            if (session('role_id') == 1 || in_array(652, session('permissions'))) {
                                $dropdown .= '<button type="button" class="dropdown-item activate_staff" data-id=' . $result->employee_id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Activate Staff</div></button>';
                            }

                        }
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
        if ($request->get('search_date_from')) {
            if ($request->get('search_date_to')) {
                $from = $request->get('search_date_from') . ' 00:00:00';
                $to = $request->get('search_date_to') . ' 23:59:59';
                $datatable->whereBetween('employees.created_at', [$from, $to]);
            } else {
                $from = $request->get('search_date_from');
                $datatable->whereDate('employees.created_at', $from);
            }
        }
        return $datatable->make(true);
    }


    // Heading: N/A
    // Sidebar: N/A
    // URL: team_lead/submit
    // Description: this method is used for Assign Agent AS per Priority
    public function assign_hub_agent(Request $request)
    {
        try {
            $rvAgentAssignHub = RvAgentAssignHub::where('agent_id', $request->employee_id);
            if ($rvAgentAssignHub) {
                $rvAgentAssignHub->delete();
            }

            foreach ($request->assign_hubs as $key => $value) {
                $priority = $key + 1;
                RvAgentAssignHub::create([
                    'agent_id' => $request->employee_id,
                    'city_id' => $value,
                    'priority' => $priority,
                ]);
            }

            return redirect()->route('admin.team_lead.index')->with('success', 'Assigned SuccessFully');

        } catch (Exception $ex) {

            return redirect()->route('admin.team_lead.index')->with('error', $ex->getMessage());
        }
    }


    public function deactivate_staff(Request $request)
    {
        $get_employee = Employee::where('id', $request->employee_id);

        if($get_employee->exists())
        {
            $get_employee->update([
                'status_id' => '2',
            ]);
        }

        $role = Admin::where('id', Auth::id())->first();
        
        NotificationsController::send(218, Auth::id(), $role->role->name);

    }


    public function activate_staff(Request $request)
    {
        $get_employee = Employee::where('id', $request->employee_id);

        if($get_employee->exists())
        {
            $get_employee->update([
                'status_id' => '1',
            ]);
        }
    }

    public function add_additional_days(Request $request)
    {
        $date = date('Y-m-d', strtotime($request->add_additional_days));

        $is_date_assigned = EmployeeAdditionalDay::where('working_days', $date);

        if(!$is_date_assigned->exists()){
            
            $employee_additional_days = new EmployeeAdditionalDay();
    
            $employee_additional_days->employee_id = $request->employee_id;
            $employee_additional_days->working_days = $date;
    
            $employee_additional_days->save();
    
    
            return redirect()->route('admin.team_lead.index');
        }else{
            return redirect()->route('admin.team_lead.index')->with('error', 'Date Already Assigned');

        }

    }



    public function delete_additional_days(Request $request)
    {
        $object = EmployeeAdditionalDay::get();
        $employee_additional_days = EmployeeAdditionalDay::find($request->id);
        $employee_additional_days->delete();
        return response()->json(['status' => 1, 'success'=> 'Successfully Deleted', 'object' => $object]);
    }
}
