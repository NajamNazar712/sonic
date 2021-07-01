<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Admins\V2Pickup\V2AdminPickupsController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentScanningJourneyController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminRole;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\ReturnNote;
use App\Http\Models\Admin\SalePersonTag;
use App\Http\Models\BookingType;

use App\Http\Models\Consolidation;
use App\Http\Models\ConsolidationShipments;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\CRM\CrmRequestCaseNature;
use App\Http\Models\CRM\CrmRequestCaseNatureType;
use App\Http\Models\CRM\CrmRequestChannel;
use App\Http\Models\DonePaymentShipment;
use App\Http\Models\OpenParcelHistory;
use App\Http\Models\PackagingMaterialRequest;
use App\Http\Models\PackagingMaterialRequestDetail;
use App\Http\Models\PackagingMaterialRequestHistory;
use App\Http\Models\Product;
use App\Http\Models\Rider;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentPaymentStatus;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\Shipper\ShipperAirWaybillSettings;
use App\Http\Models\Shipper\User;
use App\Http\Models\ShippingMode;
use App\Http\Models\Warehouse\WarehouseFulfilmentHubs;
use App\http\Models\WarehouseStock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class AdminParcelHistoryController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }
    public function index(Request $request){
        $riders = Rider::get();
        $admins = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id' )
            ->select('a.id as id', 'a.name as name')
            ->whereNotIn('admin_roles.department_id', [1])->get();
        return view('admin.parcel_history.index')->with(['riders' => $riders, 'admins' => $admins]);
    }

    public function list(){
        $open_parcel_history = OpenParcelHistory::leftjoin('shipments as s', 's.id', '=', 'open_parcel_histories.shipment_id')
            ->leftjoin('riders as r', 'r.id', '=', 'open_parcel_histories.user_id')
            ->leftjoin('cities as c', 'c.id', '=', 'r.city_id')
            ->leftjoin('admins as a', 'a.id', '=', 'open_parcel_histories.user_id')
            ->select('open_parcel_histories.id as id', 's.tracking_number as tracking_number','r.name as rider_name', 'a.name as admin_name', 'open_parcel_histories.user_mode as user_mode', 'open_parcel_histories.user_id as user', 'open_parcel_histories.remarks as remarks', 'open_parcel_histories.amount as amount', 'open_parcel_histories.date', 'c.name as city');

        $datatables = Datatables::of($open_parcel_history)
            ->editColumn('tracking_number_link', function ($open_parcel) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$open_parcel->tracking_number' class='tracking' target='_blank'>$open_parcel->tracking_number</a></u>";
            })
            ->editColumn('user_mode', function($open_parcel) {
            return ($open_parcel->user_mode == 1) ? 'Rider' : 'Admin';
            })
            ->editColumn('user', function($open_parcel) {
            return ($open_parcel->user_mode == 1) ? $open_parcel->rider_name : $open_parcel->admin_name;
            })
            ->editColumn('date', function($open_parcel) {
                $date = str_replace('00:00:00', '', $open_parcel->date);
                return $date;
            })
            ->filterColumn('user', function($query, $keyword) {
                $query->where(function ($sub_query) use ($keyword) {
                    $sub_query->where('a.name', 'like', '%' . $keyword . '%');
                })
                ->orWhere(function ($sub_query) use ($keyword) {
                    $sub_query->where('r.name', 'like', '%' . $keyword . '%');
                });
            });
        return $datatables->make(true);
    }

    public function shipment_get_info(Request $request){
        $tracking_number = $request->tracking_number;
        $shipment = Shipment::where('tracking_number', $tracking_number);
        if($shipment->exists()){
            $shipment = $shipment->first();
            return response()->json(['status' => 1, 'shipment_id' => $shipment->id]);
        }
        else{
            return response()->json(['status' => 0, 'error' => 'Tracking Number invalid!']);
        }
    }
    public function remarks_submit(Request $request){
        $open_parcel_existing = OpenParcelHistory::where('shipment_id', $request->shipment_id);
        if($open_parcel_existing->exists()){
            return redirect()->back()->with('error', 'Open Parcel Tracking Number already exists!');
        }
        if($request->select_user_mode == 1){
            $user_id = $request->select_rider;
        }
        else{
            $user_id = $request->select_admin;
        }
        $open_parcel_history = new OpenParcelHistory();
        $open_parcel_history->shipment_id = $request->shipment_id;
        $open_parcel_history->user_mode = $request->select_user_mode;
        $open_parcel_history->user_id = $user_id;
        $open_parcel_history->remarks = $request->remarks;
        $open_parcel_history->amount = $request->amount;
        $open_parcel_history->date = $request->date_formatted;
        $open_parcel_history->save();

        return redirect()->back()->with('success', 'Open Parcel Remarks updated successfully');
    }
}
