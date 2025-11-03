<?php

namespace App\Http\Controllers\Admins\Reports;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admins\ActivityTrailController;

class SubHubStationVisibilityController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('Permission');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 835);

        $statuses = DB::connection('reports')->table('shipment_status')->whereIn('id', [1, 55])->get();
        return view('admin.reports.sub_hub')->with(['statuses' => $statuses]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {

        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 836);
        }

        $from = $request->get('search_date_from');
        $to = $request->get('search_date_to');

        if ($from && $to) {
            $from = Carbon::parse($from)->toDateString();
            $to = Carbon::parse($to)->toDateString();
        } else {
            $from = Carbon::now()->toDateString();
            $to = Carbon::now()->toDateString();
        }

        $data = DB::table('shipments')
        ->select(['shipments.tracking_number','dc.name as destination' , 'shipments.consignee_address as consignee_address', 'oc.name as origin', 'h.name as hub','ca.name as sub_hub','ss.name as current_status'])
        ->leftJoin('consignee_address_areas as caa' , 'caa.shipment_id', 'shipments.id')
        ->leftJoin('city_areas as ca' ,'ca.id' , 'caa.city_area_id')
        ->leftJoin('shipment_status as ss', 'ss.id','shipments.shipper_status_id')
        ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
        ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
        ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
        ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
        ->whereBetween('shipments.created_at', [$from, $to]);
    
        if ($status = $request->get('search_status')) {
            $data->where('ss.id', '=', $status);
        } else{
            $data->whereIn('ss.id', [1,55]);
        }

        $datatable = Datatables::of($data);
        return $datatable->make(true);


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
