<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Controllers\Admins\ShipmentChargesController;

use App\Http\Controllers\Shippers\ShipperShipmentBookController;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\PackagingStockHistory;
use App\Http\Models\CargoConsignment;
use App\Http\Models\City;
use App\Http\Models\PackagingCharge;
use App\Http\Models\PackagingMaterialRequest;
use App\Http\Models\PackagingMaterialRequestDetail;
use App\Http\Models\PackagingMaterialRequestHistory;
use App\Http\Models\PackagingMaterialRequestStatus;
use App\Http\Models\PackagingMaterialTypes;
use App\Http\Models\PackagingMaterialTypesHistory;
use App\Http\Models\PackagingMaterialTypeSizes;
use App\Http\Models\PackagingMaterialTypeSizesHistory;
use App\Http\Models\PackagingPaymentMode;
use App\Http\Models\PendingPayment;
use App\Http\Models\Product;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentItem;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\DiscountCharge;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\Warehouse\Warehouse;
use App\Http\Models\Warehouse\WarehouseFulfilmentHubs;
use App\Http\Models\Warehouse\WarehouseFulfilmentHubsHistory;
use App\Http\Models\Warehouse\WarehouseHistory;
use App\Http\Models\WarehouseStock;
use App\Http\Models\WarehouseStockLog;
use App\Http\Models\WarehouseStockLogDetail;
use App\Http\Models\WarehouseStockRequest;
use App\Http\Models\WarehouseStockRequestDetail;
use App\Http\Models\WarehouseStockRequestHistory;
use App\Http\Models\WMS\WmsCurrentStock;
use App\Http\Models\WMS\WmsLabellingCharge;
use App\Http\Models\WMS\WmsOrderPackaging;
use App\Http\Models\WMS\WmsOrderProcess;
use App\Http\Models\WMS\WmsPackingCharge;
use App\Http\Models\WMS\WmsPendingPicking;
use App\Http\Models\WMS\WmsPendingPickingShipments;
use App\Http\Models\WMS\WmsProduct;
use App\Http\Models\WMS\WmsProductBarcode;
use App\Http\Models\WMS\WmsProductCategory;
use App\Http\Models\WMS\WmsReplacementShipmentProduct;
use App\Http\Models\WMS\WmsShipmentProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\PackagingMaterialCart;
use App\Http\Models\ShipperPackagingMaterailType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;

class AdminPackagingMaterialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function packaging_index()
    {

        $packaging_material_status = PackagingMaterialRequestStatus::all();
        $warehouses = Warehouse::where('status', 1)->get();
        $packaging_type = PackagingMaterialTypes::with('sizes')->where('status', 1)->get();
        return view('admin.materials.flyers.index')->with(['packaging_types' => $packaging_type, 'warehouses' => $warehouses, 'packaging_material_status' => $packaging_material_status]);
    }

    public function packaging_list(Request $request)
    {

        $stock_requests = WarehouseStockRequest::leftjoin('warehouses as rw', 'rw.id', 'warehouse_stock_requests.requested_by')
            ->leftjoin('warehouses as sw', 'sw.id', 'warehouse_stock_requests.send_by')
            ->leftjoin('cities as rb', 'rb.id', '=', 'rw.hub_id')
            ->leftjoin('cities as sb', 'sb.id', '=', 'sw.hub_id')
            ->join('admins as cb', 'cb.id', '=', 'warehouse_stock_requests.created_by')
            ->join('packaging_material_request_statuses as pmrs', 'pmrs.id', '=', 'warehouse_stock_requests.status_id')
            ->select(['warehouse_stock_requests.id as stock_request_id', 'warehouse_stock_requests.tracking_number', 'warehouse_stock_requests.tracking_number as tracking_number_link', 'sb.name as send_by', 'rb.name as requested_by', 'cb.name as created_by', 'pmrs.id', 'pmrs.name as status', 'warehouse_stock_requests.created_at', 'warehouse_stock_requests.status_id', 'sw.master_type as master_type']);

        return Datatables::of($stock_requests)
            ->editColumn('tracking_number_link', function ($stock_requests) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$stock_requests->tracking_number' class='tracking' target='_blank'>$stock_requests->tracking_number</a></u>";
            })
            ->filterColumn('status', function ($query, $keyword) {
                if ($keyword != '') {
                    $query->where('warehouse_stock_requests.status_id', '=', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->editColumn('send_by', function ($stock_requests) {
                if ($stock_requests->master_type == 1) {
                    return "Master";
                } else {
                    return $stock_requests->send_by;
                }
            })
            ->addColumn('action', function ($stock) {
                $confirm_button = '<button type="button" class="dropdown-item confirm"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Confirm</div></button>';
                $dispatch_button = '<button type="button" class="dropdown-item dispatch"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Dispatch</div></button>';
                $cancel_button = '<button type="button" class="dropdown-item cancel"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x"></i></div><div class="col-9 offset-1">Cancel</div></button>';
                $detail_button = '<button type="button" class="dropdown-item details"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-list"></i></div><div class="col-9 offset-1">Details</div></button>';

                $dropdown = '
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm">
                    ';

                $dropdown .= $detail_button;
                if ($stock->status_id == 1) {
                    if (session('role_id') == 1 || in_array(223, session('permissions'))) {
                        $dropdown .= $confirm_button;
                    }
                    if (session('role_id') == 1 || in_array(225, session('permissions'))) {
                        $dropdown .= $cancel_button;
                    }

                } else if ($stock->status_id == 2) {
                    if (session('role_id') == 1 || in_array(224, session('permissions'))) {
                        $dropdown .= $dispatch_button;
                    }
                }

                $dropdown .= '
                        </div>
                      </div>
                    ';

                return $dropdown;
            })
            ->make(true);
    }

    public function packaging_request_sizes(Request $request)
    {
        $id = $request->id;
        if ($id) {
            $sizes = PackagingMaterialTypeSizes::where('type_id', $id);
            if ($sizes->exists()) {
                $sizes = $sizes->select('id', 'size', 'standard_charges')->get();
                return response()->json(['status' => 0, 'sizes' => $sizes]);
            } else {
                $type = PackagingMaterialTypes::find($id)->type;
                return response()->json(['status' => 1, 'error' => 'No Size found for type: ' . $type]);
            }
        }
    }

    public function add_stock(Request $request)
    {
        $reference_number = $request->invoice_number;

        if ($reference_number != null) {
            $master = Warehouse::where('master_type', 1)->first();
            if ($master) {
                $type_ids = explode(',', $request->packaging_type_ids);
                $type_size_ids = explode(',', $request->packaging_size_ids);
                $quantities = explode(',', $request->packaging_quantities);

                $log = new WarehouseStockLog();
                $log->warehouse_id = $master->id;
                $log->updated_by = Auth::id();
                $log->reference_number = $reference_number;
                $log->save();

                foreach ($type_ids as $index => $type) {
                    $stock = WarehouseStock::where('warehouse_id', $master->id)->where('type_id', $type)->where('type_size_id', $type_size_ids[$index])->first();
                    if ($stock) {
                        $stock->stock += $quantities[$index];
                    } else {
                        $stock = new WarehouseStock();
                        $stock->warehouse_id = $master->id;
                        $stock->type_id = $type;
                        $stock->type_size_id = $type_size_ids[$index];
                        $stock->stock = $quantities[$index];
                    }
                    $stock->save();

                    $log_details = new WarehouseStockLogDetail();
                    $log_details->warehouse_stock_log_id = $log->id;
                    $log_details->type_id = $type;
                    $log_details->size_id = $type_size_ids[$index];
                    $log_details->quantity = $quantities[$index];
                    $log_details->save();
                }

                return redirect()->back()->with('success', 'Stock added successfully!');
            } else {
                return redirect()->back()->with('error', 'Master Warehouse not found!');
            }

        } else {
            return redirect()->back()->with('error', 'No invoice number entered!');
        }

    }

    public function request_check_quantity(Request $request)
    {
        $type_id = $request->type_id;
        $size_id = $request->size_id;
        $warehouse_id = $request->warehouse_id;
        $quantity = $request->quantity;
        if ($type_id == null) {
            return response()->json(['status' => 1, 'error' => 'Please select Packaging Material Type']);
        }

        if ($size_id == null) {
            return response()->json(['status' => 1, 'error' => 'Please select Packaging Material Size']);
        }

        if ($warehouse_id == null) {
            return response()->json(['status' => 1, 'error' => 'Please select Warehouse']);
        }

        $warehouse = Warehouse::find($warehouse_id);
        if ($warehouse) {
            if ($warehouse->status == 1) {
                $warehouse_stock = WarehouseStock::where('warehouse_id', $warehouse_id)->where('type_id', $type_id)->where('type_size_id', $size_id);
                if ($warehouse_stock->exists()) {
                    $warehouse_stock = $warehouse_stock->first();
                    if ($warehouse_stock->stock > $quantity) {
                        return response()->json(['status' => 0]);
                    } else {
                        return response()->json(['status' => 1, 'error' => 'Warehouse does not have selected quantity!']);
                    }

                } else {
                    return response()->json(['status' => 1, 'error' => 'Warehouse does not have this packaging material!']);
                }

            } else {
                return response()->json(['status' => 1, 'error' => 'Warehouse is disabled, please select another warehouse!']);
            }
        } else {
            return response()->json(['status' => 1, 'error' => 'Warehouse is not found!']);
        }


    }

    public function request_submit(Request $request)
    {
        $request_from = $request->request_from;
        $request_for = $request->request_for;
        $type_ids = explode(',', $request->request_type_ids);
        $type_size_ids = explode(',', $request->request_size_ids);
        $quantities = explode(',', $request->request_quantities);
        $user_id = Auth::id();
        $warehouse_stock_request = new WarehouseStockRequest();
        $warehouse_stock_request->requested_by = $request_for;
        $warehouse_stock_request->send_by = $request_from;
        $warehouse_stock_request->created_by = $user_id;
        $warehouse_stock_request->status_id = 1;
        $warehouse_stock_request->save();

        foreach ($type_ids as $index => $type) {
            $warehouse_stock_details = new WarehouseStockRequestDetail();
            $warehouse_stock_details->request_id = $warehouse_stock_request->id;
            $warehouse_stock_details->type_id = $type;
            $warehouse_stock_details->size_id = $type_size_ids[$index];
            $warehouse_stock_details->quantity = $quantities[$index];
            $warehouse_stock_details->save();
        }

        return redirect()->back()->with('success', 'Stock requested successfully!');
    }

    public function fetch_cities(Request $request)
    {
        $cities = City::where(['hub' => 1, 'status' => 1])->select('id', 'name')->get();
        return response()->json(['status' => 1, 'cities' => $cities]);

    }

    public function fetch_pickup_address(Request $request)
    {
        $shipper_id = $request->shipper_id;
        $shipper = User::findOrFail($shipper_id);
        if ($shipper) {
            $pickup_data = array();
            $pickup_address = $shipper->shipping;
            foreach ($pickup_address as $id => $pickup) {
                if ($pickup->hidden == 0 && $pickup->status == 1) {
                    $pickup_data[$id]['pickup_address_id'] = $pickup->id;
                    $pickup_data[$id]['pickup_address'] = $pickup->pickup_address;
                }
            }
            return response()->json(['status' => 0, 'pickup_data' => $pickup_data]);
        }
    }

    public function request_index(Request $request)
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 281);
        $admin_id = Auth::id();
        $tagged_shippers = array();
        $all_shippers = array();
        if (session('role_id') == 1) {
            $tagged_shippers = User::where('status', 3)->pluck('id')->toArray();
        } else if (session('department_id') == 7) {
            $tagged_shippers = session('tagged_shippers');
        }

        $payment_mode = PackagingPaymentMode::all();
        $packaging_request_status = PackagingMaterialRequestStatus::select('id', 'name')->get();
        $packaging_material_types = PackagingMaterialTypes::where('status', 1)->get();
        $cities = City::where('status', 1)->orderBy('name')->get();
        $foc_accounts = GlobalSettings::where('type', 'foc_account_tag');
        $foc_shippers = array();
        if ($foc_accounts->exists()) {
            $foc_accounts = $foc_accounts->first();
            $foc_account_ids = array_map('intval', explode(',', $foc_accounts->text));

            $all_shippers = array_merge($tagged_shippers, $foc_account_ids);
        }

        $all_shippers = User::select('id', 'name')->whereIn('id', $all_shippers)->where('status', 3)->get();

        return view('admin.materials.requests.index')->with(['payment_mode' => $payment_mode, 'packaging_request_status' => $packaging_request_status, 'packaging_material_types' => $packaging_material_types, 'cities' => $cities, 'shippers' => $all_shippers]);
    }

    public function request_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 282);
        }

        $requests = PackagingMaterialRequest::join('cities as ct', 'ct.id', '=', 'packaging_material_requests.city_id')
            ->join('users as u', 'u.id', '=', 'packaging_material_requests.user_id')
            ->join('packaging_payment_modes as ppm', 'ppm.id', '=', 'packaging_material_requests.packaging_payment_mode_id')
            ->leftjoin('shipments as s', 's.tracking_number', '=', 'packaging_material_requests.tracking_number')
            ->leftjoin('admins as rb', 'rb.id', '=', 'packaging_material_requests.requested_by')
