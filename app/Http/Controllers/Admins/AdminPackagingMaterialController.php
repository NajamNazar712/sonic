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
use App\Http\Models\PackagingMaterialRequestDetail;
use App\Http\Models\PackagingMaterialRequestHistory;
use App\Http\Models\PackagingMaterialRequestStatus;
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
use App\Http\Models\Warehouse\WarehouseFulfilmentHubs;
use App\Http\Models\Warehouse\WarehouseFulfilmentHubsHistory;
use App\Http\Models\Warehouse\WarehouseHistory;
use App\http\Models\WarehouseStock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use DB;
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
        $packaging_request_status = PackagingMaterialRequestStatus::select('id', 'name')->get();
        $packaging = PackagingMaterialStockHead::latest()->first();
        return view('admin.materials.requests.index')->with(['packaging'=>$packaging,'payment_mode'=>$payment_mode, 'packaging_request_status' => $packaging_request_status]);
    }
    public function request_list(Request $request){
        $requests = PackagingMaterialRequest::join('cities as ct','ct.id','=','packaging_material_requests.city_id')
            ->join('users as u','u.id','=','packaging_material_requests.user_id')
            ->join('packaging_payment_modes as ppm','ppm.id','=','packaging_material_requests.packaging_payment_mode_id')
            ->leftjoin('shipments as s', 's.tracking_number', '=', 'packaging_material_requests.tracking_number')
            ->leftjoin('packaging_material_request_statuses as pmrs', 'pmrs.id', '=', 'packaging_material_requests.status')
            ->leftjoin('packaging_material_request_details as pmrd', 'pmrd.packaging_material_request_id', '=', 'packaging_material_requests.id')
            ->select(['packaging_material_requests.id as request_id','u.name as shipper','packaging_material_requests.created_at','ct.name as city','packaging_material_requests.address','ppm.mode','packaging_material_requests.amount','packaging_material_requests.tracking_number','packaging_material_requests.tracking_number as tracking_number_link','pmrs.name as status','packaging_material_requests.status as status_id', DB::raw('sum(pmrd.quantity) as total_quantity'), 's.id as shipment_id', 's.booking_type_id as booking_type_id'])
        ->groupBy('packaging_material_requests.id');

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
            ->editColumn('total_quantity_button', function ($material){
                if($material->total_quantity > 0){
                    return '<button type="button" class="btn btn-outline-success mr-1 quantity">' . $material->total_quantity . '</button>';
                }else{
                    return '-';
                }
            })
            ->addColumn('aging',function ($shipments){
                $days = Carbon::now()->diffInDays($shipments->created_at);
                if($days == 0){
                    return "-";
                }else{
                    return $days;
                }
            })
            ->editColumn('amount', function($shipment){
                return number_format($shipment->amount);
            })
            ->addColumn('action',function ($packaging) {
                if (($packaging->status_id !== 4 && $packaging->status_id !== 5) && (session('role_id') == 1 || in_array(80, session('permissions')))) {
                    $dropdown = '
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm">
                    ';
                    if ($packaging->status_id == 1) {
                        $dropdown .= '<button type="button" class="dropdown-item confirm"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Confirm</div></button>';
                        $dropdown .= '<button type="button" class="dropdown-item cancel"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Cancel</div></button>';
                    }
                    if ($packaging->status_id >= 2) {
                        $dropdown .= '<button type="button" class="dropdown-item grn"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Print GRN</div></button>';
                    }
                    if ($packaging->status_id == 2) {
                        $dropdown .= '<button type="button" class="dropdown-item dispatch"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Dispatch</div></button>';
                    }
                    if ($packaging->status_id == 3) {
                        $dropdown .= '<button type="button" class="dropdown-item completed"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Completed</div></button>';
                        $dropdown .= '<button type="button" class="dropdown-item replenished"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Replenished</div></button>';
                    }

                    $dropdown .= '
                    </div>
                  </div>
                  ';
                }
                else{
                    $dropdown = '';
                }

                    return $dropdown;
            })
            ->make(true);
    }

    public function quantity_details(Request $request){
        $request_id = $request->id;

        $packaging_material_request_details = PackagingMaterialRequestDetail::leftjoin('packaging_material_types as pmt', 'pmt.id', '=', 'packaging_material_request_details.type_id')->leftjoin('packaging_material_type_sizes as pmts', 'pmts.id', '=', 'packaging_material_request_details.type_size_id')->select('pmt.type as type', 'pmts.size as size', 'packaging_material_request_details.quantity')->where('packaging_material_request_id',$request_id)->get();
        return response()->json(['status' => 1, 'types' => $packaging_material_request_details]);
    }

    public function request_confirm(Request $request){
        $request_id = $request->id;

        $request_details = PackagingMaterialRequest::where('id',$request_id)->first();

        if($request_details->exists()){
            $request_details->status = 2;
            $request_details->save();


            $packaging_request_history = new PackagingMaterialRequestHistory();
            $packaging_request_history->packaging_material_request_id = $request_id;
            $packaging_request_history->status = 2;
            $packaging_request_history->updated_by = Auth::id();
            $packaging_request_history->save();

            return response()->json(['status' => 1, 'success'=>"Packaging Material Request has been confirmed successfully!"]);
        }
        else{
            return response()->json(['status' => 0, 'success'=>"Packaging Material Request does\'nt exists!"]);
        }
    }

    public function request_cancel(Request $request){
        $request_id = $request->id;

        $request_details = PackagingMaterialRequest::where('id',$request_id)->first();

        if($request_details->exists()){
            $request_details->status = 6;
            $request_details->save();

            $packaging_request_history = new PackagingMaterialRequestHistory();
            $packaging_request_history->packaging_material_request_id = $request_id;
            $packaging_request_history->status = 6;
            $packaging_request_history->updated_by = Auth::id();
            $packaging_request_history->save();

            return response()->json(['status' => 1, 'success'=>"Packaging Material Request has been canceled successfully!"]);
        }
        else{
            return response()->json(['status' => 0, 'success'=>"Packaging Material Request does\'nt exists!"]);
        }
    }

    public function request_completed(Request $request){
        $request_id = $request->id;

        $request_details = PackagingMaterialRequest::where('id',$request_id)->first();

        if($request_details->exists()){
            $request_details->status = 4;
            $request_details->save();

            $packaging_request_history = new PackagingMaterialRequestHistory();
            $packaging_request_history->packaging_material_request_id = $request_id;
            $packaging_request_history->status = 4;
            $packaging_request_history->updated_by = Auth::id();
            $packaging_request_history->save();

            return response()->json(['status' => 1, 'success'=>"Packaging Material Request has been completed successfully!"]);
        }
        else{
            return response()->json(['status' => 0, 'success'=>"Packaging Material Request does\'nt exists!"]);
        }
    }

    public function request_dispatch_submit(Request $request){
        $request_id = $request->id;

        $packaging_material_request = PackagingMaterialRequest::where('id',$request_id)->with('city')->first();
        $packaging_material_request_details = PackagingMaterialRequestDetail::where('packaging_material_request_id',$request_id);
        if(!$packaging_material_request_details->exists()){
            return response()->json(['status'=>0,'error'=>"Request Invalid!"]);
        }
        else{
            $packaging_material_request_details = $packaging_material_request_details->get();
        }
        $hub_id = $packaging_material_request->city->hub_id;

        $fulfilment_hub = WarehouseFulfilmentHubs::where('hub_id',$hub_id);

        if (!$fulfilment_hub->exists()) {
            return response()->json(['status'=>0,'error'=>"Warehouse does\'nt exists for requested hub!"]);
        }
        else {
            $fulfilment_hub = $fulfilment_hub->first();
        }

        $check = false;
        $error = '';

        $warehouse_id = $fulfilment_hub->warehouse_id;

        foreach ($packaging_material_request_details as $detail){
            $type_id = $detail->type_id;
            $type_size_id = $detail->type_size_id;
            $stock = WarehouseStock::where(['warehouse_id' => $warehouse_id, 'type_id' => $type_id, 'type_size_id' => $type_size_id])->first();

            if($stock['stock'] < $detail->quantity || !$stock->exists()){
                if($check == true){
                    $error = $error .", ". $detail->packaging_type->type." (" . $detail->packaging_type_size->size . ")";
                }
                else{
                    $error = $detail->packaging_type->type." (" . $detail->packaging_type_size->size . ")";
                }
                $check = true;
            }
        }

        if($check == true){
            return response()->json(['status'=>0,'error'=>"Insufficient quantity of ". $error]);
        }
        else{
            foreach ($packaging_material_request_details as $detail_sub){
                $type_id = $detail_sub->type_id;
                $type_size_id = $detail_sub->type_size_id;
                $stock = WarehouseStock::where(['warehouse_id' => $warehouse_id, 'type_id' => $type_id, 'type_size_id' => $type_size_id]);

                    $stock = $stock->first();
                    $stock->stock = $stock['stock'] - $detail_sub->quantity;
                    $stock->save();

            }
            $packaging_material_request->status = 3;
            $packaging_material_request->save();


            $packaging_request_history = new PackagingMaterialRequestHistory();
            $packaging_request_history->packaging_material_request_id = $request_id;
            $packaging_request_history->status = 3;
            $packaging_request_history->updated_by = Auth::id();
            $packaging_request_history->save();
            return response()->json(['status'=>1,'success'=>"Packaging Material has been dispatched successfully!"]);
        }
    }

    public function request_replenish(Request $request){
        $request_id = $request->id;

        $packaging_material_request = PackagingMaterialRequest::where('id',$request_id)->with('city')->first();
        $packaging_material_request_details = PackagingMaterialRequestDetail::where('packaging_material_request_id',$request_id);
        if(!$packaging_material_request_details->exists()){
            return response()->json(['status'=>0,'error'=>"Request Invalid!"]);
        }
        else{
            $packaging_material_request_details = $packaging_material_request_details->get();
        }
        $hub_id = $packaging_material_request->city->hub_id;

        $fulfilment_hub = WarehouseFulfilmentHubs::where('hub_id',$hub_id);

        if (!$fulfilment_hub->exists()) {
            return response()->json(['status'=>0,'error'=>"Warehouse does\'nt exists for requested hub!"]);
        }
        else {
            $fulfilment_hub = $fulfilment_hub->first();
        }

        $warehouse_id = $fulfilment_hub->warehouse_id;

        foreach ($packaging_material_request_details as $detail_add){
            $type_id = $detail_add->type_id;
            $type_size_id = $detail_add->type_size_id;
            $stock = WarehouseStock::where(['warehouse_id' => $warehouse_id, 'type_id' => $type_id, 'type_size_id' => $type_size_id]);

                $stock = $stock->first();
                $stock->stock = $stock['stock'] + $detail_add->quantity;
                $stock->save();
        }
        $packaging_material_request->status = 5;
        $packaging_material_request->save();


        $packaging_request_history = new PackagingMaterialRequestHistory();
        $packaging_request_history->packaging_material_request_id = $request_id;
        $packaging_request_history->status = 5;
        $packaging_request_history->updated_by = Auth::id();
        $packaging_request_history->save();

        return response()->json(['status'=>1,'success'=>"Packaging Material has been Replenished successfully!"]);
    }


    public function good_receiving_note(Request $request){
        $request_id = $request->id;

        $request_details = PackagingMaterialRequest::where('id',$request_id)->first();
        $user = User::where('id', $request_details->user_id)->first();
        $request_type_details = PackagingMaterialRequestDetail::where('packaging_material_request_id', $request_details->id)->get();

//        return $request_type_details;

        $total_quantity = 0;
        foreach ($request_type_details as $type_detail){
            $total_quantity = $total_quantity + $type_detail->quantity;
        }
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();


        $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Good Receiving Note</title>

                    <style>
                      @page {
                        size: A4 portrait;
                      }

                      * {
                        -webkit-print-color-adjust: exact !important;
                        color-adjust: exact !important;
                      }

                      body {
                        background: none !important;
                        color: #09262e !important;
                        font-size: 0.9rem !important;
                      }

                      hr {
                        border-top: 1px dashed #000000;
                      }

                      table.table-bordered {
                        page-break-inside: avoid;
                      }

                      table.table-bordered tbody tr td {
                        border: 1px solid #09262e !important;
                      }

                      .color.primary {
                        background: #c8c8c8 !important;
                      }

                      .color.secondary {
                        background: #ebebeb !important;
                      }

                      .border {
                        border: 1px solid #09262e !important;
                      }

                      .cargo_checklist {
                        page-break-before: always;
                      }
                    </style>
                  </head>
                  <body>
                    <div>
                      <div class="good_receiving_note">
                        <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr>
                              <td class="text-center align-middle"><img src="' . asset('img/trax_logo.png') . '" width="150" class="d-block mx-auto"></td>
                              <td class="text-center align-middle color primary"><strong>Good Receiving Note</strong></td>
                              <td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                              </tr>
                            <tr>
                              <td class="color secondary"><strong>City</strong></td>
                              <td>' . $request_details->City->name . '</td>
                              ';
        if($request_details->tracking_number != null) {
            $html .= '
                                          <td rowspan="9" class="text-center align-middle">
                                          <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($request_details->tracking_number, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                                          <span><strong>' . $request_details->tracking_number . '</strong></span>
                                        </td>';
        }
        $html .= '
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Total Quantity</strong></td>
                              <td>' . number_format($total_quantity) . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Tracking Number</strong></td>
                              <td>' . $request_details->tracking_number . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Shipper</strong></td>
                              <td>' . $user->name . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Address</strong></td>
                              <td>' . $request_details->address . '</td>
                            </tr>
                          </tbody>
                        </table>
                        
                        <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr>
                              <td class="color primary"><strong>S. No.</strong></td>
                              <td class="color primary"><strong>Packaging Type</strong></td>
                              <td class="color primary"><strong>Size</strong></td>
                              <td class="color primary"><strong>Quantity</strong></td>
      ';

        $serial_number = 1;

        foreach ($request_type_details as $detail) {

            $html .= '
                            <tr>
                              <td>' . $serial_number . '</td>
                              <td>' . $detail->packaging_type->type . '</td>
                              <td>' . $detail->packaging_type_size->size  . '</td>
                              <td>' . number_format($detail->quantity) . '</td>
                            </tr>
        ';

            $serial_number++;
        }

        $html .= '
                          </tbody>
                        </table>
                      </div>
                    </div>

                    <script>
                      window.onload = function() {
                        window.print();
                      }
                    </script>
                  </body>
                </html>
      ';

        return $html;

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
                    return 'Disabled';
                }
                else{
                    return 'Enabled';
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
                        if ($type->status == 0) {
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
        $type->updated_by = Auth::id();
        $type->save();

        $type_history = new PackagingMaterialTypesHistory();
        $type_history->type_id = $type->id;
        $type_history->type = $request->edit_type;
        $type_history->description = $request->edit_description;
        $type_history->status = $type->status;
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
        $type->updated_by = Auth::id();
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
            $status = 'enabled';
        }
        else{
            $status = 'disabled';
        }
        return response()->json(['status' => 1, 'success'=>"Packaging Material Type " . $type->type . " has been " . $status . " successfully!"]);
    }

    public function warehouse_index(){
        $warehouse_ids = Warehouse::where('status', 1)->where('master_type','!=', 1)->pluck('hub_id')->toArray();
        $all_warehouse_ids = Warehouse::where('status', 1)->pluck('hub_id')->toArray();
        $all_warehouse_cities = City::where('status', 1)->where('hub', 1)->whereIn('id', $all_warehouse_ids)->get();
        $hubs = City::where('status', 1)->where('hub', 1)->whereNotIn('id', $warehouse_ids)->get();
        $all_hubs = City::whereIn('id', $warehouse_ids)->where('status', 1)->get();
        $fulfilment_ids = WarehouseFulfilmentHubs::all()->pluck('hub_id')->toArray();
        $fulfilment_hubs = City::where('status', '=' ,1)->where('hub', '=' ,1)->whereNotIn('id', $fulfilment_ids)->get();
        $all_active_hubs = City::all()->where('status',1)->where('hub', 1);
        $master_warehouse = Warehouse::where('master_type', 1)->first();
        $master_warehouse_hub = '';
        if($master_warehouse){
            $master_warehouse_hub = $master_warehouse->hub_id;
        }
        return view('admin.materials.warehouses.index')->with(['hubs'=> $hubs, 'fulfilment_hubs' => $fulfilment_hubs, 'all_hubs' => $all_hubs, 'all_active_hubs' => $all_active_hubs, 'master_warehouse_hub' => $master_warehouse_hub, 'all_warehouse_cities' => $all_warehouse_cities]);
    }

    public function warehouse_list(Request $request){
        $types = Warehouse::leftjoin('admins as ac', 'ac.id', '=', 'warehouses.created_by')
            ->leftjoin('admins as au', 'au.id', '=', 'warehouses.updated_by')
            ->leftjoin('cities as h', 'h.id', '=', 'warehouses.hub_id')
            ->leftJoin('warehouse_fulfilment_hubs as wfh', function ($join) {
                $join->on('wfh.warehouse_id', '=', 'warehouses.id');
            })
            ->select('warehouses.id','h.name as hub','warehouses.status','warehouses.created_at','warehouses.updated_at','ac.name as created_by','au.name as updated_by',DB::raw('count(wfh.id) as associated_hubs'))
            ->where('warehouses.master_type', '!=', 1)
            ->groupBy('warehouses.id');
        return Datatables::of($types)
            ->editColumn('associated_hubs', function ($warehouse){
                if($warehouse->associated_hubs > 0){
                    return '<button type="button" class="btn btn-outline-success mr-1 associated_hubs">' . $warehouse->associated_hubs . '</button>';
                }else{
                    return '-';
                }
            })
            ->editColumn('status',function ($warehouse){
                if($warehouse->status == 0){
                    return 'Disable';
                }
                else{
                    return 'Enable';
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
                    $edit_button = '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit</div></button>';

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
                            $dropdown .= $disable_button;
                        } else {
                            $dropdown .= $enable_button;
                        }
                    }
                    if ((session('role_id') == 1 || in_array(220, session('permissions')))) {
                        $dropdown .= $edit_button;
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
        $warehouse->updated_by = Auth::id();
        $warehouse->save();

        $warehouse_history = new WarehouseHistory();
        $warehouse_history->warehouse_id = $warehouse->id;
        $warehouse_history->hub_id = $warehouse->hub_id;
        $warehouse_history->status = $request->status;
        $warehouse_history->master_type = $warehouse->master_type;
        $warehouse_history->created_by = $warehouse->created_by;
        $warehouse_history->updated_by = Auth::id();
        $warehouse_history->save();

        if($request->status == 1){
            $status = 'Enabled';
        }
        else{
            $status = 'Disabled';
        }
        return response()->json(['status' => 1, 'success'=>"Warehouse has been " . $status . " successfully!"]);
    }

    public function warehouse_add(Request $request){
        $hub_id = $request->hub_id;
        $city_ids = $request->city_ids;

        if($hub_id){
            $warehouse = new Warehouse();
            $warehouse->hub_id = $hub_id;
            $warehouse->status = 1;
            $warehouse->master_type = 0;
            $warehouse->created_by = Auth::id();
            $warehouse->save();

            $warehouse_history = new WarehouseHistory();
            $warehouse_history->warehouse_id = $warehouse->id;
            $warehouse_history->hub_id = $hub_id;
            $warehouse_history->status = 1;
            $warehouse_history->master_type = 0;
            $warehouse_history->created_by = Auth::id();
            $warehouse_history->save();

            if($warehouse){
                foreach($city_ids as $id){
                    $fulfilment_hub = new WarehouseFulfilmentHubs();
                    $fulfilment_hub->warehouse_id = $warehouse->id;
                    $fulfilment_hub->hub_id = $id;
                    $fulfilment_hub->save();

                    $fulfilment_hub_history = new WarehouseFulfilmentHubsHistory();
                    $fulfilment_hub_history->warehouse_id = $warehouse->id;
                    $fulfilment_hub_history->hub_id = $id;
                    $fulfilment_hub_history->updated_by = Auth::id();
                    $fulfilment_hub_history->save();

                }
                return redirect()->back()->with(['success' => 'Warehouse  has been added!']);

            }
        }
        return redirect()->back()->with(['error' => 'Warehouse could not be added!']);

    }

    public function warehouse_master_add(Request $request){
        $hub_id = $request->hub;
        if($hub_id){
            $master_hub = Warehouse::where('master_type', 1);
            if($master_hub->exists()){
                $master_hub = $master_hub->first();
                if($master_hub->hub_id != $hub_id){

                    $warehouse_history = new WarehouseHistory();
                    $warehouse_history->warehouse_id = $master_hub->id;
                    $warehouse_history->hub_id = $master_hub->hub_id;
                    $warehouse_history->status = $master_hub->status;
                    $warehouse_history->master_type = 1;
                    $warehouse_history->created_by = $master_hub->created_by;
                    $warehouse_history->updated_by = Auth::id();
                    $warehouse_history->save();


                    $master_hub->hub_id = $hub_id;
                    $master_hub->updated_by = Auth::id();
                    $master_hub->save();


                    return redirect()->back()->with(['success' => 'Warehouse has been updated and set as Master Warehouse!']);

                }else{
                    return redirect()->back()->with(['error' => 'Master warehouse already set as the selected Hub!']);
                }

            }else{
                $master_hub = new Warehouse();
                $master_hub->hub_id = $hub_id;
                $master_hub->master_type = 1;
                $master_hub->created_by = Auth::id();
                $master_hub->save();

                return redirect()->back()->with(['success' => 'Warehouse has been updated and set as Master Warehouse!']);
            }
        }else{
            return redirect()->back()->with(['error' => 'Hub ID not found!']);

        }
    }
    public function warehouse_edit_data(Request $request){
        $id = $request->id;
        if($id){
            $warehouse = Warehouse::find($id);
            $associated_hubs = $warehouse->associated_hubs->pluck('hub_id')->toArray();

            if($warehouse){
                return response()->json(['status' => 0, 'warehouse' => $warehouse, 'associated_hubs' => $associated_hubs]);
            }else{
                return response()->json(['status' => 1, 'error' => 'Warehouse not found']);
            }
        }else{
            return response()->json(['status' => 1, 'error' => 'Warehouse ID not found']);
        }
    }
    public function warehouse_edit(Request $request){

        $warehouse_id = $request->warehouse_id;
        if($warehouse_id){
            $warehouse = Warehouse::find($warehouse_id);
            $warehouse->hub_id = $request->hub_id;
            $warehouse->updated_by = Auth::id();
            $warehouse->save();


            $warehouse_history = new WarehouseHistory();
            $warehouse_history->warehouse_id = $warehouse->id;
            $warehouse_history->hub_id = $request->hub_id;
            $warehouse_history->status = $warehouse->status;
            $warehouse_history->master_type = $warehouse->master_type;
            $warehouse_history->created_by = $warehouse->created_by;
            $warehouse_history->updated_by = Auth::id();
            $warehouse_history->save();

            if($warehouse){
                WarehouseFulfilmentHubs::where('warehouse_id', $warehouse_id)->delete();

                foreach ($request->city_ids as $city){
                    $fulfilment_hub = new WarehouseFulfilmentHubs();
                    $fulfilment_hub->warehouse_id = $warehouse_id;
                    $fulfilment_hub->hub_id = $city;
                    $fulfilment_hub->save();

                    $fulfilment_hub_history = new WarehouseFulfilmentHubsHistory();
                    $fulfilment_hub_history->warehouse_id = $warehouse->id;
                    $fulfilment_hub_history->hub_id = $city;
                    $fulfilment_hub_history->updated_by = Auth::id();
                    $fulfilment_hub_history->save();
                }
                return redirect()->back()->with(['success' => 'Warehouse has been updated successfully!']);
            }
        }else{
            return redirect()->back()->with(['error' => 'Warehouse ID not found!']);
        }

    }

    public function warehouse_hubs(Request $request){
        $associated_hubs = WarehouseFulfilmentHubs::leftjoin('cities as h', 'h.id', '=', 'warehouse_fulfilment_hubs.hub_id')->select('h.name')->where('warehouse_id', $request->id)->get();
        return response()->json(['status' => 1, 'associated_hubs' => $associated_hubs]);
    }

    public function inventory_index(Request $request){
        $warehouses = Warehouse::leftjoin('cities as h', 'h.id', '=', 'warehouses.hub_id')->select('warehouses.id as id', 'h.name as name')->where('warehouses.master_type',0)->get();
        $master_warehouse = Warehouse::leftjoin('cities as h', 'h.id', '=', 'warehouses.hub_id')->select('warehouses.id as id', 'h.name as name')->where('warehouses.master_type',1)->first();
        return view('admin.materials.inventory.index')->with(['warehouses' => $warehouses, 'master_warehouse' => $master_warehouse]);
    }

    public function inventory_list(Request $request){
        $warehouses = $request->warehouses;
        $packaging_inventory = WarehouseStock::leftjoin('warehouses as wm', function($join){
            $join->on('wm.id', '=', 'warehouse_stocks.warehouse_id')
            ->where('wm.master_type', 1);
        })
            ->leftjoin('warehouses as w', function($join){
                $join->on('w.id', '=', 'warehouse_stocks.warehouse_id')
                    ->where('w.master_type', 0);
            })
            ->leftjoin('packaging_material_types as pmt', 'pmt.id', '=', 'warehouse_stocks.type_id')
            ->leftjoin('packaging_material_type_sizes as pmts', 'pmts.id', '=', 'warehouse_stocks.type_size_id')
            ->select('pmt.id', 'pmt.type as packaging_type', 'pmts.size as size', DB::raw('(select sum(warehouse_stocks.stock) from warehouse_stocks where warehouse_stocks.warehouse_id = wm.id and warehouse_stocks.type_id = pmt.id and warehouse_stocks.type_size_id = pmts.id) as master_warehouse'), DB::raw('(select sum(warehouse_stocks.stock) from warehouse_stocks where warehouse_stocks.type_id = pmt.id and warehouse_stocks.type_size_id = pmts.id) as total'))->groupBy('pmts.id');

        foreach ($warehouses as $warehouse){
            $packaging_inventory->addselect(DB::raw('(select sum(warehouse_stocks.stock) from warehouse_stocks where  warehouse_stocks.warehouse_id = '. $warehouse['id'] .' and warehouse_stocks.type_id = pmt.id and warehouse_stocks.type_size_id = pmts.id) as '. strtolower(str_replace(' ', '', $warehouse['name']))));
        }
        return Datatables::of($packaging_inventory)
            ->editColumn('packaging_type' ,function($inventory){
                return $inventory->packaging_type . ' - ' . $inventory->size;
            })
            ->editColumn('total' ,function($inventory){
                if($inventory->total == null){
                    return '0';
                }
                else{
                    return $inventory->total;
                }
            })->make(true);
    }

}
