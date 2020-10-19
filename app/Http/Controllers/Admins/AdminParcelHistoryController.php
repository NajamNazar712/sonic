<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Admins\V2Pickup\V2AdminPickupsController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentScanningJourneyController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\Admin;
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
        $tracking_numbers = explode(',', $request->tracking_numbers);
        $shipment = Shipment::where('tracking_number', $tracking_numbers);


        $rider_name = Rider::select('id','name')->get();
        $admin_name = Admin::select('id','name')->get();
        return view('admin.parcel_history.index')->with(['rider_name' => $rider_name,'admin_name' => $admin_name]);
    }
    public function open_guilty_parcel_remarks(Request $request){

        if($request->action == 'addRemark')
        {
            parse_str($request->formData, $formData);

            $result = OpenParcelHistory::create([
                'shipment_id' => $formData['tracking_number_id'],
                'user_id'   => $formData['selected_user_id'],
                'remarks'   => $formData['remarks'],
                'amount'    => $formData['amount'],
                'date'      => $formData['date_submit']
            ]);

            if($result->id){
                $data['message'] = 'success';
            }
            else{
                $data['message'] = 'failed';
            }
            //echo json_encode($data);
            return redirect()->back()->with(['success' => 'Remarks Successfully Added!']);
        }
        else{
            $track = Shipment::where('tracking_number', $request->tracking_number)->get()->toArray();
            if(count($track)){
                $data['id'] = $track[0]['id'];
                $data['result'] = 'true';
            }
            else{
                $data['result'] = 'false';
            }
            echo \GuzzleHttp\json_encode($data);
            //return redirect()->back()->with(['status' => 1, 'success' => 'Petty Cash Statement Draft Successfully Updated!']);
        }
    }

}