//            ->leftjoin('user_shipping_infos as usi', 'usi.id', '=', 's.pickup_address_id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 's.id')
                    ->where('sj.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = s.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftjoin('packaging_material_request_statuses as pmrs', 'pmrs.id', '=', 'packaging_material_requests.status_id')
            ->leftjoin('packaging_material_request_details as pmrd', 'pmrd.packaging_material_request_id', '=', 'packaging_material_requests.id')
            ->select(['packaging_material_requests.id as request_id', 'u.name as shipper', 'packaging_material_requests.created_at', 'ct.name as city', 'packaging_material_requests.address', 'ppm.mode', 'packaging_material_requests.amount', 'packaging_material_requests.tracking_number', 'packaging_material_requests.tracking_number as tracking_number_link', 'pmrs.name as status', 'packaging_material_requests.status_id as status_id', DB::raw('sum(pmrd.quantity) as total_quantity'), 's.id as shipment_id', 's.shipper_status_id as shipper_status_id', 's.booking_type_id as booking_type_id', 's.created_at as confirmed_date', 'sj.remarks as remarks', 'rb.name as requested_by', 'u.phone as shipper_phone'])
            ->groupBy('packaging_material_requests.id')
            ->having('total_quantity', '>', 0);

        if (session('department_id') == 7) {
            if (!in_array(session('id'), session('sale_users_bypass'))) {
                $requests = $requests->whereIn('u.id', session('tagged_shippers'));
            }
        }
        $datatables =  Datatables::of($requests)
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('total_quantity_button', function ($material) {
                if ($material->total_quantity > 0) {
                    return '<div class="text-center"><button type="button" class="btn btn-sm btn-outline-info quantity">' . $material->total_quantity . '</button></div>';
                } else {
                    return '-';
                }
            })
            ->addColumn('aging', function ($shipments) {
                $days = Carbon::now()->diffInDays($shipments->created_at);
                if ($days == 0) {
                    return "-";
                } else {
                    return $days;
                }
            })
            ->addColumn('confirmed_aging', function ($shipments) {
                if ($shipments->confirmed_date) {
                    $days = Carbon::now()->diffInDays($shipments->confirmed_date);
                    if ($days == 0) {
                        return "-";
                    } else {
                        return $days;
                    }
                } else {
                    return "-";
                }
            })
            ->filterColumn('status', function ($query, $keyword) {
                if ($keyword != '') {
                    $query->where('packaging_material_requests.status_id', '=', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->editColumn('amount', function ($shipment) {
                return number_format($shipment->amount);
            })
            ->addColumn('action', function ($packaging) {
                if ((session('role_id') == 1 || in_array(226, session('permissions')) || in_array(227, session('permissions'))) && ($packaging->status_id == 1 || $packaging->status_id == 2)) {
                    $dropdown = '
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm">
                    ';
                    if ($packaging->status_id == 1) {
                        if (session('role_id') == 1 || in_array(226, session('permissions'))) {
                            $dropdown .= '<button type="button" class="dropdown-item confirm"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Confirm</div></button>';
                        }
                    }
                    if ((session('role_id') == 1 || in_array(227, session('permissions'))) && ($packaging->status_id == 1 || $packaging->status_id == 2)) {
                        $dropdown .= '<button type="button" class="dropdown-item cancel"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Cancel</div></button>';
                    }
                    if ($packaging->status_id == 1) {
                        $dropdown .= '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit</div></button>';
                    }

//                    if ($packaging->status_id >= 2) {
//                        $dropdown .= '<button type="button" class="dropdown-item grn"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Print GRN</div></button>';
//                    }
//                    if ((session('role_id') == 1) || $packaging->status_id == 2 && $packaging->shipper_status_id == 1 && (in_array(80, session('permissions')))) {
//                        $dropdown .= '<button type="button" class="dropdown-item dispatch"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Dispatch</div></button>';
//                    }
                    if ($packaging->confirmed_date != null) {
                        $dropdown .= '<button type="button" class="dropdown-item remarks"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Add/Update Remarks</div></button>';
                    }

                    $dropdown .= '
                    </div>
                  </div>
                  ';
                } else {
                    $dropdown = '';
                }

                return $dropdown;
            });

            if ($request->get('requested_from_date') && $request->get('requested_to_date')) {
                $from = date('Y-m-d 00:00:01', strtotime($request->get('requested_from_date')));
                $to = date('Y-m-d 23:59:59', strtotime($request->get('requested_to_date')));
                $datatables->whereBetween('packaging_material_requests.created_at', [$from, $to]);
            }

    //
            return $datatables->make(true);
    }

    public function request_update(Request $request)
    {
        $request_id = $request->request_id;

        $index_array = [];
        $total_charges = 0;

        DB::beginTransaction();
        try {
            foreach ($request->quantity as $index => $quantity) {
                array_push($index_array, $index);
                $package = PackagingMaterialRequestDetail::where('packaging_material_request_id', $request_id)
                    ->where('id', $index)->first();
                $package->quantity = $quantity;
                $package->update();

                $user_id = $package->packaging_request->user_id;
                $packaging_type_id = $package->type_id;
                $packaging_size_id = $package->type_size_id;

                $size = PackagingMaterialTypeSizes::find($packaging_size_id);
                $total_charges += $quantity * $size->standard_charges;

                $created_at = $package->packaging_request->created_at;
            }

            PackagingMaterialRequestDetail::whereNotIn('id', $index_array)
                ->where('packaging_material_request_id', $request_id)->delete();

            $booking_day = $created_at;
            $discount = DiscountCharge::where('user_id', $user_id)->whereDate('to', '<=', $booking_day)->whereDate('from', '>=', $booking_day)->whereNotNull('packaging');

            if ($discount->exists()) {
                $discount = $discount->orderBy('shipping_mode_id', 'ASC')->first();

                $discount_packaging = $discount->packaging;

                if (strpos($discount_packaging, '%') !== FALSE) {
                    $discount_packaging = (floatval(str_replace('%', '', $discount_packaging)) / 100) * $total_charges;
                    $total_charges -= $discount_packaging;
                } else {
                    $total_charges -= floatval($discount_packaging);
                }
            }

            $packaging_request = PackagingMaterialRequest::find($request_id);
            $packaging_request->amount = $total_charges;
            $packaging_request->update();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 0, 'error' => $e->getMessage()]);
            return response()->json(['status' => 0, 'error' => 'Packaging request quantity can\'t be updated!']);
        }

        DB::commit();
        return response()->json(['status' => 1, 'success' => 'Packaging request quantity updated successfully!']);
    }

    public function quantity_details(Request $request)
    {
        $request_id = $request->id;

        $packaging_material_request_details = PackagingMaterialRequestDetail::leftjoin('packaging_material_types as pmt', 'pmt.id', '=', 'packaging_material_request_details.type_id')->leftjoin('packaging_material_type_sizes as pmts', 'pmts.id', '=', 'packaging_material_request_details.type_size_id')->select('pmt.type as type', 'pmts.size as size', 'packaging_material_request_details.quantity', 'packaging_material_request_details.id as index')->where('packaging_material_request_id', $request_id)->get();
        return response()->json(['status' => 1, 'types' => $packaging_material_request_details, 'request_id' => $request_id]);
    }

    public function add_pickup_address($user_id, $address, $person_of_contact, $phone_number, $email_address, $city_id, $status)
    {

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

    static public function add_product_location($shipment_id, $product_id, $quantity, $pickup_address_id, $courier)
    {
        $processed_quantity = $quantity;
        while ($processed_quantity != 0) {
            $product_barcodes = WmsProductBarcode::join('wms_store_requests as wsr', 'wsr.id', '=', 'wms_product_barcodes.store_request_id')->select('leaf_id')->where('wms_product_barcodes.product_id', $product_id)->where('wsr.warehouse_pickup_address_id', $pickup_address_id)->whereNull('wms_product_barcodes.shipment_id')->where('wms_product_barcodes.status', 3)->first();
            if ($product_barcodes) {
                $product_barcode_locations = WmsProductBarcode::where('product_id', $product_id)->whereNull('shipment_id')->where('leaf_id', $product_barcodes->leaf_id)->where('status', 3)->take($processed_quantity)->get();
                $total_location_count = count($product_barcode_locations);
                if ($processed_quantity <= $total_location_count) {
                    foreach ($product_barcode_locations as $product_barcode_location_index => $product_barcode_location) {
                        if ($product_barcode_location_index < $quantity) {
                            $product_barcode_location->shipment_id = $shipment_id;
                            $product_barcode_location->courier_id = $courier;
                            $product_barcode_location->save();
                        }
                    }
                    $pending_picking = new WmsPendingPicking();
                    $pending_picking->leaf_id = $product_barcodes->leaf_id;
                    $pending_picking->product_id = $product_id;
                    $pending_picking->quantity = $processed_quantity;
                    $pending_picking->shipment_id = $shipment_id;
                    $pending_picking->courier_id = $courier;
                    $pending_picking->save();

                    $processed_quantity = 0;
                } else {
                    foreach ($product_barcode_locations as $product_barcode_location_index => $product_barcode_location) {
                        $product_barcode_location->shipment_id = $shipment_id;
                        $product_barcode_location->courier_id = $courier;
                        $product_barcode_location->save();
                    }
                    $pending_picking = new WmsPendingPicking();
                    $pending_picking->leaf_id = $product_barcodes->leaf_id;
                    $pending_picking->product_id = $product_id;
                    $pending_picking->quantity = count($product_barcode_locations);
                    $pending_picking->shipment_id = $shipment_id;
                    $pending_picking->courier_id = $courier;
                    $pending_picking->save();

                    $processed_quantity = $processed_quantity - $total_location_count;
                }
            }
        }
    }

    public function request_confirm(Request $request)
    {
        $request_id = $request->id;

        $request_details = PackagingMaterialRequest::where('id', $request_id)->first();

        if ($request_details != null) {
            if ($request_details->shipment_id == null) {

                $total_charges = $request_details->amount;

                $city_id = $request_details->city_id;
                $city_hub = City::where('id', $city_id)->first();
                $hub_id = $city_hub->hub_id;

                $fulfilment_hub = WarehouseFulfilmentHubs::where('hub_id', $hub_id);

                if (!$fulfilment_hub->exists()) {
                    return response()->json(['status' => 0, 'error' => 'Warehouse does\'nt exists for requested hub!']);
                } else {
                    $fulfilment_hub = $fulfilment_hub->latest('id')->first();
                }

                $warehouse_id = $fulfilment_hub->warehouse_id;

                $warehouse = Warehouse::where('id', $warehouse_id)->first();

                $warehouse_hub_id = $warehouse->hub_id;

                $user_id = $request_details->user_id;

                $setting = GlobalSettings::where('type', 'packaging_material')->first();
                $wms_user_id = $setting->setting_value;
                $user_shipping_info = UserShippingInfo::where(['user_id' => $wms_user_id, 'city_id' => $warehouse_hub_id]);

                if ($user_shipping_info->exists()) {
                    $trax_address = $user_shipping_info->latest('id')->first();
                } else {
                    return response()->json(['status' => 0, 'error' => 'Marco Warehouse does\'nt exists for requested hub!']);
                }
                //            return response()->json(['status' => 0, 'error'=>$trax_address->id]);

                $product_ids = array();
                $invalid_product_ids = array();
                $total_quantity = 0;
                $invalid_products = '';
                foreach ($request_details->items as $item) {
                    $product_ids[] = $item->wms_product_id;
                    $total_quantity = $total_quantity + $item->quantity;

                    $check_current_stock = WmsCurrentStock::where('product_id', $item->wms_product_id)->where('warehouse_pickup_address_id', $trax_address->id)->where('user_id', $wms_user_id);
                    if ($check_current_stock->exists()) {
                        $check_current_stock = $check_current_stock->first();
                        if ($check_current_stock->stock < $item->quantity) {
                            $product = WmsProduct::find($item->wms_product_id);
                            $invalid_product_ids[] = $item->wms_product_id;
                            if ($invalid_products == '') {
                                $invalid_products = $product->name;
                            } else {
                                $invalid_products = $invalid_products . ' ' . $product->name;
                            }
                        }
                    } else {
                        $product = WmsProduct::find($item->wms_product_id);
                        $invalid_product_ids[] = $item->wms_product_id;
                        if ($invalid_products == '') {
                            $invalid_products = $product->name;
                        } else {
                            $invalid_products = $invalid_products . ' ' . $product->name;
                        }
                    }
                }
                //dd($invalid_product_ids);
                if (count($invalid_product_ids) > 0) {
                    return response()->json(['status' => 0, 'error' => $invalid_products . ' does\'nt exists in requested hub!']);
                }
                $flag = true;
                foreach ($request_details->items as $item) {
                    $product_barcodes = WmsProductBarcode::join('wms_store_requests as wsr', 'wsr.id', '=', 'wms_product_barcodes.store_request_id')->where('wms_product_barcodes.product_id', $item->wms_product_id)->where('wsr.warehouse_pickup_address_id', $trax_address->id)->whereNull('wms_product_barcodes.shipment_id')->where('wms_product_barcodes.status', 3)->count();
                    if ($product_barcodes < $item->quantity) {
                        $wms_current_stock = WmsCurrentStock::where('product_id', $item->wms_product_id)->where('warehouse_pickup_address_id', $trax_address->id)->first();
                        $wms_current_stock->stock = $product_barcodes;
                        $wms_current_stock->save();
                        $flag = false;
                    }
                }
                if ($flag == false) {
                    return response()->json(['status' => 0, 'error' => 'Selected Product(s) quantity exceed!']);
                }

                $shipper_details = User::where('id', $user_id)->select('name', 'poc', 'phone', 'email')->first();
                $shipment_consignee_name = "Packaging Material to $shipper_details->name";

                if ($request_details->packaging_payment_mode_id == 1) {
                    $shipment = $this->book($user_id, 1, $trax_address->id, 1, $request_details->city_id, $shipment_consignee_name, $request_details->address, $request_details->phone, null, null, null, 0, null, 1, 1, null, $total_charges, 1, 1, 1);
                } else {
                    $shipment = $this->book($user_id, 1, $trax_address->id, 1, $request_details->city_id, $shipment_consignee_name, $request_details->address, $request_details->phone, null, null, null, 0, null, 1, 1, null, 0, 1, 1, 1, $total_charges);
                }

                $new_tracking_number = $this->generate_tracking_number($shipment->id, $trax_address->city_id, $request_details->city_id);


                $details = '';
                $serial = 1;
                foreach ($request_details->items as $item) {
                    $product = WmsProduct::find($item->wms_product_id);
                    if ($details == '') {
                        $details = $serial . '.) ' . $product->name . '-' . $item->quantity . '</br>';
                    } else {
                        $details .= ', ' . $serial . '.) ' . $product->name . '-' . $item->quantity . '</br>';
                    }
                    $shipment_product = new WmsShipmentProduct();
                    $shipment_product->shipment_id = $shipment->id;
                    $shipment_product->product_id = $product->id;
                    $shipment_product->quantity = $item->quantity;
                    $shipment_product->courier_id = 1;
                    $shipment_product->save();

//                    $this->add_product_location($shipment->id, $product->id, $item->quantity, $trax_address->id, 1);

                    $existing_pending_picking = WmsPendingPicking::where('product_id', $product->id)->where('hub_id', $warehouse_hub_id)->where('status', 0)->first();
                    if($existing_pending_picking){
                        $new_quantity = $existing_pending_picking->quantity + $item->quantity;
                        $existing_pending_picking->quantity = $new_quantity;
                        $existing_pending_picking->save();

                        $pending_picking_shipment = new WmsPendingPickingShipments();
                        $pending_picking_shipment->picking_id = $existing_pending_picking->id;
                        $pending_picking_shipment->shipment_id = $shipment->id;
                        $pending_picking_shipment->courier_id = 1;
                        $pending_picking_shipment->save();
                    }
                    else{
                        $pending_picking = new WmsPendingPicking();
                        $pending_picking->product_id = $product->id;
                        $pending_picking->hub_id = $warehouse_hub_id;
                        $pending_picking->quantity = $item->quantity;
                        $pending_picking->save();

                        $pending_picking_shipment = new WmsPendingPickingShipments();
                        $pending_picking_shipment->picking_id = $pending_picking->id;
                        $pending_picking_shipment->shipment_id = $shipment->id;
                        $pending_picking_shipment->courier_id = 1;
                        $pending_picking_shipment->save();
                    }
                    $serial++;
                    $current_stock = WmsCurrentStock::where('product_id', $product->id)->where('warehouse_pickup_address_id', $trax_address->id)->where('user_id', $wms_user_id)->first();
                    $new_available_stock = $current_stock->stock - $item->quantity;
                    $current_stock->stock = $new_available_stock;
                    $current_stock->save();


                    $serial++;
                }

                $shipment_item = new ShipmentItem();
                $shipment_item->shipment_id = $shipment->id;
                $shipment_item->product_type_id = 24;
                $shipment_item->description = $details;
                $shipment_item->quantity = 1;
                $shipment_item->price = null;
                $shipment_item->insurance = null;
                $shipment_item->type = 0;
                $shipment_item->save();

                $labeling_charges = WmsLabellingCharge::where('user_id', $user_id)->first();
                $order_process = new WmsOrderProcess();
                $order_process->shipment_id = $shipment->id;
                $order_process->sku_count = count($product_ids);
                $order_process->courier_id = 1;
                $order_process->quantity = $total_quantity;
                $order_process->packing_charges = 0;
                if ($labeling_charges) {
                    $order_process->labelling_charges = $labeling_charges->charges;
                }
                $order_process->save();

                PackagingMaterialRequest::where('id', $request_details->id)->update([
                    'tracking_number' => $new_tracking_number,
                    'shipment_id' => $shipment->id
                ]);


                ShipmentsJourneyController::add($shipment->id, 1, 1, NULL, NULL, NULL, Auth::id(), $request_id);

                ShipmentChargesController::packaging_material($shipment->id, $request_details->packaging_payment_mode_id, $total_charges);

                $request_details->status_id = 2;
                $request_details->save();

                $packaging_request_history = new PackagingMaterialRequestHistory();
                $packaging_request_history->packaging_material_request_id = $request_id;
                $packaging_request_history->status = 2;
                $packaging_request_history->updated_by = Auth::id();
                $packaging_request_history->save();

                return response()->json(['status' => 1, 'success' => "Packaging Material Request has been confirmed successfully!"]);
            } else {
                return response()->json(['status' => 0, 'error' => "Packaging Material Request already marked as confirmed!"]);
            }
        } else {
            return response()->json(['status' => 0, 'error' => "Packaging Material Request does\'nt exists!"]);
        }
    }

    public function request_cancel(Request $request)
    {
        $request_id = $request->id;

        $packaging_material_request = PackagingMaterialRequest::where('id', $request_id)->first();

        if ($packaging_material_request->status_id > 2) {
            return response()->json(['status' => 0, 'error' => 'Cancellation failed, Request is already processed!']);
        } else {
            if ($packaging_material_request->status_id == 1) {
                $packaging_material_request->status_id = 6;
                $packaging_material_request->save();

                $packaging_request_history = new PackagingMaterialRequestHistory();
                $packaging_request_history->packaging_material_request_id = $request_id;
                $packaging_request_history->status = 6;
                $packaging_request_history->updated_by = Auth::id();
                $packaging_request_history->save();
            } elseif ($packaging_material_request->status_id == 2) {
                $shipment_id = $packaging_material_request->shipment_id;
                $shipment = Shipment::find($shipment_id);
                if ($shipment) {
                    if ($shipment->warehouse_order_status == 2 || $shipment->warehouse_order_status == 3 || $shipment->warehouse_order_status == 8) {
                        $shipment->warehouse_order_status = 9;
                        $shipment->shipper_status_id = 17;
                        $shipment->consignee_status_id = 17;

                        ShipmentsJourneyController::add($shipment_id, 17, 17, NULL, NULL, NULL, Auth::id());
                        $shipment->save();

                        $shipment_products = WmsShipmentProduct::where('shipment_id', $shipment->id)->where('courier_id', 1)->get();
                        if ($shipment_products) {
                            foreach ($shipment_products as $shipment_product) {
                                $current_stock_addition = WmsCurrentStock::where('product_id', $shipment_product->product_id)->where('warehouse_pickup_address_id', $shipment->pickup_address_id)->first();
                                if ($current_stock_addition) {
                                    $current_stock_addition->stock = $current_stock_addition->stock + $shipment_product->quantity;
                                    $current_stock_addition->save();
                                }
                            }
                        }
                        $packaging_material_request->status_id = 6;
                        $packaging_material_request->save();

                        WmsProductBarcode::where('shipment_id', $shipment_id)->where('courier_id', 1)->update(['shipment_id' => null, 'courier_id' => null, 'picklist_id' => null]);

                    } else {
                        return response()->json(['status' => 0, 'error' => 'Cancellation failed, not ready to be cancelled!']);
                    }
                }
                return response()->json(['status' => 1, 'success' => 'Request cancelled successfully!']);
            }
        }
    }

    public function request_completed(Request $request)
    {
        $request_id = $request->id;

        $request_details = PackagingMaterialRequest::where('id', $request_id)->first();

        if ($request_details->exists()) {
            $request_details->status = 4;
            $request_details->save();

            $packaging_request_history = new PackagingMaterialRequestHistory();
            $packaging_request_history->packaging_material_request_id = $request_id;
            $packaging_request_history->status = 4;
            $packaging_request_history->updated_by = Auth::id();
            $packaging_request_history->save();

            return response()->json(['status' => 1, 'success' => "Packaging Material Request has been completed successfully!"]);
        } else {
            return response()->json(['status' => 0, 'success' => "Packaging Material Request does\'nt exists!"]);
        }
    }

    public function request_dispatch_submit(Request $request)
    {
        $request_id = $request->id;

        $packaging_material_request = PackagingMaterialRequest::where('id', $request_id)->with('city')->first();
        $packaging_material_request_details = PackagingMaterialRequestDetail::where('packaging_material_request_id', $request_id);
        if (!$packaging_material_request_details->exists()) {
            return response()->json(['status' => 0, 'error' => "Request Invalid!"]);
        } else {
            $packaging_material_request_details = $packaging_material_request_details->get();
        }
        $hub_id = $packaging_material_request->city->hub_id;

        $fulfilment_hub = WarehouseFulfilmentHubs::where('hub_id', $hub_id);

        if (!$fulfilment_hub->exists()) {
            return response()->json(['status' => 0, 'error' => "Warehouse does\'nt exists for requested hub!"]);
        } else {
            $fulfilment_hub = $fulfilment_hub->latest('id')->first();
        }

        $check = false;
        $error = '';

        $warehouse_id = $fulfilment_hub->warehouse_id;

        foreach ($packaging_material_request_details as $detail) {
            $type_id = $detail->type_id;
            $type_size_id = $detail->type_size_id;
            $stock = WarehouseStock::where(['warehouse_id' => $warehouse_id, 'type_id' => $type_id, 'type_size_id' => $type_size_id])->first();

            if ($stock['stock'] < $detail->quantity || !$stock->exists()) {
                if ($check == true) {
                    $error = $error . ", " . $detail->packaging_type->type . " (" . $detail->packaging_type_size->size . ")";
                } else {
                    $error = $detail->packaging_type->type . " (" . $detail->packaging_type_size->size . ")";
                }
                $check = true;
            }
        }

        if ($check == true) {
            return response()->json(['status' => 0, 'error' => "Insufficient quantity of " . $error]);
        } else {
            foreach ($packaging_material_request_details as $detail_sub) {
                $type_id = $detail_sub->type_id;
                $type_size_id = $detail_sub->type_size_id;
                $stock = WarehouseStock::where(['warehouse_id' => $warehouse_id, 'type_id' => $type_id, 'type_size_id' => $type_size_id]);

                $stock = $stock->first();
                $stock->stock = $stock['stock'] - $detail_sub->quantity;
                $stock->save();

            }
            $packaging_material_request->status_id = 3;
            $packaging_material_request->save();

            $shipment = Shipment::where('tracking_number', $packaging_material_request->tracking_number)->first();
            if ($shipment) {
                Shipment::where('id', $shipment->id)->update([
                    'shipper_status_id' => 2,
                    'consignee_status_id' => 2,
                ]);
                ShipmentsJourneyController::add($shipment->id, 2, 2, NULL, NULL, NULL, Auth::id(), $packaging_material_request->id);
            }


            $packaging_request_history = new PackagingMaterialRequestHistory();
            $packaging_request_history->packaging_material_request_id = $request_id;
            $packaging_request_history->status = 3;
            $packaging_request_history->updated_by = Auth::id();
            $packaging_request_history->save();
            return response()->json(['status' => 1, 'success' => "Packaging Material has been dispatched successfully!"]);
        }
    }

    public function request_replenish(Request $request)
    {
        $request_id = $request->id;

        $packaging_material_request = PackagingMaterialRequest::where('id', $request_id)->with('city')->first();
        $packaging_material_request_details = PackagingMaterialRequestDetail::where('packaging_material_request_id', $request_id);
        if (!$packaging_material_request_details->exists()) {
            return response()->json(['status' => 0, 'error' => "Request Invalid!"]);
        } else {
            $packaging_material_request_details = $packaging_material_request_details->get();
        }
        $hub_id = $packaging_material_request->city->hub_id;

        $fulfilment_hub = WarehouseFulfilmentHubs::where('hub_id', $hub_id);

        if (!$fulfilment_hub->exists()) {
            return response()->json(['status' => 0, 'error' => "Warehouse does\'nt exists for requested hub!"]);
        } else {
            $fulfilment_hub = $fulfilment_hub->first();
        }

        $warehouse_id = $fulfilment_hub->warehouse_id;

        foreach ($packaging_material_request_details as $detail_add) {
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

        return response()->json(['status' => 1, 'success' => "Packaging Material has been Replenished successfully!"]);
    }

    public function good_receiving_note(Request $request)
    {
        $request_id = $request->id;

        $request_details = PackagingMaterialRequest::where('id', $request_id)->first();
        $user = User::where('id', $request_details->user_id)->first();
        $request_type_details = PackagingMaterialRequestDetail::where('packaging_material_request_id', $request_details->id)->get();

//        return $request_type_details;

        $total_quantity = 0;
        foreach ($request_type_details as $type_detail) {
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
                              <td class="text-center align-middle"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto"></td>
                              <td class="text-center align-middle color primary"><strong>Good Receiving Note</strong></td>
                              <td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                              </tr>
                            <tr>
                              <td class="color secondary"><strong>City</strong></td>
                              <td>' . $request_details->City->name . '</td>
                              ';
        if ($request_details->tracking_number != null) {
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
                              <td>' . $detail->packaging_type_size->size . '</td>
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

    private function book($user_id, $service_type_id, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $special_instructions, $estimated_weight, $shipping_mode_id, $same_day_timing_id, $amount, $payment_mode_id, $shipper_status_id, $consignee_status_id, $packaging_material_charges = null)
    {
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
        $shipment->special_instructions = $special_instructions;


        $shipment->estimated_weight = $estimated_weight;
        $shipment->shipping_mode_id = $shipping_mode_id;
        $shipment->same_day_timing_id = $same_day_timing_id;

        $shipment->amount = $amount;
        $shipment->payment_mode_id = $payment_mode_id;
        $shipment->shipper_status_id = $shipper_status_id;
        $shipment->consignee_status_id = $consignee_status_id;

        $shipment->packaging_material_request = 1;
        $shipment->packaging_material_charges = $packaging_material_charges;
        $shipment->warehouse = $packaging_material_charges;
        $shipment->warehouse = 1;
        $shipment->warehouse_order_type = 1;
        $shipment->warehouse_order_status = 1;

        $shipment->save();

        return $shipment;
    }

    private function generate_tracking_number($shipment_id, $pickup_city_id, $consignee_city_id)
    {
        $shipment = Shipment::find($shipment_id);

        $tracking_number = $pickup_city_id . $consignee_city_id . str_pad($shipment_id, 6, '0', STR_PAD_LEFT);

        $shipment->tracking_number = $tracking_number;

        $shipment->save();

        return $tracking_number;
    }

    private function add_item($shipment_id, $product_type_id, $item_description, $item_quantity, $price, $insurance, $type)
    {
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

    public function types_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 338);
        $existing_shipper_packing_types = ShipperPackagingMaterailType::all();
        $arr_shipper_packing_types = array();
        if (count($existing_shipper_packing_types) > 0) {
            // $settings = $settings->first();
            foreach ($existing_shipper_packing_types as $existing_shipper_packing_type) {
                array_push($arr_shipper_packing_types, $existing_shipper_packing_type->shipper_id);
                // $arr_shipper_packing_types = array_map('intval', explode(',', $settings->text));
            }
            // $rider_id = $settings->setting_value;
        }
        // dd($arr_shipper_packing_types);
        return view('admin.materials.types.index')->with(['arr_shipper_packing_types' => $arr_shipper_packing_types]);
    }

    public function types_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 339);
        }
        $types = PackagingMaterialTypes::leftjoin('admins as ac', 'ac.id', '=', 'packaging_material_types.created_by')
            ->leftjoin('admins as au', 'au.id', '=', 'packaging_material_types.updated_by')
            ->select('packaging_material_types.id', 'packaging_material_types.type', 'packaging_material_types.category', 'packaging_material_types.description', 'packaging_material_types.status', 'packaging_material_types.created_at', 'packaging_material_types.updated_at', 'ac.name as created_by', 'au.name as updated_by','packaging_material_types.packaging_type');
        return Datatables::of($types)
            ->editColumn('status', function ($type) {
                if ($type->status == 0) {
                    return 'Disabled';
                } else {
                    return 'Enabled';
                }
            })
            ->editColumn('updated_by', function ($type) {
                if ($type->updated_by == null) {
                    return '-';
                } else {
                    return $type->updated_by;
                }
            })
            ->editColumn('updated_at', function ($type) {
                if ($type->updated_by == null) {
                    return '-';
                } else {
                    return $type->updated_at;
                }
            })
            ->editColumn('category', function ($type) {
                if ($type->category == 1) {
                    return 'Packaging Material';
                } else {
                    return 'Stationary';

                }
            })
            ->editColumn('packaging_type',function($type){
                if($type->packaging_type == 1){
                    return 'Internal';
                }
                else if($type->packaging_type == 2){
                    return 'External';
                }
                else if($type->packaging_type == 3){
                    return 'Both';
                }
                else if($type->packaging_type == 4){
                    return 'Only Shipper';
                }
                else{
                    return 'Marco';
                }
            })
            ->filterColumn('packaging_material_types.packaging_type', function ($query, $keyword) {
                $keyword = strtolower($keyword);
                if ($keyword == 'internal') {
                    $query->where('packaging_material_types.packaging_type', 1);
                }
                else if ($keyword == 'external') {
                    $query->where('packaging_material_types.packaging_type', 2);
                }
                else if ($keyword == 'both') {
                    $query->where('packaging_material_types.packaging_type', 2);
                }
                else if ($keyword == 'only' || $keyword == 'only shipper') {
                    $query->where('packaging_material_types.packaging_type', 4);
                }
                else if ($keyword == 'only' || $keyword == 'marco') {
                    $query->where('packaging_material_types.packaging_type', 5);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn('action', function ($type) {//Change ID
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

    public function all_shippers()
    {
        $field = ' <div class="form-group">
        <label>Select Shippers</label>

        <fieldset class="form-group">
            <select name="shippers_ids[]" multiple="multiple" id="shippers_ids" class="form-control select2" placeholder="Select Shippers*" required data-rule-required="true" data-msg-required="This field is required">
            ';


        $shippers = User::all();

        foreach ($shippers as $shipper) {
            $field .= '<option value="' . $shipper->id . '">' . $shipper->name . '</option> ';
        }


        $field .= '  </select></fieldset></div>';
        return $field;
    }

    public function all_shippers_edit()
    {
        $field = ' <div class="form-group">
        <label>Select Shippers</label>

        <fieldset class="form-group">
            <select name="shippers_ids_edit[]" multiple="multiple" id="shippers_ids_edit" class="form-control select2" placeholder="Select Shippers*" required data-rule-required="true" data-msg-required="This field is required">
            ';


        $shippers = User::all();

        foreach ($shippers as $shipper) {
            $field .= '<option value="' . $shipper->id . '">' . $shipper->name . '</option> ';
        }


        $field .= '  </select></fieldset></div>';
        return $field;
    }


    public function type_add(Request $request)
    {
        $packaging_type = 3;
        if ($request->has('packaging_type') && $request->packaging_type == 'external') {
            $packaging_type = 2;
        } else if ($request->has('packaging_type') && $request->packaging_type == 'internal') {
            $packaging_type = 1;
        } else if ($request->has('packaging_type') && $request->packaging_type == 'only_shipper') {
            $packaging_type = 4;
        } else if ($request->has('packaging_type') && $request->packaging_type == 'marco') {
            $packaging_type = 5;
        }

        $type = new PackagingMaterialTypes();
        $type->type = $request->type;
        $type->description = $request->description;
        $type->packaging_type = $packaging_type;
        $type->category = $request->category;
        $type->status = 1;
        $type->created_by = Auth::id();
        $type->save();

        if ($request->hasFile('packaging_picture')) {
            $filename = 'packaging_picture_' . $type->id . '.png';
            $file = $request->file('packaging_picture');
            Storage::disk('public')->putFileAs('packaging_pictures/', $file, $filename);
            $type->picture = $filename;
        }
        if ($request->hasFile('packaging_picture1')) {
            $filename = 'packaging_picture_1_' . $type->id . '.png';
            $file = $request->file('packaging_picture1');
            Storage::disk('public')->putFileAs('packaging_pictures/', $file, $filename);
            $type->picture_1 = $filename;
        }
        if ($request->hasFile('packaging_picture2')) {
            $filename = 'packaging_picture_2_' . $type->id . '.png';
            $file = $request->file('packaging_picture2');
            Storage::disk('public')->putFileAs('packaging_pictures/', $file, $filename);
            $type->picture_2 = $filename;
        }
        if ($request->hasFile('packaging_picture3')) {
            $filename = 'packaging_picture_3_' . $type->id . '.png';
            $file = $request->file('packaging_picture3');
            Storage::disk('public')->putFileAs('packaging_pictures/', $file, $filename);
            $type->picture_3 = $filename;
        }
        if ($request->hasFile('packaging_picture4')) {
            $filename = 'packaging_picture_4_' . $type->id . '.png';
            $file = $request->file('packaging_picture4');
            Storage::disk('public')->putFileAs('packaging_pictures/', $file, $filename);
            $type->picture_4 = $filename;
        }

        $type->save();

        $type_history = new PackagingMaterialTypesHistory();
        $type_history->type_id = $type->id;
        $type_history->type = $request->type;
        $type_history->description = $request->description;
        $type_history->status = 1;
        $type_history->created_by = Auth::id();
        $type_history->save();
        if ($packaging_type == 4) {
            foreach ($request->shippers_ids as $index => $shippers_id) {

                $shipper_packaging_materail = new ShipperPackagingMaterailType;
                $shipper_packaging_materail->shipper_id = $shippers_id;
                $shipper_packaging_materail->type_id = $type->id;

                $shipper_packaging_materail->save();
            }
        }

        foreach ($request->size as $index => $type_size) {
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


            $setting = GlobalSettings::where('type', 'packaging_material');
            if ($setting->exists()) {
                $setting = $setting->first();
                $existing_product_category = WmsProductCategory::where('name', 'Packaging Material');
                if ($existing_product_category->exists()) {
                    $product_type = $existing_product_category->first();
                } else {
                    $product_type = new WmsProductCategory();
                    $product_type->name = 'Packaging Material';
                    $product_type->user_id = $setting->setting_value;
                    $product_type->save();
                }

                $product = new WmsProduct();
                $product->name = $packaging_material_type_size->type->type . ' - ' . $packaging_material_type_size->size;
                $product->sku_id = 'PM-' . $packaging_material_type_size->type->id . '-' . $packaging_material_type_size->id;
                $product->description = 'Packaging Material Type-Size : ' . $packaging_material_type_size->type->type . '-' . $packaging_material_type_size->size;
                $product->buffer_quantity = 1;
                $product->status = 1;
                $product->user_id = $setting->setting_value;
                $product->category_id = $product_type->id;
                $product->save();
                $barcode = $setting->setting_value . '-' . strtoupper($product->sku_id);
                $product->barcode_series = $barcode;
                $product->save();

                $packaging_material_type_size->wms_product_id = $product->id;
                $packaging_material_type_size->save();
            }
        }
        return redirect()->back()->with(['status' => 1, 'success' => "Packaging Material Type has been Added successfully!"]);
    }

    public function type_details(Request $request)
    {
        $type = PackagingMaterialTypes::where('id', $request->id)->first();
        $sizes = PackagingMaterialTypeSizes::where('type_id', $request->id)->get();
        $existing_shipper_packing_types = ShipperPackagingMaterailType::where('type_id', $type->id)->get();

        $arr_shipper_packing_types = array();
        if (count($existing_shipper_packing_types) > 0) {
            // $settings = $settings->first();
            foreach ($existing_shipper_packing_types as $existing_shipper_packing_type) {
                array_push($arr_shipper_packing_types, $existing_shipper_packing_type->shipper_id);
                // $arr_shipper_packing_types = array_map('intval', explode(',', $settings->text));
            }
            // $rider_id = $settings->setting_value;
        }
        return response()->json(['status' => 1, 'type' => $type, 'sizes' => $sizes, 'arr_shipper_packing_types' => $arr_shipper_packing_types]);
    }

    public function type_edit(Request $request)
    {
        $packaging_type_edit = 3;
        if ($request->has('packaging_type_edit') && $request->packaging_type_edit == 'external') {
            $packaging_type_edit = 2;
        } else if ($request->has('packaging_type_edit') && $request->packaging_type_edit == 'internal') {
            $packaging_type_edit = 1;
        } else if ($request->has('packaging_type_edit') && $request->packaging_type_edit == 'only_shipper') {
            $packaging_type_edit = 4;
        } else if ($request->has('packaging_type_edit') && $request->packaging_type_edit == 'marco') {
            $packaging_type_edit = 5;
        }

        $type = PackagingMaterialTypes::where('id', $request->id)->first();
        $type->type = $request->edit_type;
        $type->category = $request->category_edit;

        $type->description = $request->edit_description;
        $type->updated_by = Auth::id();
        $type->packaging_type = $packaging_type_edit;

        if ($request->hasFile('edit_packaging_picture')) {
            $filename = 'packaging_picture_' . $type->id . '.png';
            $file = $request->file('edit_packaging_picture');
            Storage::disk('public')->putFileAs('packaging_pictures/', $file, $filename);
            $type->picture = $filename;
        }
        if ($request->hasFile('edit_packaging_picture1')) {
            $filename = 'packaging_picture_1_' . $type->id . '.png';
            $file = $request->file('edit_packaging_picture1');
            Storage::disk('public')->putFileAs('packaging_pictures/', $file, $filename);
            $type->picture_1 = $filename;
        }
        if ($request->hasFile('edit_packaging_picture2')) {
            $filename = 'packaging_picture_2_' . $type->id . '.png';
            $file = $request->file('edit_packaging_picture2');
            Storage::disk('public')->putFileAs('packaging_pictures/', $file, $filename);
            $type->picture_2 = $filename;
        }
        if ($request->hasFile('edit_packaging_picture3')) {
            $filename = 'packaging_picture_3_' . $type->id . '.png';
            $file = $request->file('edit_packaging_picture3');
            Storage::disk('public')->putFileAs('packaging_pictures/', $file, $filename);
            $type->picture_3 = $filename;
        }
        if ($request->hasFile('edit_packaging_picture4')) {
            $filename = 'packaging_picture_4_' . $type->id . '.png';
            $file = $request->file('edit_packaging_picture4');
            Storage::disk('public')->putFileAs('packaging_pictures/', $file, $filename);
            $type->picture_4 = $filename;
        }
        $type->save();


        $type_history = new PackagingMaterialTypesHistory();
        $type_history->type_id = $type->id;
        $type_history->type = $request->edit_type;
        $type_history->description = $request->edit_description;
        $type_history->status = $type->status;
        $type_history->created_by = $type->created_by;
        $type_history->updated_by = Auth::id();
        $type_history->save();
        if ($packaging_type_edit == 4) {

            ShipperPackagingMaterailType::where('type_id', $type->id)->delete();
            foreach ($request->shippers_ids_edit as $index => $shippers_id) {
                $shipper_packaging_materail = new ShipperPackagingMaterailType;
                $shipper_packaging_materail->shipper_id = $shippers_id;
                $shipper_packaging_materail->type_id = $type->id;
                $shipper_packaging_materail->save();
            }
        } else {
            ShipperPackagingMaterailType::where('type_id', $type->id)->delete();

        }
        $product_check = false;
        $setting = GlobalSettings::where('type', 'packaging_material');
        if ($setting->exists()) {
            $setting = $setting->first();
            $product_check = true;
        }

        foreach ($request->edit_size as $index => $type_size) {
            if (array_key_exists($index, $request->size_id)) {
                $packaging_material_type_size = PackagingMaterialTypeSizes::where('id', $request->size_id[$index])->first();
                $packaging_material_type_size->size = $type_size;
                $packaging_material_type_size->type_id = $type->id;
                $packaging_material_type_size->standard_charges = $request->edit_standard_charges[$index];
                $packaging_material_type_size->save();
                if ($product_check) {
                    $product = WmsProduct::find($packaging_material_type_size->wms_product_id);
                    $product->name = $packaging_material_type_size->type->type . ' - ' . $packaging_material_type_size->size;
                    $product->description = 'Packaging Material Type-Size : ' . $packaging_material_type_size->type->type . '-' . $packaging_material_type_size->size;
                    $product->user_id = $setting->setting_value;
                    $product->save();
                }
            } else {

                $packaging_material_type_size = new PackagingMaterialTypeSizes();
                $packaging_material_type_size->size = $type_size;
                $packaging_material_type_size->type_id = $type->id;
                $packaging_material_type_size->standard_charges = $request->edit_standard_charges[$index];
                $packaging_material_type_size->save();

                if ($product_check) {
                    $existing_product_category = WmsProductCategory::where('name', 'Packaging Material');
                    if ($existing_product_category->exists()) {
                        $product_type = $existing_product_category->first();
                    } else {
                        $product_type = new WmsProductCategory();
                        $product_type->name = 'Packaging Material';
                        $product_type->user_id = $setting->setting_value;
                        $product_type->save();
                    }

                    $product = new WmsProduct();
                    $product->name = $packaging_material_type_size->type->type . ' - ' . $packaging_material_type_size->size;
                    $product->sku_id = 'PM-' . $packaging_material_type_size->type->id . '-' . $packaging_material_type_size->id;
                    $product->description = 'Packaging Material Type-Size : ' . $packaging_material_type_size->type->type . '-' . $packaging_material_type_size->size;
                    $product->buffer_quantity = 1;
                    $product->status = 1;
                    $product->user_id = $setting->setting_value;
                    $product->category_id = $product_type->id;
                    $product->save();
                    $barcode = $setting->setting_value . '-' . strtoupper($product->sku_id);
                    $product->barcode_series = $barcode;
                    $product->save();

                    $packaging_material_type_size->wms_product_id = $product->id;
                    $packaging_material_type_size->save();
                }
            }
            $type_size_history = new PackagingMaterialTypeSizesHistory();
            $type_size_history->size = $type_size;
            $type_size_history->type_id = $type->id;
            $type_size_history->standard_charges = $request->edit_standard_charges[$index];
            $type_size_history->updated_by = Auth::id();
            $type_size_history->save();
        }
        return redirect()->back()->with(['status' => 1, 'success' => "Packaging Material Type has been Edited successfully!"]);
    }

    public function type_enable_disable(Request $request)
    {
        $type = PackagingMaterialTypes::where('id', $request->id)->first();
        $type->status = $request->status;
        $type->updated_by = Auth::id();
        $type->save();
        if ($type->status == 0) {
            PackagingMaterialCart::where('type_id', $type->id)->delete();
        }
        $type_history = new PackagingMaterialTypesHistory();
        $type_history->type_id = $request->id;
        $type_history->type = $type->type;
        $type_history->description = $type->description;
        $type_history->status = $request->status;
        $type_history->created_by = $type->created_by;
        $type_history->updated_by = Auth::id();
        $type_history->save();

        $sizes = $type->sizes;
        if ($sizes) {
            foreach ($sizes as $size) {
                $product = WmsProduct::find($size->wms_product_id);
                $product->status = $request->status;
                $product->save();
            }
        }
        if ($request->status == 1) {
            $status = 'enabled';
        } else {
            $status = 'disabled';
        }
        return response()->json(['status' => 1, 'success' => "Packaging Material Type " . $type->type . " has been " . $status . " successfully!"]);
    }

    public function warehouse_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 283);
        $warehouse_ids = Warehouse::where('status', 1)->where('master_type', '!=', 1)->pluck('hub_id')->toArray();
        $all_warehouse_ids = Warehouse::where('status', 1)->pluck('hub_id')->toArray();
        $all_warehouse_cities = City::where('status', 1)->where('hub', 1)->whereIn('id', $all_warehouse_ids)->get();
        $hubs = City::where('status', 1)->where('hub', 1)->whereNotIn('id', $warehouse_ids)->get();
        $all_hubs = City::whereIn('id', $warehouse_ids)->where('status', 1)->get();
        $fulfilment_ids = WarehouseFulfilmentHubs::all()->pluck('hub_id')->toArray();
        $fulfilment_hubs = City::where('status', '=', 1)->where('hub', '=', 1)->whereNotIn('id', $fulfilment_ids)->get();
        $all_active_hubs = City::all()->where('status', 1)->where('hub', 1);
        $master_warehouse = Warehouse::where('master_type', 1)->first();
        $master_warehouse_hub = '';
        if ($master_warehouse) {
            $master_warehouse_hub = $master_warehouse->hub_id;
        }
        return view('admin.materials.warehouses.index')->with(['hubs' => $hubs, 'fulfilment_hubs' => $fulfilment_hubs, 'all_hubs' => $all_hubs, 'all_active_hubs' => $all_active_hubs, 'master_warehouse_hub' => $master_warehouse_hub, 'all_warehouse_cities' => $all_warehouse_cities]);
    }

    public function warehouse_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 284);
        }
        $types = Warehouse::leftjoin('admins as ac', 'ac.id', '=', 'warehouses.created_by')
            ->leftjoin('admins as au', 'au.id', '=', 'warehouses.updated_by')
            ->leftjoin('cities as h', 'h.id', '=', 'warehouses.hub_id')
            ->leftJoin('warehouse_fulfilment_hubs as wfh', function ($join) {
                $join->on('wfh.warehouse_id', '=', 'warehouses.id');
            })
            ->select('warehouses.id', 'h.name as hub', 'warehouses.status', 'warehouses.created_at', 'warehouses.updated_at', 'ac.name as created_by', 'au.name as updated_by', DB::raw('count(wfh.id) as associated_hubs'))
            ->where('warehouses.master_type', '!=', 1)
            ->groupBy('warehouses.id');
        return Datatables::of($types)
            ->editColumn('associated_hubs_button', function ($warehouse) {
                if ($warehouse->associated_hubs > 0) {
                    return '<button type="button" class="btn btn-sm btn-outline-info mr-1 associated_hubs">' . $warehouse->associated_hubs . '</button>';
                } else {
                    return '-';
                }
            })
            ->editColumn('status', function ($warehouse) {
                if ($warehouse->status == 0) {
                    return 'Disable';
                } else {
                    return 'Enable';
                }
            })
            ->editColumn('updated_by', function ($warehouse) {
                if ($warehouse->updated_by == null) {
                    return '-';
                } else {
                    return $warehouse->updated_by;
                }
            })
            ->editColumn('updated_at', function ($warehouse) {
                if ($warehouse->updated_by == null) {
                    return '-';
                } else {
                    return $warehouse->updated_at;
                }
            })
            ->addColumn('action', function ($warehouse) {//Change ID
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

    public function warehouse_enable_disable(Request $request)
    {
        $warehouse = Warehouse::where('id', $request->id)->first();
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

        if ($request->status == 1) {
            $status = 'Enabled';
        } else {
            $status = 'Disabled';
        }
        return response()->json(['status' => 1, 'success' => "Warehouse has been " . $status . " successfully!"]);
    }

    public function warehouse_add(Request $request)
    {
        $hub_id = $request->hub_id;
        $city_ids = $request->city_ids;

        if ($hub_id) {
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

            if ($warehouse) {
                foreach ($city_ids as $id) {
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

    public function warehouse_master_add(Request $request)
    {
        $hub_id = $request->hub;
        if ($hub_id) {
            $master_hub = Warehouse::where('master_type', 1);
            if ($master_hub->exists()) {
                $master_hub = $master_hub->first();
                if ($master_hub->hub_id != $hub_id) {

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

                } else {
                    return redirect()->back()->with(['error' => 'Master warehouse already set as the selected Hub!']);
                }

            } else {
                $master_hub = new Warehouse();
                $master_hub->hub_id = $hub_id;
                $master_hub->master_type = 1;
                $master_hub->created_by = Auth::id();
                $master_hub->save();

                return redirect()->back()->with(['success' => 'Warehouse has been updated and set as Master Warehouse!']);
            }
        } else {
            return redirect()->back()->with(['error' => 'Hub ID not found!']);

        }
    }

    public function warehouse_edit_data(Request $request)
    {
        $id = $request->id;
        if ($id) {
            $warehouse = Warehouse::find($id);
            $associated_hubs = $warehouse->associated_hubs->pluck('hub_id')->toArray();

            if ($warehouse) {
                return response()->json(['status' => 0, 'warehouse' => $warehouse, 'associated_hubs' => $associated_hubs]);
            } else {
                return response()->json(['status' => 1, 'error' => 'Warehouse not found']);
            }
        } else {
            return response()->json(['status' => 1, 'error' => 'Warehouse ID not found']);
        }
    }

    public function warehouse_edit(Request $request)
    {

        $warehouse_id = $request->warehouse_id;
        if ($warehouse_id) {
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

            if ($warehouse) {
                WarehouseFulfilmentHubs::where('warehouse_id', $warehouse_id)->delete();

                foreach ($request->city_ids as $city) {
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
        } else {
            return redirect()->back()->with(['error' => 'Warehouse ID not found!']);
        }

    }

    public function warehouse_hubs(Request $request)
    {
        $associated_hubs = WarehouseFulfilmentHubs::leftjoin('cities as h', 'h.id', '=', 'warehouse_fulfilment_hubs.hub_id')->select('h.name')->where('warehouse_id', $request->id)->get();
        return response()->json(['status' => 1, 'associated_hubs' => $associated_hubs]);
    }

    public function inventory_index(Request $request)
    {
        $warehouses = Warehouse::leftjoin('cities as h', 'h.id', '=', 'warehouses.hub_id')->join('warehouse_stocks as ws', 'ws.warehouse_id', '=', 'warehouses.id')->select('warehouses.id as id', 'h.name as name')->where('warehouses.master_type', 0)->groupBy('warehouses.id')->get();
        $master_warehouse = Warehouse::leftjoin('cities as h', 'h.id', '=', 'warehouses.hub_id')->select('warehouses.id as id', 'h.name as name')->where('warehouses.master_type', 1)->first();
        return view('admin.materials.inventory.index')->with(['warehouses' => $warehouses, 'master_warehouse' => $master_warehouse]);
    }

    public function inventory_list(Request $request)
    {
        $warehouses = $request->warehouses;
//        return $warehouses;
        $packaging_inventory = WarehouseStock::
//        leftjoin('warehouses as wm', function ($join) {
//            $join->on('wm.id', '=', 'warehouse_stocks.warehouse_id')
//                ->where('wm.master_type', 1);
//        })
        leftjoin('warehouses as w', function ($join) {
            $join->on('w.id', '=', 'warehouse_stocks.warehouse_id')
                ->where('w.master_type', 0);
        })
            ->leftjoin('packaging_material_types as pmt', 'pmt.id', '=', 'warehouse_stocks.type_id')
            ->leftjoin('packaging_material_type_sizes as pmts', 'pmts.id', '=', 'warehouse_stocks.type_size_id')
            ->select('pmt.id', 'pmt.type as packaging_type', 'pmts.size as size', DB::raw('(select sum(warehouse_stocks.stock) from warehouse_stocks where warehouse_stocks.warehouse_id = ' . $request->master_warehouse_id . ' and warehouse_stocks.type_id = pmt.id and warehouse_stocks.type_size_id = pmts.id) as master_warehouse'), DB::raw('(select sum(warehouse_stocks.stock) from warehouse_stocks where warehouse_stocks.type_id = pmt.id and warehouse_stocks.type_size_id = pmts.id) as total'))->groupBy('pmts.id');
        if (!empty($warehouses)) {
            foreach ($warehouses as $warehouse) {
                $packaging_inventory->addselect(DB::raw('(select sum(warehouse_stocks.stock) from warehouse_stocks where  warehouse_stocks.warehouse_id = ' . $warehouse['id'] . ' and warehouse_stocks.type_id = pmt.id and warehouse_stocks.type_size_id = pmts.id) as ' . strtolower(str_replace(' ', '', $warehouse['name']))));
            }
        }
        return Datatables::of($packaging_inventory)
            ->editColumn('packaging_type', function ($inventory) {
                return $inventory->packaging_type . ' - ' . $inventory->size;
            })
            ->editColumn('total', function ($inventory) {
                if ($inventory->total == null) {
                    return '0';
                } else {
                    return $inventory->total;
                }
            })->make(true);
    }

    public function stock_request_cancel(Request $request)
    {
        $request_id = $request->stock_request_id;
        $request_details = WarehouseStockRequest::find($request_id);

        if ($request_details) {
            if ($request_details->status_id != 6) {
                $request_details->status_id = 6;
                $request_details->save();

                $warehoude_stock_request_history = new WarehouseStockRequestHistory();
                $warehoude_stock_request_history->warehouse_stock_request_id = $request_id;
                $warehoude_stock_request_history->status = 6;
                $warehoude_stock_request_history->updated_by = Auth::id();
                $warehoude_stock_request_history->save();


                return response()->json(['status' => 1, 'success' => "Packaging Material Request has been canceled successfully!"]);
            } else {
                return response()->json(['status' => 0, 'error' => "Packaging Material Request already cancelled!"]);
            }

        } else {
            return response()->json(['status' => 0, 'error' => "Packaging Material Request does\'nt exists!"]);
        }
    }

    public function stock_request_confirm(Request $request)
    {
        $request_id = $request->stock_request_id;
        $stock_request = WarehouseStockRequest::find($request_id);
        if ($stock_request) {
            if ($stock_request->status_id != 6) {
                $details = '';
                foreach ($stock_request->stock_request_details as $item) {
                    $details .= $item->quantity . ' ' . $item->packaging_size->size . ' ' . $item->packaging_type->type . ' ';
                }
                $pickup_hub_id = $stock_request->request_send_by->hub_id;
                $consignee_hub_id = $stock_request->request_requested_by->hub_id;

                $request_status = $this->request_booked($request_id, $details, $pickup_hub_id, $consignee_hub_id);

                if ($request_status == "booked") {
                    $stock_request->status_id = 2;
                    $stock_request->save();

                    return response()->json(['status' => 0, 'success' => 'Packaging Material Request successfully confirmed!']);

                } else {
                    return $request_status;
                }
            } else {
                return response()->json(['status' => 1, 'error' => 'Packaging Material Request is Cancelled!']);
            }

        }
    }

    public function request_booked($request_id, $details, $pickup_hub_id, $consignee_hub_id)
    {
        $settings = GlobalSettings::where('type', 'packaging_material_stock_movement_account_id')->first();
        if ($settings) {
            $pickup = UserShippingInfo::where('user_id', $settings->setting_value)->where('city_id', $pickup_hub_id)->where('hidden', 2)->where('poc', '=', 'Trax Logistics');
            $pickup_address_id = '';
            if ($pickup->exists()) {
                $pickup = $pickup->first();
                $pickup_address_id = $pickup->id;
            } else {
                $pickup_address_id = ShipperShipmentBookController::add_pickup_address($settings->setting_value, 'Trax Office', 'Trax Logistics', null, '0213-8772222', 'info@trax.pk', $pickup_hub_id, 0, 1);
            }

            $shipment = $this->book($settings->setting_value, 1, $pickup_address_id, 1, $consignee_hub_id, 'Trax Logistics', 'Trax Office', '0213-8772222', NULL, 'info@trax.pk', NULL, 0, Carbon::now(), NULL, 1, 1, NULL, 0, 1, 2, 2);

            $tracking_number = $this->generate_tracking_number($shipment->id, $pickup_hub_id, $consignee_hub_id);
            $stock_request = WarehouseStockRequest::find($request_id);
            $stock_request->tracking_number = $tracking_number;
            $stock_request->save();
            $this->add_item($shipment->id, 24, $details, 1, null, 0, 0);

            ShipmentsJourneyController::add($shipment->id, 1, 1, NULL, NULL, NULL, Auth::id(), $request_id);
            ShipmentsJourneyController::add($shipment->id, 2, 2, NULL, NULL, NULL, Auth::id(), $request_id);

            return "booked";
//            return response()->json(['status' => 1, 'success' => 'Packaging Material Request successfully confirmed!']);

        } else {
            return response()->json(['status' => 1, 'error' => 'Settings not found for Packaging Material Stock Movement Account']);
        }

    }

    public function stock_request_details(Request $request)
    {
        $request_id = $request->id;
        $details = array();
        if ($request_id) {
            $stock_request_details = WarehouseStockRequestDetail::where('request_id', $request_id);
            if ($stock_request_details->exists()) {
                $details = $stock_request_details->with(['packaging_type', 'packaging_size'])->get();

                return response()->json(['status' => 0, 'details' => $details]);
            } else {
                return response()->json(['status' => 1, 'error' => 'No Details found!']);
            }
        }
    }

    public function stock_request_dispatch(Request $request)
    {
        $request_id = $request->stock_request_id;

        if ($request_id) {
            $stock_request = WarehouseStockRequest::find($request_id);
            if ($stock_request) {
                $request_details = WarehouseStockRequestDetail::where('request_id', $request_id)->get();
//                $warehouse_sender = WarehouseStock::where('warehouse_id', $stock_request->send_by)->get();
                foreach ($request_details as $request_detail) {
                    if (!WarehouseStock::where('warehouse_id', $stock_request->send_by)->where('type_id', $request_detail->type_id)->where('type_size_id', $request_detail->size_id)->where('stock', '>', $request_detail->quantity)->exists()) {
                        return response()->json(['status' => 1, 'error' => 'Could not dispatch request, please check stock!']);
                    }
                }
                foreach ($request_details as $request_detail) {
                    $sender_stock = WarehouseStock::where('warehouse_id', $stock_request->send_by)->where('type_id', $request_detail->type_id)->where('type_size_id', $request_detail->size_id)->first();

                    $sender_stock->stock -= $request_detail->quantity;
                    $sender_stock->save();

//                    if(WarehouseStock::where('warehouse_id', $stock_request->requested_by)->where('type_id', $request_detail->type_id)->where('type_size_id', $request_detail->size_id)->exists()){
//                        $receiver_stock = WarehouseStock::where('warehouse_id', $stock_request->requested_by)->where('type_id', $request_detail->type_id)->where('type_size_id', $request_detail->size_id)->first();
//                        $receiver_stock->stock += $request_detail->quantity;
//                        $request_detail->save();
//                    }else{
//
//                        $receiver_stock = new WarehouseStock();
//                        $receiver_stock->warehouse_id = $stock_request->requested_by;
//                        $receiver_stock->type_id = $request_detail->type_id;
//                        $receiver_stock->type_size_id = $request_detail->size_id;
//                        $receiver_stock->stock = $request_detail->quantity;
//                        $receiver_stock->save();
//
//                    }


                }

                $stock_request->status_id = 3;
                $stock_request->save();
                return response()->json(['status' => 0, 'success' => 'Request Successfully dispatched!']);

            } else {
                return response()->json(['status' => 1, 'error' => 'Could not find request!']);
            }
        }
    }

    public function stock_send_submit(Request $request)
    {

        $send_from = $request->send_from;
        $send_for = $request->send_for;
        $type_ids = explode(',', $request->send_type_ids);
        $type_size_ids = explode(',', $request->send_size_ids);
        $quantities = explode(',', $request->send_quantities);
        $user_id = Auth::id();
        $warehouse_stock_request = new WarehouseStockRequest();
        $warehouse_stock_request->requested_by = $send_for;
        $warehouse_stock_request->send_by = $send_from;
        $warehouse_stock_request->created_by = $user_id;
        $warehouse_stock_request->status_id = 1;
        $warehouse_stock_request->save();

        foreach ($type_ids as $index => $type) {
            $warehouse_stock_details = new WarehouseStockRequestDetail();
            $warehouse_stock_details->request_id = $warehouse_stock_request->id;
            $warehouse_stock_details->type_id = $type;
            $warehouse_stock_details->size_id = $type_size_ids[$index];
            $warehouse_stock_details->quantity = $quantities[$index];
            $warehouse_stock_details->save();
        }

        return redirect()->back()->with('success', 'Stock requested successfully!');
    }

    public function packaging_request_remarks(Request $request)
    {
        $shipment_id = $request->id;
        $remarks = $request->remarks;
        $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment_id)->latest('id')->first();
        if ($shipment_journey) {
            $shipment_journey->remarks = $remarks;
            $shipment_journey->save();
        } else {
            return response()->json(['status' => 0, 'error' => 'Packaging request remarks can\'t be updated!']);
        }

        return response()->json(['status' => 1, 'success' => 'Packaging request remarks updated successfully!']);
    }

    public function packaging_request_submit(Request $request)
    {

        $user_id = $request->shippers_select;
        $packaging_type_ids = explode(",", $request->packaging_type_ids);
        $packaging_size_ids = explode(",", $request->packaging_size_ids);
        $packaging_quantities = explode(",", $request->packaging_quantities);

        $total_charges = 0;

        foreach ($packaging_type_ids as $index => $packaging_type_id) {
            $charges = PackagingMaterialTypeSizes::find($packaging_size_ids[$index]);

            $total_charges += $packaging_quantities[$index] * $charges->standard_charges;
        }

        $today = Carbon::today();

        $discount = DiscountCharge::where('user_id', $user_id)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today)->whereNotNull('packaging');

        if ($discount->exists()) {
            $discount = $discount->orderBy('shipping_mode_id', 'ASC')->first();

            $discount_packaging = $discount->packaging;

            if (strpos($discount_packaging, '%') !== FALSE) {
                $discount_packaging = (floatval(str_replace('%', '', $discount_packaging)) / 100) * $total_charges;
                $total_charges -= $discount_packaging;
            } else {
                $total_charges -= floatval($discount_packaging);
            }
        }

        if ($request->input('address_select') != 0) {
            $address_id = $request->input('address_select');
            $user_address = UserShippingInfo::find($address_id);
        }


        if ($request->mode_of_payment == 1) {
            if ($request->input('address_select') == 0) {
                $result = PackagingMaterialRequest::create([
                    'user_id' => $user_id,
                    'city_id' => $request->new_pickup_city,
                    'address' => $request->new_pickup_address,
                    'poc' => $request->new_pickup_person_of_contact,
                    'phone' => $request->new_pickup_phone_number,
                    'amount' => $total_charges,
                    'status_id' => 1,
                    'packaging_payment_mode_id' => $request->mode_of_payment,
                    'requested_by' => Auth::id()
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
                    'packaging_payment_mode_id' => $request->mode_of_payment,
                    'requested_by' => Auth::id()
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
                return redirect()->back()->with('success', 'Request submitted Successfully, The delivery for this request will be attempted to you within 2-3 working days and it cannot be cancelled after the status of this request is confirmed');
            } else {
                return redirect()->back()->with('error', 'Request not submitted!');
            }
        } else {
            if (PendingPayment::where('user_id', $user_id)->exists()) {
                $balance = PendingPayment::where('user_id', $user_id)->first()->pending_payment_shipments->sum('payable');

            } else {
                return redirect()->back()->with('error', 'Can\'t  Request material!');
            }

            if ($total_charges <= $balance) {
                if ($request->input('address_select') == 0) {
                    $result = PackagingMaterialRequest::create([
                        'user_id' => $user_id,
                        'city_id' => $request->new_pickup_city,
                        'address' => $request->new_pickup_address,
                        'poc' => $request->new_pickup_person_of_contact,
                        'phone' => $request->new_pickup_phone_number,
                        'amount' => $total_charges,
                        'status_id' => 1,
                        'packaging_payment_mode_id' => $request->mode_of_payment,
                        'requested_by' => Auth::id()
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
                        'packaging_payment_mode_id' => $request->mode_of_payment,
                        'requested_by' => Auth::id()
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
                    return redirect()->back()->with('success', 'Request submitted Successfully, The delivery for this request will be attempted to you within 2-3 working days and it cannot be cancelled after the status of this request is confirmed');
                } else {
                    return redirect()->back()->with('error', 'Request not submitted!');
                }

            } else {
                return redirect()->back()->with('error', 'Not enough balance!');
            }
        }
    }
}
