<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Controllers\Admins\ShipmentChargesController;

use App\Http\Models\Admin\PackagingMaterialStockHead;
use App\Http\Models\Admin\PackagingMaterialStockHub;
use App\Http\Models\Admin\PackagingStockHistory;
use App\Http\Models\CargoConsignment;
use App\Http\Models\City;
use App\Http\Models\PackagingCharge;
use App\Http\Models\PackagingMaterialRequest;
use App\Http\models\PackagingMaterialTypes;
use App\Http\Models\PackagingMaterialTypesHistory;
use App\Http\models\PackagingMaterialTypeSizes;
use App\Http\Models\PackagingMaterialTypeSizesHistory;
use App\Http\Models\PackagingPaymentMode;
use App\Http\Models\PendingPayment;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentItem;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\Warehouse\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;

class AdminPackagingMaterialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function packaging_index(){
        $packaging = PackagingMaterialStockHead::latest()->first();
        return view('admin.materials.flyers.index')->with('packaging',$packaging);
    }
    public function packaging_list(Request $request){
        $packaging = PackagingStockHistory::leftjoin('cities','cities.id','=','packaging_stock_histories.hub_id')
            ->join('admins as ad','ad.id','=','packaging_stock_histories.admin_id')
            ->select(['packaging_stock_histories.id as psh_id','packaging_stock_histories.reference_number','packaging_stock_histories.entry_type','packaging_stock_histories.created_at','packaging_stock_histories.small_flyers','packaging_stock_histories.medium_flyers','packaging_stock_histories.large_flyers','packaging_stock_histories.boxes','ad.name as admin','cities.name as hub']);
        return Datatables::of($packaging)
            ->editColumn('small_flyers', function($packaging){
                return number_format($packaging->small_flyers);
            })
            ->editColumn('medium_flyers', function($packaging){
                return number_format($packaging->medium_flyers);
            })
            ->editColumn('large_flyers', function($packaging){
                return number_format($packaging->large_flyers);
            })
            ->editColumn('boxes', function($packaging){
                return number_format($packaging->boxes);
            })
            ->editColumn('entry_type',function($packaging){
                if($packaging->entry_type == 0){
                    return "Inbound";
                }
                else if($packaging->entry_type == 1){
                    return "Outbound";
                }
            })
            ->make(true);
    }
    public function add_stock(Request $request){
        $reference_number = $request->invoice_number;
        $sm_quantity = ($request->add_stock_smflyer != null)? $request->add_stock_smflyer:0;
        $md_quantity = ($request->add_stock_mdflyer != null)? $request->add_stock_mdflyer:0;
        $lg_quantity = ($request->add_stock_lgflyer != null)? $request->add_stock_lgflyer:0;
        $box_quantity = ($request->add_stock_boxes != null)? $request->add_stock_boxes:0;
        if($reference_number != null){
            $packaging = PackagingMaterialStockHead::latest()->first();
            if($packaging){
                $small = $packaging->small_flyers;
                $medium = $packaging->medium_flyers;
                $large = $packaging->large_flyers;
                $box = $packaging->boxes;
                $small += $sm_quantity;
                $medium += $md_quantity;
                $large += $lg_quantity;
                $box += $box_quantity;
              $packaging_head =  PackagingMaterialStockHead::create([
                   'small_flyers'=>$small,
                   'medium_flyers'=>$medium,
                   'large_flyers'=>$large,
                   'boxes'=>$box
                ]);
                if($packaging_head){
                    PackagingStockHistory::create([
                        'admin_id'=>Auth::id(),
                        'small_flyers'=>$sm_quantity,
                        'medium_flyers'=>$md_quantity,
                        'large_flyers'=>$lg_quantity,
                        'boxes'=>$box_quantity,
                        'entry_type'=>0,
                        'reference_number'=>$reference_number
                    ]);
                }
                return redirect()->back()->with('success','Stock added successfully!');
            }else{
                return redirect()->back()->with('error','No previous record found in database!');

            }
        }else{
            return redirect()->back()->with('error','No invoice number entered!');
        }
    }
    public function fetch_cities(Request $request){
        $cities = City::where(['hub'=>1,'status'=>1])->select('id','name')->get();
        return response()->json(['status'=>1,'cities'=>$cities]);

    }
    public function send_stock(Request $request){
//        return $request;
        $small = 0; $medium = 0; $large = 0; $box = 0;
        $sm_quantity = ($request->send_stock_smflyer != null)? $request->send_stock_smflyer:0;
        $md_quantity = ($request->send_stock_mdflyer != null)? $request->send_stock_mdflyer:0;
        $lg_quantity = ($request->send_stock_lgflyer != null)? $request->send_stock_lgflyer:0;
        $box_quantity = ($request->send_stock_boxes != null)? $request->send_stock_boxes:0;

        $hub_id = $request->city_select;
        $reference_number = false;
        $reference_number = $request->invoice_number;
        if($reference_number != 0){
            $cargo_id = CargoConsignment::where('id',$reference_number)->where('status_id','!=',3)->exists();
        }else if($reference_number == 0){
            $cargo_id = true;
        }
        if($cargo_id){
            $packaging = PackagingMaterialStockHub::where('hub_id',$hub_id);
            if($packaging->exists()){
                $packaging = $packaging->first();
                if($sm_quantity != 0  || $md_quantity != 0 || $lg_quantity != 0 || $box_quantity != 0){
                    $result = $this->sub_head_stock($sm_quantity,$md_quantity,$lg_quantity,$box_quantity);
                }else{
                    return redirect()->back()->with('error','Canot send 0 Stock!');
                }
                $small = $packaging->small_flyers;
                $medium = $packaging->medium_flyers;
                $large = $packaging->large_flyers;
                $box = $packaging->boxes;

                if($result == true) {
                    $small += $sm_quantity;
                    $medium += $md_quantity;
                    $large += $lg_quantity;
                    $box += $box_quantity;
                    $packaging_hub = PackagingMaterialStockHub::where('hub_id', $hub_id)->update([
                        'small_flyers' => $small,
                        'medium_flyers' => $medium,
                        'large_flyers' => $large,
                        'boxes' => $box
                    ]);
                    if ($packaging_hub) {
                        PackagingStockHistory::create([
                            'admin_id' => Auth::id(),
                            'small_flyers' => $sm_quantity,
                            'medium_flyers' => $md_quantity,
                            'large_flyers' => $lg_quantity,
                            'boxes' => $box_quantity,
                            'entry_type' => 1,
                            'hub_id' => $hub_id,
                            'reference_number' => $reference_number
                        ]);
                    }
                    return redirect()->back()->with('success', 'Stock added successfully!');
                }else{
                    return redirect()->back()->with('error','Stock looks short, check again!');

                }
            }else{
                if($sm_quantity != 0  || $md_quantity != 0 || $lg_quantity != 0 || $box_quantity != 0){
                    $result = $this->sub_head_stock($sm_quantity,$md_quantity,$lg_quantity,$box_quantity);
                }else{
                    return redirect()->back()->with('error','Canot send 0 Stock!');
                }
                if($result == true) {
                    $packaging_hub = PackagingMaterialStockHub::create([
                        'hub_id' => $hub_id,
                        'small_flyers' => $sm_quantity,
                        'medium_flyers' => $md_quantity,
                        'large_flyers' => $lg_quantity,
                        'boxes' => $box_quantity
                    ]);
                    if ($packaging_hub) {
                        PackagingStockHistory::create([
                            'admin_id' => Auth::id(),
                            'small_flyers' => $sm_quantity,
                            'medium_flyers' => $md_quantity,
                            'large_flyers' => $lg_quantity,
                            'boxes' => $box_quantity,
                            'entry_type' => 1,
                            'hub_id' => $hub_id,
                            'reference_number' => $reference_number
                        ]);
                    }
                    return redirect()->back()->with('success', 'Stock added successfully!');
                }else{
                    return redirect()->back()->with('error','Stock looks short, check again!');
                }
            }
        }else{
            return redirect()->back()->with('error','Cargo ID wrong or already received!');
        }
    }
    protected function sub_head_stock($small,$medium,$large,$box){
        $head_stocks = PackagingMaterialStockHead::latest()->first();
        $small_flyers = 0; $medium_flyers = 0; $medium_flyers = 0; $large_flyers = 0;
        $small_flyers = $head_stocks->small_flyers;
        $medium_flyers = $head_stocks->medium_flyers;
        $large_flyers = $head_stocks->large_flyers;
        $boxes = $head_stocks->boxes;
        if($small <= $small_flyers && $medium <= $medium_flyers && $large <= $large_flyers && $box <= $boxes){

            $small_flyers -= $small;
            $medium_flyers -= $medium;
            $large_flyers -= $large;
            $boxes -= $box;
            $packaging_head =  PackagingMaterialStockHead::create([
                'small_flyers'=>$small_flyers,
                'medium_flyers'=>$medium_flyers,
                'large_flyers'=>$large_flyers,
                'boxes'=>$boxes
            ]);
            if($packaging_head){
                return 1;
            }
        }else{
            return 0;
        }
    }
    protected function sub_hub_stock($hub,$small,$medium,$large,$box){
        $head_stocks = PackagingMaterialStockHub::where('hub_id',$hub)->first();
        $small_flyers = 0; $medium_flyers = 0; $medium_flyers = 0; $large_flyers = 0;
        $small_flyers = $head_stocks->small_flyers;
        $medium_flyers = $head_stocks->medium_flyers;
        $large_flyers = $head_stocks->large_flyers;
        $boxes = $head_stocks->boxes;
        if($small <= $small_flyers && $medium <= $medium_flyers && $large <= $large_flyers && $box <= $boxes){

            $small_flyers -= $small;
            $medium_flyers -= $medium;
            $large_flyers -= $large;
            $boxes -= $box;
            $packaging_head =  PackagingMaterialStockHub::where('hub_id',$hub)->update([
                'small_flyers'=>$small_flyers,
                'medium_flyers'=>$medium_flyers,
                'large_flyers'=>$large_flyers,
                'boxes'=>$boxes
            ]);
            if($packaging_head){
                return 1;
            }
        }else{
            return 0;
        }
    }
    public function request_index(Request $request){
        $payment_mode = PackagingPaymentMode::all();
        $packaging = PackagingMaterialStockHead::latest()->first();
        return view('admin.materials.requests.index')->with(['packaging'=>$packaging,'payment_mode'=>$payment_mode]);
    }
    public function request_list(Request $request){
        $requests = PackagingMaterialRequest::join('cities as ct','ct.id','=','packaging_material_requests.city_id')
            ->join('users as u','u.id','=','packaging_material_requests.user_id')
            ->join('packaging_payment_modes as ppm','ppm.id','=','packaging_material_requests.packaging_payment_mode_id')
            ->select(['packaging_material_requests.id as request_id','u.name as shipper','packaging_material_requests.created_at','ct.name as city','packaging_material_requests.small_flyers','packaging_material_requests.medium_flyers','packaging_material_requests.large_flyers','packaging_material_requests.boxes','packaging_material_requests.address','ppm.mode','packaging_material_requests.status','packaging_material_requests.amount','packaging_material_requests.tracking_number','packaging_material_requests.tracking_number as tracking_number_link']);

        if(session('department_id') == 7){
            if(session('role_id') != 4 ){
                $requests = $requests->whereIn('u.id', session('tagged_shippers'));
            }
        }
        return Datatables::of($requests)
            ->editColumn('tracking_number_link',function ($shipments){
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('small_flyers', function($packaging){
                return number_format($packaging->small_flyers);
            })
            ->editColumn('medium_flyers', function($packaging){
                return number_format($packaging->medium_flyers);
            })
            ->editColumn('large_flyers', function($packaging){
                return number_format($packaging->large_flyers);
            })
            ->editColumn('boxes', function($packaging){
                return number_format($packaging->boxes);
            })
            ->editColumn('amount', function($shipment){
                return number_format($shipment->amount);
            })
            ->editColumn('status',function($packaging){
                if($packaging->status == 0){
                    return "Booked";
                }
                else if($packaging->status == 1){
                    return "Dispatched";
                }
            })
            ->addColumn('action',function ($packaging) {
                if (($packaging->status == 0) && (session('role_id') == 1 || in_array(80, session('permissions')))) {
                    $dropdown = '
                      <span class="dropdown">
                        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false"><i class="ft-settings"></i></button>
                        <div class="dropdown-menu open-left arrow">
                            <a class="dropdown-item dispatch"><i class="ft-fast-forward primary"></i> Dispatch</a>
                        </div>
                      </span>
                    ';

                    return $dropdown;
                }
                else {
                    return '';
                }
            })
            ->make(true);
    }
    public function request_dispatch_submit(Request $request){
        $request_id = $request->id;

        $request_details = PackagingMaterialRequest::where('id',$request_id)->with('city')->first();

        $hub_id = $request_details->city->hub_id;

        if ($hub_id == 202) {
            $head_stocks = PackagingMaterialStockHead::latest()->first();
        }
        else {
            $head_stocks = PackagingMaterialStockHub::where('hub_id',$hub_id);

            if (!$head_stocks->exists()) {
                return response()->json(['status'=>0,'error'=>"No stock exists!"]);
            }
            else {
                $head_stocks = $head_stocks->first();
            }
        }

//        return $request_details->city->hub_id;
        if($request_details->small_flyers > $head_stocks->small_flyers || $request_details->medium_flyers > $head_stocks->medium_flyers || $request_details->large_flyers > $head_stocks->large_flyers || $request_details->boxes > $head_stocks->boxes){
            return response()->json(['status'=>0,'error'=>"Insufficient quantity!"]);
        }else{

//            $balance = PendingPayment::where('user_id', $request_details->user_id)->first()->pending_payment_shipments->sum('payable');
//            $total_charges = 0;
//            $charges = PackagingCharge::where('user_id',$request_details->user_id)->latest()->first();
//            $total_charges += $request_details->small_flyers * $charges->sm_flyer;
//            $total_charges += $request_details->medium_flyers * $charges->md_flyer;
//            $total_charges += $request_details->large_flyers * $charges->lg_flyer;
//            $total_charges += $request_details->boxes * $charges->box_flyer;
//            if($request_details->packaging_payment_mode_id == 2){
//                if($total_charges > $balance){
//                    return response()->json(['status'=>0,'error'=>"Insufficient Balance!"]);
//
//                }
//            }

               $pickup_address = UserShippingInfo::where(['user_id'=>$request_details->user_id,'city_id'=>$hub_id,'hidden'=>1]);
               if(!$pickup_address->exists()){
                   $shipper_details = User::where('id',$request_details->user_id)->select('name','poc','phone','email')->first();
                   $shipment_consignee_name = "Packaging Material to $shipper_details->name";
                   $pickup_address = UserShippingInfo::create(['user_id'=>$request_details->user_id,'pickup_address'=>"Trax Office",'poc'=>$shipper_details->poc,'phone'=>$shipper_details->phone,'email'=>$shipper_details->email,'city_id'=>$hub_id,'hidden'=>1]);
               }else{
                   $shipper_details = User::where('id',$request_details->user_id)->select('name','poc','phone','email')->first();
                   $shipment_consignee_name = "Packaging Material to $shipper_details->name";
                $pickup_address = $pickup_address->first();
               }

               $now = Carbon::today();

               $details = '';

               if ($request_details->small_flyers != 0) {
                $details .= $request_details->small_flyers . ' Small Flyers, ';
               }

               if ($request_details->medium_flyers != 0) {
                $details .= $request_details->medium_flyers . ' Medium Flyers, ';
               }

               if ($request_details->large_flyers != 0) {
                $details .= $request_details->large_flyers . ' Large Flyers, ';
               }

               if ($request_details->small_flyers != 0) {
                $details .= $request_details->small_flyers . ' Small Flyers, ';
               }

               if ($request_details->boxes != 0) {
                $details .= $request_details->boxes . ' Boxes, ';
               }

               $details = substr($details, 0, -2);

               if($request_details->packaging_payment_mode_id == 1){
                  $shipment = $this->book($request_details->user_id,1,$pickup_address->id,1,$request_details->city_id,$shipment_consignee_name,$request_details->address,$request_details->phone,null,null,null,0,$now,$details,1,1,null,$request_details->amount,1,2,2);
               }else{
                 $shipment = $this->book($request_details->user_id,1,$pickup_address->id,1,$request_details->city_id,$shipment_consignee_name,$request_details->address,$request_details->phone,null,null,null,0,$now,$details,1,1,null,0,1,2,2);
               }
               $new_tracking_number = $this->generate_tracking_number($shipment->id, $pickup_address->city_id, $request_details->city_id);

               $this->add_item($shipment->id,24,$details,1,null,0,0);

               PackagingMaterialRequest::where('id',$request_id)->update([
                    'tracking_number'=>$new_tracking_number
               ]);
               ShipmentsJourneyController::add($shipment->id, 2, 2, NULL, NULL, $request_details->user_id, NULL);

               ShipmentChargesController::packaging_material($shipment->id, $request_details->packaging_payment_mode_id, $request_details->amount);
                if ($hub_id == 202) {
                    $this->sub_head_stock($request_details->small_flyers,$request_details->medium_flyers,$request_details->large_flyers,$request_details->boxes);
                }
                else {
                    $this->sub_hub_stock($hub_id,$request_details->small_flyers,$request_details->medium_flyers,$request_details->large_flyers,$request_details->boxes);
                }

                $request_details->status = 1;
                $request_details->save();
                return response()->json(['status'=>1,'success'=>"Packaging Material has been dispatched successfully!"]);


        }
//        if($request_details->medium_flyers > $head_stocks->medium_flyers){
//            return response()->json(['status'=>0,'error'=>"Insufficient quantity!"]);
//        }
//        return $head_stocks;

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

    public function types_index(){
        return view('admin.materials.types.index');
    }

    public function types_list(Request $request){
        $types = PackagingMaterialTypes::leftjoin('admins as ac', 'ac.id', '=', 'packaging_material_types.created_by')
            ->leftjoin('admins as au', 'au.id', '=', 'packaging_material_types.updated_by')
        ->select('packaging_material_types.id','packaging_material_types.type','packaging_material_types.description','packaging_material_types.status','packaging_material_types.created_at','packaging_material_types.updated_at','ac.name as created_by','au.name as updated_by');
        return Datatables::of($types)
            ->editColumn('status',function ($type){
                if($type->status == 0){
                    return 'Enabled';
                }
                else{
                    return 'Disabled';
                }
            })
            ->editColumn('updated_by', function($type){
                if($type->updated_by == null){
                    return '-';
                }
                else{
                    return $type->updated_by;
                }
            })
            ->editColumn('updated_at', function($type){
                if($type->updated_by == null){
                    return '-';
                }
                else{
                    return $type->updated_at;
                }
            })
            ->addColumn('action', function($type) {//Change ID
                $dropdown = '';
                if ((session('role_id') == 1 || in_array(215, session('permissions')) || in_array(216, session('permissions')))) {
                    $edit = '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit</div></button>';
                    $enable_button = '<button type="button" class="dropdown-item enable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
                    $disable_button = '<button type="button" class="dropdown-item disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';

                    $dropdown = '
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm">
                    ';

                    if ((session('role_id') == 1 || in_array(215, session('permissions')))) {
                        $dropdown .= $edit;
                    }
                    if ((session('role_id') == 1 || in_array(216, session('permissions')))) {
                        if ($type->status == 1) {
                            $dropdown .= $enable_button;
                        } else {
                            $dropdown .= $disable_button;
                        }
                    }


                    $dropdown .= '
                        </div>
                      </div>
                    ';
                }
                    return $dropdown;
            })
            ->make(true);
    }
    public function type_add(Request $request){
        $type = new PackagingMaterialTypes();
        $type->type = $request->type;
        $type->description = $request->description;
        $type->status = 0;
        $type->created_by = Auth::id();
        $type->save();

        $type_history = new PackagingMaterialTypesHistory();
        $type_history->type_id = $type->id;
        $type_history->type = $request->type;
        $type_history->description = $request->description;
        $type_history->status = 0;
        $type_history->created_by = Auth::id();
        $type_history->save();

        foreach($request->size as $index => $type_size){
            $packaging_material_type_size = new PackagingMaterialTypeSizes();
            $packaging_material_type_size->size = $type_size;
            $packaging_material_type_size->type_id = $type->id;
            $packaging_material_type_size->standard_charges = $request->standard_charges[$index];
            $packaging_material_type_size->save();

            $type_size_history = new PackagingMaterialTypeSizesHistory();
            $type_size_history->size = $type_size;
            $type_size_history->type_id = $type->id;
            $type_size_history->standard_charges = $request->standard_charges[$index];
            $type_size_history->updated_by = Auth::id();
            $type_size_history->save();
        }
        return redirect()->back()->with(['status'=>1,'success'=>"Packaging Material Type has been Added successfully!"]);
    }

    public function type_details(Request $request){
        $type = PackagingMaterialTypes::where('id',$request->id)->first();
        $sizes = PackagingMaterialTypeSizes::where('type_id',$request->id)->get();

        return response()->json(['status' => 1, 'type' => $type, 'sizes' => $sizes]);
    }
    public function type_edit(Request $request){
        $type = PackagingMaterialTypes::where('id',$request->id)->first();
        $type->type = $request->edit_type;
        $type->description = $request->edit_description;
        $type->status = 0;
        $type->updated_by = Auth::id();
        $type->save();

        $type_history = new PackagingMaterialTypesHistory();
        $type_history->type_id = $type->id;
        $type_history->type = $request->edit_type;
        $type_history->description = $request->edit_description;
        $type_history->status = 0;
        $type_history->created_by = $type->created_by;
        $type_history->updated_by = Auth::id();
        $type_history->save();

        foreach($request->edit_size as $index => $type_size){
            if(array_key_exists($index, $request->size_id)){
                $packaging_material_type_size = PackagingMaterialTypeSizes::where('id',$request->size_id[$index])->first();
                $packaging_material_type_size->size = $type_size;
                $packaging_material_type_size->type_id = $type->id;
                $packaging_material_type_size->standard_charges = $request->edit_standard_charges[$index];
                $packaging_material_type_size->save();
            }
            else{
                $packaging_material_type_size = new PackagingMaterialTypeSizes();
                $packaging_material_type_size->size = $type_size;
                $packaging_material_type_size->type_id = $type->id;
                $packaging_material_type_size->standard_charges = $request->edit_standard_charges[$index];
                $packaging_material_type_size->save();
            }
            $type_size_history = new PackagingMaterialTypeSizesHistory();
            $type_size_history->size = $type_size;
            $type_size_history->type_id = $type->id;
            $type_size_history->standard_charges = $request->edit_standard_charges[$index];
            $type_size_history->updated_by = Auth::id();
            $type_size_history->save();
        }
        return redirect()->back()->with(['status'=>1,'success'=>"Packaging Material Type has been Edited successfully!"]);
    }

    public function type_enable_disable(Request $request){
        $type = PackagingMaterialTypes::where('id',$request->id)->first();
        $type->status = $request->status;
        $type->save();

        $type_history = new PackagingMaterialTypesHistory();
        $type_history->type_id = $request->id;
        $type_history->type = $type->type;
        $type_history->description = $type->description;
        $type_history->status = $request->status;
        $type_history->created_by = $type->created_by;
        $type_history->updated_by = Auth::id();
        $type_history->save();

        if($request->status == 1){
            $status = 'disabled';
        }
        else{
            $status = 'enabled';
        }
        return response()->json(['status' => 1, 'success'=>"Packaging Material Type " . $type->type . " has been " . $status . " successfully!"]);
    }

    public function warehouse_index(){
        return view('admin.materials.warehouses.index');
    }

    public function warehouse_list(Request $request){
        $types = Warehouse::leftjoin('admins as ac', 'ac.id', '=', 'warehouses.created_by')
            ->leftjoin('admins as au', 'au.id', '=', 'warehouses.updated_by')
            ->leftjoin('cities as h', 'h.id', '=', 'warehouses.hub_id')
            ->select('warehouses.id','warehouses.master_type','h.name as hub','warehouses.status','warehouses.created_at','warehouses.updated_at','ac.name as created_by','au.name as updated_by');
        return Datatables::of($types)
            ->editColumn('status',function ($warehouse){
                if($warehouse->status == 0){
                    return 'Enabled';
                }
                else{
                    return 'Disabled';
                }
            })
            ->editColumn('master_type',function ($warehouse){
                if($warehouse->status == 0){
                    return 'Child';
                }
                else{
                    return 'Master';
                }
            })
            ->editColumn('updated_by', function($warehouse){
                if($warehouse->updated_by == null){
                    return '-';
                }
                else{
                    return $warehouse->updated_by;
                }
            })
            ->editColumn('updated_at', function($warehouse){
                if($warehouse->updated_by == null){
                    return '-';
                }
                else{
                    return $warehouse->updated_at;
                }
            })
            ->addColumn('action', function($warehouse) {//Change ID
                $dropdown = '';
                if ((session('role_id') == 1 || in_array(218, session('permissions')))) {
//                    $edit = '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit</div></button>';
                    $enable_button = '<button type="button" class="dropdown-item enable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
                    $disable_button = '<button type="button" class="dropdown-item disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';

                    $dropdown = '
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm">
                    ';

//                    if ((session('role_id') == 1 || in_array(215, session('permissions')))) {
//                        $dropdown .= $edit;
//                    }
                    if ((session('role_id') == 1 || in_array(218, session('permissions')))) {
                        if ($warehouse->status == 1) {
                            $dropdown .= $enable_button;
                        } else {
                            $dropdown .= $disable_button;
                        }
                    }


                    $dropdown .= '
                        </div>
                      </div>
                    ';
                }
                return $dropdown;
            })
            ->make(true);
    }
    public function warehouse_enable_disable(Request $request){
        $warehouse = Warehouse::where('id',$request->id)->first();
        $warehouse->status = $request->status;
        $warehouse->save();

//        $type_history = new PackagingMaterialTypesHistory();
//        $type_history->type_id = $request->id;
//        $type_history->type = $type->type;
//        $type_history->description = $type->description;
//        $type_history->status = $request->status;
//        $type_history->created_by = $type->created_by;
//        $type_history->updated_by = Auth::id();
//        $type_history->save();

        if($request->status == 1){
            $status = 'disabled';
        }
        else{
            $status = 'enabled';
        }
        return response()->json(['status' => 1, 'success'=>"Warehouse has been " . $status . " successfully!"]);
    }
}
