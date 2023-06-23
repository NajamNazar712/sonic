<?php

namespace App\Http\Controllers;

use App\Http\Models\Shipper\User;
use App\Http\Models\Shopify\ShopifyInvoiceSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class ShopifyController extends Controller
{
    private $names = [
        'image' => 'Shipper Invoice Logo',
        'address' => 'Shipper Address',
        'message' => "Message"
    ];

    private $messages = [
        'integer' => ':attribute must be an Integer.',
        'digits' => ':attribute must be of :digits Digits.',
        'exists' => 'Given :attribute is of Invalid ID.',
        'image' => ':attribute must be an Image.'
    ];

    public function invoice_settings(Request $request){

        $request_data = json_decode($request->data);
        $user_id = $request->user_id;
        $rules = [
            'address' => ['string', 'max:255'],
            'message' => ['string', 'max:255'],
            'image' => ['nullable', 'mimes:jpg,jpeg,png', 'max:2048']
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        }
        else {
            $shopify_invoice = ShopifyInvoiceSetting::where('user_id', $user_id);
            if($shopify_invoice->exists()){
                $shopify_invoice = $shopify_invoice->first();
                $shopify_invoice->address = $request_data->address;
                $shopify_invoice->message = $request_data->message;

            }else{
                $shopify_invoice = new ShopifyInvoiceSetting();
                $shopify_invoice->user_id = $user_id;
                $shopify_invoice->address = $request_data->address;
                $shopify_invoice->message = $request_data->message;
            }

            if ($request->image != null) {
                $time = Carbon::now()->toDateString();
                $filename = 'logo_' . $user_id . $time . '.png';
                $file = $request->image;
                $picture_path = 'shopify_invoice_logos/' . $filename;

                Storage::disk('public')->put($picture_path, file_get_contents($file));

                $shopify_invoice->image = $filename;
            }

            $shopify_invoice->save();
            return response()->json(['status' => 0, 'success' => 'Invoice settings successfully updated!']);
        }
        return response()->json(['status' => 1, 'error' => 'Invalid request!']);

    }

    static public function invoice_generate($user_id, $order, $invoice, $shipment){

        if(!empty($order)){
            $user = User::find($user_id)->name;
            $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . public_path('app-assets/css/bootstrap.min.css') . '">

                    <title>Shopify Invoice</title>

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

                      table.table-bordered tbody tr td {
                        border: 1px solid #09262e !important;
                      }

                      .color.primary {
                        background: #c8c8c8 !important;
                      }

                      .color.secondary {
                        background: #ebebeb !important;
                      }

                      .border {
                        border: 1px solid #09262e !important;
                      }
                      
                      p{
                      font-size: 18px;
                      }
                      table td{
                        font-size: 20px;
                      }
                    </style>
                  </head>
                  <body>
                    <div class="border pt-1">
      ';

            $html .= '<div class="container-fluid">
                        <div class="row no-gutters">
                            <div class="col-6 text-left">
                             <img src="'. public_path('storage/shopify_invoice_logos/' . $invoice->image).'" width="100" class="d-block mb-1">
                            </div>
                            <div class="col-6 text-right"><h3>Invoices for #' . $order['order'] . '</h3></div>
                        </div>
                        <div>
                            <div><h2>'. $user.'</h2></div>
                        </div>
                        <div>
                            <div><h5>'. $invoice->address .'</h5></div>
                            <div class="border-bottom mt-2 mb-2"></div>
                        </div>
                             
                         <div>
                            <h1>Item Details</h1>
                         </div>
                             
                             
                        <div>
                            <table class="table border">
                                <thead>
                                    <tr>
                                        <td>Quantity</td>
                                        <td>Item</td>
                                        <td>Price</td>
                                    </tr>
                                    
                                </thead>
                                <tbody>';
                        foreach ($order['items'] as $item){
                            $html .='<tr>
                                        <td>'. $item['quantity'] .'x</td>
                                        <td>'. $item['name'] .'</td>
                                        <td>Rs.'. $item['price'] .'</td>
                                    </tr>';
                        }
                    $consignee_name = '';
                    $consignee_address = '';
                    $consignee_phone = '';
                    if(isset($order['consignee_name']) && !empty($order['consignee_name'])){
                        $consignee_name = $order['consignee_name'];
                    }
                    else{
                        $consignee_name = $shipment->consignee_name;
                    }
                    if(isset($order['consignee_address']) && !empty($order['consignee_address'])){
                        $consignee_address = $order['consignee_address'];
                    }
                    else{
                        $consignee_address = $shipment->consignee_address;
                    }

                    if(isset($order['consignee_phone']) && !empty($order['consignee_phone'])){
                        $consignee_phone = $order['consignee_phone'];
                    }
                    else{
                        $consignee_phone = $shipment->consignee_phone_number_1;
                    }

    $html .='                </tbody>
                            </table>
                        </div>
                             
                         <div class="mb-1">
                            <h1>Payment Details</h1>
                         </div>
                             
                         <div>
                         <table class="table border">
                             <tbody>
                             <tr>
                                 <td>Subtotal price: </td><td>Rs.'. $order['subtotal_price'] .'</td>
                             </tr>
                             <tr>
                                 <td>Total tax: </td><td>Rs.'. $order['total_tax'] .'</td>
                             </tr>
                             <tr>
                                 <td>Shipping: </td><td>Rs.'. $order['shipping'] .'</td>
                             </tr>
                             <tr>
                                 <td><b>Total price:</b></td><td><b>Rs.'. $order['total_price'] .'</b></td>
                             </tr>
                             <tr>
                                 <td><b>Total paid: </b></td><td><b>Rs.'. $order['total_paid'] .'</b></td>
                             </tr>
                             <tr>
                                 <td><b>Outstanding Amount:</b></td><td><b>Rs.'. $order['outstanding_amount'] .'</b></td>
                             </tr>
                             </tbody>
                         </table>
                         </div> 
                         <div class="mb-1">
                            <h1>Shipping Details</h1>
                         </div>   
                         <div>
                             <div class="border p-2">
                                <h3 class="">'. $consignee_name .'</h3>
                                <p>'. $consignee_address .'</p>
                                <p class="mb-0">Phone: '. $consignee_phone .'</p>
                            </div>
                        </div>
                            
                        <div class="mb-2 mt-2">
                            <p>'.  $invoice->message .'</p>
                        </div>
                      </div>
                             
                      </div>';

            $html .= '
<div class="col m-1 row justify-content-center"><div class="col"><hr></div>
                      <div class=""><i class="la la-cut la-rotate-180 align-middle"></i></div></div>
                    </div>
                  </body>
                </html>
      ';

            return $html;
        }
    }
}
