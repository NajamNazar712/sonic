<?php

namespace App\Http\Controllers;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\BookingType;
use App\Http\Models\CashHandlingCharge;
use App\Http\Models\City;
use App\Http\Models\CorporateCashHandlingCharge;
use App\Http\Models\CorporateDefaultCashHandlingCharge;
use App\Http\Models\CorporateDefaultFuelSurcharge;
use App\Http\Models\CorporateDefaultInsuranceCharge;
use App\Http\Models\CorporateDefaultRateStatus;
use App\Http\Models\CorporateDefaultReturnCharge;
use App\Http\Models\CorporateDefaultWeightCharge;
use App\Http\Models\CorporateDeliveryTypeStatus;
use App\Http\Models\CorporateFuelSurcharge;
use App\Http\Models\CorporateInsuranceCharge;
use App\Http\Models\CorporateMinChargeableWeight;
use App\Http\Models\CorporateRateStatus;
use App\Http\Models\CorporateReturnCharge;
use App\Http\Models\CorporateReturnChargeZoneWise;
use App\Http\Models\CorporateWeightCharge;
use App\Http\Models\CorporateWeightChargeZoneWise;
use App\Http\Models\CRFTermsConditions;
use App\Http\Models\DwsWeightCharges;
use App\Http\Models\FuelSurcharge;
use App\Http\Models\InsuranceCharge;
use App\Http\Models\InternationalRatesCashHandlingCharges;
use App\Http\Models\InternationalRatesHub;
use App\Http\Models\InternationalRatesInsuranceCharges;
use App\Http\Models\InternationalRatesReturnCharges;
use App\Http\Models\InternationalRatesStatus;
use App\Http\Models\InternationalRatesWeightCharges;
use App\Http\Models\InternationalStandardDhlRate;
use App\Http\Models\InternationalUserRate;
use App\Http\Models\PackagingCharge;
use App\Http\Models\Rates\Corporate\CorporateDefaultRateDestinationHub;
use App\Http\Models\Rates\Corporate\CorporateDefaultRateOriginHub;
use App\Http\Models\Rates\Corporate\CorporateRateDestinationHub;
use App\Http\Models\Rates\Corporate\CorporateRateOriginHub;
use App\Http\Models\Rates\InternationalEconomyRate;
use App\Http\Models\Rates\InternationalEconomyRateStatus;
use App\Http\Models\Rates\RateDestinationHub;
use App\Http\Models\Rates\RateOriginHub;
use App\Http\Models\RateStatus;
use App\Http\Models\ReturnCharge;
use App\Http\Models\Shipper\User;
use App\Http\Models\ShippingMode;
use App\Http\Models\WeightCharge;
use App\Http\Models\Zone;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Http\Request;

class ShipperAgreementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin')->only(['view_crf_agreement']);

        $this->middleware('Permission');
    }

    public static function view_crf_agreement($id, $file_type = null,$for_shipper_agreement_modal = false){
        $shipper_id = $id;

        if(!$for_shipper_agreement_modal) {
            $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Customer Registration Form</title>

                    <style>
                      @page {
                        size: A4 portrait;
                      }

                      * {
                        -webkit-print-color-adjust: exact !important;
                        color-adjust: exact !important;
                      }

                      body {
                        background: none !important;
                        color: #09262e !important;
                        font-size: 0.9rem !important;
                        font-family: "Open Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                      }

                      hr {
                        border-top: 1px dashed #000000;
                      }
                      h4{
                      font-family: "Open Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                      }
                      table.table-bordered {
                        page-break-inside: avoid;
                      }

/*                      table.table-bordered tbody tr td {
                        border: 1px solid #09262e !important;
                      }*/

                      .color.primary {
                        background: #c8c8c8 !important;
                      }

                      .color.secondary {
                        background: #ebebeb !important;
                      }

                      .border {
                        border: 1px solid #09262e !important;
                      }
                      .double-border{
                        border: 3px solid #000000 !important;
                       }
                       .double-border-bottom{
                        border-bottom: 3px solid #000000 !important;
                       }
                      td.replacement span {
                        width: 22px;
                      }
                      .table td, .table th {
                        border-top: none;
                      }  
                      td.replacement span img {
                        display: block;
                        width: 100%;
                        margin: auto;
                        background: #c8c8c8;
                        border-radius: 25px;
                      }
                      
                      td.complaint {
                            background: #09262e !important;
                            color: #ffffff;
                       }
                       .new-page{page-break-before:always}
                       /*.terms_conditions{page-break-before:always}*/
                    </style>
                  </head>
                  <body>
                    <div>
                    <div class="double-border">
      ';
        }
        else{
            $html =  '<div>
                    <div class="double-border">';
        }
        $shipper = User::find($id);
        if(!$shipper){
            return redirect(route('cod.404'));
        }
        $poc_name = '';
        $poc_name = $shipper->poc;
        $shipper_bank = $shipper->bank()->where('default_bank', 1)->first();
        
        $sales_person_name = '';
        $sales_person = $shipper->sales_person()->where('status', 0)->first();
        if($sales_person){
            $sales_person_name = Admin::find($sales_person->admin_id)->name ?? "";
        }
        $claim_policy = '<h2 class="text-center mt-4">Claim Policy</h2>
<style> .table1 tr:nth-child(even) {background-color: #d9e2f3;}</style>
<table class="table table-bordered table1 p-4" >
                                    <thead style="/*background-color: #4472c4; color: white;*/font-weight: bold;">
                                        <tr>
                                            <td>S.no</td>
                                            <td>Claim Type</td>
                                            <td>Pre-Requisition</td>
                                            <td>Cut Off</td>
                                            <td>Action</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>Damage + Short Content
                                                    on Delivery (Consignee).</td>
                                            <td>Within 24
                                                Hours</td>
                                            <td>Product details + Marketing
                                                    picture + Damage parcel picture
                                                    by consignee + Invoice.</td>
                                            <td>For all the damage & short
                                                content at delivery, shipper
                                                must inform through claim form
                                                within given cut-off time</td>        
                                        </tr>
                                        <tr>
                                            <td>2-a</td>
                                            <td>Lost - Booked not
                                                  arrived.</td>
                                            <td>4 Days of
                                            Booking</td>
                                            <td>Scanned signed receiving sheet+
                                                invoice</td>
                                            <td>For all the shipments booked
                                                but not arrived at Trax, shipper
                                                must inform within given cut-off
                                                time</td>    
                                        </tr>
                                        <tr>
                                            <td>2-b</td>
                                            <td>Lost – After arrival
                                                before delivery.</td>
                                            <td>45 Days</td>
                                            <td>Product details + Marketing
                                            picture + Invoice</td>
                                            <td>For all the shipments arrived at
                                                Trax office but not delivered or
                                                returned, shipper must inform
                                                within given cut-off time</td>
                                        </tr>
                                        <tr>
                                            <td>2-c</td>
                                            <td>Lost –
                                            Returned/Charges
                                            Deducted.</td>
                                            <td>3 Days</td>
                                            <td>Product Details + Marketing
                                                picture + Invoice</td>
                                                <td>For all the return shipments,
                                                which are not received
                                                physically to shipper.</td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>Theft & Snatching</td>
                                            <td>2 Days Notice</td>
                                            <td>Product Details</td>
                                                <td>No Claim</td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td>Weight Dispute</td>
                                            <td>3 Days of Arrival</td>
                                            <td>Product details + Picture +
                                                Actual weight of product + or
                                                Alternate CN# of similar
                                                product.</td>
                                                <td>Claim amount disburse upon
                                                calculation & Shipper can claim
                                                it within given cut-off time.</td>
                                        </tr>
                                         <tr>
                                            <td>5</td>
                                            <td>Tariff</td>
                                            <td>2 Days of Invoice</td>
                                            <td>Proposal copy + Calculation Error</td>
                                                <td>Claim amount disburse upon
                                                    calculation & Shipper can claim
                                                    it within given cut-off time</td>
                                        </tr>
                                         <tr>
                                            <td>6</td>
                                            <td>Damage + Content Short
                                            Return (Shipper).</td>
                                            <td>24 Hours of Return.</td>
                                            <td>Product details + Picture + Invoice</td>
                                                <td>Shipper can claim any damage
                                                    short content in return
                                                    shipment within given cut-off
                                                    time.</td>
                                        </tr>
</tbody>
</table>

<table class="table table-bordered " >
    <tbody>
        <tr>
            <td>Flyer/Packing un intact while delivery claim
            amount will be Up to 80% of product value.</td>
            <td>Flyer/Packing burnt + damage claim amount
            will be up to 80% of invoice.</td>
        </tr>
        <tr>
            <td>Flyer/Packing un intact and mishandled at
            time of return, shipper needs write broken
            received on return receiving document -
            claim amount will be up to 50%.</td>
            <td>Packing intact and no mishandling no amount
            disburse. </td>
        </tr>
        </tbody>
</table>

<ul>Note:
<li>If Cod amount is 0-25,000/- claim would be up to 80% of product cost</li>
<li>If COD amount is 25000+ Trax will not entertain any claim</li>
<li>Shipper using Trax flyers or approved packaging only then the claim will be entertained
otherwise it will be rejected</li>
<li>No claim will be entertained in case of theft and snatching</li>
<li>Total claim processing time is 15 working days</li>

</ul>
';

        $page = '<table class="table table-sm table-borderless mb-0">
                        <tbody>
                          <tr class="double-border-bottom">
                            <td colspan="4" class="text-center align-middle"><img src="' . asset('img/trax_logo_new.png') . '" width="150" class="d-block mx-auto"></td>
                          </tr>
                          <tr class="double-border-bottom">
                            <td colspan="4" class="text-center align-middle"><strong><b>Customer Registration Form</b></strong></td>
                          </tr>
                          <tr class="double-border-bottom">
                            <td colspan="4" class="text-center align-middle"><p></p></td>
                          </tr>
                          <tr class="">
                                <td colspan="1" class=""><strong>Company</strong></td>
                                <td colspan="1">' . $shipper->name . '</td>
                          
                                <td colspan="1"><strong>City</strong></td>
                                <td colspan="1">' . $shipper->city->name . '</td>
                           </tr>     
                          <tr>
                                <td colspan="1"><strong>E-mail Address</strong></td>
                                <td colspan="1">' . $shipper->email . '</td>
                                
                                <td colspan="1"><strong>Nature Of Account</strong></td>
                                <td colspan="1">' . $shipper->account_type->name . '</td>
                          </tr>      
                          <tr>      
                                <td colspan="1"><strong>Address</strong></td>
                                <td colspan="1">' . $shipper->address . '</td>
                                
                                <td colspan="1"><strong>Bank Name</strong></td>
                                <td colspan="1">' . $shipper_bank->bank->name . '</td>
                          </tr>
                          <tr>      
                                <td colspan="1"><strong>Person Of Contact</strong></td>
                                
                                <td colspan="1">' . $poc_name . '</td>
                                
                                <td colspan="1"><strong>Account No.</strong></td>
                                <td colspan="1">' . $shipper_bank->account_no . '</td>
                          </tr>
                          <tr>      
                                <td colspan="1"><strong>Phone Number 1</strong></td>
                                
                                <td colspan="1">' . $shipper->phone . '</td>
                                
                                <td colspan="1"><strong>Account Title</strong></td>
                                <td colspan="1">' . $shipper_bank->account_title . '</td>
                          </tr>
                          <tr>      
                                <td colspan="1"><strong>Phone Number 2</strong></td>
                                
                                <td colspan="1">' . $shipper->phone2 . '</td>
                                
                                <td colspan="1"><strong>IBAN Number</strong></td>
                                <td colspan="1">' . $shipper_bank->iban . '</td>
                          </tr>
                          <tr>      
                                <td colspan="1"><strong>CNIC Number</strong></td>
                                
                                <td colspan="1">' . $shipper->cnic . '</td>
                                
                                <td colspan="1"><strong>Payment Cycle</strong></td>
                                <td colspan="1">' . ucfirst($shipper->payment_cycle->name) . '</td>
                          </tr>
                          <tr>      
                                <td colspan="1"><strong>NTN Number</strong></td>
                                
                                <td colspan="1">' . $shipper->ntn_no . '</td>
                                
                                <td colspan="1"><strong>Sales Person</strong></td>
                                <td colspan="1">' . $sales_person_name . '</td>
                          </tr>
                          <tr>      
                                <td colspan="1"><strong>URL</strong></td>
                                
                                <td colspan="1">' . $shipper->url . '</td>
                          </tr>
                          <tr>      
                                <td colspan="1"><strong>Product Type</strong></td>
                                
                                <td colspan="1">' . $shipper->products->product_name . '</td>
                          </tr>
                          <tr>      
                                <td colspan="1"><strong>Expected Average Shipments</strong></td>
                                
                                <td colspan="1">' . $shipper->average_shipments . '</td>
                          </tr>
                          <tr class="double-border-bottom">
                          <td colspan="4"></td></tr>
                          
                        </tbody>
                      </table>';
        $html .= $page;

            $international_rates = FALSE;
            $rate_status = FALSE;
            if(InternationalEconomyRateStatus::where([['user_id', $id],['status',2]])->exists())
            {
                $international_rates = TRUE;
            }
            else if(InternationalUserRate::where('user_id', $id)->exists()){
                $international_rates = TRUE;
            }

            if($shipper->account_type_id == 1){
                if(RateStatus::where('user_id', $id)->exists()){
                    $rate_status = TRUE;
                }
            }
            else{
                if($shipper->corporate_rate_type_id == 3){
                    if(CorporateDefaultRateStatus::where('user_id', $id)->exists()){
                        $rate_status = TRUE;
                    }
                }
                else{
                    if(CorporateRateStatus::where('user_id', $id)->exists()){
                        $rate_status = TRUE;
                    }
                }

            }


            if($rate_status){
                $corporate_rate_type = 1;
                if($shipper->corporate_rate_type_id == 1 || $shipper->corporate_rate_type_id == null){
                    $corporate_rate_type = 1;
                }
                else if($shipper->corporate_rate_type_id == 2 ){
                    $corporate_rate_type = 2;
                }
                else{
                    $corporate_rate_type = 3;
                }
               /* $packaging_details = '';
                $packaging_charges = PackagingCharge::where('user_id', $id)->get();
                if($packaging_charges){
                    $packaging_details .= '<table class="table table-sm table-bordered mb-0">
                                <tbody>';


                    $ptype = '';
                    $packaging_details .= '<tr class="color secondary"><td colspan="5"><strong>Packaging Materials</strong></td></tr>';

                    foreach ($packaging_charges as $type){
                        if($ptype != $type->type_id){
                            $packaging_details .= '<tr class="color primary"><td colspan="5"><strong>' . $type->packaging_type->type . '</strong></td></tr>';
                            $ptype = $type->type_id;
                            $packaging_details .= '<tr><th class="color secondary">Size</th><th class="color secondary">Charges</th></tr>';
                        }
                        $packaging_details .= '<tr><td colspan="1">' . $type->packaging_size->size . '</td>';
                        $packaging_details .= '<td colspan="1">' . $type->charges . '</td></tr>';
                    }
                    $packaging_details .=  '</tbody>
                              </table>';
                }*/


                if($shipper->account_type_id == 1){
                    $rates_switch = RateStatus::where('user_id', $id)->where('status', 1)->get();
                    $rate_origin_hubs = RateOriginHub::where('user_id', $id);
                    if($rate_origin_hubs->exists()){
                        $rate_origin_hubs = $rate_origin_hubs->get();
                    }
                    $rate_destination_hubs = RateDestinationHub::where('user_id', $id);
                    if($rate_destination_hubs->exists()){
                        $rate_destination_hubs = $rate_destination_hubs->get();
                    }

                }else if($shipper->account_type_id == 2){
                    if($corporate_rate_type == 3){
                        $rates_switch = CorporateDefaultRateStatus::where('user_id', $id)->where('status', 1)->get();
                        $rate_origin_hubs = CorporateDefaultRateOriginHub::where('user_id', $id);
                        if($rate_origin_hubs->exists()){
                            $rate_origin_hubs = $rate_origin_hubs->get();
                        }
                        $rate_destination_hubs = CorporateDefaultRateDestinationHub::where('user_id', $id);
                        if($rate_destination_hubs->exists()){
                            $rate_destination_hubs = $rate_destination_hubs->get();
                        }
                    }
                    else{
                        $rates_switch = CorporateRateStatus::where('user_id', $id)->where('status', 1)->get();
                        $rate_origin_hubs = CorporateRateOriginHub::where('user_id', $id);
                        if($rate_origin_hubs->exists()){
                            $rate_origin_hubs = $rate_origin_hubs->get();
                        }
                        $rate_destination_hubs = CorporateRateDestinationHub::where('user_id', $id);
                        if($rate_destination_hubs->exists()){
                            $rate_destination_hubs = $rate_destination_hubs->get();
                        }
                    }
                }

                $rate_details = '';

                $overnight_origins = [];
                $overland_origins = [];
                $detain_origins = [];
                $sameday_origins = [];

                if($rate_origin_hubs || count($rate_origin_hubs) > 0){
                    foreach($rate_origin_hubs as $index => $origin){

                        if($origin->shipping_mode_id == 1){

                            $overnight_origins[] = $origin->city_id;
                        }
                        else if($origin->shipping_mode_id == 2){

                            $overland_origins[] = $origin->city_id;
                        }
                        else if($origin->shipping_mode_id == 3){

                            $detain_origins[] = $origin->city_id;
                        }
                        else if($origin->shipping_mode_id == 4){

                            $sameday_origins[] = $origin->city_id;
                        }

                    }
                }
                $overnight_destinations = [];
                $overland_destinations = [];
                $detain_destinations = [];
                $sameday_destinations = [];
                if($rate_destination_hubs || count($rate_destination_hubs) > 0){
                    foreach($rate_destination_hubs as $index => $destination){

                        if($destination->shipping_mode_id == 1){

                            $overnight_destinations[] = $destination->city_id;

                        }
                        else if($destination->shipping_mode_id == 2){

                            $overland_destinations[] = $destination->city_id;

                        }
                        else if($destination->shipping_mode_id == 3){

                            $detain_destinations[] = $destination->city_id;

                        }
                        else if($destination->shipping_mode_id == 4){

                            $sameday_destinations[] = $destination->city_id;

                        }

                    }
                }

                foreach ($rates_switch as $rate){
                    $service_type_details = '';
                    $chargeable_weight_details = '';
                    $weight_charges_details = '';
                    $rate_origin_details = '';
                    $rate_destination_details = '';

                    $service_type = ShippingMode::find($rate->shipping_mode_id);
                    $service_type_details = '<div class="row"><div class="col-5"> <table class="table color secondary table-sm table-bordered mb-0 mt-0><thead class=" color secondary">
<tr>
<td><strong>Shipping Mode </strong></td>
<td>' . $service_type->mode . '</td>
</tr></thead></table></div></div>';
                    if($rate->shipping_mode_id == 1){
                        if(count($overnight_origins) > 0){
                            $rate_origin_details = '<div class="row"><div class="col-12"> <table class="table color secondary table-sm table-bordered mb-0 mt-0><thead class=" color secondary">
<tr>
<td><strong>Origin Cities </strong></td>';

                            $origin_names = City::whereIn('id',$overnight_origins)->select('name')->get();
                            $origin_hub_names = '';
                            foreach ($origin_names as $origin_name){
                                $origin_hub_names .= $origin_name->name . ', ';
                            }
                            $rate_origin_details .= '<td>' . $origin_hub_names . '</td>
</tr></thead></table></div></div>';
                        }

                        if(count($overnight_destinations) > 0){
                            $rate_destination_details = '<div class="row"><div class="col-12"> <table class="table color secondary table-sm table-bordered mb-0 mt-0><thead class=" color secondary">
<tr>
<td><strong>Destination Cities </strong></td>';

                            $destination_names = City::whereIn('id',$overnight_destinations)->select('name')->get();
                            $destination_hub_names = '';
                            foreach ($destination_names as $destination_name){
                                $destination_hub_names .= $destination_name->name . ', ';
                            }
                            $rate_destination_details .= '<td>' . $destination_hub_names . '</td>
</tr></thead></table></div></div>';
                        }
                    }
                    if($rate->shipping_mode_id == 2){
                        if(count($overland_origins) > 0){
                            $rate_origin_details = '<div class="row"><div class="col-5"> <table class="table color secondary table-sm table-bordered mb-0 mt-0><thead class=" color secondary">
<tr>
<td><strong>Origin Cities </strong></td>';

                            $origin_names = City::whereIn('id',$overland_origins)->select('name')->get();
                            $origin_hub_names = '';
                            foreach ($origin_names as $origin_name){
                                $origin_hub_names .= $origin_name->name . ', ';
                            }
                            $rate_origin_details .= '<td>' . $origin_hub_names . '</td>
</tr></thead></table></div></div>';
                        }

                        if(count($overland_destinations) > 0){
                            $rate_destination_details = '<div class="row"><div class="col-5"> <table class="table color secondary table-sm table-bordered mb-0 mt-0><thead class=" color secondary">
<tr>
<td><strong>Destination Cities </strong></td>';

                            $destination_names = City::whereIn('id',$overland_destinations)->select('name')->get();
                            $destination_hub_names = '';
                            foreach ($destination_names as $destination_name){
                                $destination_hub_names .= $destination_name->name . ', ';
                            }
                            $rate_destination_details .= '<td>' . $destination_hub_names . '</td>
</tr></thead></table></div></div>';
                        }
                    }
                    if($rate->shipping_mode_id == 3){
                        if(count($detain_origins) > 0){
                            $rate_origin_details = '<div class="row"><div class="col-5"> <table class="table color secondary table-sm table-bordered mb-0 mt-0><thead class=" color secondary">
<tr>
<td><strong>Origin Cities </strong></td>';

                            $origin_names = City::whereIn('id',$detain_origins)->select('name')->get();
                            $origin_hub_names = '';
                            foreach ($origin_names as $origin_name){
                                $origin_hub_names .= $origin_name->name . ', ';
                            }
                            $rate_origin_details .= '<td>' . $origin_hub_names . '</td>
</tr></thead></table></div></div>';
                        }

                        if(count($detain_destinations) > 0){
                            $rate_destination_details = '<div class="row"><div class="col-5"> <table class="table color secondary table-sm table-bordered mb-0 mt-0><thead class=" color secondary">
<tr>
<td><strong>Destination Cities </strong></td>';

                            $destination_names = City::whereIn('id',$detain_destinations)->select('name')->get();
                            $destination_hub_names = '';
                            foreach ($destination_names as $destination_name){
                                $destination_hub_names .= $destination_name->name . ', ';
                            }
                            $rate_destination_details .= '<td>' . $destination_hub_names . '</td>
</tr></thead></table></div></div>';
                        }
                    }
                    if($rate->shipping_mode_id == 4){
                        if(count($sameday_origins) > 0){
                            $rate_origin_details = '<div class="row"><div class="col-5"> <table class="table color secondary table-sm table-bordered mb-0 mt-0><thead class=" color secondary">
<tr>
<td><strong>Origin Cities </strong></td>';

                            $origin_names = City::whereIn('id',$sameday_origins)->select('name')->get();
                            $origin_hub_names = '';
                            foreach ($origin_names as $origin_name){
                                $origin_hub_names .= $origin_name->name . ', ';
                            }
                            $rate_origin_details .= '<td>' . $origin_hub_names . '</td>
</tr></thead></table></div></div>';
                        }

                        if(count($sameday_destinations) > 0){
                            $rate_destination_details = '<div class="row"><div class="col-5"> <table class="table color secondary table-sm table-bordered mb-0 mt-0><thead class=" color secondary">
<tr>
<td><strong>Destination Cities </strong></td>';

                            $destination_names = City::whereIn('id',$sameday_destinations)->select('name')->get();
                            $destination_hub_names = '';
                            foreach ($destination_names as $destination_name){
                                $destination_hub_names .= $destination_name->name . ', ';
                            }
                            $rate_destination_details .= '<td>' . $destination_hub_names . '</td>
</tr></thead></table></div></div>';
                        }
                    }



                    if($shipper->account_type_id == 1){
                        $weight_charges = WeightCharge::where('user_id', $id)->where('shipping_mode_id', $rate->shipping_mode_id)->get();
                    }else{
                        if($corporate_rate_type == 1){
                            $weight_charges = CorporateWeightCharge::where('user_id', $id)->where('shipping_mode_id', $rate->shipping_mode_id)->get();
                        }
                        else if($corporate_rate_type == 2){
                            $weight_charges = CorporateWeightChargeZoneWise::where('user_id', $id)->where('shipping_mode_id', $rate->shipping_mode_id)->get();
                        }
                        else{
                            $weight_charges = CorporateDefaultWeightCharge::where('user_id', $id)->where('shipping_mode_id', $rate->shipping_mode_id)->get();
                        }

                    }

                    if($weight_charges){
                        $weight_charges_details = '<div class="row"><div class="col-5"> <table class="table color secondary table-sm table-bordered mb-0 mt-0"><thead><tr><td><strong>Weight Charges</strong></thead></table></div></div>';
                        

                        if($shipper->account_type_id == 2 && $corporate_rate_type != 3 ){
                            if($corporate_rate_type != 3){
                                $chargeable_weight = CorporateMinChargeableWeight::where('user_id', $id)->where('shipping_mode_id', $rate->shipping_mode_id)->where('delivery_type_id', 1)->first();

                                $chargeable_weight_details = '<div class="row mb-0"><div class="col-5"><table class="table table-sm table-bordered mb-0 mt-0">
                            <tbody><tr><td class="color primary" ><strong>Delivery Type</strong></td><td>' . $chargeable_weight->delivery_type->delivery_type . '</td></tr><tr><td class="color primary"><strong>Charges</strong></td><td>' . $chargeable_weight->min_chargeable_weight . '</td></tr></tbody>
                          </table></div></div>';
                                $weight_charges_details .= $chargeable_weight_details;
                            }
                            if($rate->shipping_mode_id != 4){
                                if($corporate_rate_type == 1){
                                    $weight_charges_details .= '<table class="table table-sm table-bordered mt-0 mb-0"><thead><tr><th>Range Up</th><th>Range Down</th><th>Flat Charges/KG (Local)</th><th>Flat Charges/KG (National-Zone A)</th><th>Flat Charges/KG (National-Zone B)</th><th>Flat Charges/KG (National-Zone C)</th><th>Flat Charges/KG (National-Zone D)</th></tr></thead><tbody>';
                                }
                                else{
                                    $weight_charges_details .= '<table class="table table-sm table-bordered mt-0 mb-0"><thead><tr><th>Range Up</th><th>Range Down</th><th>Flat Charges/KG (Local)</th><th>Flat Charges/KG (Same-Zone)</th><th>Flat Charges/KG (Different-Zone)</th></tr></thead><tbody>';
                                }


                                foreach ($weight_charges as $weight_charge){
                                    if($weight_charge->delivery_type_id == 1){
                                        if($corporate_rate_type == 1){
                                            $weight_charges_details .= '<tr><td>' . $weight_charge->range_up . '</td><td>' . $weight_charge->range_down . '</td><td>' . $weight_charge->local_or_6hr . '</td><td>' . $weight_charge->national_charges_class_0 . '</td><td>' . $weight_charge->national_charges_class_1 . '</td><td>' . $weight_charge->national_charges_class_2 . '</td><td>' . $weight_charge->national_charges_class_3 . '</td></tr>';
                                        }
                                        else{
                                            $weight_charges_details .= '<tr><td>' . $weight_charge->range_up . '</td><td>' . $weight_charge->range_down . '</td><td>' . $weight_charge->local . '</td><td>' . $weight_charge->same_zone . '</td><td>' . $weight_charge->different_zone . '</td></tr>';
                                        }
                                    }
                                }

                                $corporate_delivery_type = CorporateDeliveryTypeStatus::where('shipping_mode_id', $rate->shipping_mode_id)->where('delivery_type_id', 2);
                                if($corporate_delivery_type->exists()){
                                    $weight_charges_details .= '</tbody></table>';
                                    $chargeable_weight = CorporateMinChargeableWeight::where('user_id', $id)->where('shipping_mode_id', $rate->shipping_mode_id)->where('delivery_type_id', 2)->first();
                                    if($chargeable_weight){
                                        $chargeable_weight_details = '<div class="row mb-0"><div class="col-5"><table class="table table-sm table-bordered mb-0">
                            <tbody><tr><td class="color primary" ><strong>Delivery Type</strong></td><td>' . $chargeable_weight->delivery_type->delivery_type . '</td></tr><tr><td class="color primary"><strong>Charges</strong></td><td>' . $chargeable_weight->min_chargeable_weight . '</td></tr></tbody>
                          </table></div></div>';
                                        $weight_charges_details .= $chargeable_weight_details;
                                    }
                                    if($corporate_rate_type == 1){
                                        $weight_charges_details .= '<table class="table table-sm table-bordered mb-0"><thead><tr><th>Range Up</th><th>Range Down</th><th>Flat Charges/KG (Local)</th><th>Flat Charges/KG (National-Zone A)</th><th>Flat Charges/KG (National-Zone B)</th><th>Flat Charges/KG (National-Zone C)</th><th>Flat Charges/KG (National-Zone D)</th></tr></thead><tbody>';
                                    }
                                    else{
                                        $weight_charges_details .= '<table class="table table-sm table-bordered mt-0 mb-0"><thead><tr><th>Range Up</th><th>Range Down</th><th>Flat Charges/KG (Local)</th><th>Flat Charges/KG (Same-Zone)</th><th>Flat Charges/KG (Different-Zone)</th></tr></thead><tbody>';
                                    }

                                    foreach ($weight_charges as $weight_charge){
                                        if($weight_charge->delivery_type_id == 2){
                                            if($corporate_rate_type == 1){
                                                $weight_charges_details .= '<tr><td>' . $weight_charge->range_up . '</td><td>' . $weight_charge->range_down . '</td><td>' . $weight_charge->local_or_6hr . '</td><td>' . $weight_charge->national_charges_class_0 . '</td><td>' . $weight_charge->national_charges_class_1 . '</td><td>' . $weight_charge->national_charges_class_2 . '</td><td>' . $weight_charge->national_charges_class_3 . '</td></tr>';
                                            }
                                            else{
                                                $weight_charges_details .= '<tr><td>' . $weight_charge->range_up . '</td><td>' . $weight_charge->range_down . '</td><td>' . $weight_charge->local . '</td><td>' . $weight_charge->same_zone . '</td><td>' . $weight_charge->different_zone . '</td></tr>';
                                            }
                                        }
                                    }
                                    $weight_charges_details .= '</tbody></table>';
                                }

                            }else{
                                $weight_charges_details .= '<table class="table table-sm table-bordered"><thead><tr><th>Range Up</th><th>Range Down</th><th>6hr Charges</th><th>Sameday Charges</th></tr></thead><tbody>';

                                foreach ($weight_charges as $weight_charge){
                                    if($weight_charge->delivery_type_id == 1){
                                        if($corporate_rate_type == 1){
                                            $weight_charges_details .= '<tr><td>' . $weight_charge->range_up . '</td><td>' . $weight_charge->range_down . '</td><td>' . $weight_charge->local_or_6hr . '</td><td>' . $weight_charge->national_charges_class_0 . '</td></tr>';
                                        }
                                        else{
                                            $weight_charges_details .= '<tr><td>' . $weight_charge->range_up . '</td><td>' . $weight_charge->range_down . '</td><td>' . $weight_charge->local . '</td><td>' . $weight_charge->same_zone . '</td></tr>';
                                        }

                                    }
                                }
                                $corporate_delivery_type = CorporateDeliveryTypeStatus::where('shipping_mode_id', $rate->shipping_mode_id)->where('delivery_type_id', 2);
                                if($corporate_delivery_type->exists()){
                                    $weight_charges_details .= '</tbody></table>';

                                    $chargeable_weight = CorporateMinChargeableWeight::where('user_id', $id)->where('shipping_mode_id', $rate->shipping_mode_id)->where('delivery_type_id', 2)->first();
                                    if($chargeable_weight){
                                        $chargeable_weight_details = '<div class="row mb-1"><div class="col-5"><table class="table table-sm table-bordered mb-0">
                            <tbody><tr><td class="color primary" ><strong>Delivery Type</strong></td><td>' . $chargeable_weight->delivery_type->delivery_type . '</td></tr><tr><td class="color primary"><strong>Charges</strong></td><td>' . $chargeable_weight->min_chargeable_weight . '</td></tr></tbody>
                          </table></div></div>';
                                        $weight_charges_details .= $chargeable_weight_details;
                                    }

                                    $weight_charges_details .= '<table class="table table-sm table-bordered"><thead><tr><th>Range Up</th><th>Range Down</th><th>6hr Charges</th><th>Sameday Charges</th></tr></thead><tbody>';
                                    foreach ($weight_charges as $weight_charge){
                                        if($weight_charge->delivery_type_id == 2){
                                            if($corporate_rate_type == 1){
                                                $weight_charges_details .= '<tr><td>' . $weight_charge->range_up . '</td><td>' . $weight_charge->range_down . '</td><td>' . $weight_charge->local_or_6hr . '</td><td>' . $weight_charge->national_charges_class_0 . '</td></tr>';
                                            }
                                            else{
                                                $weight_charges_details .= '<tr><td>' . $weight_charge->range_up . '</td><td>' . $weight_charge->range_down . '</td><td>' . $weight_charge->local . '</td><td>' . $weight_charge->same_zone . '</td></tr>';
                                            }
                                        }
                                    }
                                    $weight_charges_details .= '</tbody></table>';
                                }


                            }


                        }
                        if($shipper->account_type_id == 1 || $corporate_rate_type == 3) {
                            if($rate->shipping_mode_id != 4){
                                $weight_charges_details .= '<table class="table table-sm table-bordered mb-0"><thead><tr><th>Range Up</th><th>Range Down</th><th>Weight Addition</th><th>Local Charges</th><th>National Charges Class A</th><th>National Charges Class B</th><th>National Charges Class C</th><th>National Charges Class D</th></tr></thead><tbody>';

                                foreach ($weight_charges as $weight_charge) {
                                    if ($shipper->account_type_id == 1 || $corporate_rate_type == 3) {
                                        $weight_charges_details .= '<tr><td>' . $weight_charge->range_up . '</td><td>' . $weight_charge->range_down . '</td><td>' . $weight_charge->spkg . '</td><td>' . $weight_charge->local_or_6hr . '</td><td>' . $weight_charge->national_charges_class_0 . '</td><td>' . $weight_charge->national_charges_class_1 . '</td><td>' . $weight_charge->national_charges_class_2 . '</td><td>' . $weight_charge->national_charges_class_3 . '</td></tr>';
                                    }
                                }
                                $weight_charges_details .= '</tbody></table>';
                            }else{
                                $weight_charges_details .= '<table class="table table-sm table-bordered mb-0"><thead><tr><th>Range Up</th><th>Range Down</th><th>Weight Addition</th><th>6hr Charges</th><th>Sameday  Charges</th></tr></thead><tbody>';

                                foreach ($weight_charges as $weight_charge) {
                                    if ($shipper->account_type_id == 1) {
                                        $weight_charges_details .= '<tr><td>' . $weight_charge->range_up . '</td><td>' . $weight_charge->range_down . '</td><td>' . $weight_charge->spkg . '</td><td>' . $weight_charge->local_or_6hr . '</td><td>' . $weight_charge->national_charges_class_0 . '</td></tr>';
                                    }
                                }
                                $weight_charges_details .= '</tbody></table>';
                            }

                        }
                    }

                    $cash_handling_details = '';
                    if($shipper->account_type_id == 1){
                        $cash_handling = CashHandlingCharge::where('user_id', $id)->where('shipping_mode_id', $rate->shipping_mode_id)->get();
                    }else{
                        if($corporate_rate_type == 3){
                            $cash_handling = CorporateDefaultCashHandlingCharge::where('user_id', $id)->where('shipping_mode_id', $rate->shipping_mode_id)->get();
                        }
                        else{
                            $cash_handling = CorporateCashHandlingCharge::where('user_id', $id)->where('shipping_mode_id', $rate->shipping_mode_id)->get();
                        }
                    }
                    if(count($cash_handling) > 0){
                        $cash_handling_details = '<div class="row"><div class="col-6"><table class="table color secondary table-sm table-bordered mb-0 mt-0"><thead><tr><td><strong>Cash Handling Charges </strong></thead></table></div></div>';
                        $cash_handling_details .= '<div class="row"><div class="col-6"><table class="table table-sm table-bordered mb-0"><thead><tr><th>Range Up</th><th>Range Down</th><th>Charges</th></tr></thead><tbody>';
                        foreach ($cash_handling as $cash){
                            $cash_handling_details .= '<tr><td>' . $cash->range_up . '</td><td>' . $cash->range_down . '</td><td>' . $cash->charges . '</td></tr>';
                        }
                        $cash_handling_details .= '</tbody></table></div></div>';
                    }

                    $insurance_charges_details = '';
                    if($shipper->account_type_id == 1){
                        $insurance_charges = InsuranceCharge::where('user_id', $id)->where('shipping_mode_id', $rate->shipping_mode_id)->get();
                    }else{
                        if($corporate_rate_type == 3){
                            $insurance_charges = CorporateDefaultInsuranceCharge::where('user_id', $id)->where('shipping_mode_id', $rate->shipping_mode_id)->get();
                        }
                        else{
                            $insurance_charges = CorporateInsuranceCharge::where('user_id', $id)->where('shipping_mode_id', $rate->shipping_mode_id)->get();
                        }
                    }
                    if(count($insurance_charges) > 0){
                        $insurance_charges_details = '<div class="row"><div class="col-6"><table class="table color secondary table-sm table-bordered mb-0 mt-0"><thead><tr><td><strong>Insurance Charges </strong></thead></table></div></div>';
                        $insurance_charges_details .= '<div class="row"><div class="col-6"><table class="table table-sm table-bordered mb-0"><thead><tr><th>Range Up</th><th>Range Down</th><th>Charges</th></tr></thead><tbody>';
                        foreach ($insurance_charges as $insurance){
                            $insurance_charges_details .= '<tr><td>' . $insurance->range_up . '</td><td>' . $insurance->range_down . '</td><td>' . $insurance->charges . '</td></tr>';
                        }
                        $insurance_charges_details .= '</tbody></table></div></div>';
                    }

                    $fuel_surcharge_charges_details = '';
                    if($shipper->account_type_id == 1){
                        $fuel_surcharge = FuelSurcharge::where('user_id', $id)->where('shipping_mode_id', $rate->shipping_mode_id)->first();
                    }else{
                        if($corporate_rate_type == 3){
                            $fuel_surcharge = CorporateDefaultFuelSurcharge::where('user_id', $id)->where('shipping_mode_id', $rate->shipping_mode_id)->first();
                        }
                        else{
                            $fuel_surcharge = CorporateFuelSurcharge::where('user_id', $id)->where('shipping_mode_id', $rate->shipping_mode_id)->first();
                        }
                    }
                    if($fuel_surcharge){
                        $fuel_surcharge_charges_details = '<div class="row"><div class="col-5"> <table class="table color secondary table-sm table-bordered mb-0 mt-0"><thead><tr><td><strong>Fuel Surcharge </strong></thead></table></div></div>';
                        $fuel_surcharge_charges_details .= '<div class="row mb-0"><div class="col-5"><table class="table table-sm table-bordered mb-0">
                            <tbody><tr><td class="color primary" ><strong>Fuel Charges</strong></td><td>' . $fuel_surcharge->fuel_surcharge . '%</td></tr></tr></tbody>
                          </table></div></div>';
                    }

                    $return_charges_details = '';
                    if($shipper->account_type_id == 1){
                        $return_charges = ReturnCharge::where('user_id', $id)->where('shipping_mode_id', $rate->shipping_mode_id)->first();
                    }else{
                        if($corporate_rate_type == 1){
                            $return_charges = CorporateReturnCharge::where('user_id', $id)->where('shipping_mode_id', $rate->shipping_mode_id)->first();
                        }
                        else if($corporate_rate_type == 2){
                            $return_charges = CorporateReturnChargeZoneWise::where('user_id', $id)->where('shipping_mode_id', $rate->shipping_mode_id)->first();
                        }
                        else{
                            $return_charges = CorporateDefaultReturnCharge::where('user_id', $id)->where('shipping_mode_id', $rate->shipping_mode_id)->first();
                        }
                    }
                    if($return_charges){
                        $return_charges_details .= '<div class="row"><div class="col-6"><table class="table color secondary table-sm table-bordered mb-0 mt-0"><thead><tr><td><strong>Return Charges </strong></thead></table></div></div>';
                        if($rate->shipping_mode_id != 4){
                            if($corporate_rate_type == 1 || $corporate_rate_type == 3){
                                $return_charges_details .= '<div class="row"><div class="col-6"><table class="table table-sm table-bordered mb-0"><thead><tr><th>Local</th><th>Zone A</th><th>Zone B</th><th>Zone C</th><th>Zone D</th></tr></thead><tbody>';
                            }
                            else{
                                $return_charges_details .= '<div class="row"><div class="col-6"><table class="table table-sm table-bordered mb-0"><thead><tr><th>Local</th><th>Same Zone</th><th>Different Zone</th></tr></thead><tbody>';
                            }


                            if($corporate_rate_type == 1 ||  $corporate_rate_type == 3){
                                $return_charges_details .= '<tr><td>' . $return_charges->local . '</td><td>' . $return_charges->national_charges_class_0 . '</td><td>' . $return_charges->national_charges_class_1 . '</td><td>' . $return_charges->national_charges_class_2 . '</td><td>' . $return_charges->national_charges_class_3 . '</td></tr>';
                            }
                            else{
                                $return_charges_details .= '<tr><td>' . $return_charges->local . '</td><td>' . $return_charges->same_zone . '</td><td>' . $return_charges->different_zone . '</td></tr>';
                            }


                            $return_charges_details .= '</tbody></table></div></div>';


                        }else{
                            $return_charges_details .= '<div class="row"><div class="col-6"><table class="table table-sm table-bordered mb-0"><thead><tr><th>Local</th><th>National</th></tr></thead><tbody>';

                            if($corporate_rate_type == 1){
                                $return_charges_details .= '<tr><td>' . $return_charges->local . '</td><td>' . $return_charges->national_charges_class_0 . '</td></tr>';
                            }
                            else{
                                $return_charges_details .= '<tr><td>' . $return_charges->local . '</td><td>' . $return_charges->same_zone . '</td></tr>';
                            }


                            $return_charges_details .= '</tbody></table></div></div>';

                        }
                    }



                    $rate_details .= $service_type_details;
                    $rate_details .= $rate_origin_details;
                    $rate_details .= $rate_destination_details;
                    $rate_details .= $weight_charges_details;
                    $rate_details .= $cash_handling_details;
                    $rate_details .= $insurance_charges_details;
                    $rate_details .= $fuel_surcharge_charges_details;
                    $rate_details .= $return_charges_details;
                    $rate_details .= '<div class="new-page"></div>';
                }
              /*  $html .= $packaging_details;*/
                $html .= $rate_details;
            }

            if($international_rates){
                $international_rate_boxes = '';
                $international_rate_status = InternationalUserRate::where('user_id', $id)->first();
                $international_economic_rate_status = (InternationalEconomyRate::where('user_id', $id)->count() > 0);
                if($international_economic_rate_status)
                {
                    $intl_box = '';

                    $intl_box .= '<div class="row"><div class="col-12 border"> <table class="table color secondary table-sm table-bordered mb-0 mt-0"><thead class="color secondary text-center">
                    <tr><td><strong>International Economy Rate(s)</strong></td></tr></thead></table>';
                    $zone_rates = InternationalEconomyRate::where('user_id', $id)->get()->groupBy('zone_id');
                    foreach ($zone_rates as $zone_id => $rates)
                    {
                        $intl_box .= '<table class="table table-sm table-bordered mb-0 mt-0"><thead><tr class="color secondary"><td colspan="4"><strong>Weight Charges ('.Zone::find($zone_id)->name.')</strong></td></tr>
                    <tr><th>Range Up</th><th>Range Down</th><th>Weight Addition</th><th>Flat Charges</th></tr></thead><tbody>';
                        foreach ($rates as $rate)
                        {
                            $weight_addition = 0.00;
                            if($rate->weight_addition == 1)
                            {
                                $weight_addition = $rate->kg_range;
                            }
                            $intl_box .= '<tr><td>'.$rate->range_up.'</td><td>'.$rate->range_down.'</td><td>'.$weight_addition.'</td><td>'.$rate->flat_charges.'</td></tr>';
                        }
                        $intl_box .= '</tbody></table>';
                    }

                    $intl_box .= '</div></div>';

                    $html .= $intl_box;

                }

                if($international_rate_status){

                    $intl_box = '';

                    $intl_box .= '<div class="row"><div class="col-12 border"> <table class="table color secondary table-sm table-bordered mb-0 mt-0"><thead class="color secondary text-center">
                    <tr><td><strong>International Rate(s)</strong></td></tr></thead></table>';
                  /*  $margin_1 = 0;
                    $margin_2 = 0;
                    $margin_3 = 0;
                    $margin_4 = 0;
                    $margin_5 = 0;
                    $margin_6 = 0;
                    $margin_7 = 0;
                    $margin_8 = 0;
                    $margin_9 = 0;
                    $margin_10 = 0;
                    $margin_11 = 0;
                    $margin_1 = (float)$international_rate_status->margin_1;
                    $margin_2 = (float)$international_rate_status->margin_2;
                    $margin_3 = (float)$international_rate_status->margin_3;
                    $margin_4 = (float)$international_rate_status->margin_4;
                    $margin_5 = (float)$international_rate_status->margin_5;
                    $margin_6 = (float)$international_rate_status->margin_6;
                    $margin_7 = (float)$international_rate_status->margin_7;
                    $margin_8 = (float)$international_rate_status->margin_8;
                    $margin_9 = (float)$international_rate_status->margin_9;
                    $margin_10 = (float)$international_rate_status->margin_10;
                    $margin_11 = (float)$international_rate_status->margin_11;*/
                  $margin = array('margin_1' => 0,'margin_2' => 0,'margin_3' => 0,'margin_4' => 0,'margin_5' => 0,'margin_6' => 0,'margin_7' => 0,'margin_8' => 0,'margin_9' => 0,'margin_10' => 0,'margin_11' => 0,);
                    $margin['margin_1']= (float)$international_rate_status->margin_1;
                    $margin['margin_2']= (float)$international_rate_status->margin_2;
                    $margin['margin_3']= (float)$international_rate_status->margin_3;
                    $margin['margin_4']= (float)$international_rate_status->margin_4;
                    $margin['margin_5']= (float)$international_rate_status->margin_5;
                    $margin['margin_6']= (float)$international_rate_status->margin_6;
                    $margin['margin_7']= (float)$international_rate_status->margin_7;
                    $margin['margin_8']= (float)$international_rate_status->margin_8;
                    $margin['margin_9']= (float)$international_rate_status->margin_9;
                    $margin['margin_10'] = (float)$international_rate_status->margin_10;
                    $margin['margin_11'] = (float)$international_rate_status->margin_11;

                    $fuel_charges = 0;
                    $fuel_surcharge = GlobalSettings::where('type', 'international_fuel_surcharge');
                    if($fuel_surcharge->exists()){
                        $fuel_surcharge = $fuel_surcharge->first();
                        $fuel_charges = (float)$fuel_surcharge->text;
                    }

                    $exchange_rate_charges = 0;
                    $exchange_rate = GlobalSettings::where('type', 'international_exchange_rate');
                    if($exchange_rate->exists()){
                        $exchange_rate = $exchange_rate->first();
                        $exchange_rate_charges = (float)$exchange_rate->text;
                    }

                    $gst = 0;
                    $gst_rate = GlobalSettings::where('type', 'international_gst_rate');
                    if($gst_rate->exists()){
                        $gst_rate = $gst_rate->first();
                        $gst = (float)$gst_rate->text;
                    }

                    $intl_charges = '<div class="row"><div class="col-12"><table class="table color secondary table-sm table-bordered mb-0 mt-0"><thead><tr><td><strong>Charges</strong></thead></table></div></div>';
                    $intl_charges .= '<div class="row"><div class="col-12"><table class="table table-sm table-bordered mb-0"><thead><tr><th>Fuel Surcharge</th><th>Exchange Rate</th><th>GST</th></tr></thead><tbody>';

                    $intl_charges .= '<tr><td>' . $fuel_charges .'%' . '</td><td>' . $exchange_rate_charges . '</td><td>' . $gst . '%' . '</td></tr>';

                    $intl_charges .= '</tbody></table></div></div>';
                    $intl_box .= $intl_charges;

                    $fuel_surcharge_flat = $fuel_charges / 100;
                    $gst_flat = $gst / 100;

                    $intl_weight_charges = InternationalStandardDhlRate::all();
                    $intl_weight_charges_details = '';
                    if(count($intl_weight_charges) > 0){
                        $intl_weight_charges_details .= '<div class="row"><div class="col-12"> <table class="table color secondary table-sm table-bordered mb-0 mt-0"><thead><tr><td><strong>Weight Charges </strong></thead></table></div></div>';
                        $intl_weight_charges_details .= '<table class="table table-sm table-bordered mb-0"><thead><tr><th>Range Up</th><th>Range Down</th><th>Zone 1</th><th>Zone 2</th><th>Zone 3</th><th>Zone 4</th><th>Zone 5</th><th>Zone 6</th><th>Zone 7</th><th>Zone 8</th><th>Zone 9</th><th>Zone 10</th><th>Zone 11</th></tr></thead><tbody>';

                        foreach ($intl_weight_charges as $weight_charge) {
                           
                            $zone_1_charges = self::international_charges_calculate($weight_charge->zone_1, $fuel_surcharge_flat, $exchange_rate_charges,  $margin['margin_1'], $gst_flat);
                            $zone_2_charges = self::international_charges_calculate($weight_charge->zone_2, $fuel_surcharge_flat, $exchange_rate_charges,  $margin['margin_2'], $gst_flat);
                            $zone_3_charges = self::international_charges_calculate($weight_charge->zone_3, $fuel_surcharge_flat, $exchange_rate_charges,  $margin['margin_3'], $gst_flat);
                            $zone_4_charges = self::international_charges_calculate($weight_charge->zone_4, $fuel_surcharge_flat, $exchange_rate_charges, $margin['margin_4'], $gst_flat);
                            $zone_5_charges = self::international_charges_calculate($weight_charge->zone_5, $fuel_surcharge_flat, $exchange_rate_charges,  $margin['margin_5'], $gst_flat);
                            $zone_6_charges = self::international_charges_calculate($weight_charge->zone_6, $fuel_surcharge_flat, $exchange_rate_charges,  $margin['margin_6'], $gst_flat);
                            $zone_7_charges = self::international_charges_calculate($weight_charge->zone_7, $fuel_surcharge_flat, $exchange_rate_charges,  $margin['margin_7'], $gst_flat);
                            $zone_8_charges = self::international_charges_calculate($weight_charge->zone_8, $fuel_surcharge_flat, $exchange_rate_charges,  $margin['margin_8'], $gst_flat);
                            $zone_9_charges = self::international_charges_calculate($weight_charge->zone_9, $fuel_surcharge_flat, $exchange_rate_charges,  $margin['margin_9'], $gst_flat);
                            $zone_10_charges = self::international_charges_calculate($weight_charge->zone_10, $fuel_surcharge_flat, $exchange_rate_charges,  $margin['margin_10'], $gst_flat);
                            $zone_11_charges = self::international_charges_calculate($weight_charge->zone_11, $fuel_surcharge_flat, $exchange_rate_charges,  $margin['margin_11'], $gst_flat);

                            $intl_weight_charges_details .= '<tr><td>' . $weight_charge->range_up . '</td><td>' . $weight_charge->range_down . '</td><td>' . $zone_1_charges . '</td><td>' . $zone_2_charges . '</td><td>' . $zone_3_charges . '</td><td>' . $zone_4_charges . '</td><td>' . $zone_5_charges . '</td><td>' . $zone_6_charges . '</td><td>' . $zone_7_charges . '</td><td>' . $zone_8_charges . '</td><td>' . $zone_9_charges . '</td><td>' . $zone_10_charges . '</td><td>' . $zone_11_charges . '</td></tr>';
                        }
                        $intl_weight_charges_details .= '</tbody></table>';
                        $intl_box .= $intl_weight_charges_details;
                    }


                    $international_rate_boxes .= $intl_box;
                    $html .= $international_rate_boxes;

                    $html .= '<div class="new-page"></div>';
                }
            }


        $html .=$claim_policy;
        $html .= '</div>';
        $fuel_charge = '';
        if($shipper->account_type_id == 1){
            $fuel_surcharge = FuelSurcharge::where('user_id', $id)->where('shipping_mode_id', $shipper->default_shipping_mode)->first();
        }else{
            $fuel_surcharge = CorporateFuelSurcharge::where('user_id', $id)->where('shipping_mode_id', $shipper->default_shipping_mode)->first();
        }
        if($fuel_surcharge){
            $fuel_charge = $fuel_surcharge->fuel_surcharge;
        }
        $check = '';
        if($shipper->term_and_conditions){
            $check = 'checked';
        }
        $terms_conditions = '<div class="terms_conditions pl-2 pt-6"><h2><u>General Terms & Conditions </u></h2>';
        $terms_conditions .= '<ul class="">
                               <li>The client must agree to the following terms and conditions: </li>
                               <li><strong>TRAX Online (Pvt) Ltd.</strong> will act as an agent on behalf of the customer. We shall have complete legal authority to collect the cash and transfer the ownership of goods to the consignee. </li>
                               <li>Copy of NTN Certificate will be required for account activation. </li>
                               <li>Taxes will be applicable on total shipment charges depending on the origin of shipments. </li>
                               <li>13% GST will be applied to the shipments originating from Sindh. </li>
                               <li>16% GST will be applied to the shipments originating from Punjab. </li>
                               <li>'.$fuel_charge.' % Fuel Surcharge will be applied.</li>
                               <li>All the rates are subjected to change at any time. However, customer will be informed 2 weeks prior to the incorporation of the change. </li>
                               <li>During the transit, if any government agency like CAA, FIA inspect the shipment for security or regulatory reasons, customer will be responsible to provide the relevant documents. </li>
                               <li>Customer should not move or ship any of the below mentioned items through <strong>TRAX Online (Pvt) Ltd.</strong></li>
                               <li>Currency, jewelry, Bullion, Antiques, Liquor, Stamps, Precious Metals, Precious Stones, Works of Art, Fire Arms, Plants, Drugs, Explosives, Animals, Perishable goods and items, Negotiable Instruments in bearer form, Lewd Objects, Obscene and Pornographic Material, Industrial Carbons and Diamonds, hazardous or combustible materials, and all other items/articles restricted by IATA (International Air Transport Association), ICAO (International Civil Aviation Organization) and any item whose distribution is regulated by law or by any statute of the Provincial or Federal Government. TRAX will have full legal authority to act against the customer in case any such item is found in any shipments.</li>
                               <li>In case any such prohibited/fake product is distributed/transferred/shipped/ couriered via TRAX Online, and TRAX Online or any of its employees face any legal charges in lieu of such shipment, shipper shall be solely and fully responsible for it and shall fully indemnify TRAX Online in this regard. Moreover, such indemnification shall not relinquish the legal and constitutional right of TRAX Online to take legal actions/raise claim against shipper for grievance caused due to said shipment. </li>
                               <li>Any illegal and immodest product (if any) exchange shall be the responsibility of shipper and its consignee and shall not be the responsibility of Trax Online. </li>
                               <li>Shipper and its consignee shall indemnify Trax Online from any legal claims arising against each other in light of such exchange.</li>
                               <li><strong>TRAX Online (Pvt) Ltd.</strong> may add new terms & conditions at any point in time.</li>
                             </ul>';
        if(!$for_shipper_agreement_modal) {
            $terms_conditions .= '<h2 class="mt-0"><u>Acknowledgment & Signature</u></h2>';
            $terms_conditions .= '<div class="m-2"><input class="form-check-input" type="checkbox" value="1" disabled ' . $check . '> <span class="mt-2">I hereby accept all the terms and conditions mention above along with the agreed upon rates mentioned within.</span> </div><div class="row mt-2"><div class="col-6"><span class="border-bottom"><strong>Rates Added By</strong></span><p class="pt-2">' . $sales_person_name . '</p></div><div class="col-6"><p><span class="border-bottom"><strong>Shipper Signature</strong></span></p><p class="pt-2"><span class="border-bottom"><strong>Company Stamp</strong></span></p></div></div>';
        }


        $terms_conditions .= '</div>';
        $html .= $terms_conditions;
        $html .= '</div>';
        $notification = false;
        if(!$for_shipper_agreement_modal) {
            if ($file_type == 1) {
                $notification = true;
            }
            if (!$notification) {
                $html .= '<script>
                  window.onload = function() {
                    window.print();
                  }
                </script>';
            }
            $html .= '</body>
                </html>
      ';
        }

        return $html;
    }

    static public function international_charges_calculate($zone_charge, $fsc, $er, $margin, $gst){
        
        $zone_charge = (float)$zone_charge;
        $overall_charges = 0;
        $overall_charges = $zone_charge * $fsc;
        $overall_charges = $overall_charges + $zone_charge;
        $margin_charges = ((100 + $margin) / 100) * $overall_charges;
        $charges_w_gst = ($margin_charges * $gst);
        $charges_w_gst = $charges_w_gst + $margin_charges;
        $final_charges = $charges_w_gst * $er;
        return round($final_charges, 2);

    }


    public function accept($token, $id){
        if(($id != null) && ($token !== null)){
            $user = User::find($id);
            if($user && $user->term_and_conditions == 0){
                $term = CRFTermsConditions::where('user_id', $id)->where('token', $token);
                if($term->exists()){
                    $term = $term->delete();
                    User::where('id', $id)->update(['term_and_conditions' => 1]);
                    return redirect(route('cod.terms.success'));
                }
            }else{
                return redirect(route('cod.404'));
            }
        }
        return redirect(route('cod.404'));
    }
    public function accept_success(){
        return view('client.terms_success');
    }

    public function crf_download($token, $id){
        if(($id != null) && ($token !== null)){
            $user = User::find($id);
            if($user && $user->term_and_conditions == 0){

                $term = CRFTermsConditions::where('user_id', $id)->where('token', $token);
                if($term->exists()){
                    $html = self::view_crf_agreement($id, 1);
                    $pdf = SnappyPDF::loadHTML($html);

                    $filename = 'Customer Registration Form' . '.pdf';
                    return $pdf->download($filename);
                }
            }else{
                return redirect(route('cod.404'));

            }
        }
    }
    /*public function old_crf_download(Request $request, $token, $id){
        $names = [
            'id' => 'Shipper ID',
            'token' => 'Token',
        ];

        $messages = [
            'required' => ':attribute is Required.',
            'integer' => ':attribute must be an Integer.',
            'string' => ':attribute must be a String.',
        ];
        $rules = [
            'id' => ['required', 'integer', Rule::exists('users', 'id')],
            'token' => ['required', 'string']
        ];

        $validate = Validator::make($request->all(), $rules, $messages);

        $validate->setAttributeNames($names);

        if ($validate->fails()) {
            return redirect(route('cod.404'));
        }
        if(($id != null) && ($token !== null)){
            $user = User::find($id);
            if($user && $user->term_and_conditions == 0){

                $term = CRFTermsConditions::where('user_id', $id)->where('token', $token);
                if($term->exists()){
                    $html = self::view_crf_agreement($id, 1);
                    $pdf = SnappyPDF::loadHTML($html);

                    $filename = 'Customer Registration Form' . '.pdf';
                    return $pdf->download($filename);
                }
            }else{
                return redirect(route('cod.404'));
            }
        }
    }*/
}
