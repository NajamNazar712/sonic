<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Http\Models\City;
use Illuminate\Http\Request;
use App\Http\Models\CityArea;
use App\Http\Models\Shipment;
use App\HandoverShipmentPiece;
use App\Http\Models\Admin\Admin;
use Yajra\Datatables\Datatables;
use App\Http\Models\ShipmentPiece;
use Illuminate\Support\Facades\DB;
use App\Http\Models\ShipmentStatus;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\Handover\Handover;
use App\Http\Models\Handover\HandoverStatus;
use App\Http\Models\ShipmentScanningJourney;
use App\Http\Models\Handover\HandoverShipments;
use App\Http\Models\Handover\HandoverResponsibilities;
use App\Http\Models\Handover\HandoverShipmentsJourney;
use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Models\Admin\DeliveryLocationMappingKeyword;
use App\Http\Controllers\Admins\Handover\HandoverShipmentJourneyController;
use App\Http\Models\Admin\AdminDepartment;
use App\Http\Models\ShipmentsJourney;
use App\ShipmentScanningJourneyAreaLog;
use App\Http\Models\Handover\ExcessHandoverShipment;
use App\Http\Models\HR\EmployeeDesignation;

class AdminShipmentHandoverController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function handover_create_index()
    {

        // prevent users from manually accessing the old handover page
        $hub = HandoverResponsibilities::leftjoin('cities as c', 'c.id', '=', 'handover_responsibilities.hub_id')
            ->select(['c.id', 'c.name'])->groupBy('handover_responsibilities.hub_id')->get();
        return view('admin.handover_new.new_index')->with(['hubs' => $hub]);

        // $hub=HandoverResponsibilities::leftjoin('cities as c','c.id','=','handover_responsibilities.hub_id')
        // ->select(['c.id','c.name'])->groupBy('handover_responsibilities.hub_id')->get();
        // return view('admin.handover.index')->with(['hubs'=>$hub]);
    }

    public function handover_create_index_new()
    {
        $hub = HandoverResponsibilities::leftjoin('cities as c', 'c.id', '=', 'handover_responsibilities.hub_id')
            ->select(['c.id', 'c.name'])->groupBy('handover_responsibilities.hub_id')->get();
        return view('admin.handover_new.new_index')->with(['hubs' => $hub]);
    }

    public function handover_dropdown_val_fetch_from(Request $request)
    {
        $type = $request->type;
        $value = $request->get('value');
        // $dependent = $request->get('dependent');-
        $dependent = "select From Person";
        $data = HandoverResponsibilities::where('hub_id', $value)->where('status', 1)->distinct('id')->get();
        $output = '<option value ="">' . ucfirst($dependent) . '</option> ';
        foreach ($data as $row) {
            if ($type == 0 && isset($row->name)) {
                $output .= '<option value ="' . $row->id . '">' . $row->name . '</option> ';
            } else if ($type == 1 && !isset($row->name) && isset($row->admin_id)) {
                $output .= '<option value ="' . $row->id . '">' . Admin::where('id', $row->admin_id)->first()->name . '</option> ';
            }
        }
        echo $output;
    }

    // public function handover_dropdown_val_fetch_from(Request $request){
    //   $type = $request->type;
    //   $value = $request->get('value');
    //   $dependent = "select From Person";
    //   $data = DB::table('admins')
    //   ->leftJoin('handover_responsibilities as handover_admins', 'handover_admins.admin_id', 'admins.id')
    //   ->select('handover_admins.admin_id', 'handover_admins.name')
    //   ->where('handover_admins.hub_id', $value)
    //   ->whereNotNull('handover_admins.admin_id')
    //   ->get();
    //   $output = '<option value="">' . ucfirst($dependent) . '</option>';
    //   foreach ($data as $row) {
    //       if ($type == 0 && $row->name) {
    //           $output .= '<option value="' . $row->admin_id . '">' . $row->name . '</option>';
    //       } elseif ($type == 1 && /* !isset($row->name) && */ isset($row->admin_id)) {
    //           $output .= '<option value="' . $row->admin_id . '">' . $row->name . '</option>';
    //       }
    //   }
    //   echo $output;
    // }


    //admin.handover.create.shipment_details
    public function arrival_bulk_shipment_details(Request $request)
    {
        $shipment = Shipment::where('tracking_number', $request->tracking_number);
        if ($shipment->exists()) {
            $shipment = $shipment->first();
            $latest_shipper_status = $shipment->shipment_journey()->latest('id')->first()->shipper_status_id ?? null;
            $shipment_pieces1 = $shipment->pieces;
            $handover_shipment = HandoverShipments::where('shipment_id', $shipment->id)->whereIn('status', [1, 3]);
            // if(!in_array($latest_shipper_status, [1,3,5,14,18,21,23,25,26,28,30,31,32,34,36,37,38,51]) && isset($latest_shipper_status)){
            if ($handover_shipment->exists()) {
                return ['status' => 1, 'error' => 'Shipment is already in another Handover Note'];
            }

            $details = array();
            $details['id'] = $shipment->id;
            $details['tracking_number'] = $shipment->tracking_number;
            $details['shipper'] = $shipment->user->name;
            $details['phone_number'] = $shipment->user->phone;
            $details['pickup_date'] = $shipment->pickup_date;
            $details['special_instructions'] = $shipment->special_instructions;
            ShipmentScanningJourneyController::add($shipment->id, 26, 1, Auth::id(), NULL, NULL, NULL, NULL, session('latitude'), session('longitude'), NULL, $request->action);

            // Tokenize consignee address → dedupe → lower (for case-insensitive match)
            $tokens = collect(preg_split('/[^\p{L}\p{N}]+/u', $shipment->consignee_address ?? ''))
                ->filter()                      // remove empties
                ->map(fn($w) => mb_strtolower($w))
                ->unique()
                ->values()
                ->all();

            $delivery_area = 0; // default when not found

            if (!empty($tokens)) {
                // One query: find first active mapping in this city whose keyword is in the tokens (case-insensitive)
                $found = DeliveryLocationMappingKeyword::query()
                    ->join('delivery_location_mappings as dlm', 'delivery_location_mapping_keywords.mapping_id', '=', 'dlm.id')
                    ->where('dlm.city_id', $shipment->consignee_city_id)
                    ->where('dlm.status', 1) // only active areas
                    ->whereIn(DB::raw('LOWER(delivery_location_mapping_keywords.keyword)'), $tokens)
                    ->select('dlm.id as mapping_id', 'dlm.area_name', 'delivery_location_mapping_keywords.keyword')
                    ->first();

                if ($found) {
                    $delivery_area = (int) $found->mapping_id;
                }
            }

// Optional consistency check against request->delivery_location_mapping
            if (!is_null($request->delivery_location_mapping)) {
                if ((int) $request->delivery_location_mapping !== (int) $delivery_area) {
                    return ['status' => 1, 'error' => 'Delivery Location is different'];
                }
            }

            $details['delivery_area'] = $delivery_area;


            if ($shipment_pieces1 === 1) {

                return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
            } else if ($shipment_pieces1 > 1) {
                $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();
                $details['pieces_count'] = $shipment->pieces;
                ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), NULL, NULL, NULL, NULL, session('latitude'), session('longitude'), NULL, $request->action);
                return ['status' => 3, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
            } else {
                return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
            }
            // }else{
            //   return ['status' => 1, 'error' => 'Restricted To Scan !!'];

            // }
        }

    }


    //admin.handover.create.shipment_details_new
    public function arrival_bulk_shipment_details_new(Request $request)
    {
        $shipment = Shipment::where('tracking_number', $request->tracking_number);
        if ($shipment->exists()) {
            $shipment = $shipment->first();

            // $blocked_shipments = ShipmentsJourney::where('shipment_id', $shipment->id)
            // ->orderBy('created_at', 'desc')
            // ->orderBy('id', 'desc')
            // ->select('shipper_status_id')
            // ->first();
            if ($shipment->shipper_status_id == 51 || $shipment->shipper_status_id == 18) {
                ShipmentScanningJourneyController::add($shipment->id, 26, 1, Auth::id(), NULL, NULL, NULL, NULL, session('latitude'), session('longitude'), NULL, $request->action);
                return ['status' => 1, 'error' => 'Cannot scan this shipment.'];
            }

            $latest_shipper_status = $shipment->shipment_journey()->latest('id')->first()->shipper_status_id ?? null;
            $shipment_pieces1 = $shipment->pieces;
            $handover_shipment = HandoverShipments::where('shipment_id', $shipment->id)->whereIn('status', [1, 3]);
            // if(!in_array($latest_shipper_status, [1,3,5,14,18,21,23,25,26,28,30,31,32,34,36,37,38,51]) && isset($latest_shipper_status)){
            if ($handover_shipment->exists()) {
                return ['status' => 1, 'error' => 'Shipment is already in another Handover Note'];
            }
            $details = array();
            $details['id'] = $shipment->id;
            $details['tracking_number'] = $shipment->tracking_number;
            $details['shipper'] = $shipment->user->name;
            $details['phone_number'] = $shipment->user->phone;
            $details['pickup_date'] = $shipment->pickup_date;
            $details['special_instructions'] = $shipment->special_instructions;
            ShipmentScanningJourneyController::add($shipment->id, 26, 1, Auth::id(), NULL, NULL, NULL, NULL, session('latitude'), session('longitude'), NULL, $request->action);

            // --- Optimized, safe, case-insensitive delivery-area detection ---

            $delivery_area = 0; // default when not found

// Tokenize consignee address → letters/digits only, lowercase, dedupe
            $tokens = preg_split('/[^\p{L}\p{N}]+/u', $shipment->consignee_address ?? '');
            $tokens = array_values(array_unique(array_filter(array_map('mb_strtolower', $tokens))));

            if (!empty($tokens)) {
                $found = DeliveryLocationMappingKeyword::query()
                    ->join('delivery_location_mappings as dlm', 'delivery_location_mapping_keywords.mapping_id', '=', 'dlm.id')
                    ->where('dlm.city_id', $shipment->consignee_city_id)
                    ->where('dlm.status', 1) // active areas only
                    ->whereIn(DB::raw('LOWER(delivery_location_mapping_keywords.keyword)'), $tokens)
                    ->select('dlm.id as mapping_id')    // you can also select area_name if needed
                    ->first();

                if ($found) {
                    $delivery_area = (int) $found->mapping_id;
                }
            }

// Consistency check against requested mapping (if provided)
            if (!is_null($request->delivery_location_mapping)) {
                if ((int) $request->delivery_location_mapping !== $delivery_area) {
                    return ['status' => 1, 'error' => 'Delivery Location is different'];
                }
            }

            $details['delivery_area'] = $delivery_area;


            if ($shipment_pieces1 === 1) {

                return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
            } else if ($shipment_pieces1 > 1) {
                $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();
                $details['pieces_count'] = $shipment->pieces;
                ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), NULL, NULL, NULL, NULL, session('latitude'), session('longitude'), NULL, $request->action);
                return ['status' => 3, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
            } else {
                return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
            }
            // }else{
            //   return ['status' => 1, 'error' => 'Restricted To Scan !!'];

            // }
        }

    }


    //receive old screen
    public function handover_receive_index()
    {
        // return view('admin.handover.receive');
        $handover_bag_numbers = Handover::select('id', 'bag_number')
            ->whereNotNull('bag_number')
            ->orderBy('id', 'desc')
            ->get();
        return view('admin.handover_new.new_receive', compact('handover_bag_numbers'));
    }


    //receive new screen
    public function handover_receive_index_new()
    {
        $handover_bag_numbers = Handover::select('id', 'bag_number')
            ->whereNotNull('bag_number')
            ->orderBy('id', 'desc')
            ->get();
        return view('admin.handover_new.new_receive', compact('handover_bag_numbers'));
    }

    public function arrival_bulk_shipment_details_receive(Request $request)
    {
        $shipment = Shipment::where('tracking_number', $request->tracking_number);

        if ($shipment->exists()) {
            $shipment = $shipment->first();
            $shipment_pieces = $shipment->pieces;
            $handover_shipments = HandoverShipments::where('shipment_id', $shipment->id)->whereIn('status', [1, 3]);
            $details = array();
            $details['id'] = $shipment->id;
            $details['tracking_number'] = $shipment->tracking_number;
            $details['shipper'] = $shipment->user->name;
            $details['phone_number'] = $shipment->user->phone;
            $details['pickup_date'] = $shipment->pickup_date;
            $details['special_instructions'] = $shipment->special_instructions;

            if ($handover_shipments->exists() && $shipment_pieces == 1) {
                ShipmentScanningJourneyController::add($shipment->id, 27, 1, Auth::id(), NULL, NULL, NULL, NULL, session('latitude'), session('longitude'), NULL, $request->action);
                return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
            } else if ($shipment_pieces > 1) {
                $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();
                $details['pieces_count'] = $shipment->pieces;
                ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), NULL, NULL, NULL, NULL, session('latitude'), session('longitude'), NULL, $request->action);
                return ['status' => 3, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
            } else {
                return ['status' => 1, 'error' => 'Shipment not in Handover / not ready to update!'];
            }

        } else {
            return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
        }
    }


    public function arrival_bulk_shipment_details_receive_new(Request $request)
    {
        $shipment = Shipment::where('tracking_number', $request->tracking_number);
        if ($shipment->exists()) {
            $current_handover_id = $request->handover;
            $current_handover = Handover::where('id', $current_handover_id)->first();
            $current_bag_number = $current_handover->bag_number ?? '';

            $shipment = $shipment->first();
            $shipment_pieces = $shipment->pieces;

            // $lost_shipments = ShipmentsJourney::where('shipment_id', $shipment->id)
            //   ->orderBy('created_at', 'desc')
            //   ->orderBy('id', 'desc')
            //   ->select('shipper_status_id')
            //   ->first();

            if ($shipment->shipper_status_id == 18 || $shipment->shipper_status_id == 51) {
                return ['status' => 1, 'error' => 'This shipment is either lost of closed.'];
            }

            $handover_shipments = HandoverShipments::where('shipment_id', $shipment->id)->whereIn('status', [1, 3]);

            // trying to scan after partial recive
            $handover_shipment_id = HandoverShipments::where('shipment_id', $shipment->id)->select(['handover_id', 'shipment_id', 'status'])->first();
            if (($handover_shipment_id && $handover_shipment_id->handover_id == (int)$current_handover_id) && $handover_shipment_id->status == 2) {
                return ['status' => 1, 'error' => 'This shipment is already verified'];
            }

            // stop receving after 1 time
            $excess_handovers = ExcessHandoverShipment::where('shipment_ids', $shipment->id)
                ->select(['handover_id', 'shipment_ids', 'excess_shipment'])
                ->get();

            if ($excess_handovers->isNotEmpty()) {
                foreach ($excess_handovers as $excess_handover) {
                    if ($excess_handover->excess_shipment == 1) {
                        if ($excess_handover->handover_id == $current_handover_id || $excess_handover->excess_handover_id == $current_handover_id) {
                            return ['status' => 1, 'error' => 'This shipment is already added in this bag as an excess shipment.'];
                        }
                    } else if ($excess_handover->handover_id == $current_handover_id) {
                        return ['status' => 1, 'error' => 'This shipment is already verified.'];
                    }
                }
            }
            // shipments with no handover/bag
            if (!$handover_shipments->first()) {
                $details = array();
                $details['id'] = $shipment->id;
                $details['tracking_number'] = $shipment->tracking_number;
                $details['shipper'] = $shipment->user->name;
                $details['phone_number'] = $shipment->user->phone;
                $details['pickup_date'] = $shipment->pickup_date;
                $details['special_instructions'] = $shipment->special_instructions;
                // $details['bag_number'] = $bag_number;
                $details['verification_status'] = 'Excess';

                if ($shipment_pieces == 1) {
                    ShipmentScanningJourneyController::add($shipment->id, 27, 1, Auth::id(), NULL, NULL, NULL, NULL, session('latitude'), session('longitude'), NULL, $request->action);
                    return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
                } else if ($shipment_pieces > 1) {
                    $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();
                    $details['pieces_count'] = $shipment->pieces;
                    ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), NULL, NULL, NULL, NULL, session('latitude'), session('longitude'), NULL, $request->action);
                    return ['status' => 3, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                }
            } else if ($handover_shipments->first()) {
                $handover = Handover::where('id', $handover_shipments->first()->handover_id)->first();
                $bag_number = $handover->bag_number;

                $details = array();
                $details['id'] = $shipment->id;
                $details['tracking_number'] = $shipment->tracking_number;
                $details['shipper'] = $shipment->user->name;
                $details['phone_number'] = $shipment->user->phone;
                $details['pickup_date'] = $shipment->pickup_date;
                $details['special_instructions'] = $shipment->special_instructions;
                $details['bag_number'] = $bag_number;

                if ($bag_number == $current_bag_number) {
                    $details['verification_status'] = 'Verified';
                } elseif ($bag_number != $current_bag_number) {
                    $details['verification_status'] = 'Excess';
                }

                if ($handover_shipments->exists() && $shipment_pieces == 1) {
                    ShipmentScanningJourneyController::add($shipment->id, 27, 1, Auth::id(), NULL, NULL, NULL, NULL, session('latitude'), session('longitude'), NULL, $request->action);
                    return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
                } else if ($shipment_pieces > 1) {
                    $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();
                    $details['pieces_count'] = $shipment->pieces;
                    ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), NULL, NULL, NULL, NULL, session('latitude'), session('longitude'), NULL, $request->action);
                    return ['status' => 3, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                }
            } else {
                return ['status' => 1, 'error' => 'Shipment not in Handover / not ready to update!'];
            }
        } else {
            return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
        }
    }

    //admin.handover.create.store
    public function bulk_handover_submit(Request $request)
    {
        $shipment_ids = explode(',', $request->shipment_ids);
        // $hub_id = explode(',', $request->hub);
        $total = count($shipment_ids);
        if ($total > 0) {
            $from_admin_dept = Admin::find($request->from);
            $to_admin_dept = Admin::find($request->to);
            $handover = new Handover();
            $handover->created_by = Auth::id();
            $handover->from = $request->from;
            $handover->from_dept_area_desg = $from_admin_dept->Edesignation->department_id ?? null;
            $handover->to = $request->to;
            $handover->to_dept_area_desg = $to_admin_dept->Edesignation->department_id ?? null;
            $handover->hub = $request->hub_id;
            $handover->status_id = 1;
            $handover->shipments = $total;

            $handover->save();
            $handover_id = $handover->id;
            foreach ($shipment_ids as $shipment_id) {
                $handover_shipments = new HandoverShipments();
                $handover_shipments->handover_id = $handover_id;
                $handover_shipments->shipment_id = $shipment_id;
                $handover_shipments->status = 1;
                $handover_shipments->save();

                HandoverShipmentJourneyController::add($shipment_id, $handover_id, 1);
            }

            return redirect()->route('admin.handover.create.index')->with('success', 'Handover Note created Successfully!');
        }
        return redirect()->back()->with('error', 'No shipments scanned!');
    }

    //admin.handover.create.store_new
    public function bulk_handover_submit_new(Request $request)
    {

        $request->validate([
            'bag_number' => 'required|numeric|unique:handovers,bag_number',
        ]);

        // $shipment_ids_json = explode(',', $request->shipment_ids);

        $shipment_ids_json = json_decode($request->shipment_ids);
        // $shipment_ids = array_map(function($id) {
        //   return intval(substr($id, 6));
        // }, json_decode($shipment_ids_json, true));

        $normal_status_ids = [
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10,
            11, 12, 13, 14, 15, 17, 19,
            49, 50, 52, 53, 54, 55, 56,
            58, 59, 61, 62, 65, 67, 68
        ];
        $return_status_ids = [
            /* 18, */
            20, 21, 22, 23, 24, 25, 26,
            27, 28, 29, 30, 31, 32, 33, 34,
            35, 36, 37, 38, 44, 45, 46, 47,
            48, /* 51, */
            56, 57,
            69, 70, 72, 73, 75, 76
        ];

        // $shipment_type = ShipmentsJourney::select('id', 'shipment_id', 'shipper_status_id')
        // ->whereIn('shipment_id', $shipment_ids)
        // ->whereIn('id', function ($query) use ($shipment_ids) {
        //   $query->select(DB::raw('MAX(id)'))
        //   ->from('shipments_journey')
        //   ->whereIn('shipment_id', $shipment_ids)
        //   ->groupBy('shipment_id');
        // })
        // ->get();

        $shipmentIds = Shipment::whereIn('tracking_number', $shipment_ids_json)->select('id', 'shipper_status_id')->get();
        $shipper_status_ids = $shipmentIds->pluck('shipper_status_id');

        $has_normal = $shipper_status_ids->intersect($normal_status_ids)->isNotEmpty();
        $has_return = $shipper_status_ids->intersect($return_status_ids)->isNotEmpty();

        if ($has_normal && $has_return) {
            return redirect()->back()->with('error', 'All shipments must be of the same type (normal or return).');
        }

        // $hub_id = explode(',', $request->hub);
        $total = count($shipmentIds);
        if ($total > 0) {
            $from_admin_dept = Admin::find($request->from);
            $to_admin_dept = Admin::find($request->to);
            $handover = new Handover();
            $handover->created_by = Auth::id();
            $handover->from = $request->from;
            $handover->from_dept_area_desg = $from_admin_dept->Edesignation->department_id ?? null;
            $handover->to = $request->to;
            $handover->to_dept_area_desg = $to_admin_dept->Edesignation->department_id ?? null;
            $handover->hub = $request->hub_id;
            $handover->bag_number = $request->bag_number;
            $handover->status_id = 1;
            $handover->shipments = $total;

            $handover->save();
            $handover_id = $handover->id;
            foreach ($shipmentIds as $shipment_id) {
                $handover_shipments = new HandoverShipments();
                $handover_shipments->handover_id = $handover_id;
                $handover_shipments->shipment_id = $shipment_id->id;
                $handover_shipments->status = 1;
                $handover_shipments->save();
                HandoverShipmentJourneyController::add($shipment_id->id, $handover_id, 1);
            }
            return redirect()->route('admin.handover.create.new_index')->with('success', 'Handover Bag created Successfully!');
        }
        return redirect()->back()->with('error', 'No shipments scanned!');
    }

    public function bulk_handover_submit_receive(Request $request)
    {
        $shipment_ids = explode(',', $request->shipment_ids);
        $handover_ids = array();
        foreach ($shipment_ids as $shipment_id) {
            $handover_shipments = HandoverShipments::where('shipment_id', $shipment_id)->whereIn('status', [1, 3]);
            if ($handover_shipments->exists()) {
                $handover_shipments = $handover_shipments->latest()->first();
                $handover_id = $handover_shipments->handover_id;
                $handover_shipments->status = 2;
                $handover_shipments->save();
                if (!in_array($handover_id, $handover_ids)) {
                    $handover_ids[] = $handover_id;
                }

                HandoverShipmentJourneyController::add($shipment_id, $handover_id, 2);
            }
        }

        if (count($handover_ids) > 0) {
            foreach ($handover_ids as $handover_id) {
                $not_updated_count = HandoverShipments::where('handover_id', $handover_id)->where('status', 1)->count();
                $received_count = HandoverShipments::where('handover_id', $handover_id)->whereIn('status', [2, 3])->count();
                $handover = Handover::find($handover_id);
                if ($not_updated_count == 0) {
                    $handover->status_id = 4;
                } else {
                    $handover->status_id = 3;
                }
                $handover->received = $received_count;
                $handover->received_at = Carbon::now();
                $handover->received_by = Auth::id();
                $handover->save();
            }
        }
        return redirect()->route('admin.handover.receive.index')->with('success', 'Handover Note Received Successfully!');

    }

    public function bulk_handover_submit_receive_new(Request $request)
    {
        $shipment_ids = explode(',', $request->shipment_ids);
        $handover_ids = array();
        foreach ($shipment_ids as $shipment_id) {
            $handover_shipments = HandoverShipments::where('shipment_id', $shipment_id)->whereIn('status', [1, 3]);
            if ($handover_shipments->exists()) {
                $handover_shipments = $handover_shipments->latest()->first();
                $handover_id = $handover_shipments->handover_id;
                $handover_shipments->status = 2;

                $current_handover_bag = Handover::where('id', (int)$request->handover_id)
                    ->select(['id', 'bag_number'])
                    ->first();

                $receving_shipment_bag = Handover::where('id', $handover_shipments->handover_id)
                    ->select(['id', 'bag_number'])
                    ->first();

                if ($current_handover_bag->bag_number == $receving_shipment_bag->bag_number) {
                    $handover_shipments->save();
                }
                if (!in_array($handover_id, $handover_ids)) {
                    $handover_ids[] = $handover_id;
                }
                HandoverShipmentJourneyController::add($shipment_id, $handover_id, 2);

                // Save Verified or Excess shipments
                $excess_shipment = null;
                $request_bag_number = Handover::where('id', $request->handover_id)
                    ->select('id', 'bag_number')
                    ->first();
                $existing_bag_number = Handover::where('id', $handover_shipments->handover_id)
                    ->select('id', 'bag_number')
                    ->first();

                if ($request_bag_number->bag_number == $existing_bag_number->bag_number) {
                    $excess_shipment = 0;
                    $data = [
                        'handover_id' => (int)$request->handover_id,
                        'excess_handover_id' => null,
                        'bag_number' => $request_bag_number->bag_number,
                        'excess_bag_number' => null,
                        'shipment_ids' => $shipment_id,
                        'excess_shipment' => $excess_shipment
                    ];
                    ExcessHandoverShipment::create($data);
                } else {
                    $excess_handover_id = Handover::where('bag_number', $existing_bag_number->bag_number)->first()->id;
                    $excess_shipment = 1;
                    $data = [
                        'handover_id' => (int)$request->handover_id,
                        'excess_handover_id' => $excess_handover_id,
                        'bag_number' => null,
                        'excess_bag_number' => $existing_bag_number->bag_number,
                        'shipment_ids' => $shipment_id,
                        'excess_shipment' => $excess_shipment
                    ];
                    ExcessHandoverShipment::create($data);
                }
            } else if (!$handover_shipments->exists()) {
                $excess_shipment = 1;
                $data = [
                    'handover_id' => (int)$request->handover_id,
                    'excess_handover_id' => null,
                    'bag_number' => null,
                    'excess_bag_number' => null,
                    'shipment_ids' => $shipment_id,
                    'excess_shipment' => $excess_shipment
                ];
                ExcessHandoverShipment::create($data);
            }
        }

        if (count($handover_ids) > 0) {
            foreach ($handover_ids as $handover_id) {
                $not_updated_count = HandoverShipments::where('handover_id', $handover_id)->where('status', 1)->count();
                $received_count = HandoverShipments::where('handover_id', $handover_id)->whereIn('status', [2, 3])->count();
                $handover = Handover::find($handover_id);
                if ($not_updated_count == 0) {
                    $handover->status_id = 4;
                } else {
                    $handover->status_id = 3;
                }
                $handover->received = $received_count;
                $handover->received_at = Carbon::now();
                $handover->received_by = Auth::id();
                $handover->save();
            }
        }
        return redirect()->route('admin.handover.receive.new_index')->with('success', 'Handover Bag Received Successfully!');

    }

