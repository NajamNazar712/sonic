<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminAppSlider;
use App\Http\Models\Admin\AdminDepartment;
use App\Http\Models\Admin\AdminRole;
use App\Http\Models\Admin\AutoTagTerritory;
use App\Http\Models\Admin\BookingSmsForShippers;
use App\Http\Models\Admin\BusinessProjectionReason;
use App\Http\Models\Admin\BusinessProjectionShipment;
use App\Http\Models\Admin\ByPassWeightShippers;
use App\Http\Models\Admin\CompletedAgingReport;
use App\Http\Models\Admin\CrmAutoTagUser;
use App\Http\Models\Admin\DeliveryLocationMapping;
use App\Http\Models\Admin\DeliveryLocationMappingKeyword;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\Fleet;
use App\Http\Models\Admin\Fuel\FuelFactorHistory;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\Lead\LeadNotification;
use App\Http\Models\Admin\Lead\LeadNotificationAttachment;
use App\Http\Models\Admin\Lead\LeadTagging;
use App\Http\Models\Admin\Lead\LeadZone;
use App\Http\Models\Admin\LostShipmentAdmin;
use App\Http\Models\Admin\LostShipmentShipper;
use App\Http\Models\Admin\MonthClosingStatus;
use App\Http\Models\Admin\MonthClosingType;
use App\Http\Models\Admin\NonServiceArea;
use App\Http\Models\Admin\PendingCashCollectionAgingReport;
use App\Http\Models\Admin\PettyCashAccountHead;
use App\Http\Models\Admin\PettyCashAccountHeadAccountTitle;
use App\Http\Models\Admin\PettyCashAccountTitle;
use App\Http\Models\Admin\PettyCashConsignee;
use App\Http\Models\Admin\PettyCashConsigneeHub;
use App\Http\Models\Admin\RcpTatOption;
use App\Http\Models\Admin\RetailAppSlider;
use App\http\Models\Admin\ReturnReasonMandatoryShipper;
use App\Http\Models\Admin\RouteManagement;
use App\Http\Models\Admin\RouteManagementJunction;
use App\Http\Models\Admin\SalePersonTag;
use App\Http\Models\Admin\SalePersonTarget;
use App\Http\Models\Admin\SalePersonTargetLog;
use App\Http\Models\Admin\SalePersonTargetDelete;
use App\Http\Models\Admin\SalesDesignation;
use App\Http\Models\Admin\SalesDesignationJourney;
use App\Http\Models\Admin\SalesIncentiveDate;
use App\Http\Models\Admin\ShortReceiveReportTimeHubWise;
use App\Http\Models\Admin\StandardWeightCharge;
use App\Http\Models\Admin\Territory;
use App\Http\Models\Admin\VehicleType;
use App\Http\Models\Admin\WalkInInternationalStandardWeightCharge;
use App\Http\Models\Admin\WalkInInternationalStandardWeightChargeHub;
use App\Http\Models\Admin\WalkInStandardWeightCharge;
use App\Http\Models\Blacklist\BlacklistCondition;
use App\Http\Models\Blacklist\BlacklistedConsignee;
use App\Http\Models\Blacklist\BlacklistedConsigneeManuallyBlacklisted;
use App\Http\Models\Blacklist\BlacklistedConsigneeManuallyExcluded;
use App\Http\Models\Blacklist\BlacklistLabeling;
use App\Http\Models\Blacklist\BlacklistLogic;
use App\Http\Models\Blacklist\BlacklistOperation;
use App\Http\Models\Blacklist\BlacklistSetting;
use App\Http\Models\Blacklist\BlacklistSettingCondition;
use App\Http\Models\Blacklist\BlacklistShipmentRange;
use App\Http\Models\Blacklist\ConsigneeInformation;
use App\Http\Models\City;
use App\Http\Models\CorporateDefaultFuelSurcharge;
use App\Http\Models\CorporateDefaultHistoryFuelSurcharge;
use App\Http\Models\CorporateDefaultRateStatus;
use App\Http\Models\CorporateFuelSurcharge;
use App\Http\Models\CorporateRateStatus;
use App\Http\Models\CorporateWeightCharge;
use App\Http\Models\CRM\CrmRequestCaseNature;
use App\Http\Models\CRM\CrmRequestCaseNatureType;
use App\Http\Models\CRM\CrmTatHolidays;
use App\Http\Models\CrmAgent;
use App\Http\Models\DeliveryCallVerificationRatio;
use App\Http\Models\FleetDriver;
use App\Http\Models\FleetVendor;
use App\Http\Models\FuelSurcharge;
use App\Http\Models\Holiday;
use App\Http\Models\InternationalStandardDhlRate;
use App\Http\Models\MultipleSaleLead;
use App\Http\Models\MultipleSaleTagging;
use App\Http\Models\OvernightOverlandReportOriginHubs;
use App\Http\Models\Rates\HistoryCorporateFuelSurcharge;
use App\Http\Models\Rates\HistoryCorporateWeightCharge;
use App\Http\Models\Rates\HistoryFuelSurcharge;
use App\Http\Models\Rates\HistoryWeightCharge;
use App\Http\Models\Rates\MinimumChargeableWeightSetting;
use App\Http\Models\RateStatus;
use App\Http\Models\Referral;
use App\Http\Models\RestrictedCityIntercept;
use App\Http\Models\RestrictParcelsAttempt;
use App\Http\Models\Rider;
use App\Http\Models\Rider\RidersIncentiveSetting;
use App\Http\Models\Rider\RidersShipmentPaymentType;
use App\Http\Models\Rider\RidersShipmentWeightRange;
use App\Http\Models\Rider\RiderTickerImage;
use App\Http\Models\RiderCategory;
use App\Http\Models\Runner;
use App\Http\Models\RunnerJunction;
use App\Http\Models\SaleTierTag;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\ShipmentStatusReason;
use App\Http\Models\Shipper\User;
use App\Http\Models\ShippingMode;
use App\Http\Models\TelenorShipmentStatusEstimatedTime;
use App\Http\Models\Webhook\ShipmentStatusesForShipperWebhook;
use App\Http\Models\Webhook\ShipmentStatusSubscription;
use App\Http\Models\WeightCharge;
use App\Http\Models\WeightChargeFactorHistory;
use App\Http\Models\Zone;
use App\Http\Models\Admin\BookingDestinationMapping;
use App\Http\Models\Admin\BookingDestinationMappingKeyword;
use App\Http\Models\Admin\LeadTaggingService;
use App\Http\Models\ServiceList;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpParser\Node\Expr\Ternary;
use Yajra\Datatables\Datatables;

class GlobalSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function pickup_index()
    {
        $settings = GlobalSettings::where('type', '=', 'pickup_weight')->first();
        return view('admin.settings.pickup')->with('settings', $settings);
    }

    public function add_pickup_weight(Request $request)
    {

        if ($request->isMethod('post')) {
            $result = GlobalSettings::create([
                'setting_value' => $request->pickup_weight,
                'type' => 'pickup_weight',
            ]);
            if ($result) {
                return redirect()->back()->with('success', 'Pickup request weight updated');
            }
        } else {
            $record = GlobalSettings::where('type', 'pickup_weight')->get();
            $result = GlobalSettings::where('id', $record[0]->id)->update([
                'setting_value' => $request->pickup_weight,
                'type' => 'pickup_weight',
            ]);
            if ($result) {
                return redirect()->back()->with('success', 'Pickup request weight updated');
            }
        }
    }

    public function shipment_cancellation_cut_off_days_index()
    {
        $settings = GlobalSettings::where('type', 'shipment_cancellation_cut_off_days')->first();

        return view('admin.settings.shipment_cancellation_cut_off_days')->with('settings', $settings);
    }

    public function shipment_cancellation_cut_off_days_store(Request $request)
    {
        $settings = GlobalSettings::where('type', 'shipment_cancellation_cut_off_days')->first();

        $settings->setting_value = $request->shipment_cancellation_cut_off_days;

        $settings->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function auto_account_disabled_days_index()
    {
        $settings = GlobalSettings::where('type', 'auto_account_disabled_days')->first();

        return view('admin.settings.auto_account_disabled_days')->with('settings', $settings);
    }

    public function auto_account_disabled_days_store(Request $request)
    {
        $settings = GlobalSettings::where('type', 'auto_account_disabled_days')->first();

        $settings->setting_value = $request->auto_account_disabled_days;

        $settings->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function non_service_area_index()
    {
        $current_nsa = NonServiceArea::all();

        if ($current_nsa) {
            $current_nsa = $current_nsa->pluck('name')->toArray();

            $current_nsa = implode(',', $current_nsa);
        }

        return view('admin.settings.non_service_area')->with('current_nsa', $current_nsa);
    }

    public function non_service_area_store(Request $request)
    {
        $new_nsa = explode(',', $request->non_service_areas);

        $current_nsa = NonServiceArea::pluck('name')->toArray();

        $add_nsa = array_diff($new_nsa, $current_nsa);
        $delete_nsa = array_diff($current_nsa, $new_nsa);

        if (!empty($delete_nsa)) {
            NonServiceArea::whereIn('name', $delete_nsa)->delete();
        }

        foreach ($add_nsa as $name) {
            $nsa = new NonServiceArea();

            $nsa->name = $name;

            $nsa->save();
        }

        return redirect()->back()->with('success', 'Non Service Area(s) Updated!');
    }

    public function daily_pickup_sales_cron_index()
    {
        $settings = GlobalSettings::where('type', 'daily_pickup_sales_cron_time')->first();

        return view('admin.settings.daily_pickup_sales_cron_time')->with('settings', $settings);
    }

    public function daily_pickup_sales_cron_store(Request $request)
    {
        $settings = GlobalSettings::where('type', 'daily_pickup_sales_cron_time')->first();

        $settings->setting_value = $request->daily_pickup_sales_cron_time;

        $settings->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function ticker_index()
    {
        $admin_ticker = null;
        $shipper_ticker = null;

        $settings = GlobalSettings::where('type', 'admin_ticker');

        if ($settings->exists()) {
            $settings = $settings->first();

            $ticker = $settings->text;

            if (!empty($ticker)) {
                $admin_ticker = $ticker;
            }
        }

        $settings = GlobalSettings::where('type', 'shipper_ticker');

        if ($settings->exists()) {
            $settings = $settings->first();

            $ticker = $settings->text;

            if (!empty($ticker)) {
                $shipper_ticker = $ticker;
            }
        }

        return view('admin.settings.ticker')->with(['admin_ticker' => $admin_ticker, 'shipper_ticker' => $shipper_ticker]);
    }

    public function ticker_store(Request $request)
    {
        $settings = GlobalSettings::where('type', 'admin_ticker');

        if ($settings->exists()) {
            $settings = $settings->first();
        } else {
            $settings = new GlobalSettings();

            $settings->type = 'admin_ticker';
        }

        $settings->text = ($request->admin_ticker) ? $request->admin_ticker : '';

        $settings->save();

        $settings = GlobalSettings::where('type', 'shipper_ticker');

        if ($settings->exists()) {
            $settings = $settings->first();
        } else {
            $settings = new GlobalSettings();

            $settings->type = 'shipper_ticker';
        }

        $settings->text = ($request->shipper_ticker) ? $request->shipper_ticker : '';

        $settings->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function petty_cash_heads_index()
    {
        return view('admin.settings.petty_cash.account_head');
    }

    public function petty_cash_heads_list(Request $request)
    {
        $heads = PettyCashAccountHead::select('id', 'name', 'status');
        return Datatables::of($heads)
            ->editColumn('status', function ($heads) {
                if ($heads->status == 0) {
                    return 'Inactive';
                } else {
                    return 'Active';
                }
            })
            ->addColumn('action', function ($heads) {
                if (session('role_id') == 1 || count(array_intersect([160, 161, 162], session('permissions'))) !== 0) {

                    $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';
                    if (session('role_id') == 1 || in_array(160, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item edit" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';

                    }
                    if ($heads->status == 1) {
                        if (session('role_id') == 1 || in_array(162, session('permissions'))) {
                            $dropdown .= '<button type="button" class="dropdown-item inactive" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-alert-octagon"></i></div><div class="col-9 offset-1">Inactive</div></button>';
                        } else {
                            $dropdown .= '<button type="button" class="dropdown-item" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x"></i></div><div class="col-9 offset-1">No Action</div></button>';
                        }
                    } else {
                        if (session('role_id') == 1 || in_array(161, session('permissions'))) {
                            $dropdown .= '<button type="button" class="dropdown-item enable" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check"></i></div><div class="col-9 offset-1">Active</div></button>';
                        } else {
                            $dropdown .= '<button type="button" class="dropdown-item" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x"></i></div><div class="col-9 offset-1">No Action</div></button>';
                        }
                    }

                    return $dropdown;
                } else {
                    return '';
                }
            })
            ->make(true);
    }

    public function petty_cash_heads_add(Request $request)
    {
        $head = trim($request->head);
        if ($head) {
            $account_head = new PettyCashAccountHead();
            $account_head->name = $head;
            $account_head->save();

            return response()->json(['status' => 1, 'success' => 'Head of Account successfully added!']);
        } else {
            return response()->json(['status' => 0, 'error' => 'Head of Account is empty']);
        }
    }

    public function petty_cash_heads_edit(Request $request)
    {
        $head = $request->head_id;
        if ($head) {
            $account_head = PettyCashAccountHead::find($head);
            $account_head->name = $request->account_head;
            $account_head->save();

            return response()->json(['status' => 1, 'success' => 'Head of Account successfully updated!']);
        } else {
            return response()->json(['status' => 0, 'error' => 'Head of Account is empty!']);
        }
    }

    public function petty_cash_heads_active(Request $request)
    {
        $head = $request->head_id;
        if ($head) {
            $account_head = PettyCashAccountHead::find($head);
            if ($account_head->status == 0) {
                $account_head->status = 1;
                $account_head->save();
                return response()->json(['status' => 1, 'success' => 'Head of Account successfully activated!']);
            } else {
                return response()->json(['status' => 0, 'error' => 'Head of Account is already active!']);
            }
        } else {
            return response()->json(['status' => 0, 'error' => 'Head of Account is empty!']);
        }
    }

    public function petty_cash_heads_inactive(Request $request)
    {
        $head = $request->head_id;
        if ($head) {
            $account_head = PettyCashAccountHead::find($head);
            if ($account_head->status == 1) {
                $account_head->status = 0;
                $account_head->save();
                return response()->json(['status' => 1, 'success' => 'Head of Account successfully inactivated!']);
            } else {
                return response()->json(['status' => 0, 'error' => 'Head of Account is already inactive!']);
            }

        } else {
            return response()->json(['status' => 0, 'error' => 'Head of Account is empty!']);
        }
    }

    public function petty_cash_titles_index()
    {
        $heads = PettyCashAccountHead::where('status', 1)->get();
        return view('admin.settings.petty_cash.account_title')->with(['heads' => $heads]);
    }

    public function petty_cash_titles_list(Request $request)
    {
        $heads = PettyCashAccountTitle::select('id', 'name', 'status');
        return Datatables::of($heads)
            ->editColumn('status', function ($heads) {
                if ($heads->status == 0) {
                    return 'Inactive';
                } else {
                    return 'Active';
                }
            })
            ->addColumn('action', function ($heads) {
                if (session('role_id') == 1 || count(array_intersect([160, 161, 162], session('permissions'))) !== 0) {
                    $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';
                    if (session('role_id') == 1 || in_array(164, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item edit" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';
                    }
                    if ($heads->status == 1) {
                        if (session('role_id') == 1 || in_array(166, session('permissions'))) {
                            $dropdown .= '<button type="button" class="dropdown-item inactive" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-alert-octagon"></i></div><div class="col-9 offset-1">Inactive</div></button>';
                        } else {
                            $dropdown .= '<button type="button" class="dropdown-item" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x"></i></div><div class="col-9 offset-1">No Action</div></button>';
                        }
                    } else {
                        if (session('role_id') == 1 || in_array(165, session('permissions'))) {

                            $dropdown .= '<button type="button" class="dropdown-item enable" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check"></i></div><div class="col-9 offset-1">Active</div></button>';
                        } else {
                            $dropdown .= '<button type="button" class="dropdown-item" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x"></i></div><div class="col-9 offset-1">No Action</div></button>';

                        }
                    }

                    return $dropdown;
                } else {
                    return '';
                }
            })
            ->make(true);
    }

    public function petty_cash_titles_add(Request $request)
    {
        $title = trim($request->title);
        $heads = array();
        $heads = $request->heads;
        if ($title) {
            $account_title = new PettyCashAccountTitle();
            $account_title->name = $title;
            $account_title->save();
            foreach ($heads as $head) {
                $head_title = new PettyCashAccountHeadAccountTitle();
                $head_title->petty_cash_account_head_id = $head;
                $head_title->petty_cash_account_title_id = $account_title->id;
                $head_title->save();
            }
            return response()->json(['status' => 1, 'success' => 'Head of Account successfully added!']);
        } else {
            return response()->json(['status' => 0, 'error' => 'Head of Account is empty']);
        }
    }

    public function petty_cash_titles_info(Request $request)
    {
        $title_id = $request->title_id;
        $title = PettyCashAccountTitle::find($title_id);
        if ($title) {
            $heads = PettyCashAccountHeadAccountTitle::where('petty_cash_account_title_id', $title_id)->pluck('petty_cash_account_head_id')->toArray();

            return response()->json(['status' => 1, 'heads' => $heads, 'title' => $title]);
        } else {
            return response()->json(['status' => 0, 'error' => 'Title not found!']);
        }

    }

    public function petty_cash_titles_edit(Request $request)
    {
        $title = trim($request->account_title);
        $heads = array();
        $heads = $request->heads;
        $title_id = $request->title_id;
        if (!$title) {
            return response()->json(['status' => 0, 'error' => 'Title not found!']);
        }
        if (!$title_id) {
            return response()->json(['status' => 0, 'error' => 'Title ID not found!']);
        }
        if (empty($heads)) {
            return response()->json(['status' => 0, 'error' => 'Heads not selected!']);
        }
        $title_details = PettyCashAccountTitle::find($title_id);
        $title_details->name = $title;
        $title_details->save();
        PettyCashAccountHeadAccountTitle::where('petty_cash_account_title_id', $title_id)->delete();
        foreach ($heads as $head) {
            $title_heads = new PettyCashAccountHeadAccountTitle();
            $title_heads->petty_cash_account_head_id = $head;
            $title_heads->petty_cash_account_title_id = $title_id;
            $title_heads->save();
        }
        return response()->json(['status' => 1, 'success' => 'Title successfully edited!']);
    }

    public function petty_cash_titles_active(Request $request)
    {
        $title = $request->title_id;
        if ($title) {
            $account_title = PettyCashAccountTitle::find($title);
            if ($account_title->status == 0) {
                $account_title->status = 1;
                $account_title->save();
                return response()->json(['status' => 1, 'success' => 'Title of Account successfully activated!']);
            } else {
                return response()->json(['status' => 0, 'error' => 'Title of Account is already active!']);
            }
        } else {
            return response()->json(['status' => 0, 'error' => 'Title of Account is empty!']);
        }
    }

    public function petty_cash_titles_inactive(Request $request)
    {
        $title = $request->title_id;
        if ($title) {
            $account_title = PettyCashAccountTitle::find($title);
            if ($account_title->status == 1) {
                $account_title->status = 0;
                $account_title->save();
                return response()->json(['status' => 1, 'success' => 'Title of Account successfully inactivated!']);
            } else {
                return response()->json(['status' => 0, 'error' => 'Title of Account is already inactive!']);
            }

        } else {
            return response()->json(['status' => 0, 'error' => 'Title of Account is empty!']);
        }
    }

    public function petty_cash_consignee_index()
    {
        $consignees = User::where([['status', 3], ['blacklist', 0]])->get(['id', 'name']);
        $hubs = City::where('hub', 1)->get(['id', 'name']);
        return view('admin.settings.petty_cash.consignee_settings')->with(['consignees' => $consignees, 'hubs' => $hubs]);
    }

    public function petty_cash_consignee_list()
    {

        $data = PettyCashConsignee::join('cities as hubs', 'hubs.id', '=', 'petty_cash_consignees.hub_id')
            ->select('petty_cash_consignees.hub_id as hub_id', 'petty_cash_consignees.id as id', 'hubs.name as hub_name', 'petty_cash_consignees.consignee_name as consignee_name');
        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                $update_city_url = route('admin.settings.petty_cash.consignee.city.index', $data->id);
                $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

                $dropdown .= '<button type="button" class="dropdown-item edit" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';
                $dropdown .= '<a href="' . $update_city_url . '"><button type="button" class="dropdown-item" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus"></i></div><div class="col-9 offset-1">Update Cities</div></button></a>';

                return $dropdown;

            })
            ->make(true);
    }

    public function petty_cash_consignee_store(Request $request)
    {
        $hub_error = "";
        $hub_error_status = PettyCashConsignee::where('hub_id', $request->hub)->exists();
        if ($hub_error_status) {
            $hub_error = "Please Select a Unique Hub";
        }
        if ($hub_error_status) {
            return response()->json(['status' => 0, 'hub_error' => $hub_error]);
        }

        $table = new PettyCashConsignee();
        $table->consignee_name = $request->consignee;
        $table->hub_id = $request->hub;
        $table->save();

        return response()->json(['status' => 1, 'success' => "Petty Cash Consignee Added Successfully"]);
    }

    public function petty_cash_consignee_edit(Request $request)
    {
        $hub_error = "";
        $hub_error_status = PettyCashConsignee::where([['hub_id', $request->hub], ['id', '!=', $request->id]])->exists();
        if ($hub_error_status) {
            $hub_error = "Please Select a Unique Hub";
        }
        if ($hub_error_status) {
            return response()->json(['status' => 0, 'hub_error' => $hub_error]);
        }

        $table = PettyCashConsignee::find($request->id);
        if (!$table->exists()) {
            return response()->json(['status' => 0, 'error' => "Petty Cash Consignee Information Not Found, Please Try again!"]);
        }

        $table->consignee_name = $request->consignee;
        $table->hub_id = $request->hub;
        $table->update();

        return response()->json(['status' => 1, 'success' => "Petty Cash Consignee Updated Successfully"]);
    }

    public function petty_cash_consignee_city_index($id)
    {
        $cities = City::all(['id', 'name']);
        $consignee = PettyCashConsignee::where('petty_cash_consignees.id', $id)
            ->join('cities as hubs', 'hubs.id', '=', 'petty_cash_consignees.hub_id')
            ->select('hubs.name as hub_name', 'petty_cash_consignees.consignee_name as consignee_name', 'petty_cash_consignees.id as id')
            ->first();
        $petty_cash_cities = PettyCashConsigneeHub::where('petty_cash_consignee_id', $id)->pluck('city_id')->toArray();
        return view('admin.settings.petty_cash.consignee_city_update')->with(['cities' => $cities, 'petty_cash_cities' => $petty_cash_cities, 'consignee' => $consignee]);
    }

    public function petty_cash_consignee_city_check($id, Request $request)
    {
        if ($request->has('cities')) {
            if (count($request->cities) > 0) {
                $cities = $request->cities;
                foreach ($cities as $city_id) {
                    if (PettyCashConsigneeHub::where([['city_id', $city_id], ['petty_cash_consignee_id', '!=', $id]])->exists()) {
                        return response()->json(['error' => 'Select Unique Cities, ' . City::find($city_id)->name . ' is already assigned to consignee']);
                    }
                }
            } else {
                return response()->json(['error' => 'Cities are required']);
            }
        } else {
            return response()->json(['error' => 'Cities are required']);
        }

        return 0;
    }

    public function petty_cash_consignee_city_update($id, Request $request)
    {
        PettyCashConsigneeHub::where('petty_cash_consignee_id', $id)->delete();
        if ($request->has('cities')) {
            if (count($request->cities) > 0) {
                $cities = $request->cities;
                foreach ($cities as $city_id) {
                    $petty_cash_consignee_city = new PettyCashConsigneeHub();
                    $petty_cash_consignee_city->petty_cash_consignee_id = $id;
                    $petty_cash_consignee_city->city_id = $city_id;
                    $petty_cash_consignee_city->save();
                }
            }
        }
        return redirect()->route('admin.settings.petty_cash.consignee.index')->with('success', 'Petty Cash Consignee Cities Updated!');
    }

    public function walk_in_index()
    {
        $walk_in_hub_ol = WalkInStandardWeightCharge::where(['shipping_mode_id' => 2, 'delivery_type_id' => 2])->first();
        $walk_in_hub_on = WalkInStandardWeightCharge::where(['shipping_mode_id' => 1, 'delivery_type_id' => 2])->first();
        $walk_in_hub_dn = WalkInStandardWeightCharge::where(['shipping_mode_id' => 3, 'delivery_type_id' => 2])->first();
        $walk_in_door_ol = WalkInStandardWeightCharge::where(['shipping_mode_id' => 2, 'delivery_type_id' => 1])->first();
        $walk_in_door_on = WalkInStandardWeightCharge::where(['shipping_mode_id' => 1, 'delivery_type_id' => 1])->first();
        $walk_in_door_dn = WalkInStandardWeightCharge::where(['shipping_mode_id' => 3, 'delivery_type_id' => 1])->first();
        return view('admin.settings.walk_in')->with(['walk_in_hub_ol' => $walk_in_hub_ol, 'walk_in_hub_on' => $walk_in_hub_on, 'walk_in_hub_dn' => $walk_in_hub_dn, 'walk_in_door_ol' => $walk_in_door_ol, 'walk_in_door_on' => $walk_in_door_on, 'walk_in_door_dn' => $walk_in_door_dn]);
    }

    public function walk_in_store(Request $request)
    {

        WalkInStandardWeightCharge::where(['shipping_mode_id' => 1, 'delivery_type_id' => 1])->update([
            'actual_weight' => $request->walk_in_door_on_a,
            'chargeable_weight_local' => $request->walk_in_door_on_chargeable_weight_local,
            'chargeable_weight_charges_class_0' => $request->walk_in_door_on_chargeable_weight_class_0_charges,
            'chargeable_weight_charges_class_1' => $request->walk_in_door_on_chargeable_weight_class_1_charges,
            'chargeable_weight_charges_class_2' => $request->walk_in_door_on_chargeable_weight_class_2_charges,
            'chargeable_weight_charges_class_3' => $request->walk_in_door_on_chargeable_weight_class_3_charges,
            'local' => $request->walk_in_door_on_a_local,
            'national_charges_class_0' => $request->walk_in_door_on_return_class_0_charges,
            'national_charges_class_1' => $request->walk_in_door_on_return_class_1_charges,
            'national_charges_class_2' => $request->walk_in_door_on_return_class_2_charges,
            'national_charges_class_3' => $request->walk_in_door_on_return_class_3_charges,
        ]);

        WalkInStandardWeightCharge::where(['shipping_mode_id' => 1, 'delivery_type_id' => 2])->update([
            'actual_weight' => $request->walk_in_hub_on_a,
            'chargeable_weight_local' => $request->walk_in_hub_on_chargeable_weight_local,
            'chargeable_weight_charges_class_0' => $request->walk_in_hub_on_chargeable_weight_class_0_charges,
            'chargeable_weight_charges_class_1' => $request->walk_in_hub_on_chargeable_weight_class_1_charges,
            'chargeable_weight_charges_class_2' => $request->walk_in_hub_on_chargeable_weight_class_2_charges,
            'chargeable_weight_charges_class_3' => $request->walk_in_hub_on_chargeable_weight_class_3_charges,
            'local' => $request->walk_in_hub_on_a_local,
            'national_charges_class_0' => $request->walk_in_hub_on_return_class_0_charges,
            'national_charges_class_1' => $request->walk_in_hub_on_return_class_1_charges,
            'national_charges_class_2' => $request->walk_in_hub_on_return_class_2_charges,
            'national_charges_class_3' => $request->walk_in_hub_on_return_class_3_charges,
        ]);

        WalkInStandardWeightCharge::where(['shipping_mode_id' => 2, 'delivery_type_id' => 1])->update([
            'actual_weight' => $request->walk_in_door_ol_a,
            'chargeable_weight_local' => $request->walk_in_door_ol_chargeable_weight_local,
            'chargeable_weight_charges_class_0' => $request->walk_in_door_ol_chargeable_weight_class_0_charges,
            'chargeable_weight_charges_class_1' => $request->walk_in_door_ol_chargeable_weight_class_1_charges,
            'chargeable_weight_charges_class_2' => $request->walk_in_door_ol_chargeable_weight_class_2_charges,
            'chargeable_weight_charges_class_3' => $request->walk_in_door_ol_chargeable_weight_class_3_charges,
            'local' => $request->walk_in_door_ol_a_local,
            'national_charges_class_0' => $request->walk_in_door_ol_return_class_0_charges,
            'national_charges_class_1' => $request->walk_in_door_ol_return_class_1_charges,
            'national_charges_class_2' => $request->walk_in_door_ol_return_class_2_charges,
            'national_charges_class_3' => $request->walk_in_door_ol_return_class_3_charges,
        ]);

        WalkInStandardWeightCharge::where(['shipping_mode_id' => 2, 'delivery_type_id' => 2])->update([
            'actual_weight' => $request->walk_in_hub_ol_a,
            'chargeable_weight_local' => $request->walk_in_hub_ol_chargeable_weight_local,
            'chargeable_weight_charges_class_0' => $request->walk_in_hub_ol_chargeable_weight_class_0_charges,
            'chargeable_weight_charges_class_1' => $request->walk_in_hub_ol_chargeable_weight_class_1_charges,
            'chargeable_weight_charges_class_2' => $request->walk_in_hub_ol_chargeable_weight_class_2_charges,
            'chargeable_weight_charges_class_3' => $request->walk_in_hub_ol_chargeable_weight_class_3_charges,
            'local' => $request->walk_in_hub_ol_a_local,
            'national_charges_class_0' => $request->walk_in_hub_ol_return_class_0_charges,
            'national_charges_class_1' => $request->walk_in_hub_ol_return_class_1_charges,
            'national_charges_class_2' => $request->walk_in_hub_ol_return_class_2_charges,
            'national_charges_class_3' => $request->walk_in_hub_ol_return_class_3_charges,
        ]);

        WalkInStandardWeightCharge::where(['shipping_mode_id' => 3, 'delivery_type_id' => 1])->update([
            'actual_weight' => $request->walk_in_door_dn_a,
            'chargeable_weight_local' => $request->walk_in_door_dn_chargeable_weight_local,
            'chargeable_weight_charges_class_0' => $request->walk_in_door_dn_chargeable_weight_class_0_charges,
            'chargeable_weight_charges_class_1' => $request->walk_in_door_dn_chargeable_weight_class_1_charges,
            'chargeable_weight_charges_class_2' => $request->walk_in_door_dn_chargeable_weight_class_2_charges,
            'chargeable_weight_charges_class_3' => $request->walk_in_door_dn_chargeable_weight_class_3_charges,
            'local' => $request->walk_in_door_dn_a_local,
            'national_charges_class_0' => $request->walk_in_door_dn_return_class_0_charges,
            'national_charges_class_1' => $request->walk_in_door_dn_return_class_1_charges,
            'national_charges_class_2' => $request->walk_in_door_dn_return_class_2_charges,
            'national_charges_class_3' => $request->walk_in_door_dn_return_class_3_charges,
        ]);

        WalkInStandardWeightCharge::where(['shipping_mode_id' => 3, 'delivery_type_id' => 2])->update([
            'actual_weight' => $request->walk_in_hub_dn_a,
            'chargeable_weight_local' => $request->walk_in_hub_dn_chargeable_weight_local,
            'chargeable_weight_charges_class_0' => $request->walk_in_hub_dn_chargeable_weight_class_0_charges,
            'chargeable_weight_charges_class_1' => $request->walk_in_hub_dn_chargeable_weight_class_1_charges,
            'chargeable_weight_charges_class_2' => $request->walk_in_hub_dn_chargeable_weight_class_2_charges,
            'chargeable_weight_charges_class_3' => $request->walk_in_hub_dn_chargeable_weight_class_3_charges,
            'local' => $request->walk_in_hub_dn_a_local,
            'national_charges_class_0' => $request->walk_in_hub_dn_return_class_0_charges,
            'national_charges_class_1' => $request->walk_in_hub_dn_return_class_1_charges,
            'national_charges_class_2' => $request->walk_in_hub_dn_return_class_2_charges,
            'national_charges_class_3' => $request->walk_in_hub_dn_return_class_3_charges,
        ]);

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function debriefing_report_cut_off_time_index()
    {
        $settings = GlobalSettings::where('type', 'debriefing_report_arrival_cut_off_time')->first();

        if ($settings) {
            $arrival_cut_off_time = $settings->setting_value;
        } else {
            $arrival_cut_off_time = 12;
        }

        $settings = GlobalSettings::where('type', 'debriefing_report_day_cut_off_time')->first();

        if ($settings) {
            $day_cut_off_time = $settings->setting_value;
        } else {
            $day_cut_off_time = 12;
        }

        return view('admin.settings.debriefing_report_cut_off_time')->with(['arrival_cut_off_time' => $arrival_cut_off_time, 'day_cut_off_time' => $day_cut_off_time]);
    }

    public function debriefing_report_cut_off_time_store(Request $request)
    {
        $settings = GlobalSettings::where('type', 'debriefing_report_arrival_cut_off_time');

        if ($settings->exists()) {
            $settings = $settings->first();
        } else {
            $settings = new GlobalSettings();

            $settings->type = 'debriefing_report_arrival_cut_off_time';
        }

        $settings->setting_value = $request->debriefing_report_arrival_cut_off_time;

        $settings->save();

        $start = GlobalSettings::where('type', 'debriefing_report_day_cut_off_time');

        if ($start->exists()) {
            $start = $start->first();
        } else {
            $start = new GlobalSettings();

            $start->type = 'debriefing_report_day_cut_off_time';
        }

        $start->setting_value = $request->debriefing_report_day_cut_off_time;

        $start->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function debriefing_break_time_setting_index()
    {
        $settings = GlobalSettings::where('type', 'debriefing_break_time_setting')->first();
        $total = GlobalSettings::where('type', 'debriefing_total_time_setting')->first();

        $break_timings = 0;
        $total_timings = 0;
        if ($settings) {
            $break_timings = floatval($settings->text);
        }

        if ($total) {
            $total_timings = floatval($total->text);
        }

        return view('admin.settings.debriefing_total_time_setting',compact('break_timings','total_timings'));
    }

    public function debriefing_break_time_setting_store(Request $request)
    {
        $settings = GlobalSettings::where('type', 'debriefing_break_time_setting');

        if ($settings->exists()) {
            $settings = $settings->first();
        } else {
            $settings = new GlobalSettings();

            $settings->type = 'debriefing_break_time_setting';
        }

        $settings->text = $request->break_timings;

        $settings->save();

        $settings = GlobalSettings::where('type', 'debriefing_total_time_setting');

        if ($settings->exists()) {
            $settings = $settings->first();
        } else {
            $settings = new GlobalSettings();

            $settings->type = 'debriefing_total_time_setting';
        }

        $settings->text = $request->total_timings;

        $settings->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function auto_invoice_generation_and_due_date_index()
    {
        $auto_invoice_generation = GlobalSettings::where('type', 'auto_invoice_generation_time')->first();
        $due_date_days = GlobalSettings::where('type', 'due_date_days')->first();
        return view('admin.settings.auto_invoice_generation_and_due_date_index')->with(['auto_invoice_generation_time' => $auto_invoice_generation, 'due_date_days' => $due_date_days]);
    }

    public function auto_invoice_generation_and_due_date_store(Request $request)
    {

        $settings_invoice = GlobalSettings::where('type', 'auto_invoice_generation_time')->first();
        $settings_due_date = GlobalSettings::where('type', 'due_date_days')->first();

        $settings_invoice->setting_value = $request->auto_invoice_generation_hours;
        $settings_due_date->setting_value = $request->due_date_days;

        $settings_invoice->save();
        $settings_due_date->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function fuel_factor_index()
    {
        $shippers = User::where('status', 3)->where('blacklist', 0)->select('id', 'name')->get();
        return view('admin.settings.fuel_factor')->with(['shippers' => $shippers]);
    }

    public function fuel_factor_store(Request $request)
    {
        $include_ids = [];

        $fuel_factor = $request->fuel_factor;

        if ($fuel_factor != null) {
            if ($request->has('all_shippers_checkbox')) {
                $shipping_modes = ShippingMode::all();
                if (!empty($include_ids)) {
                    $users = User::whereIn('id', $include_ids)->get();
                }
                else {
                    $users = User::where('status', 3)->get();
                }
                if (!$users->isEmpty()) {
                    foreach ($users as $user) {
                        foreach ($shipping_modes as $shipping_mode) {
                            if ($user->account_type_id == 1) {
                                $rate_status = RateStatus::where('user_id', $user->id)->where('shipping_mode_id', $shipping_mode->id);
                            } else {
                                if($user->corporate_rate_type_id != 3 && $user->new_rate_type_id == null){
                                    $rate_status = CorporateRateStatus::where('user_id', $user->id)->where('shipping_mode_id', $shipping_mode->id);
                                }
                                else{
                                    $rate_status = CorporateDefaultRateStatus::where('user_id', $user->id)->where('shipping_mode_id', $shipping_mode->id);
                                }
                            }

                            if ($rate_status->exists()) {
                                $rate_status = $rate_status->first();

                                if ($user->account_type_id == 1) {
                                    $fuel_surcharge = FuelSurcharge::where('user_id', $user->id)->where('shipping_mode_id', $shipping_mode->id);
                                } else {
                                    if($user->corporate_rate_type_id != 3 && $user->new_rate_type_id == null) {
                                        $fuel_surcharge = CorporateFuelSurcharge::where('user_id', $user->id)->where('shipping_mode_id', $shipping_mode->id);
                                    }
                                    else{
                                        $fuel_surcharge = CorporateDefaultFuelSurcharge::where('user_id', $user->id)->where('shipping_mode_id', $shipping_mode->id);
                                    }
                                }
                                if ($fuel_surcharge->exists()) {
                                    $fuel_surcharge = $fuel_surcharge->first();

                                    if ($rate_status->fuel_charges == 1) {
                                        $update_fuel_surcharge = $fuel_surcharge->fuel_surcharge + $fuel_factor;
                                    } else {
                                        $update_fuel_surcharge = $fuel_factor;

                                        $rate_status->fuel_charges = 1;
                                        $rate_status->save();
                                    }

                                    if ($update_fuel_surcharge >= 0) {
                                        $fuel_surcharge->fuel_surcharge = $update_fuel_surcharge;
                                    } else {
                                        $fuel_surcharge->fuel_surcharge = 0;
                                    }
                                    $fuel_surcharge->save();

                                    if ($user->account_type_id == 1) {
                                        $fuel_surcharge_history = new HistoryFuelSurcharge();
                                    } else {
                                        if($user->corporate_rate_type_id != 3 && $user->new_rate_type_id == null) {
                                            $fuel_surcharge_history = new HistoryCorporateFuelSurcharge();
                                        }
                                        else{
                                            $fuel_surcharge_history = new CorporateDefaultHistoryFuelSurcharge();
                                        }
                                    }

                                    $fuel_surcharge_history->user_id = $user->id;
                                    $fuel_surcharge_history->shipping_mode_id = $shipping_mode->id;
                                    $fuel_surcharge_history->fuel_surcharge = $update_fuel_surcharge;
                                    $fuel_surcharge_history->save();

                                } else {
                                    if($fuel_factor < 0) {
                                        $fuel_factor = 0;
                                    }

                                    $rate_status->fuel_charges = 1;
                                    $rate_status->save();
                                    if ($user->account_type_id == 1) {
                                        $fuel_surcharge = new FuelSurcharge();
                                        $fuel_surcharge->user_id = $user->id;
                                        $fuel_surcharge->shipping_mode_id = $shipping_mode->id;
                                        $fuel_surcharge->fuel_surcharge = $fuel_factor;
                                        $fuel_surcharge->save();
                                    } else {
                                        if($user->corporate_rate_type_id != 3 && $user->new_rate_type_id == null) {
                                            $fuel_surcharge = new CorporateFuelSurcharge();
                                            $fuel_surcharge->user_id = $user->id;
                                            $fuel_surcharge->shipping_mode_id = $shipping_mode->id;
                                            $fuel_surcharge->fuel_surcharge = $fuel_factor;
                                            $fuel_surcharge->save();
                                        }
                                        else{
                                            $fuel_surcharge = new CorporateDefaultFuelSurcharge();
                                            $fuel_surcharge->user_id = $user->id;
                                            $fuel_surcharge->shipping_mode_id = $shipping_mode->id;
                                            $fuel_surcharge->fuel_surcharge = $fuel_factor;
                                            $fuel_surcharge->save();
                                        }
                                    }

                                }
                            }

                        }
                    }

                    $fuel_factor_history = new FuelFactorHistory();
                    $fuel_factor_history->fuel_factor = $fuel_factor;
                    $fuel_factor_history->admin_id = Auth::id();
                    $fuel_factor_history->save();

                    return redirect()->back()->with('success', 'Fuel Factor Updated!');
                } else {
                    return redirect()->back()->with('error', 'Fuel Factor failed to update!');
                }
            } else {
                if (count($request->shippers) > 0) {

                    $shipping_modes = ShippingMode::all();
                    if (!empty($include_ids)) {
                        $users = User::whereIn('id', $request->shippers)->whereIn('id', $include_ids)->get();
                    }
                    else {
                        $users = User::whereIn('id', $include_ids)->get();
                    }
                    if (!$users->isEmpty()) {
                        foreach ($users as $user) {
                            foreach ($shipping_modes as $shipping_mode) {
                                if ($user->account_type_id == 1) {
                                    $rate_status = RateStatus::where('user_id', $user->id)->where('shipping_mode_id', $shipping_mode->id);
                                } else {
                                    if($user->corporate_rate_type_id != 3 && $user->new_rate_type_id == null) {
                                        $rate_status = CorporateRateStatus::where('user_id', $user->id)->where('shipping_mode_id', $shipping_mode->id);
                                    }
                                    else{
                                        $rate_status = CorporateDefaultRateStatus::where('user_id', $user->id)->where('shipping_mode_id', $shipping_mode->id);
                                    }
                                }

                                if ($rate_status->exists()) {
                                    $rate_status = $rate_status->first();

                                    if ($user->account_type_id == 1) {
                                        $fuel_surcharge = FuelSurcharge::where('user_id', $user->id)->where('shipping_mode_id', $shipping_mode->id);
                                    } else {
                                        if($user->corporate_rate_type_id != 3 && $user->new_rate_type_id == null) {
                                            $fuel_surcharge = CorporateFuelSurcharge::where('user_id', $user->id)->where('shipping_mode_id', $shipping_mode->id);
                                        }
                                        else{
                                            $fuel_surcharge = CorporateDefaultFuelSurcharge::where('user_id', $user->id)->where('shipping_mode_id', $shipping_mode->id);
                                        }
                                    }
                                    if ($fuel_surcharge->exists()) {
                                        $fuel_surcharge = $fuel_surcharge->first();

                                        if ($rate_status->fuel_charges == 1) {
                                            $update_fuel_surcharge = $fuel_surcharge->fuel_surcharge + $fuel_factor;
                                        } else {
                                            $update_fuel_surcharge = $fuel_factor;

                                            $rate_status->fuel_charges = 1;
                                            $rate_status->save();
                                        }

                                        if ($update_fuel_surcharge >= 0) {
                                            $fuel_surcharge->fuel_surcharge = $update_fuel_surcharge;
                                        } else {
                                            $fuel_surcharge->fuel_surcharge = 0;
                                        }
                                        $fuel_surcharge->save();

                                        if ($user->account_type_id == 1) {
                                            $fuel_surcharge_history = new HistoryFuelSurcharge();
                                        } else {
                                            if($user->corporate_rate_type_id != 3 && $user->new_rate_type_id == null) {
                                                $fuel_surcharge_history = new HistoryCorporateFuelSurcharge();
                                            }
                                            else{
                                                $fuel_surcharge_history = new CorporateDefaultHistoryFuelSurcharge();
                                            }
                                        }

                                        $fuel_surcharge_history->user_id = $user->id;
                                        $fuel_surcharge_history->shipping_mode_id = $shipping_mode->id;
                                        $fuel_surcharge_history->fuel_surcharge = $update_fuel_surcharge;
                                        $fuel_surcharge_history->save();

                                    } else {
                                        if($fuel_factor < 0) {
                                            $fuel_factor = 0;
                                        }

                                        $rate_status->fuel_charges = 1;
                                        $rate_status->save();
                                        if ($user->account_type_id == 1) {
                                            $fuel_surcharge = new FuelSurcharge();
                                            $fuel_surcharge->user_id = $user->id;
                                            $fuel_surcharge->shipping_mode_id = $shipping_mode->id;
                                            $fuel_surcharge->fuel_surcharge = $fuel_factor;
                                            $fuel_surcharge->save();
                                        } else {
                                            if($user->corporate_rate_type_id != 3 && $user->new_rate_type_id == null) {
                                                $fuel_surcharge = new CorporateFuelSurcharge();
                                                $fuel_surcharge->user_id = $user->id;
                                                $fuel_surcharge->shipping_mode_id = $shipping_mode->id;
                                                $fuel_surcharge->fuel_surcharge = $fuel_factor;
                                                $fuel_surcharge->save();
                                            }
                                            else{
                                                $fuel_surcharge = new CorporateDefaultFuelSurcharge();
                                                $fuel_surcharge->user_id = $user->id;
                                                $fuel_surcharge->shipping_mode_id = $shipping_mode->id;
                                                $fuel_surcharge->fuel_surcharge = $fuel_factor;
                                                $fuel_surcharge->save();
                                            }
                                        }

                                    }
                                }

                            }
                        }

                        $fuel_factor_history = new FuelFactorHistory();
                        $fuel_factor_history->fuel_factor = $fuel_factor;
                        $fuel_factor_history->admin_id = Auth::id();
                        $fuel_factor_history->save();

                        return redirect()->back()->with('success', 'Fuel Factor Updated!');
                    } else {
                        return redirect()->back()->with('error', 'Fuel Factor failed to update!');
                    }
                } else {
                    return redirect()->back()->with('error', 'Shippers not selected!');
                }
            }
        }
    }

    public function return_note_restriction_bypass_index()
    {
        $role_ids = array();

        $settings = GlobalSettings::where('type', 'return_note_restriction_bypass');

        if ($settings->exists()) {
            $settings = $settings->first();
            $role_ids = array_map('intval', explode(',', $settings->text));
        }

        $roles = AdminRole::with('department')->where('id', '!=', 1)->get();

        return view('admin.settings.return_note_restriction_bypass')->with(['roles' => $roles, 'role_ids' => $role_ids]);
    }

    public function return_note_restriction_bypass_store(Request $request)
    {
        if ($request->has('roles')) {
            $roles = implode(',', $request->roles);
            $settings = GlobalSettings::where('type', 'return_note_restriction_bypass');

            if ($settings->exists()) {
                $settings = $settings->first();
            } else {
                $settings = new GlobalSettings();

                $settings->type = 'return_note_restriction_bypass';
                $settings->setting_value = 0;

            }
            $settings->text = $roles;
            $settings->save();
        } else {
            GlobalSettings::where('type', 'return_note_restriction_bypass')->delete();
        }

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function cod_cap_zones_index()
    {
        $class_a = GlobalSettings::where('type', 'cod_cap_for_zone_class_0')->first();
        $class_b = GlobalSettings::where('type', 'cod_cap_for_zone_class_1')->first();
        $class_c = GlobalSettings::where('type', 'cod_cap_for_zone_class_2')->first();
        $class_d = GlobalSettings::where('type', 'cod_cap_for_zone_class_3')->first();

        return view('admin.settings.cod_cap_zone')->with(['class_a' => $class_a, 'class_b' => $class_b, 'class_c' => $class_c, 'class_d' => $class_d]);
    }

    public function cod_cap_zones_update(Request $request)
    {
        if ($request->class_a != null && $request->class_b != null && $request->class_c != null && $request->class_d != null) {
            GlobalSettings::where('type', 'cod_cap_for_zone_class_0')->update([
                'setting_value' => $request->class_a,
            ]);
            GlobalSettings::where('type', 'cod_cap_for_zone_class_1')->update([
                'setting_value' => $request->class_b,
            ]);
            GlobalSettings::where('type', 'cod_cap_for_zone_class_2')->update([
                'setting_value' => $request->class_c,
            ]);
            GlobalSettings::where('type', 'cod_cap_for_zone_class_3')->update([
                'setting_value' => $request->class_d,
            ]);
            return redirect()->back()->with('success', 'Settings Updated!');
        } else {
            return redirect()->back()->with('error', 'Settings can\'t be updated');
        }
    }

    public function ibft_charges_index()
    {
        $settings = GlobalSettings::where('type', 'ibft_charges');

        if ($settings->exists()) {
            $settings = $settings->first();

            $ibft_charges = $settings->setting_value;
        } else {
            $ibft_charges = 0;
        }

        return view('admin.settings.ibft_charges')->with('ibft_charges', $ibft_charges);
    }

    public function ibft_charges_store(Request $request)
    {
        $settings = GlobalSettings::where('type', 'ibft_charges');

        if ($settings->exists()) {
            $settings = $settings->first();
        } else {
            $settings = new GlobalSettings();

            $settings->type = 'ibft_charges';
        }

        $settings->setting_value = $request->ibft_charges;

        $settings->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function weight_factor_index(Request $request)
    {
        $settings = GlobalSettings::where('type', 'weight_charges_factor')->first();
        $weight_factor = '';
        if ($settings) {
            $weight_factor = $settings->setting_value;
        }
        $shippers = User::where('status', 3)->where('blacklist', 0)->select('id', 'name')->get();
        return view('admin.settings.weight_factor')->with(['weight_factor' => $weight_factor, 'shippers' => $shippers]);
    }

    public function weight_factor_update(Request $request)
    {

        $weight_factor = $request->weight_factor;
        if ($weight_factor != null) {
            if ($request->has('all_shippers_checkbox')) {

                $settings = GlobalSettings::where('type', 'weight_charges_factor');
                if ($settings->exists()) {
                    $settings = $settings->first();
                    $settings->setting_value = $weight_factor;
                    $settings->save();
                } else {
                    $global_settings = new GlobalSettings();
                    $global_settings->setting_value = $weight_factor;
                    $global_settings->type = 'weight_charges_factor';
                    $global_settings->save();
                }
                $this->weight_factor_account_charges_update($weight_factor, null);

                return redirect()->back()->with('success', 'Weight Charges Factor is Updated!');
            } else {

                $settings = GlobalSettings::where('type', 'weight_charges_factor');
                if ($settings->exists()) {
                    $settings = $settings->first();
                    $settings->setting_value = $weight_factor;
                    $settings->save();
                } else {
                    $global_settings = new GlobalSettings();
                    $global_settings->setting_value = $weight_factor;
                    $global_settings->type = 'weight_charges_factor';
                    $global_settings->save();
                }
                $this->weight_factor_account_charges_update($weight_factor, $request->shippers);

                return redirect()->back()->with('success', 'Weight Charges Factor is Updated!');
            }

        }
        return redirect()->back()->with('error', 'Settings can\'t be updated');

    }

    public function weight_factor_account_charges_update($weight_factor, $shippers = null)
    {
        $shipping_modes = ShippingMode::all();
        if ($shippers == null) {
            $users = User::where('status', 3)->select('id', 'account_type_id')->get();
            if (!$users->isEmpty()) {
                foreach ($users as $user) {
                    foreach ($shipping_modes as $shipping_mode) {
                        if ($user->account_type_id == 1) {
                            $rate_status = RateStatus::where('user_id', $user->id)->where('shipping_mode_id', $shipping_mode->id);
                        } else {
                            $rate_status = CorporateRateStatus::where('user_id', $user->id)->where('shipping_mode_id', $shipping_mode->id);
                        }

                        if ($rate_status->exists()) {

                            if ($user->account_type_id == 1) {
                                $weight_charge = WeightCharge::where('user_id', $user->id)->where('shipping_mode_id', $shipping_mode->id);
                            } else {
                                $weight_charge = CorporateWeightCharge::where('user_id', $user->id)->where('shipping_mode_id', $shipping_mode->id);
                            }
                            if ($weight_charge->exists()) {
                                $weight_charges = $weight_charge->get();

                                foreach ($weight_charges as $charge) {
                                    $local_or_6hr = self::calculate_weight_charges_factor($charge->local_or_6hr);
                                    $national_charges_class_0 = self::calculate_weight_charges_factor($charge->national_charges_class_0);

                                    if (strpos($charge->national_charges_class_1, '%') == false) {
                                        $national_charges_class_1 = self::calculate_weight_charges_factor($charge->national_charges_class_1);
                                    } else {
                                        $national_charges_class_1 = $charge->national_charges_class_1;
                                    }

                                    if (strpos($charge->national_charges_class_2, '%') == false) {
                                        $national_charges_class_2 = self::calculate_weight_charges_factor($charge->national_charges_class_2);
                                    } else {
                                        $national_charges_class_2 = $charge->national_charges_class_2;
                                    }

                                    if (strpos($charge->national_charges_class_3, '%') == false) {
                                        $national_charges_class_3 = self::calculate_weight_charges_factor($charge->national_charges_class_3);
                                    } else {
                                        $national_charges_class_3 = $charge->national_charges_class_3;
                                    }

                                    if ($user->account_type_id == 1) {
                                        $weight_charge_history = new HistoryWeightCharge();
                                        $weight_charge_history->user_id = $charge->user_id;
                                        $weight_charge_history->shipping_mode_id = $charge->shipping_mode_id;
                                        $weight_charge_history->range_up = $charge->range_up;
                                        $weight_charge_history->range_down = $charge->range_down;
                                        $weight_charge_history->weight_addition = $charge->weight_addition;
                                        $weight_charge_history->spkg = $charge->spkg;
                                        $weight_charge_history->local_or_6hr = $charge->local_or_6hr;
                                        $weight_charge_history->national_charges_class_0 = $charge->national_charges_class_0;
                                        $weight_charge_history->national_charges_class_1 = $charge->national_charges_class_1;
                                        $weight_charge_history->national_charges_class_2 = $charge->national_charges_class_2;
                                        $weight_charge_history->national_charges_class_3 = $charge->national_charges_class_3;
                                        $weight_charge_history->save();

                                        WeightCharge::where('id', $charge->id)->update(['local_or_6hr' => $local_or_6hr, 'national_charges_class_0' => $national_charges_class_0, 'national_charges_class_1' => $national_charges_class_1, 'national_charges_class_2' => $national_charges_class_2, 'national_charges_class_3' => $national_charges_class_3]);

                                    } else {
                                        $weight_charge_history = new HistoryCorporateWeightCharge();
                                        $weight_charge_history->user_id = $charge->user_id;
                                        $weight_charge_history->shipping_mode_id = $charge->shipping_mode_id;
                                        $weight_charge_history->delivery_type_id = $charge->delivery_type_id;
                                        $weight_charge_history->range_up = $charge->range_up;
                                        $weight_charge_history->range_down = $charge->range_down;
                                        $weight_charge_history->local_or_6hr = $charge->local_or_6hr;
                                        $weight_charge_history->national_charges_class_0 = $charge->national_charges_class_0;
                                        $weight_charge_history->national_charges_class_1 = $charge->national_charges_class_1;
                                        $weight_charge_history->national_charges_class_2 = $charge->national_charges_class_2;
                                        $weight_charge_history->national_charges_class_3 = $charge->national_charges_class_3;
                                        $weight_charge_history->save();
                                        CorporateWeightCharge::where('id', $charge->id)->update(['local_or_6hr' => $local_or_6hr, 'national_charges_class_0' => $national_charges_class_0, 'national_charges_class_1' => $national_charges_class_1, 'national_charges_class_2' => $national_charges_class_2, 'national_charges_class_3' => $national_charges_class_3]);

                                    }

                                }

                            }
                        }

                    }
                }

                $weight_factor_history = new WeightChargeFactorHistory();
                $weight_factor_history->weight_factor = $weight_factor;
                $weight_factor_history->admin_id = Auth::id();
                $weight_factor_history->save();

            }
        } else {
            $users = User::whereIn('id', $shippers)->select('id', 'account_type_id')->get();
            if (!$users->isEmpty()) {
                foreach ($users as $user) {
                    foreach ($shipping_modes as $shipping_mode) {
                        if ($user->account_type_id == 1) {
                            $rate_status = RateStatus::where('user_id', $user->id)->where('shipping_mode_id', $shipping_mode->id);
                        } else {
                            $rate_status = CorporateRateStatus::where('user_id', $user->id)->where('shipping_mode_id', $shipping_mode->id);
                        }
                        if ($rate_status->exists()) {

                            if ($user->account_type_id == 1) {
                                $weight_charge = WeightCharge::where('user_id', $user->id)->where('shipping_mode_id', $shipping_mode->id);
                            } else {
                                $weight_charge = CorporateWeightCharge::where('user_id', $user->id)->where('shipping_mode_id', $shipping_mode->id);
                            }
                            if ($weight_charge->exists()) {
                                $weight_charges = $weight_charge->get();

                                foreach ($weight_charges as $charge) {
                                    $local_or_6hr = self::calculate_weight_charges_factor($charge->local_or_6hr);
                                    $national_charges_class_0 = self::calculate_weight_charges_factor($charge->national_charges_class_0);

                                    if (strpos($charge->national_charges_class_1, '%') == false) {
                                        $national_charges_class_1 = self::calculate_weight_charges_factor($charge->national_charges_class_1);
                                    } else {
                                        $national_charges_class_1 = $charge->national_charges_class_1;
                                    }

                                    if (strpos($charge->national_charges_class_2, '%') == false) {
                                        $national_charges_class_2 = self::calculate_weight_charges_factor($charge->national_charges_class_2);
                                    } else {
                                        $national_charges_class_2 = $charge->national_charges_class_2;
                                    }

                                    if (strpos($charge->national_charges_class_3, '%') == false) {
                                        $national_charges_class_3 = self::calculate_weight_charges_factor($charge->national_charges_class_3);
                                    } else {
                                        $national_charges_class_3 = $charge->national_charges_class_3;
                                    }

                                    if ($user->account_type_id == 1) {
                                        $weight_charge_history = new HistoryWeightCharge();
                                        $weight_charge_history->user_id = $charge->user_id;
                                        $weight_charge_history->shipping_mode_id = $charge->shipping_mode_id;
                                        $weight_charge_history->range_up = $charge->range_up;
                                        $weight_charge_history->range_down = $charge->range_down;
                                        $weight_charge_history->weight_addition = $charge->weight_addition;
                                        $weight_charge_history->spkg = $charge->spkg;
                                        $weight_charge_history->local_or_6hr = $charge->local_or_6hr;
                                        $weight_charge_history->national_charges_class_0 = $charge->national_charges_class_0;
                                        $weight_charge_history->national_charges_class_1 = $charge->national_charges_class_1;
                                        $weight_charge_history->national_charges_class_2 = $charge->national_charges_class_2;
                                        $weight_charge_history->national_charges_class_3 = $charge->national_charges_class_3;
                                        $weight_charge_history->save();

                                        WeightCharge::where('id', $charge->id)->update(['local_or_6hr' => $local_or_6hr, 'national_charges_class_0' => $national_charges_class_0, 'national_charges_class_1' => $national_charges_class_1, 'national_charges_class_2' => $national_charges_class_2, 'national_charges_class_3' => $national_charges_class_3]);

                                    } else {
                                        $weight_charge_history = new HistoryCorporateWeightCharge();
                                        $weight_charge_history->user_id = $charge->user_id;
                                        $weight_charge_history->shipping_mode_id = $charge->shipping_mode_id;
                                        $weight_charge_history->delivery_type_id = $charge->delivery_type_id;
                                        $weight_charge_history->range_up = $charge->range_up;
                                        $weight_charge_history->range_down = $charge->range_down;
                                        $weight_charge_history->local_or_6hr = $charge->local_or_6hr;
                                        $weight_charge_history->national_charges_class_0 = $charge->national_charges_class_0;
                                        $weight_charge_history->national_charges_class_1 = $charge->national_charges_class_1;
                                        $weight_charge_history->national_charges_class_2 = $charge->national_charges_class_2;
                                        $weight_charge_history->national_charges_class_3 = $charge->national_charges_class_3;
                                        $weight_charge_history->save();
                                        CorporateWeightCharge::where('id', $charge->id)->update(['local_or_6hr' => $local_or_6hr, 'national_charges_class_0' => $national_charges_class_0, 'national_charges_class_1' => $national_charges_class_1, 'national_charges_class_2' => $national_charges_class_2, 'national_charges_class_3' => $national_charges_class_3]);

                                    }

                                }

                            }

                        }

                    }
                }

                $weight_factor_history = new WeightChargeFactorHistory();
                $weight_factor_history->weight_factor = $weight_factor;
                $weight_factor_history->admin_id = Auth::id();
                $weight_factor_history->save();

            }
        }

    }

    private function calculate_weight_charges_factor($charges)
    {
        if ($charges != 0) {
            $weight_factor = GlobalSettings::where('type', 'weight_charges_factor');
            if ($weight_factor->exists()) {
                $weight_factor = $weight_factor->first();
                $weight_factor_percentage = (floatval($weight_factor->setting_value) / 100) * $charges;
                $charges += $weight_factor_percentage;
                return ROUND($charges, 0, PHP_ROUND_HALF_DOWN);
            } else {
                return $charges;
            }
        } else {
            return $charges;
        }
    }

    public function stock_movement_index(Request $request)
    {
        $settings = GlobalSettings::where('type', 'packaging_material_stock_movement_account_id')->first();
        $account_id = '';
        $account_name = '';
        if ($settings) {
            $account_id = $settings->setting_value;
            $account_name = User::find($account_id)->name;
        }
        return view('admin.settings.stock_movement')->with(['account_id' => $account_id, 'account_name' => $account_name]);
    }

    public function stock_movement_update(Request $request)
    {

        $stock_movement_account_id = $request->stock_movement_account_id;
        if ($stock_movement_account_id != null) {
            $settings = GlobalSettings::where('type', 'packaging_material_stock_movement_account_id');
            if ($settings->exists()) {
                $settings = $settings->first();
                $settings->setting_value = $stock_movement_account_id;
                $settings->save();
            } else {
                $global_settings = new GlobalSettings();
                $global_settings->setting_value = $stock_movement_account_id;
                $global_settings->type = 'packaging_material_stock_movement_account_id';
                $global_settings->save();

            }

            return redirect()->back()->with('success', 'Packaging Material Stock Movement Account Updated!');

        }
        return redirect()->back()->with('error', 'Settings can\'t be updated');

    }

    public function delivery_call_verification_ratio_index()
    {
        $settings = DeliveryCallVerificationRatio::get();
        return view('admin.settings.delivery_call_verification_ratio')->with(['settings' => $settings]);
    }

    public function delivery_call_verification_ratio_update(Request $request)
    {
//        dd($request);
        $settings = DeliveryCallVerificationRatio::truncate();
        foreach ($request->verification as $index => $call_verification) {
            $new_ratios = new DeliveryCallVerificationRatio();
            $new_ratios->id = $index;
            $new_ratios->min = $request->min[$index];
            $new_ratios->max = $request->max[$index];
            $new_ratios->verification = $call_verification;
            $new_ratios->save();
        }
        $new_ratios = new DeliveryCallVerificationRatio();
        return redirect()->back()->with('success', 'Call verification ratio is Updated Successfully!');
    }

    public function return_confirmation_pending_shipment_selection_time_index()
    {
        $settings = GlobalSettings::where('type', 'return_confirmation_pending_shipment_selection_time')->first();

        if ($settings) {
            $return_confirmation_pending_shipment_selection_time = $settings->setting_value;
        } else {
            $return_confirmation_pending_shipment_selection_time = 0;
        }

        return view('admin.settings.return_confirmation_pending_shipment_selection_time')->with(['return_confirmation_pending_shipment_selection_time' => $return_confirmation_pending_shipment_selection_time]);
    }

    public function return_confirmation_pending_shipment_selection_time_store(Request $request)
    {
        $settings = GlobalSettings::where('type', 'return_confirmation_pending_shipment_selection_time');

        if ($settings->exists()) {
            $settings = $settings->first();
        } else {
            $settings = new GlobalSettings();

            $settings->type = 'return_confirmation_pending_shipment_selection_time';
        }

        $settings->setting_value = $request->return_confirmation_pending_shipment_selection_time;

        $settings->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function consolidation_max_shipments_index()
    {
        $settings = GlobalSettings::where('type', 'maximum_consolidation_shipments')->first();
        return view('admin.settings.max_consolidation_shipments')->with(['settings' => $settings]);
    }

    public function consolidation_max_shipments_update(Request $request)
    {
        $settings = GlobalSettings::where('type', 'maximum_consolidation_shipments')->first();

        $settings->setting_value = $request->max_shipments;

        $settings->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function crm_case_nature_types_index()
    {
        $case_nature = CrmRequestCaseNature::whereNotIn('id', [3])->select(['id', 'name'])->get();

        return view('admin.settings.add_case_nature_types')->with(['case_nature' => $case_nature]);
    }

    public function crm_case_nature_types_list(Request $request)
    {
        $case_nature_types = CrmRequestCaseNatureType::leftjoin('crm_request_case_nature as crcs', 'crcs.id', '=', 'crm_request_case_nature_types.nature_id')
            ->select('crm_request_case_nature_types.id','crcs.name as case_nature', 'crm_request_case_nature_types.type as case_nature_type', 'crm_request_case_nature_types.status_id as status')
            ->where('crm_request_case_nature_types.id','<>',34);
        $datatable = Datatables::of($case_nature_types)
            ->editColumn('status', function($case_nature_types) {
                if($case_nature_types->status==1){
                    return 'Enable';
                }else{
                    return 'Disable';

                }
            })
            ->addColumn('action', function ($data) {
                if (session('role_id') == 1 || in_array(537, session('permissions'))) {
                    $dropdown = '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">';
                    if ($data->status == 0) {
                        $dropdown .= '<button type="button" class="dropdown-item enable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
                    } else {
                        $dropdown .= '<button type="button" class="dropdown-item disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';
                    }
                    $dropdown .= '
                    </div>
                  </div>
          ';
                }else{
                    $dropdown = '';

                }
                return $dropdown;

            });
        return $datatable->make(true);
    }

    public function crm_case_nature_types_status(Request $request){
        $crm_case_nature_type = CrmRequestCaseNatureType::find($request->id);
        if ($request->status == 1) {
            $crm_case_nature_type->status_id = 1;
            $crm_case_nature_type->save();
            return response()->json(['status' => 1, 'success' => 'Case Nature Type Enabled Successfully']);
        } elseif ($request->status == 0) {
            $crm_case_nature_type->status_id = 0;
            $crm_case_nature_type->save();
            return response()->json(['status' => 1, 'success' => 'Case Nature Type Disabled Successfully']);
        } else {
            return response()->json(['status' => 0, 'error' => 'Invalid Request']);
        }
    }

    public function crm_case_nature_types_store(Request $request)
    {
        $nature = $request->case_nature;
        $type = $request->case_nature_type;
        if ($nature == null && $type == null && $nature == '' && $type == '') {
            if ($nature == null && $nature == '') {
                return response()->json(['status' => 0, 'error' => 'Please select Case Nature!']);
            }
            if ($type == null && $type == '') {
                return response()->json(['status' => 0, 'error' => 'Please enter Case Nature Type!']);
            }
        }
        $case_nature_types = CrmRequestCaseNatureType::where('type', $type);
        if ($case_nature_types->exists()) {
            return response()->json(['status' => 0, 'error' => 'Same Case Nature Type already exists!']);
        } else {
            $new_case_nature_type = new CrmRequestCaseNatureType();
            $new_case_nature_type->nature_id = $nature;
            $new_case_nature_type->type = $type;
            $new_case_nature_type->updated_at = Carbon::now();
            $new_case_nature_type->updated_by = Auth::id();
            $new_case_nature_type->type = $type;
            $new_case_nature_type->save();

            return response()->json(['status' => 1, 'success' => 'New Case Nature Type added successfully!']);
        }
    }

    public function return_delivered_to_shipper_email_cut_off_time_index()
    {

        $settings = GlobalSettings::where('type', 'return_delivered_to_shipper_cut_off_time')->first();

        if ($settings) {
            $rdts_email_cut_off_time = $settings->setting_value;
        } else {
            $rdts_email_cut_off_time = 12;
        }

        return view('admin.settings.return_delivered_to_shipper_cut_off_time')->with(['rdts_email_cut_off_time' => $rdts_email_cut_off_time]);
    }

    public function return_delivered_to_shipper_email_cut_off_time_store(Request $request)
    {
        $settings = GlobalSettings::where('type', 'return_delivered_to_shipper_cut_off_time');

        if ($settings->exists()) {
            $settings = $settings->first();
        } else {
            $settings = new GlobalSettings();

            $settings->type = 'return_delivered_to_shipper_cut_off_time';
        }

        $settings->setting_value = $request->rdts_email_cut_off_time;

        $settings->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function crm_reopen_count_index()
    {
        $settings = GlobalSettings::where('type', 'crm_reopen_count')->first();
        return view('admin.settings.crm_reopen')->with(['settings' => $settings]);
    }

    public function crm_reopen_count_submit(Request $request)
    {
        $settings = GlobalSettings::where('type', 'crm_reopen_count')->first();

        $settings->setting_value = $request->count;
        $settings->text = $request->reopen;

        $settings->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function multiple_sale_tagging_index()
    {    ActivityTrailController::createActivityTrailLog(Auth::id(),360);
        $lead_admins = MultipleSaleLead::select('admin_id')->pluck('admin_id')->toArray();
        $admins = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id')
            ->where('admin_roles.department_id', 7)
            ->where('admin_roles.id', '!=', 4)
            ->where('a.status', 1)
            ->whereNotIn('a.id', $lead_admins)
            ->select('a.id as id', 'a.name as name')
            ->get();
        return view('admin.settings.multiple_sale_person')->with(['admins' => $admins]);
    }

    public function multiple_sale_tagging_list(Request $request)
    {
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),361);
        }
        $multiple_sale_tagging = MultipleSaleLead::leftjoin('admins as a', 'a.id', '=', 'multiple_sale_leads.admin_id')
            ->leftjoin('admins as ua', 'ua.id', '=', 'multiple_sale_leads.updated_by')
            ->leftjoin('multiple_sale_taggings as mst', 'mst.lead_id', '=', 'multiple_sale_leads.id')
            ->select('multiple_sale_leads.id as id', 'a.name as head_admin', 'a.id as head_admin_id', DB::raw('count(mst.id) as tagged_admins'), 'ua.name as updated_by', 'multiple_sale_leads.updated_at as updated_at')
            ->groupBy('a.name');

        return Datatables::of($multiple_sale_tagging)
            ->editColumn('tagged_admins_count', function ($leads) {
                if ($leads->tagged_admins != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle tagged" data-target-id=' . $leads->id . '>' . $leads->tagged_admins . '</button>';
                } else {
                    return 0;
                }
            })
            ->addColumn('action', function ($leads) {
                $dropdown = '
              <div class="btn-group col">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';
                $dropdown .= '<button type="button" data-target-id=' . $leads->head_admin_id . ' class="dropdown-item assign" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus"></i></div><div class="col-9 offset-1">Assign</div></button>';

                return $dropdown;
            })->make(true);
    }

    public function multiple_sale_tagging_submit(Request $request)
    {
        $admin = Admin::find($request->lead);
        $existing_lead = MultipleSaleLead::where('admin_id', $admin->id)->first();
        if (!$existing_lead) {
            $new_lead = new MultipleSaleLead();
            $new_lead->admin_id = $admin->id;
            $new_lead->updated_by = Auth::id();
            $new_lead->save();
            return redirect()->back()->with('success', 'New Lead added successfully!');
        } else {
            return redirect()->back()->with('error', 'Same Lead already Exists!');
        }
    }

    public function multiple_sale_tagging_assign_view(Request $request)
    {
        $tagged_admins = MultipleSaleLead::leftjoin('multiple_sale_taggings as mst', 'mst.lead_id', '=', 'multiple_sale_leads.id')->select('mst.admin_id')->where('multiple_sale_leads.admin_id', $request->head_id)->whereNotNull('mst.admin_id')->pluck('mst.admin_id')->toArray();
        $admins = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id')
            ->where('admin_roles.department_id', 7)
            ->where('admin_roles.id', '!=', 4)
            ->where('a.status', 1)
            ->where('a.id', '!=', $request->head_id)
            ->whereNotIn('a.id', $tagged_admins)
            ->select('a.id as id', 'a.name as name')
            ->get();
//        if($tagged_admins){
        //            $admins->whereNotIn('a.id', $tagged_admins);
        //        }
        if ($admins) {
            return response()->json(['status' => 1, 'admins' => $admins]);
        } else {
            return response()->json(['status' => 0, 'error' => 'No Admins to assign!']);
        }
    }

    public function multiple_sale_tagging_assign_submit(Request $request)
    {
        $lead = MultipleSaleLead::where('admin_id', $request->lead_id)->first();
        $admins = $request->admins;
        if ($lead) {
            if ($admins) {
                $lead->updated_by = Auth::id();
                $lead->save();
                foreach ($admins as $admin_id) {
                    $new_users = new MultipleSaleTagging();
                    $new_users->lead_id = $lead->id;
                    $new_users->admin_id = $admin_id;
                    $new_users->save();
                }
                return redirect()->back()->with('success', 'Users assigned Successfully!');
            } else {
                return redirect()->back()->with('error', 'No Users selected!');
            }
        } else {
            return redirect()->back()->with('error', 'Invalid Lead selected!');
        }
    }

    public function multiple_sale_tagging_assign_view_assigned(Request $request)
    {
        $tagged_users = MultipleSaleTagging::leftjoin('admins as a', 'a.id', '=', 'multiple_sale_taggings.admin_id')->where('lead_id', $request->id)->select('a.name')->pluck('a.name')->toArray();
        return response()->json(['status' => 1, 'tagged_users' => $tagged_users]);
    }

    public function foc_account_index()
    {
        $shippers = User::where('status', 3)->where('blacklist', 0)->select('id', 'name')->get();
        $settings = GlobalSettings::where('type', 'foc_account_tag');
        $foc_account_tags = array();
        if ($settings->exists()) {
            $settings = $settings->first();
            $foc_account_tags = array_map('intval', explode(',', $settings->text));
        }
        return view('admin.settings.foc_account')->with(['shippers' => $shippers, 'foc_account_tags' => $foc_account_tags]);
    }

    public function foc_account_store(Request $request)
    {
        if ($request->has('shippers')) {
            if (count($request->shippers) > 0) {
                $shippers = implode(',', $request->shippers);
                $settings = GlobalSettings::where('type', 'foc_account_tag');

                if ($settings->exists()) {
                    $settings = $settings->first();
                } else {
                    $settings = new GlobalSettings();

                    $settings->type = 'foc_account_tag';
                    $settings->setting_value = 0;

                }
                $settings->text = $shippers;
                $settings->save();
            }
            return redirect()->back()->with('success', 'Settings Updated!');

        } else {
            return redirect()->back()->with('error', 'No shippers selected!');
        }

    }
    public function invoice_against_return_delivered_shipper_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(),532);
        $shippers = User::where('account_type_id','!=',1)->select('id', 'name')->get();
        $settings = GlobalSettings::where('type', 'invoice_against_return_delivered_shipper');
        $tags = array();
        if ($settings->exists()) {
            $settings = $settings->first();
            $tags = array_map('intval', explode(',', $settings->text));
        }
        return view('admin.settings.invoice_against_return_delivered_shipper')->with(['shippers' => $shippers, 'tags' => $tags]);
    }
    public function invoice_against_return_delivered_shipper_store(Request $request)
    {
        if ($request->has('shippers')) {
            if (count($request->shippers) > 0) {
                $shippers = implode(',', $request->shippers);
                $settings = GlobalSettings::where('type', 'invoice_against_return_delivered_shipper');

                if ($settings->exists()) {
                    $settings = $settings->first();
                } else {
                    $settings = new GlobalSettings();

                    $settings->type = 'invoice_against_return_delivered_shipper';
                    $settings->setting_value = 0;

                }
                $settings->text = $shippers;
                $settings->save();
            }
            return redirect()->back()->with('success', 'Settings Updated!');

        } else {
            return redirect()->back()->with('error', 'No shippers selected!');
        }
    }

    public function ccd_booking_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 412);
        $shippers = User::where('status', 3)->where('blacklist', 0)->select('id', 'name')->get();
        $settings = GlobalSettings::where('type', 'ccd_booking');
        $ccd_booking = array();
        if ($settings->exists()) {
            $settings = $settings->first();
            $ccd_booking = array_map('intval', explode(',', $settings->text));
        }
        return view('admin.settings.ccd_booking')->with(['shippers' => $shippers, 'ccd_booking' => $ccd_booking]);
    }

    public function ccd_booking_store(Request $request)
    {
        if ($request->has('shippers')) {
            if (count($request->shippers) > 0) {
                $shippers = implode(',', $request->shippers);
                $settings = GlobalSettings::where('type', 'ccd_booking');

                if ($settings->exists()) {
                    $settings = $settings->first();
                } else {
                    $settings = new GlobalSettings();

                    $settings->type = 'ccd_booking';
                    $settings->setting_value = 0;

                }
                $settings->text = $shippers;
                $settings->save();
            }
            return redirect()->back()->with('success', 'Settings Updated!');

        } else {
            return redirect()->back()->with('error', 'No shippers selected!');
        }

    }

    public function minimum_chargeable_weight_index()
    {
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
        return view('admin.settings.minimum_chargeable_weight')->with(['on' => $on, 'ol' => $ol, 'det' => $det, 'same_day' => $same_day]);
    }

    public function minimum_chargeable_weight_update(Request $request)
    {
        $on = MinimumChargeableWeightSetting::where('shipping_mode_id', 1)->update(['weight' => $request->on]);
        $standard_on = StandardWeightCharge::where('shipping_mode_id', 1)->first();
        $standard_on->range_up = $request->on;
        $standard_on->save();
        $ol = MinimumChargeableWeightSetting::where('shipping_mode_id', 2)->update(['weight' => $request->ol]);
        $standard_ol = StandardWeightCharge::where('shipping_mode_id', 2)->first();
        $standard_ol->range_up = $request->ol;
        $standard_ol->save();
        $detain = MinimumChargeableWeightSetting::where('shipping_mode_id', 3)->update(['weight' => $request->det]);
        $standard_det = StandardWeightCharge::where('shipping_mode_id', 3)->first();
        $standard_det->range_up = $request->det;
        $standard_det->save();
        $same_day = MinimumChargeableWeightSetting::where('shipping_mode_id', 4)->update(['weight' => $request->same_day]);
        $standard_same_day = StandardWeightCharge::where('shipping_mode_id', 4)->first();
        $standard_same_day->range_up = $request->same_day;
        $standard_same_day->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function sales_person_targets()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 50);
        $sales = DB::table('admins')->whereExists(function ($query) {
            $query->from('admin_roles')
                ->where('admins.role_id', '=', DB::raw('`admin_roles`.`id`'))
                ->where('department_id', '=', 7);
        })->select('id', 'name')->where('admins.status', 1)->get();

        $targets = SalePersonTarget::all();
        return view('admin.settings.sales_person.sales_target')->with(['sales_person' => $sales, 'targets' => $targets]);
    }

    public function sales_person_targets_submit(Request $request)
    {
        $start_date = $request->search_date_from_formatted;
        $end_date = Carbon::parse($start_date)->addDays(30)->toDateTimeString();
        if ($start_date == null || $end_date == null) {
            return redirect()->back()->with('error', 'Date not selected!');
        }
        $sales_persons = $request->sales_person;
        if (count($sales_persons) > 0) {
            foreach ($sales_persons as $person) {

                $sales_target = SalePersonTarget::where('sales_person_id', $person);
                if ($sales_target->exists()) {
                    $sales_target = $sales_target->first();

                    $sales_person_log = new SalePersonTargetLog();
                    $sales_person_log->start_date = $sales_target->start_date;
                    $sales_person_log->end_date = $sales_target->end_date;
                    $sales_person_log->sales_person_id = $sales_target->sales_person_id;
                    $sales_person_log->target_days = $sales_target->target_days;
                    $sales_person_log->target_month = $sales_target->target_month;
                    $sales_person_log->average_revenue = $sales_target->average_revenue;
                    $sales_person_log->save();

                    $sales_target->start_date = $start_date;
                    $sales_target->end_date = $end_date;
                    $sales_target->target_days = $request->target_shipment_days;
                    $sales_target->target_month = $request->target_shipment_month;
                    $sales_target->average_revenue = $request->average_revenue;
                    $sales_target->save();

                } else {
                    $sale_person_target = new SalePersonTarget();
                    $sale_person_target->start_date = $start_date;
                    $sale_person_target->end_date = $end_date;
                    $sale_person_target->sales_person_id = $person;
                    $sale_person_target->target_days = $request->target_shipment_days;
                    $sale_person_target->target_month = $request->target_shipment_month;
                    $sale_person_target->average_revenue = $request->average_revenue;
                    $sale_person_target->save();
                }
            }
        }

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function sales_person_targets_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 110);
        }
        $targets = SalePersonTarget::leftjoin('admins as a', 'a.id', '=', 'sale_person_targets.sales_person_id')
            ->select('sale_person_targets.id as id','sale_person_targets.id as target_id', 'sale_person_targets.start_date', 'sale_person_targets.end_date', 'a.name as sales_person', 'sale_person_targets.target_days', 'sale_person_targets.target_month', 'sale_person_targets.average_revenue', DB::raw('(sale_person_targets.target_days*sale_person_targets.average_revenue) as per_day_revenue_target'), DB::raw('(sale_person_targets.target_month*sale_person_targets.average_revenue) as per_month_revenue_target'))->where('a.status', 1);

        $datatable = Datatables::of($targets);
        return $datatable->make(true);
    }

    public function sales_person_targets_history()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 51);
        return view('admin.settings.sales_person.history');
    }

    public function sales_person_targets_history_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 111);
        }
        $targets = SalePersonTargetLog::leftjoin('admins as a', 'a.id', '=', 'sale_person_target_logs.sales_person_id')
            ->select('sale_person_target_logs.id as target_id', 'sale_person_target_logs.start_date', 'sale_person_target_logs.end_date', 'a.name as sales_person', 'sale_person_target_logs.target_days', 'sale_person_target_logs.target_month as target_month', 'sale_person_target_logs.average_revenue', 'sale_person_target_logs.created_at')
            ->orderBy('sale_person_target_logs.created_at');
        return Datatables::of($targets)->make(true);
    }

    public function overnight_overland_cargo_report_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(),368);
        $overnight_rad_tat = GlobalSettings::where('type', 'rad_tat_overnight')->first();
        $overland_rad_tat = GlobalSettings::where('type', 'rad_tat_overland')->first();
        return view('admin.settings.overnight_overland_cargo_report.index')->with(['overnight' => $overnight_rad_tat, 'overland' => $overland_rad_tat]);
    }

    public function overnight_overland_cargo_report_list(Request $request)
    {
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),369);
        }

        $setting = City::leftjoin('admins as a', 'a.id', '=', 'cities.cut_off_time_updated_by')
            ->select('cities.id as origin_id', 'cities.name as origin', 'cities.cut_off_time as cut_off_time', 'cities.cut_off_time_updated_at as updated_at', 'a.name as updated_by')
            ->where('cities.hub', 1);
        return Datatables::of($setting)
            ->addColumn('action', function ($requests) {

                $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';
                $dropdown .= '<button type="button" data-target-id=' . $requests->origin_id . ' class="dropdown-item edit" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';
                return $dropdown;
            })->make(true);
    }

    public function overnight_overland_cargo_report_rad_tat_submit(Request $request)
    {
        GlobalSettings::where('type', 'rad_tat_overnight')->update([
            'setting_value' => $request->overnight,
        ]);
        GlobalSettings::where('type', 'rad_tat_overland')->update([
            'setting_value' => $request->overland,
        ]);
        return redirect()->back()->with('success', 'Rad Tat Updated Successfully!');
    }

    public function overnight_overland_cargo_report_edit_index($id)
    {
        $origin = City::find($id);
        $hubs = City::where('hub', 1)->where('status', 1)->get();
        $overnight_hubs = OvernightOverlandReportOriginHubs::where('origin_id', $id)->where('shipping_mode_id', 1)->pluck('hub_id')->toArray();
        return view('admin.settings.overnight_overland_cargo_report.update')->with(['origin' => $origin, 'hubs' => $hubs, 'overnight_hubs' => $overnight_hubs]);
    }

    public function overnight_overland_cargo_report_origin_submit(Request $request)
    {
        $city = City::find($request->origin_id);
        $city->cut_off_time = $request->cut_off_time;
        $city->cut_off_time_updated_at = Carbon::now();
        $city->cut_off_time_updated_by = Auth::id();
        $city->save();

        OvernightOverlandReportOriginHubs::where('origin_id', $city->id)->delete();

        $hubs = City::where('hub', 1)->where('id', '!=', $city->id)->where('status', 1)->pluck('id')->toArray();

        if ($request->has('hub_ids')) {
            foreach ($hubs as $hub) {
                if (in_array($hub, $request->hub_ids)) {
                    $overnight_hubs = new OvernightOverlandReportOriginHubs();
                    $overnight_hubs->origin_id = $city->id;
                    $overnight_hubs->hub_id = $hub;
                    $overnight_hubs->shipping_mode_id = 1;
                    $overnight_hubs->save();
                } else {
                    $overland_hubs = new OvernightOverlandReportOriginHubs();
                    $overland_hubs->origin_id = $city->id;
                    $overland_hubs->hub_id = $hub;
                    $overland_hubs->shipping_mode_id = 2;
                    $overland_hubs->save();
                }
            }
        } else {
            foreach ($hubs as $hub) {
                $overland_hubs = new OvernightOverlandReportOriginHubs();
                $overland_hubs->origin_id = $city->id;
                $overland_hubs->hub_id = $hub;
                $overland_hubs->shipping_mode_id = 2;
                $overland_hubs->save();
            }
        }

        return redirect()->route('admin.settings.overnight_overland_cargo_report.index')->with('success', 'Setting Updated Successfully');
    }

    public function projection_percentage_index()
    {
        $settings = GlobalSettings::where('type', 'sales_projection_percentage')->first();
        $percentage = '';
        if ($settings) {
            $percentage = $settings->setting_value;
        }
        return view('admin.settings.sales.percentage')->with(['projection_percentage' => $percentage]);
    }

    public function projection_percentage_update(Request $request)
    {
        $percentage = $request->projection_percentage;
        $setting = GlobalSettings::where('type', 'sales_projection_percentage');
        if ($setting->exists()) {
            $setting = $setting->first();
            $setting->setting_value = $percentage;
            $setting->save();
        } else {
            $setting = new GlobalSettings();
            $setting->setting_value = $percentage;
            $setting->type = 'sales_projection_percentage';
            $setting->save();
        }
        return redirect()->back()->with('success', 'Setting updated');
    }

    public function projection_reason_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(),340);
        return view('admin.settings.sales.reasons');
    }

    public function projection_reason_list(Request $request)
    {
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),341);
        }
        $reasons = BusinessProjectionReason::all(['id', 'name']);
        return Datatables::of($reasons)->make(true);
    }

    public function projection_reason_update(Request $request)
    {
        $reason = $request->reason;
        if ($reason == null && $reason == '') {
            return response()->json(['status' => 0, 'error' => 'Please enter reason!']);
        }
        $projection_reason = BusinessProjectionReason::where('name', $reason);
        if ($projection_reason->exists()) {
            return response()->json(['status' => 0, 'error' => 'Same reason already exists!']);
        } else {
            $business_projection_reason = new BusinessProjectionReason();
            $business_projection_reason->name = $reason;
            $business_projection_reason->save();

            return response()->json(['status' => 1, 'success' => 'New Reason added successfully!']);
        }
    }

    public function projection_shipments_index()
    {    ActivityTrailController::createActivityTrailLog(Auth::id(),342);
        $shippers = User::where('status', '>', 1)->select('id', 'name');

        if (session('department_id') == 7) {
//            if(session('role_id') != 4 ){
            $shippers = $shippers->where(function ($query) {
                $query->whereIn('users.id', session('tagged_shippers'));
            });
//            }
        }
        $shippers = $shippers->get();

        $business_shipment = BusinessProjectionShipment::all();

        return view('admin.settings.sales.shipments')->with(['shippers' => $shippers, 'shipments' => $business_shipment]);
    }

    public function projection_shipments_update(Request $request)
    {
        $shippers = $request->shippers;
        $shipment = $request->projected_shipment;
        if (count($shippers) > 0) {
            foreach ($shippers as $shipper) {
                $business_shipment = BusinessProjectionShipment::where('user_id', $shipper);
                if ($business_shipment->exists()) {
                    $business_shipment = $business_shipment->first();
                    $business_shipment->shipment = $shipment;
                } else {
                    $business_shipment = new BusinessProjectionShipment();
                    $business_shipment->user_id = $shipper;
                    $business_shipment->shipment = $shipment;
                }
                $business_shipment->save();
            }
            return redirect()->back()->with('success', 'Settings successfully updated');
        } else {
            return redirect()->back()->with('error', 'Shippers not selected!');
        }
    }

    public function projection_shipments_list(Request $request)
    {
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),343);
        }
        $shipments = BusinessProjectionShipment::join('users as u', 'u.id', '=', 'business_projection_shipments.user_id')->select('u.name as shipper', 'business_projection_shipments.shipment');
        if (session('department_id') == 7) {
            if (!in_array(session('id'), session('sale_users_bypass'))) {
                $shipments = $shipments->where(function ($query) {
                    $query->whereIn('u.id', session('tagged_shippers'));
                });
            }
        }
        return Datatables::of($shipments)->make(true);
    }

    public function delay_in_delivery_massage()
    {
        $message = '';
        $settings = GlobalSettings::where('type', 'crm_delay_in_delivery_message')->first();
        if ($settings) {
            $message = $settings->text;
        }
        return view('admin.settings.CRM.crm_delay_in_delivery_message')->with(['message' => $message]);
    }

    public function delay_in_delivery_massage_store(Request $request)
    {
        $message = $request->delay_in_delivery_message;

        if ($message) {
            $setting = GlobalSettings::where('type', 'crm_delay_in_delivery_message');
            if ($setting->exists()) {
                $setting = $setting->first();
                $setting->text = $message;
                $setting->save();
            } else {
                $setting = new GlobalSettings();
                $setting->type = 'crm_delay_in_delivery_message';
                $setting->setting_value = 0;
                $setting->text = $message;
                $setting->save();
            }
            return redirect()->back()->with(['success' => 'Message added successfully!']);
        }
        return redirect()->back()->with(['error' => 'Please write a Message!']);
    }

    public function auto_crm_comment_index()
    {
        $settings = GlobalSettings::where('type', 'auto_crm_comment')->first();

        return view('admin.settings.crm_comment')->with('settings', $settings);
    }

    public function auto_crm_comment_store(Request $request)
    {
        $settings = GlobalSettings::where('type', 'auto_crm_comment')->first();

        $settings->text = $request->comment;

        $settings->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function blacklist_index()
    {
        return view('admin.settings.blacklist.index');
    }

    public function blacklist_list(Request $request)
    {
        $blacklist = BlacklistSetting::join('admins as a', 'a.id', '=', 'blacklist_settings.added_by')
            ->leftjoin('admins as u', 'u.id', '=', 'blacklist_settings.updated_by')
            ->join('blacklist_labelings as bl', 'bl.id', '=', 'blacklist_settings.labeling_id')
            ->select('blacklist_settings.id as category_id', 'blacklist_settings.name as category_name', 'bl.name as labeling_name', 'a.name as added_by', 'u.name as updated_by', 'blacklist_settings.status', 'blacklist_settings.created_at as added_at', 'blacklist_settings.updated_at');
        $datatable = Datatables::of($blacklist)
            ->addColumn('category_status', function ($data) {
                if ($data->status == 0) {
                    return 'Disable';
                } else {
                    return 'Enable';
                }
            })
            ->addColumn('action', function ($data) {

                $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

                $dropdown .= '<button type="button" class="dropdown-item edit" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';

                if ($data->status == 1) {
                    $dropdown .= '<button type="button" class="dropdown-item disable" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-alert-octagon"></i></div><div class="col-9 offset-1">Disable</div></button>';
                } else {
                    $dropdown .= '<button type="button" class="dropdown-item enable" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check"></i></div><div class="col-9 offset-1">Enable</div></button>';
                }

                return $dropdown;

            });

        return $datatable->make(true);
    }

    public function blacklist_add()
    {
        $labelings = BlacklistLabeling::all(['id', 'name']);
        $conditions = BlacklistCondition::all(['id', 'name']);
        $logics = BlacklistLogic::all(['id', 'name']);
        $shipment_ranges = BlacklistShipmentRange::all(['id', 'name']);
        $operations = BlacklistOperation::all(['id', 'name']);
        return view('admin.settings.blacklist.add')->with(['labelings' => $labelings, 'conditions' => $conditions, 'logics' => $logics, 'shipment_ranges' => $shipment_ranges, 'operations' => $operations]);
    }

    public function blacklist_add_store(Request $request)
    {

        $conditions = $request->condition_select;
        $name = $request->name;
        $labeling_id = $request->labeling_select;
        $color = $request->color;
        $message = $request->message;

        if (BlacklistSetting::where('color', $color)->exists()) {
            return redirect()->back()->with('error', 'Color already selected!');
        }
        if (count($conditions) > count(array_flip($conditions))) {
            return redirect()->back()->with('error', 'Same condition selected multiple times!');
        }

        if (!empty($conditions)) {

            $blacklist_setting = new BlacklistSetting();
            $blacklist_setting->name = $name;
            $blacklist_setting->labeling_id = $labeling_id;
            $blacklist_setting->color = $color;
            $blacklist_setting->message = $message;
            $blacklist_setting->added_by = Auth::id();
            $blacklist_setting->save();
            $setting_id = $blacklist_setting->id;
            foreach ($conditions as $key => $condition) {
                foreach ($request->logic_select[$key] as $row => $logic) {
                    $blacklist_setting_condition = new BlacklistSettingCondition();
                    $blacklist_setting_condition->blacklist_setting_id = $setting_id;
                    $blacklist_setting_condition->blacklist_condition_id = $condition;
                    $blacklist_setting_condition->blacklist_logic_id = $logic;
                    $blacklist_setting_condition->blacklist_logic_value = $request->logic_percentage[$key][$row];
                    $blacklist_setting_condition->blacklist_shipment_range_id = $request->shipment_range_select[$key][$row];
                    $blacklist_setting_condition->blacklist_shipment_range_value = $request->shipment_range[$key][$row];
                    if ($request->has('operation_select')) {
                        if (array_key_exists($key, $request->operation_select)) {
                            if (array_key_exists($row, $request->operation_select[$key])) {
                                $blacklist_setting_condition->blacklist_operation_id = $request->operation_select[$key][$row];
                            }
                        }
                    }
                    $blacklist_setting_condition->save();
                }

            }
            return redirect()->back()->with('success', 'Setting successfully added!');
        }
    }

    public function blacklist_unique_criteria(Request $request)
    {
        $condition_id = $request->condition;
        $logic_id = $request->logic_select;
        $logic_value = $request->logic_value;
        if ($request->has('category_id')) {
            $category_id = $request->category_id;
            if (BlacklistSettingCondition::where('blacklist_setting_id', '!=', $category_id)->where('blacklist_condition_id', $condition_id)->where('blacklist_logic_id', $logic_id)->where('blacklist_logic_value', $logic_value)->exists()) {
                return "true";
            } else {
                return "false";
            }
        }
        if (BlacklistSettingCondition::where('blacklist_condition_id', $condition_id)->where('blacklist_logic_id', $logic_id)->where('blacklist_logic_value', $logic_value)->exists()) {
            return "true";
        } else {
            return "false";
        }

    }

    public function blacklist_status(Request $request)
    {
        $id = $request->id;
        $status = $request->status;
        $blacklist_setting = BlacklistSetting::find($id);
        if (!$blacklist_setting) {
            return response()->json(['status' => 1, 'error' => 'Setting not found!']);
        }

        if ($status == 1) {
            $blacklist_setting->status = 1;
        } else if ($status == 0) {
            $blacklist_setting->status = 0;
        }
        $blacklist_setting->save();

        return response()->json(['status' => 0, 'success' => 'Setting updated successfully!']);
    }

    public function blacklist_edit(Request $request, $id)
    {
        $blacklist_setting = BlacklistSetting::find($id);
        if ($blacklist_setting) {
            $condition_ids = $blacklist_setting->conditions()->pluck('blacklist_condition_id')->toArray();
            $condition_ids = array_unique($condition_ids);
            $blacklist_conditions = $blacklist_setting->conditions->groupBy('blacklist_condition_id');
            $labelings = BlacklistLabeling::all(['id', 'name']);
            $conditions = BlacklistCondition::all(['id', 'name']);
            $logics = BlacklistLogic::all(['id', 'name']);
            $shipment_ranges = BlacklistShipmentRange::all(['id', 'name']);
            $operations = BlacklistOperation::all(['id', 'name']);

            return view('admin.settings.blacklist.edit')->with(['setting_id' => $id, 'labelings' => $labelings, 'conditions' => $conditions, 'logics' => $logics, 'shipment_ranges' => $shipment_ranges, 'operations' => $operations, 'blacklist_setting' => $blacklist_setting, 'condition_ids' => $condition_ids, 'blacklist_conditions' => $blacklist_conditions]);
        } else {
            return redirect()->back()->with('error', 'Settings not found!');
        }
    }

    public function blacklist_edit_submit(Request $request)
    {

        $id = $request->setting_id;
        $conditions = $request->condition_select;
        $name = $request->name;
        $labeling_id = $request->labeling_select;
        $color = $request->color;
        $message = $request->message;
        if (BlacklistSetting::where('color', $color)->where('id', '<>', $id)->exists()) {
            return redirect()->back()->with('error', 'Color already selected!');
        }
        if (count($conditions) > count(array_flip($conditions))) {
            return redirect()->back()->with('error', 'Same condition selected multiple times!');
        }

        if (!empty($conditions)) {

            $blacklist_setting = BlacklistSetting::find($id);
            if ($blacklist_setting) {
                $blacklist_setting->name = $name;
                $blacklist_setting->labeling_id = $labeling_id;
                $blacklist_setting->color = $color;
                $blacklist_setting->message = $message;
                $blacklist_setting->updated_by = Auth::id();
                $blacklist_setting->save();
                $setting_id = $id;
                BlacklistSettingCondition::where('blacklist_setting_id', $id)->delete();
                foreach ($conditions as $key => $condition) {
                    foreach ($request->logic_select[$key] as $row => $logic) {
                        $blacklist_setting_condition = new BlacklistSettingCondition();
                        $blacklist_setting_condition->blacklist_setting_id = $setting_id;
                        $blacklist_setting_condition->blacklist_condition_id = $condition;
                        $blacklist_setting_condition->blacklist_logic_id = $logic;
                        $blacklist_setting_condition->blacklist_logic_value = $request->logic_percentage[$key][$row];
                        $blacklist_setting_condition->blacklist_shipment_range_id = $request->shipment_range_select[$key][$row];
                        $blacklist_setting_condition->blacklist_shipment_range_value = $request->shipment_range[$key][$row];
                        if ($request->has('operation_select')) {
                            if (array_key_exists($key, $request->operation_select)) {
                                if (array_key_exists($row, $request->operation_select[$key])) {
                                    $blacklist_setting_condition->blacklist_operation_id = $request->operation_select[$key][$row];
                                }
                            }
                        }
                        $blacklist_setting_condition->save();
                    }

                }
                return redirect()->back()->with('success', 'Setting successfully updated!');
            }
            return redirect()->back()->with('error', 'Category not found!');
        }
        return redirect()->back()->with('error', 'Conditions not selected!');
    }

    public function blacklist_search_index()
    {
        $blacklists = BlacklistSetting::select(['id', 'name'])->where('status', 1)->get();
        return view('admin.settings.blacklist.search')->with(['blacklists' => $blacklists]);
    }

    public function blacklist_search_consignee(Request $request)
    {
        $phone = $request->phone;
        $data = array();
        $consignee_information = ConsigneeInformation::where('phone', $phone);
        if ($consignee_information->exists()) {
            $consignee_information = $consignee_information->first();
            $data['consignee'] = array();

            $data['consignee']['id'] = $consignee_information->id;
            $data['consignee']['name'] = $consignee_information->name;
            $data['consignee']['phone'] = $consignee_information->phone;
            $data['consignee']['phone2'] = $consignee_information->phone2;
            $data['consignee']['address'] = $consignee_information->address;
            $data['consignee']['city'] = $consignee_information->consignee_city->name;
            $consignee_information_id = $consignee_information->id;
            $manual_blacklist = BlacklistedConsigneeManuallyBlacklisted::where('consignee_information_id', $consignee_information_id);
            $color = null;
            if ($manual_blacklist->exists()) {
                $manual_blacklist = $manual_blacklist->first();
                $color = BlacklistSetting::find($manual_blacklist->blacklist_setting_id)->color;
            }
            if (BlacklistedConsignee::where('consignee_information_id', $consignee_information_id)->exists()) {
                $data['blacklist'] = array();
                $data['blacklist']['total_shipments'] = $consignee_information->blacklisted_consignee->shipments;
                $data['blacklist']['delivered'] = $consignee_information->blacklisted_consignee->delivered;
                $data['blacklist']['delivered_ratio'] = $consignee_information->blacklisted_consignee->delivered_ratio;
                $data['blacklist']['undelivered'] = $consignee_information->blacklisted_consignee->undelivered;
                $data['blacklist']['undelivered_ratio'] = $consignee_information->blacklisted_consignee->undelivered_ratio;
                $data['blacklist']['return'] = $consignee_information->blacklisted_consignee->return;
                $data['blacklist']['return_ratio'] = $consignee_information->blacklisted_consignee->return_ratio;
                if ($color == null) {
                    $data['blacklist']['color'] = $consignee_information->blacklisted_consignee->blacklist->color;
                } else {
                    $data['blacklist']['color'] = $color;
                }
            }
            return response()->json(['status' => 0, 'success' => 'Consignee information found!', 'details' => $data]);
        }
        return response()->json(['status' => 1, 'error' => 'Consignee not found']);
    }

    public function blacklist_search_update(Request $request)
    {
        $action = $request->action;
        $blacklist_setting_id = $request->label_select;
        $consignee_information_id = $request->consignee_information_id;
        if ($action == 'exclude') {
            $blacklist = BlacklistedConsigneeManuallyExcluded::where('consignee_information_id', $consignee_information_id);
            if ($blacklist->exists()) {
                return redirect()->back()->with('error', 'Already excluded!');
            }

            $blacklist = new BlacklistedConsigneeManuallyExcluded();
            $blacklist->consignee_information_id = $consignee_information_id;
            $blacklist->excluded_by = Auth::id();
            $blacklist->save();

        } else if ($action == 'label') {
            $blacklist = BlacklistedConsigneeManuallyBlacklisted::where('consignee_information_id', $consignee_information_id);
            if ($blacklist->exists()) {
                $blacklist = $blacklist->first();
                $blacklist->added_by = Auth::id();
                $blacklist->blacklist_setting_id = $blacklist_setting_id;
                $blacklist->save();
            } else {
                $blacklist = new BlacklistedConsigneeManuallyBlacklisted();
                $blacklist->consignee_information_id = $consignee_information_id;
                $blacklist->added_by = Auth::id();
                $blacklist->blacklist_setting_id = $blacklist_setting_id;
                $blacklist->save();
            }

        }
        return redirect()->back()->with('success', 'Successfully updated!');
    }

    public function commission_percentage_index()
    {
        $settings = GlobalSettings::where('type', 'commission_percentage')->first();
        $percentage = '';
        if ($settings) {
            $percentage = $settings->text;
        }
        return view('admin.settings.commission.commission_percentage')->with(['commission_percentage' => $percentage]);
    }

    public function commission_percentage_update(Request $request)
    {
        $percentage = $request->commission_percentage;
        $setting = GlobalSettings::where('type', 'commission_percentage');
        if ($setting->exists()) {
            $setting = $setting->first();
            $setting->setting_value = 0;
            $setting->text = $percentage;
            $setting->save();
        } else {
            $setting = new GlobalSettings();
            $setting->text = $percentage;
            $setting->type = 'commission_percentage';
            $setting->save();
        }
        return redirect()->back()->with('success', 'Setting updated');
    }

    public function return_reason_index()
    {
        return view('admin.settings.return.reason');
    }

    public function return_reason_list(Request $request)
    {
        $reason_ids = DB::table('shipment_status_shipment_status_reason')->where('shipment_status_id', 20)->pluck('shipment_status_reason_id')->toArray();
        $reasons = ShipmentStatusReason::whereIn('id', $reason_ids)->select('id', 'name');
        $datatable = Datatables::of($reasons)
            ->addColumn('action', function ($data) {

                $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

                $dropdown .= '<button type="button" class="dropdown-item edit" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';

                return $dropdown;

            });

        return $datatable->make(true);
    }

    public function return_reason_add(Request $request)
    {
        $reason = trim($request->reason);
        if ($reason) {
            $shipment_reasons = ShipmentStatusReason::where('name', 'like', strtolower($reason));
            if(!$shipment_reasons->exists()){

                $shipment_reason = new ShipmentStatusReason();
                $shipment_reason->name = $reason;
                $shipment_reason->save();

                DB::table('shipment_status_shipment_status_reason')->insert(['shipment_status_id' => 20, 'shipment_status_reason_id' => $shipment_reason->id]);

                return response()->json(['status' => 0, 'success' => 'Reason added successfully!']);
            }
            else{
                $shipment_reason = $shipment_reasons->first();
                DB::table('shipment_status_shipment_status_reason')->insert(['shipment_status_id' => 20, 'shipment_status_reason_id' => $shipment_reason->id]);
                return response()->json(['status' => 0, 'success' => 'Reason added successfully!']);
            }

        }
        return response()->json(['status' => 1, 'error' => 'Please enter reason!']);
    }

    public function return_reason_get(Request $request)
    {
        $id = $request->reason_id;
        if ($id) {
            $reason = ShipmentStatusReason::find($id);
            if ($reason) {
                return response()->json(['status' => 0, 'reason' => $reason->name]);
            }
            return response()->json(['status' => 1, 'error' => 'Reason not found!']);
        }
        return response()->json(['status' => 1, 'error' => 'Please select reason!']);
    }

    public function return_reason_edit(Request $request)
    {
        $reason_id = $request->reason_id;
        $reason = $request->reason;
        if ($reason) {
            $reason_detail = ShipmentStatusReason::find($reason_id);
            $reason_detail->name = $reason;
            $reason_detail->save();
            return response()->json(['status' => 0, 'success' => 'Reason updated successfully!']);
        }
        return response()->json(['status' => 1, 'error' => 'Please enter reason!']);
    }

    public function pickup_cut_off_settings_index()
    {
        $settings = GlobalSettings::where('type', 'pickup_request_cut_off_time')->first();

        if ($settings) {
            $pickup_request_cut_off_time = $settings->setting_value;
        } else {
            $pickup_request_cut_off_time = 15;
        }

        $settings = GlobalSettings::where('type', 'pickup_arrival_cut_off_time')->first();

        if ($settings) {
            $pickup_arrival_cut_off_time = $settings->setting_value;
        } else {
            $pickup_arrival_cut_off_time = 8;
        }

        $settings = GlobalSettings::where('type', 'rider_assignment_cut_off_time')->first();

        if ($settings) {
            $rider_assignment_cut_off_time = $settings->setting_value;
        } else {
            $rider_assignment_cut_off_time = 0;
        }
        $settings = GlobalSettings::where('type', 'global_rider_id')->first();

        if ($settings) {
            $global_rider_id = $settings->setting_value;
        } else {
            $global_rider_id = 0;
        }

        return view('admin.settings.pickup_settings')->with(['pickup_request_cut_off_time' => $pickup_request_cut_off_time, 'pickup_arrival_cut_off_time' => $pickup_arrival_cut_off_time, 'rider_assignment_cut_off_time' => $rider_assignment_cut_off_time, 'global_rider_id' => $global_rider_id]);
    }

    public function pickup_cut_off_settings_store(Request $request)
    {
        $request_settings = GlobalSettings::where('type', 'pickup_request_cut_off_time');

        if ($request_settings->exists()) {
            $request_settings = $request_settings->first();
        } else {
            $request_settings = new GlobalSettings();

            $request_settings->type = 'pickup_request_cut_off_time';
        }

        $request_settings->setting_value = $request->request_cut_off_time;

        $request_settings->save();

        $request_arrival = GlobalSettings::where('type', 'pickup_arrival_cut_off_time');

        if ($request_arrival->exists()) {
            $request_arrival = $request_arrival->first();
        } else {
            $request_arrival = new GlobalSettings();

            $request_arrival->type = 'pickup_arrival_cut_off_time';
        }

        $request_arrival->setting_value = $request->arrival_cut_off_time;

        $request_arrival->save();

        $rider_assignment = GlobalSettings::where('type', 'rider_assignment_cut_off_time');

        if ($rider_assignment->exists()) {
            $rider_assignment = $rider_assignment->first();
        } else {
            $rider_assignment = new GlobalSettings();

            $rider_assignment->type = 'rider_assignment_cut_off_time';
        }
        if ($request->rider_assignment_off_time != null) {
            $rider_assignment->setting_value = $request->rider_assignment_off_time;
        } else {
            $rider_assignment->setting_value = 0;
        }

        $rider_assignment->save();

        $global_rider = GlobalSettings::where('type', 'global_rider_id');

        if ($global_rider->exists()) {
            $global_rider = $global_rider->first();
        } else {
            $global_rider = new GlobalSettings();

            $global_rider->type = 'global_rider_id';
        }

        $global_rider->setting_value = $request->global_rider_id;

        $global_rider->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function completed_aging_report_settings_index()
    {

        $settings = GlobalSettings::where('type', 'completed_aging_report_time')->first();

        if ($settings) {
            $completed_aging_report_time = $settings->setting_value;
        } else {
            $completed_aging_report_time = 10;
        }

        return view('admin.settings.aging_report')->with(['completed_aging_report_time' => $completed_aging_report_time]);
    }

    public function completed_aging_report_settings_store(Request $request)
    {
        $request_settings = GlobalSettings::where('type', 'completed_aging_report_time');

        if ($request_settings->exists()) {
            $request_settings = $request_settings->first();
        } else {
            $request_settings = new GlobalSettings();

            $request_settings->type = 'completed_aging_report_time';
        }

        $request_settings->setting_value = $request->completed_aging_report_time;

        $request_settings->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public static function insertCompletedAgingData()
    {

        $now = Carbon::now();
        $total = 0;
        $hubs = City::where('hub', 1)->where('status', 1)->pluck('id')->toArray();
        if (count($hubs) > 0) {
            DB::table('completed_aging_reports')->truncate();
            foreach ($hubs as $hub_id) {
                $total_count = 0;
                $zone_id = City::find($hub_id)->zone_id;
//                $zone_id = ZoneClassCity::where('city_id',$hub_id)->first();
                $completed_aging_report = new CompletedAgingReport();
                $completed_aging_report->hub_id = $hub_id;
                $completed_aging_report->zone_id = $zone_id;

                $completed_aging_report->date = $now;

                $delviery_notes = DeliveryNote::where('hub_id', $hub_id)->where('cash_collection_status', 1)->where('dncc_status', 0)->where('status', 1)->get();
                foreach ($delviery_notes as $delviery_note) {
                    $start = $delviery_note->status_verified_at;
                    $start = Carbon::parse($start);
                    $difference = $start->diffInDays($now);
                    if ($difference > 2) {
                        $total_count++;
                    }
                }
                $total += $total_count;
                $completed_aging_report->count = $total_count;
                $completed_aging_report->save();
            }

            NotificationsController::send(71, $now);
        }
    }

    public static function insertPendingCashCollectionData()
    {

        $now = Carbon::now();
        $total = 0;

        $hubs = City::where('hub', 1)->where('status', 1)->pluck('id')->toArray();
        if (count($hubs) > 0) {
            DB::table('pending_cash_collection_aging_reports')->truncate();
            foreach ($hubs as $hub_id) {
                $total_count = 0;
                $zone_id = City::find($hub_id)->zone_id;

                $pending_cash_collection_aging_report = new PendingCashCollectionAgingReport();

                $pending_cash_collection_aging_report->hub_id = $hub_id;
                $pending_cash_collection_aging_report->zone_id = $zone_id;

                $pending_cash_collection_aging_report->date = $now;

                $delviery_notes = DeliveryNote::where('hub_id', $hub_id)->where('pending_status', 1)->where('cash_collection_status', 0)->where('dncc_status', 0)->get();
                foreach ($delviery_notes as $delviery_note) {
                    $start = $delviery_note->status_updated_at;
                    $start = Carbon::parse($start);
                    $difference = $start->diffInDays($now);
                    if ($difference > 2) {
                        $total_count++;
                    }
                }
                $total += $total_count;
                $pending_cash_collection_aging_report->count = $total_count;
                $pending_cash_collection_aging_report->save();
            }
        }
        NotificationsController::send(72, $now);

    }

    public function crm_default_agent_index()
    {
        $agents = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id')
            ->where('admin_roles.department_id', 3)
            ->get();

        $setting = GlobalSettings::where('type', 'crm_default_agent')->first();
        return view('admin.settings.CRM.default_agent')->with(['agents' => $agents, 'setting' => $setting]);
    }

    public function crm_default_agent_store(Request $request)
    {
        $setting = GlobalSettings::where('type', 'crm_default_agent');
        if ($setting->exists()) {
            $setting = $setting->first();
            $setting->setting_value = $request->sale_person;
            $setting->save();
        } else {
            $setting = new GlobalSettings();
            $setting->setting_value = $request->sale_person;
            $setting->type = 'crm_default_agent';
            $setting->save();
        }
        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function zero_charges_report_settings_index()
    {

        $settings = GlobalSettings::where('type', 'zero_charges_report_time')->first();

        if ($settings) {
            $zero_charges_report_time = $settings->setting_value;
        } else {
            $zero_charges_report_time = 10;
        }

        return view('admin.settings.zero_charges_report_settings')->with(['zero_charges_report_time' => $zero_charges_report_time]);
    }

    public function zero_charges_report_settings_store(Request $request)
    {
        $request_settings = GlobalSettings::where('type', 'zero_charges_report_time');

        if ($request_settings->exists()) {
            $request_settings = $request_settings->first();
        } else {
            $request_settings = new GlobalSettings();

            $request_settings->type = 'zero_charges_report_time';
        }

        $request_settings->setting_value = $request->zero_charges_report_time;

        $request_settings->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function not_attempted_cron_index()
    {
        $settings = GlobalSettings::where('type', 'not_attempted_cron_time')->first();

        return view('admin.settings.not_attempted_report_cron_time')->with('settings', $settings);
    }

    public function not_attempted_cron_store(Request $request)
    {
        $settings = GlobalSettings::where('type', 'not_attempted_cron_time');
        if ($settings->exists()) {
            $settings = $settings->first();
        } else {
            $settings = new GlobalSettings();
            $settings->type = 'not_attempted_cron_time';
        }

        $settings->setting_value = $request->not_attempted_cron_time;
        $settings->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function holidays_index()
    {    ActivityTrailController::createActivityTrailLog(Auth::id(),366);
        return view('admin.settings.holidays');
    }

    public function holidays_list(Request $request)
    {
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),367);
        }
        $holidays = Holiday::leftjoin('admins as a', 'a.id', '=', 'holidays.created_by')
            ->select('holidays.reason as reason', 'holidays.holiday as holiday', 'holidays.created_at as created_at', 'a.name as created_by');

        return Datatables::of($holidays)
            ->make(true);
    }

    public function holidays_add(Request $request)
    {
        $holiday_date = $request->holiday_date;
        $holiday_reason = $request->holiday_reason;
        $existing_holiday = CrmTatHolidays::where('holiday', $holiday_date);
        if ($existing_holiday->exists()) {
            return ['status' => 0, 'error' => 'Holiday is already marked on the selected date!'];
        } else {
            $new_holiday = new Holiday();
            $new_holiday->holiday = $holiday_date;
            $new_holiday->reason = $holiday_reason;
            $new_holiday->created_by = Auth::id();
            $new_holiday->save();
            return ['status' => 1, 'success' => 'Holiday added successfully!'];
        }
    }

    public function station_recovery_cron_index()
    {
        $settings = GlobalSettings::where('type', 'station_recovery_cron_time');
        $time = '';
        if ($settings->exists()) {
            $settings = $settings->first();
            $time = $settings->setting_value;
        }

        return view('admin.settings.station_recovery.station_recovery_cron_time')->with('time', $time);
    }

    public function station_recovery_cron_store(Request $request)
    {
        $settings = GlobalSettings::where('type', 'station_recovery_cron_time');
        if ($settings->exists()) {
            $settings = $settings->first();
        } else {
            $settings = new GlobalSettings();
            $settings->type = 'station_recovery_cron_time';
        }
        $settings->setting_value = $request->station_recovery_cron_time;

        $settings->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function over_payment_limit_index()
    {
        $settings = GlobalSettings::where('type', 'over_payment_limit');

        if ($settings->exists()) {
            $settings = $settings->first();

            $over_payment_limit = $settings->setting_value;
        } else {
            $over_payment_limit = 6000000;
        }

        return view('admin.settings.over_payment_limit')->with('over_payment_limit', $over_payment_limit);
    }

    public function over_payment_limit_store(Request $request)
    {
        $settings = GlobalSettings::where('type', 'over_payment_limit');

        if ($settings->exists()) {
            $settings = $settings->first();
        } else {
            $settings = new GlobalSettings();

            $settings->type = 'over_payment_limit';
        }

        $settings->setting_value = $request->over_payment_limit;

        $settings->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function nsa_account_index()
    {
        $shippers = User::where('status', 3)->where('blacklist', 0)->select('id', 'name')->get();
        $riders = Rider::where('status', 1)->select('id', 'name')->get();
        $settings = GlobalSettings::where('type', 'nsa_accounts');
        $rider_id = null;
        $nsa_accounts = array();
        if ($settings->exists()) {
            $settings = $settings->first();
            $nsa_accounts = array_map('intval', explode(',', $settings->text));
            $rider_id = $settings->setting_value;
        }
        return view('admin.settings.nsa_account')->with(['shippers' => $shippers, 'riders' => $riders, 'rider_id' => $rider_id, 'nsa_accounts' => $nsa_accounts]);
    }

    public function nsa_account_store(Request $request)
    {
        if ($request->has('shippers')) {
            if (count($request->shippers) > 0) {
                $shippers = implode(',', $request->shippers);
                $settings = GlobalSettings::where('type', 'nsa_accounts');

                if ($settings->exists()) {
                    $settings = $settings->first();
                } else {
                    $settings = new GlobalSettings();

                    $settings->type = 'nsa_accounts';

                }
                $settings->setting_value = $request->rider;
                $settings->text = $shippers;
                $settings->save();
            }
            return redirect()->back()->with('success', 'Settings Updated!');

        } else {
            return redirect()->back()->with('error', 'No shippers selected!');
        }

    }

    public function carrefour_account_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(),444);
        $shippers = User::where('status', 3)->where('blacklist', 0)->select('id', 'name')->get();
        $riders = Rider::where('status', 1)->select('id', 'name')->get();
        $settings = GlobalSettings::where('type', 'carrefour_accounts');
        $rider_id = null;
        $carrefour_accounts = array();
        if ($settings->exists()) {
            $settings = $settings->first();
            $carrefour_accounts = array_map('intval', explode(',', $settings->text));
            $rider_id = $settings->setting_value;
        }
        return view('admin.settings.carrefour.accounts')->with(['shippers' => $shippers, 'riders' => $riders, 'rider_id' => $rider_id, 'carrefour_accounts' => $carrefour_accounts]);
    }

    public function carrefour_account_store(Request $request)
    {
        if ($request->has('shippers')) {
            if (count($request->shippers) > 0) {
                $shippers = implode(',', $request->shippers);
                $settings = GlobalSettings::where('type', 'carrefour_accounts');

                if ($settings->exists()) {
                    $settings = $settings->first();
                } else {
                    $settings = new GlobalSettings();

                    $settings->type = 'carrefour_accounts';

                }
                $settings->setting_value = 0;
                $settings->text = $shippers;
                $settings->save();
            }
            return redirect()->back()->with('success', 'Settings Updated!');

        } else {
            return redirect()->back()->with('error', 'No shippers selected!');
        }

    }
    public function restrict_cities_intercept_index()
    {
        $cities = City::where('status', 1)->select('id', 'name')->get();
        $restricted_cities = RestrictedCityIntercept::pluck('city_id')->toArray();
        return view('admin.settings.restrict_cities_intercept')->with(['cities' => $cities, 'restricted_cities' => $restricted_cities]);
    }

    public function restrict_cities_intercept_store(Request $request)
    {
        RestrictedCityIntercept::truncate();
        if ($request->has('cities')) {
            if (count($request->cities) > 0) {
                $cities = $request->cities;
                foreach ($cities as $city_id) {
                    $restricted_city = new RestrictedCityIntercept();
                    $restricted_city->city_id = $city_id;
                    $restricted_city->save();
                }
            }
        }
        return redirect()->back()->with('success', 'Settings Updated!');

    }

    public function short_received_hub_wise_cron_index()
    {

        $settings = GlobalSettings::where('type', 'short_received_hub_wise_cron')->first();

        if ($settings) {
            $default_time = $settings->setting_value;
        } else {
            $default_time = 8;
        }
        $cities = City::where('status', 1)->select('id', 'name')->get();
        $existing_cities = ShortReceiveReportTimeHubWise::get();

        return view('admin.settings.short_received_report_hub_wise_time')->with(['default_time' => $default_time, 'cities' => $cities, 'existing_cities' => $existing_cities]);
    }

    public function short_received_hub_wise_cron_store(Request $request)
    {
        $settings = GlobalSettings::where('type', 'short_received_hub_wise_cron');

        if ($settings->exists()) {
            $settings = $settings->first();
        } else {
            $settings = new GlobalSettings();

            $settings->type = 'short_received_hub_wise_cron';
        }

        $settings->setting_value = $request->default_time;

        $settings->save();
        ShortReceiveReportTimeHubWise::truncate();
        if ($request->has('cities')) {
            $new_cities = $request->cities;
            foreach ($new_cities as $index => $city) {
                $n_city = new ShortReceiveReportTimeHubWise();
                $n_city->hub_id = $city;
                $n_city->time = $request->time[$index];
                $n_city->save();
            }
        }

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function restrict_parcels_attempt_index()
    {        ActivityTrailController::createActivityTrailLog(Auth::id(),356);
        $already_restricted_shippers = RestrictParcelsAttempt::pluck('shipper_id')->toArray();
        $shippers = User::where('status', 3)->whereNotIn('id', $already_restricted_shippers)->get();
        return view('admin.settings.retrun_parcels_after_attempts')->with('shippers', $shippers);
    }

    public function restrict_parcels_attempt_list(Request $request)
    {
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),357);
        }
        $restricted_parcels_attempt = RestrictParcelsAttempt::join('users as u', 'u.id', '=', 'restrict_parcels_attempts.shipper_id')
            ->join('admins as a', 'a.id', '=', 'restrict_parcels_attempts.updated_by')
            ->select('restrict_parcels_attempts.id', 'u.name as shipper', 'restrict_parcels_attempts.attempt_days', 'restrict_parcels_attempts.status', 'restrict_parcels_attempts.created_at', 'restrict_parcels_attempts.updated_at', 'a.name as updated_by');
        $datatable = Datatables::of($restricted_parcels_attempt)
            ->editColumn('status', function ($data) {
                if ($data->status == 1) {
                    return 'Enabled';
                } else {
                    return 'Disabled';
                }
            })
            ->addColumn('action', function ($data) {
                $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

                $dropdown .= '<button type="button" class="dropdown-item edit" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';

                if ($data->status == 1) {
                    $dropdown .= '<button type="button" class="dropdown-item disable" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Disable</div></button>';
                } else {
                    $dropdown .= '<button type="button" class="dropdown-item enable" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Enable</div></button>';
                }

                return $dropdown;

            });

        return $datatable->make(true);
    }

    public function restrict_parcels_attempt_add(Request $request)
    {
        if ($request->has('shipper_id') && $request->has('attempt_days')) {
            $shipper_id = $request->shipper_id;
            $attempt_days = $request->attempt_days;
            if ($shipper_id != null && $attempt_days != null) {
                $restrict_shipper = new RestrictParcelsAttempt();
                $restrict_shipper->shipper_id = $shipper_id;
                $restrict_shipper->attempt_days = $attempt_days;
                $restrict_shipper->updated_by = Auth::id();
                $restrict_shipper->save();

                return redirect()->back()->with('success', 'Setting updated successfully!');
            } else {
                return redirect()->back()->with('error', 'Data not found!');
            }
        } else {
            return redirect()->back()->with('error', 'Something went wrong!');
        }
    }

    public function restrict_parcels_attempt_edit(Request $request)
    {
        if ($request->has('id') && $request->has('attempt_days')) {
            $id = $request->id;
            $attempt_days = $request->attempt_days;
            if ($id != null && $attempt_days != null) {
                $restrict_shipper = RestrictParcelsAttempt::find($id);
                $restrict_shipper->attempt_days = $attempt_days;
                $restrict_shipper->status = 1;
                $restrict_shipper->updated_by = Auth::id();
                $restrict_shipper->save();

                return redirect()->back()->with('success', 'Setting updated successfully!');
            } else {
                return redirect()->back()->with('error', 'Data not found!');
            }
        } else {
            return redirect()->back()->with('error', 'Something went wrong!');
        }
    }

    public function restrict_parcels_attempt_enable_disable(Request $request)
    {
        if ($request->has('id') && $request->has('status')) {
            $id = $request->id;
            $status = $request->status;
            if ($id != null && $status != null) {
                $restrict_shipper = RestrictParcelsAttempt::find($id);
                $restrict_shipper->status = $status;
                $restrict_shipper->updated_by = Auth::id();
                $restrict_shipper->save();
                if ($status == 1) {
                    $text = 'Enabled';
                } else {
                    $text = 'Disabled';
                }
                return response()->json(['status' => 1, 'success' => 'Setting ' . $text . ' Successfully!']);
            } else {
                return response()->json(['status' => 0, 'error' => 'Data not found']);
            }
        } else {
            return response()->json(['status' => 0, 'error' => 'Something went wrong!']);
        }
    }

    public function runner_report_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(),251);
        $cities = City::where('status', 1)->where('hub', 1)->get();
        return view('admin.settings.runner.index')->with('cities', $cities);
    }

    public function runner_report_list(Request $request)
    {
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),252);
        }
        $runner_report = Runner::join('admins as a', 'a.id', '=', 'runners.created_by')
            ->select('runners.id as id', 'runners.name as runner', 'runners.created_at', 'a.name as created_by', 'runners.status as status');
        $datatable = Datatables::of($runner_report)
            ->editColumn('status', function ($runner) {
                if ($runner->status == 0 || $runner->status == 1) {
                    return 'Enable';
                } else {
                    return 'Disable';
                }
            })
            ->addColumn('action', function ($runner) {
                $enable = '<button type="button" class="dropdown-item status"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Enable</div></button>';
                $disable = '<button type="button" class="dropdown-item status"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Disable</div></button>';

                $dropdown = '
                    <div class="btn-group">
                      <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                      <div class="dropdown-menu dropdown-menu-sm">';

                if ($runner->status == 2) {
                    $dropdown .= $enable;
                }
                if ($runner->status == 1 || $runner->status == 0) {
                    $dropdown .= $disable;
                }
                return $dropdown;

            });
        return $datatable->make(true);
    }

    public function runner_report_unique(Request $request)
    {
        if ($request->filled('runner_name')) {
            $runner = Runner::where('name', $request->input('runner_name'));

            if (!$runner->exists()) {
                return 'true';
            } else {
                return 'false';
            }
        } else {
            return 'true';
        }
    }

    public function runner_report_add(Request $request)
    {
        if ($request->has('junction')) {
            $runner = new Runner();
            $runner->name = $request->runner_name;
            $runner->created_by = Auth::id();
            $runner->save();

            $origin = new RunnerJunction();
            $origin->runner_id = $runner->id;
            $origin->junction_id = $request->origin;
            $origin->order = 1;
            $origin->save();

            $serial = 2;
            $junctions = array($request->origin, $request->destination);
            foreach ($request->junction as $junction_id) {
                if (!in_array($junction_id, $junctions)) {
                    $junctions[] = $junction_id;
                    $junction = new RunnerJunction();
                    $junction->runner_id = $runner->id;
                    $junction->junction_id = $junction_id;
                    $junction->order = $serial;
                    $junction->save();

                    $serial++;
                }
            }

            $destination = new RunnerJunction();
            $destination->runner_id = $runner->id;
            $destination->junction_id = $request->destination;
            $destination->order = $serial;
            $destination->save();

            return redirect()->back()->with('success', 'Runner updated successfully!');
        } else {
            return redirect()->back()->with('error', 'Please add junctions!');
        }
    }

    public function arrived_at_origin_sms_for_shipper_index()
    {
        $shippers = User::where('status', 3)->select('id', 'name')->get();

        $existing_shippers = BookingSmsForShippers::pluck('user_id')->toArray();

        return view('admin.settings.arrived_at_origin_sms_for_shipper')->with(['shippers' => $shippers, 'existing_shippers' => $existing_shippers]);
    }

    public function arrived_at_origin_sms_for_shipper_update(Request $request)
    {
        $shippers = $request->shippers;
        BookingSmsForShippers::truncate();
        if ($shippers != null) {
            if (count($shippers) > 0) {
                foreach ($shippers as $shipper) {
                    ;
                    $business_shipment = new BookingSmsForShippers();
                    $business_shipment->user_id = $shipper;
                    $business_shipment->save();
                }
            }
        }
        return redirect()->back()->with('success', 'Settings successfully updated');
    }

    public function pickup_address_wise_payment_accounts_index()
    {
        $shippers = User::where('status', 3)->where('blacklist', 0)->select('id', 'name')->get();
        $settings = GlobalSettings::where('type', 'pickup_wise_payment_accounts');
        $pickup_wise_accounts = array();
        if ($settings->exists()) {
            $settings = $settings->first();
            $pickup_wise_accounts = array_map('intval', explode(',', $settings->text));
        }
        return view('admin.settings.pickup_address_wise_payment_accounts')->with(['shippers' => $shippers, 'pickup_wise_accounts' => $pickup_wise_accounts]);
    }

    public function pickup_address_wise_payment_accounts_submit(Request $request)
    {
        if ($request->has('shippers')) {
            if (count($request->shippers) > 0) {
                $shippers = implode(',', $request->shippers);
                $settings = GlobalSettings::where('type', 'pickup_wise_payment_accounts');

                if ($settings->exists()) {
                    $settings = $settings->first();
                } else {
                    $settings = new GlobalSettings();

                    $settings->type = 'pickup_wise_payment_accounts';
                    $settings->setting_value = 0;

                }
                $settings->text = $shippers;
                $settings->save();
            }
            return redirect()->back()->with('success', 'Settings Updated!');

        } else {
            return redirect()->back()->with('error', 'No shippers selected!');
        }
    }

    public function runner_report_enable_disable(Request $request)
    {
        $id = $request->id;
        $runner = Runner::where('id', $id)->first();
        if ($runner) {
            if ($runner->status == 2) {
                $runner->status = 1;
                $runner->save();
            } else {
                $runner->status = 2;
                $runner->save();
            }
            return response()->json(['status' => 1, 'success' => 'Status Successfully Updated!']);
        }
    }

    public function international_walk_in_index()
    {
        $walk_in_standard_charges = WalkInInternationalStandardWeightCharge::all();

        $cities = City::where('hub', 1)->where('business_category_id', 2)->select('id', 'name')->get();
        return view('admin.settings.international_walk_in')->with(['cities' => $cities, 'walk_in_standard_charges' => $walk_in_standard_charges]);
    }
    public function international_walk_in_store(Request $request)
    {
        WalkInInternationalStandardWeightCharge::truncate();
        WalkInInternationalStandardWeightChargeHub::truncate();

        foreach ($request->standard_charges as $index => $charge_id) {
            $standard_weight_charge = new WalkInInternationalStandardWeightCharge();
            $standard_weight_charge->id = $charge_id;
            $standard_weight_charge->shipping_mode_id = 2;
            $standard_weight_charge->hub_actual_weight = $request->hub_actual_weight[$charge_id];
            $standard_weight_charge->hub_chargeable_weight = $request->hub_chargeable_weight[$charge_id];
            $standard_weight_charge->hub_return_charges = $request->hub_return_charges[$charge_id];
            $standard_weight_charge->door_actual_weight = $request->door_actual_weight[$charge_id];
            $standard_weight_charge->door_chargeable_weight = $request->door_chargeable_weight[$charge_id];
            $standard_weight_charge->door_return_charges = $request->door_return_charges[$charge_id];
            $standard_weight_charge->save();

            foreach ($request->hubs[$charge_id] as $hub_id) {
                $standard_weight_charge_hub = new WalkInInternationalStandardWeightChargeHub();
                $standard_weight_charge_hub->international_charges_id = $charge_id;
                $standard_weight_charge_hub->hub_id = $hub_id;
                $standard_weight_charge_hub->save();
            }
        }

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function month_closing_type_index()
    {
        return view('admin.settings.month_closing.types');
    }

    public function month_closing_type_list(Request $request)
    {
        $types = MonthClosingType::select('id', 'name');
        return Datatables::of($types)
            ->addColumn('action', function ($types) {
                $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

                $dropdown .= '<button type="button" class="dropdown-item edit" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';

                return $dropdown;
            })
            ->make(true);
    }

    public function month_closing_type_add(Request $request)
    {
        $type = trim($request->type);
        if ($type) {
            $closing_type = new MonthClosingType();
            $closing_type->name = $type;
            $closing_type->save();

            return response()->json(['status' => 1, 'success' => 'Month Closing Type successfully added!']);
        } else {
            return response()->json(['status' => 0, 'error' => 'Month Closing Type is empty']);
        }
    }

    public function month_closing_type_edit(Request $request)
    {
        $type_id = $request->type_id;
        if ($type_id) {
            $type = MonthClosingType::find($type_id);
            $type->name = $request->type_name;
            $type->save();

            return response()->json(['status' => 1, 'success' => 'Month Closing Type successfully updated!']);
        } else {
            return response()->json(['status' => 0, 'error' => 'Month Closing Type is empty!']);
        }
    }

    public function month_closing_status_index()
    {
        return view('admin.settings.month_closing.status');
    }

    public function month_closing_status_list(Request $request)
    {
        $types = MonthClosingStatus::select('id', 'name');
        return Datatables::of($types)
            ->addColumn('action', function ($types) {
                $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

                $dropdown .= '<button type="button" class="dropdown-item edit" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';

                return $dropdown;
            })
            ->make(true);
    }

    public function month_closing_status_add(Request $request)
    {
        $status = trim($request->status);
        if ($status) {
            $closing_status = new MonthClosingStatus();
            $closing_status->name = $status;
            $closing_status->save();

            return response()->json(['status' => 1, 'success' => 'Month Closing Status successfully added!']);
        } else {
            return response()->json(['status' => 0, 'error' => 'Month Closing Status is empty']);
        }
    }

    public function month_closing_status_edit(Request $request)
    {
        $status_id = $request->status_id;
        if ($status_id) {
            $month_closing_status = MonthClosingStatus::find($status_id);
            $month_closing_status->name = $request->status_name;
            $month_closing_status->save();

            return response()->json(['status' => 1, 'success' => 'Month Closing Status successfully updated!']);
        } else {
            return response()->json(['status' => 0, 'error' => 'Month Closing Status is empty!']);
        }
    }

    public function international_rates_index()
    {
        $fuel_charges = '';
        $fuel_surcharge = GlobalSettings::where('type', 'international_fuel_surcharge');
        if ($fuel_surcharge->exists()) {
            $fuel_surcharge = $fuel_surcharge->first();
            $fuel_charges = (float) $fuel_surcharge->text;
        }
        $exchange_rate_charges = '';
        $exchange_rate = GlobalSettings::where('type', 'international_exchange_rate');
        if ($exchange_rate->exists()) {
            $exchange_rate = $exchange_rate->first();
            $exchange_rate_charges = (float) $exchange_rate->text;
        }
        $gst = '';
        $gst_charges = GlobalSettings::where('type', 'international_gst_rate');
        if ($gst_charges->exists()) {
            $gst_charges = $gst_charges->first();
            $gst = (float) $gst_charges->text;
        }
        return view('admin.settings.international.index')->with(['fuel_surcharge' => $fuel_charges, 'exchange_rate' => $exchange_rate_charges, 'gst' => $gst]);
    }

    public function international_rates_update(Request $request)
    {
        $fuel_surcharge_rate = GlobalSettings::where('type', 'international_fuel_surcharge');
        if ($fuel_surcharge_rate->exists()) {
            $fuel_surcharge_rate = $fuel_surcharge_rate->first();
        } else {
            $fuel_surcharge_rate = new GlobalSettings();
            $fuel_surcharge_rate->type = 'international_fuel_surcharge';
        }
        $fuel_surcharge_rate->setting_value = $request->fuel_surcharge;
        $fuel_surcharge_rate->text = $request->fuel_surcharge;
        $fuel_surcharge_rate->save();
        $exchange_rate_value = GlobalSettings::where('type', 'international_exchange_rate');
        if ($exchange_rate_value->exists()) {
            $exchange_rate_value = $exchange_rate_value->first();
        } else {
            $exchange_rate_value = new GlobalSettings();
            $exchange_rate_value->type = 'international_exchange_rate';
        }
        $exchange_rate_value->setting_value = $request->exchange_rate;
        $exchange_rate_value->text = $request->exchange_rate;
        $exchange_rate_value->save();

        $gst = GlobalSettings::where('type', 'international_gst_rate');
        if ($gst->exists()) {
            $gst = $gst->first();
        } else {
            $gst = new GlobalSettings();
            $gst->type = 'international_gst_rate';
        }
        $gst->setting_value = $request->gst;
        $gst->text = $request->gst;
        $gst->save();

        return redirect()->back()->with('success', 'Settings Updated!');

    }

    public function rider_ticker_index()
    {
        $rider_ticker = RiderTickerImage::orderBy('id', 'ASC')->get();
        $admin_ticker = AdminAppSlider::orderBy('id', 'ASC')->get();
        $retail_ticker = RetailAppSlider::orderBy('id', 'ASC')->get();
        return view('admin.settings.rider_ticker')->with(['id' => 1, 'rider_ticker' => $rider_ticker, 'admin_ticker' => $admin_ticker, 'retail_ticker' => $retail_ticker]);
    }

    public function rider_ticker_store(Request $request)
    {
        $request->validate([
            'upload_image_1' => 'nullable|image|mimes:jpeg,png|max:2048',
            'upload_image_2' => 'nullable|image|mimes:jpeg,png|max:2048',
            'upload_image_3' => 'nullable|image|mimes:jpeg,png|max:2048',
            'upload_image_4' => 'nullable|image|mimes:jpeg,png|max:2048',
            'upload_image_5' => 'nullable|image|mimes:jpeg,png|max:2048',
        ]);

        if (!$request->hasFile('upload_image_1') && !$request->hasFile('upload_image_2') && !$request->hasFile('upload_image_3') && !$request->hasFile('upload_image_4') && !$request->hasFile('upload_image_5')) {
            return redirect()->back()->with(['error' => 'No Image Provided']);
        }

        if ($request->hasFile('upload_image_1')) {
            if ($request->has('rider_ticker_id_1')) {
                $ticker_id = $request->get('rider_ticker_id_1');
                $rider_ticker = RiderTickerImage::find($ticker_id);
                Storage::disk('public')->delete($rider_ticker->picture_path);
            } else {

                $rider_ticker = new RiderTickerImage();
                $rider_ticker->save();
            }

            $picture_path = 'rider_ticker/' . $rider_ticker->id . '.png';
            Storage::disk('public')->put($picture_path, file_get_contents($request->upload_image_1));
            $rider_ticker->picture_path = $picture_path;
            $rider_ticker->save();
        }
        if ($request->hasFile('upload_image_2')) {
            if ($request->has('rider_ticker_id_2')) {
                $ticker_id = $request->get('rider_ticker_id_2');
                $rider_ticker = RiderTickerImage::find($ticker_id);
                Storage::disk('public')->delete($rider_ticker->picture_path);
            } else {

                $rider_ticker = new RiderTickerImage();
                $rider_ticker->save();
            }

            $picture_path = 'rider_ticker/' . $rider_ticker->id . '.png';
            Storage::disk('public')->put($picture_path, file_get_contents($request->upload_image_2));
            $rider_ticker->picture_path = $picture_path;
            $rider_ticker->save();
        }
        if ($request->hasFile('upload_image_3')) {
            if ($request->has('rider_ticker_id_3')) {
                $ticker_id = $request->get('rider_ticker_id_3');
                $rider_ticker = RiderTickerImage::find($ticker_id);
                Storage::disk('public')->delete($rider_ticker->picture_path);
            } else {

                $rider_ticker = new RiderTickerImage();
                $rider_ticker->save();
            }

            $picture_path = 'rider_ticker/' . $rider_ticker->id . '.png';
            Storage::disk('public')->put($picture_path, file_get_contents($request->upload_image_3));
            $rider_ticker->picture_path = $picture_path;
            $rider_ticker->save();
        }
        if ($request->hasFile('upload_image_4')) {
            if ($request->has('rider_ticker_id_4')) {
                $ticker_id = $request->get('rider_ticker_id_4');
                $rider_ticker = RiderTickerImage::find($ticker_id);
                Storage::disk('public')->delete($rider_ticker->picture_path);
            } else {

                $rider_ticker = new RiderTickerImage();
                $rider_ticker->save();
            }

            $picture_path = 'rider_ticker/' . $rider_ticker->id . '.png';
            Storage::disk('public')->put($picture_path, file_get_contents($request->upload_image_4));
            $rider_ticker->picture_path = $picture_path;
            $rider_ticker->save();
        }
        if ($request->hasFile('upload_image_5')) {
            if ($request->has('rider_ticker_id_5')) {
                $ticker_id = $request->get('rider_ticker_id_5');
                $rider_ticker = RiderTickerImage::find($ticker_id);
                Storage::disk('public')->delete($rider_ticker->picture_path);
            } else {

                $rider_ticker = new RiderTickerImage();
                $rider_ticker->save();
            }

            $picture_path = 'rider_ticker/' . $rider_ticker->id . '.png';
            Storage::disk('public')->put($picture_path, file_get_contents($request->upload_image_5));
            $rider_ticker->picture_path = $picture_path;
            $rider_ticker->save();
        }
        return redirect()->back()->with(['success' => 'Images Uploaded!']);
    }

    public function international_rates_upload_index()
    {    ActivityTrailController::createActivityTrailLog(Auth::id(),381);
        return view('admin.settings.international.excel_upload');
    }

    public function international_standard_dhl_rates_list(Request $request)
    {
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),382);
        }
        $rates_list = InternationalStandardDhlRate::select('id', 'range_up', 'range_down', 'zone_1', 'zone_2', 'zone_3', 'zone_4', 'zone_5', 'zone_6', 'zone_7', 'zone_8', 'zone_9', 'zone_10', 'zone_11');

        return Datatables::of($rates_list)->make(true);
    }
    public function international_rates_upload_excel(Request $request)
    {

        $names = [
            'range_up' => 'Range Up',
            'range_down' => 'Range Down',
            'zone_1' => 'Zone 1',
            'zone_2' => 'Zone 2',
            'zone_3' => 'Zone 3',
            'zone_4' => 'Zone 4',
            'zone_5' => 'Zone 5',
            'zone_6' => 'Zone 6',
            'zone_7' => 'Zone 7',
            'zone_8' => 'Zone 8',
            'zone_9' => 'Zone 9',
            'zone_10' => 'Zone 10',
            'zone_11' => 'Zone 11',
        ];

        $messages = [
            'required' => ':attribute is Required.',
            'integer' => ':attribute must be an Integer.',
            'exists' => 'Given :attribute is Invalid.',
        ];
        $rules = [
            'range_up' => ['required', 'numeric', 'between:0.01,300', Rule::exists('international_standard_dhl_rates', 'range_up')],
            'range_down' => ['required', 'numeric', 'between:0.01,300', Rule::exists('international_standard_dhl_rates', 'range_down')],
            'zone_1' => ['required', 'numeric', 'between:0,1000000'],
            'zone_2' => ['required', 'numeric', 'between:0,1000000'],
            'zone_3' => ['required', 'numeric', 'between:0,1000000'],
            'zone_4' => ['required', 'numeric', 'between:0,1000000'],
            'zone_5' => ['required', 'numeric', 'between:0,1000000'],
            'zone_6' => ['required', 'numeric', 'between:0,1000000'],
            'zone_7' => ['required', 'numeric', 'between:0,1000000'],
            'zone_8' => ['required', 'numeric', 'between:0,1000000'],
            'zone_9' => ['required', 'numeric', 'between:0,1000000'],
            'zone_10' => ['required', 'numeric', 'between:0,1000000'],
            'zone_11' => ['required', 'numeric', 'between:0,1000000'],
        ];

        $fields = [0 => 'range_up', 1 => 'range_down', 2 => 'zone_1', 3 => 'zone_2', 4 => 'zone_3', 5 => 'zone_4', 6 => 'zone_5', 7 => 'zone_6', 8 => 'zone_7', 9 => 'zone_8', 10 => 'zone_9', 11 => 'zone_10', 12 => 'zone_11'];

        if ($file = $request->file('rates')) {
            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

            $header = ['Range Up', 'Range Down', 'Zone 1', 'Zone 2', 'Zone 3', 'Zone 4', 'Zone 5', 'Zone 6', 'Zone 7', 'Zone 8', 'Zone 9', 'Zone 10', 'Zone 11'];

            if (isset($spreadsheet)) {
                $header_correct = true;

                foreach ($spreadsheet[0] as $index => $header_value) {
                    if ($index == 12) {
                    } elseif (!isset($header[$index]) || $header_value != $header[$index]) {
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
                $rate_range_ids = array();
                $rate_range_id_row = array();
                foreach ($rows as $key => $row) {
                    $row_id = $key + 2;

                    $validate = Validator::make($row, $rules, $messages);

                    $validate->setAttributeNames($names);

                    if ($validate->fails()) {
                        $errors['Row #' . $row_id] = $validate->errors()->all();
                    }

                    if (empty($errors['Row #' . $row_id])) {
                        if ((!empty(trim($row['range_up']))) && (!empty(trim($row['range_down'])))) {
                            if (empty($rate_range_ids)) {
                                $rate_range_ids[] = $row['range_up'];
                                $rate_range_id_row[$row['range_up']] = $row_id;
                            } else {
                                if (in_array($row['range_up'], $rate_range_ids)) {
                                    $errors['Row #' . $row_id][] = 'Same Range as of Row #' . $rate_range_id_row[$row['range_up']];
                                } else {
                                    $rate_range_ids[] = $row['range_up'];
                                    $rate_range_id_row[$row['range_up']] = $row_id;
                                }
                            }
                        }
                        if (!InternationalStandardDhlRate::where('range_up', $row['range_up'])->where('range_down', $row['range_down'])->exists()) {
                            $errors['Row #' . $row_id][] = 'Range does not exists at #' . $row[$row_id];
                        }
                    }
                }
                if (empty($errors)) {
                    $updated = 0;
                    $not_updated = 0;
                    foreach ($rows as $key => $row) {
                        $row_id = $key + 2;
                        $range_up = trim($row['range_up']);
                        $range_down = trim($row['range_down']);
                        $zone_1 = trim($row['zone_1']);
                        $zone_2 = trim($row['zone_2']);
                        $zone_3 = trim($row['zone_3']);
                        $zone_4 = trim($row['zone_4']);
                        $zone_5 = trim($row['zone_5']);
                        $zone_6 = trim($row['zone_6']);
                        $zone_7 = trim($row['zone_7']);
                        $zone_8 = trim($row['zone_8']);
                        $zone_9 = trim($row['zone_9']);
                        $zone_10 = trim($row['zone_10']);
                        $zone_11 = trim($row['zone_11']);

                        $standard_rate = InternationalStandardDhlRate::where('range_up', $range_up)->where('range_down', $range_down);
                        if ($standard_rate->exists()) {
                            $standard_rate = $standard_rate->first();
                            $standard_rate->zone_1 = ($zone_1 != null) ? $zone_1 : 0;
                            $standard_rate->zone_2 = ($zone_2 != null) ? $zone_2 : 0;
                            $standard_rate->zone_3 = ($zone_3 != null) ? $zone_3 : 0;
                            $standard_rate->zone_4 = ($zone_4 != null) ? $zone_4 : 0;
                            $standard_rate->zone_5 = ($zone_5 != null) ? $zone_5 : 0;
                            $standard_rate->zone_6 = ($zone_6 != null) ? $zone_6 : 0;
                            $standard_rate->zone_7 = ($zone_7 != null) ? $zone_7 : 0;
                            $standard_rate->zone_8 = ($zone_8 != null) ? $zone_8 : 0;
                            $standard_rate->zone_9 = ($zone_9 != null) ? $zone_9 : 0;
                            $standard_rate->zone_10 = ($zone_10 != null) ? $zone_10 : 0;
                            $standard_rate->zone_11 = ($zone_11 != null) ? $zone_11 : 0;
                            $standard_rate->save();
                            $updated++;
                        } else {
                            $not_updated++;
                        }

                    }
                    $error_msg = '';
                    if ($not_updated > 1) {
                        $error_msg = 'Total ' . $not_updated . ' rows could not updated!';
                    }

                    return redirect()->back()->with(['success' => 'Total ' . $updated . ' rows updated', 'error' => $error_msg]);
                } else {
                    $errors = array_map(function ($row, $errors) {
                        return $row . ':' . PHP_EOL . implode(' | ', $errors);
                    }, array_keys($errors), $errors);

                    return redirect()->back()->withErrors($errors);
                }

            } else {
                return redirect()->back()->with('error', 'No Rates in File');
            }

        }
    }

    public function rider_incentive_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(),244);
        $rider_categories = RiderCategory::whereIn('id', [1, 2])->select('id', 'name')->get();
        $shipment_payment_types = RidersShipmentPaymentType::select('id', 'name')->get();
        $weight_ranges = RidersShipmentWeightRange::select('id', 'name')->get();
        return view('admin.settings.rider_incentive.index')->with(['rider_categories' => $rider_categories, 'payment_types' => $shipment_payment_types, 'weight_ranges' => $weight_ranges]);
    }

    public function rider_incentive_list(Request $request)
    {
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),245);
        }
        $types = RidersIncentiveSetting::join('rider_categories as rc', 'rc.id', '=', 'riders_incentive_settings.rider_category_id')
            ->join('riders_shipment_payment_types as rspt', 'rspt.id', '=', 'riders_incentive_settings.rider_shipment_payment_type_id')
            ->join('riders_shipment_weight_ranges as rswr', 'rswr.id', '=', 'riders_incentive_settings.rider_shipment_weight_range_id')
            ->join('admins as ab', 'ab.id', '=', 'riders_incentive_settings.added_by')
            ->leftjoin('admins as ub', 'ub.id', '=', 'riders_incentive_settings.last_updated_by')
            ->select('riders_incentive_settings.id as row_id', 'riders_incentive_settings.value', 'rc.name as rider_category', 'rspt.name as payment_type', 'rswr.name as weight_range', 'ab.name as added_by', 'ub.name as last_updated_by', 'riders_incentive_settings.created_at as added_at', 'riders_incentive_settings.updated_at');
        return Datatables::of($types)
            ->addColumn('action', function ($types) {
                $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

                $dropdown .= '<button type="button" class="dropdown-item edit" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';

                return $dropdown;
            })
            ->make(true);
    }

    public function rider_incentive_store(Request $request)
    {

        $names = [
            'rider_category_select' => 'Rider Category',
            'delivery_payment_select' => 'Rider Delivery Payment',
            'weight_range_select' => 'Rider Weight Range',
            'incentive_value' => 'Rider Incentive/Shipment',

        ];

        $messages = [
            'required' => ':attribute is Required.',
            'integer' => ':attribute must be an Integer.',
            'numeric' => ':attribute must be a Number.',
            'boolean' => ':attribute must be 0 or 1.',
            'digits_between' => ':attribute must be between :min and :max Digits.',
            'exists' => 'Given :attribute is of Invalid ID.',

        ];

        $rules = [
            'rider_category_select' => ['required', 'integer', 'digits_between:1,10', 'exists:rider_categories,id'],
            'delivery_payment_select' => ['required', 'integer', 'digits_between:1,10', 'exists:riders_shipment_payment_types,id'],
            'weight_range_select' => ['required', 'integer', 'digits_between:1,10', 'exists:riders_shipment_weight_ranges,id'],
            'incentive_value' => ['required', 'integer', 'digits_between:1,100000'],
        ];

        $validate = Validator::make($request->all(), $rules, $messages);

        $validate->setAttributeNames($names);

        if ($validate->fails()) {
            return redirect()->back()->with(['errors' => $validate->errors()]);
        } else {
            $rider_category_id = $request->rider_category_select;
            $delivery_payment_type_id = $request->delivery_payment_select;
            $weight_range_id = $request->weight_range_select;
            $value = $request->incentive_value;

            $setting = RidersIncentiveSetting::where(['rider_category_id' => $rider_category_id, 'rider_shipment_payment_type_id' => $delivery_payment_type_id, 'rider_shipment_weight_range_id' => $weight_range_id]);
            if ($setting->exists()) {
                return redirect()->back()->with('error', 'Setting already exists!');
            } else {
                $rider_setting = new RidersIncentiveSetting();
                $rider_setting->rider_category_id = $rider_category_id;
                $rider_setting->rider_shipment_payment_type_id = $delivery_payment_type_id;
                $rider_setting->rider_shipment_weight_range_id = $weight_range_id;
                $rider_setting->value = $value;
                $rider_setting->added_by = Auth::id();
                $rider_setting->save();

                return redirect()->back()->with('success', 'Setting updated successfully!');
            }
        }

    }

    public function rider_incentive_details(Request $request)
    {
        $id = $request->id;
        if ($id) {
            $rider_incentive = RidersIncentiveSetting::find($id);
            if ($rider_incentive) {
                return response()->json(['status' => 0, 'details' => $rider_incentive]);
            } else {
                return response()->json(['status' => 1, 'error' => 'Setting not found!']);
            }
        } else {
            return response()->json(['status' => 1, 'error' => 'Request error!']);
        }
    }

    public function rider_incentive_update(Request $request)
    {

        $incentive_setting_id = $request->incentive_setting_id;
        $value = $request->edit_incentive_value;
        if ($value == '') {
            return redirect()->back()->with('error', 'Value not entered!');
        }

        $setting = RidersIncentiveSetting::where('id', $incentive_setting_id);
        if (!$setting->exists()) {
            return redirect()->back()->with('error', 'Setting not found!');
        } else {
            $rider_setting = $setting->first();
            $rider_setting->value = $value;
            $rider_setting->last_updated_by = Auth::id();
            $rider_setting->save();

            return redirect()->back()->with('success', 'Setting updated successfully!');
        }

    }

    public function rider_incentive_cron_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(),241);
        $value = null;
        $settings = GlobalSettings::where('type', 'rider_incentive_cron_time')->first();
        if ($settings) {
            $value = $settings->setting_value;
        }
        return view('admin.settings.rider_incentive.rider_incentive_cron')->with('value', $value);
    }

    public function rider_incentive_cron_store(Request $request)
    {
        $settings = GlobalSettings::where('type', 'rider_incentive_cron_time');

        if ($settings->exists()) {
            $settings = $settings->first();

        } else {
            $settings = new GlobalSettings();
            $settings->type = 'rider_incentive_cron_time';
        }
        $settings->setting_value = $request->rider_incentive_cron_time;
        $settings->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function rcp_tat_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(),238);
        $tat_options = RcpTatOption::all();
        return view('admin.settings.rcp_tat_view')->with(['tat_options' => $tat_options]);
    }

    public function rcp_tat_list(Request $request)
    {
        $shippers = User::join('rcp_tat_options as tat_option', 'users.rcp_tat_option_id', '=', 'tat_option.id')
            ->leftJoin('admins as a', 'a.id', '=', 'users.rcp_tat_updated_by')
            ->select(['users.id as id', 'users.name as shipper', 'users.rcp_tat_updated_at as updated_at', 'a.name as updated_by', 'tat_option.name as tat', 'users.rcp_tat_option_id as tat_id'])
            ->where([['users.status', 3], ['users.blacklist', 0]]);

        return Datatables::of($shippers)
            ->addColumn('action', function ($shippers) {
                $dropdown = '';
                if (session('role_id') == 1 || count(array_intersect([489], session('permissions'))) !== 0) {
                    $dropdown = '
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                            <div class="dropdown-menu dropdown-menu-sm">
                        ';

                    $dropdown .= '<button type="button" class="dropdown-item edit" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';
                }
                return $dropdown;
            })
            ->make(true);
    }

    public function rcp_tat_update(Request $request)
    {
        $shipper_ids = explode(',', $request->shipper_id);
        User::whereIn('id', $shipper_ids)->update([
            'rcp_tat_option_id' => $request->tat_option,
            'rcp_tat_updated_by' => Auth::id(),
            'rcp_tat_updated_at' => now(),
        ]);

        return back()->with(['success' => 'Shipper TAT Updated Successfully']);
    }

    public function fleet_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(),246);
        $vehicles = VehicleType::all();
        $drivers = FleetDriver::all();
        $vendor = FleetVendor::all();
        return view('admin.settings.fleet_index')->with(['vehicles'=> $vehicles,'drivers' => $drivers,'vendors' => $vendor]);
    }

    public function fleet_list(Request $request)
    {
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),247);
        }
        // $fleet = Fleet::all();
        $fleet = Fleet::leftjoin('vehicle_types as vt','fleets.vehicle_type_id','=','vt.id')
            ->leftjoin('fleet_drivers as fd','fd.id','=','fleets.driver_id')
            ->leftjoin('fleet_vendors as fv','fv.id','=','fleets.vendor_id')
            ->select(['fleets.id','fleets.created_at', 'fleets.reg_number', 'fleets.tracking_id', 'fleets.status', 'vt.name as vehicle_type','fd.name as driver','fv.name as vendor'])
            ->orderBy('fleets.created_at','desc');
        // ->select();

        $datatable = Datatables::of($fleet)
            ->editColumn('status', function ($fleet) {
                if ($fleet->status == 1) {
                    return 'Enable';
                } else {
                    return 'Disable';
                }
            })
            // ->editColumn('vehicle_type_id', function ($fleet) {

            //         return $fleet->vehicle_type->name;
            // })
            ->addColumn('action', function ($fleet) {
                $enable = '<button type="button" class="dropdown-item status"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Enable</div></button>';
                $disable = '<button type="button" class="dropdown-item status"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Disable</div></button>';

                $dropdown = '
                    <div class="btn-group">
                      <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                      <div class="dropdown-menu dropdown-menu-sm">';
                $dropdown .= '<button type="button" class="dropdown-item edit_fleet" data-target-id=' . $fleet->id . ' rel="edit_fleet"  data-toggle="modal" data-target="#editFleet"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';

                if ($fleet->status == 0) {
                    $dropdown .= $enable;
                }
                if ($fleet->status == 1) {
                    $dropdown .= $disable;
                }
                return $dropdown;

            });
        return $datatable->make(true);
    }

    public function fleet_store(Request $request)
    {

        $vehicle_select = $request->vehicle_select;
        if ($vehicle_select == 'other') {
            $vehicle_type = new VehicleType;
            $vehicle_type->name = $request->vehicle_type_name;
            $vehicle_type->save();

            $fleet = new Fleet;
            $fleet->reg_number = $request->reg_number;
            $fleet->vehicle_type_id = $vehicle_type->id;
            $fleet->tracking_id = $request->tracking_id;
            $fleet->status = 1;

            $fleet->save();
            return redirect()->back()->with('success', 'Fleet Added successfully!');

        } else {


            $fleet = new Fleet;
            $fleet->reg_number = $request->reg_number;
            $fleet->vehicle_type_id = $request->vehicle_select;
            $fleet->tracking_id = $request->tracking_id;
            $fleet->driver_id = $request->driver;
            $fleet->vendor_id = $request->vendor;
            $fleet->status = 1;
            $fleet->save();
            return redirect()->back()->with('success', 'Fleet Added successfully!');

        }
    }

    public function fleet_edit($id){
        $vehicles = VehicleType::all();
        $fleet = Fleet::find($id);
        $drivers = FleetDriver::all();
        $vendors = FleetVendor::all();
        return view('admin.settings.fleet_edit', compact('fleet','vehicles','drivers','vendors'));

    }

    public function fleet_update(Request $request, $id)
    {  
        $vehicle_select = $request->vehicle_select;
        if ($vehicle_select == 'other') {
            $vehicle_type = new VehicleType;
            $vehicle_type->name = $request->vehicle_type_name;
            $vehicle_type->save();

            $fleet = Fleet::find($id);
            $fleet->reg_number = $request->reg_number;
            $fleet->vehicle_type_id = $vehicle_type->id;
            $fleet->tracking_id = $request->tracking_id;
            $fleet->driver_id = $request->driver;
            $fleet->vendor_id = $request->vendor;
            $fleet->save();
            return redirect()->back()->with('success', 'Fleet Updated successfully!');

        }else{
            $fleet = Fleet::find($id);
            $fleet->reg_number = $request->reg_number;
            $fleet->vehicle_type_id = $request->vehicle_select;
            $fleet->tracking_id = $request->tracking_id;
            $fleet->driver_id = $request->driver;
            $fleet->vendor_id = $request->vendor;
            $fleet->save();
            return redirect()->back()->with('success', 'Fleet Updated successfully!');

        }

    }
    public function fleet_enable_disable(Request $request){
        $id = $request->id;
        $fleet = Fleet::find($id);
        if ($fleet) {
            if ($fleet->status == 0) {
                $fleet->status = 1;
                $fleet->save();
            } else {
                $fleet->status = 0;
                $fleet->save();
            }
            return response()->json(['status' => 1, 'success' => 'Status Successfully Updated!']);
        }
    }

    public function fleet_unique(Request $request)
    {
        if ($request->filled('reg_number')) {
            $fleet = Fleet::where('reg_number', $request->input('reg_number'));

            if (!$fleet->exists()) {
                return 'true';
            } else {
                return 'false';
            }
        } else {
            return 'true';
        }
    }

    public function route_management_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),248);
        $cities = City::where('status', 1)->where('hub', 1)->get();
        return view('admin.settings.route_management_index')->with('cities', $cities);

    }


    public function route_management_edit($id){
        $cities = City::where('status', 1)->where('hub', 1)->get();
        $route_management = RouteManagement::find($id);
        return view('admin.settings.route_management_edit', compact('cities','route_management'));
    }

    public function route_management_update(Request $request, $id){

        $route_management = RouteManagement::find($id);
        $route_management->route_code = $request->route_code;
        $route_management->route_title = $request->route_title;
        $route_management->starting_point_id = $request->starting_point_id;
        $route_management->end_point_id = $request->end_point_id;
        $route_management->save();
        foreach ($route_management->junctions as $value) {
            $route_management_junction = RouteManagementJunction::find($value->id);
            $route_management_junction->delete();
        }
        foreach ($request->edit_junction as $value) {
            $route_management_junction = new RouteManagementJunction;
            $route_management_junction->junction_id = $value;
            $route_management_junction->route_management_id = $route_management->id;
            $route_management_junction->save();
        }
        return redirect()->back()->with('success', 'Route Updated successfully!');
    }
    public function route_management_list(Request $request)
    {
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),249);
        }
        $route_management = RouteManagement::leftjoin('cities as stp','route_managements.starting_point_id','stp.id')
            ->leftjoin('cities as endp','route_managements.end_point_id','endp.id')
            ->select('route_managements.id','route_managements.created_at','route_managements.route_code','route_managements.status', 'route_managements.route_title', 'stp.id as starting_id', 'stp.name as starting_name', 'stp.hub_location_latitude as starting_lat', 'stp.hub_location_longitude as starting_long', 'endp.id as end_id', 'endp.name as end_name', 'endp.hub_location_latitude as end_lat', 'endp.hub_location_longitude as end_long')
            ->orderBy('route_managements.created_at','desc');
        $datatable = Datatables::of($route_management)
            ->editColumn('status', function ($route_management) {
                if ($route_management->status == 1) {
                    return 'Enable';
                } else {
                    return 'Disable';
                }
            })->editColumn('starting_id', function ($route_management) {
                // $route_management->starting_id.'-'.
                return "<a href='https://www.google.com/maps/?q=".$route_management->starting_lat.",".$route_management->starting_long."' target='_blank' class='btn btn-sm btn-outline-info align-middle'><i class='ft-map-pin'></i></a> ".$route_management->starting_name;
            })
            ->editColumn('end_id', function ($route_management) {
                return "<a href='https://www.google.com/maps/?q=".$route_management->end_lat.",".$route_management->end_long."' target='_blank' class='btn btn-sm btn-outline-info align-middle'><i class='ft-map-pin'></i></a> ".$route_management->end_name;

                return $route_management->end_id.'-'.$route_management->end_name;
            })

            ->addColumn('junctions', function ($route_management) {
                $junctions = RouteManagementJunction::where('route_management_id',$route_management->id)->get();
                $junction_data = '';


                foreach ($junctions as $junction) {
                    $city = City::find($junction->junction_id);
                    $junction_data.= "<a href='https://www.google.com/maps/?q=".$city->hub_location_latitude.",".$city->hub_location_longitude."' target='_blank' class='btn btn-sm btn-outline-info align-middle'><i class='ft-map-pin'></i></a> ".$city->name ."<br><br>";

                }
                return $junction_data;
                // return $route_management->id;
            })
            ->addColumn('excel_junctions', function ($route_management) {
                $junctions = RouteManagementJunction::where('route_management_id',$route_management->id)->get();
                $junction_data = '';


                foreach ($junctions as $value) {
                    $junction_data.= $value->junction->name.' , ';

                }
                return $junction_data;
                // return $route_management->id;
            })

            ->addColumn('action', function ($fleet) {
                $enable = '<button type="button" class="dropdown-item status"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Enable</div></button>';
                $disable = '<button type="button" class="dropdown-item status"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Disable</div></button>';

                $dropdown = '
                    <div class="btn-group">
                      <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                      <div class="dropdown-menu dropdown-menu-sm">';
                $dropdown .= '<button type="button" class="dropdown-item edit_route_management"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';

                if ($fleet->status == 0) {
                    $dropdown .= $enable;
                }
                if ($fleet->status == 1) {
                    $dropdown .= $disable;
                }
                return $dropdown;

            });
        return $datatable->make(true);

    }
    public function route_management_unique(Request $request)
    {
        if ($request->filled('route_code')) {
            $fleet = RouteManagement::where('route_code', $request->input('route_code'));

            if (!$fleet->exists()) {
                return 'true';
            } else {
                return 'false';
            }
        } else {
            return 'true';
        }
    }

    public function route_management_store(Request $request)
    {
        $route_management = new RouteManagement;
        $route_management->route_code = $request->route_code;
        $route_management->route_title = $request->route_title;
        $route_management->starting_point_id = $request->starting_point_id;
        $route_management->end_point_id = $request->end_point_id;
        $route_management->status = 1;
        $route_management->save();

        foreach ($request->junction as $junction_id) {
            $route_management_junction = new RouteManagementJunction;
            $route_management_junction->junction_id = $junction_id;
            $route_management_junction->route_management_id = $route_management->id;
            $route_management_junction->save();
        }
        return redirect()->back()->with('success', 'Route Added successfully!');

    }

    public function route_management_enable_disable(Request $request){
        $id = $request->id;
        $route_management = RouteManagement::find($id);
        if ($route_management) {
            if ($route_management->status == 0) {
                $route_management->status = 1;
                $route_management->save();
            } else {
                $route_management->status = 0;
                $route_management->save();
            }
            return response()->json(['status' => 1, 'success' => 'Status Successfully Updated!']);
        }
    }

    public function debriefing_time_setting_index(){

        ActivityTrailController::createActivityTrailLog(Auth::id(),445);
        $settings = GlobalSettings::where('type', 'debriefing_time_setting');

        if ($settings->exists()) {
            $settings = $settings->first();
            $time = $settings->text;
        }
        else {
            $time = 0;
        }
       
        return view('admin.settings.debriefing_time_setting_index')->with(['time' => $time]);

    }

    public function debriefing_time_setting_update(Request $request){
        $settings = GlobalSettings::where('type', 'debriefing_time_setting');

        if ($settings->exists()) {
            $settings = $settings->first();
            $settings->text = $request->debriefing_time;
        }
        else {
            $settings = new GlobalSettings();
            $settings->text = $request->debriefing_time;
            $settings->type = 'debriefing_time_setting';
            $settings->setting_value = 0;
        }
        $settings->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function shipment_status_eta_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(),449);
        $shipment_status = ShipmentStatus::whereNotIn('id', [1, 17])->where('status', 1)->select(['id', 'name'])->get();

        return view('admin.settings.telenor.shipment_status_eta')->with(['shipment_status' => $shipment_status]);
    }

    public function shipment_status_eta_list(Request $request)
    {
        $shipment_status = TelenorShipmentStatusEstimatedTime::join('shipment_status as ss', 'ss.id', '=', 'telenor_shipment_status_estimated_times.shipper_status_id')
            ->select('telenor_shipment_status_estimated_times.id', 'telenor_shipment_status_estimated_times.shipper_status_id','ss.name as status_name', 'telenor_shipment_status_estimated_times.eta as eta', 'telenor_shipment_status_estimated_times.updated_at');

        $datatable = Datatables::of($shipment_status)
            ->addColumn('action', function ($data) {
                $dropdown = '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">';
                $dropdown .= '<button type="button" class="dropdown-item edit" data-eta="'. $data->eta .'" data-status="' . $data->shipper_status_id .'"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Edit</div></button>';
                $dropdown .= '
                    </div>
                  </div>
          ';
                return $dropdown;

            });
        return $datatable->make(true);
    }

    public function shipment_status_eta_store(Request $request){
        $status_id = $request->shipment_status_id;
        $eta = $request->eta;

        if($status_id && $eta){
            $setting = TelenorShipmentStatusEstimatedTime::where('shipper_status_id', $status_id);
            if($setting->exists()){
                return response()->json(['status' => 0, 'error' => 'Setting already exists!']);
            }

            $setting = new TelenorShipmentStatusEstimatedTime();
            $setting->shipper_status_id = $status_id;
            $setting->eta = $eta;
            $setting->save();

            return response()->json(['status' => 1, 'success' => 'Setting Updated Successfully!']);
        }
        else{
            return response()->json(['status' => 0, 'error' => 'Some Data missing!']);
        }
    }

    public function shipment_status_eta_edit(Request $request){
        $eta = $request->eta;
        $id = $request->id;
        if($eta && $id){
            $setting = TelenorShipmentStatusEstimatedTime::where('id', $id);
            if($setting->exists()){
                $setting = $setting->first();
                $setting->eta = $eta;
                $setting->save();

                return response()->json(['status' => 1, 'success' => 'Setting Updated Successfully!']);
            }
            return response()->json(['status' => 0, 'error' => 'Setting does not exists!']);

        }
        else{
            return response()->json(['status' => 0, 'error' => 'Some Data missing!']);
        }
    }

    public function fleet_store_driver(Request $request){

        $driver = new FleetDriver();
        $driver->name = $request->driver_name;
        $driver->phone_no = $request->phone_number;
        $driver->cnic_no = $request->cnic;
        $driver->save();

        return redirect()->back()->with('success', 'Driver Added!');
    }

    public function fleet_cnic_unique(Request $request)
    {
        if ($request->filled('cnic')) {
            $driver = FleetDriver::where('cnic_no', $request->input('cnic'));

            if (!$driver->exists()) {
                return 'true';
            } else {
                return 'false';
            }
        } else {
            return 'true';
        }
    }

    public function fleet_store_vendor(Request $request){

        $vendors = FleetVendor::pluck('name')->toArray();
        $vendor_names = explode(',', $request->vendor_name);

        foreach($vendor_names as $vendor_name){
            if(!in_array($vendor_name,$vendors)){
                FleetVendor::create([
                    'name' => $vendor_name,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Vendor Added!');
    }
    public function rider_shipment_attempt_settings_index()
    {
        $settings = GlobalSettings::whereIn('type', ['rider_shipment_attempt_count', 'rider_shipment_attempt_waiting_duration'])->get();

        return view('admin.settings.rider_shipment_attempt')->with('settings', $settings);
    }

    public function rider_shipment_attempt_settings_store(Request $request)
    {
        $settings = GlobalSettings::where('type', 'rider_shipment_attempt_count')->first();
        $settings->setting_value = $request->shipment_attempt_count;
        $settings->save();

        $settings = GlobalSettings::where('type', 'rider_shipment_attempt_waiting_duration')->first();
        $settings->setting_value = $request->shipment_attempt_duration;
        $settings->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function last_mile_cron_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),446);
        $settings = GlobalSettings::where('type', 'last_mile_cron_time')->first();

        $default_time = NULL;
        if ($settings) {
            $default_time = $settings->setting_value;
        }
        return view('admin.settings.last_mile.last_mile_cron')->with(['value' => $default_time]);
    }

    public function last_mile_cron_store(Request $request){
        $settings = GlobalSettings::where('type', 'last_mile_cron_time');

        if ($settings->exists()) {
            $settings = $settings->first();
        } else {
            $settings = new GlobalSettings();

            $settings->type = 'last_mile_cron_time';
        }

        $settings->setting_value = $request->last_mile_cron_time;

        $settings->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function dhl_sync_time_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),447);
        $settings = GlobalSettings::where('type', 'dhl_sync_time_1')->first();

        $default_time_1 = NULL;
        if ($settings) {
            $default_time_1 = $settings->setting_value;
        }

        $settings = GlobalSettings::where('type', 'dhl_sync_time_2')->first();

        $default_time_2 = NULL;
        if ($settings) {
            $default_time_2 = $settings->setting_value;
        }
        return view('admin.settings.international.dhl_sync_time')->with(['time_1' => $default_time_1, 'time_2' => $default_time_2]);
    }

    public function dhl_sync_time_store(Request $request){
        $settings_1 = GlobalSettings::where('type', 'dhl_sync_time_1');

        if ($settings_1->exists()) {
            $settings_1 = $settings_1->first();
        } else {
            $settings_1 = new GlobalSettings();

            $settings_1->type = 'dhl_sync_time_1';
        }

        $settings_1->setting_value = $request->shipment_sync_time_1;

        $settings_1->save();

        $settings_2 = GlobalSettings::where('type', 'dhl_sync_time_2');

        if ($settings_2->exists()) {
            $settings_2 = $settings_2->first();
        } else {
            $settings_2 = new GlobalSettings();

            $settings_2->type = 'dhl_sync_time_2';
        }

        $settings_2->setting_value = $request->shipment_sync_time_2;

        $settings_2->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function international_automation_user_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),448);
        $settings = GlobalSettings::where('type', 'dhl_user_id')->first();

        $dhl_user_id = NULL;
        if ($settings) {
            $dhl_user_id = $settings->setting_value;
        }
        return view('admin.settings.international.automation_user')->with(['dhl_user_id' => $dhl_user_id]);
    }

    public function international_automation_user_store(Request $request){
        $settings = GlobalSettings::where('type', 'dhl_user_id');

        if ($settings->exists()) {
            $settings = $settings->first();
        } else {
            $settings = new GlobalSettings();

            $settings->type = 'dhl_user_id';
        }

        $settings->setting_value = $request->dhl_user_id;

        $settings->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function crm_auto_assigning_index(){
        
        ActivityTrailController::createActivityTrailLog(Auth::id(),469);
        $agents = Admin::select('id', 'name')->whereIn('role_id',[37,28])->get();//37,28 role
        $zones = Zone::where('status',1)->where('business_category_id',1)->get();
        // $case_natures = CrmRequestCaseNature::whereIn('id',[1,2])->get();

        return view('admin.settings.CRM.auto_assigning')->with(['agents' => $agents , 'zones' => $zones]);
    }

    public function crm_auto_assigning_list(){
        $roles = CrmAgent::join('admins as ad', 'ad.id', '=', 'crm_agents.admin_id')
                 ->join('zones as z','z.id','crm_agents.zone_id')   
        ->select('crm_agents.id', 'ad.name as agent_name', 'z.name as zone_name','crm_agents.status as status','crm_agents.case_nature_id as case_nature');
        
    $datatables = Datatables::of($roles)
        ->addColumn('action', function($roles) {
            if (session('role_id') == 1 || in_array(618, session('permissions'))) {
                if($roles->id == 1 || $roles->id == 2){
                    $dropdown = '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                    ';
                    
                    if($roles->status == 1 ){

                        $dropdown .=' <button type="button" class="dropdown-item enable_disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-minus-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';
                    }else{

                        $dropdown .=' <button type="button" class="dropdown-item enable_disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
                    }
                    $dropdown .='</div>
                    </div>
                        ';
  
                    return $dropdown;
                }else{
                    $dropdown = '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                    <button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>
                    ';
                    // $dropdown .=' <button type="button" class="dropdown-item delete"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-minus-circle"></i></div><div class="col-9 offset-1">Delete</div></button>';
                    if($roles->status == 1 ){

                        $dropdown .=' <button type="button" class="dropdown-item enable_disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-minus-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';
                    }else{

                        $dropdown .=' <button type="button" class="dropdown-item enable_disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
                    }
                    
                    $dropdown .='</div>
                  </div>
          ';

          return $dropdown;
                }
               
            }
            else {
                return '';
            }
        })
        ->editColumn('zone_name', function($roles) {
                if($roles->id == 1 || $roles->id == 2){
                    return '-';
                }else{
                    return $roles->zone_name;
                }
               
        })->editColumn('status', function($roles) {
            if($roles->status == 1){
                return 'Enable';
            }else{
                return 'Disable';
            }
            
        })->editColumn('case_nature', function($roles) {
            if($roles->case_nature == 4){
                return 'Claim';
            }else{
                return 'Complaints / Service Request';
            }
            
        });

    return $datatables->make(true);
    }

    public function crm_auto_assigning_submit(Request $request){
        $crm_agent = CrmAgent::where('admin_id',$request->admin_id);
        if(!$crm_agent->exists()){

            CrmAgent::create($request->all());
            return redirect()->back()->with('success', 'Agent Added!');
        }else{
            return redirect()->back()->with('error', 'Agent Already Exists!');

        }
    }

    public function crm_auto_assigning_data(Request $request){
        $crm_agent_data = CrmAgent::find($request->id);

        $agent_id = $crm_agent_data->admin_id;
        $zone_id = $crm_agent_data->zone_id;
        $case_nature_id = $crm_agent_data->case_nature_id;
        $crm_agent_id = $crm_agent_data->id;
        return response()->json(['status' => 1, 'agent_id' => $agent_id,'zone_id' => $zone_id ,'crm_agent_id'=> $crm_agent_id ,'case_nature_id'=> $case_nature_id]);

    }

    public function crm_auto_assigning_delete(Request $request){
        CrmAgent::find($request->id)->delete();
        return response()->json(['status' => 1, 'success' => 'Assigned Agent Deleted']);

    }


    public function crm_auto_assigning_update(Request $request){
        $crm_agent_data = CrmAgent::find($request->crm_agent_id);

        $crm_agent_data->admin_id = $request->admin_id;
        $crm_agent_data->zone_id = $request->zone_id;
        $crm_agent_data->case_nature_id = $request->case_nature_id;
        $crm_agent_data->save();
        return redirect()->back()->with('success', 'Agent Updated!');

    }

    public function crm_auto_assigning_enable_disable(Request $request){
        $crm_agent = CrmAgent::find($request->id);
        if($crm_agent->status == 1){
            $crm_agent->status = 0;
            $crm_agent->save();
        return redirect()->back()->with('success', 'Agent Disabled!');

        }else{
            $crm_agent->status = 1;
            $crm_agent->save();
        return redirect()->back()->with('success', 'Agent Enabled!');

        }
    }
public function sales_incentive()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(),460);
        $incentives = SalesDesignation::where('status', 1)->get();
        $date = SalesIncentiveDate::first();
        if($date){
            $date = Carbon::parse($date->to)->toDateString();
        }
        else{
            $date = Carbon::now()->toDateString();
        }
