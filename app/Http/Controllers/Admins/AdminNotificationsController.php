<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationsController;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Shipper\User;
use App\Http\Models\Notification;

use Yajra\Datatables\Datatables;
use Carbon\Carbon;

class AdminNotificationsController extends Controller
{
    public function __construct() {
      $this->middleware('auth:admin');

      $this->middleware('Permission');
    }

    public function index() {
      return view('admin.notifications.index');
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
        ->editColumn('updated_at', function($notifications) {
            return Carbon::parse($notifications->updated_at)->format('d/m/Y H:i A');
        })
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
        })
        ->filterColumn('status', function($query, $keyword) {
            $keyword = strtolower($keyword);

            if (strpos('enabled', $keyword) !== FALSE) {
                $query->where('notifications.status', '=', 1);
            }
            else if (strpos('disabled', $keyword) !== FALSE) {
                $query->where('notifications.status', '=', 0);
            }
            else {
                $query->whereRaw('false');
            }
        });

        return $datatables->make(true);
    }

    public function send_custom_email(Request $request) {
        if ($request->get('receiver') == 1) {
            $emails = Admin::all()->pluck('email')->toArray();
        }
        else {
            $emails = User::where('status', '=', 3)->where('blacklist', '=', 0)->get()->pluck('email')->toArray();
        }

        if (!empty($emails)) {
            $subject = $request->get('subject');
            $body = $request->get('body');

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
            $details['fields'] = ['company_name', 'arrival_at', 'pickup_city', 'consignee_name', 'consignee_city', 'order_id', 'weight', 'tracking_number'];
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
            $details['fields'] = ['delivery_note_number', 'rider', 'company_name', 'departure_at', 'consignee_name', 'consignee_address', 'order_id', 'amount', 'payment_mode', 'tracking_number'];
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
             $details['fields'] = ['company_name', 'city', 'bank', 'bank_branch', 'account_number', 'account_title', 'iban', 'account_city', 'payment_mode', 'payment_cycle', 'payment_done_id', 'payment_done_at', 'total_shipments', 'delivered_shipments', 'returned_shipments', 'adjusted_shipments', 'total_amount', 'total_charges', 'total_gst', 'total_payable', 'consignee_name', 'consignee_city', 'order_id', 'estimated_weight', 'actual_weight', 'tracking_number', 'amount', 'charges', 'gst', 'payable'];
        }
        else if ($id == 21) {
             $details['fields'] = ['company_name', 'service_type', 'pickup_address', 'pickup_city', 'consignee_name', 'consignee_address', 'consignee_city', 'order_id', 'amount', 'payment_mode', 'tracking_number'];
        }
        else if ($id == 22) {
             $details['fields'] = ['company_name', 'person_of_contact', 'phone_number', 'address', 'city'];
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

            $notification->save();

            return ['status' => 0, 'success' => 'Notification has been edited'];
        }
        else {
            return ['status' => 1, 'error' => 'No Notication with given ID is present'];
        }
    }
}