//list
    public function handover_list_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 383);
        $hubs = HandoverResponsibilities::leftjoin('cities as c', 'c.id', '=', 'handover_responsibilities.hub_id')
            ->select(['c.id', 'c.name'])->groupBy('handover_responsibilities.hub_id')->get();

        $handover_admins = HandoverResponsibilities::select('admin.id as id', 'admin.name as name', 'handover_responsibilities.id as handover_responsibility_id')
            ->join('admins as admin', 'admin.id', 'handover_responsibilities.admin_id')
            ->where('admin_id', '!=', '')
            ->where('admin.status', 1)
            ->distinct('id')
            ->get();
        $areas = DB::table('city_areas')->where('status', 1)->select('id', 'name')->get();

        return view('admin.handover.list')->with(['hubs' => $hubs, 'handover_admins' => $handover_admins, 'areas' => $areas]);
    }

    public function handover_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 384);
        }

        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = date('Y-m-d 00:00:01', strtotime($request->get('search_date_from')));
            $to = date('Y-m-d 23:59:59', strtotime($request->get('search_date_to')));
        } else {
            $date = date('Y-m-d');
            $from = date('Y-m-d 00:00:01', strtotime($date . '-3 month'));
            $to = date('Y-m-d 23:59:59', strtotime($date));
        }

        $normal_status_ids = [
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10,
            11, 12, 13, 14, 15, 17, 19, 49,
            50, 52, 53, 54, 55, 56,
            58, 59, 61, 62, 65, 67, 68
        ];

        $return_status_ids = [
            20, 21, 22, 23, 24, 25, 26,
            27, 28, 29, 30, 31, 32, 33, 34,
            35, 36, 37, 38, 44, 45, 46, 47,
            48, 56, 57, 69, 70, 72, 73, 75, 76
        ];

        $normal_status_ids_str = implode(',', $normal_status_ids);
        $return_status_ids_str = implode(',', $return_status_ids);

        $handover_list = DB::connection('reports')->table('handovers')
            ->leftjoin('cities as c', 'c.id', '=', 'handovers.hub')
            ->leftJoin('admins as a', function ($join) {
                $join->on('a.id', '=', 'handovers.created_by')
                    ->where('a.role_id', '!=', 1);
            })
            ->leftJoin('admins as ad', function ($join) {
                $join->on('ad.id', '=', 'handovers.received_by')
                    ->where('ad.role_id', '!=', 1);
            })
            ->join('handover_statuses as hs', 'hs.id', '=', 'handovers.status_id')
            ->join('handover_responsibilities as hr', 'hr.id', '=', 'handovers.from')
            ->join('handover_responsibilities as hor', 'hor.id', '=', 'handovers.to')
            ->join('handover_shipments as hss', 'hss.handover_id', '=', 'handovers.id')
            ->leftJoin('admins', 'hr.admin_id', '=', 'admins.id')
            ->leftJoin('employee_designations', 'admins.designation_id', '=', 'employee_designations.id')
            ->leftJoin('admin_departments', 'employee_designations.department_id', '=', 'admin_departments.id')
            ->leftJoin('admins as to_admin', 'hor.admin_id', '=', 'to_admin.id')
            ->leftJoin('employee_designations as ed', 'to_admin.designation_id', '=', 'ed.id')
            ->leftJoin('admin_departments as adp', 'ed.department_id', '=', 'adp.id')
            ->join('shipments as s', 's.id', '=', 'hss.shipment_id')
            ->leftJoin('city_areas as c_from', 'c_from.id', '=', 'hr.city_area_id')
            ->leftJoin('city_areas as c_to', 'c_to.id', '=', 'hor.city_area_id')
            ->leftJoin('handover_shipments_journeys as hsj_f', function ($join) {
                $join->on('hsj_f.handover_id', '=', 'handovers.id')
                    ->where('hsj_f.id', '=', DB::connection('reports')->raw('(select max(id) from handover_shipments_journeys where handover_shipments_journeys.handover_id = handovers.id and status = 1)'));
            })
            ->leftJoin('handover_shipments_journeys as hsj_r', function ($join) {
                $join->on('hsj_r.handover_id', '=', 'handovers.id')
                    ->where('hsj_r.id', '=', DB::connection('reports')->raw('(select max(id) from handover_shipments_journeys where handover_shipments_journeys.handover_id = handovers.id and status = 2)'));
            })
            ->leftJoin('shipment_scanning_journeys as ssj_hss_f', function ($join) {
                $join->on('ssj_hss_f.shipment_id', '=', 'hsj_f.shipment_id')
                    ->where('ssj_hss_f.screen_location_id', '=', 26)
                    ->whereRaw('ssj_hss_f.id = (select max(id) from shipment_scanning_journeys where shipment_scanning_journeys.updated_at <= hsj_f.updated_at AND shipment_scanning_journeys.shipment_id = hsj_f.shipment_id)');
            })
            ->leftJoin('shipment_scanning_journeys as ssj_hss_r', function ($join) {
                $join->on('ssj_hss_r.shipment_id', '=', 'hsj_r.shipment_id')
                    ->where('ssj_hss_r.screen_location_id', '=', 27)
                    ->whereRaw('ssj_hss_r.id = (select max(id) from shipment_scanning_journeys where shipment_scanning_journeys.updated_at <= hsj_r.updated_at AND shipment_scanning_journeys.shipment_id = hsj_r.shipment_id)');
            })
            ->leftJoin('shipment_scanning_journey_area_logs as ssj_f', 'ssj_f.shipment_scanning_journey_id', '=', 'ssj_hss_f.id')
            ->leftJoin('shipment_scanning_journey_area_logs as ssj_r', 'ssj_r.shipment_scanning_journey_id', '=', 'ssj_hss_r.id')
            ->leftJoin('city_areas as caf', 'caf.id', '=', 'ssj_f.area_id')
            ->leftJoin('city_areas as car', 'car.id', '=', 'ssj_r.area_id')
            ->leftJoin('excess_handover_shipments', function ($join) {
                $join->on('excess_handover_shipments.handover_id', '=', 'handovers.id')
                    ->where('excess_handover_shipments.excess_shipment', '=', 1);
            })
            ->select([
                'handovers.id as handover_id', 'a.name as created_by', 'a.id as created_by_id',
                'ad.name as received_by', 'ad.id as received_by_id', 's.shipper_status_id',
                'hr.admin_id as from_admin_id', 'hor.admin_id as to_admin_id', 'c.name as hub',
                'handovers.shipments as shipment_count', 'handovers.shipments as total_shipments', 'hs.name as status',
                'handovers.received as received_shipments', 'hr.name as from_name', 'hor.name as to_name',
                'admin_departments.name as from_dept_area_desg', 'adp.name as to_dept_area_desg',

                'handovers.received_at', 'handovers.created_at',
                DB::raw('(select shipments - received_shipments from handovers where handovers.id = hss.handover_id) as remaining'),
                DB::raw('SUM(s.pieces) as shipment_pieces'), 'c_from.name as from_area', 'c_to.name as to_area',
                'ssj_f.location_status as forward_location_status', 'ssj_r.location_status as received_location_status',
                'caf.name as forwarded_area_name', 'car.name as received_area_name',
                'handovers.bag_number as bag_number',
                'hss.shipment_id',
                DB::raw("COUNT(DISTINCT excess_handover_shipments.id) AS excess_shipments")
            ]);

        if ($tracking_number = $request->get('search_tracking')) {
            $handover_list->where('s.tracking_number', $tracking_number);
        }

        if ($hub = $request->get('search_hub')) {
            $handover_list->where('handovers.hub', '=', $hub);
        }

        if ($from_admin = $request->get('search_from_admin')) {
            $handover_list->where('handovers.from', '=', $from_admin);
        }
        if ($to_admin = $request->get('search_to_admin')) {
            $handover_list->where('handovers.to', '=', $to_admin);
        }
        if ($bag_number = $request->get('search_bag_number')) {
            $handover_list->where('handovers.bag_number', '=', $bag_number);
        }

        $handover_list->whereBetween('handovers.created_at', [$from, $to])
            // ->orderBy('handovers.id', 'DESC')
            ->groupBy('handovers.id');

        $datatable = Datatables::of($handover_list)
            ->addColumn('handover_id_padded', function ($handover) {
                return str_pad($handover->handover_id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('shipment_count', function ($handover_list) {
                if ($handover_list->shipment_count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $handover_list->shipment_count . '</button>';
                } else {
                    return 0;
                }
            })
            ->filterColumn('excess_shipments', function ($query, $keyword) {
                $query->whereRaw("
                        (
                            SELECT COUNT(DISTINCT excess_handover_shipments.id)
                            FROM excess_handover_shipments
                            WHERE excess_handover_shipments.handover_id = handovers.id
                            AND excess_handover_shipments.excess_shipment = 1
                        ) = ?
                    ", [$keyword]);
            })
            ->editColumn('excess_shipments', function ($handover_list) {
                if ($handover_list->excess_shipments > 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $handover_list->excess_shipments . '</button>';
                } else {
                    return 0;
                }
            })
            ->addColumn('bag_type', function ($handover) use ($normal_status_ids, $return_status_ids) {
                if (is_null($handover->bag_number)) {
                    return '-';
                }
                if (in_array($handover->shipper_status_id, $normal_status_ids)) {
                    return 'Normal';
                } elseif (in_array($handover->shipper_status_id, $return_status_ids)) {
                    return 'Return';
                } else {
                    return '-';
                }
            })
            ->filterColumn('bag_type', function ($query, $keyword) use ($normal_status_ids, $return_status_ids) {
                $query->where(function ($subQuery) use ($keyword, $normal_status_ids, $return_status_ids) {
                    // Normalize the keyword for case-insensitivity and trim spaces
                    $keyword = strtolower(trim($keyword));
                    if ($keyword === 'r' || $keyword === 'n') {
                        // Show both "Normal" and "Return"
                        $subQuery->whereIn('s.shipper_status_id', array_merge($normal_status_ids, $return_status_ids))
                            ->whereNotNull('handovers.bag_number');
                    } elseif (str_starts_with('normal', $keyword)) {
                        $subQuery->whereIn('s.shipper_status_id', $normal_status_ids)
                            ->whereNotNull('handovers.bag_number');
                    } elseif (str_starts_with('return', $keyword)) {
                        $subQuery->whereIn('s.shipper_status_id', $return_status_ids)
                            ->whereNotNull('handovers.bag_number');
                    } elseif ($keyword === '-') {
                        // Include only rows where bag_number is NULL
                        $subQuery->whereNull('handovers.bag_number');
                    } else {
                        // Fallback for invalid keywords: force no results
                        $subQuery->whereRaw('1 = 0');
                    }
                });
            })
            ->editColumn('from', function ($handover_list) {
                if (isset($handover_list->from_admin_id)) {
                    return Admin::where('id', $handover_list->from_admin_id)->first()->name;
                } else {
                    return $handover_list->from_name;
                }
            })
            ->filterColumn('from', function ($query, $keyword) {
                $to_admin_ids = Admin::where('name', 'LIKE', '%' . $keyword . '%')->pluck('id');
                if ($to_admin_ids->isNotEmpty()) {
                    $query->whereIn('hr.admin_id', $to_admin_ids);
                } else {
                    $query->whereRaw('1 = 0');
                }
            })
            ->editColumn('to', function ($handover_list) {
                if (isset($handover_list->to_admin_id)) {
                    return Admin::where('id', $handover_list->to_admin_id)->first()->name;
                } else {
                    return $handover_list->to_name;
                }
            })
            ->filterColumn('to', function ($query, $keyword) {
                $to_admin_ids = Admin::where('name', 'LIKE', '%' . $keyword . '%')->pluck('id');
                if ($to_admin_ids->isNotEmpty()) {
                    $query->whereIn('hor.admin_id', $to_admin_ids);
                } else {
                    $query->whereRaw('1 = 0');
                }
            })
            ->addColumn('user_type', function ($handover_list) {
                if (isset($handover_list->from_admin_id, $handover_list->to_admin_id)) {
                    return 'User';
                } else {
                    return 'Department';
                }
            })
            ->filterColumn('user_type', function ($query, $keyword) {
                $query->where(function ($query) use ($keyword) {
                    $keyword = strtolower($keyword);

                    if (stripos('user', $keyword) !== false) {
                        $query->whereNotNull('hr.admin_id')
                            ->whereNotNull('hor.admin_id');
                    } elseif (stripos('department', $keyword) !== false) {
                        $query->whereNull('hr.admin_id')
                            ->whereNull('hor.admin_id');
                    } elseif (stripos('department', $keyword) === false || stripos('user', $keyword) === false) {
                        $query->whereRaw('1 = 0');
                    }
                });
            })
            ->addColumn('remaining_shipment_count', function ($handover_list) {
                if ($handover_list->shipment_count != 0 && $handover_list->received_shipments != 0) {
                    $remaining = $handover_list->shipment_count - $handover_list->received_shipments;
                    if ($remaining > 0) {
                        return '<button class="btn btn-sm btn-outline-info align-middle">' . $remaining . '</button>';
                    } else {
                        return 0;
                    }
                } else {
                    return 0;
                }
            })
            ->addColumn('shipment_pieces', function ($handover_list) {
                return '<button class="btn btn-sm btn-outline-info shipment_pieces align-middle">Piece(s) Breakup</button>';
            })
            ->editColumn('created_at_area', function ($handover_list) {
                $forward_location_status = $handover_list->forward_location_status === 0 ? 'Off-site' : ($handover_list->forward_location_status === 1 ? 'On-site' : '-');
                $forwarded_area_name = $handover_list->forwarded_area_name ?? '-';
                return $forwarded_area_name . ' | ' . $forward_location_status;
            })
            ->filterColumn('created_at_area', function ($query, $keyword) {
                $keyword = '%' . strtolower(trim($keyword)) . '%';
                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery->whereRaw('LOWER(caf.name) LIKE ?', [$keyword])
                        ->orWhereRaw(
                            'LOWER(CASE 
                    WHEN ssj_f.location_status = 0 THEN "off-site"
                    WHEN ssj_f.location_status = 1 THEN "on-site"
                    ELSE "-" 
                    END
                  ) LIKE ?', [$keyword]);
                });
            })
            ->editColumn('received_at_area', function ($handover_list) {
                $received_location_status = $handover_list->received_location_status === 0 ? 'Off-site' : ($handover_list->received_location_status === 1 ? 'On-site' : '-');
                $received_area_name = $handover_list->received_area_name ?? '-';
                return $received_area_name . ' | ' . $received_location_status;
            })
            ->filterColumn('received_at_area', function ($query, $keyword) {
                $keyword = '%' . strtolower(trim($keyword)) . '%';
                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery->whereRaw('LOWER(car.name) LIKE ?', [$keyword])
                        ->orWhereRaw(
                            'LOWER(CASE 
                          WHEN ssj_r.location_status = 0 THEN "off-site"
                          WHEN ssj_r.location_status = 1 THEN "on-site"
                          ELSE "-" 
                      END) LIKE ?', [$keyword]);
                });
            });

        if ($hub = $request->get('search_hub')) {
            $handover_list->where('handovers.hub', '=', $hub);
        }

        if ($from_admin = $request->get('search_from_admin')) {
            $handover_list->where('handovers.from', '=', $from_admin);
        }
        if ($to_admin = $request->get('search_to_admin')) {
            $handover_list->where('handovers.to', '=', $to_admin);
        }
        if ($bag_number = $request->get('search_bag_number')) {
            $handover_list->where('handovers.bag_number', '=', $bag_number);
        }

        if ($search_area = $request->get('search_area')) {
            // $datatable->where(function($query) use ($search_area) {
            //     $query->where('ssj_f.area_id', '=', $search_area)
            //     ->orWhere('ssj_r.area_id', '=', $search_area);
            // });

            $area_name = CityArea::where('id', (int)$search_area)
                ->select('name')
                ->first();

            $handover_list->where(function ($query) use ($area_name) {
                $query->where('c_from.name', '=', $area_name->name)
                    ->orWhere('c_to.name', '=', $area_name->name);
            });
        }
        return $datatable->rawColumns(['shipment_pieces', 'remaining_shipment_count', 'excess_shipments', 'shipment_count'])->make(true);
    }

    public function handover_shipments_count(Request $request)
    {

        $handover_id = $request->input('id');
        $handover_shipments = HandoverShipments::where('handover_id', $handover_id)->get();
        $shipments = array();
        if ($handover_shipments->count() != 0) {
            foreach ($handover_shipments as $handover_shipment) {
                $shipment = Shipment::find($handover_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Handover Note Shipments', 'shipments' => $shipments];
        } else {
            return ['status' => 0, 'success' => 'No Handover Note Shipments', 'shipments' => FALSE];
        }
    }

    public function excess_handover_shipments_count(Request $request)
    {
        $handover_id = $request->input('id');
        $excess_handover_shipments = ExcessHandoverShipment::where('handover_id', $handover_id)
            ->where('excess_shipment', 1)
            ->select('shipment_ids')
            ->get();
        $shipments = array();
        if ($excess_handover_shipments->count() != 0) {
            foreach ($excess_handover_shipments as $excess_handover_shipment) {
                $shipment = Shipment::find($excess_handover_shipment->shipment_ids);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Handover Note Shipments', 'shipments' => $shipments];
        } else {
            return ['status' => 0, 'success' => 'No Handover Note Shipments', 'shipments' => FALSE];
        }
    }

    public function handover_shipments_delivered(Request $request)
    {

        $handover_ids = $request->input('handover_ids');
        //$handover_ids = explode(',', $request->handover_ids);

        foreach ($handover_ids as $handover_id) {
            $handover_request = Handover::find($handover_id);

            if ($handover_request->status_id == 2) {
                return ['status' => 1, 'error' => 'Handover No # ' . $handover_request->id . ' has already been modified'];
            }
        }

        foreach ($handover_ids as $handover_id) {
            $handover_request = Handover::where('id', $handover_id)->where('status_id', 1)->first();

            if ($handover_request) {
                $handover_request->status_id = 2;
                $handover_request->save();
                $handover_shipments = $handover_request->handover_note_shipments;
                if (count($handover_shipments)) {
                    foreach ($handover_shipments as $shipment) {
                        $shipment->status = 3;
                        $shipment->save();
                        HandoverShipmentJourneyController::add($shipment->shipment_id, $handover_request->id, 3);
                    }
                }
            } else {
                return ['status' => 1, 'error' => 'Handover note already updated!'];
            }
        }

        return ['status' => 0, 'success' => 'Handover Shipments has been Delivered'];
    }

    public function handover_print(Request $request)
    {
        $handover_note_ids = $request->ids;

        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $html = '';
        $html = '
              <!doctype html>
              <html lang="en">
                <head>
                  <meta charset="utf-8">
                  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                  <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                  <title>Handover Note</title>

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

                    td.replacement span {
                      width: 22px;
                    }

                    td.replacement span img {
                      display: block;
                      width: 100%;
                      margin: auto;
                      background: #c8c8c8;
                      border-radius: 25px;
                    }

                    td.try_and_buy span {
                      width: 22px;
                    }

                    td.try_and_buy span img {
                      display: block;
                      width: 100%;
                      margin: auto;
                      background: #c8c8c8;
                      border-radius: 25px;
                    }
                    
                    td.complaint {
                          background: #09262e !important;
                          color: #ffffff;
                     }
                    td.details_changed {
                          background: #000000 !important;
                          color: #ffffff;
                     }
                     div.page
                      {
                          page-break-after: always;
                          page-break-inside: avoid;
                      }
                  </style>
                </head>
                <body>
                  <div>
                  
          ';

        foreach ($handover_note_ids as $handover_note_id) {
            $html .= '<div class="page text-center">';
            $total_shipments = 0;
            $shipments = HandoverShipments::where('handover_id', $handover_note_id)->select('shipment_id', 'status')->orderBy('shipment_id', 'asc')->get();

            $shipment_details = '
                    <table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="color primary"><strong>S. No.</strong></td>
                          <td class="color primary"><strong>Shipment ID</strong></td>
                          <td class="color primary"><strong>Tracking No.</strong></td>
                          <td class="color primary"><strong>Shipper</strong></td>
                          <td class="color primary"><strong>Status</strong></td>
                        </tr>
          ';


            foreach ($shipments as $parcel) {
                $total_shipments++;
                $shipment = Shipment::find($parcel->shipment_id);
                $handover_id = HandoverShipments::find($parcel->handover_id);
                $class = null;
                $status_check = $parcel->my_status->name;
                // $status=$status_check->my_status->name;
                // if($status_check == 3){
                //     $status = 'Received';
                // }
                // else{
                //     $status = 'Not Received';
                // }

                $shipment_details_row_start = '
                        <tr>
                          <td class="' . $class . '">' . $total_shipments . '</td>
                          <td class="' . $class . '">' . $shipment->id . '</td>
                          <td class="' . $class . '">' . $shipment->tracking_number . '</td>
                          <td class="' . $class . '">' . $shipment->user->name . '</td>
                          <td class="' . $class . '">' . $status_check . '</td>
              ';

                $shipment_details .= $shipment_details_row_start;


            }
            $shipment_details .= '
                      </tbody>
                    </table>
          ';


            $handover_note_details = Handover::where('id', $handover_note_id)->first();
            $status = $handover_note_details->status->name;
            $main_details = '
                    <table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="text-center align-middle"><img src="' . asset('img/trax_logo_new.png') . '" width="150" class="d-block mx-auto"></td>
                          <td class="text-center align-middle color primary"><strong>Handover Note</strong></td>
                          <td class="text-center align-middle color secondary">Created at ' . $handover_note_details->created_at . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                          <td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                        </tr>
                        <tr>
                          <td class="color secondary"><strong>Created By</strong></td>
                          <td>' . ucfirst(Auth::user()->name) . '</td>
                          <td colspan="2" rowspan="7" class="pl-1 pr-1 text-center align-middle">
                            <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($handover_note_id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                            <span><strong>' . str_pad($handover_note_id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                          </td>
                        </tr>
                        <tr>
                          <td class="color secondary"><strong>Total Shipments</strong></td>
                          <td>' . $handover_note_details->shipments . '</td>
                        </tr>
                        <tr>
                          <td class="color secondary"><strong>Received Shipments</strong></td>
                          <td>' . $handover_note_details->received . '</td>
                        </tr>
                        <tr>
                          <td class="color secondary"><strong>Received At</strong></td>
                          <td>' . $handover_note_details->received_at . '</td>
                        </tr>
                        <tr>
                          <td class="color secondary"><strong>Status</strong></td>
                          <td>' . $status . '</td>
                        </tr>
                      </tbody>
                    </table>
          ';

            $html .= $main_details;
            $html .= $shipment_details;

            $html .= '
          </div>
          ';

        }

        $html .= '
     
                  </div>

                  <script>
                    window.onload = function() {
                      window.print();
                    }
                  </script>
                </body>
              </html>
      ';


        //$view[]=$html;
        return $html;
    }


//Responsible
    public function responsibles_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 385);
        $hubs = City::select(['id', 'name'])->where('hub', 1)->get();
        return view('admin.handover.responsibles')->with(['hubs' => $hubs]);
    }

    public function responsibles_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 386);
        }
        $responsibles_list = HandoverResponsibilities::leftjoin('cities as c', 'c.id', '=', 'handover_responsibilities.hub_id')
            ->join('admins as a', 'a.id', '=', 'handover_responsibilities.created_by')
            ->leftjoin('city_areas as ca', 'ca.id', '=', 'handover_responsibilities.city_area_id')
            ->leftjoin('admins as u', 'u.id', '=', 'handover_responsibilities.updated_by')
            ->select('ca.name as area', 'handover_responsibilities.id as responsible_id', 'handover_responsibilities.name as name', 'c.name as hub', 'c.id as hub_id', 'a.name as created', 'u.name as updated', 'handover_responsibilities.status as status', 'handover_responsibilities.admin_id as admin_id');

        $datatable = Datatables::of($responsibles_list)
            ->setRowAttr([
                'hub' => function ($data) {
                    return $data->hub_id;
                }
            ])
            ->addColumn('status', function ($data) {
                if ($data->status == 0) {
                    return 'Disable';
                } else {
                    return 'Enable';
                }
            })
            ->editColumn('name', function ($data) {
                if (isset($data->name)) {
                    return $data->name;
                } else {
                    return Admin::where('id', $data->admin_id)->first()->name ?? '-';
                }
            })
            ->addColumn('action', function ($data) {

                $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

                $dropdown .= '<button type="button"  class="dropdown-item edit" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';

                if ($data->status == 1) {
                    $dropdown .= '<button type="button" class="dropdown-item disable" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-alert-octagon"></i></div><div class="col-9 offset-1">Disable</div></button>';
                } else {
                    $dropdown .= '<button type="button" class="dropdown-item enable" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check"></i></div><div class="col-9 offset-1">Enable</div></button>';
                }

                return $dropdown;

            })->rawColumns(['action']);

        return $datatable->make(true);

    }

    public function responsibles_add(Request $request)
    {
        $responsible = new HandoverResponsibilities();

        $responsible->hub_id = $request->hub;
        $responsible->created_by = Auth::id();
        $responsible->status = 1;
        $responsible->city_area_id = $request->city_area_id;


        if (isset($request->name)) {
            $responsible->name = $request->name;
            $responsible->admin_id = NULL;
        } else {
            $responsible->name = NULL;
            $responsible->admin_id = $request->city_responsible_hubs_admins;
        }

        $responsible->updated_by = Auth::id();
        $responsible->save();

        return redirect()->back()->with(['status' => 1, 'success' => "Responsible has been Added successfully!"]);
    }


    public function responsibles_status(Request $request)
    {
        $id = $request->id;
        $status = $request->status;
        $responsible = HandoverResponsibilities::find($id);
        if (!$responsible) {
            return response()->json(['status' => 1, 'error' => 'Not found!']);
        }

        if ($status == 1) {
            $responsible->status = 1;
        } else if ($status == 0) {
            $responsible->status = 0;
        }
        $responsible->save();

        return response()->json(['status' => 0, 'success' => 'Status updated successfully!']);
    }

    public function responsibles_editview(Request $request)
    {

        $responsible_id = HandoverResponsibilities::where('id', $request->id)->first();
        // $hub_id=HandoverResponsibilities::where('hub_id',$responsible_id->hub)->first();
        return response()->json(['status' => 1, 'responsible' => $responsible_id]);
    }

    public function responsibles_edit(Request $request)
    {
        $responsible = HandoverResponsibilities::find($request->id);

        if ($responsible) {
            $responsible->name = isset($request->name) ? $request->name : null;
            $responsible->hub_id = $request->hub;
            $responsible->updated_by = Auth::id();
            $responsible->city_area_id = $request->city_area_id;
            $responsible->admin_id = isset($request->name) ? null : $request->edit_city_responsible_hubs_admins;
            $responsible->save();

            return redirect()->back()->with(['status' => 1, 'success' => "Responsible has been Edited successfully!"]);
        }

        return redirect()->back()->with(['status' => 0, 'error' => "Responsible not found!"]);
    }


    public function handover_shipments_remaining(Request $request)
    {
        $handover_id = $request->input('id');
        $handover_shipments = HandoverShipments::where('handover_id', $handover_id)->where('status', 1)->get();
        $shipments = array();
        if ($handover_shipments->count() != 0) {
            foreach ($handover_shipments as $handover_shipment) {
                $shipment = Shipment::find($handover_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Handover Note Shipments', 'shipments' => $shipments];
        } else {
            return ['status' => 0, 'success' => 'No Handover Note Shipments', 'shipments' => FALSE];
        }
    }

    // for Pieces Modal
    public function handover_shipments_pieces(Request $request)
    {
        $handover_id = $request->input('id');
        $handover_shipments = HandoverShipments::where('handover_id', $handover_id)->get();
        $shipments = [];
        if ($handover_shipments->count() != 0) {
            foreach ($handover_shipments as $handover_shipment) {
                $shipment = Shipment::where('id', $handover_shipment->shipment_id)
                    ->first();
                if ($shipment) {
                    $tracking_number['tracking_number'][] = $shipment->tracking_number;
                    $pieces['pieces'][] = $shipment->pieces;
                }
            }
            return ['status' => 0, 'success' => 'Handover Note Shipments', 'tracking_number' => $tracking_number, 'pieces' => $pieces];
        } else {
            return ['status' => 0, 'success' => 'No Handover Note Shipments', 'shipments' => FALSE];
        }

    }

    public function sub_area(Request $request)
    {

        $handover_responsibility = HandoverResponsibilities::with('city_area')->find($request->id);

        if (isset($handover_responsibility->city_area)) {
            return response()->json(['status' => 1, 'sub_area_id' => $handover_responsibility->city_area->name]);
        } else {

            return response()->json(['status' => 0]);
        }

    }

    public function get_sub_area(Request $request)
    {
        if (isset($request->city_id)) {
            $admins = Admin::where('default_hub_id', $request->city_id)->where('status', 1)->get();
            $areas = CityArea::where('city_id', $request->city_id)->where('status', 1)->get();
        }

        return response()->json(['status' => 1, 'admins' => $admins, 'areas' => $areas]);


    }

    public function get_user(Request $request)
    {
        if (isset($request->city_id)) {
            $users = HandoverResponsibilities::select('admin.id as id', 'admin.name as name')
                ->join('admins as admin', 'admin.id', 'handover_responsibilities.admin_id')
                ->where('admin_id', '!=', '')->where('handover_responsibilities.hub_id', $request->city_id)
                ->where('handover_responsibilities.status', 1)
                ->where('admin.status', 1)
                ->distinct('id');
            if ($users->exists()) {
                return response()->json(['status' => 1, 'users' => $users->get()]);
            }
        } else {
            return response()->json(['status' => 0]);
        }
    }

    public static function handoverHubCount($shipment_ids, $current_hub)
    {
        $handover_shipments = HandoverShipments::whereIn('shipment_id', $shipment_ids)->get();
        $handover_ids = $handover_shipments->pluck('handover_id');
        $handovers = Handover::whereIn('id', $handover_ids)->get();
        $hub_count = $handovers->where('hub', $current_hub)->count();
        if ($hub_count >= 3) {
            return redirect()->back()->with('error', 'You cannot add more handovers for this hub.');
        }
        return null;
    }

    public function handover_shipment_type(Request $request)
    {
        $handover_id = $request->bag_number;
        $normal_status_ids = [
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10,
            11, 12, 13, 14, 15, 17, 19, 49,
            50, 52, 53, 54, 55, 56,
            58, 59, 61, 62, 65, 67, 68
        ];

        $return_status_ids = [
            /* 18, */
            20, 21, 22, 23, 24, 25, 26,
            27, 28, 29, 30, 31, 32, 33, 34,
            35, 36, 37, 38, 44, 45, 46, 47,
            48, /* 51, */
            56, 57,
            69, 70, 72, 73, 75, 76
        ];

        $normal_status_ids_str = implode(',', $normal_status_ids);
        $return_status_ids_str = implode(',', $return_status_ids);

        $handover_shipments = HandoverShipments::where('handover_id', $handover_id)
            ->leftJoin('shipments', 'handover_shipments.shipment_id', '=', 'shipments.id')
            // ->leftJoin('shipments_journey', 'handover_shipments.shipment_id', '=', 'shipments_journey.shipment_id')
            // ->leftJoin(DB::raw('(
            //   SELECT sj.id, sj.shipment_id, sj.shipper_status_id
            //   FROM shipments_journey sj
            //   JOIN (
            //       SELECT shipment_id, MAX(created_at) as max_created_at
            //       FROM shipments_journey
            //       GROUP BY shipment_id
            //   ) latest_journey
            //   ON sj.shipment_id = latest_journey.shipment_id
            //   AND sj.created_at = latest_journey.max_created_at
            // ) as latest_journey'),
            // 'handover_shipments.shipment_id', '=', 'latest_journey.shipment_id')
            ->leftJoin('users', 'shipments.user_id', '=', 'users.id')
            ->select(
                'handover_shipments.id',
                'handover_shipments.handover_id',
                'shipments.tracking_number',
                'shipments.consignee_phone_number_1',
                'shipments.consignee_name',
                'shipments.pickup_date',
                'shipments.special_instructions',
                'users.name as shipper_name',
                DB::raw("
                CASE 
                    WHEN shipments.shipper_status_id IN ($normal_status_ids_str) THEN 'Shipment Type is Normal'
                    WHEN shipments.shipper_status_id IN ($return_status_ids_str) THEN 'Shipment Type is Return'
                    ELSE 'Unknown'
                END as shipment_type_text
            ")
            )
            ->where('handover_shipments.status', '!=', 2)
            ->groupBy('shipments.tracking_number');

        $datatable = Datatables::of($handover_shipments)
            ->editColumn('tracking_number', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            });
        return $datatable
            ->rawColumns(['tracking_number'])
            ->make(true);
    }

    // public function same_hub_handover_count(Request $request)
    // {
    //   $shipment = Shipment::where('tracking_number', $request->tracking_number)
    //     ->select('id')
    //     ->first();
    //   if (!$shipment) {
    //     return ['status' => 0, 'error' => 'Shipment not found.'];
    //   }

    //   $handover_shipments = HandoverShipments::where('shipment_id', $shipment->id)
    //   ->take(3)
    //   ->join('handovers', 'handover_shipments.handover_id', '=', 'handovers.id')
    //   ->whereNotNull('handovers.bag_number')
    //   ->orderby('handover_shipments.created_at','desc')
    //   ->get();

    //   if ($handover_shipments->count() === 3) {
    //       $handover_ids = $handover_shipments->pluck('handover_id');
    //       $hubs = Handover::whereIn('id', $handover_ids)
    //         ->pluck('hub');
    //       if ($hubs->every(function ($hub) use ($hubs) {
    //         return $hub == $hubs->first();
    //       })) {
    //           if ($hubs->first() == $request->hub_id) {
    //             return ['status' => 1, 'error' => 'You cannot add more handovers for this hub.'];
    //           }
    //       }
    //   }
    // }

    public function same_hub_handover_count(Request $request)
    {
        $selected_bag_type = $request->selected_bag_type;
        $shipment = Shipment::where('tracking_number', $request->tracking_number)
            ->select('id', 'shipper_status_id')
            ->first();

        if (!$shipment) {
            return ['status' => 1, 'error' => 'Shipment not found.'];
        }

        $handover_exists = HandoverShipments::where('shipment_id', $shipment->id)
            ->latest('updated_at')
            ->where('status', 1)
            ->first();

        if ($handover_exists) {
            return ['status' => 1, 'error' => 'Handover is already created.'];
        }

        $normal_status_ids = [
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10,
            11, 12, 13, 14, 15, 17, 19, 49,
            50, 52, 53, 54, 55, 56,
            58, 59, 61, 62, 65, 67, 68
        ];

        $return_status_ids = [
            /* 18, */
            20, 21, 22, 23, 24, 25, 26,
            27, 28, 29, 30, 31, 32, 33, 34,
            35, 36, 37, 38, 44, 45, 46, 47,
            48, /* 51, */
            56, 57,
            69, 70, 72, 73, 75, 76
        ];

        //  Determine allowed statuses based on bag type
        $allowed_status_ids = (($selected_bag_type == 1) ? $normal_status_ids : $return_status_ids);

        if (in_array($shipment->shipper_status_id, [18, 51])) {
            return ['status' => 1, 'error' => 'This shipment is related to lost or case close'];
        }

        if (!in_array($shipment->shipper_status_id, $allowed_status_ids)) {
            return ['status' => 1, 'error' => 'Shipment type is not correct.'];
        }
    }

    public function bag_number_dropdown(Request $request)
    {
        $handover_bag_numbers = Handover::select('id', 'bag_number')
            ->whereNotNull('bag_number')
            ->orderBy('id', 'desc')
            ->take(6)
            ->get();
        return response()->json($handover_bag_numbers);
    }

    public function unique_bag_number(Request $request)
    {
        $bag_number = $request->bag_number;
        $stored_bag_numbers = Handover::whereNotNull('bag_number')->pluck('bag_number');
        if ($stored_bag_numbers->contains($bag_number)) {
            return ['status' => 1, 'error' => 'This bag number already exists.'];
        }
    }

    public function check_bag_type(Request $request)
    {
        $selected_bag_type = $request->selected_bag_type;
        $tracking_number = $request->tracking_number;

        $shipment = Shipment::where('tracking_number', $tracking_number)
            ->select('id', 'shipper_status_id')
            ->first();

        // if shipment doesn't exist
        if (!$shipment) {
            return ['status' => 1, 'error' => 'Shipment does not exist'];
        }

        $normal_status_ids = [
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10,
            11, 12, 13, 14, 15, 17, 19, 49,
            50, 52, 53, 54, 55, 56,
            58, 59, 61, 62, 65, 67, 68
        ];

        $return_status_ids = [
            /* 18, */
            20, 21, 22, 23, 24, 25, 26,
            27, 28, 29, 30, 31, 32, 33, 34,
            35, 36, 37, 38, 44, 45, 46, 47,
            48, /* 51, */
            56, 57,
            69, 70, 72, 73, 75, 76
        ];

        // normal shipments
        if ($selected_bag_type == 1) {
            $allowed_status_ids = $normal_status_ids;
        } // return shipments
        else {
            $allowed_status_ids = $return_status_ids;
        }

        if (($shipment->shipper_status_id == 18 || $shipment->shipper_status_id == 51)) {
            return ['status' => 1, 'error' => 'This shipment is related to lost or case close'];
        }
        if (!in_array($shipment->shipper_status_id, $allowed_status_ids)) {
            return ['status' => 1, 'error' => 'Shipment type is not correct.'];
        }
    }

    public function handover_exists(Request $request)
    {
        $tracking_number = $request->tracking_number;
        $shipment = Shipment::where('tracking_number', $tracking_number)
            ->select('id')
            ->first();

        // if shipment doesn't exist
        if (!$shipment) {
            return ['status' => 1, 'error' => 'Shipment does not exist'];
        }

        $handover_exists = HandoverShipments::where('shipment_id', $shipment->id)
            ->latest('updated_at')
            ->where('status', 1)
            ->first();

        if ($handover_exists) {
            return ['status' => 1, 'error' => 'Handover is already created.'];
        }
    }

    public function check_full_bag(Request $request)
    {
        $check_full_bag = Handover::where('id', (int)$request->bag_number)
            ->select(['shipments', 'received'])
            ->first();

        if ($check_full_bag == null) {
            return;
        }

        if ($check_full_bag->shipments == $check_full_bag->received) {
            return ['status' => 1, 'error' => 'This handover bag is fully received'];
        }
    }

    public function check_handover_bag_receive(Request $request)
    {
        $shipent_id = Shipment::where('tracking_number', $request->tracking_number)
            ->select(['id'])
            ->first()->id;

        $exisiting_handover_id = HandoverShipments::where('shipment_id', $shipent_id)
            ->orderBy('id', 'desc')
            ->first();

        $exisiting_handover_id = $exisiting_handover_id ? $exisiting_handover_id->handover_id : null;

        $existing_bag = Handover::where('id', $exisiting_handover_id)
            ->select('bag_number')
            ->first();

        $bag_number = $existing_bag ? $existing_bag->bag_number : null;

        if ($bag_number != null) {
            return ['status' => 1, 'error' => 'This CN: ' . $request->tracking_number . " cannot be scanned on the current screen. Please scan it on the 'New Handover Receive' screen"];
        }

        if ($exisiting_handover_id == null) {
            return ['status' => 1, 'error' => 'Shipment not in Handover / not ready to update!'];
        }
    }
}