//        if($incentives){
            return view('admin.settings.sales.incentive')->with(['incentives' => $incentives, 'date' => $date]);
//        }
//        else{
//            return redirect()->back()->with('error', 'Sales Designations not set!');
//        }

    }

    public function sales_incentive_add(Request $request)
    {
        if(count($request->designations) > 0)
        {
            foreach ($request->designations as $designation_id => $incentive){
                $sales_designation_journeys = new SalesDesignationJourney();
                $sales_designation_journeys->sales_designation_id = $designation_id;
                $sales_designation_journeys->incentive = $incentive;
                $sales_designation_journeys->updated_by = Auth::id();
                $sales_designation_journeys->save();

                $designations = SalesDesignation::find($designation_id);
                $designations->incentive = $incentive;
                $designations->updated_by = Auth::id();
                $designations->save();
            }
            SalesIncentiveDate::truncate();
            $to_date = Carbon::parse($request->date_formatted)->endOfDay();
            $cron_day = Carbon::parse($request->date_formatted)->addDay()->format('d');
            $from_date = Carbon::parse($request->date_formatted)->subMonth()->addDay()->startOfDay();
            $sale_incentive_date = new SalesIncentiveDate();
            $sale_incentive_date->from = $from_date;
            $sale_incentive_date->to = $to_date;
            $sale_incentive_date->cron_day = $cron_day;
            $sale_incentive_date->save();


            return redirect()->back()->with('success', 'Incentive Added Successfully!');
        }
        else
        {
            return redirect()->back()->with('error', 'Incentive Not Added Successfully!');
        }
    }


    public function crm_auto_tagging_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),474);
        $agents = Admin::select('id', 'name')->whereIn('role_id', [9,10,11,33,55])->where('status',1)->get();//37,28 role
        $cities = city::where('status',1)->get();
        return view('admin.settings.CRM.auto_tagging')->with(['agents' => $agents , 'cities' => $cities]);
    }

    public function crm_auto_tagging_list(){
        $roles = CrmAutoTagUser::join('admins as ad', 'ad.id', '=', 'crm_auto_tag_users.admin_id')
                 ->join('cities as c','c.id','crm_auto_tag_users.city_id')   
        ->select('crm_auto_tag_users.id', 'ad.name as agent_name', 'c.name as city_name','crm_auto_tag_users.status');
        
    $datatables = Datatables::of($roles)
        ->addColumn('action', function($roles) {
            if (session('role_id') == 1 || in_array(640, session('permissions'))) {
                    $dropdown = '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                    <button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>
                    ';
                    // $dropdown .=' <button type="button" class="dropdown-item delete"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-minus-circle"></i></div><div class="col-9 offset-1">Delete</div></button>';
                    if($roles->status == 1 ){

                        $dropdown .=' <button type="button" class="dropdown-item enable_disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-minus-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';
                    }else{

                        $dropdown .=' <button type="button" class="dropdown-item enable_disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
                    }
                    
                    $dropdown .='</div>
                  </div>
          ';

          return $dropdown;
               
            }
            else {
                return '';
            }
        })->editColumn('status', function($roles) {
            if($roles->status == 1){
                return 'Enable';
            }else{
                return 'Disable';
            }
            
        });

    return $datatables->make(true);
    }

    public function crm_auto_tagging_submit(Request $request){
        $crm_agent = CrmAutoTagUser::where('city_id',$request->city_id);
        if(!$crm_agent->exists()){
            CrmAutoTagUser::create($request->all());
            return redirect()->back()->with('success', 'Agent Added!');
        }else{
            return redirect()->back()->with('error', 'Location already exist, Please edit the Tagged user');

        }


    }

    public function crm_auto_tagging_data(Request $request){
        $crm_agent_data = CrmAutoTagUser::find($request->id);

        $agent_id = $crm_agent_data->admin_id;
        $city_id = $crm_agent_data->city_id;
        $crm_agent_id = $crm_agent_data->id;
        return response()->json(['status' => 1, 'agent_id' => $agent_id,'city_id' => $city_id ,'crm_agent_id'=> $crm_agent_id]);

    }

    public function crm_auto_tagging_delete(Request $request){
        CrmAutoTagUser::find($request->id)->delete();
        return response()->json(['status' => 1, 'success' => 'Tagged Agent Deleted']);

    }


    public function crm_auto_tagging_update(Request $request){
        $crm_agent_data = CrmAutoTagUser::find($request->crm_agent_id);

        $crm_agent_data->admin_id = $request->admin_id;
        $crm_agent_data->city_id = $request->city_id;
        $crm_agent_data->save();
        return redirect()->back()->with('success', 'Agent Updated!');

    }

    public function crm_auto_tagging_enable_disable(Request $request){
        $crm_agent = CrmAutoTagUser::find($request->id);
        if($crm_agent->status == 1){
            $crm_agent->status = 0;
            $crm_agent->save();
        return redirect()->back()->with('success', 'Agent Disabled!');

        }else{
            $crm_agent->status = 1;
            $crm_agent->save();
        return redirect()->back()->with('success', 'Agent Enabled!');

        }
    }

    public function admin_ticker_store(Request $request)
    {
        $request->validate([
            'upload_image_6' => 'nullable|image|mimes:jpeg,png|max:2048',
            'upload_image_7' => 'nullable|image|mimes:jpeg,png|max:2048',
            'upload_image_8' => 'nullable|image|mimes:jpeg,png|max:2048',
            'upload_image_9' => 'nullable|image|mimes:jpeg,png|max:2048',
            'upload_image_10' => 'nullable|image|mimes:jpeg,png|max:2048',
        ]);

        if (!$request->hasFile('upload_image_6') && !$request->hasFile('upload_image_7') && !$request->hasFile('upload_image_8') && !$request->hasFile('upload_image_9') && !$request->hasFile('upload_image_10')) {
            return redirect()->back()->with(['error' => 'No Image Provided']);
        }

        if ($request->hasFile('upload_image_6')) {
            if ($request->has('admin_ticker_id_1')) {
                $ticker_id = $request->get('admin_ticker_id_1');
                $admin_ticker = AdminAppSlider::find($ticker_id);
                Storage::disk('public')->delete($admin_ticker->picture_path);
            } else {

                $admin_ticker = new AdminAppSlider();
                $admin_ticker->save();
            }

            $picture_path = 'admin_ticker/' . $admin_ticker->id . '.png';
            Storage::disk('public')->put($picture_path, file_get_contents($request->upload_image_6));
            $admin_ticker->picture_path = $picture_path;
            $admin_ticker->save();
        }
        if ($request->hasFile('upload_image_7')) {
            if ($request->has('admin_ticker_id_2')) {
                $ticker_id = $request->get('admin_ticker_id_2');
                $admin_ticker = AdminAppSlider::find($ticker_id);
                Storage::disk('public')->delete($admin_ticker->picture_path);
            } else {

                $admin_ticker = new AdminAppSlider();
                $admin_ticker->save();
            }

            $picture_path = 'admin_ticker/' . $admin_ticker->id . '.png';
            Storage::disk('public')->put($picture_path, file_get_contents($request->upload_image_7));
            $admin_ticker->picture_path = $picture_path;
            $admin_ticker->save();
        }
        if ($request->hasFile('upload_image_8')) {
            if ($request->has('admin_ticker_id_3')) {
                $ticker_id = $request->get('admin_ticker_id_3');
                $admin_ticker = AdminAppSlider::find($ticker_id);
                Storage::disk('public')->delete($admin_ticker->picture_path);
            } else {

                $admin_ticker = new AdminAppSlider();
                $admin_ticker->save();
            }

            $picture_path = 'admin_ticker/' . $admin_ticker->id . '.png';
            Storage::disk('public')->put($picture_path, file_get_contents($request->upload_image_8));
            $admin_ticker->picture_path = $picture_path;
            $admin_ticker->save();
        }
        if ($request->hasFile('upload_image_9')) {
            if ($request->has('admin_ticker_id_4')) {
                $ticker_id = $request->get('admin_ticker_id_4');
                $admin_ticker = AdminAppSlider::find($ticker_id);
                Storage::disk('public')->delete($admin_ticker->picture_path);
            } else {

                $admin_ticker = new AdminAppSlider();
                $admin_ticker->save();
            }

            $picture_path = 'admin_ticker/' . $admin_ticker->id . '.png';
            Storage::disk('public')->put($picture_path, file_get_contents($request->upload_image_9));
            $admin_ticker->picture_path = $picture_path;
            $admin_ticker->save();
        }
        if ($request->hasFile('upload_image_10')) {
            if ($request->has('admin_ticker_id_5')) {
                $ticker_id = $request->get('admin_ticker_id_5');
                $admin_ticker = AdminAppSlider::find($ticker_id);
                Storage::disk('public')->delete($admin_ticker->picture_path);
            } else {

                $admin_ticker = new AdminAppSlider();
                $admin_ticker->save();
            }

            $picture_path = 'admin_ticker/' . $admin_ticker->id . '.png';
            Storage::disk('public')->put($picture_path, file_get_contents($request->upload_image_10));
            $admin_ticker->picture_path = $picture_path;
            $admin_ticker->save();
        }
        return redirect()->back()->with(['success' => 'Images Uploaded!']);
    }

    public function retail_ticker_store(Request $request)
    {
        $request->validate([
            'upload_image_11' => 'nullable|image|mimes:jpeg,png|max:2048',
            'upload_image_12' => 'nullable|image|mimes:jpeg,png|max:2048',
            'upload_image_13' => 'nullable|image|mimes:jpeg,png|max:2048',
            'upload_image_14' => 'nullable|image|mimes:jpeg,png|max:2048',
            'upload_image_15' => 'nullable|image|mimes:jpeg,png|max:2048',
        ]);

        if (!$request->hasFile('upload_image_11') && !$request->hasFile('upload_image_12') && !$request->hasFile('upload_image_13') && !$request->hasFile('upload_image_14') && !$request->hasFile('upload_image_15')) {
            return redirect()->back()->with(['error' => 'No Image Provided']);
        }

        if ($request->hasFile('upload_image_11')) {
            if ($request->has('retail_ticker_id_1')) {
                $ticker_id = $request->get('retail_ticker_id_1');
                $retail_ticker = RetailAppSlider::find($ticker_id);
                Storage::disk('public')->delete($retail_ticker->picture_path);
            } else {

                $retail_ticker = new RetailAppSlider();
                $retail_ticker->save();
            }

            $picture_path = 'retail_ticker/' . $retail_ticker->id . '.png';
            Storage::disk('public')->put($picture_path, file_get_contents($request->upload_image_11));
            $retail_ticker->picture_path = $picture_path;
            $retail_ticker->save();
        }
        if ($request->hasFile('upload_image_12')) {
            if ($request->has('retail_ticker_id_2')) {
                $ticker_id = $request->get('retail_ticker_id_2');
                $retail_ticker = RetailAppSlider::find($ticker_id);
                Storage::disk('public')->delete($retail_ticker->picture_path);
            } else {

                $retail_ticker = new RetailAppSlider();
                $retail_ticker->save();
            }

            $picture_path = 'retail_ticker/' . $retail_ticker->id . '.png';
            Storage::disk('public')->put($picture_path, file_get_contents($request->upload_image_12));
            $retail_ticker->picture_path = $picture_path;
            $retail_ticker->save();
        }
        if ($request->hasFile('upload_image_13')) {
            if ($request->has('retail_ticker_id_3')) {
                $ticker_id = $request->get('retail_ticker_id_3');
                $retail_ticker = RetailAppSlider::find($ticker_id);
                Storage::disk('public')->delete($retail_ticker->picture_path);
            } else {

                $retail_ticker = new RetailAppSlider();
                $retail_ticker->save();
            }

            $picture_path = 'retail_ticker/' . $retail_ticker->id . '.png';
            Storage::disk('public')->put($picture_path, file_get_contents($request->upload_image_13));
            $retail_ticker->picture_path = $picture_path;
            $retail_ticker->save();
        }
        if ($request->hasFile('upload_image_14')) {
            if ($request->has('retail_ticker_id_4')) {
                $ticker_id = $request->get('retail_ticker_id_4');
                $retail_ticker = RetailAppSlider::find($ticker_id);
                Storage::disk('public')->delete($retail_ticker->picture_path);
            } else {

                $retail_ticker = new RetailAppSlider();
                $retail_ticker->save();
            }

            $picture_path = 'retail_ticker/' . $retail_ticker->id . '.png';
            Storage::disk('public')->put($picture_path, file_get_contents($request->upload_image_14));
            $retail_ticker->picture_path = $picture_path;
            $retail_ticker->save();
        }
        if ($request->hasFile('upload_image_15')) {
            if ($request->has('retail_ticker_id_5')) {
                $ticker_id = $request->get('retail_ticker_id_5');
                $retail_ticker = RetailAppSlider::find($ticker_id);
                Storage::disk('public')->delete($retail_ticker->picture_path);
            } else {

                $retail_ticker = new RetailAppSlider();
                $retail_ticker->save();
            }

            $picture_path = 'retail_ticker/' . $retail_ticker->id . '.png';
            Storage::disk('public')->put($picture_path, file_get_contents($request->upload_image_15));
            $retail_ticker->picture_path = $picture_path;
            $retail_ticker->save();
        }
        return redirect()->back()->with(['success' => 'Images Uploaded!']);
    }

    public function status_webhook_index(Request $request)
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 478);
        return view('admin.settings.shipper.status_webhook_index');
    }

    public function status_webhook_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 479);
        }
        $shippers = ShipmentStatusSubscription::leftjoin('users as s', 's.id', '=', 'shipment_status_subscriptions.user_id')
            ->select('s.id as account_id', 's.name as shipper_name', 'shipment_status_subscriptions.url as url','shipment_status_subscriptions.id as id')->where('shipment_status_subscriptions.status', 1);

        if(session('role_id') != 1)
        {
            $shippers = $shippers
                ->leftjoin('sale_person_tags as spt',function($join){
                    $join->on('spt.user_id','=','s.id')
                        ->where('spt.status',0);
                })
                ->leftjoin('sale_tier_tags as stt',function($join){
                    $join->on('stt.user_id','=','s.id')
                        ->where('stt.kam','!=',null);
                })
                ->where(function($q){
                        $q->where('spt.admin_id',Auth::id())
                            ->orWhere('stt.kam',Auth::id());
                });
        }

        $datatable = Datatables::of($shippers)
            ->addColumn('action', function ($shipper) {
                if (session('role_id') == 1 || in_array(646, session('permissions'))) {
                    $route = route('admin.settings.shippers.status_webhook.edit',$shipper->account_id);
                    $dropdown = '
                          <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                            <div class="dropdown-menu dropdown-menu-sm">
                                <a href="'.$route.'" class="dropdown-item">Update Status Mapping</a></div></div>';

                    return $dropdown;
                }
                return '';
            });
        return $datatable->make(true);
    }

    public function status_webhook_edit($id, Request $request)
    {
        $webhook = ShipmentStatusSubscription::where('user_id',$id)->first();

        if($webhook)
        {
            if(session('role_id') != 1)
            {
                $spt = SalePersonTag::where('user_id',$id)->where('admin_id',Auth::id())->where('status',0);
                $stt = SaleTierTag::where('user_id',$id)->where('kam',Auth::id());

                if($spt->doesntExist() && $stt->doesntExist())
                {
                    return back()->with(['error'=>"Shipper Not Assigned to you"]);
                }
            }
            $statuses = ShipmentStatus::where('status',1)->get();
            $shippers_statuses = ShipmentStatusesForShipperWebhook::where('user_id',$webhook->user_id)->get(['status_id','webhook_status']);


            $shipper_statuses = array();
            foreach ($shippers_statuses as $status)
            {
                $shipper_statuses[$status->status_id] = $status->webhook_status;
            }

            return view('admin.settings.shipper.status_webhook_edit',compact('statuses','shipper_statuses','webhook'));
        }
        return back()->with(['error'=>"Invalid Shipper ID"]);
    }

    public function status_webhook_update(Request $request)
    {

        ActivityTrailController::createActivityTrailLog(Auth::id(), 480);
        ShipmentStatusesForShipperWebhook::where('user_id',$request->shipper_id)->delete();

        foreach ($request->webhook_status as $key => $status)
        {
           if($status != null)
           {
               $shipper_status = new ShipmentStatusesForShipperWebhook();
               $shipper_status->user_id = $request->shipper_id;
               $shipper_status->status_id = $key;
               $shipper_status->webhook_status = $status;
               $shipper_status->save();
           }
        }

        return redirect()->route('admin.settings.shippers.status_webhook.index')->with(['success'=>'Shipper Statuses Updated Successfully']);
    }

 public function omni_user_setting_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),483);
        
        $shippers = array();
        $omni_accounts = array();
        $settings = GlobalSettings::where('type', 'omni_users');
        if ($settings->exists()) {
            $settings = $settings->first();
            if($settings->text != NULL){
                $omni_accounts = array_map('intval', explode(',', $settings->text));
            }
        }
        $users = User::where('status',3)->where('blacklist', 0)->select('id','name')->get();
        return view('admin.settings.omni_user')->with(['shippers' => $omni_accounts,'users' => $users]);
    }

    public function omni_user_setting_update(Request $request){
        if ($request->has('shippers')) {
            if (count($request->shippers) > 0) {
                $shippers = implode(',', $request->shippers);
                $settings = GlobalSettings::where('type', 'omni_users');

                if ($settings->exists()) {
                    $settings = $settings->first();
                } else {
                    $settings = new GlobalSettings();

                    $settings->type = 'omni_users';
                    $settings->setting_value = 0;

                }
                $settings->text = $shippers;
                $settings->save();
            }
            return redirect()->back()->with('success', 'Settings Updated!');

        } else {
            return redirect()->back()->with('error', 'No shippers selected!');
        }

    }

	public function reattempt_percentage_index()
    {
        $settings = GlobalSettings::where('type', 'reattempt_percentage');
        $percentage = '';
        if ($settings->exists()) {
            $settings = $settings->first();
            $percentage = $settings->setting_value;
        }

        $settings = GlobalSettings::where('type', 'reattempt_count');
        $count = '';
        if ($settings->exists()) {
            $settings = $settings->first();
            $count = $settings->setting_value;
        }
        
        $settings = GlobalSettings::where('type', 'reattempt_flag');
        $switch = 1;
        if ($settings->exists()) {
            $settings = $settings->first();
            $switch = $settings->setting_value;
        }

        return view('admin.settings.return.reattempt_percentage')->with(['percentage' => $percentage, 'count' => $count, 'switch' => $switch]);
    }

    public function reattempt_percentage_store(Request $request)
    {
        if($request->has('on_default')){

            $settings = GlobalSettings::where('type', 'reattempt_percentage');
            if ($settings->exists()) {
                $settings = $settings->first();
            } else {
                $settings = new GlobalSettings();
                $settings->type = 'reattempt_percentage';
            }
            $settings->setting_value = $request->reattempt_percentage;
    
            $settings->save();

            $settings = GlobalSettings::where('type', 'reattempt_count');
            if ($settings->exists()) {
                $settings = $settings->first();
            } else {
                $settings = new GlobalSettings();
                $settings->type = 'reattempt_count';
            }
            $settings->setting_value = $request->reattempt_count;
    
            $settings->save();


            $settings = GlobalSettings::where('type', 'reattempt_flag');
            if ($settings->exists()) {
                $settings = $settings->first();
            } else {
                $settings = new GlobalSettings();
                $settings->type = 'reattempt_flag';
            }
            $settings->setting_value = 1;
    
            $settings->save();
        }else{
            $settings = GlobalSettings::where('type', 'reattempt_flag');
            if ($settings->exists()) {
                $settings = $settings->first();
            } else {
                $settings = new GlobalSettings();
                $settings->type = 'reattempt_flag';
            }
            $settings->setting_value = 0;
    
            $settings->save();
        }

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function lead_tagging_index(){
        
        ActivityTrailController::createActivityTrailLog(Auth::id(),493);
        $agents = Admin::join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.name','admins.id'])->where('status', 1)->where('ar.department_id',7)->get();
        // $agents = Admin::select('id', 'name')->whereIn('role_id', [9,10,11,33,55])->where('status',1)->get();//37,28 role
        $services = DB::table('service_list')->where('status',1)->get();
        $cities = City::where('status',1)->get();
        $territories = Territory::all();
        $zones = Zone::where('zones.status',1)->where('zones.business_category_id',1)->select('zones.id as id','zones.name as name')->get();

        return view('admin.settings.lead_management.auto_tagging')->with(['agents' => $agents , 'cities' => $cities , 'services' => $services , 'zones' => $zones , 'territories' => $territories ]);
    }

    public function lead_tagging_list(Request $request){
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 494);
        }
        $roles = LeadTagging::join('admins as ad', 'ad.id', '=', 'lead_taggings.sale_person_id')
        ->leftjoin('zones as z','z.id','lead_taggings.zone_id')   
        // ->leftjoin('service_list as s','s.id','lead_taggings.service_id')
        ->leftjoin('territories as t','t.id','lead_taggings.territory_id')   
        ->leftjoin('cities as c','c.id','lead_taggings.city_id')   
        ->select('lead_taggings.id', 'ad.name as agent_name', 'c.name as city_name', 't.name as territory_name', 'z.name as zone', DB::raw('(SELECT COUNT(id) FROM lead_tagging_services WHERE lead_tagging_id = lead_taggings.id) AS service2'),'lead_taggings.status');
        // ->select('lead_taggings.id','lead_taggings.service_id as service', 'ad.name as agent_name', 'c.name as city_name', 't.name as territory_name', 'z.name as zone', 's.name as service2','lead_taggings.status');
        
    $datatables = Datatables::of($roles)
        ->addColumn('action', function($roles) {
            if (session('role_id') == 1 || in_array(663, session('permissions'))) {
                    $dropdown = '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                    <button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>
                    ';
                    // $dropdown .=' <button type="button" class="dropdown-item delete"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-minus-circle"></i></div><div class="col-9 offset-1">Delete</div></button>';
                    if($roles->status == 1 ){

                        $dropdown .=' <button type="button" class="dropdown-item enable_disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-minus-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';
                    }else{

                        $dropdown .=' <button type="button" class="dropdown-item enable_disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
                    }
                    
                    $dropdown .='</div>
                  </div>
          ';

          return $dropdown;
               
            }
            else {
                return '';
            }
        })->editColumn('status', function($roles) {
            if($roles->status == 1){
                return 'Enable';
            }else{
                return 'Disable';
            }
            
        })->editColumn('zone', function($roles) {
            if($roles->zone == '' || $roles->zone == null){
                return 'All Zones';
            }else{
                return $roles->zone;
            }
            
        })->editColumn('city_name', function($roles) {
            if($roles->city_name == '' || $roles->city_name == null){
                return 'All Cities';
            }else{
                return $roles->city_name;
            }
            
        })
        ->editColumn('service', function($roles) {
            if($roles->service == '' || $roles->service == null){
                return '0';
            }else{
                return $roles->service;
            }

        })
        ->editColumn('service2', function($roles) {

            if($roles->service2 == '' || $roles->service2 == null){
                return 'All Services';
            }else{
                return $roles->service2;
            }

        })

        ->editColumn('territory_name', function($roles) {

            if($roles->territory_name == '' || $roles->territory_name == null){
                return 'All Territories';
            }else{
                return $roles->territory_name;
            }

        })
        ->addColumn('service2_link', function($roles) {
            return '<button class="btn btn-sm btn-outline-info align-middle services_link" id="'.$roles->id.'"><span class="align-middle">' . $roles->service2 . '</span></button>';
        });

    return $datatables->make(true);
    }

    public function lead_tagging_submit(Request $request){
        
        // if($request->zone_id == 0){
        //     $check_leads = LeadTagging::where('zone_id',$request->zone_id)->where('sale_person_id',$request->agent_id)->where('service_id',$request->service_id);
        //     // $check_leads = LeadTagging::where('zone_id',$request->zone_id)->where('city_id', $request->city_id)->where('territory_id', $request->territory_id)->where('service_id', $request->service_id)->where('sale_person_id',$request->agent_id)->where('status', 1)->orWhere(function ($query) use ($request){
        //     //     $query->where('zone_id', '=', '0')
        //     //     ->where('service_id', $request->service_id)
        //     //     ->where('sale_person_id',$request->agent_id)
        //     //     ->where('status', 1);
        //     // })->orWhere(function ($query) use ($request){
        //     //     $query->where('zone_id', '=', $request->zone_id)
        //     //     ->where('city_id', '=', '0')
        //     //     ->where('sale_person_id',$request->agent_id)
        //     //     ->where('service_id', $request->service_id)
        //     //     ->where('status', 1);
        //     // });
        //     if(!$check_leads->exists()){
        //         $lead_tagging = new LeadTagging;
        //         $lead_tagging->sale_person_id = $request->agent_id;
        //         $lead_tagging->zone_id = $request->zone_id;
        //         if($request->city_id){
        //             $lead_tagging->city_id = $request->city_id;
        //         }
        //         if($request->territory_id){
        //             $lead_tagging->territory_id = $request->territory_id;
        //         }
        //         $lead_tagging->service_id = $request->service_id;
        //         $lead_tagging->save();
        //         return redirect()->back()->with('success', 'Lead Agent Added!');
        //     }else{
        //         return redirect()->back()->with('error', 'Lead Agent already exist');
        //     }
        // }else{

            $check_leads = LeadTagging::where('zone_id',$request->zone_id)->where('city_id',$request->city_id)->where('sale_person_id',$request->agent_id)->where('territory_id',$request->territory_id);
    
            if(!$check_leads->exists()){
                $lead_tagging = new LeadTagging;
                $lead_tagging->sale_person_id = $request->agent_id;
                $lead_tagging->zone_id = $request->zone_id;
                $lead_tagging->city_id = $request->city_id;
                $lead_tagging->territory_id = $request->territory_id;
                $lead_tagging->save();
                foreach($request->service_id as $service_id){
                    LeadTaggingService::create([
                        'service_id' => $service_id,
                        'lead_tagging_id' => $lead_tagging->id,
                    ]);
                }
    
                return redirect()->back()->with('success', 'Lead Agent Added!');
    
            }else{
                return redirect()->back()->with('error', 'Lead Agent already exist');
            }
        // }
    }

    public function lead_tagging_data(Request $request){
        $lead_tagging = LeadTagging::find($request->id);

        $service_id = LeadTaggingService::where('lead_tagging_id',$request->id)->pluck('service_id')->toArray();
        $agent_id = $lead_tagging->sale_person_id;
        $city_id = $lead_tagging->city_id;
        $zone_id = $lead_tagging->zone_id;
        $lead_tagging_id = $lead_tagging->id;
        $territory_id = $lead_tagging->territory_id;

        return response()->json(['status' => 1, 'agent_id' => $agent_id,'city_id' => $city_id ,'zone_id'=> $zone_id ,'service_id'=> $service_id ,'lead_tagging_id'=> $lead_tagging_id ,'territory_id'=> $territory_id]);

    }


    public function lead_tagging_update(Request $request){
        $service_id = $request->service_id;
        $check_leads = LeadTagging::where('city_id',$request->city_id)
        ->where('sale_person_id',$request->agent_id)
        ->where('territory_id',$request->territory_id)
        ->where('id','<>',$request->lead_tagging_id);

        if(!$check_leads->exists()){
            
            $lead_tagging = LeadTagging::find($request->lead_tagging_id);
            $lead_tagging->sale_person_id = $request->agent_id;
            $lead_tagging->zone_id = $request->zone_id;
            $lead_tagging->city_id = $request->city_id;
            $lead_tagging->territory_id = $request->territory_id;
            $lead_tagging->save();

            LeadTaggingService::where('lead_tagging_id',$request->lead_tagging_id)->delete();

            foreach($request->service_id as $service_id){
                LeadTaggingService::create([
                    'service_id' => $service_id,
                    'lead_tagging_id' => $lead_tagging->id,
                ]);
            }
            return redirect()->back()->with('success', 'Lead Agent Updated!');
        }else{
            return redirect()->back()->with('error', 'Lead Agent already exist');

        }

    }

    public function lead_tagging_enable_disable(Request $request){
        $lead_tagging = LeadTagging::find($request->id);
        if($lead_tagging->status == 1){
            $lead_tagging->status = 0;
            $lead_tagging->save();
        return redirect()->back()->with('success', 'Lead Agent Disabled!');

        }else{
            $lead_tagging->status = 1;
            $lead_tagging->save();
        return redirect()->back()->with('success', 'Lead Agent Enabled!');

        }
    }


    public function lead_zones_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),495);
        $admins = Admin::join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.name','admins.id'])->where('status', 1)->where('ar.department_id',7)->get();

        // $admins = Admin::select('id', 'name')->whereIn('role_id', [9,10,11,33,55])->where('status',1)->get();//37,28 role
        $zones = Zone::where('status',1)->where('business_category_id',1)->get();
        return view('admin.settings.lead_management.zone_tagging')->with(['admins' => $admins , 'zones' => $zones]);
    }

    public function lead_zones_list(Request $request){
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 496);
        }
        $roles = LeadZone::join('admins as ad', 'ad.id', '=', 'lead_zones.admin_id')
                 ->join('zones as z','z.id','lead_zones.zone_id')   
        ->select('lead_zones.id', 'ad.name as agent_name', 'z.name as zone','lead_zones.status');
        
    $datatables = Datatables::of($roles)
        ->addColumn('action', function($roles) {
            if (session('role_id') == 1 || in_array(666, session('permissions'))) {
                    $dropdown = '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                    <button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>
                    ';
                    // $dropdown .=' <button type="button" class="dropdown-item delete"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-minus-circle"></i></div><div class="col-9 offset-1">Delete</div></button>';
                    if($roles->status == 1 ){

                        $dropdown .=' <button type="button" class="dropdown-item enable_disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-minus-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';
                    }else{

                        $dropdown .=' <button type="button" class="dropdown-item enable_disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
                    }
                    
                    $dropdown .='</div>
                  </div>
          ';

          return $dropdown;
               
            }
            else {
                return '';
            }
        })->editColumn('status', function($roles) {
            if($roles->status == 1){
                return 'Enable';
            }else{
                return 'Disable';
            }
            
        });

    return $datatables->make(true);
    }

    public function lead_zones_submit(Request $request){
        foreach ($request->zone_id as $zone) {
            foreach ($request->agent_id as $agent) {
                $check_leads = LeadZone::where('zone_id',$zone)->where('admin_id',$agent);
        
                if(!$check_leads->exists()){
                    $lead_zone = new LeadZone;
                    $lead_zone->admin_id = $agent;
                    $lead_zone->zone_id = $zone;
                    $lead_zone->save();
        
                    
                }
            }
        }
        return redirect()->back()->with('success', 'Agent Zone Added!');
    }

    public function lead_zones_data(Request $request){
        $lead_zone = LeadZone::find($request->id);

        $admin_id = $lead_zone->admin_id;
        $zone_id = $lead_zone->zone_id;
        $lead_zone_id = $lead_zone->id;

        return response()->json(['status' => 1, 'admin_id' => $admin_id,'zone_id'=> $zone_id ,'lead_zone_id'=> $lead_zone_id]);

    }


    public function lead_zones_update(Request $request){
        $check_leads = LeadZone::where('zone_id',$request->zone_id)->where('admin_id',$request->agent_id);

        if(!$check_leads->exists()){
            $lead_zone = LeadZone::find($request->lead_zone_id);
            $lead_zone->admin_id = $request->agent_id;
            $lead_zone->zone_id = $request->zone_id;
            $lead_zone->save();
            return redirect()->back()->with('success', 'Agent Zone Updated!');
        }else{
            return redirect()->back()->with('error', 'Agent Zone already exist');
        }
    }

    public function lead_zones_enable_disable(Request $request){
        $lead_zone = LeadZone::find($request->id);
        if($lead_zone->status == 1){
            $lead_zone->status = 0;
            $lead_zone->save();
        return redirect()->back()->with('success', 'Agent Zone Disabled!');

        }else{
            $lead_zone->status = 1;
            $lead_zone->save();
        return redirect()->back()->with('success', 'Agent Zone Enabled!');

        }
    }



    
    public function lead_notification_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),497);
        return view('admin.settings.lead_management.notification');
    }

    public function lead_notification_list(Request $request){
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 498);
        }
        $notifications = LeadNotification::join('admins as a', 'lead_notifications.updated_by', '=', 'a.id')
        ->select('lead_notifications.id', 'lead_notifications.name', 'lead_notifications.type_id as type', 'lead_notifications.updated_at', 'a.name as updated_by', 'lead_notifications.status');

        $datatables = Datatables::of($notifications)
        ->setRowAttr([
            'data-type' => function($notification) {
                return $notification->type;
            },
        ])
        ->editColumn('status', function ($notification) {
            return (($notification->status) ? 'Enabled' : 'Disabled');
        })
        
        ->editColumn('type', function ($notification) {
            if($notification->type == 1){
                return 'Email';
            }else{
                return 'SMS';
            }
        })
        ->addColumn('action', function($notification) {
            $edit_button = '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';
            $enable_button = '<button type="button" class="dropdown-item enable_disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
            $disable_button = '<button type="button" class="dropdown-item enable_disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';

            $dropdown = '
                <div class="btn-group">
                  <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                  <div class="dropdown-menu dropdown-menu-sm">
            ';

            if (session('role_id') == 1 || in_array(672, session('permissions'))) {
                $dropdown .= $edit_button;
            }

            if (session('role_id') == 1 || in_array(672, session('permissions'))) {
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
        });

        return $datatables->make(true);
    }


    public function lead_notification_data(Request $request){
        $id = $request->id;
        $lead_notification = LeadNotification::find($id);

        $details = array();
        $attachments_details = array();
        if ($lead_notification->type_id == 1) {
            $details['subject'] = $lead_notification->subject;
        }

        $details['body'] = $lead_notification->body;

        if ($id == 1) {
            $details['fields'] = ['shipper_name', 'tagged_salesperson_name', 'tagged_salesperson_number', 'tagged_salesperson_email'];
            $attachments =  LeadNotificationAttachment::where('notification_id',$id)->get();
            foreach ($attachments as $image) {
                $img_url = asset('uploads/notification_attachments/'.$image->attachment);
                $attachments_details[] = array('id' => $image->id,'date' => Carbon::parse($image->created_at)->toDateTimeString(),'image'=> $img_url);
            }
            $details['attachments'] = $attachments_details;
        }
        else if ($id == 2) {
            $details['fields'] = ['shipper_name', 'tagged_salesperson_name', 'tagged_salesperson_number', 'tagged_salesperson_email'];
        }
        else if ($id == 3) {
            $details['fields'] = ['shipper_name', 'tagged_salesperson_name', 'tagged_salesperson_number', 'tagged_salesperson_email'];
        }
        else if ($id == 4) {
            $details['fields'] = ['shipper_name', 'tagged_salesperson_name', 'tagged_salesperson_number', 'tagged_salesperson_email'];
        }
		return $details;


        return response()->json(['status' => 1, 'details' => $details]);

    }


    public function lead_notification_update(Request $request){

        $lead_notification = LeadNotification::find($request->lead_notification_id);

        $lead_notification->subject = $request->subject;
        $lead_notification->body = $request->body;

        $lead_notification->save();
        if($request->lead_notification_id == 1){
            if($request->selected_ids){
                $image_ids = explode(',', $request->selected_ids);
                foreach ($image_ids as $id){
        
                    $file_name = 'notification_image_'.$id;
                    $image = $request->file($file_name);
        
                    $extension = $image->getClientOriginalExtension();
                    $random = rand(1000, 100000);
                    $now = Carbon::now();
                    $time = $now->year . '_' . $now->month;
                    $generated_image_name = $time . $random . Auth::id() . '.' . $extension;
                    $image->move(public_path('uploads/notification_attachments'), $generated_image_name);
                    $notification_image = new LeadNotificationAttachment();
                    $notification_image->notification_id = $lead_notification->id;
                    $notification_image->added_by = Auth::id();
                    $notification_image->attachment = $generated_image_name;
                    $notification_image->save();
                }
            }

        }
        return redirect()->back()->with('success', 'Notification Updated!');
       

    }

    public function lead_notification_enable_disable(Request $request){
        $lead_notification = LeadNotification::find($request->id);
        if($lead_notification->status == 1){
            $lead_notification->status = 0;
            $lead_notification->save();
        return redirect()->back()->with('success', 'Notification Disabled!');

        }else{
            $lead_notification->status = 1;
            $lead_notification->save();
        return redirect()->back()->with('success', 'Notification Enabled!');

        }
    }
    public function lead_notification_delete_image(Request $request){
        $lead_notification_attachment = LeadNotificationAttachment::where('id',$request->image_id)->where('notification_id',$request->notification_id);
        if($lead_notification_attachment->exists()){
            $lead_notification_attachment = $lead_notification_attachment->get()->first();
            $lead_notification_attachment->delete();
            return response()->json(['status' => 0, 'success' => 'Image Deleted!']);
        } else {
            return response()->json(['status' => 1, 'error' => 'Image Not Found']);
        }
    }
    public function shippers_return_address_index()
    {
        $shippers = User::where('status', 3)->where('blacklist', 0)->select('id', 'name')->get();
        $settings = GlobalSettings::where('type', 'shipper_return_address');
        $shipper_return_address = array();
        if ($settings->exists()) {
            $settings = $settings->first();
            $shipper_return_address = array_map('intval', explode(',', $settings->text));
        }
        return view('admin.settings.shipper.shipper_return_address')->with(['shippers' => $shippers, 'shipper_return_address' => $shipper_return_address]);
    }

    public function shippers_return_address_store(Request $request)
    {
        if ($request->has('shippers')) {
            if (count($request->shippers) > 0) {
                $shippers = implode(',', $request->shippers);
                $settings = GlobalSettings::where('type', 'shipper_return_address');

                if ($settings->exists()) {
                    $settings = $settings->first();
                } else {
                    $settings = new GlobalSettings();

                    $settings->type = 'shipper_return_address';
                    $settings->setting_value = 0;

                }
                $settings->text = $shippers;
                $settings->save();
            }
            return redirect()->back()->with('success', 'Settings Updated!');

        } else {
            return redirect()->back()->with('error', 'No shippers selected!');
        }

    }

    public function shipper_origin_index(){
        $shippers = User::where('status', 3)->where('blacklist', 0)->select('id', 'name')->get();
        $settings = GlobalSettings::where('type', 'shipper_origin_change');
        $shipper_origin_change = array();
        if ($settings->exists()) {
            $settings = $settings->first();
            $shipper_origin_change = array_map('intval', explode(',', $settings->text));
        }
        return view('admin.settings.shipper.shipper_origin_change')->with(['shippers' => $shippers, 'shipper_origin_change' => $shipper_origin_change]);
    }
        
    public function shipper_origin_store(Request $request)
    {
        if ($request->has('shippers')) {
            if (count($request->shippers) > 0) {
                $shippers = implode(',', $request->shippers);
                $settings = GlobalSettings::where('type', 'shipper_origin_change');

                if ($settings->exists()) {
                    $settings = $settings->first();
                } else {
                    $settings = new GlobalSettings();

                    $settings->type = 'shipper_origin_change';
                    $settings->setting_value = 0;

                }
                $settings->text = $shippers;
                $settings->save();
            }
            return redirect()->back()->with('success', 'Settings Updated!');

        } else {
            return redirect()->back()->with('error', 'No shippers selected!');

        }

    }

    public function rcp_sms_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),508);
        $setting = GlobalSettings::where('type','return_confirmation_pending_sms')->first();
        $time = GlobalSettings::where('type','rcp_sms_cron_time')->first();
