<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\CRM\CrmRequestStatus;
use App\Http\Models\CRM\CrmRequestStatusHistory;
use App\Http\Models\CRM\CrmRequestTagging;
use App\Http\Models\CRM\CrmRequestTaggingHistory;
use App\Http\Models\SaleTierTag;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\CrmAutoTagUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CRMController extends Controller
{
    //launched_by = 0 => Admin
    //launched_by = 1 => Shipper
    //launched_by = 2 => Substitute Shipper

    static public function add($case_nature_id, $case_nature_type_id = NULL, $channel_id, $status_id = 1, $launched_by_id = NULL, $launched_by, $shipment_id = NULL, $shipper_id = NULL, $agent_id = NULL,$description = NULL, $product_cost = NULL, $product_picture = NULL, $invoice_picture = NULL, $damage_product_picture = NULL, $product_packaging_picture = NULL, $actual_product_picture = NULL, $damage_product_price = NULL, $missing_product_picture = NULL, $product_packaging_picture_for_content_short = NULL, $actual_product_picture_for_content_short = NULL, $missing_product_price = NULL){
        $crm_request = new CrmRequest();
        $crm_request->case_nature_id = $case_nature_id;
        $crm_request->case_nature_type_id = $case_nature_type_id;
        $crm_request->channel_id = $channel_id;
        $crm_request->status_id = $status_id;
        $crm_request->launched_by_id = $launched_by_id;
        $crm_request->launched_by = $launched_by;
        $crm_request->shipment_id = $shipment_id;
        $crm_request->shipper_id = $shipper_id;
        $crm_request->agent_id = $agent_id;
        $crm_request->description = $description;
        $crm_request->product_cost = $product_cost;
        $crm_request->damage_product_price = $damage_product_price;
        $crm_request->missing_product_price = $missing_product_price;
        $crm_request->save();
        if($product_picture != null){
            $filename = 'claim_product_' . $crm_request->id . '.png';
            $file = $product_picture;
            Storage::disk('public')->putFileAs('crm_claims', $file, $filename);
            $crm_request->product_picture = $filename;
        }
        else{
            $crm_request->product_picture = $product_picture;
        }
        if($invoice_picture != null){
            $filename = 'claim_invoice_' . $crm_request->id . '.png';
            $file = $invoice_picture;
            Storage::disk('public')->putFileAs('crm_claims', $file, $filename);
            $crm_request->invoice_picture = $filename;
        }
        else{
            $crm_request->invoice_picture = $invoice_picture;
        }
        if($damage_product_picture != null){
            $filename = 'claim_damage_product_' . $crm_request->id . '.png';
            $file = $damage_product_picture;
            Storage::disk('public')->putFileAs('crm_claims', $file, $filename);
            $crm_request->damage_product_picture = $filename;
        }
        else{
            $crm_request->damage_product_picture = $damage_product_picture;
        }
        if($product_packaging_picture != null){
            $filename = 'claim_product_packaging_' . $crm_request->id . '.png';
            $file = $product_packaging_picture;
            Storage::disk('public')->putFileAs('crm_claims', $file, $filename);
            $crm_request->product_packaging_picture = $filename;
        }
        else{
            $crm_request->product_packaging_picture = $product_packaging_picture;
        }
        if($actual_product_picture != null){
            $filename = 'claim_actual_product_' . $crm_request->id . '.png';
            $file = $actual_product_picture;
            Storage::disk('public')->putFileAs('crm_claims', $file, $filename);
            $crm_request->actual_product_picture = $filename;
        }
        else{
            $crm_request->actual_product_picture = $actual_product_picture;
        }
        if($missing_product_picture != null){
            $filename = 'claim_missing_product_' . $crm_request->id . '.png';
            $file = $missing_product_picture;
            Storage::disk('public')->putFileAs('crm_claims', $file, $filename);
            $crm_request->missing_product_picture = $filename;
        }
        else{
            $crm_request->missing_product_picture = $missing_product_picture;
        }
        if($product_packaging_picture_for_content_short != null){
            $filename = 'claim_product_content_short_' . $crm_request->id . '.png';
            $file = $product_packaging_picture_for_content_short;
            Storage::disk('public')->putFileAs('crm_claims', $file, $filename);
            $crm_request->product_packaging_picture_for_content_short = $filename;
        }
        else{
            $crm_request->product_packaging_picture_for_content_short = $product_packaging_picture_for_content_short;
        }
        if($actual_product_picture_for_content_short != null){
            $filename = 'claim_actual_content_short_' . $crm_request->id . '.png';
            $file = $actual_product_picture_for_content_short;
            Storage::disk('public')->putFileAs('crm_claims', $file, $filename);
            $crm_request->actual_product_picture_for_content_short = $filename;
        }
        else{
            $crm_request->actual_product_picture_for_content_short = $actual_product_picture_for_content_short;
        }
        $crm_request->save();


        $id = $crm_request->id;

        $crm_request_status_history = new CrmRequestStatusHistory();

        $crm_request_status_history->crm_request_id = $id;
//        $crm_request_status_history->agent_id = $launched_by_id;
        $crm_request_status_history->status_id = 1;

        $crm_request_status_history->save();
        NotificationsController::send(31, $id);
        NotificationsController::send(142, $id);

        if($case_nature_type_id != null){
            if(in_array($case_nature_type_id, [11, 12, 13])){
                $crm_request->status_id = 2;
                $crm_request->save();
                NotificationsController::send(41, $id);

                $crm_request_status_history = new CrmRequestStatusHistory();

                $crm_request_status_history->crm_request_id = $id;
                $crm_request_status_history->status_id = 2;

                $crm_request_status_history->save();


                if($crm_request->case_nature_type_id == 3 || $crm_request->case_nature_type_id == 5){
                    $crm_city_id = $crm_request->shipment->pickup_address->city->id;

                }else{
                    $crm_city_id = $crm_request->shipment->consignee_city_id;
                }

                $crm_auto_tag_user = CrmAutoTagUser::where('city_id',$crm_city_id)->where('status',1);
                if($crm_auto_tag_user->exists()){

                    $crm_auto_tag_user = $crm_auto_tag_user->get()->first();
                    $tagged_crm_request = CrmRequestTagging::where('crm_request_id', $crm_request->id)->first();
                    if(!empty($tagged_crm_request)){
                        if($tagged_crm_request['tagged_id'] != $crm_auto_tag_user->admin_id) {
                            CrmRequestTagging::where('crm_request_id', $crm_request->id)->update([
                                'crm_request_tagging_type_id' => 5,
                                'tagged_id' => $crm_auto_tag_user->admin_id
                            ]);

                            CrmRequestTaggingHistory::create([
                                'crm_request_id' => $crm_request->id,
                                'crm_request_tagging_type_id' => 5,
                                'tagged_id' => $crm_auto_tag_user->admin_id,
                                'agent_id' => 306,
                                'hub_id' => NULL
                            ]);
                            NotificationsController::send(31,$crm_request->id);
                        }
                    }
                    else{
                        CrmRequestTagging::create([
                            'crm_request_id' => $crm_request->id,
                            'crm_request_tagging_type_id' => 5,
                            'tagged_id' => $crm_auto_tag_user->admin_id,
                            'hub_id' => NULL
                        ]);

                        CrmRequestTaggingHistory::create([
                            'crm_request_id' => $crm_request->id,
                            'crm_request_tagging_type_id' => 5,
                            'tagged_id' => $crm_auto_tag_user->admin_id,
                            'agent_id' => 306,
                            'hub_id' => NULL
                        ]);
                        NotificationsController::send(31,$crm_request->id);
                    }
                }
            }
        }

        if($case_nature_id == 1 && ($launched_by == 1 || $launched_by == 2)){
            $settings = GlobalSettings::where('type', 'auto_crm_comment');
            if($settings->exists()){
                $settings = $settings->first();
                $comment = $settings->text;
                $comment_by = 0;
                $comment_type = 0;

                $default_agent_setting = GlobalSettings::where('type', 'crm_default_agent');
                if($default_agent_setting->exists()){
                    $default_agent_setting = $default_agent_setting->first();
                    $default_agent_id = $default_agent_setting->setting_value;
                }
                else{
                    $default_agent_id = 306;
                }
                CRMCommentController::add($id, $default_agent_id,$comment_by,$comment_type, $comment,1);
            }
        }

        if ($case_nature_id == 4) {
            $comment = "Dear concern, Please note your case is under scrutiny in the claims department, the circle of claim-resolution is 15 working days, if there’s any update about this CN/CLAIM, our claims team will get in touch with you by means of email, CRM or call and update you at their earliest.";
            $comment_by = 0;
            $comment_type = 0;

            $default_agent_setting = GlobalSettings::where('type', 'crm_default_agent');
            if ($default_agent_setting->exists()) {
                $default_agent_setting = $default_agent_setting->first();
                $default_agent_id = $default_agent_setting->setting_value;
            } else {
                $default_agent_id = 306;
            }
            CRMCommentController::add($id, $default_agent_id, $comment_by, $comment_type, $comment,1);
        }
        return $crm_request->id;
    }
}
