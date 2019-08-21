<?php

namespace App\Http\Controllers;

use App\Http\Models\Admin\Admin;
use App\Http\Models\BookingType;
use App\Http\Models\CashHandlingCharge;
use App\Http\Models\CorporateCashHandlingCharge;
use App\Http\Models\CorporateFuelSurcharge;
use App\Http\Models\CorporateMinChargeableWeight;
use App\Http\Models\CorporateRateStatus;
use App\Http\Models\CorporateReturnCharge;
use App\Http\Models\CorporateWeightCharge;
use App\Http\Models\FuelSurcharge;
use App\Http\Models\PackagingCharge;
use App\Http\Models\RateStatus;
use App\Http\Models\ReturnCharge;
use App\Http\Models\Shipper\User;
use App\Http\Models\ShippingMode;
use App\Http\Models\WeightCharge;
use Illuminate\Http\Request;

class ShipperAgreementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function view_crf_agreement(Request $request, $id){
        $shipper_id = $id;

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

//                      table.table-bordered tbody tr td {
//                        border: 1px solid #09262e !important;
//                      }

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
                    </style>
                  </head>
                  <body>
                    <div>
                    <div class="double-border">
      ';
        $shipper = User::find($id);
        $poc = $shipper->shipping()->where('default_address', 1)->select('poc')->first();
        $poc_name = '';
        if($poc){
            $poc_name = $poc->poc;
        }
        $sales_person_name = '';
        $sales_person = $shipper->sales_person()->where('status', 0)->first();
        if($sales_person){
            $sales_person_name = Admin::find($sales_person->admin_id)->name;
        }


        $page = '<table class="table table-sm table-borderless mb-0">
                        <tbody>
                          <tr class="double-border-bottom">
                            <td colspan="4" class="text-center align-middle"><img src="' . asset('img/trax_logo.png') . '" width="150" class="d-block mx-auto"></td>
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
                                <td colspan="1">' . $shipper->bank->bank->name . '</td>
                          </tr>
                          <tr>      
                                <td colspan="1"><strong>Person Of Contact</strong></td>
                                
                                <td colspan="1">' . $poc_name . '</td>
                                
                                <td colspan="1"><strong>Account No.</strong></td>
                                <td colspan="1">' . $shipper->bank->account_no . '</td>
                          </tr>
                          <tr>      
                                <td colspan="1"><strong>Phone Number 1</strong></td>
                                
                                <td colspan="1">' . $shipper->phone . '</td>
                                
                                <td colspan="1"><strong>Account Title</strong></td>
                                <td colspan="1">' . $shipper->bank->account_title . '</td>
                          </tr>
                          <tr>      
                                <td colspan="1"><strong>Phone Number 2</strong></td>
                                
                                <td colspan="1">' . $shipper->phone2 . '</td>
                                
                                <td colspan="1"><strong>IBAN Number</strong></td>
                                <td colspan="1">' . $shipper->bank->iban . '</td>
                          </tr>
                          <tr>      
                                <td colspan="1"><strong>CNIC Number</strong></td>
                                
                                <td colspan="1">' . $shipper->cnic . '</td>
                                
                                <td colspan="1"><strong>Payment Cycle</strong></td>
                                <td colspan="1">' . ucfirst($shipper->bank->payment_cycle) . '</td>
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
                          <tr class="double-border-bottom">
                          <td colspan="4"></td></tr>
                          
                        </tbody>
                      </table>';


            $packaging_details = '';
            if($shipper->account_type_id == 1){
                $packaging_details .= '<table class="table table-sm table-bordered mb-0">
                            <tbody>';
                $packaging_charges = PackagingCharge::where('user_id', $id)->get();
                if($packaging_charges){
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
                }
                $packaging_details .=  '</tbody>
                          </table>';
                $rates_switch = RateStatus::where('user_id', $id)->get();

            }else if($shipper->account_type_id == 2){
                $rates_switch = CorporateRateStatus::where('user_id', $id)->get();
            }

            $rate_details = '';

