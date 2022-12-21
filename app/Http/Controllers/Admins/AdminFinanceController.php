<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Controllers\ShipmentScanningJourneyController;
use App\Http\Models\Admin\AdjustmentLog;
use App\Http\Models\Admin\AdjustmentType;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\ChangeShipmentAmountLog;
use App\Http\Models\Admin\ChangeShipmentWeightLog;
use App\Http\Models\Admin\CorporateUserPackagingInvoice;
use App\Http\Models\Admin\InvoiceAdjustment;
use App\Http\Models\Admin\InvoiceAdjustmentReasons;
use App\Http\Models\Admin\Retail\RetailShipment;
use App\Http\Models\Admin\Retail\RetailShipperInfo;
use App\Http\Models\Admin\ResolvedOutstandingShipment;
use App\Http\Models\Admin\RevertStatusRequest;
use App\Http\Models\Admin\StationDepositNoteAdjustment;
use App\Http\Models\Admin\StationDepositNoteSlip;
use App\Http\Models\Admin\VisionSoft\VisionSoftCodPaymentClear;
use App\Http\Models\ChargesModes;
use App\Http\Models\ConsolidationShipments;
use App\Http\Models\CorporateDiscountCharge;
use App\Http\Models\CorporateMinChargeableWeight;
use App\Http\Models\CorporateRateStatus;
use App\Http\Models\CorporateWeightCharge;
use App\Http\Models\CorporateWeightChargeZoneWise;
use App\Http\Models\CRM\CrmRequestChannel;
use App\Http\Models\DiscountCharge;
use App\Http\Models\DonePaymentCalculation;
use App\Http\Models\InternationalDhlZone;
use App\Http\Models\InternationalShipment;
use App\Http\Models\InternationalUserRate;
use App\Http\Models\InternationalUsersCreditLimit;
use App\Http\Models\InvoiceUploadSlip;
use App\Http\Models\PackagingMaterialRequest;
use App\Http\Models\PackagingMaterialRequestDetail;
use App\Http\Models\PackagingMaterialRequestHistory;
use App\Http\Models\PendingPaymentCalculation;
use App\Http\Models\PickupAddressIbanMapping;
use App\Http\Models\Rates\Corporate\CorporateReimbursementSetting;
use App\Http\Models\RateStatus;
use App\Http\Models\ReimbursementInvoiceShipment;
use App\Http\Models\RetailAdjustmentLog;
use App\Http\Models\RetailDonePayment;
use App\Http\Models\RetailDonePaymentCalculation;
use App\Http\Models\RetailDonePaymentShipment;
use App\Http\Models\RetailPendingPayment;
use App\Http\Models\RetailPendingPaymentCalculation;
use App\Http\Models\RetailPendingPaymentShipment;
use App\Http\Models\RevertStatusRequestLog;
use App\Http\Models\Rider;
use App\Http\Models\ShipmentItem;
use App\Http\Models\ShipmentsPaymentJourney;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\ShippingMode;
use App\Http\Models\Warehouse\Warehouse;
use App\Http\Models\WeightCharge;
use App\Http\Models\ZoneClassCity;
use DateTime;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Controllers\ShipmentsPaymentJourneyController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Models\Admin\PettyCashStatement;

use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Notification;
use App\Http\Models\BanksList;
use App\Http\Models\City;
use App\Http\Models\Zone;
use App\Http\Models\BookingType;
use App\Http\Models\Admin\StationDepositNote;
use App\Http\Models\Admin\DeliveryNoteStationDepositNote;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\Admin\LostShipmentAdmin;
use App\Http\Models\Admin\LostShipmentShipper;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserBankInfo;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\PendingPayment;
use App\Http\Models\PendingPaymentShipment;
use App\Http\Models\DonePayment;
use App\Http\Models\DonePaymentShipment;
use App\Http\Models\PendingInvoiceShipment;
use App\Http\Models\Invoice;
use App\Http\Models\InvoiceShipment;
use App\Http\Models\InvoiceStatus;
use App\Http\Models\Sister_account\MergedSisterAccount;
use App\Http\Models\InvoiceForReimbursement;
use SnappyImage;
use SnappyPDF;
use Auth;
use DB;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use NumberToWords\NumberToWords;
use App\Http\Models\Admin\WalkinFtlInvoice;

class AdminFinanceController extends Controller
{
    static private function amount_to_words($amount)
    {

        $number_to_words = new NumberToWords();
        $number_transformer = $number_to_words->getNumberTransformer('en');

        $amount_in_words = $number_transformer->toWords($amount, 'PKR');

        $last_position = strrpos($amount_in_words, ' ');

        if ($last_position !== FALSE) {
            $amount_in_words = substr_replace($amount_in_words, ' & ', $last_position, strlen(' '));
        }

        $amount_in_words = str_replace('-', ' ', $amount_in_words);

        $amount_in_words = ucwords($amount_in_words);

        return $amount_in_words;
    }

    static private function international_gst()
    {
        $gst_charges = GlobalSettings::where('type', 'international_gst_rate');
        if ($gst_charges->exists()) {
            $gst_charges = $gst_charges->first();
            return $gst_charges->setting_value / 100;
        } else {
            return 0.13;
        }
    }

    static private function gst($zone_id)
    {
        $zone = Zone::find($zone_id);

        if ($zone) {
            return $zone->gst;
        } else {
            return 0.13;
        }
    }

    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function outstanding_sdn_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 36);
        $banks = BanksList::where('affiliate', 1)->get();
        $all_banks = BanksList::all();
        $hubs = City::orderBy('name')->where('hub', 1)->get();
        $petty_cash_ids = array();
        $petty_cash_ids = StationDepositNote::where('petty_cash_statement_id', '!=', null)->pluck('petty_cash_statement_id')->toArray();
        $petty_cash_list = PettyCashStatement::whereIn('status', [0, 1, 2, 7])->whereNotIn('id', $petty_cash_ids)->select('id')->get();
        return view('admin.finance.outstanding_sdn')->with(['banks' => $banks, 'hubs' => $hubs, 'all_banks' => $all_banks, 'petty_cash_list' => $petty_cash_list]);
    }

    public function outstanding_sdn_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 96);
        }

        $station_deposit_notes = StationDepositNote::join('cities as h', 'station_deposit_notes.hub_id', '=', 'h.id')
            ->join('admins as a', 'station_deposit_notes.deposited_by', '=', 'a.id')
            ->select('station_deposit_notes.id', 'station_deposit_notes.id as sdn_number', 'h.name as hub', 'station_deposit_notes.dncc_count', 'station_deposit_notes.dncc_count as dncc_count_link', 'station_deposit_notes.sdn_delivered_shipments', 'station_deposit_notes.sdn_delivered_shipments as delivered_shipments_link', 'station_deposit_notes.sdn_amount', 'a.name as deposited_by', 'station_deposit_notes.created_at as deposited_at', 'station_deposit_notes.deposit_slip', 'station_deposit_notes.deposit_slip_status', 'station_deposit_notes.sdn_deposit_amount', 'station_deposit_notes.adjustment_amount', 'station_deposit_notes.adjustment_date', 'station_deposit_notes.adjustment_ref')
            ->where('station_deposit_notes.status', 1)->where('sdn_type', 1);

        if (session('role_id') != 1) {
            $station_deposit_notes = $station_deposit_notes->whereIn('h.id', session('hubs'));
        }

        $datatables = Datatables::of($station_deposit_notes)
            ->editColumn('sdn_number', function ($station_deposit_note) {
                return '<button class="btn btn-sm btn-outline-info align-middle"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($station_deposit_note->sdn_number, 6, '0', STR_PAD_LEFT) . '</span></button>';
            })
            ->filterColumn('station_deposit_notes.id', function ($query, $keyword) {
                return $query->where('station_deposit_notes.id', '=', $keyword);
            })
            ->editColumn('dncc_count_link', function ($station_deposit_note) {
                if ($station_deposit_note->dncc_count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $station_deposit_note->dncc_count . '</button>';
                } else {
                    return 0;
                }
            })
            ->addColumn('adjusted_reference_link', function ($sdn) {
                if ($sdn->adjustment_ref != null) {
                    return $sdn->adjustment_ref;
                } else {
                    $adjustment_count = StationDepositNoteAdjustment::where('sdn_id', $sdn->sdn_number)->count();
                    if ($adjustment_count > 0) {
                        return '<button class="btn btn-sm btn-outline-info align-middle">' . $adjustment_count . '</button>';
                    } else {
                        return 0;
                    }
                }
            })
            ->addColumn('adjusted_reference_count', function ($sdn) {
                if ($sdn->adjustment_ref != null) {
                    return $sdn->adjustment_ref;
                } else {
                    $adjustment_count = StationDepositNoteAdjustment::where('sdn_id', $sdn->sdn_number)->count();
                    return $adjustment_count;
                }
            })
            ->editColumn('sdn_amount', function ($shipment) {
                return number_format($shipment->sdn_amount);
            })
            ->editColumn('sdn_deposit_amount', function ($shipment) {
                return number_format($shipment->sdn_deposit_amount);
            })
            ->addColumn('sdn_adjustment_amount', function ($shipment) {
                if ($shipment->adjustment_amount) {
                    return number_format($shipment->adjustment_amount);
                } else {
                    return '-';
                }
            })
            ->editColumn('delivered_shipments_link', function ($station_deposit_note) {
                if ($station_deposit_note->sdn_delivered_shipments != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $station_deposit_note->sdn_delivered_shipments . '</button>';
                } else {
                    return 0;
                }
            })
            ->addColumn('sdn_number_padded', function ($station_deposit_note) {
                return str_pad($station_deposit_note->sdn_number, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('deposit_slip', function ($station_deposit_note) {
                if ($station_deposit_note->deposit_slip == null && $station_deposit_note->deposit_slip_status == 1) {
                    return '<a class="btn btn-sm btn-outline-info align-middle deposit_slip_view" href="#"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';

                } else if ($station_deposit_note->deposit_slip != null) {
                    $now = Carbon::now();
                    if ($now->diffInDays($station_deposit_note->created_at) > 1) {
                        return '<a class="btn btn-sm btn-outline-info align-middle" href="' . asset('uploads/sdn/' . $station_deposit_note->deposit_slip) . '" target="_blank"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';

                    } else {
                        $img = Storage::disk('s3')->temporaryUrl('station_deposit_notes/' . $station_deposit_note->deposit_slip, now()->addMinutes(5));
                        return '<a class="btn btn-sm btn-outline-info align-middle" href="' . $img . '" target="_blank"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';
                    }
                } else {
                    return '-';
                }

            })
            ->filterColumn('bank', function ($query, $keyword) {

                if ($keyword != '') {
                    $query->where('b.id', '=', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn('action', function ($station_deposit_note) {
                $reconcile_delivery_notes_button = '<button type="button" class="dropdown-item reconcile_delivery_notes"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-list"></i></div><div class="col-9 offset-1">Reconcile Delivery Notes</div></button>';
                $edit_deposit_button = '<button type="button" class="dropdown-item edit_deposit_slip"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit Deposit Slip</div></button>';
                $export_to_excel_button = '<button type="button" class="dropdown-item export_to_excel"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-download"></i></div><div class="col-9 offset-1">Export to Excel</div></button>';
                $sdn_adjustment_add_button = '<button type="button" class="dropdown-item adjustment_add"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-alert-octagon"></i></div><div class="col-9 offset-1">Add SDN Adjustment</div></button>';
                $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

                if (session('role_id') == 1 || in_array(53, session('permissions'))) {
                    $dropdown .= $reconcile_delivery_notes_button;
                }

                $delivery_note_shipments = DeliveryNoteStationDepositNote::leftjoin('delivery_note_shipments as dns', 'dns.delivery_note_id', '=', 'delivery_note_station_deposit_notes.delivery_note_id')->where('delivery_note_station_deposit_notes.station_deposit_note_id', $station_deposit_note->id)->where('dns.status', '!=', 7)->get();
                if (count($delivery_note_shipments) > 0 && (session('role_id') == 1 || in_array(677, session('permissions')))) {
                    $dropdown .= $edit_deposit_button;
                }
                if (session('role_id') == 1 || in_array(251, session('permissions'))) {
                    $dropdown .= $sdn_adjustment_add_button;
                }


                $dropdown .= $export_to_excel_button;

                $dropdown .= '
                </div>
              </div>
            ';

                return $dropdown;
            });

        return $datatables->make(true);
    }

    //sdn_list
    public function outstanding_sdn_dncc(Request $request)
    {
        $sdn_id = $request->input('sdn_id');
        $sdn_details = StationDepositNote::find($sdn_id);
        $dn_list = $sdn_details->delivery_notes_list;
        if ($dn_list->count() != 0) {
            $delivery_notes = array();
            foreach ($dn_list as $notes) {
                $delivery_notes[] = $notes->delivery_note_id;
            }
            return ['status' => 0, 'success' => 'Booked Shipments', 'delivery_notes' => $delivery_notes];
        } else {
            return ['status' => 0, 'success' => 'No Booked Shipments', 'delivery_notes' => FALSE];
        }
    }

    public function outstanding_sdn_shipments_delivered(Request $request)
    {
        $sdn_id = $request->input('sdn_id');
        $sdn_details = StationDepositNote::find($sdn_id);
        $dn_list = $sdn_details->delivery_notes_list;
        if ($dn_list->count() != 0) {
            $shipments = array();
            foreach ($dn_list as $note) {
                $shipment_ids = DeliveryNoteShipment::where('delivery_note_id', $note->delivery_note_id)->where('status', '>', 1)->select('shipment_id')->get();
                foreach ($shipment_ids as $id) {
                    $shipments[$note->delivery_note_id][] = Shipment::find($id)->pluck('tracking_number');
                }
            }
            return ['status' => 0, 'success' => 'Delivered Shipments', 'shipments' => $shipments];
        } else {
            return ['status' => 0, 'success' => 'No Delivered Shipments', 'shipments' => FALSE];

        }

    }

    //dncc print
    public function outstanding_sdn_dncc_print(Request $request)
    {

        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Delivery Note Cash Collection</title>

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
                      
                      .w-150 {
                        width: 150px;
                      }
                      
                      .w-200 {
                        width: 200px;
                      }

                      .line {
                        border-bottom: 1px solid #09262e !important;
                      }

                      .manual_form {
                        page-break-inside: avoid;
                      }
                    </style>
                  </head>
                  <body>
                    <div>
      ';
        $delivery_note = DeliveryNote::where('id', $request->id);
        if ($delivery_note->exists()) {
            $delivery_note_data = $delivery_note->first();
            $delivery_note_data->last_updated_at = Carbon::now();
            $delivery_note_data->save();
            $total_shipments = 0;
            $total_cod_amount = 0;
            $dncc_status = array(14, 16, 30, 36, 37);
            $shipment_ids = DeliveryNoteShipment::where('delivery_note_id', $request->id)->where('status', '>', 1)->where('status', '!=', 8)->select('shipment_id')->get();
            $filtered_shipments = Shipment::whereIn('id', $shipment_ids)->whereIn('shipper_status_id', $dncc_status)->orderBy('id')->get();
            //echo "<pre>";print_r($filtered_shipments);echo "</pre>";die();
            $shipment_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="color primary"><strong>S. No.</strong></td>
                            <td class="color primary"><strong>Tracking No.</strong></td>
                            <td class="color primary"><strong>Consignee Name & Phone No(s).</strong></td>
                            <td class="color primary"><strong>Consignee Address</strong></td>
                            <td class="color primary"><strong>Service Type</strong></td>
                            <td class="color primary"><strong>Client Name & Phone</strong></td>
                            <td class="color primary"><strong>Weight</strong></td>
                            <td class="color primary"><strong>Collection Amount</strong></td>
                          </tr>
        ';


            foreach ($filtered_shipments as $shipment) {
                $total_shipments++;
//                    $shipment = Shipment::find($parcel->shipment_id);

                $shipment_details_row_start = '
                          <tr>
                            <td>' . $total_shipments . '</td>
                            <td>' . $shipment->tracking_number . '</td>
                            <td>' . $shipment->consignee_name . ' | ' . $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') . '</td>
                            <td>' . $shipment->consignee_address . '</td>
                            <td>' . $shipment->booking_type->booking_type . '</td>
                            <td>' . $shipment->user->name . ' | ' . $shipment->user->phone . (($shipment->phone2) ? (' / ' . $shipment->phone2) : '') . '</td>
                            <td>' . (($shipment->booking_type_id == 2) ? $shipment->replacement_weight : $shipment->actual_weight) . '</td>
                            <td>Rs ' . number_format($shipment->received_amount) . '</td>
                            
                          </tr>
            ';
                $total_cod_amount += $shipment->received_amount;
                $shipment_details .= $shipment_details_row_start;
            }
            $shipment_details .= '
                        </tbody>
                      </table>
        ';
            $delivery_note_details = DeliveryNote::where('id', $request->id)->first();
            $rider = Rider::where('id', $delivery_note_details->rider_id)->first();
            $city_name = $delivery_note_details->hub->name;
            $rider_name = $rider->name;
            $category = $rider->rider_category->name;
            $route_name = $delivery_note_details->route->code . ' (' . $delivery_note_details->route->start . ' to ' . $delivery_note_details->route->end . ')';
            $main_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="text-center align-middle"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto"></td>';
            if ($request->has('temporary') && ($request->temporary != null)) {
                $main_details .= '<td class="text-center align-middle color primary"><strong>Temporary Cash Collection</strong></td>';
            } else {
                $main_details .= '<td class="text-center align-middle color primary"><strong>Delivery Note Cash Collection</strong></td>';
            }


            $main_details .= '<td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Delivery Note No.</strong></td>
                            <td>' . str_pad($delivery_note_details->id, 6, '0', STR_PAD_LEFT) . '</td>
                          </tr>
                          <tr>
                          <tr>
                            <td class="color secondary"><strong>Rider Name</strong></td>
                            <td>' . $rider_name . '</td>
                            <td rowspan="7" class="pl-1 pr-1 text-center align-middle">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($request->id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . str_pad($request->id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                            </td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Category</strong></td>
                            <td>' . $category . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Route</strong></td>
                            <td>' . $route_name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>City</strong></td>
                            <td>' . $city_name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total Shipments</strong></td>
                            <td>' . $delivery_note_details->shipments_count . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Delivered Shipments</strong></td>
                            <td>' . $total_shipments . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>DNCC Amount</strong></td>
                            <td>Rs ' . number_format($total_cod_amount) . '</td>
                          </tr>
                        </tbody>
                      </table>
        ';
            $html .= $main_details;
            $html .= $shipment_details;
            $html .= '
                      <div class="mt-2 manual_form">
                      <div class="row  mt-1">
                         <div class="col">
                            <div class="text-right">
                                <span class="d-inline-block w-150 text-left"><strong>DNCC Amount</strong></span>
                                <strong>Rs. ' . number_format($total_cod_amount) . '</strong>
                            </div>
                          </div>
                        </div>
                        <hr>
                        
                        <div class="row justify-content-center align-items-end mt-5">
                          <div class="col justify-content-center ">
                            <div class="text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Rider Name</strong>
                            </div>
                          </div>
                          <div class="col justify-content-center ">
                            <div class="text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Rider Signature</strong>
                            </div>
                          </div>
                        </div>
                        <div class="row justify-content-between align-items-end mt-5">
                          <div class="col justify-content-center ">
                            <div class="text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Operation Staff Name</strong>
                            </div>
                          </div>
                          <div class="col justify-content-center ">
                            <div class="text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Operation Staff Signature</strong>
                            </div>
                          </div>
                        </div>
                        <div class="row justify-content-between align-items-end mt-5">
                          <div class="col justify-content-center ">
                            <div class="text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Cashier Name</strong>
                            </div>
                          </div>
                          <div class="col justify-content-center ">
                            <div class="text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Cashier Signature</strong>
                            </div>
                          </div>
                        </div>
                      </div>
        ';
        }

//        return $shipments;


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

        return $html;


    }

    public function outstanding_sdn_delivery_notes_list(Request $request)
    {
        $delivery_notes = StationDepositNote::join('delivery_note_station_deposit_notes as dnsdn', 'station_deposit_notes.id', '=', 'dnsdn.station_deposit_note_id')
            ->join('delivery_notes as dn', 'dnsdn.delivery_note_id', '=', 'dn.id')
            ->join('cities as h', 'dn.hub_id', '=', 'h.id')
            ->join('riders as ri', 'dn.rider_id', '=', 'ri.id')
            ->join('admins as a', 'dn.admin_id', '=', 'a.id')
            ->join('admins as au', 'dn.updated_by', '=', 'au.id')
            ->select('dn.id', 'dn.id as delivery_note_number', 'h.name as hub', 'ri.name as rider', 'dn.shipments_count as shipments', 'dn.delivered_shipments', 'a.name as assigned_by', 'dn.created_at as assigned_at', 'au.name as updated_by', 'dn.updated_at', 'dn.received_cod_amount as dncc_amount');

        if ($request->has('id')) {
            $delivery_notes->where('station_deposit_notes.id', $request->id);
        } else {
            $delivery_notes->whereRaw('FALSE');
        }

        $datatables = Datatables::of($delivery_notes)
            ->editColumn('delivery_note_number', function ($delivery_notes) {
                return str_pad($delivery_notes->delivery_note_number, 6, '0', STR_PAD_LEFT);
            })
            ->filterColumn('dn.id', function ($query, $keyword) {
                return $query->where('dn.id', '=', $keyword);
            })
            ->editColumn('dncc_amount', function ($shipment) {
                return number_format($shipment->dncc_amount);
            })
            ->addColumn('action', function ($delivery_notes) {
                return '<div class="btn-group">
                  <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                  <div class="dropdown-menu dropdown-menu-sm">
                    <button type="button" class="dropdown-item edit_expense"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit Expense</div></button>
                  </div>
                </div>
            ';
            });

        return $datatables->make(true);
    }

    public function outstanding_sdn_reconcile_delivery_notes(Request $request)
    {

        $this::outstanding_sdn_reconcile_delivery_notes_function($request->station_deposit_note_id, $request->delivery_note_ids);

        return redirect()->back()->with('success', 'Station Deposit No.' . str_pad($request->station_deposit_note_id, 6, '0', STR_PAD_LEFT) . ' has been Reconciled');
    }

    public function outstanding_sdn_reconcile_delivery_notes_excel(Request $request)
    {

        $rules = [
            'excel' => ['required', 'mimes:xlx,xlsx'],
        ];
        $validate = Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return back()->with(['error' => "Only Excel files are allowed"]);
        }

        $names = [
            'sdn_id' => 'Outstanding SDN Number',
        ];

        $messages = [
            'required' => ':attribute is Required.',
            'integer' => ':attribute must be an Integer.',
            'exists' => 'Given :attribute is Invalid.',

        ];
        $rules = [
            'sdn_id' => ['required', 'integer', Rule::exists('station_deposit_notes', 'id')->where(function ($query) {
                $query->where('status', 1);
            })],
        ];


        $fields = [0 => 'sdn_id'];
        if ($file = $request->file('excel')) {
            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

            $header = ['Outstanding SDN Number'];

            if (isset($spreadsheet)) {
                $header_correct = true;

                foreach ($spreadsheet[0] as $index => $header_value) {
                    if (!isset($header[$index]) || $header_value != $header[$index]) {
                        $header_correct = false;
                        break;
                    }
                }
                if (!$header_correct) {
                    return redirect()->back()->with('error', 'Invalid Columns, Kindly follow the Template provided');
                } else {
                    unset($spreadsheet[0]);
                }
            }
            if (!empty($spreadsheet) || !isset($spreadsheet)) {
                $rows = array();
                foreach ($spreadsheet as $spreadsheet_row) {
                    $row = array();

                    foreach ($spreadsheet_row as $key => $value) {
                        $row[$fields[$key]] = $value;
                    }

                    $rows[] = $row;
                }

                unset($spreadsheet);
                $errors = array();
                $sdn_array = array();

                foreach ($rows as $key => $row) {
                    $row_id = $key + 2;

                    $validate = Validator::make($row, $rules, $messages);

                    $validate->setAttributeNames($names);

                    if ($validate->fails()) {
                        $errors['Row #' . $row_id] = $validate->errors()->all();
                    } else if (StationDepositNote::where('id', $row['sdn_id'])->whereIn('hub_id', session('hubs'))->doesntExist()) {
                        $errors['Row #' . $row_id] = array("Invalid SDN Number");
                    } else if (StationDepositNote::where('id', $row['sdn_id'])->where('status', 2)->exists()) {
                        $errors['Row #' . $row_id] = array("SDN Already Reconciled");
                    } else if (in_array($row['sdn_id'], $sdn_array)) {
                        $errors['Row #' . $row_id] = array("Duplicate SDN Number");
                    }

                    $sdn_array[] = $row['sdn_id'];
                }

                if (empty($errors)) {

                    foreach ($rows as $index => $row) {
                        $delivery_notes = DeliveryNoteStationDepositNote::where('station_deposit_note_id', $row['sdn_id'])->pluck('delivery_note_id')->toArray();
                        $delivery_notes = implode(',', $delivery_notes);
                        $this::outstanding_sdn_reconcile_delivery_notes_function($row['sdn_id'], $delivery_notes);
                    }

                    return redirect()->back()->with(['success' => 'SDN Reconciled Successfully']);
                } else {
                    $errors = array_map(function ($row, $errors) {
                        return $row . ':' . PHP_EOL . implode(' | ', $errors);
                    }, array_keys($errors), $errors);
                    return redirect()->back()->withErrors($errors);
                }

            } else {
                return redirect()->back()->with('error', 'No Records in File');
            }
        }
    }

    public static function outstanding_sdn_reconcile_delivery_notes_function($station_deposit_note_id, $delivery_note_ids)
    {
        $updated_by = Auth::id();
        $station_deposit_note = StationDepositNote::find($station_deposit_note_id);

        $station_deposit_note->status = 2;
        $station_deposit_note->status_updated_at = Carbon::now();
        $station_deposit_note->status_updated_by = $updated_by;

        $station_deposit_note->save();

        $delivery_note_ids = explode(',', $delivery_note_ids);

        foreach ($delivery_note_ids as $delivery_note_id) {
            foreach (DeliveryNoteShipment::where('delivery_note_id', $delivery_note_id)->get() as $delivery_note_shipment) {
                if (in_array($delivery_note_shipment->status, [4, 5, 6])) {
                    $delivery_note_shipment->status = 7;

                    $delivery_note_shipment->save();
                    $shipment = Shipment::where('id', $delivery_note_shipment->shipment_id)->where('booking_type_id', '=', 4);

                    if ($shipment->exists()) {
                        $shipment = $shipment->first();

                        $shipment->walk_in_status = 1;

                        $shipment->save();
                    }
                }
            }
        }

        DeliveryController::add_sdn_logs($station_deposit_note_id, 2, $updated_by);
    }

    public function outstanding_sdn_export_to_excel(Request $request)
    {
        $delivery_note_station_deposit_notes = DeliveryNoteStationDepositNote::where('station_deposit_note_id', $request->id);

        if ($delivery_note_station_deposit_notes->exists()) {
            $delivery_note_station_deposit_notes = $delivery_note_station_deposit_notes->get();

            $details = array();

            $details[] = ['S. No.', 'DNCC No.', 'Hub', 'Rider', 'Route', 'Shipments', 'Delivered Shipments', 'DNCC Amount'];

            $serial_number = 1;

            foreach ($delivery_note_station_deposit_notes as $delivery_note_station_deposit_note) {
                $delivery_note = DeliveryNote::find($delivery_note_station_deposit_note->delivery_note_id);

                $row = array();

                $row[] = $serial_number;
                $row[] = str_pad($delivery_note->id, 6, '0', STR_PAD_LEFT);
                $row[] = $delivery_note->hub->name;
                $row[] = $delivery_note->rider->name;
                $row[] = $delivery_note->route->code . ' (' . $delivery_note->route->start . ' to ' . $delivery_note->route->end . ')';
                $row[] = $delivery_note->shipments_count;
                $row[] = $delivery_note->delivered_shipments;
                $row[] = number_format($delivery_note->received_cod_amount);

                $details[] = $row;

                $serial_number++;
            }

            $spreadsheet = new Spreadsheet();
            $spreadsheet->getActiveSheet()->fromArray($details);

            $writer = new Xlsx($spreadsheet);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="sonic_sdn_' . $request->id . '.xlsx"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
        }
    }

    public function outstanding_shipments_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 37);
        $hubs = City::orderBy('name')->get();
        $booking_types = BookingType::all();
        $service_type = BookingType::all();
        $shipment_status = ShipmentStatus::select('id', 'name')->get();
        return view('admin.finance.outstanding_shipments')->with(['hubs' => $hubs, 'booking_types' => $booking_types, 'service_type' => $service_type, 'shipment_status' => $shipment_status]);
    }

    public function outstanding_shipments_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 97);
        }

        if ($recovery_status = $request->get('recovery_status')) {
            if ($recovery_status == 7) {
                $count = DeliveryNoteShipment::where('status', '=', 7);
            } else if ($recovery_status == 11) {
                $count = DeliveryNoteShipment::where('status', '=', 7);
            } else {
                $count = DeliveryNoteShipment::whereIn('status', [4, 5, 6]);
            }
        } else {
            $count = DeliveryNoteShipment::whereIn('status', [4, 5, 6]);
        }

        $count = $count->count();

        $shipments = DeliveryNoteShipment::join('shipments as s', 'delivery_note_shipments.shipment_id', '=', 's.id')
            ->leftjoin('retail_shipments as rs', 'rs.shipment_id', '=', 's.id')
            ->join('user_shipping_infos as usi', 's.pickup_address_id', '=', 'usi.id')
            ->join('cities as oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities as dc', 's.consignee_city_id', '=', 'dc.id')
            ->join('cities as hc', 'dc.hub_id', '=', 'hc.id')
            ->join('users as u', 's.user_id', '=', 'u.id')
            ->join('booking_types as bt', 's.booking_type_id', '=', 'bt.id')
            ->leftjoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 's.id')
                    ->where('sj.id', '=', DB::raw('(SELECT MAX(id) FROM shipments_journey WHERE shipment_id = s.id)'));
            })
            ->leftjoin('shipments_journey as sjd', function ($join) {
                $join->on('sjd.shipment_id', '=', 's.id')
                    ->where('sjd.id', '=', DB::raw('(SELECT MAX(id) FROM shipments_journey WHERE shipment_id = s.id AND shipments_journey.shipper_status_id IN (14, 16, 30, 36))'));
            })
            ->leftjoin('shipment_status as ss', 'sj.shipper_status_id', '=', 'ss.id')
            ->leftjoin('delivery_note_station_deposit_notes as dnsdn', 'delivery_note_shipments.delivery_note_id', '=', 'dnsdn.delivery_note_id')
            ->leftjoin('revert_status_request_logs as rsrl', function ($join) {
                $join->on('rsrl.delivery_note_id', '=', 'delivery_note_shipments.delivery_note_id')
                    ->where('rsrl.id', '=', DB::raw('(SELECT MAX(id) FROM revert_status_request_logs WHERE revert_status_request_logs.delivery_note_id = delivery_note_shipments.delivery_note_id AND shipment_id = s.id)'));
            })
            ->leftjoin('admins as a', 'a.id', '=', 'rsrl.updated_by')
            ->leftjoin('revert_status_requests as rsr', function ($join) {
                $join->on('rsr.delivery_note_id', '=', 'delivery_note_shipments.delivery_note_id')
                    ->where('rsr.id', '=', DB::raw('(SELECT MAX(id) FROM revert_status_requests WHERE revert_status_requests.delivery_note_id = delivery_note_shipments.delivery_note_id AND shipment_id = s.id)'));
            })
            ->leftjoin('consolidation_shipments as consolidations', function ($join) {
                $join->on('consolidations.shipment_id', '=', 's.id')
                    ->where('consolidations.consolidation_id', '=',
                        DB::raw('(select consolidation_id from consolidation_shipments where consolidation_shipments.shipment_id = s.id)'));
            })
            ->select('s.id', 's.user_id', 's.tracking_number', 's.tracking_number as tracking_id', 's.consignee_name as consignee', 's.consignee_address as address', 'dc.name as destination', 'hc.name as hub', 'u.name as shipper', 'bt.booking_type as service_type', 's.amount', 'ss.name as status', 'sj.updated_at as status_updated_at', 'sj.remarks', 'delivery_note_shipments.delivery_note_id as dncc', 'delivery_note_shipments.delivery_note_id as dncc_link', 'dnsdn.station_deposit_note_id as sdn', 'dnsdn.station_deposit_note_id as sdn_link', 'sjd.created_at as delivered_at', 's.booking_type_id', 'usi.poc', 'delivery_note_shipments.status as recovery_status', 'rsr.created_at as recovery_date', 'rsrl.previous_status as previous_status', 'rsr.image as revert_requested_image', 'rsr.id as image_id', 'consolidations.consolidation_id', 'a.name as request_reverted_by');

        if (session('role_id') != 1) {
            $shipments = $shipments->whereIn('dc.hub_id', session('hubs'));
        }

        if (session('department_id') == 8) {
            $shipments = $shipments->where('s.shipment_type',2);
        }

        $check_lost_shipments_admins = LostShipmentAdmin::where('admin_id', Auth::id());
        if ($check_lost_shipments_admins->exists()) {
            $lost_shipments_shippers_id = LostShipmentShipper::pluck('user_id')->toArray();
            $shipments = $shipments->whereIn('s.user_id', $lost_shipments_shippers_id);

        }

        $datatables = Datatables::of($shipments)
            ->setTotalRecords($count)
            ->setRowAttr([
                'data-dncc' => function ($shipments) {
                    return $shipments->dncc;
                },
                'data-sdn' => function ($shipments) {
                    return $shipments->sdn;
                },
                'recovery_status' => function ($shipments) {
                    return $shipments->recovery_status;
                },
                'class' => function ($shipments) {
                    if ($shipments->recovery_status == 11) {
                        if (in_array($shipments->previous_status, [4, 5, 6])) {
                            return 'outstanding_revert';
                        } else if ($shipments->previous_status == 7) {
                            return 'resolved_revert';
                        } else {
                            return '';
                        }
                    } else {
                        return '';
                    }
                },
                'consolidation_id' => function ($shipments) {
                    if ($shipments->consolidation_id != null) {
                        return $shipments->consolidation_id;
                    } else {
                        return '';
                    }
                }
            ])
            ->editColumn('shipper', function ($shipment) {
                if ($shipment->booking_type_id == 4) {
                    return $shipment->shipper . ' (' . $shipment->poc . ')';
                } else {
                    return $shipment->shipper;
                }
            })
            ->filterColumn('ss.id', function ($query, $keyword) {
                if ($keyword != '') {
                    $query->where('ss.id', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('u.name', function ($query, $keyword) {
                $query->where(function ($sub_query) use ($keyword) {
                    $sub_query->where('s.booking_type_id', '!=', 4)
                        ->where('u.name', 'like', '%' . $keyword . '%');
                })
                    ->orWhere(function ($sub_query) use ($keyword) {
                        $sub_query->where('s.booking_type_id', '=', 4)
                            ->where('usi.poc', 'like', '%' . $keyword . '%');
                    });
            })
            ->editColumn('amount', function ($shipment) {
                return number_format($shipment->amount);
            })
            ->editColumn('request_reverted_by', function ($shipments) {
                if ($shipments->request_reverted_by != null) {
                    return $shipments->request_reverted_by;
                } else {
                    return '-';
                }
            })
            ->orderColumn('u.name', 'u.name $1, usi.poc $1')
            ->editColumn('tracking_number', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('dncc', function ($shipment) {
                if ($shipment->dncc) {
                    return str_pad($shipment->dncc, 6, '0', STR_PAD_LEFT);
                } else {
                    return '';
                }
            })
            ->filterColumn('delivery_note_shipments.delivery_note_id', function ($query, $keyword) {
                return $query->where('delivery_note_shipments.delivery_note_id', '=', $keyword);
            })
            ->editColumn('dncc_link', function ($shipment) {
                if ($shipment->dncc) {
                    return '<button class="btn btn-sm btn-outline-info align-middle print"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($shipment->dncc, 6, '0', STR_PAD_LEFT) . '</span></button>';
                } else {
                    return '';
                }
            })
            ->editColumn('sdn', function ($shipment) {
                if ($shipment->sdn) {
                    return str_pad($shipment->sdn, 6, '0', STR_PAD_LEFT);
                } else {
                    return '';
                }
            })
            ->editColumn('sdn_link', function ($shipment) {
                if ($shipment->sdn) {
                    return '<button class="btn btn-sm btn-outline-info align-middle print"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($shipment->sdn, 6, '0', STR_PAD_LEFT) . '</span></button>';
                } else {
                    return '';
                }
            })
            ->filterColumn('dnsdn.station_deposit_note_id', function ($query, $keyword) {
                return $query->where('dnsdn.station_deposit_note_id', '=', $keyword);
            })
            ->addColumn('aging', function ($shipment) {
                $updated_at = Carbon::parse($shipment->status_updated_at)->startOfDay();

                $now = Carbon::now()->startOfDay();

                return $updated_at->diffInDays($now) . 'd';
            })
            ->addColumn('shipment_recovery_status', function ($shipment) {
                if (in_array($shipment->recovery_status, [4, 5, 6])) {
                    return "Outstanding";
                } else if ($shipment->recovery_status == 7) {
                    return "Resolved";
                } else if ($shipment->recovery_status == 11) {
                    return "Revert Requested";
                }
            })
            ->editColumn('revert_requested_image_button', function ($shipments) {
                $image = '<div class="text-center">';
                if ($shipments->recovery_status == 11) {
                    if ($shipments->revert_requested_image != null) {
                        $image .= '<button type="button" class="btn btn-primary btn-sm"><a class="white" href=' . route('admin.finance.outstanding_shipments.revert_requested_image', [$shipments->image_id]) . ' target="_blank">View</a></button>';
                    }
                }
                $image .= '</div>';
                return $image;
            })
            ->addColumn('action', function ($shipment) {
                $resolve_button = '<button type="button" class="dropdown-item resolve"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check-circle"></i></div><div class="col-9 offset-1">Resolve</div></button>';
                $reject_button = '<button type="button" class="dropdown-item reject"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Reject</div></button>';
                $adjust_in_payment_button = '<button type="button" class="dropdown-item adjust_in_payment"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-alert-octagon"></i></div><div class="col-9 offset-1">Adjust in Payment</div></button>';

                if (session('role_id') == 1 || count(array_intersect([55, 56, 346], session('permissions'))) !== 0) {
                    $dropdown = '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                ';

                    if (session('role_id') == 1 || in_array(55, session('permissions'))) {
                        if ($shipment->recovery_status != 7 && $shipment->recovery_status != 11) {
                            $dropdown .= $resolve_button;
                        }
                    }

                    $updated_at = Carbon::parse($shipment->status_updated_at)->startOfDay();

                    $now = Carbon::now()->startOfDay();

                    $difference = $updated_at->diffInDays($now);

                    if (($difference <= 2 && in_array(56, session('permissions'))) || session('role_id') == 1 || in_array(346, session('permissions'))) {

                        $dropdown .= $adjust_in_payment_button;

                    }

                    if (session('role_id') == 1 || in_array(55, session('permissions'))) {
                        if ($shipment->recovery_status == 11) {
                            $dropdown .= $reject_button;
                        }
                    }

                    $dropdown .= '
                    </div>
                  </div>
                ';

                    return $dropdown;
                } else {
                    return '';
                }
            });

        if ($recovery_status = $request->get('recovery_status')) {
            if ($recovery_status == 7) {
                $datatables->where('delivery_note_shipments.status', '=', 7);
            } else if ($recovery_status == 11) {
                $datatables->where('delivery_note_shipments.status', '=', 11);
            } else {
                $datatables->whereIn('delivery_note_shipments.status', [4, 5, 6]);
            }
        } else {
            $datatables->whereIn('delivery_note_shipments.status', [4, 5, 6]);
        }

        if ($hub = $request->get('hub')) {
            $datatables->where('hc.id', '=', $hub);
        }

        if ($service = $request->get('service')) {
            $datatables->where('bt.id', '=', $service);
        }

        if ($delivery_date_from = $request->get('delivery_date_from')) {
            $datatables->where('sjd.created_at', '>=', $delivery_date_from);
        }

        if ($delivery_date_to = $request->get('delivery_date_to')) {
            $datatables->where('sjd.created_at', '<', Carbon::parse($delivery_date_to)->addDay()->toDateTimeString());
        }

        if ($tracking_numbers = $request->get('tracking_numbers')) {
            $datatables->whereIn('s.tracking_number', explode(',', $tracking_numbers));
        }

        return $datatables->make(true);
    }

    public function outstanding_shipments_resolved(Request $request)
    {
        $delivery_note_shipment = DeliveryNoteShipment::where('shipment_id', $request->id)->whereIn('status', [4, 5, 6, 11]);

        if ($delivery_note_shipment->exists()) {
            $delivery_note_shipment = $delivery_note_shipment->first();
            $shipment = Shipment::where('id', $delivery_note_shipment->shipment_id)->where('booking_type_id', '=', 4);

            if ($shipment->exists()) {
                $shipment = $shipment->first();


                $shipment->walk_in_status = 1;

                $shipment->save();
            }

            $shipment_resolved = Shipment::find($delivery_note_shipment->shipment_id);
            if ($shipment_resolved) {
                $lost_shipment_shipper = LostShipmentShipper::where('user_id', $shipment_resolved->user_id);
                if ($lost_shipment_shipper->exists()) {
                    return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been modified'];
                }
            }


            $delivery_note_shipment->status = 7;

            $delivery_note_shipment->save();

            return ['status' => 0, 'success' => 'Shipment has been marked Resolved'];
        } else {
            return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been modified'];
        }
    }

    public function outstanding_shipments_bulk_resolved(Request $request)
    {
        foreach ($request->shipments as $shipment_id) {
            $delivery_note_shipment = DeliveryNoteShipment::where('shipment_id', $shipment_id)->whereIn('status', [4, 5, 6, 11]);
            if ($delivery_note_shipment->exists()) {
                $delivery_note_shipment = $delivery_note_shipment->first();

                $shipment = Shipment::where('id', $delivery_note_shipment->shipment_id)->where('booking_type_id', '=', 4);

                if ($shipment->exists()) {
                    $shipment = $shipment->first();
                    $shipment->walk_in_status = 1;
                    $shipment->save();
                }

                $shipment_resolved = Shipment::find($delivery_note_shipment->shipment_id);
                if ($shipment_resolved) {
                    $lost_shipment_shipper = LostShipmentShipper::where('user_id', $shipment_resolved->user_id);
                    if (!$lost_shipment_shipper->exists()) {

                        $delivery_note_shipment->status = 7;

                        $delivery_note_shipment->save();
                    }
                }

            }
        }
        return ['status' => 0, 'success' => 'Shipments has been marked Resolved'];
    }

    public function revert_requested_image($image_id)
    {
        $revert_Status_request = RevertStatusRequest::find($image_id);
        $image_url = $revert_Status_request->image;
        $url = Storage::url('revert_status_requests/' . $image_url . '');

        return view('admin.finance.revert_requested_image')->with(['url' => $url]);
    }

    public function outstanding_walk_in_shipments_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 38);
        $shipping_modes = ShippingMode::where('id', '!=', 4)->get();
        $shipment_status = ShipmentStatus::select('id', 'name')->get();
        $charges_mode_name = ChargesModes::select('id', 'charges_mode')->get();
        $status = [['id' => 0, 'text' => 'Pending Charges Collection'], ['id' => 1, 'text' => 'Resolved'], ['id' => 2, 'text' => 'Pending Return Charges Collection']];
        return view('admin.finance.outstanding_walk_in_shipments')->with(['shipment_status' => $shipment_status, 'status' => json_encode($status), 'charges_mode_name' => $charges_mode_name, 'shipping_modes' => $shipping_modes]);
    }

    public function outstanding_walk_in_shipments_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 98);
        }

        $shipments = Shipment::join('cities as dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as hc', 'dc.hub_id', '=', 'hc.id')
            ->join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos as usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities as oc', 'usi.city_id', '=', 'oc.id')
            ->join('booking_types as bt', 'shipments.booking_type_id', '=', 'bt.id')
            ->leftjoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id', '=', DB::raw('(SELECT MAX(id) FROM shipments_journey WHERE shipments_journey.shipment_id = shipments.id)'));
            })
            ->leftjoin('charges_modes as cm', 'shipments.charges_mode_id', '=', 'cm.id')
            ->leftjoin('shipments_journey as an', function ($join) {
                $join->on('an.shipment_id', '=', 'shipments.id')
                    ->where('an.id', '=', DB::raw('(SELECT MAX(id) FROM shipments_journey WHERE shipments_journey.shipment_id = shipments.id and shipper_status_id = 1)'));
            })
            ->leftjoin('admins as adn', 'adn.id', '=', 'an.admin_id')
            ->leftjoin('shipment_status as ss', 'sj.shipper_status_id', '=', 'ss.id')
            ->leftjoin('admins as a', 'sj.admin_id', '=', 'a.id')
            ->leftjoin('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
            ->leftjoin('shipment_items as sis', 'sis.shipment_id', '=', 'shipments.id')
            ->leftjoin('products as prod', 'prod.id', '=', 'sis.product_type_id')
            ->leftjoin('resolved_outstanding_shipments as ros', 'ros.shipment_id', '=', 'shipments.id')
            ->leftjoin('admins as rosa', 'rosa.id', '=', 'ros.resolved_by')
            ->select('shipments.id', 'shipments.tracking_number', 'shipments.tracking_number as tracking_no', 'shipments.consignee_name as consignee', 'shipments.consignee_address as address', 'dc.name as destination', 'hc.name as hub', 'ss.name as status', 'sj.updated_at as status_updated_at', 'a.name as updated_by', 'shipments.created_at', 'shipments.amount', 'shipments.received_amount', 'shipments.charges_mode_id', 'sj.shipper_status_id as shipper_status_id', 'shipments.return_charges as return_charges', 'shipments.gst as gst', 'shipments.fuel_surcharge as fuel_surcharge', 'shipments.weight_charges as weight_charges', 'cm.charges_mode as charges_modes', 'shipments.walk_in_status as walk_in_status', 'shipments.walk_in_status as walk_in_status_id', 'adn.name as booked_by', 'oc.name as origin', 'shipments.actual_weight as actual_weight', 'shipments.chargeable_weight as chargeable_weight', 'sm.mode as shipping_mode', 'sis.quantity as item_quantity', 'prod.product_name as product_name', 'rosa.name as resolved_by', 'ros.created_at as resolved_at')
            ->where('shipments.booking_type_id', 4)->where('shipments.shipper_status_id', '!=', 17);


        $datatables = Datatables::of($shipments)
            ->editColumn('tracking_number', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('walk_in_status', function ($shipments) {
                if ($shipments->walk_in_status == 0) {
                    return 'Pending Charges Collection';
                } else if ($shipments->walk_in_status == 1) {
                    return 'Resolved';
                } else {
                    return 'Pending Return Charges Collection';
                }
            })
            ->addColumn('charges', function ($shipment) {
                if ($shipment->charges_mode_id == 1) {
                    return (($shipment->received_amount) ? number_format($shipment->received_amount) : '0');
                } else if ($shipment->charges_mode_id == 2) {
                    return (($shipment->amount) ? number_format($shipment->amount) : '0');
                } else {
                    return '0';
                }
            })
            ->editColumn('return_charges', function ($shipment) {
                return number_format($shipment->return_charges, 2);
            })
            ->editColumn('weight_charges', function ($shipment) {
                return number_format($shipment->weight_charges, 2);
            })
            ->addColumn('aging', function ($shipment) {
                $updated_at = Carbon::parse($shipment->status_updated_at)->startOfDay();

                $now = Carbon::now()->startOfDay();

                return $updated_at->diffInDays($now) . 'd';
            })
            ->filterColumn('ss.id', function ($query, $keyword) {
                if ($keyword != '') {
                    $query->where('ss.id', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn('action', function ($shipment) {
                $resolve_button = '<button type="button" class="dropdown-item resolve"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check-circle"></i></div><div class="col-9 offset-1">Resolve</div></button>';

                if ((($shipment->charges_mode_id == 1) || ($shipment->charges_mode_id == 2 && ($shipment->shipper_status_id == 14 || $shipment->shipper_status_id == 25))) && ($shipment->walk_in_status != 1) && (session('role_id') == 1 || count(array_intersect([168], session('permissions'))) !== 0)) {
                    $dropdown = '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                ';

                    if (session('role_id') == 1 || in_array(168, session('permissions'))) {
                        $dropdown .= $resolve_button;
                    }

                    $dropdown .= '
                    </div>
                  </div>
                ';

                    return $dropdown;
                } else {
                    return '';
                }
            });

        if ($tracking_numbers = $request->get('tracking_numbers')) {
            $datatables->whereIn('shipments.tracking_number', explode(',', $tracking_numbers));
        }
        return $datatables->make(true);
    }

    public function outstanding_walk_in_shipments_resolved(Request $request)
    {
        $shipment = Shipment::where('id', $request->id)->where('walk_in_status', '!=', 1);

        if ($shipment->exists()) {
            $shipment = $shipment->first();

            $shipment->walk_in_status = 1;

            $shipment->save();

            $resolved_shipment = new ResolvedOutstandingShipment();
            $resolved_shipment->shipment_id = $shipment->id;
            $resolved_shipment->resolved_by = Auth::id();
            $resolved_shipment->save();

            return ['status' => 0, 'success' => 'Shipment has been marked Resolved'];
        } else {
            return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been modified'];
        }
    }

    public function outstanding_walk_in_shipments_bulk_resolved(Request $request)
    {
        $shipment_ids = $request->shipments;
        foreach ($shipment_ids as $shipment_id) {
            $shipment = Shipment::where('id', $shipment_id)->where('walk_in_status', '!=', 1);
            if ($shipment->exists()) {
                $shipment = $shipment->first();
                if ((($shipment->charges_mode_id == 1) || ($shipment->charges_mode_id == 2 && ($shipment->shipper_status_id == 14 || $shipment->shipper_status_id == 25)))) {

                    $shipment->walk_in_status = 1;

                    $shipment->save();

                    $resolved_shipment = new ResolvedOutstandingShipment();
                    $resolved_shipment->shipment_id = $shipment->id;
                    $resolved_shipment->resolved_by = Auth::id();
                    $resolved_shipment->save();
                }
            }

        }
        return ['status' => 0, 'success' => 'Selected Shipments has been marked as Resolved'];
    }

    public function outstanding_sdn_edit_deposit_slip(Request $request)
    {
        $sdn_id = $request->sdn_id;
        if ($sdn_id) {
            $slips = StationDepositNoteSlip::where('station_deposit_note_id', $sdn_id)->get();
            if (count($slips) > 0) {
                $sorted_array = array();
                foreach ($slips as $slip) {
                    $sorted_array[$slip->id]['date'] = $slip->deposit_date;
                    $sorted_array[$slip->id]['bank'] = $slip->bank_id;
                    $sorted_array[$slip->id]['amount'] = $slip->amount;
                    $sorted_array[$slip->id]['image'] = $slip->image;
                }

                return ['status' => 0, 'slips' => $sorted_array];
            } else {
                return ['status' => 1, 'error' => 'No deposit note slips found!'];
            }
        } else {
            return ['status' => 1, 'error' => 'No deposit note ID selected!'];

        }
    }

    public function outstanding_sdn_edit_deposit_slip_submit(Request $request)
    {

        $sdn_id = $request->sdn_id;
        $deposit_ids = explode(',', $request->deposit_rows);
        $total_amount = 0;
        foreach ($deposit_ids as $deposit_id) {
            $total_amount += $request->amount[$deposit_id];
            $slip = StationDepositNoteSlip::find($deposit_id);
            $slip->deposit_date = $request->date[$deposit_id];
            $slip->bank_id = $request->bank[$deposit_id];
            $slip->amount = $request->amount[$deposit_id];

            $file_name = 'deposit_slip_' . $deposit_id;
            if ($request->has($file_name)) {
                $image = $request->file($file_name);
//                $extension = $image->getClientOriginalExtension();
                $extension = 'png';
                $random = rand(1000, 100000);
                $now = Carbon::now();
                $time = $now->year . '_' . $now->month;
                $slip_name = $time . $random . Auth::id() . '.' . $extension;
                $image->move(public_path('uploads/sdn'), $slip_name);

                $slip->image = $slip_name;
            }
            $slip->save();

        }

        if ($request->new_deposit_rows) {
            $new_deposit_ids = explode(',', $request->new_deposit_rows);
            if (count($new_deposit_ids) > 0) {
                foreach ($new_deposit_ids as $row) {
                    $total_amount += $request->new_amount[$row];

                    $deposit_details = new StationDepositNoteSlip();
                    $deposit_details->station_deposit_note_id = $sdn_id;
                    $deposit_details->deposit_date = $request->new_date[$row];
                    $deposit_details->bank_id = $request->new_bank[$row];
                    $deposit_details->amount = $request->new_amount[$row];
                    $deposit_details->save();
                    $file_name = 'new_deposit_slip_' . $row;
                    $image = $request->file($file_name);
                    $extension = 'png';
                    $random = rand(1000, 100000);
                    $now = Carbon::now();
                    $time = $now->year . '_' . $now->month;
                    $slip = $time . $random . Auth::id() . '.' . $extension;
                    $image->move(public_path('uploads/sdn'), $slip);

                    $deposit_details->image = $slip;
                    $deposit_details->save();
                }
            }

        }

        $sdn_detail = StationDepositNote::find($sdn_id);
        $sdn_detail->sdn_deposit_amount = $total_amount;
        $sdn_detail->save();
        return redirect()->back()->with(['status' => 1, 'success' => 'Deposit Slip edited successfully!']);
    }

    static public function replacement_or_try_and_buy_adjust_in_payment($shipment_id)
    {
        $delivery_note_shipment = DeliveryNoteShipment::where('shipment_id', $shipment_id)->whereIn('status', [4, 5, 6]);
        if ($delivery_note_shipment->exists()) {
            $delivery_note_shipment = $delivery_note_shipment->first();

            $shipment = Shipment::find($shipment_id);

            $delivery_note_shipment->status = 8;

            $delivery_note_shipment->save();

            $delivery_note = $delivery_note_shipment->delivery_note;

            $delivery_note_amount = $delivery_note->received_cod_amount - $shipment->amount;

            if ($delivery_note_amount < 0) {
                $delivery_note_amount = 0;
            }

            $delivery_note->delivered_shipments = $delivery_note->delivered_shipments - 1;
            $delivery_note->received_cod_amount = $delivery_note_amount;

            $delivery_note->save();

            $delivery_note_station_deposit_note = $delivery_note->delivery_note_station_deposit_note;

            if ($delivery_note_station_deposit_note) {
                $station_deposit_note = $delivery_note_station_deposit_note->station_deposit_note;

                $station_deposit_note_amount = $station_deposit_note->sdn_amount - $shipment->amount;

                if ($station_deposit_note_amount < 0) {
                    $station_deposit_note_amount = 0;
                }

                $station_deposit_note->sdn_delivered_shipments = $station_deposit_note->sdn_delivered_shipments - 1;
                $station_deposit_note->sdn_amount = $station_deposit_note_amount;
                $station_deposit_note->sdn_net_amount = $station_deposit_note_amount;

                $station_deposit_note->save();
            }

            $shipment->payment_status_id = 4;

            $shipment->save();

            $account_type_id = $shipment->user->account_type_id;

            if ($account_type_id == 1) {
                $pending_payment_shipment = PendingPaymentShipment::where('shipment_id', $shipment_id);

                if ($pending_payment_shipment->exists()) {
                    $pending_payment_shipment = $pending_payment_shipment->latest()->first();

                    self::adjust_payment($pending_payment_shipment->pending_payment_id, $shipment_id, 0, 14);
                } else {
                    $done_payment_shipment = DonePaymentShipment::where('shipment_id', $shipment_id);

                    if ($done_payment_shipment->exists()) {
                        $done_payment_shipment = $done_payment_shipment->latest()->first();

                        self::adjust_payment($done_payment_shipment->done_payment_id, $shipment_id, 1, 14);
                    }
                }
            } else {
                $payment_shipment_id = NULL;
                $payment_type = NULL;
                $invoice_shipment_id = NULL;
                $invoice_type = NULL;

                $pending_payment_shipment = PendingPaymentShipment::where('shipment_id', $shipment->id);

                if ($pending_payment_shipment->exists()) {
                    $pending_payment_shipment = $pending_payment_shipment->latest()->first();

                    $payment_shipment_id = $pending_payment_shipment->id;
                    $payment_type = 0;
                } else {
                    $done_payment_shipment = DonePaymentShipment::where('shipment_id', $shipment->id);

                    if ($done_payment_shipment->exists()) {
                        $done_payment_shipment = $done_payment_shipment->latest()->first();

                        $payment_shipment_id = $done_payment_shipment->id;
                        $done_payment_id = $done_payment_shipment->done_payment_id;
                        $payment_type = 1;
                    }
                }

                $pending_invoice_shipment = PendingInvoiceShipment::where('shipment_id', $shipment->id);

                if ($pending_invoice_shipment->exists()) {
                    $pending_invoice_shipment = $pending_invoice_shipment->latest()->first();

                    $invoice_shipment_id = $pending_invoice_shipment->id;
                    $invoice_type = 0;
                } else {
                    $done_invoice_shipment = InvoiceShipment::where('shipment_id', $shipment->id);

                    if ($done_invoice_shipment->exists()) {
                        $done_invoice_shipment = $done_invoice_shipment->latest()->first();

                        $invoice_shipment_id = $done_invoice_shipment->id;
                        $invoice_type = 1;
                    }
                }

                if ($payment_shipment_id != NULL || $invoice_shipment_id != NULL) {
                    self::adjust_invoice($shipment->id, $payment_shipment_id, $payment_type, $invoice_shipment_id, $invoice_type, 3);
                }
            }
            if (isset($payment_type) && $payment_type == 1) {
                ShipmentsPaymentJourneyController::add($shipment_id, 4, Auth::id(), '', $done_payment_id);
            } else {
                ShipmentsPaymentJourneyController::add($shipment_id, 4, Auth::id());
            }

        }
    }

    public function outstanding_shipments_bulk_adjust_in_payment(Request $request)
    {
        $now = Carbon::now()->startOfDay();
        foreach ($request->shipment_ids as $index => $shipment_id) {
            $delivery_note_id = $request->dncc[$index];
            $delivery_note_shipment = DeliveryNoteShipment::where('shipment_id', $shipment_id)->whereIn('status', [4, 5, 6, 11])->where('delivery_note_id', $delivery_note_id);
            if ($delivery_note_shipment->exists()) {
                $delivery_note_shipment = $delivery_note_shipment->first();

                $shipment = Shipment::find($shipment_id);
                $lost_shipment_shipper = LostShipmentShipper::where('user_id', $shipment->user_id);
                if ($lost_shipment_shipper->exists()) {
                    $lost_shipments_admins = LostShipmentAdmin::where('admin_id', Auth::id());
                    if ($lost_shipments_admins->exists()) {
                        if ($shipment->shipment_type == 1) {
                            $journey = ShipmentsJourney::where('shipment_id', $shipment->id)->latest('id')->first();
                            if ($journey) {
                                $start = $journey->created_at;
                                $difference = $start->diffInDays($now);
                                if ($difference <= 2 || (session('role_id') == 1) || in_array(346, session('permissions'))) {

                                    $delivery_note_shipment->status = 8;

                                    $delivery_note_shipment->save();

                                    $delivery_note = $delivery_note_shipment->delivery_note;

                                    $delivery_note_amount = $delivery_note->received_cod_amount - $shipment->amount;

                                    if ($delivery_note_amount < 0) {
                                        $delivery_note_amount = 0;
                                    }

                                    $delivery_note->delivered_shipments = $delivery_note->delivered_shipments - 1;
                                    $delivery_note->received_cod_amount = $delivery_note_amount;

                                    $delivery_note->save();

                                    $delivery_note_station_deposit_note = $delivery_note->delivery_note_station_deposit_note;

                                    if ($delivery_note_station_deposit_note) {
                                        $station_deposit_note = $delivery_note_station_deposit_note->station_deposit_note;

                                        $station_deposit_note_amount = $station_deposit_note->sdn_amount - $shipment->amount;

                                        if ($station_deposit_note_amount < 0) {
                                            $station_deposit_note_amount = 0;
                                        }

                                        $station_deposit_note->sdn_delivered_shipments = $station_deposit_note->sdn_delivered_shipments - 1;
                                        $station_deposit_note->sdn_amount = $station_deposit_note_amount;
                                        $station_deposit_note->sdn_net_amount = $station_deposit_note_amount;

                                        $station_deposit_note->save();
                                    }

                                    $shipment->shipper_status_id = 13;
                                    $shipment->consignee_status_id = 13;

                                    $shipment->payment_status_id = 4;

                                    $shipment->save();

                                    if ($shipment->booking_type_id == 3) {
                                        ShipmentItem::where('shipment_id', $shipment->id)->update(['bought' => 0]);
                                    }

                                    $account_type_id = $shipment->user->account_type_id;

                                    if ($account_type_id == 1) {
                                        $pending_payment_shipment = PendingPaymentShipment::where('shipment_id', $shipment_id);

                                        if ($pending_payment_shipment->exists()) {
                                            $pending_payment_shipment = $pending_payment_shipment->latest()->first();

                                            $this->adjust_payment($pending_payment_shipment->pending_payment_id, $shipment_id, 0, 2);
                                        } else {
                                            $done_payment_shipment = DonePaymentShipment::where('shipment_id', $shipment_id);

                                            if ($done_payment_shipment->exists()) {
                                                $done_payment_shipment = $done_payment_shipment->latest()->first();

                                                $this->adjust_payment($done_payment_shipment->done_payment_id, $shipment_id, 1, 2);
                                            }
                                        }
                                    } else {
                                        $payment_shipment_id = NULL;
                                        $payment_type = NULL;
                                        $invoice_shipment_id = NULL;
                                        $invoice_type = NULL;

                                        $pending_payment_shipment = PendingPaymentShipment::where('shipment_id', $shipment_id);

                                        if ($pending_payment_shipment->exists()) {
                                            $pending_payment_shipment = $pending_payment_shipment->latest()->first();

                                            $payment_shipment_id = $pending_payment_shipment->id;
                                            $payment_type = 0;
                                        } else {
                                            $done_payment_shipment = DonePaymentShipment::where('shipment_id', $shipment_id);

                                            if ($done_payment_shipment->exists()) {
                                                $done_payment_shipment = $done_payment_shipment->latest()->first();

                                                $payment_shipment_id = $done_payment_shipment->id;
                                                $payment_type = 1;
                                                $done_payment_id = $done_payment_shipment->done_payment_id;
                                            }
                                        }

                                        $pending_invoice_shipment = PendingInvoiceShipment::where('shipment_id', $shipment_id);

                                        if ($pending_invoice_shipment->exists()) {
                                            $pending_invoice_shipment = $pending_invoice_shipment->latest()->first();

                                            $invoice_shipment_id = $pending_invoice_shipment->id;
                                            $invoice_type = 0;
                                        } else {
                                            $done_invoice_shipment = InvoiceShipment::where('shipment_id', $shipment_id);

                                            if ($done_invoice_shipment->exists()) {
                                                $done_invoice_shipment = $done_invoice_shipment->latest()->first();

                                                $invoice_shipment_id = $done_invoice_shipment->id;
                                                $invoice_type = 1;
                                            }
                                        }

                                        if ($payment_shipment_id != NULL || $invoice_shipment_id != NULL) {
                                            self::adjust_invoice($shipment->id, $payment_shipment_id, $payment_type, $invoice_shipment_id, $invoice_type, 2);
                                        }
                                    }

                                    if (isset($payment_type) && $payment_type == 1) {
                                        ShipmentsPaymentJourneyController::add($shipment_id, 4, Auth::id(), '', $done_payment_id);
                                    } else {
                                        ShipmentsPaymentJourneyController::add($shipment_id, 4, Auth::id());
                                    }

                                    ShipmentsJourneyController::add($shipment_id, 13, 13, NULL, "Shipment Reverted", NULL, Auth::id());

                                    if ($shipment->packaging_material_request == 1) {
                                        $packaging_material_shipment = PackagingMaterialRequest::where('tracking_number', $shipment->tracking_number)->first();
                                        if ($packaging_material_shipment != null) {
                                            $packaging_material_shipment->status_id = 3;
                                            $packaging_material_shipment->save();

                                            $packaging_request_history = new PackagingMaterialRequestHistory();
                                            $packaging_request_history->packaging_material_request_id = $packaging_material_shipment->id;
                                            $packaging_request_history->status = 3;
                                            $packaging_request_history->updated_by = \Illuminate\Support\Facades\Auth::id();
                                            $packaging_request_history->save();
                                        }
                                    }

                                    NotificationsController::send(21, $shipment_id, Auth::id());
                                }
                            }
                        } elseif ($shipment->shipment_type == 2) {
                            $journey = ShipmentsJourney::where('shipment_id', $shipment->id)->latest('id')->first();
                            if ($journey) {
                                $start = $journey->created_at;
                                $difference = $start->diffInDays($now);
                                if ($difference <= 2 || (session('role_id') == 1 || in_array(346, session('permissions')))) {

                                    $delivery_note_shipment->status = 8;

                                    $delivery_note_shipment->save();

                                    $delivery_note = $delivery_note_shipment->delivery_note;

                                    $delivery_note_amount = $delivery_note->received_cod_amount - $shipment->amount;

                                    if ($delivery_note_amount < 0) {
                                        $delivery_note_amount = 0;
                                    }

                                    $delivery_note->delivered_shipments = $delivery_note->delivered_shipments - 1;
                                    $delivery_note->received_cod_amount = $delivery_note_amount;

                                    $delivery_note->save();

                                    $delivery_note_station_deposit_note = $delivery_note->delivery_note_station_deposit_note;

                                    if ($delivery_note_station_deposit_note) {
                                        $station_deposit_note = $delivery_note_station_deposit_note->station_deposit_note;

                                        $station_deposit_note_amount = $station_deposit_note->sdn_amount - $shipment->amount;

                                        if ($station_deposit_note_amount < 0) {
                                            $station_deposit_note_amount = 0;
                                        }

                                        $station_deposit_note->sdn_delivered_shipments = $station_deposit_note->sdn_delivered_shipments - 1;
                                        $station_deposit_note->sdn_amount = $station_deposit_note_amount;
                                        $station_deposit_note->sdn_net_amount = $station_deposit_note_amount;

                                        $station_deposit_note->save();
                                    }

                                    $shipment->shipper_status_id = 13;
                                    $shipment->consignee_status_id = 13;

                                    $shipment->payment_status_id = 4;

                                    $shipment->save();

                                    if ($shipment->booking_type_id == 3) {
                                        ShipmentItem::where('shipment_id', $shipment->id)->update(['bought' => 0]);
                                    }

                                    // $account_type_id = $shipment->user->account_type_id;
                                    // if ($account_type_id == 1) {
                                    //     $pending_payment_shipment = RetailPendingPaymentShipment::where('shipment_id', $shipment_id);
                                    //     if ($pending_payment_shipment->exists()) {
                                    //         $pending_payment_shipment = $pending_payment_shipment->latest()->first();
                                    //         $this->adjust_payment($pending_payment_shipment->retail_pending_payment_id, $shipment_id, 0, 2);
                                    //     }
                                    //     else {
                                    //         $done_payment_shipment = RetailDonePaymentShipment::where('shipment_id', $shipment_id);
                                    //         if ($done_payment_shipment->exists()) {
                                    //             $done_payment_shipment = $done_payment_shipment->latest()->first();
                                    //             $this->adjust_payment($done_payment_shipment->retail_done_payment_id, $shipment_id, 1, 2);
                                    //         }
                                    //     }
                                    // }
                                    // else {
                                    $payment_shipment_id = NULL;
                                    $payment_type = NULL;
                                    $invoice_shipment_id = NULL;
                                    $invoice_type = NULL;

                                    $pending_payment_shipment = RetailPendingPaymentShipment::where('shipment_id', $shipment_id);

                                    if ($pending_payment_shipment->exists()) {
                                        $pending_payment_shipment = $pending_payment_shipment->latest()->first();

                                        $payment_shipment_id = $pending_payment_shipment->id;
                                        $payment_type = 0;
                                    } else {
                                        $done_payment_shipment = RetailDonePaymentShipment::where('shipment_id', $shipment_id);

                                        if ($done_payment_shipment->exists()) {
                                            $done_payment_shipment = $done_payment_shipment->latest()->first();

                                            $payment_shipment_id = $done_payment_shipment->id;
                                            $payment_type = 1;
                                            $done_payment_id = $done_payment_shipment->retail_done_payment_id;
                                        }
                                    }
                                    //check for retail
                                    $pending_invoice_shipment = PendingInvoiceShipment::where('shipment_id', $shipment_id);

                                    if ($pending_invoice_shipment->exists()) {
                                        $pending_invoice_shipment = $pending_invoice_shipment->latest()->first();

                                        $invoice_shipment_id = $pending_invoice_shipment->id;
                                        $invoice_type = 0;
                                    } else {
                                        $done_invoice_shipment = InvoiceShipment::where('shipment_id', $shipment_id);

                                        if ($done_invoice_shipment->exists()) {
                                            $done_invoice_shipment = $done_invoice_shipment->latest()->first();

                                            $invoice_shipment_id = $done_invoice_shipment->id;
                                            $invoice_type = 1;
                                        }
                                    }
                                    //check for end

                                    if ($payment_shipment_id != NULL || $invoice_shipment_id != NULL) {
                                        self::adjust_invoice($shipment->id, $payment_shipment_id, $payment_type, $invoice_shipment_id, $invoice_type, 2);
                                    }
                                    // }

                                    if (isset($payment_type) && $payment_type == 1) {
                                        ShipmentsPaymentJourneyController::add($shipment_id, 4, Auth::id(), '', $done_payment_id, 1);
                                    } else {
                                        ShipmentsPaymentJourneyController::add($shipment_id, 4, Auth::id(), '', NULL, 1);
                                    }

                                    ShipmentsJourneyController::add($shipment_id, 13, 13, NULL, "Shipment Reverted", NULL, Auth::id());

                                    if ($shipment->packaging_material_request == 1) {
                                        $packaging_material_shipment = PackagingMaterialRequest::where('tracking_number', $shipment->tracking_number)->first();
                                        if ($packaging_material_shipment != null) {
                                            $packaging_material_shipment->status_id = 3;
                                            $packaging_material_shipment->save();

                                            $packaging_request_history = new PackagingMaterialRequestHistory();
                                            $packaging_request_history->packaging_material_request_id = $packaging_material_shipment->id;
                                            $packaging_request_history->status = 3;
                                            $packaging_request_history->updated_by = \Illuminate\Support\Facades\Auth::id();
                                            $packaging_request_history->save();
                                        }
                                    }

                                    NotificationsController::send(21, $shipment_id, Auth::id());
                                }
                            }
                        }
                    }

                } else {
                    if ($shipment->shipment_type == 1) {
                        $journey = ShipmentsJourney::where('shipment_id', $shipment->id)->latest('id')->first();
                        if ($journey) {
                            $start = $journey->created_at;
                            $difference = $start->diffInDays($now);
                            if ($difference <= 2 || (session('role_id') == 1) || in_array(346, session('permissions'))) {

                                $delivery_note_shipment->status = 8;

                                $delivery_note_shipment->save();

                                $delivery_note = $delivery_note_shipment->delivery_note;

                                $delivery_note_amount = $delivery_note->received_cod_amount - $shipment->amount;

                                if ($delivery_note_amount < 0) {
                                    $delivery_note_amount = 0;
                                }

                                $delivery_note->delivered_shipments = $delivery_note->delivered_shipments - 1;
                                $delivery_note->received_cod_amount = $delivery_note_amount;

                                $delivery_note->save();

                                $delivery_note_station_deposit_note = $delivery_note->delivery_note_station_deposit_note;

                                if ($delivery_note_station_deposit_note) {
                                    $station_deposit_note = $delivery_note_station_deposit_note->station_deposit_note;

                                    $station_deposit_note_amount = $station_deposit_note->sdn_amount - $shipment->amount;

                                    if ($station_deposit_note_amount < 0) {
                                        $station_deposit_note_amount = 0;
                                    }

                                    $station_deposit_note->sdn_delivered_shipments = $station_deposit_note->sdn_delivered_shipments - 1;
                                    $station_deposit_note->sdn_amount = $station_deposit_note_amount;
                                    $station_deposit_note->sdn_net_amount = $station_deposit_note_amount;

                                    $station_deposit_note->save();
                                }

                                $shipment->shipper_status_id = 13;
                                $shipment->consignee_status_id = 13;

                                $shipment->payment_status_id = 4;

                                $shipment->save();

                                if ($shipment->booking_type_id == 3) {
                                    ShipmentItem::where('shipment_id', $shipment->id)->update(['bought' => 0]);
                                }

                                $account_type_id = $shipment->user->account_type_id;

                                if ($account_type_id == 1) {
                                    $pending_payment_shipment = PendingPaymentShipment::where('shipment_id', $shipment_id);

                                    if ($pending_payment_shipment->exists()) {
                                        $pending_payment_shipment = $pending_payment_shipment->latest()->first();

                                        $this->adjust_payment($pending_payment_shipment->pending_payment_id, $shipment_id, 0, 2);
                                    } else {
                                        $done_payment_shipment = DonePaymentShipment::where('shipment_id', $shipment_id);

                                        if ($done_payment_shipment->exists()) {
                                            $done_payment_shipment = $done_payment_shipment->latest()->first();

                                            $this->adjust_payment($done_payment_shipment->done_payment_id, $shipment_id, 1, 2);
                                        }
                                    }
                                } else {
                                    $payment_shipment_id = NULL;
                                    $payment_type = NULL;
                                    $invoice_shipment_id = NULL;
                                    $invoice_type = NULL;

                                    $pending_payment_shipment = PendingPaymentShipment::where('shipment_id', $shipment_id);

                                    if ($pending_payment_shipment->exists()) {
                                        $pending_payment_shipment = $pending_payment_shipment->latest()->first();

                                        $payment_shipment_id = $pending_payment_shipment->id;
                                        $payment_type = 0;
                                    } else {
                                        $done_payment_shipment = DonePaymentShipment::where('shipment_id', $shipment_id);

                                        if ($done_payment_shipment->exists()) {
                                            $done_payment_shipment = $done_payment_shipment->latest()->first();

                                            $payment_shipment_id = $done_payment_shipment->id;
                                            $payment_type = 1;
                                            $done_payment_id = $done_payment_shipment->done_payment_id;
                                        }
                                    }

                                    $pending_invoice_shipment = PendingInvoiceShipment::where('shipment_id', $shipment_id);

                                    if ($pending_invoice_shipment->exists()) {
                                        $pending_invoice_shipment = $pending_invoice_shipment->latest()->first();

                                        $invoice_shipment_id = $pending_invoice_shipment->id;
                                        $invoice_type = 0;
                                    } else {
                                        $done_invoice_shipment = InvoiceShipment::where('shipment_id', $shipment_id);

                                        if ($done_invoice_shipment->exists()) {
                                            $done_invoice_shipment = $done_invoice_shipment->latest()->first();

                                            $invoice_shipment_id = $done_invoice_shipment->id;
                                            $invoice_type = 1;
                                        }
                                    }

                                    if ($payment_shipment_id != NULL || $invoice_shipment_id != NULL) {
                                        self::adjust_invoice($shipment->id, $payment_shipment_id, $payment_type, $invoice_shipment_id, $invoice_type, 2);
                                    }
                                }

                                if (isset($payment_type) && $payment_type == 1) {
                                    ShipmentsPaymentJourneyController::add($shipment_id, 4, Auth::id(), '', $done_payment_id);
                                } else {
                                    ShipmentsPaymentJourneyController::add($shipment_id, 4, Auth::id());
                                }

                                ShipmentsJourneyController::add($shipment_id, 13, 13, NULL, "Shipment Reverted", NULL, Auth::id());

                                if ($shipment->packaging_material_request == 1) {
                                    $packaging_material_shipment = PackagingMaterialRequest::where('tracking_number', $shipment->tracking_number)->first();
                                    if ($packaging_material_shipment != null) {
                                        $packaging_material_shipment->status_id = 3;
                                        $packaging_material_shipment->save();

                                        $packaging_request_history = new PackagingMaterialRequestHistory();
                                        $packaging_request_history->packaging_material_request_id = $packaging_material_shipment->id;
                                        $packaging_request_history->status = 3;
                                        $packaging_request_history->updated_by = \Illuminate\Support\Facades\Auth::id();
                                        $packaging_request_history->save();
                                    }
                                }

                                NotificationsController::send(21, $shipment_id, Auth::id());
                            }
                        }
                    } elseif ($shipment->shipment_type == 2) {
                        $journey = ShipmentsJourney::where('shipment_id', $shipment->id)->latest('id')->first();
                        if ($journey) {
                            $start = $journey->created_at;
                            $difference = $start->diffInDays($now);
                            if ($difference <= 2 || (session('role_id') == 1 || in_array(346, session('permissions')))) {

                                $delivery_note_shipment->status = 8;

                                $delivery_note_shipment->save();

                                $delivery_note = $delivery_note_shipment->delivery_note;

                                $delivery_note_amount = $delivery_note->received_cod_amount - $shipment->amount;

                                if ($delivery_note_amount < 0) {
                                    $delivery_note_amount = 0;
                                }

                                $delivery_note->delivered_shipments = $delivery_note->delivered_shipments - 1;
                                $delivery_note->received_cod_amount = $delivery_note_amount;

                                $delivery_note->save();

                                $delivery_note_station_deposit_note = $delivery_note->delivery_note_station_deposit_note;

                                if ($delivery_note_station_deposit_note) {
                                    $station_deposit_note = $delivery_note_station_deposit_note->station_deposit_note;

                                    $station_deposit_note_amount = $station_deposit_note->sdn_amount - $shipment->amount;

                                    if ($station_deposit_note_amount < 0) {
                                        $station_deposit_note_amount = 0;
                                    }

                                    $station_deposit_note->sdn_delivered_shipments = $station_deposit_note->sdn_delivered_shipments - 1;
                                    $station_deposit_note->sdn_amount = $station_deposit_note_amount;
                                    $station_deposit_note->sdn_net_amount = $station_deposit_note_amount;

                                    $station_deposit_note->save();
                                }

                                $shipment->shipper_status_id = 13;
                                $shipment->consignee_status_id = 13;

                                $shipment->payment_status_id = 4;

                                $shipment->save();

                                if ($shipment->booking_type_id == 3) {
                                    ShipmentItem::where('shipment_id', $shipment->id)->update(['bought' => 0]);
                                }

                                // $account_type_id = $shipment->user->account_type_id;
                                // if ($account_type_id == 1) {
                                //     $pending_payment_shipment = RetailPendingPaymentShipment::where('shipment_id', $shipment_id);
                                //     if ($pending_payment_shipment->exists()) {
                                //         $pending_payment_shipment = $pending_payment_shipment->latest()->first();
                                //         $this->adjust_payment($pending_payment_shipment->retail_pending_payment_id, $shipment_id, 0, 2);
                                //     }
                                //     else {
                                //         $done_payment_shipment = RetailDonePaymentShipment::where('shipment_id', $shipment_id);
                                //         if ($done_payment_shipment->exists()) {
                                //             $done_payment_shipment = $done_payment_shipment->latest()->first();
                                //             $this->adjust_payment($done_payment_shipment->retail_done_payment_id, $shipment_id, 1, 2);
                                //         }
                                //     }
                                // }
                                // else {
                                $payment_shipment_id = NULL;
                                $payment_type = NULL;
                                $invoice_shipment_id = NULL;
                                $invoice_type = NULL;

                                $pending_payment_shipment = RetailPendingPaymentShipment::where('shipment_id', $shipment_id);

                                if ($pending_payment_shipment->exists()) {
                                    $pending_payment_shipment = $pending_payment_shipment->latest()->first();

                                    $payment_shipment_id = $pending_payment_shipment->id;
                                    $payment_type = 0;
                                } else {
                                    $done_payment_shipment = RetailDonePaymentShipment::where('shipment_id', $shipment_id);

                                    if ($done_payment_shipment->exists()) {
                                        $done_payment_shipment = $done_payment_shipment->latest()->first();

                                        $payment_shipment_id = $done_payment_shipment->id;
                                        $payment_type = 1;
                                        $done_payment_id = $done_payment_shipment->retail_done_payment_id;
                                    }
                                }
                                //check for retail
                                $pending_invoice_shipment = PendingInvoiceShipment::where('shipment_id', $shipment_id);

                                if ($pending_invoice_shipment->exists()) {
                                    $pending_invoice_shipment = $pending_invoice_shipment->latest()->first();

                                    $invoice_shipment_id = $pending_invoice_shipment->id;
                                    $invoice_type = 0;
                                } else {
                                    $done_invoice_shipment = InvoiceShipment::where('shipment_id', $shipment_id);

                                    if ($done_invoice_shipment->exists()) {
                                        $done_invoice_shipment = $done_invoice_shipment->latest()->first();

                                        $invoice_shipment_id = $done_invoice_shipment->id;
                                        $invoice_type = 1;
                                    }
                                }
                                //check for end

                                if ($payment_shipment_id != NULL || $invoice_shipment_id != NULL) {
                                    self::adjust_invoice($shipment->id, $payment_shipment_id, $payment_type, $invoice_shipment_id, $invoice_type, 2);
                                }
                                // }

                                if (isset($payment_type) && $payment_type == 1) {
                                    ShipmentsPaymentJourneyController::add($shipment_id, 4, Auth::id(), '', $done_payment_id, 1);
                                } else {
                                    ShipmentsPaymentJourneyController::add($shipment_id, 4, Auth::id(), '', NULL, 1);
                                }

                                ShipmentsJourneyController::add($shipment_id, 13, 13, NULL, "Shipment Reverted", NULL, Auth::id());

                                if ($shipment->packaging_material_request == 1) {
                                    $packaging_material_shipment = PackagingMaterialRequest::where('tracking_number', $shipment->tracking_number)->first();
                                    if ($packaging_material_shipment != null) {
                                        $packaging_material_shipment->status_id = 3;
                                        $packaging_material_shipment->save();

                                        $packaging_request_history = new PackagingMaterialRequestHistory();
                                        $packaging_request_history->packaging_material_request_id = $packaging_material_shipment->id;
                                        $packaging_request_history->status = 3;
                                        $packaging_request_history->updated_by = \Illuminate\Support\Facades\Auth::id();
                                        $packaging_request_history->save();
                                    }
                                }

                                NotificationsController::send(21, $shipment_id, Auth::id());
                            }
                        }
                    }
                }


            }

        }
        return ['status' => 0, 'success' => 'Shipments has been marked to be Adjusted in Payment'];


    }

    public function outstanding_shipments_adjust_in_payment(Request $request)
    {
        $now = Carbon::now()->startOfDay();
        $delivery_note_shipment = DeliveryNoteShipment::where('shipment_id', $request->id)->whereIn('status', [4, 5, 6, 11])->where('delivery_note_id', $request->dncc);

        if ($delivery_note_shipment->exists()) {
            $delivery_note_shipment = $delivery_note_shipment->first();

            $shipment = Shipment::find($request->id);
            $lost_shipment_shipper = LostShipmentShipper::where('user_id', $shipment->user_id);
            if ($lost_shipment_shipper->exists()) {
                $lost_shipments_admins = LostShipmentAdmin::where('admin_id', Auth::id());
                if ($lost_shipments_admins->exists()) {
                    if ($shipment->shipment_type == 1) {
                        $journey = ShipmentsJourney::where('shipment_id', $shipment->id)->latest('id')->first();
                        if ($journey) {
                            $start = $journey->created_at;
                            $difference = $start->diffInDays($now);
                            if ($difference <= 2 || (session('role_id') == 1 || in_array(346, session('permissions')))) {

                                $delivery_note_shipment->status = 8;

                                $delivery_note_shipment->save();

                                $delivery_note = $delivery_note_shipment->delivery_note;

                                $delivery_note_amount = $delivery_note->received_cod_amount - $shipment->amount;

                                if ($delivery_note_amount < 0) {
                                    $delivery_note_amount = 0;
                                }

                                $delivery_note->delivered_shipments = $delivery_note->delivered_shipments - 1;
                                $delivery_note->received_cod_amount = $delivery_note_amount;

                                $delivery_note->save();

                                $delivery_note_station_deposit_note = $delivery_note->delivery_note_station_deposit_note;

                                if ($delivery_note_station_deposit_note) {
                                    $station_deposit_note = $delivery_note_station_deposit_note->station_deposit_note;

                                    $station_deposit_note_amount = $station_deposit_note->sdn_amount - $shipment->amount;

                                    if ($station_deposit_note_amount < 0) {
                                        $station_deposit_note_amount = 0;
                                    }

                                    $station_deposit_note->sdn_delivered_shipments = $station_deposit_note->sdn_delivered_shipments - 1;
                                    $station_deposit_note->sdn_amount = $station_deposit_note_amount;
                                    $station_deposit_note->sdn_net_amount = $station_deposit_note_amount;

                                    $station_deposit_note->save();
                                }

                                $shipment->shipper_status_id = 13;
                                $shipment->consignee_status_id = 13;

                                $shipment->payment_status_id = 4;

                                $shipment->save();

                                if ($shipment->booking_type_id == 3) {
                                    ShipmentItem::where('shipment_id', $shipment->id)->update(['bought' => 0]);
                                }

                                $account_type_id = $shipment->user->account_type_id;

                                if ($account_type_id == 1) {
                                    $pending_payment_shipment = PendingPaymentShipment::where('shipment_id', $request->id);

                                    if ($pending_payment_shipment->exists()) {
                                        $pending_payment_shipment = $pending_payment_shipment->latest()->first();

                                        $this->adjust_payment($pending_payment_shipment->pending_payment_id, $request->id, 0, 2);
                                    } else {
                                        $done_payment_shipment = DonePaymentShipment::where('shipment_id', $request->id);

                                        if ($done_payment_shipment->exists()) {
                                            $done_payment_shipment = $done_payment_shipment->latest()->first();

                                            $this->adjust_payment($done_payment_shipment->done_payment_id, $request->id, 1, 2);
                                        }
                                    }
                                } else {
                                    $payment_shipment_id = NULL;
                                    $payment_type = NULL;
                                    $invoice_shipment_id = NULL;
                                    $invoice_type = NULL;

                                    $pending_payment_shipment = PendingPaymentShipment::where('shipment_id', $shipment->id);

                                    if ($pending_payment_shipment->exists()) {
                                        $pending_payment_shipment = $pending_payment_shipment->latest()->first();

                                        $payment_shipment_id = $pending_payment_shipment->id;
                                        $payment_type = 0;
                                    } else {
                                        $done_payment_shipment = DonePaymentShipment::where('shipment_id', $shipment->id);

                                        if ($done_payment_shipment->exists()) {
                                            $done_payment_shipment = $done_payment_shipment->latest()->first();

                                            $payment_shipment_id = $done_payment_shipment->id;
                                            $payment_type = 1;
                                            $done_payment_id = $done_payment_shipment->done_payment_id;
                                        }
                                    }

                                    $pending_invoice_shipment = PendingInvoiceShipment::where('shipment_id', $shipment->id);

                                    if ($pending_invoice_shipment->exists()) {
                                        $pending_invoice_shipment = $pending_invoice_shipment->latest()->first();

                                        $invoice_shipment_id = $pending_invoice_shipment->id;
                                        $invoice_type = 0;
                                    } else {
                                        $done_invoice_shipment = InvoiceShipment::where('shipment_id', $shipment->id);

                                        if ($done_invoice_shipment->exists()) {
                                            $done_invoice_shipment = $done_invoice_shipment->latest()->first();

                                            $invoice_shipment_id = $done_invoice_shipment->id;
                                            $invoice_type = 1;
                                        }
                                    }

                                    if ($payment_shipment_id != NULL || $invoice_shipment_id != NULL) {
                                        self::adjust_invoice($shipment->id, $payment_shipment_id, $payment_type, $invoice_shipment_id, $invoice_type, 2);
                                    }
                                }

                                if (isset($payment_type) && $payment_type == 1) {
                                    ShipmentsPaymentJourneyController::add($request->id, 4, Auth::id(), '', $done_payment_id);
                                } else {
                                    ShipmentsPaymentJourneyController::add($request->id, 4, Auth::id());
                                }

                                ShipmentsJourneyController::add($request->id, 13, 13, NULL, NULL, NULL, Auth::id());

                                if ($shipment->packaging_material_request == 1) {
                                    $packaging_material_shipment = PackagingMaterialRequest::where('tracking_number', $shipment->tracking_number)->first();
                                    if ($packaging_material_shipment != null) {
                                        $packaging_material_shipment->status_id = 3;
                                        $packaging_material_shipment->save();

                                        $packaging_request_history = new PackagingMaterialRequestHistory();
                                        $packaging_request_history->packaging_material_request_id = $packaging_material_shipment->id;
                                        $packaging_request_history->status = 3;
                                        $packaging_request_history->updated_by = \Illuminate\Support\Facades\Auth::id();
                                        $packaging_request_history->save();
                                    }
                                }

                                NotificationsController::send(21, $request->id, Auth::id());

                                return ['status' => 0, 'success' => 'Shipment has been marked to be Adjusted in Payment'];

                            } else {
                                return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has not been modified because it reaches the 6 days'];
                            }
                        }
                    } elseif ($shipment->shipment_type == 2) {
                        $journey = ShipmentsJourney::where('shipment_id', $shipment->id)->latest('id')->first();
                        if ($journey) {
                            $start = $journey->created_at;
                            $difference = $start->diffInDays($now);
                            if ($difference <= 2 || (session('role_id') == 1 || in_array(346, session('permissions')))) {

                                $delivery_note_shipment->status = 8;

                                $delivery_note_shipment->save();

                                $delivery_note = $delivery_note_shipment->delivery_note;

                                $delivery_note_amount = $delivery_note->received_cod_amount - $shipment->amount;

                                if ($delivery_note_amount < 0) {
                                    $delivery_note_amount = 0;
                                }

                                $delivery_note->delivered_shipments = $delivery_note->delivered_shipments - 1;
                                $delivery_note->received_cod_amount = $delivery_note_amount;

                                $delivery_note->save();

                                $delivery_note_station_deposit_note = $delivery_note->delivery_note_station_deposit_note;

                                if ($delivery_note_station_deposit_note) {
                                    $station_deposit_note = $delivery_note_station_deposit_note->station_deposit_note;

                                    $station_deposit_note_amount = $station_deposit_note->sdn_amount - $shipment->amount;

                                    if ($station_deposit_note_amount < 0) {
                                        $station_deposit_note_amount = 0;
                                    }

                                    $station_deposit_note->sdn_delivered_shipments = $station_deposit_note->sdn_delivered_shipments - 1;
                                    $station_deposit_note->sdn_amount = $station_deposit_note_amount;
                                    $station_deposit_note->sdn_net_amount = $station_deposit_note_amount;

                                    $station_deposit_note->save();
                                }

                                $shipment->shipper_status_id = 13;
                                $shipment->consignee_status_id = 13;

                                $shipment->payment_status_id = 4;

                                $shipment->save();

                                if ($shipment->booking_type_id == 3) {
                                    ShipmentItem::where('shipment_id', $shipment->id)->update(['bought' => 0]);
                                }

                                // $account_type_id = $shipment->user->account_type_id;
                                // if ($account_type_id == 1) {
                                //     $pending_payment_shipment = RetailPendingPaymentShipment::where('shipment_id', $request->id);
                                //     if ($pending_payment_shipment->exists()) {
                                //         $pending_payment_shipment = $pending_payment_shipment->latest()->first();
                                //         $this->adjust_payment($pending_payment_shipment->retail_pending_payment_id, $request->id, 0, 2);
                                //     }
                                //     else {
                                //         $done_payment_shipment = RetailDonePaymentShipment::where('shipment_id', $request->id);
                                //         if ($done_payment_shipment->exists()) {
                                //             $done_payment_shipment = $done_payment_shipment->latest()->first();
                                //             $this->adjust_payment($done_payment_shipment->retail_done_payment_id, $request->id, 1, 2);
                                //         }
                                //     }
                                // }
                                // else {
                                $payment_shipment_id = NULL;
                                $payment_type = NULL;
                                $invoice_shipment_id = NULL;
                                $invoice_type = NULL;

                                $pending_payment_shipment = RetailPendingPaymentShipment::where('shipment_id', $shipment->id);

                                if ($pending_payment_shipment->exists()) {
                                    $pending_payment_shipment = $pending_payment_shipment->latest()->first();

                                    $payment_shipment_id = $pending_payment_shipment->id;
                                    $payment_type = 0;
                                } else {
                                    $done_payment_shipment = RetailDonePaymentShipment::where('shipment_id', $shipment->id);

                                    if ($done_payment_shipment->exists()) {
                                        $done_payment_shipment = $done_payment_shipment->latest()->first();

                                        $payment_shipment_id = $done_payment_shipment->id;
                                        $payment_type = 1;
                                        $done_payment_id = $done_payment_shipment->retail_done_payment_id;
                                    }
                                }
                                //check for retail
                                $pending_invoice_shipment = PendingInvoiceShipment::where('shipment_id', $shipment->id);

                                if ($pending_invoice_shipment->exists()) {
                                    $pending_invoice_shipment = $pending_invoice_shipment->latest()->first();

                                    $invoice_shipment_id = $pending_invoice_shipment->id;
                                    $invoice_type = 0;
                                } else {
                                    $done_invoice_shipment = InvoiceShipment::where('shipment_id', $shipment->id);

                                    if ($done_invoice_shipment->exists()) {
                                        $done_invoice_shipment = $done_invoice_shipment->latest()->first();

                                        $invoice_shipment_id = $done_invoice_shipment->id;
                                        $invoice_type = 1;
                                    }
                                }
                                //check for retail end
                                if ($payment_shipment_id != NULL || $invoice_shipment_id != NULL) {
                                    self::adjust_invoice($shipment->id, $payment_shipment_id, $payment_type, $invoice_shipment_id, $invoice_type, 2);
                                }
                                // }

                                if (isset($payment_type) && $payment_type == 1) {
                                    ShipmentsPaymentJourneyController::add($request->id, 4, Auth::id(), '', $done_payment_id, 1);
                                } else {
                                    ShipmentsPaymentJourneyController::add($request->id, 4, Auth::id(), '', NULL, 1);
                                }

                                ShipmentsJourneyController::add($request->id, 13, 13, NULL, "Shipment Reverted", NULL, Auth::id());

                                if ($shipment->packaging_material_request == 1) {
                                    $packaging_material_shipment = PackagingMaterialRequest::where('tracking_number', $shipment->tracking_number)->first();
                                    if ($packaging_material_shipment != null) {
                                        $packaging_material_shipment->status_id = 3;
                                        $packaging_material_shipment->save();

                                        $packaging_request_history = new PackagingMaterialRequestHistory();
                                        $packaging_request_history->packaging_material_request_id = $packaging_material_shipment->id;
                                        $packaging_request_history->status = 3;
                                        $packaging_request_history->updated_by = \Illuminate\Support\Facades\Auth::id();
                                        $packaging_request_history->save();
                                    }
                                }

                                NotificationsController::send(21, $request->id, Auth::id());

                                return ['status' => 0, 'success' => 'Shipment has been marked to be Adjusted in Payment'];

                            } else {
                                return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has not been modified because it reaches the 6 days'];
                            }
                        }
                    } else {
                        return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment is of retail it can not be reverted'];

                    }
                }

            } else {
                if ($shipment->shipment_type == 1) {
                    $journey = ShipmentsJourney::where('shipment_id', $shipment->id)->latest('id')->first();
                    if ($journey) {
                        $start = $journey->created_at;
                        $difference = $start->diffInDays($now);
                        if ($difference <= 2 || (session('role_id') == 1 || in_array(346, session('permissions')))) {

                            $delivery_note_shipment->status = 8;

                            $delivery_note_shipment->save();

                            $delivery_note = $delivery_note_shipment->delivery_note;

                            $delivery_note_amount = $delivery_note->received_cod_amount - $shipment->amount;

                            if ($delivery_note_amount < 0) {
                                $delivery_note_amount = 0;
                            }

                            $delivery_note->delivered_shipments = $delivery_note->delivered_shipments - 1;
                            $delivery_note->received_cod_amount = $delivery_note_amount;

                            $delivery_note->save();

                            $delivery_note_station_deposit_note = $delivery_note->delivery_note_station_deposit_note;

                            if ($delivery_note_station_deposit_note) {
                                $station_deposit_note = $delivery_note_station_deposit_note->station_deposit_note;

                                $station_deposit_note_amount = $station_deposit_note->sdn_amount - $shipment->amount;

                                if ($station_deposit_note_amount < 0) {
                                    $station_deposit_note_amount = 0;
                                }

                                $station_deposit_note->sdn_delivered_shipments = $station_deposit_note->sdn_delivered_shipments - 1;
                                $station_deposit_note->sdn_amount = $station_deposit_note_amount;
                                $station_deposit_note->sdn_net_amount = $station_deposit_note_amount;

                                $station_deposit_note->save();
                            }

                            $shipment->shipper_status_id = 13;
                            $shipment->consignee_status_id = 13;

                            $shipment->payment_status_id = 4;

                            $shipment->save();

                            if ($shipment->booking_type_id == 3) {
                                ShipmentItem::where('shipment_id', $shipment->id)->update(['bought' => 0]);
                            }

                            $account_type_id = $shipment->user->account_type_id;

                            if ($account_type_id == 1) {
                                $pending_payment_shipment = PendingPaymentShipment::where('shipment_id', $request->id);

                                if ($pending_payment_shipment->exists()) {
                                    $pending_payment_shipment = $pending_payment_shipment->latest()->first();

                                    $this->adjust_payment($pending_payment_shipment->pending_payment_id, $request->id, 0, 2);
                                } else {
                                    $done_payment_shipment = DonePaymentShipment::where('shipment_id', $request->id);

                                    if ($done_payment_shipment->exists()) {
                                        $done_payment_shipment = $done_payment_shipment->latest()->first();

                                        $this->adjust_payment($done_payment_shipment->done_payment_id, $request->id, 1, 2);
                                    }
                                }
                            } else {
                                $payment_shipment_id = NULL;
                                $payment_type = NULL;
                                $invoice_shipment_id = NULL;
                                $invoice_type = NULL;

                                $pending_payment_shipment = PendingPaymentShipment::where('shipment_id', $shipment->id);

                                if ($pending_payment_shipment->exists()) {
                                    $pending_payment_shipment = $pending_payment_shipment->latest()->first();

                                    $payment_shipment_id = $pending_payment_shipment->id;
                                    $payment_type = 0;
                                } else {
                                    $done_payment_shipment = DonePaymentShipment::where('shipment_id', $shipment->id);

                                    if ($done_payment_shipment->exists()) {
                                        $done_payment_shipment = $done_payment_shipment->latest()->first();

                                        $payment_shipment_id = $done_payment_shipment->id;
                                        $payment_type = 1;
                                        $done_payment_id = $done_payment_shipment->done_payment_id;
                                    }
                                }

                                $pending_invoice_shipment = PendingInvoiceShipment::where('shipment_id', $shipment->id);

                                if ($pending_invoice_shipment->exists()) {
                                    $pending_invoice_shipment = $pending_invoice_shipment->latest()->first();

                                    $invoice_shipment_id = $pending_invoice_shipment->id;
                                    $invoice_type = 0;
                                } else {
                                    $done_invoice_shipment = InvoiceShipment::where('shipment_id', $shipment->id);

                                    if ($done_invoice_shipment->exists()) {
                                        $done_invoice_shipment = $done_invoice_shipment->latest()->first();

                                        $invoice_shipment_id = $done_invoice_shipment->id;
                                        $invoice_type = 1;
                                    }
                                }

                                if ($payment_shipment_id != NULL || $invoice_shipment_id != NULL) {
                                    self::adjust_invoice($shipment->id, $payment_shipment_id, $payment_type, $invoice_shipment_id, $invoice_type, 2);
                                }
                            }

                            if (isset($payment_type) && $payment_type == 1) {
                                ShipmentsPaymentJourneyController::add($request->id, 4, Auth::id(), '', $done_payment_id);
                            } else {
                                ShipmentsPaymentJourneyController::add($request->id, 4, Auth::id());
                            }

                            ShipmentsJourneyController::add($request->id, 13, 13, NULL, NULL, NULL, Auth::id());

                            if ($shipment->packaging_material_request == 1) {
                                $packaging_material_shipment = PackagingMaterialRequest::where('tracking_number', $shipment->tracking_number)->first();
                                if ($packaging_material_shipment != null) {
                                    $packaging_material_shipment->status_id = 3;
                                    $packaging_material_shipment->save();

                                    $packaging_request_history = new PackagingMaterialRequestHistory();
                                    $packaging_request_history->packaging_material_request_id = $packaging_material_shipment->id;
                                    $packaging_request_history->status = 3;
                                    $packaging_request_history->updated_by = \Illuminate\Support\Facades\Auth::id();
                                    $packaging_request_history->save();
                                }
                            }

                            NotificationsController::send(21, $request->id, Auth::id());

                            return ['status' => 0, 'success' => 'Shipment has been marked to be Adjusted in Payment'];

                        } else {
                            return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has not been modified because it reaches the 6 days'];
                        }
                    }
                } elseif ($shipment->shipment_type == 2) {
                    $journey = ShipmentsJourney::where('shipment_id', $shipment->id)->latest('id')->first();
                    if ($journey) {
                        $start = $journey->created_at;
                        $difference = $start->diffInDays($now);
                        if ($difference <= 2 || (session('role_id') == 1 || in_array(346, session('permissions')))) {

                            $delivery_note_shipment->status = 8;

                            $delivery_note_shipment->save();

                            $delivery_note = $delivery_note_shipment->delivery_note;

                            $delivery_note_amount = $delivery_note->received_cod_amount - $shipment->amount;

                            if ($delivery_note_amount < 0) {
                                $delivery_note_amount = 0;
                            }

                            $delivery_note->delivered_shipments = $delivery_note->delivered_shipments - 1;
                            $delivery_note->received_cod_amount = $delivery_note_amount;

                            $delivery_note->save();

                            $delivery_note_station_deposit_note = $delivery_note->delivery_note_station_deposit_note;

                            if ($delivery_note_station_deposit_note) {
                                $station_deposit_note = $delivery_note_station_deposit_note->station_deposit_note;

                                $station_deposit_note_amount = $station_deposit_note->sdn_amount - $shipment->amount;

                                if ($station_deposit_note_amount < 0) {
                                    $station_deposit_note_amount = 0;
                                }

                                $station_deposit_note->sdn_delivered_shipments = $station_deposit_note->sdn_delivered_shipments - 1;
                                $station_deposit_note->sdn_amount = $station_deposit_note_amount;
                                $station_deposit_note->sdn_net_amount = $station_deposit_note_amount;

                                $station_deposit_note->save();
                            }

                            $shipment->shipper_status_id = 13;
                            $shipment->consignee_status_id = 13;

                            $shipment->payment_status_id = 4;

                            $shipment->save();

                            if ($shipment->booking_type_id == 3) {
                                ShipmentItem::where('shipment_id', $shipment->id)->update(['bought' => 0]);
                            }

                            // $account_type_id = $shipment->user->account_type_id;
                            // if ($account_type_id == 1) {
                            //     $pending_payment_shipment = RetailPendingPaymentShipment::where('shipment_id', $request->id);
                            //     if ($pending_payment_shipment->exists()) {
                            //         $pending_payment_shipment = $pending_payment_shipment->latest()->first();
                            //         $this->adjust_payment($pending_payment_shipment->retail_pending_payment_id, $request->id, 0, 2);
                            //     }
                            //     else {
                            //         $done_payment_shipment = RetailDonePaymentShipment::where('shipment_id', $request->id);
                            //         if ($done_payment_shipment->exists()) {
                            //             $done_payment_shipment = $done_payment_shipment->latest()->first();
                            //             $this->adjust_payment($done_payment_shipment->retail_done_payment_id, $request->id, 1, 2);
                            //         }
                            //     }
                            // }
                            // else {
                            $payment_shipment_id = NULL;
                            $payment_type = NULL;
                            $invoice_shipment_id = NULL;
                            $invoice_type = NULL;

                            $pending_payment_shipment = RetailPendingPaymentShipment::where('shipment_id', $shipment->id);

                            if ($pending_payment_shipment->exists()) {
                                $pending_payment_shipment = $pending_payment_shipment->latest()->first();

                                $payment_shipment_id = $pending_payment_shipment->id;
                                $payment_type = 0;
                            } else {
                                $done_payment_shipment = RetailDonePaymentShipment::where('shipment_id', $shipment->id);

                                if ($done_payment_shipment->exists()) {
                                    $done_payment_shipment = $done_payment_shipment->latest()->first();

                                    $payment_shipment_id = $done_payment_shipment->id;
                                    $payment_type = 1;
                                    $done_payment_id = $done_payment_shipment->retail_done_payment_id;
                                }
                            }
                            //check for retail
                            $pending_invoice_shipment = PendingInvoiceShipment::where('shipment_id', $shipment->id);

                            if ($pending_invoice_shipment->exists()) {
                                $pending_invoice_shipment = $pending_invoice_shipment->latest()->first();

                                $invoice_shipment_id = $pending_invoice_shipment->id;
                                $invoice_type = 0;
                            } else {
                                $done_invoice_shipment = InvoiceShipment::where('shipment_id', $shipment->id);

                                if ($done_invoice_shipment->exists()) {
                                    $done_invoice_shipment = $done_invoice_shipment->latest()->first();

                                    $invoice_shipment_id = $done_invoice_shipment->id;
                                    $invoice_type = 1;
                                }
                            }
                            //check for retail end
                            if ($payment_shipment_id != NULL || $invoice_shipment_id != NULL) {
                                self::adjust_invoice($shipment->id, $payment_shipment_id, $payment_type, $invoice_shipment_id, $invoice_type, 2);
                            }
                            // }

                            if (isset($payment_type) && $payment_type == 1) {
                                ShipmentsPaymentJourneyController::add($request->id, 4, Auth::id(), '', $done_payment_id, 1);
                            } else {
                                ShipmentsPaymentJourneyController::add($request->id, 4, Auth::id(), '', NULL, 1);
                            }

                            ShipmentsJourneyController::add($request->id, 13, 13, NULL, NULL, NULL, Auth::id());

                            if ($shipment->packaging_material_request == 1) {
                                $packaging_material_shipment = PackagingMaterialRequest::where('tracking_number', $shipment->tracking_number)->first();
                                if ($packaging_material_shipment != null) {
                                    $packaging_material_shipment->status_id = 3;
                                    $packaging_material_shipment->save();

                                    $packaging_request_history = new PackagingMaterialRequestHistory();
                                    $packaging_request_history->packaging_material_request_id = $packaging_material_shipment->id;
                                    $packaging_request_history->status = 3;
                                    $packaging_request_history->updated_by = \Illuminate\Support\Facades\Auth::id();
                                    $packaging_request_history->save();
                                }
                            }

                            NotificationsController::send(21, $request->id, Auth::id());

                            return ['status' => 0, 'success' => 'Shipment has been marked to be Adjusted in Payment'];

                        } else {
                            return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has not been modified because it reaches the 6 days'];
                        }
                    }
                } else {
                    return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment is of retail it can not be reverted'];

                }
            }


        } else {
            return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been modified'];
        }
    }

    public function change_shipment_amount_index()
    {
        return view('admin.finance.change_shipment_amount');
    }

    public function change_shipment_amount_shipment_details(Request $request)
    {
        $shipment = Shipment::where('tracking_number', $request->tracking_number);

        if ($shipment->exists()) {
            $shipment = $shipment->first();
            $retail_shipment = RetailShipment::where('shipment_id', $shipment->id);
            if ($retail_shipment->exists()) {
                $retail_shipment = $retail_shipment->first();
                if ($retail_shipment->shipping_mode != 3) {
                    return ['status' => 1, 'error' => 'Retail Shipment amount can\'t be changed!'];
                }
            }

            if ($shipment->booking_type_id == 3) {
                return ['status' => 1, 'error' => 'Try & Buy shipment\'s amount can\'t be changed!'];
            }

            if (!in_array($shipment->shipper_status_id, [14, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 44, 45, 46])) {
                $pending_payment_shipment = PendingPaymentShipment::where('shipment_id', $shipment->id);

                if (!$pending_payment_shipment->exists()) {
                    $done_payment_shipment = DonePaymentShipment::where('shipment_id', $shipment->id);

                    if (!$done_payment_shipment->exists()) {
                        $account_type_id = $shipment->user->account_type_id;

                        if ($account_type_id == 2) {
                            $pending_invoice_shipment = PendingInvoiceShipment::where('shipment_id', $shipment->id);

                            if ($pending_invoice_shipment->exists()) {
                                return ['status' => 1, 'error' => 'A Invoice Payment of given Shipment is in Pending'];
                            } else {
                                $invoice_shipment = InvoiceShipment::where('shipment_id', $shipment->id);

                                if ($invoice_shipment->exists()) {
                                    return ['status' => 1, 'error' => 'A Invoice Payment of given Shipment has already been Processed'];
                                }
                            }
                        }

                        $details = array();

                        $shipper = $shipment->user;

                        $details['id'] = $shipment->id;

                        $details['tracking_number'] = $shipment->tracking_number;
                        $details['status'] = $shipment->status_shipper->name;

                        $details['service_type'] = $shipment->booking_type->booking_type;
                        $details['shipping_mode'] = $shipment->shipping_mode->mode;
                        $details['weight'] = ($shipment->actual_weight) ? floatval($shipment->actual_weight) : floatval($shipment->estimated_weight);

                        $details['payment_mode'] = $shipment->payment_mode->mode;
                        $details['amount'] = number_format($shipment->amount);

                        $details['shipper']['name'] = $shipper->name;
                        $details['shipper']['account_number'] = str_pad($shipper->id, 6, '0', STR_PAD_LEFT);
                        $details['shipper']['phone_number_1'] = $shipper->phone;
                        $details['shipper']['phone_number_2'] = $shipper->phone2;
                        $details['shipper']['origin'] = $shipper->city->name;
                        $details['shipper']['address'] = $shipper->address;

                        $details['consignee']['name'] = $shipment->consignee_name;
                        $details['consignee']['phone_number_1'] = $shipment->consignee_phone_number_1;
                        $details['consignee']['phone_number_2'] = $shipment->consignee_phone_number_2;
                        $details['consignee']['destination'] = $shipment->consignee_city->name;
                        $details['consignee']['address'] = $shipment->consignee_address;

                        ShipmentScanningJourneyController::add($shipment->id, 13, 1, Auth::id(), null, null);
                        return ['status' => 0, 'success' => 'Shipment\'s amount can be changed', 'details' => $details];
                    } else {
                        return ['status' => 1, 'error' => 'A Payment of given Shipment has already been Processed'];
                    }
                } else {
                    return ['status' => 1, 'error' => 'A Payment of given Shipment is in Pending'];
                }
            } else {
                return ['status' => 1, 'error' => 'Shipment has already been Delivered'];
            }
        } else {
            return ['status' => 1, 'error' => 'No Shipment exists with given Tracking Number'];
        }
    }

    public function change_shipment_amount_store(Request $request)
    {
        $shipment_id = $request->input('shipment_id');
        $amount = str_replace(',', '', $request->input('amount'));

        $shipment = Shipment::find($shipment_id);

        $change_shipment_amount = new ChangeShipmentAmountLog();

        $change_shipment_amount->shipment_id = $shipment->id;
        $change_shipment_amount->old_amount = $shipment->amount;
        $change_shipment_amount->new_amount = $amount;
        $change_shipment_amount->admin_id = Auth::id();
        $change_shipment_amount->remarks = $request->remarks;
        $change_shipment_amount->save();

        $shipment->amount = $amount;

        $shipment->save();

        ShipmentChargesController::cash_handling($shipment_id);

        return redirect()->route('admin.finance.change_shipment_amount.index')->with('success', 'Shipment\'s amount has been changed');
    }

    public function change_shipment_weight_index()
    {
        return view('admin.finance.change_shipment_weight');
    }

    public function change_shipment_weight_shipment_details(Request $request)
    {
        $shipment = Shipment::where('tracking_number', $request->tracking_number);

        if ($shipment->exists()) {
            $shipment = $shipment->first();
//            [14, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 44, 45, 46]
            if (!in_array($shipment->shipper_status_id, [32, 33, 34, 35, 36, 37, 38, 46])) {
                $message = '';
                $pending_payment_shipment = PendingPaymentShipment::where('shipment_id', $shipment->id);

                if ($pending_payment_shipment->exists()) {
                    $message = 'Shipment\'s payment is pending or already processed';
                } else {
                    $done_payment_shipment = DonePaymentShipment::where('shipment_id', $shipment->id);
                    if ($done_payment_shipment->exists()) {
                        $message = 'Shipment\'s payment is pending or already processed';
                    } else {
                        $account_type_id = $shipment->user->account_type_id;
                        if ($account_type_id == 2) {
                            $pending_invoice_shipment = PendingInvoiceShipment::where('shipment_id', $shipment->id);
                            if ($pending_invoice_shipment->exists()) {
                                $message = 'A Invoice Payment of given Shipment is in Pending';
                            } else {
                                $invoice_shipment = InvoiceShipment::where('shipment_id', $shipment->id);

                                if ($invoice_shipment->exists()) {
                                    $message = 'A Invoice Payment of given Shipment has already been Processed';

                                }
                            }
                        }
                    }
                }

                $details = array();

                $shipper = $shipment->user;

                $details['id'] = $shipment->id;

                $details['tracking_number'] = $shipment->tracking_number;
                $details['status'] = $shipment->status_shipper->name;

                $details['booking_type_id'] = $shipment->booking_type_id;
                $details['service_type'] = $shipment->booking_type->booking_type;
                $details['shipping_mode'] = $shipment->shipping_mode->mode;
                $details['weight'] = ($shipment->actual_weight) ? floatval($shipment->actual_weight) : floatval($shipment->estimated_weight);

                $details['payment_mode'] = $shipment->payment_mode->mode;
                $details['amount'] = number_format($shipment->amount);

                $details['shipper']['name'] = $shipper->name;
                $details['shipper']['account_number'] = str_pad($shipper->id, 6, '0', STR_PAD_LEFT);
                $details['shipper']['phone_number_1'] = $shipper->phone;
                $details['shipper']['phone_number_2'] = $shipper->phone2;
                $details['shipper']['origin'] = $shipper->city->name;
                $details['shipper']['address'] = $shipper->address;

                $details['consignee']['name'] = $shipment->consignee_name;
                $details['consignee']['phone_number_1'] = $shipment->consignee_phone_number_1;
                $details['consignee']['phone_number_2'] = $shipment->consignee_phone_number_2;
                $details['consignee']['destination'] = $shipment->consignee_city->name;
                $details['consignee']['address'] = $shipment->consignee_address;

                if ($shipment->booking_type_id == 2) {
                    $details['items'] = $shipment->items;
                }

                ShipmentScanningJourneyController::add($shipment->id, 14, 1, Auth::id(), null, null);
                return ['status' => 0, 'success' => 'Shipment\'s weight can be changed', 'warning' => $message, 'details' => $details];
            } else {
                return ['status' => 1, 'error' => 'Shipment has already been Delivered'];
            }
        } else {
            return ['status' => 1, 'error' => 'No Shipment exists with given Tracking Number'];
        }
    }

    public function change_shipment_weight_calculate_amount(Request $request)
    {
        $shipment_id = $request->shipment_id;
        $weight = $request->weight;
        $shipment = Shipment::find($shipment_id);
        if ($shipment->business_category_id == 1) {
            $weight_result = ShipmentChargesController::calculate_weight($shipment->user->account_type_id, $shipment->user_id, $shipment->shipping_mode_id, $shipment->same_day_timing_id, $shipment->walk_in_delivery_type_id, $weight, $shipment->pickup_address->city_id, $shipment->pickup_address->city->zone_id, $shipment->consignee_city_id, $shipment->booking_type_id, $shipment->amount);
        } else {
            $international_rate = InternationalUserRate::where('user_id', $shipment->user_id);
            if ($international_rate->exists()) {
                $international_rate = $international_rate->first();
                $margin = $international_rate->margin;
                $zone_id = $shipment->consignee_city->zone_id;
                $international_zone = InternationalDhlZone::where('zone_id', $zone_id)->first();
                if ($international_zone) {
                    $weight_result = ShipmentChargesController::calculate_international_weight($margin, $weight, $international_zone->zone_name);
                }
            }
        }

        $fuel_result = ShipmentChargesController::calculate_fuel_surcharge($shipment->user->account_type_id, $shipment->user_id, $shipment->shipping_mode_id, $weight_result['weight_charges']);

        if ($fuel_result && $weight_result) {
            $new_charges = $weight_result['weight_charges'] + $shipment->cash_handling_charges + $shipment->insurance_charges + $shipment->return_charges + $fuel_result['fuel_surcharge'] + $shipment->replacement_charges + $shipment->try_and_buy_charges + $shipment->packaging_material_charges + $shipment->intercept_charges + $shipment->nsa_osa_charges + $shipment->packaging_charges;

            return response()->json(['status' => 1, 'new_charges' => $new_charges]);
        } else {
            return response()->json(['status' => 0]);
        }
    }

    public function change_shipment_weight_store(Request $request)
    {

        $shipment_id = $request->input('shipment_id');
        $weight = $request->input('weight');
        $replacement_weight = null;

        $shipment = Shipment::find($shipment_id);
        $previous_weight_charges = $shipment->weight_charges + $shipment->cash_handling_charges + $shipment->insurance_charges + $shipment->return_charges + $shipment->fuel_surcharge + $shipment->replacement_charges + $shipment->try_and_buy_charges + $shipment->packaging_material_charges + $shipment->intercept_charges + $shipment->nsa_osa_charges + $shipment->packaging_charges;

        if ($shipment->actual_weight == null) {
            return redirect()->route('admin.finance.change_shipment_weight.index')->with('error', 'Shipment is not arrived yet so weight can not be changed!');
        }
        if ($request->has('replacement_checkbox')) {
            $weight = $request->input('shipment_weight');
            if ($weight == null) {
                $weight = $shipment->actual_weight;
            }
            $replacement_weight = $request->input('replacement_weight');
        }
        $old_shipment_weight = $shipment->actual_weight;

        if ($request->has('replacement_checkbox')) {
            $shipment->actual_weight = $weight;
            $shipment->replacement_weight = $replacement_weight;
            $shipment->save();
        } else {
            $shipment->actual_weight = $weight;
            $shipment->save();
        }

        ShipmentChargesController::weight($shipment_id);
        ShipmentChargesController::fuel_surcharge($shipment_id);

        $shipment = Shipment::find($shipment_id);
        $new_weight_charges = $shipment->weight_charges + $shipment->cash_handling_charges + $shipment->insurance_charges + $shipment->return_charges + $shipment->fuel_surcharge + $shipment->replacement_charges + $shipment->try_and_buy_charges + $shipment->packaging_material_charges + $shipment->intercept_charges + $shipment->nsa_osa_charges + $shipment->packaging_charges;

        $change_shipment_weight = new ChangeShipmentWeightLog();

        $change_shipment_weight->shipment_id = $shipment->id;
        $change_shipment_weight->old_weight = $old_shipment_weight;
        $change_shipment_weight->new_weight = $weight;
        $change_shipment_weight->admin_id = Auth::id();
        $change_shipment_weight->old_charges = $previous_weight_charges;
        $change_shipment_weight->new_charges = $new_weight_charges;
        $change_shipment_weight->save();

        $adjustment_amount = $previous_weight_charges - $new_weight_charges;

        $pending_payment = PendingPaymentShipment::where('shipment_id', $shipment->id);

        if ($pending_payment->exists()) {
            $pending_payment = $pending_payment->first();

            $previous_gst = $pending_payment->gst;
            if ($shipment->business_category_id == 1) {
                $new_gst = ROUND(($new_weight_charges * self::gst($shipment->pickup_address->city->zone_id)), 2, PHP_ROUND_HALF_DOWN);
            } else {
                $new_gst = ROUND(($new_weight_charges * self::international_gst()), 2, PHP_ROUND_HALF_DOWN);
            }

            $adjustment_amount += $previous_gst - $new_gst;

            self::add_adjustment($shipment->id, $adjustment_amount, 'Change Shipment Weight Adjustment', 12, $new_weight_charges);
        } else {
            $done_payment = DonePaymentShipment::where('shipment_id', $shipment->id);
            if ($done_payment->exists()) {
                $done_payment = $done_payment->first();

                $previous_gst = $done_payment->gst;

                if ($shipment->business_category_id == 1) {
                    $new_gst = ROUND(($new_weight_charges * self::gst($shipment->pickup_address->city->zone_id)), 2, PHP_ROUND_HALF_DOWN);
                } else {
                    $new_gst = ROUND(($new_weight_charges * self::international_gst()), 2, PHP_ROUND_HALF_DOWN);
                }

                $adjustment_amount += $previous_gst - $new_gst;

                self::add_adjustment($shipment->id, $adjustment_amount, 'Change Shipment Weight Adjustment', 12, $new_weight_charges);
            }
        }

        return redirect()->route('admin.finance.change_shipment_weight.index')->with('success', 'Shipment\'s weight has been changed');
    }

    public function change_shipment_weight_excel_store(Request $request)
    {
        $names = [
            'tracking_number' => 'Tracking Number',
            'actual_weight' => 'Actual Weight',
        ];

        $messages = [
            'required' => ':attribute is Required.',
            'integer' => ':attribute must be an Integer.',
            'exists' => 'Given :attribute is Invalid / not ready for update.',
        ];
        $rules = [
            'tracking_number' => ['required', 'integer', Rule::exists('shipments', 'tracking_number')->where(function ($query) {
                $query->whereNotIn('shipper_status_id', [32, 33, 34, 35, 36, 37, 38, 46]);
            })],
            'actual_weight' => ['required', 'numeric', 'between:0.01,100000'],
        ];

        $fields = [0 => 'tracking_number', 1 => 'actual_weight'];


        if ($file = $request->file('shipments')) {
            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

            $header = ['Tracking Number', 'Actual Weight'];

            if (isset($spreadsheet)) {
                $header_correct = TRUE;

                foreach ($spreadsheet[0] as $index => $header_value) {
                    if ($index == 2) {
                    } elseif (!isset($header[$index]) || $header_value != $header[$index]) {
                        $header_correct = FALSE;
                        break;
                    }
                }

                if (!$header_correct) {
                    return redirect()->back()->with('error', 'Invalid Columns, Kindly follow the Template provided');
                } else {
                    unset($spreadsheet[0]);
                }
            }

            if (!empty($spreadsheet) || !isset($spreadsheet)) {
                $rows = array();
                foreach ($spreadsheet as $spreadsheet_row) {
                    $row = array();

                    foreach ($spreadsheet_row as $key => $value) {
                        $row[$fields[$key]] = $value;
                    }

                    $rows[] = $row;
                }

                unset($spreadsheet);
                $errors = array();
                $tracking_ids = array();
                $tracking_id_row = array();
                foreach ($rows as $key => $row) {
                    $row_id = $key + 2;

                    $validate = Validator::make($row, $rules, $messages);

                    $validate->setAttributeNames($names);

                    if ($validate->fails()) {
                        $errors['Row #' . $row_id] = $validate->errors()->all();
                    }
                    if (empty($errors['Row #' . $row_id])) {
                        if (!empty(trim($row['tracking_number']))) {
                            if (empty($tracking_ids)) {
                                $tracking_ids[] = $row['tracking_number'];
                                $tracking_id_row[$row['tracking_number']] = $row_id;
                            } else {
                                if (in_array($row['tracking_number'], $tracking_ids)) {
                                    $errors['Row #' . $row_id][] = 'Same Tracking Number as of Row #' . $tracking_id_row[$row['tracking_number']];
                                } else {
                                    $tracking_ids[] = $row['tracking_number'];
                                    $tracking_id_row[$row['tracking_number']] = $row_id;
                                }
                            }
                        }
                        if (!Shipment::where('tracking_number', $row['tracking_number'])->exists()) {
                            $errors['Row #' . $row_id][] = 'Shipment is already updated from Booked Status #' . $row['tracking_number'];
                        }
                        if (Shipment::where('tracking_number', $row['tracking_number'])->where('booking_type_id', 2)->exists()) {
                            $errors['Row #' . $row_id][] = 'Replacement shipment can not updated from excel #' . $row['tracking_number'];
                        }
                    }
                }
                if (empty($errors)) {
                    $tracking_numbers = array();
                    foreach ($rows as $key => $row) {
                        $row_id = $key + 2;
                        $tracking = trim($row['tracking_number']);
                        $weight = $row['actual_weight'];
                        $shipment = Shipment::where('tracking_number', $tracking)->first();
                        $shipment_id = $shipment->id;
                        $replacement_weight = null;

                        if ($shipment->booking_type_id == 2) {
                            continue;
                        }

                        $previous_weight_charges = $shipment->weight_charges + $shipment->cash_handling_charges + $shipment->insurance_charges + $shipment->return_charges + $shipment->fuel_surcharge + $shipment->replacement_charges + $shipment->try_and_buy_charges + $shipment->packaging_material_charges + $shipment->intercept_charges + $shipment->nsa_osa_charges + $shipment->packaging_charges;

                        if ($shipment->actual_weight == null) {
                            return redirect()->route('admin.finance.change_shipment_weight.index')->with('error', 'Shipment is not arrived yet so weight can not be changed!');
                        }

                        $old_shipment_weight = $shipment->actual_weight;


                        $shipment->actual_weight = $weight;
                        $shipment->save();


                        ShipmentChargesController::weight($shipment_id);
                        ShipmentChargesController::fuel_surcharge($shipment_id);

                        $shipment = $shipment->refresh();
                        $new_weight_charges = $shipment->weight_charges + $shipment->cash_handling_charges + $shipment->insurance_charges + $shipment->return_charges + $shipment->fuel_surcharge + $shipment->replacement_charges + $shipment->try_and_buy_charges + $shipment->packaging_material_charges + $shipment->intercept_charges + $shipment->nsa_osa_charges + $shipment->packaging_charges;

                        $change_shipment_weight = new ChangeShipmentWeightLog();

                        $change_shipment_weight->shipment_id = $shipment->id;
                        $change_shipment_weight->old_weight = $old_shipment_weight;
                        $change_shipment_weight->new_weight = $weight;
                        $change_shipment_weight->admin_id = Auth::id();
                        $change_shipment_weight->old_charges = $previous_weight_charges;
                        $change_shipment_weight->new_charges = $new_weight_charges;
                        $change_shipment_weight->save();

                        $adjustment_amount = $previous_weight_charges - $new_weight_charges;

                        $pending_payment = PendingPaymentShipment::where('shipment_id', $shipment->id);

                        if ($pending_payment->exists()) {
                            $pending_payment = $pending_payment->first();

                            $previous_gst = $pending_payment->gst;
                            if ($shipment->business_category_id == 1) {
                                $new_gst = ROUND(($new_weight_charges * self::gst($shipment->pickup_address->city->zone_id)), 2, PHP_ROUND_HALF_DOWN);
                            } else {
                                $new_gst = ROUND(($new_weight_charges * self::international_gst()), 2, PHP_ROUND_HALF_DOWN);
                            }

                            $adjustment_amount += $previous_gst - $new_gst;

                            self::add_adjustment($shipment->id, $adjustment_amount, 'Change Shipment Weight Adjustment', 12, $new_weight_charges);
                        } else {
                            $done_payment = DonePaymentShipment::where('shipment_id', $shipment->id);
                            if ($done_payment->exists()) {
                                $done_payment = $done_payment->first();

                                $previous_gst = $done_payment->gst;

                                if ($shipment->business_category_id == 1) {
                                    $new_gst = ROUND(($new_weight_charges * self::gst($shipment->pickup_address->city->zone_id)), 2, PHP_ROUND_HALF_DOWN);
                                } else {
                                    $new_gst = ROUND(($new_weight_charges * self::international_gst()), 2, PHP_ROUND_HALF_DOWN);
                                }

                                $adjustment_amount += $previous_gst - $new_gst;

                                self::add_adjustment($shipment->id, $adjustment_amount, 'Change Shipment Weight Adjustment', 12, $new_weight_charges);
                            }
                        }


                        $tracking_numbers['Row #' . $row_id] = $tracking;

                    }
                    $tracking_numbers = implode(' | ', array_map(function ($row, $tracking_number) {
                        return $row . ': ' . $tracking_number;
                    }, array_keys($tracking_numbers), $tracking_numbers));

                    return redirect()->back()->with(['success' => 'Total ' . count($rows) . ' Shipment(s) Updated with Tracking Number(s):' . PHP_EOL . $tracking_numbers]);
                } else {
                    $errors = array_map(function ($row, $errors) {
                        return $row . ':' . PHP_EOL . implode(' | ', $errors);
                    }, array_keys($errors), $errors);

                    return redirect()->back()->withErrors($errors);
                }

            } else {
                return redirect()->back()->with('error', 'No Shipments in File');
            }

        }

    }


    static public function add_adjustment($shipment_id, $payable, $payable_remarks = '', $adjustment_type = NULL, $charges = NULL)
    {
        $shipment = Shipment::find($shipment_id);
        $amount = 0;
        $charges = 0;
        $gst = 0;
        if ($shipment->shipment_type == 1) {
            $pending_payment = PendingPayment::where('user_id', $shipment->user_id);

            if ($pending_payment->exists()) {
                $pending_payment = $pending_payment->first();

                $pending_payment->total_shipments = $pending_payment->total_shipments + 1;
                $pending_payment->adjusted_shipments = $pending_payment->adjusted_shipments + 1;

                $pending_payment->save();
            } else {
                $pending_payment = new PendingPayment();

                $pending_payment->user_id = $shipment->user_id;
                $pending_payment->total_shipments = 1;
                $pending_payment->delivered_shipments = 0;
                $pending_payment->returned_shipments = 0;
                $pending_payment->adjusted_shipments = 1;

                $pending_payment->save();
            }

            $pending_payment_shipment = new PendingPaymentShipment();

            $pending_payment_shipment->pending_payment_id = $pending_payment->id;
            $pending_payment_shipment->shipment_id = $shipment_id;
            $pending_payment_shipment->type = 2;
            $pending_payment_shipment->amount = $amount;
            $pending_payment_shipment->charges = $charges;
            $pending_payment_shipment->gst = $gst;
            $pending_payment_shipment->payable = $payable;

            $pending_payment_shipment->save();
            self::add_pending_payment_charges($pending_payment->id, $amount, $charges, $gst, $payable);
            if ($adjustment_type) {
                self::adjustment_logs_add($shipment_id, $adjustment_type, $payable, $payable_remarks, $pending_payment_shipment->id, 1);
            }

            ShipmentsPaymentJourneyController::add($shipment_id, 4, Auth::id(), $payable_remarks);
        } else {
            $retail_shipment = RetailShipment::where('shipment_id', $shipment->id)->first();
            if (in_array($adjustment_type, [2, 6, 7, 8, 9, 10, 11, 15, 16])) {
                $pending_payment = RetailPendingPayment::where('user_id', $retail_shipment->shipper_account_no);

                if ($pending_payment->exists()) {
                    $pending_payment = $pending_payment->first();

                    $pending_payment->total_shipments = $pending_payment->total_shipments + 1;
                    $pending_payment->adjusted_shipments = $pending_payment->adjusted_shipments + 1;

                    $pending_payment->save();
                } else {
                    $pending_payment = new RetailPendingPayment();

                    $pending_payment->user_id = $retail_shipment->shipper_account_no;
                    $pending_payment->total_shipments = 1;
                    $pending_payment->delivered_shipments = 0;
                    $pending_payment->adjusted_shipments = 1;

                    $pending_payment->save();
                }

                if ($adjustment_type == 2) {
                    $payable = 0 - $payable;
                }

                $pending_payment_shipment = new RetailPendingPaymentShipment();

                $pending_payment_shipment->retail_pending_payment_id = $pending_payment->id;
                $pending_payment_shipment->shipment_id = $shipment_id;
                $pending_payment_shipment->type = 2;
                $pending_payment_shipment->amount = $amount;
                $pending_payment_shipment->payable = $payable;

                $pending_payment_shipment->save();
                self::add_pending_payment_charges($pending_payment->id, $amount, $charges, $gst, $payable, 0, 1);
                if ($adjustment_type) {
                    self::adjustment_logs_add($shipment_id, $adjustment_type, $payable, $payable_remarks, $pending_payment_shipment->id, 1, 1);
                }

                ShipmentsPaymentJourneyController::add($shipment_id, 4, Auth::id(), $payable_remarks, NULL, 1);
            }
        }
    }

    public function add_shipment_adjustment_index()
    {
        $adjustment_types = AdjustmentType::whereIn('id', [6, 7, 8, 9, 10, 11, 15, 16])->get();
        return view('admin.finance.add_shipment_adjustment')->with(['adjustment_types' => $adjustment_types]);
    }

    public function add_shipment_adjustment_shipment_details(Request $request)
    {
        $shipment = Shipment::where('tracking_number', $request->tracking_number);

        if ($shipment->exists()) {
            $shipment = $shipment->first();

            $details = array();

            $shipper = $shipment->user;

            $details['id'] = $shipment->id;

            $details['tracking_number'] = $shipment->tracking_number;
            $details['status'] = $shipment->status_shipper->name;

            $details['service_type'] = $shipment->booking_type->booking_type;
            if($shipment->shipment_type == 1){

                $details['shipping_mode'] = $shipment->shipping_mode->mode;
            }
            else{
                $retail_shipment = RetailShipment::where('shipment_id',$shipment->id)->first();
                if($retail_shipment){
                    $details['shipping_mode'] = $retail_shipment->shipping_modes->name;
                }
            }
            $details['weight'] = ($shipment->actual_weight) ? floatval($shipment->actual_weight) : floatval($shipment->estimated_weight);

            $details['payment_mode'] = $shipment->payment_mode->mode;
            $details['amount'] = number_format($shipment->amount);

            $details['shipper']['name'] = $shipper->name;
            $details['shipper']['account_number'] = str_pad($shipper->id, 6, '0', STR_PAD_LEFT);
            $details['shipper']['phone_number_1'] = $shipper->phone;
            $details['shipper']['phone_number_2'] = $shipper->phone2;
            $details['shipper']['origin'] = $shipper->city->name;
            $details['shipper']['address'] = $shipper->address;

            $details['consignee']['name'] = $shipment->consignee_name;
            $details['consignee']['phone_number_1'] = $shipment->consignee_phone_number_1;
            $details['consignee']['phone_number_2'] = $shipment->consignee_phone_number_2;
            $details['consignee']['destination'] = $shipment->consignee_city->name;
            $details['consignee']['address'] = $shipment->consignee_address;

            return ['status' => 0, 'success' => 'Shipment\'s weight can be changed', 'details' => $details];
        } else {
            return ['status' => 1, 'error' => 'No Shipment exists with given Tracking Number'];
        }
    }

    public function add_shipment_adjustment_store(Request $request)
    {
        $shipment_id = $request->input('shipment_id');
        $payable = str_replace(',', '', $request->input('payable'));
        $payable_remarks = $request->input('payable_remarks');
        $adjustment_type = $request->input('adjustment_type');

        $this->add_adjustment($shipment_id, $payable, $payable_remarks, $adjustment_type);

        return redirect()->route('admin.finance.add_shipment_adjustment.index')->with('success', 'Shipment\'s adjustment has been added');
    }

    static public function return_confirmed_revert($shipment_id, $adjustment_type)
    {
        $shipment = Shipment::find($shipment_id);
        if ($shipment->booking_type_id == 4) {
            $receivable = ROUND(($shipment->fuel_surcharge + $shipment->weight_charges + $shipment->gst), 0, PHP_ROUND_HALF_DOWN);

            if ($shipment->charges_mode_id == 1) {
                $shipment->amount = 0;

                $shipment->received_amount = $receivable;

            } else {
                $shipment->amount = $receivable;

                $shipment->received_amount = NULL;

            }

            $shipment->return_charges = NULL;
            $shipment->payment_status_id = 4;

            $shipment->save();
        } else {
            $shipment->return_charges = NULL;
            $shipment->payment_status_id = 4;

            $shipment->save();

            $account_type_id = $shipment->user->account_type_id;

            if ($account_type_id == 1) {
                $pending_payment_shipment = PendingPaymentShipment::where('shipment_id', $shipment_id);

                if ($pending_payment_shipment->exists()) {
                    $pending_payment_shipment = $pending_payment_shipment->latest()->first();

                    self::adjust_payment($pending_payment_shipment->pending_payment_id, $shipment_id, 0, $adjustment_type);
                } else {
                    $done_payment_shipment = DonePaymentShipment::where('shipment_id', $shipment_id);

                    if ($done_payment_shipment->exists()) {
                        $done_payment_shipment = $done_payment_shipment->latest()->first();

                        self::adjust_payment($done_payment_shipment->done_payment_id, $shipment_id, 1, $adjustment_type);
                    }
                }
            } else {
                $payment_shipment_id = NULL;
                $payment_type = NULL;
                $invoice_shipment_id = NULL;
                $invoice_type = NULL;

                $pending_payment_shipment = PendingPaymentShipment::where('shipment_id', $shipment_id);

                if ($pending_payment_shipment->exists()) {
                    $pending_payment_shipment = $pending_payment_shipment->latest()->first();

                    $payment_shipment_id = $pending_payment_shipment->id;
                    $payment_type = 0;
                } else {
                    $done_payment_shipment = DonePaymentShipment::where('shipment_id', $shipment_id);

                    if ($done_payment_shipment->exists()) {
                        $done_payment_shipment = $done_payment_shipment->latest()->first();

                        $payment_shipment_id = $done_payment_shipment->id;
                        $payment_type = 1;
                        $done_payment_id = $done_payment_shipment->done_payment_id;
                    }
                }

                $pending_invoice_shipment = PendingInvoiceShipment::where('shipment_id', $shipment_id);

                if ($pending_invoice_shipment->exists()) {
                    $pending_invoice_shipment = $pending_invoice_shipment->latest()->first();

                    $invoice_shipment_id = $pending_invoice_shipment->id;
                    $invoice_type = 0;
                } else {
                    $done_invoice_shipment = InvoiceShipment::where('shipment_id', $shipment_id);

                    if ($done_invoice_shipment->exists()) {
                        $done_invoice_shipment = $done_invoice_shipment->latest()->first();

                        $invoice_shipment_id = $done_invoice_shipment->id;
                        $invoice_type = 1;
                    }
                }

                if ($payment_shipment_id != NULL || $invoice_shipment_id != NULL) {
                    self::adjust_invoice($shipment_id, $payment_shipment_id, $payment_type, $invoice_shipment_id, $invoice_type, $adjustment_type);
                }
            }

            if (isset($payment_type) && $payment_type == 1) {
                ShipmentsPaymentJourneyController::add($shipment_id, 4, Auth::id(), '', $done_payment_id);
            } else {
                ShipmentsPaymentJourneyController::add($shipment_id, 4, Auth::id());
            }
        }

    }

    static public function add_payment($shipment_id, $type)
    {
        $shipment = Shipment::find($shipment_id);
        $setting = CorporateReimbursementSetting::where('user_id', $shipment->user_id);
        $crs = false;
        if ($setting->exists()) {
            $setting = $setting->first();
            if ($setting->setting_on == 1 && $shipment->user->account_type_id == 2) {
                $crs = true;
            }
        }

        $amount = $shipment->amount;
        if ($shipment->shipment_type == 1) {
            if (!$shipment->packaging_material_request) {
                if ($type == 0) {
                    $charges = $shipment->weight_charges + $shipment->cash_handling_charges + $shipment->insurance_charges + $shipment->fuel_surcharge + $shipment->replacement_charges + $shipment->try_and_buy_charges + $shipment->intercept_charges + $shipment->nsa_osa_charges + $shipment->esc_charges;
                    if ($shipment->business_category_id == 1) {
                        $gst = ROUND(($charges * self::gst($shipment->pickup_address->city->zone_id)), 2, PHP_ROUND_HALF_DOWN);
                    } else {
                        $gst = ROUND(($charges * self::international_gst()), 2, PHP_ROUND_HALF_DOWN);
                    }

                    if ($crs) {
                        $wht = (($charges + $gst) * 3) / 100;
                    } else {
                        $wht = 0;
                    }
                    $payable = $amount - ($charges + $gst - $wht);
                } else {
                    $amount = 0;
                    $charges = $shipment->weight_charges + $shipment->insurance_charges + $shipment->return_charges + $shipment->fuel_surcharge + $shipment->intercept_charges + $shipment->nsa_osa_charges;
                    if ($shipment->business_category_id == 1) {
                        $gst = ROUND(($charges * self::gst($shipment->pickup_address->city->zone_id)), 2, PHP_ROUND_HALF_DOWN);
                    } else {
                        $gst = ROUND(($charges * self::international_gst()), 2, PHP_ROUND_HALF_DOWN);
                    }

                    if ($crs) {
                        $wht = (($charges + $gst) * 3) / 100;
                    } else {
                        $wht = 0;
                    }
                    $payable = 0 - ($charges + $gst - $wht);
                }
            } else {
                $charges = $shipment->packaging_material_charges;
                $gst = 0;

                if ($crs) {
                    $wht = (($charges + $gst) * 3) / 100;
                } else {
                    $wht = 0;
                }

                $payable = $amount - ($charges + $gst - $wht);
            }

            $account_type_id = $shipment->user->account_type_id;

            $valid = TRUE;

            if ($account_type_id == 1) {
                if ($type != 2 && PendingPaymentShipment::where('shipment_id', $shipment_id)->where('type', $type)->exists()) {
                    $valid = FALSE;
                }
            } else {
                if ($type != 2 && PendingInvoiceShipment::where('shipment_id', $shipment_id)->where('type', $type)->exists()) {
                    $valid = FALSE;
                }
            }

            if ($valid) {
                if ($account_type_id == 1 || ($account_type_id == 2 && !$shipment->packaging_material_request) || $crs) {
                    $pending_payment = PendingPayment::where('user_id', $shipment->user_id);

                    if ($pending_payment->exists()) {
                        $pending_payment = $pending_payment->first();

                        $pending_payment->total_shipments = $pending_payment->total_shipments + 1;

                        if ($type == 0) {
                            $pending_payment->delivered_shipments = $pending_payment->delivered_shipments + 1;
                        } else {
                            $pending_payment->returned_shipments = $pending_payment->returned_shipments + 1;
                        }

                        $pending_payment->save();
                    } else {
                        $pending_payment = new PendingPayment();

                        $pending_payment->user_id = $shipment->user_id;
                        $pending_payment->total_shipments = 1;

                        if ($type == 0) {
                            $pending_payment->delivered_shipments = 1;
                            $pending_payment->returned_shipments = 0;
                            $pending_payment->adjusted_shipments = 0;
                        } else {
                            $pending_payment->delivered_shipments = 0;
                            $pending_payment->returned_shipments = 1;
                            $pending_payment->adjusted_shipments = 0;
                        }

                        $pending_payment->save();
                    }

                    $pending_payment_shipment = new PendingPaymentShipment();

                    if ($account_type_id == 1) {
                        $pending_payment_shipment->pending_payment_id = $pending_payment->id;
                        $pending_payment_shipment->shipment_id = $shipment_id;
                        $pending_payment_shipment->type = $type;
                        $pending_payment_shipment->amount = $amount;
                        $pending_payment_shipment->charges = $charges;
                        $pending_payment_shipment->gst = $gst;
                        $pending_payment_shipment->wht = 0;
                        $pending_payment_shipment->payable = $payable;

                        $pending_payment_shipment->save();

                        self::add_pending_payment_charges($pending_payment->id, $amount, $charges, $gst, $payable, $wht);
                    } else {
                        if (!$shipment->packaging_material_request) {

                            $pending_payment_shipment->pending_payment_id = $pending_payment->id;
                            $pending_payment_shipment->shipment_id = $shipment_id;
                            $pending_payment_shipment->type = $type;
                            $pending_payment_shipment->amount = $amount;
                            if ($crs) {
                                $pending_payment_shipment->charges = $charges;
                                $pending_payment_shipment->gst = $gst;
                                $pending_payment_shipment->wht = $wht;
                                $pending_payment_shipment->payable = $payable;
                            } else {
                                $pending_payment_shipment->charges = 0;
                                $pending_payment_shipment->gst = 0;
                                $pending_payment_shipment->wht = 0;
                                $pending_payment_shipment->payable = $amount;
                            }

                            $pending_payment_shipment->save();

                            if ($crs) {
                                self::add_pending_payment_charges($pending_payment->id, $amount, $charges, $gst, $payable, $wht);
                            } else {
                                self::add_pending_payment_charges($pending_payment->id, $amount, 0, 0, $amount, 0);
                            }

                            $pending_invoice_shipment = new PendingInvoiceShipment();

                            $pending_invoice_shipment->shipment_id = $shipment_id;
                            $pending_invoice_shipment->type = $type;
                            $pending_invoice_shipment->charges = $charges;
                            $pending_invoice_shipment->gst = $gst;
                            $pending_invoice_shipment->invoice_amount = $charges + $gst;

                            $pending_invoice_shipment->save();
                        } else {
                            if ($crs) {
                                $pending_payment_shipment->pending_payment_id = $pending_payment->id;
                                $pending_payment_shipment->shipment_id = $shipment_id;
                                $pending_payment_shipment->type = $type;
                                $pending_payment_shipment->amount = $amount;
                                $pending_payment_shipment->payable = $payable;
                                $pending_payment_shipment->save();

                                self::add_pending_payment_charges($pending_payment->id, $amount, $charges, $gst, $payable, $wht);
                            }

                            $pending_invoice_shipment = new PendingInvoiceShipment();

                            $pending_invoice_shipment->shipment_id = $shipment_id;
                            $pending_invoice_shipment->type = $type;
                            $pending_invoice_shipment->charges = $charges;
                            $pending_invoice_shipment->gst = $gst;
                            $pending_invoice_shipment->invoice_amount = $charges + $gst;

                            $pending_invoice_shipment->save();
                        }
                    }
                } else {
                    if ($shipment->packaging_material_request == 1) {
                        $packaging_check = PackagingMaterialRequest::where('tracking_number', $shipment->tracking_number);
                        if ($packaging_check->exists()) {
                            $packaging_check = $packaging_check->first();
                            if ($packaging_check->packaging_payment_mode_id == 2) {
                                $pending_invoice_shipment = new PendingInvoiceShipment();

                                $pending_invoice_shipment->shipment_id = $shipment_id;
                                $pending_invoice_shipment->type = $type;
                                $pending_invoice_shipment->charges = $charges;
                                $pending_invoice_shipment->gst = $gst;
                                $pending_invoice_shipment->invoice_amount = $charges + $gst;

                                $pending_invoice_shipment->save();
                            }
                        }
                    } else {
                        $pending_invoice_shipment = new PendingInvoiceShipment();

                        $pending_invoice_shipment->shipment_id = $shipment_id;
                        $pending_invoice_shipment->type = $type;
                        $pending_invoice_shipment->charges = $charges;
                        $pending_invoice_shipment->gst = $gst;
                        $pending_invoice_shipment->invoice_amount = $charges + $gst;

                        $pending_invoice_shipment->save();
                    }
                }
            }
        } else {
            $retail_shipment = RetailShipment::where('shipment_id', $shipment->id)->first();

            if ($retail_shipment->shipping_mode == 3) {
                if ($shipment->charges_mode_id == 2) {
                    $charges = $retail_shipment->total_charges;
                } else {
                    $charges = 0;
                }
                $gst = 0;
                $payable = $amount - $charges;

//            $account_type_id = $retail_shipment->shipper->account_type_id;

                $valid = TRUE;

                if ($type != 2 && RetailPendingPaymentShipment::where('shipment_id', $shipment_id)->where('type', $type)->exists()) {
                    $valid = FALSE;
                }

                if ($valid) {
                    if ($amount != 0) {
                        $pending_payment = RetailPendingPayment::where('user_id', $retail_shipment->shipper_account_no);

                        if ($pending_payment->exists()) {
                            $pending_payment = $pending_payment->first();

                            $pending_payment->total_shipments = $pending_payment->total_shipments + 1;

                            if ($type == 0) {
                                $pending_payment->delivered_shipments = $pending_payment->delivered_shipments + 1;
                            } else {
                                $pending_payment->adjusted_shipments = $pending_payment->adjusted_shipments + 1;

                            }

                            $pending_payment->save();
                        } else {
                            $pending_payment = new RetailPendingPayment();

                            $pending_payment->user_id = $retail_shipment->shipper_account_no;
                            $pending_payment->total_shipments = 1;

                            if ($type == 0) {
                                $pending_payment->delivered_shipments = 1;
                                $pending_payment->adjusted_shipments = 0;
                            } else {
                                $pending_payment->delivered_shipments = 0;
                                $pending_payment->adjusted_shipments = 1;
                            }

                            $pending_payment->save();
                        }

                        $pending_payment_shipment = new RetailPendingPaymentShipment();

                        $pending_payment_shipment->retail_pending_payment_id = $pending_payment->id;
                        $pending_payment_shipment->shipment_id = $shipment_id;
                        $pending_payment_shipment->type = $type;
                        $pending_payment_shipment->amount = $amount;
                        $pending_payment_shipment->payable = $payable;

                        $pending_payment_shipment->save();

                        self::add_pending_payment_charges($pending_payment->id, $amount, $charges, $gst, $payable, 0, 1);
                    }
                }
            }
        }
    }

    static public function add_corporate_delivered_cod($shipment_id)
    {
        $type = 0;
        $shipment = Shipment::find($shipment_id);

        $amount = $shipment->amount;

        if (!$shipment->packaging_material_request && $amount != 0) {
            $pending_payment = PendingPayment::where('user_id', $shipment->user_id);
            if ($pending_payment->exists()) {
                $pending_payment = $pending_payment->first();

                $pending_payment->total_shipments = $pending_payment->total_shipments + 1;

                $pending_payment->delivered_shipments = $pending_payment->delivered_shipments + 1;

                $pending_payment->save();
            } else {
                $pending_payment = new PendingPayment();

                $pending_payment->user_id = $shipment->user_id;
                $pending_payment->total_shipments = 1;

                $pending_payment->delivered_shipments = 1;
                $pending_payment->returned_shipments = 0;
                $pending_payment->adjusted_shipments = 0;

                $pending_payment->save();
            }

            $pending_payment_shipment = new PendingPaymentShipment();
            if ($amount != 0) {
                $pending_payment_shipment->pending_payment_id = $pending_payment->id;
                $pending_payment_shipment->shipment_id = $shipment_id;
                $pending_payment_shipment->type = $type;
                $pending_payment_shipment->amount = $amount;
                $pending_payment_shipment->charges = 0;
                $pending_payment_shipment->gst = 0;
                $pending_payment_shipment->payable = $amount;

                $pending_payment_shipment->save();
            }

        }

    }

    static public function add_corporate_return_charges($shipment_id)
    {
        $shipment = Shipment::find($shipment_id);

        $amount = $shipment->amount;
        if ($shipment->booking_type_id == 2) {

            $charges = $shipment->return_charges - $shipment->cash_handling_charges - $shipment->replacement_charges;

        } else if ($shipment->booking_type_id == 3) {

            $charges = $shipment->return_charges - $shipment->cash_handling_charges - $shipment->try_and_buy_charges;

        } else {

            $charges = $shipment->return_charges - $shipment->cash_handling_charges;

        }

        if ($shipment->business_category_id == 1) {
            $gst = ROUND(($charges * self::gst($shipment->pickup_address->city->zone_id)), 2, PHP_ROUND_HALF_DOWN);
        } else {
            $gst = ROUND(($charges * self::international_gst()), 2, PHP_ROUND_HALF_DOWN);
        }

        $charges = $charges - $amount;

        $pending_invoice_shipment = new PendingInvoiceShipment();

        $pending_invoice_shipment->shipment_id = $shipment_id;
        $pending_invoice_shipment->type = 1;
        $pending_invoice_shipment->charges = $charges;
        $pending_invoice_shipment->gst = $gst;
        $pending_invoice_shipment->invoice_amount = $charges + $gst;

        $pending_invoice_shipment->save();

    }

    private static function adjust_payment($payment_id, $shipment_id, $payment_type, $adjustment_type = NULL)
    {
        $shipment = Shipment::find($shipment_id);
        if ($shipment->shipment_type == 1) {
            if ($payment_type == 0) {
                $payment_shipment = PendingPaymentShipment::where('pending_payment_id', $payment_id)->where('shipment_id', $shipment_id)->latest()->first();
                if ($payment_shipment) {
                    $payment = $payment_shipment->pending_payment;

                    $total_shipments = $payment->total_shipments - 1;

                    if ($total_shipments == 0) {
                        $payment->delete();
                        PendingPaymentCalculation::where('pending_payment_id', $payment_id)->delete();
                    } else {
                        $payment->total_shipments = $total_shipments;

                        if ($payment_shipment->type == 0) {
                            $delivered_shipments = $payment->delivered_shipments - 1;

                            $payment->delivered_shipments = (($delivered_shipments > 0) ? $delivered_shipments : 0);
                        }
                        if ($payment_shipment->type == 1) {
                            $returned_shipments = $payment->returned_shipments - 1;

                            $payment->returned_shipments = (($returned_shipments > 0) ? $returned_shipments : 0);
                        } else {
                            $adjusted_shipments = $payment->adjusted_shipments - 1;

                            $payment->adjusted_shipments = (($adjusted_shipments > 0) ? $adjusted_shipments : 0);
                        }

                        $payment->save();
                        $payment_payment_calculation = PendingPaymentCalculation::where('pending_payment_id', $payment_id);
                        if ($payment_payment_calculation->exists()) {
                            $payment_payment_calculation = $payment_payment_calculation->first();
                            $payment_payment_calculation->amount = $payment_payment_calculation->amount - $payment_shipment->amount;
                            $payment_payment_calculation->charges = $payment_payment_calculation->charges - $payment_shipment->charges;
                            $payment_payment_calculation->gst = $payment_payment_calculation->gst - $payment_shipment->gst;
                            $payment_payment_calculation->payable = $payment_payment_calculation->payable - $payment_shipment->payable;
                            $payment_payment_calculation->save();
                        }
                    }

                    $payment_shipment->delete();
                }
            } else {
                $payment_shipment = DonePaymentShipment::where('done_payment_id', $payment_id)->where('shipment_id', $shipment_id)->latest()->first();

                $shipment = Shipment::find($shipment_id);

                $amount = 0;
                $charges = 0;
                $gst = 0;
                $payable = 0 - $payment_shipment->payable;

                $pending_payment = PendingPayment::where('user_id', $shipment->user_id);

                if ($pending_payment->exists()) {
                    $pending_payment = $pending_payment->first();

                    $pending_payment->total_shipments = $pending_payment->total_shipments + 1;
                    $pending_payment->adjusted_shipments = $pending_payment->adjusted_shipments + 1;

                    $pending_payment->save();
                } else {
                    $pending_payment = new PendingPayment();

                    $pending_payment->user_id = $shipment->user_id;
                    $pending_payment->total_shipments = 1;
                    $pending_payment->delivered_shipments = 0;
                    $pending_payment->returned_shipments = 0;
                    $pending_payment->adjusted_shipments = 1;

                    $pending_payment->save();
                }

                $pending_payment_shipment = new PendingPaymentShipment();

                $pending_payment_shipment->pending_payment_id = $pending_payment->id;
                $pending_payment_shipment->shipment_id = $shipment_id;
                $pending_payment_shipment->type = 2;
                $pending_payment_shipment->amount = $amount;
                $pending_payment_shipment->charges = $charges;
                $pending_payment_shipment->gst = $gst;
                $pending_payment_shipment->payable = $payable;

                $pending_payment_shipment->save();

                self::add_pending_payment_charges($pending_payment->id, $amount, $charges, $gst, $payable);

                if ($adjustment_type) {
                    self::adjustment_logs_add($shipment_id, $adjustment_type, $payable, NULL, $pending_payment_shipment->id, 1);
                }
            }
        } elseif ($shipment->shipment_type == 2) {
            if ($payment_type == 0) {
                $payment_shipment = RetailPendingPaymentShipment::where('retail_pending_payment_id', $payment_id)->where('shipment_id', $shipment_id)->latest()->first();
                if ($payment_shipment) {
                    $payment = RetailPendingPayment::find($payment_shipment->retail_pending_payment_id);

                    // $payment = $payment_shipment->pending_payment;

                    $total_shipments = $payment->total_shipments - 1;

                    if ($total_shipments == 0) {
                        $payment->delete();
                        RetailPendingPaymentCalculation::where('retail_pending_payment_id', $payment_id)->delete();
                    } else {
                        $payment->total_shipments = $total_shipments;

                        if ($payment_shipment->type == 0) {
                            $delivered_shipments = $payment->delivered_shipments - 1;

                            $payment->delivered_shipments = (($delivered_shipments > 0) ? $delivered_shipments : 0);
                        } else {
                            $adjusted_shipments = $payment->adjusted_shipments - 1;

                            $payment->adjusted_shipments = (($adjusted_shipments > 0) ? $adjusted_shipments : 0);
                        }

                        $payment->save();
                        $payment_payment_calculation = RetailPendingPaymentCalculation::where('retail_pending_payment_id', $payment_id);
                        if ($payment_payment_calculation->exists()) {
                            $payment_payment_calculation = $payment_payment_calculation->first();
                            $payment_payment_calculation->amount = $payment_payment_calculation->amount - $payment_shipment->amount;
                            $payment_payment_calculation->payable = $payment_payment_calculation->payable - $payment_shipment->payable;
                            $payment_payment_calculation->save();
                        }
                    }

                    $payment_shipment->delete();
                }
            } else {
                $payment_shipment = RetailDonePaymentShipment::where('retail_done_payment_id', $payment_id)->where('shipment_id', $shipment_id)->latest()->first();
                $rdp = RetailDonePayment::find($payment_shipment->retail_done_payment_id);

                $shipment = Shipment::find($shipment_id);

                $amount = 0;
                $payable = 0 - $payment_shipment->payable;

                $pending_payment = RetailPendingPayment::where('user_id', $rdp->user_id);

                if ($pending_payment->exists()) {
                    $pending_payment = $pending_payment->first();

                    $pending_payment->total_shipments = $pending_payment->total_shipments + 1;
                    $pending_payment->adjusted_shipments = $pending_payment->adjusted_shipments + 1;

                    $pending_payment->save();
                } else {
                    $pending_payment = new RetailPendingPayment();

                    $pending_payment->user_id = $rdp->user_id;
                    $pending_payment->total_shipments = 1;
                    $pending_payment->delivered_shipments = 0;
                    $pending_payment->adjusted_shipments = 1;

                    $pending_payment->save();
                }

                $pending_payment_shipment = new RetailPendingPaymentShipment();

                $pending_payment_shipment->retail_pending_payment_id = $pending_payment->id;
                $pending_payment_shipment->shipment_id = $shipment_id;
                $pending_payment_shipment->type = 2;
                $pending_payment_shipment->amount = $amount;
                $pending_payment_shipment->payable = $payable;

                $pending_payment_shipment->save();

                self::add_pending_payment_charges($pending_payment->id, $amount, 0, 0, $payable, 1);

                if ($adjustment_type) {
                    self::adjustment_logs_add($shipment_id, $adjustment_type, $payable, NULL, $pending_payment_shipment->id, 1, 1);
                }
            }
        }

    }

    private static function adjust_invoice($shipment_id, $payment_shipment_id, $payment_type, $invoice_shipment_id, $invoice_type, $adjustment_type = NULL)
    {
        $shipment = Shipment::find($shipment_id);
        if ($shipment->shipment_type == 1) {
            if ($payment_shipment_id) {
                if ($payment_type == 0) {
                    $payment_shipment = PendingPaymentShipment::find($payment_shipment_id);

                    if ($payment_shipment) {
                        $payment = $payment_shipment->pending_payment;

                        $total_shipments = $payment->total_shipments - 1;

                        if ($total_shipments == 0) {
                            $payment->delete();
                        } else {
                            $payment->total_shipments = $total_shipments;

                            if ($payment_shipment->type == 0) {
                                $delivered_shipments = $payment->delivered_shipments - 1;

                                $payment->delivered_shipments = (($delivered_shipments > 0) ? $delivered_shipments : 0);
                            }
                            //remove for retail
                            if ($payment_shipment->type == 1) {
                                $returned_shipments = $payment->returned_shipments - 1;

                                $payment->returned_shipments = (($returned_shipments > 0) ? $returned_shipments : 0);
                            } //remove for retail end
                            else {
                                $adjusted_shipments = $payment->adjusted_shipments - 1;

                                $payment->adjusted_shipments = (($adjusted_shipments > 0) ? $adjusted_shipments : 0);
                            }

                            $payment->save();
                        }

                        $payment_shipment->delete();
                    }
                } else {
                    $payment_shipment = DonePaymentShipment::find($payment_shipment_id);

                    if ($payment_shipment) {
                        $shipment = Shipment::find($shipment_id);

                        $amount = 0;
                        $charges = 0;
                        $gst = 0;
                        $payable = 0 - $payment_shipment->payable;
                        //retail pending payment
                        $pending_payment = PendingPayment::where('user_id', $shipment->user_id);

                        if ($pending_payment->exists()) {
                            $pending_payment = $pending_payment->first();

                            $pending_payment->total_shipments = $pending_payment->total_shipments + 1;
                            $pending_payment->adjusted_shipments = $pending_payment->adjusted_shipments + 1;

                            $pending_payment->save();
                        } else {
                            $pending_payment = new PendingPayment();

                            $pending_payment->user_id = $shipment->user_id;
                            $pending_payment->total_shipments = 1;
                            $pending_payment->delivered_shipments = 0;
                            $pending_payment->returned_shipments = 0;
                            $pending_payment->adjusted_shipments = 1;

                            $pending_payment->save();
                        }

                        $pending_payment_shipment = new PendingPaymentShipment();

                        $pending_payment_shipment->pending_payment_id = $pending_payment->id;
                        $pending_payment_shipment->shipment_id = $shipment_id;
                        $pending_payment_shipment->type = 2;
                        $pending_payment_shipment->amount = $amount;
                        $pending_payment_shipment->charges = $charges;
                        $pending_payment_shipment->gst = $gst;
                        $pending_payment_shipment->payable = $payable;

                        $pending_payment_shipment->save();
                        if ($adjustment_type) {
                            self::adjustment_logs_add($shipment_id, $adjustment_type, $payable, NULL, $pending_payment_shipment->id, 1);
                        }
                    }
                }
            }

            if ($invoice_shipment_id) {
                if ($invoice_type == 0) {
                    $invoice_shipment = PendingInvoiceShipment::find($invoice_shipment_id);

                    $invoice_shipment->delete();
                } else {
                    $invoice_shipment = InvoiceShipment::find($invoice_shipment_id);

                    if ($invoice_shipment) {
                        $shipment = Shipment::find($shipment_id);

                        $amount = 0;
                        $charges = 0;
                        $gst = 0;
                        $payable = $invoice_shipment->invoice_amount;

                        $pending_payment = PendingPayment::where('user_id', $shipment->user_id);

                        if ($pending_payment->exists()) {
                            $pending_payment = $pending_payment->first();

                            $pending_payment->total_shipments = $pending_payment->total_shipments + 1;
                            $pending_payment->adjusted_shipments = $pending_payment->adjusted_shipments + 1;

                            $pending_payment->save();
                        } else {
                            $pending_payment = new PendingPayment();

                            $pending_payment->user_id = $shipment->user_id;
                            $pending_payment->total_shipments = 1;
                            $pending_payment->delivered_shipments = 0;
                            $pending_payment->returned_shipments = 0;
                            $pending_payment->adjusted_shipments = 1;

                            $pending_payment->save();
                        }

                        $pending_payment_shipment = new PendingPaymentShipment();

                        $pending_payment_shipment->pending_payment_id = $pending_payment->id;
                        $pending_payment_shipment->shipment_id = $shipment_id;
                        $pending_payment_shipment->type = 2;
                        $pending_payment_shipment->amount = $amount;
                        $pending_payment_shipment->charges = $charges;
                        $pending_payment_shipment->gst = $gst;
                        $pending_payment_shipment->payable = $payable;

                        $pending_payment_shipment->save();
                        if ($adjustment_type) {
                            self::adjustment_logs_add($shipment_id, $adjustment_type, $payable, NULL, $pending_payment_shipment->id, 1);
                        }
                    }
                }
            }
        } elseif ($shipment->shipment_type == 2) {
            if ($payment_shipment_id) {
                if ($payment_type == 0) {
                    $payment_shipment = RetailPendingPaymentShipment::find($payment_shipment_id);

                    if ($payment_shipment) {
                        // $payment = $payment_shipment->pending_payment;
                        $payment = RetailPendingPayment::find($payment_shipment->retail_pending_payment_id);

                        $total_shipments = $payment->total_shipments - 1;

                        if ($total_shipments == 0) {
                            $payment->delete();
                            //for retail only
                            RetailPendingPaymentCalculation::where('retail_pending_payment_id', $payment->id)->delete();

                        } else {
                            $payment->total_shipments = $total_shipments;

                            if ($payment_shipment->type == 0) {
                                $delivered_shipments = $payment->delivered_shipments - 1;

                                $payment->delivered_shipments = (($delivered_shipments > 0) ? $delivered_shipments : 0);
                            } else {
                                $adjusted_shipments = $payment->adjusted_shipments - 1;

                                $payment->adjusted_shipments = (($adjusted_shipments > 0) ? $adjusted_shipments : 0);
                            }

                            $payment->save();
                            $payment_payment_calculation = RetailPendingPaymentCalculation::where('retail_pending_payment_id', $payment->id);
                            if ($payment_payment_calculation->exists()) {
                                $payment_payment_calculation = $payment_payment_calculation->first();
                                $payment_payment_calculation->amount = $payment_payment_calculation->amount - $payment_shipment->amount;
                                $payment_payment_calculation->payable = $payment_payment_calculation->payable - $payment_shipment->payable;
                                $payment_payment_calculation->save();
                            }
                        }

                        $payment_shipment->delete();
                    }
                } else {
                    $payment_shipment = RetailDonePaymentShipment::find($payment_shipment_id);
                    $rdp = RetailDonePayment::find($payment_shipment->retail_done_payment_id);

                    if ($payment_shipment) {
                        $shipment = Shipment::find($shipment_id);

                        $amount = 0;
                        $payable = 0 - $payment_shipment->payable;
                        //retail pending payment
                        $pending_payment = RetailPendingPayment::where('user_id', $rdp->user_id);

                        if ($pending_payment->exists()) {
                            $pending_payment = $pending_payment->first();

                            $pending_payment->total_shipments = $pending_payment->total_shipments + 1;
                            $pending_payment->adjusted_shipments = $pending_payment->adjusted_shipments + 1;

                            $pending_payment->save();
                        } else {
                            $pending_payment = new RetailPendingPayment();

                            $pending_payment->user_id = $rdp->user_id;
                            $pending_payment->total_shipments = 1;
                            $pending_payment->delivered_shipments = 0;
                            $pending_payment->adjusted_shipments = 1;

                            $pending_payment->save();
                        }

                        $pending_payment_shipment = new RetailPendingPaymentShipment();

                        $pending_payment_shipment->retail_pending_payment_id = $pending_payment->id;
                        $pending_payment_shipment->shipment_id = $shipment_id;
                        $pending_payment_shipment->type = 2;
                        $pending_payment_shipment->amount = $amount;
                        $pending_payment_shipment->payable = $payable;

                        $pending_payment_shipment->save();
                        //for retail only

                        self::add_pending_payment_charges($pending_payment->id, $amount, 0, 0, $payable, 0, 1);

                        if ($adjustment_type) {
                            self::adjustment_logs_add($shipment_id, $adjustment_type, $payable, NULL, $pending_payment_shipment->id, 1, 1);
                        }
                    }
                }
            }

            if ($invoice_shipment_id) {
                if ($invoice_type == 0) {
                    $invoice_shipment = PendingInvoiceShipment::find($invoice_shipment_id);

                    $invoice_shipment->delete();
                } else {
                    $invoice_shipment = InvoiceShipment::find($invoice_shipment_id);

                    if ($invoice_shipment) {
                        $shipment = Shipment::find($shipment_id);
                        $rs = RetailShipment::where('shipment_id', $shipment_id)->get()->first();
                        $amount = 0;
                        $payable = $invoice_shipment->invoice_amount;

                        $pending_payment = RetailPendingPayment::where('user_id', $rs->shipper_account_no);

                        if ($pending_payment->exists()) {
                            $pending_payment = $pending_payment->first();

                            $pending_payment->total_shipments = $pending_payment->total_shipments + 1;
                            $pending_payment->adjusted_shipments = $pending_payment->adjusted_shipments + 1;

                            $pending_payment->save();
                        } else {
                            $pending_payment = new RetailPendingPayment();

                            $pending_payment->user_id = $rs->shipper_account_no;
                            $pending_payment->total_shipments = 1;
                            $pending_payment->delivered_shipments = 0;
                            $pending_payment->adjusted_shipments = 1;

                            $pending_payment->save();
                        }

                        $pending_payment_shipment = new RetailPendingPaymentShipment();

                        $pending_payment_shipment->retail_pending_payment_id = $pending_payment->id;
                        $pending_payment_shipment->shipment_id = $shipment_id;
                        $pending_payment_shipment->type = 2;
                        $pending_payment_shipment->amount = $amount;
                        $pending_payment_shipment->payable = $payable;

                        $pending_payment_shipment->save();
                        if ($adjustment_type) {
                            self::adjustment_logs_add($shipment_id, $adjustment_type, $payable, NULL, $pending_payment_shipment->id, 1, 1);
                        }
                    }
                }
            }
        }

    }

    public function make_payments_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 29);
        $banks = BanksList::all();
        $company_banks = BanksList::where('affiliate', 1)->get();
        if (session('department_id') == 7 && !in_array(session('id'), session('sale_users_bypass'))) {
            $shippers = User::whereIn('id', session('tagged_shippers'))->select('id', 'name')->get();
        } else {
            $shippers = User::select('id', 'name')->get();
        }
        $shipper_status = [1 => 'Active', 2 => 'Inactive'];
        $total_amount = PendingPaymentShipment::sum('amount');
        $total_charges = PendingPaymentShipment::sum('charges');
        $total_payable = PendingPaymentShipment::sum('payable');
        return view('admin.finance.make_payments')->with(['banks' => $banks, 'shipper_status' => $shipper_status, 'total_amount' => $total_amount, 'company_banks' => $company_banks, 'total_charges' => $total_charges, 'total_payable' => $total_payable, 'shippers' => $shippers]);
    }

    public function make_payments_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 89);
        }

        $pending_payments = PendingPayment::join('users as u', 'pending_payments.user_id', '=', 'u.id')
            ->join('cities as c', 'u.city_id', '=', 'c.id')
            ->join('user_bank_infos as ubi', function ($join) {
                $join->on('pending_payments.user_id', '=', 'ubi.user_id')
                    ->where('ubi.default_bank', DB::raw(1));
            })
            ->join('banks_lists as ub', 'ubi.bank_name', '=', 'ub.id')
            ->join('payment_cycles as pc', 'u.payment_cycle_id', '=', 'pc.id')
            ->join('cities as bc', 'ubi.city_id', '=', 'bc.id')
            ->join('pending_payment_shipments as pps', 'pending_payments.id', '=', 'pps.pending_payment_id')
            ->leftjoin('pending_payment_calculations as ppc', 'ppc.pending_payment_id', '=', 'pending_payments.id')
            ->join('shipments as s', 's.id', '=', 'pps.shipment_id')
            ->join('user_shipping_infos AS usi', 's.pickup_address_id', '=', 'usi.id')
            ->leftjoin('pending_shipments_for_payments as psfp', 'psfp.user_id', '=', 'pending_payments.user_id')
            ->select('pending_payments.id as id', 'pending_payments.created_at', 'u.name as shipper', 'c.name as city', 'u.phone', 'u.phone2', 'u.address', 'pending_payments.total_shipments', 'pending_payments.delivered_shipments', 'pending_payments.delivered_shipments as delivered_shipments_count', 'pending_payments.returned_shipments', 'pending_payments.returned_shipments as returned_shipments_count ', 'pending_payments.adjusted_shipments', 'pending_payments.adjusted_shipments as adjusted_shipments_count', 'ppc.amount as total_amount', 'ppc.charges as total_charges', 'ppc.gst as total_gst', 'ppc.wht as total_wht', 'ppc.payable as total_payable', 'ub.name as bank', 'ubi.bank_branch', 'ubi.account_no', 'ubi.account_title', 'ubi.iban', 'bc.name as account_city', 'pc.name as payment_cycle', 's.booking_type_id', 'usi.poc', 's.packaging_charges', 'u.documents_status', DB::raw('IFNULL(psfp.pending_shipments_count,0) as total_pending_shipments'))
            ->groupBy('pending_payments.id');

        if (session('department_id') == 7) {
            if (!in_array(session('id'), session('sale_users_bypass'))) {
                $pending_payments = $pending_payments->where(function ($query) {
                    $query->whereIn('u.id', session('tagged_shippers'));
                });
            }
        } else if (session('role_id') != 1) {
            $pending_payments = $pending_payments->whereIn('c.hub_id', session('hubs'));
        }

        $datatables = Datatables::of($pending_payments)
            ->addColumn('total_deductable', function ($pending_payments) {
                return number_format(($pending_payments->total_charges + $pending_payments->total_gst), 2);

            })
            ->editColumn('shipper', function ($shipment) {
                if ($shipment->booking_type_id == 4) {
                    return $shipment->shipper . ' (' . $shipment->poc . ')';
                } else {
                    return $shipment->shipper;
                }
            })
            ->filterColumn('u.name', function ($query, $keyword) {
                $query->where(function ($sub_query) use ($keyword) {
                    $sub_query->where('s.booking_type_id', '!=', 4)
                        ->where('u.name', 'like', '%' . $keyword . '%');
                })
                    ->orWhere(function ($sub_query) use ($keyword) {
                        $sub_query->where('s.booking_type_id', '=', 4)
                            ->where('usi.poc', 'like', '%' . $keyword . '%');
                    });
            })
            ->orderColumn('u.name', 'u.name $1, usi.poc $1')
            ->editColumn('delivered_shipments', function ($pending_payment) {
                if ($pending_payment->delivered_shipments != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $pending_payment->delivered_shipments . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('returned_shipments', function ($pending_payment) {
                if ($pending_payment->returned_shipments != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $pending_payment->returned_shipments . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('adjusted_shipments', function ($pending_payment) {
                if ($pending_payment->adjusted_shipments != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $pending_payment->adjusted_shipments . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('total_amount', function ($pending_payment) {
                return number_format($pending_payment->total_amount, 2);
            })
            ->editColumn('total_charges', function ($pending_payment) {
                return number_format($pending_payment->total_charges, 2);
            })
            ->editColumn('total_gst', function ($pending_payment) {
                return number_format($pending_payment->total_gst, 2);
            })
            ->editColumn('total_wht', function ($pending_payment) {
                return number_format($pending_payment->total_wht, 2);
            })
            ->editColumn('packaging_charges', function ($pending_payment) {
                return number_format($pending_payment->packaging_charges, 2);
            })
            ->editColumn('total_payable', function ($pending_payment) {
                return number_format(ROUND($pending_payment->total_payable, 0, PHP_ROUND_HALF_DOWN));
            })
            ->addColumn('phone_numbers', function ($pending_payment) {
                $phone_numbers = $pending_payment->phone;

                if (!empty($pending_payment->phone2)) {
                    $phone_numbers .= ' - ' . $pending_payment->phone2;
                }

                return $phone_numbers;
            })
            ->removeColumn('phone')
            ->removeColumn('phone2')
            ->addColumn('return_shipments_average_aging', function ($pending_payment) {
                if ($pending_payment->returned_shipments != 0) {
                    $shipments = 0;
                    $days = 0;

                    $now = Carbon::now()->startOfDay();

                    $pending_payment_shipments = PendingPaymentShipment::where('pending_payment_id', $pending_payment->id)->where('type', 1)->get();

                    foreach ($pending_payment_shipments as $pending_payment_shipment) {
                        $created_at = Carbon::parse($pending_payment_shipment->created_at)->startOfDay();

                        $days += $created_at->diffInDays($now);

                        $shipments++;
                    }

                    if ($shipments > 0) {
                        return round(($days / $shipments), 2) . 'd';
                    } else {
                        return '-';
                    }
                } else {
                    return '-';
                }
            })
            ->addColumn('action', function ($pending_payment) {
                $view_details_button = '<button type="button" class="dropdown-item view_details"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-file-text"></i></div><div class="col-9 offset-1">View Details</div></button>';
                $make_payments_button = '<button type="button" class="dropdown-item make_payment"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-credit-card"></i></div><div class="col-9 offset-1">Make Payment</div></button>';

                $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

                $dropdown .= $view_details_button;

                if (($pending_payment->documents_status == 2) && (session('role_id') == 1 || in_array(60, session('permissions')))) {
                    $dropdown .= $make_payments_button;
                }

                $dropdown .= '
                </div>
              </div>
            ';

                return $dropdown;
            })
            ->filterColumn('phone_numbers', function ($query, $keyword) {
                $search = str_replace('-', '', $keyword);

                if ($keyword != '') {
                    $query->where(function ($sub_query) use ($keyword) {
                        $sub_query->where('u.phone', 'like', '%' . $keyword . '%')
                            ->orWhere('u.phone2', 'like', '%' . $keyword . '%');
                    });
                } else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('bank', function ($query, $keyword) {

                if ($keyword != '') {
                    $query->where('ub.id', '=', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->orderColumn('phone_numbers', 'u.phone $1, u.phone2 $1');

        if ($payment_filter = $request->get('payment_filter')) {
            if ($payment_filter == 1) {
                $dayOfweek = Carbon::today()->dayOfWeek;
                $dayOfMonth = Carbon::today()->format('d');
                $datatables = $datatables->where(function ($query) use ($payment_filter, $dayOfweek, $dayOfMonth) {
                    $query->where(function ($sub_query) use ($payment_filter) {
                        $sub_query->where('u.payment_cycle_id', 1);
                    })
                        ->orwhere(function ($sub_query) use ($payment_filter, $dayOfweek, $dayOfMonth) {
                            $sub_query->where('u.payment_day', '=', DB::raw("IF (u.payment_cycle_id = 2, $dayOfweek, IF  (u.payment_cycle_id = 3, $dayOfMonth,''))"));
                        });
                });
            } else {
                $datatables->whereIn('u.payment_cycle_id', [1, 2, 3]);
            }
        }

        if ($tracking_number = $request->get('tracking_number')) {
            $datatables->join('shipments as ss', 'pps.shipment_id', '=', 'ss.id')
                ->where('ss.tracking_number', '=', $tracking_number);
        }

        if ($positive_negative_filter = $request->get('positive_negative_filter')) {
            if ($positive_negative_filter == 1) {
                $datatables->having('total_payable', '>=', 0);
            } else if ($positive_negative_filter == 2) {
                $datatables->having('total_payable', '<', 0);
            }
        }

        if ($shipper = $request->get('search_shipper')) {
            $datatables->where('u.id', '=', $shipper);
        }

        if ($shipper_status = $request->get('shipper_status')) {
            if ($shipper_status == 1) {
                $datatables->where('u.status', '=', 3)->where('u.blacklist', 0);
            } else {
                $datatables->where('u.status', '!=', 3);
            }
        }
        if ($request->get('shipper_document_status') !== null) {
            $shipper_document_status = $request->get('shipper_document_status');
            if ($shipper_document_status == 0) {
                $datatables->where('u.documents_status', '=', 0);
            } else if ($shipper_document_status == 1) {
                $datatables->where('u.documents_status', '=', 1);
            } else if ($shipper_document_status == 2) {
                $datatables->where('u.documents_status', '=', 2);
            } else if ($shipper_document_status == 3) {
                $datatables->where('u.documents_status', '=', 3);
            } else {
                $datatables->whereRaw('false');
            }
        }

        return $datatables->make(true);
    }

    public function make_payments_delivered_shipments(Request $request)
    {
        $tracking_numbers = array();

        $pending_payment_shipments = PendingPaymentShipment::where('pending_payment_id', $request->id)->where('type', 0)->get();

        foreach ($pending_payment_shipments as $pending_payment_shipment) {
            $shipment = $pending_payment_shipment->shipment;

            $tracking_numbers[] = $shipment->tracking_number;
        }

        return $tracking_numbers;
    }

    public function make_payments_returned_shipments(Request $request)
    {
        $tracking_numbers = array();

        $pending_payment_shipments = PendingPaymentShipment::where('pending_payment_id', $request->id)->where('type', 1)->get();

        foreach ($pending_payment_shipments as $pending_payment_shipment) {
            $shipment = $pending_payment_shipment->shipment;

            $tracking_numbers[] = $shipment->tracking_number;
        }

        return $tracking_numbers;
    }

    public function make_payments_adjusted_shipments(Request $request)
    {
        $tracking_numbers = array();

        $pending_payment_shipments = PendingPaymentShipment::where('pending_payment_id', $request->id)->where('type', 2)->get();

        foreach ($pending_payment_shipments as $pending_payment_shipment) {
            $shipment = $pending_payment_shipment->shipment;

            $tracking_numbers[] = $shipment->tracking_number;
        }

        return $tracking_numbers;
    }

    public function make_payments_shipment_details(Request $request)
    {
        $details = array();

        $pending_payment_shipments = PendingPaymentShipment::where('pending_payment_id', $request->id)->get();

        foreach ($pending_payment_shipments as $pending_payment_shipment) {
            $shipment = $pending_payment_shipment->shipment;

            $detail = array();
            if ($request->has('pickup_address_id')) {
                if ($request->pickup_address_id == $shipment->pickup_address_id) {
                    $detail['tracking_number'] = $shipment->tracking_number;

                    if ($pending_payment_shipment->type == 0) {
                        $detail['type'] = 'Delivered';
                    } else if ($pending_payment_shipment->type == 1) {
                        $detail['type'] = 'Returned';
                    } else {
                        $detail['type'] = 'Adjusted';
                    }

                    $detail['amount'] = number_format($pending_payment_shipment->amount);
                    $detail['charges'] = number_format($pending_payment_shipment->charges, 2);
                    $detail['gst'] = number_format($pending_payment_shipment->gst, 2);
                    $detail['deductable'] = number_format(($pending_payment_shipment->charges + $pending_payment_shipment->gst), 2);
                    $detail['payable'] = number_format($pending_payment_shipment->payable, 2);

                    $details[] = $detail;
                }

            } else {
                $detail['tracking_number'] = $shipment->tracking_number;

                if ($pending_payment_shipment->type == 0) {
                    $detail['type'] = 'Delivered';
                } else if ($pending_payment_shipment->type == 1) {
                    $detail['type'] = 'Returned';
                } else {
                    $detail['type'] = 'Adjusted';
                }

                $detail['amount'] = number_format($pending_payment_shipment->amount);
                $detail['charges'] = number_format($pending_payment_shipment->charges, 2);
                $detail['gst'] = number_format($pending_payment_shipment->gst, 2);
                $detail['deductable'] = number_format(($pending_payment_shipment->charges + $pending_payment_shipment->gst), 2);
                $detail['payable'] = number_format($pending_payment_shipment->payable, 2);

                $details[] = $detail;
            }

        }

        return $details;
    }

    public function make_payments_shipment_list(Request $request)
    {
        $pending_payment_shipments = PendingPaymentShipment::join('shipments as s', 'pending_payment_shipments.shipment_id', '=', 's.id')
            ->join('user_shipping_infos AS usi', 's.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('users as u', 's.user_id', '=', 'u.id')
            ->join('shipment_status as ss', 's.shipper_status_id', '=', 'ss.id')
            ->leftjoin('consolidation_shipments as consolidations', function ($join) {
                $join->on('consolidations.shipment_id', '=', 's.id')
                    ->where('consolidations.consolidation_id', '=',
                        DB::raw('(select consolidation_id from consolidation_shipments where consolidation_shipments.shipment_id = s.id)'));
            })
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 's.id')
                    ->where('sj.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = s.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->select('pending_payment_shipments.id', 'u.name as shipper', 's.tracking_number as shipment', 'pending_payment_shipments.type', 'ss.name as status', 'pending_payment_shipments.created_at', 'pending_payment_shipments.amount', 'pending_payment_shipments.charges', 'pending_payment_shipments.gst', 'pending_payment_shipments.wht', 'pending_payment_shipments.payable', 'consolidations.consolidation_id', 'oc.name as origin', 'u.account_type_id', 's.pickup_address_id','sj.created_at as arrival_date');

        if ($request->has('ids')) {
            $pending_payment_shipments->whereIn('pending_payment_shipments.pending_payment_id', $request->ids);
        } else {
            $pending_payment_shipments->whereRaw('FALSE');
        }
        if ($request->has('pickup_address_id')) {
            $pending_payment_shipments->where('s.pickup_address_id', $request->pickup_address_id);
        }

        $datatables = Datatables::of($pending_payment_shipments)
            ->setRowAttr([
                'consolidation_id' => function ($deliveries) {
                    if ($deliveries->consolidation_id != null) {
                        return $deliveries->consolidation_id;
                    } else {
                        return '';
                    }
                },
                'type_id' => function ($deliveries) {
                    return $deliveries->type;
                },
                'account_type' => function ($deliveries) {
                    return $deliveries->account_type_id;
                }
            ])
            ->addColumn('deductable', function ($pending_payment_shipments) {
                return number_format(($pending_payment_shipments->charges + $pending_payment_shipments->gst), 2);
            })
            ->addColumn('aging', function ($pending_payment_shipments) {
                $now = Carbon::now()->startOfDay();

                $created_at = Carbon::parse($pending_payment_shipments->created_at)->startOfDay();

                return $created_at->diffInDays($now) . 'd';
            })
            ->editColumn('amount', function ($pending_payment_shipment) {
                return number_format($pending_payment_shipment->amount);
            })
            ->editColumn('charges', function ($pending_payment_shipment) {
                return number_format($pending_payment_shipment->charges, 2);
            })
            ->editColumn('gst', function ($pending_payment_shipment) {
                return number_format($pending_payment_shipment->gst, 2);
            })
            ->editColumn('wht', function ($pending_payment_shipment) {
                return number_format($pending_payment_shipment->wht, 2);
            })
            ->editColumn('payable', function ($pending_payment_shipment) {
                return number_format($pending_payment_shipment->payable, 2);
            })
            ->editColumn('type', function ($pending_payment_shipment) {
                if ($pending_payment_shipment->type == 0) {
                    return 'Delivered';
                } else if ($pending_payment_shipment->type == 1) {
                    return 'Returned';
                } else {
                    return 'Adjusted';
                }
            })
            ->filterColumn('type', function ($query, $keyword) {
                if ($keyword == 0 || $keyword == 1 || $keyword == 2) {
                    $query->where('pending_payment_shipments.type', '=', $keyword);
                } else {
                    $query->whereIn('pending_payment_shipments.type', [0, 1, 2]);
                }
            })
            ->filterColumn('deductable', function ($query, $keyword) {
                $query->where(DB::raw('pending_payment_shipments.charges + pending_payment_shipments.gst'), '=', $keyword);
            })
            ->orderColumn('deductable', DB::raw('pending_payment_shipments.charges + pending_payment_shipments.gst') . ' $1');

        return $datatables->make(true);
    }

    public function make_payments_shipment_export_selected(Request $request)
    {
        $pending_payment_shipment_ids = explode(',', $request->ids);

        $filename = 'sonic_pending_payment_shipments';

        $details = array();

        $details[] = ['S. No.', 'Shipper', 'Shipment', 'Origin','Type', 'Status', 'Delivery / Return Datetime', 'Aging', 'Amount', 'Charges', 'GST', 'Deductable', 'Payable', 'Arrival Date' ];

        $serial_number = 1;

        foreach ($pending_payment_shipment_ids as $pending_payment_shipment_id) {
            $pending_payment_shipment = PendingPaymentShipment::find($pending_payment_shipment_id);

            $shipment = $pending_payment_shipment->shipment;

            if ($pending_payment_shipment->type == 0) {
                $type = 'Delivered';
            } else if ($pending_payment_shipment->type == 1) {
                $type = 'Returned';
            } else {
                $type = 'Adjusted';
            }

            $now = Carbon::now()->startOfDay();
            $created_at = Carbon::parse($pending_payment_shipment->created_at)->startOfDay();
            $aging = $created_at->diffInDays($now) . 'd';
            $arrival_date = ShipmentsJourney::where('shipment_id',$shipment->id)->where('shipper_status_id',2)->first();

            $row = array();

            $row[] = $serial_number;
            $row[] = $shipment->user->name;
            $row[] = $shipment->tracking_number;
            $row[] = $shipment->pickup_address->city->name;
            $row[] = $type;
            $row[] = $shipment->status_shipper->name;
            $row[] = $pending_payment_shipment->created_at;
            $row[] = $aging;
            $row[] = $pending_payment_shipment->amount;
            $row[] = $pending_payment_shipment->charges;
            $row[] = $pending_payment_shipment->gst;
            $row[] = ($pending_payment_shipment->charges + $pending_payment_shipment->gst);
            $row[] = $pending_payment_shipment->payable;
            $row[] = ($arrival_date) ? $arrival_date->created_at : '';

            $serial_number++;

            $details[] = $row;
        }

        $filename .= '.xlsx';

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->getStyle('C')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('H')->getNumberFormat()->setFormatCode('#,##0');
        $spreadsheet->getActiveSheet()->getStyle('I')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('J')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('K')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('L')->getNumberFormat()->setFormatCode('#,##0.00');

        $spreadsheet->getActiveSheet()->fromArray($details);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function make_payments_verify(Request $request)
    {
        $settings = DB::connection('reports')->table('global_settings')->where('type', 'over_payment_limit')->first();

        if ($settings) {
            $over_payment_limit = $settings->setting_value;
        } else {
            $over_payment_limit = 6000000;
        }

        $pending_payment_shipment_ids = explode(',', $request->pending_payment_shipment_ids);

        $pending_payment_payables = array();

        $shipment_ids = array();
        $duplicate_shipment_ids = array();
        $duplicate_shipments = array();

        foreach ($pending_payment_shipment_ids as $pending_payment_shipment_id) {
            $pending_payment_shipment = PendingPaymentShipment::find($pending_payment_shipment_id);

            if ($pending_payment_shipment) {
                if (!isset($pending_payment_payables[$pending_payment_shipment->pending_payment_id])) {
                    $pending_payment_payables[$pending_payment_shipment->pending_payment_id] = $pending_payment_shipment->payable;
                } else {
                    $pending_payment_payables[$pending_payment_shipment->pending_payment_id] = $pending_payment_payables[$pending_payment_shipment->pending_payment_id] + $pending_payment_shipment->payable;
                }

                $shipment_id = $pending_payment_shipment->shipment_id;
                $type = $pending_payment_shipment->type;

                if (!isset($shipment_ids[$type]) || !in_array($shipment_id, $shipment_ids[$type])) {
                    $shipment_ids[$type][] = $shipment_id;
                } else {
                    if (!isset($duplicate_shipment_ids[$type]) || !in_array($shipment_id, $duplicate_shipment_ids[$type])) {
                        $duplicate_shipment_ids[$type][] = $shipment_id;

                        $shipment = Shipment::find($shipment_id);

                        $duplicate_shipment = $shipment->tracking_number . ' - ';

                        if ($type == 0) {
                            $duplicate_shipment .= 'Delivered';
                        } else if ($type == 1) {
                            $duplicate_shipment .= 'Returned';
                        } else {
                            $duplicate_shipment .= 'Adjusted';
                        }
                        $duplicate_shipments[] = $duplicate_shipment;
                    }
                }
            }
        }

        $shipper_ids = array();

        $negative_payments = array();

        $over_payments = array();

        foreach ($pending_payment_payables as $pending_payment_id => $payable) {
            $pending_payment_shipper = PendingPayment::find($pending_payment_id)->shipper;

            $shipper_ids[] = $pending_payment_shipper->id;

            if ($payable < 0) {
                $negative_payments[] = $pending_payment_shipper->name;
            } else if ($payable > $over_payment_limit) {
                $over_payment = array();

                $over_payment['shipper'] = $pending_payment_shipper->name;
                $over_payment['payable'] = number_format($payable);

                $over_payments[] = $over_payment;
            }
        }

        if (empty($over_payments)) {
            $over_payments = false;
        }

        if (empty($negative_payments)) {
            if (empty($duplicate_shipments)) {
                foreach ($shipper_ids as $shipper_id) {
                    $merged_account = MergedSisterAccount::where('user_id', $shipper_id);

                    if ($merged_account->exists()) {
                        $merged_account = $merged_account->first();

                        $merged_accounts = MergedSisterAccount::where('merged_head_id', $merged_account->merged_head_id)->get();

                        $merged_account_negative = array();

                        foreach ($merged_accounts as $merge_account) {
                            $pending_payment_shipper = PendingPayment::where('user_id', $merge_account->user_id);

                            if ($pending_payment_shipper->exists()) {
                                $pending_payment_shipper = $pending_payment_shipper->first();

                                $payable = PendingPaymentShipment::where('pending_payment_id', $pending_payment_shipper->id)->sum('payable');

                                if ($payable < 0) {
                                    $merged_account_negative['shipper'] = User::find($shipper_id)->name;
                                    $merged_account_negative['merged_account'][] = $pending_payment_shipper->shipper->name;
                                    $merged_account_negative['payable'][] = $payable;
                                }
                            }
                        }

                        if (!empty($merged_account_negative)) {
                            return ['status' => 2, 'negative_payments' => false, 'duplicate_shipments' => false, 'merged_account_negative' => $merged_account_negative];
                        }
                    }
                }

                return ['status' => 0, 'negative_payments' => false, 'duplicate_shipments' => false, 'over_payments' => $over_payments];
            } else {
                return ['status' => 0, 'negative_payments' => false, 'duplicate_shipments' => $duplicate_shipments, 'over_payments' => $over_payments];
            }
        } else {
            return ['status' => 1, 'negative_payments' => $negative_payments];
        }
    }

    public function make_payments_export_bank_order(Request $request)
    {
        $done_payment_ids = explode(',', $request->done_payment_ids);

        $filename = 'sonic_bank_order';

        $details = array();

        $details[] = ['Payment ID', 'Client', 'Account Title', 'IBAN', 'Bank', 'Payable'];

        foreach ($done_payment_ids as $done_payment_id) {
            $filename .= '_' . $done_payment_id;

            $done_payment = DonePayment::find($done_payment_id);

            $shipper = User::find($done_payment->user_id);

            if ($done_payment->user_bank_info_id == NULL) {
                $shipper_bank = UserBankInfo::where('user_id', $shipper->id)->where('default_bank', 1)->first();
            } else {
                $shipper_bank = UserBankInfo::find($done_payment->user_bank_info_id);
            }

            $payable = number_format(ROUND((DonePaymentShipment::where('done_payment_id', $done_payment_id)->sum('payable') - $done_payment->ibft_charges), 0, PHP_ROUND_HALF_DOWN));

            $row = array();

            $row[] = str_pad($done_payment->id, 6, '0', STR_PAD_LEFT);
            $row[] = $shipper->name;
            $row[] = $shipper_bank->account_title;
            $row[] = $shipper_bank->iban;
            $row[] = $shipper_bank->bank->name;
            $row[] = $payable;

            $details[] = $row;
        }

        $filename .= '.xlsx';

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode('#,##0');

        $spreadsheet->getActiveSheet()->fromArray($details);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function make_payments_store(Request $request)
    {

        $pending_payment_shipment_ids = PendingPaymentShipment::whereIn('id', explode(',', $request->pending_payment_shipment_ids))->select('pending_payment_id', 'id')->get()->mapToGroups(function ($item, $key) {
            return [$item['pending_payment_id'] => $item['id']];
        })->toArray();
        $company_bank = $request->get('company_bank_id');

        $done_payment_ids = array();
//        $present_consolidation_shipments = array();
//        if(ConsolidationShipments::whereIn('shipment_id', $pending_payment_shipment_ids)->exists()){
//            foreach ($pending_payment_shipment_ids as $shipment_id){
//                $present = ConsolidationShipments::where('shipment_id', $shipment_id);
//                if($present->exists()){
//                    $present = $present->first();
//                    $consolidation_id = $present->consolidation_id;
//
//                }
//            }
//        }


        foreach ($pending_payment_shipment_ids as $pending_payment_id => $pending_payment_shipment_ids) {
            $total_shipments = PendingPaymentShipment::where('pending_payment_id', $pending_payment_id)->count();
            $selected_shipments = count($pending_payment_shipment_ids);

            $pending_payment = PendingPayment::find($pending_payment_id);

            if ($pending_payment) {
                $user_bank_id = NULL;

                if ($request->has('make_payments_pickup_wise')) {
                    $pickup_address_map = PickupAddressIbanMapping::where('pickup_address_id', $request->pickup_address_id)->select('bank_info_id as id');
                    if ($pickup_address_map->exists()) {
                        $user_bank_id = $pickup_address_map->first();
                    } else {
                        $user_bank_id = UserBankInfo::where('user_id', $pending_payment->user_id)->where('default_bank', 1)->select('id')->first();
                    }
                } else {
                    $user_bank_id = UserBankInfo::where('user_id', $pending_payment->user_id)->where('default_bank', 1)->select('id')->first();

                }

                if ($user_bank_id) {
                    $user_bank_id = $user_bank_id->id;
                }

                if ($total_shipments == $selected_shipments) {


                    $done_payment = new DonePayment();

                    $done_payment->user_id = $pending_payment->user_id;
                    $done_payment->total_shipments = $pending_payment->total_shipments;
                    $done_payment->delivered_shipments = $pending_payment->delivered_shipments;
                    $done_payment->returned_shipments = $pending_payment->returned_shipments;
                    $done_payment->adjusted_shipments = $pending_payment->adjusted_shipments;
                    $done_payment->user_bank_info_id = $user_bank_id;
                    $done_payment->company_bank_id = $company_bank;


                    $settings = GlobalSettings::where('type', 'ibft_charges');

                    if ($settings->exists()) {
                        $settings = $settings->first();

                        $done_payment->ibft_charges = $settings->setting_value;
                    }

                    $done_payment->save();

                    $pending_payment->delete();

                    foreach ($pending_payment_shipment_ids as $pending_payment_shipment_id) {
                        $pending_payment_shipment = PendingPaymentShipment::find($pending_payment_shipment_id);

                        if ($pending_payment_shipment) {
                            $done_payment_shipment = new DonePaymentShipment();

                            $done_payment_shipment->created_at = $pending_payment_shipment->created_at;
                            $done_payment_shipment->done_payment_id = $done_payment->id;
                            $done_payment_shipment->shipment_id = $pending_payment_shipment->shipment_id;
                            $done_payment_shipment->type = $pending_payment_shipment->type;
                            $done_payment_shipment->amount = $pending_payment_shipment->amount;
                            $done_payment_shipment->charges = $pending_payment_shipment->charges;
                            $done_payment_shipment->gst = $pending_payment_shipment->gst;
                            $done_payment_shipment->wht = $pending_payment_shipment->wht;
                            $done_payment_shipment->payable = $pending_payment_shipment->payable;
                            $done_payment->company_bank_id = $company_bank;

                            $done_payment_shipment->save();

                            $packaging_material_charges = 0;
                            $adjustment_amount = 0;
                            if ($pending_payment_shipment->type == 2) {
                                $adjustment_amount = $pending_payment_shipment->payable;
                            }
                            $shipment = Shipment::find($pending_payment_shipment->shipment_id);

                            if ($shipment->packaging_material_request) {
                                $packaging_material_charges = $shipment->packaging_material_charges;
                                if ($packaging_material_charges == null) {
                                    $packaging_material_charges = 0;
                                }
                            }

                            self::add_done_payment_charges($done_payment->id, $pending_payment_shipment->amount, $pending_payment_shipment->charges, $pending_payment_shipment->gst, $pending_payment_shipment->payable, $packaging_material_charges, $adjustment_amount, null, $pending_payment_shipment->wht);
                            $pending_payment_shipment->delete();

                            self::adjustment_logs_done(1, $pending_payment_shipment_id, $done_payment_shipment->id);

                            if ($done_payment_shipment->type == 0) {
                                $shipment = Shipment::find($pending_payment_shipment->shipment_id);

                                $shipment->payment_status_id = 1;

                                $shipment->save();

                                ShipmentsPaymentJourneyController::add($shipment->id, 1, Auth::id(), '', $done_payment->id);
                            } else if ($done_payment_shipment->type == 1) {
                                $shipment = Shipment::find($pending_payment_shipment->shipment_id);

                                $shipment->payment_status_id = 5;

                                $shipment->save();

                                ShipmentsPaymentJourneyController::add($shipment->id, 5, Auth::id(), '', $done_payment->id);
                            }
                        }
                    }

                    $done_payment_ids[] = $done_payment->id;

                    NotificationsController::send(20, $done_payment->id);
                } else {
                    $done_payment = new DonePayment();

                    $done_payment->user_id = $pending_payment->user_id;
                    $done_payment->total_shipments = 0;
                    $done_payment->delivered_shipments = 0;
                    $done_payment->returned_shipments = 0;
                    $done_payment->adjusted_shipments = 0;
                    $done_payment->user_bank_info_id = $user_bank_id;
                    $done_payment->company_bank_id = $company_bank;
                    $settings = GlobalSettings::where('type', 'ibft_charges');

                    if ($settings->exists()) {
                        $settings = $settings->first();

                        $done_payment->ibft_charges = $settings->setting_value;
                    }

                    $done_payment->save();

                    $total_shipments = 0;
                    $delivered_shipments = 0;
                    $returned_shipments = 0;
                    $adjusted_shipments = 0;

                    foreach ($pending_payment_shipment_ids as $pending_payment_shipment_id) {
                        $pending_payment_shipment = PendingPaymentShipment::find($pending_payment_shipment_id);

                        if ($pending_payment_shipment) {
                            $total_shipments++;

                            if ($pending_payment_shipment->type == 0) {
                                $delivered_shipments++;
                            } else if ($pending_payment_shipment->type == 1) {
                                $returned_shipments++;
                            } else {
                                $adjusted_shipments++;
                            }

                            $done_payment_shipment = new DonePaymentShipment();

                            $done_payment_shipment->created_at = $pending_payment_shipment->created_at;
                            $done_payment_shipment->done_payment_id = $done_payment->id;
                            $done_payment_shipment->shipment_id = $pending_payment_shipment->shipment_id;
                            $done_payment_shipment->type = $pending_payment_shipment->type;
                            $done_payment_shipment->amount = $pending_payment_shipment->amount;
                            $done_payment_shipment->charges = $pending_payment_shipment->charges;
                            $done_payment_shipment->gst = $pending_payment_shipment->gst;
                            $done_payment_shipment->wht = $pending_payment_shipment->wht;
                            $done_payment_shipment->payable = $pending_payment_shipment->payable;
                            $done_payment->company_bank_id = $company_bank;

                            $done_payment_shipment->save();

                            $packaging_charges = 0;

                            $packaging_material_charges = 0;

                            $shipment = Shipment::find($pending_payment_shipment->shipment_id);

                            if ($shipment->packaging_material_request) {
                                $packaging_material_charges = $shipment->packaging_material_charges;
                                if ($packaging_material_charges == null) {
                                    $packaging_material_charges = 0;
                                }
                            }

                            $adjustment_amount = 0;
                            if ($pending_payment_shipment->type == 2) {
                                $adjustment_amount = $pending_payment_shipment->amount;
                            }

                            self::add_done_payment_charges($done_payment->id, $pending_payment_shipment->amount, $pending_payment_shipment->charges, $pending_payment_shipment->gst, $pending_payment_shipment->payable, $packaging_material_charges, $adjustment_amount, null, $pending_payment_shipment->wht);

                            self::adjustment_logs_done(1, $pending_payment_shipment_id, $done_payment_shipment->id);

                            $pending_payment_shipment->delete();

                            self::sub_pending_payment_charges($pending_payment_shipment->pending_payment_id, $pending_payment_shipment->amount, $pending_payment_shipment->charges, $pending_payment_shipment->gst, $pending_payment_shipment->payable, null, $pending_payment_shipment->wht);

                            if ($done_payment_shipment->type == 1) {
                                $shipment = Shipment::find($pending_payment_shipment->shipment_id);

                                $shipment->payment_status_id = 5;

                                $shipment->save();

                                ShipmentsPaymentJourneyController::add($shipment->id, 5, Auth::id(), '', $done_payment->id);
                            } else {
                                $shipment = Shipment::find($pending_payment_shipment->shipment_id);

                                $shipment->payment_status_id = 1;

                                $shipment->save();

                                ShipmentsPaymentJourneyController::add($shipment->id, 1, Auth::id(), '', $done_payment->id);
                            }
                        }
                    }

                    $done_payment->total_shipments = $total_shipments;
                    $done_payment->delivered_shipments = $delivered_shipments;
                    $done_payment->returned_shipments = $returned_shipments;
                    $done_payment->adjusted_shipments = $adjusted_shipments;
                    $done_payment->user_bank_info_id = $user_bank_id;


                    $done_payment->save();

                    $pending_payment->total_shipments = PendingPaymentShipment::where('pending_payment_id', $pending_payment_id)->count();
                    $pending_payment->delivered_shipments = PendingPaymentShipment::where('pending_payment_id', $pending_payment_id)->where('type', 0)->count();
                    $pending_payment->returned_shipments = PendingPaymentShipment::where('pending_payment_id', $pending_payment_id)->where('type', 1)->count();
                    $pending_payment->adjusted_shipments = PendingPaymentShipment::where('pending_payment_id', $pending_payment_id)->where('type', 2)->count();

                    $pending_payment->save();

                    $done_payment_ids[] = $done_payment->id;

                    NotificationsController::send(20, $done_payment->id);
                }
            }
        }

        return redirect()->back()->with(['success' => 'Payment(s) has been Made.', 'print' => $done_payment_ids]);
    }

    public function make_payments_stats_calculate(Request $request)
    {
        $total_amount = 0;
        $total_charges = 0;
        $total_payable = 0;

        if ($positive_negative_filter = $request->get('positive_negative_filter')) {
            if (PendingPayment::exists()) {
                foreach (PendingPayment::get() as $pending_payment) {
                    $payable = PendingPaymentShipment::where('pending_payment_id', $pending_payment->id)->sum('payable');

                    if ($positive_negative_filter == 1 && $payable >= 0) {
                        $total_amount += PendingPaymentShipment::where('pending_payment_id', $pending_payment->id)->sum('amount');
                        $total_charges += PendingPaymentShipment::where('pending_payment_id', $pending_payment->id)->sum('charges');
                        $total_payable += $payable;
                    } else if ($positive_negative_filter == 2 && $payable < 0) {
                        $total_amount += PendingPaymentShipment::where('pending_payment_id', $pending_payment->id)->sum('amount');
                        $total_charges += PendingPaymentShipment::where('pending_payment_id', $pending_payment->id)->sum('charges');
                        $total_payable += $payable;
                    }
                }
            } else {
                $total_amount = PendingPaymentShipment::sum('amount');
                $total_charges = PendingPaymentShipment::sum('charges');
                $total_payable = PendingPaymentShipment::sum('payable');
            }
        } else {
            $total_amount = PendingPaymentShipment::sum('amount');
            $total_charges = PendingPaymentShipment::sum('charges');
            $total_payable = PendingPaymentShipment::sum('payable');
        }

        return ['total_amount' => $total_amount, 'total_charges' => $total_charges, 'total_payable' => $total_payable];
    }

    static public function done_payment($shipment_id, $type)
    {
        $shipment = Shipment::find($shipment_id);
        if ($shipment->shipment_type == 1) {
            $user_bank_id = NULL;

            $user_bank_id = UserBankInfo::where('user_id', $shipment->user_id)->where('default_bank', 1)->select('id')->first();

            if ($user_bank_id) {
                $user_bank_id = $user_bank_id->id;
            }

            $done_payment = new DonePayment();

            $done_payment->user_id = $shipment->user_id;
            $done_payment->total_shipments = 1;

            if ($type == 0) {
                $done_payment->delivered_shipments = 1;
            } else {
                $done_payment->returned_shipments = 1;
            }
            $done_payment->user_bank_info_id = $user_bank_id;
            $done_payment->status = 1;

            $done_payment->save();
            if ($type == 0) {
                $charges = ($shipment->amount - $shipment->gst);
            } else {
                $charges = ($shipment->received_amount - $shipment->gst);
            }

            $done_payment_shipment = new DonePaymentShipment();

            $done_payment_shipment->done_payment_id = $done_payment->id;
            $done_payment_shipment->shipment_id = $shipment_id;
            $done_payment_shipment->type = $type;
            $done_payment_shipment->amount = 0;
            $done_payment_shipment->charges = $charges;
            $done_payment_shipment->gst = $shipment->gst;
            $done_payment_shipment->payable = $shipment->amount;

            $done_payment_shipment->save();

            $shipment->payment_status_id = 7;

            $shipment->save();
            $packaging_material_charges = 0;
            $adjustment_amount = 0;
            $shipment = Shipment::find($shipment_id);

            if ($shipment->packaging_material_request) {
                $packaging_material_charges = $shipment->packaging_material_charges;
                if ($packaging_material_charges == null) {
                    $packaging_material_charges = 0;
                }
            }
            self::add_done_payment_charges($done_payment->id, 0, $charges, $shipment->gst, $shipment->amount, $packaging_material_charges, $adjustment_amount);
            ShipmentsPaymentJourneyController::add($shipment->id, 5, Auth::id(), '', $done_payment->id);
            ShipmentsPaymentJourneyController::add($shipment->id, 7, Auth::id(), '', $done_payment->id);
        } else {
            if ($type == 0) {
                $retail_shipment = RetailShipment::where('shipment_id', $shipment->id)->first();

                if ($retail_shipment->shipping_mode == 3) {

                    $done_payment = new DonePayment();

                    $done_payment->user_id = $retail_shipment->shipper_account_no;
                    $done_payment->total_shipments = 1;
                    $done_payment->delivered_shipments = 1;
                    $done_payment->status = 1;

                    $done_payment->save();

                    $done_payment_shipment = new DonePaymentShipment();

                    $done_payment_shipment->retail_done_payment_id = $done_payment->id;
                    $done_payment_shipment->shipment_id = $shipment_id;
                    $done_payment_shipment->type = $type;
                    $done_payment_shipment->amount = 0;
                    $done_payment_shipment->payable = $shipment->amount;

                    $done_payment_shipment->save();

                    $shipment->payment_status_id = 7;

                    $shipment->save();
                    $adjustment_amount = 0;
                    self::add_done_payment_charges($done_payment->id, 0, 0, 0, $shipment->amount, 0, $adjustment_amount, 1);
                    ShipmentsPaymentJourneyController::add($shipment->id, 5, Auth::id(), '', $done_payment->id, 1);
                    ShipmentsPaymentJourneyController::add($shipment->id, 7, Auth::id(), '', $done_payment->id, 1);
                }
            }
        }
    }

    public function done_payments_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 30);
        if (session('department_id') == 7 && !in_array(session('id'), session('sale_users_bypass'))) {
            $shippers = User::whereIn('id', session('tagged_shippers'))->select('id', 'name')->get();
        } else {
            $shippers = User::select('id', 'name')->get();
        }

        $shipper_status = [1 => 'Active', 2 => 'Inactive'];

        $banks = BanksList::all();
        $company_banks = BanksList::where('affiliate', 1)->get();
        $case_nature_channels = CrmRequestChannel::where('id', '!=', 1)->get();

        return view('admin.finance.done_payments')->with(['banks' => $banks, 'company_banks' => $company_banks, 'shippers' => $shippers, 'case_nature_channels' => $case_nature_channels, 'shipper_status' => $shipper_status]);
    }

    public function done_payments_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 90);
        }

        $count = DB::table('done_payments');

        $count = $count->join('users as u', 'done_payments.user_id', '=', 'u.id');

        if (session('department_id') == 7) {
            if (!in_array(session('id'), session('sale_users_bypass'))) {
                $count = $count->where(function ($query) {
                    $query->whereIn('u.id', session('tagged_shippers'));
                });
            }
        } else if (session('role_id') != 1) {
            $count = $count->join('cities as c', 'u.city_id', '=', 'c.id')
                ->whereIn('c.hub_id', session('hubs'));
        }

        if ($tracking_numbers = $request->get('tracking_numbers')) {
            $count = $count->join('done_payment_shipments as dps', 'done_payments.id', '=', 'dps.done_payment_id')
                ->join('shipments as ss', 'dps.shipment_id', '=', 'ss.id')
                ->whereIn('ss.tracking_number', explode(',', $tracking_numbers))
                ->groupby('done_payments.id');
        }

        if ($payment_ids = $request->get('search_payment_ids')) {
            $count = $count->whereIn('done_payments.id', explode(',', $payment_ids));
        }

        if ($shipper = $request->get('search_shipper')) {
            $count = $count->where('u.id', '=', $shipper);
        }

        if ($shipper_status = $request->get('search_shipper_status')) {
            if ($shipper_status == 1) {
                $count = $count->where('u.status', '=', 3)->where('u.blacklist', 0);
            } else {
                $count = $count->where('u.status', '!=', 3);
            }
        }

        if ($request->get('search_from') && $request->get('search_to')) {
            $from = $request->get('search_from');
            $to = $request->get('search_to');
            $count = $count->whereBetween('done_payments.created_at', [$from, $to]);
        }

        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $count = $count->whereBetween('done_payments.status_updated_at', [$from, $to]);
        }

        $count = $count->count();

        $done_payments = DonePayment::
//        with('VisionSoftCodPaymentClear','shipment_payment_journey_last_status_two')
        join('users as u', 'done_payments.user_id', '=', 'u.id')
            ->join('cities as c', 'u.city_id', '=', 'c.id')
            ->leftjoin('done_payment_calculations as dpc', 'dpc.done_payment_id', '=', 'done_payments.id')
            ->leftJoin('admins as ad', function ($join) {
                $join->on('ad.id', '=', 'done_payments.status_updated_by');
            })
            ->leftJoin('user_bank_infos as ubi', function ($join) {
                $join->on('ubi.id', '=', 'done_payments.user_bank_info_id');
            })
            ->leftJoin('user_bank_infos as ubi_default', function ($join) {
                $join->on('ubi_default.user_id', '=', 'u.id')
                    ->where('ubi_default.default_bank', DB::raw(1));
            })
            ->leftJoin('banks_lists as ub', function ($join) {
                $join->where(function ($sub_query) {
                    $sub_query->whereNotNull('done_payments.user_bank_info_id')
                        ->where('ubi.bank_name', '=', DB::raw('`ub`.`id`'));
                })->orWhere(function ($sub_query) {
                    $sub_query->whereNull('done_payments.user_bank_info_id')
                        ->where('ubi_default.bank_name', '=', DB::raw('`ub`.`id`'));
                });
            })
            ->leftjoin('banks_lists as b', 'done_payments.company_bank_id', '=', 'b.id')
            ->select('done_payments.user_id as user_id', 'done_payments.id as id', 'done_payments.id as payment_id', 'u.name as shipper', 'c.name as city', 'u.phone', 'u.phone2', 'u.address', 'done_payments.total_shipments', 'done_payments.delivered_shipments', 'done_payments.delivered_shipments as delivered_shipments_count', 'done_payments.returned_shipments', 'done_payments.returned_shipments as returned_shipments_count', 'done_payments.adjusted_shipments', 'done_payments.adjusted_shipments as adjusted_shipments_count', 'dpc.amount as total_amount', 'dpc.charges as total_charges', 'dpc.gst as total_gst', 'dpc.payable as total_payable', 'ub.name as bank', 'done_payments.reference_number', 'done_payments.created_at as done_at', 'b.name as company_bank', 'done_payments.status', 'done_payments.ibft_charges', 'dpc.packaging_charges', 'dpc.adjustment as adjustment_charges', 'done_payments.status_updated_at as status_updated_at', 'dpc.wht as total_wht', 'done_payments.created_at as start_date', 'done_payments.updated_at as end_date', 'ad.name as admin_name', 'done_payments.updated_at as updated_at');

        if (session('department_id') == 7) {
            if (!in_array(session('id'), session('sale_users_bypass'))) {
                $done_payments = $done_payments->where(function ($query) {
                    $query->whereIn('u.id', session('tagged_shippers'));
                });
            }
        } else if (session('role_id') != 1) {
            $done_payments = $done_payments->whereIn('c.hub_id', session('hubs'));
        }


        $datatables = Datatables::of($done_payments)
            ->setTotalRecords($count)
            ->addColumn('id_padded', function ($done_payment) {
                return str_pad($done_payment->id, 6, '0', STR_PAD_LEFT);
            })
            ->addColumn('user_id_padded', function ($done_payment) {
                return str_pad($done_payment->user_id, 6, '0', STR_PAD_LEFT);
            })
            ->filterColumn('done_payments.id', function ($query, $keyword) {
                return $query->where('done_payments.id', '=', $keyword);
            })
            ->editColumn('payment_id', function ($done_payment) {
                return '<button class="btn btn-sm btn-outline-info align-middle"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($done_payment->id, 6, '0', STR_PAD_LEFT) . '</span></button>';
            })
            ->addColumn('total_deductable', function ($done_payment) {
                return number_format(($done_payment->total_charges + $done_payment->total_gst + $done_payment->ibft_charges - $done_payment->total_wht), 2);
            })
            ->editColumn('delivered_shipments', function ($done_payment) {
                if ($done_payment->delivered_shipments != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $done_payment->delivered_shipments . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('returned_shipments', function ($done_payment) {
                if ($done_payment->returned_shipments != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $done_payment->returned_shipments . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('adjusted_shipments', function ($done_payment) {
                if ($done_payment->adjusted_shipments != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $done_payment->adjusted_shipments . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('total_amount', function ($done_payment) {
                return number_format($done_payment->total_amount, 2);
            })
            ->editColumn('total_charges', function ($done_payment) {
                return number_format(($done_payment->total_charges + $done_payment->ibft_charges), 2);
            })
            ->editColumn('total_gst', function ($done_payment) {
                return number_format($done_payment->total_gst, 2);
            })
            ->editColumn('packaging_charges', function ($done_payment) {
                return number_format($done_payment->packaging_charges, 2);
            })
            ->editColumn('adjustment_charges', function ($done_payment) {
                return number_format($done_payment->adjustment_charges, 2);
            })
            ->editColumn('total_payable', function ($done_payment) {
                return number_format(ROUND($done_payment->total_payable - $done_payment->ibft_charges, 0, PHP_ROUND_HALF_DOWN));
            })
            ->addColumn('phone_numbers', function ($done_payment) {
                $phone_numbers = $done_payment->phone;

                if (!empty($done_payment->phone2)) {
                    $phone_numbers .= ' - ' . $done_payment->phone2;
                }

                return $phone_numbers;
            })
            ->removeColumn('phone')
            ->removeColumn('phone2')
            ->editColumn('status', function ($done_payment) {
                if ($done_payment->status == 0) {
                    return 'Processed';
                } else if ($done_payment->status == 1) {
                    return 'Paid';
                } else if ($done_payment->status == 2) {
                    return 'Reverted';
                } else {
                    return 'Unknown';
                }
            })
//            ->addColumn('aging', function ($done_payment) {
//
//                if ($done_payment->status == 0) {
//
////                    $start_date = date_create($done_payment->updated_at);
//                    $start_date = date('d-m-Y H:i:s', strtotime($done_payment->updated_at));
//                    $end_date = date('d-m-Y H:i:s', strtotime(Carbon::now()));
//                    $start_date = Carbon::parse($start_date);
//                    $end_date = Carbon::parse($end_date);
//                    $interval = $end_date->diffInHours($start_date);
//                    return $interval . ' Hrs';
//
////                    $start_date = isset($done_payment->start_date) ? date('Y-m-d H:i:s', strtotime($done_payment->start_date)) : '';
////                    $end_date = isset($done_payment->VisionSoftCodPaymentClear->created_at) ? date('Y-m-d H:i:s', strtotime($done_payment->VisionSoftCodPaymentClear->created_at)) : date('Y-m-d H:i:s', strtotime($done_payment->end_date));
////
////                    if (!empty($end_date) && !empty($start_date)) {
////
////                        $datetime1 = date_create($start_date);
////                        $datetime2 = date_create($end_date);
////
////                        // Calculates the difference between DateTime objects
////                        $interval = date_diff($datetime1, $datetime2);
//////                        return $interval->format('%m months, %d days,%h hours and %i mints');
////                        return $interval->format('%h hours and %i mints');
////                    } else {
////                        return '-';
////                    }
//                } elseif ($done_payment->status == 2) {
////                    $done_payment = ShipmentsPaymentJourney::with('done_payment')
////                        ->where('status_id', 2)
////                        ->where('payment_id', 54)
////                        ->groupBy('status_id')
////                        ->orderBy('status_id', 'DESC')
////                        ->select('shipments_payment_journey.id', 'shipments_payment_journey.status_id', 'shipments_payment_journey.created_at as date')->get();
//
////                    dd($done_payment->shipment_payment_journey_last_status_two[0]->created_at);
//                    if(isset($done_payment->shipment_payment_journey_last_status_two)) {
//
////                    $start_date = (isset($done_payment->shipment_payment_journey_last_status_two[0]->created_at)) ? date('d-m-Y H:i:s', strtotime($done_payment->shipment_payment_journey_last_status_two[0]->created_at)) : $done_payment->updated_at;
//
//                        $start_date = $done_payment->shipment_payment_journey_last_status_two->created_at;
//                        $end_date = date('d-m-Y H:i:s', strtotime(Carbon::now()));
//                        $start_date = Carbon::parse($start_date);
//                        $end_date = Carbon::parse($end_date);
//                        $interval = $end_date->diffInHours($start_date);
//
//                    }
//                    return $interval . ' Hrs';
//
//                } else {
//                    return '-';
//                }
//
//            })
            ->addColumn('updated_at', function ($done_payment) {
                if (!empty($done_payment->status_updated_at)) {
                    $updated_at = $done_payment->status_updated_at;
                    return $updated_at;
                } else {
                    return '-';
                }

            })
            ->addColumn('updated_by', function ($done_payment) {
                if (!empty($done_payment->admin_name)) {
                    $updated_by = $done_payment->admin_name;
                    return $updated_by;
                } else {
                    return '-';
                }
            })
            ->filterColumn('bank', function ($query, $keyword) {

                if ($keyword != '') {
                    $query->where('ub.id', '=', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('company_bank', function ($query, $keyword) {

                if ($keyword != '') {
                    $query->where('b.id', '=', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn('action', function ($done_payment) {
                $dropdown = '<div class="btn-group">
                  <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                  <div class="dropdown-menu dropdown-menu-sm">
                    <button type="button" class="dropdown-item view_details"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-file-text"></i></div><div class="col-9 offset-1">View Details</div></button>
                    <button type="button" class="dropdown-item view_status_history"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-file-text"></i></div><div class="col-9 offset-1">View Status History</div></button>';

                if (session('role_id') == 1 || session('department_id') == 4) {
                    $dropdown .= '<button type="button" class="dropdown-item update_details"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Update Details</div></button>';
                }

                $dropdown .= '<button type="button" class="dropdown-item export_to_excel"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-download"></i></div><div class="col-9 offset-1">Export to Excel</div></button>
                    <button type="button" class="dropdown-item request_add"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Add Request</div></button>
                  </div>
                </div>
            ';
                return $dropdown;
            })
            ->filterColumn('phone_numbers', function ($query, $keyword) {
                $search = str_replace('-', '', $keyword);

                if ($keyword != '') {
                    $query->where(function ($sub_query) use ($keyword) {
                        $sub_query->where('u.phone', 'like', '%' . $keyword . '%')
                            ->orWhere('u.phone2', 'like', '%' . $keyword . '%');
                    });
                } else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('status', function ($query, $keyword) {
                $keyword = strtolower($keyword);

                if ($keyword == 0) {
                    $query->where('done_payments.status', '=', 0);
                } else if ($keyword == 1) {
                    $query->where('done_payments.status', '=', 1);
                } else if ($keyword == 2) {
                    $query->where('done_payments.status', '=', 2);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->orderColumn('phone_numbers', 'u.phone $1, u.phone2 $1');

        if ($tracking_numbers = $request->get('tracking_numbers')) {
            $datatables->join('done_payment_shipments as dps', 'done_payments.id', '=', 'dps.done_payment_id')
                ->join('shipments as ss', 'dps.shipment_id', '=', 'ss.id')
                ->whereIn('ss.tracking_number', explode(',', $tracking_numbers))
                ->groupby('done_payments.id');
        }
        if ($payment_ids = $request->get('search_payment_ids')) {
            $datatables->whereIn('done_payments.id', explode(',', $payment_ids));
        }

        if ($shipper = $request->get('search_shipper')) {
            $datatables->where('u.id', '=', $shipper);
        }

        if ($shipper_status = $request->get('search_shipper_status')) {
            if ($shipper_status == 1) {
                $datatables->where('u.status', '=', 3)->where('u.blacklist', 0);
            } else {
                $datatables->where('u.status', '!=', 3);
            }
        }

        if ($request->get('search_from') && $request->get('search_to')) {
            $from = $request->get('search_from');
            $to = $request->get('search_to');
            $datatables->whereBetween('done_payments.created_at', [$from, $to]);
        }

        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatables->whereBetween('done_payments.status_updated_at', [$from, $to]);
        }


//         dd($datatables->make(true));
        return $datatables->make(true);


    }

    public function view_status_history(Request $request)
    {
        if (!empty($request->id)) {

            $status_history = array();
//            $done_payments = DonePayment::
//                 join('shipments_payment_journey as spj', 'spj.payment_id', '=', 'done_payments.id')
//                ->leftjoin('admins', 'admins.id', '=', 'spj.admin_id')
//                ->leftjoin('shipment_payment_status as sps', 'spj.status_id', '=', 'sps.id')
//                ->where('done_payments.id', $request->id)
//                ->select('done_payments.id as payment_id', 'spj.id', 'spj.status_id as status_id', 'spj.admin_id', 'spj.updated_at as updated_at', 'admins.name as admin', 'sps.name as payment_status')
//                ->get();


            $done_payments = ShipmentsPaymentJourney::with('admin', 'status')
                ->where('payment_id', $request->id)
                ->groupBy('status_id')
                ->orderBy('status_id', 'DESC')
                ->get();


            if (count($done_payments) > 0) {
                foreach ($done_payments as $key => $pj) {

                    $status_history['shipment_id'][$key] = $pj->shipment_id;
                    $status_history['admin'][$key] = isset($pj->admin->name) ? $pj->admin->name : 'N/A';
                    $status_history['payment_id'][$key] = isset($pj->payment_id) ? $pj->payment_id : '';
                    $status_history['payment_status'][$key] = isset($pj->status->name) ? $pj->status->name : '';
                    $status_history['status_updated_at'][$key] = isset($pj->updated_at) ? date('Y-m-d H:i:s', strtotime($pj->updated_at)) : '';
                    $status_history['status'][$key] = 2;


                }
//            dd($status_history);
                echo json_encode($status_history);
            } else {
                return json_encode(['status' => 0, 'error' => 'Something Went Wrong']);
            }
        } else
            return json_encode(['status' => 0, 'error' => 'Payment Not Found']);

    }

    public function done_payments_paid(Request $request)
    {
        foreach ($request->ids as $done_payment_id) {
            $done_payment = DonePayment::find($done_payment_id);

            if ($done_payment->status != 1) {
                $done_payment->status = 1;
                $done_payment->status_updated_at = Carbon::now();
                $done_payment->status_updated_by = Auth::id();

                $done_payment->save();

                $payment_clear = new VisionSoftCodPaymentClear();
                $payment_clear->payment_id = $done_payment_id;
                $payment_clear->status = 1;
                $payment_clear->save();

                foreach ($done_payment->done_payment_shipments as $done_payment_shipment) {
                    $shipment = $done_payment_shipment->shipment;

                    if ($done_payment_shipment->type == 1) {
                        $shipment->payment_status_id = 7;

                        $shipment->save();

                        ShipmentsPaymentJourneyController::add($shipment->id, 7, Auth::id(), '', $done_payment->id);
                    } else {
                        $shipment->payment_status_id = 3;

                        $shipment->save();

                        ShipmentsPaymentJourneyController::add($shipment->id, 3, Auth::id(), '', $done_payment->id);
                    }
                }
            }
        }

        return ['status' => 0, 'success' => 'Payment(s) marked Paid'];
    }

    public function done_payments_reverted(Request $request)
    {
        foreach ($request->ids as $done_payment_id) {
            $done_payment = DonePayment::find($done_payment_id);

            if ($done_payment->status != 2) {
                $done_payment->status = 2;
                $done_payment->status_updated_at = Carbon::now();
                $done_payment->status_updated_by = Auth::id();

                $done_payment->save();

                $payment_clear = new VisionSoftCodPaymentClear();
                $payment_clear->payment_id = $done_payment_id;
                $payment_clear->status = 2;
                $payment_clear->save();

                foreach ($done_payment->done_payment_shipments as $done_payment_shipment) {
                    $shipment = $done_payment_shipment->shipment;

                    if ($done_payment_shipment->type == 1) {
                        $shipment->payment_status_id = 6;

                        $shipment->save();

                        ShipmentsPaymentJourneyController::add($shipment->id, 6, Auth::id(), '', $done_payment->id);
                    } else {
                        $shipment->payment_status_id = 2;

                        $shipment->save();

                        ShipmentsPaymentJourneyController::add($shipment->id, 2, Auth::id(), '', $done_payment->id);
                        //NotificationsController::send(92,$done_payment->id ,$shipment->id);
                    }
                }

            }
            NotificationsController::send(92, $done_payment->id);
        }

        return ['status' => 0, 'success' => 'Payment(s) marked Reverted'];
    }

    public function done_payments_excel_store(Request $request)
    {
        $names = [
            'payment_id' => 'Payment ID',
            'company_bank_id' => 'Company Bank ID',
            'status' => 'Status',
        ];

        $messages = [
            'required' => ':attribute is Required.',
            'integer' => ':attribute must be an Integer.',
        ];

        $rules = [
            'payment_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('done_payments', 'id')],
            'company_bank_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('banks_lists', 'id')->where(function ($query) {
                $query->where('affiliate', DB::raw(1));
            })],
            'status' => ['required', 'string', 'in:paid,Paid,Reverted,reverted,PAID,REVERTED'],
        ];

        $fields = [0 => 'payment_id', 1 => 'company_bank_id', 2 => 'status'];

        if ($file = $request->file('payments')) {

            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

            $header = ['Payment ID', 'Company Bank ID', 'Status'];
        }

        if (isset($spreadsheet)) {
            $header_correct = TRUE;

            foreach ($spreadsheet[0] as $index => $header_value) {
                if ($index == 1) {
                } elseif (!isset($header[$index]) || $header_value != $header[$index]) {
                    $header_correct = FALSE;
                    break;
                }
            }

            if (!$header_correct) {
                return redirect()->back()->with('error', 'Invalid Columns, Kindly follow the Template provided');
            } else {
                unset($spreadsheet[0]);
            }
        }

        if (!isset($spreadsheet) || !empty($spreadsheet)) {
            $rows = array();

            if (isset($spreadsheet)) {
                foreach ($spreadsheet as $spreadsheet_row) {
                    $row = array();

                    foreach ($spreadsheet_row as $key => $value) {
                        $row[$fields[$key]] = $value;
                    }

                    $rows[] = $row;
                }

                unset($spreadsheet);
            }
            foreach ($rows as $key => $row) {
                $row_id = $key + 2;

                $validate = Validator::make($row, $rules, $messages);

                $validate->setAttributeNames($names);

                if ($validate->fails()) {
                    $errors['Row #' . $row_id] = $validate->errors()->all();
                }
            }
            if (isset($errors)) {
                $errors = array_map(function ($row, $errors) {
                    return $row . ':' . PHP_EOL . implode(' | ', $errors);
                }, array_keys($errors), $errors);
                return redirect()->back()->withErrors($errors);
            } else {
                foreach ($rows as $key => $row) {
                    $payment_id = (int)$row['payment_id'];
                    $done_payment = DonePayment::find($payment_id);
                    $status = strtolower($row['status']);
                    if ($status == "paid") {
                        if ($done_payment->status != 1) {
                            $payment_clear = new VisionSoftCodPaymentClear();
                            $payment_clear->payment_id = $payment_id;
                            $payment_clear->status = 1;
                            $payment_clear->save();

                            $done_payment->company_bank_id = (int)$row['company_bank_id'];
                            $done_payment->status_updated_at = Carbon::now();
                            $done_payment->status_updated_by = Auth::id();
                            $done_payment->status = 1;

                            $done_payment->save();

                            foreach ($done_payment->done_payment_shipments as $done_payment_shipment) {
                                $shipment = $done_payment_shipment->shipment;

                                if ($done_payment_shipment->type == 1) {
                                    $shipment->payment_status_id = 7;

                                    $shipment->save();

                                    ShipmentsPaymentJourneyController::add($shipment->id, 7, Auth::id(), '', $done_payment->id);
                                } else {
                                    $shipment->payment_status_id = 3;

                                    $shipment->save();

                                    ShipmentsPaymentJourneyController::add($shipment->id, 3, Auth::id(), '', $done_payment->id);
                                }
                            }
                        }
                    } elseif ($status == "reverted") {
                        if ($done_payment->status != 2 && $done_payment->status != 1) {
                            /*$payment_clear = new VisionSoftCodPaymentClear();
                            $payment_clear->payment_id = $payment_id;
                            $payment_clear->status = 2;
                            $payment_clear->save();*/

                            $done_payment->company_bank_id = (int)$row['company_bank_id'];
                            $done_payment->status_updated_at = Carbon::now();
                            $done_payment->status_updated_by = Auth::id();
                            $done_payment->status = 2;

                            $done_payment->save();

                            foreach ($done_payment->done_payment_shipments as $done_payment_shipment) {
                                $shipment = $done_payment_shipment->shipment;

                                if ($done_payment_shipment->type == 1) {
                                    $shipment->payment_status_id = 6;

                                    $shipment->save();

                                    ShipmentsPaymentJourneyController::add($shipment->id, 6, Auth::id(), '', $done_payment->id);
                                } else {
                                    $shipment->payment_status_id = 2;

                                    $shipment->save();

                                    ShipmentsPaymentJourneyController::add($shipment->id, 2, Auth::id(), '', $done_payment->id);
                                }
                            }
                        }

                        if ($done_payment->status == 2) {
                            NotificationsController::send(92, $done_payment->id);
                        }

                    }
                }
                return redirect()->back()->with(['success' => 'Status of ' . count($rows) . ' Payment(s) has been Updated']);
            }

        } else {
            return redirect()->back()->with('error', 'No Payments in File');
        }
    }

    public function done_payments_delivered_shipments(Request $request)
    {
        $tracking_numbers = array();

        $done_payment_shipments = DonePaymentShipment::where('done_payment_id', $request->id)->where('type', 0)->get();

        foreach ($done_payment_shipments as $done_payment_shipment) {
            $shipment = $done_payment_shipment->shipment;

            $tracking_numbers[] = $shipment->tracking_number;
        }

        return $tracking_numbers;
    }

    public function done_payments_returned_shipments(Request $request)
    {
        $tracking_numbers = array();

        $done_payment_shipments = DonePaymentShipment::where('done_payment_id', $request->id)->where('type', 1)->get();

        foreach ($done_payment_shipments as $done_payment_shipment) {
            $shipment = $done_payment_shipment->shipment;

            $tracking_numbers[] = $shipment->tracking_number;
        }

        return $tracking_numbers;
    }

    public function done_payments_adjusted_shipments(Request $request)
    {
        $tracking_numbers = array();

        $done_payment_shipments = DonePaymentShipment::where('done_payment_id', $request->id)->where('type', 2)->get();

        foreach ($done_payment_shipments as $done_payment_shipment) {
            $shipment = $done_payment_shipment->shipment;

            $tracking_numbers[] = $shipment->tracking_number;
        }

        return $tracking_numbers;
    }

    public function done_payments_details_print(Request $request)
    {
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $done_payment = DonePayment::find($request->id);

        $shipper = $done_payment->shipper;

        if ($done_payment->user_bank_info_id == null) {
            $shipper_bank = UserBankInfo::where('user_id', $shipper->id)->where('default_bank', 1)->first();
        } else {
            $shipper_bank = UserBankInfo::find($done_payment->user_bank_info_id);
        }


        $account_type_id = $shipper->account_type_id;

        $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Payment Details / Sales Tax Invoice</title>

                    <style>
                      @page {
                        size: A4 portrait;
                        margin: 0mm;
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

                      .summary {
                        page-break-inside: avoid;
                      }
                    </style>
                  </head>
                  <body>
                    <div>
                      <div class="p-1">
                        <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr>
                              <td class="text-center align-middle"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto"></td>
                              <td class="text-center align-middle color primary"><strong>Payment Details / Sales Tax Invoice</strong></td>
                              <td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Payment / Invoice ID</strong></td>
                              <td>' . str_pad($done_payment->id, 6, '0', STR_PAD_LEFT) . '</td>
                              <td rowspan="11" class="text-center align-middle">
                                <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($done_payment->id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                                <span><strong>' . str_pad($done_payment->id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                              </td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Client</strong></td>
                              <td>' . $shipper->name . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Client Bank</strong></td>
                              <td>' . $shipper_bank->bank->name . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Account Title</strong></td>
                              <td>' . $shipper_bank->account_title . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>IBAN</strong></td>
                              <td>' . $shipper_bank->iban . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Company Bank</strong></td>
                              <td>' . (($done_payment->company_bank_id) ? $done_payment->company_bank->name : '') . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Reference Number</strong></td>
                              <td>' . $done_payment->reference_number . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>NTN</strong></td>
                              <td>' . $shipper->ntn_no . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>STRN</strong></td>
                              <td>' . $shipper->strn_no . '</td>
                            </tr>
        ';

        $shipment_details = '';

        $serial_number = 1;

        $total_collection_amount = 0;
        $total_weight_charges = 0;
        $total_cash_handling_charges = 0;
        $total_insurance_charges = 0;
        $total_replacement_charges = 0;
        $total_try_and_buy_charges = 0;
        $total_return_charges = 0;
        $total_packing_charges = 0;
        $total_packaging_material_charges = 0;
        $total_fuel_surcharge = 0;
        $total_intercept_charges = 0;
        $total_nsa_osa_charges = 0;
        $total_gst = 0;
        $total_wht = 0;
        $total_charges = 0;
        $total_adjustments = 0;
        $total_payable = 0;
        foreach ($done_payment->done_payment_shipments as $done_payment_shipment) {
            $shipment = $done_payment_shipment->shipment;

            $shipment_weight = $shipment->actual_weight;
            $weight_charges = $shipment->weight_charges;

            if ($done_payment_shipment->type != 2) {
                $change_shipment_weight_log = ChangeShipmentWeightLog::where('shipment_id', $shipment->id);
                if ($change_shipment_weight_log->exists()) {
                    $change_shipment_weight_log = $change_shipment_weight_log->first();

                    $done_payment_shipment_date = Carbon::parse($done_payment_shipment->created_at);
                    $change_shipment_weight_log_date = Carbon::parse($change_shipment_weight_log->created_at);

                    if ($change_shipment_weight_log_date->gt($done_payment_shipment_date)) {
                        $shipment_weight = $change_shipment_weight_log->old_weight;
                        $weight_charges = $change_shipment_weight_log->old_charges;
                    }
                }
            }

            if ($done_payment_shipment->type == 0) {
                $type = 'Delivered';
            } else if ($done_payment_shipment->type == 1) {
                $type = 'Returned';
            } else {
                $type = 'Adjusted';
            }

            $pickup_address = $shipment->pickup_address;

            $shipment_details .= '
                            <tr>
                              <td>' . $serial_number . '</td>
                              <td>' . $shipment->tracking_number . '</td>
                              <td>' . $type . '</td>
                              <td>' . $shipment->order_id . '</td>
                              <td>' . (($pickup_address->vendor) ? $pickup_address->vendor : '') . '</td>
                              <td>' . $pickup_address->city->name . '</td>
                              <td>' . $shipment->consignee_city->name . '</td>
                              <td>' . $shipment->shipping_mode->mode . '</td>
                              <td>' . $shipment->consignee_name . ' ' . $shipment->consignee_phone_number_1 . '</td>
                              <td>' . $shipment->booking_type->booking_type . '</td>
                              <td>' . $shipment_weight . '</td>
                              <td>' . number_format($done_payment_shipment->amount) . '</td>
                              <td>' . (($done_payment_shipment->type != 2 && $done_payment_shipment->charges != 0) ? number_format($weight_charges, 2) : '0') . '</td>
                              <td>' . (($done_payment_shipment->type == 0 && $done_payment_shipment->charges != 0) ? number_format($shipment->cash_handling_charges, 2) : '0') . '</td>
                              <td>' . (($done_payment_shipment->type != 2 && $done_payment_shipment->charges != 0) ? number_format($shipment->nsa_osa_charges, 2) : '0') . '</td>
                              <td>' . (($done_payment_shipment->type == 2) ? number_format($done_payment_shipment->payable, 2) : '0') . '</td>
                              <td>' . ((1 == 1 && $done_payment_shipment->charges != 0) ? number_format($done_payment_shipment->charges, 2) : '0') . '</td>
                              <td>' . ((1 == 1 && $done_payment_shipment->charges != 0) ? number_format($done_payment_shipment->gst, 2) : '0') . '</td>
                              <td>' . ((1 == 1 && $done_payment_shipment->charges != 0) ? number_format($done_payment_shipment->wht, 2) : '0') . '</td>
                              <td>' . number_format($done_payment_shipment->amount - $done_payment_shipment->payable, 2) . '</td>
                              <td>' . number_format($done_payment_shipment->payable, 2) . '</td>
                            </tr>
            ';

            $serial_number++;

            if (1 == 1) {
                if ($done_payment_shipment->type != 2) {
                    if ($done_payment_shipment->charges != 0) {
                        if ($done_payment_shipment->type == 0) {
                            $total_collection_amount += $done_payment_shipment->amount;
                            $total_cash_handling_charges += $shipment->cash_handling_charges;
                            $total_replacement_charges += $shipment->replacement_charges;
                            $total_try_and_buy_charges += $shipment->try_and_buy_charges;
                        } else {
                            $total_return_charges += $shipment->return_charges;
                        }

                        $total_weight_charges += $weight_charges;

                        if ($shipment->packaging_material_request) {
                            $total_packaging_material_charges += $shipment->packaging_material_charges;
                        }
                        if ($shipment->packaging_charges != null) {
                            $total_packing_charges += $shipment->packaging_charges;
                        }
                        $total_insurance_charges += $shipment->insurance_charges;
                        $total_fuel_surcharge += $shipment->fuel_surcharge;
                        $total_intercept_charges += $shipment->intercept_charges;
                        $total_nsa_osa_charges += $shipment->nsa_osa_charges;
                    } else if ($done_payment_shipment->type == 0) {
                        $total_collection_amount += $done_payment_shipment->amount;
                    }
                } else {
                    $total_adjustments += $done_payment_shipment->payable;
                }

                $total_gst += $done_payment_shipment->gst;
                $total_wht += $done_payment_shipment->wht;
                $total_charges += $done_payment_shipment->charges;
                $total_payable += $done_payment_shipment->payable;
            } else {
                if ($done_payment_shipment->type == 0) {
                    $total_collection_amount += $done_payment_shipment->amount;
                } else if ($done_payment_shipment->type == 2) {
                    $total_adjustments += $done_payment_shipment->payable;
                }

                $total_payable += $done_payment_shipment->payable;
            }
        }

        $shipment_details .= '
                            <tr>
                                <td colspan="10"></td>
                                <td class="color primary"><strong>Total</strong></td>
                                <td class="color secondary"><strong>' . number_format($total_collection_amount) . '</strong></td>
                                <td class="color secondary"><strong>' . number_format($total_weight_charges, 2) . '</strong></td>
                                <td class="color secondary"><strong>' . number_format($total_cash_handling_charges, 2) . '</strong></td>
                                <td class="color secondary"><strong>' . number_format($total_nsa_osa_charges, 2) . '</strong></td>
                                <td class="color secondary"><strong>' . number_format($total_adjustments, 2) . '</strong></td>
                                <td class="color secondary"><strong>' . number_format($total_charges, 2) . '</strong></td>
                                <td class="color secondary"><strong>' . number_format($total_gst, 2) . '</strong></td>
                                <td class="color secondary"><strong>' . number_format($total_wht, 2) . '</strong></td>
                                <td class="color secondary"><strong>' . number_format($total_collection_amount - $total_payable, 2) . '</strong></td>
                                <td class="color secondary"><strong>' . number_format($total_payable, 2) . '</strong></td>
                            </tr>
      ';

        $html .= '
                            <tr>
                              <td class="color secondary"><strong>Total Collection Amount (PKR)</strong></td>
                              <td>' . number_format($total_collection_amount) . '</td>
                            </tr> 
                            <tr>
                              <td class="color secondary"><strong>Total WHT Amount (PKR)</strong></td>
                              <td>' . number_format($total_wht, 2) . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Total Payable (PKR)</strong></td>
                              <td>' . number_format(ROUND(($total_payable - $done_payment->ibft_charges), 0, PHP_ROUND_HALF_DOWN)) . '</td>
                            </tr>
                          </tbody>
                        </table>

                        <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr>
                              <td class="color primary"><strong>S. No.</strong></td>
                              <td class="color primary"><strong>Tracking No.</strong></td>
                              <td class="color primary"><strong>Type</strong></td>
                              <td class="color primary"><strong>Order ID</strong></td>
                              <td class="color primary"><strong>Vendor</strong></td>
                              <td class="color primary"><strong>Origin</strong></td>
                              <td class="color primary"><strong>Destination</strong></td>
                              <td class="color primary"><strong>Shipping Mode</strong></td>
                              <td class="color primary"><strong>Consignee</strong></td>
                              <td class="color primary"><strong>Service Type</strong></td>
                              <td class="color primary"><strong>Weight (kg)</strong></td>
                              <td class="color primary"><strong>Collection Amount (PKR)</strong></td>
                              <td class="color primary"><strong>Weight Charges (PKR)</strong></td>
                              <td class="color primary"><strong>Cash Handling Charges (PKR)</strong></td>
                              <td class="color primary"><strong>OSA Charges (PKR)</strong></td>
                              <td class="color primary"><strong>Adjustments (PKR)</strong></td>
                              <td class="color primary"><strong>Total Charges (PKR)</strong></td>
                              <td class="color primary"><strong>GST</strong></td>
                              <td class="color primary"><strong>WHT</strong></td>
                              <td class="color primary"><strong>Net Retained Amount (PKR)</strong></td>
                              <td class="color primary"><strong>Net Disbursement Amount (PKR)</strong></td>
                            </tr>
      ';

        $html .= $shipment_details;

        $html .= '
                          </tbody>
                        </table>

                        <div class="row">
                            <div class="col-6">
                                <table class="table table-sm table-bordered border summary">
                                  <tbody>
                                    <tr>
                                        <td class="color primary" colspan="2"><strong>Charges Summary (PKR)</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>Total Weight Charges</strong></td>
                                        <td>' . number_format($total_weight_charges, 2) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>Total Cash Handling Charges</strong></td>
                                        <td>' . number_format($total_cash_handling_charges, 2) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>Total Insurance Charges</strong></td>
                                        <td>' . number_format($total_insurance_charges, 2) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>Total Replacement Charges</strong></td>
                                        <td>' . number_format($total_replacement_charges, 2) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>Total Try & Buy Charges</strong></td>
                                        <td>' . number_format($total_try_and_buy_charges, 2) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>Total Return Charges</strong></td>
                                        <td>' . number_format($total_return_charges, 2) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>Total Fuel Surcharge</strong></td>
                                        <td>' . number_format($total_fuel_surcharge, 2) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>Total Intercept Charges</strong></td>
                                        <td>' . number_format($total_intercept_charges, 2) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>Total OSA Charges</strong></td>
                                        <td>' . number_format($total_nsa_osa_charges, 2) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>Total Charges (w/o GST)</strong></td>
                                        <td class="color secondary">' . number_format(($total_charges - $total_packaging_material_charges), 2) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>Total GST</strong></td>
                                        <td class="color secondary">' . number_format($total_gst, 2) . '</td>
                                    </tr>
                                   
                                    <tr>
                                        <td class="color secondary"><strong>Total WHT (Deductable)</strong></td>
                                        <td class="color secondary">' . number_format($total_wht, 2) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>Total Packing Charges</strong></td>
                                        <td>' . number_format($total_packing_charges, 2) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>Total Packaging Material Charges</strong></td>
                                        <td>' . number_format($total_packaging_material_charges, 2) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>Total Adjustments</strong></td>
                                        <td class="color secondary">' . number_format($total_adjustments, 2) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>IBFT Charges</strong></td>
                                        <td>' . number_format($done_payment->ibft_charges, 2) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color primary"><strong>Overall Charges</strong></td>
                                        <td class="color secondary"><strong>' . number_format(($total_charges + $total_gst - $total_adjustments + $done_payment->ibft_charges - $total_wht), 2) . '</strong></td>
                                    </tr>
                                  </tbody>
                                </table>
                                <span style="color: red">* 13% GST is applicable for Sindh Region 16% GST for Punjab .KPK</span>
                            </div>
                        </div>
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

    public function done_payments_details(Request $request)
    {
        $done_payment = DonePayment::find($request->id);

        $details = array();

        $details['reference_number'] = $done_payment->reference_number;
        $details['company_bank_id'] = $done_payment->company_bank_id;

        return $details;
    }

    public function done_payments_update_details(Request $request)
    {
        $done_payment = DonePayment::find($request->id);

        $done_payment->reference_number = $request->reference_number;
        $done_payment->company_bank_id = $request->company_bank_id;

        $done_payment->save();

        return ['status' => 0, 'success' => 'Details Updated'];
    }

    public function done_payments_export_to_excel(Request $request)
    {
        $done_payment = DonePayment::find($request->id);

        $filename = 'sonic_payment_details_' . $request->id . '.xlsx';

        $details = array();

        $details[] = ['S. No.', 'Tracking No.', 'Booking Date', 'Type', 'Order ID', 'Vendor', 'Origin', 'Consignee Name', 'Consignee Phone', 'Destination', 'Service Type', 'Weight (kg)', 'Collection Amount (PKR)', 'Weight Charges (PKR)', 'Cash Handling Charges (PKR)', 'OSA Charges (PKR)', 'Adjustments (PKR)', 'Total Charges (PKR)', 'GST', 'WHT', 'Net Retained Amount (PKR)', 'Net Disbursement Amount (PKR)
'];

        $account_type_id = $done_payment->shipper->account_type_id;

        $serial_number = 1;

        $total_collection_amount = 0;
        $total_weight_charges = 0;
        $total_cash_handling_charges = 0;
        $total_insurance_charges = 0;
        $total_replacement_charges = 0;
        $total_try_and_buy_charges = 0;
        $total_return_charges = 0;
        $total_packaging_material_charges = 0;
        $total_fuel_surcharge = 0;
        $total_intercept_charges = 0;
        $total_nsa_osa_charges = 0;
        $total_gst = 0;
        $total_wht = 0;
        $total_charges = 0;
        $total_adjustments = 0;
        $total_payable = 0;

        foreach ($done_payment->done_payment_shipments as $done_payment_shipment) {
            $shipment = $done_payment_shipment->shipment;

            $shipment_weight = $shipment->actual_weight;
            $weight_charges = $shipment->weight_charges;

            if ($done_payment_shipment->type != 2) {
                $change_shipment_weight_log = ChangeShipmentWeightLog::where('shipment_id', $shipment->id);
                if ($change_shipment_weight_log->exists()) {
                    $change_shipment_weight_log = $change_shipment_weight_log->first();

                    $done_payment_shipment_date = Carbon::parse($done_payment_shipment->created_at);
                    $change_shipment_weight_log_date = Carbon::parse($change_shipment_weight_log->created_at);

                    if ($change_shipment_weight_log_date->gt($done_payment_shipment_date)) {
                        $shipment_weight = $change_shipment_weight_log->old_weight;
                        $weight_charges = $change_shipment_weight_log->old_charges;
                    }
                }
            }

            if ($done_payment_shipment->type == 0) {
                $type = 'Delivered';
            } else if ($done_payment_shipment->type == 1) {
                $type = 'Returned';
            } else {
                $type = 'Adjusted';
            }

            $pickup_address = $shipment->pickup_address;

            $row = array();

            $row[] = $serial_number;
            $row[] = $shipment->tracking_number;
            $row[] = $shipment->created_at;
            $row[] = $type;
            $row[] = $shipment->order_id;
            $row[] = $pickup_address->vendor;
            $row[] = $pickup_address->city->name;
            $row[] = $shipment->consignee_name;
            $row[] = $shipment->consignee_phone_number_1;
            $row[] = $shipment->consignee_city->name;
            $row[] = $shipment->booking_type->booking_type;
            $row[] = $shipment_weight;
            $row[] = $done_payment_shipment->amount;
            $row[] = (($done_payment_shipment->type != 2 && $done_payment_shipment->charges != 0) ? $weight_charges : 0);
            $row[] = (($done_payment_shipment->type == 0 && $done_payment_shipment->charges != 0) ? $shipment->cash_handling_charges : 0);
            $row[] = (($done_payment_shipment->type != 2 && $done_payment_shipment->charges != 0) ? $shipment->nsa_osa_charges : 0);
            $row[] = (($done_payment_shipment->type == 2) ? $done_payment_shipment->payable : 0);

            $row[] = ((1 == 1 && $done_payment_shipment->charges != 0) ? number_format($done_payment_shipment->charges, 2) : '0');
            $row[] = ((1 == 1 && $done_payment_shipment->charges != 0) ? number_format($done_payment_shipment->gst, 2) : '0');
            $row[] = ((1 == 1 && $done_payment_shipment->charges != 0) ? number_format($done_payment_shipment->wht, 2) : '0');
            $row[] = ((1 == 1 && $done_payment_shipment->charges != 0) ? number_format($done_payment_shipment->amount - $done_payment_shipment->payable, 2) : '0');
            $row[] = ((1 == 1 && $done_payment_shipment->charges != 0) ? number_format($done_payment_shipment->payable, 2) : '0');

            $details[] = $row;

            $serial_number++;

            if (1 == 1) {
                if ($done_payment_shipment->type != 2) {
                    if ($done_payment_shipment->charges != 0) {
                        if ($done_payment_shipment->type == 0) {
                            $total_collection_amount += $done_payment_shipment->amount;
                            $total_cash_handling_charges += $shipment->cash_handling_charges;
                            $total_replacement_charges += $shipment->replacement_charges;
                            $total_try_and_buy_charges += $shipment->try_and_buy_charges;
                        } else {
                            $total_return_charges += $shipment->return_charges;
                        }

                        $total_weight_charges += $shipment->weight_charges;

                        if ($shipment->packaging_material_request) {
                            $total_packaging_material_charges += $shipment->packaging_material_charges;
                        }

                        $total_insurance_charges += $shipment->insurance_charges;
                        $total_fuel_surcharge += $shipment->fuel_surcharge;
                        $total_intercept_charges += $shipment->intercept_charges;
                        $total_nsa_osa_charges += $shipment->nsa_osa_charges;
                    } else if ($done_payment_shipment->type == 0) {
                        $total_collection_amount += $done_payment_shipment->amount;
                    }
                } else {
                    $total_adjustments += $done_payment_shipment->payable;
                }

                $total_gst += $done_payment_shipment->gst;
                $total_wht += $done_payment_shipment->wht;
                $total_charges += $done_payment_shipment->charges;
                $total_payable += $done_payment_shipment->payable;
            } else {
                if ($done_payment_shipment->type == 0) {
                    $total_collection_amount += $done_payment_shipment->amount;
                } else if ($done_payment_shipment->type == 2) {
                    $total_adjustments += $done_payment_shipment->payable;
                }

                $total_payable += $done_payment_shipment->payable;
            }
        }

        $total_columns = count($details[0]);

        $summary = ['Total Weight Charges' => $total_weight_charges, 'Total Cash Handling Charges' => $total_cash_handling_charges, 'Total Insurance Charges' => $total_insurance_charges, 'Total Replacement Charges' => $total_replacement_charges, 'Total Try & Buy Charges' => $total_try_and_buy_charges, 'Total Return Charges' => $total_return_charges, 'Total Fuel Surcharge' => $total_fuel_surcharge, 'Total Intercept Charges' => $total_intercept_charges, 'Total OSA Charges' => $total_nsa_osa_charges, 'Total Charges (w/o GST)' => ($total_charges - $total_packaging_material_charges), 'Total GST' => $total_gst, 'Total WHT (Deductable)' => $total_wht, 'Total Packaging Material Charges' => $total_packaging_material_charges, 'Total Adjustments' => $total_adjustments, 'IBFT Charges' => $done_payment->ibft_charges, 'Overall Charges' => ($total_charges + $total_gst - $total_adjustments + $done_payment->ibft_charges - $total_wht)];

        $details[] = [];

        $row = array();

        for ($c = 0; $c < $total_columns; $c++) {
            $row[] = '';
        }

        $row[] = 'Charges Summary (PKR)';
        $row[] = '';

        $details[] = $row;

        foreach ($summary as $name => $value) {
            $row = array();

            for ($c = 0; $c < $total_columns; $c++) {
                $row[] = '';
            }

            $row[] = $name;
            $row[] = $value;

            $details[] = $row;
        }

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->getStyle('B')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('M')->getNumberFormat()->setFormatCode('#,##0');
        $spreadsheet->getActiveSheet()->getStyle('N')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('O')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('P')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('Q')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('S')->getNumberFormat()->setFormatCode('#,##0.00');

        $spreadsheet->getActiveSheet()->fromArray($details);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function done_payments_generate_report_to_email()
    {
        $date = Carbon::today()->format('Y-m-d');
        $response = AdminReportsEmailController::done_payment($date . ' 00:00:00');
        return ['status' => 1, 'success' => ' Done Payment(s) Report Generated'];
    }

    static public function generate_invoice()
    {

        $settings = GlobalSettings::where('type', 'due_date_days');

        if ($settings->exists()) {
            $settings = $settings->first();

            $due_date_days = $settings->setting_value;
        } else {
            $due_date_days = 7;
        }


        $current_date = Carbon::now()->startOfDay();
        $current_date_string = $current_date->toDateString();
        // dd($current_date_string);
//        $cities = City::all();
        $users = User::where('account_type_id', 2)->get();

        foreach ($users as $user) {

            $generate = FALSE;

            $user_id = $user->id;

            $user_banking_information = UserBankInfo::where('user_id', $user_id)->where('default_bank', 1);

            if ($user_banking_information->exists()) {

                $user_banking_information = $user_banking_information->first();
                /* if ($user_banking_information->generation_date == $current_date->day) {
                     $generate = TRUE;

                     $billing_period_from_date = Carbon::now()->subDay()->day($user_banking_information->generation_date)->startOfDay()->toDateString();
                 }*/
                if ($user_banking_information->invoicing_cycle_id == 1) {
                    if ($user_banking_information->generation_date == $current_date->dayOfWeekIso) {
                        $generate = TRUE;

                        $billing_period_from_date = Carbon::now()->subDays(7)->startOfDay()->toDateString();
                    }
                } else if ($user_banking_information->invoicing_cycle_id == 2) {
                    if ($current_date->day == 14 || $current_date->day == 28) {
                        $generate = TRUE;

                        if ($current_date->day == 14) {
                            $billing_period_from_date = Carbon::now()->subMonth()->day(28)->startOfDay()->toDateString();
                        } else {
                            $billing_period_from_date = Carbon::now()->day(14)->startOfDay()->toDateString();
                        }
                    }
                } else if ($user_banking_information->invoicing_cycle_id == 3) {
                    if ($user_banking_information->generation_date == $current_date->day) {
                        $generate = TRUE;

                        /*$billing_period_from_date = Carbon::now()->subDay()->day($user_banking_information->generation_date)->startOfDay()->toDateString();*/
                        $billing_period_from_date = Carbon::now()->startOfMonth()->startOfDay()->toDateString();
                        $current_date_string = Carbon::now()->addDay()->toDateString();

                    }
                } else if ($user_banking_information->invoicing_cycle_id == 4) {
                    /*   if ($user_banking_information->generation_date == $current_date->dayOfWeekIso) {*/    //need to be update
                    $generate = TRUE;

                    $billing_period_from_date = Carbon::now()->subDays(1)->startOfDay()->toDateString();
                    //}
                }


                if ($generate) {

                    $pending_invoice_shipments = PendingInvoiceShipment::whereDate('created_at', '<', $current_date_string)->whereHas('shipment', function ($query) use ($user_id) {
                        $query->where('user_id', $user_id);
                    });

                    if ($pending_invoice_shipments->exists()) {

                        $invoice = new Invoice();

                        $invoice->user_id = $user_id;
                        $invoice->invoicing_date = Carbon::now()->subDay()->startOfDay()->toDateString();
                        $invoice->billing_period_from_date = $billing_period_from_date;
                        $invoice->billing_period_to_date = Carbon::now()->subDay()->startOfDay()->toDateString();
                        $invoice->due_date = Carbon::now()->addDays($due_date_days)->startOfDay()->toDateString();
                        $invoice->status_id = 1;
                        $invoice->invoice_type = 1;

                        $invoice->save();

                        $invoice_id = $invoice->id;

                        $invoice_number = $user_id . str_pad($invoice_id, 6, '0', STR_PAD_LEFT);

                        $total_shipments = 0;
                        $total_delivered_shipments = 0;
                        $total_returned_shipments = 0;
                        $total_adjusted_shipments = 0;
                        $total_charges = 0;
                        $total_gst = 0;
                        $total_invoice_amount = 0;
                        $shipment_count = 0;

                        $returned_shipper = GlobalSettings::where('type', 'invoice_against_return_delivered_shipper')->select('text')->first();
                        $array = explode(",", $returned_shipper->text);

                        foreach ($pending_invoice_shipments->get() as $pending_invoice_shipment) {

                            $check_status = true;
                            if (in_array($user_id, $array)) {

                                $shipment_id = $pending_invoice_shipment->shipment_id;
                                $shipment_status_ids = ShipmentsJourney::where('shipment_id', $shipment_id)->select('shipper_status_id')->latest()->first();
                                $status = $shipment_status_ids->shipper_status_id;

                                if (($status != 14) && ($status != 25)) {
                                    $check_status = false;
                                }
                            }

                            if ($check_status) {

                                $shipment_count += 1;

                                $invoice_shipment = new InvoiceShipment();

                                $invoice_shipment->created_at = $pending_invoice_shipment->created_at;
                                $invoice_shipment->invoice_id = $invoice_id;
                                $invoice_shipment->shipment_id = $pending_invoice_shipment->shipment_id;
                                $invoice_shipment->type = $pending_invoice_shipment->type;
                                $invoice_shipment->charges = $pending_invoice_shipment->charges;
                                $invoice_shipment->gst = $pending_invoice_shipment->gst;
                                $invoice_shipment->invoice_amount = $pending_invoice_shipment->invoice_amount;

                                $invoice_shipment->save();

                                $total_shipments++;

                                if ($pending_invoice_shipment->type == 0) {
                                    $total_delivered_shipments++;
                                } else if ($pending_invoice_shipment->type == 1) {
                                    $total_returned_shipments++;
                                } else {
                                    $total_adjusted_shipments++;
                                }

                                self::adjustment_logs_done(2, $pending_invoice_shipment->id, $invoice_shipment->id);

                                $total_charges = $total_charges + $pending_invoice_shipment->charges;
                                $total_gst = $total_gst + $pending_invoice_shipment->gst;
                                $total_invoice_amount = $total_invoice_amount + $pending_invoice_shipment->invoice_amount;

                                $pending_invoice_shipment->delete();

                            }
                        }
                        if ($shipment_count < 1) {
                            $invoice->delete();
                        } else {
                            $invoice->invoice_number = $invoice_number;
                            $invoice->total_shipments = $total_shipments;
                            $invoice->total_delivered_shipments = $total_delivered_shipments;
                            $invoice->total_returned_shipments = $total_returned_shipments;
                            $invoice->total_adjusted_shipments = $total_adjusted_shipments;
                            $invoice->total_charges = $total_charges;
                            $invoice->total_gst = $total_gst;
                            $invoice->total_invoice_amount = ROUND($total_invoice_amount, 0, PHP_ROUND_HALF_DOWN);

                            $invoice->save();

//                        if( $invoice->total_invoice_amount < 50000){
//                            $users = User::find($user_id);
//                            $users->blacklist = 1;
//                            $users->blacklist_reason = '<strong> Auto Blacklisted - </strong>'. "Invoice Amount was less than PKR 50,000";
//                            $users->save();
//                        }

                            NotificationsController::send(27, $invoice_id);
                        }
                    }
                }


                $packaging_invoice_toggle_on = CorporateUserPackagingInvoice::where('user_id', $user_id)->where('status', 1)->first();
                if ($packaging_invoice_toggle_on && $generate) {

                    $packaging_material_requests = PackagingMaterialRequest::where('user_id', $user_id)->whereBetween('updated_at', [$billing_period_from_date, $current_date_string])->where('status_id', 4)->whereNotNull('shipment_id');

                    if ($packaging_material_requests->exists()) {

                        $invoice = new Invoice();

                        $invoice->user_id = $user_id;
                        $invoice->invoicing_date = Carbon::now()->subDay()->startOfDay()->toDateString();
                        $invoice->billing_period_from_date = $billing_period_from_date;
                        $invoice->billing_period_to_date = Carbon::now()->subDay()->startOfDay()->toDateString();
                        $invoice->due_date = Carbon::now()->addDays($due_date_days)->startOfDay()->toDateString();
                        $invoice->status_id = 1;
                        $invoice->invoice_type = 2;

                        $invoice->save();

                        $invoice_id = $invoice->id;

                        $invoice_number = $user_id . str_pad($invoice_id, 6, '0', STR_PAD_LEFT);

                        $total_shipments = 0;
                        $total_delivered_shipments = 0;
                        $total_returned_shipments = 0;
                        $total_adjusted_shipments = 0;
                        $total_charges = 0;
                        $total_gst = 0;
                        $total_invoice_amount = 0;

                        foreach ($packaging_material_requests->get() as $packaging_material_request) {

                            $gst = $packaging_material_request->city->zone->gst;

                            $invoice_shipment = new InvoiceShipment();

                            $invoice_shipment->created_at = $packaging_material_request->upadated_at;
                            $invoice_shipment->invoice_id = $invoice_id;
                            $invoice_shipment->shipment_id = $packaging_material_request->shipment_id;
                            $invoice_shipment->type = 0;
                            $invoice_shipment->charges = $packaging_material_request->amount;
                            $invoice_shipment->gst = round($packaging_material_request->amount * $gst);
                            $invoice_amount = $packaging_material_request->amount + round($packaging_material_request->amount * $gst);
                            $invoice_shipment->invoice_amount = $invoice_amount;

                            $invoice_shipment->save();

                            $total_shipments++;

                            if ($invoice_shipment->type == 0) {
                                $total_delivered_shipments++;
                            } else if ($invoice_shipment->type == 1) {
                                $total_returned_shipments++;
                            } else {
                                $total_adjusted_shipments++;
                            }

                            //self::adjustment_logs_done(2, $pending_invoice_shipment->id, $invoice_shipment->id);

                            $total_charges = $total_charges + $packaging_material_request->amount;
                            $total_gst = $total_gst + round($packaging_material_request->amount * $gst);
                            $total_invoice_amount = $total_invoice_amount + $invoice_amount;

                            //$pending_invoice_shipment->delete();
                        }

                        $invoice->invoice_number = $invoice_number;
                        $invoice->total_shipments = $total_shipments;
                        $invoice->total_delivered_shipments = $total_delivered_shipments;
                        $invoice->total_returned_shipments = $total_returned_shipments;
                        $invoice->total_adjusted_shipments = $total_adjusted_shipments;
                        $invoice->total_charges = round($total_charges);
                        $invoice->total_gst = round($total_gst);
                        $invoice->total_invoice_amount = ROUND($total_invoice_amount, 0, PHP_ROUND_HALF_DOWN);

                        $invoice->save();

                    }
                }
            }
        }
    }

    static public function generate_reimbursement_invoice()
    {

        $current_date = Carbon::now()->startOfDay();
        $current_date_string = $current_date->toDateTimeString();
        $billing_period_from_date = Carbon::now()->subMonth()->startOfMonth()->startOfDay()->toDateTimeString();
        $billing_period_to_date = Carbon::now()->subMonth()->endOfMonth()->endOfDay()->toDateTimeString();

        $users = User::where('account_type_id', 1)->get();

        foreach ($users as $user) {
            $user_id = $user->id;

            $pending_payments = PendingPayment::where('user_id', $user_id)->whereBetween('created_at', [$billing_period_from_date, $billing_period_to_date]);

            if ($pending_payments->exists()) {
                $invoice_for_reimbursement = InvoiceForReimbursement::where('user_id', $user_id)->where('payment_type', 0)->where('from_date', $billing_period_from_date)->where('to_date', $billing_period_to_date);
                $generate = TRUE;

                if ($invoice_for_reimbursement->exists()) {
                    $invoice = $invoice_for_reimbursement->first();
                    if ($invoice->to_show == 0) {
                        $invoice->to_show = 1;
                        $invoice->invoicing_date = $billing_period_to_date;
                        $invoice->created_at = $current_date_string;

                        $invoice->update();

                        $invoice_number = $invoice->invoice_number;
                    } else {
                        $generate = FALSE;
                    }
                } else {
                    $invoice = new InvoiceForReimbursement();

                    $invoice->user_id = $user_id;
                    $invoice->invoicing_date = $billing_period_to_date;
                    $invoice->from_date = $billing_period_from_date;
                    $invoice->to_date = $billing_period_to_date;
                    $invoice->payment_type = 0;
                    $invoice->to_show = 1;

                    $invoice->save();

                    $invoice_number = $user_id . str_pad($invoice->id, 6, '0', STR_PAD_LEFT);

                }

                if ($generate) {
                    $invoice_id = $invoice->id;
                    $total_charges = 0;
                    $total_gst = 0;
                    $total_invoice_amount = 0;

                    $pending_payments = $pending_payments->get();
                    foreach ($pending_payments as $pending_payment) {
                        $calculation = $pending_payment->pending_payment_calculation;
                        $total_charges = $total_charges + $calculation->charges;
                        $total_gst = $total_gst + $calculation->gst;
                        $total_invoice_amount = $total_invoice_amount + $calculation->charges + $calculation->gst;

                        foreach ($pending_payment->pending_payment_shipments as $shipment) {
                            $invoice_shipment = new ReimbursementInvoiceShipment();

                            $invoice_shipment->created_at = $shipment->created_at;
                            $invoice_shipment->invoice_id = $invoice_id;
                            $invoice_shipment->shipment_id = $shipment->shipment_id;
                            $invoice_shipment->type = $shipment->type;
                            $invoice_shipment->charges = $shipment->charges;
                            $invoice_shipment->gst = $shipment->gst;
                            $invoice_shipment->invoice_amount = $shipment->charges + $shipment->gst;

                            $invoice_shipment->save();

                        }
                    }

                    $invoice->invoice_number = $invoice_number;
                    $invoice->total_charges = $total_charges;
                    $invoice->total_gst = $total_gst;
                    $invoice->total_invoice_amount = ROUND($total_invoice_amount, 0, PHP_ROUND_HALF_DOWN);

                    $invoice->save();
                }
            }

            $done_payments = DonePayment::where('user_id', $user_id)->whereBetween('created_at', [$billing_period_from_date, $billing_period_to_date]);

            if ($done_payments->exists()) {
                $invoice_for_reimbursement = InvoiceForReimbursement::where('user_id', $user_id)->where('payment_type', 1)->where('from_date', $billing_period_from_date)->where('to_date', $billing_period_to_date);
                $generate = TRUE;
                if ($invoice_for_reimbursement->exists()) {
                    $invoice = $invoice_for_reimbursement->first();
                    if ($invoice->to_show == 0) {
                        $invoice->to_show = 1;
                        $invoice->invoicing_date = $billing_period_to_date;
                        $invoice->created_at = $current_date_string;

                        $invoice->update();

                        $invoice_number = $invoice->invoice_number;
                    } else {
                        $generate = FALSE;
                    }
                } else {
                    $invoice = new InvoiceForReimbursement();

                    $invoice->user_id = $user_id;
                    $invoice->invoicing_date = $billing_period_to_date;
                    $invoice->from_date = $billing_period_from_date;
                    $invoice->to_date = $billing_period_to_date;
                    $invoice->payment_type = 1;
                    $invoice->to_show = 1;

                    $invoice->save();

                    $invoice_number = $user_id . str_pad($invoice->id, 6, '0', STR_PAD_LEFT);

                }

                if ($generate) {
                    $invoice_id = $invoice->id;
                    $total_charges = 0;
                    $total_gst = 0;
                    $total_invoice_amount = 0;

                    $done_payments = $done_payments->get();
                    foreach ($done_payments as $done_payment) {
                        $calculation = $done_payment->done_payment_calculation;
                        $total_charges = $total_charges + $calculation->charges;
                        $total_gst = $total_gst + $calculation->gst;
                        $total_invoice_amount = $total_invoice_amount + $calculation->charges + $calculation->gst;

                        foreach ($done_payment->done_payment_shipments as $shipment) {
                            $invoice_shipment = new ReimbursementInvoiceShipment();

                            $invoice_shipment->created_at = $shipment->created_at;
                            $invoice_shipment->invoice_id = $invoice_id;
                            $invoice_shipment->shipment_id = $shipment->shipment_id;
                            $invoice_shipment->type = $shipment->type;
                            $invoice_shipment->charges = $shipment->charges;
                            $invoice_shipment->gst = $shipment->gst;
                            $invoice_shipment->invoice_amount = $shipment->charges + $shipment->gst;

                            $invoice_shipment->save();

                        }
                    }

                    $invoice->invoice_number = $invoice_number;
                    $invoice->total_charges = $total_charges;
                    $invoice->total_gst = $total_gst;
                    $invoice->total_invoice_amount = ROUND($total_invoice_amount, 0, PHP_ROUND_HALF_DOWN);

                    $invoice->save();
                }
            }
        }
    }

    static public function generate_invoice_print($id, $email = FALSE, $header = FALSE)
    {
        $invoice = Invoice::find($id);

        $shipper = $invoice->shipper;

        $shipper_bank = $shipper->bank()->where('default_bank', 1)->first();

        $account_type_id = $shipper->account_type_id;

        $invoice_number_serial_number = 1;
        $html = '';

        if (!$email) {
            $html .= '
            <!doctype html>
            <html lang="en">
              <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                <title>Invoice</title>
            ';
        }

        $html .= '
                <style>' . file_get_contents(public_path('app-assets/css/bootstrap.min.css')) . '</style>
        ';

        $html .= '
            <style>@page{size:A4 portrait; margin-top: 12rem; margin-bottom: 2rem; margin-left: 0rem; margin-right: 0rem;}*{-webkit-print-color-adjust:exact!important;color-adjust:exact!important}body{background:none!important;color:#09262e!important;font-size:0.7rem!important}hr{border-top:1px dashed #000}table.table-bordered{page-break-inside:avoid}table.table-bordered thead tr th, table.table-bordered tbody tr td{border:1px solid #09262e!important}.color.primary{background:#c8c8c8!important}.color.secondary{background:#ebebeb!important}.border{border:1px solid #09262e!important}.summary{page-break-inside:avoid}.shipments_summary{page-break-before:always}</style>
        ';

        if ($header) {
            $html .= '
            <style>@page{margin-top: 1rem; margin-bottom: 1rem;}.summary_header .header{width: 10%;}.summary_header .heading{width: 15%;}.summary_footer .footer{width: 75%;}</style>
            ';
        }

        if (!$email) {
            $html .= '
              </head>
              <body>
            ';
        }

        $html .= '
                <div>
                  <div class="p-1">
        ';

        if ($header) {
            $html .= '
                    <div class="row no-gutters align-items-center summary_header mb-2">
                        <div class="col-12 text-right">
                            <img src="' . asset('img/invoice_summary_header_logo.png') . '" class="header">
                        </div>
                        <div class="col-12 text-center">
                            <img src="' . asset('img/invoice_summary_header_heading.png') . '" class="heading">
                        </div>
                    </div>
            ';
        }

        $html .= '<div>
          <div class="p-1">';

        $html .= '
            <div class="row align-items-start justify-content-between summary">
                <div class="col-6">
                    <table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                            <td class="color primary" colspan="2"><strong>Customer Details</strong></td>
                        </tr>
                        <tr>
                            <td class="color secondary"><strong>Account No.</strong></td>
                            <td>' . str_pad($shipper->id, 6, '0', STR_PAD_LEFT) . '</td>
                        </tr>';
        if ($account_type_id == 2) {
            $html .= '<tr>
                                <td class="color secondary"><strong>Shipper Name</strong></td>
                                <td>' . $shipper->name . '</td>
                            </tr>';
        }
        $html .= '<tr>
                            <td class="color secondary"><strong>Name</strong></td>
                            <td>' . (($account_type_id == 2) ? $shipper_bank->billing_person_name : $shipper->name) . '</td>
                        </tr>
                        <tr>
                            <td class="color secondary"><strong>Address</strong></td>
                            <td>' . (($account_type_id == 2) ? $shipper_bank->billing_address : $shipper->address) . '</td>
                        </tr>
                        <tr>
                            <td class="color secondary"><strong>Contact No.</strong></td>
                            <td>' . (($account_type_id == 2) ? $shipper_bank->billing_person_phone : $shipper->phone) . '</td>
                        </tr>
                        <tr>
                          <td class="color secondary"><strong>NTN</strong></td>
                          <td>' . $shipper->ntn_no . '</td>
                        </tr>  ';
        if ($invoice->invoice_type == 1) {
            $html .= '<tr>
                          <td class="color secondary"><strong>STRN</strong></td>
                          <td>' . $shipper->strn_no . '</td>
                        </tr>';
        }

        $html .= '</tbody>
                    </table>
                </div>

                <div class="col-4">
                    <table class="table table-sm table-bordered border">
                      <tbody> ';
        if ($invoice->invoice_type == 1) {
            $html .= '<tr>
                            <td class="color primary"><strong>NTN</strong></td>
                            <td>7930679-5</td>
                        </tr>
                        <tr>
                            <td class="color primary"><strong>SNTN</strong></td>
                            <td>S-7930679-5</td>
                        </tr>
                        <tr>
                            <td class="color primary"><strong>PNTN</strong></td>
                            <td>P-7930679-5</td>
                        </tr>';

        }

        $html .= '<tr>
                            <td class="color primary"><strong>Billing Period</strong></td>
                            <td>' . Carbon::parse($invoice->billing_period_from_date)->format('Y-m-d') . ' <-> ' . Carbon::parse($invoice->billing_period_to_date)->format('Y-m-d') . '</td>
                        </tr>
                        <tr>
                            <td class="color primary"><strong>Invoice No.</strong></td>
                            <td>' . $invoice->invoice_number . ' - ' . $invoice_number_serial_number . '</td>
                        </tr>
                        <tr> ';
        if ($invoice->invoice_type == 1) {
            $html .= '<td class="color primary"><strong>Invoice Date</strong></td>
                            <td>' . Carbon::parse($invoice->invoicing_date)->format('Y-m-d') . '</td>
                        </tr>';

        }

        $html .= '<tr>
                            <td class="color primary"><strong>Due Date</strong></td>
                            <td>' . Carbon::parse($invoice->due_date)->format('Y-m-d') . '</td>
                        </tr>
                       </tbody>
                    </table>
                </div>
            </div>
    ';


        $shipment_details = array();

        $serial_number = array();

        $origins = array();

        $total_charges = 0;
        $total_gst = 0;
        $total_invoice_amount = 0;

        if ($invoice->invoice_type == 1) {
            foreach ($invoice->invoice_shipments as $invoice_shipment) {
                $shipment = $invoice_shipment->shipment;


                $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 2);

                if ($shipment_journey->exists()) {
                    $date = $shipment_journey->first()->created_at;
                } else {
                    $date = $shipment->created_at;
                }

                $date = Carbon::parse($date)->format('Y-m-d');

                $origin = $shipment->pickup_address->city->name;

                if (!in_array($origin, $origins)) {
                    $origins[] = $origin;
                }

                if (!isset($shipment_details[$origin])) {
                    $shipment_details[$origin] = '';
                }

                if (!isset($serial_number[$origin])) {
                    $serial_number[$origin] = 1;
                }

                $shipment_details[$origin] .= '
                        <tr>
                          <td>' . $serial_number[$origin] . '</td>
                          <td>' . $shipment->tracking_number . '</td>
                          <td>' . $shipment->order_id . '</td>
                          <td>' . $shipment->consignee_city->name . '</td>
                          <td>' . $shipment->shipping_mode->mode . '</td>
                          <td>' . $date . '</td>
                          <td>' . $shipment->actual_weight . '</td>
                          <td>' . (($invoice_shipment->type != 2) ? number_format($shipment->weight_charges, 2) : '0') . '</td>
                          <td>' . (($invoice_shipment->type != 2) ? number_format($shipment->fuel_surcharge, 2) : '0') . '</td>
                          <td>' . (($invoice_shipment->type != 2) ? number_format($shipment->nsa_osa_charges, 2) : '0') . '</td>
                          <td>' . (($invoice_shipment->type == 2) ? number_format($invoice_shipment->invoice_amount, 2) : '0') . '</td>
                          <td>' . (($invoice_shipment->type == 2) ? number_format($shipment->packaging_material_charges, 2) : '0') . '</td>
                          <td>' . (($invoice_shipment->type != 2) ? number_format($shipment->esc_charges, 2) : '0') . '</td>
                          <td>' . number_format($invoice_shipment->charges, 2) . '</td>
                          <td>' . number_format($invoice_shipment->gst, 2) . '</td>
                          <td>' . number_format($invoice_shipment->invoice_amount, 2) . '</td>
                        </tr>
            ';

                $serial_number[$origin]++;

                if (!isset($total_weight_charges[$origin])) {
                    $total_weight_charges[$origin] = 0;
                }

                if (!isset($total_cash_handling_charges[$origin])) {
                    $total_cash_handling_charges[$origin] = 0;
                }

                if (!isset($total_insurance_charges[$origin])) {
                    $total_insurance_charges[$origin] = 0;
                }

                if (!isset($total_return_charges[$origin])) {
                    $total_return_charges[$origin] = 0;
                }

                if (!isset($total_fuel_surcharge[$origin])) {
                    $total_fuel_surcharge[$origin] = 0;
                }

                if (!isset($total_replacement_charges[$origin])) {
                    $total_replacement_charges[$origin] = 0;
                }

                if (!isset($total_try_and_buy_charges[$origin])) {
                    $total_try_and_buy_charges[$origin] = 0;
                }

                if (!isset($total_packaging_material_charges[$origin])) {
                    $total_packaging_material_charges[$origin] = 0;
                }

                if (!isset($total_intercept_charges[$origin])) {
                    $total_intercept_charges[$origin] = 0;
                }

                if (!isset($total_nsa_osa_charges[$origin])) {
                    $total_nsa_osa_charges[$origin] = 0;
                }

                if (!isset($total_adjustment_charges[$origin])) {
                    $total_adjustment_charges[$origin] = 0;
                }

                if (!isset($total_extra_service_charges[$origin])) {
                    $total_extra_service_charges[$origin] = 0;
                }


                if ($invoice_shipment->type != 2) {
                    if ($invoice_shipment->type == 0) {
                        $total_cash_handling_charges[$origin] += $shipment->cash_handling_charges;
                        $total_replacement_charges[$origin] += $shipment->replacement_charges;
                        $total_try_and_buy_charges[$origin] += $shipment->try_and_buy_charges;
                        $total_extra_service_charges[$origin] += $shipment->esc_charges;
                    } else {
                        $total_return_charges[$origin] += $shipment->return_charges;
                    }

                    $total_weight_charges[$origin] += $shipment->weight_charges;

                    if ($shipment->packaging_material_request) {
                        $total_packaging_material_charges[$origin] += $shipment->packaging_material_charges;
                    }

                    $total_insurance_charges[$origin] += $shipment->insurance_charges;
                    $total_fuel_surcharge[$origin] += $shipment->fuel_surcharge;
                    $total_intercept_charges[$origin] += $shipment->intercept_charges;
                    $total_nsa_osa_charges[$origin] += $shipment->nsa_osa_charges;
                } else {
                    $total_adjustment_charges[$origin] += $invoice_shipment->invoice_amount;
                }

                $total_charges += $invoice_shipment->charges;
                $total_gst += $invoice_shipment->gst;
                $total_invoice_amount += $invoice_shipment->invoice_amount;
            }

            $html .= '
                    <table class="table table-sm table-bordered border">
                      <thead>
                        <tr>
                            <th colspan="13" class="color primary text-center">Invoice Summary</th>
                        </tr>
                        <tr>
                            <th class="color secondary">Origin</th>
                            <th class="color secondary">Weight Charges (PKR)</th>
                            <th class="color secondary">Cash Handling Charges (PKR)</th>
                            <th class="color secondary">Insurance Charges (PKR)</th>
                            <th class="color secondary">Replacement Charges (PKR)</th>
                            <th class="color secondary">Try & Buy Charges (PKR)</th>
                            <th class="color secondary">Return Charges (PKR)</th>
                            <th class="color secondary">Fuel Surcharge (PKR)</th>
                            <th class="color secondary">Intercept Charges (PKR)</th>
                            <th class="color secondary">OSA Charges (PKR)</th>
                            <th class="color secondary">Packaging Charges (PKR)</th>
                            <th class="color secondary">Extra Service Charges (PKR)</th>
                            <th class="color secondary">Adjustment Charges (PKR)</th>
                        </tr>
                      </thead>
                      <tbody>
        ';

            foreach ($origins as $origin) {
                $html .= '
                        <tr>
                            <td>' . $origin . '</td>
                            <td>' . number_format($total_weight_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_cash_handling_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_insurance_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_replacement_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_try_and_buy_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_return_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_fuel_surcharge[$origin], 2) . '</td>
                            <td>' . number_format($total_intercept_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_nsa_osa_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_packaging_material_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_extra_service_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_adjustment_charges[$origin], 2) . '</td>
                        </tr>
            ';
            }

            $html .= '
                      </tbody>
                    </table>

                    <div class="row justify-content-end">
                        <div class="col-4">
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                  <td class="color secondary text-left"><strong>Subtotal (PKR)</strong></td>
                                  <td class="text-right">' . number_format($total_charges, 2) . '</td>
                                </tr>
                                <tr>
                                  <td class="color secondary text-left"><strong>GST (PKR)</strong></td>
                                  <td class="text-right">' . number_format($total_gst, 2) . '</td>
                                </tr>
                                <tr>
                                  <td class="color primary text-left"><strong>Total Invoice Amount (PKR)</strong></td>
                                  <td class="color secondary text-right">' . number_format(ROUND($total_invoice_amount, 0, PHP_ROUND_HALF_DOWN)) . '</td>
                                </tr>
                              </tbody>
                            </table>
                        </div>
                    </div>

                    <table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="color primary" style="width: 150px;"><strong>Amount in Words</strong></td>
                          <td class="color secondary">' . self::amount_to_words($total_invoice_amount) . ' Only</td>
                        </tr>
                      </tbody>
                    </table>

                    <table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="color primary text-center" colspan="2"><strong>Bank Account Details</strong></td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Benificiary Name</strong></td>
                          <td>Trax Online Private Limited</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Bank</strong></td>
                          <td>Meezan Bank</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Account No.</strong></td>
                          <td>0102951143</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Branch No.</strong></td>
                          <td>9912</td>
                        </tr>
                      </tbody>
                    </table>

                    <div class="mb-1 text-center font-italic"><strong>Disclaimer:</strong> This is a system generated invoice. No signature required.</div>
        ';

            if ($header) {
                $html .= '
                    <div class="row no-gutters align-items-center summary_footer mt-2">
                        <div class="col-12 text-center">
                            <img src="' . asset('img/invoice_summary_header_footer.png') . '" class="footer">
                        </div>
                    </div>
            ';
            }

            foreach ($origins as $origin) {
                $html .= '
                    <table class="table table-sm table-bordered border shipments_summary">
                      <thead>
                        <tr>
                            <th class="color primary text-center" colspan="16">Shipment(s) Summary - ' . $origin . '</th>
                        </tr>
                        <tr>
                          <th class="color secondary">S. No.</th>
                          <th class="color secondary">Tracking No.</th>
                          <th class="color secondary">Order ID</th>
                          <th class="color secondary">Destination</th>
                          <th class="color secondary">Shipping Mode</th>
                          <th class="color secondary">Arrival Date</th>
                          <th class="color secondary">Weight (kg)</th>
                          <th class="color secondary">Weight Charges (PKR)</th>
                          <th class="color secondary">Fuel Surcharge (PKR)</th>
                          <th class="color secondary">OSA Charges (PKR)</th>
                          <th class="color secondary">Adjustment Charges (PKR)</th>
                          <th class="color secondary">Packaging Charges (PKR)</th>
                          <th class="color secondary">Extra Service Charges (PKR)</th>
                          <th class="color secondary">Total Charges (PKR)</th>
                          <th class="color secondary">GST (PKR)</th>
                          <th class="color secondary">Invoice Amount (PKR)</th>
                        </tr>
                    </thead>
                    <tbody>
            ';

                $html .= $shipment_details[$origin];

                $html .= '
                      </tbody>
                    </table>
            ';
            }

        } else {
            $shipment_ids = $invoice->invoice_shipments->pluck('shipment_id')->toArray();

            $packaging_details = array();
            $origin_arrays = array();
            $size_arrays = array();

            if (count($shipment_ids) > 0) {

                $packaging_materials = PackagingMaterialRequest::whereIn('shipment_id', $shipment_ids)->where('packaging_material_requests.status_id', 4)
                    ->where('packaging_material_requests.user_id', $shipper->id);

                if ($packaging_materials->exists()) {
                    $packaging_materials = $packaging_materials->orderBy('city_id', 'asc')->get();
                    foreach ($packaging_materials as $packaging_material) {
                        foreach ($packaging_material->items as $details) {
                            if (!isset($packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['quantity'])) {
                                $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['quantity'] = 1;
                                $zone = Zone::find($packaging_material->city->zone_id);
                                $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['origin'] = $packaging_material->city->name;
                                $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['rates'] = $details->packaging_type_size->standard_charges;
                                $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['description'] = $details->packaging_type_size->type->type . '-' . $details->packaging_type_size->size;
                                $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['quantity'] = $details->quantity;
                                $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['gst'] = $zone->gst;
                            } else {
                                $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['quantity']++;
                            }
                        }
                    }
                }

            }

            if (count($packaging_details) > 0) {

                $html .= '<table class="table table-sm table-bordered border">
          <thead>
            <tr>
                <th colspan="12" class="color primary text-center">Invoice Summary</th>
            </tr>
            <tr>
                <th class="color secondary">Origin</th>
                <th class="color secondary">Description</th>
                <th class="color secondary">Rates</th>
                <th class="color secondary">Quantity</th>
                <th class="color secondary">Total Charges Without GST</th>
                <th class="color secondary">SST %</th>
                <th class="color secondary">SST Amount</th>
                <th class="color secondary">Total Amount with SST</th>
              
            </tr>
          </thead>
          <tbody>
';
                $rates_total = 0;
                $quantity_total = 0;
                $total_amount_without_gst = 0;
                $total_sst_amount = 0;
                $overall_amount = 0;


                foreach ($packaging_details as $cities) {
                    foreach ($cities as $packaging_material) {
                        $amount_without_gst = $packaging_material['rates'] * $packaging_material['quantity'];
                        $sst_amount = round($amount_without_gst * $packaging_material['gst']);
                        $total_amount_with_sst = round($amount_without_gst + $sst_amount);

                        $rates_total = $rates_total + $packaging_material['rates'];
                        $quantity_total = $quantity_total + $packaging_material['quantity'];
                        $total_amount_without_gst = $total_amount_without_gst + $amount_without_gst;
                        $total_sst_amount = $total_sst_amount + $sst_amount;
                        $overall_amount = $overall_amount + $total_amount_with_sst;
                        $html .= '
            <tr>
                <td>' . $packaging_material['origin'] . '</td>
                <td>' . $packaging_material['description'] . '</td>
                <td>' . $packaging_material['rates'] . '</td>
                <td>' . $packaging_material['quantity'] . '</td>
                <td>' . $amount_without_gst . '</td>
                <td>' . $packaging_material['gst'] * 100 . '%' . '</td>
                <td>' . round($sst_amount) . '</td>
                <td>' . number_format($total_amount_with_sst) . '</td>
              
            </tr>';
                        /* $html .= $packaging_material['origin'];*/
                    }
                }
                $html .= '<tr>
                <td colspan="3" class="text-center">Total Amount</td>
            
                <td>' . $quantity_total . '</td>
                <td>' . $total_amount_without_gst . '</td>
                <td></td>
                <td>' . number_format($total_sst_amount) . '</td>
                <td>' . number_format($overall_amount) . '</td>
            </tr>';
                $amount_in_words = '';
                $amount_in_words = self::amount_to_words($overall_amount);

                $html .= '<table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="color primary" style="width: 150px;"><strong>Amount in Words</strong></td>
                          <td class="color secondary">' . $amount_in_words . ' Only</td>
                        </tr>
                      </tbody>
                    </table>

                    <table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="color primary text-center" colspan="2"><strong>Bank Account Details</strong></td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Benificiary Name</strong></td>
                          <td>Trax Online Private Limited</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Bank</strong></td>
                          <td>Meezan Bank</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Account No.</strong></td>
                          <td>0102951143</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>IBAN No.</strong></td>
                          <td>PK02MEZN0099120104111731</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Branch Name.</strong></td>
                          <td>Liaquat Market Malir Branch</td>
                        </tr>';

                $html .= ' <tr>
                                      <td class="color secondary" style="width: 150px;"><strong>Branch Code.</strong></td>
                                      <td>9912</td>
                                    </tr>
                                    </tbody>
                    </table>

                    <div class="mb-1 text-center font-italic"><strong>Disclaimer:</strong> This is a system generated invoice. No signature required.</div>
            ';
            }


            $html .= '
                      </tbody>
                    </table>
            ';

            $invoice_number_serial_number++;
        }

        $html .= '
                  </div>
                </div>
        ';

        if (!$email) {
            $html .= '
                <script>
                  window.onload = function() {

                    window.print();
                  }
                </script>
              </body>
            </html>
            ';
        }

        return $html;
    }

    static public function generate_reimbursement_invoice_print($id, $email = FALSE, $header = FALSE)
    {
        $invoice = InvoiceForReimbursement::find($id);

        $shipper = $invoice->shipper;

        $shipper_bank = $shipper->bank()->where('default_bank', 1)->first();

        $account_type_id = $shipper->account_type_id;

        $html = '';

        if (!$email) {
            $html .= '
            <!doctype html>
            <html lang="en">
              <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                <title>Invoice</title>
            ';
        }

        $html .= '
                <style>' . file_get_contents(public_path('app-assets/css/bootstrap.min.css')) . '</style>
        ';

        $html .= '
            <style>@page{size:A4 portrait; margin-top: 12rem; margin-bottom: 2rem; margin-left: 0rem; margin-right: 0rem;}*{-webkit-print-color-adjust:exact!important;color-adjust:exact!important}body{background:none!important;color:#09262e!important;font-size:0.7rem!important}hr{border-top:1px dashed #000}table.table-bordered{page-break-inside:avoid}table.table-bordered thead tr th, table.table-bordered tbody tr td{border:1px solid #09262e!important}.color.primary{background:#c8c8c8!important}.color.secondary{background:#ebebeb!important}.border{border:1px solid #09262e!important}.summary{page-break-inside:avoid}.shipments_summary{page-break-before:always}</style>
        ';

        if ($header) {
            $html .= '
            <style>@page{margin-top: 1rem; margin-bottom: 1rem;}.summary_header .header{width: 10%;}.summary_header .heading{width: 15%;}.summary_footer .footer{width: 75%;}</style>
            ';
        }

        if (!$email) {
            $html .= '
              </head>
              <body>
            ';
        }

        $html .= '
                <div>
                  <div class="p-1">
        ';

        if ($header) {
            $html .= '
                    <div class="row no-gutters align-items-center summary_header mb-2">
                        <div class="col-12 text-right">
                            <img src="' . asset('img/invoice_summary_header_logo.png') . '" class="header">
                        </div>
                        <div class="col-12 text-center">
                            <img src="' . asset('img/invoice_summary_header_heading.png') . '" class="heading">
                        </div>
                    </div>
            ';
        }

        $html .= '
                    <div class="row align-items-start justify-content-between summary">
                        <div class="col-6">
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                    <td class="color primary" colspan="2"><strong>Customer Details</strong></td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Account No.</strong></td>
                                    <td>' . str_pad($shipper->id, 6, '0', STR_PAD_LEFT) . '</td>
                                </tr>';
        if ($account_type_id == 2) {
            $html .= '<tr>
                                        <td class="color secondary"><strong>Shipper Name</strong></td>
                                        <td>' . $shipper->name . '</td>
                                    </tr>';
        }
        $html .= '<tr>
                                    <td class="color secondary"><strong>Name</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_person_name : $shipper->name) . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Address</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_address : $shipper->address) . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Contact No.</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_person_phone : $shipper->phone) . '</td>
                                </tr>
                                <tr>
                                  <td class="color secondary"><strong>NTN</strong></td>
                                  <td>' . $shipper->ntn_no . '</td>
                                </tr>
                                <tr>
                                  <td class="color secondary"><strong>STRN</strong></td>
                                  <td>' . $shipper->strn_no . '</td>
                                </tr>
                               </tbody>
                            </table>
                        </div>

                        <div class="col-4">
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                    <td class="color primary"><strong>NTN</strong></td>
                                    <td>7930679-5</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>SNTN</strong></td>
                                    <td>S-7930679-5</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>PNTN</strong></td>
                                    <td>P-7930679-5</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>Billing Period</strong></td>
                                    <td>' . Carbon::parse($invoice->from_date)->format('Y-m-d') . ' <-> ' . Carbon::parse($invoice->to_date)->format('Y-m-d') . '</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>Invoice No.</strong></td>
                                    <td>' . $invoice->invoice_number . '</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>Invoice Date</strong></td>
                                    <td>' . Carbon::parse($invoice->invoicing_date)->format('Y-m-d') . '</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>Due Date</strong></td>
                                    <td>' . Carbon::parse($invoice->due_date)->format('Y-m-d') . '</td>
                                </tr>
                               </tbody>
                            </table>
                        </div>
                    </div>
        ';

        $shipment_details = array();

        $serial_number = array();

        $origins = array();

        $total_charges = 0;
        $total_gst = 0;
        $total_invoice_amount = 0;

        foreach ($invoice->invoice_shipments as $invoice_shipment) {
            $shipment = $invoice_shipment->shipment;

            $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 2);

            if ($shipment_journey->exists()) {
                $date = $shipment_journey->first()->created_at;
            } else {
                $date = $shipment->created_at;
            }

            $date = Carbon::parse($date)->format('Y-m-d');

            $origin = $shipment->pickup_address->city->name;

            if (!in_array($origin, $origins)) {
                $origins[] = $origin;
            }

            if (!isset($shipment_details[$origin])) {
                $shipment_details[$origin] = '';
            }

            if (!isset($serial_number[$origin])) {
                $serial_number[$origin] = 1;
            }

            $shipment_details[$origin] .= '
                        <tr>
                          <td>' . $serial_number[$origin] . '</td>
                          <td>' . $shipment->tracking_number . '</td>
                          <td>' . $shipment->order_id . '</td>
                          <td>' . $shipment->consignee_city->name . '</td>
                          <td>' . $shipment->shipping_mode->mode . '</td>
                          <td>' . $date . '</td>
                          <td>' . $shipment->actual_weight . '</td>
                          <td>' . (($invoice_shipment->type != 2) ? number_format($shipment->weight_charges, 2) : '0') . '</td>
                          <td>' . (($invoice_shipment->type != 2) ? number_format($shipment->fuel_surcharge, 2) : '0') . '</td>
                          <td>' . (($invoice_shipment->type != 2) ? number_format($shipment->nsa_osa_charges, 2) : '0') . '</td>
                          <td>' . (($invoice_shipment->type == 2) ? number_format($invoice_shipment->invoice_amount, 2) : '0') . '</td>
                          <td>' . (($invoice_shipment->type == 2) ? number_format($shipment->packaging_material_charges, 2) : '0') . '</td>
                          <td>' . number_format($invoice_shipment->charges, 2) . '</td>
                          <td>' . number_format($invoice_shipment->gst, 2) . '</td>
                          <td>' . number_format($invoice_shipment->invoice_amount, 2) . '</td>
                        </tr>
            ';

            $serial_number[$origin]++;

            if (!isset($total_weight_charges[$origin])) {
                $total_weight_charges[$origin] = 0;
            }

            if (!isset($total_cash_handling_charges[$origin])) {
                $total_cash_handling_charges[$origin] = 0;
            }

            if (!isset($total_insurance_charges[$origin])) {
                $total_insurance_charges[$origin] = 0;
            }

            if (!isset($total_return_charges[$origin])) {
                $total_return_charges[$origin] = 0;
            }

            if (!isset($total_fuel_surcharge[$origin])) {
                $total_fuel_surcharge[$origin] = 0;
            }

            if (!isset($total_replacement_charges[$origin])) {
                $total_replacement_charges[$origin] = 0;
            }

            if (!isset($total_try_and_buy_charges[$origin])) {
                $total_try_and_buy_charges[$origin] = 0;
            }

            if (!isset($total_packaging_material_charges[$origin])) {
                $total_packaging_material_charges[$origin] = 0;
            }

            if (!isset($total_intercept_charges[$origin])) {
                $total_intercept_charges[$origin] = 0;
            }

            if (!isset($total_nsa_osa_charges[$origin])) {
                $total_nsa_osa_charges[$origin] = 0;
            }

            if (!isset($total_adjustment_charges[$origin])) {
                $total_adjustment_charges[$origin] = 0;
            }

            if ($invoice_shipment->type != 2) {
                if ($invoice_shipment->type == 0) {
                    $total_cash_handling_charges[$origin] += $shipment->cash_handling_charges;
                    $total_replacement_charges[$origin] += $shipment->replacement_charges;
                    $total_try_and_buy_charges[$origin] += $shipment->try_and_buy_charges;
                } else {
                    $total_return_charges[$origin] += $shipment->return_charges;
                }

                $total_weight_charges[$origin] += $shipment->weight_charges;

                if ($shipment->packaging_material_request) {
                    $total_packaging_material_charges[$origin] += $shipment->packaging_material_charges;
                }

                $total_insurance_charges[$origin] += $shipment->insurance_charges;
                $total_fuel_surcharge[$origin] += $shipment->fuel_surcharge;
                $total_intercept_charges[$origin] += $shipment->intercept_charges;
                $total_nsa_osa_charges[$origin] += $shipment->nsa_osa_charges;
            } else {
                $total_adjustment_charges[$origin] += $invoice_shipment->invoice_amount;
            }

            $total_charges += $invoice_shipment->charges;
            $total_gst += $invoice_shipment->gst;
            $total_invoice_amount += $invoice_shipment->invoice_amount;
        }

        $html .= '
                    <table class="table table-sm table-bordered border">
                      <thead>
                        <tr>
                            <th colspan="12" class="color primary text-center">Invoice Summary</th>
                        </tr>
                        <tr>
                            <th class="color secondary">Origin</th>
                            <th class="color secondary">Weight Charges (PKR)</th>
                            <th class="color secondary">Cash Handling Charges (PKR)</th>
                            <th class="color secondary">Insurance Charges (PKR)</th>
                            <th class="color secondary">Replacement Charges (PKR)</th>
                            <th class="color secondary">Try & Buy Charges (PKR)</th>
                            <th class="color secondary">Return Charges (PKR)</th>
                            <th class="color secondary">Fuel Surcharge (PKR)</th>
                            <th class="color secondary">Intercept Charges (PKR)</th>
                            <th class="color secondary">OSA Charges (PKR)</th>
                            <th class="color secondary">Packaging Charges (PKR)</th>
                            <th class="color secondary">Adjustment Charges (PKR)</th>
                        </tr>
                      </thead>
                      <tbody>
        ';

        foreach ($origins as $origin) {
            $html .= '
                        <tr>
                            <td>' . $origin . '</td>
                            <td>' . number_format($total_weight_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_cash_handling_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_insurance_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_replacement_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_try_and_buy_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_return_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_fuel_surcharge[$origin], 2) . '</td>
                            <td>' . number_format($total_intercept_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_nsa_osa_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_packaging_material_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_adjustment_charges[$origin], 2) . '</td>
                        </tr>
            ';
        }

        $html .= '
                      </tbody>
                    </table>

                    <div class="row justify-content-end">
                        <div class="col-4">
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                  <td class="color secondary text-left"><strong>Subtotal (PKR)</strong></td>
                                  <td class="text-right">' . number_format($total_charges, 2) . '</td>
                                </tr>
                                <tr>
                                  <td class="color secondary text-left"><strong>GST (PKR)</strong></td>
                                  <td class="text-right">' . number_format($total_gst, 2) . '</td>
                                </tr>
                                <tr>
                                  <td class="color primary text-left"><strong>Total Invoice Amount (PKR)</strong></td>
                                  <td class="color secondary text-right">' . number_format(ROUND($total_invoice_amount, 0, PHP_ROUND_HALF_DOWN)) . '</td>
                                </tr>
                              </tbody>
                            </table>
                        </div>
                    </div>

                    <table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="color primary" style="width: 150px;"><strong>Amount in Words</strong></td>
                          <td class="color secondary">' . self::amount_to_words($total_invoice_amount) . ' Only</td>
                        </tr>
                      </tbody>
                    </table>

                    <table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="color primary text-center" colspan="2"><strong>Bank Account Details</strong></td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Benificiary Name</strong></td>
                          <td>Trax Online Private Limited</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Bank</strong></td>
                          <td>Meezan Bank</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Account No.</strong></td>
                          <td>0102951143</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Branch No.</strong></td>
                          <td>9912</td>
                        </tr>
                      </tbody>
                    </table>

                    <div class="mb-1 text-center font-italic"><strong>Disclaimer:</strong> This is a system generated invoice. No signature required.</div>
        ';

        if ($header) {
            $html .= '
                    <div class="row no-gutters align-items-center summary_footer mt-2">
                        <div class="col-12 text-center">
                            <img src="' . asset('img/invoice_summary_header_footer.png') . '" class="footer">
                        </div>
                    </div>
            ';
        }

        foreach ($origins as $origin) {
            $html .= '
                    <table class="table table-sm table-bordered border shipments_summary">
                      <thead>
                        <tr>
                            <th class="color primary text-center" colspan="15">Shipment(s) Summary - ' . $origin . '</th>
                        </tr>
                        <tr>
                          <th class="color secondary">S. No.</th>
                          <th class="color secondary">Tracking No.</th>
                          <th class="color secondary">Order ID</th>
                          <th class="color secondary">Destination</th>
                          <th class="color secondary">Shipping Mode</th>
                          <th class="color secondary">Arrival Date</th>
                          <th class="color secondary">Weight (kg)</th>
                          <th class="color secondary">Weight Charges (PKR)</th>
                          <th class="color secondary">Fuel Surcharge (PKR)</th>
                          <th class="color secondary">OSA Charges (PKR)</th>
                          <th class="color secondary">Adjustment Charges (PKR)</th>
                          <th class="color secondary">Packaging Charges (PKR)</th>
                          <th class="color secondary">Total Charges (PKR)</th>
                          <th class="color secondary">GST (PKR)</th>
                          <th class="color secondary">Invoice Amount (PKR)</th>
                        </tr>
                    </thead>
                    <tbody>
            ';

            $html .= $shipment_details[$origin];

            $html .= '
                      </tbody>
                    </table>
            ';
        }

        $html .= '
                  </div>
                </div>
        ';

        if (!$email) {
            $html .= '
                <script>
                  window.onload = function() {

                    window.print();
                  }
                </script>
              </body>
            </html>
            ';
        }

        return $html;
    }

    static public function generate_invoice_print_origin_wise($id, $email = FALSE)
    {
        $invoice = Invoice::find($id);

        $shipper = $invoice->shipper;

        $shipper_bank = $shipper->bank()->where('default_bank', 1)->first();

        $account_type_id = $shipper->account_type_id;

        $html = '';

        if (!$email) {
            $html .= '
            <!doctype html>
            <html lang="en">
              <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                <title>Invoice</title>
            ';
        }

        $html .= '
                <style>' . file_get_contents(public_path('app-assets/css/bootstrap.min.css')) . '</style>
        ';

        $html .= '
            <style>@page{size:A4 portrait; margin-top: 12rem; margin-bottom: 2rem; margin-left: 0rem; margin-right: 0rem;}*{-webkit-print-color-adjust:exact!important;color-adjust:exact!important}body{background:none!important;color:#09262e!important;font-size:0.7rem!important}hr{border-top:1px dashed #000}table.table-bordered{page-break-inside:avoid}table.table-bordered thead tr th, table.table-bordered tbody tr td{border:1px solid #09262e!important}.color.primary{background:#c8c8c8!important}.color.secondary{background:#ebebeb!important}.border{border:1px solid #09262e!important}.summary{page-break-before:always;page-break-inside:avoid}.shipments_summary{page-break-before:always}</style>
        ';

        if (!$email) {
            $html .= '
              </head>
              <body>
            ';
        }

        $shipment_counts = array();

        $origins = array();

        $total_charges = array();
        $total_gst = array();
        $total_invoice_amount = array();

        $packaging_details = array();

        if ($invoice->invoice_type == 1) {
            foreach ($invoice->invoice_shipments as $invoice_shipment) {
                $shipment = $invoice_shipment->shipment;

                $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 2);

                if ($shipment_journey->exists()) {
                    $date = $shipment_journey->first()->created_at;
                } else {
                    $date = $shipment->created_at;
                }

                $date = Carbon::parse($date)->format('Y-m-d');

                $origin = $shipment->pickup_address->city->name;

                if (!in_array($origin, $origins)) {
                    $origins[] = $origin;
                }

                if (!isset($shipment_counts[$origin])) {
                    $shipment_counts[$origin] = 0;
                }

                $shipment_counts[$origin]++;

                if (!isset($total_weight_charges[$origin])) {
                    $total_weight_charges[$origin] = 0;
                }

                if (!isset($total_cash_handling_charges[$origin])) {
                    $total_cash_handling_charges[$origin] = 0;
                }

                if (!isset($total_insurance_charges[$origin])) {
                    $total_insurance_charges[$origin] = 0;
                }

                if (!isset($total_return_charges[$origin])) {
                    $total_return_charges[$origin] = 0;
                }

                if (!isset($total_fuel_surcharge[$origin])) {
                    $total_fuel_surcharge[$origin] = 0;
                }

                if (!isset($total_replacement_charges[$origin])) {
                    $total_replacement_charges[$origin] = 0;
                }

                if (!isset($total_try_and_buy_charges[$origin])) {
                    $total_try_and_buy_charges[$origin] = 0;
                }

                if (!isset($total_packaging_material_charges[$origin])) {
                    $total_packaging_material_charges[$origin] = 0;
                }

                if (!isset($total_intercept_charges[$origin])) {
                    $total_intercept_charges[$origin] = 0;
                }

                if (!isset($total_nsa_osa_charges[$origin])) {
                    $total_nsa_osa_charges[$origin] = 0;
                }

                if (!isset($total_adjustment_charges[$origin])) {
                    $total_adjustment_charges[$origin] = 0;
                }

                if (!isset($total_charges[$origin])) {
                    $total_charges[$origin] = 0;
                }

                if (!isset($total_gst[$origin])) {
                    $total_gst[$origin] = 0;
                }

                if (!isset($total_invoice_amount[$origin])) {
                    $total_invoice_amount[$origin] = 0;
                }


                if (!isset($total_extra_service_charges[$origin])) {
                    $total_extra_service_charges[$origin] = 0;
                }

                if ($invoice_shipment->type != 2) {
                    if ($invoice_shipment->type == 0) {
                        $total_cash_handling_charges[$origin] += $shipment->cash_handling_charges;
                        $total_replacement_charges[$origin] += $shipment->replacement_charges;
                        $total_try_and_buy_charges[$origin] += $shipment->try_and_buy_charges;
                        $total_extra_service_charges[$origin] += $shipment->esc_charges;

                    } else {
                        $total_return_charges[$origin] += $shipment->return_charges;
                    }


                    $total_weight_charges[$origin] += $shipment->weight_charges;

                    if ($shipment->packaging_material_request) {
                        $total_packaging_material_charges[$origin] += $shipment->packaging_material_charges;
                    }

                    $total_insurance_charges[$origin] += $shipment->insurance_charges;
                    $total_fuel_surcharge[$origin] += $shipment->fuel_surcharge;
                    $total_intercept_charges[$origin] += $shipment->intercept_charges;
                    $total_nsa_osa_charges[$origin] += $shipment->nsa_osa_charges;
                } else {
                    $total_adjustment_charges[$origin] += $invoice_shipment->invoice_amount;
                }

                $total_charges[$origin] += $invoice_shipment->charges;
                $total_gst[$origin] += $invoice_shipment->gst;
                $total_invoice_amount[$origin] += $invoice_shipment->invoice_amount;
            }
        } else {

            $shipment_ids = $invoice->invoice_shipments->pluck('shipment_id')->toArray();

            if (count($shipment_ids) > 0) {

                $packaging_materials = PackagingMaterialRequest::whereIn('shipment_id', $shipment_ids)->where('packaging_material_requests.status_id', 4)
                    ->where('packaging_material_requests.user_id', $shipper->id);

                if ($packaging_materials->exists()) {
                    $packaging_materials = $packaging_materials->orderBy('city_id', 'asc')->get();
                    foreach ($packaging_materials as $packaging_material) {
                        foreach ($packaging_material->items as $details) {
                            if (!isset($packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['quantity'])) {
                                $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['quantity'] = 1;
                                $zone = Zone::find($packaging_material->city->zone_id);
                                $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['origin'] = $packaging_material->city->name;
                                $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['rates'] = $details->packaging_type_size->standard_charges;
                                $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['description'] = $details->packaging_type_size->type->type . '-' . $details->packaging_type_size->size;
                                $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['quantity'] = $details->quantity;
                                $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['gst'] = $zone->gst;
                            } else {
                                $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['quantity']++;
                            }
                        }
                    }
                }

            }

        }

        $invoice_number_serial_number = 1;

        if (count($origins) > 0 && $invoice->invoice_type == 1) {
            foreach ($origins as $origin) {

                $html .= '
                <div>
                  <div class="p-1">
        ';
                $html .= '
                    <div class="row align-items-start justify-content-between summary">
                        
                        <div class="col-6">
                            <h1><b><u>SALES TAX INVOICE</u></b></h1>
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                    <td class="color primary" colspan="2"><strong>Bill To</strong></td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Account No.</strong></td>
                                    <td>' . str_pad($shipper->id, 6, '0', STR_PAD_LEFT) . '</td>
                                </tr>';
                if ($account_type_id == 2) {
                    $html .= '<tr>
                                        <td class="color secondary"><strong>Shipper Name</strong></td>
                                        <td>' . $shipper->name . '</td>
                                    </tr>';
                }
                $html .= '<tr>
                                    <td class="color secondary"><strong>Name</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_person_name : $shipper->name) . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Address</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_address : $shipper->address) . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Contact No.</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_person_phone : $shipper->phone) . '</td>
                                </tr>
                                <tr>
                                  <td class="color secondary"><strong>NTN</strong></td>
                                  <td>' . $shipper->ntn_no . '</td>
                                </tr>  ';

                $html .= '<tr>
                                  <td class="color secondary"><strong>STRN</strong></td>
                                  <td>' . $shipper->strn_no . '</td>
                                </tr>';


                $html .= '</tbody>
                            </table>
                        </div>

                        <div class="col-4">
                            <h1 style="text-align:center"><img style="height:90px;"  src="' . asset('img/invoice_summary_header_logo.png') . '" class="header"></h1>
                            <table class="table table-sm table-bordered border">
                              <tbody> ';
                if ($invoice->invoice_type == 1) {
                    $html .= '<tr>
                                    <td class="color primary"><strong>SNTN</strong></td>
                                    <td>S-7930679-5</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>PNTN</strong></td>
                                    <td>P-7930679-5</td>
                                </tr>';

                }

                $html .= '<tr>
                                    <td class="color primary"><strong>Billing Period</strong></td>
                                    <td>' . Carbon::parse($invoice->billing_period_from_date)->format('Y-m-d') . ' <-> ' . Carbon::parse($invoice->billing_period_to_date)->format('Y-m-d') . '</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>Invoice No.</strong></td>
                                    <td>' . $invoice->invoice_number . ' - ' . $invoice_number_serial_number . '</td>
                                </tr>
                                <tr> ';

                $html .= '<td class="color primary"><strong>Invoice Date</strong></td>
                                    <td>' . Carbon::parse($invoice->invoicing_date)->format('Y-m-d') . '</td>
                                </tr>';


                $html .= '<tr>
                                    <td class="color primary"><strong>Due Date</strong></td>
                                    <td>' . Carbon::parse($invoice->due_date)->format('Y-m-d') . '</td>
                                </tr>
                               </tbody>
                            </table>
                        </div>
                    </div>
            ';

                $html .= '
                    <table class="table table-sm table-bordered border">
                      <thead>
                        <tr>
                            <th colspan="3" class="color primary text-center">Invoice Summary - ' . $origin . '</th>
                        </tr>
                        <tr>
                            <th class="color secondary">Description Of Services</th>
                            <th class="color secondary" style="width:50px;">Quantity</th>
                            <th class="color secondary">Total Charges</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Weight Charges</td>
                            <td rowspan="12">' . $shipment_counts[$origin] . '</td>
                            <td>' . number_format($total_weight_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Fuel Surcharge</td>
                            <td>' . number_format($total_fuel_surcharge[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Cash Handling Charges</td>
                            <td>' . number_format($total_cash_handling_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Insurance Charges</td>
                            <td>' . number_format($total_insurance_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Replacement Charges</td>
                            <td>' . number_format($total_replacement_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Try & Buy Charges</td>
                            <td>' . number_format($total_try_and_buy_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Return Charges</td>
                            <td>' . number_format($total_return_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Intercept Charges</td>
                            <td>' . number_format($total_intercept_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>OSA Charges</td>
                            <td>' . number_format($total_nsa_osa_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Packaging Charges</td>
                            <td>' . number_format($total_packaging_material_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Adjustments</td>
                            <td>' . number_format($total_adjustment_charges[$origin], 2) . '</td>
                        </tr>
                         <tr>
                            <td>Extra Service Charges</td>
                            <td>' . number_format($total_extra_service_charges[$origin], 2) . '</td>
                        </tr>
                      </tbody>
                      </table>
            ';
                $html .= '
                    <div class="row justify-content-end">
                        <div class="col-4">
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                  <td class="color secondary text-left"><strong>Subtotal (PKR)</strong></td>
                                  <td class="text-right">' . number_format($total_charges[$origin], 2) . '</td>
                                </tr>
                                <tr>
                                  <td class="color secondary text-left"><strong>SST (PKR)</strong></td>
                                  <td class="text-right">' . number_format($total_gst[$origin], 2) . '</td>
                                </tr>
                                <tr>
                                  <td class="color primary text-left"><strong>Total Invoice Amount (PKR)</strong></td>
                                  <td class="color secondary text-right">' . number_format(ROUND($total_invoice_amount[$origin], 0, PHP_ROUND_HALF_DOWN)) . '</td>
                                </tr>
                              </tbody>
                            </table>
                        </div>
                    </div>';

                $amount_in_words = '';
                $amount_in_words = self::amount_to_words(ROUND($total_invoice_amount[$origin], 0, PHP_ROUND_HALF_DOWN));

                $html .= '<table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="color primary" style="width: 150px;"><strong>Amount in Words</strong></td>
                          <td class="color secondary">' . $amount_in_words . ' Only</td>
                        </tr>
                      </tbody>
                    </table>

                    <table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="color primary text-center" colspan="2"><strong>Bank Account Details</strong></td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Benificiary Name</strong></td>
                          <td>Trax Online Private Limited</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Bank</strong></td>
                          <td>Meezan Bank</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Account No.</strong></td>
                          <td>0102951143</td>
                        </tr> 
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Branch Code.</strong></td>
                          <td>9912</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Branch Address.</strong></td>
                          <td>Liaqatabad Market Malir Branch</td>
                        </tr>
                        </tbody>
                    </table>

                    <div class="mb-1 text-center font-italic"><strong>Disclaimer:</strong> This is a system generated invoice. No signature required.</div>
            ';

                $invoice_number_serial_number++;
            }
        } else {

            $html .= '
                <div>
                  <div class="p-1">
        ';
            $html .= '
                    <div class="row align-items-start justify-content-between summary">
                        <div class="col-6">
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                    <td class="color primary" colspan="2"><strong>Customer Details</strong></td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Account No.</strong></td>
                                    <td>' . str_pad($shipper->id, 6, '0', STR_PAD_LEFT) . '</td>
                                </tr>';
            if ($account_type_id == 2) {
                $html .= '<tr>
                                        <td class="color secondary"><strong>Shipper Name</strong></td>
                                        <td>' . $shipper->name . '</td>
                                    </tr>';
            }
            $html .= '<tr>
                                    <td class="color secondary"><strong>Name</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_person_name : $shipper->name) . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Address</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_address : $shipper->address) . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Contact No.</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_person_phone : $shipper->phone) . '</td>
                                </tr>
                                <tr>
                                  <td class="color secondary"><strong>NTN</strong></td>
                                  <td>' . $shipper->ntn_no . '</td>
                                </tr>  ';

            $html .= '</tbody>
                            </table>
                        </div>

                        <div class="col-4">
                            <table class="table table-sm table-bordered border">
                              <tbody> ';


            $html .= '<tr>
                                    <td class="color primary"><strong>Billing Period</strong></td>
                                    <td>' . Carbon::parse($invoice->billing_period_from_date)->format('Y-m-d') . ' <-> ' . Carbon::parse($invoice->billing_period_to_date)->format('Y-m-d') . '</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>Invoice No.</strong></td>
                                    <td>' . $invoice->invoice_number . ' - ' . $invoice_number_serial_number . '</td>
                                </tr>
                                <tr> ';
            if ($invoice->invoice_type == 1) {
                $html .= '<td class="color primary"><strong>Invoice Date</strong></td>
                                    <td>' . Carbon::parse($invoice->invoicing_date)->format('Y-m-d') . '</td>
                                </tr>';

            }

            $html .= '<tr>
                                    <td class="color primary"><strong>Due Date</strong></td>
                                    <td>' . Carbon::parse($invoice->due_date)->format('Y-m-d') . '</td>
                                </tr>
                               </tbody>
                            </table>
                        </div>
                    </div>
            ';

            $html .= '<table class="table table-sm table-bordered border">
          <thead>
            <tr>
                <th colspan="12" class="color primary text-center">Invoice Summary</th>
            </tr>
            <tr>
                <th class="color secondary">Origin</th>
                <th class="color secondary">Description</th>
                <th class="color secondary">Rates</th>
                <th class="color secondary">Quantity</th>
                <th class="color secondary">Total Charges Without GST</th>
                <th class="color secondary">SST %</th>
                <th class="color secondary">SST Amount</th>
                <th class="color secondary">Total Amount with SST</th>
              
            </tr>
          </thead>
          <tbody>
';
            $rates_total = 0;
            $quantity_total = 0;
            $total_amount_without_gst = 0;
            $total_sst_amount = 0;
            $overall_amount = 0;


            foreach ($packaging_details as $cities) {
                foreach ($cities as $packaging_material) {
                    $amount_without_gst = $packaging_material['rates'] * $packaging_material['quantity'];
                    $sst_amount = round($amount_without_gst * $packaging_material['gst']);
                    $total_amount_with_sst = round($amount_without_gst + $sst_amount);

                    $rates_total = $rates_total + $packaging_material['rates'];
                    $quantity_total = $quantity_total + $packaging_material['quantity'];
                    $total_amount_without_gst = $total_amount_without_gst + $amount_without_gst;
                    $total_sst_amount = $total_sst_amount + $sst_amount;
                    $overall_amount = $overall_amount + $total_amount_with_sst;
                    $html .= '
                        <tr>
                            <td>' . $packaging_material['origin'] . '</td>
                            <td>' . $packaging_material['description'] . '</td>
                            <td>' . $packaging_material['rates'] . '</td>
                            <td>' . $packaging_material['quantity'] . '</td>
                            <td>' . $amount_without_gst . '</td>
                            <td>' . $packaging_material['gst'] * 100 . '%' . '</td>
                            <td>' . round($sst_amount) . '</td>
                            <td>' . number_format($total_amount_with_sst) . '</td>
                          
                        </tr>';
                }
            }


            $html .= '<tr>
                <td colspan="3" class="text-center">Total Amount</td>
            
                <td>' . $quantity_total . '</td>
                <td>' . $total_amount_without_gst . '</td>
                <td></td>
                <td>' . number_format($total_sst_amount) . '</td>
                <td>' . number_format($overall_amount) . '</td>
            </tr>';
            $amount_in_words = '';
            $amount_in_words = self::amount_to_words($overall_amount);

            $html .= '<table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="color primary" style="width: 150px;"><strong>Amount in Words</strong></td>
                          <td class="color secondary">' . $amount_in_words . ' Only</td>
                        </tr>
                      </tbody>
                    </table>

                    <table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="color primary text-center" colspan="2"><strong>Bank Account Details</strong></td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Benificiary Name</strong></td>
                          <td>Trax Online Private Limited</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Bank</strong></td>
                          <td>Meezan Bank</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Account No.</strong></td>
                          <td>0102951143</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>IBAN No.</strong></td>
                          <td>PK02MEZN0099120104111731</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Branch Name.</strong></td>
                          <td>Liaquat Market Malir Branch</td>
                        </tr>';

            $html .= ' <tr>
                                      <td class="color secondary" style="width: 150px;"><strong>Branch Code.</strong></td>
                                      <td>9912</td>
                                    </tr>
                                    </tbody>
                    </table>

                    <div class="mb-1 text-center font-italic"><strong>Disclaimer:</strong> This is a system generated invoice. No signature required.</div>
            ';


            $html .= '
                      </tbody>
                    </table>
            ';


        }

        $html .= '
                  </div>
                </div>
        ';

        if (!$email) {
            $html .= '
                <script>
                  window.onload = function() {

                    window.print();
                  }
                </script>
              </body>
            </html>
            ';
        }

        return $html;
    }

    static public function generate_reimbursement_invoice_print_origin_wise($id, $email = FALSE)
    {
        $invoice = InvoiceForReimbursement::find($id);

        $shipper = $invoice->shipper;

        $shipper_bank = $shipper->bank()->where('default_bank', 1)->first();

        $account_type_id = $shipper->account_type_id;

        $html = '';

        if (!$email) {
            $html .= '
            <!doctype html>
            <html lang="en">
              <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                <title>Invoice</title>
            ';
        }

        $html .= '
                <style>' . file_get_contents(public_path('app-assets/css/bootstrap.min.css')) . '</style>
        ';

        $html .= '
            <style>@page{size:A4 portrait; margin-top: 12rem; margin-bottom: 2rem; margin-left: 0rem; margin-right: 0rem;}*{-webkit-print-color-adjust:exact!important;color-adjust:exact!important}body{background:none!important;color:#09262e!important;font-size:0.7rem!important}hr{border-top:1px dashed #000}table.table-bordered{page-break-inside:avoid}table.table-bordered thead tr th, table.table-bordered tbody tr td{border:1px solid #09262e!important}.color.primary{background:#c8c8c8!important}.color.secondary{background:#ebebeb!important}.border{border:1px solid #09262e!important}.summary{page-break-before:always;page-break-inside:avoid}.shipments_summary{page-break-before:always}</style>
        ';

        if (!$email) {
            $html .= '
              </head>
              <body>
            ';
        }

        $shipment_details = array();

        $shipment_counts = array();

        $origins = array();

        $total_charges = array();
        $total_gst = array();
        $total_invoice_amount = array();

        foreach ($invoice->invoice_shipments as $invoice_shipment) {
            $shipment = $invoice_shipment->shipment;

            $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 2);

            if ($shipment_journey->exists()) {
                $date = $shipment_journey->first()->created_at;
            } else {
                $date = $shipment->created_at;
            }

            $date = Carbon::parse($date)->format('Y-m-d');

            $origin = $shipment->pickup_address->city->name;

            if (!in_array($origin, $origins)) {
                $origins[] = $origin;
            }

            if (!isset($shipment_counts[$origin])) {
                $shipment_counts[$origin] = 0;
            }

            $shipment_counts[$origin]++;

            if (!isset($total_weight_charges[$origin])) {
                $total_weight_charges[$origin] = 0;
            }

            if (!isset($total_cash_handling_charges[$origin])) {
                $total_cash_handling_charges[$origin] = 0;
            }

            if (!isset($total_insurance_charges[$origin])) {
                $total_insurance_charges[$origin] = 0;
            }

            if (!isset($total_return_charges[$origin])) {
                $total_return_charges[$origin] = 0;
            }

            if (!isset($total_fuel_surcharge[$origin])) {
                $total_fuel_surcharge[$origin] = 0;
            }

            if (!isset($total_replacement_charges[$origin])) {
                $total_replacement_charges[$origin] = 0;
            }

            if (!isset($total_try_and_buy_charges[$origin])) {
                $total_try_and_buy_charges[$origin] = 0;
            }

            if (!isset($total_packaging_material_charges[$origin])) {
                $total_packaging_material_charges[$origin] = 0;
            }

            if (!isset($total_intercept_charges[$origin])) {
                $total_intercept_charges[$origin] = 0;
            }

            if (!isset($total_nsa_osa_charges[$origin])) {
                $total_nsa_osa_charges[$origin] = 0;
            }

            if (!isset($total_adjustment_charges[$origin])) {
                $total_adjustment_charges[$origin] = 0;
            }

            if (!isset($total_charges[$origin])) {
                $total_charges[$origin] = 0;
            }

            if (!isset($total_gst[$origin])) {
                $total_gst[$origin] = 0;
            }

            if (!isset($total_invoice_amount[$origin])) {
                $total_invoice_amount[$origin] = 0;
            }

            if ($invoice_shipment->type != 2) {
                if ($invoice_shipment->type == 0) {
                    $total_cash_handling_charges[$origin] += $shipment->cash_handling_charges;
                    $total_replacement_charges[$origin] += $shipment->replacement_charges;
                    $total_try_and_buy_charges[$origin] += $shipment->try_and_buy_charges;
                } else {
                    $total_return_charges[$origin] += $shipment->return_charges;
                }

                $total_weight_charges[$origin] += $shipment->weight_charges;

                if ($shipment->packaging_material_request) {
                    $total_packaging_material_charges[$origin] += $shipment->packaging_material_charges;
                }

                $total_insurance_charges[$origin] += $shipment->insurance_charges;
                $total_fuel_surcharge[$origin] += $shipment->fuel_surcharge;
                $total_intercept_charges[$origin] += $shipment->intercept_charges;
                $total_nsa_osa_charges[$origin] += $shipment->nsa_osa_charges;
            } else {
                $total_adjustment_charges[$origin] += $invoice_shipment->invoice_amount;
            }

            $total_charges[$origin] += $invoice_shipment->charges;
            $total_gst[$origin] += $invoice_shipment->gst;
            $total_invoice_amount[$origin] += $invoice_shipment->invoice_amount;
        }

        $invoice_number_serial_number = 1;

        $html .= '
                <div>
                  <div class="p-1">
        ';

        foreach ($origins as $origin) {

            $html .= '
                <div>
                  <div class="p-1">
        ';
            $html .= '
                    <div class="row align-items-start justify-content-between summary">
                        
                        <div class="col-6">
                            <h1><b><u>SALES TAX INVOICE</u></b></h1>
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                    <td class="color primary" colspan="2"><strong>Bill To</strong></td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Account No.</strong></td>
                                    <td>' . str_pad($shipper->id, 6, '0', STR_PAD_LEFT) . '</td>
                                </tr>';
            if ($account_type_id == 2) {
                $html .= '<tr>
                                        <td class="color secondary"><strong>Shipper Name</strong></td>
                                        <td>' . $shipper->name . '</td>
                                    </tr>';
            }
            $html .= '<tr>
                                    <td class="color secondary"><strong>Name</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_person_name : $shipper->name) . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Address</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_address : $shipper->address) . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Contact No.</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_person_phone : $shipper->phone) . '</td>
                                </tr>
                                <tr>
                                  <td class="color secondary"><strong>NTN</strong></td>
                                  <td>' . $shipper->ntn_no . '</td>
                                </tr>  ';

            $html .= '<tr>
                                  <td class="color secondary"><strong>STRN</strong></td>
                                  <td>' . $shipper->strn_no . '</td>
                                </tr>';


            $html .= '</tbody>
                            </table>
                        </div>

                        <div class="col-4">
                            <h1 style="text-align:center"><img style="height:85px" src="' . asset('img/invoice_summary_header_logo.png') . '" class="header"></h1>
                            <table class="table table-sm table-bordered border">
                              <tbody> ';
            $html .= '<tr>
                                    <td class="color primary"><strong>SNTN</strong></td>
                                    <td>S-7930679-5</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>PNTN</strong></td>
                                    <td>P-7930679-5</td>
                                </tr>';

            $html .= '<tr>
                                    <td class="color primary"><strong>Billing Period</strong></td>
                                    <td>' . Carbon::parse($invoice->billing_period_from_date)->format('Y-m-d') . ' <-> ' . Carbon::parse($invoice->billing_period_to_date)->format('Y-m-d') . '</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>Invoice No.</strong></td>
                                    <td>' . $invoice->invoice_number . ' - ' . $invoice_number_serial_number . '</td>
                                </tr>
                                <tr> ';

            $html .= '<td class="color primary"><strong>Invoice Date</strong></td>
                                    <td>' . Carbon::parse($invoice->invoicing_date)->format('Y-m-d') . '</td>
                                </tr>';


            $html .= '
                               </tbody>
                            </table>
                        </div>
                    </div>
            ';

            $html .= '
                    <table class="table table-sm table-bordered border">
                      <thead>
                        <tr>
                            <th colspan="3" class="color primary text-center">Invoice Summary - ' . $origin . '</th>
                        </tr>
                        <tr>
                            <th class="color secondary">Description Of Services</th>
                            <th class="color secondary" style="width:50px;">Quantity</th>
                            <th class="color secondary">Total Charges</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Weight Charges</td>
                            <td rowspan="11">' . $shipment_counts[$origin] . '</td>
                            <td>' . number_format($total_weight_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Fuel Surcharge</td>
                            <td>' . number_format($total_fuel_surcharge[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Cash Handling Charges</td>
                            <td>' . number_format($total_cash_handling_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Insurance Charges</td>
                            <td>' . number_format($total_insurance_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Replacement Charges</td>
                            <td>' . number_format($total_replacement_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Try & Buy Charges</td>
                            <td>' . number_format($total_try_and_buy_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Return Charges</td>
                            <td>' . number_format($total_return_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Intercept Charges</td>
                            <td>' . number_format($total_intercept_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>OSA Charges</td>
                            <td>' . number_format($total_nsa_osa_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Packaging Charges</td>
                            <td>' . number_format($total_packaging_material_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Adjustments</td>
                            <td>' . number_format($total_adjustment_charges[$origin], 2) . '</td>
                        </tr>
                      </tbody>
                      </table>
            ';
            $html .= '
                    <div class="row justify-content-end">
                        <div class="col-4">
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                  <td class="color secondary text-left"><strong>Subtotal (PKR)</strong></td>
                                  <td class="text-right">' . number_format($total_charges[$origin], 2) . '</td>
                                </tr>
                                <tr>
                                  <td class="color secondary text-left"><strong>SST (PKR)</strong></td>
                                  <td class="text-right">' . number_format($total_gst[$origin], 2) . '</td>
                                </tr>
                                <tr>
                                  <td class="color primary text-left"><strong>Total Invoice Amount (PKR)</strong></td>
                                  <td class="color secondary text-right">' . number_format(ROUND($total_invoice_amount[$origin], 0, PHP_ROUND_HALF_DOWN)) . '</td>
                                </tr>
                              </tbody>
                            </table>
                        </div>
                    </div>';

            $amount_in_words = '';
            $amount_in_words = self::amount_to_words(ROUND($total_invoice_amount[$origin], 0, PHP_ROUND_HALF_DOWN));

            $html .= '<table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="color primary" style="width: 150px;"><strong>Amount in Words</strong></td>
                          <td class="color secondary">' . $amount_in_words . ' Only</td>
                        </tr>
                      </tbody>
                    </table>

                    <table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="color primary text-center" colspan="2"><strong>Bank Account Details</strong></td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Benificiary Name</strong></td>
                          <td>Trax Online Private Limited</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Bank</strong></td>
                          <td>Meezan Bank</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Account No.</strong></td>
                          <td>0102951143</td>
                        </tr> 
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Branch Code.</strong></td>
                          <td>9912</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Branch Address.</strong></td>
                          <td>Liaqatabad Market Malir Branch</td>
                        </tr>
                        </tbody>
                    </table>

                    <div class="mb-1 text-center font-italic"><strong>Disclaimer:</strong> This is a system generated invoice. No signature required.</div>
            ';

            $invoice_number_serial_number++;
        }

        $html .= '
                  </div>
                </div>
        ';

        if (!$email) {
            $html .= '
                <script>
                  window.onload = function() {

                    window.print();
                  }
                </script>
              </body>
            </html>
            ';
        }

        return $html;
    }

    static public function generate_invoice_print_gst_wise($id, $email = FALSE)
    {
        $invoice = Invoice::find($id);

        $shipper = $invoice->shipper;

        $shipper_bank = $shipper->bank()->where('default_bank', 1)->first();

        $account_type_id = $shipper->account_type_id;

        $html = '';

        if (!$email) {
            $html .= '
            <!doctype html>
            <html lang="en">
              <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                <title>Invoice</title>
            ';
        }

        $html .= '
                <style>' . file_get_contents(public_path('app-assets/css/bootstrap.min.css')) . '</style>
        ';

        $html .= '
            <style>@page{size:A4 portrait; margin-top: 12rem; margin-bottom: 2rem; margin-left: 0rem; margin-right: 0rem;}*{-webkit-print-color-adjust:exact!important;color-adjust:exact!important}body{background:none!important;color:#09262e!important;font-size:0.7rem!important}hr{border-top:1px dashed #000}table.table-bordered{page-break-inside:avoid}table.table-bordered thead tr th, table.table-bordered tbody tr td{border:1px solid #09262e!important}.color.primary{background:#c8c8c8!important}.color.secondary{background:#ebebeb!important}.border{border:1px solid #09262e!important}.summary{page-break-before:always;page-break-inside:avoid}.shipments_summary{page-break-before:always}</style>
        ';

        if (!$email) {
            $html .= '
              </head>
              <body>
            ';
        }

        $shipment_details = array();

        $serial_number = array();

        $origins = array();
        $origin_city_ids = array();
        $origins_gst_wise = array();
        $origins_gst_wise_city_ids = array();

        $total_charges = array();
        $total_gst = array();
        $total_packaging_material_charges = array();
        $total_invoice_amount = array();

        foreach ($invoice->invoice_shipments as $invoice_shipment) {
            $shipment = $invoice_shipment->shipment;
            
            $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 2);

            if ($shipment_journey->exists()) {
                $date = $shipment_journey->first()->created_at;
            } else {
                $date = $shipment->created_at;
            }

            $date = Carbon::parse($date)->format('Y-m-d');

            $origin_city = $shipment->pickup_address->city;

            $origin = $origin_city->name;
            $origin_city_id = $origin_city->id;
            $gst = self::gst($origin_city->zone_id);

            if (!in_array($origin, $origins)) {
                $origins[] = $origin;

                if (!isset($origins_gst_wise[$gst])) {
                    $origins_gst_wise[$gst] = array();
                }

                $origins_gst_wise[$gst][] = $origin;
            }
            /* echo $origin_city_id;*/
            //for packaging
            if (!in_array($origin_city_id, $origin_city_ids)) {
                $origin_city_ids[] = $origin_city_id;

                if (!isset($origins_gst_wise_city_ids[$gst])) {
                    $origins_gst_wise_city_ids[$gst] = array();
                }
                $origins_gst_wise_city_ids[$gst][] = $origin_city_id;
            }

            if (!isset($shipment_details[$origin])) {
                $shipment_details[$origin] = '';
            }

            if (!isset($serial_number[$origin])) {
                $serial_number[$origin] = 1;
            }

            $shipment_details[$origin] .= '
                        <tr>
                          <td>' . $serial_number[$origin] . '</td>
                          <td>' . $shipment->tracking_number . '</td>
                          <td>' . $shipment->order_id . '</td>
                          <td>' . $shipment->consignee_city->name . '</td>
                          <td>' . $shipment->shipping_mode->mode . '</td>
                          <td>' . $date . '</td>
                          <td>' . $shipment->actual_weight . '</td>
                          <td>' . (($invoice_shipment->type != 2) ? number_format($shipment->weight_charges, 2) : '0') . '</td>
                          <td>' . (($invoice_shipment->type != 2) ? number_format($shipment->fuel_surcharge, 2) : '0') . '</td>
                          <td>' . (($invoice_shipment->type != 2) ? number_format($shipment->nsa_osa_charges, 2) : '0') . '</td>
                          <td>' . (($invoice_shipment->type == 2) ? number_format($invoice_shipment->invoice_amount, 2) : '0') . '</td>
                          <td>' . (($invoice_shipment->type == 2) ? number_format($shipment->packaging_material_charges, 2) : '0') . '</td>
                          <td>' . (($invoice_shipment->type != 2) ? number_format($shipment->esc_charges, 2) : '0') . '</td>
                          <td>' . number_format($invoice_shipment->charges, 2) . '</td>
                          <td>' . number_format($invoice_shipment->gst, 2) . '</td>
                          <td>' . number_format($invoice_shipment->invoice_amount, 2) . '</td>
                        </tr>
            ';

            $serial_number[$origin]++;

            if (!isset($total_weight_charges[$origin])) {
                $total_weight_charges[$origin] = 0;
            }

            if (!isset($total_cash_handling_charges[$origin])) {
                $total_cash_handling_charges[$origin] = 0;
            }

            if (!isset($total_insurance_charges[$origin])) {
                $total_insurance_charges[$origin] = 0;
            }

            if (!isset($total_return_charges[$origin])) {
                $total_return_charges[$origin] = 0;
            }

            if (!isset($total_fuel_surcharge[$origin])) {
                $total_fuel_surcharge[$origin] = 0;
            }

            if (!isset($total_replacement_charges[$origin])) {
                $total_replacement_charges[$origin] = 0;
            }

            if (!isset($total_try_and_buy_charges[$origin])) {
                $total_try_and_buy_charges[$origin] = 0;
            }

            if (!isset($total_intercept_charges[$origin])) {
                $total_intercept_charges[$origin] = 0;
            }

            if (!isset($total_nsa_osa_charges[$origin])) {
                $total_nsa_osa_charges[$origin] = 0;
            }

            if (!isset($total_adjustment_charges[$origin])) {
                $total_adjustment_charges[$origin] = 0;
            }

            if (!isset($total_extra_service_charges[$origin])) {
                $total_extra_service_charges[$origin] = 0;
            }

            if (!isset($total_charges[$gst])) {
                $total_charges[$gst] = 0;
            }

            if (!isset($total_gst[$gst])) {
                $total_gst[$gst] = 0;
            }

            if (!isset($total_packaging_material_charges[$gst])) {
                $total_packaging_material_charges[$gst] = 0;
            }

            if (!isset($total_invoice_amount[$gst])) {
                $total_invoice_amount[$gst] = 0;
            }

            if ($invoice_shipment->type != 2) {
                if ($invoice_shipment->type == 0) {
                    $total_cash_handling_charges[$origin] += $shipment->cash_handling_charges;
                    $total_replacement_charges[$origin] += $shipment->replacement_charges;
                    $total_try_and_buy_charges[$origin] += $shipment->try_and_buy_charges;
                    $total_extra_service_charges[$origin] += $shipment->esc_charges;
                } else {
                    $total_return_charges[$origin] += $shipment->return_charges;
                }

                $total_weight_charges[$origin] += $shipment->weight_charges;
                $total_insurance_charges[$origin] += $shipment->insurance_charges;
                $total_fuel_surcharge[$origin] += $shipment->fuel_surcharge;
                $total_intercept_charges[$origin] += $shipment->intercept_charges;
                $total_nsa_osa_charges[$origin] += $shipment->nsa_osa_charges;
            } else {
                $total_adjustment_charges[$origin] += $invoice_shipment->invoice_amount;
            }

            if ($invoice_shipment->type != 2 && $shipment->packaging_material_request) {
                $total_packaging_material_charges[$gst] += $shipment->packaging_material_charges;

                $total_charges[$gst] += ($invoice_shipment->charges - $shipment->packaging_material_charges);
            } else {
                $total_charges[$gst] += $invoice_shipment->charges;
            }

            $total_gst[$gst] += $invoice_shipment->gst;
            $total_invoice_amount[$gst] += $invoice_shipment->invoice_amount;
        }

        $invoice_number_serial_number = 1;

        $html .= '
                <div>
                  <div class="p-1">
        ';

        if ($invoice->invoice_type == 1) {
            foreach ($origins_gst_wise as $gst => $origins) {
                $html .= '
                    <div class="row align-items-start justify-content-between summary">
                        <div class="col-6">
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                    <td class="color primary" colspan="2"><strong>Customer Details</strong></td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Account No.</strong></td>
                                    <td>' . str_pad($shipper->id, 6, '0', STR_PAD_LEFT) . '</td>
                                </tr>';
                if ($account_type_id == 2) {
                    $html .= '<tr>
                                        <td class="color secondary"><strong>Shipper Name</strong></td>
                                        <td>' . $shipper->name . '</td>
                                    </tr>';
                }
                $html .= '<tr>
                                    <td class="color secondary"><strong>Name</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_person_name : $shipper->name) . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Address</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_address : $shipper->address) . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Contact No.</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_person_phone : $shipper->phone) . '</td>
                                </tr>
                                <tr>
                                  <td class="color secondary"><strong>NTN</strong></td>
                                  <td>' . $shipper->ntn_no . '</td>
                                </tr>
                                <tr>
                                  <td class="color secondary"><strong>STRN</strong></td>
                                  <td>' . $shipper->strn_no . '</td>
                                </tr>
                               </tbody>
                            </table>
                        </div>

                        <div class="col-4">
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                    <td class="color primary"><strong>NTN</strong></td>
                                    <td>7930679-5</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>SNTN</strong></td>
                                    <td>S-7930679-5</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>PNTN</strong></td>
                                    <td>P-7930679-5</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>Billing Period</strong></td>
                                    <td>' . Carbon::parse($invoice->billing_period_from_date)->format('Y-m-d') . ' <-> ' . Carbon::parse($invoice->billing_period_to_date)->format('Y-m-d') . '</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>Invoice No.</strong></td>
                                    <td>' . $invoice->invoice_number . ' - ' . $invoice_number_serial_number . '</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>Invoice Date</strong></td>
                                    <td>' . Carbon::parse($invoice->invoicing_date)->format('Y-m-d') . '</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>Due Date</strong></td>
                                    <td>' . Carbon::parse($invoice->due_date)->format('Y-m-d') . '</td>
                                </tr>
                               </tbody>
                            </table>
                        </div>
                    </div>
            ';

                $html .= '
                    <table class="table table-sm table-bordered border">
                      <thead>
                        <tr>
                            <th colspan="12" class="color primary text-center">Invoice Summary</th>
                        </tr>
                        <tr>
                            <th class="color secondary">Origin</th>
                            <th class="color secondary">Weight Charges (PKR)</th>
                            <th class="color secondary">Cash Handling Charges (PKR)</th>
                            <th class="color secondary">Insurance Charges (PKR)</th>
                            <th class="color secondary">Replacement Charges (PKR)</th>
                            <th class="color secondary">Try & Buy Charges (PKR)</th>
                            <th class="color secondary">Return Charges (PKR)</th>
                            <th class="color secondary">Fuel Surcharge (PKR)</th>
                            <th class="color secondary">Intercept Charges (PKR)</th>
                            <th class="color secondary">OSA Charges (PKR)</th>
                            <th class="color secondary">Adjustment Charges (PKR)</th>
                            <th class="color secondary">Extra Service Charges (PKR)</th>
                        </tr>
                      </thead>
                      <tbody>
            ';

                foreach ($origins as $origin) {
                    $html .= '
                        <tr>
                            <td>' . $origin . '</td>
                            <td>' . number_format($total_weight_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_cash_handling_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_insurance_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_replacement_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_try_and_buy_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_return_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_fuel_surcharge[$origin], 2) . '</td>
                            <td>' . number_format($total_intercept_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_nsa_osa_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_adjustment_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_extra_service_charges[$origin], 2) . '</td>
                        </tr>
                ';
                }

                $html .= '
                      </tbody>
                    </table>

                    <div class="row justify-content-end">
                        <div class="col-4">
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                  <td class="color secondary text-left"><strong>Subtotal (PKR)</strong></td>
                                  <td class="text-right">' . number_format($total_charges[$gst], 2) . '</td>
                                </tr>
                                <tr>
                                  <td class="color secondary text-left"><strong>GST (PKR)</strong></td>
                                  <td class="text-right">' . number_format($total_gst[$gst], 2) . '</td>
                                </tr>
                                <tr>
                                  <td class="color secondary text-left"><strong>Packaging Charges (PKR)</strong></td>
                                  <td class="text-right">' . number_format($total_packaging_material_charges[$gst], 2) . '</td>
                                </tr>
                                <tr>
                                  <td class="color primary text-left"><strong>Total Invoice Amount (PKR)</strong></td>
                                  <td class="color secondary text-right">' . number_format(ROUND($total_invoice_amount[$gst], 0, PHP_ROUND_HALF_DOWN)) . '</td>
                                </tr>
                              </tbody>
                            </table>
                        </div>
                    </div>

                    <table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="color primary" style="width: 150px;"><strong>Amount in Words</strong></td>
                          <td class="color secondary">' . self::amount_to_words($total_invoice_amount[$gst]) . ' Only</td>
                        </tr>
                      </tbody>
                    </table>

                    <table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="color primary text-center" colspan="2"><strong>Bank Account Details</strong></td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Benificiary Name</strong></td>
                          <td>Trax Online Private Limited</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Bank</strong></td>
                          <td>Meezan Bank</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Account No.</strong></td>
                          <td>0102951143</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Branch No.</strong></td>
                          <td>9912</td>
                        </tr>
                      </tbody>
                    </table>

                    <div class="mb-1 text-center font-italic"><strong>Disclaimer:</strong> This is a system generated invoice. No signature required.</div>
            ';

                foreach ($origins as $origin) {
                    $html .= '
                    <table class="table table-sm table-bordered border shipments_summary">
                      <thead>
                        <tr>
                            <th class="color primary text-center" colspan="16">Shipment(s) Summary - ' . $origin . '</th>
                        </tr>
                        <tr>
                          <th class="color secondary">S. No.</th>
                          <th class="color secondary">Tracking No.</th>
                          <th class="color secondary">Order ID</th>
                          <th class="color secondary">Destination</th>
                          <th class="color secondary">Shipping Mode</th>
                          <th class="color secondary">Arrival Date</th>
                          <th class="color secondary">Weight (kg)</th>
                          <th class="color secondary">Weight Charges (PKR)</th>
                          <th class="color secondary">Fuel Surcharge (PKR)</th>
                          <th class="color secondary">OSA Charges (PKR)</th>
                          <th class="color secondary">Adjustment Charges (PKR)</th>
                          <th class="color secondary">Packaging Charges (PKR)</th>
                          <th class="color secondary">Extra Service Charges (PKR)</th>
                          <th class="color secondary">Total Charges (PKR)</th>
                          <th class="color secondary">GST (PKR)</th>
                          <th class="color secondary">Invoice Amount (PKR)</th>
                        </tr>
                    </thead>
                    <tbody>
                ';

                    $html .= $shipment_details[$origin];

                    $html .= '
                      </tbody>
                    </table>
                ';
                }

                $invoice_number_serial_number++;
            }
        } else {

            foreach ($origins_gst_wise_city_ids as $gst => $origins) {

                $size_array = array();
                $packaging_details = array();
                $shipment_ids = $invoice->invoice_shipments->pluck('shipment_id')->toArray();

                if (count($shipment_ids) > 0) {

                    $html .= '
                    <div class="row align-items-start justify-content-between summary">
                        <div class="col-6">
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                    <td class="color primary" colspan="2"><strong>Customer Details</strong></td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Account No.</strong></td>
                                    <td>' . str_pad($shipper->id, 6, '0', STR_PAD_LEFT) . '</td>
                                </tr>';
                    if ($account_type_id == 2) {
                        $html .= '<tr>
                                        <td class="color secondary"><strong>Shipper Name</strong></td>
                                        <td>' . $shipper->name . '</td>
                                    </tr>';
                    }
                    $html .= '<tr>
                                    <td class="color secondary"><strong>Name</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_person_name : $shipper->name) . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Address</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_address : $shipper->address) . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Contact No.</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_person_phone : $shipper->phone) . '</td>
                                </tr>
                                <tr>
                                  <td class="color secondary"><strong>NTN</strong></td>
                                  <td>' . $shipper->ntn_no . '</td>
                                </tr>
                                <tr>
                                  <td class="color secondary"><strong>STRN</strong></td>
                                  <td>' . $shipper->strn_no . '</td>
                                </tr>
                               </tbody>
                            </table>
                        </div>

                        <div class="col-4">
                            <table class="table table-sm table-bordered border">
                              <tbody>
                             
                                <tr>
                                    <td class="color primary"><strong>Billing Period</strong></td>
                                    <td>' . Carbon::parse($invoice->billing_period_from_date)->format('Y-m-d') . ' <-> ' . Carbon::parse($invoice->billing_period_to_date)->format('Y-m-d') . '</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>Invoice No.</strong></td>
                                    <td>' . $invoice->invoice_number . ' - ' . $invoice_number_serial_number . '</td>
                                </tr>
                               
                                <tr>
                                    <td class="color primary"><strong>Due Date</strong></td>
                                    <td>' . Carbon::parse($invoice->due_date)->format('Y-m-d') . '</td>
                                </tr>
                               </tbody>
                            </table>
                        </div>
                    </div>
            ';

                    $html .= '<table class="table table-sm table-bordered border">
          <thead>
            <tr>
                <th colspan="11" class="color primary text-center">Invoice Summary</th>
            </tr>
            <tr>
                <th class="color secondary">Origin</th>
                <th class="color secondary">Description</th>
                <th class="color secondary">Rates</th>
                <th class="color secondary">Quantity</th>
                <th class="color secondary">Total Charges Without GST</th>
                <th class="color secondary">SST %</th>
                <th class="color secondary">SST Amount</th>
                <th class="color secondary">Total Amount with SST</th>
              
            </tr>
          </thead><tbody>';
                    $warehouses = Warehouse::whereIn('hub_id', $origins)->get();

                    $hubs = array();
                    foreach ($warehouses as $warehouse) {
                        $hubs = array_merge($warehouse->associated_hubs->pluck('hub_id')->toArray(), $hubs);
                    }

                    $packaging_materials = PackagingMaterialRequest::whereIn('shipment_id', $shipment_ids)->where('packaging_material_requests.status_id', 4)
                        ->whereIn('city_id', $hubs)
                        ->where('packaging_material_requests.user_id', $shipper->id);

                    if ($packaging_materials->exists()) {
                        $packaging_materials = $packaging_materials->get();

                        foreach ($packaging_materials as $packaging_material) {
                            foreach ($packaging_material->items as $details) {
                                if (!isset($packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['quantity'])) {
                                    $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['quantity'] = 1;
                                    $zone = Zone::find($packaging_material->city->zone_id);
                                    $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['origin'] = $packaging_material->city->name;
                                    $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['rates'] = $details->packaging_type_size->standard_charges;
                                    $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['description'] = $details->packaging_type_size->type->type . '-' . $details->packaging_type_size->size;
                                    $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['quantity'] = $details->quantity;
                                    $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['gst'] = $zone->gst;
                                } else {
                                    $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['quantity']++;
                                }

                            }

                        }

                        if (count($packaging_details) > 0) {

                            $rates_total = 0;
                            $quantity_total = 0;
                            $total_amount_without_gst = 0;
                            $total_sst_amount = 0;
                            $overall_amount = 0;

                            /*foreach ($packaging_details as $packaging_material) {

                                $amount_without_gst = $packaging_material['rates'] * $packaging_material['quantity'];
                                $sst_amount = round($amount_without_gst * $packaging_material['gst']);
                                $total_amount_with_sst = round($amount_without_gst + $sst_amount);

                                $rates_total = $rates_total + $packaging_material['rates'];
                                $quantity_total = $quantity_total + $packaging_material['quantity'];
                                $total_amount_without_gst = $total_amount_without_gst + $amount_without_gst;
                                $total_sst_amount = $total_sst_amount + $sst_amount;
                                $overall_amount = $overall_amount + $total_amount_with_sst;
                                $html .= '
                                        <tr>
                                            <td>' . $packaging_material['origin'] . '</td>
                                            <td>' . $packaging_material['description'] . '</td>
                                            <td>' . $packaging_material['rates'] . '</td>
                                            <td>' . $packaging_material['quantity'] . '</td>
                                            <td>' . $amount_without_gst . '</td>
                                            <td>' . $packaging_material['gst'] * 100 . '%' . '</td>
                                            <td>' . round($sst_amount) . '</td>
                                            <td>' . number_format($total_amount_with_sst) . '</td>

                                        </tr>';

                            }*/
                            foreach ($packaging_details as $cities) {
                                foreach ($cities as $packaging_material) {
                                    $amount_without_gst = $packaging_material['rates'] * $packaging_material['quantity'];
                                    $sst_amount = round($amount_without_gst * $packaging_material['gst']);
                                    $total_amount_with_sst = round($amount_without_gst + $sst_amount);

                                    $rates_total = $rates_total + $packaging_material['rates'];
                                    $quantity_total = $quantity_total + $packaging_material['quantity'];
                                    $total_amount_without_gst = $total_amount_without_gst + $amount_without_gst;
                                    $total_sst_amount = $total_sst_amount + $sst_amount;
                                    $overall_amount = $overall_amount + $total_amount_with_sst;
                                    $html .= '
                                            <tr>
                                                <td>' . $packaging_material['origin'] . '</td>
                                                <td>' . $packaging_material['description'] . '</td>
                                                <td>' . $packaging_material['rates'] . '</td>
                                                <td>' . $packaging_material['quantity'] . '</td>
                                                <td>' . $amount_without_gst . '</td>
                                                <td>' . $packaging_material['gst'] * 100 . '%' . '</td>
                                                <td>' . round($sst_amount) . '</td>
                                                <td>' . number_format($total_amount_with_sst) . '</td>
                                              
                                            </tr>';
                                    /* $html .= $packaging_material['origin'];*/
                                }
                            }

                            $html .= '<tr>
                                            <td colspan="3" class="text-center">Total Amount</td>
                                        
                                            <td>' . $quantity_total . '</td>
                                            <td>' . $total_amount_without_gst . '</td>
                                            <td></td>
                                            <td>' . number_format($total_sst_amount) . '</td>
                                            <td>' . number_format($overall_amount) . '</td>
                                        </tr>';
                            $amount_in_words = '';
                            $amount_in_words = self::amount_to_words($overall_amount);

                            $html .= '<table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                  <td class="color primary" style="width: 150px;"><strong>Amount in Words</strong></td>
                                  <td class="color secondary">' . $amount_in_words . ' Only</td>
                                </tr>
                              </tbody>
                            </table>';
                        }

                    }
                }
                $html .= '<table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="color primary text-center" colspan="2"><strong>Bank Account Details</strong></td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Benificiary Name</strong></td>
                          <td>Trax Online Private Limited</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Bank</strong></td>
                          <td>Meezan Bank</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Account No.</strong></td>
                          <td>0102951143</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>IBAN No.</strong></td>
                          <td>PK02MEZN0099120104111731</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Branch Name.</strong></td>
                          <td>Liaquat Market Malir Branch</td>
                        </tr>';

                $html .= ' <tr>
                                      <td class="color secondary" style="width: 150px;"><strong>Branch Code.</strong></td>
                                      <td>9912</td>
                                    </tr>
                                    </tbody>
                    </table>

                    <div class="mb-1 text-center font-italic"><strong>Disclaimer:</strong> This is a system generated invoice. No signature required.</div>
            ';


                $html .= '
                      </tbody>
                    </table>
            ';

                $invoice_number_serial_number++;
            }
        }

        $html .= '
                  </div>
                </div>';

        if (!$email) {
            $html .= '
                <script>
                  window.onload = function() {

                    window.print();
                  }
                </script>
              </body>
            </html>
            ';
        }

        return $html;
    }

    static public function generate_reimbursement_invoice_print_gst_wise($id, $email = FALSE)
    {
        $invoice = InvoiceForReimbursement::find($id);

        $shipper = $invoice->shipper;

        $shipper_bank = $shipper->bank()->where('default_bank', 1)->first();

        $account_type_id = $shipper->account_type_id;

        $html = '';

        if (!$email) {
            $html .= '
            <!doctype html>
            <html lang="en">
              <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                <title>Invoice</title>
            ';
        }

        $html .= '
                <style>' . file_get_contents(public_path('app-assets/css/bootstrap.min.css')) . '</style>
        ';

        $html .= '
            <style>@page{size:A4 portrait; margin-top: 12rem; margin-bottom: 2rem; margin-left: 0rem; margin-right: 0rem;}*{-webkit-print-color-adjust:exact!important;color-adjust:exact!important}body{background:none!important;color:#09262e!important;font-size:0.7rem!important}hr{border-top:1px dashed #000}table.table-bordered{page-break-inside:avoid}table.table-bordered thead tr th, table.table-bordered tbody tr td{border:1px solid #09262e!important}.color.primary{background:#c8c8c8!important}.color.secondary{background:#ebebeb!important}.border{border:1px solid #09262e!important}.summary{page-break-before:always;page-break-inside:avoid}.shipments_summary{page-break-before:always}</style>
        ';

        if (!$email) {
            $html .= '
              </head>
              <body>
            ';
        }

        $shipment_details = array();

        $serial_number = array();

        $origins = array();
        $origins_gst_wise = array();

        $total_charges = array();
        $total_gst = array();
        $total_packaging_material_charges = array();
        $total_invoice_amount = array();

        foreach ($invoice->invoice_shipments as $invoice_shipment) {
            $shipment = $invoice_shipment->shipment;

            $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 2);

            if ($shipment_journey->exists()) {
                $date = $shipment_journey->first()->created_at;
            } else {
                $date = $shipment->created_at;
            }

            $date = Carbon::parse($date)->format('Y-m-d');

            $origin_city = $shipment->pickup_address->city;

            $origin = $origin_city->name;
            $gst = self::gst($origin_city->zone_id);

            if (!in_array($origin, $origins)) {
                $origins[] = $origin;

                if (!isset($origins_gst_wise[$gst])) {
                    $origins_gst_wise[$gst] = array();
                }

                $origins_gst_wise[$gst][] = $origin;
            }

            if (!isset($shipment_details[$origin])) {
                $shipment_details[$origin] = '';
            }

            if (!isset($serial_number[$origin])) {
                $serial_number[$origin] = 1;
            }

            $shipment_details[$origin] .= '
                        <tr>
                          <td>' . $serial_number[$origin] . '</td>
                          <td>' . $shipment->tracking_number . '</td>
                          <td>' . $shipment->order_id . '</td>
                          <td>' . $shipment->consignee_city->name . '</td>
                          <td>' . $shipment->shipping_mode->mode . '</td>
                          <td>' . $date . '</td>
                          <td>' . $shipment->actual_weight . '</td>
                          <td>' . (($invoice_shipment->type != 2) ? number_format($shipment->weight_charges, 2) : '0') . '</td>
                          <td>' . (($invoice_shipment->type != 2) ? number_format($shipment->fuel_surcharge, 2) : '0') . '</td>
                          <td>' . (($invoice_shipment->type != 2) ? number_format($shipment->nsa_osa_charges, 2) : '0') . '</td>
                          <td>' . (($invoice_shipment->type == 2) ? number_format($invoice_shipment->invoice_amount, 2) : '0') . '</td>
                          <td>' . (($invoice_shipment->type == 2) ? number_format($shipment->packaging_material_charges, 2) : '0') . '</td>
                          <td>' . number_format($invoice_shipment->charges, 2) . '</td>
                          <td>' . number_format($invoice_shipment->gst, 2) . '</td>
                          <td>' . number_format($invoice_shipment->invoice_amount, 2) . '</td>
                        </tr>
            ';

            $serial_number[$origin]++;

            if (!isset($total_weight_charges[$origin])) {
                $total_weight_charges[$origin] = 0;
            }

            if (!isset($total_cash_handling_charges[$origin])) {
                $total_cash_handling_charges[$origin] = 0;
            }

            if (!isset($total_insurance_charges[$origin])) {
                $total_insurance_charges[$origin] = 0;
            }

            if (!isset($total_return_charges[$origin])) {
                $total_return_charges[$origin] = 0;
            }

            if (!isset($total_fuel_surcharge[$origin])) {
                $total_fuel_surcharge[$origin] = 0;
            }

            if (!isset($total_replacement_charges[$origin])) {
                $total_replacement_charges[$origin] = 0;
            }

            if (!isset($total_try_and_buy_charges[$origin])) {
                $total_try_and_buy_charges[$origin] = 0;
            }

            if (!isset($total_intercept_charges[$origin])) {
                $total_intercept_charges[$origin] = 0;
            }

            if (!isset($total_nsa_osa_charges[$origin])) {
                $total_nsa_osa_charges[$origin] = 0;
            }

            if (!isset($total_adjustment_charges[$origin])) {
                $total_adjustment_charges[$origin] = 0;
            }

            if (!isset($total_charges[$gst])) {
                $total_charges[$gst] = 0;
            }

            if (!isset($total_gst[$gst])) {
                $total_gst[$gst] = 0;
            }

            if (!isset($total_packaging_material_charges[$gst])) {
                $total_packaging_material_charges[$gst] = 0;
            }

            if (!isset($total_invoice_amount[$gst])) {
                $total_invoice_amount[$gst] = 0;
            }

            if ($invoice_shipment->type != 2) {
                if ($invoice_shipment->type == 0) {
                    $total_cash_handling_charges[$origin] += $shipment->cash_handling_charges;
                    $total_replacement_charges[$origin] += $shipment->replacement_charges;
                    $total_try_and_buy_charges[$origin] += $shipment->try_and_buy_charges;
                } else {
                    $total_return_charges[$origin] += $shipment->return_charges;
                }

                $total_weight_charges[$origin] += $shipment->weight_charges;
                $total_insurance_charges[$origin] += $shipment->insurance_charges;
                $total_fuel_surcharge[$origin] += $shipment->fuel_surcharge;
                $total_intercept_charges[$origin] += $shipment->intercept_charges;
                $total_nsa_osa_charges[$origin] += $shipment->nsa_osa_charges;
            } else {
                $total_adjustment_charges[$origin] += $invoice_shipment->invoice_amount;
            }

            if ($invoice_shipment->type != 2 && $shipment->packaging_material_request) {
                $total_packaging_material_charges[$gst] += $shipment->packaging_material_charges;

                $total_charges[$gst] += ($invoice_shipment->charges - $shipment->packaging_material_charges);
            } else {
                $total_charges[$gst] += $invoice_shipment->charges;
            }

            $total_gst[$gst] += $invoice_shipment->gst;
            $total_invoice_amount[$gst] += $invoice_shipment->invoice_amount;
        }

        $invoice_number_serial_number = 1;

        $html .= '
                <div>
                  <div class="p-1">
        ';

        foreach ($origins_gst_wise as $gst => $origins) {
            $html .= '
                    <div class="row align-items-start justify-content-between summary">
                        <div class="col-6">
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                    <td class="color primary" colspan="2"><strong>Customer Details</strong></td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Account No.</strong></td>
                                    <td>' . str_pad($shipper->id, 6, '0', STR_PAD_LEFT) . '</td>
                                </tr>';
            if ($account_type_id == 2) {
                $html .= '<tr>
                                        <td class="color secondary"><strong>Shipper Name</strong></td>
                                        <td>' . $shipper->name . '</td>
                                    </tr>';
            }
            $html .= '<tr>
                                    <td class="color secondary"><strong>Name</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_person_name : $shipper->name) . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Address</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_address : $shipper->address) . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Contact No.</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_person_phone : $shipper->phone) . '</td>
                                </tr>
                                <tr>
                                  <td class="color secondary"><strong>NTN</strong></td>
                                  <td>' . $shipper->ntn_no . '</td>
                                </tr>
                                <tr>
                                  <td class="color secondary"><strong>STRN</strong></td>
                                  <td>' . $shipper->strn_no . '</td>
                                </tr>
                               </tbody>
                            </table>
                        </div>

                        <div class="col-4">
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                    <td class="color primary"><strong>NTN</strong></td>
                                    <td>7930679-5</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>SNTN</strong></td>
                                    <td>S-7930679-5</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>PNTN</strong></td>
                                    <td>P-7930679-5</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>Billing Period</strong></td>
                                    <td>' . Carbon::parse($invoice->from_date)->format('Y-m-d') . ' <-> ' . Carbon::parse($invoice->to_date)->format('Y-m-d') . '</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>Invoice No.</strong></td>
                                    <td>' . $invoice->invoice_number . ' - ' . $invoice_number_serial_number . '</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>Invoice Date</strong></td>
                                    <td>' . Carbon::parse($invoice->invoicing_date)->format('Y-m-d') . '</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>Due Date</strong></td>
                                    <td>' . Carbon::parse($invoice->due_date)->format('Y-m-d') . '</td>
                                </tr>
                               </tbody>
                            </table>
                        </div>
                    </div>
            ';

            $html .= '
                    <table class="table table-sm table-bordered border">
                      <thead>
                        <tr>
                            <th colspan="12" class="color primary text-center">Invoice Summary</th>
                        </tr>
                        <tr>
                            <th class="color secondary">Origin</th>
                            <th class="color secondary">Weight Charges (PKR)</th>
                            <th class="color secondary">Cash Handling Charges (PKR)</th>
                            <th class="color secondary">Insurance Charges (PKR)</th>
                            <th class="color secondary">Replacement Charges (PKR)</th>
                            <th class="color secondary">Try & Buy Charges (PKR)</th>
                            <th class="color secondary">Return Charges (PKR)</th>
                            <th class="color secondary">Fuel Surcharge (PKR)</th>
                            <th class="color secondary">Intercept Charges (PKR)</th>
                            <th class="color secondary">OSA Charges (PKR)</th>
                            <th class="color secondary">Adjustment Charges (PKR)</th>
                        </tr>
                      </thead>
                      <tbody>
            ';

            foreach ($origins as $origin) {
                $html .= '
                        <tr>
                            <td>' . $origin . '</td>
                            <td>' . number_format($total_weight_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_cash_handling_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_insurance_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_replacement_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_try_and_buy_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_return_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_fuel_surcharge[$origin], 2) . '</td>
                            <td>' . number_format($total_intercept_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_nsa_osa_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_adjustment_charges[$origin], 2) . '</td>
                        </tr>
                ';
            }

            $html .= '
                      </tbody>
                    </table>

                    <div class="row justify-content-end">
                        <div class="col-4">
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                  <td class="color secondary text-left"><strong>Subtotal (PKR)</strong></td>
                                  <td class="text-right">' . number_format($total_charges[$gst], 2) . '</td>
                                </tr>
                                <tr>
                                  <td class="color secondary text-left"><strong>GST (PKR)</strong></td>
                                  <td class="text-right">' . number_format($total_gst[$gst], 2) . '</td>
                                </tr>
                                <tr>
                                  <td class="color secondary text-left"><strong>Packaging Charges (PKR)</strong></td>
                                  <td class="text-right">' . number_format($total_packaging_material_charges[$gst], 2) . '</td>
                                </tr>
                                <tr>
                                  <td class="color primary text-left"><strong>Total Invoice Amount (PKR)</strong></td>
                                  <td class="color secondary text-right">' . number_format(ROUND($total_invoice_amount[$gst], 0, PHP_ROUND_HALF_DOWN)) . '</td>
                                </tr>
                              </tbody>
                            </table>
                        </div>
                    </div>

                    <table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="color primary" style="width: 150px;"><strong>Amount in Words</strong></td>
                          <td class="color secondary">' . self::amount_to_words($total_invoice_amount[$gst]) . ' Only</td>
                        </tr>
                      </tbody>
                    </table>

                    <table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="color primary text-center" colspan="2"><strong>Bank Account Details</strong></td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Benificiary Name</strong></td>
                          <td>Trax Online Private Limited</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Bank</strong></td>
                          <td>Meezan Bank</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Account No.</strong></td>
                          <td>0102951143</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Branch No.</strong></td>
                          <td>9912</td>
                        </tr>
                      </tbody>
                    </table>

                    <div class="mb-1 text-center font-italic"><strong>Disclaimer:</strong> This is a system generated invoice. No signature required.</div>
            ';

            foreach ($origins as $origin) {
                $html .= '
                    <table class="table table-sm table-bordered border shipments_summary">
                      <thead>
                        <tr>
                            <th class="color primary text-center" colspan="15">Shipment(s) Summary - ' . $origin . '</th>
                        </tr>
                        <tr>
                          <th class="color secondary">S. No.</th>
                          <th class="color secondary">Tracking No.</th>
                          <th class="color secondary">Order ID</th>
                          <th class="color secondary">Destination</th>
                          <th class="color secondary">Shipping Mode</th>
                          <th class="color secondary">Arrival Date</th>
                          <th class="color secondary">Weight (kg)</th>
                          <th class="color secondary">Weight Charges (PKR)</th>
                          <th class="color secondary">Fuel Surcharge (PKR)</th>
                          <th class="color secondary">OSA Charges (PKR)</th>
                          <th class="color secondary">Adjustment Charges (PKR)</th>
                          <th class="color secondary">Packaging Charges (PKR)</th>
                          <th class="color secondary">Total Charges (PKR)</th>
                          <th class="color secondary">GST (PKR)</th>
                          <th class="color secondary">Invoice Amount (PKR)</th>
                        </tr>
                    </thead>
                    <tbody>
                ';

                $html .= $shipment_details[$origin];

                $html .= '
                      </tbody>
                    </table>
                ';
            }

            $invoice_number_serial_number++;
        }

        $html .= '
                  </div>
                </div>
        ';

        if (!$email) {
            $html .= '
                <script>
                  window.onload = function() {

                    window.print();
                  }
                </script>
              </body>
            </html>
            ';
        }

        return $html;
    }

    public function invoices_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 34);
        $company_banks = BanksList::where('affiliate', 1)->get();
        $invoice_statuses = InvoiceStatus::get();
        $adjustment_reasons = InvoiceAdjustmentReasons::all();
        return view('admin.finance.invoices')->with(['company_banks' => $company_banks, 'invoice_statuses' => $invoice_statuses,'adjustment_reasons' => $adjustment_reasons]);
    }

    public function reimbursement_invoices_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 472);
        return view('admin.finance.reimbursement_invoices');
    }

    public function received_invoices_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 35);
        $company_banks = BanksList::where('affiliate', 1)->get();
        $invoice_statuses = InvoiceStatus::where('id', 3)->get();

        return view('admin.finance.received_invoice')->with(['company_banks' => $company_banks, 'invoice_statuses' => $invoice_statuses]);
    }

    public function invoices_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 94);
        }

        $invoice = Invoice::leftjoin('users as u', 'invoices.user_id', '=', 'u.id')
            ->leftjoin('cities as c', 'u.city_id', '=', 'c.id')
            ->leftjoin('banks_lists as b', 'invoices.company_bank_id', '=', 'b.id')
            ->join('invoice_statuses as is', 'invoices.status_id', '=', 'is.id')
            ->join('user_bank_infos as ubi', 'ubi.user_id', '=', 'u.id')
            ->join('invoicing_cycles as ic', 'ic.id', '=', 'ubi.invoicing_cycle_id')
            ->select('invoices.id as id', 'invoices.invoice_number as invoice_number', 'invoices.invoice_number as invoice_number_btn', 'u.name as shipper', 'c.name as city', 'invoices.total_charges as total_charges', 'invoices.total_gst as total_gst', 'invoices.total_invoice_amount as total_invoice_amount', 'invoices.created_at as created_at', 'invoices.due_date as due_date', 'invoices.received_date as received_date', 'b.name as company_bank', 'invoices.received_amount as received_amount', 'invoices.tax_amount as tax_amount', 'invoices.deposit_date as deposit_date', 'is.name as status', 'invoices.status_id as status_id', 'invoices.invoicing_date as invoicing_date', 'ic.name as invoicing_cycle', 'invoices.invoice_type as invoice_type', DB::raw('NULL as payment_type'), DB::raw('2 as account_type'), 'is.id as is_id','invoices.deposited_amount as deposited_amount','invoices.adjusted_amount as adjusted_amount')
            ->where('ubi.default_bank', 1);

        if (session('department_id') == 7 && !in_array(session('id'), session('sale_users_bypass'))) {
            $invoice->whereIn('invoices.user_id', session('tagged_shippers'));
        }

        $reim_invoice = InvoiceForReimbursement::join('users as u', 'invoice_for_reimbursements.user_id', '=', 'u.id')
            ->join('cities as c', 'u.city_id', '=', 'c.id')
            ->select('invoice_for_reimbursements.id as id', 'invoice_for_reimbursements.invoice_number as invoice_number', 'invoice_for_reimbursements.invoice_number as invoice_number_btn', 'u.name as shipper', 'c.name as city', 'invoice_for_reimbursements.total_charges as total_charges', 'invoice_for_reimbursements.total_gst as total_gst', 'invoice_for_reimbursements.total_invoice_amount as total_invoice_amount', 'invoice_for_reimbursements.created_at as created_at', DB::raw('NULL as due_date'), DB::raw('NULL as received_date'), DB::raw('NULL as company_bank'), DB::raw('NULL as received_amount'), DB::raw('NULL as tax_amount'), DB::raw('NULL as deposit_date'), DB::raw('NULL as status'), DB::raw('NULL as status_id'), 'invoice_for_reimbursements.invoicing_date as invoicing_date', DB::raw('NULL as invoicing_cycle'), DB::raw('NULL as invoice_type'), 'invoice_for_reimbursements.payment_type as payment_type', DB::raw('1 as account_type'), DB::raw('NULL as is_id'), DB::raw('NULL as deposited_amount'),DB::raw('NULL as adjusted_amount'))
            ->where('invoice_for_reimbursements.to_show', 1);

        if (session('department_id') == 7 && !in_array(session('id'), session('sale_users_bypass'))) {
            $reim_invoice->whereIn('invoice_for_reimbursements.user_id', session('tagged_shippers'));
        }

        $invoices = DB::query()->fromSub($reim_invoice->union($invoice), 'invoices');

        $datatables = Datatables::of($invoices)
            ->addColumn('account', function ($invoice) {
                if ($invoice->account_type == 2) {
                    return 'Corporate Account';
                } else {
                    return 'Reimbursement Account';
                }
            })
            ->addColumn('balance_amount', function ($invoice) {
                if ($invoice->account_type == 2 && $invoice->is_id != 3) {
                    return $invoice->total_invoice_amount - ($invoice->adjusted_amount + $invoice->deposited_amount);
                }
                else{
                    return '-';
                }
            })
            ->editColumn('invoice_type', function ($invoice) {
                if ($invoice->account_type == 2) {
                    if ($invoice->invoice_type == 1) {
                        return 'Courier Invoice';
                    } else {
                        return 'Packaging Invoice';
                    }
                } else {
                    return '-';
                }
            })
            ->editColumn('invoice_number_btn', function ($invoice) {
                return '<button class="btn btn-sm btn-outline-info align-middle ">' . $invoice->invoice_number_btn . '</button>';
            })
            ->editColumn('payment_type', function ($invoice) {
                if ($invoice->account_type == 1) {
                    if ($invoice->payment_type == 1) {
                        return "Done";
                    } else {
                        return "Make";
                    }
                } else {
                    return '';
                }
            })
            ->editColumn('total_gst', function ($invoice) {
                return number_format($invoice->total_gst, 2);
            })
            ->editColumn('total_invoice_amount', function ($invoice) {
                return number_format(ROUND($invoice->total_invoice_amount, 0, PHP_ROUND_HALF_DOWN));
            })
            ->editColumn('created_at', function ($invoice) {
                return Carbon::parse($invoice->created_at)->format('Y-m-d');
            })
            ->editColumn('invoicing_date', function ($invoice) {
                return Carbon::parse($invoice->invoicing_date)->format('Y-m-d');
            })
            ->addColumn('aging', function ($invoice) {
                if ($invoice->status_id == 1 && $invoice->account_type == 2) {
                    $days = Carbon::now()->diffInDays($invoice->created_at);

                    if ($days == 0) {
                        return '-';
                    } else {
                        return $days;
                    }
                } else {
                    return '-';
                }
            })
            ->editColumn('due_date', function ($invoice) {
                return Carbon::parse($invoice->due_date)->format('Y-m-d');
            })
            ->editColumn('received_date', function ($invoice) {
                if ($invoice->received_date) {
                    return Carbon::parse($invoice->received_date)->format('Y-m-d');
                } else {
                    return '';
                }
            })
            ->editColumn('deposit_date', function ($invoice) {
                if ($invoice->deposit_date) {
                    return Carbon::parse($invoice->received_date)->format('Y-m-d');
                } else {
                    return '';
                }
            })
            ->addColumn('deposit_slip', function ($invoice) {
                if ($invoice->account_type == 2) {
                    $invoice_slip_count = InvoiceUploadSlip::where('invoice_id', $invoice->id)->count();
                    if ($invoice_slip_count > 0) {
                        return '<a class="btn btn-sm btn-outline-info align-middle deposit_slip_view" href="#"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">'.$invoice_slip_count .'</span></a>';
                    }
                    return "-";
                } else {
                    return "-";
                }
            })
            ->addColumn('invoice_adjustment', function ($invoice) {
                if ($invoice->account_type == 2) {
                    $invoice_adjustment_count = InvoiceAdjustment::where('invoice_id', $invoice->id)->count();
                    if ($invoice_adjustment_count > 0) {
                        return '<a class="btn btn-sm btn-outline-info align-middle adjustment_view" href="#"><span class="align-middle">'.  $invoice_adjustment_count .'</span></a>';
                    }
                    return "-";
                } else {
                    return "-";
                }
            })
            ->addColumn('overdue_by', function ($invoice) {
                if ($invoice->status_id == 1 && $invoice->account_type == 2) {
                    $days = Carbon::now()->diffInDays($invoice->due_date);

                    if ($days == 0) {
                        return '-';
                    } else {
                        return $days;
                    }
                } else {
                    return '-';
                }
            })
            ->filterColumn('invoice_type', function ($query, $keyword) {
                if ($keyword == 1) {
                    $query->where('invoices.invoice_type', 1);
                } else if ($keyword == 2) {
                    $query->where('invoices.invoice_type', 2);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('account_type', function ($query, $keyword) {
                $query->where('invoices.account_type', $keyword);
            })
            ->filterColumn('payment_type', function ($query, $keyword) {

                $keyword = strtolower($keyword);
                if($keyword == 'done'){
                    $query->where('invoices.payment_type', 1);
                }
                else if($keyword == 'make'){
                    $query->where('invoices.payment_type', 0);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn('action', function ($invoice) {
                $export_to_excel_button = '<button type="button" class="dropdown-item export_to_excel"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-download"></i></div><div class="col-9 offset-1">Export to Excel</div></button>';
                $email_reminder_button = '<button type="button" class="dropdown-item email_reminder"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Email Reminder</div></button>';
                $mark_as_received_button = '<button type="button" class="dropdown-item mark_as_received"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Mark as Received</div></button>';
                $origin_wise_print_button = '<button type="button" class="dropdown-item print_origin_wise"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-printer"></i></div><div class="col-9 offset-1">Origin Wise Print</div></button>';
                $gst_wise_print_button = '<button type="button" class="dropdown-item print_gst_wise"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-printer"></i></div><div class="col-9 offset-1">GST Wise Print</div></button>';

                $upload_deposit_slip_button = '<button type="button" class="dropdown-item" data-target-id="' . $invoice->id . '" data-target="#uploadDepositSlip" data-toggle="modal"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-alert-octagon"></i></div><div class="col-9 offset-1">Upload Deposit Slip</div></button>';
                $detailed_print_button = '<button type="button" class="dropdown-item detail_print"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-printer"></i></div><div class="col-9 offset-1">Detailed Print</div></button>';
                $add_adjustment = '<button type="button" class="dropdown-item add_adjustment"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Add Adjustment</div></button>';

                $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

                $dropdown .= $export_to_excel_button;

                if ((session('role_id') == 1 || in_array(121, session('permissions'))) && $invoice->status_id == 1 && Notification::find(28)->status) {
                    $dropdown .= $email_reminder_button;
                }

                if ((session('role_id') == 1 || in_array(821, session('permissions'))) && $invoice->status_id != 3 && $invoice->account_type == 2 && ($invoice->total_invoice_amount - ($invoice->adjusted_amount + $invoice->deposited_amount)) > 0) {
                    $dropdown .= $add_adjustment;
                }

//                if ((session('role_id') == 1 || in_array(122, session('permissions'))) && $invoice->status_id != 3) {
//                    $dropdown .= $mark_as_received_button;
//                }

                $dropdown .= $origin_wise_print_button;
                $dropdown .= $gst_wise_print_button;
                $dropdown .= $detailed_print_button;

                if ((session('role_id') == 1 || in_array(589, session('permissions'))) && $invoice->account_type == 2 && $invoice->status_id != 3 &&  ($invoice->total_invoice_amount - ($invoice->adjusted_amount + $invoice->deposited_amount)) >= 0) {
                    $dropdown .= $upload_deposit_slip_button;
                }
                $dropdown .= '
                </div>
              </div>
            ';

                return $dropdown;
            });

        if ($request->get('invoice_from') && $request->get('invoice_to')) {
            $from = date('Y-m-d 00:00:00', strtotime($request->get('invoice_from')));
            $to = date('Y-m-d 23:59:59', strtotime($request->get('invoice_to')));
            $datatables->whereBetween('invoices.invoicing_date', [$from, $to]);

        }

        if ($request->get('generation_from') && $request->get('generation_to')) {
            $from = date('Y-m-d 00:00:00', strtotime($request->get('generation_from')));
            $to = date('Y-m-d 23:59:59', strtotime($request->get('generation_to')));
            $datatables->whereBetween('invoices.created_at', [$from, $to]);

        }
        
        return $datatables->make(true);
    }


    public function reimbursement_invoices_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 473);
        }

        $invoices = InvoiceForReimbursement::leftjoin('users as u', 'invoice_for_reimbursements.user_id', '=', 'u.id')
            ->leftjoin('cities as c', 'u.city_id', '=', 'c.id')
            ->select('invoice_for_reimbursements.id', 'invoice_for_reimbursements.invoice_number', 'u.name as shipper', 'c.name as city', 'invoice_for_reimbursements.total_charges', 'invoice_for_reimbursements.total_gst', 'invoice_for_reimbursements.total_invoice_amount', 'invoice_for_reimbursements.created_at', 'invoice_for_reimbursements.invoicing_date', 'invoice_for_reimbursements.payment_type')
            ->where('invoice_for_reimbursements.to_show', 1);
        if (session('department_id') == 7) {
            $invoices->whereIn('invoice_for_reimbursements.user_id', session('tagged_shippers'));
        }
        $datatables = Datatables::of($invoices)
            ->addColumn('invoice_number_button', function ($invoice) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $invoice->invoice_number . '</button>';
            })
            ->editColumn('total_charges', function ($invoice) {
                return number_format($invoice->total_charges, 2);
            })
            ->editColumn('payment_type', function ($invoice) {
                if ($invoice->payment_type == 1) {
                    return "Done";
                } else {
                    return "Make";
                }
            })
            ->editColumn('total_gst', function ($invoice) {
                return number_format($invoice->total_gst, 2);
            })
            ->editColumn('total_invoice_amount', function ($invoice) {
                return number_format(ROUND($invoice->total_invoice_amount, 0, PHP_ROUND_HALF_DOWN));
            })
            ->editColumn('created_at', function ($invoice) {
                return Carbon::parse($invoice->created_at)->format('Y-m-d');
            })
            ->editColumn('invoicing_date', function ($invoice) {
                return Carbon::parse($invoice->invoicing_date)->format('Y-m-d') ?? "-";
            })
            ->addColumn('invoicing_cycle', function ($invoice) {
                return "Monthly";
            })
            ->addColumn('action', function ($invoice) {
                $export_to_excel_button = '<button type="button" class="dropdown-item export_to_excel"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-download"></i></div><div class="col-9 offset-1">Export to Excel</div></button>';
                $origin_wise_print_button = '<button type="button" class="dropdown-item print_origin_wise"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-printer"></i></div><div class="col-9 offset-1">Origin Wise Print</div></button>';
                $gst_wise_print_button = '<button type="button" class="dropdown-item print_gst_wise"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-printer"></i></div><div class="col-9 offset-1">GST Wise Print</div></button>';

                $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

                $dropdown .= $export_to_excel_button;
                $dropdown .= $origin_wise_print_button;
                $dropdown .= $gst_wise_print_button;
                $dropdown .= '
                </div>
              </div>
            ';

                return $dropdown;
            });

        return $datatables->make(true);
    }

    public function invoices_slip(Request $request)
    {   //dd($request->all());
        $request->validate([
            'deposit_slip.*' => 'mimes:jpg,jpeg,png',
        ]);

        $invoice = Invoice::find($request->invoice_id);
        $check_amount = $invoice->deposited_amount + $request->total_amount + $invoice->adjusted_amount;
        if($check_amount > $invoice->total_invoice_amount){
            return redirect()->back()->with('error','The amount you entered is exceeding the balance amount');
        }

        $deposited_amount = 0;

        $files = $request->file('deposit_slip');
        foreach ($request->date as $row => $date) {
            $deposit_details = new InvoiceUploadSlip();
            $deposit_details->invoice_id = $request->invoice_id;
            $deposit_details->deposit_date = $request->date[$row];
            $deposit_details->bank_id = $request->bank[$row];
            $deposit_details->amount = $request->amount[$row];
            $image = $files[$row];
            $extension = 'png';
            $random = rand(1000, 100000);
            $now = Carbon::now();
            $time = $now->year . '_' . $now->month;
            $slip = $time . $random . Auth::id() . '.' . $extension;
            $image->move(public_path('uploads/invoices'), $slip);
            $deposit_details->added_by = Auth::id();
            $deposit_details->image = $slip;
            $deposit_details->save();

            $deposited_amount += $request->amount[$row];
        }

        if($deposited_amount > 0){
            $invoice->deposited_amount += $deposited_amount;
            if ($deposited_amount != $invoice->total_invoice_amount) {
                $invoice->status_id = 4;
            }
            $invoice->save();
        }

        return redirect()->back()->with(['success' => 'Deposit Slips uploaded successfully!']);
    }

    public function invoices_slip_view(Request $request)
    {
        $invoice_id = $request->invoice_id;
        if ($invoice_id) {
            $slips = InvoiceUploadSlip::where('invoice_id', $invoice_id)->get();
            if (count($slips) > 0) {
                $sorted_array = array();
                $now = Carbon::now();
                foreach ($slips as $slip) {
                    $sorted_array[$slip->id]['date'] = Carbon::parse($slip->deposit_date)->toDateString();
                    $sorted_array[$slip->id]['bank'] = BanksList::find($slip->bank_id)->name;
                    $sorted_array[$slip->id]['amount'] = $slip->amount;
                    $img_url = 'uploads/invoices/' . $slip->image;
                    if (file_exists($img_url)) {
                        $sorted_array[$slip->id]['image'] = '<a class="btn btn-sm btn-outline-info align-middle" href="' . asset('uploads/invoices/' . $slip->image) . '" target="_blank"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';
                    } else {
                        $sorted_array[$slip->id]['image'] = "-";
                    }
                }

                return ['status' => 0, 'slips' => $sorted_array];
            } else {
                return ['status' => 1, 'error' => 'No invoice slips found!'];
            }
        } else {
            return ['status' => 1, 'error' => 'No Invoice Selected!'];

        }
    }


    public function received_invoices_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 95);
        }
        $invoices = Invoice::join('users as u', 'invoices.user_id', '=', 'u.id')
            ->join('cities as c', 'u.city_id', '=', 'c.id')
            ->leftjoin('banks_lists as b', 'invoices.company_bank_id', '=', 'b.id')
            ->join('invoice_statuses as is', 'invoices.status_id', '=', 'is.id')
            ->join('user_bank_infos as ubi', 'ubi.user_id', '=', 'u.id')
            ->join('invoicing_cycles as ic', 'ic.id', '=', 'ubi.invoicing_cycle_id')
            ->select('invoices.id', 'invoices.invoice_number', 'u.name as shipper', 'c.name as city', 'invoices.total_charges', 'invoices.total_gst', 'invoices.total_invoice_amount', 'invoices.created_at', 'invoices.due_date', 'invoices.received_date', 'b.name as company_bank', 'invoices.received_amount', 'invoices.tax_amount', 'invoices.deposit_date', 'is.name as status', 'invoices.status_id', 'invoices.invoicing_date', 'ic.name as invoicing_cycle')->where('is.id', 3)->where('ubi.default_bank', 1);
        if (session('department_id') == 7 && !in_array(session('id'), session('sale_users_bypass'))) {
            $invoices->whereIn('invoices.user_id', session('tagged_shippers'));
        }
        $datatables = Datatables::of($invoices)
            ->addColumn('invoice_number_button', function ($invoice) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $invoice->invoice_number . '</button>';
            })
            ->editColumn('total_charges', function ($invoice) {
                return number_format($invoice->total_charges, 2);
            })
            ->editColumn('total_gst', function ($invoice) {
                return number_format($invoice->total_gst, 2);
            })
            ->editColumn('total_invoice_amount', function ($invoice) {
                return number_format(ROUND($invoice->total_invoice_amount, 0, PHP_ROUND_HALF_DOWN));
            })
            ->editColumn('created_at', function ($invoice) {
                return Carbon::parse($invoice->created_at)->format('Y-m-d');
            })
            ->editColumn('invoicing_date', function ($invoice) {
                return Carbon::parse($invoice->invoicing_date)->format('Y-m-d');
            })
            ->addColumn('aging', function ($invoice) {
                if ($invoice->status_id == 1) {
                    $days = Carbon::now()->diffInDays($invoice->created_at);

                    if ($days == 0) {
                        return '-';
                    } else {
                        return $days;
                    }
                } else {
                    return '-';
                }
            })
            ->editColumn('due_date', function ($invoice) {
                return Carbon::parse($invoice->due_date)->format('Y-m-d');
            })
            ->editColumn('received_date', function ($invoice) {
                if ($invoice->received_date) {
                    return Carbon::parse($invoice->received_date)->format('Y-m-d');
                } else {
                    return '';
                }
            })
            ->editColumn('deposit_date', function ($invoice) {
                if ($invoice->deposit_date) {
                    return Carbon::parse($invoice->received_date)->format('Y-m-d');
                } else {
                    return '';
                }
            })
            ->editColumn('due_date', function ($invoice) {
                return Carbon::parse($invoice->due_date)->format('Y-m-d');
            })
            ->addColumn('overdue_by', function ($invoice) {
                if ($invoice->status_id == 1) {
                    $days = Carbon::now()->diffInDays($invoice->due_date);

                    if ($days == 0) {
                        return '-';
                    } else {
                        return $days;
                    }
                } else {
                    return '-';
                }
            })
            ->addColumn('upload_slip', function ($invoice) {
                $invoice_slip_count = InvoiceUploadSlip::where('invoice_id', $invoice->id)->count();
                if ($invoice_slip_count > 0) {
                    return '<a class="btn btn-sm btn-outline-info align-middle deposit_slip_view" href="#"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';
                }

                return "-";
            })
            ->addColumn('action', function ($invoice) {
                $export_to_excel_button = '<button type="button" class="dropdown-item export_to_excel"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-download"></i></div><div class="col-9 offset-1">Export to Excel</div></button>';
                $email_reminder_button = '<button type="button" class="dropdown-item email_reminder"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Email Reminder</div></button>';
                $mark_as_received_button = '<button type="button" class="dropdown-item mark_as_received"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Mark as Received</div></button>';
                $origin_wise_print_button = '<button type="button" class="dropdown-item print_origin_wise"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-printer"></i></div><div class="col-9 offset-1">Origin Wise Print</div></button>';


                $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

                $dropdown .= $export_to_excel_button;

                if ((session('role_id') == 1 || in_array(121, session('permissions'))) && $invoice->status_id == 1 && Notification::find(28)->status) {
                    $dropdown .= $email_reminder_button;
                }

                if ((session('role_id') == 1 || in_array(122, session('permissions'))) && $invoice->status_id != 3) {
                    $dropdown .= $mark_as_received_button;
                }

                $dropdown .= $origin_wise_print_button;

                $dropdown .= '
                </div>
              </div>
            ';

                return $dropdown;
            });

        return $datatables->make(true);
    }

    public function invoices_detail_print(Request $request)
    {

        $invoice = Invoice::find($request->id);

        if ($invoice) {
            if ($request->has('header')) {
                return self::generate_invoice_print($invoice->id, FALSE, TRUE);
            } else {
                return self::generate_invoice_print($invoice->id);
            }
        } else {
            return '';
        }
    }

    public function reimbursement_detail_invoices_print(Request $request)
    {
        $invoice = InvoiceForReimbursement::find($request->id);

        if ($invoice) {
            if ($request->has('header')) {
                return self::generate_reimbursement_invoice_print($invoice->id, FALSE, TRUE);
            } else {
                return self::generate_reimbursement_invoice_print($invoice->id);
            }
        } else {
            return '';
        }
    }

    public function invoices_print_origin_wise(Request $request)
    {
        if ($account_type = $request->get('account_type')) {
            if ($account_type == 1) {
                $invoice = InvoiceForReimbursement::find($request->id);

                if ($invoice) {
                    return self::generate_reimbursement_invoice_print_origin_wise($invoice->id);
                } else {
                    return '';
                }
            } else if ($account_type == 2) {
                $invoice = Invoice::find($request->id);

                if ($invoice) {
                    return self::generate_invoice_print_origin_wise($invoice->id);
                } else {
                    return '';
                }
            } else {
                return "";
            }
        } else {
            return "";
        }

    }

    public function reimbursement_invoices_print_origin_wise(Request $request)
    {
        $invoice = InvoiceForReimbursement::find($request->id);

        if ($invoice) {
            return self::generate_reimbursement_invoice_print_origin_wise($invoice->id);
        } else {
            return '';
        }
    }

    public function invoices_print_gst_wise(Request $request)
    {
        if ($account_type = $request->get('account_type')) {
            if ($account_type == 1) {
                $invoice = InvoiceForReimbursement::find($request->id);

                if ($invoice) {
                    return self::generate_reimbursement_invoice_print_gst_wise($invoice->id);
                } else {
                    return '';
                }
            } else if ($account_type == 2) {
                $invoice = Invoice::find($request->id);

                if ($invoice) {
                    return self::generate_invoice_print_gst_wise($invoice->id);
                } else {
                    return '';
                }
            } else {
                return "";
            }
        } else {
            return "";
        }
    }

    public function reimbursement_invoices_print_gst_wise(Request $request)
    {
        $invoice = InvoiceForReimbursement::find($request->id);

        if ($invoice) {
            return self::generate_reimbursement_invoice_print_gst_wise($invoice->id);
        } else {
            return '';
        }
    }

    public function invoices_export_to_excel(Request $request)
    {
        $invoice = Invoice::find($request->id);

        $filename = 'sonic_invoice_details_' . $request->id . '.xlsx';

        $details = array();

        $details[] = ['S. No.', 'Tracking No.', 'Origin', 'Destination', 'Arrival Date', 'Weight (kg)', 'Weight Charges (PKR)', 'Fuel Surcharge (PKR)', 'OSA Charges (PKR)', 'Adjustment Charges (PKR)', 'Total Charges (PKR)', 'GST (PKR)', 'Invoice Amount (PKR)', 'Intercept Charges  (PKR)'];

        $serial_number = 1;

        foreach ($invoice->invoice_shipments as $invoice_shipment) {
            $shipment = $invoice_shipment->shipment;

            $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 2);

            if ($shipment_journey->exists()) {
                $date = $shipment_journey->first()->created_at;
            } else {
                $date = $shipment->created_at;
            }

            $date = Carbon::parse($date)->format('Y-m-d');

            $row = array();

            $row[] = $serial_number;
            $row[] = $shipment->tracking_number;
            $row[] = $shipment->pickup_address->city->name;
            $row[] = $shipment->consignee_city->name;
            $row[] = $shipment->created_at;
            $row[] = $shipment->actual_weight;
            $row[] = (($invoice_shipment->type != 2) ? $shipment->weight_charges : 0);
            $row[] = (($invoice_shipment->type != 2) ? $shipment->fuel_surcharge : 0);
            $row[] = (($invoice_shipment->type != 2) ? $shipment->nsa_osa_charges : 0);
            $row[] = (($invoice_shipment->type == 2) ? $shipment->adjustment_charges : 0);
            $row[] = $invoice_shipment->charges;
            $row[] = $invoice_shipment->gst;
            $row[] = $invoice_shipment->invoice_amount;
            $row[] = $shipment->intercept_charges;

            $details[] = $row;

            $serial_number++;
        }

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->getStyle('B')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('G')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('H')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('I')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('J')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('K')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('L')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('M')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('N')->getNumberFormat()->setFormatCode('#,##0.00');

        $spreadsheet->getActiveSheet()->fromArray($details);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function reimbursement_invoices_export_to_excel(Request $request)
    {
        $invoice = InvoiceForReimbursement::find($request->id);

        $filename = 'sonic_invoice_details_' . $request->id . '.xlsx';

        $details = array();

        $details[] = ['S. No.', 'Tracking No.', 'Origin', 'Destination', 'Arrival Date', 'Weight (kg)', 'Weight Charges (PKR)', 'Fuel Surcharge (PKR)', 'OSA Charges (PKR)', 'Adjustment Charges (PKR)', 'Total Charges (PKR)', 'GST (PKR)', 'Invoice Amount (PKR)','Intercept Charges (PKR)'];

        $serial_number = 1;

        foreach ($invoice->invoice_shipments as $invoice_shipment) {
            $shipment = $invoice_shipment->shipment;

            $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 2);

            if ($shipment_journey->exists()) {
                $date = $shipment_journey->first()->created_at;
            } else {
                $date = $shipment->created_at;
            }

            $date = Carbon::parse($date)->format('Y-m-d');

            $row = array();

            $row[] = $serial_number;
            $row[] = $shipment->tracking_number;
            $row[] = $shipment->pickup_address->city->name;
            $row[] = $shipment->consignee_city->name;
            $row[] = $shipment->created_at;
            $row[] = $shipment->actual_weight;
            $row[] = (($invoice_shipment->type != 2) ? $shipment->weight_charges : 0);
            $row[] = (($invoice_shipment->type != 2) ? $shipment->fuel_surcharge : 0);
            $row[] = (($invoice_shipment->type != 2) ? $shipment->nsa_osa_charges : 0);
            $row[] = (($invoice_shipment->type == 2) ? $shipment->adjustment_charges : 0);
            $row[] = $invoice_shipment->charges;
            $row[] = $invoice_shipment->gst;
            $row[] = $invoice_shipment->invoice_amount;
            $row[] = $shipment->intercept_charges;

            $details[] = $row;

            $serial_number++;
        }

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->getStyle('B')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('G')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('H')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('I')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('J')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('K')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('L')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('M')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('N')->getNumberFormat()->setFormatCode('#,##0.00');

        $spreadsheet->getActiveSheet()->fromArray($details);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function invoices_email_reminder(Request $request)
    {
        $invoice = Invoice::find($request->id);

        if ($invoice) {
            $invoice->status_id = 2;

            $invoice->save();

            NotificationsController::send(28, $request->id);

            return ['status' => 0, 'success' => 'Reminder has been Sent'];
        } else {
            return ['status' => 1, 'error' => 'No such Invoice'];
        }
    }

    public function invoices_mark_as_received(Request $request)
    {
        $invoice = Invoice::find($request->id);

        if ($invoice) {
            $invoice->received_date = Carbon::now()->format('Y-m-d 00:00:00');
            $invoice->company_bank_id = $request->company_bank;
            $invoice->received_amount = $request->received_amount;
            $invoice->tax_amount = $request->tax_amount;
            $invoice->deposit_date = $request->deposit_date_formatted;
            $invoice->status_id = 3;

            $invoice->save();
            $this->international_credit_limit_reset($invoice->user_id);
        }

        return redirect()->route('admin.finance.invoices.index')->with('success', 'Invoice has been marked as Received');
    }

    public function invoices_mark_as_received_all(Request $request)
    {

        foreach ($request->id as $id) {
            $invoice = Invoice::find($id);

            if ($invoice) {
                $invoice->received_date = Carbon::now()->format('Y-m-d 00:00:00');
                $invoice->company_bank_id = 29;
                $invoice->received_amount = $invoice->total_invoice_amount;
                $invoice->tax_amount = null;
                $invoice->deposit_date = Carbon::now()->format('Y-m-d 00:00:00');
                $invoice->status_id = 3;

                $invoice->save();
                $this->international_credit_limit_reset($invoice->user_id);
            }
        }

        return 1;
    }

    public function invoice_for_reimbursement_index(Request $request)
    {
        $payment_types = [['id' => 0, 'name' => 'Make'], ['id' => 1, 'name' => 'Done']];

        $shippers = User::select('id', 'name')->get();

        return view('admin.finance.invoice_for_reimbursement')->with(['payment_types' => $payment_types, 'shippers' => $shippers]);
    }

    public function invoice_for_reimbursement_generate(Request $request)
    {
        $payment_type = $request->input('payment_type');
        $shipper = $request->input('shipper');
        $from_date = $request->input('from');
        $to_date = $request->input('to');

        if ($payment_type == 0) {
            $payments = PendingPayment::where('user_id', $shipper);
        } else {
            $payments = DonePayment::where('user_id', $shipper);
        }

        $payments = $payments->whereBetween('created_at', [$from_date, $to_date]);

        if ($payments->exists()) {
            $payments = $payments->get();

            $shipper = User::find($shipper);

            $shipper_bank = $shipper->bank()->where('default_bank', 1)->first();

            $account_type_id = $shipper->account_type_id;

            $html = '';

            $html .= '
            <!doctype html>
            <html lang="en">
              <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                <title>Invoice</title>
            ';

            $html .= '
                <style>' . file_get_contents(public_path('app-assets/css/bootstrap.min.css')) . '</style>
            ';

            $html .= '
                <style>@page{size:A4 portrait; margin-top: 10rem; margin-bottom: 8rem;}*{-webkit-print-color-adjust:exact!important;color-adjust:exact!important}body{background:none!important;color:#09262e!important;font-size:0.9rem!important}hr{border-top:1px dashed #000}table.table-bordered{page-break-inside:avoid}table.table-bordered thead tr th, table.table-bordered tbody tr td{border:1px solid #09262e!important}.color.primary{background:#c8c8c8!important}.color.secondary{background:#ebebeb!important}.border{border:1px solid #09262e!important}.summary{page-break-inside:avoid}.shipments_summary{page-break-before:always}</style>
            ';

            $html .= '
              </head>
              <body>
            ';

            $invoice_for_reimbursement = InvoiceForReimbursement::where('user_id', $shipper->id)->where('payment_type', $payment_type)->where('from_date', $from_date)->where('to_date', $to_date);

            if ($invoice_for_reimbursement->exists()) {
                $invoice_for_reimbursement = $invoice_for_reimbursement->first();

                $invoice_number = $invoice_for_reimbursement->invoice_number;
            } else {
                $invoice_for_reimbursement = new InvoiceForReimbursement();

                $invoice_for_reimbursement->user_id = $shipper->id;
                $invoice_for_reimbursement->payment_type = $payment_type;
                $invoice_for_reimbursement->from_date = $from_date;
                $invoice_for_reimbursement->to_date = $to_date;

                $invoice_for_reimbursement->save();

                $invoice_number = $shipper->id . str_pad($invoice_for_reimbursement->id, 6, '0', STR_PAD_LEFT);

                $invoice_for_reimbursement->invoice_number = $invoice_number;

                $invoice_for_reimbursement->save();
            }

            $shipment_details = '';

            $serial_number = 1;

            $total_weight_charges = 0;
            $total_cash_handling_charges = 0;
            $total_insurance_charges = 0;
            $total_return_charges = 0;
            $total_fuel_surcharge = 0;
            $total_replacement_charges = 0;
            $total_try_and_buy_charges = 0;
            $total_packaging_material_charges = 0;
            $total_intercept_charges = 0;
            $total_nsa_osa_charges = 0;
            $total_adjustment_charges = 0;
            $total_charges = 0;
            $total_gst = 0;
            $total_invoice_amount = 0;
            $total_ibft_charges = 0;

            foreach ($payments as $payment) {
                if ($payment_type == 0) {
                    $payment_shipments = $payment->pending_payment_shipments;
                } else {
                    $payment_shipments = $payment->done_payment_shipments;

                    $total_ibft_charges += $payment->ibft_charges;
                }

                foreach ($payment_shipments as $invoice_shipment) {
                    $shipment = $invoice_shipment->shipment;

                    $shipment_weight = $shipment->actual_weight;
                    $weight_charges = $shipment->weight_charges;

                    if ($invoice_shipment->type != 2) {
                        $change_shipment_weight_log = ChangeShipmentWeightLog::where('shipment_id', $shipment->id);
                        if ($change_shipment_weight_log->exists()) {
                            $change_shipment_weight_log = $change_shipment_weight_log->first();

                            $invoice_shipment_date = Carbon::parse($invoice_shipment->created_at);
                            $change_shipment_weight_log_date = Carbon::parse($change_shipment_weight_log->created_at);

                            if ($change_shipment_weight_log_date->gt($invoice_shipment_date)) {
                                $shipment_weight = $change_shipment_weight_log->old_weight;
                                $weight_charges = $change_shipment_weight_log->old_charges;
                            }
                        }
                    }

                    if ($invoice_shipment->type != 2 || ($invoice_shipment->type == 2 && $invoice_shipment->payable < 0)) {
                        $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 2);

                        if ($shipment_journey->exists()) {
                            $date = $shipment_journey->first()->created_at;
                        } else {
                            $date = $shipment->created_at;
                        }

                        $date = Carbon::parse($date)->format('Y-m-d');

                        $shipment_details .= '
                                    <tr>
                                      <td>' . $serial_number . '</td>
                                      <td>' . $shipment->tracking_number . '</td>
                                      <td>' . $shipment->pickup_address->city->name . '</td>
                                      <td>' . $shipment->consignee_city->name . '</td>
                                      <td>' . $shipment->shipping_mode->mode . '</td>
                                      <td>' . $date . '</td>
                                      <td>' . $shipment_weight . '</td>
                                      <td>' . (($invoice_shipment->type != 2) ? number_format($weight_charges, 2) : '0') . '</td>
                                      <td>' . (($invoice_shipment->type != 2) ? number_format($shipment->fuel_surcharge, 2) : '0') . '</td>
                                      <td>' . (($invoice_shipment->type != 2) ? number_format($shipment->nsa_osa_charges, 2) : '0') . '</td>
                                      <td>' . (($invoice_shipment->type == 2) ? number_format($invoice_shipment->payable, 2) : '0') . '</td>
                                      <td>' . number_format($invoice_shipment->charges, 2) . '</td>
                                      <td>' . number_format($invoice_shipment->gst, 2) . '</td>
                                      <td>' . (($invoice_shipment->type != 2) ? number_format(($invoice_shipment->charges + $invoice_shipment->gst), 2) : number_format($invoice_shipment->payable, 2)) . '</td>
                                    </tr>
                        ';

                        $serial_number++;

                        if ($invoice_shipment->type != 2) {
                            if ($invoice_shipment->type == 0) {
                                $total_cash_handling_charges += $shipment->cash_handling_charges;
                                $total_replacement_charges += $shipment->replacement_charges;
                                $total_try_and_buy_charges += $shipment->try_and_buy_charges;
                            } else {
                                $total_return_charges += $shipment->return_charges;
                            }

                            $total_weight_charges += $weight_charges;

                            if ($shipment->packaging_material_request) {
                                $total_packaging_material_charges += $shipment->packaging_material_charges;
                            }

                            $total_insurance_charges += $shipment->insurance_charges;
                            $total_fuel_surcharge += $shipment->fuel_surcharge;
                            $total_intercept_charges += $shipment->intercept_charges;
                            $total_nsa_osa_charges += $shipment->nsa_osa_charges;
                        } else {
                            $total_adjustment_charges += $invoice_shipment->payable;

                            $total_invoice_amount += $invoice_shipment->payable;
                        }

                        $total_charges += $invoice_shipment->charges;
                        $total_gst += $invoice_shipment->gst;
                        $total_invoice_amount += ($invoice_shipment->charges + $invoice_shipment->gst);
                    }
                }
            }

            $html .= '
                <div>
                  <div class="p-1">
                    <div class="row align-items-start justify-content-between summary">
                        <div class="col-6">
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                    <td class="color primary" colspan="2"><strong>Customer Details</strong></td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Account No.</strong></td>
                                    <td>' . str_pad($shipper->id, 6, '0', STR_PAD_LEFT) . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Name</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_person_name : $shipper->name) . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Address</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_address : $shipper->address) . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Contact No.</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_person_phone : $shipper->phone) . '</td>
                                </tr>
                                <tr>
                                  <td class="color secondary"><strong>NTN</strong></td>
                                  <td>' . $shipper->ntn_no . '</td>
                                </tr>
                               </tbody>
                            </table>
                        </div>

                        <div class="col-4">
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                    <td class="color primary"><strong>NTN</strong></td>
                                    <td>7930679-5</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>Billing Period</strong></td>
                                    <td>' . Carbon::parse($from_date)->format('Y-m-d') . ' <-> ' . Carbon::parse($to_date)->format('Y-m-d') . '</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>Invoice No.</strong></td>
                                    <td>' . $invoice_number . '</td>
                                </tr>
                               </tbody>
                            </table>
                        </div>
                    </div>
            ';

            $html .= '
                    <table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                            <td class="color primary text-left"><strong>Invoice Summary</strong></td>
                            <td class="color primary text-right" style="width: 20%;"><strong>Amount (PKR)</strong></td>
                        </tr>
                        <tr>
                          <td class="text-left">Weight Charges</td>
                          <td class="text-right">' . number_format($total_weight_charges, 2) . '</td>
                        </tr>
                        <tr>
                          <td class="text-left">Cash Handling Charges</td>
                          <td class="text-right">' . number_format($total_cash_handling_charges, 2) . '</td>
                        </tr>
                        <tr>
                          <td class="text-left">Insurance Charges</td>
                          <td class="text-right">' . number_format($total_insurance_charges, 2) . '</td>
                        </tr>
                        <tr>
                          <td class="text-left">Replacement Charges</td>
                          <td class="text-right">' . number_format($total_replacement_charges, 2) . '</td>
                        </tr>
                        <tr>
                          <td class="text-left">Try & Buy Charges</td>
                          <td class="text-right">' . number_format($total_try_and_buy_charges, 2) . '</td>
                        </tr>
                        <tr>
                          <td class="text-left">Return Charges</td>
                          <td class="text-right">' . number_format($total_return_charges, 2) . '</td>
                        </tr>
                        <tr>
                          <td class="text-left">Fuel Surcharge</td>
                          <td class="text-right">' . number_format($total_fuel_surcharge, 2) . '</td>
                        </tr>
                        <tr>
                          <td class="text-left">Intercept Charges</td>
                          <td class="text-right">' . number_format($total_intercept_charges, 2) . '</td>
                        </tr>
                        <tr>
                          <td class="text-left">OSA Charges</td>
                          <td class="text-right">' . number_format($total_nsa_osa_charges, 2) . '</td>
                        </tr>
                        <tr>
                          <td class="text-left">Packaging Charges</td>
                          <td class="text-right">' . number_format($total_packaging_material_charges, 2) . '</td>
                        </tr>
                      </tbody>
                    </table>

                    <div class="row justify-content-end">
                        <div class="col-4">
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                  <td class="color secondary text-left"><strong>Subtotal (PKR)</strong></td>
                                  <td class="text-right">' . number_format($total_charges, 2) . '</td>
                                </tr>
                                <tr>
                                  <td class="color secondary text-left"><strong>GST (PKR)</strong></td>
                                  <td class="text-right">' . number_format($total_gst, 2) . '</td>
                                </tr>
                                <tr>
                                  <td class="color secondary text-left"><strong>Adjustment Charges (PKR)</strong></td>
                                  <td class="text-right">' . number_format($total_adjustment_charges, 2) . '</td>
                                </tr>
                ';

            if ($payment_type == 0) {
                $html .= '
                                <tr>
                                  <td class="color primary text-left"><strong>Total Invoice Amount (PKR)</strong></td>
                                  <td class="color secondary text-right">' . number_format(ROUND($total_invoice_amount, 0, PHP_ROUND_HALF_DOWN)) . '</td>
                                </tr>
                    ';
            } else {
                $html .= '
                                <tr>
                                  <td class="color secondary text-left"><strong>IBFT Charges (PKR)</strong></td>
                                  <td class="text-right">' . number_format($total_ibft_charges, 2) . '</td>
                                </tr>
                                <tr>
                                  <td class="color primary text-left"><strong>Total Invoice Amount (PKR)</strong></td>
                                  <td class="color secondary text-right">' . number_format(ROUND(($total_invoice_amount - $total_ibft_charges), 0, PHP_ROUND_HALF_DOWN)) . '</td>
                                </tr>
                    ';
            }

            $html .= '
                              </tbody>
                            </table>
                        </div>
                    </div>

                    <table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="color primary" style="width: 150px;"><strong>Amount in Words</strong></td>
                          <td class="color secondary">' . self::amount_to_words($total_invoice_amount) . ' Only</td>
                        </tr>
                      </tbody>
                    </table>

                    <table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="color primary text-center" colspan="2"><strong>Bank Account Details</strong></td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Benificiary Name</strong></td>
                          <td>Trax Online Private Limited</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Bank</strong></td>
                          <td>Meezan Bank</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Account No.</strong></td>
                          <td>0102951143</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Branch No.</strong></td>
                          <td>9912</td>
                        </tr>
                      </tbody>
                    </table>

                    <div class="mb-1 text-center font-italic"><strong>Disclaimer:</strong> This is a system generated invoice. No signature required.</div>

                    <table class="table table-sm table-bordered border shipments_summary">
                      <thead>
                        <tr>
                            <th class="color primary text-center" colspan="15">Shipment(s) Summary</th>
                        </tr>
                        <tr>
                          <th class="color secondary">S. No.</th>
                          <th class="color secondary">Tracking No.</th>
                          <th class="color secondary">Origin</th>
                          <th class="color secondary">Destination</th>
                          <th class="color secondary">Shipping Mode</th>
                          <th class="color secondary">Arrival Date</th>
                          <th class="color secondary">Weight (kg)</th>
                          <th class="color secondary">Weight Charges (PKR)</th>
                          <th class="color secondary">Fuel Surcharge (PKR)</th>
                          <th class="color secondary">OSA Charges (PKR)</th>
                          <th class="color secondary">Adjustment Charges (PKR)</th>
                          <th class="color secondary">Total Charges (PKR)</th>
                          <th class="color secondary">GST (PKR)</th>
                          <th class="color secondary">Invoice Amount (PKR)</th>
                        </tr>
                    </thead>
                    <tbody>
            ';

            $html .= $shipment_details;

            $html .= '
                      </tbody>
                    </table>
                  </div>
                </div>
            ';

            $html .= '
                <script>
                  window.onload = function() {

                    window.print();
                  }
                </script>
              </body>
            </html>
            ';

            return $html;
        } else {
            return '
            <html>
                <body>No Payment(s) for the given Criteria</body>
                <script>
                  window.onload = function() {
                  }
                </script>
            </html>
            ';
        }
    }

    static public function adjustment_logs_add($shipment_id, $adjustment_type, $adjustment_amount, $remarks = NULL, $pending_id, $type, $retail = NULL)
    {
        if ($retail != null) {
            $adjustment_log = new RetailAdjustmentLog();
        } else {
            $adjustment_log = new AdjustmentLog();
        }
        $adjustment_log->shipment_id = $shipment_id;
        $adjustment_log->adjustment_type_id = $adjustment_type;
        $adjustment_log->admin_id = Auth::id();
        $adjustment_log->adjustment_amount = $adjustment_amount;
        $adjustment_log->remarks = $remarks;
        $adjustment_log->pending_id = $pending_id;
        $adjustment_log->type = $type;
        $adjustment_log->save();
    }

    static public function adjustment_logs_done($type, $pending_id, $done_id, $retail = NULL)
    {
        if ($retail != null) {
            $adjustment_log = RetailAdjustmentLog::where('pending_id', $pending_id)->where('type', $type);
        } else {
            $adjustment_log = AdjustmentLog::where('pending_id', $pending_id)->where('type', $type);
        }

        if ($adjustment_log->exists()) {
            $adjustment_log = $adjustment_log->first();
            $adjustment_log->pending_id = NULL;
            $adjustment_log->done_id = $done_id;
            $adjustment_log->save();
        }
    }

    public function revert_request_shipments_check(Request $request)
    {

        $filtered_shipments = array();
        $filtered_dncc = array();
        if (!empty($request->shipment_ids)) {
            foreach ($request->shipment_ids as $index => $shipment_id) {
                if (DeliveryNoteShipment::where('delivery_note_id', $request->delivery_note_ids[$index])->where('shipment_id', $shipment_id)->whereIn('status', [4, 5, 6, 7])->exists()) {
                    $tracking_number = Shipment::find($shipment_id)->tracking_number;
                    $filtered_shipments[$shipment_id] = $tracking_number;
                    $filtered_dncc[$shipment_id] = $request->delivery_note_ids[$index];
                }
            }
            if (count($filtered_shipments) > 0) {
                return response()->json(['status' => 0, 'shipments' => $filtered_shipments, 'delivery_note_ids' => $filtered_dncc]);
            } else {
                return response()->json(['status' => 1, 'error' => 'Revert request can not be requested for these shipments']);
            }
        }
    }

    public function revert_request_submit(Request $request)
    {

        if ($request->revert_shipment_ids != null) {
            $shipment_ids = explode(',', $request->revert_shipment_ids);
            $delivery_note_ids = explode(',', $request->revert_delivery_note_ids);

            foreach ($shipment_ids as $index => $shipment_id) {
                $previous = DeliveryNoteShipment::where('delivery_note_id', $delivery_note_ids[$index])->where('shipment_id', $shipment_id)->first();
                $previous_status = $previous->status;
                DeliveryNoteShipment::where('delivery_note_id', $delivery_note_ids[$index])->where('shipment_id', $shipment_id)->update(['status' => 11]);
                $revert_status_request = new RevertStatusRequest();
                $revert_status_request->shipment_id = $shipment_id;
                $revert_status_request->delivery_note_id = $delivery_note_ids[$index];
                $revert_status_request->remarks = $request->remarks[$shipment_id];
                $file_name = 'upload_image' . $shipment_id;

                $filename = 'revert_request_' . $delivery_note_ids[$index] . '_detail_' . $shipment_id . '.png';

                $file = $request->file($file_name);

                Storage::disk('public')->putFileAs('revert_status_requests', $file, $filename);

                $revert_status_request->image = $filename;
                $revert_status_request->admin_id = Auth::id();
                $revert_status_request->save();


                $revert_status_request_log = new RevertStatusRequestLog();
                $revert_status_request_log->delivery_note_id = $delivery_note_ids[$index];
                $revert_status_request_log->shipment_id = $shipment_id;
                $revert_status_request_log->previous_status = $previous_status;
                $revert_status_request_log->updated_by = Auth::id();
                $revert_status_request_log->save();

            }
            return redirect()->back()->with(['success' => 'Shipments updated to Revert Request Status!']);
        } else {
            return redirect()->back()->with(['errors' => 'Shipments not selected!']);
        }
    }

    static public function add_pending_payment_charges($pending_payment_id, $amount, $charges, $gst, $payable, $wht = 0, $retail = NULL)
    {
        if ($retail != null) {
            $pending_payment_charges = RetailPendingPaymentCalculation::where('retail_pending_payment_id', $pending_payment_id);
            if ($pending_payment_charges->exists()) {
                $pending_payment_charges = $pending_payment_charges->first();
                $pending_payment_charges->amount = $pending_payment_charges->amount + $amount;
                $pending_payment_charges->payable = $pending_payment_charges->payable + $payable;
                $pending_payment_charges->save();
            } else {
                $pending_payment_charges = new RetailPendingPaymentCalculation();
                $pending_payment_charges->retail_pending_payment_id = $pending_payment_id;
                $pending_payment_charges->amount = $amount;
                $pending_payment_charges->payable = $payable;
                $pending_payment_charges->save();
            }
        } else {
            $pending_payment_charges = PendingPaymentCalculation::where('pending_payment_id', $pending_payment_id);
            if ($pending_payment_charges->exists()) {
                $pending_payment_charges = $pending_payment_charges->first();
                $pending_payment_charges->amount = $pending_payment_charges->amount + $amount;
                $pending_payment_charges->charges = $pending_payment_charges->charges + $charges;
                $pending_payment_charges->gst = $pending_payment_charges->gst + $gst;
                $pending_payment_charges->wht = $pending_payment_charges->wht + $wht;
                $pending_payment_charges->payable = $pending_payment_charges->payable + $payable;
                $pending_payment_charges->save();
            } else {
                $pending_payment_charges = new PendingPaymentCalculation();
                $pending_payment_charges->pending_payment_id = $pending_payment_id;
                $pending_payment_charges->amount = $amount;
                $pending_payment_charges->charges = $charges;
                $pending_payment_charges->gst = $gst;
                $pending_payment_charges->wht = $wht;
                $pending_payment_charges->payable = $payable;
                $pending_payment_charges->save();
            }
        }
    }

    static public function add_done_payment_charges($done_payment_id, $amount, $charges, $gst, $payable, $packaging_material_charges, $adjustment_amount, $retail = NULL, $wht = 0)
    {
        if ($retail != null) {
            $done_payment_charges = RetailDonePaymentCalculation::where('retail_done_payment_id', $done_payment_id);
            if ($done_payment_charges->exists()) {
                $done_payment_charges = $done_payment_charges->first();
                $done_payment_charges->amount = $done_payment_charges->amount + $amount;
                $done_payment_charges->payable = $done_payment_charges->payable + $payable;
                $done_payment_charges->adjustment = $done_payment_charges->adjustment + $adjustment_amount;
                $done_payment_charges->save();
            } else {
                $done_payment_charges = new RetailDonePaymentCalculation();
                $done_payment_charges->retail_done_payment_id = $done_payment_id;
                $done_payment_charges->amount = $amount;
                $done_payment_charges->payable = $payable;
                $done_payment_charges->adjustment = $adjustment_amount;
                $done_payment_charges->save();
            }
        } else {
            $done_payment_charges = DonePaymentCalculation::where('done_payment_id', $done_payment_id);
            if ($done_payment_charges->exists()) {
                $done_payment_charges = $done_payment_charges->first();
                $done_payment_charges->amount = $done_payment_charges->amount + $amount;
                $done_payment_charges->charges = $done_payment_charges->charges + $charges;
                $done_payment_charges->gst = $done_payment_charges->gst + $gst;
                $done_payment_charges->wht = $done_payment_charges->wht + $wht;
                $done_payment_charges->payable = $done_payment_charges->payable + $payable;
                $done_payment_charges->packaging_charges = $done_payment_charges->packaging_charges + $packaging_material_charges;
                $done_payment_charges->adjustment = $done_payment_charges->adjustment + $adjustment_amount;
                $done_payment_charges->save();
            } else {
                $done_payment_charges = new DonePaymentCalculation();
                $done_payment_charges->done_payment_id = $done_payment_id;
                $done_payment_charges->amount = $amount;
                $done_payment_charges->charges = $charges;
                $done_payment_charges->gst = $gst;
                $done_payment_charges->wht = $wht;
                $done_payment_charges->payable = $payable;
                $done_payment_charges->packaging_charges = $packaging_material_charges;
                $done_payment_charges->adjustment = $adjustment_amount;
                $done_payment_charges->save();
            }
        }
    }

    static public function sub_pending_payment_charges($pending_payment_id, $amount, $charges, $gst, $payable, $retail = NULL, $wht = 0)
    {

        if ($retail != null) {
            $pending_payment_charges = RetailPendingPaymentCalculation::where('retail_pending_payment_id', $pending_payment_id);
            if ($pending_payment_charges->exists()) {
                $pending_payment_charges = $pending_payment_charges->first();
                $pending_payment_charges->amount = $pending_payment_charges->amount - $amount;
                $pending_payment_charges->payable = $pending_payment_charges->payable - $payable;
                $pending_payment_charges->save();
            }
        } else {
            $pending_payment_charges = PendingPaymentCalculation::where('pending_payment_id', $pending_payment_id);
            if ($pending_payment_charges->exists()) {
                $pending_payment_charges = $pending_payment_charges->first();
                $pending_payment_charges->amount = $pending_payment_charges->amount - $amount;
                $pending_payment_charges->charges = $pending_payment_charges->charges - $charges;
                $pending_payment_charges->gst = $pending_payment_charges->gst - $gst;
                $pending_payment_charges->wht = $pending_payment_charges->wht - $wht;
                $pending_payment_charges->payable = $pending_payment_charges->payable - $payable;
                $pending_payment_charges->save();
            }
        }
    }

    public function make_payments_pickup_wise_index()
    {
        $banks = BanksList::all();
        $company_banks = BanksList::where('affiliate', 1)->get();
        $settings = GlobalSettings::where('type', 'pickup_wise_payment_accounts');
        if ($settings->exists()) {
            $pickup_wise_accounts = array();
            $settings = $settings->first();
            $pickup_wise_accounts = array_map('intval', explode(',', $settings->text));
            if (session('department_id') == 7 && !in_array(session('id'), session('sale_users_bypass'))) {
                $sales_person_shippers = array();
                foreach ($pickup_wise_accounts as $account_id) {
                    if (in_array($account_id, session('tagged_shippers'))) {
                        $sales_person_shippers[] = $account_id;
                    }
                }
                $shippers = User::whereIn('id', $sales_person_shippers)->select('id', 'name')->get();
            } else {
                $shippers = User::select('id', 'name')->whereIn('id', $pickup_wise_accounts)->get();
            }
            $shipper_status = [1 => 'Active', 2 => 'Inactive'];
            $total_amount = PendingPaymentShipment::join('pending_payments', 'pending_payments.id', '=', 'pending_payment_shipments.pending_payment_id')->whereIn('pending_payments.user_id', $shippers)->sum('pending_payment_shipments.amount');
            $total_charges = PendingPaymentShipment::join('pending_payments', 'pending_payments.id', '=', 'pending_payment_shipments.pending_payment_id')->whereIn('pending_payments.user_id', $shippers)->sum('pending_payment_shipments.charges');
            $total_payable = PendingPaymentShipment::join('pending_payments', 'pending_payments.id', '=', 'pending_payment_shipments.pending_payment_id')->whereIn('pending_payments.user_id', $shippers)->sum('pending_payment_shipments.payable');
            ActivityTrailController::createActivityTrailLog(Auth::id(), 31);
            return view('admin.finance.make_payments_pickup_wise')->with(['banks' => $banks, 'shipper_status' => $shipper_status, 'total_amount' => $total_amount, 'company_banks' => $company_banks, 'total_charges' => $total_charges, 'total_payable' => $total_payable, 'shippers' => $shippers]);
        }
        return redirect()->back()->with('error', 'No settings found!');
    }

    public function make_payments_pickup_wise_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 91);
        }

        $settings = GlobalSettings::where('type', 'pickup_wise_payment_accounts')->first();
        $pickup_wise_accounts = array();
        $shippers = array();
        $pickup_wise_accounts = array_map('intval', explode(',', $settings->text));
        if (session('department_id') == 7 && !in_array(session('id'), session('sale_users_bypass'))) {
            $sales_person_shippers = array();
            foreach ($pickup_wise_accounts as $account_id) {
                if (in_array($account_id, session('tagged_shippers'))) {
                    $sales_person_shippers[] = $account_id;
                }
            }
            $shippers = User::whereIn('id', $sales_person_shippers)->select('id', 'name')->get();
        } else {
            $shippers = User::select('id')->whereIn('id', $pickup_wise_accounts)->get();
        }
        $pending_payments = PendingPayment::join('users as u', 'pending_payments.user_id', '=', 'u.id')
            ->join('cities as c', 'u.city_id', '=', 'c.id')
            ->join('pending_payment_shipments as pps', 'pending_payments.id', '=', 'pps.pending_payment_id')
            ->join('shipments as s', 's.id', '=', 'pps.shipment_id')
            ->join('user_shipping_infos AS usi', 's.pickup_address_id', '=', 'usi.id')
            ->join('cities as pc', 'pc.id', '=', 'usi.city_id')
            ->select('pending_payments.id as id', 'pending_payments.created_at', 'u.name as shipper', 'pc.name as city', 'u.phone', 'u.phone2', 'usi.pickup_address', 'usi.vendor', DB::raw('(select count(ps.id) from shipments as ps INNER JOIN pending_payment_shipments AS ppsc ON ps.id = ppsc.shipment_id where ppsc.pending_payment_id = pending_payments.id and ps.pickup_address_id = s.pickup_address_id) as total_shipments'), DB::raw('SUM(pps.amount) as total_amount'), DB::raw('SUM(pps.charges) as total_charges'), DB::raw('SUM(pps.gst) as total_gst'), DB::raw('SUM(pps.payable) as total_payable'), 's.booking_type_id', 'usi.poc', DB::raw('(select count(id) from shipments where shipments.user_id = u.id and shipments.pickup_address_id = s.pickup_address_id and shipments.shipper_status_id not in (1, 14, 17, 20, 21, 22, 23, 24, 25, 30, 31, 51)) as total_pending_shipments'), DB::raw('SUM(IF(pps.type = 2, pps.payable, 0)) as total_adjustments'), 's.packaging_charges', 'u.documents_status', 'usi.id as pickup_address_id')
            ->whereIn('u.id', $shippers)
//            ->groupBy('pending_payments.id');
            ->groupBy('s.pickup_address_id');

//        if(session('department_id') == 7){
//            if(session('role_id') != 4 ){
//                $pending_payments = $pending_payments->where(function ($query) use ($shippers) {
//                    $query->whereIn('u.id', $shippers);
//                });
//            }
//        }
        if (session('role_id') != 1) {
            $pending_payments = $pending_payments->whereIn('c.hub_id', session('hubs'));
        }

        $datatables = Datatables::of($pending_payments)
            ->setRowAttr([
                'data-pickup_address_id' => function ($shipments) {
                    return $shipments->pickup_address_id;
                },

            ])
            ->addColumn('total_deductable', function ($pending_payments) {
                return number_format(($pending_payments->total_charges + $pending_payments->total_gst), 2);
            })
            ->editColumn('shipper', function ($shipment) {
                if ($shipment->booking_type_id == 4) {
                    return $shipment->shipper . ' (' . $shipment->poc . ')';
                } else {
                    return $shipment->shipper;
                }
            })
            ->filterColumn('u.name', function ($query, $keyword) {
                $query->where(function ($sub_query) use ($keyword) {
                    $sub_query->where('s.booking_type_id', '!=', 4)
                        ->where('u.name', 'like', '%' . $keyword . '%');
                })
                    ->orWhere(function ($sub_query) use ($keyword) {
                        $sub_query->where('s.booking_type_id', '=', 4)
                            ->where('usi.poc', 'like', '%' . $keyword . '%');
                    });
            })
            ->orderColumn('u.name', 'u.name $1, usi.poc $1')
            ->editColumn('total_amount', function ($pending_payment) {
                return number_format($pending_payment->total_amount, 2);
            })
            ->editColumn('total_charges', function ($pending_payment) {
                return number_format($pending_payment->total_charges, 2);
            })
            ->editColumn('total_gst', function ($pending_payment) {
                return number_format($pending_payment->total_gst, 2);
            })
            ->editColumn('packaging_charges', function ($pending_payment) {
                return number_format($pending_payment->packaging_charges, 2);
            })
            ->editColumn('total_payable', function ($pending_payment) {
                return number_format(ROUND($pending_payment->total_payable, 0, PHP_ROUND_HALF_DOWN));
            })
            ->editColumn('total_adjustments', function ($pending_payment) {
                if ($pending_payment->total_adjustments) {
                    return number_format($pending_payment->total_adjustments, 2);
                } else {
                    return 0;
                }
            })
            ->addColumn('phone_numbers', function ($pending_payment) {
                $phone_numbers = $pending_payment->phone;

                if (!empty($pending_payment->phone2)) {
                    $phone_numbers .= ' - ' . $pending_payment->phone2;
                }

                return $phone_numbers;
            })
            ->removeColumn('phone')
            ->removeColumn('phone2')
            ->addColumn('return_shipments_average_aging', function ($pending_payment) {
                if ($pending_payment->returned_shipments != 0) {
                    $shipments = 0;
                    $days = 0;

                    $now = Carbon::now()->startOfDay();

                    $pending_payment_shipments = PendingPaymentShipment::where('pending_payment_id', $pending_payment->id)->where('type', 1)->get();

                    foreach ($pending_payment_shipments as $pending_payment_shipment) {
                        $created_at = Carbon::parse($pending_payment_shipment->created_at)->startOfDay();

                        $days += $created_at->diffInDays($now);

                        $shipments++;
                    }

                    if ($shipments > 0) {
                        return round(($days / $shipments), 2) . 'd';
                    } else {
                        return '-';
                    }
                } else {
                    return '-';
                }
            })
            ->addColumn('action', function ($pending_payment) {
                $view_details_button = '<button type="button" class="dropdown-item view_details"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-file-text"></i></div><div class="col-9 offset-1">View Details</div></button>';
                $make_payments_button = '<button type="button" class="dropdown-item make_payment"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-credit-card"></i></div><div class="col-9 offset-1">Make Payment</div></button>';

                $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

                $dropdown .= $view_details_button;

                if (($pending_payment->documents_status == 2) && (session('role_id') == 1 || in_array(60, session('permissions')))) {
                    $dropdown .= $make_payments_button;
                }

                $dropdown .= '
                </div>
              </div>
            ';

                return $dropdown;
            })
            ->filterColumn('phone_numbers', function ($query, $keyword) {
                $search = str_replace('-', '', $keyword);

                if ($keyword != '') {
                    $query->where(function ($sub_query) use ($keyword) {
                        $sub_query->where('u.phone', 'like', '%' . $keyword . '%')
                            ->orWhere('u.phone2', 'like', '%' . $keyword . '%');
                    });
                } else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('bank', function ($query, $keyword) {

                if ($keyword != '') {
                    $query->where('ub.id', '=', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->orderColumn('phone_numbers', 'u.phone $1, u.phone2 $1');

        if ($tracking_number = $request->get('tracking_number')) {
            $datatables->join('shipments as ss', 'pps.shipment_id', '=', 'ss.id')
                ->where('ss.tracking_number', '=', $tracking_number);
        }

        if ($positive_negative_filter = $request->get('positive_negative_filter')) {
            if ($positive_negative_filter == 1) {
                $datatables->having('total_payable', '>=', 0);
            } else if ($positive_negative_filter == 2) {
                $datatables->having('total_payable', '<', 0);
            }
        }

        if ($shipper = $request->get('search_shipper')) {
            $datatables->where('u.id', '=', $shipper);
        }

        if ($shipper_status = $request->get('shipper_status')) {
            if ($shipper_status == 1) {
                $datatables->where('u.status', '=', 3)->where('u.blacklist', 0);
            } else {
                $datatables->where('u.status', '!=', 3);
            }
        }
        if ($request->get('shipper_document_status') !== null) {
            $shipper_document_status = $request->get('shipper_document_status');
            if ($shipper_document_status == 0) {
                $datatables->where('u.documents_status', '=', 0);
            } else if ($shipper_document_status == 1) {
                $datatables->where('u.documents_status', '=', 1);
            } else if ($shipper_document_status == 2) {
                $datatables->where('u.documents_status', '=', 2);
            } else if ($shipper_document_status == 3) {
                $datatables->where('u.documents_status', '=', 3);
            } else {
                $datatables->whereRaw('false');
            }
        }
        if ($request->get('payment_filter') !== null) {
            $payment_amount = $request->get('payment_filter');

            $datatables->having('total_payable', '>', $payment_amount);

        }

        return $datatables->make(true);
    }

    static public function invoice_creation($user_id, $billing_period_from_date, $due_date_days, $pending_invoice_shipments)
    {

        $invoice = new Invoice();

        $invoice->user_id = $user_id;
        $invoice->invoicing_date = Carbon::now()->subDay()->startOfDay()->toDateString();
        $invoice->billing_period_from_date = $billing_period_from_date;
        $invoice->billing_period_to_date = Carbon::now()->subDay()->startOfDay()->toDateString();
        $invoice->due_date = Carbon::now()->addDays($due_date_days)->startOfDay()->toDateString();
        $invoice->status_id = 1;

        $invoice->save();

        $invoice_id = $invoice->id;

        $invoice_number = $user_id . str_pad($invoice_id, 6, '0', STR_PAD_LEFT);

        $total_shipments = 0;
        $total_delivered_shipments = 0;
        $total_returned_shipments = 0;
        $total_adjusted_shipments = 0;
        $total_charges = 0;
        $total_gst = 0;
        $total_invoice_amount = 0;

        foreach ($pending_invoice_shipments->get() as $pending_invoice_shipment) {
            $invoice_shipment = new InvoiceShipment();

            $invoice_shipment->created_at = $pending_invoice_shipment->created_at;
            $invoice_shipment->invoice_id = $invoice_id;
            $invoice_shipment->shipment_id = $pending_invoice_shipment->shipment_id;
            $invoice_shipment->type = $pending_invoice_shipment->type;
            $invoice_shipment->charges = $pending_invoice_shipment->charges;
            $invoice_shipment->gst = $pending_invoice_shipment->gst;
            $invoice_shipment->invoice_amount = $pending_invoice_shipment->invoice_amount;

            $invoice_shipment->save();

            $total_shipments++;

            if ($pending_invoice_shipment->type == 0) {
                $total_delivered_shipments++;
            } else if ($pending_invoice_shipment->type == 1) {
                $total_returned_shipments++;
            } else {
                $total_adjusted_shipments++;
            }

            self::adjustment_logs_done(2, $pending_invoice_shipment->id, $invoice_shipment->id);

            $total_charges = $total_charges + $pending_invoice_shipment->charges;
            $total_gst = $total_gst + $pending_invoice_shipment->gst;
            $total_invoice_amount = $total_invoice_amount + $pending_invoice_shipment->invoice_amount;

            $pending_invoice_shipment->delete();
        }

        $invoice->invoice_number = $invoice_number;
        $invoice->total_shipments = $total_shipments;
        $invoice->total_delivered_shipments = $total_delivered_shipments;
        $invoice->total_returned_shipments = $total_returned_shipments;
        $invoice->total_adjusted_shipments = $total_adjusted_shipments;
        $invoice->total_charges = $total_charges;
        $invoice->total_gst = $total_gst;
        $invoice->total_invoice_amount = ROUND($total_invoice_amount, 0, PHP_ROUND_HALF_DOWN);

        $invoice->save();

        NotificationsController::send(27, $invoice_id);
    }


    public function retail_make_payments_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 32);
        $banks = BanksList::all();
        $company_banks = BanksList::where('affiliate', 1)->get();
        $shippers = RetailShipperInfo::select('id', 'shipper_name')->get();
        $shipper_status = [1 => 'Active', 2 => 'Inactive'];
        $total_amount = RetailPendingPaymentShipment::sum('amount');
        $total_payable = RetailPendingPaymentShipment::sum('payable');
        return view('admin.finance.retail.make_payments')->with(['banks' => $banks, 'shipper_status' => $shipper_status, 'total_amount' => $total_amount, 'company_banks' => $company_banks, 'total_payable' => $total_payable, 'shippers' => $shippers]);
    }

    public function retail_make_payments_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 92);
        }

        $pending_payments = RetailPendingPayment::join('retail_shipper_infos as rsi', 'retail_pending_payments.user_id', '=', 'rsi.id')
            ->join('cities as c', 'rsi.city_id', '=', 'c.id')
            ->join('banks_lists as ub', 'rsi.bank_id', '=', 'ub.id')
            ->join('cities as bc', 'rsi.city_id', '=', 'bc.id')
            ->join('retail_pending_payment_shipments as pps', 'retail_pending_payments.id', '=', 'pps.retail_pending_payment_id')
            ->leftjoin('retail_pending_payment_calculations as ppc', 'ppc.retail_pending_payment_id', '=', 'retail_pending_payments.id')
            ->join('shipments as s', 's.id', '=', 'pps.shipment_id')
            ->leftjoin('retail_pending_shipments_for_payments as psfp', 'psfp.user_id', '=', 'retail_pending_payments.user_id')
            ->select('retail_pending_payments.id as id', 'retail_pending_payments.created_at', 'rsi.shipper_name as shipper', 'c.name as city', 'rsi.shipper_phone_no', 'rsi.shipper_address', 'retail_pending_payments.total_shipments', 'retail_pending_payments.delivered_shipments', 'retail_pending_payments.delivered_shipments as delivered_shipments_count', 'retail_pending_payments.adjusted_shipments', 'retail_pending_payments.adjusted_shipments as adjusted_shipments_count', 'ppc.amount as total_amount', 'ppc.payable as total_payable', 'ub.name as bank', 'rsi.account_number', 'rsi.iban', 'bc.name as account_city', 's.booking_type_id', DB::raw('IFNULL(psfp.pending_shipments_count,0) as total_pending_shipments'))
            ->groupBy('retail_pending_payments.id');

        if (session('role_id') != 1) {
            $pending_payments = $pending_payments->whereIn('c.hub_id', session('hubs'));
        }

        $datatables = Datatables::of($pending_payments)
            ->editColumn('delivered_shipments', function ($pending_payment) {
                if ($pending_payment->delivered_shipments != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $pending_payment->delivered_shipments . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('adjusted_shipments', function ($pending_payment) {
                if ($pending_payment->adjusted_shipments != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $pending_payment->adjusted_shipments . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('total_amount', function ($pending_payment) {
                return number_format($pending_payment->total_amount, 2);
            })
            ->editColumn('total_payable', function ($pending_payment) {
                return number_format(ROUND($pending_payment->total_payable, 0, PHP_ROUND_HALF_DOWN));
            })
            ->addColumn('phone_numbers', function ($pending_payment) {
                $phone_numbers = $pending_payment->shipper_phone_no;

                return $phone_numbers;
            })
            ->addColumn('action', function ($pending_payment) {
                $view_details_button = '<button type="button" class="dropdown-item view_details"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-file-text"></i></div><div class="col-9 offset-1">View Details</div></button>';
                $make_payments_button = '<button type="button" class="dropdown-item make_payment"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-credit-card"></i></div><div class="col-9 offset-1">Make Payment</div></button>';

                $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

                $dropdown .= $view_details_button;

                if (session('role_id') == 1 || in_array(460, session('permissions'))) {
                    $dropdown .= $make_payments_button;
                }

                $dropdown .= '
                </div>
              </div>
            ';

                return $dropdown;
            })
            ->filterColumn('phone_numbers', function ($query, $keyword) {
                $search = str_replace('-', '', $keyword);

                if ($keyword != '') {
                    $query->where(function ($sub_query) use ($keyword) {
                        $sub_query->where('rsi.shipper_phone_no', 'like', '%' . $keyword . '%');
                    });
                } else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('bank', function ($query, $keyword) {

                if ($keyword != '') {
                    $query->where('ub.id', '=', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->orderColumn('phone_numbers', 'rsi.shipper_phone_no $1');

        if ($tracking_number = $request->get('tracking_number')) {
            $datatables->join('shipments as ss', 'pps.shipment_id', '=', 'ss.id')
                ->where('ss.tracking_number', '=', $tracking_number);
        }

        if ($positive_negative_filter = $request->get('positive_negative_filter')) {
            if ($positive_negative_filter == 1) {
                $datatables->having('total_payable', '>=', 0);
            } else if ($positive_negative_filter == 2) {
                $datatables->having('total_payable', '<', 0);
            }
        }

        if ($shipper = $request->get('search_shipper')) {
            $datatables->where('rsi.id', '=', $shipper);
        }

        return $datatables->make(true);
    }

    public function retail_make_payments_delivered_shipments(Request $request)
    {
        $tracking_numbers = array();

        $pending_payment_shipments = RetailPendingPaymentShipment::where('retail_pending_payment_id', $request->id)->where('type', 0)->get();

        foreach ($pending_payment_shipments as $pending_payment_shipment) {
            $shipment = $pending_payment_shipment->shipment;

            $tracking_numbers[] = $shipment->tracking_number;
        }

        return $tracking_numbers;
    }

    public function retail_make_payments_adjusted_shipments(Request $request)
    {
        $tracking_numbers = array();

        $pending_payment_shipments = RetailPendingPaymentShipment::where('retail_pending_payment_id', $request->id)->where('type', 2)->get();

        foreach ($pending_payment_shipments as $pending_payment_shipment) {
            $shipment = $pending_payment_shipment->shipment;

            $tracking_numbers[] = $shipment->tracking_number;
        }

        return $tracking_numbers;
    }

    public function retail_make_payments_shipment_details(Request $request)
    {
        $details = array();

        $pending_payment_shipments = RetailPendingPaymentShipment::where('retail_pending_payment_id', $request->id)->get();

        foreach ($pending_payment_shipments as $pending_payment_shipment) {
            $shipment = $pending_payment_shipment->shipment;

            $detail = array();
            if ($request->has('pickup_address_id')) {
                if ($request->pickup_address_id == $shipment->pickup_address_id) {
                    $detail['tracking_number'] = $shipment->tracking_number;

                    if ($pending_payment_shipment->type == 0) {
                        $detail['type'] = 'Delivered';
                    } else {
                        $detail['type'] = 'Adjusted';
                    }

                    $detail['amount'] = number_format($pending_payment_shipment->amount);
                    $detail['payable'] = number_format($pending_payment_shipment->payable, 2);

                    $details[] = $detail;
                }

            } else {
                $detail['tracking_number'] = $shipment->tracking_number;

                if ($pending_payment_shipment->type == 0) {
                    $detail['type'] = 'Delivered';
                } else {
                    $detail['type'] = 'Adjusted';
                }

                $detail['amount'] = number_format($pending_payment_shipment->amount);
                $detail['payable'] = number_format($pending_payment_shipment->payable, 2);

                $details[] = $detail;
            }

        }

        return $details;
    }

    public function retail_make_payments_shipment_list(Request $request)
    {
        $pending_payment_shipments = RetailPendingPaymentShipment::join('shipments as s', 'retail_pending_payment_shipments.shipment_id', '=', 's.id')
            ->join('retail_shipments as rs', 'rs.shipment_id', '=', 's.id')
            ->join('user_shipping_infos AS usi', 's.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('retail_shipper_infos as rsi', 'rs.shipper_account_no', '=', 'rsi.id')
            ->join('shipment_status as ss', 's.shipper_status_id', '=', 'ss.id')
            ->leftjoin('consolidation_shipments as consolidations', function ($join) {
                $join->on('consolidations.shipment_id', '=', 's.id')
                    ->where('consolidations.consolidation_id', '=',
                        DB::raw('(select consolidation_id from consolidation_shipments where consolidation_shipments.shipment_id = s.id)'));
            })
            ->select('retail_pending_payment_shipments.id', 'rsi.shipper_name as shipper', 's.tracking_number as shipment', 'retail_pending_payment_shipments.type', 'ss.name as status', 'retail_pending_payment_shipments.created_at', 'retail_pending_payment_shipments.amount', 'retail_pending_payment_shipments.payable', 'consolidations.consolidation_id', 'oc.name as origin', 's.pickup_address_id');

        if ($request->has('ids')) {
            $pending_payment_shipments->whereIn('retail_pending_payment_shipments.retail_pending_payment_id', $request->ids);
        } else {
            $pending_payment_shipments->whereRaw('FALSE');
        }
        if ($request->has('pickup_address_id')) {
            $pending_payment_shipments->where('s.pickup_address_id', $request->pickup_address_id);
        }

        $datatables = Datatables::of($pending_payment_shipments)
            ->setRowAttr([
                'consolidation_id' => function ($deliveries) {
                    if ($deliveries->consolidation_id != null) {
                        return $deliveries->consolidation_id;
                    } else {
                        return '';
                    }
                },
                'type_id' => function ($deliveries) {
                    return $deliveries->type;
                }
            ])
            ->addColumn('aging', function ($pending_payment_shipments) {
                $now = Carbon::now()->startOfDay();

                $created_at = Carbon::parse($pending_payment_shipments->created_at)->startOfDay();

                return $created_at->diffInDays($now) . 'd';
            })
            ->editColumn('amount', function ($pending_payment_shipment) {
                return number_format($pending_payment_shipment->amount);
            })
            ->editColumn('payable', function ($pending_payment_shipment) {
                return number_format($pending_payment_shipment->payable, 2);
            })
            ->editColumn('type', function ($pending_payment_shipment) {
                if ($pending_payment_shipment->type == 0) {
                    return 'Delivered';
                } else {
                    return 'Adjusted';
                }
            })
            ->filterColumn('type', function ($query, $keyword) {
                if ($keyword == 0 || $keyword == 1 || $keyword == 2) {
                    $query->where('retail_pending_payment_shipments.type', '=', $keyword);
                } else {
                    $query->whereIn('retail_pending_payment_shipments.type', [0, 1, 2]);
                }
            });

        return $datatables->make(true);
    }

    public function retail_make_payments_shipment_export_selected(Request $request)
    {
        $pending_payment_shipment_ids = explode(',', $request->ids);

        $filename = 'sonic_retail_pending_payment_shipments';

        $details = array();

        $details[] = ['S. No.', 'Shipper', 'Shipment', 'Origin', 'Type', 'Status', 'Delivery Datetime', 'Aging', 'Amount', 'Payable'];

        $serial_number = 1;

        foreach ($pending_payment_shipment_ids as $pending_payment_shipment_id) {
            $pending_payment_shipment = RetailPendingPaymentShipment::find($pending_payment_shipment_id);

            $shipment = $pending_payment_shipment->shipment;

            if ($pending_payment_shipment->type == 0) {
                $type = 'Delivered';
            } else {
                $type = 'Adjusted';
            }

            $now = Carbon::now()->startOfDay();
            $created_at = Carbon::parse($pending_payment_shipment->created_at)->startOfDay();
            $aging = $created_at->diffInDays($now) . 'd';

            $row = array();

            $row[] = $serial_number;
            $row[] = $shipment->retail->shipper->shipper_name;
            $row[] = $shipment->tracking_number;
            $row[] = $shipment->pickup_address->city->name;
            $row[] = $type;
            $row[] = $shipment->status_shipper->name;
            $row[] = $pending_payment_shipment->created_at;
            $row[] = $aging;
            $row[] = $pending_payment_shipment->amount;
            $row[] = $pending_payment_shipment->payable;

            $serial_number++;

            $details[] = $row;
        }

        $filename .= '.xlsx';

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->getStyle('C')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('H')->getNumberFormat()->setFormatCode('#,##0');
        $spreadsheet->getActiveSheet()->getStyle('I')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('J')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('K')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('L')->getNumberFormat()->setFormatCode('#,##0.00');

        $spreadsheet->getActiveSheet()->fromArray($details);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function retail_make_payments_verify(Request $request)
    {
        $settings = DB::connection('reports')->table('global_settings')->where('type', 'over_payment_limit')->first();

        if ($settings) {
            $over_payment_limit = $settings->setting_value;
        } else {
            $over_payment_limit = 6000000;
        }

        $pending_payment_shipment_ids = explode(',', $request->pending_payment_shipment_ids);

        $pending_payment_payables = array();

        $shipment_ids = array();
        $duplicate_shipment_ids = array();
        $duplicate_shipments = array();

        foreach ($pending_payment_shipment_ids as $pending_payment_shipment_id) {
            $pending_payment_shipment = RetailPendingPaymentShipment::find($pending_payment_shipment_id);

            if ($pending_payment_shipment) {
                if (!isset($pending_payment_payables[$pending_payment_shipment->retail_pending_payment_id])) {
                    $pending_payment_payables[$pending_payment_shipment->retail_pending_payment_id] = $pending_payment_shipment->payable;
                } else {
                    $pending_payment_payables[$pending_payment_shipment->retail_pending_payment_id] = $pending_payment_payables[$pending_payment_shipment->retail_pending_payment_id] + $pending_payment_shipment->payable;
                }

                $shipment_id = $pending_payment_shipment->shipment_id;
                $type = $pending_payment_shipment->type;

                if (!isset($shipment_ids[$type]) || !in_array($shipment_id, $shipment_ids[$type])) {
                    $shipment_ids[$type][] = $shipment_id;
                } else {
                    if (!isset($duplicate_shipment_ids[$type]) || !in_array($shipment_id, $duplicate_shipment_ids[$type])) {
                        $duplicate_shipment_ids[$type][] = $shipment_id;

                        $shipment = Shipment::find($shipment_id);

                        $duplicate_shipment = $shipment->tracking_number . ' - ';

                        if ($type == 0) {
                            $duplicate_shipment .= 'Delivered';
                        } else {
                            $duplicate_shipment .= 'Adjusted';
                        }
                        $duplicate_shipments[] = $duplicate_shipment;
                    }
                }
            }
        }

        $shipper_ids = array();

        $negative_payments = array();

        $over_payments = array();

        foreach ($pending_payment_payables as $pending_payment_id => $payable) {
            $pending_payment_shipper = RetailPendingPayment::find($pending_payment_id)->shipper;

            $shipper_ids[] = $pending_payment_shipper->id;

            if ($payable < 0) {
                $negative_payments[] = $pending_payment_shipper->shipper_name;
            } else if ($payable > $over_payment_limit) {
                $over_payment = array();

                $over_payment['shipper'] = $pending_payment_shipper->shipper_name;
                $over_payment['payable'] = number_format($payable);

                $over_payments[] = $over_payment;
            }
        }

        if (empty($over_payments)) {
            $over_payments = false;
        }

        if (empty($negative_payments)) {
            if (empty($duplicate_shipments)) {
                return ['status' => 0, 'negative_payments' => false, 'duplicate_shipments' => false, 'over_payments' => $over_payments];
            } else {
                return ['status' => 0, 'negative_payments' => false, 'duplicate_shipments' => $duplicate_shipments, 'over_payments' => $over_payments];
            }
        } else {
            return ['status' => 1, 'negative_payments' => $negative_payments];
        }
    }


    public function retail_make_payments_export_bank_order(Request $request)
    {
        $done_payment_ids = explode(',', $request->done_payment_ids);

        $filename = 'sonic_retail_bank_order';

        $details = array();

        $details[] = ['Payment ID', 'Client', 'IBAN', 'Bank', 'Payable'];

        foreach ($done_payment_ids as $done_payment_id) {
            $filename .= '_' . $done_payment_id;

            $done_payment = RetailDonePayment::find($done_payment_id);

            $shipper = RetailShipperInfo::find($done_payment->user_id);


            $payable = number_format(ROUND((RetailDonePaymentShipment::where('retail_done_payment_id', $done_payment_id)->sum('payable') - $done_payment->ibft_charges), 0, PHP_ROUND_HALF_DOWN));

            $row = array();

            $row[] = str_pad($done_payment->id, 6, '0', STR_PAD_LEFT);
            $row[] = $shipper->shipper_name;
            $row[] = $shipper->iban;
            $row[] = $shipper->bank->name;
            $row[] = $payable;

            $details[] = $row;
        }

        $filename .= '.xlsx';

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode('#,##0');

        $spreadsheet->getActiveSheet()->fromArray($details);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function retail_make_payments_store(Request $request)
    {

        $pending_payment_shipment_ids = RetailPendingPaymentShipment::whereIn('id', explode(',', $request->pending_payment_shipment_ids))->select('retail_pending_payment_id', 'id')->get()->mapToGroups(function ($item, $key) {
            return [$item['retail_pending_payment_id'] => $item['id']];
        })->toArray();
        $company_bank = $request->get('company_bank_id');

        $done_payment_ids = array();


        foreach ($pending_payment_shipment_ids as $pending_payment_id => $pending_payment_shipment_ids) {
            $total_shipments = RetailPendingPaymentShipment::where('retail_pending_payment_id', $pending_payment_id)->count();
            $selected_shipments = count($pending_payment_shipment_ids);

            $pending_payment = RetailPendingPayment::find($pending_payment_id);

            if ($pending_payment) {
                $user_bank_id = RetailShipperInfo::where('id', $pending_payment->user_id)->first();

                if ($user_bank_id) {
                    $user_bank_id = $user_bank_id->id;
                }

                if ($total_shipments == $selected_shipments) {


                    $done_payment = new RetailDonePayment();

                    $done_payment->user_id = $pending_payment->user_id;
                    $done_payment->total_shipments = $pending_payment->total_shipments;
                    $done_payment->delivered_shipments = $pending_payment->delivered_shipments;
                    $done_payment->adjusted_shipments = $pending_payment->adjusted_shipments;
                    $done_payment->company_bank_id = $company_bank;
                    $done_payment->user_bank_info_id = $user_bank_id;


                    $settings = GlobalSettings::where('type', 'ibft_charges');

                    if ($settings->exists()) {
                        $settings = $settings->first();

                        $done_payment->ibft_charges = $settings->setting_value;
                    }

                    $done_payment->save();

                    $pending_payment->delete();

                    foreach ($pending_payment_shipment_ids as $pending_payment_shipment_id) {
                        $pending_payment_shipment = RetailPendingPaymentShipment::find($pending_payment_shipment_id);

                        if ($pending_payment_shipment) {
                            $done_payment_shipment = new RetailDonePaymentShipment();

                            $done_payment_shipment->created_at = $pending_payment_shipment->created_at;
                            $done_payment_shipment->retail_done_payment_id = $done_payment->id;
                            $done_payment_shipment->shipment_id = $pending_payment_shipment->shipment_id;
                            $done_payment_shipment->type = $pending_payment_shipment->type;
                            $done_payment_shipment->amount = $pending_payment_shipment->amount;
                            $done_payment_shipment->payable = $pending_payment_shipment->payable;
                            $done_payment->company_bank_id = $company_bank;

                            $done_payment_shipment->save();

                            $packaging_material_charges = 0;
                            $adjustment_amount = 0;
                            if ($pending_payment_shipment->type == 2) {
                                $adjustment_amount = $pending_payment_shipment->payable;
                            }

                            self::add_done_payment_charges($done_payment->id, $pending_payment_shipment->amount, 0, 0, $pending_payment_shipment->payable, $packaging_material_charges, $adjustment_amount, 1);
                            $pending_payment_shipment->delete();

                            self::adjustment_logs_done(1, $pending_payment_shipment_id, $done_payment_shipment->id);

                            if ($done_payment_shipment->type == 0) {
                                $shipment = Shipment::find($pending_payment_shipment->shipment_id);

                                $shipment->payment_status_id = 1;

                                $shipment->save();

                                ShipmentsPaymentJourneyController::add($shipment->id, 1, Auth::id(), '', $done_payment->id, 1);
                            } else if ($done_payment_shipment->type == 1) {
                                $shipment = Shipment::find($pending_payment_shipment->shipment_id);

                                $shipment->payment_status_id = 5;

                                $shipment->save();

                                ShipmentsPaymentJourneyController::add($shipment->id, 5, Auth::id(), '', $done_payment->id, 1);
                            }
                        }
                    }

                    $done_payment_ids[] = $done_payment->id;

                    NotificationsController::send(20, $done_payment->id);
                } else {
                    $done_payment = new RetailDonePayment();

                    $done_payment->user_id = $pending_payment->user_id;
                    $done_payment->total_shipments = 0;
                    $done_payment->delivered_shipments = 0;
                    $done_payment->adjusted_shipments = 0;
                    $done_payment->company_bank_id = $company_bank;
                    $done_payment->user_bank_info_id = $user_bank_id;
                    $settings = GlobalSettings::where('type', 'ibft_charges');

                    if ($settings->exists()) {
                        $settings = $settings->first();

                        $done_payment->ibft_charges = $settings->setting_value;
                    }

                    $done_payment->save();

                    $total_shipments = 0;
                    $delivered_shipments = 0;
                    $adjusted_shipments = 0;

                    foreach ($pending_payment_shipment_ids as $pending_payment_shipment_id) {
                        $pending_payment_shipment = RetailPendingPaymentShipment::find($pending_payment_shipment_id);

                        if ($pending_payment_shipment) {
                            $total_shipments++;

                            if ($pending_payment_shipment->type == 0) {
                                $delivered_shipments++;
                            } else {
                                $adjusted_shipments++;
                            }

                            $done_payment_shipment = new RetailDonePaymentShipment();

                            $done_payment_shipment->created_at = $pending_payment_shipment->created_at;
                            $done_payment_shipment->retail_done_payment_id = $done_payment->id;
                            $done_payment_shipment->shipment_id = $pending_payment_shipment->shipment_id;
                            $done_payment_shipment->type = $pending_payment_shipment->type;
                            $done_payment_shipment->amount = $pending_payment_shipment->amount;
                            $done_payment_shipment->payable = $pending_payment_shipment->payable;
                            $done_payment->company_bank_id = $company_bank;

                            $done_payment_shipment->save();

                            $packaging_material_charges = 0;


                            $adjustment_amount = 0;
                            if ($pending_payment_shipment->type == 2) {
                                $adjustment_amount = $pending_payment_shipment->amount;
                            }

                            self::add_done_payment_charges($done_payment->id, $pending_payment_shipment->amount, 0, 0, $pending_payment_shipment->payable, $packaging_material_charges, $adjustment_amount, 1);

                            self::adjustment_logs_done(1, $pending_payment_shipment_id, $done_payment_shipment->id, 1);

                            $pending_payment_shipment->delete();

                            self::sub_pending_payment_charges($pending_payment_shipment->retail_pending_payment_id, $pending_payment_shipment->amount, 0, 0, $pending_payment_shipment->payable, 1);

                            if ($done_payment_shipment->type == 1) {
                                $shipment = Shipment::find($pending_payment_shipment->shipment_id);

                                $shipment->payment_status_id = 5;

                                $shipment->save();

                                ShipmentsPaymentJourneyController::add($shipment->id, 5, Auth::id(), '', $done_payment->id, 1);
                            } else {
                                $shipment = Shipment::find($pending_payment_shipment->shipment_id);

                                $shipment->payment_status_id = 1;

                                $shipment->save();

                                ShipmentsPaymentJourneyController::add($shipment->id, 1, Auth::id(), '', $done_payment->id, 1);
                            }
                        }
                    }

                    $done_payment->total_shipments = $total_shipments;
                    $done_payment->delivered_shipments = $delivered_shipments;
                    $done_payment->adjusted_shipments = $adjusted_shipments;


                    $done_payment->save();

                    $pending_payment->total_shipments = RetailPendingPaymentShipment::where('retail_pending_payment_id', $pending_payment_id)->count();
                    $pending_payment->delivered_shipments = RetailPendingPaymentShipment::where('retail_pending_payment_id', $pending_payment_id)->where('type', 0)->count();
                    $pending_payment->adjusted_shipments = RetailPendingPaymentShipment::where('retail_pending_payment_id', $pending_payment_id)->where('type', 2)->count();

                    $pending_payment->save();

                    $done_payment_ids[] = $done_payment->id;

//                    NotificationsController::send(20, $done_payment->id);
                }
            }
        }

        return redirect()->back()->with(['success' => 'Payment(s) has been Made.', 'print' => $done_payment_ids]);
    }

    public function retail_make_payments_stats_calculate(Request $request)
    {
        $total_amount = 0;
        $total_payable = 0;

        if ($positive_negative_filter = $request->get('positive_negative_filter')) {
            if (RetailPendingPayment::exists()) {
                foreach (RetailPendingPayment::get() as $pending_payment) {
                    $payable = RetailPendingPaymentShipment::where('retail_pending_payment_id', $pending_payment->id)->sum('payable');

                    if ($positive_negative_filter == 1 && $payable >= 0) {
                        $total_amount += RetailPendingPaymentShipment::where('retail_pending_payment_id', $pending_payment->id)->sum('amount');
                        $total_payable += $payable;
                    } else if ($positive_negative_filter == 2 && $payable < 0) {
                        $total_amount += RetailPendingPaymentShipment::where('retail_pending_payment_id', $pending_payment->id)->sum('amount');
                        $total_payable += $payable;
                    }
                }
            } else {
                $total_amount = RetailPendingPaymentShipment::sum('amount');
                $total_payable = RetailPendingPaymentShipment::sum('payable');
            }
        } else {
            $total_amount = RetailPendingPaymentShipment::sum('amount');
            $total_payable = RetailPendingPaymentShipment::sum('payable');
        }

        return ['total_amount' => $total_amount, 'total_payable' => $total_payable];
    }

    public function retail_done_payments_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 33);
        $shippers = RetailShipperInfo::select('id', 'shipper_name as name')->get();


        $shipper_status = [1 => 'Active', 2 => 'Inactive'];

        $banks = BanksList::all();
        $company_banks = BanksList::where('affiliate', 1)->get();
        $case_nature_channels = CrmRequestChannel::where('id', '!=', 1)->get();

        return view('admin.finance.retail.done_payments')->with(['banks' => $banks, 'company_banks' => $company_banks, 'shippers' => $shippers, 'case_nature_channels' => $case_nature_channels, 'shipper_status' => $shipper_status]);
    }

    public function retail_done_payments_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 93);
        }

        $done_payments = RetailDonePayment::join('retail_shipper_infos as rsi', 'retail_done_payments.user_id', '=', 'rsi.id')
            ->join('cities as c', 'rsi.city_id', '=', 'c.id')
            ->leftjoin('retail_done_payment_calculations as dpc', 'dpc.retail_done_payment_id', '=', 'retail_done_payments.id')
            ->leftJoin('banks_lists as ubi', 'ubi.id', '=', 'rsi.bank_id')
            ->leftjoin('banks_lists as b', 'retail_done_payments.company_bank_id', '=', 'b.id')
            ->select('retail_done_payments.id as id', 'retail_done_payments.id as payment_id', 'rsi.shipper_name as shipper', 'c.name as city', 'rsi.shipper_phone_no as shipper_phone', 'rsi.shipper_address', 'retail_done_payments.total_shipments', 'retail_done_payments.delivered_shipments', 'retail_done_payments.delivered_shipments as delivered_shipments_count', 'retail_done_payments.adjusted_shipments', 'retail_done_payments.adjusted_shipments as adjusted_shipments_count', 'dpc.amount as total_amount', 'dpc.payable as total_payable', 'ubi.name as bank', 'retail_done_payments.reference_number', 'retail_done_payments.created_at as done_at', 'b.name as company_bank', 'retail_done_payments.status', 'retail_done_payments.ibft_charges', 'dpc.adjustment as adjustment_charges', 'retail_done_payments.status_updated_at as status_updated_at');

        if (session('role_id') != 1) {
            $done_payments = $done_payments->whereIn('c.hub_id', session('hubs'));
        }

        $datatables = Datatables::of($done_payments)
            ->addColumn('id_padded', function ($done_payment) {
                return str_pad($done_payment->id, 6, '0', STR_PAD_LEFT);
            })
            ->filterColumn('retail_done_payments.id', function ($query, $keyword) {
                return $query->where('retail_done_payments.id', '=', $keyword);
            })
            ->editColumn('payment_id', function ($done_payment) {
                return '<button class="btn btn-sm btn-outline-info align-middle"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($done_payment->id, 6, '0', STR_PAD_LEFT) . '</span></button>';
            })
            ->addColumn('total_deductable', function ($done_payment) {
                return number_format($done_payment->ibft_charges, 2);
            })
            ->editColumn('delivered_shipments', function ($done_payment) {
                if ($done_payment->delivered_shipments != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $done_payment->delivered_shipments . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('adjusted_shipments', function ($done_payment) {
                if ($done_payment->adjusted_shipments != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $done_payment->adjusted_shipments . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('total_amount', function ($done_payment) {
                return number_format($done_payment->total_amount, 2);
            })
            ->editColumn('adjustment_charges', function ($done_payment) {
                return number_format($done_payment->adjustment_charges, 2);
            })
            ->editColumn('total_payable', function ($done_payment) {
                return number_format(ROUND($done_payment->total_payable - $done_payment->ibft_charges, 0, PHP_ROUND_HALF_DOWN));
            })
            ->editColumn('status', function ($done_payment) {
                if ($done_payment->status == 0) {
                    return 'Processed';
                } else if ($done_payment->status == 1) {
                    return 'Paid';
                } else if ($done_payment->status == 2) {
                    return 'Reverted';
                } else {
                    return 'Unknown';
                }
            })
            ->filterColumn('bank', function ($query, $keyword) {

                if ($keyword != '') {
                    $query->where('ub.id', '=', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('company_bank', function ($query, $keyword) {

                if ($keyword != '') {
                    $query->where('b.id', '=', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn('action', function ($done_payment) {
                $dropdown = '<div class="btn-group">
                  <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                  <div class="dropdown-menu dropdown-menu-sm">
                    <button type="button" class="dropdown-item view_details"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-file-text"></i></div><div class="col-9 offset-1">View Details</div></button>';

                if (session('role_id') == 1 || session('department_id') == 4) {
                    $dropdown .= '<button type="button" class="dropdown-item update_details"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Update Details</div></button>';
                }

                $dropdown .= '<button type="button" class="dropdown-item export_to_excel"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-download"></i></div><div class="col-9 offset-1">Export to Excel</div></button>
                    <button type="button" class="dropdown-item request_add"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Add Request</div></button>
                  </div>
                </div>
            ';
                return $dropdown;
            })
            ->filterColumn('status', function ($query, $keyword) {
                $keyword = strtolower($keyword);

                if ($keyword == 0) {
                    $query->where('retail_done_payments.status', '=', 0);
                } else if ($keyword == 1) {
                    $query->where('retail_done_payments.status', '=', 1);
                } else if ($keyword == 2) {
                    $query->where('retail_done_payments.status', '=', 2);
                } else {
                    $query->whereRaw('false');
                }
            });

        if ($tracking_numbers = $request->get('tracking_numbers')) {
            $datatables->join('retail_done_payment_shipments as dps', 'retail_done_payments.id', '=', 'dps.retail_done_payment_id')
                ->join('shipments as ss', 'dps.shipment_id', '=', 'ss.id')
                ->whereIn('ss.tracking_number', explode(',', $tracking_numbers))
                ->groupby('retail_done_payments.id');
        }
        if ($payment_ids = $request->get('search_payment_ids')) {
            $datatables->whereIn('retail_done_payments.id', explode(',', $payment_ids));
        }

        if ($shipper = $request->get('search_shipper')) {
            $datatables->where('rsi.id', '=', $shipper);
        }

        if ($request->get('search_from') && $request->get('search_to')) {
            $from = $request->get('search_from');
            $to = $request->get('search_to');
            $datatables->whereBetween('retail_done_payments.created_at', [$from, $to]);
        }

        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatables->whereBetween('retail_done_payments.status_updated_at', [$from, $to]);
        }
        return $datatables->make(true);
    }

    public function retail_done_payments_paid(Request $request)
    {
        foreach ($request->ids as $done_payment_id) {
            $done_payment = RetailDonePayment::find($done_payment_id);

            if ($done_payment->status != 1) {
                $done_payment->status = 1;
                $done_payment->status_updated_at = Carbon::now();
                $done_payment->status_updated_by = Auth::id();

                $done_payment->save();

                $name = "";
                $phone = "";
                $amount = 0;
                $detail = array();
                if (isset($done_payment->shipper->shipper_name)) {
                    $name = $done_payment->shipper->shipper_name;
                    $phone = $done_payment->shipper->shipper_phone_no;
                    $updated_at = $done_payment->updated_at;
                    $amount = isset($done_payment->retail_done_payment_calculations->payable) ? $done_payment->retail_done_payment_calculations->payable : 0;
                    $done_payment_id = $done_payment->retail_done_payment_calculations->retail_done_payment_id;

//                    $done_payment_id = str_pad($done_payment_id, 6, '0', STR_PAD_LEFT);
                    $detail['name'] = $name;
                    $detail['phone'] = $phone;
                    $detail['updated_at'] = $updated_at;
                    $detail['done_payment_id'] = $done_payment_id;
                    NotificationsController::send(172, $detail);//payment ki id bhejni h amount ki jagah baqi send k function k andar s hi fetching krlnga
                }

                /*$payment_clear = new VisionSoftCodPaymentClear();
                $payment_clear->payment_id = $done_payment_id;
                $payment_clear->status = 1;
                $payment_clear->save();*/

                foreach ($done_payment->done_payment_shipments as $done_payment_shipment) {
                    $shipment = $done_payment_shipment->shipment;

                    if ($done_payment_shipment->type == 1) {
                        $shipment->payment_status_id = 7;

                        $shipment->save();

                        ShipmentsPaymentJourneyController::add($shipment->id, 7, Auth::id(), '', $done_payment->id, 1);
                    } else {
                        $shipment->payment_status_id = 3;

                        $shipment->save();

                        ShipmentsPaymentJourneyController::add($shipment->id, 3, Auth::id(), '', $done_payment->id, 1);
                    }
                }
            }
        }
        return ['status' => 0, 'success' => 'Payment(s) marked Paid'];
    }

    public function retail_done_payments_reverted(Request $request)
    {
        foreach ($request->ids as $done_payment_id) {
            $done_payment = RetailDonePayment::find($done_payment_id);

            if ($done_payment->status != 2) {
                $done_payment->status = 2;
                $done_payment->status_updated_at = Carbon::now();
                $done_payment->status_updated_by = Auth::id();

                $done_payment->save();

                /*$payment_clear = new VisionSoftCodPaymentClear();
                $payment_clear->payment_id = $done_payment_id;
                $payment_clear->status = 2;
                $payment_clear->save();*/

                foreach ($done_payment->done_payment_shipments as $done_payment_shipment) {
                    $shipment = $done_payment_shipment->shipment;

                    if ($done_payment_shipment->type == 1) {
                        $shipment->payment_status_id = 6;

                        $shipment->save();

                        ShipmentsPaymentJourneyController::add($shipment->id, 6, Auth::id(), '', $done_payment->id, 1);
                    } else {
                        $shipment->payment_status_id = 2;

                        $shipment->save();

                        ShipmentsPaymentJourneyController::add($shipment->id, 2, Auth::id(), '', $done_payment->id, 1);
                        //NotificationsController::send(92,$done_payment->id ,$shipment->id);
                    }
                }

            }
//            NotificationsController::send(92,$done_payment->id);
        }

        return ['status' => 0, 'success' => 'Payment(s) marked Reverted'];
    }

    public function retail_done_payments_excel_store(Request $request)
    {
        $names = [
            'payment_id' => 'Payment ID',
            'company_bank_id' => 'Company Bank ID',
            'status' => 'Status',
        ];

        $messages = [
            'required' => ':attribute is Required.',
            'integer' => ':attribute must be an Integer.',
        ];

        $rules = [
            'payment_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('retail_done_payments', 'id')],
            'company_bank_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('banks_lists', 'id')->where(function ($query) {
                $query->where('affiliate', DB::raw(1));
            })],
            'status' => ['required', 'string', 'in:paid,Paid,Reverted,reverted,PAID,REVERTED'],
        ];

        $fields = [0 => 'payment_id', 1 => 'company_bank_id', 2 => 'status'];

        if ($file = $request->file('payments')) {

            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

            $header = ['Payment ID', 'Company Bank ID', 'Status'];
        }

        if (isset($spreadsheet)) {
            $header_correct = TRUE;

            foreach ($spreadsheet[0] as $index => $header_value) {
                if ($index == 1) {
                } elseif (!isset($header[$index]) || $header_value != $header[$index]) {
                    $header_correct = FALSE;
                    break;
                }
            }

            if (!$header_correct) {
                return redirect()->back()->with('error', 'Invalid Columns, Kindly follow the Template provided');
            } else {
                unset($spreadsheet[0]);
            }
        }

        if (!isset($spreadsheet) || !empty($spreadsheet)) {
            $rows = array();

            if (isset($spreadsheet)) {
                foreach ($spreadsheet as $spreadsheet_row) {
                    $row = array();

                    foreach ($spreadsheet_row as $key => $value) {
                        $row[$fields[$key]] = $value;
                    }

                    $rows[] = $row;
                }

                unset($spreadsheet);
            }
            foreach ($rows as $key => $row) {
                $row_id = $key + 2;

                $validate = Validator::make($row, $rules, $messages);

                $validate->setAttributeNames($names);

                if ($validate->fails()) {
                    $errors['Row #' . $row_id] = $validate->errors()->all();
                }
            }
            if (isset($errors)) {
                $errors = array_map(function ($row, $errors) {
                    return $row . ':' . PHP_EOL . implode(' | ', $errors);
                }, array_keys($errors), $errors);
                return redirect()->back()->withErrors($errors);
            } else {
                foreach ($rows as $key => $row) {
                    $payment_id = (int)$row['payment_id'];
                    $done_payment = RetailDonePayment::find($payment_id);
                    $status = strtolower($row['status']);
                    if ($status == "paid") {
                        if ($done_payment->status != 1) {
                            /*$payment_clear = new VisionSoftCodPaymentClear();
                            $payment_clear->payment_id = $payment_id;
                            $payment_clear->status = 1;
                            $payment_clear->save();*/

                            $done_payment->company_bank_id = (int)$row['company_bank_id'];
                            $done_payment->status_updated_at = Carbon::now();
                            $done_payment->status_updated_by = Auth::id();
                            $done_payment->status = 1;

                            $done_payment->save();

                            foreach ($done_payment->done_payment_shipments as $done_payment_shipment) {
                                $shipment = $done_payment_shipment->shipment;

                                if ($done_payment_shipment->type == 1) {
                                    $shipment->payment_status_id = 7;

                                    $shipment->save();

                                    ShipmentsPaymentJourneyController::add($shipment->id, 7, Auth::id(), '', $done_payment->id, 1);
                                } else {
                                    $shipment->payment_status_id = 3;

                                    $shipment->save();

                                    ShipmentsPaymentJourneyController::add($shipment->id, 3, Auth::id(), '', $done_payment->id, 1);


                                    $done_payment_id = $done_payment->retail_done_payment_calculations->retail_done_payment_id;
                                    $detail = array();

                                    $detail['name'] = $done_payment->shipper->shipper_name;;
                                    $detail['phone'] = $done_payment->shipper->shipper_phone_no;
                                    $detail['updated_at'] = $done_payment->updated_at;
                                    $detail['done_payment_id'] = $done_payment_id;
                                    NotificationsController::send(172, $detail);
                                }
                            }
                        }
                    } elseif ($status == "reverted") {
                        if ($done_payment->status != 2 && $done_payment->status != 1) {
                            /*$payment_clear = new VisionSoftCodPaymentClear();
                            $payment_clear->payment_id = $payment_id;
                            $payment_clear->status = 2;
                            $payment_clear->save();*/

                            $done_payment->company_bank_id = (int)$row['company_bank_id'];
                            $done_payment->status_updated_at = Carbon::now();
                            $done_payment->status_updated_by = Auth::id();
                            $done_payment->status = 2;

                            $done_payment->save();

                            foreach ($done_payment->done_payment_shipments as $done_payment_shipment) {
                                $shipment = $done_payment_shipment->shipment;

                                if ($done_payment_shipment->type == 1) {
                                    $shipment->payment_status_id = 6;

                                    $shipment->save();

                                    ShipmentsPaymentJourneyController::add($shipment->id, 6, Auth::id(), '', $done_payment->id, 1);
                                } else {
                                    $shipment->payment_status_id = 2;

                                    $shipment->save();

                                    ShipmentsPaymentJourneyController::add($shipment->id, 2, Auth::id(), '', $done_payment->id, 1);
                                }
                            }
                        }

                        /*if ($done_payment->status == 2) {
                            NotificationsController::send(92,$done_payment->id);
                        }*/

                    }
                }
                return redirect()->back()->with(['success' => 'Status of ' . count($rows) . ' Payment(s) has been Updated']);
            }

        } else {
            return redirect()->back()->with('error', 'No Payments in File');
        }
    }

    public function retail_done_payments_delivered_shipments(Request $request)
    {
        $tracking_numbers = array();

        $done_payment_shipments = RetailDonePaymentShipment::where('retail_done_payment_id', $request->id)->where('type', 0)->get();

        foreach ($done_payment_shipments as $done_payment_shipment) {
            $shipment = $done_payment_shipment->shipment;

            $tracking_numbers[] = $shipment->tracking_number;
        }

        return $tracking_numbers;
    }


    public function retail_done_payments_adjusted_shipments(Request $request)
    {
        $tracking_numbers = array();

        $done_payment_shipments = RetailDonePaymentShipment::where('retail_done_payment_id', $request->id)->where('type', 2)->get();

        foreach ($done_payment_shipments as $done_payment_shipment) {
            $shipment = $done_payment_shipment->shipment;

            $tracking_numbers[] = $shipment->tracking_number;
        }

        return $tracking_numbers;
    }

    public function retail_done_payments_details_print(Request $request)
    {
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $done_payment = RetailDonePayment::find($request->id);

        if (!$done_payment) {
            return ['status' => 1, 'error' => 'Payment not found'];
        }

        $shipper = $done_payment->shipper;


        $shipper_bank = $shipper;


//        $account_type_id = $shipper->account_type_id;

        $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Retail Payment Details</title>

                    <style>
                      @page {
                        size: A4 portrait;
                        margin: 0mm;
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

                      .summary {
                        page-break-inside: avoid;
                      }
                    </style>
                  </head>
                  <body>
                    <div>
                      <div class="p-1">
                        <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr>
                              <td class="text-center align-middle"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto"></td>
                              <td class="text-center align-middle color primary"><strong>Retail Payment Details</strong></td>
                              <td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Payment ID</strong></td>
                              <td>' . str_pad($done_payment->id, 6, '0', STR_PAD_LEFT) . '</td>
                              <td rowspan="11" class="text-center align-middle">
                                <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($done_payment->id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                                <span><strong>' . str_pad($done_payment->id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                              </td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Client</strong></td>
                              <td>' . $shipper->shipper_name . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Client Bank</strong></td>
                              <td>' . $shipper_bank->bank->name . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Account Number</strong></td>
                              <td>' . $shipper_bank->account_number . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>IBAN</strong></td>
                              <td>' . $shipper_bank->iban . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Company Bank</strong></td>
                              <td>' . (($done_payment->company_bank_id) ? $done_payment->company_bank->name : '') . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Reference Number</strong></td>
                              <td>' . $done_payment->reference_number . '</td>
                            </tr>
        ';

        $shipment_details = '';

        $serial_number = 1;

        $total_collection_amount = 0;
        $total_adjustments = 0;
        $total_payable = 0;
        foreach ($done_payment->done_payment_shipments as $done_payment_shipment) {
            $shipment = $done_payment_shipment->shipment;

            $shipment_weight = $shipment->actual_weight;

            if ($done_payment_shipment->type != 2) {
                $change_shipment_weight_log = ChangeShipmentWeightLog::where('shipment_id', $shipment->id);
                if ($change_shipment_weight_log->exists()) {
                    $change_shipment_weight_log = $change_shipment_weight_log->first();

                    $done_payment_shipment_date = Carbon::parse($done_payment_shipment->created_at);
                    $change_shipment_weight_log_date = Carbon::parse($change_shipment_weight_log->created_at);

                    if ($change_shipment_weight_log_date->gt($done_payment_shipment_date)) {
                        $shipment_weight = $change_shipment_weight_log->old_weight;
                    }
                }
            }

            if ($done_payment_shipment->type == 0) {
                $type = 'Delivered';
            } else {
                $type = 'Adjusted';
            }
            $account_type_id = 1;
            $pickup_address = $shipment->pickup_address;

            $shipment_details .= '
                            <tr>
                              <td>' . $serial_number . '</td>
                              <td>' . $shipment->tracking_number . '</td>
                              <td>' . $type . '</td>
                              <td>' . $pickup_address->city->name . '</td>
                              <td>' . $shipment->consignee_city->name . '</td>
                              <td>' . $shipment->retail->shipping_modes->name . '</td>
                              <td>' . $shipment->consignee_name . ' ' . $shipment->consignee_phone_number_1 . '</td>
                              <td>' . $shipment_weight . '</td>
                              <td>' . number_format($done_payment_shipment->amount) . '</td>
                              <td>' . (($done_payment_shipment->type == 2) ? number_format($done_payment_shipment->payable, 2) : '0') . '</td>
                            </tr>
            ';

            $serial_number++;

            if ($account_type_id == 1) {
                if ($done_payment_shipment->type != 1) {
                    if ($done_payment_shipment->charges != 0) {
                        if ($done_payment_shipment->type == 0) {
                            $total_collection_amount += $done_payment_shipment->amount;
                        }
                    } else if ($done_payment_shipment->type == 0) {
                        $total_collection_amount += $done_payment_shipment->amount;
                    }
                    if ($done_payment_shipment->type == 2) {
                        $total_adjustments += $done_payment_shipment->payable;

                    }
                } else {
                    // $total_adjustments += $done_payment_shipment->payable;
                }

                $total_payable += $done_payment_shipment->payable;
            } else {
                if ($done_payment_shipment->type == 0) {
                    $total_collection_amount += $done_payment_shipment->amount;
                } else if ($done_payment_shipment->type == 1) {
                    // $total_adjustments += $done_payment_shipment->payable;
                } else if ($done_payment_shipment->type == 2) {
                    $total_adjustments += $done_payment_shipment->payable;
                }

                $total_payable += $done_payment_shipment->payable;
            }
        }

        $shipment_details .= '
                            <tr>
                                <td colspan=7></td>
                                <td class="color primary"><strong>Total</strong></td>
                                <td class="color secondary"><strong>' . number_format($total_collection_amount) . '</strong></td>
                                <td class="color secondary"><strong>' . number_format($total_adjustments, 2) . '</strong></td>
                            </tr>
      ';

        $html .= '
                            <tr>
                              <td class="color secondary"><strong>Total Collection Amount (PKR)</strong></td>
                              <td>' . number_format($total_collection_amount) . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Total Payable (PKR)</strong></td>
                              <td>' . number_format(ROUND(($total_payable - $done_payment->ibft_charges), 0, PHP_ROUND_HALF_DOWN)) . '</td>
                            </tr>
                          </tbody>
                        </table>

                        <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr>
                              <td class="color primary"><strong>S. No.</strong></td>
                              <td class="color primary"><strong>Tracking No.</strong></td>
                              <td class="color primary"><strong>Type</strong></td>
                              <td class="color primary"><strong>Origin</strong></td>
                              <td class="color primary"><strong>Destination</strong></td>
                              <td class="color primary"><strong>Shipping Mode</strong></td>
                              <td class="color primary"><strong>Consignee</strong></td>
                              <td class="color primary"><strong>Weight (kg)</strong></td>
                              <td class="color primary"><strong>Collection Amount (PKR)</strong></td>
                              <td class="color primary"><strong>Adjustments (PKR)</strong></td>
                            </tr>
      ';

        $html .= $shipment_details;

        $html .= '
                          </tbody>
                        </table>

                        <div class="row">
                            <div class="col-6">
                                <table class="table table-sm table-bordered border summary">
                                  <tbody>
                                    <tr>
                                        <td class="color primary" colspan="2"><strong>Charges Summary (PKR)</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>Total Adjustments</strong></td>
                                        <td class="color secondary">' . number_format($total_adjustments, 2) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>IBFT Charges</strong></td>
                                        <td>' . number_format($done_payment->ibft_charges, 2) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color primary"><strong>Overall Charges</strong></td>
                                        <td class="color secondary"><strong>' . number_format(($total_adjustments + $done_payment->ibft_charges), 2) . '</strong></td>
                                    </tr>
                                  </tbody>
                                </table>
                                <span style="color: red">* 13% GST is applicable for Sindh Region 16% GST for Punjab & KPK</span>
                            </div>
                        </div>
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

    public function retail_done_payments_details(Request $request)
    {
        $done_payment = RetailDonePayment::find($request->id);

        $details = array();

        $details['reference_number'] = $done_payment->reference_number;
        $details['company_bank_id'] = $done_payment->company_bank_id;

        return $details;
    }

    public function retail_done_payments_update_details(Request $request)
    {
        $done_payment = RetailDonePayment::find($request->id);

        $done_payment->reference_number = $request->reference_number;
        $done_payment->company_bank_id = $request->company_bank_id;

        $done_payment->save();

        return ['status' => 0, 'success' => 'Details Updated'];
    }

    public function retail_done_payments_export_to_excel(Request $request)
    {
        $done_payment = RetailDonePayment::find($request->id);

        $filename = 'sonic_retail_payment_details_' . $request->id . '.xlsx';

        $details = array();

        $details[] = ['S. No.', 'Tracking No.', 'Booking Date', 'Type', 'Origin', 'Consignee Name', 'Consignee Phone', 'Destination', 'Service Type', 'Weight (kg)', 'Collection Amount (PKR)', 'Adjustments (PKR)'];

        $account_type_id = 1;

        $serial_number = 1;

        $total_collection_amount = 0;
        $total_adjustments = 0;
        $total_payable = 0;

        foreach ($done_payment->done_payment_shipments as $done_payment_shipment) {
            $shipment = $done_payment_shipment->shipment;

            $shipment_weight = $shipment->actual_weight;

            if ($done_payment_shipment->type != 2) {
                $change_shipment_weight_log = ChangeShipmentWeightLog::where('shipment_id', $shipment->id);
                if ($change_shipment_weight_log->exists()) {
                    $change_shipment_weight_log = $change_shipment_weight_log->first();

                    $done_payment_shipment_date = Carbon::parse($done_payment_shipment->created_at);
                    $change_shipment_weight_log_date = Carbon::parse($change_shipment_weight_log->created_at);

                    if ($change_shipment_weight_log_date->gt($done_payment_shipment_date)) {
                        $shipment_weight = $change_shipment_weight_log->old_weight;
                    }
                }
            }

            if ($done_payment_shipment->type == 0) {
                $type = 'Delivered';
            } else {
                $type = 'Adjusted';
            }

            $pickup_address = $shipment->pickup_address;

            $row = array();

            $row[] = $serial_number;
            $row[] = $shipment->tracking_number;
            $row[] = $shipment->created_at;
            $row[] = $type;
            $row[] = $pickup_address->city->name;
            $row[] = $shipment->consignee_name;
            $row[] = $shipment->consignee_phone_number_1;
            $row[] = $shipment->consignee_city->name;
            $row[] = $shipment_weight;
            $row[] = $done_payment_shipment->amount;
            $row[] = (($done_payment_shipment->type == 2) ? $done_payment_shipment->payable : 0);

            $details[] = $row;

            $serial_number++;

            if ($account_type_id == 1) {
                if ($done_payment_shipment->type != 2) {
                    if ($done_payment_shipment->charges != 0) {
                        if ($done_payment_shipment->type == 0) {
                            $total_collection_amount += $done_payment_shipment->amount;
                        }
                    } else if ($done_payment_shipment->type == 0) {
                        $total_collection_amount += $done_payment_shipment->amount;
                    }
                } else {
                    $total_adjustments += $done_payment_shipment->payable;
                }

                $total_payable += $done_payment_shipment->payable;
            } else {
                if ($done_payment_shipment->type == 0) {
                    $total_collection_amount += $done_payment_shipment->amount;
                } else if ($done_payment_shipment->type == 2) {
                    $total_adjustments += $done_payment_shipment->payable;
                }

                $total_payable += $done_payment_shipment->payable;
            }
        }

        $total_columns = count($details[0]);

        $summary = ['Total Adjustments' => $total_adjustments, 'IBFT Charges' => $done_payment->ibft_charges, 'Overall Charges' => ($total_adjustments + $done_payment->ibft_charges)];

        $details[] = [];

        $row = array();

        for ($c = 0; $c < $total_columns; $c++) {
            $row[] = '';
        }

        $row[] = 'Charges Summary (PKR)';
        $row[] = '';

        $details[] = $row;

        foreach ($summary as $name => $value) {
            $row = array();

            for ($c = 0; $c < $total_columns; $c++) {
                $row[] = '';
            }

            $row[] = $name;
            $row[] = $value;

            $details[] = $row;
        }

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->getStyle('B')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('M')->getNumberFormat()->setFormatCode('#,##0');
        $spreadsheet->getActiveSheet()->getStyle('N')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('O')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('P')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('Q')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('S')->getNumberFormat()->setFormatCode('#,##0.00');

        $spreadsheet->getActiveSheet()->fromArray($details);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function ftl_invoice_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 261);
        $company_banks = BanksList::where('affiliate', 1)->get();
        return view('admin.finance.ftl_invoices')->with(['company_banks' => $company_banks]);
    }

    public function ftl_invoice_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 262);
        }
        $invoices = WalkinFtlInvoice::join('ftl_requests as ftlr', 'walkin_ftl_invoices.ftl_request_id', '=', 'ftlr.id')
            ->join('cities as origin', 'ftlr.origin_id', '=', 'origin.id')
            ->join('cities as destination', 'ftlr.destination_id', '=', 'destination.id')
            ->join('vehicle_types as vt', 'vt.id', 'ftlr.vehicle_id')
            ->leftjoin('shipments as s', 'ftlr.shipment_id', '=', 's.id')
            ->leftjoin('banks_lists as bl', 'walkin_ftl_invoices.company_bank_id', '=', 'bl.id')
            ->leftjoin('transport_mode_vendors as ven', 'ftlr.vendor_id', '=', 'ven.id')
            ->select('walkin_ftl_invoices.id', 'walkin_ftl_invoices.invoice_number', 'ftlr.id as request_id', 'origin.name as origin', 'destination.name as destination', 's.tracking_number', 'ftlr.weight', 'vt.name as vehicle', 'ftlr.quantity', 'ftlr.date as request_date', 'ven.name as vendor', 'ftlr.freight_cost as total_cost', 'ftlr.freight_charges as charges', 'ftlr.gst', 'ftlr.total_charges as total_charges', 'walkin_ftl_invoices.status_id as status', 'walkin_ftl_invoices.receiving_date', 'walkin_ftl_invoices.received_amount as received_amount', 'bl.name as company_bank', 'walkin_ftl_invoices.tax_amount as tax_amount', 'walkin_ftl_invoices.deposit_date as deposit_date', 'ftlr.collection_type');

        $datatables = Datatables::of($invoices)
            ->addColumn('invoice_number_button', function ($invoice) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $invoice->invoice_number . '</button>';
            })
            ->editColumn('total_charges', function ($invoice) {
                return number_format($invoice->total_charges, 2);
            })
            ->editColumn('request_date', function ($invoice) {
                return Carbon::parse($invoice->request_date)->format('Y-m-d');

            })
            ->editColumn('gst', function ($invoice) {
                return number_format($invoice->gst, 2);
            })
            ->editColumn('collection_type', function ($invoice) {
                if ($invoice->collection_type == 1) {
                    return 'Invoice';
                } else {
                    return 'Cash';
                }
            })
            ->editColumn('total_invoice_amount', function ($invoice) {
                return number_format(ROUND($invoice->total_invoice_amount, 0, PHP_ROUND_HALF_DOWN));
            })
            ->editColumn('receiving_date', function ($invoice) {
                if ($invoice->receiving_date) {
                    return Carbon::parse($invoice->receiving_date)->format('Y-m-d');
                } else {
                    return '';
                }
            })
            ->editColumn('deposit_date', function ($invoice) {
                if ($invoice->deposit_date) {
                    return Carbon::parse($invoice->deposit_date)->format('Y-m-d');
                } else {
                    return '';
                }
            })
            ->editColumn('status', function ($invoice) {
                if ($invoice->status == 1) {
                    return 'Pending';
                } else {
                    return 'Received';

                }
            })
            ->addColumn('action', function ($invoice) {
                $mark_as_received_button = '<button type="button" class="dropdown-item mark_as_received"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Receive</div></button>';

                $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

                if ($invoice->status == 1) {
                    if (session('role_id') == 1 || in_array(510, session('permissions'))) {
                        $dropdown .= $mark_as_received_button;
                    }

                }


                $dropdown .= '
                </div>
              </div>
            ';

                return $dropdown;
            });

        return $datatables->make(true);
    }

    public function ftl_invoice_received(Request $request)
    {
        if ($request->ids) {
            $ids = explode(',', $request->ids);
            foreach ($ids as $id) {
                $walkin_ftl_invoice = WalkinFtlInvoice::find($id);
                $walkin_ftl_invoice->receiving_date = $request->receiving_date_formatted;
                $walkin_ftl_invoice->company_bank_id = $request->company_bank;
                $walkin_ftl_invoice->deposit_date = $request->deposit_date_formatted;
                $walkin_ftl_invoice->received_amount = $request->received_amount;
                $walkin_ftl_invoice->tax_amount = $request->tax_amount;
                $walkin_ftl_invoice->status_id = 2;
                $walkin_ftl_invoice->save();
            }
        } elseif ($request->id) {
            $walkin_ftl_invoice = WalkinFtlInvoice::find($request->id);
            $walkin_ftl_invoice->receiving_date = $request->receiving_date_formatted;
            $walkin_ftl_invoice->company_bank_id = $request->company_bank;
            $walkin_ftl_invoice->deposit_date = $request->deposit_date_formatted;
            $walkin_ftl_invoice->received_amount = $request->received_amount;
            $walkin_ftl_invoice->tax_amount = $request->tax_amount;

            $walkin_ftl_invoice->status_id = 2;
            $walkin_ftl_invoice->save();

        }
        return redirect()->back()->with('success', 'Invoice has been marked as Received');
    }

    public function ftl_invoice_print(Request $request)
    {
        $walkin_ftl_invoice = WalkinFtlInvoice::find($request->id);

        if ($walkin_ftl_invoice) {

            $html = '';


            $html .= '
                    <!doctype html>
                    <html lang="en">
                      <head>
                        <meta charset="utf-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                        <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                        <title>Air Waybill</title>

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
                            width: 12.5% !important;
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

                          .border.twice {
                            border-width: 2px !important;
                          }

                          .border.twice-top {
                            border-top-width: 2px !important;
                          }

                          .border.twice-bottom {
                            border-bottom-width: 2px !important;
                          }

                          .border.twice-left {
                            border-left-width: 2px !important;
                          }

                          .border.twice-right {
                            border-right-width: 2px !important;
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
                           
                          .invoice table.table-bordered tbody tr td {
                            width: auto !important;
                          }
                        </style>
                      </head>
                      <body>
                    ';


            $html .= '<div class="invoice p-1">
                    <table class="table table-bordered border">
                      <tbody>
                        <tr>
                          <td class="text-left align-middle">
                            <img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mb-1">
                            <div><strong>TRAX ONLINE PRIVATE LIMITED</strong></div>
                            <div><strong>Address:</strong> Plot #4, DMCHS, Block #7/8, Adjacent to IBL Building Centre, Tipu Sultan Road, Karachi.</div>
                            <div><strong>NTN:</strong> 7930679-5</div>
                          </td>
                          <td class="text-center align-middle color primary"><strong>INVOICE</strong></td>
                        </tr>
                      </tbody>
                    </table>
                    <div class="row align-items-start justify-content-between summary">
                        <div class="col-6">
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                    <td class="color primary" colspan="2"><strong>Sender Details</strong></td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Name</strong></td>
                                    <td>' . $walkin_ftl_invoice->ftl_request->shipment->pickup_address->poc . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Contact No.</strong></td>
                                    <td>' . $walkin_ftl_invoice->ftl_request->shipment->pickup_address->phone . '</td>
                                </tr>
                               </tbody>
                            </table>
                        </div>

                        <div class="col-6">
                            <table class="table table-sm table-bordered border invoice">
                              <tbody>
                                <tr>
                                    <td class="color primary"><strong>Tracking No.</strong></td>
                                    <td>' . $walkin_ftl_invoice->ftl_request->shipment->tracking_number . '</td>
                                </tr>
                               </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row align-items-start justify-content-between summary">
                    <div class="col-6">
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                    <td class="color primary" colspan="2"><strong>Receiver Details</strong></td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Name</strong></td>
                                    <td>' . $walkin_ftl_invoice->ftl_request->shipment->consignee_name . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Address</strong></td>
                                    <td>' . $walkin_ftl_invoice->ftl_request->shipment->consignee_address . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Contact No.</strong></td>
                                    <td>' . $walkin_ftl_invoice->ftl_request->shipment->consignee_phone_number_1 . (($walkin_ftl_invoice->ftl_request->shipment->consignee_phone_number_2) ? (' / ' . $walkin_ftl_invoice->ftl_request->shipment->consignee_phone_number_2) : '') . '</td>
                                </tr>
                               </tbody>
                            </table>
                        </div>
                        <div class="col-6">
                            <table class="table table-sm table-bordered border invoice">
                              <tbody>
                                <tr class="color primary">
                                    <td colspan="4"><strong>Shipment Details</strong></td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Shipping Mode</strong></td>
                                    <td>' . $walkin_ftl_invoice->ftl_request->shipment->shipping_mode->mode . '</td>
                                    <td class="color secondary"><strong>Order ID</strong></td>
                                    <td>' . $walkin_ftl_invoice->ftl_request->shipment->order_id . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Origin</strong></td>
                                    <td>' . $walkin_ftl_invoice->ftl_request->origin->name . '</td>
                                    <td class="color secondary"><strong>Destination</strong></td>
                                    <td>' . $walkin_ftl_invoice->ftl_request->destination->name . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Booking Date</strong></td>
                                    <td>' . $walkin_ftl_invoice->ftl_request->date . '</td>
                                    <td class="color secondary"><strong>Weight</strong></td>
                                    <td>' . $walkin_ftl_invoice->ftl_request->weight . '</td>
                                </tr>
                               </tbody>
                            </table>
                        </div>
                    </div>
                    
            ';


            $html .= '
            <table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                            <td class="color primary text-left"><strong>Invoice Summary</strong></td>
                            <td class="color primary text-right" style="width: 20% !important;"><strong>Amount (PKR)</strong></td>
                        </tr>
                        <tr>
                          <td class="text-left">Frieght Charges</td>
                          <td class="text-right">' . number_format($walkin_ftl_invoice->ftl_request->freight_charges) . '</td>
                        </tr>
                        <tr>
                          <td class="text-left">Received Amount</td>
                          <td class="text-right">' . number_format($walkin_ftl_invoice->received_amount) . '</td>
                        </tr>
                        <tr>
                          <td class="text-left">Tax Amount</td>
                          <td class="text-right">' . number_format($walkin_ftl_invoice->tax_amount) . '</td>
                        </tr>
                      </tbody>
                    </table>
            
                    <div class="row justify-content-end">
                        <div class="col-4">
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                
                                <tr>
                                  <td class="color secondary text-left"><strong>GST (PKR)</strong></td>
                                  <td class="text-right">' . number_format($walkin_ftl_invoice->ftl_request->gst) . '</td>
                                </tr>
                                <tr>
                                  <td class="color primary text-left"><strong>Total Invoice Amount (PKR)</strong></td>
                                  <td class="color secondary text-right">' . number_format($walkin_ftl_invoice->ftl_request->total_charges) . '</td>
                                </tr>
                              </tbody>
                            </table>
                        </div>
                    </div>
                    </div>
                    <div class="mb-1 text-center font-italic"><strong>Disclaimer:</strong> This is a system generated invoice. No signature required.</div>
            ';

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

            return $html;
        } else {
            return '';
        }
    }

    public function ftl_invoice_export_to_excel(Request $request)
    {
        $walkin_ftl_invoice = WalkinFtlInvoice::find($request->id);

        $filename = 'sonic_ftl_invoice_details_' . $request->id . '.xlsx';

        // $details = array();

        // $details[] = ['S. No.', 'Tracking No.', 'Origin', 'Destination', 'Arrival Date', 'Weight (kg)', 'Weight Charges (PKR)', 'Fuel Surcharge (PKR)', 'OSA Charges (PKR)', 'Adjustment Charges (PKR)', 'Total Charges (PKR)', 'GST (PKR)', 'Invoice Amount (PKR)'];

        // $serial_number = 1;

        // foreach ($invoice->invoice_shipments as $invoice_shipment) {
        //     $shipment = $invoice_shipment->shipment;

        //     $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 2);

        //     if ($shipment_journey->exists()) {
        //         $date = $shipment_journey->first()->created_at;
        //     }
        //     else {
        //         $date = $shipment->created_at;
        //     }

        //     $date = Carbon::parse($date)->format('Y-m-d');

        //     $row = array();

        //     $row[] = $serial_number;
        //     $row[] = $shipment->tracking_number;
        //     $row[] = $shipment->pickup_address->city->name;
        //     $row[] = $shipment->consignee_city->name;
        //     $row[] = $shipment->created_at;
        //     $row[] = $shipment->actual_weight;
        //     $row[] = (($invoice_shipment->type != 2) ? $shipment->weight_charges : 0);
        //     $row[] = (($invoice_shipment->type != 2) ? $shipment->fuel_surcharge : 0);
        //     $row[] = (($invoice_shipment->type != 2) ? $shipment->nsa_osa_charges : 0);
        //     $row[] = (($invoice_shipment->type == 2) ? $shipment->adjustment_charges : 0);
        //     $row[] = $invoice_shipment->charges;
        //     $row[] = $invoice_shipment->gst;
        //     $row[] = $invoice_shipment->invoice_amount;

        //     $details[] = $row;

        //     $serial_number++;
        // }

        // $spreadsheet = new Spreadsheet();

        // $spreadsheet->getActiveSheet()->getStyle('B')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        // $spreadsheet->getActiveSheet()->getStyle('G')->getNumberFormat()->setFormatCode('#,##0.00');
        // $spreadsheet->getActiveSheet()->getStyle('H')->getNumberFormat()->setFormatCode('#,##0.00');
        // $spreadsheet->getActiveSheet()->getStyle('I')->getNumberFormat()->setFormatCode('#,##0.00');
        // $spreadsheet->getActiveSheet()->getStyle('J')->getNumberFormat()->setFormatCode('#,##0.00');
        // $spreadsheet->getActiveSheet()->getStyle('K')->getNumberFormat()->setFormatCode('#,##0.00');
        // $spreadsheet->getActiveSheet()->getStyle('L')->getNumberFormat()->setFormatCode('#,##0.00');
        // $spreadsheet->getActiveSheet()->getStyle('M')->getNumberFormat()->setFormatCode('#,##0.00');

        // $spreadsheet->getActiveSheet()->fromArray($details);

        // $writer = new Xlsx($spreadsheet);

        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // header('Content-Disposition: attachment;filename="' . $filename .'"');
        // header('Cache-Control: max-age=0');

        // $writer->save('php://output');
    }

    static public function email_print_invoice($id, $bool)
    {
        $html = self::generate_invoice_print($id, $bool);
        $filename = 'invoice_' . $id;
        $path = public_path() . '/' . 'reports/' . $filename . '.pdf';
        $pdf = SnappyPDF::loadHTML($html)->save($path);
        $link = url('/') . '/' . 'reports/invoice_' . $id . '.pdf';
        return $link;
    }

    public function international_credit_limit_reset($user_id)
    {
        $credit_user = InternationalUsersCreditLimit::where('user_id', $user_id);
        if ($credit_user->exists()) {
            $credit_user = $credit_user->first();
            $credit_user->limit_usage = 0;
            $credit_user->save();
        }
    }

    public function retail_done_payments_generate_report_to_email()
    {
        $date = Carbon::today()->toDateString();
        $response = AdminReportsEmailController::retail_done_payment($date);
        return ['status' => 1, 'success' => ' Retail Done Payment(s) Report Generated'];
    }


    public function reimbursement_invoice_print(Request $request)
    {

        $invoice = InvoiceForReimbursement::find($request->id);

        $shipper = $invoice->shipper;

        $shipper_bank = $shipper->bank()->where('default_bank', 1)->first();

        $account_type_id = $shipper->account_type_id;

        $html = '';

        /*  if (!$email) {*/
        $html .= '
            <!doctype html>
            <html lang="en">
              <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                <title>Invoice</title>
            ';
        //}

        $html .= '
                <style>' . file_get_contents(public_path('app-assets/css/bootstrap.min.css')) . '</style>
        ';

        $html .= '
            <style>@page{size:A4 portrait; margin-top: 12rem; margin-bottom: 2rem; margin-left: 0rem; margin-right: 0rem;}*{-webkit-print-color-adjust:exact!important;color-adjust:exact!important}body{background:none!important;color:#09262e!important;font-size:0.7rem!important}hr{border-top:1px dashed #000}table.table-bordered{page-break-inside:avoid}table.table-bordered thead tr th, table.table-bordered tbody tr td{border:1px solid #09262e!important}.color.primary{background:#c8c8c8!important}.color.secondary{background:#ebebeb!important}.border{border:1px solid #09262e!important}.summary{page-break-before:always;page-break-inside:avoid}.shipments_summary{page-break-before:always}</style>
        ';

        /*  if (!$email) {*/
        $html .= '
              </head>
              <body>
            ';
        //}

        $shipment_details = array();

        $shipment_counts = array();

        $origins = array();

        $total_charges = 0;
        $total_gst = 0;
        $total_invoice_amount = 0;

        foreach ($invoice->invoice_shipments as $invoice_shipment) {
            $shipment = $invoice_shipment->shipment;

            $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 2);

            if ($shipment_journey->exists()) {
                $date = $shipment_journey->first()->created_at;
            } else {
                $date = $shipment->created_at;
            }

            $date = Carbon::parse($date)->format('Y-m-d');

            $origin = $shipment->pickup_address->city->name;

            if (!in_array($origin, $origins)) {
                $origins[] = $origin;
            }

            if (!isset($shipment_counts[$origin])) {
                $shipment_counts[$origin] = 1;
            } else {
                $shipment_counts[$origin]++;
            }


            if (!isset($total_weight_charges[$origin])) {
                $total_weight_charges[$origin] = 0;
            }

            if (!isset($total_cash_handling_charges[$origin])) {
                $total_cash_handling_charges[$origin] = 0;
            }

            if (!isset($total_insurance_charges[$origin])) {
                $total_insurance_charges[$origin] = 0;
            }

            if (!isset($total_return_charges[$origin])) {
                $total_return_charges[$origin] = 0;
            }

            if (!isset($total_fuel_surcharge[$origin])) {
                $total_fuel_surcharge[$origin] = 0;
            }

            if (!isset($total_replacement_charges[$origin])) {
                $total_replacement_charges[$origin] = 0;
            }

            if (!isset($total_try_and_buy_charges[$origin])) {
                $total_try_and_buy_charges[$origin] = 0;
            }

            if (!isset($total_packaging_material_charges[$origin])) {
                $total_packaging_material_charges[$origin] = 0;
            }

            if (!isset($total_intercept_charges[$origin])) {
                $total_intercept_charges[$origin] = 0;
            }

            if (!isset($total_nsa_osa_charges[$origin])) {
                $total_nsa_osa_charges[$origin] = 0;
            }

            if (!isset($total_adjustment_charges[$origin])) {
                $total_adjustment_charges[$origin] = 0;
            }

            if ($invoice_shipment->type != 2) {
                if ($invoice_shipment->type == 0) {
                    $total_cash_handling_charges[$origin] += $shipment->cash_handling_charges;
                    $total_replacement_charges[$origin] += $shipment->replacement_charges;
                    $total_try_and_buy_charges[$origin] += $shipment->try_and_buy_charges;
                } else {
                    $total_return_charges[$origin] += $shipment->return_charges;
                }

                $total_weight_charges[$origin] += $shipment->weight_charges;

                if ($shipment->packaging_material_request) {
                    $total_packaging_material_charges[$origin] += $shipment->packaging_material_charges;
                }

                $total_insurance_charges[$origin] += $shipment->insurance_charges;
                $total_fuel_surcharge[$origin] += $shipment->fuel_surcharge;
                $total_intercept_charges[$origin] += $shipment->intercept_charges;
                $total_nsa_osa_charges[$origin] += $shipment->nsa_osa_charges;
            } else {
                $total_adjustment_charges[$origin] += $invoice_shipment->invoice_amount;
            }

            $total_charges += $invoice_shipment->charges;
            $total_gst += $invoice_shipment->gst;
            $total_invoice_amount += $invoice_shipment->invoice_amount;
        }

        $invoice_number_serial_number = 1;

        $html .= '
                <div>
                  <div class="p-1">
        ';

        $html .= '
                <div>
                  <div class="p-1">
        ';
        $html .= '
                    <div class="row align-items-start justify-content-between summary">
                        
                        <div class="col-6">
                            <h1><b><u>SALES TAX INVOICE</u></b></h1>
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                    <td class="color primary" colspan="2"><strong>Bill To</strong></td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Account No.</strong></td>
                                    <td>' . str_pad($shipper->id, 6, '0', STR_PAD_LEFT) . '</td>
                                </tr>';
        if ($account_type_id == 2) {
            $html .= '<tr>
                                        <td class="color secondary"><strong>Shipper Name</strong></td>
                                        <td>' . $shipper->name . '</td>
                                    </tr>';
        }
        $html .= '<tr>
                                    <td class="color secondary"><strong>Name</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_person_name : $shipper->name) . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Address</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_address : $shipper->address) . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Contact No.</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_person_phone : $shipper->phone) . '</td>
                                </tr>
                                <tr>
                                  <td class="color secondary"><strong>NTN</strong></td>
                                  <td>' . $shipper->ntn_no . '</td>
                                </tr>  ';

        $html .= '<tr>
                                  <td class="color secondary"><strong>STRN</strong></td>
                                  <td>' . $shipper->strn_no . '</td>
                                </tr>';


        $html .= '</tbody>
                            </table>
                        </div>

                        <div class="col-4">
                            <h1 style="text-align:center"><img style="height:85px" src="' . asset('img/invoice_summary_header_logo.png') . '" class="header"></h1>
                            <table class="table table-sm table-bordered border">
                              <tbody> ';
        $html .= '<tr>
                                    <td class="color primary"><strong>SNTN</strong></td>
                                    <td>S-7930679-5</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>PNTN</strong></td>
                                    <td>P-7930679-5</td>
                                </tr>';

        $html .= '<tr>
                                    <td class="color primary"><strong>Billing Period</strong></td>
                                    <td>' . Carbon::parse($invoice->billing_period_from_date)->format('Y-m-d') . ' <-> ' . Carbon::parse($invoice->billing_period_to_date)->format('Y-m-d') . '</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>Invoice No.</strong></td>
                                    <td>' . $invoice->invoice_number . ' - ' . $invoice_number_serial_number . '</td>
                                </tr>
                                <tr> ';

        $html .= '<td class="color primary"><strong>Invoice Date</strong></td>
                                    <td>' . Carbon::parse($invoice->invoicing_date)->format('Y-m-d') . '</td>
                                </tr>';


        $html .= '
                               </tbody>
                            </table>
                        </div>
                    </div>
            ';

        foreach ($origins as $origin) {

            $html .= '
                    <table class="table table-sm table-bordered border">
                      <thead>
                        <tr>
                            <th colspan="3" class="color primary text-center">Invoice Summary - ' . $origin . '</th>
                        </tr>
                        <tr>
                            <th class="color secondary">Description Of Services</th>
                            <th class="color secondary" style="width:50px;">Quantity</th>
                            <th class="color secondary">Total Charges</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Weight Charges</td>
                            <td rowspan="11">' . $shipment_counts[$origin] . '</td>
                            <td>' . number_format($total_weight_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Fuel Surcharge</td>
                            <td>' . number_format($total_fuel_surcharge[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Cash Handling Charges</td>
                            <td>' . number_format($total_cash_handling_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Insurance Charges</td>
                            <td>' . number_format($total_insurance_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Replacement Charges</td>
                            <td>' . number_format($total_replacement_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Try & Buy Charges</td>
                            <td>' . number_format($total_try_and_buy_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Return Charges</td>
                            <td>' . number_format($total_return_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Intercept Charges</td>
                            <td>' . number_format($total_intercept_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>OSA Charges</td>
                            <td>' . number_format($total_nsa_osa_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Packaging Charges</td>
                            <td>' . number_format($total_packaging_material_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Adjustments</td>
                            <td>' . number_format($total_adjustment_charges[$origin], 2) . '</td>
                        </tr>
                      </tbody>
                      </table>
            ';

            $invoice_number_serial_number++;
        }

        $html .= '
                    <div class="row justify-content-end">
                        <div class="col-4">
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                  <td class="color secondary text-left"><strong>Subtotal (PKR)</strong></td>
                                  <td class="text-right">' . number_format($total_charges, 2) . '</td>
                                </tr>
                                <tr>
                                  <td class="color secondary text-left"><strong>SST (PKR)</strong></td>
                                  <td class="text-right">' . number_format($total_gst, 2) . '</td>
                                </tr>
                                <tr>
                                  <td class="color primary text-left"><strong>Total Invoice Amount (PKR)</strong></td>
                                  <td class="color secondary text-right">' . number_format(ROUND($total_invoice_amount, 0, PHP_ROUND_HALF_DOWN)) . '</td>
                                </tr>
                              </tbody>
                            </table>
                        </div>
                    </div>';

        $amount_in_words = self::amount_to_words(ROUND($total_invoice_amount, 0, PHP_ROUND_HALF_DOWN));

        $html .= '<table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="color primary" style="width: 150px;"><strong>Amount in Words</strong></td>
                          <td class="color secondary">' . $amount_in_words . ' Only</td>
                        </tr>
                      </tbody>
                    </table>

                    <table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="color primary text-center" colspan="2"><strong>Bank Account Details</strong></td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Benificiary Name</strong></td>
                          <td>Trax Online Private Limited</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Bank</strong></td>
                          <td>Meezan Bank</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Account No.</strong></td>
                          <td>0102951143</td>
                        </tr> 
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Branch Code.</strong></td>
                          <td>9912</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Branch Address.</strong></td>
                          <td>Liaqatabad Market Malir Branch</td>
                        </tr>
                        </tbody>
                    </table>

                    <div class="mb-1 text-center font-italic"><strong>Disclaimer:</strong> This is a system generated invoice. No signature required.</div>
            ';


        $html .= '
                  </div>
                </div>
        ';

        /*  if (!$email) {*/
        $html .= '
                <script>
                  window.onload = function() {

                    window.print();
                  }
                </script>
              </body>
            </html>
            ';
        //}

        return $html;

    }

    public function corporate_invoice_print(Request $request)
    {

        $invoice = Invoice::find($request->id);

        $shipper = $invoice->shipper;

        $shipper_bank = $shipper->bank()->where('default_bank', 1)->first();

        $account_type_id = $shipper->account_type_id;

        $invoice_number_serial_number = 1;

        $html = '';

       /* if (!$email) {*/
            $html .= '
            <!doctype html>
            <html lang="en">
              <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                <title>Invoice</title>
            ';
        //}

        $html .= '
                <style>' . file_get_contents(public_path('app-assets/css/bootstrap.min.css')) . '</style>
        ';

        $html .= '
            <style>@page{size:A4 portrait; margin-top: 12rem; margin-bottom: 2rem; margin-left: 0rem; margin-right: 0rem;}*{-webkit-print-color-adjust:exact!important;color-adjust:exact!important}body{background:none!important;color:#09262e!important;font-size:0.7rem!important}hr{border-top:1px dashed #000}table.table-bordered{page-break-inside:avoid}table.table-bordered thead tr th, table.table-bordered tbody tr td{border:1px solid #09262e!important}.color.primary{background:#c8c8c8!important}.color.secondary{background:#ebebeb!important}.border{border:1px solid #09262e!important}.summary{page-break-inside:avoid}.shipments_summary{page-break-before:always}</style>
        ';

      /*  if ($header) {*/
            $html .= '
            <style>@page{margin-top: 1rem; margin-bottom: 1rem;}.summary_header .header{width: 10%;}.summary_header .heading{width: 15%;}.summary_footer .footer{width: 75%;}</style>
            ';
        //}

        /*if (!$email) {*/
            $html .= '
              </head>
              <body>
            ';
        //}

        $html .= '
                <div>
                  <div class="p-1">
        ';


        $invoice_number_serial_number = 1;

        $html .= '
                <div>
                  <div class="p-1">
        ';

        $html .= '
                <div>
                  <div class="p-1">
        ';
        $html .= '
                    <div class="row align-items-start justify-content-between summary">
                        
                        <div class="col-6">
                          <!--  <h1><b><u>SALES TAX INVOICE</u></b></h1>-->
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                    <td class="color primary" colspan="2"><strong>Bill To</strong></td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Account No.</strong></td>
                                    <td>' . str_pad($shipper->id, 6, '0', STR_PAD_LEFT) . '</td>
                                </tr>';
        if ($account_type_id == 2) {
            $html .= '<tr>
                                        <td class="color secondary"><strong>Shipper Name</strong></td>
                                        <td>' . $shipper->name . '</td>
                                    </tr>';
        }
        $html .= '<tr>
                                    <td class="color secondary"><strong>Name</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_person_name : $shipper->name) . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Address</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_address : $shipper->address) . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Contact No.</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_person_phone : $shipper->phone) . '</td>
                                </tr>';

        if ($invoice->invoice_type == 1) {
            $html .= '<tr>
                                  <td class="color secondary"><strong>NTN</strong></td>
                                  <td>' . $shipper->ntn_no . '</td>
                                </tr>  

                                <tr>
                                  <td class="color secondary"><strong>STRN</strong></td>
                                  <td>' . $shipper->strn_no . '</td>
                                </tr>';
        }


        $html .= '</tbody>
                            </table>
                        </div>

                        <div class="col-4">
                          
                            <table class="table table-sm table-bordered border">
                              <tbody> ';
        if ($invoice->invoice_type == 1) {
            $html .= '<tr>
                                    <td class="color primary"><strong>SNTN</strong></td>
                                    <td>S-7930679-5</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>PNTN</strong></td>
                                    <td>P-7930679-5</td>
                                </tr>';
        }

        $html .= '<tr>
                                    <td class="color primary"><strong>Billing Period</strong></td>
                                    <td>' . Carbon::parse($invoice->billing_period_from_date)->format('Y-m-d') . ' <-> ' . Carbon::parse($invoice->billing_period_to_date)->format('Y-m-d') . '</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>Invoice No.</strong></td>
                                    <td>' . $invoice->invoice_number . ' - ' . $invoice_number_serial_number . '</td>
                                </tr>
                                <tr> ';

        $html .= '<td class="color primary"><strong>Invoice Date</strong></td>
                                    <td>' . Carbon::parse($invoice->invoicing_date)->format('Y-m-d') . '</td>
                                </tr>';


        $html .= '
                               </tbody>
                            </table>
                        </div>
                    </div>
            ';


        $shipment_details = array();

        $serial_number = array();

        $origins = array();

        $total_charges = 0;
        $total_gst = 0;
        $total_invoice_amount = 0;

        if ($invoice->invoice_type == 1) {
            foreach ($invoice->invoice_shipments as $invoice_shipment) {
                $shipment = $invoice_shipment->shipment;


                $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 2);

                if ($shipment_journey->exists()) {
                    $date = $shipment_journey->first()->created_at;
                } else {
                    $date = $shipment->created_at;
                }

                $date = Carbon::parse($date)->format('Y-m-d');

                $origin = $shipment->pickup_address->city->name;

                if (!in_array($origin, $origins)) {
                    $origins[] = $origin;
                }

                if (!isset($shipment_details[$origin])) {
                    $shipment_details[$origin] = '';
                }

                if (!isset($serial_number[$origin])) {
                    $serial_number[$origin] = 1;
                }

                $shipment_details[$origin] .= '
                        <tr>
                          <td>' . $serial_number[$origin] . '</td>
                          <td>' . $shipment->tracking_number . '</td>
                          <td>' . $shipment->order_id . '</td>
                          <td>' . $shipment->consignee_city->name . '</td>
                          <td>' . $shipment->shipping_mode->mode . '</td>
                          <td>' . $date . '</td>
                          <td>' . $shipment->actual_weight . '</td>
                          <td>' . (($invoice_shipment->type != 2) ? number_format($shipment->weight_charges, 2) : '0') . '</td>
                          <td>' . (($invoice_shipment->type != 2) ? number_format($shipment->fuel_surcharge, 2) : '0') . '</td>
                          <td>' . (($invoice_shipment->type != 2) ? number_format($shipment->nsa_osa_charges, 2) : '0') . '</td>
                          <td>' . (($invoice_shipment->type == 2) ? number_format($invoice_shipment->invoice_amount, 2) : '0') . '</td>
                          <td>' . (($invoice_shipment->type == 2) ? number_format($shipment->packaging_material_charges, 2) : '0') . '</td>
                          <td>' . (($invoice_shipment->type != 2) ? number_format($shipment->esc_charges, 2) : '0') . '</td>
                          <td>' . number_format($invoice_shipment->charges, 2) . '</td>
                          <td>' . number_format($invoice_shipment->gst, 2) . '</td>
                          <td>' . number_format($invoice_shipment->invoice_amount, 2) . '</td>
                        </tr>
            ';

                $serial_number[$origin]++;

                if (!isset($total_weight_charges[$origin])) {
                    $total_weight_charges[$origin] = 0;
                }

                if (!isset($total_cash_handling_charges[$origin])) {
                    $total_cash_handling_charges[$origin] = 0;
                }

                if (!isset($total_insurance_charges[$origin])) {
                    $total_insurance_charges[$origin] = 0;
                }

                if (!isset($total_return_charges[$origin])) {
                    $total_return_charges[$origin] = 0;
                }

                if (!isset($total_fuel_surcharge[$origin])) {
                    $total_fuel_surcharge[$origin] = 0;
                }

                if (!isset($total_replacement_charges[$origin])) {
                    $total_replacement_charges[$origin] = 0;
                }

                if (!isset($total_try_and_buy_charges[$origin])) {
                    $total_try_and_buy_charges[$origin] = 0;
                }

                if (!isset($total_packaging_material_charges[$origin])) {
                    $total_packaging_material_charges[$origin] = 0;
                }

                if (!isset($total_intercept_charges[$origin])) {
                    $total_intercept_charges[$origin] = 0;
                }

                if (!isset($total_nsa_osa_charges[$origin])) {
                    $total_nsa_osa_charges[$origin] = 0;
                }

                if (!isset($total_adjustment_charges[$origin])) {
                    $total_adjustment_charges[$origin] = 0;
                }

                if (!isset($total_extra_service_charges[$origin])) {
                    $total_extra_service_charges[$origin] = 0;
                }


                if ($invoice_shipment->type != 2) {
                    if ($invoice_shipment->type == 0) {
                        $total_cash_handling_charges[$origin] += $shipment->cash_handling_charges;
                        $total_replacement_charges[$origin] += $shipment->replacement_charges;
                        $total_try_and_buy_charges[$origin] += $shipment->try_and_buy_charges;
                        $total_extra_service_charges[$origin] += $shipment->esc_charges;
                    } else {
                        $total_return_charges[$origin] += $shipment->return_charges;
                    }

                    $total_weight_charges[$origin] += $shipment->weight_charges;

                    if ($shipment->packaging_material_request) {
                        $total_packaging_material_charges[$origin] += $shipment->packaging_material_charges;
                    }

                    $total_insurance_charges[$origin] += $shipment->insurance_charges;
                    $total_fuel_surcharge[$origin] += $shipment->fuel_surcharge;
                    $total_intercept_charges[$origin] += $shipment->intercept_charges;
                    $total_nsa_osa_charges[$origin] += $shipment->nsa_osa_charges;
                } else {
                    $total_adjustment_charges[$origin] += $invoice_shipment->invoice_amount;
                }

                $total_charges += $invoice_shipment->charges;
                $total_gst += $invoice_shipment->gst;
                $total_invoice_amount += $invoice_shipment->invoice_amount;
            }

            $html .= '
                    <table class="table table-sm table-bordered border">
                      <thead>
                        <tr>
                            <th colspan="13" class="color primary text-center">Invoice Summary</th>
                        </tr>
                        <tr>
                            <th class="color secondary">Origin</th>
                            <th class="color secondary">Weight Charges (PKR)</th>
                            <th class="color secondary">Cash Handling Charges (PKR)</th>
                            <th class="color secondary">Insurance Charges (PKR)</th>
                            <th class="color secondary">Replacement Charges (PKR)</th>
                            <th class="color secondary">Try & Buy Charges (PKR)</th>
                            <th class="color secondary">Return Charges (PKR)</th>
                            <th class="color secondary">Fuel Surcharge (PKR)</th>
                            <th class="color secondary">Intercept Charges (PKR)</th>
                            <th class="color secondary">OSA Charges (PKR)</th>
                            <th class="color secondary">Packaging Charges (PKR)</th>
                            <th class="color secondary">Extra Service Charges (PKR)</th>
                            <th class="color secondary">Adjustment Charges (PKR)</th>
                        </tr>
                      </thead>
                      <tbody>
        ';

            foreach ($origins as $origin) {
                $html .= '
                        <tr>
                            <td>' . $origin . '</td>
                            <td>' . number_format($total_weight_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_cash_handling_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_insurance_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_replacement_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_try_and_buy_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_return_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_fuel_surcharge[$origin], 2) . '</td>
                            <td>' . number_format($total_intercept_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_nsa_osa_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_packaging_material_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_extra_service_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_adjustment_charges[$origin], 2) . '</td>
                        </tr>
            ';
            }

            $html .= '
                      </tbody>
                    </table>

                    <div class="row justify-content-end">
                        <div class="col-4">
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                  <td class="color secondary text-left"><strong>Subtotal (PKR)</strong></td>
                                  <td class="text-right">' . number_format($total_charges, 2) . '</td>
                                </tr>
                                <tr>
                                  <td class="color secondary text-left"><strong>GST (PKR)</strong></td>
                                  <td class="text-right">' . number_format($total_gst, 2) . '</td>
                                </tr>
                                <tr>
                                  <td class="color primary text-left"><strong>Total Invoice Amount (PKR)</strong></td>
                                  <td class="color secondary text-right">' . number_format(ROUND($total_invoice_amount, 0, PHP_ROUND_HALF_DOWN)) . '</td>
                                </tr>
                              </tbody>
                            </table>
                        </div>
                    </div>

                    <table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="color primary" style="width: 150px;"><strong>Amount in Words</strong></td>
                          <td class="color secondary">' . self::amount_to_words($total_invoice_amount) . ' Only</td>
                        </tr>
                      </tbody>
                    </table>

                    <table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="color primary text-center" colspan="2"><strong>Bank Account Details</strong></td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Benificiary Name</strong></td>
                          <td>Trax Online Private Limited</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Bank</strong></td>
                          <td>Meezan Bank</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Account No.</strong></td>
                          <td>0102951143</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Branch No.</strong></td>
                          <td>9912</td>
                        </tr>
                      </tbody>
                    </table>

                    <div class="mb-1 text-center font-italic"><strong>Disclaimer:</strong> This is a system generated invoice. No signature required.</div>
        ';

        } else {
            $shipment_ids = $invoice->invoice_shipments->pluck('shipment_id')->toArray();

            $packaging_details = array();
            $origin_arrays = array();
            $size_arrays = array();

            if (count($shipment_ids) > 0) {

                $packaging_materials = PackagingMaterialRequest::whereIn('shipment_id', $shipment_ids)->where('packaging_material_requests.status_id', 4)
                    ->where('packaging_material_requests.user_id', $shipper->id);

                if ($packaging_materials->exists()) {
                    $packaging_materials = $packaging_materials->orderBy('city_id', 'asc')->get();
                    foreach ($packaging_materials as $packaging_material) {
                        foreach ($packaging_material->items as $details) {
                            if (!isset($packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['quantity'])) {
                                $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['quantity'] = 1;
                                $zone = Zone::find($packaging_material->city->zone_id);
                                $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['origin'] = $packaging_material->city->name;
                                $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['rates'] = $details->packaging_type_size->standard_charges;
                                $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['description'] = $details->packaging_type_size->type->type . '-' . $details->packaging_type_size->size;
                                $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['quantity'] = $details->quantity;
                                $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['gst'] = $zone->gst;
                            } else {
                                $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['quantity']++;
                            }
                        }
                    }
                }

            }

            if (count($packaging_details) > 0) {

                $html .= '<table class="table table-sm table-bordered border">
          <thead>
            <tr>
                <th colspan="12" class="color primary text-center">Invoice Summary</th>
            </tr>
            <tr>
                <th class="color secondary">Origin</th>
                <th class="color secondary">Description</th>
                <th class="color secondary">Rates</th>
                <th class="color secondary">Quantity</th>
                <th class="color secondary">Total Charges Without GST</th>
                <th class="color secondary">SST %</th>
                <th class="color secondary">SST Amount</th>
                <th class="color secondary">Total Amount with SST</th>
              
            </tr>
          </thead>
          <tbody>
';
                $rates_total = 0;
                $quantity_total = 0;
                $total_amount_without_gst = 0;
                $total_sst_amount = 0;
                $overall_amount = 0;


                foreach ($packaging_details as $cities) {
                    foreach ($cities as $packaging_material) {
                        $amount_without_gst = $packaging_material['rates'] * $packaging_material['quantity'];
                        $sst_amount = round($amount_without_gst * $packaging_material['gst']);
                        $total_amount_with_sst = round($amount_without_gst + $sst_amount);

                        $rates_total = $rates_total + $packaging_material['rates'];
                        $quantity_total = $quantity_total + $packaging_material['quantity'];
                        $total_amount_without_gst = $total_amount_without_gst + $amount_without_gst;
                        $total_sst_amount = $total_sst_amount + $sst_amount;
                        $overall_amount = $overall_amount + $total_amount_with_sst;
                        $html .= '
            <tr>
                <td>' . $packaging_material['origin'] . '</td>
                <td>' . $packaging_material['description'] . '</td>
                <td>' . $packaging_material['rates'] . '</td>
                <td>' . $packaging_material['quantity'] . '</td>
                <td>' . $amount_without_gst . '</td>
                <td>' . $packaging_material['gst'] * 100 . '%' . '</td>
                <td>' . round($sst_amount) . '</td>
                <td>' . number_format($total_amount_with_sst) . '</td>
              
            </tr>';
                        /* $html .= $packaging_material['origin'];*/
                    }
                }
                $html .= '<tr>
                <td colspan="3" class="text-center">Total Amount</td>
            
                <td>' . $quantity_total . '</td>
                <td>' . $total_amount_without_gst . '</td>
                <td></td>
                <td>' . number_format($total_sst_amount) . '</td>
                <td>' . number_format($overall_amount) . '</td>
            </tr>';
                $amount_in_words = '';
                $amount_in_words = self::amount_to_words($overall_amount);

                $html .= '<table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="color primary" style="width: 150px;"><strong>Amount in Words</strong></td>
                          <td class="color secondary">' . $amount_in_words . ' Only</td>
                        </tr>
                      </tbody>
                    </table>

                    <table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="color primary text-center" colspan="2"><strong>Bank Account Details</strong></td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Benificiary Name</strong></td>
                          <td>Trax Online Private Limited</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Bank</strong></td>
                          <td>Meezan Bank</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Account No.</strong></td>
                          <td>0102951143</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>IBAN No.</strong></td>
                          <td>PK02MEZN0099120104111731</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Branch Name.</strong></td>
                          <td>Liaquat Market Malir Branch</td>
                        </tr>';

                $html .= ' <tr>
                                      <td class="color secondary" style="width: 150px;"><strong>Branch Code.</strong></td>
                                      <td>9912</td>
                                    </tr>
                                    </tbody>
                    </table>

                    <div class="mb-1 text-center font-italic"><strong>Disclaimer:</strong> This is a system generated invoice. No signature required.</div>
            ';
            }


            $html .= '
                      </tbody>
                    </table>
            ';

            $invoice_number_serial_number++;
        }

        $html .= '
                  </div>
                </div>
        ';

       /* if (!$email) {*/
            $html .= '
                <script>
                  window.onload = function() {

                    window.print();
                  }
                </script>
              </body>
            </html>
            ';
        //}

        return $html;


    }

    static public function update_esc_charges($shipment_id,$type){
        $shipment = Shipment::find($shipment_id);
        if ($shipment->shipment_type == 1) {
            if (!$shipment->packaging_material_request) {
                if ($type == 0) {
                    $charges = $shipment->weight_charges + $shipment->cash_handling_charges + $shipment->insurance_charges + $shipment->fuel_surcharge + $shipment->replacement_charges + $shipment->try_and_buy_charges + $shipment->intercept_charges + $shipment->nsa_osa_charges + $shipment->esc_charges;
                    if ($shipment->business_category_id == 1) {
                        $gst = ROUND(($charges * self::gst($shipment->pickup_address->city->zone_id)), 2, PHP_ROUND_HALF_DOWN);
                    } else {
                        $gst = ROUND(($charges * self::international_gst()), 2, PHP_ROUND_HALF_DOWN);
                    }
                }
            } else {
                $charges = $shipment->packaging_material_charges;
                $gst = 0;
            }


            $pending_invoice_shipment = PendingInvoiceShipment::where('shipment_id',$shipment_id)->where('type',0);
            if($pending_invoice_shipment->exists()){
                $pending_invoice_shipment = $pending_invoice_shipment->latest()->first();
                $pending_invoice_shipment->type = $type;
                $pending_invoice_shipment->charges = $charges;
                $pending_invoice_shipment->gst = $gst;
                $pending_invoice_shipment->invoice_amount = $charges + $gst;
                $pending_invoice_shipment->save();
            }
            else{
                $invoice_shipment = InvoiceShipment::where('shipment_id',$shipment_id)->where('type',0);
                if($invoice_shipment->exists()){
                    $invoice_shipment = $invoice_shipment->latest()->first();
                    if($invoice_shipment) {
                        $invoice = Invoice::find($invoice_shipment->invoice_id);
                        if ($invoice->status_id != 3) {

                            $total_charges = 0;
                            $total_gst = 0;
                            $total_invoice_amount = 0;

                            $invoice_shipment->charges = $charges;
                            $invoice_shipment->gst = $gst;
                            $invoice_shipment->invoice_amount = $charges + $gst;
                            $invoice_shipment->save();

                            foreach ($invoice->invoice_shipments as $shipments) {

                                $total_charges = $total_charges + $shipments->charges;
                                $total_gst = $total_gst + $shipments->gst;
                                $total_invoice_amount = $total_invoice_amount + $shipments->invoice_amount;
                            }

                            $invoice->total_charges = $total_charges;
                            $invoice->total_gst = $total_gst;
                            $invoice->total_invoice_amount = $total_invoice_amount;
                            $invoice->save();

                        }
                    }
                }
            }
        }
    }

    public function invoice_add_adjustment(Request $request){

        $invoice = Invoice::find($request->invoice_id);

        if($invoice){
            $check_amount = $invoice->deposited_amount + $invoice->adjusted_amount + $request->adjustment_amount;
            if($check_amount > $invoice->total_invoice_amount){
                return redirect()->back()->with('error','The amount you entered is exceeding the balance amount');
            }

            $adjustment = new InvoiceAdjustment();
            $adjustment->invoice_id = $request->invoice_id;
            $adjustment->amount = $request->adjustment_amount;
            $adjustment->reason_id = $request->adjustment_reason;
            $adjustment->remarks = $request->adjustment_remarks;
            $adjustment->added_by = Auth::id();
            $adjustment->save();

            $total_adjustment = InvoiceAdjustment::where('invoice_id',$request->invoice_id)->sum('amount');

            $invoice->adjusted_amount = $total_adjustment;
            $invoice->save();

            return redirect()->back()->with('success','Adjustment Added');
        }
        else{
            return redirect()->back()->with('error','Invalid Invoice');
        }

    }

    public function invoice_adjustment_view(Request $request){
        $invoice_id = $request->invoice_id;
        if ($invoice_id) {
            $adjustments = InvoiceAdjustment::where('invoice_id', $invoice_id)->get();
            if (count($adjustments) > 0) {
                $sorted_array = array();
                $now = Carbon::now();
                foreach ($adjustments as $adjustment) {
                    $sorted_array[$adjustment->id]['adjusted_date'] = Carbon::parse($adjustment->created_at)->toDateString();
                    $sorted_array[$adjustment->id]['amount'] = $adjustment->amount;
                    $sorted_array[$adjustment->id]['reason'] = $adjustment->reasons->name;
                    $sorted_array[$adjustment->id]['remarks'] = $adjustment->remarks;
                    $sorted_array[$adjustment->id]['added_by'] = Admin::find($adjustment->added_by)->name;
                }

                return ['status' => 0, 'adjustments' => $sorted_array];
            } else {
                return ['status' => 1, 'error' => 'No invoice slips found!'];
            }
        } else {
            return ['status' => 1, 'error' => 'No Invoice Selected!'];

        }
    }

    public function add_bulk_shipment_adjustment_store(Request $request)
    {
        $names = [
            'tracking_number' => 'Tracking Number',
            'payable' => 'Payable',
            'adjustment_type_id' => 'Adjustment Type Id',
            'remarks' => 'Remarks',
        ];

        $messages = [
            'required' => ':attribute is Required.',
            'integer' => ':attribute must be an Integer.',
        ];

        $rules = [
            'tracking_number' => ['required', 'integer', Rule::exists('shipments', 'tracking_number')],
            'payable' => ['required', 'integer','not_in:0','min:-500000','max:500000'],
            'adjustment_type_id' => ['required', 'integer', Rule::exists('adjustment_types', 'id')->where(function($query) {
                $query->whereIn('id', [6, 7, 8, 9, 10, 11, 15, 16]);
                })],
            'remarks' => ['required'],
        ];

        $fields = [0 => 'tracking_number', 1 => 'payable', 2 => 'adjustment_type_id', 3 => 'remarks'];

        if ($file = $request->file('shipments')) {

            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

            $header = ['Tracking Number', 'Payable', 'Adjustment Type Id','Remarks'];
        }

        if (isset($spreadsheet)) {
            $header_correct = TRUE;

            foreach ($spreadsheet[0] as $index => $header_value) {
                if ($index == 1) {
                } elseif (!isset($header[$index]) || $header_value != $header[$index]) {
                    $header_correct = FALSE;
                    break;
                }
            }

            if (!$header_correct) {
                return redirect()->back()->with('error', 'Invalid Columns, Kindly follow the Template provided');
            } else {
                unset($spreadsheet[0]);
            }
        }

        if (!isset($spreadsheet) || !empty($spreadsheet)) {
            $rows = array();

            if (isset($spreadsheet)) {
                foreach ($spreadsheet as $spreadsheet_row) {
                    $row = array();

                    foreach ($spreadsheet_row as $key => $value) {
                        $row[$fields[$key]] = $value;
                    }

                    $rows[] = $row;
                }

                unset($spreadsheet);
            }
            foreach ($rows as $key => $row) {
                $row_id = $key + 2;

                $validate = Validator::make($row, $rules, $messages);

                $validate->setAttributeNames($names);

                if ($validate->fails()) {
                    $errors['Row #' . $row_id] = $validate->errors()->all();
                }
            }
            if (isset($errors)) {
                $errors = array_map(function ($row, $errors) {
                    return $row . ':' . PHP_EOL . implode(' | ', $errors);
                }, array_keys($errors), $errors);
                return redirect()->back()->withErrors($errors);
            } else {
                foreach ($rows as $key => $row) {

                    $tracking_number = (int)$row['tracking_number'];
                    $shipment = Shipment::where('tracking_number',$tracking_number)->first();
                    $payable = str_replace(',', '', $row['payable']);
                    $remarks = $row['remarks'];
                    $adjustment_type_id = (int)$row['adjustment_type_id'];
                    $this->add_adjustment($shipment->id, $payable, $remarks, $adjustment_type_id);

                    }
                }
                return redirect()->back()->with(['success' => count($rows) . ' Adjustment Added']);
            }

         else {
            return redirect()->back()->with('error', 'Invalid Tracking Numbers');
        }
    }
}