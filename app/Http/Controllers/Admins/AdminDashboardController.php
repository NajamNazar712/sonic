<?php

namespace App\Http\Controllers\Admins;

use Exception;
use Carbon\Carbon;
use App\FafCharges;
use App\Models\CityLog;
use App\RouteLocations;
use App\Http\Models\City;
use App\Http\Models\Zone;
use App\Http\Models\Rider;
use App\Http\Models\Route;
use App\Http\Models\Product;
use App\LeadProgressSetting;
use App\TerritoryTagHistory;
use CreateCityOsaRatesTable;
use Illuminate\Http\Request;
use App\Http\Models\CityArea;
use App\Http\Models\Province;
use App\Http\Models\Shipment;
use App\Models\ParentProduct;
use App\Http\Models\AdminLogs;
use App\Http\Models\BanksList;
use App\Http\Models\Reference;
use App\Http\Models\RouteType;
use App\Http\Models\PickupType;
use App\Http\Models\RateRemark;
use App\Http\Models\RateStatus;
use App\ZeroCodDiscountCharges;
use Illuminate\Validation\Rule;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\Fleet;
use App\Http\Models\BookingType;
use App\Http\Models\CityHistory;
use App\Http\Models\CityOsaRate;
use App\Http\Models\HR\Employee;
use App\Http\Models\SaleTierTag;
use App\Http\Traits\FilterTrait;
use Yajra\DataTables\DataTables;
use App\Http\Models\CityDelivery;
use App\Http\Models\DeliveryType;
use App\Http\Models\PaymentCycle;
use App\Http\Models\ReturnCharge;
use App\Http\Models\Shipper\User;
use App\Http\Models\ShippingMode;
use App\Http\Models\WalkInCities;
use App\Http\Models\WeightCharge;
use App\Jobs\CountFintechCharges;
use App\Models\AccountTaggingLog;
use App\Http\Models\Admin\Segment;
use App\Http\Models\DuplicateUser;
use App\Http\Models\EmployeeShift;
use App\Http\Models\FuelSurcharge;
use App\Http\Models\RiderCategory;
use App\Http\Models\ZoneClassCity;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Admin\AdminHub;
use App\Http\Models\DiscountCharge;
use App\Http\Models\InvoicingCycle;
use App\Http\Models\PendingPayment;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\ShipperContact;
use App\Models\CityStatusChangeLog;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Lead\Lead;
use App\Http\Models\Admin\Territory;
use App\Http\Models\InsuranceCharge;
use App\Http\Models\PackagingCharge;
use App\Jobs\MakeDynamicHubsMapping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Models\BusinessCategory;
use App\Http\Models\DwsWeightCharges;
use App\Http\Models\HR\StaffCategory;
use App\Http\Models\ShipmentsJourney;
use App\HistoryZeroCodDiscountCharges;
use App\Http\Models\HistorySmsCharges;
use App\Http\Models\HR\EmployeeGender;
use App\Http\Models\PendingSmsCharges;
use App\Http\Models\Rates\RateHistory;
use App\Http\Models\ReportingLocation;
use App\Http\Traits\RateReusableTrait;
use App\PendingZeroCodDiscountCharges;
use App\ShipmentReturnDiscountCharges;
use Illuminate\Support\Facades\Schema;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\Lead\LeadLog;
use App\Http\Models\BookingTypeCharges;
use App\Http\Models\CashHandlingCharge;
use App\Http\Models\SaleTierTagHistory;
use App\Http\Models\SubCategorySegment;
use App\Http\Models\WMS\WmsStorageType;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\CorporateZeroCodDiscountCharges;
use App\Http\Models\Admin\SalePersonTag;
use App\Http\Models\CorporateRateStatus;
use App\Http\Models\HR\EmployeeDomicile;
use App\Http\Models\HR\EmployeeReligion;
use App\Http\Models\NotificationSetting;
use App\Http\Models\Rates\RateOriginHub;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\AverageShipmentCycle;
use App\Http\Models\Commission\SalesTier;
use App\Http\Models\DiscountWeightCharge;
use App\Http\Models\Shipper\UserBankInfo;
use App\Http\Models\WMS\WmsPackingCharge;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Http\Models\Admin\UserCheckStatus;
use App\Http\Models\CorporateWeightCharge;
use App\Http\Models\HR\EmployeeBloodGroup;
use App\Http\Models\ShipmentPaymentStatus;
use App\Http\Models\CorporateFuelSurcharge;
use App\Http\Models\HR\EmployeeDesignation;
use App\Http\Models\PackagingMaterialTypes;
use App\Http\Models\PendingPaymentShipment;
use App\Http\Models\Shipper\SubstituteUser;
use App\Http\Models\UserDocumentAttachment;
use App\Http\Models\WMS\WmsLabellingCharge;
use App\Http\Models\WMS\WmsUserInformation;
use App\Http\Models\Admin\CorporateRateType;
use App\Http\Models\DwsWeightChargesHistory;
use App\Http\Models\PendingDwsWeightCharges;
use App\Http\Models\Rates\HistoryRateStatus;
use App\Http\Models\Rates\PendingRateStatus;
use App\Http\Models\WMS\WmsPerProductCharge;
use Illuminate\Database\Eloquent\Collection;
use App\HistoryShipmentReturnDiscountCharges;
use App\Http\Models\Admin\UserFintectCharges;
use App\Http\Models\HR\EmployeeMaritalStatus;
use App\Http\Models\Rates\RateDestinationHub;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\ShipperNotificationEmail;
use App\Http\Models\WMS\WmsStorageTypeCharge;
use App\PendingShipmentReturnDiscountCharges;
use App\Http\Models\Rates\HistoryReturnCharge;
use App\Http\Models\Rates\HistoryWeightCharge;
use App\Http\Models\Rates\PendingReturnCharge;
use App\Http\Models\Rates\PendingWeightCharge;
use App\CorporateShipmentReturnDiscountCharges;
use App\Http\Models\Admin\StandardReturnCharge;
use App\Http\Models\Admin\StandardWeightCharge;
use App\Http\Models\Commission\SalesCommission;
use App\Http\Models\CorporateDefaultRateStatus;
use App\Http\Models\NotificationSettingShipper;
use App\Http\Models\PackagingMaterialTypeSizes;
use App\Http\Models\Rates\HistoryFuelSurcharge;
use App\Http\Models\Rates\HistoryRateOriginHub;
use App\Http\Models\Rates\PendingFuelSurcharge;
use App\Http\Models\Rates\PendingRateOriginHub;
use App\Http\Models\WMS\WmsPerSquareFootCharge;
use App\Jobs\UserDisableBlockEmailNotification;
use App\PendingCorporateZeroCodDiscountCharges;
use App\Http\Models\Admin\StandardFuelSurcharge;
use App\Http\Models\CRM\CrmRequestStatusHistory;
use App\Http\Models\HistoryDiscountWeightCharge;
use App\Http\Models\PendingDiscountWeightCharge;
use App\Http\Models\Rates\HistoryDiscountCharge;
use App\Http\Models\Rates\PendingDiscountCharge;
use App\Http\Models\WMS\WmsHistoryPackingCharge;
use App\Http\Models\WMS\WmsPendingPackingCharge;
use App\Http\Controllers\NotificationsController;
use App\Http\Models\CorporateDefaultWeightCharge;
use App\Http\Models\Rates\HistoryInsuranceCharge;
use App\Http\Models\Rates\HistoryPackagingCharge;
use App\Http\Models\Rates\PendingInsuranceCharge;
use App\Http\Models\Rates\PendingPackagingCharge;
use App\Http\Models\Admin\ShipementReceiveDetails;
use App\Http\Models\Admin\ShipperInterceptExclude;
use App\Http\Models\Admin\StandardInsuranceCharge;
use App\Http\Models\Admin\StandardPackagingCharge;
use App\Http\Models\CorporateDefaultFuelSurcharge;
use App\Http\Models\CorporateWeightChargeZoneWise;
use App\Http\Models\InternationalUsersInformation;
use App\Http\Models\Operataions\OperationForecast;
use App\Http\Models\WMS\WmsHistoryLabellingCharge;
use App\Http\Models\WMS\WmsHistoryUserInformation;
use App\Http\Models\WMS\WmsPendingLabellingCharge;
use App\Http\Models\WMS\WmsPendingUserInformation;
use App\Http\Models\Admin\standard_fintech_charges;
use App\Http\Models\Commission\SalesCommissionUser;
use App\Http\Models\Rates\InternationalEconomyRate;
use App\Http\Models\WMS\WmsHistoryPerProductCharge;
use App\Http\Models\WMS\WmsPendingPerProductCharge;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\CargoManifest\V2Junctions;
use App\Http\Models\Admin\HistoryShipperBankAccount;
use App\Http\Models\Admin\StandardBookingTypeCharge;
use App\Http\Models\Rates\HistoryBookingTypeCharges;
use App\Http\Models\Rates\HistoryCashHandlingCharge;
use App\Http\Models\Rates\HistoryRateDestinationHub;
use App\Http\Models\Rates\PendingBookingTypeCharges;
use App\Http\Models\Rates\PendingCashHandlingCharge;
use App\Http\Models\Rates\PendingRateDestinationHub;
use App\Http\Models\WMS\WmsHistoryStorageTypeCharge;
use App\Http\Models\WMS\WmsPendingStorageTypeCharge;
use App\Http\Models\Admin\StandardCashHandlingCharge;
use App\Http\Models\Admin\WalkInStandardWeightCharge;
use App\Http\Models\Rates\HistoryCorporateRateStatus;
use App\Http\Models\Rates\PendingCorporateRateStatus;
use App\Http\Models\Shipper\SubstituteUserPermission;
use App\Http\Models\Sister_account\MergedAccountHead;
use App\Http\Models\CorporateDefaultHistoryRateStatus;
use App\Http\Models\PendingCorporateDefaultRateStatus;
use App\Http\Models\WMS\WmsHistoryPerSquareFootCharge;

use App\Http\Models\WMS\WmsPendingPerSquareFootCharge;
use App\PendingCorporateShipmentReturnDiscountCharges;
use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Models\Rates\HistoryCorporateWeightCharge;
use App\Http\Models\Sister_account\MergedSisterAccount;
use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Models\CorporateDefaultHistoryWeightCharge;
use App\Http\Models\Rates\HistoryCorporateFuelSurcharge;
use App\Http\Models\Admin\CargoManifest\V2JunctionRoutes;
use App\Http\Models\CorporateDefaultHistoryFuelSurcharge;
use App\Http\Models\HistoryCorporateWeightChargeZoneWise;
use App\Http\Models\Rates\InternationalEconomyRateStatus;
use App\Http\Models\Rates\MinimumChargeableWeightSetting;
use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Models\Admin\CargoManifest\V2JunctionMapping;
use App\Http\Controllers\Admins\DwsWeightChargesController;
use App\Http\Models\Admin\CargoManifest\V2JunctionVehicles;
use App\Http\Models\Admin\CorporateUserPackagingInvoiceLog;
use App\Http\Models\Commission\SalesCommissionExternalUser;
use App\Http\Models\Operataions\OperationForecastShipments;
use App\Http\Models\Shipper\SubstituteUserModulePermission;
use App\Http\Models\Survey\DisableAccountIntimationQuestion;
use App\Http\Models\Operataions\OperationForecastWeightRange;
use App\Http\Models\Sister_account\MergedSisterAccountMapping;
use App\Http\Models\Survey\DisableAccountIntimationSendSurvey;
use App\Http\Models\Operataions\OperationsOutgoingTopCustomers;
use App\Http\Models\Survey\DisableAccountIntimationSubmitSurvey;
use App\Http\Models\Operataions\OperationsOutgoingPickupRequests;
use App\Http\Models\Operataions\OperationsForecastLastUpdatedTime;
use App\Http\Models\Operataions\OperationsOutgoingTopCustomersShipments;
use App\Http\Models\Operataions\OperationsOutgoingPickupRequestShipments;
use App\Http\Models\Sister_account\Substitute_user\SubstituteUserMergeSisterAccountMapping;

class AdminDashboardController extends Controller
{   use RateReusableTrait,FilterTrait;

    public function __construct()
    {   $this->middleware('auth:admin');
        $this->middleware('Permission');
    }
    
    static $paymentCycleDays = [
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
    ];

    public function index()
    {
         return view('admin.simple_dashboard');
    }

    public function user_fintech_charges(Request $req){
        $UserFintectCharges = new UserFintectCharges();
        $values =  $UserFintectCharges::where('user_id',$req->userID)->where('status','1')->first();
        if(!empty($values)){
            return response()->json([
                'status' => '200',
                'data'   => $values,
            ]);
        }
        else{
            return response()->json([
                'status' => '404',
                'data'   => '',
            ]);
        }
    }

    public function add_fintech_charges(Request $req){
        $UserFintectCharges = new UserFintectCharges();
        $standard_fintech_charges = standard_fintech_charges::find(1);

        if(!empty($standard_fintech_charges)){
            if($req->checkboxval == 'false'){
                $UserFintectCharges::where('user_id',$req->userID)->update([
                    'status'     => 2,
                    'updated_by' => session('id')
                ]); 

                return response()->json([
                    'status'  => '200',
                    'message' => 'Fintech Charges Updated Successfully!',
                ]);
            }
            if($req->checkboxval == 'true'){
                if($standard_fintech_charges->standard_fintech_charges > $req->fintechCharges){
                    return response()->json([
                        'status'  => '401',
                        'message' => 'Shipper Fintech Charges Should be Greater then standard Fintech charges',
                    ]);
                }
                try{
                    $value =  $UserFintectCharges::where('user_id',$req->userID)->first();
                    if(!empty($value->user_id)){
                        $UserFintectCharges::where('user_id',$req->userID)->update([
                            'fintech_charges'  => $req->fintechCharges,
                            'status'           => '1',
                            'updated_by'       => session('id')
                        ]); 
                    }
                    else{
                        $UserFintectCharges->user_id            = $req->userID;
                        $UserFintectCharges->fintech_charges    = $req->fintechCharges;
                        $UserFintectCharges->added_by           = session('id');
                        $UserFintectCharges->updated_by         = session('id');
                        $UserFintectCharges->save();
                    }
                        return response()->json([
                            'status'  => '200',
                            'message' => 'Fintech Charges Updated Successfully!',
                        ]);
                }
                catch(exception $e){
                    return response()->json([
                        'message' => 'Charges Not Set',
                    ]); 
                }  
            }
        }
        else{
            return response()->json([
                'status'  => '401',
                'message' => 'First Set Standard Fintech Charges then user Charges',
            ]);
        }
    }
    public function statistics_search(Request $request)
    {
        $graph = array();
        $destination_id = $request->destination;
        $shipper = $request->shipper;
        $current_date = $request->current_date;
        $old_date = $request->old_date;
        $date = $old_date;
        $dates = array();
        $dates[] = $date;
        while ($date != $current_date) {
            $date = date('Y-m-d H:i:s', strtotime($date . ' +1 day'));
            $dates[] = $date;
        }

        if (($destination_id != '') && ($shipper != '')) {
            foreach ($dates as $this_date) {
                $comparison_date = $this_date;
                $graph['dates'][] = Carbon::parse($this_date)->format('d M');

                $booked               = Shipment::whereDate('created_at', $comparison_date)->where(['user_id' => $shipper, 'consignee_city_id' => $destination_id, 'shipper_status_id' => 1]);
                $arrived              = Shipment::whereDate('created_at', $comparison_date)->where(['user_id' => $shipper, 'consignee_city_id' => $destination_id])->where('shipper_status_id', 2);
                $in_transit           = Shipment::whereDate('created_at', $comparison_date)->where(['user_id' => $shipper, 'consignee_city_id' => $destination_id])->where('shipper_status_id', 3);
                $canceled             = Shipment::whereDate('created_at', $comparison_date)->where(['user_id' => $shipper, 'consignee_city_id' => $destination_id])->where('shipper_status_id', 17);
                $destination          = Shipment::whereDate('created_at', $comparison_date)->where(['user_id' => $shipper, 'consignee_city_id' => $destination_id])->where('shipper_status_id', 4);
                $out_for_delivery     = Shipment::whereDate('created_at', $comparison_date)->where(['user_id' => $shipper, 'consignee_city_id' => $destination_id])->where('shipper_status_id', 5);
                $return_confirm       = Shipment::whereDate('created_at', $comparison_date)->where(['user_id' => $shipper, 'consignee_city_id' => $destination_id])->where('shipper_status_id', 20);
                $return_delivered     = Shipment::whereDate('created_at', $comparison_date)->where(['user_id' => $shipper, 'consignee_city_id' => $destination_id])->where('shipper_status_id', 25);
                $pending_shipments    = Shipment::whereDate('created_at', $comparison_date)->where(['user_id' => $shipper, 'consignee_city_id' => $destination_id])->whereIn('shipper_status_id', [6, 7, 8, 9, 13, 15, 18, 51, 52, 56]);
                $pending_return       = Shipment::whereDate('created_at', $comparison_date)->where(['user_id' => $shipper, 'consignee_city_id' => $destination_id])->whereIn('shipper_status_id', [21, 22, 23, 24, 26, 27, 28, 29, 57, 60]);
                $confirmation_pending = Shipment::whereDate('created_at', $comparison_date)->where(['user_id' => $shipper, 'consignee_city_id' => $destination_id])->whereIn('shipper_status_id', [12, 54, 55]);
                $delivered            = Shipment::whereDate('created_at', $comparison_date)->where(['user_id' => $shipper, 'consignee_city_id' => $destination_id])->whereIn('shipper_status_id', [14, 16, 30, 36, 37, 39, 40, 41, 47]);
//                $complaints_launched = CrmRequestStatusHistory::join('crm_requests as cr', 'cr.id', '=', 'crm_request_status_histories.crm_request_id')->where('crm_request_status_histories.status_id', 1)->where('cr.shipper_id', $shipper)->whereDate('crm_request_status_histories.created_at', $comparison_date);
//                $complaints_in_process = CrmRequestStatusHistory::join('crm_requests as cr', 'cr.id', '=', 'crm_request_status_histories.crm_request_id')->where('crm_request_status_histories.status_id', 2)->where('cr.shipper_id', $shipper)->whereDate('crm_request_status_histories.created_at', $comparison_date);
//                $complaints_closed = CrmRequestStatusHistory::join('crm_requests as cr', 'cr.id', '=', 'crm_request_status_histories.crm_request_id')->where('crm_request_status_histories.status_id', 4)->where('cr.shipper_id', $shipper)->whereDate('crm_request_status_histories.created_at', $comparison_date);
//                $complaints_rejected = CrmRequestStatusHistory::join('crm_requests as cr', 'cr.id', '=', 'crm_request_status_histories.crm_request_id')->where('crm_request_status_histories.status_id', 7)->where('cr.shipper_id', $shipper)->whereDate('crm_request_status_histories.created_at', $comparison_date);
                if (session('role_id') != 1) {
                    $booked = $booked->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $arrived = $arrived->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $in_transit = $in_transit->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $canceled = $canceled->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $delivered = $delivered->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $destination = $destination->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $out_for_delivery = $out_for_delivery->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $return_confirm = $return_confirm->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $return_delivered = $return_delivered->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $pending_shipments = $pending_shipments->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $confirmation_pending = $confirmation_pending->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $pending_return = $pending_return->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                }

                $graph['booked'][]               = $booked->count();
                $graph['arrived'][]              = $arrived->count();
                $graph['in_transit'][]           = $in_transit->count();
                $graph['canceled'][]             = $canceled->count();
                $graph['delivered'][]            = $delivered->count();
                $graph['destination'][]          = $destination->count();
                $graph['out_for_delivery'][]     = $out_for_delivery->count();
                $graph['return_confirm'][]       = $return_confirm->count();
                $graph['return_delivered'][]     = $return_delivered->count();
                $graph['pending_shipments'][]    = $pending_shipments->count();
                $graph['confirmation_pending'][] = $confirmation_pending->count();
                $graph['pending_return'][]       = $pending_return->count();
//                $graph['complaints_launched'][] = $complaints_launched->count();
//                $graph['complaints_in_process'][]  = $complaints_in_process->count();
//                $graph['complaints_closed'][] = $complaints_closed->count();
//                $graph['complaints_rejected'][] = $complaints_rejected->count();
            }
        } else if (($destination_id == '') && ($shipper != '')) {
            foreach ($dates as $this_date) {
                $comparison_date = $this_date;
                $graph['dates'][] = Carbon::parse($this_date)->format('d M');

                $booked = Shipment::whereDate('created_at', $comparison_date)->where('user_id', $shipper)->where('shipper_status_id', 1);
                $arrived = Shipment::whereDate('created_at', $comparison_date)->where('user_id', $shipper)->where('shipper_status_id', 2);
                $in_transit = Shipment::whereDate('created_at', $comparison_date)->where('user_id', $shipper)->where('shipper_status_id', 3);
                $canceled = Shipment::whereDate('created_at', $comparison_date)->where('user_id', $shipper)->where('shipper_status_id', 17);
                $destination = Shipment::whereDate('created_at', $comparison_date)->where('user_id', $shipper)->where('shipper_status_id', 4);
                $out_for_delivery = Shipment::whereDate('created_at', $comparison_date)->where('user_id', $shipper)->where('shipper_status_id', 5);
                $return_confirm = Shipment::whereDate('created_at', $comparison_date)->where('user_id', $shipper)->where('shipper_status_id', 20);
                $return_delivered = Shipment::whereDate('created_at', $comparison_date)->where('user_id', $shipper)->where('shipper_status_id', 25);
                $pending_shipments = Shipment::whereDate('created_at', $comparison_date)->where('user_id', $shipper)->whereIn('shipper_status_id', [6, 7, 8, 9, 13, 15, 18, 51, 52, 56]);
                $pending_return = Shipment::whereDate('created_at', $comparison_date)->where('user_id', $shipper)->whereIn('shipper_status_id', [21, 22, 23, 24, 26, 27, 28, 29, 57, 60]);
                $confirmation_pending = Shipment::whereDate('created_at', $comparison_date)->where('user_id', $shipper)->whereIn('shipper_status_id', [12, 54, 55]);
                $delivered = Shipment::whereDate('created_at', $comparison_date)->where('user_id', $shipper)->whereIn('shipper_status_id', [14, 16, 30, 36, 37, 39, 40, 41, 47]);
//                $complaints_launched = CrmRequestStatusHistory::join('crm_requests as cr', 'cr.id', '=', 'crm_request_status_histories.crm_request_id')->where('crm_request_status_histories.status_id', 1)->where('cr.shipper_id', $shipper)->whereDate('crm_request_status_histories.created_at', $comparison_date);
//                $complaints_in_process = CrmRequestStatusHistory::join('crm_requests as cr', 'cr.id', '=', 'crm_request_status_histories.crm_request_id')->where('crm_request_status_histories.status_id', 2)->where('cr.shipper_id', $shipper)->whereDate('crm_request_status_histories.created_at', $comparison_date);
//                $complaints_closed = CrmRequestStatusHistory::join('crm_requests as cr', 'cr.id', '=', 'crm_request_status_histories.crm_request_id')->where('crm_request_status_histories.status_id', 4)->where('cr.shipper_id', $shipper)->whereDate('crm_request_status_histories.created_at', $comparison_date);
//                $complaints_rejected = CrmRequestStatusHistory::join('crm_requests as cr', 'cr.id', '=', 'crm_request_status_histories.crm_request_id')->where('crm_request_status_histories.status_id', 7)->where('cr.shipper_id', $shipper)->whereDate('crm_request_status_histories.created_at', $comparison_date);

                if (session('role_id') != 1) {
                    $booked = $booked->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $arrived = $arrived->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $in_transit = $in_transit->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $canceled = $canceled->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $delivered = $delivered->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $destination = $destination->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $out_for_delivery = $out_for_delivery->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $return_confirm = $return_confirm->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $return_delivered = $return_delivered->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $pending_shipments = $pending_shipments->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $confirmation_pending = $confirmation_pending->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $pending_return = $pending_return->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                }

                $graph['booked'][] = $booked->count();
                $graph['arrived'][] = $arrived->count();
                $graph['in_transit'][] = $in_transit->count();
                $graph['canceled'][] = $canceled->count();
                $graph['delivered'][] = $delivered->count();
                $graph['destination'][] = $destination->count();
                $graph['out_for_delivery'][] = $out_for_delivery->count();
                $graph['return_confirm'][] = $return_confirm->count();
                $graph['return_delivered'][] = $return_delivered->count();
                $graph['pending_shipments'][] = $pending_shipments->count();
                $graph['confirmation_pending'][] = $confirmation_pending->count();
                $graph['pending_return'][] = $pending_return->count();
//                $graph['complaints_launched'][] = $complaints_launched->count();
//                $graph['complaints_in_process'][]  = $complaints_in_process->count();
//                $graph['complaints_closed'][] = $complaints_closed->count();
//                $graph['complaints_rejected'][] = $complaints_rejected->count();
            }
        } else if (($destination_id != '') && ($shipper == '')) {
            foreach ($dates as $this_date) {
                $comparison_date = $this_date;
                $graph['dates'][] = Carbon::parse($this_date)->format('d M');

                $booked = Shipment::whereDate('created_at', $comparison_date)->where(['consignee_city_id' => $destination_id])->where('shipper_status_id', 1);
                $arrived = Shipment::whereDate('created_at', $comparison_date)->where(['consignee_city_id' => $destination_id])->where('shipper_status_id', 2);
                $in_transit = Shipment::whereDate('created_at', $comparison_date)->where(['consignee_city_id' => $destination_id])->where('shipper_status_id', 3);
                $canceled = Shipment::whereDate('created_at', $comparison_date)->where(['consignee_city_id' => $destination_id])->where('shipper_status_id', 17);
                $destination = Shipment::whereDate('created_at', $comparison_date)->where(['consignee_city_id' => $destination_id])->where('shipper_status_id', 4);
                $out_for_delivery = Shipment::whereDate('created_at', $comparison_date)->where(['consignee_city_id' => $destination_id])->where('shipper_status_id', 5);
                $return_confirm = Shipment::whereDate('created_at', $comparison_date)->where(['consignee_city_id' => $destination_id])->where('shipper_status_id', 20);
                $return_delivered = Shipment::whereDate('created_at', $comparison_date)->where(['consignee_city_id' => $destination_id])->where('shipper_status_id', 25);
                $pending_shipments = Shipment::whereDate('created_at', $comparison_date)->where(['consignee_city_id' => $destination_id])->whereIn('shipper_status_id', [6, 7, 8, 9, 13, 15, 18, 51, 52, 56]);
                $pending_return = Shipment::whereDate('created_at', $comparison_date)->where(['consignee_city_id' => $destination_id])->whereIn('shipper_status_id', [21, 22, 23, 24, 26, 27, 28, 29, 57, 60]);
                $confirmation_pending = Shipment::whereDate('created_at', $comparison_date)->where(['consignee_city_id' => $destination_id])->whereIn('shipper_status_id', [12, 54, 55]);
                $delivered = Shipment::whereDate('created_at', $comparison_date)->where(['consignee_city_id' => $destination_id])->whereIn('shipper_status_id', [14, 16, 30, 36, 37, 39, 40, 41, 47]);
//                $complaints_launched = CrmRequestStatusHistory::join('crm_requests as cr', 'cr.id', '=', 'crm_request_status_histories.crm_request_id')->where('crm_request_status_histories.status_id', 1)->whereDate('crm_request_status_histories.created_at', $comparison_date);
//                $complaints_in_process = CrmRequestStatusHistory::join('crm_requests as cr', 'cr.id', '=', 'crm_request_status_histories.crm_request_id')->where('crm_request_status_histories.status_id', 2)->whereDate('crm_request_status_histories.created_at', $comparison_date);
//                $complaints_closed = CrmRequestStatusHistory::join('crm_requests as cr', 'cr.id', '=', 'crm_request_status_histories.crm_request_id')->where('crm_request_status_histories.status_id', 4)->whereDate('crm_request_status_histories.created_at', $comparison_date);
//                $complaints_rejected = CrmRequestStatusHistory::join('crm_requests as cr', 'cr.id', '=', 'crm_request_status_histories.crm_request_id')->where('crm_request_status_histories.status_id', 7)->whereDate('crm_request_status_histories.created_at', $comparison_date);

                if (session('role_id') != 1) {
                    $booked = $booked->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $arrived = $arrived->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $in_transit = $in_transit->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $canceled = $canceled->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $delivered = $delivered->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $destination = $destination->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $out_for_delivery = $out_for_delivery->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $return_confirm = $return_confirm->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $return_delivered = $return_delivered->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $pending_shipments = $pending_shipments->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $confirmation_pending = $confirmation_pending->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $pending_return = $pending_return->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                }

                $graph['booked'][] = $booked->count();
                $graph['arrived'][] = $arrived->count();
                $graph['in_transit'][] = $in_transit->count();
                $graph['canceled'][] = $canceled->count();
                $graph['delivered'][] = $delivered->count();
                $graph['destination'][] = $destination->count();
                $graph['out_for_delivery'][] = $out_for_delivery->count();
                $graph['return_confirm'][] = $return_confirm->count();
                $graph['return_delivered'][] = $return_delivered->count();
                $graph['pending_shipments'][] = $pending_shipments->count();
                $graph['confirmation_pending'][] = $confirmation_pending->count();
                $graph['pending_return'][] = $pending_return->count();
//                $graph['complaints_launched'][] = $complaints_launched->count();
//                $graph['complaints_in_process'][]  = $complaints_in_process->count();
//                $graph['complaints_closed'][] = $complaints_closed->count();
//                $graph['complaints_rejected'][] = $complaints_rejected->count();
            }
        } else {
            foreach ($dates as $this_date) {
                $comparison_date = $this_date;
                $graph['dates'][] = Carbon::parse($this_date)->format('d M');

                $booked = Shipment::whereDate('created_at', $comparison_date)->where('shipper_status_id', 1);
                $arrived = Shipment::whereDate('created_at', $comparison_date)->where('shipper_status_id', 2);
                $in_transit = Shipment::whereDate('created_at', $comparison_date)->where('shipper_status_id', 3);
                $canceled = Shipment::whereDate('created_at', $comparison_date)->where('shipper_status_id', 17);
                $destination = Shipment::whereDate('created_at', $comparison_date)->where('shipper_status_id', 4);
                $out_for_delivery = Shipment::whereDate('created_at', $comparison_date)->where('shipper_status_id', 5);
                $return_confirm = Shipment::whereDate('created_at', $comparison_date)->where('shipper_status_id', 20);
                $return_delivered = Shipment::whereDate('created_at', $comparison_date)->where('shipper_status_id', 25);
                $pending_shipments = Shipment::whereDate('created_at', $comparison_date)->whereIn('shipper_status_id', [6, 7, 8, 9, 13, 15, 18, 51, 52, 56]);
                $pending_return = Shipment::whereDate('created_at', $comparison_date)->whereIn('shipper_status_id', [21, 22, 23, 24, 26, 27, 28, 29, 57, 60]);
                $confirmation_pending = Shipment::whereDate('created_at', $comparison_date)->whereIn('shipper_status_id', [12, 54, 55]);
                $delivered = Shipment::whereDate('created_at', $comparison_date)->whereIn('shipper_status_id', [14, 16, 30, 36, 37, 39, 40, 41, 47]);
//                $complaints_launched = CrmRequestStatusHistory::join('crm_requests as cr', 'cr.id', '=', 'crm_request_status_histories.crm_request_id')->where('crm_request_status_histories.status_id', 1)->whereDate('crm_request_status_histories.created_at', $comparison_date);
//                $complaints_in_process = CrmRequestStatusHistory::join('crm_requests as cr', 'cr.id', '=', 'crm_request_status_histories.crm_request_id')->where('crm_request_status_histories.status_id', 2)->whereDate('crm_request_status_histories.created_at', $comparison_date);
//                $complaints_closed = CrmRequestStatusHistory::join('crm_requests as cr', 'cr.id', '=', 'crm_request_status_histories.crm_request_id')->where('crm_request_status_histories.status_id', 4)->whereDate('crm_request_status_histories.created_at', $comparison_date);
//                $complaints_rejected = CrmRequestStatusHistory::join('crm_requests as cr', 'cr.id', '=', 'crm_request_status_histories.crm_request_id')->where('crm_request_status_histories.status_id', 7)->whereDate('crm_request_status_histories.created_at', $comparison_date);

                if (session('role_id') != 1) {
                    $booked = $booked->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $arrived = $arrived->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $in_transit = $in_transit->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $canceled = $canceled->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $delivered = $delivered->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $destination = $destination->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $out_for_delivery = $out_for_delivery->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $return_confirm = $return_confirm->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $return_delivered = $return_delivered->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $pending_shipments = $pending_shipments->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $confirmation_pending = $confirmation_pending->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $pending_return = $pending_return->where(function ($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                }

                $graph['booked'][] = $booked->count();
                $graph['arrived'][] = $arrived->count();
                $graph['in_transit'][] = $in_transit->count();
                $graph['canceled'][] = $canceled->count();
                $graph['delivered'][] = $delivered->count();
                $graph['destination'] = $destination->count();
                $graph['out_for_delivery'] = $out_for_delivery->count();
                $graph['return_confirm'] = $return_confirm->count();
                $graph['return_delivered'] = $return_delivered->count();
                $graph['pending_shipments'] = $pending_shipments->count();
                $graph['confirmation_pending'] = $confirmation_pending->count();
                $graph['pending_return'] = $pending_return->count();
//                $graph['complaints_launched'][] = $complaints_launched->count();
//                $graph['complaints_in_process'][]  = $complaints_in_process->count();
//                $graph['complaints_closed'][] = $complaints_closed->count();
//                $graph['complaints_rejected'][] = $complaints_rejected->count();
            }
        }


        return response()->json(['status' => 1, 'graph' => $graph]);
    }


    public function operation_forecast_search(Request $request)
    {
        if (($request->get('search_date_from') && $request->get('search_date_to'))) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
        } else {
            $from = Carbon::now()->subDays(29);
            $to = Carbon::now();
        }
        if ($request->get('search_hub')) {
            $hub = $request->get('search_hub');
        } else {
            $admin = Admin::where('id', Auth::id())->first();
            $hub = $admin->default_hub_id;
        }
        if ($request->get('search_service_type')) {
            $service_type_id = $request->get('search_service_type');
        } else {
            $service_type_id = 1;
        }

        $today = Carbon::now()->endOfDay();
        $thirtyDays = Carbon::now()->subDays(29)->startOfDay();
        //incoming
        $doughnut_chart_shipments_count['booked'] = OperationForecast::where('shipper_status_id', 1)->where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at', [$from, $to])->sum('operation_forecasts.count');
        $doughnut_chart_shipments_count['arrived_at_origin'] = OperationForecast::where('shipper_status_id', 2)->where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at', [$from, $to])->sum('operation_forecasts.count');
        $doughnut_chart_shipments_count['in_transit'] = OperationForecast::where('shipper_status_id', 3)->where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at', [$from, $to])->sum('operation_forecasts.count');
        $doughnut_chart_shipments_count['arrived_at_destination'] = OperationForecast::where('shipper_status_id', 4)->where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at', [$from, $to])->sum('operation_forecasts.count');
        $doughnut_chart_shipments_count['not_attempted'] = OperationForecast::where('shipper_status_id', 7)->where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at', [$from, $to])->sum('operation_forecasts.count');
        $doughnut_chart_shipments_count['delivery_unsuccessful'] = OperationForecast::where('shipper_status_id', 8)->where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at', [$from, $to])->sum('operation_forecasts.count');
        $doughnut_chart_shipments_count['on_hold'] = OperationForecast::where('shipper_status_id', 9)->where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at', [$from, $to])->sum('operation_forecasts.count');
        $doughnut_chart_shipments_count['total'] = $doughnut_chart_shipments_count['booked'] + $doughnut_chart_shipments_count['arrived_at_origin'] + $doughnut_chart_shipments_count['in_transit'] + $doughnut_chart_shipments_count['arrived_at_destination'] + $doughnut_chart_shipments_count['not_attempted'] + $doughnut_chart_shipments_count['delivery_unsuccessful'] + $doughnut_chart_shipments_count['on_hold'];

        $incoming_bar_chart_shipments['one'] = OperationForecastShipments::where('weight_range_id', 1)->where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at', [$from, $to])->count();
        $incoming_bar_chart_shipments['two'] = OperationForecastShipments::where('weight_range_id', 2)->where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at', [$from, $to])->count();
        $incoming_bar_chart_shipments['three'] = OperationForecastShipments::where('weight_range_id', 3)->where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at', [$from, $to])->count();
        $incoming_bar_chart_shipments['four'] = OperationForecastShipments::where('weight_range_id', 4)->where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at', [$from, $to])->count();


        $riders_count = Rider::where('status', 1)->where('city_id', $hub)->count();
        $sixtyDays = Carbon::now()->subDays(58)->startOfDay();
        if ($riders_count == 0) {
            $per_rider_loads = ceil(($incoming_bar_chart_shipments['one'] + $incoming_bar_chart_shipments['two'] + $incoming_bar_chart_shipments['three'] + $incoming_bar_chart_shipments['four']));
        } else {
            $per_rider_loads = ceil(($incoming_bar_chart_shipments['one'] + $incoming_bar_chart_shipments['two'] + $incoming_bar_chart_shipments['three'] + $incoming_bar_chart_shipments['four']) / $riders_count);
        }
        $light_deliveries = ($incoming_bar_chart_shipments['one'] + $incoming_bar_chart_shipments['two']);
        $heavy_deliveries = ($incoming_bar_chart_shipments['three'] + $incoming_bar_chart_shipments['four']);

        $day_wise_growth_thirty = OperationForecast::where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at', [$thirtyDays, $today])->sum('operation_forecasts.count');
        $day_wise_growth_sixty = OperationForecast::where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at', [$sixtyDays, $thirtyDays])->sum('operation_forecasts.count');
        if ($day_wise_growth_sixty == 0) {
            $day_wise_growth_percentage = 0;
        } else {
            $day_wise_growth = ($day_wise_growth_thirty - $day_wise_growth_sixty) / $day_wise_growth_sixty;
            $day_wise_growth_percentage = number_format($day_wise_growth * 100, 1);
        }
        $operation_incoming['per_rider_loads'] = $per_rider_loads;
        $operation_incoming['day_wise_growth'] = $day_wise_growth_percentage . '%';
        $operation_incoming['heavy_deliveries'] = $heavy_deliveries;
        $operation_incoming['light_deliveries'] = $light_deliveries;

        //outgoing
        $operation_outgoing_pickups['no_of_shipments'] = OperationsOutgoingPickupRequests::where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at', [$thirtyDays, $today])->sum('operations_outgoing_pickup_requests.shipments_count');
        $operation_outgoing_pickups['pickups_count'] = OperationsOutgoingPickupRequests::select(DB::raw('count(operations_outgoing_pickup_requests.id) as count'))->where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at', [$thirtyDays, $today])->groupBy('operations_outgoing_pickup_requests.pickup_request_id')->get();
        $operation_outgoing_pickups['pickups'] = 0;
        foreach ($operation_outgoing_pickups['pickups_count'] as $pickups_count) {
            $operation_outgoing_pickups['pickups'] = $operation_outgoing_pickups['pickups'] + $pickups_count->count;
        }

        $outgoing_top_five_customers = OperationsOutgoingTopCustomers::leftjoin('users as u', 'u.id', '=', 'operations_outgoing_top_customers.user_id')->select('u.name as name', DB::raw('(SELECT SUM(shipments_count) FROM operations_outgoing_top_customers AS ootc WHERE ootc.user_id = operations_outgoing_top_customers.user_id AND updated_at BETWEEN "' . $from . '" AND "' . $to . '") AS count'))
            ->whereBetween('operations_outgoing_top_customers.created_at', [$from, $to])
            ->orderBy('count', 'desc')
            ->groupBy('u.id')
            ->take(5)->get()->toArray();
        if (array_key_exists(0, $outgoing_top_five_customers)) {
            $outgoing_doughnut_top_five_customers['first'] = $outgoing_top_five_customers[0];
        } else {
            $outgoing_doughnut_top_five_customers['first']['name'] = '-';
            $outgoing_doughnut_top_five_customers['first']['count'] = 0;
        }
        if (array_key_exists(1, $outgoing_top_five_customers)) {
            $outgoing_doughnut_top_five_customers['second'] = $outgoing_top_five_customers[1];
        } else {
            $outgoing_doughnut_top_five_customers['second']['name'] = '-';
            $outgoing_doughnut_top_five_customers['second']['count'] = 0;
        }
        if (array_key_exists(2, $outgoing_top_five_customers)) {
            $outgoing_doughnut_top_five_customers['third'] = $outgoing_top_five_customers[2];
        } else {
            $outgoing_doughnut_top_five_customers['third']['name'] = '-';
            $outgoing_doughnut_top_five_customers['third']['count'] = 0;
        }
        if (array_key_exists(3, $outgoing_top_five_customers)) {
            $outgoing_doughnut_top_five_customers['fourth'] = $outgoing_top_five_customers[3];
        } else {
            $outgoing_doughnut_top_five_customers['fourth']['name'] = '-';
            $outgoing_doughnut_top_five_customers['fourth']['count'] = 0;
        }
        if (array_key_exists(4, $outgoing_top_five_customers)) {
            $outgoing_doughnut_top_five_customers['fifth'] = $outgoing_top_five_customers[4];
        } else {
            $outgoing_doughnut_top_five_customers['fifth']['name'] = '-';
            $outgoing_doughnut_top_five_customers['fifth']['count'] = 0;
        }
        $outgoing_doughnut_top_five_customers['total'] = $outgoing_doughnut_top_five_customers['first']['count'] + $outgoing_doughnut_top_five_customers['second']['count'] + $outgoing_doughnut_top_five_customers['third']['count'] + $outgoing_doughnut_top_five_customers['fourth']['count'] + $outgoing_doughnut_top_five_customers['fifth']['count'];

        $outgoing_bar_chart_shipments['one'] = OperationsOutgoingPickupRequestShipments::where('weight_range_id', 1)->where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at', [$from, $to])->count();
        $outgoing_bar_chart_shipments['two'] = OperationsOutgoingPickupRequestShipments::where('weight_range_id', 2)->where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at', [$from, $to])->count();
        $outgoing_bar_chart_shipments['three'] = OperationsOutgoingPickupRequestShipments::where('weight_range_id', 3)->where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at', [$from, $to])->count();
        $outgoing_bar_chart_shipments['four'] = OperationsOutgoingPickupRequestShipments::where('weight_range_id', 4)->where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at', [$from, $to])->count();


        if ($riders_count == 0) {
            $outgoing_per_rider_loads = ceil(($outgoing_bar_chart_shipments['one'] + $outgoing_bar_chart_shipments['two'] + $outgoing_bar_chart_shipments['three'] + $outgoing_bar_chart_shipments['four']));
        } else {
            $outgoing_per_rider_loads = ceil(($outgoing_bar_chart_shipments['one'] + $outgoing_bar_chart_shipments['two'] + $outgoing_bar_chart_shipments['three'] + $outgoing_bar_chart_shipments['four']) / $riders_count);
        }
        $outgoing_light_deliveries = ($outgoing_bar_chart_shipments['one'] + $outgoing_bar_chart_shipments['two']);
        $outgoing_heavy_deliveries = ($outgoing_bar_chart_shipments['three'] + $outgoing_bar_chart_shipments['four']);

        $outgoing_day_wise_growth_thirty = OperationsOutgoingPickupRequests::where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at', [$thirtyDays, $today])->sum('operations_outgoing_pickup_requests.shipments_count');
        $outgoing_day_wise_growth_sixty = OperationsOutgoingPickupRequests::where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at', [$sixtyDays, $thirtyDays])->sum('operations_outgoing_pickup_requests.shipments_count');
        if ($outgoing_day_wise_growth_sixty == 0) {
            $outgoing_day_wise_growth_percentage = 0;
        } else {
            $outgoing_day_wise_growth = ($outgoing_day_wise_growth_thirty - $outgoing_day_wise_growth_sixty) / $outgoing_day_wise_growth_sixty;
            $outgoing_day_wise_growth_percentage = number_format($outgoing_day_wise_growth * 100, 1);
        }
        $operation_outgoing['per_rider_loads'] = $outgoing_per_rider_loads;
        $operation_outgoing['day_wise_growth'] = $outgoing_day_wise_growth_percentage . '%';
        $operation_outgoing['heavy_deliveries'] = $outgoing_heavy_deliveries;
        $operation_outgoing['light_deliveries'] = $outgoing_light_deliveries;

        return response()->json(['status' => 1, 'doughnut_chart_shipments_count' => $doughnut_chart_shipments_count, 'incoming_bar_chart_shipments' => $incoming_bar_chart_shipments, 'operation_incoming' => $operation_incoming, 'operation_outgoing_pickups' => $operation_outgoing_pickups, 'outgoing_doughnut_top_five_customers' => $outgoing_doughnut_top_five_customers, 'outgoing_bar_chart_shipments' => $outgoing_bar_chart_shipments, 'operation_outgoing' => $operation_outgoing]);
    }

    public function incoming_list(Request $request)
    {
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
        } else {
            $from = Carbon::now()->subDays(29);
            $to = Carbon::now()->endOfDay();
        }
        if ($request->get('search_hub')) {
            $hub = $request->get('search_hub');
        } else {
            $admin = Admin::where('id', Auth::id())->first();
            $hub = $admin->default_hub_id;
        }
        if ($request->get('search_service_type')) {
            $service_type_id = $request->get('search_service_type');
        } else {
            $service_type_id = 1;
        }
        $operation_incoming = OperationForecast::leftjoin('shipment_status as ss', 'ss.id', '=', 'operation_forecasts.shipper_status_id')
            ->select('operation_forecasts.id as opfs_id', 'ss.id as shipper_status_id', 'ss.name as status', DB::raw('(SELECT SUM(count) FROM operation_forecasts AS opfs WHERE opfs.shipper_status_id = operation_forecasts.shipper_status_id AND opfs.hub_id = "' . $hub . '" AND opfs.booking_type_id = "' . $service_type_id . '" AND updated_at BETWEEN "' . $from . '" AND "' . $to . '") AS count'))
            ->whereIn('shipper_status_id', [1, 2, 3, 4, 5, 7, 8, 9])
            ->where('operation_forecasts.hub_id', $hub)
            ->where('operation_forecasts.booking_type_id', $service_type_id)
            ->whereBetween('operation_forecasts.updated_at', [$from, $to])
            ->groupBy('shipper_status_id')
            ->orderBy('shipper_status_id', 'asc');
        $datatable = Datatables::of($operation_incoming)
            ->setRowAttr([
                'class' => function ($statuses) {
                    if ($statuses->shipper_status_id == 1) {
                        return 'statusBooked';
                    } else if ($statuses->shipper_status_id == 2) {
                        return 'statusOrigin';
                    } else if ($statuses->shipper_status_id == 3) {
                        return 'statusIntransit';
                    } else if ($statuses->shipper_status_id == 4) {
                        return 'statusDestination';
                    } else if ($statuses->shipper_status_id == 7) {
                        return 'statusNotattempted';
                    } else if ($statuses->shipper_status_id == 8) {
                        return 'statusDeliveryunsuccessful';
                    } else if ($statuses->shipper_status_id == 9) {
                        return 'statusOnhold';
                    }
                }
            ])
            ->editColumn('count_link', function ($shipments) use ($from, $to, $service_type_id, $hub) {
                if ($shipments->count > 0) {
                    $route = route('admin.operation_forecasting.incoming.shipments_list');
                    return "<u><a href='{$route}/$from/$to/$service_type_id/$hub/$shipments->shipper_status_id' class='white' target='_blank'>$shipments->count</a></u>";
                } else {
                    return 0;
                }
            });
        return $datatable->make(true);
    }

    public function delivered_returned_list(Request $request)
    {
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
        } else {
            $from = Carbon::now()->subDays(29);
            $to = Carbon::now()->endOfDay();
        }
        if ($request->get('search_hub')) {
            $hub = $request->get('search_hub');
        } else {
            $admin = Admin::where('id', Auth::id())->first();
            $hub = $admin->default_hub_id;
        }
        if ($request->get('search_service_type')) {
            $service_type_id = $request->get('search_service_type');
        } else {
            $service_type_id = 1;
        }
        $operation_incoming_delivered_returned = OperationForecast::leftjoin('shipment_status as ss', 'ss.id', '=', 'operation_forecasts.shipper_status_id')
            ->select('operation_forecasts.id as opfs_id', 'ss.id as shipper_status_id', 'ss.name as status', DB::raw('(SELECT SUM(count) FROM operation_forecasts AS opfs WHERE opfs.shipper_status_id = operation_forecasts.shipper_status_id AND opfs.hub_id = "' . $hub . '" AND opfs.booking_type_id = "' . $service_type_id . '" AND updated_at BETWEEN "' . $from . '" AND "' . $to . '") AS count'))
            ->whereIn('shipper_status_id', [14, 25, 30, 31])
            ->where('operation_forecasts.hub_id', $hub)
            ->where('operation_forecasts.booking_type_id', $service_type_id)
            ->whereBetween('operation_forecasts.updated_at', [$from, $to])
            ->groupBy('shipper_status_id')
            ->orderBy('shipper_status_id', 'asc');
        $datatable = Datatables::of($operation_incoming_delivered_returned)
            ->editColumn('count_link', function ($shipments) use ($from, $to, $service_type_id, $hub) {
                if ($shipments->count > 0) {
                    $route = route('admin.operation_forecasting.incoming.shipments_list');
                    return "<u><a href='{$route}/$from/$to/$service_type_id/$hub/$shipments->shipper_status_id' target='_blank'>$shipments->count</a></u>";
                } else {
                    return 0;
                }
            });
        return $datatable->make(true);
    }

    public function outgoing_top_customers_list(Request $request)
    {
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
        } else {
            $from = Carbon::now()->subDays(29);
            $to = Carbon::now()->endOfDay();
        }

        $outgoing_top_five_customers = OperationsOutgoingTopCustomers::leftjoin('users as u', 'u.id', '=', 'operations_outgoing_top_customers.user_id')->select('operations_outgoing_top_customers.id as id', 'u.name as name', 'u.id as user_id', DB::raw('(SELECT SUM(shipments_count) FROM operations_outgoing_top_customers AS ootc WHERE ootc.user_id = operations_outgoing_top_customers.user_id AND updated_at BETWEEN "' . $from . '" AND "' . $to . '") AS count'))
            ->whereBetween('operations_outgoing_top_customers.created_at', [$from, $to])
            ->orderBy('count', 'desc')
            ->groupBy('u.id')
            ->take(5);
        $datatable = Datatables::of($outgoing_top_five_customers)
            ->editColumn('count', function ($shipments) use ($from, $to) {
                if ($shipments->count > 0) {
                    $route = route('admin.operation_forecasting.outgoing.shipments_list');
                    return "<u><a href='{$route}/$from/$to/$shipments->user_id' class='white' target='_blank'>$shipments->count</a></u>";
                } else {
                    return 0;
                }
            });
        return $datatable->make(true);
    }

    public function incoming_weight_range_list(Request $request)
    {
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
        } else {
            $from = Carbon::now()->subDays(29);
            $to = Carbon::now()->endOfDay();
        }

        if ($request->get('search_hub')) {
            $hub = $request->get('search_hub');
        } else {
            $admin = Admin::where('id', Auth::id())->first();
            $hub = $admin->default_hub_id;
        }
        if ($request->get('search_service_type')) {
            $service_type_id = $request->get('search_service_type');
        } else {
            $service_type_id = 1;
        }
        $operation_incoming = OperationForecastWeightRange::leftjoin('operation_forecast_shipments as ofss', 'ofss.weight_range_id', '=', 'operation_forecast_weight_ranges.id')
            ->select('operation_forecast_weight_ranges.name as range', DB::raw('(SELECT count(id) FROM operation_forecast_shipments AS ofs WHERE ofs.weight_range_id = ofss.weight_range_id AND ofs.hub_id = "' . $hub . '"  AND ofs.booking_type_id = "' . $service_type_id . '" AND ofs.updated_at BETWEEN "' . $from . '" AND "' . $to . '") AS count'))->groupBy('operation_forecast_weight_ranges.id');
        $datatable = Datatables::of($operation_incoming);
        return $datatable->make(true);
    }

    public function outgoing_weight_range_list(Request $request)
    {
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
        } else {
            $from = Carbon::now()->subDays(29);
            $to = Carbon::now()->endOfDay();
        }

        if ($request->get('search_hub')) {
            $hub = $request->get('search_hub');
        } else {
            $admin = Admin::where('id', Auth::id())->first();
            $hub = $admin->default_hub_id;
        }
        if ($request->get('search_service_type')) {
            $service_type_id = $request->get('search_service_type');
        } else {
            $service_type_id = 1;
        }
        $operation_outgoing = OperationForecastWeightRange::leftjoin('operations_outgoing_pickup_request_shipments as ooprs', 'ooprs.weight_range_id', '=', 'operation_forecast_weight_ranges.id')
            ->select('operation_forecast_weight_ranges.name as range', DB::raw('(SELECT count(id) FROM operations_outgoing_pickup_request_shipments AS oopr WHERE oopr.weight_range_id = ooprs.weight_range_id AND oopr.hub_id = "' . $hub . '"  AND oopr.booking_type_id = "' . $service_type_id . '" AND oopr.updated_at BETWEEN "' . $from . '" AND "' . $to . '") AS count'))->groupBy('operation_forecast_weight_ranges.id');
        $datatable = Datatables::of($operation_outgoing);
        return $datatable->make(true);
    }

    public function shipments_list($from, $to, $service_type_id, $hub, $shipper_status_id)
    {
        $operation_forecasting_ids = OperationForecast::select('operation_forecasts.id as id')
            ->where('operation_forecasts.shipper_status_id', $shipper_status_id)
            ->where('operation_forecasts.hub_id', $hub)
            ->where('operation_forecasts.booking_type_id', $service_type_id)
            ->whereBetween('operation_forecasts.updated_at', [$from, $to])
            ->pluck('id')->toArray();
        $operation_forecasting_shipments_list = OperationForecastShipments::leftjoin('shipments as s', 's.id', '=', 'operation_forecast_shipments.shipment_id')
            ->select('s.tracking_number as tracking_number')
            ->whereIn('operation_forecast_shipments.operation_forecast_id', $operation_forecasting_ids)
            ->whereBetween('operation_forecast_shipments.updated_at', [$from, $to])
            ->groupBy('s.id')
            ->get();
        if (!empty($operation_forecasting_shipments_list)) {
            return view('admin.operation_forecasting.index')->with(['shipments' => $operation_forecasting_shipments_list]);
        }
    }

    public function outgoing_shipments_list($from, $to, $user_id)
    {
        $top_customers = OperationsOutgoingTopCustomers::select('operations_outgoing_top_customers.id as id')
            ->where('operations_outgoing_top_customers.user_id', $user_id)
            ->whereBetween('operations_outgoing_top_customers.updated_at', [$from, $to])
            ->pluck('id')->toArray();
        $operation_outgoing_top_customer = OperationsOutgoingTopCustomersShipments::leftjoin('shipments as s', 's.id', '=', 'operations_outgoing_top_customers_shipments.shipment_id')
            ->whereIn('operations_outgoing_top_customers_shipments.customer_id', $top_customers)
            ->whereBetween('operations_outgoing_top_customers_shipments.updated_at', [$from, $to])
            ->groupBy('s.id')
            ->get();
        if (!empty($operation_outgoing_top_customer)) {
            return view('admin.operation_forecasting.outgoing_index')->with('shipments', $operation_outgoing_top_customer);
        }
    }

    public function orderPending()
    {
        return view('admin.pending_booked_orders');
    }

    public function pendingAccountsList()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 1);
        $salesperson = Admin::join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.name', 'admins.id'])->where('status', 1)->where('ar.department_id', 7)->get();
        $products = Product::select('id', 'product_name')->get();
        $segments = Segment::all();
        $sale_tier_types = Admin::where('admins.status', 1)->where('role_id', '!=', 1)->get();
        $corporate_rate_types = CorporateRateType::all();
        $payment_cycles = PaymentCycle::all();

        $territories = Territory::select('id', 'name')->where('territory_status', '=', '1')->get();
        $commission_percentage = '';
            $settings = GlobalSettings::where('type', 'commission_percentage');
            if ($settings->exists()) {
                $settings = $settings->first();
                $commission_percentage = $settings->text;
            }
            $sales_tiers = SalesTier::where('status', 1)->get(['id', 'tier_name', 'tier_type', 'commission', 'sales_status']);
            $admin_users = Admin::leftjoin('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.id', 'admins.name','ar.department_id','admins.trax_id'])->where('admins.status', 1)->get();
            $riders_permanent = Rider::where('rider_type_id', 1)->get();
     
            $users = array();
            $sales = array();
            $all_users = array();
            foreach ($admin_users as $u) {
                if ($u->department_id != 7) {
                    $users[] = array('id' => $u->id, 'text' => $u->name . '-' . $u->trax_id);
                } else {
                    $sales[] = array('id' => $u->id, 'text' => $u->name . '-' . $u->trax_id);
                }
            }
          
            $all_users['results'][0]['text'] = 'Sales';
            $all_users['results'][0]['children'] = $sales;
            $all_users['results'][1]['text'] = 'Admins';
            $all_users['results'][1]['children'] = $users;
            $all_users['results'][2]['text'] = 'Riders';
            $all_users['results'][2]['children'] = [];
            $all_users['pagination']['more'] = true;
            // $pending_shippers = User::whereIn('status',[0, 1, 2, 5])->get();

        // $cities = City::where('status', 1)->where('business_category_id', 1)->select('id', 'name')->get();
        return view('admin.accounts.pending_accounts_list')->with(['products' => $products, 'segments' => $segments, 'sale_name' => $salesperson, 'sale_tier_types' => $sale_tier_types, 'corporate_rate_types' => $corporate_rate_types, 'territories' => $territories,'sales_tiers'=>$sales_tiers, 'commission_percentage'=>$commission_percentage,'riders_permanent'=>$riders_permanent,'all_users'=>$all_users, 'payment_cycles'=>$payment_cycles]);
    }

    public function activeAccountsList()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 2);
        
        $salesperson = Admin::join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.name', 'admins.id'])->where('status', 1)->where('ar.department_id', 7)->get();
        $products = Product::select('id', 'product_name')->get();
        $segments = Segment::all();
        $general_segments = SubCategorySegment::where('segment_id', 1)->get();
        $ecom_segments = SubCategorySegment::where('segment_id', 2)->get();
        $payment_cycles = PaymentCycle::all();
        $sale_tier_types = Admin::where('admins.status', 1)->where('role_id', '!=', 1)->get();
        $territories = Territory::select('id', 'name')->where('territory_status', '=', '1')->get();
        $block_disable_reasons = DB::table('block_disable_reason_users')->select('id', 'name')->get();
        $commission_percentage = '';
        $settings = GlobalSettings::where('type', 'commission_percentage');
        if ($settings->exists()) {
            $settings = $settings->first();
            $commission_percentage = $settings->text;
        }
        $sales_tiers = SalesTier::where('status', 1)->get(['id', 'tier_name', 'tier_type', 'commission', 'sales_status']);
        $admin_users = Admin::leftjoin('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.id', 'admins.name','ar.department_id','admins.trax_id'])->where('admins.status', 1)->get();
        $riders_permanent = Rider::where('rider_type_id', 1)->get();
 
        $users = array();
        $sales = array();
        $all_users = array();
        foreach ($admin_users as $u) {
            if ($u->department_id != 7) {
                $users[] = array('id' => $u->id, 'text' => $u->name . '-' . $u->trax_id);
            } else {
                $sales[] = array('id' => $u->id, 'text' => $u->name . '-' . $u->trax_id);
            }
        }
      
        $all_users['results'][0]['text'] = 'Sales';
        $all_users['results'][0]['children'] = $sales;
        $all_users['results'][1]['text'] = 'Admins';
        $all_users['results'][1]['children'] = $users;
        $all_users['results'][2]['text'] = 'Riders';
        $all_users['results'][2]['children'] = [];
        $all_users['pagination']['more'] = true;
        // $active_shippers = User::whereIn('status', [3, 4])->get();
        return view('admin.accounts.active_accounts_list')->with(['products' => $products, 'sale_name' => $salesperson,'payment_cycles' => $payment_cycles, 'segments' => $segments, 'ecom_segments' => $ecom_segments, 'general_segments' => $general_segments, 'sale_tier_types' => $sale_tier_types, 'territories' => $territories,'sales_tiers'=>$sales_tiers, 'commission_percentage'=>$commission_percentage,'riders_permanent'=>$riders_permanent,'all_users'=>$all_users, 'block_disable_reasons'=> $block_disable_reasons]);
    }

    public function shipperNamesForDropdown(Request $request, $type)
    {
        $keyword = $request->search;
        $subSegment = $request->input('sub_segment_select', null);
        $account_tye_id = $request->input('account_type_id', null);
        $report_type = $request->report_type; //1 => for kam & poc qsr report
        if($report_type == 1) {
            $shippers = User::leftJoin('sale_tier_tags', 'sale_tier_tags.user_id', '=', 'users.id')
            ->where(function($query) {
            $query->whereNotNull('sale_tier_tags.poc')
                 ->orWhereNotNull('sale_tier_tags.kam');
            })
            ->where('users.name', 'like', '%' . $keyword . '%');

        } else {
            $shippers = User::where(function($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('id', 'like', '%' . $keyword . '%');
            });

        }
        
        if($type == 'active')
        {
            $shippers = $shippers->whereIn('users.status', [3, 4]);
        }
        elseif($type == 'pending')
        {
            $shippers = $shippers->whereIn('users.status', [0, 1, 2, 5]);
        }
        if($subSegment){
            $shippers = $shippers->where('users.segment_id', $subSegment);
        }
        if($account_tye_id){
            $shippers = $shippers->whereIn('users.account_type_id', $account_tye_id);
        }
        $shippers = $shippers->where(function($query){
            $idsToExclude = FilterTrait::class::getFilteredIds(auth()->user()->id);
            if (!empty($idsToExclude)) {
                $query->whereNotIn('users.id', $idsToExclude);
            }
        });

        $shippers = $shippers->select('users.id','name as text')->take(10)->get()->toArray();

        return response()->json($shippers);
    }

    public function shipperNamesForDropdown_invoice(Request $request, $type)
    {
        $keyword = $request->search;
        $shippers = User::where('name', 'like', '%' . $keyword . '%');

        if (session('department_id') == 7 && !in_array(session('id'), session('sale_users_bypass'))) {
            $shippers = $shippers->whereIn('id', session('tagged_shippers'));
        } else {
            $shippers = $shippers->whereIn('status', [3, 4]);
        }

        $shippers = $shippers->where(function($query){
            $idsToExclude = FilterTrait::class::getFilteredIds(auth()->user()->id);
            if (!empty($idsToExclude)) {
                $query->whereNotIn('users.id', $idsToExclude);
            }
        });

        $shippers = $shippers->select('id','name as text')->take(10)->get()->toArray();

        return response()->json($shippers);
    }

    public function shipperExclude(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
        ]);

        $user_id = $request->user_id;
        // Convert checkbox value to boolean
        $exclude_shipper = $request->input('exclude_shipper') ? true : false; 
        $different_consignee = $request->input('different_consignee') ? true : false;
        $same_consignee = $request->input('same_consignee') ? true : false;

        if ($different_consignee && $same_consignee) {
            return redirect()->back()->with('error', 'Cannot select both consignee types at the same time.');
        }

        if  (
                ($exclude_shipper && $same_consignee) || 
                ($exclude_shipper && $different_consignee) || 
                ($exclude_shipper && $same_consignee && $different_consignee)
            ) {
            return redirect()->back()->with('error', 'Cannot select consignee types if shipper is excluded.');
        }

        if (!$different_consignee && !$same_consignee && !$exclude_shipper) {
            return redirect()->back()->with('error', 'Please select an option first');
        }

        ShipperInterceptExclude::updateOrCreate(
            ['user_id' => $user_id],
            [
                'exclude_shipper' => $exclude_shipper,
                'different_consignee' => $different_consignee,
                'same_consignee' => $same_consignee,
            ]
        );

        return redirect()->back()->with('success', 'Shipper exclude settings saved successfully.');
    }

    public function blockAccountsList()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 275);
        $salesperson = Admin::join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.name', 'admins.id'])->where('ar.department_id', 7)->get();
        $sale_tier_types = Admin::where('admins.status', 1)->where('role_id', '!=', 1)->get();
        return view('admin.accounts.block_accounts_list')->with(['sale_name' => $salesperson, 'sale_tier_types' => $sale_tier_types]);
    }

    public function UserStatus(Request $request)
    {

        $id = $request->shid; //shipper id
        $status = $request->status;
        $user = User::find($id);
        if (!$user) {
            return back()->with('danger', 'User not found.');
        }
        if ($status == 'activate') {

            if ($user->status == 2) {   
                $now = Carbon::now();
                $action = User::where('id', $user->id)->update(['status' => 3, 'account_activated_by' => Auth::id(), 'activated_at' => $now, 'reactivated_at' => $now]);
                if ($user->lead_id != null) {
                    $lead = Lead::find($user->lead_id);
                    $lead_log = new LeadLog();
                    $lead_log->lead_id = $lead->id;
                    $lead_log->prev_status_id = $lead->status_id;
                    $lead_log->status_id = 12;
                    $lead_log->sale_person_id = $lead->sale_person_id;
                    $lead_log->reference_person_id = $lead->reference_person_id;
                    $lead_log->updated_by = Auth::id();
                    $lead_log->save();

                    $lead->status_id = 12;
                    $lead->updated_by = Auth::id();
                    $lead->save();
                }
                if ($action == 1) { 
                    if($user->sms_charges_status == 1) {
                        $notification_settings = NotificationSetting::where('shipper_toggle' , 0)->pluck('id');
                        foreach($notification_settings as $notification_setting ) {
                            $notification_setting_shipper = new NotificationSettingShipper();
                            $notification_setting_shipper->notification_setting_id = $notification_setting;
                            $notification_setting_shipper->shipper_id = $user->id;
                            $notification_setting_shipper->save();
                        }
                    }
                    NotificationsController::send(1, $user->id);

                    return redirect()->route('admin.accounts.active')->with('success', 'User is activated.');
                } else {
                    return back()->with('danger', 'There is some problem please try again.');
                }
            } else {
                return back()->with('danger', 'This user\'s rates are not set.');
            }
        }

    }

    public function tagSubmit(Request $request)
    {
        $tag_id = $request->admin_id;
        $shipper_id = $request->shipper_id;
        $user = User::find($shipper_id);
        $shipper_hub_id = $user->city->hub_id;
        $sale_persons = array();
        $old_sale_person = '';
        $new_sale_person = '';
        if (AdminHub::where('admin_id', $tag_id)->where('hub_id', $shipper_hub_id)->exists()) {
            if (!SalePersonTag::where(['admin_id' => $tag_id, 'user_id' => $shipper_id, 'status' => 0])->exists()) {
                $old_sale_person = SalePersonTag::where('user_id', $shipper_id)->where('status', 0)->latest()->first();
                if ($old_sale_person) {
                    $old_sale_person = $old_sale_person->sales_person;
                    $old_sale_person_date = $old_sale_person->created_at;
                } else {
                    $old_sale_person = null;
                    $old_sale_person_date = null;
                }
                $new_sale_person = Admin::find($tag_id);
                $shipper_data = SalePersonTag::where('user_id', $shipper_id)->where('status', 0)->get();
                if ($shipper_data->count() > 0) {
                    SalePersonTag::where('user_id', $shipper_id)->where('status', 0)->update(['status' => 1]);
                }
                $shipper = User::find($shipper_id);
                $sale_person_tag = new SalePersonTag();
                $sale_person_tag->admin_id = $tag_id;
                $sale_person_tag->user_id = $shipper_id;
                $sale_person_tag->save();

                AccountTaggingLog::logTagging(
                    $shipper_id,       
                    Auth::id(),         
                    $old_sale_person?->id, 
                    $tag_id,            
                    1                    
                );

                $shipper_zone_id = $user->city->zone_id; 
                $zone = Zone::where('status', 1)->where('id', $shipper_zone_id)->first();

                $sale_persons[$shipper_id] = ['old_sale_person' => $old_sale_person, 'new_sale_person' => $new_sale_person, 'old_sale_person_date' => $old_sale_person_date ,'zone' => $zone];

                NotificationsController::send(81, $sale_persons, Auth::id());
                NotificationsController::send(119, $sale_persons, Auth::id());


            } else {
                return ['status' => 0, 'error' => "Shipper is already tagged to  Sales Person!"];
            }

            return ['status' => 1, 'success' => "Shipper is tagged to Sales Person!"];
        } else {
            return ['status' => 0, 'error' => "Shipper is not tagged to Sales Person!"];

        }

    }
    public function tagSubmitBulk(Request $request)
    {
        $tag_id = $request->admin_id;
        $shipper_ids = $request->shipper_ids;
        $sale_persons = array();
        if ($shipper_ids) {
            foreach ($shipper_ids as $shipper_id) {
                $old_sale_person_data = '';
                $old_sale_person_date = '';
                $user = User::find($shipper_id);
                $shipper_hub_id = $user->city->hub_id;
                if (AdminHub::where('admin_id', $tag_id)->where('hub_id', $shipper_hub_id)->exists()) {
                    if (!SalePersonTag::where(['admin_id' => $tag_id, 'user_id' => $shipper_id, 'status' => 0])->exists()) {
                        $old_sale_person = SalePersonTag::where('user_id', $shipper_id)->where('status', 0)->latest()->first();
                        if ($old_sale_person) {
                            $old_sale_person_date = $old_sale_person->created_at;
                            $old_sale_person->status = 1;
                            $old_sale_person->save();
                            $old_sale_person_data = $old_sale_person->sales_person;
                        }
                        $new_sale_person = Admin::find($tag_id);

                        AccountTaggingLog::logTagging(
                            $shipper_id,       
                            Auth::id(),         
                            $old_sale_person_data?->id, 
                            $tag_id,            
                            1                    
                        );

                        $sale_person_tag = new SalePersonTag();
                        $sale_person_tag->admin_id = $tag_id;
                        $sale_person_tag->user_id = $shipper_id;
                        $sale_person_tag->status = 0;
                        $sale_person_tag->save();

                        $shipper_zone_id = $user->city->zone_id; 
                        $zone = Zone::where('status', 1)->where('id', $shipper_zone_id)->first();
                        
                        $sale_persons[$shipper_id] = ['old_sale_person' => $old_sale_person_data, 'new_sale_person' => $new_sale_person, 'old_sale_person_date' => $old_sale_person_date,'zone' => $zone];
                    }
                }
            }

            NotificationsController::send(81, $sale_persons, Auth::id());
            NotificationsController::send(119, $sale_persons, Auth::id());
            return ['status' => 1, 'success' => "Shipper is tagged to Sales Person!"];
        } else {
            return ['status' => 0, 'error' => "Shipper is not tagged to Sales Person!"];
        }
    }

    public function rejectReasonSubmit(Request $request)
    {
        $shipper_id = $request->shipper_id;
        $reject_reason = $request->rejected_reason;
        $user = User::find($shipper_id);
        if ($user->status != 3) {
            $user->status = 5;
        }
        if ($user->rate_type_id_status == 1) {
            $user->rate_type_id_status = 2;
        }
        $user->rejected_reason = $reject_reason;
        $user->rate_status = 2;
        $user->rates_rejected_by = Auth::id();
        $user->rates_rejected_at = Carbon::now();
        $user->save();
        NotificationsController::send(64, $shipper_id);
        return ['success' => 'Rates has been rejected!'];
    }

    public function UserStatusBlock(Request $request)
    {
        $user_id = $request->id;
        $remarks = $request->remarks;
        $reason = $request->reason;
        $status = $request->status;

        $user = User::where('id', $user_id);
        if ($user->exists()) {
            $user = $user->first();
            

            if ($status == 'block') {
                $negative_balance_status = false;
                $merged_account = MergedSisterAccount::where('user_id', $user_id);
                if ($merged_account->exists()) {
                    $merged_account = $merged_account->first();
                    $merged_accounts = MergedSisterAccount::where('merged_head_id', $merged_account->merged_head_id)->get();
                    foreach ($merged_accounts as $merge_account) {
                        $pending_payment_shipper = PendingPayment::where('user_id', $merge_account->user_id);
                        if ($pending_payment_shipper->exists()) {
                            $pending_payment_shipper = $pending_payment_shipper->first();
                            $payable = PendingPaymentShipment::where('pending_payment_id', $pending_payment_shipper->id)->sum('payable');
                            if ($payable < 0) {
                                $negative_balance_status = true;
                            }
                        }
                    }
                } else {
                    $pending_payment_shipper = PendingPayment::where('user_id', $user_id);
                    if ($pending_payment_shipper->exists()) {
                        $pending_payment_shipper = $pending_payment_shipper->first();
                        $payable = PendingPaymentShipment::where('pending_payment_id', $pending_payment_shipper->id)->sum('payable');
                        if ($payable < 0) {
                            $negative_balance_status = true;
                        }
                    }
                }
                if ($negative_balance_status == false) {
                    if ($user->blacklist == 0) {                        
                        $user->blacklist = 1;
                        $user->blacklist_reason = $remarks;
                        $user->blacklist_reason_1 = $reason;
                        $user->blocked_at = Carbon::now()->format('Y-m-d H:i:s');
                        $user->save();

                        UserDisableBlockEmailNotification::dispatch($user);


                        return response()->json(['status' => 1, 'success' => "User added to the blacklist!"]);
                    } else {
                        return response()->json(['status' => 0, 'error' => "User is already in blacklist!"]);
                    }
                } else {
                    return response()->json(['status' => 0, 'error' => "Negative balance found!"]);
                }
            } else if ($status == 'unblock') {
                if ($user->blacklist == 1) {
                    $user->blacklist = 0;
                    $user->save();
                    return response()->json(['status' => 1, 'success' => "User removed from the blacklist!"]);
                } else {
                    return response()->json(['status' => 0, 'error' => "User is not in the blacklist!"]);
                }
            }

        } else {
            return response()->json(['status' => 0, 'error' => "User doesn\'t exist!"]);
        }
    }

    public function UserStatusChange(Request $request)
    {
        $user_id = $request->id;
        $status = $request->status;
        $remarks = $request->remarks;
        $reason = $request->reason;

        $user = User::where('id', $user_id);
        if ($user->exists()) {
            $user = $user->first();
            if ($status == 'enable') {
                if ($user->status == 4 || $user->status == 6) {
                    $user->status = 3;
                    $user->disable_remarks = null;
                    $user->reactivated_at = Carbon::now();
                    //$user->disable_at = null;

                    $user->save();
                    return response()->json(['status' => 1, 'success' => "User is now enabled!"]);
                } else if($user->status == 3){
                    return response()->json(['status' => 0, 'error' => "User is already enabled!"]);
                }

                else
                {
                    return response()->json(['status' => 0, 'error' => "Something went wrong!"]);
                }
            } else if ($status == 'disable') {
                if ($user->status == 3) {
                    $user->disable_at =  Carbon::now()->format('Y-m-d H:i:s');
                    $user->disable_reason = $remarks;
                    $user->disable_reason_1 = $reason;
                    $user->status = 4;
                    $user->save();
                    UserDisableBlockEmailNotification::dispatch($user);

                    //                    add row in user_check_status table
                    $userstatus = UserCheckStatus::where('user_id', $user_id)->count();

                    if ($userstatus == 1) {
                        $userstatus = UserCheckStatus::where('user_id', $user_id)->first();
                        $count = $userstatus->status_count + 1;
                        $userstatus->status_count = $count;
                        $userstatus->save();
                    } elseif ($userstatus == 0) {
                        $userstatus = new UserCheckStatus();
                        $userstatus->user_id = $user_id;
                        $userstatus->status = $user->status;
                        $userstatus->status_count =$userstatus->status_count + 1;
                        $userstatus->save();
                    }
                    //                    add row in user_status table end

                    return response()->json(['status' => 1, 'success' => "User is now disabled!"]);
                } else {
                    return response()->json(['status' => 0, 'error' => "User is already disabled!"]);

                }
            }
            else if($status == 'pause')
            {
                if ($user->status == 3) {
                    $user->status = 6;
                    $user->disable_remarks = null;
                    
                    $user->save();
                    return response()->json(['status' => 1, 'success' => "User is now Paused!"]);
                } else if($user->status == 6) {
                    return response()->json(['status' => 0, 'error' => "User is already Paused!"]);
                }
                else
                {
                    return response()->json(['status' => 0, 'error' => "Something went wrong!"]);
                }
            }
        } else {
            return response()->json(['status' => 0, 'error' => "User doesn\'t exist!"]);
        }
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function viewBankInfo($id)
    {
        $user = User::find($id);
        $banks = $user->bank;
//        return $banks;
        $returnHTML = view('admin/components/bank')->with(['banks' => $banks, 'user' => $user])->render();
        return response()->json($returnHTML);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function viewShippingInfo($id)
    {
        $user = User::find($id);
        $shipping = $user->shipping()->where('hidden', 0)->get();
        $returnHTML = view('admin.components.shipping')->with(['shipping' => $shipping, 'user' => $user])->render();
        return response()->json($returnHTML);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function viewShipperRates($id)
    {
        $user = User::find($id);
        $shipping = $user->shipping;
        $returnHTML = view('admin.components.shipping')->with(['shipping' => $shipping, 'user' => $user])->render();
        return response()->json($returnHTML);
    }

    public function addRatesView($id)
    {
        $user = User::find($id);
        if (!RateStatus::where('user_id', $user->id)->exists()) {
            $sale_person = SalePersonTag::where('user_id', $id)->first();
            $weight = StandardWeightCharge::all()->groupBy('shipping_mode_id');
            $bookingType = StandardBookingTypeCharge::all()->groupBy('shipping_mode_id');
            $cash = StandardCashHandlingCharge::all()->groupBy('shipping_mode_id');
            $insurance = StandardInsuranceCharge::all()->groupBy('shipping_mode_id');
            $return = StandardReturnCharge::all()->groupBy('shipping_mode_id');
            $fuel = StandardFuelSurcharge::all()->groupBy('shipping_mode_id');
            $packaging_material_types = PackagingMaterialTypes::where('status', 1)->get();
            $packaging_sizes = array();
            $invoicing_cycles = InvoicingCycle::where('id', '!=', 2)->get();
            $storage_types = WmsStorageType::all()->where('status', 1);
            if (count($packaging_material_types) > 0) {

                foreach ($packaging_material_types as $type) {
                    $packaging_sizes[$type->id] = PackagingMaterialTypeSizes::where('type_id', $type->id)->get();
                }
            }


            $minimum_chargeable_weights = MinimumChargeableWeightSetting::get();
            $on = null;
            $ol = null;
            $det = null;
            $same_day = null;
            foreach ($minimum_chargeable_weights as $minimum_chargeable_weight) {
                if ($minimum_chargeable_weight->shipping_mode_id == 1) {
                    $on = $minimum_chargeable_weight->weight;
                } elseif ($minimum_chargeable_weight->shipping_mode_id == 2) {
                    $ol = $minimum_chargeable_weight->weight;
                } elseif ($minimum_chargeable_weight->shipping_mode_id == 3) {
                    $det = $minimum_chargeable_weight->weight;
                } else {
                    $same_day = $minimum_chargeable_weight->weight;
                }
            }
            $commission_percentage = '';
            $settings = GlobalSettings::where('type', 'commission_percentage');
            if ($settings->exists()) {
                $settings = $settings->first();
                $commission_percentage = $settings->text;
            }
            $sales_tiers = SalesTier::where('status', 1)->get(['id', 'tier_name', 'tier_type', 'commission', 'sales_status']);
            $admin_users = Admin::leftjoin('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.id', 'admins.name','ar.department_id','admins.trax_id'])->where('admins.status', 1)->get();
            $riders_permanent = Rider::where('rider_type_id', 1)->get();
     
            $users = array();
            $sales = array();
            $all_users = array();
            foreach ($admin_users as $u) {
                if ($u->department_id != 7) {
                    $users[] = array('id' => $u->id, 'text' => $u->name . '-' . $u->trax_id);
                } else {
                    $sales[] = array('id' => $u->id, 'text' => $u->name . '-' . $u->trax_id);
                }
            }
          
            $all_users['results'][0]['text'] = 'Sales';
            $all_users['results'][0]['children'] = $sales;
            $all_users['results'][1]['text'] = 'Admins';
            $all_users['results'][1]['children'] = $users;
            $all_users['results'][2]['text'] = 'Riders';
            $all_users['results'][2]['children'] = [];
            $all_users['pagination']['more'] = true;

            $cities = City::where('status', 1)->where('business_category_id', 1)->select('id', 'name')->get();
            return view('admin.accounts.add_rates')->with(['riders_permanents'=>$riders_permanent,'shipper' => $user, 'weight' => $weight, 'shippingType' => $bookingType, 'cashHandling' => $cash, 'insuranceCharges' => $insurance, 'returnCharges' => $return, 'fuelCharges' => $fuel, 'sale_person' => $sale_person, 'packaging_material_types' => $packaging_material_types, 'packaging_material_type_sizes' => $packaging_sizes, 'invoicing_cycles' => $invoicing_cycles, 'storage_types' => $storage_types, 'on' => $on, 'ol' => $ol, 'det' => $det, 'same_day' => $same_day, 'commission_percentage' => $commission_percentage, 'sales_tiers' => $sales_tiers, 'users' => $all_users, 'cities' => $cities]);
        }
        return redirect()->back()->with('error', 'User rates not found!');
    }
//    public function salesTag(Request $name)
//    {
//        $salesperson = admins::join('admin_department as ad', 'admins.role_id', '=', 'ad.role_id')
//            ->join('admin_roles as ar', 'ar.department_id', '=', 7)->get();
//    }

    public function viewRates($id, $date = null)
    {

        $user = User::find($id);
        $on_dws_charges = null;
        $ol_dws_charges = null;
        $detain_dws_charges = null;
        $sameday_dws_charges = null;

        
        if ($date == null) {
            
            $dws_weight = DwsWeightCharges::where('user_id', $id);

            if ($dws_weight->exists()) {
                $dws_weight = $dws_weight->get();
                foreach ($dws_weight as $value) {
                    if ($value->shipping_mode_id == 1) {
                        $on_dws_charges = $value->dws_weight_status;

                    } elseif ($value->shipping_mode_id == 2) {
                        $ol_dws_charges = $value->dws_weight_status;

                    } elseif ($value->shipping_mode_id == 3) {
                        $detain_dws_charges = $value->dws_weight_status;

                    } elseif ($value->shipping_mode_id == 4) {
                        $sameday_dws_charges = $value->dws_weight_status;

                    }
                }
            }
            $cash = CashHandlingCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $insurance = InsuranceCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $return = ReturnCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $fuel = FuelSurcharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $weight = WeightCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $bookingType = BookingTypeCharges::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $switches = RateStatus::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $discount = DiscountCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $rate_origin_hubs = RateOriginHub::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $rate_destination_hubs = RateDestinationHub::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $discount_weight_rates = DiscountWeightCharge::all()->where('user_id',$id)->groupBy(['shipping_mode_id','destination_id']);
            $sms_charge = User::where('id', $id)->select(['id','sms_charges','sms_charges_status'])->get();

            $zero_cod_discount = ZeroCodDiscountCharges::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $return_discount_charges = ShipmentReturnDiscountCharges::all()->where('user_id', $id)->groupBy('shipping_mode_id');

        } else {
            $tomorrow = Carbon::parse($date)->addDay(1);

            $dws_weight = DwsWeightChargesHistory::where('user_id', $id)->where('created_at', '>=', $date)->where('created_at', '<', $tomorrow);

            if ($dws_weight->exists()) {
                $dws_weight = $dws_weight->get();
                foreach ($dws_weight as $value) {
                    if ($value->shipping_mode_id == 1) {
                        $on_dws_charges = $value->dws_weight_status;

                    } elseif ($value->shipping_mode_id == 2) {
                        $ol_dws_charges = $value->dws_weight_status;

                    } elseif ($value->shipping_mode_id == 3) {
                        $detain_dws_charges = $value->dws_weight_status;

                    } elseif ($value->shipping_mode_id == 4) {
                        $sameday_dws_charges = $value->dws_weight_status;

                    }
                }
            }
            $cash = HistoryCashHandlingCharge::all()->where('user_id', $id)->where('created_at', '>=', $date)->where('created_at', '<', $tomorrow)->groupBy('shipping_mode_id');
            $insurance = HistoryInsuranceCharge::all()->where('user_id', $id)->where('created_at', '>=', $date)->where('created_at', '<', $tomorrow)->groupBy('shipping_mode_id');
            $return = HistoryReturnCharge::all()->where('user_id', $id)->where('created_at', '>=', $date)->where('created_at', '<', $tomorrow)->groupBy('shipping_mode_id');
            $fuel = HistoryFuelSurcharge::all()->where('user_id', $id)->where('created_at', '>=', $date)->where('created_at', '<', $tomorrow)->groupBy('shipping_mode_id');
            $weight = HistoryWeightCharge::all()->where('user_id', $id)->where('created_at', '>=', $date)->where('created_at', '<', $tomorrow)->groupBy('shipping_mode_id');
            $bookingType = HistoryBookingTypeCharges::all()->where('user_id', $id)->where('created_at', '>=', $date)->where('created_at', '<', $tomorrow)->groupBy('shipping_mode_id');
            $switches = HistoryRateStatus::all()->where('user_id', $id)->where('created_at', '>=', $date)->where('created_at', '<', $tomorrow)->groupBy('shipping_mode_id');
            $discount = HistoryDiscountCharge::all()->where('user_id', $id)->where('created_at', '>=', $date)->where('created_at', '<', $tomorrow)->groupBy('shipping_mode_id');
            $rate_origin_hubs = HistoryRateOriginHub::all()->where('user_id', $id)->where('created_at', '>=', $date)->where('created_at', '<', $tomorrow)->groupBy('shipping_mode_id');
            $rate_destination_hubs = HistoryRateDestinationHub::all()->where('user_id', $id)->where('created_at', '>=', $date)->where('created_at', '<', $tomorrow)->groupBy('shipping_mode_id');
            $discount_weight_rates = HistoryDiscountWeightCharge::all()->where('user_id',$id)->where('created_at', '>=', $date)->where('created_at', '<', $tomorrow)->groupBy(['shipping_mode_id','destination_id']);
            $sms_charge = HistorySmsCharges::where('user_id', $id)->get();
$zero_cod_discount = HistoryZeroCodDiscountCharges::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $return_discount_charges = HistoryShipmentReturnDiscountCharges::all()->where('user_id', $id)->groupBy('shipping_mode_id');        }


        $sale_person = SalePersonTag::where('user_id', $id)->where('status', 0)->first();
        $packaging = PackagingCharge::all()->where('user_id', $id);
        $packaging_type_ids = array_unique($packaging->pluck('type_id')->toArray());

        $packaging_material_types = PackagingMaterialTypes::with(['sizes'])->where('status', 1)->get();
        $wms_user_info = WmsUserInformation::where('user_id', $id)->first();
        $wms_product_charges = WmsPerProductCharge::where('user_id', $id)->first();
        $wms_square_foot_charges = WmsPerSquareFootCharge::where('user_id', $id)->first();
        $wms_packing_charges = WmsPackingCharge::where('user_id', $id)->get();
        $wms_labelling_charges = WmsLabellingCharge::where('user_id', $id)->first();
        $wms_storage_charges = WmsStorageTypeCharge::where('user_id', $id)->get();
        $storage_types = WmsStorageType::all()->where('status', 1);
        $invoicing_cycles = InvoicingCycle::where('id', '!=', 2)->get();
        $rate_remarks = RateRemark::where('user_id', $id)->orderBy('created_at', 'desc')->get();
        $packaging_charges = array();
        if (count($packaging) > 0) {

            foreach ($packaging as $charge) {
                $packaging_charges[$charge->type_id][] = $charge;
            }
        }

        $sales_commission = SalesCommission::where('shipper_id', $id)->first();

        $cities = City::where('status', 1)->where('business_category_id', 1)->select('id', 'name')->get();

        $overnight_origins = [];
        $overland_origins = [];
        $detain_origins = [];
        $sameday_origins = [];
        if ($rate_origin_hubs || count($rate_origin_hubs) > 0) {
            foreach ($rate_origin_hubs as $index => $origin) {

                if ($index == 1) {
                    foreach ($origin as $origin_data) {
                        $overnight_origins[] = $origin_data->city_id;
                    }
                } else if ($index == 2) {
                    foreach ($origin as $origin_data) {
                        $overland_origins[] = $origin_data->city_id;
                    }
                } else if ($index == 3) {
                    foreach ($origin as $origin_data) {
                        $detain_origins[] = $origin_data->city_id;
                    }
                } else if ($index == 4) {
                    foreach ($origin as $origin_data) {
                        $sameday_origins[] = $origin_data->city_id;
                    }
                }

            }
        }
        $overnight_destinations = [];
        $overland_destinations = [];
        $detain_destinations = [];
        $sameday_destinations = [];
        if ($rate_destination_hubs || count($rate_destination_hubs) > 0) {
            foreach ($rate_destination_hubs as $index => $destination) {

                if ($index == 1) {
                    foreach ($destination as $destination_data) {
                        $overnight_destinations[] = $destination_data->city_id;
                    }
                } else if ($index == 2) {
                    foreach ($destination as $destination_data) {
                        $overland_destinations[] = $destination_data->city_id;
                    }
                } else if ($index == 3) {
                    foreach ($destination as $destination_data) {
                        $detain_destinations[] = $destination_data->city_id;
                    }
                } else if ($index == 4) {
                    foreach ($destination as $destination_data) {
                        $sameday_destinations[] = $destination_data->city_id;
                    }
                }

            }
        }


        // $riders_permanent = Rider::where('rider_type_id', 1)->get();
        // foreach($riders_permanent as $rider){
        //     $riders[] = array('id' => $rider->id, 'text' => $rider->name);
        // }

        if (session('department_id') == 7) {
            if ($sale_person['admin_id'] == Auth::id() || in_array(session('id'), session('sale_users_bypass')) || in_array($id, session('tagged_shippers'))) {
                // return view('admin.accounts.view_rates')->with(['riders_permanents'=>$riders_permanent,'sameday_dws_charges' => $sameday_dws_charges, 'detain_dws_charges' => $detain_dws_charges, 'ol_dws_charges' => $ol_dws_charges, 'on_dws_charges' => $on_dws_charges, 'shipper' => $user, 'switches' => $switches, 'weight' => $weight, 'shippingType' => $bookingType, 'cashHandling' => $cash, 'insuranceCharges' => $insurance, 'returnCharges' => $return, 'fuelCharges' => $fuel, 'packagingCharges' => $packaging, 'discountCharges' => $discount,'discount_weight_rates' => $discount_weight_rates, 'sale_person' => $sale_person, 'packaging_material_types' => $packaging_material_types, 'packaging_type_ids' => $packaging_type_ids, 'packaging_charges' => $packaging_charges, 'wms_user_info' => $wms_user_info, 'wms_product_charges' => $wms_product_charges, 'wms_square_foot_charges' => $wms_square_foot_charges, 'wms_packing_charges' => $wms_packing_charges, 'wms_labelling_charges' => $wms_labelling_charges, 'wms_storage_charges' => $wms_storage_charges, 'invoicing_cycles' => $invoicing_cycles, 'storage_types' => $storage_types, 'rate_remarks' => $rate_remarks, 'sales_commission' => $sales_commission, 'overnight_origins' => $overnight_origins, 'overland_origins' => $overland_origins, 'detain_origins' => $detain_origins, 'sameday_origins' => $sameday_origins, 'overnight_destinations' => $overnight_destinations, 'overland_destinations' => $overland_destinations, 'detain_destinations' => $detain_destinations, 'sameday_destinations' => $sameday_destinations, 'cities' => $cities]);
                return view('admin.accounts.view_rates')->with(['sameday_dws_charges' => $sameday_dws_charges, 'detain_dws_charges' => $detain_dws_charges, 'ol_dws_charges' => $ol_dws_charges, 'on_dws_charges' => $on_dws_charges, 'shipper' => $user, 'switches' => $switches, 'weight' => $weight, 'shippingType' => $bookingType, 'cashHandling' => $cash, 'insuranceCharges' => $insurance, 'returnCharges' => $return, 'fuelCharges' => $fuel, 'packagingCharges' => $packaging, 'discountCharges' => $discount,'discount_weight_rates' => $discount_weight_rates, 'sale_person' => $sale_person, 'packaging_material_types' => $packaging_material_types, 'packaging_type_ids' => $packaging_type_ids, 'packaging_charges' => $packaging_charges, 'wms_user_info' => $wms_user_info, 'wms_product_charges' => $wms_product_charges, 'wms_square_foot_charges' => $wms_square_foot_charges, 'wms_packing_charges' => $wms_packing_charges, 'wms_labelling_charges' => $wms_labelling_charges, 'wms_storage_charges' => $wms_storage_charges, 'invoicing_cycles' => $invoicing_cycles, 'storage_types' => $storage_types, 'rate_remarks' => $rate_remarks, 'sales_commission' => $sales_commission, 'overnight_origins' => $overnight_origins, 'overland_origins' => $overland_origins, 'detain_origins' => $detain_origins, 'sameday_origins' => $sameday_origins, 'overnight_destinations' => $overnight_destinations, 'overland_destinations' => $overland_destinations, 'detain_destinations' => $detain_destinations, 'sameday_destinations' => $sameday_destinations, 'cities' => $cities, 'sms_charges' => $sms_charge,'zero_cod_discount'=>$zero_cod_discount,'return_discount_charges'=>$return_discount_charges]);
            } else {
                return view('admin.access_denied');
            }
        } else {
            // return view('admin.accounts.view_rates')->with(['riders_permanents'=>$riders_permanent,'sameday_dws_charges' => $sameday_dws_charges, 'detain_dws_charges' => $detain_dws_charges, 'ol_dws_charges' => $ol_dws_charges, 'on_dws_charges' => $on_dws_charges, 'shipper' => $user, 'switches' => $switches, 'weight' => $weight, 'shippingType' => $bookingType, 'cashHandling' => $cash, 'insuranceCharges' => $insurance, 'returnCharges' => $return, 'fuelCharges' => $fuel, 'packagingCharges' => $packaging, 'discountCharges' => $discount,'discount_weight_rates' => $discount_weight_rates, 'sale_person' => $sale_person, 'packaging_material_types' => $packaging_material_types, 'packaging_type_ids' => $packaging_type_ids, 'packaging_charges' => $packaging_charges, 'wms_user_info' => $wms_user_info, 'wms_product_charges' => $wms_product_charges, 'wms_square_foot_charges' => $wms_square_foot_charges, 'wms_packing_charges' => $wms_packing_charges, 'wms_labelling_charges' => $wms_labelling_charges, 'wms_storage_charges' => $wms_storage_charges, 'invoicing_cycles' => $invoicing_cycles, 'storage_types' => $storage_types, 'rate_remarks' => $rate_remarks, 'sales_commission' => $sales_commission, 'overnight_origins' => $overnight_origins, 'overland_origins' => $overland_origins, 'detain_origins' => $detain_origins, 'sameday_origins' => $sameday_origins, 'overnight_destinations' => $overnight_destinations, 'overland_destinations' => $overland_destinations, 'detain_destinations' => $detain_destinations, 'sameday_destinations' => $sameday_destinations, 'cities' => $cities,'zero_cod_discount'=>$zero_cod_discount,'return_discount_charges'=>$return_discount_charges]);
            return view('admin.accounts.view_rates')->with(['sameday_dws_charges' => $sameday_dws_charges, 'detain_dws_charges' => $detain_dws_charges, 'ol_dws_charges' => $ol_dws_charges, 'on_dws_charges' => $on_dws_charges, 'shipper' => $user, 'switches' => $switches, 'weight' => $weight, 'shippingType' => $bookingType, 'cashHandling' => $cash, 'insuranceCharges' => $insurance, 'returnCharges' => $return, 'fuelCharges' => $fuel, 'packagingCharges' => $packaging, 'discountCharges' => $discount,'discount_weight_rates' => $discount_weight_rates, 'sale_person' => $sale_person, 'packaging_material_types' => $packaging_material_types, 'packaging_type_ids' => $packaging_type_ids, 'packaging_charges' => $packaging_charges, 'wms_user_info' => $wms_user_info, 'wms_product_charges' => $wms_product_charges, 'wms_square_foot_charges' => $wms_square_foot_charges, 'wms_packing_charges' => $wms_packing_charges, 'wms_labelling_charges' => $wms_labelling_charges, 'wms_storage_charges' => $wms_storage_charges, 'invoicing_cycles' => $invoicing_cycles, 'storage_types' => $storage_types, 'rate_remarks' => $rate_remarks, 'sales_commission' => $sales_commission, 'overnight_origins' => $overnight_origins, 'overland_origins' => $overland_origins, 'detain_origins' => $detain_origins, 'sameday_origins' => $sameday_origins, 'overnight_destinations' => $overnight_destinations, 'overland_destinations' => $overland_destinations, 'detain_destinations' => $detain_destinations, 'sameday_destinations' => $sameday_destinations, 'cities' => $cities, 'sms_charges' => $sms_charge,'zero_cod_discount'=>$zero_cod_discount,'return_discount_charges'=>$return_discount_charges]);
        }
    }


    public function editRatesView($id)
    {
        $user = User::find($id);

        $pending_dws = PendingDwsWeightCharges::where('user_id', $id);
        if($pending_dws->exists()){
            $dws_weight = $pending_dws;

        }else{
            $dws_weight = DwsWeightCharges::where('user_id', $id);
        }

        $on_dws_charges = null;
        $ol_dws_charges = null;
        $detain_dws_charges = null;
        $sameday_dws_charges = null;

        if ($dws_weight->exists()) {
            $dws_weight = $dws_weight->get();
            foreach ($dws_weight as $value) {
                if ($value->shipping_mode_id == 1) {
                    $on_dws_charges = $value->dws_weight_status;

                } elseif ($value->shipping_mode_id == 2) {
                    $ol_dws_charges = $value->dws_weight_status;

                } elseif ($value->shipping_mode_id == 3) {
                    $detain_dws_charges = $value->dws_weight_status;

                } elseif ($value->shipping_mode_id == 4) {
                    $sameday_dws_charges = $value->dws_weight_status;

                }
            }
        }
        $sale_person = SalePersonTag::where('user_id', $id)->where('status', 0)->first();
        $minimum_chargeable_weights = MinimumChargeableWeightSetting::get();
        $on = null;
        $ol = null;
        $det = null;
        $same_day = null;
        foreach ($minimum_chargeable_weights as $minimum_chargeable_weight) {
            if ($minimum_chargeable_weight->shipping_mode_id == 1) {
                $on = $minimum_chargeable_weight->weight;
            } elseif ($minimum_chargeable_weight->shipping_mode_id == 2) {
                $ol = $minimum_chargeable_weight->weight;
            } elseif ($minimum_chargeable_weight->shipping_mode_id == 3) {
                $det = $minimum_chargeable_weight->weight;
            } else {
                $same_day = $minimum_chargeable_weight->weight;
            }
        }

        $commission_percentage = '';
        $settings = GlobalSettings::where('type', 'commission_percentage');
        if ($settings->exists()) {
            $settings = $settings->first();
            $commission_percentage = $settings->text;
        }
        $sales_tiers = SalesTier::where('status', 1)->get(['id', 'tier_name', 'tier_type', 'commission', 'sales_status']);
        $admin_users = Admin::leftjoin('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.id', 'admins.name','ar.department_id','admins.trax_id'])->where('admins.status', 1)->get();
        $riders_permanent = Rider::where('rider_type_id', 1)->get();

        $users = array();
        $sales = array();
        $all_users = array();

        foreach ($admin_users as $admin_user) {
            if ($admin_user->department_id != 7) {
                $users[] = array('id' => $admin_user->id, 'text' => $admin_user->name . '-' . $admin_user->trax_id);
            } else {
                $sales[] = array('id' => $admin_user->id, 'text' => $admin_user->name .  '-' . $admin_user->trax_id);
            }
        }

        $all_users['results'][0]['text'] = 'Sales';
        $all_users['results'][0]['children'] = $sales;
        $all_users['results'][1]['text'] = 'Admins';
        $all_users['results'][1]['children'] = $users;
        $all_users['results'][2]['text'] ='Riders';
        $all_users['results'][2]['children'] = [];
        $all_users['pagination']['more'] = true;

        $existing_commission_array = array();
        $sale_commission = SalesCommission::where('shipper_id', $user->id)->first();
        if ($sale_commission) {
            $sale_commission_users = SalesCommissionUser::where('sales_commission_id', $sale_commission->id)->get();
            if ($sale_commission_users) {
                foreach ($sale_commission_users as $index => $sale_commission_user) {
                    $sales_tier = SalesTier::find($sale_commission_user->tier_id);
                    if ($sales_tier) {
                        $existing_commission_array[$index]['sales_commission_id'] = $sale_commission_user->sales_commission_id;
                        $existing_commission_array[$index]['tier_type_id'] = $sales_tier->tier_type;
                        $existing_commission_array[$index]['tier_id'] = $sale_commission_user->tier_id;
                        $existing_commission_array[$index]['tier_name'] = $sales_tier->tier_name;
                        if ($sales_tier->tier_type == 1) {
                            if($sale_commission_user->user_type == "1"){
                                $com_admin = Admin::find($sale_commission_user->user_id);
                            }else{
                                $com_admin = Rider::find($sale_commission_user->user_id);                            
                            }                            
                            $existing_commission_array[$index]['user_name'] = $com_admin['name'];
                            $existing_commission_array[$index]['user_id'] = $com_admin['id'];
                        } else if ($sales_tier->tier_type == 2) {
                            $external_user = SalesCommissionExternalUser::find($sale_commission_user->user_id);
                            $existing_commission_array[$index]['user_name'] = $external_user->name;
                        }
                        $existing_commission_array[$index]['commission'] = $sale_commission_user->commission;
                    }
                }
            }
        }

        if ((($user['rate_status'] >= 0) && ($user['status'] == 1 || $user['status'] == 5)) || (($user['rate_status'] == 0) && $user['status'] == 3)) {
            $switches = RateStatus::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $sms_charge = User::where('id', $id)->select(['id','sms_charges','sms_charges_status'])->first();
            $weight = WeightCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $bookingType = BookingTypeCharges::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $cash = CashHandlingCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $insurance = InsuranceCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $return = ReturnCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $fuel = FuelSurcharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $packaging = PackagingCharge::all()->where('user_id', $id);
            $packaging_type_ids = array_unique($packaging->pluck('type_id')->toArray());
            $wms_user_info = WmsUserInformation::where('user_id', $id)->first();
            $wms_product_charges = WmsPerProductCharge::where('user_id', $id)->first();
            $wms_square_foot_charges = WmsPerSquareFootCharge::where('user_id', $id)->first();
            $wms_packing_charges = WmsPackingCharge::where('user_id', $id)->get();
            $wms_labelling_charges = WmsLabellingCharge::where('user_id', $id)->first();
            $wms_storage_charges = WmsStorageTypeCharge::where('user_id', $id)->get();
            $storage_types = WmsStorageType::all()->where('status', 1);
            $invoicing_cycles = InvoicingCycle::where('id', '!=', 2)->get();
            $discount = DiscountCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $discount_weight_charges = DiscountWeightCharge::all()->where('user_id', $id)->groupBy(['shipping_mode_id','destination_id']);
            $rate_status = $user['rate_status'];
            $packaging_material_types = PackagingMaterialTypes::with(['sizes'])->where('status', 1)->get();
            $rate_remarks = RateRemark::where('user_id', $id)->orderBy('created_at', 'desc')->get();
            $zero_cod_discount = ZeroCodDiscountCharges::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $return_discount_charges = ShipmentReturnDiscountCharges::all()->where('user_id', $id)->groupBy('shipping_mode_id');

            $packaging_sizes = array();
            if (count($packaging_material_types) > 0) {

                foreach ($packaging_material_types as $type) {
                    $packaging_sizes[$type->id] = $type->sizes;
                }
            }

            $packaging_charges = array();
            if (count($packaging) > 0) {

                foreach ($packaging as $charge) {
                    $packaging_charges[$charge->type_id][] = $charge;
                }
            }

            $existing = 0;

            $cities = City::where('status', 1)->where('business_category_id', 1)->select('id', 'name')->get();

            $rate_origin_hubs = RateOriginHub::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $rate_destination_hubs = RateDestinationHub::all()->where('user_id', $id)->groupBy('shipping_mode_id');

            $overnight_origins = [];
            $overland_origins = [];
            $detain_origins = [];
            $sameday_origins = [];
            if ($rate_origin_hubs || count($rate_origin_hubs) > 0) {
                foreach ($rate_origin_hubs as $index => $origin) {

                    if ($index == 1) {
                        foreach ($origin as $origin_data) {
                            $overnight_origins[] = $origin_data->city_id;
                        }
                    } else if ($index == 2) {
                        foreach ($origin as $origin_data) {
                            $overland_origins[] = $origin_data->city_id;
                        }
                    } else if ($index == 3) {
                        foreach ($origin as $origin_data) {
                            $detain_origins[] = $origin_data->city_id;
                        }
                    } else if ($index == 4) {
                        foreach ($origin as $origin_data) {
                            $sameday_origins[] = $origin_data->city_id;
                        }
                    }

                }
            }
            $overnight_destinations = [];
            $overland_destinations = [];
            $detain_destinations = [];
            $sameday_destinations = [];
            if ($rate_destination_hubs || count($rate_destination_hubs) > 0) {
                foreach ($rate_destination_hubs as $index => $destination) {

                    if ($index == 1) {
                        foreach ($destination as $destination_data) {
                            $overnight_destinations[] = $destination_data->city_id;
                        }
                    } else if ($index == 2) {
                        foreach ($destination as $destination_data) {
                            $overland_destinations[] = $destination_data->city_id;
                        }
                    } else if ($index == 3) {
                        foreach ($destination as $destination_data) {
                            $detain_destinations[] = $destination_data->city_id;
                        }
                    } else if ($index == 4) {
                        foreach ($destination as $destination_data) {
                            $sameday_destinations[] = $destination_data->city_id;
                        }
                    }

                }
            }
            if (session('department_id') == 7) {
                if ($sale_person['admin_id'] == Auth::id() || in_array(session('id'), session('sale_users_bypass')) || in_array($id, session('tagged_shippers'))) {
                    return view('admin.accounts.edit_rates')->with(['riders_permanents'=>$riders_permanent,'sameday_dws_charges' => $sameday_dws_charges, 'detain_dws_charges' => $detain_dws_charges, 'ol_dws_charges' => $ol_dws_charges, 'on_dws_charges' => $on_dws_charges, 'shipper' => $user, 'switches' => $switches, 'weight' => $weight, 'shippingType' => $bookingType, 'cashHandling' => $cash, 'insuranceCharges' => $insurance, 'returnCharges' => $return, 'fuelCharges' => $fuel, 'discountCharges' => $discount,'discount_weight_charges' => $discount_weight_charges, 'rate_status' => $rate_status, 'sale_person' => $sale_person, 'packaging_material_types' => $packaging_material_types, 'packaging_type_ids' => $packaging_type_ids, 'packaging_charges' => $packaging_charges, 'wms_user_info' => $wms_user_info, 'wms_product_charges' => $wms_product_charges, 'wms_square_foot_charges' => $wms_square_foot_charges, 'wms_packing_charges' => $wms_packing_charges, 'wms_labelling_charges' => $wms_labelling_charges, 'wms_storage_charges' => $wms_storage_charges, 'invoicing_cycles' => $invoicing_cycles, 'storage_types' => $storage_types, 'existing' => $existing, 'packaging_material_type_sizes' => $packaging_sizes, 'rate_remarks' => $rate_remarks, 'on' => $on, 'ol' => $ol, 'det' => $det, 'same_day' => $same_day, 'commission_percentage' => $commission_percentage, 'sales_tiers' => $sales_tiers, 'users' => $all_users, 'existing_commission_array' => $existing_commission_array, 'overnight_origins' => $overnight_origins, 'overland_origins' => $overland_origins, 'detain_origins' => $detain_origins, 'sameday_origins' => $sameday_origins, 'overnight_destinations' => $overnight_destinations, 'overland_destinations' => $overland_destinations, 'detain_destinations' => $detain_destinations, 'sameday_destinations' => $sameday_destinations, 'cities' => $cities, 'sms_charge'=> $sms_charge,'zero_cod_discount'=>$zero_cod_discount,'return_discount_charges'=>$return_discount_charges]);
                } else {
                    return view('admin.access_denied');
                }
            } else {
                return view('admin.accounts.edit_rates')->with(['riders_permanents'=>$riders_permanent,'sameday_dws_charges' => $sameday_dws_charges, 'detain_dws_charges' => $detain_dws_charges, 'ol_dws_charges' => $ol_dws_charges, 'on_dws_charges' => $on_dws_charges, 'shipper' => $user, 'switches' => $switches, 'weight' => $weight, 'shippingType' => $bookingType, 'cashHandling' => $cash, 'insuranceCharges' => $insurance, 'returnCharges' => $return, 'fuelCharges' => $fuel, 'discountCharges' => $discount,'discount_weight_charges' => $discount_weight_charges, 'rate_status' => $rate_status, 'sale_person' => $sale_person, 'packaging_material_types' => $packaging_material_types, 'packaging_type_ids' => $packaging_type_ids, 'packaging_charges' => $packaging_charges, 'existing' => $existing, 'wms_user_info' => $wms_user_info, 'wms_product_charges' => $wms_product_charges, 'wms_square_foot_charges' => $wms_square_foot_charges, 'wms_packing_charges' => $wms_packing_charges, 'wms_labelling_charges' => $wms_labelling_charges, 'wms_storage_charges' => $wms_storage_charges, 'invoicing_cycles' => $invoicing_cycles, 'storage_types' => $storage_types, 'packaging_material_type_sizes' => $packaging_sizes, 'rate_remarks' => $rate_remarks, 'on' => $on, 'ol' => $ol, 'det' => $det, 'same_day' => $same_day, 'commission_percentage' => $commission_percentage, 'sales_tiers' => $sales_tiers, 'users' => $all_users, 'existing_commission_array' => $existing_commission_array, 'overnight_origins' => $overnight_origins, 'overland_origins' => $overland_origins, 'detain_origins' => $detain_origins, 'sameday_origins' => $sameday_origins, 'overnight_destinations' => $overnight_destinations, 'overland_destinations' => $overland_destinations, 'detain_destinations' => $detain_destinations, 'sameday_destinations' => $sameday_destinations, 'cities' => $cities, 'sms_charge' => $sms_charge,'zero_cod_discount'=>$zero_cod_discount,'return_discount_charges'=>$return_discount_charges]);
            }

        } elseif (($user['rate_status'] >= 1) && ($user['status'] == 3)) {
            $e_switches = RateStatus::all()->where('user_id', $id)->groupBy('shipping_mode_id');
//        return $switches;
//        var_dump(empty($switches));exit();
            $e_weight = WeightCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
//        $cash = '';
            $e_sms_charge = User::where('id', $id)->select(['id','sms_charges','sms_charges_status'])->first();
            $e_bookingType = BookingTypeCharges::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $e_cash = CashHandlingCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $e_insurance = InsuranceCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $e_return = ReturnCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $e_fuel = FuelSurcharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $e_packaging = PackagingCharge::all()->where('user_id', $id);
            $e_packaging_type_ids = array_unique($e_packaging->pluck('type_id')->toArray());
            $e_wms_user_info = WmsUserInformation::where('user_id', $id)->first();
            $e_wms_product_charges = WmsPerProductCharge::where('user_id', $id)->first();
            $e_wms_square_foot_charges = WmsPerSquareFootCharge::where('user_id', $id)->first();
            $e_wms_packing_charges = WmsPackingCharge::where('user_id', $id)->get();
            $e_wms_labelling_charges = WmsLabellingCharge::where('user_id', $id)->first();
            $e_wms_storage_charges = WmsStorageTypeCharge::where('user_id', $id)->get();
            $e_storage_types = WmsStorageType::all()->where('status', 1);
            $e_invoicing_cycles = InvoicingCycle::where('id', '!=', 2)->get();
            $e_discount = DiscountCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $e_discount_weight_charges = DiscountWeightCharge::all()->where('user_id', $id)->groupBy(['shipping_mode_id','destination_id']);

            $e_rate_status = $user['rate_status'];
            $e_packaging_material_types = PackagingMaterialTypes::with(['sizes'])->where('status', 1)->get();

            $e_zero_cod_discount = ZeroCodDiscountCharges::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $e_return_discount_charges = ShipmentReturnDiscountCharges::all()->where('user_id', $id)->groupBy('shipping_mode_id');

            $e_packaging_charges = array();
            if (count($e_packaging) > 0) {

                foreach ($e_packaging as $e_charge) {
                    $e_packaging_charges[$e_charge->type_id][] = $e_charge;
                }
            }

            $switches = PendingRateStatus::all()->where('user_id', $id)->groupBy('shipping_mode_id');

            $weight = PendingWeightCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $sms_charge = PendingSmsCharges::where('user_id', $id)->first();
            $bookingType = PendingBookingTypeCharges::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $cash = PendingCashHandlingCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $insurance = PendingInsuranceCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $return = PendingReturnCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $fuel = PendingFuelSurcharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $packaging = PendingPackagingCharge::all()->where('user_id', $id);
            $discount = PendingDiscountCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $discount_weight_charges = PendingDiscountWeightCharge::all()->where('user_id', $id)->groupBy(['shipping_mode_id','destination_id']);
            $rate_status = $user['rate_status'];
            $packaging_material_types = PackagingMaterialTypes::where('status', 1)->get();
            $packaging_type_ids = array_unique($packaging->pluck('type_id')->toArray());
            $wms_user_info = WmsPendingUserInformation::where('user_id', $id)->first();
            $wms_product_charges = WmsPendingPerProductCharge::where('user_id', $id)->first();
            $wms_square_foot_charges = WmsPendingPerSquareFootCharge::where('user_id', $id)->first();
            $wms_packing_charges = WmsPendingPackingCharge::where('user_id', $id)->get();
            $wms_labelling_charges = WmsPendingLabellingCharge::where('user_id', $id)->first();
            $wms_storage_charges = WmsPendingStorageTypeCharge::where('user_id', $id)->get();
            $storage_types = WmsStorageType::all()->where('status', 1);
            $invoicing_cycles = InvoicingCycle::where('id', '!=', 2)->get();
            $rate_remarks = RateRemark::where('user_id', $id)->orderBy('created_at', 'desc')->get();
            $zero_cod_discount = PendingZeroCodDiscountCharges::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $return_discount_charges = PendingShipmentReturnDiscountCharges::all()->where('user_id', $id)->groupBy('shipping_mode_id');

            
            $packaging_charges = array();
            $packaging_sizes = array();
            if (count($packaging_material_types) > 0) {

                foreach ($packaging_material_types as $type) {
                    $packaging_sizes[$type->id] = $type->sizes;
                }
            }
            if (count($packaging) > 0) {

                foreach ($packaging as $charge) {
                    $packaging_charges[$charge->type_id][] = $charge;
                }
            }
            $cities = City::where('status', 1)->where('business_category_id', 1)->select('id', 'name')->get();

            $rate_origin_hubs = PendingRateOriginHub::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $rate_destination_hubs = PendingRateDestinationHub::all()->where('user_id', $id)->groupBy('shipping_mode_id');

            $overnight_origins = [];
            $overland_origins = [];
            $detain_origins = [];
            $sameday_origins = [];
            if ($rate_origin_hubs || count($rate_origin_hubs) > 0) {
                foreach ($rate_origin_hubs as $index => $origin) {

                    if ($index == 1) {
                        foreach ($origin as $origin_data) {
                            $overnight_origins[] = $origin_data->city_id;
                        }
                    } else if ($index == 2) {
                        foreach ($origin as $origin_data) {
                            $overland_origins[] = $origin_data->city_id;
                        }
                    } else if ($index == 3) {
                        foreach ($origin as $origin_data) {
                            $detain_origins[] = $origin_data->city_id;
                        }
                    } else if ($index == 4) {
                        foreach ($origin as $origin_data) {
                            $sameday_origins[] = $origin_data->city_id;
                        }
                    }

                }
            }
            $overnight_destinations = [];
            $overland_destinations = [];
            $detain_destinations = [];
            $sameday_destinations = [];
            if ($rate_destination_hubs || count($rate_destination_hubs) > 0) {
                foreach ($rate_destination_hubs as $index => $destination) {

                    if ($index == 1) {
                        foreach ($destination as $destination_data) {
                            $overnight_destinations[] = $destination_data->city_id;
                        }
                    } else if ($index == 2) {
                        foreach ($destination as $destination_data) {
                            $overland_destinations[] = $destination_data->city_id;
                        }
                    } else if ($index == 3) {
                        foreach ($destination as $destination_data) {
                            $detain_destinations[] = $destination_data->city_id;
                        }
                    } else if ($index == 4) {
                        foreach ($destination as $destination_data) {
                            $sameday_destinations[] = $destination_data->city_id;
                        }
                    }

                }
            }
            $existing = 1;
            if (session('department_id') == 7) {
                if ($sale_person['admin_id'] == Auth::id() || in_array(session('id'), session('sale_users_bypass')) || in_array($id, session('tagged_shippers'))) {
                    return view('admin.accounts.edit_rates')->with(['riders_permanents'=>$riders_permanent,'sameday_dws_charges' => $sameday_dws_charges, 'detain_dws_charges' => $detain_dws_charges, 'ol_dws_charges' => $ol_dws_charges, 'on_dws_charges' => $on_dws_charges, 'shipper' => $user, 'switches' => $switches, 'weight' => $weight, 'shippingType' => $bookingType, 'cashHandling' => $cash, 'insuranceCharges' => $insurance, 'returnCharges' => $return, 'fuelCharges' => $fuel, 'discountCharges' => $discount,'discount_weight_charges'=>$discount_weight_charges, 'rate_status' => $rate_status, 'sale_person' => $sale_person, 'packaging_material_types' => $packaging_material_types, 'packaging_type_ids' => $packaging_type_ids, 'packaging_charges' => $packaging_charges, 'wms_user_info' => $wms_user_info, 'wms_product_charges' => $wms_product_charges, 'wms_square_foot_charges' => $wms_square_foot_charges, 'wms_packing_charges' => $wms_packing_charges, 'wms_labelling_charges' => $wms_labelling_charges, 'wms_storage_charges' => $wms_storage_charges, 'invoicing_cycles' => $invoicing_cycles, 'storage_types' => $storage_types, 'e_switches' => $e_switches, 'e_weight' => $e_weight, 'e_shippingType' => $e_bookingType, 'e_cashHandling' => $e_cash, 'e_insuranceCharges' => $e_insurance, 'e_returnCharges' => $e_return, 'e_fuelCharges' => $e_fuel, 'e_discountCharges' => $e_discount,'e_discount_weight_charges'=>$e_discount_weight_charges, 'e_rate_status' => $e_rate_status, 'e_packaging_material_types' => $e_packaging_material_types, 'e_packaging_type_ids' => $e_packaging_type_ids, 'e_packaging_charges' => $e_packaging_charges, 'e_wms_user_info' => $e_wms_user_info, 'e_wms_product_charges' => $e_wms_product_charges, 'e_wms_square_foot_charges' => $e_wms_square_foot_charges, 'e_wms_packing_charges' => $e_wms_packing_charges, 'e_wms_labelling_charges' => $e_wms_labelling_charges, 'e_wms_storage_charges' => $e_wms_storage_charges, 'e_invoicing_cycles' => $e_invoicing_cycles, 'e_storage_types' => $e_storage_types, 'existing' => $existing, 'packaging_material_type_sizes' => $packaging_sizes, 'rate_remarks' => $rate_remarks, 'on' => $on, 'ol' => $ol, 'det' => $det, 'same_day' => $same_day, 'commission_percentage' => $commission_percentage, 'sales_tiers' => $sales_tiers, 'users' => $all_users, 'existing_commission_array' => $existing_commission_array, 'overnight_origins' => $overnight_origins, 'overland_origins' => $overland_origins, 'detain_origins' => $detain_origins, 'sameday_origins' => $sameday_origins, 'overnight_destinations' => $overnight_destinations, 'overland_destinations' => $overland_destinations, 'detain_destinations' => $detain_destinations, 'sameday_destinations' => $sameday_destinations, 'cities' => $cities, 'sms_charge'=>$sms_charge ,'e_sms_charge'=>$e_sms_charge,'zero_cod_discount'=>$zero_cod_discount,'return_discount_charges'=>$return_discount_charges,'e_zero_cod_discount'=>$e_zero_cod_discount,'e_return_discount_charges'=>$e_return_discount_charges]);
                } else {
                    return view('admin.access_denied');
                }
            } else {
                return view('admin.accounts.edit_rates')->with(['riders_permanents'=>$riders_permanent,'sameday_dws_charges' => $sameday_dws_charges, 'detain_dws_charges' => $detain_dws_charges, 'ol_dws_charges' => $ol_dws_charges, 'on_dws_charges' => $on_dws_charges, 'shipper' => $user, 'switches' => $switches, 'weight' => $weight, 'shippingType' => $bookingType, 'cashHandling' => $cash, 'insuranceCharges' => $insurance, 'returnCharges' => $return, 'fuelCharges' => $fuel, 'discountCharges' => $discount,'discount_weight_charges'=>$discount_weight_charges, 'rate_status' => $rate_status, 'sale_person' => $sale_person, 'packaging_material_types' => $packaging_material_types, 'packaging_type_ids' => $packaging_type_ids, 'packaging_charges' => $packaging_charges, 'existing' => $existing, 'wms_user_info' => $wms_user_info, 'wms_product_charges' => $wms_product_charges, 'wms_square_foot_charges' => $wms_square_foot_charges, 'wms_packing_charges' => $wms_packing_charges, 'wms_labelling_charges' => $wms_labelling_charges, 'wms_storage_charges' => $wms_storage_charges, 'invoicing_cycles' => $invoicing_cycles, 'storage_types' => $storage_types, 'e_switches' => $e_switches, 'e_weight' => $e_weight, 'e_shippingType' => $e_bookingType, 'e_cashHandling' => $e_cash, 'e_insuranceCharges' => $e_insurance, 'e_returnCharges' => $e_return, 'e_fuelCharges' => $e_fuel, 'e_discountCharges' => $e_discount,'e_discount_weight_charges'=>$e_discount_weight_charges, 'e_rate_status' => $e_rate_status, 'e_packaging_material_types' => $e_packaging_material_types, 'e_packaging_type_ids' => $e_packaging_type_ids, 'e_packaging_charges' => $e_packaging_charges, 'e_wms_user_info' => $e_wms_user_info, 'e_wms_product_charges' => $e_wms_product_charges, 'e_wms_square_foot_charges' => $e_wms_square_foot_charges, 'e_wms_packing_charges' => $e_wms_packing_charges, 'e_wms_labelling_charges' => $e_wms_labelling_charges, 'e_wms_storage_charges' => $e_wms_storage_charges, 'e_invoicing_cycles' => $e_invoicing_cycles, 'e_storage_types' => $e_storage_types, 'packaging_material_type_sizes' => $packaging_sizes, 'rate_remarks' => $rate_remarks, 'on' => $on, 'ol' => $ol, 'det' => $det, 'same_day' => $same_day, 'commission_percentage' => $commission_percentage, 'sales_tiers' => $sales_tiers, 'users' => $all_users, 'existing_commission_array' => $existing_commission_array, 'overnight_origins' => $overnight_origins, 'overland_origins' => $overland_origins, 'detain_origins' => $detain_origins, 'sameday_origins' => $sameday_origins, 'overnight_destinations' => $overnight_destinations, 'overland_destinations' => $overland_destinations, 'detain_destinations' => $detain_destinations, 'sameday_destinations' => $sameday_destinations, 'cities' => $cities, 'sms_charge'=>$sms_charge, 'e_sms_charge'=>$e_sms_charge,'zero_cod_discount'=>$zero_cod_discount,'return_discount_charges'=>$return_discount_charges,'e_zero_cod_discount'=>$e_zero_cod_discount,'e_return_discount_charges'=>$e_return_discount_charges]);
            }
        } else {
            return redirect(route('admin.accounts.pending'));
        }
    }

    public function editRates(Request $request, $id)
    {
        $user = User::find($id);
        if ($user['status'] != 3) {
            $messages = [
                'sms_charges.required' => 'The sms charges field is required.',
                'sms_charges.gt' => 'The sms charges must be greater than 0.',
                'on_wa_range_up.*.required' => 'The overnight range up field is required.',
                'on_wa_range_up.*.numeric' => 'The overnight range up field must be numeric or decimal.',
                'on_wa_range_up.*.between' => 'The overnight range up field must be between 0 to 999.99',
                'on_wa_range_down.*.required' => 'The overnight range down field is required.',
                'on_wa_range_down.*.numeric' => 'The overnight range down field must be numeric or decimal.',
                'on_wa_range_down.*.between' => 'The overnight range down field must be between 0 to 999.99',
                'on_wa_spkg.*.numeric' => 'The overnight KG Range field must be numeric.',
                'on_wa_local_charges.*.required' => 'The overnight local charges field is required.',
                'on_wa_local_charges.*.numeric' => 'The overnight local charges field must be numeric.',
                'on_class_0_charges.*.numeric' => 'The overnight class A charges field must be numeric.',
                'on_class_0_charges.*.required' => 'The overnight class A charges field is required.',
                'on_class_1_charges.*.required' => 'The overnight class B charges field is required.',
                'on_class_2_charges.*.required' => 'The overnight class C charges field is required.',
                'on_class_3_charges.*.required' => 'The overnight class D charges field is required.',
                'on_replacement_charges.numeric' => 'The overnight replacement charges field must be numeric.',
                'on_replacement_charges.required' => 'The overnight replacement charges field is required.',
                'on_tnb_charges.numeric' => 'The overnight try and buy charges field must be numeric.',
                'on_tnb_charges.required' => 'The overnight try and buy charges field is required.',
                'on_reverse_charges.numeric' => 'The overnight reverse pickup charges field must be numeric.',
                'on_reverse_charges.required' => 'The overnight reverse pickup charges field is required.',
                'on_cash_range_up.*.required_if' => 'The overnight cash range up field is required.',
                'on_cash_range_up.*.numeric' => 'The overnight cash range up field must be numeric.',
                'on_cash_range_down.*.required_if' => 'The overnight cash range down field is required.',
                'on_cash_range_down.*.numeric' => 'The overnight cash range down field must be numeric.',
                'on_cash_charges.*.required_if' => 'The overnight cash charges field is required.',

                'on_ins_range_up.*.required_if' => 'The overnight insurance range up field is required.',
                'on_ins_range_up.*.numeric' => 'The overnight insurance range up field must be numeric or percentage.',
                'on_ins_range_down.*.required_if' => 'The overnight insurance range down field is required.',
                'on_ins_range_down.*.numeric' => 'The overnight insurance range down field must be numeric or percentage.',
                'on_ins_charges.*.required_if' => 'The overnight insurance charges field is required.',

                'on_return_local_charges.required_if' => 'The overnight return local charges field is required.',
                'on_return_local_charges.numeric' => 'The overnight return local charges field must be numeric or percentage.',
                'on_return_class_0_charges.*.numeric' => 'The overnight class A return charges field must be numeric.',
                'on_return_class_0_charges.*.required_if' => 'The overnight class A return charges field is required.',
                'on_return_class_1_charges.*.required_if' => 'The overnight class B return charges field is required.',
                'on_return_class_2_charges.*.required_if' => 'The overnight class C return charges field is required.',
                'on_return_class_3_charges.*.required_if' => 'The overnight class D return charges field is required.',
                'overnight_fuel_surcharge.required_if' => 'The overnight return national charges field is required.',
                'overnight_fuel_surcharge.numeric' => 'The overnight return national charges field must be numeric or percentage.',

                'on_discount_title.required_if' => 'The overnight discount title field must be required',
                'on_daterange.required_if' => 'The overnight discount date range field must be required',
                'on_discount_weight_rate.required_if' => 'The overnight discount weight field must be required',
                'on_discount_cash_rate.required_if' => 'The overnight discount cash field must be required',
                'on_discount_insurance_rate.required_if' => 'The overnight discount insurance field must be required',
                'on_discount_return_rate.required_if' => 'The overnight discount return field must be required',
                'on_discount_packaging_rate.required_if' => 'The overnight discount packaging field must be required',
                'on_discount_title.required_with' => 'The overnight discount title field is required',
                'on_daterange.required_with' => 'The overnight discount date field is required',
                //overland starts
                'ol_wa_range_up.*.required' => 'The overland range up field is required.',
                'ol_wa_range_up.*.numeric' => 'The overland range up field must be numeric or decimal.',
                'ol_wa_range_up.*.between' => 'The overland range up field must be between 0 to 999.99',
                'ol_wa_range_down.*.required' => 'The overland range down field is required.',
                'ol_wa_range_down.*.numeric' => 'The overland range down field must be numeric or decimal.',
                'ol_wa_range_down.*.between' => 'The overland range down field must be between 0 to 999.99',
                'ol_wa_spkg.*.numeric' => 'The overland KG Range field must be numeric.',
                'ol_wa_local_charges.*.required' => 'The overland local charges field is required.',
                'ol_wa_local_charges.*.numeric' => 'The overland local charges field must be numeric.',
                'ol_class_0_charges.*.required' => 'The overland class A charges field is required.',
                'ol_class_0_charges.*.numeric' => 'The overland class A charges field must be numeric.',
                'ol_class_1_charges.*.required' => 'The overland class B charges field is required.',
                'ol_class_2_charges.*.required' => 'The overland class C charges field is required.',
                'ol_class_3_charges.*.required' => 'The overland class D charges field is required.',
                'ol_replacement_charges.numeric' => 'The overland replacement charges field must be numeric.',
                'ol_replacement_charges.required' => 'The overland replacement charges field is required.',
                'ol_tnb_charges.numeric' => 'The overland try and buy charges field must be numeric.',
                'ol_tnb_charges.required' => 'The overland try and buy charges field is required.',
                'ol_reverse_charges.numeric' => 'The overland reverse pickup charges field must be numeric.',
                'ol_reverse_charges.required' => 'The overland reverse pickup charges field is required.',
                'ol_cash_range_up.*.required_if' => 'The overland cash range up field is required.',
                'ol_cash_range_up.*.numeric' => 'The overland cash range up field must be numeric.',
                'ol_cash_range_down.*.required_if' => 'The overland cash range down field is required.',
                'ol_cash_range_down.*.numeric' => 'The overland cash range down field must be numeric.',
                'ol_cash_charges.*.required_if' => 'The overland cash charges field is required.',
                'ol_ins_range_up.*.required_if' => 'The overland insurance range up field is required.',
                'ol_ins_range_up.*.numeric' => 'The overland insurance range up field must be numeric or percentage.',
                'ol_ins_range_down.*.required_if' => 'The overland insurance range down field is required.',
                'ol_ins_range_down.*.numeric' => 'The overland insurance range down field must be numeric or percentage.',
                'ol_ins_charges.*.required_if' => 'The overland insurance charges field is required.',
                'ol_return_local_charges.required_if' => 'The overland return local charges field is required.',
                'ol_return_local_charges.numeric' => 'The overland return local charges field must be numeric or percentage.',
                'ol_return_class_0_charges.*.required_if' => 'The overland class A return charges field is required.',
                'ol_return_class_1_charges.*.required_if' => 'The overland class B return charges field is required.',
                'ol_return_class_2_charges.*.required_if' => 'The overland class C return charges field is required.',
                'ol_return_class_3_charges.*.required_if' => 'The overland class D return charges field is required.',
                'overland_fuel_surcharge.required_if' => 'The overland return national charges field is required.',
                'overland_fuel_surcharge.numeric' => 'The overland return national charges field must be numeric or percentage.',

                'ol_discount_title.required_if' => 'The overland discount title field must be required',
                'ol_daterange.required_if' => 'The overland discount date range field must be required',
                'ol_discount_weight_rate.required_if' => 'The overland discount weight field must be required',
                'ol_discount_cash_rate.required_if' => 'The overland discount cash field must be required',
                'ol_discount_insurance_rate.required_if' => 'The overland discount insurance field must be required',
                'ol_discount_return_rate.required_if' => 'The overland discount return field must be required',
                'ol_discount_packaging_rate.required_if' => 'The overland discount packaging field must be required',
                'ol_discount_title.required_with' => 'The overland discount title field is required',
                'ol_daterange.required_with' => 'The overland discount date field is required',
                //overland end and detain starts
                'detain_wa_range_up.*.required' => 'The detain range up field is required.',
                'detain_wa_range_up.*.numeric' => 'The detain range up field must be numeric or decimal.',
                'detain_wa_range_up.*.between' => 'The detain range up field must be between 0 to 999.99',
                'detain_wa_range_down.*.required' => 'The detain range down field is required.',
                'detain_wa_range_down.*.numeric' => 'The detain range down field must be numeric or decimal.',
                'detain_wa_range_down.*.between' => 'The detain range down field must be between 0 to 999.99',
                'detain_wa_spkg.*.numeric' => 'The detain KG Range field must be numeric.',
                'detain_wa_local_charges.*.required' => 'The detain local charges field is required.',
                'detain_wa_local_charges.*.numeric' => 'The detain local charges field must be numeric.',
                'detain_class_0_charges.*.required' => 'The detain class A charges field is required.',
                'detain_class_0_charges.*.numeric' => 'The detain class A charges field must be numeric.',
                'detain_class_1_charges.*.required' => 'The detain class B charges field is required.',
                'detain_class_2_charges.*.required' => 'The detain class C charges field is required.',
                'detain_class_3_charges.*.required' => 'The detain class D charges field is required.',
                'detain_replacement_charges.numeric' => 'The detain replacement charges field must be numeric.',
                'detain_replacement_charges.required' => 'The detain replacement charges field is required.',
                'detain_tnb_charges.numeric' => 'The detain try and buy charges field must be numeric.',
                'detain_tnb_charges.required' => 'The detain try and buy charges field is required.',
                'detain_reverse_charges.numeric' => 'The detain reverse pickup charges field must be numeric.',
                'detain_reverse_charges.required' => 'The detain reverse pickup charges field is required.',
                'detain_cash_range_up.*.required_if' => 'The detain cash range up field is required.',
                'detain_cash_range_up.*.numeric' => 'The detain cash range up field must be numeric.',
                'detain_cash_range_down.*.required_if' => 'The detain cash range down field is required.',
                'detain_cash_range_down.*.numeric' => 'The detain cash range down field must be numeric.',
                'detain_cash_charges.*.required_if' => 'The detain cash charges field is required.',
                'detain_ins_range_up.*.required_if' => 'The detain insurance range up field is required.',
                'detain_ins_range_up.*.numeric' => 'The detain insurance range up field must be numeric or percentage.',
                'detain_ins_range_down.*.required_if' => 'The detain insurance range down field is required.',
                'detain_ins_range_down.*.numeric' => 'The detain insurance range down field must be numeric or percentage.',
                'detain_ins_charges.*.required_if' => 'The detain insurance charges field is required.',
                'detain_return_local_charges.required_if' => 'The detain return local charges field is required.',
                'detain_return_local_charges.numeric' => 'The detain return local charges field must be numeric or percentage.',
                'detain_return_class_0_charges.*.required_if' => 'The detain class A return charges field is required.',
                'detain_return_class_1_charges.*.required_if' => 'The detain class B return charges field is required.',
                'detain_return_class_2_charges.*.required_if' => 'The detain class C return charges field is required.',
                'detain_return_class_3_charges.*.required_if' => 'The detain class D return charges field is required.',
                'detain_fuel_surcharge.required_if' => 'The detain return national charges field is required.',
                'detain_fuel_surcharge.numeric' => 'The detain return national charges field must be numeric or percentage.',

                'detain_discount_title.required_if' => 'The detain discount title field must be required',
                'detain_daterange.required_if' => 'The detain discount date range field must be required',
                'detain_discount_weight_rate.required_if' => 'The detain discount weight field must be required',
                'detain_discount_cash_rate.required_if' => 'The detain discount cash field must be required',
                'detain_discount_insurance_rate.required_if' => 'The detain discount insurance field must be required',
                'detain_discount_return_rate.required_if' => 'The detain discount return field must be required',
                'detain_discount_packaging_rate.required_if' => 'The detain discount packaging field must be required',
                'detain_discount_title.required_with' => 'The detain discount title field is required',
                'detain_daterange.required_with' => 'The detain discount date field is required',
                //detain ends and sameday starts
                'sameday_wa_range_up.*.required' => 'The sameday range up field is required.',
                'sameday_wa_range_up.*.numeric' => 'The sameday range up field must be numeric or decimal.',
                'sameday_wa_range_up.*.between' => 'The sameday range up field must be between 0 to 999.99',
                'sameday_wa_range_down.*.required' => 'The sameday range down field is required.',
                'sameday_wa_range_down.*.numeric' => 'The sameday range down field must be numeric or decimal.',
                'sameday_wa_range_down.*.between' => 'The sameday range down field must be between 0 to 999.99',
                'sameday_wa_spkg.*.numeric' => 'The sameday KG Range field must be numeric.',
                'sameday_wa_local_charges.*.required' => 'The sameday local charges field is required.',
                'sameday_wa_local_charges.*.numeric' => 'The sameday local charges field must be numeric.',
                'sameday_wa_class_0_charges.*.required' => 'The sameday class A charges field is required.',
                'sameday_wa_class_0_charges.*.numeric' => 'The sameday class A charges field must be numeric.',
                'sameday_replacement_charges.numeric' => 'The sameday replacement charges field must be numeric.',
                'sameday_replacement_charges.required' => 'The sameday replacement charges field is required.',
                'sameday_tnb_charges.numeric' => 'The sameday try and buy charges field must be numeric.',
                'sameday_tnb_charges.required' => 'The sameday try and buy charges field is required.',
                'sameday_reverse_charges.numeric' => 'The sameday reverse pickup charges field must be numeric.',
                'sameday_reverse_charges.required' => 'The sameday reverse pickup charges field is required.',
                'sameday_cash_range_up.*.required_if' => 'The sameday cash range up field is required.',
                'sameday_cash_range_up.*.numeric' => 'The sameday cash range up field must be numeric.',
                'sameday_cash_range_down.*.required_if' => 'The sameday cash range down field is required.',
                'sameday_cash_range_down.*.numeric' => 'The sameday cash range down field must be numeric.',
                'sameday_cash_charges.*.required_if' => 'The sameday cash charges field is required.',
                'sameday_ins_range_up.*.required_if' => 'The sameday insurance range up field is required.',
                'sameday_ins_range_up.*.numeric' => 'The sameday insurance range up field must be numeric or percentage.',
                'sameday_ins_range_down.*.required_if' => 'The sameday insurance range down field is required.',
                'sameday_ins_range_down.*.numeric' => 'The sameday insurance range down field must be numeric or percentage.',
                'sameday_ins_charges.*.required_if' => 'The sameday insurance charges field is required.',
                'sameday_return_local_charges.required_if' => 'The sameday return local charges field is required.',
                'sameday_return_local_charges.numeric' => 'The sameday return local charges field must be numeric or percentage.',
                'sameday_return_class_0_charges.*.required_if' => 'The sameday class A return charges field is required.',
                'sameday_return_class_1_charges.*.required_if' => 'The sameday class B return charges field is required.',
                'sameday_return_class_2_charges.*.required_if' => 'The sameday class C return charges field is required.',
                'sameday_return_class_3_charges.*.required_if' => 'The sameday class D return charges field is required.',
                'sameday_fuel_surcharge.required_if' => 'The sameday return national charges field is required.',
                'sameday_fuel_surcharge.numeric' => 'The sameday return national charges field must be numeric or percentage.',

                'sameday_discount_title.required_if' => 'The sameday discount title field must be required',
                'sameday_daterange.required_if' => 'The sameday discount date range field must be required',
                'sameday_discount_weight_rate.required_if' => 'The sameday discount weight field must be required',
                'sameday_discount_cash_rate.required_if' => 'The sameday discount cash field must be required',
                'sameday_discount_insurance_rate.required_if' => 'The sameday discount insurance field must be required',
                'sameday_discount_return_rate.required_if' => 'The sameday discount return field must be required',
                'sameday_discount_packaging_rate.required_if' => 'The sameday discount packaging field must be required',
                'sameday_discount_title.required_with' => 'The sameday discount title field is required',
                'sameday_daterange.required_with' => 'The sameday discount date field is required',
                //sameday ends

                //warehouse starts
                'invoicing_cycle.*.required' => 'Invoicing Cycle field is required.',
                'invoicing_date.*.required' => 'Invoicing Cycle field is required.',
                'invoicing_cycle.*.numeric' => 'Invoicing Cycle field must be numeric.',
                'invoicing_date.*.numeric' => 'Invoicing Cycle field must be numeric.',
                'ppc_charges.required' => 'Per product charges field id required',
                'psf_charges.required' => 'Per square foot charges field id required',
                'storage_type.*.required_if' => 'Storage type field is required.',
                'storage_type_charges.*.required_if' => 'Storage type charges field is required',
                'storage_type.*.numeric' => 'Storage type field must be numeric.',
                'storage_type_charges.*.numeric' => 'Storage type charges field must be numeric',
                'packing_type.*.required' => 'Packing type field is required',
                'packing_charges.*.required' => 'Packing charges field is required',
                'packing_charges.*.numeric' => 'Packing charges field must be numeric',
                'labelling_charges.required' => 'Labelling charges field is required',
                'labelling_charges.numeric' => 'Labelling charges field must be numeric',

                'discount_on_destination.required_if' => 'Rush Destination Field is required if discount weight (destination-wise) toggle is on',
                'discount_ol_destination.required_if' => 'Saver Plus Destination Field is required if discount weight (destination-wise) toggle is on',
                'discount_d_destination.required_if' => 'Detain Destination Field is required if discount weight (destination-wise) toggle is on',
                'discount_sd_destination.required_if' => 'Same Day Destination Field is required if discount weight (destination-wise) toggle is on',

                'on_cod_discount_per' => 'Zero Cod Discount percentage is required',
                'on_return_discount_per' => 'Return Discount percentage is required',

                'ol_cod_discount_per' => 'Zero Cod Discount percentage is required',
                'ol_return_discount_per' => 'Return Discount percentage is required',

                'detain_cod_discount_per' => 'Zero Cod Discount percentage is required',
                'detain_return_discount_per' => 'Return Discount percentage is required',

                'sameday_cod_discount_per' => 'Zero Cod Discount percentage is required',
                'sameday_return_discount_per' => 'Return Discount percentage is required',

            ];

            $validations = array();
            $on_validations = array();
            $ol_validations = array();
            $detain_validations = array();
            $sameday_validations = array();
            $sms_validations = array();

            if($request->has('sms_main_switch') && $request->sms_main_switch == 'on') {
                $sms_validations = [
                    'sms_charges' => 'required|gt:0'
                ];
            }

            if ($request->has('on_main_switch') && $request->on_main_switch == 'on') {
                $on_validations = [
                    'on_wa_range_up.*' => 'required|numeric|between:0,100000',
                    'on_wa_range_down.*' => 'required|numeric|between:0,100000',
                    'on_wa_local_charges.*' => 'required|numeric',
                    'on_class_0_charges.*' => 'required|numeric',
                    'on_class_1_charges.*' => 'required',
                    'on_class_2_charges.*' => 'required',
                    'on_class_3_charges.*' => 'required',
                    'on_wa_spkg.*' => 'numeric',
                    'on_replacement_charges' => 'required|numeric',
                    'on_tnb_charges' => 'required|numeric',
                    'on_reverse_charges' => 'required|numeric',
                    'on_cash_range_up.*' => 'required_if:on_cash_handling_switch,==,on|numeric',
                    'on_cash_range_down.*' => 'required_if:on_cash_handling_switch,==,on|numeric',
                    'on_cash_charges.*' => 'required_if:on_cash_handling_switch,==,on',
                    'on_ins_range_up.*' => 'required_if:on_insurance_charges_switch,==,on|numeric',
                    'on_ins_range_down.*' => 'required_if:on_insurance_charges_switch,==,on|numeric',
                    'on_ins_charges.*' => 'required_if:on_insurance_charges_switch,==,on',
                    'on_return_local_charges.*' => 'required_if:on_return_switch,==,on|numeric',
                    'on_return_class_0_charges.*' => 'required_if:on_return_switch,==,on|numeric',
                    'on_return_class_1_charges.*' => 'required_if:on_return_switch,==,on|numeric',
                    'on_return_class_2_charges.*' => 'required_if:on_return_switch,==,on|numeric',
                    'on_return_class_3_charges.*' => 'required_if:on_return_switch,==,on|numeric',
                    'overnight_fuel_surcharge' => 'required_if:overnight_fuel_switch,==,on|numeric',

                    'on_discount_title' => 'required_with:on_discount_weight_rate,on_discount_cash_rate,on_discount_insurance_rate,on_discount_return_rate,on_discount_packaging_rate',
                    'on_daterange' => 'required_with:on_discount_weight_rate,on_discount_cash_rate,on_discount_insurance_rate,on_discount_return_rate,on_discount_packaging_rate',
                    'on_discount_weight_rate' => 'required_if:on_discount_weight_switch,==,on',
                    'on_discount_cash_rate' => 'required_if:on_discount_cash_switch,==,on',
                    'on_discount_insurance_rate' => 'required_if:on_discount_insurance_switch,==,on',
                    'on_discount_return_rate' => 'required_if:on_discount_return_switch,==,on',
                    'on_discount_packaging_rate' => 'required_if:on_discount_packaging_switch,==,on',

                    'discount_on_destination' => 'required_if:on_discount_destination_wise_weight_switch,==,on',

                    'on_cod_discount_per' => 'required_if:on_zero_cod_switch,==,on',
                    'on_return_discount_per' => 'required_if:on_return_discount_switch,==,on',
                ];
            }
            //overland
            if ($request->has('ol_main_switch') && $request->ol_main_switch == 'on') {
                $ol_validations = [
                    'ol_wa_range_up.*' => 'required|numeric|between:0,100000',
                    'ol_wa_range_down.*' => 'required|numeric|between:0,100000',
                    'ol_wa_local_charges.*' => 'required|numeric',
                    'ol_class_0_charges.*' => 'required|numeric',
                    'ol_class_1_charges.*' => 'required',
                    'ol_class_2_charges.*' => 'required',
                    'ol_class_3_charges.*' => 'required',
                    'ol_wa_spkg.*' => 'numeric',
                    'ol_replacement_charges' => 'required|numeric',
                    'ol_tnb_charges' => 'required|numeric',
                    'ol_reverse_charges' => 'required|numeric',
                    'ol_cash_range_up.*' => 'required_if:ol_cash_handling_switch,==,on|numeric',
                    'ol_cash_range_down.*' => 'required_if:ol_cash_handling_switch,==,on|numeric',
                    'ol_cash_charges.*' => 'required_if:ol_cash_handling_switch,==,on',
                    'ol_ins_range_up.*' => 'required_if:ol_insurance_charges_switch,==,on|numeric',
                    'ol_ins_range_down.*' => 'required_if:ol_insurance_charges_switch,==,on|numeric',
                    'ol_ins_charges.*' => 'required_if:ol_insurance_charges_switch,==,on',
                    'ol_return_local_charges.*' => 'required_if:ol_return_switch,==,on|numeric',
                    'ol_return_class_0_charges.*' => 'required_if:ol_return_switch,==,on|numeric',
                    'ol_return_class_1_charges.*' => 'required_if:ol_return_switch,==,on|numeric',
                    'ol_return_class_2_charges.*' => 'required_if:ol_return_switch,==,on|numeric',
                    'ol_return_class_3_charges.*' => 'required_if:ol_return_switch,==,on|numeric',
                    'overland_fuel_surcharge' => 'required_if:overland_fuel_switch,==,on|numeric',

                    'ol_discount_title' => 'required_with:ol_discount_weight_rate,ol_discount_cash_rate,ol_discount_insurance_rate,ol_discount_return_rate,ol_discount_packaging_rate',
                    'ol_daterange' => 'required_with:ol_discount_weight_rate,ol_discount_cash_rate,ol_discount_insurance_rate,ol_discount_return_rate,ol_discount_packaging_rate',
                    'ol_discount_weight_rate' => 'required_if:ol_discount_weight_switch,==,on',
                    'ol_discount_cash_rate' => 'required_if:ol_discount_cash_switch,==,on',
                    'ol_discount_insurance_rate' => 'required_if:ol_discount_insurance_switch,==,on',
                    'ol_discount_return_rate' => 'required_if:ol_discount_return_switch,==,on',
                    'ol_discount_packaging_rate' => 'required_if:ol_discount_packaging_switch,==,on',

                    'discount_ol_destination' => 'required_if:ol_discount_destination_wise_weight_switch,==,on',
                    'ol_cod_discount_per' => 'required_if:ol_zero_cod_switch,==,on',
                    'ol_return_discount_per' => 'required_if:ol_return_discount_switch,==,on',
                ];
            }
            //overland
            if ($request->has('detain_main_switch') && $request->detain_main_switch == 'on') {
                $detain_validations = [
                    'detain_wa_range_up.*' => 'required|numeric|between:0,100000',
                    'detain_wa_range_down.*' => 'required|numeric|between:0,100000',
                    'detain_wa_local_charges.*' => 'required|numeric',
                    'detain_class_0_charges.*' => 'required|numeric',
                    'detain_class_1_charges.*' => 'required',
                    'detain_class_2_charges.*' => 'required',
                    'detain_class_3_charges.*' => 'required',
                    'detain_wa_spkg.*' => 'numeric',
                    'detain_replacement_charges' => 'required|numeric',
                    'detain_tnb_charges' => 'required|numeric',
                    'detain_reverse_charges' => 'required|numeric',
                    'detain_cash_range_up.*' => 'required_if:detain_cash_handling_switch,==,on|numeric',
                    'detain_cash_range_down.*' => 'required_if:detain_cash_handling_switch,==,on|numeric',
                    'detain_cash_charges.*' => 'required_if:detain_cash_handling_switch,==,on',
                    'detain_ins_range_up.*' => 'required_if:detain_insurance_charges_switch,==,on|numeric',
                    'detain_ins_range_down.*' => 'required_if:detain_insurance_charges_switch,==,on|numeric',
                    'detain_ins_charges.*' => 'required_if:detain_insurance_charges_switch,==,on',
                    'detain_return_local_charges.*' => 'required_if:detain_return_switch,==,on|numeric',
                    'detain_return_class_0_charges.*' => 'required_if:detain_return_switch,==,on|numeric',
                    'detain_return_class_1_charges.*' => 'required_if:detain_return_switch,==,on|numeric',
                    'detain_return_class_2_charges.*' => 'required_if:detain_return_switch,==,on|numeric',
                    'detain_return_class_3_charges.*' => 'required_if:detain_return_switch,==,on|numeric',
                    'detain_fuel_surcharge' => 'required_if:detain_fuel_switch,==,on|numeric',

                    'detain_discount_title' => 'required_with:detain_discount_weight_rate,detain_discount_cash_rate,detain_discount_insurance_rate,detain_discount_return_rate,detain_discount_packaging_rate',
                    'detain_daterange' => 'required_with:detain_discount_weight_rate,detain_discount_cash_rate,detain_discount_insurance_rate,detain_discount_return_rate,detain_discount_packaging_rate',
                    'detain_discount_weight_rate' => 'required_if:detain_discount_weight_switch,==,on',
                    'detain_discount_cash_rate' => 'required_if:detain_discount_cash_switch,==,on',
                    'detain_discount_insurance_rate' => 'required_if:detain_discount_insurance_switch,==,on',
                    'detain_discount_return_rate' => 'required_if:detain_discount_return_switch,==,on',
                    'detain_discount_packaging_rate' => 'required_if:detain_discount_packaging_switch,==,on',

                    'discount_d_destination' => 'required_if:d_discount_destination_wise_weight_switch,==,on',
                    'detain_cod_discount_per' => 'required_if:detain_zero_cod_switch,==,on',
                    'detain_return_discount_per' => 'required_if:detain_return_discount_switch,==,on',
                ];
            }
            //sameday
            if ($request->has('sameday_main_switch') && $request->sameday_main_switch == 'on') {
                $sameday_validations = [
                    'sameday_wa_range_up.*' => 'required|numeric|between:0,100000',
                    'sameday_wa_range_down.*' => 'required|numeric|between:0,100000',
                    'sameday_wa_local_charges.*' => 'required|numeric',
                    'sameday_class_0_charges.*' => 'required|numeric',
                    'sameday_wa_spkg.*' => 'numeric',
                    'sameday_replacement_charges' => 'required|numeric',
                    'sameday_tnb_charges' => 'required|numeric',
                    'sameday_reverse_charges' => 'required|numeric',
                    'sameday_cash_range_up.*' => 'required_if:sameday_cash_handling_switch,==,on|numeric',
                    'sameday_cash_range_down.*' => 'required_if:sameday_cash_handling_switch,==,on|numeric',
                    'sameday_cash_charges.*' => 'required_if:sameday_cash_handling_switch,==,on',
                    'sameday_ins_range_up.*' => 'required_if:sameday_insurance_charges_switch,==,on|numeric',
                    'sameday_ins_range_down.*' => 'required_if:sameday_insurance_charges_switch,==,on|numeric',
                    'sameday_ins_charges.*' => 'required_if:sameday_insurance_charges_switch,==,on',
                    'sameday_return_local_charges.*' => 'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_return_class_0_charges.*' => 'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_return_class_1_charges.*' => 'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_return_class_2charges.*' => 'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_return_class_3_charges.*' => 'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_fuel_surcharge' => 'required_if:sameday_fuel_switch,==,on|numeric',

                    'sameday_discount_title' => 'required_with:sameday_discount_weight_rate,sameday_discount_cash_rate,sameday_discount_insurance_rate,sameday_discount_return_rate,sameday_discount_packaging_rate',
                    'sameday_daterange' => 'required_with:sameday_discount_weight_rate,sameday_discount_cash_rate,sameday_discount_insurance_rate,sameday_discount_return_rate,sameday_discount_packaging_rate',
                    'sameday_discount_weight_rate' => 'required_if:sameday_discount_weight_switch,==,on',
                    'sameday_discount_cash_rate' => 'required_if:sameday_discount_cash_switch,==,on',
                    'sameday_discount_insurance_rate' => 'required_if:sameday_discount_insurance_switch,==,on',
                    'sameday_discount_return_rate' => 'required_if:sameday_discount_return_switch,==,on',
                    'sameday_discount_packaging_rate' => 'required_if:sameday_discount_packaging_switch,==,on',

                    'discount_sd_destination' => 'required_if:sd_discount_destination_wise_weight_switch,==,on',
                    'sameday_cod_discount_per' => 'required_if:sameday_zero_cod_switch,==,on',
                    'sameday_return_discount_per' => 'required_if:sameday_return_discount_switch,==,on',
                ];
            }

            if ($request->has('warehouse_main_switch') && $request->warehouse_main_switch == 'on') {
                $warehouse_validations = [
                    'invoicing_cycle' => 'required|numeric',
                    'invoicing_date.*' => 'required_if:invoicing_cycle,1,3',
                    'ppc_charges' => 'required_if:ppc_switch,==,on',
                    'psf_charges' => 'required_if:psf_switch,==,on',
                    'storage_type.*' => 'required_if:storage_charges_switch,==,on',
                    'storage_type_charges.*' => 'required_if:storage_charges_switch,==,on|numeric',
                    'packing_type.*' => 'required_if:packing_charges_switch,==,on',
                    'packing_charges.*' => 'required_if:packing_charges_switch,==,on|numeric',
                    'labelling_charges.*' => 'required_if:labelling_charges_switch,==,on|numeric',
                ];
            }
            $validations = array_merge($on_validations, $ol_validations, $detain_validations, $sameday_validations, $sms_validations);

            $validate = Validator::make($request->all(), $validations, $messages);

            if ($validate->fails()) {
                return redirect()->back()
                    ->withErrors($validate)
                    ->withInput();
            }

            if($request->has('sms_main_switch') && $request->sms_main_switch == 'on') {
                User::where('id', $id)->update([
                    'sms_charges' => $request->sms_charges,
                    'sms_charges_status' => ($request->has('sms_main_switch')) ? 1 : 0,
                ]);
            }
            else{
                User::where('id', $id)->update([
                    'sms_charges' => null,
                    'sms_charges_status' =>  0,
                ]);
            }
            if ($request->on_rate_record != null) {
                RateStatus::where('id', $request->on_rate_record)
                    ->update([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'status' => ($request->has('on_main_switch')) ? 1 : 0,
                        'cash_handling_charges' => ($request->has('on_cash_handling_switch')) ? 1 : 0,
                        'insurance_charges' => ($request->has('on_insurance_charges_switch')) ? 1 : 0,
                        'return_charges' => ($request->has('on_return_switch')) ? 1 : 0,
                        'fuel_charges' => ($request->has('overnight_fuel_switch')) ? 1 : 0,
                        'zero_cod_discount' => ($request->has('on_zero_cod_switch')) ? 1 : 0,
                        'return_discount' => ($request->has('on_return_discount_switch')) ? 1 : 0,
                    ]);
            } else {
                RateStatus::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 1,
                    'status' => ($request->has('on_main_switch')) ? 1 : 0,
                    'cash_handling_charges' => ($request->has('on_cash_handling_switch')) ? 1 : 0,
                    'insurance_charges' => ($request->has('on_insurance_charges_switch')) ? 1 : 0,
                    'return_charges' => ($request->has('on_return_switch')) ? 1 : 0,
                    'fuel_charges' => ($request->has('overnight_fuel_switch')) ? 1 : 0,
                    'zero_cod_discount' => ($request->has('on_zero_cod_switch')) ? 1 : 0,
                    'return_discount' => ($request->has('on_return_discount_switch')) ? 1 : 0,
                ]);
            }
            if ($request->ol_rate_record != null) {
                RateStatus::where('id', $request->ol_rate_record)
                    ->update([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'status' => ($request->has('ol_main_switch')) ? 1 : 0,
                        'cash_handling_charges' => ($request->has('ol_cash_handling_switch')) ? 1 : 0,
                        'insurance_charges' => ($request->has('ol_insurance_charges_switch')) ? 1 : 0,
                        'return_charges' => ($request->has('ol_return_switch')) ? 1 : 0,
                        'fuel_charges' => ($request->has('overland_fuel_switch')) ? 1 : 0,
                        'zero_cod_discount' => ($request->has('ol_zero_cod_switch')) ? 1 : 0,
                        'return_discount' => ($request->has('ol_return_discount_switch')) ? 1 : 0,
                    ]);
            } else {
                RateStatus::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 2,
                    'status' => ($request->has('ol_main_switch')) ? 1 : 0,
                    'cash_handling_charges' => ($request->has('ol_cash_handling_switch')) ? 1 : 0,
                    'insurance_charges' => ($request->has('ol_insurance_charges_switch')) ? 1 : 0,
                    'return_charges' => ($request->has('ol_return_switch')) ? 1 : 0,
                    'fuel_charges' => ($request->has('overland_fuel_switch')) ? 1 : 0,
                    'zero_cod_discount' => ($request->has('ol_zero_cod_switch')) ? 1 : 0,
                    'return_discount' => ($request->has('ol_return_discount_switch')) ? 1 : 0,
                ]);
            }
            if ($request->det_rate_record != null) {
                RateStatus::where('id', $request->det_rate_record)
                    ->update([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'status' => ($request->has('detain_main_switch')) ? 1 : 0,
                        'cash_handling_charges' => ($request->has('detain_cash_handling_switch')) ? 1 : 0,
                        'insurance_charges' => ($request->has('detain_insurance_charges_switch')) ? 1 : 0,
                        'return_charges' => ($request->has('detain_return_switch')) ? 1 : 0,
                        'fuel_charges' => ($request->has('detain_fuel_switch')) ? 1 : 0,
                        'zero_cod_discount' => ($request->has('detain_zero_cod_switch')) ? 1 : 0,
                        'return_discount' => ($request->has('detain_return_discount_switch')) ? 1 : 0,
                    ]);
            } else {
                RateStatus::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 3,
                    'status' => ($request->has('detain_main_switch')) ? 1 : 0,
                    'cash_handling_charges' => ($request->has('detain_cash_handling_switch')) ? 1 : 0,
                    'insurance_charges' => ($request->has('detain_insurance_charges_switch')) ? 1 : 0,
                    'return_charges' => ($request->has('detain_return_switch')) ? 1 : 0,
                    'fuel_charges' => ($request->has('detain_fuel_switch')) ? 1 : 0,
                    'zero_cod_discount' => ($request->has('detain_zero_cod_switch')) ? 1 : 0,
                    'return_discount' => ($request->has('detain_return_discount_switch')) ? 1 : 0,
                ]);
            }
            if ($request->same_rate_record != null) {
                RateStatus::where('id', $request->same_rate_record)
                    ->update([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'status' => ($request->has('sameday_main_switch')) ? 1 : 0,
                        'cash_handling_charges' => ($request->has('sameday_cash_handling_switch')) ? 1 : 0,
                        'insurance_charges' => ($request->has('sameday_insurance_charges_switch')) ? 1 : 0,
                        'return_charges' => ($request->has('sameday_return_switch')) ? 1 : 0,
                        'fuel_charges' => ($request->has('sameday_fuel_switch')) ? 1 : 0,
                        'zero_cod_discount' => ($request->has('sameday_zero_cod_switch')) ? 1 : 0,
                        'return_discount' => ($request->has('sameday_return_discount_switch')) ? 1 : 0,
                    ]);
            } else {
                RateStatus::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 4,
                    'status' => ($request->has('sameday_main_switch')) ? 1 : 0,
                    'cash_handling_charges' => ($request->has('sameday_cash_handling_switch')) ? 1 : 0,
                    'insurance_charges' => ($request->has('sameday_insurance_charges_switch')) ? 1 : 0,
                    'return_charges' => ($request->has('sameday_return_switch')) ? 1 : 0,
                    'fuel_charges' => ($request->has('sameday_fuel_switch')) ? 1 : 0,
                    'zero_cod_discount' => ($request->has('sameday_zero_cod_switch')) ? 1 : 0,
                    'return_discount' => ($request->has('sameday_return_discount_switch')) ? 1 : 0,
                ]);
            }

//            if($request->has('packaging_switch') && $request->packaging_switch == 'on'){
//                $packaging_types = PackagingMaterialTypes::where('status', 1)->get();
//                PackagingCharge::where('user_id', $id)->delete();
//                foreach ($packaging_types as $type){
//                    if($request->has('packaging_type_'.$type->id)){
//                        $packaging_size = PackagingMaterialTypeSizes::where('type_id', $type->id)->get();
//                        foreach ($packaging_size as $size) {
//                            $key = "packaging_material_size.$size->id";
//                            $packaging_charges = new PackagingCharge();
//                            $packaging_charges->user_id = $id;
//                            $packaging_charges->type_id = $type->id;
//                            $packaging_charges->size_id = $size->id;
//                            $packaging_charges->charges = ($request->has($key) ? $request->packaging_material_size[$size->id]: 0);
//                            $packaging_charges->save();
//                        }
//
//                    }
//                }
//
//            }

            DiscountWeightCharge::where('user_id',$id)->delete();
            if ($request->has('on_main_switch') && $request->on_main_switch == 'on') {

                if ($request->has('on_default') && $request->on_default == 'on') {
                    $default_shipping_mode = User::where('id', $id)->update([
                        'default_shipping_mode' => 1
                    ]);
                }

                $ONRateAlready = RateStatus::where(['user_id' => $id, 'shipping_mode_id' => 1])->get();

                if (!$ONRateAlready->isEmpty()) {
                    RateOriginHub::where(['user_id' => $id, 'shipping_mode_id' => 1])->delete();
                    if ($request->has('on_origin_hubs')) {
                        foreach ($request->on_origin_hubs as $origin_id) {
                            $rate_origin_hub = new RateOriginHub();
                            $rate_origin_hub->user_id = $id;
                            $rate_origin_hub->shipping_mode_id = 1;
                            $rate_origin_hub->city_id = $origin_id;
                            $rate_origin_hub->save();
                        }
                    }
                    RateDestinationHub::where(['user_id' => $id, 'shipping_mode_id' => 1])->delete();
                    if ($request->has('on_destination_hubs')) {
                        foreach ($request->on_destination_hubs as $destination_id) {
                            $rate_destination_hub = new RateDestinationHub();
                            $rate_destination_hub->user_id = $id;
                            $rate_destination_hub->shipping_mode_id = 1;
                            $rate_destination_hub->city_id = $destination_id;
                            $rate_destination_hub->save();
                        }
                    }
                    $wa_switch = array();
                    $wa_spkg = array();
                    WeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 1])->whereNotIn('id', $request->on_weight_record)->delete();
                    foreach ($request->on_weight_record as $index => $on_weight_record) {
                        if ($request->has('on_wa_switch')) {
                            if (array_key_exists($index, $request->on_wa_switch)) {
                                $wa_switch[$index] = 1;
                            } else {
                                $wa_switch[$index] = 0;
                            };
                        } else {
                            $wa_switch[$index] = 0;
                        }
                        if ($request->has('on_wa_spkg')) {
                            if (array_key_exists($index, $request->on_wa_spkg)) {
                                $wa_spkg[$index] = $request->on_wa_spkg[$index];
                            } else {
                                $wa_spkg[$index] = 0;
                            };
                        } else {
                            $wa_spkg[$index] = 0;
                        }
//                        return $request->on_class_0_charges[$index];
                        if ($request->on_weight_record[$index] != null) {

                            $weight_row = WeightCharge::where('id', $request->on_weight_record[$index])
                                ->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 1,
                                    'range_up' => $request->on_wa_range_up[$index],
                                    'range_down' => $request->on_wa_range_down[$index],
                                    'weight_addition' => $wa_switch[$index],
                                    'spkg' => $wa_spkg[$index],
                                    'local_or_6hr' => $request->on_wa_local_charges[$index],
                                    'national_charges_class_0' => $request->on_class_0_charges[$index],
                                    'national_charges_class_1' => $request->on_class_1_charges[$index],
                                    'national_charges_class_2' => $request->on_class_2_charges[$index],
                                    'national_charges_class_3' => $request->on_class_3_charges[$index]
                                ]);
                        }
                        if ($request->on_weight_record[$index] == null) {
                            WeightCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'range_up' => $request->on_wa_range_up[$index],
                                'range_down' => $request->on_wa_range_down[$index],
                                'weight_addition' => $wa_switch[$index],
                                'spkg' => $wa_spkg[$index],
                                'local_or_6hr' => $request->on_wa_local_charges[$index],
                                'national_charges_class_0' => $request->on_class_0_charges[$index],
                                'national_charges_class_1' => $request->on_class_1_charges[$index],
                                'national_charges_class_2' => $request->on_class_2_charges[$index],
                                'national_charges_class_3' => $request->on_class_3_charges[$index]
                            ]);
                        }


                    }

                    //Replacement and Try and Buy charges
                    if ($request->on_booking_record != null) {
                        BookingTypeCharges::where(['id' => $request->on_booking_record])->update([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'replacement_charges' => $request->on_replacement_charges,
                            'try_and_buy_charges' => $request->on_tnb_charges,
                            'reverse_pickup_charges' => $request->on_reverse_charges
                        ]);
                    } else {
                        BookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'replacement_charges' => $request->on_replacement_charges,
                            'try_and_buy_charges' => $request->on_tnb_charges,
                            'reverse_pickup_charges' => $request->on_reverse_charges
                        ]);
                    }


                    //Cash handling Charges
                    if ($request->has('on_cash_handling_switch') && $request->on_cash_handling_switch == 'on') {
                        CashHandlingCharge::where(['user_id' => $id, 'shipping_mode_id' => 1])->whereNotIn('id', $request->on_cash_record)->delete();

                        foreach ($request->on_cash_record as $index => $on_cash_record) {
                            if ($request->on_cash_record[$index] != null) {
                                CashHandlingCharge::where(['id' => $request->on_cash_record[$index]])->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 1,
                                    'range_up' => $request->on_cash_range_up[$index],
                                    'range_down' => $request->on_cash_range_down[$index],
                                    'charges' => $request->on_cash_charges[$index]
                                ]);
                            }
                            if ($request->on_cash_record[$index] == null) {
                                CashHandlingCharge::create([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 1,
                                    'range_up' => $request->on_cash_range_up[$index],
                                    'range_down' => $request->on_cash_range_down[$index],
                                    'charges' => $request->on_cash_charges[$index]
                                ]);
                            }
                        }
                    }
                    //insurance charges
                    if ($request->has('on_insurance_charges_switch') && $request->on_insurance_charges_switch == 'on') {
                        InsuranceCharge::where(['user_id' => $id, 'shipping_mode_id' => 1])->whereNotIn('id', $request->on_insurance_record)->delete();
                        foreach ($request->on_insurance_record as $insurance => $on_insurance_record) {
                            if ($request->on_insurance_record[$insurance] != null) {
                                InsuranceCharge::where(['id' => $request->on_insurance_record[$insurance]])->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 1,
                                    'range_up' => $request->on_ins_range_up[$insurance],
                                    'range_down' => $request->on_ins_range_down[$insurance],
                                    'charges' => $request->on_ins_charges[$insurance]
                                ]);
                            }
                            if ($request->on_insurance_record[$insurance] == null) {
                                InsuranceCharge::create([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 1,
                                    'range_up' => $request->on_ins_range_up[$insurance],
                                    'range_down' => $request->on_ins_range_down[$insurance],
                                    'charges' => $request->on_ins_charges[$insurance]
                                ]);
                            }
                        }
                    }
                    //Return Charges
                    if ($request->has('on_return_switch') && $request->on_return_switch == 'on') {
                        if ($request->on_return_record != null) {
                            ReturnCharge::where(['id' => $request->on_return_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'local' => $request->on_return_local_charges,
                                'national_charges_class_0' => $request->on_return_class_0_charges,
                                'national_charges_class_1' => $request->on_return_class_1_charges,
                                'national_charges_class_2' => $request->on_return_class_2_charges,
                                'national_charges_class_3' => $request->on_return_class_3_charges
                            ]);
                        } elseif ($request->on_return_record == null) {
                            ReturnCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'local' => $request->on_return_local_charges,
                                'national_charges_class_0' => $request->on_return_class_0_charges,
                                'national_charges_class_1' => $request->on_return_class_1_charges,
                                'national_charges_class_2' => $request->on_return_class_2_charges,
                                'national_charges_class_3' => $request->on_return_class_3_charges
                            ]);
                        }

                    }
                    //Return Charges
                    if ($request->has('overnight_fuel_switch') && $request->overnight_fuel_switch == 'on') {
                        if ($request->on_fuel_record != null) {
                            FuelSurcharge::where(['id' => $request->on_fuel_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'fuel_surcharge' => $request->overnight_fuel_surcharge
                            ]);
                        } elseif ($request->on_fuel_record == null) {
                            FuelSurcharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'fuel_surcharge' => $request->overnight_fuel_surcharge
                            ]);
                        }

                    }


                    $discount_cash = 0;
                    $discount_weight = 0;
                    $discount_insurance = 0;
                    $discount_return = 0;
                    $discount_packaging = 0;

                    if ($request->has('on_discount_weight_switch') && $request->on_discount_weight_switch == 'on') {
                        $discount_weight = $request->on_discount_weight_rate != null ? $request->on_discount_weight_rate : 0;
//                    $discount_weight = $request->on_discount_weight_rate;
                    }
                    if ($request->has('on_discount_cash_switch') && $request->on_discount_cash_switch == 'on') {
                        $discount_cash = $request->on_discount_cash_rate != null ? $request->on_discount_cash_rate : 0;
                    }
                    if ($request->has('on_discount_insurance_switch') && $request->on_discount_insurance_switch == 'on') {
                        $discount_insurance = $request->on_discount_insurance_rate != null ? $request->on_discount_insurance_rate : 0;
                    }
                    if ($request->has('on_discount_return_switch') && $request->on_discount_return_switch == 'on') {
                        $discount_return = $request->on_discount_return_rate != null ? $request->on_discount_insurance_rate : 0;
                    }
                    if ($request->has('on_discount_packaging_switch') && $request->on_discount_packaging_switch == 'on') {
                        $discount_packaging = $request->on_discount_packaging_rate != null ? $request->on_discount_packaging_rate : 0;
                    }
                    if ($discount_weight != 0 || $discount_cash != 0 || $discount_insurance != 0 || $discount_return != 0 || $discount_packaging != 0) {


                        $date_str = $request->on_daterange;
                        $date_sep = explode(' - ', $date_str);
                        $date_to = explode('/', $date_sep[0]);
                        $date_from = explode('/', $date_sep[1]);
                        $to = Carbon::create($date_to[2], $date_to[0], $date_to[1], 0, 0, 0, 'UTC')->toDateTimeString();
                        $from = Carbon::create($date_from[2], $date_from[0], $date_from[1], 0, 0, 0, 'UTC')->toDateTimeString();

                        if ($request->on_discount_record != null) {
                            DiscountCharge::where(['id' => $request->on_discount_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'title' => $request->on_discount_title,
                                'weight' => $discount_weight,
                                'cash' => $discount_cash,
                                'insurance' => $discount_insurance,
                                'return' => $discount_return,
                                'packaging' => $discount_packaging,
                                'to' => $to,
                                'from' => $from,
                                'added_by' => Auth::id()
                            ]);
                        } elseif ($request->on_discount_record == null) {
                            DiscountCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'title' => $request->on_discount_title,
                                'weight' => $discount_weight,
                                'cash' => $discount_cash,
                                'insurance' => $discount_insurance,
                                'return' => $discount_return,
                                'packaging' => $discount_packaging,
                                'to' => $to,
                                'from' => $from,
                                'added_by' => Auth::id()
                            ]);
                        }

                    }

                    if($request->has('on_discount_destination_wise_weight_switch') && $request->on_discount_destination_wise_weight_switch == "on")
                    {
                        foreach($request->discount_on_destination as $parent_key => $destination)
                        {
                            $discount_wa_switch = array();
                            $discount_wa_spkg = array();
                            foreach($request->discount_on_wa_range_up[$parent_key] as $child_key => $value)
                            {
                                if ($request->has('discount_on_wa_switch.'.$parent_key)) {
                                    if (array_key_exists($child_key, $request->discount_on_wa_switch[$parent_key])) {
                                        $discount_wa_switch[$child_key] = 1;
                                    } else {
                                        $discount_wa_switch[$child_key] = 0;
                                    };
                                } else {
                                    $discount_wa_switch[$child_key] = 0;
                                }
                                if ($request->has('discount_on_wa_spkg.'.$parent_key)) {
                                    if (array_key_exists($child_key, $request->discount_on_wa_spkg[$parent_key])) {
                                        $discount_wa_spkg[$child_key] = $request->discount_on_wa_spkg[$parent_key][$child_key];
                                    } else {
                                        $discount_wa_spkg[$child_key] = 0.5;
                                    };
                                } else {
                                    $discount_wa_spkg[$child_key] = 0.5;
                                }
                                DiscountWeightCharge::create([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 1,
                                    'destination_id' => $destination,
                                    'range_up' => $request->discount_on_wa_range_up[$parent_key][$child_key],
                                    'range_down' => $request->discount_on_wa_range_down[$parent_key][$child_key],
                                    'weight_addition' => $discount_wa_switch[$child_key],
                                    'spkg' => $discount_wa_spkg[$child_key],
                                    'local_or_6hr' => $request->discount_on_wa_local_charges[$parent_key][$child_key],
                                ]);
                            }
                        }
                    }

                }

                //shipping_mode 1 //weight_charges 0

                if ($request->has('on_dws_weight')) {
                    if ($request->on_dws_weight == 2) {
                        DwsWeightChargesController::edit($id, 1, 2, Auth::id());
                    } else {
                        DwsWeightChargesController::edit($id, 1, 1, Auth::id());

                    }
                } else {
                    DwsWeightChargesController::delete_dws_rate($id, 1);
                }
            }

            //overland
            if ($request->has('ol_main_switch') && $request->ol_main_switch == 'on') {

                if ($request->has('ol_default') && $request->ol_default == 'on') {
                    $default_shipping_mode = User::where('id', $id)->update([
                        'default_shipping_mode' => 2
                    ]);
                }

                $ONRateAlready = RateStatus::where(['user_id' => $id, 'shipping_mode_id' => 2])->get();

                if (!$ONRateAlready->isEmpty()) {
                    RateOriginHub::where(['user_id' => $id, 'shipping_mode_id' => 2])->delete();
                    if ($request->has('ol_origin_hubs')) {
                        foreach ($request->ol_origin_hubs as $origin_id) {
                            $rate_origin_hub = new RateOriginHub();
                            $rate_origin_hub->user_id = $id;
                            $rate_origin_hub->shipping_mode_id = 2;
                            $rate_origin_hub->city_id = $origin_id;
                            $rate_origin_hub->save();
                        }
                    }
                    RateDestinationHub::where(['user_id' => $id, 'shipping_mode_id' => 2])->delete();
                    if ($request->has('ol_destination_hubs')) {
                        foreach ($request->ol_destination_hubs as $destination_id) {
                            $rate_destination_hub = new RateDestinationHub();
                            $rate_destination_hub->user_id = $id;
                            $rate_destination_hub->shipping_mode_id = 2;
                            $rate_destination_hub->city_id = $destination_id;
                            $rate_destination_hub->save();
                        }
                    }
                    $wa_switch = array();
                    $wa_spkg = array();
                    WeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 2])->whereNotIn('id', $request->ol_weight_record)->delete();
                    foreach ($request->ol_weight_record as $index => $ol_weight_record) {
                        if ($request->has('ol_wa_switch')) {
                            if (array_key_exists($index, $request->ol_wa_switch)) {
                                $wa_switch[$index] = 1;
                            } else {
                                $wa_switch[$index] = 0;
                            };
                        } else {
                            $wa_switch[$index] = 0;
                        }
                        if ($request->has('ol_wa_spkg')) {
                            if (array_key_exists($index, $request->ol_wa_spkg)) {
                                $wa_spkg[$index] = $request->ol_wa_spkg[$index];
                            } else {
                                $wa_spkg[$index] = 0;
                            };
                        } else {
                            $wa_spkg[$index] = 0;
                        }
                        if ($request->ol_weight_record[$index] != null) {

                            $weight_row = WeightCharge::where('id', $request->ol_weight_record[$index])
                                ->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 2,
                                    'range_up' => $request->ol_wa_range_up[$index],
                                    'range_down' => $request->ol_wa_range_down[$index],
                                    'weight_addition' => $wa_switch[$index],
                                    'spkg' => $wa_spkg[$index],
                                    'local_or_6hr' => $request->ol_wa_local_charges[$index],
                                    'national_charges_class_0' => $request->ol_class_0_charges[$index],
                                    'national_charges_class_1' => $request->ol_class_1_charges[$index],
                                    'national_charges_class_2' => $request->ol_class_2_charges[$index],
                                    'national_charges_class_3' => $request->ol_class_3_charges[$index]
                                ]);
                        }
                        if ($request->ol_weight_record[$index] == null) {
                            WeightCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'range_up' => $request->ol_wa_range_up[$index],
                                'range_down' => $request->ol_wa_range_down[$index],
                                'weight_addition' => $wa_switch[$index],
                                'spkg' => $wa_spkg[$index],
                                'local_or_6hr' => $request->ol_wa_local_charges[$index],
                                'national_charges_class_0' => $request->ol_class_0_charges[$index],
                                'national_charges_class_1' => $request->ol_class_1_charges[$index],
                                'national_charges_class_2' => $request->ol_class_2_charges[$index],
                                'national_charges_class_3' => $request->ol_class_3_charges[$index]
                            ]);
                        }


                    }

                    //Replacement and Try and Buy charges
                    if ($request->ol_booking_record != null) {
                        BookingTypeCharges::where(['id' => $request->ol_booking_record])->update([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'replacement_charges' => $request->ol_replacement_charges,
                            'try_and_buy_charges' => $request->ol_tnb_charges,
                            'reverse_pickup_charges' => $request->ol_reverse_charges
                        ]);
                    } else {
                        BookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'replacement_charges' => $request->ol_replacement_charges,
                            'try_and_buy_charges' => $request->ol_tnb_charges,
                            'reverse_pickup_charges' => $request->ol_reverse_charges
                        ]);
                    }


                    //Cash handling Charges
                    if ($request->has('ol_cash_handling_switch') && $request->ol_cash_handling_switch == 'on') {
                        CashHandlingCharge::where(['user_id' => $id, 'shipping_mode_id' => 2])->whereNotIn('id', $request->ol_cash_record)->delete();

                        foreach ($request->ol_cash_record as $index => $ol_cash_record) {
                            if ($request->ol_cash_record[$index] != null) {
                                CashHandlingCharge::where(['id' => $request->ol_cash_record[$index]])->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 2,
                                    'range_up' => $request->ol_cash_range_up[$index],
                                    'range_down' => $request->ol_cash_range_down[$index],
                                    'charges' => $request->ol_cash_charges[$index]
                                ]);
                            }
                            if ($request->ol_cash_record[$index] == null) {
                                CashHandlingCharge::create([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 2,
                                    'range_up' => $request->ol_cash_range_up[$index],
                                    'range_down' => $request->ol_cash_range_down[$index],
                                    'charges' => $request->ol_cash_charges[$index]
                                ]);
                            }
                        }
                    }
                    //insurance charges
                    if ($request->has('ol_insurance_charges_switch') && $request->ol_insurance_charges_switch == 'on') {
                        InsuranceCharge::where(['user_id' => $id, 'shipping_mode_id' => 2])->whereNotIn('id', $request->ol_insurance_record)->delete();
                        foreach ($request->ol_insurance_record as $insurance => $ol_insurance_record) {
                            if ($request->ol_insurance_record[$insurance] != null) {
                                InsuranceCharge::where(['id' => $request->ol_insurance_record[$insurance]])->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 2,
                                    'range_up' => $request->ol_ins_range_up[$insurance],
                                    'range_down' => $request->ol_ins_range_down[$insurance],
                                    'charges' => $request->ol_ins_charges[$insurance]
                                ]);
                            }
                            if ($request->ol_insurance_record[$insurance] == null) {
                                InsuranceCharge::create([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 2,
                                    'range_up' => $request->ol_ins_range_up[$insurance],
                                    'range_down' => $request->ol_ins_range_down[$insurance],
                                    'charges' => $request->ol_ins_charges[$insurance]
                                ]);
                            }
                        }
                    }
                    //Return Charges
                    if ($request->has('ol_return_switch') && $request->ol_return_switch == 'on') {
                        if ($request->ol_return_record != null) {
                            ReturnCharge::where(['id' => $request->ol_return_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'local' => $request->ol_return_local_charges,
                                'national_charges_class_0' => $request->ol_return_class_0_charges,
                                'national_charges_class_1' => $request->ol_return_class_1_charges,
                                'national_charges_class_2' => $request->ol_return_class_2_charges,
                                'national_charges_class_3' => $request->ol_return_class_3_charges
                            ]);
                        } elseif ($request->ol_return_record == null) {
                            ReturnCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'local' => $request->ol_return_local_charges,
                                'national_charges_class_0' => $request->ol_return_class_0_charges,
                                'national_charges_class_1' => $request->ol_return_class_1_charges,
                                'national_charges_class_2' => $request->ol_return_class_2_charges,
                                'national_charges_class_3' => $request->ol_return_class_3_charges
                            ]);
                        }

                    }
                    //Return Charges
                    if ($request->has('overland_fuel_switch') && $request->overland_fuel_switch == 'on') {
                        if ($request->ol_fuel_record != null) {
                            FuelSurcharge::where(['id' => $request->ol_fuel_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'fuel_surcharge' => $request->overland_fuel_surcharge
                            ]);
                        } elseif ($request->ol_fuel_record == null) {
                            FuelSurcharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'fuel_surcharge' => $request->overland_fuel_surcharge
                            ]);
                        }

                    }

                    $discount_cash = 0;
                    $discount_weight = 0;
                    $discount_insurance = 0;
                    $discount_return = 0;
                    $discount_packaging = 0;

                    if ($request->has('ol_discount_weight_switch') && $request->ol_discount_weight_switch == 'on') {
                        $discount_weight = $request->ol_discount_weight_rate != null ? $request->ol_discount_weight_rate : 0;
//                    $discount_weight = $request->ol_discount_weight_rate;
                    }
                    if ($request->has('ol_discount_cash_switch') && $request->ol_discount_cash_switch == 'on') {
                        $discount_cash = $request->ol_discount_cash_rate != null ? $request->ol_discount_cash_rate : 0;
                    }
                    if ($request->has('ol_discount_insurance_switch') && $request->ol_discount_insurance_switch == 'on') {
                        $discount_insurance = $request->ol_discount_insurance_rate != null ? $request->ol_discount_insurance_rate : 0;
                    }
                    if ($request->has('ol_discount_return_switch') && $request->ol_discount_return_switch == 'on') {
                        $discount_return = $request->ol_discount_return_rate != null ? $request->ol_discount_insurance_rate : 0;
                    }
                    if ($request->has('ol_discount_packaging_switch') && $request->ol_discount_packaging_switch == 'on') {
                        $discount_packaging = $request->ol_discount_packaging_rate != null ? $request->ol_discount_packaging_rate : 0;
                    }
                    if ($discount_weight != 0 || $discount_cash != 0 || $discount_insurance != 0 || $discount_return != 0 || $discount_packaging != 0) {


                        $date_str = $request->ol_daterange;
                        $date_sep = explode(' - ', $date_str);
                        $date_to = explode('/', $date_sep[0]);
                        $date_from = explode('/', $date_sep[1]);
                        $to = Carbon::create($date_to[2], $date_to[0], $date_to[1], 0, 0, 0, 'UTC')->toDateTimeString();
                        $from = Carbon::create($date_from[2], $date_from[0], $date_from[1], 0, 0, 0, 'UTC')->toDateTimeString();

                        if ($request->ol_discount_record != null) {
                            DiscountCharge::where(['id' => $request->ol_discount_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'title' => $request->ol_discount_title,
                                'weight' => $discount_weight,
                                'cash' => $discount_cash,
                                'insurance' => $discount_insurance,
                                'return' => $discount_return,
                                'packaging' => $discount_packaging,
                                'to' => $to,
                                'from' => $from,
                                'added_by' => Auth::id()
                            ]);
                        } elseif ($request->ol_discount_record == null) {
                            DiscountCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'title' => $request->ol_discount_title,
                                'weight' => $discount_weight,
                                'cash' => $discount_cash,
                                'insurance' => $discount_insurance,
                                'return' => $discount_return,
                                'packaging' => $discount_packaging,
                                'to' => $to,
                                'from' => $from,
                                'added_by' => Auth::id()
                            ]);
                        }

                    }

                    if($request->has('ol_discount_destination_wise_weight_switch') && $request->ol_discount_destination_wise_weight_switch == "on")
                    {
                        foreach($request->discount_ol_destination as $parent_key => $destination)
                        {
                            $discount_wa_switch = array();
                            $discount_wa_spkg = array();
                            foreach($request->discount_ol_wa_range_up[$parent_key] as $child_key => $value)
                            {
                                if ($request->has('discount_ol_wa_switch.'.$parent_key)) {
                                    if (array_key_exists($child_key, $request->discount_ol_wa_switch[$parent_key])) {
                                        $discount_wa_switch[$child_key] = 1;
                                    } else {
                                        $discount_wa_switch[$child_key] = 0;
                                    };
                                } else {
                                    $discount_wa_switch[$child_key] = 0;
                                }
                                if ($request->has('discount_ol_wa_spkg.'.$parent_key)) {
                                    if (array_key_exists($child_key, $request->discount_ol_wa_spkg[$parent_key])) {
                                        $discount_wa_spkg[$child_key] = $request->discount_ol_wa_spkg[$parent_key][$child_key];
                                    } else {
                                        $discount_wa_spkg[$child_key] = 0.5;
                                    };
                                } else {
                                    $discount_wa_spkg[$child_key] = 0.5;
                                }
                                DiscountWeightCharge::create([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 2,
                                    'destination_id' => $destination,
                                    'range_up' => $request->discount_ol_wa_range_up[$parent_key][$child_key],
                                    'range_down' => $request->discount_ol_wa_range_down[$parent_key][$child_key],
                                    'weight_addition' => $discount_wa_switch[$child_key],
                                    'spkg' => $discount_wa_spkg[$child_key],
                                    'local_or_6hr' => $request->discount_ol_wa_local_charges[$parent_key][$child_key],
                                ]);
                            }
                        }
                    }

                }
                if ($request->has('ol_dws_weight')) {
                    if ($request->ol_dws_weight == 2) {
                        DwsWeightChargesController::edit($id, 2, 2, Auth::id());

                    } else {

                        DwsWeightChargesController::edit($id, 2, 1, Auth::id());
                    }

                } else {
                    DwsWeightChargesController::delete_dws_rate($id, 2);
                }
            }

            //detain
            if ($request->has('detain_main_switch') && $request->detain_main_switch == 'on') {

                if ($request->has('det_default') && $request->det_default == 'on') {
                    $default_shipping_mode = User::where('id', $id)->update([
                        'default_shipping_mode' => 3
                    ]);
                }
                $ONRateAlready = RateStatus::where(['user_id' => $id, 'shipping_mode_id' => 3])->get();

                if (!$ONRateAlready->isEmpty()) {
                    RateOriginHub::where(['user_id' => $id, 'shipping_mode_id' => 3])->delete();
                    if ($request->has('detain_origin_hubs')) {
                        foreach ($request->detain_origin_hubs as $origin_id) {
                            $rate_origin_hub = new RateOriginHub();
                            $rate_origin_hub->user_id = $id;
                            $rate_origin_hub->shipping_mode_id = 3;
                            $rate_origin_hub->city_id = $origin_id;
                            $rate_origin_hub->save();
                        }
                    }
                    RateDestinationHub::where(['user_id' => $id, 'shipping_mode_id' => 3])->delete();
                    if ($request->has('detain_destination_hubs')) {
                        foreach ($request->detain_destination_hubs as $destination_id) {
                            $rate_destination_hub = new RateDestinationHub();
                            $rate_destination_hub->user_id = $id;
                            $rate_destination_hub->shipping_mode_id = 3;
                            $rate_destination_hub->city_id = $destination_id;
                            $rate_destination_hub->save();
                        }
                    }
                    $wa_switch = array();
                    $wa_spkg = array();
                    WeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 3])->whereNotIn('id', $request->detain_weight_record)->delete();
                    foreach ($request->detain_weight_record as $index => $detain_weight_record) {
                        if ($request->has('detain_wa_switch')) {
                            if (array_key_exists($index, $request->detain_wa_switch)) {
                                $wa_switch[$index] = 1;
                            } else {
                                $wa_switch[$index] = 0;
                            };
                        } else {
                            $wa_switch[$index] = 0;
                        }
                        if ($request->has('detain_wa_spkg')) {
                            if (array_key_exists($index, $request->detain_wa_spkg)) {
                                $wa_spkg[$index] = $request->detain_wa_spkg[$index];
                            } else {
                                $wa_spkg[$index] = 0;
                            };
                        } else {
                            $wa_spkg[$index] = 0;
                        }
                        if ($request->detain_weight_record[$index] != null) {

                            $weight_row = WeightCharge::where('id', $request->detain_weight_record[$index])
                                ->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 3,
                                    'range_up' => $request->detain_wa_range_up[$index],
                                    'range_down' => $request->detain_wa_range_down[$index],
                                    'weight_addition' => $wa_switch[$index],
                                    'spkg' => $wa_spkg[$index],
                                    'local_or_6hr' => $request->detain_wa_local_charges[$index],
                                    'national_charges_class_0' => $request->detain_class_0_charges[$index],
                                    'national_charges_class_1' => $request->detain_class_1_charges[$index],
                                    'national_charges_class_2' => $request->detain_class_2_charges[$index],
                                    'national_charges_class_3' => $request->detain_class_3_charges[$index]
                                ]);
                        }
                        if ($request->detain_weight_record[$index] == null) {
                            WeightCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'range_up' => $request->detain_wa_range_up[$index],
                                'range_down' => $request->detain_wa_range_down[$index],
                                'weight_addition' => $wa_switch[$index],
                                'spkg' => $wa_spkg[$index],
                                'local_or_6hr' => $request->detain_wa_local_charges[$index],
                                'national_charges_class_0' => $request->detain_class_0_charges[$index],
                                'national_charges_class_1' => $request->detain_class_1_charges[$index],
                                'national_charges_class_2' => $request->detain_class_2_charges[$index],
                                'national_charges_class_3' => $request->detain_class_3_charges[$index]
                            ]);
                        }


                    }

                    //Replacement and Try and Buy charges
                    if ($request->detain_booking_record != null) {
                        BookingTypeCharges::where(['id' => $request->detain_booking_record])->update([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'replacement_charges' => $request->detain_replacement_charges,
                            'try_and_buy_charges' => $request->detain_tnb_charges,
                            'reverse_pickup_charges' => $request->detain_reverse_charges
                        ]);
                    } else {
                        BookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'replacement_charges' => $request->detain_replacement_charges,
                            'try_and_buy_charges' => $request->detain_tnb_charges,
                            'reverse_pickup_charges' => $request->detain_reverse_charges
                        ]);
                    }


                    //Cash handling Charges
                    if ($request->has('detain_cash_handling_switch') && $request->detain_cash_handling_switch == 'on') {
                        CashHandlingCharge::where(['user_id' => $id, 'shipping_mode_id' => 3])->whereNotIn('id', $request->detain_cash_record)->delete();

                        foreach ($request->detain_cash_record as $index => $detain_cash_record) {
                            if ($request->detain_cash_record[$index] != null) {
                                CashHandlingCharge::where(['id' => $request->detain_cash_record[$index]])->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 3,
                                    'range_up' => $request->detain_cash_range_up[$index],
                                    'range_down' => $request->detain_cash_range_down[$index],
                                    'charges' => $request->detain_cash_charges[$index]
                                ]);
                            }
                            if ($request->detain_cash_record[$index] == null) {
                                CashHandlingCharge::create([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 3,
                                    'range_up' => $request->detain_cash_range_up[$index],
                                    'range_down' => $request->detain_cash_range_down[$index],
                                    'charges' => $request->detain_cash_charges[$index]
                                ]);
                            }
                        }
                    }
                    //insurance charges
                    if ($request->has('detain_insurance_charges_switch') && $request->detain_insurance_charges_switch == 'on') {
                        InsuranceCharge::where(['user_id' => $id, 'shipping_mode_id' => 3])->whereNotIn('id', $request->detain_insurance_record)->delete();
                        foreach ($request->detain_insurance_record as $insurance => $detain_insurance_record) {
                            if ($request->detain_insurance_record[$insurance] != null) {
                                InsuranceCharge::where(['id' => $request->detain_insurance_record[$insurance]])->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 3,
                                    'range_up' => $request->detain_ins_range_up[$insurance],
                                    'range_down' => $request->detain_ins_range_down[$insurance],
                                    'charges' => $request->detain_ins_charges[$insurance]
                                ]);
                            }
                            if ($request->detain_insurance_record[$insurance] == null) {
                                InsuranceCharge::create([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 3,
                                    'range_up' => $request->detain_ins_range_up[$insurance],
                                    'range_down' => $request->detain_ins_range_down[$insurance],
                                    'charges' => $request->detain_ins_charges[$insurance]
                                ]);
                            }
                        }
                    }
                    //Return Charges
                    if ($request->has('detain_return_switch') && $request->detain_return_switch == 'on') {
                        if ($request->detain_return_record != null) {
                            ReturnCharge::where(['id' => $request->detain_return_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'local' => $request->detain_return_local_charges,
                                'national_charges_class_0' => $request->detain_return_class_0_charges,
                                'national_charges_class_1' => $request->detain_return_class_1_charges,
                                'national_charges_class_2' => $request->detain_return_class_2_charges,
                                'national_charges_class_3' => $request->detain_return_class_3_charges
                            ]);
                        } elseif ($request->detain_return_record == null) {
                            ReturnCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'local' => $request->detain_return_local_charges,
                                'national_charges_class_0' => $request->detain_return_class_0_charges,
                                'national_charges_class_1' => $request->detain_return_class_1_charges,
                                'national_charges_class_2' => $request->detain_return_class_2_charges,
                                'national_charges_class_3' => $request->detain_return_class_3_charges
                            ]);
                        }

                    }
                    //Return Charges
                    if ($request->has('detain_fuel_switch') && $request->detain_fuel_switch == 'on') {
                        if ($request->detain_fuel_record != null) {
                            FuelSurcharge::where(['id' => $request->detain_fuel_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'fuel_surcharge' => $request->detain_fuel_surcharge
                            ]);
                        } elseif ($request->detain_fuel_record == null) {
                            FuelSurcharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'fuel_surcharge' => $request->detain_fuel_surcharge
                            ]);
                        }

                    }

                    $discount_cash = 0;
                    $discount_weight = 0;
                    $discount_insurance = 0;
                    $discount_return = 0;
                    $discount_packaging = 0;

                    if ($request->has('detain_discount_weight_switch') && $request->detain_discount_weight_switch == 'on') {
                        $discount_weight = $request->detain_discount_weight_rate != null ? $request->detain_discount_weight_rate : 0;
//                    $discount_weight = $request->detain_discount_weight_rate;
                    }
                    if ($request->has('detain_discount_cash_switch') && $request->detain_discount_cash_switch == 'on') {
                        $discount_cash = $request->detain_discount_cash_rate != null ? $request->detain_discount_cash_rate : 0;
                    }
                    if ($request->has('detain_discount_insurance_switch') && $request->detain_discount_insurance_switch == 'on') {
                        $discount_insurance = $request->detain_discount_insurance_rate != null ? $request->detain_discount_insurance_rate : 0;
                    }
                    if ($request->has('detain_discount_return_switch') && $request->detain_discount_return_switch == 'on') {
                        $discount_return = $request->detain_discount_return_rate != null ? $request->detain_discount_insurance_rate : 0;
                    }
                    if ($request->has('detain_discount_packaging_switch') && $request->detain_discount_packaging_switch == 'on') {
                        $discount_packaging = $request->detain_discount_packaging_rate != null ? $request->detain_discount_packaging_rate : 0;
                    }
                    if ($discount_weight != 0 || $discount_cash != 0 || $discount_insurance != 0 || $discount_return != 0 || $discount_packaging != 0) {


                        $date_str = $request->detain_daterange;
                        $date_sep = explode(' - ', $date_str);
                        $date_to = explode('/', $date_sep[0]);
                        $date_from = explode('/', $date_sep[1]);
                        $to = Carbon::create($date_to[2], $date_to[0], $date_to[1], 0, 0, 0, 'UTC')->toDateTimeString();
                        $from = Carbon::create($date_from[2], $date_from[0], $date_from[1], 0, 0, 0, 'UTC')->toDateTimeString();

                        if ($request->detain_discount_record != null) {
                            DiscountCharge::where(['id' => $request->detain_discount_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'title' => $request->detain_discount_title,
                                'weight' => $discount_weight,
                                'cash' => $discount_cash,
                                'insurance' => $discount_insurance,
                                'return' => $discount_return,
                                'packaging' => $discount_packaging,
                                'to' => $to,
                                'from' => $from,
                                'added_by' => Auth::id()
                            ]);
                        } elseif ($request->detain_discount_record == null) {
                            DiscountCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'title' => $request->detain_discount_title,
                                'weight' => $discount_weight,
                                'cash' => $discount_cash,
                                'insurance' => $discount_insurance,
                                'return' => $discount_return,
                                'packaging' => $discount_packaging,
                                'to' => $to,
                                'from' => $from,
                                'added_by' => Auth::id()
                            ]);
                        }

                    }

                    if($request->has('d_discount_destination_wise_weight_switch') && $request->d_discount_destination_wise_weight_switch == "on")
                    {
                        foreach($request->discount_d_destination as $parent_key => $destination)
                        {
                            $discount_wa_switch = array();
                            $discount_wa_spkg = array();
                            foreach($request->discount_d_wa_range_up[$parent_key] as $child_key => $value)
                            {
                                if ($request->has('discount_d_wa_switch.'.$parent_key)) {
                                    if (array_key_exists($child_key, $request->discount_d_wa_switch[$parent_key])) {
                                        $discount_wa_switch[$child_key] = 1;
                                    } else {
                                        $discount_wa_switch[$child_key] = 0;
                                    };
                                } else {
                                    $discount_wa_switch[$child_key] = 0;
                                }
                                if ($request->has('discount_d_wa_spkg.'.$parent_key)) {
                                    if (array_key_exists($child_key, $request->discount_d_wa_spkg[$parent_key])) {
                                        $discount_wa_spkg[$child_key] = $request->discount_d_wa_spkg[$parent_key][$child_key];
                                    } else {
                                        $discount_wa_spkg[$child_key] = 0.5;
                                    };
                                } else {
                                    $discount_wa_spkg[$child_key] = 0.5;
                                }
                                DiscountWeightCharge::create([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 3,
                                    'destination_id' => $destination,
                                    'range_up' => $request->discount_d_wa_range_up[$parent_key][$child_key],
                                    'range_down' => $request->discount_d_wa_range_down[$parent_key][$child_key],
                                    'weight_addition' => $discount_wa_switch[$child_key],
                                    'spkg' => $discount_wa_spkg[$child_key],
                                    'local_or_6hr' => $request->discount_d_wa_local_charges[$parent_key][$child_key],
                                ]);
                            }
                        }
                    }

                }
                if ($request->has('detain_dws_weight')) {
                    if ($request->detain_dws_weight == 2) {
                        DwsWeightChargesController::edit($id, 3, 2, Auth::id());


                    } else {
                        DwsWeightChargesController::edit($id, 3, 1, Auth::id());
                    }

                } else {
                    DwsWeightChargesController::delete_dws_rate($id, 3);
                }
            }

            //sameday
            if ($request->has('sameday_main_switch') && $request->sameday_main_switch == 'on') {

                if ($request->has('sameday_default') && $request->sameday_default == 'on') {
                    $default_shipping_mode = User::where('id', $id)->update([
                        'default_shipping_mode' => 4
                    ]);
                }

                $ONRateAlready = RateStatus::where(['user_id' => $id, 'shipping_mode_id' => 4])->get();

                if (!$ONRateAlready->isEmpty()) {
                    RateOriginHub::where(['user_id' => $id, 'shipping_mode_id' => 4])->delete();
                    if ($request->has('sameday_origin_hubs')) {
                        foreach ($request->sameday_origin_hubs as $origin_id) {
                            $rate_origin_hub = new RateOriginHub();
                            $rate_origin_hub->user_id = $id;
                            $rate_origin_hub->shipping_mode_id = 4;
                            $rate_origin_hub->city_id = $origin_id;
                            $rate_origin_hub->save();
                        }
                    }
                    RateDestinationHub::where(['user_id' => $id, 'shipping_mode_id' => 4])->delete();
                    if ($request->has('sameday_destination_hubs')) {
                        foreach ($request->sameday_destination_hubs as $destination_id) {
                            $rate_destination_hub = new RateDestinationHub();
                            $rate_destination_hub->user_id = $id;
                            $rate_destination_hub->shipping_mode_id = 4;
                            $rate_destination_hub->city_id = $destination_id;
                            $rate_destination_hub->save();
                        }
                    }
                    $wa_switch = array();
                    $wa_spkg = array();
                    WeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 4])->whereNotIn('id', $request->sameday_weight_record)->delete();
                    foreach ($request->sameday_weight_record as $index => $sameday_weight_record) {
                        if ($request->has('sameday_wa_switch')) {
                            if (array_key_exists($index, $request->sameday_wa_switch)) {
                                $wa_switch[$index] = 1;
                            } else {
                                $wa_switch[$index] = 0;
                            };
                        } else {
                            $wa_switch[$index] = 0;
                        }
                        if ($request->has('sameday_wa_spkg')) {
                            if (array_key_exists($index, $request->sameday_wa_spkg)) {
                                $wa_spkg[$index] = $request->sameday_wa_spkg[$index];
                            } else {
                                $wa_spkg[$index] = 0;
                            };
                        } else {
                            $wa_spkg[$index] = 0;
                        }
                        if ($request->sameday_weight_record[$index] != null) {

                            $weight_row = WeightCharge::where('id', $request->sameday_weight_record[$index])
                                ->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 4,
                                    'range_up' => $request->sameday_wa_range_up[$index],
                                    'range_down' => $request->sameday_wa_range_down[$index],
                                    'weight_addition' => $wa_switch[$index],
                                    'spkg' => $wa_spkg[$index],
                                    'local_or_6hr' => $request->sameday_wa_local_charges[$index],
                                    'national_charges_class_0' => $request->sameday_class_0_charges[$index],
                                    'national_charges_class_1' => 0,
                                    'national_charges_class_2' => 0,
                                    'national_charges_class_3' => 0
                                ]);
                        }
                        if ($request->sameday_weight_record[$index] == null) {
                            WeightCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'range_up' => $request->sameday_wa_range_up[$index],
                                'range_down' => $request->sameday_wa_range_down[$index],
                                'weight_addition' => $wa_switch[$index],
                                'spkg' => $wa_spkg[$index],
                                'local_or_6hr' => $request->sameday_wa_local_charges[$index],
                                'national_charges_class_0' => $request->sameday_class_0_charges[$index],
                                'national_charges_class_1' => 0,
                                'national_charges_class_2' => 0,
                                'national_charges_class_3' => 0
                            ]);
                        }


                    }

                    //Replacement and Try and Buy charges
                    if ($request->sameday_booking_record != null) {
                        BookingTypeCharges::where(['id' => $request->sameday_booking_record])->update([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'replacement_charges' => $request->sameday_replacement_charges,
                            'try_and_buy_charges' => $request->sameday_tnb_charges,
                            'reverse_pickup_charges' => $request->sameday_reverse_charges
                        ]);
                    } else {
                        BookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'replacement_charges' => $request->sameday_replacement_charges,
                            'try_and_buy_charges' => $request->sameday_tnb_charges,
                            'reverse_pickup_charges' => $request->sameday_reverse_charges
                        ]);
                    }


                    //Cash handling Charges
                    if ($request->has('sameday_cash_handling_switch') && $request->sameday_cash_handling_switch == 'on') {
                        CashHandlingCharge::where(['user_id' => $id, 'shipping_mode_id' => 4])->whereNotIn('id', $request->sameday_cash_record)->delete();

                        foreach ($request->sameday_cash_record as $index => $sameday_cash_record) {
                            if ($request->sameday_cash_record[$index] != null) {
                                CashHandlingCharge::where(['id' => $request->sameday_cash_record[$index]])->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 4,
                                    'range_up' => $request->sameday_cash_range_up[$index],
                                    'range_down' => $request->sameday_cash_range_down[$index],
                                    'charges' => $request->sameday_cash_charges[$index]
                                ]);
                            }
                            if ($request->sameday_cash_record[$index] == null) {
                                CashHandlingCharge::create([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 4,
                                    'range_up' => $request->sameday_cash_range_up[$index],
                                    'range_down' => $request->sameday_cash_range_down[$index],
                                    'charges' => $request->sameday_cash_charges[$index]
                                ]);
                            }
                        }
                    }
                    //insurance charges
                    if ($request->has('sameday_insurance_charges_switch') && $request->sameday_insurance_charges_switch == 'on') {
                        InsuranceCharge::where(['user_id' => $id, 'shipping_mode_id' => 4])->whereNotIn('id', $request->sameday_insurance_record)->delete();
                        foreach ($request->sameday_insurance_record as $insurance => $sameday_insurance_record) {
                            if ($request->sameday_insurance_record[$insurance] != null) {
                                InsuranceCharge::where(['id' => $request->sameday_insurance_record[$insurance]])->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 4,
                                    'range_up' => $request->sameday_ins_range_up[$insurance],
                                    'range_down' => $request->sameday_ins_range_down[$insurance],
                                    'charges' => $request->sameday_ins_charges[$insurance]
                                ]);
                            }
                            if ($request->sameday_insurance_record[$insurance] == null) {
                                InsuranceCharge::create([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 4,
                                    'range_up' => $request->sameday_ins_range_up[$insurance],
                                    'range_down' => $request->sameday_ins_range_down[$insurance],
                                    'charges' => $request->sameday_ins_charges[$insurance]
                                ]);
                            }
                        }
                    }
                    //Return Charges
                    if ($request->has('sameday_return_switch') && $request->sameday_return_switch == 'on') {
                        if ($request->sameday_return_record != null) {
                            ReturnCharge::where(['id' => $request->sameday_return_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'local' => $request->sameday_return_local_charges,
                                'national_charges_class_0' => $request->sameday_return_class_0_charges,
                                'national_charges_class_1' => 0,
                                'national_charges_class_2' => 0,
                                'national_charges_class_3' => 0
                            ]);
                        } elseif ($request->sameday_return_record == null) {
                            ReturnCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'local' => $request->sameday_return_local_charges,
                                'national_charges_class_0' => $request->sameday_return_class_0_charges,
                                'national_charges_class_1' => 0,
                                'national_charges_class_2' => 0,
                                'national_charges_class_3' => 0
                            ]);
                        }

                    }
                    //Return Charges
                    if ($request->has('sameday_fuel_switch') && $request->sameday_fuel_switch == 'on') {
                        if ($request->sameday_fuel_record != null) {
                            FuelSurcharge::where(['id' => $request->sameday_fuel_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'fuel_surcharge' => $request->sameday_fuel_surcharge
                            ]);
                        } elseif ($request->sameday_fuel_record == null) {
                            FuelSurcharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'fuel_surcharge' => $request->sameday_fuel_surcharge
                            ]);
                        }

                    }

                    $discount_cash = 0;
                    $discount_weight = 0;
                    $discount_insurance = 0;
                    $discount_return = 0;
                    $discount_packaging = 0;

                    if ($request->has('sameday_discount_weight_switch') && $request->sameday_discount_weight_switch == 'on') {
                        $discount_weight = $request->sameday_discount_weight_rate != null ? $request->sameday_discount_weight_rate : 0;
//                    $discount_weight = $request->sameday_discount_weight_rate;
                    }
                    if ($request->has('sameday_discount_cash_switch') && $request->sameday_discount_cash_switch == 'on') {
                        $discount_cash = $request->sameday_discount_cash_rate != null ? $request->sameday_discount_cash_rate : 0;
                    }
                    if ($request->has('sameday_discount_insurance_switch') && $request->sameday_discount_insurance_switch == 'on') {
                        $discount_insurance = $request->sameday_discount_insurance_rate != null ? $request->sameday_discount_insurance_rate : 0;
                    }
                    if ($request->has('sameday_discount_return_switch') && $request->sameday_discount_return_switch == 'on') {
                        $discount_return = $request->sameday_discount_return_rate != null ? $request->sameday_discount_insurance_rate : 0;
                    }
                    if ($request->has('sameday_discount_packaging_switch') && $request->sameday_discount_packaging_switch == 'on') {
                        $discount_packaging = $request->sameday_discount_packaging_rate != null ? $request->sameday_discount_packaging_rate : 0;
                    }
                    if ($discount_weight != 0 || $discount_cash != 0 || $discount_insurance != 0 || $discount_return != 0 || $discount_packaging != 0) {


                        $date_str = $request->sameday_daterange;
                        $date_sep = explode(' - ', $date_str);
                        $date_to = explode('/', $date_sep[0]);
                        $date_from = explode('/', $date_sep[1]);
                        $to = Carbon::create($date_to[2], $date_to[0], $date_to[1], 0, 0, 0, 'UTC')->toDateTimeString();
                        $from = Carbon::create($date_from[2], $date_from[0], $date_from[1], 0, 0, 0, 'UTC')->toDateTimeString();

                        if ($request->sameday_discount_record != null) {
                            DiscountCharge::where(['id' => $request->sameday_discount_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'title' => $request->sameday_discount_title,
                                'weight' => $discount_weight,
                                'cash' => $discount_cash,
                                'insurance' => $discount_insurance,
                                'return' => $discount_return,
                                'packaging' => $discount_packaging,
                                'to' => $to,
                                'from' => $from,
                                'added_by' => Auth::id()
                            ]);
                        } elseif ($request->sameday_discount_record == null) {
                            DiscountCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'title' => $request->sameday_discount_title,
                                'weight' => $discount_weight,
                                'cash' => $discount_cash,
                                'insurance' => $discount_insurance,
                                'return' => $discount_return,
                                'packaging' => $discount_packaging,
                                'to' => $to,
                                'from' => $from,
                                'added_by' => Auth::id()
                            ]);
                        }

                    }

                    if($request->has('sd_discount_destination_wise_weight_switch') && $request->sd_discount_destination_wise_weight_switch == "on")
                    {
                        foreach($request->discount_sd_destination as $parent_key => $destination)
                        {
                            $discount_wa_switch = array();
                            $discount_wa_spkg = array();
                            foreach($request->discount_sd_wa_range_up[$parent_key] as $child_key => $value)
                            {
                                if ($request->has('discount_sd_wa_switch.'.$parent_key)) {
                                    if (array_key_exists($child_key, $request->discount_sd_wa_switch[$parent_key])) {
                                        $discount_wa_switch[$child_key] = 1;
                                    } else {
                                        $discount_wa_switch[$child_key] = 0;
                                    };
                                } else {
                                    $discount_wa_switch[$child_key] = 0;
                                }
                                if ($request->has('discount_sd_wa_spkg.'.$parent_key)) {
                                    if (array_key_exists($child_key, $request->discount_sd_wa_spkg[$parent_key])) {
                                        $discount_wa_spkg[$child_key] = $request->discount_sd_wa_spkg[$parent_key][$child_key];
                                    } else {
                                        $discount_wa_spkg[$child_key] = 0.5;
                                    };
                                } else {
                                    $discount_wa_spkg[$child_key] = 0.5;
                                }
                                DiscountWeightCharge::create([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 4,
                                    'destination_id' => $destination,
                                    'range_up' => $request->discount_sd_wa_range_up[$parent_key][$child_key],
                                    'range_down' => $request->discount_sd_wa_range_down[$parent_key][$child_key],
                                    'weight_addition' => $discount_wa_switch[$child_key],
                                    'spkg' => $discount_wa_spkg[$child_key],
                                    'local_or_6hr' => $request->discount_sd_wa_local_charges[$parent_key][$child_key],
                                ]);
                            }
                        }
                    }

                }
                if ($request->has('sameday_dws_weight')) {
                    if ($request->sameday_dws_weight == 2) {
                        DwsWeightChargesController::edit($id, 4, 2, Auth::id());
                    } else {
                        DwsWeightChargesController::edit($id, 4, 1, Auth::id());
                    }

                } else {
                    DwsWeightChargesController::delete_dws_rate($id, 4);
                }
            }

            self::discounted_cod_and_return($request,$id,2,'default');

            if ($request->has('warehouse_main_switch') && $request->warehouse_main_switch == 'on') {
                $wms_user_info = WmsUserInformation::where('user_id', $id);
                if ($wms_user_info->exists()) {
                    $wms_user_info = $wms_user_info->first();
                    $wms_user_info->invoicing_cycle = $request->invoicing_cycle;
                    $wms_user_info->invoicing_date = 1;
                    $wms_user_info->per_product_charges = ($request->has('ppc_switch')) ? 1 : 0;
                    $wms_user_info->per_square_foot_charges = ($request->has('psf_switch')) ? 1 : 0;
                    $wms_user_info->packing_charges = ($request->has('packing_charges_switch')) ? 1 : 0;
                    $wms_user_info->labelling_charges = ($request->has('labelling_charges_switch')) ? 1 : 0;
                    $wms_user_info->storage_charges = ($request->has('storage_charges_switch')) ? 1 : 0;
                } else {
                    $wms_user_info = new WmsUserInformation();
                    $wms_user_info->user_id = $id;
                    $wms_user_info->warehousing = 1;
                    $wms_user_info->invoicing_cycle = $request->invoicing_cycle;
                    $wms_user_info->invoicing_date = 1;
                    $wms_user_info->per_product_charges = ($request->has('ppc_switch')) ? 1 : 0;
                    $wms_user_info->per_square_foot_charges = ($request->has('psf_switch')) ? 1 : 0;
                    $wms_user_info->packing_charges = ($request->has('packing_charges_switch')) ? 1 : 0;
                    $wms_user_info->labelling_charges = ($request->has('labelling_charges_switch')) ? 1 : 0;
                    $wms_user_info->storage_charges = ($request->has('storage_charges_switch')) ? 1 : 0;

                }
                $wms_user_info->save();

                if ($request->has('ppc_switch')) {
                    $ppc = WmsPerProductCharge::where('user_id', $id);
                    if ($ppc->exists()) {
                        $ppc = $ppc->first();
                        $ppc->charges = $request->ppc_charges;
                    } else {
                        $ppc = new WmsPerProductCharge();
                        $ppc->user_id = $id;
                        $ppc->charges = $request->ppc_charges;
                    }
                    $ppc->save();
                } else {
                    WmsPerProductCharge::where('user_id', $id)->delete();
                }
                if ($request->has('psf_switch')) {
                    $psf = WmsPerSquareFootCharge::where('user_id', $id);
                    if ($psf->exists()) {
                        $psf = $psf->first();
                        $psf->charges = $request->psf_charges;
                    } else {
                        $psf = new WmsPerSquareFootCharge();
                        $psf->user_id = $id;
                        $psf->charges = $request->psf_charges;
                    }
                    $psf->save();

                }
                if ($request->has('storage_charges_switch')) {
                    WmsStorageTypeCharge::where('user_id', $id)->delete();
                    foreach ($request->storage_type as $key => $storage_type) {
                        $storage_charges = new WmsStorageTypeCharge();
                        $storage_charges->user_id = $id;
                        $storage_charges->storage_type_id = $storage_type;
                        $storage_charges->charges = $request->storage_type_charges[$key];
                        $storage_charges->save();
                    }
                } else {
                    WmsStorageTypeCharge::where('user_id', $id)->delete();
                }

                if ($request->has('packing_charges_switch')) {
                    WmsPackingCharge::where('user_id', $id)->delete();
                    foreach ($request->packing_type as $key => $packing) {
                        $ptype = new WmsPackingCharge();
                        $ptype->user_id = $id;
                        $ptype->packing_type_id = $packing;
                        $ptype->packing_size_id = $request->packing_size[$key];
                        $ptype->charges = $request->packing_charges[$key];
                        $ptype->save();
                    }
                } else {
                    WmsPackingCharge::where('user_id', $id)->delete();
                }

                if ($request->has('labelling_charges_switch')) {
                    $labelling = WmsLabellingCharge::where('user_id', $id);
                    if ($labelling->exists()) {
                        $labelling = $labelling->first();
                        $labelling->charges = $request->labelling_charges;
                    } else {
                        $labelling = new WmsLabellingCharge();
                        $labelling->user_id = $id;
                        $labelling->charges = $request->labelling_charges;
                    }
                    $labelling->save();

                } else {
                    WmsLabellingCharge::where('user_id', $id)->delete();
                }
            } else {
                $wms_user_info = WmsUserInformation::where('user_id', $id);
                if ($wms_user_info->exists()) {
                    $wms_user_info = $wms_user_info->first();
                    $wms_user_info->warehousing = 0;
                    $wms_user_info->save();
                }
            }
            User::where('id', $id)->update(['rate_status' => 1]);
            if ($request->has('rate_remarks') && $request->rate_remarks != null) {
                $rate_remark = new RateRemark();
                $rate_remark->user_id = $id;
                $rate_remark->remarks = $request->rate_remarks;
                $rate_remark->admin_id = Auth::id();
                $rate_remark->save();

            }

            if ($request->has('edit_commission') && $request->edit_commission == 1) {
                if ($request->total_commission > 0) {
                    $existing_sale_commission = SalesCommission::where('shipper_id', $id)->first();
                    $user_type = [];
                    if ($existing_sale_commission) {
                        $types = SalesCommissionUser::where('sales_commission_id', $existing_sale_commission->id)->pluck('user_type')->toArray();
                        foreach($types as $key => $user_type_value){
                            $user_type[$key+1] = $user_type_value;
                        }
                        SalesCommissionUser::where('sales_commission_id', $existing_sale_commission->id)->delete();
                        SalesCommissionExternalUser::where('shipper_id', $id)->delete();
                        SalesCommission::where('shipper_id', $id)->delete();
                    }
                    $total_commission = $request->total_commission;
                    $users_count = count($request->user_id);

                    $sales_commission = new SalesCommission();
                    $sales_commission->shipper_id = $id;
                    $sales_commission->commission_users_count = $users_count;
                    $sales_commission->commission = $total_commission;
                    $sales_commission->updated_by = Auth::id();
                    $sales_commission->save();
                    $sales_commission_id = $sales_commission->id;
                    $actual_commission = 0;
                    foreach ($request->tier_id as $row_id => $tier) {
                        $sales_tier = SalesTier::find($tier);
                        if ($sales_tier) {
                            $sales_commission_user = new SalesCommissionUser();
                            $sales_commission_user->sales_commission_id = $sales_commission_id;
                            $sales_commission_user->tier_type_id = $sales_tier->tier_type;
                            $sales_commission_user->tier_id = $tier;
                            if ($sales_tier->tier_type == 1) {
                                if (strpos($request->user_id[$row_id], 'riders') !== false) {                      
                                    $sales_commission_user->user_type = "2";
                                }  
                                if(isset($user_type[$row_id]) && $user_type[$row_id] == "2"){
                                    $sales_commission_user->user_type = "2";
                                } 
                                $sales_commission_user->user_id = $request->user_id[$row_id];
                            } else if ($sales_tier->tier_type == 2) {
                                $external_user = new SalesCommissionExternalUser();
                                $external_user->name = $request->user_id[$row_id];
                                $external_user->shipper_id = $id;
                                $external_user->save();
                                $sales_commission_user->user_id = $external_user->id;
                            }
                            $sales_commission_user->commission = $request->commission_percentage[$row_id];
                            $actual_commission += $request->commission_percentage[$row_id];
                            $sales_commission_user->save();
                        }
                    }
                    $sales_commission->commission = $actual_commission;
                    $sales_commission->save();
                } else {
                    $existing_sale_commission = SalesCommission::where('shipper_id', $id)->first();
                    if ($existing_sale_commission) {
                        SalesCommissionUser::where('sales_commission_id', $existing_sale_commission->id)->delete();
                        SalesCommissionExternalUser::where('shipper_id', $id)->delete();
                        SalesCommission::where('shipper_id', $id)->delete();
                    }
                }
            }
            if ($request->authorize == 1) { 
                DwsWeightChargesController::approve($id,1);
                User::where('id', $id)->update(['rate_status' => 0, 'status' => 2, 'rates_authorized_by' => Auth::id(), 'rates_approved_at' => Carbon::now()]);
                return redirect(route('admin.accounts.pending'))->with('success', 'User is now authorized.');
            }

            return redirect()->back()->with('success', 'All Rates are updateddd');
        }

        if ($user['status'] == 3) {
            $messages = [
                'sms_charges.required' => 'The sms charges field is required.',
                'sms_charges.gt' => 'The sms charges must be greater than 0.',
                'on_wa_range_up.*.required' => 'The overnight range up field is required.',
                'on_wa_range_up.*.numeric' => 'The overnight range up field must be numeric or decimal.',
                'on_wa_range_up.*.between' => 'The overnight range up field must be between 0 to 999.99',
                'on_wa_range_down.*.required' => 'The overnight range down field is required.',
                'on_wa_range_down.*.numeric' => 'The overnight range down field must be numeric or decimal.',
                'on_wa_range_down.*.between' => 'The overnight range down field must be between 0 to 999.99',
                'on_wa_spkg.*.numeric' => 'The overnight KG Range field must be numeric.',
                'on_wa_local_charges.*.required' => 'The overnight local charges field is required.',
                'on_wa_local_charges.*.numeric' => 'The overnight local charges field must be numeric.',
                'on_class_0_charges.*.numeric' => 'The overnight class A charges field must be numeric.',
                'on_class_0_charges.*.required' => 'The overnight class A charges field is required.',
                'on_class_1_charges.*.required' => 'The overnight class B charges field is required.',
                'on_class_2_charges.*.required' => 'The overnight class C charges field is required.',
                'on_class_3_charges.*.required' => 'The overnight class D charges field is required.',
//            'on_class_0_charges.*.numeric' => 'The overnight national charges field must be numeric.',
                'on_replacement_charges.numeric' => 'The overnight replacement charges field must be numeric.',
                'on_replacement_charges.required' => 'The overnight replacement charges field is required.',
                'on_tnb_charges.numeric' => 'The overnight try and buy charges field must be numeric.',
                'on_tnb_charges.required' => 'The overnight try and buy charges field is required.',
                'on_reverse_charges.numeric' => 'The overnight reverse pickup charges field must be numeric.',
                'on_reverse_charges.required' => 'The overnight reverse pickup charges field is required.',
                'on_cash_range_up.*.required_if' => 'The overnight cash range up field is required.',
                'on_cash_range_up.*.numeric' => 'The overnight cash range up field must be numeric.',
                'on_cash_range_down.*.required_if' => 'The overnight cash range down field is required.',
                'on_cash_range_down.*.numeric' => 'The overnight cash range down field must be numeric.',
                'on_cash_charges.*.required_if' => 'The overnight cash charges field is required.',
                //'on_cash_charges.*.string' => 'The overnight cash charges field must be string.',
                'on_ins_range_up.*.required_if' => 'The overnight insurance range up field is required.',
                'on_ins_range_up.*.numeric' => 'The overnight insurance range up field must be numeric or percentage.',
                'on_ins_range_down.*.required_if' => 'The overnight insurance range down field is required.',
                'on_ins_range_down.*.numeric' => 'The overnight insurance range down field must be numeric or percentage.',
                'on_ins_charges.*.required_if' => 'The overnight insurance charges field is required.',
                //'on_ins_charges.*.string' => 'The overnight insurance charges field must be string.',
                'on_return_local_charges.required_if' => 'The overnight return local charges field is required.',
                'on_return_local_charges.numeric' => 'The overnight return local charges field must be numeric or percentage.',
                'on_return_class_0_charges.*.required_if' => 'The overnight class A return charges field is required.',
                'on_return_class_1_charges.*.required_if' => 'The overnight class B return charges field is required.',
                'on_return_class_2_charges.*.required_if' => 'The overnight class C return charges field is required.',
                'on_return_class_3_charges.*.required_if' => 'The overnight class D return charges field is required.',
                'overnight_fuel_surcharge.required_if' => 'The overnight return national charges field is required.',
                'overnight_fuel_surcharge.numeric' => 'The overnight return national charges field must be numeric or percentage.',

                'on_discount_title.required_if' => 'The overnight discount title field must be required',
                'on_daterange.required_if' => 'The overnight discount date range field must be required',
                'on_discount_weight_rate.required_if' => 'The overnight discount weight field must be required',
//            'on_discount_weight_rate.numeric' => 'The overnight discount weight field must be numeric',
                'on_discount_cash_rate.required_if' => 'The overnight discount cash field must be required',
//            'on_discount_cash_rate.numeric' => 'The overnight discount cash field must be numeric',
                'on_discount_insurance_rate.required_if' => 'The overnight discount insurance field must be required',
//            'on_discount_insurance_rate.numeric' => 'The overnight discount insurance field must be numeric',
                'on_discount_return_rate.required_if' => 'The overnight discount return field must be required',
//            'on_discount_return_rate.numeric' => 'The overnight discount return field must be numeric',
                'on_discount_packaging_rate.required_if' => 'The overnight discount packaging field must be required',
//            'on_discount_packaging_rate.numeric' => 'The overnight discount packaging field must be numeric',
                'on_discount_title.required_with' => 'The overnight discount title field is required',
                'on_daterange.required_with' => 'The overnight discount date field is required',
                //overland starts
                'ol_wa_range_up.*.required' => 'The overland range up field is required.',
                'ol_wa_range_up.*.numeric' => 'The overland range up field must be numeric or decimal.',
                'ol_wa_range_up.*.between' => 'The overland range up field must be between 0 to 999.99',
                'ol_wa_range_down.*.required' => 'The overland range down field is required.',
                'ol_wa_range_down.*.numeric' => 'The overland range down field must be numeric or decimal.',
                'ol_wa_range_down.*.between' => 'The overland range down field must be between 0 to 999.99',
                'ol_wa_spkg.*.numeric' => 'The overland KG Range field must be numeric.',
                'ol_wa_local_charges.*.required' => 'The overland local charges field is required.',
                'ol_wa_local_charges.*.numeric' => 'The overland local charges field must be numeric.',
                'ol_class_0_charges.*.required' => 'The overland class A charges field is required.',
                'ol_class_0_charges.*.numeric' => 'The overland class A charges field must be numeric.',
                'ol_class_1_charges.*.required' => 'The overland class B charges field is required.',
                'ol_class_2_charges.*.required' => 'The overland class C charges field is required.',
                'ol_class_3_charges.*.required' => 'The overland class D charges field is required.',
                'ol_replacement_charges.numeric' => 'The overland replacement charges field must be numeric.',
                'ol_replacement_charges.required' => 'The overland replacement charges field is required.',
                'ol_tnb_charges.numeric' => 'The overland try and buy charges field must be numeric.',
                'ol_tnb_charges.required' => 'The overland try and buy charges field is required.',
                'ol_reverse_charges.numeric' => 'The overland reverse pickup charges field must be numeric.',
                'ol_reverse_charges.required' => 'The overland reverse pickup charges field is required.',
                'ol_cash_range_up.*.required_if' => 'The overland cash range up field is required.',
                'ol_cash_range_up.*.numeric' => 'The overland cash range up field must be numeric.',
                'ol_cash_range_down.*.required_if' => 'The overland cash range down field is required.',
                'ol_cash_range_down.*.numeric' => 'The overland cash range down field must be numeric.',
                'ol_cash_charges.*.required_if' => 'The overland cash charges field is required.',
                //'ol_cash_charges.*.numeric' => 'The overland cash charges field must be numeric or percentage.',
                'ol_ins_range_up.*.required_if' => 'The overland insurance range up field is required.',
                'ol_ins_range_up.*.numeric' => 'The overland insurance range up field must be numeric or percentage.',
                'ol_ins_range_down.*.required_if' => 'The overland insurance range down field is required.',
                'ol_ins_range_down.*.numeric' => 'The overland insurance range down field must be numeric or percentage.',
                'ol_ins_charges.*.required_if' => 'The overland insurance charges field is required.',
                //'ol_ins_charges.*.numeric' => 'The overland insurance charges field must be numeric or percentage.',
                'ol_return_local_charges.required_if' => 'The overland return local charges field is required.',
                'ol_return_local_charges.numeric' => 'The overland return local charges field must be numeric or percentage.',
                'ol_return_class_0_charges.*.required_if' => 'The overland class A return charges field is required.',
                'ol_return_class_1_charges.*.required_if' => 'The overland class B return charges field is required.',
                'ol_return_class_2_charges.*.required_if' => 'The overland class C return charges field is required.',
                'ol_return_class_3_charges.*.required_if' => 'The overland class D return charges field is required.',
                'overland_fuel_surcharge.required_if' => 'The overland return national charges field is required.',
                'overland_fuel_surcharge.numeric' => 'The overland return national charges field must be numeric or percentage.',

                'ol_discount_title.required_if' => 'The overland discount title field must be required',
                'ol_daterange.required_if' => 'The overland discount date range field must be required',
                'ol_discount_weight_rate.required_if' => 'The overland discount weight field must be required',
//            'ol_discount_weight_rate.numeric' => 'The overland discount weight field must be numeric',
                'ol_discount_cash_rate.required_if' => 'The overland discount cash field must be required',
//            'ol_discount_cash_rate.numeric' => 'The overland discount cash field must be numeric',
                'ol_discount_insurance_rate.required_if' => 'The overland discount insurance field must be required',
//            'ol_discount_insurance_rate.numeric' => 'The overland discount insurance field must be numeric',
                'ol_discount_return_rate.required_if' => 'The overland discount return field must be required',
//            'ol_discount_return_rate.numeric' => 'The overland discount return field must be numeric',
                'ol_discount_packaging_rate.required_if' => 'The overland discount packaging field must be required',
//            'ol_discount_packaging_rate.numeric' => 'The overland discount packaging field must be numeric',
                'ol_discount_title.required_with' => 'The overland discount title field is required',
                'ol_daterange.required_with' => 'The overland discount date field is required',
                //overland end and detain starts
                'detain_wa_range_up.*.required' => 'The detain range up field is required.',
                'detain_wa_range_up.*.numeric' => 'The detain range up field must be numeric or decimal.',
                'detain_wa_range_up.*.between' => 'The detain range up field must be between 0 to 999.99',
                'detain_wa_range_down.*.required' => 'The detain range down field is required.',
                'detain_wa_range_down.*.numeric' => 'The detain range down field must be numeric or decimal.',
                'detain_wa_range_down.*.between' => 'The detain range down field must be between 0 to 999.99',
                'detain_wa_spkg.*.numeric' => 'The detain KG Range field must be numeric.',
                'detain_wa_local_charges.*.required' => 'The detain local charges field is required.',
                'detain_wa_local_charges.*.numeric' => 'The detain local charges field must be numeric.',
                'detain_class_0_charges.*.required' => 'The detain class A charges field is required.',
                'detain_class_0_charges.*.numeric' => 'The detain class A charges field must be numeric.',
                'detain_class_1_charges.*.required' => 'The detain class B charges field is required.',
                'detain_class_2_charges.*.required' => 'The detain class C charges field is required.',
                'detain_class_3_charges.*.required' => 'The detain class D charges field is required.',
                'detain_replacement_charges.numeric' => 'The detain replacement charges field must be numeric.',
                'detain_replacement_charges.required' => 'The detain replacement charges field is required.',
                'detain_tnb_charges.numeric' => 'The detain try and buy charges field must be numeric.',
                'detain_tnb_charges.required' => 'The detain try and buy charges field is required.',
                'detain_reverse_charges.numeric' => 'The detain reverse pickup charges field must be numeric.',
                'detain_reverse_charges.required' => 'The detain reverse pickup charges field is required.',
                'detain_cash_range_up.*.required_if' => 'The detain cash range up field is required.',
                'detain_cash_range_up.*.numeric' => 'The detain cash range up field must be numeric.',
                'detain_cash_range_down.*.required_if' => 'The detain cash range down field is required.',
                'detain_cash_range_down.*.numeric' => 'The detain cash range down field must be numeric.',
                'detain_cash_charges.*.required_if' => 'The detain cash charges field is required.',
                //'detain_cash_charges.*.numeric' => 'The detain cash charges field must be numeric or percentage.',
                'detain_ins_range_up.*.required_if' => 'The detain insurance range up field is required.',
                'detain_ins_range_up.*.numeric' => 'The detain insurance range up field must be numeric or percentage.',
                'detain_ins_range_down.*.required_if' => 'The detain insurance range down field is required.',
                'detain_ins_range_down.*.numeric' => 'The detain insurance range down field must be numeric or percentage.',
                'detain_ins_charges.*.required_if' => 'The detain insurance charges field is required.',
                //'detain_ins_charges.*.numeric' => 'The detain insurance charges field must be numeric or percentage.',
                'detain_return_local_charges.required_if' => 'The detain return local charges field is required.',
                'detain_return_local_charges.numeric' => 'The detain return local charges field must be numeric or percentage.',
                'detain_return_class_0_charges.*.required_if' => 'The detain class A return charges field is required.',
                'detain_return_class_1_charges.*.required_if' => 'The detain class B return charges field is required.',
                'detain_return_class_2_charges.*.required_if' => 'The detain class C return charges field is required.',
                'detain_return_class_3_charges.*.required_if' => 'The detain class D return charges field is required.',
                'detain_fuel_surcharge.required_if' => 'The detain return national charges field is required.',
                'detain_fuel_surcharge.numeric' => 'The detain return national charges field must be numeric or percentage.',

                'detain_discount_title.required_if' => 'The detain discount title field must be required',
                'detain_daterange.required_if' => 'The detain discount date range field must be required',
                'detain_discount_weight_rate.required_if' => 'The detain discount weight field must be required',
//            'detain_discount_weight_rate.numeric' => 'The detain discount weight field must be numeric',
                'detain_discount_cash_rate.required_if' => 'The detain discount cash field must be required',
//            'detain_discount_cash_rate.numeric' => 'The detain discount cash field must be numeric',
                'detain_discount_insurance_rate.required_if' => 'The detain discount insurance field must be required',
//            'detain_discount_insurance_rate.numeric' => 'The detain discount insurance field must be numeric',
                'detain_discount_return_rate.required_if' => 'The detain discount return field must be required',
//            'detain_discount_return_rate.numeric' => 'The detain discount return field must be numeric',
                'detain_discount_packaging_rate.required_if' => 'The detain discount packaging field must be required',
//            'detain_discount_packaging_rate.numeric' => 'The detain discount packaging field must be numeric',
                'detain_discount_title.required_with' => 'The detain discount title field is required',
                'detain_daterange.required_with' => 'The detain discount date field is required',
                //detain ends and sameday starts
                'sameday_wa_range_up.*.required' => 'The sameday range up field is required.',
                'sameday_wa_range_up.*.numeric' => 'The sameday range up field must be numeric or decimal.',
                'sameday_wa_range_up.*.between' => 'The sameday range up field must be between 0 to 999.99',
                'sameday_wa_range_down.*.required' => 'The sameday range down field is required.',
                'sameday_wa_range_down.*.numeric' => 'The sameday range down field must be numeric or decimal.',
                'sameday_wa_range_down.*.between' => 'The sameday range down field must be between 0 to 999.99',
                'sameday_wa_spkg.*.numeric' => 'The sameday KG Range field must be numeric.',
                'sameday_wa_local_charges.*.required' => 'The sameday local charges field is required.',
                'sameday_wa_local_charges.*.numeric' => 'The sameday local charges field must be numeric.',
                'sameday_wa_class_0_charges.*.required' => 'The sameday class A charges field is required.',
                'sameday_wa_class_0_charges.*.numeric' => 'The sameday class A charges field must be numeric.',
                'sameday_replacement_charges.numeric' => 'The sameday replacement charges field must be numeric.',
                'sameday_replacement_charges.required' => 'The sameday replacement charges field is required.',
                'sameday_tnb_charges.numeric' => 'The sameday try and buy charges field must be numeric.',
                'sameday_tnb_charges.required' => 'The sameday try and buy charges field is required.',
                'sameday_reverse_charges.numeric' => 'The sameday reverse pickup charges field must be numeric.',
                'sameday_reverse_charges.required' => 'The sameday reverse pickup charges field is required.',
                'sameday_cash_range_up.*.required_if' => 'The sameday cash range up field is required.',
                'sameday_cash_range_up.*.numeric' => 'The sameday cash range up field must be numeric.',
                'sameday_cash_range_down.*.required_if' => 'The sameday cash range down field is required.',
                'sameday_cash_range_down.*.numeric' => 'The sameday cash range down field must be numeric.',
                'sameday_cash_charges.*.required_if' => 'The sameday cash charges field is required.',
                // 'sameday_cash_charges.*.numeric' => 'The sameday cash charges field must be numeric or percentage.',
                'sameday_ins_range_up.*.required_if' => 'The sameday insurance range up field is required.',
                'sameday_ins_range_up.*.numeric' => 'The sameday insurance range up field must be numeric or percentage.',
                'sameday_ins_range_down.*.required_if' => 'The sameday insurance range down field is required.',
                'sameday_ins_range_down.*.numeric' => 'The sameday insurance range down field must be numeric or percentage.',
                'sameday_ins_charges.*.required_if' => 'The sameday insurance charges field is required.',
                //'sameday_ins_charges.*.numeric' => 'The sameday insurance charges field must be numeric or percentage.',
                'sameday_return_local_charges.required_if' => 'The sameday return local charges field is required.',
                'sameday_return_local_charges.numeric' => 'The sameday return local charges field must be numeric or percentage.',
                'sameday_return_class_0_charges.*.required_if' => 'The sameday class A return charges field is required.',
                'sameday_return_class_1_charges.*.required_if' => 'The sameday class B return charges field is required.',
                'sameday_return_class_2_charges.*.required_if' => 'The sameday class C return charges field is required.',
                'sameday_return_class_3_charges.*.required_if' => 'The sameday class D return charges field is required.',
                'sameday_fuel_surcharge.required_if' => 'The sameday return national charges field is required.',
                'sameday_fuel_surcharge.numeric' => 'The sameday return national charges field must be numeric or percentage.',

                'sameday_discount_title.required_if' => 'The sameday discount title field must be required',
                'sameday_daterange.required_if' => 'The sameday discount date range field must be required',
                'sameday_discount_weight_rate.required_if' => 'The sameday discount weight field must be required',
//            'sameday_discount_weight_rate.numeric' => 'The sameday discount weight field must be numeric',
                'sameday_discount_cash_rate.required_if' => 'The sameday discount cash field must be required',
//            'sameday_discount_cash_rate.numeric' => 'The sameday discount cash field must be numeric',
                'sameday_discount_insurance_rate.required_if' => 'The sameday discount insurance field must be required',
//            'sameday_discount_insurance_rate.numeric' => 'The sameday discount insurance field must be numeric',
                'sameday_discount_return_rate.required_if' => 'The sameday discount return field must be required',
//            'sameday_discount_return_rate.numeric' => 'The sameday discount return field must be numeric',
                'sameday_discount_packaging_rate.required_if' => 'The sameday discount packaging field must be required',
//            'sameday_discount_packaging_rate.numeric' => 'The sameday discount packaging field must be numeric',
                'sameday_discount_title.required_with' => 'The sameday discount title field is required',
                'sameday_daterange.required_with' => 'The sameday discount date field is required',
                //sameday ends

                //warehouse starts
                'invoicing_cycle.*.required' => 'Invoicing Cycle field is required.',
                'invoicing_date.*.required' => 'Invoicing Cycle field is required.',
                'invoicing_cycle.*.numeric' => 'Invoicing Cycle field must be numeric.',
                'invoicing_date.*.numeric' => 'Invoicing Cycle field must be numeric.',
                'ppc_charges.required' => 'Per product charges field id required',
                'psf_charges.required' => 'Per square foot charges field id required',
                'storage_type.*.required_if' => 'Storage type field is required.',
                'storage_type_charges.*.required_if' => 'Storage type charges field is required',
                'storage_type.*.numeric' => 'Storage type field must be numeric.',
                'storage_type_charges.*.numeric' => 'Storage type charges field must be numeric',
                'packing_type.*.required' => 'Packing type field is required',
                'packing_charges.*.required' => 'Packing charges field is required',
                'packing_charges.*.numeric' => 'Packing charges field must be numeric',
                'labelling_charges.required' => 'Labelling charges field is required',
                'labelling_charges.numeric' => 'Labelling charges field must be numeric',

                'discount_on_destination.required_if' => 'Rush Destination Field is required if discount weight (destination-wise) toggle is on',
                'discount_ol_destination.required_if' => 'Saver Plus Destination Field is required if discount weight (destination-wise) toggle is on',
                'discount_d_destination.required_if' => 'Detain Destination Field is required if discount weight (destination-wise) toggle is on',
                'discount_sd_destination.required_if' => 'Same Day Destination Field is required if discount weight (destination-wise) toggle is on',

                'on_cod_discount_per' => 'Zero Cod Discount percentage is required',
                'on_return_discount_per' => 'Return Discount percentage is required',

                'ol_cod_discount_per' => 'Zero Cod Discount percentage is required',
                'ol_return_discount_per' => 'Return Discount percentage is required',

                'detain_cod_discount_per' => 'Zero Cod Discount percentage is required',
                'detain_return_discount_per' => 'Return Discount percentage is required',

                'sameday_cod_discount_per' => 'Zero Cod Discount percentage is required',
                'sameday_return_discount_per' => 'Return Discount percentage is required',

            ];

            $validations = array();
            $on_validations = array();
            $ol_validations = array();
            $detain_validations = array();
            $sameday_validations = array();
            $sms_validations = array();

            if($request->has('sms_main_switch') && $request->sms_main_switch == 'on') {
                $sms_validations = [
                    'sms_charges' => 'required|gt:0'
                ];
            }

            if ($request->has('on_main_switch') && $request->on_main_switch == 'on') {
                $on_validations = [
                    'on_wa_range_up.*' => 'required|numeric|between:0,100000',
                    'on_wa_range_down.*' => 'required|numeric|between:0,100000',
                    'on_wa_local_charges.*' => 'required|numeric',
                    'on_class_0_charges.*' => 'required|numeric',
                    'on_class_1_charges.*' => 'required',
                    'on_class_2_charges.*' => 'required',
                    'on_class_3_charges.*' => 'required',
                    'on_wa_spkg.*' => 'numeric',
                    'on_replacement_charges' => 'required|numeric',
                    'on_tnb_charges' => 'required|numeric',
                    'on_reverse_charges' => 'required|numeric',
                    'on_cash_range_up.*' => 'required_if:on_cash_handling_switch,==,on|numeric',
                    'on_cash_range_down.*' => 'required_if:on_cash_handling_switch,==,on|numeric',
                    'on_cash_charges.*' => 'required_if:on_cash_handling_switch,==,on',
                    'on_ins_range_up.*' => 'required_if:on_insurance_charges_switch,==,on|numeric',
                    'on_ins_range_down.*' => 'required_if:on_insurance_charges_switch,==,on|numeric',
                    'on_ins_charges.*' => 'required_if:on_insurance_charges_switch,==,on',
                    'on_return_local_charges.*' => 'required_if:on_return_switch,==,on|numeric',
                    'on_return_class_0_charges.*' => 'required_if:on_return_switch,==,on|numeric',
                    'on_return_class_1_charges.*' => 'required_if:on_return_switch,==,on|numeric',
                    'on_return_class_2_charges.*' => 'required_if:on_return_switch,==,on|numeric',
                    'on_return_class_3_charges.*' => 'required_if:on_return_switch,==,on|numeric',
                    'overnight_fuel_surcharge' => 'required_if:overnight_fuel_switch,==,on|numeric',

                    'on_discount_title' => 'required_with:on_discount_weight_rate,on_discount_cash_rate,on_discount_insurance_rate,on_discount_return_rate,on_discount_packaging_rate',
                    'on_daterange' => 'required_with:on_discount_weight_rate,on_discount_cash_rate,on_discount_insurance_rate,on_discount_return_rate,on_discount_packaging_rate',
                    'on_discount_weight_rate' => 'required_if:on_discount_weight_switch,==,on',
                    'on_discount_cash_rate' => 'required_if:on_discount_cash_switch,==,on',
                    'on_discount_insurance_rate' => 'required_if:on_discount_insurance_switch,==,on',
                    'on_discount_return_rate' => 'required_if:on_discount_return_switch,==,on',
                    'on_discount_packaging_rate' => 'required_if:on_discount_packaging_switch,==,on',

                    'discount_on_destination' => 'required_if:on_discount_destination_wise_weight_switch,==,on',

                    'on_cod_discount_per' => 'required_if:on_zero_cod_switch,==,on',
                    'on_return_discount_per' => 'required_if:on_return_discount_switch,==,on',
                ];
            }
            //overland
            if ($request->has('ol_main_switch') && $request->ol_main_switch == 'on') {
                $ol_validations = [
                    'ol_wa_range_up.*' => 'required|numeric|between:0,100000',
                    'ol_wa_range_down.*' => 'required|numeric|between:0,100000',
                    'ol_wa_local_charges.*' => 'required|numeric',
                    'ol_class_0_charges.*' => 'required|numeric',
                    'ol_class_1_charges.*' => 'required',
                    'ol_class_2_charges.*' => 'required',
                    'ol_class_3_charges.*' => 'required',
                    'ol_wa_spkg.*' => 'numeric',
                    'ol_replacement_charges' => 'required|numeric',
                    'ol_tnb_charges' => 'required|numeric',
                    'ol_reverse_charges' => 'required|numeric',
                    'ol_cash_range_up.*' => 'required_if:ol_cash_handling_switch,==,on|numeric',
                    'ol_cash_range_down.*' => 'required_if:ol_cash_handling_switch,==,on|numeric',
                    'ol_cash_charges.*' => 'required_if:ol_cash_handling_switch,==,on',
                    'ol_ins_range_up.*' => 'required_if:ol_insurance_charges_switch,==,on|numeric',
                    'ol_ins_range_down.*' => 'required_if:ol_insurance_charges_switch,==,on|numeric',
                    'ol_ins_charges.*' => 'required_if:ol_insurance_charges_switch,==,on',
                    'ol_return_local_charges.*' => 'required_if:ol_return_switch,==,on|numeric',
                    'ol_return_class_0_charges.*' => 'required_if:ol_return_switch,==,on|numeric',
                    'ol_return_class_1_charges.*' => 'required_if:ol_return_switch,==,on|numeric',
                    'ol_return_class_2_charges.*' => 'required_if:ol_return_switch,==,on|numeric',
                    'ol_return_class_3_charges.*' => 'required_if:ol_return_switch,==,on|numeric',
                    'overland_fuel_surcharge' => 'required_if:overland_fuel_switch,==,on|numeric',

                    'ol_discount_title' => 'required_with:ol_discount_weight_rate,ol_discount_cash_rate,ol_discount_insurance_rate,ol_discount_return_rate,ol_discount_packaging_rate',
                    'ol_daterange' => 'required_with:ol_discount_weight_rate,ol_discount_cash_rate,ol_discount_insurance_rate,ol_discount_return_rate,ol_discount_packaging_rate',
                    'ol_discount_weight_rate' => 'required_if:ol_discount_weight_switch,==,on',
                    'ol_discount_cash_rate' => 'required_if:ol_discount_cash_switch,==,on',
                    'ol_discount_insurance_rate' => 'required_if:ol_discount_insurance_switch,==,on',
                    'ol_discount_return_rate' => 'required_if:ol_discount_return_switch,==,on',
                    'ol_discount_packaging_rate' => 'required_if:ol_discount_packaging_switch,==,on',

                    'discount_ol_destination' => 'required_if:ol_discount_destination_wise_weight_switch,==,on',
                    'ol_cod_discount_per' => 'required_if:ol_zero_cod_switch,==,on',
                    'ol_return_discount_per' => 'required_if:ol_return_discount_switch,==,on',
                ];
            }
            //overland
            if ($request->has('detain_main_switch') && $request->detain_main_switch == 'on') {
                $detain_validations = [
                    'detain_wa_range_up.*' => 'required|numeric|between:0,100000',
                    'detain_wa_range_down.*' => 'required|numeric|between:0,100000',
                    'detain_wa_local_charges.*' => 'required|numeric',
                    'detain_class_0_charges.*' => 'required|numeric',
                    'detain_class_1_charges.*' => 'required',
                    'detain_class_2_charges.*' => 'required',
                    'detain_class_3_charges.*' => 'required',
                    'detain_wa_spkg.*' => 'numeric',
                    'detain_replacement_charges' => 'required|numeric',
                    'detain_tnb_charges' => 'required|numeric',
                    'detain_reverse_charges' => 'required|numeric',
                    'detain_cash_range_up.*' => 'required_if:detain_cash_handling_switch,==,on|numeric',
                    'detain_cash_range_down.*' => 'required_if:detain_cash_handling_switch,==,on|numeric',
                    'detain_cash_charges.*' => 'required_if:detain_cash_handling_switch,==,on',
                    'detain_ins_range_up.*' => 'required_if:detain_insurance_charges_switch,==,on|numeric',
                    'detain_ins_range_down.*' => 'required_if:detain_insurance_charges_switch,==,on|numeric',
                    'detain_ins_charges.*' => 'required_if:detain_insurance_charges_switch,==,on',
                    'detain_return_local_charges.*' => 'required_if:detain_return_switch,==,on|numeric',
                    'detain_return_class_0_charges.*' => 'required_if:detain_return_switch,==,on|numeric',
                    'detain_return_class_1_charges.*' => 'required_if:detain_return_switch,==,on|numeric',
                    'detain_return_class_2_charges.*' => 'required_if:detain_return_switch,==,on|numeric',
                    'detain_return_class_3_charges.*' => 'required_if:detain_return_switch,==,on|numeric',
                    'detain_fuel_surcharge' => 'required_if:detain_fuel_switch,==,on|numeric',

                    'detain_discount_title' => 'required_with:detain_discount_weight_rate,detain_discount_cash_rate,detain_discount_insurance_rate,detain_discount_return_rate,detain_discount_packaging_rate',
                    'detain_daterange' => 'required_with:detain_discount_weight_rate,detain_discount_cash_rate,detain_discount_insurance_rate,detain_discount_return_rate,detain_discount_packaging_rate',
                    'detain_discount_weight_rate' => 'required_if:detain_discount_weight_switch,==,on',
                    'detain_discount_cash_rate' => 'required_if:detain_discount_cash_switch,==,on',
                    'detain_discount_insurance_rate' => 'required_if:detain_discount_insurance_switch,==,on',
                    'detain_discount_return_rate' => 'required_if:detain_discount_return_switch,==,on',
                    'detain_discount_packaging_rate' => 'required_if:detain_discount_packaging_switch,==,on',

                    'discount_d_destination' => 'required_if:d_discount_destination_wise_weight_switch,==,on',
                    'detain_cod_discount_per' => 'required_if:detain_zero_cod_switch,==,on',
                    'detain_return_discount_per' => 'required_if:detain_return_discount_switch,==,on',
                ];
            }
            //sameday
            if ($request->has('sameday_main_switch') && $request->sameday_main_switch == 'on') {
                $sameday_validations = [
                    'sameday_wa_range_up.*' => 'required|numeric|between:0,100000',
                    'sameday_wa_range_down.*' => 'required|numeric|between:0,100000',
                    'sameday_wa_local_charges.*' => 'required|numeric',
                    'sameday_class_0_charges.*' => 'required|numeric',
                    'sameday_wa_spkg.*' => 'numeric',
                    'sameday_replacement_charges' => 'required|numeric',
                    'sameday_tnb_charges' => 'required|numeric',
                    'sameday_reverse_charges' => 'required|numeric',
                    'sameday_cash_range_up.*' => 'required_if:sameday_cash_handling_switch,==,on|numeric',
                    'sameday_cash_range_down.*' => 'required_if:sameday_cash_handling_switch,==,on|numeric',
                    'sameday_cash_charges.*' => 'required_if:sameday_cash_handling_switch,==,on',
                    'sameday_ins_range_up.*' => 'required_if:sameday_insurance_charges_switch,==,on|numeric',
                    'sameday_ins_range_down.*' => 'required_if:sameday_insurance_charges_switch,==,on|numeric',
                    'sameday_ins_charges.*' => 'required_if:sameday_insurance_charges_switch,==,on',
                    'sameday_return_local_charges.*' => 'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_return_class_0_charges.*' => 'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_return_class_1_charges.*' => 'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_return_class_2_charges.*' => 'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_return_class_3_charges.*' => 'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_fuel_surcharge' => 'required_if:sameday_fuel_switch,==,on|numeric',

                    'sameday_discount_title' => 'required_with:sameday_discount_weight_rate,sameday_discount_cash_rate,sameday_discount_insurance_rate,sameday_discount_return_rate,sameday_discount_packaging_rate',
                    'sameday_daterange' => 'required_with:sameday_discount_weight_rate,sameday_discount_cash_rate,sameday_discount_insurance_rate,sameday_discount_return_rate,sameday_discount_packaging_rate',
                    'sameday_discount_weight_rate' => 'required_if:sameday_discount_weight_switch,==,on',
                    'sameday_discount_cash_rate' => 'required_if:sameday_discount_cash_switch,==,on',
                    'sameday_discount_insurance_rate' => 'required_if:sameday_discount_insurance_switch,==,on',
                    'sameday_discount_return_rate' => 'required_if:sameday_discount_return_switch,==,on',
                    'sameday_discount_packaging_rate' => 'required_if:sameday_discount_packaging_switch,==,on',

                    'discount_sd_destination' => 'required_if:sd_discount_destination_wise_weight_switch,==,on',
                    'sameday_cod_discount_per' => 'required_if:sameday_zero_cod_switch,==,on',
                    'sameday_return_discount_per' => 'required_if:sameday_return_discount_switch,==,on',
                ];
            }

            if ($request->has('warehouse_main_switch') && $request->warehouse_main_switch == 'on') {
                $warehouse_validations = [
                    'invoicing_cycle' => 'required|numeric',
                    'invoicing_date.*' => 'required_if:invoicing_cycle,1,3',
                    'ppc_charges' => 'required_if:ppc_switch,==,on',
                    'psf_charges' => 'required_if:psf_switch,==,on',
                    'storage_type.*' => 'required_if:storage_charges_switch,==,on',
                    'storage_type_charges.*' => 'required_if:storage_charges_switch,==,on|numeric',
                    'packing_type.*' => 'required_if:packing_charges_switch,==,on',
                    'packing_charges.*' => 'required_if:packing_charges_switch,==,on|numeric',
                    'labelling_charges.*' => 'required_if:labelling_charges_switch,==,on|numeric',
                ];
            }

            $validations = array_merge($on_validations, $ol_validations, $detain_validations, $sameday_validations,$sms_validations);

            $validate = Validator::make($request->all(), $validations, $messages);
            if ($validate->fails()) {
                return redirect()->back()
                    ->withErrors($validate)
                    ->withInput();
            }
            // if($request->has('sms_main_switch') && $request->sms_main_switch == 'on') {
            //     User::where('id', $id)->update([
            //         'sms_charges_type_id' => $request->smsPostType,
            //         'sms_charges' => $request->sms_charges,
            //         'sms_charges_status' => ($request->has('sms_main_switch')) ? 1 : 0,
            //     ]);
            // }

            if ($request->has('on_default') && $request->on_default == 'on') {
                $default_shipping_mode = User::where('id', $id)->update([
                    'default_shipping_mode' => 1
                ]);
            }
            if ($request->has('ol_default') && $request->ol_default == 'on') {
                $default_shipping_mode = User::where('id', $id)->update([
                    'default_shipping_mode' => 2
                ]);
            }
            if ($request->has('det_default') && $request->det_default == 'on') {
                $default_shipping_mode = User::where('id', $id)->update([
                    'default_shipping_mode' => 3
                ]);
            }
            if ($request->has('sameday_default') && $request->sameday_default == 'on') {
                $default_shipping_mode = User::where('id', $id)->update([
                    'default_shipping_mode' => 4
                ]);
            }

            PendingSmsCharges::where('user_id', $id)->delete();
            PendingRateStatus::where('user_id', $id)->delete();
            PendingWeightCharge::where('user_id', $id)->delete();
            PendingBookingTypeCharges::where('user_id', $id)->delete();
            PendingCashHandlingCharge::where('user_id', $id)->delete();
            PendingInsuranceCharge::where('user_id', $id)->delete();
            PendingReturnCharge::where('user_id', $id)->delete();
            PendingFuelSurcharge::where('user_id', $id)->delete();
            PendingPackagingCharge::where('user_id', $id)->delete();
            PendingDiscountCharge::where('user_id', $id)->delete();
            PendingDiscountWeightCharge::where('user_id', $id)->delete();
            PendingRateOriginHub::where('user_id', $id)->delete();
            PendingRateDestinationHub::where('user_id', $id)->delete();
            self::discounted_cod_and_return($request,$id,1,'default','delete');

            if($request->has('sms_main_switch') && $request->sms_main_switch == 'on') {
                $smsRate = PendingSmsCharges::where('user_id' , $id)->get();
                if ($smsRate->isEmpty()) {
                    PendingSmsCharges::create([
                        'user_id' => $id,
                        'sms_charges' => $request->sms_charges,
                        'sms_charges_status' => ($request->has('sms_main_switch')) ? 1 : 0,
                    ]);
                }

            }
            if ($request->has('on_main_switch') && $request->on_main_switch == 'on') {
                $ONRateAlready = PendingRateStatus::where('user_id', $id)->where('shipping_mode_id', 1)->get();
                if ($ONRateAlready->isEmpty()) {

                    PendingRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'status' => ($request->has('on_main_switch')) ? 1 : 0,
                        'cash_handling_charges' => ($request->has('on_cash_handling_switch')) ? 1 : 0,
                        'insurance_charges' => ($request->has('on_insurance_charges_switch')) ? 1 : 0,
                        'return_charges' => ($request->has('on_return_switch')) ? 1 : 0,
                        'fuel_charges' => ($request->has('overnight_fuel_switch')) ? 1 : 0,
                        'zero_cod_discount' => ($request->has('on_zero_cod_switch')) ? 1 : 0,
                        'return_discount' => ($request->has('on_return_discount_switch')) ? 1 : 0,

                    ]);


                    if ($request->has('on_origin_hubs')) {
                        foreach ($request->on_origin_hubs as $origin_id) {
                            $rate_origin_hub = new PendingRateOriginHub();
                            $rate_origin_hub->user_id = $id;
                            $rate_origin_hub->shipping_mode_id = 1;
                            $rate_origin_hub->city_id = $origin_id;
                            $rate_origin_hub->save();
                        }
                    }

                    if ($request->has('on_destination_hubs')) {
                        foreach ($request->on_destination_hubs as $destination_id) {
                            $rate_destination_hub = new PendingRateDestinationHub();
                            $rate_destination_hub->user_id = $id;
                            $rate_destination_hub->shipping_mode_id = 1;
                            $rate_destination_hub->city_id = $destination_id;
                            $rate_destination_hub->save();
                        }
                    }
                    $wa_switch = array();
                    $wa_spkg = array();
                    foreach ($request->on_wa_range_up as $index => $on_wa_range_up) {
                        if ($request->has('on_wa_switch')) {
                            if (array_key_exists($index, $request->on_wa_switch)) {
                                $wa_switch[$index] = 1;
                            } else {
                                $wa_switch[$index] = 0;
                            };
                        } else {
                            $wa_switch[$index] = 0;
                        }
                        if ($request->has('on_wa_spkg')) {
                            if (array_key_exists($index, $request->on_wa_spkg)) {
                                $wa_spkg[$index] = $request->on_wa_spkg[$index];
                            } else {
                                $wa_spkg[$index] = 0;
                            };
                        } else {
                            $wa_spkg[$index] = 0;
                        }
                        PendingWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'range_up' => $request->on_wa_range_up[$index],
                            'range_down' => $request->on_wa_range_down[$index],
                            'weight_addition' => $wa_switch[$index],
                            'spkg' => $wa_spkg[$index],
                            'local_or_6hr' => $request->on_wa_local_charges[$index],
                            'national_charges_class_0' => $request->on_class_0_charges[$index],
                            'national_charges_class_1' => $request->on_class_1_charges[$index],
                            'national_charges_class_2' => $request->on_class_2_charges[$index],
                            'national_charges_class_3' => $request->on_class_3_charges[$index]
                        ]);

                    }

                    //Replacement and Try and Buy charges
                    PendingBookingTypeCharges::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'replacement_charges' => $request->on_replacement_charges,
                        'try_and_buy_charges' => $request->on_tnb_charges,
                        'reverse_pickup_charges' => $request->on_reverse_charges
                    ]);
                    //Cash handling Charges
                    if ($request->has('on_cash_handling_switch') && $request->on_cash_handling_switch == 'on') {
                        foreach ($request->on_cash_range_up as $ind => $on_cash_range_up) {
                            PendingCashHandlingCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'range_up' => $request->on_cash_range_up[$ind],
                                'range_down' => $request->on_cash_range_down[$ind],
                                'charges' => $request->on_cash_charges[$ind]
                            ]);
                        }
                    }
                    //insurance charges
                    if ($request->has('on_insurance_charges_switch') && $request->on_insurance_charges_switch == 'on') {
                        foreach ($request->on_ins_range_up as $insurance => $on_ins_range_up) {
                            PendingInsuranceCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'range_up' => $request->on_ins_range_up[$insurance],
                                'range_down' => $request->on_ins_range_down[$insurance],
                                'charges' => $request->on_ins_charges[$insurance]
                            ]);
                        }
                    }
                    //Return Charges
                    if ($request->has('on_return_switch') && $request->on_return_switch == 'on') {
                        PendingReturnCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'local' => $request->on_return_local_charges,
                            'national_charges_class_0' => $request->on_return_class_0_charges,
                            'national_charges_class_1' => $request->on_return_class_1_charges,
                            'national_charges_class_2' => $request->on_return_class_2_charges,
                            'national_charges_class_3' => $request->on_return_class_3_charges
                        ]);
                    }
                    //Return Charges
                    if ($request->has('overnight_fuel_switch') && $request->overnight_fuel_switch == 'on') {
                        PendingFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'fuel_surcharge' => $request->overnight_fuel_surcharge
                        ]);
                    }

                    $discount_cash = null;
                    $discount_weight = null;
                    $discount_insurance = null;
                    $discount_return = null;
                    $discount_packaging = null;

                    if ($request->has('on_discount_weight_switch') && $request->on_discount_weight_switch == 'on') {
                        $discount_weight = $request->on_discount_weight_rate != null ? $request->on_discount_weight_rate : null;
//                    $discount_weight = $request->on_discount_weight_rate;
                    }
                    if ($request->has('on_discount_cash_switch') && $request->on_discount_cash_switch == 'on') {
                        $discount_cash = $request->on_discount_cash_rate != null ? $request->on_discount_cash_rate : null;
                    }
                    if ($request->has('on_discount_insurance_switch') && $request->on_discount_insurance_switch == 'on') {
                        $discount_insurance = $request->on_discount_insurance_rate != null ? $request->on_discount_insurance_rate : null;
                    }
                    if ($request->has('on_discount_return_switch') && $request->on_discount_return_switch == 'on') {
                        $discount_return = $request->on_discount_return_rate != null ? $request->on_discount_insurance_rate : null;
                    }
                    if ($request->has('on_discount_packaging_switch') && $request->on_discount_packaging_switch == 'on') {
                        $discount_packaging = $request->on_discount_packaging_rate != null ? $request->on_discount_packaging_rate : null;
                    }
                    if ($discount_weight != null || $discount_cash != null || $discount_insurance != null || $discount_return != null || $discount_packaging != null) {


                        $date_str = $request->on_daterange;
                        $date_sep = explode(' - ', $date_str);
                        $date_to = explode('/', $date_sep[0]);
                        $date_from = explode('/', $date_sep[1]);
                        $to = Carbon::create($date_to[2], $date_to[0], $date_to[1], 0, 0, 0, 'UTC')->toDateTimeString();
                        $from = Carbon::create($date_from[2], $date_from[0], $date_from[1], 0, 0, 0, 'UTC')->toDateTimeString();


                        PendingDiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'title' => $request->on_discount_title,
                            'weight' => $discount_weight,
                            'cash' => $discount_cash,
                            'insurance' => $discount_insurance,
                            'return' => $discount_return,
                            'packaging' => $discount_packaging,
                            'to' => $to,
                            'from' => $from,
                            'added_by' => Auth::id()
                        ]);
                    }

                    if($request->has('on_discount_destination_wise_weight_switch') && $request->on_discount_destination_wise_weight_switch == "on")
                    {
                        foreach($request->discount_on_destination as $parent_key => $destination)
                        {
                            $discount_wa_switch = array();
                            $discount_wa_spkg = array();
                            foreach($request->discount_on_wa_range_up[$parent_key] as $child_key => $value)
                            {
                                if ($request->has('discount_on_wa_switch.'.$parent_key)) {
                                    if (array_key_exists($child_key, $request->discount_on_wa_switch[$parent_key])) {
                                        $discount_wa_switch[$child_key] = 1;
                                    } else {
                                        $discount_wa_switch[$child_key] = 0;
                                    };
                                } else {
                                    $discount_wa_switch[$child_key] = 0;
                                }
                                if ($request->has('discount_on_wa_spkg.'.$parent_key)) {
                                    if (array_key_exists($child_key, $request->discount_on_wa_spkg[$parent_key])) {
                                        $discount_wa_spkg[$child_key] = $request->discount_on_wa_spkg[$parent_key][$child_key];
                                    } else {
                                        $discount_wa_spkg[$child_key] = 0.5;
                                    };
                                } else {
                                    $discount_wa_spkg[$child_key] = 0.5;
                                }
                                PendingDiscountWeightCharge::create([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 1,
                                    'destination_id' => $destination,
                                    'range_up' => $request->discount_on_wa_range_up[$parent_key][$child_key],
                                    'range_down' => $request->discount_on_wa_range_down[$parent_key][$child_key],
                                    'weight_addition' => $discount_wa_switch[$child_key],
                                    'spkg' => $discount_wa_spkg[$child_key],
                                    'local_or_6hr' => $request->discount_on_wa_local_charges[$parent_key][$child_key],
                                ]);
                            }
                        }
                    }

                }
                if ($request->has('on_dws_weight')) {
                    if ($request->on_dws_weight == 2) {
                        DwsWeightChargesController::edit($id, 1, 2, Auth::id());

                    } else {
                        DwsWeightChargesController::edit($id, 1, 1, Auth::id());
                    }

                } else {
                    DwsWeightChargesController::delete_dws_rate($id, 1);
                }
            }
            //Overland
            if ($request->has('ol_main_switch') && $request->ol_main_switch == 'on') {

                $OLRatePresent = PendingRateStatus::where('user_id', $id)->where('shipping_mode_id', 2)->get();

                if ($OLRatePresent->isEmpty()) {
                    PendingRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'status' => ($request->has('ol_main_switch')) ? 1 : 0,
                        'cash_handling_charges' => ($request->has('ol_cash_handling_switch')) ? 1 : 0,
                        'insurance_charges' => ($request->has('ol_insurance_charges_switch')) ? 1 : 0,
                        'return_charges' => ($request->has('ol_return_switch')) ? 1 : 0,
                        'fuel_charges' => ($request->has('overland_fuel_switch')) ? 1 : 0,
                        'zero_cod_discount' => ($request->has('ol_zero_cod_switch')) ? 1 : 0,
                        'return_discount' => ($request->has('ol_return_discount_switch')) ? 1 : 0,

                    ]);

                    if ($request->has('ol_origin_hubs')) {
                        foreach ($request->ol_origin_hubs as $origin_id) {
                            $rate_origin_hub = new PendingRateOriginHub();
                            $rate_origin_hub->user_id = $id;
                            $rate_origin_hub->shipping_mode_id = 2;
                            $rate_origin_hub->city_id = $origin_id;
                            $rate_origin_hub->save();
                        }
                    }

                    if ($request->has('ol_destination_hubs')) {
                        foreach ($request->ol_destination_hubs as $destination_id) {
                            $rate_destination_hub = new PendingRateDestinationHub();
                            $rate_destination_hub->user_id = $id;
                            $rate_destination_hub->shipping_mode_id = 2;
                            $rate_destination_hub->city_id = $destination_id;
                            $rate_destination_hub->save();
                        }
                    }
                    $wa_switch_overland = array();
                    $wa_spkg_overland = array();
                    foreach ($request->ol_wa_range_up as $index => $ol_wa_range_up) {
                        if ($request->has('ol_wa_switch')) {
                            if (array_key_exists($index, $request->ol_wa_switch)) {
                                $wa_switch_overland[$index] = 1;
                            } else {
                                $wa_switch_overland[$index] = 0;
                            };
                        } else {
                            $wa_switch_overland[$index] = 0;
                        }
                        if ($request->has('ol_wa_spkg')) {
                            if (array_key_exists($index, $request->ol_wa_spkg)) {
                                $wa_spkg_overland[$index] = $request->ol_wa_spkg[$index];
                            } else {
                                $wa_spkg_overland[$index] = 0;
                            };
                        } else {
                            $wa_spkg_overland[$index] = 0;
                        }
                        PendingWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'range_up' => $request->ol_wa_range_up[$index],
                            'range_down' => $request->ol_wa_range_down[$index],
                            'weight_addition' => $wa_switch_overland[$index],
                            'spkg' => $wa_spkg_overland[$index],
                            'local_or_6hr' => $request->ol_wa_local_charges[$index],
                            'national_charges_class_0' => $request->ol_class_0_charges[$index],
                            'national_charges_class_1' => $request->ol_class_1_charges[$index],
                            'national_charges_class_2' => $request->ol_class_2_charges[$index],
                            'national_charges_class_3' => $request->ol_class_3_charges[$index]
                        ]);
                    }


                    //Replacement and Try and Buy charges
                    PendingBookingTypeCharges::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'replacement_charges' => $request->ol_replacement_charges,
                        'try_and_buy_charges' => $request->ol_tnb_charges,
                        'reverse_pickup_charges' => $request->ol_reverse_charges

                    ]);
                    //Cash handling Charges
                    if ($request->has('ol_cash_handling_switch') && $request->ol_cash_handling_switch == 'on') {
                        foreach ($request->ol_cash_range_up as $ind => $ol_cash_range_up) {
                            PendingCashHandlingCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'range_up' => $request->ol_cash_range_up[$ind],
                                'range_down' => $request->ol_cash_range_down[$ind],
                                'charges' => $request->ol_cash_charges[$ind]
                            ]);
                        }
                    }
                    //insurance charges
                    if ($request->has('ol_insurance_charges_switch') && $request->ol_insurance_charges_switch == 'on') {
                        foreach ($request->ol_ins_range_up as $insurance => $ol_ins_range_up) {
                            PendingInsuranceCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'range_up' => $request->ol_ins_range_up[$insurance],
                                'range_down' => $request->ol_ins_range_down[$insurance],
                                'charges' => $request->ol_ins_charges[$insurance]
                            ]);
                        }
                    }
                    //Return Charges
                    if ($request->has('ol_return_switch') && $request->ol_return_switch == 'on') {
                        PendingReturnCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'local' => $request->ol_return_local_charges,
                            'national_charges_class_0' => $request->ol_return_class_0_charges,
                            'national_charges_class_1' => $request->ol_return_class_1_charges,
                            'national_charges_class_2' => $request->ol_return_class_2_charges,
                            'national_charges_class_3' => $request->ol_return_class_3_charges
                        ]);
                    }
                    if ($request->has('overland_fuel_switch') && $request->overland_fuel_switch == 'on') {
                        PendingFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'fuel_surcharge' => $request->overland_fuel_surcharge
                        ]);
                    }

                    $discount_cash = null;
                    $discount_weight = null;
                    $discount_insurance = null;
                    $discount_return = null;
                    $discount_packaging = null;

                    if ($request->has('ol_discount_weight_switch') && $request->ol_discount_weight_switch == 'on') {
                        $discount_weight = $request->ol_discount_weight_rate != null ? $request->ol_discount_weight_rate : null;
//                    $discount_weight = $request->ol_discount_weight_rate;
                    }
                    if ($request->has('ol_discount_cash_switch') && $request->ol_discount_cash_switch == 'on') {
                        $discount_cash = $request->ol_discount_cash_rate != null ? $request->ol_discount_cash_rate : null;
                    }
                    if ($request->has('ol_discount_insurance_switch') && $request->ol_discount_insurance_switch == 'on') {
                        $discount_insurance = $request->ol_discount_insurance_rate != null ? $request->ol_discount_insurance_rate : null;
                    }
                    if ($request->has('ol_discount_return_switch') && $request->ol_discount_return_switch == 'on') {
                        $discount_return = $request->ol_discount_return_rate != null ? $request->ol_discount_insurance_rate : null;
                    }
                    if ($request->has('ol_discount_packaging_switch') && $request->ol_discount_packaging_switch == 'on') {
                        $discount_packaging = $request->ol_discount_packaging_rate != null ? $request->ol_discount_packaging_rate : null;
                    }
                    if ($discount_weight != null || $discount_cash != null || $discount_insurance != null || $discount_return != null || $discount_packaging != null) {


                        $date_str = $request->ol_daterange;
                        $date_sep = explode(' - ', $date_str);
                        $date_to = explode('/', $date_sep[0]);
                        $date_from = explode('/', $date_sep[1]);
                        $to = Carbon::create($date_to[2], $date_to[0], $date_to[1], 0, 0, 0, 'UTC')->toDateTimeString();
                        $from = Carbon::create($date_from[2], $date_from[0], $date_from[1], 0, 0, 0, 'UTC')->toDateTimeString();


                        PendingDiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'title' => $request->ol_discount_title,
                            'weight' => $discount_weight,
                            'cash' => $discount_cash,
                            'insurance' => $discount_insurance,
                            'return' => $discount_return,
                            'packaging' => $discount_packaging,
                            'to' => $to,
                            'from' => $from,
                            'added_by' => Auth::id()
                        ]);
                    }

                    if($request->has('ol_discount_destination_wise_weight_switch') && $request->ol_discount_destination_wise_weight_switch == "on")
                    {
                        foreach($request->discount_ol_destination as $parent_key => $destination)
                        {
                            $discount_wa_switch = array();
                            $discount_wa_spkg = array();
                            foreach($request->discount_ol_wa_range_up[$parent_key] as $child_key => $value)
                            {
                                if ($request->has('discount_ol_wa_switch.'.$parent_key)) {
                                    if (array_key_exists($child_key, $request->discount_ol_wa_switch[$parent_key])) {
                                        $discount_wa_switch[$child_key] = 1;
                                    } else {
                                        $discount_wa_switch[$child_key] = 0;
                                    };
                                } else {
                                    $discount_wa_switch[$child_key] = 0;
                                }
                                if ($request->has('discount_ol_wa_spkg.'.$parent_key)) {
                                    if (array_key_exists($child_key, $request->discount_ol_wa_spkg[$parent_key])) {
                                        $discount_wa_spkg[$child_key] = $request->discount_ol_wa_spkg[$parent_key][$child_key];
                                    } else {
                                        $discount_wa_spkg[$child_key] = 0.5;
                                    };
                                } else {
                                    $discount_wa_spkg[$child_key] = 0.5;
                                }
                                PendingDiscountWeightCharge::create([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 2,
                                    'destination_id' => $destination,
                                    'range_up' => $request->discount_ol_wa_range_up[$parent_key][$child_key],
                                    'range_down' => $request->discount_ol_wa_range_down[$parent_key][$child_key],
                                    'weight_addition' => $discount_wa_switch[$child_key],
                                    'spkg' => $discount_wa_spkg[$child_key],
                                    'local_or_6hr' => $request->discount_ol_wa_local_charges[$parent_key][$child_key],
                                ]);
                            }
                        }
                    }

                }
                if ($request->has('ol_dws_weight')) {
                    if ($request->ol_dws_weight == 2) {
                        DwsWeightChargesController::edit($id, 2, 2, Auth::id());
                    } else {
                        DwsWeightChargesController::edit($id, 2, 1, Auth::id());
                    }

                } else {
                    DwsWeightChargesController::delete_dws_rate($id, 2);
                }

            }
            //Detain
            if ($request->has('detain_main_switch') && $request->detain_main_switch == 'on') {

                $DetainRatePresent = PendingRateStatus::where('user_id', $id)->where('shipping_mode_id', 3)->get();

                if ($DetainRatePresent->isEmpty()) {
                    PendingRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'status' => ($request->has('detain_main_switch')) ? 1 : 0,
                        'cash_handling_charges' => ($request->has('detain_cash_handling_switch')) ? 1 : 0,
                        'insurance_charges' => ($request->has('detain_insurance_charges_switch')) ? 1 : 0,
                        'return_charges' => ($request->has('detain_return_switch')) ? 1 : 0,
                        'fuel_charges' => ($request->has('detain_fuel_switch')) ? 1 : 0,
                        'zero_cod_discount' => ($request->has('detain_zero_cod_switch')) ? 1 : 0,
                        'return_discount' => ($request->has('detain_return_discount_switch')) ? 1 : 0,

                    ]);

                    if ($request->has('detain_origin_hubs')) {
                        foreach ($request->detain_origin_hubs as $origin_id) {
                            $rate_origin_hub = new PendingRateOriginHub();
                            $rate_origin_hub->user_id = $id;
                            $rate_origin_hub->shipping_mode_id = 3;
                            $rate_origin_hub->city_id = $origin_id;
                            $rate_origin_hub->save();
                        }
                    }

                    if ($request->has('detain_destination_hubs')) {
                        foreach ($request->detain_destination_hubs as $destination_id) {
                            $rate_destination_hub = new PendingRateDestinationHub();
                            $rate_destination_hub->user_id = $id;
                            $rate_destination_hub->shipping_mode_id = 3;
                            $rate_destination_hub->city_id = $destination_id;
                            $rate_destination_hub->save();
                        }
                    }
                    $wa_switch_detain = array();
                    $wa_spkg_detain = array();
                    foreach ($request->detain_wa_range_up as $index => $detain_wa_range_up) {
                        if ($request->has('detain_wa_switch')) {
                            if (array_key_exists($index, $request->detain_wa_switch)) {
                                $wa_switch_detain[$index] = 1;
                            } else {
                                $wa_switch_detain[$index] = 0;
                            };
                        } else {
                            $wa_switch_detain[$index] = 0;
                        }
                        if ($request->has('detain_wa_spkg')) {
                            if (array_key_exists($index, $request->detain_wa_spkg)) {
                                $wa_spkg_detain[$index] = $request->detain_wa_spkg[$index];
                            } else {
                                $wa_spkg_detain[$index] = 0;
                            };
                        } else {
                            $wa_spkg_detain[$index] = 0;
                        }
                        PendingWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'range_up' => $request->detain_wa_range_up[$index],
                            'range_down' => $request->detain_wa_range_down[$index],
                            'weight_addition' => $wa_switch_detain[$index],
                            'spkg' => $wa_spkg_detain[$index],
                            'local_or_6hr' => $request->detain_wa_local_charges[$index],
                            'national_charges_class_0' => $request->detain_class_0_charges[$index],
                            'national_charges_class_1' => $request->detain_class_1_charges[$index],
                            'national_charges_class_2' => $request->detain_class_2_charges[$index],
                            'national_charges_class_3' => $request->detain_class_3_charges[$index]
                        ]);
                    }


                    //Replacement and Try and Buy charges
                    PendingBookingTypeCharges::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'replacement_charges' => $request->detain_replacement_charges,
                        'try_and_buy_charges' => $request->detain_tnb_charges,
                        'reverse_pickup_charges' => $request->detain_reverse_charges

                    ]);
                    //Cash handling Charges
                    if ($request->has('detain_cash_handling_switch') && $request->detain_cash_handling_switch == 'on') {
                        foreach ($request->detain_cash_range_up as $ind => $detain_cash_range_up) {
                            PendingCashHandlingCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'range_up' => $request->detain_cash_range_up[$ind],
                                'range_down' => $request->detain_cash_range_down[$ind],
                                'charges' => $request->detain_cash_charges[$ind]
                            ]);
                        }
                    }
                    //insurance charges
                    if ($request->has('detain_insurance_charges_switch') && $request->detain_insurance_charges_switch == 'on') {
                        foreach ($request->detain_ins_range_up as $insurance => $detain_ins_range_up) {
                            PendingInsuranceCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'range_up' => $request->detain_ins_range_up[$insurance],
                                'range_down' => $request->detain_ins_range_down[$insurance],
                                'charges' => $request->detain_ins_charges[$insurance]
                            ]);
                        }
                    }
                    //Return Charges
                    if ($request->has('detain_return_switch') && $request->detain_return_switch == 'on') {
                        PendingReturnCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'local' => $request->detain_return_local_charges,
                            'national_charges_class_0' => $request->detain_return_class_0_charges,
                            'national_charges_class_1' => $request->detain_return_class_1_charges,
                            'national_charges_class_2' => $request->detain_return_class_2_charges,
                            'national_charges_class_3' => $request->detain_return_class_3_charges
                        ]);
                    }
                    if ($request->has('detain_fuel_switch') && $request->detain_fuel_switch == 'on') {
                        PendingFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'fuel_surcharge' => $request->detain_fuel_surcharge
                        ]);
                    }

                    $discount_cash = null;
                    $discount_weight = null;
                    $discount_insurance = null;
                    $discount_return = null;
                    $discount_packaging = null;

                    if ($request->has('detain_discount_weight_switch') && $request->detain_discount_weight_switch == 'on') {
                        $discount_weight = $request->detain_discount_weight_rate != null ? $request->detain_discount_weight_rate : null;
//                    $discount_weight = $request->detain_discount_weight_rate;
                    }
                    if ($request->has('detain_discount_cash_switch') && $request->detain_discount_cash_switch == 'on') {
                        $discount_cash = $request->detain_discount_cash_rate != null ? $request->detain_discount_cash_rate : null;
                    }
                    if ($request->has('detain_discount_insurance_switch') && $request->detain_discount_insurance_switch == 'on') {
                        $discount_insurance = $request->detain_discount_insurance_rate != null ? $request->detain_discount_insurance_rate : null;
                    }
                    if ($request->has('detain_discount_return_switch') && $request->detain_discount_return_switch == 'on') {
                        $discount_return = $request->detain_discount_return_rate != null ? $request->detain_discount_insurance_rate : null;
                    }
                    if ($request->has('detain_discount_packaging_switch') && $request->detain_discount_packaging_switch == 'on') {
                        $discount_packaging = $request->detain_discount_packaging_rate != null ? $request->detain_discount_packaging_rate : null;
                    }
                    if ($discount_weight != null || $discount_cash != null || $discount_insurance != null || $discount_return != null || $discount_packaging != null) {


                        $date_str = $request->detain_daterange;
                        $date_sep = explode(' - ', $date_str);
                        $date_to = explode('/', $date_sep[0]);
                        $date_from = explode('/', $date_sep[1]);
                        $to = Carbon::create($date_to[2], $date_to[0], $date_to[1], 0, 0, 0, 'UTC')->toDateTimeString();
                        $from = Carbon::create($date_from[2], $date_from[0], $date_from[1], 0, 0, 0, 'UTC')->toDateTimeString();


                        PendingDiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'title' => $request->detain_discount_title,
                            'weight' => $discount_weight,
                            'cash' => $discount_cash,
                            'insurance' => $discount_insurance,
                            'return' => $discount_return,
                            'packaging' => $discount_packaging,
                            'to' => $to,
                            'from' => $from,
                            'added_by' => Auth::id()
                        ]);
                    }

                    if($request->has('d_discount_destination_wise_weight_switch') && $request->d_discount_destination_wise_weight_switch == "on")
                    {
                        foreach($request->discount_d_destination as $parent_key => $destination)
                        {
                            $discount_wa_switch = array();
                            $discount_wa_spkg = array();
                            foreach($request->discount_d_wa_range_up[$parent_key] as $child_key => $value)
                            {
                                if ($request->has('discount_d_wa_switch.'.$parent_key)) {
                                    if (array_key_exists($child_key, $request->discount_d_wa_switch[$parent_key])) {
                                        $discount_wa_switch[$child_key] = 1;
                                    } else {
                                        $discount_wa_switch[$child_key] = 0;
                                    };
                                } else {
                                    $discount_wa_switch[$child_key] = 0;
                                }
                                if ($request->has('discount_d_wa_spkg.'.$parent_key)) {
                                    if (array_key_exists($child_key, $request->discount_d_wa_spkg[$parent_key])) {
                                        $discount_wa_spkg[$child_key] = $request->discount_d_wa_spkg[$parent_key][$child_key];
                                    } else {
                                        $discount_wa_spkg[$child_key] = 0.5;
                                    };
                                } else {
                                    $discount_wa_spkg[$child_key] = 0.5;
                                }
                                PendingDiscountWeightCharge::create([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 3,
                                    'destination_id' => $destination,
                                    'range_up' => $request->discount_d_wa_range_up[$parent_key][$child_key],
                                    'range_down' => $request->discount_d_wa_range_down[$parent_key][$child_key],
                                    'weight_addition' => $discount_wa_switch[$child_key],
                                    'spkg' => $discount_wa_spkg[$child_key],
                                    'local_or_6hr' => $request->discount_d_wa_local_charges[$parent_key][$child_key],
                                ]);
                            }
                        }
                    }

                }
                if ($request->has('detain_dws_weight')) {
                    if ($request->detain_dws_weight == 2) {
                        DwsWeightChargesController::edit($id, 3, 2, Auth::id());

                    } else {
                        DwsWeightChargesController::edit($id, 3, 1, Auth::id());


                    }

                } else {
                    DwsWeightChargesController::delete_dws_rate($id, 3);
                }
            }
            //Sameday
            if ($request->has('sameday_main_switch') && $request->sameday_main_switch == 'on') {

                $SamedayRatePresent = PendingRateStatus::where('user_id', $id)->where('shipping_mode_id', 4)->get();
                if ($SamedayRatePresent->isEmpty()) {
                    PendingRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'status' => ($request->has('sameday_main_switch')) ? 1 : 0,
                        'cash_handling_charges' => ($request->has('sameday_cash_handling_switch')) ? 1 : 0,
                        'insurance_charges' => ($request->has('sameday_insurance_charges_switch')) ? 1 : 0,
                        'return_charges' => ($request->has('sameday_return_switch')) ? 1 : 0,
                        'fuel_charges' => ($request->has('sameday_fuel_switch')) ? 1 : 0,
                        'zero_cod_discount' => ($request->has('sameday_zero_cod_switch')) ? 1 : 0,
                        'return_discount' => ($request->has('sameday_return_discount_switch')) ? 1 : 0,
                    ]);

                    if ($request->has('sameday_origin_hubs')) {
                        foreach ($request->sameday_origin_hubs as $origin_id) {
                            $rate_origin_hub = new PendingRateOriginHub();
                            $rate_origin_hub->user_id = $id;
                            $rate_origin_hub->shipping_mode_id = 4;
                            $rate_origin_hub->city_id = $origin_id;
                            $rate_origin_hub->save();
                        }
                    }


                    if ($request->has('sameday_destination_hubs')) {
                        foreach ($request->sameday_destination_hubs as $destination_id) {
                            $rate_destination_hub = new PendingRateDestinationHub();
                            $rate_destination_hub->user_id = $id;
                            $rate_destination_hub->shipping_mode_id = 4;
                            $rate_destination_hub->city_id = $destination_id;
                            $rate_destination_hub->save();
                        }
                    }
                    $wa_switch_sameday = array();
                    $wa_spkg_sameday = array();
                    foreach ($request->sameday_wa_range_up as $index => $sameday_wa_range_up) {
                        if ($request->has('sameday_wa_switch')) {
                            if (array_key_exists($index, $request->sameday_wa_switch)) {
                                $wa_switch_sameday[$index] = 1;
                            } else {
                                $wa_switch_sameday[$index] = 0;
                            };
                        } else {
                            $wa_switch_sameday[$index] = 0;
                        }
                        if ($request->has('sameday_wa_spkg')) {
                            if (array_key_exists($index, $request->sameday_wa_spkg)) {
                                $wa_spkg_sameday[$index] = $request->sameday_wa_spkg[$index];
                            } else {
                                $wa_spkg_sameday[$index] = 0;
                            };
                        } else {
                            $wa_spkg_sameday[$index] = 0;
                        }
                        PendingWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'range_up' => $request->sameday_wa_range_up[$index],
                            'range_down' => $request->sameday_wa_range_down[$index],
                            'weight_addition' => $wa_switch_sameday[$index],
                            'spkg' => $wa_spkg_sameday[$index],
                            'local_or_6hr' => $request->sameday_wa_local_charges[$index],
                            'national_charges_class_0' => $request->sameday_class_0_charges[$index],
                            'national_charges_class_1' => 0,
                            'national_charges_class_2' => 0,
                            'national_charges_class_3' => 0
                        ]);
                    }


                    //Replacement and Try and Buy charges
                    PendingBookingTypeCharges::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'replacement_charges' => $request->sameday_replacement_charges,
                        'try_and_buy_charges' => $request->sameday_tnb_charges,
                        'reverse_pickup_charges' => $request->sameday_reverse_charges

                    ]);
                    //Cash handling Charges
                    if ($request->has('sameday_cash_handling_switch') && $request->sameday_cash_handling_switch == 'on') {
                        foreach ($request->sameday_cash_range_up as $ind => $sameday_cash_range_up) {
                            PendingCashHandlingCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'range_up' => $request->sameday_cash_range_up[$ind],
                                'range_down' => $request->sameday_cash_range_down[$ind],
                                'charges' => $request->sameday_cash_charges[$ind]
                            ]);
                        }
                    }
                    //insurance charges
                    if ($request->has('sameday_insurance_charges_switch') && $request->sameday_insurance_charges_switch == 'on') {
                        foreach ($request->sameday_ins_range_up as $insurance => $sameday_ins_range_up) {
                            PendingInsuranceCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'range_up' => $request->sameday_ins_range_up[$insurance],
                                'range_down' => $request->sameday_ins_range_down[$insurance],
                                'charges' => $request->sameday_ins_charges[$insurance]
                            ]);
                        }
                    }
                    //Return Charges
                    if ($request->has('sameday_return_switch') && $request->sameday_return_switch == 'on') {
                        PendingReturnCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'local' => $request->sameday_return_local_charges,
                            'national_charges_class_0' => $request->sameday_return_class_0_charges,
                            'national_charges_class_1' => 0,
                            'national_charges_class_2' => 0,
                            'national_charges_class_3' => 0
                        ]);
                    }
                    if ($request->has('sameday_fuel_switch') && $request->sameday_fuel_switch == 'on') {
                        PendingFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'fuel_surcharge' => $request->sameday_fuel_surcharge
                        ]);
                    }

                    $discount_cash = null;
                    $discount_weight = null;
                    $discount_insurance = null;
                    $discount_return = null;
                    $discount_packaging = null;

                    if ($request->has('sameday_discount_weight_switch') && $request->sameday_discount_weight_switch == 'on') {
                        $discount_weight = $request->sameday_discount_weight_rate != null ? $request->sameday_discount_weight_rate : null;
//                    $discount_weight = $request->sameday_discount_weight_rate;
                    }
                    if ($request->has('sameday_discount_cash_switch') && $request->sameday_discount_cash_switch == 'on') {
                        $discount_cash = $request->sameday_discount_cash_rate != null ? $request->sameday_discount_cash_rate : null;
                    }
                    if ($request->has('sameday_discount_insurance_switch') && $request->sameday_discount_insurance_switch == 'on') {
                        $discount_insurance = $request->sameday_discount_insurance_rate != null ? $request->sameday_discount_insurance_rate : null;
                    }
                    if ($request->has('sameday_discount_return_switch') && $request->sameday_discount_return_switch == 'on') {
                        $discount_return = $request->sameday_discount_return_rate != null ? $request->sameday_discount_insurance_rate : null;
                    }
                    if ($request->has('sameday_discount_packaging_switch') && $request->sameday_discount_packaging_switch == 'on') {
                        $discount_packaging = $request->sameday_discount_packaging_rate != null ? $request->sameday_discount_packaging_rate : null;
                    }
                    if ($discount_weight != null || $discount_cash != null || $discount_insurance != null || $discount_return != null || $discount_packaging != null) {


                        $date_str = $request->sameday_daterange;
                        $date_sep = explode(' - ', $date_str);
                        $date_to = explode('/', $date_sep[0]);
                        $date_from = explode('/', $date_sep[1]);
                        $to = Carbon::create($date_to[2], $date_to[0], $date_to[1], 0, 0, 0, 'UTC')->toDateTimeString();
                        $from = Carbon::create($date_from[2], $date_from[0], $date_from[1], 0, 0, 0, 'UTC')->toDateTimeString();


                        PendingDiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'title' => $request->sameday_discount_title,
                            'weight' => $discount_weight,
                            'cash' => $discount_cash,
                            'insurance' => $discount_insurance,
                            'return' => $discount_return,
                            'packaging' => $discount_packaging,
                            'to' => $to,
                            'from' => $from,
                            'added_by' => Auth::id()
                        ]);
                    }

                    if($request->has('sd_discount_destination_wise_weight_switch') && $request->sd_discount_destination_wise_weight_switch == "on")
                    {
                        foreach($request->discount_sd_destination as $parent_key => $destination)
                        {
                            $discount_wa_switch = array();
                            $discount_wa_spkg = array();
                            foreach($request->discount_sd_wa_range_up[$parent_key] as $child_key => $value)
                            {
                                if ($request->has('discount_sd_wa_switch.'.$parent_key)) {
                                    if (array_key_exists($child_key, $request->discount_sd_wa_switch[$parent_key])) {
                                        $discount_wa_switch[$child_key] = 1;
                                    } else {
                                        $discount_wa_switch[$child_key] = 0;
                                    };
                                } else {
                                    $discount_wa_switch[$child_key] = 0;
                                }
                                if ($request->has('discount_sd_wa_spkg.'.$parent_key)) {
                                    if (array_key_exists($child_key, $request->discount_sd_wa_spkg[$parent_key])) {
                                        $discount_wa_spkg[$child_key] = $request->discount_sd_wa_spkg[$parent_key][$child_key];
                                    } else {
                                        $discount_wa_spkg[$child_key] = 0.5;
                                    };
                                } else {
                                    $discount_wa_spkg[$child_key] = 0.5;
                                }
                                PendingDiscountWeightCharge::create([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 4,
                                    'destination_id' => $destination,
                                    'range_up' => $request->discount_sd_wa_range_up[$parent_key][$child_key],
                                    'range_down' => $request->discount_sd_wa_range_down[$parent_key][$child_key],
                                    'weight_addition' => $discount_wa_switch[$child_key],
                                    'spkg' => $discount_wa_spkg[$child_key],
                                    'local_or_6hr' => $request->discount_sd_wa_local_charges[$parent_key][$child_key],
                                ]);
                            }
                        }
                    }

                }
                if ($request->has('sameday_dws_weight')) {
                    if ($request->sameday_dws_weight == 2) {
                        DwsWeightChargesController::edit($id, 4, 2, Auth::id());
                    } else {
                        DwsWeightChargesController::edit($id, 4, 1, Auth::id());
                    }

                } else {
                    DwsWeightChargesController::delete_dws_rate($id, 4);
                }
            }

            self::discounted_cod_and_return($request,$id,1,'default');

            WmsPendingUserInformation::where('user_id', $id)->delete();
            WmsPendingPerProductCharge::where('user_id', $id)->delete();
            WmsPendingPerSquareFootCharge::where('user_id', $id)->delete();
            WmsPendingStorageTypeCharge::where('user_id', $id)->delete();
            WmsPendingPackingCharge::where('user_id', $id)->delete();
            WmsPendingLabellingCharge::where('user_id', $id)->delete();
            if ($request->has('warehouse_main_switch') && $request->warehouse_main_switch == 'on') {

                $wms_user_info = new WmsPendingUserInformation();
                $wms_user_info->user_id = $id;
                $wms_user_info->warehousing = 1;
                $wms_user_info->invoicing_cycle = $request->invoicing_cycle;
                $wms_user_info->invoicing_date = 1;
                $wms_user_info->per_product_charges = ($request->has('ppc_switch')) ? 1 : 0;
                $wms_user_info->per_square_foot_charges = ($request->has('psf_switch')) ? 1 : 0;
                $wms_user_info->packing_charges = ($request->has('packing_charges_switch')) ? 1 : 0;
                $wms_user_info->labelling_charges = ($request->has('labelling_charges_switch')) ? 1 : 0;
                $wms_user_info->storage_charges = ($request->has('storage_charges_switch')) ? 1 : 0;
                $wms_user_info->save();

                if ($request->has('ppc_switch')) {
                    $ppc = new WmsPendingPerProductCharge();
                    $ppc->user_id = $id;
                    $ppc->charges = $request->ppc_charges;
                    $ppc->save();
                }
                if ($request->has('psf_switch')) {
                    $psf = new WmsPendingPerSquareFootCharge();
                    $psf->user_id = $id;
                    $psf->charges = $request->psf_charges;
                    $psf->save();
                }
                if ($request->has('storage_charges_switch')) {
                    foreach ($request->storage_type as $key => $storage_type) {
                        $storage_charges = new WmsPendingStorageTypeCharge();
                        $storage_charges->user_id = $id;
                        $storage_charges->storage_type_id = $storage_type;
                        $storage_charges->charges = $request->storage_type_charges[$key];
                        $storage_charges->save();
                    }
                }

                if ($request->has('packing_charges_switch')) {
                    foreach ($request->packing_type as $key => $packing) {
                        $ptype = new WmsPendingPackingCharge();
                        $ptype->user_id = $id;
                        $ptype->packing_type_id = $packing;
                        $ptype->packing_size_id = $request->packing_size[$key];
                        $ptype->charges = $request->packing_charges[$key];
                        $ptype->save();
                    }
                }

                if ($request->has('labelling_charges_switch')) {
                    $labelling = new WmsPendingLabellingCharge();
                    $labelling->user_id = $id;
                    $labelling->charges = $request->labelling_charges;
                    $labelling->save();
                }
            }


            if ($request->approve == 1) {
                $user = User::find($id);

                DwsWeightChargesController::approve($id);

                if ($rate_origin_hubs = RateOriginHub::where('user_id', $id)->get()) {
                    foreach ($rate_origin_hubs as $rate_origin_hub) {
                        $history_rate_origin_hub = new HistoryRateOriginHub();
                        $history_rate_origin_hub->user_id = $rate_origin_hub->user_id;
                        $history_rate_origin_hub->shipping_mode_id = $rate_origin_hub->shipping_mode_id;
                        $history_rate_origin_hub->city_id = $rate_origin_hub->city_id;
                        $history_rate_origin_hub->save();
                    }
                }
                if ($rate_destination_hubs = RateDestinationHub::where('user_id', $id)->get()) {
                    foreach ($rate_destination_hubs as $rate_destination_hub) {
                        $history_rate_destination_hub = new HistoryRateDestinationHub();
                        $history_rate_destination_hub->user_id = $rate_destination_hub->user_id;
                        $history_rate_destination_hub->shipping_mode_id = $rate_destination_hub->shipping_mode_id;
                        $history_rate_destination_hub->city_id = $rate_destination_hub->city_id;
                        $history_rate_destination_hub->save();
                    }
                }
                if($current_sms = User::where('id', $id)->where('sms_charges_status', 1)->first()){
                    HistorySmsCharges::create([
                        'user_id' => $id,
                        'sms_charges' => $current_sms['sms_charges'],
                        'sms_charges_status' => $current_sms['sms_charges_status']
                        
                    ]);
                }
                if ($switches = RateStatus::where(['user_id' => $id, 'shipping_mode_id' => 1])->first()) {

                    HistoryRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'status' => $switches['status'],
                        'cash_handling_charges' => $switches['cash_handling_charges'],
                        'insurance_charges' => $switches['insurance_charges'],
                        'return_charges' => $switches['return_charges'],
                        'fuel_charges' => $switches['fuel_charges'],
                        'zero_cod_discount' => $switches['zero_cod_discount'],
                        'return_discount' => $switches['return_discount'],
                    ]);
                }
                if ($switches = RateStatus::where(['user_id' => $id, 'shipping_mode_id' => 2])->first()) {
                    HistoryRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'status' => $switches['status'],
                        'cash_handling_charges' => $switches['cash_handling_charges'],
                        'insurance_charges' => $switches['insurance_charges'],
                        'return_charges' => $switches['return_charges'],
                        'fuel_charges' => $switches['fuel_charges'],
                        'zero_cod_discount' => $switches['zero_cod_discount'],
                        'return_discount' => $switches['return_discount'],
                    ]);
                }
                if ($switches = RateStatus::where(['user_id' => $id, 'shipping_mode_id' => 3])->first()) {
                    HistoryRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'status' => $switches['status'],
                        'cash_handling_charges' => $switches['cash_handling_charges'],
                        'insurance_charges' => $switches['insurance_charges'],
                        'return_charges' => $switches['return_charges'],
                        'fuel_charges' => $switches['fuel_charges'],
                        'zero_cod_discount' => $switches['zero_cod_discount'],
                        'return_discount' => $switches['return_discount'],
                    ]);
                }
                if ($switches = RateStatus::where(['user_id' => $id, 'shipping_mode_id' => 4])->first()) {
                    HistoryRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'status' => $switches['status'],
                        'cash_handling_charges' => $switches['cash_handling_charges'],
                        'insurance_charges' => $switches['insurance_charges'],
                        'return_charges' => $switches['return_charges'],
                        'fuel_charges' => $switches['fuel_charges'],
                        'zero_cod_discount' => $switches['zero_cod_discount'],
                        'return_discount' => $switches['return_discount'],
                    ]);
                }
                if ($weights = WeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 1])->get()) {
                    foreach ($weights as $weight) {
                        HistoryWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'range_up' => $weight['range_up'],
                            'range_down' => $weight['range_down'],
                            'weight_addition' => $weight['weight_addition'],
                            'spkg' => $weight['spkg'],
                            'local_or_6hr' => $weight['local_or_6hr'],
                            'national_charges_class_0' => $weight['national_charges_class_0'],
                            'national_charges_class_1' => $weight['national_charges_class_1'],
                            'national_charges_class_2' => $weight['national_charges_class_2'],
                            'national_charges_class_3' => $weight['national_charges_class_3'],
                        ]);
                    }
                }
                if ($weights = WeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 2])->get()) {
                    foreach ($weights as $weight) {
                        HistoryWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'range_up' => $weight['range_up'],
                            'range_down' => $weight['range_down'],
                            'weight_addition' => $weight['weight_addition'],
                            'spkg' => $weight['spkg'],
                            'local_or_6hr' => $weight['local_or_6hr'],
                            'national_charges_class_0' => $weight['national_charges_class_0'],
                            'national_charges_class_1' => $weight['national_charges_class_1'],
                            'national_charges_class_2' => $weight['national_charges_class_2'],
                            'national_charges_class_3' => $weight['national_charges_class_3'],
                        ]);
                    }
                }
                if ($weights = WeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 3])->get()) {
                    foreach ($weights as $weight) {
                        HistoryWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'range_up' => $weight['range_up'],
                            'range_down' => $weight['range_down'],
                            'weight_addition' => $weight['weight_addition'],
                            'spkg' => $weight['spkg'],
                            'local_or_6hr' => $weight['local_or_6hr'],
                            'national_charges_class_0' => $weight['national_charges_class_0'],
                            'national_charges_class_1' => $weight['national_charges_class_1'],
                            'national_charges_class_2' => $weight['national_charges_class_2'],
                            'national_charges_class_3' => $weight['national_charges_class_3'],
                        ]);
                    }
                }
                if ($weights = WeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 4])->get()) {
                    foreach ($weights as $weight) {
                        HistoryWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'range_up' => $weight['range_up'],
                            'range_down' => $weight['range_down'],
                            'weight_addition' => $weight['weight_addition'],
                            'spkg' => $weight['spkg'],
                            'local_or_6hr' => $weight['local_or_6hr'],
                            'national_charges_class_0' => $weight['national_charges_class_0'],
                            'national_charges_class_1' => $weight['national_charges_class_1'],
                            'national_charges_class_2' => $weight['national_charges_class_2'],
                            'national_charges_class_3' => $weight['national_charges_class_3'],
                        ]);
                    }
                }
                if ($bookingTypes = BookingTypeCharges::where(['user_id' => $id, 'shipping_mode_id' => 1])->get()) {
                    foreach ($bookingTypes as $bookingType) {
                        HistoryBookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'replacement_charges' => $bookingType['replacement_charges'],
                            'try_and_buy_charges' => $bookingType['try_and_buy_charges'],
                            'reverse_pickup_charges' =>$bookingType['reverse_pickup_charges']
                        ]);
                    }
                }
                if ($bookingTypes = BookingTypeCharges::where(['user_id' => $id, 'shipping_mode_id' => 2])->get()) {
                    foreach ($bookingTypes as $bookingType) {
                        HistoryBookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'replacement_charges' => $bookingType['replacement_charges'],
                            'try_and_buy_charges' => $bookingType['try_and_buy_charges'],
                            'reverse_pickup_charges' =>$bookingType['reverse_pickup_charges']
                        ]);
                    }
                }
                if ($bookingTypes = BookingTypeCharges::where(['user_id' => $id, 'shipping_mode_id' => 3])->get()) {
                    foreach ($bookingTypes as $bookingType) {
                        HistoryBookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'replacement_charges' => $bookingType['replacement_charges'],
                            'try_and_buy_charges' => $bookingType['try_and_buy_charges'],
                            'reverse_pickup_charges' =>$bookingType['reverse_pickup_charges']

                        ]);
                    }
                }
                if ($bookingTypes = BookingTypeCharges::where(['user_id' => $id, 'shipping_mode_id' => 4])->get()) {
                    foreach ($bookingTypes as $bookingType) {
                        HistoryBookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'replacement_charges' => $bookingType['replacement_charges'],
                            'try_and_buy_charges' => $bookingType['try_and_buy_charges'],
                            'reverse_pickup_charges' =>$bookingType['reverse_pickup_charges']

                        ]);
                    }
                }

                if ($cashs = CashHandlingCharge::where(['user_id' => $id, 'shipping_mode_id' => 1])->get()) {
                    foreach ($cashs as $cash) {
                        HistoryCashHandlingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'range_up' => $cash['range_up'],
                            'range_down' => $cash['range_down'],
                            'charges' => $cash['charges']
                        ]);
                    }
                }
                if ($cashs = CashHandlingCharge::where(['user_id' => $id, 'shipping_mode_id' => 2])->get()) {
                    foreach ($cashs as $cash) {
                        HistoryCashHandlingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'range_up' => $cash['range_up'],
                            'range_down' => $cash['range_down'],
                            'charges' => $cash['charges']
                        ]);
                    }
                }
                if ($cashs = CashHandlingCharge::where(['user_id' => $id, 'shipping_mode_id' => 3])->get()) {
                    foreach ($cashs as $cash) {
                        HistoryCashHandlingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'range_up' => $cash['range_up'],
                            'range_down' => $cash['range_down'],
                            'charges' => $cash['charges']
                        ]);
                    }
                }
                if ($cashs = CashHandlingCharge::where(['user_id' => $id, 'shipping_mode_id' => 4])->get()) {
                    foreach ($cashs as $cash) {
                        HistoryCashHandlingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'range_up' => $cash['range_up'],
                            'range_down' => $cash['range_down'],
                            'charges' => $cash['charges']
                        ]);
                    }
                }
                if ($insurances = InsuranceCharge::where(['user_id' => $id, 'shipping_mode_id' => 1])->get()) {
                    foreach ($insurances as $insurance) {
                        HistoryInsuranceCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'range_up' => $insurance['range_up'],
                            'range_down' => $insurance['range_down'],
                            'charges' => $insurance['charges']
                        ]);
                    }
                }
                if ($insurances = InsuranceCharge::where(['user_id' => $id, 'shipping_mode_id' => 2])->get()) {
                    foreach ($insurances as $insurance) {
                        HistoryInsuranceCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'range_up' => $insurance['range_up'],
                            'range_down' => $insurance['range_down'],
                            'charges' => $insurance['charges']
                        ]);
                    }
                }
                if ($insurances = InsuranceCharge::where(['user_id' => $id, 'shipping_mode_id' => 3])->get()) {
                    foreach ($insurances as $insurance) {
                        HistoryInsuranceCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'range_up' => $insurance['range_up'],
                            'range_down' => $insurance['range_down'],
                            'charges' => $insurance['charges']
                        ]);
                    }
                }
                if ($insurances = InsuranceCharge::where(['user_id' => $id, 'shipping_mode_id' => 4])->get()) {
                    foreach ($insurances as $insurance) {
                        HistoryInsuranceCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'range_up' => $insurance['range_up'],
                            'range_down' => $insurance['range_down'],
                            'charges' => $insurance['charges']
                        ]);
                    }
                }
                if ($returns = ReturnCharge::where(['user_id' => $id, 'shipping_mode_id' => 1])->get()) {
                    foreach ($returns as $return) {
                        HistoryReturnCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'local' => $return['local'],
                            'national_charges_class_0' => $return['national_charges_class_0'],
                            'national_charges_class_1' => $return['national_charges_class_1'],
                            'national_charges_class_2' => $return['national_charges_class_2'],
                            'national_charges_class_3' => $return['national_charges_class_3']
                        ]);
                    }
                }
                if ($returns = ReturnCharge::where(['user_id' => $id, 'shipping_mode_id' => 2])->get()) {
                    foreach ($returns as $return) {
                        HistoryReturnCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'local' => $return['local'],
                            'national_charges_class_0' => $return['national_charges_class_0'],
                            'national_charges_class_1' => $return['national_charges_class_1'],
                            'national_charges_class_2' => $return['national_charges_class_2'],
                            'national_charges_class_3' => $return['national_charges_class_3']
                        ]);
                    }
                }
                if ($returns = ReturnCharge::where(['user_id' => $id, 'shipping_mode_id' => 3])->get()) {
                    foreach ($returns as $return) {
                        HistoryReturnCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'local' => $return['local'],
                            'national_charges_class_0' => $return['national_charges_class_0'],
                            'national_charges_class_1' => $return['national_charges_class_1'],
                            'national_charges_class_2' => $return['national_charges_class_2'],
                            'national_charges_class_3' => $return['national_charges_class_3']
                        ]);
                    }
                }
                if ($returns = ReturnCharge::where(['user_id' => $id, 'shipping_mode_id' => 4])->get()) {
                    foreach ($returns as $return) {
                        HistoryReturnCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'local' => $return['local'],
                            'national_charges_class_0' => $return['national_charges_class_0'],
                            'national_charges_class_1' => $return['national_charges_class_1'],
                            'national_charges_class_2' => $return['national_charges_class_2'],
                            'national_charges_class_3' => $return['national_charges_class_3']
                        ]);
                    }
                }
                if ($fuels = FuelSurcharge::where(['user_id' => $id, 'shipping_mode_id' => 1])->get()) {
                    foreach ($fuels as $fuel) {
                        HistoryFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'fuel_surcharge' => $fuel['fuel_surcharge']
                        ]);
                    }
                }
                if ($fuels = FuelSurcharge::where(['user_id' => $id, 'shipping_mode_id' => 2])->get()) {
                    foreach ($fuels as $fuel) {
                        HistoryFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'fuel_surcharge' => $fuel['fuel_surcharge']
                        ]);
                    }
                }
                if ($fuels = FuelSurcharge::where(['user_id' => $id, 'shipping_mode_id' => 3])->get()) {
                    foreach ($fuels as $fuel) {
                        HistoryFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'fuel_surcharge' => $fuel['fuel_surcharge']
                        ]);
                    }
                }
                if ($fuels = FuelSurcharge::where(['user_id' => $id, 'shipping_mode_id' => 4])->get()) {
                    foreach ($fuels as $fuel) {
                        HistoryFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'fuel_surcharge' => $fuel['fuel_surcharge']
                        ]);
                    }
                }
                if ($packagings = PackagingCharge::where('user_id', '=', $id)->get()) {
                    foreach ($packagings as $packaging) {
                        $packaging_charges = new HistoryPackagingCharge();
                        $packaging_charges->user_id = $id;
                        $packaging_charges->type_id = $packaging->type_id;
                        $packaging_charges->size_id = $packaging->size_id;
                        $packaging_charges->charges = $packaging->charges;
                        $packaging_charges->save();
                    }
                }
                if ($packagings = PackagingCharge::where('user_id', '=', $id)->get()) {
                    foreach ($packagings as $packaging) {
                        $packaging_charges = new HistoryPackagingCharge();
                        $packaging_charges->user_id = $id;
                        $packaging_charges->type_id = $packaging->type_id;
                        $packaging_charges->size_id = $packaging->size_id;
                        $packaging_charges->charges = $packaging->charges;
                        $packaging_charges->save();
                    }
                }
                if ($packagings = PackagingCharge::where('user_id', '=', $id)->get()) {
                    foreach ($packagings as $packaging) {
                        $packaging_charges = new HistoryPackagingCharge();
                        $packaging_charges->user_id = $id;
                        $packaging_charges->type_id = $packaging->type_id;
                        $packaging_charges->size_id = $packaging->size_id;
                        $packaging_charges->charges = $packaging->charges;
                        $packaging_charges->save();
                    }
                }
                if ($packagings = PackagingCharge::where('user_id', '=', $id)->get()) {
                    foreach ($packagings as $packaging) {
                        $packaging_charges = new HistoryPackagingCharge();
                        $packaging_charges->user_id = $id;
                        $packaging_charges->type_id = $packaging->type_id;
                        $packaging_charges->size_id = $packaging->size_id;
                        $packaging_charges->charges = $packaging->charges;
                        $packaging_charges->save();
                    }
                }
                if ($discounts = DiscountCharge::where(['user_id' => $id, 'shipping_mode_id' => 1])->get()) {
                    foreach ($discounts as $discount) {
                        HistoryDiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'title' => $discount['title'],
                            'weight' => $discount['weight'],
                            'cash' => $discount['cash'],
                            'insurance' => $discount['insurance'],
                            'return' => $discount['return'],
                            'packaging' => $discount['packaging'],
                            'to' => $discount['to'],
                            'from' => $discount['from'],
                            'added_by' => $discount['added_by']
                        ]);
                    }
                }
                if ($discounts = DiscountCharge::where(['user_id' => $id, 'shipping_mode_id' => 2])->get()) {
                    foreach ($discounts as $discount) {
                        HistoryDiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'title' => $discount['title'],
                            'weight' => $discount['weight'],
                            'cash' => $discount['cash'],
                            'insurance' => $discount['insurance'],
                            'return' => $discount['return'],
                            'packaging' => $discount['packaging'],
                            'to' => $discount['to'],
                            'from' => $discount['from'],
                            'added_by' => $discount['added_by']
                        ]);
                    }
                }
                if ($discounts = DiscountCharge::where(['user_id' => $id, 'shipping_mode_id' => 3])->get()) {
                    foreach ($discounts as $discount) {
                        HistoryDiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'title' => $discount['title'],
                            'weight' => $discount['weight'],
                            'cash' => $discount['cash'],
                            'insurance' => $discount['insurance'],
                            'return' => $discount['return'],
                            'packaging' => $discount['packaging'],
                            'to' => $discount['to'],
                            'from' => $discount['from'],
                            'added_by' => $discount['added_by']
                        ]);
                    }
                }
                if ($discounts = DiscountCharge::where(['user_id' => $id, 'shipping_mode_id' => 4])->get()) {
                    foreach ($discounts as $discount) {
                        HistoryDiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'title' => $discount['title'],
                            'weight' => $discount['weight'],
                            'cash' => $discount['cash'],
                            'insurance' => $discount['insurance'],
                            'return' => $discount['return'],
                            'packaging' => $discount['packaging'],
                            'to' => $discount['to'],
                            'from' => $discount['from'],
                            'added_by' => $discount['added_by']
                        ]);
                    }
                }


                if ($discount_weights = DiscountWeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 1])->get()) {
                    foreach ($discount_weights as $discount) {
                        HistoryDiscountWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'destination_id' => $discount['destination_id'],
                            'range_up' => $discount['range_up'],
                            'range_down' => $discount['range_down'],
                            'weight_addition' => $discount['weight_addition'],
                            'spkg' => $discount['spkg'],
                            'local_or_6hr' => $discount['local_or_6hr'],
                        ]);
                    }
                }
                if ($discount_weights = DiscountWeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 2])->get()) {
                    foreach ($discount_weights as $discount) {
                        HistoryDiscountWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'destination_id' => $discount['destination_id'],
                            'range_up' => $discount['range_up'],
                            'range_down' => $discount['range_down'],
                            'weight_addition' => $discount['weight_addition'],
                            'spkg' => $discount['spkg'],
                            'local_or_6hr' => $discount['local_or_6hr'],
                        ]);
                    }
                }
                if ($discount_weights = DiscountWeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 3])->get()) {
                    foreach ($discount_weights as $discount) {
                        HistoryDiscountWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'destination_id' => $discount['destination_id'],
                            'range_up' => $discount['range_up'],
                            'range_down' => $discount['range_down'],
                            'weight_addition' => $discount['weight_addition'],
                            'spkg' => $discount['spkg'],
                            'local_or_6hr' => $discount['local_or_6hr'],
                        ]);
                    }
                }
                if ($discount_weights = DiscountWeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 4])->get()) {
                    foreach ($discount_weights as $discount) {
                        HistoryDiscountWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'destination_id' => $discount['destination_id'],
                            'range_up' => $discount['range_up'],
                            'range_down' => $discount['range_down'],
                            'weight_addition' => $discount['weight_addition'],
                            'spkg' => $discount['spkg'],
                            'local_or_6hr' => $discount['local_or_6hr'],
                        ]);
                    }
                }

                self::discounted_cod_and_return($request,$id,0,'default','fetch_and_dump');

                $s = RateStatus::where(['user_id' => $id])->first();
                RateHistory::create([
                    'user_id' => $id,
                    'updated_by' => $user['rates_updated_by'],
                    'approved_by' => $user['rates_authorized_by'],
                    'from_date' => $s['created_at'],
                    'to_date' => Carbon::now()
                ]);

                if ($wms_user_info = WmsUserInformation::where('user_id', $id)->first()) {
                    $wms_user_information = new WmsHistoryUserInformation();
                    $wms_user_information->user_id = $id;
                    $wms_user_information->warehousing = $wms_user_info['warehousing'];
                    $wms_user_information->invoicing_cycle = $wms_user_info['invoicing_cycle'];
                    $wms_user_information->invoicing_date = 1;
                    $wms_user_information->per_product_charges = $wms_user_info['per_product_charges'];
                    $wms_user_information->per_square_foot_charges = $wms_user_info['per_square_foot_charges'];
                    $wms_user_information->packing_charges = $wms_user_info['packing_charges'];
                    $wms_user_information->labelling_charges = $wms_user_info['labelling_charges'];
                    $wms_user_information->storage_charges = $wms_user_info['storage_charges'];
                    $wms_user_information->save();
                }
                if ($ppc = WmsPerProductCharge::where('user_id', $id)->first()) {
                    $ppc_history = new WmsHistoryPerProductCharge();
                    $ppc_history->user_id = $ppc['user_id'];
                    $ppc_history->charges = $ppc['charges'];
                    $ppc_history->save();
                }
                if ($psf = WmsPerSquareFootCharge::where('user_id', $id)->first()) {
                    $psf_history = new WmsHistoryPerSquareFootCharge();
                    $psf_history->user_id = $psf['user_id'];
                    $psf_history->charges = $psf['charges'];
                    $psf_history->save();
                }

                if ($storage_type_charges = WmsStorageTypeCharge::where('user_id', $id)->get()) {
                    foreach ($storage_type_charges as $storage_charges) {
                        $history_storage_charge = new WmsHistoryStorageTypeCharge();
                        $history_storage_charge->user_id = $id;
                        $history_storage_charge->storage_type_id = $storage_charges['storage_type_id'];
                        $history_storage_charge->charges = $storage_charges['charges'];
                        $history_storage_charge->save();
                    }
                }

                if ($packing_charges = WmsPackingCharge::where('user_id', $id)->get()) {
                    foreach ($packing_charges as $packing_charges) {
                        $history_packing_charge = new WmsHistoryPackingCharge();
                        $history_packing_charge->user_id = $id;
                        $history_packing_charge->packing_type_id = $packing_charges['packing_type_id'];
                        $history_packing_charge->packing_size_id = $packing_charges['packing_size_id'];
                        $history_packing_charge->charges = $packing_charges['charges'];
                        $history_packing_charge->save();
                    }
                }

                if ($labelling = WmsLabellingCharge::where('user_id', $id)->first()) {
                    $labelling_history = new WmsHistoryLabellingCharge();
                    $labelling_history->user_id = $labelling['user_id'];
                    $labelling_history->charges = $labelling['charges'];
                    $labelling_history->save();
                }

                WmsUserInformation::where('user_id', $id)->delete();
                WmsPerProductCharge::where('user_id', $id)->delete();
                WmsPerSquareFootCharge::where('user_id', $id)->delete();
                WmsStorageTypeCharge::where('user_id', $id)->delete();
                WmsPackingCharge::where('user_id', $id)->delete();
                WmsLabellingCharge::where('user_id', $id)->delete();

                User::where('id', $id)->update([
                    'sms_charges'=> null,
                    'sms_charges_status'=> 0,
                ]);
                RateStatus::where('user_id', $id)->delete();
                WeightCharge::where('user_id', $id)->delete();
                BookingTypeCharges::where('user_id', $id)->delete();
                CashHandlingCharge::where('user_id', $id)->delete();
                InsuranceCharge::where('user_id', $id)->delete();
                ReturnCharge::where('user_id', $id)->delete();
                FuelSurcharge::where('user_id', $id)->delete();
                PackagingCharge::where('user_id', $id)->delete();
                DiscountCharge::where('user_id', $id)->delete();
                DiscountWeightCharge::where('user_id',$id)->delete();

                RateOriginHub::where('user_id', $id)->delete();
                RateDestinationHub::where('user_id', $id)->delete();

                self::discounted_cod_and_return($request,$id,2,'default','delete');

                if ($pending_rate_origin_hubs = PendingRateOriginHub::where('user_id', $id)->get()) {
                    foreach ($pending_rate_origin_hubs as $pending_rate_origin_hub) {
                        $rate_origin_hub = new RateOriginHub();
                        $rate_origin_hub->user_id = $pending_rate_origin_hub->user_id;
                        $rate_origin_hub->shipping_mode_id = $pending_rate_origin_hub->shipping_mode_id;
                        $rate_origin_hub->city_id = $pending_rate_origin_hub->city_id;
                        $rate_origin_hub->save();
                    }
                }
                if ($pending_rate_destination_hubs = PendingRateDestinationHub::where('user_id', $id)->get()) {
                    foreach ($pending_rate_destination_hubs as $pending_rate_destination_hub) {
                        $rate_destination_hub = new RateDestinationHub();
                        $rate_destination_hub->user_id = $pending_rate_destination_hub->user_id;
                        $rate_destination_hub->shipping_mode_id = $pending_rate_destination_hub->shipping_mode_id;
                        $rate_destination_hub->city_id = $pending_rate_destination_hub->city_id;
                        $rate_destination_hub->save();
                    }
                }
                if($pending_sms = PendingSmsCharges::where('user_id', $id)->first()){
                    User::where('id', $id)->update([
                        'sms_charges'=> $pending_sms->sms_charges,
                        'sms_charges_status'=> $pending_sms->sms_charges_status,
                    ]);
                }
                if ($pendingswitchs = PendingRateStatus::where(['user_id' => $id, 'shipping_mode_id' => 1])->first()) {
                    RateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'status' => $pendingswitchs['status'],
                        'cash_handling_charges' => $pendingswitchs['cash_handling_charges'],
                        'insurance_charges' => $pendingswitchs['insurance_charges'],
                        'return_charges' => $pendingswitchs['return_charges'],
                        'fuel_charges' => $pendingswitchs['fuel_charges'],
                        'zero_cod_discount' => $pendingswitchs['zero_cod_discount'],
                        'return_discount' => $pendingswitchs['return_discount'],
                    ]);
                }
                if ($pendingswitchs = PendingRateStatus::where(['user_id' => $id, 'shipping_mode_id' => 2])->first()) {
                    RateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'status' => $pendingswitchs['status'],
                        'cash_handling_charges' => $pendingswitchs['cash_handling_charges'],
                        'insurance_charges' => $pendingswitchs['insurance_charges'],
                        'return_charges' => $pendingswitchs['return_charges'],
                        'fuel_charges' => $pendingswitchs['fuel_charges'],
                        'zero_cod_discount' => $pendingswitchs['zero_cod_discount'],
                        'return_discount' => $pendingswitchs['return_discount'],
                    ]);
                }
                if ($pendingswitchs = PendingRateStatus::where(['user_id' => $id, 'shipping_mode_id' => 3])->first()) {
                    RateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'status' => $pendingswitchs['status'],
                        'cash_handling_charges' => $pendingswitchs['cash_handling_charges'],
                        'insurance_charges' => $pendingswitchs['insurance_charges'],
                        'return_charges' => $pendingswitchs['return_charges'],
                        'fuel_charges' => $pendingswitchs['fuel_charges'],
                        'zero_cod_discount' => $pendingswitchs['zero_cod_discount'],
                        'return_discount' => $pendingswitchs['return_discount'],
                    ]);
                }
                if ($pendingswitchs = PendingRateStatus::where(['user_id' => $id, 'shipping_mode_id' => 4])->first()) {
                    RateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'status' => $pendingswitchs['status'],
                        'cash_handling_charges' => $pendingswitchs['cash_handling_charges'],
                        'insurance_charges' => $pendingswitchs['insurance_charges'],
                        'return_charges' => $pendingswitchs['return_charges'],
                        'fuel_charges' => $pendingswitchs['fuel_charges'],
                        'zero_cod_discount' => $pendingswitchs['zero_cod_discount'],
                        'return_discount' => $pendingswitchs['return_discount'],
                    ]);
                }
                if ($pendingweights = PendingWeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 1])->get()) {
                    foreach ($pendingweights as $pendingweight) {
                        WeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'range_up' => $pendingweight['range_up'],
                            'range_down' => $pendingweight['range_down'],
                            'weight_addition' => $pendingweight['weight_addition'],
                            'spkg' => $pendingweight['spkg'],
                            'local_or_6hr' => $pendingweight['local_or_6hr'],
                            'national_charges_class_0' => $pendingweight['national_charges_class_0'],
                            'national_charges_class_1' => $pendingweight['national_charges_class_1'],
                            'national_charges_class_2' => $pendingweight['national_charges_class_2'],
                            'national_charges_class_3' => $pendingweight['national_charges_class_3'],
                        ]);
                    }
                }
                if ($pendingweights = PendingWeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 2])->get()) {
                    foreach ($pendingweights as $pendingweight) {
                        WeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'range_up' => $pendingweight['range_up'],
                            'range_down' => $pendingweight['range_down'],
                            'weight_addition' => $pendingweight['weight_addition'],
                            'spkg' => $pendingweight['spkg'],
                            'local_or_6hr' => $pendingweight['local_or_6hr'],
                            'national_charges_class_0' => $pendingweight['national_charges_class_0'],
                            'national_charges_class_1' => $pendingweight['national_charges_class_1'],
                            'national_charges_class_2' => $pendingweight['national_charges_class_2'],
                            'national_charges_class_3' => $pendingweight['national_charges_class_3'],
                        ]);
                    }
                }
                if ($pendingweights = PendingWeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 3])->get()) {
                    foreach ($pendingweights as $pendingweight) {
                        WeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'range_up' => $pendingweight['range_up'],
                            'range_down' => $pendingweight['range_down'],
                            'weight_addition' => $pendingweight['weight_addition'],
                            'spkg' => $pendingweight['spkg'],
                            'local_or_6hr' => $pendingweight['local_or_6hr'],
                            'national_charges_class_0' => $pendingweight['national_charges_class_0'],
                            'national_charges_class_1' => $pendingweight['national_charges_class_1'],
                            'national_charges_class_2' => $pendingweight['national_charges_class_2'],
                            'national_charges_class_3' => $pendingweight['national_charges_class_3'],
                        ]);
                    }
                }
                if ($pendingweights = PendingWeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 4])->get()) {
                    foreach ($pendingweights as $pendingweight) {
                        WeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'range_up' => $pendingweight['range_up'],
                            'range_down' => $pendingweight['range_down'],
                            'weight_addition' => $pendingweight['weight_addition'],
                            'spkg' => $pendingweight['spkg'],
                            'local_or_6hr' => $pendingweight['local_or_6hr'],
                            'national_charges_class_0' => $pendingweight['national_charges_class_0'],
                            'national_charges_class_1' => $pendingweight['national_charges_class_1'],
                            'national_charges_class_2' => $pendingweight['national_charges_class_2'],
                            'national_charges_class_3' => $pendingweight['national_charges_class_3'],
                        ]);
                    }
                }
                if ($pendingbookingTypes = PendingBookingTypeCharges::where(['user_id' => $id, 'shipping_mode_id' => 1])->get()) {
                    foreach ($pendingbookingTypes as $pendingbookingType) {
                        BookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'replacement_charges' => $pendingbookingType['replacement_charges'],
                            'try_and_buy_charges' => $pendingbookingType['try_and_buy_charges'],
                            'reverse_pickup_charges' => $pendingbookingType['reverse_pickup_charges'],

                        ]);
                    }
                }
                if ($pendingbookingTypes = PendingBookingTypeCharges::where(['user_id' => $id, 'shipping_mode_id' => 2])->get()) {
                    foreach ($pendingbookingTypes as $pendingbookingType) {
                        BookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'replacement_charges' => $pendingbookingType['replacement_charges'],
                            'try_and_buy_charges' => $pendingbookingType['try_and_buy_charges'],
                            'reverse_pickup_charges' => $pendingbookingType['reverse_pickup_charges'],

                        ]);
                    }
                }
                if ($pendingbookingTypes = PendingBookingTypeCharges::where(['user_id' => $id, 'shipping_mode_id' => 3])->get()) {
                    foreach ($pendingbookingTypes as $pendingbookingType) {
                        BookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'replacement_charges' => $pendingbookingType['replacement_charges'],
                            'try_and_buy_charges' => $pendingbookingType['try_and_buy_charges'],
                            'reverse_pickup_charges' => $pendingbookingType['reverse_pickup_charges'],

                        ]);
                    }
                }
                if ($pendingbookingTypes = PendingBookingTypeCharges::where(['user_id' => $id, 'shipping_mode_id' => 4])->get()) {
                    foreach ($pendingbookingTypes as $pendingbookingType) {
                        BookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'replacement_charges' => $pendingbookingType['replacement_charges'],
                            'try_and_buy_charges' => $pendingbookingType['try_and_buy_charges'],
                            'reverse_pickup_charges' => $pendingbookingType['reverse_pickup_charges'],

                        ]);
                    }
                }
                if ($pendingcashs = PendingCashHandlingCharge::where(['user_id' => $id, 'shipping_mode_id' => 1])->get()) {
                    foreach ($pendingcashs as $pendingcash) {
                        CashHandlingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'range_up' => $pendingcash['range_up'],
                            'range_down' => $pendingcash['range_down'],
                            'charges' => $pendingcash['charges']
                        ]);
                    }
                }
                if ($pendingcashs = PendingCashHandlingCharge::where(['user_id' => $id, 'shipping_mode_id' => 2])->get()) {
                    foreach ($pendingcashs as $pendingcash) {
                        CashHandlingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'range_up' => $pendingcash['range_up'],
                            'range_down' => $pendingcash['range_down'],
                            'charges' => $pendingcash['charges']
                        ]);
                    }
                }
                if ($pendingcashs = PendingCashHandlingCharge::where(['user_id' => $id, 'shipping_mode_id' => 3])->get()) {
                    foreach ($pendingcashs as $pendingcash) {
                        CashHandlingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'range_up' => $pendingcash['range_up'],
                            'range_down' => $pendingcash['range_down'],
                            'charges' => $pendingcash['charges']
                        ]);
                    }
                }
                if ($pendingcashs = PendingCashHandlingCharge::where(['user_id' => $id, 'shipping_mode_id' => 4])->get()) {
                    foreach ($pendingcashs as $pendingcash) {
                        CashHandlingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'range_up' => $pendingcash['range_up'],
                            'range_down' => $pendingcash['range_down'],
                            'charges' => $pendingcash['charges']
                        ]);
                    }
                }
                if ($pendinginsurances = PendingInsuranceCharge::where(['user_id' => $id, 'shipping_mode_id' => 1])->get()) {
                    foreach ($pendinginsurances as $pendinginsurance) {
                        InsuranceCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'range_up' => $pendinginsurance['range_up'],
                            'range_down' => $pendinginsurance['range_down'],
                            'charges' => $pendinginsurance['charges']
                        ]);
                    }
                }
                if ($pendinginsurances = PendingInsuranceCharge::where(['user_id' => $id, 'shipping_mode_id' => 2])->get()) {
                    foreach ($pendinginsurances as $pendinginsurance) {
                        InsuranceCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'range_up' => $pendinginsurance['range_up'],
                            'range_down' => $pendinginsurance['range_down'],
                            'charges' => $pendinginsurance['charges']
                        ]);
                    }
                }
                if ($pendinginsurances = PendingInsuranceCharge::where(['user_id' => $id, 'shipping_mode_id' => 3])->get()) {
                    foreach ($pendinginsurances as $pendinginsurance) {
                        InsuranceCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'range_up' => $pendinginsurance['range_up'],
                            'range_down' => $pendinginsurance['range_down'],
                            'charges' => $pendinginsurance['charges']
                        ]);
                    }
                }
                if ($pendinginsurances = PendingInsuranceCharge::where(['user_id' => $id, 'shipping_mode_id' => 4])->get()) {
                    foreach ($pendinginsurances as $pendinginsurance) {
                        InsuranceCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'range_up' => $pendinginsurance['range_up'],
                            'range_down' => $pendinginsurance['range_down'],
                            'charges' => $pendinginsurance['charges']
                        ]);
                    }
                }
                if ($pendingreturns = PendingReturnCharge::where(['user_id' => $id, 'shipping_mode_id' => 1])->get()) {
                    foreach ($pendingreturns as $pendingreturn) {
                        ReturnCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'local' => $pendingreturn['local'],
                            'national_charges_class_0' => $pendingreturn['national_charges_class_0'],
                            'national_charges_class_1' => $pendingreturn['national_charges_class_1'],
                            'national_charges_class_2' => $pendingreturn['national_charges_class_2'],
                            'national_charges_class_3' => $pendingreturn['national_charges_class_3']
                        ]);
                    }
                }
                if ($pendingreturns = PendingReturnCharge::where(['user_id' => $id, 'shipping_mode_id' => 2])->get()) {
                    foreach ($pendingreturns as $pendingreturn) {
                        ReturnCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'local' => $pendingreturn['local'],
                            'national_charges_class_0' => $pendingreturn['national_charges_class_0'],
                            'national_charges_class_1' => $pendingreturn['national_charges_class_1'],
                            'national_charges_class_2' => $pendingreturn['national_charges_class_2'],
                            'national_charges_class_3' => $pendingreturn['national_charges_class_3']
                        ]);
                    }
                }
                if ($pendingreturns = PendingReturnCharge::where(['user_id' => $id, 'shipping_mode_id' => 3])->get()) {
                    foreach ($pendingreturns as $pendingreturn) {
                        ReturnCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'local' => $pendingreturn['local'],
                            'national_charges_class_0' => $pendingreturn['national_charges_class_0'],
                            'national_charges_class_1' => $pendingreturn['national_charges_class_1'],
                            'national_charges_class_2' => $pendingreturn['national_charges_class_2'],
                            'national_charges_class_3' => $pendingreturn['national_charges_class_3']
                        ]);
                    }
                }
                if ($pendingreturns = PendingReturnCharge::where(['user_id' => $id, 'shipping_mode_id' => 4])->get()) {
                    foreach ($pendingreturns as $pendingreturn) {
                        ReturnCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'local' => $pendingreturn['local'],
                            'national_charges_class_0' => $pendingreturn['national_charges_class_0'],
                            'national_charges_class_1' => $pendingreturn['national_charges_class_1'],
                            'national_charges_class_2' => $pendingreturn['national_charges_class_2'],
                            'national_charges_class_3' => $pendingreturn['national_charges_class_3']
                        ]);
                    }
                }
                if ($pendingfuels = PendingFuelSurcharge::where(['user_id' => $id, 'shipping_mode_id' => 1])->get()) {
                    foreach ($pendingfuels as $pendingfuel) {
                        FuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'fuel_surcharge' => $pendingfuel['fuel_surcharge']
                        ]);
                    }
                }
                if ($pendingfuels = PendingFuelSurcharge::where(['user_id' => $id, 'shipping_mode_id' => 2])->get()) {
                    foreach ($pendingfuels as $pendingfuel) {
                        FuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'fuel_surcharge' => $pendingfuel['fuel_surcharge']
                        ]);
                    }
                }
                if ($pendingfuels = PendingFuelSurcharge::where(['user_id' => $id, 'shipping_mode_id' => 3])->get()) {
                    foreach ($pendingfuels as $pendingfuel) {
                        FuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'fuel_surcharge' => $pendingfuel['fuel_surcharge']
                        ]);
                    }
                }
                if ($pendingfuels = PendingFuelSurcharge::where(['user_id' => $id, 'shipping_mode_id' => 4])->get()) {
                    foreach ($pendingfuels as $pendingfuel) {
                        FuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'fuel_surcharge' => $pendingfuel['fuel_surcharge']
                        ]);
                    }
                }
                if ($pendingpackagings = PendingPackagingCharge::where('user_id', '=', $id)->get()) {
                    foreach ($pendingpackagings as $pendingpackaging) {
                        $packaging_charges = new PackagingCharge();
                        $packaging_charges->user_id = $id;
                        $packaging_charges->type_id = $pendingpackaging->type_id;
                        $packaging_charges->size_id = $pendingpackaging->size_id;
                        $packaging_charges->charges = $pendingpackaging->charges;
                        $packaging_charges->save();
                    }
                }
                if ($pendingdiscounts = PendingDiscountCharge::where(['user_id' => $id, 'shipping_mode_id' => 1])->get()) {
                    foreach ($pendingdiscounts as $pendingdiscount) {
                        DiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'title' => $pendingdiscount['title'],
                            'weight' => $pendingdiscount['weight'],
                            'cash' => $pendingdiscount['cash'],
                            'insurance' => $pendingdiscount['insurance'],
                            'return' => $pendingdiscount['return'],
                            'packaging' => $pendingdiscount['packaging'],
                            'to' => $pendingdiscount['to'],
                            'from' => $pendingdiscount['from'],
                            'added_by' => $pendingdiscount['added_by']
                        ]);
                    }
                }
                if ($pendingdiscounts = PendingDiscountCharge::where(['user_id' => $id, 'shipping_mode_id' => 2])->get()) {
                    foreach ($pendingdiscounts as $pendingdiscount) {
                        DiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'title' => $pendingdiscount['title'],
                            'weight' => $pendingdiscount['weight'],
                            'cash' => $pendingdiscount['cash'],
                            'insurance' => $pendingdiscount['insurance'],
                            'return' => $pendingdiscount['return'],
                            'packaging' => $pendingdiscount['packaging'],
                            'to' => $pendingdiscount['to'],
                            'from' => $pendingdiscount['from'],
                            'added_by' => $pendingdiscount['added_by']
                        ]);
                    }
                }
                if ($pendingdiscounts = PendingDiscountCharge::where(['user_id' => $id, 'shipping_mode_id' => 3])->get()) {
                    foreach ($pendingdiscounts as $pendingdiscount) {
                        DiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'title' => $pendingdiscount['title'],
                            'weight' => $pendingdiscount['weight'],
                            'cash' => $pendingdiscount['cash'],
                            'insurance' => $pendingdiscount['insurance'],
                            'return' => $pendingdiscount['return'],
                            'packaging' => $pendingdiscount['packaging'],
                            'to' => $pendingdiscount['to'],
                            'from' => $pendingdiscount['from'],
                            'added_by' => $pendingdiscount['added_by']
                        ]);
                    }
                }
                if ($pendingdiscounts = PendingDiscountCharge::where(['user_id' => $id, 'shipping_mode_id' => 4])->get()) {
                    foreach ($pendingdiscounts as $pendingdiscount) {
                        DiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'title' => $pendingdiscount['title'],
                            'weight' => $pendingdiscount['weight'],
                            'cash' => $pendingdiscount['cash'],
                            'insurance' => $pendingdiscount['insurance'],
                            'return' => $pendingdiscount['return'],
                            'packaging' => $pendingdiscount['packaging'],
                            'to' => $pendingdiscount['to'],
                            'from' => $pendingdiscount['from'],
                            'added_by' => $pendingdiscount['added_by']
                        ]);
                    }
                }



                if ($pending_discount_weights = PendingDiscountWeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 1])->get()) {
                    foreach ($pending_discount_weights as $discount) {
                        DiscountWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'destination_id' => $discount['destination_id'],
                            'range_up' => $discount['range_up'],
                            'range_down' => $discount['range_down'],
                            'weight_addition' => $discount['weight_addition'],
                            'spkg' => $discount['spkg'],
                            'local_or_6hr' => $discount['local_or_6hr'],
                        ]);
                    }
                }
                if ($pending_discount_weights = PendingDiscountWeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 2])->get()) {
                    foreach ($pending_discount_weights as $discount) {
                        DiscountWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'destination_id' => $discount['destination_id'],
                            'range_up' => $discount['range_up'],
                            'range_down' => $discount['range_down'],
                            'weight_addition' => $discount['weight_addition'],
                            'spkg' => $discount['spkg'],
                            'local_or_6hr' => $discount['local_or_6hr'],
                        ]);
                    }
                }
                if ($pending_discount_weights = PendingDiscountWeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 3])->get()) {
                    foreach ($pending_discount_weights as $discount) {
                        DiscountWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'destination_id' => $discount['destination_id'],
                            'range_up' => $discount['range_up'],
                            'range_down' => $discount['range_down'],
                            'weight_addition' => $discount['weight_addition'],
                            'spkg' => $discount['spkg'],
                            'local_or_6hr' => $discount['local_or_6hr'],
                        ]);
                    }
                }
                if ($pending_discount_weights = PendingDiscountWeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 4])->get()) {
                    foreach ($pending_discount_weights as $discount) {
                        DiscountWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'destination_id' => $discount['destination_id'],
                            'range_up' => $discount['range_up'],
                            'range_down' => $discount['range_down'],
                            'weight_addition' => $discount['weight_addition'],
                            'spkg' => $discount['spkg'],
                            'local_or_6hr' => $discount['local_or_6hr'],
                        ]);
                    }
                }

                self::discounted_cod_and_return($request,$id,3,'default','fetch_and_dump');

                if ($wms_user_info = WmsPendingUserInformation::where('user_id', $id)->first()) {
                    $wms_user_information = new WmsUserInformation();
                    $wms_user_information->user_id = $id;
                    $wms_user_information->warehousing = $wms_user_info['warehousing'];
                    $wms_user_information->invoicing_cycle = $wms_user_info['invoicing_cycle'];
                    $wms_user_information->invoicing_date = 1;
                    $wms_user_information->per_product_charges = $wms_user_info['per_product_charges'];
                    $wms_user_information->per_square_foot_charges = $wms_user_info['per_square_foot_charges'];
                    $wms_user_information->packing_charges = $wms_user_info['packing_charges'];
                    $wms_user_information->labelling_charges = $wms_user_info['labelling_charges'];
                    $wms_user_information->storage_charges = $wms_user_info['storage_charges'];
                    $wms_user_information->save();
                }
                if ($ppc = WmsPendingPerProductCharge::where('user_id', $id)->first()) {
                    $ppc_history = new WmsPerProductCharge();
                    $ppc_history->user_id = $ppc['user_id'];
                    $ppc_history->charges = $ppc['charges'];
                    $ppc_history->save();
                }
                if ($psf = WmsPendingPerSquareFootCharge::where('user_id', $id)->first()) {
                    $psf_history = new WmsPerSquareFootCharge();
                    $psf_history->user_id = $psf['user_id'];
                    $psf_history->charges = $psf['charges'];
                    $psf_history->save();
                }

                if ($storage_type_charges = WmsPendingStorageTypeCharge::where('user_id', $id)->get()) {
                    foreach ($storage_type_charges as $storage_charges) {
                        $history_storage_charge = new WmsStorageTypeCharge();
                        $history_storage_charge->user_id = $id;
                        $history_storage_charge->storage_type_id = $storage_charges['storage_type_id'];
                        $history_storage_charge->charges = $storage_charges['charges'];
                        $history_storage_charge->save();
                    }
                }

                if ($packing_charges = WmsPendingPackingCharge::where('user_id', $id)->get()) {
                    foreach ($packing_charges as $packing_charges) {
                        $history_packing_charge = new WmsPackingCharge();
                        $history_packing_charge->user_id = $id;
                        $history_packing_charge->packing_type_id = $packing_charges['packing_type_id'];
                        $history_packing_charge->packing_size_id = $packing_charges['packing_size_id'];
                        $history_packing_charge->charges = $packing_charges['charges'];
                        $history_packing_charge->save();
                    }
                }

                if ($labelling = WmsPendingLabellingCharge::where('user_id', $id)->first()) {
                    $labelling_history = new WmsLabellingCharge();
                    $labelling_history->user_id = $labelling['user_id'];
                    $labelling_history->charges = $labelling['charges'];
                    $labelling_history->save();
                }

                WmsPendingUserInformation::where('user_id', $id)->delete();
                WmsPendingPerProductCharge::where('user_id', $id)->delete();
                WmsPendingPerSquareFootCharge::where('user_id', $id)->delete();
                WmsPendingStorageTypeCharge::where('user_id', $id)->delete();
                WmsPendingPackingCharge::where('user_id', $id)->delete();
                WmsPendingLabellingCharge::where('user_id', $id)->delete();
                PendingSmsCharges::where('user_id', $id)->delete();
                PendingRateStatus::where('user_id', $id)->delete();
                PendingWeightCharge::where('user_id', $id)->delete();
                PendingBookingTypeCharges::where('user_id', $id)->delete();
                PendingCashHandlingCharge::where('user_id', $id)->delete();
                PendingInsuranceCharge::where('user_id', $id)->delete();
                PendingReturnCharge::where('user_id', $id)->delete();
                PendingFuelSurcharge::where('user_id', $id)->delete();
                PendingPackagingCharge::where('user_id', $id)->delete();
                PendingDiscountCharge::where('user_id', $id)->delete();
                PendingDiscountWeightCharge::where('user_id',$id)->delete();
                PendingRateOriginHub::where('user_id', $id)->delete();
                PendingRateDestinationHub::where('user_id', $id)->delete();
                self::discounted_cod_and_return($request,$id,1,'default','delete');

                User::where('id', $id)->update(['rate_status' => 0, 'agreement_signed' => 1, 'rates_authorized_by' => Auth::id(), 'rates_approved_at' => Carbon::now()]);
                if ($request->has('rate_remarks') && $request->rate_remarks != null) {
                    $rate_remark = new RateRemark();
                    $rate_remark->user_id = $id;
                    $rate_remark->remarks = $request->rate_remarks;
                    $rate_remark->admin_id = Auth::id();
                    $rate_remark->save();

                }

                $user = User::find($id);
                if($user->sms_charges_status == 1) {
                    $notification_settings = NotificationSetting::where('shipper_toggle' , 0)->pluck('id');
                    foreach($notification_settings as $notification_setting ) {
                        $notification_setting_shipper = new NotificationSettingShipper();
                        $notification_setting_shipper->notification_setting_id = $notification_setting;
                        $notification_setting_shipper->shipper_id = $id;
                        $notification_setting_shipper->save();
                    }
                }

                return redirect(route('admin.accounts.active'))->with('success', 'User Rates is now approved.');
            }
            User::where('id', $id)->update(['rate_status' => 1, 'rates_updated_by' => Auth::id()]);
            if ($request->has('rate_remarks') && $request->rate_remarks != null) {
                $rate_remark = new RateRemark();
                $rate_remark->user_id = $id;
                $rate_remark->remarks = $request->rate_remarks;
                $rate_remark->admin_id = Auth::id();
                $rate_remark->save();
            }

            if ($request->has('edit_commission') && $request->edit_commission == 1) {
                if ($request->total_commission > 0) {
                    $existing_sale_commission = SalesCommission::where('shipper_id', $id)->first();
                    $user_type = [];
                    if ($existing_sale_commission) {
                        $types = SalesCommissionUser::where('sales_commission_id', $existing_sale_commission->id)->pluck('user_type')->toArray();
                        foreach($types as $key => $user_type_value){
                            $user_type[$key+1] = $user_type_value;
                        }
                        SalesCommissionUser::where('sales_commission_id', $existing_sale_commission->id)->delete();
                        SalesCommissionExternalUser::where('shipper_id', $id)->delete();
                        SalesCommission::where('shipper_id', $id)->delete();
                    }
                    $total_commission = $request->total_commission;
                    $users_count = count($request->user_id);

                    $sales_commission = new SalesCommission();
                    $sales_commission->shipper_id = $id;
                    $sales_commission->commission_users_count = $users_count;
                    $sales_commission->commission = $total_commission;
                    $sales_commission->updated_by = Auth::id();
                    $sales_commission->save();
                    $sales_commission_id = $sales_commission->id;
                    $actual_commission = 0;
                    foreach ($request->tier_id as $row_id => $tier) {
                        $sales_tier = SalesTier::find($tier);
                        if ($sales_tier) {
                            $sales_commission_user = new SalesCommissionUser();
                            $sales_commission_user->sales_commission_id = $sales_commission_id;
                            $sales_commission_user->tier_type_id = $sales_tier->tier_type;
                            $sales_commission_user->tier_id = $tier;
                            if ($sales_tier->tier_type == 1) {
                                if (strpos($request->user_id[$row_id], 'riders') !== false) {                      
                                    $sales_commission_user->user_type = "2";
                                }  
                                if(isset($user_type[$row_id]) && $user_type[$row_id] == "2"){
                                    $sales_commission_user->user_type = "2";
                                }
                                $sales_commission_user->user_id = $request->user_id[$row_id];
                            } else if ($sales_tier->tier_type == 2) {
                                $external_user = new SalesCommissionExternalUser();
                                $external_user->name = $request->user_id[$row_id];
                                $external_user->shipper_id = $id;
                                $external_user->save();
                                $sales_commission_user->user_id = $external_user->id;
                            }
                            $sales_commission_user->commission = $request->commission_percentage[$row_id];
                            $actual_commission += $request->commission_percentage[$row_id];
                            $sales_commission_user->save();
                        }
                    }
                    $sales_commission->commission = $actual_commission;
                    $sales_commission->save();
                } else {
                    $existing_sale_commission = SalesCommission::where('shipper_id', $id)->first();
                    if ($existing_sale_commission) {
                        SalesCommissionUser::where('sales_commission_id', $existing_sale_commission->id)->delete();
                        SalesCommissionExternalUser::where('shipper_id', $id)->delete();
                        SalesCommission::where('shipper_id', $id)->delete();
                    }
                }
            }

            NotificationsController::send(34, $id, Auth::id());

            return redirect()->back()->with('success', 'All Rates are updated');
        }
    }
    /**
     * @param Request $request
     * @param $id
     * @return int
     */
    public function addRates(Request $request, $id)
    {
        $messages = [
            'sms_charges.required' => 'The sms charges field is required.',
            'sms_charges.gt' => 'The sms charges must be greater than 0.',
            'on_wa_range_up.*.required' => 'The overnight range up field is required.',
            'on_wa_range_up.*.numeric' => 'The overnight range up field must be numeric or decimal.',
            'on_wa_range_up.*.between' => 'The overnight range up field must be between 0 to 999.99',
            'on_wa_range_down.*.required' => 'The overnight range down field is required.',
            'on_wa_range_down.*.numeric' => 'The overnight range down field must be numeric or decimal.',
            'on_wa_range_down.*.between' => 'The overnight range down field must be between 0 to 999.99',
            'on_wa_spkg.*.numeric' => 'The overnight KG Range field must be numeric.',
            'on_wa_local_charges.*.required' => 'The overnight local charges field is required.',
            'on_wa_local_charges.*.numeric' => 'The overnight local charges field must be numeric.',
            'on_class_0_charges.*.numeric' => 'The overnight class A charges field must be numeric.',
            'on_class_0_charges.*.required' => 'The overnight class A charges field is required.',
            'on_class_1_charges.*.required' => 'The overnight class B charges field is required.',
            'on_class_2_charges.*.required' => 'The overnight class C charges field is required.',
            'on_class_3_charges.*.required' => 'The overnight class D charges field is required.',
//            'on_class_0_charges.*.numeric' => 'The overnight national charges field must be numeric.',
            'on_replacement_charges.numeric' => 'The overnight replacement charges field must be numeric.',
            'on_replacement_charges.required' => 'The overnight replacement charges field is required.',
            'on_tnb_charges.numeric' => 'The overnight try and buy charges field must be numeric.',
            'on_tnb_charges.required' => 'The overnight try and buy charges field is required.',
            'on_reverse_charges.numeric' => 'The overnight reverse pickup charges field must be numeric.',
            'on_reverse_charges.required' => 'The overnight reverse pickup charges field is required.',
            'on_cash_range_up.*.required_if' => 'The overnight cash range up field is required.',
            'on_cash_range_up.*.numeric' => 'The overnight cash range up field must be numeric.',
            'on_cash_range_down.*.required_if' => 'The overnight cash range down field is required.',
            'on_cash_range_down.*.numeric' => 'The overnight cash range down field must be numeric.',
            'on_cash_charges.*.required_if' => 'The overnight cash charges field is required.',
            //'on_cash_charges.*.string' => 'The overnight cash charges field must be string.',
            'on_ins_range_up.*.required_if' => 'The overnight insurance range up field is required.',
            'on_ins_range_up.*.numeric' => 'The overnight insurance range up field must be numeric or percentage.',
            'on_ins_range_down.*.required_if' => 'The overnight insurance range down field is required.',
            'on_ins_range_down.*.numeric' => 'The overnight insurance range down field must be numeric or percentage.',
            'on_ins_charges.*.required_if' => 'The overnight insurance charges field is required.',
            //'on_ins_charges.*.string' => 'The overnight insurance charges field must be string.',
            'on_return_local_charges.required_if' => 'The overnight return local charges field is required.',
            'on_return_local_charges.numeric' => 'The overnight return local charges field must be numeric or percentage.',
            'on_return_class_0_charges.*.numeric' => 'The overnight class A return charges field must be numeric.',
            'on_return_class_0_charges.*.required_if' => 'The overnight class A return charges field is required.',
            'on_return_class_1_charges.*.required_if' => 'The overnight class B return charges field is required.',
            'on_return_class_2_charges.*.required_if' => 'The overnight class C return charges field is required.',
            'on_return_class_3_charges.*.required_if' => 'The overnight class D return charges field is required.',
            'overnight_fuel_surcharge.required_if' => 'The overnight return national charges field is required.',
            'overnight_fuel_surcharge.numeric' => 'The overnight return national charges field must be numeric or percentage.',
            'packaging_material_size_.*.required_if' => 'The overnight packaging material size charges field is required',
            'packaging_material_size_.*.numeric' => 'The overnight packaging material size charges field must be numeric',
            'on_discount_title.required_if' => 'The overnight discount title field must be required',
            'on_daterange.required_if' => 'The overnight discount date range field must be required',
            'on_discount_weight_rate.required_if' => 'The overnight discount weight field must be required',
//            'on_discount_weight_rate.numeric' => 'The overnight discount weight field must be numeric',
            'on_discount_cash_rate.required_if' => 'The overnight discount cash field must be required',
//            'on_discount_cash_rate.numeric' => 'The overnight discount cash field must be numeric',
            'on_discount_insurance_rate.required_if' => 'The overnight discount insurance field must be required',
//            'on_discount_insurance_rate.numeric' => 'The overnight discount insurance field must be numeric',
            'on_discount_return_rate.required_if' => 'The overnight discount return field must be required',
//            'on_discount_return_rate.numeric' => 'The overnight discount return field must be numeric',
            'on_discount_packaging_rate.required_if' => 'The overnight discount packaging field must be required',
//            'on_discount_packaging_rate.numeric' => 'The overnight discount packaging field must be numeric',
            'on_discount_title.required_with' => 'The overnight discount title field is required',
            'on_daterange.required_with' => 'The overnight discount date field is required',
            //overland starts
            'ol_wa_range_up.*.required' => 'The overland range up field is required.',
            'ol_wa_range_up.*.numeric' => 'The overland range up field must be numeric or decimal.',
            'ol_wa_range_up.*.between' => 'The overland range up field must be between 0 to 999.99',
            'ol_wa_range_down.*.required' => 'The overland range down field is required.',
            'ol_wa_range_down.*.numeric' => 'The overland range down field must be numeric or decimal.',
            'ol_wa_range_down.*.between' => 'The overland range down field must be between 0 to 999.99',
            'ol_wa_spkg.*.numeric' => 'The overland KG Range field must be numeric.',
            'ol_wa_local_charges.*.required' => 'The overland local charges field is required.',
            'ol_wa_local_charges.*.numeric' => 'The overland local charges field must be numeric.',
            'ol_class_0_charges.*.required' => 'The overland class A charges field is required.',
            'ol_class_0_charges.*.numeric' => 'The overland class A charges field must be numeric.',
            'ol_class_1_charges.*.required' => 'The overland class B charges field is required.',
            'ol_class_2_charges.*.required' => 'The overland class C charges field is required.',
            'ol_class_3_charges.*.required' => 'The overland class D charges field is required.',
            'ol_replacement_charges.numeric' => 'The overland replacement charges field must be numeric.',
            'ol_replacement_charges.required' => 'The overland replacement charges field is required.',
            'ol_tnb_charges.numeric' => 'The overland try and buy charges field must be numeric.',
            'ol_tnb_charges.required' => 'The overland try and buy charges field is required.',
            'ol_reverse_charges.numeric' => 'The overland reverse pickup charges field must be numeric.',
            'ol_reverse_charges.required' => 'The overland reverse pickup charges field is required.',
            'ol_cash_range_up.*.required_if' => 'The overland cash range up field is required.',
            'ol_cash_range_up.*.numeric' => 'The overland cash range up field must be numeric.',
            'ol_cash_range_down.*.required_if' => 'The overland cash range down field is required.',
            'ol_cash_range_down.*.numeric' => 'The overland cash range down field must be numeric.',
            'ol_cash_charges.*.required_if' => 'The overland cash charges field is required.',
            //'ol_cash_charges.*.numeric' => 'The overland cash charges field must be numeric or percentage.',
            'ol_ins_range_up.*.required_if' => 'The overland insurance range up field is required.',
            'ol_ins_range_up.*.numeric' => 'The overland insurance range up field must be numeric or percentage.',
            'ol_ins_range_down.*.required_if' => 'The overland insurance range down field is required.',
            'ol_ins_range_down.*.numeric' => 'The overland insurance range down field must be numeric or percentage.',
            'ol_ins_charges.*.required_if' => 'The overland insurance charges field is required.',
            //'ol_ins_charges.*.numeric' => 'The overland insurance charges field must be numeric or percentage.',
            'ol_return_local_charges.required_if' => 'The overland return local charges field is required.',
            'ol_return_local_charges.numeric' => 'The overland return local charges field must be numeric or percentage.',
            'ol_return_class_0_charges.*.required_if' => 'The overland class A return charges field is required.',
            'ol_return_class_1_charges.*.required_if' => 'The overland class B return charges field is required.',
            'ol_return_class_2_charges.*.required_if' => 'The overland class C return charges field is required.',
            'ol_return_class_3_charges.*.required_if' => 'The overland class D return charges field is required.',
            'overland_fuel_surcharge.required_if' => 'The overland return national charges field is required.',
            'overland_fuel_surcharge.numeric' => 'The overland return national charges field must be numeric or percentage.',

            'ol_discount_title.required_if' => 'The overland discount title field must be required',
            'ol_daterange.required_if' => 'The overland discount date range field must be required',
            'ol_discount_weight_rate.required_if' => 'The overland discount weight field must be required',
//            'ol_discount_weight_rate.numeric' => 'The overland discount weight field must be numeric',
            'ol_discount_cash_rate.required_if' => 'The overland discount cash field must be required',
//            'ol_discount_cash_rate.numeric' => 'The overland discount cash field must be numeric',
            'ol_discount_insurance_rate.required_if' => 'The overland discount insurance field must be required',
//            'ol_discount_insurance_rate.numeric' => 'The overland discount insurance field must be numeric',
            'ol_discount_return_rate.required_if' => 'The overland discount return field must be required',
//            'ol_discount_return_rate.numeric' => 'The overland discount return field must be numeric',
            'ol_discount_packaging_rate.required_if' => 'The overland discount packaging field must be required',
//            'ol_discount_packaging_rate.numeric' => 'The overland discount packaging field must be numeric',
            'ol_discount_title.required_with' => 'The overland discount title field is required',
            'ol_daterange.required_with' => 'The overland discount date field is required',
            //overland end and detain starts
            'detain_wa_range_up.*.required' => 'The detain range up field is required.',
            'detain_wa_range_up.*.numeric' => 'The detain range up field must be numeric or decimal.',
            'detain_wa_range_up.*.between' => 'The detain range up field must be between 0 to 999.99',
            'detain_wa_range_down.*.required' => 'The detain range down field is required.',
            'detain_wa_range_down.*.numeric' => 'The detain range down field must be numeric or decimal.',
            'detain_wa_range_down.*.between' => 'The detain range down field must be between 0 to 999.99',
            'detain_wa_spkg.*.numeric' => 'The detain KG Range field must be numeric.',
            'detain_wa_local_charges.*.required' => 'The detain local charges field is required.',
            'detain_wa_local_charges.*.numeric' => 'The detain local charges field must be numeric.',
            'detain_class_0_charges.*.required' => 'The detain class A charges field is required.',
            'detain_class_0_charges.*.numeric' => 'The detain class A charges field must be numeric.',
            'detain_class_1_charges.*.required' => 'The detain class B charges field is required.',
            'detain_class_2_charges.*.required' => 'The detain class C charges field is required.',
            'detain_class_3_charges.*.required' => 'The detain class D charges field is required.',
            'detain_replacement_charges.numeric' => 'The detain replacement charges field must be numeric.',
            'detain_replacement_charges.required' => 'The detain replacement charges field is required.',
            'detain_tnb_charges.numeric' => 'The detain try and buy charges field must be numeric.',
            'detain_tnb_charges.required' => 'The detain try and buy charges field is required.',
            'detain_reverse_charges.numeric' => 'The detain reverse pickup charges field must be numeric.',
            'detain_reverse_charges.required' => 'The detain reverse pickup charges field is required.',
            'detain_cash_range_up.*.required_if' => 'The detain cash range up field is required.',
            'detain_cash_range_up.*.numeric' => 'The detain cash range up field must be numeric.',
            'detain_cash_range_down.*.required_if' => 'The detain cash range down field is required.',
            'detain_cash_range_down.*.numeric' => 'The detain cash range down field must be numeric.',
            'detain_cash_charges.*.required_if' => 'The detain cash charges field is required.',
            //'detain_cash_charges.*.numeric' => 'The detain cash charges field must be numeric or percentage.',
            'detain_ins_range_up.*.required_if' => 'The detain insurance range up field is required.',
            'detain_ins_range_up.*.numeric' => 'The detain insurance range up field must be numeric or percentage.',
            'detain_ins_range_down.*.required_if' => 'The detain insurance range down field is required.',
            'detain_ins_range_down.*.numeric' => 'The detain insurance range down field must be numeric or percentage.',
            'detain_ins_charges.*.required_if' => 'The detain insurance charges field is required.',
            //'detain_ins_charges.*.numeric' => 'The detain insurance charges field must be numeric or percentage.',
            'detain_return_local_charges.required_if' => 'The detain return local charges field is required.',
            'detain_return_local_charges.numeric' => 'The detain return local charges field must be numeric or percentage.',
            'detain_return_class_0_charges.*.required_if' => 'The detain class A return charges field is required.',
            'detain_return_class_1_charges.*.required_if' => 'The detain class B return charges field is required.',
            'detain_return_class_2_charges.*.required_if' => 'The detain class C return charges field is required.',
            'detain_return_class_3_charges.*.required_if' => 'The detain class D return charges field is required.',
            'detain_fuel_surcharge.required_if' => 'The detain return national charges field is required.',
            'detain_fuel_surcharge.numeric' => 'The detain return national charges field must be numeric or percentage.',

            'detain_discount_title.required_if' => 'The detain discount title field must be required',
            'detain_daterange.required_if' => 'The detain discount date range field must be required',
            'detain_discount_weight_rate.required_if' => 'The detain discount weight field must be required',
//            'detain_discount_weight_rate.numeric' => 'The detain discount weight field must be numeric',
            'detain_discount_cash_rate.required_if' => 'The detain discount cash field must be required',
//            'detain_discount_cash_rate.numeric' => 'The detain discount cash field must be numeric',
            'detain_discount_insurance_rate.required_if' => 'The detain discount insurance field must be required',
//            'detain_discount_insurance_rate.numeric' => 'The detain discount insurance field must be numeric',
            'detain_discount_return_rate.required_if' => 'The detain discount return field must be required',
//            'detain_discount_return_rate.numeric' => 'The detain discount return field must be numeric',
            'detain_discount_packaging_rate.required_if' => 'The detain discount packaging field must be required',
//            'detain_discount_packaging_rate.numeric' => 'The detain discount packaging field must be numeric',
            'detain_discount_title.required_with' => 'The detain discount title field is required',
            'detain_daterange.required_with' => 'The detain discount date field is required',
            //detain ends and sameday starts
            'sameday_wa_range_up.*.required' => 'The sameday range up field is required.',
            'sameday_wa_range_up.*.numeric' => 'The sameday range up field must be numeric or decimal.',
            'sameday_wa_range_up.*.between' => 'The sameday range up field must be between 0 to 999.99',
            'sameday_wa_range_down.*.required' => 'The sameday range down field is required.',
            'sameday_wa_range_down.*.numeric' => 'The sameday range down field must be numeric or decimal.',
            'sameday_wa_range_down.*.between' => 'The sameday range down field must be between 0 to 999.99',
            'sameday_wa_spkg.*.numeric' => 'The sameday KG Range field must be numeric.',
            'sameday_wa_local_charges.*.required' => 'The sameday local charges field is required.',
            'sameday_wa_local_charges.*.numeric' => 'The sameday local charges field must be numeric.',
            'sameday_wa_class_0_charges.*.required' => 'The sameday class A charges field is required.',
            'sameday_wa_class_0_charges.*.numeric' => 'The sameday class A charges field must be numeric.',
            'sameday_replacement_charges.numeric' => 'The sameday replacement charges field must be numeric.',
            'sameday_replacement_charges.required' => 'The sameday replacement charges field is required.',
            'sameday_tnb_charges.numeric' => 'The sameday try and buy charges field must be numeric.',
            'sameday_tnb_charges.required' => 'The sameday try and buy charges field is required.',
            'sameday_reverse_charges.numeric' => 'The sameday reverse pickup charges field must be numeric.',
            'sameday_reverse_charges.required' => 'The sameday reverse pickup charges field is required.',
            'sameday_cash_range_up.*.required_if' => 'The sameday cash range up field is required.',
            'sameday_cash_range_up.*.numeric' => 'The sameday cash range up field must be numeric.',
            'sameday_cash_range_down.*.required_if' => 'The sameday cash range down field is required.',
            'sameday_cash_range_down.*.numeric' => 'The sameday cash range down field must be numeric.',
            'sameday_cash_charges.*.required_if' => 'The sameday cash charges field is required.',
            // 'sameday_cash_charges.*.numeric' => 'The sameday cash charges field must be numeric or percentage.',
            'sameday_ins_range_up.*.required_if' => 'The sameday insurance range up field is required.',
            'sameday_ins_range_up.*.numeric' => 'The sameday insurance range up field must be numeric or percentage.',
            'sameday_ins_range_down.*.required_if' => 'The sameday insurance range down field is required.',
            'sameday_ins_range_down.*.numeric' => 'The sameday insurance range down field must be numeric or percentage.',
            'sameday_ins_charges.*.required_if' => 'The sameday insurance charges field is required.',
            //'sameday_ins_charges.*.numeric' => 'The sameday insurance charges field must be numeric or percentage.',
            'sameday_return_local_charges.required_if' => 'The sameday return local charges field is required.',
            'sameday_return_local_charges.numeric' => 'The sameday return local charges field must be numeric or percentage.',
            'sameday_return_class_0_charges.*.required_if' => 'The sameday class A return charges field is required.',
            'sameday_return_class_1_charges.*.required_if' => 'The sameday class B return charges field is required.',
            'sameday_return_class_2_charges.*.required_if' => 'The sameday class C return charges field is required.',
            'sameday_return_class_3_charges.*.required_if' => 'The sameday class D return charges field is required.',
            'sameday_fuel_surcharge.required_if' => 'The sameday return national charges field is required.',
            'sameday_fuel_surcharge.numeric' => 'The sameday return national charges field must be numeric or percentage.',

            'sameday_discount_title.required_if' => 'The sameday discount title field must be required',
            'sameday_daterange.required_if' => 'The sameday discount date range field must be required',
            'sameday_discount_weight_rate.required_if' => 'The sameday discount weight field must be required',
//            'sameday_discount_weight_rate.numeric' => 'The sameday discount weight field must be numeric',
            'sameday_discount_cash_rate.required_if' => 'The sameday discount cash field must be required',
//            'sameday_discount_cash_rate.numeric' => 'The sameday discount cash field must be numeric',
            'sameday_discount_insurance_rate.required_if' => 'The sameday discount insurance field must be required',
//            'sameday_discount_insurance_rate.numeric' => 'The sameday discount insurance field must be numeric',
            'sameday_discount_return_rate.required_if' => 'The sameday discount return field must be required',
//            'sameday_discount_return_rate.numeric' => 'The sameday discount return field must be numeric',
            'sameday_discount_packaging_rate.required_if' => 'The sameday discount packaging field must be required',
//            'sameday_discount_packaging_rate.numeric' => 'The sameday discount packaging field must be numeric',
            'sameday_discount_title.required_with' => 'The sameday discount title field is required',
            'sameday_daterange.required_with' => 'The sameday discount date field is required',
            //sameday ends

            //warehouse starts
            'invoicing_cycle.*.required' => 'Invoicing Cycle field is required.',
            'invoicing_date.*.required' => 'Invoicing Cycle field is required.',
            'invoicing_cycle.*.numeric' => 'Invoicing Cycle field must be numeric.',
            'invoicing_date.*.numeric' => 'Invoicing Cycle field must be numeric.',
            'ppc_charges.required' => 'Per product charges field id required',
            'psf_charges.required' => 'Per square foot charges field id required',
            'storage_type.*.required_if' => 'Storage type field is required.',
            'storage_type_charges.*.required_if' => 'Storage type charges field is required',
            'storage_type.*.numeric' => 'Storage type field must be numeric.',
            'storage_type_charges.*.numeric' => 'Storage type charges field must be numeric',
            'packing_type.*.required' => 'Packing type field is required',
            'packing_charges.*.required' => 'Packing charges field is required',
            'packing_charges.*.numeric' => 'Packing charges field must be numeric',
            'labelling_charges.required' => 'Labelling charges field is required',
            'labelling_charges.numeric' => 'Labelling charges field must be numeric',

            'discount_on_destination.required_if' => 'Rush Destination Field is required if discount weight (destination-wise) toggle is on',
            'discount_ol_destination.required_if' => 'Saver Plus Destination Field is required if discount weight (destination-wise) toggle is on',
            'discount_d_destination.required_if' => 'Detain Destination Field is required if discount weight (destination-wise) toggle is on',
            'discount_sd_destination.required_if' => 'Same Day Destination Field is required if discount weight (destination-wise) toggle is on',

            'on_cod_discount_per' => 'Zero Cod Discount percentage is required',
            'on_return_discount_per' => 'Return Discount percentage is required',

            'ol_cod_discount_per' => 'Zero Cod Discount percentage is required',
            'ol_return_discount_per' => 'Return Discount percentage is required',

            'detain_cod_discount_per' => 'Zero Cod Discount percentage is required',
            'detain_return_discount_per' => 'Return Discount percentage is required',

            'sameday_cod_discount_per' => 'Zero Cod Discount percentage is required',
            'sameday_return_discount_per' => 'Return Discount percentage is required',

        ];
        $validations = array();
        $on_validations = array();
        $ol_validations = array();
        $detain_validations = array();
        $sameday_validations = array();
        $sms_validations = array();

        $shipper_id = $id;
        if($request->has('sms_main_switch') && $request->sms_main_switch == 'on') {
            $sms_validations = [
                'sms_charges' => 'required|gt:0'
            ];
        }
        if ($request->has('on_main_switch') && $request->on_main_switch == 'on') {
            $on_validations = [
                'on_wa_range_up.*' => 'required|numeric|between:0,100000',
                'on_wa_range_down.*' => 'required|numeric|between:0,100000',
                'on_wa_local_charges.*' => 'required|numeric',
                'on_class_0_charges.*' => 'required|numeric',
                'on_class_1_charges.*' => 'required',
                'on_class_2_charges.*' => 'required',
                'on_class_3_charges.*' => 'required',
                'on_wa_spkg.*' => 'numeric',
                'on_replacement_charges' => 'required|numeric',
                'on_tnb_charges' => 'required|numeric',
                'on_reverse_charges' => 'required|numeric',
                'on_cash_range_up.*' => 'required_if:on_cash_handling_switch,==,on|numeric',
                'on_cash_range_down.*' => 'required_if:on_cash_handling_switch,==,on|numeric',
                'on_cash_charges.*' => 'required_if:on_cash_handling_switch,==,on',
                'on_ins_range_up.*' => 'required_if:on_insurance_charges_switch,==,on|numeric',
                'on_ins_range_down.*' => 'required_if:on_insurance_charges_switch,==,on|numeric',
                'on_ins_charges.*' => 'required_if:on_insurance_charges_switch,==,on',
                'on_return_local_charges.*' => 'required_if:on_return_switch,==,on|numeric',
                'on_return_class_0_charges.*' => 'required_if:on_return_switch,==,on|numeric',
                'on_return_class_1_charges.*' => 'required_if:on_return_switch,==,on|numeric',
                'on_return_class_2_charges.*' => 'required_if:on_return_switch,==,on|numeric',
                'on_return_class_3_charges.*' => 'required_if:on_return_switch,==,on|numeric',
                'overnight_fuel_surcharge' => 'required_if:overnight_fuel_switch,==,on|numeric',

                'on_discount_title' => 'required_with:on_discount_weight_rate,on_discount_cash_rate,on_discount_insurance_rate,on_discount_return_rate,on_discount_packaging_rate',
                'on_daterange' => 'required_with:on_discount_weight_rate,on_discount_cash_rate,on_discount_insurance_rate,on_discount_return_rate,on_discount_packaging_rate',
                'on_discount_weight_rate' => 'required_if:on_discount_weight_switch,==,on',
                'on_discount_cash_rate' => 'required_if:on_discount_cash_switch,==,on',
                'on_discount_insurance_rate' => 'required_if:on_discount_insurance_switch,==,on',
                'on_discount_return_rate' => 'required_if:on_discount_return_switch,==,on',
                'on_discount_packaging_rate' => 'required_if:on_discount_packaging_switch,==,on',
                'overnight_open_box'=>'required_if:on_open_box_switch,==,on',
                'discount_on_destination' => 'required_if:on_discount_destination_wise_weight_switch,==,on',
                'on_cod_discount_per' => 'required_if:on_zero_cod_switch,==,on',
                'on_return_discount_per' => 'required_if:on_return_discount_switch,==,on',
            ];
        }
        //overland
        if ($request->has('ol_main_switch') && $request->ol_main_switch == 'on') {
            $ol_validations = [
                'ol_wa_range_up.*' => 'required|numeric|between:0,100000',
                'ol_wa_range_down.*' => 'required|numeric|between:0,100000',
                'ol_wa_local_charges.*' => 'required|numeric',
                'ol_class_0_charges.*' => 'required|numeric',
                'ol_class_1_charges.*' => 'required',
                'ol_class_2_charges.*' => 'required',
                'ol_class_3_charges.*' => 'required',
                'ol_wa_spkg.*' => 'numeric',
                'ol_replacement_charges' => 'required|numeric',
                'ol_tnb_charges' => 'required|numeric',
                'ol_reverse_charges' => 'required|numeric',
                'ol_cash_range_up.*' => 'required_if:ol_cash_handling_switch,==,on|numeric',
                'ol_cash_range_down.*' => 'required_if:ol_cash_handling_switch,==,on|numeric',
                'ol_cash_charges.*' => 'required_if:ol_cash_handling_switch,==,on',
                'ol_ins_range_up.*' => 'required_if:ol_insurance_charges_switch,==,on|numeric',
                'ol_ins_range_down.*' => 'required_if:ol_insurance_charges_switch,==,on|numeric',
                'ol_ins_charges.*' => 'required_if:ol_insurance_charges_switch,==,on',
                'ol_return_local_charges.*' => 'required_if:ol_return_switch,==,on|numeric',
                'ol_return_class_0_charges.*' => 'required_if:ol_return_switch,==,on|numeric',
                'ol_return_class_1_charges.*' => 'required_if:ol_return_switch,==,on|numeric',
                'ol_return_class_2_charges.*' => 'required_if:ol_return_switch,==,on|numeric',
                'ol_return_class_3_charges.*' => 'required_if:ol_return_switch,==,on|numeric',
                'overland_fuel_surcharge' => 'required_if:overland_fuel_switch,==,on|numeric',

                'ol_discount_title' => 'required_with:ol_discount_weight_rate,ol_discount_cash_rate,ol_discount_insurance_rate,ol_discount_return_rate,ol_discount_packaging_rate',
                'ol_daterange' => 'required_with:ol_discount_weight_rate,ol_discount_cash_rate,ol_discount_insurance_rate,ol_discount_return_rate,ol_discount_packaging_rate',
                'ol_discount_weight_rate' => 'required_if:ol_discount_weight_switch,==,on',
                'ol_discount_cash_rate' => 'required_if:ol_discount_cash_switch,==,on',
                'ol_discount_insurance_rate' => 'required_if:ol_discount_insurance_switch,==,on',
                'ol_discount_return_rate' => 'required_if:ol_discount_return_switch,==,on',
                'ol_discount_packaging_rate' => 'required_if:ol_discount_packaging_switch,==,on',
                'overland_open_box'=>'required_if:ol_open_box_switch,==,on',
                'discount_ol_destination' => 'required_if:ol_discount_destination_wise_weight_switch,==,on',
                'ol_cod_discount_per' => 'required_if:ol_zero_cod_switch,==,on',
                'ol_return_discount_per' => 'required_if:ol_return_discount_switch,==,on',
            ];
        }
        //overland
        if ($request->has('detain_main_switch') && $request->detain_main_switch == 'on') {
            $detain_validations = [
                'detain_wa_range_up.*' => 'required|numeric|between:0,100000',
                'detain_wa_range_down.*' => 'required|numeric|between:0,100000',
                'detain_wa_local_charges.*' => 'required|numeric',
                'detain_class_0_charges.*' => 'required|numeric',
                'detain_class_1_charges.*' => 'required',
                'detain_class_2_charges.*' => 'required',
                'detain_class_3_charges.*' => 'required',
                'detain_wa_spkg.*' => 'numeric',
                'detain_replacement_charges' => 'required|numeric',
                'detain_tnb_charges' => 'required|numeric',
                'detain_reverse_charges' => 'required|numeric',
                'detain_cash_range_up.*' => 'required_if:detain_cash_handling_switch,==,on|numeric',
                'detain_cash_range_down.*' => 'required_if:detain_cash_handling_switch,==,on|numeric',
                'detain_cash_charges.*' => 'required_if:detain_cash_handling_switch,==,on',
                'detain_ins_range_up.*' => 'required_if:detain_insurance_charges_switch,==,on|numeric',
                'detain_ins_range_down.*' => 'required_if:detain_insurance_charges_switch,==,on|numeric',
                'detain_ins_charges.*' => 'required_if:detain_insurance_charges_switch,==,on',
                'detain_return_local_charges.*' => 'required_if:detain_return_switch,==,on|numeric',
                'detain_return_class_0_charges.*' => 'required_if:detain_return_switch,==,on|numeric',
                'detain_return_class_1_charges.*' => 'required_if:detain_return_switch,==,on|numeric',
                'detain_return_class_2_charges.*' => 'required_if:detain_return_switch,==,on|numeric',
                'detain_return_class_3_charges.*' => 'required_if:detain_return_switch,==,on|numeric',
                'detain_fuel_surcharge' => 'required_if:detain_fuel_switch,==,on|numeric',

                'detain_discount_title' => 'required_with:detain_discount_weight_rate,detain_discount_cash_rate,detain_discount_insurance_rate,detain_discount_return_rate,detain_discount_packaging_rate',
                'detain_daterange' => 'required_with:detain_discount_weight_rate,detain_discount_cash_rate,detain_discount_insurance_rate,detain_discount_return_rate,detain_discount_packaging_rate',
                'detain_discount_weight_rate' => 'required_if:detain_discount_weight_switch,==,on',
                'detain_discount_cash_rate' => 'required_if:detain_discount_cash_switch,==,on',
                'detain_discount_insurance_rate' => 'required_if:detain_discount_insurance_switch,==,on',
                'detain_discount_return_rate' => 'required_if:detain_discount_return_switch,==,on',
                'detain_discount_packaging_rate' => 'required_if:detain_discount_packaging_switch,==,on',
                'detain_open_box'=>'required_if:detain_open_box_switch,==,on',
                'discount_d_destination' => 'required_if:d_discount_destination_wise_weight_switch,==,on',
                'detain_cod_discount_per' => 'required_if:detain_zero_cod_switch,==,on',
                'detain_return_discount_per' => 'required_if:detain_return_discount_switch,==,on',
            ];
        }
        //sameday
        if ($request->has('sameday_main_switch') && $request->sameday_main_switch == 'on') {
            $sameday_validations = [
                'sameday_wa_range_up.*' => 'required|numeric|between:0,100000',
                'sameday_wa_range_down.*' => 'required|numeric|between:0,100000',
                'sameday_wa_local_charges.*' => 'required|numeric',
                'sameday_class_0_charges.*' => 'required|numeric',
                'sameday_wa_spkg.*' => 'numeric',
                'sameday_replacement_charges' => 'required|numeric',
                'sameday_tnb_charges' => 'required|numeric',
                'sameday_reverse_charges' => 'required|numeric',
                'sameday_cash_range_up.*' => 'required_if:sameday_cash_handling_switch,==,on|numeric',
                'sameday_cash_range_down.*' => 'required_if:sameday_cash_handling_switch,==,on|numeric',
                'sameday_cash_charges.*' => 'required_if:sameday_cash_handling_switch,==,on',
                'sameday_ins_range_up.*' => 'required_if:sameday_insurance_charges_switch,==,on|numeric',
                'sameday_ins_range_down.*' => 'required_if:sameday_insurance_charges_switch,==,on|numeric',
                'sameday_ins_charges.*' => 'required_if:sameday_insurance_charges_switch,==,on',
                'sameday_return_local_charges.*' => 'required_if:sameday_return_switch,==,on|numeric',
                'sameday_return_class_0_charges.*' => 'required_if:sameday_return_switch,==,on|numeric',
                'sameday_return_class_1_charges.*' => 'required_if:sameday_return_switch,==,on|numeric',
                'sameday_return_class_2charges.*' => 'required_if:sameday_return_switch,==,on|numeric',
                'sameday_return_class_3_charges.*' => 'required_if:sameday_return_switch,==,on|numeric',
                'sameday_fuel_surcharge' => 'required_if:sameday_fuel_switch,==,on|numeric',

                'sameday_discount_title' => 'required_with:sameday_discount_weight_rate,sameday_discount_cash_rate,sameday_discount_insurance_rate,sameday_discount_return_rate,sameday_discount_packaging_rate',
                'sameday_daterange' => 'required_with:sameday_discount_weight_rate,sameday_discount_cash_rate,sameday_discount_insurance_rate,sameday_discount_return_rate,sameday_discount_packaging_rate',
                'sameday_discount_weight_rate' => 'required_if:sameday_discount_weight_switch,==,on',
                'sameday_discount_cash_rate' => 'required_if:sameday_discount_cash_switch,==,on',
                'sameday_discount_insurance_rate' => 'required_if:sameday_discount_insurance_switch,==,on',
                'sameday_discount_return_rate' => 'required_if:sameday_discount_return_switch,==,on',
                'sameday_discount_packaging_rate' => 'required_if:sameday_discount_packaging_switch,==,on',
                'sd_open_box'=>'required_if:sd_open_box_switch,==,on',
                'discount_sd_destination' => 'required_if:sd_discount_destination_wise_weight_switch,==,on',
                'sameday_cod_discount_per' => 'required_if:sameday_zero_cod_switch,==,on',
                'sameday_return_discount_per' => 'required_if:sameday_return_discount_switch,==,on',
            ];
        }

        if ($request->has('warehouse_main_switch') && $request->warehouse_main_switch == 'on') {
            $warehouse_validations = [
                'invoicing_cycle' => 'required|numeric',
                'invoicing_date.*' => 'required_if:invoicing_cycle,1,3',
                'ppc_charges' => 'required_if:ppc_switch,==,on',
                'psf_charges' => 'required_if:psf_switch,==,on',
                'storage_type.*' => 'required_if:storage_charges_switch,==,on',
                'storage_type_charges.*' => 'required_if:storage_charges_switch,==,on|numeric',
                'packing_type.*' => 'required_if:packing_charges_switch,==,on',
                'packing_charges.*' => 'required_if:packing_charges_switch,==,on|numeric',
                'labelling_charges.*' => 'required_if:labelling_charges_switch,==,on|numeric',
            ];
        }

        $validations = array_merge($on_validations, $ol_validations, $detain_validations, $sameday_validations, $sms_validations);

        $validate = Validator::make($request->all(), $validations, $messages);

        if ($validate->fails()) {
            return redirect()->back()
                ->withErrors($validate)
                ->withInput();
        }

        if($request->has('sms_main_switch') && $request->sms_main_switch == 'on') {
            User::where('id', $id)->update([
                'sms_charges' => $request->sms_charges,
                'sms_charges_status' => ($request->has('sms_main_switch')) ? 1 : 0,
            ]);
        }
        if ($request->has('on_main_switch') && $request->on_main_switch == 'on') {
            if ($request->has('on_default') && $request->on_default == 'on') {
                $default_shipping_mode = User::where('id', $id)->update([
                    'default_shipping_mode' => 1
                ]);
            }
            $ONRateAlready = RateStatus::where('user_id', $id)->where('shipping_mode_id', 1)->get();

            if ($ONRateAlready->isEmpty()) {

                if ($request->has('on_origin_hubs')) {
                    foreach ($request->on_origin_hubs as $origin_id) {
                        $rate_origin_hub = new RateOriginHub();
                        $rate_origin_hub->user_id = $id;
                        $rate_origin_hub->shipping_mode_id = 1;
                        $rate_origin_hub->city_id = $origin_id;
                        $rate_origin_hub->save();
                    }
                }
                if ($request->has('on_destination_hubs')) {
                    foreach ($request->on_destination_hubs as $destination_id) {
                        $rate_destination_hub = new RateDestinationHub();
                        $rate_destination_hub->user_id = $id;
                        $rate_destination_hub->shipping_mode_id = 1;
                        $rate_destination_hub->city_id = $destination_id;
                        $rate_destination_hub->save();
                    }
                }
                RateStatus::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 1,
                    'status' => ($request->has('on_main_switch')) ? 1 : 0,
                    'cash_handling_charges' => ($request->has('on_cash_handling_switch')) ? 1 : 0,
                    'insurance_charges' => ($request->has('on_insurance_charges_switch')) ? 1 : 0,
                    'return_charges' => ($request->has('on_return_switch')) ? 1 : 0,
                    'fuel_charges' => ($request->has('overnight_fuel_switch')) ? 1 : 0,
                    'zero_cod_discount' => ($request->has('on_zero_cod_switch')) ? 1 : 0,
                    'return_discount' => ($request->has('on_return_discount_switch')) ? 1 : 0,
                ]);
                $wa_switch = array();
                $wa_spkg = array();
                foreach ($request->on_wa_range_up as $index => $on_wa_range_up) {
                    if ($request->has('on_wa_switch')) {
                        if (array_key_exists($index, $request->on_wa_switch)) {
                            $wa_switch[$index] = 1;
                        } else {
                            $wa_switch[$index] = 0;
                        };
                    } else {
                        $wa_switch[$index] = 0;
                    }
                    if ($request->has('on_wa_spkg')) {
                        if (array_key_exists($index, $request->on_wa_spkg)) {
                            $wa_spkg[$index] = $request->on_wa_spkg[$index];
                        } else {
                            $wa_spkg[$index] = 0.5;
                        };
                    } else {
                        $wa_spkg[$index] = 0.5;
                    }
                    WeightCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'range_up' => $request->on_wa_range_up[$index],
                        'range_down' => $request->on_wa_range_down[$index],
                        'weight_addition' => $wa_switch[$index],
                        'spkg' => $wa_spkg[$index],
                        'local_or_6hr' => $request->on_wa_local_charges[$index],
                        'national_charges_class_0' => $request->on_class_0_charges[$index],
                        'national_charges_class_1' => $request->on_class_1_charges[$index],
                        'national_charges_class_2' => $request->on_class_2_charges[$index],
                        'national_charges_class_3' => $request->on_class_3_charges[$index]
                    ]);

                }

                //Replacement and Try and Buy charges
                BookingTypeCharges::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 1,
                    'replacement_charges' => $request->on_replacement_charges,
                    'try_and_buy_charges' => $request->on_tnb_charges,
                    'reverse_pickup_charges' => $request->on_reverse_charges
                ]);
                //Cash handling Charges
                if ($request->has('on_cash_handling_switch') && $request->on_cash_handling_switch == 'on') {
                    foreach ($request->on_cash_range_up as $ind => $on_cash_range_up) {
                        CashHandlingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'range_up' => $request->on_cash_range_up[$ind],
                            'range_down' => $request->on_cash_range_down[$ind],
                            'charges' => $request->on_cash_charges[$ind]
                        ]);
                    }
                }
                //insurance charges
                if ($request->has('on_insurance_charges_switch') && $request->on_insurance_charges_switch == 'on') {
                    foreach ($request->on_ins_range_up as $insurance => $on_ins_range_up) {
                        InsuranceCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'range_up' => $request->on_ins_range_up[$insurance],
                            'range_down' => $request->on_ins_range_down[$insurance],
                            'charges' => $request->on_ins_charges[$insurance]
                        ]);
                    }
                }
                //Return Charges
                if ($request->has('on_return_switch') && $request->on_return_switch == 'on') {
                    ReturnCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'local' => $request->on_return_local_charges,
                        'national_charges_class_0' => $request->on_return_class_0_charges,
                        'national_charges_class_1' => $request->on_return_class_1_charges,
                        'national_charges_class_2' => $request->on_return_class_2_charges,
                        'national_charges_class_3' => $request->on_return_class_3_charges
                    ]);
                }
                //Return Charges
                if ($request->has('overnight_fuel_switch') && $request->overnight_fuel_switch == 'on') {
                    FuelSurcharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'fuel_surcharge' => $request->overnight_fuel_surcharge
                    ]);
                }

                $discount_cash = null;
                $discount_weight = null;
                $discount_insurance = null;
                $discount_return = null;
                $discount_packaging = null;

                if ($request->has('on_discount_weight_switch') && $request->on_discount_weight_switch == 'on') {
                    $discount_weight = $request->on_discount_weight_rate != null ? $request->on_discount_weight_rate : null;
//                    $discount_weight = $request->on_discount_weight_rate;
                }
                if ($request->has('on_discount_cash_switch') && $request->on_discount_cash_switch == 'on') {
                    $discount_cash = $request->on_discount_cash_rate != null ? $request->on_discount_cash_rate : null;
                }
                if ($request->has('on_discount_insurance_switch') && $request->on_discount_insurance_switch == 'on') {
                    $discount_insurance = $request->on_discount_insurance_rate != null ? $request->on_discount_insurance_rate : null;
                }
                if ($request->has('on_discount_return_switch') && $request->on_discount_return_switch == 'on') {
                    $discount_return = $request->on_discount_return_rate != null ? $request->on_discount_insurance_rate : null;
                }
                if ($request->has('on_discount_packaging_switch') && $request->on_discount_packaging_switch == 'on') {
                    $discount_packaging = $request->on_discount_packaging_rate != null ? $request->on_discount_packaging_rate : null;
                }
                if ($discount_weight != null || $discount_cash != null || $discount_insurance != null || $discount_return != null || $discount_packaging != null) {


                    $date_str = $request->on_daterange;
                    $date_sep = explode(' - ', $date_str);
                    $date_to = explode('/', $date_sep[0]);
                    $date_from = explode('/', $date_sep[1]);
                    $to = Carbon::create($date_to[2], $date_to[0], $date_to[1], 0, 0, 0, 'UTC')->toDateTimeString();
                    $from = Carbon::create($date_from[2], $date_from[0], $date_from[1], 0, 0, 0, 'UTC')->toDateTimeString();


                    DiscountCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'title' => $request->on_discount_title,
                        'weight' => $discount_weight,
                        'cash' => $discount_cash,
                        'insurance' => $discount_insurance,
                        'return' => $discount_return,
                        'packaging' => $discount_packaging,
                        'to' => $to,
                        'from' => $from,
                        'added_by' => Auth::id()
                    ]);
                }

                if($request->has('on_discount_destination_wise_weight_switch') && $request->on_discount_destination_wise_weight_switch == "on")
                {
                    foreach($request->discount_on_destination as $parent_key => $destination)
                    {
                        $discount_wa_switch = array();
                        $discount_wa_spkg = array();
                        foreach($request->discount_on_wa_range_up[$parent_key] as $child_key => $value)
                        {
                            if ($request->has('discount_on_wa_switch.'.$parent_key)) {
                                if (array_key_exists($child_key, $request->discount_on_wa_switch[$parent_key])) {
                                    $discount_wa_switch[$child_key] = 1;
                                } else {
                                    $discount_wa_switch[$child_key] = 0;
                                };
                            } else {
                                $discount_wa_switch[$child_key] = 0;
                            }
                            if ($request->has('discount_on_wa_spkg.'.$parent_key)) {
                                if (array_key_exists($child_key, $request->discount_on_wa_spkg[$parent_key])) {
                                    $discount_wa_spkg[$child_key] = $request->discount_on_wa_spkg[$parent_key][$child_key];
                                } else {
                                    $discount_wa_spkg[$child_key] = 0.5;
                                };
                            } else {
                                $discount_wa_spkg[$child_key] = 0.5;
                            }
                            DiscountWeightCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'destination_id' => $destination,
                                'range_up' => $request->discount_on_wa_range_up[$parent_key][$child_key],
                                'range_down' => $request->discount_on_wa_range_down[$parent_key][$child_key],
                                'weight_addition' => $discount_wa_switch[$child_key],
                                'spkg' => $discount_wa_spkg[$child_key],
                                'local_or_6hr' => $request->discount_on_wa_local_charges[$parent_key][$child_key],
                            ]);
                        }
                    }
                }

            }
            if ($request->has('on_dws_weight')) {
                if ($request->on_dws_weight == 2) {
                    DwsWeightChargesController::add($id, 1, 2, Auth::id());

                } else {
                    DwsWeightChargesController::add($id, 1, 1, Auth::id());
                }

            }


        }
        //Overland
        if ($request->has('ol_main_switch') && $request->ol_main_switch == 'on') {
            if ($request->has('ol_default') && $request->ol_default == 'on') {
                $default_shipping_mode = User::where('id', $id)->update([
                    'default_shipping_mode' => 2
                ]);
            }

            $OLRatePresent = RateStatus::where('user_id', $id)->where('shipping_mode_id', 2)->get();

            if ($OLRatePresent->isEmpty()) {

                if ($request->has('ol_origin_hubs')) {
                    foreach ($request->ol_origin_hubs as $origin_id) {
                        $rate_origin_hub = new RateOriginHub();
                        $rate_origin_hub->user_id = $id;
                        $rate_origin_hub->shipping_mode_id = 2;
                        $rate_origin_hub->city_id = $origin_id;
                        $rate_origin_hub->save();
                    }
                }
                if ($request->has('ol_destination_hubs')) {
                    foreach ($request->ol_destination_hubs as $destination_id) {
                        $rate_destination_hub = new RateDestinationHub();
                        $rate_destination_hub->user_id = $id;
                        $rate_destination_hub->shipping_mode_id = 2;
                        $rate_destination_hub->city_id = $destination_id;
                        $rate_destination_hub->save();
                    }
                }

                RateStatus::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 2,
                    'status' => ($request->has('ol_main_switch')) ? 1 : 0,
                    'cash_handling_charges' => ($request->has('ol_cash_handling_switch')) ? 1 : 0,
                    'insurance_charges' => ($request->has('ol_insurance_charges_switch')) ? 1 : 0,
                    'return_charges' => ($request->has('ol_return_switch')) ? 1 : 0,
                    'fuel_charges' => ($request->has('overland_fuel_switch')) ? 1 : 0,
                    'zero_cod_discount' => ($request->has('ol_zero_cod_switch')) ? 1 : 0,
                    'return_discount' => ($request->has('ol_return_discount_switch')) ? 1 : 0,
                ]);
                $wa_switch_overland = array();
                $wa_spkg_overland = array();
                $standard_weight = StandardWeightCharge::where('shipping_mode_id', 2)->pluck('kg_range')->toArray();
                foreach ($request->ol_wa_range_up as $index => $ol_wa_range_up) {
                    if ($request->has('ol_wa_switch')) {
                        if (array_key_exists($index, $request->ol_wa_switch)) {
                            $wa_switch_overland[$index] = 1;
                        } else {
                            $wa_switch_overland[$index] = 0;
                        };
                    } else {
                        $wa_switch_overland[$index] = 0;
                    }
                    if ($request->has('ol_wa_spkg')) {
                        if (array_key_exists($index, $request->ol_wa_spkg)) {
                            $wa_spkg_overland[$index] = $request->ol_wa_spkg[$index];
                        } else {
                            $wa_spkg_overland[$index] = isset($standard_weight[$index]) ? $standard_weight[$index] : 0;
                        };
                    } else {
                        $wa_spkg_overland[$index] = isset($standard_weight[$index]) ? $standard_weight[$index] : 0;
                    }
                    WeightCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'range_up' => $request->ol_wa_range_up[$index],
                        'range_down' => $request->ol_wa_range_down[$index],
                        'weight_addition' => $wa_switch_overland[$index],
                        'spkg' => $wa_spkg_overland[$index],
                        'local_or_6hr' => $request->ol_wa_local_charges[$index],
                        'national_charges_class_0' => $request->ol_class_0_charges[$index],
                        'national_charges_class_1' => $request->ol_class_1_charges[$index],
                        'national_charges_class_2' => $request->ol_class_2_charges[$index],
                        'national_charges_class_3' => $request->ol_class_3_charges[$index]
                    ]);
                }


                //Replacement and Try and Buy charges
                BookingTypeCharges::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 2,
                    'replacement_charges' => $request->ol_replacement_charges,
                    'try_and_buy_charges' => $request->ol_tnb_charges,
                    'reverse_pickup_charges' => $request->ol_reverse_charges
                ]);
                //Cash handling Charges
                if ($request->has('ol_cash_handling_switch') && $request->ol_cash_handling_switch == 'on') {
                    foreach ($request->ol_cash_range_up as $ind => $ol_cash_range_up) {
                        CashHandlingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'range_up' => $request->ol_cash_range_up[$ind],
                            'range_down' => $request->ol_cash_range_down[$ind],
                            'charges' => $request->ol_cash_charges[$ind]
                        ]);
                    }
                }
                //insurance charges
                if ($request->has('ol_insurance_charges_switch') && $request->ol_insurance_charges_switch == 'on') {
                    foreach ($request->ol_ins_range_up as $insurance => $ol_ins_range_up) {
                        InsuranceCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'range_up' => $request->ol_ins_range_up[$insurance],
                            'range_down' => $request->ol_ins_range_down[$insurance],
                            'charges' => $request->ol_ins_charges[$insurance]
                        ]);
                    }
                }
                //Return Charges
                if ($request->has('ol_return_switch') && $request->ol_return_switch == 'on') {
                    ReturnCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'local' => $request->ol_return_local_charges,
                        'national_charges_class_0' => $request->ol_return_class_0_charges,
                        'national_charges_class_1' => $request->ol_return_class_1_charges,
                        'national_charges_class_2' => $request->ol_return_class_2_charges,
                        'national_charges_class_3' => $request->ol_return_class_3_charges
                    ]);
                }
                if ($request->has('overland_fuel_switch') && $request->overland_fuel_switch == 'on') {
                    FuelSurcharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'fuel_surcharge' => $request->overland_fuel_surcharge
                    ]);
                }

                $discount_cash = null;
                $discount_weight = null;
                $discount_insurance = null;
                $discount_return = null;
                $discount_packaging = null;

                if ($request->has('ol_discount_weight_switch') && $request->ol_discount_weight_switch == 'on') {
                    $discount_weight = $request->ol_discount_weight_rate != null ? $request->ol_discount_weight_rate : null;
//                    $discount_weight = $request->ol_discount_weight_rate;
                }
                if ($request->has('ol_discount_cash_switch') && $request->ol_discount_cash_switch == 'on') {
                    $discount_cash = $request->ol_discount_cash_rate != null ? $request->ol_discount_cash_rate : null;
                }
                if ($request->has('ol_discount_insurance_switch') && $request->ol_discount_insurance_switch == 'on') {
                    $discount_insurance = $request->ol_discount_insurance_rate != null ? $request->ol_discount_insurance_rate : null;
                }
                if ($request->has('ol_discount_return_switch') && $request->ol_discount_return_switch == 'on') {
                    $discount_return = $request->ol_discount_return_rate != null ? $request->ol_discount_insurance_rate : null;
                }
                if ($request->has('ol_discount_packaging_switch') && $request->ol_discount_packaging_switch == 'on') {
                    $discount_packaging = $request->ol_discount_packaging_rate != null ? $request->ol_discount_packaging_rate : null;
                }
                if ($discount_weight != null || $discount_cash != null || $discount_insurance != null || $discount_return != null || $discount_packaging != null) {


                    $date_str = $request->ol_daterange;
                    $date_sep = explode(' - ', $date_str);
                    $date_to = explode('/', $date_sep[0]);
                    $date_from = explode('/', $date_sep[1]);
                    $to = Carbon::create($date_to[2], $date_to[0], $date_to[1], 0, 0, 0, 'UTC')->toDateTimeString();
                    $from = Carbon::create($date_from[2], $date_from[0], $date_from[1], 0, 0, 0, 'UTC')->toDateTimeString();


                    DiscountCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'title' => $request->ol_discount_title,
                        'weight' => $discount_weight,
                        'cash' => $discount_cash,
                        'insurance' => $discount_insurance,
                        'return' => $discount_return,
                        'packaging' => $discount_packaging,
                        'to' => $to,
                        'from' => $from,
                        'added_by' => Auth::id()
                    ]);
                }

                if($request->has('ol_discount_destination_wise_weight_switch') && $request->ol_discount_destination_wise_weight_switch == "on")
                {
                    foreach($request->discount_ol_destination as $parent_key => $destination)
                    {
                        $discount_wa_switch = array();
                        $discount_wa_spkg = array();
                        foreach($request->discount_ol_wa_range_up[$parent_key] as $child_key => $value)
                        {
                            if ($request->has('discount_ol_wa_switch.'.$parent_key)) {
                                if (array_key_exists($child_key, $request->discount_ol_wa_switch[$parent_key])) {
                                    $discount_wa_switch[$child_key] = 1;
                                } else {
                                    $discount_wa_switch[$child_key] = 0;
                                };
                            } else {
                                $discount_wa_switch[$child_key] = 0;
                            }
                            if ($request->has('discount_ol_wa_spkg.'.$parent_key)) {
                                if (array_key_exists($child_key, $request->discount_ol_wa_spkg[$parent_key])) {
                                    $discount_wa_spkg[$child_key] = $request->discount_ol_wa_spkg[$parent_key][$child_key];
                                } else {
                                    $discount_wa_spkg[$child_key] = 0.5;
                                };
                            } else {
                                $discount_wa_spkg[$child_key] = 0.5;
                            }
                            DiscountWeightCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'destination_id' => $destination,
                                'range_up' => $request->discount_ol_wa_range_up[$parent_key][$child_key],
                                'range_down' => $request->discount_ol_wa_range_down[$parent_key][$child_key],
                                'weight_addition' => $discount_wa_switch[$child_key],
                                'spkg' => $discount_wa_spkg[$child_key],
                                'local_or_6hr' => $request->discount_ol_wa_local_charges[$parent_key][$child_key],
                            ]);
                        }
                    }
                }


            }
            if ($request->has('ol_dws_weight')) {
                if ($request->ol_dws_weight == 2) {
                    DwsWeightChargesController::add($id, 2, 2, Auth::id());

                } else {
                    DwsWeightChargesController::add($id, 2, 1, Auth::id());

                }

            }

        }
        //Detain
        if ($request->has('detain_main_switch') && $request->detain_main_switch == 'on') {
            if ($request->has('det_default') && $request->det_default == 'on') {
                $default_shipping_mode = User::where('id', $id)->update([
                    'default_shipping_mode' => 3
                ]);
            }

            $DetainRatePresent = RateStatus::where('user_id', $id)->where('shipping_mode_id', 3)->get();

            if ($DetainRatePresent->isEmpty()) {

                if ($request->has('detain_origin_hubs')) {
                    foreach ($request->detain_origin_hubs as $origin_id) {
                        $rate_origin_hub = new RateOriginHub();
                        $rate_origin_hub->user_id = $id;
                        $rate_origin_hub->shipping_mode_id = 3;
                        $rate_origin_hub->city_id = $origin_id;
                        $rate_origin_hub->save();
                    }
                }
                if ($request->has('detain_destination_hubs')) {
                    foreach ($request->detain_destination_hubs as $destination_id) {
                        $rate_destination_hub = new RateDestinationHub();
                        $rate_destination_hub->user_id = $id;
                        $rate_destination_hub->shipping_mode_id = 3;
                        $rate_destination_hub->city_id = $destination_id;
                        $rate_destination_hub->save();
                    }
                }

                RateStatus::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 3,
                    'status' => ($request->has('detain_main_switch')) ? 1 : 0,
                    'cash_handling_charges' => ($request->has('detain_cash_handling_switch')) ? 1 : 0,
                    'insurance_charges' => ($request->has('detain_insurance_charges_switch')) ? 1 : 0,
                    'return_charges' => ($request->has('detain_return_switch')) ? 1 : 0,
                    'fuel_charges' => ($request->has('detain_fuel_switch')) ? 1 : 0,
                    'zero_cod_discount' => ($request->has('detain_zero_cod_switch')) ? 1 : 0,
                    'return_discount' => ($request->has('detain_return_discount_switch')) ? 1 : 0,
                ]);
                $wa_switch_detain = array();
                $wa_spkg_detain = array();
                $standard_weight = StandardWeightCharge::where('shipping_mode_id', 3)->pluck('kg_range')->toArray();
                foreach ($request->detain_wa_range_up as $index => $detain_wa_range_up) {
                    if ($request->has('detain_wa_switch')) {
                        if (array_key_exists($index, $request->detain_wa_switch)) {
                            $wa_switch_detain[$index] = 1;
                        } else {
                            $wa_switch_detain[$index] = 0;
                        };
                    } else {
                        $wa_switch_detain[$index] = 0;
                    }
                    if ($request->has('detain_wa_spkg')) {
                        if (array_key_exists($index, $request->detain_wa_spkg)) {
                            $wa_spkg_detain[$index] = $request->detain_wa_spkg[$index];
                        } else {
                            $wa_spkg_detain[$index] = isset($standard_weight[$index]) ? $standard_weight[$index] : 0;
                        };
                    } else {
                        $wa_spkg_detain[$index] = isset($standard_weight[$index]) ? $standard_weight[$index] : 0;
                    }
                    WeightCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'range_up' => $request->detain_wa_range_up[$index],
                        'range_down' => $request->detain_wa_range_down[$index],
                        'weight_addition' => $wa_switch_detain[$index],
                        'spkg' => $wa_spkg_detain[$index],
                        'local_or_6hr' => $request->detain_wa_local_charges[$index],
                        'national_charges_class_0' => $request->detain_class_0_charges[$index],
                        'national_charges_class_1' => $request->detain_class_1_charges[$index],
                        'national_charges_class_2' => $request->detain_class_2_charges[$index],
                        'national_charges_class_3' => $request->detain_class_3_charges[$index]
                    ]);
                }


                //Replacement and Try and Buy charges
                BookingTypeCharges::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 3,
                    'replacement_charges' => $request->detain_replacement_charges,
                    'try_and_buy_charges' => $request->detain_tnb_charges,
                    'reverse_pickup_charges' => $request->detain_reverse_charges
                ]);
                //Cash handling Charges
                if ($request->has('detain_cash_handling_switch') && $request->detain_cash_handling_switch == 'on') {
                    foreach ($request->detain_cash_range_up as $ind => $detain_cash_range_up) {
                        CashHandlingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'range_up' => $request->detain_cash_range_up[$ind],
                            'range_down' => $request->detain_cash_range_down[$ind],
                            'charges' => $request->detain_cash_charges[$ind]
                        ]);
                    }
                }
                //insurance charges
                if ($request->has('detain_insurance_charges_switch') && $request->detain_insurance_charges_switch == 'on') {
                    foreach ($request->detain_ins_range_up as $insurance => $detain_ins_range_up) {
                        InsuranceCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'range_up' => $request->detain_ins_range_up[$insurance],
                            'range_down' => $request->detain_ins_range_down[$insurance],
                            'charges' => $request->detain_ins_charges[$insurance]
                        ]);
                    }
                }
                //Return Charges
                if ($request->has('detain_return_switch') && $request->detain_return_switch == 'on') {
                    ReturnCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'local' => $request->detain_return_local_charges,
                        'national_charges_class_0' => $request->detain_return_class_0_charges,
                        'national_charges_class_1' => $request->detain_return_class_1_charges,
                        'national_charges_class_2' => $request->detain_return_class_2_charges,
                        'national_charges_class_3' => $request->detain_return_class_3_charges
                    ]);
                }
                if ($request->has('detain_fuel_switch') && $request->detain_fuel_switch == 'on') {
                    FuelSurcharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'fuel_surcharge' => $request->detain_fuel_surcharge
                    ]);
                }

                $discount_cash = null;
                $discount_weight = null;
                $discount_insurance = null;
                $discount_return = null;
                $discount_packaging = null;

                if ($request->has('detain_discount_weight_switch') && $request->detain_discount_weight_switch == 'on') {
                    $discount_weight = $request->detain_discount_weight_rate != null ? $request->detain_discount_weight_rate : null;
//                    $discount_weight = $request->detain_discount_weight_rate;
                }
                if ($request->has('detain_discount_cash_switch') && $request->detain_discount_cash_switch == 'on') {
                    $discount_cash = $request->detain_discount_cash_rate != null ? $request->detain_discount_cash_rate : null;
                }
                if ($request->has('detain_discount_insurance_switch') && $request->detain_discount_insurance_switch == 'on') {
                    $discount_insurance = $request->detain_discount_insurance_rate != null ? $request->detain_discount_insurance_rate : null;
                }
                if ($request->has('detain_discount_return_switch') && $request->detain_discount_return_switch == 'on') {
                    $discount_return = $request->detain_discount_return_rate != null ? $request->detain_discount_insurance_rate : null;
                }
                if ($request->has('detain_discount_packaging_switch') && $request->detain_discount_packaging_switch == 'on') {
                    $discount_packaging = $request->detain_discount_packaging_rate != null ? $request->detain_discount_packaging_rate : null;
                }
                if ($discount_weight != null || $discount_cash != null || $discount_insurance != null || $discount_return != null || $discount_packaging != null) {


                    $date_str = $request->detain_daterange;
                    $date_sep = explode(' - ', $date_str);
                    $date_to = explode('/', $date_sep[0]);
                    $date_from = explode('/', $date_sep[1]);
                    $to = Carbon::create($date_to[2], $date_to[0], $date_to[1], 0, 0, 0, 'UTC')->toDateTimeString();
                    $from = Carbon::create($date_from[2], $date_from[0], $date_from[1], 0, 0, 0, 'UTC')->toDateTimeString();


                    DiscountCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'title' => $request->detain_discount_title,
                        'weight' => $discount_weight,
                        'cash' => $discount_cash,
                        'insurance' => $discount_insurance,
                        'return' => $discount_return,
                        'packaging' => $discount_packaging,
                        'to' => $to,
                        'from' => $from,
                        'added_by' => Auth::id()
                    ]);
                }

                if($request->has('d_discount_destination_wise_weight_switch') && $request->d_discount_destination_wise_weight_switch == "on")
                {
                    foreach($request->discount_d_destination as $parent_key => $destination)
                    {
                        $discount_wa_switch = array();
                        $discount_wa_spkg = array();
                        foreach($request->discount_d_wa_range_up[$parent_key] as $child_key => $value)
                        {
                            if ($request->has('discount_d_wa_switch.'.$parent_key)) {
                                if (array_key_exists($child_key, $request->discount_d_wa_switch[$parent_key])) {
                                    $discount_wa_switch[$child_key] = 1;
                                } else {
                                    $discount_wa_switch[$child_key] = 0;
                                };
                            } else {
                                $discount_wa_switch[$child_key] = 0;
                            }
                            if ($request->has('discount_d_wa_spkg.'.$parent_key)) {
                                if (array_key_exists($child_key, $request->discount_d_wa_spkg[$parent_key])) {
                                    $discount_wa_spkg[$child_key] = $request->discount_d_wa_spkg[$parent_key][$child_key];
                                } else {
                                    $discount_wa_spkg[$child_key] = 0.5;
                                };
                            } else {
                                $discount_wa_spkg[$child_key] = 0.5;
                            }
                            DiscountWeightCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'destination_id' => $destination,
                                'range_up' => $request->discount_d_wa_range_up[$parent_key][$child_key],
                                'range_down' => $request->discount_d_wa_range_down[$parent_key][$child_key],
                                'weight_addition' => $discount_wa_switch[$child_key],
                                'spkg' => $discount_wa_spkg[$child_key],
                                'local_or_6hr' => $request->discount_d_wa_local_charges[$parent_key][$child_key],
                            ]);
                        }
                    }
                }

            }
            if ($request->has('detain_dws_weight')) {
                if ($request->detain_dws_weight == 2) {

                    DwsWeightChargesController::add($id, 3, 2, Auth::id());

                } else {
                    DwsWeightChargesController::add($id, 3, 1, Auth::id());

                }

            }

        }
        //Sameday
        if ($request->has('sameday_main_switch') && $request->sameday_main_switch == 'on') {
            if ($request->has('sameday_default') && $request->sameday_default == 'on') {
                $default_shipping_mode = User::where('id', $id)->update([
                    'default_shipping_mode' => 4
                ]);
            }

            $SamedayRatePresent = RateStatus::where('user_id', $id)->where('shipping_mode_id', 4)->get();
            if ($SamedayRatePresent->isEmpty()) {

                if ($request->has('sameday_origin_hubs')) {
                    foreach ($request->sameday_origin_hubs as $origin_id) {
                        $rate_origin_hub = new RateOriginHub();
                        $rate_origin_hub->user_id = $id;
                        $rate_origin_hub->shipping_mode_id = 4;
                        $rate_origin_hub->city_id = $origin_id;
                        $rate_origin_hub->save();
                    }
                }
                if ($request->has('sameday_destination_hubs')) {
                    foreach ($request->sameday_destination_hubs as $destination_id) {
                        $rate_destination_hub = new RateDestinationHub();
                        $rate_destination_hub->user_id = $id;
                        $rate_destination_hub->shipping_mode_id = 4;
                        $rate_destination_hub->city_id = $destination_id;
                        $rate_destination_hub->save();
                    }
                }

                RateStatus::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 4,
                    'status' => ($request->has('sameday_main_switch')) ? 1 : 0,
                    'cash_handling_charges' => ($request->has('sameday_cash_handling_switch')) ? 1 : 0,
                    'insurance_charges' => ($request->has('sameday_insurance_charges_switch')) ? 1 : 0,
                    'return_charges' => ($request->has('sameday_return_switch')) ? 1 : 0,
                    'fuel_charges' => ($request->has('sameday_fuel_switch')) ? 1 : 0,
                    'zero_cod_discount' => ($request->has('sameday_zero_cod_switch')) ? 1 : 0,
                    'return_discount' => ($request->has('sameday_return_discount_switch')) ? 1 : 0,
                ]);
                $wa_switch_sameday = array();
                $wa_spkg_sameday = array();
                $standard_weight = StandardWeightCharge::where('shipping_mode_id', 4)->pluck('kg_range')->toArray();
                foreach ($request->sameday_wa_range_up as $index => $sameday_wa_range_up) {
                    if ($request->has('sameday_wa_switch')) {
                        if (array_key_exists($index, $request->sameday_wa_switch)) {
                            $wa_switch_sameday[$index] = 1;
                        } else {
                            $wa_switch_sameday[$index] = 0;
                        };
                    } else {
                        $wa_switch_sameday[$index] = 0;
                    }
                    if ($request->has('sameday_wa_spkg')) {
                        if (array_key_exists($index, $request->sameday_wa_spkg)) {
                            $wa_spkg_sameday[$index] = $request->sameday_wa_spkg[$index];
                        } else {
                            $wa_spkg_sameday[$index] = isset($standard_weight[$index]) ? $standard_weight[$index] : 0;
                        };
                    } else {
                        $wa_spkg_sameday[$index] = isset($standard_weight[$index]) ? $standard_weight[$index] : 0;
                    }
                    WeightCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'range_up' => $request->sameday_wa_range_up[$index],
                        'range_down' => $request->sameday_wa_range_down[$index],
                        'weight_addition' => $wa_switch_sameday[$index],
                        'spkg' => $wa_spkg_sameday[$index],
                        'local_or_6hr' => $request->sameday_wa_local_charges[$index],
                        'national_charges_class_0' => $request->sameday_class_0_charges[$index],
                        'national_charges_class_1' => '10%',
                        'national_charges_class_2' => '20%',
                        'national_charges_class_3' => 250
                    ]);
                }


                //Replacement and Try and Buy charges
                BookingTypeCharges::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 4,
                    'replacement_charges' => $request->sameday_replacement_charges,
                    'try_and_buy_charges' => $request->sameday_tnb_charges,
                    'reverse_pickup_charges' => $request->sameday_reverse_charges
                ]);
                //Cash handling Charges
                if ($request->has('sameday_cash_handling_switch') && $request->sameday_cash_handling_switch == 'on') {
                    foreach ($request->sameday_cash_range_up as $ind => $sameday_cash_range_up) {
                        CashHandlingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'range_up' => $request->sameday_cash_range_up[$ind],
                            'range_down' => $request->sameday_cash_range_down[$ind],
                            'charges' => $request->sameday_cash_charges[$ind]
                        ]);
                    }
                }
                //insurance charges
                if ($request->has('sameday_insurance_charges_switch') && $request->sameday_insurance_charges_switch == 'on') {
                    foreach ($request->sameday_ins_range_up as $insurance => $sameday_ins_range_up) {
                        InsuranceCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'range_up' => $request->sameday_ins_range_up[$insurance],
                            'range_down' => $request->sameday_ins_range_down[$insurance],
                            'charges' => $request->sameday_ins_charges[$insurance]
                        ]);
                    }
                }
                //Return Charges
                if ($request->has('sameday_return_switch') && $request->sameday_return_switch == 'on') {
                    ReturnCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'local' => $request->sameday_return_local_charges,
                        'national_charges_class_0' => $request->sameday_return_class_0_charges,
                        'national_charges_class_1' => '0%',
                        'national_charges_class_2' => '0%',
                        'national_charges_class_3' => '0%'
                    ]);
                }
                if ($request->has('sameday_fuel_switch') && $request->sameday_fuel_switch == 'on') {
                    FuelSurcharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'fuel_surcharge' => $request->sameday_fuel_surcharge
                    ]);
                }

                $discount_cash = null;
                $discount_weight = null;
                $discount_insurance = null;
                $discount_return = null;
                $discount_packaging = null;

                if ($request->has('sameday_discount_weight_switch') && $request->sameday_discount_weight_switch == 'on') {
                    $discount_weight = $request->sameday_discount_weight_rate != null ? $request->sameday_discount_weight_rate : null;
//                    $discount_weight = $request->sameday_discount_weight_rate;
                }
                if ($request->has('sameday_discount_cash_switch') && $request->sameday_discount_cash_switch == 'on') {
                    $discount_cash = $request->sameday_discount_cash_rate != null ? $request->sameday_discount_cash_rate : null;
                }
                if ($request->has('sameday_discount_insurance_switch') && $request->sameday_discount_insurance_switch == 'on') {
                    $discount_insurance = $request->sameday_discount_insurance_rate != null ? $request->sameday_discount_insurance_rate : null;
                }
                if ($request->has('sameday_discount_return_switch') && $request->sameday_discount_return_switch == 'on') {
                    $discount_return = $request->sameday_discount_return_rate != null ? $request->sameday_discount_insurance_rate : null;
                }
                if ($request->has('sameday_discount_packaging_switch') && $request->sameday_discount_packaging_switch == 'on') {
                    $discount_packaging = $request->sameday_discount_packaging_rate != null ? $request->sameday_discount_packaging_rate : null;
                }
                if ($discount_weight != null || $discount_cash != null || $discount_insurance != null || $discount_return != null || $discount_packaging != null) {


                    $date_str = $request->sameday_daterange;
                    $date_sep = explode(' - ', $date_str);
                    $date_to = explode('/', $date_sep[0]);
                    $date_from = explode('/', $date_sep[1]);
                    $to = Carbon::create($date_to[2], $date_to[0], $date_to[1], 0, 0, 0, 'UTC')->toDateTimeString();
                    $from = Carbon::create($date_from[2], $date_from[0], $date_from[1], 0, 0, 0, 'UTC')->toDateTimeString();


                    DiscountCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'title' => $request->sameday_discount_title,
                        'weight' => $discount_weight,
                        'cash' => $discount_cash,
                        'insurance' => $discount_insurance,
                        'return' => $discount_return,
                        'packaging' => $discount_packaging,
                        'to' => $to,
                        'from' => $from,
                        'added_by' => Auth::id()
                    ]);
                }

                if($request->has('sd_discount_destination_wise_weight_switch') && $request->sd_discount_destination_wise_weight_switch == "on")
                {
                    foreach($request->discount_sd_destination as $parent_key => $destination)
                    {
                        $discount_wa_switch = array();
                        $discount_wa_spkg = array();
                        foreach($request->discount_sd_wa_range_up[$parent_key] as $child_key => $value)
                        {
                            if ($request->has('discount_sd_wa_switch.'.$parent_key)) {
                                if (array_key_exists($child_key, $request->discount_sd_wa_switch[$parent_key])) {
                                    $discount_wa_switch[$child_key] = 1;
                                } else {
                                    $discount_wa_switch[$child_key] = 0;
                                };
                            } else {
                                $discount_wa_switch[$child_key] = 0;
                            }
                            if ($request->has('discount_sd_wa_spkg.'.$parent_key)) {
                                if (array_key_exists($child_key, $request->discount_sd_wa_spkg[$parent_key])) {
                                    $discount_wa_spkg[$child_key] = $request->discount_sd_wa_spkg[$parent_key][$child_key];
                                } else {
                                    $discount_wa_spkg[$child_key] = 0.5;
                                };
                            } else {
                                $discount_wa_spkg[$child_key] = 0.5;
                            }
                            DiscountWeightCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'destination_id' => $destination,
                                'range_up' => $request->discount_sd_wa_range_up[$parent_key][$child_key],
                                'range_down' => $request->discount_sd_wa_range_down[$parent_key][$child_key],
                                'weight_addition' => $discount_wa_switch[$child_key],
                                'spkg' => $discount_wa_spkg[$child_key],
                                'local_or_6hr' => $request->discount_sd_wa_local_charges[$parent_key][$child_key],
                            ]);
                        }
                    }
                }

            }
            if ($request->has('sameday_dws_weight')) {
                if ($request->sameday_dws_weight == 2) {
                    DwsWeightChargesController::add($id, 4, 2, Auth::id());

                } else {
                    DwsWeightChargesController::add($id, 4, 1, Auth::id());

                }

            }

        }

        self::discounted_cod_and_return($request,$id,2,'default');

        $warehouse_charges = 0;
        if ($request->has('warehouse_main_switch') && $request->warehouse_main_switch == 'on') {
            $warehouse_charges = 1;

            $wms_user_info = new WmsUserInformation();
            $wms_user_info->user_id = $id;
            $wms_user_info->warehousing = 1;
            $wms_user_info->invoicing_cycle = $request->invoicing_cycle;
            $wms_user_info->invoicing_date = 1;
            $wms_user_info->per_product_charges = ($request->has('ppc_switch')) ? 1 : 0;
            $wms_user_info->per_square_foot_charges = ($request->has('psf_switch')) ? 1 : 0;
            $wms_user_info->packing_charges = ($request->has('packing_charges_switch')) ? 1 : 0;
            $wms_user_info->labelling_charges = ($request->has('labelling_charges_switch')) ? 1 : 0;
            $wms_user_info->storage_charges = ($request->has('storage_charges_switch')) ? 1 : 0;
            $wms_user_info->save();

            if ($request->has('ppc_switch')) {
                $ppc = new WmsPerProductCharge();
                $ppc->user_id = $id;
                $ppc->charges = $request->ppc_charges;
                $ppc->save();
            }
            if ($request->has('psf_switch')) {
                $psf = new WmsPerSquareFootCharge();
                $psf->user_id = $id;
                $psf->charges = $request->psf_charges;
                $psf->save();
            }
            if ($request->has('storage_charges_switch')) {
                foreach ($request->storage_type as $key => $storage_type) {
                    $storage_charges = new WmsStorageTypeCharge();
                    $storage_charges->user_id = $id;
                    $storage_charges->storage_type_id = $storage_type;
                    $storage_charges->charges = $request->storage_type_charges[$key];
                    $storage_charges->save();
                }
            }


            if ($request->has('packing_charges_switch')) {
                foreach ($request->packing_type as $key => $packing) {
                    $ptype = new WmsPackingCharge();
                    $ptype->user_id = $id;
                    $ptype->packing_type_id = $packing;
                    $ptype->packing_size_id = $request->packing_size[$key];
                    $ptype->charges = $request->packing_charges[$key];
                    $ptype->save();
                }
            }

            if ($request->has('labelling_charges_switch')) {
                $labelling = new WmsLabellingCharge();
                $labelling->user_id = $id;
                $labelling->charges = $request->labelling_charges;
                $labelling->save();
            }
        }

        if ($request->has('wordpress_account') && $request->request_custom_quotations == 0) {
            User::where('id', $id)->update(['status' => 2, 'rates_added_by' => 346, 'rates_authorized_by' => 346, 'rates_approved_at' => Carbon::now(), 'rates_added_at' => Carbon::now(), 'rate_status' => 0, 'request_custom_quotation' => 0, 'on_board_status' => 1]);
            session(['status' => 2]);
        } else if ($request->has('wordpress_account') && $request->request_custom_quotations == 1) {
            User::where('id', $id)->update(['status' => 0, 'rate_status' => 0, 'request_custom_quotation' => 1, 'on_board_status' => 1]);

            // NotificationsController::send(231, $id);
        } else {
            User::where('id', $id)->update(['status' => 1, 'rates_added_by' => Auth::id(), 'rates_added_at' => Carbon::now(), 'on_board_status' => 1]);
        }
        

        if ($request->has('rate_remarks') && $request->rate_remarks != null) {
            $rate_remark = new RateRemark();
            $rate_remark->user_id = $id;
            $rate_remark->remarks = $request->rate_remarks;
            $rate_remark->admin_id = Auth::id();
            $rate_remark->save();

        }


        //overnight

        $weight_charges = WeightCharge::where('user_id', $id)->where('shipping_mode_id', 1);
        $standard_charges = StandardWeightCharge::where('shipping_mode_id', 1);
        $overnight_changes = 0;
        if ($weight_charges->exists()) {
            $standard_range_up = $standard_charges->pluck('range_up')->toArray();
            $weight_range_up = $weight_charges->pluck('range_up')->toArray();
            $weight_range_up_diff = $this->compare_data($weight_range_up, $standard_range_up);

            $standard_range_down = $standard_charges->pluck('range_down')->toArray();
            $weight_range_down = $weight_charges->pluck('range_down')->toArray();
            $weight_range_down_diff = $this->compare_data($weight_range_down, $standard_range_down);

            $standard_kg_range = $standard_charges->pluck('kg_range')->toArray();
            $weight_kg_range = $weight_charges->pluck('spkg')->toArray();
            $kg_range_diff = $this->compare_data($weight_kg_range, $standard_kg_range);

            $standard_weight_addition = $standard_charges->pluck('weight_addition')->toArray();
            $weight_addition = $weight_charges->pluck('weight_addition')->toArray();
            $weight_addition_diff = $this->compare_data($standard_weight_addition, $weight_addition);

            $standard_local = $standard_charges->pluck('local_or_6hr')->toArray();
            $weight_local = $weight_charges->pluck('local_or_6hr')->toArray();
            $local_diff = $this->compare_data($weight_local, $standard_local);

            $standard_national_0 = $standard_charges->pluck('national_charges_class_0')->toArray();
            $weight_national_0 = $weight_charges->pluck('national_charges_class_0')->toArray();
            $national_charges_0_diff = $this->compare_data($weight_national_0, $standard_national_0);

            $standard_national_1 = $standard_charges->pluck('national_charges_class_1')->toArray();
            $weight_national_1 = $weight_charges->pluck('national_charges_class_1')->toArray();
            $national_charges_1_diff = $this->compare_data($weight_national_1, $standard_national_1);

            $standard_national_2 = $standard_charges->pluck('national_charges_class_2')->toArray();
            $weight_national_2 = $weight_charges->pluck('national_charges_class_2')->toArray();
            $national_charges_2_diff = $this->compare_data($weight_national_2, $standard_national_2);

            $standard_national_3 = $standard_charges->pluck('national_charges_class_3')->toArray();
            $weight_national_3 = $weight_charges->pluck('national_charges_class_3')->toArray();
            $national_charges_3_diff = $this->compare_data($weight_national_3, $standard_national_3);

            $standard_booking_type_charges = StandardBookingTypeCharge::where('shipping_mode_id', 1)->first();
            $booking_type_charges = BookingTypeCharges::where('shipping_mode_id', 1)->where('user_id', $id)->first();

            $booking_type_charges_diff = 0;
            if ($booking_type_charges) {
                if ($standard_booking_type_charges->replacement_charges != $booking_type_charges->replacement_charges || $standard_booking_type_charges->try_and_buy_charges != $booking_type_charges->try_and_buy_charges || $standard_booking_type_charges->reverse_pickup_charges != $booking_type_charges->reverse_pickup_charges) {
                    $booking_type_charges_diff = 1;
                }
            }


            $cash_handling_charges = CashHandlingCharge::where('shipping_mode_id', 1)->where('user_id', $id);
            $cash_handling_charges_change = 0;
            if ($cash_handling_charges->exists()) {
                $standard_cash_handling_charges = StandardCashHandlingCharge::where('shipping_mode_id', 1);

                $cash_handling_charges_range_up = $cash_handling_charges->pluck('range_up')->toArray();
                $standard_cash_handling_charges_range_up = $standard_cash_handling_charges->pluck('range_up')->toArray();
                $cash_handling_range_up_diff = $this->compare_data($cash_handling_charges_range_up, $standard_cash_handling_charges_range_up);

                $cash_handling_charges_range_down = $cash_handling_charges->pluck('range_down')->toArray();
                $standard_cash_handling_charges_range_down = $standard_cash_handling_charges->pluck('range_down')->toArray();
                $cash_handling_range_down_diff = $this->compare_data($cash_handling_charges_range_down, $standard_cash_handling_charges_range_down);

                $cash_handling_charges = $cash_handling_charges->pluck('charges')->toArray();
                $standard_cash_handling_charges = $standard_cash_handling_charges->pluck('charges')->toArray();
                $cash_handling_charges_diff = $this->compare_data($cash_handling_charges, $standard_cash_handling_charges);

                if (($cash_handling_range_up_diff == 1) || ($cash_handling_range_down_diff == 1) || ($cash_handling_charges_diff == 1)) {
                    $cash_handling_charges_change = 1;
                }

            } else {
                $cash_handling_charges_change = 1;
            }

            $return_charges = ReturnCharge::where('shipping_mode_id', 1)->where('user_id', $id);
            $return_charges_diff = 0;

            if ($return_charges->exists()) {
                $standard_return_charges = StandardReturnCharge::where('shipping_mode_id', 1)->first();
                $return_charges = $return_charges->first();


                if ($return_charges->local != $standard_return_charges->local || $return_charges->national_charges_class_0 != $standard_return_charges->national_charges_class_0 || $return_charges->national_charges_class_1 != $standard_return_charges->national_charges_class_1 || $return_charges->national_charges_class_2 != $standard_return_charges->national_charges_class_2 || $return_charges->national_charges_class_3 != $standard_return_charges->national_charges_class_3) {
                    $return_charges_diff = 1;
                }
            } else {
                $return_charges_diff = 1;
            }

            $fuel_surcharge = FuelSurcharge::where('shipping_mode_id', 1)->where('user_id', $id);
            $fuel_surcharge_diff = 0;
            if ($fuel_surcharge->exists()) {
                $standard_fuel_surcharge = StandardFuelSurcharge::where('shipping_mode_id', 1)->first();
                $fuel_surcharge = $fuel_surcharge->first();

                if ($standard_fuel_surcharge->fuel_surcharge != $fuel_surcharge->fuel_surcharge) {
                    $fuel_surcharge_diff = 1;
                }
            } else {
                $fuel_surcharge_diff = 1;
            }

            $insurance_charge = InsuranceCharge::where('shipping_mode_id', 1)->where('user_id', $id);
            $insurance_charges_diff = 0;
            if ($insurance_charge->exists()) {
                $insurance_charges_diff = 1;
            }
            $on_dws_weight_diff = 0;
            if ($request->has('on_dws_weight')) {
                if ($request->on_dws_weight == 0) {
                    $on_dws_weight_diff = 1;
                }
            }
			 $on_open_box_diff = 0;
            if ($request->has('on_open_box_switch') && $request->on_open_box_switch == 'on') {
                if($request->overnight_open_box != null){
                    $on_open_box_diff = 1;
                }
            }
            $on_discount_weight_charges_diff = 0;
            if ($request->has('on_discount_destination_wise_weight_switch')) {
                    $on_discount_weight_charges_diff = 1;
            }

            if ($weight_range_up_diff == 1 || $weight_range_down_diff == 1 || $kg_range_diff == 1 || $local_diff == 1 || $national_charges_0_diff == 1 || $national_charges_1_diff == 1 || $national_charges_2_diff == 1 || $national_charges_3_diff == 1 || $booking_type_charges_diff == 1 || $cash_handling_charges_change == 1 || $return_charges_diff == 1 || $fuel_surcharge_diff == 1 || $weight_addition_diff == 1 || $insurance_charges_diff == 1 || $on_dws_weight_diff == 1 || $on_discount_weight_charges_diff == 1 || $on_open_box_diff == 1) {                $overnight_changes = 1;

            }
        }

        //overland
        $weight_charges = WeightCharge::where('user_id', $id)->where('shipping_mode_id', 2);
        $standard_charges = StandardWeightCharge::where('shipping_mode_id', 2);
        $overland_changes = 0;
        if ($weight_charges->exists()) {
            $standard_range_up = $standard_charges->pluck('range_up')->toArray();
            $weight_range_up = $weight_charges->pluck('range_up')->toArray();
            $weight_range_up_diff = $this->compare_data($weight_range_up, $standard_range_up);

            $standard_range_down = $standard_charges->pluck('range_down')->toArray();
            $weight_range_down = $weight_charges->pluck('range_down')->toArray();
            $weight_range_down_diff = $this->compare_data($weight_range_down, $standard_range_down);

            $standard_kg_range = $standard_charges->pluck('kg_range')->toArray();
            $weight_kg_range = $weight_charges->pluck('spkg')->toArray();
            $kg_range_diff = $this->compare_data($weight_kg_range, $standard_kg_range);

            $standard_weight_addition = $standard_charges->pluck('weight_addition')->toArray();
            $weight_addition = $weight_charges->pluck('weight_addition')->toArray();
            $weight_addition_diff = $this->compare_data($standard_weight_addition, $weight_addition);

            $standard_local = $standard_charges->pluck('local_or_6hr')->toArray();
            $weight_local = $weight_charges->pluck('local_or_6hr')->toArray();
            $local_diff = $this->compare_data($weight_local, $standard_local);

            $standard_national_0 = $standard_charges->pluck('national_charges_class_0')->toArray();
            $weight_national_0 = $weight_charges->pluck('national_charges_class_0')->toArray();
            $national_charges_0_diff = $this->compare_data($weight_national_0, $standard_national_0);

            $standard_national_1 = $standard_charges->pluck('national_charges_class_1')->toArray();
            $weight_national_1 = $weight_charges->pluck('national_charges_class_1')->toArray();
            $national_charges_1_diff = $this->compare_data($weight_national_1, $standard_national_1);

            $standard_national_2 = $standard_charges->pluck('national_charges_class_2')->toArray();
            $weight_national_2 = $weight_charges->pluck('national_charges_class_2')->toArray();
            $national_charges_2_diff = $this->compare_data($weight_national_2, $standard_national_2);

            $standard_national_3 = $standard_charges->pluck('national_charges_class_3')->toArray();
            $weight_national_3 = $weight_charges->pluck('national_charges_class_3')->toArray();
            $national_charges_3_diff = $this->compare_data($weight_national_3, $standard_national_3);

            $standard_booking_type_charges = StandardBookingTypeCharge::where('shipping_mode_id', 2)->first();
            $booking_type_charges = BookingTypeCharges::where('shipping_mode_id', 2)->where('user_id', $id)->first();

            $booking_type_charges_diff = 0;
            if ($booking_type_charges) {
                if ($standard_booking_type_charges->replacement_charges != $booking_type_charges->replacement_charges || $standard_booking_type_charges->try_and_buy_charges != $booking_type_charges->try_and_buy_charges || $standard_booking_type_charges->reverse_pickup_charges != $booking_type_charges->reverse_pickup_charges) {
                    $booking_type_charges_diff = 1;
                }
            }

            $cash_handling_charges = CashHandlingCharge::where('shipping_mode_id', 2)->where('user_id', $id);
            $cash_handling_charges_change = 0;
            if ($cash_handling_charges->exists()) {
                $standard_cash_handling_charges = StandardCashHandlingCharge::where('shipping_mode_id', 2);

                $cash_handling_charges_range_up = $cash_handling_charges->pluck('range_up')->toArray();
                $standard_cash_handling_charges_range_up = $standard_cash_handling_charges->pluck('range_up')->toArray();
                $cash_handling_range_up_diff = $this->compare_data($cash_handling_charges_range_up, $standard_cash_handling_charges_range_up);

                $cash_handling_charges_range_down = $cash_handling_charges->pluck('range_down')->toArray();
                $standard_cash_handling_charges_range_down = $standard_cash_handling_charges->pluck('range_down')->toArray();
                $cash_handling_range_down_diff = $this->compare_data($cash_handling_charges_range_down, $standard_cash_handling_charges_range_down);

                $cash_handling_charges = $cash_handling_charges->pluck('charges')->toArray();
                $standard_cash_handling_charges = $standard_cash_handling_charges->pluck('charges')->toArray();
                $cash_handling_charges_diff = $this->compare_data($cash_handling_charges, $standard_cash_handling_charges);

                if (($cash_handling_range_up_diff == 1) || ($cash_handling_range_down_diff == 1) || ($cash_handling_charges_diff == 1)) {
                    $cash_handling_charges_change = 1;
                }

            } else {
                //for toggle close
                $cash_handling_charges_change = 1;
            }

            $return_charges = ReturnCharge::where('shipping_mode_id', 2)->where('user_id', $id);
            $return_charges_diff = 0;

            if ($return_charges->exists()) {
                $standard_return_charges = StandardReturnCharge::where('shipping_mode_id', 2)->first();
                $return_charges = $return_charges->first();


                if ($return_charges->local != $standard_return_charges->local || $return_charges->national_charges_class_0 != $standard_return_charges->national_charges_class_0 || $return_charges->national_charges_class_1 != $standard_return_charges->national_charges_class_1 || $return_charges->national_charges_class_2 != $standard_return_charges->national_charges_class_2 || $return_charges->national_charges_class_3 != $standard_return_charges->national_charges_class_3) {
                    $return_charges_diff = 1;
                }
            } else {
                $return_charges_diff = 1;
            }

            $fuel_surcharge = FuelSurcharge::where('shipping_mode_id', 2)->where('user_id', $id);
            $fuel_surcharge_diff = 0;
            if ($fuel_surcharge->exists()) {
                $standard_fuel_surcharge = StandardFuelSurcharge::where('shipping_mode_id', 2)->first();
                $fuel_surcharge = $fuel_surcharge->first();

                if ($standard_fuel_surcharge->fuel_surcharge != $fuel_surcharge->fuel_surcharge) {
                    $fuel_surcharge_diff = 1;
                }
            } else {
                $fuel_surcharge_diff = 1;
            }

            $insurance_charge = InsuranceCharge::where('shipping_mode_id', 2)->where('user_id', $id);
            $insurance_charges_diff = 0;
            if ($insurance_charge->exists()) {
                $insurance_charges_diff = 1;
            }
            $ol_dws_weight_diff = 0;
            if ($request->has('ol_dws_weight')) {
                if ($request->ol_dws_weight == 0) {
                    $ol_dws_weight_diff = 1;
                }
            }

            $ol_discount_weight_charges_diff = 0;
            if ($request->has('ol_discount_destination_wise_weight_switch')) {
                    $ol_discount_weight_charges_diff = 1;
            }
			 $ol_open_box_diff = 0;
	            if ($request->has('ol_open_box_switch') && $request->ol_open_box_switch == 'on') {
	                if($request->overland_open_box != null){
	                    $ol_open_box_diff = 1;
	                }
            }

            if ($weight_range_up_diff == 1 || $weight_range_down_diff == 1 || $kg_range_diff == 1 || $local_diff == 1 || $national_charges_0_diff == 1 || $national_charges_1_diff == 1 || $national_charges_2_diff == 1 || $national_charges_3_diff == 1 || $booking_type_charges_diff == 1 || $cash_handling_charges_change == 1 || $return_charges_diff == 1 || $fuel_surcharge_diff == 1 || $weight_addition_diff == 1 || $insurance_charges_diff == 1 || $ol_dws_weight_diff == 1 || $ol_discount_weight_charges_diff == 1 || $ol_open_box_diff == 1) {
                $overland_changes = 1;
            }
        }

        //detain
        $weight_charges = WeightCharge::where('user_id', $id)->where('shipping_mode_id', 3);
        $standard_charges = StandardWeightCharge::where('shipping_mode_id', 3);
        $detain_changes = 0;
        if ($weight_charges->exists()) {
            $standard_range_up = $standard_charges->pluck('range_up')->toArray();
            $weight_range_up = $weight_charges->pluck('range_up')->toArray();
            $weight_range_up_diff = $this->compare_data($weight_range_up, $standard_range_up);

            $standard_range_down = $standard_charges->pluck('range_down')->toArray();
            $weight_range_down = $weight_charges->pluck('range_down')->toArray();
            $weight_range_down_diff = $this->compare_data($weight_range_down, $standard_range_down);

            $standard_kg_range = $standard_charges->pluck('kg_range')->toArray();
            $weight_kg_range = $weight_charges->pluck('spkg')->toArray();
            $kg_range_diff = $this->compare_data($weight_kg_range, $standard_kg_range);

            $standard_weight_addition = $standard_charges->pluck('weight_addition')->toArray();
            $weight_addition = $weight_charges->pluck('weight_addition')->toArray();
            $weight_addition_diff = $this->compare_data($standard_weight_addition, $weight_addition);

            $standard_local = $standard_charges->pluck('local_or_6hr')->toArray();
            $weight_local = $weight_charges->pluck('local_or_6hr')->toArray();
            $local_diff = $this->compare_data($weight_local, $standard_local);

            $standard_national_0 = $standard_charges->pluck('national_charges_class_0')->toArray();
            $weight_national_0 = $weight_charges->pluck('national_charges_class_0')->toArray();
            $national_charges_0_diff = $this->compare_data($weight_national_0, $standard_national_0);

            $standard_national_1 = $standard_charges->pluck('national_charges_class_1')->toArray();
            $weight_national_1 = $weight_charges->pluck('national_charges_class_1')->toArray();
            $national_charges_1_diff = $this->compare_data($weight_national_1, $standard_national_1);

            $standard_national_2 = $standard_charges->pluck('national_charges_class_2')->toArray();
            $weight_national_2 = $weight_charges->pluck('national_charges_class_2')->toArray();
            $national_charges_2_diff = $this->compare_data($weight_national_2, $standard_national_2);

            $standard_national_3 = $standard_charges->pluck('national_charges_class_3')->toArray();
            $weight_national_3 = $weight_charges->pluck('national_charges_class_3')->toArray();
            $national_charges_3_diff = $this->compare_data($weight_national_3, $standard_national_3);

            $standard_booking_type_charges = StandardBookingTypeCharge::where('shipping_mode_id', 3)->first();
            $booking_type_charges = BookingTypeCharges::where('shipping_mode_id', 3)->where('user_id', $id)->first();

            $booking_type_charges_diff = 0;
            if ($booking_type_charges) {
                if ($standard_booking_type_charges->replacement_charges != $booking_type_charges->replacement_charges || $standard_booking_type_charges->try_and_buy_charges != $booking_type_charges->try_and_buy_charges || $standard_booking_type_charges->reverse_pickup_charges != $booking_type_charges->reverse_pickup_charges) {
                    $booking_type_charges_diff = 1;
                }
            }

            $cash_handling_charges = CashHandlingCharge::where('shipping_mode_id', 3)->where('user_id', $id);
            $cash_handling_charges_change = 0;
            if ($cash_handling_charges->exists()) {
                $standard_cash_handling_charges = StandardCashHandlingCharge::where('shipping_mode_id', 3);

                $cash_handling_charges_range_up = $cash_handling_charges->pluck('range_up')->toArray();
                $standard_cash_handling_charges_range_up = $standard_cash_handling_charges->pluck('range_up')->toArray();
                $cash_handling_range_up_diff = $this->compare_data($cash_handling_charges_range_up, $standard_cash_handling_charges_range_up);

                $cash_handling_charges_range_down = $cash_handling_charges->pluck('range_down')->toArray();
                $standard_cash_handling_charges_range_down = $standard_cash_handling_charges->pluck('range_down')->toArray();
                $cash_handling_range_down_diff = $this->compare_data($cash_handling_charges_range_down, $standard_cash_handling_charges_range_down);

                $cash_handling_charges = $cash_handling_charges->pluck('charges')->toArray();
                $standard_cash_handling_charges = $standard_cash_handling_charges->pluck('charges')->toArray();
                $cash_handling_charges_diff = $this->compare_data($cash_handling_charges, $standard_cash_handling_charges);

                if (($cash_handling_range_up_diff == 1) || ($cash_handling_range_down_diff == 1) || ($cash_handling_charges_diff == 1)) {
                    $cash_handling_charges_change = 1;
                }

            } else {
                $cash_handling_charges_change = 1;
            }

            $return_charges = ReturnCharge::where('shipping_mode_id', 3)->where('user_id', $id);
            $return_charges_diff = 0;

            if ($return_charges->exists()) {
                $standard_return_charges = StandardReturnCharge::where('shipping_mode_id', 3)->first();
                $return_charges = $return_charges->first();


                if ($return_charges->local != $standard_return_charges->local || $return_charges->national_charges_class_0 != $standard_return_charges->national_charges_class_0 || $return_charges->national_charges_class_1 != $standard_return_charges->national_charges_class_1 || $return_charges->national_charges_class_2 != $standard_return_charges->national_charges_class_2 || $return_charges->national_charges_class_3 != $standard_return_charges->national_charges_class_3) {
                    $return_charges_diff = 1;
                }
            } else {
                $return_charges_diff = 1;
            }

            $fuel_surcharge = FuelSurcharge::where('shipping_mode_id', 3)->where('user_id', $id);
            $fuel_surcharge_diff = 0;
            if ($fuel_surcharge->exists()) {
                $standard_fuel_surcharge = StandardFuelSurcharge::where('shipping_mode_id', 3)->first();
                $fuel_surcharge = $fuel_surcharge->first();

                if ($standard_fuel_surcharge->fuel_surcharge != $fuel_surcharge->fuel_surcharge) {
                    $fuel_surcharge_diff = 1;
                }
            } else {
                $fuel_surcharge_diff = 1;
            }

            $insurance_charge = InsuranceCharge::where('shipping_mode_id', 3)->where('user_id', $id);
            $insurance_charges_diff = 0;
            if ($insurance_charge->exists()) {
                $insurance_charges_diff = 1;
            }
            $detain_dws_weight_diff = 0;
            if ($request->has('detain_dws_weight')) {
                if ($request->detain_dws_weight == 0) {
                    $detain_dws_weight_diff = 1;
                }
            }

            $d_discount_weight_charges_diff = 0;
                if ($request->d_discount_destination_wise_weight_switch == "on") {
                    $d_discount_weight_charges_diff = 1;
            }

			$detain_open_box_diff = 0;
	            if ($request->has('detain_open_box_switch') && $request->detain_open_box_switch == 'on') {
	                if($request->detain_open_box != null){
	                    $detain_open_box_diff = 1;
	                }
            }


            if ($weight_range_up_diff == 1 || $weight_range_down_diff == 1 || $kg_range_diff == 1 || $local_diff == 1 || $national_charges_0_diff == 1 || $national_charges_1_diff == 1 || $national_charges_2_diff == 1 || $national_charges_3_diff == 1 || $booking_type_charges_diff == 1 || $cash_handling_charges_change == 1 || $return_charges_diff == 1 || $fuel_surcharge_diff == 1 || $weight_addition_diff == 1 || $insurance_charges_diff == 1 || $detain_dws_weight_diff == 1 || $d_discount_weight_charges_diff == 1 || $d_discount_weight_charges_diff == 1 || $detain_open_box_diff == 1) {
                $detain_changes = 1;
            }
        }

        //sameday
        $weight_charges = WeightCharge::where('user_id', $id)->where('shipping_mode_id', 4);
        $standard_charges = StandardWeightCharge::where('shipping_mode_id', 4);
        $sameday_changes = 0;
        if ($weight_charges->exists()) {
            $standard_range_up = $standard_charges->pluck('range_up')->toArray();
            $weight_range_up = $weight_charges->pluck('range_up')->toArray();
            $weight_range_up_diff = $this->compare_data($weight_range_up, $standard_range_up);

            $standard_range_down = $standard_charges->pluck('range_down')->toArray();
            $weight_range_down = $weight_charges->pluck('range_down')->toArray();
            $weight_range_down_diff = $this->compare_data($weight_range_down, $standard_range_down);

            $standard_kg_range = $standard_charges->pluck('kg_range')->toArray();
            $weight_kg_range = $weight_charges->pluck('spkg')->toArray();
            $kg_range_diff = $this->compare_data($weight_kg_range, $standard_kg_range);

            $standard_weight_addition = $standard_charges->pluck('weight_addition')->toArray();
            $weight_addition = $weight_charges->pluck('weight_addition')->toArray();
            $weight_addition_diff = $this->compare_data($standard_weight_addition, $weight_addition);

            $standard_local = $standard_charges->pluck('local_or_6hr')->toArray();
            $weight_local = $weight_charges->pluck('local_or_6hr')->toArray();
            $local_diff = $this->compare_data($weight_local, $standard_local);

            $standard_national_0 = $standard_charges->pluck('national_charges_class_0')->toArray();
            $weight_national_0 = $weight_charges->pluck('national_charges_class_0')->toArray();
            $national_charges_0_diff = $this->compare_data($weight_national_0, $standard_national_0);

            $standard_national_1 = $standard_charges->pluck('national_charges_class_1')->toArray();
            $weight_national_1 = $weight_charges->pluck('national_charges_class_1')->toArray();
            $national_charges_1_diff = $this->compare_data($weight_national_1, $standard_national_1);

            $standard_national_2 = $standard_charges->pluck('national_charges_class_2')->toArray();
            $weight_national_2 = $weight_charges->pluck('national_charges_class_2')->toArray();
            $national_charges_2_diff = $this->compare_data($weight_national_2, $standard_national_2);

            $standard_national_3 = $standard_charges->pluck('national_charges_class_3')->toArray();
            $weight_national_3 = $weight_charges->pluck('national_charges_class_3')->toArray();
            $national_charges_3_diff = $this->compare_data($weight_national_3, $standard_national_3);

            $standard_booking_type_charges = StandardBookingTypeCharge::where('shipping_mode_id', 4)->first();
            $booking_type_charges = BookingTypeCharges::where('shipping_mode_id', 4)->where('user_id', $id)->first();

            $booking_type_charges_diff = 0;
            if ($booking_type_charges) {
                if ($standard_booking_type_charges->replacement_charges != $booking_type_charges->replacement_charges || $standard_booking_type_charges->try_and_buy_charges != $booking_type_charges->try_and_buy_charges || $standard_booking_type_charges->reverse_pickup_charges != $booking_type_charges->reverse_pickup_charges) {
                    $booking_type_charges_diff = 1;
                }
            }


            $cash_handling_charges = CashHandlingCharge::where('shipping_mode_id', 4)->where('user_id', $id);
            $cash_handling_charges_change = 0;
            if ($cash_handling_charges->exists()) {
                $standard_cash_handling_charges = StandardCashHandlingCharge::where('shipping_mode_id', 4);

                $cash_handling_charges_range_up = $cash_handling_charges->pluck('range_up')->toArray();
                $standard_cash_handling_charges_range_up = $standard_cash_handling_charges->pluck('range_up')->toArray();
                $cash_handling_range_up_diff = $this->compare_data($cash_handling_charges_range_up, $standard_cash_handling_charges_range_up);

                $cash_handling_charges_range_down = $cash_handling_charges->pluck('range_down')->toArray();
                $standard_cash_handling_charges_range_down = $standard_cash_handling_charges->pluck('range_down')->toArray();
                $cash_handling_range_down_diff = $this->compare_data($cash_handling_charges_range_down, $standard_cash_handling_charges_range_down);

                $cash_handling_charges = $cash_handling_charges->pluck('charges')->toArray();
                $standard_cash_handling_charges = $standard_cash_handling_charges->pluck('charges')->toArray();
                $cash_handling_charges_diff = $this->compare_data($cash_handling_charges, $standard_cash_handling_charges);

                if (($cash_handling_range_up_diff == 1) || ($cash_handling_range_down_diff == 1) || ($cash_handling_charges_diff == 1)) {
                    $cash_handling_charges_change = 1;
                }

            } else {
                $cash_handling_charges_change = 1;
            }

            $return_charges = ReturnCharge::where('shipping_mode_id', 4)->where('user_id', $id);
            $return_charges_diff = 0;

            if ($return_charges->exists()) {
                $standard_return_charges = StandardReturnCharge::where('shipping_mode_id', 4)->first();
                $return_charges = $return_charges->first();


                if ($return_charges->local != $standard_return_charges->local || $return_charges->national_charges_class_0 != $standard_return_charges->national_charges_class_0 || $return_charges->national_charges_class_1 != $standard_return_charges->national_charges_class_1 || $return_charges->national_charges_class_2 != $standard_return_charges->national_charges_class_2 || $return_charges->national_charges_class_3 != $standard_return_charges->national_charges_class_3) {
                    $return_charges_diff = 1;
                }
            } else {
                $return_charges_diff = 1;
            }

            $fuel_surcharge = FuelSurcharge::where('shipping_mode_id', 4)->where('user_id', $id);
            $fuel_surcharge_diff = 0;
            if ($fuel_surcharge->exists()) {
                $standard_fuel_surcharge = StandardFuelSurcharge::where('shipping_mode_id', 4)->first();
                $fuel_surcharge = $fuel_surcharge->first();

                if ($standard_fuel_surcharge->fuel_surcharge != $fuel_surcharge->fuel_surcharge) {
                    $fuel_surcharge_diff = 1;
                }
            } else {
                $fuel_surcharge_diff = 1;
            }

            $insurance_charge = InsuranceCharge::where('shipping_mode_id', 4)->where('user_id', $id);
            $insurance_charges_diff = 0;
            if ($insurance_charge->exists()) {
                $insurance_charges_diff = 1;
            }
            $sameday_dws_weight_diff = 0;
            if ($request->has('sameday_dws_weight')) {
                if ($request->sameday_dws_weight == 0) {
                    $sameday_dws_weight_diff = 1;
                }
            }

            $sd_discount_weight_charges_diff = 0;
            if ($request->has('sd_discount_destination_wise_weight_switch')) {
                    $sd_discount_weight_charges_diff = 1;
            }
			$sd_open_box_diff = 0;
            	if ($request->has('sd_open_box_switch') && $request->sd_open_box_switch == 'on') {
                	if($request->sd_open_box != null){
                    	$sd_open_box_diff = 1;
                }
            }

            if ($weight_range_up_diff == 1 || $weight_range_down_diff == 1 || $kg_range_diff == 1 || $local_diff == 1 || $national_charges_0_diff == 1 || $national_charges_1_diff == 1 || $national_charges_2_diff == 1 || $national_charges_3_diff == 1 || $booking_type_charges_diff == 1 || $cash_handling_charges_change == 1 || $return_charges_diff == 1 || $fuel_surcharge_diff == 1 || $weight_addition_diff == 1 || $insurance_charges_diff == 1 || $sameday_dws_weight_diff == 1 || $sd_discount_weight_charges_diff == 1 || $sd_open_box_diff == 1) {
                $sameday_changes = 1;
            }
        }


        if ($overnight_changes == 0 && $overland_changes == 0 && $detain_changes == 0 && $sameday_changes == 0 && $warehouse_charges == 0) {
            DwsWeightChargesController::approve($id);

            if ($request->has('wordpress_account') && $request->request_custom_quotations == 0) {
                User::where('id', $id)->update(['status' => 2, 'rates_added_by' => 346, 'rates_authorized_by' => 346, 'rates_approved_at' => Carbon::now(), 'rates_added_at' => Carbon::now(), 'rate_status' => 0, 'request_custom_quotation' => 0, 'on_board_status' => 1]);
                session(['status' => 2]);

            } else if ($request->has('wordpress_account') && $request->request_custom_quotations == 1) {
                User::where('id', $id)->update(['status' => 0, 'rate_status' => 0, 'request_custom_quotation' => 1, 'on_board_status' => 1]);
                NotificationsController::send(231, $id);

            } else {
                User::where('id', $id)->update(['status' => 1, 'rates_added_by' => Auth::id(), 'rates_added_at' => Carbon::now(), 'on_board_status' => 1]);
            }
            
        }


        //Sales Commisssion
        if(!$request->has('wordpress_account')){
            if ($request->has('user_id')) {
                $total_commission = $request->total_commission;
                $users_count = count($request->user_id);
    
                $sales_commission = SalesCommission::where('shipper_id', $shipper_id);
                if ($sales_commission->exists()) {
                    $sales_commission = $sales_commission->first();
                    $sales_commission->commission_users_count = $users_count;
                    $sales_commission->commission = $total_commission;
                    $sales_commission->updated_by = Auth::id();
                    $sales_commission->save();
                    $sales_commission_id = $sales_commission->id;
                    $actual_commission = 0;
                    SalesCommissionUser::where('sales_commission_id', $sales_commission_id)->delete();
                    foreach ($request->tier_id as $row_id => $tier) {
                        $sales_tier = SalesTier::find($tier);
                        if ($sales_tier) {
                            $sales_commission_user = new SalesCommissionUser();
                            $sales_commission_user->sales_commission_id = $sales_commission_id;
                            $sales_commission_user->tier_type_id = $sales_tier->tier_type;
                            $sales_commission_user->tier_id = $tier;
                            if ($sales_tier->tier_type == 1) {
                                if (strpos($request->user_id[$row_id], 'riders') !== false) {                      
                                    $sales_commission_user->user_type = "2";
                                }  
                                $sales_commission_user->user_id = $request->user_id[$row_id];
                            }                        
                            else if ($sales_tier->tier_type == 2) {
                                $external_user = new SalesCommissionExternalUser();
                                $external_user->name = $request->user_id[$row_id];
                                $external_user->shipper_id = $shipper_id;
                                $external_user->save();
                                $sales_commission_user->user_id = $external_user->id;
                            }
                            $sales_commission_user->commission = $request->commission_percentage[$row_id];
                            $actual_commission += $request->commission_percentage[$row_id];
                            $sales_commission_user->save();
                        }
                    }
                    $sales_commission->commission = $actual_commission;
                    $sales_commission->save();
    
                } else {
                    $sales_commission = new SalesCommission();
                    $sales_commission->shipper_id = $shipper_id;
                    $sales_commission->commission_users_count = $users_count;
                    $sales_commission->commission = $total_commission;
                    $sales_commission->updated_by = Auth::id();
                    $sales_commission->save();
                    $sales_commission_id = $sales_commission->id;
                    $actual_commission = 0;
                    foreach ($request->tier_id as $row_id => $tier) {
                        $sales_tier = SalesTier::find($tier);
                        if ($sales_tier) {
                            $sales_commission_user = new SalesCommissionUser();
                            $sales_commission_user->sales_commission_id = $sales_commission_id;
                            $sales_commission_user->tier_type_id = $sales_tier->tier_type;
                            $sales_commission_user->tier_id = $tier;
                            if ($sales_tier->tier_type == 1) {
                                if (strpos($request->user_id[$row_id], 'riders') !== false) {                      
                                    $sales_commission_user->user_type = "2";
                                }  
                                $sales_commission_user->user_id = $request->user_id[$row_id];         
                            } else if ($sales_tier->tier_type == 2) {
                                $external_user = new SalesCommissionExternalUser();
                                $external_user->name = $request->user_id[$row_id];
                                $external_user->shipper_id = $shipper_id;
                                $external_user->save();
                                $sales_commission_user->user_id = $external_user->id;
                            }
                            $sales_commission_user->commission = $request->commission_percentage[$row_id];
                            $actual_commission += $request->commission_percentage[$row_id];
                            $sales_commission_user->save();
                        }
                    }
                    $sales_commission->commission = $actual_commission;
                    $sales_commission->save();
                }
    
            }
        }

        $user =  User::find($id);
        if($request->request_custom_quotations == 1 && $user->lead_id){
            NotificationsController::send(38, $id);
        }

        //Sales Commissison End

        if(!$request->has('wordpress_account')){
            return redirect(route('admin.accounts.pending'))->with('success', 'All Rates are added');
        }
    }


    // public function duplicate_info(Request $request)
    // {
    //     $shipper_id = $request->shipper_id;
    //     $duplicate = DuplicateUser::where('user_id', $shipper_id)->first();
    //     $data = array();
    //     $data['phone'] = ($duplicate->phone) ? $duplicate->phone : '';
    //     $data['cnic'] = ($duplicate->cnic) ? $duplicate->cnic : '';
    //     $data['iban'] = ($duplicate->iban) ? $duplicate->iban : '';
    //     $data['name'] = ($duplicate->name) ? $duplicate->name : '';
    //     return response()->json(['status' => 1, 'info' => $data]);
    // }

    public function duplicate_info(Request $request)
    {
        $shipper_id = $request->shipper_id;
        $duplicate = DuplicateUser::where('user_id', $shipper_id)->first();
        $user = User::where('id', $shipper_id)->with('bank')->first();
    
        if (!$duplicate) {
            $duplicate = (object) [
                'phone' => null,
                'cnic' => null,
                'name' => null,
                'iban' => null
            ];
        }
    
        if (!$user) {
            $user = (object) [
                'ntn_no' => null,
                'email' => null
            ];
        }
    
        // Set null values to empty strings
        $duplicate->phone = $duplicate->phone ?? '';
        $duplicate->cnic = $duplicate->cnic ?? '';
        $duplicate->name = $duplicate->name ?? '';
        $duplicate->iban = $duplicate->iban ?? '';
        $user->ntn_no = $user->ntn_no ?? '';
        $user->email = $user->email ?? '';
    
        $data = array();
        $data['phone'] = ($duplicate->phone) ? $duplicate->phone : '';
        $data['cnic'] = ($duplicate->cnic) ? $duplicate->cnic : '';
        $data['name'] = ($duplicate->name) ? $duplicate->name : '';
        $data['iban'] = ($duplicate->iban) ? $duplicate->iban : '';
        $data['ntn'] = ($user->ntn_no) ? $user->ntn_no : '';
        $data['email'] = ($user->email) ? $user->email : '';
    
        // Get user IDs with same phone number
        $similarUsersPhone = User::where('phone', $duplicate->phone)
            ->where('id', '!=', $shipper_id)
            ->where('created_at', '<', $user->created_at)
            ->pluck('id')
            ->toArray();

        // $similarUsersPhone = User::whereNotNull('phone')
        //     ->where('phone', '!=', '')
        //     ->where('phone', $duplicate->phone)
        //     ->where('id', '!=', $shipper_id)
        //     ->where('created_at', '<', $user->created_at)
        //     ->pluck('id')
        //     ->toArray();
    
        // Get user IDs with same CNIC
        // $similarUsersCnic = User::where('cnic', $duplicate->cnic)
        //     ->where('id', '!=', $shipper_id)
        //     ->where('created_at', '<', $user->created_at)
        //     ->pluck('id')
        //     ->toArray();

        $similarUsersCnic = User::whereNotNull('cnic')
            ->where('cnic', '!=', '')
            ->where('cnic', $duplicate->cnic)
            ->where('id', '!=', $shipper_id)
            ->where('created_at', '<', $user->created_at)
            ->pluck('id')
            ->toArray();

        // Get user IDs with same name

        // $similarUsersName = DuplicateUser::where('name', $duplicate->name)
        // $similarUsersName = [];
        // if ($duplicate->name) 
        // {
        //     $similarUsersName = DuplicateUser::where('name', 'like', '%' . $duplicate->name . '%')
        //     // ->where('user_id', '=', $shipper_id)
        //     ->where('created_at', '<', $user->created_at)
        //     ->pluck('user_id')
        //     ->toArray();
        // }
        $similarUsersName = [];
        if ($duplicate->name) {
            $similarUsersName = DuplicateUser::where('name', 'like', '%' . $duplicate->name . '%')
                ->where(function ($query) use ($user) {
                    $query->where('created_at', '<', $user->created_at)
                        ->orWhere('user_id', $user->id); // Include the current user
                })
                ->pluck('user_id')
                ->toArray();
        }

        // Get all IBANs associated with the user
        $ibanCollection = DuplicateUser::where('user_id', $user->id)
        ->pluck('iban')
        ->toArray();

        // Get user IDs with same IBANs
        $similarUsersIban = DuplicateUser::whereIn('iban', $ibanCollection)
        ->where('user_id', '!=', $shipper_id)
        ->pluck('user_id')
        ->toArray();

        // Get the duplicated IBANs
        $duplicatedIbans = DuplicateUser::whereIn('user_id', $similarUsersIban)
        ->whereIn('iban', $ibanCollection)
        ->pluck('iban')
        ->toArray();

        // Get user IDs with same NTN
        $similarUsersNtn = [];
        if ($user->ntn_no) {
            $similarUsersNtn = User::where('ntn_no', $user->ntn_no)
                ->where('id', '!=', $shipper_id)
                ->where('created_at', '<', $user->created_at)
                ->pluck('id')
                ->toArray();
        }
    
        // Get user IDs with same Email
        $similarUsersEmail = User::where('email', $user->email)
            ->where('id', '!=', $shipper_id)
            ->groupBy('email') // Group by email to find duplicates
            ->havingRaw('COUNT(email) > 1') // Only select emails that have duplicates
            ->pluck('email')
            ->toArray();
    
        $data['shared_phone'] = implode(', ', $similarUsersPhone);
        $data['shared_cnic'] = implode(', ', $similarUsersCnic);
        $data['shared_name'] = implode(', ', $similarUsersName);
        $data['shared_iban'] = implode(', ', $similarUsersIban);
        $data['shared_ntn_no'] = !empty($similarUsersNtn) ? implode(', ', $similarUsersNtn) : '';
        $data['shared_email'] = !empty($similarUsersEmail) ? implode(', ', $similarUsersEmail) : '';
        $data['duplicated_ibans'] = implode(', ', array_unique($duplicatedIbans));

        return response()->json(['status' => 1, 'info' => $data]);
    }
    
    public function activeAccountListAjax(Request $request)
    {        

        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 62);
        }

        // $usersWithSameNtn = User::whereNotNull('ntn_no')
        //     ->select('ntn_no', DB::raw('COUNT(*) as count'))
        //     ->groupBy('ntn_no')
        //     ->havingRaw('COUNT(*) > 1')
        //     ->pluck('ntn_no')
        //     ->toArray();
        
        $duplicateEmailCount = User::whereNotNull('email')
            ->select('email', DB::raw('COUNT(*) as count'))
            ->groupBy('email')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('email')
            ->toArray();

        $users = DB::connection('mysql')->table('users')->join('cities', 'users.city_id', '=', 'cities.id')
            ->leftjoin('products as p', 'p.id', '=', 'users.product_id')
            ->leftjoin('sub_category_segments as seg_sub', 'seg_sub.id', '=', 'users.sub_segment_id')
            ->leftjoin('referrals as ref', 'ref.id', '=', 'users.referral_id')
            ->leftjoin('segments as seg', 'seg.id', '=', 'users.segment_id')
            ->leftjoin('admins as rab', 'rab.id', '=', 'users.rates_added_by')
            ->leftjoin('admins as rabna', 'rabna.id', '=', 'users.rates_updated_by')
            ->leftjoin('admins as rabb', 'rabb.id', '=', 'users.rates_authorized_by')
            ->leftjoin('admins as rrb', 'rrb.id', '=', 'users.rates_rejected_by')
            ->leftjoin('admins as rabba', 'rabba.id', '=', 'users.account_activated_by')
            ->leftjoin('account_types as at', 'at.id', '=', 'users.account_type_id')
            ->leftjoin('sale_person_tags as spt', function ($join) {
                $join->on('spt.user_id', '=', 'users.id')
                    ->leftjoin('admins as ad', 'ad.id', '=', 'spt.admin_id')
                    ->where('spt.status', '=', 0);
            })
            ->leftjoin('duplicate_users as du', 'du.user_id', '=', 'users.id')
            ->leftjoin('international_users_informations as iui', 'iui.user_id', '=', 'users.id')
            ->leftjoin('user_document_attachments as uda', 'uda.user_id', '=', 'users.id')
            ->leftjoin('admins as dab', 'dab.id', '=', 'uda.approved_by')
            ->leftjoin('admins as drb', 'drb.id', '=', 'uda.rejected_by')
            ->leftjoin('sale_tier_tags as st', 'st.user_id', '=', 'users.id')
            ->leftjoin('sales_commissions as sc', 'sc.shipper_id', '=', 'users.id')
            ->leftjoin('sales_commission_users as scu', 'sc.id', '=', 'scu.sales_commission_id')
            ->leftjoin('admins as scun', 'scun.id', '=', 'scu.user_id')
            ->leftjoin('riders as scun_r', 'scun_r.id', '=', 'scu.user_id')
            ->leftjoin('admins as poc', 'poc.id', '=', 'st.poc')
            ->leftjoin('admins as k', 'k.id', '=', 'st.kam')
            ->leftjoin('admins as r', 'r.id', '=', 'st.ref')
            ->leftjoin('admins as e', 'e.id', '=', 'st.eso')
            ->leftjoin('territories as t', 't.id', '=', 'users.territory_id')
            ->leftjoin('user_check_statuses as ucs', 'ucs.user_id', '=', 'users.id')
            ->leftjoin('zones as z','cities.zone_id','=','z.id')
            ->leftjoin('payment_cycles as pc', 'pc.id', '=', 'users.payment_cycle_id')
            ->leftjoin('block_disable_reason_users as bdru', 'bdru.id', '=', 'users.disable_reason_1')
            ->leftjoin('faf_charges', function($join){
                $join->on('faf_charges.user_id', '=', 'users.id')->where('faf_charges.status', '=', 1);
            })
            ->leftJoin('wallet_users', 'wallet_users.user_id', 'users.id')

            // ->select(['users.ntn_no', 'users.blacklist', 'rrb.name as rates_rejected_by', 'users.disable_at as disable_at', 'users.rates_added_at as rates_added_at', 'users.rates_approved_at as rates_approved_at', 'users.rates_rejected_at as rates_rejected_at', 'users.disable_reason as disable_reason', 'users.rejected_reason as rejected_reason', 'users.rate_status as rate_status', 'users.id', 'ad.name as admin_tag_id', 'users.name', 'cities.name as city', 'users.poc', 'p.product_name as product_type', 'rab.name as added_by', 'rabna.name as updated_by', 'users.created_at', 'rabb.name as approved_by', 'rabba.name as account_activated_by', 'users.activated_at as activated_date', 'users.status', 'users.account_type_id', 'at.name as account_type', 'users.documents_status', 'users.documents_status_reason as documents_rejection_reason', 'users.other_product_name', 'users.auto_shipment_cancellation_days', 'du.phone as duplicate_phone', 'du.cnic as duplicate_cnic', 'du.iban as duplicate_iban', 'du.name as duplicate_name', 'users.brand_name as brand_name', 'iui.status as international_rate_status', 'iui.rejected_reason as international_rejected_reason', 'uda.uploaded_at as documents_uploaded_at', 'uda.approved_at as documents_approved_at', 'dab.name as documents_approved_by', 'drb.name as documents_rejected_by', 'uda.rejected_at as documents_rejected_at', 'poc.name as tagged_poc', 'k.name as kam', 'r.name as ref','r.trax_id as rider_id', 'users.address as address', 'users.email', 't.name as territory', 'users.corporate_rate_type_id as corporate_rate_type_id', 'users.new_rate_type_id as new_rate_type_id', 'seg.name as segment', 'seg_sub.name as sub_segment', 'ref.name as referral_name','ucs.status_count as status_count','z.name as zone', 'pc.id as payment_cycle_id','pc.name as payment_cycle','users.payment_cycle_days as payment_cycle_days','e.name as eso','scun.name as search','scun_r.name as search_user_type','users.lead_id', 'users.average_shipments', 'bdru.name as reason','users.sms_charges','faf_charges.status as fc_status'])

            ->select([
                'users.ntn_no',
                'users.blacklist',
                'rrb.name as rates_rejected_by',
                'users.disable_at',
                'users.rates_added_at',
                'users.rates_approved_at',
                'users.rates_rejected_at',
                'users.disable_reason',
                'users.rejected_reason',
                'users.rate_status',
                'users.id',
                'ad.name as admin_tag_id',
                'users.name',
                'cities.name as city',
                'users.poc',
                'p.product_name as product_type',
                'rab.name as added_by',
                'rabna.name as updated_by',
                'users.created_at',
                'rabb.name as approved_by',
                'rabba.name as account_activated_by',
                'users.activated_at as activated_date',
                'users.status',
                'users.account_type_id',
                'at.name as account_type',
                'users.documents_status',
                'users.documents_status_reason as documents_rejection_reason',
                'users.other_product_name',
                'users.auto_shipment_cancellation_days',
                'du.phone as duplicate_phone',
                'du.cnic as duplicate_cnic',
                'du.iban as duplicate_iban',
                'du.name as duplicate_name',
                'users.brand_name',
                'iui.status as international_rate_status',
                'iui.rejected_reason as international_rejected_reason',
                'uda.uploaded_at as documents_uploaded_at',
                'uda.approved_at as documents_approved_at',
                'dab.name as documents_approved_by',
                'drb.name as documents_rejected_by',
                'uda.rejected_at as documents_rejected_at',
                'poc.name as tagged_poc',
                'k.name as kam',
                'r.name as ref',
                'r.trax_id as rider_id',
                'users.address',
                'users.email',
                't.name as territory',
                'users.corporate_rate_type_id',
                'users.new_rate_type_id',
                'seg.name as segment',
                'seg_sub.name as sub_segment',
                'ref.name as referral_name',
                'ucs.status_count',
                'z.name as zone',
                'pc.id as payment_cycle_id',
                'pc.name as payment_cycle',
                'users.payment_cycle_days',
                'e.name as eso',
                'scun.name as search',
                'scun_r.name as search_user_type',
                'users.lead_id',
                'users.average_shipments',
                'bdru.name as reason',
                'users.sms_charges',
                'faf_charges.status as fc_status',
                'wallet_users.user_id as wallet_shippers'
            ])
            ->where(function($query){
                $idsToExclude = FilterTrait::class::getFilteredIds(auth()->user()->id);
                if (!empty($idsToExclude)) {
                    $query->whereNotIn('users.id', $idsToExclude);
                }
            })
            ->whereIn('users.status', [3, 4, 6])
            ->where('users.blacklist', 0)
            ->groupBy('users.id');
        if (session('role_id') != 1) {
            $users = $users->whereIn('cities.hub_id', session('hubs'));
        }

        if (session('department_id') == 7) {
            if (!in_array(session('id'), session('sale_users_bypass'))) {
                $users = $users->whereIn('users.id', session('tagged_shippers'));
            }
        }

        if ($sale_persons = $request->get('sale_persons')) {
            $users = $users->where('ad.id', $sale_persons);
        }

        if ($search_cnic = $request->get('search_cnic')) {
            $users = $users->where('users.cnic', $search_cnic);
        }
        if ($search_shipper = $request->get('search_shipper')) {
            $users = $users->whereIn('users.id', $search_shipper);
        }

        if ($search_iban = $request->get('search_iban')) {
            $users = $users->leftjoin('user_bank_infos as ubi', function ($join) use ($search_iban) {
                $join->on('ubi.user_id', '=', 'users.id')
                    ->where('ubi.iban', $search_iban);
            });
        }
        if ($search_email = $request->get('search_email')) {
            $users = $users->where('users.email', $search_email);
        }

        return Datatables::of($users)
            ->addColumn('days_to_disable', function ($user) {
                if ($user->disable_at == null)
                {
                    $disable_date = 0;
                      return $disable_date;
                }
                elseif ($user->disable_at != null)
                {
                    $disable_date = strtotime($user->disable_at);
                    $current_date = strtotime(date('Y-m-d h:i:s'));

                    $timeDiff = abs($current_date - $disable_date);
                    $numberDays = $timeDiff/86400;
                    $numberDays = intval($numberDays);

                    return $numberDays;
                }

            })
            ->addColumn('lead_id_link', function($user) {
                
                if($user->lead_id)
                {
                    return '<a href="' . route('admin.leads.view_remarks',['id' =>$user->lead_id]) . '" style="text-decoration: underline;" target="_blank">' . $user->lead_id . '</a>';

                }  else {
                   return '';
                }

            })
            ->addColumn('id_padded', function ($user) {
                return str_pad($user->id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('rate_status', function ($users) {
                if ($users->rate_status == 0) {
                    return "Approved";
                } elseif ($users->rate_status == 1) {
                    return "Requested";
                } else {
                    return "Rejected";
                }
            })->editColumn('disable_reason', function ($users) {
                if ($users->disable_reason != null) {
                    return $users->disable_reason;
                } else {
                    return "-";
                }
            })
            ->filterColumn('users.id', function ($query, $keyword) {
                return $query->where('users.id', '=', $keyword);
            })
            ->editColumn('status', function ($users) {
                if ($users->status == 3) {
                    return "Enable";
                } else if($users->status == 6) {
                    return "Booking Paused";
                }
                else{
                    return "Disable";
                }
            })
            ->editColumn('documents_status', function ($users) {
                if ($users->documents_status == 0) {
                    return "Incomplete";
                } elseif ($users->documents_status == 1) {
                    return "Pending for Approval";
                } elseif ($users->documents_status == 2) {
                    return "Approved";
                } elseif ($users->documents_status == 3) {
                    return "Rejected";
                }
            })
            ->editColumn('fc_status', function ($users) {
                return $users->fc_status ? 'Yes' : 'No';
            })
            ->filterColumn('faf_charges.status', function ($query, $keyword) {
                if($keyword)
                {
                    return $query->whereNotNull('faf_charges.status');
                }
                else
                {
                    return $query->whereNull('faf_charges.status');
                }
            })
            ->editColumn('rejected_reason', function ($users) {
                if ($users->rejected_reason != null && $users->rate_status == 2) {
                    return $users->rejected_reason;
                } else {
                    return "-";
                }
            })
            ->editColumn('eso', function ($users) {
                $sales_tiers = DB::table('sales_tiers')->where('tier_name', 'LIKE', '%ESO%')->orWhere('tier_name', 'LIKE', '%eso%')->first()->id ?? null;
                $shipper = DB::table('sales_commissions')->where('shipper_id', $users->id)->first();
                
                if (isset($shipper, $sales_tiers)) {
                    $sales_commission_users = DB::table('sales_commission_users')
                        ->where(['user_type' => '2', 'tier_id' => $sales_tiers, 'sales_commission_id' => $shipper->id])
                        ->get();
                
                    if ($sales_commission_users->isNotEmpty()) {
                        $rider_names = [];
                        foreach ($sales_commission_users as $sales_commission_user) {
                            $rider = Rider::find($sales_commission_user->user_id);
                            if ($rider) {
                                $rider_names[] = $rider->name;
                            } else {
                                return '-';
                            }
                        }
                        $rider_name = implode(', ', $rider_names);
                        return $rider_name;
                    } else {
                        return '-';
                    }
                } else {
                    return '-';
                }

            })

            ->editColumn('tagged_poc', function ($users) {
                $sales_tiers = DB::table('sales_tiers')->where('tier_name', 'LIKE', '%POC%')->orWhere('tier_name', 'LIKE', '%poc%')->first()->id ?? null;
                $shipper = DB::table('sales_commissions')->where('shipper_id', $users->id)->first();
                
                if (isset($shipper, $sales_tiers)) {
                    $sales_commission_users = DB::table('sales_commission_users')
                        ->where(['tier_id' => $sales_tiers, 'sales_commission_id' => $shipper->id])
                        ->get();
                    if ($sales_commission_users->isNotEmpty()) {
                        $array = [];
                        foreach ($sales_commission_users as $sales_commission_user) {
                            $type = $sales_commission_user->user_type;
                            $admins = ($type == 1) ? Admin::find($sales_commission_user->user_id) : Rider::find($sales_commission_user->user_id);
                            if ($admins) {
                                $array[] = $admins->name;
                            } else {
                                return '-';
                            }
                        }
                        $array = implode(', ', $array);
                        return $array;
                    } else {
                        return $users->tagged_poc;
                    }
                } else {
                    return $users->tagged_poc;
                }

            })

            ->editColumn('ref', function ($users) {
                $sales_tiers = DB::table('sales_tiers')->where('tier_name', 'LIKE', '%REF%')->orWhere('tier_name', 'LIKE', '%ref%')->first()->id ?? null;
                $shipper = DB::table('sales_commissions')->where('shipper_id', $users->id)->first();
                
                if (isset($shipper, $sales_tiers)) {
                    $sales_commission_users = DB::table('sales_commission_users')
                        ->where(['tier_id' => $sales_tiers, 'sales_commission_id' => $shipper->id])
                        ->get();
                
                    if ($sales_commission_users->isNotEmpty()) {
                        $array = [];
                        foreach ($sales_commission_users as $sales_commission_user) {
                            $type = $sales_commission_user->user_type;
                            $admins = ($type == 1) ? Admin::find($sales_commission_user->user_id) : Rider::find($sales_commission_user->user_id);
                            if ($admins) {
                                $array[] = $admins->name;
                            } else {
                                return '-';
                            }
                        }
                        $ref = explode(', ', $users->ref);
                        $new_array = array_unique(array_merge($array, $ref));
                        $new_array = implode(', ', $new_array);
                        
                        $array = implode(', ', $array);
                        return $array;
                    } else {
                        if($users->rider_id)
                        {
                            return $users->rider_id.'-'.$users->ref;
                        }
                        return '-';
                    }
                } else {
                    if($users->rider_id)
                    {
                        return $users->rider_id.'-'.$users->ref;
                    }
                    return '-';
                }
            })
            ->filterColumn('r.name', function ($query, $keyword) {
                $query->where('r.name', $keyword)
                ->orWhere('scun.name', $keyword)->orWhere('scun_r.name', $keyword);
            })


            ->filterColumn('k.name', function ($query, $keyword) {
                $query->where('k.name', $keyword);
            })

  
            ->filterColumn('poc.name', function ($query, $keyword) {
                $query->where('poc.name', $keyword)
                ->orWhere('scun.name', $keyword)->orWhere('scun_r.name', $keyword);
            })

            ->filterColumn('status', function ($query, $keyword) {
                if ($keyword == 3 || $keyword == 4 || $keyword == 6) {
                    $query->where('users.status', '=', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('users.payment_cycle_days', function ($query, $keyword) {                
                $keywordLower = strtolower($keyword);
                
                $paymentCycleDays = self::$paymentCycleDays;
                if (str_replace(['e', 'v', 'r', 'y','w','k','d','a'], '', $keywordLower) === '') {
                    $query->whereIn('pc.id', [2, 4, 5]);
                } else {
                    $keywordFound = [];
                
                    foreach ($paymentCycleDays as $key => $dayMap) {
                        if (stripos($dayMap, $keywordLower) !== false) {
                            $keywordFound[] = $key;
                        }
                    }
                
                    if (count($keywordFound) > 0) {
                        $query->whereRaw("FIND_IN_SET(?, users.payment_cycle_days) > 0", [$keywordFound])->whereNotIn('pc.id', [1, 3, 6]);
                    } else if (is_numeric($keyword) || is_numeric($keyword . 'rd') || is_numeric($keyword . 'nd') || is_numeric($keyword . 'th')) {
                        $keyword = preg_replace("/[^0-9]/", "", $keyword);
                        $query->whereRaw("FIND_IN_SET(?, users.payment_cycle_days) > 0", [$keyword])->whereNotIn('pc.id', [2, 4, 5]);
                    } else {
                        $query->whereRaw('false');
                    }
                }
            })
            ->editColumn('product_type', function ($user) {
                if ($user->product_type == 'Other') {
                    return $user->other_product_name;
                } else {
                    return $user->product_type;
                }
            })
            ->filterColumn('product_type', function ($query, $keyword) {

                if ($keyword != '' || $keyword != 24) {
                    $query->where('p.id', $keyword);
                } else {
                    $query->whereRaw('false');
                }   
            })
            ->addColumn('duplication', function ($users)  use ($request, $duplicateEmailCount){
                
                $count = 0;

                $UniqueNtnCount = User::whereNotNull('ntn_no')
                    ->select('ntn_no', DB::raw('COUNT(*) as count'), 'users.id')
                    ->groupBy('ntn_no')
                    // ->havingRaw('COUNT(*) > 1')
                    ->distinct()
                    ->pluck('users.id')
                    ->toArray();

                if (!is_null($users->ntn_no) && !in_array($users->id, $UniqueNtnCount)) {
                    $count++;
                }
                
                if (in_array($users->email, $duplicateEmailCount)) {
                    $count++;
                }

                if ($users->duplicate_phone != null) {
                    $count++;
                }
                if ($users->duplicate_cnic != null) {
                    $count++;
                }

                if ($users->duplicate_iban != null) {
                    $count++;
                }

                if ($users->duplicate_name != null) {
                    $count++;
                }
               
                if ($count > 0 && !$request->get('excel')) {
                    return '<button class="btn btn-sm btn-outline-info align-middle duplicate_modal">' . $count . '</button>';
                } else {
                    return $count;
                }
            })


            
            ->editColumn('international_rate_status', function ($users) {
                if ($users->international_rate_status != null) {
                    if ($users->international_rate_status == 1) {
                        return "Approved";
                    } elseif ($users->international_rate_status == 2) {
                        return "Requested";
                    } elseif ($users->international_rate_status == 3) {
                        return "Rejected";
                    } elseif ($users->international_rate_status == 4) {
                        return "Requested";
                    } elseif ($users->international_rate_status == 5) {
                        return "Rejected";
                    }
                } else {
                    return "International Rates are not set";
                }
            })
            ->editColumn('international_rejected_reason', function ($users) {
                if ($users->international_rejected_reason != null && $users->international_rate_status == 3) {
                    return $users->international_rejected_reason;
                } else {
                    return "-";
                }
                
            })
            ->editColumn('id_padded', function ($users) {
                $route = route('admin.accounts.view.profile', ['id' => $users->id]);
                return '<a href="' . $route . '" style="text-decoration: underline;">' . $users->id . '</a>';
            })
            ->editColumn('international_rejected_reason', function ($users) {
                if ($users->international_rejected_reason != null && $users->international_rate_status == 3) {
                    return $users->international_rejected_reason;
                } else {
                    return "-";
                }
            })->editColumn('payment_cycle_days', function ($pending_payment) {
                $payment_cycle = $pending_payment->payment_cycle_id;
                $payment_cycle_days = $pending_payment->payment_cycle_days;
               
            
                if ($payment_cycle == 2 || $payment_cycle == 4 || $payment_cycle == 5) {//Weekiy, Twice A Week And Thrice A Week.
                    $payment_cycle_days = explode(',', $payment_cycle_days);
                    $dayMap = self::$paymentCycleDays;
                    $cycleText = AdminFinanceController::getCycleText($payment_cycle_days, $dayMap);
                
                    return $cycleText;
                }
            
                if (($payment_cycle == 3 || $payment_cycle == 6) && $payment_cycle_days != '0') {//Monthly And Fortnightly
                    $payment_cycle_days = explode(',', $payment_cycle_days);
                    if (count($payment_cycle_days) == 1) {
                        $day = (int)$payment_cycle_days[0];
                        return AdminFinanceController::getDayOfMonthText($day);
                    } elseif (count($payment_cycle_days) == 2) {
                        $day1 = (int)$payment_cycle_days[0];
                        $day2 = (int)$payment_cycle_days[1];
                        return AdminFinanceController::getDayOfMonthText($day1) . " And " . AdminFinanceController::getDayOfMonthText($day2);
                    }
                }else{
                    return '-';
                }
            
                if ($payment_cycle == 1) {// Daily
                    return '-';
                }
            })
            ->addColumn('expected_average_shipments', function ($users){
                return $users->average_shipments;
            })
            ->filterColumn('users.average_shipments', function ($query, $keyword) {
                return $query->where('users.average_shipments', '=', $keyword);
            })
            ->editColumn('wallet_shippers', function($user){
                return $user->wallet_shippers ? "Fintech" : "Normal";
            })
            ->filterColumn('wallet_shippers', function($query, $keyword){
                if($keyword == 1) {
                    $query->whereNotNull('wallet_users.user_id');
                } else if($keyword == 2) {
                    $query->whereNull('wallet_users.user_id');
                }
            })
            ->addColumn("action", function ($result) {
                if ($result->id != 8761 && $result->id != 9358) {
                    if (in_array($result->id, session('tagged_shippers'))) {
                        $multiple_sale_check = true;
                    } else {
                        $multiple_sale_check = false;
                    }

                    $sale_check = SalePersonTag::where('user_id', $result->id)->first();
                    $dropdown = '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm accounts">
                ';

                    if (session('role_id') == 1 || in_array(361, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item remove_sales_tier"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-minus-circle"></i></div><div class="col-9 offset-1">Remove Sales Tier Tagging</div></button>';
                    }

                    $dropdown .= '<button type="button" class="dropdown-item" data-target-id="' . $result->id . '" data-toggle="modal" data-target="#BankInfoModal"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Bank Info</div></button>';

                    $dropdown .= '<button type="button" class="dropdown-item" data-target-id="' . $result->id . '" data-toggle="modal" data-target="#ShippingInfoModal"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Shipping Info</div></button>';
                    if ($result->status == 3 && (session('role_id') == 1 || in_array(session('id'), session('sale_users_bypass')))) {
                        $dropdown .= '<button type="button" class="dropdown-item" data-target-id="' . $result->id . '" data-toggle="modal" data-target="#SalesTagModal"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Sales Person</div></button>';
                    }
                    if ($result->account_type_id == 1) {
                        if (RateStatus::where('user_id', $result->id)->exists() && (session('role_id') == 1 || in_array(12, session('permissions')))) {
                            if ($result->rate_status == 0 || $result->rate_status == 2 || session('role_id') == 1 || in_array(session('id'), session('sale_users_bypass')) || session('role_id') == 2 || session('role_id') == 7 || in_array(session('id'), session('sale_users_bypass'))) {
                                $dropdown .= '<button onclick="window.open(\'' . route('admin.edit.rates', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit Rates</div></button>';
                            }
                        }
                        if (RateStatus::where('user_id', $result->id)->exists() && (session('role_id') == 1 || in_array(115, session('permissions')))) {
                            $dropdown .= '<button onclick="window.open(\'' . route('admin.view.rates', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Rates</div></button>';
                        }
                        if ((session('role_id') == 1 || in_array(115, session('permissions')))) {
                            $dropdown .= '<button type="button" class="dropdown-item rates_history" data-target-id=' . $result->id . ' rel="rates_history" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Rates History</div></button>';
                        }

                    } else {
                        if ($result->corporate_rate_type_id != 3 && $result->new_rate_type_id == null) {
                            if (CorporateRateStatus::where('user_id', $result->id)->exists() && (session('role_id') == 1 || in_array(12, session('permissions')))) {
                                $dropdown .= '<button onclick="window.open(\'' . route('admin.corporate.edit.rates', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit Rates</div></button>';
                            }
                        } else if ($result->corporate_rate_type_id == 3 && $result->new_rate_type_id == null) {
                            if (CorporateDefaultRateStatus::where('user_id', $result->id)->exists() && (session('role_id') == 1 || in_array(12, session('permissions')))) {
                                $dropdown .= '<button onclick="window.open(\'' . route('admin.corporate.default.edit.rates', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit Rates</div></button>';
                            }
                        } else if ($result->corporate_rate_type_id != null && ($result->new_rate_type_id == 1 || $result->new_rate_type_id == 2)) {
                            if (PendingCorporateRateStatus::where('user_id', $result->id)->exists() && (session('role_id') == 1 || in_array(12, session('permissions')))) {
                                $dropdown .= '<button onclick="window.open(\'' . route('admin.corporate.edit.rates', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit Rates</div></button>';
                            }
                        } else {
                            if (PendingCorporateDefaultRateStatus::where('user_id', $result->id)->exists() && (session('role_id') == 1 || in_array(12, session('permissions')))) {
                                $dropdown .= '<button onclick="window.open(\'' . route('admin.corporate.default.edit.rates', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit Rates</div></button>';
                            }
                        }
                        if ($result->corporate_rate_type_id == 3) {
                            if (CorporateDefaultRateStatus::where('user_id', $result->id)->exists() && (session('role_id') == 1 || in_array(115, session('permissions')))) {
                                $dropdown .= '<button onclick="window.open(\'' . route('admin.corporate.default.rates.view', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Rates</div></button>';
                            }
                        } else {
                            if (CorporateRateStatus::where('user_id', $result->id)->exists() && (session('role_id') == 1 || in_array(115, session('permissions')))) {
                                $dropdown .= '<button onclick="window.open(\'' . route('admin.corporate.view.rates', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Rates</div></button>';
                            }
                        }

                        if ((session('role_id') == 1 || in_array(115, session('permissions')))) {
                            $dropdown .= '<button type="button" class="dropdown-item rates_history" data-target-id=' . $result->id . ' rel="rates_history" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Rates History</div></button>';
                        }

                    }
                    if ((session('role_id') == 1 || in_array(487, session('permissions')))) {
                        if ($result->account_type_id == 2 && $result->corporate_rate_type_id != null) {
                            $dropdown .= '<button type="button" class="dropdown-item change_rate_type" rel="block"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-user-x "></i></div><div class="col-9 offset-1">Change Rate Type</div></button>';
                        }
                    }
                    if ((session('role_id') == 1 || session('department_id') == 4)) {
                        if (CorporateUserPackagingInvoiceLog::where('user_id', $result->id)->exists()) {
                            $dropdown .= '<button type="button" class="dropdown-item view_invoice_log" rel="block"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-user-x "></i></div><div class="col-9 offset-1">View Packaging Invoice Log</div></button>';
                        }
                    }

                    if ($result->blacklist == 0 && (session('role_id') == 1 || in_array(14, session('permissions')))) {
                        $dropdown .= '<button type="button" class="dropdown-item blacklist" data-id="' . $result->id . '" rel="block"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-user-x"></i></div><div class="col-9 offset-1">Block</div></div></button>';
                    }

                    if (session('role_id') == 1 || in_array(13, session('permissions'))) {
                        if ($result->status == 3) {
                            $dropdown .= '<button type="button" class="dropdown-item userdisable" data-id="' . $result->id . '"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-user-minus"></i></div><div class="col-9 offset-1">Disable</div></button>';

                        } else {
                            $dropdown .= '<button type="button" class="dropdown-item userenable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-user-plus"></i></div><div class="col-9 offset-1">Enable</div></button>';

                        }
                    }
                    if (session('role_id') == 1 || in_array(244, session('permissions'))) {
                        if ($result->status > 0) {
                            $dropdown .= '<button onclick="window.open(\'' . route('admin.accounts.view_crf_agreement', ['id' => $result->id]) . '\')" type="button" class="dropdown-item view_crf" data-target-id="' . $result->id . '"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View CRF</div></button>';
                        }
                    }

                    if (session('role_id') == 1 || in_array(110, session('permissions'))) {
                        $dropdown .= '<button onclick="window.open(\'' . route('admin.accounts.view.profile', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Profile</div></button>';
                    }

                    $merged = MergedSisterAccount::where('user_id', $result->id);
                    if (!$merged->exists()) {
                        if (session('role_id') == 1 || in_array(session('id'), session('sale_users_bypass')) || (($multiple_sale_check == true) || (($sale_check) && ($sale_check->admin_id == Auth::id())) || in_array(241, session('permissions')))) {
                            $dropdown .= '<button onclick="window.open(\'' . route('admin.accounts.sister_account.add.account', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Add Sister Account</div></button>';
                        }
                    }

                    $shippers = GlobalSettings::where('type','mms_setting')->select('text')->first();
                    if($shippers){
                        $shippers = explode(',', $shippers->text);
                        
                        if(in_array($result->id,$shippers) && session('role_id') == 1 || in_array(873, session('permissions')))
                        {
                            $dropdown .= '<button onclick="window.open(\'' . route('admin.accounts.substitute_account_management.index', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Special Dashboard Account</div></button>';
                        }
                    }

                    $dropdown .= '<button onclick="window.open(\'' . route('admin.accounts.documents', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Documents</div></button>';
                    if (session('role_id') == 1 || in_array(149, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item shipment_days_button"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Auto Shipment Cancel Days</div></button>';
                    }
                    if ($sale_check) {
                        $dropdown .= '<button onclick="window.open(\'' . route('admin.accounts.add_contacts', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Add Contacts</div></button>';
                    }
                    if (session('role_id') == 1 || in_array(365, session('permissions'))) {
                        // hide this because change in payment cycle scenario when register shipper, now this should be change similarly while edit
                        $dropdown .= '<button type="button" class="dropdown-item payment_cycle"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-activity"></i></div><div class="col-9 offset-1">Payment Cycle</div></button>';
                    }
                    if ((!InternationalUsersInformation::where('user_id', $result->id)->exists()) && (session('role_id') == 1 || in_array(439, session('permissions')))) {
                        $dropdown .= '<button onclick="window.open(\'' . route('admin.international.rates.update.index', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-bar-chart"></i></div><div class="col-9 offset-1">Intl Add Rates</div></button>';
                    } else {
                        if (session('role_id') == 1 || in_array(439, session('permissions'))) {
                            $dropdown .= '<button onclick="window.open(\'' . route('admin.international.rates.update.index', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-bar-chart"></i></div><div class="col-9 offset-1">Intl Edit Rates</div></button>';
                        }
                        if (session('role_id') == 1 || in_array(440, session('permissions'))) {
                            $dropdown .= '<button onclick="window.open(\'' . route('admin.international.rates.view.index', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-eye"></i></div><div class="col-9 offset-1">Intl View Rates</div></button>';
                        }
                        if ((session('role_id') == 1 || in_array(590, session('permissions'))) && $result->account_type_id == 2) {
                            $dropdown .= '<button type="button" class="dropdown-item credit_limit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-bar-chart"></i></div><div class="col-9 offset-1">Intl Credit Limit</div></button>';
                        }
                    }

                    if (InternationalEconomyRateStatus::where('user_id', $result->id)->doesntExist()) {
                        if (session('role_id') == 1 || in_array(528, session('permissions'))) {
                            $dropdown .= '<button onclick="window.open(\'' . route('admin.international.rates.economy.create', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-bar-chart"></i></div><div class="col-9 offset-1">Intl Add Economy Rates</div></button>';
                        }
                    } else {
                        if ((session('role_id') == 1 || in_array(528, session('permissions')))) {
                            $dropdown .= '<button onclick="window.open(\'' . route('admin.international.rates.economy.create', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-bar-chart"></i></div><div class="col-9 offset-1">Intl Edit Economy Rates</div></button>';
                        }
                    }

                    if (InternationalEconomyRate::where('user_id', $result->id)->count() > 0) {
                        if (session('role_id') == 1 || in_array(530, session('permissions'))) {
                            $dropdown .= '<button onclick="window.open(\'' . route('admin.international.rates.economy.create', ['id' => $result->id, 'view' => 'view']) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-bar-chart"></i></div><div class="col-9 offset-1">Intl View Economy Rates</div></button>';
                        }
                    }

                    if ($result->account_type_id == 2 && (session('role_id') == 1 || count(array_intersect([598, 599], session('permissions'))) !== 0)) {
                        $dropdown .= '<button onclick="window.open(\'' . route('admin.corporate.reimbursement_setting.index', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-bar-chart"></i></div><div class="col-9 offset-1">Corporate Reimbursement Setting</div></button>';
                    }

                    if (session('role_id') == 1 || in_array(619, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item restrict_order_id"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Restrict Order ID</div></button>';
                    }
                    if (session('role_id') == 1 || in_array(998, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item faf_charges_status"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Add Faf Charges</div></button>';
                    }

                    if (session('role_id') == 1 || in_array(660, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item auto_cancel_days_setting"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-minus-circle"></i></div><div class="col-9 offset-1">Auto Cancel Days</div></button>';
                    }

                    
                    if (session('role_id') == 1 || in_array(855, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item add_fintech_charges"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Add Fintech Charges</div></button>';
                    }

                    if (session('role_id') == 1 || in_array(999, session('permissions'))) {
                        if ($result->status == 3) {
                            $dropdown .= '<button type="button" class="dropdown-item pause_shipper_booking"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Pause Shipper Booking</div></button>';
                        }
                    }
                

                $dropdown .= '<button type="button" class="dropdown-item add_shipper_exclude_intercept_type"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Add shipper exclude/Intercept 
                Type </div></button>';

                $dropdown .= '<button type="button" class="dropdown-item account_tagging_history" data-id="' . $result->id . '" data-toggle="modal" data-target="#AccountTaggingHistoryModal">
                    <div class="row no-gutters align-items-center">
                        <div class="col-2"><i class="ft-activity"></i></div>
                        <div class="col-9 offset-1">Account Tagging History</div>
                    </div>
                </button>';

                    $dropdown .= '
                    </div>
                  </div>
                ';

                    return $dropdown;

                }
            })
            ->rawColumns(['lead_id_link', 'duplication', 'id_padded', 'action'])
            ->make(true);

    }


    public function pendingAccountListAjax(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 61);
        }

        // $duplicateNtnCount = User::whereNotNull('ntn_no')
        //     ->select('ntn_no', DB::raw('COUNT(*) as count'))
        //     ->groupBy('ntn_no')
        //     ->havingRaw('COUNT(*) > 1')
        //     ->pluck('ntn_no')
        //     ->toArray();

        $duplicateEmailCount = User::whereNotNull('email')
            ->select('email', DB::raw('COUNT(*) as count'))
            ->groupBy('email')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('email')
            ->toArray();

        $users = User::join('cities', 'users.city_id', '=', 'cities.id')
            ->leftjoin('products', 'products.id', '=', 'users.product_id')
            ->leftjoin('sub_category_segments as seg_sub', 'seg_sub.id', '=', 'users.sub_segment_id')
            ->leftjoin('referrals as ref', 'ref.id', '=', 'users.referral_id')
            ->leftjoin('segments as seg', 'seg.id', '=', 'users.segment_id')
            ->leftjoin('admins as rab', 'rab.id', '=', 'users.rates_added_by')
            ->leftjoin('admins as rabb', 'rabb.id', '=', 'users.rates_authorized_by')
            ->leftjoin('admins as rrb', 'rrb.id', '=', 'users.rates_rejected_by')
            ->leftjoin('account_types as at', 'at.id', '=', 'users.account_type_id')
            ->leftjoin('sale_person_tags as spt', function ($join) {
                $join->on('spt.user_id', '=', 'users.id')
                    ->leftjoin('admins as ad', 'ad.id', '=', 'spt.admin_id')
                    ->where('spt.status', '=', 0);
            })
            ->leftjoin('duplicate_users as du', 'du.user_id', '=', 'users.id')
            ->leftjoin('user_document_attachments as uda', 'uda.user_id', '=', 'users.id')
            ->leftjoin('admins as dab', 'dab.id', '=', 'uda.approved_by')
            ->leftjoin('admins as drb', 'drb.id', '=', 'uda.rejected_by')
            ->leftjoin('international_users_informations as iui', 'iui.user_id', '=', 'users.id')
            ->leftjoin('sale_tier_tags as st', 'st.user_id', '=', 'users.id')
            ->leftjoin('sales_commissions as sc', 'sc.shipper_id', '=', 'users.id')
            ->leftjoin('sales_commission_users as scu', 'sc.id', '=', 'scu.sales_commission_id')
            ->leftjoin('admins as scun', 'scun.id', '=', 'scu.user_id')
            ->leftjoin('riders as scun_r', 'scun_r.id', '=', 'scu.user_id')
            ->leftjoin('admins as p', 'p.id', '=', 'st.poc')
            ->leftjoin('admins as k', 'k.id', '=', 'st.kam')
            ->leftjoin('riders as r', 'r.id', '=', 'st.ref')
            ->leftjoin('admins as e', 'e.id', '=', 'st.eso')
            ->leftjoin('payment_cycles as pc', 'pc.id', '=', 'users.payment_cycle_id')
            ->leftjoin('faf_charges', function($join){
                $join->on('faf_charges.user_id', '=', 'users.id')->where('faf_charges.status', '=', 1);
            })
            ->leftjoin('territories as t', 't.id', '=', 'users.territory_id')
            ->select(['users.ntn_no','rrb.name as rates_rejected_by', 'users.rates_added_at as rates_added_at', 'users.rates_approved_at as rates_approved_at', 'users.rates_rejected_at as rates_rejected_at', 'users.rate_status as rate_status', 'users.rejected_reason as rejected_reason', 'users.id', 'ad.name as admin_tag_id', 'users.name', 'cities.name as city', 'users.poc', 'users.cnic', 'users.status', 'users.created_at', 'products.product_name as product_type', 'users.blacklist', 'rab.name as rates_added_by', 'rabb.name as rates_authorized_by', 'users.account_type_id', 'at.name as account_type', 'users.documents_status', 'users.documents_status_reason as documents_rejection_reason', 'users.other_product_name', 'du.phone as duplicate_phone', 'du.cnic as duplicate_cnic', 'du.iban as duplicate_iban', 'du.name as duplicate_name', 'uda.uploaded_at as documents_uploaded_at', 'uda.approved_at as documents_approved_at', 'dab.name as documents_approved_by', 'drb.name as documents_rejected_by', 'uda.rejected_at as documents_rejected_at', 'iui.status as international_status', 'iui.status as international_rate_status', 'iui.rejected_reason as international_rejected_reason', 'p.name as tagged_poc', 'k.name as kam', 'r.name as ref','r.trax_id as rider_id', 'users.corporate_rate_type_id', 'users.email', 't.name as territory', 'users.address as address', 'seg.name as segment', 'seg_sub.name as sub_segment', 'ref.name as referral_name', 'pc.id as payment_cycle_id','pc.name as payment_cycle','users.payment_cycle_days as payment_cycle_days','e.name as eso', 'users.status as status_id', 'users.lead_id','scun.name as search','scun_r.name as search_user_type' , 'users.sms_charges', 'users.on_board_status', 'users.request_custom_quotation', 'faf_charges.status as fc_status'])->whereIn('users.status', [0, 1, 2, 5])->where('users.blacklist', 0)->where('users.email_verified', 1)
            ->where(function($query){
                $idsToExclude = FilterTrait::class::getFilteredIds(auth()->user()->id);
                if (!empty($idsToExclude)) {
                    $query->whereNotIn('users.id', $idsToExclude);
                }
            })
            ->groupBy('users.id');
        if (session('role_id') != 1) {
            $users = $users->whereIn('cities.hub_id', session('hubs'));
        }
        if (session('department_id') == 7) {
            if (!in_array(session('id'), session('sale_users_bypass'))) {
                $users = $users->whereIn('users.id', session('tagged_shippers'));
            }
        }
        if ($sale_persons = $request->get('sale_persons')) {
            $users = $users->whereIn('ad.id', $sale_persons);
        }

        if ($search_cnic = $request->get('search_cnic')) {
            $users = $users->where('users.cnic', $search_cnic);
        }
        if ($search_shipper = $request->get('search_shipper')) {
            $users = $users->whereIn('users.id', $search_shipper);
        }

        if ($search_iban = $request->get('search_iban')) {
            $users = $users->join('user_bank_infos as ubi', function ($join) use ($search_iban) {
                $join->on('ubi.user_id', '=', 'users.id')
                    ->where('ubi.iban', $search_iban);
            });
        }

        if ($search_email = $request->get('search_email')) {
            $users = $users->where('users.email', $search_email);
        }
        return Datatables::of($users)
        
            ->addColumn('lead_id_link', function ($users) {
                if($users->lead_id) {
                    $route = route('admin.leads.view_remarks', ['id' => $users->lead_id]);
                    return "<u><a href='{$route}\' target='_blank'>" . str_pad($users->lead_id, 3, '0', STR_PAD_LEFT) . "</a></u>";
                }
                //return $lead->lead_id;
            })
            ->filterColumn('users.lead_id', function ($query, $keyword) {
                return $query->where('users.lead_id', '=', $keyword);
            })
            ->addColumn('id', function ($user) {
                return str_pad($user->id, 6, '0', STR_PAD_LEFT);
            })
            ->filterColumn('users.id', function ($query, $keyword) {
                return $query->where('users.id', '=', $keyword);
            })
            ->editColumn('rejected_reason', function ($users) {
                if ($users->rejected_reason != null && $users->rate_status == 2) {
                    return $users->rejected_reason;
                } else {
                    return "-";
                }
            })
            ->editColumn('rate_status', function ($users) {
                if ($users->rate_status == 2) {
                    return "Rejected";
                } else if ($users->rate_status == 1) {
                    return "Requested";
                } else if ($users->rate_status == 0 && $users->status == 2) {
                    return "Authorized";
                } else if ($users->rate_status == 0 && $users->status == 1) {
                    return "Requested";
                } else if ($users->rate_status == 0 && $users->status == 0 && $users->request_custom_quotation != 1) {
                    return "Pending";
                }else{
                    return "Requested For Custom Quotation";
                }
            })
            ->editColumn('documents_status', function ($users) {
                if ($users->documents_status == 0) {
                    return "Incomplete";
                } elseif ($users->documents_status == 1) {
                    return "Pending for Approval";
                } elseif ($users->documents_status == 2) {
                    return "Approved";
                } elseif ($users->documents_status == 3) {
                    return "Rejected";
                }
            })
            ->editColumn('fc_status', function ($users) {
                return $users->fc_status ? 'Yes' : 'No';
            })
            ->filterColumn('faf_charges.status', function ($query, $keyword) {
                if($keyword)
                {
                    return $query->whereNotNull('faf_charges.status');
                }
                else
                {
                    return $query->whereNull('faf_charges.status');
                }
            })
            ->editColumn('status', function ($users) {
                return $users->status == 0 ? 'Request Received' : ($users->status == 1 ? 'Rates Added' : ($users->status == 2 ? 'Pending for Activation' : ($users->status == 5 ? 'Rates Rejected' : '')));
            })
            ->editColumn('id_padded', function ($users) {
                $route = route('admin.accounts.view.profile', ['id' => $users->id]);
                return '<a href="' . $route . '" style="text-decoration: underline;">' . $users->id . '</a>';

            })
            ->editColumn('eso', function ($users) {
                $sales_tiers = DB::table('sales_tiers')->where('tier_name', 'LIKE', '%ESO%')->orWhere('tier_name', 'LIKE', '%eso%')->first()->id ?? null;
                $shipper = DB::table('sales_commissions')->where('shipper_id', $users->id)->first();
                
                if (isset($sales_tiers,$shipper)) {
                    $sales_commission_users = DB::table('sales_commission_users')
                        ->where(['user_type' => '2', 'tier_id' => $sales_tiers, 'sales_commission_id' => $shipper->id])
                        ->get();
                
                    if ($sales_commission_users->isNotEmpty()) {
                        $rider_names = [];
                        foreach ($sales_commission_users as $sales_commission_user) {
                            $rider = Rider::find($sales_commission_user->user_id);
                            if ($rider) {
                                $rider_names[] = $rider->name;
                            } else {
                                return '-';
                            }
                        }
                        $rider_name = implode(', ', $rider_names);
                        return $rider_name;
                    } else {
                        return '-';
                    }
                } else {
                    return '-';
                }

            })
            ->editColumn('tagged_poc', function ($users) {
                $sales_tiers = DB::table('sales_tiers')->where('tier_name', 'LIKE', '%POC%')->orWhere('tier_name', 'LIKE', '%poc%')->first()->id ?? null;
                $shipper = DB::table('sales_commissions')->where('shipper_id', $users->id)->first();
                
                if (isset($shipper, $sales_tiers)) {
                    $sales_commission_users = DB::table('sales_commission_users')
                        ->where(['tier_id' => $sales_tiers, 'sales_commission_id' => $shipper->id])
                        ->get();
                    if ($sales_commission_users->isNotEmpty()) {
                        $array = [];
                        foreach ($sales_commission_users as $sales_commission_user) {
                            $type = $sales_commission_user->user_type;
                            $admins = ($type == 1) ? Admin::find($sales_commission_user->user_id) : Rider::find($sales_commission_user->user_id);
                            if ($admins) {
                                $array[] = $admins->name;
                            } else {
                                return '-';
                            }
                        }
                        $array = implode(', ', $array);
                        return $array;
                    } else {
                        return $users->tagged_poc;
                    }
                } else {
                    return $users->tagged_poc;
                }

            })

            ->editColumn('ref', function ($users) {
                $sales_tiers = DB::table('sales_tiers')->where('tier_name', 'LIKE', '%REF%')->orWhere('tier_name', 'LIKE', '%ref%')->first()->id ?? null;
                $shipper = DB::table('sales_commissions')->where('shipper_id', $users->id)->first();
                
                if (isset($shipper, $sales_tiers)) {
                    $sales_commission_users = DB::table('sales_commission_users')
                        ->where(['tier_id' => $sales_tiers, 'sales_commission_id' => $shipper->id])
                        ->get();

                    if ($sales_commission_users->isNotEmpty()) {
                        $array = [];
                        foreach ($sales_commission_users as $sales_commission_user) {
                            $type = $sales_commission_user->user_type;
                            $admins = ($type == 1) ? Admin::find($sales_commission_user->user_id) : Rider::find($sales_commission_user->user_id);
                            if ($admins) {
                                $array[] = $admins->name;
                            } else {
                                return '-';
                            }
                        }
                        $array = implode(', ', $array);
                        return $array;
                    } else {
                        if($users->rider_id)
                        {
                            return $users->rider_id.'-'.$users->ref;
                        }
                        return '-';
                    }
                } else {
                    if($users->rider_id)
                        {
                            return $users->rider_id.'-'.$users->ref;
                        }
                        return '-';
                }

            })

            ->filterColumn('r.name', function ($query, $keyword) {
                $query->where('r.name', $keyword)
                ->orWhere('scun.name', $keyword)->orWhere('scun_r.name', $keyword);
            })


            ->filterColumn('k.name', function ($query, $keyword) {
                $query->where('k.name', $keyword);
            })

  
            ->filterColumn('p.name', function ($query, $keyword) {
                $query->where('p.name', $keyword)
                ->orWhere('scun.name', $keyword)->orWhere('scun_r.name', $keyword);
            })
           
            ->filterColumn('status', function ($query, $keyword) {
                $keyword = strtolower($keyword);

                if ($keyword == 0 || $keyword == 1 || $keyword == 2 || $keyword == 5) {
                    $query->where('users.status', '=', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })->filterColumn('users.payment_cycle_days', function ($query, $keyword) {
                
                $paymentCycleDays = self::$paymentCycleDays;
                $keywordLower = strtolower($keyword);
            
                if (str_replace(['e', 'v', 'r', 'y','w','k','d','a'], '', $keywordLower) === '') {
                    $query->whereIn('pc.id', [2, 4, 5]);
                } else {
                    $keywordFound = [];
                
                    foreach ($paymentCycleDays as $key => $dayMap) {
                        if (stripos($dayMap, $keywordLower) !== false) {
                            $keywordFound[] = $key;
                        }
                    }
                
                    if (count($keywordFound) > 0) {
                        $query->whereRaw("FIND_IN_SET(?, users.payment_cycle_days) > 0", [$keywordFound])->whereNotIn('pc.id', [1, 3, 6]);
                    } else if (is_numeric($keyword) || is_numeric($keyword . 'rd') || is_numeric($keyword . 'nd') || is_numeric($keyword . 'th')) {
                        $keyword = preg_replace("/[^0-9]/", "", $keyword);
                        $query->whereRaw("FIND_IN_SET(?, users.payment_cycle_days) > 0", [$keyword])->whereNotIn('pc.id', [2, 4, 5]);
                    } else {
                        $query->whereRaw('false');
                    }
                }
                
            })
            ->editColumn('product_type', function ($user) {
                if ($user->product_type == 'Other') {
                    return $user->other_product_name;
                } else {
                    return $user->product_type;
                }
            })
            ->filterColumn('product_type', function ($query, $keyword) {
                if ($keyword != '' || $keyword != 24) {
                    $query->where('products.id', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn('duplication', function ($users)  use ($request, $duplicateEmailCount){
                $count = 0;

                // Unique ntn numbers and user ids
                $UniqueNtnCount = User::whereNotNull('ntn_no')
                    ->select('ntn_no', DB::raw('COUNT(*) as count'), 'users.id')
                    ->groupBy('ntn_no')
                    // ->havingRaw('COUNT(*) > 1')
                    ->distinct()
                    ->pluck('users.id', 'users.ntn_no')
                    ->toArray();
                if (!is_null($users->ntn_no) && !in_array($users->id, $UniqueNtnCount)) {
                    $count++;
                }

                if (in_array($users->email, $duplicateEmailCount)) {
                    $count++;
                }

                if ($users->duplicate_phone != null) {
                    $count++;
                }
                if ($users->duplicate_cnic != null) {
                    $count++;
                }
                if ($users->duplicate_iban != null) {
                    $count++;
                }
                if ($users->duplicate_name != null) {
                    $count++;
                }
                if ($count > 0 && !$request->get('excel')) {
                    return '<button class="btn btn-sm btn-outline-info align-middle duplicate_modal">' . $count . '</button>';
                } else {
                    return $count;
                }
            })
            ->editColumn('international_rate_status', function ($users) {
                if ($users->international_rate_status != null) {
                    if ($users->international_rate_status == 1) {
                        return "Approved";
                    } elseif ($users->international_rate_status == 2) {
                        return "Requested";
                    } elseif ($users->international_rate_status == 3) {
                        return "Rejected";
                    } elseif ($users->international_rate_status == 4) {
                        return "Requested";
                    } elseif ($users->international_rate_status == 5) {
                        return "Rejected";
                    }
                } else {
                    return "International Rates are not set";
                }
            })
            ->editColumn('international_rejected_reason', function ($users) {
                if ($users->international_rejected_reason != null && $users->international_rate_status == 3) {
                    return $users->international_rejected_reason;
                } else {
                    return "-";
                }
            })->editColumn('payment_cycle_days', function ($pending_payment) {
                $payment_cycle = $pending_payment->payment_cycle_id;
                $payment_cycle_days = $pending_payment->payment_cycle_days;
                $daysMap = self::$paymentCycleDays;;
            
                if ($payment_cycle == 2 || $payment_cycle == 4 || $payment_cycle == 5) {//Weekiy, Twice A Week And Thrice A Week.
                    $payment_cycle_days = explode(',', $payment_cycle_days);
                    $cycleText = AdminFinanceController::getCycleText($payment_cycle_days, $daysMap);
                    return $cycleText;
                }
            
                if (($payment_cycle == 3 || $payment_cycle == 6) && $payment_cycle_days != '0') {//Monthly And Fortnightly
                    $payment_cycle_days = explode(',', $payment_cycle_days);
                    if (count($payment_cycle_days) == 1) {
                        $day = (int)$payment_cycle_days[0];
                        return AdminFinanceController::getDayOfMonthText($day);
                    } elseif (count($payment_cycle_days) == 2) {
                        $day1 = (int)$payment_cycle_days[0];
                        $day2 = (int)$payment_cycle_days[1];
                        return AdminFinanceController::getDayOfMonthText($day1) . " And " . AdminFinanceController::getDayOfMonthText($day2);
                    }
                }else{
                    return '-';
                }
            
                if ($payment_cycle == 1) {// Daily
                    return '-';
                }
            })
            ->addColumn("lead_progress", function ($user) {
                if(isset($user->lead_id) && !empty($lead_progress_setting->percent)){
                    $weight_charges = WeightCharge::where('user_id' , $user->id);

                    $description = '-';
                    
                    if(($user->on_board_status < 1 && $user->created_at > '2024-06-13 00:00:00')){
                        $lead_progress_setting = LeadProgressSetting::find(1);
                        $percentage = $lead_progress_setting->percent;
    
                        $description = "Your account is $percentage% completed";
                    }else if(($weight_charges->exists() || $user->request_custom_quotation == 1) && !isset($user->rates_added_by)){
                        $lead_progress_setting = LeadProgressSetting::find(2);
                        $percentage = $lead_progress_setting->percent;
    
                        $description = "Your account is $percentage% completed";
    
                    }else if (isset($user->rates_added_by) && $user->documents_status != 2){
                        $lead_progress_setting = LeadProgressSetting::find(3);
                        $percentage = $lead_progress_setting->percent;
    
                        $description = "Your account is $percentage% completed";
    
                    }else if ($user->documents_status == 2 && $user->status != 3){
                        $lead_progress_setting = LeadProgressSetting::find(4);
                        $percentage = $lead_progress_setting->percent;
                        
                        $description = "Your account is $percentage% completed";
    
                    }else if ($user->status == 3){
                        $lead_progress_setting = LeadProgressSetting::find(5);
                        $percentage = $lead_progress_setting->percent;
    
                        $description = "Your account is activated";
    
                    }

                    return $description;
                }else{
                    return '-';
                }
            })
                ->addColumn("action", function ($result) {
                if (in_array($result->id, session('tagged_shippers'))) {
                    $multiple_sale_check = true;
                } else {
                    $multiple_sale_check = false;
                }
                $sale_check = SalePersonTag::where('user_id', $result->id)->first();
                $dropdown = '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm accounts">
                ';

                if (session('role_id') == 1 || in_array(361, session('permissions'))) {
                    $dropdown .= '<button type="button" class="dropdown-item remove_sales_tier"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-minus-circle"></i></div><div class="col-9 offset-1">Remove Sales Tier Tagging</div></button>';
                }

                $dropdown .= '<button type="button" class="dropdown-item" data-target-id="' . $result->id . '" data-toggle="modal" data-target="#BankInfoModal"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Bank Info</div></button>';

                $dropdown .= '<button type="button" class="dropdown-item" data-target-id="' . $result->id . '" data-toggle="modal" data-target="#ShippingInfoModal"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Shipping Info</div></button>';
                if (session('role_id') == 1 || in_array(session('id'), session('sale_users_bypass'))) {
                    $dropdown .= '<button type="button" class="dropdown-item" data-target-id="' . $result->id . '" data-toggle="modal" data-target="#SalesTagModal"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Sales Person</div></button>';
                }
                if (($result->status == 2 || $result->international_status == 1) && $result->documents_status == 2 && (session('role_id') == 1 || in_array(9, session('permissions')))) {
                    $dropdown .= '<button type="button" class="dropdown-item active_account" rel="activate" data-target-id="' . $result->id . '"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Activate Account</div></button>';

                }
                if (($sale_check != null || $multiple_sale_check) && $result->status != 2) {
                    if ($result->account_type_id == 1) {
                        if (RateStatus::where('user_id', $result->id)->exists() && (session('role_id') == 1 || in_array(7, session('permissions')))) {
                            if ($result->status != 2 && (in_array($result->rate_status, [0, 1, 2, 5]) || session('role_id') == 1 || session('role_id') == 2 || session('role_id') == 7 || in_array(session('id'), session('sale_users_bypass')))) {
                                $dropdown .= '<button onclick="window.open(\'' . route('admin.edit.rates', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit Rates</div></button>';
                            }
                        } else {
                            if (session('role_id') == 1 || in_array(6, session('permissions'))) {

                                if($result->lead_id){
                                    if($result->on_board_status < 1){
                                         $dropdown .= "";
                                    }else{
                                        $dropdown .= '<button onclick="window.open(\'' . route('admin.add.rates', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Add Rates</div></button>';
                                    }
                                }else{
                                    $dropdown .= '<button onclick="window.open(\'' . route('admin.add.rates', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Add Rates</div></button>';
                                }
                              
                            }
                        }
                    } else {
                        if ($result->corporate_rate_type_id == null) {
                            $dropdown .= '<button type="button" class="dropdown-item rate_type"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-bar-chart-2"></i></div><div class="col-9 offset-1">Add Rate Type</div></button>';
                        } else {
                            if (CorporateRateStatus::where('user_id', $result->id)->exists() && (session('role_id') == 1 || in_array(7, session('permissions'))) && ($result->corporate_rate_type_id == 1 || $result->corporate_rate_type_id == 2)) {

                                if ($result->status != 2) {
                                    $dropdown .= '<button onclick="window.open(\'' . route('admin.corporate.edit.rates', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit Rates</div></button>';
                                }
                            } else if (CorporateDefaultRateStatus::where('user_id', $result->id)->exists() && (session('role_id') == 1 || in_array(7, session('permissions'))) && $result->corporate_rate_type_id == 3) {
                                if ($result->status != 2) {
                                    $dropdown .= '<button onclick="window.open(\'' . route('admin.corporate.default.edit.rates', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit Rates</div></button>';
                                }
                            } else {

                                if (session('role_id') == 1 || in_array(6, session('permissions'))) {
                                    $dropdown .= '<button onclick="window.open(\'' . route('admin.corporate.add.rates', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Add Rates</div></button>';
                                }
                            }
                        }

                    }


                }

//                if($result->account_type_id == 1){
                if (($result->rate_status == 0 && $result->status == 2) && (InternationalUsersInformation::where('user_id', $result->id)->exists() == false) && (session('role_id') == 1 || in_array(8, session('permissions')))) {
                    $dropdown .= '<button type="button" class="dropdown-item reject_rates" rel="block"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Reject Rates</div></button>';
                }
//                }

                if ($result->account_type_id == 1) {
                    if (RateStatus::where('user_id', $result->id)->exists() && (session('role_id') == 1 || in_array(114, session('permissions')))) {
                        if ($result->status != 0) {
                            $dropdown .= '<button onclick="window.open(\'' . route('admin.view.rates', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Rates</div></button>';
                        }
                    }
                } else {
                    if ($result->status != 0) {
                        if (CorporateRateStatus::where('user_id', $result->id)->exists() && (session('role_id') == 1 || in_array(114, session('permissions'))) && ($result->corporate_rate_type_id == 1 || $result->corporate_rate_type_id == 2)) {
                            $dropdown .= '<button onclick="window.open(\'' . route('admin.corporate.view.rates', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Rates</div></button>';
                        } else if (CorporateDefaultRateStatus::where('user_id', $result->id)->exists() && (session('role_id') == 1 || in_array(114, session('permissions'))) && $result->corporate_rate_type_id == 3) {
                            $dropdown .= '<button onclick="window.open(\'' . route('admin.corporate.default.rates.view', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Rates</div></button>';
                        }
                    }
                }
                if ($result->blacklist == 0 && (session('role_id') == 1 || in_array(10, session('permissions')))) {
                    $dropdown .= '<button type="button" class="dropdown-item blacklist" rel="block"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-user-x "></i></div><div class="col-9 offset-1">Block</div></button>';
                }


                if (session('role_id') == 1 || in_array(110, session('permissions'))) {                
                    if(($result->lead_id && $result->on_board_status == 1) || !$result->lead_id){
                        $dropdown .= '<button onclick="window.open(\'' . route('admin.accounts.view.profile', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Profile</div></button>';
                    }else{
                        $dropdown .= "";
                    }
                }
                $merged = MergedSisterAccount::where('user_id', $result->id);
                if ($sale_check != null) {
                    if (!$merged->exists()) {
                        if (session('role_id') == 1 || in_array(session('id'), session('sale_users_bypass')) || (($multiple_sale_check == true) || (($sale_check) && ($sale_check->admin_id == Auth::id())) || in_array(241, session('permissions')))) {
                            $dropdown .= '<button onclick="window.open(\'' . route('admin.accounts.sister_account.add.account', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Add Sister Account</div></button>';
                        }
                    }
                }
                if (session('role_id') == 1 || in_array(244, session('permissions'))) {
                    if ($result->status > 0) {
                        $dropdown .= '<button onclick="window.open(\'' . route('admin.accounts.view_crf_agreement', ['id' => $result->id]) . '\')" type="button" class="dropdown-item view_crf" data-target-id="' . $result->id . '"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View CRF</div></button>';
                    }
                }
                $dropdown .= '<button onclick="window.open(\'' . route('admin.accounts.documents', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Documents</div></button>';

                if ($sale_check) {
                    $dropdown .= '<button onclick="window.open(\'' . route('admin.accounts.add_contacts', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Add Contacts</div></button>';
                }
                if (($sale_check != null || $multiple_sale_check) && $result->status != 2) {

                    if ((!InternationalUsersInformation::where('user_id', $result->id)->exists()) && (session('role_id') == 1 || in_array(439, session('permissions')))) {
                        $dropdown .= '<button onclick="window.open(\'' . route('admin.international.rates.update.index', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-bar-chart"></i></div><div class="col-9 offset-1">Intl Add Rates</div></button>';
                    } else {
                        if (session('role_id') == 1 || in_array(439, session('permissions'))) {
                            $dropdown .= '<button onclick="window.open(\'' . route('admin.international.rates.update.index', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-bar-chart"></i></div><div class="col-9 offset-1">Intl Edit Rates</div></button>';
                        }
                        if (session('role_id') == 1 || in_array(440, session('permissions'))) {
                            $dropdown .= '<button onclick="window.open(\'' . route('admin.international.rates.view.index', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-eye"></i></div><div class="col-9 offset-1">Intl View Rates</div></button>';
                        }
                    }
                }

                if (InternationalEconomyRateStatus::where('user_id', $result->id)->doesntExist()) {
                    if (session('role_id') == 1 || in_array(528, session('permissions'))) {
                        $dropdown .= '<button onclick="window.open(\'' . route('admin.international.rates.economy.create', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-bar-chart"></i></div><div class="col-9 offset-1">Intl Add Economy Rates</div></button>';
                    }
                } else {
                    if ((session('role_id') == 1 || in_array(528, session('permissions')))) {
                        $dropdown .= '<button onclick="window.open(\'' . route('admin.international.rates.economy.create', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-bar-chart"></i></div><div class="col-9 offset-1">Intl Edit Economy Rates</div></button>';
                    }
                }

                if (InternationalEconomyRate::where('user_id', $result->id)->count() > 0) {
                    if (session('role_id') == 1 || in_array(530, session('permissions'))) {
                        $dropdown .= '<button onclick="window.open(\'' . route('admin.international.rates.economy.create', ['id' => $result->id, 'view' => 'view']) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-bar-chart"></i></div><div class="col-9 offset-1">Intl View Economy Rates</div></button>';
                    }
                }

                if ($result->account_type_id == 2 && (session('role_id') == 1 || count(array_intersect([598, 599], session('permissions'))) !== 0)) {
                    $dropdown .= '<button onclick="window.open(\'' . route('admin.corporate.reimbursement_setting.index', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-bar-chart"></i></div><div class="col-9 offset-1">Corporate Reimbursement Setting</div></button>';
                }

                if (session('role_id') == 1 || in_array(619, session('permissions'))) {
                    $dropdown .= '<button type="button" class="dropdown-item restrict_order_id"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Restrict Order ID</div></button>';
                }
                if (session('role_id') == 1 || in_array(998, session('permissions'))) {
                    $dropdown .= '<button type="button" class="dropdown-item faf_charges_status"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Add Faf Charges</div></button>';
                }

                if (session('role_id') == 1 || in_array(856, session('permissions'))) {
                    $dropdown .= '<button type="button" class="dropdown-item add_fintech_charges"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Add Fintech Charges</div></button>';
                }

                $dropdown .= '<button type="button" class="dropdown-item account_tagging_history" data-id="' . $result->id . '" data-toggle="modal" data-target="#AccountTaggingHistoryModal">
                    <div class="row no-gutters align-items-center">
                        <div class="col-2"><i class="ft-activity"></i></div>
                        <div class="col-9 offset-1">Account Tagging History</div>
                    </div>
                </button>';

                $dropdown .= '
                    </div>
                  </div>
                ';

                return $dropdown;
            })
            ->rawColumns(['lead_id_link', 'duplication', 'id_padded', 'action'])
            ->make(true);

    }

    public function blockAccountListAjax(Request $request)
    {
        $globalArray = [];

        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 276);
        }
        $users = User::join('cities', 'users.city_id', '=', 'cities.id')
            ->leftjoin('sale_person_tags as spt', function ($join) {
                $join->on('spt.user_id', '=', 'users.id')
                    ->leftjoin('admins as ad', 'ad.id', '=', 'spt.admin_id')
                    ->where('spt.status', '=', 0);
            })
            ->leftjoin('sale_tier_tags as st', 'st.user_id', '=', 'users.id')
            ->leftjoin('admins as a', 'a.id', '=', 'st.poc')
            ->leftjoin('admins as d', 'd.id', '=', 'st.kam')
            ->leftjoin('admins as h', 'h.id', '=', 'st.ref')
            ->leftjoin('block_disable_reason_users as bdru', 'bdru.id', '=', 'users.blacklist_reason_1')

            ->select(['users.id', 'users.name', 'users.disable_at as disable_at', 'cities.name as city', 'users.poc', 'users.blacklist_reason as remarks', 'ad.name as admin_tag_id', 'a.name as poc_tagged', 'd.name as kam', 'h.name as ref', 'bdru.name as reason', 'users.activated_at', 'users.blocked_at'])->where('blacklist', 1);

        if (session('role_id') != 1) {
            $users = $users->whereIn('cities.hub_id', session('hubs'));
        }
        if (session('department_id') == 7) {
            if (!in_array(session('id'), session('sale_users_bypass'))) {
                $users = $users->whereIn('users.id', session('tagged_shippers'));
            }
        }
        if ($sale_persons = $request->get('sale_persons')) {
            $users = $users->whereIn('ad.id', $sale_persons);
        }
        if ($search_email = $request->get('search_email')) {
            $users = $users->where('users.email', $search_email);
        }
        return Datatables::of($users)
            ->addColumn('id', function ($user) {
                return str_pad($user->id, 6, '0', STR_PAD_LEFT);
            })
            ->filterColumn('users.id', function ($query, $keyword) {
                return $query->where('users.id', '=', $keyword);
            })
            ->editColumn('id_padded', function ($users) {
                $route = route('admin.accounts.view.profile', ['id' => $users->id]);
                return '<a href="' . $route . '" style="text-decoration: underline;">' . $users->id . '</a>';

            })
            ->addColumn("action", function ($result) {
                $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

                $dropdown .= '<button type="button" class="dropdown-item" data-target-id="' . $result->id . '" data-toggle="modal" data-target="#BankInfoModal"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Bank Info</div></button>';

                $dropdown .= '<button type="button" class="dropdown-item" data-target-id="' . $result->id . '" data-toggle="modal" data-target="#ShippingInfoModal"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Shipping Info</div></button>';

                if (session('role_id') == 1 || in_array(16, session('permissions'))) {
                    $dropdown .= '<button type="button" class="dropdown-item blacklist" rel="unblock"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-user-plus "></i></div><div class="col-9 offset-1">Unblock</div></button>';
                }


                if (session('role_id') == 1 || in_array(110, session('permissions'))) {
                    $dropdown .= '<button onclick="window.open(\'' . route('admin.accounts.view.profile', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Profile</div></button>';
                }
                if (session('role_id') == 1 || in_array(244, session('permissions'))) {
                    if ($result->status > 0) {
                        $dropdown .= '<button onclick="window.open(\'' . route('admin.accounts.view_crf_agreement', ['id' => $result->id]) . '\')" type="button" class="dropdown-item view_crf" data-target-id="' . $result->id . '"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View CRF</div></button>';
                    }
                }
                $dropdown .= '
                </div>
              </div>
            ';

                return $dropdown;
            })
            ->rawColumns(['id_padded', 'action'])
            ->make(true);

    }

    function user_payment_cycles_days($user){
        $payment_cycle_days = explode(',', $user->payment_cycle_days);
        $weekly = [2, 4, 5]; // Twice, Thrice, and Weekly.
        $fort_month = [3, 6]; // Monthly and Fortnight.
        $days = [];
    
        if (isset($user->payment_cycle->id)) {
            if (in_array($user->payment_cycle->id, $weekly)) {
                foreach ($payment_cycle_days as $payment_cycle_day) {
                    $date = Carbon::now()->startOfWeek()->addDays($payment_cycle_day - 1);                                                
                    $dayName = $date->format('l');
                    $days[] = $dayName;
                }
            } else if (in_array($user->payment_cycle->id, $fort_month)) {
                $days[] = "Every " . implode(', ', $payment_cycle_days) . " of the month";
            } else {//Daily
                $days[] = 'Daily';
            }
        } else {
            $days[] = 'Payment Cycle Not Defined'; 
        }
    
        $days = implode(', ', $days);
    
        return $days;
    }
    
    //User Profile Methods

    public function userProfile($id)
    {
        $user = User::find($id);
        $product = Product::find($user->product_id);
        $payment_cycle_days = $this->user_payment_cycles_days($user);
        $products = Product::all();
        $banks = BanksList::all();
        // $parent_products = ParentProduct::get();
        // $parent_product = ParentProduct::find($user->parent_product_id);
        $invoicing_cycle = InvoicingCycle::all();
        $city_list = City::all();
        $emails = ShipperNotificationEmail::where('user_id', $user->id)->select('email')->get();
        $email_ids = ShipperNotificationEmail::where('user_id', $user->id)->pluck('email')->toArray();
        $email_ids = implode(',', $email_ids);
        $reference = Reference::where('id', $user->reference_id)->first();
        $segments = Segment::all();
        $sub_segments = SubCategorySegment::where('segment_id', $user->segment->id ?? null)->get();
        $average_shipment_duration = AverageShipmentCycle::where('id', $user->average_shipment_duration_id)->first();
        $average_shipment_durations_cycle = AverageShipmentCycle::all();
        $user_bank_default = UserBankInfo::where('user_id', $user->id)->where('default_bank', 1)->first();
        $territories = Territory::select('id', 'name')->get();
        return view('admin.accounts.profile')->with(['user' => $user, 'product_name' => $product->product_name ?? null, 'banks' => $banks, 'all_cities' => $city_list, 'products' => $products, 'invoicing_cycle' => $invoicing_cycle, 'emails' => $emails, 'email_ids' => $email_ids, 'reference' => $reference, 'average_shipment_duration' => $average_shipment_duration, 'average_shipment_durations_cycle' =>$average_shipment_durations_cycle, 'user_bank_default' => $user_bank_default, 'segments' => $segments, 'sub_segments' => $sub_segments, 'territories' => $territories,'days'=>$payment_cycle_days]);
    }

    public function updateProfile(Request $request)
    {
        $user_id = $request->user_id;

        //1 for Admin, 0 for User

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'address' => 'required|string|max:255',
            'poc' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'cnic' => 'required|string|max:255',
            'segment_id' => 'required',
            'sub_segment_id' => 'required',
            'avg_shipments' => 'required',
            'average_shipment_duration_id' => 'required',

        ]);


        $flag = true;
        $users = User::where('email', $request->email)->orWhere('phone', $request->phone)->get();
        if ($users) {
            foreach ($users as $user) {
                if ($user_id != $user->id) {
                    $flag = false;
                }
            }
        }

        if ($flag == true) {
            if ($request->password == "" || $request->password == null) {
                User::where('id', $user_id)->update(['name' => $request->name, 'poc' => $request->poc, 'email' => $request->email, 'address' => $request->address, 'phone' => $request->phone, 'phone2' => $request->phone2, 'cnic' => $request->cnic,
                    'ntn_no' => $request->ntn_no, 'strn_no' => $request->strn_no, 'updated_by_type' => 1, 'updated_by_id' => Auth::id(), 'city_id' => $request->city_id, 'segment_id' => $request->segment_id, 'sub_segment_id' => $request->sub_segment_id, 'url' => $request->url, 'product_id' => $request->product_id, 'other_product_name' => $request->has('product_name') ? $request->product_name : null, 'brand_name' => $request->has('brand_name') ? $request->brand_name : null , 'average_shipments' => $request->avg_shipments, 'average_shipment_duration_id' => $request->average_shipment_duration_id]);
                AdminLogs::create([
                    'admin_id' => Auth::id(),
                    'user_id' => $user_id

                ]);
            } else {
                User::where('id', $user_id)->update(['name' => $request->name, 'poc' => $request->poc, 'email' => $request->email, 'address' => $request->address, 'phone' => $request->phone, 'phone2' => $request->phone2, 'cnic' => $request->cnic,
                    'ntn_no' => $request->ntn_no, "password" => Hash::make($request->password), 'updated_by_type' => 1, 'updated_by_id' => Auth::id(), 'city_id' => $request->city_id, 'segment_id' => $request->segment_id, 'sub_segment_id' => $request->sub_segment_id, 'url' => $request->url, 'product_id' => $request->product_id, 'brand_name' => $request->has('brand_name') ? $request->brand_name : null , 'average_shipments' => $request->avg_shipments, 'average_shipment_duration_id' => $request->average_shipment_duration_id]);
            }

            return redirect()->back()->with(['success' => "Profile Information Successfully Updated"]);
        } else {
            return redirect()->back()->with(['error' => "Email Address and Phone Number must be unique"]);
        }
    }

    public function updateBankInfo(Request $request)
    {

        $user_id = $request->user_id;
        $user = User::find($user_id);
        //1 for Admin, 0 for User

        $request->validate([
            'bank_name' => 'required|max:255',
            'bank_branch' => 'required|string|max:255',
            'account_no' => 'required|string|max:255',
            'account_title' => 'required|string|max:255',
            'iban' => 'required|string|max:255',
        ]);
        $old_bank_detail = UserBankInfo::where('user_id', $user_id)->select('bank_name')->first();

        if ($user->account_type_id == 1) {
            UserBankInfo::where('user_id', $user_id)->update(['bank_branch' => $request->bank_branch, 'bank_name' => $request->bank_name, 'account_no' => $request->account_no,
                'account_title' => $request->account_title, 'iban' => $request->iban, 'city_id' => $request->bank_city]);

        } else {
            $generation_date = null;
            if ($request->invoicing_cycle_id == 2 || $request->invoicing_cycle_id == 4) {
                $generation_date = null;
            } else {
                $generation_date = $request->generation_date;
            }
            UserBankInfo::where('user_id', $user_id)->update([
                'bank_branch' => $request->bank_branch,
                'bank_name' => $request->bank_name,
                'account_no' => $request->account_no,
                'account_title' => $request->account_title,
                'iban' => $request->iban,
                'city_id' => $request->bank_city,
                'invoicing_cycle_id' => $request->invoicing_cycle_id,
                'generation_date' => $generation_date,
                'billing_person_name' => $request->billing_person_name,
                'billing_person_phone' => $request->billing_person_phone,
                'billing_person_email' => $request->billing_person_email,
                'billing_address' => $request->billing_address
            ]);
        }
        if ($old_bank_detail->bank_name != $request->bank_name) {
            $bank_history = new HistoryShipperBankAccount();
            $bank_history->user_id = $user_id;
            $bank_history->bank_id = $old_bank_detail->bank_name;
            $bank_history->save();

        }

        //Update the latest bank info record default_bank value to 1 of this user 
        $latestBankInfo = UserBankInfo::where('user_id', $user_id)->latest()->first();
        $latestBankInfo->default_bank = 1;
        $latestBankInfo->save();

        AdminLogs::create([
            'admin_id' => Auth::id(),
            'user_id' => $user_id
        ]);
        return redirect()->back()->with(['success' => "Bank Information Successfully Updated"]);
    }

    public function getPickups(Request $request)
    {
        $pickups = UserShippingInfo::join('cities as c', 'user_shipping_infos.city_id', '=', 'c.id')
            ->select(['user_shipping_infos.id as id', 'user_shipping_infos.pickup_brand_name as pickup_brand_name', 'user_shipping_infos.pickup_address as pickup_address', 'user_shipping_infos.poc as poc', 'user_shipping_infos.phone as phone', 'user_shipping_infos.email as email', 'user_shipping_infos.status as status', 'user_shipping_infos.default_address as default_address', 'user_shipping_infos.user_id as user_id', 'c.name as city_name', 'user_shipping_infos.vendor'])
            ->where('user_id', $request->user_id)
            ->where('hidden', 0);

        return Datatables::of($pickups)
            ->addColumn("status", function ($result) {
                if ($result->default_address == 1) {
                    $status = "Default Address";
                } elseif ($result->status == 1) {
                    $status = "Enabled";
                } elseif ($result->status == 0) {
                    $status = "Disabled";
                }
                return $status;
            })
            ->filterColumn('status', function ($query, $keyword) {
                if ($keyword != '') {
                    if ($keyword == 2) {
                        $query->where('user_shipping_infos.default_address', 1);
                    } else {
                        $query->where('user_shipping_infos.status', $keyword);
                    }
                } else {
                    $query->whereRaw('false');
                }
            })
            ->make(true);
    }


    public function cityView(Request $request)
    {
//        $req = $request->route();
//        $uri_path = $req->getPath();
//        $uri_parts = explode('/', $uri_path);
//        $uri_tail = end($uri_parts);
//        return $uri_tail;
//        $hubs = City::where('hub',1)->get();
//        return $hubs[0]->id;
        ActivityTrailController::createActivityTrailLog(Auth::id(), 348);

        $business_categories = BusinessCategory::all();
        return view('admin.management.city_management')->with(['business_categories' => $business_categories]);
    }

    public function cityListAjax(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 349);
        }

        $cities = City::join('cities as h', 'cities.hub_id', '=', 'h.id')
            ->leftjoin('city_histories as ch', function ($join) {
                $join->on('ch.city_id', '=', 'cities.id')
                    ->where('ch.created_at', '=',
                        DB::raw('(select max(created_at) from city_histories where city_histories.city_id = cities.id)'));
            })
            ->leftjoin('admins as a', 'a.id', '=', 'ch.updated_by')

            ->leftjoin('admins as c', 'c.id', '=', 'cities.created_by')

            ->leftjoin('business_categories as bc', 'bc.id', '=', 'cities.business_category_id')
            ->join('zones as z', 'cities.zone_id', '=', 'z.id')
            ->leftJoin('provinces', 'provinces.id', 'cities.province_id')
            ->select(['cities.id as city_id', 'cities.city_code as city_code', 'cities.id as id', 'cities.name as name', 'h.name as hub', 'cities.hub_id', 'z.name as zone', 'cities.hub as isHub', 'cities.status as status', 'ch.created_at as updated', 'a.name as updated_by', 'cities.gc_area as gc_area', 'cities.attempt_tat as attempt_tat', 'cities.location_latitude', 'cities.location_longitude', 'cities.address as address', 'cities.business_category_id as business_category_id', 'bc.name as business_category', 'cities.hub_location_latitude', 'cities.hub_location_longitude', 'cities.iata_code as iata_code','cities.booking_enable_status as booking_enable_status', 'c.name as created_by', 'cities.created_at as created_at', 'provinces.name as province_name', DB::raw('(SELECT COUNT(*) FROM city_logs WHERE city_logs.city_id = cities.id) as city_logs'), DB::raw('(SELECT COUNT(*) FROM city_status_change_logs WHERE city_status_change_logs.city_id = cities.id AND column_type = 2) as status_change_logs_count')
            ,DB::raw('(SELECT COUNT(*) FROM city_status_change_logs WHERE city_status_change_logs.city_id = cities.id AND column_type = 1) as booking_status_change_logs_count')])
            ->where('cities.permanent_disabled',0);

        return Datatables::of($cities)
            ->editColumn('status', function ($cities) {
                return ($cities->status == 1) ? 'Active' : 'Inactive';
            })
            ->editColumn('gc_area', function ($cities) {
                return ($cities->gc_area == 1) ? 'Yes' : 'No';
            })
            ->editColumn('booking_enable_status', function ($cities) {
                return ($cities->booking_enable_status == 1) ? 'Yes' : 'No';
            })
            ->filterColumn('modes', function ($query, $keyword) {

                if ($keyword != '') {
                    $query->where('sm.id', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn('location', function ($result) {
                $location = '<div class="text-center">';
                if ($result->location_latitude != null && $result->location_longitude != null) {
                    $location .= '<button type="button" class="btn btn-primary btn-sm"><a class="white" href="http://www.google.com/maps/place/' . $result->location_latitude . ',' . $result->location_longitude . '" target="_blank"><i class="la la-map-marker align-middle"></i></a></button>';
                    $location .= '</div>';
                    return $location;
                } else {
                    return '-';
                }
            })
            ->addColumn('hub_location', function ($result) {
                $location = '<div class="text-center">';
                if ($result->hub_location_latitude != null && $result->hub_location_longitude != null) {
                    $location .= '<button type="button" class="btn btn-primary btn-sm"><a class="white" href="http://www.google.com/maps/place/' . $result->hub_location_latitude . ',' . $result->hub_location_longitude . '" target="_blank"><i class="la la-map-marker align-middle"></i></a></button>';
                    $location .= '</div>';
                    return $location;
                } else {
                    return '-';
                }
            })
            ->editColumn('status_logs', function ($cities) {
                if ($cities->status_change_logs_count) {
                    return '<button class="btn btn-sm btn-outline-info align-middle status_logs" data-id="' . $cities->city_id . '" data-type="city">' . $cities->status_change_logs_count . '</button>';
                }

                return '-';
            })
            ->editColumn('booking_enable_disable_logs', function ($cities) {
                if ($cities->booking_status_change_logs_count) {
                    return '<button class="btn btn-sm btn-outline-info align-middle booking_enable_disable_logs" data-id="' . $cities->city_id . '" data-type="booking">' . $cities->booking_status_change_logs_count . '</button>';
                }

                return '-';
            })
            ->orderColumn('status_logs', 'status_change_logs_count $1')
            ->orderColumn('booking_enable_disable_logs', 'booking_enable_disable_logs_count $1')
            ->filterColumn('status_logs', function ($query, $keyword) {
                $keyword = trim($keyword);
                $query->whereRaw('(SELECT COUNT(*) FROM city_status_change_logs WHERE city_status_change_logs.city_id = cities.id AND column_type = 2) = ?', [(int) $keyword]);
            })
            ->filterColumn('booking_enable_disable_logs', function ($query, $keyword) {
                $keyword = trim($keyword);
                $query->whereRaw('(SELECT COUNT(*) FROM city_status_change_logs WHERE city_status_change_logs.city_id = cities.id AND column_type = 1) = ?', [(int) $keyword]);
            })
            ->editColumn('city_logs', function ($cities) {
                if ($cities->city_logs) {
                    return '<button class="btn btn-sm btn-outline-info align-middle city_logs" data-id="' . $cities->city_id . '" data-type="city">' . $cities->city_logs . '</button>';
                }

                return '-';
            })
            ->addColumn("action", function ($result) {
                if (session('role_id') == 1 || count(array_intersect([90, 91,850], session('permissions'))) !== 0) {
                    $dropdown = '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                ';

                    if (session('role_id') == 1 || in_array(90, session('permissions'))) {
                        if ($result->business_category_id == 1) {
                            $dropdown .= '<button type="button" class="dropdown-item" data-target-id=' . $result->city_id . ' rel="editcity" data-toggle="modal" data-target="#editCity"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Update City Status</div></button>';
                        } else {
                            $dropdown .= '<button type="button" class="dropdown-item" data-target-id=' . $result->city_id . ' rel="editinternationalcity" data-toggle="modal" data-target="#editInternationalCity"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Update International City Status</div></button>';
                        }
                    }

                    if (session('role_id') == 1 || in_array(91, session('permissions'))) {
                        if ($result->status == 1) {
                            $dropdown .= '<button type="button" class="dropdown-item deactivate" data-target-id=' . $result->city_id . ' rel="cityInactive" hub=' . $result->isHub . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Deactivate City</div></button>';
                        } else {
                            $dropdown .= '<button type="button" class="dropdown-item deactivate" data-target-id=' . $result->city_id . ' rel="cityactive" hub=' . $result->isHub . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Activate City</div></button>';
                        }
                    }

                    if (session('role_id') == 1 || in_array(850, session('permissions'))) {
                        if ($result->isHub == 1) {
                            $dropdown .= '<a target="_blank" class="dropdown-item" href='.route('admin.management.add_city_sub_area', ['id' => $result->id]).'>
                                <div class="row no-gutters align-items-center">
                                    <div class="col-2"><i class="ft-plus-circle"></i></div>
                                    <div class="col-9 offset-1">Add Areas</div>
                                </div>                          
                            </a>';
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
            })
            ->addColumn('osa_list', function ($result) {
                $osa_count = CityOsaRate::where('city_id', $result->city_id);
                if ($osa_count->exists()) {
                    $btn = '<div class="text-center">';
                    $btn .= '<button type="button" class="btn btn-primary btn-sm">' . $osa_count->count() . '</button>';
                    $btn .= '</div>';
                    return $btn;
                } else {
                    return '-';
                }
            })
            ->editColumn('created_at', function ($cities) {
                if ($cities->created_at) {
                    return $cities->created_at->toDateTimeString() === '-0001-11-30 00:00:00' ? '-' : $cities->created_at->toDateTimeString();
                }
                return '-';
            })

            ->rawColumns(['location','hub_location','osa_list','action','city_logs', 'status_logs', 'booking_enable_disable_logs'])
            ->make(true);
    }

    public function getCityForm()
    {
        $hubs = City::where('hub', 1)->where('business_category_id', 1)->where('status', 1)->get();
        $zones = Zone::where('business_category_id', 1)->get();
        $shippingMode = ShippingMode::all();
        $booking = BookingType::where('id', '!=', 4)->get();
        $vehicles = Fleet::where('status', 1)->select(['id', 'reg_number'])->get();
        $provinces = Province::all();
        return view('admin.management.add_city_form')->with(['hubs' => $hubs, 'zones' => $zones, 'shippingMode' => $shippingMode, 'bookings' => $booking, 'vehicles' => $vehicles, 'provinces' => $provinces]);
    }

    public function getEditCityForm($id)
    {
        $city = City::find($id);
        if ($city->hub == 1) {
            $cityhub = '';
            $isHub = 1;
        } else {
            $cityhub = City::select(['id', 'name'])->where('id', $city->hub_id)->get();
            $isHub = 0;

        }
        $delivery_array = CityDelivery::where('city_id', $city->id)->select(['booking_type_id', 'shipping_mode_id'])->get();
        $delivery = array();
        foreach ($delivery_array as $delivery_details) {
            $delivery[$delivery_details['booking_type_id']][] = $delivery_details['shipping_mode_id'];
        }


        $hubs = City::where('hub', 1)->where('business_category_id', 1)->where('status', 1)->get();
        $zones = Zone::where('business_category_id', 1)->get();
        $shippingMode = ShippingMode::all();
        $booking = BookingType::where('id', '!=', 4)->get();
        $walk_in_city = WalkInCities::where('city_id', $city['id'])->get();
        $walk_in_delivery = array();
        $osa_list = CityOsaRate::where('city_id', $city->id)->get();
        $vehicles = Fleet::where('status', 1)->select(['id', 'reg_number'])->get();

        foreach ($walk_in_city as $walk_in_detail) {
            $walk_in_delivery[$walk_in_detail['delivery']] = $walk_in_detail['delivery'];
        }
        $provinces = Province::all();
        return view('admin.management.edit_city_form')->with(['hubs' => $hubs, 'zones' => $zones, 'shippingMode' => $shippingMode, 'bookings' => $booking, 'isHub' => $isHub, 'city' => $city, 'delivery' => $delivery, 'cityhub' => $cityhub, 'walk_in_city' => $walk_in_delivery, 'osa_list' => $osa_list, 'vehicles' => $vehicles, 'provinces' => $provinces]);

    }

    public function updateCity(Request $request, $id)
    {

        $city_id = City::where('id', $id)->first();
        if ($city_id) {
            if ($request->has('updatedelivery') && count($request->updatedelivery) > 0) {
                $oldCity = City::with(['osaRates', 'deliveries', 'walkIns'])->find($id);
                if ($request->postType == 'city') {
                    City::where('id', $id)->update([
                        'name' => $request->cityName,
                        'city_code' => $request->city_code,
                        'hub' => 0,
                        'hub_id' => $request->hubs,
                        'zone_id' => City::find($request->hubs)->zone_id,
                        'pickup' => ($request->has('pickup')) ? 1 : 0,
                        'gc_area' => ($request->has('gc_area')) ? 1 : 0,
                        'attempt_tat' => $request->attempt_tat,
                        'location_latitude' => $request->latitude,
                        'location_longitude' => $request->longitude,
                        'hub_location_latitude' => $request->hub_latitude,
                        'hub_location_longitude' => $request->hub_longitude,
                        'address' => $request->address,
                        'pickup_cut_off_time' => $request->pickup_cut_off_time
                    ]);
                    CityHistory::create([
                        'city_id' => $id,
                        'hub' => 0,
                        'hub_id' => $request->hubs,
                        'zone_id' => City::find($request->hubs)->zone_id,
                        'pickup' => ($request->has('pickup')) ? 1 : 0,
                        'status' => $city_id->status,
                        'gc_area' => ($request->has('gc_area')) ? 1 : 0,
                        'attempt_tat' => $request->attempt_tat,
                        'updated_by' => Auth::id(),
                        'location_latitude' => $request->latitude,
                        'location_longitude' => $request->longitude,
                        'hub_location_latitude' => $request->hub_latitude,
                        'hub_location_longitude' => $request->hub_longitude,
                        'address' => $request->address,
                        'pickup_cut_off_time' => $request->pickup_cut_off_time
                    ]);
                    WalkInCities::where('city_id', $id)->delete();
                    if (!empty($request->walk_in_delivery)) {
                        foreach ($request->walk_in_delivery as $index => $delivery_walk_in) {
                            WalkInCities::create([
                                'city_id' => $id,
                                'pickup' => ($request->has('pickup')) ? 1 : 0,
                                'delivery' => $index,
                            ]);
                        }
                    }

                    CityDelivery::where('city_id', $id)->delete();
                    CityOsaRate::where('city_id', $id)->delete();

                    foreach ($request->updatedelivery as $booking_type_id => $shipping_modes) {
                        foreach ($shipping_modes as $shipping_mode_id => $shipping_mode_value) {
                            CityDelivery::create([
                                'city_id' => $id,
                                'booking_type_id' => $booking_type_id,
                                'shipping_mode_id' => $shipping_mode_id,
                            ]);
                        }
                    }

                    if ($request->osa_name != null) {
                        foreach ($request->osa_name as $key => $value) {

                            $osa_charges = new CityOsaRate();
                            $osa_charges->city_id = $id;
                            $osa_charges->osa_name = $value;
                            $osa_charges->osa_rate = $request->osa_rate[$key];
                            $osa_charges->admin_id = Auth::id();
                            $osa_charges->save();
                        }
                    }

                    $this->logCityChangesAfterUpdate($oldCity);
                    return redirect()->back()->with('success', 'City updated successfully');
                } elseif ($request->postType == 'hub') {
                    City::where('id', $id)->update([
                        'name' => $request->cityName,
                        'city_code' => $request->city_code,
                        'hub' => 1,
                        'hub_id' => $id,
                        'zone_id' => $request->zone_id,
                        'province_id' => $request->province_id,
                        'pickup' => ($request->has('pickup')) ? 1 : 0,
                        'gc_area' => ($request->has('gc_area')) ? 1 : 0,
                        'attempt_tat' => $request->attempt_tat,
                        'location_latitude' => $request->latitude,
                        'location_longitude' => $request->longitude,
                        'hub_location_latitude' => $request->hub_latitude,
                        'hub_location_longitude' => $request->hub_longitude,
                        'address' => $request->address,
                        'pickup_cut_off_time' => $request->pickup_cut_off_time
                    ]);
                    CityHistory::create([
                        'city_id' => $id,
                        'hub' => 1,
                        'hub_id' => $id,
                        'zone_id' => $request->zone_id,
                        'province_id' => $request->province_id,
                        'pickup' => ($request->has('pickup')) ? 1 : 0,
                        'status' => $city_id->status,
                        'gc_area' => ($request->has('gc_area')) ? 1 : 0,
                        'attempt_tat' => $request->attempt_tat,
                        'updated_by' => Auth::id(),
                        'location_latitude' => $request->latitude,
                        'location_longitude' => $request->longitude,
                        'hub_location_latitude' => $request->hub_latitude,
                        'hub_location_longitude' => $request->hub_longitude,
                        'address' => $request->address,
                        'pickup_cut_off_time' => $request->pickup_cut_off_time
                    ]);
                    WalkInCities::where('city_id', $id)->delete();
                    if (!empty($request->walk_in_delivery)) {
                        foreach ($request->walk_in_delivery as $index => $delivery_walk_in) {
                            WalkInCities::create([
                                'city_id' => $id,
                                'pickup' => ($request->has('pickup')) ? 1 : 0,
                                'delivery' => $index,
                            ]);
                        }
                    }

                    CityDelivery::where('city_id', $id)->delete();
                    CityOsaRate::where('city_id', $id)->delete();

                    foreach ($request->updatedelivery as $booking_type_id => $shipping_modes) {
                        foreach ($shipping_modes as $shipping_mode_id => $shipping_mode_value) {
                            CityDelivery::create([
                                'city_id' => $id,
                                'booking_type_id' => $booking_type_id,
                                'shipping_mode_id' => $shipping_mode_id,
                            ]);
                        }
                    }
                    if ($request->osa_name != null) {
                        foreach ($request->osa_name as $key => $value) {

                            $osa_charges = new CityOsaRate();
                            $osa_charges->city_id = $id;
                            $osa_charges->osa_name = $value;
                            $osa_charges->osa_rate = $request->osa_rate[$key];
                            $osa_charges->admin_id = Auth::id();
                            $osa_charges->save();
                        }
                    }

                    $admin_ids = Admin::where('management_user', 1)->pluck('id')->toArray();
                    self::addManagementHubUser($admin_ids, $id);



                    //Check if Closest Hub is Selected then auto assign mappings according to the selected hub to the new newly created hub and delete all current mappings
                    if ($request->closest_hub) {
                        $closestHubId = $request->closest_hub;

                        // get all mappings ids of the edited hub
                        $mappings = V2JunctionMapping::where('origin_id', $id)->orWhere('destination_id', $id)->pluck('id')->toArray();

                        //delete current junctions
                        V2Junctions::whereIn('junction_mapping_id', $mappings)->delete();

                        //delete current junction_routes
                        $v2JunctionRoutes = V2JunctionRoutes::whereIn('junction_mapping_id', $mappings)->pluck('id')->toArray();

                        //delete current junction_vehicles
                        V2JunctionVehicles::whereIn('junction_route_id', $v2JunctionRoutes)->delete();

                        V2JunctionRoutes::whereIn('id', $v2JunctionRoutes)->delete();
                        V2JunctionMapping::whereIn('id', $mappings)->delete();
                        //All current mappings of this city are removed now
                        //--------x------------x-------------x-------------x----------------

                        //now create new mappings according to the selected hub
                        $city = City::find($id);
                        $this->makeDynamicHubsMapping($request->vehicles, $closestHubId, $city);
                    }
                
                    $this->logCityChangesAfterUpdate($oldCity);
                    return redirect()->back()->with('success', 'Hub/city updated successfully');
                }
            } else {
                return redirect()->back()->with('error', 'Please select atleast one shipping mode!');
            }
        }

    }

    //update city end
    public function addCityHub(Request $request)
    {

        if ($request->postType == 'city') {
            $zone_id = City::find($request->hubs)->zone_id;

            $city = City::create([
                'name' => $request->cityName,
                'city_code' => $request->city_code,
                'hub' => 0,
                'hub_id' => $request->hubs,
                'zone_id' => $zone_id,
                'pickup' => ($request->has('pickup')) ? 1 : 0,
                'gc_area' => ($request->has('gc_area')) ? 1 : 0,
                'attempt_tat' => $request->attempt_tat,
                'status' => 1,
                'created_by' => Auth::id(),
                'location_latitude' => $request->latitude,
                'location_longitude' => $request->longitude,
                'hub_location_latitude' => $request->hub_latitude,
                'hub_location_longitude' => $request->hub_longitude,
                'address' => $request->address,
                'pickup_cut_off_time' => $request->pickup_cut_off_time
            ]);

            CityHistory::create([
                'city_id' => $city->id,
                'hub' => 0,
                'hub_id' => $request->hubs,
                'zone_id' => $zone_id,
                'pickup' => ($request->has('pickup')) ? 1 : 0,
                'status' => 1,
                'gc_area' => ($request->has('gc_area')) ? 1 : 0,
                'attempt_tat' => $request->attempt_tat,
                'updated_by' => Auth::id(),
                'location_latitude' => $request->latitude,
                'location_longitude' => $request->longitude,
                'hub_location_latitude' => $request->hub_latitude,
                'hub_location_longitude' => $request->hub_longitude,
                'address' => $request->address,
                'pickup_cut_off_time' => $request->pickup_cut_off_time
            ]);

            if (!empty($request->walk_in_delivery)) {
                foreach ($request->walk_in_delivery as $index => $delivery_walk_in) {
                    WalkInCities::create([
                        'city_id' => $city->id,
                        'pickup' => ($request->has('pickup')) ? 1 : 0,
                        'delivery' => $index,
                    ]);
                }
            }

            foreach ($request->delivery as $booking_type_id => $shipping_modes) {
                foreach ($shipping_modes as $shipping_mode_id => $shipping_mode_value) {
                    CityDelivery::create([
                        'city_id' => $city->id,
                        'booking_type_id' => $booking_type_id,
                        'shipping_mode_id' => $shipping_mode_id,
                    ]);
                }
            }

            $zones = Zone::where('business_category_id', 1)->get();
            foreach ($zones as $zone) {
                $zone_class_city = new ZoneClassCity();

                $zone_class_city->zone_id = $zone->id;
                $zone_class_city->city_id = $city->id;
                $zone_class_city->class = 3;
                $zone_class_city->zone_classification_id = 1;

                $zone_class_city->save();

                $zone_class_city = new ZoneClassCity();

                $zone_class_city->zone_id = $zone->id;
                $zone_class_city->city_id = $city->id;
                $zone_class_city->class = 3;
                $zone_class_city->zone_classification_id = 2;

                $zone_class_city->save();
            }
            if ($request->osa_name != null) {

                foreach ($request->osa_name as $key => $value) {

                    $osa_charges = new CityOsaRate();
                    $osa_charges->city_id = $city->id;
                    $osa_charges->osa_name = $value;
                    $osa_charges->osa_rate = $request->osa_rate[$key];
                    $osa_charges->admin_id = Auth::id();
                    $osa_charges->save();
                }
            }

            return redirect()->back()->with('success', 'City added successfully');
        } elseif ($request->postType == 'hub') {

            $city = City::create([
                'name' => $request->cityName,
                'city_code' => $request->city_code,
                'hub' => 1,
                'zone_id' => $request->zone_id,
                'province_id' => $request->province_id,
                'pickup' => ($request->has('pickup')) ? 1 : 0,
                'gc_area' => ($request->has('gc_area')) ? 1 : 0,
                'attempt_tat' => $request->attempt_tat,
                'status' => 1,
                'created_by' => Auth::id(),
                'location_latitude' => $request->latitude,
                'location_longitude' => $request->longitude,
                'hub_location_latitude' => $request->hub_latitude,
                'hub_location_longitude' => $request->hub_longitude,
                'address' => $request->address,
                'pickup_cut_off_time' => $request->pickup_cut_off_time
            ]);

            CityHistory::create([
                'city_id' => $city->id,
                'hub' => 1,
                'zone_id' => $request->zone_id,
                'pickup' => ($request->has('pickup')) ? 1 : 0,
                'status' => 1,
                'gc_area' => ($request->has('gc_area')) ? 1 : 0,
                'attempt_tat' => $request->attempt_tat,
                'updated_by' => Auth::id(),
                'location_latitude' => $request->latitude,
                'location_longitude' => $request->longitude,
                'hub_location_latitude' => $request->hub_latitude,
                'hub_location_longitude' => $request->hub_longitude,
                'address' => $request->address,
                'pickup_cut_off_time' => $request->pickup_cut_off_time
            ]);

            if (!empty($request->walk_in_delivery)) {
                foreach ($request->walk_in_delivery as $index => $delivery_walk_in) {
                    WalkInCities::create([
                        'city_id' => $city->id,
                        'pickup' => ($request->has('pickup')) ? 1 : 0,
                        'delivery' => $index,
                    ]);
                }
            }

            City::where('id', $city->id)->update(['hub_id' => $city->id]);

            foreach ($request->delivery as $booking_type_id => $shipping_modes) {
                foreach ($shipping_modes as $shipping_mode_id => $shipping_mode_value) {
                    CityDelivery::create([
                        'city_id' => $city->id,
                        'booking_type_id' => $booking_type_id,
                        'shipping_mode_id' => $shipping_mode_id,
                    ]);
                }
            }

            $zones = Zone::where('business_category_id', 1)->get();
            foreach ($zones as $zone) {
                $zone_class_city = new ZoneClassCity();

                $zone_class_city->zone_id = $zone->id;
                $zone_class_city->city_id = $city->id;
                $zone_class_city->class = 3;
                $zone_class_city->zone_classification_id = 1;

                $zone_class_city->save();

                $zone_class_city = new ZoneClassCity();

                $zone_class_city->zone_id = $zone->id;
                $zone_class_city->city_id = $city->id;
                $zone_class_city->class = 3;
                $zone_class_city->zone_classification_id = 2;

                $zone_class_city->save();
            }
            if ($request->osa_name != null) {

                foreach ($request->osa_name as $key => $value) {

                    $osa_charges = new CityOsaRate();
                    $osa_charges->city_id = $city->id;
                    $osa_charges->osa_name = $value;
                    $osa_charges->osa_rate = $request->osa_rate[$key];
                    $osa_charges->admin_id = Auth::id();
                    $osa_charges->save();
                }
            }

            $admin_ids = Admin::where('management_user', 1)->pluck('id')->toArray();
            self::addManagementHubUser($admin_ids, $city->id);

            //Check if Closest Hub is Selected then auto assign mappings according to the selected hub to the new newly created hub.
            if ($request->closest_hub) {
                $closestHubId = $request->closest_hub;
                $this->makeDynamicHubsMapping($request->vehicles, $closestHubId, $city);
            }
   
        }

        return redirect()->back()->with('success', 'Hub city added successfully');

    }

    private function makeDynamicHubsMapping($requestVehicles, $closestHubId, $city)
    {
            //take this hub as reference hub
            $authId = Auth::id();

            DB::beginTransaction();

            try {

                // for creating mappings from origin to destination (1st way)
                $closestHubOriginMappings = V2JunctionMapping::where('origin_id', $closestHubId)->get();

                $closestHubDestinationMappings = V2JunctionMapping::where('destination_id', $closestHubId)->get();

                $newJunctions1 = [];
                $newRouteVehicles1 = [];

                foreach ($closestHubOriginMappings as  $closestHubMapping) {
                    
                    $mapping = new V2JunctionMapping();

                    $mapping->origin_id = $city->id; //here origin will be the newly created hub for all mappings
                    $mapping->destination_id = $closestHubMapping->destination_id;//destinations will be of the closest hub
                    $mapping->status = $closestHubMapping->status;
                    $mapping->updated_by = $authId;
                    $mapping->save();

                    $junctions = V2Junctions::where('junction_mapping_id', $closestHubMapping->id)->get();


                    //--------------x---------x---------x--------x-------x---------x----------x--------x
                    // TO-6836 (Adding the reference hub as junction in new mappings)
                    // $newJunctions1[] = [
                    //     'junction_mapping_id' => $mapping->id,
                    //     'junction_id' => $closestHubId,
                    //     'created_at' => now(),
                    //     'updated_at' => now()
                    // ];

                    // $junctionRoute = new V2JunctionRoutes();
                    // $junctionRoute->junction_mapping_id = $mapping->id;
                    // $junctionRoute->starting_hub_id = $mapping->origin_id;
                    // $junctionRoute->ending_hub_id = $mapping->destination_id;
                    // $junctionRoute->created_at = now();
                    // $junctionRoute->save();

                    // foreach ($requestVehicles as $vehicle) {
                    //     $junctionRouteVehicles[] = [
                    //         'junction_route_id' => $junctionRoute->id,
                    //         'vehicle_id' => $vehicle,
                    //         'created_at' => now(),
                    //         'updated_at' => now()
                    //     ];
                    // }
                    // V2JunctionVehicles::insert($junctionRouteVehicles);

                    //--------------x---------x---------x--------END TO-6836-------x---------x----------x--------x

                    $newJunctions1[] = [
                        'junction_mapping_id' => $mapping->id,
                        'junction_id' => $closestHubId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                    foreach ($junctions as $j) {
                        $newJunctions1[] = [
                            'junction_mapping_id' => $mapping->id,
                            'junction_id' => $j->junction_id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }

                    $previous = $mapping->origin_id;

                    $routeJunctions = V2JunctionRoutes::where('junction_mapping_id', $closestHubMapping->id)->get();

                    foreach ($routeJunctions as $rj) {
                        $route_junction = new V2JunctionRoutes();
                        $route_junction->junction_mapping_id = $mapping->id;
                        $route_junction->starting_hub_id = $previous;
                        $route_junction->ending_hub_id = $rj->starting_hub_id;
                        $route_junction->save();
            
                        $previous = $rj->starting_hub_id;

                        $vehicles = V2JunctionVehicles::where('junction_route_id', $rj->id)->get();
            
                        foreach ($vehicles as $vehicle) {
                            $newRouteVehicles1[] = [
                                'junction_route_id' => $route_junction->id,
                                'vehicle_id' => $vehicle->vehicle_id,
                                'created_at' => now(),
                                'updated_at' => now()
                            ];
                        }
                    }

                    $junctionRoute = new V2JunctionRoutes();
                    $junctionRoute->junction_mapping_id = $mapping->id;
                    $junctionRoute->starting_hub_id = $previous;
                    $junctionRoute->ending_hub_id = $mapping->destination_id;
                    $junctionRoute->created_at = now();
                    $junctionRoute->save();

                    foreach ($requestVehicles as $vehicle) {
                        $junctionRouteVehicles[] = [
                            'junction_route_id' => $junctionRoute->id,
                            'vehicle_id' => $vehicle,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                    V2JunctionVehicles::insert($junctionRouteVehicles);
                    
                }

                V2Junctions::insert($newJunctions1);
                V2JunctionVehicles::insert($newRouteVehicles1);

                //for creating mapping between the newly created hub and the closest hub
                $mapping1 = new V2JunctionMapping();

                $mapping1->origin_id = $city->id;
                $mapping1->destination_id = $closestHubId;
                $mapping1->updated_by = Auth::id();
                $mapping1->save();

                $route_junction1 = new V2JunctionRoutes();
                $route_junction1->junction_mapping_id = $mapping1->id;
                $route_junction1->starting_hub_id = $mapping1->origin_id;
                $route_junction1->ending_hub_id = $mapping1->destination_id;
                $route_junction1->save();

                $singleRouteVehicles1 = [];
                foreach ($requestVehicles as $vehicle) {
                    $singleRouteVehicles1[] = [
                        'junction_route_id' => $route_junction1->id,
                        'vehicle_id' => $vehicle,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                V2JunctionVehicles::insert($singleRouteVehicles1);

                // --------------x-------------------x-------------x---------------

                // --------------x-------------------x-------------x---------------

                // for creating mappings from destination to origin (2nd way)

                $newJunctions2 = [];
                $newRouteVehicles2 = [];

                foreach ($closestHubDestinationMappings as  $closestHubMapping) {
                    
                    $mapping = new V2JunctionMapping();

                    $mapping->origin_id = $closestHubMapping->origin_id; //here origin will be the closest hub for all mappings
                    $mapping->destination_id = $city->id;//here destination will be the newly created hub for all mappings
                    $mapping->status = $closestHubMapping->status;
                    $mapping->updated_by = $authId;
                    $mapping->save();

                    $junctions = V2Junctions::where('junction_mapping_id', $closestHubMapping->id)->get();

                    foreach ($junctions as $j) {
                        $newJunctions2[] = [
                            'junction_mapping_id' => $mapping->id,
                            'junction_id' => $j->junction_id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }

                    //------x--------x------x-------x------x------x-------x-------x--------x
                    // TO-6836 (Adding the reference hub as junction in new mappings)
                    // $newJunctions2[] = [
                    //     'junction_mapping_id' => $mapping->id,
                    //     'junction_id' => $closestHubId,
                    //     'created_at' => now(),
                    //     'updated_at' => now()
                    // ];

                    
                    // $junctionRoute2 = new V2JunctionRoutes();
                    // $junctionRoute2->junction_mapping_id = $mapping->id;
                    // $junctionRoute2->starting_hub_id = $mapping->origin_id;
                    // $junctionRoute2->ending_hub_id = $mapping->destination_id;
                    // $junctionRoute2->created_at = now();
                    // $junctionRoute2->save();

                    // foreach ($requestVehicles as $vehicle) {
                    //     $junctionRoute2Vehicles[] = [
                    //         'junction_route_id' => $junctionRoute2->id,
                    //         'vehicle_id' => $vehicle,
                    //         'created_at' => now(),
                    //         'updated_at' => now()
                    //     ];
                    // }
                    // V2JunctionVehicles::insert($junctionRoute2Vehicles);

                    //------x--------x------x-------x------END TO-6836------x-------x-------x--------x

                    $newJunctions2[] = [
                        'junction_mapping_id' => $mapping->id,
                        'junction_id' => $closestHubId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                    $previous = $mapping->origin_id;

                    $routeJunctions = V2JunctionRoutes::where('junction_mapping_id', $closestHubMapping->id)->get();

                    foreach ($routeJunctions as $rj) {
                        $route_junction = new V2JunctionRoutes();
                        $route_junction->junction_mapping_id = $mapping->id;
                        $route_junction->starting_hub_id = $previous;
                        $route_junction->ending_hub_id = $rj->ending_hub_id;
                        $route_junction->save();
            
                        $previous = $rj->ending_hub_id;

                        $vehicles = V2JunctionVehicles::where('junction_route_id', $rj->id)->get();
            
                        foreach ($vehicles as $vehicle) {
                            $newRouteVehicles2[] = [
                                'junction_route_id' => $route_junction->id,
                                'vehicle_id' => $vehicle->vehicle_id,
                                'created_at' => now(),
                                'updated_at' => now()
                            ];
                        }
                    }

                    $junctionRoute2 = new V2JunctionRoutes();
                    $junctionRoute2->junction_mapping_id = $mapping->id;
                    $junctionRoute2->starting_hub_id = $previous;
                    $junctionRoute2->ending_hub_id = $mapping->destination_id;
                    $junctionRoute2->created_at = now();
                    $junctionRoute2->save();

                    foreach ($requestVehicles as $vehicle) {
                        $junctionRoute2Vehicles[] = [
                            'junction_route_id' => $junctionRoute2->id,
                            'vehicle_id' => $vehicle,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                    V2JunctionVehicles::insert($junctionRoute2Vehicles);
                    
                }

                V2Junctions::insert($newJunctions2);
                V2JunctionVehicles::insert($newRouteVehicles2);

                //for creating second way mapping between the newly created hub and the closest hub
                $mapping2 = new V2JunctionMapping();

                $mapping2->origin_id = $closestHubId;
                $mapping2->destination_id = $city->id;
                $mapping2->updated_by = Auth::id();
                $mapping2->save();

                $route_junction2 = new V2JunctionRoutes();
                $route_junction2->junction_mapping_id = $mapping2->id;
                $route_junction2->starting_hub_id = $mapping2->origin_id;
                $route_junction2->ending_hub_id = $mapping2->destination_id;
                $route_junction2->save();

                $singleRouteVehicles2 = [];
                foreach ($requestVehicles as $vehicle) {
                    $singleRouteVehicles2[] = [
                        'junction_route_id' => $route_junction2->id,
                        'vehicle_id' => $vehicle,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                V2JunctionVehicles::insert($singleRouteVehicles2);

                // --------------x-------------------x-------------x---------------

                // --------------x-------------------x-------------x----------

                // Commit the transaction
                DB::commit();

            } catch (\Exception $e) {
                // Rollback the transaction if any error occurs
                DB::rollBack();
                throw $e;
            }
    }

    public function CityStatus(Request $request)
    {
        $id = $request->cid; //city id
        $status = $request->status;
        if ($status == 'cityInactive') {
            $city = City::find($id);
            if ($city->status == 1 && $city->hub == 1) {
                $citylist = City::where('hub_id', $id)->where('id', '!=', $id)->where('status', 1)->count();
                if ($citylist == 0) {
                    City::where('id', $city->id)->update(['status' => 0]);
                    $zone_cities = City::where('zone_id', $city->zone_id)->where('status', 1)->count();
                    if ($zone_cities == 0) {
                        $zone = Zone::find($city->zone_id);
                        $zone->status = 0;
                        $zone->save();
                    }
                    $this->logCityStatusChanges((array) $id, false, 2);
                    return redirect()->route('admin.management.city')->with('success', 'City is inactive now.');
                } else {
                    return redirect()->route('admin.management.city')->with('error', 'There are some active cities in hub, please deactivate those cities first!');
                }
            } elseif ($city->status == 1) {
                $city->status = 0;
                $city->save();

                $this->logCityStatusChanges((array) $id, false, 2);
                return redirect()->route('admin.management.city')->with('success', 'City is inactive now.');
            }
        } elseif ($status == 'cityactive') {
            $city = City::find($id);
            $zone = Zone::find($city->zone_id);
            if ($zone->status != 1) {
                return redirect()->route('admin.management.city')->with('error', 'Please, activate or change Zone for city first!');
            }
            if ($city->hub == 1) {
                if ($city->status == 0) {
                    $city->status = 1;
                    $city->save();
                    $this->logCityStatusChanges((array) $id, true, 2);
                    return redirect()->route('admin.management.city')->with('success', 'City is active now.');
                }
            } else {
                $hub = City::where('id', $city->hub_id)->where('status', '=', 1);
                if ($hub->exists()) {
                    $city->status = 1;
                    $city->save();
                    $this->logCityStatusChanges((array) $id, true, 2);
                    return redirect()->route('admin.management.city')->with('success', 'City is active now.');

                } else {
                    return redirect()->route('admin.management.city')->with('error', 'Please, activate or change hub for city first!');

                }
            }


            if ($city->status == 0) {
                $action = City::where('id', $city->id)->update(['status' => 1]);
                if ($action == 1) {
                    return redirect()->route('admin.management.city')->with('success', 'City is active now.');
                } else {
                    return redirect()->route('admin.management.city')->with('danger', 'There is some problem please try again.');
                }
            } else {
                return redirect()->route('admin.management.city')->with('danger', 'This city is already inactive.');

            }
        }
     

        return redirect()->route('admin.management.city')->with('danger', 'This city is already inactive.');
    }

    public function CityStatusCheck($id)
    {
        $hubs = City::select('name')->where('hub_id', $id)->where('id', '!=', $id)->where('status', 1)->get();

        return response()->json($hubs);
    }

    //route management
    public function routeView()
    {
        $route_type = RouteType::all();
        $users = User::join('user_shipping_infos as usi', 'usi.user_id', '=', 'users.id')->select('users.id', 'pickup_address', 'users.name', 'usi.id as address_id')->where('usi.status', 1)->get();
        return view('admin.management.route_management')->with(['users' => $users, 'route_type' => $route_type]);
    }

    public function routeListAjax()
    {
        $routes = Route::join('cities', 'routes.city_id', '=', 'cities.id')
            ->leftjoin('route_types as rt', 'rt.id', '=', 'routes.route_type_id')
            ->leftjoin('riders', 'riders.route_id', '=', 'routes.id')
            ->select(['cities.name as city', 'routes.id as id', 'routes.code as code', 'routes.start', 'routes.end', 'routes.junction', 'routes.status as status', 'routes.created_at as created', 'rt.id as route_type_id ', 'rt.name as route_type', 'riders.name as rider']);

        if (session('role_id') != 1) {
            $routes = $routes->whereIn('cities.hub_id', session('hubs'));
        }

        return Datatables::of($routes)
            ->editColumn('status', function ($routes) {
                return ($routes->status == 0) ? 'Inactive' : 'Active';
            })
            ->filterColumn('status', function ($query, $keyword) {
                $keyword = strtolower($keyword);

                if (strpos('active', $keyword) !== FALSE) {
                    $query->where('routes.status', '=', 1);
                } else if (strpos('inactive', $keyword) !== FALSE) {
                    $query->where('routes.status', '=', 0);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn("action", function ($result) {
                if (session('role_id') == 1 || count(array_intersect([94, 95], session('permissions'))) !== 0) {
                    $dropdown = '
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm">
                    ';

                    if (session('role_id') == 1 || in_array(94, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item" data-target-id=' . $result->id . ' rel="editroute" data-toggle="modal" data-target="#editRoute"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Update Route</div></button>';
                    }

                    if (session('role_id') == 1 || in_array(95, session('permissions'))) {
                        if ($result->status == 1) {
                            $dropdown .= '<button type="button" class="dropdown-item deactivate" data-target-id=' . $result->id . ' rel="routeInactive"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Deactivate Route</div></button>';
                        } else {
                            $dropdown .= '<button type="button" class="dropdown-item deactivate" data-target-id=' . $result->id . ' rel="routeActive"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Activate Route</div></button>';
                        }
                    }

//                    $dropdown .= '<button type="button" class="dropdown-item assign_location" data-target-id=' . $result->id . ' rel="assignlocation" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Assign Shipper</div></button>';


//                    $dropdown .= '<button type="button" class="dropdown-item view_location" data-target-id=' . $result->id . ' rel="assignlocation" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Shipper</div></button>';


                    $dropdown .= '
                        </div>
                      </div>
                    ';

                    return $dropdown;
                } else {
                    return '';
                }
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function addRouteView()
    {
        $route_types = RouteType::all();
        $cities = City::where('business_category_id', 1)->where('status', 1)->select(['id', 'name'])->get();
        $riders = Rider::where('status', 1)->select(['id', 'name'])->get();
        return view('admin.management.add_route_form')->with(['cities' => $cities, 'riders' => $riders, 'route_types' => $route_types]);
    }

    public function addRouteDetails(Request $request)
    {

        $validations = [
            'city_id' => 'required|numeric',
            'route_code' => 'required',
            'start' => 'required',
            'route_type_id' => 'required',
            'end' => 'required',
            'junction' => 'required'
        ];
        $validate = Validator::make($request->all(), $validations);

        if ($validate->fails()) {
            return redirect()->back()
                ->withErrors($validate);
        }
        $route = Route::create([
            'city_id' => $request->city_id,
            'code' => $request->route_code,
            'start' => $request->start,
            'route_type_id' => $request->route_type_id,
            'end' => $request->end,
            'junction' => $request->junction,
            'status' => 1
        ]);
        if (Rider::where('route_id', '=', $route->id)->exists()) {
            return redirect()->back()->with('error', 'Route id already exists !');
        } else {
            $rider_id = $request->rider_id;
            $rider = Rider::find($rider_id);
            if ($rider) {
                $rider->route_id = $route->id;
                $rider->save();
            }
        }
        return redirect()->back()->with('success', 'Route added successfully');
    }

    public function editRouteView($id)
    {
        $citylist = City::where('status', 1)->select(['id', 'name'])->get();
        $riders = Rider::where('status', 1)->select(['id', 'name'])->get();
        $route_types = RouteType::all();
        $current_rider = Rider::where('route_id', $id);
        if ($current_rider->exists()) {
            $current_rider = $current_rider->select('id')->first();
            $current_rider_id = $current_rider->id;
        } else {
            $current_rider_id = NULL;
        }
        $route = Route::find($id);
        $route_type_id = $route->route_type_id;
        $current_route_type_id = RouteType::find($route_type_id)->id;
        return view('admin.management.edit_route_form')->with(['route_id' => $id, 'cities' => $citylist, 'route' => $route, 'riders' => $riders, 'current_rider_id' => $current_rider_id, 'route_types' => $route_types, 'current_route_type_id' => $current_route_type_id]);
    }

    public function editRouteDetails(Request $request, $id)
    {
        $validations = [
            'city_id' => 'required|numeric',
            'route_code' => 'required',
            'start' => 'required',
            'end' => 'required',
            'route_type_id' => 'required',
            'junction' => 'required'
        ];
        $validate = Validator::make($request->all(), $validations);

        if ($validate->fails()) {
            return redirect()->back()
                ->withErrors($validate);
        }
        $route = Route::where('id', $id)->update([
            'city_id' => $request->city_id,
            'code' => $request->route_code,
            'start' => $request->start,
            'end' => $request->end,
            'route_type_id' => $request->route_type_id,
            'junction' => $request->junction,
        ]);

        $existing_riders = Rider::where('route_id', $id);
        if ($existing_riders->exists()) {
            $existing_riders = $existing_riders->get();
            foreach ($existing_riders as $existing_rider) {
                $existing_rider->route_id = null;
                $existing_rider->save();
            }
        }

//        if(Rider::where('route_id','=',$id)->exists()){
//            return redirect()->back()->with('success', 'Details Updated !');
//        }
//        else{
        $rider_id = $request->rider_id;
        $rider = Rider::find($rider_id);
        if ($rider) {
            $rider->route_id = $id;
            $rider->save();
        }
//        }
        return redirect()->back()->with('success', 'Route updated successfully');
    }

    public function routeStatus(Request $request)
    {
        $id = $request->cid;
        $status = $request->status;
//        return $id;
        if ($status == 'routeActive') {
            $route = Route::where('id', $id)->update(['status' => 1]);
            if ($route) {
                return redirect()->back()->with('success', 'Route is activated successfully');
            }
        } else if ($status == 'routeInactive') {
            Route::where('id', $id)->update(['status' => 0]);
            return redirect()->back()->with('success', 'Route is now inactive');

        }

    }

    public function riderView()
    {
        //$route_types = RouteType::all();
        $category = RiderCategory::all();
        return view('admin.management.rider_management')->with(['categories' => $category/*,'route_types' =>$route_types*/]);
    }

    public function riderListAjax()
    {

        $rider = Rider::join('cities', 'riders.city_id', '=', 'cities.id')
            ->join('cities as c', 'cities.hub_id', '=', 'c.id')
            ->join('routes', 'routes.id', '=', 'riders.route_id')
            ->join('rider_categories', 'rider_categories.id', '=', 'riders.rider_category_id')
            ->leftjoin('admins as cb', 'cb.id', '=', 'riders.created_by')
            ->leftjoin('admins as ub', 'ub.id', '=', 'riders.updated_by')
            ->select(['cities.name as city', 'c.name as hub', 'riders.id as rider_id', 'riders.id', 'riders.name as rider', 'riders.trax_id', 'riders.phone', 'riders.cnic', 'riders.address', 'routes.code as route', 'routes.start', 'routes.end', 'rider_categories.name as category', 'riders.status as                           status', 'riders.created_at', 'cb.name as created_by', 'ub.name as updated_by']);

        if (session('role_id') != 1) {
            $rider = $rider->whereIn('cities.hub_id', session('hubs'));
        }

        return Datatables::of($rider)
            ->editColumn('status', function ($rider) {
                return ($rider->status == 0) ? 'Inactive' : 'Active';
            })
            ->editColumn('trax_id', function ($rider) {
                if ($rider->trax_id != null) {
                    return $rider->trax_id;
                } else {
                    return '-';
                }

            })
            ->editColumn('route', function ($rider) {
                return $rider->route . ' (' . $rider->start . ' to ' . $rider->end . ')';
            })
            ->filterColumn('route', function ($query, $keyword) {
                $keyword = strtolower($keyword);
                if ($keyword != '') {
                    $query->where('routes.code', 'like', '%' . $keyword . '%')->orWhere('routes.start', 'like', '%' . $keyword . '%')->orWhere('routes.end', 'like', '%' . $keyword . '%');
                } else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn("action", function ($rider) {
                if (session('role_id') == 1 || count(array_intersect([98, 99], session('permissions'))) !== 0) {
                    $dropdown = '
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm">
                    ';

                    if (session('role_id') == 1 || in_array(98, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item" data-target-id=' . $rider->id . ' rel="editRider" data-toggle="modal" data-target="#editRider"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Update Rider</div></button>';
                    }

                    if (session('role_id') == 1 || in_array(99, session('permissions'))) {
                        if ($rider->status == 1) {
                            $dropdown .= '<button type="button" class="dropdown-item deactivate" data-target-id=' . $rider->id . '  rel="riderInactive"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Deactivate Rider</div></button>';
                        } else {
                            $dropdown .= '<button type="button" class="dropdown-item deactivate" data-target-id=' . $rider->id . '  rel="riderActive"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Activate Rider</div></button>';
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
            })
            ->make(true);
    }

    public function addRiderView()
    {
        $city = City::where('business_category_id', 1)->select(['id', 'name'])->get();
        $route_types = RouteType::all();
        $category = RiderCategory::all();
        return view('admin.management.add_rider_form')->with(['cities' => $city, 'categories' => $category, 'route_types' => $route_types]);
    }

    public function categoryListAjax(Request $request)
    {
        $city_id = $request->id;
        $route = Route::select(['id', 'code', 'start', 'end'])->where('city_id', $city_id)->where('status', 1);

        if (session('role_id') != 1) {
            $route = $route->whereHas('city', function ($query) {
                $query->whereIn('hub_id', session('hubs'));
            });
        }

        $route = $route->get();

        $city_areas = CityArea::where('city_id',$city_id)->get();
        
        return response()->json(['route' => $route, 'areas' => $city_areas]);
    }
    
    public function replacementListAjax(Request $request)
    {
        // This is employee list for if staff category type is contractual it return only contractual employees list because pnly contractual can replace contractual employee
        $employee = Employee::find($request->employee_id);
        $replacement_employees = Employee::select('id', 'name', 'trax_id','last_working_date')
        ->where('employee_type_id', $employee->employee_type_id)
        ->whereNotNull('trax_id');
        if($employee->staff_category_id == 3){

            $replacement_employees = $replacement_employees->where('staff_category_id', 3);
        }
        $replacement_employees = $replacement_employees->get();

        return response()->json(['replacement_employees'=>$replacement_employees]);
    }

    public function addRiderDetails(Request $request)
    {

        $validations = [
            'city_id' => 'required|numeric',
            'rider_name' => 'required|max:255',
            'phone' => 'required|max:255',
            'cnic' => 'required|max:255',
            'address' => 'required|max:255',
            'route_id' => 'required|numeric',
            'rider_category' => 'required|numeric',
            'pin' => 'required|numeric',
        ];
        $validate = Validator::make($request->all(), $validations);

        if ($validate->fails()) {
            return redirect()->back()
                ->withErrors($validate);
        }
        $global_setting = GlobalSettings::where('type', 'latest_employee_id');

        if ($global_setting->exists()) {
            $global_setting = $global_setting->first();
            $trax_id = $global_setting->setting_value + 1;
            $global_setting->setting_value = $trax_id;
            $global_setting->save();
            $trax_id = 'Trax' . str_pad($trax_id, 5, '0', STR_PAD_LEFT);
        } else {
            $trax_id = null;
        }
        $rider = Rider::create([
            'city_id' => $request->city_id,
            'name' => $request->rider_name,
            'phone' => $request->phone,
            'cnic' => $request->cnic,
            'address' => $request->address,
            'route_id' => $request->route_id,
            'rider_category_id' => $request->rider_category,
            'status' => 1,
            'special_rider' => ($request->has('special_rider_checkbox') ? 1 : 0),
            'pin' => bcrypt($request->pin),
            'created_by' => Auth::id(),
            'trax_id' => $trax_id,
        ]);
        if ($rider) {
            NotificationsController::send(61, $rider->id, $request->pin);
            return redirect()->back()->with('success', 'Rider added successfully');
        }

    }

    public function editRiderView($id)
    {
        $city = City::where('business_category_id', 1)->select(['id', 'name'])->get();
        $category = RiderCategory::all();
        $rider = Rider::find($id);
        $route_types = RouteType::all();
        $route = Route::where('city_id', $rider->city_id)->get();
        return view('admin.management.edit_rider_form')->with(['rider_id' => $id, 'cities' => $city, 'categories' => $category, 'rider' => $rider, 'routes' => $route, 'route_types' => $route_types]);
    }

    public function editRiderDetails(Request $request, $id)
    {

        $validations = [
            'city_id' => 'required|numeric',
            'rider_name' => 'required|max:255',
            'phone' => 'required|max:255',
            'cnic' => 'required|max:255',
            'address' => 'required|max:255',
            'route_id' => 'required|numeric',
            'rider_category' => 'required|numeric'
        ];
        $validate = Validator::make($request->all(), $validations);

        if ($validate->fails()) {
            return redirect()->back()
                ->withErrors($validate);
        }
        // $rider = Rider::where('id',$id)->update([
        //     'city_id'=>$request->city_id,
        //     'name'=>$request->rider_name,
        //     'phone'=>$request->phone,
        //     'cnic'=>$request->cnic,
        //     'address'=>$request->address,
        //     'route_id'=>$request->route_id,
        //     'rider_category_id'=>$request->rider_category,
        //     'special_rider' => ($request->has('special_rider_checkbox')? 1:0)
        // ]);

        $rider = Rider::find($id);

        $rider->city_id = $request->city_id;
        $rider->name = $request->rider_name;
        $rider->phone = $request->phone;
        $rider->cnic = $request->cnic;
        $rider->address = $request->address;
        $rider->route_id = $request->route_id;

        $rider->rider_category_id = $request->rider_category;

        if ($request->has('special_rider_checkbox')) {
            $rider->special_rider = 1;
        } else {
            $rider->special_rider = 0;
        }

        if ($request->pin != '') {
            if ($rider->dummy_pin != $request->pin) {
                $rider->pin = bcrypt($request->pin);
                $rider->dummy_pin = $request->pin;

                NotificationsController::send(61, $rider->id, $request->pin);
            }
        }
        $rider->updated_by = Auth::id();
        $rider->save();

        if ($rider) {
            return redirect()->back()->with('success', 'Rider updated successfully');
        }
    }

    public function riderStatus(Request $request)
    {
        $id = $request->cid;
        $status = $request->status;
        if ($status == 'riderActive') {
            $rider = Rider::where('id', $id)->update(['status' => 1, 'updated_by' => Auth::id()]);
            if ($rider) {
                return redirect()->back()->with('success', 'Rider is activated successfully');
            }
        } else if ($status == 'riderInactive') {
            $rider = Rider::where('id', $id)->update(['status' => 0, 'route_id' => null, 'updated_by' => Auth::id()]);
            if ($rider) {
                return redirect()->back()->with('success', 'Rider is now inactive');
            }

        }

    }

    public function rider_phone_unique(Request $request)
    {
        if ($request->filled('phone')) {
            if ($request->input('phone') == '0213-8772222') {
                return 'true';
            } else {
                $rider = Rider::where('phone', $request->input('phone'));

                if ($request->has('id')) {
                    $rider = $rider->where('id', '!=', $request->input('id'));
                }

                if (!$rider->exists()) {
                    return 'true';
                } else {
                    return 'false';
                }
            }
        } else {
            return 'true';
        }
    }

    public function add_notification_emails(Request $request)
    {
        $emails = $request->email_address;
        if ($emails != '') {
            $email_address = explode(',', $emails);
            $user = $request->add_shipper_id;
            ShipperNotificationEmail::where('user_id', $user)->delete();
            foreach ($email_address as $email) {

                $shipper_notification_email = new ShipperNotificationEmail();
                $shipper_notification_email->user_id = $user;
                $shipper_notification_email->email = $email;
                $shipper_notification_email->save();

            }
            return redirect()->back()->with('success', 'Email Address Added.');

        } else {
            return back()->with('danger', 'There is no email selected!');
        }
    }

    public function edit_notification_emails(Request $request)
    {

        $emails = $request->email_address;
        if ($emails != '') {
            $email_address = explode(',', $emails);
            $user = $request->edit_shipper_id;
            foreach ($email_address as $email) {
                if (!ShipperNotificationEmail::where('user_id', $user)->where('email', '=', $email)->exists()) {
                    $shipper_notification_email = new ShipperNotificationEmail();
                    $shipper_notification_email->user_id = $user;
                    $shipper_notification_email->email = $email;
                    $shipper_notification_email->save();
                }
            }
            ShipperNotificationEmail::where('user_id', $user)->whereNotIn('email', $email_address)->delete();
            return redirect()->back()->with('success', 'Email Address updated.');

        } else {
            return back()->with('danger', 'There is no email selected!');
        }
    }

    public function walk_in_city_list()
    {
        $cities = City::where('status', 1)->select('id', 'name')->get();
        $walk_in_cities = WalkInCities::all();
        $shipping_modes = ShippingMode::where('id', '<', 4)->get();
        $pickup_cities = City::select('id', 'name')->where('pickup', 1)->get();
        $delivery_types = DeliveryType::get();
        return view('admin.management.walk_in_city_list')->with(['cities' => $cities, 'walk_in_cities' => $walk_in_cities, 'shipping_modes' => $shipping_modes, 'pickup_cities' => $pickup_cities, 'delivery_types' => $delivery_types]);
    }

    public function check_min_charges(Request $request)
    {
        if ($request->pickup_city != null && $request->consignee_city != null) {
            $min_charges = WalkInStandardWeightCharge::where(['shipping_mode_id' => $request->shipping_mode, 'delivery_type_id' => $request->delivery_type])->first();
            if ($request->pickup_city == $request->consignee_city) {
                $min_charges = $min_charges['chargeable_weight_local'];
            } else {
                $city = City::where('id', $request->consignee_city)->first();
                $zone_class = ZoneClassCity::where(['zone_id' => $city['zone_id'], 'city_id' => $request->consignee_city])->first();
                if ($zone_class['class'] == 1) {
                    $min_charges = $min_charges['chargeable_weight_charges_class_1'];
                } elseif ($zone_class['class'] == 2) {
                    $min_charges = $min_charges['chargeable_weight_charges_class_2'];
                } elseif ($zone_class['class'] == 3) {
                    $min_charges = $min_charges['chargeable_weight_charges_class_3'];
                } else {
                    $min_charges = $min_charges['chargeable_weight_charges_class_0'];
                }
            }
            return ['status' => 1, 'min_charges' => $min_charges];
        }
    }

    public function add_sister_account_view(Request $request)
    {
        $user = User::leftjoin('cities as c', 'c.id', '=', 'users.city_id')->leftjoin('products as p', 'p.id', '=', 'users.product_id')->leftjoin('sale_person_tags as spt', 'spt.user_id', '=', 'users.id')->leftjoin('admins as a', 'a.id', '=', 'spt.admin_id')->select('users.id as id', 'users.name as company_name', 'c.name as city', 'users.poc as contact_name', 'users.phone as phone', 'users.address as address', 'users.email as email', 'users.status as status', 'p.product_name as product_type', 'a.name as tagged_to')->where('users.id', $request->id)->first();
        return view('admin.accounts.sister_accounts.add')->with(['first_account' => $user]);
    }

    public function get_account_info(Request $request)
    {
        $user_id = $request->id;
        $check_user = User::where('id', $user_id);
        if ($check_user->exists()) {
            $check_user = $check_user->first();
            if ($check_user->blacklist == 0) {
                $check_merged_accounts = MergedSisterAccount::where('user_id', $user_id);

                if (!$check_merged_accounts->exists()) {
                    $user = User::leftjoin('cities as c', 'c.id', '=', 'users.city_id')->leftjoin('products as p', 'p.id', '=', 'users.product_id')->leftjoin('sale_person_tags as spt', 'spt.user_id', '=', 'users.id')->leftjoin('admins as a', 'a.id', '=', 'spt.admin_id')->select('users.id as id', 'users.name as company_name', 'c.name as city', 'users.poc as contact_name', 'users.phone as phone', 'users.address as address', 'users.email as email', 'users.status as status', 'p.product_name as product_type', 'a.name as tagged_to')->where('users.id', $request->id)->first();

                    return ['status' => 0, 'info' => $user];
                } else {
                    return ['status' => 1, 'error' => "Already registered as a sister account"];
                }
            } else {
                return ['status' => 1, 'error' => "Account ID is blocked!"];
            }
        } else {
            return ['status' => 1, 'error' => "Account ID does'nt exists!"];
        }
    }

    public function add_sister_account_submit(Request $request)
    {
        $account_ids = explode(',', $request->account_ids);
        if (count($account_ids) > 1) {
            $merge_account_head = new MergedAccountHead();
            $merge_account_head->name = $request->group_name;
            $merge_account_head->created_by = Auth::id();
            $merge_account_head->save();
            foreach ($account_ids as $index => $account_id) {
                $sister_account = new MergedSisterAccount();
                $sister_account->merged_head_id = $merge_account_head->id;
                $sister_account->user_id = $account_id;
                $sister_account->save();

                foreach ($account_ids as $notify_index => $notify_account_id) {
                    if ($index != $notify_index) {
                        NotificationsController::send(36, $notify_account_id, $account_id);
                    }
                }
            }
            return redirect()->route('admin.accounts.merged_account.index')->with(['success' => "Accounts merged successfully."]);
        } else {
            return redirect()->back()->with('error', "Sister accounts are not selected!");
        }
    }

    public function merged_accounts_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 277);
        return view('admin.accounts.sister_accounts.merged_accounts.index');
    }

    public function merged_accounts_list(Request $request)
    {

        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 278);
        }

        $merged_accounts = MergedAccountHead::leftjoin('admins as ac', 'ac.id', '=', 'merged_account_heads.created_by')
            ->leftjoin('admins as au', 'au.id', '=', 'merged_account_heads.updated_by')
            ->select('merged_account_heads.id as id', 'merged_account_heads.name as name', 'merged_account_heads.created_at as created', 'merged_account_heads.updated_at as updated_at', 'ac.name as created_by', 'au.name as updated_by', DB::raw('(select count(id) from merged_sister_accounts where merged_sister_accounts.merged_head_id = merged_account_heads.id) as accounts'));
        return Datatables::of($merged_accounts)
            ->editColumn('accounts_button', function ($users) {
                return '<div class="text-center"><button type="button" class="btn btn-sm btn-outline-info accounts_button">' . $users->accounts . '</button></div>';
            })
            ->editColumn('updated_by', function ($users) {
                if ($users->updated_by != null) {
                    return $users->updated_by;
                } else {
                    return "-";
                }
            })
            ->editColumn('updated_at', function ($users) {
                if ($users->updated_by != null) {
                    return $users->updated_at;
                } else {
                    return "-";
                }
            })
            ->addColumn("action", function ($users) {
                $dropdown = '';
                if (session('role_id') == 1 || in_array(session('id'), session('sale_users_bypass')) || in_array(242, session('permissions'))) {
                    $dropdown .= '
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm">
                    ';

                    $dropdown .= '<button onclick="window.open(\'' . route('admin.accounts.sister_account.edit.index', ['id' => $users->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit Sister Account</div></button>';
                    $dropdown .= '<button type="button" class="dropdown-item mapping"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Mapping</div></button>';


                    $dropdown .= '
                        </div>
                      </div>
                    ';
                }
                return $dropdown;
            })
            ->rawColumns(['accounts_button', 'action'])
            ->make(true);

    }

    public function merged_accounts_info(Request $request)
    {
        $merged_head_id = $request->id;
        $accounts = MergedSisterAccount::leftjoin('users as u', 'u.id', '=', 'merged_sister_accounts.user_id')
            ->leftjoin('cities as c', 'c.id', '=', 'u.city_id')
            ->select('u.id as id', 'u.name as name', 'u.poc as poc', 'u.phone as phone', 'u.address as address', 'c.name as city')
            ->where('merged_head_id', $merged_head_id)
            ->get();
        return response(['accounts' => $accounts]);
    }

    public function edit_sister_account_view(Request $request)
    {
        $group_name = MergedAccountHead::where('id', $request->id)->first();
        $merged_accounts = MergedAccountHead::leftjoin('merged_sister_accounts as msa', 'msa.merged_head_id', '=', 'merged_account_heads.id')->leftjoin('users as u', 'u.id', '=', 'msa.user_id')->leftjoin('cities as c', 'c.id', '=', 'u.city_id')->leftjoin('products as p', 'p.id', '=', 'u.product_id')->leftjoin('sale_person_tags as spt', 'spt.user_id', '=', 'u.id')->leftjoin('admins as a', 'a.id', '=', 'spt.admin_id')->select('u.id as id', 'u.name as company_name', 'c.name as city', 'u.poc as contact_name', 'u.phone as phone', 'u.address as address', 'u.email as email', 'u.status as status', 'p.product_name as product_type', 'a.name as tagged_to')->where('merged_account_heads.id', $request->id)->groupBy('u.id')->get();
        return view('admin.accounts.sister_accounts.edit')->with(['merged_accounts' => $merged_accounts, 'group_name' => $group_name]);
    }

    public function edit_sister_account_submit(Request $request)
    {
        $account_ids = explode(',', $request->account_ids);
        $merged_accounts = MergedSisterAccount::where('merged_head_id', $request->merged_id)->pluck('user_id')->toArray();
        $new_merged = array_diff($account_ids, $merged_accounts);
        $remove_merged = array_diff($merged_accounts, $account_ids);
        $previous_accounts = array_diff($merged_accounts, $new_merged, $remove_merged);
        if (count($account_ids) > 1) {
            $merge_account_head = MergedAccountHead::where('id', $request->merged_id)->first();
            $merge_account_head->name = $request->group_name;
            $merge_account_head->updated_by = Auth::id();
            $merge_account_head->save();
            MergedSisterAccount::where('merged_head_id', $merge_account_head->id)->whereIn('user_id', $remove_merged)->delete();
            foreach ($remove_merged as $remove_account_id) {
                foreach ($previous_accounts as $previous) {
                    NotificationsController::send(37, $previous, $remove_account_id);
                }
            }
            foreach ($new_merged as $account_id) {
                $sister_account = new MergedSisterAccount();
                $sister_account->merged_head_id = $merge_account_head->id;
                $sister_account->user_id = $account_id;
                $sister_account->save();
                foreach ($previous_accounts as $previous) {
                    NotificationsController::send(36, $previous, $account_id);
                }
            }

            return redirect()->route('admin.accounts.merged_account.index')->with('success', "Sister accounts updated successfully!");
        } else {
            return redirect()->back()->with('error', "Sister accounts are not selected!");
        }
    }

    public function merged_accounts_mapping_info(Request $request)
    {
        $merged_accounts = MergedSisterAccount::leftjoin('users as u', 'u.id', '=', 'merged_sister_accounts.user_id')->select('u.id as id', 'u.name as company_name')->where('merged_sister_accounts.merged_head_id', $request->id)->get();
        $merged_mapping = MergedSisterAccountMapping::where('merged_head_id', $request->id)->select('head_user_id', 'sister_user_id')->get();
        return response(['merged_accounts' => $merged_accounts, 'merged_mapping' => $merged_mapping]);
    }

    public function merged_accounts_mapping_submit(Request $request)
    {
        $merged_account = MergedAccountHead::where('id', $request->id)->first();
        $merged_account->updated_by = Auth::id();
        $merged_account->save();
        MergedSisterAccountMapping::where('merged_head_id', $merged_account->id)->delete();
        if ($request->has('sister_account')) {
            foreach ($request->sister_account as $index => $account) {
                foreach ($request->sister_account[$index] as $sub_index => $switch) {
                    if ($switch == "on") {
                        $new_mapping = new MergedSisterAccountMapping();
                        $new_mapping->merged_head_id = $merged_account->id;
                        $new_mapping->head_user_id = $index;
                        $new_mapping->sister_user_id = $sub_index;
                        $new_mapping->save();
                    }
                }
            }
            return redirect()->back()->with('success', "Mapping updated successfully!");
        } else {
            return redirect()->back()->with('success', "Mapping updated successfully!");
        }
    }

    public function warehousing_active(Request $request)
    {
        $shipper = User::find($request->id);
        if ($shipper && ($shipper->warehousing == 0)) {
            $shipper->warehousing = 1;
            $shipper->save();

            return response()->json(['status' => 1, 'success' => 'Warehousing activated successfully!']);
        } else {
            return response()->json(['status' => 0, 'error' => 'Warehousing already activated!']);
        }
    }

    public function warehousing_inactive(Request $request)
    {
        $shipper = User::find($request->id);
        if ($shipper && ($shipper->warehousing == 1)) {
            $shipper->warehousing = 0;
            $shipper->save();

            return response()->json(['status' => 1, 'success' => 'Warehousing deactivated successfully!']);
        } else {
            return response()->json(['status' => 0, 'error' => 'Warehousing already deactivated!']);
        }
    }

    public function update_profile_password(Request $request)
    {
        return view('admin.profile.change_password');
    }

    public function update_profile_password_submit(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:4',
        ]);
        if ($request->password == $request->confirm_password) {
            $admin = Admin::where('id', Auth::id());

            $admin->update(['password' => Hash::make($request->password), 'dummy_pin' => $request->password, 'updated_by' => Auth::id()]);

            $admin = $admin->first();
            $employee = Employee::where('trax_id', $admin->trax_id)->where('trax_id', '!=', null);
            if ($employee->exists()) {
                $employee = $employee->first();
                $employee->pin = $admin->dummy_pin;
                $employee->update();
            }

//            if(session()->has('first_login') && session('first_login') != 1){
//                session(['first_login' => 1]);
//                Admin::where('id',Auth::id())->update(['first_login' => 1]);
//            }
            return redirect()->back()->with(['success' => "Pin Updated Successfully!"]);
        } else {
            return redirect()->back()->with(['error' => "The pin and confirmation pin do not match!"]);
        }
    }

    public function userDocuments($id)
    {
        $documents = UserDocumentAttachment::where('user_id', $id)->first();

        $user = User::find($id);
        if ($documents == null) {
            $documents = false;
        }
        return view('admin.profile.documents')->with(['id' => $id, 'documents' => $documents, 'document_status' => $user->documents_status, 'shipper' => $user->name]);
    }

    public function viewUserDocuments($id, $check, $pdf)
    {
        $user_documents = UserDocumentAttachment::where('user_id', $id)->first();
        if ($check == 'filled_and_signed_pdf') {
            $file = $user_documents->filled_and_signed_pdf;
        } elseif ($check == 'signed_acknowledgement_pdf') {
            $file = $user_documents->signed_acknowledgement_pdf;
        } elseif ($check == 'cnic_front_image') {
            $file = $user_documents->cnic_front_image;
        } elseif ($check == 'cnic_back_image') {
            $file = $user_documents->cnic_back_image;
        } elseif ($check == 'blank_cheque_image') {
            $file = $user_documents->blank_cheque_image;
        } elseif ($check == 'e_sign_image') {
            $file = $user_documents->e_sign_image;
        } else {
            return redirect()->back()->with('error', 'File not found!');
        }
        $url = Storage::url('users_attached_documents/' . $id . '/' . $file);
        return view('admin.profile.documents_view')->with(['url' => $url, 'pdf' => $pdf]);
    }

    public function approveDocuments($id, $approve, $reason)
    {

        $user = User::find($id);
        $user_document = UserDocumentAttachment::where('user_id', $id)->first();
        if ($approve == 1) {
            $user->documents_status = 2;
            $user->documents_status_reason = null;
            $user->save();
            $user_document->approved_at = Carbon::now();
            $user_document->approved_by = Auth::id();
            $user_document->save();
            return redirect()->back()->with(['success' => 'Files approved successfully']);

        } else if ($approve == 0) {
            $user->documents_status = 3;
            $user->documents_status_reason = $reason;
            $user->save();
            $user_document->rejected_at = Carbon::now();
            $user_document->rejected_by = Auth::id();
            $user_document->save();
            return redirect()->back()->with(['success' => 'Files rejected successfully']);
        } else {
            return redirect()->back()->with(['error' => 'Something went wrong']);
        }

    }

    public function userDocumentsEdit(Request $request)
    {
        $user_attachment = UserDocumentAttachment::where('user_id', $request->user_id)->first();
        if ($user_attachment) {
            return response()->json(['status' => 1, 'user_attachment' => $user_attachment]);
        } else {
            return response()->json(['status' => 0]);
        }
    }

    public function uploadDocuments(Request $request)
    {
        $validation = [
            'filled_and_signed_pdf' => 'mimes:pdf|max:5120',
            'signed_acknowledgement_pdf' => 'mimes:pdf|max:5120',
            'cnic_front_image' => 'mimes:png,jpeg,jpg|max:2048',
            'cnic_back_image' => 'mimes:png,jpeg,jpg|max:2048',
            'blank_cheque_image' => 'mimes:png,jpeg,jpg|max:2048',
        ];
        $validate = Validator::make($request->all(), $validation);

        if ($validate->fails()) {
            return redirect()->back()->with(['errors' => $validate->errors()]);
        }
        $date = Carbon::now()->format('Y_m_d');
        $user_attachment = UserDocumentAttachment::where('user_id', $request->user_id)->first();
        if ($user_attachment) {
            if ($request->hasFile('filled_and_signed_pdf')) {
                if ($user_attachment->filled_and_signed_pdf != NULL) {
                    Storage::disk('public')->delete('users_attached_documents/' . $request->user_id . '/' . $user_attachment->filled_and_signed_pdf);
                }
                $filename = 'filled_and_signed_pdf_' . $date . '_' . $request->user_id . '.pdf';
                $file = $request->file('filled_and_signed_pdf');
                Storage::disk('public')->putFileAs('users_attached_documents/' . $request->user_id . '', $file, $filename);
                $user_attachment->filled_and_signed_pdf = $filename;
            }
            if ($request->hasFile('signed_acknowledgement_pdf')) {
                if ($user_attachment->signed_acknowledgement_pdf != NULL) {
                    Storage::disk('public')->delete('users_attached_documents/' . $request->user_id . '/' . $user_attachment->signed_acknowledgement_pdf);
                }
                $filename = 'signed_acknowledgement_pdf_' . $date . '_' . $request->user_id . '.pdf';
                $file = $request->file('signed_acknowledgement_pdf');
                Storage::disk('public')->putFileAs('users_attached_documents/' . $request->user_id . '', $file, $filename);
                $user_attachment->signed_acknowledgement_pdf = $filename;
            }
            if ($request->hasFile('cnic_front_image')) {
                if ($user_attachment->cnic_front_image != NULL) {
                    Storage::disk('public')->delete('users_attached_documents/' . $request->user_id . '/' . $user_attachment->cnic_front_image);
                }
                $filename = 'cnic_front_image_' . $date . '_' . $request->user_id . '.png';
                $file = $request->file('cnic_front_image');
                Storage::disk('public')->putFileAs('users_attached_documents/' . $request->user_id . '', $file, $filename);
                $user_attachment->cnic_front_image = $filename;
            }
            if ($request->hasFile('cnic_back_image')) {
                if ($user_attachment->cnic_back_image != NULL) {
                    Storage::disk('public')->delete('users_attached_documents/' . $request->user_id . '/' . $user_attachment->cnic_back_image);
                }
                $filename = 'cnic_back_image_' . $date . '_' . $request->user_id . '.png';
                $file = $request->file('cnic_back_image');
                Storage::disk('public')->putFileAs('users_attached_documents/' . $request->user_id . '', $file, $filename);
                $user_attachment->cnic_back_image = $filename;
            }
            if ($request->hasFile('blank_cheque_image')) {
                if ($user_attachment->blank_cheque_image != NULL) {
                    Storage::disk('public')->delete('users_attached_documents/' . $request->user_id . '/' . $user_attachment->blank_cheque_image);
                }
                $filename = 'blank_cheque_image_' . $date . '_' . $request->user_id . '.png';
                $file = $request->file('blank_cheque_image');
                Storage::disk('public')->putFileAs('users_attached_documents/' . $request->user_id . '', $file, $filename);
                $user_attachment->blank_cheque_image = $filename;
            }
            $user_attachment->uploaded_at = Carbon::now();
            $user_attachment->uploaded_by = Auth::id();
            $user_attachment->save();
        } else {

            $new_user_attachment = new UserDocumentAttachment();
            if ($request->hasFile('filled_and_signed_pdf')) {
                $filename = 'filled_and_signed_pdf_' . $date . '_' . $request->user_id . '.pdf';
                $file = $request->file('filled_and_signed_pdf');
                Storage::disk('public')->putFileAs('users_attached_documents/' . $request->user_id . '', $file, $filename);
                $new_user_attachment->filled_and_signed_pdf = $filename;
            }

            if ($request->hasFile('signed_acknowledgement_pdf')) {
                $filename = 'signed_acknowledgement_pdf_' . $date . '_' . $request->user_id . '.pdf';
                $file = $request->file('signed_acknowledgement_pdf');
                Storage::disk('public')->putFileAs('users_attached_documents/' . $request->user_id . '', $file, $filename);
                $new_user_attachment->signed_acknowledgement_pdf = $filename;
            }

            if ($request->hasFile('cnic_front_image')) {
                $filename = 'cnic_front_image_' . $date . '_' . $request->user_id . '.png';
                $file = $request->file('cnic_front_image');
                Storage::disk('public')->putFileAs('users_attached_documents/' . $request->user_id . '', $file, $filename);
                $new_user_attachment->cnic_front_image = $filename;
            }

            if ($request->hasFile('cnic_back_image')) {
                $filename = 'cnic_back_image_' . $date . '_' . $request->user_id . '.png';
                $file = $request->file('cnic_back_image');
                Storage::disk('public')->putFileAs('users_attached_documents/' . $request->user_id . '', $file, $filename);
                $new_user_attachment->cnic_back_image = $filename;
            }

            if ($request->hasFile('blank_cheque_image')) {
                $filename = 'blank_cheque_image_' . $date . '_' . $request->user_id . '.png';
                $file = $request->file('blank_cheque_image');
                Storage::disk('public')->putFileAs('users_attached_documents/' . $request->user_id . '', $file, $filename);
                $new_user_attachment->blank_cheque_image = $filename;
            }

            $new_user_attachment->user_id = $request->user_id;
            $new_user_attachment->uploaded_at = Carbon::now();
            $new_user_attachment->uploaded_by = Auth::id();
            $new_user_attachment->save();


        }
        $user = User::find($request->user_id);
        $user->documents_status = 0;
        $user->documents_status_reason = null;
        $user->save();

        return redirect()->back()->with(['success' => 'Files uploaded successfully']);
    }

    public function userDocumentsConfirm(Request $request)
    {
        $user = User::find($request->user_id);
        if ($user) {
            $user_attachment = UserDocumentAttachment::where('user_id', $request->user_id)->first();
            if ($user_attachment->filled_and_signed_pdf != null && $user_attachment->signed_acknowledgement_pdf != null && $user_attachment->cnic_front_image != null && $user_attachment->cnic_back_image != null && $user_attachment->blank_cheque_image != null) {
                $user->documents_status = 1;
                $user->save();
            }

            return response()->json(['status' => 1, 'success' => 'Files confirmed successfully']);
        }
        return response()->json(['error' => 'User not found!']);
    }

    public function edit_rates_user_documents(Request $request)
    {
        $date = Carbon::now();
        $user_attachment = UserDocumentAttachment::where('user_id', $request->user_id)->first();
        if ($user_attachment) {
            if ($request->hasFile('filled_and_signed_pdf')) {
                if ($user_attachment->filled_and_signed_pdf != NULL) {
                    Storage::disk('public')->delete('users_attached_documents/' . $request->user_id . '/' . $user_attachment->filled_and_signed_pdf);
                }
                $filename = 'filled_and_signed_pdf_' . $date . '_' . $request->user_id . '.pdf';
                $file = $request->file('filled_and_signed_pdf');
                Storage::disk('public')->putFileAs('users_attached_documents/' . $request->user_id . '', $file, $filename);
                $user_attachment->filled_and_signed_pdf = $filename;
            }
            if ($request->hasFile('signed_acknowledgement_pdf')) {
                if ($user_attachment->signed_acknowledgement_pdf != NULL) {
                    Storage::disk('public')->delete('users_attached_documents/' . $request->user_id . '/' . $user_attachment->signed_acknowledgement_pdf);
                }
                $filename = 'signed_acknowledgement_pdf_' . $date . '_' . $request->user_id . '.pdf';
                $file = $request->file('signed_acknowledgement_pdf');
                Storage::disk('public')->putFileAs('users_attached_documents/' . $request->user_id . '', $file, $filename);
                $user_attachment->signed_acknowledgement_pdf = $filename;
            }
            $user_attachment->save();
        } else {
            $new_user_attachment = new UserDocumentAttachment();
            if ($request->hasFile('filled_and_signed_pdf')) {
                $filename = 'filled_and_signed_pdf_' . $date . '_' . $request->user_id . '.pdf';
                $file = $request->file('filled_and_signed_pdf');
                Storage::disk('public')->putFileAs('users_attached_documents/' . $request->user_id . '', $file, $filename);
                $new_user_attachment->filled_and_signed_pdf = $filename;
            }

            if ($request->hasFile('signed_acknowledgement_pdf')) {
                $filename = 'signed_acknowledgement_pdf_' . $date . '_' . $request->user_id . '.pdf';
                $file = $request->file('signed_acknowledgement_pdf');
                Storage::disk('public')->putFileAs('users_attached_documents/' . $request->user_id . '', $file, $filename);
                $new_user_attachment->signed_acknowledgement_pdf = $filename;
            }
            $new_user_attachment->user_id = $request->user_id;
            $new_user_attachment->save();
        }
        $user = User::find($request->user_id);
        $user->documents_status = 0;
        $user->documents_status_reason = null;
        $user->save();
        return ['success' => 'User Document Uploaded!'];
    }

    public function add_contacts($id)
    {
        $shipper = User::find($id);
        $sale_person = SalePersonTag::where('user_id', $id)->where('status', 0)->first();
        $admin = Admin::find($sale_person->admin_id);
        $contacts = ShipperContact::where('shipper_id', $id);
        if ($contacts->exists()) {
            $contacts = $contacts->get();
        } else {
            $contacts = null;
        }
        return view('admin.accounts.multiple_poc')->with(['sale_person' => $admin, 'contacts' => $contacts, 'shipper' => $shipper]);
    }

    public function add_contacts_store(Request $request)
    {
        ShipperContact::where('shipper_id', $request->shipper_id)->delete();
        if ($request->has('poc')) {
            foreach ($request->poc as $index => $poc) {
                $shipper_contact = new ShipperContact();
                $shipper_contact->shipper_id = $request->shipper_id;
                $shipper_contact->poc = $poc;
                $shipper_contact->designation = $request->designation[$index];
                $shipper_contact->phone_number = $request->phone[$index];
                $shipper_contact->save();
            }
        }
        return redirect()->back()->with(['success' => 'Contacts updated successfully']);
    }

    public function payment_cycle_info(Request $request)
    {
        $user_id = $request->shipper_id;
        if ($user_id) {
            $user = User::find($user_id);
            if ($user) {
                $details = ['payment_cycle_id' => $user->payment_cycle_id, 'payment_day' => $user->payment_cycle_days];
                return response()->json(['status' => 0, 'details' => $details]);
                
            } else {
                return response()->json(['status' => 1, 'error' => 'User not found!']);
            }
        }
    }

    public function payment_cycle_submit(Request $request)
    {
        $shipper_ids = explode(',', $request->shipper_id);
        $error_messages = [];
        
        foreach ($shipper_ids as $shipper_id) {
            try {
                $payment_cycles = $request->payment_cycles;
                $selected_days = $request->selected_days;
                $fortnite = $request->fortnite;
                $monthly = $request->monthly;
        
                switch ($payment_cycles) {
                    case '2':
                    case '4':
                    case '5':
                        $payment_cycle_days = $selected_days;
                        break;
                    case '6':
                        $payment_cycle_days = $fortnite;
                        break;
                    case '3':
                        $payment_cycle_days = $monthly;
                        break;
                    default:
                        $payment_cycle_days = 0;
                }
        
                $shipper = User::find($shipper_id);
                if ($shipper) {
                    $shipper->update([
                        'payment_cycle_id' => $payment_cycles,
                        'payment_cycle_days' => $payment_cycle_days
                    ]);
                } else {
                    $error_messages[] = $shipper_id;
                }
            } catch (Exception $th) {
                $error_messages[] = $th->getMessage();
            }
        }
        
        if (!empty($error_messages)) {
            $error_message = implode(', ', $error_messages);
            return redirect()->back()->with(['error' => "Shipper with IDS : $error_message Not Found"]);
        } else {
            return redirect()->back()->with(['success' => 'Payment Cycle Updated Successfully']);
        }        
    }
    public function getInternationalCityForm()
    {
        $hubs = City::where('hub', 1)->where('business_category_id', 2)->where('status', 1)->get();
        $zones = Zone::where('business_category_id', 2)->get();
        return view('admin.management.add_international_city_form')->with(['hubs' => $hubs, 'zones' => $zones]);
    }
    public function getEditInternationalCityForm($id)
    {
        $city = City::find($id);
        if ($city->hub == 1) {
            $cityhub = '';
            $isHub = 1;
        } else {
            $cityhub = City::select(['id', 'name'])->where('id', $city->hub_id)->get();
            $isHub = 0;

        }
        $delivery_array = CityDelivery::where('city_id', $city->id)->select(['booking_type_id', 'shipping_mode_id'])->get();
        $delivery = array();
        foreach ($delivery_array as $delivery_details) {
            $delivery[$delivery_details['booking_type_id']][] = $delivery_details['shipping_mode_id'];
        }


        $hubs = City::where('hub', 1)->where('business_category_id', 2)->where('status', 1)->get();
        $zones = Zone::where('business_category_id', 2)->get();
        return view('admin.management.edit_international_city_form')->with(['hubs' => $hubs, 'zones' => $zones, 'isHub' => $isHub, 'city' => $city, 'delivery' => $delivery, 'cityhub' => $cityhub]);

    }

    public function updateInternationalCity(Request $request, $id)
    {
        $city_id = City::where('id', $id)->first();
        if ($request->postType == 'city') {
            City::where('id', $id)->update([
                'name' => $request->cityName,
                'city_code' => $request->city_code,
                'hub' => 0,
                'hub_id' => $request->hubs,
                'zone_id' => City::find($request->hubs)->zone_id,
                'pickup' => 0,
                'gc_area' => 0,
                'attempt_tat' => $request->attempt_tat,
                'location_latitude' => NULL,
                'location_longitude' => NULL,
                'address' => NULL
            ]);
            CityHistory::create([
                'city_id' => $id,
                'hub' => 0,
                'hub_id' => $request->hubs,
                'zone_id' => City::find($request->hubs)->zone_id,
                'pickup' => 0,
                'status' => $city_id->status,
                'gc_area' => 0,
                'attempt_tat' => $request->attempt_tat,
                'updated_by' => Auth::id(),
                'location_latitude' => NULL,
                'location_longitude' => NULL,
                'address' => NULL
            ]);

            return redirect()->back()->with('success', 'City updated successfully');
        } elseif ($request->postType == 'hub') {
            $city = City::where('id', $id)->first();
            if ($city->zone_id != $request->zone_id) {
                $city->hub_cities()->update([
                    'zone_id' => $request->zone_id,
                ]);
            }
            $city->update([
                'name' => $request->countryName,
                'city_code' => $request->city_code,
                'iata_code' => $request->iata_code,
                'hub' => 1,
                'hub_id' => $id,
                'zone_id' => $request->zone_id,
                'pickup' => 0,
                'gc_area' => 0,
                'attempt_tat' => $request->attempt_tat,
                'location_latitude' => NULL,
                'location_longitude' => NULL,
                'address' => NULL
            ]);
            CityHistory::create([
                'city_id' => $id,
                'hub' => 1,
                'hub_id' => $id,
                'zone_id' => $request->zone_id,
                'pickup' => 0,
                'status' => $city_id->status,
                'gc_area' => 0,
                'attempt_tat' => $request->attempt_tat,
                'updated_by' => Auth::id(),
                'location_latitude' => NULL,
                'location_longitude' => NULL,
                'address' => NULL
            ]);
            return redirect()->back()->with('success', 'Hub/city updated successfully');
        }
    }

    //update international city end
    public function addInternationalCityHub(Request $request)
    {
        if ($request->postType == 'city') {
            $zone_id = City::find($request->hubs)->zone_id;

            $city = City::create([
                'name' => $request->cityName,
                'city_code' => $request->city_code,
                'hub' => 0,
                'hub_id' => $request->hubs,
                'zone_id' => $zone_id,
                'pickup' => 0,
                'gc_area' => 0,
                'attempt_tat' => $request->attempt_tat,
                'status' => 1,
                'location_latitude' => NULL,
                'location_longitude' => NULL,
                'address' => NULL,
                'business_category_id' => 2
            ]);

            CityHistory::create([
                'city_id' => $city->id,
                'hub' => 0,
                'hub_id' => $request->hubs,
                'zone_id' => $zone_id,
                'pickup' => 0,
                'status' => 1,
                'gc_area' => 0,
                'attempt_tat' => $request->attempt_tat,
                'updated_by' => Auth::id(),
                'location_latitude' => NULL,
                'location_longitude' => NULL,
                'address' => NULL
            ]);

            return redirect()->back()->with('success', 'City added successfully');
        } elseif ($request->postType == 'hub') {
            $city = City::create([
                'name' => $request->countryName,
                'city_code' => $request->city_code,
                'iata_code' => $request->iata_code,
                'hub' => 1,
                'zone_id' => $request->zone_id,
                'pickup' => 0,
                'gc_area' => 0,
                'attempt_tat' => $request->attempt_tat,
                'status' => 1,
                'location_latitude' => NULL,
                'location_longitude' => NULL,
                'address' => NULL,
                'business_category_id' => 2
            ]);

            CityHistory::create([
                'city_id' => $city->id,
                'hub' => 1,
                'zone_id' => $request->zone_id,
                'pickup' => 0,
                'status' => 1,
                'gc_area' => 0,
                'attempt_tat' => $request->attempt_tat,
                'updated_by' => Auth::id(),
                'location_latitude' => NULL,
                'location_longitude' => NULL,
                'address' => NULL
            ]);
            City::where('id', $city->id)->update(['hub_id' => $city->id]);
            return redirect()->back()->with('success', 'Hub city added successfully');
        }
    }

    public function assign_locations_submit(Request $request)
    {
        $route_id = $request->route_id;
        $pickup_address_ids = explode(',', $request->pickup_address_id);
//        RouteLocations::where('route_id',$route_id)->delete();

        if ($route_id) {
            RouteLocations::whereIn('pickup_address_id', $pickup_address_ids)->delete();
            foreach ($pickup_address_ids as $pickup_address) {
                $location = new RouteLocations();
                $location->route_id = $route_id;
                $location->pickup_address_id = $pickup_address;
                $location->save();
            }
        }
        return redirect()->back()->with(['success' => "Location has been Assigned successfully!"]);
    }

    public function user_address(Request $request)
    {

        $user_id = $request->user_id;
        if ($user_id != null) {
            $addresses = UserShippingInfo::select('id', 'pickup_address')->where('user_id', $user_id)->where('user_shipping_infos.hidden', 0)->get();
            if ($addresses) {
                return response()->json(['status' => 1, 'addresses' => $addresses]);
            } else {
                return response()->json(['status' => 0, 'error' => 'Address Not Found']);
            }
        }
    }


    public function assign_locations_view(Request $request)
    {
        $route_id = $request->route_id;
        $data = array();
        if ($route_id) {
            $locations = RouteLocations::join('user_shipping_infos as usi', 'usi.id', '=', 'route_locations.pickup_address_id')
                ->join('users as u', 'u.id', '=', 'usi.user_id')->where('route_locations.route_id', $route_id)->select('u.name as shipper', 'usi.pickup_address as address')->get();
            foreach ($locations as $location) {
                $data[] =  '<strong>' . $location->shipper . '</strong>'  . ' - ' . $location->address;
            }
            return response()->json(['locations' => $data]);
        }
    }

    public function set_as_pickup_route(Request $request)
    {
        foreach ($request->route_id as $id) {
            if ($id) {
                $route = Route::find($id);
                if ($route) {
                    if ($route->route_type_id == 2) {
                        $route->route_type_id = 1;
                        $route->save();
                    }
                }
            }
        }
        return response()->json(['status' => 1, 'success' => 'Route type has been updated !']);
    }

    public function kam_poc_ref_tag(Request $request)
    {
        $poc = $request->poc;
        $kam = $request->kam;
        $ref = $request->ref;
        $eso = $request->eso;

        $send_to_emails = [$poc, $kam, $ref, $eso];
        
        $shipper_ids = $request->shipper_ids;
        if ($kam == null && $poc == null && $ref == null && $eso == null) {
            return response()->json(['status' => 0, 'error' => "One field is mandatory!"]);
        } else {
            if ($shipper_ids) {

                $notification_data = [];
                $send_to_emails = [$poc, $kam, $ref];
                $notification_data['email_to'] = Admin::whereIn('id',$send_to_emails)->pluck('email')->toArray();
                $notification_data['admin_name'] = Admin::find(Auth::id())->name;
                $notification_data['new_poc_person'] = Admin::find($poc) ?  Admin::find($poc)->name : '-';
                $notification_data['new_kam_person'] =  Admin::find($kam) ?  Admin::find($kam)->name : '-';
                $notification_data['new_ref_person'] =  Admin::find($ref) ?  Admin::find($ref)->name : '-';
                $notification_data['new_eso_person'] =  Admin::find($eso) ?  Admin::find($eso)->name : '-';

                $notification_data['old_poc_person'] = '-';
                $notification_data['old_kam_person'] = '-';
                $notification_data['old_ref_person'] = '-';
                $notification_data['old_eso_person'] = '-';
                $notification_data['old_person_date'] = '-';
                $notification_data['old_person_email'] = null;
                
                foreach ($shipper_ids as $shipper_id) {    
                    $shipper_name = User::find($shipper_id)->name;

                    
                    $notification_data['shipper_name'] = $shipper_name;
                    

                    $sale_tier = SaleTierTag::where('user_id', $shipper_id);
                    if ($sale_tier->exists()) {
                        $sale_tier = $sale_tier->first();

                        $send_cc_emails = [$sale_tier->poc, $sale_tier->kam, $sale_tier->ref];
                        $notification_data['old_person_email'] = Admin::whereIn('id',$send_cc_emails)->pluck('email')->toArray();

                        $notification_data['old_poc_person'] = Admin::find($sale_tier->poc) ? Admin::find($sale_tier->poc)->name : '-';
                        $notification_data['old_kam_person'] = Admin::find($sale_tier->kam) ? Admin::find($sale_tier->kam)->name : '-';
                        $notification_data['old_eso_person'] = Admin::find($sale_tier->eso) ? Admin::find($sale_tier->eso)->name : '-';
                        $notification_data['old_ref_person'] = Admin::find($sale_tier->ref) ? Admin::find($sale_tier->ref)->name : '-';

                        $notification_data['old_person_date'] = $sale_tier->created_at;

                        $sale_tier_history = new SaleTierTagHistory();
                        $sale_tier_history->sale_tier_tag_id = $sale_tier->id;
                        $sale_tier_history->user_id = $sale_tier->user_id;
                        $sale_tier_history->poc = $sale_tier->poc;
                        $sale_tier_history->kam = $sale_tier->kam;
                        $sale_tier_history->ref = $sale_tier->ref;
                        $sale_tier_history->eso = $sale_tier->eso;
                        $sale_tier_history->save();

                        $sale_tier->user_id = $shipper_id;
                        $sale_tier->poc = $poc;
                        $sale_tier->kam = $kam;
                        $sale_tier->ref = $ref;
                        $sale_tier->eso = $eso;
                        $sale_tier->save();
                        // return response()->json(['status'=>1,'success'=>"Updated!"]);
                    } else {
                        $sale_tier = new SaleTierTag();
                        $sale_tier->user_id = $shipper_id;
                        $sale_tier->poc = $poc;
                        $sale_tier->kam = $kam;
                        $sale_tier->eso = $eso;
                        $sale_tier->ref = $ref;
                        $sale_tier->save();
                    }
                    NotificationsController::send(218, $notification_data);
                }
                return response()->json(['status' => 1, 'success' => "Updated!"]);
            } else {
                return ['status' => 0, 'error' => "Select One Shipper!"];
            }
        }
    }

    public function kam_poc_ref_tag_info(Request $request)
    {
        $shipper_id = $request->shipper_id;
        if ($shipper_id) {
            $info = array();
            $sale_tier = SaleTierTag::where('user_id', $shipper_id);
            if ($sale_tier->exists()) {
                $flag = false;
                $sale_tier = $sale_tier->first();
                if ($sale_tier->poc != null) {
                    $info['poc'] = $sale_tier->poc_admin->name;
                    $flag = true;
                } else {
                    $info['poc'] = '-';
                }
                if ($sale_tier->kam != null) {
                    $info['kam'] = $sale_tier->kam_admin->name;
                    $flag = true;
                } else {
                    $info['kam'] = '-';
                }
                if ($sale_tier->ref != null) {
                    $info['ref'] = $sale_tier->ref_admin->name;
                    $flag = true;
                } else {
                    $info['ref'] = '-';
                }
                if ($flag) {
                    return response()->json(['status' => 1, 'info' => $info]);
                } else {
                    return response()->json(['status' => 0, 'error' => 'No Sales tier found!']);
                }
            } else {
                return response()->json(['status' => 0, 'error' => 'No Sales tier found!']);
            }
        } else {
            return ['status' => 0, 'error' => "Shipper not found with sales tier tagging!"];
        }
    }

    public function kam_poc_ref_tag_remove(Request $request)
    {
        $shipper_id = $request->shipper_id;
        if ($shipper_id) {
            $sale_tier = SaleTierTag::where('user_id', $shipper_id);
            if ($sale_tier->exists()) {
                $sale_tier = $sale_tier->first();
                if ($request->has('poc')) {
                    $sale_tier->poc = null;
                }
                if ($request->has('kam')) {
                    $sale_tier->kam = null;
                }
                if ($request->has('ref')) {
                    $sale_tier->ref = null;
                }
                $sale_tier->save();
                return redirect()->back()->with('success', 'Sales tier tag removed successfully!');
            } else {
                return redirect()->back()->with('error', 'No Sales tier found!');
            }
        } else {
            return redirect()->back()->with('error', 'Shipper not found with sales tier tagging!');
        }
    }

    public function rate_history_date(Request $request)
    {

        $user_id = $request->user_id;
        $details = array();
        if ($user_id) {
            $user = User::find($user_id);
            if ($user->account_type_id == 1) {
                $old_reimbursement_account = HistoryRateStatus::where('user_id', $user_id);


                if ($old_reimbursement_account->exists()) {
                    $old_reimbursement_account_dates = $old_reimbursement_account->select('created_at')->groupBy('created_at')->get();
                    $count = $old_reimbursement_account_dates->count();
                    $lastIndex = count($old_reimbursement_account_dates) - 1;

                    foreach ($old_reimbursement_account_dates as $key => $date) {
                        $date = Carbon::parse($date->created_at)->toDateString();
                        $firstValue = false;
                        if (isset($old_reimbursement_account_dates[$key], $old_reimbursement_account_dates[$key - 1])) {
                            if ($key == 0){
                                $date1 = $old_reimbursement_account_dates[$key];
                                $date2 = null;
                                $firstValue = true;
                            } elseif ($key == $lastIndex) {
                                $date1 = $old_reimbursement_account_dates[$key];
                                $date2 = null;
                            } else {
                                $date1 = $old_reimbursement_account_dates[$key];
                                $date2 = $old_reimbursement_account_dates[$key - 1];
                            }
                        } else {
                            $date1 = $date;
                            $date2 = null;
                            $firstValue = true;
                        }

                        //Weight Check
                        $compare_weight = $this->compareWeightCharges($user_id, WeightCharge::class, HistoryWeightCharge::class, $date1 ?? null, $date2 ?? null, $firstValue, $count);

                        //Fuel Check
                        $compare_fuel_surcharge = $this->compareFuelCharges($user_id, FuelSurcharge::class, HistoryFuelSurcharge::class, $date1 ?? null, $date2 ?? null, $firstValue, $count);

                        if (!array_key_exists($date, $details)) {
                            $details[$date]['weight'] = $compare_weight;
                            $details[$date]['fuel'] = $compare_fuel_surcharge;
                        }
                    }

                    return response()->json(['status' => 1, 'account_type' => 1, 'details' => $details, 'user_id' => $user_id, 'compare_weight' => $compare_weight ?? '', 'compare_fuel_surcharge' => $compare_fuel_surcharge ?? '']);
                } else {
                    return response()->json(['status' => 0, 'error' => 'No Data Found']);
                }
            } else {
                $old_corporate_account = HistoryCorporateRateStatus::where('user_id', $user_id);

                if ($old_corporate_account->exists()) {
                    $old_corporate_account_dates = $old_corporate_account->select('created_at')->groupBy('created_at')->get();
                    $count = $old_corporate_account_dates->count();
                    $lastIndex = count($old_corporate_account_dates) - 1;

                    foreach ($old_corporate_account_dates as $key => $date) {
                        $date = Carbon::parse($date['created_at'])->toDateString();
                        $firstValue = false;
                        if (isset($old_corporate_account_dates[$key], $old_corporate_account_dates[$key - 1])) {
                            if ($key == 0){
                                $date1 = $old_corporate_account_dates[$key];
                                $date2 = null;
                                $firstValue = true;
                            } elseif ($key == $lastIndex) {
                                $date1 = $old_corporate_account_dates[$key];
                                $date2 = null;
                            } else {
                                $date1 = $old_corporate_account_dates[$key];
                                $date2 = $old_corporate_account_dates[$key - 1];
                            }
                        } else {
                            $date1 = $date;
                            $date2 = null;
                            $firstValue = true;
                        }

                        $user = User::find($user_id);
                        $corporateRateType = $user->corporate_rate_type_id;

                        list($table1, $table2) = $this->getWeightTables($corporateRateType);

                        $compare_weight = $this->compareWeightCharges(
                            $user_id, $table1, $table2, $date1, $date2, $firstValue, $count
                        );

                        list($fTable1, $fTable2) = $this->getFuelTables($corporateRateType);

                        $compare_fuel_surcharge =  $this->compareFuelCharges($user_id, $fTable1, $fTable2, $date1, $date2, $firstValue, $count);

                        if (!array_key_exists($date, $details)) {
                            $details[$date]['weight'] = $compare_weight;
                            $details[$date]['fuel'] = $compare_fuel_surcharge;
                        }
                    }
                    return response()->json(['status' => 1, 'success', 'account_type' => 2, 'details' => $details, 'user_id' => $user_id, 'compare_weight' => $compare_weight ?? '', 'compare_fuel_surcharge' => $compare_fuel_surcharge ?? '']);
                } else {
                    return response()->json(['status' => 0, 'error' => 'No Data Found']);
                }
            }
        } else {
            return response()->json(['status' => 0, 'error' => 'No Data Found']);
        }

    }

//     public function modesAjax(Request $request) {
//        $city_id = $request->id;
//        $shipping_mode_ids = CityDelivery::select('shipping_mode_id')->where('city_id', $city_id);
//
//        if($shipping_mode_ids->exists()){
//            $shipping_modes = $shipping_mode_ids->get();
//            $data = array();
//            foreach($shipping_modes as $id){
//                $shipping_mode = ShippingMode::find($id)->mode;
//                $name = $shipping_mode;
//                $data[] = $name;
//            }
//            return response()->json(['status' => 1, 'shipping_mode' => $data]);
//        }
//        else {
//            return response()->json(['status' => 0, 'No Shipping mode found!']);
//        }
//     }
    public function add_territory(Request $request)
    {
        $territory = $request->territory;
        $user_ids = $request->user_ids;
        if ($user_ids) {
            $user_ids = explode(',', $user_ids);
            foreach ($user_ids as $id) {
                $user = User::find($id);
                if ($user->territory_id)
                    return redirect()->back()->with('error', 'Territory not added as ' . $user->name . ' is tagged previously');
            }
            foreach ($user_ids as $id) {
                $user = User::find($id);
                $user->territory_id = $territory;
                $user->save();

                $history = new TerritoryTagHistory();
                $history->user_id = $id;
                $history->admin_id = Auth::id();
                $history->save();
            }
            return redirect()->back()->with('success', 'Territory is added.');
        } else {
            return redirect()->back()->with('error', 'Territory not added.');
        }
    }

    public function add_segments(Request $request)
    {
        $segment_id = $request->bulk_segment;
        $sub_segment_id = $request->bulk_sub_segment;
        $user_ids = $request->user_ids;
        if ($user_ids) {
            $user_ids = explode(',', $user_ids);
            foreach ($user_ids as $id) {
                $user = User::find($id);
                $user->sub_segment_id = $sub_segment_id;
                $user->segment_id = $segment_id;
                $user->save();
            }
            return redirect()->back()->with('success', 'Segments is added.');
        } else {
            return redirect()->back()->with('error', 'Segments not added.');
        }
    }

    public function disable_account_intimation_survey_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 557);
        $disabled_shippers = User::where('status', '=', 4)->where('blacklist', '=', 0)->get();

        return view('admin.accounts.disable_account_intimation_survey')->with(['disabled_shippers' => $disabled_shippers]);

    }

    public function disable_account_intimation_survey_list(Request $request)
    {


        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 558);
        }

        $questions = DisableAccountIntimationQuestion::leftjoin('admins as created_user','created_user.id','disable_account_intimation_questions.created_by')
        ->leftjoin('admins as updated_user','updated_user.id','disable_account_intimation_questions.updated_by')
        ->select(['disable_account_intimation_questions.*', 'created_user.name as created_by_name' , 'updated_user.name as updated_by_name' ]);

        return Datatables::of($questions)
            ->editColumn('status', function ($notification) {
                if ($notification->status == 0) {
                    return 'Disabled';
                } else {
                    return "Enabled";
                }
            })
            ->addColumn('action', function($notification) {
                $edit_button = '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';
                $enable_button = '<button type="button" class="dropdown-item enable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
                $disable_button = '<button type="button" class="dropdown-item disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';

                $dropdown = '
                    <div class="btn-group">
                      <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                      <div class="dropdown-menu dropdown-menu-sm">
                ';

                if (session('role_id') == 1 || in_array(764, session('permissions'))) {
                    if ($notification->status) {
                        $dropdown .= $edit_button;
                    }
                }

                if (session('role_id') == 1 || in_array(765, session('permissions'))) {
                    if ($notification->status) {
                        $dropdown .= $disable_button;
                    }
                    else {
                        $dropdown .= $enable_button;
                    }
                }

                $dropdown .= '
                      </div>
                    </div>
                ';

                return $dropdown;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function details(Request $request) {

        $notification = DisableAccountIntimationQuestion::find($request->id);

        return $notification;

    }

    public function edit(Request $request) {
        $notification = DisableAccountIntimationQuestion::find($request->get('id'));

        if ($notification) {

            if($notification->status == 1)
            {
                $notification->questions = $request->get('question');
                $notification->option1 = $request->get('option1');
                $notification->option2 = $request->get('option2');
                $notification->option3 = $request->get('option3');
                $notification->option4 = $request->get('option4');
                $notification->updated_by = Auth::id();

                $notification->save();

                return ['status' => 0, 'success' => 'Question has been edited'];

            }
            else{
                return ['status' => 1, 'error' => 'Some one disabled this question please refresh your page'];
            }


        }
        else {
            return ['status' => 1, 'error' => 'No Question with given ID is present'];
        }
    }

    public function add(Request $request) {

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

        $notification = new DisableAccountIntimationQuestion();

        $notification->questions = $request->get('question');
        $notification->option1 = $request->get('option1');
        $notification->option2 = $request->get('option2');
        $notification->option3 = $request->get('option3');
        $notification->option4 = $request->get('option4');
        $notification->created_by = Auth::id();
        $notification->created_at = $timestamp;
        $notification->updated_at = $timestamp;
        $notification->save();

        return ['status' => 0, 'success' => 'Question has been Added'];
    }


    public function status(Request $request) {

        $notification = DisableAccountIntimationQuestion::find($request->id);

        if ($notification) {

            $notification->status = $request->status;
            $notification->updated_by = Auth::id();
            $notification->save();

            if ($request->status) {
                return ['status' => 0, 'success' => 'Question has been enabled'];
            }
            else {
                return ['status' => 0, 'success' => 'Question has been disabled'];
            }
        }
        else {
            return ['status' => 1, 'error' => 'No Notication with given ID is present'];
        }
    }

    public function send_survey(Request $request)
    {
        if($request)
        {
            $disabled_shippers = "";

            if($request->all_shippers_checkbox == "on")
            {
                $disabled_shippers = User::where('status', '=', 4)->where('blacklist', '=', 0)->select(['id','email','name','phone'])->get();
            }
            else if($request->all_shippers_checkbox == "off"){

                $disabled_shippers = User::where('status', '=', 4)->where('blacklist', '=', 0)->whereIn("id",$request->shipper_ids)->select(['id','email','name','phone'])->get();
            }

            if($request->send_via == "email")
            {
                // id 179 is used for email notification Disable Account Intimation Survey
                NotificationsController::send(179, $disabled_shippers);
                return ['status' => 0, 'success' => 'Email Notification Send Sucessfully'];

            }
            else if($request->send_via == "sms")
            {
                // id 180 is used for sms notification Disable Account Intimation Survey
                NotificationsController::send(180, $disabled_shippers);

                return ['status' => 0, 'success' => 'SMS Notification Send Sucessfully'];
            }
            else if($request->send_via == "both")
            {
                // id 179 is used for email notification Disable Account Intimation Survey
                NotificationsController::send(179, $disabled_shippers);

                // id 180 is used for sms notification Disable Account Intimation Survey
                NotificationsController::send(180, $disabled_shippers);

                return ['status' => 0, 'success' => 'Email and SMS Notification Send Sucessfully'];
            }
            else{

                return ['status' => 1, 'error' => 'No Notication with given ID is present'];
            }
        }
    }

    public function survey_report(Request $request)
    {

        ActivityTrailController::createActivityTrailLog(Auth::id(), 559);
        $disabled_shippers = User::where('status', '=', 4)->where('blacklist', '=', 0)->get();

        return view('admin.accounts.disable_account_intimation_survey_report')->with(['disabled_shippers' => $disabled_shippers]);
    }

    public function survey_report_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 560);
        }

        $surveyReport = DisableAccountIntimationSendSurvey::join('users','disable_account_intimation_send_surveys.shipper_id','users.id')
        ->join('admins as send_by','send_by.id','disable_account_intimation_send_surveys.send_by')
        ->select(['users.name as shipper_name','users.email','users.phone','disable_account_intimation_send_surveys.random_id','disable_account_intimation_send_surveys.send_via','send_by.name as send_by','disable_account_intimation_send_surveys.status','disable_account_intimation_send_surveys.url','disable_account_intimation_send_surveys.created_at']);


        return Datatables::of($surveyReport)
            ->editColumn('status', function ($surveyReport) {
                if ($surveyReport->status == 0) {
                    return 'Not Collected';
                } else {
                    return "Collected";
                }
            })
            ->editColumn('send_via', function ($surveyReport) {
                if ($surveyReport->send_via == 'sms') {
                    return 'SMS';
                } else {
                    return "Email";
                }
            })
            ->editColumn('url', function ($surveyReport) {

                return $url = "<a href='$surveyReport->url' target='_blank'> $surveyReport->url</a>";
            })
            ->editColumn('answers', function ($surveyReport) {
                if ($surveyReport->status == 0) {
                    return ' - ';
                } else {
                    return $url = "<button class='btn btn-sm btn-outline-info align-middle show_answers'> Show Answers </button>";
                }

            })
            ->addColumn('url_excel', function ($surveyReport) {

                return $url =  $surveyReport->url;
            })
            ->rawColumns(['url', 'answers', 'url_excel'])
            ->make(true);
    }


    public function submitresponse_report(Request $request)
    {
        $survey_id = $request->survey_id;

        $submit_survey_answers = DisableAccountIntimationSubmitSurvey::join('disable_account_intimation_questions as questions','disable_account_intimation_submit_surveys.question_id','questions.id')
        ->select(['questions.id','questions.questions','questions.option1','questions.option2','questions.option3','questions.option4','disable_account_intimation_submit_surveys.selected_option'])
        ->where('disable_account_intimation_submit_surveys.survey_id',$survey_id);


        if($submit_survey_answers->exists())
        {
            $submit_survey_data = $submit_survey_answers->get();
            return response()->json(['status' => 1, 'submit_survey_data' => $submit_survey_data]);

        }
        else{
            return response()->json(['status' => 0, 'submit_survey_data' => []]);
        }

    }


    public function todayActiveAccountsList()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 279);
        return view('admin.accounts.today_active_accounts_list');

    }

    public function todayActiveAccountListAjax(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 280);
        }
        $users = User::join('cities', 'users.city_id', '=', 'cities.id')
            ->leftjoin('account_types as at', 'at.id', '=', 'users.account_type_id')
            ->leftjoin('sale_person_tags as spt', function ($join) {
                $join->on('spt.user_id', '=', 'users.id')
                    ->leftjoin('admins as ad', 'ad.id', '=', 'spt.admin_id')
                    ->where('spt.status', '=', 0);
            })
            ->leftjoin('sale_tier_tags as st', 'st.user_id', '=', 'users.id')
            ->leftjoin('admins as poc', 'poc.id', '=', 'st.poc')
            ->select(['users.phone as phone', 'users.email as email', 'users.id', 'ad.name as admin_tag_id', 'users.name', 'users.poc', 'users.account_type_id', 'at.name as account_type'])->whereIn('users.status', [3, 4])->where('blacklist', 0)
            ->where('users.activated_at', '>', Carbon::parse('-24 hours'));
        if (session('role_id') != 1) {
            $users = $users->whereIn('cities.hub_id', session('hubs'));
        }
        if (session('department_id') == 7) {
            if (!in_array(session('id'), session('sale_users_bypass'))) {
                $users = $users->whereIn('users.id', session('tagged_shippers'));
            }
        }
        return Datatables::of($users)
            ->addColumn('id_padded', function ($user) {
                return str_pad($user->id, 6, '0', STR_PAD_LEFT);
            })
            ->filterColumn('users.id', function ($query, $keyword) {
                return $query->where('users.id', '=', $keyword);
            })
            ->make(true);
    }


    static public function compare_data($array_1, $array_2)
    {
        if (count($array_2) == count($array_1)) {
            $diff = array_diff($array_1, $array_2);
            if (count($diff) > 0) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 1;
        }
    }

    public function restrict_order_id_info(Request $request)
    {
        $user = User::find($request->user_id);
        return response()->json(['status' => $user->restrict_order_id]);
    }

    public function restrict_order_id_submit(Request $request)
    {
        $user = User::find($request->user_id);
        if ($request->has('restrict_order_id_checkbox')) {
            $user->restrict_order_id = 1;
            $user->save();
            return redirect()->back()->with('success', 'Order ID restricted successfully!');
        } else {
            $user->restrict_order_id = 0;
            $user->save();
            return redirect()->back()->with('success', 'Order ID restriction removed successfully!');
        }
    }

    public function admin_profile(Request $request)
    {
        $user = Employee::leftjoin('employee_designations as d', 'd.id', '=', 'employees.designation_id')
            ->leftjoin('admin_departments as ad', 'd.department_id', '=', 'ad.id')
            ->leftjoin('employee_blood_groups as bg', 'bg.id', '=', 'employees.blood_group')
            ->select('employees.trax_id as trax_id', 'employees.name as name', 'employees.official_email as email', 'employees.phone_number as phone', 'd.name as designation', 'ad.name as department_name', 'bg.name as blood_group', 'employees.emergency_contact as emergency_contact_no', 'employees.emergency_contact_person as emergency_contact_person', 'bg.id as blood_group_id', 'employees.personal_email as personal_email', 'employees.official_phone_number as official_phone_number')
            ->where('employees.trax_id', Auth::user()->trax_id);
        if ($user->exists()) {
            $user = $user->first();
            $email = $user->email;
            if ($user->personal_email) {
                $email .= " / " . $user->personal_email;
            }
            $phone = $user->phone;
            if ($user->official_phone_number) {
                $phone .= " / " . $user->official_phone_number;
            }
            return response()->json(['full_name' => $user->name, 'department' => $user->department_name, 'designation' => $user->designation, 'employee_id' => $user->trax_id, 'email' => $email, 'contact' => $phone, 'blood_group' => $user->blood_group, 'emergency_contact_no' => $user->emergency_contact_no, 'emergency_contact_person' => $user->emergency_contact_person]);
        } else {
            return response()->json(['error' => 'User not found!']);
        }

    }

    public function edit_profile(Request $request)
    {
        $employee = Employee::where('trax_id', Auth::user()->trax_id)->where('trax_id', '!=', null);
        if ($employee->exists()) {
            $employee = $employee->first();
            $blood_groups = EmployeeBloodGroup::all();
            return view('admin.profile.edit_profile_form')->with(['blood_groups' => $blood_groups, 'employee' => $employee]);
        } else {
            return response()->json(['status' => 1, 'error' => 'User not found!']);
        }

    }

    public function edit_profile_submit(Request $request)
    {
        $employee = Employee::where('trax_id', Auth::user()->trax_id)->where('trax_id', '!=', null);
        if ($employee->exists()) {
            $employee = $employee->first();
            $employee->blood_group = $request->blood_group;
            $employee->emergency_contact_person = $request->emergency_contact_name;
            $employee->emergency_contact = $request->emergency_contact_no;
            $employee->save();
            return redirect()->back()->with('success', 'Profile Successfully Updated');
        } else {
            return redirect()->back()->with('error', 'User not found!');
        }

    }

    public function add_retag_territory(Request $request)
    {
        $territory = $request->territory;
        $user_ids = $request->user_ids;
        if ($user_ids) {
            $user_ids = explode(',', $user_ids);
            foreach ($user_ids as $id) {
                $user = User::find($id);
                if (!$user->territory_id)
                    return redirect()->back()->with('error', 'Retagging not done as ' . $user->name . ' is not tagged previously');
            }
            foreach ($user_ids as $id) {
                $user = User::find($id);
                $user->territory_id = $territory;
                $user->save();

                $history = new TerritoryTagHistory();
                $history->user_id = $id;
                $history->admin_id = Auth::id();
                $history->save();
            }
            return redirect()->back()->with('success', 'Territory is added.');
        } else {
            return redirect()->back()->with('error', 'Territory not added.');
        }
    }

    public function osa_list(Request $request)
    {
        $city = City::find($request->city_id);

        $osa_list = CityOsaRate::where('city_id', $city->id)->get();
        return response()->json(['status' => 1, 'osa_list' => $osa_list]);

    }

    public function packaging_invoice_log(Request $request)
    {
        $logs = CorporateUserPackagingInvoiceLog::join('admins as a', 'a.id', '=', 'corporate_user_packaging_invoice_logs.admin_id')
            ->select('a.name as admin', 'corporate_user_packaging_invoice_logs.created_at as time', 'corporate_user_packaging_invoice_logs.status as status')
            ->where('corporate_user_packaging_invoice_logs.user_id', $request->user_id);
        if ($logs->exists()) {
            $logs = $logs->get();
            return response()->json(['status' => 0, 'details' => $logs]);
        } else {
            return response()->json(['status' => 1, 'error' => 'No Log found!']);
        }

    }

    public function auto_cancelation_days(Request $request)
    {
        $user = User::find($request->user_id);
        if (!$user) {
            return back()->with(['error' => 'Invalid Shipper']);
        }

        $user->auto_shipment_cancellation_days = $request->cancelation_days;
        $user->update();

        return back()->with(['success' => 'Auto Cancelation Days Updated Successfully']);

    }

    public function check_profile(Request $request)
    {
        $admin_id = Auth::id();
        $admin = Admin::find($admin_id);
        if ($admin && $admin->trax_id) {
            $admin_profile = Employee::where('trax_id', $admin->trax_id);
            if ($admin_profile->exists()) {
                $admin_profile = $admin_profile->first();
                if (!$admin_profile->blood_group || !$admin_profile->emergency_contact || !$admin_profile->emergency_contact_person || !$admin_profile->guardian_name || !$admin_profile->mother_name || !$admin_profile->address || !$admin_profile->employee_gender_id || !$admin_profile->religion_id || !$admin_profile->marital_status_id || !$admin_profile->date_of_birth || !$admin_profile->staff_category_id || !$admin_profile->shift_id || !$admin_profile->domicile_id) {
                    return response()->json(['status' => 0, 'message' => "Please Update Your Profile"]);
                }
                return response()->json(['status' => 2, 'info' => "Profile Already Updated"]);
            } else {

                return response()->json(['status' => 1, 'error' => "Admin Profile Not Found"]);
            }
        } else {
            return response()->json(['status' => 1, 'error' => "Admin Profile Not Found"]);
        }

    }

    public function get_one_time_profile(Request $request)
    {
        $admin_id = Auth::id();
        $admin = Admin::find($admin_id);
        if ($admin && $admin->trax_id) {
            $blood_group_list = EmployeeBloodGroup::all();
            $gender_list = EmployeeGender::all();
            $religion_list = EmployeeReligion::all();
            $marital_status_list = EmployeeMaritalStatus::all();
            $staff_category_list = StaffCategory::all();
            $shift_list = EmployeeShift::all();
            $domecile_list = EmployeeDomicile::all();
            $admin_profile = Employee::where('trax_id', $admin->trax_id);
            if ($admin_profile->exists()) {
                $admin_profile = $admin_profile->first();
                return view('admin.profile.edit_one_time_profile')->with(["blood_groups" => $blood_group_list, 'genders' => $gender_list, 'religions' => $religion_list, 'maritial_statuses' => $marital_status_list, 'domiciles' => $domecile_list, 'staff_categories' => $staff_category_list, 'shifts' => $shift_list, 'employee' => $admin_profile]);
            } else {
                return redirect()->back()->with('error', 'Admin Profile Not Found');
            }
        } else {
            return redirect()->back()->with('error', 'Admin Profile Not Found');
        }
    }

    public function update_one_time_profile(Request $request)
    {
        $employee_request = Employee::find($request->employee_id);
        if ($employee_request) {
            $admin = Admin::where('trax_id', $employee_request->trax_id);
            if ($admin->exists()) {
                $admin = $admin->first();
                $city = City::find($employee_request->city_id);
                $employee_request->zone_id = $city->zone_id;
                if ($request->has('gender')) {
                    $employee_request->employee_gender_id = $request->gender;
                }
                if ($request->has('guardian_name')) {
                    $employee_request->guardian_name = $request->guardian_name;
                }

                if ($request->has('religion')) {
                    $employee_request->religion_id = $request->religion;
                }

                if ($request->has('domicile')) {
                    $employee_request->domicile_id = $request->domicile;
                }

                if ($request->has('marital_status')) {
                    $employee_request->marital_status_id = $request->marital_status;
                }

                if ($request->has('blood_group')) {
                    $employee_request->blood_group = $request->blood_group;
                }

                if ($request->has('address')) {
                    $employee_request->address = $request->address;
                }

                if ($request->has('emergency_contact')) {
                    $employee_request->emergency_contact = $request->emergency_contact;
                }

                if ($request->has('emergency_contact_person')) {
                    $employee_request->emergency_contact_person = $request->emergency_contact_person;
                }

                if ($request->has('official_email')) {
                    $employee_request->official_email = $request->official_email;
                    $admin->email = $request->official_email;
                }

                if ($request->has('date_of_birth_formatted')) {
                    $employee_request->date_of_birth = $request->date_of_birth_formatted;
                }

                if ($request->has('mother_name')) {
                    $employee_request->mother_name = $request->mother_name;
                }

                if ($request->has('shift_id')) {
                    $employee_request->shift_id = $request->shift_id;
                    $admin->shift_id = $request->shift_id;
                }

                if ($request->has('staff_category')) {
                    $employee_request->staff_category_id = $request->staff_category;
                }

                $employee_request->save();
                $admin->save();
                return redirect()->route('admin.dashboard.index')->with('success', 'Profile Updated Successfully');
            } else {
                return redirect()->route('admin.dashboard.index')->with('error', 'User not found!');
            }
        } else {
            return redirect()->route('admin.dashboard.index')->with('error', 'User not found!');
        }
    }

    public function substitute_accounts_view($id)
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 662);
        return view('admin.accounts.substitute_account_management.index')->with(['shipper_id' => $id]);
    }

    public function substitute_accounts_list(Request $request,$id) {

        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 663);
        }
        $substitute_users = SubstituteUser::join('admins','admins.id','substitute_users.created_by_admin_id')
        ->select('substitute_users.id', 'substitute_users.name', 'substitute_users.phone_number', 'substitute_users.email', 'substitute_users.cnic', 'substitute_users.created_at', 'substitute_users.updated_at', 'substitute_users.status', 'substitute_users.restriction', 'admins.name as created_by')
        ->where('substitute_users.user_id', $id)
        ->where('substitute_users.is_created_by_admin',1);

        $datatables = Datatables::of($substitute_users)
        ->editColumn('status', function ($substitute_user) {
            return (($substitute_user->status) ? 'Enabled' : 'Disabled');
        })
        ->editColumn('restriction', function ($substitute_user) {
            return (($substitute_user->restriction) ? 'Enabled' : 'Disabled');
        })
        ->addColumn('action', function($substitute_user) {
            $edit_button = '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';
            $enable_button = '<button type="button" class="dropdown-item enable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
            $disable_button = '<button type="button" class="dropdown-item disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';

            $dropdown = '
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

            if (session('role_id') == 1 || in_array(875, session('permissions'))) {

                $dropdown .= $edit_button;
            }

            if ($substitute_user->status) {
                $dropdown .= $disable_button;
            }
            else {
                $dropdown .= $enable_button;
            }

            $dropdown .= '
                </div>
            </div>
            ';

            return $dropdown;
        })
        ->filterColumn('status', function($query, $keyword) {
            $keyword = strtolower($keyword);

            if ($keyword != '') {
                $query->where('substitute_users.status', '=', $keyword);
            }
            else {
                $query->whereRaw('FALSE');
            }
        });

        return $datatables->make(true);
    }
    public function substitute_accounts_email(Request $request,$id = null) {
        if ($request->filled('email')) {
            $email = SubstituteUser::where('email', $request->input('email'));

            if ($id) {
                $email = $email->where('id', '!=', $id);
            }

            if (!$email->exists()) {
                return 'true';
            }
            else {
                return 'false';
            }
        }
        else {
          return 'false';
        }
    }

    public function substitute_accounts_add_index($shipper_id) {

        ActivityTrailController::createActivityTrailLog(Auth::id(), 664);
        $permissions = SubstituteUserModulePermission::whereIn('id', [10])->get();

        $merged_head_account_ids = MergedSisterAccount::where('user_id',$shipper_id)->pluck('merged_head_id')->toArray();
        $sister_accounts = MergedSisterAccount::join('users','users.id','merged_sister_accounts.user_id')
        ->select('merged_sister_accounts.user_id','users.name','merged_sister_accounts.merged_head_id')
        ->whereIn('merged_sister_accounts.merged_head_id',$merged_head_account_ids)
        ->where('merged_sister_accounts.user_id', '!=' , $shipper_id)->get();

        return view('admin.accounts.substitute_account_management.add.index')->with(['shipper_id' => $shipper_id,'permissions' => $permissions , 'sister_accounts' => $sister_accounts]);
    }

    public function substitute_accounts_add_store(Request $request,$id) {

        $substitute_user = new SubstituteUser();

        $substitute_user->user_id = $id;
        $substitute_user->name = $request->input('name');
        $substitute_user->email = $request->input('email');
        $substitute_user->phone_number = $request->input('phone_number');
        $substitute_user->cnic = $request->input('cnic');
        $substitute_user->password = bcrypt($request->input('password'));
        $substitute_user->restriction = $request->input('restriction');
        $substitute_user->is_created_by_admin = 1;
        $substitute_user->created_by_admin_id = Auth::id();
        $substitute_user->save();

        if ($request->has('account_ids')) {

            foreach($request->input('account_ids') as $merge_head_id => $account_ids) {
                foreach($account_ids as  $account_id) {
                    $Substitute_user_merge_sister_account_mapping = new SubstituteUserMergeSisterAccountMapping();

                    $Substitute_user_merge_sister_account_mapping->substitute_user_id = $substitute_user->id;
                    $Substitute_user_merge_sister_account_mapping->merged_head_id = $merge_head_id;
                    $Substitute_user_merge_sister_account_mapping->head_user_id = $id;
                    $Substitute_user_merge_sister_account_mapping->sister_user_id = $account_id;

                    $Substitute_user_merge_sister_account_mapping->save();
                }
            }
        }

        $new_permission_ids = [10,16,17,18,19];

        SubstituteUserPermission::where('substitute_user_id', $id)->delete();

        foreach($new_permission_ids as $permission_id) {
            $substitute_user_permission = new SubstituteUserPermission();

            $substitute_user_permission->substitute_user_id = $substitute_user->id;
            $substitute_user_permission->permission_id = $permission_id;

            $substitute_user_permission->save();
        }

        // if ($request->has('permission_ids')) {
        //     foreach($request->input('permission_ids') as $permission_id) {
        //     $substitute_user_permission = new SubstituteUserPermission();

        //     $substitute_user_permission->substitute_user_id = $substitute_user->id;
        //     $substitute_user_permission->permission_id = $permission_id;

        //     $substitute_user_permission->save();
        //     }
        // }

        return redirect()->route('admin.accounts.substitute_account_management.index',$id)->with(['success' => 'Substitute User: ' . $request->input('name') . ' has been added!','shipper_id' => $id]);
    }

    public function substitute_accounts_status(Request $request) {
        $substitute_user = SubstituteUser::find($request->id);

        if ($substitute_user) {
            $substitute_user->status = $request->status;

            $substitute_user->save();

            if ($request->status) {
            return ['status' => 0, 'success' => 'Substitute User has been enabled'];
            }
            else {
            return ['status' => 0, 'success' => 'Substitute User has been disabled'];
            }
        }
        else {
            return ['status' => 1, 'error' => 'No Substitute User with given ID is present'];
        }
    }

    public function substitute_accounts_update_index($shipper_id , $id) {

        $permissions = SubstituteUserModulePermission::whereIn('id', [10])->get();
        $substitute_user = SubstituteUser::find($id);

        $merged_head_account_ids = MergedSisterAccount::where('user_id',$shipper_id)->pluck('merged_head_id')->toArray();
        $sister_accounts = MergedSisterAccount::join('users','users.id','merged_sister_accounts.user_id')
        ->select('merged_sister_accounts.user_id','users.name','merged_sister_accounts.merged_head_id')
        ->whereIn('merged_sister_accounts.merged_head_id',$merged_head_account_ids)
        ->where('merged_sister_accounts.user_id', '!=' , $shipper_id)->get();

        $merged_accounts = SubstituteUserMergeSisterAccountMapping::where('substitute_user_id',$id)->pluck('sister_user_id')->toArray();

        $substitute_user_permissions = $substitute_user->permissions->pluck('permission_id')->toArray();

        return view('admin.accounts.substitute_account_management.update.index')->with(['permissions' => $permissions, 'substitute_user' => $substitute_user, 'substitute_user_permissions' => $substitute_user_permissions, 'shipper_id' => $shipper_id , "id" => $id, 'sister_accounts' => $sister_accounts , 'merged_accounts' => $merged_accounts]);
    }

    public function substitute_accounts_update_store(Request $request, $shipper_id , $id) {

        $substitute_user = SubstituteUser::find($id);
        $substitute_user->user_id = $shipper_id;
        $substitute_user->name = $request->input('name');
        $substitute_user->email = $request->input('email');
        $substitute_user->phone_number = $request->input('phone_number');
        $substitute_user->cnic = $request->input('cnic');
        $substitute_user->restriction = $request->input('restriction');

        if ($request->filled('password')) {
        $substitute_user->password = bcrypt($request->input('password'));
        }

        $substitute_user->save();
        SubstituteUserMergeSisterAccountMapping::where('substitute_user_id',$id)->delete();

        if ($request->has('account_ids')) {


            foreach($request->input('account_ids') as $merge_head_id => $account_ids) {
                foreach($account_ids as  $account_id) {
                    $Substitute_user_merge_sister_account_mapping = new SubstituteUserMergeSisterAccountMapping();

                    $Substitute_user_merge_sister_account_mapping->substitute_user_id = $substitute_user->id;
                    $Substitute_user_merge_sister_account_mapping->merged_head_id = $merge_head_id;
                    $Substitute_user_merge_sister_account_mapping->head_user_id = $shipper_id;
                    $Substitute_user_merge_sister_account_mapping->sister_user_id = $account_id;

                    $Substitute_user_merge_sister_account_mapping->save();
                }
            }
        }

        // if ($request->has('permission_ids')) {
        //     $current_permission_ids = SubstituteUserPermission::where('substitute_user_id', $id)->pluck('permission_id')->toArray();

        //     $delete_permission_ids = array_diff($current_permission_ids, $request->input('permission_ids'));
        //     $new_permission_ids = array_diff($request->input('permission_ids'), $current_permission_ids);

        //     SubstituteUserPermission::where('substitute_user_id', $id)->whereIn('permission_id', $delete_permission_ids)->delete();

        //     foreach($new_permission_ids as $permission_id) {
        //         $substitute_user_permission = new SubstituteUserPermission();

        //         $substitute_user_permission->substitute_user_id = $id;
        //         $substitute_user_permission->permission_id = $permission_id;

        //         $substitute_user_permission->save();
        //     }
        // }
        // else {
        //     SubstituteUserPermission::where('substitute_user_id', $id)->delete();
        // }

        return redirect()->route('admin.accounts.substitute_account_management.index',$shipper_id)->with(['success' => 'Substitute User: ' . $request->input('name') . ' has been updated!' , 'shipper_id' => $id]);
    }


    public function shipment_received_details(){
        ActivityTrailController::createActivityTrailLog(Auth::id(), 669);
        $admin = Admin::select('id', 'name', 'trax_id')->where('status', 1)->get();
        return view('admin.management.shipment_received.index');
    }

    public function shipment_received_details_list(Request $request)
    {

        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 670);
        }
        $all_received = ShipementReceiveDetails::join('admins','shipment_receiver_details.received_by','admins.id')
        ->select(
            'shipment_receiver_details.tracking_number as tracking_id',
            'shipment_receiver_details.receiver_name as receiverName',
            'shipment_receiver_details.receiver_cnic as receiverCnic',
            'shipment_receiver_details.receiver_relationship as relationship',
            'shipment_receiver_details.created_at as created',
            'admins.name as created_by'
            );


        $datatable = Datatables::of($all_received);

        return $datatable->make(true);
    }

    public function shipment_received_excel_upload(Request $request){

        $names = [
            'tracking_number' => 'Tracking Number',
            'receiver_name' => 'Receiver Name',
            'receiver_cnic' => 'Receiver Cnic',
            'receiver_relationship' => 'Receiver Releationship',
        ];

        $messages = [
            'required' => ':attribute is Required.',
            'required_if' => ':attribute is Required when :other is :value.',

            'receiver_cnic.regex' => ':attribute format is Invalid, required Format is: 00000-0000000-0.',
        ];

        $rules = [
            'tracking_number' => ['required', 'between:1,255'],
            'receiver_name' => ['required', 'between:1,100'],
            'receiver_cnic' => ['required', 'regex:/^[0-9]{5}-[0-9]{7}-[0-9]$/'],
            'receiver_relationship' => ['required', 'between:1,100'],
        ];

        if ($file = $request->file('receivers_excel')) {
            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();
            $header = ['Tracking Number', 'Receiver Name', 'Receiver Cnic', 'Receiver Relationship'];
        }

        if (isset($spreadsheet)) {
            $header_correct = TRUE;

            $fields = [0 => 'tracking_number', 1 => 'receiver_name', 2 => 'receiver_cnic', 3 => 'receiver_relationship'];

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
        }

        $shippers = GlobalSettings::where('type','mms_setting')->select('text')->first();
        $shippers = explode(',', $shippers->text);
        $special_dashboard_shippers = User::whereIn('id', $shippers)->pluck('id')->toArray();

        foreach ($rows as $key => $row) {
            $row_id = $key + 2;

            $validate = Validator::make($row, $rules, $messages);

            $validate->setAttributeNames($names);

            if ($validate->fails()) {
                foreach ($validate->errors()->toArray() as $key => $error_array) {
                    foreach ($error_array as $error) {
                        if (!isset($errors[$row_id][$key])) {
                            $errors[$row_id][$key] = $error;
                        }
                    }
                }
            }
        }

        if (empty($errors)) {
            foreach ($rows as $key => $row) {
                $shipment = Shipment::where('tracking_number', $row['tracking_number'])->first();
                if (empty($shipment)) {
                    return redirect()->back()->with(['error' => 'Tracking Number ' . $row['tracking_number'] . ' is Invalid']);
                } else {
                    if (in_array($shipment->user_id, $special_dashboard_shippers)) {
                        if ($shipment->shipper_status_id == 14) {
                            if ($shipment->amount != 0) {
                                return redirect()->back()->with(['error' => 'Tracking Number ' . $row['tracking_number'] . ' has no zero cod amount']);
                            }
                        } else {
                            return redirect()->back()->with(['error' => 'Tracking Number ' . $row['tracking_number'] . ' is not delivered']);
                        }
                    } else {
                        return redirect()->back()->with(['error' => 'Tracking Number ' . $row['tracking_number'] . ' has No Special Dashboard Shipper Shipment']);
                    }
                }
            }

            foreach ($rows as $key => $row) {
                $receive_details = ShipementReceiveDetails::where('tracking_number', $row['tracking_number'])->first();

                $shipment = Shipment::where('tracking_number', $row['tracking_number'])->first();
                if ($receive_details) {
                    $receive_details->shipment_id = $shipment->id;
                    $receive_details->tracking_number = $row['tracking_number'];
                    $receive_details->receiver_name = $row['receiver_name'];
                    $receive_details->receiver_cnic = $row['receiver_cnic'];
                    $receive_details->receiver_relationship = $row['receiver_relationship'];
                    $receive_details->received_by = Auth::id();
                    $receive_details->update();
                } else {
                    $receive_details = new ShipementReceiveDetails();
                    $receive_details->shipment_id = $shipment->id;
                    $receive_details->tracking_number = $row['tracking_number'];
                    $receive_details->receiver_name = $row['receiver_name'];
                    $receive_details->receiver_cnic = $row['receiver_cnic'];
                    $receive_details->receiver_relationship = $row['receiver_relationship'];
                    $receive_details->received_by = Auth::id();
                    $receive_details->save();
                }

            }
            return redirect()->back()->with(['success' => 'Uploaded Successfully']);
        }
        else{
            $errors = array_map(function ($row, $errors) {
                return $row . ':' . PHP_EOL . implode(' | ', $errors);
            }, array_keys($errors), $errors);

            return redirect()->back()->withErrors($errors);
        }

    }


    public function add_city_sub_area($city_id){
        ActivityTrailController::createActivityTrailLog(Auth::id(), 642);
        $cities = City::where('id',$city_id)->get();
        $reporting_locations = ReportingLocation::where('status', 1)->where('city_id',$city_id)->get();
        return view('admin.management.add_sub_area')->with(['cities' => $cities, 'reporting_locations' => $reporting_locations,'city_id'=>$city_id]);
    }

    public function add_city_sub_area_ajax(Request $request){

        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 643);
        }

        $city_area = CityArea::where('city_areas.city_id',$request->city_id)
            ->join('cities as c', 'c.id', '=', 'city_areas.city_id')
            ->leftjoin('admins as a', 'a.id', '=', 'city_areas.updated_by')
            ->leftjoin('reporting_locations as rl', 'rl.id', '=', 'city_areas.report_location_id')
            ->select('city_areas.*','c.location_latitude','c.location_longitude','c.name as city_name','a.name as admin_name','rl.name as relocation_name');

        return Datatables::of($city_area)
            ->addColumn('location', function ($result) {
                $location = '<div class="text-center">';
                if ($result->location_latitude != null && $result->location_longitude != null) {
                    $location .= '<button type="button" class="btn btn-primary btn-sm"><a class="white" href="http://www.google.com/maps/place/' . $result->location_latitude . ',' . $result->location_longitude . '" target="_blank"><i class="la la-map-marker align-middle"></i></a></button>';
                    $location .= '</div>';
                    return $location;
                } else {
                    return '-';
                }
            })
            ->editColumn('status', function ($result) {
              if($result->status == 0){
                  return 'Not Active';
              }else{
                  return  'Active';
              }
            })
            ->editColumn('relocation_name', function ($result) {
                $rl = $result->relocation_name . '-'.$result->city_name;
                return $rl;

            })
            ->editColumn('default', function ($result) {
              if($result->default == 1){
                  return 'Yes';
              }else{
                  return  'No';
              }
            })
            ->addColumn("action", function ($result) {
                if (session('role_id') == 1 || count(array_intersect([90, 91], session('permissions'))) !== 0) {
                    $dropdown = '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                ';
                    if (session('role_id') == 1 || in_array(843, session('permissions'))) {
                            $dropdown .= '<button type="button" class="dropdown-item" 
                             data-target-id=' . $result->id . ' 
                             data-target-city_id='.$result->city_id .' 
                             data-target-report_location_id=' . $result->report_location_id . ' 
                             data-target-name=' . $result->name . ' 
                             rel="editcityarea" data-toggle="modal" data-target="#city_area_edit_modal"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit Area</div></button>';
                    }

                    if (session('role_id') == 1 || in_array(844, session('permissions'))) {
                            if($result->status == 0) {
                                $dropdown .= '<button type="button" class="dropdown-item"><div class="row no-gutters align-items-center active_sub_area" rel="1"><div class="col-2"><i class="ft-check-circle"></i></div><div class="col-9 offset-1">Active</div></button>';
                            }else{
                                $dropdown .= '<button type="button" class="dropdown-item"><div class="row no-gutters align-items-center active_sub_area" rel="0"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Deactivate</div></button>';
                            }
                    }

                    if (session('role_id') == 1 || in_array(844, session('permissions'))) {
                        if($result->detault == 0) {
                            $dropdown .= '<button type="button" class="dropdown-item"><div class="row no-gutters align-items-center mark_default" rel="1"><div class="col-2"><i class="ft-check-circle"></i></div><div class="col-9 offset-1">Mark Default</div></button>';
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
            })
            ->make(true);

    }

    public function city_sub_area_post(Request $request){

        $names = [
            'id' => 'ID',
            'name' => 'Name',
            'city_id' => 'City ID',
            'report_location_id' => 'Reporting ID',
        ];

        $messages = [
            'required' => ':attribute is Required.',
            'required_if' => ':attribute is Required when :other is :value.',
            'filled' => ':attribute is Optional but cannot be Empty if Present.',
            'integer' => ':attribute must be an Integer.',
            'numeric' => ':attribute must be a Number.',
            'boolean' => ':attribute must be 0 or 1.',
            'digits_between' => ':attribute must be between :min and :max Digits.',
            'email' => ':attribute must be a Valid Email Address.',
            'exists' => 'Given :attribute is of Invalid ID.',
            'unique' => ':attribute is already Present.',
            'date_format' => ':attribute must be of valid Format, required Format is: YYYY-MM-DD.',
            'in' => ':attribute must be No or Yes.',
            'check_name' => ':attribute is already exists.',
            'check_id' => ':attribute with same area and city already exist.',
            ];

        $rules = [
            'id' => 'nullable',
            'name' => 'required|string|max:255|check_name',
            'city_id' => 'required|string|max:255',
            'report_location_id' => 'required|string|max:255',
        ];


        Validator::extend('check_name', function ($attribute, $value, $parameters, $validator){
            $data = $validator->getData();
            $name = $data['name'];
            $city_id = $data['city_id'];
            if(!isset($data['id'])) {
                $city_area = CityArea::where('city_id', $city_id)->where('name', $name);
                if ($city_area->exists()) {
                    return false;
                } else {
                    return true;
                }
            }else{
                $id = $data['id'];
                $city_area = CityArea::where('city_id', $city_id)->where('name', $name)->where('id','!=',$id);
                if ($city_area->exists()) {
                    return false;
                } else {
                    return true;
                }
            }

        });

        $validate = Validator::make($request->all(), $rules, $messages);


        if ($validate->passes()) {

            $default_city = CityArea::where('city_id',$request->city_id);
            $city_area = !isset($request->id) ?  new CityArea() : CityArea::find($request->id);
            $city_area->city_id  = $request->city_id;
            $city_area->report_location_id  = $request->report_location_id;
            $city_area->name  = $request->name;
            $city_area->updated_by  = auth()->user()->id;
            $city_area->status  = 1;
            $city_area->default  = ($default_city->exists()) ? 0 : 1;
            $city_area->save();

            $data = response()->json([
                'status' => 1,
                'message' => 'Success',
            ]);

        }else{
            $data = response()->json([
                'status' => 0,
                'errors' => $validate->errors(),
                'message' => 'Error',
            ]);
        }
        return $data;


    }

    public function city_area_status(Request $request){
        if(isset($request->id)){
            $city_area = CityArea::where('id',$request->id)->update(['status'=>$request->status]);
            $data = response()->json([
                'status' => 1,
                'message' => 'Success',
            ]);

            return $data;
        }
    }
    public function city_area_default(Request $request){
        if(isset($request->id)){
            CityArea::where('id','!=' ,$request->id)->where('city_id',$request->city_id)->update(['default'=>0]);
            CityArea::where('id',$request->id)->update(['default'=>$request->default_status]);
            $data = response()->json([
                'status' => 1,
                'message' => 'Success',
            ]);

            return $data;
        }
    }

    public function disable_booking_status(Request $request){
        $cityIDS = $request->input('cityIDS', []);

        if(!is_array($cityIDS) || empty($cityIDS)){
            return response()->json(['status' => 'Invalid IDS'], 400);
        }
        $error = City::whereIn('id', $cityIDS)->where('booking_enable_status', 0)->get();

        if(count($error) > 0 && count($cityIDS) === count($error)){
            return response()->json(['status' => 'Already Disabled !!']);
        }else if (count($error) > 0 && count($cityIDS) != count($error)){
            return response()->json(['status' => 'Some Of The Selected Cities Are Already Disabled']);
        }

        City::whereIn('id', $cityIDS)->update(['booking_enable_status' => '0']);

        $this->logCityStatusChanges($cityIDS, false);

        return response()->json(['status' => 200]);
    }

    public function enable_booking_status(Request $request){
        $cityIDS = $request->input('cityIDS', []);

        if(!is_array($cityIDS) || empty($cityIDS)){
            return response()->json(['status' => 'Invalid IDS'], 400);
        }

        $error = City::whereIn('id', $cityIDS)->where('booking_enable_status', 1)->get();

        if(count($error) > 0 && count($cityIDS) === count($error)){
            return response()->json(['status' => 'Already Enabled !!']);
        }else if (count($error) > 0 && count($cityIDS) != count($error)){
            return response()->json(['status' => 'Some Of The Selected Cities Are Already Enabled']);
        }

        City::whereIn('id', $cityIDS)->update(['booking_enable_status' => '1']);
        $this->logCityStatusChanges($cityIDS, true);

        return response()->json(['status' => 200]);

    }


    public function add_rate_commission_corporate_reimb(Request $request, $shipper_ids)
    {

        $shipper_ids = explode(',', $shipper_ids);
        foreach($shipper_ids as $shipper_id)
        {
            if($request->has('user_id')){

                $total_commission = $request->total_commission;
                $users_count = count($request->user_id);

                //when shipper register
                $sales_commission_register = SalesCommission::where('shipper_id', $shipper_id);
                if($sales_commission_register->exists()){
                    if(!isset($sales_commission_register->first()->updated_by)){
                        SalesCommissionUser::where('sales_commission_id', $sales_commission_register->first()->id)->delete();
                        SalesCommission::where('shipper_id', $shipper_id)->delete();
                    }
                }

                $sales_commission = SalesCommission::where('shipper_id', $shipper_id);
                if($sales_commission->exists()){
                    $user_type = [];
                    $sales_commission = $sales_commission->first();
                    $sales_commission_user = SalesCommissionUser::where('sales_commission_id', $sales_commission->id)->pluck('commission')->toArray();
                    $sales_commission_user_count = SalesCommissionUser::where('sales_commission_id', $sales_commission->id)->pluck('user_id')->toArray();
                    $types = SalesCommissionUser::where('sales_commission_id', $sales_commission->id)->pluck('user_type')->toArray();
                    foreach($types as $key => $user_type_value){
                        if(isset($sales_commission_user_count[$key])){
                            $user_type[$sales_commission_user_count[$key]] = $user_type_value ;
                        }
                    }
                    if(count($sales_commission_user) > 0){
                        $total_commission = array_sum($sales_commission_user) + $total_commission;
                        $sales_commission_user_count = array_unique(array_merge($sales_commission_user_count, $request->user_id));
                    }else{
                        $total_commission;
                    }

                    $sales_commission->commission_users_count = count($sales_commission_user_count);
                    $sales_commission->commission = strval($total_commission);
                    $sales_commission->updated_by = Auth::id();
                    $sales_commission->save();
                    $sales_commission_id = $sales_commission->id;
                    $actual_commission = 0;
                    if ($request->has('edit')){
                        Sales::where('sales_commission_id', $sales_commission_id)->delete();
                    }

                    foreach($request->tier_id as $row_id => $tier){
                        $sales_tier = SalesTier::find($tier);
                        $old_kam_id = null; 

                        if(isset($request->user_id[$row_id])){
                            $old_kam_id = SalesCommissionUser::where('sales_commission_id', $sales_commission_id)->latest()->first()->tier_id == 3 ? SalesCommissionUser::where('sales_commission_id', $sales_commission_id)->latest()->first()->user_id : null;

                            if (strpos($request->user_id[$row_id], 'riders') !== false) {
                                preg_match('/\d+/', $request->user_id[$row_id], $matches);
                                $rider_id = isset($matches[0]) ? $matches[0] : null;
                                $same_user = SalesCommissionUser::where('sales_commission_id', $sales_commission_id)->where('user_id', $rider_id);
                                if($same_user->exists()){
                                    $same_user->delete();
                                }
                            }else{
                                $same_user = SalesCommissionUser::where('sales_commission_id', $sales_commission_id)->where('user_id', $request->user_id[$row_id]);
                                if($same_user->exists()){
                                    $same_user->delete();
                                }
                            }
                        }
                        if($sales_tier){
                            $sales_commission_user = new SalesCommissionUser();
                            $sales_commission_user->sales_commission_id = $sales_commission_id;
                            $sales_commission_user->tier_type_id = $sales_tier->tier_type;
                            $sales_commission_user->tier_id = $tier;
                            if($sales_tier->tier_type == 1){
                                $index = intval($request->user_id[$row_id]);
                                if (strpos($request->user_id[$row_id], 'riders') !== false) {
                                    $sales_commission_user->user_type = "2";
                                }

                                if(isset($user_type[$index]) && $user_type[$index] == "2"){
                                    $sales_commission_user->user_type = "2";
                                }
                                $sales_commission_user->user_id = $request->user_id[$row_id];

                            }else if($sales_tier->tier_type == 2){
                                $external_user = new SalesCommissionExternalUser();
                                $external_user->name = $request->user_id[$row_id];
                                $external_user->shipper_id = $shipper_id;
                                $external_user->save();
                                $sales_commission_user->user_id = $external_user->id;
                            }
                            $sales_commission_user->commission = $request->commission_percentage[$row_id];
                            $actual_commission += $request->commission_percentage[$row_id];
                            $sales_commission_user->save();

                        if($tier == 3){
                            AccountTaggingLog::logTagging(
                                $shipper_id,        
                                Auth::id(),             
                                $old_kam_id,                 
                                $sales_commission_user->user_id, 
                                3                
                            );
                        }
                        }
                    }

                    $sales_commission->commission = $total_commission;
                    $sales_commission->save();
                }else{
                    $sales_commission = new SalesCommission();
                    $sales_commission->shipper_id = $shipper_id;
                    $sales_commission->commission_users_count = $users_count;
                    $sales_commission->commission = $total_commission;
                    $sales_commission->updated_by = Auth::id();
                    $sales_commission->save();
                    $sales_commission_id = $sales_commission->id;
                    $actual_commission = 0;
                    foreach($request->tier_id as $row_id => $tier){
                        $sales_tier = SalesTier::find($tier);
                        if($sales_tier){
                            $sales_commission_user = new SalesCommissionUser();
                            $sales_commission_user->sales_commission_id = $sales_commission_id;
                            $sales_commission_user->tier_type_id = $sales_tier->tier_type;
                            $sales_commission_user->tier_id = $tier;
                            if($sales_tier->tier_type == 1){
                                if (strpos($request->user_id[$row_id], 'riders') !== false) {
                                    $sales_commission_user->user_type = "2";
                                }


                                if(isset($user_type[$row_id]) && $user_type[$row_id] == "2"){
                                    $sales_commission_user->user_type = "2";
                                }

                                $sales_commission_user->user_id = $request->user_id[$row_id];
                            }else if($sales_tier->tier_type == 2){
                                $external_user = new SalesCommissionExternalUser();
                                $external_user->name = $request->user_id[$row_id];
                                $external_user->shipper_id = $shipper_id;
                                $external_user->save();
                                $sales_commission_user->user_id = $external_user->id;
                            }
                            $sales_commission_user->commission = $request->commission_percentage[$row_id];
                            $actual_commission += $request->commission_percentage[$row_id];
                            $sales_commission_user->save();

                            if($tier == 3){
                                AccountTaggingLog::logTagging(
                                    $shipper_id,        
                                    Auth::id(),             
                                    null,                 
                                    $sales_commission_user->user_id, 
                                    3                   
                                );
                            }
                        }
                    }
                    $sales_commission->commission = $actual_commission;
                    $sales_commission->save();
                }
            }else{
                $existing_sale_commission = SalesCommission::where('shipper_id', $shipper_ids)->first();
                    if ($existing_sale_commission) {
                        SalesCommissionUser::where('sales_commission_id', $existing_sale_commission->id)->delete();
                        SalesCommissionExternalUser::where('shipper_id', $shipper_ids)->delete();
                        SalesCommission::where('shipper_id', $shipper_ids)->delete();
                    }
            }

            self::balance_count_commission($shipper_id);

            $sale_tier_tag = SaleTierTag::where('user_id', $shipper_id);
            $sales_tiers_kam = DB::table('sales_tiers')->where('tier_name', 'LIKE', '%KAM%')->orWhere('tier_name', 'LIKE', '%kam%')->first()->id ?? null;

            if(isset($request->tier_id[$row_id]) && (isset($request->user_id[$row_id])) && $request->tier_id[$row_id] == $sales_tiers_kam){
                if (!$sale_tier_tag->exists()) {
                    $sale_tier_object = new SaleTierTag();
                    $sale_tier_object->user_id = $shipper_id;
                    $sale_tier_object->kam = $request->user_id[$row_id];
                    $sale_tier_object->save();
                } else {
                    $sale_tier_object = $sale_tier_tag->first();
                    $sale_tier_object->kam = $request->user_id[$row_id];
                    $sale_tier_object->save();
                }

               
            }

        }

        return back()->with('success', 'Commission Has Been Added !!');

    }

    public function balance_count_commission($shipperId)
    {
        $sales_commission = SalesCommission::where('shipper_id', $shipperId)->first();

        if ($sales_commission) {
            $sales_commission_id = $sales_commission->id;
            $actual_commission = SalesCommissionUser::whereIn('sales_commission_id', [$sales_commission_id])->pluck('commission')->toArray();
            $sales_commission->commission = array_sum($actual_commission);
            $sales_commission->commission_users_count = SalesCommissionUser::whereIn('sales_commission_id', [$sales_commission_id])->count();
            $sales_commission->save();
        }
    }

    public function excluded_shippers(Request $request){
        $user_id = $request->user_id;
        $intercept_shipper = ShipperInterceptExclude::where('user_id', $user_id)->first();
        if($intercept_shipper){
            return response()->json(['intercept_shipper' => $intercept_shipper]);
        }else {
            return response()->json(['intercept_shipper' => null]);
        }
    }

    public static function addManagementHubUser($admin_ids, $insertedIds) {

        // Handle insertedIds logic
        if (is_array($insertedIds) && count($insertedIds) > 0 && count($admin_ids) > 0) {
            foreach ($insertedIds as $city_id) {
                // Repeat the process for each city_id
                foreach ($admin_ids as $admin_id) {
                    $admin_hub_exist = AdminHub::where('admin_id', $admin_id)
                        ->where('hub_id', $city_id)
                        ->first();
                    if (!$admin_hub_exist) {
                        $data[] = [
                            'admin_id' => $admin_id,
                            'hub_id' => $city_id
                        ];
                    }
                }
            }

            if (!empty($data)) {
                AdminHub::insert($data);
            }
        } else if (count($admin_ids) > 0) {
            //For Single Normal Old
            foreach ($admin_ids as $admin_id) {
                $admin_hub_exist = AdminHub::where('admin_id', $admin_id)
                    ->where('hub_id', $insertedIds)
                    ->first();
                if (!$admin_hub_exist) {
                    $data[] = [
                        'admin_id' => $admin_id,
                        'hub_id' => $insertedIds
                    ];
                }
            }
            if (!empty($data)) {
                AdminHub::insert($data);
            }
        }
    }

    public function faf_charges_info(Request $request)
    {
        $faf_charges = FafCharges::where('user_id',$request->user_id)->first();
        return response()->json(['status' => !empty($faf_charges->status) ? $faf_charges->status : 0]);
    }
    public function faf_charges_submit(Request $request)
    {
        $faf_charges_checkbox = isset($request->faf_charges_checkbox) ? 1 : 0;
        $faf_charges = FafCharges::where('user_id',$request->user_id)->first();
        if(empty($faf_charges)){
            $faf_charges = new FafCharges();
        }
        $faf_charges->user_id =$request->user_id;
        $faf_charges->status =$faf_charges_checkbox;
        $faf_charges->save();

        return redirect()->back()->with('success', 'FAF Charges Status Updated');
    }

    public static function compareWeightCharges($user_id, $table1, $table2, $date1 = null, $date2 = null, $firstValue, $count_list)
    {
        $excludeColumns = [
            'id',
            'user_id',
            'shipping_mode_id',
            'delivery_type_id',
            'created_at',
            'updated_at',
            'base'
        ];

        $currentColumns = array_diff((new $table1)->getFillable(), $excludeColumns);
        $previousColumns = array_diff((new $table2)->getFillable(), $excludeColumns);

        $calculateTotal = function ($table, $user_id, $date, $columns) {
            $query = $table::where('user_id', $user_id);
            if ($date) {
                $query->where('created_at', $date);
            }
            return $query->selectRaw('SUM(' . implode(') + SUM(', $columns) . ') as total')->value('total');
        };

        if (empty($date2)) {
            $latestHistory = $table2::where('user_id', $user_id)->latest('created_at')->first();

            if (!$latestHistory) {
                return $table1::where('user_id', $user_id)->exists() ? 'Rates Updated Only' : '';
            }

            $currentTotal = $calculateTotal($table1, $user_id, null, $currentColumns);
            $previousTotal = $calculateTotal($table2, $user_id, $latestHistory->created_at, $previousColumns);

            if (!$firstValue || ($count_list) == 1) {
                return $currentTotal > $previousTotal ? 'green' : ($currentTotal < $previousTotal ? 'red' : 'yellow');
            }

            return 'Rates Updated Only';
        } else {
            $previousDate = $table2::where('user_id', $user_id)->where('created_at', '>=', $date2->created_at)->value('created_at');
            $currentDate = $table2::where('user_id', $user_id)->where('created_at', '>=', $date1->created_at)->value('created_at');

            $currentTotal = $calculateTotal($table2, $user_id, $currentDate, $currentColumns);
            $previousTotal = $calculateTotal($table2, $user_id, $previousDate, $previousColumns);

            return $currentTotal > $previousTotal ? 'green' : ($currentTotal < $previousTotal ? 'red' : 'yellow');
        }
    }


    public static function compareFuelCharges($user_id, $table1, $table2, $date1 = null, $date2 = null, $firstValue, $count_list)
    {

        // Helper function to get fuel surcharge sum based on user ID and optional date
        $getFuelSurchargeSum = function($table, $user_id, $date = null) {
            $query = $table::where('user_id', $user_id);
            if ($date) {
                $query->where('created_at', $date);
            }
            return $query->sum('fuel_surcharge');
        };

        if (empty($date2)) {
            // Case where date2 is empty
            $latestDate = $table2::where('user_id', $user_id)->latest('created_at')->value('created_at');
            $historyFuelSurchargeSum = $getFuelSurchargeSum($table2, $user_id, $latestDate);
            $existingFuelSurchargeSum = $getFuelSurchargeSum($table1, $user_id);

        } else {
            // Case with both dates provided
            $historyDate = $table2::where('user_id', $user_id)->where('created_at', '>=', $date2->created_at)->value('created_at');
            $historyFuelSurchargeSum = $getFuelSurchargeSum($table2, $user_id, $historyDate);

            $nextDate = $table2::where('user_id', $user_id)->where('created_at', '>=', $date1->created_at)->value('created_at');
            $existingFuelSurchargeSum = $getFuelSurchargeSum($table2, $user_id, $nextDate);
        }

        // Comparison of fuel surcharges
        if (!$firstValue || ($count_list) == 1) {
            return $existingFuelSurchargeSum > $historyFuelSurchargeSum ? 'green'
                : ($existingFuelSurchargeSum < $historyFuelSurchargeSum ? 'red' : 'yellow');
        }

        return 'Fuel Added Only';
    }

    private function getWeightTables(int $weightType): array
    {
        switch ($weightType) {
            case 1:
                return [CorporateWeightCharge::class, HistoryCorporateWeightCharge::class];
            case 2:
                return [CorporateWeightChargeZoneWise::class, HistoryCorporateWeightChargeZoneWise::class];
            default:
                return [CorporateDefaultWeightCharge::class, CorporateDefaultHistoryWeightCharge::class];
        }
    }

    private function getFuelTables(int $fuelType): array
    {
        if ($fuelType === 1 || $fuelType === 2) {
            return [CorporateFuelSurcharge::class, HistoryCorporateFuelSurcharge::class];
        }

        return [CorporateDefaultFuelSurcharge::class, CorporateDefaultFuelSurcharge::class];
    }

    public function addExcelCityHub(Request $request)
    {
        $errors = [];
        $rows = [];

        // Check if a file is uploaded
        if ($file = $request->file('add_city')) {
            $spreadsheet = IOFactory::createReaderForFile($file)
                ->setReadDataOnly(true)
                ->load($file)
                ->getActiveSheet()
                ->toArray();
        }

        $fields1 = [
            'regular_rush', 'regular_saver_plus', 'regular_swift', 'regular_same_day',
            'replacement_rush', 'replacement_saver_plus', 'replacement_swift', 'replacement_same_day',
            'try_and_buy_rush', 'try_and_buy_saver_plus', 'try_and_buy_swift', 'try_and_buy_same_day',
            'reverse_pickup_rush', 'reverse_pickup_saver_plus', 'reverse_pickup_swift', 'reverse_pickup_same_day',
            'ftl_rush', 'ftl_saver_plus', 'ftl_swift', 'ftl_same_day',
            'walkin_rush', 'walkin_saver_plus', 'walkin_swift'
        ];

        if (!empty($spreadsheet)) {
            $column_count = 48;
            $fields = [
                'name', 'city_code', 'is_city', 'is_hub', 'hub_id', 'zone_id', 'address', 'attempt_tat',
                'location_latitude', 'location_longitude', 'hub_location_latitude', 'hub_location_longitude', 'pickup', 'pickup_cut_off_time', 'gc_area',
                'regular_rush', 'regular_saver_plus', 'regular_swift', 'regular_same_day',
                'replacement_rush', 'replacement_saver_plus', 'replacement_swift', 'replacement_same_day',
                'try_and_buy_rush', 'try_and_buy_saver_plus', 'try_and_buy_swift', 'try_and_buy_same_day',
                'reverse_pickup_rush', 'reverse_pickup_saver_plus', 'reverse_pickup_swift', 'reverse_pickup_same_day',
                'ftl_rush', 'ftl_saver_plus', 'ftl_swift', 'ftl_same_day',
                'walkin_rush', 'walkin_saver_plus', 'walkin_swift',
                'osa_name_1','osa_rate_1','osa_name_2','osa_rate_2','osa_name_3','osa_rate_3','osa_name_4','osa_rate_4','closest_hub','vehicles_list'
            ];

            if (count($spreadsheet[0]) !== $column_count) {
                return redirect()->back()->with('error', 'Invalid Columns, Kindly follow the Template provided');
            }

            unset($spreadsheet[0]);

            $rows = array_map(function ($row) use ($fields, $fields1) {
                $combinedRow = array_combine($fields, $row);

                foreach ($fields1 as $field) {
                    if (array_key_exists($field, $combinedRow) && is_null($combinedRow[$field])) {
                        unset($combinedRow[$field]);
                    }
                }

                return $combinedRow;
            }, $spreadsheet);

        } else {
            $forms = $request->except(['_token', '_method']);
            $rows = array_map(function ($form) use ($fields1) {
                foreach ($fields1 as $field) {
                    if (array_key_exists($field, $form) && is_null($form[$field])) {
                        unset($form[$field]);
                    }
                }
                return $form;
            }, $forms);

        }

        if(empty($rows)){
            return redirect()->back()->with('error', 'Excel is empty');
        }

        //TO-6917-limitation-setting-in-city-manag
        if(count($rows) > 300){
            return redirect()->route('admin.management.city.index')->with('error', 'Maximum limit of bulk is 300');
        }
        //END

        // Validation rules, messages, and attribute names
        $rules = [
            'name' => 'required|string',
            'city_code' => 'nullable',
            'hub_id' => 'required_without:zone_id|required_if:is_city,1|hub_id_check',
            'zone_id' => 'required_without:hub_id|required_if:is_hub,1|zone_id_check',
            'is_city' => 'required_without_all:is_hub|nullable|boolean',
            'is_hub'  => 'required_without_all:is_city|nullable|boolean',
            'attempt_tat' => 'required|integer|min:1',
            'location_latitude' => 'required|numeric',
            'location_longitude' => 'required|numeric',
            'hub_location_latitude' => 'required|numeric',
            'hub_location_longitude' => 'required|numeric',
            'pickup' => 'boolean',
            'address' => 'nullable',
            'gc_area' => 'nullable|boolean',
            'pickup_cut_off_time' => 'required_if:pickup,1|min:0|max:23',
            'closest_hub' => 'nullable|required_with:vehicles_list',
            'vehicles_list' => 'required_with:closest_hub',
            'delivery_types' => 'required_without_all:regular_rush,regular_saver_plus,regular_swift,regular_same_day,replacement_rush,replacement_saver_plus,replacement_swift,replacement_same_day,try_and_buy_rush,try_and_buy_saver_plus,try_and_buy_swift,try_and_buy_same_day,reverse_pickup_rush,reverse_pickup_saver_plus,reverse_pickup_swift,reverse_pickup_same_day,ftl_rush,ftl_saver_plus,ftl_swift,ftl_same_day|boolean',
        ];
        for ($i = 1; $i <= 4; $i++) {
            $rules["osa_name_$i"] = ['nullable', 'regex:/^[a-zA-Z0-9\s]+$/'];
            $rules["osa_rate_$i"] = 'required_with:osa_name_' . $i;
        }
        $messages = [
            'name.required' => 'City Name is required.',
            'hub_id.required_without' => 'Select Hub is required when you set is_city bit to 1.',
            'zone_id.required_without' => 'Select Zone is required when you set is_hub bit to 1.',
            'attempt_tat.required' => 'Add Attempt TAT is required.',
            'latitude.required' => 'Latitude is required.',
            'longitude.required' => 'Longitude is required.',
            'hub_latitude.required_if' => 'Hub Latitude is required when Hub is selected.',
            'hub_longitude.required_if' => 'Hub Longitude is required when Hub is selected.',
            'pickup_cut_off_time.required_if' => 'Pickup Cut Off Time is required if Pickup is selected.',
            'delivery_types.required_without_all' => 'At least one delivery type must be selected.',
            'is_city.required_without_all' => 'Either city or hub must be selected.',
            'is_hub.required_without_all'  => 'Either hub or city must be selected.',
            'zone_id_check'  => 'Zone Not Exists Or Not Required When City Is Selected.',
            'hub_id_check'  => 'Hub Not Exists Or Not Required When Hub Is Selected.',
        ];

        for ($i = 1; $i <= 4; $i++) {
            $messages["osa_rate_$i.required_if"] = "OSA Rate $i is required when OSA Name $i is provided.";
        }

        $names = [
            'name' => 'City Name',
            'hub_id' => 'Select Hub',
            'zone_id' => 'Select Zone',
            'attempt_tat' => 'Attempt TAT',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'hub_latitude' => 'Hub Latitude',
            'hub_longitude' => 'Hub Longitude',
            'is_city' => 'City',
            'is_hub' => 'Hub',
            'gc_area'=> 'GC Area',
            'address'=> 'Address'
        ];

        Validator::extend('hub_id_check', function ($attribute, $value, $parameters, $validator)  {
            if(!is_null($value)){
                $exists = City::where('id', $value)->exists();
                if (!$exists) {
                    $validator->addReplacer('hub_name_check', function ($message, $attribute, $rule, $parameters) use($value) {
                        return "$value does not exist.";
                    });
                    return false;
                }
                return true;
            }
            return true;
        });

        Validator::extend('zone_id_check', function ($attribute, $value, $parameters, $validator)  {
            if(!is_null($value)){
                $exists = Zone::where('id', $value)->exists();
                if (!$exists) {
                    $validator->addReplacer('zone_name_check', function ($message, $attribute, $rule, $parameters) use($value) {
                        return "$value does not exist.";
                    });
                    return false;
                }
                return true;
            }
            return true;
        });

        foreach ($rows as $row_id => $row) {
            $validate = Validator::make($row, $rules, $messages);
            $validate->setAttributeNames($names);

            $validate->after(function ($validator) use ($row, $request) {
                if ($row['is_city'] == 1 && $row['is_hub'] == 1) {
                    $validator->errors()->add('is_city', 'Both is_city and is_hub cannot be present at the same time.');
                    $validator->errors()->add('is_hub', 'Both is_city and is_hub cannot be present at the same time.');
                }

                if ($row['is_city'] == 0 && $row['is_hub'] == 0) {
                    $validator->errors()->add('is_city', 'Both is_city and is_hub cannot be 0 at the same time.');
                    $validator->errors()->add('is_hub', 'Both is_city and is_hub cannot be 0 at the same time.');
                }
            });

            // Check if validation fails
            if ($validate->fails()) {
                foreach ($validate->errors()->toArray() as $key => $error_array) {
                    foreach ($error_array as $error) {
                        $errors[$row_id][$key] = $error;
                    }
                }
            }
        }

        if (empty($errors)) {
            $isHubArray = [];
            $isCityArray = [];
            $hubMappings = [];

            $forms = $rows;

            $keysToUnsetDeliveryTypes = [
                'regular_rush',
                'regular_saver_plus',
                'regular_swift',
                'regular_same_day',
                'replacement_rush',
                'replacement_saver_plus',
                'replacement_swift',
                'replacement_same_day',
                'try_and_buy_rush',
                'try_and_buy_saver_plus',
                'try_and_buy_swift',
                'try_and_buy_same_day',
                'reverse_pickup_rush',
                'reverse_pickup_saver_plus',
                'reverse_pickup_swift',
                'reverse_pickup_same_day',
                'ftl_rush',
                'ftl_saver_plus',
                'ftl_swift',
                'ftl_same_day',
                'walkin_rush',
                'walkin_saver_plus',
                'walkin_swift',
            ];

            $city_hub_exclude = ['is_city',
                'is_hub'];

            $walk_in_types = [ 'walkin_rush',
                'walkin_saver_plus',
                'walkin_swift',
                ];

            $closest_hub_types = [ 'closest_hub',
                'vehicles_list',
            ];

            $osa_list_excluded = [ 'osa_name_1','osa_rate_1','osa_name_2','osa_rate_2','osa_name_3','osa_rate_3','osa_name_4','osa_rate_4'];

            $cityID = array_column($forms, 'hub_id');
            $cities = City::whereIn('id', $cityID)->get()->keyBy('id');

            foreach ($forms as $item) {
                if ((array_key_exists('closest_hub', $item) && is_null($item['closest_hub'])) || $item['is_city'] == 1) {
                    unset($item['closest_hub']);
                }
                if ((array_key_exists('vehicles_list', $item) && is_null($item['vehicles_list'])) || $item['is_city'] == 1) {
                    unset($item['vehicles_list']);
                }

                if (isset($item['is_hub']) && $item['is_hub'] == "1") {
                    $item['hub'] = 1;
                    $item['created_at'] = now();
                    $item['updated_at'] = now();
                    $item['is_excel'] = 1;
                    $item['created_by'] = auth()->id();
                    $isHubArray[] = $item;
                    $hubMappings[] = [
                        'closest_hub' => $item['closest_hub'] ?? null,
                        'vehicles_list' => $item['vehicles_list'] ?? null
                    ];
                }

                if (isset($item['is_city']) && $item['is_city'] == "1") {
                    $zone = $cities->get($item['hub_id']);
                    $item['zone_id'] = $zone ? $zone->zone_id : null;
                    $item['created_at'] = now();
                    $item['updated_at'] = now();
                    $item['is_excel'] = 1;
                    $item['created_by'] = auth()->id();
                    $isCityArray[] = $item;
                }
            }


            if (!empty($isCityArray)) {
                // Process the city data for delivery and walk-in types
                list($isCityArray, $deliveryTypes, $walkInTypes, $osaList) = self::processCityArray($isCityArray, $keysToUnsetDeliveryTypes, $city_hub_exclude, $walk_in_types, $osa_list_excluded, []);

                if (!empty($isCityArray)) {
                    City::insert($isCityArray);
                }

                $insertedIds = self::getLastInsertedCityIds(count($isCityArray));
                sort($insertedIds);

                $historyArray = self::historyCityArray($insertedIds);

                if(!empty($historyArray)){
                    CityHistory::insert($historyArray);
                }

                if(!empty($osaList)){
                    self::processOsaList($insertedIds, $osaList);
                }

                $walkInTypesFiltered = self::filterTypes($walkInTypes);
                $deliveryTypesFiltered = self::filterTypes($deliveryTypes);


                $walkInTypeToBeInserted = self::prepareWalkInTypes($insertedIds, $walkInTypesFiltered);
                if (!empty($walkInTypeToBeInserted)) {
                    WalkInCities::insert($walkInTypeToBeInserted);
                }

                $deliveryTypesToBeInserted = self::prepareDeliveryTypes($insertedIds, $deliveryTypesFiltered);
                if (!empty($deliveryTypesToBeInserted)) {
                    CityDelivery::insert($deliveryTypesToBeInserted);
                }

                // Insert cities into zone classes
                self::insertCitiesToZones($insertedIds, 1);
                self::insertCitiesToZones($insertedIds, 2);
            }

            if (!empty($isHubArray)) {
                // Process the hub data for delivery and walk-in types
                list($isHubArray, $deliveryTypes, $walkInTypes, $osaList, $closestHubTypes) = self::processCityArray($isHubArray, $keysToUnsetDeliveryTypes, $city_hub_exclude, $walk_in_types, $osa_list_excluded, $closest_hub_types);

                if (!empty($isHubArray)) {
                    City::insert($isHubArray);
                }

                //Inserted IDS
                $insertedIds = self::getLastInsertedCityIds(count($isHubArray));
                sort($insertedIds);

                $historyArray = self::historyCityArray($insertedIds);

                if(!empty($historyArray)){
                    CityHistory::insert($historyArray);
                }


                if(!empty($osaList)){
                    self::processOsaList($insertedIds, $osaList);
                }

                $walkInTypesFiltered = self::filterTypes($walkInTypes);
                $deliveryTypesFiltered = self::filterTypes($deliveryTypes);

                $walkInTypeToBeInserted = self::prepareWalkInTypes($insertedIds, $walkInTypesFiltered);
                if (!empty($walkInTypeToBeInserted)) {
                    WalkInCities::insert($walkInTypeToBeInserted);
                }

                $deliveryTypesToBeInserted = self::prepareDeliveryTypes($insertedIds, $deliveryTypesFiltered);
                if (!empty($deliveryTypesToBeInserted)) {
                    CityDelivery::insert($deliveryTypesToBeInserted);
                }

                // Insert hubs into zone classes
                $this->insertCitiesToZones($insertedIds, 1);
                $this->insertCitiesToZones($insertedIds, 2);

                //Add Management Hub Users In Admin Hubs
                $admin_ids = Admin::where('management_user', 1)->pluck('id')->toArray();
                self::addManagementHubUser($admin_ids, $insertedIds);

                // Insertion for dynamic mapping hubs
                if (!empty($hubMappings) && !empty($insertedIds)) {

                    foreach ($hubMappings as $key => $value) {

                        if(!isset($value['closest_hub'])){
                            continue;
                        }

                        if(isset($insertedIds[$key])){
                            if (is_array($value['vehicles_list'])) {
                                $vehicles_list = implode(',', $value['vehicles_list']);
                            } else {
                                $vehicles_list = $value['vehicles_list'];
                            }
                            $hubMappings[$key] = [
                                'closest_hub' => $value['closest_hub'],
                                'vehicles' => explode(',' , $vehicles_list),
                                'city_id' => $insertedIds[$key],
                            ];
                        }
                    }

                    // Dispatch jobs for each mapping
                    foreach ($hubMappings as $mapping) {
                        if(isset($mapping['closest_hub'], $mapping['vehicles'], $mapping['city_id'])) {
                            dispatch(new MakeDynamicHubsMapping($mapping['vehicles'], $mapping['closest_hub'], $mapping['city_id'], auth()->id()));
                        }
                    }
                }

            }
            return redirect()->route('admin.management.city.index')->with('success', 'Hub city added successfully');
        } else {
            $hubs = City::pluck( 'name', 'id');

            $zones = Zone::pluck('name', 'id');

            $vehicles = Fleet::where('status', 1)->pluck('reg_number','id');

            return view('admin.errors.bulk-excel-city-errors')->with([
                'data' => $rows,
                'errors' => $errors,
                'hubs' => $hubs,
                'zones' => $zones,
                'vehicles' => $vehicles
            ]);
        }
    }

    // Function to process city/hub array and filter out unwanted keys
    public static function processCityArray(
        $array,
        $keysToUnsetDeliveryTypes,
        $city_hub_exclude,
        $walk_in_types,
        $osa_list_excluded,
        $closest_hub_types
    ) {
        $deliveryTypes = [];
        $walkInTypes = [];
        $osaList = [];
        $closestHubTypes = [];

        foreach ($array as $key2 => $values) {
            if (!is_array($values)) {
                continue;
            }

            $keysToUnset = [];

            // Check for delivery types
            foreach ($values as $key3 => $value) {
                if (in_array($key3, $keysToUnsetDeliveryTypes)) {
                    $deliveryTypes[] = $key2 . $key3;
                    $keysToUnset[] = $key3;
                }
                // Unset for city hub exclude
                if (in_array($key3, $city_hub_exclude)) {
                    $keysToUnset[] = $key3;
                }
                // Unset for walk-in types and track them
                if (in_array($key3, $walk_in_types)) {
                    $walkInTypes[] = $key2 . $key3;
                    $keysToUnset[] = $key3;
                }
                // Unset for OSA list exclude but store the value in $osaList first
                if (in_array($key3, $osa_list_excluded)) {
                    if (isset($array[$key2][$key3])) {
                        $osaList[$key2 . $key3] = $array[$key2][$key3];
                    }
                    $keysToUnset[] = $key3;
                }
                // Unset for closest hub types and store the value in $closestHubTypes
                if (in_array($key3, $closest_hub_types)) {
                    if (isset($array[$key2]['closest_hub']) && isset($array[$key2]['vehicles_list'])) {
                        $closestHubTypes[$array[$key2]['closest_hub']] = $array[$key2]['vehicles_list'];
                    } else {
                        $closestHubTypes[$key2] = []; // For precise insertion for hub-wise index if null too
                    }
                    $keysToUnset[] = $key3;
                }
            }

            foreach ($keysToUnset as $keyToUnset) {
                unset($array[$key2][$keyToUnset]);
            }
        }

        return [$array, $deliveryTypes, $walkInTypes, $osaList, $closestHubTypes];
    }

    // Function to retrieve the last inserted city IDs
    public static function getLastInsertedCityIds($count) {
        return DB::table('cities')
            ->where('is_excel', 1)
            ->orderBy('id', 'desc')
            ->limit($count)
            ->pluck('id')
            ->toArray();
    }

    // Function to filter types based on their prefixes
    public static function filterTypes($types) {
        $filteredTypes = [];
        foreach ($types as $item) {
            preg_match('/^\d+/', $item, $matches);
            $prefix = isset($matches[0]) ? intval($matches[0]) : 0;
            $filteredTypes[$prefix][] = $item;
        }
        foreach ($filteredTypes as $prefix => $group) {
            $group = array_values($group);
        }
        return $filteredTypes;
    }

    // Function to prepare walk-in types for insertion
    public static function prepareWalkInTypes($insertedIds, $filteredTypes) {
        $walkInTypesToBeInserted = [];
        $cities = City::whereIn('id', $insertedIds)->get()->keyBy('id');

        foreach ($filteredTypes as $key => $values) {
            if (isset($insertedIds[$key])) {
                $city = $cities[$insertedIds[$key]];

                foreach ($values as $value) {
                    $type = self::getWalkInType($value);
                    if ($type !== null) {
                        $walkInTypesToBeInserted[] = [
                            'city_id' => $insertedIds[$key],
                            'pickup' => $city->pickup,
                            'delivery' => $type
                        ];
                    }
                }
            }
        }
        return $walkInTypesToBeInserted;
    }

    // Function to map walk-in type values
    public static function getWalkInType($value) {
        if (str_contains($value, 'rush')) {
            return 1;
        } elseif (str_contains($value, 'saver_plus')) {
            return 2;
        } elseif (str_contains($value, 'swift')) {
            return 3;
        }
        return null;
    }

    // Function to prepare delivery types for insertion
    public static function prepareDeliveryTypes($insertedIds, $filteredTypes) {
        $deliveryTypesToBeInserted = [];

        foreach ($filteredTypes as $key => $values) {
            if (isset($insertedIds[$key])) {
                foreach ($values as $value) {
                    list($bType, $sType) = self::getDeliveryType($value);

                    if ($bType !== null && $sType !== null) {
                        $deliveryTypesToBeInserted[] = [
                            'city_id' => $insertedIds[$key],
                            'booking_type_id' => $sType,
                            'shipping_mode_id' => $bType
                        ];
                    }
                }
            }
        }
        return $deliveryTypesToBeInserted;
    }

    // Function to map delivery type values
    public static function getDeliveryType($value) {
        $bType = null;
        $sType = null;

        if (str_contains($value, 'rush')) {
            $bType = 1;
        } elseif (str_contains($value, 'saver_plus')) {
            $bType = 2;
        } elseif (str_contains($value, 'swift')) {
            $bType = 3;
        } elseif (str_contains($value, 'same_day')) {
            $bType = 4;
        }

        if (str_contains($value, 'regular')) {
            $sType = 1;
        } elseif (str_contains($value, 'replacement')) {
            $sType = 2;
        } elseif (str_contains($value, 'try_and_buy')) {
            $sType = 3;
        } elseif (str_contains($value, 'reverse_pickup')) {
            $sType = 5;
        } elseif (str_contains($value, 'ftl')) {
            $sType = 6;
        }

        return [$bType, $sType];
    }

    // Function to insert cities into zone  s
    public static function insertCitiesToZones($insertedIds, $classificationId) {
        $zones = Zone::where('business_category_id', 1)->get();
        $zoneClassData = [];

        foreach ($insertedIds as $city) {
            foreach ($zones as $zone) {
                $zoneClassData[] = [
                    'city_id' => $city,
                    'zone_id' => $zone->id,
                    'class' => 3,
                    'zone_classification_id' => $classificationId,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }

        if (!empty($zoneClassData)) {
            ZoneClassCity::insert($zoneClassData);
        }
    }


    public static function processOsaList($insertedIds, $osaList)
    {
        $result = [];

        foreach ($insertedIds as $key => $id) {
            $tempResult = [];

            foreach ($osaList as $key2 => $value) {
                if (preg_match('/(\d+)osa_(name|rate)_(\d+)/', $key2, $matches)) {
                    $index = $matches[1];
                    $field = $matches[2];
                    $sub_index = $matches[3];

                    if ($index == $key) {
                        $tempResult[$sub_index][$field] = $value;
                    }
                }
            }

            if (!empty($tempResult)) {
                $result[$id] = $tempResult;
            }
        }

        $osaListToBeInserted = [];
        foreach($result as $key => $value){
            foreach($value as $value2){
                $osaListToBeInserted[]=[
                    'city_id' => $key,
                    'osa_name' => $value2['name'],
                    'osa_rate' => $value2['rate'],
                    'admin_id' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }
        CityOsaRate::insert($osaListToBeInserted);
    }


    public static function historyCityArray(array $cityIds)
    {
        $cities = City::whereIn('id', $cityIds)->get()->keyBy('id');

        $historyArray = [];

        foreach ($cityIds as $cityId) {
            if ($cities->has($cityId)) {
                $cityData = $cities->get($cityId);

                $historyEntry = [
                    'zone_id' => $cityData->zone_id,
                    'attempt_tat' => $cityData->attempt_tat,
                    'location_latitude' => $cityData->location_latitude,
                    'location_longitude' => $cityData->location_longitude,
                    'hub_location_latitude' => $cityData->hub_location_latitude,
                    'hub_location_longitude' => $cityData->hub_location_longitude,
                    'address' => $cityData->address,
                    'status' => $cityData->status,
                    'gc_area' => $cityData->gc_area,
                    'pickup' => $cityData->pickup,
                    'pickup_cut_off_time' => $cityData->pickup_cut_off_time,
                    'hub' => $cityData->hub,
                    'city_id' => $cityData->id,
                ];

                $historyArray[] = $historyEntry;
            }
        }

        City::whereIn('id', $cityIds)->where('hub', 1)->update(['hub_id' => DB::raw('id')]);

        return $historyArray;
    }

    private function logCityStatusChanges(array $cityIds, bool $newStatus, $columnType = 1)
    {
        $logs = [];
        $userId = auth()->id();
        $now = now();

        foreach ($cityIds as $cityId) {
            $logs[] = [
                'column_type' => $columnType, 
                'updated_by' => $userId,
                'city_id' => $cityId,
                'new_status' => $newStatus,
                'changed_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        CityStatusChangeLog::insert($logs);
    }

    public function get_city_status_logs($cityId, Request $request)
    {
        $type = $request->get('type') === 'booking' ? 1 : 2;

        $logs = CityStatusChangeLog::with(['city', 'admin'])
            ->where('city_id', $cityId)
            ->where('column_type', $type)
            ->orderByDesc('changed_at')
            ->get()
            ->map(function ($log) {
                return [
                    'city_name' => $log->city?->name ?? 'N/A',
                    'new_status' => $log->new_status,
                    'changed_at' => optional($log->changed_at)->format('Y-m-d H:i:s'),
                    'updated_by_name' => $log->admin?->name ?? 'N/A',
                ];
            });

        return response()->json($logs);
    }
    
    public static function logCityChangesAfterUpdate($oldCity)
    {
        $cityId = $oldCity->id;

        $oldHub = !empty($oldCity->hub_id) ? City::find($oldCity->hub_id) : null;
        $oldZone = !empty($oldCity->zone_id) ? Zone::find($oldCity->zone_id) : null;
        $oldProvince = !empty($oldCity->province_id) ? Province::find($oldCity->province_id) : null;

        $oldData = [
            'city' => array_merge(
                $oldCity->only([
                    'name',
                    'city_code',
                    'hub',
                    'hub_id',
                    'zone_id',
                    'province_id',
                    'pickup',
                    'gc_area',
                    'attempt_tat',
                    'location_latitude',
                    'location_longitude',
                    'hub_location_latitude',
                    'hub_location_longitude',
                    'address',
                    'pickup_cut_off_time'
                ]),
                [
                    'hub_name' => optional($oldHub)->name,
                    'zone_name' => optional($oldZone)->name,
                    'province_name' => optional($oldProvince)->name,
                ]
            ),
            'osa_rates' => $oldCity->osaRates->map(function ($r) {
                return ['osa_name' => $r->osa_name, 'osa_rate' => $r->osa_rate];
            })->toArray(),
            'deliveries' => $oldCity->deliveries->map(function ($d) {
                return ['booking_type_id' => $d->booking_type_id, 'shipping_mode_id' => $d->shipping_mode_id];
            })->toArray(),
            'walk_ins' => $oldCity->walkIns->map(function ($w) {
                return ['pickup' => $w->pickup, 'delivery' => $w->delivery];
            })->toArray(),
        ];

        $newCity = City::with(['osaRates', 'deliveries', 'walkIns'])->find($cityId);

        if (!$newCity) {
            return;
        }

        $newHub = !empty($newCity->hub_id) ? City::find($newCity->hub_id) : null;
        $newZone = !empty($newCity->zone_id) ? Zone::find($newCity->zone_id) : null;
        $newProvince = !empty($newCity->province_id) ? Province::find($newCity->province_id) : null;

        $newData = [
            'city' => array_merge(
                $newCity->only([
                    'name',
                    'city_code',
                    'hub',
                    'hub_id',
                    'zone_id',
                    'province_id',
                    'pickup',
                    'gc_area',
                    'attempt_tat',
                    'location_latitude',
                    'location_longitude',
                    'hub_location_latitude',
                    'hub_location_longitude',
                    'address',
                    'pickup_cut_off_time'
                ]),
                [
                    'hub_name' => optional($newHub)->name,
                    'zone_name' => optional($newZone)->name,
                    'province_name' => optional($newProvince)->name,
                ]
            ),
            'osa_rates' => $newCity->osaRates->map(function ($r) {
                return ['osa_name' => $r->osa_name, 'osa_rate' => $r->osa_rate];
            })->toArray(),
            'deliveries' => $newCity->deliveries->map(function ($d) {
                return ['booking_type_id' => $d->booking_type_id, 'shipping_mode_id' => $d->shipping_mode_id];
            })->toArray(),
            'walk_ins' => $newCity->walkIns->map(function ($w) {
                return ['pickup' => $w->pickup, 'delivery' => $w->delivery];
            })->toArray(),
        ];

        if ($oldData !== $newData) {
            CityLog::create([
                'city_id' => $cityId,
                'old_data' => $oldData,
                'new_data' => $newData,
                'admin_id' => auth()->id(),
            ]);
        }
    }

    public static function getCityChanges(array $oldData, array $newData)
    {
        $changes = [];

        $excludedFields = ['zone_id', 'hub_id', 'province_id'];


        foreach ($oldData['city'] as $key => $oldValue) {

            if (in_array($key, $excludedFields)) {
                continue;
            }

            $newValue = $newData['city'][$key] ?? null;
            if ($oldValue != $newValue) {
                $changes['city'][$key] = [
                    'old' => $oldValue,
                    'new' => $newValue
                ];
            }
        }

        $compareList = function (array $oldList, array $newList) {
            $old = collect($oldList)->map(fn($v) => json_encode($v))->toArray();
            $new = collect($newList)->map(fn($v) => json_encode($v))->toArray();

            return [
                'removed' => array_values(array_map('json_decode', array_diff($old, $new))),
                'added'   => array_values(array_map('json_decode', array_diff($new, $old))),
            ];
        };

        $osaChanges = $compareList($oldData['osa_rates'], $newData['osa_rates']);
        if (!empty($osaChanges['added']) || !empty($osaChanges['removed'])) {
            $changes['osa_rates'] = $osaChanges;
        }

        $deliveryChanges = $compareList($oldData['deliveries'], $newData['deliveries']);
        if (!empty($deliveryChanges['added']) || !empty($deliveryChanges['removed'])) {
            $changes['deliveries'] = $deliveryChanges;
        }

        $walkInChanges = $compareList($oldData['walk_ins'], $newData['walk_ins']);
        if (!empty($walkInChanges['added']) || !empty($walkInChanges['removed'])) {
            $changes['walk_ins'] = $walkInChanges;
        }

        return $changes;
    }

    public function getAjaxCityChanges($id)
    {
        $logs = CityLog::with('admin')->where('city_id', $id)
            ->orderByDesc('id')  
            ->get();

        if ($logs->isEmpty()) {
            return response()->json([]);
        }

        $allChanges = [];

        foreach ($logs as $log) {
            $oldData = is_string($log->old_data) ? json_decode($log->old_data, true) : $log->old_data;
            $newData = is_string($log->new_data) ? json_decode($log->new_data, true) : $log->new_data;

            $allChanges[] = [
                'timestamp' => $log->created_at->toDateTimeString(),
                'changes' => $this->getCityChanges($oldData, $newData),
                'admin' => $log->admin?->name . ' - ' . $log->admin?->trax_id
            ];
        }

        return response()->json($allChanges);
    }

    public function taggingHistory($id)
    {
        $logs = AccountTaggingLog::with(['changedBy', 'newSalesAdmin', 'prevSalesAdmin'])
            ->where('account_id', $id)
            ->orderByDesc('created_at')
            ->get()
            ->groupBy(function ($log) {
                return $log->created_at->format('Y-m-d H:i:s');
            });

        return response()->json($logs);
    }
}