<?php

namespace App\Http\Controllers\Admins;


use App\Http\Models\AppNotification;
use App\Http\Models\AppType;
use App\Http\Models\EmployeeDeviceToken;
use App\Http\Models\NotificationType;
use App\Http\Models\Rider;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationsController;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Shipper\User;
use App\Http\Models\Notification;

use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Models\City;
use Illuminate\Support\Facades\Storage;

use Auth;

class AdminNotificationsController extends Controller
{
    public function __construct() {
      $this->middleware('auth:admin');

      $this->middleware('Permission');
    }

    public function index() {
        $notifications = NotificationType::all();
        $riders = Rider::where('status', 1)->get();
        $employees = Admin::where('status', 1)->get();
        $hubs = DB::connection('reports')->table('cities')->where('hub',1)->where('status', 1)->where('business_category_id', 1)->select('id','name')->get();
        $cities = DB::connection('reports')->table('cities')->where('status', 1)->where('business_category_id', 1)->select('id','name')->get();
      return view('admin.notifications.index')->with(['notifications'=>$notifications, 'riders' => $riders, 'employees' => $employees, 'hubs' => $hubs, 'cities' => $cities]);
    }

    public function list(Request $request) {
        $notifications = Notification::join('notification_types as nt', 'notifications.type_id', '=', 'nt.id')
        ->join('admins as a', 'notifications.updated_by', '=', 'a.id')
        ->select('notifications.id', 'notifications.name', 'notifications.type_id', 'nt.name as type', 'notifications.updated_at', 'a.name as updated_by', 'notifications.status');

        $datatables = Datatables::of($notifications)
        ->setRowAttr([
            'data-type' => function($notification) {
                return $notification->type_id;
            },
        ])
        ->editColumn('status', function ($notification) {
            return (($notification->status) ? 'Enabled' : 'Disabled');
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

            if (session('role_id') == 1 || in_array(101, session('permissions'))) {
                $dropdown .= $edit_button;
            }

            if (session('role_id') == 1 || in_array(102, session('permissions'))) {
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

    public function send_custom_email(Request $request) {
        $attachments = array();
        if ($request->get('receiver') == 1) {
            if($request->get('search_hub') == 0) {
                $emails = Admin::all()->pluck('email')->toArray();
            }
            else {
                $emails = Admin::where('default_hub_id', '=', $request->get('search_hub'))->pluck('email')->toArray();
            }
        }
        else {
            if($request->get('search_city') == 0)
            {
                if ($request->get('shipper_status') == 1) {
                    $emails = User::where('status', '=', 3)->where('blacklist', '=', 0)->get()->pluck('email')->toArray();
                }
                else if ($request->get('shipper_status') == 2) {
                    $emails = User::where('status', '=', 4)->where('blacklist', '=', 0)->get()->pluck('email')->toArray();
                }
                else {
                    $emails = User::where('blacklist', '=', 0)->get()->pluck('email')->toArray();
                }
            }
            else
            {
                if ($request->get('shipper_status') == 1) {
                    $emails = User::where('status', '=', 3)->where('blacklist', '=', 0)->where('city_id', '=', $request->get('search_city'))->get()->pluck('email')->toArray();
                }
                else if ($request->get('shipper_status') == 2) {
                    $emails = User::where('status', '=', 4)->where('blacklist', '=', 0)->where('city_id', '=', $request->get('search_city'))->get()->pluck('email')->toArray();
                }
                else {
                    $emails = User::where('blacklist', '=', 0)->where('city_id', '=', $request->get('search_city'))->get()->pluck('email')->toArray();
                }
            }
        }

        if (!empty($emails)) {
            $subject = $request->get('subject');
            $body = $request->get('body');
            $body_attachment_message= PHP_EOL . 'Please Find the Attachment from the following Link(s).'. PHP_EOL;
            if($request->hasFile('attachment'))
            {
                $files = $request->file('attachment');
                foreach ($files as $file) {
                    $image = $file;
                    $extension = $image->getClientOriginalExtension();
                    $originalname = pathinfo( $image->getClientOriginalName(), PATHINFO_FILENAME);
                    $random = rand(1000, 100000);
                    $now = Carbon::now();
                    $time = $now->year . '_' . $now->month;
                    $image_name =  $time . $random . Auth::id();
                    $generated_image_name =  $image_name . '.' . $extension;
                    $image->move(public_path('storage/custom_email_attachments'), $generated_image_name);
                    $fullpath = Storage::disk('public')->url('/custom_email_attachments/'.$generated_image_name);
                    array_push($attachments,$fullpath);
                }
                foreach ($attachments as $key => $attachment)
                {
                    $key = $key + 1;
                    $link = '<a href="' . $attachment . '" target="_blank"><u> Attachment '.$key.'</u></a>';
                    $body_attachment_message = $body_attachment_message . $link . PHP_EOL;
                }
                // $body_attachment_message =  $body_attachment_message. PHP_EOL . 'NOTE: the attachments will be removed after 7 days(s)';
                $body = $body . PHP_EOL . $body_attachment_message;
            }
            foreach ($emails as $to) {
                NotificationsController::custom(1, $subject, $body, $to);
            }

            return redirect()->back()->with('success', 'Custom Email Sent');
        }
        else {
            return back()->withErrors('No Receiver to send Email to!');
        }
    }

    public function status(Request $request) {
        $notification = Notification::find($request->id);

        if ($notification) {
            $notification->status = $request->status;
            $notification->updated_by = Auth::id();

            $notification->save();

            if ($request->status) {
                return ['status' => 0, 'success' => 'Notification has been enabled'];
            }
            else {
                return ['status' => 0, 'success' => 'Notification has been disabled'];
            }
        }
        else {
            return ['status' => 1, 'error' => 'No Notication with given ID is present'];
        }
    }

    public function details(Request $request) {
        $id = $request->id;

        $notification = Notification::find($id);

        $details = array();

        if ($notification->type_id == 1) {
            $details['subject'] = $notification->subject;
        }

        $details['body'] = $notification->body;

        if ($id == 1) {
            $details['fields'] = ['account_id', 'company_name', 'email', 'person_of_contact', 'phone_no_1', 'phone_no_2', 'address', 'city', 'cnic', 'ntn_no', 'api_token'];
        }
        else if ($id == 2) {
            $details['fields'] = ['account_id', 'company_name', 'service_type', 'pickup_address', 'pickup_city', 'order_id', 'pickup_date', 'shipping_mode', 'amount', 'payment_mode', 'tracking_number'];
        }
        else if ($id == 3) {
            $details['fields'] = ['company_name', 'service_type', 'pickup_address', 'pickup_city', 'consignee_name', 'consignee_address', 'consignee_city', 'order_id', 'shipping_mode', 'amount', 'payment_mode', 'tracking_number'];
        }
        else if ($id == 4) {
            $details['fields'] = ['company_name', 'arrival_at', 'pickup_city', 'consignee_name', 'consignee_city', 'order_id', 'weight', 'tracking_number', 'item_product_type', 'item_description', 'item_quantity', 'amount'];
        }
        else if ($id == 5) {
            $details['fields'] = ['cargo_number', 'company_name', 'departure_at', 'order_id', 'tracking_number'];
        }
        else if ($id == 6) {
            $details['fields'] = ['cargo_number', 'company_name', 'departure_at', 'order_id', 'tracking_number'];
        }
        else if ($id == 7) {
            $details['fields'] = ['cargo_number', 'arrival_at', 'company_name', 'order_id', 'tracking_number'];
        }
        else if ($id == 8) {
            $details['fields'] = ['cargo_number', 'arrival_at', 'company_name', 'order_id', 'tracking_number'];
        }
        else if ($id == 9) {
            $details['fields'] = ['cargo_number', 'departure_at', 'seal_number', 'builty_number', 'expected_arrival_date', 'shipping_mode', 'transport_mode', 'vendor', 'sender', 'tracking_number'];
        }
        else if ($id == 10) {
            $details['fields'] = ['delivery_note_number', 'rider', 'company_name', 'departure_at', 'order_id', 'tracking_number'];
        }
        else if ($id == 11) {
            $details['fields'] = ['delivery_note_number', 'rider', 'company_name', 'departure_at', 'order_id', 'tracking_number'];
        }
        else if ($id == 12) {
            $details['fields'] = ['delivery_note_number', 'rider', 'company_name', 'departure_at', 'consignee_name', 'consignee_address', 'order_id', 'amount', 'payment_mode', 'tracking_number', 'refusal_otp'];
        }
        else if ($id == 13) {
            $details['fields'] = ['delivery_note_number', 'rider', 'company_name', 'departure_at', 'order_id', 'tracking_number', 'status'];
        }
        else if ($id == 14) {
            $details['fields'] = ['delivery_note_number', 'rider', 'company_name', 'departure_at', 'order_id', 'tracking_number', 'status'];
        }
        else if ($id == 15) {
            $details['fields'] = ['return_note_number', 'rider', 'company_name', 'departure_at', 'order_id', 'tracking_number', 'status'];
        }
        else if ($id == 16) {
            $details['fields'] = ['return_note_number', 'rider', 'company_name', 'departure_at', 'order_id', 'tracking_number', 'status'];
        }
        else if ($id == 17) {
             $details['fields'] = ['account_id', 'company_name', 'service_type', 'order_id', 'tracking_number', 'new_tracking_number'];
        }
        else if ($id == 18) {
             $details['fields'] = ['account_id', 'company_name', 'service_type', 'order_id', 'tracking_number', 'new_tracking_number'];
        }
        else if ($id == 19) {
             $details['fields'] = ['dispute_number', 'dispute_type', 'dispute_description', 'launched_by', 'city', 'tracking_number'];
        }
        else if ($id == 20) {
             $details['fields'] = ['company_name', 'city', 'bank', 'bank_branch', 'account_number', 'account_title', 'iban', 'account_city', 'payment_cycle', 'payment_done_id', 'payment_done_at', 'total_shipments', 'delivered_shipments', 'returned_shipments', 'adjusted_shipments', 'total_amount', 'total_weight_charges', 'total_cash_handling_charges', 'total_insurance_charges', 'total_replacement_charges', 'total_return_charges', 'total_packaging_material_charges', 'total_fuel_surcharge', 'total_gst', 'total_charges', 'total_payable', 'consignee_name', 'consignee_city', 'order_id', 'estimated_weight', 'actual_weight', 'chargeable_weight', 'tracking_number', 'amount', 'weight_charges', 'cash_handling_charges', 'charges', 'gst', 'payable']; //total_try_and_buy_charges
        }
        else if ($id == 21) {
             $details['fields'] = ['company_name', 'service_type', 'pickup_address', 'pickup_city', 'consignee_name', 'consignee_address', 'consignee_city', 'order_id', 'amount', 'payment_mode', 'tracking_number'];
        }
        else if ($id == 22) {
             $details['fields'] = ['company_name', 'person_of_contact', 'phone_number', 'address', 'city'];
        }
        else if ($id == 23) {
             $details['fields'] = ['company_name', 'service_type', 'pickup_address', 'pickup_city', 'consignee_name', 'consignee_phone_number_1', 'consignee_phone_number_2', 'consignee_email', 'consignee_address', 'consignee_city', 'order_id', 'shipping_mode', 'amount', 'payment_mode', 'status', 'status_reason', 'status_date', 'arrival_date', 'tracking_number'];
        }
        else if ($id == 24) {
             $details['fields'] = ['hub', 'service_type', 'pickup_address', 'pickup_city', 'consignee_name', 'consignee_phone_number_1', 'consignee_phone_number_2', 'consignee_email', 'consignee_address', 'consignee_city', 'order_id', 'shipping_mode', 'status', 'status_reason', 'status_date', 'tracking_number'];
        }
        else if ($id == 25) {
             $details['fields'] = ['hub', 'service_type', 'pickup_address', 'pickup_city', 'consignee_name', 'consignee_phone_number_1', 'consignee_phone_number_2', 'consignee_email', 'consignee_address', 'consignee_city', 'order_id', 'shipping_mode', 'status', 'status_reason', 'status_date', 'tracking_number'];
        }
        else if ($id == 26) {
            $details['fields'] = ['date', 'link'];
        }
        else if ($id == 27) {
             $details['fields'] = ['account_id', 'company_name', 'invoice_number', 'billing_period_from_date', 'billing_period_to_date', 'due_date', 'invoice'];
        }
        else if ($id == 28) {
             $details['fields'] = ['account_id', 'company_name', 'invoice_number', 'billing_period_from_date', 'billing_period_to_date', 'due_date', 'invoice'];
        }
        else if ($id == 29) {
            $details['fields'] = ['pin_code'];
        }
        else if ($id == 30) {
            $details['fields'] = ['poc','receiving_of_pickup','company_name','tracking_number','order_id','destination','service_type','amount','quantity','product_type','description','estimated_weight'];
        }
		else if($id == 31){
            $details['fields'] = ['request_id','tracking_number','shipper_name','email','phone','destination','channel','case_nature','case_nature_type','details','status'];
        }
        else if ($id == 32) {
             $details['fields'] = ['nsa', 'tracking_number'];
        }
        else if ($id == 33) {
             $details['fields'] = ['tracking_number','destination','nsa_osa_estimated_charges','remarks'];
        }
        else if ($id == 34) {
             $details['fields'] = ['user_id', 'updated_at', 'tagged_sales_person'];
        }
        else if ($id == 35) {
             $details['fields'] = ['consignee_name', 'shipper_name', 'tracking_number', 'receiver_name','status_date','order_id'];
        }
        else if ($id == 36 || $id == 37) {
             $details['fields'] = ['account_id', 'company_name_b', 'company_name_a', 'trax_logo'];
        }
		else if ($id == 38){
            $details['fields'] = ['shipper_name','button','trax_logo','link'];
        }
        else if ($id == 39) {
            $details['fields'] = ['company_name','tracking_number', 'status_updated_at', 'receiver_name','returned_at'];
        }else if ($id == 40) {
            $details['fields'] = ['rider_name','delivery_note_id','password'];
        }
        else if ($id == 41) {
            $details['fields'] = ['request_id'];
        }
        else if ($id == 42) {
            $details['fields'] = ['rider_name', 'rider_phone_number'];
        }
        else if ($id == 43) {
            $details['fields'] = ['vendor', 'shipper_name', 'shipments_detail'];
        }
        else if ($id == 44) {
            $details['fields'] = ['hub', 'date', 'link', 'preview'];
        }
        else if ($id == 45) {
            $details['fields'] = ['zone', 'date', 'link', 'preview'];
        }
        else if ($id == 46) {
            $details['fields'] = ['date', 'link', 'preview'];
        }
        else if ($id == 47) {
            $details['fields'] = ['date', 'link', 'preview'];
        }
        else if ($id == 48) {
            $details['fields'] = ['date', 'link', 'preview'];
        }
        else if ($id == 49) {
            $details['fields'] = ['date', 'link', 'preview'];
        }
        else if ($id == 50) {
            $details['fields'] = ['status', 'contact_person', 'rider', 'rider_phone', 'vendor', 'shipper_name'];
        }
        else if ($id == 51) {
            $details['fields'] = ['status', 'contact_person', 'rider', 'rider_phone', 'vendor', 'shipper_name'];
        }
		else if ($id == 52) {
            $details['fields'] = ['contact_person', 'company_name', 'rider_name', 'rider_phone_number', 'order_id', 'tracking_number'];
        }
		else if ($id == 53) {
            $details['fields'] = ['hub', 'date', 'preview','link'];
        }
		else if ($id == 54) {
            $details['fields'] = ['zone', 'date', 'preview','link'];
        }
		else if ($id == 55) {
            $details['fields'] = ['date', 'preview','link'];
        }
        else if ($id == 56){
            $details['fields'] = ['account_id', 'shipper_name','preview', 'sale_person'];
        }
		else if ($id == 57){
            $details['fields'] = ['preview'];
        }
        else if ($id == 58){
            $details['fields'] = ['shipper_name'];
        }
        else if ($id == 59){
            $details['fields'] = ['shipping_mode', 'date', 'preview', 'link'];
        }
        else if ($id == 60){
            $details['fields'] = ['preview', 'date'];
        }
        else if ($id == 61){
            $details['fields'] = ['rider_name', 'pin'];
        }
        else if ($id == 62){
            $details['fields'] = ['cancel_shipment'];
        }
        else if ($id == 63){
            $details['fields'] = ['pickup_request_ID', 'shipper', 'date'];
        }
        else if ($id == 64){
            $details['fields'] = ['account_id', 'name'];
        }
        else if ($id == 65){
            $details['fields'] = ['request_id', 'case_nature', 'case_nature_type'];
        }
        else if ($id == 66){
            $details['fields'] = ['request_id', '[escalation].'];
        }
		else if ($id == 67){
            $details['fields'] = ['zero_report'];
        }
		else if ($id == 68 || $id == 69 ||  $id == 70 ||  $id == 71 || $id == 72){
            $details['fields'] = ['preview'];
        }
		else if ($id == 73){
            $details['fields'] = ['pickup_request_id', 'tracking_numbers', 'company_name', 'pickup_city'];
        }
		else if ($id == 74){
            $details['fields'] = ['preview','link'];
        }
		else if ($id == 75){
            $details['fields'] = ['name','address'];
        }
		else if ($id == 76){
            $details['fields'] = ['hub','date','link'];
        }
		else if ($id == 77){
            $details['fields'] = ['rider_name','rider_phone'];
        }
		else if ($id == 78){
            $details['fields'] = ['hub','date'];
        }
		else if ($id == 79){
            $details['fields'] = ['zone','date'];
        }
		else if ($id == 80){
            $details['fields'] = ['date'];
        }
		else if ($id == 81){
            $details['fields'] = ['preview'];
        }
		else if ($id == 82){
            $details['fields'] = ['preview','link'];
        }
		else if($id == 83)
		{
            $details['fields'] = ['shipment_picked_date','rider_name','shipper_name','requested_date','number'];
        }
		else if ($id == 84){
            $details['fields'] = ['date','tracking_number','shipper_name','product_description','cod_amount','origin', 'destination', 'status','preview'];
        }
		else if ($id == 85){
            $details['fields'] = ['preview'];
        }
		else if ($id == 86 || $id == 87){
            $details['fields'] = ['hub','date','link'];
        }
		else if ($id == 88){
            $details['fields'] = ['runner','date','link'];
        }
        else if ($id == 89) {
            $details['fields'] = ['link'];
        }
		else if ($id == 90)
		{
            $details['fields'] = ['link'];
        }
		else if ($id == 91)
		{
            $details['fields'] = ['pin'];
        }
        else if ($id == 92)
        {
            $details['fields'] = ['shipper_name','payment_id'];
        }
        else if ($id == 96)
        {
            $details['fields'] = ['link'];
        }
        else if ($id == 98)
        {
            $details['fields'] = ['preview'];
        }
        else if ($id == 99)
        {
            $details['fields'] = ['sales_person','preview'];
        }
        else if ($id == 100)
        {
            $details['fields'] = ['head_of_sales','preview'];
        }
        else if ($id == 101 || $id == 102)
        {
            $details['fields'] = ['hub','date','preview'];
        }
        else if ($id == 103)
        {
            $details['fields'] = ['date','preview'];
        }
        else if ($id == 105)
        {
            $details['fields'] = ['sales_person','pickup_address','city_name','reason'];
        }
        else if ($id == 106)
        {
            $details['fields'] = ['old_rider_name','pickup_request_id','pickup_coordinator_name','new_rider_name'];
        }
        else if ($id == 107)
        {
            $details['fields'] = ['new_rider_name','pickup_request_id','pickup_coordinator_name','old_rider_name'];
        }
        else if ($id == 108 || $id == 109)
        {
            $details['fields'] = ['date','preview'];
        }
        else if ($id == 110 || $id == 111)
        {
            $details['fields'] = ['link'];
        }
        else if ($id == 112)
        {
            $details['fields'] = ['preview'];
        }
        else if ($id == 113){
            $details['fields'] = ['contact_person', 'link'];
        }
        else if ($id == 114){
            $details['fields'] = ['code'];
        }
        else if ($id == 115){
            $details['fields'] = ['shipper','tracking_number'];
        }
        else if ($id == 116){
            $details['fields'] = ['shipper','tracking_number'];
        }
        else if ($id == 117){
            $details['fields'] = ['data','status'];
        }
        else if ($id == 118){
            $details['fields'] = ['product','sku_id'];
        }
        else if ($id == 118){
            $details['fields'] = ['product','sku_id'];
        }
        else if ($id == 200){
            $details['fields'] = ['admin','admin_user_name','full_name','department','designation'];
        }
        else if ($id == 201){
            $details['fields'] = ['admin','admin_user_name','full_name','department','designation'];
        }
        else if ($id == 202){
            $details['fields'] = ['admin','admin_user_name','trax_id','full_name','email','sonic_password','outlook_password'];
        }
        else if ($id == 203){
            $details['fields'] = ['date','preview'];
        }
        else if ($id == 204){
            $details['fields'] = ['date','sale_person','preview'];
        }
		else if ($id == 119){
            $details['fields'] = ['shipper_name','new_sale_person'];
        }
		else if ($id == 120){
            $details['fields'] = ['date', 'link'];
        }
        else if ($id == 121){
            $details['fields'] = ['date', 'link'];
        }
        else if ($id == 122){
            $details['fields'] = ['date', 'link'];
        }
        else if ($id == 123){
            $details['fields'] = ['date', 'link'];
        }
        else if ($id == 124){
            $details['fields'] = ['date', 'link'];
        }
        else if ($id == 125){
            $details['fields'] = ['date', 'link'];
        }
        else if ($id == 126){
            $details['fields'] = ['tracking_number','location'];
        }
        else if ($id == 127){
            $details['fields'] = ['date','contact_person','preview'];
        }
		else if ($id == 128){
            $details['fields'] = ['station_manager'];
        }
		else if ($id == 129){
            $details['fields'] = ['name','code'];
        }
		else if ($id == 130)
        {
            $details['fields'] = ['month','year','link'];
        }
		else if ($id == 131)
        {
            $details['fields'] = ['request_no'];
        }
		else if ($id == 132)
        {
            $details['fields'] = ['company_name', 'amount', 'tracking_number', 'rider', 'refusal_otp'];
        }
		else if ($id == 133)
        {
            $details['fields'] = ['erf_id','admin','date','link'];
        }
		else if ($id == 134)
        {
            $details['fields'] = ['company_name','preview'];
        }
        else if ($id == 135)
        {
            $details['fields'] = ['company_name', 'amount', 'tracking_number', 'rider', 'refusal_otp'];
        }
        else if ($id == 136)
        {
            $details['fields'] = ['shipper','message'];
        }
        else if ($id == 137)
        {
            $details['fields'] = ['rider_name','delivery_note','otp'];
        }
        else if ($id == 138)
        {
            $details['fields'] = ['name','code'];
        }
        else if ($id == 139 || $id == 140)
        {
            $details['fields'] = ['preview'];
        }
        else if ($id == 141)
        {
            $details['fields'] = ['preview','date','link'];
        }
        else if ($id == 142)
        {
            $details['fields'] = ['request_id','tracking_number','shipper_name','email','phone','channel','case_nature','case_nature_type','status','details'];
        }
        else if ($id == 143)
        {
            $details['fields'] = ['preview'];
        }
        else if ($id == 144)
        {
            $details['fields'] = ['rider_name','otp'];
        }

		else if ($id == 145)
        {
            $details['fields'] = ['tracking_number','status','reason','link'];
        }
		else if ($id == 146)
        {
            $details['fields'] = ['emp_id','name','designation','link'];
        }
		else if ($id == 147)
        {
            $details['fields'] = ['time','preview'];
        }
		else if ($id == 148)
        {
            $details['fields'] = ['hub','date','link'];
        }
		else if ($id == 149)
        {
            $details['fields'] = ['Shipper','person_of_contact','Shipper name'];
        }
        else if ($id == 150){
            $details['fields'] = ['preview'];
        }
        else if($id == 152 || $id == 153){
            $details['fields'] = ['shipper_name'];
        }
        else if ($id == 154){
            $details['fields'] = ['shipment_no','Sales_Person','shipper_name'];
        }
        else if ($id == 155){
            $details['fields'] = ['preview'];
        }
        else if ($id == 156)
        {
            $details['fields'] = ['month','year','link'];
        }
        else if ($id == 157)
        {
            $details['fields'] = ['date', 'link'];
        }
		else if ($id == 158)
        {
            $details['fields'] = ['rider_name', 'otp'];
        }
        else if ($id == 160)
        {
            $details['fields'] = ['date','preview', 'link'];
        }
        else if ($id == 162)
        {
            $details['fields'] = ['user_name','otp'];
        }
        else if ($id == 163)
        {
            $details['fields'] = ['tracking_no','status', 'reason'];
        }
        else if ($id == 165)
        {
            $details['fields'] = ['consignee','tracking_no', 'otp'];
        }
        else if ($id == 167)
        {
            $details['fields'] = ['status_code','link'];
        }
        else if ($id == 168)
        {
            $details['fields'] = ['preview','date'];
        }
        else if ($id == 166)
        {
            $details['fields'] = ['company_name','arrival_at','tracking_number','order_id','consignee_name','consignee_city'];
        }
        else if ($id == 169)
        {
            $details['fields'] = ['consignee','tracking_number','brand_name','amount'];
        }
        else if ($id == 170)
        {
            $details['fields'] = ['sale_person','preview'];
        }
        else if ($id == 171)
        {
            $details['fields'] = ['name'];
        }
        else if ($id == 172)
        {
            $details['fields'] = ['shipper_name','total_amount','status_link','payment_id'];
        }
		return $details;
    }

    public function edit(Request $request) {
        $notification = Notification::find($request->get('id'));

        if ($notification) {
            if ($notification->type_id == 1) {
                $notification->subject = $request->get('subject');
            }

            $notification->body = $request->get('body');
            $notification->updated_by = Auth::id();

            $notification->save();

            return ['status' => 0, 'success' => 'Notification has been edited'];
        }
        else {
            return ['status' => 1, 'error' => 'No Notication with given ID is present'];
        }
    }

    public function send_custom_notification(Request $request) {
        $title = $request->get('notification_title');
        $message = $request->get('notification_body');
        if ($request->get('notification_receiver') == 2) {
            $rider_ids = $request->get('riders');
            $device_tokens = EmployeeDeviceToken::whereIn('employee_id', $rider_ids)->where('employee_type_id', 2)->select('employee_id', 'device_token', 'employee_type_id');
        }
        elseif ($request->get('notification_receiver') == 1) {
            $employees_ids = $request->get('employees');
            $device_tokens = EmployeeDeviceToken::whereIn('employee_id', $employees_ids)->where('employee_type_id', 1)->select('employee_id', 'device_token', 'employee_type_id');
        }
        if($device_tokens->exists()){
            foreach($device_tokens->get() as $device_token){
                NotificationsController::bolt_app_notification($device_token->employee_id, $device_token->employee_type_id,$device_token->device_token, $title, $message);
            }

            return redirect()->back()->with('success', 'Custom Notification Sent');
        }
        return back()->withErrors('No Receiver to send notification to!');
    }

    public function app_notification_index(Request $request)
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(),450);
        $app_type = AppType::all();
        return view('admin.notifications.app_notifications_index')->with(['app_type'=>$app_type]);
    }

    public function app_notification_list(Request $request)
    {
        $notifications = AppNotification::join('admins as a', 'app_notifications.updated_by', '=', 'a.id')
            ->join('app_types as at', 'app_notifications.app_id', '=', 'at.id')
            ->select('app_notifications.id as id', 'app_notifications.name as name', 'app_notifications.app_id as app_id', 'app_notifications.updated_at as updated_at', 'a.name as updated_by', 'app_notifications.status as status', 'at.name as app_name');
        $datatables = Datatables::of($notifications)
            ->setRowAttr([
                'data-type' => function($notification) {
                    return $notification->app_id;
                },
            ])
            ->editColumn('status', function ($notification) {
                return (($notification->status) ? 'Enabled' : 'Disabled');
            })
            ->addColumn('action', function ($notification) {
                if (session('role_id') == 1 || in_array(602, session('permissions')) || session('role_id') == 1 || in_array(603, session('permissions'))) {
                    $edit_button = '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';
                    $enable_button = '<button type="button" class="dropdown-item enable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
                    $disable_button = '<button type="button" class="dropdown-item disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';

                    $dropdown = '
                <div class="btn-group">
                  <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                  <div class="dropdown-menu dropdown-menu-sm">
            ';

                    if (session('role_id') == 1 || in_array(602, session('permissions'))) {
                        $dropdown .= $edit_button;
                    }

                    if (session('role_id') == 1 || in_array(603, session('permissions'))) {
                        if ($notification->status) {
                            $dropdown .= $disable_button;
                        } else {
                            $dropdown .= $enable_button;
                        }
                    }

                    $dropdown .= '
                  </div>
                </div>
            ';

                    return $dropdown;
                } else {
                    return "";
                }
            });

        return $datatables->make(true);
    }

    public function app_notification_status(Request $request) {
        $notification = AppNotification::find($request->id);
        if ($notification) {
            $notification->status = $request->status;
            $notification->updated_by = Auth::id();

            $notification->save();

            if ($request->status) {
                return ['status' => 0, 'success' => 'Notification has been enabled'];
            }
            else {
                return ['status' => 0, 'success' => 'Notification has been disabled'];
            }
        }
        else {
            return ['status' => 1, 'error' => 'No Notification with given ID is present'];
        }
    }

    public function app_notification_details(Request $request) {
        $id = $request->id;
        $notification = AppNotification::find($id);
        if($notification){
            $details = array();
            $details['title'] = $notification->title;
            $details['body'] = $notification->body;
            if($id == 1){
                $details['fields'] = ["shipper_name", "rider"];
            }else if($id == 2){
                $details['fields'] = ["shipper_name", "rider"];
            }else if($id == 3){
                $details['fields'] = ["shipper_name"];
            }else if($id == 4){
                $details['fields'] = ["shipper_name"];
            }else if($id == 5){
                $details['fields'] = ["note_id"];
            }else if($id == 6){
                $details['fields'] = ["note_id"];
            }else if($id == 7){
                $details['fields'] = ["shipper_name", "tracking_no", "status_name"];
            }else if($id == 8){
                $details['fields'] = ["consignee_name", "tracking_no", "status_name"];
            }else if($id == 9){
                $details['fields'] = ["rider", "otp"];
            }else if($id == 10){
                $details['fields'] = ["time", "name"];
            }else if($id == 11){
                $details['fields'] = ["from", "to", "status"];
            }else if($id == 12){
                $details['fields'] = ["employee_name","trax_id","from", "to"];
            }else if($id == 13){
                $details['fields'] = ["employee_name","trax_id","from", "to"];
            }else if($id == 14){
                $details['fields'] = ["date","sale_person","lead_id"];
            }else if($id == 15){
                $details['fields'] = ["sale_person","lead_id"];
            }else if($id == 16){
                $details['fields'] = ["sale_person","shipper_names"];
            }
            return $details;
        }else{
            return ['status' => 1, 'error' => 'No Notification with given ID is present'];
        }

    }

    public function app_notification_edit(Request $request) {
        $notification = AppNotification::find($request->id);
        if ($notification) {
            $notification->title = $request->get('title');
            $notification->body = $request->get('body');
            $notification->updated_by = Auth::id();
            $notification->save();
            return ['status' => 0, 'success' => 'Notification has been edited'];
        }
        else {
            return ['status' => 1, 'error' => 'No Notication with given ID is present'];
        }
    }
}