//        $data = DB::table('rcp_sms_cron_time')->get();
//        $data = GlobalSettings::where('type','rcp_sms_cron_time')->first();
        return view('admin.settings.return.rcp_sms')->with(['setting' => $setting, 'time' => $time]);
    }

    public function rcp_sms_update(Request $request){
        $setting = GlobalSettings::where('type','return_confirmation_pending_sms')->first();
        $time = GlobalSettings::where('type','rcp_sms_cron_time')->first();
        $setting->setting_value = $request->toggle_check;
        $setting->text = $request->sms_count;
        $time->text = $request->time;
        $setting->save();
        $time->save();

        return redirect()->back()->with('success','Setting Updated');
    }

//    CRON TIME SETTINGS

//        public function rcp_sms_cron_index(Request $request)
//        {
//            ActivityTrailController::createActivityTrailLog(Auth::id(),508);
//            $setting = DB::table('rcp_sms_cron_time')->where('type','return_confirmation_pending_sms')->first();
//            return view('admin.settings.return.rcp_sms');
//        }
//
//        public function rcp_sms_cron_update(Request $request)
//        {
//            $from = DB::table('rcp_sms_cron_time')->where('name','TAT Cut-Off Time From')->first();
//
//            $cut_off_time_from = $from->setting_value;
//
//            return view('admin.settings.return.rcp_sms')->with(['cut_off_time_from' => $cut_off_time_from, 'cut_off_time_to' => $cut_off_time_to]);
//        }

