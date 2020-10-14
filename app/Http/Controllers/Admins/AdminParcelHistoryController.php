<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Admins\V2Pickup\V2AdminPickupsController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\BookingType;

use App\Http\Models\Consolidation;
use App\Http\Models\ConsolidationShipments;
use App\Http\Models\CRM\CrmRequestCaseNature;
use App\Http\Models\CRM\CrmRequestCaseNatureType;
use App\Http\Models\CRM\CrmRequestChannel;
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
    public function index(){
        $rider_name = Rider::select('id','name')->get();
        $admin_name = Admin::select('id','name')->get();
        return view('admin.parcel_history.index')->with(['rider_name' => $rider_name,'admin_name' => $admin_name]);
    }
    public function open_guilty_parcel_insert(Request $request){
        $open_parcel_complain = new OpenParcelHistory();
        $open_parcel_complain->shipment_id = $request->shipment_id;
        $open_parcel_complain = $request->user_id;
        $open_parcel_complain->remarks = $request->remarks;
        $open_parcel_complain->amount = $request->amount;
        $open_parcel_complain->date = $request->date;
        $open_parcel_complain->save();

        return redirect()->back()->with(['success' => 'Complain successfully added!']);
    }

}
