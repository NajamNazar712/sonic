<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Lead\Lead;
use App\Http\Models\Admin\Lead\LeadNotification;
use App\Http\Models\Admin\Lead\LeadNotificationAttachment;
use App\Http\Models\Admin\Lead\LeadTagging;
use App\Http\Models\Admin\Lead\LeadTaggingHistory;
use App\Http\Models\SMS;
use Carbon\Carbon;
use App\Mail\Notifications;
use Illuminate\Support\Facades\Mail;
use App\Jobs\ProcessSMS;
use App\Jobs\ProcessOTPSMS;


class LeadTaggingController extends Controller
{
    

    static public function auto_tagging($lead_id,$admin_id){
        $lead = Lead::find($lead_id);
        $sales_person = LeadTagging::where('city_id',$lead->city_id)->where('service_id',$lead->service_id)->where('status',1)->orderBy('count','asc')->get()->first();
        $lead->sale_person_id = $sales_person->sale_person_id;
        $lead->updated_by = $admin_id;
        $lead->sale_person_updated_at = Carbon::now();
        $lead->save();
        $sales_person->count += 1;
        $sales_person->save();

        LeadTaggingHistory::create([
            'sale_person_id' => $sales_person->id,
            'lead_id' => $lead->id,
            'status' => 1,
        ]);

        $lead_notification_email = LeadNotification::find(1);
        $lead_notification_sms = LeadNotification::find(2);

        $subject = $lead_notification_email->subject;
        $body = $lead_notification_email->body;
        $to = $lead->email_address;
        $body_attachment_message= PHP_EOL . 'Please Find the Attachment from the following Link(s).'. PHP_EOL;
        $fields = ['shipper_name' => $lead->contact_person, 'tagged_salesperson_name' => $lead->sales_person->name, 'tagged_salesperson_number' => $lead->sales_person->phone_number, 'tagged_salesperson_email' => $lead->sales_person->email];
       
        $sms_body = $lead_notification_sms->body;

        foreach ($fields as $key => $field) {
            if (strpos($subject, '[' . $key . ']') !== FALSE) {
                $subject = str_replace('[' . $key . ']', $field, $subject);
            }

            if (strpos($body, '[' . $key . ']') !== FALSE) {
                $body = str_replace('[' . $key . ']', $field, $body);
                $sms_body = str_replace('[' . $key . ']', $field, $sms_body);
            }
        }
        
        $lead_attachments = LeadNotificationAttachment::where('notification_id',1)->get();
        foreach ($lead_attachments as $key => $attachment){
                        $key = $key + 1;
                        $link = '<a href="' . $attachment . '" target="_blank"><u> Attachment '.$key.'</u></a>';
                        $body_attachment_message = $body_attachment_message . $link . PHP_EOL;
        }
                    // $body_attachment_message =  $body_attachment_message. PHP_EOL . 'NOTE: the attachments will be removed after 7 days(s)';
                    $body = $body . PHP_EOL . $body_attachment_message;
        self::email($subject, $body, $to);



        $sms_to = $lead->phone_number;
        
        self::sms($body, $to);

        
    }

    static private function email($subject, $body, $to, $cc = NULL, $bcc = NULL, $from = NULL) {
        if($to){
            if(is_array($to)){
                $to = array_values(array_filter($to));
                if(empty($to)){
                    return false;
                }

                if(is_array($cc)){
                    $cc = array_values(array_filter($cc));
                    if(empty($cc)){
                        $cc = NULL;
                    }
                }
                if(is_array($bcc)){
                    $bcc = array_values(array_filter($bcc));
                    if(empty($bcc)){
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

    static private function sms($body, $to) {
        $sms = new SMS();
        $sms->to = str_replace('-', '', $to);
        $sms->body = $body;
        $sms->save();
  
        dispatch(new ProcessSMS($sms));
      }


}
