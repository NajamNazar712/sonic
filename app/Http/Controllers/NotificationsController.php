<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use App\Http\Models\SMS;
use App\Jobs\ProcessSMS;
use App\Http\Models\City;
use App\Http\Models\Zone;
use App\Http\Models\Rider;
use App\Http\Models\Runner;
use App\Jobs\ProcessOTPSMS;
use App\Mail\Notifications;
use App\Http\Models\Dispute;
use App\Http\Models\Invoice;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use App\Http\Models\Shipment;
use App\Jobs\ProcessOTPSMSITS;
use App\Http\Models\PickupNote;
use App\Http\Models\Admin\Admin;
use App\Http\Models\DonePayment;
use App\Http\Models\HR\Employee;
use App\Http\Models\SaleTierTag;
use App\Http\Models\ShipmentOtp;
use App\Http\Models\Notification;
use App\Http\Models\RunnerDetail;
use App\Http\Models\ShipmentItem;
use App\Http\Models\Shipper\User;
use App\Jobs\GenerateDeliveryOTP;
use App\Http\Models\ConsigneeUser;
use App\Http\Models\PickupRequest;
use App\Http\Models\RiderDelivery;
use App\PayFastTransactionDetials;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Admin\AdminHub;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\HR\LeaveStatus;
use App\Http\Models\ShipmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\AdminRole;
use App\Http\Models\Admin\CrmSmsLog;
use App\Http\Models\Admin\Lead\Lead;
use App\Http\Models\AppNotification;
use App\Http\Models\CRM\CrmComments;
use App\Http\Models\DailyFakeStatus;
use App\Jobs\ProcessOTPSMSForBotSMS;
use App\ReturnDeliveredToShipperSms;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Models\Admin\ReturnNote;
use App\Http\Models\CargoConsignment;
use App\Http\Models\HR\EmployeeLeave;
use App\Http\Models\ShipmentsJourney;
use App\Jobs\ProcessPushNotification;
use App\Http\Models\RetailDonePayment;
use App\Http\Models\ReturnNoteRequest;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\CRFTermsConditions;
use App\Http\Models\DeliveryNoteOtpSms;
use App\Http\Models\FnfSectionEmployee;
use App\Jobs\ProcessDeliveryNoteOtpSms;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Http\Models\Admin\SalePersonTag;
use App\Http\Models\EmployeeDeviceToken;
use App\Http\Models\EmployeeRequisition;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\CRM\CrmRequestStatus;
use App\Http\Models\ShipmentStatusReason;
use App\Http\Models\Shipper\UserBankInfo;
use App\Http\Models\Admin\AdminDepartment;
use App\Http\Models\CRM\CrmRequestTagging;
use App\Http\Models\ShipmentPiecesRequest;
use App\Http\Models\V2Pickup\V2PickupNote;
//use App\ReturnDeliveredToShipperSms;
use App\Jobs\ProcessDeliveryNoteOtpSmsITS;
use GuzzleHttp\Exception\RequestException;
use App\Http\Models\Admin\ActivityTrailLog;
use App\Http\Models\Admin\AdminUserRequest;
use App\Http\Models\V2Pickup\V2RiderPickup;
use App\Http\Models\Admin\Retail\RetailUser;
use App\Http\Models\Excel_reports\KaeNumber;
use App\Http\Models\ShipmentsPaymentJourney;
use App\Http\Models\Admin\ReturnNoteShipment;
use App\Http\Models\Excel_reports\Debriefing;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\ShipperNotificationEmail;
use App\Http\Models\V2Pickup\V2PickupRequest;
use App\Http\Models\Admin\CompletedAgingReport;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\Commission\SalesCommission;
use App\Http\Models\Excel_reports\HubWiseSplit;
use App\Http\Models\Excel_reports\MonthAverage;
use App\Http\Models\Admin\FintechPaymentDetails;
use App\Http\Models\EmployeeNotificationHistory;
use App\Http\Models\OvernightOverlandReportData;
use App\Http\Models\CRM\CrmRequestCaseNatureType;
use App\Http\Models\Admin\MasterCargo\MasterCargo;
use App\Http\Models\Admin\Retail\RetailShipperInfo;
use App\Http\Models\Commission\SalesCommissionUser;
use App\Http\Models\Excel_reports\QaReportPettyCash;
use App\Http\Models\Excel_reports\SalePersonNumbers;
use App\Http\Models\HR\EmployeeAttendanceAdjustment;
use App\Http\Models\V2Pickup\V2PickupRequestAttempt;
use App\Http\Models\Excel_reports\DonePaymentsReport;
use App\Http\Models\V2Pickup\V2PickupRequestShipment;
use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Controllers\Admins\AdminReportsController;
use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Models\Admin\Attendance\EmployeeAttendance;
use App\Http\Controllers\Admins\GlobalSettingsController;
use App\Http\Models\Excel_reports\MonthAverageDestination;
use App\Http\Models\V2Pickup\V2PickupRequestNotPickReason;
use App\Http\Models\Admin\PendingCashCollectionAgingReport;
use App\Http\Models\Excel_reports\RetailDonePaymentsReport;
use App\Http\Controllers\Admins\AdminReportsEmailController;
use App\Http\Models\Survey\DisableAccountIntimationSendSurvey;
use App\Http\Models\Admin\OneLink\OneLinkOutForDeliveryShipmentPayment;

use Illuminate\Support\Facades\Log;

class NotificationsController extends Controller
{
    static private function sms($body, $to, $otp = NULL)
    {
        $sms = new SMS();

        $sms->to = str_replace('-', '', $to);
        $sms->body = $body;

        if ($otp == 1) {
            $sms->otp = 1;
        }

        $sms->save();

        if ($otp == 1) {
            dispatch(new ProcessOTPSMS($sms));
        } else {
            dispatch(new ProcessSMS($sms));
        }
    }

    static private function push_notification($employee_id, $employee_type, $title, $body, $screen = NULL)
    {
        $notification_history = new EmployeeNotificationHistory();

        $notification_history->employee_id = $employee_id;
        $notification_history->employee_type_id = $employee_type;
        $notification_history->title = $title;
        $notification_history->message = $body;
        $notification_history->screen_id = $screen;
        $notification_history->save();
        dispatch(new ProcessPushNotification($notification_history));
    }

    static private function sms_otp($body, $to, $name, $otp, $type)
    {
        if ($type == 1) {
            $sms = new SMS();

            $sms->to = str_replace('-', '', $to);
            $sms->body = $body;
            $sms->otp = 1;

            $sms->save();

            dispatch(new ProcessOTPSMSITS($sms, $name, $otp));
        } else if ($type == 2) {
            $sms = new DeliveryNoteOtpSms();

            $sms->to = str_replace('-', '', $to);
            $sms->body = $body;

            $sms->save();

            dispatch(new ProcessDeliveryNoteOtpSmsITS($sms, $name, $otp));
        } else if ($type == 3) {
            $sms = new SMS();

            $sms->to = str_replace('-', '', $to);
            $sms->body = $body;
            $sms->otp = 1;

            $sms->save();
            dispatch(new GenerateDeliveryOTP($sms, $name, $otp));
        }
    }

    static private function delivery_note_otp_sms($body, $to)
    {
        $sms = new DeliveryNoteOtpSms();

        $sms->to = str_replace('-', '', $to);
        $sms->body = $body;

        $sms->save();

        dispatch(new ProcessDeliveryNoteOtpSms($sms));
    }

    static private function bot_sms($body, $to)
    {
        $sms = new SMS();

        $sms->to = str_replace('-', '', $to);
        $sms->body = $body;

        $sms->save();

        dispatch(new ProcessOTPSMSForBotSMS($sms));
    }

    static private function email($subject, $body, $to, $cc = NULL, $bcc = NULL, $from = NULL)
    {
        if ($to) {

            if (is_array($to)) {
                //                dd($to);
                $to = array_values(array_filter($to));
                if (empty($to)) {
                    return false;
                }
            }
            if ($cc != NULL) {
                if (is_array($cc)) {
                    $cc = array_values(array_filter($cc));
                    if (empty($cc)) {
                        $cc = NULL;
                    }
                }
            }
            if ($bcc != NULL) {
                if (is_array($bcc)) {
                    $bcc = array_values(array_filter($bcc));
                    if (empty($bcc)) {
                        $bcc = NULL;
                    }
                }
            }


            $mail = Mail::to($to);

            if ($cc) {
                $mail->cc($cc);
            }

            if ($bcc) {
                $mail->bcc($bcc);
            }

            $mail->send(new Notifications($subject, $body, $from));
        }
    }

    static public function send($id, $reference_1_id, $reference_2_id = NULL, $reference_3_id = NULL)
    {

        $notification = Notification::find($id);
        if ($notification) {

            if ($notification->status) {

                if ($notification->type_id == 1) {
                    $subject = $notification->subject;
                }

                $body = $notification->body;
                $head = $notification->head;


                if ($id == 1) {
                    $fields = ['account_id' => 'id', 'company_name' => 'name', 'email' => 'email', 'person_of_contact' => 'poc', 'phone_no_1' => 'phone', 'phone_no_2' => 'phone2', 'address' => 'address', 'cnic' => 'cnic', 'ntn_no' => 'ntn_no', 'api_token' => 'api_token'];

                    $shipper = User::find($reference_1_id);
                    $sales_person = SalePersonTag::where('user_id', $reference_1_id)->where('status', 0)->first();
                    $cc = array();
                    //                    $bcc = array();
                    $sale_person_email = Admin::find($sales_person->admin_id)->email;
                    if ($sale_person_email) {
                        $cc[] = $sale_person_email;
                    }


                    /*$admins = Admin::join('admin_roles', 'admins.role_id', '=', 'admin_roles.id')->join('admin_hubs', 'admin_hubs.admin_id', '=', 'admins.id')->where('admin_roles.department_id', 6)->where('admin_hubs.hub_id', '=', $shipper->city_id)->where('status', 1)->where('role_id', '!=', 55);

                    if ($admins->exists()) {
                        $bcc = $admins->pluck('admins.email')->toArray();
                    }*/

                    if (ShipperNotificationEmail::where('user_id', $shipper->id)->exists()) {
                        $to = ShipperNotificationEmail::where('user_id', $shipper->id)->whereNotNull('email')->pluck('email')->toArray();
                    } else {
                        $to = $shipper->email;
                    }
                    /*if (empty($bcc)) {
                        $bcc = NULL;
                    }*/
                    foreach ($fields as $key => $field) {
                        if (strpos($subject, '[' . $key . ']') !== FALSE) {
                            if ($key == 'account_id') {
                                $subject = str_replace('[' . $key . ']', str_pad($shipper[$field], 6, '0', STR_PAD_LEFT), $subject);
                            } else {
                                $subject = str_replace('[' . $key . ']', $shipper[$field], $subject);
                            }
                        }

                        if (strpos($body, '[' . $key . ']') !== FALSE) {
                            if ($key == 'account_id') {
                                $body = str_replace('[' . $key . ']', str_pad($shipper[$field], 6, '0', STR_PAD_LEFT), $body);
                            } else {
                                $body = str_replace('[' . $key . ']', $shipper[$field], $body);
                            }
                        }
                    }

                    if (strpos($subject, '[city]') !== FALSE) {
                        $subject = str_replace('[city]', $shipper->city->name, $subject);
                    }

                    if (strpos($body, '[city]') !== FALSE) {
                        $body = str_replace('[city]', $shipper->city->name, $body);
                    }
                    if ($to) {
                        self::email($subject, $body, $to, $cc);
                    }
                } else if ($id == 2) {
                    $fields = ['order_id' => 'order_id', 'pickup_date' => 'pickup_date', 'amount' => 'amount', 'tracking_number' => 'tracking_number'];

                    $shipment = Shipment::find($reference_1_id);

                    $shipper = $shipment->user;

                    $to = $shipper->phone;

                    foreach ($fields as $key => $field) {
                        if (strpos($body, '[' . $key . ']') !== FALSE) {
                            $body = str_replace('[' . $key . ']', $shipment[$field], $body);
                        }
                    }

                    if (strpos($body, '[account_id]') !== FALSE) {
                        $body = str_replace('[account_id]', str_pad($shipper->id, 6, '0', STR_PAD_LEFT), $body);
                    }

                    if (strpos($body, '[company_name]') !== FALSE) {
                        $body = str_replace('[company_name]', $shipper->name, $body);
                    }

                    if (strpos($body, '[service_type]') !== FALSE) {
                        $body = str_replace('[service_type]', $shipment->booking_type->booking_type, $body);
                    }

                    if (strpos($body, '[pickup_address]') !== FALSE) {
                        $body = str_replace('[pickup_address]', $shipment->pickup_address->address, $body);
                    }

                    if (strpos($body, '[pickup_city]') !== FALSE) {
                        $body = str_replace('[pickup_city]', $shipment->pickup_address->city->name, $body);
                    }

                    if (strpos($body, '[shipping_mode]') !== FALSE) {
                        $body = str_replace('[shipping_mode]', $shipment->shipping_mode->mode, $body);
                    }

                    if (strpos($body, '[payment_mode]') !== FALSE) {
                        $body = str_replace('[payment_mode]', $shipment->payment_mode->mode, $body);
                    }

                    self::sms($body, $to);
                } else if ($id == 3) {
                    $fields = ['consignee_name' => 'consignee_name', 'consignee_address' => 'consignee_address', 'order_id' => 'order_id', 'amount' => 'amount', 'tracking_number' => 'tracking_number'];
                    $shipment = Shipment::find($reference_1_id);

                    $shipper = $shipment->user;

                    $to = $shipment->consignee_phone_number_1;


                    foreach ($fields as $key => $field) {
                        if (strpos($body, '[' . $key . ']') !== FALSE) {
                            $body = str_replace('[' . $key . ']', $shipment[$field], $body);
                        }
                    }

                    if ($shipment->pickup_address->pickup_brand_name != NULL) {
                        $brand_name = $shipment->pickup_address->pickup_brand_name;
                    } else {
                        if ($shipper->brand_name != NULL) {
                            $brand_name = $shipper->brand_name;
                        } else {
                            $brand_name = $shipper->name;
                        }
                    }
                    if (strpos($body, '[company_name]') !== FALSE) {
                        $body = str_replace('[company_name]', $brand_name, $body);
                    }

                    if (strpos($body, '[service_type]') !== FALSE) {
                        $body = str_replace('[service_type]', $shipment->booking_type->booking_type, $body);
                    }

                    if (strpos($body, '[service_type]') !== FALSE) {
                        $body = str_replace('[service_type]', $shipment->booking_type->booking_type, $body);
                    }

                    if (strpos($body, '[pickup_address]') !== FALSE) {
                        $body = str_replace('[pickup_address]', $shipment->pickup_address->address, $body);
                    }

                    if (strpos($body, '[pickup_city]') !== FALSE) {
                        $body = str_replace('[pickup_city]', $shipment->pickup_address->city->name, $body);
                    }

                    if (strpos($body, '[consignee_city]') !== FALSE) {
                        $body = str_replace('[consignee_city]', $shipment->consignee_city->name, $body);
                    }

                    if (strpos($body, '[shipping_mode]') !== FALSE) {
                        $body = str_replace('[shipping_mode]', $shipment->shipping_mode->mode, $body);
                    }

                    if (strpos($body, '[payment_mode]') !== FALSE) {
                        $body = str_replace('[payment_mode]', $shipment->payment_mode->mode, $body);
                    }


                    self::sms($body, $to);
                } else if ($id == 4) {
                    $possible_fields = ['pickup_city', 'consignee_name', 'consignee_city', 'order_id', 'weight', 'tracking_number', 'item_product_type', 'item_description', 'item_quantity', 'amount'];

                    $field_names = ['pickup_city' => 'Pickup City', 'consignee_name' => 'Consignee Name', 'consignee_city' => 'Consignee City', 'order_id' => 'Order ID', 'weight' => 'Weight', 'tracking_number' => 'Tracking Number', 'item_product_type' => 'Item Product Type', 'item_description' => 'Item Description', 'item_quantity' => 'Item Quantity', 'amount' => 'Amount'];

                    $present_fields = array();

                    $first_field = NULL;

                    $position = NULL;

                    foreach ($possible_fields as $field) {
                        $new_position = strpos($body, '[' . $field . ']');

                        if ($new_position !== FALSE) {
                            if ($position == NULL) {
                                $present_fields[] = $field;

                                $first_field = $field;
                            } else if ($new_position > $position) {
                                $present_fields[] = $field;
                            } else {
                                array_unshift($present_fields, $field);
                            }

                            $position = $new_position;
                        }
                    }

                    $today = Carbon::now()->toDateTimeString();

                    $user_wise_shipments = array();

                    $origin_hub_ids = array();

                    foreach ($reference_1_id as $shipment_id) {
                        $shipment = Shipment::find($shipment_id);

                        $origin_hub_id = $shipment->pickup_address->city->hub_id;

                        if (!in_array($origin_hub_id, $origin_hub_ids)) {
                            $origin_hub_ids[] = $origin_hub_id;
                        }

                        $details = array();

                        $details['pickup_city'] = $shipment->pickup_address->city->name;
                        $details['consignee_name'] = $shipment->consignee_name;
                        $details['consignee_city'] = $shipment->consignee_city->name;
                        $details['order_id'] = $shipment->order_id;
                        $details['weight'] = $shipment->actual_weight;
                        $details['tracking_number'] = $shipment->tracking_number;
                        $details['amount'] = $shipment->amount;
                        $details['return_notes_id'] = $shipment->return_notes_id;

                        if ($shipment->booking_type_id == 1 || $shipment->booking_type_id == 2) {
                            foreach ($shipment->items as $item) {
                                if ($item->type == 0) {
                                    $details['item_product_type'] = $item->product->product_name;
                                    $details['item_description'] = $item->description;
                                    $details['item_quantity'] = $item->quantity;
                                }
                            }
                        } else {
                            $details['item_product_type'] = '';
                            $details['item_description'] = '';
                            $details['item_quantity'] = '';
                        }

                        $user_wise_shipments[$shipment->user_id][] = $details;
                    }

                    $original_subject = $subject;
                    $original_body = $body;

                    foreach ($user_wise_shipments as $user_id => $shipments) {
                        $shipper = User::find($user_id);

                        if (strpos($subject, '[company_name]') !== FALSE) {
                            $subject = str_replace('[company_name]', $shipper->name, $subject);
                        }

                        if (strpos($body, '[company_name]') !== FALSE) {
                            $body = str_replace('[company_name]', $shipper->name, $body);
                        }

                        if (strpos($subject, '[arrival_at]') !== FALSE) {
                            $subject = str_replace('[arrival_at]', $today, $subject);
                        }

                        if (strpos($body, '[arrival_at]') !== FALSE) {
                            $body = str_replace('[arrival_at]', $today, $body);
                        }

                        //              $to = $shipper->email;

                        if (ShipperNotificationEmail::where('user_id', $shipper->id)->exists()) {
                            $to = ShipperNotificationEmail::where('user_id', $shipper->id)->whereNotNull('email')->pluck('email')->toArray();
                        } else {
                            $to = $shipper->email;
                        }
                        $shipment_details = '<table style=\"padding:5px; border: 1px solid black; border-collapse: collapse;"><tbody><tr>';

                        $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">S. No.</td>';

                        foreach ($present_fields as $field) {
                            $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">' . $field_names[$field] . '</td>';
                        }

                        $shipment_details .= '</tr>';

                        $serial_number = 1;

                        foreach ($shipments as $shipment) {
                            $shipment_details .= '<tr>';

                            $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial_number . '</td>';

                            foreach ($present_fields as $field) {
                                if (!empty($shipment[$field])) {
                                    $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment[$field] . '</td>';
                                } else {
                                    $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"></td>';
                                }
                            }

                            $shipment_details .= '</tr>';

                            $serial_number++;
                        }

                        $shipment_details .= '</tbody></table>';

                        foreach ($present_fields as $field) {
                            if ($field != $first_field) {
                                $body = str_replace('[' . $field . ']', '', $body);
                            }
                        }

                        $body = str_replace('[' . $first_field . ']', $shipment_details, $body);

                        $bcc = array();

                        //              $general_admins = Admin::whereIn('role_id', [6])->where('status', 1);
                        //
                        //              if ($general_admins->exists()) {
                        //                $bcc = array_merge($bcc, $general_admins->pluck('email')->toArray());
                        //              }

                        $related_admins = Admin::whereIn('role_id', [10])->where('status', 1)->where('id', '!=', 276)->whereNotNull('email')->whereHas('hubs', function ($query) use ($origin_hub_ids) {
                            $query->whereIn('hub_id', $origin_hub_ids);
                        });

                        if ($related_admins->exists()) {
                            $bcc = array_merge($bcc, $related_admins->pluck('email')->toArray());
                        }

                        if (empty($bcc)) {
                            $bcc = NULL;
                        }

                        self::email($subject, $body, $to, NULL, $bcc);

                        $subject = $original_subject;
                        $body = $original_body;
                    }
                } else if ($id == 5) {
                    $cargo_fields = ['cargo_number' => 'id', 'departure_at' => 'created_at'];

                    $shipment_fields = ['order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

                    $cargo_consignment = CargoConsignment::find($reference_1_id);

                    $shipment = Shipment::find($reference_2_id);

                    $shipper = $shipment->user;

                    //            $to = $shipper->email;
                    if (ShipperNotificationEmail::where('user_id', $shipper->id)->exists()) {
                        $to = ShipperNotificationEmail::where('user_id', $shipper->id)->whereNotNull('email')->pluck('email')->toArray();
                    } else {
                        $to = $shipper->email;
                    }
                    foreach ($cargo_fields as $key => $field) {
                        if (strpos($subject, '[' . $key . ']') !== FALSE) {
                            if ($key == 'cargo_number') {
                                $subject = str_replace('[' . $key . ']', str_pad($cargo_consignment[$field], 6, '0', STR_PAD_LEFT), $subject);
                            } else {
                                $subject = str_replace('[' . $key . ']', $cargo_consignment[$field], $subject);
                            }
                        }

                        if (strpos($body, '[' . $key . ']') !== FALSE) {
                            if ($key == 'cargo_number') {
                                $body = str_replace('[' . $key . ']', str_pad($cargo_consignment[$field], 6, '0', STR_PAD_LEFT), $body);
                            } else {
                                $body = str_replace('[' . $key . ']', $cargo_consignment[$field], $body);
                            }
                        }
                    }

                    foreach ($shipment_fields as $key => $field) {
                        if (strpos($subject, '[' . $key . ']') !== FALSE) {
                            $subject = str_replace('[' . $key . ']', $shipment[$field], $subject);
                        }

                        if (strpos($body, '[' . $key . ']') !== FALSE) {
                            $body = str_replace('[' . $key . ']', $shipment[$field], $body);
                        }
                    }

                    if (strpos($body, '[company_name]') !== FALSE) {
                        $body = str_replace('[company_name]', $shipper->name, $body);
                    }

                    self::email($subject, $body, $to);
                } else if ($id == 6) {
                    $cargo_fields = ['cargo_number' => 'id', 'departure_at' => 'created_at'];

                    $shipment_fields = ['order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

                    $cargo_consignment = CargoConsignment::find($reference_1_id);

                    $shipment = Shipment::find($reference_2_id);

                    $shipper = $shipment->user;

                    $to = $shipper->phone;

                    foreach ($cargo_fields as $key => $field) {
                        if ($key == 'cargo_number') {
                            $body = str_replace('[' . $key . ']', str_pad($cargo_consignment[$field], 6, '0', STR_PAD_LEFT), $body);
                        } else {
                            $body = str_replace('[' . $key . ']', $cargo_consignment[$field], $body);
                        }
                    }

                    foreach ($shipment_fields as $key => $field) {
                        if (strpos($body, '[' . $key . ']') !== FALSE) {
                            $body = str_replace('[' . $key . ']', $shipment[$field], $body);
                        }
                    }

                    if (strpos($body, '[company_name]') !== FALSE) {
                        $body = str_replace('[company_name]', $shipper->name, $body);
                    }

                    self::sms($body, $to);
                } else if ($id == 7) {
                    $cargo_fields = ['cargo_number' => 'id', 'arrival_at' => 'updated_at'];

                    $shipment_fields = ['order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

                    $cargo_consignment = CargoConsignment::find($reference_1_id);

                    $shipment = Shipment::find($reference_2_id);

                    $shipper = $shipment->user;

                    //            $to = $shipper->email;
                    if (ShipperNotificationEmail::where('user_id', $shipper->id)->exists()) {
                        $to = ShipperNotificationEmail::where('user_id', $shipper->id)->whereNotNull('email')->pluck('email')->toArray();
                    } else {
                        $to = $shipper->email;
                    }
                    foreach ($cargo_fields as $key => $field) {
                        if (strpos($subject, '[' . $key . ']') !== FALSE) {
                            if ($key == 'cargo_number') {
                                $subject = str_replace('[' . $key . ']', str_pad($cargo_consignment[$field], 6, '0', STR_PAD_LEFT), $subject);
                            } else {
                                $subject = str_replace('[' . $key . ']', $cargo_consignment[$field], $subject);
                            }
                        }

                        if (strpos($body, '[' . $key . ']') !== FALSE) {
                            if ($key == 'cargo_number') {
                                $body = str_replace('[' . $key . ']', str_pad($cargo_consignment[$field], 6, '0', STR_PAD_LEFT), $body);
                            } else {
                                $body = str_replace('[' . $key . ']', $cargo_consignment[$field], $body);
                            }
                        }
                    }

                    foreach ($shipment_fields as $key => $field) {
                        if (strpos($subject, '[' . $key . ']') !== FALSE) {
                            $subject = str_replace('[' . $key . ']', $shipment[$field], $subject);
                        }

                        if (strpos($body, '[' . $key . ']') !== FALSE) {
                            $body = str_replace('[' . $key . ']', $shipment[$field], $body);
                        }
                    }

                    if (strpos($subject, '[company_name]') !== FALSE) {
                        $subject = str_replace('[company_name]', $shipper->name, $subject);
                    }

                    if (strpos($body, '[company_name]') !== FALSE) {
                        $body = str_replace('[company_name]', $shipper->name, $body);
                    }

                    self::email($subject, $body, $to);
                } else if ($id == 8) {
                    $cargo_fields = ['cargo_number' => 'id', 'arrival_at' => 'updated_at'];

                    $shipment_fields = ['order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

                    $cargo_consignment = CargoConsignment::find($reference_1_id);

                    $shipment = Shipment::find($reference_2_id);

                    $shipper = $shipment->user;

                    $to = $shipper->phone;

                    foreach ($cargo_fields as $key => $field) {
                        if (strpos($body, '[' . $key . ']') !== FALSE) {
                            if ($key == 'cargo_number') {
                                $body = str_replace('[' . $key . ']', str_pad($cargo_consignment[$field], 6, '0', STR_PAD_LEFT), $body);
                            } else {
                                $body = str_replace('[' . $key . ']', $cargo_consignment[$field], $body);
                            }
                        }
                    }

                    foreach ($shipment_fields as $key => $field) {
                        if (strpos($body, '[' . $key . ']') !== FALSE) {
                            $body = str_replace('[' . $key . ']', $shipment[$field], $body);
                        }
                    }

                    if (strpos($body, '[company_name]') !== FALSE) {
                        $body = str_replace('[company_name]', $shipper->name, $body);
                    }

                    self::sms($body, $to);
                } else if ($id == 9) {
                    $fields = ['cargo_number' => 'id', 'departure_at' => 'created_at', 'seal_number' => 'seal_number', 'builty_number' => 'builty_number', 'expected_arrival_date', 'expected_arrival_date'];

                    $cargo_consignment = CargoConsignment::find($reference_1_id);

                    foreach ($fields as $key => $field) {
                        if (strpos($subject, '[' . $key . ']') !== FALSE) {
                            if ($key == 'cargo_number') {
                                $subject = str_replace('[' . $key . ']', str_pad($cargo_consignment[$field], 6, '0', STR_PAD_LEFT), $subject);
                            } else {
                                $subject = str_replace('[' . $key . ']', $cargo_consignment[$field], $subject);
                            }
                        }

                        if (strpos($body, '[' . $key . ']') !== FALSE) {
                            if ($key == 'cargo_number') {
                                $body = str_replace('[' . $key . ']', str_pad($cargo_consignment[$field], 6, '0', STR_PAD_LEFT), $body);
                            } else {
                                $body = str_replace('[' . $key . ']', $cargo_consignment[$field], $body);
                            }
                        }
                    }

                    if (strpos($subject, '[shipping_mode]') !== FALSE) {
                        $subject = str_replace('[shipping_mode]', $cargo_consignment->shipping_mode->mode, $subject);
                    }

                    if (strpos($body, '[shipping_mode]') !== FALSE) {
                        $body = str_replace('[shipping_mode]', $cargo_consignment->shipping_mode->mode, $body);
                    }

                    if (strpos($subject, '[transport_mode]') !== FALSE) {
                        $subject = str_replace('[transport_mode]', $cargo_consignment->transport_mode->name, $subject);
                    }

                    if (strpos($body, '[transport_mode]') !== FALSE) {
                        $body = str_replace('[transport_mode]', $cargo_consignment->transport_mode->name, $body);
                    }

                    if (strpos($subject, '[vendor]') !== FALSE) {
                        $subject = str_replace('[vendor]', $cargo_consignment->transport_mode_vendor->name, $subject);
                    }

                    if (strpos($body, '[vendor]') !== FALSE) {
                        $body = str_replace('[vendor]', $cargo_consignment->transport_mode_vendor->name, $body);
                    }

                    if (strpos($subject, '[sender]') !== FALSE) {
                        $subject = str_replace('[sender]', $cargo_consignment->sender->name, $subject);
                    }

                    if (strpos($body, '[sender]') !== FALSE) {
                        $body = str_replace('[sender]', $cargo_consignment->sender->name, $body);
                    }

                    if (strpos($body, '[tracking_number]') !== FALSE) {
                        $tracking_numbers = '';

                        foreach ($cargo_consignment->cargo_consignment_shipments as $cargo_consignment_shipment) {
                            $shipment = $cargo_consignment_shipment->shipment;

                            $tracking_numbers .= $shipment->tracking_number . PHP_EOL;
                        }

                        $body = str_replace('[tracking_number]', $tracking_numbers, $body);
                    }

                    $to = array();

                    $general_admins = Admin::whereIn('role_id', [3, 4])->where('status', 1)->whereNotNull('email');

                    if ($general_admins->exists()) {
                        $to = array_merge($to, $general_admins->pluck('email')->toArray());
                    }

                    $origin_hub_id = $cargo_consignment->origin_hub_id;
                    $destination_hub_id = $cargo_consignment->destination_hub_id;

                    $related_admins = Admin::whereIn('role_id', [8, 9, 10, 25])->where('status', 1)->whereNotNull('email')->whereHas('hubs', function ($query) use ($origin_hub_id, $destination_hub_id) {
                        $query->where('hub_id', $origin_hub_id)
                            ->orWhere('hub_id', $destination_hub_id);
                    });

                    if ($related_admins->exists()) {
                        $to = array_merge($to, $related_admins->pluck('email')->toArray());
                    }

                    if (!empty($to)) {
                        self::email($subject, $body, $to);
                    }
                } else if ($id == 10) {
                    $delivery_note_fields = ['delivery_note_number' => 'id', 'departure_at' => 'created_at'];

                    $shipment_fields = ['order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

                    $delivery_note = DeliveryNote::find($reference_1_id);

                    $shipment = Shipment::find($reference_2_id);

                    $shipper = $shipment->user;


                    if (ShipperNotificationEmail::where('user_id', $shipper->id)->exists()) {
                        $to = ShipperNotificationEmail::where('user_id', $shipper->id)->whereNotNull('email')->pluck('email')->toArray();
                    } else {
                        $to = $shipper->email;
                    }
                    foreach ($delivery_note_fields as $key => $field) {
                        if (strpos($subject, '[' . $key . ']') !== FALSE) {
                            if ($key == 'delivery_note_number') {
                                $subject = str_replace('[' . $key . ']', str_pad($delivery_note[$field], 6, '0', STR_PAD_LEFT), $subject);
                            } else {
                                $subject = str_replace('[' . $key . ']', $delivery_note[$field], $subject);
                            }
                        }

                        if (strpos($body, '[' . $key . ']') !== FALSE) {
                            if ($key == 'delivery_note_number') {
                                $body = str_replace('[' . $key . ']', str_pad($delivery_note[$field], 6, '0', STR_PAD_LEFT), $body);
                            } else {
                                $body = str_replace('[' . $key . ']', $delivery_note[$field], $body);
                            }
                        }
                    }

                    foreach ($shipment_fields as $key => $field) {
                        if (strpos($subject, '[' . $key . ']') !== FALSE) {
                            $subject = str_replace('[' . $key . ']', $shipment[$field], $subject);
                        }

                        if (strpos($body, '[' . $key . ']') !== FALSE) {
                            $body = str_replace('[' . $key . ']', $shipment[$field], $body);
                        }
                    }

                    if (strpos($subject, '[rider]') !== FALSE) {
                        $subject = str_replace('[rider]', $delivery_note->rider->name . ' (' . $delivery_note->rider->phone . ')', $subject);
                    }

                    if (strpos($body, '[rider]') !== FALSE) {
                        $body = str_replace('[rider]', $delivery_note->rider->name . ' (' . $delivery_note->rider->phone . ')', $body);
                    }

                    if (strpos($subject, '[company_name]') !== FALSE) {
                        $subject = str_replace('[company_name]', $shipper->name, $subject);
                    }

                    if (strpos($body, '[company_name]') !== FALSE) {
                        $body = str_replace('[company_name]', $shipper->name, $body);
                    }

                    self::email($subject, $body, $to);
                } else if ($id == 11) {
                    $delivery_note_fields = ['delivery_note_number' => 'id', 'departure_at' => 'created_at'];

                    $shipment_fields = ['order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

                    $delivery_note = DeliveryNote::find($reference_1_id);

                    $shipment = Shipment::find($reference_2_id);

                    $shipper = $shipment->user;

                    $to = $shipper->phone;

                    foreach ($delivery_note_fields as $key => $field) {
                        if (strpos($body, '[' . $key . ']') !== FALSE) {
                            if ($key == 'delivery_note_number') {
                                $body = str_replace('[' . $key . ']', str_pad($delivery_note[$field], 6, '0', STR_PAD_LEFT), $body);
                            } else {
                                $body = str_replace('[' . $key . ']', $delivery_note[$field], $body);
                            }
                        }
                    }

                    foreach ($shipment_fields as $key => $field) {
                        if (strpos($body, '[' . $key . ']') !== FALSE) {
                            $body = str_replace('[' . $key . ']', $shipment[$field], $body);
                        }
                    }

                    if (strpos($body, '[rider]') !== FALSE) {
                        $body = str_replace('[rider]', $delivery_note->rider->name . ' (' . $delivery_note->rider->phone . ')', $body);
                    }

                    if (strpos($body, '[company_name]') !== FALSE) {
                        $body = str_replace('[company_name]', $shipper->name, $body);
                    }

                    self::sms($body, $to);
                } else if ($id == 12) {

                    $payment_link = $reference_3_id;

                    $delivery_note_fields = ['delivery_note_number' => 'id', 'departure_at' => 'created_at'];
                    $shipment_fields = ['consignee_name' => 'consignee_name', 'consignee_address' => 'consignee_address', 'order_id' => 'order_id', 'amount' => 'amount', 'tracking_number' => 'tracking_number'];
                    $delivery_note = DeliveryNote::find($reference_1_id);
                    $delivery_note_shipment = DeliveryNoteShipment::where('delivery_note_id', $reference_1_id)->where('shipment_id', $reference_2_id)->first();
                    $shipment = Shipment::find($reference_2_id);

                    //PayFast Payment Link Send 
                    // $payfast = new PayFastTransactionDetials();
                    // $payment_link = $payfast::where('invoice_ref_id',$shipment->tracking_number)->first();  
                    //End


                    $shipment_otp = ShipmentOtp::where('shipment_id', $shipment['id']);
                    $shipper = $shipment->user;
                    $to = $shipment->consignee_phone_number_1;

                    foreach ($delivery_note_fields as $key => $field) {
                        if (strpos($body, '[' . $key . ']') !== FALSE) {
                            if ($key == 'delivery_note_number') {
                                $body = str_replace('[' . $key . ']', str_pad($delivery_note[$field], 6, '0', STR_PAD_LEFT), $body);
                            } else {
                                $body = str_replace('[' . $key . ']', $delivery_note[$field], $body);
                            }
                        }
                    }

                    foreach ($shipment_fields as $key => $field) {
                        if (strpos($body, '[' . $key . ']') !== FALSE) {
                            $body = str_replace('[' . $key . ']', $shipment[$field], $body);
                        }
                    }

                    if ($delivery_note->special_rider) {
                        if (strpos($body, '[rider]') !== FALSE) {
                            if ($delivery_note_shipment->rider_information) {
                                $body = str_replace('[rider]', str_replace('-', '', $delivery_note->special_rider_phone), $body);
                            } else {
                                $body = str_replace('[rider]', '', $body);
                            }
                        }
                    } else {
                        if (strpos($body, '[rider]') !== FALSE) {
                            if ($delivery_note_shipment->rider_information) {
                                $body = str_replace('[rider]', str_replace('-', '', $delivery_note->rider->phone), $body);
                            } else {
                                $body = str_replace('[rider]', '', $body);
                            }
                        }
                    }

                    if ($shipment->pickup_address->pickup_brand_name != NULL) {
                        $brand_name = $shipment->pickup_address->pickup_brand_name;
                    } else {
                        if ($shipper->brand_name != NULL) {
                            $brand_name = $shipper->brand_name;
                        } else {
                            $brand_name = $shipper->name;
                        }
                    }

                    if (strpos($body, '[company_name]') !== FALSE) {
                        $body = str_replace('[company_name]', substr(preg_replace('/[^A-Za-z0-9 ]/', '', $brand_name), 0, 25), $body);
                    }

                    if (strpos($body, '[online_payment_link]') !== FALSE) {
                        $body = str_replace('[online_payment_link]', $payment_link, $body);
                    }

                    if (strpos($body, '[payment_mode]') !== FALSE) {
                        $body = str_replace('[payment_mode]', $shipment->payment_mode->mode, $body);
                    }
                    if ($shipment_otp->exists()) {
                        $shipment_otp = $shipment_otp->first();
                        if (strpos($body, '[refusal_otp]') !== FALSE) {
                            $body = str_replace('[refusal_otp]', $shipment_otp->otp, $body);
                        }
                    }
                    self::sms($body, $to);
                } else if ($id == 13) {
                    $delivery_note_fields = ['delivery_note_number' => 'id', 'departure_at' => 'created_at'];

                    $shipment_fields = ['order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

                    $delivery_note = DeliveryNote::find($reference_1_id);

                    foreach ($delivery_note_fields as $key => $field) {
                        if (strpos($subject, '[' . $key . ']') !== FALSE) {
                            if ($key == 'delivery_note_number') {
                                $subject = str_replace('[' . $key . ']', str_pad($delivery_note[$field], 6, '0', STR_PAD_LEFT), $subject);
                            } else {
                                $subject = str_replace('[' . $key . ']', $delivery_note[$field], $subject);
                            }
                        }

                        if (strpos($body, '[' . $key . ']') !== FALSE) {
                            if ($key == 'delivery_note_number') {
                                $body = str_replace('[' . $key . ']', str_pad($delivery_note[$field], 6, '0', STR_PAD_LEFT), $body);
                            } else {
                                $body = str_replace('[' . $key . ']', $delivery_note[$field], $body);
                            }
                        }
                    }

                    if (strpos($subject, '[rider]') !== FALSE) {
                        $subject = str_replace('[rider]', $delivery_note->rider->name . ' (' . $delivery_note->rider->phone . ')', $subject);
                    }

                    if (strpos($body, '[rider]') !== FALSE) {
                        $body = str_replace('[rider]', $delivery_note->rider->name . ' (' . $delivery_note->rider->phone . ')', $body);
                    }

                    $original_subject = $subject;
                    $original_body = $body;

                    foreach ($delivery_note->delivery_note_shipments as $delivery_note_shipment) {
                        $shipment = $delivery_note_shipment->shipment;

                        if ($shipment->shipper_status_id != 12) {
                            $shipper = $shipment->user;

                            //                $to = $shipper->email;
                            if (ShipperNotificationEmail::where('user_id', $shipper->id)->exists()) {
                                $to = ShipperNotificationEmail::where('user_id', $shipper->id)->whereNotNull('email')->pluck('email')->toArray();
                            } else {
                                $to = $shipper->email;
                            }
                            foreach ($shipment_fields as $key => $field) {
                                if (strpos($subject, '[' . $key . ']') !== FALSE) {
                                    $subject = str_replace('[' . $key . ']', $shipment[$field], $subject);
                                }

                                if (strpos($body, '[' . $key . ']') !== FALSE) {
                                    $body = str_replace('[' . $key . ']', $shipment[$field], $body);
                                }
                            }

                            if (strpos($subject, '[company_name]') !== FALSE) {
                                $subject = str_replace('[company_name]', $shipper->name, $subject);
                            }

                            if (strpos($body, '[company_name]') !== FALSE) {
                                $body = str_replace('[company_name]', $shipper->name, $body);
                            }

                            if (strpos($subject, '[status]') !== FALSE) {
                                $subject = str_replace('[status]', $shipment->status_shipper->name, $subject);
                            }

                            if (strpos($body, '[status]') !== FALSE) {
                                $body = str_replace('[status]', $shipment->status_shipper->name, $body);
                            }

                            self::email($subject, $body, $to);

                            $subject = $original_subject;
                            $body = $original_body;
                        }
                    }
                } else if ($id == 14) {
                    $delivery_note_fields = ['delivery_note_number' => 'id', 'departure_at' => 'created_at'];

                    $shipment_fields = ['order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

                    $delivery_note = DeliveryNote::find($reference_1_id);

                    foreach ($delivery_note_fields as $key => $field) {
                        if (strpos($body, '[' . $key . ']') !== FALSE) {
                            if ($key == 'delivery_note_number') {
                                $body = str_replace('[' . $key . ']', str_pad($delivery_note[$field], 6, '0', STR_PAD_LEFT), $body);
                            } else {
                                $body = str_replace('[' . $key . ']', $delivery_note[$field], $body);
                            }
                        }
                    }

                    if (strpos($body, '[rider]') !== FALSE) {
                        $body = str_replace('[rider]', $delivery_note->rider->name . ' (' . $delivery_note->rider->phone . ')', $body);
                    }

                    $original_body = $body;

                    foreach ($delivery_note->delivery_note_shipments as $delivery_note_shipment) {
                        $shipment = $delivery_note_shipment->shipment;

                        if ($shipment->shipper_status_id != 12) {
                            $shipper = $shipment->user;

                            $to = $shipper->phone;

                            foreach ($shipment_fields as $key => $field) {
                                if (strpos($body, '[' . $key . ']') !== FALSE) {
                                    $body = str_replace('[' . $key . ']', $shipment[$field], $body);
                                }
                            }

                            if (strpos($body, '[company_name]') !== FALSE) {
                                $body = str_replace('[company_name]', $shipper->name, $body);
                            }

                            if (strpos($body, '[status]') !== FALSE) {
                                $body = str_replace('[status]', $shipment->status_shipper->name, $body);
                            }

                            self::sms($body, $to);

                            $body = $original_body;
                        }
                    }
                } else if ($id == 15) {
                    if ($reference_1_id != 0) {
                        $return_note_fields = ['return_note_number' => 'id', 'departure_at' => 'created_at'];

                        $shipment_fields = ['order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

                        $return_note = ReturnNote::find($reference_1_id);

                        foreach ($return_note_fields as $key => $field) {
                            if (strpos($subject, '[' . $key . ']') !== FALSE) {
                                if ($key == 'return_note_number') {
                                    $subject = str_replace('[' . $key . ']', str_pad($return_note[$field], 6, '0', STR_PAD_LEFT), $subject);
                                } else {
                                    $subject = str_replace('[' . $key . ']', $return_note[$field], $subject);
                                }
                            }

                            if (strpos($body, '[' . $key . ']') !== FALSE) {
                                if ($key == 'return_note_number') {
                                    $body = str_replace('[' . $key . ']', str_pad($return_note[$field], 6, '0', STR_PAD_LEFT), $body);
                                } else {
                                    $body = str_replace('[' . $key . ']', $return_note[$field], $body);
                                }
                            }
                        }

                        if (strpos($subject, '[rider]') !== FALSE) {
                            $subject = str_replace('[rider]', $return_note->rider->name . ' (' . $return_note->rider->phone . ')', $subject);
                        }

                        if (strpos($body, '[rider]') !== FALSE) {
                            $body = str_replace('[rider]', $return_note->rider->name . ' (' . $return_note->rider->phone . ')', $body);
                        }

                        $original_subject = $subject;
                        $original_body = $body;

                        foreach ($return_note->return_note_shipments as $return_note_shipment) {
                            $shipment = $return_note_shipment->shipment;

                            $shipper = $shipment->user;

                            //                $to = $shipper->email;
                            if (ShipperNotificationEmail::where('user_id', $shipper->id)->exists()) {
                                $to = ShipperNotificationEmail::where('user_id', $shipper->id)->whereNotNull('email')->pluck('email')->toArray();
                            } else {
                                $to = $shipper->email;
                            }
                            foreach ($shipment_fields as $key => $field) {
                                if (strpos($subject, '[' . $key . ']') !== FALSE) {
                                    $subject = str_replace('[' . $key . ']', $shipment[$field], $subject);
                                }

                                if (strpos($body, '[' . $key . ']') !== FALSE) {
                                    $body = str_replace('[' . $key . ']', $shipment[$field], $body);
                                }
                            }

                            if (strpos($subject, '[company_name]') !== FALSE) {
                                $subject = str_replace('[company_name]', $shipper->name, $subject);
                            }

                            if (strpos($body, '[company_name]') !== FALSE) {
                                $body = str_replace('[company_name]', $shipper->name, $body);
                            }

                            if (strpos($subject, '[status]') !== FALSE) {
                                $subject = str_replace('[status]', $shipment->status_shipper->name, $subject);
                            }

                            if (strpos($body, '[status]') !== FALSE) {
                                $body = str_replace('[status]', $shipment->status_shipper->name, $body);
                            }

                            self::email($subject, $body, $to);

                            $subject = $original_subject;
                            $body = $original_body;
                        }
                    } else {
                        $remove_fields = ['return_note_number', 'departure_at', 'rider'];

                        $fields = ['order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

                        $shipment = Shipment::find($reference_2_id);

                        $shipper = $shipment->user;

                        //              $to = $shipper->email;
                        if (ShipperNotificationEmail::where('user_id', $shipper->id)->exists()) {
                            $to = ShipperNotificationEmail::where('user_id', $shipper->id)->whereNotNull('email')->pluck('email')->toArray();
                        } else {
                            $to = $shipper->email;
                        }
                        foreach ($remove_fields as $field) {
                            if (strpos($subject, '[' . $field . ']') !== FALSE) {
                                $subject = str_replace('[' . $field . ']', '-', $subject);
                            }

                            if (strpos($body, '[' . $field . ']') !== FALSE) {
                                $body = str_replace('[' . $field . ']', '-', $body);
                            }
                        }

                        foreach ($fields as $key => $field) {
                            if (strpos($subject, '[' . $key . ']') !== FALSE) {
                                $subject = str_replace('[' . $key . ']', $shipment[$field], $subject);
                            }

                            if (strpos($body, '[' . $key . ']') !== FALSE) {
                                $body = str_replace('[' . $key . ']', $shipment[$field], $body);
                            }
                        }

                        if (strpos($subject, '[company_name]') !== FALSE) {
                            $subject = str_replace('[company_name]', $shipper->name, $subject);
                        }

                        if (strpos($body, '[company_name]') !== FALSE) {
                            $body = str_replace('[company_name]', $shipper->name, $body);
                        }

                        if (strpos($subject, '[status]') !== FALSE) {
                            $subject = str_replace('[status]', $shipment->status_shipper->name, $subject);
                        }

                        if (strpos($body, '[status]') !== FALSE) {
                            $body = str_replace('[status]', $shipment->status_shipper->name, $body);
                        }

                        self::email($subject, $body, $to);
                    }
                } else if ($id == 16) {
                    if ($reference_1_id != 0) {
                        $return_note_fields = ['return_note_number' => 'id', 'departure_at' => 'created_at'];

                        $shipment_fields = ['order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

                        $return_note = ReturnNote::find($reference_1_id);

                        foreach ($return_note_fields as $key => $field) {
                            if (strpos($body, '[' . $key . ']') !== FALSE) {
                                if ($key == 'return_note_number') {
                                    $body = str_replace('[' . $key . ']', str_pad($return_note[$field], 6, '0', STR_PAD_LEFT), $body);
                                } else {
                                    $body = str_replace('[' . $key . ']', $return_note[$field], $body);
                                }
                            }
                        }

                        if (strpos($body, '[rider]') !== FALSE) {
                            $body = str_replace('[rider]', $return_note->rider->name . ' (' . $return_note->rider->phone . ')', $body);
                        }

                        $original_body = $body;

                        foreach ($return_note->return_note_shipments as $return_note_shipment) {
                            $shipment = $return_note_shipment->shipment;

                            $shipper = $shipment->user;

                            $to = $shipper->phone;

                            foreach ($shipment_fields as $key => $field) {
                                if (strpos($body, '[' . $key . ']') !== FALSE) {
                                    $body = str_replace('[' . $key . ']', $shipment[$field], $body);
                                }
                            }

                            if (strpos($body, '[company_name]') !== FALSE) {
                                $body = str_replace('[company_name]', $shipper->name, $body);
                            }

                            if (strpos($body, '[status]') !== FALSE) {
                                $body = str_replace('[status]', $shipment->status_shipper->name, $body);
                            }

                            self::sms($body, $to);

                            $body = $original_body;
                        }
                    } else {
                        $remove_fields = ['return_note_number', 'departure_at', 'rider'];

                        $fields = ['order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

                        $shipment = Shipment::find($reference_2_id);

                        $shipper = $shipment->user;

                        $to = $shipper->phone;

                        foreach ($remove_fields as $field) {
                            if (strpos($body, '[' . $field . ']') !== FALSE) {
                                $body = str_replace('[' . $field . ']', '-', $body);
                            }
                        }

                        foreach ($fields as $key => $field) {
                            if (strpos($body, '[' . $key . ']') !== FALSE) {
                                $body = str_replace('[' . $key . ']', $shipment[$field], $body);
                            }
                        }

                        if (strpos($body, '[company_name]') !== FALSE) {
                            $body = str_replace('[company_name]', $shipper->name, $body);
                        }

                        if (strpos($body, '[status]') !== FALSE) {
                            $body = str_replace('[status]', $shipment->status_shipper->name, $body);
                        }

                        self::sms($body, $to);
                    }
                } else if ($id == 17) {
                    $fields = ['order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

                    $shipment = Shipment::find($reference_1_id);

                    $new_shipment = Shipment::find($reference_2_id);

                    $shipper = $shipment->user;

                    //            $to = $shipper->email;
                    if (ShipperNotificationEmail::where('user_id', $shipper->id)->exists()) {
                        $to = ShipperNotificationEmail::where('user_id', $shipper->id)->whereNotNull('email')->pluck('email')->toArray();
                    } else {
                        $to = $shipper->email;
                    }
                    foreach ($fields as $key => $field) {
                        if (strpos($subject, '[' . $key . ']') !== FALSE) {
                            $subject = str_replace('[' . $key . ']', $shipment[$field], $subject);
                        }

                        if (strpos($body, '[' . $key . ']') !== FALSE) {
                            $body = str_replace('[' . $key . ']', $shipment[$field], $body);
                        }
                    }

                    if (strpos($subject, '[account_id]') !== FALSE) {
                        $subject = str_replace('[account_id]', str_pad($shipper->id, 6, '0', STR_PAD_LEFT), $subject);
                    }

                    if (strpos($body, '[account_id]') !== FALSE) {
                        $body = str_replace('[account_id]', str_pad($shipper->id, 6, '0', STR_PAD_LEFT), $body);
                    }

                    if (strpos($subject, '[company_name]') !== FALSE) {
                        $subject = str_replace('[company_name]', $shipper->name, $subject);
                    }

                    if (strpos($body, '[company_name]') !== FALSE) {
                        $body = str_replace('[company_name]', $shipper->name, $body);
                    }

                    if (strpos($subject, '[service_type]') !== FALSE) {
                        $subject = str_replace('[service_type]', $shipment->booking_type->booking_type, $subject);
                    }

                    if (strpos($body, '[service_type]') !== FALSE) {
                        $body = str_replace('[service_type]', $shipment->booking_type->booking_type, $body);
                    }

                    if (strpos($subject, '[new_tracking_number]') !== FALSE) {
                        $subject = str_replace('[new_tracking_number]', $new_shipment->tracking_number, $subject);
                    }

                    if (strpos($body, '[new_tracking_number]') !== FALSE) {
                        $body = str_replace('[new_tracking_number]', $new_shipment->tracking_number, $body);
                    }

                    self::email($subject, $body, $to);
                } else if ($id == 18) {
                    $fields = ['order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

                    $shipment = Shipment::find($reference_1_id);

                    $new_shipment = Shipment::find($reference_2_id);

                    $shipper = $shipment->user;

                    $to = $shipper->phone;

                    foreach ($fields as $key => $field) {
                        if (strpos($body, '[' . $key . ']') !== FALSE) {
                            $body = str_replace('[' . $key . ']', $shipment[$field], $body);
                        }
                    }

                    if (strpos($body, '[account_id]') !== FALSE) {
                        $body = str_replace('[account_id]', $shipper->id, $body);
                    }

                    if (strpos($body, '[company_name]') !== FALSE) {
                        $body = str_replace('[company_name]', $shipper->name, $body);
                    }

                    if (strpos($body, '[service_type]') !== FALSE) {
                        $body = str_replace('[service_type]', $shipment->booking_type->booking_type, $body);
                    }

                    if (strpos($body, '[new_tracking_number]') !== FALSE) {
                        $body = str_replace('[new_tracking_number]', $new_shipment->tracking_number, $body);
                    }

                    self::sms($body, $to);
                } else if ($id == 19) {
                    $fields = ['dispute_number' => 'id', 'dispute_description' => 'description'];

                    $dispute = Dispute::find($reference_1_id);

                    if ($dispute->raised_by_status == 0) {
                        $launched_by = $dispute->admins;
                    } else {
                        $launched_by = $dispute->shipper;
                    }

                    foreach ($fields as $key => $field) {
                        if (strpos($subject, '[' . $key . ']') !== FALSE) {
                            if ($key == 'dispute_number') {
                                $subject = str_replace('[' . $key . ']', str_pad($dispute[$field], 6, '0', STR_PAD_LEFT), $subject);
                            } else {
                                $subject = str_replace('[' . $key . ']', $dispute[$field], $subject);
                            }
                        }

                        if (strpos($body, '[' . $key . ']') !== FALSE) {
                            if ($key == 'dispute_number') {
                                $body = str_replace('[' . $key . ']', str_pad($dispute[$field], 6, '0', STR_PAD_LEFT), $body);
                            } else {
                                $body = str_replace('[' . $key . ']', $dispute[$field], $body);
                            }
                        }
                    }

                    if (strpos($subject, '[dispute_type]') !== FALSE) {
                        $subject = str_replace('[dispute_type]', $dispute->dispute_types->type, $subject);
                    }

                    if (strpos($body, '[dispute_type]') !== FALSE) {
                        $body = str_replace('[dispute_type]', $dispute->dispute_types->type, $body);
                    }

                    if (strpos($subject, '[launched_by]') !== FALSE) {
                        $subject = str_replace('[launched_by]', $launched_by->name, $subject);
                    }

                    if (strpos($body, '[launched_by]') !== FALSE) {
                        $body = str_replace('[launched_by]', $launched_by->name, $body);
                    }

                    if (strpos($subject, '[city]') !== FALSE) {
                        $subject = str_replace('[city]', $dispute->city->name, $subject);
                    }

                    if (strpos($body, '[city]') !== FALSE) {
                        $body = str_replace('[city]', $dispute->city->name, $body);
                    }

                    if (strpos($subject, '[tracking_number]') !== FALSE) {
                        $tracking_numbers = '';

                        foreach ($dispute->dispute_shipments as $dispute_shipment) {
                            $shipment = $dispute_shipment->shipment;

                            $tracking_numbers .= $shipment->tracking_number . ', ';
                        }

                        $tracking_numbers .= substr($tracking_numbers, 0, -2) . PHP_EOL;

                        $subject = str_replace('[tracking_number]', $tracking_numbers, $subject);
                    }

                    if (strpos($body, '[tracking_number]') !== FALSE) {
                        $tracking_numbers = '';

                        foreach ($dispute->dispute_shipments as $dispute_shipment) {
                            $shipment = $dispute_shipment->shipment;

                            $tracking_numbers .= $shipment->tracking_number . ', ';
                        }

                        $tracking_numbers .= substr($tracking_numbers, 0, -2) . PHP_EOL;

                        $body = str_replace('[tracking_number]', $tracking_numbers, $body);
                    }

                    $to = [$launched_by->email];

                    $general_admins = Admin::whereIn('role_id', [4, 3, 2, 5, 25])->where('status', 1)->whereNotNull('email');

                    if ($general_admins->exists()) {
                        $to = array_merge($to, $general_admins->pluck('email')->toArray());
                    }

                    $hub_id = $dispute->city->hub_id;

                    $related_admins = Admin::whereIn('role_id', [8, 11])->where('status', 1)->whereNotNull('email')->whereHas('hubs', function ($query) use ($hub_id) {
                        $query->where('hub_id', $hub_id);
                    });

                    if ($related_admins->exists()) {
                        $to = array_merge($to, $related_admins->pluck('email')->toArray());
                    }

                    self::email($subject, $body, $to);
                } else if ($id == 20) {
                    $possible_fields = ['consignee_name', 'consignee_city', 'order_id', 'estimated_weight', 'actual_weight', 'chargeable_weight', 'tracking_number', 'amount', 'weight_charges', 'cash_handling_charges', 'charges', 'gst', 'payable'];

                    $field_names = ['consignee_name' => 'Consignee Name', 'consignee_city' => 'Consignee City', 'order_id' => 'Order ID', 'estimated_weight' => 'Estimated Weight', 'actual_weight' => 'Actual Weight', 'chargeable_weight' => 'Chargeable Weight', 'tracking_number' => 'Tracking Number', 'amount' => 'Collection Amount (PKR)', 'weight_charges' => 'Weight Charges (PKR)', 'cash_handling_charges' => 'Cash Handling Charges (PKR)', 'charges' => 'Total Charges (PKR)', 'gst' => 'GST (PKR)', 'payable' => 'Payable (PKR)'];

                    $present_fields = array();

                    $first_field = NULL;

                    $position = NULL;

                    foreach ($possible_fields as $field) {
                        $new_position = strpos($body, '[' . $field . ']');

                        if ($new_position !== FALSE) {
                            if ($position == NULL) {
                                $present_fields[] = $field;

                                $first_field = $field;
                            } else if ($new_position > $position) {
                                $present_fields[] = $field;
                            } else {
                                array_unshift($present_fields, $field);
                            }

                            $position = $new_position;
                        }
                    }

                    $done_payment = DonePayment::find($reference_1_id);

                    $shipper = User::find($done_payment->user_id);

                    if (strpos($subject, '[company_name]') !== FALSE) {
                        $subject = str_replace('[company_name]', $shipper->name, $subject);
                    }

                    if (strpos($body, '[company_name]') !== FALSE) {
                        $body = str_replace('[company_name]', $shipper->name, $body);
                    }

                    if (strpos($subject, '[city]') !== FALSE) {
                        $subject = str_replace('[city]', $shipper->city->name, $subject);
                    }

                    if (strpos($body, '[city]') !== FALSE) {
                        $body = str_replace('[city]', $shipper->city->name, $body);
                    }

                    if (strpos($subject, '[bank]') !== FALSE) {
                        $subject = str_replace('[bank]', $shipper->bank->bank_name, $subject);
                    }

                    if (strpos($body, '[bank]') !== FALSE) {
                        $body = str_replace('[bank]', $shipper->bank->bank_name, $body);
                    }

                    if (strpos($subject, '[bank_branch]') !== FALSE) {
                        $subject = str_replace('[bank_branch]', $shipper->bank->bank_branch, $subject);
                    }

                    if (strpos($body, '[bank_branch]') !== FALSE) {
                        $body = str_replace('[bank_branch]', $shipper->bank->bank_branch, $body);
                    }

                    if (strpos($subject, '[account_number]') !== FALSE) {
                        $subject = str_replace('[account_number]', $shipper->bank->account_no, $subject);
                    }

                    if (strpos($body, '[account_number]') !== FALSE) {
                        $body = str_replace('[account_number]', $shipper->bank->account_no, $body);
                    }

                    if (strpos($subject, '[account_title]') !== FALSE) {
                        $subject = str_replace('[account_title]', $shipper->bank->account_title, $subject);
                    }

                    if (strpos($body, '[account_title]') !== FALSE) {
                        $body = str_replace('[account_title]', $shipper->bank->account_title, $body);
                    }

                    if (strpos($subject, '[iban]') !== FALSE) {
                        $subject = str_replace('[iban]', $shipper->bank->iban, $subject);
                    }

                    if (strpos($body, '[iban]') !== FALSE) {
                        $body = str_replace('[iban]', $shipper->bank->iban, $body);
                    }

                    if (strpos($subject, '[account_city]') !== FALSE) {
                        $subject = str_replace('[account_city]', $shipper->bank->city->name, $subject);
                    }

                    if (strpos($body, '[account_city]') !== FALSE) {
                        $body = str_replace('[account_city]', $shipper->bank->city->name, $body);
                    }

                    if (strpos($subject, '[payment_cycle]') !== FALSE) {
                        $subject = str_replace('[payment_cycle]', $shipper->payment_cycle->name, $subject);
                    }

                    if (strpos($body, '[account_number]') !== FALSE) {
                        $body = str_replace('[payment_cycle]', $shipper->bank->payment_cycle, $body);
                    }

                    if (strpos($subject, '[payment_done_id]') !== FALSE) {
                        $subject = str_replace('[payment_done_id]', str_pad($done_payment->id, 6, '0', STR_PAD_LEFT), $subject);
                    }

                    if (strpos($body, '[payment_done_id]') !== FALSE) {
                        $body = str_replace('[payment_done_id]', str_pad($done_payment->id, 6, '0', STR_PAD_LEFT), $body);
                    }

                    if (strpos($subject, '[payment_done_at]') !== FALSE) {
                        $subject = str_replace('[payment_done_at]', $done_payment->created_at, $subject);
                    }

                    if (strpos($body, '[payment_done_at]') !== FALSE) {
                        $body = str_replace('[payment_done_at]', $done_payment->created_at, $body);
                    }

                    if (strpos($subject, '[total_shipments]') !== FALSE) {
                        $subject = str_replace('[total_shipments]', $done_payment->total_shipments, $subject);
                    }

                    if (strpos($body, '[total_shipments]') !== FALSE) {
                        $body = str_replace('[total_shipments]', $done_payment->total_shipments, $body);
                    }

                    if (strpos($subject, '[delivered_shipments]') !== FALSE) {
                        $subject = str_replace('[delivered_shipments]', $done_payment->delivered_shipments, $subject);
                    }

                    if (strpos($body, '[delivered_shipments]') !== FALSE) {
                        $body = str_replace('[delivered_shipments]', $done_payment->delivered_shipments, $body);
                    }

                    if (strpos($subject, '[returned_shipments]') !== FALSE) {
                        $subject = str_replace('[returned_shipments]', $done_payment->returned_shipments, $subject);
                    }

                    if (strpos($body, '[returned_shipments]') !== FALSE) {
                        $body = str_replace('[returned_shipments]', $done_payment->returned_shipments, $body);
                    }

                    if (strpos($subject, '[adjusted_shipments]') !== FALSE) {
                        $subject = str_replace('[adjusted_shipments]', $done_payment->adjusted_shipments, $subject);
                    }

                    if (strpos($body, '[adjusted_shipments]') !== FALSE) {
                        $body = str_replace('[adjusted_shipments]', $done_payment->adjusted_shipments, $body);
                    }

                    //            $to = $shipper->email;
                    if (ShipperNotificationEmail::where('user_id', $shipper->id)->exists()) {
                        $to = ShipperNotificationEmail::where('user_id', $shipper->id)->whereNotNull('email')->pluck('email')->toArray();
                    } else {
                        $to = $shipper->email;
                    }
                    $shipment_details = '<table style="padding:5px; border: 1px solid black; border-collapse: collapse;"><tbody><tr>';

                    $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">S. No.</td>';

                    foreach ($present_fields as $field) {
                        $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">' . $field_names[$field] . '</td>';
                    }

                    $shipment_details .= '</tr>';

                    $serial_number = 1;

                    $total_amount = 0;
                    $total_weight_charges = 0;
                    $total_cash_handling_charges = 0;
                    $total_insurance_charges = 0;
                    $total_replacement_charges = 0;
                    // $total_try_and_buy_charges = 0;
                    $total_return_charges = 0;
                    $total_packaging_material_charges = 0;
                    $total_fuel_surcharge = 0;
                    $total_gst = 0;
                    $total_charges = 0;
                    $total_payable = 0;

                    foreach ($done_payment->done_payment_shipments as $done_payment_shipment) {
                        $shipment_details .= '<tr>';

                        $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial_number . '</td>';

                        $shipment = $done_payment_shipment->shipment;

                        foreach ($present_fields as $field) {
                            if (in_array($field, ['amount', 'charges', 'gst', 'payable'])) {
                                $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $done_payment_shipment[$field] . '</td>';
                            } else if ($field == 'consignee_city') {
                                $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment->consignee_city->name . '</td>';
                            } else if (!empty($shipment[$field])) {
                                $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment[$field] . '</td>';
                            } else {
                                $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"></td>';
                            }
                        }

                        $shipment_details .= '</tr>';

                        $serial_number++;

                        if ($done_payment_shipment->type == 0) {
                            $total_amount += $done_payment_shipment->amount;
                            $total_cash_handling_charges += $shipment->cash_handling_charges;
                            $total_replacement_charges += $shipment->replacement_charges;
                            // $total_try_and_buy_charges += $shipment->try_and_buy_charges;
                        } else {
                            $total_return_charges += $shipment->return_charges;
                        }

                        $total_weight_charges += $shipment->weight_charges;

                        if ($shipment->packaging_material_request) {
                            $total_packaging_material_charges += $shipment->packaging_material_charges;
                        }

                        $total_insurance_charges += $shipment->insurance_charges;
                        $total_fuel_surcharge += $shipment->fuel_surcharge;

                        $total_gst += $done_payment_shipment->gst;
                        $total_charges += $done_payment_shipment->charges + $done_payment_shipment->gst;
                        $total_payable += $done_payment_shipment->payable;
                    }

                    $shipment_details .= '</tbody></table>';

                    foreach ($present_fields as $field) {
                        if ($field != $first_field) {
                            $body = str_replace('[' . $field . ']', '', $body);
                        }
                    }

                    $body = str_replace('[' . $first_field . ']', $shipment_details, $body);

                    if (strpos($subject, '[total_amount]') !== FALSE) {
                        $subject = str_replace('[total_amount]', $total_amount, $subject);
                    }

                    if (strpos($body, '[total_amount]') !== FALSE) {
                        $body = str_replace('[total_amount]', $total_amount, $body);
                    }

                    if (strpos($subject, '[total_weight_charges]') !== FALSE) {
                        $subject = str_replace('[total_weight_charges]', $total_weight_charges, $subject);
                    }

                    if (strpos($body, '[total_weight_charges]') !== FALSE) {
                        $body = str_replace('[total_weight_charges]', $total_weight_charges, $body);
                    }

                    if (strpos($subject, '[total_cash_handling_charges]') !== FALSE) {
                        $subject = str_replace('[total_cash_handling_charges]', $total_cash_handling_charges, $subject);
                    }

                    if (strpos($body, '[total_cash_handling_charges]') !== FALSE) {
                        $body = str_replace('[total_cash_handling_charges]', $total_cash_handling_charges, $body);
                    }

                    if (strpos($subject, '[total_insurance_charges]') !== FALSE) {
                        $subject = str_replace('[total_insurance_charges]', $total_insurance_charges, $subject);
                    }

                    if (strpos($body, '[total_insurance_charges]') !== FALSE) {
                        $body = str_replace('[total_insurance_charges]', $total_insurance_charges, $body);
                    }

                    if (strpos($subject, '[total_replacement_charges]') !== FALSE) {
                        $subject = str_replace('[total_replacement_charges]', $total_replacement_charges, $subject);
                    }

                    if (strpos($body, '[total_replacement_charges]') !== FALSE) {
                        $body = str_replace('[total_replacement_charges]', $total_replacement_charges, $body);
                    }

                    // if (strpos($subject, '[total_try_and_buy_charges]') !== FALSE) {
                    //   $subject = str_replace('[total_try_and_buy_charges]', $total_try_and_buy_charges, $subject);
                    // }

                    // if (strpos($body, '[total_try_and_buy_charges]') !== FALSE) {
                    //   $body = str_replace('[total_try_and_buy_charges]', $total_try_and_buy_charges, $body);
                    // }

                    if (strpos($subject, '[total_return_charges]') !== FALSE) {
                        $subject = str_replace('[total_return_charges]', $total_return_charges, $subject);
                    }

                    if (strpos($body, '[total_return_charges]') !== FALSE) {
                        $body = str_replace('[total_return_charges]', $total_return_charges, $body);
                    }

                    if (strpos($subject, '[total_packaging_material_charges]') !== FALSE) {
                        $subject = str_replace('[total_packaging_material_charges]', $total_packaging_material_charges, $subject);
                    }

                    if (strpos($body, '[total_packaging_material_charges]') !== FALSE) {
                        $body = str_replace('[total_packaging_material_charges]', $total_packaging_material_charges, $body);
                    }

                    if (strpos($subject, '[total_fuel_surcharge]') !== FALSE) {
                        $subject = str_replace('[total_fuel_surcharge]', $total_fuel_surcharge, $subject);
                    }

                    if (strpos($body, '[total_fuel_surcharge]') !== FALSE) {
                        $body = str_replace('[total_fuel_surcharge]', $total_fuel_surcharge, $body);
                    }

                    if (strpos($subject, '[total_gst]') !== FALSE) {
                        $subject = str_replace('[total_gst]', $total_gst, $subject);
                    }

                    if (strpos($body, '[total_gst]') !== FALSE) {
                        $body = str_replace('[total_gst]', $total_gst, $body);
                    }

                    if (strpos($subject, '[total_charges]') !== FALSE) {
                        $subject = str_replace('[total_charges]', $total_charges, $subject);
                    }

                    if (strpos($body, '[total_charges]') !== FALSE) {
                        $body = str_replace('[total_charges]', $total_charges, $body);
                    }

                    if (strpos($subject, '[total_payable]') !== FALSE) {
                        $subject = str_replace('[total_payable]', $total_payable, $subject);
                    }

                    if (strpos($body, '[total_payable]') !== FALSE) {
                        $body = str_replace('[total_payable]', $total_payable, $body);
                    }

                    $bcc = array();

                    $general_admins = Admin::whereIn('role_id', [4, 3])->where('status', 1)->whereNotNull('email');
                    if ($general_admins->exists()) {
                        $bcc = array_merge($bcc, $general_admins->pluck('email')->toArray());
                    }

                    $hub_id = $shipper->city->hub_id;

                    $related_admins = Admin::whereIn('role_id', [10])->where('status', 1)->whereNotNull('email')->whereHas('hubs', function ($query) use ($hub_id) {
                        $query->where('hub_id', $hub_id);
                    });

                    if ($related_admins->exists()) {
                        $bcc = array_merge($bcc, $related_admins->pluck('email')->toArray());
                    }

                    if (empty($bcc)) {
                        $bcc = NULL;
                    }

                    self::email($subject, $body, $to, NULL, $bcc);
                } else if ($id == 21) {
                    $fields = ['consignee_name' => 'consignee_name', 'consignee_address' => 'consignee_address', 'order_id' => 'order_id', 'amount' => 'amount', 'tracking_number' => 'tracking_number'];

                    $shipment = Shipment::find($reference_1_id);

                    $shipper = $shipment->user;

                    foreach ($fields as $key => $field) {
                        if (strpos($subject, '[' . $key . ']') !== FALSE) {
                            $subject = str_replace('[' . $key . ']', $shipment[$field], $subject);
                        }

                        if (strpos($body, '[' . $key . ']') !== FALSE) {
                            $body = str_replace('[' . $key . ']', $shipment[$field], $body);
                        }
                    }

                    if (strpos($subject, '[company_name]') !== FALSE) {
                        $subject = str_replace('[company_name]', $shipper->name, $subject);
                    }

                    if (strpos($body, '[company_name]') !== FALSE) {
                        $body = str_replace('[company_name]', $shipper->name, $body);
                    }

                    if (strpos($subject, '[service_type]') !== FALSE) {
                        $subject = str_replace('[service_type]', $shipment->booking_type->booking_type, $subject);
                    }

                    if (strpos($body, '[service_type]') !== FALSE) {
                        $body = str_replace('[service_type]', $shipment->booking_type->booking_type, $body);
                    }

                    if (strpos($subject, '[pickup_address]') !== FALSE) {
                        $subject = str_replace('[pickup_address]', $shipment->pickup_address->address, $subject);
                    }

                    if (strpos($body, '[pickup_address]') !== FALSE) {
                        $body = str_replace('[pickup_address]', $shipment->pickup_address->address, $body);
                    }

                    if (strpos($subject, '[pickup_city]') !== FALSE) {
                        $subject = str_replace('[pickup_city]', $shipment->pickup_address->city->name, $subject);
                    }

                    if (strpos($body, '[pickup_city]') !== FALSE) {
                        $body = str_replace('[pickup_city]', $shipment->pickup_address->city->name, $body);
                    }

                    if (strpos($subject, '[consignee_city]') !== FALSE) {
                        $subject = str_replace('[consignee_city]', $shipment->consignee_city->name, $subject);
                    }

                    if (strpos($body, '[consignee_city]') !== FALSE) {
                        $body = str_replace('[consignee_city]', $shipment->consignee_city->name, $body);
                    }

                    if (strpos($subject, '[payment_mode]') !== FALSE) {
                        $subject = str_replace('[payment_mode]', $shipment->payment_mode->mode, $subject);
                    }

                    if (strpos($body, '[payment_mode]') !== FALSE) {
                        $body = str_replace('[payment_mode]', $shipment->payment_mode->mode, $body);
                    }

                    //            $to = [$shipper->email];
                    if (ShipperNotificationEmail::where('user_id', $shipper->id)->exists()) {
                        $to = ShipperNotificationEmail::where('user_id', $shipper->id)->whereNotNull('email')->pluck('email')->toArray();
                    } else {
                        $to = [$shipper->email];
                    }
                    $general_admins = Admin::whereIn('role_id', [4])->where('status', 1)->whereNotNull('email');

                    if ($general_admins->exists()) {
                        $to = array_merge($to, $general_admins->pluck('email')->toArray());
                    }
                    $reference_2_id_email = Admin::find($reference_2_id)->email;
                    if ($reference_2_id_email != null) {
                        $to[] = $reference_2_id_email;
                    }

                    self::email($subject, $body, $to);
                } else if ($id == 22) {
                    $possible_fields = ['company_name', 'person_of_contact', 'phone_number', 'address', 'city'];

                    $present_fields = array();

                    $first_field = NULL;

                    $position = NULL;

                    foreach ($possible_fields as $field) {
                        $new_position = strpos($body, '[' . $field . ']');

                        if ($new_position !== FALSE) {
                            if ($position == NULL) {
                                $present_fields[] = $field;

                                $first_field = $field;
                            } else if ($new_position > $position) {
                                $present_fields[] = $field;
                            } else {
                                array_unshift($present_fields, $field);
                            }

                            $position = $new_position;
                        }
                    }

                    $pickup_note = V2PickupNote::find($reference_1_id);

                    $to = $pickup_note->rider->phone;

                    $pickup_details = '';

                    foreach ($pickup_note->pickup_note_requests as $pickup_note_request) {
                        $pickup_request = $pickup_note_request->pickup_request;
                        $pickup_address = $pickup_request->pickup_address;

                        foreach ($present_fields as $field) {
                            if ($field == 'company_name') {
                                $pickup_details .= $pickup_request->shipper->name . ', ';
                            } else if ($field == 'person_of_contact') {
                                $pickup_details .= $pickup_address->poc . ', ';
                            } else if ($field == 'phone_number') {
                                $pickup_details .= $pickup_address->phone . ', ';
                            } else if ($field == 'address') {
                                $pickup_details .= $pickup_address->pickup_address . ', ';
                            } else if ($field == 'city') {
                                $pickup_details .= $pickup_address->city->name . ', ';
                            }
                        }

                        $pickup_details = substr($pickup_details, 0, -2) . PHP_EOL;
                    }

                    foreach ($present_fields as $field) {
                        if ($field != $first_field) {
                            $body = str_replace('[' . $field . ']', '', $body);
                        }
                    }

                    $body = str_replace('[' . $first_field . ']', $pickup_details, $body);

                    self::sms($body, $to);
                } else if ($id == 23) {
                    $shipments = Shipment::where('shipper_status_id', 12);

                    if ($shipments->exists()) {
                        $subject = $notification->subject;
                        $body = $notification->body;

                        $possible_fields = ['service_type', 'pickup_address', 'pickup_city', 'consignee_name', 'consignee_phone_number_1', 'consignee_phone_number_2', 'consignee_email', 'consignee_address', 'consignee_city', 'order_id', 'shipping_mode', 'amount', 'payment_mode', 'status', 'status_reason', 'status_date', 'arrival_date', 'tracking_number'];

                        $field_names = ['service_type' => 'Service Type', 'pickup_address' => 'Pickup Address', 'pickup_city' => 'Pickup City', 'consignee_name' => 'Consignee Name', 'consignee_phone_number_1' => 'Consignee Phone Number 1', 'consignee_phone_number_2' => 'Consignee Phone Number 2', 'consignee_email' => 'Consignee Email', 'consignee_address' => 'Consignee Address', 'consignee_city' => 'Consignee City', 'order_id' => 'Order ID', 'shipping_mode' => 'Shipping Mode', 'amount' => 'Amount', 'payment_mode' => 'Payment Mode', 'status' => 'Status', 'status_reason' => 'Status Reason', 'status_date' => 'Status Date', 'arrival_date' => 'Arrival Date', 'tracking_number' => 'Tracking Number'];

                        $present_fields = array();

                        $first_field = NULL;

                        $position = NULL;

                        foreach ($possible_fields as $field) {
                            $new_position = strpos($body, '[' . $field . ']');

                            if ($new_position !== FALSE) {
                                if ($position == NULL) {
                                    $present_fields[] = $field;

                                    $first_field = $field;
                                } else if ($new_position > $position) {
                                    $present_fields[] = $field;
                                } else {
                                    array_unshift($present_fields, $field);
                                }

                                $position = $new_position;
                            }
                        }

                        $shipments = $shipments->get();

                        $settings = GlobalSettings::where('type', 'return_confirmation_pending_shipment_selection_time')->first();

                        if ($settings) {
                            $return_confirmation_pending_shipment_selection_time = $settings->setting_value;
                        } else {
                            $return_confirmation_pending_shipment_selection_time = 0;
                        }

                        $yesterday = Carbon::yesterday();

                        $yesterday->hour = $return_confirmation_pending_shipment_selection_time;

                        $today = Carbon::today();

                        $today->hour = $return_confirmation_pending_shipment_selection_time;

                        $user_wise_shipments = array();

                        foreach ($shipments as $shipment) {
                            $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 12)->latest()->first();

                            if ($shipment_journey && $shipment_journey->verification && Carbon::parse($shipment_journey->created_at)->greaterThanOrEqualTo($yesterday) && Carbon::parse($shipment_journey->created_at)->lessThan($today)) {
                                $details = array();

                                $details['service_type'] = $shipment->booking_type->booking_type;
                                $details['pickup_address'] = $shipment->pickup_address->address;
                                $details['pickup_city'] = $shipment->pickup_address->city->name;
                                $details['consignee_name'] = $shipment->consignee_name;
                                $details['consignee_phone_number_1'] = $shipment->consignee_phone_number_1;
                                $details['consignee_phone_number_2'] = $shipment->consignee_phone_number_2;
                                $details['consignee_email'] = $shipment->consignee_email;
                                $details['consignee_address'] = $shipment->consignee_address;
                                $details['consignee_city'] = $shipment->consignee_city->name;
                                $details['order_id'] = $shipment->order_id;
                                $details['shipping_mode'] = $shipment->shipping_mode->mode;
                                $details['amount'] = $shipment->amount;
                                $details['payment_mode'] = $shipment->payment_mode->mode;
                                $details['status'] = $shipment_journey->shipment_status_shipper->name;

                                if ($shipment_journey->status_reason_id) {
                                    $details['status_reason'] = $shipment_journey->shipment_status_reason->name;
                                }

                                $details['status_date'] = $shipment_journey->created_at;

                                $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 2)->first();

                                if ($shipment_journey) {
                                    $details['arrival_date'] = $shipment_journey->created_at;
                                } else {
                                    $details['arrival_date'] = $shipment->created_at;
                                }

                                $details['tracking_number'] = $shipment->tracking_number;

                                $user_wise_shipments[$shipment->user_id][] = $details;
                            }
                        }

                        if (!empty($user_wise_shipments)) {
                            $original_subject = $subject;
                            $original_body = $body;

                            foreach ($user_wise_shipments as $user_id => $shipments) {
                                $shipper = User::find($user_id);

                                if (strpos($subject, '[company_name]') !== FALSE) {
                                    $subject = str_replace('[company_name]', $shipper->name, $subject);
                                }

                                if (strpos($body, '[company_name]') !== FALSE) {
                                    $body = str_replace('[company_name]', $shipper->name, $body);
                                }
                                if (ShipperNotificationEmail::where('user_id', $shipper->id)->exists()) {
                                    $to = ShipperNotificationEmail::where('user_id', $shipper->id)->whereNotNull('email')->pluck('email')->toArray();
                                } else {
                                    $to = $shipper->email;
                                }

                                $shipment_details = '<table style="padding:5px; border: 1px solid black; border-collapse: collapse;"><tbody><tr>';

                                $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">S. No.</td>';

                                foreach ($present_fields as $field) {
                                    $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">' . $field_names[$field] . '</td>';
                                }

                                $shipment_details .= '</tr>';

                                $serial_number = 1;

                                foreach ($shipments as $shipment) {
                                    $shipment_details .= '<tr>';

                                    $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial_number . '</td>';

                                    foreach ($present_fields as $field) {
                                        if (!empty($shipment[$field])) {
                                            $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment[$field] . '</td>';
                                        } else {
                                            $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"></td>';
                                        }
                                    }

                                    $shipment_details .= '</tr>';

                                    $serial_number++;
                                }

                                $shipment_details .= '</tbody></table>';

                                foreach ($present_fields as $field) {
                                    if ($field != $first_field) {
                                        $body = str_replace('[' . $field . ']', '', $body);
                                    }
                                }

                                $body = str_replace('[' . $first_field . ']', $shipment_details, $body);

                                $cc = array();


                                //                  $general_admins = Admin::whereIn('role_id', [15, 21])->where('status', 1);
                                //
                                //                  if ($general_admins->exists()) {
                                //                    $cc = array_merge($cc, $general_admins->pluck('email')->toArray());
                                //                  }

                                self::email($subject, $body, $to, $cc, NULL, 'returns@trax.pk');

                                $subject = $original_subject;
                                $body = $original_body;
                            }
                        }
                    }
                } else if ($id == 24) {
                    $shipments = Shipment::where('shipper_status_id', 20);

                    if ($shipments->exists()) {
                        $subject = $notification->subject;
                        $body = $notification->body;

                        $possible_fields = ['service_type', 'pickup_address', 'pickup_city', 'consignee_name', 'consignee_phone_number_1', 'consignee_phone_number_2', 'consignee_email', 'consignee_address', 'consignee_city', 'order_id', 'shipping_mode', 'status', 'status_reason', 'status_date', 'tracking_number'];

                        $field_names = ['service_type' => 'Service Type', 'pickup_address' => 'Pickup Address', 'pickup_city' => 'Pickup City', 'consignee_name' => 'Consignee Name', 'consignee_phone_number_1' => 'Consignee Phone Number 1', 'consignee_phone_number_2' => 'Consignee Phone Number 2', 'consignee_email' => 'Consignee Email', 'consignee_address' => 'Consignee Address', 'consignee_city' => 'Consignee City', 'order_id' => 'Order ID', 'shipping_mode' => 'Shipping Mode', 'status' => 'Status', 'status_reason' => 'Status Reason', 'status_date' => 'Status Date', 'tracking_number' => 'Tracking Number'];

                        $present_fields = array();

                        $first_field = NULL;

                        $position = NULL;

                        foreach ($possible_fields as $field) {
                            $new_position = strpos($body, '[' . $field . ']');

                            if ($new_position !== FALSE) {
                                if ($position == NULL) {
                                    $present_fields[] = $field;

                                    $first_field = $field;
                                } else if ($new_position > $position) {
                                    $present_fields[] = $field;
                                } else {
                                    array_unshift($present_fields, $field);
                                }

                                $position = $new_position;
                            }
                        }

                        $shipments = $shipments->get();

                        $hub_wise_shipments = array();

                        foreach ($shipments as $shipment) {
                            $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 20)->latest()->first();

                            if ($shipment_journey && $shipment_journey->verification) {
                                $details = array();

                                $details['service_type'] = $shipment->booking_type->booking_type;
                                $details['pickup_address'] = $shipment->pickup_address->address;
                                $details['pickup_city'] = $shipment->pickup_address->city->name;
                                $details['consignee_name'] = $shipment->consignee_name;
                                $details['consignee_phone_number_1'] = $shipment->consignee_phone_number_1;
                                $details['consignee_phone_number_2'] = $shipment->consignee_phone_number_2;
                                $details['consignee_email'] = $shipment->consignee_email;
                                $details['consignee_address'] = $shipment->consignee_address;
                                $details['consignee_city'] = $shipment->consignee_city->name;
                                $details['order_id'] = $shipment->order_id;
                                $details['shipping_mode'] = $shipment->shipping_mode->mode;
                                $details['status'] = $shipment_journey->shipment_status_shipper->name;

                                if ($shipment_journey->status_reason_id) {
                                    $details['status_reason'] = $shipment_journey->shipment_status_reason->name;
                                }

                                $details['status_date'] = $shipment_journey->created_at;

                                $details['tracking_number'] = $shipment->tracking_number;

                                $hub_wise_shipments[$shipment->consignee_city->hub_id][] = $details;
                            }
                        }

                        if (!empty($hub_wise_shipments)) {
                            $original_subject = $subject;
                            $original_body = $body;

                            foreach ($hub_wise_shipments as $hub_id => $shipments) {
                                $hub = City::find($hub_id);

                                if (strpos($subject, '[hub]') !== FALSE) {
                                    $subject = str_replace('[hub]', $hub->name, $subject);
                                }

                                if (strpos($body, '[hub]') !== FALSE) {
                                    $body = str_replace('[hub]', $hub->name, $body);
                                }

                                $shipment_details = '<table style="padding:5px; border: 1px solid black; border-collapse: collapse;"><tbody><tr>';

                                $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">S. No.</td>';

                                foreach ($present_fields as $field) {
                                    $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">' . $field_names[$field] . '</td>';
                                }

                                $shipment_details .= '</tr>';

                                $serial_number = 1;

                                foreach ($shipments as $shipment) {
                                    $shipment_details .= '<tr>';

                                    $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial_number . '</td>';

                                    foreach ($present_fields as $field) {
                                        if (!empty($shipment[$field])) {
                                            $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment[$field] . '</td>';
                                        } else {
                                            $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"></td>';
                                        }
                                    }

                                    $shipment_details .= '</tr>';

                                    $serial_number++;
                                }

                                $shipment_details .= '</tbody></table>';

                                foreach ($present_fields as $field) {
                                    if ($field != $first_field) {
                                        $body = str_replace('[' . $field . ']', '', $body);
                                    }
                                }

                                $body = str_replace('[' . $first_field . ']', $shipment_details, $body);

                                $to = array();

                                /*$general_admins = Admin::whereIn('role_id', [3])->where('status', 1);

                                if ($general_admins->exists()) {
                                    $to = array_merge($to, $general_admins->pluck('email')->toArray());
                                }*/

                                $related_admins = Admin::whereIn('role_id', [9, 10, 25])->where('status', 1)->whereNotNull('email')->whereHas('hubs', function ($query) use ($hub_id) {
                                    $query->where('hub_id', $hub_id);
                                });

                                if ($related_admins->exists()) {
                                    $to = array_merge($to, $related_admins->pluck('email')->toArray());
                                }

                                $on_request_admin = Admin::where('id', 10)->where('status', 1)->whereNotNull('email');

                                if ($on_request_admin->exists()) {
                                    $to = array_merge($to, $on_request_admin->pluck('email')->toArray());
                                }

                                self::email($subject, $body, $to);

                                $subject = $original_subject;
                                $body = $original_body;
                            }
                        }
                    }
                } else if ($id == 25) {
                    $shipments = Shipment::where('shipper_status_id', 13);

                    if ($shipments->exists()) {
                        $subject = $notification->subject;
                        $body = $notification->body;

                        $possible_fields = ['service_type', 'pickup_address', 'pickup_city', 'consignee_name', 'consignee_phone_number_1', 'consignee_phone_number_2', 'consignee_email', 'consignee_address', 'consignee_city', 'order_id', 'shipping_mode', 'status', 'status_reason', 'status_date', 'tracking_number'];

                        $field_names = ['service_type' => 'Service Type', 'pickup_address' => 'Pickup Address', 'pickup_city' => 'Pickup City', 'consignee_name' => 'Consignee Name', 'consignee_phone_number_1' => 'Consignee Phone Number 1', 'consignee_phone_number_2' => 'Consignee Phone Number 2', 'consignee_email' => 'Consignee Email', 'consignee_address' => 'Consignee Address', 'consignee_city' => 'Consignee City', 'order_id' => 'Order ID', 'shipping_mode' => 'Shipping Mode', 'status' => 'Status', 'status_reason' => 'Status Reason', 'status_date' => 'Status Date', 'tracking_number' => 'Tracking Number'];

                        $present_fields = array();

                        $first_field = NULL;

                        $position = NULL;

                        foreach ($possible_fields as $field) {
                            $new_position = strpos($body, '[' . $field . ']');

                            if ($new_position !== FALSE) {
                                if ($position == NULL) {
                                    $present_fields[] = $field;

                                    $first_field = $field;
                                } else if ($new_position > $position) {
                                    $present_fields[] = $field;
                                } else {
                                    array_unshift($present_fields, $field);
                                }

                                $position = $new_position;
                            }
                        }

                        $shipments = $shipments->get();

                        $hub_wise_shipments = array();

                        foreach ($shipments as $shipment) {
                            $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 13)->latest()->first();

                            if ($shipment_journey && $shipment_journey->verification) {
                                $details = array();

                                $details['service_type'] = $shipment->booking_type->booking_type;
                                $details['pickup_address'] = $shipment->pickup_address->address;
                                $details['pickup_city'] = $shipment->pickup_address->city->name;
                                $details['consignee_name'] = $shipment->consignee_name;
                                $details['consignee_phone_number_1'] = $shipment->consignee_phone_number_1;
                                $details['consignee_phone_number_2'] = $shipment->consignee_phone_number_2;
                                $details['consignee_email'] = $shipment->consignee_email;
                                $details['consignee_address'] = $shipment->consignee_address;
                                $details['consignee_city'] = $shipment->consignee_city->name;
                                $details['order_id'] = $shipment->order_id;
                                $details['shipping_mode'] = $shipment->shipping_mode->mode;
                                $details['status'] = $shipment_journey->shipment_status_shipper->name;

                                if ($shipment_journey->status_reason_id) {
                                    $details['status_reason'] = $shipment_journey->shipment_status_reason->name;
                                }

                                $details['status_date'] = $shipment_journey->created_at;

                                $details['tracking_number'] = $shipment->tracking_number;

                                $hub_wise_shipments[$shipment->consignee_city->hub_id][] = $details;
                            }
                        }

                        if (!empty($hub_wise_shipments)) {
                            $original_subject = $subject;
                            $original_body = $body;

                            foreach ($hub_wise_shipments as $hub_id => $shipments) {
                                $hub = City::find($hub_id);

                                if (strpos($subject, '[hub]') !== FALSE) {
                                    $subject = str_replace('[hub]', $hub->name, $subject);
                                }

                                if (strpos($body, '[hub]') !== FALSE) {
                                    $body = str_replace('[hub]', $hub->name, $body);
                                }

                                $shipment_details = '<table style="padding:5px; border: 1px solid black; border-collapse: collapse;"><tbody><tr>';

                                $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">S. No.</td>';

                                foreach ($present_fields as $field) {
                                    $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">' . $field_names[$field] . '</td>';
                                }

                                $shipment_details .= '</tr>';

                                $serial_number = 1;

                                foreach ($shipments as $shipment) {
                                    $shipment_details .= '<tr>';

                                    $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial_number . '</td>';

                                    foreach ($present_fields as $field) {
                                        if (!empty($shipment[$field])) {
                                            $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment[$field] . '</td>';
                                        } else {
                                            $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"></td>';
                                        }
                                    }

                                    $shipment_details .= '</tr>';

                                    $serial_number++;
                                }

                                $shipment_details .= '</tbody></table>';

                                foreach ($present_fields as $field) {
                                    if ($field != $first_field) {
                                        $body = str_replace('[' . $field . ']', '', $body);
                                    }
                                }

                                $body = str_replace('[' . $first_field . ']', $shipment_details, $body);

                                $to = array();

                                //                                $general_admins = Admin::whereIn('role_id', [3])->where('status', 1);
                                //
                                //                                if ($general_admins->exists()) {
                                //                                    $to = array_merge($to, $general_admins->pluck('email')->toArray());
                                //                                }

                                $related_admins = Admin::whereIn('role_id', [9, 10, 25, 31])->where('status', 1)->whereNotNull('email')->whereHas('hubs', function ($query) use ($hub_id) {
                                    $query->where('hub_id', $hub_id);
                                });

                                if ($related_admins->exists()) {
                                    $to = array_merge($to, $related_admins->pluck('email')->toArray());
                                }

                                self::email($subject, $body, $to);

                                $subject = $original_subject;
                                $body = $original_body;
                            }
                        }
                    }
                } else if ($id == 26) {
                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $reference_1_id, $subject);
                    }

                    if (strpos($body, '[date]') !== FALSE) {
                        $body = str_replace('[date]', $reference_1_id, $body);
                    }

                    $link = '<a href="' . $reference_2_id . '" target="_blank">Report</a>';

                    if (strpos($subject, '[link]') !== FALSE) {
                        $subject = str_replace('[link]', $link, $subject);
                    }

                    if (strpos($body, '[link]') !== FALSE) {
                        $body = str_replace('[link]', $link, $body);
                    }

                    $to = array();
                    $bcc = array();
                    $cc = array();
                    /*$admins = Admin::whereIn('role_id', [2, 3, 4, 20, 22, 61, 58, 56, 40, 31])->where('status', 1);

                    if ($admins->exists()) {
                        $to = array_merge($to, $admins->pluck('email')->toArray());
                    }

                    $admins = Admin::join('admin_roles', 'admins.role_id', '=', 'admin_roles.id')->where('admin_roles.department_id', 7)->where('admins.status', 1);

                    if ($admins->exists()) {
                        $to = array_merge($to, $admins->pluck('admins.email')->toArray());
                    }

                    $ceo = Admin::find(8);

                    if ($ceo) {
                        $to[] = $ceo->email;
                    }*/
                    $to = ['mohsin.ali@trax.pk', 'waqas@trax.pk', 'hassan@trax.pk', 'noman.aziz@trax.pk', 'asad.ahsan@trax.pk', 'fawad.ahmed@trax.pk', 'nadir.qureshi@trax.pk', 'hammad.saleem@trax.pk', 'rahat.ali@trax.pk', 'hassan.arman@trax.pk'];

                    $bcc = ['muhammad.waqas@trax.pk', 'danish.zahid@trax.pk'];
                    self::email($subject, $body, $to, $cc, $bcc);
                } else if ($id == 27) {

                    $shipper_fields = ['account_id' => 'id', 'company_name' => 'name'];

                    $invoice_fields = ['invoice_number' => 'invoice_number', 'billing_period_from_date' => 'billing_period_from_date', 'billing_period_to_date' => 'billing_period_to_date', 'due_date' => 'due_date'];

                    $invoice = Invoice::find($reference_1_id);

                    $to = array();

                    $shipper = $invoice->shipper;

                    $to[] = $shipper->email;


                    if (isset($shipper->bank)) {
                        foreach ($shipper->bank as $bank) {
                            $to[] = $bank->billing_person_email;
                        }
                    }

                    foreach ($shipper_fields as $key => $field) {
                        if (strpos($subject, '[' . $key . ']') !== FALSE) {
                            if ($key == 'account_id') {
                                $subject = str_replace('[' . $key . ']', str_pad($shipper[$field], 6, '0', STR_PAD_LEFT), $subject);
                            } else {
                                $subject = str_replace('[' . $key . ']', $shipper[$field], $subject);
                            }
                        }

                        if (strpos($body, '[' . $key . ']') !== FALSE) {
                            if ($key == 'account_id') {
                                $body = str_replace('[' . $key . ']', str_pad($shipper[$field], 6, '0', STR_PAD_LEFT), $body);
                            } else {
                                $body = str_replace('[' . $key . ']', $shipper[$field], $body);
                            }
                        }
                    }

                    foreach ($invoice_fields as $key => $field) {
                        if (strpos($subject, '[' . $key . ']') !== FALSE) {
                            if ($key == 'billing_period_from_date' || $key == 'billing_period_to_date' || $key == 'due_date') {
                                $subject = str_replace('[' . $key . ']', Carbon::parse($invoice[$field])->format('d/m/Y'), $subject);
                            } else {
                                $subject = str_replace('[' . $key . ']', $invoice[$field], $subject);
                            }
                        }

                        if (strpos($body, '[' . $key . ']') !== FALSE) {
                            if ($key == 'billing_period_from_date' || $key == 'billing_period_to_date' || $key == 'due_date') {
                                $body = str_replace('[' . $key . ']', Carbon::parse($invoice[$field])->format('d/m/Y'), $body);
                            } else {
                                $body = str_replace('[' . $key . ']', $invoice[$field], $body);
                            }
                        }
                    }

                    if (strpos($subject, '[invoice]') !== FALSE) {
                        $subject = str_replace('[invoice]', '', $subject);
                    }

                    if (strpos($body, '[invoice]') !== FALSE) {

                        $invoice = AdminFinanceController::email_print_invoice($reference_1_id, TRUE);
                        $link = '<a href="' . $invoice . '" download="invoice" target="_blank" >Invoice</a>';
                        $body = str_replace('[invoice]', $link, $body);
                    }

                    $cc = array();

                    $cc[] = 'shafay.tariq@trax.pk';
                    $sales_person = SalePersonTag::where('user_id', $shipper->id)->where('status', 0)->first();
                    // $sales_person_admin = Admin::find($sales_person->admin_id);

                    if ($sales_person) {
                        $cc[] = Admin::find($sales_person->admin_id)->email;
                    }
                    $regional_managers = Admin::join('admin_hubs', 'admin_hubs.admin_id', '=', 'admins.id')->where('role_id', 60)->where('admins.status', 1)->where('admin_hubs.hub_id', '=', $shipper->city->zone_id);

                    // $regional_manager = Admin::whereIn('role_id', [44, 27])->where('default_hub_id', $sales_person_admin->default_hub1)->get()->first();
                    // $general_admins = Admin::where('role_id', 2)->where('status', 1);
                    if ($regional_managers->exists()) {
                        $cc = array_merge($cc, $regional_managers->whereNotNull('admins.email')->pluck('admins.email')->toArray());
                        // $cc[] = $regional_manager->email;
                    }

                    $general_admins = Admin::whereIn('role_id', [4, 31])->where('status', 1)->whereNotNull('email');

                    if ($general_admins->exists()) {
                        $cc = array_merge($cc, $general_admins->pluck('email')->toArray());
                    }

                    self::email($subject, $body, $to, $cc);
                } else if ($id == 28) {
                    $shipper_fields = ['account_id' => 'id', 'company_name' => 'name'];

                    $invoice_fields = ['invoice_number' => 'invoice_number', 'billing_period_from_date' => 'billing_period_from_date', 'billing_period_to_date' => 'billing_period_to_date', 'due_date' => 'due_date'];

                    $invoice = Invoice::find($reference_1_id);

                    $to = array();

                    $shipper = $invoice->shipper;

                    $to[] = $shipper->email;

                    $to[] = $shipper->bank->billing_person_email;

                    foreach ($shipper_fields as $key => $field) {
                        if (strpos($subject, '[' . $key . ']') !== FALSE) {
                            if ($key == 'account_id') {
                                $subject = str_replace('[' . $key . ']', str_pad($shipper[$field], 6, '0', STR_PAD_LEFT), $subject);
                            } else {
                                $subject = str_replace('[' . $key . ']', $shipper[$field], $subject);
                            }
                        }

                        if (strpos($body, '[' . $key . ']') !== FALSE) {
                            if ($key == 'account_id') {
                                $body = str_replace('[' . $key . ']', str_pad($shipper[$field], 6, '0', STR_PAD_LEFT), $body);
                            } else {
                                $body = str_replace('[' . $key . ']', $shipper[$field], $body);
                            }
                        }
                    }

                    foreach ($invoice_fields as $key => $field) {
                        if (strpos($subject, '[' . $key . ']') !== FALSE) {
                            if ($key == 'billing_period_from_date' || $key == 'billing_period_to_date' || $key == 'due_date') {
                                $subject = str_replace('[' . $key . ']', Carbon::parse($invoice[$field])->format('d/m/Y'), $subject);
                            } else {
                                $subject = str_replace('[' . $key . ']', $invoice[$field], $subject);
                            }
                        }

                        if (strpos($body, '[' . $key . ']') !== FALSE) {
                            if ($key == 'billing_period_from_date' || $key == 'billing_period_to_date' || $key == 'due_date') {
                                $body = str_replace('[' . $key . ']', Carbon::parse($invoice[$field])->format('d/m/Y'), $body);
                            } else {
                                $body = str_replace('[' . $key . ']', $invoice[$field], $body);
                            }
                        }
                    }

                    if (strpos($subject, '[invoice]') !== FALSE) {
                        $subject = str_replace('[invoice]', '', $subject);
                    }

                    if (strpos($body, '[invoice]') !== FALSE) {
                        $invoice = AdminFinanceController::generate_invoice_print($reference_1_id, TRUE);

                        $body = str_replace('[invoice]', preg_replace('/\r|\n/', '', $invoice), $body);
                    }

                    $cc = array();

                    $general_admins = Admin::where('role_id', 2)->where('status', 1)->whereNotNull('email');

                    if ($general_admins->exists()) {
                        $cc = array_merge($cc, $general_admins->pluck('email')->toArray());
                    }

                    self::email($subject, $body, $to, $cc);
                } else if ($id == 31) {
                    $possible_fields = ['tracking_number', 'shipper_name', 'email', 'phone', 'destination', 'channel', 'case_nature', 'case_nature_type', 'details', 'status'];
                    $shipment_email = false;
                    $crm_request = CrmRequest::find($reference_1_id);
                    if ($crm_request) {
                        $tagging = CrmRequestTagging::where('crm_request_id', $crm_request->id)->first();
                        if ($crm_request->status_id != 1) {
                            if ($tagging) {

                                if ($tagging->crm_request_tagging_type_id == 1) {
                                    $roles = AdminRole::where('department_id', $tagging->tagged_id)->pluck('id');
                                    $admin_department = Admin::whereIn('role_id', $roles)->where('status', 1)->whereNotNull('email');
                                    if ($admin_department->exists()) {
                                        $to = $admin_department->pluck('email')->toArray();
                                        // $to_sms = $admin_department->pluck('phone_number');
                                    } else {
                                        $to = 'complaints@trax.pk';
                                    }
                                } else if ($tagging->crm_request_tagging_type_id == 2) {
                                    $admin_department = Admin::find($tagging->tagged_id)->email;
                                    $to = $admin_department;
                                    // $to_sms = Admin::find($tagging->tagged_id)->phone_number;
                                } else {
                                    $to = 'complaints@trax.pk';
                                }
                            } else {
                                // $to = array();
                                // array_push($to, 'complaints@trax.pk');
                                $to = 'complaints@trax.pk';
                            }
                        } else {
                            $to = 'complaints@trax.pk';
                        }

                        $table_details = '';

                        if ($crm_request->shipment_id) {
                            $shipment = Shipment::find($crm_request->shipment_id);
                            $tracking_number = '';
                            $shipper_name = '';
                            $shipper_email = '';
                            $shipper_phone = '';
                            $shipper_destination = '';
                            if ($shipment) {
                                $shipment_email = true;
                                if (strpos($subject, '[tracking_number]') !== FALSE) {
                                    $subject = str_replace('[tracking_number]', $shipment->tracking_number, $subject);
                                    $tracking_number = $shipment->tracking_number;
                                }
                                if (strpos($subject, '[shipper_name]') !== FALSE) {
                                    $subject = str_replace('[shipper_name]', $shipment->user->name, $subject);
                                    $shipper_name = $shipment->user->name;
                                }
                                if (strpos($subject, '[email]') !== FALSE) {
                                    $subject = str_replace('[email]', $shipment->user->email, $subject);
                                    $shipper_email = $shipment->user->email;
                                }
                                if (strpos($subject, '[phone]') !== FALSE) {
                                    $subject = str_replace('[phone]', $shipment->user->phone, $subject);
                                    $shipper_phone = $shipment->user->phone;
                                }
                                if (strpos($subject, '[destination]') !== FALSE) {
                                    $subject = str_replace('[destination]', $shipment->consignee_city->name, $subject);
                                    $shipper_destination = $shipment->consignee_city->name;
                                }


                                if (strpos($body, '[tracking_number]') !== FALSE) {
                                    $table_details .= '<tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">Tracking Number</th><td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment->tracking_number . '</td></tr>';
                                }
                                if (strpos($body, '[shipper_name]') !== FALSE) {
                                    $table_details .= '<tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">Shipper Name</th><td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment->user->name . '</td></tr>';
                                }
                                if (strpos($body, '[email]') !== FALSE) {
                                    $table_details .= '<tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">Shipper Email</th><td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment->user->email . '</td></tr>';
                                }
                                if (strpos($body, '[phone]') !== FALSE) {
                                    $table_details .= '<tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">Shipper Phone</th><td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment->user->phone . '</td></tr>';
                                }
                                if (strpos($body, '[destination]') !== FALSE) {
                                    $table_details .= '<tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">Destination</th><td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment->consignee_city->name . '</td></tr>';
                                }
                            } else {
                                if (strpos($body, '[tracking_number]') !== FALSE) {
                                    $body = str_replace('[tracking_number]', '', $body);
                                }
                                if (strpos($body, '[shipper_name]') !== FALSE) {
                                    $body = str_replace('[shipper_name]', '', $body);
                                }
                                if (strpos($body, '[email]') !== FALSE) {
                                    $body = str_replace('[email]', '', $body);
                                }
                                if (strpos($body, '[phone]') !== FALSE) {
                                    $body = str_replace('[phone]', '', $body);
                                }
                                if (strpos($body, '[destination]') !== FALSE) {
                                    $body = str_replace('[destination]', '', $body);
                                }
                            }
                        }

                        if (strpos($subject, '[request_id]') !== FALSE) {
                            $subject = str_replace('[request_id]', $crm_request->id, $subject);
                        }
                        if (strpos($subject, '[channel]') !== FALSE) {
                            $subject = str_replace('[channel]', $crm_request->channel->channel, $subject);
                        }
                        if (strpos($subject, '[case_nature]') !== FALSE) {
                            $subject = str_replace('[case_nature]', $crm_request->nature->name, $subject);
                        }
                        if (strpos($subject, '[case_nature_type]') !== FALSE) {
                            $subject = str_replace('[case_nature_type]', $crm_request->nature->type, $subject);
                        }
                        if (strpos($subject, '[status]') !== FALSE) {
                            $subject = str_replace('[status]', $crm_request->request_status->name, $subject);
                        }
                        if (strpos($subject, '[details]') !== FALSE) {
                            $subject = str_replace('[details]', $crm_request->description, $subject);
                        }

                        if (strpos($body, '[channel]') !== FALSE) {
                            $table_details .= '<tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">Channel</th><td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $crm_request->channel->channel . '</td></tr>';
                        }
                        if (strpos($body, '[case_nature]') !== FALSE) {
                            $table_details .= '<tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">Case Nature</th><td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $crm_request->nature->name . '</td></tr>';
                        }
                        if ($crm_request->case_nature_id != 3) {
                            if (strpos($body, '[case_nature_type]') !== FALSE) {
                                $table_details .= '<tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">Case Nature Type</th><td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $crm_request->nature_type->type . '</td></tr>';
                            }
                        }
                        if (strpos($body, '[status]') !== FALSE) {
                            $table_details .= '<tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">Status</th><td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $crm_request->request_status->name . '</td></tr>';
                        }
                        if (strpos($body, '[details]') !== FALSE) {
                            $table_details .= '<tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">Description</th><td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $crm_request->description . '</td></tr>';
                        }

                        $table_details = '<table style="padding:5px; border: 1px solid black; border-collapse: collapse;"><tbody>' . $table_details . '</tbody></table>';

                        $first = TRUE;

                        foreach ($possible_fields as $possible_field) {
                            if (strpos($body, '[' . $possible_field . ']') !== FALSE) {
                                if ($first) {
                                    $body = str_replace('[' . $possible_field . ']', $table_details, $body);

                                    $first = FALSE;
                                } else {
                                    $body = str_replace('[' . $possible_field . ']', '', $body);
                                }
                            }
                        }

                        if ($crm_request->status_id != 1) {
                            $cc = array();
                            $crm_roles = Admin::where('role_id', 37)->where('status', 1)->whereNotNull('email');
                            if ($crm_roles->exists()) {
                                $crm_roles = $crm_roles->pluck('email')->toArray();
                                $cc = array_merge($cc, $crm_roles);
                            }
                            if ($to != 'complaints@trax.pk') {
                                $complains_email = 'complaints@trax.pk';
                                array_push($cc, $complains_email);
                                self::email($subject, $body, $to, $cc);
                            } else {
                                self::email($subject, $body, $to);
                                if ($shipment_email) {
                                    self::email($subject, $body, $shipment->user->email);
                                }
                            }
                        } else {

                            self::email($subject, $body, $to);
                            if ($shipment_email) {
                                self::email($subject, $body, $shipment->user->email);
                            }
                        }
                        // $sms_body= 'Request ID: '.$crm_request->id.', Tracking Number :'.$tracking_number.' '.PHP_EOL.
                        // 'Shipper Name: '.$shipper_name.''.PHP_EOL.
                        // 'Shipper Email: '.$shipper_email.''.PHP_EOL.
                        // 'Shipper Phone: '.$shipper_phone.''.PHP_EOL.
                        // 'Destination: '.$shipper_destination.''.PHP_EOL.
                        // 'Channel: '.$crm_request->channel->channel.''.PHP_EOL.
                        // 'Case Nature: '.$crm_request->nature->name.''.PHP_EOL.
                        // 'Case Nature Type: '.$crm_request->nature->type.''.PHP_EOL.
                        // 'Status: '.$crm_request->request_status->name.''.PHP_EOL.
                        // 'Description: '.$crm_request->description.''.PHP_EOL.'';

                        // self::sms($sms_body, $to_sms);
                    }
                } else if ($id == 32) {
                    $nsa_shipment = Shipment::find($reference_1_id);

                    if (strpos($subject, '[nsa]') !== FALSE) {
                        $subject = str_replace('[nsa]', $reference_2_id, $subject);
                    }

                    if (strpos($body, '[nsa]') !== FALSE) {
                        $body = str_replace('[nsa]', $reference_2_id, $body);
                    }

                    if (strpos($subject, '[tracking_number]') !== FALSE) {
                        $subject = str_replace('[tracking_number]', $nsa_shipment->tracking_number, $subject);
                    }

                    if (strpos($body, '[tracking_number]') !== FALSE) {
                        $body = str_replace('[tracking_number]', $nsa_shipment->tracking_number, $body);
                    }

                    if (ShipperNotificationEmail::where('user_id', $nsa_shipment->user_id)->exists()) {
                        $to = ShipperNotificationEmail::where('user_id', $nsa_shipment->user_id)->whereNotNull('email')->pluck('email')->toArray();
                    } else {
                        $to = $nsa_shipment->user->email;
                    }

                    self::email($subject, $body, $to);
                } else if ($id == 33) {
                    $nsa_shipment = Shipment::find($reference_1_id);
                    if (strpos($subject, '[tracking_number]') !== FALSE) {
                        $subject = str_replace('[tracking_number]', $nsa_shipment->tracking_number, $subject);
                    }
                    if (strpos($body, '[tracking_number]') !== FALSE) {
                        $body = str_replace('[tracking_number]', $nsa_shipment->tracking_number, $body);
                    }
                    if (strpos($subject, '[destination]') !== FALSE) {
                        $subject = str_replace('[destination]', $nsa_shipment->consignee_city->name, $subject);
                    }
                    if (strpos($body, '[destination]') !== FALSE) {
                        $body = str_replace('[destination]', $nsa_shipment->consignee_city->name, $body);
                    }

                    if (strpos($subject, '[nsa_osa_estimated_charges]') !== FALSE) {
                        $subject = str_replace('[nsa_osa_estimated_charges]', $nsa_shipment->nsa_osa_estimated_charges, $subject);
                    }
                    if (strpos($body, '[nsa_osa_estimated_charges]') !== FALSE) {
                        $body = str_replace('[nsa_osa_estimated_charges]', $nsa_shipment->nsa_osa_estimated_charges, $body);
                    }

                    $journey = ShipmentsJourney::where('shipment_id', $reference_1_id)->where('shipper_status_id', 12)->whereIn('status_reason_id', [12, 34])->latest('id')->first();
                    if (strpos($subject, '[remarks]') !== FALSE) {
                        $subject = str_replace('[remarks]', $journey->remarks, $subject);
                    }
                    if (strpos($body, '[remarks]') !== FALSE) {
                        $body = str_replace('[remarks]', $journey->remarks, $body);
                    }

                    $hub_id = $nsa_shipment->consignee_city->hub_id;

                    if (ShipperNotificationEmail::where('user_id', $nsa_shipment->user_id)->exists()) {
                        $to = ShipperNotificationEmail::where('user_id', $nsa_shipment->user_id)->whereNotNull('email')->pluck('email')->toArray();
                    } else {
                        $to = $nsa_shipment->user->email;
                    }

                    $cc = array();

                    $general_admins = Admin::whereIn('role_id', [3, 7, 14])->where('status', 1)->whereNotNull('email');

                    if ($general_admins->exists()) {
                        $cc = array_merge($cc, $general_admins->pluck('email')->toArray());
                    }

                    $related_admins = Admin::whereIn('role_id', [8, 9])->where('status', 1)->whereNotNull('email')->whereHas('hubs', function ($query) use ($hub_id) {
                        $query->where('hub_id', $hub_id);
                    });

                    if ($related_admins->exists()) {
                        $cc = array_merge($cc, $related_admins->pluck('email')->toArray());
                    }

                    self::email($subject, $body, $to);
                } else if ($id == 34) {
                    $user_id = str_pad($reference_1_id, 6, '0', STR_PAD_LEFT);
                    $updated_at = Carbon::now();
                    $sale_person = Admin::where('id', $reference_2_id)->first();

                    if (strpos($subject, '[user_id]') !== FALSE) {
                        $subject = str_replace('[user_id]', $user_id, $subject);
                    }
                    if (strpos($body, '[user_id]') !== FALSE) {
                        $body = str_replace('[user_id]', $user_id, $body);
                    }
                    if (strpos($subject, '[updated_at]') !== FALSE) {
                        $subject = str_replace('[updated_at]', $updated_at, $subject);
                    }
                    if (strpos($body, '[updated_at]') !== FALSE) {
                        $body = str_replace('[updated_at]', $updated_at, $body);
                    }

                    if (strpos($body, '[tagged_sales_person]') !== FALSE) {
                        $body = str_replace('[tagged_sales_person]', $sale_person->name, $body);
                    }
                    $to = array();

                    $admins = Admin::whereIn('role_id', [2, 4])->where('status', 1)->whereNotNull('email');

                    if ($admins->exists()) {
                        $to = array_merge($to, $admins->pluck('email')->toArray());
                    }

                    self::email($subject, $body, $to);
                } else if ($id == 35) {
                    $shipment = Shipment::find($reference_1_id);
                    //yep sms
                    if ($shipment->user_id == 12613) {
                        $body = 'Your YAP Debit card has been successfully delivered.' . PHP_EOL . ' Thankyou';
                        $to = $shipment->consignee_phone_number_1;
                        self::sms($body, $to);
                    } else {

                        $shipment_journey = ShipmentsJourney::where('shipment_id', $reference_1_id)->where('verification', 1)->latest('id')->first();


                        if (strpos($body, '[tracking_number]') !== FALSE) {
                            $body = str_replace('[tracking_number]', $shipment->tracking_number, $body);
                        }
                        if (strpos($body, '[consignee_name]') !== FALSE) {
                            $body = str_replace('[consignee_name]', $shipment->consignee_name, $body);
                        }
                        if ($shipment->pickup_address->pickup_brand_name != NULL) {
                            $brand_name = $shipment->pickup_address->pickup_brand_name;
                        } else {
                            if ($shipment->user->brand_name != NULL) {
                                $brand_name = $shipment->user->brand_name;
                            } else {
                                $brand_name = $shipment->user->name;
                            }
                        }
                        if (strpos($body, '[shipper_name]') !== FALSE) {
                            $body = str_replace('[shipper_name]', $brand_name, $body);
                        }
                        if (strpos($body, '[receiver_name]') !== FALSE) {
                            $body = str_replace('[receiver_name]', $shipment_journey->received_or_refused_by, $body);
                        }
                        if (strpos($body, '[order_id]') !== FALSE) {
                            $body = str_replace('[order_id]', $shipment->order_id, $body);
                        }

                        if (strpos($body, '[status_date]') !== FALSE) {
                            $body = str_replace('[status_date]', $shipment_journey->created_at, $body);
                        }

                        $to = $shipment->consignee_phone_number_1;
                        self::sms($body, $to);
                    }
                    //yep sms end
                } else if ($id == 36 || $id == 37) {
                    $account_a = User::where('id', $reference_1_id)->first();
                    $account_b = User::where('id', $reference_2_id)->first();
                    //                $account_id_a = str_pad($account_a->id, 6, '0', STR_PAD_LEFT);
                    $account_id_b = str_pad($account_b->id, 6, '0', STR_PAD_LEFT);
                    $logo = '<img class="brand-logo trax" alt="Trax" src="' . asset('img/trax_logo_new.png') . '" width="100" height="50">';
                    if (strpos($subject, '[account_id]') !== FALSE) {
                        $subject = str_replace('[account_id]', $account_id_b, $subject);
                    }
                    if (strpos($body, '[account_id]') !== FALSE) {
                        $body = str_replace('[account_id]', $account_id_b, $body);
                    }
                    if (strpos($subject, '[company_name_b]') !== FALSE) {
                        $subject = str_replace('[company_name_b]', $account_b->name, $subject);
                    }
                    if (strpos($body, '[company_name_b]') !== FALSE) {
                        $body = str_replace('[company_name_b]', $account_b->name, $body);
                    }

                    if (strpos($body, '[company_name_a]') !== FALSE) {
                        $body = str_replace('[company_name_a]', $account_a->name, $body);
                    }
                    if (strpos($body, '[trax_logo]') !== FALSE) {
                        $body = str_replace('[trax_logo]', $logo, $body);
                    }
                    $to = $account_a->email;

                    self::email($subject, $body, $to);
                } else if ($id == 38) {
                    $shipper = User::find($reference_1_id);
                    if ($shipper) {
                        $terms = CRFTermsConditions::where('user_id', $shipper->id)->first();
                        if ($terms) {
                            $yes_link = '<a href="' . route('cod.terms.accept', ['token' => $terms->token, 'id' => $shipper->id]) . '"><b>Yes</b></a>';
                            $yes = 'To accept terms and conditions:' . PHP_EOL . $yes_link;
                            $download_link = '<a href="' . route('cod.terms.download', ['token' => $terms->token, 'id' => $shipper->id]) . '">Download CRF</a>';
                            $download = 'To download CRF document:' . PHP_EOL . $download_link;
                            $logo = '<img class="brand-logo trax" alt="Trax" src="' . asset('img/trax_logo_new.png') . '" width="100" height="50">';
                            $button = $yes;
                            $link = $download;
                            /*                            $button = '<div class="row"><button onclick="window.open(' . route('cod.terms.accept', ['token' => $terms->token, 'id' => $shipper->id]) . ')" type="button" style="width: 100px; height: 40px; background-color: transparent; border: 2px solid black; border-radius: 5px; font-size: 25px; font-weight: bold;">Yes</button>';
                                                        $link = '<div class="row"><button onclick="window.open(' . route('cod.terms.download', ['token' => $terms->token, 'id' => $shipper->id]) . ')" type="button" style="height: 40px; background-color: transparent; border: 2px solid black; border-radius: 5px; font-size: 18px; font-weight: bold;">CRF Download</button>';*/
                            if (strpos($subject, '[shipper_name]') !== FALSE) {
                                $subject = str_replace('[shipper_name]', $shipper->name, $subject);
                            }
                            if (strpos($body, '[shipper_name]') !== FALSE) {
                                $body = str_replace('[shipper_name]', $shipper->name, $body);
                            }
                            if (strpos($body, '[trax_logo]') !== FALSE) {
                                $body = str_replace('[trax_logo]', $logo, $body);
                            }

                            if (strpos($body, '[link]') !== FALSE) {
                                $body = str_replace('[link]', $link, $body);
                            }

                            if (strpos($body, '[button]') !== FALSE) {
                                $body = str_replace('[button]', $button, $body);
                            }


                            $sale_person_id = SalePersonTag::where('user_id', $shipper->id)->where('status', 0)->select('admin_id')->first();
                            if ($sale_person_id) {
                                $sale_person_email = Admin::find($sale_person_id->admin_id)->email;
                            }
                            $sale_person_hub = Admin::find($sale_person_id->admin_id)->default_hub;

                            /*$regional_manager = Admin::join('admin_hubs', 'admin_hubs.admin_id', '=', 'admins.id')
                                ->where('admins.role_id', 4)->where('admins.status', 1)->where('admin_hubs.hub_id', '=', $sale_person_hub)->select('email')->first();*/
                            //                            $department_head_email = Admin::where('role_id', 44)->where('status', 1)->select('email')->first();
                            $cc = array();
                            $related_admins = Admin::where('role_id', 44)->where('status', 1)->whereNotNull('email')->whereHas('hubs', function ($query) use ($sale_person_hub) {
                                $query->where('hub_id', $sale_person_hub);
                            });
                            if ($related_admins->exists()) {
                                $cc = array_merge($cc, $related_admins->pluck('email')->toArray());
                            }


                            if ($sale_person_email) {
                                $cc[] = $sale_person_email;
                            }

                            //                            if($department_head_email) {
                            //                                $cc[] = $department_head_email;
                            //                            }


                            $to = $shipper->email;
                            self::email($subject, $body, $to, $cc);
                        }
                    }
                } else if ($id == 39) {

                    $subject = $notification->subject;
                    $body = $notification->body;

                    $settings = GlobalSettings::where('type', 'return_delivered_to_shipper_cut_off_time');

                    if ($settings->exists()) {
                        $settings = $settings->first();
                        $rdts_time = $settings->setting_value;
                    } else {
                        $rdts_time = 0;
                    }

                    $yesterday = Carbon::yesterday();

                    $yesterday->hour = $rdts_time;


                    $today = Carbon::today();

                    $today->hour = $rdts_time;

                    //                    $yesterday =  Carbon::now()->startOfDay()->toDateTimeString();
//                    $today = Carbon::parse($yesterday)->endOfDay()->toDateTimeString();

                    $possible_fields = ['tracking_number', 'status_updated_at', 'receiver_name', ''];

                    $field_names = ['tracking_number' => 'Tracking Number', 'status_updated_at' => 'Status Updated At', 'receiver_name' => 'Received By', 'returned_at' => 'Returned At'];

                    $present_fields = array();

                    $first_field = NULL;

                    $position = NULL;

                    foreach ($possible_fields as $field) {
                        $new_position = strpos($body, '[' . $field . ']');

                        if ($new_position !== FALSE) {
                            if ($position == NULL) {
                                $present_fields[] = $field;

                                $first_field = $field;
                            } else if ($new_position > $position) {
                                $present_fields[] = $field;
                            } else {
                                array_unshift($present_fields, $field);
                            }

                            $position = $new_position;
                        }
                    }

                    $user_wise_shipments = array();
                    $shipment_details = ShipmentsJourney::join('shipments', 'shipments.id', '=', 'shipments_journey.shipment_id')
                        ->whereIn('shipments.shipper_status_id', [25, 31, 38])
                        ->whereIn('shipments_journey.shipper_status_id', [25, 31, 38])
                        ->whereBetween('shipments_journey.created_at', [$yesterday, $today])->select('shipments.user_id', 'shipments.tracking_number', 'shipments_journey.created_at', 'shipments_journey.received_or_refused_by', 'shipments_journey.reference_1_id');

                    if ($shipment_details->exists()) {

                        $shipment_details = $shipment_details->get();

                        foreach ($shipment_details as $data) {

                            $details = array();

                            $details['tracking_number'] = $data->tracking_number;
                            $details['status_updated_at'] = $data->created_at;
                            $details['receiver_name'] = $data->received_or_refused_by;
                            $return_note = ReturnNote::find($data->reference_1_id);
                            $returned_at = '';
                            if ($return_note && $return_note->actual_date != null) {
                                $returned_at = Carbon::parse($return_note->actual_date)->toDateString();
                            }
                            $details['returned_at'] = $returned_at;

                            $user_wise_shipments[$data->user_id][] = $details;
                        }

                        if (!empty($user_wise_shipments)) {
                            $original_subject = $subject;
                            $original_body = $body;

                            foreach ($user_wise_shipments as $user_id => $shipments) {
                                $shipper = User::find($user_id);

                                if (strpos($subject, '[company_name]') !== FALSE) {
                                    $subject = str_replace('[company_name]', $shipper->name, $subject);
                                }

                                if (strpos($body, '[company_name]') !== FALSE) {
                                    $body = str_replace('[company_name]', $shipper->name, $body);
                                }

                                if (ShipperNotificationEmail::where('user_id', $shipper->id)->exists()) {

                                    $to = ShipperNotificationEmail::where('user_id', $shipper->id)->pluck('email')->toArray();
                                } else {
                                    $to = $shipper->email;
                                }


                                $shipment_details = '<table style="padding:5px; border: 1px solid black; border-collapse: collapse;"><tbody><tr>';

                                $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">S. No.</td>';

                                foreach ($present_fields as $field) {
                                    $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">' . $field_names[$field] . '</td>';
                                }

                                $shipment_details .= '</tr>';

                                $serial_number = 1;

                                foreach ($shipments as $shipment) {
                                    $shipment_details .= '<tr>';

                                    $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial_number . '</td>';

                                    foreach ($present_fields as $field) {
                                        if (!empty($shipment[$field])) {
                                            $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment[$field] . '</td>';
                                        } else {
                                            $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"></td>';
                                        }
                                    }

                                    $shipment_details .= '</tr>';

                                    $serial_number++;
                                }

                                $shipment_details .= '</tbody></table>';

                                foreach ($present_fields as $field) {
                                    if ($field != $first_field) {
                                        $body = str_replace('[' . $field . ']', '', $body);
                                    }
                                }

                                $body = str_replace('[' . $first_field . ']', $shipment_details, $body);

                                $data1 = ReturnNote::join('return_note_shipments as rns', 'rns.return_note_id', '=', 'return_notes.id')
                                    ->leftjoin('shipments as s', 's.id', '=', 'rns.shipment_id')
                                    ->leftjoin('shipments_journey as sj', 'sj.shipment_id', '=', 's.id')
                                    ->whereIn('sj.shipper_status_id', [25, 31, 38])
                                    ->where('s.user_id', $user_id)
                                    ->whereBetween('sj.created_at', [$yesterday, $today])
                                    ->whereBetween('return_notes.updated_at', [$yesterday, $today])
                                    ->select('return_notes.id as return_note_id', 'sj.shipment_id as shipment_id')
                                    //                                    ->where('sj.reference_1_id','return_notes.id')
                                    ->get();
                                //                                dd($data1);

                                //                                $data1 = ReturnDeliveredToShipperSms::join('return_notes as rn', 'rn.id', '=', 'return_delivered_to_shipper_sms.return_note_id')
//                                    ->join('shipments', 'shipments.id', '=', 'return_delivered_to_shipper_sms.shipment_id')
//                                    ->join('users as u', 'u.id', '=', 'return_delivered_to_shipper_sms.user_id')
////                                    ->where('return_delivered_to_shipper_sms.status', 0)
//                                    ->whereBetween('return_delivered_to_shipper_sms.created_at',[$yesterday,$today])
//                                    ->select('return_delivered_to_shipper_sms.return_note_id as return_note_id','shipments.id as shipment_id','return_delivered_to_shipper_sms.user_id as user_id',
//                                        'u.phone as phone_number',
//                                        DB::raw("(select count(return_note_id)
//                                        from return_delivered_to_shipper_sms
//                                        Where  created_at >= '$yesterday'
//                                        and  created_at <= '$today'
//                                        and return_note_id = rn.id) as shipment_count"))
//                                    ->groupBy('return_delivered_to_shipper_sms.return_note_id')
//                                    ->get();

                                $da = [];
                                $i = 0;
                                foreach ($data1 as $key => $value) {

                                    $da[$value->return_note_id]['shipment_id'][$i] = isset($da[$value->return_note_id]['shipment_id'][$i]) ? $da[$value->return_note_id]['shipment_id'][$i] : $value->shipment_id;
                                    $da[$value->return_note_id]['count'] = isset($da[$value->return_note_id]['count']) ? $da[$value->return_note_id]['count'] += 1 : 1;

                                    $i++;
                                }
                                $return_detail = "";
                                foreach ($da as $key => $val) {

                                    $shipment_ids = implode(',', $val['shipment_id']);
                                    $count = $val['count'];
                                    $return_detail .= PHP_EOL . " Return ID : $key ," . PHP_EOL . "Having shipments : $count " . PHP_EOL . "---" . PHP_EOL;

                                }

                                if (strpos($body, '[return_detail]') !== FALSE) {
                                    $body = str_replace('[return_detail]', $return_detail, $body);
                                }
                                //                                dd($body);

                                self::email($subject, $body, $to);

                                $subject = $original_subject;
                                $body = $original_body;
                            }
                        }
                    }
                } else if ($id == 40) {
                    $delivery_note = DeliveryNote::find($reference_1_id);
                    if ($delivery_note) {
                        $rider = Rider::find($delivery_note->rider_id);
                        $delivery_note_id = str_pad($delivery_note->id, 6, '0', STR_PAD_LEFT);

                        if (strpos($body, '[rider_name]') !== FALSE) {
                            $body = str_replace('[rider_name]', $rider->name, $body);
                        }

                        if (strpos($body, '[delivery_note_id]') !== FALSE) {
                            $body = str_replace('[delivery_note_id]', $delivery_note_id, $body);
                        }

                        if (strpos($body, '[password]') !== FALSE) {
                            $body = str_replace('[password]', $delivery_note->password, $body);
                        }
                        $to = $rider->phone;
                        self::sms($body, $to);
                    }
                } else if ($id == 41) {
                    $crm_request = CrmRequest::find($reference_1_id);
                    if ($crm_request) {
                        $user = User::find($crm_request->shipper_id);
                        if ($user) {
                            $crm_request_id = str_pad($crm_request->id, 6, '0', STR_PAD_LEFT);

                            if (strpos($subject, '[request_id]') !== FALSE) {
                                $subject = str_replace('[request_id]', $crm_request_id, $subject);
                            }
                            $to = $user->email;
                            self::email($subject, $body, $to);
                        }
                    }
                } else if ($id == 42) {
                    $rider = Rider::find($reference_1_id);
                    $user = User::find($reference_2_id);
                    if ($user) {
                        if ($rider) {
                            if (strpos($body, '[rider_name]') !== FALSE) {
                                $body = str_replace('[rider_name]', $rider->name, $body);
                            }

                            if (strpos($body, '[rider_phone_number]') !== FALSE) {
                                $body = str_replace('[rider_phone_number]', $rider->phone, $body);
                            }

                            $to = $user->phone;
                            self::sms($body, $to);
                        }
                    }
                } else if ($id == 43) {
                    $pickup_request = V2PickupRequest::find($reference_1_id);
                    if ($pickup_request) {
                        $vendor = $pickup_request->pickup_address->vendor;
                        if ($vendor != null) {
                            $shipper_name = $pickup_request->shipper->name;

                            if (strpos($subject, '[shipper_name]') !== FALSE) {
                                $subject = str_replace('[shipper_name]', $shipper_name, $subject);
                            }
                            if (strpos($body, '[shipper_name]') !== FALSE) {
                                $body = str_replace('[shipper_name]', $shipper_name, $body);
                            }
                            if (strpos($body, '[vendor]') !== FALSE) {
                                $body = str_replace('[vendor]', $vendor, $body);
                            }

                            $assigned_shipments = $pickup_request->pickup_request_shipments;
                            $shipment_details = '<table style="width:100%;">';
                            $shipment_details .= '<thead><tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Tracking Number.</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Item Description</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Destination</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Quantity</th></tr></thead>';
                            $shipment_details .= '<tbody>';
                            foreach ($assigned_shipments as $assigned_shipment) {
                                $shipment = $assigned_shipment->shipment;
                                $items = ShipmentItem::where('shipment_id', $shipment->id)->first();
                                $shipment_details .= '<tr>';
                                $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment->tracking_number . '</td>';
                                $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . (isset($items->description) ? $items->description : '') . '</td>';
                                $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment->consignee_city->name . '</td>';
                                $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $items->quantity . '</td>';
                                $shipment_details .= '</tr>';
                            }
                            $shipment_details .= '</tbody></table>';

                            if (strpos($body, '[shipments_detail]') !== FALSE) {
                                $body = str_replace('[shipments_detail]', $shipment_details, $body);
                            }

                            $to = $pickup_request->pickup_address->email;
                            self::email($subject, $body, $to);
                        }
                    }
                } else if ($id == 44) {
                    $hubs = DB::connection('reports')->table('cities')->where('hub', 1)->select('id', 'name');
                    if ($hubs->exists()) {
                        $hubs = $hubs->get();

                        $original_subject = $subject;
                        $original_body = $body;

                        $date = $reference_1_id;
                        foreach ($hubs as $hub) {
                            $debriefing = Debriefing::where('hub', $hub->id)->first();
                            if (strpos($subject, '[hub]') !== FALSE) {
                                $subject = str_replace('[hub]', $hub->name, $subject);
                            }
                            if (strpos($body, '[hub]') !== FALSE) {
                                $body = str_replace('[hub]', $hub->name, $body);
                            }

                            if (strpos($subject, '[date]') !== FALSE) {
                                $subject = str_replace('[date]', $date, $subject);
                            }
                            if (strpos($body, '[date]') !== FALSE) {
                                $body = str_replace('[date]', $date, $body);
                            }

                            $details = '<table style="width:100%;">';
                            $details .= '<thead><tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue">Hub</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Delivered</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Delivery Unsuccessful</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">On Hold</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse; color: red">Status Not Attempted</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse; color: red">Fake Status</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Confirmation Pending</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue">Total</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue">Ratio</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Delivery Note Pending</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue">Total</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue">Ratio</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Delivery Tomorrow</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue">Grand Total</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue">Ratio</th></tr></thead>';
                            $details .= '<tbody>';
                            $details .= '<tr>';
                            $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold">' . $hub->name . '</td>';
                            $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $debriefing->delivered . '</td>';
                            $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $debriefing->delivery_unsuccessful . '</td>';
                            $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $debriefing->on_hold . '</td>';
                            $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: red">' . $debriefing->status_not_attempted . '</td>';
                            $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: red">' . $debriefing->fake_status . '</td>';
                            $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $debriefing->confirmation_pending . '</td>';
                            $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold">' . $debriefing->total_1 . '</td>';
                            $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold">' . $debriefing->total_1_ratio . '</td>';
                            $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $debriefing->delivery_note_pending . '</td>';
                            $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold">' . $debriefing->total_2 . '</td>';
                            $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold">' . $debriefing->total_2_ratio . '</td>';
                            $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $debriefing->delivery_tomorrow . '</td>';
                            $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold">' . $debriefing->grand_total . '</td>';
                            $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold">' . $debriefing->grand_total_ratio . '</td>';
                            $details .= '</tr>';
                            $details .= '</tbody></table>';

                            if (strpos($body, '[preview]') !== FALSE) {
                                $body = str_replace('[preview]', $details, $body);
                            }
                            $file = Storage::disk('public')->url('/reports/debriefing/hubs/debriefing_report_' . $date . '_' . $hub->id . '.xlsx');

                            $link = '<br/><a href="' . $file . '" target="_blank"><u>Download</u></a>';

                            if (strpos($subject, '[link]') !== FALSE) {
                                $subject = str_replace('[link]', $link, $subject);
                            }
                            if (strpos($body, '[link]') !== FALSE) {
                                $body = str_replace('[link]', $link, $body);
                            }
                            $operation_admins = Admin::join('admin_hubs', 'admin_hubs.admin_id', '=', 'admins.id')->whereIn('admins.role_id', [9, 10])->where('admins.status', 1)->whereNotNull('admins.email')->where('admin_hubs.hub_id', '=', $hub->id);
                            if ($operation_admins->exists()) {
                                $to = $operation_admins->pluck('admins.email')->toArray();
                            }
                            $cc = array();

                            $general_managers = Admin::join('admin_hubs', 'admin_hubs.admin_id', '=', 'admins.id')->whereIn('role_id', [3, 8, 18, 19, 20, 34])->where('admins.status', 1)->where('admin_hubs.hub_id', '=', $hub->id)->whereNotNull('admins.email');

                            if ($general_managers->exists()) {
                                $cc = array_merge($cc, $general_managers->pluck('admins.email')->toArray());
                            }

                            self::email($subject, $body, $to, $cc);

                            $subject = $original_subject;
                            $body = $original_body;
                        }
                    }
                } else if ($id == 45) {
                    $zones = DB::connection('reports')->table('zones')->where('status', 1)->select('id', 'name');
                    if ($zones->exists()) {
                        $zones = $zones->get();

                        $original_subject = $subject;
                        $original_body = $body;

                        $date = $reference_1_id;
                        foreach ($zones as $zone) {
                            $debriefings = Debriefing::where('zone_id', $zone->id)->get();
                            if (strpos($subject, '[zone]') !== FALSE) {
                                $subject = str_replace('[zone]', $zone->name, $subject);
                            }
                            if (strpos($body, '[zone]') !== FALSE) {
                                $body = str_replace('[zone]', $zone->name, $body);
                            }

                            if (strpos($subject, '[date]') !== FALSE) {
                                $subject = str_replace('[date]', $date, $subject);
                            }
                            if (strpos($body, '[date]') !== FALSE) {
                                $body = str_replace('[date]', $date, $body);
                            }

                            $details = '<table style="width:100%;">';
                            $details .= '<thead><tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue">Hub</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Delivered</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Delivery Unsuccessful</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">On Hold</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse; color: red">Status Not Attempted</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse; color: red">Fake Status</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Confirmation Pending</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue">Total</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue">Ratio</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Delivery Note Pending</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue">Total</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue">Ratio</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Delivery Tomorrow</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue">Grand Total</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue">Ratio</th></tr></thead>';
                            $details .= '<tbody>';

                            $total_delivered = 0;
                            $total_delivery_unsuccessful = 0;
                            $total_on_hold = 0;
                            $total_status_not_attempted = 0;
                            $total_fake_status = 0;
                            $total_confirmation_pending = 0;
                            $total_delivery_note_pending = 0;
                            $total_delivery_tomorrow = 0;
                            $total_total_1 = 0;
                            $total_total_1_ratio = 0;
                            $total_total_2 = 0;
                            $total_total_2_ratio = 0;
                            $total_grand_total = 0;
                            $total_grand_total_ratio = 0;
                            foreach ($debriefings as $debriefing) {
                                $details .= '<tr>';
                                $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold">' . $debriefing->city->name . '</td>';
                                $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $debriefing->delivered . '</td>';
                                $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $debriefing->delivery_unsuccessful . '</td>';
                                $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $debriefing->on_hold . '</td>';
                                $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: red">' . $debriefing->status_not_attempted . '</td>';
                                $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: red">' . $debriefing->fake_status . '</td>';
                                $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $debriefing->confirmation_pending . '</td>';
                                $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold">' . $debriefing->total_1 . '</td>';
                                $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold">' . $debriefing->total_1_ratio . '</td>';
                                $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $debriefing->delivery_note_pending . '</td>';
                                $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold">' . $debriefing->total_2 . '</td>';
                                $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold">' . $debriefing->total_2_ratio . '</td>';
                                $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $debriefing->delivery_tomorrow . '</td>';
                                $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold">' . $debriefing->grand_total . '</td>';
                                $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold">' . $debriefing->grand_total_ratio . '</td>';
                                $details .= '</tr>';

                                $total_delivered = $total_delivered + $debriefing->delivered;
                                $total_delivery_unsuccessful = $total_delivery_unsuccessful + $debriefing->delivered;
                                $total_on_hold = $total_on_hold + $debriefing->delivered;
                                $total_status_not_attempted = $total_status_not_attempted + $debriefing->delivered;
                                $total_fake_status = $total_fake_status + $debriefing->fake_status;
                                $total_confirmation_pending = $total_confirmation_pending + $debriefing->confirmation_pending;
                                $total_delivery_note_pending = $total_delivery_note_pending + $debriefing->delivery_note_pending;
                                $total_delivery_tomorrow = $total_delivery_tomorrow + $debriefing->delivery_tomorrow;
                                $total_total_1 = $total_total_1 + $debriefing->total_1;
                                $total_total_2 = $total_total_2 + $debriefing->total_2;
                                $total_grand_total = $total_grand_total + $debriefing->grand_total;
                                if ($debriefing->total_1 != 0) {
                                    $total_total_1_ratio = $total_delivered / $debriefing->total_1;
                                } else {
                                    $total_total_1_ratio = 0;
                                }
                                if ($debriefing->total_2 != 0) {
                                    $total_total_2_ratio = $total_delivered / $debriefing->total_2;
                                } else {
                                    $total_total_2_ratio = 0;
                                }
                                if ($debriefing->grand_total != 0) {
                                    $total_grand_total_ratio = $total_delivered / $debriefing->grand_total;
                                } else {
                                    $total_grand_total_ratio = 0;
                                }
                            }
                            $details .= '<tr>';
                            $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold"> Total </td>';
                            $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold">' . $total_delivered . '</td>';
                            $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold">' . $total_delivery_unsuccessful . '</td>';
                            $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold">' . $total_on_hold . '</td>';
                            $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: red; font-weight: bold">' . $total_status_not_attempted . '</td>';
                            $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: red; font-weight: bold">' . $total_fake_status . '</td>';
                            $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold">' . $total_confirmation_pending . '</td>';
                            $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold">' . $total_total_1 . '</td>';
                            $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold">' . $total_total_1_ratio . '</td>';
                            $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold">' . $total_delivery_note_pending . '</td>';
                            $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold">' . $total_total_2 . '</td>';
                            $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold">' . $total_total_2_ratio . '</td>';
                            $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold">' . $total_delivery_tomorrow . '</td>';
                            $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold">' . $total_grand_total . '</td>';
                            $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold">' . $total_grand_total_ratio . '</td>';
                            $details .= '</tr>';
                            $details .= '</tbody></table>';

                            if (strpos($body, '[preview]') !== FALSE) {
                                $body = str_replace('[preview]', $details, $body);
                            }
                            $file = Storage::disk('public')->url('/reports/debriefing/zones/debriefing_report_' . $date . '_' . $zone->id . '.xlsx');

                            $link = '<br/><a href="' . $file . '" target="_blank"><u>Download</u></a>';
                            if (strpos($subject, '[link]') !== FALSE) {
                                $subject = str_replace('[link]', $link, $subject);
                            }
                            if (strpos($body, '[link]') !== FALSE) {
                                $body = str_replace('[link]', $link, $body);
                            }

                            $to = array();

                            //   $operation_admins = Admin::join('admin_hubs','admin_hubs.admin_id', '=', 'admins.id')->join('cities','cities.id','=','admin_hubs.hub_id')->whereIn('admins.role_id', [8, 9])->where('admins.status', 1)->where('cities.zone_id','=', $zone->id);
                            //   if ($operation_admins->exists()) {
                            //       $to = $operation_admins->pluck('admins.email')->toArray();
                            //   }
                            // $cc = array();

                            // $general_managers = Admin::join('admin_hubs','admin_hubs.admin_id', '=', 'admins.id')->whereIn('role_id', [3, 6, 8, 15, 18, 19, 20, 34])->where('admins.status', 1)->where('admin_hubs.hub_id','=', $zone->id);

                            // if ($general_managers->exists()) {
                            //     $cc = $general_managers->pluck('admins.email')->toArray();
                            // }

                            $to[] = 'uzair.anees@trax.pk';
                            $admins = Admin::whereIn('role_id', [31])->where('status', 1)->whereNotNull('email');

                            if ($admins->exists()) {
                                $to = array_merge($to, $admins->pluck('email')->toArray());
                            }
                            $cc = ['shahbaz.abbasi@trax.pk'];

                            self::email($subject, $body, $to, $cc);

                            $subject = $original_subject;
                            $body = $original_body;
                        }
                    }
                } else if ($id == 46) {
                    $date = $reference_1_id;
                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $date, $subject);
                    }
                    if (strpos($body, '[date]') !== FALSE) {
                        $body = str_replace('[date]', $date, $body);
                    }

                    $debriefings = Debriefing::get();

                    $details = '<table style="width:100%;">';
                    $details .= '<thead><tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue">Hub</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Delivered</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Delivery Unsuccessful</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">On Hold</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse; color: red">Status Not Attempted</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse; color: red">Fake Status</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Confirmation Pending</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue">Total</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue">Ratio</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Delivery Note Pending</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue">Total</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue">Ratio</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Delivery Tomorrow</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue">Grand Total</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue">Ratio</th></tr></thead>';
                    $details .= '<tbody>';

                    $total_delivered = 0;
                    $total_delivery_unsuccessful = 0;
                    $total_on_hold = 0;
                    $total_status_not_attempted = 0;
                    $total_fake_status = 0;
                    $total_confirmation_pending = 0;
                    $total_delivery_note_pending = 0;
                    $total_delivery_tomorrow = 0;
                    $total_total_1 = 0;
                    $total_total_1_ratio = 0;
                    $total_total_2 = 0;
                    $total_total_2_ratio = 0;
                    $total_grand_total = 0;
                    $total_grand_total_ratio = 0;
                    foreach ($debriefings as $debriefing) {
                        $details .= '<tr>';
                        $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold">' . $debriefing->city->name . '</td>';
                        $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;e">' . $debriefing->delivered . '</td>';
                        $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $debriefing->delivery_unsuccessful . '</td>';
                        $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $debriefing->on_hold . '</td>';
                        $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: red">' . $debriefing->status_not_attempted . '</td>';
                        $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: red">' . $debriefing->fake_status . '</td>';
                        $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $debriefing->confirmation_pending . '</td>';
                        $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold">' . $debriefing->total_1 . '</td>';
                        $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold">' . $debriefing->total_1_ratio . '</td>';
                        $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $debriefing->delivery_note_pending . '</td>';
                        $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold">' . $debriefing->total_2 . '</td>';
                        $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold">' . $debriefing->total_2_ratio . '</td>';
                        $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $debriefing->delivery_tomorrow . '</td>';
                        $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold">' . $debriefing->grand_total . '</td>';
                        $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold">' . $debriefing->grand_total_ratio . '</td>';
                        $details .= '</tr>';

                        $total_delivered = $total_delivered + $debriefing->delivered;
                        $total_delivery_unsuccessful = $total_delivery_unsuccessful + $debriefing->delivered;
                        $total_on_hold = $total_on_hold + $debriefing->delivered;
                        $total_status_not_attempted = $total_status_not_attempted + $debriefing->delivered;
                        $total_fake_status = $total_fake_status + $debriefing->fake_status;
                        $total_confirmation_pending = $total_confirmation_pending + $debriefing->confirmation_pending;
                        $total_delivery_note_pending = $total_delivery_note_pending + $debriefing->delivery_note_pending;
                        $total_delivery_tomorrow = $total_delivery_tomorrow + $debriefing->delivery_tomorrow;
                        $total_total_1 = $total_total_1 + $debriefing->total_1;
                        $total_total_2 = $total_total_2 + $debriefing->total_2;
                        $total_grand_total = $total_grand_total + $debriefing->grand_total;
                    }
                    if ($debriefing->total_1 != 0) {
                        $total_total_1_ratio = $total_delivered / $debriefing->total_1;
                    } else {
                        $total_total_1_ratio = 0;
                    }
                    if ($debriefing->total_2 != 0) {
                        $total_total_2_ratio = $total_delivered / $debriefing->total_2;
                    } else {
                        $total_total_2_ratio = 0;
                    }
                    if ($debriefing->grand_total != 0) {
                        $total_grand_total_ratio = $total_delivered / $debriefing->grand_total;
                    } else {
                        $total_grand_total_ratio = 0;
                    }
                    $details .= '<tr>';
                    $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold"> Total </td>';
                    $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold">' . $total_delivered . '</td>';
                    $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold">' . $total_delivery_unsuccessful . '</td>';
                    $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold">' . $total_on_hold . '</td>';
                    $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: red; font-weight: bold">' . $total_status_not_attempted . '</td>';
                    $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: red; font-weight: bold">' . $total_fake_status . '</td>';
                    $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold">' . $total_confirmation_pending . '</td>';
                    $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold">' . $total_total_1 . '</td>';
                    $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold">' . $total_total_1_ratio . '</td>';
                    $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold">' . $total_delivery_note_pending . '</td>';
                    $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold">' . $total_total_2 . '</td>';
                    $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold">' . $total_total_2_ratio . '</td>';
                    $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold">' . $total_delivery_tomorrow . '</td>';
                    $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold">' . $total_grand_total . '</td>';
                    $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold">' . $total_grand_total_ratio . '</td>';
                    $details .= '</tr>';
                    $details .= '</tbody></table>';

                    if (strpos($body, '[preview]') !== FALSE) {
                        $body = str_replace('[preview]', $details, $body);
                    }

                    $file = Storage::disk('public')->url('/reports/debriefing/overall/debriefing_report_' . $date . '.xlsx');
                    $link = '<br/><a href="' . $file . '" target="_blank"><u>Download</u></a>';
                    if (strpos($subject, '[link]') !== FALSE) {
                        $subject = str_replace('[link]', $link, $subject);
                    }
                    if (strpos($body, '[link]') !== FALSE) {
                        $body = str_replace('[link]', $link, $body);
                    }
                    // $to = 'hassan@trax.pk';
                    // $cc = array();

                    // $department_heads = Admin::whereIn('role_id', [2, 3, 4, 6, 15, 18, 19, 22, 34, 36])->where('status', 1);

                    // if ($department_heads->exists()) {
                    //   $cc = array_merge($cc, $department_heads->pluck('email')->toArray());
                    // }

                    // self::email($subject, $body, $to, $cc);

                    $to = 'uzair.anees@trax.pk';
                    $cc = ['shahbaz.abbasi@trax.pk'];

                    self::email($subject, $body, $to, $cc);
                } else if ($id == 47) {
                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $reference_1_id, $subject);
                    }

                    if (strpos($body, '[date]') !== FALSE) {
                        $body = str_replace('[date]', $reference_1_id, $body);
                    }
                    //                $file = storage_path($reference_2_id);

                    $link = '<a href="' . $reference_2_id . '" target="_blank">Report</a>';

                    if (strpos($subject, '[link]') !== FALSE) {
                        $subject = str_replace('[link]', $link, $subject);
                    }

                    if (strpos($body, '[link]') !== FALSE) {
                        $body = str_replace('[link]', $link, $body);
                    }
                    $date = Carbon::today()->startOfDay()->toDateTimeString();
                    $date_end = Carbon::today()->endOfDay()->toDateTimeString();
                    $sale_person_number_data = SalePersonNumbers::whereBetween('created_at', [$date, $date_end])->orderBy('shipments', 'desc')->get();
                    $html = '<table><thead><tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>S No.</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Admin</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Achieved Shipments</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Target Shipments</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Target Achieved %</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Achieved Revenue</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Target Revenue</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Target Achieved %</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Avg Revenue/Parcel</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Contribution</strong></th></tr></thead><tbody>';
                    $serial = 1;
                    $shipments_count = 0;
                    $revenue_count = 0;
                    $avg_revenue_count = 0;
                    $contribution_count = 0;
                    $total_target_shipments = 0;
                    $total_target_shipments_achieved = 0;
                    $total_target_revenue = 0;
                    $total_target_revenue_achieved = 0;
                    $sum_total_target_revenue_achieved = 0;
                    $total_target_revenue_avg = 0;
                    foreach ($sale_person_number_data as $sale_person_number) {
                        $all_shipments_target_revenue = 0;
                        $target_shipments_achieved = 0;
                        $target_revenue_achieved = 0;
                        $target_shipments = $sale_person_number->target_shipments;
                        if ($target_shipments > 0) {
                            $target_shipments_achieved = ($sale_person_number->shipments / $target_shipments) * 100;
                        }
                        $target_revenue = $sale_person_number->target_revenue;
                        if ($target_revenue > 0) {
                            $all_shipments_target_revenue = $target_revenue * $target_shipments;
                            $total_target_revenue_avg += $all_shipments_target_revenue;
                            if ($all_shipments_target_revenue > 0) {
                                $target_revenue_achieved = ($sale_person_number->revenue / $all_shipments_target_revenue) * 100;
                            } else {
                                $target_revenue_achieved = 0;
                            }
                        }
                        $html .= '<tr>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial . '</td>';
                        if ($sale_person_number->admin_id == 0) {
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">Walk-In</td>';
                        } else {
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $sale_person_number->sales_person->name . '</td>';
                        }
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format($sale_person_number->shipments) . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; background-color:#FFE699;">' . number_format($sale_person_number->target_shipments) . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; background-color:#C7E0B4;">' . number_format($target_shipments_achieved) . '%</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format(round($sale_person_number->revenue)) . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; background-color:#FFE699;">' . number_format($all_shipments_target_revenue) . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; background-color:#C7E0B4;">' . number_format($target_revenue_achieved) . '%</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format(round($sale_person_number->avg_revenue)) . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format($sale_person_number->contribution, 2, '.', '') . '%</td>';
                        $html .= '</tr>';
                        $shipments_count = $shipments_count + $sale_person_number->shipments;
                        $revenue_count = $revenue_count + $sale_person_number->revenue;
                        $contribution_count = $contribution_count + $sale_person_number->contribution;
                        $total_target_shipments += $sale_person_number->target_shipments;
                        $total_target_revenue += $sale_person_number->target_revenue;
                        $sum_total_target_revenue_achieved += $target_revenue_achieved;
                        $serial++;
                    }
                    if ($total_target_shipments > 0) {
                        $total_target_shipments_achieved = ($shipments_count / $total_target_shipments) * 100;
                    }
                    $total_all_shipments_target_revenue = 0;
                    if ($total_target_revenue_avg > 0) {
                        $total_target_revenue_achieved = ($revenue_count / $total_target_revenue_avg) * 100;
                    }
                    if ($shipments_count != 0) {
                        $avg_revenue_count = $revenue_count / $shipments_count;
                    } else {
                        $avg_revenue_count = 0;
                    }
                    $html .= '<tr>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">Total</td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"></td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format($shipments_count) . '</td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; background-color:#FFE699;">' . number_format($total_target_shipments) . '</td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; background-color:#C7E0B4;">' . number_format($total_target_shipments_achieved) . '%</td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format(round($revenue_count)) . '</td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; background-color:#FFE699;">' . number_format(round($total_target_revenue_avg)) . '</td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; background-color:#C7E0B4;">' . number_format($total_target_revenue_achieved) . '%</td>';

                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format(round($avg_revenue_count)) . '</td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . ceil($contribution_count) . '%</td>';
                    $html .= '</tr>';

                    $html .= '</tr>';
                    $html .= '</tbody></table>';

                    if (strpos($body, '[preview]') !== FALSE) {
                        $body = str_replace('[preview]', $html, $body);
                    }

                    $to = array();
                    /*$cc = array();

                    $admins = Admin::whereIn('role_id', [2, 3, 4, 20, 31])->where('status', 1);

                    if ($admins->exists()) {
                        $to = array_merge($to, $admins->pluck('email')->toArray());
                    }

                    $admins = Admin::join('admin_roles', 'admins.role_id', '=', 'admin_roles.id')->where('admin_roles.department_id', 7)->where('admins.status', 1);

                    if ($admins->exists()) {
                        $to = array_merge($to, $admins->pluck('admins.email')->toArray());
                    }

                    $ceo = Admin::find(8);

                    if ($ceo) {
                        $to[] = $ceo->email;
                    }

                    $extra_admins = ['rahat.ali@trax.pk'];

                    $to = array_merge($to, $extra_admins);*/


                    $to = ['mohsin.ali@trax.pk', 'waqas@trax.pk', 'khan.usama@trax.pk', 'hassan@trax.pk', 'noman.aziz@trax.pk', 'fawad.ahmed@trax.pk', 'nadir.qureshi@trax.pk'];

                    $cc = array();
                    $bcc = array();
                    $bcc = ['muhammad.waqas@trax.pk'];
                    self::email($subject, $body, $to, $cc, $bcc);
                } else if ($id == 48) {

                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $reference_1_id, $subject);
                    }

                    if (strpos($body, '[date]') !== FALSE) {
                        $body = str_replace('[date]', $reference_1_id, $body);
                    }
                    //                $file = storage_path($reference_2_id);

                    $link = '<a href="' . $reference_2_id . '" target="_blank">Report</a>';

                    if (strpos($subject, '[link]') !== FALSE) {
                        $subject = str_replace('[link]', $link, $subject);
                    }

                    if (strpos($body, '[link]') !== FALSE) {
                        $body = str_replace('[link]', $link, $body);
                    }
                    $date = Carbon::today()->startOfDay()->toDateTimeString();
                    $date_end = Carbon::today()->endOfDay()->toDateTimeString();
                    $hub_wise_split_data = HubWiseSplit::whereBetween('created_at', [$date, $date_end])->orderBy('shipments', 'desc')->get();
                    $html = '<table><thead><tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>S No.</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Origin</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Hub</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Count of Parcels</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Ratio</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Actual Weight</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Avg Actual Weight/Shipment</strong></th></tr></thead><tbody>';
                    $serial = 1;
                    $shipments_count = 0;
                    $ratio_count = 0;
                    $actual_weight_count = 0;
                    $avg_actual_weight_count = 0;
                    foreach ($hub_wise_split_data as $hub_wise_split) {
                        $html .= '<tr>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $hub_wise_split->origin_city->name . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $hub_wise_split->city->name . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format($hub_wise_split->shipments) . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $hub_wise_split->ratio . '%</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format((float) $hub_wise_split->actual_weight, 2, '.', '') . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format((float) $hub_wise_split->avg_actual_weight, 2, '.', '') . '</td>';
                        $html .= '</tr>';
                        $shipments_count = $shipments_count + $hub_wise_split->shipments;
                        $ratio_count = $ratio_count + $hub_wise_split->ratio;
                        $actual_weight_count = $actual_weight_count + $hub_wise_split->actual_weight;
                        $serial++;
                    }
                    if ($shipments_count <= 0) {
                        $avg_actual_weight_count = 0;
                    } else {
                        $avg_actual_weight_count = $actual_weight_count / $shipments_count;
                    }
                    $html .= '<tr>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">Total</td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"></td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"></td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format($shipments_count) . '</td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . ceil($ratio_count) . '%</td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format((float) $actual_weight_count, 2, '.', '') . '</td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format((float) $avg_actual_weight_count, 2, '.', '') . '</td>';
                    $html .= '</tr>';

                    $html .= '</tr>';
                    $html .= '</tbody></table>';

                    if (strpos($body, '[preview]') !== FALSE) {
                        $body = str_replace('[preview]', $html, $body);
                    }

                    $to = array();

                    $cc = array();
                    $admins = Admin::whereIn('role_id', [2, 3, 4, 20, 31, 8, 9, 10, 22, 25, 30, 46])->where('status', 1);

                    if ($admins->exists()) {
                        $to = $admins->pluck('email')->toArray();
                    }

                    $admins = Admin::join('admin_roles', 'admins.role_id', '=', 'admin_roles.id')->where('admin_roles.department_id', 7)->where('admins.status', 1);

                    if ($admins->exists()) {
                        $to = array_merge($to, $admins->pluck('admins.email')->toArray());
                    }

                    $ceo = Admin::find(8);
                    if ($ceo) {
                        array_push($to, $ceo->email);
                    }
                    $extra_admins = ['syed.sharique@trax.pk'];

                    $to = array_merge($to, $extra_admins);

                    $to[] = 'muhammad.waqas@trax.pk';
                    foreach ($to as $email) {
                        self::email($subject, $body, $email);
                    }
                } else if ($id == 49) {
                    $reference_1_id = Carbon::parse($reference_1_id)->subDay()->toDateString();
                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $reference_1_id, $subject);
                    }

                    if (strpos($body, '[date]') !== FALSE) {
                        $body = str_replace('[date]', $reference_1_id, $body);
                    }
                    //                $file = storage_path($reference_2_id);

                    $link = '<a href="' . $reference_2_id . '" target="_blank">Report</a>';

                    if (strpos($subject, '[link]') !== FALSE) {
                        $subject = str_replace('[link]', $link, $subject);
                    }

                    if (strpos($body, '[link]') !== FALSE) {
                        $body = str_replace('[link]', $link, $body);
                    }
                    $date = Carbon::today()->startOfDay()->toDateTimeString();
                    $date_end = Carbon::today()->endOfDay()->toDateTimeString();
                    $month_average_data = MonthAverage::whereBetween('created_at', [$date, $date_end])->orderBy('shipments', 'desc')->get();
                    $html = '<table><thead><tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>S No.</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Origin</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Total Parcel</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Revenue</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Avg Revenue/Parcel</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Avg Shipments/Day</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Avg Revenue/Day</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Month Speed</strong></th></tr></thead><tbody>';
                    $serial = 1;
                    $shipments_count = 0;
                    $revenue_count = 0;
                    $avg_revenue_count = 0;
                    $avg_shipments_count = 0;
                    $avg_revenue_per_day_count = 0;
                    $month_speed_count = 0;
                    foreach ($month_average_data as $month_average) {
                        $html .= '<tr>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $month_average->city->name . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format($month_average->shipments) . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format(round($month_average->revenue)) . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format(round($month_average->avg_revenue)) . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format(round($month_average->avg_shipments)) . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format(round($month_average->avg_revenue_per_day)) . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format(round($month_average->month_speed)) . '</td>';
                        $html .= '</tr>';
                        $shipments_count = $shipments_count + $month_average->shipments;
                        $revenue_count = $revenue_count + $month_average->revenue;
                        $avg_shipments_count = $avg_shipments_count + $month_average->avg_shipments;
                        $avg_revenue_per_day_count = $avg_revenue_per_day_count + $month_average->avg_revenue_per_day;
                        $month_speed_count = $month_speed_count + $month_average->month_speed;
                        $serial++;
                    }
                    if ($shipments_count != 0) {
                        $avg_revenue_count = $revenue_count / $shipments_count;
                    } else {
                        $avg_revenue_count = 0;
                    }
                    $html .= '<tr>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">Total</td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"></td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format($shipments_count) . '</td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format(round($revenue_count)) . '</td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format(round($avg_revenue_count)) . '</td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format(round($avg_shipments_count)) . '</td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format(round($avg_revenue_per_day_count)) . '</td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format(round($month_speed_count)) . '</td>';
                    $html .= '</tr>';

                    $html .= '</tr>';
                    $html .= '</tbody></table>';

                    if (strpos($body, '[preview]') !== FALSE) {
                        $body = str_replace('[preview]', $html, $body);
                    }

                    $to = array();

                    //                    $admins = Admin::whereIn('role_id', [2, 3, 4, 20, 22, 31])->where('status', 1);
                    //
                    //                    if ($admins->exists()) {
                    //                        $to = array_merge($to, $admins->pluck('email')->toArray());
                    //                    }
                    //
                    //                    $admins = Admin::join('admin_roles', 'admins.role_id', '=', 'admin_roles.id')->where('admin_roles.department_id', 7)->where('admins.status', 1);
                    //
                    //                    if ($admins->exists()) {
                    //                        $to = array_merge($to, $admins->pluck('admins.email')->toArray());
                    //                    }
                    //
                    //                    $ceo = Admin::find(8);
                    //
                    //                    $cc = array();
                    //                    $cc = [$ceo->email, 'asad@trax.pk', 'fawwad.haider@trax.pk'];
                    //
                    //                    $extra_admins = ['rahat.ali@trax.pk'];
                    //                    $to = array_merge($to, $extra_admins);

                    $to = ['mohsin.ali@trax.pk', 'waqas@trax.pk', 'khan.usama@trax.pk', 'hassan@trax.pk', 'noman.aziz@trax.pk', 'asad@trax.pk', 'fawad.ahmed@trax.pk', 'nadir.qureshi@trax.pk'];
                    $cc = array();
                    $bcc = array();
                    $bcc = ['muhammad.waqas@trax.pk'];
                    self::email($subject, $body, $to, $cc, $bcc);

                    //                 $to = array();
                    //                 $cc = array();
                    //
                    //                 $admins = Admin::whereIn('id', [36, 7])->where('status', 1);
                    //
                    //                 if ($admins->exists()) {
                    //                     $to = array_merge($to, $admins->pluck('email')->toArray());
                    //                 }
                    //
                    //                 self::email($subject, $body, $to);
                } else if ($id == 50) {
                    $done = "Done";
                    $not_done = "Not Done";
                    // $pickup_note = PickupNote::find($reference_1_id);
                    $pickup_request = V2PickupRequest::find($reference_2_id);
                    $vendor = $pickup_request->pickup_address->vendor;
                    $shipper_name = $pickup_request->shipper->name;
                    $contact_person = $pickup_request->pickup_address->poc;
                    if ($pickup_request->status == 2 || $pickup_request->status == 0) {
                        if ($pickup_request->status == 2) {
                            if (strpos($subject, '[status]') !== FALSE) {
                                $subject = str_replace('[status]', $done, $subject);
                            }

                            if (strpos($body, '[status]') !== FALSE) {
                                $body = str_replace('[status]', $done, $body);
                            }
                        } elseif ($pickup_request->status == 0) {
                            if (strpos($subject, '[status]') !== FALSE) {
                                $subject = str_replace('[status]', $not_done, $subject);
                            }

                            if (strpos($body, '[status]') !== FALSE) {
                                $body = str_replace('[status]', $not_done, $body);
                            }
                        }

                        if (strpos($subject, '[contact_person]') !== FALSE) {
                            $subject = str_replace('[contact_person]', $contact_person, $subject);
                        }

                        if (strpos($subject, '[rider]') !== FALSE) {
                            $subject = str_replace('[rider]', $pickup_request->rider->name, $subject);
                        }

                        if (strpos($subject, '[rider_phone]') !== FALSE) {
                            $subject = str_replace('[rider_phone]', $pickup_request->rider->phone, $subject);
                        }

                        if (strpos($subject, '[vendor]') !== FALSE) {
                            $subject = str_replace('[vendor]', $vendor, $subject);
                        }

                        if (strpos($subject, '[shipper_name]') !== FALSE) {
                            $subject = str_replace('[shipper_name]', $shipper_name, $subject);
                        }

                        if (strpos($body, '[contact_person]') !== FALSE) {
                            $body = str_replace('[contact_person]', $contact_person, $body);
                        }

                        if (strpos($body, '[rider]') !== FALSE) {
                            $body = str_replace('[rider]', $pickup_request->rider->name, $body);
                        }

                        if (strpos($body, '[rider_phone]') !== FALSE) {
                            $body = str_replace('[rider_phone]', $pickup_request->rider->phone, $body);
                        }

                        if (strpos($body, '[vendor]') !== FALSE) {
                            $body = str_replace('[vendor]', $vendor, $body);
                        }

                        if (strpos($body, '[shipper_name]') !== FALSE) {
                            $body = str_replace('[shipper_name]', $shipper_name, $body);
                        }
                        $to = $pickup_request->pickup_address->email;
                        self::email($subject, $body, $to);
                    }
                } else if ($id == 51) {
                    $done = "Done";
                    $not_done = "Not Done";
                    //$pickup_note = PickupNote::find($reference_1_id);
                    $pickup_request = V2PickupRequest::find($reference_2_id);
                    $shipper_name = $pickup_request->shipper->name;
                    $vendor = $pickup_request->pickup_address->vendor;
                    $contact_person = $pickup_request->pickup_address->poc;
                    if ($pickup_request->status == 2 || $pickup_request->status == 0) {
                        if ($pickup_request->status == 2) {
                            if (strpos($body, '[status]') !== FALSE) {
                                $body = str_replace('[status]', $done, $body);
                            }
                        } elseif ($pickup_request->status == 0) {
                            if (strpos($body, '[status]') !== FALSE) {
                                $body = str_replace('[status]', $not_done, $body);
                            }
                        }

                        if (strpos($body, '[contact_person]') !== FALSE) {
                            $body = str_replace('[contact_person]', $contact_person, $body);
                        }

                        if (strpos($body, '[rider]') !== FALSE) {
                            $body = str_replace('[rider]', $pickup_request->rider->name, $body);
                        }

                        if (strpos($body, '[rider_phone]') !== FALSE) {
                            $body = str_replace('[rider_phone]', $pickup_request->rider->phone, $body);
                        }

                        if (strpos($body, '[vendor]') !== FALSE) {
                            $body = str_replace('[vendor]', $vendor, $body);
                        }

                        if (strpos($body, '[shipper_name]') !== FALSE) {
                            $body = str_replace('[shipper_name]', $shipper_name, $body);
                        }
                        $to = $pickup_request->pickup_address->phone;
                        self::sms($body, $to);
                    }
                } else if ($id == 53) {
                    $date = Carbon::yesterday()->format('Y-m-d');
                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $date, $subject);
                    }

                    if (strpos($body, '[date]') !== FALSE) {
                        $body = str_replace('[date]', $date, $body);
                    }

                    $link = '<a href="' . $reference_2_id . '" target="_blank">Tracking Number(s)</a>';

                    if (strpos($subject, '[link]') !== FALSE) {
                        $subject = str_replace('[link]', $link, $subject);
                    }

                    if (strpos($body, '[link]') !== FALSE) {
                        $body = str_replace('[link]', $link, $body);
                    }
                    $date = Carbon::today()->startOfDay()->toDateTimeString();
                    $date_end = Carbon::today()->endOfDay()->toDateTimeString();
                    $daily_fake_status_datas = DailyFakeStatus::whereBetween('created_at', [$date, $date_end])
                        ->select('hub_id', 'rider_id', DB::raw('sum(total_delivery_notes) as total_delivery_notes'), DB::raw('sum(total_shipments) as total_shipments'), DB::raw('sum(total_undelivered_shipments) as total_undelivered_shipments'), DB::raw('sum(total_fake_status_shipments) as total_fake_status_shipments'))->where('hub_id', $reference_1_id)->orderBy('total_shipments', 'desc')->groupBy('rider_id')->get();
                    $html = '<table><thead><tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Row Labels.</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Count of Delivery Note No.</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Sum of Total Shipments</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Sum of Undelivered Shipments</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Sum of Shipments Marked With Fake Status</strong></th></tr></thead><tbody>';
                    $total_delivery_notes = 0;
                    $total_shipments = 0;
                    $total_undelivered_shipments = 0;
                    $total_fake_status_shipments = 0;
                    foreach ($daily_fake_status_datas as $daily_fake_status_data) {
                        $html .= '<tr>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $daily_fake_status_data->rider->name . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format($daily_fake_status_data->total_delivery_notes) . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format($daily_fake_status_data->total_shipments) . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format($daily_fake_status_data->total_undelivered_shipments) . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format($daily_fake_status_data->total_fake_status_shipments) . '</td>';
                        $html .= '</tr>';
                        $total_delivery_notes = $total_delivery_notes + $daily_fake_status_data->total_delivery_notes;
                        $total_shipments = $total_shipments + $daily_fake_status_data->total_shipments;
                        $total_undelivered_shipments = $total_undelivered_shipments + $daily_fake_status_data->total_undelivered_shipments;
                        $total_fake_status_shipments = $total_fake_status_shipments + $daily_fake_status_data->total_fake_status_shipments;
                        $hub = $daily_fake_status_data->hub->name;
                    }
                    if (strpos($subject, '[hub]') !== FALSE) {
                        $subject = str_replace('[hub]', $hub, $subject);
                    }
                    if (strpos($body, '[hub]') !== FALSE) {
                        $body = str_replace('[hub]', $hub, $body);
                    }

                    $html .= '<tr>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"><b>' . $hub . '</b></td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"><b>' . number_format($total_delivery_notes) . '</b></td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"><b>' . number_format($total_shipments) . '</b></td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"><b>' . number_format($total_undelivered_shipments) . '</b></td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"><b>' . number_format($total_fake_status_shipments) . '</b></td>';
                    $html .= '</tr>';

                    $html .= '</tr>';
                    $html .= '</tbody></table>';

                    if (strpos($body, '[preview]') !== FALSE) {
                        $body = str_replace('[preview]', $html, $body);
                    }

                    $to = array();
                    $cc = array();

                    $admins = Admin::leftjoin('admin_hubs as ah', 'ah.admin_id', '=', 'admins.id')->whereIn('role_id', [10])->where('status', 1)->where('ah.hub_id', $reference_1_id);
                    $cc_admins = Admin::leftjoin('admin_hubs as ah', 'ah.admin_id', '=', 'admins.id')->whereIn('role_id', [9, 25])->where('status', 1)->where('ah.hub_id', $reference_1_id);

                    if ($admins->exists()) {
                        $to = array_merge($to, $admins->pluck('email')->toArray());
                    }
                    if ($cc_admins->exists()) {
                        $cc = array_merge($cc, $cc_admins->pluck('email')->toArray());
                    }

                    self::email($subject, $body, $to, $cc);
                } else if ($id == 54) {
                    $date = Carbon::yesterday()->format('Y-m-d');
                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $date, $subject);
                    }

                    if (strpos($body, '[date]') !== FALSE) {
                        $body = str_replace('[date]', $date, $body);
                    }

                    $link = '<a href="' . $reference_2_id . '" target="_blank">Tracking Number(s)</a>';

                    if (strpos($subject, '[link]') !== FALSE) {
                        $subject = str_replace('[link]', $link, $subject);
                    }

                    if (strpos($body, '[link]') !== FALSE) {
                        $body = str_replace('[link]', $link, $body);
                    }
                    $date = Carbon::today()->startOfDay()->toDateTimeString();
                    $date_end = Carbon::today()->endOfDay()->toDateTimeString();
                    $daily_fake_status_data_zones = DailyFakeStatus::whereBetween('created_at', [$date, $date_end])
                        ->select('hub_id', 'zone_id', 'rider_id', DB::raw('sum(total_delivery_notes) as total_delivery_notes'), DB::raw('sum(total_shipments) as total_shipments'), DB::raw('sum(total_undelivered_shipments) as total_undelivered_shipments'), DB::raw('sum(total_fake_status_shipments) as total_fake_status_shipments'))->where('zone_id', $reference_1_id)->orderBy('total_shipments', 'desc')->groupBy('hub_id')->get();
                    $html = '<table><thead><tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Row Labels.</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Count of Delivery Note No.</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Sum of Total Shipments</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Sum of Undelivered Shipments</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Sum of Shipments Marked With Fake Status</strong></th></tr></thead><tbody>';

                    foreach ($daily_fake_status_data_zones as $daily_fake_status_data_zone) {
                        $total_delivery_notes = 0;
                        $total_shipments = 0;
                        $total_undelivered_shipments = 0;
                        $total_fake_status_shipments = 0;
                        $daily_fake_status_datas = DailyFakeStatus::whereBetween('created_at', [$date, $date_end])
                            ->select('zone_id', 'hub_id', 'rider_id', DB::raw('sum(total_delivery_notes) as total_delivery_notes'), DB::raw('sum(total_shipments) as total_shipments'), DB::raw('sum(total_undelivered_shipments) as total_undelivered_shipments'), DB::raw('sum(total_fake_status_shipments) as total_fake_status_shipments'))->where('hub_id', $daily_fake_status_data_zone->hub_id)->orderBy('total_shipments', 'desc')->groupBy('rider_id')->get();
                        foreach ($daily_fake_status_datas as $daily_fake_status_data) {
                            $html .= '<tr>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $daily_fake_status_data->rider->name . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format($daily_fake_status_data->total_delivery_notes) . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format($daily_fake_status_data->total_shipments) . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format($daily_fake_status_data->total_undelivered_shipments) . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format($daily_fake_status_data->total_fake_status_shipments) . '</td>';
                            $html .= '</tr>';
                            $total_delivery_notes = $total_delivery_notes + $daily_fake_status_data->total_delivery_notes;
                            $total_shipments = $total_shipments + $daily_fake_status_data->total_shipments;
                            $total_undelivered_shipments = $total_undelivered_shipments + $daily_fake_status_data->total_undelivered_shipments;
                            $total_fake_status_shipments = $total_fake_status_shipments + $daily_fake_status_data->total_fake_status_shipments;
                            $hub = $daily_fake_status_data->hub->name;
                        }

                        $html .= '<tr>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"><b>' . $hub . '</b></td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"><b>' . number_format($total_delivery_notes) . '</b></td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"><b>' . number_format($total_shipments) . '</b></td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"><b>' . number_format($total_undelivered_shipments) . '</b></td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"><b>' . number_format($total_fake_status_shipments) . '</b></td>';
                        $html .= '</tr>';
                        $zone = $daily_fake_status_data->zone->name;
                    }

                    $html .= '</tr>';
                    $html .= '</tbody></table>';


                    if (strpos($subject, '[zone]') !== FALSE) {
                        $subject = str_replace('[zone]', $zone, $subject);
                    }
                    if (strpos($body, '[zone]') !== FALSE) {
                        $body = str_replace('[zone]', $zone, $body);
                    }
                    if (strpos($body, '[preview]') !== FALSE) {
                        $body = str_replace('[preview]', $html, $body);
                    }

                    $to = array();
                    $cc = array();

                    $admins = Admin::leftjoin('admin_hubs as ah', 'ah.admin_id', '=', 'admins.id')->leftjoin('cities as c', 'c.id', '=', 'ah.hub_id')->whereIn('role_id', [9])->where('admins.status', 1)->where('c.zone_id', $reference_1_id);
                    $cc_admins = Admin::leftjoin('admin_hubs as ah', 'ah.admin_id', '=', 'admins.id')->leftjoin('cities as c', 'c.id', '=', 'ah.hub_id')->whereIn('role_id', [8])->where('admins.status', 1)->where('c.zone_id', $reference_1_id);
                    if ($admins->exists()) {
                        $to = array_merge($to, $admins->distinct('id')->pluck('email')->toArray());
                    }
                    if ($cc_admins->exists()) {
                        $cc = array_merge($cc, $cc_admins->distinct('id')->pluck('email')->toArray());
                    }

                    self::email($subject, $body, $to, $cc);
                } else if ($id == 55) {

                    $date = Carbon::yesterday()->format('Y-m-d');
                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $date, $subject);
                    }

                    if (strpos($body, '[date]') !== FALSE) {
                        $body = str_replace('[date]', $date, $body);
                    }

                    $link = '<a href="' . $reference_2_id . '" target="_blank">Tracking Number(s)</a>';

                    if (strpos($subject, '[link]') !== FALSE) {
                        $subject = str_replace('[link]', $link, $subject);
                    }

                    if (strpos($body, '[link]') !== FALSE) {
                        $body = str_replace('[link]', $link, $body);
                    }
                    $date = Carbon::today()->startOfDay()->toDateTimeString();
                    $date_end = Carbon::today()->endOfDay()->toDateTimeString();
                    $daily_fake_status_datas = DailyFakeStatus::whereBetween('created_at', [$date, $date_end])
                        ->select('hub_id', 'zone_id', 'rider_id', DB::raw('sum(total_delivery_notes) as total_delivery_notes'), DB::raw('sum(total_shipments) as total_shipments'), DB::raw('sum(total_undelivered_shipments) as total_undelivered_shipments'), DB::raw('sum(total_fake_status_shipments) as total_fake_status_shipments'))->orderBy('total_shipments', 'desc')->groupBy('hub_id')->get();
                    $html = '<table><thead><tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Row Labels.</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Count of Delivery Note No.</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Sum of Total Shipments</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Sum of Undelivered Shipments</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Sum of Shipments Marked With Fake Status</strong></th></tr></thead><tbody>';
                    $total_delivery_notes = 0;
                    $total_shipments = 0;
                    $total_undelivered_shipments = 0;
                    $total_fake_status_shipments = 0;

                    foreach ($daily_fake_status_datas as $daily_fake_status_data) {
                        $hub = $daily_fake_status_data->hub->name;
                        $html .= '<tr>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $hub . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format($daily_fake_status_data->total_delivery_notes) . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format($daily_fake_status_data->total_shipments) . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format($daily_fake_status_data->total_undelivered_shipments) . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format($daily_fake_status_data->total_fake_status_shipments) . '</td>';
                        $html .= '</tr>';
                        $total_delivery_notes = $total_delivery_notes + $daily_fake_status_data->total_delivery_notes;
                        $total_shipments = $total_shipments + $daily_fake_status_data->total_shipments;
                        $total_undelivered_shipments = $total_undelivered_shipments + $daily_fake_status_data->total_undelivered_shipments;
                        $total_fake_status_shipments = $total_fake_status_shipments + $daily_fake_status_data->total_fake_status_shipments;
                    }
                    $html .= '<tr>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"><b>Total</b></td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"><b>' . number_format($total_delivery_notes) . '</b></td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"><b>' . number_format($total_shipments) . '</b></td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"><b>' . number_format($total_undelivered_shipments) . '</b></td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"><b>' . number_format($total_fake_status_shipments) . '</b></td>';
                    $html .= '</tr>';

                    $html .= '</tr>';
                    $html .= '</tbody></table>';
                    if (strpos($body, '[preview]') !== FALSE) {
                        $body = str_replace('[preview]', $html, $body);
                    }

                    $to = array();
                    $cc = array();

                    $admins = Admin::leftjoin('admin_hubs as ah', 'ah.admin_id', '=', 'admins.id')->leftjoin('cities as c', 'c.id', '=', 'ah.hub_id')->whereIn('role_id', [2, 3, 4, 31])->where('admins.status', 1);


                    $cc_admins = Admin::whereIn('id', [8])->where('status', 1);
                    if ($admins->exists()) {
                        $to = array_merge($to, $admins->distinct('id')->pluck('email')->toArray());
                    }
                    if ($cc_admins->exists()) {
                        $cc = array_merge($cc, $cc_admins->distinct('id')->pluck('email')->toArray());
                    }

                    self::email($subject, $body, $to, $cc);
                } else if ($id == 56) {
                    $date = Carbon::now();
                    $from_date = $date->subDays(7)->startOfDay()->toDateTimeString();

                    $negative = DB::connection('reports')->table('pending_payment_shipments')->leftjoin('shipments as s', 's.id', '=', 'pending_payment_shipments.shipment_id')
                        ->leftjoin('users as u', 'u.id', '=', 's.user_id')
                        ->leftjoin('user_shipping_infos as usi', 'usi.id', '=', 's.pickup_address_id')
                        ->leftjoin('cities as c', 'c.id', '=', 'usi.city_id')
                        ->select('u.id as account_id', 'u.name as name', 'c.name as origin', DB::raw('SUM(pending_payment_shipments.payable) as sum_payable'), DB::raw("(select max(id) from shipments where shipments.user_id = s.user_id and shipments.created_at > '" . $from_date . "') as shipment_exist"))
                        ->havingRaw('shipment_exist is null')
                        ->groupBy('u.id')->having('sum_payable', '<', 0)
                        ->get();

                    if (count($negative) > 0) {

                        $filtered_data = array();
                        $shipper_sales_person = SalePersonTag::all()->where('admin_id', $reference_1_id)->where('status', 0)->groupBy('admin_id');
                        if (count($shipper_sales_person) > 0) {
                            foreach ($shipper_sales_person as $sale_person_id => $sale_persons) {
                                $check = false;
                                $html = '<table><thead><tr>';
                                if (strpos($body, '[account_id]') !== FALSE) {
                                    $html .= '<th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Account ID(s).</strong></th>';
                                }
                                if (strpos($body, '[shipper_name]') !== FALSE) {

                                    $html .= '<th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Shipper Name(s).</strong></th>';
                                }
                                $html .= '<th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Origin.</strong></th>';
                                $html .= '<th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Negative Balance.</strong></th>';

                                $html .= '</tr></thead><tbody>';
                                $total_payable = 0;
                                foreach ($sale_persons as $sale_person) {
                                    foreach ($negative as $data) {
                                        if ($sale_person->user_id == $data->account_id) {
                                            $html .= '<tr>';
                                            if (strpos($body, '[account_id]') !== FALSE) {
                                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data->account_id . '</td>';
                                            }
                                            if (strpos($body, '[shipper_name]') !== FALSE) {
                                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data->name . '</td>';
                                            }
                                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data->origin . '</td>';
                                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data->sum_payable . '</td>';
                                            $html .= '</tr>';
                                            $check = true;
                                            $total_payable = $total_payable + $data->sum_payable;
                                        }
                                    }
                                }
                                $html .= '<tr><td></td><td></td><td style="padding:5px; border: 1px solid black; border-collapse: collapse;">Total</td><td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $total_payable . '</td></tr>';
                                $html .= '</tbody></table>';
                                $body = str_replace('[account_id]', '', $body);
                                $body = str_replace('[shipper_name]', '', $body);
                                $admin_sale_person = Admin::find($sale_person_id);
                                if (strpos($body, '[sale_person]') !== FALSE) {
                                    $body = str_replace('[sale_person]', $admin_sale_person->name, $body);
                                }
                                if (strpos($body, '[preview]') !== FALSE) {
                                    $body = str_replace('[preview]', $html, $body);
                                }

                                $to = array();
                                $cc = array();
                                $sale_person_email = $admin_sale_person->email;
                                $to = array_merge($to, [$sale_person_email]);

                                $cc_admins = Admin::whereIn('id', [12, 32, 13, 60, 49, 174, 428])->where('status', 1);
                                if ($cc_admins->exists()) {
                                    $cc = array_merge($cc, $cc_admins->distinct('id')->pluck('email')->toArray());
                                }
                                $to[] = 'shafay.tariq@trax.pk';
                                if ($check == true) {
                                    self::email($subject, $body, $to, $cc);
                                }
                            }
                        }
                    }
                } else if ($id == 57) {
                    $shipper = User::find($reference_1_id);
                    $shipments = Shipment::where('user_id', $shipper->id);
                    $complain_date = CrmRequest::where('shipper_id', $shipper->id)->whereIn('status_id', [1, 2]);
                    $resolution_date = CrmRequest::where('shipper_id', $shipper->id)->whereIn('status_id', [3, 4]);
                    if ($shipper) {
                        //
                        $html = '<table style="width:100%;">';
                        $html .= '<thead><tr>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Account ID</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Shipper</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Origin</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Date of Registration</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">POC</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Contact</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Address</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Email ID</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Last Booking Date</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Last Pickup Date</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Last Complaint Date</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Last Complaint Resolution Date</th>';
                        $html .= '</tr></thead><tbody>';

                        $html .= '<tr>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipper->id . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipper->name . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipper->city->name . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipper->created_at . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipper->poc . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipper->phone . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipper->address . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipper->email . '</td>';
                        if ($shipments->exists()) {
                            $shipment = $shipments->latest()->first();
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment->created_at . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment->pickup_date . '</td>';
                        } else {
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">-</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">-</td>';
                        }
                        if ($complain_date->exists()) {
                            $crm = $complain_date->latest()->first();
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $crm->created_at . '</td>';
                        } else {
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">-</td>';
                        }
                        if ($resolution_date->exists()) {
                            $crm = $complain_date->latest()->first();
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $crm->created_at . '</td>';
                        } else {
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">-</td>';
                        }

                        $html .= '</tr>';
                        $html .= '</tbody></table>';

                        if (strpos($body, '[preview]') !== FALSE) {
                            $body = str_replace('[preview]', $html, $body);
                        }

                        $to = array();
                        $cc = array();
                        $sale_person_email = '';
                        $sale_person_id = SalePersonTag::where('user_id', $reference_1_id)->where('status', 0)->select('admin_id')->first();
                        if ($sale_person_id) {
                            $sale_person_email = Admin::find($sale_person_id->admin_id)->email;
                        }
                        $sale_head_email = Admin::where('role_id', 4)->select('email')->first();

                        if ($sale_person_email != '') {
                            $cc[] = $sale_person_email;
                        }

                        if ($sale_head_email) {
                            $cc[] = $sale_head_email->email;
                        }


                        $to[] = $shipper->email;

                        $city_id = $shipper->city_id;
                        $hub_id = City::find($city_id)->hub_id;

                        $managers = Admin::whereIn('role_id', [31, 39, 44])->where('status', 1)->pluck('id', 'email')->toArray();
                        foreach ($managers as $rms => $index) {
                            $admin_hubs = AdminHub::where('admin_id', $index);
                            if ($admin_hubs->exists()) {
                                $admin_hubs = $admin_hubs->pluck('hub_id')->toArray();
                                if (in_array($hub_id, $admin_hubs)) {
                                    $to[] = $rms;
                                }
                            }
                        }

                        self::email($subject, $body, $to, $cc);
                    }
                } else if ($id == 58) {
                    $shipper = User::find($reference_1_id);
                    if ($shipper) {
                        if (strpos($body, '[shipper_name]') !== FALSE) {
                            $body = str_replace('[shipper_name]', $shipper->name, $body);
                        }
                        $to = $shipper->phone;
                        self::sms($body, $to);
                    }
                } else if ($id == 59) {
                    $report_data = OvernightOverlandReportData::where('shipping_mode_id', $reference_1_id);
                    if ($report_data->exists()) {
                        $report_data = $report_data->get();
                        $date = Carbon::today()->format('Y-m-d');
                        if ($reference_1_id == 1) {
                            $shipping_mode = 'Overnight';
                        } else {
                            $shipping_mode = 'Overland';
                        }
                        if (strpos($subject, '[shipping_mode]') !== FALSE) {
                            $subject = str_replace('[shipping_mode]', $shipping_mode, $subject);
                        }
                        if (strpos($subject, '[date]') !== FALSE) {
                            $subject = str_replace('[date]', $date, $subject);
                        }
                        if (strpos($body, '[date]') !== FALSE) {
                            $body = str_replace('[date]', $date, $body);
                        }

                        $details = '<table style="width:100%;">';
                        $details .= '<thead><tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">S. No</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Cargo#</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Origin</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Destination</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">No. of Parcels</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Mode of Shipment</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Vendor</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Cargo Created Date</th></tr></thead>';
                        $serial = 1;
                        $details .= '<tbody>';
                        foreach ($report_data as $data) {
                            $details .= '<tr>';
                            if ($data->status == 1) {
                                $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: red;">' . $serial . '</td>';
                                $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: red;">' . str_pad($data->cargo_id, 6, "0", STR_PAD_LEFT) . '</td>';
                                $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: red;">' . $data->origin->name . '</td>';
                                $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: red;">' . $data->destination->name . '</td>';
                                $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: red;">' . $data->total_parcels . '</td>';
                                $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: red;">' . $data->shipping_mode->mode . '</td>';
                                $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: red;">' . $data->transport_mode_vendor->name . '</td>';
                                $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: red;">' . $data->cargo_created_at . '</td>';
                            } else {
                                $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial . '</td>';
                                $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . str_pad($data->cargo_id, 6, "0", STR_PAD_LEFT) . '</td>';
                                $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data->origin->name . '</td>';
                                $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data->destination->name . '</td>';
                                $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data->total_parcels . '</td>';
                                $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data->shipping_mode->mode . '</td>';
                                $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data->transport_mode_vendor->name . '</td>';
                                $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data->cargo_created_at . '</td>';
                            }
                            $details .= '</tr>';

                            $serial++;
                        }
                        $details .= '</tbody></table>';

                        if (strpos($body, '[preview]') !== FALSE) {
                            $body = str_replace('[preview]', $details, $body);
                        }

                        $link = '<a href="' . $reference_2_id . '" target="_blank"><u>Report</u></a>';

                        if (strpos($subject, '[link]') !== FALSE) {
                            $subject = str_replace('[link]', $link, $subject);
                        }
                        if (strpos($body, '[link]') !== FALSE) {
                            $body = str_replace('[link]', $link, $body);
                        }
                        $admins = Admin::whereIn('id', [7, 55, 37, 25])->where('status', 1);


                        //                    $cc_admins = Admin::whereIn('id', [3, 20, 8, 9]);
                        if ($admins->exists()) {
                            $to = $admins->distinct('id')->pluck('email')->toArray();
                        }
                        //                    if ($cc_admins->exists()) {
                        //                        $cc = $cc_admins->distinct('id')->pluck('email')->toArray();
                        //                    }


                        self::email($subject, $body, $to);
                        //                    $admins =Admin::whereIn('id', [8, 37])->where('status', 1);
                        //
                        //
                        //                    $cc_admins = Admin::whereIn('id', [3, 20, 8, 9]);
                        //                    if ($admins->exists()) {
                        //                        $to = $admins->distinct('id')->pluck('email')->toArray();
                        //                    }
                        //                    if ($cc_admins->exists()) {
                        //                        $cc = $cc_admins->distinct('id')->pluck('email')->toArray();
                        //                    }
                        //
                        //
                        //                    self::email($subject, $body, $to, $cc);
                    }
                } else if ($id == 60) {
                    $sale_admins = Admin::join('admin_roles', 'admins.role_id', '=', 'admin_roles.id')->where('admin_roles.department_id', 7)->where('admins.status', 1)->select('admins.id as id', 'admins.email as email')->get();
                    if ($sale_admins) {
                        foreach ($sale_admins as $sale_admin) {
                            $body = $notification->body;
                            $tagged_shippers = SalePersonTag::where('admin_id', $sale_admin->id)->where('status', 0);
                            if ($tagged_shippers->exists()) {
                                $tagged_shippers = $tagged_shippers->pluck('user_id')->toArray();
                                if (count($tagged_shippers) > 0) {
                                    if (strpos($subject, '[date]') !== FALSE) {
                                        $subject = str_replace('[date]', $reference_1_id, $subject);
                                    }
                                    if (strpos($body, '[date]') !== FALSE) {
                                        $body = str_replace('[date]', $reference_1_id, $body);
                                    }
                                    $details = '<table style="width:100%;">';
                                    $details .= '<thead><tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">S. No</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Account ID</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Shipper</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Origin</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Activated At</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Document Status</th></tr></thead>';
                                    $serial = 1;
                                    $details .= '<tbody>';
                                    $check = false;
                                    foreach ($tagged_shippers as $tagged_shipper) {
                                        $shipper = User::where('id', $tagged_shipper)->where('status', 3)->whereIn('documents_status', [0, 3])->first();
                                        if ($shipper) {
                                            if ($shipper->documents_status == 0) {
                                                $document_status = "Incomplete";
                                            } else {
                                                $document_status = "Rejected";
                                            }
                                            $details .= '<tr>';
                                            $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial . '</td>';
                                            $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . str_pad($shipper->id, 6, "0", STR_PAD_LEFT) . '</td>';
                                            $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipper->name . '</td>';
                                            $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipper->city->name . '</td>';
                                            $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipper->activated_at . '</td>';
                                            $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $document_status . '</td>';
                                            $details .= '</tr>';
                                            $serial++;
                                            $check = true;
                                        }
                                    }
                                    $details .= '</tbody></table>';
                                    if (strpos($body, '[preview]') !== FALSE) {
                                        $body = str_replace('[preview]', $details, $body);
                                    }
                                    $to = $sale_admin->email;

                                    $cc_admins = Admin::whereIn('role_id', [2, 4])->where('status', 1);
                                    if ($cc_admins->exists()) {
                                        $cc = $cc_admins->distinct('id')->pluck('email')->toArray();
                                    }
                                    if ($check == true) {
                                        self::email($subject, $body, $to, $cc);
                                    }
                                }
                            }
                        }
                    }
                } else if ($id == 61) {
                    $rider = Rider::find($reference_1_id);
                    $pin = $reference_2_id;
                    if ($rider) {
                        if (strpos($body, '[rider_name]') !== FALSE) {
                            $body = str_replace('[rider_name]', $rider->name, $body);
                        }
                        if (strpos($body, '[pin]') !== FALSE) {
                            $body = str_replace('[pin]', $pin, $body);
                        }

                        $to = $rider->phone;
                        self::sms($body, $to);
                    }
                } else if ($id == 62) {

                    $users = User::where('status', 3)->get();
                    if ($users) {
                        foreach ($users as $user) {


                            $date = Carbon::yesterday()->format('Y-m-d');
                            $shipment_cancel = ShipmentsJourney::join('shipments as s', 'shipments_journey.shipment_id', '=', 's.id')
                                ->leftjoin('shipment_items as si', 's.id', '=', 'si.shipment_id')
                                ->leftjoin('user_shipping_infos as usi', 's.pickup_address_id', '=', 'usi.id')
                                ->select('s.tracking_number as tracking_number', 's.created_at as booking_date', 's.order_id as order_id', 's.amount as cod', 'usi.vendor as vendor_name', 'si.description as desc')
                                ->where('s.user_id', $user->id)
                                ->where('shipments_journey.shipper_status_id', 17)
                                ->where('shipments_journey.created_at', '>=', $date)
                                ->where('s.warehouse', 0)
                                ->groupBy('s.id')
                                ->get();
                            if (count($shipment_cancel) > 0) {

                                $subject = $notification->subject;
                                $body = $notification->body;

                                $cancel_shipment = '<table style="width:100%;">';
                                $cancel_shipment .= '<thead><tr>
                                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Tracking #</th>
                                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Vendor Name</th>
                                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">COD Amount</th>
                                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Booking Date</th>
                                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Orde ID</th>
                                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Item Description</th></tr></thead>';
                                $cancel_shipment .= '<tbody>';


                                foreach ($shipment_cancel as $data) {
                                    $cancel_shipment .= '<tr>';
                                    $cancel_shipment .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data->tracking_number . '</td>';
                                    $cancel_shipment .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data->vendor_name . '</td>';
                                    $cancel_shipment .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data->cod . '</td>';
                                    $cancel_shipment .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data->booking_date . '</td>';
                                    $cancel_shipment .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data->order_id . '</td>';
                                    $cancel_shipment .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data->desc . '</td>';
                                    $cancel_shipment .= '</tr>';
                                }
                                $cancel_shipment .= '</tbody></table>';

                                if (strpos($body, '[cancel_shipment]') !== FALSE) {
                                    $body = str_replace('[cancel_shipment]', $cancel_shipment, $body);
                                }

                                $to = $user->email;

                                self::email($subject, $body, $to);
                            }
                        }
                    }
                } else if ($id == 63) {
                    $date = Carbon::yesterday()->format('Y-m-d');
                    $pickup_requests = V2PickupRequest::where('status_id', '=', 4)->where('updated_at', '>=', $date)->get();
                    foreach ($pickup_requests as $pickup_request) {

                        $subject = $notification->subject;
                        $body = $notification->body;

                        $pickup_request_id = $pickup_request->id;
                        $pickup_date = $pickup_request->updated_at;

                        $logo = '<img class="brand-logo trax" alt="Trax" src="' . asset('img/trax_logo_new.png') . '" width="100" height="50">';

                        if (strpos($subject, '[pickup_request_ID]') !== FALSE) {
                            $subject = str_replace('[pickup_request_ID]', $pickup_request_id, $subject);
                        }
                        if (strpos($body, '[pickup_request_ID]') !== FALSE) {
                            $body = str_replace('[pickup_request_ID]', $pickup_request_id, $body);
                        }
                        if (strpos($body, '[shipper]') !== FALSE) {
                            $body = str_replace('[shipper]', $pickup_request->shipper->name, $body);
                        }
                        if (strpos($body, '[date]') !== FALSE) {
                            $body = str_replace('[date]', $pickup_date, $body);
                        }
                        if (strpos($body, '[trax_logo]') !== FALSE) {
                            $body = str_replace('[trax_logo]', $logo, $body);
                        }

                        $to = $pickup_request->shipper->email;
                        self::email($subject, $body, $to);
                    }
                } else if ($id == 64) {

                    $to = array();
                    $cc = array();
                    $user = User::find($reference_1_id);

                    if ($user) {

                        //array_push($to,$user->email);
                        $subject = $notification->subject;
                        $body = $notification->body;
                        // $user_bank_info = UserBankInfo::where('user_id', $user->id)->first();

                        $account_id = $user->id;
                        $shipper_name = $user->name;

                        $logo = '<img class="brand-logo trax" alt="Trax" src="' . asset('img/trax_logo_new.png') . '" width="100" height="50">';

                        if (strpos($subject, '[account_id]') !== FALSE) {
                            $subject = str_replace('[account_id]', $account_id, $subject);
                        }
                        if (strpos($subject, '[name]') !== FALSE) {
                            $subject = str_replace('[name]', $shipper_name, $subject);
                        }

                        if (strpos($body, '[account_id]') !== FALSE) {
                            $body = str_replace('[account_id]', $account_id, $body);
                        }
                        if (strpos($body, '[name]') !== FALSE) {
                            $body = str_replace('[name]', $shipper_name, $body);
                        }
                        if (strpos($body, '[trax_logo]') !== FALSE) {
                            $body = str_replace('[trax_logo]', $logo, $body);
                        }

                        $admins_sales = Admin::where('role_id', 4)->where('status', 1);
                        $admins_finance = Admin::where('role_id', 2)->where('status', 1);

                        if ($admins_sales->exists()) {
                            $to = array_merge($to, $admins_sales->pluck('email')->toArray());
                        }
                        if ($admins_finance->exists()) {
                            $cc = array_merge($cc, $admins_finance->pluck('email')->toArray());
                        }

                        self::email($subject, $body, $to, $cc);
                    }
                } else if ($id == 65) {
                    $crm_request_id = str_pad($reference_1_id, 6, '0', STR_PAD_LEFT);
                    $to = $reference_2_id;
                    $crm_request = CrmRequest::find($reference_1_id);
                    $subject = $notification->subject;
                    $body = $notification->body;

                    if (strpos($subject, '[request_id]') !== FALSE) {
                        $subject = str_replace('[request_id]', $crm_request_id, $subject);
                    }
                    if (strpos($subject, '[case_nature]') !== FALSE) {
                        $subject = str_replace('[case_nature]', $crm_request->nature->name, $subject);
                    }
                    if (strpos($subject, '[case_nature_type]') !== FALSE) {
                        $subject = str_replace('[case_nature_type]', $crm_request->nature_type->type, $subject);
                    }

                    if (strpos($body, '[request_id]') !== FALSE) {
                        $body = str_replace('[request_id]', $crm_request_id, $body);
                    }
                    if (strpos($body, '[case_nature]') !== FALSE) {
                        $body = str_replace('[case_nature]', $crm_request->nature->name, $body);
                    }
                    if (strpos($body, '[case_nature_type]') !== FALSE) {
                        $body = str_replace('[case_nature_type]', $crm_request->nature_type->type, $body);
                    }

                    self::email($subject, $body, $to);
                } else if ($id == 66) {
                    $crm_request_id = str_pad($reference_1_id, 6, '0', STR_PAD_LEFT);
                    $to = $reference_2_id['to'];
                    $cc = $reference_2_id['cc'];
                    $bcc = $reference_2_id['bcc'];
                    $escalation = $reference_2_id['level'];

                    $subject = $notification->subject;
                    $body = $notification->body;

                    if (strpos($subject, '[request_id]') !== FALSE) {
                        $subject = str_replace('[request_id]', $crm_request_id, $subject);
                    }
                    if (strpos($subject, '[escalation]') !== FALSE) {
                        $subject = str_replace('[escalation]', $escalation, $subject);
                    }

                    if (strpos($body, '[request_id]') !== FALSE) {
                        $body = str_replace('[request_id]', $crm_request_id, $body);
                    }
                    if (strpos($body, '[escalation]') !== FALSE) {
                        $body = str_replace('[escalation]', $escalation, $body);
                    }

                    self::email($subject, $body, $to, $cc, $bcc);
                } else if ($id == 67) {

                    $to = array();
                    $date = \Carbon\Carbon::yesterday()->format('Y-m-d');
                    $zero_report = "";
                    $users = Shipment::join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
                        ->join('shipments_journey AS sj', 'shipments.id', '=', 'sj.shipment_id')
                        ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
                        ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
                        ->join('users AS u', 'shipments.user_id', '=', 'u.id')
                        ->select('shipments.tracking_number as tracking_number', 'u.name as shipper', 'oc.name as origin', 'dc.name as destination', 'shipments.amount as cod', 'shipments.actual_weight as actual_weight')
                        ->where('sj.shipper_status_id', 2)
                        ->where('sj.created_at', '>=', $date)
                        // ->where('shipments.amount','!=', 0)
                        // ->groupBy('shipments.id')
                        ->get();

                    if ($users) {

                        $subject = $notification->subject;
                        $body = $notification->body;
                        $zero_report = '<table style="width:100%;">';
                        $zero_report .= '<thead><tr>
                                        <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Tracking #</th>
                                        <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Shipper</th>
                                        <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Origin</th>
                                        <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Destination</th>
                                        <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">COD Amount</th>
                                        <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Actual Weight</th>
                                        <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Total Charges(w/o GST)</th></tr></thead>';
                        $zero_report .= '<tbody>';

                        foreach ($users as $user) {

                            $charges = $user->weight_charges + $user->cash_handling_charges + $user->insurance_charges + $user->return_charges + $user->fuel_surcharge + $user->replacement_charges + $user->try_and_buy_charges + $user->packaging_material_charges + $user->intercept_charges + $user->nsa_osa_charges;

                            if ($charges == 0) {
                                $zero_report .= '<tr>';
                                $zero_report .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $user->tracking_number . '</td>';
                                $zero_report .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $user->shipper . '</td>';
                                $zero_report .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $user->origin . '</td>';
                                $zero_report .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $user->destination . '</td>';
                                $zero_report .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $user->cod . '</td>';
                                $zero_report .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $user->actual_weight . '</td>';
                                $zero_report .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $charges . '</td>';
                                $zero_report .= '</tr>';
                            }
                            // $charges=0;


                        }
                        $zero_report .= '</tbody></table>';
                        if (strpos($body, '[zero_report]') !== FALSE) {
                            $body = str_replace('[zero_report]', $zero_report, $body);
                        }
                        $admins = Admin::whereIn('id', [12, 49, 60])->where('status', 1);
                        if ($admins->exists()) {
                            $to = $admins->distinct('id')->pluck('email')->toArray();
                        }
                        self::email($subject, $body, $to);
                    }
                } else if ($id == 68) {
                    $date = $reference_1_id;
                    $hub_shipment = $reference_2_id;
                    $subject = $notification->subject;
                    $body = $notification->body;

                    if (strpos($subject, '[hub]') !== FALSE) {
                        $subject = str_replace('[hub]', $hub_shipment['name'], $subject);
                    }
                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $date, $subject);
                    }
                    $html = '<table style="width:100%;">';
                    $html .= '<thead><tr>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Hub</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Zero</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">One</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Two</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Three</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Four</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Five</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Six Plus</th>';
                    $html .= '</tr></thead><tbody>';

                    $html .= '<tr>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $hub_shipment['name'] . '</td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $hub_shipment['zero'] . '</td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $hub_shipment['one'] . '</td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $hub_shipment['two'] . '</td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $hub_shipment['three'] . '</td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $hub_shipment['four'] . '</td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $hub_shipment['five'] . '</td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $hub_shipment['six_plus'] . '</td>';
                    $html .= '</tr>';
                    $html .= '</tbody></table>';

                    if (strpos($body, '[preview]') !== FALSE) {
                        $body = str_replace('[preview]', $html, $body);
                    }

                    $to = array();
                    $operation_admins = Admin::join('admin_hubs', 'admin_hubs.admin_id', '=', 'admins.id')->whereIn('admins.role_id', [9, 10])->where('admins.status', 1)->where('admin_hubs.hub_id', '=', $hub_shipment['id']);
                    if ($operation_admins->exists()) {
                        $to = array_merge($to, $operation_admins->pluck('admins.email')->toArray());
                    }
                    $cc = array();

                    $general_managers = Admin::join('admin_hubs', 'admin_hubs.admin_id', '=', 'admins.id')->whereIn('role_id', [3, 6, 8, 15, 18, 19, 20, 25, 34, 45])->where('admins.status', 1)->where('admin_hubs.hub_id', '=', $hub_shipment['id']);

                    if ($general_managers->exists()) {
                        $cc = array_merge($cc, $general_managers->pluck('admins.email')->toArray());
                    }

                    self::email($subject, $body, $to, $cc);
                } else if ($id == 69) {
                    $date = $reference_1_id;
                    $zone_hub_shipments = $reference_2_id;
                    $subject = $notification->subject;
                    $body = $notification->body;
                    $html = '<table style="width:100%;">';
                    $html .= '<thead><tr>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Hub</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Zero</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">One</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Two</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Three</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Four</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Five</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Six Plus</th>';
                    $html .= '</tr></thead><tbody>';
                    foreach ($zone_hub_shipments as $zone_hub_shipment) {
                        $html .= '<tr>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $zone_hub_shipment['name'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $zone_hub_shipment['zero'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $zone_hub_shipment['one'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $zone_hub_shipment['two'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $zone_hub_shipment['three'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $zone_hub_shipment['four'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $zone_hub_shipment['five'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $zone_hub_shipment['six_plus'] . '</td>';
                        $html .= '</tr>';
                        $zone = $zone_hub_shipment['zone'];
                        $zone_id = $zone_hub_shipment['zone_id'];
                    }
                    $html .= '</tbody></table>';

                    if (strpos($subject, '[zone]') !== FALSE) {
                        $subject = str_replace('[zone]', $zone, $subject);
                    }
                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $date, $subject);
                    }
                    if (strpos($body, '[preview]') !== FALSE) {
                        $body = str_replace('[preview]', $html, $body);
                    }

                    $to = array();

                    $operation_admins = Admin::join('admin_hubs', 'admin_hubs.admin_id', '=', 'admins.id')->join('cities', 'cities.id', '=', 'admin_hubs.hub_id')->whereIn('admins.role_id', [8, 9])->where('admins.status', 1)->where('cities.zone_id', '=', $zone_id);
                    $cc = array();

                    $general_managers = Admin::join('admin_hubs', 'admin_hubs.admin_id', '=', 'admins.id')->whereIn('role_id', [3, 6, 8, 15, 18, 19, 20, 25, 34, 45])->where('admins.status', 1)->where('admin_hubs.hub_id', '=', $zone_id);
                    if ($operation_admins->exists()) {
                        $to = array_merge($to, $operation_admins->pluck('email')->toArray());
                    }

                    if ($general_managers->exists()) {
                        $cc = array_merge($cc, $general_managers->pluck('admins.email')->toArray());
                    }

                    self::email($subject, $body, $to, $cc);
                } else if ($id == 70) {
                    $date = $reference_1_id;
                    $hub_shipments = $reference_2_id;
                    $subject = $notification->subject;
                    $body = $notification->body;
                    $html = '<table style="width:100%;">';
                    $html .= '<thead><tr>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Hub</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Zero</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">One</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Two</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Three</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Four</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Five</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Six Plus</th>';
                    $html .= '</tr></thead><tbody>';
                    foreach ($hub_shipments as $hub_shipment) {
                        $html .= '<tr>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $hub_shipment['name'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $hub_shipment['zero'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $hub_shipment['one'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $hub_shipment['two'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $hub_shipment['three'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $hub_shipment['four'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $hub_shipment['five'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $hub_shipment['six_plus'] . '</td>';
                        $html .= '</tr>';
                    }
                    $html .= '</tbody></table>';

                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $date, $subject);
                    }
                    if (strpos($body, '[preview]') !== FALSE) {
                        $body = str_replace('[preview]', $html, $body);
                    }

                    $to = 'hassan@trax.pk';
                    $cc = array();

                    $department_heads = Admin::whereIn('role_id', [2, 3, 4, 6, 15, 18, 19, 22, 25, 34, 36])->where('status', 1);

                    if ($department_heads->exists()) {
                        $cc = array_merge($cc, $department_heads->pluck('email')->toArray());
                    }

                    self::email($subject, $body, $to, $cc);
                } else if ($id == 71) {
                    $completed_agings = CompletedAgingReport::orderBy('count', 'desc')->get();

                    $subject = $notification->subject;
                    $body = $notification->body;
                    $date = Carbon::now()->toDateString();
                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $date, $subject);
                    }
                    $html = '<table style="width:100%;">';
                    $html .= '<thead><tr>
                                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Hub</th>
                                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Zone</th>
                                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Completed > 2days</th>
                                           </tr></thead><tbody>';
                    $total_count = 0;
                    foreach ($completed_agings as $completed_aging) {
                        $html .= '<tr>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $completed_aging->city->name . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $completed_aging->zone->name . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $completed_aging->count . '</td>';
                        $html .= '</tr>';
                        $total_count = $total_count + $completed_aging->count;
                    }
                    $html .= '<tr>';
                    $html .= '<td colspan="2" style="padding:5px; border: 1px solid black; border-collapse: collapse;">Total Numbers</td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $total_count . '</td>';
                    $html .= '</tr>';
                    $html .= '</tbody></table>';
                    if (strpos($body, '[preview]') !== FALSE) {
                        $body = str_replace('[preview]', $html, $body);
                    }

                    $admins = Admin::whereIn('id', [12, 49, 216])->where('status', 1);

                    if ($admins->exists()) {
                        $to = $admins->pluck('email')->toArray();
                    }

                    self::email($subject, $body, $to);
                } else if ($id == 72) {
                    $pending_cash_collection_agings = PendingCashCollectionAgingReport::orderBy('count', 'desc')->get();

                    $subject = $notification->subject;
                    $body = $notification->body;
                    $date = Carbon::now()->toDateString();
                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $date, $subject);
                    }
                    $html = '<table style="width:100%;">';
                    $html .= '<thead><tr>
                                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Hub</th>
                                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Zone</th>
                                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Completed > 2days</th>
                                           </tr></thead><tbody>';
                    $total_count = 0;
                    foreach ($pending_cash_collection_agings as $pending_cash_collection_aging) {
                        $html .= '<tr>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $pending_cash_collection_aging->city->name . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $pending_cash_collection_aging->zone->name . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $pending_cash_collection_aging->count . '</td>';
                        $html .= '</tr>';
                        $total_count = $total_count + $pending_cash_collection_aging->count;
                    }
                    $html .= '<tr>';
                    $html .= '<td colspan="2" style="padding:5px; border: 1px solid black; border-collapse: collapse;">Total Numbers</td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $total_count . '</td>';
                    $html .= '</tr>';
                    $html .= '</tbody></table>';
                    if (strpos($body, '[preview]') !== FALSE) {
                        $body = str_replace('[preview]', $html, $body);
                    }

                    $admins = Admin::whereIn('id', [12, 49, 216])->where('status', 1);

                    if ($admins->exists()) {
                        $to = $admins->pluck('email')->toArray();
                    }

                    self::email($subject, $body, $to);
                } else if ($id == 73) {

                    $tracking_numbers = $reference_1_id;
                    $pickup_request_id = $reference_2_id;
                    $pickup_request = V2PickupRequest::find($pickup_request_id);
                    if ($pickup_request) {
                        if (strpos($subject, '[pickup_request_id]') !== FALSE) {
                            $subject = str_replace('[pickup_request_id]', $pickup_request_id, $subject);
                        }

                        if (strpos($body, '[pickup_request_id]') !== FALSE) {
                            $body = str_replace('[pickup_request_id]', $pickup_request_id, $body);
                        }

                        $company_name = $pickup_request->shipper->name;

                        if (strpos($subject, '[company_name]') !== FALSE) {
                            $subject = str_replace('[company_name]', $company_name, $subject);
                        }

                        if (strpos($body, '[company_name]') !== FALSE) {
                            $body = str_replace('[company_name]', $company_name, $body);
                        }

                        $pickup_city = $pickup_request->pickup_city->name;
                        if (strpos($subject, '[pickup_city]') !== FALSE) {
                            $subject = str_replace('[pickup_city]', $pickup_city, $subject);
                        }

                        if (strpos($body, '[pickup_city]') !== FALSE) {
                            $body = str_replace('[pickup_city]', $pickup_city, $body);
                        }

                        $html = '<table><thead><tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>S No.</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Tracking Number</strong></th></tr></thead><tbody>';
                        foreach ($tracking_numbers as $row => $tracking) {
                            $row++;
                            $html .= '<tr><td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $row . '</td><td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $tracking . '</td></tr>';
                        }

                        $html .= '</tbody></table>';
                        if (strpos($body, '[tracking_numbers]') !== FALSE) {
                            $body = str_replace('[tracking_numbers]', $html, $body);
                        }

                        $to = array();
                        $bcc = array();

                        $to[] = $pickup_request->shipper->email;

                        $sales_person = SalePersonTag::where('user_id', $pickup_request->shipper_id)->where('status', 0)->first();

                        if ($sales_person) {
                            $bcc[] = Admin::find($sales_person->admin_id)->email;
                        }

                        $city_id = $pickup_request->pickup_city->hub_id;

                        $related_admins = Admin::whereIn('role_id', [10])->where('status', 1)->whereHas('hubs', function ($query) use ($city_id) {
                            $query->where('hub_id', $city_id);
                        });

                        if ($related_admins->exists()) {
                            $bcc = array_merge($bcc, $related_admins->pluck('email')->toArray());
                        }
                        if (empty($bcc)) {
                            $bcc = NULL;
                        }

                        self::email($subject, $body, $to, NULL, $bcc);
                    }
                } else if ($id == 74) {
                    $qa_report_petty_cash = QaReportPettyCash::get();

                    $subject = $notification->subject;
                    $body = $notification->body;
                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $reference_1_id, $subject);
                    }

                    if (strpos($body, '[date]') !== FALSE) {
                        $body = str_replace('[date]', $reference_1_id, $body);
                    }
                    //                $file = storage_path($reference_2_id);

                    $link = '<a href="' . $reference_2_id . '" target="_blank">Report</a>';

                    if (strpos($subject, '[link]') !== FALSE) {
                        $subject = str_replace('[link]', $link, $subject);
                    }

                    if (strpos($body, '[link]') !== FALSE) {
                        $body = str_replace('[link]', $link, $body);
                    }
                    $html = '<table style="width:100%;">';
                    $html .= '<thead><tr>
                                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Hub</th>
                                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Station Approval</th>
                                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Operation Approval</th>
                                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Finance Approval</th>
                                           </tr></thead><tbody>';
                    $total_count = 0;
                    foreach ($qa_report_petty_cash as $report_petty_cash) {
                        $html .= '<tr>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $report_petty_cash->hub->name . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $report_petty_cash->station_approval . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $report_petty_cash->operation_approval . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $report_petty_cash->finance_approval . '</td>';
                        $html .= '</tr>';
                    }
                    $html .= '</tbody></table>';
                    if (strpos($body, '[preview]') !== FALSE) {
                        $body = str_replace('[preview]', $html, $body);
                    }

                    $admins = Admin::whereIn('id', [174, 60, 12])->where('status', 1);

                    if ($admins->exists()) {
                        $to = $admins->pluck('email')->toArray();
                    }

                    self::email($subject, $body, $to);
                } else if ($id == 75) {
                    $shipment = Shipment::find($reference_1_id);
                    $address = $reference_2_id;
                    if ($shipment) {
                        if (strpos($body, '[name]') !== FALSE) {
                            $body = str_replace('[name]', $shipment->consignee_name, $body);
                        }
                        if (strpos($body, '[address]') !== FALSE) {
                            $body = str_replace('[address]', $address, $body);
                        }

                        $to = $shipment->consignee_phone_number_1;
                        self::sms($body, $to);
                        if ($shipment->consignee_phone_number_2 != NULL) {
                            $to = $shipment->consignee_phone_number_2;
                            self::sms($body, $to);
                        }
                    }
                } else if ($id == 76) {
                    if ($reference_1_id != 0) {
                        $hub = City::find($reference_1_id);
                        $hub_id = $hub->id;
                    }

                    $date = Carbon::today()->format('Y m d');
                    $subject = $notification->subject;
                    $body = $notification->body;

                    if (strpos($subject, '[hub]') !== FALSE) {
                        if ($reference_1_id != 0) {
                            $subject = str_replace('[hub]', $hub->name, $subject);
                        } else {
                            $subject = str_replace('[hub]', 'Overall', $subject);
                        }
                    }

                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $date, $subject);
                    }

                    if (strpos($body, '[date]') !== FALSE) {
                        $body = str_replace('[date]', $date, $body);
                    }

                    $link = '<a href="' . $reference_2_id . '" target="_blank">Report</a>';

                    if (strpos($body, '[link]') !== FALSE) {
                        $body = str_replace('[link]', $link, $body);
                    }

                    $to = array();
                    $cc = array();
                    $bcc = array();

                    if ($reference_1_id != 0) {
                        $to_admins = Admin::whereIn('role_id', [10, 17, 25, 30])->where('status', 1)->whereHas('hubs', function ($query) use ($hub_id) {
                            $query->where('hub_id', $hub_id);
                        });
                        if ($to_admins->exists()) {
                            $to = array_merge($to, $to_admins->pluck('email')->toArray());
                        }
                        $cc_admins = Admin::whereIn('role_id', [8, 9, 3, 2])->where('status', 1)->whereHas('hubs', function ($query) use ($hub_id) {
                            $query->where('hub_id', $hub_id);
                        });
                        if ($cc_admins->exists()) {
                            $cc = array_merge($cc, $cc_admins->pluck('email')->toArray());
                        }
                    } else {
                        $to[] = 'aamir.sohail@trax.pk';
                        $to[] = 'fawad.ahmed@trax.pk';
                        $cc[] = 'shafay.tariq@trax.pk';
                    }

                    self::email($subject, $body, $to, $cc);
                } else if ($id == 77) {
                    $rider_id = $reference_1_id;
                    $shipment_id = $reference_2_id;
                    $rider = Rider::find($rider_id);
                    if ($rider) {

                        if (strpos($body, '[rider_name]') !== FALSE) {
                            $body = str_replace('[rider_name]', $rider->name, $body);
                        }

                        if (strpos($body, '[rider_phone]') !== FALSE) {
                            $body = str_replace('[rider_phone]', $rider->phone, $body);
                        }
                        $shipment = Shipment::find($shipment_id);

                        $to = $shipment->pickup_address->phone;
                        self::sms($body, $to);
                    }
                } else if ($id == 78) {
                    $date = Carbon::today()->toDateString();
                    $hub_shipment = $reference_2_id;
                    $subject = $notification->subject;
                    $body = $notification->body;
                    $hub = City::find($reference_1_id);
                    if (strpos($subject, '[hub]') !== FALSE) {
                        $subject = str_replace('[hub]', $hub->name, $subject);
                    }
                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $date, $subject);
                    }
                    $html = '<table style="width:100%;">';
                    $html .= '<thead><tr>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">S No.</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Tracking Number</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Origin</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Hub</th>';
                    $html .= '</tr></thead><tbody>';
                    $serial = 1;
                    foreach ($hub_shipment as $origin_shipment) {
                        foreach ($origin_shipment as $index => $data) {
                            $html .= '<tr>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data['tracking_number'] . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data['origin_name'] . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data['hub_name'] . '</td>';
                            $html .= '</tr>';
                            $serial++;
                        }
                    }

                    $html .= '</tbody></table>';
                    if (strpos($body, '[preview]') !== FALSE) {
                        $body = str_replace('[preview]', $html, $body);
                    }

                    $to = array();
                    $operation_admins = Admin::join('admin_hubs', 'admin_hubs.admin_id', '=', 'admins.id')->whereIn('admins.role_id', [8, 9, 10, 25, 33])->where('admins.status', 1)->where('admin_hubs.hub_id', '=', $hub->id);
                    if ($operation_admins->exists()) {
                        $to = array_merge($to, $operation_admins->pluck('admins.email')->toArray());
                    }
                    self::email($subject, $body, $to);
                } else if ($id == 79) {
                    $date = Carbon::today()->toDateString();
                    $zone_hub_shipments = $reference_2_id;
                    $subject = $notification->subject;
                    $body = $notification->body;
                    $zone = Zone::find($reference_1_id);
                    $html = '<table style="width:100%;">';
                    $html .= '<thead><tr>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">S No.</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Tracking Number</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Origin</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Hub</th>';
                    $html .= '</tr></thead><tbody>';
                    $serial = 1;
                    foreach ($zone_hub_shipments as $zone_hub_shipment) {
                        foreach ($zone_hub_shipment as $origin_shipment) {
                            foreach ($origin_shipment as $index => $data) {
                                $html .= '<tr>';
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial . '</td>';
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data['tracking_number'] . '</td>';
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data['origin_name'] . '</td>';
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data['hub_name'] . '</td>';
                                $html .= '</tr>';
                                $serial++;
                            }
                        }
                    }
                    $html .= '</tbody></table>';
                    if (strpos($subject, '[zone]') !== FALSE) {
                        $subject = str_replace('[zone]', $zone->name, $subject);
                    }
                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $date, $subject);
                    }
                    if (strpos($body, '[preview]') !== FALSE) {
                        $body = str_replace('[preview]', $html, $body);
                    }

                    $to = array();

                    $operation_admins = Admin::join('admin_hubs', 'admin_hubs.admin_id', '=', 'admins.id')->join('cities', 'cities.id', '=', 'admin_hubs.hub_id')->whereIn('admins.role_id', [8, 9, 25])->where('admins.status', 1)->where('cities.zone_id', '=', $zone->id);
                    if ($operation_admins->exists()) {
                        $to = array_merge($to, $operation_admins->pluck('email')->toArray());
                    }

                    self::email($subject, $body, $to);
                } else if ($id == 80) {
                    $date = $reference_1_id;
                    $hub_shipments = $reference_2_id;
                    $subject = $notification->subject;
                    $body = $notification->body;
                    $html = '<table style="width:100%;">';
                    $html .= '<thead><tr>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">S No.</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Tracking Number</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Origin</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Hub</th>';
                    $html .= '</tr></thead><tbody>';
                    $serial = 1;
                    foreach ($hub_shipments as $hub_shipment) {
                        foreach ($hub_shipment as $origin_shipment) {
                            foreach ($origin_shipment as $index => $data) {
                                $html .= '<tr>';
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial . '</td>';
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data['tracking_number'] . '</td>';
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data['origin_name'] . '</td>';
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data['hub_name'] . '</td>';
                                $serial++;
                            }
                        }
                    }
                    $html .= '</tbody></table>';
                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $date, $subject);
                    }
                    if (strpos($body, '[preview]') !== FALSE) {
                        $body = str_replace('[preview]', $html, $body);
                    }

                    $to = array();

                    $department_head = Admin::where('role_id', 3)->where('status', 1);

                    if ($department_head->exists()) {
                        $to = array_merge($to, $department_head->pluck('email')->toArray());
                        $to[] = 'hammad.saleem@trax.pk';
                        self::email($subject, $body, $to);
                    }
                } else if ($id == 81) {
                    $subject = $notification->subject;
                    $body = $notification->body;
                    $sales_person = $reference_1_id;
                    $admin_id = $reference_2_id;
                    $user = Admin::find($admin_id);
                    $tagged_by = $user->name;
                    $html = '<table style="width:100%;">';
                    $html .= '<thead><tr>
                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Shipper Name</th>
                            <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Tagged By</th>
                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Old Sales Person</th> 
                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Old Tag Date</th> 
                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">New Sales Person</th>';
                    $html .= '</tr></thead><tbody>';

                    $to = array();
                    $cc = array('waqas@trax.pk', 'khan.usama@trax.pk', 'shahrukh.raheem@trax.pk');
                    foreach ($sales_person as $index => $person) {
                        // dd($person);
                        $shipper = User::find($index);
                        $html .= '<tr>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipper->name . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $tagged_by . '</td>';
                        if ($person['old_sale_person'] != null) {
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $person['old_sale_person']->name . '</td>';
                        } else {
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">-</td>';
                        }
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $person['old_sale_person_date'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $person['new_sale_person']->name . '</td>';
                        $html .= '</tr>';

                        if ($person['zone']->id == 1) {
                            $cc[] = 'waqas.shaikh@trax.pk';
                            $cc[] = 'nabeel.ahmed@trax.pk';
                        } else if ($person['zone']->id == 3) {
                            $cc[] = 'abbas.niazi@trax.pk';
                            $cc[] = '';
                        } else if ($person['zone']->id == 2) {
                            $cc[] = 'ali.qureshi@trax.pk';
                            $cc[] = 'adeel.ali@trax.pk';
                        }

                        if ($person['old_sale_person']->email) {
                            $cc[] = $person['old_sale_person']->email;
                        }

                        if ($person['new_sale_person']->email) {
                            $to[] = $person['new_sale_person']->email;
                        }
                    }

                    $html .= '</tbody></table>';

                    if (strpos($body, '[preview]') !== FALSE) {
                        $body = str_replace('[preview]', $html, $body);
                    }

                    $sale_head_email = Admin::where('role_id', 4)->select('email')->first();

                    if ($sale_head_email != '') {
                        $cc[] = $sale_head_email->email;
                    }
                    if ($to == null) {
                        $cc = null;
                    }

                    self::email($subject, $body, $to, $cc);
                } else if ($id == 82) {
                    $done_payment_report = DonePaymentsReport::get();
                    if ($done_payment_report) {
                        $date = Carbon::today()->format('Y-m-d');
                        $subject = $notification->subject;
                        $body = $notification->body;
                        if (strpos($subject, '[date]') !== FALSE) {
                            $subject = str_replace('[date]', $date, $subject);
                        }

                        if (strpos($body, '[date]') !== FALSE) {
                            $body = str_replace('[date]', $date, $body);
                        }

                        $link = '<a href="' . $reference_2_id . '" target="_blank">Report</a>';

                        if (strpos($subject, '[link]') !== FALSE) {
                            $subject = str_replace('[link]', $link, $subject);
                        }

                        if (strpos($body, '[link]') !== FALSE) {
                            $body = str_replace('[link]', $link, $body);
                        }
                        $summary_html = '<div style="margin-bottom: 100px;"><table style="width:100%;">';
                        $summary_html .= '<thead><tr>
                                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Total Shippers</th>
                                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Total Amount</th>
                                           </tr></thead><tbody>';
                        $shippers = array();
                        $html = '<table style="width:100%;">';
                        $html .= '<thead><tr>
                                            <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">S No.</th>
                                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Payment ID</th>
                                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Shipper Name</th>
                                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">IBAN Number</th>
                                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Amount</th>
                                           </tr></thead><tbody>';
                        $total_amount = 0;
                        $serial = 1;
                        foreach ($done_payment_report as $done_payment) {
                            if (!in_array($done_payment->shipper_id, $shippers)) {
                                $shippers[$done_payment->shipper_id] = $done_payment->shipper_id;
                            }
                            $html .= '<tr>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . str_pad($done_payment->payment_id, 6, '0', STR_PAD_LEFT) . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $done_payment->shipper_name . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $done_payment->iban_number . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format($done_payment->amount) . '</td>';
                            $html .= '</tr>';
                            $total_amount = $total_amount + $done_payment->amount;
                            $serial++;
                        }
                        $html .= '<tr>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">Total</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"></td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"></td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"></td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format($total_amount) . '</td>';
                        $html .= '</tr>';
                        $html .= '</tbody></table>';

                        $summary_html .= '<tr>';
                        $summary_html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . count($shippers) . '</td>';
                        $summary_html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format($total_amount) . '</td>';
                        $summary_html .= '</tr>';
                        $summary_html .= '</tbody></table></div>';

                        $html = $summary_html . $html;
                        if (strpos($body, '[preview]') !== FALSE) {
                            $body = str_replace('[preview]', $html, $body);
                        }

                        $to = array();
                        $bcc = array();
                        $to[] = 'hassan@trax.pk';
                        $to[] = 'fawad.ahmed@trax.pk';
                        $to[] = 'shafay.tariq@trax.pk';
                        $to[] = 'wajiha.majeed@trax.pk';
                        $to[] = 'huzaifa.aamir@trax.pk';
                        $to[] = 'mohsin.khan@trax.pk';
                        $bcc[] = 'muhammad.waqas@trax.pk';
                        $bcc[] = 'danish.zahid@trax.pk';

                        self::email($subject, $body, $to, NULL, $bcc);
                    }
                } else if ($id == 83) {
                    $pickup_request_id = $reference_1_id;
                    $pickup_note_id = $reference_2_id;

                    if ($pickup_request_id) {
                        $pickup = v2PickupRequest::find($pickup_request_id);
                        if ($pickup) {
                            $shipper_name = $pickup->shipper->name;
                            $rider_name = $pickup->rider->name;
                            $number = 0;
                            $shipment_pickup_date = '';
                            $rider_pickup = V2RiderPickup::where('pickup_request_id', $pickup_request_id)->where('pickup_note_id', $pickup_note_id);
                            if ($rider_pickup->exists()) {
                                $rider_pickup = $rider_pickup->first();
                                $number = $rider_pickup->shipments;
                                $shipment_pickup_date = $rider_pickup->created_at;
                            }
                            if (strpos($body, '[rider_name]') !== FALSE) {
                                $body = str_replace('[rider_name]', $rider_name, $body);
                            }
                            if (strpos($body, '[shipper_name]') !== FALSE) {
                                $body = str_replace('[shipper_name]', $shipper_name, $body);
                            }
                            if (strpos($body, '[requested_date]') !== FALSE) {
                                $body = str_replace('[requested_date]', $pickup->created_at, $body);
                            }
                            if (strpos($body, '[number]') !== FALSE) {
                                if ($number == 0) {
                                    $body = str_replace('[number]', 0, $body);
                                } else {
                                    $body = str_replace('[number]', $pickup->number, $body);
                                }
                            }
                            if (strpos($subject, '[rider_name]') !== FALSE) {
                                $subject = str_replace('[rider_name]', $rider_name, $subject);
                            }
                            if (strpos($subject, '[shipper_name]') !== FALSE) {
                                $subject = str_replace('[shipper_name]', $shipper_name, $subject);
                            }
                            if (strpos($subject, '[requested_date]') !== FALSE) {
                                $subject = str_replace('[requested_date]', $pickup->created_at, $subject);
                            }
                            if (strpos($subject, '[number]') !== FALSE) {
                                if ($number == 0) {
                                    $subject = str_replace('[number]', 0, $subject);
                                } else {
                                    $subject = str_replace('[number]', $pickup->number, $subject);
                                }
                            }
                            if (strpos($subject, '[shipment_picked_date]') !== FALSE) {
                                if ($shipment_pickup_date != null) {
                                    $subject = str_replace('[shipment_picked_date]', $shipment_pickup_date, $subject);
                                } else {
                                    $subject = str_replace('[shipment_picked_date]', '-', $subject);
                                }
                            }

                            if ($pickup->shipper->email) {
                                $to[] = $pickup->shipper->email;
                            }
                            self::email($subject, $body, $to);
                        }
                    }
                } else if ($id == 84) {
                    $yesterday = Carbon::yesterday();
                    $shipper_wise_shipments = array();
                    $shipment_requests = ShipmentPiecesRequest::whereDate('created_at', $yesterday);
                    if ($shipment_requests->exists()) {
                        $shipment_ids = $shipment_requests->pluck('shipment_id')->toArray();
                        if (count($shipment_ids) > 0) {
                            foreach ($shipment_ids as $shipment_id) {
                                $user_id = Shipment::find($shipment_id)->user_id;
                                if (!array_key_exists($user_id, $shipper_wise_shipments)) {
                                    $shipper_wise_shipments[$user_id] = array();
                                }
                                if (!in_array($shipment_id, $shipper_wise_shipments[$user_id])) {
                                    $shipper_wise_shipments[$user_id][] = $shipment_id;
                                }
                            }
                            if (count($shipper_wise_shipments) > 0) {
                                foreach ($shipper_wise_shipments as $shipper_id => $shipments) {
                                    $subject = $notification->subject;
                                    $body = $notification->body;
                                    $shipper = User::find($shipper_id);
                                    if (strpos($subject, '[date]') !== FALSE) {
                                        $subject = str_replace('[date]', $yesterday->toDateString(), $subject);
                                    }
                                    if (strpos($body, '[date]') !== FALSE) {
                                        $body = str_replace('[date]', $yesterday->toDateString(), $body);
                                    }
                                    if (strpos($subject, '[shipper_name]') !== FALSE) {
                                        $subject = str_replace('[shipper_name]', $shipper->name, $subject);
                                    }
                                    if (strpos($body, '[shipper_name]') !== FALSE) {
                                        $body = str_replace('[shipper_name]', $shipper->name, $body);
                                    }
                                    $tracking_number = FALSE;

                                    $product_description = FALSE;

                                    $cod_amount = FALSE;

                                    $origin = FALSE;

                                    $destination = FALSE;

                                    $status = FALSE;

                                    if (strpos($body, '[tracking_number]') !== FALSE) {
                                        $tracking_number = TRUE;
                                        $body = str_replace('[tracking_number]', '', $body);
                                    }

                                    if (strpos($body, '[product_description]') !== FALSE) {
                                        $product_description = TRUE;
                                        $body = str_replace('[product_description]', '', $body);
                                    }

                                    if (strpos($body, '[cod_amount]') !== FALSE) {
                                        $cod_amount = TRUE;
                                        $body = str_replace('[cod_amount]', '', $body);
                                    }

                                    if (strpos($body, '[origin]') !== FALSE) {
                                        $origin = TRUE;
                                        $body = str_replace('[origin]', '', $body);
                                    }

                                    if (strpos($body, '[destination]') !== FALSE) {
                                        $destination = TRUE;
                                        $body = str_replace('[destination]', '', $body);
                                    }

                                    if (strpos($body, '[status]') !== FALSE) {
                                        $status = TRUE;
                                        $body = str_replace('[status]', '', $body);
                                    }

                                    $html = '<table style="width:100%;">';

                                    $html .= '<thead><tr>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">S No.</th>';

                                    if ($tracking_number) {
                                        $html .= '<th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Tracking No.</th>';
                                    }
                                    if ($product_description) {
                                        $html .= '<th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Product Description</th>';
                                    }
                                    if ($cod_amount) {
                                        $html .= '<th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Amount</th>';
                                    }
                                    if ($origin) {
                                        $html .= '<th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Origin</th>';
                                    }
                                    if ($destination) {
                                        $html .= '<th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Destination</th>';
                                    }
                                    if ($status) {
                                        $html .= '<th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Status</th>';
                                    }
                                    $html .= '</tr></thead><tbody>';
                                    $serial = 1;
                                    foreach ($shipments as $shipment_id) {
                                        $shipment = Shipment::find($shipment_id);
                                        $html .= '<tr><td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial . '</td>';
                                        if ($tracking_number) {
                                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment->tracking_number . '</td>';
                                        }
                                        if ($product_description) {
                                            $description = '';
                                            foreach ($shipment->items as $item) {
                                                $description .= $item->product->product_name . ' (x' . $item->quantity . '), ';
                                            }
                                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $description . '</td>';
                                        }
                                        if ($cod_amount) {
                                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment->amount . '</td>';
                                        }
                                        if ($origin) {
                                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment->pickup_address->city->name . '</td>';
                                        }
                                        if ($destination) {
                                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment->consignee_city->name . '</td>';
                                        }
                                        if ($status) {
                                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment->status_shipper->name . '</td>';
                                        }
                                        $html .= '</tr>';
                                        $serial++;
                                    }
                                    $html .= '</tbody></table>';

                                    if (strpos($body, '[preview]') !== FALSE) {
                                        $body = str_replace('[preview]', $html, $body);
                                    }
                                    $to = array();
                                    if ($shipper->email) {
                                        $to[] = $shipper->email;
                                    }
                                    self::email($subject, $body, $to);
                                }
                            }
                        }
                    }
                } else if ($id == 85) {
                    $shipments = Shipment::find($reference_1_id);
                    $booking_person = $reference_2_id;
                    $admin = Admin::find($booking_person);
                    $subject = $notification->subject;
                    $body = $notification->body;

                    $html = '<table style="width:100%;">';
                    $html .= '<thead><tr>
                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Tracking Number</th>
                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Origin</th>
                            <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Destination</th>
                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Total Charges</th>';
                    $html .= '</tr></thead><tbody>';

                    foreach ($shipments as $shipment) {
                        $origin = $shipment->pickup_address;
                        $destination = $shipment->consignee_city;
                        ;
                        $html .= '<tr>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment->tracking_number . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $origin->pickup_address . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $destination->name . '</td>';
                        if ($shipment->packaging_charges == null) {
                            if ($shipment->charges_mode_id == 1) {
                                $amount = $shipment->received_amount;
                            } else {
                                $amount = $shipment->amount;
                            }
                            $total_charges = $amount;
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $total_charges . '</td>';
                        } else {
                            if ($shipment->charges_mode_id == 1) {
                                $amount = $shipment->received_amount;
                            } else {
                                $amount = $shipment->amount;
                            }
                            $packaging = $shipment->packaging_charges;
                            $total_charges = $amount + $packaging;
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $total_charges . '</td>';
                        }
                        $html .= '</tr>';
                    }

                    $html .= '</tbody></table>';
                    if (strpos($body, '[preview]') !== FALSE) {
                        $body = str_replace('[preview]', $html, $body);
                    }

                    $finance = Admin::whereIn('id', [12, 60, 49])->where('status', 1);

                    $to = array();
                    if ($admin) {
                        $to[] = $admin->email;
                    }

                    if ($finance->exists()) {
                        $to = array_merge($to, $finance->pluck('email')->toArray());
                    }
                    $to[] = 'shafay.tariq@trax.pk';

                    self::email($subject, $body, $to);
                } else if ($id == 86) {
                    $hub = City::find($reference_1_id);
                    $date = Carbon::today()->format('Y m d');
                    $subject = $notification->subject;
                    $body = $notification->body;

                    $hub_id = $hub->id;

                    if (strpos($subject, '[hub]') !== FALSE) {
                        $subject = str_replace('[hub]', $hub->name, $subject);
                    }

                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $date, $subject);
                    }

                    if (strpos($body, '[date]') !== FALSE) {
                        $body = str_replace('[date]', $date, $body);
                    }

                    $link = '<a href="' . $reference_2_id . '" target="_blank">Report</a>';

                    if (strpos($body, '[link]') !== FALSE) {
                        $body = str_replace('[link]', $link, $body);
                    }

                    $to = array();

                    $to_admins = Admin::whereIn('role_id', [23, 30, 10, 8, 3, 9, 25, 45])->where('status', 1)->whereHas('hubs', function ($query) use ($hub_id) {
                        $query->where('hub_id', $hub_id);
                    });
                    if ($to_admins->exists()) {
                        $to = array_merge($to, $to_admins->pluck('email')->toArray());
                    }

                    self::email($subject, $body, $to);
                } else if ($id == 87) {
                    $hub = City::find($reference_1_id);
                    $date = Carbon::now()->toDateString();
                    $subject = $notification->subject;
                    $body = $notification->body;

                    $hub_id = $hub->id;

                    if (strpos($subject, '[hub]') !== FALSE) {
                        $subject = str_replace('[hub]', $hub->name, $subject);
                    }

                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $date, $subject);
                    }

                    if (strpos($body, '[date]') !== FALSE) {
                        $body = str_replace('[date]', $date, $body);
                    }

                    $link = '<a href="' . $reference_2_id . '" target="_blank">Report</a>';

                    if (strpos($body, '[link]') !== FALSE) {
                        $body = str_replace('[link]', $link, $body);
                    }

                    $to = array();

                    $to_admins = Admin::whereIn('role_id', [23, 30, 10, 9, 25])->where('status', 1)->whereHas('hubs', function ($query) use ($hub_id) {
                        $query->where('hub_id', $hub_id);
                    });
                    if ($to_admins->exists()) {
                        $to = array_merge($to, $to_admins->pluck('email')->toArray());
                    }

                    self::email($subject, $body, $to);
                } else if ($id == 88) {
                    $subject = $notification->subject;
                    $body = $notification->body;
                    $date = Carbon::now()->toDateString();
                    $runner_detail = RunnerDetail::find($reference_1_id);
                    $runner = Runner::find($runner_detail->runner_id);
                    $link = '<a href="' . $reference_2_id . '" target="_blank">Report</a>';

                    if (strpos($subject, '[runner]') !== FALSE) {
                        $subject = str_replace('[runner]', $runner->name, $subject);
                    }

                    if (strpos($body, '[runner]') !== FALSE) {
                        $body = str_replace('[runner]', $runner->name, $body);
                    }
                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $date, $subject);
                    }

                    if (strpos($body, '[date]') !== FALSE) {
                        $body = str_replace('[date]', $date, $body);
                    }
                    if (strpos($subject, '[link]') !== FALSE) {
                        $subject = str_replace('[link]', $link, $subject);
                    }

                    if (strpos($body, '[link]') !== FALSE) {
                        $body = str_replace('[link]', $link, $body);
                    }

                    //                    $admin = Admin::whereIn('role_id', [23, 46, 3])->where('status', 1);

                    //              $to[] = 'hassan@trax.pk';
                    //              if ($admin->exists()) {
                    //                  $to = array_merge($to, $admin->pluck('email')->toArray());
                    //              }
                    $to[] = 'syed.sharique@trax.pk';
                    $to[] = 'balaj.khan@trax.pk';
                    $to[] = 'bilal.shah@trax.pk';


                    self::email($subject, $body, $to);
                } else if ($id == 89) {

                    $finance = Admin::whereIn('id', [60, 12])->where('status', 1);

                    $to = array();

                    if ($finance->exists()) {
                        $to = array_merge($to, $finance->pluck('email')->toArray());
                    }


                    $file = $reference_2_id;
                    $link = '<br/><a href="' . $file . '" target="_blank"><u>Download</u></a>';

                    if (strpos($body, '[link]') !== FALSE) {
                        $body = str_replace('[link]', $link, $body);
                    }
                    self::email($subject, $body, $to);
                } else if ($id == 90) {
                    $to = array();
                    $finance = Admin::whereIn('id', [12, 60, 79, 216])->where('status', 1);
                    if ($finance->exists()) {
                        $to = array_merge($to, $finance->pluck('email')->toArray());
                    }
                    $file = $reference_2_id;
                    $link = '<br/><a href="' . $file . '" target="_blank"><u>Download</u></a>';
                    if (strpos($body, '[link]') !== FALSE) {
                        $body = str_replace('[link]', $link, $body);
                    }
                    self::email($subject, $body, $to);
                } else if ($id == 91) {
                    $user = User::find($reference_1_id);
                    $pin = $reference_2_id;
                    if ($user) {
                        if (strpos($body, '[user_name]') !== FALSE) {
                            $body = str_replace('[user_name]', $user->name, $body);
                        }
                        if (strpos($body, '[pin]') !== FALSE) {
                            $body = str_replace('[pin]', $pin, $body);
                        }

                        $to = $user->phone;
                        self::sms($body, $to);
                    }
                } else if ($id == 92) {
                    $subject = $notification->subject;
                    $body = $notification->body;
                    $done_payment = DonePayment::find($reference_1_id);

                    $user = $done_payment->shipper;
                    $payment_id = $done_payment->id;


                    $sale_person_id = SalePersonTag::where('user_id', $user->id)->where('status', 0)->select('admin_id')->first();
                    if ($sale_person_id) {
                        $sale_person_email = Admin::find($sale_person_id->admin_id)->email;
                    }

                    $to = array();
                    $cc = array();

                    if (strpos($body, '[shipper_name]') !== FALSE) {
                        $body = str_replace('[shipper_name]', $user->name, $body);
                    }
                    if (strpos($body, '[payment_id]') !== FALSE) {
                        $body = str_replace('[payment_id]', $payment_id, $body);
                    }

                    if ($user) {
                        $to[] = $user->email;
                    }

                    if ($sale_person_email) {
                        $cc[] = $sale_person_email;
                    }
                    $to[] = 'wajiha.majeed@trax.pk';
                    $to[] = 'shafay.tariq@trax.pk';

                    self::email($subject, $body, $to, $cc);
                } else if ($id == 93) {
                    if ($reference_2_id) {
                        $subject = $notification->subject;
                        $body = $notification->body;
                        $user_emails = User::whereIn('id', $reference_2_id)->pluck('email')->toArray();
                        // dd($user_emails);
                        if (count($user_emails) >= 0) {
                            foreach ($user_emails as $email) {
                                $to = $email;
                                self::email($subject, $body, $to);
                            }
                        }
                    }
                } else if ($id == 94) {

                    $subject = $notification->subject;
                    $body = $notification->body;
                    $user = User::find($reference_2_id);
                    if ($user) {
                        $to[] = $user->email;
                    }
                    self::email($subject, $body, $to);
                } else if ($id == 95) {
                    if ($reference_2_id) {
                        $subject = $notification->subject;
                        $body = $notification->body;
                        $user_emails = User::whereIn('id', $reference_2_id)->pluck('email')->toArray();
                        // dd($user_emails);
                        if (count($user_emails) >= 0) {
                            foreach ($user_emails as $email) {
                                $to = $email;
                                self::email($subject, $body, $to);
                            }
                        }
                    }
                } else if ($id == 96) {
                    $to = array();
                    $id = 3324;
                    $user = User::find($id);
                    if ($user) {
                        $to[] = $user->email;
                    }
                    $file = $reference_2_id;
                    $link = '<br/><a href="' . $file . '" target="_blank"><u>Download</u></a>';
                    if (strpos($body, '[link]') !== FALSE) {
                        $body = str_replace('[link]', $link, $body);
                    }
                    self::email($subject, $body, $to);
                } else if ($id == 99) {
                    $subject = $notification->subject;
                    $body = $notification->body;
                    $data = $reference_1_id;
                    $html = '<table style="width:100%;">';
                    $html .= '<thead><tr>
				    <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Requested Date</th>
				    <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Shipper Name</th>
				    <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Not Picked Reason</th>';
                    $html .= '</tr></thead><tbody>';
                    foreach ($data as $pickup_data) {
                        $html .= '<tr>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $pickup_data['requested_date'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $pickup_data['shipper_name'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $pickup_data['reason'] . '</td>';
                        $html .= '</tr>';
                        $to = $pickup_data['saleperson_email'];
                    }
                    $html .= '</tbody></table>';
                    if (strpos($body, '[preview]') !== FALSE) {
                        $body = str_replace('[preview]', $html, $body);
                    }
                    if (strpos($body, '[sales_person]') !== FALSE) {
                        $body = str_replace('[sales_person]', $pickup_data['sales_person'], $body);
                    }
                    self::email($subject, $body, $to);
                } else if ($id == 100) {
                    $date = Carbon::yesterday()->toDateString();
                    $date_from = $date . ' 08:00:00';
                    $next_day = Carbon::parse($date)->addDay(1);
                    $date_to = $next_day->toDateString();
                    $date_to = $date_to . ' 07:59:59';
                    $total_shipments = 0;
                    $after_cutoff_time = 0;
                    $pickup_requests = V2PickupRequest::join('users as u', 'v2_pickup_requests.shipper_id', '=', 'u.id')
                        ->join('v2_pickup_request_statuses as prs', 'prs.id', '=', 'v2_pickup_requests.status_id')
                        ->join('v2_pickup_request_shipments as vs', 'vs.id', '=', 'v2_pickup_requests.id')
                        ->join('user_shipping_infos AS usi', 'v2_pickup_requests.pickup_address_id', '=', 'usi.id')
                        ->join('cities as oc', 'usi.city_id', '=', 'oc.id')
                        ->join('v2_pickup_request_attempts as vpra', function ($join) {
                            $join->on('vpra.pickup_request_id', '=', 'v2_pickup_requests.id')
                                ->where(
                                    'vpra.created_at',
                                    '=',
                                    DB::raw('(select max(created_at) from v2_pickup_request_attempts where v2_pickup_request_attempts.pickup_request_id = v2_pickup_requests.id AND v2_pickup_request_attempts.reason_id is not Null )')
                                );
                        })
                        ->join('v2_pickup_request_not_pick_reasons as npr', 'npr.id', '=', 'vpra.reason_id')
                        ->select('v2_pickup_requests.id as id', 'v2_pickup_requests.created_at as requested_date', 'u.id as user_id', 'oc.name as origin', 'u.name as shipper_name', 'npr.name as reason', 'vs.created_at as booking_date', 'v2_pickup_requests.after_cut_off_time as after_cut_off_time', 'vpra.created_at')
                        ->where('v2_pickup_requests.status_id', 3)
                        ->whereNotNull('vpra.reason_id')
                        ->wherebetween('v2_pickup_requests.created_at', [$date_from, $date_to])
                        ->get();

                    $to = array();
                    if (count($pickup_requests) > 0) {
                        $subject = $notification->subject;
                        $body = $notification->body;

                        $html = '<table style="width:100%;">';
                        $html .= '<thead><tr>
                            <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Booking Date</th>
                            <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Shipper Name</th>
                            <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Origin</th>
                            <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Shipments</th>
                            <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">After Cut Off Time</th>
                            <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Not Picked</th>';
                        $html .= '</tr></thead><tbody>';

                        foreach ($pickup_requests as $pickup) {


                            $after_cutoff_time += $pickup->after_cut_off_time;
                            $to = array();
                            $to[] = $pickup->saleperson_email;
                            $html .= '<tr>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $pickup->requested_date . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $pickup->shipper_name . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $pickup->origin . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . V2PickupRequestShipment::where('pickup_request_id', $pickup->id)->count() . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $pickup->after_cut_off_time . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $pickup->reason . '</td>';
                            $html .= '</tr>';
                        }
                        $html .= '<tr>';
                        $html .= '<td  style="padding:5px; border: 1px solid black; border-collapse: collapse;">Total Numbers</td>';
                        $html .= '<td colspan="5" style="padding:5px; border: 1px solid black; font-weight:bold; border-collapse: collapse; text-align: center;">' . count($pickup_requests) . '</td>';
                        $html .= '</tr>';
                        $html .= '</tbody>';
                        $html .= '</table>';
                        if (strpos($body, '[preview]') !== FALSE) {
                            $body = str_replace('[preview]', $html, $body);
                        }

                        $admins_sales = Admin::where('role_id', 4)->where('status', 1)->select('name', 'email')->first();

                        if (strpos($body, '[head_of_sales]') !== FALSE) {
                            $body = str_replace('[head_of_sales]', $admins_sales->name, $body);
                        }

                        $to = $admins_sales->email;
                        self::email($subject, $body, $to);
                    }
                } else if ($id == 101) {
                    $date = Carbon::today()->toDateString();
                    $hub_shipment = $reference_2_id;
                    $subject = $notification->subject;
                    $body = $notification->body;
                    $hub = City::find($reference_1_id);
                    if (strpos($subject, '[hub]') !== FALSE) {
                        $subject = str_replace('[hub]', $hub->name, $subject);
                    }
                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $date, $subject);
                    }
                    $html = '<table style="width:100%;">';
                    $html .= '<thead><tr>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">S No.</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Tracking Number</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Origin</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Hub</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Vendor</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Poc</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Phone</th>';
                    $html .= '</tr></thead><tbody>';
                    $serial = 1;
                    foreach ($hub_shipment as $origin_shipment) {
                        foreach ($origin_shipment as $index => $data) {
                            $html .= '<tr>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data['tracking_number'] . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data['origin_name'] . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data['hub_name'] . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data['vendor'] . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data['poc'] . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data['phone'] . '</td>';
                            $html .= '</tr>';
                            $serial++;
                        }
                    }

                    $html .= '</tbody></table>';
                    if (strpos($body, '[preview]') !== FALSE) {
                        $body = str_replace('[preview]', $html, $body);
                    }

                    $to = array();
                    $operation_admins = Admin::join('admin_hubs', 'admin_hubs.admin_id', '=', 'admins.id')->whereIn('admins.role_id', [9, 10, 25])->where('admins.status', 1)->where('admin_hubs.hub_id', '=', $hub->id);
                    if ($operation_admins->exists()) {
                        $to = array_merge($to, $operation_admins->pluck('admins.email')->toArray());
                    }
                    self::email($subject, $body, $to);
                } else if ($id == 102) {
                    $date = Carbon::today()->toDateString();
                    $zone_hub_shipments = $reference_2_id;
                    $subject = $notification->subject;
                    $body = $notification->body;
                    $zone = Zone::find($reference_1_id);
                    $html = '<table style="width:100%;">';
                    $html .= '<thead><tr>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">S No.</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Tracking Number</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Origin</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Hub</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Vendor</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Poc</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Phone</th>';
                    $html .= '</tr></thead><tbody>';
                    $serial = 1;
                    foreach ($zone_hub_shipments as $zone_hub_shipment) {
                        foreach ($zone_hub_shipment as $origin_shipment) {
                            foreach ($origin_shipment as $index => $data) {
                                $html .= '<tr>';
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial . '</td>';
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data['tracking_number'] . '</td>';
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data['origin_name'] . '</td>';
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data['hub_name'] . '</td>';
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data['vendor'] . '</td>';
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data['poc'] . '</td>';
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data['phone'] . '</td>';
                                $html .= '</tr>';
                                $serial++;
                            }
                        }
                    }
                    $html .= '</tbody></table>';
                    if (strpos($subject, '[zone]') !== FALSE) {
                        $subject = str_replace('[zone]', $zone->name, $subject);
                    }
                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $date, $subject);
                    }
                    if (strpos($body, '[preview]') !== FALSE) {
                        $body = str_replace('[preview]', $html, $body);
                    }

                    $to = array();

                    $operation_admins = Admin::join('admin_hubs', 'admin_hubs.admin_id', '=', 'admins.id')->join('cities', 'cities.id', '=', 'admin_hubs.hub_id')->whereIn('admins.role_id', [8, 9, 25, 31])->where('admins.status', 1)->where('cities.zone_id', '=', $zone->id);
                    if ($operation_admins->exists()) {
                        $to = array_merge($to, $operation_admins->pluck('email')->toArray());
                    }

                    self::email($subject, $body, $to);
                } else if ($id == 103) {
                    $date = $reference_1_id;
                    $hub_shipments = $reference_2_id;
                    $subject = $notification->subject;
                    $body = $notification->body;
                    $html = '<table style="width:100%;">';
                    $html .= '<thead><tr>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">S No.</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Tracking Number</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Origin</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Hub</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Vendor</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Poc</th>
                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Phone</th>';
                    $html .= '</tr></thead><tbody>';
                    $serial = 1;
                    foreach ($hub_shipments as $hub_shipment) {
                        foreach ($hub_shipment as $origin_shipment) {
                            foreach ($origin_shipment as $index => $data) {
                                $html .= '<tr>';
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial . '</td>';
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data['tracking_number'] . '</td>';
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data['origin_name'] . '</td>';
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data['hub_name'] . '</td>';
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data['vendor'] . '</td>';
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data['poc'] . '</td>';
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data['phone'] . '</td>';
                                $html .= '</tr>';
                                $serial++;
                            }
                        }
                    }
                    $html .= '</tbody></table>';
                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $date, $subject);
                    }
                    if (strpos($body, '[preview]') !== FALSE) {
                        $body = str_replace('[preview]', $html, $body);
                    }

                    $to = array();

                    $department_head = Admin::where('role_id', 3)->where('status', 1);

                    if ($department_head->exists()) {
                        $to = array_merge($to, $department_head->pluck('email')->toArray());
                        self::email($subject, $body, $to);
                    }
                } else if ($id == 104) {
                    $shipment_id = $reference_1_id;
                    $phone = Shipment::find($shipment_id)->consignee_phone_number_1;
                    $to = $phone;
                    self::sms($body, $to);
                } else if ($id == 105) {

                    $pickup_request_id = $reference_1_id;
                    $reason_id = $reference_2_id;
                    $pickup_request = V2PickupRequest::find($pickup_request_id);

                    //start
                    //pickup address
                    $pickup_address = $pickup_request->pickup_address->pickup_address;
                    //city name
                    $city_name = $pickup_request->pickup_address->city->name;
                    //end

                    $shipper_name = $pickup_request->shipper->name;
                    $shipper_id = $pickup_request->shipper->id;
                    $reason = V2PickupRequestNotPickReason::find($reason_id);
                    $sale_person = SalePersonTag::where('user_id', $shipper_id)->where('status', 0)->first();
                    if ($sale_person) {


                        $sales_person = Admin::find($sale_person->admin_id);
                        $sales_person_name = $sales_person->name;
                        $sale_person_phone = $sales_person->phone_number;
                        if (strpos($body, '[sales_person]') !== FALSE) {
                            $body = str_replace('[sales_person]', $sales_person_name, $body);
                        }
                        if (strpos($body, '[shipper_name]') !== FALSE) {
                            $body = str_replace('[shipper_name]', $shipper_name, $body);
                        }
                        if (strpos($body, '[reason]') !== FALSE) {
                            $body = str_replace('[reason]', $reason->name, $body);
                        }
                        if (strpos($body, '[pickup_address]') !== FALSE) {
                            $body = str_replace('[pickup_address]', $pickup_address, $body);
                        }
                        if (strpos($body, '[city_name]') !== FALSE) {
                            $body = str_replace('[city_name]', $city_name, $body);
                        }

                        $to = $sale_person_phone;
                        self::sms($body, $to);
                    }
                } else if ($id == 106) {

                    $body = $notification->body;
                    $pickup_request_id = $reference_2_id;
                    $riders = $reference_1_id;
                    $old_rider_id = $riders['old_rider_id'];
                    $new_rider_id = $riders['new_rider_id'];

                    //                    $rider = Rider::find($rider);
                    $old_rider = Rider::find($old_rider_id);
                    $new_rider = Rider::find($new_rider_id);
                    $admin_name = V2PickupRequest::find($pickup_request_id)->last_admin->name;


                    if (strpos($body, '[old_rider_name]') !== FALSE) {
                        $body = str_replace('[old_rider_name]', $old_rider->name, $body);
                    }
                    if (strpos($body, '[new_rider_name]') !== FALSE) {
                        $body = str_replace('[new_rider_name]', $new_rider->name, $body);
                    }
                    if (strpos($body, '[pickup_request_id]') !== FALSE) {
                        $body = str_replace('[pickup_request_id]', str_pad($pickup_request_id, 6, '0', STR_PAD_LEFT), $body);
                    }
                    if (strpos($body, '[pickup_coordinator_name]') !== FALSE) {
                        $body = str_replace('[pickup_coordinator_name]', $admin_name, $body);
                    }

                    $to = $old_rider->phone;
                    self::sms($body, $to);
                } else if ($id == 107) {

                    $body = $notification->body;
                    $pickup_request_id = $reference_2_id;
                    $riders = $reference_1_id;
                    $old_rider_id = $riders['old_rider_id'];
                    $new_rider_id = $riders['new_rider_id'];

                    //                    $rider = Rider::find($rider);
                    $old_rider = Rider::find($old_rider_id);
                    $new_rider = Rider::find($new_rider_id);
                    $admin_name = V2PickupRequest::find($pickup_request_id)->last_admin->name;


                    if (strpos($body, '[old_rider_name]') !== FALSE) {
                        $body = str_replace('[old_rider_name]', $old_rider->name, $body);
                    }
                    if (strpos($body, '[new_rider_name]') !== FALSE) {
                        $body = str_replace('[new_rider_name]', $new_rider->name, $body);
                    }
                    if (strpos($body, '[pickup_request_id]') !== FALSE) {
                        $body = str_replace('[pickup_request_id]', str_pad($pickup_request_id, 6, '0', STR_PAD_LEFT), $body);
                    }
                    if (strpos($body, '[pickup_coordinator_name]') !== FALSE) {
                        $body = str_replace('[pickup_coordinator_name]', $admin_name, $body);
                    }

                    $to = $new_rider->phone;
                    self::sms($body, $to);
                } else if ($id == 108) {
                    $date = Carbon::today()->toDateString();
                    $shipments = $reference_2_id;
                    $subject = $notification->subject;
                    $body = $notification->body;
                    $date = $reference_1_id;
                    $html = '<table style="width:100%;">';
                    $html .= '<thead><tr>
			                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">S No.</th>
			                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Tracking Number</th>
			                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Origin</th>
			                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Destination</th>
			                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Shipper</th>
			                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Status</th>
			                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Last Status Date</th>
			                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Arrival Status Date</th>';
                    $html .= '</tr></thead><tbody>';
                    $serial = 1;
                    foreach ($shipments as $index => $shipment) {
                        $html .= '<tr>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment['tracking_number'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment['origin'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment['destination'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment['shipper'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment['status'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment['last_status_date'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment['arrival_status_date'] . '</td>';
                        $html .= '</tr>';
                        $serial++;
                    }
                    $html .= '</tbody></table>';
                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $date, $subject);
                    }
                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $date, $body);
                    }
                    if (strpos($body, '[preview]') !== FALSE) {
                        $body = str_replace('[preview]', $html, $body);
                    }


                    $to = array();

                    $operation_admins = Admin::whereIn('id', [10, 288, 423])->where('admins.status', 1);
                    if ($operation_admins->exists()) {
                        $to = array_merge($to, $operation_admins->pluck('email')->toArray());
                    }

                    self::email($subject, $body, $to);
                } else if ($id == 109) {
                    $date = Carbon::today()->toDateString();
                    $shipments = $reference_2_id;
                    $subject = $notification->subject;
                    $body = $notification->body;
                    $date = $reference_1_id;
                    $html = '<table style="width:100%;">';
                    $html .= '<thead><tr>
                                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">S No.</th>
                                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Tracking Number</th>
                                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Origin</th>
                                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Destination</th>
                                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Shipper</th>
                                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Status</th>
                                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Last Status Date</th>
                                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Arrival Status Date</th>';
                    $html .= '</tr></thead><tbody>';
                    $serial = 1;
                    foreach ($shipments as $index => $shipment) {
                        $html .= '<tr>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment['tracking_number'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment['origin'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment['destination'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment['shipper'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment['status'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment['last_status_date'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment['arrival_status_date'] . '</td>';
                        $html .= '</tr>';
                        $serial++;
                    }
                    $html .= '</tbody></table>';
                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $date, $subject);
                    }
                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $date, $body);
                    }
                    if (strpos($body, '[preview]') !== FALSE) {
                        $body = str_replace('[preview]', $html, $body);
                    }

                    $to = array();

                    $operation_admins = Admin::whereIn('id', [10, 288, 423])->where('admins.status', 1);
                    if ($operation_admins->exists()) {
                        $to = array_merge($to, $operation_admins->pluck('email')->toArray());
                    }

                    self::email($subject, $body, $to);
                } else if ($id == 110) {
                    $date = $reference_1_id;
                    $file = $reference_2_id;
                    $link = '<br/><a href="' . $file . '" target="_blank"><u>Download</u></a>';
                    if (strpos($body, '[link]') !== FALSE) {
                        $body = str_replace('[link]', $link, $body);
                    }
                    $to = ['uzair.anees@trax.pk'];
                    $cc = ['shahbaz.abbasi@trax.pk'];

                    self::email($subject, $body, $to, $cc);
                } else if ($id == 111) {
                    $date = $reference_1_id;
                    $file = $reference_2_id;
                    $link = '<br/><a href="' . $file . '" target="_blank"><u>Download</u></a>';
                    if (strpos($body, '[link]') !== FALSE) {
                        $body = str_replace('[link]', $link, $body);
                    }
                    $to = ['uzair.anees@trax.pk'];
                    $cc = ['shahbaz.abbasi@trax.pk'];

                    self::email($subject, $body, $to, $cc);
                } else if ($id == 112) {
                    $subject = $notification->subject;
                    $body = $notification->body;
                    $data = $reference_1_id;
                    if ($data != null) {
                        if (strpos($body, '[preview]') !== FALSE) {
                            $body = str_replace('[preview]', $data, $body);
                        }
                        //$admins = Admin::whereIn('id', [10, 288, 423,426,481,58])->where('status',1);
                        $admins = Admin::whereIn('id', [36, 55, 288])->where('status', 1);

                        if ($admins->exists()) {
                            $to = $admins->pluck('email')->toArray();
                        }

                        /* $admins = Admin::whereIn('id', [70,228,161,137,15,36,21,386,397,414,428,495,50])->where('status',1);

                         if ($admins->exists()) {
                             $cc = $admins->pluck('email')->toArray();
                         }*/
                        self::email($subject, $body, $to /*,$cc*/);
                    }
                } else if ($id == 113) {
                    $subject = $notification->subject;
                    $body = $notification->body;
                    $lead = $reference_1_id;
                    $route = route('cod.register', ['lead_id' => $lead->id]);
                    if ($lead != null) {
                        $sales_person = Admin::find($lead->sale_person_id);

                        if ($sales_person->official_phone_number != null) {
                            $phone_number = $sales_person->official_phone_number;
                        } else {
                            $phone_number = $sales_person->phone_number;
                        }

                        $html = '<div style="height: 100%; width: 100%; left: 0; top: 0; overflow: hidden; position: fixed;background-color: #F5F5F5">
                    <div align="center" style="overflow: hidden; display: flex; justify-content:space-around; margin-bottom: 20px;">
                        <img src="' . asset('img/sonic_logo_new.png') . '" alt="Sonic" style="display: inline-block; width: 10%;">
                        <img src="' . asset('img/trax_logo_new.png') . '" alt="Trax" style="display: inline-block; width: 15%">
                    </div>';

                        if (strpos($body, '[contact_person]') !== FALSE) {
                            $body = str_replace('[contact_person]', $lead->contact_person, $body);
                        }

                        if (strpos($body, '[sales_person]') !== FALSE) {
                            $body = str_replace('[sales_person]', $sales_person->name, $body);
                        }

                        if (strpos($body, '[sales_person_contact]') !== FALSE) {
                            $body = str_replace('[sales_person_contact]', $phone_number, $body);
                        }

                        $link = '<div style="margin-top: 20px"><a href="' . $route . '" target="_blank" style="background-color: #003399; color: white; padding: 1em 1.5em; text-decoration: none;">Click To Register</a></div>';

                        if (strpos($body, '[link]') !== FALSE) {
                            $body = str_replace('[link]', $link, $body);
                        }
                        $html .= '<div style="margin-bottom: 0px; background-color: #ffffff; vertical-align: middle;"><p>';

                        $html .= $body . '</p>
                    </div>
                        <p style="margin-top: 0px; margin-bottom: 0px; vertical-align: middle;">Copyright © ' . now()->year . ' By TRAX, All Rights Reserved.</p>
                    </div>';
                        $to = $lead->email_address;
                        self::email($subject, $html, $to);
                    }
                } else if ($id == 114) {
                    $code = $reference_1_id;
                    $user_id = $reference_2_id;
                    $body = $notification->body;
                    if (strpos($body, '[code]') !== FALSE) {
                        $body = str_replace('[code]', $code, $body);
                    }
                    $user_phone = User::find($user_id)->phone;
                    $to = $user_phone;
                    self::sms($body, $to);
                } else if ($id == 115) {
                    $tracking_number = $reference_1_id;
                    $shipper_info_id = $reference_2_id;
                    $body = $notification->body;
                    if ($tracking_number) {
                        $shipper_info = RetailShipperInfo::find($shipper_info_id);

                        if (strpos($body, '[tracking_number]') !== FALSE) {
                            $body = str_replace('[tracking_number]', $tracking_number, $body);
                        }
                        if (strpos($body, '[shipper]') !== FALSE) {
                            $body = str_replace('[shipper]', $shipper_info->name, $body);
                        }
                        $to = $shipper_info->shipper_phone_no;
                        self::sms($body, $to);
                    }
                } else if ($id == 116) {
                    $tracking_number = $reference_1_id;
                    $shipper_info_id = $reference_2_id;
                    $body = $notification->body;
                    if ($tracking_number) {
                        $shipper_info = RetailShipperInfo::find($shipper_info_id);

                        if (strpos($body, '[tracking_number]') !== FALSE) {
                            $body = str_replace('[tracking_number]', $tracking_number, $body);
                        }
                        if (strpos($body, '[shipper]') !== FALSE) {
                            $body = str_replace('[shipper]', $shipper_info->name, $body);
                        }
                        $to = $shipper_info->shipper_phone_no;
                        self::sms($body, $to);
                    }
                } else if ($id == 117) {
                    $date = str_replace('00:00:00', '', Carbon::today());
                    $subject = $notification->subject;
                    $body = $notification->body;
                    $claim_id = $reference_1_id;
                    $status_id = $reference_2_id;
                    $claim = CrmRequest::find($claim_id);
                    $to = array();
                    if ($claim) {
                        $user = $claim->shipment->user;
                        $status = CrmRequestStatus::find($status_id);
                        if (strpos($subject, '[date]') !== FALSE) {
                            $subject = str_replace('[date]', $date, $subject);
                        }
                        if (strpos($body, '[date]') !== FALSE) {
                            $body = str_replace('[date]', $date, $body);
                        }
                        if (strpos($subject, '[id]') !== FALSE) {
                            $subject = str_replace('[id]', str_pad($claim->id, 6, '0', STR_PAD_LEFT), $subject);
                        }
                        if (strpos($body, '[id]') !== FALSE) {
                            $body = str_replace('[id]', str_pad($claim->id, 6, '0', STR_PAD_LEFT), $body);
                        }
                        if (strpos($subject, '[status]') !== FALSE) {
                            $subject = str_replace('[status]', $status->name, $subject);
                        }
                        if (strpos($body, '[status]') !== FALSE) {
                            $body = str_replace('[status]', $status->name, $body);
                        }

                        $sale_person = SalePersonTag::where('user_id', $user->id)->where('status', 0)->first();
                        $to[] = $user->email;
                        $to[] = $sale_person->sales_person->email;
                        self::email($subject, $body, $to);
                    }
                } else if ($id == 200) {
                    $subject = $notification->subject;
                    $body = $notification->body;
                    $admin_user_id = $reference_1_id;
                    $admin_user = AdminUserRequest::find($admin_user_id);
                    $admin = Admin::find(374);
                    if ($admin_user) {
                        $full_name = 'Full Name: ' . $admin_user->name;
                        $department = 'Department: ' . $admin_user->depart->name;
                        $designation = 'Designation: ' . $admin_user->designation;
                        if (strpos($subject, '[admin]') !== FALSE) {
                            $subject = str_replace('[admin]', $admin->name, $subject);
                        }
                        if (strpos($body, '[admin]') !== FALSE) {
                            $body = str_replace('[admin]', $admin->name, $body);
                        }
                        if (strpos($subject, '[admin_user_name]') !== FALSE) {
                            $subject = str_replace('[admin_user_name]', $admin_user->name, $subject);
                        }
                        if (strpos($body, '[admin_user_name]') !== FALSE) {
                            $body = str_replace('[admin_user_name]', $admin_user->name, $body);
                        }
                        if (strpos($body, '[full_name]') !== FALSE) {
                            $body = str_replace('[full_name]', $full_name, $body);
                        }
                        if (strpos($body, '[department]') !== FALSE) {
                            $body = str_replace('[department]', $department, $body);
                        }
                        if (strpos($body, '[designation]') !== FALSE) {
                            $body = str_replace('[designation]', $designation, $body);
                        }
                        $to = $admin->email;
                        self::email($subject, $body, $to);
                    }
                } else if ($id == 201) {
                    $subject = $notification->subject;
                    $body = $notification->body;
                    $admin_user_id = $reference_1_id;
                    $admin_user = AdminUserRequest::find($admin_user_id);
                    $admin = Admin::find(5);
                    if ($admin_user) {
                        $full_name = 'Full Name: ' . $admin_user->name;
                        $department = 'Department: ' . $admin_user->depart->name;
                        $designation = 'Designation: ' . $admin_user->designation;
                        if (strpos($subject, '[admin]') !== FALSE) {
                            $subject = str_replace('[admin]', $admin->name, $subject);
                        }
                        if (strpos($body, '[admin]') !== FALSE) {
                            $body = str_replace('[admin]', $admin->name, $body);
                        }
                        if (strpos($subject, '[admin_user_name]') !== FALSE) {
                            $subject = str_replace('[admin_user_name]', $admin_user->name, $subject);
                        }
                        if (strpos($body, '[admin_user_name]') !== FALSE) {
                            $body = str_replace('[admin_user_name]', $admin_user->name, $body);
                        }
                        if (strpos($body, '[full_name]') !== FALSE) {
                            $body = str_replace('[full_name]', $full_name, $body);
                        }
                        if (strpos($body, '[department]') !== FALSE) {
                            $body = str_replace('[department]', $department, $body);
                        }
                        if (strpos($body, '[designation]') !== FALSE) {
                            $body = str_replace('[designation]', $designation, $body);
                        }
                        $to = $admin->email;
                        self::email($subject, $body, $to);
                    }
                } else if ($id == 202) {
                    $subject = $notification->subject;
                    $body = $notification->body;
                    $admin_user_id = $reference_1_id;
                    $admin_user = AdminUserRequest::find($admin_user_id);
                    if ($admin_user) {
                        $requested_by_admin = Admin::find($admin_user->request_added_by);
                        $trax_id = 'Trax ID: ' . $admin_user->trax_id;
                        $full_name = 'Full Name: ' . $admin_user->name;
                        $email = 'Sonic & Email ID: ' . $admin_user->email;
                        $password = 'Sonic Password: ' . $admin_user->visible_password;

                        if ($admin_user->outlook_email == 1) {
                            $outlook_password = 'Outlook Password: ' . $admin_user->visible_outlook_password;
                        } else {
                            $outlook_password = '';
                        }
                        if (strpos($subject, '[admin]') !== FALSE) {
                            $subject = str_replace('[admin]', $requested_by_admin->name, $subject);
                        }
                        if (strpos($body, '[admin]') !== FALSE) {
                            $body = str_replace('[admin]', $requested_by_admin->name, $body);
                        }
                        if (strpos($subject, '[admin_user_name]') !== FALSE) {
                            $subject = str_replace('[admin_user_name]', $admin_user->name, $subject);
                        }
                        if (strpos($body, '[admin_user_name]') !== FALSE) {
                            $body = str_replace('[admin_user_name]', $admin_user->name, $body);
                        }
                        if (strpos($body, '[trax_id]') !== FALSE) {
                            $body = str_replace('[trax_id]', $trax_id, $body);
                        }
                        if (strpos($body, '[full_name]') !== FALSE) {
                            $body = str_replace('[full_name]', $full_name, $body);
                        }
                        if (strpos($body, '[email]') !== FALSE) {
                            $body = str_replace('[email]', $email, $body);
                        }
                        if (strpos($body, '[sonic_password]') !== FALSE) {
                            $body = str_replace('[sonic_password]', $password, $body);
                        }
                        if (strpos($body, '[outlook_password]') !== FALSE) {
                            $body = str_replace('[outlook_password]', $outlook_password, $body);
                        }
                        $to = $requested_by_admin->email;

                        $admin = Admin::find(374);
                        $cc = $admin->email;
                        self::email($subject, $body, $to, $cc);
                    }
                } else if ($id == 203) {
                    $date = str_replace('00:00:00', '', $reference_2_id);
                    $subject = $notification->subject;
                    $body = $notification->body;
                    $lead_ids = $reference_1_id;
                    $html = '<table style="width:100%;">';
                    $html .= '<thead><tr>
                                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">S No.</th>
                                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Contact Person</th>
                                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Hub</th>
                                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Phone Number</th>
                                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Email</th>
                                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Requested Date</th>
                                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Message</th>';
                    $html .= '</tr></thead><tbody>';
                    $serial = 1;
                    foreach ($lead_ids as $lead_id) {
                        $lead = Lead::find($lead_id);
                        $html .= '<tr>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $lead->contact_person . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $lead->city->name . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $lead->phone_number . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $lead->email_address . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $lead->requested_date . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $lead->message . '</td>';
                        $html .= '</tr>';
                        $serial++;
                    }
                    $html .= '</tbody></table>';
                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $date, $subject);
                    }
                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $date, $body);
                    }
                    if (strpos($body, '[preview]') !== FALSE) {
                        $body = str_replace('[preview]', $html, $body);
                    }

                    $to = array();

                    $to[] = 'waqas@trax.pk';
                    // $to[] = 'nazneen.arshad@trax.pk';

                    self::email($subject, $body, $to);
                } else if ($id == 204) {
                    $date = str_replace('00:00:00', '', Carbon::today());
                    $subject = $notification->subject;
                    $body = $notification->body;
                    $leads = $reference_1_id;
                    $sale_person_id = $reference_2_id;
                    $sale_person = Admin::find($sale_person_id);
                    if ($sale_person) {
                        $html = '<table style="width:100%;">';
                        $html .= '<thead><tr>
                                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">S No.</th>
                                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Contact Person</th>
                                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Hub</th>
                                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Phone Number</th>
                                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Email</th>
                                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Requested Date</th>
                                               <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Message</th>';
                        $html .= '</tr></thead><tbody>';
                        $serial = 1;
                        foreach ($leads as $lead) {
                            $html .= '<tr>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $lead->contact_person . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $lead->city->name . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $lead->phone_number . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $lead->email_address . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $lead->requested_date . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $lead->message . '</td>';
                            $html .= '</tr>';
                            $serial++;
                        }
                        $html .= '</tbody></table>';
                        if (strpos($subject, '[date]') !== FALSE) {
                            $subject = str_replace('[date]', $date, $subject);
                        }
                        if (strpos($subject, '[date]') !== FALSE) {
                            $subject = str_replace('[date]', $date, $body);
                        }
                        if (strpos($body, '[sale_person]') !== FALSE) {
                            $body = str_replace('[sale_person]', $sale_person->name, $body);
                        }
                        if (strpos($body, '[preview]') !== FALSE) {
                            $body = str_replace('[preview]', $html, $body);
                        }

                        $to = array();

                        $to[] = $sale_person->email;

                        self::email($subject, $body, $to);
                    }
                } else if ($id == 119) {
                    $body = $notification->body;
                    $sales_person = $reference_1_id;
                    $admin_id = $reference_2_id;
                    $to = array();
                    foreach ($sales_person as $index => $person) {
                        $shipper = User::find($index);
                        if (strpos($body, '[shipper_name]') !== FALSE) {
                            $body = str_replace('[shipper_name]', $shipper->name, $body);
                        } else {
                            $body = str_replace($shipper_name, $shipper->name, $body);
                        }
                        if (strpos($body, '[new_sale_person]') !== FALSE) {
                            $body = str_replace('[new_sale_person]', $person['new_sale_person']->name, $body);
                        } else {
                            $body = str_replace($sale_person, $person['new_sale_person']->name, $body);
                        }
                        $to = $person['new_sale_person']->phone_number;
                        $shipper_name = $shipper->name;
                        $sale_person = $person['new_sale_person']->name;
                        self::sms($body, $to);
                    }
                } else if ($id == 120) {
                    $date = Carbon::yesterday()->format('Y-m-d');

                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $date, $subject);
                    }

                    if (strpos($body, '[date]') !== FALSE) {
                        $body = str_replace('[date]', $date, $body);
                    }

                    $link = '<a href="' . $reference_2_id . '" target="_blank">Report</a>';

                    if (strpos($subject, '[link]') !== FALSE) {
                        $subject = str_replace('[link]', $link, $subject);
                    }

                    if (strpos($body, '[link]') !== FALSE) {
                        $body = str_replace('[link]', $link, $body);
                    }

                    $to = array();


                    $sale_person_email = Admin::find($reference_1_id)->email;

                    if ($sale_person_email) {
                        self::email($subject, $body, $sale_person_email);
                    }
                } else if ($id == 121) {
                    $date = Carbon::yesterday()->format('Y-m-d');

                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $date, $subject);
                    }

                    if (strpos($body, '[date]') !== FALSE) {
                        $body = str_replace('[date]', $date, $body);
                    }

                    $link = '<a href="' . $reference_2_id . '" target="_blank">Report</a>';

                    if (strpos($subject, '[link]') !== FALSE) {
                        $subject = str_replace('[link]', $link, $subject);
                    }

                    if (strpos($body, '[link]') !== FALSE) {
                        $body = str_replace('[link]', $link, $body);
                    }

                    $to = array();

                    $rm = Admin::find($reference_1_id)->email;

                    if ($rm) {
                        self::email($subject, $body, $rm);
                    }
                } else if ($id == 122) {
                    $date = Carbon::yesterday()->format('Y-m-d');

                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $date, $subject);
                    }

                    if (strpos($body, '[date]') !== FALSE) {
                        $body = str_replace('[date]', $date, $body);
                    }

                    $link = '<a href="' . $reference_2_id . '" target="_blank">Report</a>';

                    if (strpos($subject, '[link]') !== FALSE) {
                        $subject = str_replace('[link]', $link, $subject);
                    }

                    if (strpos($body, '[link]') !== FALSE) {
                        $body = str_replace('[link]', $link, $body);
                    }

                    $to = array();

                    $sale_person_email = Admin::find($reference_1_id)->email;

                    if ($sale_person_email) {
                        self::email($subject, $body, $sale_person_email);
                    }
                } else if ($id == 123) {
                    $date = Carbon::yesterday()->format('Y-m-d');

                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $date, $subject);
                    }

                    if (strpos($body, '[date]') !== FALSE) {
                        $body = str_replace('[date]', $date, $body);
                    }

                    $link = '<a href="' . $reference_2_id . '" target="_blank">Report</a>';

                    if (strpos($subject, '[link]') !== FALSE) {
                        $subject = str_replace('[link]', $link, $subject);
                    }

                    if (strpos($body, '[link]') !== FALSE) {
                        $body = str_replace('[link]', $link, $body);
                    }

                    $to = array();

                    $rm = Admin::find($reference_1_id)->email;

                    if ($rm) {
                        self::email($subject, $body, $rm);
                    }
                } else if ($id == 124) {
                    $date = Carbon::yesterday()->format('Y-m-d');

                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $date, $subject);
                    }

                    if (strpos($body, '[date]') !== FALSE) {
                        $body = str_replace('[date]', $date, $body);
                    }

                    $link = '<a href="' . $reference_2_id . '" target="_blank">Report</a>';

                    if (strpos($subject, '[link]') !== FALSE) {
                        $subject = str_replace('[link]', $link, $subject);
                    }

                    if (strpos($body, '[link]') !== FALSE) {
                        $body = str_replace('[link]', $link, $body);
                    }

                    $to = array();

                    $sale_person_email = Admin::find($reference_1_id)->email;

                    if ($sale_person_email) {
                        self::email($subject, $body, $sale_person_email);
                    }
                } else if ($id == 125) {
                    $date = Carbon::yesterday()->format('Y-m-d');

                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $date, $subject);
                    }

                    if (strpos($body, '[date]') !== FALSE) {
                        $body = str_replace('[date]', $date, $body);
                    }

                    $link = '<a href="' . $reference_2_id . '" target="_blank">Report</a>';

                    if (strpos($subject, '[link]') !== FALSE) {
                        $subject = str_replace('[link]', $link, $subject);
                    }

                    if (strpos($body, '[link]') !== FALSE) {
                        $body = str_replace('[link]', $link, $body);
                    }

                    $to = array();

                    $rm = Admin::find($reference_1_id)->email;

                    if ($rm) {
                        self::email($subject, $body, $rm);
                    }
                } else if ($id == 126) {
                    $shipment = Shipment::find($reference_1_id);
                    $phone = $shipment->consignee_phone_number_1;
                    $tracking_number = $shipment->tracking_number;
                    $city = $shipment->consignee_city;
                    if ($city->location_latitude != null && $city->location_longitude != null) {
                        $lat = $city->location_latitude;
                        $long = $city->location_longitude;
                        $location = 'www.google.com/maps/place/' . $lat . ',' . $long;
                    } else {
                        $location = '-';
                    }
                    if (strpos($body, '[tracking_number]') !== FALSE) {
                        $body = str_replace('[tracking_number]', $tracking_number, $body);
                    }
                    if (strpos($body, '[location]') !== FALSE) {
                        $body = str_replace('[location]', $location, $body);
                    }
                    $to = $phone;
                    self::sms($body, $to);
                } else if ($id == 127) {
                    $date = Carbon::yesterday()->format('Y-m-d');

                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $date, $subject);
                    }

                    $group_logs = ActivityTrailLog::leftjoin('admins as a', 'a.id', '=', 'activity_trail_logs.admin_id')
                        ->leftjoin('admin_roles as ar', 'ar.id', '=', 'a.role_id')
                        ->leftjoin('admin_departments as ad', 'ad.id', '=', 'ar.department_id')
                        ->leftjoin('activity_trail_actions as ata', 'ata.id', '=', 'activity_trail_logs.action_id')
                        ->select('ad.name as department', 'activity_trail_logs.id as id', 'a.name as name', 'a.designation as designation', 'ata.screen_name as screen_name', 'ata.action as action', 'activity_trail_logs.created_at as created_at', 'ar.department_id as department_id')
                        ->where('emailed', 0)
                        ->get()
                        ->groupBy('department_id');

                    foreach ($group_logs as $key => $logs) {
                        $body = $notification->body;
                        $head_role_id = AdminRole::where('department_id', $key)
                            ->whereIn('id', ActivityTrailController::$department_head_ids)
                            ->pluck('id')
                            ->first();

                        if ($head_role_id == null) {
                            echo "No Department Head is found for department " . $logs[0]->department . " | ";
                            //                            foreach ($logs as $index => $log) {
                            //                                ActivityTrailLog::find($log->id)
                            //                                    ->update(['emailed' => 1]);
                            //                            }
                        } else {
                            $heads = Admin::where('role_id', $head_role_id)->get();

                            if (count($heads) > 1) {
                                $name = "Concern Head";
                            } else {
                                $name = $heads[0]->name;
                            }
                            if (strpos($body, '[contact_person]') !== FALSE) {
                                $body = str_replace('[contact_person]', $name, $body);
                            }

                            $preview = '<table style="width:100%;">';
                            $preview .= '<thead><tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">S No.</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Team Member Name</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Designation</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Screen Name</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Action Performed</th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Action Performed Time</th></tr></thead>';
                            $preview .= '<tbody>';
                            foreach ($logs as $index => $log) {
                                $preview .= '<tr>';
                                $preview .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . ($index + 1) . '</td>';
                                $preview .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $log->name . '</td>';
                                $preview .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $log->designation . '</td>';
                                $preview .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $log->screen_name . '</td>';
                                $preview .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $log->action . '</td>';
                                $preview .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $log->created_at . '</td>';
                                $preview .= '</tr>';
                                ActivityTrailLog::find($log->id)
                                    ->update(['emailed' => 1]);
                            }

                            $preview .= '</tbody></table>';
                            if (strpos($body, '[preview]') !== FALSE) {
                                $body = str_replace('[preview]', $preview, $body);
                            }

                            $to = array();

                            foreach ($heads as $head) {
                                array_push($to, $head->email);
                            }

                            self::email($subject, $body, $to);
                            echo "Email for department " . $log->department . " is sent successfully | ";
                        }
                    }
                } else if ($id == 128) {
                    $master_cargo_id = $reference_1_id;
                    if ($master_cargo_id) {

                        $master_cargo = MasterCargo::find($master_cargo_id);


                        $destination = $master_cargo->destination_hub_id;
                        $html = '<table style="width:100%;">';
                        $html .= '<thead><tr>
                                       <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">S No.</th>
                                       <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Master Cargo Number</th>
                                       <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Bags</th>
                                       <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Shipments</th>
                                       <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Origin</th>
                                       <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Destination</th>
                                       <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Junction 1</th>
                                       <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Junction 2</th>
                                       <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Actual Weight</th>';
                        $html .= '</tr></thead><tbody>';

                        $serial = 1;
                        $html .= '<tr>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $master_cargo->id . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $master_cargo->bags . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $master_cargo->shipments . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $master_cargo->origin_hub['name'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $master_cargo->destination_hub['name'] . '</td>';

                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $master_cargo->junction_hub_1['name'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $master_cargo->junction_hub_2['name'] . '</td>';

                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $master_cargo->actual_weight . '</td>';
                        $html .= '</tr>';

                        $html .= '</tbody></table> <br>';


                        //juction table


                        $html .= '<table style="width:100%;">';
                        $html .= '<thead><tr>
                                       <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">S No.</th>
                                       <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Junctions</th>';
                        $html .= '</tr></thead><tbody>';

                        $serial = 1;

                        if ($master_cargo->route_management_id) {

                            foreach ($master_cargo->route_management->junctions as $value) {
                                $html .= '<tr>';
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial . '</td>';
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $value->junction['name'] . '</td>';
                                $html .= '</tr>';
                                $serial++;
                            }
                        }
                        $html .= '</tbody></table>';

                        if (strpos($body, '[preview]') !== FALSE) {
                            $body = str_replace('[preview]', $html, $body);
                        }

                        $admins = Admin::where('role_id', 10)->where('status', 1)->get();
                        foreach ($admins as $admin) {
                            if ($master_cargo->route_management_id) {

                                foreach ($master_cargo->route_management->junctions as $value) {
                                    $assign_hubs = AdminHub::where('admin_id', $admin->id)->where('hub_id', $value->junction['id']);
                                    if ($assign_hubs->exists()) {
                                        $to = $admin->email;
                                    }
                                }
                            }
                            if ($to != null) {
                                self::email($subject, $body, $to);
                            }
                            // if ($master_cargo->junction_hub_1_id != null) {
                            //     $assign_hubs = AdminHub::where('admin_id', $admin->id)->where('hub_id', $master_cargo->junction_hub_1_id);
                            //     if ($assign_hubs->exists()) {
                            //         $to = $admin->email;
                            //         self::email($subject, $body, $to);
                            //     }
                            // }

                            // if ($master_cargo->junction_hub_2_id != null) {
                            //     $assign_hubs = AdminHub::where('admin_id', $admin->id)->where('hub_id', $master_cargo->junction_hub_2_id);
                            //     if ($assign_hubs->exists()) {
                            //         $to = $admin->email;
                            //         self::email($subject, $body, $to);
                            //     }
                            // }

                            if ($master_cargo->destination_hub_id) {
                                $assign_hubs = AdminHub::where('admin_id', $admin->id)->where('hub_id', $master_cargo->destination_hub_id);
                                if ($assign_hubs->exists()) {
                                    $to = $admin->email;
                                    self::email($subject, $body, $to);
                                }
                            }
                        }
                    }
                } else if ($id == 129) {
                    $retail_user = $reference_1_id;
                    $otp = $reference_2_id;
                    if (strpos($body, '[name]') !== FALSE) {
                        $body = str_replace('[name]', $retail_user->name, $body);
                    }
                    if (strpos($body, '[code]') !== FALSE) {
                        $body = str_replace('[code]', $otp, $body);
                    }
                    $to = $retail_user->phone_no;
                    self::sms($body, $to, 1);
                } else if ($id == 130) {
                    $file_path = $reference_1_id['file_path'];
                    $from = $reference_1_id['from'];
                    $to = $reference_1_id['to'];
                    $subject = 'Revenue Monthly Report By Arrival Date  | ';

                    $subject .= 'From ( ' . $from . ' - ' . $to . ' )';

                    $file = Storage::disk('public')->url($file_path);
                    $link = '<a href="' . $file . '" target="_blank"><u>Download</u></a>';

                    if (strpos($body, '[link]') !== FALSE) {
                        $body = str_replace('[link]', $link, $body);
                    }

                    $to = ['shafay.tariq@trax.pk', 'adnan.ahsan@trax.pk', 'fawad.ahmed@trax.pk', 'hammad.majid@trax.pk'];

                    $cc = ["muhammad.waqas@trax.pk", "danish.zahid@trax.pk"];

                    self::email($subject, $body, $to, $cc);
                } else if ($id == 133) {

                    $data = $reference_1_id;
                    $erf = EmployeeRequisition::find($data['id']);
                    $erf_id = str_pad($erf->id, 6, 0, STR_PAD_LEFT);
                    $admin_emails = explode(',', $data['email']);

                    foreach ($admin_emails as $email) {
                        $admin = Admin::where('email', $email)->first();
                        if ($admin) {
                            $link = '<a href="' . $reference_2_id . '" target="_blank">Report</a>';

                            if (strpos($body, '[link]') !== FALSE) {
                                $body = str_replace('[link]', $link, $body);
                            }

                            if (strpos($subject, '[erf_id]') !== FALSE) {
                                $subject = str_replace('[erf_id]', $erf_id, $subject);
                            }

                            if (strpos($body, '[erf_id]') !== FALSE) {
                                $body = str_replace('[erf_id]', $erf_id, $body);
                            }
                            if (strpos($body, '[admin]') !== FALSE) {
                                $body = str_replace('[admin]', $admin->name, $body);
                            }
                            if (strpos($body, '[date]') !== FALSE) {
                                $body = str_replace('[date]', $erf->created_at, $body);
                            }

                            $to = $email;
                            self::email($subject, $body, $to);
                        }
                    }
                } else if ($id == 136) {
                    $shipper_id = $reference_1_id;
                    $crm_comment_id = $reference_2_id;

                    $user = User::find($shipper_id);
                    $crm_comment = CrmComments::find($crm_comment_id);

                    if (strpos($body, '[shipper]') !== FALSE) {
                        $body = str_replace('[shipper]', $user->name, $body);
                    }

                    if (strpos($body, '[message]') !== FALSE) {
                        $body = str_replace('[message]', $crm_comment->comment, $body);
                    }

                    $to = $user->email;
                    self::email($subject, $body, $to);
                } else if ($id == 132) {
                    $delivery_note_fields = ['delivery_note_number' => 'id', 'departure_at' => 'created_at'];

                    $shipment_fields = [
                        'consignee_name' => 'consignee_name',
                        'consignee_address' => 'consignee_address',
                        'order_id' => 'order_id',
                        'amount' => 'amount',
                        'tracking_number' => 'tracking_number'
                    ];

                    $delivery_note = DeliveryNote::find($reference_1_id);

                    $delivery_note_shipment = DeliveryNoteShipment::where('delivery_note_id', $reference_1_id)
                        ->where('shipment_id', $reference_2_id)->first();

                    $shipment = Shipment::find($reference_2_id);
                    $shipment_otp = ShipmentOtp::where('shipment_id', $shipment->id);
                    //yap sms
                    if ($shipment->user_id == 12613) {
                        $refusal_otp = '';
                        $rider = '';
                        if ($shipment_otp->exists()) {
                            $shipment_otp = $shipment_otp->first();

                            $refusal_otp = $shipment_otp->otp;
                        }
                        if ($delivery_note->special_rider) {
                            if (strpos($body, '[rider]') !== FALSE) {
                                if ($delivery_note_shipment->rider_information) {
                                    $rider = substr(preg_replace('/[^A-Za-z0-9 ]/', '', $delivery_note->special_rider_name), 0, 20);
                                }
                            }
                        } else {
                            if (strpos($body, '[rider]') !== FALSE) {
                                if ($delivery_note_shipment->rider_information) {
                                    $rider = substr(preg_replace('/[^A-Za-z0-9 ]/', '', $delivery_note->rider->name), 0, 20);
                                }
                            }
                        }
                        $body = 'Your YAP debit card is on route and will be delivered between [Start Time] – [End time] Please keep your CNIC ready for verification purposes.' . PHP_EOL . PHP_EOL . 'AWB: ' . $shipment->tracking_number . PHP_EOL . PHP_EOL . 'Rider: ' . $rider . PHP_EOL . PHP_EOL . 'Refusal OTP: ' . $refusal_otp . PHP_EOL . PHP_EOL . 'Helpline: 021-111-118-729';
                        $to = $shipment->consignee_phone_number_1;

                        self::sms($body, $to);
                        //yap sms end
                    } else {

                        $shipper = $shipment->user;

                        $to = $shipment->consignee_phone_number_1;

                        foreach ($delivery_note_fields as $key => $field) {
                            if (strpos($body, '[' . $key . ']') !== FALSE) {
                                if ($key == 'delivery_note_number') {
                                    $body = str_replace('[' . $key . ']', str_pad($delivery_note[$field], 6, '0', STR_PAD_LEFT), $body);
                                } else {
                                    $body = str_replace('[' . $key . ']', $delivery_note[$field], $body);
                                }
                            }
                        }

                        foreach ($shipment_fields as $key => $field) {

                            if (strpos($body, '[' . $key . ']') !== FALSE) {

                                if ($shipment['consignee_name']) {
                                    $first_name = explode(' ', trim($shipment['consignee_name']));
                                    $shipment['consignee_name'] = $first_name[0];
                                }

                                $body = str_replace('[' . $key . ']', $shipment[$field], $body);
                            }
                        }
                        if ($delivery_note->special_rider) {
                            if (strpos($body, '[rider]') !== FALSE) {
                                if ($delivery_note_shipment->rider_information) {
                                    $body = str_replace('[rider]', str_replace('-', '', $delivery_note->special_rider_phone), $body);
                                } else {
                                    $body = str_replace('[rider]', '', $body);
                                }
                            }
                        } else {
                            if (strpos($body, '[rider]') !== FALSE) {
                                if ($delivery_note_shipment->rider_information) {
                                    $body = str_replace('[rider]', str_replace('-', '', $delivery_note->rider->phone), $body);
                                } else {
                                    $body = str_replace('[rider]', '', $body);
                                }
                            }
                        }

                        if ($shipment->pickup_address->pickup_brand_name != NULL) {
                            $brand_name = $shipment->pickup_address->pickup_brand_name;
                        } else {
                            if ($shipper->brand_name != NULL) {
                                $brand_name = $shipper->brand_name;
                            } else {
                                $brand_name = $shipper->name;
                            }
                        }
                        if (strpos($body, '[company_name]') !== FALSE) {
                            $body = str_replace('[company_name]', substr(preg_replace('/[^A-Za-z0-9 ]/', '', $brand_name), 0, 25), $body);
                        }

                        if (strpos($body, '[payment_mode]') !== FALSE) {
                            $body = str_replace('[payment_mode]', $shipment->payment_mode->mode, $body);
                        }

                        if ($shipment_otp->exists()) {
                            $shipment_otp = $shipment_otp->first();
                            if (strpos($body, '[refusal_otp]') !== FALSE) {
                                $body = str_replace('[refusal_otp]', $shipment_otp->otp, $body);
                            }
                        }
                        self::sms($body, $to);
                    }
                } else if ($id == 134) {
                    $user = User::find($reference_1_id);
                    $shipments = Shipment::where('user_id', $reference_1_id)
                        ->where('shipper_status_id', 1)
                        ->whereBetween('created_at', [Carbon::now()->subHours(48), Carbon::now()->subHours(47)]);
                    if ($shipments->exists()) {
                        $shipments = $shipments->get();
                        $html = '';

                        $subject = $notification->subject;
                        $body = $notification->body;
                        if (strpos($body, '[company_name]') !== FALSE) {
                            $body = str_replace('[company_name]', 'Khaddi', $body);
                        }
                        $html .= '<table style="width:100%;">';
                        $html .= '<thead><tr>
                                <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">S No.</th>';
                        $html .= '<th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Shipment Booked Date</th>';
                        $html .= '<th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Tracking No.</th>';
                        $html .= '<th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Pickup Address</th>';
                        $html .= '<th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Status</th>';
                        $html .= '</tr></thead><tbody>';
                        $serial = 0;
                        $to = array();

                        foreach ($user->shipping as $user_shipping_info) {
                            $pickup_body = $notification->body;
                            if (strpos($pickup_body, '[company_name]') !== FALSE) {
                                $pickup_body = str_replace('[company_name]', 'Khaddi', $pickup_body);
                            }
                            $pickup_html = '';
                            $pickup_html .= '<table style="width:100%;">';
                            $pickup_html .= '<thead><tr>
                                <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">S No.</th>';
                            $pickup_html .= '<th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Shipment Booked Date</th>';
                            $pickup_html .= '<th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Tracking No.</th>';
                            $pickup_html .= '<th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Pickup Address</th>';
                            $pickup_html .= '<th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Status</th>';
                            $pickup_html .= '</tr></thead><tbody>';
                            $pickup_address_to = '';
                            foreach ($shipments as $shipment) {
                                if ($user_shipping_info->id == $shipment->pickup_address_id) {
                                    $pickup_html .= '<tr>';

                                    $pickup_html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . ++$serial . '</td>';
                                    $pickup_html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment->created_at->toDateString() . '</td>';
                                    $pickup_html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment->tracking_number . '</td>';

                                    $pickup_address = UserShippingInfo::where('id', $user_shipping_info->id)->where('status', 1);

                                    if ($pickup_address->exists()) {
                                        $pickup_html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $pickup_address->first()->pickup_address . '</td>';
                                        $pickup_address_to = $user_shipping_info->email;
                                    }
                                    $pickup_html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">Booked</td>';

                                    $pickup_html .= '</tr>';
                                }
                            }
                            $pickup_html .= '</table>';
                            if (strpos($pickup_body, '[preview]') !== FALSE) {
                                $pickup_body = str_replace('[preview]', $pickup_html, $pickup_body);
                            }
                            if (!empty($pickup_address_to) || $pickup_address_to != '') {

                                self::email($subject, $pickup_body, $pickup_address_to);
                            }
                        }


                        foreach ($shipments as $shipment) {
                            $html .= '<tr>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . ++$serial . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment->created_at->toDateString() . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment->tracking_number . '</td>';
                            $pickup_address = UserShippingInfo::where('id', $shipment->pickup_address_id)->where('status', 1);
                            if ($pickup_address->exists()) {
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $pickup_address->first()->pickup_address . '</td>';
                            }
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">Booked</td>';
                            $html .= '</tr>';
                        }
                        $to = array_merge($to, User::where('id', $user->id)->pluck('email')->toArray());
                        $html .= '</table>';
                        if (strpos($body, '[preview]') !== FALSE) {
                            $body = str_replace('[preview]', $html, $body);
                        }
                        self::email($subject, $body, $to);
                        $to = array_unique($to);
                    }
                } else if ($id == 135) {
                    $delivery_note_fields = ['delivery_note_number' => 'id', 'departure_at' => 'created_at'];

                    $shipment_fields = ['consignee_name' => 'consignee_name', 'consignee_address' => 'consignee_address', 'order_id' => 'order_id', 'amount' => 'amount', 'tracking_number' => 'tracking_number'];

                    $delivery_note = DeliveryNote::find($reference_1_id);

                    $delivery_note_shipment = DeliveryNoteShipment::where('delivery_note_id', $reference_1_id)->where('shipment_id', $reference_2_id)->first();

                    $shipment = Shipment::find($reference_2_id);
                    $shipment_otp = ShipmentOtp::where('shipment_id', $shipment->id);
                    $shipper = $shipment->user;

                    $to = $shipment->consignee_phone_number_1;

                    foreach ($delivery_note_fields as $key => $field) {
                        if (strpos($body, '[' . $key . ']') !== FALSE) {
                            if ($key == 'delivery_note_number') {
                                $body = str_replace('[' . $key . ']', str_pad($delivery_note[$field], 6, '0', STR_PAD_LEFT), $body);
                            } else {
                                $body = str_replace('[' . $key . ']', $delivery_note[$field], $body);
                            }
                        }
                    }

                    foreach ($shipment_fields as $key => $field) {
                        if (strpos($body, '[' . $key . ']') !== FALSE) {

                            if ($shipment['consignee_name']) {
                                $first_name = explode(' ', trim($shipment['consignee_name']));
                                $shipment['consignee_name'] = $first_name[0];
                            }
                            $body = str_replace('[' . $key . ']', $shipment[$field], $body);
                        }
                    }
                    if ($delivery_note->special_rider) {
                        if (strpos($body, '[rider]') !== FALSE) {
                            if ($delivery_note_shipment->rider_information) {
                                $body = str_replace('[rider]', str_replace('-', '', $delivery_note->special_rider_phone), $body);
                            } else {
                                $body = str_replace('[rider]', '', $body);
                            }
                        }
                    } else {
                        if (strpos($body, '[rider]') !== FALSE) {
                            if ($delivery_note_shipment->rider_information) {
                                $body = str_replace('[rider]', str_replace('-', '', $delivery_note->rider->phone), $body);
                            } else {
                                $body = str_replace('[rider]', '', $body);
                            }
                        }
                    }

                    if ($shipment->pickup_address->pickup_brand_name != NULL) {
                        $brand_name = $shipment->pickup_address->pickup_brand_name;
                    } else {
                        if ($shipper->brand_name != NULL) {
                            $brand_name = $shipper->brand_name;
                        } else {
                            $brand_name = $shipper->name;
                        }
                    }
                    if (strpos($body, '[company_name]') !== FALSE) {
                        $body = str_replace('[company_name]', substr(preg_replace('/[^A-Za-z0-9 ]/', '', $brand_name), 0, 25), $body);
                    }

                    if (strpos($body, '[payment_mode]') !== FALSE) {
                        $body = str_replace('[payment_mode]', $shipment->payment_mode->mode, $body);
                    }
                    if ($shipment_otp->exists()) {
                        $shipment_otp = $shipment_otp->first();
                        if (strpos($body, '[refusal_otp]') !== FALSE) {
                            $body = str_replace('[refusal_otp]', $shipment_otp->otp, $body);
                        }
                    }
                    self::sms($body, $to);
                } else if ($id == 137) {
                    $rider_id = $reference_1_id;
                    $delivery_note_id = $reference_2_id;

                    $rider = Rider::find($rider_id);
                    $delivery_note = DeliveryNote::find($delivery_note_id);

                    if (strpos($body, '[rider_name]') !== FALSE) {
                        $body = str_replace('[rider_name]', $rider->name, $body);
                    }

                    if (strpos($body, '[delivery_note]') !== FALSE) {
                        $body = str_replace('[delivery_note]', $delivery_note->id, $body);
                    }

                    if (strpos($body, '[otp]') !== FALSE) {
                        $body = str_replace('[otp]', $delivery_note->otp, $body);
                    }

                    $to = $rider->phone;
                    self::delivery_note_otp_sms($body, $to);
                } else if ($id == 138) {
                    $admin = $reference_1_id;
                    $otp = $reference_2_id['otp'];
                    $name = '';
                    if (strpos($body, '[name]') !== FALSE) {
                        $body = str_replace('[name]', $admin->name, $body);
                        $name = $admin->name;
                    }
                    if (strpos($body, '[code]') !== FALSE) {
                        $body = str_replace('[code]', $otp, $body);
                    }
                    $to = $reference_2_id['phone_number'];
                    // self::sms($body, $to, 1);

                    self::sms_otp($body, $to, $name, $otp, 1);
                } else if ($id == 139) {
                    $yesterday = Carbon::yesterday();
                    $today = Carbon::today();

                    $rider_pickups = V2RiderPickup::leftjoin('v2_pickup_request_not_pick_reasons as pnpr', 'v2_rider_pickups.pickup_not_pick_reason_id', 'pnpr.id')
                        ->join('v2_pickup_notes as pn', 'v2_rider_pickups.pickup_note_id', 'pn.id')
                        ->join('v2_pickup_requests as pr', 'v2_rider_pickups.pickup_request_id', 'pr.id')
                        ->join('riders as r', 'pn.rider_id', 'r.id')
                        ->join('users as u', 'pr.shipper_id', 'u.id')
                        ->join('user_shipping_infos as usi', 'pr.pickup_address_id', 'usi.id')
                        ->join('cities as c', 'usi.city_id', 'c.id')
                        ->select('v2_rider_pickups.id', 'r.name as rider', 'r.id as rider_id', 'v2_rider_pickups.shipments as shipments', 'c.name as origin_hub', 'c.hub_id as hub_id')
                        ->whereBetween('v2_rider_pickups.created_at', [$yesterday, $today]);

                    if ($rider_pickups->exists()) {
                        $rider_pickups = $rider_pickups->get();
                        $hubss = DB::connection('reports')->table('cities')->where('hub', 1)->select('id', 'name')->get();
                        foreach ($hubss as $hub) {

                            $body_updated = $body;
                            $html = '<table style="width:100%;">';
                            $html .= '<thead><tr>
                                                   <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">S No.</th>
                                                   <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Rider Name</th>
                                                   <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Rider ID</th>
                                                   <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Shipments</th>
                                                   <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Origin Hub</th>';
                            $html .= '</tr></thead><tbody>';

                            $serial = 1;

                            // $hubs = array();
                            $hub_id = null;
                            foreach ($rider_pickups as $rider_pickup) {
                                if ($hub->id == $rider_pickup->hub_id) {

                                    $html .= '<tr>';
                                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial++ . '</td>';
                                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $rider_pickup->rider . '</td>';
                                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $rider_pickup->rider_id . '</td>';
                                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $rider_pickup->shipments . '</td>';
                                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $rider_pickup->origin_hub . '</td>';
                                    $html .= '</tr>';

                                    $hub_id = $rider_pickup->hub_id;
                                }
                            }
                            $html .= '</tbody></table> <br>';
                            if (strpos($body_updated, '[preview]') !== FALSE) {
                                $body_updated = str_replace('[preview]', $html, $body_updated);
                            }
                            $to = array();
                            if ($hub_id != null) {
                                $admins = Admin::join('admin_hubs', 'admin_hubs.admin_id', '=', 'admins.id')
                                    ->whereIn('admins.role_id', [8, 9])->where('admins.status', 1)
                                    ->where('admin_hubs.hub_id', $hub_id);
                                // $admins = Admin::whereIn('admins.role_id', [8, 9])->where('admins.status', 1);
                                if ($admins->exists()) {
                                    $to = array_merge($to, $admins->pluck('email')->toArray());
                                }

                                self::email($subject, $body_updated, $to);
                            }
                        }
                    }
                } else if ($id == 140) {
                    $date = $reference_1_id;
                    $deliveries = Rider::join('cities', 'cities.id', '=', 'riders.city_id')
                        ->join('delivery_notes', function ($join) {
                            $join->on('delivery_notes.rider_id', '=', 'riders.id')
                                ->where('delivery_notes.created_at', '=', DB::raw('(select max(created_at) from delivery_notes where delivery_notes.rider_id= riders.id)'));
                        })
                        ->select('riders.id as rider_id', 'riders.name as rider_name', 'cities.name as city_name', 'delivery_notes.created_at as rider_delivery_created', 'riders.phone as phone_no', 'riders.cnic as cnic_no', 'rider_type_id as rider_type')
                        ->whereDate('delivery_notes.created_at', '<=', $date)
                        ->where('riders.status', 1)
                        ->groupBy('rider_id')
                        ->get();
                    $html = '<p>Dear Concern,
                            Please Find below the details of delivery riders (Active) whose delivery sheets were not created since past 2 or more days.</p>';

                    $html .= '<table style="width:100%;">';
                    $html .= '<thead><tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Rider ID</th>';
                    $html .= '<th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Rider Name</th>';
                    $html .= '<th style="padding:5px; border: 1px solid black; border-collapse: collapse;">City</th>';
                    $html .= '<th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Phone Number</th>';
                    $html .= '<th style="padding:5px; border: 1px solid black; border-collapse: collapse;">CNIC Number</th>';
                    $html .= '<th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Rider Type</th>';
                    $html .= '</tr></thead><tbody>';
                    $a = 0;

                    foreach ($deliveries as $delivery) {
                        if ($delivery->rider_type == 2) {
                            $type = "Incentive";
                        } else {
                            $type = "Permanent";
                        }
                        $html .= '<tr>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $delivery->rider_id . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $delivery->rider_name . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $delivery->city_name . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $delivery->phone_no . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $delivery->cnic_no . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $type . '</td>';
                    }
                    $html .= '</tr></tbody></table>';


                    $body_updated = $body;
                    $body_updated = str_replace('[preview]', $html, $body_updated);
                    $subject = 'Inactive Rider For 2 Days or More ';
                    $to = ['talha.motiwala@trax.pk', 'wasiq.edhi@trax.pk', 'rameel.khan@trax.pk', 'abdul.ahad@trax.pk', 'fahad.ahmed@trax.pk', 'fabiha.shahid@trax.pk'];
                    self::email($subject, $body_updated, $to);
                } else if ($id == 141) {
                    $retail_done_payment_report = RetailDonePaymentsReport::get();
                    if ($retail_done_payment_report) {
                        $date = Carbon::today()->format('Y-m-d');
                        $subject = $notification->subject;
                        $body = $notification->body;
                        if (strpos($subject, '[date]') !== FALSE) {
                            $subject = str_replace('[date]', $date, $subject);
                        }

                        if (strpos($body, '[date]') !== FALSE) {
                            $body = str_replace('[date]', $date, $body);
                        }

                        $link = '<a href="' . $reference_2_id . '" target="_blank">Report</a>';

                        if (strpos($subject, '[link]') !== FALSE) {
                            $subject = str_replace('[link]', $link, $subject);
                        }

                        if (strpos($body, '[link]') !== FALSE) {
                            $body = str_replace('[link]', $link, $body);
                        }
                        $summary_html = '<div style="margin-bottom: 100px;"><table style="width:100%;">';
                        $summary_html .= '<thead><tr>
                                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Total Shippers</th>
                                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Total Amount</th>
                                           </tr></thead><tbody>';
                        $shippers = array();
                        $html = '<table style="width:100%;">';
                        $html .= '<thead><tr>
                                            <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">S No.</th>
                                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Payment ID</th>
                                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Shipper Name</th>
                                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">IBAN Number</th>
                                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Amount</th>
                                           </tr></thead><tbody>';
                        $total_amount = 0;
                        $serial = 1;
                        foreach ($retail_done_payment_report as $retail_done_payment) {
                            if (!in_array($retail_done_payment->shipper_id, $shippers)) {
                                $shippers[$retail_done_payment->shipper_id] = $retail_done_payment->shipper_id;
                            }
                            $html .= '<tr>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . str_pad($retail_done_payment->payment_id, 6, '0', STR_PAD_LEFT) . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $retail_done_payment->shipper_name . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $retail_done_payment->iban_number . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format($retail_done_payment->amount) . '</td>';
                            $html .= '</tr>';
                            $total_amount = $total_amount + $retail_done_payment->amount;
                            $serial++;
                        }
                        $html .= '<tr>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">Total</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"></td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"></td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"></td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format($total_amount) . '</td>';
                        $html .= '</tr>';
                        $html .= '</tbody></table>';

                        $summary_html .= '<tr>';
                        $summary_html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . count($shippers) . '</td>';
                        $summary_html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format($total_amount) . '</td>';
                        $summary_html .= '</tr>';
                        $summary_html .= '</tbody></table></div>';

                        $html = $summary_html . $html;
                        if (strpos($body, '[preview]') !== FALSE) {
                            $body = str_replace('[preview]', $html, $body);
                        }

                        $to = array();
                        $bcc = array();
                        $to[] = 'hassan@trax.pk';
                        $to[] = 'fawad.ahmed@trax.pk';
                        $to[] = 'shafay.tariq@trax.pk';
                        $to[] = 'wajiha.majeed@trax.pk';
                        $to[] = 'huzaifa.aamir@trax.pk';
                        $to[] = 'mohsin.khan@trax.pk';
                        $bcc[] = 'muhammad.waqas@trax.pk';
                        $bcc[] = 'danish.zahid@trax.pk';

                        self::email($subject, $body, $to, NULL, $bcc);
                    }
                } else if ($id == 142) {

                    $crm_request = CrmRequest::find($reference_1_id);
                    if ($crm_request) {
                        if ($crm_request->shipment_id) {
                            $shipment = Shipment::find($crm_request->shipment_id);
                            if (strpos($body, '[request_id]') !== FALSE) {
                                $body = str_replace('[request_id]', $crm_request->id, $body);
                            }
                            if (strpos($body, '[tracking_number]') !== FALSE) {
                                $body = str_replace('[tracking_number]', $shipment->tracking_number, $body);
                            }
                            if (strpos($body, '[shipper_name]') !== FALSE) {
                                $body = str_replace('[shipper_name]', $shipment->user->name, $body);
                            }
                            if (strpos($body, '[email]') !== FALSE) {
                                $body = str_replace('[email]', $shipment->user->email, $body);
                            }
                            if (strpos($body, '[phone]') !== FALSE) {
                                $body = str_replace('[phone]', $shipment->user->phone, $body);
                            }
                            if (strpos($body, '[destination]') !== FALSE) {
                                $body = str_replace('[destination]', $shipment->consignee_city->name, $body);
                            }
                            if (strpos($body, '[channel]') !== FALSE) {
                                $body = str_replace('[channel]', $crm_request->channel->channel, $body);
                            }
                            if (strpos($body, '[case_nature]') !== FALSE) {
                                $body = str_replace('[case_nature]', $crm_request->nature->name, $body);
                            }
                            if (strpos($body, '[case_nature_type]') !== FALSE) {
                                $body = str_replace('[case_nature_type]', $crm_request->nature->type, $body);
                            }
                            if (strpos($body, '[status]') !== FALSE) {
                                $body = str_replace('[status]', $crm_request->request_status->name, $body);
                            }
                            if (strpos($body, '[details]') !== FALSE) {
                                $body = str_replace('[details]', $crm_request->description, $body);
                            }
                            $to = $shipment->user->phone;
                            self::sms($body, $to);
                        }
                    }
                } else if ($id == 143) {
                    $shipment = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
                        ->join('sale_tier_tags as stt', function ($join) {
                            $join->on('stt.user_id', 'u.id');
                        })
                        ->join('admins as a', 'a.id', 'stt.kam')
                        ->select('u.name as username', 'u.id as userid', 'a.name as adminname', 'a.email as email')
                        ->where('shipments.shipper_status_id', 20)
                        ->groupBy('userid')
                        ->get();


                    foreach ($shipment as $data) {

                        $getdata = Shipment::where('user_id', '=', $data->userid)->where('shipments.shipper_status_id', 20)->get();
                        $html = '<b>Shipper Name is :' . $data->username . '</b>';
                        $html .= '<table style="width:100%;">';
                        $html .= '<thead><tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Tracking Number</th>';
                        $html .= '<th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Data/Time</th>';
                        $html .= '</tr></thead><tbody>';

                        foreach ($getdata as $value) {
                            $html .= '<tr>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $value->tracking_number . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $value->updated_at . '</td>';
                        }
                        $html .= '</tr></tbody></table>';
                        $body_updated = $body;
                        $body_updated = str_replace('[preview]', $html, $body_updated);
                        $subject = 'Return Confirm Mail';
                        $to = $data->email;
                        self::email($subject, $body_updated, $to, NULL, NULL, 'returns@trax.pk');
                    }
                } else if ($id == 144) {
                    $rider = $reference_1_id;
                    $otp = $reference_2_id;
                    $name = '';
                    if (strpos($body, '[rider_name]') !== FALSE) {
                        $body = str_replace('[rider_name]', $rider->name, $body);
                        $name = $rider->name;
                    }
                    if (strpos($body, '[otp]') !== FALSE) {
                        $body = str_replace('[otp]', $otp, $body);
                    }
                    $to = $rider->phone;
                    // self::delivery_note_otp_sms($body, $to);

                    self::sms_otp($body, $to, $name, $otp, 2);
                } else if ($id == 145) {
                    $shipment_id = $reference_1_id;
                    $delivery_note_id = $reference_2_id;

                    $shipment = Shipment::find($shipment_id);
                    $delivery_city_id = $shipment->consignee_city->id;
                    $setting_city_id = GlobalSettings::where('type', 'undeliverd_sms_hubwise');
                    if ($setting_city_id->exists()) {
                        $setting_city_id = $setting_city_id->first();
                        $city_ids = explode(',', $setting_city_id->text);
                        if (in_array($delivery_city_id, $city_ids)) {

                            $tracking_number = $shipment->tracking_number;

                            $link = route('shipment.status.verify', ['tracking_number' => $tracking_number, 'delivery_note_id' => $delivery_note_id]);
                            $reason = '';
                            $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->latest()->first();

                            if ($shipment_journey) {
                                $current_status = $shipment_journey->shipment_status_consignee->name;
                                if ($shipment_journey->status_reason_id) {
                                    $reason = ' ' . $shipment_journey->shipment_status_reason->name;
                                }
                            } else {
                                $current_status = $shipment->status_consignee->name;
                            }


                            if (strpos($body, '[tracking_number]') !== FALSE) {
                                $body = str_replace('[tracking_number]', $tracking_number, $body);
                            }
                            if (strpos($body, '[status]') !== FALSE) {
                                $body = str_replace('[status]', $current_status, $body);
                            }
                            if (strpos($body, '[reason]') !== FALSE) {
                                $body = str_replace('[reason]', $reason, $body);
                            }

                            if (strpos($body, '[link]') !== FALSE) {
                                $body = str_replace('[link]', $link, $body);
                            }
                            $to = $shipment->consignee_phone_number_1;
                            self::bot_sms($body, $to);
                        }
                    } else {
                        $tracking_number = $shipment->tracking_number;

                        $link = route('shipment.status.verify', ['tracking_number' => $tracking_number, 'delivery_note_id' => $delivery_note_id]);
                        $reason = '';
                        $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->latest()->first();

                        if ($shipment_journey) {
                            $current_status = $shipment_journey->shipment_status_consignee->name;
                            if ($shipment_journey->status_reason_id) {
                                $reason = ' ' . $shipment_journey->shipment_status_reason->name;
                            }
                        } else {
                            $current_status = $shipment->status_consignee->name;
                        }


                        if (strpos($body, '[tracking_number]') !== FALSE) {
                            $body = str_replace('[tracking_number]', $tracking_number, $body);
                        }
                        if (strpos($body, '[status]') !== FALSE) {
                            $body = str_replace('[status]', $current_status, $body);
                        }
                        if (strpos($body, '[reason]') !== FALSE) {
                            $body = str_replace('[reason]', $reason, $body);
                        }

                        if (strpos($body, '[link]') !== FALSE) {
                            $body = str_replace('[link]', $link, $body);
                        }
                        $to = $shipment->consignee_phone_number_1;
                        self::bot_sms($body, $to);
                    }
                } else if ($id == 146) {

                    $fnf_id = $reference_1_id;
                    $admin_trax_ids = $reference_2_id;

                    $fnf = FnfSectionEmployee::find($fnf_id);

                    foreach ($admin_trax_ids as $trax_id) {
                        $var_link = $body;
                        $admin = Admin::where('trax_id', $trax_id);
                        $route = '';
                        if ($admin->exists()) {
                            $admin = $admin->first();
                            if ($admin->trax_id == 'Trax01099') {
                                $route = url('admin/human_resource/fnf/' . $fnf_id . '/it_support');
                            } else if ($admin->trax_id == 'Trax04484') {
                                $route = url('admin/human_resource/fnf/' . $fnf_id . '/finance');
                            } else if ($admin->trax_id == 'Trax00043') {
                                $route = url('admin/human_resource/fnf/' . $fnf_id . '/cs');
                            } else if ($admin->trax_id === 'Trax02533') {
                                $route = url('admin/human_resource/fnf/' . $fnf_id . '/hr');
                            } else if ($admin->trax_id === 'Trax03840') {
                                $route = url('admin/human_resource/fnf/' . $fnf_id . '/administration');
                            } else if ($admin->trax_id === $fnf->reporting_manager->trax_id) {
                                $route = url('admin/human_resource/fnf/' . $fnf_id . '/rm');
                            } else if ($admin->trax_id === $fnf->department_head->trax_id) {
                                $route = url('admin/human_resource/fnf/' . $fnf_id . '/hod_approval');
                            }

                            $link = '<a href=' . $route . '>' . $route . '</a>';

                            if (strpos($var_link, '[link]') !== FALSE) {
                                $var_link = str_replace('[link]', $link, $var_link);
                            }

                            if (strpos($subject, '[emp_id]') !== FALSE) {
                                $subject = str_replace('[emp_id]', $fnf->employee->trax_id, $subject);
                            }

                            if (strpos($var_link, '[emp_id]') !== FALSE) {
                                $var_link = str_replace('[emp_id]', $fnf->employee->trax_id, $var_link);
                            }
                            if (strpos($var_link, '[name]') !== FALSE) {
                                $var_link = str_replace('[name]', $fnf->employee->name, $var_link);
                            }
                            if (strpos($var_link, '[designation]') !== FALSE) {
                                $var_link = str_replace('[designation]', $fnf->employee->designation->name, $var_link);
                            }
                            $to = $admin->email;
                            self::email($subject, $var_link, $to);
                        }
                    }
                } else if ($id == 147) {
                    $data = $reference_1_id;

                    if (strpos($subject, '[time]') !== FALSE) {
                        $subject = str_replace('[time]', $data['time'], $subject);
                    }

                    if (strpos($body, '[time]') !== FALSE) {
                        $body = str_replace('[time]', $data['time'], $body);
                    }

                    $html = '<table><thead><tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Time Slot</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Total Status Updated</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Status Updated From Bolt</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Percentage</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Status Updated From Sonic</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Percentage</strong></th></tr></thead><tbody>';

                    $html .= '<tr><td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data['time'] . '</td><td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data['total_status_updated'] . '</td><td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data['bolt_status_updated'] . '</td><td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data['bolt_status_percentage'] . '</td><td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data['sonic_status_updated'] . '</td><td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data['sonic_status_percentage'] . '</td></tr>';

                    $html .= '</tbody></table>';

                    if (strpos($body, '[preview]') !== FALSE) {
                        $body = str_replace('[preview]', $html, $body);
                    }

                    $to = array();

                    $admins = Admin::whereIn('role_id', [3, 19])->where('status', 1);

                    if ($admins->exists()) {
                        $to = array_merge($to, $admins->pluck('email')->toArray());
                    }
                    if (!empty($to)) {
                        self::email($subject, $body, $to);
                    }
                } else if ($id == 148) {
                    $hub = City::find($reference_1_id);
                    $date = Carbon::now()->toDateString();
                    $subject = $notification->subject;
                    $body = $notification->body;

                    $hub_id = $hub->id;

                    if (strpos($subject, '[hub]') !== FALSE) {
                        $subject = str_replace('[hub]', $hub->name, $subject);
                    }

                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $date, $subject);
                    }

                    if (strpos($body, '[date]') !== FALSE) {
                        $body = str_replace('[date]', $date, $body);
                    }

                    $link = '<a href="' . $reference_2_id . '" target="_blank">Report</a>';

                    if (strpos($body, '[link]') !== FALSE) {
                        $body = str_replace('[link]', $link, $body);
                    }

                    $to = array();

                    $to_admins = Admin::whereIn('role_id', [23, 30, 10, 9, 25])->where('status', 1)->whereHas('hubs', function ($query) use ($hub_id) {
                        $query->where('hub_id', $hub_id);
                    });
                    if ($to_admins->exists()) {
                        $to = array_merge($to, $to_admins->pluck('email')->toArray());
                    }

                    self::email($subject, $body, $to);
                } else if ($id == 149) {
                    $user_id = $reference_1_id;

                    $user = User::find($user_id);

                    if (strpos($subject, '[Shipper]') !== FALSE) {
                        $subject = str_replace('[Shipper]', $user->name, $subject);
                    }

                    if (strpos($body, '[Shipper name]') !== FALSE) {
                        $body = str_replace('[Shipper name]', $user->name, $body);
                    }

                    $shipper_body = $body;
                    $sale_person_body = $body;
                    $finance_body = $body;
                    if (strpos($shipper_body, '[person_of_contact]') !== FALSE) {
                        $shipper_body = str_replace('[person_of_contact]', $user->name, $shipper_body);
                    }
                    $to = $user->email;
                    self::email($subject, $shipper_body, $to);

                    $sale_person = SalePersonTag::join('admins as sale_person', 'sale_person.id', '=', 'sale_person_tags.admin_id')
                        ->where('sale_person_tags.user_id', $user_id)
                        ->where('sale_person_tags.status', 0)
                        ->select(['sale_person.email as email', 'sale_person.name as name'])
                        ->latest('sale_person_tags.created_at')
                        ->first();

                    if (strpos($sale_person_body, '[person_of_contact]') !== FALSE) {
                        $sale_person_body = str_replace('[person_of_contact]', $sale_person->name, $sale_person_body);
                    }

                    $to = $sale_person->email;
                    self::email($subject, $sale_person_body, $to);

                    $finance_admin = AdminDepartment::where('id', 4)->first();
                    $finance_admin = $finance_admin->department_head;

                    if (strpos($finance_body, '[person_of_contact]') !== FALSE) {
                        $finance_body = str_replace('[person_of_contact]', $finance_admin['name'], $finance_body);
                    }

                    $to = $finance_admin['email'];
                    self::email($subject, $finance_body, $to);
                } else if ($id == 150) {
                    $shipment_ids = $reference_1_id;
                    $table = '<div><table><thead><tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Customer Name</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Tracking Number</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Origin</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Destination</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Remarks</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Collection Amount</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Updated By</strong></th></tr></thead><tbody>';

                    foreach ($shipment_ids as $shipment_id) {
                        $shipment = Shipment::find($shipment_id);
                        if ($shipment) {
                            $remarks = '';
                            $shipper_id = $shipment->user_id;
                            $journey = ShipmentsJourney::where('shipment_id', $shipment_id)->select('remarks', 'rider_id', 'admin_id')->latest()->first();
                            $updated_by = '-';
                            if ($journey) {
                                $remarks = $journey->remarks;
                                if ($journey->rider_id != null) {
                                    $rider = Rider::find($journey->rider_id);
                                    if ($rider) {
                                        $updated_by = $rider->name . '(Rider)';
                                    }
                                }
                                if ($journey->admin_id != null) {
                                    $admin = Admin::find($journey->admin_id);
                                    if ($admin) {
                                        $updated_by = $admin->name . '(Admin)';
                                    }
                                }
                            }
                            $table .= '<tr><td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment->user->name . '</td><td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment->tracking_number . '</td><td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment->pickup_address->city->name . '</td><td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment->consignee_city->name . '</td><td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $remarks . '</td><td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment->amount . '</td><td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $updated_by . '</td></tr>';
                        }
                    }

                    $table .= '</tbody></table></div>';

                    if (strpos($body, '[preview]') !== FALSE) {
                        $body = str_replace('[preview]', $table, $body);
                    }

                    $to = array();
                    $other = array();

                    $sales_person = SalePersonTag::where('user_id', $shipper_id)->where('status', 0)->first();
                    if ($sales_person) {
                        $to[] = Admin::find($sales_person->admin_id)->email;
                    }
                    $kam = SaleTierTag::where('user_id', $shipper_id);
                    if ($kam->exists()) {
                        $kam = $kam->first();
                        if ($kam) {
                            $admin = Admin::where('id', $kam->kam)->first();
                            if ($admin) {
                                $to[] = $admin->email;
                            }
                        }
                    }

                    $other = ['hassan@trax.pk', 'waqas@trax.pk', 'mohsin.ali@trax.pk', 'ali.qureshi@trax.pk', 'nadir.qureshi@trax.pk', 'hammad.saleem@trax.pk', 'shahzad.farooq@trax.pk'];
                    $to = array_merge($to, $other);
                    if (count($to) > 0) {
                        self::email($subject, $body, $to);
                    }
                } else if ($id == 152) {
                    $shipment = Shipment::find($reference_1_id);

                    $shipper = $shipment->user;

                    $to = $shipper->phone;

                    if (strpos($body, '[shipper_name]') !== FALSE) {
                        $body = str_replace('[shipper_name]', $shipper->name, $body);
                    }

                    self::sms($body, $to);
                } else if ($id == 153) {
                    $shipment = Shipment::find($reference_1_id);

                    $shipper = $shipment->user;

                    $to = $shipper->email;

                    if (strpos($body, '[shipper_name]') !== FALSE) {
                        $body = str_replace('[shipper_name]', $shipper->name, $body);
                    }

                    self::email($subject, $body, $to);
                } else if ($id == 154) {
                    $shipment = Shipment::find($reference_1_id);

                    $shipper = $shipment->user;

                    $sales_person_data = array();
                    $sales_person_tag = SalePersonTag::where('user_id', $shipper->id)->where('status', 0)->first();
                    if ($sales_person_tag) {
                        $sales_person_tag = Admin::find($sales_person_tag->admin_id);
                        $sales_person_data['name'] = $sales_person_tag->name;
                        $to = $sales_person_tag->email;

                        if (strpos($body, '[Sales_Person]') !== FALSE) {
                            $body = str_replace('[Sales_Person]', $sales_person_data['name'], $body);
                        }

                        if (strpos($body, '[shipment_no]') !== FALSE) {
                            $body = str_replace('[shipment_no]', $shipment->tracking_number, $body);
                        }

                        if (strpos($body, '[shipper_name]') !== FALSE) {
                            $body = str_replace('[shipper_name]', $shipment->user->name, $body);
                        }

                        self::email($subject, $body, $to);
                    }
                } else if ($id == 155) {
                    $getdata = $reference_1_id;

                    $datas = Rider::wherein("id", $getdata)->get();

                    $html = '<p>Dear (HR / It support),
                    The Rider(s)  have been deactivated from system,
                    please deactivate their issued  Official Sim.<p>';

                    $html .= '<table style="width:100%;">';
                    $html .= '<thead><tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Rider ID</th>';
                    $html .= '<th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Trax ID</th>';
                    $html .= '<th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Rider Name</th>';
                    $html .= '<th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Phone Number</th>';
                    $html .= '<th style="padding:5px; border: 1px solid black; border-collapse: collapse;">CNIC Number</th>';
                    $html .= '</tr></thead><tbody>';

                    foreach ($datas as $data) {

                        $html .= '<tr>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data->id . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data->trax_id . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data->name . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data->phone . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $data->cnic . '</td>';
                    }
                    $html .= '</tr></tbody></table>';
                    $body_updated = $body;
                    $body_updated = str_replace('[preview]', $html, $body_updated);
                    $subject = ' Rider Deactivation';
                    $to = ['hasnain.saleem@trax.pk', 'abdul.ahad@trax.pk', 'saleem.abbas@trax.pk', 'nadeem.sarwar@trax.pk', 'hr.dept@trax.pk', 'danish.zahid@trax.pk', 'ali.raza@trax.pk'];

                    self::email($subject, $body_updated, $to);
                } else if ($id == 156) {
                    $file_path = $reference_1_id['file_path'];
                    $from = $reference_1_id['from'];
                    $to = $reference_1_id['to'];
                    $subject = 'Revenue Monthly Report By Delivery Date  | ';

                    $subject .= 'From ( ' . $from . ' - ' . $to . ' )';

                    $file = Storage::disk('public')->url($file_path);
                    $link = '<a href="' . $file . '" target="_blank"><u>Download</u></a>';

                    if (strpos($body, '[link]') !== FALSE) {
                        $body = str_replace('[link]', $link, $body);
                    }

                    $to = ['shafay.tariq@trax.pk', 'adnan.ahsan@trax.pk', 'fawad.ahmed@trax.pk', 'hammad.majid@trax.pk'];

                    $cc = ["muhammad.waqas@trax.pk", "danish.zahid@trax.pk"];

                    self::email($subject, $body, $to, $cc);
                } else if ($id == 157) {
                    $date = Carbon::yesterday()->format('Y-m-d');

                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $date, $subject);
                    }

                    if (strpos($body, '[date]') !== FALSE) {
                        $body = str_replace('[date]', $date, $body);
                    }

                    $link = '<a href="' . $reference_2_id . '" target="_blank">Report</a>';

                    if (strpos($subject, '[link]') !== FALSE) {
                        $subject = str_replace('[link]', $link, $subject);
                    }

                    if (strpos($body, '[link]') !== FALSE) {
                        $body = str_replace('[link]', $link, $body);
                    }

                    $to = array();


                    $sale_person_email = Admin::find($reference_1_id)->email;

                    if ($sale_person_email) {
                        self::email($subject, $body, $sale_person_email);
                    }
                } else if ($id == 158) {
                    $rider = Rider::find($reference_1_id);
                    if ($rider) {
                        if (strpos($body, '[rider_name]') !== FALSE) {
                            $body = str_replace('[rider_name]', $rider->name, $body);
                        }
                        if (strpos($body, '[otp]') !== FALSE) {
                            $body = str_replace('[otp]', $rider->reset_pin_otp, $body);
                        }
                        $to = $rider->phone;
                        self::sms($body, $to, 1);
                    }
                } else if ($id == 159) {
                    $admin = Admin::find($reference_1_id);
                    if ($admin) {
                        $to = $admin->email;
                        self::email($subject, $body, $to);
                    }
                } else if ($id == 160) {
                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $reference_1_id, $subject);
                    }

                    $link = '<a href="' . $reference_2_id . '" target="_blank">Report</a>';

                    if (strpos($body, '[link]') !== FALSE) {
                        $body = str_replace('[link]', $link, $body);
                    }
                    $date = Carbon::today()->startOfDay()->toDateTimeString();
                    $date_end = Carbon::today()->endOfDay()->toDateTimeString();
                    $sale_person_number_data = KaeNumber::whereBetween('created_at', [$date, $date_end])->orderBy('shipments', 'desc')->get();
                    $html = '<table><thead><tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>S No.</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Admin</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Achieved Shipments</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Target Shipments</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Target Achieved %</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Achieved Revenue</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Target Revenue</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Target Achieved %</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Avg Revenue/Parcel</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Contribution</strong></th></tr></thead><tbody>';
                    $serial = 1;
                    $shipments_count = 0;
                    $revenue_count = 0;
                    $avg_revenue_count = 0;
                    $contribution_count = 0;
                    $total_target_shipments = 0;
                    $total_target_shipments_achieved = 0;
                    $total_target_revenue = 0;
                    $total_target_revenue_achieved = 0;
                    $sum_total_target_revenue_achieved = 0;
                    $total_target_revenue_avg = 0;
                    foreach ($sale_person_number_data as $sale_person_number) {
                        $all_shipments_target_revenue = 0;
                        $target_shipments_achieved = 0;
                        $target_revenue_achieved = 0;
                        $target_shipments = $sale_person_number->target_shipments;
                        if ($target_shipments > 0) {
                            $target_shipments_achieved = ($sale_person_number->shipments / $target_shipments) * 100;
                        }
                        $target_revenue = $sale_person_number->target_revenue;
                        if ($target_revenue > 0) {
                            $all_shipments_target_revenue = $target_revenue * $target_shipments;
                            $total_target_revenue_avg += $all_shipments_target_revenue;
                            if ($all_shipments_target_revenue > 0) {
                                $target_revenue_achieved = ($sale_person_number->revenue / $all_shipments_target_revenue) * 100;
                            } else {
                                $target_revenue_achieved = 0;
                            }
                        }
                        $html .= '<tr>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial . '</td>';
                        if ($sale_person_number->admin_id == 0) {
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">Walk-In</td>';
                        } else {
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $sale_person_number->sales_person->name . '</td>';
                        }
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format($sale_person_number->shipments) . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; background-color:#FFE699;">' . number_format($sale_person_number->target_shipments) . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; background-color:#C7E0B4;">' . number_format($target_shipments_achieved) . '%</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format(round($sale_person_number->revenue)) . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; background-color:#FFE699;">' . number_format($all_shipments_target_revenue) . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; background-color:#C7E0B4;">' . number_format($target_revenue_achieved) . '%</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format(round($sale_person_number->avg_revenue)) . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format($sale_person_number->contribution, 2, '.', '') . '%</td>';
                        $html .= '</tr>';
                        $shipments_count = $shipments_count + $sale_person_number->shipments;
                        $revenue_count = $revenue_count + $sale_person_number->revenue;
                        $contribution_count = $contribution_count + $sale_person_number->contribution;
                        $total_target_shipments += $sale_person_number->target_shipments;
                        $total_target_revenue += $sale_person_number->target_revenue;
                        $sum_total_target_revenue_achieved += $target_revenue_achieved;
                        $serial++;
                    }
                    if ($total_target_shipments > 0) {
                        $total_target_shipments_achieved = ($shipments_count / $total_target_shipments) * 100;
                    }
                    $total_all_shipments_target_revenue = 0;
                    if ($total_target_revenue_avg > 0) {
                        $total_target_revenue_achieved = ($revenue_count / $total_target_revenue_avg) * 100;
                    }
                    if ($shipments_count != 0) {
                        $avg_revenue_count = $revenue_count / $shipments_count;
                    } else {
                        $avg_revenue_count = 0;
                    }
                    $html .= '<tr>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">Total</td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"></td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format($shipments_count) . '</td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; background-color:#FFE699;">' . number_format($total_target_shipments) . '</td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; background-color:#C7E0B4;">' . number_format($total_target_shipments_achieved) . '%</td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format(round($revenue_count)) . '</td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; background-color:#FFE699;">' . number_format(round($total_target_revenue_avg)) . '</td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; background-color:#C7E0B4;">' . number_format($total_target_revenue_achieved) . '%</td>';

                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format(round($avg_revenue_count)) . '</td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . ceil($contribution_count) . '%</td>';
                    $html .= '</tr>';

                    $html .= '</tr>';
                    $html .= '</tbody></table>';

                    if (strpos($body, '[preview]') !== FALSE) {
                        $body = str_replace('[preview]', $html, $body);
                    }

                    $to = array();

                    $to = ['mohsin.ali@trax.pk', 'waqas@trax.pk', 'hassan@trax.pk', 'noman.aziz@trax.pk', 'fawad.ahmed@trax.pk', 'nadir.qureshi@trax.pk'];

                    $cc = array();
                    $bcc = array();
                    $bcc = ['muhammad.waqas@trax.pk'];
                    self::email($subject, $body, $to, $cc, $bcc);
                } else if ($id == 162) {
                    $admin = Admin::find($reference_1_id);
                    if ($admin) {
                        if (strpos($body, '[user_name]') !== FALSE) {
                            $body = str_replace('[user_name]', $admin->name, $body);
                        }
                        if (strpos($body, '[otp]') !== FALSE) {
                            $body = str_replace('[otp]', $admin->reset_pin_otp, $body);
                        }
                        $to = $reference_2_id;
                        self::sms_otp($body, $to, $admin->name, $admin->reset_pin_otp, 1);
                    }
                } else if ($id == 163) {
                    $shipment = Shipment::find($reference_1_id);
                    $shipment_journey = ShipmentsJourney::where('shipment_id', $reference_1_id)
                        ->where('reference_1_id', $reference_2_id)
                        ->orderBy('id', 'DESC');
                    if ($shipment && $shipment_journey->exists()) {
                        $shipment_journey = $shipment_journey->first();
                        if (strpos($body, '[tracking_no]') !== FALSE) {
                            $body = str_replace('[tracking_no]', $shipment->tracking_number, $body);
                        }
                        if (strpos($body, '[status]') !== FALSE) {
                            $body = str_replace('[status]', $shipment_journey->shipment_status_shipper->name, $body);
                        }
                        if (strpos($body, '[reason]') !== FALSE) {
                            $body = str_replace('[reason]', $shipment_journey->shipment_status_reason->name, $body);
                        }
                        $to = $shipment->consignee_phone_number_1;
                        self::sms($body, $to);
                    }
                } else if ($id == 164) {
                    $getdata = $reference_1_id;

                    $data = Employee::leftjoin('employee_designations as d', 'd.id', '=', 'employees.designation_id')
                        ->leftjoin('admin_departments as ad', 'd.department_id', '=', 'ad.id')
                        ->select('employees.id as id', 'employees.trax_id as trax_id', 'employees.name as name', 'employees.cnic as cnic', 'employees.phone_number as phone_number', 'employees.employee_type_id as employee_type_id', 'd.name as designation', 'ad.name as department_name')
                        ->wherein('employees.id', $getdata)->get();

                    $is_sent = false;

                    $html = '<p>Dear HR,

                    Following Employee(s) has updated their documents through Bolt App,
                    please check and verify his/her documents.<p>';

                    $html .= '<table style="width:100%;">';
                    $html .= '<thead><tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Employee ID</th>';
                    $html .= '<th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Name</th>';
                    $html .= '<th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Phone Number</th>';
                    $html .= '<th style="padding:5px; border: 1px solid black; border-collapse: collapse;">CNIC Number</th>';
                    $html .= '<th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Category</th>';
                    $html .= '<th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Department</th>';
                    $html .= '<th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Designation</th>';
                    $html .= '</tr></thead><tbody>';

                    $admins = Admin::whereIn('role_id', [70, 69, 63])->pluck('id')->toArray();
                    foreach ($admins as $admin) {
                        $admin_hubs = AdminHub::where('admin_id', $admin)->pluck('hub_id')->toArray();
                        foreach ($data as $datum) {

                            $data_set = Employee::find($datum->id);
                            $employee_hub = City::find($data_set->city_id);
                            $hub_id = $employee_hub->hub_id;

                            if (in_array($hub_id, $admin_hubs)) {
                                $category = ($datum->employee_type_id == 1) ? "Staff" : "Rider";
                                $designation = ($datum->employee_type_id == 1) ? $datum->designation : "-";

                                $html .= '<tr>';
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $datum->trax_id . '</td>';
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $datum->name . '</td>';
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $datum->phone_number . '</td>';
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $datum->cnic . '</td>';
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $category . '</td>';
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $datum->department_name . '</td>';
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $designation . '</td>';

                                $data_set->attachment_update = 0;
                                $data_set->save();

                                $is_sent = true;
                            }
                        }
                        $html .= '</tr></tbody></table>';
                        $body_updated = $body;
                        $body_updated = str_replace('[preview]', $html, $body_updated);
                        $subject = ' Employee Documents Update';
                        if ($is_sent) {
                            $admin_email = Admin::find($admin);
                            if ($admin_email->email) {
                                $to = $admin_email->email;
                                self::email($subject, $body_updated, $to);
                            }
                        }
                    }
                } else if ($id == 165) {
                    $shipment = Shipment::find($reference_1_id);
                    $shipment_otp = ShipmentOtp::find($reference_2_id);
                    if ($shipment && $shipment_otp) {
                        if (strpos($body, '[consignee]') !== FALSE) {
                            $body = str_replace('[consignee]', $shipment->consignee_name, $body);
                        }
                        if (strpos($body, '[tracking_no]') !== FALSE) {
                            $body = str_replace('[tracking_no]', $shipment->tracking_number, $body);
                        }
                        if (strpos($body, '[otp]') !== FALSE) {
                            $body = str_replace('[otp]', $shipment_otp->otp, $body);
                        }
                        $to = $shipment->consignee_phone_number_1;
                        self::sms($body, $to);
                        if ($shipment->consignee_phone_number_2 != NULL) {
                            $to = $shipment->consignee_phone_number_2;
                            self::sms($body, $to);
                        }
                    }
                } else if ($id == 166) {
                    $possible_fields = ['pickup_city', 'consignee_name', 'consignee_city', 'order_id', 'weight', 'tracking_number', 'item_product_type', 'item_description', 'item_quantity', 'amount'];

                    $field_names = ['pickup_city' => 'Pickup City', 'consignee_name' => 'Consignee Name', 'consignee_city' => 'Consignee City', 'order_id' => 'Order ID', 'weight' => 'Weight', 'tracking_number' => 'Tracking Number', 'item_product_type' => 'Item Product Type', 'item_description' => 'Item Description', 'item_quantity' => 'Item Quantity', 'amount' => 'Amount'];

                    $present_fields = array();

                    $first_field = NULL;

                    $position = NULL;

                    foreach ($possible_fields as $field) {
                        $new_position = strpos($body, '[' . $field . ']');

                        if ($new_position !== FALSE) {
                            if ($position == NULL) {
                                $present_fields[] = $field;

                                $first_field = $field;
                            } else if ($new_position > $position) {
                                $present_fields[] = $field;
                            } else {
                                array_unshift($present_fields, $field);
                            }

                            $position = $new_position;
                        }
                    }

                    $today = Carbon::now()->toDateTimeString();

                    $user_wise_shipments = array();

                    $origin_hub_ids = array();

                    foreach ($reference_1_id as $shipment_id) {
                        $shipment = Shipment::find($shipment_id);

                        $origin_hub_id = $shipment->pickup_address->city->hub_id;

                        if (!in_array($origin_hub_id, $origin_hub_ids)) {
                            $origin_hub_ids[] = $origin_hub_id;
                        }

                        $details = array();

                        $details['pickup_city'] = $shipment->pickup_address->city->name;
                        $details['consignee_name'] = $shipment->consignee_name;
                        $details['consignee_city'] = $shipment->consignee_city->name;
                        $details['order_id'] = $shipment->order_id;
                        $details['weight'] = $shipment->actual_weight;
                        $details['tracking_number'] = $shipment->tracking_number;
                        $details['amount'] = $shipment->amount;

                        if ($shipment->booking_type_id == 1 || $shipment->booking_type_id == 2) {
                            foreach ($shipment->items as $item) {
                                if ($item->type == 0) {
                                    $details['item_product_type'] = $item->product->product_name;
                                    $details['item_description'] = $item->description;
                                    $details['item_quantity'] = $item->quantity;
                                }
                            }
                        } else {
                            $details['item_product_type'] = '';
                            $details['item_description'] = '';
                            $details['item_quantity'] = '';
                        }

                        $user_wise_shipments[$shipment->user_id][] = $details;
                    }

                    $original_subject = $subject;
                    $original_body = $body;

                    foreach ($user_wise_shipments as $user_id => $shipments) {
                        $shipper = User::find($user_id);

                        if (strpos($subject, '[company_name]') !== FALSE) {
                            $subject = str_replace('[company_name]', $shipper->name, $subject);
                        }

                        if (strpos($body, '[company_name]') !== FALSE) {
                            $body = str_replace('[company_name]', $shipper->name, $body);
                        }

                        if (strpos($subject, '[arrival_at]') !== FALSE) {
                            $subject = str_replace('[arrival_at]', $today, $subject);
                        }

                        if (strpos($body, '[arrival_at]') !== FALSE) {
                            $body = str_replace('[arrival_at]', $today, $body);
                        }

                        //              $to = $shipper->email;
                        if (ShipperNotificationEmail::where('user_id', $shipper->id)->exists()) {
                            $to = ShipperNotificationEmail::where('user_id', $shipper->id)->pluck('email')->toArray();
                        } else {
                            $to = $shipper->email;
                        }
                        $shipment_details = '<table style="padding:5px; border: 1px solid black; border-collapse: collapse;"><tbody><tr>';

                        $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">S. No.</td>';

                        foreach ($present_fields as $field) {
                            $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">' . $field_names[$field] . '</td>';
                        }

                        $shipment_details .= '</tr>';

                        $serial_number = 1;

                        foreach ($shipments as $shipment) {
                            $shipment_details .= '<tr>';

                            $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial_number . '</td>';

                            foreach ($present_fields as $field) {
                                if (!empty($shipment[$field])) {
                                    $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment[$field] . '</td>';
                                } else {
                                    $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"></td>';
                                }
                            }

                            $shipment_details .= '</tr>';

                            $serial_number++;
                        }

                        $shipment_details .= '</tbody></table>';

                        foreach ($present_fields as $field) {
                            if ($field != $first_field) {
                                $body = str_replace('[' . $field . ']', '', $body);
                            }
                        }

                        $body = str_replace('[' . $first_field . ']', $shipment_details, $body);

                        $bcc = array();

                        //              $general_admins = Admin::whereIn('role_id', [6])->where('status', 1);
                        //
                        //              if ($general_admins->exists()) {
                        //                $bcc = array_merge($bcc, $general_admins->pluck('email')->toArray());
                        //              }

                        // $related_admins = Admin::whereIn('role_id', [10])->where('status', 1)->where('id', '!=', 276)->whereHas('hubs', function ($query) use ($origin_hub_ids) {
                        //     $query->whereIn('hub_id', $origin_hub_ids);
                        // });

                        // if ($related_admins->exists()) {
                        //     $bcc = array_merge($bcc, $related_admins->pluck('email')->toArray());
                        // }

                        // if (empty($bcc)) {
                        //     $bcc = NULL;
                        // }

                        // self::email($subject, $body, $to, NULL, $bcc);
                        self::email($subject, $body, $to, NULL, NULL);

                        $subject = $original_subject;
                        $body = $original_body;
                    }
                } else if ($id == 167) {
                    $user_id = $reference_1_id['user_id'];
                    $status_code = $reference_1_id['status_code'];
                    $link = $reference_1_id['url'];
                    $message = $reference_1_id['message'];

                    if (strpos($body, '[status_code]') !== FALSE) {
                        $body = str_replace('[status_code]', $status_code, $body);
                    }
                    if (strpos($body, '[link]') !== FALSE) {
                        $body = str_replace('[link]', $link, $body);
                    }
                    if (strpos($body, '[message]') !== FALSE) {
                        $body = str_replace('[message]', $message, $body);
                    }
                    $to = array();
                    $cc = array();
                    $shipper = User::find($user_id);
                    $sales_person = SalePersonTag::where('user_id', $user_id)->where('status', 0)->first();

                    $cc[] = Admin::find($sales_person->admin_id)->email;

                    if (ShipperNotificationEmail::where('user_id', $shipper->id)->exists()) {
                        $to = ShipperNotificationEmail::where('user_id', $shipper->id)->pluck('email')->toArray();
                    } else {
                        $to = $shipper->email;
                    }
                    self::email($subject, $body, $to, $cc);
                } else if ($id == 168) {
                    $from = $reference_1_id;
                    $to = $reference_2_id;
                    $statuses = ShipmentStatus::whereIn('id', [1, 2, 3, 4, 5, 8, 12, 13, 14, 20, 21, 22, 23, 24, 25, 55])->orderBy('id', 'asc')->orderBy('name', 'desc')->get();
                    $shipments_counts = array();
                    $nsa_accounts = [3324];
                    if (count($nsa_accounts) > 0) {
                        foreach ($statuses as $status) {
                            $shipments_counts[$status->name] = Shipment::whereIn('user_id', $nsa_accounts)->where('shipper_status_id', $status->id)->whereBetween('created_at', [$from, $to])->count();
                        }

                        $preview = "<div style='display: flex;flex-wrap: wrap;margin-right: -15px;margin-left: -15px'>";
                        foreach ($shipments_counts as $status => $count) {
                            $preview .= "
                                <div style='background-color: #11f118;flex: 0 0 16.666667%;max-width: 25%;min-width: 25%;width: 100%;min-height: 150px;max-height: 150px;margin-right: 20px;position: relative;text-align:center;height:fit-content;padding:0px 20px;margin-bottom:20px;'>
                                
                                    <h2 style='color:#fff;padding-bottom:0px;margin-bottom:0px;'>" . $count . "</h2>
                                    <h4 style='color:#fff;padding-top:0px;margin-top:0px;'>" . $status . "</h4>    
                                </div>
                            ";
                        }

                        $preview .= "</div>";

                        $date = $from->toDateString();

                        if (strpos($subject, '[date]') !== FALSE) {
                            $subject = str_replace('[date]', $date, $subject);
                        }

                        if (strpos($body, '[date]') !== FALSE) {
                            $body = str_replace('[date]', $date, $body);
                        }

                        if (strpos($body, '[preview]') !== FALSE) {
                            $body = str_replace('[preview]', $preview, $body);
                        }

                        $to = ["talha.hussain@trax.pk", 'syed.anam@trax.pk', 'waqas@trax.pk', 'ops.telenor@trax.pk'];
                        self::email($subject, $body, $to);
                    }
                } else if ($id == 169) {

                    $shipment_id = $reference_1_id;
                    $shipment = Shipment::find($shipment_id);
                    //yep sms
                    if ($shipment->user_id == 12613) {
                        $body = 'Your YAP Debit Card is marked for return. Reply with TRAX YES ' . $shipment->tracking_number . ' to receive it or TRAX NO ' . $shipment->tracking_number . ' to return';
                        $to = $shipment->consignee_phone_number_1;
                        $data = array($body, $to);
                        return $data;
                    }
                    //yep sms start
                    $shipper_name = NULL;
                    if ($shipment->user->brand_name != null) {
                        $shipper_name = $shipment->user->brand_name;
                    } else {
                        $shipper_name = $shipment->user->name;
                    }

                    if (strpos($body, '[consignee]') !== FALSE) {
                        $body = str_replace('[consignee]', $shipment->consignee_name, $body);
                    }


                    if (strpos($body, '[tracking_number]') !== FALSE) {
                        $body = str_replace('[tracking_number]', $shipment->tracking_number, $body);
                    }

                    if (strpos($body, '[amount]') !== FALSE) {
                        $body = str_replace('[amount]', $shipment->amount, $body);
                    }

                    if (strpos($body, '[brand_name]') !== FALSE) {
                        $body = str_replace('[brand_name]', $shipper_name, $body);
                    }

                    $to = $shipment->consignee_phone_number_1;
                    $data = array($body, $to);
                    return $data;
                } else if ($id == 131) {
                    $request_no = $reference_1_id;
                    $admin_id = $reference_2_id;
                    if (strpos($body, '[request_no]') !== FALSE) {
                        $body = str_replace('[request_no]', $request_no, $body);
                    }
                    $admin = Admin::find($admin_id);
                    $to = $admin->email;
                    self::email($subject, $body, $to);
                } else if ($id == 170) {
                    $sale_person = Admin::find($reference_1_id);
                    $shipper_ids = $reference_2_id;
                    if ($sale_person && $sale_person->email) {
                        $shippers_data = User::leftJoin('shipments as s', function ($join) {
                            $join->on('s.user_id', '=', 'users.id')
                                ->where(
                                    's.id',
                                    '=',
                                    DB::raw('(select max(id) from shipments where shipments.user_id = users.id)')
                                );
                        })
                            ->select('users.name as shipper_name', 'users.id as account_id', 's.tracking_number as last_tracking_number')
                            ->whereIn('users.id', $reference_2_id);
                        if ($shippers_data->exists()) {
                            $shippers_data = $shippers_data->get();
                            if (strpos($body, '[sale_person]') !== FALSE) {
                                $body = str_replace('[sale_person]', $sale_person->name, $body);
                            }
                            $html = '';
                            $html .= '<table style="width:100%;">';
                            $html .= '<thead><tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Shipper Name</th>';
                            $html .= '<th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Account ID</th>';
                            $html .= '<th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Last Tracking No.</th>';
                            $html .= '</tr></thead><tbody>';
                            foreach ($shippers_data as $datum) {
                                $account_id = str_pad($datum->account_id, 6, 0, STR_PAD_LEFT);
                                $last_tracking = ($datum->last_tracking_number) ? $datum->last_tracking_number : "-";
                                $html .= '<tr>';
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $datum->shipper_name . '</td>';
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $account_id . '</td>';
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $last_tracking . '</td>';
                            }
                            $html .= '</tr></tbody></table>';
                            $body_updated = $body;
                            $body_updated = str_replace('[preview]', $html, $body_updated);
                            $to = $sale_person->email;
                            self::email($subject, $body_updated, $to);
                        }
                    }
                } else if ($id == 171) {
                    $name = $reference_1_id;
                    $phone_number = $reference_2_id;

                    if (strpos($body, '[name]') !== FALSE) {
                        $body = str_replace('[name]', $name, $body);
                    }

                    $to = $phone_number;
                    self::sms($body, $to);
                } else if ($id == 172) {
                    $detail = $reference_1_id;

                    $payment = RetailDonePayment::leftjoin('retail_done_payment_shipments as rdps', 'rdps.retail_done_payment_id', '=', 'retail_done_payments.id')
                        ->leftjoin('retail_done_payment_calculations as rdpc', 'rdpc.retail_done_payment_id', '=', 'retail_done_payments.id')
                        ->where('retail_done_payments.id', $detail['done_payment_id'])
                        ->select('rdpc.amount as total_amount', 'rdpc.payable as payable', 'retail_done_payments.ibft_charges as charges', 'rdpc.adjustment as adjustment', 'rdps.shipment_id as tracking_number', 'retail_done_payments.user_id as user_id')->first();
                    //                   dd($payment->payable);

                    $payable = number_format(ROUND($payment->payable - $payment->charges, 0, PHP_ROUND_HALF_DOWN));
                    $adjustment = $payment->adjustment;
                    $total_amaount = $payment->total_amount;
                    $tracking_no = $payment->tracking_number;
                    $user_id = $payment->user_id;

                    if (strpos($body, '[shipper_name]') !== FALSE) {
                        $body = str_replace('[shipper_name]', $detail['name'], $body);
                    }
                    if (strpos($body, '[total_amount]') !== FALSE) {
                        $body = str_replace('[total_amount]', $total_amaount, $body);
                    }
                    if (strpos($body, '[updated_at]') !== FALSE) {
                        $body = str_replace('[updated_at]', $detail['updated_at'], $body);
                    }
                    $link = url('payment_details' . '/' . base64_encode($detail['done_payment_id']) . '/' . base64_encode("$user_id"));
                    if (strpos($body, '[status_link]') !== FALSE) {
                        $body = str_replace('[status_link]', $link, $body);
                    }

                    $to = $detail['phone'];

                    self::sms($body, $to);
                } else if ($id == 173) {
                    $erf_id = $reference_1_id;
                    $erf = EmployeeRequisition::find($erf_id);
                    $hod_email = Admin::find($erf->department_head_id)->email;
                    $erf = "ERF ID #" . $erf_id;
                    if (strpos($body, '[erf_id]') !== FALSE) {
                        $body = str_replace('[erf_id]', $erf, $body);
                    }
                    $to = $hod_email;
                    self::email($subject, $body, $to);
                } else if ($id == 174) {
                    $erf_id = $reference_1_id;
                    $erf = EmployeeRequisition::find($erf_id);
                    if ($erf->status_id == 2) {
                        $admin = Admin::find($erf->department_head_id);
                        $name = $admin->name . ' ' . '(HOD)';
                        $to = 'hassan@trax.pk';
                    } else if ($erf->status_id == 3) {
                        $name = 'Muhammad Hassan Khan' . '(CEO)';
                        $to = 'hr.dept@trax.pk';
                    }

                    $erf = "ERF ID #" . $erf_id;
                    if (strpos($body, '[erf_id]') !== FALSE) {
                        $body = str_replace('[erf_id]', $erf, $body);
                    }
                    if (strpos($body, '[admin]') !== FALSE) {
                        $body = str_replace('[admin]', $name, $body);
                    }

                    self::email($subject, $body, $to);
                } else if ($id == 175) {
                    $link = '<a href="' . $reference_2_id . '" target="_blank">Report</a>';

                    if (strpos($body, '[link]') !== FALSE) {
                        $body = str_replace('[link]', $link, $body);
                    }

                    $to = array();
                    $to = [$reference_1_id];

                    self::email($subject, $body, $to);
                } else if ($id == 176) {
                    $flag = true;
                    $crm_comment_id = $reference_2_id;
                    $crm_comment = CrmComments::find($crm_comment_id);
                    if ($crm_comment) {
                        $crm_request = CrmRequest::find($crm_comment->crm_request_id);
                        $agent_id = $crm_request->agent_id;
                        if ($crm_request) {
                            $type = $reference_1_id;

                            if ($type == 1) {
                                $shipper = User::find($crm_request->shipper_id);
                                if ($shipper) {
                                    $name = $shipper->name;
                                    $phone_number = $shipper->phone;
                                } else {
                                    $flag = false;
                                }
                            } else if ($type == 2) {
                                if ($crm_request->shipment_id != null) {
                                    $shipment = Shipment::find($crm_request->shipment_id);
                                    if ($shipment) {
                                        $name = $shipment->consignee_name;
                                        $phone_number = $shipment->consignee_phone_number_1;
                                    } else {
                                        $flag = false;
                                    }
                                } else {
                                    $flag = false;
                                }
                            } else {
                                $flag = false;
                            }
                        } else {
                            $flag = false;
                        }
                    } else {
                        $flag = false;
                    }
                    if ($flag) {
                        //                        dd($name,$crm_request->id,$crm_comment->comment);
                        //todo : now yahan p log savekrne k lye code krna h

                        $agent = Admin::where('id', $agent_id)->select('name')->first();

                        $crm_log = new CrmSmsLog();
                        $crm_log->crm_request_id = $crm_request->id;
                        $crm_log->agent = isset($agent->name) ? $agent->name : '-';
                        $crm_log->massage = $crm_comment->comment;
                        $crm_log->save();

                        if (strpos($body, '[name]') !== FALSE) {
                            $body = str_replace('[name]', $name, $body);
                        }
                        if (strpos($body, '[crm_request_id]') !== FALSE) {
                            $body = str_replace('[crm_request_id]', str_pad($crm_request->id, 6, '0', STR_PAD_LEFT), $body);
                        }
                        if (strpos($body, '[comment]') !== FALSE) {
                            $body = str_replace('[comment]', preg_replace("/<br\W*?\/>/", "\n", $crm_comment->comment), $body);
                        }

                        $to = $phone_number;
                        self::sms($body, $to);
                    }
                } else if ($id == 177) {
                    $pickup_req = V2PickupRequest::find($reference_1_id);

                    $pikup_shipment_id = V2PickupRequestShipment::where('pickup_request_id', $reference_1_id)->get()->first();

                    $shipment = Shipment::find($pikup_shipment_id->shipment_id);


                    $sales_person = SalePersonTag::where('user_id', $shipment->user_id)->where('status', 0)->first();

                    $shipper = User::find($shipment->user_id);

                    $sale_person_detail = Admin::find($sales_person->admin_id);
                    if ($sale_person_detail) {


                        if (strpos($body, '[shipper_name]') !== FALSE) {
                            $body = str_replace('[shipper_name]', $shipper->name, $body);
                        }

                        $pickup_req_no = str_pad($pickup_req->id, 6, '0', STR_PAD_LEFT);

                        if (strpos($body, '[pickup_request_no]') !== FALSE) {
                            $body = str_replace('[pickup_request_no]', $pickup_req_no, $body);
                        }

                        if (strpos($body, '[remarks]') !== FALSE) {
                            $body = str_replace('[remarks]', $pickup_req->remarks, $body);
                        }

                        self::email($subject, $body, $sale_person_detail->email);
                    }

                    $sales_tier_tag = SaleTierTag::where('user_id', $shipment->user_id);


                    if ($sales_tier_tag->exists()) {
                        $sales_tier_tag = $sales_tier_tag->first()->kam;
                        if ($sales_tier_tag) {

                            $kam = Admin::find($sales_tier_tag);
                            if ($kam) {
                                if (strpos($subject, '[sales_person]') !== FALSE) {
                                    $subject = str_replace('[sales_person]', $kam->name, $subject);
                                }

                                if (strpos($body, '[shipper_name]') !== FALSE) {
                                    $body = str_replace('[shipper_name]', $shipper->name, $body);
                                }

                                $pickup_req_no = str_pad($pickup_req->id, 6, '0', STR_PAD_LEFT);

                                if (strpos($body, '[pickup_request_no]') !== FALSE) {
                                    $body = str_replace('[pickup_request_no]', $pickup_req_no, $body);
                                }

                                if (strpos($body, '[remarks]') !== FALSE) {
                                    $body = str_replace('[remarks]', $pickup_req->remarks, $body);
                                }

                                self::email($subject, $body, $kam->email);
                            }
                        }
                    }
                } else if ($id == 178) {

                    $shipment = Shipment::find($reference_1_id);
                    // dd($shipment);
                    if ($shipment) {
                        if (strpos($body, '[name]') !== FALSE) {
                            $body = str_replace('[name]', $shipment->consignee_name, $body);
                        }

                        $to = $shipment->consignee_phone_number_1;
                        self::sms($body, $to);
                        if ($shipment->consignee_phone_number_2 != NULL) {
                            $to = $shipment->consignee_phone_number_2;
                            self::sms($body, $to);
                        }
                    }
                } else if ($id == 179) {

                    if ($reference_1_id != null) {

                        foreach ($reference_1_id as $key => $val) {
                            // $body = $notification->body;
                            $random_id = date("dmy") . $val->id . date("his");
                            $send_by = Auth::id();
                            $timestamp = Carbon::now()->format('Y-m-d H:i:s');
                            $link = url("/survey_form/$random_id");

                            $survey_record = new DisableAccountIntimationSendSurvey();
                            $survey_record->shipper_id = $val->id;
                            $survey_record->random_id = $random_id;
                            $survey_record->send_by = $send_by;
                            $survey_record->send_via = "email";
                            $survey_record->url = $link;
                            $survey_record->status = 0;
                            $survey_record->created_at = $timestamp;
                            $survey_record->updated_at = $timestamp;
                            $survey_record->save();


                            if (strpos($body, '[link]') !== FALSE) {
                                $email_body = str_replace('[link]', $link, $body);
                            }

                            self::email($subject, $email_body, $val->email);
                        }
                    }
                } else if ($id == 180) {

                    if ($reference_1_id != null) {

                        foreach ($reference_1_id as $key => $val) {
                            $random_id = date("his") . $val->id . date("dmy");
                            $send_by = Auth::id();
                            $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
                            $link = url("/survey_form/$random_id");

                            $survey_record = new DisableAccountIntimationSendSurvey();
                            $survey_record->shipper_id = $val->id;
                            $survey_record->random_id = $random_id;
                            $survey_record->send_by = $send_by;
                            $survey_record->send_via = "sms";
                            $survey_record->url = $link;
                            $survey_record->status = 0;
                            $survey_record->created_at = $timestamp;
                            $survey_record->updated_at = $timestamp;
                            $survey_record->save();

                            if (strpos($body, '[link]') !== FALSE) {
                                $sms_body = str_replace('[link]', $link, $body);
                            }

                            self::sms($sms_body, $val->phone);
                        }
                    }
                } else if ($id == 181) {
                    $detail = $reference_1_id;

                    if (strpos($body, '[name]') !== FALSE) {
                        $body = str_replace('[name]', $detail['name'], $body);
                    }

                    if (strpos($body, '[reason]') !== FALSE) {
                        $body = str_replace('[reason]', $detail['reason'], $body);
                    }
                    $phone_number = $detail['contact_number'];
                    $to = $phone_number;
                    self::sms($body, $to);
                } else if ($id == 182) {

                    $employee = $reference_1_id;
                    if ($employee) {
                        $link = '<a href="' . route('admin.human_resource.employee_confirmation.index') . '" target="_blank"><u>Click To View</u></a>';

                        if (strpos($body, '[link]') !== FALSE) {
                            $body = str_replace('[link]', $link, $body);
                        }
                        if (strpos($body, '[emp_id]') !== FALSE) {
                            $body = str_replace('[emp_id]', $employee->trax_id, $body);
                        }
                        if (strpos($body, '[name]') !== FALSE) {
                            $body = str_replace('[name]', $employee->name, $body);
                        }
                        if (strpos($body, '[designation]') !== FALSE) {
                            $body = str_replace('[designation]', $employee->designation->name, $body);
                        }
                        if (strpos($body, '[joining_date]') !== FALSE) {
                            $body = str_replace('[joining_date]', $employee->joining_date, $body);
                        }

                        if (strpos($subject, '[emp_id]') !== FALSE) {
                            $subject = str_replace('[emp_id]', $employee->trax_id, $subject);
                        }

                        if (strpos($subject, '[name]') !== FALSE) {
                            $subject = str_replace('[name]', $employee->name, $subject);
                        }

                        $body .= PHP_EOL . PHP_EOL . '<img class="brand-logo trax" alt="Trax" src="' . asset('img/trax_logo_new.png') . '" width="100" height="50">
                        <p>Copyright © ' . now()->year . ' By TRAX, All Rights Reserved.</p>';

                        if ($employee->line_manager) {
                            if ($employee->line_manager->official_email) {
                                $to = ['muhammad.sohail@trax.pk', 'shahzad.ali@trax.pk', $employee->line_manager->official_email];
                            } else {
                                $to = ['muhammad.sohail@trax.pk', 'shahzad.ali@trax.pk'];
                            }
                        } else {
                            $to = ['muhammad.sohail@trax.pk', 'shahzad.ali@trax.pk'];
                        }

                        self::email($subject, $body, $to);
                    }
                } else if ($id == 183) {
                    $detail = $reference_1_id;
                    if (strpos($body, '[tracking_number]') !== FALSE) {
                        $body = str_replace('[tracking_number]', $detail['tracking_number'], $body);
                    }
                    if (strpos($body, '[consignee]') !== FALSE) {
                        $body = str_replace('[consignee]', $detail['name'], $body);
                    }
                    if (strpos($body, '[shipper]') !== FALSE) {
                        $body = str_replace('[shipper]', $detail['shipper_name'], $body);
                    }
                    $to = $detail['shipper_number_1'];
                    self::sms($body, $to);
                    if ($detail['shipper_number_2'] != NULL) {
                        $to = $detail['shipper_number_2'];
                        self::sms($body, $to);
                    }
                } else if ($id == 184) {
                    $detail = $reference_1_id;
                    if (strpos($body, '[consignee]') !== FALSE) {
                        $body = str_replace('[consignee]', $detail['name'], $body);
                    }
                    if (strpos($body, '[tracking_number]') !== FALSE) {
                        $body = str_replace('[tracking_number]', $detail['tracking_number'], $body);
                    }
                    $to = $detail['consignee_number_1'];
                    self::sms($body, $to);
                    if ($detail['consignee_number_2'] != NULL) {
                        $to = $detail['consignee_number_2'];
                        self::sms($body, $to);
                    }
                } else if ($id == 185) {
                    $one_link_transaction = OneLinkOutForDeliveryShipmentPayment::find($reference_2_id);
                    $rider = Rider::find($reference_1_id);
                    if ($one_link_transaction && $rider) {
                        if (strpos($body, '[amount]') !== FALSE) {
                            $body = str_replace('[amount]', $one_link_transaction->transaction_amount, $body);
                        }
                        if (strpos($body, '[tracking_number]') !== FALSE) {
                            $body = str_replace('[tracking_number]', $one_link_transaction->tracking_number, $body);
                        }
                        if (strpos($body, '[rider]') !== FALSE) {
                            $body = str_replace('[rider]', $rider->name, $body);
                        }
                        $to = $rider->phone;
                        self::sms($body, $to);
                    }
                } else if ($id == 186) {
                    $role = $reference_2_id;
                    $admin = Admin::find($reference_1_id);
                    if ($admin && $role) {
                        if (strpos($body, '[admin]') !== FALSE) {
                            $body = str_replace('[admin]', $admin->name, $body);
                        }
                        if (strpos($body, '[role]') !== FALSE) {
                            $body = str_replace('[role]', $role, $body);
                        }
                        $to = ['anas.anwer@trax.pk', 'danish.zahid@trax.pk', 'umair.badar@trax.pk'];
                        self::email($subject, $body, $to);
                    }
                } else if ($id == 190) {

                    $reference_1_id = Carbon::parse($reference_1_id)->subDay()->toDateString();
                    if (strpos($subject, '[date]') !== FALSE) {
                        $subject = str_replace('[date]', $reference_1_id, $subject);
                    }

                    if (strpos($body, '[date]') !== FALSE) {
                        $body = str_replace('[date]', $reference_1_id, $body);
                    }

                    $link = '<a href="' . $reference_2_id . '" target="_blank">Report</a>';

                    if (strpos($subject, '[link]') !== FALSE) {
                        $subject = str_replace('[link]', $link, $subject);
                    }

                    if (strpos($body, '[link]') !== FALSE) {
                        $body = str_replace('[link]', $link, $body);
                    }
                    $date = Carbon::today()->startOfDay()->toDateTimeString();
                    $date_end = Carbon::today()->endOfDay()->toDateTimeString();
                    $month_average_data = MonthAverageDestination::whereBetween('created_at', [$date, $date_end])->orderBy('shipments', 'desc')->get();
                    $html = '<table><thead><tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>S No.</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Destination</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Total Parcel</strong></th><th style="padding:5px; border: 1px solid black; border-collapse: collapse;"><strong>Avg Shipments/Day</strong></th></tr></thead><tbody>';
                    $serial = 1;
                    $shipments_count = 0;
                    $revenue_count = 0;
                    $avg_revenue_count = 0;
                    $avg_shipments_count = 0;
                    $avg_revenue_per_day_count = 0;
                    $month_speed_count = 0;
                    foreach ($month_average_data as $month_average) {
                        $html .= '<tr>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $month_average->city->name . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format($month_average->shipments) . '</td>';
                        // $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format(round($month_average->revenue)) . '</td>';
                        // $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format(round($month_average->avg_revenue)) . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format(round($month_average->avg_shipments)) . '</td>';
                        // $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format(round($month_average->avg_revenue_per_day)) . '</td>';
                        // $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format(round($month_average->month_speed)) . '</td>';
                        $html .= '</tr>';
                        $shipments_count = $shipments_count + $month_average->shipments;
                        $revenue_count = $revenue_count + $month_average->revenue;
                        $avg_shipments_count = $avg_shipments_count + $month_average->avg_shipments;
                        $avg_revenue_per_day_count = $avg_revenue_per_day_count + $month_average->avg_revenue_per_day;
                        $month_speed_count = $month_speed_count + $month_average->month_speed;
                        $serial++;
                    }
                    if ($shipments_count != 0) {
                        $avg_revenue_count = $revenue_count / $shipments_count;
                    } else {
                        $avg_revenue_count = 0;
                    }
                    $html .= '<tr>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">Total</td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"></td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format($shipments_count) . '</td>';
                    // $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format(round($revenue_count)) . '</td>';
                    // $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format(round($avg_revenue_count)) . '</td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format(round($avg_shipments_count)) . '</td>';
                    // $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format(round($avg_revenue_per_day_count)) . '</td>';
                    // $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . number_format(round($month_speed_count)) . '</td>';
                    $html .= '</tr>';

                    // $html .= '</tr>';
                    $html .= '</tbody></table>';

                    if (strpos($body, '[preview]') !== FALSE) {
                        $body = str_replace('[preview]', $html, $body);
                    }

                    $to = array();
                    // $to = ['mohsin.ali@trax.pk', 'waqas@trax.pk', 'khan.usama@trax.pk', 'hassan@trax.pk', 'noman.aziz@trax.pk', 'asad@trax.pk', 'fawad.ahmed@trax.pk'];
                    $to = ['info@trax.pk', 'mohsin.ali@trax.pk', 'waqas@trax.pk', 'hassan@trax.pk', 'noman.aziz@trax.pk', 'asad@trax.pk', 'fawad.ahmed@trax.pk', 'nadir.qureshi@trax.pk'];
                    $cc = array();
                    $bcc = array();
                    $bcc = ['muhammad.waqas@trax.pk'];

                    self::email($subject, $body, $to, $cc, $bcc);
                } else if ($id == 191) {

                    $crm_id = $reference_1_id->id;
                    $tracking_number = $reference_1_id->tracking_number;
                    $dnt = Carbon::parse($reference_1_id->updated_at)->toDateTimeString();
                    $reason = $reference_1_id->reason;

                    if (strpos($body, '[Shipper name]') !== FALSE) {
                        $body = str_replace('[Shipper name]', $reference_1_id->shipper_name, $body);
                    }

                    $body .= PHP_EOL . PHP_EOL . '<table style="padding:5px; border: 1px solid black; border-collapse: collapse;"><tbody><tr style="padding:5px;border: 1px solid black"> <th style="padding:5px;border: 1px solid black">CRM Request ID</th> <th style="padding:5px;border: 1px solid black"> Tracking Number </th> <th style="padding:5px;border: 1px solid black"> Close Date & Time </th> <th style="padding:5px;border: 1px solid black"> Reason </th> </tr>';
                    $body .= '<tr style="padding:5px;border: 1px solid black"> <td style="padding:5px;border: 1px solid black"> ' . $crm_id . ' </td> <td style="padding:5px;border: 1px solid black"> ' . $tracking_number . ' </td> <td style="padding:5px;border: 1px solid black"> ' . $dnt . ' </td> <td style="padding:5px;border: 1px solid black"> ' . $reason . ' </td> </tr>';
                    $body .= '</table>';

                    $to = $reference_1_id->email;

                    self::email($subject, $body, $to);
                } else if ($id == 192) {
                    $rider_id = $reference_1_id;
                    $shipment_id = $reference_2_id;
                    $name = '';
                    $rider = Rider::find($rider_id);
                    $shipment = Shipment::find($shipment_id);
                    $shipment_otp = ShipmentOtp::where('shipment_id', $shipment_id)->where('rider_id', $rider_id)->whereDate('updated_at', Carbon::today())->first();

                    if (strpos($body, '[consignee_name]') !== FALSE) {
                        $body = str_replace('[consignee_name]', $shipment->consignee_name, $body);
                        $name = $shipment->consignee_name;
                    }
                    if (strpos($body, '[rider_name]') !== FALSE) {
                        $body = str_replace('[rider_name]', $rider->name, $body);
                        $name = $rider->name;
                    }

                    if (strpos($body, '[tracking_number]') !== FALSE) {
                        $body = str_replace('[tracking_number]', $shipment->tracking_number, $body);
                    }

                    if (strpos($body, '[otp]') !== FALSE) {
                        $body = str_replace('[otp]', $shipment_otp->otp, $body);
                    }

                    $to = $shipment->consignee_phone_number_1;
                    // self::sms($body, $to, 1);
                    self::sms_otp($body, $to, $name, $shipment_otp->otp, 3);
                    // self::sms_otp($body, $to, $name, 21323, 3);
                    if ($shipment->consignee_phone_number_2 != NULL) {
                        $to = $shipment->consignee_phone_number_2;
                        // self::sms($body, $to, 1);
                        self::sms_otp($body, $to, $name, $shipment_otp->otp, 3);

                    }
                } else if ($id == 205) {
                    $subject = $notification->subject;
                    $body = $notification->body;
                    $shipment_details = $reference_1_id;
                    $admin = $reference_2_id;
                    $html = '<table style="width:100%;">';
                    $html .= '<thead><tr>
                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">S No.</th>
                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Tracking Number</th>
                            <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Consignee Name</th>
                            <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Consignee Phone Number</th>
                            <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Consignee Address</th>
                            <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Consignee City</th>
                            <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Amount</th>
                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">DNCC Number</th> 
                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Status</th> 
                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Delivered At</th>
                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Reverted At</th>
                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Reverted By</th>';
                    $html .= '</tr></thead><tbody>';

                    $serial_number = 0;
                    foreach ($shipment_details as $index => $shipment_detail) {
                        $serial_number++;
                        $html .= '<tr>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial_number . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment_detail['tracking_number'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment_detail['consignee_name'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment_detail['consignee_phone'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment_detail['consignee_address'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment_detail['consignee_city'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment_detail['amount'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment_detail['dncc'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment_detail['status'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment_detail['delivered_at'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment_detail['reverted_at'] . '</td>';
                        $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment_detail['reverted_by'] . '</td>';
                        $html .= '</tr>';
                    }

                    $html .= '</tbody></table>';

                    if (strpos($body, '[admin]') !== FALSE) {
                        $body = str_replace('[admin]', $admin->name, $body);
                    }

                    if (strpos($body, '[preview]') !== FALSE) {
                        $body = str_replace('[preview]', $html, $body);
                    }
                    $to = $admin->email;

                    self::email($subject, $body, $to);
                } else if ($id == 206) {
                    $subject = 'Retail Sales Report By Arrival Date  | ';
                    $file_path = $reference_1_id['file_path'];
                    $from = $reference_1_id['from'];
                    $to = $reference_1_id['to'];

                    $subject .= 'From ( ' . $from . ' - ' . $to . ' )';

                    $file = Storage::disk('public')->url($file_path);
                    $link = '<a href="' . $file . '" target="_blank"><u>Download</u></a>';

                    if (strpos($body, '[link]') !== FALSE) {
                        $body = str_replace('[link]', $link, $body);
                    }

                    $to = ['shafay.tariq@trax.pk', 'adnan.ahsan@trax.pk', 'fawad.ahmed@trax.pk', 'hammad.majid@trax.pk'];

                    $cc = ["muhammad.waqas@trax.pk", "danish.zahid@trax.pk"];

                    self::email($subject, $body, $to, $cc);
                } else if ($id == 207) {
                    $file_path = $reference_1_id['file_path'];
                    $from = $reference_1_id['from'];
                    $to = $reference_1_id['to'];
                    $subject = 'Retail Sales Report By Delivery Date  | ';

                    $subject .= 'From ( ' . $from . ' - ' . $to . ' )';
                    $file = Storage::disk('public')->url($file_path);
                    $link = '<a href="' . $file . '" target="_blank"><u>Download</u></a>';

                    if (strpos($body, '[link]') !== FALSE) {
                        $body = str_replace('[link]', $link, $body);
                    }

                    $to = ['shafay.tariq@trax.pk', 'adnan.ahsan@trax.pk', 'fawad.ahmed@trax.pk', 'hammad.majid@trax.pk'];

                    $cc = ["muhammad.waqas@trax.pk", "danish.zahid@trax.pk"];

                    self::email($subject, $body, $to, $cc);
                } else if ($id == 210) {
                    $possible_fields = ['pickup_city', 'consignee_name', 'consignee_city', 'order_id', 'weight', 'tracking_number', 'item_product_type', 'item_description', 'item_quantity', 'amount'];

                    $field_names = ['pickup_city' => 'Pickup City', 'consignee_name' => 'Consignee Name', 'consignee_city' => 'Consignee City', 'order_id' => 'Order ID', 'weight' => 'Weight', 'tracking_number' => 'Tracking Number', 'item_product_type' => 'Item Product Type', 'item_description' => 'Item Description', 'item_quantity' => 'Item Quantity', 'amount' => 'Amount'];

                    $present_fields = array();

                    $first_field = NULL;

                    $position = NULL;

                    foreach ($possible_fields as $field) {
                        $new_position = strpos($body, '[' . $field . ']');

                        if ($new_position !== FALSE) {
                            if ($position == NULL) {
                                $present_fields[] = $field;

                                $first_field = $field;
                            } else if ($new_position > $position) {
                                $present_fields[] = $field;
                            } else {
                                array_unshift($present_fields, $field);
                            }

                            $position = $new_position;
                        }
                    }

                    $today = Carbon::now()->toDateTimeString();

                    $user_wise_shipments = array();

                    $origin_hub_ids = array();

                    foreach ($reference_1_id as $shipment_id) {
                        $shipment = Shipment::find($shipment_id);

                        $origin_hub_id = $shipment->pickup_address->city->hub_id;

                        if (!in_array($origin_hub_id, $origin_hub_ids)) {
                            $origin_hub_ids[] = $origin_hub_id;
                        }

                        $details = array();

                        $details['pickup_city'] = $shipment->pickup_address->city->name;
                        $details['consignee_name'] = $shipment->consignee_name;
                        $details['consignee_city'] = $shipment->consignee_city->name;
                        $details['order_id'] = $shipment->order_id;
                        $details['weight'] = $shipment->actual_weight;
                        $details['tracking_number'] = $shipment->tracking_number;
                        $details['amount'] = $shipment->amount;

                        if ($shipment->booking_type_id == 1 || $shipment->booking_type_id == 2) {
                            foreach ($shipment->items as $item) {
                                if ($item->type == 0) {
                                    $details['item_product_type'] = $item->product->product_name;
                                    $details['item_description'] = $item->description;
                                    $details['item_quantity'] = $item->quantity;
                                }
                            }
                        } else {
                            $details['item_product_type'] = '';
                            $details['item_description'] = '';
                            $details['item_quantity'] = '';
                        }

                        $user_wise_shipments[$shipment->user_id][] = $details;
                    }

                    $original_subject = $subject;
                    $original_body = $body;

                    foreach ($user_wise_shipments as $user_id => $shipments) {
                        $shipper = User::find($user_id);

                        if (strpos($subject, '[company_name]') !== FALSE) {
                            $subject = str_replace('[company_name]', $shipper->name, $subject);
                        }

                        if (strpos($body, '[company_name]') !== FALSE) {
                            $body = str_replace('[company_name]', $shipper->name, $body);
                        }

                        if (strpos($subject, '[received_at]') !== FALSE) {
                            $subject = str_replace('[received_at]', $today, $subject);
                        }

                        if (strpos($body, '[received_at]') !== FALSE) {
                            $body = str_replace('[received_at]', $today, $body);
                        }

                        //              $to = $shipper->email;

                        if (ShipperNotificationEmail::where('user_id', $shipper->id)->exists()) {
                            $to = ShipperNotificationEmail::where('user_id', $shipper->id)->whereNotNull('email')->pluck('email')->toArray();
                        } else {
                            $to = $shipper->email;
                        }
                        $shipment_details = '<table style="padding:5px; border: 1px solid black; border-collapse: collapse;"><tbody><tr>';

                        $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">S. No.</td>';

                        foreach ($present_fields as $field) {
                            $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">' . $field_names[$field] . '</td>';
                        }

                        $shipment_details .= '</tr>';

                        $serial_number = 1;

                        foreach ($shipments as $shipment) {
                            $shipment_details .= '<tr>';

                            $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial_number . '</td>';

                            foreach ($present_fields as $field) {
                                if (!empty($shipment[$field])) {
                                    $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment[$field] . '</td>';
                                } else {
                                    $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"></td>';
                                }
                            }

                            $shipment_details .= '</tr>';

                            $serial_number++;
                        }

                        $shipment_details .= '</tbody></table>';

                        foreach ($present_fields as $field) {
                            if ($field != $first_field) {
                                $body = str_replace('[' . $field . ']', '', $body);
                            }
                        }

                        $body = str_replace('[' . $first_field . ']', $shipment_details, $body);

                        $bcc = array();


                        $related_admins = Admin::whereIn('role_id', [10])->where('status', 1)->where('id', '!=', 276)->whereNotNull('email')->whereHas('hubs', function ($query) use ($origin_hub_ids) {
                            $query->whereIn('hub_id', $origin_hub_ids);
                        });

                        if ($related_admins->exists()) {
                            $bcc = array_merge($bcc, $related_admins->pluck('email')->toArray());
                        }

                        if (empty($bcc)) {
                            $bcc = NULL;
                        }

                        self::email($subject, $body, $to, NULL, $bcc);

                        $subject = $original_subject;
                        $body = $original_body;
                    }
                } else if ($id == 208) {
                    $employee_id = $reference_1_id;
                    $employee = Employee::find($employee_id);

                    if ($employee) {
                        if (strpos($subject, '[employee_name]') !== FALSE) {
                            $subject = str_replace('[employee_name]', $employee->name, $subject);
                        }

                        if (strpos($body, '[employee_name]') !== FALSE) {
                            $body = str_replace('[employee_name]', $employee->name, $body);
                        }

                        if (strpos($body, '[emp_id]') !== FALSE) {
                            $body = str_replace('[emp_id]', $employee->trax_id, $body);
                        }

                        $line_manager = Employee::find($employee->line_manager_id);
                        if ($line_manager) {
                            self::email($subject, $body, $line_manager->official_email);
                        }
                    }
                } else if ($id == 209) {
                    $subject = $notification->subject;
                    $body = $notification->body;
                    $date = Carbon::now();
                    $line_managers = Employee::where('is_line_manager', 1)->where('official_email', '!=', null)->get();
                    $original_subject = $subject;
                    $original_body = $body;

                    foreach ($line_managers as $line_manager) {

                        $html = '<table style="width:100%;">';
                        $html .= '<thead><tr>
                                <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Trax ID</th>
                                <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Name</th>
                                <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Designation</th>
                                <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Leaves Availed</th>
                                <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Late</th>
                                <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Attendance Adjustment</th>
                                <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Date</th>
                                <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Day</th>
                                <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Clock In</th>
                                <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Clock Out</th>';
                        $html .= '</tr></thead><tbody>';
                        $employees_attendances = Employee::join('employee_attendances as ea', 'ea.employee_id', '=', 'employees.id')
                            ->join('employee_designations as ed', 'ed.id', '=', 'employees.designation_id')
                            ->join('employee_shifts as es', 'es.id', '=', 'employees.shift_id')
                            ->leftjoin('employee_attendance_adjustments as eaa', 'eaa.id', '=', 'employees.id')
                            ->select('employees.trax_id', 'employees.name as name', 'ed.name as designation', 'ea.leave_status', 'ea.clock_in_datetime', 'ea.clock_out_datetime', 'ea.attendance_date as attendance_date', 'es.start_time', 'es.extension_minutes', 'eaa.status')
                            ->where('employees.line_manager_id', $line_manager->id)
                            ->whereBetween('ea.attendance_date', [$reference_1_id, $reference_2_id])
                            ->orderBy('ea.attendance_date')
                            ->get();

                        $employees_attendances_count = count($employees_attendances);

                        $summary = [];
                        foreach ($employees_attendances as $index => $employees_attendance) {
                            $expected_clockin = Carbon::createFromFormat('Y-m-d H:i:s', $employees_attendance->attendance_date . $employees_attendance->start_time)->addMinutes((int) $employees_attendance->extension_minutes);
                            $clock_in = Carbon::parse($employees_attendance->clock_in_datetime);
                            $time_diff = $expected_clockin->diffInMinutes(Carbon::parse($clock_in), false);
                            $html .= '<tr>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $employees_attendance->trax_id . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $employees_attendance->name . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $employees_attendance->designation . '</td>';
                            if ($employees_attendance['leave_status'] == 0) {
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">No</td>';
                            } else {
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">Yes</td>';
                            }
                            if ($time_diff > 0) {
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">Yes</td>';
                            } else {
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">No</td>';
                            }
                            if ($employees_attendance['status'] == 0) {
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">No</td>';
                            } else {
                                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">Yes</td>';
                            }
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $employees_attendance->attendance_date . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . Carbon::parse($employees_attendance->attendance_date)->format('l') . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $employees_attendance['clock_in_datetime'] . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $employees_attendance['clock_out_datetime'] . '</td>';
                            $html .= '</tr>';
                            $summary[] = AdminReportsEmailController::weekly_attendence_summary($employees_attendance);
                        }

                        $html .= '</tbody></table>';
                        if ($employees_attendances_count > 0 && isset($employees_attendances)) {

                            $link = AdminReportsEmailController::weekly_attendence_summary_excel($summary, $line_manager->id);
                            if (strpos($body, '[line_manager]') !== FALSE) {
                                $body = str_replace('[line_manager]', $line_manager->name, $body);
                            }
                            if (strpos($body, '[preview]') !== FALSE) {
                                $body = str_replace('[preview]', $html, $body);
                                $line_manager->official_email;
                            }
                            if (strpos($body, '[link]') !== FALSE) {
                                $body = str_replace('[link]', $link, $body);
                                $line_manager->official_email;
                            }

                            self::email($subject, $body, $line_manager->official_email);
                            $html = '';
                            $subject = $original_subject;
                            $body = $original_body;
                        }
                    }
                } else if ($id == 211) {
                    $subject = $notification->subject;
                    $body = $notification->body;
                    if (strpos($body, '[Date&Day]') !== FALSE) {
                        $body = str_replace('[Date&Day]', $reference_2_id, $body);
                    }

                    self::email($subject, $body, $reference_1_id);
                } else if ($id == 213) {
                    $subject = $notification->subject;
                    $body = $notification->body;
                    if (strpos($body, '[link]') !== FALSE) {
                        $body = str_replace('[link]', $reference_1_id, $body);
                    }
                    $to = array();
                    $general_admins = Admin::whereIn('role_id', [7, 2])->where('status', 1)->whereNotNull('email');

                    if ($general_admins->exists()) {
                        $to = array_merge($to, $general_admins->pluck('email')->toArray());
                    }
                    self::email($subject, $body, $to);
                } else if ($id == 212) {
                    $employee_id = $reference_1_id;
                    $employee = Employee::find($employee_id);

                    if ($employee) {
                        if (strpos($subject, '[employee_name]') !== FALSE) {
                            $subject = str_replace('[employee_name]', $employee->name, $subject);
                        }

                        if (strpos($body, '[employee_name]') !== FALSE) {
                            $body = str_replace('[employee_name]', $employee->name, $body);
                        }

                        $line_manager = Employee::whereNotNull('official_email')
                            ->where('id', $employee->line_manager_id)
                            ->first();

                        if ($line_manager) {
                            self::email($subject, $body, $line_manager->official_email);
                        }
                    }
                } else if ($id == 214) {
                    //                    $subject = $notification->subject;
                    $file_path = $reference_1_id['file_path'];

                    $from = $reference_1_id['from'];
                    $to = $reference_1_id['to'];
                    $subject = 'Revenue Daily Report By Arrival Date  | ';

                    $subject .= $from;

                    $file = Storage::disk('public')->url($file_path);

                    $link = '<a href="' . $file . '" target="_blank"><u>Download</u></a>';

                    //                    $date = $from;
                    //                    if (strpos($subject, '[date]') !== FALSE) {
                    //                        $subject = str_replace('[date]', $date, $subject);
                    //                    }

                    if (strpos($body, '[link]') !== FALSE) {
                        $body = str_replace('[link]', $link, $body);
                    }

                    $to = ['tanveer.malik@trax.pk', 'muhammad.jawwad@trax.pk', 'fawad.ahmed@trax.pk', 'waqas@trax.pk', 'shafay.tariq@trax.pk', 'huzaifa.aamir@trax.pk', 'hammad.majid@trax.pk'];

                    $cc = ["muhammad.waqas@trax.pk", "danish.zahid@trax.pk"];

                    self::email($subject, $body, $to, $cc);
                } else if ($id == 215) {

                    $details = $reference_1_id;
                    $temp_emails = $reference_2_id;
                    $subject = $notification->subject;
                    $body = $notification->body;

                    if (strpos($body, '[preview]') !== FALSE) {
                        $body = str_replace('[preview]', $details, $body);
                    }

                    foreach ($temp_emails as $temp_email) {
                        self::email($subject, $body, $temp_email);
                    }
                } // Today work has been done
                else if ($id == 216) {

                    foreach ($reference_1_id as $key => $return_note) {

                        $old_body = $body;

                        if (strpos($old_body, '[return_notes_id]') !== FALSE) {
                            $old_body = str_replace('[return_notes_id]', $return_note->return_id, $old_body);
                        }

                        if (strpos($old_body, '[shipments_count]') !== FALSE) {
                            //                            dd($old_body,$return_note->shipment_count);
                            $old_body = str_replace('[shipments_count]', $return_note->shipment_count, $old_body);
                        }
                        $to = $return_note['phone_number'];

                        self::sms($old_body, $to);
                        // $notify[$key] = [
                        //     'return_note_id'=>$return_noted->return_id,
                        //     'user_id'=>$return_noted->user_id,
                        //     'shipment_count'=>$return_noted->total_shipments,
                        //     'status'=>1,
                        //     'created_at'=>Carbon::now(),
                        //     'updated_at'=>Carbon::now(),

                        // ];

                    }

                    // $user_ids = $reference_1_id->pluck('user_id')->toArray();
                    // ReturnDeliveredToShipperSms::whereIn('user_id',$user_ids)->update(['status'=>0]);
                    // ReturnDeliveredToShipperSms::insert($notify);
                } else if ($id == 217) {
                    $fintech_transaction = FintechPaymentDetails::join('trax_pay_transactions', 'fintech_payment_details.trax_pay_id', 'trax_pay_transactions.id')
                        ->join('shipments', 'trax_pay_transactions.shipment_id', 'shipments.id')
                        ->select(
                            'trax_pay_transactions.cod_amount as Amont',
                            'shipments.tracking_number as tracking_no',
                            'fintech_payment_details.rider_tip as tip'
                        )
                        ->where('trax_pay_transactions.id', $reference_2_id)->first();
                    $rider = Rider::find($reference_1_id);
                    if ($fintech_transaction && $rider) {
                        if (strpos($body, '[amount]') !== FALSE) {
                            $body = str_replace('[amount]', $fintech_transaction->Amont, $body);
                        }
                        if (strpos($body, '[tracking_number]') !== FALSE) {
                            $body = str_replace('[tracking_number]', $fintech_transaction->tracking_no, $body);
                        }
                        if (strpos($body, '[tip]') !== FALSE) {
                            $body = str_replace('[tip]', $fintech_transaction->tip, $body);
                        }
                        if (strpos($body, '[rider]') !== FALSE) {
                            $body = str_replace('[rider]', $rider->name, $body);
                        }
                        // $to = $rider->phone;
                        $to = '03110127222';
                        // self::sms($body, $to);
                    }
                } else if ($id == 218) {
                    $subject = $notification->subject;
                    $body = $notification->body;

                    $email_to = $reference_1_id['email_to'];

                    $shipper = $reference_1_id['shipper_name'];
                    $tagged_by = $reference_1_id['admin_name'];

                    $new_poc_person = $reference_1_id['new_poc_person'];
                    $new_kam_person = $reference_1_id['new_kam_person'];
                    $new_ref_person = $reference_1_id['new_ref_person'];


                    $old_poc_person = $reference_1_id['old_poc_person'];
                    $old_kam_person = $reference_1_id['old_kam_person'];
                    $old_ref_person = $reference_1_id['old_ref_person'];
                    $old_person_date = $reference_1_id['old_person_date'];
                    $old_person_email = $reference_1_id['old_person_email'];





                    $html = '<table style="width:100%;">';
                    $html .= '<thead><tr>
                        <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Shipper Name</th>
                            <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Tagged By</th>
                        <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Old Person(s)</th> 
                        <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Old Person(s) Date</th> 
                        <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">New Person(s)</th>';
                    $html .= '</tr></thead><tbody>';

                    $to = array();
                    $cc = array('Waqas@trax.pk', 'shahrukh.raheem@trax.pk', 'khan.usama@trax.pk');

                    $old_person_header = '<tr> <th> POC </th> <th> KAM </th> <th> REF </th> </tr>';

                    $html .= '<tr>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipper . '</td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $tagged_by . '</td>';

                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"> <table border="1"> ' . $old_person_header . ' <tr> <td>' . $old_poc_person . '</td> <td>' . $old_kam_person . '</td> <td>' . $old_ref_person . '</td> </tr> </table> </td>';
                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $old_person_date . '</td>';


                    $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"> <table border="1"> ' . $old_person_header . ' <tr> <td>' . $new_poc_person . '</td> <td>' . $new_kam_person . '</td> <td>' . $new_ref_person . '</td> </tr> </table> </td>';
                    $html .= '</tr>';

                    $to = $email_to;


                    if (!empty($old_person_email)) {
                        $cc = array_merge($cc, $old_person_email);
                    }

                    $html .= '</tbody></table>';

                    if (strpos($body, '[preview]') !== FALSE) {
                        $body = str_replace('[preview]', $html, $body);
                    }

                    // dd($subject, $body, $to, $cc);
                    self::email($subject, $body, $to, $cc);
                } else if ($id == 221) {
                    $responses = $reference_1_id;
                    $to = 'mohsin.khan@trax.pk';
                    $cc = 'shahrukh.raheem@trax.pk';

                    $date = Carbon::now()->toFormattedDateString();

                    $preview = view('email.crm-response-rate-email-template', [
                        'responses' => $responses,
                        'date' => $date
                        ])->render();

                    $body=str_replace('[preview]', $preview, $body);

                    self::email($subject, $body, $to, $cc);
                } else if ($id = 222) {
                    $array = array();
                    $crm_case_closeds = $reference_1_id;
                    
                    foreach ($crm_case_closeds as $item) {
                        $shipperId = $item['shipper_id'];

                        if (!isset($array[$shipperId])) {
                            $array[$shipperId] = [];
                        }

                        $array[$shipperId][] = $item;
                    }


                    foreach ($array as $shipperId => $items) {
                        $ids = array();
                        $email = User::where('id', $shipperId)->pluck('email')->toArray();
                        $shipper_name = User::where('id', $items[0]['shipper_id'])->value('name');
                        
                        foreach ($items as $key => $value) {
                            $ids[] = $value['id'];
                        }

                        $ids = implode(',', $ids);

                        $htmlHeader = '<table style="width:100%; border-collapse: collapse; border: 1px solid black;">';
                        $htmlHeader .= '<thead><tr style="background-color: #f2f2f2;">
                                            <th style="padding:10px; border: 1px solid black;">Request #</th>
                                            <th style="padding:10px; border: 1px solid black;">Type</th>
                                            <th style="padding:10px; border: 1px solid black;">Resolution</th>
                                            <th style="padding:10px; border: 1px solid black;">Status</th>
                                            <th style="padding:10px; border: 1px solid black;">Resolved Within</th>
                                        </tr></thead><tbody>';

                        $htmlHeader .= '<p style="text-align: center; font-size: 16px;">';
                        $htmlHeader .= '<a style="display: inline-block; padding: 10px 20px; background-color: #3498db; color: #fff; text-decoration: none; border-radius: 5px; font-weight: bold;" href="' . route('survey.feedback.index', ['ids' => $ids]) . '">Click Here To Rate</a>';
                        $htmlHeader .= '</p>';
                        $html = $htmlHeader;


                        foreach ($items as $item) {
                            $resolved_within = $item['created_at']->diffInDays($item['updated_at']);
                            $type = CrmRequestCaseNatureType::where('id', $item["case_nature_type_id"])->value('type');

                            $resolution = ShipmentsJourney::where('shipment_id', $item['shipment_id'])->latest()->first();
                            $resolution = ShipmentStatusReason::where('id', $resolution["shipper_status_id"])->latest()->first();

                            $html .= '<tr>';

                            $html .= '<td style="padding:5px; border: 1px solid black;">' . $item["id"] . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black;">' . ($type ?? '-') . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black;">' . ($resolution['name'] ?? '-') . '</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black;">Closed</td>';
                            $html .= '<td style="padding:5px; border: 1px solid black;">' . ($resolved_within == 0 ? '1 Day' : $resolved_within . ' Days') . '</td>';

                            $html .= '</tr>';
                        }

                        $html .= '</tbody>
                        </table>
                       ';


                        $body = str_replace('[preview]', $html, $notification->body);
                        $body = str_replace('[shipper]', $shipper_name ?? 'Valued Customer', $body);
                        self::email($subject, $body, $email);
                    }

                } 
            }
        }
    }
    static public function custom($type, $subject, $body, $to)
    {
        if ($type == 1) {
            self::email($subject, $body, $to);
        }
    }
    static public function custom_sms($body, $to)
    {
        self::sms($body, $to);
    }

    static public function bolt_app_notification($employee_id, $employee_type, $device_token, $notification_title, $notification_body)
    {
        $server_key = 'AAAAPew_cdc:APA91bEJb7w_3-rOI5Pkr1wVVG9Qtl_WBQh_fEEk1N0yY-CHeUwOWKmSUODGhFbGuJv-BaqY-NS6KAYIo3Cw_UyKm2PvlM4reEae1SPj-y75z0Eu722IYUUqm_M2W9UOYnu40QyCIFGL';
        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

        $message = [
            'data' => [
                'title' => $notification_title,
                'body' => $notification_body
            ],
            'to' => $device_token
        ];

        $client = new Client(['base_uri' => $fcmUrl, 'http_errors' => FALSE, 'connect_timeout' => 120, 'timeout' => 120]);

        $response = $client->post('', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'key=' . $server_key,
            ],
            'body' => json_encode($message)
        ]);
        $response = json_decode($response->getBody()->getContents(), true);
        if ($response['success'] != 0) {
            $notification_history = new EmployeeNotificationHistory();
            $notification_history->employee_id = $employee_id;
            $notification_history->employee_type_id = $employee_type;
            $notification_history->title = $notification_title;
            $notification_history->message = $notification_body;
            $notification_history->save();
        }
    }

    static public function bolt_forget_pin($phone_number, $pin, $name)
    {
        $notification = Notification::find(61);
        $body = $notification->body;
        if (strpos($body, '[rider_name]') !== FALSE) {
            $body = str_replace('[rider_name]', $name, $body);
        }
        if (strpos($body, '[pin]') !== FALSE) {
            $body = str_replace('[pin]', $pin, $body);
        }

        $to = $phone_number;
        self::sms($body, $to, 1);
    }

    static public function trax_otp_verification($phone_number, $pin)
    {
        $body = 'Dear Consignee,' . PHP_EOL . 'Your OTP for Trax is: [pin]';
        if (strpos($body, '[pin]') !== FALSE) {
            $body = str_replace('[pin]', $pin, $body);
        }
        $to = $phone_number;
        self::sms_otp($body, $to, "Consignee", $pin, 1);
    }

    static public function app_notification($id, $employee_id, $employee_type, $reference1_id, $reference2_id = NULL)
    {
        $push_notification = AppNotification::find($id);
        if ($push_notification) {
            if ($push_notification->status) {
                $title = $push_notification->title;
                $body = $push_notification->body;
                if ($id == 1) {
                    $rider = Rider::find($reference1_id);
                    $shipper = User::find($reference2_id);
                    if (strpos($body, '[shipper_name]') !== FALSE) {
                        $body = str_replace('[shipper_name]', $shipper->name, $body);
                    }
                    if (strpos($body, '[rider]') !== FALSE) {
                        $body = str_replace('[rider]', $rider->name, $body);
                    }
                    self::push_notification($employee_id, $employee_type, $title, $body);
                } else if ($id == 2) {
                    $rider = Rider::find($reference1_id);
                    $shipper = User::find($reference2_id);
                    if (strpos($body, '[shipper_name]') !== FALSE) {
                        $body = str_replace('[shipper_name]', $shipper->name, $body);
                    }
                    if (strpos($body, '[rider]') !== FALSE) {
                        $body = str_replace('[rider]', $rider->name, $body);
                    }
                    self::push_notification($employee_id, $employee_type, $title, $body);
                } else if ($id == 3) {
                    $shipper = User::find($reference1_id);
                    if (strpos($body, '[shipper_name]') !== FALSE) {
                        $body = str_replace('[shipper_name]', $shipper->name, $body);
                    }
                    self::push_notification($employee_id, $employee_type, $title, $body);
                } else if ($id == 4) {
                    $shipper = User::find($reference1_id);
                    if (strpos($body, '[shipper_name]') !== FALSE) {
                        $body = str_replace('[shipper_name]', $shipper->name, $body);
                    }
                    self::push_notification($employee_id, $employee_type, $title, $body);
                } else if ($id == 5) {
                    if (strpos($body, '[note_id]') !== FALSE) {
                        $body = str_replace('[note_id]', $reference1_id, $body);
                    }
                    self::push_notification($employee_id, $employee_type, $title, $body);
                } else if ($id == 6) {
                    if (strpos($body, '[note_id]') !== FALSE) {
                        $body = str_replace('[note_id]', $reference1_id, $body);
                    }
                    self::push_notification($employee_id, $employee_type, $title, $body);
                } else if ($id == 7) {
                    $shipment = Shipment::find($reference1_id);
                    $status = ShipmentStatus::find($reference2_id);
                    $shipper = User::find($employee_id);
                    if (strpos($body, '[shipper_name]') !== FALSE) {
                        $body = str_replace('[shipper_name]', $shipper->name, $body);
                    }
                    if (strpos($body, '[tracking_no]') !== FALSE) {
                        $body = str_replace('[tracking_no]', $shipment->tracking_number, $body);
                    }
                    if (strpos($body, '[status_name]') !== FALSE) {
                        $body = str_replace('[status_name]', $status->name, $body);
                    }
                    self::push_notification($employee_id, $employee_type, $title, $body);
                } else if ($id == 8) {
                    $shipment = Shipment::find($reference1_id);
                    $status = ShipmentStatus::find($reference2_id);
                    $consignee_user = ConsigneeUser::find($employee_id);
                    if (strpos($body, '[consignee_name]') !== FALSE) {
                        $body = str_replace('[consignee_name]', $consignee_user->name, $body);
                    }
                    if (strpos($body, '[tracking_no]') !== FALSE) {
                        $body = str_replace('[tracking_no]', $shipment->tracking_number, $body);
                    }
                    if (strpos($body, '[status_name]') !== FALSE) {
                        $body = str_replace('[status_name]', $status->name, $body);
                    }
                    self::push_notification($employee_id, $employee_type, $title, $body);
                } else if ($id == 9) {
                    $rider = Rider::find($employee_id);
                    if (strpos($body, '[rider]') !== FALSE) {
                        $body = str_replace('[rider]', $rider->name, $body);
                    }
                    if (strpos($body, '[otp]') !== FALSE) {
                        $body = str_replace('[otp]', $reference1_id, $body);
                    }
                    self::push_notification($employee_id, $employee_type, $title, $body);
                } else if ($id == 11) {
                    if ($employee_type == 1) {
                        $user = Admin::find($employee_id);
                    } else {
                        $user = Rider::find($employee_id);
                    }
                    $leave = EmployeeLeave::find($reference1_id);
                    if ($user && $leave) {
                        if ($leave->status == 1) {
                            $status = "Submitted";
                        } else {
                            $leave_status = LeaveStatus::find($leave->status);
                            $status = $leave_status->name;
                        }
                        if (strpos($body, '[from]') !== FALSE) {
                            $body = str_replace('[from]', $leave->from, $body);
                        }
                        if ($leave->to) {
                            if (strpos($body, '[to]') !== FALSE) {
                                $body = str_replace('[to]', $leave->to, $body);
                            }
                        } else {
                            if (strpos($body, '[to]') !== FALSE) {
                                $body = str_replace('[to]', $leave->from, $body);
                            }
                        }
                        if (strpos($body, '[status]') !== FALSE) {
                            $body = str_replace('[status]', $status, $body);
                        }
                        self::push_notification($employee_id, $employee_type, $title, $body);
                    }
                } else if ($id == 12) {
                    $leave = EmployeeLeave::find($reference1_id);
                    if ($leave) {
                        $user = Employee::find($leave->employee_id);
                        if (strpos($body, '[employee_name]') !== FALSE) {
                            $body = str_replace('[employee_name]', $user->name, $body);
                        }
                        if (strpos($body, '[trax_id]') !== FALSE) {
                            $body = str_replace('[trax_id]', $user->trax_id, $body);
                        }
                        if (strpos($body, '[from]') !== FALSE) {
                            $body = str_replace('[from]', $leave->from, $body);
                        }
                        if ($leave->to) {
                            if (strpos($body, '[to]') !== FALSE) {
                                $body = str_replace('[to]', $leave->to, $body);
                            }
                        } else {
                            if (strpos($body, '[to]') !== FALSE) {
                                $body = str_replace('[to]', $leave->from, $body);
                            }
                        }
                        self::push_notification($employee_id, $employee_type, $title, $body);
                    }
                } else if ($id == 13) {
                    $leave = EmployeeLeave::find($reference1_id);
                    if ($leave) {
                        if ($leave->employee_type_id == 1) {
                            $user = Admin::find($leave->employee_id);
                        } else {
                            $user = Rider::find($leave->employee_id);
                        }
                        if (strpos($body, '[employee_name]') !== FALSE) {
                            $body = str_replace('[employee_name]', $user->name, $body);
                        }
                        if (strpos($body, '[trax_id]') !== FALSE) {
                            $body = str_replace('[trax_id]', $user->trax_id, $body);
                        }
                        if (strpos($body, '[from]') !== FALSE) {
                            $body = str_replace('[from]', $leave->from, $body);
                        }
                        if ($leave->to) {
                            if (strpos($body, '[to]') !== FALSE) {
                                $body = str_replace('[to]', $leave->to, $body);
                            }
                        } else {
                            if (strpos($body, '[to]') !== FALSE) {
                                $body = str_replace('[to]', $leave->from, $body);
                            }
                        }
                        self::push_notification($employee_id, $employee_type, $title, $body);
                    }
                } else if ($id == 14) {
                    $lead = Lead::find($reference1_id);
                    $user = Admin::find($employee_id);
                    if (strpos($title, '[date]') !== FALSE) {
                        $date = Carbon::now()->format('Y-m-d');
                        $title = str_replace('[date]', $date, $title);
                    }
                    if ($lead && $user) {
                        if (strpos($body, '[sale_person]') !== FALSE) {
                            $body = str_replace('[sale_person]', $user->name, $body);
                        }
                        if (strpos($body, '[lead_id]') !== FALSE) {
                            $body = str_replace('[lead_id]', $lead->id, $body);
                        }
                        self::push_notification($employee_id, $employee_type, $title, $body);
                    }
                } else if ($id == 15) {
                    $lead = Lead::find($reference1_id);
                    $user = Admin::find($employee_id);
                    if ($lead && $user) {
                        if (strpos($body, '[sale_person]') !== FALSE) {
                            $body = str_replace('[sale_person]', $user->name, $body);
                        }
                        if (strpos($body, '[lead_id]') !== FALSE) {
                            $body = str_replace('[lead_id]', $lead->id, $body);
                        }
                        self::push_notification($employee_id, $employee_type, $title, $body, $lead->id);
                    }
                } else if ($id == 16) {
                    $admin = Admin::find($reference1_id);
                    $shippers = User::whereIn('id', $reference2_id)->select('name');
                    if ($admin && $shippers->exists()) {
                        $shippers = $shippers->get();
                        $shipper_name = '';
                        foreach ($shippers as $shipper) {
                            $shipper_name .= $shipper->name . PHP_EOL;
                        }
                        if (strpos($body, '[sale_person]') !== FALSE) {
                            $body = str_replace('[sale_person]', $admin->name, $body);
                        }
                        if (strpos($body, '[shipper_names]') !== FALSE) {
                            $body = str_replace('[shipper_names]', $shipper_name, $body);
                        }
                        self::push_notification($employee_id, $employee_type, $title, $body);
                    }
                } else if ($id == 17) {
                    if ($employee_type == 1) {
                        $user = Admin::find($employee_id);
                    } else {
                        $user = Rider::find($employee_id);
                    }
                    $leave = EmployeeAttendanceAdjustment::find($reference1_id);
                    if ($user && $leave) {
                        if ($leave->status == 1) {
                            $status = "Submitted";
                        } else {
                            $leave_status = LeaveStatus::find($leave->status);
                            $status = $leave_status->name;
                        }
                        if (strpos($body, '[date]') !== FALSE) {
                            $body = str_replace('[date]', $leave->date, $body);
                        }
                        if (strpos($body, '[status]') !== FALSE) {
                            $body = str_replace('[status]', $status, $body);
                        }
                        self::push_notification($employee_id, $employee_type, $title, $body);
                    }
                } else if ($id == 18) {
                    $leave = EmployeeAttendanceAdjustment::find($reference1_id);
                    if ($leave) {
                        $user = Employee::find($leave->employee_id);
                        if (strpos($body, '[employee_name]') !== FALSE) {
                            $body = str_replace('[employee_name]', $user->name, $body);
                        }
                        if (strpos($body, '[trax_id]') !== FALSE) {
                            $body = str_replace('[trax_id]', $user->trax_id, $body);
                        }
                        if (strpos($body, '[date]') !== FALSE) {
                            $body = str_replace('[date]', $leave->date, $body);
                        }
                        $admin_id = Admin::where('employee_id', $employee_id)->value('id');
                        self::push_notification($admin_id, $employee_type, $title, $body);
                    }
                } else if ($id == 19) {
                    $one_link_transaction = OneLinkOutForDeliveryShipmentPayment::find($reference2_id);
                    $rider = Rider::find($reference1_id);
                    if ($one_link_transaction && $rider) {
                        if (strpos($body, '[amount]') !== FALSE) {
                            $body = str_replace('[amount]', $one_link_transaction->transaction_amount, $body);
                        }
                        if (strpos($body, '[tracking_number]') !== FALSE) {
                            $body = str_replace('[tracking_number]', $one_link_transaction->tracking_number, $body);
                        }
                        if (strpos($body, '[rider]') !== FALSE) {
                            $body = str_replace('[rider]', $rider->name, $body);
                        }
                        self::push_notification($employee_id, $employee_type, $title, $body);
                    }
                } else if ($id == 20) {
                    if ($employee_id) {
                        $admin_id = Admin::where('employee_id', $employee_id)->value('id');
                        if ($admin_id)
                            self::push_notification($admin_id, $employee_type, $title, $body);
                    }
                }

                //For Fintech
                else if ($id == 21) {
                    $fintech_transaction = FintechPaymentDetails::find($reference2_id);
                    $rider = Rider::find($reference1_id);
                    if ($fintech_transaction && $rider) {

                        //title
                        if (strpos($title, '[tracking_number]') !== FALSE) {
                            $title = str_replace('[tracking_number]', $fintech_transaction->tracking_id, $title);
                        }
                        if (strpos($title, '[amount]') !== FALSE) {
                            $title = str_replace('[amount]', $fintech_transaction->cod_amount, $title);
                        }

                        //body
                        if (strpos($body, '[amount]') !== FALSE) {
                            $body = str_replace('[amount]', $fintech_transaction->cod_amount, $body);
                        }
                        if (strpos($body, '[tracking_number]') !== FALSE) {
                            $body = str_replace('[tracking_number]', $fintech_transaction->tracking_id, $body);
                        }
                        if (strpos($body, '[tip]') !== FALSE) {
                            $body = str_replace('[tip]', $fintech_transaction->rider_tip, $body);
                        }
                        if (strpos($body, '[rider]') !== FALSE) {
                            $body = str_replace('[rider]', $rider->name, $body);
                        }
                        self::push_notification($employee_id, $employee_type, $title, $body);
                    }
                }
            }
        }
    }

}