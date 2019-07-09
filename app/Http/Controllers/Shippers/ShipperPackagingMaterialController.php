<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Models\City;
use App\Http\Models\DiscountCharge;
use App\Http\Models\PackagingCharge;
use App\Http\Models\PackagingMaterialRequest;
use App\Http\Models\PackagingMaterialRequestDetail;
use App\http\models\PackagingMaterialRequestStatus;
use App\Http\models\PackagingMaterialTypes;
use App\Http\models\PackagingMaterialTypeSizes;
use App\Http\Models\PackagingPaymentMode;
use App\Http\Models\PendingPayment;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentItem;
use App\Http\Models\Warehouse\Warehouse;
use App\Http\Models\Warehouse\WarehouseFulfilmentHubs;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;

class ShipperPackagingMaterialController extends Controller
{
    public function __construct() {
        $this->middleware('auth:web,substitute_users');

        $this->middleware('Permission');
    }
    public function packaging_request(){
        $packaging_type = PackagingMaterialTypes::where('status', 1)->get();
//        $packaging_size = PackagingMaterialTypeSizes::all('id','size','type_id')->groupBy('type_id');

        $status = PackagingMaterialRequestStatus::all();
        $cities = City::where('status',1)->orderBy('name')->get();
        $address = UserShippingInfo::where(['user_id'=>session('user_id'),'hidden'=>0])->with('city')->get();
        $payment_mode = PackagingPaymentMode::all();

        return view('client.packaging.flyers.index')->with(['address'=>$address,'cities'=>$cities,'payment_mode'=>$payment_mode, 'status' => $status, 'packaging_types' => $packaging_type]);
    }
    public function add_pickup_address($user_id, $address, $person_of_contact, $phone_number, $email_address, $city_id, $status) {

        $user_shipping_info = new UserShippingInfo();

        $user_shipping_info->user_id = $user_id;
        $user_shipping_info->pickup_address = $address;
        $user_shipping_info->poc = $person_of_contact;
        $user_shipping_info->phone = $phone_number;
        $user_shipping_info->email = $email_address;
        $user_shipping_info->city_id = $city_id;
        $user_shipping_info->status = $status;

        $user_shipping_info->save();

        return $user_shipping_info->id;
    }
    public function packaging_request_list(Request $request){
        $requests = PackagingMaterialRequest::join('cities as ct','ct.id','=','packaging_material_requests.city_id')
            ->join('packaging_payment_modes as ppm','ppm.id','=','packaging_material_requests.packaging_payment_mode_id')
            ->join('packaging_material_request_statuses as prs', 'prs.id','=', 'packaging_material_requests.status_id')
            ->select(['packaging_material_requests.id as request_id','packaging_material_requests.created_at','ct.name as city','packaging_material_requests.address','ppm.mode','packaging_material_requests.status_id','packaging_material_requests.amount','packaging_material_requests.tracking_number','packaging_material_requests.tracking_number as tracking_number_link', 'prs.name as request_status'])
        ->where('packaging_material_requests.user_id', session('user_id'));

        return Datatables::of($requests)
            ->editColumn('tracking_number_link',function ($shipments){
                $route = route('cod.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })

            ->editColumn('amount', function($requests){
                return number_format($requests->amount);
            })
            ->addColumn('aging', function ($requests){
                return Carbon::parse($requests->created_at)->diffInDays();
            })
            ->addColumn('action',function ($packaging) {
//                if (($packaging->status == 0) && (session('role_id') == 1 || in_array(80, session('permissions')))) {
                $dropdown = "
                        <div class='btn-group'>
                           <button type='button' class='btn btn-sm btn-success dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Actions</button>
                            <div class='dropdown-menu dropdown-menu-sm'>";
                $detail_button = '<a href="javascript:void(0);" class="dropdown-item primary details" ><i class="ft-list"></i> Details</a>';
                $dropdown .= $detail_button;
                $dropdown .= "
                            </div>
                        </div>
                    ";
                return $dropdown;
//                }
//                else {
//                    return '';
//                }
            })
            ->make(true);


    }

    public function packaging_request_details(Request $request){
        $request_id = $request->id;
        $details = array();
        if($request_id){
            $material_details = PackagingMaterialRequestDetail::where('packaging_material_request_id', $request_id);
            if($material_details->exists()){
                $details = $material_details->with(['packaging_type', 'packaging_type_size'])->get();

                return response()->json(['status' => 0, 'details' => $details]);
            }else{
                return response()->json(['status' => 1, 'error' => 'No Details found!']);
            }
        }
    }

    public function packaging_request_sizes(Request $request){
        $id = $request->id;
        if($id){
            $sizes = PackagingMaterialTypeSizes::where('type_id', $id);
            if($sizes->exists()){
                $sizes = $sizes->select('id', 'size')->get();
                return response()->json(['status' => 0, 'sizes' => $sizes]);
            }else{
                $type = PackagingMaterialTypes::find($id)->type;
                return response()->json(['status' => 1, 'error' => 'No Size found for type: '.$type]);
            }
        }
    }

    public function packaging_request_submit(Request $request){

        $packaging_type_ids = explode(",",$request->packaging_type_ids);
        $packaging_size_ids = explode(",",$request->packaging_size_ids);
        $packaging_quantities = explode(",",$request->packaging_quantities);

        $total_charges = 0;

        foreach ($packaging_type_ids as $index => $packaging_type_id){
            $charges = PackagingCharge::where('user_id',session('user_id'))->where(['type_id' => $packaging_type_id, 'size_id' => $packaging_size_ids[$index]])->latest()->first();
            if($charges != null){
                    $total_charges += $packaging_quantities[$index] * $charges->charges;
            }
        }
        $today = Carbon::today();

        if($discount = DiscountCharge::where('user_id', session('user_id'))->where('shipping_mode_id',1)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today)->exists()){
            $discount = $discount->first();
        }else if($discount = DiscountCharge::where('user_id', session('user_id'))->where('shipping_mode_id',2)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today)->exists()){
            $discount = $discount->first();
        }else if($discount = DiscountCharge::where('user_id', session('user_id'))->where('shipping_mode_id',3)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today)->exists()){
            $discount = $discount->first();
        }else if($discount = DiscountCharge::where('user_id', session('user_id'))->where('shipping_mode_id',4)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today)->exists()){
            $discount = $discount->first();
        }
        if(!empty($discount->packaging)){
            $discount_packaging = $discount->packaging;
            if (strpos($discount_packaging, '%') !== FALSE) {
                $discount_packaging = (floatval(str_replace('%', '', $discount_packaging)) / 100) * $total_charges;
            }
            else {
                $discount_packaging += floatval($discount_packaging);
            }
            $total_charges = $discount_packaging;
        }