//      END

    public function consignee_sms_expire_index()
    {
        $settings = GlobalSettings::where('type', 'consignee_sms_expire_time')->first();

        if ($settings) {
            $consignee_sms_expire_time = $settings->setting_value;
        } else {
            $consignee_sms_expire_time = 20;
        }

        return view('admin.settings.last_mile.consignee_sms_expire_time')->with(['consignee_sms_expire_time' => $consignee_sms_expire_time]);
    }

    public function consignee_sms_expire_store(Request $request)
    {
        $settings = GlobalSettings::where('type', 'consignee_sms_expire_time');

        if ($settings->exists()) {
            $settings = $settings->first();
        } else {
            $settings = new GlobalSettings();

            $settings->type = 'consignee_sms_expire_time';
        }

        $settings->setting_value = $request->consignee_sms_expire_time;

        $settings->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }

	public function sales_user_restriction_index()
    {
        $user_ids = array();

        $settings = GlobalSettings::where('type', 'sales_user_restriction_bypass');

        if ($settings->exists()) {
            $settings = $settings->first();
            $user_ids = array_map('intval', explode(',', $settings->text));
        }

        $sale_persons = Admin::join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.name','admins.id'])->where('status', 1)->where('ar.department_id',7)->get();

        return view('admin.settings.sales.sale_person_restriction_bypass')->with(['sale_persons' => $sale_persons, 'user_ids' => $user_ids]);
    }

    public function sales_user_restriction_store(Request $request)
    {
        if ($request->has('users')) {
            $roles = implode(',', $request->users);
            $settings = GlobalSettings::where('type', 'sales_user_restriction_bypass');

            if ($settings->exists()) {
                $settings = $settings->first();
            } else {
                $settings = new GlobalSettings();

                $settings->type = 'sales_user_restriction_bypass';
                $settings->setting_value = 0;

            }
            $settings->text = $roles;
            $settings->save();
        } else {
            GlobalSettings::where('type', 'sales_user_restriction_bypass')->delete();
        }

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function return_reason_mandatory_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(), 510);
        $already_added_shippers = ReturnReasonMandatoryShipper::pluck('shipper_id')->toArray();
        $shippers = User::join('cities as c', 'users.city_id', '=', 'c.id')
            ->where('users.status', 3)->where('users.blacklist', 0)->whereNotIn('users.id',$already_added_shippers)->select('users.id as id', 'users.name as name');
        if (session('role_id') != 1) {
            $shippers = $shippers->whereIn('c.hub_id', session('hubs'));
        }
        $shippers = $shippers->get();
        return view('admin.settings.return.return_reason_mandatory',compact('shippers'));

    }

    public function return_reason_mandatory_list(Request $request){
        $shippers = ReturnReasonMandatoryShipper::join('users as u', 'return_reason_mandatory_shippers.shipper_id', 'u.id')
            ->join('admins as ad', 'return_reason_mandatory_shippers.added_by', '=', 'ad.id')
            ->join('cities as c', 'u.city_id', '=', 'c.id')
            ->select('u.name as shipper_name', 'ad.name as added_by','c.name as shipper_city', 'return_reason_mandatory_shippers.created_at as added_at');
        if (session('role_id') != 1) {
            $shippers = $shippers->whereIn('c.hub_id', session('hubs'));
        }
        $datatables = Datatables::of($shippers)
            ->editColumn('added_at', function ($shippers) {
                return Carbon::parse($shippers->added_at)->format("Y-m-d");
            });
        return $datatables->make(true);
    }

    public function return_reason_mandatory_store(Request $request)
    {
        $shipper_id = $request->shipper_id;
        $return_shipper_reason = new ReturnReasonMandatoryShipper();
        $return_shipper_reason->shipper_id = $shipper_id;
        $return_shipper_reason->added_by = Auth::id();
        $return_shipper_reason->save();
        return redirect()->back()->with('success', 'Shipper Has Been Added!');
    }
    public function cn_print_right()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 519);
        $admin_roles_id = AdminRole::all();