            foreach ($rates_switch as $rate){
                $service_type_details = '';
                $chargeable_weight_details = '';
                $weight_charges_details = '';
                $service_type = ShippingMode::find($rate->shipping_mode_id);
                $service_type_details = '<div class="row p-1"><div class="col-3 p-1 color secondary rounded border"><h4><strong>Shipping Mode </strong></h4></div><div class="col-3 pt-1 color border rounded text-center"><h4>' . $service_type->mode . '</h4></div></div>';


                if($shipper->account_type_id == 1){
                    $weight_charges = WeightCharge::where('user_id', $id)->where('shipping_mode_id', $rate->shipping_mode_id)->get();
                }else{
                    $weight_charges = CorporateWeightCharge::where('user_id', $id)->where('shipping_mode_id', $rate->shipping_mode_id)->get();
                }

                if($weight_charges){
                    $weight_charges_details = '<div class="row p-1"><div class="col-3 p-1 color secondary rounded border"><h4><strong>Weight Charges </strong></h4></div></div>';
                    if($shipper->account_type_id == 2){
                        $chargeable_weight = CorporateMinChargeableWeight::where('user_id', $id)->where('shipping_mode_id', $rate->shipping_mode_id)->where('delivery_type_id', 1)->first();

                        $chargeable_weight_details = '<div class="row mb-1"><div class="col-5"><table class="table table-sm table-bordered mb-0">
                            <tbody><tr><td class="color primary" ><strong>Delivery Type</strong></td><td>' . $chargeable_weight->delivery_type->delivery_type . '</td></tr><tr><td class="color primary"><strong>Charges</strong></td><td>' . $chargeable_weight->min_chargeable_weight . '</td></tr></tbody>
                          </table></div></div>';
                        $weight_charges_details .= $chargeable_weight_details;
                        if($rate->shipping_mode_id != 4){
                            $weight_charges_details .= '<table class="table table-sm table-bordered"><thead><tr><th>Range Up</th><th>Range Down</th><th>Flat Charges/KG (Local)</th><th>Flat Charges/KG (National-Zone A)</th><th>Flat Charges/KG (National-Zone B)</th><th>Flat Charges/KG (National-Zone C)</th><th>Flat Charges/KG (National-Zone D)</th></tr></thead><tbody>';

                            foreach ($weight_charges as $weight_charge){
                                if($weight_charge->delivery_type_id == 1){
                                    $weight_charges_details .= '<tr><td>' . $weight_charge->range_up . '</td><td>' . $weight_charge->range_down . '</td><td>' . $weight_charge->local_or_6hr . '</td><td>' . $weight_charge->national_charges_class_0 . '</td><td>' . $weight_charge->national_charges_class_1 . '</td><td>' . $weight_charge->national_charges_class_2 . '</td><td>' . $weight_charge->national_charges_class_3 . '</td><td>' . $weight_charge->national_charges_class_4 . '</td></tr>';
                                }
                            }
                            $weight_charges_details .= '</tbody></table>';

                            $chargeable_weight = CorporateMinChargeableWeight::where('user_id', $id)->where('shipping_mode_id', $rate->shipping_mode_id)->where('delivery_type_id', 2)->first();

                            $chargeable_weight_details = '<div class="row mb-1"><div class="col-5"><table class="table table-sm table-bordered mb-0">
                            <tbody><tr><td class="color primary" ><strong>Delivery Type</strong></td><td>' . $chargeable_weight->delivery_type->delivery_type . '</td></tr><tr><td class="color primary"><strong>Charges</strong></td><td>' . $chargeable_weight->min_chargeable_weight . '</td></tr></tbody>
                          </table></div></div>';
                            $weight_charges_details .= $chargeable_weight_details;
                            $weight_charges_details .= '<table class="table table-sm table-bordered"><thead><tr><th>Range Up</th><th>Range Down</th><th>Flat Charges/KG (Local)</th><th>Flat Charges/KG (National-Zone A)</th><th>Flat Charges/KG (National-Zone B)</th><th>Flat Charges/KG (National-Zone C)</th><th>Flat Charges/KG (National-Zone D)</th></tr></thead><tbody>';
                            foreach ($weight_charges as $weight_charge){
                                if($weight_charge->delivery_type_id == 2){
                                    $weight_charges_details .= '<tr><td>' . $weight_charge->range_up . '</td><td>' . $weight_charge->range_down . '</td><td>' . $weight_charge->local_or_6hr . '</td><td>' . $weight_charge->national_charges_class_0 . '</td><td>' . $weight_charge->national_charges_class_1 . '</td><td>' . $weight_charge->national_charges_class_2 . '</td><td>' . $weight_charge->national_charges_class_3 . '</td><td>' . $weight_charge->national_charges_class_4 . '</td></tr>';
                                }
                            }
                            $weight_charges_details .= '</tbody></table>';


                        }else{
                            $weight_charges_details .= '<table class="table table-sm table-bordered"><thead><tr><th>Range Up</th><th>Range Down</th><th>6hr Charges</th><th>Sameday Charges</th></tr></thead><tbody>';

                            foreach ($weight_charges as $weight_charge){
                                if($weight_charge->delivery_type_id == 1){
                                    $weight_charges_details .= '<tr><td>' . $weight_charge->range_up . '</td><td>' . $weight_charge->range_down . '</td><td>' . $weight_charge->local_or_6hr . '</td><td>' . $weight_charge->national_charges_class_0 . '</td></tr>';
                                }
                            }
                            $weight_charges_details .= '</tbody></table>';

                            $chargeable_weight = CorporateMinChargeableWeight::where('user_id', $id)->where('shipping_mode_id', $rate->shipping_mode_id)->where('delivery_type_id', 2)->first();

                            $chargeable_weight_details = '<div class="row mb-1"><div class="col-5"><table class="table table-sm table-bordered mb-0">
                            <tbody><tr><td class="color primary" ><strong>Delivery Type</strong></td><td>' . $chargeable_weight->delivery_type->delivery_type . '</td></tr><tr><td class="color primary"><strong>Charges</strong></td><td>' . $chargeable_weight->min_chargeable_weight . '</td></tr></tbody>
                          </table></div></div>';
                            $weight_charges_details .= $chargeable_weight_details;
                            $weight_charges_details .= '<table class="table table-sm table-bordered"><thead><tr><th>Range Up</th><th>Range Down</th><th>6hr Charges</th><th>Sameday Charges</th></tr></thead><tbody>';
                            foreach ($weight_charges as $weight_charge){
                                if($weight_charge->delivery_type_id == 2){
                                    $weight_charges_details .= '<tr><td>' . $weight_charge->range_up . '</td><td>' . $weight_charge->range_down . '</td><td>' . $weight_charge->local_or_6hr . '</td><td>' . $weight_charge->national_charges_class_0 . '</td></tr>';
                                }
                            }
                            $weight_charges_details .= '</tbody></table>';

                        }


                    }
                    if($shipper->account_type_id == 1) {
                        if($rate->shipping_mode_id != 4){
                            $weight_charges_details .= '<table class="table table-sm table-bordered mb-0"><thead><tr><th>Range Up</th><th>Range Down</th><th>Weight Slab</th><th>Local Charges</th><th>National Charges Class A</th><th>National Charges Class B</th><th>National Charges Class C</th><th>National Charges Class D</th></tr></thead><tbody>';

                            foreach ($weight_charges as $weight_charge) {
                                if ($shipper->account_type_id == 1) {
                                    $weight_charges_details .= '<tr><td>' . $weight_charge->range_up . '</td><td>' . $weight_charge->range_down . '</td><td>' . $weight_charge->spkg . '</td><td>' . $weight_charge->local_or_6hr . '</td><td>' . $weight_charge->national_charges_class_0 . '</td><td>' . $weight_charge->national_charges_class_1 . '</td><td>' . $weight_charge->national_charges_class_2 . '</td><td>' . $weight_charge->national_charges_class_3 . '</td></tr>';
                                }
                            }
                            $weight_charges_details .= '</tbody></table>';
                        }else{
                            $weight_charges_details .= '<table class="table table-sm table-bordered mb-0"><thead><tr><th>Range Up</th><th>Range Down</th><th>Weight Slab</th><th>6hr Charges</th><th>Sameday  Charges</th></tr></thead><tbody>';

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
                    $cash_handling = CorporateCashHandlingCharge::where('user_id', $id)->where('shipping_mode_id', $rate->shipping_mode_id)->get();
                }
                if($cash_handling){
                    $cash_handling_details = '<div class="row p-1"><div class="col-3 p-1 color secondary rounded border"><h4><strong>Cash Handling Charges </strong></h4></div></div>';
                    $cash_handling_details .= '<table class="table table-sm table-bordered mb-0"><thead><tr><th>Range Up</th><th>Range Down</th><th>Charges</th></tr></thead><tbody>';
                    foreach ($cash_handling as $cash){
                        $cash_handling_details .= '<tr><td>' . $cash->range_up . '</td><td>' . $cash->range_down . '</td><td>' . $cash->charges . '</td></tr>';
                    }
                    $cash_handling_details .= '</tbody></table>';
                }

                $fuel_surcharge_charges_details = '';
                if($shipper->account_type_id == 1){
                    $fuel_surcharge = FuelSurcharge::where('user_id', $id)->where('shipping_mode_id', $rate->shipping_mode_id)->first();
                }else{
                    $fuel_surcharge = CorporateFuelSurcharge::where('user_id', $id)->where('shipping_mode_id', $rate->shipping_mode_id)->first();
                }
                if($fuel_surcharge){
                    $fuel_surcharge_charges_details = '<div class="row p-1"><div class="col-3 p-1 color secondary rounded border"><h4><strong>Fuel Surcharge </strong></h4></div></div>';
                    $fuel_surcharge_charges_details .= '<div class="row mb-1"><div class="col-5"><table class="table table-sm table-bordered mb-0">
                            <tbody><tr><td class="color primary" ><strong>Fuel Charges</strong></td><td>' . $fuel_surcharge->fuel_surcharge . '%</td></tr></tr></tbody>
                          </table></div></div>';
                }

                $return_charges_details = '';
                if($shipper->account_type_id == 1){
                    $return_charges = ReturnCharge::where('user_id', $id)->where('shipping_mode_id', $rate->shipping_mode_id)->first();
                }else{
                    $return_charges = CorporateReturnCharge::where('user_id', $id)->where('shipping_mode_id', $rate->shipping_mode_id)->first();
                }
                if($return_charges){
                    $return_charges_details .= '<div class="row p-1"><div class="col-3 p-1 color secondary rounded border"><h4><strong>Return Charges </strong></h4></div></div>';
                    $return_charges_details .= '<table class="table table-sm table-bordered mb-0"><thead><tr><th>Local</th><th>Zone A</th><th>Zone B</th><th>Zone C</th><th>Zone D</th></tr></thead><tbody>';
                    foreach ($return_charges as $return_charge){
                        $return_charges_details .= '<tr><td>' . $return_charges->local . '</td><td>' . $return_charges->national_charges_class_0 . '</td><td>' . $return_charges->national_charges_class_1 . '</td><td>' . $return_charges->national_charges_class_2 . '</td><td>' . $weight_charge->national_charges_class_3 . '</td></tr>';
                    }
                    $return_charges_details .= '</tbody></table>';


                }

            $rate_details .= $service_type_details;
            $rate_details .= $weight_charges_details;
            $rate_details .= $cash_handling_details;
            $rate_details .= $fuel_surcharge_charges_details;
            $rate_details .= $return_charges_details;
            }




        $html .= $page;
        $html .= $packaging_details;

        $html .= $rate_details;
        $html .= '</div>
                    </div>

                    
                  </body>
                </html>
      ';

        return $html;
    }

}
