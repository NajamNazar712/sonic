<?php

namespace App\Http\Controllers\Admins\LocalFleet;

use App\Http\Controllers\Controller;
use App\Models\LocalFleetVehicle;
use App\Models\LocalFleetVehicleTrip;
use App\Models\LocalTripJobReference;
use App\Models\LocalTripVehicleCost;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class AdminLocalFleetReportController extends Controller
{

    public function __construct()
    {   $this->middleware('auth:admin');
        $this->middleware('Permission');
    }
    public function trip_index()
    {
        return view('admin.local_fleet.trip_list');
    }
    public function trip_list(Request $request)
    {
        $data = LocalFleetVehicleTrip::join('local_fleet_vehicles as lfv', 'lfv.id', '=', 'local_fleet_vehicle_trips.vehicle_id')
            ->join('cities as c','c.id','=','lfv.city_id')
            ->select(
                'local_fleet_vehicle_trips.id',
                'lfv.vehicle_number',
                'lfv.make',
                'lfv.city_id',
                'c.name as city_name',
                'lfv.vendor_name',
                'local_fleet_vehicle_trips.out_time',
                'local_fleet_vehicle_trips.in_time',
                'local_fleet_vehicle_trips.out_meter',
                'local_fleet_vehicle_trips.in_meter',
                'local_fleet_vehicle_trips.mileage',
                'local_fleet_vehicle_trips.fuel_liters',
                'local_fleet_vehicle_trips.total_dn_count',
                'local_fleet_vehicle_trips.total_rn_count',
                'local_fleet_vehicle_trips.total_pickup_count',
                'local_fleet_vehicle_trips.total_trip_cost',
                'local_fleet_vehicle_trips.incident_report',
                'local_fleet_vehicle_trips.created_at',
            );

        if(session('role_id') != 1) {
            $data->whereIn('c.hub_id',session('hubs'));
        }

        return DataTables::of($data)
//            ->editColumn('total_dn_count',function ($query) {
//                if ($query->total_dn_count != 0) {
//                    return $query->total_dn_count;
////                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $query->total_dn_count . '</button>';
//                } else {
//                    return '-';
//                }
//            })
//            ->editColumn('total_rn_count',function ($query) {
//                if ($query->total_rn_count != 0) {
//                    return $query->total_rn_count;
////                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $query->total_rn_count . '</button>';
//                } else {
//                    return '-';
//                }
//            })
//            ->editColumn('total_pickup_count',function ($query) {
//                if ($query->total_pickup_count != 0) {
//                    return $query->total_pickup_count;
////                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $query->total_pickup_count . '</button>';
//                } else {
//                    return '-';
//                }
//            })
            ->editColumn('total_trip_cost',function ($cost){
                if($cost->total_trip_cost ){
                    return number_format($cost->total_trip_cost);
                }
               return 0;
            })
            ->editColumn('out_time', function ($row) {
                if($row->out_time) {
                    return \Carbon\Carbon::parse($row->out_time)->format('d M Y, h:i A');
                }
                return '-';

            })
            ->editColumn('in_time', function ($row) {
                if($row->in_time) {
                    return \Carbon\Carbon::parse($row->in_time)->format('d M Y, h:i A');
                }
                return '-';

            })
            ->editColumn('created_at', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->format('d M Y, h:i A');
            })
            ->editColumn('incident_report', function ($row) {
                if (!$row->incident_report) {
                    return '-';
                }

                $text = e($row->incident_report);

                if (strlen($text) <= 40) {
                    return $text;
                }

                return '
                    <span class="short-text">' . substr($text, 0, 40) . '...</span>
                    <span class="full-text d-none">' . $text . '</span>
                    <a href="javascript:void(0)" class="show-more">Show more</a>
                ';
            })
            ->addColumn('action', function ($roles) {
                    $dropdown = '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                    <button type="button" class="dropdown-item view-trip-cost"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-bookmark"></i></div><div class="col-9 offset-1">View Trip Cost</div></button>
                    ';
                    $dropdown .= '</div>  </div>';
                    return $dropdown;
            })
            ->rawColumns(['action','incident_report','total_dn_count','total_rn_count','total_pickup_count'])
            ->make(true);
    }

    public function tripCostDetail($trip_id)
    {
        $costs = LocalTripVehicleCost::where('trip_id', $trip_id)->get();

        $outCost = $costs->where('trip_type', 0)->first();
        $inCost  = $costs->where('trip_type', 1)->first();

        return response()->json([
            'status' => 0,
            'data' => [
                'out' => [
                    'amount'  => $outCost?->cost_amount ?? 0,
                    'remarks' => $outCost?->remarks ?? '-',
                    'receipt' => $outCost?->receipt_path
                        ? asset('storage/' . $outCost->receipt_path)
                        : null,
                    'date' => $outCost?->created_at
                        ? \Carbon\Carbon::parse($outCost->created_at)->format('d M Y, h:i A')
                        : '-',
                ],
                'in' => [
                    'amount'  => $inCost?->cost_amount ?? 0,
                    'remarks' => $inCost?->remarks ?? '-',
                    'receipt' => $inCost?->receipt_path
                        ? asset('storage/' . $inCost->receipt_path)
                        : null,
                    'date' => $inCost?->created_at
                        ? \Carbon\Carbon::parse($inCost->created_at)->format('d M Y, h:i A')
                        : '-',
                ],
            ],
        ]);
    }


    public function consolidated_trips_index()
    {
        return view('admin.local_fleet.vehicle_wise_trips_report');
    }
    public function consolidated_trips_list(Request $request)
    {
        $data = LocalFleetVehicleTrip::join('local_fleet_vehicles as lfv', 'lfv.id', '=', 'local_fleet_vehicle_trips.vehicle_id')
            ->join('cities as c', 'c.id', '=', 'lfv.city_id')
            ->select(
                'local_fleet_vehicle_trips.trip_date',
                'lfv.id as vehicle_id',
                'lfv.vehicle_number',
                'c.name as city_name',
                'lfv.make',
                'lfv.vendor_name',
                'lfv.vehicle_type',
                'lfv.capacity',
                DB::raw('COUNT(local_fleet_vehicle_trips.id) as total_trips'),
                DB::raw('SUM(local_fleet_vehicle_trips.total_dn_count) as total_dns'),
                DB::raw('SUM(local_fleet_vehicle_trips.total_dn_shipment_count) as total_dn_shipments'),
                DB::raw('SUM(local_fleet_vehicle_trips.total_dn_shipment_weight) as total_dn_shipment_weight'),

                DB::raw('SUM(local_fleet_vehicle_trips.total_rn_count) as total_rns'),
                DB::raw('SUM(local_fleet_vehicle_trips.total_rn_shipment_count) as total_rn_shipments'),
                DB::raw('SUM(local_fleet_vehicle_trips.total_rn_shipment_weight) as total_rn_shipment_weight'),

                DB::raw('SUM(local_fleet_vehicle_trips.total_pickup_count) as total_pickup_count'),
                DB::raw('SUM(local_fleet_vehicle_trips.total_pickup_shipment_count) as total_pickup_shipments'),
                DB::raw('SUM(local_fleet_vehicle_trips.total_pickup_shipment_weight) as total_pickup_shipment_weight'),

                DB::raw('SUM(local_fleet_vehicle_trips.fuel_liters) as fuel_liters_day'),
                DB::raw('SUM(local_fleet_vehicle_trips.total_trip_cost) as total_trip_cost')
            )
            ->groupBy(
                'local_fleet_vehicle_trips.trip_date',
                'lfv.id',
//                'lfv.vehicle_number',
//                'c.name',
//                'lfv.make',
//                'lfv.vendor_name',
//                'lfv.vehicle_type',
//                'lfv.capacity'
            );

        if (session('role_id') != 1) {
            $data->whereIn('c.hub_id', session('hubs'));
        }

        // DATE FILTER (FAST & SAFE)
        if ($request->from_date && $request->to_date) {
            $data->whereBetween('local_fleet_vehicle_trips.trip_date', [$request->from_date, $request->to_date]);
        } else {
            $to_date = Carbon::now()->toDateString();
            $data->where('local_fleet_vehicle_trips.trip_date', $to_date);
        }

        return DataTables::of($data)
            ->editColumn('report_date', function ($row) {
                return \Carbon\Carbon::parse($row->report_date)->format('d M Y');
            })
            ->editColumn('vehicle_type', function ($row) {
                return $row->vehicle_type == 1 ? 'Permanent' : 'Temporary';
            })
            ->editColumn('total_trip_cost', function ($row) {
                return number_format($row->total_trip_cost ?? 0);
            })
//            ->editColumn('total_trips',function ($query) {
//                if ($query->total_trips != 0) {
//                    return $query->total_trip;
//                } else {
//                    return '-';
//                }
//            })
//            ->editColumn('total_dns',function ($query) {
//                if ($query->total_dns != 0) {
//                    return $query->total_dns;
//                } else {
//                    return '-';
//                }
//            })
//            ->editColumn('total_rns',function ($query) {
//                if ($query->total_rns != 0) {
//                    return $query->total_rns;
//                } else {
//                    return '-';
//                }
//            })
//            ->editColumn('total_pickup_count',function ($query) {
//                if ($query->total_pickup_count != 0) {
//                    return $query->total_pickup_count;
//                } else {
//                    return '-';
//                }
//            })
            ->addColumn('action', function ($row) {
                $dropdown = '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                    <button type="button" class="dropdown-item view-day-remarks" data-date="'.$row->trip_date.'" data-vehicle="'.$row->vehicle_id.'"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-bookmark"></i></div><div class="col-9 offset-1">View Details</div></button>
                    ';
                $dropdown .= '</div>  </div>';
                return $dropdown;
            })
            ->rawColumns(['action','total_trips','total_dns','total_rns','total_pickup_count'])
            ->make(true);
    }

    public function consolidated_trips_cost_details(Request $request)
    {
        $tripIds = LocalFleetVehicleTrip::where('vehicle_id', $request->vehicle_id)
            ->where('trip_date', $request->trip_date)
            ->pluck('id');

        $costs = LocalTripVehicleCost::whereIn('trip_id', $tripIds)->get();
        $data = [];
        foreach ($costs as $c) {
            $data[] = [
                'type'    => $c->trip_type == 0 ? 'OUT' : 'IN',
                'remarks' => $c->remarks ?? '-',
                'cost'    => number_format($c->cost_amount),
                'receipt' => $c->receipt_path
                    ? asset('storage/'.$c->receipt_path)
                    : null,
                'date'    => $c->created_at->format('d M Y, h:i A')
            ];
        }

        return response()->json([
            'status' => 0,
            'data' => $data
        ]);
    }



}
