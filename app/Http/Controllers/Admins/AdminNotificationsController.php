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
use App\Http\Models\SMS;
use Illuminate\Support\Facades\Storage;

use Auth;
use function foo\func;

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
        ->select('notifications.id', 'notifications.name', 'notifications.type_id', 'nt.name as type', 'notifications.updated_at as updated', 'a.name as updated_by', 'notifications.status');

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
        })->rawColumns(['action']);

        return $datatables->make(true);
    }

    public function send_custom_email(Request $request) {
        
        $attachments = array();
        $from_email = $request->notification_sender;
        
        if ($request->get('receiver') == 1) {
            if($request->get('search_hub') == 0) {
                $emails = Admin::all()->pluck('email')->toArray();
            }
            else {
                $emails = Admin::where('default_hub_id', '=', $request->get('search_hub'))->pluck('email')->toArray();
            }
        }
        else {
            $segment = isset($request->segment) ? $request->segment : null ;

            if ($request->get('shipper_status') == 1) {
                $emails = User::where('status', '=', 3)->where('blacklist', '=', 0);
            }
            else if ($request->get('shipper_status') == 2) {
                $emails = User::where('status', '=', 4)->where('blacklist', '=', 0);
            }
            else {
                $emails = User::where('blacklist', '=', 0);
            }

            if ($segment != null && $segment != 3) {
                $emails = $emails->where('segment_id', $segment);
            }

            if($request->get('search_city') != 0){
                $emails = $emails->where('city_id', '=', $request->get('search_city'));
            }

            $emails = $emails->get()->pluck('email')->toArray();
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


            $totalItems = count($emails);
            
            $chunkSize = 25;
            $chunks = array_chunk($emails, $chunkSize);
           
            foreach ($chunks as $parsed_emails) {
                NotificationsController::custom(1, $subject, $body, $parsed_emails, $from_email);
            }
            // for ($offset = 0; $offset < $totalItems; $offset += $chunkSize) {
            //     $parsed_emails = array_slice($emails, $offset, $chunkSize);
                
            //     NotificationsController::custom(1, $subject, $body, $parsed_emails,$from_email);
                
            // }
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
            $details['receiver'] = ['Shipper', 'Sales Person'];

            $details['fields'] = ['account_id', 'company_name', 'email', 'person_of_contact', 'phone_no_1', 'phone_no_2', 'address', 'city', 'cnic', 'ntn_no', 'api_token'];
        }
        else if ($id == 2) {
            $details['receiver'] = ['Shipper Email'];

            $details['fields'] = ['account_id', 'company_name', 'service_type', 'pickup_address', 'pickup_city', 'order_id', 'pickup_date', 'shipping_mode', 'amount', 'payment_mode', 'tracking_number'];
        }
        else if ($id == 3) {
            $details['receiver'] = ['Consignee Phone Number'];

            $details['fields'] = ['company_name', 'service_type', 'pickup_address', 'pickup_city', 'consignee_name', 'consignee_address', 'consignee_city', 'order_id', 'shipping_mode', 'amount', 'payment_mode', 'tracking_number'];
        }
        else if ($id == 4) {
            $details['receiver'] = ['Shipper Email'];
            
            $details['fields'] = ['company_name', 'arrival_at', 'pickup_city', 'consignee_name', 'consignee_city', 'order_id', 'weight', 'tracking_number', 'item_product_type', 'item_description', 'item_quantity', 'amount'];
        }
        else if ($id == 5) {
            $details['receiver'] = ['Shipper Email'];
             
            $details['fields'] = ['cargo_number', 'company_name', 'departure_at', 'order_id', 'tracking_number'];
        }
        else if ($id == 6) {
            $details['receiver'] = ['Shipper Phone Number'];

            $details['fields'] = ['cargo_number', 'company_name', 'departure_at', 'order_id', 'tracking_number'];
        }
        else if ($id == 7) {
            $details['receiver'] = ['Shipper Email'];
             
            $details['fields'] = ['cargo_number', 'arrival_at', 'company_name', 'order_id', 'tracking_number'];
        }
        else if ($id == 8) {
            $details['receiver'] = ['Shipper Phone Number'];
            
            $details['fields'] = ['cargo_number', 'arrival_at', 'company_name', 'order_id', 'tracking_number'];
        }
        else if ($id == 9) {
            $details['receiver'] = ['Role-Department Head (Operations)','Role-Department Head (Sales)'];
            
            $details['fields'] = ['cargo_number', 'departure_at', 'seal_number', 'builty_number', 'expected_arrival_date', 'shipping_mode', 'transport_mode', 'vendor', 'sender', 'tracking_number'];
        }
        else if ($id == 10) {
            $details['receiver'] = ['Shipper Email'];
            
            $details['fields'] = ['delivery_note_number', 'rider', 'company_name', 'departure_at', 'order_id', 'tracking_number'];
        }
        else if ($id == 11) {
            $details['receiver'] = ['Shipper Phone Number'];
             
            $details['fields'] = ['delivery_note_number', 'rider', 'company_name', 'departure_at', 'order_id', 'tracking_number'];
        }
        else if ($id == 12) {
            $details['receiver'] = ['Consignee Phone Number'];

            $details['fields'] = ['delivery_note_number', 'rider', 'company_name', 'departure_at', 'consignee_name', 'consignee_address', 'order_id', 'amount', 'payment_mode', 'tracking_number', 'refusal_otp','online_payment_link', 'tracking_link'];
        }
        else if ($id == 13) {
             $details['receiver'] = ['Shipper Email'];
             
            $details['fields'] = ['delivery_note_number', 'rider', 'company_name', 'departure_at', 'order_id', 'tracking_number', 'status'];
        }
        else if ($id == 14) {
             $details['receiver'] = ['Shipper Email'];
             
            $details['fields'] = ['delivery_note_number', 'rider', 'company_name', 'departure_at', 'order_id', 'tracking_number', 'status'];
        }
        else if ($id == 15) {
            $details['receiver'] = ['Shipper Email'];
            
            $details['fields'] = ['return_note_number', 'rider', 'company_name', 'departure_at', 'order_id', 'tracking_number', 'status'];
        }
        else if ($id == 16) {
            $details['receiver'] = ['Shipper Phone Number'];
            
            $details['fields'] = ['return_note_number', 'rider', 'company_name', 'departure_at', 'order_id', 'tracking_number', 'status'];
        }
        else if ($id == 17) {
             $details['receiver'] = ['Shipper Email'];
             
             $details['fields'] = ['account_id', 'company_name', 'service_type', 'order_id', 'tracking_number', 'new_tracking_number'];
        }
        else if ($id == 18) {
             $details['receiver'] = ['Shipper Phone Number'];
             
             $details['fields'] = ['account_id', 'company_name', 'service_type', 'order_id', 'tracking_number', 'new_tracking_number'];
        }
        else if ($id == 19) {
             $details['receiver'] = ['Shipper/Admins'];
             
             $details['fields'] = ['dispute_number', 'dispute_type', 'dispute_description', 'launched_by', 'city', 'tracking_number'];
        }
        else if ($id == 20) {
             $details['receiver'] = ['Shipper Email'];
             
             $details['fields'] = ['company_name', 'city', 'bank', 'bank_branch', 'account_number', 'account_title', 'iban', 'account_city', 'payment_cycle', 'payment_done_id', 'payment_done_at', 'total_shipments', 'delivered_shipments', 'returned_shipments', 'adjusted_shipments', 'total_amount', 'total_weight_charges', 'total_cash_handling_charges', 'total_insurance_charges', 'total_replacement_charges', 'total_return_charges', 'total_packaging_material_charges', 'total_fuel_surcharge', 'total_gst', 'total_charges', 'total_payable', 'consignee_name', 'consignee_city', 'order_id', 'estimated_weight', 'actual_weight', 'chargeable_weight', 'tracking_number', 'amount', 'weight_charges', 'cash_handling_charges', 'charges', 'gst', 'payable']; //total_try_and_buy_charges
        }
        else if ($id == 21) {
             $details['receiver'] = ['Shipper','Role-Department Head (Sales)'];

             $details['fields'] = ['company_name', 'service_type', 'pickup_address', 'pickup_city', 'consignee_name', 'consignee_address', 'consignee_city', 'order_id', 'amount', 'payment_mode', 'tracking_number'];
        }
        else if ($id == 22) {
             $details['receiver'] = ['Rider Phone Number'];
             
             $details['fields'] = ['company_name', 'person_of_contact', 'phone_number', 'address', 'city'];
        }
        else if ($id == 23) {
             $details['receiver'] = ['Shipper Email'];
             
             $details['fields'] = ['company_name', 'service_type', 'pickup_address', 'pickup_city', 'consignee_name', 'consignee_phone_number_1', 'consignee_phone_number_2', 'consignee_email', 'consignee_address', 'consignee_city', 'order_id', 'shipping_mode', 'amount', 'payment_mode', 'status', 'status_reason', 'status_date', 'arrival_date', 'tracking_number'];
        }
        else if ($id == 24) {
            $details['receiver'] = ['Admins/Role-Zonal Manager (Operations)','Admins/Role-Station Manager (Operations)'];

            $details['fields'] = ['hub', 'service_type', 'pickup_address', 'pickup_city', 'consignee_name', 'consignee_phone_number_1', 'consignee_phone_number_2', 'consignee_email', 'consignee_address', 'consignee_city', 'order_id', 'shipping_mode', 'status', 'status_reason', 'status_date', 'tracking_number'];
        }
        else if ($id == 25) {
             $details['receiver'] = ['Role-Zonal Manager (Operations)','Role-Station Manager (Operations)','Role-Karachi Operations-Ramish (Operations)','Role-Sales and Operations (Administration)'];

             $details['fields'] = ['hub', 'service_type', 'pickup_address', 'pickup_city', 'consignee_name', 'consignee_phone_number_1', 'consignee_phone_number_2', 'consignee_email', 'consignee_address', 'consignee_city', 'order_id', 'shipping_mode', 'status', 'status_reason', 'status_date', 'tracking_number'];
        }
        else if ($id == 26) {
            $details['receiver'] = ['mohsin.ali@trax.pk','waqas@trax.pk','munawar.shamsi@logiserves.com','munawar.shamsi@logiserves.com;','fawad.ahmed@trax.pk','nadir.qureshi@trax.pk','hammad.saleem@trax.pk','rahat.ali@trax.pk','hassan.arman@trax.pk','m.sohail@trax.pk','ghazanfar.ali@trax.pk','BCC-(muhammad.waqas@trax.pk','faisal.hasan@trax.pk','munawar.shamsi@logiserves.com'];

            $details['fields'] = ['date', 'link'];
        }
        else if ($id == 27) {
            $details['receiver'] = ['Shipper','Shipper Billing Person','Sales Person','CC-Role-Department Head (Sales)','CC-Role-Sales and Operations (Administration)'];

            $details['fields'] = ['account_id', 'company_name', 'invoice_number', 'billing_period_from_date', 'billing_period_to_date', 'due_date', 'invoice'];
        }
        else if ($id == 28) {
            $details['receiver'] = ['Shipper','Shipper Billing Person','CC-Role-Department Head (Finance)'];

            $details['fields'] = ['account_id', 'company_name', 'invoice_number', 'billing_period_from_date', 'billing_period_to_date', 'due_date', 'invoice'];
        }
        else if ($id == 29) {
            $details['fields'] = ['pin_code'];
        }
        else if ($id == 30) {
            $details['fields'] = ['poc','receiving_of_pickup','company_name','tracking_number','order_id','destination','service_type','amount','quantity','product_type','description','estimated_weight'];
        }
		else if($id == 31){
            $details['receiver'] = ['Admin/complaints@trax.pk'];

            $details['fields'] = ['request_id','tracking_number','shipper_name','email','phone','destination','channel','case_nature','case_nature_type','details','status'];
        }
        else if ($id == 32) {
            $details['receiver'] = ['ShipperNotificationEmail/Shipper'];

            $details['fields'] = ['nsa', 'tracking_number'];
        }
        else if ($id == 33) {
            $details['receiver'] = ['ShipperNotificationEmail/Shipper','CC-Role-Department Head (Operations)','CC-Role-Senior Officer (Finance)','CC-Role-Officer (Finance)','CC-Role-Regional Manager (Operations),CC-Role-Zonal Manager (Operations)'];

            $details['fields'] = ['tracking_number','destination','nsa_osa_estimated_charges','remarks'];
        }
        else if ($id == 34) {
            $details['receiver'] = ['Role-Department Head (Finance)','Role-Department Head (Sales)'];

            $details['fields'] = ['user_id', 'updated_at', 'tagged_sales_person'];
        }
        else if ($id == 35) {
             $details['receiver'] = ['Consignee Phone Number'];

             $details['fields'] = ['consignee_name', 'shipper_name', 'tracking_number', 'receiver_name','status_date','order_id'];
        }
        else if ($id == 36 || $id == 37) {
             $details['receiver'] = ['Shipper Email'];

             $details['fields'] = ['account_id', 'company_name_b', 'company_name_a', 'trax_logo'];
        }
		else if ($id == 38){
            $details['receiver'] = ['Shipper Email'];

            $details['fields'] = ['shipper_name','button','trax_logo','link'];
        }
        else if ($id == 39) {
            $details['receiver'] = ['ShipperNotificationEmail/Shipper'];

            $details['fields'] = ['company_name','tracking_number', 'status_updated_at', 'receiver_name','returned_at','shipments_count','return_notes_id','return_detail'];
        }else if ($id == 40) {
            $details['receiver'] = ['Rider Phone Number'];

            $details['fields'] = ['rider_name','delivery_note_id','password'];
        }
        else if ($id == 41) {
            $details['receiver'] = ['Shipper Email'];

            $details['fields'] = ['request_id'];
        }
        else if ($id == 42) {
            $details['receiver'] = ['Shipper Phone Number'];

            $details['fields'] = ['rider_name', 'rider_phone_number'];
        }
        else if ($id == 43) {
            $details['receiver'] = ['Pickup Address Email'];

            $details['fields'] = ['vendor', 'shipper_name', 'shipments_detail'];
        }
        else if ($id == 44) {
            $details['receiver'] = ['Role-Zonal Manager (Operations),Role-Station Manager (Operations)','CC-Role-Department Head (Operations)','CC-Role-Regional Manager (Operations)','CC-Role-Debriefing Officer (Customer Experience)','CC-Role-Lead Debriefer (Customer Experience)','CC-Role-Network Manager (Operations)'];

            $details['fields'] = ['hub', 'date', 'link', 'preview'];
        }
        else if ($id == 45) {
            $details['receiver'] = ['Role-Sales and Operations (Administration)'];

            $details['fields'] = ['zone', 'date', 'link', 'preview'];
        }
        else if ($id == 46) {
            $details['receiver'] = ['uzair.anees@trax.pk','CC-(shahbaz.abbasi@trax.pk)'];

            $details['fields'] = ['date', 'link', 'preview'];
        }
        else if ($id == 47) {
            $details['receiver'] = ['abbas.ali@trax.pk','ali.cheema@trax.pk','tanveer.malik@trax.pk','waqas@trax.pk','khan.usama@trax.pk','munawar.shamsi@logiserves.com','fawad.ahmed@trax.pk','nadir.qureshi@trax.pk','m.sohail@trax.pk','CC-(muhammad.waqas@trax.pk','CC-(faisal.hasan@trax.pk)','CC-(munawar.shamsi@logiserves.com)'];

            $details['fields'] = ['date', 'link', 'preview'];
        }
        else if ($id == 48) {
            $details['receiver'] = ['Role-Department Head (Finance)','Role-Department Head (Operations)','Role-Department Head (Sales)','Role-Regional Manager (Operations)','Role-Zonal Manager (Operations)','Role-Station Manager (Operations)','Role-Network Manager (Operations)','Role-Team Leader BI (Customer Experience)','Role-Karachi Operations-Ramish (Operations)','Role-Sales and Operations (Administration)'];

            $details['fields'] = ['date', 'link', 'preview'];
        }
        else if ($id == 49) {
            $details['receiver'] = ['mohsin.ali@trax.pk','waqas@trax.pk','khan.usama@trax.pk','munawar.shamsi@logiserves.com','asad@trax.pk','fawad.ahmed@trax.pk','nadir.qureshi@trax.pk','m.sohail@trax.pk','BCC-(muhammad.waqas@trax.pk)'];

            $details['fields'] = ['date', 'link', 'preview'];
        }
        else if ($id == 50) {
            $details['receiver'] = ['Pickup Address Email'];

            $details['fields'] = ['status', 'contact_person', 'rider', 'rider_phone', 'vendor', 'shipper_name'];
        }
        else if ($id == 51) {
            $details['receiver'] = ['Pickup Address Phone Number'];

            $details['fields'] = ['status', 'contact_person', 'rider', 'rider_phone', 'vendor', 'shipper_name'];
        }
		else if ($id == 52) {
            $details['fields'] = ['contact_person', 'company_name', 'rider_name', 'rider_phone_number', 'order_id', 'tracking_number'];
        }
		else if ($id == 53) {
            $details['receiver'] = ['Role-Station Manager (Operations)','CC-Role-Zonal Manager (Operations)'];

            $details['fields'] = ['hub', 'date', 'preview','link'];
        }
		else if ($id == 54) {
            $details['receiver'] = ['Role-Zonal Manager (Operations)','CC-Role-Regional Manager (Operations)'];

            $details['fields'] = ['zone', 'date', 'preview','link'];
        }
		else if ($id == 55) {
            $details['receiver'] = ['Role-Department Head (Finance)','Role-Department Head (Operations)','Role-Department Head (Sales)'];

            $details['fields'] = ['date', 'preview','link'];
        }
        else if ($id == 56){
            $details['receiver'] = ['Sales Person','CC-(fawad@outlook.com)','CC-(waqar@outlook.com)','CC-(talha.motiwala@trax.pk)','CC-(shafay.tariq@trax.pk)','CC-(wajiha.majeed@trax.pk)'];

            $details['fields'] = ['account_id', 'shipper_name','preview', 'sale_person'];
        }
		else if ($id == 57){
            $details['receiver'] = ['Shipper','Role-Sales and Operations (Administration)','Role-Team Lead (Sales)','CC-Role-Department Head (Sales)'];

            $details['fields'] = ['preview'];
        }
        else if ($id == 58){
            $details['receiver'] = ['Shipper Phone Number'];

            $details['fields'] = ['shipper_name'];
        }
        else if ($id == 59){
            $details['receiver'] = ['anas.anwer@trax.pk','uzair.anees@trax.pk'];

            $details['fields'] = ['shipping_mode', 'date', 'preview', 'link'];
        }
        else if ($id == 60){
            $details['receiver'] = ['Sales Person','CC-Role-Department Head (Finance),CC-Role-Department Head (Sales)'];

            $details['fields'] = ['preview', 'date'];
        }
        else if ($id == 61){
            $details['receiver'] = ['Rider Phone Number'];

            $details['fields'] = ['rider_name', 'pin'];
        }
        else if ($id == 62){
            $details['receiver'] = ['Shipper Email'];

            $details['fields'] = ['cancel_shipment'];
        }
        else if ($id == 63){
            $details['receiver'] = ['Shipper Email'];

            $details['fields'] = ['pickup_request_ID', 'shipper', 'date'];
        }
        else if ($id == 64){
            $details['receiver'] = ['Role-(Department Head (Sales))','CC-(Role-(Department Head (Finance)))'];

            $details['fields'] = ['account_id', 'name'];
        }
        else if ($id == 65){
            $details['receiver'] = ['Admin Email'];

            $details['fields'] = ['request_id', 'case_nature', 'case_nature_type'];
        }
        else if ($id == 66){
            $details['receiver'] = ['CrmEscalationTaggingLevelEmail STATUS-1','CC-(CrmEscalationTaggingLevelEmail STATUS-2)','BCC-(CrmEscalationTaggingLevelEmail STATUS-3)'];

            $details['fields'] = ['request_id', '[escalation].'];
        }
		else if ($id == 67){
            $details['receiver'] = ['sheharyar.majid@trax.pk','shafay.tariq@trax.pk','talha.motiwala@trax.pk'];

            $details['fields'] = ['zero_report'];
        }
		else if ($id == 68 || $id == 69 ||  $id == 70 ||  $id == 71 || $id == 72){
            if($id == 68 || $id == 69) {
                $details['receiver'] = ['Role-Zonal Manager (Operations)','Role-Station Manager (Operations)','CC-Role-Department Head (Operations)','CC-Role-Department Head (Customer Experience)','CC-Role-Regional Manager (Operations)','CC-Role-Team Leader (Customer Experience)','CC-Role-Debriefing Officer (Customer Experience)','CC-Role-Lead Debriefer (Customer Experience)','CC-Role-Network Manager (Operations)','CC-Role-HR/Admin officer (Human Resources)'];
            }  else if ($id ==  70) {
                $details['receiver'] = ['m.sohail@trax.pk','CC-Role-Department Head (Finance)','CC-Role-Department Head (Operations)','CC-Role-Department Head (Sales)','CC-Role-Department Head (Customer Experience)','CC-Role-Team Leader (Customer Experience)','CC-Role-Debriefing Officer (Customer Experience)','CC-Role-Team Leader BI (Customer Experience)','CC-Role-Karachi Operations-Ramish (Opertions)'];
            } else if  ($id == 71) {
                $details['receiver'] = ['fawad@outlook.com','shafay.tariq@trax.pk','faizan.ahmed@trax.pk'];
            } else if  ($id == 72) {
                $details['receiver'] = ['fawad@outlook.com','shafay.tariq@trax.pk','faizan.ahmed@trax.pk'];
            }

            $details['fields'] = ['preview'];
        }
		else if ($id == 73){
            $details['receiver'] = ['Shipper','BCC-Role-Station Manager (Operations))'];

            $details['fields'] = ['pickup_request_id', 'tracking_numbers', 'company_name', 'pickup_city'];
        }
		else if ($id == 74){
            $details['receiver'] = ['fawad@outlook.com','talha.motiwala@trax.pk','wajiha.majeed@trax.pk'];

            $details['fields'] = ['preview','link'];
        }
		else if ($id == 75){
            $details['receiver'] = ['Consignee Phone Number'];

            $details['fields'] = ['name','address'];
        }
		else if ($id == 76){
            $details['receiver'] = ['Role-Station Manager (Operations)','Role-Cashier (Finance)','aamir.sohail@trax.pk','fawad.ahmed@trax.pk','CC-Role-Department Head (Finance)','CC-Role-Department Head (Operations)','CC-Role-Regional Manager (Operations)','CC-Role-Zonal Manager (Operations)'];

            $details['fields'] = ['hub','date','link'];
        }
		else if ($id == 77){
            $details['receiver'] = ['Pickup Address Phone Number'];

            $details['fields'] = ['rider_name','rider_phone'];
        }
		else if ($id == 78){
            $details['receiver'] = ['Role-Regional Manager (Operations)','Role-Zonal Manager (Operations)','Role-Station Manager (Operations)','Role-Team Lead (Sales)'];

            $details['fields'] = ['hub','date'];
        }
		else if ($id == 79){
            $details['receiver'] = ['Role-Regional Manager (Operations)','Role-Zonal Manager (Operations)'];

            $details['fields'] = ['zone','date'];
        }
		else if ($id == 80){
            $details['receiver'] = ['Role-Department Head (Operations)'];

            $details['fields'] = ['date'];
        }
		else if ($id == 81){
            $details['receiver'] = ['Sales Person','CC-(waqas@trax.pk)','CC-(khan.usama@trax.pk)','CC-(shahrukh.raheem@trax.pk/nabeel.ahmed@trax.pk','ali.qureshi@trax.pk','adeel.ali@trax.pk'];

            $details['fields'] = ['preview'];
        }
		else if ($id == 82){
            $details['receiver'] = ['syed.furqan@trax.pk','m.sohail@trax.pk','fawad.ahmed@trax.pk','aftab.qidwai@trax.pk','wajiha.majeed@trax.pk','huzaifa.aamir@trax.pk','mohsin.khan@trax.pk','BCC-(muhammad.waqas@trax.pk)','BCC-(noman.arshad@trax.pk)'];

            $details['fields'] = ['preview','link'];
        }
		else if($id == 83)
		{
            $details['receiver'] = ['Shipper Email'];

            $details['fields'] = ['shipment_picked_date','rider_name','shipper_name','requested_date','number'];
        }
		else if ($id == 84){
            $details['receiver'] = ['Shipper Email'];

            $details['fields'] = ['date','tracking_number','shipper_name','product_description','cod_amount','origin', 'destination', 'status','preview'];
        }
		else if ($id == 85){
            $details['receiver'] = ['Admin/fawad@outlook.com,shafay.tariq@trax.pk,talha.motiwala@trax.pk'];

            $details['fields'] = ['preview'];
        }
		else if ($id == 86 || $id == 87){
            if($id == 86) {
                $details['receiver'] = ['Role-Department Head (Operations)','Role-Regional Manager (Operations)','Role-Zonal Manager (Operations)','Role-Station Manager (Operations)','Role-Supply Chain Executive (Operations)','Role-HR/Admin officer (Human Resources)'];
            } else if ($id == 87) {
                $details['receiver'] = ['Role-Zonal Manager (Operations)','Role-Station Manager (Operations)','Role-Supply Chain Executive (Operations)'];
            }
            $details['fields'] = ['hub','date','link'];
        }
		else if ($id == 88){
            $details['receiver'] = ['syed.sharique@trax.pk'];

            $details['fields'] = ['runner','date','link'];
        }
        else if ($id == 89) {
            $details['receiver'] = ['fawad@outlook.com','talha.motiwala@trax.pk'];

            $details['fields'] = ['link'];
        }
		else if ($id == 90)
		{
            $details['receiver'] = ['fawad@outlook.com','talha.motiwala@trax.pk','usama.shahid@trax.pk','faizan.ahmed@trax.pk'];

            $details['fields'] = ['link'];
        }
		else if ($id == 91)
		{
            $details['receiver'] = ['Shipper Phone Number'];

            $details['fields'] = ['pin'];
        }
        else if ($id == 92)
        {
            $details['receiver'] = ['Shipper/wajiha.majeed@trax.pk','CC-(Sales Person )'];

            $details['fields'] = ['shipper_name','payment_id'];
        }
        else if ($id == 96)
        {
            $details['receiver'] = ['Shipper Email'];

            $details['fields'] = ['link'];
        }
        else if ($id == 98)
        {
            $details['fields'] = ['preview'];
        }
        else if ($id == 99)
        {
            $details['receiver'] = ['Sales Person Email'];

            $details['fields'] = ['sales_person','preview'];
        }
        else if ($id == 100)
        {
            $details['receiver'] = ['Sales Person Email'];

            $details['fields'] = ['head_of_sales','preview'];
        }
        else if ($id == 101 || $id == 102)
        {
            if ($id == 101)
            {
                $details['receiver'] = ['Role-Zonal Manager (Operations)','Role-Station Manager (Operations)'];

            } else if($id == 102) {

                $details['receiver'] = ['Role-Regional Manager (Operations)','Role-Zonal Manager (Operations)'];
            }
            $details['fields'] = ['hub','date','preview'];
        }
        else if ($id == 103)
        {
            $details['receiver'] = ['Role-Department Head (Operations)'];

            $details['fields'] = ['date','preview'];
        }
        else if ($id == 105)
        {
            $details['receiver'] = ['Sales Person Phone Number'];

            $details['fields'] = ['sales_person','pickup_address','city_name','reason'];
        }
        else if ($id == 106)
        {
            $details['receiver'] = ['Rider Phone Number'];

            $details['fields'] = ['old_rider_name','pickup_request_id','pickup_coordinator_name','new_rider_name'];
        }
        else if ($id == 107)
        {
            $details['receiver'] = ['Rider Phone Number'];

            $details['fields'] = ['new_rider_name','pickup_request_id','pickup_coordinator_name','old_rider_name'];
        }
        else if ($id == 108 || $id == 109)
        {

            $details['receiver'] = ['balaj.khan@trax.pk','syed.asif@trax.pk'];

            $details['fields'] = ['date','preview'];
        }
        else if (/* $id == 110 || */ $id == 111)
        {
            $details['receiver'] = ['uzair.anees@trax.pk','shahbaz.abbasi@trax.pk'];

            $details['fields'] = ['link'];
        }
        else if ($id == 112)
        {
            $details['receiver'] = ['syed.sharique@trax.pk','uzair.anees@trax.pk','balaj.khan@trax.pk'];

            $details['fields'] = ['preview'];
        }
        else if ($id == 113){
            $details['receiver'] = ['Lead Email Address'];

            $details['fields'] = ['contact_person', 'link','sales_person','sales_person_contact'];
        }
        else if ($id == 114){
            $details['receiver'] = ['Shipper Phone Number'];

            $details['fields'] = ['code'];
        }
        else if ($id == 115){
            $details['receiver'] = ['Shipper Phone Number'];

            $details['fields'] = ['shipper','tracking_number', 'total_charges'];
        }
        else if ($id == 116){
            $details['receiver'] = ['Shipper Phone Number'];

            $details['fields'] = ['shipper','tracking_number'];
        }
        else if ($id == 117){
            $details['receiver'] = ['Sales Person Email'];

            $details['fields'] = ['data','status'];
        }
        else if ($id == 118){
            $details['fields'] = ['product','sku_id'];
        }
        else if ($id == 118){
            $details['fields'] = ['product','sku_id'];
        }
        else if ($id == 200){
            $details['receiver'] = ['Admin '];

            $details['fields'] = ['admin','admin_user_name','full_name','department','designation'];
        }
        else if ($id == 201){
            $details['receiver'] = ['Admin Email'];

            $details['fields'] = ['admin','admin_user_name','full_name','department','designation'];
        }
        else if ($id == 202){
            $details['receiver'] = ['Admin','CC-(Admin)'];

            $details['fields'] = ['admin','admin_user_name','trax_id','full_name','email','sonic_password','outlook_password'];
        }
        else if ($id == 203){
            $details['receiver'] = ['waqas@trax.pk'];

            $details['fields'] = ['date','preview'];
        }
        else if ($id == 204){
            $details['receiver'] = ['Sales Person Email'];

            $details['fields'] = ['date','sale_person','preview'];
        }
		else if ($id == 119){
            $details['receiver'] = ['Sales Person Phone Number'];

            $details['fields'] = ['shipper_name','new_sale_person'];
        }
		else if ($id == 120){
            $details['receiver'] = ['Sales Person Email'];

            $details['fields'] = ['date', 'link'];
        }
        else if ($id == 121){
            $details['receiver'] = ['Admin Email'];

            $details['fields'] = ['date', 'link'];
        }
        else if ($id == 122){
            $details['receiver'] = ['Sales Person Email'];

            $details['fields'] = ['date', 'link'];
        }
        else if ($id == 123){
            $details['receiver'] = ['Admin Email'];

            $details['fields'] = ['date', 'link'];
        }
        else if ($id == 124){
            $details['receiver'] = ['Sales Person Email'];

            $details['fields'] = ['date', 'link'];
        }
        else if ($id == 125){
            $details['receiver'] = ['Admin Email'];

            $details['fields'] = ['date', 'link'];
        }
        else if ($id == 126){
            $details['receiver'] = ['Consignee Phone Number'];

            $details['fields'] = ['tracking_number','location'];
        }
        else if ($id == 127){
            $details['receiver'] = ['Admin Email'];

            $details['fields'] = ['date','contact_person','preview'];
        }
		else if ($id == 128){
            $details['receiver'] = ['Admin Email'];

            $details['fields'] = ['station_manager'];
        }
		else if ($id == 129){
            $details['receiver'] = ['Retail Phone Number'];

            $details['fields'] = ['name','code'];
        }
		else if ($id == 130)
        {
            $details['receiver'] = ['adnan.ahsan@trax.pk,fawad.ahmed@trax.pk,hammad.majid@trax.pk,m.sohail@trax.pk,ghazanfar.ali@trax.pk','CC-(muhammad.waqas@trax.pk,faisal.hasan@trax.pk)'];

            $details['fields'] = ['month','year','link'];
        }
		else if ($id == 131)
        {
            $details['receiver'] = ['Admin Email'];

            $details['fields'] = ['request_no'];
        }
		else if ($id == 132)
        {
            $details['receiver'] = ['Consignee Phone Number'];

            $details['fields'] = ['company_name', 'amount', 'tracking_number', 'rider', 'refusal_otp', 'consignee_name'];
        }
		else if ($id == 133)
        {
            $details['receiver'] = ['Admin Email'];

            $details['fields'] = ['erf_id','admin','date','link'];
        }
		else if ($id == 134)
        {
            $details['receiver'] = ['Pickup Address Email'];

            $details['fields'] = ['company_name','preview'];
        }
        else if ($id == 135)
        {
            $details['receiver'] = ['Consignee Phone Number'];

            $details['fields'] = ['company_name', 'amount', 'tracking_number', 'rider', 'refusal_otp', 'consignee_name'];
        }
        else if ($id == 136)
        {
            $details['receiver'] = ['Shipper Email'];

            $details['fields'] = ['shipper','message'];
        }
        else if ($id == 137)
        {
            $details['receiver'] = ['Rider Phone Number'];

            $details['fields'] = ['rider_name','delivery_note','otp'];
        }
        else if ($id == 138)
        {
            $details['receiver'] = ['Admin Phone Number'];

            $details['fields'] = ['name','code'];
        }
        else if ($id == 139 || $id == 140)
        {
            if($id == 139) {
                $details['receiver'] = ['Admin Email'];
            } else if ($id == 140) {
                $details['receiver'] = ['abdul.ahad@trax.pk'];
            }

            $details['fields'] = ['preview'];
        }
        else if ($id == 141)
        {
            $details['receiver'] = ['syed.furqan@trax.pk,m.sohail@trax.pk,fawad.ahmed@trax.pk,aftab.qidwai@trax.pk,wajiha.majeed@trax.pk,huzaifa.aamir@trax.pk,mohsin.khan@trax.pk','BCC-(muhammad.waqas@trax.pk,noman.arshad@trax.pk)'];

            $details['fields'] = ['preview','date','link'];
        }
        else if ($id == 142)
        {
            $details['receiver'] = ['Shipper Phone Number'];

            $details['fields'] = ['request_id','tracking_number','shipper_name','email','phone','channel','case_nature','case_nature_type','status','details'];
        }
        else if ($id == 143)
        {
            $details['receiver'] = ['Admin Email'];

            $details['fields'] = ['preview'];
        }
        else if ($id == 144)
        {
            $details['receiver'] = ['Rider Phone Number'];

            $details['fields'] = ['rider_name','otp'];
        }

		else if ($id == 145)
        {
            $details['receiver'] = ['Consignee Phone Number'];

            $details['fields'] = ['tracking_number','status','reason','link'];
        }
		else if ($id == 146)
        {
            $details['receiver'] = ['Admin Email'];

            $details['fields'] = ['emp_id','name','designation','link'];
        }
		else if ($id == 147)
        {
            $details['receiver'] = ['Role-Department Head (Operations)','Role-Network Manager Debriefing & Ops Excellence (Operations)'];

            $details['fields'] = ['time','preview'];
        }
		else if ($id == 148)
        {
            $details['receiver'] = ['Role-Zonal Manager (Operations)','Role-Station Manager (Operations)','Role-Supply Chain Executive (Operations)'];

            $details['fields'] = ['hub','date','link'];
        }
		else if ($id == 149)
        {
            $details['receiver'] = ['Sales Person','Department-Finance'];

            $details['fields'] = ['Shipper','person_of_contact','Shipper name'];
        }
        else if ($id == 150){
            $details['receiver'] = ['Sales Person','CC-m.sohail@trax.pk','CC-tauseef.sarfaraz@trax.pk','CC-cs.dept@trax.pk','CC-Mohsin.khan@trax.pk','CC-ops.excellence@trax.pk'];

            $details['fields'] = ['preview'];
        }
        else if($id == 152 || $id == 153){

            if($id == 152) {
                $details['receiver'] = ['Shipper Phone Number'];

            } else if ($id == 153) {
                $details['receiver'] = ['Shipper Email'];

            }
            $details['fields'] = ['shipper_name'];
        }
        else if ($id == 154){
            $details['receiver'] = ['Sales Person Email'];

            $details['fields'] = ['shipment_no','Sales_Person','shipper_name'];
        }
        else if ($id == 155){
            $details['receiver'] = ['abdul.ahad@trax.pk','saleem.abbas@trax.pk','nadeem.sarwar@trax.pk','hr.dept@trax.pk','ali.raza@trax.pk'];

            $details['fields'] = ['preview'];
        }
        else if ($id == 156)
        {
            $details['receiver'] = ['adnan.ahsan@trax.pk','fawad.ahmed@trax.pk','hammad.majid@trax.pk','m.sohail@trax.pk','ghazanfar.ali@trax.pk','CC-muhammad.waqas@trax.pk','CC-faisal.hasan@trax.pk'];

            $details['fields'] = ['month','year','link'];
        }
        else if ($id == 157)
        {
            $details['receiver'] = ['Sales Person Email'];

            $details['fields'] = ['date', 'link'];
        }
		else if ($id == 158)
        {
            $details['receiver'] = ['Rider Phone Number'];

            $details['fields'] = ['rider_name', 'otp'];
        }
        else if ($id == 160)
        {
            $details['receiver'] = ['mohsin.ali@trax.pk','waqas@trax.pk','munawar.shamsi@logiserves.com','fawad.ahmed@trax.pk','nadir.qureshi@trax.pk','m.sohail@trax.pk','BCC-(muhammad.waqas@trax.pk)'];

            $details['fields'] = ['date','preview', 'link'];
        }
        else if ($id == 162)
        {
            $details['receiver'] = ['Admin Email'];

            $details['fields'] = ['user_name','otp'];
        }
        else if ($id == 163)
        {
            $details['receiver'] = ['Consignee Phone Number'];

            $details['fields'] = ['tracking_no','status', 'reason'];
        }
        else if ($id == 165)
        {
            $details['receiver'] = ['Consignee Phone Number'];

            $details['fields'] = ['consignee','tracking_no', 'otp'];
        }
        else if ($id == 167)
        {
            $details['receiver'] = ['Shipper','CC-(Sales Person)'];

            $details['fields'] = ['status_code','link'];
        }
        else if ($id == 168)
        {
            $details['receiver'] = ['talha.hussain@trax.pk','syed.anam@trax.pk','waqas@trax.pk','ops.telenor@trax.pk'];

            $details['fields'] = ['preview','date'];
        }
        else if ($id == 166)
        {
            $details['receiver'] = ['Shipper Email'];

            $details['fields'] = ['company_name','arrival_at','tracking_number','order_id','consignee_name','consignee_city'];
        }
        else if ($id == 169)
        {
            $details['receiver'] = ['Consignee Phone Number'];

            $details['fields'] = ['consignee','tracking_number','brand_name','amount'];
        }
        else if ($id == 170)
        {
            $details['receiver'] = ['Sales Person Email'];

            $details['fields'] = ['sale_person','preview'];
        }
        else if ($id == 171)
        {
            $details['receiver'] = ['Employee Phone Number'];

            $details['fields'] = ['name'];
        }
		else if ($id == 172)
        {
            $details['receiver'] = ['Shipper Phone Number'];

            $details['fields'] = ['shipper_name','total_amount','status_link','updated_at'];
        }
        else if ($id == 173)
        {
            $details['receiver'] = ['Admin Email'];

            $details['fields'] = ['erf_id'];
        }
        else if ($id == 174)
        {
            $details['receiver'] = ['m.sohail@trax.pk/hr.dept@trax.pk'];

            $details['fields'] = ['erf_id','admin'];
        }
        else if ($id == 175)
        {
            $details['receiver'] = ['Sales Person Email'];

            $details['fields'] = ['link'];
        }
		else if ($id == 176)
        {
            $details['receiver'] = ['Shipper Phone Number/Consignee Phone Number'];

            $details['fields'] = ['name','crm_request_id','comment'];
        }
		else if ($id == 177)
        {
            $details['receiver'] = ['Sales Person/Admin'];

            $details['fields'] = ['sales_person','shipper_name','pickup_request_no','remarks'];
        }
		else if ($id == 178)
        {
            $details['receiver'] = ['Consignee Phone Number'];

            $details['fields'] = ['name'];
        }else if ($id == 181)
        {
            $details['receiver'] = ['Lead Phone Number'];

            $details['fields'] = ['name','reason'];

        }else if ($id == 182)
        {
            $details['receiver'] = ['shahzad.ali@trax.pk','Line Manager'];

            $details['fields'] = ['link','emp_id','name','designation','joining_date'];
        }
		elseif($id == 183){
            $details['receiver'] = ['Shipper Phone Number'];

            $details['fields'] = ['shipper','tracking_number'];
        }
        elseif($id == 184){
            $details['receiver'] = ['Consignee Phone Number'];

            $details['fields'] = ['consignee','tracking_number'];
        }
        elseif($id == 185){
            $details['receiver'] = ['Rider Phone Number'];

            $details['fields'] = ['rider', 'amount', 'tracking_number'];
        }
        elseif($id == 186){
            $details['receiver'] = ['faisal.hasan@trax.pk,muhammad.waqas@trax.pk'];

            $details['fields'] = ['admin', 'role'];
        }
        elseif($id == 191){
            $details['receiver'] = ['Shipper Email'];

            $details['fields'] = ['Shipper name'];
        }
        elseif($id == 192){
            $details['receiver'] = ['Consignee Phone Number'];

            $details['fields'] = ['consignee_name', 'rider_name', 'tracking_number', 'otp'];
        }
        elseif($id == 205){
            $details['receiver'] = ['Admin','talha.hussain@trax.pk'];

            $details['fields'] = ['admin', 'preview'];
        }
        else if ($id == 206)
        {
            $details['receiver'] = ['adnan.ahsan@trax.pk','fawad.ahmed@trax.pk','hammad.majid@trax.pk','m.sohail@trax.pk','ghazanfar.ali@trax.pk','CC-muhammad.waqas@trax.pk','CC-faisal.hasan@trax.pk'];

            $details['fields'] = ['month','year','link'];
        }
        else if ($id == 207)
        {
            $details['receiver'] = ['adnan.ahsan@trax.pk','fawad.ahmed@trax.pk','hammad.majid@trax.pk','m.sohail@trax.pk','ghazanfar.ali@trax.pk','CC-muhammad.waqas@trax.pk','CC-faisal.hasan@trax.pk','CC-munawar.shamsi@logiserves.com'];

            $details['fields'] = ['month','year','link'];
        }
        elseif($id == 208)
        {
            $details['receiver'] = ['Line Manager'];

            $details['fields'] = ['employee_name', 'emp_id'];
        }
        elseif($id == 209)
        {
            $details['receiver'] = ['Line Manager'];

            $details['fields'] = ['line_manager','preview','link'];
        }
        else if ($id == 210) {
            $details['receiver'] = ['ShipperNotificationEmail/Shipper'];

            $details['fields'] = ['company_name', 'arrival_at', 'pickup_city', 'consignee_name', 'consignee_city', 'order_id', 'weight', 'tracking_number', 'item_product_type', 'item_description', 'item_quantity', 'amount'];
        }
        
        elseif($id == 211)
        {
            $details['receiver'] = ['Employee Email'];

            $details['fields'] = ['Date&Day'];
        }
        elseif($id == 212)
        {
            $details['receiver'] = ['Line Manager'];

            $details['fields'] = ['employee_name'];
        }
		elseif($id == 213)
        {
            $details['receiver'] = ['Role-Department Head (Finance)','Role-Senior Officer (Finance)'];

            $details['fields'] = ['link'];
        }
        else if ($id == 214)
        {
            // $details['receiver'] = ['tanveer.malik@trax.pk','muhammad.jawwad@trax.pk','fawad.ahmed@trax.pk','waqas@trax.pk','huzaifa.aamir@trax.pk','hammad.majid@trax.pk','ghazanfar.ali@trax.pk','CC-muhammad.waqas@trax.pk','CC-faisal.hasan@trax.pk','CC-munawar.shamsi@logiserves.com'];
            $details['receiver'] = ['syed.furqan@trax.pk','fawad.ahmed@trax.pk','hammad.majid@trax.pk','BCC-(munawar.shamsi@logiserves.com,sahban.ghani@trax.pk)'];

            $details['fields'] = ['link'];
        }
        else if ($id == 216)
        {
            $details['receiver'] = ['Shipper Phone Number'];

            $details['fields'] = ['shipments_count','tracking_number','return_notes_id'];
        }
        else if ($id == 217)
        {
            $details['receiver'] = ['Rider'];

            $details['fields'] = ['rider','amount','tip','tracking_number'];
        }
        else if ($id == 218)
        {
            $details['receiver'] = ['Admin Email'];
        }
        else if($id == 221)
        {
            $details['receiver'] = ['muhammad.zain@trax.pk', 'mohsin.khan@trax.pk', 'muhammad.anas@trax.pk', 'CC-(tauseef.sarfaraz@trax.pk)'];

            $details['fields'] = ['preview'];
        }
        else if($id == 222)
        {
            $details['receiver'] = ['Shipper Email'];

            $details['fields'] = ['shipper', 'preview'];
        }
        else if($id == 223)
        {
            $details['receiver'] = ['muhammad.zain@trax.pk', 'mohsin.khan@trax.pk', 'muhammad.anas@trax.pk', 'CC-(tauseef.sarfaraz@trax.pk)'];

            $details['fields'] = ['preview'];
        }
        else if($id == 224)
        {
            $details['receiver'] = ['Role-Department Head (Operations)','Role-Zonal Manager (Operations)','Role-Manager Operation Intelligence (Operations)','Role-Network Manager Debriefing & Ops Excellence (Operations)','Role-Operation Excellence (Operations)','Role-Network Manager MMS (Operations)','Role-Debriefing Supervisor & Ops Excellence (Operations)','Role-Zonal Manager-Without Finance Access (Operations)/muhammad.ahmed@trax.pk,sahban.ghani@trax.pk'];

            $details['fields'] = ['date_time', 'link'];
        }
        else if($id == 225)
        {
            $details['receiver'] = ['Role-Department Head (Operations)','Role-Zonal Manager (Operations)','Role-Manager Operation Intelligence (Operations)','Role-Operation Excellence (Operations)','Role-Network Manager MMS (Operations)','Role-Debriefing Supervisor & Ops Excellence (Operations)','Role-Zonal Manager-Without Finance Access (Operations)/muhammad.ahmed@trax.pk,sahban.ghani@trax.pk)'];

            $details['fields'] = ['date_time', 'link'];
        }
        else if($id == 220)
        {
            $details['receiver'] = ['Shipper Email'];

            $details['fields'] = ['preview', 'link'];
        }
        else if($id == 228)
        {
            $details['receiver'] = ['Role-Senior HR Executive (HR & Internal Audit )','Role-HR Executive-Without Payslip (HR & Internal Audit)','Role-Department Head (HR & Internal Audit)'];

            $details['fields'] = ['employee_name', 'employee_type', 'depatment', 'updated_by'];
        }
        else if ($id == 232 || $id == 233)
        {
            $details['receiver'] = ['Shipper Email'];

            $details['fields'] = ['Booking_at','preview'];
        }
        else if ($id == 234)
        {
            $details['receiver'] = [''];

            $details['fields'] = [''];
        }
        else if ($id == 243)
        {
            $details['receiver'] = ['Retail Shipper'];

            $details['fields'] = ['user_name','otp','expire_at'];
        }

        else if ($id == 248)
        {
            $details['receiver'] = ['Shipper'];

            $details['fields'] = ['user_name','otp','expire_at'];
        }

        else if (/* $id == 110 || $id == 226 || */ $id == 236 || $id == 237 || $id == 238 || $id == 239 || $id == 240 || $id == 241 || $id == 242)
        {
            $details['receiver'] = ['shahbaz.abbasi@trax.pk', 'mansoor.ahmad@trax.pk'];
            $details['fields'] = ['link'];
        }
        else if ( $id == 254)
        {
            $details['receiver'] = ['sahban.ghani@logiserves.com', 'hammad.saleem@slgtrax.com', 'fawad.ahmed@slgtrax.com', 'syed.furqan@slgtrax.com'];
            $details['fields'] = ['link'];
        }
        else if($id == 244) {
            $details['receiver'] = ['Consignee Phone Number'];
            $details['fields'] = ['tracking_number', 'rider_number', 'otp'];
        }
        else if($id == 245) {
            $details['receiver'] = ['Consignee Phone Number'];
            $details['fields'] = ['tracking_number'];
        }
        else if($id == 246) {
            $details['receiver'] = ['Consignee Phone Number'];
            //$details['fields'] = ['tracking_number'];
        }
        else if($id == 247) {
            $details['receiver'] = ['Consignee Phone Number'];
            //$details['fields'] = ['tracking_number'];
        }

        return $details;
    }

    public function edit(Request $request)
    {
        $notification = Notification::find($request->get('id'));
        
        if ($notification) 
        {
            if ($notification->type_id == 1) 
            {
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
            ->select('app_notifications.id as id', 'app_notifications.name as name', 'app_notifications.app_id as app_id', 'app_notifications.updated_at as updated', 'a.name as updated_by', 'app_notifications.status as status', 'at.name as app_name');
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
            })->rawColumns(['action']);

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
            }else if($id == 17){
                $details['fields'] = ["date", "status"];
            }else if($id == 18){
                $details['fields'] = ["employee_name","trax_id","date"];
            }else if($id == 19){
                $details['fields'] = ['rider', 'amount', 'tracking_number'];
            } else if ($id == 21) {
                $details['fields'] = ['rider', 'amount', 'tip', "tracking_number"];
            } else if ($id == 22) {
                $details['fields'] = ['tracking_number'];
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

    public function sms_logs_view()
    {
        $notifications = Notification::where('status', 1)->get();
        return view('admin.sms_logs.index', compact('notifications'));
    }

    // SMS logs
    public function sms_logs(Request $request)
    {
        $search_from = $request->search_from;
        $search_to = $request->search_to;

        $logs = SMS::join('shipment_sms_logs', 'shipment_sms_logs.sms_id', '=', 'sms.id')
            ->join('notifications', 'shipment_sms_logs.notification_id', 'notifications.id')
            ->join('shipments', 'shipment_sms_logs.shipment_id', 'shipments.id')
            ->select(
                'sms.to', 
                'sms.body as body', 
                'sms.created_at as sms_created_at', 
                'shipment_sms_logs.*', 
                'notifications.name as name', 
                'shipments.tracking_number as tracking_number'
            );

            if ($search_from && $search_to) {
                $search_from = Carbon::createFromFormat('d F, Y', $search_from)->format('Y-m-d');
                $search_to = Carbon::createFromFormat('d F, Y', $search_to)->format('Y-m-d');
                $logs->whereBetween('sms.created_at', [$search_from, $search_to]);
            }

        return DataTables::of($logs)
            ->editColumn('shipment_sms_logs.id', function ($row) {
                return $row->id;
            })
            ->editColumn('name', function ($row) {
                return $row->name;
            })
            ->editColumn('sms_created_at', function ($row) {
                return $row->created_at ? $row->created_at->format('Y-m-d H:i:s') : '';
            })
            ->editColumn('to', function ($row) {
                return $row->to;
            })
            ->editColumn('body', function ($row) {
                return $row->body;
            })
            ->editColumn('tracking_number', function ($row) {
                return $row->tracking_number;
            })
            ->filterColumn('tracking_number', function ($query, $keyword) {
                $query->where('shipments.tracking_number', 'like', "%{$keyword}%");
            })
            ->filterColumn('to', function ($query, $keyword) {
                $query->where('sms.to', 'like', "%{$keyword}%");
            })
            ->filterColumn('name', function ($query, $keyword) {
                $query->where('notifications.id', 'like', "%{$keyword}%");
            })
            ->filterColumn('sms_created_at', function ($query, $keyword) {
                $query->where('sms.created_at', 'like', "%{$keyword}%");
            })
        ->make(true);
    }
}