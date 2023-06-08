<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\Lead\Lead;
use App\Http\Models\Admin\Lead\LeadNotification;
use App\Http\Models\Admin\Lead\LeadNotificationAttachment;
use App\Http\Models\Admin\Lead\LeadTagging;
use App\Http\Models\Admin\Lead\LeadTaggingHistory;
use App\Http\Models\City;
use App\Http\Models\SMS;
use App\Jobs\ProcessSMS;
use App\Mail\Notifications;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class LeadTaggingController extends Controller
{

    public static function auto_tagging($lead_id, $admin_id)
    {
        $lead = Lead::find($lead_id);
        $zone = City::find($lead->city_id);
        //updated
        $sales_person = LeadTagging::leftjoin('lead_tagging_services as lts','lts.lead_tagging_id','=','lead_taggings.id')
                        ->where('lead_taggings.status',1)
                        ->select('lead_taggings.id','lead_taggings.sale_person_id','lead_taggings.zone_id','lead_taggings.city_id','lead_taggings.count','lead_taggings.territory_id','lead_taggings.zone_id','lts.service_id')
                        ->orderBy('lead_taggings.count', 'asc')
                        ->get();
        $sales_person_tagging_id = 0;
        $sales_person_tagging_lead_id = 0;
    
        foreach ($sales_person as $value) {
            
            if($lead->city_id == $value->city_id && $lead->territory_id == $value->territory_id && $lead->service_id == $value->service_id && $zone->zone_id == $value->zone_id){
                $sales_person_tagging_id = $value->sale_person_id;
                $sales_person_tagging_lead_id = $value->id;
                break;
            }elseif($lead->city_id == $value->city_id && $value->territory_id == 0 && $lead->service_id == $value->service_id && $zone->zone_id == $value->zone_id){
                $sales_person_tagging_id = $value->sale_person_id;
                $sales_person_tagging_lead_id = $value->id;
                break;
            }elseif($value->city_id == 0 && $value->territory_id == 0 && $lead->service_id == $value->service_id && $zone->zone_id == $value->zone_id){
                
                $sales_person_tagging_id = $value->sale_person_id;
                $sales_person_tagging_lead_id = $value->id;
                break;
            }elseif($value->city_id == 0 && $value->territory_id == 0 && $lead->service_id == $value->service_id && $value->zone_id == 0){
                $sales_person_tagging_id = $value->sale_person_id;
                $sales_person_tagging_lead_id = $value->id;
                break;
            }

        }
        
        //updated end
        
        if($sales_person_tagging_id != 0){
            
            $lead->sale_person_id = $sales_person_tagging_id;
            $lead->updated_by = $admin_id;
            $lead->sale_person_updated_at = Carbon::now();
            $lead->save();
            $update_count = LeadTagging::find($sales_person_tagging_lead_id);
            $update_count->count += 1;
            $update_count->save();
            LeadTaggingHistory::create([
                'sale_person_id' => $sales_person_tagging_id,
                'lead_id' => $lead->id,
                'status' => 1,
            ]);

            $lead_notification_email = LeadNotification::find(1);
            $lead_notification_sms = LeadNotification::find(2);

            $subject = $lead_notification_email->subject;
            $body = $lead_notification_email->body;
            $to = $lead->email_address;
            $body_attachment_message = PHP_EOL . 'Please Find the Attachment from the following Link(s).' . PHP_EOL;
            $fields = ['shipper_name' => $lead->contact_person, 'tagged_salesperson_name' => $lead->sales_person->name, 'tagged_salesperson_number' => ($lead->sales_person->official_phone_number != null)? $lead->sales_person->official_phone_number: $lead->sales_person->phone_number, 'tagged_salesperson_email' => $lead->sales_person->email];

            $sms_body = $lead_notification_sms->body;

            foreach ($fields as $key => $field) {
                if (strpos($subject, '[' . $key . ']') !== false) {
                    $subject = str_replace('[' . $key . ']', $field, $subject);
                }

                if (strpos($body, '[' . $key . ']') !== false) {
                    $body = str_replace('[' . $key . ']', $field, $body);
                    $sms_body = str_replace('[' . $key . ']', $field, $sms_body);
                }
            }

            $lead_attachments = LeadNotificationAttachment::where('notification_id', 1);
            if($lead_attachments->exists()){
                $lead_attachments = $lead_attachments->get();
            }
            foreach ($lead_attachments as $key => $attachment) {
                $key = $key + 1;
                $url = asset('uploads/notification_attachments/'.$attachment->attachment);
                $link = '<a href="' . $url . '" target="_blank"><u> Attachment ' . $key . '</u></a>';
                $body_attachment_message = $body_attachment_message . $link . PHP_EOL;
            }
            $body = $body . PHP_EOL . $body_attachment_message;
            if($lead_notification_email->status){

                self::email($subject, $body, $to);
            }
            $sms_to = $lead->phone_number;
            if($lead_notification_sms->status){

                self::sms($sms_body, $sms_to);
            }


            NotificationsController::app_notification(14, $lead->sale_person_id, 1, $lead->id);
        }
        

    }

    private static function email($subject, $body, $to, $cc = null, $bcc = null, $from = null)
    {
        if ($to) {
            if (is_array($to)) {
                $to = array_values(array_filter($to));
                if (empty($to)) {
                    return false;
                }

                if (is_array($cc)) {
                    $cc = array_values(array_filter($cc));
                    if (empty($cc)) {
                        $cc = null;
                    }
                }
                if (is_array($bcc)) {
                    $bcc = array_values(array_filter($bcc));
                    if (empty($bcc)) {
                        $bcc = null;
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

    private static function sms($body, $to)
    {
        $sms = new SMS();
        $sms->to = str_replace('-', '', $to);
        $sms->body = $body;
        $sms->save();

        dispatch(new ProcessSMS($sms));
    }

    public static function notification_unresponsive($lead_id)
    {
        $lead = Lead::find($lead_id);
        $lead_notification_email = LeadNotification::find(3);
        $lead_notification_sms = LeadNotification::find(4);

        $subject = $lead_notification_email->subject;
        $body = $lead_notification_email->body;
        $to = $lead->email_address;

        $fields = ['shipper_name' => $lead->contact_person, 'tagged_salesperson_name' => $lead->sales_person->name, 'tagged_salesperson_number' => $lead->sales_person->official_phone_number, 'tagged_salesperson_email' => $lead->sales_person->email];

        $sms_body = $lead_notification_sms->body;

        foreach ($fields as $key => $field) {
            if (strpos($subject, '[' . $key . ']') !== false) {
                $subject = str_replace('[' . $key . ']', $field, $subject);
            }

            if (strpos($body, '[' . $key . ']') !== false) {
                $body = str_replace('[' . $key . ']', $field, $body);
            }
            if (strpos($sms_body, '[' . $key . ']') !== false) {
                $sms_body = str_replace('[' . $key . ']', $field, $sms_body);
            }
        }
        if($lead_notification_email->status){
            self::email($subject, $body, $to);
        }

        $sms_to = $lead->phone_number;
        if($lead_notification_sms->status){
            self::sms($sms_body, $sms_to);
        }


    }
}