        if ($request->input('address_select') == 0) {
            $city_id = $request->new_pickup_city;
            $city_hub = City::where('id', $city_id)->first();
            $hub_id = $city_hub->hub_id;
        }
        else{
            $address_id = $request->input('address_select');
            $user_address = UserShippingInfo::find($address_id);
            $city_hub = City::where('id', $user_address->city_id)->first();
            $hub_id = $city_hub->hub_id;
        }

        $fulfilment_hub = WarehouseFulfilmentHubs::where('hub_id', $hub_id);

        if (!$fulfilment_hub->exists()) {
            return redirect()->back()->with('error', 'Warehouse does\'nt exists for requested hub!');
        } else {
            $fulfilment_hub = $fulfilment_hub->first();
        }

        $warehouse_id = $fulfilment_hub->warehouse_id;

        $warehouse = Warehouse::where('id', $warehouse_id)->first();

        $warehouse_hub_id = $warehouse->hub_id;
        $hub = City::where('id', $warehouse_hub_id)->first();

        $user_id = session('user_id');

        $pickup_address_office = 'Trax Office ' . $hub->name;
        $pickup_address_email = 'Info@Trax.pk';
        $pickup_address_poc = 'Trax Logistics';
        $pickup_address_phone = '0213-877-22-22';

        $pickup_address_id = $this->add_pickup_address($user_id, $pickup_address_office, $pickup_address_poc, $pickup_address_phone, $pickup_address_email, $warehouse_hub_id, 0);
        $trax_address = UserShippingInfo::find($pickup_address_id);

        $now = Carbon::today();

        $details = '';

        $details = substr($details, 0, -2);

        $shipper_details = User::where('id', $user_id)->select('name', 'poc', 'phone', 'email')->first();
        $shipment_consignee_name = "Packaging Material to $shipper_details->name";

