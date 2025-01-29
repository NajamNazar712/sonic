<?php

namespace App\Http\Controllers\Admins;

use DB;
use Exception;
use Carbon\Carbon;
use App\Http\Models\City;
use App\Http\Models\RvState;
use Illuminate\Http\Request;
use App\Http\Models\Shipment;
use App\EmployeeAdditionalDay;
use App\RvAssignAgentSubStatus;
use App\Http\Models\Admin\Admin;
use App\Http\Models\HR\Employee;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\AdminRole;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\RvAgentAssignHub;
use App\Http\Models\HR\EmployeeStatus;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Http\Models\RvAssignAgentStatus;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\RvShipmentAssignAgent;
use App\Http\Controllers\NotificationsController;
use App\Http\Models\RvShipmentAssignAgentDetails;
use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Models\Admin\Attendance\EmployeeAttendance;
use App\Http\Models\HR\EmployeeType;
use App\Http\Models\Zone;

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

        ActivityTrailController::createActivityTrailLog(Auth::id(), 703);

        $zones = Zone::where('status', '1')->where('business_category_id', '1')->select('id', 'name')->get();
        $employee_additional_days = EmployeeAdditionalDay::get();
        $empid = Admin::find(Auth::id())->employee_id;
        $employee_statuses = EmployeeStatus::all();
        $number_of_available_agents = Employee::where('employee_type_id', 1)->where('line_manager_id', $empid)->where('staff_category_id', 3)->where('is_line_manager', 0)->where('status_id', '!=', 2)->pluck('id')->toArray();

        $Attendance = EmployeeAttendance::whereIn('employee_id', $number_of_available_agents)
            ->whereDate('attendance_date', '=', now()->format('Y-m-d'))
            ->whereIn('id', function ($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('employee_attendances')
                    ->groupBy('employee_attendances.employee_id');
            })
            ->get();

        $employee_types = EmployeeType::all();
        return view('admin.leads.team_lead')->with(['employee_types' => $employee_types, 'employee_statuses' => $employee_statuses, 'zones' => $zones, 'employee_additional_days' => $employee_additional_days, 'number_of_available_agents' => $Attendance]);
    }

    // Heading: N/A
    // Sidebar: N/A
    // URL: team_lead/list
    // Description: this method is used for viewing agents of team lead
    public function team_lead_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 117);
        }

        
        $employees = Employee::join('cities', 'employees.city_id', '=', 'cities.id')
            ->leftjoin('employees as lm', 'lm.id', 'employees.line_manager_id')
            ->leftjoin('admin_departments as ads', 'ads.id', '=', 'employees.department_id')
            ->leftjoin('admins as staff', 'staff.CNIC', '=', 'employees.CNIC')
            ->leftjoin('staff_categories as est', 'est.id', '=', 'employees.staff_category_id')
            ->leftjoin('riders as r', 'r.trax_id', '=', 'employees.trax_id')
            ->leftjoin('rider_requests as rr', 'rr.id', '=', 'employees.rider_request_id')
            ->leftjoin('rider_types as rr_rt', 'rr_rt.id', '=', 'rr.rider_type_id')
            ->leftjoin('rider_types as r_rt', 'r_rt.id', '=', 'r.rider_type_id')
            ->leftjoin('rider_types as er_rt', 'er_rt.id', '=', 'employees.rider_type_id')
            ->leftjoin('employee_designations as ed', 'ed.id', '=', 'employees.designation_id')
            ->join('employee_types as et', 'et.id', '=', 'employees.employee_type_id')
            ->join('employee_statuses as es', 'es.id', '=', 'employees.status_id')
            ->leftjoin('employee_attendances as ea', function ($join) {
                $join->on('ea.employee_id', '=', 'employees.id')
                    ->where('ea.id', '=', \Illuminate\Support\Facades\DB::raw('(select max(id) from employee_attendances where employee_attendances.employee_id = employees.id)'));
            })
            ->leftJoin('rv_agent_assign_hubs as rvab', function ($join) {
                $join->on('rvab.agent_id', '=', 'staff.id')
                    ->groupBy('rvab.zone_id')
                    ->havingRaw('COUNT(DISTINCT rvab.agent_id) > 1');
                })

            ->select(['employees.rider_type_id as rider_type_id', 'rvab.zone_id as rv_zone_id', 'ea.attendance_date as attendance_date', 'employees.id as employee_id', 'employees.name as employee_name', 'employees.city_id as city_id', 'cities.name as city', 'employees.trax_id as employee_trax_id', 'employees.request_status_id', 'employees.status_id as status_id', 'employees.employee_type_id', 'employees.cnic', 'employees.phone_number', 'et.name as employee_type', 'es.name as status', 'ads.name as department_name', 'employees.shift_id as shift_id', 'est.name as staff_category', 'employees.staff_category_id', 'employees.joining_date', 'ed.name as designation', 'staff.id as staff_id', 'employees.is_line_manager', 'lm.name as line_manager', 'employees.line_manager_id', 'employees.last_working_date as last_working_date', 'employees.official_email as official_email', 'employees.confirmation_status', 'employees.old_trax_id as old_trax_id', 'employees.remarks as remarks', 'staff.id as sid'])
            ->where('employees.staff_category_id', 3)
            // ->where('employees.line_manager_id', $empid)
            ->where('employees.is_line_manager', 0)
            ->where('et.id', 1)
            ->groupBy('staff.CNIC');
            
            if ($request->get('number_of_available_agents_input') == '2') {
                $employees = $employees->where('attendance_date', Carbon::now()->format('Y-m-d'))->get();
            }
            
        if (session('role_id') != 1) {
           $empid = Admin::find(Auth::id())->employee_id;
           $employees->where('employees.line_manager_id', $empid);
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
                if ($keyword == 5) {
                    return $query->where('employees.staff_category_id', '=', 3);
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

            ->editColumn('shipments', function ($user) {
                $shipment = explode(',', $user->shipments);

                $tracking_number = Shipment::whereIn('id', $shipment)->pluck('tracking_number')->toArray();

                if (empty($shipment[0])) {
                    return '--';
                } else {
                    return '<button class="btn btn-sm btn-outline-info align-middle assigned_shipment"  data-assigned=' . implode(',', $tracking_number) . '>' . count($tracking_number) . '</button>';
                }
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

            ->editColumn('attendance_date', function ($user) {
                $date = Carbon::parse($user->attendance_date);
                if ($date->isToday() && isset($user->attendance_date) && (isset($user->status_id) && $user->status_id != 2)) {
                    return 'Online';
                } else {
                    return 'Offline';
                }
            })
            ->filterColumn('ea.attendance_date', function ($query, $keyword) {
                if ($keyword != '' && $keyword != "Offline") {
                    $query->where('ea.attendance_date', "like", "%" . $keyword . "%");

                } else if($keyword == "Offline") {
                    $query->whereDate('ea.created_at', '!=', Carbon::Today());
                }
            })
            ->addColumn("action", function ($result) {
                if (session('role_id') == 1 || in_array(903, session('permissions'))) {
                    $dropdown = '
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                            <div class="dropdown-menu dropdown-menu-sm">
                        ';
                    if ($result->staff_category_id == 3) {
                        if ($result->status_id == 1 || $result->status_id == 3) {
                            if (session('role_id') == 1 || in_array(903, session('permissions'))) {
                                $rv_zone = RvAgentAssignHub::where('agent_id', $result->sid)->orderBy('priority', 'ASC')->get();
                                $rv_zone = $rv_zone->pluck('zone_id')->toArray();
                                $rv_zone = array_unique( $rv_zone);
                                $rv_zone = implode(',', $rv_zone);

                                $dropdown .= '<button type="button" class="dropdown-item assign_hub" data-id="' . $result->sid . '" data-city="' . $rv_zone . '"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Assign Zones</div></div></button>';
                                $dropdown .= '<button type="button" class="dropdown-item deactivate_staff" data-id=' . $result->employee_id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Deactivate Staff</div></button>';
                            }
                            if (session('role_id') == 1 || in_array(903, session('permissions'))) { 
                                $dropdown .= '<button type="button" class="dropdown-item add_additional_days" data-ename=' . $result->employee_name . ' data-id=' . $result->employee_id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Add Additional Days</div></button>';
                            }
                        }


                        if ($result->status_id == 2) {
                            if (session('role_id') == 1 || in_array(903, session('permissions'))) {
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
            })->rawColumns(['shipments','action']);

        return $datatable->make(true);
    }

    public function shipment_assign_index()
    {
        $number_of_rv_tickets = count(RvShipmentAssignAgent::get()) > 0 ? count(RvShipmentAssignAgent::get()) : 0;
        $number_of_rv_ticket = count(RvShipmentAssignAgent::get()) > 0 ? count(RvShipmentAssignAgent::get()) : 1;
        $number_of_pending_tickets = RvShipmentAssignAgent::where('rv_state_id', 3)->get();
        $number_of_pending_ticket_percentage = number_format((count($number_of_pending_tickets) / ($number_of_rv_ticket) * 100),2);
        $number_of_closed_tickets = RvShipmentAssignAgent::where('rv_state_id', 4)->get();
        $number_of_closed_ticket_percentage = number_format((count($number_of_closed_tickets) / ($number_of_rv_ticket) * 100),2);
        $number_of_connected_calls = RvShipmentAssignAgent::whereIn('rv_assign_agent_status_id', [1, 2, 3, 5])->get();
        $number_of_connected_calls_percentage = number_format((count($number_of_connected_calls) / ($number_of_rv_ticket) * 100),2);
        $number_of_unresponsive_call = RvShipmentAssignAgent::where('rv_assign_agent_status_id', 6)->get();
        $number_of_unresponsive_percentage = number_format((count($number_of_unresponsive_call) / ($number_of_rv_ticket) * 100),2);
        $number_of_reattempt_call = RvShipmentAssignAgent::where('rv_assign_agent_status_id', 2)->get();
        $number_of_reattempt_percentage = number_format((count($number_of_reattempt_call) / ($number_of_rv_ticket) * 100),2);
        $number_of_return_confirm_call = RvShipmentAssignAgent::where('rv_assign_agent_status_id', 1)->get();
        $number_of_return_confirm_percentage = number_format((count($number_of_return_confirm_call) / ($number_of_rv_ticket) * 100),2);
        $number_of_intercept_call = RvShipmentAssignAgent::where('rv_assign_agent_status_id', 3)->get();
        $number_of_intercept_percentage = number_format((count($number_of_intercept_call) / ($number_of_rv_ticket) * 100),2);
        $number_of_self_collection_call = RvShipmentAssignAgent::where('rv_assign_agent_status_id', 5)->get();
        $number_of_self_collection_percentage = number_format((count($number_of_self_collection_call) / ($number_of_rv_ticket) * 100),2);
        $number_of_available_agents = Employee::where('employee_type_id', 1)->where('staff_category_id', 3)->where('is_line_manager', 0)->where('status_id', '!=', 2)->pluck('id')->toArray();
        $online_agents = EmployeeAttendance::whereIn('employee_id', $number_of_available_agents)
            ->whereDate('attendance_date', '=', now()->format('Y-m-d'))
            ->whereIn('id', function ($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('employee_attendances')
                    ->groupBy('employee_attendances.employee_id');
            })
            ->get();

        return view('admin.rv_assign_shipments.index')->with(['online_agents' => $online_agents,'number_of_available_agents' => $number_of_available_agents, 'number_of_rv_tickets' => $number_of_rv_tickets, 'number_of_closed_tickets' => $number_of_closed_tickets, 'number_of_closed_ticket_percentage' => $number_of_closed_ticket_percentage, 'number_of_pending_tickets' => $number_of_pending_tickets, 'number_of_pending_ticket_percentage' => $number_of_pending_ticket_percentage, 'number_of_connected_calls' => $number_of_connected_calls, 'number_of_connected_calls_percentage' => $number_of_connected_calls_percentage, 'number_of_unresponsive_call' => $number_of_unresponsive_call, 'number_of_unresponsive_percentage' => $number_of_unresponsive_percentage, 'number_of_reattempt_call' => $number_of_reattempt_call, 'number_of_reattempt_percentage' => $number_of_reattempt_percentage, 'number_of_return_confirm_call' => $number_of_return_confirm_call, 'number_of_return_confirm_percentage' => $number_of_return_confirm_percentage, 'number_of_intercept_call' => $number_of_intercept_call, 'number_of_intercept_percentage' => $number_of_intercept_percentage, 'number_of_self_collection_call' => $number_of_self_collection_call, 'number_of_self_collection_percentage' => $number_of_self_collection_percentage]);
    }

    // Heading: N/A
    // Sidebar: N/A
    // URL: assigned_shipment/list
    // Description: this method is used for Listing Shipment Assign.
    public function shipment_assign_list(Request $request)
    {
        $assigned_agent_shipment = RvShipmentAssignAgent::join('admins as staff', 'staff.id', 'rv_shipment_assign_agents.agent_id')
            ->join('shipments as shipment', 'shipment.id', 'rv_shipment_assign_agents.shipment_id')
            ->select(['shipment.tracking_number as tracking_number', 'rv_shipment_assign_agents.shipment_id as shipment_id', 'staff.name as agent_name', 'rv_shipment_assign_agents.rv_assign_agent_status_id as status', 'rv_shipment_assign_agents.rv_assign_agent_sub_status_id as sub_status', 'rv_shipment_assign_agents.rv_state_id as state', 'rv_shipment_assign_agents.updated_by_id as updated_by']);


        if ($request->get('number_of_tickets_input') == '1') {

            $assigned_agent_shipment;
        }
        if ($request->get('number_of_pending_tickets_input') == '3') {
            $assigned_agent_shipment = $assigned_agent_shipment->where('rv_shipment_assign_agents.rv_state_id', 3);
        }

        if ($request->get('number_of_closed_tickets_input') == '4') {
            $assigned_agent_shipment = $assigned_agent_shipment->where('rv_shipment_assign_agents.rv_state_id', 4);
        }

        if ($request->get('number_of_connected_calls_input') == '5') {
            $assigned_agent_shipment = $assigned_agent_shipment->whereIn('rv_shipment_assign_agents.rv_assign_agent_status_id', [1, 2, 3, 5]);
        }

        if ($request->get('number_of_unresponsive_calls_input') == '6') {
            $assigned_agent_shipment = $assigned_agent_shipment->where('rv_shipment_assign_agents.rv_assign_agent_status_id', 6);
        }

        if ($request->get('number_of_reattempt_calls_input') == '7') {
            $assigned_agent_shipment = $assigned_agent_shipment->where('rv_shipment_assign_agents.rv_assign_agent_status_id', 2);
        }

        if ($request->get('number_of_return_confirm_calls_input') == '8') {
            $assigned_agent_shipment = $assigned_agent_shipment->where('rv_shipment_assign_agents.rv_assign_agent_status_id', 1);
        }

        if ($request->get('number_of_intercepted_calls_input') == '9') {
            $assigned_agent_shipment = $assigned_agent_shipment->where('rv_shipment_assign_agents.rv_assign_agent_status_id', 3);
        }

        if ($request->get('number_of_self_collection_calls_input') == '10') {
            $assigned_agent_shipment = $assigned_agent_shipment->where('rv_shipment_assign_agents.rv_assign_agent_status_id', 5);
        }

        $datatable = Datatables::of($assigned_agent_shipment)
            ->editColumn('status', function ($assigned_agent_shipment) {
                $status = RvAssignAgentStatus::where('id', $assigned_agent_shipment->status)->first();
                return $status ? $status->name : '-----';
            })

            ->editColumn('sub_status', function ($assigned_agent_shipment) {
                $sub_status = RvAssignAgentSubStatus::where('id', $assigned_agent_shipment->sub_status)->first();
                return $sub_status ? $sub_status->name : '-----';
            })

            ->editColumn('state', function ($assigned_agent_shipment) {
                $state = RvState::where('id', $assigned_agent_shipment->state)->first();
                return $state ? $state->name : '-----';
            })

            ->editColumn('updated_by', function ($assigned_agent_shipment) {
                $admin = Admin::where('id', $assigned_agent_shipment->updated_by)->first();
                return $admin ? $admin->name : 'Unknown';
            })

            ->editColumn('tracking_number', function ($assigned_agent_shipment) {
                $route = route('admin.tracking.index');
                return '<p><a href="' . $route . '?tracking_number=' . $assigned_agent_shipment->tracking_number . '" style="text-decoration: underline;">' . $assigned_agent_shipment->tracking_number . '</a></p>';
            })
            ->rawColumns(['tracking_number']);


        return $datatable->make(true);
    }


    // Heading: N/A
    // Sidebar: N/A
    // URL: team_lead/submit
    // Description: this method is used for Assign Agent AS per Priority
    public function assign_zone_agent(Request $request)
    {
        $zones = [];
        $sorted_zones = explode(',', $request->unsorted_zones);

        foreach ($sorted_zones as $key => $value) {
            $zones[] = City::where('zone_id', $value)->get();
            $count_zone[] = City::where('zone_id', $value)->count();
        }

        $count_zone = max($count_zone);

        try {
            $employeeIds = [];
        
            //since we are getting employee id and we have to save admin id in table 
            // If employee_id is present, add it to the array
            if ($request->has('employee_id') && $request->employee_id !== null) {
                $employeeIds[] = $request->employee_id;
            }
            $employee_id_bulks = explode(',', $request->employee_id_bulk);
            // If employee_id_bulk is present, add it to the array
            if ($request->has('employee_id_bulk')) {
                foreach ($employee_id_bulks as $bulkValue) {        
                    $admin = Admin::where('employee_id', $bulkValue)->first();
                    if ($admin) {
                        $employeeIds[] = $admin->id;
                    }
                }
            }
            foreach ($employeeIds as $employeeId) {
                $rvAgentAssignHub = RvAgentAssignHub::where('agent_id', $employeeId)->get();
                if ($rvAgentAssignHub->isNotEmpty()) {
                    $rvAgentAssignHub->each(function ($id) {
                        $id->delete();
                    });
                }
        
                $count = 0;
                foreach ($sorted_zones as $key => $value) {
                    for ($i = 0; $i < $count_zone; $i++) {
                        if (!isset($zones[$key][$i])) {
                            break;
                        } else {
                            $count = $count + 1;

                            RvAgentAssignHub::create([
                                'agent_id' => $employeeId,
                                'city_id' => $zones[$key][$i]['id'],
                                'zone_id' => $zones[$key][$i]['zone_id'],
                                'priority' => $count,
                            ]);
                        }
                    }
                }
            }
            if ($count > 0) {
                return redirect()->route('admin.team_lead.index')->with('success', 'Zone Assigned Successfully');

            } else {
                return redirect()->route('admin.team_lead.index')->with('error', 'Zone Not Assigned');

            }
        } catch (Exception $ex) {
            return redirect()->route('admin.team_lead.index')->with('error', $ex->getMessage());
        }
    }


    public function deactivate_staff(Request $request)
    {
        $get_employee = Employee::where('id', $request->employee_id);

        if ($get_employee->exists()) {
            $get_employee->update([
                'status_id' => '2',
                'remarks' => $request->deactivate_reason,
            ]);
        }
        $staff = Admin::where('employee_id', $request->employee_id)->where('trax_id', '!=', null);
        if ($staff->doesntExist()) {
            return response()->json(['status' => 1, 'error' => 'Staff not found!']);
        }
        $staff = $staff->first();

        $staff->status = 0;
        $staff->updated_by = Auth::id();
        $staff->save();

        $role = Admin::whereIn('role_id', [104, 63, 70])->pluck('email')->where('status', 1)->toArray();
        $employee = Employee::where('id', $request->employee_id)->first();
        NotificationsController::send(228, $employee, $role); //sending email to hr
    }


    public function activate_staff(Request $request)
    {
        $get_employee = Employee::where('id', $request->employee_id);

        if ($get_employee->exists()) {
            $get_employee->update([
                'status_id' => '1',
            ]);
        }
        $staff = Admin::where('employee_id', $request->employee_id)->where('trax_id', '!=', null);
        if ($staff->doesntExist()) {
            return response()->json(['status' => 1, 'error' => 'Staff not found!']);
        }
        $staff = $staff->first();

        $staff->status = 1;
        $staff->updated_by = Auth::id();
        $staff->save();
    }

    public function add_additional_days(Request $request)
    {
        try {
            $validations = [
                'employee_id' => 'required_without:employee_id_bulk',
                'employee_id_bulk' => 'required_without:employee_id',
                'add_additional_days' => 'required',
            ];

            $validate = Validator::make($request->all(), $validations);

            if ($validate->fails()) {
                return response()->json(['status' => 2, 'errors' => $validate->errors()]);
            } else {
                $error = [];
                $assigned = [];
                $employeeIds = [];
        
            //since we are getting employee id and we have to save admin id in table 
            // If employee_id is present, add it to the array
            if ($request->has('employee_id')) {
                $employeeIds[] = $request->employee_id;
                // $admin = Admin::where('employee_id', $request->employee_id)->first();
                //     if ($admin) {
                //     }
            }
            $employee_id_bulks = explode(',', $request->employee_id_bulk);
            // If employee_id_bulk is present, add it to the array
            if ($request->has('employee_id_bulk')) {
                foreach ($employee_id_bulks as $bulkValue) {        
                    // $admin = Admin::where('employee_id', $bulkValue)->first();
                    // if ($admin) {
                    //     $employeeIds[] = $admin->id;
                    // }
                    $employeeIds[] = $bulkValue;
                }
            }
            foreach ($employeeIds as $employeeId) {
                    $date = date('Y-m-d', strtotime($request->add_additional_days));
                    $is_date_assigned = EmployeeAdditionalDay::where('employee_id', $employeeId)->where('working_days', $date);
                    $employee = Employee::where('id', $employeeId)->first()->name;
                    if (!$is_date_assigned->exists()) {
                        $employee_additional_days = new EmployeeAdditionalDay();
                        $employee_additional_days->employee_id = $employeeId;
                        $employee_additional_days->working_days = $date;
                        $employee_additional_days->save();
                        $assigned[] = $employee;
                    }else{
                        $error[] = 1;
                    }
                }

                if(count($error) <= 0){
                    session()->flash('success', 'Additional Days Added Successfully');
                    return response()->json(['status' => 0, 'message' => 'Assigned Successfully']);
                }else {
                    if(count($assigned) > 0){
                        $assigned = implode(',', $assigned);
                        return response()->json(['status' => 2, 'message' => 'Already Assigned Apart From '.$assigned]);
                    }else{
                        return response()->json(['status' => 1, 'message' => 'Already Assigned']);
                    }
                }
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 3, 'errors' => $th->getMessage()]);
        }
    }

    public function delete_additional_days(Request $request)
    {

        $object = EmployeeAdditionalDay::get();
        EmployeeAdditionalDay::whereIn('id', $request->ids)->delete();
        return response()->json(['status' => 1, 'success' => 'Additional Days Deleted Successfully', 'object' => $object]);
    }

    public function get_updated_day(Request $request)
    {
        $employee_additional_days = EmployeeAdditionalDay::where('employee_id', $request->employee_id)->get();

        return response()->json(['employee_additional_days' => $employee_additional_days]);
    }
}