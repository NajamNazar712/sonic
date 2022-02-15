<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Admins\DwsWeightChargesController;
use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Models\Admin\AdminHub;
use App\Http\Models\Admin\CorporateRateType;
use App\Http\Models\Admin\CorporateUserPackagingInvoiceLog;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\HistoryShipperBankAccount;
use App\Http\Models\Admin\Lead\Lead;
use App\Http\Models\Admin\Lead\LeadLog;
use App\Http\Models\Admin\SalePersonTag;
use App\Http\Models\Admin\Segment;
use App\Http\Models\Admin\Territory;
use App\Http\Models\Admin\WalkInStandardWeightCharge;
use App\Http\Models\AdminLogs;
use App\Http\Models\AverageShipmentCycle;
use App\Http\Models\BusinessCategory;
use App\Http\Models\CityDelivery;
use App\Http\Models\BanksList;
use App\Http\Models\CityHistory;
use App\Http\Models\Commission\SalesCommission;
use App\Http\Models\Commission\SalesCommissionExternalUser;
use App\Http\Models\Commission\SalesCommissionUser;
use App\Http\Models\Commission\SalesTier;
use App\Http\Models\CorporateDefaultRateStatus;
use App\Http\Models\CorporateRateStatus;
use App\Http\Models\CRM\CrmRequestStatusHistory;
use App\Http\Models\DeliveryType;
use App\Http\Models\DuplicateUser;
use App\Http\Models\HR\Employee;
use App\Http\Models\HR\EmployeeBloodGroup;
use App\Http\Models\InternationalUsersInformation;
use App\Http\Models\InvoicingCycle;
use App\Http\Models\PackagingMaterialTypes;
use App\Http\Models\Operataions\OperationForecast;
use App\Http\Models\PaymentCycle;
use App\Http\Models\PendingCorporateDefaultRateStatus;
use App\Http\Models\RateRemark;
use App\Http\Models\PendingPayment;
use App\Http\Models\PendingPaymentShipment;
use App\Http\Models\Rates\HistoryCorporateRateStatus;
use App\Http\Models\Rates\HistoryRateDestinationHub;
use App\Http\Models\Rates\HistoryRateOriginHub;
use App\Http\Models\Rates\InternationalEconomyRate;
use App\Http\Models\Rates\InternationalEconomyRateStatus;
use App\Http\Models\Rates\MinimumChargeableWeightSetting;
use App\Http\Models\Rates\PendingCorporateRateStatus;
use App\Http\Models\Rates\PendingRateDestinationHub;
use App\Http\Models\Rates\PendingRateOriginHub;
use App\Http\Models\Rates\RateDestinationHub;
use App\Http\Models\Rates\RateOriginHub;
use App\Http\Models\Reference;
use App\Http\Models\Operataions\OperationForecastShipments;
use App\Http\Models\Operataions\OperationForecastWeightRange;
use App\Http\Models\Operataions\OperationsForecastLastUpdatedTime;
use App\Http\Models\Operataions\OperationsOutgoingPickupRequests;
use App\Http\Models\Operataions\OperationsOutgoingPickupRequestShipments;
use App\Http\Models\Operataions\OperationsOutgoingTopCustomers;
use App\Http\Models\Operataions\OperationsOutgoingTopCustomersShipments;
use App\Http\Models\PackagingMaterialTypeSizes;
use App\Http\Models\Rates\HistoryBookingTypeCharges;
use App\Http\Models\Rates\HistoryCashHandlingCharge;
use App\Http\Models\Rates\HistoryDiscountCharge;
use App\Http\Models\Rates\HistoryFuelSurcharge;
use App\Http\Models\Rates\HistoryInsuranceCharge;
use App\Http\Models\Rates\HistoryPackagingCharge;
use App\Http\Models\Rates\HistoryRateStatus;
use App\Http\Models\Rates\HistoryReturnCharge;
use App\Http\Models\Rates\HistoryWeightCharge;
use App\Http\Models\Rates\PendingRateStatus;
use App\Http\Models\Rates\RateHistory;
use App\Http\Models\SalesTierTypeTag;
use App\Http\Models\SaleTierTag;
use App\Http\Models\SaleTierTagHistory;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\Shipper\UserBankInfo;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\ShipperContact;
use App\Http\Models\ShipperNotificationEmail;
use App\Http\Models\Sister_account\MergedAccountHead;
use App\Http\Models\Sister_account\MergedSisterAccount;
use App\Http\Models\Sister_account\MergedSisterAccountMapping;
use App\Http\Models\UserDocumentAttachment;
use App\Http\Models\WalkInCities;
use App\Http\Models\ZoneClassCity;
use App\RouteLocations;
use App\Http\Models\RouteType;
use App\TerritoryTagHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Models\Admin\StandardWeightCharge;
use App\Http\Models\Admin\StandardCashHandlingCharge;
use App\Http\Models\Admin\StandardInsuranceCharge;
use App\Http\Models\Admin\StandardReturnCharge;
use App\Http\Models\Admin\StandardPackagingCharge;
use App\Http\Models\Admin\StandardFuelSurcharge;
use App\Http\Models\Admin\StandardBookingTypeCharge;
use App\Http\Models\BookingType;
use App\Http\Models\Rates\PendingCashHandlingCharge;
use App\Http\Models\CashHandlingCharge;
use App\Http\Models\City;
use App\Http\Models\Zone;
use App\Http\Models\Admin\Admin;
use App\Http\Models\FuelSurcharge;
use App\Http\Models\InsuranceCharge;
use App\Http\Models\PackagingCharge;
use App\Http\Models\Rates\PendingFuelSurcharge;
use App\Http\Models\Rates\PendingInsuranceCharge;
use App\Http\Models\Rates\PendingPackagingCharge;
use App\Http\Models\Product;
use App\Http\Models\Rider;
use App\Http\Models\RiderCategory;
use App\Http\Models\Route;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentPaymentStatus;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\WMS\WmsUserInformation;
use App\Http\Models\WMS\WmsPerProductCharge;
use App\Http\Models\WMS\WmsPerSquareFootCharge;
use App\Http\Models\WMS\WmsLabellingCharge;
use App\Http\Models\WMS\WmsPackingCharge;
use App\Http\Models\WMS\WmsStorageTypeCharge;
use App\Http\Models\WMS\WmsPendingUserInformation;
use App\Http\Models\WMS\WmsPendingPerProductCharge;
use App\Http\Models\WMS\WmsPendingPerSquareFootCharge;
use App\Http\Models\WMS\WmsPendingLabellingCharge;
use App\Http\Models\WMS\WmsPendingPackingCharge;
use App\Http\Models\WMS\WmsPendingStorageTypeCharge;
use App\Http\Models\WMS\WmsHistoryUserInformation;
use App\Http\Models\WMS\WmsHistoryPerProductCharge;
use App\Http\Models\WMS\WmsHistoryPerSquareFootCharge;
use App\Http\Models\WMS\WmsHistoryLabellingCharge;
use App\Http\Models\WMS\WmsHistoryPackingCharge;
use App\Http\Models\WMS\WmsHistoryStorageTypeCharge;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Shipper\User;
use App\Http\Models\PickupType;
use App\Http\Models\WeightCharge;
use App\Http\Models\BookingTypeCharges;
use App\Http\Models\CityOsaRate;
use App\Http\Models\ReturnCharge;
use App\Http\Models\DiscountCharge;
use App\Http\Models\PendingDwsWeightCharges;
use App\Http\Models\Rates\PendingWeightCharge;
use App\Http\Models\Rates\PendingBookingTypeCharges;
use App\Http\Models\Rates\PendingReturnCharge;
use App\Http\Models\Rates\PendingDiscountCharge;
use App\Http\Models\RateStatus;
use App\Http\Models\ShippingMode;
use App\Http\Models\SubCategorySegment;
use App\Http\Models\WMS\WmsStorageType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\HR\EmployeeDesignation;
use CreateCityOsaRatesTable;

class AdminDashboardController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function index(){
        /*$stats = array();
        $graph = array();
        $sales=array();
        $leads = array();
        $graph_dates = array();
        $today = Carbon::now()->endOfDay();
        $thirtyDays = Carbon::now()->subDays(29)->startOfDay();
        $stats['total'] = Shipment::whereBetween('created_at',[$thirtyDays,$today]);
        $stats['booked'] = Shipment::where('shipper_status_id',1)->whereBetween('created_at',[$thirtyDays,$today]);
        $stats['canceled'] = Shipment::where('shipper_status_id',17)->whereBetween('created_at',[$thirtyDays,$today]);
        $stats['arrived'] = Shipment::where('shipper_status_id',2)->whereBetween('created_at',[$thirtyDays,$today]);
        $stats['destination'] = Shipment::where('shipper_status_id',4)->whereBetween('created_at',[$thirtyDays,$today]);
        $stats['out_for_delivery'] = Shipment::where('shipper_status_id',5)->whereBetween('created_at',[$thirtyDays,$today]);
        $stats['return_confirm'] = Shipment::where('shipper_status_id',20)->whereBetween('created_at',[$thirtyDays,$today]);
        $stats['return_delivered'] = Shipment::where('shipper_status_id',25)->whereBetween('created_at',[$thirtyDays,$today]);
        $stats['pending_shipments'] = Shipment::whereIn('shipper_status_id',[6,7,8,9,13,15,18,51,52,56])->whereBetween('created_at',[$thirtyDays,$today]);
        $stats['pending_return'] = Shipment::whereIn('shipper_status_id',[21,22,23,24,26,27,28,29,57,60])->whereBetween('created_at',[$thirtyDays,$today]);
        $stats['confirmation_pending'] = Shipment::whereIn('shipper_status_id',[12,54,55])->whereBetween('created_at',[$thirtyDays,$today]);
        $stats['in_transit'] = Shipment::where('shipper_status_id',3)->whereBetween('created_at',[$thirtyDays,$today]);
        $stats['delivered'] = Shipment::whereIn('shipper_status_id',[14,16, 30, 36,37,39,40,41,47])->whereBetween('created_at',[$thirtyDays,$today]);
        $stats['pending'] = Shipment::whereIn('shipper_status_id',[4, 5,6,7,8,9,10,11,12,13,15,18,19,49])->whereBetween('created_at',[$thirtyDays,$today]);
        $stats['complaints_launched'] = CrmRequestStatusHistory::where('status_id', 1)->whereBetween('created_at',[$thirtyDays,$today]);
        $stats['complaints_in_process'] = CrmRequestStatusHistory::where('status_id', 2)->whereBetween('created_at',[$thirtyDays,$today]);
        $stats['complaints_closed'] = CrmRequestStatusHistory::where('status_id', 4)->whereBetween('created_at',[$thirtyDays,$today]);
        $stats['complaints_rejected'] = CrmRequestStatusHistory::where('status_id', 7)->whereBetween('created_at',[$thirtyDays,$today]);
        $sales['total_accounts']=DB::table('users')->select('name')->get();
        $sales['active_accounts']=User::where('status',3);
        $sales['inactive_accounts']=User::where('status',4)->where('blacklist',0);
        $sales['pending_accounts']=User::whereIn('status',[0,1,2]);
        $sales['blocked_accounts']=User::where('blacklist',1);

        if (session('role_id') != 1) {
            $stats['total'] = $stats['total']->where(function($query) {
                $query->whereHas('pickup_address.city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                })->orWhereHas('consignee_city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                });
            });
            $stats['booked'] = $stats['booked']->where(function($query) {
                $query->whereHas('pickup_address.city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                })->orWhereHas('consignee_city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                });
            });

            $stats['canceled'] = $stats['canceled']->where(function($query) {
                $query->whereHas('pickup_address.city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                })->orWhereHas('consignee_city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                });
            });

            $stats['arrived'] = $stats['arrived']->where(function($query) {
                $query->whereHas('pickup_address.city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                })->orWhereHas('consignee_city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                });
            });

            $stats['in_transit'] = $stats['in_transit']->where(function($query) {
                $query->whereHas('pickup_address.city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                })->orWhereHas('consignee_city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                });
            });

            $stats['delivered'] = $stats['delivered']->where(function($query) {
                $query->whereHas('pickup_address.city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                })->orWhereHas('consignee_city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                });
            });

            $stats['destination'] = $stats['destination']->where(function($query) {
                $query->whereHas('pickup_address.city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                })->orWhereHas('consignee_city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                });
            });
            $stats['out_for_delivery'] = $stats['out_for_delivery']->where(function($query) {
                $query->whereHas('pickup_address.city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                })->orWhereHas('consignee_city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                });
            });
            $stats['return_confirm'] = $stats['return_confirm']->where(function($query) {
                $query->whereHas('pickup_address.city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                })->orWhereHas('consignee_city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                });
            });
            $stats['return_delivered'] = $stats['return_delivered']->where(function($query) {
                $query->whereHas('pickup_address.city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                })->orWhereHas('consignee_city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                });
            });
            $stats['pending_shipments'] = $stats['pending_shipments']->where(function($query) {
                $query->whereHas('pickup_address.city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                })->orWhereHas('consignee_city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                });
            });
            $stats['confirmation_pending'] = $stats['confirmation_pending']->where(function($query) {
                $query->whereHas('pickup_address.city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                })->orWhereHas('consignee_city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                });
            });
            $stats['pending_return'] = $stats['pending_return']->where(function($query) {
                $query->whereHas('pickup_address.city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                })->orWhereHas('consignee_city', function ($sub_query) {
                    $sub_query->whereIn('hub_id', session('hubs'));
                });
            });
        }



        $stats['total'] = number_format($stats['total']->count());
        $stats['booked'] = number_format($stats['booked']->count());
        $stats['canceled'] = number_format($stats['canceled']->count());
        $stats['arrived'] = number_format($stats['arrived']->count());
        $stats['in_transit'] = number_format($stats['in_transit']->count());
        $stats['delivered'] = number_format($stats['delivered']->count());
        $stats['destination'] = number_format($stats['destination']->count());
        $stats['out_for_delivery'] = number_format($stats['out_for_delivery']->count());
        $stats['return_confirm'] = number_format($stats['return_confirm']->count());
        $stats['return_delivered'] = number_format($stats['return_delivered']->count());
        $stats['pending_shipments'] = number_format($stats['pending_shipments']->count());
        $stats['confirmation_pending'] = number_format($stats['confirmation_pending']->count());
        $stats['pending_return'] = number_format($stats['pending_return']->count());
        $stats['complaints_launched'] =  number_format($stats['complaints_launched']->count());
        $stats['complaints_in_process'] =  number_format($stats['complaints_in_process']->count());
        $stats['complaints_closed'] =  number_format($stats['complaints_closed']->count());
        $stats['complaints_rejected'] =  number_format($stats['complaints_rejected']->count());
        $sales['total_accounts']=number_format($sales['total_accounts']->count());
        $sales['active_accounts']=number_format($sales['active_accounts']->count());
        $sales['inactive_accounts']=number_format($sales['inactive_accounts']->count());
        $sales['pending_accounts']=number_format($sales['pending_accounts']->count());
        $sales['blocked_accounts']=number_format( $sales['blocked_accounts']->count());

        $graph_dates['current'] = Carbon::now();
        $graph_dates['old_date'] = Carbon::now()->subDays(29);

        if (session('department_id') == 7 && (in_array(session('id'), session('sale_users_bypass')))) {
            $shippers = User::whereIn('id', session('tagged_shippers'))->where('status', 3)->where('blacklist', 0)->select('id', 'name')->get();
        }
        else {
            $shippers = User::where('status', 3)->where('blacklist', 0)->select('id','name')->get();
        }

        $cities = City::select('id','name')->get();
        $service_type = BookingType::where('id', '!=', 3)->select('id','booking_type')->get();*/

//        $admin = Admin::where('id', Auth::id())->first();
//        //incoming
//        $doughnut_chart_shipments_count['booked'] = OperationForecast::where('shipper_status_id', 1)->where('hub_id', $admin->default_hub_id)->where('booking_type_id', 1)->whereBetween('created_at',[$thirtyDays,$today])->sum('operation_forecasts.count');
//        $doughnut_chart_shipments_count['arrived_at_origin'] = OperationForecast::where('shipper_status_id', 2)->where('hub_id', $admin->default_hub_id)->where('booking_type_id', 1)->whereBetween('created_at',[$thirtyDays,$today])->sum('operation_forecasts.count');
//        $doughnut_chart_shipments_count['in_transit'] = OperationForecast::where('shipper_status_id', 3)->where('hub_id', $admin->default_hub_id)->where('booking_type_id', 1)->whereBetween('created_at',[$thirtyDays,$today])->sum('operation_forecasts.count');
//        $doughnut_chart_shipments_count['arrived_at_destination'] = OperationForecast::where('shipper_status_id', 4)->where('hub_id', $admin->default_hub_id)->where('booking_type_id', 1)->whereBetween('created_at',[$thirtyDays,$today])->sum('operation_forecasts.count');
//        $doughnut_chart_shipments_count['not_attempted'] = OperationForecast::where('shipper_status_id', 7)->where('hub_id', $admin->default_hub_id)->where('booking_type_id', 1)->whereBetween('created_at',[$thirtyDays,$today])->sum('operation_forecasts.count');
//        $doughnut_chart_shipments_count['delivery_unsuccessful'] = OperationForecast::where('shipper_status_id', 8)->where('hub_id', $admin->default_hub_id)->where('booking_type_id', 1)->whereBetween('created_at',[$thirtyDays,$today])->sum('operation_forecasts.count');
//        $doughnut_chart_shipments_count['on_hold'] = OperationForecast::where('shipper_status_id', 9)->where('hub_id', $admin->default_hub_id)->where('booking_type_id', 1)->whereBetween('created_at',[$thirtyDays,$today])->sum('operation_forecasts.count');
//        $doughnut_chart_shipments_count['total'] = $doughnut_chart_shipments_count['booked'] + $doughnut_chart_shipments_count['arrived_at_origin'] + $doughnut_chart_shipments_count['in_transit'] + $doughnut_chart_shipments_count['arrived_at_destination'] + $doughnut_chart_shipments_count['not_attempted'] + $doughnut_chart_shipments_count['delivery_unsuccessful'] + $doughnut_chart_shipments_count['on_hold'];
//
//        $incoming_bar_chart_shipments['one'] = OperationForecastShipments::where('weight_range_id',1)->where('hub_id', $admin->default_hub_id)->where('booking_type_id', 1)->whereBetween('created_at',[$thirtyDays,$today])->count();
//        $incoming_bar_chart_shipments['two'] = OperationForecastShipments::where('weight_range_id',2)->where('hub_id', $admin->default_hub_id)->where('booking_type_id', 1)->whereBetween('created_at',[$thirtyDays,$today])->count();
//        $incoming_bar_chart_shipments['three'] = OperationForecastShipments::where('weight_range_id',3)->where('hub_id', $admin->default_hub_id)->where('booking_type_id', 1)->whereBetween('created_at',[$thirtyDays,$today])->count();
//        $incoming_bar_chart_shipments['four'] = OperationForecastShipments::where('weight_range_id',4)->where('hub_id', $admin->default_hub_id)->where('booking_type_id', 1)->whereBetween('created_at',[$thirtyDays,$today])->count();
//
//        $riders_count = Rider::where('status', 1)->where('city_id', $admin->default_hub_id)->count();
//        $sixtyDays = Carbon::now()->subDays(58)->startOfDay();
//        if($riders_count == 0){
//            $per_rider_loads = ceil(($incoming_bar_chart_shipments['one'] + $incoming_bar_chart_shipments['two'] + $incoming_bar_chart_shipments['three'] + $incoming_bar_chart_shipments['four']));
//        }
//        else{
//            $per_rider_loads = ceil(($incoming_bar_chart_shipments['one'] + $incoming_bar_chart_shipments['two'] + $incoming_bar_chart_shipments['three'] + $incoming_bar_chart_shipments['four']) / $riders_count);
//        }
//
//        $light_deliveries = ($incoming_bar_chart_shipments['one'] + $incoming_bar_chart_shipments['two']);
//        $heavy_deliveries = ($incoming_bar_chart_shipments['three'] + $incoming_bar_chart_shipments['four']);
//
//        $day_wise_growth_thirty = OperationForecast::where('hub_id', $admin->default_hub_id)->where('booking_type_id', 1)->whereBetween('created_at',[$thirtyDays,$today])->sum('operation_forecasts.count');
//        $day_wise_growth_sixty = OperationForecast::where('hub_id', $admin->default_hub_id)->where('booking_type_id', 1)->whereBetween('created_at',[$sixtyDays,$thirtyDays])->sum('operation_forecasts.count');
//        if($day_wise_growth_sixty == 0){
//            $day_wise_growth_percentage = 0;
//        }
//        else{
//            $day_wise_growth = ($day_wise_growth_thirty - $day_wise_growth_sixty) / $day_wise_growth_sixty;
//            $day_wise_growth_percentage = number_format($day_wise_growth * 100,1);
//        }
//        $operation_incoming['per_rider_loads'] = $per_rider_loads;
//        $operation_incoming['day_wise_growth'] = $day_wise_growth_percentage . '%';
//        $operation_incoming['heavy_deliveries'] = $heavy_deliveries;
//        $operation_incoming['light_deliveries'] = $light_deliveries;
//
//
//
//        $operation_dates['from'] = $graph_dates['old_date'];
//        $operation_dates['to'] = $graph_dates['current'];
//
//        //outgoing
//        $operation_outgoing_pickups['no_of_shipments'] = OperationsOutgoingPickupRequests::where('hub_id', $admin->default_hub_id)->where('booking_type_id', 1)->whereBetween('created_at',[$thirtyDays,$today])->sum('operations_outgoing_pickup_requests.shipments_count');
//        $operation_outgoing_pickups['pickups_count'] = OperationsOutgoingPickupRequests::select(DB::raw('count(operations_outgoing_pickup_requests.id) as count'))->where('hub_id', $admin->default_hub_id)->where('booking_type_id', 1)->whereBetween('created_at',[$thirtyDays,$today])->groupBy('operations_outgoing_pickup_requests.pickup_request_id')->get();
//        $operation_outgoing_pickups['pickups'] = 0;
//        foreach ($operation_outgoing_pickups['pickups_count'] as $pickups_count){
//            $operation_outgoing_pickups['pickups'] = $operation_outgoing_pickups['pickups'] + $pickups_count->count;
//        }
//
//        $outgoing_top_five_customers = OperationsOutgoingTopCustomers::leftjoin('users as u', 'u.id', '=', 'operations_outgoing_top_customers.user_id')->select('u.name as name', DB::raw('(SELECT SUM(shipments_count) FROM operations_outgoing_top_customers AS ootc WHERE ootc.user_id = operations_outgoing_top_customers.user_id AND updated_at BETWEEN "'. $thirtyDays .'" AND "'. $today .'") AS count'))
//            ->whereBetween('operations_outgoing_top_customers.created_at',[$thirtyDays,$today])
//            ->orderBy('count', 'desc')
//            ->groupBy('u.id')
//            ->take(5)->get()->toArray();
//        if(array_key_exists(0, $outgoing_top_five_customers)){
//            $outgoing_doughnut_top_five_customers['first'] = $outgoing_top_five_customers[0];
//        }
//        else{
//            $outgoing_doughnut_top_five_customers['first']['name'] = '-';
//            $outgoing_doughnut_top_five_customers['first']['count'] = 0;
//        }
//        if(array_key_exists(1, $outgoing_top_five_customers)){
//            $outgoing_doughnut_top_five_customers['second'] = $outgoing_top_five_customers[1];
//        }
//        else{
//            $outgoing_doughnut_top_five_customers['second']['name'] = '-';
//            $outgoing_doughnut_top_five_customers['second']['count'] = 0;
//        }
//        if(array_key_exists(2, $outgoing_top_five_customers)){
//            $outgoing_doughnut_top_five_customers['third'] = $outgoing_top_five_customers[2];
//        }
//        else{
//            $outgoing_doughnut_top_five_customers['third']['name'] = '-';
//            $outgoing_doughnut_top_five_customers['third']['count'] = 0;
//        }
//        if(array_key_exists(3, $outgoing_top_five_customers)){
//            $outgoing_doughnut_top_five_customers['fourth'] = $outgoing_top_five_customers[3];
//        }
//        else{
//            $outgoing_doughnut_top_five_customers['fourth']['name'] = '-';
//            $outgoing_doughnut_top_five_customers['fourth']['count'] = 0;
//        }
//        if(array_key_exists(4, $outgoing_top_five_customers)){
//            $outgoing_doughnut_top_five_customers['fifth'] = $outgoing_top_five_customers[4];
//        }
//        else{
//            $outgoing_doughnut_top_five_customers['fifth']['name'] = '-';
//            $outgoing_doughnut_top_five_customers['fifth']['count'] = 0;
//        }
//        $outgoing_doughnut_top_five_customers['total'] = $outgoing_doughnut_top_five_customers['first']['count'] + $outgoing_doughnut_top_five_customers['second']['count'] + $outgoing_doughnut_top_five_customers['third']['count'] + $outgoing_doughnut_top_five_customers['fourth']['count'] + $outgoing_doughnut_top_five_customers['fifth']['count'];
//
//        $outgoing_bar_chart_shipments['one'] = OperationsOutgoingPickupRequestShipments::where('weight_range_id',1)->where('hub_id', $admin->default_hub_id)->where('booking_type_id', 1)->whereBetween('created_at',[$thirtyDays,$today])->count();
//        $outgoing_bar_chart_shipments['two'] = OperationsOutgoingPickupRequestShipments::where('weight_range_id',2)->where('hub_id', $admin->default_hub_id)->where('booking_type_id', 1)->whereBetween('created_at',[$thirtyDays,$today])->count();
//        $outgoing_bar_chart_shipments['three'] = OperationsOutgoingPickupRequestShipments::where('weight_range_id',3)->where('hub_id', $admin->default_hub_id)->where('booking_type_id', 1)->whereBetween('created_at',[$thirtyDays,$today])->count();
//        $outgoing_bar_chart_shipments['four'] = OperationsOutgoingPickupRequestShipments::where('weight_range_id',4)->where('hub_id', $admin->default_hub_id)->where('booking_type_id', 1)->whereBetween('created_at',[$thirtyDays,$today])->count();
//
//
//        if($riders_count == 0){
//            $outgoing_per_rider_loads = ceil(($outgoing_bar_chart_shipments['one'] + $outgoing_bar_chart_shipments['two'] + $outgoing_bar_chart_shipments['three'] + $outgoing_bar_chart_shipments['four']));
//        }
//        else{
//            $outgoing_per_rider_loads = ceil(($outgoing_bar_chart_shipments['one'] + $outgoing_bar_chart_shipments['two'] + $outgoing_bar_chart_shipments['three'] + $outgoing_bar_chart_shipments['four']) / $riders_count);
//        }
//        $outgoing_light_deliveries = ($outgoing_bar_chart_shipments['one'] + $outgoing_bar_chart_shipments['two']);
//        $outgoing_heavy_deliveries = ($outgoing_bar_chart_shipments['three'] + $outgoing_bar_chart_shipments['four']);
//
//        $outgoing_day_wise_growth_thirty = OperationsOutgoingPickupRequests::where('hub_id', $admin->default_hub_id)->where('booking_type_id', 1)->whereBetween('created_at',[$thirtyDays,$today])->sum('operations_outgoing_pickup_requests.shipments_count');
//        $outgoing_day_wise_growth_sixty = OperationsOutgoingPickupRequests::where('hub_id', $admin->default_hub_id)->where('booking_type_id', 1)->whereBetween('created_at',[$sixtyDays,$thirtyDays])->sum('operations_outgoing_pickup_requests.shipments_count');
//        if($outgoing_day_wise_growth_sixty == 0){
//            $outgoing_day_wise_growth_percentage = 0;
//        }
//        else{
//            $outgoing_day_wise_growth = ($outgoing_day_wise_growth_thirty - $outgoing_day_wise_growth_sixty) / $outgoing_day_wise_growth_sixty;
//            $outgoing_day_wise_growth_percentage = number_format($outgoing_day_wise_growth * 100, 1);
//        }
//        $operation_outgoing['per_rider_loads'] = $outgoing_per_rider_loads;
//        $operation_outgoing['day_wise_growth'] = $outgoing_day_wise_growth_percentage . '%';
//        $operation_outgoing['heavy_deliveries'] = $outgoing_heavy_deliveries;
//        $operation_outgoing['light_deliveries'] = $outgoing_light_deliveries;
//
//        $last_updated_at = OperationsForecastLastUpdatedTime::latest('created_at')->first();

//        return view('admin.dashboard')->with(['stats'=>$stats,'graph'=>$graph,'dates'=>$graph_dates,'cities'=>$cities,'shippers'=>$shippers, 'doughnut_chart_shipments_count' => $doughnut_chart_shipments_count, 'incoming_bar_chart_shipments' => $incoming_bar_chart_shipments, 'operation_dates' => $operation_dates, 'default_hub_id' => $admin->default_hub_id, 'operation_incoming' => $operation_incoming, 'service_types' => $service_type, 'operation_outgoing_pickups' => $operation_outgoing_pickups, 'outgoing_doughnut_top_five_customers' => $outgoing_doughnut_top_five_customers, 'outgoing_bar_chart_shipments' => $outgoing_bar_chart_shipments, 'operation_outgoing' => $operation_outgoing, 'last_updated_at' => $last_updated_at]);
//        return view('admin.dashboard')->with(['stats'=>$stats,'graph'=>$graph,'dates'=>$graph_dates,'cities'=>$cities,'shippers'=>$shippers,'sales'=>$sales]);
        return view('admin.simple_dashboard');
    }
    public function statistics_search(Request $request){
//        return $request;
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

        if(($destination_id != '') && ($shipper != '')){
            foreach ($dates as $this_date) {
                $comparison_date = $this_date;
                $graph['dates'][] = Carbon::parse($this_date)->format('d M');

                $booked = Shipment::whereDate('created_at', $comparison_date)->where(['user_id' => $shipper, 'consignee_city_id' => $destination_id, 'shipper_status_id' => 1]);
                $arrived = Shipment::whereDate('created_at', $comparison_date)->where(['user_id' => $shipper, 'consignee_city_id' => $destination_id])->where('shipper_status_id', 2);
                $in_transit = Shipment::whereDate('created_at', $comparison_date)->where(['user_id' => $shipper, 'consignee_city_id' => $destination_id])->where('shipper_status_id', 3);
                $canceled = Shipment::whereDate('created_at', $comparison_date)->where(['user_id' => $shipper, 'consignee_city_id' => $destination_id])->where('shipper_status_id', 17);
                $destination = Shipment::whereDate('created_at', $comparison_date)->where(['user_id' => $shipper, 'consignee_city_id' => $destination_id])->where('shipper_status_id',4);
                $out_for_delivery = Shipment::whereDate('created_at', $comparison_date)->where(['user_id' => $shipper, 'consignee_city_id' => $destination_id])->where('shipper_status_id',5);
                $return_confirm = Shipment::whereDate('created_at', $comparison_date)->where(['user_id' => $shipper, 'consignee_city_id' => $destination_id])->where('shipper_status_id',20);
                $return_delivered = Shipment::whereDate('created_at', $comparison_date)->where(['user_id' => $shipper, 'consignee_city_id' => $destination_id])->where('shipper_status_id',25);
                $pending_shipments = Shipment::whereDate('created_at', $comparison_date)->where(['user_id' => $shipper, 'consignee_city_id' => $destination_id])->whereIn('shipper_status_id', [6,7,8,9,13,15,18,51,52,56]);
                $pending_return = Shipment::whereDate('created_at', $comparison_date)->where(['user_id' => $shipper, 'consignee_city_id' => $destination_id])->whereIn('shipper_status_id', [21,22,23,24,26,27,28,29,57,60]);
                $confirmation_pending = Shipment::whereDate('created_at', $comparison_date)->where(['user_id' => $shipper, 'consignee_city_id' => $destination_id])->whereIn('shipper_status_id', [12,54,55]);
                $delivered = Shipment::whereDate('created_at', $comparison_date)->where(['user_id' => $shipper, 'consignee_city_id' => $destination_id])->whereIn('shipper_status_id', [14, 16, 30, 36, 37, 39, 40, 41, 47]);
//                $complaints_launched = CrmRequestStatusHistory::join('crm_requests as cr', 'cr.id', '=', 'crm_request_status_histories.crm_request_id')->where('crm_request_status_histories.status_id', 1)->where('cr.shipper_id', $shipper)->whereDate('crm_request_status_histories.created_at', $comparison_date);
//                $complaints_in_process = CrmRequestStatusHistory::join('crm_requests as cr', 'cr.id', '=', 'crm_request_status_histories.crm_request_id')->where('crm_request_status_histories.status_id', 2)->where('cr.shipper_id', $shipper)->whereDate('crm_request_status_histories.created_at', $comparison_date);
//                $complaints_closed = CrmRequestStatusHistory::join('crm_requests as cr', 'cr.id', '=', 'crm_request_status_histories.crm_request_id')->where('crm_request_status_histories.status_id', 4)->where('cr.shipper_id', $shipper)->whereDate('crm_request_status_histories.created_at', $comparison_date);
//                $complaints_rejected = CrmRequestStatusHistory::join('crm_requests as cr', 'cr.id', '=', 'crm_request_status_histories.crm_request_id')->where('crm_request_status_histories.status_id', 7)->where('cr.shipper_id', $shipper)->whereDate('crm_request_status_histories.created_at', $comparison_date);
                if (session('role_id') != 1) {
                    $booked = $booked->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $arrived = $arrived->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $in_transit = $in_transit->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $canceled = $canceled->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $delivered = $delivered->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $destination = $destination->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $out_for_delivery = $out_for_delivery->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $return_confirm = $return_confirm->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $return_delivered = $return_delivered->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $pending_shipments = $pending_shipments->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $confirmation_pending = $confirmation_pending->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $pending_return = $pending_return->where(function($query) {
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
        }else if(($destination_id == '') && ($shipper != '')){
            foreach ($dates as $this_date) {
                $comparison_date = $this_date;
                $graph['dates'][] = Carbon::parse($this_date)->format('d M');

                $booked = Shipment::whereDate('created_at', $comparison_date)->where('user_id', $shipper)->where('shipper_status_id',1);
                $arrived = Shipment::whereDate('created_at', $comparison_date)->where('user_id', $shipper)->where('shipper_status_id', 2);
                $in_transit = Shipment::whereDate('created_at', $comparison_date)->where('user_id', $shipper)->where('shipper_status_id', 3);
                $canceled = Shipment::whereDate('created_at', $comparison_date)->where('user_id', $shipper)->where('shipper_status_id', 17);
                $destination = Shipment::whereDate('created_at', $comparison_date)->where('user_id', $shipper)->where('shipper_status_id',4);
                $out_for_delivery = Shipment::whereDate('created_at', $comparison_date)->where('user_id', $shipper)->where('shipper_status_id',5);
                $return_confirm = Shipment::whereDate('created_at', $comparison_date)->where('user_id', $shipper)->where('shipper_status_id',20);
                $return_delivered = Shipment::whereDate('created_at', $comparison_date)->where('user_id', $shipper)->where('shipper_status_id',25);
                $pending_shipments = Shipment::whereDate('created_at', $comparison_date)->where('user_id', $shipper)->whereIn('shipper_status_id', [6,7,8,9,13,15,18,51,52,56]);
                $pending_return = Shipment::whereDate('created_at', $comparison_date)->where('user_id', $shipper)->whereIn('shipper_status_id', [21,22,23,24,26,27,28,29,57,60]);
                $confirmation_pending = Shipment::whereDate('created_at', $comparison_date)->where('user_id', $shipper)->whereIn('shipper_status_id', [12,54,55]);
                $delivered = Shipment::whereDate('created_at', $comparison_date)->where('user_id', $shipper)->whereIn('shipper_status_id', [14, 16, 30, 36, 37, 39, 40, 41, 47]);
//                $complaints_launched = CrmRequestStatusHistory::join('crm_requests as cr', 'cr.id', '=', 'crm_request_status_histories.crm_request_id')->where('crm_request_status_histories.status_id', 1)->where('cr.shipper_id', $shipper)->whereDate('crm_request_status_histories.created_at', $comparison_date);
//                $complaints_in_process = CrmRequestStatusHistory::join('crm_requests as cr', 'cr.id', '=', 'crm_request_status_histories.crm_request_id')->where('crm_request_status_histories.status_id', 2)->where('cr.shipper_id', $shipper)->whereDate('crm_request_status_histories.created_at', $comparison_date);
//                $complaints_closed = CrmRequestStatusHistory::join('crm_requests as cr', 'cr.id', '=', 'crm_request_status_histories.crm_request_id')->where('crm_request_status_histories.status_id', 4)->where('cr.shipper_id', $shipper)->whereDate('crm_request_status_histories.created_at', $comparison_date);
//                $complaints_rejected = CrmRequestStatusHistory::join('crm_requests as cr', 'cr.id', '=', 'crm_request_status_histories.crm_request_id')->where('crm_request_status_histories.status_id', 7)->where('cr.shipper_id', $shipper)->whereDate('crm_request_status_histories.created_at', $comparison_date);

                if (session('role_id') != 1) {
                    $booked = $booked->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $arrived = $arrived->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $in_transit = $in_transit->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $canceled = $canceled->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $delivered = $delivered->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $destination = $destination->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $out_for_delivery = $out_for_delivery->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $return_confirm = $return_confirm->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $return_delivered = $return_delivered->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $pending_shipments = $pending_shipments->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $confirmation_pending = $confirmation_pending->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $pending_return = $pending_return->where(function($query) {
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
        }else if(($destination_id != '') && ($shipper == '')){
            foreach ($dates as $this_date) {
                $comparison_date = $this_date;
                $graph['dates'][] = Carbon::parse($this_date)->format('d M');

                $booked = Shipment::whereDate('created_at', $comparison_date)->where(['consignee_city_id' => $destination_id])->where('shipper_status_id',1);
                $arrived = Shipment::whereDate('created_at', $comparison_date)->where(['consignee_city_id' => $destination_id])->where('shipper_status_id', 2);
                $in_transit = Shipment::whereDate('created_at', $comparison_date)->where(['consignee_city_id' => $destination_id])->where('shipper_status_id', 3);
                $canceled = Shipment::whereDate('created_at', $comparison_date)->where(['consignee_city_id' => $destination_id])->where('shipper_status_id', 17);
                $destination = Shipment::whereDate('created_at', $comparison_date)->where(['consignee_city_id' => $destination_id])->where('shipper_status_id',4);
                $out_for_delivery = Shipment::whereDate('created_at', $comparison_date)->where(['consignee_city_id' => $destination_id])->where('shipper_status_id',5);
                $return_confirm = Shipment::whereDate('created_at', $comparison_date)->where(['consignee_city_id' => $destination_id])->where('shipper_status_id',20);
                $return_delivered = Shipment::whereDate('created_at', $comparison_date)->where(['consignee_city_id' => $destination_id])->where('shipper_status_id',25);
                $pending_shipments = Shipment::whereDate('created_at', $comparison_date)->where(['consignee_city_id' => $destination_id])->whereIn('shipper_status_id', [6,7,8,9,13,15,18,51,52,56]);
                $pending_return = Shipment::whereDate('created_at', $comparison_date)->where(['consignee_city_id' => $destination_id])->whereIn('shipper_status_id', [21,22,23,24,26,27,28,29,57,60]);
                $confirmation_pending = Shipment::whereDate('created_at', $comparison_date)->where(['consignee_city_id' => $destination_id])->whereIn('shipper_status_id', [12,54,55]);
                $delivered = Shipment::whereDate('created_at', $comparison_date)->where(['consignee_city_id' => $destination_id])->whereIn('shipper_status_id', [14, 16, 30, 36, 37, 39, 40, 41, 47]);
//                $complaints_launched = CrmRequestStatusHistory::join('crm_requests as cr', 'cr.id', '=', 'crm_request_status_histories.crm_request_id')->where('crm_request_status_histories.status_id', 1)->whereDate('crm_request_status_histories.created_at', $comparison_date);
//                $complaints_in_process = CrmRequestStatusHistory::join('crm_requests as cr', 'cr.id', '=', 'crm_request_status_histories.crm_request_id')->where('crm_request_status_histories.status_id', 2)->whereDate('crm_request_status_histories.created_at', $comparison_date);
//                $complaints_closed = CrmRequestStatusHistory::join('crm_requests as cr', 'cr.id', '=', 'crm_request_status_histories.crm_request_id')->where('crm_request_status_histories.status_id', 4)->whereDate('crm_request_status_histories.created_at', $comparison_date);
//                $complaints_rejected = CrmRequestStatusHistory::join('crm_requests as cr', 'cr.id', '=', 'crm_request_status_histories.crm_request_id')->where('crm_request_status_histories.status_id', 7)->whereDate('crm_request_status_histories.created_at', $comparison_date);

                if (session('role_id') != 1) {
                    $booked = $booked->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $arrived = $arrived->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $in_transit = $in_transit->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $canceled = $canceled->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $delivered = $delivered->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $destination = $destination->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $out_for_delivery = $out_for_delivery->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $return_confirm = $return_confirm->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $return_delivered = $return_delivered->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $pending_shipments = $pending_shipments->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $confirmation_pending = $confirmation_pending->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $pending_return = $pending_return->where(function($query) {
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
        }else{
            foreach ($dates as $this_date) {
                $comparison_date = $this_date;
                $graph['dates'][] = Carbon::parse($this_date)->format('d M');

                $booked = Shipment::whereDate('created_at', $comparison_date)->where('shipper_status_id',1);
                $arrived = Shipment::whereDate('created_at', $comparison_date)->where('shipper_status_id', 2);
                $in_transit = Shipment::whereDate('created_at', $comparison_date)->where('shipper_status_id', 3);
                $canceled = Shipment::whereDate('created_at', $comparison_date)->where('shipper_status_id', 17);
                $destination = Shipment::whereDate('created_at', $comparison_date)->where('shipper_status_id',4);
                $out_for_delivery = Shipment::whereDate('created_at', $comparison_date)->where('shipper_status_id',5);
                $return_confirm = Shipment::whereDate('created_at', $comparison_date)->where('shipper_status_id',20);
                $return_delivered = Shipment::whereDate('created_at', $comparison_date)->where('shipper_status_id',25);
                $pending_shipments = Shipment::whereDate('created_at', $comparison_date)->whereIn('shipper_status_id', [6,7,8,9,13,15,18,51,52,56]);
                $pending_return = Shipment::whereDate('created_at', $comparison_date)->whereIn('shipper_status_id', [21,22,23,24,26,27,28,29,57,60]);
                $confirmation_pending = Shipment::whereDate('created_at', $comparison_date)->whereIn('shipper_status_id', [12,54,55]);
                $delivered = Shipment::whereDate('created_at', $comparison_date)->whereIn('shipper_status_id', [14, 16, 30, 36, 37, 39, 40, 41, 47]);
//                $complaints_launched = CrmRequestStatusHistory::join('crm_requests as cr', 'cr.id', '=', 'crm_request_status_histories.crm_request_id')->where('crm_request_status_histories.status_id', 1)->whereDate('crm_request_status_histories.created_at', $comparison_date);
//                $complaints_in_process = CrmRequestStatusHistory::join('crm_requests as cr', 'cr.id', '=', 'crm_request_status_histories.crm_request_id')->where('crm_request_status_histories.status_id', 2)->whereDate('crm_request_status_histories.created_at', $comparison_date);
//                $complaints_closed = CrmRequestStatusHistory::join('crm_requests as cr', 'cr.id', '=', 'crm_request_status_histories.crm_request_id')->where('crm_request_status_histories.status_id', 4)->whereDate('crm_request_status_histories.created_at', $comparison_date);
//                $complaints_rejected = CrmRequestStatusHistory::join('crm_requests as cr', 'cr.id', '=', 'crm_request_status_histories.crm_request_id')->where('crm_request_status_histories.status_id', 7)->whereDate('crm_request_status_histories.created_at', $comparison_date);

                if (session('role_id') != 1) {
                    $booked = $booked->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $arrived = $arrived->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $in_transit = $in_transit->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $canceled = $canceled->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $delivered = $delivered->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });

                    $destination = $destination->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $out_for_delivery = $out_for_delivery->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $return_confirm = $return_confirm->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $return_delivered = $return_delivered->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $pending_shipments = $pending_shipments->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $confirmation_pending = $confirmation_pending->where(function($query) {
                        $query->whereHas('pickup_address.city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        })->orWhereHas('consignee_city', function ($sub_query) {
                            $sub_query->whereIn('hub_id', session('hubs'));
                        });
                    });
                    $pending_return = $pending_return->where(function($query) {
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


        return response()->json(['status'=>1,'graph'=>$graph]);
    }


    public function operation_forecast_search(Request $request){
        if(($request->get('search_date_from') && $request->get('search_date_to'))){
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
        }
        else{
            $from = Carbon::now()->subDays(29);
            $to = Carbon::now();
        }
        if($request->get('search_hub')){
            $hub = $request->get('search_hub');
        }
        else{
            $admin = Admin::where('id', Auth::id())->first();
            $hub = $admin->default_hub_id;
        }
        if($request->get('search_service_type')){
            $service_type_id = $request->get('search_service_type');
        }
        else{
            $service_type_id = 1;
        }

        $today = Carbon::now()->endOfDay();
        $thirtyDays = Carbon::now()->subDays(29)->startOfDay();
        //incoming
        $doughnut_chart_shipments_count['booked'] = OperationForecast::where('shipper_status_id', 1)->where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at',[$from,$to])->sum('operation_forecasts.count');
        $doughnut_chart_shipments_count['arrived_at_origin'] = OperationForecast::where('shipper_status_id', 2)->where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at',[$from,$to])->sum('operation_forecasts.count');
        $doughnut_chart_shipments_count['in_transit'] = OperationForecast::where('shipper_status_id', 3)->where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at',[$from,$to])->sum('operation_forecasts.count');
        $doughnut_chart_shipments_count['arrived_at_destination'] = OperationForecast::where('shipper_status_id', 4)->where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at',[$from,$to])->sum('operation_forecasts.count');
        $doughnut_chart_shipments_count['not_attempted'] = OperationForecast::where('shipper_status_id', 7)->where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at',[$from,$to])->sum('operation_forecasts.count');
        $doughnut_chart_shipments_count['delivery_unsuccessful'] = OperationForecast::where('shipper_status_id', 8)->where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at',[$from,$to])->sum('operation_forecasts.count');
        $doughnut_chart_shipments_count['on_hold'] = OperationForecast::where('shipper_status_id', 9)->where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at',[$from,$to])->sum('operation_forecasts.count');
        $doughnut_chart_shipments_count['total'] = $doughnut_chart_shipments_count['booked'] + $doughnut_chart_shipments_count['arrived_at_origin'] + $doughnut_chart_shipments_count['in_transit'] + $doughnut_chart_shipments_count['arrived_at_destination'] + $doughnut_chart_shipments_count['not_attempted'] + $doughnut_chart_shipments_count['delivery_unsuccessful'] + $doughnut_chart_shipments_count['on_hold'];

        $incoming_bar_chart_shipments['one'] = OperationForecastShipments::where('weight_range_id',1)->where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at',[$from,$to])->count();
        $incoming_bar_chart_shipments['two'] = OperationForecastShipments::where('weight_range_id',2)->where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at',[$from,$to])->count();
        $incoming_bar_chart_shipments['three'] = OperationForecastShipments::where('weight_range_id',3)->where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at',[$from,$to])->count();
        $incoming_bar_chart_shipments['four'] = OperationForecastShipments::where('weight_range_id',4)->where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at',[$from,$to])->count();



        $riders_count = Rider::where('status', 1)->where('city_id', $hub)->count();
        $sixtyDays = Carbon::now()->subDays(58)->startOfDay();
        if($riders_count == 0){
            $per_rider_loads = ceil(($incoming_bar_chart_shipments['one'] + $incoming_bar_chart_shipments['two'] + $incoming_bar_chart_shipments['three'] + $incoming_bar_chart_shipments['four']));
        }
        else{
            $per_rider_loads = ceil(($incoming_bar_chart_shipments['one'] + $incoming_bar_chart_shipments['two'] + $incoming_bar_chart_shipments['three'] + $incoming_bar_chart_shipments['four']) / $riders_count);
        }
        $light_deliveries = ($incoming_bar_chart_shipments['one'] + $incoming_bar_chart_shipments['two']);
        $heavy_deliveries = ($incoming_bar_chart_shipments['three'] + $incoming_bar_chart_shipments['four']);

        $day_wise_growth_thirty = OperationForecast::where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at',[$thirtyDays,$today])->sum('operation_forecasts.count');
        $day_wise_growth_sixty = OperationForecast::where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at',[$sixtyDays,$thirtyDays])->sum('operation_forecasts.count');
        if($day_wise_growth_sixty == 0){
            $day_wise_growth_percentage = 0;
        }
        else{
            $day_wise_growth = ($day_wise_growth_thirty - $day_wise_growth_sixty) / $day_wise_growth_sixty;
            $day_wise_growth_percentage = number_format($day_wise_growth * 100, 1);
        }
        $operation_incoming['per_rider_loads'] = $per_rider_loads;
        $operation_incoming['day_wise_growth'] = $day_wise_growth_percentage . '%';
        $operation_incoming['heavy_deliveries'] = $heavy_deliveries;
        $operation_incoming['light_deliveries'] = $light_deliveries;

        //outgoing
        $operation_outgoing_pickups['no_of_shipments'] = OperationsOutgoingPickupRequests::where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at',[$thirtyDays,$today])->sum('operations_outgoing_pickup_requests.shipments_count');
        $operation_outgoing_pickups['pickups_count'] = OperationsOutgoingPickupRequests::select(DB::raw('count(operations_outgoing_pickup_requests.id) as count'))->where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at',[$thirtyDays,$today])->groupBy('operations_outgoing_pickup_requests.pickup_request_id')->get();
        $operation_outgoing_pickups['pickups'] = 0;
        foreach ($operation_outgoing_pickups['pickups_count'] as $pickups_count){
            $operation_outgoing_pickups['pickups'] = $operation_outgoing_pickups['pickups'] + $pickups_count->count;
        }

        $outgoing_top_five_customers = OperationsOutgoingTopCustomers::leftjoin('users as u', 'u.id', '=', 'operations_outgoing_top_customers.user_id')->select('u.name as name', DB::raw('(SELECT SUM(shipments_count) FROM operations_outgoing_top_customers AS ootc WHERE ootc.user_id = operations_outgoing_top_customers.user_id AND updated_at BETWEEN "'. $from .'" AND "'. $to .'") AS count'))
            ->whereBetween('operations_outgoing_top_customers.created_at',[$from,$to])
            ->orderBy('count', 'desc')
            ->groupBy('u.id')
            ->take(5)->get()->toArray();
        if(array_key_exists(0, $outgoing_top_five_customers)){
            $outgoing_doughnut_top_five_customers['first'] = $outgoing_top_five_customers[0];
        }
        else{
            $outgoing_doughnut_top_five_customers['first']['name'] = '-';
            $outgoing_doughnut_top_five_customers['first']['count'] = 0;
        }
        if(array_key_exists(1, $outgoing_top_five_customers)){
            $outgoing_doughnut_top_five_customers['second'] = $outgoing_top_five_customers[1];
        }
        else{
            $outgoing_doughnut_top_five_customers['second']['name'] = '-';
            $outgoing_doughnut_top_five_customers['second']['count'] = 0;
        }
        if(array_key_exists(2, $outgoing_top_five_customers)){
            $outgoing_doughnut_top_five_customers['third'] = $outgoing_top_five_customers[2];
        }
        else{
            $outgoing_doughnut_top_five_customers['third']['name'] = '-';
            $outgoing_doughnut_top_five_customers['third']['count'] = 0;
        }
        if(array_key_exists(3, $outgoing_top_five_customers)){
            $outgoing_doughnut_top_five_customers['fourth'] = $outgoing_top_five_customers[3];
        }
        else{
            $outgoing_doughnut_top_five_customers['fourth']['name'] = '-';
            $outgoing_doughnut_top_five_customers['fourth']['count'] = 0;
        }
        if(array_key_exists(4, $outgoing_top_five_customers)){
            $outgoing_doughnut_top_five_customers['fifth'] = $outgoing_top_five_customers[4];
        }
        else{
            $outgoing_doughnut_top_five_customers['fifth']['name'] = '-';
            $outgoing_doughnut_top_five_customers['fifth']['count'] = 0;
        }
        $outgoing_doughnut_top_five_customers['total'] = $outgoing_doughnut_top_five_customers['first']['count'] + $outgoing_doughnut_top_five_customers['second']['count'] + $outgoing_doughnut_top_five_customers['third']['count'] + $outgoing_doughnut_top_five_customers['fourth']['count'] + $outgoing_doughnut_top_five_customers['fifth']['count'];

        $outgoing_bar_chart_shipments['one'] = OperationsOutgoingPickupRequestShipments::where('weight_range_id',1)->where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at',[$from,$to])->count();
        $outgoing_bar_chart_shipments['two'] = OperationsOutgoingPickupRequestShipments::where('weight_range_id',2)->where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at',[$from,$to])->count();
        $outgoing_bar_chart_shipments['three'] = OperationsOutgoingPickupRequestShipments::where('weight_range_id',3)->where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at',[$from,$to])->count();
        $outgoing_bar_chart_shipments['four'] = OperationsOutgoingPickupRequestShipments::where('weight_range_id',4)->where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at',[$from,$to])->count();


        if($riders_count == 0){
            $outgoing_per_rider_loads = ceil(($outgoing_bar_chart_shipments['one'] + $outgoing_bar_chart_shipments['two'] + $outgoing_bar_chart_shipments['three'] + $outgoing_bar_chart_shipments['four']));
        }
        else{
            $outgoing_per_rider_loads = ceil(($outgoing_bar_chart_shipments['one'] + $outgoing_bar_chart_shipments['two'] + $outgoing_bar_chart_shipments['three'] + $outgoing_bar_chart_shipments['four']) / $riders_count);
        }
        $outgoing_light_deliveries = ($outgoing_bar_chart_shipments['one'] + $outgoing_bar_chart_shipments['two']);
        $outgoing_heavy_deliveries = ($outgoing_bar_chart_shipments['three'] + $outgoing_bar_chart_shipments['four']);

        $outgoing_day_wise_growth_thirty = OperationsOutgoingPickupRequests::where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at',[$thirtyDays,$today])->sum('operations_outgoing_pickup_requests.shipments_count');
        $outgoing_day_wise_growth_sixty = OperationsOutgoingPickupRequests::where('hub_id', $hub)->where('booking_type_id', $service_type_id)->whereBetween('created_at',[$sixtyDays,$thirtyDays])->sum('operations_outgoing_pickup_requests.shipments_count');
        if($outgoing_day_wise_growth_sixty == 0){
            $outgoing_day_wise_growth_percentage = 0;
        }
        else{
            $outgoing_day_wise_growth = ($outgoing_day_wise_growth_thirty - $outgoing_day_wise_growth_sixty) / $outgoing_day_wise_growth_sixty;
            $outgoing_day_wise_growth_percentage = number_format($outgoing_day_wise_growth * 100, 1);
        }
        $operation_outgoing['per_rider_loads'] = $outgoing_per_rider_loads;
        $operation_outgoing['day_wise_growth'] = $outgoing_day_wise_growth_percentage . '%';
        $operation_outgoing['heavy_deliveries'] = $outgoing_heavy_deliveries;
        $operation_outgoing['light_deliveries'] = $outgoing_light_deliveries;

        return response()->json(['status'=>1, 'doughnut_chart_shipments_count' => $doughnut_chart_shipments_count, 'incoming_bar_chart_shipments' => $incoming_bar_chart_shipments, 'operation_incoming' => $operation_incoming, 'operation_outgoing_pickups' => $operation_outgoing_pickups, 'outgoing_doughnut_top_five_customers' => $outgoing_doughnut_top_five_customers, 'outgoing_bar_chart_shipments' => $outgoing_bar_chart_shipments, 'operation_outgoing' => $operation_outgoing]);
    }
    public function incoming_list(Request $request){
        if($request->get('search_date_from') && $request->get('search_date_to')){
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
        }
        else{
            $from = Carbon::now()->subDays(29);
            $to = Carbon::now()->endOfDay();
        }
        if($request->get('search_hub')){
            $hub = $request->get('search_hub');
        }
        else{
            $admin = Admin::where('id', Auth::id())->first();
            $hub = $admin->default_hub_id;
        }
        if($request->get('search_service_type')){
            $service_type_id = $request->get('search_service_type');
        }
        else{
            $service_type_id = 1;
        }
        $operation_incoming = OperationForecast::leftjoin('shipment_status as ss', 'ss.id', '=', 'operation_forecasts.shipper_status_id')
            ->select('operation_forecasts.id as opfs_id', 'ss.id as shipper_status_id', 'ss.name as status', DB::raw('(SELECT SUM(count) FROM operation_forecasts AS opfs WHERE opfs.shipper_status_id = operation_forecasts.shipper_status_id AND opfs.hub_id = "' . $hub . '" AND opfs.booking_type_id = "' . $service_type_id . '" AND updated_at BETWEEN "'. $from .'" AND "'. $to .'") AS count'))
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
                    }
                    else if ($statuses->shipper_status_id == 2){
                        return 'statusOrigin';
                    }
                    else if ($statuses->shipper_status_id == 3){
                        return 'statusIntransit';
                    }
                    else if ($statuses->shipper_status_id == 4){
                        return 'statusDestination';
                    }
                    else if ($statuses->shipper_status_id == 7){
                        return 'statusNotattempted';
                    }
                    else if ($statuses->shipper_status_id == 8){
                        return 'statusDeliveryunsuccessful';
                    }
                    else if ($statuses->shipper_status_id == 9){
                        return 'statusOnhold';
                    }
                }
            ])
            ->editColumn('count_link', function ($shipments) use($from, $to, $service_type_id, $hub) {
                if($shipments->count > 0){
                    $route = route('admin.operation_forecasting.incoming.shipments_list');
                    return "<u><a href='{$route}/$from/$to/$service_type_id/$hub/$shipments->shipper_status_id' class='white' target='_blank'>$shipments->count</a></u>";
                }
                else{
                    return 0;
                }
            });
        return $datatable->make(true);
    }
    public function delivered_returned_list(Request $request){
        if($request->get('search_date_from') && $request->get('search_date_to')){
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
        }
        else{
            $from = Carbon::now()->subDays(29);
            $to = Carbon::now()->endOfDay();
        }
        if($request->get('search_hub')){
            $hub = $request->get('search_hub');
        }
        else{
            $admin = Admin::where('id', Auth::id())->first();
            $hub = $admin->default_hub_id;
        }
        if($request->get('search_service_type')){
            $service_type_id = $request->get('search_service_type');
        }
        else{
            $service_type_id = 1;
        }
        $operation_incoming_delivered_returned = OperationForecast::leftjoin('shipment_status as ss', 'ss.id', '=', 'operation_forecasts.shipper_status_id')
            ->select('operation_forecasts.id as opfs_id', 'ss.id as shipper_status_id', 'ss.name as status', DB::raw('(SELECT SUM(count) FROM operation_forecasts AS opfs WHERE opfs.shipper_status_id = operation_forecasts.shipper_status_id AND opfs.hub_id = "' . $hub . '" AND opfs.booking_type_id = "' . $service_type_id . '" AND updated_at BETWEEN "'. $from .'" AND "'. $to .'") AS count'))
            ->whereIn('shipper_status_id', [14, 25, 30, 31])
            ->where('operation_forecasts.hub_id', $hub)
            ->where('operation_forecasts.booking_type_id', $service_type_id)
            ->whereBetween('operation_forecasts.updated_at', [$from, $to])
            ->groupBy('shipper_status_id')
            ->orderBy('shipper_status_id', 'asc');
        $datatable = Datatables::of($operation_incoming_delivered_returned)
            ->editColumn('count_link', function ($shipments) use($from, $to, $service_type_id, $hub) {
                if($shipments->count > 0){
                    $route = route('admin.operation_forecasting.incoming.shipments_list');
                    return "<u><a href='{$route}/$from/$to/$service_type_id/$hub/$shipments->shipper_status_id' target='_blank'>$shipments->count</a></u>";
                }
                else{
                    return 0;
                }
            });
        return $datatable->make(true);
    }
    public function outgoing_top_customers_list(Request $request){
        if($request->get('search_date_from') && $request->get('search_date_to')){
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
        }
        else{
            $from = Carbon::now()->subDays(29);
            $to = Carbon::now()->endOfDay();
        }

        $outgoing_top_five_customers = OperationsOutgoingTopCustomers::leftjoin('users as u', 'u.id', '=', 'operations_outgoing_top_customers.user_id')->select('operations_outgoing_top_customers.id as id', 'u.name as name', 'u.id as user_id', DB::raw('(SELECT SUM(shipments_count) FROM operations_outgoing_top_customers AS ootc WHERE ootc.user_id = operations_outgoing_top_customers.user_id AND updated_at BETWEEN "'. $from .'" AND "'. $to .'") AS count'))
            ->whereBetween('operations_outgoing_top_customers.created_at',[$from,$to])
            ->orderBy('count', 'desc')
            ->groupBy('u.id')
            ->take(5);
        $datatable = Datatables::of($outgoing_top_five_customers)
            ->editColumn('count', function ($shipments) use($from, $to) {
                if($shipments->count > 0){
                    $route = route('admin.operation_forecasting.outgoing.shipments_list');
                    return "<u><a href='{$route}/$from/$to/$shipments->user_id' class='white' target='_blank'>$shipments->count</a></u>";
                }
                else{
                    return 0;
                }
            });
        return $datatable->make(true);
    }
    public function incoming_weight_range_list(Request $request){
        if($request->get('search_date_from') && $request->get('search_date_to')){
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
        }
        else{
            $from = Carbon::now()->subDays(29);
            $to = Carbon::now()->endOfDay();
        }

        if($request->get('search_hub')){
            $hub = $request->get('search_hub');
        }
        else{
            $admin = Admin::where('id', Auth::id())->first();
            $hub = $admin->default_hub_id;
        }
        if($request->get('search_service_type')){
            $service_type_id = $request->get('search_service_type');
        }
        else{
            $service_type_id = 1;
        }
        $operation_incoming = OperationForecastWeightRange::leftjoin('operation_forecast_shipments as ofss', 'ofss.weight_range_id', '=', 'operation_forecast_weight_ranges.id')
            ->select('operation_forecast_weight_ranges.name as range', DB::raw('(SELECT count(id) FROM operation_forecast_shipments AS ofs WHERE ofs.weight_range_id = ofss.weight_range_id AND ofs.hub_id = "' . $hub . '"  AND ofs.booking_type_id = "' . $service_type_id . '" AND ofs.updated_at BETWEEN "'. $from .'" AND "'. $to .'") AS count'))->groupBy('operation_forecast_weight_ranges.id');
        $datatable = Datatables::of($operation_incoming);
        return $datatable->make(true);
    }
    public function outgoing_weight_range_list(Request $request){
        if($request->get('search_date_from') && $request->get('search_date_to')){
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
        }
        else{
            $from = Carbon::now()->subDays(29);
            $to = Carbon::now()->endOfDay();
        }

        if($request->get('search_hub')){
            $hub = $request->get('search_hub');
        }
        else{
            $admin = Admin::where('id', Auth::id())->first();
            $hub = $admin->default_hub_id;
        }
        if($request->get('search_service_type')){
            $service_type_id = $request->get('search_service_type');
        }
        else{
            $service_type_id = 1;
        }
        $operation_outgoing = OperationForecastWeightRange::leftjoin('operations_outgoing_pickup_request_shipments as ooprs', 'ooprs.weight_range_id', '=', 'operation_forecast_weight_ranges.id')
            ->select('operation_forecast_weight_ranges.name as range', DB::raw('(SELECT count(id) FROM operations_outgoing_pickup_request_shipments AS oopr WHERE oopr.weight_range_id = ooprs.weight_range_id AND oopr.hub_id = "' . $hub . '"  AND oopr.booking_type_id = "' . $service_type_id . '" AND oopr.updated_at BETWEEN "'. $from .'" AND "'. $to .'") AS count'))->groupBy('operation_forecast_weight_ranges.id');
        $datatable = Datatables::of($operation_outgoing);
        return $datatable->make(true);
    }
    public function shipments_list($from, $to, $service_type_id , $hub, $shipper_status_id){
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
        if(!empty($operation_forecasting_shipments_list)){
            return view('admin.operation_forecasting.index')->with(['shipments'=>$operation_forecasting_shipments_list]);
        }
    }
    public function outgoing_shipments_list($from, $to, $user_id){
        $top_customers = OperationsOutgoingTopCustomers::select('operations_outgoing_top_customers.id as id')
            ->where('operations_outgoing_top_customers.user_id', $user_id)
            ->whereBetween('operations_outgoing_top_customers.updated_at', [$from, $to])
            ->pluck('id')->toArray();
        $operation_outgoing_top_customer = OperationsOutgoingTopCustomersShipments::leftjoin('shipments as s', 's.id', '=', 'operations_outgoing_top_customers_shipments.shipment_id')
            ->whereIn('operations_outgoing_top_customers_shipments.customer_id', $top_customers)
            ->whereBetween('operations_outgoing_top_customers_shipments.updated_at', [$from,$to])
            ->groupBy('s.id')
            ->get();
        if(!empty($operation_outgoing_top_customer)) {
            return view('admin.operation_forecasting.outgoing_index')->with('shipments', $operation_outgoing_top_customer);
        }
    }

    public function orderPending(){
        return view('admin.pending_booked_orders');
    }
    public function pendingAccountsList(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),1);
        $salesperson = Admin::join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.name','admins.id'])->where('status', 1)->where('ar.department_id',7)->get();
        $products = Product::select('id','product_name')->get();
        $segments = Segment::all();
        $sale_tier_types = Admin::where('admins.status',1)->where('role_id','!=',1)->get();
        $corporate_rate_types = CorporateRateType::all();
        $territories = Territory::select('id','name')->get();
        return view('admin.accounts.pending_accounts_list')->with(['products'=>$products,'segments'=>$segments,'sale_name'=>$salesperson ,'sale_tier_types' => $sale_tier_types, 'corporate_rate_types' => $corporate_rate_types,'territories' => $territories]);
    }
    public function activeAccountsList(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),2);
        $shippers = User::whereIn('status', [3, 4])->get();
        $salesperson = Admin::join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.name','admins.id'])->where('status', 1)->where('ar.department_id',7)->get();
        $products = Product::select('id','product_name')->get();
        $segments = Segment::all();
        $general_segments = SubCategorySegment::where('segment_id',1)->get();
        $ecom_segments = SubCategorySegment::where('segment_id',2)->get();
        $payment_cycles = PaymentCycle::all();
        $sale_tier_types = Admin::where('admins.status',1)->where('role_id','!=',1)->get();
        $territories = Territory::select('id','name')->get();
        return view('admin.accounts.active_accounts_list')->with(['products'=>$products,'sale_name'=>$salesperson, 'shippers' => $shippers, 'payment_cycles' => $payment_cycles, 'segments' => $segments,'ecom_segments' => $ecom_segments, 'general_segments' => $general_segments ,'sale_tier_types' => $sale_tier_types,'territories' => $territories]);

    }
    public function blockAccountsList(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),275);
        $salesperson = Admin::join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.name','admins.id'])->where('ar.department_id',7)->get();
        $sale_tier_types = Admin::where('admins.status',1)->where('role_id','!=',1)->get();
        return view('admin.accounts.block_accounts_list')->with(['sale_name'=>$salesperson,'sale_tier_types' => $sale_tier_types]);
    }
    public function UserStatus(Request $request){

        $id = $request->shid; //shipper id
        $status = $request->status;
        $user = User::find($id);
        if(!$user){
            return back()->with('danger', 'User not found.');
        }
        if($status == 'activate'){

            if($user->status == 2){
                $now = Carbon::now();
                $action = User::where('id',$user->id)->update(['status'=>3,'account_activated_by'=>Auth::id(),'activated_at'=>$now ,'reactivated_at'=>$now ]);
                if($user->lead_id != null){
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
                if($action == 1){
                    NotificationsController::send(1, $user->id);

                    return redirect()->route('admin.accounts.active')->with('success', 'User is activated.');
                }else{
                    return back()->with('danger', 'There is some problem please try again.');
                }
            }else{
                return back()->with('danger', 'This user\'s rates are not set.');
            }
        }

    }
    public function tagSubmit(Request $request){
        $tag_id = $request->admin_id;
        $shipper_id = $request->shipper_id;
        $user = User::find($shipper_id);
        $shipper_hub_id = $user->city->hub_id;
        $sale_persons = array();
        $old_sale_person = '';
        $new_sale_person = '';
        if(AdminHub::where('admin_id',$tag_id)->where('hub_id',$shipper_hub_id)->exists()){
            if(!SalePersonTag::where(['admin_id'=>$tag_id,'user_id'=>$shipper_id,'status'=>0])->exists()){
                $old_sale_person = SalePersonTag::where('user_id', $shipper_id)->where('status', 0)->latest()->first();
                $old_sale_person_date = $old_sale_person->created_at;
                if($old_sale_person){
                    $old_sale_person = $old_sale_person->sales_person;
                }
                else{
                    $old_sale_person = null;
                }
                $new_sale_person = Admin::find($tag_id);
                $shipper_data =SalePersonTag::where('user_id',$shipper_id)->where('status',0)->get();
                if($shipper_data->count() > 0){
                    SalePersonTag::where('user_id',$shipper_id)->where('status',0)->update(['status' => 1]);
                }
                $shipper = User::find($shipper_id);
                $sale_person_tag = new SalePersonTag();
                $sale_person_tag->admin_id=$tag_id;
                $sale_person_tag->user_id=$shipper_id;
                $sale_person_tag->save();
                $sale_persons[$shipper_id] = ['old_sale_person' => $old_sale_person, 'new_sale_person' => $new_sale_person, 'old_sale_person_date' => $old_sale_person_date ];
                NotificationsController::send(81, $sale_persons, Auth::id());
                NotificationsController::send(119, $sale_persons, Auth::id());


            }
            else{
                return ['status'=>0,'error'=>"Shipper is already tagged to  Sales Person!"];
            }

            return ['status'=>1,'success'=>"Shipper is tagged to Sales Person!"];
        }
        else{
            return ['status'=>0,'error'=>"Shipper is not tagged to Sales Person!"];

        }

    }
    public function tagSubmitBulk(Request $request){
        $tag_id = $request->admin_id;
        $shipper_ids = $request->shipper_ids;
        $sale_persons = array();
        if($shipper_ids){
            foreach ($shipper_ids as $shipper_id){
                $old_sale_person_data = '';
                $old_sale_person_date = '';
                $user = User::find($shipper_id);
                $shipper_hub_id = $user->city->hub_id;
                if(AdminHub::where('admin_id',$tag_id)->where('hub_id',$shipper_hub_id)->exists()) {
                    if(!SalePersonTag::where(['admin_id' => $tag_id, 'user_id' => $shipper_id,'status' => 0])->exists()) {
                        $old_sale_person = SalePersonTag::where('user_id', $shipper_id)->where('status', 0)->latest()->first();
                        if($old_sale_person){
                            $old_sale_person_date = $old_sale_person->created_at;
                            $old_sale_person->status = 1;
                            $old_sale_person->save();
                            $old_sale_person_data = $old_sale_person->sales_person;
                        }
                        $new_sale_person = Admin::find($tag_id);

                        $sale_person_tag = new SalePersonTag();
                        $sale_person_tag->admin_id = $tag_id;
                        $sale_person_tag->user_id = $shipper_id;
                        $sale_person_tag->status = 0;
                        $sale_person_tag->save();
                        $sale_persons[$shipper_id] = ['old_sale_person' => $old_sale_person_data, 'new_sale_person' => $new_sale_person, 'old_sale_person_date' => $old_sale_person_date];
                    }
                }
            }

            NotificationsController::send(81,$sale_persons ,Auth::id());
            NotificationsController::send(119,$sale_persons ,Auth::id());
            return ['status'=>1,'success'=>"Shipper is tagged to Sales Person!"];
        }
        else{
            return ['status'=>0,'error'=>"Shipper is not tagged to Sales Person!"];
        }
    }

    public function rejectReasonSubmit(Request $request)
    {
        $shipper_id = $request->shipper_id;
        $reject_reason = $request->rejected_reason;
        $user = User::find($shipper_id);
        if($user->status != 3){
            $user->status = 5;
        }
        if($user->rate_type_id_status == 1){
            $user->rate_type_id_status = 2;
        }
        $user->rejected_reason = $reject_reason;
        $user->rate_status = 2;
        $user->rates_rejected_by = Auth::id();
        $user->rates_rejected_at = Carbon::now();
        $user->save();
        NotificationsController::send(64, $shipper_id );
        return ['success' => 'Rates has been rejected!'];
    }
    public function UserStatusBlock(Request $request){
        $user_id = $request->id;
        $reason = $request->reason;
        $status = $request->status;
        $user = User::where('id',$user_id);
        if($user->exists()){
            $user = $user->first();
            if($status == 'block'){
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
                }
                else{
                    $pending_payment_shipper = PendingPayment::where('user_id', $user_id);
                    if ($pending_payment_shipper->exists()) {
                        $pending_payment_shipper = $pending_payment_shipper->first();
                        $payable = PendingPaymentShipment::where('pending_payment_id', $pending_payment_shipper->id)->sum('payable');
                        if ($payable < 0) {
                            $negative_balance_status = true;
                        }
                    }
                }
                if($negative_balance_status == false){
                    if($user->blacklist == 0){
                        $user->blacklist = 1;
                        $user->blacklist_reason = $reason;
                        $user->save();
                        return response()->json(['status'=>1,'success'=>"User added to the blacklist!"]);
                    }else{
                        return response()->json(['status'=>0,'error'=>"User is already in blacklist!"]);
                    }
                }
                else{
                    return response()->json(['status'=>0,'error'=>"Negative balance found!"]);
                }
            }else if($status == 'unblock'){
                if($user->blacklist == 1){
                    $user->blacklist = 0;
                    $user->save();
                    return response()->json(['status'=>1,'success'=>"User removed from the blacklist!"]);
                }else{
                    return response()->json(['status'=>0,'error'=>"User is not in the blacklist!"]);
                }
            }

        }else{
            return response()->json(['status'=>0,'error'=>"User doesn\'t exist!"]);
        }
    }
    public function UserStatusChange(Request $request){
        $user_id = $request->id;
        $status = $request->status;
        $user = User::where('id',$user_id);
        if($user->exists()){
            $user = $user->first();
            if($status == 'enable'){
                if($user->status == 4){
                    $user->status = 3;
                    $user->disable_remarks = null;
                    $user->reactivated_at = Carbon::now();
                    $user->disable_at = null;

                    $user->save();
                    return response()->json(['status'=>1,'success'=>"User is now enabled!"]);
                }else{
                    return response()->json(['status'=>0,'error'=>"User is already enabled!"]);
                }
            }else if($status == 'disable'){
                if($user->status == 3){
                    $user->disable_at = Carbon::now();

                    $user->status = 4;
                    $user->save();
                    return response()->json(['status'=>1,'success'=>"User is now disabled!"]);
                }else{
                    return response()->json(['status'=>0,'error'=>"User is already disabled!"]);

                }
            }
        }else{
            return response()->json(['status'=>0,'error'=>"User doesn\'t exist!"]);
        }
    }



    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function viewBankInfo($id){
        $user = User::find($id);
        $banks = $user->bank;
//        return $banks;
        $returnHTML = view('admin/components/bank')->with(['banks'=>$banks,'user'=>$user])->render();
        return response()->json($returnHTML);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function viewShippingInfo($id){
        $user = User::find($id);
        $shipping = $user->shipping()->where('hidden',0)->get();
        $returnHTML = view('admin.components.shipping')->with(['shipping'=>$shipping,'user'=>$user])->render();
        return response()->json($returnHTML);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function viewShipperRates($id){
        $user = User::find($id);
        $shipping = $user->shipping;
        $returnHTML = view('admin.components.shipping')->with(['shipping'=>$shipping,'user'=>$user])->render();
        return response()->json($returnHTML);
    }

    public function addRatesView($id){
        $user = User::find($id);
        if(!RateStatus::where('user_id', $user->id)->exists()) {
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
            $admin_users = Admin::leftjoin('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.id', 'admins.name', 'ar.department_id'])->where('admins.status', 1)->get();
            $users = array();
            $sales = array();
            $all_users = array();
            foreach ($admin_users as $u) {
                if ($u->department_id != 7) {
                    $users[] = array('id' => $u->id, 'text' => $u->name);
                } else {
                    $sales[] = array('id' => $u->id, 'text' => $u->name);
                }
            }
            $all_users['results'][0]['text'] = 'Sales';
            $all_users['results'][0]['children'] = $sales;
            $all_users['results'][1]['text'] = 'Admins';
            $all_users['results'][1]['children'] = $users;
            $all_users['pagination']['more'] = true;

            $cities = City::where('status', 1)->where('business_category_id', 1)->select('id', 'name')->get();
            return view('admin.accounts.add_rates')->with(['shipper' => $user, 'weight' => $weight, 'shippingType' => $bookingType, 'cashHandling' => $cash, 'insuranceCharges' => $insurance, 'returnCharges' => $return, 'fuelCharges' => $fuel, 'sale_person' => $sale_person, 'packaging_material_types' => $packaging_material_types, 'packaging_material_type_sizes' => $packaging_sizes, 'invoicing_cycles' => $invoicing_cycles, 'storage_types' => $storage_types, 'on' => $on, 'ol' => $ol, 'det' => $det, 'same_day' => $same_day, 'commission_percentage' => $commission_percentage, 'sales_tiers' => $sales_tiers, 'users' => $all_users, 'cities' => $cities]);
        }
        return redirect()->back()->with('error','User rates not found!');
    }
//    public function salesTag(Request $name)
//    {
//        $salesperson = admins::join('admin_department as ad', 'admins.role_id', '=', 'ad.role_id')
//            ->join('admin_roles as ar', 'ar.department_id', '=', 7)->get();
//    }

    public function viewRates($id,$date = null){

            $user = User::find($id);
            $dws_weight = PendingDwsWeightCharges::where('user_id',$id);

            $on_dws_charges = null;
            $ol_dws_charges = null;
            $detain_dws_charges = null;
            $sameday_dws_charges = null;
    
            if($dws_weight->exists()){
                $dws_weight = $dws_weight->get();
                foreach ($dws_weight as $value) {
                    if($value->shipping_mode_id == 1){
                        $on_dws_charges = $value->dws_weight_status;
    
                    }elseif($value->shipping_mode_id == 2){
                        $ol_dws_charges = $value->dws_weight_status;
    
                    }elseif($value->shipping_mode_id == 3){
                        $detain_dws_charges = $value->dws_weight_status;
                        
                    }elseif($value->shipping_mode_id == 4){
                        $sameday_dws_charges = $value->dws_weight_status;
                        
                    }
                }
            }
            if($date == null){
                $cash = CashHandlingCharge::all()->where('user_id',$id)->groupBy('shipping_mode_id');
                $insurance = InsuranceCharge::all()->where('user_id',$id)->groupBy('shipping_mode_id');
                $return = ReturnCharge::all()->where('user_id',$id)->groupBy('shipping_mode_id');
                $fuel = FuelSurcharge::all()->where('user_id',$id)->groupBy('shipping_mode_id');
                $weight = WeightCharge::all()->where('user_id',$id)->groupBy('shipping_mode_id');
                $bookingType = BookingTypeCharges::all()->where('user_id',$id)->groupBy('shipping_mode_id');
                $switches = RateStatus::all()->where('user_id',$id)->groupBy('shipping_mode_id');
                $discount = DiscountCharge::all()->where('user_id',$id)->groupBy('shipping_mode_id');
                $rate_origin_hubs = RateOriginHub::all()->where('user_id',$id)->groupBy('shipping_mode_id');
                $rate_destination_hubs = RateDestinationHub::all()->where('user_id',$id)->groupBy('shipping_mode_id');

            }
            else{
                $tomorrow = Carbon::parse($date)->addDay(1);
                $cash = HistoryCashHandlingCharge::all()->where('user_id', $id)->where('created_at', '>=', $date)->where('created_at', '<', $tomorrow)->groupBy('shipping_mode_id');
                $insurance = HistoryInsuranceCharge::all()->where('user_id', $id)->where('created_at', '>=', $date)->where('created_at', '<', $tomorrow)->groupBy('shipping_mode_id');
                $return = HistoryReturnCharge::all()->where('user_id', $id)->where('created_at', '>=', $date)->where('created_at', '<', $tomorrow)->groupBy('shipping_mode_id');
                $fuel = HistoryFuelSurcharge::all()->where('user_id', $id)->where('created_at', '>=', $date)->where('created_at', '<', $tomorrow)->groupBy('shipping_mode_id');
                $weight = HistoryWeightCharge::all()->where('user_id', $id)->where('created_at', '>=', $date)->where('created_at', '<', $tomorrow)->groupBy('shipping_mode_id');
                $bookingType = HistoryBookingTypeCharges::all()->where('user_id', $id)->where('created_at', '>=', $date)->where('created_at', '<', $tomorrow)->groupBy('shipping_mode_id');
                $switches = HistoryRateStatus::all()->where('user_id', $id)->where('created_at', '>=', $date)->where('created_at', '<', $tomorrow)->groupBy('shipping_mode_id');
                $discount = HistoryDiscountCharge::all()->where('user_id', $id)->where('created_at', '>=', $date)->where('created_at', '<', $tomorrow)->groupBy('shipping_mode_id');
                $rate_origin_hubs = HistoryRateOriginHub::all()->where('user_id',$id)->where('created_at', '>=', $date)->where('created_at', '<', $tomorrow)->groupBy('shipping_mode_id');
                $rate_destination_hubs = HistoryRateDestinationHub::all()->where('user_id',$id)->where('created_at', '>=', $date)->where('created_at', '<', $tomorrow)->groupBy('shipping_mode_id');

            }


            $sale_person = SalePersonTag::where('user_id',$id)->where('status', 0)->first();
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
            $rate_remarks = RateRemark::where('user_id', $id)->orderBy('created_at','desc')->get();
            $packaging_charges = array();
            if(count($packaging) > 0){

                foreach($packaging as $charge){
                    $packaging_charges[$charge->type_id][] = $charge;
                }
            }

            $sales_commission = SalesCommission::where('shipper_id', $id)->first();

            $cities = City::where('status', 1)->where('business_category_id', 1)->select('id', 'name')->get();

            $overnight_origins = [];
            $overland_origins = [];
            $detain_origins = [];
            $sameday_origins = [];
            if($rate_origin_hubs || count($rate_origin_hubs) > 0){
                foreach($rate_origin_hubs as $index => $origin){

                    if($index == 1){
                        foreach($origin as $origin_data){
                            $overnight_origins[] = $origin_data->city_id;
                        }
                    }
                    else if($index == 2){
                        foreach($origin as $origin_data){
                            $overland_origins[] = $origin_data->city_id;
                        }
                    }
                    else if($index == 3){
                        foreach($origin as $origin_data){
                            $detain_origins[] = $origin_data->city_id;
                        }
                    }
                    else if($index == 4){
                        foreach($origin as $origin_data){
                            $sameday_origins[] = $origin_data->city_id;
                        }
                    }

                }
            }
            $overnight_destinations = [];
            $overland_destinations = [];
            $detain_destinations = [];
            $sameday_destinations = [];
            if($rate_destination_hubs || count($rate_destination_hubs) > 0){
                foreach($rate_destination_hubs as $index => $destination){

                    if($index == 1){
                        foreach($destination as $destination_data){
                            $overnight_destinations[] = $destination_data->city_id;
                        }
                    }
                    else if($index == 2){
                        foreach($destination as $destination_data){
                            $overland_destinations[] = $destination_data->city_id;
                        }
                    }
                    else if($index == 3){
                        foreach($destination as $destination_data){
                            $detain_destinations[] = $destination_data->city_id;
                        }
                    }
                    else if($index == 4){
                        foreach($destination as $destination_data){
                            $sameday_destinations[] = $destination_data->city_id;
                        }
                    }

                }
            }



            if(session('department_id') == 7){
                if($sale_person['admin_id'] == Auth::id() || in_array(session('id'), session('sale_users_bypass')) || in_array($id, session('tagged_shippers'))){
                    return view('admin.accounts.view_rates')->with(['sameday_dws_charges' => $sameday_dws_charges,'detain_dws_charges' => $detain_dws_charges,'ol_dws_charges' => $ol_dws_charges,'on_dws_charges' => $on_dws_charges,'shipper'=>$user,'switches'=>$switches,'weight'=>$weight,'shippingType'=>$bookingType,'cashHandling'=>$cash,'insuranceCharges'=>$insurance,'returnCharges'=>$return,'fuelCharges'=>$fuel,'packagingCharges'=>$packaging,'discountCharges'=>$discount, 'sale_person' => $sale_person, 'packaging_material_types' => $packaging_material_types,  'packaging_type_ids' => $packaging_type_ids, 'packaging_charges' => $packaging_charges, 'wms_user_info' => $wms_user_info, 'wms_product_charges' => $wms_product_charges, 'wms_square_foot_charges' => $wms_square_foot_charges, 'wms_packing_charges' => $wms_packing_charges, 'wms_labelling_charges' => $wms_labelling_charges, 'wms_storage_charges' => $wms_storage_charges, 'invoicing_cycles' => $invoicing_cycles, 'storage_types' => $storage_types,'rate_remarks' => $rate_remarks, 'sales_commission' => $sales_commission, 'overnight_origins' => $overnight_origins, 'overland_origins' => $overland_origins, 'detain_origins' => $detain_origins, 'sameday_origins' => $sameday_origins,'overnight_destinations' => $overnight_destinations, 'overland_destinations' => $overland_destinations, 'detain_destinations' => $detain_destinations, 'sameday_destinations' => $sameday_destinations, 'cities' => $cities]);
                }
                else{
                    return view('admin.access_denied');
                }
            }
            else{
                return view('admin.accounts.view_rates')->with(['sameday_dws_charges' => $sameday_dws_charges,'detain_dws_charges' => $detain_dws_charges,'ol_dws_charges' => $ol_dws_charges,'on_dws_charges' => $on_dws_charges,'shipper'=>$user,'switches'=>$switches,'weight'=>$weight,'shippingType'=>$bookingType,'cashHandling'=>$cash,'insuranceCharges'=>$insurance,'returnCharges'=>$return,'fuelCharges'=>$fuel,'packagingCharges'=>$packaging,'discountCharges'=>$discount, 'sale_person' => $sale_person, 'packaging_material_types' => $packaging_material_types,  'packaging_type_ids' => $packaging_type_ids, 'packaging_charges' => $packaging_charges, 'wms_user_info' => $wms_user_info, 'wms_product_charges' => $wms_product_charges, 'wms_square_foot_charges' => $wms_square_foot_charges, 'wms_packing_charges' => $wms_packing_charges, 'wms_labelling_charges' => $wms_labelling_charges, 'wms_storage_charges' => $wms_storage_charges, 'invoicing_cycles' => $invoicing_cycles, 'storage_types' => $storage_types, 'rate_remarks' => $rate_remarks,'sales_commission' => $sales_commission, 'overnight_origins' => $overnight_origins, 'overland_origins' => $overland_origins, 'detain_origins' => $detain_origins, 'sameday_origins' => $sameday_origins,'overnight_destinations' => $overnight_destinations, 'overland_destinations' => $overland_destinations, 'detain_destinations' => $detain_destinations, 'sameday_destinations' => $sameday_destinations, 'cities' => $cities]);
            }
    }


    public function editRatesView($id){
        $dws_weight = PendingDwsWeightCharges::where('user_id',$id);

        $on_dws_charges = null;
        $ol_dws_charges = null;
        $detain_dws_charges = null;
        $sameday_dws_charges = null;

        if($dws_weight->exists()){
            $dws_weight = $dws_weight->get();
            foreach ($dws_weight as $value) {
                if($value->shipping_mode_id == 1){
                    $on_dws_charges = $value->dws_weight_status;

                }elseif($value->shipping_mode_id == 2){
                    $ol_dws_charges = $value->dws_weight_status;

                }elseif($value->shipping_mode_id == 3){
                    $detain_dws_charges = $value->dws_weight_status;
                    
                }elseif($value->shipping_mode_id == 4){
                    $sameday_dws_charges = $value->dws_weight_status;
                    
                }
            }
        }
        $user = User::find($id);
        $sale_person = SalePersonTag::where('user_id',$id)->where('status', 0)->first();
        $minimum_chargeable_weights = MinimumChargeableWeightSetting::get();
        $on = null;
        $ol = null;
        $det = null;
        $same_day = null;
        foreach($minimum_chargeable_weights as $minimum_chargeable_weight){
            if($minimum_chargeable_weight->shipping_mode_id == 1){
                $on = $minimum_chargeable_weight->weight;
            }
            elseif($minimum_chargeable_weight->shipping_mode_id == 2){
                $ol = $minimum_chargeable_weight->weight;
            }
            elseif($minimum_chargeable_weight->shipping_mode_id == 3){
                $det = $minimum_chargeable_weight->weight;
            }
            else{
                $same_day = $minimum_chargeable_weight->weight;
            }
        }

        $commission_percentage = '';
        $settings = GlobalSettings::where('type', 'commission_percentage');
        if($settings->exists()){
            $settings = $settings->first();
            $commission_percentage = $settings->text;
        }
        $sales_tiers = SalesTier::where('status', 1)->get(['id', 'tier_name', 'tier_type', 'commission','sales_status']);
        $admin_users = Admin::leftjoin('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.id', 'admins.name','ar.department_id'])->where('admins.status', 1)->get();
        $users = array();
        $sales = array();
        $all_users = array();
        foreach ($admin_users as $admin_user){

            if($admin_user->department_id != 7){
                $users[] = array('id' => $admin_user->id, 'text' => $admin_user->name);
            }else{
                $sales[] = array('id' => $admin_user->id, 'text' => $admin_user->name);
            }
        }
        $all_users['results'][0]['text'] = 'Sales';
        $all_users['results'][0]['children'] = $sales;
        $all_users['results'][1]['text'] = 'Admins';
        $all_users['results'][1]['children'] = $users;
        $all_users['pagination']['more'] = true;

        $existing_commission_array = array();
        $sale_commission = SalesCommission::where('shipper_id', $user->id)->first();
        if($sale_commission){
            $sale_commission_users = SalesCommissionUser::where('sales_commission_id', $sale_commission->id)->get();
            if($sale_commission_users){
                foreach($sale_commission_users as $index => $sale_commission_user){
                    $sales_tier = SalesTier::find($sale_commission_user->tier_id);
                    if($sales_tier){
                        $existing_commission_array[$index]['sales_commission_id'] = $sale_commission_user->sales_commission_id;
                        $existing_commission_array[$index]['tier_type_id'] = $sales_tier->tier_type;
                        $existing_commission_array[$index]['tier_id'] = $sale_commission_user->tier_id;
                        $existing_commission_array[$index]['tier_name'] = $sales_tier->tier_name;
                        if($sales_tier->tier_type == 1){
                            $com_admin = Admin::find($sale_commission_user->user_id);
                            $existing_commission_array[$index]['user_name'] = $com_admin->name;
                            $existing_commission_array[$index]['user_id'] = $com_admin->id;
                        }else if($sales_tier->tier_type == 2){
                            $external_user = SalesCommissionExternalUser::find($sale_commission_user->user_id);
                            $existing_commission_array[$index]['user_name'] = $external_user->name;
                        }
                        $existing_commission_array[$index]['commission'] = $sale_commission_user->commission;
                    }
                }
            }
        }

        if ((($user['rate_status']>=0) && ($user['status']==1 || $user['status']==5)) || (($user['rate_status']==0) && $user['status']==3)) {
            $switches = RateStatus::all()->where('user_id', $id)->groupBy('shipping_mode_id');

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
            $rate_status = $user['rate_status'];
            $packaging_material_types = PackagingMaterialTypes::with(['sizes'])->where('status', 1)->get();
            $rate_remarks = RateRemark::where('user_id', $id)->orderBy('created_at','desc')->get();
            $packaging_sizes = array();
            if(count($packaging_material_types) > 0){

                foreach($packaging_material_types as $type){
                    $packaging_sizes[$type->id] = $type->sizes;
                }
            }

            $packaging_charges = array();
            if(count($packaging) > 0){

                foreach($packaging as $charge){
                    $packaging_charges[$charge->type_id][] = $charge;
                }
            }

            $existing = 0;

            $cities = City::where('status', 1)->where('business_category_id', 1)->select('id', 'name')->get();

            $rate_origin_hubs = RateOriginHub::all()->where('user_id',$id)->groupBy('shipping_mode_id');
            $rate_destination_hubs = RateDestinationHub::all()->where('user_id',$id)->groupBy('shipping_mode_id');

            $overnight_origins = [];
            $overland_origins = [];
            $detain_origins = [];
            $sameday_origins = [];
            if($rate_origin_hubs || count($rate_origin_hubs) > 0){
                foreach($rate_origin_hubs as $index => $origin){

                    if($index == 1){
                        foreach($origin as $origin_data){
                            $overnight_origins[] = $origin_data->city_id;
                        }
                    }
                    else if($index == 2){
                        foreach($origin as $origin_data){
                            $overland_origins[] = $origin_data->city_id;
                        }
                    }
                    else if($index == 3){
                        foreach($origin as $origin_data){
                            $detain_origins[] = $origin_data->city_id;
                        }
                    }
                    else if($index == 4){
                        foreach($origin as $origin_data){
                            $sameday_origins[] = $origin_data->city_id;
                        }
                    }

                }
            }
            $overnight_destinations = [];
            $overland_destinations = [];
            $detain_destinations = [];
            $sameday_destinations = [];
            if($rate_destination_hubs || count($rate_destination_hubs) > 0){
                foreach($rate_destination_hubs as $index => $destination){

                    if($index == 1){
                        foreach($destination as $destination_data){
                            $overnight_destinations[] = $destination_data->city_id;
                        }
                    }
                    else if($index == 2){
                        foreach($destination as $destination_data){
                            $overland_destinations[] = $destination_data->city_id;
                        }
                    }
                    else if($index == 3){
                        foreach($destination as $destination_data){
                            $detain_destinations[] = $destination_data->city_id;
                        }
                    }
                    else if($index == 4){
                        foreach($destination as $destination_data){
                            $sameday_destinations[] = $destination_data->city_id;
                        }
                    }

                }
            }
            if(session('department_id') == 7){
                if($sale_person['admin_id'] == Auth::id() || in_array(session('id'), session('sale_users_bypass')) || in_array($id, session('tagged_shippers'))){
                    return view('admin.accounts.edit_rates')->with(['sameday_dws_charges' => $sameday_dws_charges,'detain_dws_charges' => $detain_dws_charges,'ol_dws_charges' => $ol_dws_charges,'on_dws_charges' => $on_dws_charges,'shipper'=>$user,'switches'=>$switches,'weight'=>$weight,'shippingType'=>$bookingType,'cashHandling'=>$cash,'insuranceCharges'=>$insurance,'returnCharges'=>$return,'fuelCharges'=>$fuel, 'discountCharges'=>$discount, 'rate_status'=>$rate_status, 'sale_person' => $sale_person, 'packaging_material_types' => $packaging_material_types, 'packaging_type_ids' => $packaging_type_ids, 'packaging_charges' => $packaging_charges, 'wms_user_info' => $wms_user_info, 'wms_product_charges' => $wms_product_charges, 'wms_square_foot_charges' => $wms_square_foot_charges, 'wms_packing_charges' => $wms_packing_charges, 'wms_labelling_charges' => $wms_labelling_charges, 'wms_storage_charges' => $wms_storage_charges, 'invoicing_cycles' => $invoicing_cycles, 'storage_types' => $storage_types, 'existing' => $existing, 'packaging_material_type_sizes' => $packaging_sizes, 'rate_remarks' => $rate_remarks, 'on' => $on, 'ol' => $ol, 'det' => $det, 'same_day' => $same_day, 'commission_percentage' => $commission_percentage, 'sales_tiers' => $sales_tiers, 'users' => $all_users, 'existing_commission_array' => $existing_commission_array, 'overnight_origins' => $overnight_origins, 'overland_origins' => $overland_origins, 'detain_origins' => $detain_origins, 'sameday_origins' => $sameday_origins,'overnight_destinations' => $overnight_destinations, 'overland_destinations' => $overland_destinations, 'detain_destinations' => $detain_destinations, 'sameday_destinations' => $sameday_destinations, 'cities' => $cities]);
                }
                else{
                    return view('admin.access_denied');
                }
            }
            else{
                return view('admin.accounts.edit_rates')->with(['sameday_dws_charges' => $sameday_dws_charges,'detain_dws_charges' => $detain_dws_charges,'ol_dws_charges' => $ol_dws_charges,'on_dws_charges' => $on_dws_charges,'shipper'=>$user,'switches'=>$switches,'weight'=>$weight,'shippingType'=>$bookingType,'cashHandling'=>$cash,'insuranceCharges'=>$insurance,'returnCharges'=>$return,'fuelCharges'=>$fuel, 'discountCharges'=>$discount, 'rate_status'=>$rate_status, 'sale_person' => $sale_person, 'packaging_material_types' => $packaging_material_types, 'packaging_type_ids' => $packaging_type_ids, 'packaging_charges' => $packaging_charges, 'existing' => $existing, 'wms_user_info' => $wms_user_info, 'wms_product_charges' => $wms_product_charges, 'wms_square_foot_charges' => $wms_square_foot_charges, 'wms_packing_charges' => $wms_packing_charges, 'wms_labelling_charges' => $wms_labelling_charges, 'wms_storage_charges' => $wms_storage_charges, 'invoicing_cycles' => $invoicing_cycles, 'storage_types' => $storage_types, 'packaging_material_type_sizes' => $packaging_sizes, 'rate_remarks' => $rate_remarks, 'on' => $on, 'ol' => $ol, 'det' => $det, 'same_day' => $same_day, 'commission_percentage' => $commission_percentage, 'sales_tiers' => $sales_tiers, 'users' => $all_users, 'existing_commission_array' => $existing_commission_array, 'overnight_origins' => $overnight_origins, 'overland_origins' => $overland_origins, 'detain_origins' => $detain_origins, 'sameday_origins' => $sameday_origins,'overnight_destinations' => $overnight_destinations, 'overland_destinations' => $overland_destinations, 'detain_destinations' => $detain_destinations, 'sameday_destinations' => $sameday_destinations, 'cities' => $cities]);
            }

        }
        elseif(($user['rate_status']>=1) && ($user['status']==3)){
            $e_switches = RateStatus::all()->where('user_id', $id)->groupBy('shipping_mode_id');
//        return $switches;
//        var_dump(empty($switches));exit();
            $e_weight = WeightCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
//        $cash = '';
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
            $e_rate_status = $user['rate_status'];
            $e_packaging_material_types = PackagingMaterialTypes::with(['sizes'])->where('status', 1)->get();

            $e_packaging_charges = array();
            if(count($e_packaging) > 0){

                foreach($e_packaging as $e_charge){
                    $e_packaging_charges[$e_charge->type_id][] = $e_charge;
                }
            }

            $switches = PendingRateStatus::all()->where('user_id', $id)->groupBy('shipping_mode_id');

            $weight = PendingWeightCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');

            $bookingType = PendingBookingTypeCharges::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $cash = PendingCashHandlingCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $insurance = PendingInsuranceCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $return = PendingReturnCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $fuel = PendingFuelSurcharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $packaging = PendingPackagingCharge::all()->where('user_id', $id);
            $discount = PendingDiscountCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
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
            $rate_remarks = RateRemark::where('user_id', $id)->orderBy('created_at','desc')->get();
            $packaging_charges = array();
            $packaging_sizes = array();
            if(count($packaging_material_types) > 0){

                foreach($packaging_material_types as $type){
                    $packaging_sizes[$type->id] = $type->sizes;
                }
            }
            if(count($packaging) > 0){

                foreach($packaging as $charge){
                    $packaging_charges[$charge->type_id][] = $charge;
                }
            }
            $cities = City::where('status', 1)->where('business_category_id', 1)->select('id', 'name')->get();

            $rate_origin_hubs = PendingRateOriginHub::all()->where('user_id',$id)->groupBy('shipping_mode_id');
            $rate_destination_hubs = PendingRateDestinationHub::all()->where('user_id',$id)->groupBy('shipping_mode_id');

            $overnight_origins = [];
            $overland_origins = [];
            $detain_origins = [];
            $sameday_origins = [];
            if($rate_origin_hubs || count($rate_origin_hubs) > 0){
                foreach($rate_origin_hubs as $index => $origin){

                    if($index == 1){
                        foreach($origin as $origin_data){
                            $overnight_origins[] = $origin_data->city_id;
                        }
                    }
                    else if($index == 2){
                        foreach($origin as $origin_data){
                            $overland_origins[] = $origin_data->city_id;
                        }
                    }
                    else if($index == 3){
                        foreach($origin as $origin_data){
                            $detain_origins[] = $origin_data->city_id;
                        }
                    }
                    else if($index == 4){
                        foreach($origin as $origin_data){
                            $sameday_origins[] = $origin_data->city_id;
                        }
                    }

                }
            }
            $overnight_destinations = [];
            $overland_destinations = [];
            $detain_destinations = [];
            $sameday_destinations = [];
            if($rate_destination_hubs || count($rate_destination_hubs) > 0){
                foreach($rate_destination_hubs as $index => $destination){

                    if($index == 1){
                        foreach($destination as $destination_data){
                            $overnight_destinations[] = $destination_data->city_id;
                        }
                    }
                    else if($index == 2){
                        foreach($destination as $destination_data){
                            $overland_destinations[] = $destination_data->city_id;
                        }
                    }
                    else if($index == 3){
                        foreach($destination as $destination_data){
                            $detain_destinations[] = $destination_data->city_id;
                        }
                    }
                    else if($index == 4){
                        foreach($destination as $destination_data){
                            $sameday_destinations[] = $destination_data->city_id;
                        }
                    }

                }
            }
            $existing = 1;
            if(session('department_id') == 7){
                if($sale_person['admin_id'] == Auth::id() || in_array(session('id'), session('sale_users_bypass')) || in_array($id, session('tagged_shippers'))){
                    return view('admin.accounts.edit_rates')->with(['sameday_dws_charges' => $sameday_dws_charges,'detain_dws_charges' => $detain_dws_charges,'ol_dws_charges' => $ol_dws_charges,'on_dws_charges' => $on_dws_charges,'shipper'=>$user,'switches'=>$switches,'weight'=>$weight,'shippingType'=>$bookingType,'cashHandling'=>$cash,'insuranceCharges'=>$insurance,'returnCharges'=>$return,'fuelCharges'=>$fuel, 'discountCharges'=>$discount, 'rate_status'=>$rate_status, 'sale_person' => $sale_person, 'packaging_material_types' => $packaging_material_types, 'packaging_type_ids' => $packaging_type_ids, 'packaging_charges' => $packaging_charges, 'wms_user_info' => $wms_user_info, 'wms_product_charges' => $wms_product_charges, 'wms_square_foot_charges' => $wms_square_foot_charges, 'wms_packing_charges' => $wms_packing_charges, 'wms_labelling_charges' => $wms_labelling_charges, 'wms_storage_charges' => $wms_storage_charges, 'invoicing_cycles' => $invoicing_cycles, 'storage_types' => $storage_types,'e_switches'=>$e_switches,'e_weight'=>$e_weight,'e_shippingType'=>$e_bookingType,'e_cashHandling'=>$e_cash,'e_insuranceCharges'=>$e_insurance,'e_returnCharges'=>$e_return,'e_fuelCharges'=>$e_fuel, 'e_discountCharges'=>$e_discount, 'e_rate_status'=>$e_rate_status, 'e_packaging_material_types' => $e_packaging_material_types, 'e_packaging_type_ids' => $e_packaging_type_ids, 'e_packaging_charges' => $e_packaging_charges, 'e_wms_user_info' => $e_wms_user_info, 'e_wms_product_charges' => $e_wms_product_charges, 'e_wms_square_foot_charges' => $e_wms_square_foot_charges, 'e_wms_packing_charges' => $e_wms_packing_charges, 'e_wms_labelling_charges' => $e_wms_labelling_charges, 'e_wms_storage_charges' => $e_wms_storage_charges, 'e_invoicing_cycles' => $e_invoicing_cycles, 'e_storage_types' => $e_storage_types, 'existing' => $existing, 'packaging_material_type_sizes' => $packaging_sizes, 'rate_remarks' => $rate_remarks, 'on' => $on, 'ol' => $ol, 'det' => $det, 'same_day' => $same_day, 'commission_percentage' => $commission_percentage, 'sales_tiers' => $sales_tiers, 'users' => $all_users, 'existing_commission_array' => $existing_commission_array, 'overnight_origins' => $overnight_origins, 'overland_origins' => $overland_origins, 'detain_origins' => $detain_origins, 'sameday_origins' => $sameday_origins,'overnight_destinations' => $overnight_destinations, 'overland_destinations' => $overland_destinations, 'detain_destinations' => $detain_destinations, 'sameday_destinations' => $sameday_destinations, 'cities' => $cities]);
                }
                else{
                    return view('admin.access_denied');
                }
            }
            else{
                return view('admin.accounts.edit_rates')->with(['sameday_dws_charges' => $sameday_dws_charges,'detain_dws_charges' => $detain_dws_charges,'ol_dws_charges' => $ol_dws_charges,'on_dws_charges' => $on_dws_charges,'shipper'=>$user,'switches'=>$switches,'weight'=>$weight,'shippingType'=>$bookingType,'cashHandling'=>$cash,'insuranceCharges'=>$insurance,'returnCharges'=>$return,'fuelCharges'=>$fuel, 'discountCharges'=>$discount, 'rate_status'=>$rate_status, 'sale_person' => $sale_person, 'packaging_material_types' => $packaging_material_types, 'packaging_type_ids' => $packaging_type_ids, 'packaging_charges' => $packaging_charges, 'existing' => $existing, 'wms_user_info' => $wms_user_info, 'wms_product_charges' => $wms_product_charges, 'wms_square_foot_charges' => $wms_square_foot_charges, 'wms_packing_charges' => $wms_packing_charges, 'wms_labelling_charges' => $wms_labelling_charges, 'wms_storage_charges' => $wms_storage_charges, 'invoicing_cycles' => $invoicing_cycles, 'storage_types' => $storage_types,'e_switches'=>$e_switches,'e_weight'=>$e_weight,'e_shippingType'=>$e_bookingType,'e_cashHandling'=>$e_cash,'e_insuranceCharges'=>$e_insurance,'e_returnCharges'=>$e_return,'e_fuelCharges'=>$e_fuel, 'e_discountCharges'=>$e_discount, 'e_rate_status'=>$e_rate_status, 'e_packaging_material_types' => $e_packaging_material_types, 'e_packaging_type_ids' => $e_packaging_type_ids, 'e_packaging_charges' => $e_packaging_charges, 'e_wms_user_info' => $e_wms_user_info, 'e_wms_product_charges' => $e_wms_product_charges, 'e_wms_square_foot_charges' => $e_wms_square_foot_charges, 'e_wms_packing_charges' => $e_wms_packing_charges, 'e_wms_labelling_charges' => $e_wms_labelling_charges, 'e_wms_storage_charges' => $e_wms_storage_charges, 'e_invoicing_cycles' => $e_invoicing_cycles, 'e_storage_types' => $e_storage_types, 'packaging_material_type_sizes' => $packaging_sizes, 'rate_remarks' => $rate_remarks, 'on' => $on, 'ol' => $ol, 'det' => $det, 'same_day' => $same_day, 'commission_percentage' => $commission_percentage, 'sales_tiers' => $sales_tiers, 'users' => $all_users, 'existing_commission_array' => $existing_commission_array, 'overnight_origins' => $overnight_origins, 'overland_origins' => $overland_origins, 'detain_origins' => $detain_origins, 'sameday_origins' => $sameday_origins,'overnight_destinations' => $overnight_destinations, 'overland_destinations' => $overland_destinations, 'detain_destinations' => $detain_destinations, 'sameday_destinations' => $sameday_destinations, 'cities' => $cities]);
            }
        }
        else {
            return redirect(route('admin.accounts.pending'));
        }
    }

    public function editRates(Request $request, $id){
        $user = User::find($id);
        
        if ($user['status']!=3) {
            $messages = [
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

            ];

            $validations = array();
            $on_validations = array();
            $ol_validations = array();
            $detain_validations = array();
            $sameday_validations = array();

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
                    'on_cash_range_up.*' => 'required_if:on_cash_handling_switch,==,on|numeric',
                    'on_cash_range_down.*' => 'required_if:on_cash_handling_switch,==,on|numeric',
                    'on_cash_charges.*' => 'required_if:on_cash_handling_switch,==,on',
                    'on_ins_range_up.*' => 'required_if:on_insurance_charges_switch,==,on|numeric',
                    'on_ins_range_down.*' => 'required_if:on_insurance_charges_switch,==,on|numeric',
                    'on_ins_charges.*' => 'required_if:on_insurance_charges_switch,==,on',
                    'on_return_local_charges.*' => 'required_if:on_return_switch,==,on|numeric',
                    'on_return_class_0_charges.*'=>'required_if:on_return_switch,==,on|numeric',
                    'on_return_class_1_charges.*'=>'required_if:on_return_switch,==,on|numeric',
                    'on_return_class_2_charges.*'=>'required_if:on_return_switch,==,on|numeric',
                    'on_return_class_3_charges.*'=>'required_if:on_return_switch,==,on|numeric',
                    'overnight_fuel_surcharge' => 'required_if:overnight_fuel_switch,==,on|numeric',

                    'on_discount_title' => 'required_with:on_discount_weight_rate,on_discount_cash_rate,on_discount_insurance_rate,on_discount_return_rate,on_discount_packaging_rate',
                    'on_daterange' => 'required_with:on_discount_weight_rate,on_discount_cash_rate,on_discount_insurance_rate,on_discount_return_rate,on_discount_packaging_rate',
                    'on_discount_weight_rate' => 'required_if:on_discount_weight_switch,==,on',
                    'on_discount_cash_rate' => 'required_if:on_discount_cash_switch,==,on',
                    'on_discount_insurance_rate' => 'required_if:on_discount_insurance_switch,==,on',
                    'on_discount_return_rate' => 'required_if:on_discount_return_switch,==,on',
                    'on_discount_packaging_rate' => 'required_if:on_discount_packaging_switch,==,on'
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
                    'ol_cash_range_up.*' => 'required_if:ol_cash_handling_switch,==,on|numeric',
                    'ol_cash_range_down.*' => 'required_if:ol_cash_handling_switch,==,on|numeric',
                    'ol_cash_charges.*' => 'required_if:ol_cash_handling_switch,==,on',
                    'ol_ins_range_up.*' => 'required_if:ol_insurance_charges_switch,==,on|numeric',
                    'ol_ins_range_down.*' => 'required_if:ol_insurance_charges_switch,==,on|numeric',
                    'ol_ins_charges.*' => 'required_if:ol_insurance_charges_switch,==,on',
                    'ol_return_local_charges.*' => 'required_if:ol_return_switch,==,on|numeric',
                    'ol_return_class_0_charges.*'=>'required_if:ol_return_switch,==,on|numeric',
                    'ol_return_class_1_charges.*'=>'required_if:ol_return_switch,==,on|numeric',
                    'ol_return_class_2_charges.*'=>'required_if:ol_return_switch,==,on|numeric',
                    'ol_return_class_3_charges.*'=>'required_if:ol_return_switch,==,on|numeric',
                    'overland_fuel_surcharge' => 'required_if:overland_fuel_switch,==,on|numeric',

                    'ol_discount_title' => 'required_with:ol_discount_weight_rate,ol_discount_cash_rate,ol_discount_insurance_rate,ol_discount_return_rate,ol_discount_packaging_rate',
                    'ol_daterange' => 'required_with:ol_discount_weight_rate,ol_discount_cash_rate,ol_discount_insurance_rate,ol_discount_return_rate,ol_discount_packaging_rate',
                    'ol_discount_weight_rate' => 'required_if:ol_discount_weight_switch,==,on',
                    'ol_discount_cash_rate' => 'required_if:ol_discount_cash_switch,==,on',
                    'ol_discount_insurance_rate' => 'required_if:ol_discount_insurance_switch,==,on',
                    'ol_discount_return_rate' => 'required_if:ol_discount_return_switch,==,on',
                    'ol_discount_packaging_rate' => 'required_if:ol_discount_packaging_switch,==,on',
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
                    'detain_cash_range_up.*' => 'required_if:detain_cash_handling_switch,==,on|numeric',
                    'detain_cash_range_down.*' => 'required_if:detain_cash_handling_switch,==,on|numeric',
                    'detain_cash_charges.*' => 'required_if:detain_cash_handling_switch,==,on',
                    'detain_ins_range_up.*' => 'required_if:detain_insurance_charges_switch,==,on|numeric',
                    'detain_ins_range_down.*' => 'required_if:detain_insurance_charges_switch,==,on|numeric',
                    'detain_ins_charges.*' => 'required_if:detain_insurance_charges_switch,==,on',
                    'detain_return_local_charges.*' => 'required_if:detain_return_switch,==,on|numeric',
                    'detain_return_class_0_charges.*'=>'required_if:detain_return_switch,==,on|numeric',
                    'detain_return_class_1_charges.*'=>'required_if:detain_return_switch,==,on|numeric',
                    'detain_return_class_2_charges.*'=>'required_if:detain_return_switch,==,on|numeric',
                    'detain_return_class_3_charges.*'=>'required_if:detain_return_switch,==,on|numeric',
                    'detain_fuel_surcharge' => 'required_if:detain_fuel_switch,==,on|numeric',

                    'detain_discount_title' => 'required_with:detain_discount_weight_rate,detain_discount_cash_rate,detain_discount_insurance_rate,detain_discount_return_rate,detain_discount_packaging_rate',
                    'detain_daterange' => 'required_with:detain_discount_weight_rate,detain_discount_cash_rate,detain_discount_insurance_rate,detain_discount_return_rate,detain_discount_packaging_rate',
                    'detain_discount_weight_rate' => 'required_if:detain_discount_weight_switch,==,on',
                    'detain_discount_cash_rate' => 'required_if:detain_discount_cash_switch,==,on',
                    'detain_discount_insurance_rate' => 'required_if:detain_discount_insurance_switch,==,on',
                    'detain_discount_return_rate' => 'required_if:detain_discount_return_switch,==,on',
                    'detain_discount_packaging_rate' => 'required_if:detain_discount_packaging_switch,==,on',
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
                    'sameday_cash_range_up.*' => 'required_if:sameday_cash_handling_switch,==,on|numeric',
                    'sameday_cash_range_down.*' => 'required_if:sameday_cash_handling_switch,==,on|numeric',
                    'sameday_cash_charges.*' => 'required_if:sameday_cash_handling_switch,==,on',
                    'sameday_ins_range_up.*' => 'required_if:sameday_insurance_charges_switch,==,on|numeric',
                    'sameday_ins_range_down.*' => 'required_if:sameday_insurance_charges_switch,==,on|numeric',
                    'sameday_ins_charges.*' => 'required_if:sameday_insurance_charges_switch,==,on',
                    'sameday_return_local_charges.*' => 'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_return_class_0_charges.*'=>'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_return_class_1_charges.*'=>'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_return_class_2charges.*'=>'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_return_class_3_charges.*'=>'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_fuel_surcharge' => 'required_if:sameday_fuel_switch,==,on|numeric',

                    'sameday_discount_title' => 'required_with:sameday_discount_weight_rate,sameday_discount_cash_rate,sameday_discount_insurance_rate,sameday_discount_return_rate,sameday_discount_packaging_rate',
                    'sameday_daterange' => 'required_with:sameday_discount_weight_rate,sameday_discount_cash_rate,sameday_discount_insurance_rate,sameday_discount_return_rate,sameday_discount_packaging_rate',
                    'sameday_discount_weight_rate' => 'required_if:sameday_discount_weight_switch,==,on',
                    'sameday_discount_cash_rate' => 'required_if:sameday_discount_cash_switch,==,on',
                    'sameday_discount_insurance_rate' => 'required_if:sameday_discount_insurance_switch,==,on',
                    'sameday_discount_return_rate' => 'required_if:sameday_discount_return_switch,==,on',
                    'sameday_discount_packaging_rate' => 'required_if:sameday_discount_packaging_switch,==,on',
                ];
            }

            if($request->has('warehouse_main_switch') && $request->warehouse_main_switch == 'on'){
                $warehouse_validations = [
                    'invoicing_cycle' => 'required|numeric',
                    'invoicing_date.*' => 'required_if:invoicing_cycle,1,3',
                    'ppc_charges'=>'required_if:ppc_switch,==,on',
                    'psf_charges'=>'required_if:psf_switch,==,on',
                    'storage_type.*'=>'required_if:storage_charges_switch,==,on',
                    'storage_type_charges.*'=>'required_if:storage_charges_switch,==,on|numeric',
                    'packing_type.*' => 'required_if:packing_charges_switch,==,on',
                    'packing_charges.*' => 'required_if:packing_charges_switch,==,on|numeric',
                    'labelling_charges.*' => 'required_if:labelling_charges_switch,==,on|numeric',
                ];
            }
            $validations = array_merge($on_validations, $ol_validations, $detain_validations, $sameday_validations);

            $validate = Validator::make($request->all(), $validations, $messages);

            if ($validate->fails()) {
                return redirect()->back()
                    ->withErrors($validate)
                    ->withInput();
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
                        'fuel_charges' => ($request->has('overnight_fuel_switch')) ? 1 : 0
                    ]);
            } else {
                RateStatus::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 1,
                    'status' => ($request->has('on_main_switch')) ? 1 : 0,
                    'cash_handling_charges' => ($request->has('on_cash_handling_switch')) ? 1 : 0,
                    'insurance_charges' => ($request->has('on_insurance_charges_switch')) ? 1 : 0,
                    'return_charges' => ($request->has('on_return_switch')) ? 1 : 0,
                    'fuel_charges' => ($request->has('overnight_fuel_switch')) ? 1 : 0
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
                        'fuel_charges' => ($request->has('overland_fuel_switch')) ? 1 : 0
                    ]);
            } else {
                RateStatus::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 2,
                    'status' => ($request->has('ol_main_switch')) ? 1 : 0,
                    'cash_handling_charges' => ($request->has('ol_cash_handling_switch')) ? 1 : 0,
                    'insurance_charges' => ($request->has('ol_insurance_charges_switch')) ? 1 : 0,
                    'return_charges' => ($request->has('ol_return_switch')) ? 1 : 0,
                    'fuel_charges' => ($request->has('overland_fuel_switch')) ? 1 : 0
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
                        'fuel_charges' => ($request->has('detain_fuel_switch')) ? 1 : 0
                    ]);
            } else {
                RateStatus::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 3,
                    'status' => ($request->has('detain_main_switch')) ? 1 : 0,
                    'cash_handling_charges' => ($request->has('detain_cash_handling_switch')) ? 1 : 0,
                    'insurance_charges' => ($request->has('detain_insurance_charges_switch')) ? 1 : 0,
                    'return_charges' => ($request->has('detain_return_switch')) ? 1 : 0,
                    'fuel_charges' => ($request->has('detain_fuel_switch')) ? 1 : 0
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
                        'fuel_charges' => ($request->has('sameday_fuel_switch')) ? 1 : 0
                    ]);
            } else {
                RateStatus::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 4,
                    'status' => ($request->has('sameday_main_switch')) ? 1 : 0,
                    'cash_handling_charges' => ($request->has('sameday_cash_handling_switch')) ? 1 : 0,
                    'insurance_charges' => ($request->has('sameday_insurance_charges_switch')) ? 1 : 0,
                    'return_charges' => ($request->has('sameday_return_switch')) ? 1 : 0,
                    'fuel_charges' => ($request->has('sameday_fuel_switch')) ? 1 : 0
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

            if ($request->has('on_main_switch') && $request->on_main_switch == 'on') {

                if($request->has('on_default') && $request->on_default == 'on'){
                    $default_shipping_mode = User::where('id', $id)->update([
                        'default_shipping_mode' => 1
                    ]);
                }

                $ONRateAlready = RateStatus::where(['user_id' => $id, 'shipping_mode_id' => 1])->get();

                if (!$ONRateAlready->isEmpty()) {
                    RateOriginHub::where(['user_id' => $id, 'shipping_mode_id' => 1])->delete();
                    if($request->has('on_origin_hubs')) {
                        foreach($request->on_origin_hubs as $origin_id){
                            $rate_origin_hub = new RateOriginHub();
                            $rate_origin_hub->user_id = $id;
                            $rate_origin_hub->shipping_mode_id = 1;
                            $rate_origin_hub->city_id = $origin_id;
                            $rate_origin_hub->save();
                        }
                    }
                    RateDestinationHub::where(['user_id' => $id, 'shipping_mode_id' => 1])->delete();
                    if($request->has('on_destination_hubs')) {
                        foreach($request->on_destination_hubs as $destination_id){
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
                            'try_and_buy_charges' => $request->on_tnb_charges
                        ]);
                    } else {
                        BookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'replacement_charges' => $request->on_replacement_charges,
                            'try_and_buy_charges' => $request->on_tnb_charges
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
                                'national_charges_class_0'=> $request->on_return_class_0_charges,
                                'national_charges_class_1'=> $request->on_return_class_1_charges,
                                'national_charges_class_2'=> $request->on_return_class_2_charges,
                                'national_charges_class_3'=> $request->on_return_class_3_charges
                            ]);
                        } elseif ($request->on_return_record == null) {
                            ReturnCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'local' => $request->on_return_local_charges,
                                'national_charges_class_0'=> $request->on_return_class_0_charges,
                                'national_charges_class_1'=> $request->on_return_class_1_charges,
                                'national_charges_class_2'=> $request->on_return_class_2_charges,
                                'national_charges_class_3'=> $request->on_return_class_3_charges
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

                }
                //dd($weightAlready);

                //shipping_mode 1 //weight_charges 0
                
                if ($request->has('on_dws_weight')) {
                    if($request->on_dws_weight == 2){
                        DwsWeightChargesController::edit($id, 1, 2,Auth::id());
                    }else{
                        DwsWeightChargesController::edit($id, 1, 1,Auth::id());

                    }
                }else{
                    DwsWeightChargesController::delete_dws_rate($id, 1);
                }
            }

            //overland
            if ($request->has('ol_main_switch') && $request->ol_main_switch == 'on') {

                if($request->has('ol_default') && $request->ol_default == 'on'){
                    $default_shipping_mode = User::where('id', $id)->update([
                        'default_shipping_mode' => 2
                    ]);
                }

                $ONRateAlready = RateStatus::where(['user_id' => $id, 'shipping_mode_id' => 2])->get();

                if (!$ONRateAlready->isEmpty()) {
                    RateOriginHub::where(['user_id' => $id, 'shipping_mode_id' => 2])->delete();
                    if($request->has('ol_origin_hubs')) {
                        foreach($request->ol_origin_hubs as $origin_id){
                            $rate_origin_hub = new RateOriginHub();
                            $rate_origin_hub->user_id = $id;
                            $rate_origin_hub->shipping_mode_id = 2;
                            $rate_origin_hub->city_id = $origin_id;
                            $rate_origin_hub->save();
                        }
                    }
                    RateDestinationHub::where(['user_id' => $id, 'shipping_mode_id' => 2])->delete();
                    if($request->has('ol_destination_hubs')) {
                        foreach($request->ol_destination_hubs as $destination_id){
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
                            'try_and_buy_charges' => $request->ol_tnb_charges
                        ]);
                    } else {
                        BookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'replacement_charges' => $request->ol_replacement_charges,
                            'try_and_buy_charges' => $request->ol_tnb_charges
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
                                'national_charges_class_0'=> $request->ol_return_class_0_charges,
                                'national_charges_class_1'=> $request->ol_return_class_1_charges,
                                'national_charges_class_2'=> $request->ol_return_class_2_charges,
                                'national_charges_class_3'=> $request->ol_return_class_3_charges
                            ]);
                        } elseif ($request->ol_return_record == null) {
                            ReturnCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'local' => $request->ol_return_local_charges,
                                'national_charges_class_0'=> $request->ol_return_class_0_charges,
                                'national_charges_class_1'=> $request->ol_return_class_1_charges,
                                'national_charges_class_2'=> $request->ol_return_class_2_charges,
                                'national_charges_class_3'=> $request->ol_return_class_3_charges
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

                }
                //dd($weightAlready);
                if ($request->has('ol_dws_weight')) {
                    if($request->ol_dws_weight == 2){
                        DwsWeightChargesController::edit($id, 2, 2,Auth::id());

                    }else{
                        
                        DwsWeightChargesController::edit($id, 2, 1,Auth::id());
                    }
        
                }else{
                    DwsWeightChargesController::delete_dws_rate($id, 2);
                }
            }

            //detain
            if ($request->has('detain_main_switch') && $request->detain_main_switch == 'on') {

                if($request->has('det_default') && $request->det_default == 'on'){
                    $default_shipping_mode = User::where('id', $id)->update([
                        'default_shipping_mode' => 3
                    ]);
                }
                $ONRateAlready = RateStatus::where(['user_id' => $id, 'shipping_mode_id' => 3])->get();

                if (!$ONRateAlready->isEmpty()) {
                    RateOriginHub::where(['user_id' => $id, 'shipping_mode_id' => 3])->delete();
                    if($request->has('detain_origin_hubs')) {
                        foreach($request->detain_origin_hubs as $origin_id){
                            $rate_origin_hub = new RateOriginHub();
                            $rate_origin_hub->user_id = $id;
                            $rate_origin_hub->shipping_mode_id = 3;
                            $rate_origin_hub->city_id = $origin_id;
                            $rate_origin_hub->save();
                        }
                    }
                    RateDestinationHub::where(['user_id' => $id, 'shipping_mode_id' => 3])->delete();
                    if($request->has('detain_destination_hubs')) {
                        foreach($request->detain_destination_hubs as $destination_id){
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
                            'try_and_buy_charges' => $request->detain_tnb_charges
                        ]);
                    } else {
                        BookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'replacement_charges' => $request->detain_replacement_charges,
                            'try_and_buy_charges' => $request->detain_tnb_charges
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
                                'national_charges_class_0'=> $request->detain_return_class_0_charges,
                                'national_charges_class_1'=> $request->detain_return_class_1_charges,
                                'national_charges_class_2'=> $request->detain_return_class_2_charges,
                                'national_charges_class_3'=> $request->detain_return_class_3_charges
                            ]);
                        } elseif ($request->detain_return_record == null) {
                            ReturnCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'local' => $request->detain_return_local_charges,
                                'national_charges_class_0'=> $request->detain_return_class_0_charges,
                                'national_charges_class_1'=> $request->detain_return_class_1_charges,
                                'national_charges_class_2'=> $request->detain_return_class_2_charges,
                                'national_charges_class_3'=> $request->detain_return_class_3_charges
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

                }
                //dd($weightAlready);
                if ($request->has('detain_dws_weight')) {
                    if($request->detain_dws_weight == 2){
                        DwsWeightChargesController::edit($id, 3, 2,Auth::id());


                    }else{
                        DwsWeightChargesController::edit($id, 3, 1,Auth::id());
                    }
        
                }else{
                    DwsWeightChargesController::delete_dws_rate($id, 3);
                }
            }

            //sameday
            if ($request->has('sameday_main_switch') && $request->sameday_main_switch == 'on') {

                if($request->has('sameday_default') && $request->sameday_default == 'on'){
                    $default_shipping_mode = User::where('id', $id)->update([
                        'default_shipping_mode' => 4
                    ]);
                }

                $ONRateAlready = RateStatus::where(['user_id' => $id, 'shipping_mode_id' => 4])->get();

                if (!$ONRateAlready->isEmpty()) {
                    RateOriginHub::where(['user_id' => $id, 'shipping_mode_id' => 4])->delete();
                    if($request->has('sameday_origin_hubs')) {
                        foreach($request->sameday_origin_hubs as $origin_id){
                            $rate_origin_hub = new RateOriginHub();
                            $rate_origin_hub->user_id = $id;
                            $rate_origin_hub->shipping_mode_id = 4;
                            $rate_origin_hub->city_id = $origin_id;
                            $rate_origin_hub->save();
                        }
                    }
                    RateDestinationHub::where(['user_id' => $id, 'shipping_mode_id' => 4])->delete();
                    if($request->has('sameday_destination_hubs')) {
                        foreach($request->sameday_destination_hubs as $destination_id){
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
                            'try_and_buy_charges' => $request->sameday_tnb_charges
                        ]);
                    } else {
                        BookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'replacement_charges' => $request->sameday_replacement_charges,
                            'try_and_buy_charges' => $request->sameday_tnb_charges
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
                                'national_charges_class_0'=> $request->sameday_return_class_0_charges,
                                'national_charges_class_1'=> 0,
                                'national_charges_class_2'=> 0,
                                'national_charges_class_3'=> 0
                            ]);
                        } elseif ($request->sameday_return_record == null) {
                            ReturnCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'local' => $request->sameday_return_local_charges,
                                'national_charges_class_0'=> $request->sameday_return_class_0_charges,
                                'national_charges_class_1'=> 0,
                                'national_charges_class_2'=> 0,
                                'national_charges_class_3'=> 0
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

                }
                //dd($weightAlready);
                if ($request->has('sameday_dws_weight')) {
                    if($request->sameday_dws_weight == 2){
                        DwsWeightChargesController::edit($id, 4, 2,Auth::id());
                    }else{
                        DwsWeightChargesController::edit($id, 4, 1,Auth::id());
                    }
        
                }else{
                    DwsWeightChargesController::delete_dws_rate($id, 4);
                }
            }
            if($request->has('warehouse_main_switch') && $request->warehouse_main_switch == 'on'){
                $wms_user_info = WmsUserInformation::where('user_id', $id);
                if($wms_user_info->exists()){
                    $wms_user_info = $wms_user_info->first();
                    $wms_user_info->invoicing_cycle = $request->invoicing_cycle;
                    $wms_user_info->invoicing_date = 1;
                    $wms_user_info->per_product_charges = ($request->has('ppc_switch'))? 1:0;
                    $wms_user_info->per_square_foot_charges = ($request->has('psf_switch'))? 1:0;
                    $wms_user_info->packing_charges = ($request->has('packing_charges_switch'))? 1:0;
                    $wms_user_info->labelling_charges = ($request->has('labelling_charges_switch'))? 1:0;
                    $wms_user_info->storage_charges = ($request->has('storage_charges_switch'))? 1:0;
                }else{
                    $wms_user_info = new WmsUserInformation();
                    $wms_user_info->user_id = $id;
                    $wms_user_info->warehousing = 1;
                    $wms_user_info->invoicing_cycle = $request->invoicing_cycle;
                    $wms_user_info->invoicing_date = 1;
                    $wms_user_info->per_product_charges = ($request->has('ppc_switch'))? 1:0;
                    $wms_user_info->per_square_foot_charges = ($request->has('psf_switch'))? 1:0;
                    $wms_user_info->packing_charges = ($request->has('packing_charges_switch'))? 1:0;
                    $wms_user_info->labelling_charges = ($request->has('labelling_charges_switch'))? 1:0;
                    $wms_user_info->storage_charges = ($request->has('storage_charges_switch'))? 1:0;

                }
                $wms_user_info->save();

                if($request->has('ppc_switch')){
                    $ppc = WmsPerProductCharge::where('user_id', $id);
                    if($ppc->exists()){
                        $ppc = $ppc->first();
                        $ppc->charges = $request->ppc_charges;
                    }else{
                        $ppc = new WmsPerProductCharge();
                        $ppc->user_id = $id;
                        $ppc->charges = $request->ppc_charges;
                    }
                    $ppc->save();
                }else{
                    WmsPerProductCharge::where('user_id', $id)->delete();
                }
                if($request->has('psf_switch')){
                    $psf = WmsPerSquareFootCharge::where('user_id', $id);
                    if($psf->exists()){
                        $psf = $psf->first();
                        $psf->charges = $request->psf_charges;
                    }else{
                        $psf = new WmsPerSquareFootCharge();
                        $psf->user_id = $id;
                        $psf->charges = $request->psf_charges;
                    }
                    $psf->save();

                }
                if($request->has('storage_charges_switch')){
                    WmsStorageTypeCharge::where('user_id', $id)->delete();
                    foreach ($request->storage_type as $key => $storage_type) {
                        $storage_charges = new WmsStorageTypeCharge();
                        $storage_charges->user_id = $id;
                        $storage_charges->storage_type_id = $storage_type;
                        $storage_charges->charges = $request->storage_type_charges[$key];
                        $storage_charges->save();
                    }
                }else{
                    WmsStorageTypeCharge::where('user_id', $id)->delete();
                }

                if($request->has('packing_charges_switch')){
                    WmsPackingCharge::where('user_id', $id)->delete();
                    foreach ($request->packing_type as $key => $packing) {
                        $ptype = new WmsPackingCharge();
                        $ptype->user_id = $id;
                        $ptype->packing_type_id = $packing;
                        $ptype->packing_size_id = $request->packing_size[$key];
                        $ptype->charges = $request->packing_charges[$key];
                        $ptype->save();
                    }
                }else{
                    WmsPackingCharge::where('user_id', $id)->delete();
                }

                if($request->has('labelling_charges_switch')){
                    $labelling = WmsLabellingCharge::where('user_id',$id);
                    if($labelling->exists()){
                        $labelling = $labelling->first();
                        $labelling->charges = $request->labelling_charges;
                    }else{
                        $labelling = new WmsLabellingCharge();
                        $labelling->user_id = $id;
                        $labelling->charges = $request->labelling_charges;
                    }
                    $labelling->save();

                }else{
                    WmsLabellingCharge::where('user_id', $id)->delete();
                }
            }else{
                $wms_user_info = WmsUserInformation::where('user_id', $id);
                if($wms_user_info->exists()){
                    $wms_user_info = $wms_user_info->first();
                    $wms_user_info->warehousing = 0;
                    $wms_user_info->save();
                }
            }
            User::where('id',$id)->update(['rate_status'=>1]);
            if($request->has('rate_remarks') && $request->rate_remarks != null){
                $rate_remark = new RateRemark();
                $rate_remark->user_id = $id;
                $rate_remark->remarks = $request->rate_remarks;
                $rate_remark->admin_id = Auth::id();
                $rate_remark->save();

            }

            if($request->has('edit_commission') && $request->edit_commission == 1){
                if($request->total_commission > 0){
                    $existing_sale_commission = SalesCommission::where('shipper_id', $id)->first();
                    if($existing_sale_commission){
                        SalesCommissionUser::where('sales_commission_id', $existing_sale_commission->id)->delete();
                        SalesCommissionExternalUser::where('shipper_id',$id)->delete();
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
                    foreach($request->tier_id as $row_id => $tier){
                        $sales_tier = SalesTier::find($tier);
                        if($sales_tier){
                            $sales_commission_user = new SalesCommissionUser();
                            $sales_commission_user->sales_commission_id = $sales_commission_id;
                            $sales_commission_user->tier_type_id = $sales_tier->tier_type;
                            $sales_commission_user->tier_id = $tier;
                            if($sales_tier->tier_type == 1){
                                $sales_commission_user->user_id = $request->user_id[$row_id];
                            }else if($sales_tier->tier_type == 2){
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
                }
                else{
                    $existing_sale_commission = SalesCommission::where('shipper_id', $id)->first();
                    if($existing_sale_commission){
                        SalesCommissionUser::where('sales_commission_id', $existing_sale_commission->id)->delete();
                        SalesCommissionExternalUser::where('shipper_id',$id)->delete();
                        SalesCommission::where('shipper_id', $id)->delete();
                    }
                }
            }
            if($request->authorize == 1){
                DwsWeightChargesController::approve($id);
                User::where('id',$id)->update(['rate_status'=>0,'status'=>2,'rates_authorized_by'=>Auth::id(),'rates_approved_at'=>Carbon::now()]);
                return redirect(route('admin.accounts.pending'))->with('success','User is now authorized.');
            }

            return redirect()->back()->with('success', 'All Rates are updated');
        }

        if ($user['status'] == 3) {
            $messages = [
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

            ];

            $validations = array();
            $on_validations = array();
            $ol_validations = array();
            $detain_validations = array();
            $sameday_validations = array();

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
                    'on_cash_range_up.*' => 'required_if:on_cash_handling_switch,==,on|numeric',
                    'on_cash_range_down.*' => 'required_if:on_cash_handling_switch,==,on|numeric',
                    'on_cash_charges.*' => 'required_if:on_cash_handling_switch,==,on',
                    'on_ins_range_up.*' => 'required_if:on_insurance_charges_switch,==,on|numeric',
                    'on_ins_range_down.*' => 'required_if:on_insurance_charges_switch,==,on|numeric',
                    'on_ins_charges.*' => 'required_if:on_insurance_charges_switch,==,on',
                    'on_return_local_charges.*' => 'required_if:on_return_switch,==,on|numeric',
                    'on_return_class_0_charges.*'=>'required_if:on_return_switch,==,on|numeric',
                    'on_return_class_1_charges.*'=>'required_if:on_return_switch,==,on|numeric',
                    'on_return_class_2_charges.*'=>'required_if:on_return_switch,==,on|numeric',
                    'on_return_class_3_charges.*'=>'required_if:on_return_switch,==,on|numeric',
                    'overnight_fuel_surcharge' => 'required_if:overnight_fuel_switch,==,on|numeric',

                    'on_discount_title' => 'required_with:on_discount_weight_rate,on_discount_cash_rate,on_discount_insurance_rate,on_discount_return_rate,on_discount_packaging_rate',
                    'on_daterange' => 'required_with:on_discount_weight_rate,on_discount_cash_rate,on_discount_insurance_rate,on_discount_return_rate,on_discount_packaging_rate',
                    'on_discount_weight_rate' => 'required_if:on_discount_weight_switch,==,on',
                    'on_discount_cash_rate' => 'required_if:on_discount_cash_switch,==,on',
                    'on_discount_insurance_rate' => 'required_if:on_discount_insurance_switch,==,on',
                    'on_discount_return_rate' => 'required_if:on_discount_return_switch,==,on',
                    'on_discount_packaging_rate' => 'required_if:on_discount_packaging_switch,==,on'
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
                    'ol_cash_range_up.*' => 'required_if:ol_cash_handling_switch,==,on|numeric',
                    'ol_cash_range_down.*' => 'required_if:ol_cash_handling_switch,==,on|numeric',
                    'ol_cash_charges.*' => 'required_if:ol_cash_handling_switch,==,on',
                    'ol_ins_range_up.*' => 'required_if:ol_insurance_charges_switch,==,on|numeric',
                    'ol_ins_range_down.*' => 'required_if:ol_insurance_charges_switch,==,on|numeric',
                    'ol_ins_charges.*' => 'required_if:ol_insurance_charges_switch,==,on',
                    'ol_return_local_charges.*' => 'required_if:ol_return_switch,==,on|numeric',
                    'ol_return_class_0_charges.*'=>'required_if:ol_return_switch,==,on|numeric',
                    'ol_return_class_1_charges.*'=>'required_if:ol_return_switch,==,on|numeric',
                    'ol_return_class_2_charges.*'=>'required_if:ol_return_switch,==,on|numeric',
                    'ol_return_class_3_charges.*'=>'required_if:ol_return_switch,==,on|numeric',
                    'overland_fuel_surcharge' => 'required_if:overland_fuel_switch,==,on|numeric',

                    'ol_discount_title' => 'required_with:ol_discount_weight_rate,ol_discount_cash_rate,ol_discount_insurance_rate,ol_discount_return_rate,ol_discount_packaging_rate',
                    'ol_daterange' => 'required_with:ol_discount_weight_rate,ol_discount_cash_rate,ol_discount_insurance_rate,ol_discount_return_rate,ol_discount_packaging_rate',
                    'ol_discount_weight_rate' => 'required_if:ol_discount_weight_switch,==,on',
                    'ol_discount_cash_rate' => 'required_if:ol_discount_cash_switch,==,on',
                    'ol_discount_insurance_rate' => 'required_if:ol_discount_insurance_switch,==,on',
                    'ol_discount_return_rate' => 'required_if:ol_discount_return_switch,==,on',
                    'ol_discount_packaging_rate' => 'required_if:ol_discount_packaging_switch,==,on',
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
                    'detain_cash_range_up.*' => 'required_if:detain_cash_handling_switch,==,on|numeric',
                    'detain_cash_range_down.*' => 'required_if:detain_cash_handling_switch,==,on|numeric',
                    'detain_cash_charges.*' => 'required_if:detain_cash_handling_switch,==,on',
                    'detain_ins_range_up.*' => 'required_if:detain_insurance_charges_switch,==,on|numeric',
                    'detain_ins_range_down.*' => 'required_if:detain_insurance_charges_switch,==,on|numeric',
                    'detain_ins_charges.*' => 'required_if:detain_insurance_charges_switch,==,on',
                    'detain_return_local_charges.*' => 'required_if:detain_return_switch,==,on|numeric',
                    'detain_return_class_0_charges.*'=>'required_if:detain_return_switch,==,on|numeric',
                    'detain_return_class_1_charges.*'=>'required_if:detain_return_switch,==,on|numeric',
                    'detain_return_class_2_charges.*'=>'required_if:detain_return_switch,==,on|numeric',
                    'detain_return_class_3_charges.*'=>'required_if:detain_return_switch,==,on|numeric',
                    'detain_fuel_surcharge' => 'required_if:detain_fuel_switch,==,on|numeric',

                    'detain_discount_title' => 'required_with:detain_discount_weight_rate,detain_discount_cash_rate,detain_discount_insurance_rate,detain_discount_return_rate,detain_discount_packaging_rate',
                    'detain_daterange' => 'required_with:detain_discount_weight_rate,detain_discount_cash_rate,detain_discount_insurance_rate,detain_discount_return_rate,detain_discount_packaging_rate',
                    'detain_discount_weight_rate' => 'required_if:detain_discount_weight_switch,==,on',
                    'detain_discount_cash_rate' => 'required_if:detain_discount_cash_switch,==,on',
                    'detain_discount_insurance_rate' => 'required_if:detain_discount_insurance_switch,==,on',
                    'detain_discount_return_rate' => 'required_if:detain_discount_return_switch,==,on',
                    'detain_discount_packaging_rate' => 'required_if:detain_discount_packaging_switch,==,on',
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
                    'sameday_cash_range_up.*' => 'required_if:sameday_cash_handling_switch,==,on|numeric',
                    'sameday_cash_range_down.*' => 'required_if:sameday_cash_handling_switch,==,on|numeric',
                    'sameday_cash_charges.*' => 'required_if:sameday_cash_handling_switch,==,on',
                    'sameday_ins_range_up.*' => 'required_if:sameday_insurance_charges_switch,==,on|numeric',
                    'sameday_ins_range_down.*' => 'required_if:sameday_insurance_charges_switch,==,on|numeric',
                    'sameday_ins_charges.*' => 'required_if:sameday_insurance_charges_switch,==,on',
                    'sameday_return_local_charges.*' => 'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_return_class_0_charges.*'=>'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_return_class_1_charges.*'=>'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_return_class_2_charges.*'=>'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_return_class_3_charges.*'=>'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_fuel_surcharge' => 'required_if:sameday_fuel_switch,==,on|numeric',

                    'sameday_discount_title' => 'required_with:sameday_discount_weight_rate,sameday_discount_cash_rate,sameday_discount_insurance_rate,sameday_discount_return_rate,sameday_discount_packaging_rate',
                    'sameday_daterange' => 'required_with:sameday_discount_weight_rate,sameday_discount_cash_rate,sameday_discount_insurance_rate,sameday_discount_return_rate,sameday_discount_packaging_rate',
                    'sameday_discount_weight_rate' => 'required_if:sameday_discount_weight_switch,==,on',
                    'sameday_discount_cash_rate' => 'required_if:sameday_discount_cash_switch,==,on',
                    'sameday_discount_insurance_rate' => 'required_if:sameday_discount_insurance_switch,==,on',
                    'sameday_discount_return_rate' => 'required_if:sameday_discount_return_switch,==,on',
                    'sameday_discount_packaging_rate' => 'required_if:sameday_discount_packaging_switch,==,on',
                ];
            }

            if($request->has('warehouse_main_switch') && $request->warehouse_main_switch == 'on'){
                $warehouse_validations = [
                    'invoicing_cycle' => 'required|numeric',
                    'invoicing_date.*' => 'required_if:invoicing_cycle,1,3',
                    'ppc_charges'=>'required_if:ppc_switch,==,on',
                    'psf_charges'=>'required_if:psf_switch,==,on',
                    'storage_type.*'=>'required_if:storage_charges_switch,==,on',
                    'storage_type_charges.*'=>'required_if:storage_charges_switch,==,on|numeric',
                    'packing_type.*' => 'required_if:packing_charges_switch,==,on',
                    'packing_charges.*' => 'required_if:packing_charges_switch,==,on|numeric',
                    'labelling_charges.*' => 'required_if:labelling_charges_switch,==,on|numeric',
                ];
            }

            $validations = array_merge($on_validations, $ol_validations, $detain_validations, $sameday_validations);

            $validate = Validator::make($request->all(), $validations, $messages);

            if ($validate->fails()) {
                return redirect()->back()
                    ->withErrors($validate)
                    ->withInput();
            }

            if($request->has('on_default') && $request->on_default == 'on'){
                $default_shipping_mode = User::where('id', $id)->update([
                    'default_shipping_mode' => 1
                ]);
            }
            if($request->has('ol_default') && $request->ol_default == 'on'){
                $default_shipping_mode = User::where('id', $id)->update([
                    'default_shipping_mode' => 2
                ]);
            }
            if($request->has('det_default') && $request->det_default == 'on'){
                $default_shipping_mode = User::where('id', $id)->update([
                    'default_shipping_mode' => 3
                ]);
            }
            if($request->has('sameday_default') && $request->sameday_default == 'on'){
                $default_shipping_mode = User::where('id', $id)->update([
                    'default_shipping_mode' => 4
                ]);
            }

            PendingRateStatus::where('user_id', $id)->delete();
            PendingWeightCharge::where('user_id', $id)->delete();
            PendingBookingTypeCharges::where('user_id', $id)->delete();
            PendingCashHandlingCharge::where('user_id', $id)->delete();
            PendingInsuranceCharge::where('user_id', $id)->delete();
            PendingReturnCharge::where('user_id', $id)->delete();
            PendingFuelSurcharge::where('user_id', $id)->delete();
            PendingPackagingCharge::where('user_id', $id)->delete();
            PendingDiscountCharge::where('user_id', $id)->delete();
            PendingRateOriginHub::where('user_id', $id)->delete();
            PendingRateDestinationHub::where('user_id', $id)->delete();


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
                        'fuel_charges' => ($request->has('overnight_fuel_switch')) ? 1 : 0

                    ]);


                    if($request->has('on_origin_hubs')) {
                        foreach($request->on_origin_hubs as $origin_id){
                            $rate_origin_hub = new PendingRateOriginHub();
                            $rate_origin_hub->user_id = $id;
                            $rate_origin_hub->shipping_mode_id = 1;
                            $rate_origin_hub->city_id = $origin_id;
                            $rate_origin_hub->save();
                        }
                    }

                    if($request->has('on_destination_hubs')) {
                        foreach($request->on_destination_hubs as $destination_id){
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
                        'try_and_buy_charges' => $request->on_tnb_charges
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
                            'national_charges_class_0'=> $request->on_return_class_0_charges,
                            'national_charges_class_1'=> $request->on_return_class_1_charges,
                            'national_charges_class_2'=> $request->on_return_class_2_charges,
                            'national_charges_class_3'=> $request->on_return_class_3_charges
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

                }
                //dd($weightAlready);
                if ($request->has('on_dws_weight')) {
                    if($request->on_dws_weight == 2){
                        DwsWeightChargesController::edit($id, 1, 2,Auth::id());
                        
                    }else{
                        DwsWeightChargesController::edit($id, 1, 1,Auth::id());
                    }
        
                }else{
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
                        'fuel_charges' => ($request->has('overland_fuel_switch')) ? 1 : 0

                    ]);

                    if($request->has('ol_origin_hubs')) {
                        foreach($request->ol_origin_hubs as $origin_id){
                            $rate_origin_hub = new PendingRateOriginHub();
                            $rate_origin_hub->user_id = $id;
                            $rate_origin_hub->shipping_mode_id = 2;
                            $rate_origin_hub->city_id = $origin_id;
                            $rate_origin_hub->save();
                        }
                    }

                    if($request->has('ol_destination_hubs')) {
                        foreach($request->ol_destination_hubs as $destination_id){
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
                        'try_and_buy_charges' => $request->ol_tnb_charges
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
                            'national_charges_class_0'=> $request->ol_return_class_0_charges,
                            'national_charges_class_1'=> $request->ol_return_class_1_charges,
                            'national_charges_class_2'=> $request->ol_return_class_2_charges,
                            'national_charges_class_3'=> $request->ol_return_class_3_charges
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

                }
                if ($request->has('ol_dws_weight')) {
                    if($request->ol_dws_weight == 2){
                        DwsWeightChargesController::edit($id, 2, 2,Auth::id());
                    }else{
                        DwsWeightChargesController::edit($id, 2, 1,Auth::id());
                    }
        
                }else{
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
                        'fuel_charges' => ($request->has('detain_fuel_switch')) ? 1 : 0

                    ]);

                    if($request->has('detain_origin_hubs')) {
                        foreach($request->detain_origin_hubs as $origin_id){
                            $rate_origin_hub = new PendingRateOriginHub();
                            $rate_origin_hub->user_id = $id;
                            $rate_origin_hub->shipping_mode_id = 3;
                            $rate_origin_hub->city_id = $origin_id;
                            $rate_origin_hub->save();
                        }
                    }

                    if($request->has('detain_destination_hubs')) {
                        foreach($request->detain_destination_hubs as $destination_id){
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
                        'try_and_buy_charges' => $request->detain_tnb_charges
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
                            'national_charges_class_0'=> $request->detain_return_class_0_charges,
                            'national_charges_class_1'=> $request->detain_return_class_1_charges,
                            'national_charges_class_2'=> $request->detain_return_class_2_charges,
                            'national_charges_class_3'=> $request->detain_return_class_3_charges
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

                }
                if ($request->has('detain_dws_weight')) {
                    if($request->detain_dws_weight == 2){
                        DwsWeightChargesController::edit($id, 3, 2,Auth::id());
                        
                    }else{
                        DwsWeightChargesController::edit($id, 3, 1,Auth::id());
                        

                    }
        
                }else{
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
                        'fuel_charges' => ($request->has('sameday_fuel_switch')) ? 1 : 0
                    ]);

                    if($request->has('sameday_origin_hubs')) {
                        foreach($request->sameday_origin_hubs as $origin_id){
                            $rate_origin_hub = new PendingRateOriginHub();
                            $rate_origin_hub->user_id = $id;
                            $rate_origin_hub->shipping_mode_id = 4;
                            $rate_origin_hub->city_id = $origin_id;
                            $rate_origin_hub->save();
                        }
                    }


                    if($request->has('sameday_destination_hubs')) {
                        foreach($request->sameday_destination_hubs as $destination_id){
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
                        'try_and_buy_charges' => $request->sameday_tnb_charges
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
                            'national_charges_class_0'=> $request->sameday_return_class_0_charges,
                            'national_charges_class_1'=> 0,
                            'national_charges_class_2'=> 0,
                            'national_charges_class_3'=> 0
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

                }
                if ($request->has('sameday_dws_weight')) {
                    if($request->sameday_dws_weight == 2){
                        DwsWeightChargesController::edit($id, 4, 2,Auth::id());
                    }else{
                        DwsWeightChargesController::edit($id, 4, 1,Auth::id());
                    }
        
                }else{
                    DwsWeightChargesController::delete_dws_rate($id, 4);
                }
            }

            WmsPendingUserInformation::where('user_id', $id)->delete();
            WmsPendingPerProductCharge::where('user_id', $id)->delete();
            WmsPendingPerSquareFootCharge::where('user_id', $id)->delete();
            WmsPendingStorageTypeCharge::where('user_id', $id)->delete();
            WmsPendingPackingCharge::where('user_id', $id)->delete();
            WmsPendingLabellingCharge::where('user_id', $id)->delete();
            if($request->has('warehouse_main_switch') && $request->warehouse_main_switch == 'on'){

                $wms_user_info = new WmsPendingUserInformation();
                $wms_user_info->user_id = $id;
                $wms_user_info->warehousing = 1;
                $wms_user_info->invoicing_cycle = $request->invoicing_cycle;
                $wms_user_info->invoicing_date = 1;
                $wms_user_info->per_product_charges = ($request->has('ppc_switch'))? 1:0;
                $wms_user_info->per_square_foot_charges = ($request->has('psf_switch'))? 1:0;
                $wms_user_info->packing_charges = ($request->has('packing_charges_switch'))? 1:0;
                $wms_user_info->labelling_charges = ($request->has('labelling_charges_switch'))? 1:0;
                $wms_user_info->storage_charges = ($request->has('storage_charges_switch'))? 1:0;
                $wms_user_info->save();

                if($request->has('ppc_switch')){
                    $ppc = new WmsPendingPerProductCharge();
                    $ppc->user_id = $id;
                    $ppc->charges = $request->ppc_charges;
                    $ppc->save();
                }
                if($request->has('psf_switch')){
                    $psf = new WmsPendingPerSquareFootCharge();
                    $psf->user_id = $id;
                    $psf->charges = $request->psf_charges;
                    $psf->save();
                }
                if($request->has('storage_charges_switch')){
                    foreach ($request->storage_type as $key => $storage_type) {
                        $storage_charges = new WmsPendingStorageTypeCharge();
                        $storage_charges->user_id = $id;
                        $storage_charges->storage_type_id = $storage_type;
                        $storage_charges->charges = $request->storage_type_charges[$key];
                        $storage_charges->save();
                    }
                }

                if($request->has('packing_charges_switch')){
                    foreach ($request->packing_type as $key => $packing) {
                        $ptype = new WmsPendingPackingCharge();
                        $ptype->user_id = $id;
                        $ptype->packing_type_id = $packing;
                        $ptype->packing_size_id = $request->packing_size[$key];
                        $ptype->charges = $request->packing_charges[$key];
                        $ptype->save();
                    }
                }

                if($request->has('labelling_charges_switch')){
                    $labelling = new WmsPendingLabellingCharge();
                    $labelling->user_id = $id;
                    $labelling->charges = $request->labelling_charges;
                    $labelling->save();
                }
            }

            //dd($weightAlready);

            if ($request->approve == 1) {
                $user = User::find($id);
                
                DwsWeightChargesController::approve($id);

                if($rate_origin_hubs = RateOriginHub::where('user_id', $id)->get()) {
                    foreach ($rate_origin_hubs as $rate_origin_hub) {
                        $history_rate_origin_hub = new HistoryRateOriginHub();
                        $history_rate_origin_hub->user_id = $rate_origin_hub->user_id;
                        $history_rate_origin_hub->shipping_mode_id = $rate_origin_hub->shipping_mode_id;
                        $history_rate_origin_hub->city_id = $rate_origin_hub->city_id;
                        $history_rate_origin_hub->save();
                    }
                }
                if($rate_destination_hubs = RateDestinationHub::where('user_id', $id)->get()) {
                    foreach ($rate_destination_hubs as $rate_destination_hub) {
                        $history_rate_destination_hub = new HistoryRateDestinationHub();
                        $history_rate_destination_hub->user_id = $rate_destination_hub->user_id;
                        $history_rate_destination_hub->shipping_mode_id = $rate_destination_hub->shipping_mode_id;
                        $history_rate_destination_hub->city_id = $rate_destination_hub->city_id;
                        $history_rate_destination_hub->save();
                    }
                }
                if($switches = RateStatus::where(['user_id' => $id , 'shipping_mode_id' => 1])->first()) {

                    HistoryRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'status' => $switches['status'],
                        'cash_handling_charges' => $switches['cash_handling_charges'],
                        'insurance_charges' => $switches['insurance_charges'],
                        'return_charges' => $switches['return_charges'],
                        'fuel_charges' => $switches['fuel_charges']
                    ]);
                }
                if($switches = RateStatus::where(['user_id' => $id , 'shipping_mode_id' => 2])->first()) {
                    HistoryRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'status' => $switches['status'],
                        'cash_handling_charges' => $switches['cash_handling_charges'],
                        'insurance_charges' => $switches['insurance_charges'],
                        'return_charges' => $switches['return_charges'],
                        'fuel_charges' => $switches['fuel_charges']
                    ]);
                }
                if($switches = RateStatus::where(['user_id' => $id , 'shipping_mode_id' => 3])->first()) {
                    HistoryRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'status' => $switches['status'],
                        'cash_handling_charges' => $switches['cash_handling_charges'],
                        'insurance_charges' => $switches['insurance_charges'],
                        'return_charges' => $switches['return_charges'],
                        'fuel_charges' => $switches['fuel_charges']
                    ]);
                }
                if($switches = RateStatus::where(['user_id' => $id , 'shipping_mode_id' => 4])->first()) {
                    HistoryRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'status' => $switches['status'],
                        'cash_handling_charges' => $switches['cash_handling_charges'],
                        'insurance_charges' => $switches['insurance_charges'],
                        'return_charges' => $switches['return_charges'],
                        'fuel_charges' => $switches['fuel_charges']
                    ]);
                }
                if($weights = WeightCharge::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
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
                if($weights = WeightCharge::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
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
                if($weights = WeightCharge::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
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
                if($weights = WeightCharge::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
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
                if($bookingTypes = BookingTypeCharges::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
                    foreach ($bookingTypes as $bookingType) {
                        HistoryBookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'replacement_charges' => $bookingType['replacement_charges'],
                            'try_and_buy_charges' => $bookingType['try_and_buy_charges']
                        ]);
                    }
                }
                if($bookingTypes = BookingTypeCharges::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
                    foreach ($bookingTypes as $bookingType) {
                        HistoryBookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'replacement_charges' => $bookingType['replacement_charges'],
                            'try_and_buy_charges' => $bookingType['try_and_buy_charges']
                        ]);
                    }
                }
                if($bookingTypes = BookingTypeCharges::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
                    foreach ($bookingTypes as $bookingType) {
                        HistoryBookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'replacement_charges' => $bookingType['replacement_charges'],
                            'try_and_buy_charges' => $bookingType['try_and_buy_charges']
                        ]);
                    }
                }
                if($bookingTypes = BookingTypeCharges::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
                    foreach ($bookingTypes as $bookingType) {
                        HistoryBookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'replacement_charges' => $bookingType['replacement_charges'],
                            'try_and_buy_charges' => $bookingType['try_and_buy_charges']
                        ]);
                    }
                }

                if($cashs = CashHandlingCharge::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
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
                if($cashs = CashHandlingCharge::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
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
                if($cashs = CashHandlingCharge::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
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
                if($cashs = CashHandlingCharge::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
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
                if($insurances = InsuranceCharge::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
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
                if($insurances = InsuranceCharge::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
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
                if($insurances = InsuranceCharge::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
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
                if($insurances = InsuranceCharge::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
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
                if($returns = ReturnCharge::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
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
                if($returns = ReturnCharge::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
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
                if($returns = ReturnCharge::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
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
                if($returns = ReturnCharge::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
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
                if($fuels = FuelSurcharge::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
                    foreach ($fuels as $fuel) {
                        HistoryFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'fuel_surcharge' => $fuel['fuel_surcharge']
                        ]);
                    }
                }
                if($fuels = FuelSurcharge::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
                    foreach ($fuels as $fuel) {
                        HistoryFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'fuel_surcharge' => $fuel['fuel_surcharge']
                        ]);
                    }
                }
                if($fuels = FuelSurcharge::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
                    foreach ($fuels as $fuel) {
                        HistoryFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'fuel_surcharge' => $fuel['fuel_surcharge']
                        ]);
                    }
                }
                if($fuels = FuelSurcharge::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
                    foreach ($fuels as $fuel) {
                        HistoryFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'fuel_surcharge' => $fuel['fuel_surcharge']
                        ]);
                    }
                }
                if($packagings = PackagingCharge::where('user_id', '=', $id)->get()) {
                    foreach ($packagings as $packaging) {
                        $packaging_charges = new HistoryPackagingCharge();
                        $packaging_charges->user_id = $id;
                        $packaging_charges->type_id = $packaging->type_id;
                        $packaging_charges->size_id = $packaging->size_id;
                        $packaging_charges->charges = $packaging->charges;
                        $packaging_charges->save();
                    }
                }
                if($packagings = PackagingCharge::where('user_id', '=', $id)->get()) {
                    foreach ($packagings as $packaging) {
                        $packaging_charges = new HistoryPackagingCharge();
                        $packaging_charges->user_id = $id;
                        $packaging_charges->type_id = $packaging->type_id;
                        $packaging_charges->size_id = $packaging->size_id;
                        $packaging_charges->charges = $packaging->charges;
                        $packaging_charges->save();
                    }
                }
                if($packagings = PackagingCharge::where('user_id', '=', $id)->get()) {
                    foreach ($packagings as $packaging) {
                        $packaging_charges = new HistoryPackagingCharge();
                        $packaging_charges->user_id = $id;
                        $packaging_charges->type_id = $packaging->type_id;
                        $packaging_charges->size_id = $packaging->size_id;
                        $packaging_charges->charges = $packaging->charges;
                        $packaging_charges->save();
                    }
                }
                if($packagings = PackagingCharge::where('user_id', '=', $id)->get()) {
                    foreach ($packagings as $packaging) {
                        $packaging_charges = new HistoryPackagingCharge();
                        $packaging_charges->user_id = $id;
                        $packaging_charges->type_id = $packaging->type_id;
                        $packaging_charges->size_id = $packaging->size_id;
                        $packaging_charges->charges = $packaging->charges;
                        $packaging_charges->save();
                    }
                }
                if($discounts = DiscountCharge::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
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
                if($discounts = DiscountCharge::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
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
                if($discounts = DiscountCharge::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
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
                if($discounts = DiscountCharge::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
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
                $s = RateStatus::where(['user_id' => $id ])->first();
                RateHistory::create([
                    'user_id' => $id,
                    'updated_by' => $user['rates_updated_by'],
                    'approved_by' => $user['rates_authorized_by'],
                    'from_date' => $s['created_at'],
                    'to_date' => Carbon::now()
                ]);

                if($wms_user_info = WmsUserInformation::where('user_id', $id)->first()){
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
                if($ppc = WmsPerProductCharge::where('user_id', $id)->first()){
                    $ppc_history = new WmsHistoryPerProductCharge();
                    $ppc_history->user_id = $ppc['user_id'];
                    $ppc_history->charges = $ppc['charges'];
                    $ppc_history->save();
                }
                if($psf = WmsPerSquareFootCharge::where('user_id', $id)->first()){
                    $psf_history = new WmsHistoryPerSquareFootCharge();
                    $psf_history->user_id = $psf['user_id'];
                    $psf_history->charges = $psf['charges'];
                    $psf_history->save();
                }

                if($storage_type_charges = WmsStorageTypeCharge::where('user_id', $id)->get()){
                    foreach ($storage_type_charges as $storage_charges) {
                        $history_storage_charge = new WmsHistoryStorageTypeCharge();
                        $history_storage_charge->user_id = $id;
                        $history_storage_charge->storage_type_id = $storage_charges['storage_type_id'];
                        $history_storage_charge->charges = $storage_charges['charges'];
                        $history_storage_charge->save();
                    }
                }

                if($packing_charges = WmsPackingCharge::where('user_id', $id)->get()){
                    foreach ($packing_charges as $packing_charges) {
                        $history_packing_charge = new WmsHistoryPackingCharge();
                        $history_packing_charge->user_id = $id;
                        $history_packing_charge->packing_type_id = $packing_charges['packing_type_id'];
                        $history_packing_charge->packing_size_id = $packing_charges['packing_size_id'];
                        $history_packing_charge->charges = $packing_charges['charges'];
                        $history_packing_charge->save();
                    }
                }

                if($labelling = WmsLabellingCharge::where('user_id', $id)->first()){
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

                RateStatus::where('user_id', $id)->delete();
                WeightCharge::where('user_id', $id)->delete();
                BookingTypeCharges::where('user_id', $id)->delete();
                CashHandlingCharge::where('user_id', $id)->delete();
                InsuranceCharge::where('user_id', $id)->delete();
                ReturnCharge::where('user_id', $id)->delete();
                FuelSurcharge::where('user_id', $id)->delete();
                PackagingCharge::where('user_id', $id)->delete();
                DiscountCharge::where('user_id', $id)->delete();

                RateOriginHub::where('user_id', $id)->delete();
                RateDestinationHub::where('user_id', $id)->delete();

                if($pending_rate_origin_hubs = PendingRateOriginHub::where('user_id', $id)->get()) {
                    foreach ($pending_rate_origin_hubs as $pending_rate_origin_hub) {
                        $rate_origin_hub = new RateOriginHub();
                        $rate_origin_hub->user_id = $pending_rate_origin_hub->user_id;
                        $rate_origin_hub->shipping_mode_id = $pending_rate_origin_hub->shipping_mode_id;
                        $rate_origin_hub->city_id = $pending_rate_origin_hub->city_id;
                        $rate_origin_hub->save();
                    }
                }
                if($pending_rate_destination_hubs = PendingRateDestinationHub::where('user_id', $id)->get()) {
                    foreach ($pending_rate_destination_hubs as $pending_rate_destination_hub) {
                        $rate_destination_hub = new RateDestinationHub();
                        $rate_destination_hub->user_id = $pending_rate_destination_hub->user_id;
                        $rate_destination_hub->shipping_mode_id = $pending_rate_destination_hub->shipping_mode_id;
                        $rate_destination_hub->city_id = $pending_rate_destination_hub->city_id;
                        $rate_destination_hub->save();
                    }
                }
                if($pendingswitchs = PendingRateStatus::where(['user_id' => $id , 'shipping_mode_id' => 1])->first()) {
                    RateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'status' => $pendingswitchs['status'],
                        'cash_handling_charges' => $pendingswitchs['cash_handling_charges'],
                        'insurance_charges' => $pendingswitchs['insurance_charges'],
                        'return_charges' => $pendingswitchs['return_charges'],
                        'fuel_charges' => $pendingswitchs['fuel_charges']
                    ]);
                }
                if($pendingswitchs = PendingRateStatus::where(['user_id' => $id , 'shipping_mode_id' => 2])->first()) {
                    RateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'status' => $pendingswitchs['status'],
                        'cash_handling_charges' => $pendingswitchs['cash_handling_charges'],
                        'insurance_charges' => $pendingswitchs['insurance_charges'],
                        'return_charges' => $pendingswitchs['return_charges'],
                        'fuel_charges' => $pendingswitchs['fuel_charges']
                    ]);
                }
                if($pendingswitchs = PendingRateStatus::where(['user_id' => $id , 'shipping_mode_id' => 3])->first()) {
                    RateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'status' => $pendingswitchs['status'],
                        'cash_handling_charges' => $pendingswitchs['cash_handling_charges'],
                        'insurance_charges' => $pendingswitchs['insurance_charges'],
                        'return_charges' => $pendingswitchs['return_charges'],
                        'fuel_charges' => $pendingswitchs['fuel_charges']
                    ]);
                }
                if($pendingswitchs = PendingRateStatus::where(['user_id' => $id , 'shipping_mode_id' => 4])->first()) {
                    RateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'status' => $pendingswitchs['status'],
                        'cash_handling_charges' => $pendingswitchs['cash_handling_charges'],
                        'insurance_charges' => $pendingswitchs['insurance_charges'],
                        'return_charges' => $pendingswitchs['return_charges'],
                        'fuel_charges' => $pendingswitchs['fuel_charges']
                    ]);
                }
                if($pendingweights = PendingWeightCharge::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
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
                if($pendingweights = PendingWeightCharge::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
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
                if($pendingweights = PendingWeightCharge::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
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
                if($pendingweights = PendingWeightCharge::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
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
                if($pendingbookingTypes = PendingBookingTypeCharges::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
                    foreach ($pendingbookingTypes as $pendingbookingType) {
                        BookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'replacement_charges' => $pendingbookingType['replacement_charges'],
                            'try_and_buy_charges' => $pendingbookingType['try_and_buy_charges']
                        ]);
                    }
                }
                if($pendingbookingTypes = PendingBookingTypeCharges::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
                    foreach ($pendingbookingTypes as $pendingbookingType) {
                        BookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'replacement_charges' => $pendingbookingType['replacement_charges'],
                            'try_and_buy_charges' => $pendingbookingType['try_and_buy_charges']
                        ]);
                    }
                }
                if($pendingbookingTypes = PendingBookingTypeCharges::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
                    foreach ($pendingbookingTypes as $pendingbookingType) {
                        BookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'replacement_charges' => $pendingbookingType['replacement_charges'],
                            'try_and_buy_charges' => $pendingbookingType['try_and_buy_charges']
                        ]);
                    }
                }
                if($pendingbookingTypes = PendingBookingTypeCharges::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
                    foreach ($pendingbookingTypes as $pendingbookingType) {
                        BookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'replacement_charges' => $pendingbookingType['replacement_charges'],
                            'try_and_buy_charges' => $pendingbookingType['try_and_buy_charges']
                        ]);
                    }
                }
                if($pendingcashs = PendingCashHandlingCharge::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
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
                if($pendingcashs = PendingCashHandlingCharge::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
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
                if($pendingcashs = PendingCashHandlingCharge::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
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
                if($pendingcashs = PendingCashHandlingCharge::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
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
                if($pendinginsurances = PendingInsuranceCharge::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
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
                if($pendinginsurances = PendingInsuranceCharge::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
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
                if($pendinginsurances = PendingInsuranceCharge::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
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
                if($pendinginsurances = PendingInsuranceCharge::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
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
                if($pendingreturns = PendingReturnCharge::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
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
                if($pendingreturns = PendingReturnCharge::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
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
                if($pendingreturns = PendingReturnCharge::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
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
                if($pendingreturns = PendingReturnCharge::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
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
                if($pendingfuels = PendingFuelSurcharge::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
                    foreach ($pendingfuels as $pendingfuel) {
                        FuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'fuel_surcharge' => $pendingfuel['fuel_surcharge']
                        ]);
                    }
                }
                if($pendingfuels = PendingFuelSurcharge::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
                    foreach ($pendingfuels as $pendingfuel) {
                        FuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'fuel_surcharge' => $pendingfuel['fuel_surcharge']
                        ]);
                    }
                }
                if($pendingfuels = PendingFuelSurcharge::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
                    foreach ($pendingfuels as $pendingfuel) {
                        FuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'fuel_surcharge' => $pendingfuel['fuel_surcharge']
                        ]);
                    }
                }
                if($pendingfuels = PendingFuelSurcharge::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
                    foreach ($pendingfuels as $pendingfuel) {
                        FuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'fuel_surcharge' => $pendingfuel['fuel_surcharge']
                        ]);
                    }
                }
                if($pendingpackagings = PendingPackagingCharge::where('user_id', '=', $id)->get()) {
                    foreach ($pendingpackagings as $pendingpackaging) {
                        $packaging_charges = new PackagingCharge();
                        $packaging_charges->user_id = $id;
                        $packaging_charges->type_id = $pendingpackaging->type_id;
                        $packaging_charges->size_id = $pendingpackaging->size_id;
                        $packaging_charges->charges = $pendingpackaging->charges;
                        $packaging_charges->save();
                    }
                }
                if($pendingdiscounts = PendingDiscountCharge::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
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
                if($pendingdiscounts = PendingDiscountCharge::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
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
                if($pendingdiscounts = PendingDiscountCharge::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
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
                if($pendingdiscounts = PendingDiscountCharge::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
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

                if($wms_user_info = WmsPendingUserInformation::where('user_id', $id)->first()){
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
                if($ppc = WmsPendingPerProductCharge::where('user_id', $id)->first()){
                    $ppc_history = new WmsPerProductCharge();
                    $ppc_history->user_id = $ppc['user_id'];
                    $ppc_history->charges = $ppc['charges'];
                    $ppc_history->save();
                }
                if($psf = WmsPendingPerSquareFootCharge::where('user_id', $id)->first()){
                    $psf_history = new WmsPerSquareFootCharge();
                    $psf_history->user_id = $psf['user_id'];
                    $psf_history->charges = $psf['charges'];
                    $psf_history->save();
                }

                if($storage_type_charges = WmsPendingStorageTypeCharge::where('user_id', $id)->get()){
                    foreach ($storage_type_charges as $storage_charges) {
                        $history_storage_charge = new WmsStorageTypeCharge();
                        $history_storage_charge->user_id = $id;
                        $history_storage_charge->storage_type_id = $storage_charges['storage_type_id'];
                        $history_storage_charge->charges = $storage_charges['charges'];
                        $history_storage_charge->save();
                    }
                }

                if($packing_charges = WmsPendingPackingCharge::where('user_id', $id)->get()){
                    foreach ($packing_charges as $packing_charges) {
                        $history_packing_charge = new WmsPackingCharge();
                        $history_packing_charge->user_id = $id;
                        $history_packing_charge->packing_type_id = $packing_charges['packing_type_id'];
                        $history_packing_charge->packing_size_id = $packing_charges['packing_size_id'];
                        $history_packing_charge->charges = $packing_charges['charges'];
                        $history_packing_charge->save();
                    }
                }

                if($labelling = WmsPendingLabellingCharge::where('user_id', $id)->first()){
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

                PendingRateStatus::where('user_id', $id)->delete();
                PendingWeightCharge::where('user_id', $id)->delete();
                PendingBookingTypeCharges::where('user_id', $id)->delete();
                PendingCashHandlingCharge::where('user_id', $id)->delete();
                PendingInsuranceCharge::where('user_id', $id)->delete();
                PendingReturnCharge::where('user_id', $id)->delete();
                PendingFuelSurcharge::where('user_id', $id)->delete();
                PendingPackagingCharge::where('user_id', $id)->delete();
                PendingDiscountCharge::where('user_id', $id)->delete();
                PendingRateOriginHub::where('user_id', $id)->delete();
                PendingRateDestinationHub::where('user_id', $id)->delete();
                User::where('id', $id)->update(['rate_status' => 0,'agreement_signed' => 0, 'rates_authorized_by' => Auth::id(),'rates_approved_at'=>Carbon::now()]);
                if($request->has('rate_remarks') && $request->rate_remarks != null){
                    $rate_remark = new RateRemark();
                    $rate_remark->user_id = $id;
                    $rate_remark->remarks = $request->rate_remarks;
                    $rate_remark->admin_id = Auth::id();
                    $rate_remark->save();

                }



                if($request->total_commission == 1){
                    $existing_sale_commission = SalesCommission::where('shipper_id', $id)->first();
                    if($existing_sale_commission){
                        SalesCommissionUser::where('sales_commission_id', $existing_sale_commission->id)->delete();
                        SalesCommissionExternalUser::where('shipper_id',$id)->delete();
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
                    foreach($request->tier_id as $row_id => $tier){
                        $sales_tier = SalesTier::find($tier);
                        if($sales_tier){
                            $sales_commission_user = new SalesCommissionUser();
                            $sales_commission_user->sales_commission_id = $sales_commission_id;
                            $sales_commission_user->tier_type_id = $sales_tier->tier_type;
                            $sales_commission_user->tier_id = $tier;
                            if($sales_tier->tier_type == 1){
                                $sales_commission_user->user_id = $request->user_id[$row_id];
                            }else if($sales_tier->tier_type == 2){
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
                }
                else{
                    $existing_sale_commission = SalesCommission::where('shipper_id', $id)->first();
                    if($existing_sale_commission){
                        SalesCommissionUser::where('sales_commission_id', $existing_sale_commission->id)->delete();
                        SalesCommissionExternalUser::where('shipper_id',$id)->delete();
                        SalesCommission::where('shipper_id', $id)->delete();
                    }
                }
                return redirect(route('admin.accounts.active'))->with('success', 'User Rates is now approved.');
            }
            User::where('id', $id)->update(['rate_status' => 1, 'rates_updated_by' => Auth::id()]);
            if($request->has('rate_remarks') && $request->rate_remarks != null){
                $rate_remark = new RateRemark();
                $rate_remark->user_id = $id;
                $rate_remark->remarks = $request->rate_remarks;
                $rate_remark->admin_id = Auth::id();
                $rate_remark->save();
            }

            if($request->has('edit_commission') && $request->edit_commission == 1){
                if($request->total_commission > 0){
                    $existing_sale_commission = SalesCommission::where('shipper_id', $id)->first();
                    if($existing_sale_commission){
                        SalesCommissionUser::where('sales_commission_id', $existing_sale_commission->id)->delete();
                        SalesCommissionExternalUser::where('shipper_id',$id)->delete();
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
                    foreach($request->tier_id as $row_id => $tier){
                        $sales_tier = SalesTier::find($tier);
                        if($sales_tier){
                            $sales_commission_user = new SalesCommissionUser();
                            $sales_commission_user->sales_commission_id = $sales_commission_id;
                            $sales_commission_user->tier_type_id = $sales_tier->tier_type;
                            $sales_commission_user->tier_id = $tier;
                            if($sales_tier->tier_type == 1){
                                $sales_commission_user->user_id = $request->user_id[$row_id];
                            }else if($sales_tier->tier_type == 2){
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
                }
                else{
                    $existing_sale_commission = SalesCommission::where('shipper_id', $id)->first();
                    if($existing_sale_commission){
                        SalesCommissionUser::where('sales_commission_id', $existing_sale_commission->id)->delete();
                        SalesCommissionExternalUser::where('shipper_id',$id)->delete();
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
    public function addRates(Request $request, $id){

        $messages = [
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
            'on_discount_title.required_with'=>'The overnight discount title field is required',
            'on_daterange.required_with'=>'The overnight discount date field is required',
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
            'ol_discount_title.required_with'=>'The overland discount title field is required',
            'ol_daterange.required_with'=>'The overland discount date field is required',
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
            'detain_discount_title.required_with'=>'The detain discount title field is required',
            'detain_daterange.required_with'=>'The detain discount date field is required',
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
            'sameday_discount_title.required_with'=>'The sameday discount title field is required',
            'sameday_daterange.required_with'=>'The sameday discount date field is required',
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
        ];

        $validations = array();
        $on_validations = array();
        $ol_validations = array();
        $detain_validations = array();
        $sameday_validations = array();

        $shipper_id = $id;
        if($request->has('on_main_switch') && $request->on_main_switch == 'on'){
            $on_validations = [
                'on_wa_range_up.*' => 'required|numeric|between:0,100000',
                'on_wa_range_down.*' => 'required|numeric|between:0,100000',
                'on_wa_local_charges.*' => 'required|numeric',
                'on_class_0_charges.*' => 'required|numeric',
                'on_class_1_charges.*' => 'required',
                'on_class_2_charges.*' => 'required',
                'on_class_3_charges.*' => 'required',
                'on_wa_spkg.*'=>'numeric',
                'on_replacement_charges'=>'required|numeric',
                'on_tnb_charges'=>'required|numeric',
                'on_cash_range_up.*'=>'required_if:on_cash_handling_switch,==,on|numeric',
                'on_cash_range_down.*'=>'required_if:on_cash_handling_switch,==,on|numeric',
                'on_cash_charges.*'=>'required_if:on_cash_handling_switch,==,on',
                'on_ins_range_up.*'=>'required_if:on_insurance_charges_switch,==,on|numeric',
                'on_ins_range_down.*'=>'required_if:on_insurance_charges_switch,==,on|numeric',
                'on_ins_charges.*'=>'required_if:on_insurance_charges_switch,==,on',
                'on_return_local_charges.*'=>'required_if:on_return_switch,==,on|numeric',
                'on_return_class_0_charges.*'=>'required_if:on_return_switch,==,on|numeric',
                'on_return_class_1_charges.*'=>'required_if:on_return_switch,==,on|numeric',
                'on_return_class_2_charges.*'=>'required_if:on_return_switch,==,on|numeric',
                'on_return_class_3_charges.*'=>'required_if:on_return_switch,==,on|numeric',
                'overnight_fuel_surcharge'=>'required_if:overnight_fuel_switch,==,on|numeric',

                'on_discount_title'=>'required_with:on_discount_weight_rate,on_discount_cash_rate,on_discount_insurance_rate,on_discount_return_rate,on_discount_packaging_rate',
                'on_daterange'=>'required_with:on_discount_weight_rate,on_discount_cash_rate,on_discount_insurance_rate,on_discount_return_rate,on_discount_packaging_rate',
                'on_discount_weight_rate'=>'required_if:on_discount_weight_switch,==,on',
                'on_discount_cash_rate'=>'required_if:on_discount_cash_switch,==,on',
                'on_discount_insurance_rate'=>'required_if:on_discount_insurance_switch,==,on',
                'on_discount_return_rate'=>'required_if:on_discount_return_switch,==,on',
                'on_discount_packaging_rate'=>'required_if:on_discount_packaging_switch,==,on'
            ];
        }
        //overland
        if($request->has('ol_main_switch') && $request->ol_main_switch == 'on'){
            $ol_validations = [
                'ol_wa_range_up.*' => 'required|numeric|between:0,100000',
                'ol_wa_range_down.*' => 'required|numeric|between:0,100000',
                'ol_wa_local_charges.*' => 'required|numeric',
                'ol_class_0_charges.*' => 'required|numeric',
                'ol_class_1_charges.*' => 'required',
                'ol_class_2_charges.*' => 'required',
                'ol_class_3_charges.*' => 'required',
                'ol_wa_spkg.*'=>'numeric',
                'ol_replacement_charges'=>'required|numeric',
                'ol_tnb_charges'=>'required|numeric',
                'ol_cash_range_up.*'=>'required_if:ol_cash_handling_switch,==,on|numeric',
                'ol_cash_range_down.*'=>'required_if:ol_cash_handling_switch,==,on|numeric',
                'ol_cash_charges.*'=>'required_if:ol_cash_handling_switch,==,on',
                'ol_ins_range_up.*'=>'required_if:ol_insurance_charges_switch,==,on|numeric',
                'ol_ins_range_down.*'=>'required_if:ol_insurance_charges_switch,==,on|numeric',
                'ol_ins_charges.*'=>'required_if:ol_insurance_charges_switch,==,on',
                'ol_return_local_charges.*'=>'required_if:ol_return_switch,==,on|numeric',
                'ol_return_class_0_charges.*'=>'required_if:ol_return_switch,==,on|numeric',
                'ol_return_class_1_charges.*'=>'required_if:ol_return_switch,==,on|numeric',
                'ol_return_class_2_charges.*'=>'required_if:ol_return_switch,==,on|numeric',
                'ol_return_class_3_charges.*'=>'required_if:ol_return_switch,==,on|numeric',
                'overland_fuel_surcharge'=>'required_if:overland_fuel_switch,==,on|numeric',

                'ol_discount_title'=>'required_with:ol_discount_weight_rate,ol_discount_cash_rate,ol_discount_insurance_rate,ol_discount_return_rate,ol_discount_packaging_rate',
                'ol_daterange'=>'required_with:ol_discount_weight_rate,ol_discount_cash_rate,ol_discount_insurance_rate,ol_discount_return_rate,ol_discount_packaging_rate',
                'ol_discount_weight_rate'=>'required_if:ol_discount_weight_switch,==,on',
                'ol_discount_cash_rate'=>'required_if:ol_discount_cash_switch,==,on',
                'ol_discount_insurance_rate'=>'required_if:ol_discount_insurance_switch,==,on',
                'ol_discount_return_rate'=>'required_if:ol_discount_return_switch,==,on',
                'ol_discount_packaging_rate'=>'required_if:ol_discount_packaging_switch,==,on',
            ];
        }
        //overland
        if($request->has('detain_main_switch') && $request->detain_main_switch == 'on'){
            $detain_validations = [
                'detain_wa_range_up.*' => 'required|numeric|between:0,100000',
                'detain_wa_range_down.*' => 'required|numeric|between:0,100000',
                'detain_wa_local_charges.*' => 'required|numeric',
                'detain_class_0_charges.*' => 'required|numeric',
                'detain_class_1_charges.*' => 'required',
                'detain_class_2_charges.*' => 'required',
                'detain_class_3_charges.*' => 'required',
                'detain_wa_spkg.*'=>'numeric',
                'detain_replacement_charges'=>'required|numeric',
                'detain_tnb_charges'=>'required|numeric',
                'detain_cash_range_up.*'=>'required_if:detain_cash_handling_switch,==,on|numeric',
                'detain_cash_range_down.*'=>'required_if:detain_cash_handling_switch,==,on|numeric',
                'detain_cash_charges.*'=>'required_if:detain_cash_handling_switch,==,on',
                'detain_ins_range_up.*'=>'required_if:detain_insurance_charges_switch,==,on|numeric',
                'detain_ins_range_down.*'=>'required_if:detain_insurance_charges_switch,==,on|numeric',
                'detain_ins_charges.*'=>'required_if:detain_insurance_charges_switch,==,on',
                'detain_return_local_charges.*'=>'required_if:detain_return_switch,==,on|numeric',
                'detain_return_class_0_charges.*'=>'required_if:detain_return_switch,==,on|numeric',
                'detain_return_class_1_charges.*'=>'required_if:detain_return_switch,==,on|numeric',
                'detain_return_class_2_charges.*'=>'required_if:detain_return_switch,==,on|numeric',
                'detain_return_class_3_charges.*'=>'required_if:detain_return_switch,==,on|numeric',
                'detain_fuel_surcharge'=>'required_if:detain_fuel_switch,==,on|numeric',

                'detain_discount_title'=>'required_with:detain_discount_weight_rate,detain_discount_cash_rate,detain_discount_insurance_rate,detain_discount_return_rate,detain_discount_packaging_rate',
                'detain_daterange'=>'required_with:detain_discount_weight_rate,detain_discount_cash_rate,detain_discount_insurance_rate,detain_discount_return_rate,detain_discount_packaging_rate',
                'detain_discount_weight_rate'=>'required_if:detain_discount_weight_switch,==,on',
                'detain_discount_cash_rate'=>'required_if:detain_discount_cash_switch,==,on',
                'detain_discount_insurance_rate'=>'required_if:detain_discount_insurance_switch,==,on',
                'detain_discount_return_rate'=>'required_if:detain_discount_return_switch,==,on',
                'detain_discount_packaging_rate'=>'required_if:detain_discount_packaging_switch,==,on',
            ];
        }
        //sameday
        if($request->has('sameday_main_switch') && $request->sameday_main_switch == 'on'){
            $sameday_validations = [
                'sameday_wa_range_up.*' => 'required|numeric|between:0,100000',
                'sameday_wa_range_down.*' => 'required|numeric|between:0,100000',
                'sameday_wa_local_charges.*' => 'required|numeric',
                'sameday_class_0_charges.*' => 'required|numeric',
                'sameday_wa_spkg.*'=>'numeric',
                'sameday_replacement_charges'=>'required|numeric',
                'sameday_tnb_charges'=>'required|numeric',
                'sameday_cash_range_up.*'=>'required_if:sameday_cash_handling_switch,==,on|numeric',
                'sameday_cash_range_down.*'=>'required_if:sameday_cash_handling_switch,==,on|numeric',
                'sameday_cash_charges.*'=>'required_if:sameday_cash_handling_switch,==,on',
                'sameday_ins_range_up.*'=>'required_if:sameday_insurance_charges_switch,==,on|numeric',
                'sameday_ins_range_down.*'=>'required_if:sameday_insurance_charges_switch,==,on|numeric',
                'sameday_ins_charges.*'=>'required_if:sameday_insurance_charges_switch,==,on',
                'sameday_return_local_charges.*'=>'required_if:sameday_return_switch,==,on|numeric',
                'sameday_return_class_0_charges.*'=>'required_if:sameday_return_switch,==,on|numeric',
                'sameday_return_class_1_charges.*'=>'required_if:sameday_return_switch,==,on|numeric',
                'sameday_return_class_2charges.*'=>'required_if:sameday_return_switch,==,on|numeric',
                'sameday_return_class_3_charges.*'=>'required_if:sameday_return_switch,==,on|numeric',
                'sameday_fuel_surcharge'=>'required_if:sameday_fuel_switch,==,on|numeric',

                'sameday_discount_title'=>'required_with:sameday_discount_weight_rate,sameday_discount_cash_rate,sameday_discount_insurance_rate,sameday_discount_return_rate,sameday_discount_packaging_rate',
                'sameday_daterange'=>'required_with:sameday_discount_weight_rate,sameday_discount_cash_rate,sameday_discount_insurance_rate,sameday_discount_return_rate,sameday_discount_packaging_rate',
                'sameday_discount_weight_rate'=>'required_if:sameday_discount_weight_switch,==,on',
                'sameday_discount_cash_rate'=>'required_if:sameday_discount_cash_switch,==,on',
                'sameday_discount_insurance_rate'=>'required_if:sameday_discount_insurance_switch,==,on',
                'sameday_discount_return_rate'=>'required_if:sameday_discount_return_switch,==,on',
                'sameday_discount_packaging_rate'=>'required_if:sameday_discount_packaging_switch,==,on',
            ];
        }

        if($request->has('warehouse_main_switch') && $request->warehouse_main_switch == 'on'){
            $warehouse_validations = [
                'invoicing_cycle' => 'required|numeric',
                'invoicing_date.*' => 'required_if:invoicing_cycle,1,3',
                'ppc_charges'=>'required_if:ppc_switch,==,on',
                'psf_charges'=>'required_if:psf_switch,==,on',
                'storage_type.*'=>'required_if:storage_charges_switch,==,on',
                'storage_type_charges.*'=>'required_if:storage_charges_switch,==,on|numeric',
                'packing_type.*' => 'required_if:packing_charges_switch,==,on',
                'packing_charges.*' => 'required_if:packing_charges_switch,==,on|numeric',
                'labelling_charges.*' => 'required_if:labelling_charges_switch,==,on|numeric',
            ];
        }

        $validations = array_merge($on_validations, $ol_validations, $detain_validations, $sameday_validations);

        $validate = Validator::make($request->all(), $validations, $messages);

        if ($validate->fails()) {
            return redirect()->back()
                ->withErrors($validate)
                ->withInput();
        }


        if($request->has('on_main_switch') && $request->on_main_switch == 'on'){
            if($request->has('on_default') && $request->on_default == 'on'){
                $default_shipping_mode = User::where('id', $id)->update([
                    'default_shipping_mode' => 1
                ]);
            }
            $ONRateAlready = RateStatus::where('user_id',$id)->where('shipping_mode_id',1)->get();

            if($ONRateAlready->isEmpty()) {

                if($request->has('on_origin_hubs')) {
                    foreach($request->on_origin_hubs as $origin_id){
                        $rate_origin_hub = new RateOriginHub();
                        $rate_origin_hub->user_id = $id;
                        $rate_origin_hub->shipping_mode_id = 1;
                        $rate_origin_hub->city_id = $origin_id;
                        $rate_origin_hub->save();
                    }
                }
                if($request->has('on_destination_hubs')) {
                    foreach($request->on_destination_hubs as $destination_id){
                        $rate_destination_hub = new RateDestinationHub();
                        $rate_destination_hub->user_id = $id;
                        $rate_destination_hub->shipping_mode_id = 1;
                        $rate_destination_hub->city_id = $destination_id;
                        $rate_destination_hub->save();
                    }
                }
                RateStatus::create([
                    'user_id'=>$id,
                    'shipping_mode_id'=>1,
                    'status'=> ($request->has('on_main_switch'))? 1:0,
                    'cash_handling_charges'=> ($request->has('on_cash_handling_switch'))? 1:0,
                    'insurance_charges'=> ($request->has('on_insurance_charges_switch'))? 1:0,
                    'return_charges'=> ($request->has('on_return_switch'))? 1:0,
                    'fuel_charges'=> ($request->has('overnight_fuel_switch'))? 1:0
                ]);
                $wa_switch = array();
                $wa_spkg = array();
                foreach ($request->on_wa_range_up as $index => $on_wa_range_up) {
                    if($request->has('on_wa_switch')) {
                        if (array_key_exists($index, $request->on_wa_switch)) {
                            $wa_switch[$index] = 1;
                        } else {
                            $wa_switch[$index] = 0;
                        };
                    }else{
                        $wa_switch[$index] = 0;
                    }
                    if($request->has('on_wa_spkg')) {
                        if (array_key_exists($index, $request->on_wa_spkg)) {
                            $wa_spkg[$index] = $request->on_wa_spkg[$index];
                        } else {
                            $wa_spkg[$index] = 0.5;
                        };
                    }else{
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
                    'user_id'=>$id,
                    'shipping_mode_id'=>1,
                    'replacement_charges'=>$request->on_replacement_charges,
                    'try_and_buy_charges'=>$request->on_tnb_charges
                ]);
                //Cash handling Charges
                if($request->has('on_cash_handling_switch') && $request->on_cash_handling_switch == 'on'){
                    foreach ($request->on_cash_range_up as $ind => $on_cash_range_up){
                        CashHandlingCharge::create([
                            'user_id'=>$id,
                            'shipping_mode_id'=>1,
                            'range_up'=> $request->on_cash_range_up[$ind],
                            'range_down'=> $request->on_cash_range_down[$ind],
                            'charges'=> $request->on_cash_charges[$ind]
                        ]);
                    }
                }
                //insurance charges
                if($request->has('on_insurance_charges_switch') && $request->on_insurance_charges_switch == 'on'){
                    foreach ($request->on_ins_range_up as $insurance => $on_ins_range_up){
                        InsuranceCharge::create([
                            'user_id'=>$id,
                            'shipping_mode_id'=>1,
                            'range_up'=> $request->on_ins_range_up[$insurance],
                            'range_down'=> $request->on_ins_range_down[$insurance],
                            'charges'=> $request->on_ins_charges[$insurance]
                        ]);
                    }
                }
                //Return Charges
                if($request->has('on_return_switch') && $request->on_return_switch == 'on'){
                    ReturnCharge::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>1,
                        'local'=> $request->on_return_local_charges,
                        'national_charges_class_0'=> $request->on_return_class_0_charges,
                        'national_charges_class_1'=> $request->on_return_class_1_charges,
                        'national_charges_class_2'=> $request->on_return_class_2_charges,
                        'national_charges_class_3'=> $request->on_return_class_3_charges
                    ]);
                }
                //Return Charges
                if($request->has('overnight_fuel_switch') && $request->overnight_fuel_switch == 'on'){
                    FuelSurcharge::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>1,
                        'fuel_surcharge'=> $request->overnight_fuel_surcharge
                    ]);
                }

                $discount_cash = null;
                $discount_weight = null;
                $discount_insurance = null;
                $discount_return = null;
                $discount_packaging = null;

                if($request->has('on_discount_weight_switch') && $request->on_discount_weight_switch == 'on'){
                    $discount_weight = $request->on_discount_weight_rate != null ? $request->on_discount_weight_rate : null;
//                    $discount_weight = $request->on_discount_weight_rate;
                }
                if($request->has('on_discount_cash_switch') && $request->on_discount_cash_switch == 'on'){
                    $discount_cash = $request->on_discount_cash_rate != null ? $request->on_discount_cash_rate : null;
                }
                if($request->has('on_discount_insurance_switch') && $request->on_discount_insurance_switch == 'on'){
                    $discount_insurance = $request->on_discount_insurance_rate != null ? $request->on_discount_insurance_rate : null;
                }
                if($request->has('on_discount_return_switch') && $request->on_discount_return_switch == 'on'){
                    $discount_return = $request->on_discount_return_rate != null ? $request->on_discount_insurance_rate : null;
                }
                if($request->has('on_discount_packaging_switch') && $request->on_discount_packaging_switch == 'on'){
                    $discount_packaging = $request->on_discount_packaging_rate != null ? $request->on_discount_packaging_rate : null;
                }
                if($discount_weight != null || $discount_cash != null || $discount_insurance != null || $discount_return != null || $discount_packaging != null) {


                    $date_str = $request->on_daterange;
                    $date_sep = explode(' - ', $date_str);
                    $date_to = explode('/', $date_sep[0]);
                    $date_from = explode('/', $date_sep[1]);
                    $to = Carbon::create($date_to[2],$date_to[0],$date_to[1],0,0,0,'UTC')->toDateTimeString();
                    $from = Carbon::create($date_from[2],$date_from[0],$date_from[1],0,0,0,'UTC')->toDateTimeString();


                    DiscountCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'title'=> $request->on_discount_title,
                        'weight' => $discount_weight,
                        'cash' => $discount_cash,
                        'insurance' => $discount_insurance,
                        'return' => $discount_return,
                        'packaging' => $discount_packaging,
                        'to' => $to,
                        'from' => $from,
                        'added_by'=>Auth::id()
                    ]);
                }

            }
            //dd($weightAlready);
            if ($request->has('on_dws_weight')) {
                if($request->on_dws_weight == 2){
                    DwsWeightChargesController::add($id, 1, 2, Auth::id());

                }else{
                    DwsWeightChargesController::add($id, 1, 1, Auth::id());
                }
    
            }
        }
        //Overland
        if($request->has('ol_main_switch') && $request->ol_main_switch == 'on'){
            if($request->has('ol_default') && $request->ol_default == 'on'){
                $default_shipping_mode = User::where('id', $id)->update([
                    'default_shipping_mode' => 2
                ]);
            }

            $OLRatePresent = RateStatus::where('user_id',$id)->where('shipping_mode_id',2)->get();

            if($OLRatePresent->isEmpty()) {

                if($request->has('ol_origin_hubs')) {
                    foreach($request->ol_origin_hubs as $origin_id){
                        $rate_origin_hub = new RateOriginHub();
                        $rate_origin_hub->user_id = $id;
                        $rate_origin_hub->shipping_mode_id = 2;
                        $rate_origin_hub->city_id = $origin_id;
                        $rate_origin_hub->save();
                    }
                }
                if($request->has('ol_destination_hubs')) {
                    foreach($request->ol_destination_hubs as $destination_id){
                        $rate_destination_hub = new RateDestinationHub();
                        $rate_destination_hub->user_id = $id;
                        $rate_destination_hub->shipping_mode_id = 2;
                        $rate_destination_hub->city_id = $destination_id;
                        $rate_destination_hub->save();
                    }
                }

                RateStatus::create([
                    'user_id'=>$id,
                    'shipping_mode_id'=>2,
                    'status'=> ($request->has('ol_main_switch'))? 1:0,
                    'cash_handling_charges'=> ($request->has('ol_cash_handling_switch'))? 1:0,
                    'insurance_charges'=> ($request->has('ol_insurance_charges_switch'))? 1:0,
                    'return_charges'=> ($request->has('ol_return_switch'))? 1:0,
                    'fuel_charges'=> ($request->has('overland_fuel_switch'))? 1:0
                ]);
                $wa_switch_overland = array();
                $wa_spkg_overland = array();
                $standard_weight = StandardWeightCharge::where('shipping_mode_id',2)->pluck('kg_range')->toArray();
                foreach ($request->ol_wa_range_up as $index => $ol_wa_range_up) {
                    if($request->has('ol_wa_switch')) {
                        if (array_key_exists($index, $request->ol_wa_switch)) {
                            $wa_switch_overland[$index] = 1;
                        } else {
                            $wa_switch_overland[$index] = 0;
                        };
                    }else{
                        $wa_switch_overland[$index] = 0;
                    }
                    if($request->has('ol_wa_spkg')) {
                        if (array_key_exists($index, $request->ol_wa_spkg)) {
                            $wa_spkg_overland[$index] = $request->ol_wa_spkg[$index];
                        } else {
                            $wa_spkg_overland[$index] = isset($standard_weight[$index]) ? $standard_weight[$index] : 0;
                        };
                    }else{
                        $wa_spkg_overland[$index] =  isset($standard_weight[$index]) ? $standard_weight[$index] : 0;
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
                    'user_id'=>$id,
                    'shipping_mode_id'=>2,
                    'replacement_charges'=>$request->ol_replacement_charges,
                    'try_and_buy_charges'=>$request->ol_tnb_charges
                ]);
                //Cash handling Charges
                if($request->has('ol_cash_handling_switch') && $request->ol_cash_handling_switch == 'on'){
                    foreach ($request->ol_cash_range_up as $ind => $ol_cash_range_up){
                        CashHandlingCharge::create([
                            'user_id'=>$id,
                            'shipping_mode_id'=>2,
                            'range_up'=> $request->ol_cash_range_up[$ind],
                            'range_down'=> $request->ol_cash_range_down[$ind],
                            'charges'=> $request->ol_cash_charges[$ind]
                        ]);
                    }
                }
                //insurance charges
                if($request->has('ol_insurance_charges_switch') && $request->ol_insurance_charges_switch == 'on'){
                    foreach ($request->ol_ins_range_up as $insurance => $ol_ins_range_up){
                        InsuranceCharge::create([
                            'user_id'=>$id,
                            'shipping_mode_id'=>2,
                            'range_up'=> $request->ol_ins_range_up[$insurance],
                            'range_down'=> $request->ol_ins_range_down[$insurance],
                            'charges'=> $request->ol_ins_charges[$insurance]
                        ]);
                    }
                }
                //Return Charges
                if($request->has('ol_return_switch') && $request->ol_return_switch == 'on'){
                    ReturnCharge::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>2,
                        'local'=> $request->ol_return_local_charges,
                        'national_charges_class_0'=> $request->ol_return_class_0_charges,
                        'national_charges_class_1'=> $request->ol_return_class_1_charges,
                        'national_charges_class_2'=> $request->ol_return_class_2_charges,
                        'national_charges_class_3'=> $request->ol_return_class_3_charges
                    ]);
                }
                if($request->has('overland_fuel_switch') && $request->overland_fuel_switch == 'on'){
                    FuelSurcharge::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>2,
                        'fuel_surcharge'=> $request->overland_fuel_surcharge
                    ]);
                }

                $discount_cash = null;
                $discount_weight = null;
                $discount_insurance = null;
                $discount_return = null;
                $discount_packaging = null;

                if($request->has('ol_discount_weight_switch') && $request->ol_discount_weight_switch == 'on'){
                    $discount_weight = $request->ol_discount_weight_rate != null ? $request->ol_discount_weight_rate : null;
//                    $discount_weight = $request->ol_discount_weight_rate;
                }
                if($request->has('ol_discount_cash_switch') && $request->ol_discount_cash_switch == 'on'){
                    $discount_cash = $request->ol_discount_cash_rate != null ? $request->ol_discount_cash_rate : null;
                }
                if($request->has('ol_discount_insurance_switch') && $request->ol_discount_insurance_switch == 'on'){
                    $discount_insurance = $request->ol_discount_insurance_rate != null ? $request->ol_discount_insurance_rate : null;
                }
                if($request->has('ol_discount_return_switch') && $request->ol_discount_return_switch == 'on'){
                    $discount_return = $request->ol_discount_return_rate != null ? $request->ol_discount_insurance_rate : null;
                }
                if($request->has('ol_discount_packaging_switch') && $request->ol_discount_packaging_switch == 'on'){
                    $discount_packaging = $request->ol_discount_packaging_rate != null ? $request->ol_discount_packaging_rate : null;
                }
                if($discount_weight != null || $discount_cash != null || $discount_insurance != null || $discount_return != null || $discount_packaging != null) {


                    $date_str = $request->ol_daterange;
                    $date_sep = explode(' - ', $date_str);
                    $date_to = explode('/', $date_sep[0]);
                    $date_from = explode('/', $date_sep[1]);
                    $to = Carbon::create($date_to[2],$date_to[0],$date_to[1],0,0,0,'UTC')->toDateTimeString();
                    $from = Carbon::create($date_from[2],$date_from[0],$date_from[1],0,0,0,'UTC')->toDateTimeString();


                    DiscountCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'title'=> $request->ol_discount_title,
                        'weight' => $discount_weight,
                        'cash' => $discount_cash,
                        'insurance' => $discount_insurance,
                        'return' => $discount_return,
                        'packaging' => $discount_packaging,
                        'to' => $to,
                        'from' => $from,
                        'added_by'=>Auth::id()
                    ]);
                }

            }
            if ($request->has('ol_dws_weight')) {
                if($request->ol_dws_weight == 2){
                    DwsWeightChargesController::add($id, 2, 2, Auth::id());
                    
                }else{
                    DwsWeightChargesController::add($id, 2, 1, Auth::id());

                }
    
            }
        }
        //Detain
        if($request->has('detain_main_switch') && $request->detain_main_switch == 'on') {
            if($request->has('det_default') && $request->det_default == 'on'){
                $default_shipping_mode = User::where('id', $id)->update([
                    'default_shipping_mode' => 3
                ]);
            }

            $DetainRatePresent = RateStatus::where('user_id', $id)->where('shipping_mode_id', 3)->get();

            if ($DetainRatePresent->isEmpty()) {

                if($request->has('detain_origin_hubs')) {
                    foreach($request->detain_origin_hubs as $origin_id){
                        $rate_origin_hub = new RateOriginHub();
                        $rate_origin_hub->user_id = $id;
                        $rate_origin_hub->shipping_mode_id = 3;
                        $rate_origin_hub->city_id = $origin_id;
                        $rate_origin_hub->save();
                    }
                }
                if($request->has('detain_destination_hubs')) {
                    foreach($request->detain_destination_hubs as $destination_id){
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
                    'fuel_charges'=> ($request->has('detain_fuel_switch'))? 1:0
                ]);
                $wa_switch_detain = array();
                $wa_spkg_detain = array();
                $standard_weight = StandardWeightCharge::where('shipping_mode_id',3)->pluck('kg_range')->toArray();
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
                    'try_and_buy_charges' => $request->detain_tnb_charges
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
                        'national_charges_class_0'=> $request->detain_return_class_0_charges,
                        'national_charges_class_1'=> $request->detain_return_class_1_charges,
                        'national_charges_class_2'=> $request->detain_return_class_2_charges,
                        'national_charges_class_3'=> $request->detain_return_class_3_charges
                    ]);
                }
                if($request->has('detain_fuel_switch') && $request->detain_fuel_switch == 'on'){
                    FuelSurcharge::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>3,
                        'fuel_surcharge'=> $request->detain_fuel_surcharge
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
                    $to = Carbon::create($date_to[2],$date_to[0],$date_to[1],0,0,0,'UTC')->toDateTimeString();
                    $from = Carbon::create($date_from[2],$date_from[0],$date_from[1],0,0,0,'UTC')->toDateTimeString();


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
            if ($request->has('detain_dws_weight')) {
                if($request->detain_dws_weight == 2){
                    
                    DwsWeightChargesController::add($id, 3, 2, Auth::id());

                }else{
                    DwsWeightChargesController::add($id, 3, 1, Auth::id());

                }
    
            }
        }
        //Sameday
        if($request->has('sameday_main_switch') && $request->sameday_main_switch == 'on'){
            if($request->has('sameday_default') && $request->sameday_default == 'on'){
                $default_shipping_mode = User::where('id', $id)->update([
                    'default_shipping_mode' => 4
                ]);
            }

            $SamedayRatePresent = RateStatus::where('user_id',$id)->where('shipping_mode_id',4)->get();
            if($SamedayRatePresent->isEmpty()) {

                if($request->has('sameday_origin_hubs')) {
                    foreach($request->sameday_origin_hubs as $origin_id){
                        $rate_origin_hub = new RateOriginHub();
                        $rate_origin_hub->user_id = $id;
                        $rate_origin_hub->shipping_mode_id = 4;
                        $rate_origin_hub->city_id = $origin_id;
                        $rate_origin_hub->save();
                    }
                }
                if($request->has('sameday_destination_hubs')) {
                    foreach($request->sameday_destination_hubs as $destination_id){
                        $rate_destination_hub = new RateDestinationHub();
                        $rate_destination_hub->user_id = $id;
                        $rate_destination_hub->shipping_mode_id = 4;
                        $rate_destination_hub->city_id = $destination_id;
                        $rate_destination_hub->save();
                    }
                }

                RateStatus::create([
                    'user_id'=>$id,
                    'shipping_mode_id'=>4,
                    'status'=> ($request->has('sameday_main_switch'))? 1:0,
                    'cash_handling_charges'=> ($request->has('sameday_cash_handling_switch'))? 1:0,
                    'insurance_charges'=> ($request->has('sameday_insurance_charges_switch'))? 1:0,
                    'return_charges'=> ($request->has('sameday_return_switch'))? 1:0,
                    'fuel_charges'=> ($request->has('sameday_fuel_switch'))? 1:0
                ]);
                $wa_switch_sameday = array();
                $wa_spkg_sameday = array();
                $standard_weight = StandardWeightCharge::where('shipping_mode_id',4)->pluck('kg_range')->toArray();
                foreach ($request->sameday_wa_range_up as $index => $sameday_wa_range_up) {
                    if($request->has('sameday_wa_switch')) {
                        if (array_key_exists($index, $request->sameday_wa_switch)) {
                            $wa_switch_sameday[$index] = 1;
                        } else {
                            $wa_switch_sameday[$index] = 0;
                        };
                    }else{
                        $wa_switch_sameday[$index] = 0;
                    }
                    if($request->has('sameday_wa_spkg')) {
                        if (array_key_exists($index, $request->sameday_wa_spkg)) {
                            $wa_spkg_sameday[$index] = $request->sameday_wa_spkg[$index];
                        } else {
                            $wa_spkg_sameday[$index] = isset($standard_weight[$index]) ? $standard_weight[$index] : 0;
                        };
                    }else{
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
                    'user_id'=>$id,
                    'shipping_mode_id'=>4,
                    'replacement_charges'=>$request->sameday_replacement_charges,
                    'try_and_buy_charges'=>$request->sameday_tnb_charges
                ]);
                //Cash handling Charges
                if($request->has('sameday_cash_handling_switch') && $request->sameday_cash_handling_switch == 'on'){
                    foreach ($request->sameday_cash_range_up as $ind => $sameday_cash_range_up){
                        CashHandlingCharge::create([
                            'user_id'=>$id,
                            'shipping_mode_id'=>4,
                            'range_up'=> $request->sameday_cash_range_up[$ind],
                            'range_down'=> $request->sameday_cash_range_down[$ind],
                            'charges'=> $request->sameday_cash_charges[$ind]
                        ]);
                    }
                }
                //insurance charges
                if($request->has('sameday_insurance_charges_switch') && $request->sameday_insurance_charges_switch == 'on'){
                    foreach ($request->sameday_ins_range_up as $insurance => $sameday_ins_range_up){
                        InsuranceCharge::create([
                            'user_id'=>$id,
                            'shipping_mode_id'=>4,
                            'range_up'=> $request->sameday_ins_range_up[$insurance],
                            'range_down'=> $request->sameday_ins_range_down[$insurance],
                            'charges'=> $request->sameday_ins_charges[$insurance]
                        ]);
                    }
                }
                //Return Charges
                if($request->has('sameday_return_switch') && $request->sameday_return_switch == 'on'){
                    ReturnCharge::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>4,
                        'local'=> $request->sameday_return_local_charges,
                        'national_charges_class_0'=> $request->sameday_return_class_0_charges,
                        'national_charges_class_1'=> '0%',
                        'national_charges_class_2'=> '0%',
                        'national_charges_class_3'=> '0%'
                    ]);
                }
                if($request->has('sameday_fuel_switch') && $request->sameday_fuel_switch == 'on'){
                    FuelSurcharge::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>4,
                        'fuel_surcharge'=> $request->sameday_fuel_surcharge
                    ]);
                }

                $discount_cash = null;
                $discount_weight = null;
                $discount_insurance = null;
                $discount_return = null;
                $discount_packaging = null;

                if($request->has('sameday_discount_weight_switch') && $request->sameday_discount_weight_switch == 'on'){
                    $discount_weight = $request->sameday_discount_weight_rate != null ? $request->sameday_discount_weight_rate : null;
//                    $discount_weight = $request->sameday_discount_weight_rate;
                }
                if($request->has('sameday_discount_cash_switch') && $request->sameday_discount_cash_switch == 'on'){
                    $discount_cash = $request->sameday_discount_cash_rate != null ? $request->sameday_discount_cash_rate : null;
                }
                if($request->has('sameday_discount_insurance_switch') && $request->sameday_discount_insurance_switch == 'on'){
                    $discount_insurance = $request->sameday_discount_insurance_rate != null ? $request->sameday_discount_insurance_rate : null;
                }
                if($request->has('sameday_discount_return_switch') && $request->sameday_discount_return_switch == 'on'){
                    $discount_return = $request->sameday_discount_return_rate != null ? $request->sameday_discount_insurance_rate : null;
                }
                if($request->has('sameday_discount_packaging_switch') && $request->sameday_discount_packaging_switch == 'on'){
                    $discount_packaging = $request->sameday_discount_packaging_rate != null ? $request->sameday_discount_packaging_rate : null;
                }
                if($discount_weight != null || $discount_cash != null || $discount_insurance != null || $discount_return != null || $discount_packaging != null) {


                    $date_str = $request->sameday_daterange;
                    $date_sep = explode(' - ', $date_str);
                    $date_to = explode('/', $date_sep[0]);
                    $date_from = explode('/', $date_sep[1]);
                    $to = Carbon::create($date_to[2],$date_to[0],$date_to[1],0,0,0,'UTC')->toDateTimeString();
                    $from = Carbon::create($date_from[2],$date_from[0],$date_from[1],0,0,0,'UTC')->toDateTimeString();


                    DiscountCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'title'=> $request->sameday_discount_title,
                        'weight' => $discount_weight,
                        'cash' => $discount_cash,
                        'insurance' => $discount_insurance,
                        'return' => $discount_return,
                        'packaging' => $discount_packaging,
                        'to' => $to,
                        'from' => $from,
                        'added_by'=>Auth::id()
                    ]);
                }

            }
            if ($request->has('sameday_dws_weight')) {
                if($request->sameday_dws_weight == 2){
                    DwsWeightChargesController::add($id, 4, 2, Auth::id());

                }else{
                    DwsWeightChargesController::add($id, 4, 1, Auth::id());

                }
    
            }
        }

        $warehouse_charges = 0;
        if($request->has('warehouse_main_switch') && $request->warehouse_main_switch == 'on'){
            $warehouse_charges = 1;

            $wms_user_info = new WmsUserInformation();
            $wms_user_info->user_id = $id;
            $wms_user_info->warehousing = 1;
            $wms_user_info->invoicing_cycle = $request->invoicing_cycle;
            $wms_user_info->invoicing_date = 1;
            $wms_user_info->per_product_charges = ($request->has('ppc_switch'))? 1:0;
            $wms_user_info->per_square_foot_charges = ($request->has('psf_switch'))? 1:0;
            $wms_user_info->packing_charges = ($request->has('packing_charges_switch'))? 1:0;
            $wms_user_info->labelling_charges = ($request->has('labelling_charges_switch'))? 1:0;
            $wms_user_info->storage_charges = ($request->has('storage_charges_switch'))? 1:0;
            $wms_user_info->save();

            if($request->has('ppc_switch')){
                $ppc = new WmsPerProductCharge();
                $ppc->user_id = $id;
                $ppc->charges = $request->ppc_charges;
                $ppc->save();
            }
            if($request->has('psf_switch')){
                $psf = new WmsPerSquareFootCharge();
                $psf->user_id = $id;
                $psf->charges = $request->psf_charges;
                $psf->save();
            }
            if($request->has('storage_charges_switch')){
                foreach ($request->storage_type as $key => $storage_type) {
                    $storage_charges = new WmsStorageTypeCharge();
                    $storage_charges->user_id = $id;
                    $storage_charges->storage_type_id = $storage_type;
                    $storage_charges->charges = $request->storage_type_charges[$key];
                    $storage_charges->save();
                }
            }


            if($request->has('packing_charges_switch')){
                foreach ($request->packing_type as $key => $packing) {
                    $ptype = new WmsPackingCharge();
                    $ptype->user_id = $id;
                    $ptype->packing_type_id = $packing;
                    $ptype->packing_size_id = $request->packing_size[$key];
                    $ptype->charges = $request->packing_charges[$key];
                    $ptype->save();
                }
            }

            if($request->has('labelling_charges_switch')){
                $labelling = new WmsLabellingCharge();
                $labelling->user_id = $id;
                $labelling->charges = $request->labelling_charges;
                $labelling->save();
            }
        }


        User::where('id',$id)->update(['status'=>1,'rates_added_by'=>Auth::id(),'rates_added_at'=>Carbon::now()]);
        if($request->has('rate_remarks') && $request->rate_remarks != null){
            $rate_remark = new RateRemark();
            $rate_remark->user_id = $id;
            $rate_remark->remarks = $request->rate_remarks;
            $rate_remark->admin_id = Auth::id();
            $rate_remark->save();

        }


        //overnight

        $weight_charges = WeightCharge::where('user_id',$id)->where('shipping_mode_id',1);
        $standard_charges = StandardWeightCharge::where('shipping_mode_id',1);
        $overnight_changes = 0;
        if($weight_charges->exists()){
            $standard_range_up = $standard_charges->pluck('range_up')->toArray();
            $weight_range_up = $weight_charges->pluck('range_up')->toArray();
            $weight_range_up_diff = $this->compare_data($weight_range_up,$standard_range_up);

            $standard_range_down = $standard_charges->pluck('range_down')->toArray();
            $weight_range_down = $weight_charges->pluck('range_down')->toArray();
            $weight_range_down_diff = $this->compare_data($weight_range_down,$standard_range_down);

            $standard_kg_range = $standard_charges->pluck('kg_range')->toArray();
            $weight_kg_range = $weight_charges->pluck('spkg')->toArray();
            $kg_range_diff = $this->compare_data($weight_kg_range,$standard_kg_range);

            $standard_weight_addition = $standard_charges->pluck('weight_addition')->toArray();
            $weight_addition = $weight_charges->pluck('weight_addition')->toArray();
            $weight_addition_diff = $this->compare_data($standard_weight_addition,$weight_addition);
            //dd($weight_addition,$standard_weight_addition,$weight_addition_diff);

            $standard_local = $standard_charges->pluck('local_or_6hr')->toArray();
            $weight_local = $weight_charges->pluck('local_or_6hr')->toArray();
            $local_diff = $this->compare_data($weight_local,$standard_local);

            $standard_national_0 = $standard_charges->pluck('national_charges_class_0')->toArray();
            $weight_national_0 = $weight_charges->pluck('national_charges_class_0')->toArray();
            $national_charges_0_diff = $this->compare_data($weight_national_0,$standard_national_0);

            $standard_national_1 = $standard_charges->pluck('national_charges_class_1')->toArray();
            $weight_national_1 = $weight_charges->pluck('national_charges_class_1')->toArray();
            $national_charges_1_diff = $this->compare_data($weight_national_1,$standard_national_1);

            $standard_national_2 = $standard_charges->pluck('national_charges_class_2')->toArray();
            $weight_national_2 = $weight_charges->pluck('national_charges_class_2')->toArray();
            $national_charges_2_diff = $this->compare_data($weight_national_2,$standard_national_2);

            $standard_national_3 = $standard_charges->pluck('national_charges_class_3')->toArray();
            $weight_national_3 = $weight_charges->pluck('national_charges_class_3')->toArray();
            $national_charges_3_diff = $this->compare_data($weight_national_3,$standard_national_3);

            $standard_booking_type_charges = StandardBookingTypeCharge::where('shipping_mode_id',1)->first();
            $booking_type_charges = BookingTypeCharges::where('shipping_mode_id',1)->where('user_id',$id)->first();

            $booking_type_charges_diff = 0;
            if($booking_type_charges){
                if($standard_booking_type_charges->replacement_charges != $booking_type_charges->replacement_charges ||  $standard_booking_type_charges->try_and_buy_charges != $booking_type_charges->try_and_buy_charges){
                    $booking_type_charges_diff = 1;
                }
            }


            $cash_handling_charges = CashHandlingCharge::where('shipping_mode_id',1)->where('user_id',$id);
            $cash_handling_charges_change = 0;
            if($cash_handling_charges->exists()){
                $standard_cash_handling_charges = StandardCashHandlingCharge::where('shipping_mode_id',1);

                $cash_handling_charges_range_up = $cash_handling_charges->pluck('range_up')->toArray();
                $standard_cash_handling_charges_range_up = $standard_cash_handling_charges->pluck('range_up')->toArray();
                $cash_handling_range_up_diff = $this->compare_data($cash_handling_charges_range_up,$standard_cash_handling_charges_range_up);

                $cash_handling_charges_range_down = $cash_handling_charges->pluck('range_down')->toArray();
                $standard_cash_handling_charges_range_down = $standard_cash_handling_charges->pluck('range_down')->toArray();
                $cash_handling_range_down_diff = $this->compare_data($cash_handling_charges_range_down,$standard_cash_handling_charges_range_down);

                $cash_handling_charges = $cash_handling_charges->pluck('charges')->toArray();
                $standard_cash_handling_charges = $standard_cash_handling_charges->pluck('charges')->toArray();
                $cash_handling_charges_diff = $this->compare_data($cash_handling_charges,$standard_cash_handling_charges);

                if(($cash_handling_range_up_diff == 1) || ($cash_handling_range_down_diff == 1) || ($cash_handling_charges_diff == 1)){
                    $cash_handling_charges_change = 1;
                }

            }
            else{
                $cash_handling_charges_change = 1;
            }

            $return_charges = ReturnCharge::where('shipping_mode_id',1)->where('user_id',$id);
            $return_charges_diff = 0;

            if($return_charges->exists()){
                $standard_return_charges = StandardReturnCharge::where('shipping_mode_id',1)->first();
                $return_charges = $return_charges->first();


                if($return_charges->local != $standard_return_charges->local || $return_charges->national_charges_class_0 != $standard_return_charges->national_charges_class_0 || $return_charges->national_charges_class_1 != $standard_return_charges->national_charges_class_1 || $return_charges->national_charges_class_2 != $standard_return_charges->national_charges_class_2 || $return_charges->national_charges_class_3 != $standard_return_charges->national_charges_class_3)
                {
                    $return_charges_diff = 1;
                }
            }
            else{
                $return_charges_diff = 1;
            }

            $fuel_surcharge = FuelSurcharge::where('shipping_mode_id',1)->where('user_id',$id);
            $fuel_surcharge_diff = 0;
            if($fuel_surcharge->exists()){
                $standard_fuel_surcharge = StandardFuelSurcharge::where('shipping_mode_id',1)->first();
                $fuel_surcharge = $fuel_surcharge->first();

                if($standard_fuel_surcharge->fuel_surcharge != $fuel_surcharge->fuel_surcharge){
                    $fuel_surcharge_diff = 1;
                }
            }
            else{
                $fuel_surcharge_diff = 1;
            }

            $insurance_charge = InsuranceCharge::where('shipping_mode_id',1)->where('user_id',$id);
            $insurance_charges_diff = 0;
            if($insurance_charge->exists()){
                $insurance_charges_diff = 1;
            }
            $on_dws_weight_diff = 0;
            if ($request->has('on_dws_weight')) {
                if($request->on_dws_weight == 0){
                    $on_dws_weight_diff = 1;
                }
            }


            if($weight_range_up_diff == 1 || $weight_range_down_diff == 1 || $kg_range_diff == 1 || $local_diff == 1 || $national_charges_0_diff == 1 || $national_charges_1_diff == 1 || $national_charges_2_diff == 1 || $national_charges_3_diff == 1 || $booking_type_charges_diff == 1 || $cash_handling_charges_change == 1 || $return_charges_diff == 1 || $fuel_surcharge_diff == 1 || $weight_addition_diff == 1 || $insurance_charges_diff == 1 || $on_dws_weight_diff == 1)
            {
                $overnight_changes = 1;
                
            }
//            dd($weight_range_up_diff,$weight_range_down_diff,$kg_range_diff,$local_diff,$national_charges_0_diff,$national_charges_1_diff, $national_charges_2_diff,$national_charges_3_diff,$booking_type_charges_diff,$cash_handling_charges_change,$return_charges_diff,$fuel_surcharge_diff,$weight_addition_diff,$insurance_charges_diff,$overnight_changes);
        }

        //overland
        $weight_charges = WeightCharge::where('user_id',$id)->where('shipping_mode_id',2);
        $standard_charges = StandardWeightCharge::where('shipping_mode_id',2);
        $overland_changes = 0;
        if($weight_charges->exists()){
            $standard_range_up = $standard_charges->pluck('range_up')->toArray();
            $weight_range_up = $weight_charges->pluck('range_up')->toArray();
            $weight_range_up_diff = $this->compare_data($weight_range_up,$standard_range_up);

            $standard_range_down = $standard_charges->pluck('range_down')->toArray();
            $weight_range_down = $weight_charges->pluck('range_down')->toArray();
            $weight_range_down_diff = $this->compare_data($weight_range_down,$standard_range_down);

            $standard_kg_range = $standard_charges->pluck('kg_range')->toArray();
            $weight_kg_range = $weight_charges->pluck('spkg')->toArray();
            $kg_range_diff = $this->compare_data($weight_kg_range,$standard_kg_range);

            $standard_weight_addition = $standard_charges->pluck('weight_addition')->toArray();
            $weight_addition = $weight_charges->pluck('weight_addition')->toArray();
            $weight_addition_diff = $this->compare_data($standard_weight_addition,$weight_addition);

            $standard_local = $standard_charges->pluck('local_or_6hr')->toArray();
            $weight_local = $weight_charges->pluck('local_or_6hr')->toArray();
            $local_diff = $this->compare_data($weight_local,$standard_local);

            $standard_national_0 = $standard_charges->pluck('national_charges_class_0')->toArray();
            $weight_national_0 = $weight_charges->pluck('national_charges_class_0')->toArray();
            $national_charges_0_diff = $this->compare_data($weight_national_0,$standard_national_0);

            $standard_national_1 = $standard_charges->pluck('national_charges_class_1')->toArray();
            $weight_national_1 = $weight_charges->pluck('national_charges_class_1')->toArray();
            $national_charges_1_diff = $this->compare_data($weight_national_1,$standard_national_1);

            $standard_national_2 = $standard_charges->pluck('national_charges_class_2')->toArray();
            $weight_national_2 = $weight_charges->pluck('national_charges_class_2')->toArray();
            $national_charges_2_diff = $this->compare_data($weight_national_2,$standard_national_2);

            $standard_national_3 = $standard_charges->pluck('national_charges_class_3')->toArray();
            $weight_national_3 = $weight_charges->pluck('national_charges_class_3')->toArray();
            $national_charges_3_diff = $this->compare_data($weight_national_3,$standard_national_3);

            $standard_booking_type_charges = StandardBookingTypeCharge::where('shipping_mode_id',2)->first();
            $booking_type_charges = BookingTypeCharges::where('shipping_mode_id',2)->where('user_id',$id)->first();

            $booking_type_charges_diff = 0;
            if($booking_type_charges){
                if($standard_booking_type_charges->replacement_charges != $booking_type_charges->replacement_charges ||  $standard_booking_type_charges->try_and_buy_charges != $booking_type_charges->try_and_buy_charges){
                    $booking_type_charges_diff = 1;
                }
            }

            $cash_handling_charges = CashHandlingCharge::where('shipping_mode_id',2)->where('user_id',$id);
            $cash_handling_charges_change = 0;
            if($cash_handling_charges->exists()){
                $standard_cash_handling_charges = StandardCashHandlingCharge::where('shipping_mode_id',2);

                $cash_handling_charges_range_up = $cash_handling_charges->pluck('range_up')->toArray();
                $standard_cash_handling_charges_range_up = $standard_cash_handling_charges->pluck('range_up')->toArray();
                $cash_handling_range_up_diff = $this->compare_data($cash_handling_charges_range_up,$standard_cash_handling_charges_range_up);

                $cash_handling_charges_range_down = $cash_handling_charges->pluck('range_down')->toArray();
                $standard_cash_handling_charges_range_down = $standard_cash_handling_charges->pluck('range_down')->toArray();
                $cash_handling_range_down_diff = $this->compare_data($cash_handling_charges_range_down,$standard_cash_handling_charges_range_down);

                $cash_handling_charges = $cash_handling_charges->pluck('charges')->toArray();
                $standard_cash_handling_charges = $standard_cash_handling_charges->pluck('charges')->toArray();
                $cash_handling_charges_diff = $this->compare_data($cash_handling_charges,$standard_cash_handling_charges);

                if(($cash_handling_range_up_diff == 1) || ($cash_handling_range_down_diff == 1) || ($cash_handling_charges_diff == 1)){
                    $cash_handling_charges_change = 1;
                }

            }
            else{
                //for toggle close
                $cash_handling_charges_change = 1;
            }

            $return_charges = ReturnCharge::where('shipping_mode_id',2)->where('user_id',$id);
            $return_charges_diff = 0;

            if($return_charges->exists()){
                $standard_return_charges = StandardReturnCharge::where('shipping_mode_id',2)->first();
                $return_charges = $return_charges->first();


                if($return_charges->local != $standard_return_charges->local || $return_charges->national_charges_class_0 != $standard_return_charges->national_charges_class_0 || $return_charges->national_charges_class_1 != $standard_return_charges->national_charges_class_1 || $return_charges->national_charges_class_2 != $standard_return_charges->national_charges_class_2 || $return_charges->national_charges_class_3 != $standard_return_charges->national_charges_class_3)
                {
                    $return_charges_diff = 1;
                }
            }
            else{
                $return_charges_diff = 1;
            }

            $fuel_surcharge = FuelSurcharge::where('shipping_mode_id',2)->where('user_id',$id);
            $fuel_surcharge_diff = 0;
            if($fuel_surcharge->exists()){
                $standard_fuel_surcharge = StandardFuelSurcharge::where('shipping_mode_id',2)->first();
                $fuel_surcharge = $fuel_surcharge->first();

                if($standard_fuel_surcharge->fuel_surcharge != $fuel_surcharge->fuel_surcharge){
                    $fuel_surcharge_diff = 1;
                }
            }
            else{
                $fuel_surcharge_diff = 1;
            }

            $insurance_charge = InsuranceCharge::where('shipping_mode_id',2)->where('user_id',$id);
            $insurance_charges_diff = 0;
            if($insurance_charge->exists()){
                $insurance_charges_diff = 1;
            }
            $ol_dws_weight_diff = 0;
            if ($request->has('ol_dws_weight')) {
                if($request->ol_dws_weight == 0){
                    $ol_dws_weight_diff = 1;
                }
            }
            if($weight_range_up_diff == 1 || $weight_range_down_diff == 1 || $kg_range_diff == 1 || $local_diff == 1 || $national_charges_0_diff == 1 || $national_charges_1_diff == 1 || $national_charges_2_diff == 1 || $national_charges_3_diff == 1 || $booking_type_charges_diff == 1 || $cash_handling_charges_change == 1 || $return_charges_diff == 1 || $fuel_surcharge_diff == 1 || $weight_addition_diff == 1 || $insurance_charges_diff == 1 || $ol_dws_weight_diff == 1)
            {
                $overland_changes = 1;
            }
        }

        //detain
        $weight_charges = WeightCharge::where('user_id',$id)->where('shipping_mode_id',3);
        $standard_charges = StandardWeightCharge::where('shipping_mode_id',3);
        $detain_changes = 0;
        if($weight_charges->exists()){
            $standard_range_up = $standard_charges->pluck('range_up')->toArray();
            $weight_range_up = $weight_charges->pluck('range_up')->toArray();
            $weight_range_up_diff = $this->compare_data($weight_range_up,$standard_range_up);

            $standard_range_down = $standard_charges->pluck('range_down')->toArray();
            $weight_range_down = $weight_charges->pluck('range_down')->toArray();
            $weight_range_down_diff = $this->compare_data($weight_range_down,$standard_range_down);

            $standard_kg_range = $standard_charges->pluck('kg_range')->toArray();
            $weight_kg_range = $weight_charges->pluck('spkg')->toArray();
            $kg_range_diff = $this->compare_data($weight_kg_range,$standard_kg_range);

            $standard_weight_addition = $standard_charges->pluck('weight_addition')->toArray();
            $weight_addition = $weight_charges->pluck('weight_addition')->toArray();
            $weight_addition_diff = $this->compare_data($standard_weight_addition,$weight_addition);

            $standard_local = $standard_charges->pluck('local_or_6hr')->toArray();
            $weight_local = $weight_charges->pluck('local_or_6hr')->toArray();
            $local_diff = $this->compare_data($weight_local,$standard_local);

            $standard_national_0 = $standard_charges->pluck('national_charges_class_0')->toArray();
            $weight_national_0 = $weight_charges->pluck('national_charges_class_0')->toArray();
            $national_charges_0_diff = $this->compare_data($weight_national_0,$standard_national_0);

            $standard_national_1 = $standard_charges->pluck('national_charges_class_1')->toArray();
            $weight_national_1 = $weight_charges->pluck('national_charges_class_1')->toArray();
            $national_charges_1_diff = $this->compare_data($weight_national_1,$standard_national_1);

            $standard_national_2 = $standard_charges->pluck('national_charges_class_2')->toArray();
            $weight_national_2 = $weight_charges->pluck('national_charges_class_2')->toArray();
            $national_charges_2_diff = $this->compare_data($weight_national_2,$standard_national_2);

            $standard_national_3 = $standard_charges->pluck('national_charges_class_3')->toArray();
            $weight_national_3 = $weight_charges->pluck('national_charges_class_3')->toArray();
            $national_charges_3_diff = $this->compare_data($weight_national_3,$standard_national_3);

            $standard_booking_type_charges = StandardBookingTypeCharge::where('shipping_mode_id',3)->first();
            $booking_type_charges = BookingTypeCharges::where('shipping_mode_id',3)->where('user_id',$id)->first();

            $booking_type_charges_diff = 0;
            if($booking_type_charges){
                if($standard_booking_type_charges->replacement_charges != $booking_type_charges->replacement_charges ||  $standard_booking_type_charges->try_and_buy_charges != $booking_type_charges->try_and_buy_charges){
                    $booking_type_charges_diff = 1;
                }
            }

            $cash_handling_charges = CashHandlingCharge::where('shipping_mode_id',3)->where('user_id',$id);
            $cash_handling_charges_change = 0;
            if($cash_handling_charges->exists()){
                $standard_cash_handling_charges = StandardCashHandlingCharge::where('shipping_mode_id',3);

                $cash_handling_charges_range_up = $cash_handling_charges->pluck('range_up')->toArray();
                $standard_cash_handling_charges_range_up = $standard_cash_handling_charges->pluck('range_up')->toArray();
                $cash_handling_range_up_diff = $this->compare_data($cash_handling_charges_range_up,$standard_cash_handling_charges_range_up);

                $cash_handling_charges_range_down = $cash_handling_charges->pluck('range_down')->toArray();
                $standard_cash_handling_charges_range_down = $standard_cash_handling_charges->pluck('range_down')->toArray();
                $cash_handling_range_down_diff = $this->compare_data($cash_handling_charges_range_down,$standard_cash_handling_charges_range_down);

                $cash_handling_charges = $cash_handling_charges->pluck('charges')->toArray();
                $standard_cash_handling_charges = $standard_cash_handling_charges->pluck('charges')->toArray();
                $cash_handling_charges_diff = $this->compare_data($cash_handling_charges,$standard_cash_handling_charges);

                if(($cash_handling_range_up_diff == 1) || ($cash_handling_range_down_diff == 1) || ($cash_handling_charges_diff == 1)){
                    $cash_handling_charges_change = 1;
                }

            }
            else{
                $cash_handling_charges_change = 1;
            }

            $return_charges = ReturnCharge::where('shipping_mode_id',3)->where('user_id',$id);
            $return_charges_diff = 0;

            if($return_charges->exists()){
                $standard_return_charges = StandardReturnCharge::where('shipping_mode_id',3)->first();
                $return_charges = $return_charges->first();


                if($return_charges->local != $standard_return_charges->local || $return_charges->national_charges_class_0 != $standard_return_charges->national_charges_class_0 || $return_charges->national_charges_class_1 != $standard_return_charges->national_charges_class_1 || $return_charges->national_charges_class_2 != $standard_return_charges->national_charges_class_2 || $return_charges->national_charges_class_3 != $standard_return_charges->national_charges_class_3)
                {
                    $return_charges_diff = 1;
                }
            }
            else{
                $return_charges_diff = 1;
            }

            $fuel_surcharge = FuelSurcharge::where('shipping_mode_id',3)->where('user_id',$id);
            $fuel_surcharge_diff = 0;
            if($fuel_surcharge->exists()){
                $standard_fuel_surcharge = StandardFuelSurcharge::where('shipping_mode_id',3)->first();
                $fuel_surcharge = $fuel_surcharge->first();

                if($standard_fuel_surcharge->fuel_surcharge != $fuel_surcharge->fuel_surcharge){
                    $fuel_surcharge_diff = 1;
                }
            }
            else{
                $fuel_surcharge_diff = 1;
            }

            $insurance_charge = InsuranceCharge::where('shipping_mode_id',3)->where('user_id',$id);
            $insurance_charges_diff = 0;
            if($insurance_charge->exists()){
                $insurance_charges_diff = 1;
            }
            $detain_dws_weight_diff = 0;
            if ($request->has('detain_dws_weight')) {
                if($request->detain_dws_weight == 0){
                    $detain_dws_weight_diff = 1;
                }
            }
            if($weight_range_up_diff == 1 || $weight_range_down_diff == 1 || $kg_range_diff == 1 || $local_diff == 1 || $national_charges_0_diff == 1 || $national_charges_1_diff == 1 || $national_charges_2_diff == 1 || $national_charges_3_diff == 1 || $booking_type_charges_diff == 1 || $cash_handling_charges_change == 1 || $return_charges_diff == 1 || $fuel_surcharge_diff == 1 || $weight_addition_diff == 1 || $insurance_charges_diff == 1 || $detain_dws_weight_diff == 1)
            {
                $detain_changes = 1;
            }
        }

        //sameday
        $weight_charges = WeightCharge::where('user_id',$id)->where('shipping_mode_id',4);
        $standard_charges = StandardWeightCharge::where('shipping_mode_id',4);
        $sameday_changes = 0;
        if($weight_charges->exists()){
            $standard_range_up = $standard_charges->pluck('range_up')->toArray();
            $weight_range_up = $weight_charges->pluck('range_up')->toArray();
            $weight_range_up_diff = $this->compare_data($weight_range_up,$standard_range_up);

            $standard_range_down = $standard_charges->pluck('range_down')->toArray();
            $weight_range_down = $weight_charges->pluck('range_down')->toArray();
            $weight_range_down_diff = $this->compare_data($weight_range_down,$standard_range_down);

            $standard_kg_range = $standard_charges->pluck('kg_range')->toArray();
            $weight_kg_range = $weight_charges->pluck('spkg')->toArray();
            $kg_range_diff = $this->compare_data($weight_kg_range,$standard_kg_range);

            $standard_weight_addition = $standard_charges->pluck('weight_addition')->toArray();
            $weight_addition = $weight_charges->pluck('weight_addition')->toArray();
            $weight_addition_diff = $this->compare_data($standard_weight_addition,$weight_addition);

            $standard_local = $standard_charges->pluck('local_or_6hr')->toArray();
            $weight_local = $weight_charges->pluck('local_or_6hr')->toArray();
            $local_diff = $this->compare_data($weight_local,$standard_local);

            $standard_national_0 = $standard_charges->pluck('national_charges_class_0')->toArray();
            $weight_national_0 = $weight_charges->pluck('national_charges_class_0')->toArray();
            $national_charges_0_diff = $this->compare_data($weight_national_0,$standard_national_0);

            $standard_national_1 = $standard_charges->pluck('national_charges_class_1')->toArray();
            $weight_national_1 = $weight_charges->pluck('national_charges_class_1')->toArray();
            $national_charges_1_diff = $this->compare_data($weight_national_1,$standard_national_1);

            $standard_national_2 = $standard_charges->pluck('national_charges_class_2')->toArray();
            $weight_national_2 = $weight_charges->pluck('national_charges_class_2')->toArray();
            $national_charges_2_diff = $this->compare_data($weight_national_2,$standard_national_2);

            $standard_national_3 = $standard_charges->pluck('national_charges_class_3')->toArray();
            $weight_national_3 = $weight_charges->pluck('national_charges_class_3')->toArray();
            $national_charges_3_diff = $this->compare_data($weight_national_3,$standard_national_3);

            $standard_booking_type_charges = StandardBookingTypeCharge::where('shipping_mode_id',4)->first();
            $booking_type_charges = BookingTypeCharges::where('shipping_mode_id',4)->where('user_id',$id)->first();

            $booking_type_charges_diff = 0;
            if($booking_type_charges){
                if($standard_booking_type_charges->replacement_charges != $booking_type_charges->replacement_charges ||  $standard_booking_type_charges->try_and_buy_charges != $booking_type_charges->try_and_buy_charges){
                    $booking_type_charges_diff = 1;
                }
            }


            $cash_handling_charges = CashHandlingCharge::where('shipping_mode_id',4)->where('user_id',$id);
            $cash_handling_charges_change = 0;
            if($cash_handling_charges->exists()){
                $standard_cash_handling_charges = StandardCashHandlingCharge::where('shipping_mode_id',4);

                $cash_handling_charges_range_up = $cash_handling_charges->pluck('range_up')->toArray();
                $standard_cash_handling_charges_range_up = $standard_cash_handling_charges->pluck('range_up')->toArray();
                $cash_handling_range_up_diff = $this->compare_data($cash_handling_charges_range_up,$standard_cash_handling_charges_range_up);

                $cash_handling_charges_range_down = $cash_handling_charges->pluck('range_down')->toArray();
                $standard_cash_handling_charges_range_down = $standard_cash_handling_charges->pluck('range_down')->toArray();
                $cash_handling_range_down_diff = $this->compare_data($cash_handling_charges_range_down,$standard_cash_handling_charges_range_down);

                $cash_handling_charges = $cash_handling_charges->pluck('charges')->toArray();
                $standard_cash_handling_charges = $standard_cash_handling_charges->pluck('charges')->toArray();
                $cash_handling_charges_diff = $this->compare_data($cash_handling_charges,$standard_cash_handling_charges);

                if(($cash_handling_range_up_diff == 1) || ($cash_handling_range_down_diff == 1) || ($cash_handling_charges_diff == 1)){
                    $cash_handling_charges_change = 1;
                }

            }
            else{
                $cash_handling_charges_change = 1;
            }

            $return_charges = ReturnCharge::where('shipping_mode_id',4)->where('user_id',$id);
            $return_charges_diff = 0;

            if($return_charges->exists()){
                $standard_return_charges = StandardReturnCharge::where('shipping_mode_id',4)->first();
                $return_charges = $return_charges->first();


                if($return_charges->local != $standard_return_charges->local || $return_charges->national_charges_class_0 != $standard_return_charges->national_charges_class_0 || $return_charges->national_charges_class_1 != $standard_return_charges->national_charges_class_1 || $return_charges->national_charges_class_2 != $standard_return_charges->national_charges_class_2 || $return_charges->national_charges_class_3 != $standard_return_charges->national_charges_class_3)
                {
                    $return_charges_diff = 1;
                }
            }
            else{
                $return_charges_diff = 1;
            }

            $fuel_surcharge = FuelSurcharge::where('shipping_mode_id',4)->where('user_id',$id);
            $fuel_surcharge_diff = 0;
            if($fuel_surcharge->exists()){
                $standard_fuel_surcharge = StandardFuelSurcharge::where('shipping_mode_id',4)->first();
                $fuel_surcharge = $fuel_surcharge->first();

                if($standard_fuel_surcharge->fuel_surcharge != $fuel_surcharge->fuel_surcharge){
                    $fuel_surcharge_diff = 1;
                }
            }
            else{
                $fuel_surcharge_diff = 1;
            }

            $insurance_charge = InsuranceCharge::where('shipping_mode_id',4)->where('user_id',$id);
            $insurance_charges_diff = 0;
            if($insurance_charge->exists()){
                $insurance_charges_diff = 1;
            }
            $sameday_dws_weight_diff = 0;
            if ($request->has('sameday_dws_weight')) {
                if($request->sameday_dws_weight == 0){
                    $sameday_dws_weight_diff = 1;
                }
            }
            if($weight_range_up_diff == 1 || $weight_range_down_diff == 1 || $kg_range_diff == 1 || $local_diff == 1 || $national_charges_0_diff == 1 || $national_charges_1_diff == 1 || $national_charges_2_diff == 1 || $national_charges_3_diff == 1 || $booking_type_charges_diff == 1 || $cash_handling_charges_change == 1 || $return_charges_diff == 1 || $fuel_surcharge_diff == 1 || $weight_addition_diff == 1 || $insurance_charges_diff == 1 || $sameday_dws_weight_diff == 1)
            {
                $sameday_changes = 1;
            }
        }

        //dd($overnight_changes,$overland_changes,$detain_changes,$sameday_changes,$warehouse_charges);

        if($overnight_changes == 0 && $overland_changes == 0 && $detain_changes == 0 && $sameday_changes == 0 && $warehouse_charges == 0){
            DwsWeightChargesController::approve($id);
            User::where('id',$id)->update(['rate_status'=> 0,'status' => 2,'rates_authorized_by'=> 32, 'rates_approved_at'=> Carbon::now()]);
        }
        

        //Sales Commisssion

        if($request->has('user_id')){
            $total_commission = $request->total_commission;
            $users_count = count($request->user_id);

            $sales_commission = SalesCommission::where('shipper_id', $shipper_id);
            if($sales_commission->exists()){
                $sales_commission = $sales_commission->first();
                $sales_commission->commission_users_count = $users_count;
                $sales_commission->commission = $total_commission;
                $sales_commission->updated_by = Auth::id();
                $sales_commission->save();
                $sales_commission_id = $sales_commission->id;
                $actual_commission = 0;
                SalesCommissionUser::where('sales_commission_id', $sales_commission_id)->delete();
                foreach($request->tier_id as $row_id => $tier){
                    $sales_tier = SalesTier::find($tier);
                    if($sales_tier){
                        $sales_commission_user = new SalesCommissionUser();
                        $sales_commission_user->sales_commission_id = $sales_commission_id;
                        $sales_commission_user->tier_type_id = $sales_tier->tier_type;
                        $sales_commission_user->tier_id = $tier;
                        if($sales_tier->tier_type == 1){
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
                    }
                }
                $sales_commission->commission = $actual_commission;
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
                    }
                }
                $sales_commission->commission = $actual_commission;
                $sales_commission->save();
            }

        }


        //Sales Commissison End

        NotificationsController::send(38, $id);

        return redirect(route('admin.accounts.pending'))->with('success','All Rates are added');
    }

    public function duplicate_info(Request $request){
        $shipper_id = $request->shipper_id;
        $duplicate = DuplicateUser::where('user_id', $shipper_id)->first();
        $data = array();
        $data['phone'] = ($duplicate->phone) ? $duplicate->phone:'';
        $data['cnic'] = ($duplicate->cnic) ? $duplicate->cnic:'';
        $data['iban'] = ($duplicate->iban) ? $duplicate->iban:'';
        $data['name'] = ($duplicate->name) ? $duplicate->name:'';
        return response()->json(['status' => 1, 'info' => $data]);
    }
    public function activeAccountListAjax(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),62);
        }
        $users = User::join('cities', 'users.city_id', '=', 'cities.id')
            ->leftjoin('products as p','p.id','=','users.product_id')
            ->leftjoin('sub_category_segments as seg_sub','seg_sub.id','=','users.sub_segment_id')
            ->leftjoin('segments as seg','seg.id','=','users.segment_id')
            ->leftjoin('admins as rab','rab.id','=','users.rates_added_by')
            ->leftjoin('admins as rabna','rabna.id','=','users.rates_updated_by')
            ->leftjoin('admins as rabb','rabb.id','=','users.rates_authorized_by')
            ->leftjoin('admins as rrb','rrb.id','=','users.rates_rejected_by')
            ->leftjoin('admins as rabba','rabba.id','=','users.account_activated_by')
            ->leftjoin('account_types as at','at.id','=','users.account_type_id')
            ->leftjoin('sale_person_tags as spt', function ($join) {
                $join->on('spt.user_id', '=', 'users.id')
                    ->leftjoin('admins as ad','ad.id','=','spt.admin_id')
                    ->where('spt.status','=',0);
            })
            ->leftjoin('duplicate_users as du', 'du.user_id', '=', 'users.id')
            ->leftjoin('international_users_informations as iui', 'iui.user_id', '=', 'users.id')
            ->leftjoin('user_document_attachments as uda','uda.user_id','=','users.id')
            ->leftjoin('admins as dab','dab.id','=','uda.approved_by')
            ->leftjoin('admins as drb','drb.id','=','uda.rejected_by')
            ->leftjoin('sale_tier_tags as st','st.user_id','=','users.id')
            ->leftjoin('admins as poc','poc.id','=','st.poc')
            ->leftjoin('admins as k','k.id','=','st.kam')
            ->leftjoin('admins as r','r.id','=','st.ref')
            ->leftjoin('territories as t','t.id','=','users.territory_id')
			->select(['users.auto_shipment_cancellation_days','rrb.name as rates_rejected_by','users.disable_at as disable_at','users.rates_added_at as rates_added_at','users.rates_approved_at as rates_approved_at','users.rates_rejected_at as rates_rejected_at','users.disable_remarks as disable_remarks','users.rejected_reason as rejected_reason','users.rate_status as rate_status','users.id','ad.name as admin_tag_id', 'users.name', 'cities.name as city','users.poc', 'p.product_name as product_type','rab.name as added_by','rabna.name as updated_by','users.created_at','rabb.name as approved_by','rabba.name as account_activated_by','users.activated_at as activated_date','users.status','users.account_type_id','at.name as account_type','users.documents_status','users.documents_status_reason as documents_rejection_reason','users.other_product_name','users.auto_shipment_cancellation_days', 'du.phone as duplicate_phone','du.cnic as duplicate_cnic', 'du.iban as duplicate_iban','du.name as duplicate_name', 'users.brand_name as brand_name', 'iui.status as international_rate_status', 'iui.rejected_reason as international_rejected_reason','uda.uploaded_at as documents_uploaded_at','uda.approved_at as documents_approved_at','dab.name as documents_approved_by','drb.name as documents_rejected_by','uda.rejected_at as documents_rejected_at','poc.name as tagged_poc','k.name as kam','r.name as ref','users.address as address','users.email','t.name as territory','users.corporate_rate_type_id as corporate_rate_type_id','users.new_rate_type_id as new_rate_type_id','seg.name as segment','seg_sub.name as sub_segment'])->whereIn('users.status',[3,4])->where('blacklist',0);
        if (session('role_id') != 1) {
            $users = $users->whereIn('cities.hub_id', session('hubs'));
        }

        if(session('department_id') == 7){
            if(in_array(session('id'), session('sale_users_bypass')) ){
                $users = $users->whereIn('users.id', session('tagged_shippers'));
            }
        }

        if($sale_persons = $request->get('sale_persons')){
            $users = $users->where('ad.id', $sale_persons);
        }

        if($search_cnic = $request->get('search_cnic')){
            $users = $users->where('users.cnic', $search_cnic);
        }
        if($search_shipper = $request->get('search_shipper')){
            $users = $users->where('users.id', $search_shipper);
        }

        if($search_iban = $request->get('search_iban')){
            $users = $users->leftjoin('user_bank_infos as ubi', function($join) use ($search_iban){
                $join->on('ubi.user_id', '=', 'users.id')
                    ->where('ubi.iban', $search_iban);
            });
        }
        if($search_email = $request->get('search_email')){
            $users = $users->where('users.email',$search_email);
        }

        return Datatables::of($users)
            ->addColumn('id_padded', function ($user) {
                return str_pad($user->id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('rate_status',function ($users){
                if($users->rate_status == 0){
                    return "Approved";
                }elseif($users->rate_status == 1){
                    return "Requested";
                }
                else{
                    return "Rejected";
                }
            })->editColumn('disable_remarks',function ($users){
                if($users->disable_remarks != null){
                    return $users->disable_remarks;
                }
                else{
                    return "-";
                }
            })
            ->filterColumn('users.id', function ($query, $keyword) {
                return $query->where('users.id', '=', $keyword);
            })
            ->editColumn('status',function ($users){
                if($users->status == 3){
                    return "Enable";
                }else{
                    return "Disable";
                }
            })
            ->editColumn('documents_status',function ($users){
                if($users->documents_status == 0){
                    return "Incomplete";
                }
                elseif($users->documents_status == 1){
                    return "Pending for Approval";
                }
                elseif($users->documents_status == 2){
                    return "Approved";
                }
                elseif($users->documents_status == 3){
                    return "Rejected";
                }
            })
            ->editColumn('rejected_reason',function ($users){
                if($users->rejected_reason != null && $users->rate_status==2){
                    return $users->rejected_reason;
                }else{
                    return "-";
                }
            })
            ->filterColumn('status', function($query, $keyword) {
                if ($keyword == 3 || $keyword == 4) {
                    $query->where('users.status', '=', $keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->editColumn('product_type', function($user){
                if($user->product_type == 'Other'){
                    return $user->other_product_name;
                }else{
                    return $user->product_type;
                }
            })
            ->filterColumn('product_type',function ($query,$keyword){

                if ($keyword != '' || $keyword != 24) {
                    $query->where('p.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn('duplication', function($users){
                $count = 0;
                if($users->duplicate_phone != null){
                    $count++;
                }
                if($users->duplicate_cnic != null){
                    $count++;
                }
                if($users->duplicate_iban != null){
                    $count++;
                }
                if($users->duplicate_name != null){
                    $count++;
                }
                if($count > 0){
                    return '<button class="btn btn-sm btn-outline-info align-middle duplicate_modal">' . $count . '</button>';
                }else{
                    return $count;
                }
            })
            ->editColumn('international_rate_status',function ($users){
                if($users->international_rate_status != null){
                    if($users->international_rate_status == 1){
                        return "Approved";
                    }elseif($users->international_rate_status == 2){
                        return "Requested";
                    }elseif($users->international_rate_status == 3){
                        return "Rejected";
                    }elseif($users->international_rate_status == 4){
                        return "Requested";
                    }elseif($users->international_rate_status == 5){
                        return "Rejected";
                    }
                }
                else{
                    return "International Rates are not set";
                }
            })
            ->editColumn('international_rejected_reason',function ($users){
                if($users->international_rejected_reason != null && $users->international_rate_status == 3){
                    return $users->international_rejected_reason;
                }else{
                    return "-";
                }
            })
            ->addColumn("action", function ($result) {
                if($result->id != 8761 && $result->id != 9358){
                    if(in_array($result->id, session('tagged_shippers'))){
                        $multiple_sale_check = true;
                    }
                    else{
                        $multiple_sale_check = false;
                    }

                    $sale_check= SalePersonTag::where('user_id',$result->id)->first();
                    $dropdown = '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm accounts">
                ';

                    if(session('role_id') == 1 || in_array(361, session('permissions'))){
                        $dropdown .= '<button type="button" class="dropdown-item remove_sales_tier"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-minus-circle"></i></div><div class="col-9 offset-1">Remove Sales Tier Tagging</div></button>';
                    }

                    $dropdown .= '<button type="button" class="dropdown-item" data-target-id="' . $result->id . '" data-toggle="modal" data-target="#BankInfoModal"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Bank Info</div></button>';

                    $dropdown .= '<button type="button" class="dropdown-item" data-target-id="' . $result->id . '" data-toggle="modal" data-target="#ShippingInfoModal"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Shipping Info</div></button>';
                    if($result->status == 3 && (session('role_id') == 1 || in_array(session('id'), session('sale_users_bypass'))))
                    {
                        $dropdown .= '<button type="button" class="dropdown-item" data-target-id="' . $result->id . '" data-toggle="modal" data-target="#SalesTagModal"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Sales Person</div></button>';
                    }
                    if($result->account_type_id == 1){
                        if (RateStatus::where('user_id', $result->id)->exists() && (session('role_id') == 1 || in_array(12, session('permissions')))) {
                            if($result->rate_status == 0 || $result->rate_status == 2 || session('role_id') == 1 || in_array(session('id'), session('sale_users_bypass')) || session('role_id') == 2 || session('role_id') == 7 || in_array(session('id'), session('sale_users_bypass'))4) {
                                $dropdown .= '<button onclick="window.open(\'' . route('admin.edit.rates', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit Rates</div></button>';
                            }
                        }
                        if (RateStatus::where('user_id', $result->id)->exists() && (session('role_id') == 1 || in_array(115, session('permissions')))) {
                            $dropdown .= '<button onclick="window.open(\'' . route('admin.view.rates', ['id'=> $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Rates</div></button>';
                        }
                        if ((session('role_id') == 1 || in_array(115, session('permissions')))) {
                            $dropdown .= '<button type="button" class="dropdown-item rates_history" data-target-id=' . $result->id . ' rel="rates_history" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Rates History</div></button>';
                        }

                    }
                    else{
                        if($result->corporate_rate_type_id != 3 && $result->new_rate_type_id == null) {
                            if (CorporateRateStatus::where('user_id', $result->id)->exists() && (session('role_id') == 1 || in_array(12, session('permissions')))) {
                                $dropdown .= '<button onclick="window.open(\'' . route('admin.corporate.edit.rates', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit Rates</div></button>';
                            }
                        }
                        else if ($result->corporate_rate_type_id == 3 && $result->new_rate_type_id == null){
                            if (CorporateDefaultRateStatus::where('user_id', $result->id)->exists() && (session('role_id') == 1 || in_array(12, session('permissions')))) {
                                $dropdown .= '<button onclick="window.open(\'' . route('admin.corporate.default.edit.rates', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit Rates</div></button>';
                            }
                        }
                        else if($result->corporate_rate_type_id != null && ($result->new_rate_type_id == 1 || $result->new_rate_type_id == 2)){
                            if (PendingCorporateRateStatus::where('user_id', $result->id)->exists() && (session('role_id') == 1 || in_array(12, session('permissions')))) {
                                $dropdown .= '<button onclick="window.open(\'' . route('admin.corporate.edit.rates', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit Rates</div></button>';
                            }
                        }
                        else{
                            if (PendingCorporateDefaultRateStatus::where('user_id', $result->id)->exists() && (session('role_id') == 1 || in_array(12, session('permissions')))) {
                                $dropdown .= '<button onclick="window.open(\'' . route('admin.corporate.default.edit.rates', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit Rates</div></button>';
                            }
                        }
                        if($result->corporate_rate_type_id == 3){
                            if (CorporateDefaultRateStatus::where('user_id', $result->id)->exists() && (session('role_id') == 1 || in_array(115, session('permissions')))) {
                                $dropdown .= '<button onclick="window.open(\'' . route('admin.corporate.default.rates.view', ['id'=> $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Rates</div></button>';
                            }
                        }
                        else{
                            if (CorporateRateStatus::where('user_id', $result->id)->exists() && (session('role_id') == 1 || in_array(115, session('permissions')))) {
                                $dropdown .= '<button onclick="window.open(\'' . route('admin.corporate.view.rates', ['id'=> $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Rates</div></button>';
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
                        $dropdown .= '<button type="button" class="dropdown-item blacklist" rel="block"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-user-x "></i></div><div class="col-9 offset-1">Block</div></button>';
                    }

                    if (session('role_id') == 1 || in_array(13, session('permissions'))) {
                        if ($result->status == 3) {
                            $dropdown .= '<button type="button" class="dropdown-item userdisable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-user-minus"></i></div><div class="col-9 offset-1">Disable</div></button>';

                        }
                        else {
                            $dropdown .= '<button type="button" class="dropdown-item userenable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-user-plus"></i></div><div class="col-9 offset-1">Enable</div></button>';

                        }
                    }
                    if (session('role_id') == 1 || in_array(244, session('permissions'))) {
                        if($result->status > 0){
                            $dropdown .= '<button onclick="window.open(\'' . route('admin.accounts.view_crf_agreement', ['id' => $result->id]) . '\')" type="button" class="dropdown-item view_crf" data-target-id="' . $result->id . '"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View CRF</div></button>';
                        }
                    }

                    if(session('role_id') == 1 || in_array(110, session('permissions')))
                    {
                        $dropdown .= '<button onclick="window.open(\'' . route('admin.accounts.view.profile', ['id'=> $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Profile</div></button>';
                    }

                    $merged = MergedSisterAccount::where('user_id', $result->id);
                    if(!$merged->exists()){
                        if(session('role_id') == 1 || in_array(session('id'), session('sale_users_bypass')) || (($multiple_sale_check == true) || (($sale_check) && ($sale_check->admin_id == Auth::id())) || in_array(241, session('permissions'))))
                        {
                            $dropdown .= '<button onclick="window.open(\'' . route('admin.accounts.sister_account.add.account', ['id'=> $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Add Sister Account</div></button>';
                        }
                    }
                    $dropdown .= '<button onclick="window.open(\'' . route('admin.accounts.documents', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Documents</div></button>';
                    if (session('role_id') == 1 || in_array(149, session('permissions'))){
                        $dropdown .= '<button type="button" class="dropdown-item shipment_days_button"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Auto Shipment Cancel Days</div></button>';
                    }
                    if($sale_check){
                        $dropdown .= '<button onclick="window.open(\'' . route('admin.accounts.add_contacts', ['id'=> $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Add Contacts</div></button>';
                    }
                    if(session('role_id') == 1 || in_array(365, session('permissions'))){
                        $dropdown .= '<button type="button" class="dropdown-item payment_cycle"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-activity"></i></div><div class="col-9 offset-1">Payment Cycle</div></button>';
                    }
                    if((!InternationalUsersInformation::where('user_id', $result->id)->exists()) && (session('role_id') == 1 || in_array(439, session('permissions')))){
                        $dropdown .= '<button onclick="window.open(\'' . route('admin.international.rates.update.index', ['id'=> $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-bar-chart"></i></div><div class="col-9 offset-1">Intl Add Rates</div></button>';
                    }
                    else{
                        if(session('role_id') == 1 || in_array(439, session('permissions'))){
                            $dropdown .= '<button onclick="window.open(\'' . route('admin.international.rates.update.index', ['id'=> $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-bar-chart"></i></div><div class="col-9 offset-1">Intl Edit Rates</div></button>';
                        }
                        if(session('role_id') == 1 || in_array(440, session('permissions'))){
                            $dropdown .= '<button onclick="window.open(\'' . route('admin.international.rates.view.index', ['id'=> $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-eye"></i></div><div class="col-9 offset-1">Intl View Rates</div></button>';
                        }
                        if((session('role_id') == 1 || in_array(590, session('permissions'))) && $result->account_type_id == 2) {
                            $dropdown .= '<button type="button" class="dropdown-item credit_limit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-bar-chart"></i></div><div class="col-9 offset-1">Intl Credit Limit</div></button>';
                        }
                    }

                    if (InternationalEconomyRateStatus::where('user_id', $result->id)->doesntExist()) {
                        if(session('role_id') == 1 || in_array(528, session('permissions'))) {
                            $dropdown .= '<button onclick="window.open(\'' . route('admin.international.rates.economy.create', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-bar-chart"></i></div><div class="col-9 offset-1">Intl Add Economy Rates</div></button>';
                        }
                    } else {
                        if((session('role_id') == 1 || in_array(528, session('permissions')))) {
                            $dropdown .= '<button onclick="window.open(\'' . route('admin.international.rates.economy.create', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-bar-chart"></i></div><div class="col-9 offset-1">Intl Edit Economy Rates</div></button>';
                        }
                    }

                    if(InternationalEconomyRate::where('user_id',$result->id)->count() > 0)
                    {
                        if(session('role_id') == 1 || in_array(530, session('permissions'))) {
                            $dropdown .= '<button onclick="window.open(\'' . route('admin.international.rates.economy.create', ['id' => $result->id,'view'=>'view']) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-bar-chart"></i></div><div class="col-9 offset-1">Intl View Economy Rates</div></button>';
                        }
                    }

                    if($result->account_type_id == 2 && (session('role_id') == 1 || count(array_intersect([598, 599], session('permissions'))) !== 0)) {
                        $dropdown .= '<button onclick="window.open(\'' . route('admin.corporate.reimbursement_setting.index', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-bar-chart"></i></div><div class="col-9 offset-1">Corporate Reimbursement Setting</div></button>';
                    }

                    if (session('role_id') == 1 || in_array(619, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item restrict_order_id"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Restrict Order ID</div></button>';
                    }

                    if(session('role_id') == 1 || in_array(660, session('permissions'))){
                        $dropdown .= '<button type="button" class="dropdown-item auto_cancel_days_setting"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-minus-circle"></i></div><div class="col-9 offset-1">Auto Cancel Days</div></button>';
                    }

                    $dropdown .= '
                    </div>
                  </div>
                ';

                    return $dropdown;

                }
            })
            ->make(true);

    }


    public function pendingAccountListAjax(Request $request){

        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),61);
        }
        $users = User::join('cities', 'users.city_id', '=', 'cities.id')
            ->leftjoin('products','products.id','=','users.product_id')
            ->leftjoin('sub_category_segments as seg_sub','seg_sub.id','=','users.sub_segment_id')
            ->leftjoin('segments as seg','seg.id','=','users.segment_id')
            ->leftjoin('admins as rab','rab.id','=','users.rates_added_by')
            ->leftjoin('admins as rabb','rabb.id','=','users.rates_authorized_by')
            ->leftjoin('admins as rrb','rrb.id','=','users.rates_rejected_by')
            ->leftjoin('account_types as at','at.id','=','users.account_type_id')
            ->leftjoin('sale_person_tags as spt', function ($join) {
                $join->on('spt.user_id', '=', 'users.id')
                    ->leftjoin('admins as ad','ad.id','=','spt.admin_id')
                    ->where('spt.status','=',0);
            })
            ->leftjoin('duplicate_users as du', 'du.user_id', '=', 'users.id')
            ->leftjoin('user_document_attachments as uda','uda.user_id','=','users.id')
            ->leftjoin('admins as dab','dab.id','=','uda.approved_by')
            ->leftjoin('admins as drb','drb.id','=','uda.rejected_by')
            ->leftjoin('international_users_informations as iui', 'iui.user_id', '=', 'users.id')
            ->leftjoin('sale_tier_tags as st','st.user_id','=','users.id')
            ->leftjoin('admins as p','p.id','=','st.poc')
            ->leftjoin('admins as k','k.id','=','st.kam')
            ->leftjoin('admins as r','r.id','=','st.ref')
			->leftjoin('territories as t','t.id','=','users.territory_id')
            ->select(['rrb.name as rates_rejected_by','users.rates_added_at as rates_added_at','users.rates_approved_at as rates_approved_at','users.rates_rejected_at as rates_rejected_at','users.rate_status as rate_status','users.rejected_reason as rejected_reason','users.id','ad.name as admin_tag_id', 'users.name', 'cities.name as city' ,'users.poc', 'users.cnic','users.status', 'users.created_at','products.product_name as product_type','users.blacklist','rab.name as rates_added_by','rabb.name as rates_authorized_by','users.account_type_id','at.name as account_type','users.documents_status','users.documents_status_reason as documents_rejection_reason','users.other_product_name', 'du.phone as duplicate_phone','du.cnic as duplicate_cnic', 'du.iban as duplicate_iban','du.name as duplicate_name' ,'uda.uploaded_at as documents_uploaded_at','uda.approved_at as documents_approved_at','dab.name as documents_approved_by','drb.name as documents_rejected_by','uda.rejected_at as documents_rejected_at', 'iui.status as international_status', 'iui.status as international_rate_status', 'iui.rejected_reason as international_rejected_reason','p.name as tagged_poc','k.name as kam','r.name as ref', 'users.corporate_rate_type_id','users.email','t.name as territory','users.address as address','seg.name as segment','seg_sub.name as sub_segment'])->whereIn('users.status',[0,1,2,5])->where('blacklist',0)->where('users.email_verified',1);

        if (session('role_id') != 1) {
            $users = $users->whereIn('cities.hub_id', session('hubs'));
        }
        if(session('department_id') == 7){
            if(in_array(session('id'), session('sale_users_bypass')) ){
                $users = $users->whereIn('users.id', session('tagged_shippers'));
            }
        }
        if($sale_persons = $request->get('sale_persons')){
            $users = $users->whereIn('ad.id', $sale_persons);
        }

        if($search_cnic = $request->get('search_cnic')){
            $users = $users->where('users.cnic', $search_cnic);
        }
        if($search_shipper = $request->get('search_shipper')){
            $users = $users->where('users.name', 'like', '%' . $search_shipper . '%');
        }

        if($search_iban = $request->get('search_iban')){
            $users = $users->join('user_bank_infos as ubi', function($join) use ($search_iban){
                $join->on('ubi.user_id', '=', 'users.id')
                    ->where('ubi.iban', $search_iban);
            });
        }

        if($search_email = $request->get('search_email')){
            $users = $users->where('users.email', $search_email);
        }
        return Datatables::of($users)
            ->addColumn('id_padded', function ($user) {
                return str_pad($user->id, 6, '0', STR_PAD_LEFT);
            })
            ->filterColumn('users.id', function ($query, $keyword) {
                return $query->where('users.id', '=', $keyword);
            })
            ->editColumn('rejected_reason',function ($users){
                if($users->rejected_reason != null && $users->rate_status==2){
                    return $users->rejected_reason;
                }else{
                    return "-";
                }
            })
            ->editColumn('rate_status',function ($users){
                if($users->rate_status == 2) {
                    return "Rejected";
                }
                else if($users->rate_status == 1) {
                    return "Requested";
                }
                else if($users->rate_status == 0 && $users->status==2){
                    return "Authorized";
                }
                else if($users->rate_status == 0 && $users->status==1) {
                    return "Requested";
                }
            })
            ->editColumn('documents_status',function ($users){
                if($users->documents_status == 0){
                    return "Incomplete";
                }
                elseif($users->documents_status == 1){
                    return "Pending for Approval";
                }
                elseif($users->documents_status == 2){
                    return "Approved";
                }
                elseif($users->documents_status == 3){
                    return "Rejected";
                }
            })

            ->editColumn('status', function ($users) {
                return $users->status == 0? 'Request Received': ($users->status == 1? 'Rates Added' : ($users->status == 2? 'Pending for Activation': ($users->status == 5? 'Rates Rejected':'')));
            })
            ->filterColumn('status', function($query, $keyword) {
                $keyword = strtolower($keyword);

                if ($keyword == 0 || $keyword == 1 || $keyword == 2 || $keyword == 5) {
                    $query->where('users.status', '=', $keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->editColumn('product_type', function($user){
                if($user->product_type == 'Other'){
                    return $user->other_product_name;
                }else{
                    return $user->product_type;
                }
            })
            ->filterColumn('product_type',function ($query,$keyword){

                if ($keyword != '' || $keyword != 24) {
                    $query->where('products.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn('duplication', function($users){
                $count = 0;
                if($users->duplicate_phone != null){
                    $count++;
                }
                if($users->duplicate_cnic != null){
                    $count++;
                }
                if($users->duplicate_iban != null){
                    $count++;
                }
                if($users->duplicate_name != null){
                    $count++;
                }
                if($count > 0){
                    return '<button class="btn btn-sm btn-outline-info align-middle duplicate_modal">' . $count . '</button>';
                }else{
                    return $count;
                }
            })
            ->editColumn('international_rate_status',function ($users){
                if($users->international_rate_status != null){
                    if($users->international_rate_status == 1){
                        return "Approved";
                    }elseif($users->international_rate_status == 2){
                        return "Requested";
                    }elseif($users->international_rate_status == 3){
                        return "Rejected";
                    }elseif($users->international_rate_status == 4){
                        return "Requested";
                    }elseif($users->international_rate_status == 5){
                        return "Rejected";
                    }
                }
                else{
                    return "International Rates are not set";
                }
            })
            ->editColumn('international_rejected_reason',function ($users){
                if($users->international_rejected_reason != null && $users->international_rate_status == 3){
                    return $users->international_rejected_reason;
                }else{
                    return "-";
                }
            })
            ->addColumn("action", function ($result) {
                if(in_array($result->id, session('tagged_shippers'))){
                    $multiple_sale_check = true;
                }
                else{
                    $multiple_sale_check = false;
                }
                $sale_check= SalePersonTag::where('user_id',$result->id)->first();
                $dropdown = '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm accounts">
                ';

                if(session('role_id') == 1 || in_array(361, session('permissions'))){
                    $dropdown .= '<button type="button" class="dropdown-item remove_sales_tier"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-minus-circle"></i></div><div class="col-9 offset-1">Remove Sales Tier Tagging</div></button>';
                }

                $dropdown .= '<button type="button" class="dropdown-item" data-target-id="' . $result->id . '" data-toggle="modal" data-target="#BankInfoModal"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Bank Info</div></button>';

                $dropdown .= '<button type="button" class="dropdown-item" data-target-id="' . $result->id . '" data-toggle="modal" data-target="#ShippingInfoModal"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Shipping Info</div></button>';
                if(session('role_id') == 1 || in_array(session('id'), session('sale_users_bypass')))
                {
                    $dropdown .= '<button type="button" class="dropdown-item" data-target-id="' . $result->id . '" data-toggle="modal" data-target="#SalesTagModal"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Sales Person</div></button>';
                }
                if(($result->status == 2 || $result->international_status == 1) && $result->documents_status == 2 && (session('role_id') == 1 || in_array(9, session('permissions')))) {
                    $dropdown .= '<button type="button" class="dropdown-item active_account" rel="activate" data-target-id="' . $result->id . '"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Activate Account</div></button>';

                }
                if(($sale_check != null || $multiple_sale_check) && $result->status != 2) {
                    if($result->account_type_id == 1){
                        if (RateStatus::where('user_id', $result->id)->exists() && (session('role_id') == 1 || in_array(7, session('permissions')))) {
                            if($result->status != 2 && ($result->rate_status == 0 || $result->rate_status == 2 || $result->status == 5 || session('role_id') == 1 || in_array(session('id'), session('sale_users_bypass')) || session('role_id') == 2 || session('role_id') == 7 || in_array(session('id'), session('sale_users_bypass'))4)) {
                                $dropdown .= '<button onclick="window.open(\'' . route('admin.edit.rates', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit Rates</div></button>';
                            }
                        } else {
                            if (session('role_id') == 1 || in_array(6, session('permissions'))) {
                                $dropdown .= '<button onclick="window.open(\'' . route('admin.add.rates', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Add Rates</div></button>';
                            }
                        }
                    }else{
                        if($result->corporate_rate_type_id == null){
                            $dropdown .= '<button type="button" class="dropdown-item rate_type"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-bar-chart-2"></i></div><div class="col-9 offset-1">Add Rate Type</div></button>';
                        }
                        else{
                            if (CorporateRateStatus::where('user_id', $result->id)->exists() && (session('role_id') == 1 || in_array(7, session('permissions'))) && ($result->corporate_rate_type_id == 1 || $result->corporate_rate_type_id ==2)) {
                               
                                if($result->status != 2) {
                                    $dropdown .= '<button onclick="window.open(\'' . route('admin.corporate.edit.rates', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit Rates</div></button>';
                                }
                            }

                            else if (CorporateDefaultRateStatus::where('user_id', $result->id)->exists() && (session('role_id') == 1 || in_array(7, session('permissions'))) && $result->corporate_rate_type_id == 3) {
                                if($result->status != 2) {
                                    $dropdown .= '<button onclick="window.open(\'' . route('admin.corporate.default.edit.rates', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit Rates</div></button>';
                                }
                            }
                            else {

                                if (session('role_id') == 1 || in_array(6, session('permissions'))) {
                                    $dropdown .= '<button onclick="window.open(\'' . route('admin.corporate.add.rates', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Add Rates</div></button>';
                                }
                            }
                        }

                    }


                }

//                if($result->account_type_id == 1){
                    if(($result->rate_status == 0 && $result->status == 2) && (InternationalUsersInformation::where('user_id', $result->id)->exists() == false) && (session('role_id') == 1 || in_array(8, session('permissions')))){
                        $dropdown .= '<button type="button" class="dropdown-item reject_rates" rel="block"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Reject Rates</div></button>';
                    }
//                }

                if($result->account_type_id == 1){
                    if (RateStatus::where('user_id', $result->id)->exists() && (session('role_id') == 1 || in_array(114, session('permissions')))) {
                        if($result->status != 0) {
                            $dropdown .= '<button onclick="window.open(\'' . route('admin.view.rates', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Rates</div></button>';
                        }
                    }
                }else{
                    if($result->status != 0) {
                        if (CorporateRateStatus::where('user_id', $result->id)->exists() && (session('role_id') == 1 || in_array(114, session('permissions'))) && ($result->corporate_rate_type_id == 1 || $result->corporate_rate_type_id ==2)) {
                            $dropdown .= '<button onclick="window.open(\'' . route('admin.corporate.view.rates', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Rates</div></button>';
                        }
                        else if(CorporateDefaultRateStatus::where('user_id', $result->id)->exists() && (session('role_id') == 1 || in_array(114, session('permissions'))) && $result->corporate_rate_type_id == 3){
                            $dropdown .= '<button onclick="window.open(\'' . route('admin.corporate.default.rates.view', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Rates</div></button>';
                        }
                    }
                }
                if($result->blacklist == 0 && (session('role_id') == 1 || in_array(10, session('permissions')))) {
                    $dropdown .= '<button type="button" class="dropdown-item blacklist" rel="block"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-user-x "></i></div><div class="col-9 offset-1">Block</div></button>';
                }


                if(session('role_id') == 1 || in_array(110, session('permissions')))
                {
                    $dropdown .= '<button onclick="window.open(\'' . route('admin.accounts.view.profile', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Profile</div></button>';
                }
                $merged = MergedSisterAccount::where('user_id', $result->id);
                if($sale_check != null) {
                    if (!$merged->exists()) {
                        if (session('role_id') == 1 || in_array(session('id'), session('sale_users_bypass')) || (($multiple_sale_check == true) || (($sale_check) && ($sale_check->admin_id == Auth::id())) || in_array(241, session('permissions')))) {
                            $dropdown .= '<button onclick="window.open(\'' . route('admin.accounts.sister_account.add.account', ['id'=> $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Add Sister Account</div></button>';
                        }
                    }
                }
                if (session('role_id') == 1 || in_array(244, session('permissions'))) {
                    if($result->status > 0){
                        $dropdown .= '<button onclick="window.open(\'' . route('admin.accounts.view_crf_agreement', ['id' => $result->id]) . '\')" type="button" class="dropdown-item view_crf" data-target-id="' . $result->id . '"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View CRF</div></button>';
                    }
                }
                $dropdown .= '<button onclick="window.open(\'' . route('admin.accounts.documents', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Documents</div></button>';

                if($sale_check){
                    $dropdown .= '<button onclick="window.open(\'' . route('admin.accounts.add_contacts', ['id'=> $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Add Contacts</div></button>';
                }
                if(($sale_check != null || $multiple_sale_check) && $result->status != 2) {

                    if((!InternationalUsersInformation::where('user_id', $result->id)->exists()) && (session('role_id') == 1 || in_array(439, session('permissions')))){
                        $dropdown .= '<button onclick="window.open(\'' . route('admin.international.rates.update.index', ['id'=> $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-bar-chart"></i></div><div class="col-9 offset-1">Intl Add Rates</div></button>';
                    }
                    else{
                        if(session('role_id') == 1 || in_array(439, session('permissions'))){
                            $dropdown .= '<button onclick="window.open(\'' . route('admin.international.rates.update.index', ['id'=> $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-bar-chart"></i></div><div class="col-9 offset-1">Intl Edit Rates</div></button>';
                        }
                        if(session('role_id') == 1 || in_array(440, session('permissions'))){
                            $dropdown .= '<button onclick="window.open(\'' . route('admin.international.rates.view.index', ['id'=> $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-eye"></i></div><div class="col-9 offset-1">Intl View Rates</div></button>';
                        }
                    }
                }

                if (InternationalEconomyRateStatus::where('user_id', $result->id)->doesntExist()) {
                    if(session('role_id') == 1 || in_array(528, session('permissions'))) {
                        $dropdown .= '<button onclick="window.open(\'' . route('admin.international.rates.economy.create', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-bar-chart"></i></div><div class="col-9 offset-1">Intl Add Economy Rates</div></button>';
                    }
                } else {
                    if((session('role_id') == 1 || in_array(528, session('permissions')))) {
                        $dropdown .= '<button onclick="window.open(\'' . route('admin.international.rates.economy.create', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-bar-chart"></i></div><div class="col-9 offset-1">Intl Edit Economy Rates</div></button>';
                    }
                }

                if(InternationalEconomyRate::where('user_id',$result->id)->count() > 0)
                {
                    if(session('role_id') == 1 || in_array(530, session('permissions'))) {
                        $dropdown .= '<button onclick="window.open(\'' . route('admin.international.rates.economy.create', ['id' => $result->id,'view'=>'view']) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-bar-chart"></i></div><div class="col-9 offset-1">Intl View Economy Rates</div></button>';
                    }
                }

                if($result->account_type_id == 2 && (session('role_id') == 1 || count(array_intersect([598, 599], session('permissions'))) !== 0)) {
                    $dropdown .= '<button onclick="window.open(\'' . route('admin.corporate.reimbursement_setting.index', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-bar-chart"></i></div><div class="col-9 offset-1">Corporate Reimbursement Setting</div></button>';
                }

                if (session('role_id') == 1 || in_array(619, session('permissions'))) {
                    $dropdown .= '<button type="button" class="dropdown-item restrict_order_id"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Restrict Order ID</div></button>';
                }

                $dropdown .= '
                    </div>
                  </div>
                ';

                return $dropdown;
            })
            ->make(true);

    }
    public function blockAccountListAjax(Request $request){

        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),276);
        }
        $users = User::join('cities', 'users.city_id', '=', 'cities.id')
            ->leftjoin('sale_person_tags as spt', function ($join) {
                $join->on('spt.user_id', '=', 'users.id')
                    ->leftjoin('admins as ad','ad.id','=','spt.admin_id')
                    ->where('spt.status','=',0);
            })
            ->leftjoin('sale_tier_tags as st','st.user_id','=','users.id')
            ->leftjoin('admins as a','a.id','=','st.poc')
            ->leftjoin('admins as d','d.id','=','st.kam')
            ->leftjoin('admins as h','h.id','=','st.ref')
            ->select(['users.id', 'users.name','users.disable_at as disable_at', 'cities.name as city' ,'users.poc','users.blacklist_reason as reason','ad.name as admin_tag_id','a.name as poc_tagged','d.name as kam','h.name as ref'])->where('blacklist',1);

        if (session('role_id') != 1) {
            $users = $users->whereIn('cities.hub_id', session('hubs'));
        }
        if(session('department_id') == 7){
            if(in_array(session('id'), session('sale_users_bypass')) ){
                $users = $users->whereIn('users.id', session('tagged_shippers'));
            }
        }
        if($sale_persons = $request->get('sale_persons')){
            $users = $users->whereIn('ad.id', $sale_persons);
        }
        if($search_email = $request->get('search_email')){
            $users = $users->where('users.email',$search_email);
        }
        return Datatables::of($users)
            ->addColumn('id_padded', function ($user) {
                return str_pad($user->id, 6, '0', STR_PAD_LEFT);
            })
            ->filterColumn('users.id', function ($query, $keyword) {
                return $query->where('users.id', '=', $keyword);
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


                if(session('role_id') == 1 || in_array(110, session('permissions')))
                {
                    $dropdown .= '<button onclick="window.open(\'' . route('admin.accounts.view.profile', ['id' => $result->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Profile</div></button>';
                }
                if (session('role_id') == 1 || in_array(244, session('permissions'))) {
                    if($result->status > 0){
                        $dropdown .= '<button onclick="window.open(\'' . route('admin.accounts.view_crf_agreement', ['id' => $result->id]) . '\')" type="button" class="dropdown-item view_crf" data-target-id="' . $result->id . '"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View CRF</div></button>';
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

    //User Profile Methods

    public function userProfile($id)
    {
        $user = User::find($id);
        
        $product = Product::find($user->product_id);
        $products = Product::all();
        $banks = BanksList::all();
        $invoicing_cycle = InvoicingCycle::all();
        $city_list = City::all();
        $emails = ShipperNotificationEmail::where('user_id',$user->id)->select('email')->get();
        $email_ids = ShipperNotificationEmail::where('user_id',$user->id)->pluck('email')->toArray();
        $email_ids = implode(',', $email_ids);
        $reference = Reference::where('id', $user->reference_id)->first();
        $segments = Segment::all();
        $sub_segments = SubCategorySegment::where('segment_id',$user->segment->id)->get();
        $average_shipment_duration = AverageShipmentCycle::where('id', $user->average_shipment_duration_id)->first();
        $user_bank_default = UserBankInfo::where('user_id', $user->id)->where('default_bank', 1)->first();
        $territories = Territory::select('id','name')->get();
        return view('admin.accounts.profile')->with(['user'=>$user,'product_name'=>$product->product_name,'banks'=>$banks,'all_cities'=>$city_list,'products'=>$products,'invoicing_cycle' => $invoicing_cycle , 'emails' => $emails, 'email_ids' => $email_ids, 'reference' => $reference, 'average_shipment_duration' => $average_shipment_duration, 'user_bank_default' => $user_bank_default,'segments' => $segments,'sub_segments' => $sub_segments,'territories' => $territories]);
    }

    public function updateProfile(Request $request)
    {
        $user_id = $request->user_id;

        //1 for Admin, 0 for User

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'address'=>'required|string|max:255',
            'poc'=>'required|string|max:255',
            'phone'=>'required|string|max:255',
            'cnic'=>'required|string|max:255',
            'segment_id' => 'required',
            'sub_segment_id' => 'required'
            
        ]);


        $flag = true;
        $users = User::where('email', $request->email)->orWhere('phone', $request->phone)->get();
        if($users){
            foreach($users as $user){
                if($user_id != $user->id) {
                    $flag = false;
                }
            }
        }

        if($flag == true){
            if($request->password=="" || $request->password==null)
            {
                User::where('id',$user_id)->update(['name'=>$request->name,'poc'=>$request->poc,'email'=>$request->email,'address'=>$request->address,'phone'=>$request->phone,'phone2'=>$request->phone2,'cnic'=>$request->cnic,
                    'ntn_no'=>$request->ntn_no,'strn_no'=>$request->strn_no,'updated_by_type'=>1,'updated_by_id'=>Auth::id(),'city_id'=>$request->city_id, 'segment_id'=>$request->segment_id, 'sub_segment_id'=>$request->sub_segment_id, 'url'=>$request->url,'product_id'=>$request->product_id, 'other_product_name' => $request->has('product_name')? $request->product_name:null, 'brand_name' => $request->has('brand_name')? $request->brand_name:null]);
                AdminLogs::create([
                    'admin_id'=>Auth::id(),
                    'user_id'=>$user_id

                ]);
            }
            else
            {
                User::where('id',$user_id)->update(['name'=>$request->name,'poc'=>$request->poc,'email'=>$request->email,'address'=>$request->address,'phone'=>$request->phone,'phone2'=>$request->phone2,'cnic'=>$request->cnic,
                    'ntn_no'=>$request->ntn_no,"password"=>Hash::make($request->password),'updated_by_type'=>1,'updated_by_id'=>Auth::id(),'city_id'=>$request->city_id,'segment_id' => $request->segment_id, 'sub_segment_id'=>$request->sub_segment_id, 'url'=>$request->url,'product_id'=>$request->product_id, 'brand_name' => $request->has('brand_name')? $request->brand_name:null]);
            }

            return redirect()->back()->with(['success'=>"Profile Information Successfully Updated"]);
        }
        else{
            return redirect()->back()->with(['error'=>"Email Address and Phone Number must be unique"]);
        }
    }

    public function updateBankInfo(Request $request)
    {

        $user_id = $request->user_id;
        $user = User::find($user_id);
        //1 for Admin, 0 for User

        $request->validate([
            'bank_name'=>'required|max:255',
            'bank_branch'=>'required|string|max:255',
            'account_no'=>'required|string|max:255',
            'account_title'=>'required|string|max:255',
            'iban'=>'required|string|max:255',
        ]);
        $old_bank_detail = UserBankInfo::where('user_id',$user_id)->select('bank_name')->first();

        if($user->account_type_id == 1){
            UserBankInfo::where('user_id',$user_id)->update(['bank_branch'=>$request->bank_branch,'bank_name'=>$request->bank_name,'account_no'=>$request->account_no,
                'account_title'=>$request->account_title,'iban'=>$request->iban,'city_id'=>$request->bank_city]);

        }else{
            $generation_date = null;
            if($request->invoicing_cycle_id == 2 || $request->invoicing_cycle_id == 4 ){
                $generation_date = null;
            }else{
                $generation_date = $request->generation_date;
            }
            UserBankInfo::where('user_id',$user_id)->update([
                'bank_branch'=>$request->bank_branch,
                'bank_name'=>$request->bank_name,
                'account_no'=>$request->account_no,
                'account_title'=>$request->account_title,
                'iban'=>$request->iban,
                'city_id'=>$request->bank_city,
                'invoicing_cycle_id' => $request->invoicing_cycle_id,
                'generation_date' => $generation_date,
                'billing_person_name' => $request->billing_person_name,
                'billing_person_phone' => $request->billing_person_phone,
                'billing_person_email' => $request->billing_person_email,
                'billing_address' => $request->billing_address
            ]);
        }
        if($old_bank_detail->bank_name != $request->bank_name){
            $bank_history = new HistoryShipperBankAccount();
            $bank_history->user_id = $user_id;
            $bank_history->bank_id = $old_bank_detail->bank_name;
            $bank_history->save();

        }
        AdminLogs::create([
            'admin_id' => Auth::id(),
            'user_id' => $user_id
        ]);
        return redirect()->back()->with(['success'=>"Bank Information Successfully Updated"]);
    }

    public function getPickups(Request $request)
    {
        $pickups = UserShippingInfo::join('cities as c', 'user_shipping_infos.city_id', '=', 'c.id')
            ->select(['user_shipping_infos.id as id','user_shipping_infos.pickup_brand_name as pickup_brand_name','user_shipping_infos.pickup_address as pickup_address','user_shipping_infos.poc as poc','user_shipping_infos.phone as phone','user_shipping_infos.email as email','user_shipping_infos.status as status','user_shipping_infos.default_address as default_address','user_shipping_infos.user_id as user_id','c.name as city_name', 'user_shipping_infos.vendor'])
            ->where('user_id',$request->user_id)
            ->where('hidden', 0);

        return Datatables::of($pickups)
            ->addColumn("status", function ($result) {
                if($result->default_address==1)
                {
                    $status =  "Default Address";
                }
                elseif($result->status==1)
                {
                    $status =  "Enabled";
                }
                elseif($result->status==0)
                {
                    $status =  "Disabled";
                }
                return $status;
            })
            ->filterColumn('status',function($query,$keyword){
                if ($keyword != '') {
                    if($keyword == 2){
                        $query->where('user_shipping_infos.default_address',1);
                    }else{
                        $query->where('user_shipping_infos.status',$keyword);
                    }
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->make(true);
    }


    public function cityView(Request $request){
//        $req = $request->route();
//        $uri_path = $req->getPath();
//        $uri_parts = explode('/', $uri_path);
//        $uri_tail = end($uri_parts);
//        return $uri_tail;
//        $hubs = City::where('hub',1)->get();
//        return $hubs[0]->id;
        ActivityTrailController::createActivityTrailLog(Auth::id(),348);

        $business_categories = BusinessCategory::all();
        return view('admin.management.city_management')->with(['business_categories' => $business_categories]);
    }
    public function cityListAjax(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),349);
        }

        $cities = City::join('cities as h' ,'cities.hub_id', '=' , 'h.id')
            ->leftjoin('city_histories as ch',function($join){
                $join->on('ch.city_id', '=', 'cities.id')
                    ->where('ch.created_at','=',
                        DB::raw('(select max(created_at) from city_histories where city_histories.city_id = cities.id)'));
            })
            ->leftjoin('admins as a', 'a.id', '=', 'ch.updated_by')
            ->leftjoin('business_categories as bc', 'bc.id', '=', 'cities.business_category_id')
            ->join('zones as z', 'cities.zone_id', '=', 'z.id')
            ->select(['cities.id as city_id','cities.city_code as city_code','cities.id as id','cities.name as name' ,'h.name as hub','cities.hub_id','z.name as zone','cities.hub as isHub','cities.status as status', 'ch.created_at as updated_at' , 'a.name as updated_by', 'cities.gc_area as gc_area', 'cities.attempt_tat as attempt_tat','cities.location_latitude','cities.location_longitude', 'cities.address as address', 'cities.business_category_id as business_category_id', 'bc.name as business_category','cities.hub_location_latitude','cities.hub_location_longitude']);

        return Datatables::of($cities)
            ->editColumn('status', function ($cities) {
                return ($cities->status == 1)? 'Active': 'Inactive';
            })
            ->editColumn('gc_area', function ($cities) {
                return ($cities->gc_area == 1)? 'Yes': 'No';
            })
            ->filterColumn('modes',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('sm.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn('location', function ($result){
                $location = '<div class="text-center">';
                if($result->location_latitude != null && $result->location_longitude != null) {
                    $location .= '<button type="button" class="btn btn-primary btn-sm"><a class="white" href="http://www.google.com/maps/place/' . $result->location_latitude . ',' . $result->location_longitude . '" target="_blank"><i class="la la-map-marker align-middle"></i></a></button>';
                    $location .= '</div>';
                    return $location;
                }else{
                    return '-';
                }
            })
            ->addColumn('hub_location', function ($result){
                $location = '<div class="text-center">';
                if($result->hub_location_latitude != null && $result->hub_location_longitude != null) {
                    $location .= '<button type="button" class="btn btn-primary btn-sm"><a class="white" href="http://www.google.com/maps/place/' . $result->hub_location_latitude . ',' . $result->hub_location_longitude . '" target="_blank"><i class="la la-map-marker align-middle"></i></a></button>';
                    $location .= '</div>';
                    return $location;
                }else{
                    return '-';
                }
            })
            ->addColumn("action", function ($result) {
                if (session('role_id') == 1 || count(array_intersect([90, 91], session('permissions'))) !== 0) {
                    $dropdown = '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                ';

                    if (session('role_id') == 1 || in_array(90, session('permissions'))) {
                        if($result->business_category_id == 1){
                            $dropdown .= '<button type="button" class="dropdown-item" data-target-id=' . $result->city_id . ' rel="editcity" data-toggle="modal" data-target="#editCity"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Update City Status</div></button>';
                        }
                        else{
                            $dropdown .= '<button type="button" class="dropdown-item" data-target-id=' . $result->city_id . ' rel="editinternationalcity" data-toggle="modal" data-target="#editInternationalCity"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Update International City Status</div></button>';
                        }
                    }

                    if (session('role_id') == 1 || in_array(91, session('permissions'))) {
                        if ($result->status == 1) {
                            $dropdown .= '<button type="button" class="dropdown-item deactivate" data-target-id=' . $result->city_id . ' rel="cityInactive" hub=' . $result->isHub . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Deactivate City</div></button>';
                        }
                        else {
                            $dropdown .= '<button type="button" class="dropdown-item deactivate" data-target-id=' . $result->city_id . ' rel="cityactive" hub=' . $result->isHub . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Activate City</div></button>';
                        }
                    }

                    $dropdown .= '
                    </div>
                  </div>
                ';

                    return $dropdown;
                }
                else {
                    return '';
                }
            })
            ->addColumn('osa_list', function ($result){
                $osa_count = CityOsaRate::where('city_id',$result->city_id);
                if($osa_count->exists()){
                    $btn = '<div class="text-center">';
                    $btn .= '<button type="button" class="btn btn-primary btn-sm">'.$osa_count->count().'</button>';
                    $btn .= '</div>';
                    return $btn;
                }else{
                    return '-';
                }
            })
            ->make(true);
    }

    public function getCityForm(){
        $hubs = City::where('hub',1)->where('business_category_id', 1)->where('status',1)->get();
        $zones = Zone::where('business_category_id', 1)->get();
        $shippingMode = ShippingMode::all();
        $booking = BookingType::where('id','!=',4)->get();
        return view('admin.management.add_city_form')->with(['hubs'=>$hubs,'zones' => $zones, 'shippingMode'=>$shippingMode,'bookings'=>$booking]);
    }
    public function getEditCityForm($id){
        $city = City::find($id);
        if($city->hub == 1){
            $cityhub = '';
            $isHub = 1;
        }else{
            $cityhub = City::select(['id','name'])->where('id',$city->hub_id)->get();
            $isHub = 0;

        }
        $delivery_array = CityDelivery::where('city_id',$city->id)->select(['booking_type_id','shipping_mode_id'])->get();
        $delivery = array();
        foreach ($delivery_array as $delivery_details) {
            $delivery[$delivery_details['booking_type_id']][] = $delivery_details['shipping_mode_id'];
        }


        $hubs = City::where('hub',1)->where('business_category_id', 1)->where('status',1)->get();
        $zones = Zone::where('business_category_id', 1)->get();
        $shippingMode = ShippingMode::all();
        $booking = BookingType::where('id','!=',4)->get();
        $walk_in_city = WalkInCities::where('city_id',$city['id'])->get();
        $walk_in_delivery = array();
        $osa_list = CityOsaRate::where('city_id',$city->id)->get();

        foreach ($walk_in_city as $walk_in_detail){
            $walk_in_delivery[$walk_in_detail['delivery']] = $walk_in_detail['delivery'];
        }
        return view('admin.management.edit_city_form')->with(['hubs'=>$hubs, 'zones' => $zones, 'shippingMode'=>$shippingMode,'bookings'=>$booking,'isHub'=>$isHub,'city'=>$city,'delivery'=>$delivery,'cityhub'=>$cityhub, 'walk_in_city' => $walk_in_delivery, 'osa_list' => $osa_list]);

    }

    public function updateCity(Request $request,$id){
        CityOsaRate::where('city_id',$id)->delete();

        $city_id = City::where('id',$id)->first();
        if($city_id){
            if($request->has('updatedelivery') && count($request->updatedelivery) > 0){
                if($request->postType == 'city'){
                    City::where('id',$id)->update([
                        'name'=>$request->cityName,
                        'city_code'=>$request->city_code,
                        'hub'=>0,
                        'hub_id'=>$request->hubs,
                        'zone_id'=>City::find($request->hubs)->zone_id,
                        'pickup'=>($request->has('pickup'))? 1:0,
                        'gc_area'=>($request->has('gc_area'))? 1:0,
                        'attempt_tat'=>$request->attempt_tat,
                        'location_latitude' => $request->latitude,
                        'location_longitude' => $request->longitude,
                        'hub_location_latitude' => $request->hub_latitude,
                        'hub_location_longitude' => $request->hub_longitude,
                        'address' => $request->address
                    ]);
                    CityHistory::create([
                        'city_id'=> $id,
                        'hub' => 0,
                        'hub_id'=> $request->hubs,
                        'zone_id'=> City::find($request->hubs)->zone_id,
                        'pickup'=> ($request->has('pickup'))? 1:0,
                        'status' => $city_id->status,
                        'gc_area'=>($request->has('gc_area'))? 1:0,
                        'attempt_tat'=>$request->attempt_tat,
                        'updated_by' => Auth::id(),
                        'location_latitude' => $request->latitude,
                        'location_longitude' => $request->longitude,
                        'hub_location_latitude' => $request->hub_latitude,
                        'hub_location_longitude' => $request->hub_longitude,
                        'address' => $request->address
                    ]);
                    WalkInCities::where('city_id',$id)->delete();
                    if(!empty($request->walk_in_delivery)) {
                        foreach ($request->walk_in_delivery as $index => $delivery_walk_in) {
                            WalkInCities::create([
                                'city_id' => $id,
                                'pickup' => ($request->has('pickup')) ? 1 : 0,
                                'delivery' => $index,
                            ]);
                        }
                    }

                    CityDelivery::where('city_id',$id)->delete();

                    foreach ($request->updatedelivery as $booking_type_id => $shipping_modes) {
                        foreach ($shipping_modes as $shipping_mode_id => $shipping_mode_value) {
                            CityDelivery::create([
                                'city_id'=>$id,
                                'booking_type_id'=>$booking_type_id,
                                'shipping_mode_id'=>$shipping_mode_id,
                            ]);
                        }
                    }
                    if($request->osa_name != null){

                        foreach ($request->osa_name as $key => $value) {
    
                            $osa_charges = new CityOsaRate();
                            $osa_charges->city_id = $id;
                            $osa_charges->osa_name = $value;
                            $osa_charges->osa_rate = $request->osa_rate[$key];
                            $osa_charges->admin_id = Auth::id();
                            $osa_charges->save();
                        }
                    }
                    return redirect()->back()->with('success','City updated successfully');
                }
                elseif($request->postType == 'hub'){
                    City::where('id',$id)->update([
                        'name'=>$request->cityName,
                        'city_code'=>$request->city_code,
                        'hub'=>1,
                        'hub_id'=>$id,
                        'zone_id'=>$request->zone_id,
                        'pickup'=>($request->has('pickup'))? 1:0,
                        'gc_area'=>($request->has('gc_area'))? 1:0,
                        'attempt_tat'=>$request->attempt_tat,
                        'location_latitude' => $request->latitude,
                        'location_longitude' => $request->longitude,
                        'hub_location_latitude' => $request->hub_latitude,
                        'hub_location_longitude' => $request->hub_longitude,
                        'address' => $request->address
                    ]);
                    CityHistory::create([
                        'city_id'=> $id,
                        'hub'=>1,
                        'hub_id'=>$id,
                        'zone_id'=>$request->zone_id,
                        'pickup'=>($request->has('pickup'))? 1:0,
                        'status' => $city_id->status,
                        'gc_area'=>($request->has('gc_area'))? 1:0,
                        'attempt_tat'=>$request->attempt_tat,
                        'updated_by' => Auth::id(),
                        'location_latitude' => $request->latitude,
                        'location_longitude' => $request->longitude,
                        'hub_location_latitude' => $request->hub_latitude,
                        'hub_location_longitude' => $request->hub_longitude,
                        'address' => $request->address
                    ]);
                    WalkInCities::where('city_id',$id)->delete();
                    if(!empty($request->walk_in_delivery)) {
                        foreach ($request->walk_in_delivery as $index => $delivery_walk_in) {
                            WalkInCities::create([
                                'city_id' => $id,
                                'pickup' => ($request->has('pickup')) ? 1 : 0,
                                'delivery' => $index,
                            ]);
                        }
                    }

                    CityDelivery::where('city_id',$id)->delete();

                    foreach ($request->updatedelivery as $booking_type_id => $shipping_modes) {
                        foreach ($shipping_modes as $shipping_mode_id => $shipping_mode_value) {
                            CityDelivery::create([
                                'city_id'=>$id,
                                'booking_type_id'=>$booking_type_id,
                                'shipping_mode_id'=>$shipping_mode_id,
                            ]);
                        }
                    }
                    if($request->osa_name != null){
                    
                        foreach ($request->osa_name as $key => $value) {
    
                            $osa_charges = new CityOsaRate();
                            $osa_charges->city_id = $id;
                            $osa_charges->osa_name = $value;
                            $osa_charges->osa_rate = $request->osa_rate[$key];
                            $osa_charges->admin_id = Auth::id();
                            $osa_charges->save();
                        }
                    }
                    return redirect()->back()->with('success','Hub/city updated successfully');
                }
            }
            else{
                return redirect()->back()->with('error', 'Please select atleast one shipping mode!');
            }
        }

    }
    //update city end
    public function addCityHub(Request $request){
      
        if($request->postType == 'city'){
            $zone_id = City::find($request->hubs)->zone_id;

            $city = City::create([
                'name'=>$request->cityName,
                'city_code'=>$request->city_code,
                'hub'=>0,
                'hub_id'=>$request->hubs,
                'zone_id'=> $zone_id,
                'pickup'=>($request->has('pickup'))? 1:0,
                'gc_area'=>($request->has('gc_area'))? 1:0,
                'attempt_tat'=>$request->attempt_tat,
                'status'=>1,
                'location_latitude' => $request->latitude,
                'location_longitude' => $request->longitude,
                'hub_location_latitude' => $request->hub_latitude,
                'hub_location_longitude' => $request->hub_longitude,
                'address' => $request->address
            ]);

            CityHistory::create([
                'city_id'=> $city->id,
                'hub'=>0,
                'hub_id'=>$request->hubs,
                'zone_id'=> $zone_id,
                'pickup'=>($request->has('pickup'))? 1:0,
                'status'=>1,
                'gc_area'=>($request->has('gc_area'))? 1:0,
                'attempt_tat'=>$request->attempt_tat,
                'updated_by' => Auth::id(),
                'location_latitude' => $request->latitude,
                'location_longitude' => $request->longitude,
                'hub_location_latitude' => $request->hub_latitude,
                'hub_location_longitude' => $request->hub_longitude,
                'address' => $request->address
            ]);

            if(!empty($request->walk_in_delivery)) {
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
                        'city_id'=>$city->id,
                        'booking_type_id'=>$booking_type_id,
                        'shipping_mode_id'=>$shipping_mode_id,
                    ]);
                }
            }

            $zones = Zone::where('business_category_id', 1)->get();
            foreach ($zones as $zone){
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
            if($request->osa_name != null){
            
                foreach ($request->osa_name as $key => $value) {
    
                    $osa_charges = new CityOsaRate();
                    $osa_charges->city_id = $city->id;
                    $osa_charges->osa_name = $value;
                    $osa_charges->osa_rate = $request->osa_rate[$key];
                    $osa_charges->admin_id = Auth::id();
                    $osa_charges->save();
                }
            }

            return redirect()->back()->with('success','City added successfully');
        }elseif($request->postType == 'hub'){
            $city = City::create([
                'name'=>$request->cityName,
                'city_code'=>$request->city_code,
                'hub'=>1,
                'zone_id'=>$request->zone_id,
                'pickup'=>($request->has('pickup'))? 1:0,
                'gc_area'=>($request->has('gc_area'))? 1:0,
                'attempt_tat'=>$request->attempt_tat,
                'status'=>1,
                'location_latitude' => $request->latitude,
                'location_longitude' => $request->longitude,
                'hub_location_latitude' => $request->hub_latitude,
                'hub_location_longitude' => $request->hub_longitude,
                'address' => $request->address
            ]);

            CityHistory::create([
                'city_id'=> $city->id,
                'hub'=>1,
                'zone_id'=>$request->zone_id,
                'pickup'=>($request->has('pickup'))? 1:0,
                'status'=>1,
                'gc_area'=>($request->has('gc_area'))? 1:0,
                'attempt_tat'=>$request->attempt_tat,
                'updated_by' => Auth::id(),
                'location_latitude' => $request->latitude,
                'location_longitude' => $request->longitude,
                'hub_location_latitude' => $request->hub_latitude,
                'hub_location_longitude' => $request->hub_longitude,
                'address' => $request->address
            ]);

            if(!empty($request->walk_in_delivery)) {
                foreach ($request->walk_in_delivery as $index => $delivery_walk_in) {
                    WalkInCities::create([
                        'city_id' => $city->id,
                        'pickup' => ($request->has('pickup')) ? 1 : 0,
                        'delivery' => $index,
                    ]);
                }
            }

            City::where('id',$city->id)->update(['hub_id'=>$city->id]);

            foreach ($request->delivery as $booking_type_id => $shipping_modes) {
                foreach ($shipping_modes as $shipping_mode_id => $shipping_mode_value) {
                    CityDelivery::create([
                        'city_id'=>$city->id,
                        'booking_type_id'=>$booking_type_id,
                        'shipping_mode_id'=>$shipping_mode_id,
                    ]);
                }
            }

            $zones = Zone::where('business_category_id', 1)->get();
            foreach ($zones as $zone){
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
            if($request->osa_name != null){
            
                foreach ($request->osa_name as $key => $value) {
    
                    $osa_charges = new CityOsaRate();
                    $osa_charges->city_id = $city->id;
                    $osa_charges->osa_name = $value;
                    $osa_charges->osa_rate = $request->osa_rate[$key];
                    $osa_charges->admin_id = Auth::id();
                    $osa_charges->save();
                }
            }
            return redirect()->back()->with('success','Hub city added successfully');
        }
    }

    public function CityStatus(Request $request){
        $id = $request->cid; //city id
        $status = $request->status;
        if($status == 'cityInactive'){
            $city = City::find($id);
            if($city->status == 1 && $city->hub == 1){
                $citylist = City::where('hub_id',$id)->where('id','!=',$id)->where('status', 1)->count();
                if($citylist == 0){
                    City::where('id',$city->id)->update(['status'=>0]);
                    $zone_cities = City::where('zone_id', $city->zone_id)->where('status', 1)->count();
                    if($zone_cities == 0){
                        $zone = Zone::find($city->zone_id);
                        $zone->status = 0;
                        $zone->save();
                    }
                    return redirect()->route('admin.management.city')->with('success', 'City is inactive now.');
                }else{
                    return redirect()->route('admin.management.city')->with('error', 'There are some active cities in hub, please deactivate those cities first!');
                }
            }elseif ($city->status == 1){
                $city->status = 0;
                $city->save();
                return redirect()->route('admin.management.city')->with('success', 'City is inactive now.');
            }
        }elseif($status == 'cityactive'){
            $city = City::find($id);
            $zone = Zone::find($city->zone_id);
            if($zone->status != 1){
                return redirect()->route('admin.management.city')->with('error', 'Please, activate or change Zone for city first!');
            }
            if($city->hub == 1){
                if($city->status == 0){
                    $city->status = 1;
                    $city->save();
                    return redirect()->route('admin.management.city')->with('success', 'City is active now.');
                }
            }else{
                $hub = City::where('id', $city->hub_id)->where('status','=', 1);
                if($hub->exists()){
                    $city->status = 1;
                    $city->save();
                    return redirect()->route('admin.management.city')->with('success', 'City is active now.');

                }else{
                    return redirect()->route('admin.management.city')->with('error', 'Please, activate or change hub for city first!');

                }
            }



            if($city->status == 0){
                $action = City::where('id',$city->id)->update(['status'=>1]);
                if($action == 1){
                    return redirect()->route('admin.management.city')->with('success', 'City is active now.');
                }else{
                    return redirect()->route('admin.management.city')->with('danger', 'There is some problem please try again.');
                }
            }else{
                return redirect()->route('admin.management.city')->with('danger', 'This city is already inactive.');

            }
        }
        return redirect()->route('admin.management.city')->with('danger', 'This city is already inactive.');
    }

    public function CityStatusCheck($id){
        $hubs = City::select('name')->where('hub_id',$id)->where('id','!=',$id)->where('status', 1)->get();

        return response()->json($hubs);
    }
    //route management
    public function routeView(){
        $route_type = RouteType::all();
        $users = User::join('user_shipping_infos as usi','usi.user_id','=','users.id')->select('users.id','pickup_address','users.name','usi.id as address_id')->where('usi.status',1)->get();
        return view('admin.management.route_management')->with(['users' => $users ,'route_type' =>$route_type]);
    }
    public function routeListAjax(){
        $routes = Route::join('cities','routes.city_id','=','cities.id')
            ->leftjoin('route_types as rt','rt.id','=','routes.route_type_id')
            ->leftjoin('riders','riders.route_id','=','routes.id')
            ->select(['cities.name as city','routes.id as id','routes.code as code','routes.start','routes.end','routes.junction','routes.status as status','routes.created_at','rt.id as route_type_id ','rt.name as route_type','riders.name as rider']);

        if (session('role_id') != 1) {
            $routes = $routes->whereIn('cities.hub_id', session('hubs'));
        }

        return Datatables::of($routes)
            ->editColumn('status', function ($routes) {
                return ($routes->status == 0)? 'Inactive': 'Active';
            })
            ->filterColumn('status', function($query, $keyword) {
                $keyword = strtolower($keyword);

                if (strpos('active', $keyword) !== FALSE) {
                    $query->where('routes.status', '=', 1);
                }
                else if (strpos('inactive', $keyword) !== FALSE) {
                    $query->where('routes.status', '=', 0);
                }
                else {
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
                        }
                        else {
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
                }
                else {
                    return '';
                }
            })
            ->make(true);
    }
    public function addRouteView(){
        $route_types = RouteType::all();
        $cities = City::where('business_category_id', 1)->select(['id','name'])->get();
        $riders = Rider::where('status', 1)->select(['id','name'])->get();
        return view('admin.management.add_route_form')->with(['cities'=>$cities,'riders' => $riders ,'route_types' => $route_types]);
    }
    public function addRouteDetails(Request $request){

        //dd($request);
        $validations = [
            'city_id'=>'required|numeric',
            'route_code'=>'required',
            'start'=>'required',
            'route_type_id'=>'required',
            'end'=>'required',
            'junction'=>'required'
        ];
        $validate = Validator::make($request->all(), $validations);

        if ($validate->fails()) {
            return redirect()->back()
                ->withErrors($validate);
        }
        $route = Route::create([
            'city_id'=>$request->city_id,
            'code'=>$request->route_code,
            'start'=>$request->start,
            'route_type_id'=>$request->route_type_id,
            'end'=>$request->end,
            'junction'=>$request->junction,
            'status'=>1
        ]);
        if(Rider::where('route_id','=',$route->id)->exists()){
            return redirect()->back()->with('error', 'Route id already exists !');
        }
        else{
            $rider_id = $request->rider_id;
            $rider = Rider::find($rider_id);
            if($rider){
                $rider->route_id = $route->id;
                $rider->save();
            }
        }
        return redirect()->back()->with('success','Route added successfully');
    }
    public function editRouteView($id){
        $citylist = City::select(['id','name'])->get();
        $riders = Rider::where('status',1)->select(['id','name'])->get();
        $route_types = RouteType::all();
        $current_rider = Rider::where('route_id',$id);
        if($current_rider->exists()){
            $current_rider = $current_rider->select('id')->first();
            $current_rider_id = $current_rider->id;
        }
        else{
            $current_rider_id = NULL;
        }
        $route = Route::find($id);
        $route_type_id = $route->route_type_id;
        $current_route_type_id = RouteType::find($route_type_id)->id;
        return view('admin.management.edit_route_form')->with(['route_id'=>$id,'cities'=>$citylist,'route'=>$route,'riders' => $riders ,'current_rider_id' => $current_rider_id,'route_types' => $route_types,'current_route_type_id' => $current_route_type_id]);
    }
    public function editRouteDetails(Request $request, $id){
        $validations = [
            'city_id'=>'required|numeric',
            'route_code'=>'required',
            'start'=>'required',
            'end'=>'required',
            'route_type_id'=>'required',
            'junction'=>'required'
        ];
        $validate = Validator::make($request->all(), $validations);

        if ($validate->fails()) {
            return redirect()->back()
                ->withErrors($validate);
        }
        $route = Route::where('id',$id)->update([
            'city_id'=>$request->city_id,
            'code'=>$request->route_code,
            'start'=>$request->start,
            'end'=>$request->end,
            'route_type_id'=>$request->route_type_id,
            'junction'=>$request->junction,
        ]);

        $existing_riders = Rider::where('route_id', $id);
        if($existing_riders->exists()){
            $existing_riders= $existing_riders->get();
            foreach ($existing_riders as $existing_rider){
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
            if($rider){
                $rider->route_id = $id;
                $rider->save();
            }
//        }
        return redirect()->back()->with('success','Route updated successfully');
    }
    public function routeStatus(Request $request){
        $id = $request->cid;
        $status = $request->status;
//        return $id;
        if($status == 'routeActive'){
            $route = Route::where('id',$id)->update(['status'=>1]);
            if($route){
                return redirect()->back()->with('success','Route is activated successfully');
            }
        }else if($status == 'routeInactive'){
            Route::where('id',$id)->update(['status'=>0]);
            return redirect()->back()->with('success','Route is now inactive');

        }

    }
    public function riderView(){
        //$route_types = RouteType::all();
        $category = RiderCategory::all();
        return view('admin.management.rider_management')->with(['categories'=>$category/*,'route_types' =>$route_types*/]);
    }
    public function riderListAjax(){

        $rider = Rider::join('cities','riders.city_id','=','cities.id')
            ->join('cities as c','cities.hub_id','=','c.id')
            ->join('routes','routes.id','=','riders.route_id')
            ->join('rider_categories','rider_categories.id','=','riders.rider_category_id')
            ->leftjoin('admins as cb', 'cb.id', '=', 'riders.created_by')
            ->leftjoin('admins as ub', 'ub.id', '=', 'riders.updated_by')
            ->select(['cities.name as city','c.name as hub','riders.id as rider_id','riders.id','riders.name as rider', 'riders.trax_id' ,'riders.phone','riders.cnic',                'riders.address','routes.code as route','routes.start','routes.end','rider_categories.name as category','riders.status as                           status','riders.created_at','cb.name as created_by', 'ub.name as updated_by']);

        if (session('role_id') != 1) {
            $rider = $rider->whereIn('cities.hub_id', session('hubs'));
        }

        return Datatables::of($rider)
            ->editColumn('status', function ($rider) {
                return ($rider->status == 0)? 'Inactive': 'Active';
            })
            ->editColumn('trax_id', function ($rider) {
                if($rider->trax_id != null){
                    return $rider->trax_id;
                }
                else{
                    return '-';
                }

            })
            ->editColumn('route', function ($rider) {
                return $rider->route.' ('.$rider->start. ' to '.$rider->end.')';
            })
            ->filterColumn('route',function($query, $keyword){
                $keyword = strtolower($keyword);
                if ($keyword != '') {
                    $query->where('routes.code', 'like', '%'.$keyword.'%')->orWhere('routes.start', 'like', '%'.$keyword.'%')->orWhere('routes.end', 'like', '%'.$keyword.'%');
                }

                else {
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
                        }
                        else {
                            $dropdown .= '<button type="button" class="dropdown-item deactivate" data-target-id=' . $rider->id . '  rel="riderActive"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Activate Rider</div></button>';
                        }
                    }

                    $dropdown .= '
                        </div>
                      </div>
                    ';

                    return $dropdown;
                }
                else {
                    return '';
                }
            })
            ->make(true);
    }
    public function addRiderView(){
        $city = City::where('business_category_id', 1)->select(['id','name'])->get();
        $route_types = RouteType::all();
        $category = RiderCategory::all();
        return view('admin.management.add_rider_form')->with(['cities'=>$city,'categories'=>$category,'route_types' => $route_types]);
    }
    public function categoryListAjax(Request $request){
        $city_id = $request->id;
        $route = Route::select(['id','code','start','end'])->where('city_id',$city_id)->where('status',1);

        if (session('role_id') != 1) {
            $route = $route->whereHas('city', function ($query) {
                $query->whereIn('hub_id', session('hubs'));
            });
        }

        $route = $route->get();

        return response()->json($route);
    }
    public function addRiderDetails(Request $request){

        $validations = [
            'city_id'=>'required|numeric',
            'rider_name'=>'required|max:255',
            'phone'=>'required|max:255',
            'cnic'=>'required|max:255',
            'address'=>'required|max:255',
            'route_id'=>'required|numeric',
            'rider_category'=>'required|numeric',
            'pin' => 'required|numeric',
        ];
        $validate = Validator::make($request->all(), $validations);

        if ($validate->fails()) {
            return redirect()->back()
                ->withErrors($validate);
        }
        $global_setting = GlobalSettings::where('type', 'latest_employee_id');

        if($global_setting->exists()){
            $global_setting = $global_setting->first();
            $trax_id = $global_setting->setting_value + 1;
            $global_setting->setting_value = $trax_id;
            $global_setting->save();
            $trax_id = 'Trax'. str_pad($trax_id, 5, '0', STR_PAD_LEFT);
        }
        else{
            $trax_id = null;
        }
        $rider = Rider::create([
            'city_id'=>$request->city_id,
            'name'=>$request->rider_name,
            'phone'=>$request->phone,
            'cnic'=>$request->cnic,
            'address'=>$request->address,
            'route_id'=>$request->route_id,
            'rider_category_id'=>$request->rider_category,
            'status'=>1,
            'special_rider' => ($request->has('special_rider_checkbox')? 1:0),
            'pin'=> bcrypt($request->pin),
            'created_by' => Auth::id(),
            'trax_id' => $trax_id,
        ]);
        if($rider){
            NotificationsController::send(61, $rider->id, $request->pin);
            return redirect()->back()->with('success','Rider added successfully');
        }

    }
    public function editRiderView($id){
        $city = City::where('business_category_id', 1)->select(['id','name'])->get();
        $category = RiderCategory::all();
        $rider = Rider::find($id);
        $route_types = RouteType::all();
        $route = Route::where('city_id',$rider->city_id)->get();
        return view('admin.management.edit_rider_form')->with(['rider_id'=>$id,'cities'=>$city,'categories'=>$category,'rider'=>$rider,'routes'=>$route,'route_types' => $route_types]);
    }
    public function editRiderDetails(Request $request,$id){

        $validations = [
            'city_id'=>'required|numeric',
            'rider_name'=>'required|max:255',
            'phone'=>'required|max:255',
            'cnic'=>'required|max:255',
            'address'=>'required|max:255',
            'route_id'=>'required|numeric',
            'rider_category'=>'required|numeric'
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

        if($request->has('special_rider_checkbox')){
            $rider->special_rider = 1;
        }else{
            $rider->special_rider = 0;
        }

        if($request->pin != '') {
            if($rider->dummy_pin != $request->pin) {
                $rider->pin = bcrypt($request->pin);
                $rider->dummy_pin = $request->pin;

                NotificationsController::send(61, $rider->id, $request->pin);
            }
        }
        $rider->updated_by = Auth::id();
        $rider->save();

        if($rider){
            return redirect()->back()->with('success','Rider updated successfully');
        }
    }

    public function riderStatus(Request $request){
        $id = $request->cid;
        $status = $request->status;
        if($status == 'riderActive'){
            $rider = Rider::where('id',$id)->update(['status'=>1,'updated_by'=>Auth::id()]);
            if($rider){
                return redirect()->back()->with('success','Rider is activated successfully');
            }
        }else if($status == 'riderInactive'){
            $rider =Rider::where('id',$id)->update(['status'=>0, 'route_id' => null,'updated_by'=>Auth::id()]);
            if($rider){
                return redirect()->back()->with('success','Rider is now inactive');
            }

        }

    }

    public function rider_phone_unique(Request $request) {
        if ($request->filled('phone')) {
            if ($request->input('phone') == '0213-8772222') {
                return 'true';
            }
            else {
                $rider = Rider::where('phone', $request->input('phone'));

                if ($request->has('id')) {
                    $rider = $rider->where('id', '!=', $request->input('id'));
                }

                if (!$rider->exists()) {
                    return 'true';
                }
                else {
                    return 'false';
                }
            }
        }
        else {
            return 'true';
        }
    }

    public function add_notification_emails(Request $request){
        $emails = $request->email_address;
        if($emails != ''){
            $email_address = explode(',', $emails);
            $user = $request->add_shipper_id;
            ShipperNotificationEmail::where('user_id',$user)->delete();
            foreach ($email_address as $email){

                $shipper_notification_email = new ShipperNotificationEmail();
                $shipper_notification_email->user_id = $user;
                $shipper_notification_email->email = $email;
                $shipper_notification_email->save();

            }
            return redirect()->back()->with('success', 'Email Address Added.');

        }
        else{
            return back()->with('danger', 'There is no email selected!');
        }
    }
    public function edit_notification_emails(Request $request){

        $emails = $request->email_address;
        if($emails != ''){
            $email_address = explode(',', $emails);
            $user = $request->edit_shipper_id;
            foreach ($email_address as $email){
                if(!ShipperNotificationEmail::where('user_id',$user)->where('email','=',$email)->exists()){
                    $shipper_notification_email = new ShipperNotificationEmail();
                    $shipper_notification_email->user_id = $user;
                    $shipper_notification_email->email = $email;
                    $shipper_notification_email->save();
                }
            }
            ShipperNotificationEmail::where('user_id',$user)->whereNotIn('email',$email_address)->delete();
            return redirect()->back()->with('success', 'Email Address updated.');

        }
        else{
            return back()->with('danger', 'There is no email selected!');
        }
    }

    public function walk_in_city_list(){
        $cities = City::select('id', 'name')->get();
        $walk_in_cities = WalkInCities::all();
        $shipping_modes = ShippingMode::where('id', '<', 4)->get();
        $pickup_cities = City::select('id', 'name')->where('pickup', 1)->get();
        $delivery_types = DeliveryType::get();
        return view('admin.management.walk_in_city_list')->with(['cities' => $cities, 'walk_in_cities' => $walk_in_cities, 'shipping_modes' => $shipping_modes, 'pickup_cities' => $pickup_cities, 'delivery_types' => $delivery_types]);
    }

    public function check_min_charges(Request $request){
        if($request->pickup_city != null && $request->consignee_city != null) {
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

    public function add_sister_account_view(Request $request){
        $user = User::leftjoin('cities as c', 'c.id', '=', 'users.city_id')->leftjoin('products as p', 'p.id', '=', 'users.product_id')->leftjoin('sale_person_tags as spt', 'spt.user_id', '=', 'users.id')->leftjoin('admins as a', 'a.id', '=', 'spt.admin_id')->select('users.id as id', 'users.name as company_name', 'c.name as city', 'users.poc as contact_name', 'users.phone as phone', 'users.address as address', 'users.email as email', 'users.status as status', 'p.product_name as product_type', 'a.name as tagged_to')->where('users.id', $request->id)->first();
        return view('admin.accounts.sister_accounts.add')->with(['first_account' => $user]);
    }

    public function get_account_info(Request $request){
        $user_id = $request->id;
        $check_user = User::where('id', $user_id);
        if($check_user->exists()){
            $check_user = $check_user->first();
            if($check_user->blacklist == 0){
                $check_merged_accounts = MergedSisterAccount::where('user_id', $user_id);

                if(!$check_merged_accounts->exists()){
                    $user = User::leftjoin('cities as c', 'c.id', '=', 'users.city_id')->leftjoin('products as p', 'p.id', '=', 'users.product_id')->leftjoin('sale_person_tags as spt', 'spt.user_id', '=', 'users.id')->leftjoin('admins as a', 'a.id', '=', 'spt.admin_id')->select('users.id as id', 'users.name as company_name', 'c.name as city', 'users.poc as contact_name', 'users.phone as phone', 'users.address as address', 'users.email as email', 'users.status as status', 'p.product_name as product_type', 'a.name as tagged_to')->where('users.id', $request->id)->first();

                    return ['status' => 0, 'info' => $user];
                }
                else{
                    return ['status' => 1, 'error' => "Already registered as a sister account"];
                }
            }
            else{
                return ['status' => 1, 'error' => "Account ID is blocked!"];
            }
        }
        else{
            return ['status' => 1, 'error' => "Account ID does'nt exists!"];
        }
    }

    public function add_sister_account_submit(Request $request){
        $account_ids = explode(',',$request->account_ids);
        if(count($account_ids) > 1){
            $merge_account_head = new MergedAccountHead();
            $merge_account_head->name = $request->group_name;
            $merge_account_head->created_by = Auth::id();
            $merge_account_head->save();
            foreach ($account_ids as $index => $account_id){
                $sister_account = new MergedSisterAccount();
                $sister_account->merged_head_id = $merge_account_head->id;
                $sister_account->user_id = $account_id;
                $sister_account->save();

                foreach ($account_ids as $notify_index => $notify_account_id){
                    if($index != $notify_index){
                        NotificationsController::send(36, $notify_account_id, $account_id);
                    }
                }
            }
            return redirect()->route('admin.accounts.merged_account.index')->with(['success'=>"Accounts merged successfully."]);
        }
        else{
            return redirect()->back()->with('error', "Sister accounts are not selected!");
        }
    }

    public function merged_accounts_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),277);
        return view('admin.accounts.sister_accounts.merged_accounts.index');
    }
    public function merged_accounts_list(Request $request){

        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),278);
        }

        $merged_accounts = MergedAccountHead::leftjoin('admins as ac', 'ac.id', '=', 'merged_account_heads.created_by')
            ->leftjoin('admins as au', 'au.id', '=', 'merged_account_heads.updated_by')
            ->select('merged_account_heads.id as id', 'merged_account_heads.name as name', 'merged_account_heads.created_at as created_at', 'merged_account_heads.updated_at as updated_at', 'ac.name as created_by', 'au.name as updated_by', DB::raw('(select count(id) from merged_sister_accounts where merged_sister_accounts.merged_head_id = merged_account_heads.id) as accounts'));
        return Datatables::of($merged_accounts)

            ->editColumn('accounts_button', function ($users){
                return '<div class="text-center"><button type="button" class="btn btn-sm btn-outline-info accounts_button">' . $users->accounts . '</button></div>';
            })
            ->editColumn('updated_by', function($users){
                if($users->updated_by != null){
                    return $users->updated_by;
                }
                else{
                    return "-";
                }
            })
            ->editColumn('updated_at', function($users){
                if($users->updated_by != null){
                    return $users->updated_at;
                }
                else{
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
            ->make(true);

    }

    public function merged_accounts_info(Request $request){
        $merged_head_id = $request->id;
        $accounts = MergedSisterAccount::leftjoin('users as u', 'u.id', '=', 'merged_sister_accounts.user_id')
            ->leftjoin('cities as c', 'c.id', '=', 'u.city_id')
            ->select('u.id as id', 'u.name as name', 'u.poc as poc', 'u.phone as phone', 'u.address as address', 'c.name as city')
            ->where('merged_head_id', $merged_head_id)
            ->get();
        return response(['accounts' => $accounts]);
    }

    public function edit_sister_account_view(Request $request){
        $group_name = MergedAccountHead::where('id', $request->id)->first();
        $merged_accounts = MergedAccountHead::leftjoin('merged_sister_accounts as msa', 'msa.merged_head_id', '=', 'merged_account_heads.id')->leftjoin('users as u', 'u.id', '=', 'msa.user_id')->leftjoin('cities as c', 'c.id', '=', 'u.city_id')->leftjoin('products as p', 'p.id', '=', 'u.product_id')->leftjoin('sale_person_tags as spt', 'spt.user_id', '=', 'u.id')->leftjoin('admins as a', 'a.id', '=', 'spt.admin_id')->select('u.id as id', 'u.name as company_name', 'c.name as city', 'u.poc as contact_name', 'u.phone as phone', 'u.address as address', 'u.email as email', 'u.status as status', 'p.product_name as product_type', 'a.name as tagged_to')->where('merged_account_heads.id', $request->id)->groupBy('u.id')->get();
        return view('admin.accounts.sister_accounts.edit')->with(['merged_accounts' => $merged_accounts, 'group_name' => $group_name]);
    }
    public function edit_sister_account_submit(Request $request){
        $account_ids = explode(',',$request->account_ids);
        $merged_accounts = MergedSisterAccount::where('merged_head_id', $request->merged_id)->pluck('user_id')->toArray();
        $new_merged = array_diff($account_ids, $merged_accounts);
        $remove_merged = array_diff($merged_accounts, $account_ids);
        $previous_accounts = array_diff($merged_accounts, $new_merged, $remove_merged);
        if(count($account_ids) > 1){
            $merge_account_head = MergedAccountHead::where('id', $request->merged_id)->first();
            $merge_account_head->name = $request->group_name;
            $merge_account_head->updated_by = Auth::id();
            $merge_account_head->save();
            MergedSisterAccount::where('merged_head_id', $merge_account_head->id)->whereIn('user_id', $remove_merged)->delete();
            foreach($remove_merged as $remove_account_id){
                foreach ($previous_accounts as $previous){
                    NotificationsController::send(37, $previous, $remove_account_id);
                }
            }
            foreach ($new_merged as $account_id){
                $sister_account = new MergedSisterAccount();
                $sister_account->merged_head_id = $merge_account_head->id;
                $sister_account->user_id = $account_id;
                $sister_account->save();
                foreach ($previous_accounts as $previous){
                    NotificationsController::send(36, $previous, $account_id);
                }
            }

            return redirect()->route('admin.accounts.merged_account.index')->with('success', "Sister accounts updated successfully!");
        }
        else{
            return redirect()->back()->with('error', "Sister accounts are not selected!");
        }
    }

    public function merged_accounts_mapping_info(Request $request){
        $merged_accounts = MergedSisterAccount::leftjoin('users as u', 'u.id', '=', 'merged_sister_accounts.user_id')->select('u.id as id', 'u.name as company_name')->where('merged_sister_accounts.merged_head_id', $request->id)->get();
        $merged_mapping = MergedSisterAccountMapping::where('merged_head_id', $request->id)->select('head_user_id', 'sister_user_id')->get();
        return response(['merged_accounts' => $merged_accounts, 'merged_mapping' => $merged_mapping]);
    }
    public function merged_accounts_mapping_submit(Request $request){
        $merged_account = MergedAccountHead::where('id', $request->id)->first();
        $merged_account->updated_by = Auth::id();
        $merged_account->save();
        MergedSisterAccountMapping::where('merged_head_id', $merged_account->id)->delete();
        if($request->has('sister_account')){
            foreach ($request->sister_account as $index => $account){
                foreach ($request->sister_account[$index] as $sub_index => $switch) {
                    if($switch == "on"){
                        $new_mapping = new MergedSisterAccountMapping();
                        $new_mapping->merged_head_id = $merged_account->id;
                        $new_mapping->head_user_id = $index;
                        $new_mapping->sister_user_id = $sub_index;
                        $new_mapping->save();
                    }
                }
            }
            return redirect()->back()->with('success', "Mapping updated successfully!");
        }
        else{
            return redirect()->back()->with('success', "Mapping updated successfully!");
        }
    }

    public function warehousing_active(Request $request){
        $shipper = User::find($request->id);
        if($shipper && ($shipper->warehousing == 0)){
            $shipper->warehousing = 1;
            $shipper->save();

            return response()->json(['status' => 1, 'success' => 'Warehousing activated successfully!']);
        }else{
            return response()->json(['status' => 0, 'error' => 'Warehousing already activated!']);
        }
    }
    public function warehousing_inactive(Request $request){
        $shipper = User::find($request->id);
        if($shipper && ($shipper->warehousing == 1)){
            $shipper->warehousing = 0;
            $shipper->save();

            return response()->json(['status' => 1, 'success' => 'Warehousing deactivated successfully!']);
        }else{
            return response()->json(['status' => 0, 'error' => 'Warehousing already deactivated!']);
        }
    }

    public function update_profile_password(Request $request){
        return view('admin.profile.change_password');
    }

    public function update_profile_password_submit(Request $request){
        $request->validate([
            'password' => 'required|string|min:4',
        ]);
        if($request->password == $request->confirm_password){
            $admin = Admin::where('id',Auth::id());

            $admin->update(['password' => Hash::make($request->password),'dummy_pin' => $request->password , 'updated_by' => Auth::id()]);

            $admin = $admin->first();
            $employee = Employee::where('trax_id',$admin->trax_id)->where('trax_id','!=',null);
            if($employee->exists())
            {
                $employee = $employee->first();
                $employee->pin = $admin->dummy_pin;
                $employee->update();
            }

//            if(session()->has('first_login') && session('first_login') != 1){
//                session(['first_login' => 1]);
//                Admin::where('id',Auth::id())->update(['first_login' => 1]);
//            }
            return redirect()->back()->with(['success'=>"Pin Updated Successfully!"]);
        }
        else{
            return redirect()->back()->with(['error'=>"The pin and confirmation pin do not match!"]);
        }
    }

    public function userDocuments($id){
        $documents = UserDocumentAttachment::where('user_id', $id)->first();

        $user = User::find($id);
        if($documents == null){
            $documents = false;
        }
        return view('admin.profile.documents')->with(['id' => $id, 'documents' => $documents, 'document_status' => $user->documents_status, 'shipper' => $user->name]);
    }

    public function viewUserDocuments($id, $check, $pdf){
        $user_documents = UserDocumentAttachment::where('user_id', $id)->first();
        if($check == 'filled_and_signed_pdf'){
            $file = $user_documents->filled_and_signed_pdf;
        }
        elseif ($check == 'signed_acknowledgement_pdf'){
            $file = $user_documents->signed_acknowledgement_pdf;
        }
        elseif ($check == 'cnic_front_image'){
            $file = $user_documents->cnic_front_image;
        }
        elseif ($check == 'cnic_back_image'){
            $file = $user_documents->cnic_back_image;
        }
        elseif ($check == 'blank_cheque_image'){
            $file = $user_documents->blank_cheque_image;
        }
        elseif ($check == 'e_sign_image'){
            $file = $user_documents->e_sign_image;
        }
        else{
            return redirect()->back()->with('error', 'File not found!');
        }
        $url = Storage::url('users_attached_documents/' . $id . '/'. $file);
        return view('admin.profile.documents_view')->with(['url' => $url, 'pdf' => $pdf]);
    }
    public function approveDocuments($id, $approve, $reason){

        $user = User::find($id);
        $user_document = UserDocumentAttachment::where('user_id',$id)->first();
        if($approve == 1){
            $user->documents_status = 2;
            $user->documents_status_reason = null;
            $user->save();
            $user_document->approved_at = Carbon::now();
            $user_document->approved_by = Auth::id();
            $user_document->save();
            return redirect()->back()->with(['success' => 'Files approved successfully']);

        }
        else if($approve == 0){
            $user->documents_status = 3;
            $user->documents_status_reason = $reason;
            $user->save();
            $user_document->rejected_at = Carbon::now();
            $user_document->rejected_by = Auth::id();
            $user_document->save();
            return redirect()->back()->with(['success' => 'Files rejected successfully']);
        }
        else{
            return redirect()->back()->with(['error' => 'Something went wrong']);
        }

    }
    public function userDocumentsEdit(Request $request){
        $user_attachment = UserDocumentAttachment::where('user_id', $request->user_id)->first();
        if($user_attachment){
            return response()->json(['status' => 1, 'user_attachment' => $user_attachment]);
        }
        else{
            return response()->json(['status' => 0]);
        }
    }
    public function uploadDocuments(Request $request){
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
        if($user_attachment){
            if ($request->hasFile('filled_and_signed_pdf')) {
                if($user_attachment->filled_and_signed_pdf != NULL) {
                    Storage::disk('public')->delete('users_attached_documents/' . $request->user_id . '/' . $user_attachment->filled_and_signed_pdf);
                }
                $filename = 'filled_and_signed_pdf_'. $date . '_' . $request->user_id . '.pdf';
                $file = $request->file('filled_and_signed_pdf');
                Storage::disk('public')->putFileAs('users_attached_documents/'. $request->user_id .'', $file, $filename);
                $user_attachment->filled_and_signed_pdf = $filename;
            }
            if ($request->hasFile('signed_acknowledgement_pdf')) {
                if($user_attachment->signed_acknowledgement_pdf != NULL) {
                    Storage::disk('public')->delete('users_attached_documents/' . $request->user_id . '/' . $user_attachment->signed_acknowledgement_pdf);
                }
                $filename = 'signed_acknowledgement_pdf_'. $date . '_' . $request->user_id . '.pdf';
                $file = $request->file('signed_acknowledgement_pdf');
                Storage::disk('public')->putFileAs('users_attached_documents/'. $request->user_id .'', $file, $filename);
                $user_attachment->signed_acknowledgement_pdf = $filename;
            }
            if ($request->hasFile('cnic_front_image')) {
                if($user_attachment->cnic_front_image != NULL) {
                    Storage::disk('public')->delete('users_attached_documents/' . $request->user_id . '/' . $user_attachment->cnic_front_image);
                }
                $filename = 'cnic_front_image_'. $date . '_' . $request->user_id . '.png';
                $file = $request->file('cnic_front_image');
                Storage::disk('public')->putFileAs('users_attached_documents/'. $request->user_id .'', $file, $filename);
                $user_attachment->cnic_front_image = $filename;
            }
            if ($request->hasFile('cnic_back_image')) {
                if($user_attachment->cnic_back_image != NULL) {
                    Storage::disk('public')->delete('users_attached_documents/' . $request->user_id . '/' . $user_attachment->cnic_back_image);
                }
                $filename = 'cnic_back_image_'. $date . '_' . $request->user_id . '.png';
                $file = $request->file('cnic_back_image');
                Storage::disk('public')->putFileAs('users_attached_documents/'. $request->user_id .'', $file, $filename);
                $user_attachment->cnic_back_image = $filename;
            }
            if ($request->hasFile('blank_cheque_image')) {
                if($user_attachment->blank_cheque_image != NULL) {
                    Storage::disk('public')->delete('users_attached_documents/' . $request->user_id . '/' . $user_attachment->blank_cheque_image);
                }
                $filename = 'blank_cheque_image_'. $date . '_' . $request->user_id . '.png';
                $file = $request->file('blank_cheque_image');
                Storage::disk('public')->putFileAs('users_attached_documents/'. $request->user_id .'', $file, $filename);
                $user_attachment->blank_cheque_image = $filename;
            }
            $user_attachment->uploaded_at = Carbon::now();
            $user_attachment->uploaded_by = Auth::id();
            $user_attachment->save();
        }
        else{

            $new_user_attachment = new UserDocumentAttachment();
            if ($request->hasFile('filled_and_signed_pdf')) {
                $filename = 'filled_and_signed_pdf_'. $date . '_' . $request->user_id . '.pdf';
                $file = $request->file('filled_and_signed_pdf');
                Storage::disk('public')->putFileAs('users_attached_documents/'. $request->user_id .'', $file, $filename);
                $new_user_attachment->filled_and_signed_pdf = $filename;
            }

            if ($request->hasFile('signed_acknowledgement_pdf')) {
                $filename = 'signed_acknowledgement_pdf_'. $date . '_' . $request->user_id . '.pdf';
                $file = $request->file('signed_acknowledgement_pdf');
                Storage::disk('public')->putFileAs('users_attached_documents/'. $request->user_id .'', $file, $filename);
                $new_user_attachment->signed_acknowledgement_pdf = $filename;
            }

            if ($request->hasFile('cnic_front_image')) {
                $filename = 'cnic_front_image_'. $date . '_' . $request->user_id . '.png';
                $file = $request->file('cnic_front_image');
                Storage::disk('public')->putFileAs('users_attached_documents/'. $request->user_id .'', $file, $filename);
                $new_user_attachment->cnic_front_image = $filename;
            }

            if ($request->hasFile('cnic_back_image')) {
                $filename = 'cnic_back_image_'. $date . '_' . $request->user_id . '.png';
                $file = $request->file('cnic_back_image');
                Storage::disk('public')->putFileAs('users_attached_documents/'. $request->user_id .'', $file, $filename);
                $new_user_attachment->cnic_back_image = $filename;
            }

            if ($request->hasFile('blank_cheque_image')) {
                $filename = 'blank_cheque_image_'. $date . '_' . $request->user_id . '.png';
                $file = $request->file('blank_cheque_image');
                Storage::disk('public')->putFileAs('users_attached_documents/'. $request->user_id .'', $file, $filename);
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

    public function userDocumentsConfirm(Request $request){
        $user = User::find($request->user_id);
        if($user){
            $user_attachment = UserDocumentAttachment::where('user_id', $request->user_id)->first();
            if($user_attachment->filled_and_signed_pdf != null && $user_attachment->signed_acknowledgement_pdf != null  && $user_attachment->cnic_front_image != null  && $user_attachment->cnic_back_image != null  && $user_attachment->blank_cheque_image != null){
                $user->documents_status = 1;
                $user->save();
            }

            return response()->json(['status' => 1,'success' => 'Files confirmed successfully']);
        }
        return response()->json(['error' => 'User not found!']);
    }
    public function edit_rates_user_documents(Request $request){
        $date = Carbon::now();
        $user_attachment = UserDocumentAttachment::where('user_id', $request->user_id)->first();
        if($user_attachment){
            if ($request->hasFile('filled_and_signed_pdf')) {
                if($user_attachment->filled_and_signed_pdf != NULL) {
                    Storage::disk('public')->delete('users_attached_documents/' . $request->user_id . '/' . $user_attachment->filled_and_signed_pdf);
                }
                $filename = 'filled_and_signed_pdf_'. $date . '_' . $request->user_id . '.pdf';
                $file = $request->file('filled_and_signed_pdf');
                Storage::disk('public')->putFileAs('users_attached_documents/'. $request->user_id .'', $file, $filename);
                $user_attachment->filled_and_signed_pdf = $filename;
            }
            if ($request->hasFile('signed_acknowledgement_pdf')) {
                if($user_attachment->signed_acknowledgement_pdf != NULL) {
                    Storage::disk('public')->delete('users_attached_documents/' . $request->user_id . '/' . $user_attachment->signed_acknowledgement_pdf);
                }
                $filename = 'signed_acknowledgement_pdf_'. $date . '_' . $request->user_id . '.pdf';
                $file = $request->file('signed_acknowledgement_pdf');
                Storage::disk('public')->putFileAs('users_attached_documents/'. $request->user_id .'', $file, $filename);
                $user_attachment->signed_acknowledgement_pdf = $filename;
            }
            $user_attachment->save();
        }
        else{
            $new_user_attachment = new UserDocumentAttachment();
            if ($request->hasFile('filled_and_signed_pdf')) {
                $filename = 'filled_and_signed_pdf_'. $date . '_' . $request->user_id . '.pdf';
                $file = $request->file('filled_and_signed_pdf');
                Storage::disk('public')->putFileAs('users_attached_documents/'. $request->user_id .'', $file, $filename);
                $new_user_attachment->filled_and_signed_pdf = $filename;
            }

            if ($request->hasFile('signed_acknowledgement_pdf')) {
                $filename = 'signed_acknowledgement_pdf_'. $date . '_' . $request->user_id . '.pdf';
                $file = $request->file('signed_acknowledgement_pdf');
                Storage::disk('public')->putFileAs('users_attached_documents/'. $request->user_id .'', $file, $filename);
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

    public function add_contacts($id){
        $shipper = User::find($id);
        $sale_person = SalePersonTag::where('user_id',$id)->where('status', 0)->first();
        $admin = Admin::find($sale_person->admin_id);
        $contacts = ShipperContact::where('shipper_id', $id);
        if($contacts->exists()){
            $contacts = $contacts->get();
        }else{
            $contacts = null;
        }
        return view('admin.accounts.multiple_poc')->with(['sale_person' => $admin, 'contacts' => $contacts, 'shipper' => $shipper]);
    }

    public function add_contacts_store(Request $request){
        ShipperContact::where('shipper_id', $request->shipper_id)->delete();
        if($request->has('poc')){
            foreach($request->poc as $index => $poc){
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

    public function payment_cycle_info(Request $request){
        $user_id = $request->shipper_id;
        if($user_id){
            $user = User::find($user_id);
            if($user){
                $details = ['payment_cycle_id' => $user->payment_cycle_id, 'payment_day' => $user->payment_day];
                return response()->json(['status' => 0, 'details' => $details]);

            }else{
                return response()->json(['status' => 1, 'error' => 'User not found!']);
            }
        }
    }
    public function payment_cycle_submit(Request $request){
        $payment_cycle_id = $request->payment_cycle_select;
        if($payment_cycle_id){
            if($payment_cycle_id == 2 || $payment_cycle_id == 3){
                $payment_day = $request->payment_day;
            }
            if($request->has('shipper_ids')){
                $shipper_ids = explode(',' , $request->shipper_ids);
                if(count($shipper_ids) > 0){
                    foreach ($shipper_ids as $shipper_id){
                        $shipper = User::find($shipper_id);
                        if($shipper){
                            $shipper->payment_cycle_id = $payment_cycle_id;
                            if($payment_cycle_id == 2 || $payment_cycle_id == 3){
                                $shipper->payment_day = $payment_day;
                            }else{
                                $shipper->payment_day = NULL;
                            }
                            $shipper->save();
                        }
                    }
                    return redirect()->back()->with('success', 'Payment Cycle successfully updated!');
                }
            }else{
                $shipper_id = $request->shipper_id;
                $shipper = User::find($shipper_id);
                if($shipper){
                    $shipper->payment_cycle_id = $payment_cycle_id;
                    if($payment_cycle_id == 2 || $payment_cycle_id == 3){
                        $shipper->payment_day = $payment_day;
                    }else{
                        $shipper->payment_day = NULL;
                    }
                    $shipper->save();
                    return redirect()->back()->with('success', 'Payment Cycle successfully updated!');
                }
            }

            return redirect()->back()->with('error', 'Shipper not found!');
        }
        return redirect()->back()->with('error', 'Payment Cycle not selected!');
    }



    public function getInternationalCityForm(){
        $hubs = City::where('hub',1)->where('business_category_id', 2)->where('status',1)->get();
        $zones = Zone::where('business_category_id', 2)->get();
        return view('admin.management.add_international_city_form')->with(['hubs'=>$hubs,'zones' => $zones]);
    }
    public function getEditInternationalCityForm($id){
        $city = City::find($id);
        if($city->hub == 1){
            $cityhub = '';
            $isHub = 1;
        }else{
            $cityhub = City::select(['id','name'])->where('id',$city->hub_id)->get();
            $isHub = 0;

        }
        $delivery_array = CityDelivery::where('city_id',$city->id)->select(['booking_type_id','shipping_mode_id'])->get();
        $delivery = array();
        foreach ($delivery_array as $delivery_details) {
            $delivery[$delivery_details['booking_type_id']][] = $delivery_details['shipping_mode_id'];
        }


        $hubs = City::where('hub',1)->where('business_category_id', 2)->where('status',1)->get();
        $zones = Zone::where('business_category_id', 2)->get();
        return view('admin.management.edit_international_city_form')->with(['hubs'=>$hubs, 'zones' => $zones,'isHub'=>$isHub,'city'=>$city,'delivery'=>$delivery,'cityhub'=>$cityhub]);

    }

    public function updateInternationalCity(Request $request,$id){
        $city_id = City::where('id',$id)->first();
        if($request->postType == 'city'){
            City::where('id',$id)->update([
                'name'=>$request->cityName,
                'city_code'=>$request->city_code,
                'hub'=>0,
                'hub_id'=>$request->hubs,
                'zone_id'=>City::find($request->hubs)->zone_id,
                'pickup'=>0,
                'gc_area'=>0,
                'attempt_tat'=>$request->attempt_tat,
                'location_latitude' => NULL,
                'location_longitude' => NULL,
                'address' => NULL
            ]);
            CityHistory::create([
                'city_id'=> $id,
                'hub' => 0,
                'hub_id'=> $request->hubs,
                'zone_id'=> City::find($request->hubs)->zone_id,
                'pickup'=> 0,
                'status' => $city_id->status,
                'gc_area'=>0,
                'attempt_tat'=>$request->attempt_tat,
                'updated_by' => Auth::id(),
                'location_latitude' => NULL,
                'location_longitude' => NULL,
                'address' => NULL
            ]);

            return redirect()->back()->with('success','City updated successfully');
        }elseif($request->postType == 'hub'){
            $city = City::where('id',$id)->first();
            if($city->zone_id != $request->zone_id)
            {
                $city->hub_cities()->update([
                    'zone_id'=>$request->zone_id,
                ]);
            }
            $city->update([
                'name'=>$request->countryName,
                'city_code'=>$request->city_code,
                'hub'=>1,
                'hub_id'=>$id,
                'zone_id'=>$request->zone_id,
                'pickup'=>0,
                'gc_area'=>0,
                'attempt_tat'=>$request->attempt_tat,
                'location_latitude' => NULL,
                'location_longitude' => NULL,
                'address' => NULL
            ]);
            CityHistory::create([
                'city_id'=> $id,
                'hub'=>1,
                'hub_id'=>$id,
                'zone_id'=>$request->zone_id,
                'pickup'=>0,
                'status' => $city_id->status,
                'gc_area'=>0,
                'attempt_tat'=>$request->attempt_tat,
                'updated_by' => Auth::id(),
                'location_latitude' => NULL,
                'location_longitude' => NULL,
                'address' => NULL
            ]);
            return redirect()->back()->with('success','Hub/city updated successfully');
        }
    }
    //update international city end
    public function addInternationalCityHub(Request $request){
        if($request->postType == 'city'){
            $zone_id = City::find($request->hubs)->zone_id;

            $city = City::create([
                'name'=>$request->cityName,
                'city_code'=>$request->city_code,
                'hub'=>0,
                'hub_id'=>$request->hubs,
                'zone_id'=> $zone_id,
                'pickup'=>0,
                'gc_area'=>0,
                'attempt_tat'=>$request->attempt_tat,
                'status'=>1,
                'location_latitude' => NULL,
                'location_longitude' => NULL,
                'address' => NULL,
                'business_category_id' => 2
            ]);

            CityHistory::create([
                'city_id'=> $city->id,
                'hub'=>0,
                'hub_id'=>$request->hubs,
                'zone_id'=> $zone_id,
                'pickup'=>0,
                'status'=>1,
                'gc_area'=>0,
                'attempt_tat'=>$request->attempt_tat,
                'updated_by' => Auth::id(),
                'location_latitude' => NULL,
                'location_longitude' => NULL,
                'address' => NULL
            ]);

            return redirect()->back()->with('success','City added successfully');
        }elseif($request->postType == 'hub'){
            $city = City::create([
                'name'=>$request->countryName,
                'city_code'=>$request->city_code,
                'hub'=>1,
                'zone_id'=>$request->zone_id,
                'pickup'=>0,
                'gc_area'=>0,
                'attempt_tat'=>$request->attempt_tat,
                'status'=>1,
                'location_latitude' => NULL,
                'location_longitude' => NULL,
                'address' => NULL,
                'business_category_id' => 2
            ]);

            CityHistory::create([
                'city_id'=> $city->id,
                'hub'=>1,
                'zone_id'=>$request->zone_id,
                'pickup'=>0,
                'status'=>1,
                'gc_area'=>0,
                'attempt_tat'=>$request->attempt_tat,
                'updated_by' => Auth::id(),
                'location_latitude' => NULL,
                'location_longitude' => NULL,
                'address' => NULL
            ]);
            City::where('id',$city->id)->update(['hub_id'=>$city->id]);
            return redirect()->back()->with('success','Hub city added successfully');
        }
    }

    public function assign_locations_submit(Request $request){
        $route_id = $request->route_id;
        $pickup_address_ids = explode(',',$request->pickup_address_id);
//        RouteLocations::where('route_id',$route_id)->delete();

        if($route_id){
            RouteLocations::whereIn('pickup_address_id',$pickup_address_ids)->delete();
            foreach($pickup_address_ids as $pickup_address){
                $location = new RouteLocations();
                $location->route_id = $route_id;
                $location->pickup_address_id = $pickup_address;
                $location->save();
            }
        }
        return redirect()->back()->with(['success'=>"Location has been Assigned successfully!"]);
    }

    public function user_address(Request $request){

        $user_id = $request->user_id;
        if($user_id != null){
            $addresses = UserShippingInfo::select('id','pickup_address')->where('user_id',$user_id)->where('user_shipping_infos.hidden',0)->get();
            if($addresses)
            {
                return response()->json(['status'=> 1,'addresses' => $addresses]);
            }
            else{
                return response()->json(['status'=> 0,'error' => 'Address Not Found']);
            }
        }
    }


     public function assign_locations_view(Request $request){
        $route_id = $request->route_id;
        $data = array();
        if($route_id){
            $locations = RouteLocations::join('user_shipping_infos as usi','usi.id','=','route_locations.pickup_address_id')
                ->join('users as u','u.id','=','usi.user_id')->where('route_locations.route_id',$route_id)->select('u.name as shipper','usi.pickup_address as address')->get();
            foreach($locations as $location){
                $data[] = $location->shipper . ' - ' . $location->address;
            }
            return response()->json(['locations' => $data]);
        }
     }

     public function set_as_pickup_route(Request $request){
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

     public function kam_poc_ref_tag(Request $request){
        $poc = $request->poc;
        $kam = $request->kam;
        $ref = $request->ref;
        $shipper_ids = $request->shipper_ids;
         if($kam == null && $poc == null && $ref == null){
             return response()->json(['status'=>0,'error'=>"One field is mandatory!"]);
         }
         else{
         if($shipper_ids) {
                foreach ($shipper_ids as $shipper_id) {
                    $sale_tier =  SaleTierTag::where('user_id',$shipper_id);
                    if($sale_tier->exists()){
                        $sale_tier = $sale_tier->first();

                        $sale_tier_history = new SaleTierTagHistory();
                        $sale_tier_history->sale_tier_tag_id = $sale_tier->id;
                        $sale_tier_history->user_id = $sale_tier->user_id;
                        $sale_tier_history->poc = $sale_tier->poc;
                        $sale_tier_history->kam = $sale_tier->kam;
                        $sale_tier_history->ref = $sale_tier->ref;
                        $sale_tier_history->save();

                        $sale_tier->user_id = $shipper_id;
                        $sale_tier->poc = $poc;
                        $sale_tier->kam = $kam;
                        $sale_tier->ref = $ref;
                        $sale_tier->save();
                       // return response()->json(['status'=>1,'success'=>"Updated!"]);
                    }
                    else{
                        $sale_tier = new SaleTierTag();
                        $sale_tier->user_id = $shipper_id;
                        $sale_tier->poc = $poc;
                        $sale_tier->kam = $kam;
                        $sale_tier->ref = $ref;
                        $sale_tier->save();
                    }
                }
                return response()->json(['status'=>1,'success'=>"Updated!"]);
            }
            else{
                return ['status' => 0 ,'error'=>"Select One Shipper!"];
            }
        }
     }

     public function kam_poc_ref_tag_info(Request $request){
        $shipper_id = $request->shipper_id;
         if($shipper_id) {
             $info = array();
             $sale_tier = SaleTierTag::where('user_id', $shipper_id);
             if($sale_tier->exists()){
                 $flag = false;
                 $sale_tier = $sale_tier->first();
                 if($sale_tier->poc != null){
                     $info['poc'] = $sale_tier->poc_admin->name;
                     $flag = true;
                 }
                 else{
                     $info['poc'] = '-';
                 }
                 if($sale_tier->kam != null){
                     $info['kam'] = $sale_tier->kam_admin->name;
                     $flag = true;
                 }
                 else{
                     $info['kam'] = '-';
                 }
                 if($sale_tier->ref != null){
                     $info['ref'] = $sale_tier->ref_admin->name;
                     $flag = true;
                 }
                 else{
                     $info['ref'] = '-';
                 }
                 if($flag){
                     return response()->json(['status'=>1,'info'=>$info]);
                 }
                 else{
                     return response()->json(['status'=>0,'error'=> 'No Sales tier found!']);
                 }
             }
             else{
                 return response()->json(['status'=>0,'error'=> 'No Sales tier found!']);
             }
            }
            else{
                return ['status' => 0 ,'error'=>"Shipper not found with sales tier tagging!"];
            }
     }

     public function kam_poc_ref_tag_remove(Request $request){
        $shipper_id = $request->shipper_id;
         if($shipper_id) {
             $sale_tier = SaleTierTag::where('user_id', $shipper_id);
             if($sale_tier->exists()){
                 $sale_tier = $sale_tier->first();
                 if($request->has('poc')){
                     $sale_tier->poc = null;
                 }
                 if($request->has('kam')){
                     $sale_tier->kam = null;
                 }
                 if($request->has('ref')){
                     $sale_tier->ref = null;
                 }
                 $sale_tier->save();
                 return redirect()->back()->with('success', 'Sales tier tag removed successfully!');
             }
             else{
                 return redirect()->back()->with('error', 'No Sales tier found!');
             }
            }
            else{
                return redirect()->back()->with('error', 'Shipper not found with sales tier tagging!');
            }
     }

     public function rate_history_date(Request $request){
        $user_id = $request->user_id;
        $details = array();
        if($user_id){
            $user = User::find($user_id);
            if($user->account_type_id == 1){
                $old_reimbursement_account = HistoryRateStatus::where('user_id',$user_id);
                if($old_reimbursement_account->exists()){
                    $old_reimbursement_account_dates = $old_reimbursement_account->select('created_at')->groupBy('created_at')->get();
                    foreach($old_reimbursement_account_dates as $date){
                        $date = Carbon::parse($date->created_at)->toDateString();
                        if(!in_array($date, $details)){
                            $details[] = $date;
                        }
                    }
                    return response()->json(['status' => 1,'account_type' => 1,'details' => $details, 'user_id' => $user_id]);
                }
                else{
                    return response()->json(['status' => 0,'error'=>'No Data Found']);
                }
            }
            else{
                $old_corporate_account = HistoryCorporateRateStatus::where('user_id',$user_id);
                if ($old_corporate_account->exists()){
                    $old_corporate_account_dates = $old_corporate_account->select('created_at')->groupBy('created_at')->get();
                    foreach($old_corporate_account_dates as $date){
                        $date = Carbon::parse($date->created_at)->toDateString();
                        if(!in_array($date, $details)){
                            $details[] = $date;
                        }
                    }
                    return response()->json(['status' => 1,'success','account_type' => 2,'details' => $details, 'user_id' => $user_id]);
                }
                else{
                    return response()->json(['status' => 0, 'error' => 'No Data Found']);
                }
            }
        }
        else{
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
////                dd($shipping_mode);
//                $name = $shipping_mode;
//                dd($name);
//                $data[] = $name;
//            }
//            return response()->json(['status' => 1, 'shipping_mode' => $data]);
//        }
//        else {
//            return response()->json(['status' => 0, 'No Shipping mode found!']);
//        }
//     }
    public function add_territory(Request $request){
        $territory = $request->territory;
        $user_ids = $request->user_ids;
        if($user_ids){
            $user_ids = explode(',', $user_ids);
            foreach($user_ids as $id){
                $user = User::find($id);
                if($user->territory_id)
                return redirect()->back()->with('error', 'Territory not added as '.$user->name.' is tagged previously');
            }
            foreach($user_ids as $id){
                $user = User::find($id);
                $user->territory_id = $territory;
                $user->save();

                $history = new TerritoryTagHistory();
                $history->user_id = $id;
                $history->admin_id = Auth::id();
                $history->save();
            }
            return redirect()->back()->with('success', 'Territory is added.');
            }
        else{
            return redirect()->back()->with('error', 'Territory not added.');
        }
    }

    public function add_segments(Request $request){
        $segment_id = $request->bulk_segment;
        $sub_segment_id = $request->bulk_sub_segment;
        $user_ids = $request->user_ids;
        if($user_ids){
            $user_ids = explode(',', $user_ids);
            foreach($user_ids as $id){
                $user = User::find($id);
                $user->sub_segment_id = $sub_segment_id;
                $user->segment_id = $segment_id;
                $user->save();
            }
            return redirect()->back()->with('success', 'Segments is added.');
            }
        else{
            return redirect()->back()->with('error', 'Segments not added.');
        }
    }

    


    public function todayActiveAccountsList(){
        // dd(Carbon::parse('-24 hours'));
        // dd(Carbon::today('-12 hours'));
        // dd(Carbon::today('+12 hours'));
        ActivityTrailController::createActivityTrailLog(Auth::id(),279);
       return view('admin.accounts.today_active_accounts_list');

    }

    public function todayActiveAccountListAjax(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),280);
        }
        $users = User::join('cities', 'users.city_id', '=', 'cities.id')
            ->leftjoin('account_types as at','at.id','=','users.account_type_id')
            ->leftjoin('sale_person_tags as spt', function ($join) {
                $join->on('spt.user_id', '=', 'users.id')
                    ->leftjoin('admins as ad','ad.id','=','spt.admin_id')
                    ->where('spt.status','=',0);
            })
            ->leftjoin('sale_tier_tags as st','st.user_id','=','users.id')
            ->leftjoin('admins as poc','poc.id','=','st.poc')
			->select(['users.phone as phone','users.email as email','users.id','ad.name as admin_tag_id', 'users.name','users.poc','users.account_type_id','at.name as account_type'])->whereIn('users.status',[3,4])->where('blacklist',0)
            ->where('users.activated_at', '>', Carbon::parse('-24 hours'));
        if (session('role_id') != 1) {
            $users = $users->whereIn('cities.hub_id', session('hubs'));
        }
        if(session('department_id') == 7){
            if(in_array(session('id'), session('sale_users_bypass'))){
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


    static public function compare_data($array_1,$array_2){
        if(count($array_2) == count($array_1)){
            $diff = array_diff($array_1,$array_2);
            //dd(count($diff));
            if(count($diff) > 0){
                return 1;
            }
            else{
                return 0;
            }
        }
        else{
            return 1;
        }
    }

    public function restrict_order_id_info(Request $request){
        $user = User::find($request->user_id);
        return response()->json(['status'=>$user->restrict_order_id]);
    }

    public function restrict_order_id_submit(Request $request){
        $user = User::find($request->user_id);
        if ($request->has('restrict_order_id_checkbox')){
            $user->restrict_order_id = 1;
            $user->save();
            return redirect()->back()->with('success', 'Order ID restricted successfully!');
        }
        else{
            $user->restrict_order_id = 0;
            $user->save();
            return redirect()->back()->with('success', 'Order ID restriction removed successfully!');
        }
    }
    public function admin_profile(Request $request){
        $user = Employee::leftjoin('employee_designations as d', 'd.id', '=', 'employees.designation_id')
            ->leftjoin('admin_departments as ad', 'd.department_id', '=', 'ad.id')
            ->leftjoin('employee_blood_groups as bg', 'bg.id', '=', 'employees.blood_group')
            ->select('employees.trax_id as trax_id', 'employees.name as name', 'employees.official_email as email', 'employees.phone_number as phone', 'd.name as designation', 'ad.name as department_name', 'bg.name as blood_group', 'employees.emergency_contact as emergency_contact_no', 'employees.emergency_contact_person as emergency_contact_person', 'bg.id as blood_group_id','employees.personal_email as personal_email', 'employees.official_phone_number as official_phone_number')
            ->where('employees.trax_id', Auth::user()->trax_id);
        if($user->exists()){
            $user = $user->first();
            $email = $user->email;
            if($user->personal_email){
                $email .= " / ".$user->personal_email;
            }
            $phone = $user->phone;
            if($user->official_phone_number){
                $phone .= " / ".$user->official_phone_number;
            }
            return response()->json(['full_name' => $user->name,'department' => $user->department_name,'designation' => $user->designation,'employee_id' => $user->trax_id,'email' => $email,'contact' => $phone, 'blood_group' => $user->blood_group, 'emergency_contact_no'=> $user->emergency_contact_no, 'emergency_contact_person' => $user->emergency_contact_person]);
        }
        else{
            return response()->json(['error' => 'User not found!']);
        }

   }

    public function edit_profile(Request $request){
        $employee = Employee::where('trax_id', Auth::user()->trax_id)->where('trax_id', '!=', null);
        if($employee->exists()){
            $employee = $employee->first();
            $blood_groups = EmployeeBloodGroup::all();
            return view('admin.profile.edit_profile_form')->with(['blood_groups' => $blood_groups, 'employee' => $employee]);
        }
        else{
            return response()->json(['status' => 1, 'error' => 'User not found!']);
        }

   }

    public function edit_profile_submit(Request $request){
        $employee = Employee::where('trax_id', Auth::user()->trax_id)->where('trax_id', '!=', null);
        if($employee->exists()){
            $employee = $employee->first();
            $employee->blood_group = $request->blood_group;
            $employee->emergency_contact_person = $request->emergency_contact_name;
            $employee->emergency_contact = $request->emergency_contact_no;
            $employee->save();
            return redirect()->back()->with('success', 'Profile Successfully Updated');
        }
        else{
            return redirect()->back()->with('error', 'User not found!');
        }

   }

   public function add_retag_territory(Request $request){
    $territory = $request->territory;
    $user_ids = $request->user_ids;
        if($user_ids){
            $user_ids = explode(',', $user_ids);
            foreach($user_ids as $id){
                $user = User::find($id);
                if(!$user->territory_id)
                return redirect()->back()->with('error', 'Retagging not done as '.$user->name.' is not tagged previously');
            }
            foreach($user_ids as $id){
                $user = User::find($id);
                $user->territory_id = $territory;
                $user->save();

                $history = new TerritoryTagHistory();
                $history->user_id = $id;
                $history->admin_id = Auth::id();
                $history->save();
            }
            return redirect()->back()->with('success', 'Territory is added.');
            }
        else{
            return redirect()->back()->with('error', 'Territory not added.');
        }
    }
    public function osa_list(Request $request){
        $city = City::find($request->city_id);

        $osa_list = CityOsaRate::where('city_id',$city->id)->get();
        return response()->json(['status'=>1,'osa_list'=>$osa_list]);

    }

	public function packaging_invoice_log(Request $request){
	     $logs = CorporateUserPackagingInvoiceLog::join('admins as a','a.id','=','corporate_user_packaging_invoice_logs.admin_id')
	         ->select('a.name as admin','corporate_user_packaging_invoice_logs.created_at as time','corporate_user_packaging_invoice_logs.status as status')
	         ->where('corporate_user_packaging_invoice_logs.user_id',$request->user_id);
	     if($logs->exists()){
	         $logs = $logs->get();
	         return response()->json(['status' => 0, 'details' => $logs]);
	     }
	     else{
	         return response()->json(['status' => 1, 'error' => 'No Log found!']);
	     }

   }

	    public function auto_cancelation_days(Request $request)
    {
        $user = User::find($request->user_id);
        if(!$user)
        {
            return back()->with(['error'=>'Invalid Shipper']);
        }

        $user->auto_shipment_cancellation_days = $request->cancelation_days;
        $user->update();

        return back()->with(['success'=>'Auto Cancelation Days Updated Successfully']);

    }}

