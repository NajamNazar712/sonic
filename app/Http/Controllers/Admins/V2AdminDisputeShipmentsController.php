<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\V2Dispute;
use App\Http\Models\Admin\V2DisputeImage;
use App\Http\Models\Admin\V2DisputeReason;
use App\Http\Models\Admin\V2DisputeStatus;
use App\Http\Models\City;
use App\Http\Models\Shipment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\Datatables\Datatables;
use function foo\func;

class V2AdminDisputeShipmentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(), 521);
        $cities = City::select('id', 'name')->where('status', 1)->get();
        $reasons = V2DisputeReason::all();
        $status = V2DisputeStatus::all();
        $admins = Admin::where('status', 1)->select('id', 'name')->get();
        return view('admin.v2_dispute.index')->with(['reasons' => $reasons, 'statuses' => $status, 'cities' => $cities, 'admins' => $admins]);
    }
    public function list(Request $request){
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 522);
        }
        $dispute = V2Dispute::join('shipments', 'shipments.id', '=', 'v2_disputes.shipment_id')
        ->join('users as u', 'shipments.user_id', '=', 'u.id')
        ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
        ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
        ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
        ->join('admins as ab', 'ab.id', '=', 'v2_disputes.added_by')
        ->join('v2_dispute_reasons as dr', 'dr.id', '=', 'v2_disputes.reason_id')
        ->join('v2_dispute_statuses as ds', 'ds.id', '=', 'v2_disputes.status_id')
        ->leftjoin('admins as ub', 'ub.id', '=', 'v2_disputes.updated_by')
        ->select(['v2_disputes.id as dispute_id','v2_disputes.shipment_id', 'v2_disputes.remarks', 'v2_disputes.image', 'ab.name as added_by', 'ub.name as updated_by', 'v2_disputes.created_at', 'v2_disputes.updated_at', 'shipments.tracking_number', 'shipments.actual_weight', 'shipments.amount as cod_amount', 'oc.name as origin', 'dc.name as destination', 'u.name as shipper', 'usi.poc as poc', 'dr.name as reason', 'ds.name as status', 'v2_disputes.status_id', 'v2_disputes.reason_id']);
        if (session('role_id') != 1) {
            $dispute = $dispute->where(function ($query) {
                $query->whereIn('oc.hub_id', session('hubs'))
                    ->orWhereIn('dc.hub_id', session('hubs'));
            });
        }

        $datatables = Datatables::of($dispute)
            ->addColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('amount', function($shipment){
                return number_format($shipment->amount);
            })
            ->editColumn('shipper', function ($shipment) {
                if ($shipment->booking_type_id == 4) {
                    return $shipment->shipper .' (' . $shipment->poc . ')';
                }
                else {
                    return $shipment->shipper;
                }
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
            ->addColumn('image_view',function ($dispute){
                if($dispute->image){
                    return "<a href='#' class='btn btn-block btn-outline-info mr-1 image_popup'><i class='la la-image'></i></a>";
                }
                else{
                    return '-';
                }

            })
            ->addColumn("action", function ($result) {
                if (session('role_id') == 1 || count(array_intersect([703], session('permissions'))) !== 0) {

                    $status_update = '<a href="javascript:void(0);" class="dropdown-item update"><i class="ft-plus-circle primary"></i> Update</a>';

                    $dropdown = '
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm">';

                    if (session('role_id') == 1 || in_array(703, session('permissions'))){
                        if($result->status_id < 3){

                            $dropdown .= $status_update;
                        }
                    }else{
                        return '-';
                    }
                    $dropdown .= '</div>
                      </div>
                    ';

                    return $dropdown;

                } else {
                    return '';
                }
            });

        if ($origin = $request->get('search_origin')) {
            $dispute->where('oc.id', '=', $origin);
        }
        if ($destination = $request->get('search_destination')) {
            $dispute->where('dc.id', '=', $destination);
        }

        if ($reason = $request->get('search_reason')) {
            $dispute->where('v2_disputes.reason_id', '=', $reason);
        }
        if ($status = $request->get('search_status')) {
            $dispute->where('v2_disputes.status_id', '=', $status);
        }
        if ($launched_by = $request->get('search_launched_by')) {
            $dispute->where('v2_disputes.added_by', '=', $launched_by);
        }


        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $dispute->whereBetween('v2_disputes.created_at', [$from,$to]);
        }

        return $datatables->make(true);
    }
    public function add_submit(Request $request)
    {
        $tracking_number = $request->tracking_number;

        if ($tracking_number) {
            $shipment = Shipment::where('tracking_number', $tracking_number);
            if ($shipment->exists()) {
                $shipment = $shipment->first();
                $dispute = new V2Dispute();
                $dispute->shipment_id = $shipment->id;
                $dispute->status_id = 1;
                $dispute->reason_id = $request->reason_id;
                $dispute->remarks = $request->remarks;
                $dispute->added_by = Auth::id();
                $dispute->save();

                $file_name = 'image_1';
                if ($request->has($file_name)) {
                    $image1 = $request->file($file_name);
                    $extension = 'png';
                    $random = rand(1000, 100000);
                    $now = Carbon::now();
                    $time = $now->year . '_' . $now->month;
                    $image_name = $time . $random . $shipment->id . '_1.' . $extension;


                    $directory = 'dispute_shipments';
                    Storage::disk('public')->putFileAs($directory, $image1, $image_name);

                    $dispute->image = 1;
                    $dispute->save();
                    $this->add_images($dispute->id, $image_name);
                }
                $file_name = 'image_2';
                if ($request->has($file_name)) {
                    $image2 = $request->file($file_name);
                    $extension = 'png';
                    $random = rand(1000, 100000);
                    $now = Carbon::now();
                    $time = $now->year . '_' . $now->month;
                    $image_name = $time . $random . $shipment->id . '_2.' . $extension;
                    $directory = 'dispute_shipments';
                    Storage::disk('public')->putFileAs($directory, $image2, $image_name);
                    $this->add_images($dispute->id, $image_name);
                }

                return redirect()->back()->with('success', 'Shipment Added to Dispute!');
            }
        } else {
            return redirect()->back()->with('error', 'Invalid Tracking Number');
        }
    }

    public function add_images($dispute_id, $image_name){
        $dispute_image = new V2DisputeImage();
        $dispute_image->dispute_id = $dispute_id;
        $dispute_image->image = $image_name;
        $dispute_image->added_by = Auth::id();
        $dispute_image->save();
    }

    public function dispute_update(Request $request){
        $dispute_id = $request->dispute_id;
        if($dispute_id){
            $dispute = V2Dispute::find($dispute_id);
            if($dispute){

                if($dispute->status_id == 1){
                    $dispute->status_id = 2;
                }
                else if($dispute->status_id == 2){
                    $dispute->status_id = 3;
                }
                $dispute->save();
                return response()->json(['status' => 0, 'success' => 'Status updated successfully!']);
            }
            return response()->json(['status' => 1, 'error' => 'Something went wrong!']);
        }
        return response()->json(['status' => 1, 'error' => 'Something went wrong!']);
    }

    public function dispute_images(Request $request){
        $dispute_id = $request->dispute_id;

        if($dispute_id){
            $dispute_images = V2DisputeImage::where('dispute_id', $dispute_id);
            if($dispute_images->exists()){
                $dispute_images = $dispute_images->get();
                $data = array();
                foreach ($dispute_images as $index => $dispute_image) {
                    $url = Storage::url('dispute_shipments/'.$dispute_image->image);
                    $data[] = '<img src="'.asset($url) .'">';
                }
                return response()->json(['status' => 0, 'images' => $data]);
            }
        }
    }
}
