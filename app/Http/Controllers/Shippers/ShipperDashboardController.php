<?php

namespace App\Http\Controllers\Shippers;

use App\DailyVisit;
use App\Http\Controllers\Admins\V2Pickup\V2AdminPickupsController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FingaIntegrationController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Controllers\ShipperAgreementController;
use App\Http\Models\AccountType;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\CorporateDefaultDiscountWeightCharge;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\Lead\Lead;
use App\Http\Models\Admin\Lead\LeadZone;
use App\Http\Models\Admin\Retail\OtherParcelReceiving;
use App\Http\Models\Admin\Retail\OtherParcelReceivingShipment;
use App\Http\Models\Admin\Retail\OtherRetailShipment;
use App\Http\Models\Admin\SalePersonTag;
use App\Http\Models\Admin\ShipperVerificationPinCode as AdminShipperVerificationPinCode;
use App\Http\Models\Admin\StandardBookingTypeCharge;
use App\Http\Models\Admin\StandardCashHandlingCharge;
use App\Http\Models\Admin\StandardFuelSurcharge;
use App\Http\Models\Admin\StandardInsuranceCharge;
use App\Http\Models\Admin\StandardReturnCharge;
use App\Http\Models\Admin\StandardWeightCharge;
use App\Http\Models\Admin\UserShippingInfoStoreAddress;
use App\Http\Models\AverageShipmentCycle;
use App\Http\Models\BanksList;
use App\Http\Models\BookingType;
use App\Http\Models\BookingTypeCharges;
use App\Http\Models\BusinessCategory;
use App\Http\Models\CashHandlingCharge;
use App\Http\Models\City;
use App\Http\Models\Commission\SalesCommission;
use App\Http\Models\Commission\SalesTier;
use App\Http\Models\Consolidation;
use App\Http\Models\ConsolidationShipments;
use App\Http\Models\CorporateBookingTypeCharge;
use App\Http\Models\CorporateCashHandlingCharge;
use App\Http\Models\CorporateDefaultCashHandlingCharge;
use App\Http\Models\CorporateDefaultDiscountCharge;
use App\Http\Models\CorporateDefaultFuelSurcharge;
use App\Http\Models\CorporateDefaultInsuranceCharge;
use App\Http\Models\CorporateDefaultRateStatus;
use App\Http\Models\CorporateDefaultReturnCharge;
use App\Http\Models\CorporateDefaultWeightCharge;
use App\Http\Models\CorporateDiscountCharge;
use App\Http\Models\CorporateFuelSurcharge;
use App\Http\Models\CorporateInsuranceCharge;
use App\Http\Models\CorporateMinChargeableWeight;
use App\Http\Models\CorporateRateStatus;
use App\Http\Models\CorporateReturnCharge;
use App\Http\Models\CorporateReturnChargeZoneWise;
use App\Http\Models\CorporateWeightCharge;
use App\Http\Models\CorporateWeightChargeZoneWise;
use App\Http\Models\CRM\CrmRequestCaseNature;
use App\Http\Models\CRM\CrmRequestCaseNatureType;
use App\Http\Models\DiscountCharge;
use App\Http\Models\DiscountWeightCharge;
use App\Http\Models\DisputeType;
use App\Http\Models\FuelSurcharge;
use App\Http\Models\InsuranceCharge;
use App\Http\Models\InvoicingCycle;
use App\Http\Models\Notification;
use App\Http\Models\PackagingCharge;
use App\Http\Models\PackagingMaterialRequest;
use App\Http\Models\PackagingMaterialTypes;
use App\Http\Models\PackagingMaterialTypeSizes;
use App\Http\Models\PaymentCycle;
use App\Http\Models\PaymentMode;
use App\Http\Models\Product;
use App\Http\Models\RateRemark;
use App\Http\Models\Rates\Corporate\CorporateDefaultRateDestinationHub;
use App\Http\Models\Rates\Corporate\CorporateDefaultRateOriginHub;
use App\Http\Models\Rates\Corporate\CorporateRateDestinationHub;
use App\Http\Models\Rates\Corporate\CorporateRateOriginHub;
use App\Http\Models\Rates\MinimumChargeableWeightSetting;
use App\Http\Models\Rates\RateDestinationHub;
use App\Http\Models\Rates\RateOriginHub;
use App\Http\Models\RateStatus;
use App\Http\Models\Reference;
use App\Http\Models\ReturnCharge;
use App\Http\Models\Rider;
use App\Http\Models\Route;
use App\Http\Models\Segment;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentPaymentStatus;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserBankInfo;
use App\Http\Models\Shipper\UserOtpVerification;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\ShipperContact;
use App\Http\Models\ShipperNotificationEmail;
use App\Http\Models\Sister_account\MergedSisterAccount;
use App\Http\Models\Sister_account\MergedSisterAccountMapping;
use App\Http\Models\Sister_account\Substitute_user\SubstituteUserMergeSisterAccountMapping;
use App\Http\Models\SubCategorySegment;
use App\Http\Models\UserDefaultBankDuration;
use App\Http\Models\UserDocumentAttachment;
use App\Http\Models\WalletUser;
use App\Http\Models\WeightCharge;
use App\Http\Models\WMS\WmsCurrentStock;
use App\Http\Models\WMS\WmsLabellingCharge;
use App\Http\Models\WMS\WmsPackingCharge;
use App\Http\Models\WMS\WmsPendingPicking;
use App\Http\Models\WMS\WmsPerProductCharge;
use App\Http\Models\WMS\WmsPerSquareFootCharge;
use App\Http\Models\WMS\WmsShipmentProduct;
use App\Http\Models\WMS\WmsStorageType;
use App\Http\Models\WMS\WmsStorageTypeCharge;
use App\Http\Models\WMS\WmsUserInformation;
use App\LeadProgressSetting;
use App\RouteLocations;
use Auth;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Yajra\Datatables\Datatables;
use App\Jobs\WalletSignUpLPendingRecordLogs;
use Validator;
//use Illuminate\Support\Facades\Auth;
use App\Models\ParentProduct;

class ShipperDashboardController extends Controller
{
    public function __construct() {
      $this->middleware('auth:web,substitute_users');
      $this->middleware('Permission')->except('wordpress_access_denied', 'wordpressAddressView','wordpressBankView');
    }

    public function access_denied() {
        return view('client.access_denied');
    }

    public function wordpress_access_denied() {
        return view('client.access_denied');
    }

    public function welcome_index(){
        $shipper_id = session('user_id');
        $sales_person_data = array();
        if($shipper_id){

            $shippers = GlobalSettings::where('type','mms_setting')->select('text')->first();
            $special_shippers = explode(',', $shippers->text);

            if(session('special_dashboard_user') && in_array(session('user_id'),$special_shippers))
            {
                $stats = array();
                $today = Carbon::now()->subDays(2)->endOfDay();
                $thirtyDays = Carbon::now()->subDays(29)->startOfDay();
                $restriction = false;
                if (session('user_type') == 2) {
                    if (session('restriction') == 1) {
                        $restriction = true;
                    }
                }
                $stats['total'] = DB::connection('reports')->table('shipments')->whereBetween('shipments.created_at', [$thirtyDays, $today])->where('user_id', session('user_id'));
                $stats['booked'] = DB::connection('reports')->table('shipments')->where('shipper_status_id', 1)->whereBetween('shipments.created_at', [$thirtyDays, $today])->where('user_id', session('user_id'));
                $stats['canceled'] = DB::connection('reports')->table('shipments')->where('shipper_status_id', 17)->whereBetween('shipments.created_at', [$thirtyDays, $today])->where('user_id', session('user_id'));
                $stats['received'] = DB::connection('reports')->table('shipments')->whereIn('shipper_status_id', [2, 3, 4])->whereBetween('shipments.created_at', [$thirtyDays, $today])->where('user_id', session('user_id'));
                $stats['delivered'] = DB::connection('reports')->table('shipments')->whereIn('shipper_status_id', [14, 16, 30, 36, 37, 39, 40, 41, 47])->whereBetween('shipments.created_at', [$thirtyDays, $today])->where('user_id', session('user_id'));
                $stats['return'] = DB::connection('reports')->table('shipments')->whereIn('shipper_status_id', [20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 31, 32, 33, 34, 35, 38, 42, 43, 44, 45, 46, 50])->whereBetween('shipments.created_at', [$thirtyDays, $today])->where('user_id', session('user_id'));
                $stats['in_process'] = DB::connection('reports')->table('shipments')->whereIn('shipper_status_id', [5, 6, 7, 8, 9, 10, 11, 12, 13, 15, 18, 19, 49, 52])->whereBetween('shipments.created_at', [$thirtyDays, $today])->where('user_id', session('user_id'));
                if ($restriction == true) {
                    $stats['total'] = $stats['total']->join('substitute_user_shipments as sus', function ($join) {
                        $join->on('sus.shipment_id', '=', 'shipments.id')
                            ->where('sus.substitute_user_id', '=', Auth::id());
                    });
                    $stats['booked'] = $stats['booked']->join('substitute_user_shipments as sus', function ($join) {
                        $join->on('sus.shipment_id', '=', 'shipments.id')
                            ->where('sus.substitute_user_id', '=', Auth::id());
                    });
                    $stats['canceled'] = $stats['canceled']->join('substitute_user_shipments as sus', function ($join) {
                        $join->on('sus.shipment_id', '=', 'shipments.id')
                            ->where('sus.substitute_user_id', '=', Auth::id());
                    });
                    $stats['received'] = $stats['received']->join('substitute_user_shipments as sus', function ($join) {
                        $join->on('sus.shipment_id', '=', 'shipments.id')
                            ->where('sus.substitute_user_id', '=', Auth::id());
                    });
                    $stats['delivered'] = $stats['delivered']->join('substitute_user_shipments as sus', function ($join) {
                        $join->on('sus.shipment_id', '=', 'shipments.id')
                            ->where('sus.substitute_user_id', '=', Auth::id());
                    });
                    $stats['return'] = $stats['return']->join('substitute_user_shipments as sus', function ($join) {
                        $join->on('sus.shipment_id', '=', 'shipments.id')
                            ->where('sus.substitute_user_id', '=', Auth::id());
                    });
                    $stats['in_process'] = $stats['in_process']->join('substitute_user_shipments as sus', function ($join) {
                        $join->on('sus.shipment_id', '=', 'shipments.id')
                            ->where('sus.substitute_user_id', '=', Auth::id());
                    });
                }
                $stats['total'] = number_format($stats['total']->count());
                $stats['booked'] = number_format($stats['booked']->count());
                $stats['canceled'] = number_format($stats['canceled']->count());
                $stats['received'] = number_format($stats['received']->count());
                $stats['delivered'] = number_format($stats['delivered']->count());
                $stats['return'] = number_format($stats['return']->count());
                $stats['in_process'] = number_format($stats['in_process']->count());
                $cities = DB::connection('reports')->table('cities')->select(['id', 'name'])->get();
                $sister_users = DB::connection('reports')->table('substitute_user_merge_sister_account_mappings')->leftjoin('users as u', 'u.id', '=', 'substitute_user_merge_sister_account_mappings.sister_user_id')->where('substitute_user_merge_sister_account_mappings.head_user_id', session('user_id'))->where('substitute_user_merge_sister_account_mappings.substitute_user_id',session('substitute_user_id'))->select('u.id', 'u.name')->get();
                $user = DB::connection('reports')->table('users')->select('id', 'name')->where('id', session('user_id'))->first();
                return view('client.special_dashboard')->with(['stats' => $stats, 'cities' => $cities, 'today' => $today, 'thirtyday' => $thirtyDays, 'user' => $user, 'sister_users' => $sister_users]);
            }
            else{

                $sales_person_tag = SalePersonTag::where('user_id', $shipper_id)->where('status', 0)->latest()->first();
                if($sales_person_tag){
                    $sales_person_tag = Admin::find($sales_person_tag->admin_id);
                    $sales_person_data['name'] = $sales_person_tag->name;
                    if($sales_person_tag->official_phone_number != '')
                    {
                        $sales_person_data['phone'] = $sales_person_tag->official_phone_number;
                    }
                    else{
                        $sales_person_data['phone'] = $sales_person_tag->phone_number;
                    }
                    
                    $sales_person_data['email'] = $sales_person_tag->email;
                }
                $details = SalesCommission::join('sales_commission_users as scu','sales_commissions.id','=','scu.sales_commission_id')
                            ->join('admins as a','a.id','=','scu.user_id')
                            ->where('sales_commissions.shipper_id',session('user_id'))
                            ->wherein('scu.tier_id',[2,3])
                            ->select('a.name as name','a.email as email','a.phone_number as phone','scu.tier_id as tier_id')->orderBy('scu.created_at', 'desc')->get();

                $poc = array();
                $kam = array();
                foreach($details as $detail){
                    if($detail->tier_id == 2){
                        $poc[] = $detail;
                    }
                    else{
                        $kam[] = $detail;
                    }
                }
                $route_ids = array();
                $routes = array();
                $riders = array();
                $pickup_address_ids = UserShippingInfo::where('user_id', session('user_id'))->where('status', 1)->pluck('id')->toArray();
                if((count($pickup_address_ids) > 0) && (count($pickup_address_ids) < 100)){
                    $route_ids = RouteLocations::whereIn('pickup_address_id', $pickup_address_ids)->pluck('route_id')->toArray();
                    $routes = Route::whereIn('id', $route_ids)->where('status', 1)->pluck('id')->toArray();
                    $riders = Rider::join('cities as oc','riders.city_id','=','oc.id')
                        ->wherein('riders.route_id',$routes)
                        ->select('riders.phone as phone', 'riders.name as name','oc.name as city')->orderBy('riders.created_at', 'desc')->get();
                }

                $percentage = null; 
                $color = null;
                $short_description = "";
                $description = "";

                $user = User::find($shipper_id);
                
                $weight_charges = WeightCharge::where('user_id' , $shipper_id);
                if(($user->on_board_status < 1 && $user->created_at > '2024-06-13 00:00:00')){
                    $lead_progress_setting = LeadProgressSetting::find(1);
                    $percentage = $lead_progress_setting->percent;
                    $color = $lead_progress_setting->color;

                    $description = "Your account is $percentage% completed";
                    $short_description = 'Signed Up';
                    
                }else if(($weight_charges->exists() || $user->request_custom_quotation == 1) && !isset($user->rates_added_by)){
                    $lead_progress_setting = LeadProgressSetting::find(2);
                    $percentage = $lead_progress_setting->percent;
                    $color = $lead_progress_setting->color;

                    $description = "Your account is $percentage% completed";
                    $short_description = 'Custom Rates Requested';


                }else if (isset($user->rates_added_by) && $user->documents_status != 2){
                    $lead_progress_setting = LeadProgressSetting::find(3);
                    $percentage = $lead_progress_setting->percent;
                    $color = $lead_progress_setting->color;

                    $description = "Your account is $percentage% completed";
                    $short_description = 'Registration Confirmed';


                }else if ($user->documents_status == 2 && $user->status != 3){
                    $lead_progress_setting = LeadProgressSetting::find(4);
                    $percentage = $lead_progress_setting->percent;
                    $color = $lead_progress_setting->color;
                    
                    $description = "Your account is $percentage% completed";
                    $short_description = 'Documents Verified';


                }else if ($user->status == 3){
                    $lead_progress_setting = LeadProgressSetting::find(5);
                    $percentage = $lead_progress_setting->percent;
                    $color = $lead_progress_setting->color;

                    $description = "Your account is activated";
                    $short_description = 'Account Activated';

                }

                /*$shipper_payment = ShipperPayment::where('user_id', $shipper_id);
                if($shipper_payment->exists()){
                    $shipper_payment = $shipper_payment->first();
                }
                else{
                    $shipper_payment = null;
                }*/
                $shipper_payment = null;

                return view('client.welcome')->with(['sales_person_data'=>$sales_person_data ,'poc' => $poc,'kam' => $kam, 'pickup_riders' => $riders, 'shipper_payments' => $shipper_payment, 'percentage' => $percentage, 'user' => $user, 'color' => $color , 'description' => $description, 'short_description' => $short_description]);
            }
 
        }
    }

