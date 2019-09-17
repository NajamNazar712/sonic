<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\BookingType;
use App\Http\Models\Consolidation;
use App\Http\Models\ConsolidationShipments;
use App\Http\Models\Product;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class ShipperConsolidatedController extends Controller
{
    public function __construct() {
        $this->middleware('auth:web,substitute_users');

        $this->middleware('Permission');
    }

    public function consolidate_shipment_info(Request $request){
        $shipment_ids = $request->shipment_ids;
        if($shipment_ids){
            $first_shipment = Shipment::find($shipment_ids[0]);
            $shipments_info = array();
            $consolidated_shipment = array();
            $check = false;
            $settings = GlobalSettings::where('type', 'maximum_consolidation_shipments')->first();
            foreach ($shipment_ids as $index => $shipment_id){
                $already_consolidated = ConsolidationShipments::where('shipment_id', $shipment_id);
                $shipment = Shipment::leftjoin('user_shipping_infos as usi', 'usi.id', '=', 'shipments.pickup_address_id')
                    ->leftjoin('cities as oc', 'oc.id', '=','usi.city_id')
                    ->leftjoin('cities as dc', 'dc.id', '=','shipments.consignee_city_id')
                    ->leftjoin('shipment_items as si', 'si.shipment_id', '=', 'shipments.id')
                    ->leftjoin('products as p', 'p.id', '=', 'si.product_type_id')
                    ->select('shipments.id', 'shipments.tracking_number', 'shipments.order_id', 'oc.name as origin', 'dc.name as destination', 'shipments.consignee_name', 'shipments.consignee_phone_number_1', 'shipments.consignee_address', 'shipments.amount', 'p.product_name as product_type', 'shipments.created_at', 'shipments.booking_type_id', 'shipments.consignee_city_id', 'shipments.shipper_status_id')
                    ->where('shipments.id', $shipment_id)->first();
                if($already_consolidated->exists()){
                    $check = true;
                    $consolidated_shipment[$index] = $shipment->tracking_number;
                }
                else{
                   if ($shipment->shipper_status_id == 1 && $shipment->booking_type_id == 1 && $first_shipment->booking_type_id == $shipment->booking_type_id && $first_shipment->consignee_name == $shipment->consignee_name && $first_shipment->consignee_address == $shipment->consignee_address && $first_shipment->consignee_phone_number_1 == $shipment->consignee_phone_number_1 && $first_shipment->consignee_city_id == $shipment->consignee_city_id) {
                        $shipments_info[$index] = $shipment;
                    }
                    else{
                        if($shipment->shipper_status_id == 1){
                            if($shipment->booking_type_id == 1){
                                return response()->json(['status'=>0, 'error'=>'Different Consignee Shipments selected']);
                            }
                            else{
                                return response()->json(['status'=>0, 'error'=>'Service type must be Regular']);
                            }
                        }
                        else{
                            if($shipment->booking_type_id != 1){
                                return response()->json(['status'=>0, 'error'=>'Service type must be Regular']);
                            }
                            else{
                                return response()->json(['status'=>0, 'error'=>'Shipment Status must be Shipment - Booked']);
                            }
                        }
                    }
                }
            }
            if(count($shipment_ids) > $settings->setting_value){
                return response()->json(['status'=>0, 'error'=>'More than ' . $settings->setting_value . ' Shipments are not allowed!']);
            }
            else{
                if($check == true){
                    return response()->json(['status' => 2, 'consolidated_Shipments' => $consolidated_shipment]);
                }
                else{
                    return response()->json(['status' => 1, 'shipment_info' => $shipments_info]);
                }
            }
        }
    }

    public function consolidate_shipment_submit(Request $request){
        $shipment_ids = explode(',', $request->input('shipment_ids'));
        $shipments_count = count($shipment_ids);
        if($shipments_count > 1){
            $consolidation = new Consolidation();
            $consolidation->count = $shipments_count;
            $consolidation->default_shipment_id = $request->input('default-radio');
            $consolidation->save();
            foreach ($shipment_ids as $index => $shipment_id){
                $consolidation_shipment = new ConsolidationShipments();
                $consolidation_shipment->consolidation_id = $consolidation->id;
                $consolidation_shipment->shipment_id = $shipment_id;
                $consolidation_shipment->order = $index+1;
                $consolidation_shipment->save();
            }
            return redirect()->back()->with('success', 'Shipments Consolidated successfully!');
        }
        else{
            return redirect()->back()->with('error', 'Please select at least two Shipments');
        }
    }

    public function consolidation_history_index(){
        $shipment_status = ShipmentStatus::select('id','name')->get();
        $service_type = BookingType::all();
        $products = Product::select('id','product_name')->get();
        return view('client.shipment.consolidated.history')->with(['shipment_status'=>$shipment_status,'service_type'=>$service_type,'products'=>$products]);
    }

    public function consolidation_history_list(){
        $consolidation_shipments = ConsolidationShipments::leftjoin('shipments as s', 's.id', '=', 'consolidation_shipments.shipment_id')
            ->leftjoin('consolidations as c', 'c.id', '=', 'consolidation_shipments.consolidation_id')
            ->leftjoin('shipments as cs', 'cs.id', '=', 'c.default_shipment_id')
            ->leftjoin('users as u', 'u.id', '=', 's.user_id')
            ->leftjoin('user_shipping_infos as usi', 'usi.id', '=', 's.pickup_address_id')
            ->leftjoin('cities as oc', 'oc.id', '=','usi.city_id')
            ->leftjoin('cities as dc', 'dc.id', '=','s.consignee_city_id')
            ->leftjoin('shipment_items as si', 'si.shipment_id', '=', 's.id')
            ->leftjoin('products as p', 'p.id', '=', 'si.product_type_id')
            ->leftjoin('booking_types as bt', 'bt.id', '=', 's.booking_type_id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 's.id')
                    ->where('sj.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = s.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipments_journey as sjc', function ($join) {
                $join->on('sjc.shipment_id', '=', 's.id')
                    ->where('sjc.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = s.id)'));
            })
            ->leftjoin('shipment_status as ss', 'ss.id', '=', 'sjc.shipper_status_id')
            ->select('consolidation_shipments.consolidation_id as id', 's.tracking_number as tracking_number', 's.order_id as order_id', 'oc.name as origin', 'dc.name as destination', 's.consignee_name as consignee_name', 's.consignee_phone_number_1 as consignee_phone_number_1', 's.consignee_address as consignee_address', 's.amount as amount', 'p.product_name as product_type', 's.created_at', 'bt.booking_type as booking_type', 's.consignee_city_id as consignee_city_id', 'sj.created_at as arrival_date', 'u.name as shipper', 'ss.name as status', 'sjc.created_at as status_date', 'consolidation_shipments.order as order', 'c.count AS order_count', 'cs.tracking_number AS default_shipment_tracking_number', 'consolidation_shipments.created_at as consolidation_created_at')
        ->where('u.id', session('user_id'))
        ->groupBy('s.id');
        $datatables = Datatables::of($consolidation_shipments)
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('cod.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('id_padded', function ($consolidation_shipment) {
                return str_pad($consolidation_shipment->id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('consolidation', function ($consolidation_shipment) {
                return $consolidation_shipment->order . '/' . $consolidation_shipment->order_count;
            })
            ->editColumn('amount', function($consolidation_shipment){
                return number_format($consolidation_shipment->amount);
            })
            ->editColumn('default_shipment_tracking_number_link', function ($consolidation_shipment) {
                $route = route('cod.tracking.index');
                return "<u><a href='{$route}?tracking_number=$consolidation_shipment->default_shipment_tracking_number' class='tracking' target='_blank'>$consolidation_shipment->default_shipment_tracking_number</a></u>";
            })
            ->editColumn('arrival_date', function ($consolidation_shipment) {
                if($consolidation_shipment->arrival_date){
                    return $consolidation_shipment->arrival_date;
                }
                else{
                    return '-';
                }
            })
            ->editColumn('order_id',function ($consolidation_shipment){
                if($consolidation_shipment->order_id){
                    return $consolidation_shipment->order_id;
                }
                else{
                    return '-';
                }
            })
            ->addColumn('arrival_aging',function ($consolidation_shipment){
                if($consolidation_shipment->arrival_date){
                    $days = Carbon::now()->diffInDays($consolidation_shipment->arrival_date);
                    if($days == 0){
                        return "-";
                    }else{
                        return $days;
                    }
                }
                else{
                    return '-';
                }
            })
            ->addColumn('status_aging',function ($consolidation_shipment){
                    $days = Carbon::now()->diffInDays($consolidation_shipment->status_date);
                    if($days == 0){
                        return "-";
                    }else{
                        return $days;
                    }
            });
        return $datatables->make(true);
    }
}
