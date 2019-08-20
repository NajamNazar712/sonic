<?php

namespace App\Http\Controllers;

use App\Http\Models\Admin\Admin;
use App\Http\Models\CorporateRateStatus;
use App\Http\Models\PackagingCharge;
use App\Http\Models\RateStatus;
use App\Http\Models\Shipper\User;
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
                      }

                      hr {
                        border-top: 1px dashed #000000;
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


        $page = '<table class="table table-sm table-borderless">
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
        $rates_details = '<table class="table table-sm table-bordered">
                        <tbody>';
        if($shipper->account_type_id == 1){
            $rates_switch = RateStatus::where('user_id', $id)->get();
        }else{
            $rates_switch = CorporateRateStatus::where('user_id', $id)->get();
        }

        if($shipper->account_type_id == 1){
            $packaging_charges = PackagingCharge::where('user_id', $id)->groupBy('type_id')->get();
            if($packaging_charges){
                $ptype = '';
                foreach ($packaging_charges as $type){
                    if($ptype != $type->type_id){
                        $rates_details .= '<tr><td colspan="1">Packaging Material Type</td><td>' . $type->packaging_type->type . '</td></tr>';
                        $ptype = $type->type_id;
                    }

                }
            }
        }

        $rates_details .=  '</tbody>
                      </table>';


        $html .= $page;
        $html .= $rates_details;

        $html .= '</div>
                    </div>

                    
                  </body>
                </html>
      ';

        return $html;
    }

}