//        dd($admin_roles);
        $settings = GlobalSettings::where('type', 'cn_print_rights');
        $foc_account_tags = array();
        if ($settings->exists()) {
            $settings = $settings->first();
            $admin_roles = array_map('intval', explode(',', $settings->text));
        }
        return view('admin.settings.cn_print_right')->with(['admin_roles' => $admin_roles_id,'existing_admin_roles'=> $admin_roles ]);
    }
    public function cn_print_right_store(Request $request)
    {

        if ($request->has('admin_role')) {

            $newids=$request->get('admin_role');
//            $admin = Admin::select('id')->whereIn('role_id',$newids)->get();
//            $admin = $admin->pluck('id')->toArray();

            $role_ids = GlobalSettings::where('type','cn_print_rights')->first();
            $exist = $role_ids->text;
            if ($exist == null) {

                $default = 0;
                $role_ids->text = implode(",",$newids);
                $role_ids->save();
                return redirect()->back()->with('success', 'Settings Updated!');

            } else {
                $role_ids->text = null;
                $role_ids->save();

                $role_ids->text = implode(",",$newids);
////                $newids = $exist.','.implode(",",$admin);
//
//                $role_ids->text = $newids;
                $role_ids->save();
                return redirect()->back()->with('success', 'Settings Updated!');
            }
        }
        else
        {
            $role_ids = GlobalSettings::where('type','cn_print_rights')->first();
            $role_ids->text = null;
            $role_ids->save();

            return redirect()->back()->with('error', 'Updated But No Admin-Role selected!');
        }

    }

    public function bolt_update_version_index(){
        if(session('role_id') == 1){
            $settings = GlobalSettings::where('type', 'bolt_updated_version')->first();
            return view('admin.settings.bolt_update_version')->with('settings', $settings);
        }else{
            return redirect()->route('admin.access_denied');
        }
    }

    public function bolt_update_version_store(Request $request){
        if(session('role_id') == 1){
        $settings = GlobalSettings::where('type', 'bolt_updated_version')->first();
        $settings->setting_value = $request->updated_version;
        $settings->save();
        return redirect()->back()->with('success', 'Settings Updated!');
        }else{
            return redirect()->route('admin.access_denied');
        }
    }


    public function return_shipments_address_index()
    {
        $shippers = User::where('status', 3)->where('blacklist', 0)->select('id', 'name')->get();
        $settings = GlobalSettings::where('type', 'return_shipments_address_change_shippers');
        $return_shipments_address_change_shippers = array();
        if ($settings->exists()) {
            $settings = $settings->first();
            $return_shipments_address_change_shippers = array_map('intval', explode(',', $settings->text));
        }
        return view('admin.settings.shipper.return_shipments_address_change_shippers')->with(['shippers' => $shippers, 'return_shipments_address_change_shippers' => $return_shipments_address_change_shippers]);
    }

    public function return_shipments_address_store(Request $request)
    {
        if ($request->has('shippers')) {
            if (count($request->shippers) > 0) {
                $shippers = implode(',', $request->shippers);
                $settings = GlobalSettings::where('type', 'return_shipments_address_change_shippers');

                if ($settings->exists()) {
                    $settings = $settings->first();
                } else {
                    $settings = new GlobalSettings();

                    $settings->type = 'return_shipments_address_change_shippers';
                    $settings->setting_value = 0;

                }
                $settings->text = $shippers;
                $settings->save();
            }
            return redirect()->back()->with('success', 'Settings Updated!');

        } else {
            return redirect()->back()->with('error', 'No shippers selected!');
        }

    }

    public function auto_tag_territories_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(), 518);

        $agents = Admin::join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')
                            ->select(['admins.id', 'admins.name'])
                            ->where('admins.status', 1)->where('ar.department_id', 7)->get();

        $territories = Territory::where('territory_status',1)->get();
        $cities = City::where('status',1)->get();
        return view('admin.settings.auto_tag_territory',compact('agents','territories','cities'));
    }


    public function auto_tag_territories_list(Request $request){
       
        $roles = AutoTagTerritory::join('admins as ad', 'ad.id', '=', 'auto_tag_territories.admin_id')
        ->leftjoin('territories as t','t.id','auto_tag_territories.territory_id')   
        ->leftjoin('cities as c','c.id','t.city_id')   
        ->select('auto_tag_territories.id', 'ad.name as agent_name', 'c.name as city_name', 't.name as territory_name','auto_tag_territories.status');
        
    $datatables = Datatables::of($roles)
        ->addColumn('action', function($roles) {
            if (session('role_id') == 1 || in_array(698, session('permissions'))) {
                    $dropdown = '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                    <button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>
                    ';
                    // $dropdown .=' <button type="button" class="dropdown-item delete"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-minus-circle"></i></div><div class="col-9 offset-1">Delete</div></button>';
                    if($roles->status == 1 ){

                        $dropdown .=' <button type="button" class="dropdown-item enable_disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-minus-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';
                    }else{

                        $dropdown .=' <button type="button" class="dropdown-item enable_disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
                    }
                    
                    $dropdown .='</div>
                  </div>
          ';

          return $dropdown;
               
            }
            else {
                return '';
            }
        })->editColumn('status', function($roles) {
            if($roles->status == 1){
                return 'Enable';
            }else{
                return 'Disable';
            }
            
        })->editColumn('city_name', function($roles) {
            if($roles->city_name == '' || $roles->city_name == null){
                return 'All Cities';
            }else{
                return $roles->city_name;
            }
            
        });

    return $datatables->make(true);
    }

    public function auto_tag_territories_store(Request $request){
        $check_tagging = AutoTagTerritory::where('admin_id',$request->agent_id);
            
        if(!$check_tagging->exists()){
            $auto_tagging = new AutoTagTerritory;
            $auto_tagging->admin_id = $request->agent_id;
            $auto_tagging->territory_id = $request->territory_id;
            $auto_tagging->save();

            return redirect()->back()->with('success', 'Sales Person\'s Territory Added!');

        }else{
            return redirect()->back()->with('error', 'Sales Person\'s Territory already exist');
        }
    }
    public function auto_tag_territories_enable_disable(Request $request){
        $auto_tagging = AutoTagTerritory::find($request->id);
        if($auto_tagging->status == 1){
            $auto_tagging->status = 0;
            $auto_tagging->save();
        return redirect()->back()->with('success', 'Sales Person\'s Territory Disabled!');

        }else{
            $auto_tagging->status = 1;
            $auto_tagging->save();
        return redirect()->back()->with('success', 'Sales Person\'s Territory Enabled!');

        }
    }

    public function auto_tag_territories_data(Request $request){
        $auto_tagging = AutoTagTerritory::find($request->id);

        $agent_id = $auto_tagging->admin_id;
        $territory_id = $auto_tagging->territory_id;
        $city_id = Territory::find($territory_id)->city_id;
        $auto_tagging_id = $auto_tagging->id;

        return response()->json(['status' => 1, 'agent_id' => $agent_id,'city_id' => $city_id ,'auto_tagging_id'=> $auto_tagging_id ,'territory_id'=> $territory_id]);

    }

    public function auto_tag_territories_update(Request $request){
        $auto_tagging = AutoTagTerritory::find($request->auto_tagging_id);
        if($request->agent_id == $auto_tagging->admin_id){
            $auto_tagging->territory_id = $request->territory_id;
            $auto_tagging->save();
            return redirect()->back()->with('success', 'Sales Person\'s Territory Updated!');
        }else{
            $check_tagging = AutoTagTerritory::where('admin_id',$request->agent_id);
            if(!$check_tagging->exists()){
                $auto_tagging->admin_id = $request->agent_id;
                $auto_tagging->territory_id = $request->territory_id;
                $auto_tagging->save();
                return redirect()->back()->with('success', 'Sales Person\'s Territory Updated!');
            }else{
                return redirect()->back()->with('error', 'Sales Person\'s Territory already exist');
            }
        }
    }

    public function referral(){
        
        ActivityTrailController::createActivityTrailLog(Auth::id(), 520);

        return view('admin.settings.referral');
    }

    public function referral_list(Request $request){
        $referral = Referral::join('admins as ad','ad.id','=','referrals.admin_id')
                    ->select('referrals.id','referrals.name as name','referrals.status as status','ad.name as agent_name');
        $datatables = Datatables::of($referral)
                    ->addColumn('action', function($referral) {
                        if (session('role_id') == 1 || in_array(663, session('permissions'))) {
                                $dropdown = '<div class="btn-group">
                                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                                <div class="dropdown-menu dropdown-menu-sm">
                                ';
                                
                                //  <button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>
                                if($referral->status == 1 ){
            
                                    $dropdown .=' <button type="button" class="dropdown-item enable_disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-minus-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';
                                }else{
            
                                    $dropdown .=' <button type="button" class="dropdown-item enable_disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
                                }
                                
                                $dropdown .='</div>
                              </div>
                      ';
            
                      return $dropdown;
                           
                        }
                        else {
                            return '';
                        }
                    })->editColumn('status', function($referral) {
                        if($referral->status == 1){
                            return 'Enable';
                        }else{
                            return 'Disable';
                        }
                        
                    });
            
                return $datatables->make(true);
    }
    
    public function referral_name(Request $request){
        if ($request->filled('referral_code')) {
            $referral = Referral::where('name', $request->input('referral_code'));

            if (!$referral->exists()) {
                return 'true';
            } else {
                return 'false';
            }
        } else {
            return 'false';
        }
    }

    public function referral_store(Request $request){
        $referral = new Referral;
        $referral->name = $request->referral_code;
        $referral->admin_id = session('id');
        $referral->save();
        return redirect()->back()->with('success', 'Referral Added!');

    }

    public function referral_enable_disable(Request $request){
        $referral = Referral::find($request->id);
        if($referral->status == 1){
            $referral->status = 0;
            $referral->save();
            return response()->json(['status' => 1, 'success' => 'Referral Disabled!']);

        }else{
            $referral->status = 1;
            $referral->save();
            return response()->json(['status' => 1, 'success' => 'Referral Enabled!']);

        } 
    }

    public function rider_deactivation_cron_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 525);

        $settings = GlobalSettings::whereIn('type', ['rider_deactivation_cron_days', 'rider_deactivation_cron_status'])->get();

        return view('admin.settings.rider_deactivation_cron')->with('settings', $settings);
    }

    public function rider_deactivation_cron_store(Request $request)
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 526);

        $settings = GlobalSettings::where('type', 'rider_deactivation_cron_days')->first();
        $settings->setting_value = $request->deactivation_days;
        $settings->save();

        $settings = GlobalSettings::where('type', 'rider_deactivation_cron_status')->first();
        $settings->setting_value = ($request->cron_status) ? 1 : 0;
        $settings->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function lost_shipment_shippers_index(){

        ActivityTrailController::createActivityTrailLog(Auth::id(),527);
        
        $shippers = User::where('status',3)->get();

        return view('admin.settings.lost_shipment_shippers',compact('shippers'));
    }


    public function lost_shipment_shippers_list(Request $request){

        $shippers = LostShipmentShipper::join('users as u','u.id','=','lost_shipment_shippers.user_id')
                    ->select('u.name as shipper_name','lost_shipment_shippers.id');
        $datatables = Datatables::of($shippers)
                    ->addColumn('action', function($shippers) {
                        if (session('role_id') == 1 || in_array(709, session('permissions'))) {
                                $dropdown = '<div class="btn-group">
                                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                                <div class="dropdown-menu dropdown-menu-sm">
                                ';
            
                                    $dropdown .=' <button type="button" class="dropdown-item delete"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-minus-circle"></i></div><div class="col-9 offset-1">Delete</div></button>';
                                
                                $dropdown .='</div>
                              </div>
                      ';
            
                      return $dropdown;
                           
                        }
                        else {
                            return '';
                        }
                    });
            
                return $datatables->make(true);
    }

    public function lost_shipment_shippers_add(Request $request){
        $check_shipper = LostShipmentShipper::where('user_id',$request->shipper_id);
            
            if(!$check_shipper->exists()){
                $lost_shipment_shipper = new LostShipmentShipper();
                $lost_shipment_shipper->user_id = $request->shipper_id;
                $lost_shipment_shipper->save();
    
                return redirect()->back()->with('success', 'Shipper Added!');
    
            }else{
                return redirect()->back()->with('error', 'Shipper already exist');
            }
    }

    public function lost_shipment_shippers_delete(Request $request){
        LostShipmentShipper::find($request->id)->delete();
        return redirect()->back()->with('success', 'Shipper Deleted!');
    }




    public function lost_shipment_admins_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),528);

        $admins = Admin::where('status',1)->get();

        return view('admin.settings.lost_shipment_admins',compact('admins'));
    }


    public function lost_shipment_admins_list(Request $request){

        $admins = LostShipmentAdmin::join('admins as ad','ad.id','=','lost_shipment_admins.admin_id')
                    ->select('ad.name as admin_name','lost_shipment_admins.id');
        $datatables = Datatables::of($admins)
                    ->addColumn('action', function($admins) {
                        if (session('role_id') == 1 || in_array(711, session('permissions'))) {
                                $dropdown = '<div class="btn-group">
                                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                                <div class="dropdown-menu dropdown-menu-sm">
                                ';
            
                                    $dropdown .=' <button type="button" class="dropdown-item delete"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-minus-circle"></i></div><div class="col-9 offset-1">Delete</div></button>';
                                
                                $dropdown .='</div>
                              </div>
                      ';
            
                      return $dropdown;
                           
                        }
                        else {
                            return '';
                        }
                    });
            
                return $datatables->make(true);
    }

    public function lost_shipment_admins_add(Request $request){
        $check_admin = LostShipmentAdmin::where('admin_id',$request->admin_id);
            
            if(!$check_admin->exists()){
                $lost_shipment_admin = new LostShipmentAdmin();
                $lost_shipment_admin->admin_id = $request->admin_id;
                $lost_shipment_admin->save();
    
                return redirect()->back()->with('success', 'User Added!');
    
            }else{
                return redirect()->back()->with('error', 'User already exist');
            }
    }

    public function lost_shipment_admins_delete(Request $request){
        LostShipmentAdmin::find($request->id)->delete();
        return redirect()->back()->with('success', 'User Deleted!');
    }

    public function undelivered_sms_hub_wise(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),529);
        
        $settings = GlobalSettings::where('type', '=', 'undeliverd_sms_hubwise')->first();
        $cities = City::where('status', 1)->where('business_category_id',1)->get();

        $city_id = null;
        if($settings){
            $city_id =  explode(',', $settings->text); 

        }

        return view('admin.settings.undelivered_sms_hub_wise',compact('city_id','cities'));


    }

    public function undelivered_sms_hub_wise_submit(Request $request){
        // dump(implode(',', $request->city_id));

        $city_ids = implode(',', $request->city_id);
        $settings = GlobalSettings::where('type', 'undeliverd_sms_hubwise');

        if ($settings->exists()) {
            $settings = $settings->first();
            $settings->text = $city_ids;
            $settings->save();

        }else{
            $settings = new GlobalSettings;
            $settings->text = $city_ids;
            $settings->type = 'undeliverd_sms_hubwise';
            $settings->save();
        }
        return redirect()->back()->with('success', 'Cities Upadated');

    }

    public function delivery_area_keyword(){
        
        ActivityTrailController::createActivityTrailLog(Auth::id(),561);
        return view('admin.settings.delivery_area_keyword');
    }

    public function delivery_area_keyword_list(Request $request){
        $admins = DeliveryLocationMapping::join('admins as ad','ad.id','=','delivery_location_mappings.added_by')
                 ->join('cities as ct','ct.id','=','delivery_location_mappings.city_id')
                 ->leftjoin('admins as ub','ub.id','=','delivery_location_mappings.updated_by')
        ->select('delivery_location_mappings.id','delivery_location_mappings.area_name','ct.name as city_name','ub.name as updated_by','ad.name as added_by','delivery_location_mappings.updated_at','delivery_location_mappings.status');
        $datatables = Datatables::of($admins)
        ->addColumn('status', function($admins) {
            if($admins->status == 1){
                return 'Enable';
            }else{
                return 'Disable';
            }
        })
        ->addColumn('action', function($admins) {
            if (session('role_id') == 1 || in_array(749, session('permissions'))) {
                    $dropdown = '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                    ';
                        if($admins->status == 1){
                            $dropdown .=' <button type="button" class="dropdown-item enable_disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-minus-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';
                        }elseif($admins->status == 0){
                            $dropdown .=' <button type="button" class="dropdown-item enable_disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-minus-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
                        }
                        $dropdown .=' <button type="button" class="dropdown-item view_keyword"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Keyword</div></button>';
                        $dropdown .=' <button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';
                    
                    $dropdown .='</div>
                  </div>
          ';

          return $dropdown;
               
            }
            else {
                return '';
            }
        });

        return $datatables->make(true);
    }

    public function delivery_area_keyword_add(){
        $cities = City::where('business_category_id', 1)->where('status', 1)->get();
        return view('admin.settings.add_delivery_area',compact('cities'));
    }

    public function delivery_area_keyword_store(Request $request){
        
        $check_exists = DeliveryLocationMapping::where('city_id',$request->city_id)->where('area_name',$request->area_name);
        
        if($check_exists->exists()){
            return redirect()->back()->with('error', 'Entered Delivery Area for the selected city is already Exists!');
        }

        $keywords = explode(',', $request->delivery_area_keyword);
        $delivery_location = new DeliveryLocationMapping;
        $delivery_location->area_name = $request->area_name;
        $delivery_location->city_id = $request->city_id;
        $delivery_location->added_by = Auth::id();
        $delivery_location->save();

        foreach($keywords as $keyword){
            $delivery_location_keyword = new DeliveryLocationMappingKeyword;
            $delivery_location_keyword->keyword = $keyword;
            $delivery_location_keyword->mapping_id = $delivery_location->id;
            $delivery_location_keyword->save();

        }
        return redirect()->route('admin.settings.delivery_area_keyword.index')->with('success', 'Deivery Area Keyword Added');

    }

    public function delivery_area_keyword_enable_disable(Request $request){
        $id = $request->id;
        $delivery_location = DeliveryLocationMapping::find($id);
        if ($delivery_location) {
            if ($delivery_location->status == 1) {
                $delivery_location->status = 0;
                $delivery_location->save();
            } else {
                $delivery_location->status = 1;
                $delivery_location->save();
            }
            return response()->json(['status' => 1, 'success' => 'Status Successfully Updated!']);
        }
    }

    public function delivery_area_keyword_edit($id){
        $delivery_location = DeliveryLocationMapping::find($id);
        if($delivery_location){
            $delivery_location_keywords = $delivery_location->mappings;
            if ($delivery_location_keywords) {
                $delivery_location_keywords = $delivery_location_keywords->pluck('keyword')->toArray();

                $delivery_location_keywords = implode(',', $delivery_location_keywords);
            }

            $cities = City::where('business_category_id', 1)->where('status', 1)->get();

            return view('admin.settings.edit_delivery_area',compact('delivery_location_keywords','cities','delivery_location'));

        }else{
            return redirect()->back()->with('error', 'Deivery Area Keyword Not Found');
        }
    }

    public function delivery_area_keyword_update(Request $request){
        $delivery_location = DeliveryLocationMapping::find($request->id);
        if($delivery_location){
            if($delivery_location->city_id != $request->city_id && $delivery_location->area_name != $request->area_name){
                $check_exists = DeliveryLocationMapping::where('city_id',$request->city_id)->where('area_name',$request->area_name);
            
                if($check_exists->exists()){
                    return redirect()->back()->with('error', 'Entered Delivery Area for the selected city is already Exists!');
                }
            }
            DeliveryLocationMappingKeyword::where('mapping_id',$request->id)->delete();

            $keywords = explode(',', $request->delivery_area_keyword);
            $delivery_location->area_name = $request->area_name;
            $delivery_location->city_id = $request->city_id;
            $delivery_location->updated_by = Auth::id();
            $delivery_location->save();
    
            foreach($keywords as $keyword){
                $delivery_location_keyword = new DeliveryLocationMappingKeyword;
                $delivery_location_keyword->keyword = $keyword;
                $delivery_location_keyword->mapping_id = $delivery_location->id;
                $delivery_location_keyword->save();
    
            }
            return redirect()->route('admin.settings.delivery_area_keyword.index')->with('success', 'Deivery Area Keyword Updated');
        }else{
            return redirect()->back()->with('error', 'Deivery Area Keyword Not Found');
        }
    }

    public function delivery_area_keyword_view($id){
       
        $delivery_location = DeliveryLocationMapping::find($id);
        if($delivery_location){
            $delivery_location_keywords = $delivery_location->mappings;
            if ($delivery_location_keywords) {
                $delivery_location_keywords = $delivery_location_keywords->pluck('keyword')->toArray();

                $delivery_location_keywords = implode(',', $delivery_location_keywords);
            }

            $cities = City::where('business_category_id', 1)->where('status', 1)->get();

            return view('admin.settings.view_delivery_area',compact('delivery_location_keywords','cities','delivery_location'));

        }else{
            return redirect()->back()->with('error', 'Deivery Area Keyword Not Found');
        }
    }
    public function weight_bypass()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(),556);

        $shippers = User::where('status', 3)->where('blacklist', 0)->select('id', 'name')->get();

        $settings = ByPassWeightShippers::all()->pluck('shipper_id')->toArray();

        return view('admin.settings.weight_bypass')->with(['shippers' => $shippers, 'users' => $settings]);
    }

    public function shipper_store_weight_bypass(Request $request)
    {
        if ($request->has('shippers')) {
            if (count($request->shippers) > 0) {
                $shippers = $request->shippers;
                ByPassWeightShippers::truncate();

                foreach($shippers as $shipper)
                {
                    $update_shipper = new ByPassWeightShippers();
                    $update_shipper->shipper_id = $shipper;
                    $update_shipper->save();
                }
            }
            return redirect()->back()->with('success', 'Setting Updated!');

        } else {
            return redirect()->back()->with('error', 'No shipper selected!');
        }

    }

    public function booking_destination_keyword(){

        ActivityTrailController::createActivityTrailLog(Auth::id(),565);
        return view('admin.settings.booking_destination_mapping.index');
    }

    public function booking_destination_keyword_list(Request $request){
        $admins = BookingDestinationMapping::join('admins as ad','ad.id','=','booking_destination_mappings.added_by')
            ->join('cities as ct','ct.id','=','booking_destination_mappings.city_id')
            ->leftjoin('admins as ub','ub.id','=','booking_destination_mappings.updated_by')
            ->select('booking_destination_mappings.id','ct.name as city_name','ub.name as updated_by','ad.name as added_by','booking_destination_mappings.updated_at','booking_destination_mappings.status');
        $datatables = Datatables::of($admins)
            ->addColumn('status', function($admins) {
                if($admins->status == 1){
                    return 'Enable';
                }else{
                    return 'Disable';
                }
            })
            ->addColumn('action', function($admins) {
                if (session('role_id') == 1 || in_array(774, session('permissions'))) {
                    $dropdown = '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                    ';
                    if($admins->status == 1){
                        $dropdown .=' <button type="button" class="dropdown-item enable_disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-minus-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';
                    }elseif($admins->status == 0){
                        $dropdown .=' <button type="button" class="dropdown-item enable_disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-minus-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
                    }
                    $dropdown .=' <button type="button" class="dropdown-item view_keyword"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Keyword</div></button>';
                    $dropdown .=' <button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';

                    $dropdown .='</div>
                  </div>
          ';

                    return $dropdown;

                }
                else {
                    return '';
                }
            });

        return $datatables->make(true);
    }

    public function booking_destination_keyword_add(){
        $cities = City::where('business_category_id', 1)->where('status', 1)->get();
        return view('admin.settings.booking_destination_mapping.add',compact('cities'));
    }

    public function booking_destination_keyword_store(Request $request){

        $check_exists = BookingDestinationMapping::where('city_id',$request->city_id);

        if($check_exists->exists()){
            return redirect()->back()->with('error',  'This City Keywords Already Exists');
        }

        $keywords = explode(',', $request->booking_destination_keywords);
        $booking_destination_mapping = new BookingDestinationMapping;
        $booking_destination_mapping->city_id = $request->city_id;
        $booking_destination_mapping->added_by = Auth::id();
        $booking_destination_mapping->save();

        foreach($keywords as $keyword){
            $booking_destination_mapping_keyword = new BookingDestinationMappingKeyword();
            $booking_destination_mapping_keyword->keyword = $keyword;
            $booking_destination_mapping_keyword->mapping_id = $booking_destination_mapping->id;
            $booking_destination_mapping_keyword->save();

        }
        return redirect()->route('admin.settings.booking_destination_keyword.index')->with('success', 'Booking Destination Keyword Added');

    }

    public function booking_destination_keyword_view($id){

        $booking_destination_mapping = BookingDestinationMapping::find($id);
        if($booking_destination_mapping){
            $booking_destination_mapping_keywords = $booking_destination_mapping->mappings;
            if ($booking_destination_mapping_keywords) {
                $booking_destination_mapping_keywords = $booking_destination_mapping_keywords->pluck('keyword')->toArray();

                $booking_destination_mapping_keywords = implode(',', $booking_destination_mapping_keywords);
            }

            $cities = City::where('business_category_id', 1)->where('status', 1)->get();

            return view('admin.settings.booking_destination_mapping.view',compact('booking_destination_mapping_keywords','cities','booking_destination_mapping'));

        }else{
            return redirect()->back()->with('error', 'Deivery Area Keyword Not Found');
        }
    }

    public function booking_destination_keyword_enable_disable(Request $request){
        $id = $request->id;
        $booking_destination_mapping = BookingDestinationMapping::find($id);
        if ($booking_destination_mapping) {
            if ($booking_destination_mapping->status == 1) {
                $booking_destination_mapping->status = 0;
                $booking_destination_mapping->save();
            } else {
                $booking_destination_mapping->status = 1;
                $booking_destination_mapping->save();
            }
            return response()->json(['status' => 1, 'success' => 'Status Successfully Updated!']);
        }
    }

    public function booking_destination_keyword_edit($id){
        $booking_destination_mapping = BookingDestinationMapping::find($id);
        if($booking_destination_mapping){
            $booking_destination_mapping_keywords = $booking_destination_mapping->mappings;
            if ($booking_destination_mapping_keywords) {
                $booking_destination_mapping_keywords = $booking_destination_mapping_keywords->pluck('keyword')->toArray();

                $booking_destination_mapping_keywords = implode(',', $booking_destination_mapping_keywords);
            }

            $cities = City::where('business_category_id', 1)->where('status', 1)->get();

            return view('admin.settings.booking_destination_mapping.edit',compact('booking_destination_mapping_keywords','cities','booking_destination_mapping'));

        }else{
            return redirect()->back()->with('error', 'Deivery Area Keyword Not Found');
        }
    }

    public function booking_destination_keyword_update(Request $request){
        $booking_destination_mapping = BookingDestinationMapping::find($request->id);
        if($booking_destination_mapping){
            if($booking_destination_mapping->city_id != $request->city_id){
                $check_exists = BookingDestinationMapping::where('city_id',$request->city_id);

                if($check_exists->exists()){
                    return redirect()->back()->with('error',  'This City Keywords Already Exists');
                }
            }
            BookingDestinationMappingKeyword::where('mapping_id',$request->id)->delete();

            $keywords = explode(',', $request->booking_destination_keywords);
            $booking_destination_mapping->city_id = $request->city_id;
            $booking_destination_mapping->updated_by = Auth::id();
            $booking_destination_mapping->save();

            foreach($keywords as $keyword){
                $booking_destination_mapping_keyword = new BookingDestinationMappingKeyword();
                $booking_destination_mapping_keyword->keyword = $keyword;
                $booking_destination_mapping_keyword->mapping_id = $booking_destination_mapping->id;
                $booking_destination_mapping_keyword->save();

            }
            return redirect()->route('admin.settings.booking_destination_keyword.index')->with('success', 'Booking Destination Keyword Updated');
        }else{
            return redirect()->back()->with('error', 'Booking Destination Keyword Not Found');
        }
    }

    public function address_verify(Request $request){
        if(isset($request->city_id)){


            $city_id = $request->city_id;
            $consignee_address = $request->consignee_address;
            $check = BookingDestinationMappingKeyword::join('booking_destination_mappings as bdm', 'bdm.id', '=', 'booking_destination_mapping_keywords.mapping_id')
                ->where('bdm.status',1)
                ->select(['booking_destination_mapping_keywords.keyword'])
                ->pluck('keyword')
                ->toArray();

            $str_arr = null;
            $str_arr = preg_split('/[\s.,-,_,*,?,<,>,!,@,#,$,%,^,&,(,)]+/', $consignee_address);
            $found_keyword = array();
            foreach ($check as $nsa) {
                foreach ($str_arr as $arr_value) {
                    $arr_value = trim($arr_value);
                    if (strtolower($nsa) == strtolower($arr_value)) {
                        array_push($found_keyword,$arr_value);
                    }
                }
            }
            $invalid_cities  = array();
            if($found_keyword) {
                $data_found = BookingDestinationMappingKeyword::join('booking_destination_mappings as bdm', 'bdm.id', '=', 'booking_destination_mapping_keywords.mapping_id')
                    ->leftjoin('cities as c', 'c.id', '=', 'bdm.city_id')
                    ->select('bdm.city_id','c.name as city_name','booking_destination_mapping_keywords.keyword')
                    ->whereIn('booking_destination_mapping_keywords.keyword', $found_keyword);
                if ($data_found->exists()) {
                    $data_found =$data_found->get();

                    foreach ($data_found as $value){
                        if($value->city_id != $city_id){
                            $dd = isset($invalid_cities[$value->city_name]) ? $invalid_cities[$value->city_name] : '';
                            $invalid_cities[$value->city_name] = trim($dd)." ".$value->keyword;
                        }
                    }
                    if($invalid_cities){
                        return response()->json(['status'=>'false','invalid_cities'=>$invalid_cities,'error'=>'Invalid Address']);
                    }
                }
            }
            return response()->json(['status'=>'true']);

        }
        return response()->json(['status'=>'true']);

    }

    public function complain_portal_shippers(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),578);
        
        $shippers = array();
        
        $settings = GlobalSettings::where('type', 'complaint_portal_shippers');
        if ($settings->exists()) {
            $settings = $settings->first();
            if($settings->text != NULL){
                $shippers = array_map('intval', explode(',', $settings->text));
            }
        }
        $users = User::where('status',3)->where('blacklist', 0)->select('id','name')->get();

        return view('admin.settings.compalint_portal_shippers')->with(['shippers' => $shippers,'users' => $users]);
    }

    public function complain_portal_shippers_update(Request $request){
        if ($request->has('shippers')) {
            if (count($request->shippers) > 0) {
                $shippers = implode(',', $request->shippers);
                $settings = GlobalSettings::where('type', 'complaint_portal_shippers');

                if ($settings->exists()) {
                    $settings = $settings->first();
                } else {
                    $settings = new GlobalSettings();

                    $settings->type = 'complaint_portal_shippers';
                    $settings->setting_value = 0;

                }
                $settings->text = $shippers;
                $settings->save();
            }
            return redirect()->back()->with('success', 'Settings Updated!');

        } else {
            return redirect()->back()->with('error', 'No shippers selected!');
        }
    }
    public function delete_sale_person_targets(Request $request){
        if(isset($request->sale_person_ids) && !empty($request->sale_person_ids)){

            $sales_persons = $request->sale_person_ids;
            if (count($sales_persons) > 0) {
                foreach ($sales_persons as $person_id) {
                    $sales_target = SalePersonTarget::where('id', $person_id);
                    if ($sales_target->exists()) {
                        $sales_target = $sales_target->first();

                        $sale_person_target_del = new SalePersonTargetDelete();
                        $sale_person_target_del->deleted_id = $sales_target->id;
                        $sale_person_target_del->start_date = $sales_target->start_date;
                        $sale_person_target_del->end_date = $sales_target->end_date;
                        $sale_person_target_del->sales_person_id = $sales_target->sales_person_id;
                        $sale_person_target_del->target_days = $sales_target->target_days;
                        $sale_person_target_del->target_month = $sales_target->target_month;
                        $sale_person_target_del->average_revenue = $sales_target->average_revenue;
                        $sale_person_target_del->deleted_by = Auth::id();
                        $sale_person_target_del->save();

                    }
                }
                SalePersonTarget::whereIn('id', $sales_persons)->delete();
            }
            return response()->json(['status' => 1, 'success' => 'Delete Successfully']);
        } else {
            return response()->json(['status' => 0, 'error' => 'No Id Found']);
        }
    }


    public function lead_tagging_services(Request $request){
        $all_services = array();
        $services = LeadTaggingService::where('lead_tagging_id', $request->lead_tagging_id)->get();

        foreach ($services as $service) {
            $service_name = ServiceList::find($service->service_id);
            if($service_name){
                $all_services[] = $service_name->name;
            }
        }

        return response()->json(['status' => 1, 'services' => $all_services]);
    }
    
}