    public function welcome_list(Request $request)
    {   
        $shipments = DB::connection('reports')->table('shipments')
            ->leftJoin('users as u', 'u.id', '=', 'shipments.user_id')
            ->leftJoin('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->leftJoin('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->leftJoin('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->leftJoin('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->leftjoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=', 'sps.id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2 and shipments_journey.verification = 1)'));
            })
            ->leftJoin('shipments_journey as cj', function ($join) {
                $join->on('cj.shipment_id', '=', 'shipments.id')
                    ->where('cj.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.verification = 1)'));
            })
            ->leftJoin('shipment_status as ss', 'ss.id', '=', 'cj.shipper_status_id')
            ->leftjoin('shipment_items as si', function ($join) {
                $join->on('si.shipment_id', '=', 'shipments.id')
                    ->where('si.type', '=', 0);
            })
            ->leftjoin('products as p', 'p.id', '=', 'si.product_type_id')
            ->leftJoin('shipments_journey as sjrr', function ($join) {
                $join->on('sjrr.shipment_id', '=', 'shipments.id')
                    ->whereIn('shipments.shipper_status_id', [20, 21, 22, 23, 24, 25, 44, 47, 48, 57, 60])
                    ->where('sjrr.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id IN (12, 20) and shipments_journey.verification = 1 and shipments_journey.status_reason_id is not null)'));
            })
            ->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'sjrr.status_reason_id')
            ->select(['u.name as user_name', 'shipments.id as shipment_id', 'shipments.order_id', 'shipments.tracking_number', 'shipments.amount as collection_amount', 'shipments.actual_weight', 'shipments.weight_charges', 'shipments.cash_handling_charges', 'ss.name as current_status', 'sps.name as payment_status', 'bt.booking_type as service_type', 'p.product_name', 'si.description', 'sj.created_at as arrival_date', 'oc.name as origin', 'dc.name as destination', 'shipments.consignee_name as consignee_name', 'shipments.consignee_phone_number_1 as consignee_phone', 'ssr.name as return_reason', 'shipments.created_at as booking_date']);

        if (session('user_type') == 2) {
            if (session('restriction') == 1) {
                $shipments = $shipments->join('substitute_user_shipments as sus', function ($join) {
                    $join->on('sus.shipment_id', '=', 'shipments.id')
                        ->where('sus.substitute_user_id', '=', Auth::id());
                });
            }
        }
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');


            if ($from != null && $to != null) {

                $from_id = DB::connection('reports')->table('shipments')
                    ->where('created_at', '>=', $from)
                    ->where('created_at', '<',  $to)
                    ->orderBy('created_at', 'asc')->orderBy('id', 'asc')
                    ->limit(1)->value('id');

                $to_id = DB::connection('reports')->table('shipments')
                    ->where('created_at', '>=', $from)
                    ->where('created_at', '<',  $to)
                    ->orderBy('created_at', 'desc')->orderBy('id', 'desc')
                    ->limit(1)->value('id');
            }

            if ($from != null && $to != null) {

                if ($from_id != null && $to_id != null) {
                    $shipments->where('shipments.id', '>=', $from_id)
                        ->where('shipments.id', '<=', $to_id);
                }
            }else{
               $shipments = $shipments->whereBetween('shipments.created_at', [$from, $to]);
            }
        }

        if ($user = $request->get('search_user')) {
            $shipments->whereIn('shipments.user_id', $user);
        } else {
            $shipments->where('shipments.user_id', session('user_id'));
        }
        if ($origin = $request->get('search_origin')) {
            $shipments->where('oc.id', '=', $origin);
        }
        if ($destination = $request->get('search_destination')) {
            $shipments->where('dc.id', '=', $destination);
        }
        if ($card = $request->get('cards_filter')) {
            switch ($card) {
                case 'total':
                    $shipments->whereIn('shipments.shipper_status_id', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 15, 18, 19, 49, 52, 14, 16, 30, 36, 37, 39, 40, 41, 47, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 31, 32, 33, 34, 35, 38, 42, 43, 44, 45, 46, 50, 17]);
                    break;
                case 'booked':
                    $shipments->where('shipments.shipper_status_id', 1);
                    break;
                case 'received':
                    $shipments->whereIn('shipments.shipper_status_id', [2, 3, 4]);
                    break;
                case 'delivered':
                    $shipments->whereIn('shipments.shipper_status_id', [14, 16, 30, 36, 37, 39, 40, 41, 47]);
                    break;
                case 'returned':
                    $shipments->whereIn('shipments.shipper_status_id', [20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 31, 32, 33, 34, 35, 38, 42, 43, 44, 45, 46, 50]);
                    break;
                case 'in_process':
                    $shipments->whereIn('shipments.shipper_status_id', [5, 6, 7, 8, 9, 10, 11, 12, 13, 15, 18, 19, 49, 52]);
                    break;
                case 'cancelled':
                    $shipments->where('shipments.shipper_status_id', 17);
                    break;
            }

        }

        $datatable = Datatables::of($shipments)
            ->addColumn('tracking_number_link', function ($shipments) {
                $route = route('cod.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('collection_amount', function ($shipments) {
                return number_format($shipments->collection_amount);
            });

        return $datatable->rawColumns(['tracking_number_link'])->make(true);

    }

    public function welcome_data(Request $request)
    {
        $stats = array();
        $from = $request->from_date;
        $to = $request->to_date;
        $origin = $request->origin;
        $user = [session('user_id')];
        if($request->has('user')){
            if($request->user != null && $request->user != ''){
                $user = $request->user;
            }
        }
        $destination = $request->destination;
        if ($from == null || $to == null) {
            $today = Carbon::now()->endOfDay();
            $thirtyDays = Carbon::now()->subDays(29)->startOfDay();
        } else {
            $thirtyDays = Carbon::parse($from)->toDateTimeString();
            $today = Carbon::parse($to)->toDateTimeString();
        }
        $restriction = false;
        if (session('user_type') == 2) {
            if (session('restriction') == 1) {
                $restriction = true;
            }
        }
        $stats['total'] = DB::connection('reports')->table('shipments')->whereBetween('shipments.created_at', [$thirtyDays, $today])->whereIn('user_id', $user);

        $stats['booked'] = DB::connection('reports')->table('shipments')->where('shipper_status_id', 1)->whereBetween('shipments.created_at', [$thirtyDays, $today])->whereIn('user_id', $user);
        $stats['canceled'] = DB::connection('reports')->table('shipments')->where('shipper_status_id', 17)->whereBetween('shipments.created_at', [$thirtyDays, $today])->whereIn('user_id', $user);
        $stats['received'] = DB::connection('reports')->table('shipments')->whereIn('shipper_status_id', [2, 3, 4])->whereBetween('shipments.created_at', [$thirtyDays, $today])->whereIn('user_id', $user);
        $stats['delivered'] = DB::connection('reports')->table('shipments')->whereIn('shipper_status_id', [14, 16, 30, 36, 37, 39, 40, 41, 47])->whereBetween('shipments.created_at', [$thirtyDays, $today])->whereIn('user_id', $user);
        $stats['return'] = DB::connection('reports')->table('shipments')->whereIn('shipper_status_id', [20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 31, 32, 33, 34, 35, 38, 42, 43, 44, 45, 46, 50])->whereBetween('shipments.created_at', [$thirtyDays, $today])->whereIn('user_id', $user);
        $stats['in_process'] = DB::connection('reports')->table('shipments')->whereIn('shipper_status_id', [5, 6, 7, 8, 9, 10, 11, 12, 13, 15, 18, 19, 49, 52])->whereBetween('shipments.created_at', [$thirtyDays, $today])->whereIn('user_id', $user);
        if ($restriction == true) {
            $stats['total'] = $stats['total']->join('substitute_user_shipments as sus', function ($join) {
                $join->on('sus.shipment_id', '=', 'shipments.id')
                    ->where('sus.substitute_user_id', '=', Auth::id());
            });
            $stats['booked'] = $stats['booked']->join('substitute_user_shipments as sus', function ($join) {
                $join->on('sus.shipment_id', '=', 'shipments.id')
                    ->where('sus.substitute_user_id', '=', Auth::id());
            });
            $stats['canceled'] = $stats['canceled']->join('substitute_user_shipments as sus', function ($join) {
                $join->on('sus.shipment_id', '=', 'shipments.id')
                    ->where('sus.substitute_user_id', '=', Auth::id());
            });
            $stats['received'] = $stats['received']->join('substitute_user_shipments as sus', function ($join) {
                $join->on('sus.shipment_id', '=', 'shipments.id')
                    ->where('sus.substitute_user_id', '=', Auth::id());
            });
            $stats['delivered'] = $stats['delivered']->join('substitute_user_shipments as sus', function ($join) {
                $join->on('sus.shipment_id', '=', 'shipments.id')
                    ->where('sus.substitute_user_id', '=', Auth::id());
            });
            $stats['return'] = $stats['return']->join('substitute_user_shipments as sus', function ($join) {
                $join->on('sus.shipment_id', '=', 'shipments.id')
                    ->where('sus.substitute_user_id', '=', Auth::id());
            });
            $stats['in_process'] = $stats['in_process']->join('substitute_user_shipments as sus', function ($join) {
                $join->on('sus.shipment_id', '=', 'shipments.id')
                    ->where('sus.substitute_user_id', '=', Auth::id());
            });
        }
        if ($origin) {
            $stats['total'] = $stats['total']->whereExists(function ($query) use ($origin) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function ($sub_query) use ($origin) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $origin);
                    });
            });
            $stats['booked'] = $stats['booked']->whereExists(function ($query) use ($origin) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function ($sub_query) use ($origin) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $origin);
                    });
            });
            $stats['canceled'] = $stats['canceled']->whereExists(function ($query) use ($origin) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function ($sub_query) use ($origin) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $origin);
                    });
            });
            $stats['received'] = $stats['received']->whereExists(function ($query) use ($origin) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function ($sub_query) use ($origin) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $origin);
                    });
            });
            $stats['delivered'] = $stats['delivered']->whereExists(function ($query) use ($origin) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function ($sub_query) use ($origin) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $origin);
                    });
            });
            $stats['return'] = $stats['return']->whereExists(function ($query) use ($origin) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function ($sub_query) use ($origin) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $origin);
                    });
            });
            $stats['in_process'] = $stats['in_process']->whereExists(function ($query) use ($origin) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function ($sub_query) use ($origin) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $origin);
                    });
            });
        }

        if ($destination) {
            $stats['total'] = $stats['total']->where('consignee_city_id', $destination);
            $stats['booked'] = $stats['booked']->where('consignee_city_id', $destination);
            $stats['received'] = $stats['received']->where('consignee_city_id', $destination);
            $stats['canceled'] = $stats['canceled']->where('consignee_city_id', $destination);
            $stats['delivered'] = $stats['delivered']->where('consignee_city_id', $destination);
            $stats['return'] = $stats['return']->where('consignee_city_id', $destination);
            $stats['in_process'] = $stats['in_process']->where('consignee_city_id', $destination);
        }

        $stats['total'] = number_format($stats['total']->count());
        $stats['booked'] = number_format($stats['booked']->count());
        $stats['canceled'] = number_format($stats['canceled']->count());
        $stats['received'] = number_format($stats['received']->count());
        $stats['delivered'] = number_format($stats['delivered']->count());
        $stats['return'] = number_format($stats['return']->count());
        $stats['in_process'] = number_format($stats['in_process']->count());
        return response()->json(['status' => 1, 'stats' => $stats]);
    }

    public function opt_verify(Request $request){
        $code = $request->code;
        if($code){
            $otp_verification = UserOtpVerification::where('user_id', session('user_id'))->where('otp', $code);
            if($otp_verification->exists()){
                $otp_verification->delete();
                return response()->json(['status' => 1, 'success' => 'Your Phone Number verified for this month!']);

            }
            else{
                return response()->json(['status' => 0, 'error' => 'Invalid OTP Code!']);
            }
        }
    }
    public function opt_verify_close(Request $request){
        $request->session()->forget('phone_number_unverified');
        return response()->json(['status' => 1]);
    }

    public function orders_index() {
//        dd(session()->all());
        $should_not_show_status = array(32,33,34,35,36,37,38,46);
        $cities = City::where('status',1)->select('id','name')->get();
        $dispute_types = DisputeType::whereIn('id',[5,9])->get();
        $shipment_status = ShipmentStatus::select('id','name')->whereNotIn('id',$should_not_show_status)->get();
        $service_type = BookingType::all();
        $products = Product::select('id','product_name')->get();
        $payment_status = ShipmentPaymentStatus::all();
//        $case_nature = CrmRequestCaseNature::get();
//        $case_nature_type_complaints = CrmRequestCaseNatureType::where('nature_id', '=', 1)->where('status_id',1)->get();
//        $case_nature_type_service_requests = CrmRequestCaseNatureType::where('nature_id', '=', 2)->where('status_id',1)->get();
//        $case_nature_type_claims = CrmRequestCaseNatureType::where('nature_id', '=', 4)->where('status_id',1)->get();
        $business_categories = BusinessCategory::all();
        $payment_module = PaymentMode::all();

//        DASHBOARD ORDER DETAILS

        $permission = session('permissions');

        $case_nature = CrmRequestCaseNature::where('id', '!=', 3)->get();
        $row = array();
        if(session('user_type') !== 1){
            foreach($case_nature as $nature) {
                if (in_array(16,$permission) && ($nature->id == 1)) {
                    $row[] = $nature;
                }

                elseif (in_array(17,$permission) && ($nature->id == 2)) {
                    $row[] = $nature;
                }

                elseif (in_array(18,$permission) && ($nature->id == 3 || $nature->id == 4)) {
                    $row[] = $nature;
                }

            }
            $case_nature = $row;
        }

        $case_nature_type_complaints = CrmRequestCaseNatureType::where('nature_id', '=', 1)->where('status_id',1)->get();
        $case_nature_type_service_requests = CrmRequestCaseNatureType::where('nature_id', '=', 2)->where('status_id',1)->get();
        $case_nature_type_claims = CrmRequestCaseNatureType::where('nature_id', '=', 4)->where('status_id',1)->get();


        $merged_accounts = [];
        $get_merged_head_id = MergedSisterAccount::where('user_id', session('user_id'))->first();
        if($get_merged_head_id){
            $merged_head_id =  $get_merged_head_id->merged_head_id;
            
            $merged_user_id =  $get_merged_head_id->user_id;
            $merged_accounts = SubstituteUserMergeSisterAccountMapping::leftjoin('users as u', 'u.id', '=', 'substitute_user_merge_sister_account_mappings.sister_user_id')
            ->where('substitute_user_merge_sister_account_mappings.head_user_id', session('user_id'))
            ->where('substitute_user_merge_sister_account_mappings.substitute_user_id',session('substitute_user_id'))
            ->select('u.id', 'u.name')->get();
        }
//        END


      return view('client.dashboard')->with(['case_nature' => $case_nature,'cities'=>$cities,'dispute_types'=>$dispute_types,'shipment_status'=>$shipment_status,'service_type'=>$service_type,'products'=>$products,'payment_status'=>$payment_status,'case_nature_complaints' => $case_nature_type_complaints, 'case_nature_service_requests' => $case_nature_type_service_requests, 'case_nature_type_claims' => $case_nature_type_claims, 'business_categories' => $business_categories , 'payment_module' => $payment_module, 'merged_accounts'=> $merged_accounts]);
    }
    public function orders_list(Request $request) {

        $masp = [session('user_id')];
        $merged_account_sister_mapping = MergedSisterAccountMapping::where('head_user_id',session('user_id'))->pluck('sister_user_id')->toArray();
        
        if(count($merged_account_sister_mapping) >  0){
            $masp = array_merge($masp,$merged_account_sister_mapping);
        }

        //  if (!in_array(session('user_id'), [167, 1159, 2035, 3324, 4740, 4758, 5982, 10104, 14110, 7762])) {
        //     $connection = 'reports';
        //  }
        //  else {
        //      $connection = 'mysql';
        //  }
        if ($request->get('booking_from_date') && $request->get('booking_to_date')) {
            $from = $request->get('booking_from_date');
            $to = $request->get('booking_to_date');
            $from_back = Carbon::parse($from)->subMonth()->format('Y-m-d H:i:s');
            $to_forward = Carbon::parse($to)->addMonth()->format('Y-m-d H:i:s');
        }

        $connection = 'reports';
        $shipments = DB::connection($connection)->table('shipments')
            ->leftJoin('users as u', 'shipments.user_id', '=', 'u.id')
            ->leftJoin('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->leftJoin('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->leftJoin('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->leftJoin('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->leftJoin('shipping_modes as sm','sm.id','=','shipments.shipping_mode_id')
            ->leftJoin('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->leftJoin('payment_modes as pm','pm.id','=','shipments.payment_mode_id')
            ->leftjoin('shipments_journey', function ($join)use ($from,$to,$from_back,$to_forward) {
                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                    ->where('shipments_journey.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.verification = 1)'))
                       ->whereBetween('shipments_journey.created_at', [$from_back, $to_forward]);
            })
            ->leftJoin('shipment_status as ss','ss.id','=','shipments_journey.shipper_status_id')
            ->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'shipments_journey.status_reason_id')
            ->leftJoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=' , 'sps.id')
            ->leftJoin('business_categories as bc', 'shipments.business_category_id', '=' , 'bc.id')
            ->leftJoin('booking_channels', 'booking_channels.shipment_id', 'shipments.id')
            ->leftJoin('channels', 'channels.id','booking_channels.channel_id')
            ->whereIn('shipments.user_id', $masp)
            ->select(['u.id as user_id', 'u.name as user_name', 'shipments_journey.remarks as cancellation_remarks','shipments.id as shipment_id','shipments.tracking_number as tracking_number','shipments.order_id','bt.booking_type as service_type','ss.name as status','oc.name as origin','dc.name as destination','shipments.consignee_name','shipments.consignee_phone_number_1 as phone1','shipments.consignee_phone_number_2 as phone2','shipments.consignee_address','shipments.amount','shipments.created_at as booking_date','shipments.special_instructions as instructions','shipments.shipper_status_id', 'sps.name as payment_status','ssr.name as reason', 'shipments_journey.shipper_status_id as status_id', 'shipments.booked_by as booked_by', 'bc.name as business_category' ,'pm.mode as payment_module','shipments.tracking_number as tracking', 'channels.name as channel_name']);
//            ->where('shipments.user_id', session('user_id'))
//            ->orwhereIn('shipments.user_id', session('sister_users'))
//            ->groupBy('shipments.id');



        $shipments = $shipments->where(function ($query) {
            $query->where('shipments.user_id', session('user_id'))
                ->orwhereIn('shipments.user_id', session('sister_users'));
        });

        if(isset($request->search_account_type) && count($request->search_account_type) > 0){
            $shipments = $shipments->whereIn('shipments.user_id', $request->search_account_type);
        }

        if(session('user_type') == 2){
            if(session('restriction') == 1){
                $shipments = $shipments->join('substitute_user_shipments as sus', function($join){
                    $join->on('sus.shipment_id', '=', 'shipments.id')
                        ->where('sus.substitute_user_id', '=', Auth::id());
                });
            }
        }
        if ($request->get('booking_from_date') && $request->get('booking_to_date')) {
            $shipments = $shipments->whereBetween('shipments.created_at', [$from, $to]);
        }

        if ($tracking_numbers = $request->get('tracking_numbers')) {
            $shipments->whereIn('shipments.tracking_number', explode(',', $tracking_numbers));
        }

        if ($phone_number = $request->get('phone_number')) {
            $shipments->where('shipments.consignee_phone_number_1', $phone_number);
        }


        //Log::channel('cronJobLog')->info('s ' .$shipments->toSql() .'shipments.created_at '. $from. ' and '.$to.' users ' .session('user_id'));
        $datatable = Datatables::of($shipments)
            ->editColumn('tracking_number', function ($shipments) {
                $route = route('cod.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('phone',function ($shipments){
                if($shipments->phone2)
                    return $shipments->phone1.", ".$shipments->phone2;                    
                else
                    return $shipments->phone1." ".$shipments->phone2;
            })
            ->editColumn('amount', function($shipment){
                return number_format($shipment->amount);
            })
            ->editColumn('cancellation_remarks',function ($shipments){
                if($shipments->cancellation_remarks != null && $shipments->status_id == 17){
                    return $shipments->cancellation_remarks;
                }
                else{
                    return '-';
                }
            })
            ->editColumn('booked_by',function ($shipments){
                if($shipments->booked_by != null){
                    if($shipments->booked_by == 1){
                        return 'Main User';
                    }
                    else{
                        return 'Substitute User';
                    }
                }
                else{
                    return '-';
                }
            })
            ->filterColumn('phone', function ($query, $keyword) {
                $keyword = strtolower($keyword);

                $keyword = str_replace('-', '', $keyword);

                if ($keyword != '') {
                    $keyword = '%' . $keyword . '%';

                    $query->where(function ($sub_query) use ($keyword) {
                        $sub_query->whereRaw('REPLACE(`shipments`.`consignee_phone_number_1`, "-", "") LIKE ?', [$keyword])
                        ->orWhereRaw('REPLACE(`shipments`.`consignee_phone_number_2`, "-", "") LIKE ?', [$keyword]);
                    });
                }

                else {
                    $query->whereRaw('false');
                }
            })
            ->orderColumn('phone', 'shipments.consignee_phone_number_1 $1, shipments.consignee_phone_number_2 $1')
            ->addColumn('action',function ($shipments) {
                $view_charges_button = '<button type="button" class="dropdown-item view_charges"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Charges</div></button>';
                $cancel_button = '<button type="button" class="dropdown-item cancel_order"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-crosshair"></i></div><div class="col-9 offset-1">Cancel</div></button>';
                $dispute_button = '<button type="button" class="dropdown-item dispute_modal"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-alert-circle"></i></div><div class="col-9 offset-1">Dispute</div></button>';

                if ($shipments->shipper_status_id != 17) {
                    $options = FALSE;
                    $dropdown = '
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                            <div class="dropdown-menu dropdown-menu-sm">
                    ';

                    if ($shipments->shipper_status_id > 1 && (session('user_type') == 1 || in_array(5, session('permissions')))) {
                        $dropdown .= $view_charges_button;

                        $options = TRUE;
                    }

                    if ($shipments->shipper_status_id == 1 && (session('user_type') == 1 || in_array(2, session('permissions')))) {
                        $dropdown .= $cancel_button;

                        $options = TRUE;
                    }

                    // if (session('user_type') == 1 || in_array(6, session('permissions'))) {
                    //     $dropdown .= $dispute_button;

                    //     $options = TRUE;
                    // }


                    $dropdown .= '
                            </div>
                        </div>
                    ';

                    if ($options) {
                        if($shipments->user_id == session('user_id')){
                            return $dropdown;
                        }
                        else{
                            return '';
                        }
                    }
                    else {
                        return '';
                    }
                }
                else {
                    return '';
                }
            })
            ->filterColumn('status',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('shipments.shipper_status_id', $keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })

            ->filterColumn('payment_status',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('shipments.payment_status_id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            });

            return $datatable->rawColumns(['tracking_number','action'])->make(true);
    }
    public function order_cancel(Request $request){
        $shipment_id = $request->shipment_id;
        $reason = $request->reason;

        if($shipment_id){
            $shipment = Shipment::where('id',$shipment_id)->where('user_id', session('user_id'));
            if($shipment->exists()){
                $shipment = $shipment->first();

                if ($shipment->shipper_status_id == 1 && $shipment->shipment_type == 1) {
                    $other_retail_shipment = OtherRetailShipment::where('shipment_id',$shipment_id);
                    if ($other_retail_shipment->exists()) {
                        $other_retail_shipment = $other_retail_shipment->first();
                        $other_retail_shipment->delete();
                    }
                    $other_parcel_receiving_shipment = OtherParcelReceivingShipment::where('shipment_id', $shipment_id);
                    if ($other_parcel_receiving_shipment->exists()) {
                        $other_parcel_receiving_shipment = $other_parcel_receiving_shipment->get()->first();
                        $other_parcel_receiving_id = $other_parcel_receiving_shipment->other_parcel_receiving_id;
                        $other_parcel_receiving_shipment->delete();
                        $other_parcel_receiving = OtherParcelReceiving::find($other_parcel_receiving_id);
                        $other_parcel_receiving->total_cn = $other_parcel_receiving->total_cn-1;
                        $other_parcel_receiving->save();
                        if($other_parcel_receiving->total_cn < 1){
                            $other_parcel_receiving->total_cn = 0;
                            $other_parcel_receiving->save();
                        } 
                    }    
                    if($shipment->warehouse == 1){
                        return response()->json(['status' => 0,'error' => 'Warehouse Shipment can not be cancelled from Sonic!']);
                    }
                    //Consolidated Shipments
                    $consolidated_shipment = ConsolidationShipments::where('shipment_id', $shipment_id)->first();
                    if($consolidated_shipment){
                        $consolidation_id = $consolidated_shipment->consolidation_id;
                        ConsolidationShipments::where('id', $consolidated_shipment->id)->delete();
                        $remaining_consolidated_shipments = ConsolidationShipments::where('consolidation_id', $consolidation_id)->get();
                        if(count($remaining_consolidated_shipments) == 1){
                            ConsolidationShipments::where('consolidation_id', $consolidation_id)->delete();
                            Consolidation::where('id', $consolidation_id)->delete();
                        }
                        else{
                            foreach ($remaining_consolidated_shipments as $index => $remaining_consolidated_shipment){
                                $new_order_consolidated_shipment = ConsolidationShipments::find($remaining_consolidated_shipment->id);
                                $new_order_consolidated_shipment->order = $index + 1;
                                $new_order_consolidated_shipment->save();
                            }
                            $consolidation = Consolidation::find($consolidation_id);
                            $consolidation->count = count($remaining_consolidated_shipments);
                            if($consolidation->default_shipment_id == $shipment_id){
                                $consolidation->default_shipment_id = $remaining_consolidated_shipments[0]->shipment_id;
                            }
                            $consolidation->save();
                        }
                    }
                    //Consolidated Shipments
                    $shipment->shipper_status_id = 17;
                    $shipment->consignee_status_id = 17;

                    $packaging_material = PackagingMaterialRequest::where('tracking_number', $shipment->tracking_number)->first();
                    if($packaging_material){
                        $packaging_material->status_id = 6;
                        $packaging_material->save();
                    }
                    
                    if($shipment->warehouse == 1){
                        $shipment->warehouse_order_status = 9;
                        $shipment_products = WmsShipmentProduct::where('shipment_id', $shipment->id)->get();
                        if($shipment_products){
                            foreach ($shipment_products as $shipment_product){
                                $pending_pickings_products = WmsPendingPicking::leftjoin('wms_pending_picking_shipments as wpps', 'wpps.picking_id', '=', 'wms_pending_pickings.id')
                                    ->select('wms_pending_pickings.id as id')
                                    ->where('wpps.shipment_id', $shipment->id)
                                    ->where('wms_pending_pickings.product_id', $shipment_product->product_id)->first();
                                if($pending_pickings_products){
                                    $pending_picking = WmsPendingPicking::find($pending_pickings_products->id);
                                    $pending_picking->quantity = $pending_picking->quantity - $shipment_product->quantity;
                                    $pending_picking->save();

                                    $current_stock_addition = WmsCurrentStock::where('product_id', $shipment_product->product_id)->where('warehouse_pickup_address_id', $shipment->pickup_address_id)->first();
                                    if($current_stock_addition){
                                        $current_stock_addition->stock = $current_stock_addition->stock + $shipment_product->quantity;
                                        $current_stock_addition->save();
                                    }

                                    if($pending_picking->quantity <= 0){
                                        $pending_picking->status = 1;
                                        $pending_picking->save();
                                    }
                                }
                            }
                        }
                    }
                    $shipment->save();

                    V2AdminPickupsController::cancel($shipment_id);

                    ShipmentsJourneyController::add($shipment_id, 17, 17, NULL, 'Cancelled by Shipper '.'- '. $reason , session('user_id'), NULL);

                    return response()->json(['status'=>1,'success'=>'Shipment has been cancelled successfully']);
                }
                else {
                  
                    return response()->json(['status'=>0,'error'=>'Shipment\'s Status has already been changed']);
                }
            }else{
                return response()->json(['status'=>0,'error'=>'Shipment not found']);
            }
        }
    }
    public function order_cancel_all(Request $request)
    {
        $correct = FALSE;
        $reason = $request->reason;
        if(is_array($request->ids)){
            if(count($request->ids) > 0){
                foreach ($request->ids as $id) {
                    $shipment = Shipment::where('id', $id)->where('user_id', session('user_id'));
                    if ($shipment->exists()) {
                        $shipment = $shipment->first();

                        if ($shipment->shipper_status_id == 1 && $shipment->shipment_type == 1) {

                            $other_retail_shipment = OtherRetailShipment::where('shipment_id',$id);
                            if ($other_retail_shipment->exists()) {
                                $other_retail_shipment = $other_retail_shipment->first();
                                $other_retail_shipment->delete();
                            }
                            $other_parcel_receiving_shipment = OtherParcelReceivingShipment::where('shipment_id', $id);
                            if ($other_parcel_receiving_shipment->exists()) {
                                $other_parcel_receiving_shipment = $other_parcel_receiving_shipment->get()->first();
                                $other_parcel_receiving_id = $other_parcel_receiving_shipment->other_parcel_receiving_id;
                                $other_parcel_receiving_shipment->delete();
                                $other_parcel_receiving = OtherParcelReceiving::find($other_parcel_receiving_id);
                                $other_parcel_receiving->total_cn = $other_parcel_receiving->total_cn-1;
                                $other_parcel_receiving->save();
                                if($other_parcel_receiving->total_cn < 1){
                                    $other_parcel_receiving->total_cn = 0;
                                    $other_parcel_receiving->save();
                                }
                            }
                            if($shipment->warehouse == 1){
                                continue;
                            }
                            //Consolidated Shipments
                            $consolidated_shipment = ConsolidationShipments::where('shipment_id', $shipment->id)->first();
                            if($consolidated_shipment){
                                $consolidation_id = $consolidated_shipment->consolidation_id;
                                ConsolidationShipments::where('id', $consolidated_shipment->id)->delete();
                                $remaining_consolidated_shipments = ConsolidationShipments::where('consolidation_id', $consolidation_id)->get();

                                if(count($remaining_consolidated_shipments) == 1){
                                    ConsolidationShipments::where('consolidation_id', $consolidation_id)->delete();
                                    Consolidation::where('id', $consolidation_id)->delete();
                                }
                                else{
                                    foreach ($remaining_consolidated_shipments as $index => $remaining_consolidated_shipment){
                                        $new_order_consolidated_shipment = ConsolidationShipments::find($remaining_consolidated_shipment->id);
                                        $new_order_consolidated_shipment->order = $index + 1;
                                        $new_order_consolidated_shipment->save();
                                    }
                                    $consolidation = Consolidation::find($consolidation_id);
                                    $consolidation->count = count($remaining_consolidated_shipments);
                                    if($consolidation->default_shipment_id == $shipment->id){
                                        $consolidation->default_shipment_id = $remaining_consolidated_shipments[0]->shipment_id;
                                    }
                                    $consolidation->save();
                                }
                            }
                            //Consolidated Shipments
                            $shipment->shipper_status_id = 17;
                            $shipment->consignee_status_id = 17;
                            ShipmentsJourneyController::add($shipment->id, 17, 17, NULL, 'Cancelled by Shipper ' .'- '. $reason, session('user_id'), NULL);
                            $packaging_material = PackagingMaterialRequest::where('tracking_number', $shipment->tracking_number)->first();
                            if($packaging_material){
                                $packaging_material->status_id = 6;
                                $packaging_material->save();
                            }
                            if($shipment->warehouse == 1){
                                $shipment->warehouse_order_status = 9;
                                $shipment_products = WmsShipmentProduct::where('shipment_id', $shipment->id)->get();
                                if($shipment_products){
                                    foreach ($shipment_products as $shipment_product){
                                        $pending_pickings_products = WmsPendingPicking::leftjoin('wms_pending_picking_shipments as wpps', 'wpps.picking_id', '=', 'wms_pending_pickings.id')
                                            ->select('wms_pending_pickings.id as id')
                                            ->where('wpps.shipment_id', $shipment->id)
                                            ->where('wms_pending_pickings.product_id', $shipment_product->product_id)->first();
                                        if($pending_pickings_products){
                                            $pending_picking = WmsPendingPicking::find($pending_pickings_products->id);
                                            $pending_picking->quantity = $pending_picking->quantity - $shipment_product->quantity;
                                            $pending_picking->save();

                                            $current_stock_addition = WmsCurrentStock::where('product_id', $shipment_product->product_id)->where('warehouse_pickup_address_id', $shipment->pickup_address_id)->first();
                                            if($current_stock_addition){
                                                $current_stock_addition->stock = $current_stock_addition->stock + $shipment_product->quantity;
                                                $current_stock_addition->save();
                                            }

                                            if($pending_picking->quantity <= 0){
                                                $pending_picking->status = 1;
                                                $pending_picking->save();
                                            }
                                        }
                                    }
                                }
                            }

                            $shipment->save();

                            V2AdminPickupsController::cancel($shipment->id);

//                    ShipmentsPickupJourneyController::add($shipment->id, 4);


                            $correct = TRUE;
                        }
                    }

                }
            }
        }
        if ($correct) {
            return response()->json(['status' => 1, 'success' => 'Shipment(s) has been cancelled successfully']);
        }
        else {
            return response()->json(['status' => 0, 'error' => 'No Shipment could be cancelled']);
        }
    }
    public function get_shipment_charges(Request $request){
        $shipment_id = $request->shipment_id;
        //$shipment = Shipment::find($shipment_id);
        $shipment = Shipment::leftJoin('pending_payment_shipments as pps','shipments.id','=','pps.shipment_id')
        ->leftJoin('done_payment_shipments as dps','shipments.id','=','dps.shipment_id')
        ->leftJoin('pending_invoice_shipments as pis','shipments.id','=','pis.shipment_id')
        ->leftJoin('invoice_shipments as is','shipments.id','=','is.shipment_id')
        ->leftJoin('users as users','shipments.user_id' ,'=','users.id')
        ->where('shipments.id', $shipment_id)
        ->select(['shipments.packaging_material_request','shipments.cash_handling_charges', 'shipments.return_charges', 'shipments.insurance_charges', 'shipments.fuel_surcharge', 'shipments.replacement_charges', 'shipments.try_and_buy_charges', 'shipments.intercept_charges', 'shipments.nsa_osa_charges', 'shipments.weight_charges','pps.sms_charges as pps_sms_charge' , 'dps.sms_charges as dps_sms_charge','pis.sms_charges as pis_sms_charge', 'is.sms_charges as is_sms_charge','users.account_type_id as account_type'])->first();
        $returnHTML = view('client/components/shipment_charges')->with(['shipment'=>$shipment])->render();
        return response()->json($returnHTML);
    }
    public function ecommerce() {
      return view('client.ecommerce');
    }
    public function orderList() {
      return view('client.order_management');
    }
    public function orderPending() {
      return view('client.pending_booked_orders');
    }

    //User Profile

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

    public function userProfile()
    {
        $user = User::find(session('user_id'));
        $payment_cycle_days = $this->user_payment_cycles_days($user);
        $product = Product::find($user->product_id);
        $banks = BanksList::all();
        $city_list = City::where('status',1)->get();
        $emails = ShipperNotificationEmail::where('user_id',$user->id)->select('email')->get();
        $email_ids = ShipperNotificationEmail::where('user_id',$user->id)->pluck('email')->toArray();
        $email_ids = implode(',', $email_ids);
        $pickup_city_list = City::where('pickup',1)->where('status',1)->get();
        $reference = Reference::where('id', $user->reference_id)->first();
        $average_shipment_duration = AverageShipmentCycle::where('id', $user->average_shipment_duration_id)->first();
        $isIbanNotificationEnabled = Notification::select('status')->find(91)->status ?? false;

        return view('client.profile.index')->with(['user'=>$user,'product_name'=>$product,'banks'=>$banks,'pickup_city_list'=>$pickup_city_list, 'emails' => $emails, 'email_ids' => $email_ids, 'reference' => $reference, 'average_shipment_duration' => $average_shipment_duration, 'cities_list' => $city_list, 'days'=>$payment_cycle_days, 'isIbanNotificationEnabled' => $isIbanNotificationEnabled]);
    }

    public function storeShipperId(Request $request)
    {
        $userId = $request->input('userId');
        $shipperStoreId = $request->input('shipper_store_id');
        $userShippingInfosId = $request->input('user_shipper_infos_id');
        $userShippingInfosStatus = $request->input('user_shipper_infos_status');
        if ($userId != 2234 && $userId != 1049){
            return response()->json(['error' => 'Invalid user']);
        } 
        if (!$shipperStoreId) {
            return response()->json(['error' => 'Please enter a store ID']);
        }
        // if (!is_numeric($shipperStoreId) || $shipperStoreId < 0) {
        //     return response()->json(['error' => 'Shipper Store ID must be a non-negative number']);
        // }

        $status = null;
        if ($userShippingInfosStatus == "Enabled") {
            $status = 1;
        } else {
            $status = 0;
        }

        if ($userShippingInfosId) {
            UserShippingInfoStoreAddress::updateOrCreate(
                [
                    'user_id' => $userId,
                    'user_shipping_infos_id' => $userShippingInfosId,
                ],
                [
                    'shipper_store_id' => $shipperStoreId,
                    'status' => $status,
                ]
            );
            return response()->json(['success' => 'Store ID updated successfully']);
        }
        return response()->json(['error' => 'No User Shipping Info found']);
    }

    public function verifyPincode(Request $request)
    {
        if(isset($request->action) && $request->action == 'verify_pincode')
        {
            $user_id = session('user_id');
            $pin = rand(1000,9999);
            NotificationsController::send(91,$user_id,$pin);

            // Get the profile_otp from SMS model
            $profile_otp = AdminShipperVerificationPinCode::where('otp', $pin)->first(); //Model Updated
            if ($profile_otp) {
                $data['code'] = $pin;
                return json_encode($data);
            }
            // $data['code'] = $pin;
            // return json_encode($data);
        }
    }

    public function addBank(Request $request){

        $user_id    = session('user_id');
        if($user_id){
            if(UserBankInfo::where('user_id',$user_id)->exists())
            {
                UserBankInfo::where('user_id', $user_id)->update(['default_bank' => 0]);
            }
            $user_bank = new UserBankInfo();
            $user_bank->user_id = $user_id;
            $user_bank->bank_name = $request->bank_select;
            $user_bank->bank_branch = $request->bank_branch;
            $user_bank->account_no = $request->account_no;
            $user_bank->account_title = $request->account_title;
            $user_bank->iban = strtoupper($request->iban_no);
            $user_bank->city_id = $request->bank_city;
            $user_bank->default_bank = 1; // always make the new bank info as default
            $user_bank->save();
            
            return redirect()->back()->with(['success' => 'Bank successfully added!']);

        }
        return redirect()->back()->with(['error' => 'Session Expired!']);
    }

    public function getBanks(Request $request){
        $banks = UserBankInfo::leftJoin('cities as c','user_bank_infos.city_id','=','c.id')
        ->leftJoin('banks_lists as bl','bl.id','=','user_bank_infos.bank_name')
        ->leftjoin('wallet_users as wu', function ($join) {
            $join->on('wu.user_id', '=', 'user_bank_infos.user_id')
               ->where('wu.substitute_user_id', '0');
        })
        ->select(['user_bank_infos.id as bank_row_id','user_bank_infos.bank_branch','user_bank_infos.account_no','user_bank_infos.account_title','user_bank_infos.iban','c.name as city','bl.name as bank_name','user_bank_infos.default_bank', 'wu.user_id as wallet_user'])
        ->where('user_bank_infos.user_id', session('user_id'));

        return Datatables::of($banks)
        ->addColumn('action', function ($bank) {

            if(!$bank->wallet_user) {
                $dropdown = '
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                ';
                $default_button = '<button type="button" class="dropdown-item default"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Make Default</div></button>';

                if ($bank->default_bank) {
                    $dropdown = 'Default Address';

                }else{
                    $dropdown .= $default_button;
                }
                

                $dropdown .= '
                        </div>
                    </div>
                ';

                
            } else {
                if ($bank->default_bank) {
                    $dropdown = 'Default Address';

                }else{
                    $dropdown = '';
                }
            }

            return $dropdown;
        })->make(true);
    }

    public function getPickups(Request $request) {
        $pickups = UserShippingInfo::join('cities as c', 'user_shipping_infos.city_id', '=', 'c.id')
        ->leftjoin('city_areas as ca', 'user_shipping_infos.city_area_id', '=', 'ca.id')
        ->leftjoin('user_shipping_info_store_addresses as usisa', 'user_shipping_infos.id', '=', 'usisa.user_shipping_infos_id')
        ->select(['user_shipping_infos.id as id','user_shipping_infos.pickup_brand_name as pickup_brand_name','user_shipping_infos.pickup_address as pickup_address','user_shipping_infos.poc as poc','user_shipping_infos.phone as phone','user_shipping_infos.email as email','user_shipping_infos.status as status','user_shipping_infos.default_address as default_address','user_shipping_infos.user_id as user_id','c.name as city_name','c.id as city_id', 'user_shipping_infos.vendor','ca.name as city_area_name', 'user_shipping_infos.default_return_address', 'usisa.shipper_store_id as shipper_store_id',])
        // ->where('user_id', session('user_id'))
        ->where('user_shipping_infos.user_id', session('user_id'))
        ->where('hidden', 0);

        return Datatables::of($pickups)
        ->addColumn('action', function ($pickup) {
            $dropdown = '
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
            ';

            $edit_button = '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Update</div></button>';
            $disable_button = '<button type="button" class="dropdown-item disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';
            $enable_button = '<button type="button" class="dropdown-item enable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
            $default_button = '<button type="button" class="dropdown-item default"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Make Default Address</div></button>';
            $return_default_button = '<button type="button" class="dropdown-item return_default"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Make Default Return Address</div></button>';
            $add_store_id_button = '<button type="button" class="dropdown-item add_store_id"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Add Store ID</div></button>';
            $edit_store_id_button = '<button type="button" class="dropdown-item add_store_id"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit Store ID</div></button>';
            
            // if((auth()->user()->id == 2234 || auth()->user()->id == 10364) && $store_id_check){
            //     $dropdown .= $add_store_id_button;
            // }

            $active_status_count = UserShippingInfo::where('user_id',$pickup->user_id)
            ->where('status', 1)
            ->where('hidden', 0)
            ->count();

            if ($active_status_count == 1) {
                $disable_button = '<button type="button" class="dropdown-item disable btn btn-danger text-white" disabled><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';
            }

            if ((auth()->user()->id == 2234 || auth()->user()->id == 1049) && $pickup->shipper_store_id) {
                $dropdown .= $edit_store_id_button;
            } 
            if ((auth()->user()->id == 2234 || auth()->user()->id == 1049)  && !$pickup->shipper_store_id) {
                $dropdown .= $add_store_id_button;
            }
            if ($pickup->default_address == 1) {
                $dropdown .= '<label class="row no-gutters align-items-center p-1 font-size-small">Default Address</label>';
            }
            else{
                $dropdown .= $default_button;
            }

            if(!Shipment::where('pickup_address_id', $pickup->id)->where('shipper_status_id', '>', 1)->exists()){
                $dropdown .= $edit_button;
            }
            if ($pickup->status == 0) {
                $dropdown .= $enable_button;
            }
            else {
                if (UserShippingInfo::where('user_id', $pickup->user_id)->where('hidden', 0)->count() > 1) {
                    $dropdown .= $disable_button;
                }
            }


            $omni_user = false;
            $settings = GlobalSettings::where('type', 'omni_users');
            if ($settings->exists()) {
                $settings = $settings->first();
                if ($settings->text != NULL) {
                    $omni_accounts = array_map('intval', explode(',', $settings->text));
                    if (in_array($pickup->user_id, $omni_accounts)) {
                        $omni_user = true;
                    }
                }
            }

            if($omni_user){
                if((!$pickup->default_return_address) && ($pickup->status == 1)){
                    $dropdown .= $return_default_button;
                }
                elseif ($pickup->default_return_address){
                    $dropdown .= '<label class="row no-gutters align-items-center p-1 font-size-small">Default Return Address</label>';
                }
            }

            $dropdown .= '
                    </div>
                </div>
            ';

            return $dropdown;
        })
        ->editColumn('status', function ($pickup) {
            return ($pickup->status == 1) ? 'Enabled' : 'Disabled';
        })
        ->filterColumn('shipper_store_id', function ($query, $keyword) {
            $query->where('usisa.shipper_store_id', 'LIKE', '%' . $keyword . '%');
        })
        ->make(true);
    }


    public function pickupStatusChange(Request $request){
        $pickup_id = $request->id;
        $status = $request->status;
        $shipping_info = UserShippingInfo::where('id',$pickup_id)->first();
        $shipper_store_id = UserShippingInfoStoreAddress::where('user_shipping_infos_id', $pickup_id)->first();
        if($shipping_info->exists()){
            if($status == 'enable'){
                if($shipping_info->status == 0){
                    $shipping_info->status = 1;
                    $shipping_info->save();
                    if ($shipper_store_id !== null && (auth()->user()->id == 2234 || auth()->user()->id == 1049)) {
                        $shipper_store_id->status = 1;
                        $shipper_store_id->save();
                    }
                    return response()->json(['status'=>1,'success'=>"Pickup Address is now enabled!"]);
                }else{
                    return response()->json(['status'=>0,'error'=>"Pickup Address is already enabled!"]);
                }
            }else if($status == 'disable'){
                // if(UserShippingInfo::where('user_id',$shipping_info->user_id)->count()==1)
                // {
                //     return response()->json(['status'=>0,'error'=>"Single Pickup Address cannot be set to disabled"]);
                // }

                $active_status_count = UserShippingInfo::where('user_id',$shipping_info->user_id)
                    ->where('status', 1)
                    ->where('hidden', 0)
                    ->count();
                if($active_status_count == 1){
                    return response()->json(['status'=>0,'error'=>"Single Pickup Address cannot be set to disabled"]);
                }

                if($shipping_info->status == 1){
                    $shipping_info->status = 0;
                    $shipping_info->save();
                    if ($shipper_store_id !== null && (auth()->user()->id == 2234 || auth()->user()->id == 1049)) {
                        $shipper_store_id->status = 0;
                        $shipper_store_id->save();
                    }

                    return response()->json(['status'=>1,'success'=>"Pickup Address is now disabled!"]);
                }else{
                    return response()->json(['status'=>0,'error'=>"Pickup Address is already disabled!"]);

                }
            }
            else if($status == 'default')
            {
                if($shipping_info->default_address == 0)
                {
                    $shipping_info->default_address = 1;
                    $shipping_info->save();
                    UserShippingInfo::where('user_id', $shipping_info->user_id)->where('id', '!=', $pickup_id)->update(['default_address' => 0]);
                    return response()->json(['status'=>1,'success'=>"This Pickup Address is now default Pickup Address"]);
                }
                else
                {
                    return response()->json(['status'=>0,'error'=>"Pickup Address is already default Pickup Address"]);
                }

            }
            else if ($status == 'return_default')
            {
                if($shipping_info->default_return_address == 0)
                {
                    $shipping_info->default_return_address = 1;
                    $shipping_info->save();
                    UserShippingInfo::where('user_id', $shipping_info->user_id)->where('id', '!=', $pickup_id)->update(['default_return_address' => 0]);
                    return response()->json(['status'=>1,'success'=>"This Address is now default Return Address"]);
                }
                else
                {
                    return response()->json(['status'=>0,'error'=>"This Address is already default Return Address"]);
                }
            }
        }else{
            return response()->json(['status'=>0,'error'=>"Pickup Address doesn\'t exist!"]);
        }
    }

    static public function clear_defaultBank(){
        $today = Carbon::today();
        $duration = UserDefaultBankDuration::where('day', $today);
        if($duration->exists()){
            $duration = $duration->get();
            foreach ($duration as $key => $value) {
                $old_bank = $value->last_default_bank_id;
                if($old_bank){
                    $user_id = $value->user_id;
                    UserBankInfo::where('user_id', $user_id)->update(['default_bank' => 0]);
                    $bank_info = UserBankInfo::find($old_bank);
                    $bank_info->default_bank = 1;
                    $bank_info->save();
                    $value->delete();
                }
            }
        }
    }

    public function updateDefaultBanks(Request $request){

        $user_id = session('user_id');
        $bank_info_id = $request->bank_info_id;
        if($request->has('default_type_checkbox')){
            $day = $request->day_select;
            // $default_bank_duration = new UserDefaultBankDuration();
            $today = Carbon::today();
            $today->addDays($day);

            $duration = UserDefaultBankDuration::where('user_id',$user_id);
            if($duration->exists()){
                $duration = $duration->first();
                $duration->day = $today;
                $duration->save();

            }else{
                $user_bank = UserBankInfo::where('user_id', $user_id)->where('default_bank',1)->first();

                $default_bank_duration = new UserDefaultBankDuration();
                $default_bank_duration->user_id = $user_id;
                $default_bank_duration->last_default_bank_id = $user_bank->id;
                $default_bank_duration->day = $today;
                $default_bank_duration->save();
            }
            UserBankInfo::where('user_id', $user_id)->update(['default_bank' => 0]);
            $user_bank = UserBankInfo::find($bank_info_id);
            $user_bank->default_bank = 1;
            $user_bank->save();

        }else{
            UserDefaultBankDuration::where('user_id', $user_id)->delete();
            UserBankInfo::where('user_id', $user_id)->update(['default_bank' => 0]);
            $user_bank = UserBankInfo::find($bank_info_id);
            $user_bank->default_bank = 1;
            $user_bank->save();

        }
        return redirect()->back()->with(['success' => 'Default Bank Successfully updated!']);
    }


    public function addPickup(Request $request) {
        $pickup_address = $request->pickup_address;
        $pickup_brand_name = $request->pickup_brand_name;
        $phone = $request->phone;
        $poc = $request->poc;
        $vendor = $request->vendor;
        $email = $request->email;
        $city_id = $request->city_id;
        $user_id = session('user_id');

        if($pickup_address != null && $phone != null && $poc != null && $email != null && $city_id != null)
        {
          $usi =  UserShippingInfo::insertGetId(['user_id'=>$user_id,'pickup_address'=>$pickup_address,'pickup_brand_name'=>$pickup_brand_name,'poc'=>$poc,
                'email'=>$email,'city_id'=>$city_id,'phone'=>$phone, 'vendor' => $vendor,'created_at'=>Carbon::now()]);
            ShipperShipmentBookController::shipper_address_area($city_id,$pickup_address,$usi);
            return redirect()->back()->with('success','Pickup Address added successfully!');

        }else{
            return redirect()->back()->with('error','Pickup Address not added!');
        }
    }
    public function editPickup(Request $request) {
        $id = $request->id;
        $pickup_address = $request->pickup_address;
        $phone = $request->phone;
        $poc = $request->poc;
        $vendor = $request->vendor;
        $email = $request->email;
        $city_id = $request->city_id;

        if($pickup_address != null && $phone != null && $poc != null && $email != null && $city_id != null && $id != null)
        {
            $user_shipping_info = UserShippingInfo::find($id);
            if($user_shipping_info){
                $user_shipping_info->pickup_address = $pickup_address;
                $user_shipping_info->pickup_brand_name = $request->pickup_brand_name;
                $user_shipping_info->poc = $poc;
                $user_shipping_info->email = $email;
                $user_shipping_info->city_id = $city_id;
                $user_shipping_info->phone = $phone;
                $user_shipping_info->vendor = $vendor;
                $user_shipping_info->save();
                return redirect()->back()->with('success','Pickup Address updated successfully!');
            }
            else{
                return redirect()->back()->with('error','Pickup Address not found!');
            }
        }
        else{
            return redirect()->back()->with('error','Pickup Address not updated!');
        }
    }


    public function ledger_index()
    {
        return view('client.ledger.index');
    }

    public function ledger_list(Request $request)
    {
        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('done_payment_shipments as dps','dps.shipment_id','=','shipments.id')
            ->join('done_payments as dp','dp.id','=','dps.done_payment_id')
            ->leftjoin('user_bank_infos as ubi', 'ubi.id', '=', 'dp.user_bank_info_id')
            ->leftjoin('banks_lists as bl', 'bl.id', '=', 'ubi.bank_name')
            ->select('shipments.tracking_number as tracking_number','shipments.tracking_number as tracking_id', 'shipments.order_id as order_number', 'dps.done_payment_id as payment_id', 'bl.name as bank_name', 'dps.created_at as payment_date', 'dps.payable as cod', 'dps.type as type','ubi.iban as account_detail')
            ->where('shipments.user_id', session('user_id'))->orderBy('dps.created_at','desc');


        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $shipments->whereBetween('dps.created_at', [$from,$to]);
        }
        if ($request->get('cod_payable_from') && $request->get('cod_payable_to')) {
            $from = $request->get('cod_payable_from');
            $to = $request->get('cod_payable_to');
            $shipments->whereBetween('dps.payable', [$from,$to]);
        }

        $datatable=Datatables::of($shipments)
            ->editColumn('tracking_number', function ($shipments) {
                $route = route('cod.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('order_number',function ($shipments) {
                if ($shipments->order_number != null) {
                    return $shipments->order_number;
                } else {
                    return '-';
                }
            })
            ->editColumn('account_detail',function ($shipments) {
                if ($shipments->account_detail != null) {
                    return $shipments->account_detail;
                } else {
                    return '-';
                }
            })
            ->editColumn('bank_name',function ($shipments) {
                if ($shipments->bank_name != null) {
                    return $shipments->bank_name;
                } else {
                    return '-';
                }
            })
            ->editColumn('type',function ($shipments) {
            if ($shipments->type == 0) {
                return 'Delivered';
            }
            elseif($shipments->type == 1) {
                return 'Returned';
            }elseif($shipments->type == 3) {
                return 'Arrival';
            }
            else{
                return 'Adjusted';
            }
            })->rawColumns(['tracking_number']);


        return $datatable->make(true);

    }

    public function updateProfile(Request $request)
    {
        //1 for Admin, 0 for User
        if (session('user_type') == 1) {
                $request->validate([
                    'poc'=>'required|string|max:255',
                    'phone'=>'required|string|max:255',
                    'email'=>'required|email|between:0,100',
                ]);
                $flag = true;
                $user = User::where('email', $request->email)->orWhere('phone', $request->phone)->first();
                if($user){
                    if(session('user_id') == $user->id) {
                        $flag = true;
                    }
                    else{
                        $flag = false;
                    }
                }
                if($flag == true){
                    User::where('id', session('user_id'))->update(['poc' => $request->poc, 'phone' => $request->phone, 'phone2' => $request->phone2, 'email' => $request->email,
                        'updated_by_type' => 0, 'updated_by_id' => session('user_id')]);
                }
                else{
                    return redirect()->back()->with(['error'=>"Email Address and Phone Number must be unique"]);
                }
            }
        else{
            $request->validate([
            'poc'=>'required|string|max:255',
            'phone'=>'required|string|max:255',
        ]);
            User::where('id', session('user_id'))->update(['poc'=>$request->poc,'phone'=>$request->phone,'phone2'=>$request->phone2,
                'updated_by_type'=>0,'updated_by_id'=> session('user_id')]);
        }


        return redirect()->back()->with(['success'=>"Profile Information Successfully Updated"]);
    }

    public function update_profile_password(Request $request)
    {
        if (session('user_type') == 1) {
                $request->validate([
                    'password' => 'required|string|min:6',
                ]);
                if($request->password == $request->confirm_password){
                    User::where('id', session('user_id'))->update(['password' => Hash::make($request->password), 'updated_by_type' => 0, 'updated_by_id' => session('user_id')]);

                    if(session('status') == 0){
                        return redirect()->route('cod.welcome');
                    }else{
                        return redirect()->back()->with(['success'=>"Password Updated Successfully!"]);
                    }
                }
                else{
                    return redirect()->back()->with(['error'=>"The password and confirmation password do not match"]);
                }
            }
        else{
            return redirect()->back()->with(['error'=>"You are not allowed to change password"]);
        }

    }


    public function add_notification_emails(Request $request){
        $emails = $request->email_address;
        if($emails != ''){
            $email_address = explode(',', $emails);
            $user = session('user_id');
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
            $user = session('user_id');
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

    public function view_rates_index(){
        
        $id = session('user_id');
        
        $user = User::find($id);
        $sms_charge = User::where('id', $id)->select(['id','sms_charges','sms_charges_status'])->get();
        if(session('account_type') == 1){
            $switches = RateStatus::all()->where('user_id',$id)->groupBy('shipping_mode_id');
            $weight = WeightCharge::all()->where('user_id',$id)->groupBy('shipping_mode_id');
            $bookingType = BookingTypeCharges::all()->where('user_id',$id)->groupBy('shipping_mode_id');
            $cash = CashHandlingCharge::all()->where('user_id',$id)->groupBy('shipping_mode_id');
            $insurance = InsuranceCharge::all()->where('user_id',$id)->groupBy('shipping_mode_id');
            $return = ReturnCharge::all()->where('user_id',$id)->groupBy('shipping_mode_id');
            $fuel = FuelSurcharge::all()->where('user_id',$id)->groupBy('shipping_mode_id');
            $discount = DiscountCharge::all()->where('user_id',$id)->groupBy('shipping_mode_id');
            $sale_person = SalePersonTag::where('user_id',$id)->where('status', 0)->first();
            $packaging = PackagingCharge::all()->where('user_id', $id);
            $packaging_type_ids = array_unique($packaging->pluck('type_id')->toArray());

            $discount = DiscountCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');

            $discount_weight_rates = DiscountWeightCharge::all()->where('user_id',$id)->groupBy(['shipping_mode_id','destination_id']);

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

            $rate_origin_hubs = RateOriginHub::all()->where('user_id',$id)->groupBy('shipping_mode_id');
            $rate_destination_hubs = RateDestinationHub::all()->where('user_id',$id)->groupBy('shipping_mode_id');
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
            return view('client.rates.view')->with(['shipper'=>$user,'switches'=>$switches,'weight'=>$weight,'shippingType'=>$bookingType,'cashHandling'=>$cash,'insuranceCharges'=>$insurance,'returnCharges'=>$return,'fuelCharges'=>$fuel,'packagingCharges'=>$packaging,'discountCharges'=>$discount, 'sale_person' => $sale_person, 'packaging_material_types' => $packaging_material_types,  'packaging_type_ids' => $packaging_type_ids, 'packaging_charges' => $packaging_charges, 'wms_user_info' => $wms_user_info, 'wms_product_charges' => $wms_product_charges, 'wms_square_foot_charges' => $wms_square_foot_charges, 'wms_packing_charges' => $wms_packing_charges, 'wms_labelling_charges' => $wms_labelling_charges, 'wms_storage_charges' => $wms_storage_charges, 'invoicing_cycles' => $invoicing_cycles, 'storage_types' => $storage_types, 'rate_remarks' => $rate_remarks, 'overnight_origins' => $overnight_origins, 'overland_origins' => $overland_origins, 'detain_origins' => $detain_origins, 'sameday_origins' => $sameday_origins,'overnight_destinations' => $overnight_destinations, 'overland_destinations' => $overland_destinations, 'detain_destinations' => $detain_destinations, 'sameday_destinations' => $sameday_destinations, 'cities' => $cities,'discount_weight_rates'=>$discount_weight_rates, 'sms_charges' => $sms_charge]);
        }
        else {
            if ($user->corporate_rate_type_id != 3) {
                if($user->corporate_rate_type_id == 2){
                    $weight = CorporateWeightChargeZoneWise::all()->where('user_id', $id)->groupBy('shipping_mode_id');
                    $return = CorporateReturnChargeZoneWise::all()->where('user_id', $id)->groupBy('shipping_mode_id');
                }
                else{
                    $weight = CorporateWeightCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
                    $return = CorporateReturnCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
                }
                $switches = CorporateRateStatus::all()->where('user_id', $id)->groupBy('shipping_mode_id');
                $min_weight = CorporateMinChargeableWeight::all()->where('user_id', $id)->groupBy('shipping_mode_id');
                $bookingType = CorporateBookingTypeCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
                $cash = CorporateCashHandlingCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
                $insurance = CorporateInsuranceCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
                $return = CorporateReturnCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
                $fuel = CorporateFuelSurcharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
                $discount = CorporateDiscountCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
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

                $packaging = PackagingCharge::all()->where('user_id', $id);
                $packaging_type_ids = array_unique($packaging->pluck('type_id')->toArray());
                $packaging_charges = array();
                if (count($packaging) > 0) {

                    foreach ($packaging as $charge) {
                        $packaging_charges[$charge->type_id][] = $charge;
                    }
                }

                $rate_origin_hubs = CorporateRateOriginHub::all()->where('user_id',$id)->groupBy('shipping_mode_id');
                $rate_destination_hubs = CorporateRateDestinationHub::all()->where('user_id',$id)->groupBy('shipping_mode_id');

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
                return view('client.rates.corporate.view')->with(['shipper' => $user, 'switches' => $switches, 'weight' => $weight, 'shippingType' => $bookingType, 'cashHandling' => $cash, 'insuranceCharges' => $insurance, 'returnCharges' => $return, 'fuelCharges' => $fuel, 'discountCharges' => $discount, 'min_weight' => $min_weight, 'wms_user_info' => $wms_user_info, 'wms_product_charges' => $wms_product_charges, 'wms_square_foot_charges' => $wms_square_foot_charges, 'wms_packing_charges' => $wms_packing_charges, 'wms_labelling_charges' => $wms_labelling_charges, 'wms_storage_charges' => $wms_storage_charges, 'invoicing_cycles' => $invoicing_cycles, 'storage_types' => $storage_types, 'packaging_material_types' => $packaging_material_types, 'rate_remarks' => $rate_remarks, 'packaging_charges' => $packaging_charges, 'packaging_type_ids' => $packaging_type_ids, 'overnight_origins' => $overnight_origins, 'overland_origins' => $overland_origins, 'detain_origins' => $detain_origins, 'sameday_origins' => $sameday_origins,'overnight_destinations' => $overnight_destinations, 'overland_destinations' => $overland_destinations, 'detain_destinations' => $detain_destinations, 'sameday_destinations' => $sameday_destinations, 'cities' => $cities, 'sms_charges' => $sms_charge]);

            } else {
                $switches = CorporateDefaultRateStatus::all()->where('user_id', $id)->groupBy('shipping_mode_id');
                $weight = CorporateDefaultWeightCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
                $bookingType = CorporateBookingTypeCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
                $cash = CorporateDefaultCashHandlingCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
                $insurance = CorporateDefaultInsuranceCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
                $return = CorporateDefaultReturnCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
                $fuel = CorporateDefaultFuelSurcharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
                $discount = CorporateDefaultDiscountCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');

                $discount_weight_rates = CorporateDefaultDiscountWeightCharge::all()->where('user_id',$id)->groupBy(['shipping_mode_id','destination_id']);
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
                $rate_origin_hubs = CorporateDefaultRateOriginHub::all()->where('user_id',$id)->groupBy('shipping_mode_id');
                $rate_destination_hubs = CorporateDefaultRateDestinationHub::all()->where('user_id',$id)->groupBy('shipping_mode_id');

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
                return view('client.rates.default.view')->with(['shipper' => $user, 'switches' => $switches, 'weight' => $weight, 'shippingType' => $bookingType, 'cashHandling' => $cash, 'insuranceCharges' => $insurance, 'returnCharges' => $return, 'fuelCharges' => $fuel, 'packagingCharges' => $packaging, 'discountCharges' => $discount, 'sale_person' => $sale_person, 'packaging_material_types' => $packaging_material_types, 'packaging_type_ids' => $packaging_type_ids, 'packaging_charges' => $packaging_charges, 'wms_user_info' => $wms_user_info, 'wms_product_charges' => $wms_product_charges, 'wms_square_foot_charges' => $wms_square_foot_charges, 'wms_packing_charges' => $wms_packing_charges, 'wms_labelling_charges' => $wms_labelling_charges, 'wms_storage_charges' => $wms_storage_charges, 'invoicing_cycles' => $invoicing_cycles, 'storage_types' => $storage_types, 'rate_remarks' => $rate_remarks, 'overnight_origins' => $overnight_origins, 'overland_origins' => $overland_origins, 'detain_origins' => $detain_origins, 'sameday_origins' => $sameday_origins,'overnight_destinations' => $overnight_destinations, 'overland_destinations' => $overland_destinations, 'detain_destinations' => $detain_destinations, 'sameday_destinations' => $sameday_destinations, 'cities' => $cities,'discount_weight_rates'=>$discount_weight_rates, 'sms_charges' => $sms_charge]);

            }
        }

    }

//    public function statistics_search(Request $request){
//        $graph = array();
//        $destination = $request->destination;
//        $current_date = $request->current_date;
//        $old_date = $request->old_date;
//        $date = $old_date;
//        $dates = array();
//        $dates[] = $date;
//        while ($date != $current_date) {
//            $date = date('Y-m-d H:i:s', strtotime($date . ' +1 day'));
//            $dates[] = $date;
//        }
//        foreach ($dates as $this_date) {
//            $comparison_date = $this_date;
//            if($destination == ''){
//                $graph['dates'][] = Carbon::parse($this_date)->format('d M');
//                $graph['booked'][] = Shipment::whereDate('created_at', $comparison_date)->where('user_id', session('user_id'))->where('shipper_status_id',1)->count();
//                $graph['received'][] = Shipment::whereDate('created_at', $comparison_date)->where('user_id', session('user_id'))->whereIn('shipper_status_id',[2,3,4])->count();
//                $graph['delivered'][] = Shipment::whereDate('created_at', $comparison_date)->where('user_id', session('user_id'))->whereIn('shipper_status_id',[14,16, 30, 36,37,39,40,41,47])->count();
//                $graph['pending'][] = Shipment::whereDate('created_at', $comparison_date)->where('user_id', session('user_id'))->whereIn('shipper_status_id',[5,6,7,8,9,10,11,12,13,15,18,19])->count();
//                $graph['return'][] = Shipment::whereDate('created_at', $comparison_date)->where('user_id', session('user_id'))->whereIn('shipper_status_id',[20,21,22,23,24,25,26,27,28,29,31,32,33,34,35,38,42,43,44,45,46])->count();
//            }else{
//                $graph['dates'][] = Carbon::parse($this_date)->format('d M');
//                $graph['booked'][] = Shipment::whereDate('created_at', $comparison_date)->where(['user_id'=> session('user_id'),'consignee_city_id'=>$destination,'shipper_status_id'=>1])->count();
//                $graph['received'][] = Shipment::whereDate('created_at', $comparison_date)->where(['user_id'=> session('user_id'),'consignee_city_id'=>$destination])->whereIn('shipper_status_id',[2,3,4])->count();
//                $graph['delivered'][] = Shipment::whereDate('created_at', $comparison_date)->where(['user_id'=> session('user_id'),'consignee_city_id'=>$destination])->whereIn('shipper_status_id',[14,16, 30, 36,37,39,40,41,47])->count();
//                $graph['pending'][] = Shipment::whereDate('created_at', $comparison_date)->where(['user_id'=> session('user_id'),'consignee_city_id'=>$destination])->whereIn('shipper_status_id',[5,6,7,8,9,10,11,12,13,15,18,19])->count();
//                $graph['return'][] = Shipment::whereDate('created_at', $comparison_date)->where(['user_id'=> session('user_id'),'consignee_city_id'=>$destination])->whereIn('shipper_status_id',[20,21,22,23,24,25,26,27,28,29,31,32,33,34,35,38,42,43,44,45,46])->count();
//            }
//
//        }
//        return response()->json(['status'=>1,'graph'=>$graph]);
//    }


    public function contacts(){
        $sale_person = SalePersonTag::where('user_id', session('user_id'))->where('status', 0)->first();
        $admin = Admin::find($sale_person->admin_id);
        $contacts = ShipperContact::where('shipper_id', session('user_id'));
        if($contacts->exists()){
            $contacts = $contacts->get();
        }else{
            $contacts = null;
        }
        return view('client.profile.contacts')->with(['sale_person' => $admin, 'contacts' => $contacts]);
    }

    public function update_invoice_sort(Request $request){
        $id = session('user_id');
        $user = User::find($id);
        if($user){
            $action = $request->action;

            if($action == 'true'){
                if($user->invoice_group_by == 0){
                    $user->invoice_group_by = 1;
                    $user->save();
                    return response()->json(['status' => 1, 'success'=>'Invoice successfully updated!']);
                }else{
                    return response()->json(['status' => 0, 'error'=>'Invoice already updated']);
                }
            }
            else{
                if($user->invoice_group_by == 1){
                    $user->invoice_group_by = 0;
                    $user->save();
                    return response()->json(['status' => 1, 'success'=>'Invoice successfully updated!']);
                }else{
                    return response()->json(['status' => 0, 'error'=>'Invoice already updated']);
                }
            }
        }

    }

    public function shipper_phone_unique(Request $request) {
        if ($request->filled('phone')) {

            if($request->has('type')) {
                $user = WalletUser::where('phone', $request->input('phone'));

                if ($request->has('id')) {
                    $user = $user->where('user_id', '!=', $request->input('id'));
                }

                if (!$user->exists()) {
                    return 'true';
                }
                else {
                    return 'false';
                }
            } else {
                $user = User::where('phone', $request->input('phone'));

                if ($request->has('id')) {
                    $user = $user->where('id', '!=', $request->input('id'));
                }

                if (!$user->exists()) {
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

    public function quick_search_index() {
        return view('client.quick_search');
    }

    public function quick_search_list(Request $request) {
         if (!in_array(session('user_id'), [167, 1159, 2035, 3324, 4740, 4758, 5982, 10104, 14110, 7762])) {
            $connection = 'reports';
         }
         else {
             $connection = 'mysql';
         }

        $date = Carbon::now()->subMonths(6)->startOfDay()->toDateTimeString();

        $starting_id = DB::connection($connection)->table('shipments')->where('created_at', '>=', $date)->first()->id;

        $shipments = DB::connection($connection)->table('shipments')->join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->leftJoin('shipping_modes as sm','sm.id','=','shipments.shipping_mode_id')
            ->leftJoin('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->leftJoin('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                    ->where('shipments_journey.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.verification = 1)'));
            })
            ->leftJoin('shipment_status as ss','ss.id','=','shipments_journey.shipper_status_id')
            ->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'shipments_journey.status_reason_id')
            ->select(['u.name as user_name','shipments.tracking_number as tracking_number','shipments.order_id','bt.booking_type as service_type','ss.name as status','oc.name as origin','dc.name as destination','shipments.consignee_name','shipments.consignee_phone_number_1 as phone1','shipments.consignee_phone_number_2 as phone2','shipments.consignee_address','shipments.amount','shipments.created_at as booking_date','ssr.name as reason', 'shipments.id as shipment_id'])
            ->where('shipments.id', '>=', $starting_id);

        $shipments = $shipments->where(function ($query) {
            $query->where('shipments.user_id', session('user_id'))
                ->orwhereIn('shipments.user_id', session('sister_users'));
            });

        if (session('user_type') == 2) {
            if (session('restriction') == 1) {
                $shipments = $shipments->join('substitute_user_shipments as sus', function($join) {
                    $join->on('sus.shipment_id', '=', 'shipments.id')
                        ->where('sus.substitute_user_id', '=', Auth::id());
                });
            }
        }

        if ($tracking_number = $request->get('tracking_number')) {
            $shipments->where('shipments.tracking_number', $tracking_number);
        }

        if ($phone_number = $request->get('phone_number')) {
            $shipments->where('shipments.consignee_phone_number_1', $phone_number);
        }

        if ($order_id = $request->get('order_id')) {
            $shipments->where('shipments.order_id',$order_id);
        }

        if (!$request->get('tracking_number') && !$request->get('phone_number') && !$request->get('order_id')) {
            $shipments->whereRaw('FALSE');
        }

        $datatable = Datatables::of($shipments)
            ->editColumn('tracking_number', function ($shipments) {
                $route = route('cod.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('phone', function ($shipments){
                $phone = $shipments->phone1;

                if ($shipments->phone2) {
                    $phone .= ' / ' . $shipments->phone2;
                }

                return $phone;
            })
            ->editColumn('amount', function($shipment){
                return number_format($shipment->amount);
            });



        return $datatable->rawColumns(['tracking_number'])->make(true);
    }

    public function agreement_status(Request $request){
        if(session()->has('agreement_signed') && session('agreement_signed') != 1){
            $encoded_image = explode(",", $request->esign)[1];
            $decoded_image = base64_decode($encoded_image);

            $user_attachment = UserDocumentAttachment::where('user_id',session('user_id'));
            if($user_attachment->exists())
            {
                $user_attachment = $user_attachment->first();
            }
            else{
                $user_attachment = new UserDocumentAttachment();
                $user_attachment->user_id = session('user_id');

            }

            $date = Carbon::now()->format('Y_m_d');

            

            if($user_attachment->e_sign_image != NULL) {
                Storage::disk('public')->delete('users_attached_documents/' . session('user_id') . '/' . $user_attachment->e_sign_image);
            }
            $filename = 'e_sign_image_' . $date . '_' . session('user_id') . '.png';
            Storage::disk('public')->put('users_attached_documents/'. session('user_id') .'/'.$filename, $decoded_image);
            $user_attachment->e_sign_image = $filename;
            $user_attachment->save();
            session(['agreement_signed' => 1]);
            User::where('id',session('user_id'))->update(['agreement_signed' => 1]);


             //Download CRF for new requirement from shipper side

             $user = User::find(session('user_id'));
             if($user->lead_id){
                $this->download_crf($user_attachment, $date);        
             }

            NotificationsController::send(149,session('user_id'));
        }
        return redirect()->back()->with(['success'=>"Agreement Signed Successfully!"]);
    }



    public function get_agreement(Request $request)
    {
        $html = ShipperAgreementController::view_crf_agreement($request->id,null,TRUE);
        return $html;
    }


    public function download_crf($user_attachment, $date)
    {
        try {
            DB::beginTransaction();
            if ($user_attachment->filled_and_signed_pdf != NULL) {
                Storage::disk('public')->delete('users_attached_documents/' . session('user_id') . '/' . $user_attachment->filled_and_signed_pdf);
            }

            if ($user_attachment->signed_acknowledgement_pdf != NULL) {
                    Storage::disk('public')->delete('users_attached_documents/' . session('user_id') . '/' . $user_attachment->signed_acknowledgement_pdf);
            }
        
            $date = now()->format('Ymd_His');

            $filename_signed = 'filled_and_signed_pdf_' . $date . '_' . session('user_id') . '.pdf';
            $filename_acknowleged = 'signed_acknowledgement_pdf_' . $date . '_' . session('user_id') . '.pdf';

            $html = ShipperAgreementController::view_crf_agreement(session('user_id'));
            $pdf = SnappyPDF::loadHTML($html);

            $filePathSigned = 'users_attached_documents/' . session('user_id') . '/' . $filename_signed;
            $filePathAcknowledgement = 'users_attached_documents/' . session('user_id') . '/' . $filename_acknowleged;

            Storage::disk('public')->put($filePathSigned, $pdf->output());
            Storage::disk('public')->put($filePathAcknowledgement, $pdf->output());

            $user_attachment->filled_and_signed_pdf = $filename_signed;
            $user_attachment->signed_acknowledgement_pdf = $filename_acknowleged;

            $user_attachment->save();
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error in handling PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while processing the document.');
        }
    }



    public function rate_daily_visit (Request $request)
    {
        $visit = DailyVisit::where('id',$request->daily_visit_id)->where('shipper_id',session('user_id'));
        if($visit->doesntExist())
        {
            return back()->with(['error'=>'Invalid Request!!']);
        }
        $visit = $visit->first();
        if($visit->rated == 1)
        {
            return back()->with(['error'=>'Visit Already Been Rated.']);
        }

        if($request->action == 2)
        {
            $visit->rating_id = $request->rating;
            $visit->comment = $request->comment;
        }

        $visit->rated = 1;
        $visit->save();

        return back()->with(['success'=>'Visit Rated Successfully']);
    }

    public function mentor_health_index()
    {
        $details = User::join('cities as c','c.id','users.city_id')
        ->where('users.id',session('user_id'))
        ->select('users.id as user_id','users.name as company_name','users.email as company_email',
            'users.phone as company_phone','users.city_id as company_city_id','c.name as company_city');
        if ($details->exists())
        {
            $details = $details->first();
        }
        return view('client.mentor_health.mentor_health')->with(['details' => $details]);
    }

    public function mentor_health_add_request(Request $request)
    {
        if ($request->has('company_name') && $request->has('company_email') && $request->has('company_phone') && $request->has('company_city'))
        {
            if ($request->filled('company_name') && $request->filled('company_email') && $request->filled('company_phone') && $request->filled('company_city')) {
                try
                {
                    $url = 'https://qamhc.thementorhealth.com/api/partner/OnboardCorporate?token=d34931e3fc689a3081a41350fff38a56d8945dc5de92ff127eb706c318324d0b';

                    $verify = true;
                    if(App::environment() == 'local')
                        $verify = false;

                    $client = new Client([
                        'verify' => $verify, // Disable SSL verification
                    ]);

                    $postData = [
                        'full_name' => $request->company_name,
                        'email' => $request->company_email,
                        'phone' => $request->company_phone,
                        'city_name' => $request->company_city,
                    ];

                    $response = $client->post($url, [
                        'json' => $postData, // Send data as JSON
                    ]);

                    $statusCode = $response->getStatusCode();
                    $responseBody = $response->getBody()->getContents();

                    if ($statusCode === 200) {
                        $responseData = json_decode($responseBody, true);
                        if (stripos($responseBody, "This company already exists") !== false) {
                            return back()->with(['info' => $responseData['Message']]);
                        }
                        else
                        {
                            return back()->with(['success' => $responseData['Message']]);
                            //todo need to make a migration in which mentor health data will be store for journey
                        }

                    } else {
                        return back()->with(['error' => 'API request failed']);
                    }
                }
                catch (\Throwable  $e)
                {
                    return back()->with(['error' => $e->getMessage()]);
                }
            }
        }
        else
        {
            return back()->with(['error' => 'please provide the complete details !']);
        }
    }

    public function wordpressLeadRegistration(){
          /*if($lead_id == NULL){
            return redirect()->route('cod.getstarted');
        }*/

        $user = User::find(session('user_id'));
        $lead = Lead::find($user->lead_id);
        $account_type = AccountType::all();
        $products = Product::all();
        $parentProducts = ParentProduct::get();
        $banks = BanksList::all();
        $city_list = City::where('status',1)->where('business_category_id' ,1)->where('id','!=',1244)->get();
        $pickup_city_list = City::where('pickup',1)->where('status',1)->get();
        $references = Reference::all();
        $average_shipment_durations = AverageShipmentCycle::all();
//        $sales_persons = Admin::join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.id', 'admins.name'])->where('admins.status', 1)->where('ar.department_id', 7);
        $segments = Segment::where(function ($query) {
            $query->where('name', 'like', '%E-Commerce%')
                ->orWhere('name', 'like', '%E-Com%');
        })->get() ?? [];
        $sub_segments = SubCategorySegment::where('name', 'like', '%COD%')->get() ?? [];
        $payment_cycles = PaymentCycle::all();
        $admins = Admin::all();

        $average_shipment_duration_weekly = AverageShipmentCycle::where('name', 'like', '%weekly%')->first()->id ?? null;
        $lead_segment = Segment::where(function ($query) {
            $query->where('name', 'like', '%E-Commerce%')
                  ->orWhere('name', 'like', '%E-Com%');
        })->first()->id ?? null;
        $lead_sub_segment = SubCategorySegment::where('name', 'like', '%COD%')->first()->id ?? null;

        // This needs to be modified to reflect the new Logic of Admin able to Select which City has Pickup enabled, which Booking Type is enabled and accordingly which Shipping Mode is enabled. PickupType is no longer valid.
        // $cities = PickupType::find(1)->cities()->orderBy('city_name')->get();
        $invoicing_cycle = InvoicingCycle::all();

        // in case when the if condition fails so to avoid undefined variable errors
        $riders_permanent = collect();
        $weight = collect();

        if (!RateStatus::where('user_id', $user->id)->exists()) {
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
        }

        if($user->on_board_status == 1){
            return view('client.access_denied');
        }else{
            return view('client.wordpress_lead_registeration.index')->with(['payment_cycles'=>$payment_cycles,'products'=>$products,'cities'=>$city_list,'pickup_city_list'=>$pickup_city_list,'all_cities'=>$city_list,'banks'=>$banks,'account_types' => $account_type, 'references' => $references, 'average_shipment_durations' => $average_shipment_durations, 'segments' => $segments,'sub_segments' => $sub_segments, 'lead' => $lead,'invoicing_cycle' => $invoicing_cycle , 'user' => $user, 'riders_permanents'=>$riders_permanent,'shipper' => $user, 'weight' => $weight, 'shippingType' => $bookingType, 'cashHandling' => $cash, 'insuranceCharges' => $insurance, 'returnCharges' => $return, 'fuelCharges' => $fuel, 'packaging_material_types' => $packaging_material_types, 'packaging_material_type_sizes' => $packaging_sizes, 'invoicing_cycles' => $invoicing_cycles, 'storage_types' => $storage_types, 'on' => $on, 'ol' => $ol, 'det' => $det, 'same_day' => $same_day, 'commission_percentage' => $commission_percentage, 'sales_tiers' => $sales_tiers, 'users' => $all_users, 'cities' => $cities,'admins'=>$admins, 'average_shipment_duration_weekly'=> $average_shipment_duration_weekly, 'lead_segment'=> $lead_segment, 'lead_sub_segment'=> $lead_sub_segment, 'parentProducts'=>$parentProducts]);
        }
    }



    public function sales_person(Request $request){

        $id = $request->id;
        if($id){
            $sales_persons_city = City::where('id', $id);
            if ($sales_persons_city->exists()){
                // Check if city exists
                $sales_persons_city = $sales_persons_city->first();
                $zone_id = $sales_persons_city->zone_id; 
                $sale_persons = LeadZone::where(['zone_id' => $zone_id, 'status' => 1])->first(); 
                
                return response()->json(['status' => 0, 'sale_persons' => $sale_persons]);
            }else{
                $sale_person_admin = City::find($id)->name;
                return response()->json(['status' => 1, 'error' => 'No sales person found for the selected city: ' . $sale_person_admin]);
            }
        }
    }

    public function get_sub_segment(Request $request){
        $sub_segments = SubCategorySegment::where('segment_id',$request->segment_id);
        if($sub_segments->exists()){
            $sub_segments = $sub_segments->get();
            return response()->json(['status' => 0, 'sub_segments' => $sub_segments]);
        }else{
            return response()->json(['status' => 1]);
        }
    }

    public function wordpressAddressView(){
        $products = Product::all();

        // This needs to be modified to reflect the new Logic of Admin able to Select which City has Pickup enabled, which Booking Type is enabled and accordingly which Shipping Mode is enabled. PickupType is no longer valid.
        // $cities = PickupType::find(1)->cities()->orderBy('city_name')->get();
        $cities = City::where('pickup',1)->get();
        return view('client.components.pickup_address')->with(['cities'=>$cities,'products'=>$products]);
    }
    public function wordpressBankView(){
        $banks = BanksList::all();
        $city_list = City::where('status',1)->get();
        return view('client.components.banks')->with(['banks'=>$banks,'all_cities'=>$city_list]);
    }

    public function updateCrfSign(Request $request){
        $user = User::find($request->user_id);
        if($user->status == 1){
            session(['status' => 2]);
        }
    }

    public function SignInWalletUser(Request $request)
    {

        if (session('user_type') == 1) {
            $login_request = $request->input('login_request',null);
            if($login_request){
                $api = config('app.FINGA_URL');
                $token = FingaIntegrationController::getToken($api);
                $url = FingaIntegrationController::getLoginUrl($api, $token, $request->phone, $request->cnic, $request->email);
                $dashboardUrl = route('cod.wallet.finja_dashboard', ['url' => $url]);
                return response()->json(['status' => 1, 'redirect_url' => $dashboardUrl]);

            }
        }
    }

    public function updateprofilewalletbulk(Request $request)
    {
        if (session('user_type') == 1) {

            $names = [
                'name' => 'Name ',
                'phone' => 'Phone',
                'cnic' => 'CNIC',
                'email' => 'Email',
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
                'check_duplicate' => 'Phone Or Email Already Exists',
                'check_cnic' => 'Cnic Already Exists',
                'phone' => 'Phone starts with 03 or 923 followed by 9 digits',
                'name' => 'Only alphabetic characters and spaces',
            ];

            $rules = [
                'name' => ['required', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
                'email' => ['required', 'max:255', 'email', 'check_duplicate'],
                'phone' => ['required', 'max:255', 'regex:/^(03|923)[0-9]{2,3}-?[0-9]{7}$/'],
                'cnic' => ['required', 'max:255','check_cnic']
            ];


            Validator::extend('check_duplicate', function ($attribute, $value, $parameters, $validator)  {
                $data = $validator->getData();
                $phone = $data['phone'];
                $email = $data['email'];

                $user = WalletUser::where('email', $email)->orWhere('phone', $phone);
                if($user->exists()) {
                    return FALSE;
                } else{
                    return true;
                }
            });

            Validator::extend('check_cnic', function ($attribute, $value, $parameters, $validator)  {
                $data = $validator->getData();
                $cnic = $data['cnic'];;

                $user = WalletUser::where('cnic', $cnic);
                if($user->exists()) {
                    return FALSE;
                } else{
                    return true;
                }
            });
            $errors = array();
            $data = array();

            $bank = 0;
            foreach ($request->users as $key => $row) {
                $row_id = $row['id'];
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


                } else {
                    $data[$key] = [
                        'name' => $row['name'],
                        'cnic' => $row['cnic'],
                        'phone' => $row['phone'],
                        'email' => $row['email'],
                        'user_id' => session('user_id'),
                        'status' => 1,
                        'substitute_user_id' => isset($row['substitute_user_id']) ? $row['substitute_user_id'] : 0
                    ];
                }
            }
            if(!empty($errors)){
                return response()->json(['status' => 0, 'error' => $errors]);
            }else{

                $api = config('app.FINGA_URL');
                $token = FingaIntegrationController::getToken($api);
                $login_data = collect($data)->first();
                $url = FingaIntegrationController::getLoginUrl($api, $token, $login_data['phone'], $login_data['cnic'], $login_data['email']);
                $finja = FingaIntegrationController::signUp($data);

                if (isset($finja['error'])) {
                    $finjaArray = json_decode(json_encode($finja), true);
                    Log::channel('cronJobLog')->info('s ' . 'finjaArray' . json_encode($finjaArray));
                    // Log::channel('cronJobLog')->info('s ' . 'Api Does Not exists' . json_encode(ApiZongLog::where(['shipment_id' => $findShipmentId, 'call_date_time' => $request->start_date])->doesntExist()));
                    // Log::channel('cronJobLog')->info('s ' . 'OPS LOG' . ApiZongLog::where(['shipment_id' => $findShipmentId, 'call_date_time' => $request->start_date])->doesntExist());

                    $errorMessages = collect($finjaArray['error']['users']);
                    Log::channel('cronJobLog')->info('s ' . 'errorMessages' . json_encode($errorMessages));

                    foreach ($errorMessages as $error_val){
                        $key = array_key_first(array_filter($data, function ($row) use ($error_val) {
                            return $row['phone'] === $error_val['mobile_no'];
                        }));

                        $data[$key]['message'] = $error_val['message'];

                    }
                    return response()->json(['status' => 0, 'error_2' => $data]);
                } else {

                    $final['url'] = $url;
                    //$url = route('cod.wallet.finja_dashboard', ['url' => $finja['url']]);
                    foreach ($data as $key=>$value) {
                        $data[$key]['wallet_id'] = $finja['wallet_id'];
                        $data[$key]['created_at'] = Carbon::now();
                        $data[$key]['updated_at'] = Carbon::now();
                    }
                    WalletUser::wallet_create($data);
                    
                    $hasSubstituteZero = array_filter($data, function ($item) {
                        return isset($item['substitute_user_id']) && $item['substitute_user_id'] == 0;
                    });
                    if (!empty($hasSubstituteZero)) {
                        if(UserBankInfo::where('user_id',session('user_id'))->exists())
                        {
                            UserBankInfo::where('user_id', session('user_id'))->update(['default_bank' => 0]);
                        }
                        $user_city_id = User::where('id', session('user_id'))->value('city_id');
                        $user_bank = new UserBankInfo();
                        $user_bank->user_id = session('user_id');
                        $user_bank->bank_name = 48;
                        $user_bank->bank_branch = 'N/A';
                        $user_bank->account_no = '923322149092';
                        $user_bank->account_title = 'N/A';
                        $user_bank->iban = 'PK06TMFB0000000087042403';
                        $user_bank->city_id =  $user_city_id;
                        $user_bank->default_bank = 1; // always make the new bank info as default
                        $user_bank->save();

                    }
                    WalletSignUpLPendingRecordLogs::dispatch(session('user_id'));
                    return response()->json(['status' => 1, 'output' => $final]);

                }
            }

        }

        return response()->json(['status' => 1, 'success'=>'Profile Information Successfully Updated"']);
    }
}