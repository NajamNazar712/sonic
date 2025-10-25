<?php

namespace App\Http\Controllers\Admins\Pudo;

use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Controllers\Admins\CheckDisputeShipmentsController;
use App\Http\Controllers\Admins\Handover\HandoverShipmentJourneyController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\EmployeeAttendanceController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentOpenBoxJourneyController;
use App\Http\Controllers\ShipmentScanningJourneyController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\ReturnNote;
use App\Http\Models\Admin\ReturnNoteShipment;
use App\Http\Models\Admin\ReturnReasonMandatoryShipper;
use App\Http\Models\Admin\RiderCategoryByPass;
use App\Http\Models\BookingType;
use App\Http\Models\City;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\Handover\Handover;
use App\Http\Models\Handover\HandoverShipments;
use App\Http\Models\PendingPayment;
use App\Http\Models\PendingPaymentShipment;
use App\Http\Models\Rider;
use App\Http\Models\Rider\RiderReturnNoteRequest;
use App\Http\Models\Rider\RiderReturnNoteRequestShipment;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentDetail;
use App\Http\Models\ShipmentPiece;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\ShipmentStatus;
use App\Models\ReturnTransferNote;
use App\Models\ReturnTransferNoteShipment;
use App\Models\RiderReturnTransferNoteRequest;
use App\Models\RiderReturnTransferNoteRequestShipment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class ReturnTransferNoteController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function get_shipment_details(Request $request)
    {
        if ($request->tracking != '') {

            $different_city_statuses_2 = array(22, 24, 27, 29, 33, 35, 42, 44, 45, 46, 47, 48, 60,156);
            $different_city_statuses = array(22, 24, 27, 29, 33, 35, 37, 42, 44, 45, 46, 47, 48, 60,156);
            $allowed_statuses =     array(20, 22, 24, 27, 29, 30, 33, 35, 37, 42, 44, 45, 46, 47, 48, 60,156);
            $return_note_statuses = array(20, 22, 24, 27, 29, 30, 33, 35, 37, 42, 44, 45, 46, 47, 48, 60,156);
            $shipment = Shipment::where('tracking_number', $request->tracking)->whereIn('shipper_status_id', $allowed_statuses);
            $status = '';
            if ($shipment->exists()) {
                $shipment = $shipment->first();
                $dispute_check = CheckDisputeShipmentsController::check($shipment->id);
                if (!$dispute_check) {
                    return ['status' => 1, 'error' => 'Shipment is in Dispute! For further assistance, please contact QA (CX)'];
                }
                ShipmentScanningJourneyController::add($shipment->id, 37, 1, Auth::id(), NULL, NULL, NULL, NULL, session('latitude'), session('longitude'), NULL, $request->action);
                if ($request->shipper_id != null) {
                    $mandatory_shipper = ReturnReasonMandatoryShipper::pluck('shipper_id')->toArray();
                    if ($request->shipper_id != $shipment->user_id) {
                        if (in_array($request->shipper_id, $mandatory_shipper) || in_array($shipment->user_id, $mandatory_shipper)) {
                            return ['status' => 1, 'error' => 'Different Shipper, scan shipments of same shipper!.'];
                        }
                    }
                }
                if ($shipment->return_address_id != NULL) {
                    $destination_id = $shipment->return_address->city_id;
                } else {
                    $destination_id = $shipment->pickup_address->city_id;
                }
                $destination_id = City::where('id', $destination_id)->select('hub_id')->first();
                $destination_id = $destination_id->hub_id; //first it was origin now for return its destination
                if (session('role_id') == 1 || in_array($destination_id, session('hubs'))) {
                    $origin = $shipment->consignee_city->hub_id; //let's suppose consignee city is origin now
                    if (!$request->has('hub_id')) {
                        if ($destination_id == $origin && (in_array($shipment->shipper_status_id, $return_note_statuses))) {
                            if ($shipment->return_address_id != NULL) {
                                $destination_city_id = $shipment->return_address->city_id;
                            } else {
                                $destination_city_id = $shipment->pickup_address->city_id;
                            }

                            $destination_city = City::find($destination_city_id);
                            if ($destination_city->id == $destination_city->hub_id) {
                                $destination = $destination_city->name;
                                $hub = $destination_city->id;
                            } else {
                                $hubid = $destination_city->hub_id;
                                $destinationHub = City::find($hubid);
                                $destination = $destinationHub->name;
                                $hub = $destinationHub->id;
                            }
                            $service = $shipment->booking_type->booking_type;
                            $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id);
                            if ($shipment_journey->exists()) {
                                $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->select('shipper_status_id', 'remarks')->latest()->first();
                                //                            $remarks = ($shipment_journey->remarks != '')? $shipment_journey->remarks:' - ';
                                $status_id = ($shipment_journey->shipper_status_id) ? $shipment_journey->shipper_status_id : '';
                                if ($status_id != '') {
                                    $status_name = ShipmentStatus::where('id', $status_id)->select('name')->first();
                                    $status = $status_name->name;
                                } else {
                                    $status = ' - ';
                                }
                            }
                            if ($shipment->booking_type_id != 4) {
                                $settings = GlobalSettings::where('type', 'return_note_restriction_bypass');

                                if ($settings->exists()) {
                                    $settings = $settings->first();
                                    $role_ids = array_map('intval', explode(',', $settings->text));
                                } else {
                                    $role_ids = array();
                                }
                                array_push($role_ids, 1);

                                if (!in_array(session('role_id'), $role_ids)) {
                                    if (!$shipment->packaging_material_request) {
                                        $shipper_payable = 0;
                                        $pending_payment = PendingPayment::where('user_id', $shipment->user_id)->first();

                                        if ($pending_payment) {
                                            // Get the sum of payable amounts directly
                                            $shipper_payable = PendingPaymentShipment::where('pending_payment_id', $pending_payment->id)
                                                ->sum('payable'); // Sum the payable amounts directly

                                            // If shipper_payable is between 0 and -1, set it to 0
                                            if ($shipper_payable < 0 && $shipper_payable > -1) {
                                                $shipper_payable = 0;
                                            }
                                        }

                                        if ($shipper_payable < 0) {
                                            return response()->json(['status' => 1, 'error' => 'Shipper with Negative Balance, Contact Sales Team!']);
                                        }
                                    }
                                }
                            }
                            $class = null;
                            if (CrmRequest::where('shipment_id', $shipment->id)->where('case_nature_id', 1)->whereIn('status_id', [2, 3, 5])->exists()) {
                                $class = 'complaint_row';
                            }

                            if (!$request->has('pieces_confirm')) {
                                if ($shipment->booking_type_id == 1 && $shipment->pieces > 1) {
                                    $details = array();
                                    $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();

                                    $details['id'] = $shipment->id;
                                    $details['shipper_id'] = $shipment->user_id;
                                    $details['tracking_number'] = $shipment->tracking_number;
                                    $details['pieces_count'] = $shipment->pieces;
                                    $details['pieces_tracking_numbers'] = $shipment_pieces;
                                    return ['status' => 2, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                                }
                            }
                            return response()->json(['status' => 0, 'shId' => $shipment->id, 'tracking_number' => $shipment->tracking_number, 'destination' => $destination, 'hub' => $hub, 'consignee_name' => $shipment->consignee_name, 'phone' => $shipment->consignee_phone_number_1, 'address' => $shipment->consignee_address, 'amount' => number_format($shipment->amount), 'service_type' => $service, 'shipment_status' => $status, 'class' => $class, 'shipper_id' => $shipment->user_id]);
                        } else
                            if ($destination_id != $origin && (in_array($shipment->shipper_status_id, $different_city_statuses))) {
                                if ($shipment->return_address_id != NULL) {
                                    $destination_city_id = $shipment->return_address->city_id;
                                } else {
                                    $destination_city_id = $shipment->pickup_address->city_id;
                                }

                                $destination_city = City::find($destination_city_id);
                                if ($destination_city->id == $destination_city->hub_id) {
                                    $destination = $destination_city->name;
                                    $hub = $destination_city->id;
                                } else {
                                    $hubid = $destination_city->hub_id;
                                    $destinationHub = City::find($hubid);
                                    $destination = $destinationHub->name;
                                    $hub = $destinationHub->id;
                                }

                                $service = $shipment->booking_type->booking_type;
                                $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id);
                                if ($shipment_journey->exists()) {
                                    $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->select('shipper_status_id', 'remarks')->latest()->first();
                                    //                            $remarks = ($shipment_journey->remarks != '')? $shipment_journey->remarks:' - ';
                                    $status_id = ($shipment_journey->shipper_status_id) ? $shipment_journey->shipper_status_id : '';
                                    if ($status_id != '') {
                                        $status_name = ShipmentStatus::where('id', $status_id)->select('name')->first();
                                        $status = $status_name->name;
                                    } else {
                                        $status = ' - ';
                                    }
                                }
                                if ($shipment->booking_type_id != 4) {
                                    $settings = GlobalSettings::where('type', 'return_note_restriction_bypass');

                                    if ($settings->exists()) {
                                        $settings = $settings->first();
                                        $role_ids = array_map('intval', explode(',', $settings->text));
                                        array_push($role_ids, 1);
                                        if (!in_array(session('role_id'), $role_ids)) {
                                            if (!$shipment->packaging_material_request) {
                                                $shipper_payable = 0;
                                                // Get the first PendingPayment for the given user_id
                                                $pending_payment = PendingPayment::where('user_id', $shipment->user_id)->first();

// Check if a pending payment exists
                                                if ($pending_payment) {
                                                    // Sum the payable values directly from the PendingPaymentShipment table
                                                    $shipper_payable = PendingPaymentShipment::where('pending_payment_id', $pending_payment->id)
                                                        ->sum('payable'); // This sums all payable amounts directly in the query

                                                    // If shipper_payable is between 0 and -1, set it to 0
                                                    if ($shipper_payable < 0 && $shipper_payable > -1) {
                                                        $shipper_payable = 0;
                                                    }
                                                }

                                                if ($shipper_payable < 0) {
                                                    return response()->json(['status' => 1, 'error' => 'Shipper with Negative Balance, Contact Sales Team!']);
                                                }
                                            }
                                        }
                                    }
                                }
                                $class = null;
                                if (CrmRequest::where('shipment_id', $shipment->id)->where('case_nature_id', 1)->whereIn('status_id', [2, 3, 5])->exists()) {
                                    $class = 'complaint_row';
                                }
                                if (!$request->has('pieces_confirm')) {
                                    if ($shipment->booking_type_id == 1 && $shipment->pieces > 1) {
                                        $details = array();
                                        $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();

                                        $details['id'] = $shipment->id;
                                        $details['shipper_id'] = $shipment->user_id;
                                        $details['tracking_number'] = $shipment->tracking_number;
                                        $details['pieces_count'] = $shipment->pieces;
                                        $details['pieces_tracking_numbers'] = $shipment_pieces;
                                        return ['status' => 2, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                                    }
                                }
                                return response()->json(['status' => 0, 'shipper_id' => $shipment->user_id, 'shId' => $shipment->id, 'tracking_number' => $shipment->tracking_number, 'destination' => $destination, 'hub' => $hub, 'consignee_name' => $shipment->consignee_name, 'phone' => $shipment->consignee_phone_number_1, 'address' => $shipment->consignee_address, 'amount' => number_format($shipment->amount), 'service_type' => $service, 'shipment_status' => $status, 'class' => $class]);
                            } else {
                                return ['status' => 1, 'error' => 'Return Shipment not arrived at origin center yet.'];
                            }
                    } else
                        if ($request->has('hub_id') && ($destination_id == $request->hub_id)) {
                            $same_city_statuses = array(20, 24, 27, 29, 30, 33, 35, 37, 42, 44, 45, 46, 47, 48, 60, 22,156);
                            if ($destination_id == $origin && (in_array($shipment->shipper_status_id, $same_city_statuses))) {
                                if ($shipment->return_address_id != NULL) {
                                    $destination_city_id = $shipment->return_address->city_id;
                                } else {
                                    $destination_city_id = $shipment->pickup_address->city_id;
                                }

                                $destination_city = City::find($destination_city_id);
                                if ($destination_city->id == $destination_city->hub_id) {
                                    $destination = $destination_city->name;
                                    $hub = $destination_city->id;
                                } else {
                                    $hubid = $destination_city->hub_id;
                                    $destinationHub = City::find($hubid);
                                    $destination = $destinationHub->name;
                                    $hub = $destinationHub->id;
                                }
                                $service = $shipment->booking_type->booking_type;
                                $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id);
                                if ($shipment_journey->exists()) {
                                    $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->select('shipper_status_id', 'remarks')->latest()->first();
                                    //                            $remarks = ($shipment_journey->remarks != '')? $shipment_journey->remarks:' - ';
                                    $status_id = ($shipment_journey->shipper_status_id) ? $shipment_journey->shipper_status_id : '';
                                    if ($status_id != '') {
                                        $status_name = ShipmentStatus::where('id', $status_id)->select('name')->first();
                                        $status = $status_name->name;
                                    } else {
                                        $status = ' - ';
                                    }
                                }
                                if ($shipment->booking_type_id != 4) {
                                    $settings = GlobalSettings::where('type', 'return_note_restriction_bypass');

                                    if ($settings->exists()) {
                                        $settings = $settings->first();
                                        $role_ids = array_map('intval', explode(',', $settings->text));
                                        array_push($role_ids, 1);
                                        if (!in_array(session('role_id'), $role_ids)) {
                                            if (!$shipment->packaging_material_request) {
                                                $shipper_payable = 0;
                                                // Get the first PendingPayment for the given user_id
                                                $pending_payment = PendingPayment::where('user_id', $shipment->user_id)->first();

                                                // If a PendingPayment record is found, calculate the total payable value
                                                if ($pending_payment) {
                                                    $shipper_payable = PendingPaymentShipment::where('pending_payment_id', $pending_payment->id)
                                                        ->sum('payable'); // Sum the payable amounts directly in the query
                                                }

                                                if ($shipper_payable < 0) {
                                                    return response()->json(['status' => 1, 'error' => 'Shipper with Negative Balance, Contact Sales Team!']);
                                                }
                                            }
                                        }
                                    }
                                }
                                $class = null;
                                if (CrmRequest::where('shipment_id', $shipment->id)->where('case_nature_id', 1)->whereIn('status_id', [2, 3, 5])->exists()) {
                                    $class = 'complaint_row';
                                }
                                if (!$request->has('pieces_confirm')) {
                                    if ($shipment->booking_type_id == 1 && $shipment->pieces > 1) {
                                        $details = array();
                                        $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();

                                        $details['id'] = $shipment->id;
                                        $details['tracking_number'] = $shipment->tracking_number;
                                        $details['pieces_count'] = $shipment->pieces;
                                        $details['pieces_tracking_numbers'] = $shipment_pieces;
                                        return ['status' => 2, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                                    }
                                }
                                return response()->json(['status' => 0, 'shipper_id' => $shipment->user_id, 'shId' => $shipment->id, 'tracking_number' => $shipment->tracking_number, 'destination' => $destination, 'hub' => $hub, 'consignee_name' => $shipment->consignee_name, 'phone' => $shipment->consignee_phone_number_1, 'address' => $shipment->consignee_address, 'amount' => number_format($shipment->amount), 'service_type' => $service, 'shipment_status' => $status, 'class' => $class]);
                            } else
                                if ($destination_id != $origin && (in_array($shipment->shipper_status_id, $different_city_statuses_2))) {
                                    if ($shipment->return_address_id != NULL) {
                                        $destination_city_id = $shipment->return_address->city_id;
                                    } else {
                                        $destination_city_id = $shipment->pickup_address->city_id;
                                    }
                                    $destination_city = City::find($destination_city_id);
                                    if ($destination_city->id == $destination_city->hub_id) {
                                        $destination = $destination_city->name;
                                        $hub = $destination_city->id;
                                    } else {
                                        $hubid = $destination_city->hub_id;
                                        $destinationHub = City::find($hubid);
                                        $destination = $destinationHub->name;
                                        $hub = $destinationHub->id;
                                    }

                                    $service = $shipment->booking_type->booking_type;
                                    $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id);
                                    if ($shipment_journey->exists()) {
                                        $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->select('shipper_status_id', 'remarks')->latest()->first();
                                        //                            $remarks = ($shipment_journey->remarks != '')? $shipment_journey->remarks:' - ';
                                        $status_id = ($shipment_journey->shipper_status_id) ? $shipment_journey->shipper_status_id : '';
                                        if ($status_id != '') {
                                            $status_name = ShipmentStatus::where('id', $status_id)->select('name')->first();
                                            $status = $status_name->name;
                                        } else {
                                            $status = ' - ';
                                        }
                                    }
                                    if ($shipment->booking_type_id != 4) {
                                        $settings = GlobalSettings::where('type', 'return_note_restriction_bypass');

                                        if ($settings->exists()) {
                                            $settings = $settings->first();
                                            $role_ids = array_map('intval', explode(',', $settings->text));
                                            array_push($role_ids, 1);
                                            if (!in_array(session('role_id'), $role_ids)) {
                                                if (!$shipment->packaging_material_request) {
                                                    $shipper_payable = 0;
                                                    // Get the first PendingPayment for the given user_id
                                                    $pending_payment = PendingPayment::where('user_id', $shipment->user_id)->first();

                                                    // If a PendingPayment record is found, calculate the total payable value
                                                    if ($pending_payment) {
                                                        $shipper_payable = PendingPaymentShipment::where('pending_payment_id', $pending_payment->id)
                                                            ->sum('payable'); // Sum the payable amounts directly in the query
                                                    }

                                                    if ($shipper_payable < 0) {
                                                        return response()->json(['status' => 1, 'error' => 'Shipper with Negative Balance, Contact Sales Team!']);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    $class = null;
                                    if (CrmRequest::where('shipment_id', $shipment->id)->where('case_nature_id', 1)->whereIn('status_id', [2, 3, 5])->exists()) {
                                        $class = 'complaint_row';
                                    }
                                    if (!$request->has('pieces_confirm')) {
                                        if ($shipment->booking_type_id == 1 && $shipment->pieces > 1) {
                                            $details = array();
                                            $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();

                                            $details['id'] = $shipment->id;
                                            $details['tracking_number'] = $shipment->tracking_number;
                                            $details['pieces_count'] = $shipment->pieces;
                                            $details['pieces_tracking_numbers'] = $shipment_pieces;
                                            return ['status' => 2, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                                        }
                                    }
                                    return response()->json(['status' => 0, 'shipper_id' => $shipment->user_id, 'shId' => $shipment->id, 'tracking_number' => $shipment->tracking_number, 'destination' => $destination, 'hub' => $hub, 'consignee_name' => $shipment->consignee_name, 'phone' => $shipment->consignee_phone_number_1, 'address' => $shipment->consignee_address, 'amount' => ($shipment->amount), 'service_type' => $service, 'shipment_status' => $status, 'class' => $class]);
                                } else {
                                    return ['status' => 1, 'error' => 'Return Shipment not arrived at origin center yet.'];
                                }
                        } else {
                            return ['status' => 1, 'error' => 'Different hub, scan shipments of same hub!.'];
                        }
                } else {
                    return ['status' => 1, 'error' => 'This Shipment doesn\'t belongs to your assigned hubs!'];
                }
            } else {
                return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present, Check tracking!'];
            }
        }
    }

    public function get_piece_details(Request $request)
    {
        $shipment_id = $request->shipment_id;
        $shipment_piece_id = $request->piece_id;

        $shipment_piece = ShipmentPiece::where('tracking_number', $shipment_piece_id);
        if ($shipment_piece->exists()) {
            $shipment_piece = $shipment_piece->first();
            if ($shipment_piece->shipment_id == $shipment_id) {
                $scanned_shipment_piece = $shipment_piece->tracking_number;
                ShipmentScanningJourneyController::add($shipment_id, 37, 1, Auth::id(), NULL, NULL, $shipment_piece->id, NULL, session('latitude'), session('longitude'), NULL);
                return ['status' => 0, 'success' => 'Shipment Piece found!', 'scanned_shipment_piece' => $scanned_shipment_piece];
            } else {
                return ['status' => 1, 'error' => 'Given Item ID does not belong here'];
            }
        } else {
            return ['status' => 1, 'error' => 'No Shipment Item with given Item ID is present'];
        }
    }

    public function rider_request_note_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 610);
        return view('admin.pudo.return_transfer_note.rider_request.index');
    }

    public function rider_request_note_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 611);
        }

        $return_note_requests = RiderReturnTransferNoteRequest::join('riders as r', 'r.id', '=', 'rider_return_transfer_note_requests.rider_id')
            ->join('cities as c', 'c.id', '=', 'rider_return_transfer_note_requests.hub_id')
//            ->join('routes as ro', 'ro.id', '=', 'rider_return_transfer_note_requests.route_id')
            ->leftjoin('admins as ad', 'ad.id', '=', 'rider_return_transfer_note_requests.updated_by')
            ->join('rider_types', 'rider_types.id', '=', 'r.rider_type_id')
            ->join('zones as z', 'c.zone_id', '=', 'z.id')
//            ->whereDate('rider_return_transfer_note_requests.created_at', Carbon::today())
            ->where('rider_return_transfer_note_requests.status', 0)
            ->select('rider_return_transfer_note_requests.id as id', 'rider_return_transfer_note_requests.id as request_note_id', 'rider_return_transfer_note_requests.created_at as date', 'r.name as rider_name', 'c.name as hub' ,'rider_return_transfer_note_requests.shipment_count as shipments_count', 'rider_return_transfer_note_requests.shipment_count as shipments_count_link', 'z.name as zone_name', 'r.operation_rider_id', 'r.rider_type_id', 'rider_types.name as rt', 'ad.name as admin_name', 'rider_return_transfer_note_requests.updated_at as updated')
            ->whereBetween('rider_return_transfer_note_requests.created_at', ['2024-01-01 00:00:00', now()->toDateTimeString()])
            ->orderBy('rider_return_transfer_note_requests.id', 'DESC');

        // dd($return_note_requests->get());
        if (session('role_id') != 1) {
            $return_note_requests = $return_note_requests->whereIn('rider_return_transfer_note_requests.hub_id', session('hubs'));
        }

        $datatables = Datatables::of($return_note_requests)
            ->editColumn('amount', function ($return_note_requests) {
                return number_format($return_note_requests->amount);
            })
            ->addColumn('request_note_id_padded', function ($return_note_requests) {
                return str_pad($return_note_requests->request_note_id, 6, '0', STR_PAD_LEFT);
            })
            ->filterColumn('return_transfer_notes.id', function ($query, $keyword) {
                return $query->where('return_transfer_notes.id', '=', $keyword);
            })
            ->editColumn('shipments_count_link', function ($return_note_requests) {
                if ($return_note_requests->shipments_count != 0) {
                    $shipment_count = RiderReturnTransferNoteRequestShipment::where('request_note_id', $return_note_requests->id)
                        ->whereIn('status', [1, 4])->count();
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $shipment_count . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('shipments_count', function ($return_note_requests) {
                if ($return_note_requests->shipments_count != 0) {
                    $shipment_count = RiderReturnTransferNoteRequestShipment::where('request_note_id', $return_note_requests->id)
                        ->whereIn('status', [1, 4])->count();
                    return $shipment_count;
                } else {
                    return 0;
                }
            })
            ->editColumn('rider', function ($return_note_requests) {
                return $return_note_requests->rider_name;
            })
            ->editColumn('route', function ($return_note_requests) {
                return $return_note_requests->code . ' (' . $return_note_requests->start . ' to ' . $return_note_requests->end . ')';
            })
            ->filterColumn('route', function ($query, $keyword) {
                $keyword = strtolower($keyword);
                if ($keyword != '') {
                    $query->where('ro.code', 'like', '%' . $keyword . '%')->orWhere('ro.start', 'like', '%' . $keyword . '%')->orWhere('ro.end', 'like', '%' . $keyword . '%');
                } else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn("action", function ($result) {
                $approveRoute = route('admin.return_transfer_note.rider_request.approve', ['id' => $result->request_note_id]);
                $rejectRoute = route('admin.return_transfer_note.rider_request.reject', ['id' => $result->request_note_id]);
                $editShipmentRoute = route('admin.return_transfer_note.rider_request.update', ['id' => $result->request_note_id]);
                $approveButton = '<a href="' . $approveRoute . '" class="dropdown-item" data-target-id="' . $result->request_note_id . '" class=""><i class="ft-check-circle primary"></i> Approve</a>';
                $rejectButton = '<a href="' . $rejectRoute . '" class="dropdown-item" data-target-id="' . $result->request_note_id . '" class=""><i class="ft-minus-circle primary"></i> Reject</a>';
                $editShipmentButton = '<a href="' . $editShipmentRoute . '" class="dropdown-item returnnoteupdate" data-target-id="' . $result->return_note . '"><i class="ft-edit primary"></i> Edit</a>';
                if (session('role_id') == 1 || count(array_intersect([833, 834, 835], session('permissions'))) !== 0) {
                    $dropdown = '
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm">
                    ';
                    if (session('role_id') == 1 || in_array(833, session('permissions'))) {
                        $dropdown .= $approveButton;
                    }
                    if (session('role_id') == 1 || in_array(834, session('permissions'))) {
                        $dropdown .= $rejectButton;
                    }
                    if (session('role_id') == 1 || in_array(835, session('permissions'))) {
                        $dropdown .= $editShipmentButton;
                    }
                    $dropdown .= '
                        </div>
                      </div>
                    ';

                    return $dropdown;
                } else {
                    return '';
                }
            })->rawColumns(['action','shipments_count_link']);
        return $datatables->make(true);
    }

    public function request_note_shipments(Request $request)
    {
        $request_note_id = $request->input('request_note_id');
        $request_note = RiderReturnTransferNoteRequest::find($request_note_id);
        $shipments = array();
        if ($request_note) {
            $shipments = RiderReturnTransferNoteRequestShipment::join('shipments as s', 's.id', '=', 'rider_return_transfer_note_request_shipments.shipment_id')
                ->where('request_note_id', $request_note->id)->whereIn('rider_return_transfer_note_request_shipments.status', [1, 4])->pluck('s.tracking_number')->toArray();
        }
        if (count($shipments) > 0) {
            return ['status' => 0, 'success' => 'Return Note Shipments', 'shipments' => $shipments];
        } else {
            return ['status' => 0, 'success' => 'No Return Note Shipments', 'shipments' => FALSE];
        }
    }

    public function request_note_approve(Request $request, $id)
    {
        $admin = Auth::id();
        $approved_at =Carbon::today();
        $request_id = $id;
        $return_request = RiderReturnTransferNoteRequest::find($request_id);
        if ($return_request) {
            // $request_shipments = RiderReturnNoteRequestShipment::where('request_note_id', $return_request->id)->whereIn('status', [0, 4]);

            $shipments = RiderReturnTransferNoteRequestShipment::where('request_note_id', $return_request->id)->whereIn('status', [1, 4])->pluck('shipment_id')->toArray();
//            $open_box_ids = RiderReturnTransferNoteRequestShipment::where('request_note_id', $return_request->id)->whereIn('status', [1, 4])->where('open_box', 1)->pluck('shipment_id')->toArray();

            // dd($request_shipments->get(),$return_request->id,$notifications);

            if (count($shipments) == 0) {
                return redirect()->back()->with('error',  'Shipments not entered!');
            }
            $pending_status = array(20, 22, 24, 27, 29, 30, 33, 35, 37, 42, 44, 45, 46, 47, 48, 60,156);
            $valid_shipments = Shipment::whereIn('id', $shipments)->whereIn('shipper_status_id', $pending_status)->pluck('id');
            $shipments_count = count($valid_shipments);
            if ($shipments_count != 0) {
                if ($return_request->status != 2) {

                    $valid_shipments = $valid_shipments->toArray();
                    $invalid_shipments = array_diff($shipments, $valid_shipments);
                    //                Shipment::whereIn('id', $valid_shipments)->update(['shipper_status_id' => 23, 'consignee_status_id' => 23]);
                    // $total_cod_amount = Shipment::whereIn('id', $valid_shipments)->where(function ($query) {
                    //     $query->where('booking_type_id', '!=', 4)
                    //         ->orWhere(function ($sub_query) {
                    //             $sub_query->where('booking_type_id', '=', 4)
                    //                 ->where('charges_mode_id', '=', 2);
                    //         });
                    // })->sum('amount');


                    $return_request->status = 2;
                    $return_request->approved_by = $admin;
                    $return_request->approved_at = Carbon::today();
                    $return_request->save();

                    $order = false;
                    if ($return_request->ordering) {
                        $order = true;
                    }
                    $normal_rider = TRUE;
                    $rider_id = $return_request->rider_id;
                    $rider = Rider::find($rider_id);
                    $hub_id = $return_request->hub_id;
                    $route_id = $return_request->route_id;

                    $note = new ReturnTransferNote();
                    $note->hub_id = $hub_id;
                    $note->rider_id = $rider_id;
                    $note->route_id = $route_id;
                    $note->shipments_count = $shipments_count;
//                    $note->admin_id = $admin;
//                    $note->last_updated_at = Carbon::now();
                    $note->ordering = $order;
                    $note->last_status_updated_by = $admin;
                    $note->last_status_updated_at = $approved_at;
                    $note->created_via_app = 1;
                    $note->request_note_id = $return_request->id;

                    if ($note->save()) {
                        if (!$order) {  //Default
                            sort($valid_shipments); //sort_valid_shipments;
                        }
                        $serial = 1;
                        foreach ($valid_shipments as $shipment) {
                            $returnTransferNoteShipment = new ReturnTransferNoteShipment();
                            $returnTransferNoteShipment->return_note_id = $note->id;
                            $returnTransferNoteShipment->shipment_id = $shipment;
                            $returnTransferNoteShipment->last_status_updated_by = $admin;
                            $returnTransferNoteShipment->last_status_updated_at = $approved_at;
                            $returnTransferNoteShipment->ordering = $serial;
                            $returnTransferNoteShipment->save();

                            $serial++;
                        }

                        foreach ($valid_shipments as $shipment) {
                            $shipment_data = Shipment::find($shipment);
//                            if (in_array($shipment, $open_box_ids)) {
//                                $shipment_detail = ShipmentDetail::where('shipment_id', $shipment)->where('is_open', '=', 0)->first();
//                                if ($shipment_detail) {
//                                    $shipment_detail->is_open = 1;
//                                    $shipment_detail->save();
//                                }
//
//                                $shipment_data->open_box = 1;
//                                $shipment_data->save();
//
//                                ShipmentOpenBoxJourneyController::add($shipment, 3, $admin);
//                            }

                            $old_return_note_id = ReturnTransferNoteShipment::where('shipment_id', $shipment)->where('status', '>', 1)->orderBy('return_note_id', 'desc');

                            if ($old_return_note_id->exists()) {
                                $old_return_note_id = $old_return_note_id->first();

                                if (ReturnTransferNote::where('id', $old_return_note_id->return_note_id)->where('status', 1)->exists()) {
                                    $journey = ShipmentsJourney::where('shipment_id', $shipment)->where('verification', 0)->latest()->first();
                                    if ($journey) {
                                        ShipmentsJourneyController::add($journey->shipment_id, $journey->shipper_status_id, $journey->consignee_status_id, $journey->status_reason_id, $journey->remarks, $journey->user_id, $admin, $journey->reference_1_id, NULL, 1, $journey->received_or_refused_by);
                                    }
                                }
                            }

//                            $shipper_status_id = 23;
//                            $consignee_status_id = 23;
//
//                            if ($shipment_data->booking_type_id == 2) {
//                                $shipper_status_id = 28;
//                                $consignee_status_id = 28;
//
//                                self::update_replacement_weight_and_charges($shipment_data);
//                            }
//
//                            if ($shipment_data->booking_type_id == 3) {
//                                $shipper_status_id = 34;
//                                $consignee_status_id = 34;
//                            }
                            $shipper_status_id = 155;
                            $consignee_status_id = 155;
                            $shipment_data->shipper_status_id = $shipper_status_id;
                            $shipment_data->consignee_status_id = $consignee_status_id;
                            $shipment_data->save();

                            ShipmentsJourneyController::add($shipment, $shipper_status_id, $consignee_status_id, NULL, NULL, NULL, $admin, $note->id, $note->rider_id);

//                            $handover_shipments = HandoverShipments::where('shipment_id', $shipment)->whereIn('status', [1, 3]);
//                            if ($handover_shipments->exists()) {
//                                $handover_shipments = $handover_shipments->first();
//                                $handover_shipments->status = 2;
//                                $handover_shipments->save();
//                                $handover_count = HandoverShipments::where('status', 1)->where('handover_id', $handover_shipments->handover_id)->count();
//                                if ($handover_count == 0) {
//                                    $handover = Handover::find($handover_shipments->handover_id);
//                                    $handover->received_by = $admin;
//                                    $handover->received_at = Carbon::now();
//                                    $handover->received = $handover->received + 1;
//                                    $handover->status_id = 4;
//                                    $handover->save();
//                                }
//                                HandoverShipmentJourneyController::add($shipment, $handover_shipments->handover_id, 2);
//                            }
                        }

                        $process_one_link['shipment_ids'] = $valid_shipments;
                        $process_one_link['delivery_note_id'] = $note->id;
                        //                        dispatch(new ProcessOneLinkDeliveryNoteShipment($process_one_link));
                    }
//                    NotificationsController::send(40, $note->id);
//                    if ($normal_rider) {
//                        NotificationsController::app_notification(5, $rider_id, 2, $note->id);
//                    }
//
//                    //rider attendance
//                    if ($rider->operation_rider_id == 1 && $rider->employee_id != null) {
//                        EmployeeAttendanceController::riders_attendance_mark($rider_id);
//                    }
                    //rider attendance end

                    //todo : update status 1 to 2 (take wo next time jbtk na aae jbtk rider cat ki request dubara na daljae)
//                    $rider_bypass_type = RiderCategoryByPass::where('rider_id', $rider_id)->where('status', 1)->select('rider_category_id', 'id')->latest()->first();
//                    if ($rider_bypass_type) {
//                        $rider_bypass_id = $rider_bypass_type->id;
//                        RiderCategoryByPass::where('rider_id', $rider_id)->where('id', $rider_bypass_id)->update(["status" => 2]);
//                    }
                    //todo end
                    $return_request->status = 2;
                    $return_request->approved_by = $admin;
                    $return_request->approved_at = Carbon::today();
                    $return_request->save();
//                    RiderReturnNoteRequestShipment::where('request_note_id', $return_request->id)->whereIn('shipment_id', $valid_shipments)->update(['status' => 1]);
                    RiderReturnTransferNoteRequestShipment::where('request_note_id', $return_request->id)->whereIn('shipment_id', $invalid_shipments)->update(['status' => 3]);
                    return redirect()->back()->with('success', 'Return note has been Approved successfully' . PHP_EOL . 'Return Note ID: ' . $note->id);
                } else {
                    return redirect()->back()->with('error', 'Return Note Already Approved!');
                }
            } else {
                $return_request->status = 3;
                $return_request->updated_by = $admin;
                $return_request->save();
                RiderReturnTransferNoteRequestShipment::where('request_note_id', $return_request->id)->update(['status' => 3]);
                return redirect()->back()->with('error', 'All the Shipment(s) are not ready for return yet or already in another return note, please check tracking!');
            }
        } else {
            return redirect()->back()->with('error', 'Invalid Request ID');
        }
    }

    public function request_note_reject(Request $request, $id)
    {
        $return_request = RiderReturnTransferNoteRequest::find($id);
        if ($return_request) {
            $return_request->status = 3;
            $return_request->updated_by = Auth::id();
            $return_request->save();
            RiderReturnTransferNoteRequestShipment::where('request_note_id', $return_request->id)
                ->update(['status' => 2]);
            return redirect()->back()->with('success', 'Return Note Creation Request is Rejected Successfully');
        }
        return redirect()->back()->with('error', 'Invalid Request ID');
    }

    public function request_note_update(Request $request, $id)
    {
        $service_type = BookingType::all();
        return view('admin.pudo.return_transfer_note.rider_request.update')->with(['request_note_id' => $id, 'service_type' => $service_type]);
    }

    public function request_note_update_list(Request $request, $id)
    {
        $returns = RiderReturnTransferNoteRequest::join('rider_return_transfer_note_request_shipments as dns', 'dns.request_note_id', '=', 'rider_return_transfer_note_requests.id')
            ->join('shipments', 'shipments.id', '=', 'dns.shipment_id')
            ->join('cities AS oc', 'shipments.consignee_city_id', '=', 'oc.id')
            ->leftJoin('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->select(['rider_return_transfer_note_requests.id as return_note', 'shipments.tracking_number', 'shipments.id as shId', 'oc.name as destination', 'shipments.consignee_name', 'shipments.consignee_phone_number_1 as phone', 'shipments.consignee_address as address', 'shipments.amount as amount', 'bt.booking_type as service_type', 'shipments.payment_mode_id as payment_mode_id'])
            ->where('rider_return_transfer_note_requests.id', $id)
            ->whereIn('dns.status', [1, 4]);

        if (session('role_id') != 1) {
            $returns = $returns->whereIn('rider_return_transfer_note_requests.hub_id', session('hubs'));
        }

        return Datatables::of($returns)
            ->addColumn("action", function ($returns) {
                if ($returns->payment_mode_id != 2) {
                    return "<a href='javascript:void(0);' class='requestnoterow'><button type='button' class='btn btn-sm btn-danger'>Remove</button></a>";
                }
            })
            ->editColumn('amount', function ($shipment) {
                return number_format($shipment->amount);
            })
            ->filterColumn('service_type', function ($query, $keyword) {

                if ($keyword != '') {
                    $query->where('bt.id', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->make(true);
    }

    public function request_note_remove(Request $request)
    {
        $shipment = RiderReturnTransferNoteRequestShipment::join('shipments as s', 's.id', '=', 'rider_return_transfer_note_request_shipments.shipment_id')->where('rider_return_transfer_note_request_shipments.shipment_id', $request->shipment_id)->where('rider_return_transfer_note_request_shipments.request_note_id', $request->request_note_id)
            ->select('rider_return_transfer_note_request_shipments.*', 's.amount as cod');
        if ($shipment->exists()) {
            $shipment = $shipment->first();
            $request_note = $request->request_note_id;
            $return = RiderReturnTransferNoteRequest::find($request_note);
            if ($return) {
                if ($shipment->status == 1) {
                    $shipment->status = 5;
                } else {
                    RiderReturnTransferNoteRequestShipment::where('shipment_id', $request->shipment_id)->where('request_note_id', $request->request_note_id)->delete();
                }
                // $return->total_cod_amount = $return->total_cod_amount - $shipment->cod;
                $return->shipment_count = $return->shipment_count - 1;
                $return->updated_by = Auth::id();
                $shipment->save();
                $return->save();
                return ['status' => 0, 'success' => 'Shipment is successfully removed'];
            } else {
                return ['status' => 1, 'error' => 'Something went wrong'];
            }
        } else {
            return ['status' => 1, 'error' => 'Something went wrong'];
        }
    }

    public function request_note_remove_bulk(Request $request)
    {
        $shipments = $request->shipment_ids;
        $request_note_id = $request->request_note_id;
        if ($request_note_id) {
            foreach ($shipments as $shipment_id) {
                $shipment = RiderReturnTransferNoteRequestShipment::join('shipments as s', 's.id', '=', 'rider_return_transfer_note_request_shipments.shipment_id')->where('rider_return_transfer_note_request_shipments.shipment_id', $shipment_id)->where('rider_return_transfer_note_request_shipments.request_note_id', $request->request_note_id)
                    ->select('rider_return_transfer_note_request_shipments.*', 's.amount as cod');
                if ($shipment->exists()) {
                    $shipment = $shipment->first();
                    $request_note = $request->request_note_id;
                    $return = RiderReturnTransferNoteRequest::find($request_note);
                    if ($return) {
                        if ($shipment->status == 1) {
                            $shipment->status = 5;
                            $shipment->save();
                        } else {
                            RiderReturnTransferNoteRequestShipment::where('shipment_id', $request->shipment_id)->where('request_note_id', $request->request_note_id)->delete();
                        }
                        // $return->total_cod_amount = $return->total_cod_amount - $shipment->cod;
                        $return->shipment_count = $return->shipment_count - 1;
                        $return->updated_by = Auth::id();
                        $return->save();
                    }
                }
            }
            return ['status' => 0, 'success' => 'Shipments are successfully removed'];
        } else {
            return ['status' => 1, 'error' => 'Something went wrong'];
        }
    }

    public function add_shipments_in_request_note(Request $request)
    {
        $shipment_id = $request->shipment_id;
        $request_note_id = $request->request_note_id;
        $shipment = Shipment::find($shipment_id);
        if ($shipment) {
            $request_note = RiderReturnTransferNoteRequest::find($request_note_id);
            if ($request_note) {
                if ($request_note->status != 1) {
                    return response()->json(['status' => 1, 'error' => 'Request note is already approved']);
                }
                $rider = $request_note->rider;
                if ($shipment->payment_mode_id == 2 && $rider->ccd == 0) {
                    return response()->json(['status' => 1, 'error' => 'The selected Shipment is Credit Card on Return shipment and rider is not allowed/trained to use POS for CCD shipments']);
                }

                $request_shipment = RiderReturnTransferNoteRequestShipment::where('request_note_id', $request_note_id)->where('shipment_id', $shipment->id);
                if ($request_shipment->exists()) {
                    $request_shipment = $request_shipment->first();
                    if (in_array($request_shipment->status, [1, 4])) {
                        return response()->json(['status' => 1, 'error' => 'Shipment is already in this return note!']);
                    } else if ($request_shipment->status == 5) {
                        $request_shipment->status = 4;
                        // $request_note->total_cod_amount = $request_note->total_cod_amount + $shipment->amount;
                        $request_note->shipment_count = $request_note->shipment_count + 1;
                        $request_note->updated_by = Auth::id();
                        $request_shipment->save();
                        $request_note->save();
                        return response()->json(['status' => 0, 'success' => 'Shipment Added']);
                    }
                } else {
                    $serial = RiderReturnNoteRequestShipment::select('ordering')->where('request_note_id', $request_note->id)->orderBy('ordering', 'desc')->first();

                    $riderReturnNoteRequestShipment = new RiderReturnNoteRequestShipment();
                    $riderReturnNoteRequestShipment->request_note_id = $request_note->id;
                    $riderReturnNoteRequestShipment->shipment_id = $shipment->id;
                    $riderReturnNoteRequestShipment->open_box = 0;
                    $riderReturnNoteRequestShipment->status = 4;
                    $riderReturnNoteRequestShipment->ordering = $serial->ordering + 1;
                    $riderReturnNoteRequestShipment->save();

                    // $request_note->total_cod_amount = $request_note->total_cod_amount + $shipment->amount;
                    $request_note->shipment_count = $request_note->shipment_count + 1;
                    $request_note->updated_by = Auth::id();
                    $request_note->save();
                    return response()->json(['status' => 0, 'success' => 'Shipment Added']);
                }
            }
        } else {
            return response()->json(['status' => 1, 'error' => 'Shipments Not Found']);
        }
    }


    public function return_receive_shipments_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 308);
        return view('admin.pudo.return_transfer_note.receive.return_shipments');
    }
    public function return_receive_shipments_list(Request $request)
    {
        $isExcel = $request->get('excel') && $request->get('excel') == true;

        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 309);
        }

        // ---- 6 months window ----
        $from = Carbon::now()->subMonths(6)->startOfDay();
        $to   = Carbon::now()->endOfDay();

        $connection = 'reports'; // <- set the correct connection name you use for shipments_journey

        // First and last shipments_journey IDs within the window
        $sj_from_id = DB::connection($connection)
            ->table('shipments_journey')
            ->whereBetween('created_at', [$from, $to])
            ->orderBy('created_at', 'asc')->orderBy('id', 'asc')
            ->limit(1)->value('id');

        $sj_to_id = DB::connection($connection)
            ->table('shipments_journey')
            ->whereBetween('created_at', [$from, $to])
            ->orderBy('created_at', 'desc')->orderBy('id', 'desc')
            ->limit(1)->value('id');



        $deliveries = ReturnTransferNote::leftjoin('cities AS oc', 'return_transfer_notes.hub_id', '=', 'oc.id')
            ->leftjoin('riders', 'return_transfer_notes.rider_id', '=', 'riders.id')
            ->leftjoin('city_areas as ca', 'ca.id', '=', 'riders.area_id')
            ->leftjoin('admins', 'admins.id', '=', 'return_transfer_notes.admin_id')
            ->leftjoin('return_transfer_note_shipments as rns', 'return_transfer_notes.id', '=', 'rns.return_note_id')
            ->leftjoin('shipments as s', 'rns.shipment_id', '=', 's.id')
            ->leftJoin('shipments_journey as sj', function ($join) use ($sj_from_id, $sj_to_id) {
                $join->on('sj.reference_1_id', '=', 'return_transfer_notes.id')
                    ->whereIn('sj.shipper_status_id', [25,31,38])
                    ->where('sj.verification', 1)
                    ->whereBetween('sj.id', [$sj_from_id, $sj_to_id]);
            })
            ->leftjoin('users as uu', 'uu.id', '=', 's.user_id') // join users for excel segments
            ->select([
                'return_transfer_notes.id as return_note',
                'return_transfer_notes.id',
                'return_transfer_notes.id as return_note_id',
                'oc.name as hub',
                'riders.name as rider',
                'admins.name as assignee',
                'return_transfer_notes.created_at',
                'return_transfer_notes.shipments_count',
                'return_transfer_notes.shipments_count as shipments_count_link',
                'return_transfer_notes.status',
                'riders.trax_id as rider_trax_id',
                DB::raw('(SELECT COUNT(shipment_id) FROM return_transfer_note_shipments WHERE return_note_id = return_transfer_notes.id AND status = 0) AS shipments_unverified_count'),
                DB::raw('COUNT(sj.id) as delivered_to_shipper_count'),
                'ca.name as area',
                'return_transfer_notes.created_at as created'
            ])
            ->where('return_transfer_notes.created_at', '>=', Carbon::now()->subMonths(6))
            ->whereIn('return_transfer_notes.status', [1, 4])
            ->groupBy('return_transfer_notes.id');


        if ($isExcel) {
            $deliveries->addSelect([
                DB::raw("SUM(CASE WHEN uu.segment_id = 2 AND uu.sub_segment_id = 5 THEN 1 ELSE 0 END) as excel_ecom_cod"),
                DB::raw("SUM(CASE WHEN uu.segment_id = 1 AND uu.sub_segment_id = 12 THEN 1 ELSE 0 END) as excel_general_retail"),
                DB::raw("
                SUM(CASE WHEN (uu.segment_id = 1 AND uu.sub_segment_id IN (1,2)) 
                      OR (uu.segment_id = 2 AND uu.sub_segment_id = 7) 
                    THEN 1 ELSE 0 END
                ) as excel_general_ecom_express
            "),
                DB::raw("SUM(CASE WHEN uu.segment_id IN (1,2) AND uu.sub_segment_id IN (1,3,4,6,8,9,10,11) THEN 1 ELSE 0 END) as excel_others"),
            ]);
        }

        if (session('role_id') != 1) {
            $deliveries = $deliveries->whereIn('oc.hub_id', session('hubs'));
        }
        if ($tracking_number = $request->get('search_tracking')) {
            $deliveries->where('s.tracking_number', '=', $tracking_number);
        }
        if ($return_note_number = $request->get('return_note_number')) {
            $deliveries->where('return_transfer_notes.id', '=', $return_note_number);
        }

        $datatables = Datatables::of($deliveries)
            ->editColumn('return_note', function ($deliveries) {
                return "<a href='javascript:void(0);' class='printreturnnote'><u>" . str_pad($deliveries->return_note, 6, '0', STR_PAD_LEFT) . "</u></a>";
            })
            ->addColumn('return_note_id_padded', function ($deliveries) {
                return str_pad($deliveries->return_note_id, 6, '0', STR_PAD_LEFT);
            })
            ->addColumn('return_note_id_padded', function ($deliveries) {
                return str_pad($deliveries->return_note_id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('shipments_count_link', function ($deliveries) {
                if ($deliveries->shipments_count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $deliveries->shipments_count . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('shipments_unverified_link', function ($deliveries) {
                if ($deliveries->shipments_unverified_count != 0) {
                    return  $deliveries->shipments_unverified_count;
                } else {
                    return 0;
                }
            })
            ->filterColumn('return_transfer_notes.id', function ($query, $keyword) {
                return $query->where('return_transfer_notes.id', '=', $keyword);
            })
            ->addColumn('return_note_status', function ($deliveries) {

                if ($deliveries->status == 1) {
                    return 'Pending';
                } else if ($deliveries->status == 4) {
                    return 'Pending for completion';
                }
            })
            ->editColumn('excel_ecom_cod', fn($r) => $isExcel ? ($r->excel_ecom_cod ?? '-') : '-')
            ->editColumn('excel_general_retail', fn($r) => $isExcel ? ($r->excel_general_retail ?? '-') : '-')
            ->editColumn('excel_general_ecom_express', fn($r) => $isExcel ? ($r->excel_general_ecom_express ?? '-') : '-')
            ->editColumn('excel_others', fn($r) => $isExcel ? ($r->excel_others ?? '-') : '-')
            ->addColumn("action", function ($result) {
                $statusUpdate = route('admin.return_transfer_note.receive.status', ['id' => $result->return_note]);
//                $route = route('admin.return.receive.update', ['id' => $result->return_note]);

                $receive_button = '<a href="' . $statusUpdate . '" class="dropdown-item"><i class="ft-plus-circle primary"></i> Receive</a>';
//                $shift_shipment_button = '<a href="' . $route . '" class="dropdown-item returnnoteupdate"><i class="ft-plus-circle primary"></i> Edit Shipment</a>';
//                $return_image_upload = '<a href="javascript:void(0);" class="dropdown-item return_image_upload"><i class="ft-image primary"></i> Image Upload</a>';

                if (session('role_id') == 1 || count(array_intersect([50, 51], session('permissions'))) !== 0) {
                    $dropdown = "
                    <div class='btn-group'>
                           <button type='button' class='btn btn-sm btn-success dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Actions</button>
                            <div class='dropdown-menu dropdown-menu-sm'>";

                    if (session('role_id') == 1 || in_array(50, session('permissions'))) {
                        $dropdown .= $receive_button;
                    }

//                    if (($result->created_at->diffInMinutes(Carbon::now()) <= 60) && (session('role_id') == 1 || in_array(51, session('permissions')))) {
//                        $dropdown .= $shift_shipment_button;
//                    }

//                    if ($result->status == 3 && $result->delivered_to_shipper_count != 0) {
//                        $dropdown .= $return_image_upload;
//                    }

                    $dropdown .= "
                        </div>
                    </div>
                ";

                    return $dropdown;
                } else {
                    return '';
                }
            })->rawColumns(['action','shipments_count_link','return_note']);



        return $datatables->make(true);
    }

    public function receive_shipment_list(Request $request)
    {
        $return_note_id = $request->input('return_note_id');
        $return_note_details = ReturnTransferNote::find($return_note_id);
        $return_note_shipments = $return_note_details->return_note_shipments;
        $shipments = array();
        if ($return_note_shipments->count() != 0) {
            foreach ($return_note_shipments as $return_note_shipment) {
                $shipment = Shipment::find($return_note_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Return Note Shipments', 'shipments' => $shipments];
        } else {
            return ['status' => 0, 'success' => 'No Return Note Shipments', 'shipments' => FALSE];
        }
    }

    public function receive_shipment_status()
    {

    }


}
