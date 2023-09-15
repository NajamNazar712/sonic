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

        $zones = Zone::where('status', '1')
            ->where('business_category_id', '1')
            ->select('id', 'name')
            ->get();


        // $sortedHubs = $hubs->sortBy(function ($hub) {
        //     $agentAssignHub = $hub->agentAssignHub->first();
        //     return $agentAssignHub ? $agentAssignHub->priority : PHP_INT_MAX;
        // });

        $employee_additional_days = EmployeeAdditionalDay::get();

        $empid = Admin::find(Auth::id())->employee_id;

        $employee_statuses = EmployeeStatus::all();

        $number_of_available_agents = Employee::where('employee_type_id', 1)->where('line_manager_id', $empid)->where('staff_category_id', 3)->where('is_line_manager', 0)->pluck('id')->toArray();

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

    public function shipment_assign_index()
    {
        $number_of_rv_tickets = count(RvShipmentAssignAgent::get()) > 0 ? count(RvShipmentAssignAgent::get()) : 0;
        $number_of_rv_ticket = count(RvShipmentAssignAgent::get()) > 0 ? count(RvShipmentAssignAgent::get()) : 1;
        $number_of_pending_tickets = RvShipmentAssignAgent::where('rv_state_id', 3)->get();
        $number_of_pending_ticket_percentage = (count($number_of_pending_tickets) / ($number_of_rv_ticket) * 100);
        $number_of_closed_tickets = RvShipmentAssignAgent::where('rv_state_id', 4)->get();
        $number_of_closed_ticket_percentage = (count($number_of_closed_tickets) / ($number_of_rv_ticket) * 100);
        $number_of_connected_calls = RvShipmentAssignAgent::whereIn('rv_assign_agent_status_id', [1, 2, 3, 5])->get();
        $number_of_connected_calls_percentage = (count($number_of_connected_calls) / ($number_of_rv_ticket) * 100);
        $number_of_unresponsive_call = RvShipmentAssignAgent::where('rv_assign_agent_status_id', 6)->get();
        $number_of_unresponsive_percentage = (count($number_of_unresponsive_call) / ($number_of_rv_ticket) * 100);
        $number_of_reattempt_call = RvShipmentAssignAgent::where('rv_assign_agent_status_id', 2)->get();
        $number_of_reattempt_percentage = (count($number_of_reattempt_call) / ($number_of_rv_ticket) * 100);
        $number_of_return_confirm_call = RvShipmentAssignAgent::where('rv_assign_agent_status_id', 1)->get();
        $number_of_return_confirm_percentage = (count($number_of_return_confirm_call) / ($number_of_rv_ticket) * 100);
        $number_of_intercept_call = RvShipmentAssignAgent::where('rv_assign_agent_status_id', 3)->get();
        $number_of_intercept_percentage = (count($number_of_intercept_call) / ($number_of_rv_ticket) * 100);
        $number_of_self_collection_call = RvShipmentAssignAgent::where('rv_assign_agent_status_id', 5)->get();
        $number_of_self_collection_percentage = (count($number_of_self_collection_call) / ($number_of_rv_ticket) * 100);

        return view('admin.rv_assign_shipments.index')->with(['number_of_rv_tickets' => $number_of_rv_tickets, 'number_of_closed_tickets' => $number_of_closed_tickets, 'number_of_closed_ticket_percentage' => $number_of_closed_ticket_percentage, 'number_of_pending_tickets' => $number_of_pending_tickets, 'number_of_pending_ticket_percentage' => $number_of_pending_ticket_percentage, 'number_of_connected_calls' => $number_of_connected_calls, 'number_of_connected_calls_percentage' => $number_of_connected_calls_percentage, 'number_of_unresponsive_call' => $number_of_unresponsive_call, 'number_of_unresponsive_percentage' => $number_of_unresponsive_percentage, 'number_of_reattempt_call' => $number_of_reattempt_call, 'number_of_reattempt_percentage' => $number_of_reattempt_percentage, 'number_of_return_confirm_call' => $number_of_return_confirm_call, 'number_of_return_confirm_percentage' => $number_of_return_confirm_percentage, 'number_of_intercept_call' => $number_of_intercept_call, 'number_of_intercept_percentage' => $number_of_intercept_percentage, 'number_of_self_collection_call' => $number_of_self_collection_call, 'number_of_self_collection_percentage' => $number_of_self_collection_percentage]);
    }

    // Heading: N/A
    // Sidebar: N/A
    // URL: assigned_shipment/list
    // Description: this method is used for Listing Shipment Assign.
    public function shipment_assign_list(Request $request)
    {
        $assigned_agent_shipment = RvShipmentAssignAgent::join('admins as staff', 'staff.id', 'rv_shipment_assign_agents.agent_id')
            ->leftjoin('shipments as shipment', 'shipment.id', 'rv_shipment_assign_agents.shipment_id')
            ->select(['shipment.tracking_number as tracking_number', 'rv_shipment_assign_agents.shipment_id as shipment_id', 'staff.name as agent_name', 'rv_shipment_assign_agents.rv_assign_agent_status_id as status', 'rv_shipment_assign_agents.rv_assign_agent_sub_status_id as sub_status', 'rv_shipment_assign_agents.rv_state_id as state', 'rv_shipment_assign_agents.updated_by_id as updated_by'])
            ->groupBy('rv_shipment_assign_agents.id');


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
                if ($status['name']) {
                    return $status['name'];
                } else {
                    return '-----';
                }
            })

            ->editColumn('sub_status', function ($assigned_agent_shipment) {
                $sub_status = RvAssignAgentSubStatus::where('id', $assigned_agent_shipment->sub_status)->first();
                if ($sub_status['name']) {
                    return $sub_status['name'];
                } else {
                    return '-----';
                }
            })
            ->editColumn('state', function ($assigned_agent_shipment) {
                $state = RvState::where('id', $assigned_agent_shipment->state)->first();
                if ($state['name']) {
                    return $state['name'];
                } else {
                    return '-----';
                }
            })

            ->editColumn('updated_by', function ($assigned_agent_shipment) {
                $admin = Admin::where('id', $assigned_agent_shipment->updated_by)->first();
                return ($admin['name']);
            })

            ->editColumn('tracking_number', function ($assigned_agent_shipment) {
                $route = route('admin.tracking.index');
                return '<p><a href="' . $route . '?tracking_number=' . $assigned_agent_shipment->tracking_number . '" style="text-decoration: underline;">' . $assigned_agent_shipment->tracking_number . '</a></p>';
            });

        return $datatable->make(true);
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

        $empid = Admin::find(Auth::id())->employee_id;

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

            ->select(['employees.rider_type_id as rider_type_id', 'rvab.zone_id as rv_zone_id', 'ea.attendance_date as attendance_date', 'employees.id as employee_id', 'employees.name as employee_name', 'employees.city_id as city_id', 'cities.name as city', 'employees.trax_id', 'employees.request_status_id', 'employees.status_id as status_id', 'employees.employee_type_id', 'employees.cnic', 'employees.phone_number', 'et.name as employee_type', 'es.name as status', 'ads.name as department_name', 'employees.shift_id as shift_id', 'est.name as staff_category', 'employees.staff_category_id', 'employees.joining_date', 'ed.name as designation', 'staff.id as staff_id', 'employees.is_line_manager', 'lm.name as line_manager', 'employees.line_manager_id', 'employees.last_working_date as last_working_date', 'employees.official_email as official_email', 'employees.confirmation_status', 'employees.old_trax_id as old_trax_id', 'employees.remarks as remarks', 'staff.id as sid'])
            ->where('employees.staff_category_id', 3)
            ->where('employees.line_manager_id', $empid)
            ->where('employees.is_line_manager', 0)
            ->where('et.id', 1)
            ->groupBy('staff.CNIC');

        if ($request->get('number_of_available_agents_input') == '2') {
            $employees = $employees->where('attendance_date', Carbon::now()->format('Y-m-d'))->get();
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
            ->filterColumn('ea.attendance_date', function ($query, $keyword) {

                if ($keyword != '' && $keyword != "Offline") {
                    $query->where('ea.attendance_date', "like", "%" . $keyword . "%");

                } else if($keyword == "Offline") {

                    $query->whereDate('ea.created_at', '!=', Carbon::Today());
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
                // dd($user);
                $date = Carbon::parse($user->attendance_date);
                if ($date->isToday() && isset($user->attendance_date)) {
                    return 'Online';
                } else {
                    return 'Offline';
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
                                $dropdown .= '<button type="button" class="dropdown-item deactivate_staff" data-id=' . $result->employee_id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">De-Activate Staff</div></button>';
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
            });

        return $datatable->make(true);
    }


    // Heading: N/A
    // Sidebar: N/A
    // URL: team_lead/submit
    // Description: this method is used for Assign Agent AS per Priority
    public function assign_hub_agent(Request $request)
    {

        $zones = [];
        $sorted_zones = explode(',', $request->unsorted_zones);
        foreach ($sorted_zones as $key => $value) {
            $zones[] = City::where('zone_id', $value)->get();
        }
        $totalCount = collect($zones)->sum(function ($array) {
            return $array->count();
        });
        try {
            $rvAgentAssignHub = RvAgentAssignHub::where('agent_id', $request->employee_id)->get();

            if ($rvAgentAssignHub->isNotEmpty()) {
                $rvAgentAssignHub->each(function ($id) {
                    $id->delete();
                });
            }
            $count = 0;
            foreach ($sorted_zones as $key => $value) {
                for ($i = 0; $i < $totalCount; $i++) {
                    if (!isset($zones[$key][$i])) {
                        break;
                    } else {
                        $count = $count + 1;
                        RvAgentAssignHub::create([
                            'agent_id' => $request->employee_id,
                            'city_id' => $zones[$key][$i]['id'],
                            'zone_id' => $zones[$key][$i]['zone_id'],
                            'priority' => $count,
                        ]);
                    }
                }
            }
            if ($count > 0) {
                return redirect()->route('admin.team_lead.index')->with('success', 'Assigned Successfully');

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
            ]);
        }

        $role = Admin::whereIn('role_id', [6, 63, 70])->pluck('email')->toArray();
        $employee = Employee::where('id', $request->employee_id)->first();
        NotificationsController::send(218, $employee, $role);
    }


    public function activate_staff(Request $request)
    {
        $get_employee = Employee::where('id', $request->employee_id);

        if ($get_employee->exists()) {
            $get_employee->update([
                'status_id' => '1',
            ]);
        }
    }

    public function add_additional_days(Request $request)
    {

        try {
            $validations = [
                'employee_id' => 'required',
                'add_additional_days' => 'required',
            ];

            $validate = Validator::make($request->all(), $validations);

            if ($validate->fails()) {
                return response()->json(['status' => 2, 'errors' => $validate->errors()]);
            } else {
                $date = date('Y-m-d', strtotime($request->add_additional_days));

                $is_date_assigned = EmployeeAdditionalDay::where('employee_id', $request->employee_id)->where('working_days', $date);

                if (!$is_date_assigned->exists()) {

                    $employee_additional_days = new EmployeeAdditionalDay();

                    $employee_additional_days->employee_id = $request->employee_id;
                    $employee_additional_days->working_days = $date;

                    $employee_additional_days->save();

                    session()->flash('success', 'Assigned Successfully');

                    return response()->json(['status' => 0, 'message' => 'Assigned Successfully']);
                } else {
                    return response()->json(['status' => 1, 'message' => 'Already Assigned']);
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
        return response()->json(['status' => 1, 'success' => 'Successfully Deleted', 'object' => $object]);
    }


    // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description:
    public function assign_agent(Request $request)
    {
        $shipment_ids = $request->shipment_ids;
        if ($shipment_ids) {
            foreach ($shipment_ids as $shipment_id) {
                $check_already_assigned = RvShipmentAssignAgent::where('shipment_id', $shipment_id)->where('rv_state_id', 1)->first();
                if ($check_already_assigned) {
                    return response()->json(['status' => 1, 'error' => 'Shipments Already Assigned']);
                }
                $assign_shipments = new RvShipmentAssignAgent();
                $assign_shipments->agent_id = $request->admin_id;
                $assign_shipments->shipment_id = $shipment_id;
                $assign_shipments->status = 1;
                $assign_shipments->assigned_by = Auth::id();
                $assign_shipments->save();

                $return_assign_log = new RvShipmentAssignAgentDetails();
                $return_assign_log->return_assign_shipment_id = $assign_shipments->id;
                $return_assign_log->status = 0;
                $return_assign_log->assigned_by = Auth::id();
                $return_assign_log->save();
            }
            return response()->json(['status' => 0, 'success' => 'Shipments Assigned successfully']);
        } else {
            return response()->json(['status' => 1, 'error' => 'No Shipment found!']);
        }
    }

    // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description: Unassigning shipment from agent 
    public function unassign_agent(Request $request)
    {
        $shipment_ids = $request->shipment_ids;

        if ($request->action == 'un-assign') {
            foreach ($shipment_ids as $shipment) {
                $rv_unassign_agent = RvShipmentAssignAgent::where('shipment_id', $shipment)->where('rv_state_id', 1);
                if ($rv_unassign_agent->exists()) {
                    $rv_unassign_agent = $rv_unassign_agent->latest()->first();
                    $rv_unassign_agent->rv_state_id = 2;
                    $rv_unassign_agent->updated_by_id = Auth::id();
                    $rv_unassign_agent->save();


                    $return_assign_log = new RvShipmentAssignAgentDetails();
                    $return_assign_log->rv_shipment_assign_agent_id = $rv_unassign_agent->id;
                    $return_assign_log->rv_state_id = 2;
                    $return_assign_log->updated_by_id = Auth::id();
                    $return_assign_log->save();
                }
            }
            return ['status' => 1, 'success' => "Agent Unassigned successfully"];
        }
    }

    // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description: Assigning Unassigning Agents from Excel Sheet
    public function assign_agent_excel(Request $request)
    {
        $names = [
            'tracking_number' => 'Tracking Number',
            'agent_id' => 'Agent ID'
        ];
        $messages = [
            'required' => ':attribute is Required.',
            'integer' => ':attribute must be an Integer.',
            'digits_between' => ':attribute must be between :min and :max Digits.',
            'unique' => ':attribute is already Present.'
        ];
        $rules_with_agent = [
            'tracking_number' => ['required', 'integer'],
            'agent_id' => ['required', 'integer']
        ];

        $rules_without_agent = [
            'tracking_number' => ['required', 'integer']
        ];
        $fields = [0 => 'tracking_number', 1 => 'agent_id'];

        if ($file = $request->file('shipments')) {
            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

            $header = ['Tracking Number', 'Agent ID'];
        }
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
                return redirect()->back()->with('error', 'Invalid Columns, Kindly follow the Template provided');
            } else {
                unset($spreadsheet[0]);
            }
        }
        if (!empty($spreadsheet) || !isset($spreadsheet)) {
            $rows = array();
            foreach ($spreadsheet as $spreadsheet_row) {
                $row = array();

                foreach ($spreadsheet_row as $key => $value) {
                    $row[$fields[$key]] = $value;
                }

                $rows[] = $row;
            }

            unset($spreadsheet);
            $errors = array();
            $tracking_ids = array();
            $tracking_id_row = array();
            foreach ($rows as $key => $row) {
                $row_id = $key + 2;
                if (!empty($row['agent_id'])) {
                    $validate = Validator::make($row, $rules_with_agent, $messages);
                } else {
                    $validate = Validator::make($row, $rules_without_agent, $messages);
                }

                $validate->setAttributeNames($names);

                if ($validate->fails()) {
                    $errors['Row #' . $row_id] = $validate->errors()->all();
                }
                if (empty($errors['Row #' . $row_id])) {
                    if (!empty(trim($row['tracking_number']))) {
                        if (empty($tracking_ids)) {
                            $tracking_ids[] = $row['tracking_number'];
                            $tracking_id_row[$row['tracking_number']] = $row_id;
                        } else {
                            if (in_array($row['tracking_number'], $tracking_ids)) {
                                $errors['Row #' . $row_id][] = 'Same Tracking Number as of Row #' . $tracking_id_row[$row['tracking_number']];
                            } else {
                                $tracking_ids[] = $row['tracking_number'];
                                $tracking_id_row[$row['tracking_number']] = $row_id;
                            }
                        }
                    }
                    if (!Shipment::where('tracking_number', $row['tracking_number'])->whereIn('shipper_status_id', [12, 52])->exists()) {
                        $errors['Row #' . $row_id][] = 'Shipment is not valid #' . $row['tracking_number'];
                    }
                    if (!empty($row['agent_id'])) { //if agent = 1 or 2 or 3
                        if (!AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id')->where('admin_roles.department_id', 3)->where('a.status', 1)->where('a.id', $row['agent_id'])->exists()) {
                            $errors['Row #' . $row_id][] = 'Agent ID is not valid #' . $row['agent_id']; //remove for ticket no 4934
                        }
                    }
                }
            }
            if (empty($errors)) {
                $tracking_numbers = array();
                foreach ($rows as $key => $row) {
                    $row_id = $key + 2;
                    $shipment_id = trim($row['tracking_number']); //111
                    $agent_id = trim($row['agent_id']); //22

                    $id_shipment = Shipment::where('tracking_number', $shipment_id)->first();

                    $tracking_numbers['Row #' . $row_id] = $shipment_id;
                }
                $tracking_numbers = implode(' | ', array_map(function ($row, $tracking_number) {
                    return $row . ': ' . $tracking_number;
                }, array_keys($tracking_numbers), $tracking_numbers));

                return redirect()->back()->with(['success' => 'Total ' . count($rows) . ' Shipment(s) Updated with Tracking Number(s):' . PHP_EOL . $tracking_numbers]);
            } else {
                $errors = array_map(function ($row, $errors) {
                    return $row . ':' . PHP_EOL . implode(' | ', $errors);
                }, array_keys($errors), $errors);

                return redirect()->back()->withErrors($errors);
            }
        } else {
            return redirect()->back()->with('error', 'No Shipments in File');
        }
    }
    public function get_updated_day(Request $request)
    {
        $employee_additional_days = EmployeeAdditionalDay::where('employee_id', $request->employee_id)->get();

        return response()->json(['employee_additional_days' => $employee_additional_days]);
    }
}