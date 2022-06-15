<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Models\City;
use App\Http\Models\DiscountCharge;
use App\Http\Models\PackagingCharge;
use App\Http\Models\PackagingMaterialRequest;
use App\Http\Models\PackagingMaterialRequestDetail;
use App\Http\Models\PackagingMaterialRequestHistory;
use App\Http\Models\PackagingMaterialRequestStatus;
use App\Http\Models\PackagingMaterialTypes;
use App\Http\Models\PackagingMaterialTypeSizes;
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
use App\Http\Models\PackagingMaterialCart;
use App\Http\Models\ShipperPackagingMaterailType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\Types\Null_;
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
        $packaging_request_status = PackagingMaterialRequestStatus::select('id', 'name')->get();

        $status = PackagingMaterialRequestStatus::all();
        $cities = City::where('status',1)->orderBy('name')->get();
        $address = UserShippingInfo::where(['user_id'=>session('user_id'),'hidden'=>0])->with('city')->get();
        $payment_mode = PackagingPaymentMode::all();

        return view('client.packaging.flyers.index')->with(['address'=>$address,'cities'=>$cities,'payment_mode'=>$payment_mode, 'status' => $status, 'packaging_types' => $packaging_type, 'packaging_request_status' => $packaging_request_status]);
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
            ->leftjoin('shipments as s', 's.tracking_number', '=', 'packaging_material_requests.tracking_number')
            ->select(['packaging_material_requests.id as request_id','packaging_material_requests.poc','packaging_material_requests.reference_id','packaging_material_requests.created_at','ct.name as city','packaging_material_requests.address','ppm.mode','packaging_material_requests.status_id','packaging_material_requests.amount','packaging_material_requests.tracking_number','packaging_material_requests.tracking_number as tracking_number_link', 'prs.name as request_status', 's.id as shipment_id', 'packaging_material_requests.status_id as status_id'])
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
                $detail_button = '<button type="button" class="dropdown-item details" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Details</div></button>';
                $cancel_button = '<button type="button" class="dropdown-item cancel"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Cancel</div></button>';
                $dropdown .= $detail_button;
                if($packaging->status_id < 2){
                    $dropdown .= $cancel_button;
                }
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

    public function packaging_request_cancel(Request $request){
        $request_id = $request->id;

        $packaging_material_request = PackagingMaterialRequest::where('id', $request_id)->first();

        if($packaging_material_request->status_id > 1){
            return response()->json(['status' => 0, 'error' => 'Cancellation failed, Request is already confirmed']);
        }
        else{
            $packaging_material_request->status_id = 6;
            $packaging_material_request->save();

            $packaging_request_history = new PackagingMaterialRequestHistory();
            $packaging_request_history->packaging_material_request_id = $request_id;
            $packaging_request_history->status = 6;
            $packaging_request_history->updated_by_user_id = Auth::id();
            $packaging_request_history->save();
            return response()->json(['status' => 1, 'success' => 'Request cancelled successfully!']);
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

//    public function packaging_request_submit(Request $request){
//        $packaging_type_ids = explode(",",$request->packaging_type_ids);
//        $packaging_size_ids = explode(",",$request->packaging_size_ids);
//        $packaging_quantities = explode(",",$request->packaging_quantities);
//
//        $total_charges = 0;
//
//        foreach ($packaging_type_ids as $index => $packaging_type_id){
//            $charges = PackagingCharge::where('user_id',session('user_id'))->where(['type_id' => $packaging_type_id, 'size_id' => $packaging_size_ids[$index]])->latest()->first();
//            if($charges != null){
//                    $total_charges += $packaging_quantities[$index] * $charges->charges;
//            }else{
//                $charges = PackagingMaterialTypeSizes::find($packaging_size_ids[$index]);
//
//                $total_charges += $packaging_quantities[$index] * $charges->standard_charges;
//            }
//        }
//
//        $today = Carbon::today();
//
//        $discount = DiscountCharge::where('user_id', session('user_id'))->whereDate('to', '<=', $today)->whereDate('from', '>=', $today)->whereNotNull('packaging');
//
//        if ($discount->exists()) {
//            $discount = $discount->orderBy('shipping_mode_id', 'ASC')->first();
//
//            $discount_packaging = $discount->packaging;
//
//            if (strpos($discount_packaging, '%') !== FALSE) {
//                $discount_packaging = (floatval(str_replace('%', '', $discount_packaging)) / 100) * $total_charges;
//                $total_charges -= $discount_packaging;
//            }
//            else {
//                $total_charges -= floatval($discount_packaging);
//            }
//        }
//
//        if ($request->input('address_select') != 0) {
//            $address_id = $request->input('address_select');
//            $user_address = UserShippingInfo::find($address_id);
//        }
//
//        $user_id = session('user_id');
//
//        if($request->mode_of_payment == 1) {
//            if ($request->input('address_select') == 0) {
//                $result = PackagingMaterialRequest::create([
//                    'user_id' => $user_id,
//                    'city_id' => $request->new_pickup_city,
//                    'address' => $request->new_pickup_address,
//                    'poc' => $request->new_pickup_person_of_contact,
//                    'phone' => $request->new_pickup_phone_number,
//                    'amount' => $total_charges,
//                    'status_id' => 1,
//                    'packaging_payment_mode_id' => $request->mode_of_payment
//                ]);
//            } else {
//                $result = PackagingMaterialRequest::create([
//                    'user_id' => $user_id,
//                    'city_id' => $user_address->city_id,
//                    'address' => $user_address->pickup_address,
//                    'poc' => $user_address->poc,
//                    'phone' => $user_address->phone,
//                    'amount' => $total_charges,
//                    'status_id' => 1,
//                    'packaging_payment_mode_id' => $request->mode_of_payment
//
//                ]);
//            }
//            if ($result) {
//                foreach ($packaging_type_ids as $index => $packaging_type_id) {
//                    PackagingMaterialRequestDetail::create([
//                        'packaging_material_request_id' => $result->id,
//                        'type_id' => $packaging_type_id,
//                        'type_size_id' => $packaging_size_ids[$index],
//                        'quantity' => $packaging_quantities[$index],
//                    ]);
//                }
//                return redirect()->back()->with('success', 'Request submitted Successfully, The delivery for this request will be attempted to you within 2-3 working days and it cannot be cancelled after the status of this request is confirmed');
//            } else {
//                return redirect()->back()->with('error', 'Request not submitted!');
//            }
//        }
//        else{
//            if(session('account_type') == 1){
//                if(PendingPayment::where('user_id', session('user_id'))->exists()){
//                    $balance = PendingPayment::where('user_id', session('user_id'))->first()->pending_payment_shipments->sum('payable');
//
//                }else{
//                    return redirect()->back()->with('error','Can\'t  Request material!');
//                }
//
//                if($total_charges <= $balance){
//                    if ($request->input('address_select') == 0) {
//                        $result = PackagingMaterialRequest::create([
//                            'user_id'=>$user_id,
//                            'city_id'=>$request->new_pickup_city,
//                            'address'=>$request->new_pickup_address,
//                            'poc'=>$request->new_pickup_person_of_contact,
//                            'phone'=>$request->new_pickup_phone_number,
//                            'amount'=>$total_charges,
//                            'status_id'=>1,
//                            'packaging_payment_mode_id'=>$request->mode_of_payment
//                        ]);
//                    }
//                    else {
//                        $result = PackagingMaterialRequest::create([
//                            'user_id'=>$user_id,
//                            'city_id'=>$user_address->city_id,
//                            'address'=>$user_address->pickup_address,
//                            'poc'=>$user_address->poc,
//                            'phone'=>$user_address->phone,
//                            'amount'=>$total_charges,
//                            'status_id'=>1,
//                            'packaging_payment_mode_id'=>$request->mode_of_payment
//                        ]);
//                    }
//                    if($result){
//                        foreach ($packaging_type_ids as $index => $packaging_type_id){
//                            PackagingMaterialRequestDetail::create([
//                                'packaging_material_request_id' => $result->id,
//                                'type_id' => $packaging_type_id,
//                                'type_size_id' => $packaging_size_ids[$index],
//                                'quantity' => $packaging_quantities[$index],
//                            ]);
//                        }
//                        return redirect()->back()->with('success','Request submitted Successfully, The delivery for this request will be attempted to you within 2-3 working days and it cannot be cancelled after the status of this request is confirmed');
//                    }else{
//                        return redirect()->back()->with('error','Request not submitted!');
//                    }
//
//                }else{
//                    return redirect()->back()->with('error','Not enough balance!');
//                }
//            }
//            else{
//                if ($request->input('address_select') == 0) {
//                    $result = PackagingMaterialRequest::create([
//                        'user_id'=>$user_id,
//                        'city_id'=>$request->new_pickup_city,
//                        'address'=>$request->new_pickup_address,
//                        'poc'=>$request->new_pickup_person_of_contact,
//                        'phone'=>$request->new_pickup_phone_number,
//                        'amount'=>$total_charges,
//                        'status_id'=>1,
//                        'packaging_payment_mode_id'=>$request->mode_of_payment
//                    ]);
//                }
//                else {
//                    $result = PackagingMaterialRequest::create([
//                        'user_id'=>$user_id,
//                        'city_id'=>$user_address->city_id,
//                        'address'=>$user_address->pickup_address,
//                        'poc'=>$user_address->poc,
//                        'phone'=>$user_address->phone,
//                        'amount'=>$total_charges,
//                        'status_id'=>1,
//                        'packaging_payment_mode_id'=>$request->mode_of_payment
//                    ]);
//                }
//                if($result){
//                    foreach ($packaging_type_ids as $index => $packaging_type_id){
//                        PackagingMaterialRequestDetail::create([
//                            'packaging_material_request_id' => $result->id,
//                            'type_id' => $packaging_type_id,
//                            'type_size_id' => $packaging_size_ids[$index],
//                            'quantity' => $packaging_quantities[$index],
//                        ]);
//                    }
//                    return redirect()->back()->with('success','Request submitted Successfully, The delivery for this request will be attempted to you within 2-3 working days and it cannot be cancelled after the status of this request is confirmed');
//                }else{
//                    return redirect()->back()->with('error','Request not submitted!');
//                }
//            }
//        }
//
//    }
    public function packaging_request_submit(Request $request){

        // dd($request->all());
       
        $packaging_size_ids = $request->size;
        $packaging_quantities = $request->quantity;
        $packaging_type_ids = array();
        $packaging_wms_product_ids = array();
        $total_charges = 0;
        $packaging_cart = PackagingMaterialCart::where('user_id',session('user_id'))->whereIn('type_id', $request->types);

        if($packaging_cart->exists()){
            $packaging_cart = $packaging_cart->get();
        }else{
            return redirect()->route('cod.packaging.requests.index')->with('error', 'Request not submitted!');
        }
        foreach ($packaging_size_ids as $index => $packaging_size_id){
            $size = PackagingMaterialTypeSizes::find($packaging_size_ids[$index]);
            $packaging_type_id = $size->type->id;
            $packaging_type_ids[$index] = $packaging_type_id;
            $packaging_wms_product_ids[$index] = $size->wms_product_id;
            PackagingMaterialCart::where('user_id',session('user_id'))->where(['type_id' => $packaging_type_id, 'size_id' => $size->id])->latest()->first()->delete();
            $total_charges += $packaging_quantities[$index] * $size->standard_charges;
        }
        
        
        //old
        // $packaging_size_ids = $request->size;
        // $packaging_quantities = $request->quantity;
        // $packaging_type_ids = array();
        // $packaging_wms_product_ids = array();
        // $total_charges = 0;
        // $packaging_cart = PackagingMaterialCart::where('user_id',session('user_id'))->whereIn('type_id', $request->types);

        // if($packaging_cart->exists()){
        //     $packaging_cart = $packaging_cart->get();
        // }else{
        //     return redirect()->route('cod.packaging.requests.index')->with('error', 'Request not submitted!');
        // }
        // foreach ($packaging_cart as $index => $packaging_size_id){
        //     $size = PackagingMaterialTypeSizes::find($packaging_size_id->size_id);
        //     $packaging_type_id = $size->type->id;
        //     $packaging_type_ids[$index] = $packaging_type_id;
        //     $packaging_wms_product_ids[$index] = $size->wms_product_id;
        //     $charges = PackagingCharge::where('user_id',session('user_id'))->where(['type_id' => $packaging_type_id, 'size_id' => $packaging_size_id->size_id])->latest()->first();
        //     PackagingMaterialCart::where('user_id',session('user_id'))->where(['type_id' => $packaging_type_id, 'size_id' => $packaging_size_id->size_id])->latest()->first()->delete();
        //     if($charges != null){
        //             $total_charges += $packaging_quantities[$index] * $charges->charges;
        //     }else{
        //         $total_charges += $packaging_quantities[$index] * $size->standard_charges;
        //     }
        // }

        $today = Carbon::today();

        $discount = DiscountCharge::where('user_id', session('user_id'))->whereDate('to', '<=', $today)->whereDate('from', '>=', $today)->whereNotNull('packaging');

        if ($discount->exists()) {
            $discount = $discount->orderBy('shipping_mode_id', 'ASC')->first();

            $discount_packaging = $discount->packaging;

            if (strpos($discount_packaging, '%') !== FALSE) {
                $discount_packaging = (floatval(str_replace('%', '', $discount_packaging)) / 100) * $total_charges;
                $total_charges -= $discount_packaging;
            }
            else {
                $total_charges -= floatval($discount_packaging);
            }
        }

        if ($request->input('address_select') != 0) {
            $address_id = $request->input('address_select');
            $user_address = UserShippingInfo::find($address_id);
        }

        $user_id = session('user_id');

        if($request->mode_of_payment == 1) {
            if ($request->input('address_select') == 0) {
                $result = PackagingMaterialRequest::create([
                    'user_id' => $user_id,
                    'city_id' => $request->new_pickup_city,
                    'address' => $request->new_pickup_address,
                    'poc' => $request->new_pickup_person_of_contact,
                    'reference_id' => $request->new_pickup_reference_id,
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
                        'wms_product_id' => $packaging_wms_product_ids[$index],
                    ]);
                }
                return redirect()->route('cod.packaging.requests.index')->with('success', 'Request submitted Successfully, The delivery for this request will be attempted to you within 2-3 working days and it cannot be cancelled after the status of this request is confirmed');
            } else {
                return redirect()->route('cod.packaging.requests.index')->with('error', 'Request not submitted!');
            }
        }
        else{
            if(session('account_type') == 1){
                if(PendingPayment::where('user_id', session('user_id'))->exists()){
                    $balance = PendingPayment::where('user_id', session('user_id'))->first()->pending_payment_shipments->sum('payable');

                }else{
                    return redirect()->route('cod.packaging.requests.index')->with('error','Can\'t  Request material!');
                }

                if($total_charges <= $balance){
                    if ($request->input('address_select') == 0) {
                        $result = PackagingMaterialRequest::create([
                            'user_id'=>$user_id,
                            'city_id'=>$request->new_pickup_city,
                            'address'=>$request->new_pickup_address,
                            'poc'=>$request->new_pickup_person_of_contact,
                            'reference_id' => $request->new_pickup_reference_id,
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
                                'wms_product_id' => $packaging_wms_product_ids[$index]
                            ]);
                        }
                        return redirect()->route('cod.packaging.requests.index')->with('success','Request submitted Successfully, The delivery for this request will be attempted to you within 2-3 working days and it cannot be cancelled after the status of this request is confirmed');
                    }else{
                        return redirect()->route('cod.packaging.requests.index')->with('error','Request not submitted!');
                    }

                }else{
                    return redirect()->route('cod.packaging.requests.index')->with('error','Not enough balance!');
                }
            }
            else{
                if ($request->input('address_select') == 0) {
                    $result = PackagingMaterialRequest::create([
                        'user_id'=>$user_id,
                        'city_id'=>$request->new_pickup_city,
                        'address'=>$request->new_pickup_address,
                        'poc'=>$request->new_pickup_person_of_contact,
                        'reference_id' => $request->new_pickup_reference_id,
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
                            'wms_product_id' => $packaging_wms_product_ids[$index]
                        ]);
                    }
                    return redirect()->route('cod.packaging.requests.index')->with('success','Request submitted Successfully, The delivery for this request will be attempted to you within 2-3 working days and it cannot be cancelled after the status of this request is confirmed');
                }else{
                    return redirect()->route('cod.packaging.requests.index')->with('error','Request not submitted!');
                }
            }
        }

    }

    public function packaging_request_cart_index(){

        $shipper = User::find(session('user_id'));
        
        if(session('foc_account') == 1){
            $packaging_types = PackagingMaterialTypes::where('status', 1)->whereIn('packaging_type', [1, 3])->get();
        }
        else{
            $packaging_types = PackagingMaterialTypes::where('status', 1)->whereIn('packaging_type', [2, 3])->get();
        }
        

//        $user_charges = array();
        $standard_charges = array();
//        $size_charges = PackagingCharge::where('user_id', session('user_id'));
//        if($size_charges->exists()){
//            $size_charges = $size_charges->get();
//            foreach ($size_charges as $size_charge){
//                $user_charges[$size_charge->size_id] = $size_charge->charges;
//            }
//        }
        $size_charges = PackagingMaterialTypeSizes::get();
        foreach ($size_charges as $size_charge){
            $standard_charges[$size_charge->id] = $size_charge->standard_charges;
        }

        $pictures = array();

        foreach($packaging_types as $packaging_type){
            if($packaging_type->picture != NULL){
                $pictures[$packaging_type->id] = Storage::url('packaging_pictures/' . $packaging_type->picture);
            }
            else{
                $pictures[$packaging_type->id] = 'img/trax_logo.png';
            }
        }
        if(count($shipper->packaging_materails) > 0) {
            foreach ($shipper->packaging_materails as $value) {
                if ($value->packaging_material->picture != NULL) {
                    $pictures[$value->packaging_material->id] = Storage::url('packaging_pictures/' . $value->packaging_material->picture);
                } else {
                    $pictures[$value->packaging_material->id] = 'img/trax_logo.png';
                }
            }
        }

        return view('client.packaging.cart.index')->with(['packaging_types' => $packaging_types,'shipper_packaging_types' => $shipper->packaging_materails, /*'user_charges' => $user_charges*/'standard_charges' => $standard_charges, 'pictures' => $pictures]);
    }

    public function packaging_request_cart_details(Request $request){
        $size_ids = explode(',', $request->size_ids);
        $sizes = PackagingMaterialTypeSizes::whereIn('id', $size_ids)->get();

        $packaging_types = PackagingMaterialTypes::where('status', 1)->get();
        foreach($packaging_types as $packaging_type){
            if($packaging_type->picture != NULL){
                $pictures[$packaging_type->id] = Storage::url('packaging_pictures/' . $packaging_type->picture);
            }
            else{
                $pictures[$packaging_type->id] = 'img/trax_logo.png';
            }
        }
        $cities = City::where('status',1)->orderBy('name')->get();
        $address = UserShippingInfo::where(['user_id'=>session('user_id'),'hidden'=>0])->with('city')->get();
        $payment_mode = PackagingPaymentMode::all();
        return view('client.packaging.cart.details')->with(['sizes' => $sizes, 'address'=>$address,'cities'=>$cities,'payment_mode'=>$payment_mode,'size_ids'=>$size_ids, 'pictures' => $pictures]);
    }

    public function select_categories(){

        $shipper = User::find(session('user_id'));
        
        if(session('foc_account') == 1){
            $search_packaging_type = PackagingMaterialTypes::where('status', 1)->whereIn('packaging_type', [1, 3])->get();
        }
        else{
            $search_packaging_type = PackagingMaterialTypes::where('status', 1)->whereIn('packaging_type', [2, 3])->get();
        }

        return view('client.packaging.categories')->with(['search_packaging_types' => $search_packaging_type, 'shipper_packaging_types' => $shipper->packaging_materails]);

    }

    public function category_products($id){
        $shipper = User::find(session('user_id'));
        
        if(session('foc_account') == 1){
            $search_packaging_type = PackagingMaterialTypes::where('status', 1)->whereIn('packaging_type', [1, 3])->get();
        }
        else{
            $search_packaging_type = PackagingMaterialTypes::where('status', 1)->whereIn('packaging_type', [2, 3])->get();
        }
        $cart_count  = PackagingMaterialCart::where('user_id',session('user_id'))->count();
      
        $shipper = User::find(session('user_id'));
        
        if(session('foc_account') == 1){
            $packaging_types = PackagingMaterialTypes::where('status', 1)->where('category',$id)->whereIn('packaging_type', [1, 3])->get();
        }
        else{
            $packaging_types = PackagingMaterialTypes::where('status', 1)->where('category',$id)->whereIn('packaging_type', [2, 3])->get();
        }
        

//        $user_charges = array();
        $standard_charges = array();
//        $size_charges = PackagingCharge::where('user_id', session('user_id'));
//        if($size_charges->exists()){
//            $size_charges = $size_charges->get();
//            foreach ($size_charges as $size_charge){
//                $user_charges[$size_charge->size_id] = $size_charge->charges;
//            }
//        }
        $size_charges = PackagingMaterialTypeSizes::where('id', '!=',1)->get();
        foreach ($size_charges as $size_charge){
            $standard_charges[$size_charge->id] = $size_charge->standard_charges;
        }

        $pictures = array();

        foreach($packaging_types as $packaging_type){
            if($packaging_type->picture != NULL){
                $pictures[$packaging_type->id] = Storage::url('packaging_pictures/' . $packaging_type->picture);
            }
            else{
                $pictures[$packaging_type->id] = 'img/trax_logo.png';
            }
        }
        if(count($shipper->packaging_materails) > 0) {
            foreach ($shipper->packaging_materails as $value) {
                if ($value->packaging_material->picture != NULL) {
                    $pictures[$value->packaging_material->id] = Storage::url('packaging_pictures/' . $value->packaging_material->picture);
                } else {
                    $pictures[$value->packaging_material->id] = 'img/trax_logo.png';
                }
            }
        }
        if($id == 1){
            $category_name = "Packaging Materials";
        }else{
            $category_name = "Stationary Items";

        }
        return view('client.packaging.products')->with(['packaging_types' => $packaging_types,'shipper_packaging_types' => $shipper->packaging_materails, /*'user_charges' => $user_charges*/'standard_charges' => $standard_charges, 'pictures' => $pictures,'category' => $id, 'category_name' => $category_name, 'search_packaging_types' => $search_packaging_type]);
    }

    public function product_details($id){
        
        $shipper = User::find(session('user_id'));
        
        if(session('foc_account') == 1){
            $search_packaging_type = PackagingMaterialTypes::where('status', 1)->whereIn('packaging_type', [1, 3])->get();
        }
        else{
            $search_packaging_type = PackagingMaterialTypes::where('status', 1)->whereIn('packaging_type', [2, 3])->get();
        }

        $size_price =PackagingMaterialTypeSizes::where('type_id',$id)->get()->last()->standard_charges;
        $product = PackagingMaterialTypes::find($id);
        if ($product->picture != NULL) {
            $picture = Storage::url('packaging_pictures/' . $product->picture);
        } else {
            $picture = 'img/trax_logo.png';
        }   
        if ($product->picture_1 != NULL) {
            $picture1 = Storage::url('packaging_pictures/' . $product->picture_1);
        } else {
            $picture1 = 'img/trax_logo.png';
        } 
        if ($product->picture_2 != NULL) {
            $picture2 = Storage::url('packaging_pictures/' . $product->picture_2);
        } else {
            $picture2 = 'img/trax_logo.png';
        } 
        if ($product->picture_3 != NULL) {
            $picture3 = Storage::url('packaging_pictures/' . $product->picture_3);
        } else {
            $picture3 = 'img/trax_logo.png';
        } 
        if ($product->picture_4 != NULL) {
            $picture4 = Storage::url('packaging_pictures/' . $product->picture_4);
        } else {
            $picture4 = 'img/trax_logo.png';
        } 

        $cart_count  = PackagingMaterialCart::where('user_id',session('user_id'))->count();

        return view('client.packaging.details')->with(['product' => $product, 'count' => $cart_count, 'picture' => $picture, 'picture1' => $picture1, 'picture2' => $picture2, 'picture3' => $picture3, 'picture4' => $picture4,'search_packaging_types' => $search_packaging_type, 'size_price' => $size_price,'shipper_packaging_types' => $shipper->packaging_materails]);


    }

    public function get_charges(Request $request){
        $charges = PackagingMaterialTypeSizes::find($request->size_id);
        return ['status' => 0, 'charges' => $charges->standard_charges];


    }

    public function add_to_cart(Request $request){

        $check_cart = PackagingMaterialCart::where([
            'user_id' => session('user_id'),
             'type_id' => $request->type_id,
             'size_id' => $request->product_size,
             ]);
        if($check_cart->exists()){
            $check_cart = $check_cart->first();
            $check_cart->quantity = $check_cart->quantity + $request->quantity;
            $check_cart->save();
        }else{
            $cart  = new PackagingMaterialCart;
            $cart->user_id = session('user_id');
            $cart->size_id = $request->product_size;
            $cart->quantity = $request->quantity;
            $cart->type_id = $request->type_id;
            $cart->save();
        }

        if ($request->filled('add_cart')) {
            return redirect()->route('cod.packaging.requests.categories')->with('success', 'Product Added');
        }
        else {
            return redirect()->route('cod.packaging.requests.checkout')->with('success', 'Product Added');
        }
    }

    public function cart_count(){
        $cart_count  = PackagingMaterialCart::where('user_id',session('user_id'))->count();
        return ['status' => 0, 'count' => $cart_count];
    }

    public function checkout(){

        $shipper = User::find(session('user_id'));
        
        if(session('foc_account') == 1){
            $search_packaging_type = PackagingMaterialTypes::where('status', 1)->whereIn('packaging_type', [1, 3])->get();
        }
        else{
            $search_packaging_type = PackagingMaterialTypes::where('status', 1)->whereIn('packaging_type', [2, 3])->get();
        }
        $cart = PackagingMaterialCart::where('user_id',session('user_id'));
        if($cart->count() > 0){
            $cart = $cart->get();
            // $sizes = $cart->sizes;
            $cart_count  = PackagingMaterialCart::where('user_id',session('user_id'))->count();
            foreach($cart as $packaging_type){
                if($packaging_type->type->picture != NULL){
                    $pictures[$packaging_type->type->id] = Storage::url('packaging_pictures/' . $packaging_type->type->picture);
                }
                else{
                    $pictures[$packaging_type->type->id] = 'img/trax_logo.png';
                }
            }
          
            $cities = City::where('status',1)->orderBy('name')->get();
            $address = UserShippingInfo::where(['user_id'=>session('user_id'),'hidden'=>0])->with('city')->get();
            $payment_mode = PackagingPaymentMode::all();
            return view('client.packaging.checkout')->with(['cart_count' => $cart_count, 'cart' => $cart, 'address'=>$address,'cities'=>$cities,'payment_mode'=>$payment_mode, 'pictures' => $pictures, 'search_packaging_types' => $search_packaging_type,'shipper_packaging_types' => $shipper->packaging_materails]);
        }
        else{
            return redirect()->route('cod.packaging.requests.categories')->with('success', 'Product Added');
        }
  
    }
    public function remove_product(Request $request){

        PackagingMaterialCart::where('id',$request->id)->delete();
        return ['status' => 1];


    }
}