        if($request->mode_of_payment == 1) {
            if ($request->input('address_select') == 0) {
                $result = PackagingMaterialRequest::create([
                    'user_id' => $user_id,
                    'city_id' => $request->new_pickup_city,
                    'address' => $request->new_pickup_address,
                    'poc' => $request->new_pickup_person_of_contact,
                    'phone' => $request->new_pickup_phone_number,
                    'amount' => $total_charges,
                    'status_id' => 1,
                    'packaging_payment_mode_id' => $request->mode_of_payment
                ]);
            } else {
                $result = PackagingMaterialRequest::create([
                    'user_id' => $user_id,
                    'city_id' => $user_address->city_id,
                    'address' => $user_address->pickup_address,
                    'poc' => $user_address->poc,
                    'phone' => $user_address->phone,
                    'amount' => $total_charges,
                    'status_id' => 1,
                    'packaging_payment_mode_id' => $request->mode_of_payment

                ]);
            }
            if ($result) {
                foreach ($packaging_type_ids as $index => $packaging_type_id) {
                    PackagingMaterialRequestDetail::create([
                        'packaging_material_request_id' => $result->id,
                        'type_id' => $packaging_type_id,
                        'type_size_id' => $packaging_size_ids[$index],
                        'quantity' => $packaging_quantities[$index],
                    ]);
                }

                if ($request->input('address_select') == 0) {
                    $shipment = $this->book($user_id, 1, $trax_address->id, 1, $request->new_pickup_city, $shipment_consignee_name, $request->new_pickup_address, $request->new_pickup_phone_number, null, null, null, 0, $now, null, 1, 1, null, $total_charges, 1, 1, 1);

                    $new_tracking_number = $this->generate_tracking_number($shipment->id, $trax_address->city_id, $request->new_pickup_city);
                }
                else{
                    $shipment = $this->book($user_id, 1, $trax_address->id, 1, $user_address->city_id, $shipment_consignee_name, $user_address->pickup_address, $user_address->phone, null, null, null, 0, $now, null, 1, 1, null, $total_charges, 1, 1, 1);

                    $new_tracking_number = $this->generate_tracking_number($shipment->id, $trax_address->city_id, $user_address->city_id);
                }



                $this->add_item($shipment->id, 24, $details, 1, null, 0, 0);

                PackagingMaterialRequest::where('id', $result->id)->update([
                    'tracking_number' => $new_tracking_number
                ]);


                ShipmentsJourneyController::add($shipment->id, 1, 1, NULL, NULL, $user_id, NULL);

                ShipmentChargesController::packaging_material($shipment->id, $request->mode_of_payment, $total_charges);

                return redirect()->back()->with('success', 'Request submitted Successfully, The delivery for this request will be attempted to you within 2-3 working days and it cannot be cancelled after the status of this request is confirmed');
            } else {
                return redirect()->back()->with('error', 'Request not submitted!');
            }
        }
        else{
            if(PendingPayment::where('user_id', session('user_id'))->exists()){
                $balance = PendingPayment::where('user_id', session('user_id'))->first()->pending_payment_shipments->sum('payable');

            }else{
                return redirect()->back()->with('error','Can\'t  Request material!');
            }

            if($total_charges <= $balance){
                if ($request->input('address_select') == 0) {
                    $result = PackagingMaterialRequest::create([
                        'user_id'=>$user_id,
                        'city_id'=>$request->new_pickup_city,
                        'address'=>$request->new_pickup_address,
                        'poc'=>$request->new_pickup_person_of_contact,
                        'phone'=>$request->new_pickup_phone_number,
                        'amount'=>$total_charges,
                        'status_id'=>1,
                        'packaging_payment_mode_id'=>$request->mode_of_payment
                    ]);
                }
                else {
                    $result = PackagingMaterialRequest::create([
                        'user_id'=>$user_id,
                        'city_id'=>$user_address->city_id,
                        'address'=>$user_address->pickup_address,
                        'poc'=>$user_address->poc,
                        'phone'=>$user_address->phone,
                        'amount'=>$total_charges,
                        'status_id'=>1,
                        'packaging_payment_mode_id'=>$request->mode_of_payment
                    ]);
                }
                if($result){
                    foreach ($packaging_type_ids as $index => $packaging_type_id){
                        PackagingMaterialRequestDetail::create([
                            'packaging_material_request_id' => $result->id,
                            'type_id' => $packaging_type_id,
                            'type_size_id' => $packaging_size_ids[$index],
                            'quantity' => $packaging_quantities[$index],
                        ]);
                    }

                    if ($request->input('address_select') == 0) {
                        $shipment = $this->book($user_id, 1, $trax_address->id, 1, $request->new_pickup_city, $shipment_consignee_name, $request->new_pickup_address, $request->new_pickup_phone_number, null, null, null, 0, $now, null, 1, 1, null, $total_charges, 1, 1, 1);

                        $new_tracking_number = $this->generate_tracking_number($shipment->id, $trax_address->city_id, $request->new_pickup_city);
                    }
                    else{
                        $shipment = $this->book($user_id, 1, $trax_address->id, 1, $user_address->city_id, $shipment_consignee_name, $user_address->pickup_address, $user_address->phone, null, null, null, 0, $now, null, 1, 1, null, 0, 1, 1, 1);

                        $new_tracking_number = $this->generate_tracking_number($shipment->id, $trax_address->city_id, $user_address->city_id);
                    }

                    $this->add_item($shipment->id,24,$details,1,null,0,0);

                    PackagingMaterialRequest::where('id', $result->id)->update([
                        'tracking_number' => $new_tracking_number
                    ]);


                    ShipmentsJourneyController::add($shipment->id, 1, 1, NULL, NULL, $user_id, NULL);

                    ShipmentChargesController::packaging_material($shipment->id, $request->mode_of_payment, $total_charges);

                    return redirect()->back()->with('success','Request submitted Successfully, The delivery for this request will be attempted to you within 2-3 working days and it cannot be cancelled after the status of this request is confirmed');
                }else{
                    return redirect()->back()->with('error','Request not submitted!');
                }

            }else{
                return redirect()->back()->with('error','Not enough balance!');
            }
        }

    }

    private function book($user_id,$service_type_id, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $pickup_date, $special_instructions, $estimated_weight, $shipping_mode_id, $same_day_timing_id, $amount, $payment_mode_id,$shipper_status_id,$consignee_status_id) {
        $shipment = new Shipment();

        $shipment->user_id = $user_id;
        $shipment->booking_type_id = $service_type_id;
        $shipment->pickup_address_id = $pickup_address_id;
        $shipment->information_display = $information_display;

        $shipment->consignee_city_id = $consignee_city_id;
        $shipment->consignee_name = $consignee_name;
        $shipment->consignee_address = $consignee_address;
        $shipment->consignee_phone_number_1 = $consignee_phone_number_1;
        $shipment->consignee_phone_number_2 = $consignee_phone_number_2;
        $shipment->consignee_email = $consignee_email_address;

        $shipment->order_id = $order_id;
        $shipment->package_type = $package_type;
        $shipment->pickup_date = $pickup_date;
        $shipment->special_instructions = $special_instructions;


        $shipment->estimated_weight = $estimated_weight;
        $shipment->shipping_mode_id = $shipping_mode_id;
        $shipment->same_day_timing_id = $same_day_timing_id;

        $shipment->amount = $amount;
        $shipment->payment_mode_id = $payment_mode_id;
        $shipment->shipper_status_id = $shipper_status_id;
        $shipment->consignee_status_id = $consignee_status_id;

        $shipment->packaging_material_request = 1;

        $shipment->save();

        return $shipment;
    }
    private function generate_tracking_number($shipment_id, $pickup_city_id, $consignee_city_id) {
        $shipment = Shipment::find($shipment_id);

        $tracking_number = $pickup_city_id . $consignee_city_id . str_pad($shipment_id, 6, '0', STR_PAD_LEFT);

        $shipment->tracking_number = $tracking_number;

        $shipment->save();

        return $tracking_number;
    }
    private function add_item($shipment_id, $product_type_id, $item_description, $item_quantity, $price, $insurance, $type) {
        $shipment_item = new ShipmentItem();

        $shipment_item->shipment_id = $shipment_id;
        $shipment_item->product_type_id = $product_type_id;
        $shipment_item->description = $item_description;
        $shipment_item->quantity = $item_quantity;
        $shipment_item->price = $price;
        $shipment_item->insurance = $insurance;
        $shipment_item->type = $type;

        $shipment_item->save();
    }
}
