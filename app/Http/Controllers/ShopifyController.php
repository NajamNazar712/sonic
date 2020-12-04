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
            'image' => ['nullable', 'image', 'max:2048']
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

    static public function invoice_generate($user_id, $order, $invoice){

        if(!empty($order)){
            $user = User::find($user_id)->name;
            $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

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

                      td.replacement span {
                        width: 22px;
                      }

                      td.replacement span img {
                        display: block;
                        width: 100%;
                        margin: auto;
                        background: #c8c8c8;
                        border-radius: 25px;
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
                    <div>
      ';
            $image = asset('storage/shopify_invoice_logos/' . $invoice->image);
            $html .= '<div class="container-fluid">
                        <div class="row mb-2 p-1">
                            <div class="col-6 text-left">
                             <img src="'. $image .'" width="100" class="d-block mb-1">
                            </div>
                            <div class="col-6 text-right">Invoice for #' . $order['order'] . '</div>
                            <div class="col-12"><h2>'. $user .'</h2></div>
                            <h5 class="col-12">'. $invoice->address .'</h5>
                            <div class="col-12 border-bottom"></div>
                        </div>
                        
                             
                             <div class="col-12">
                                <h1>Item Details</h1>
                             </div>
                             
                             
                            <div class="col-12">
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
                                            <td>'. $item['price'] .'</td>
                                        </tr>';
                            }

        $html .='                </tbody>
                                </table>
                            </div>
                             
                             <div class="col-12">
                                <h1>Payment Details</h1>
                             </div>
                             
                             <div class="col-12">
                             <table class="table border">
                                 <tbody>
                                 <tr>
                                     <td>Subtotal price: </td><td>'. $order['subtotal_price'] .'</td>
                                 </tr>
                                 <tr>
                                     <td>Total tax:</td><td>'. $order['total_tax'] .'</td>
                                 </tr>
                                 <tr>
                                     <td>Shipping</td><td>'. $order['shipping'] .'</td>
                                 </tr>
                                 <tr>
                                     <td>Total price:</td><td>'. $order['total_price'] .'</td>
                                 </tr>
                                 <tr>
                                     <td>Total paid</td><td>'. $order['total_paid'] .'</td>
                                 </tr>
                                 <tr>
                                     <td>Outstanding Amount:</td><td>'. $order['outstanding_amount'] .'</td>
                                 </tr>
                                 </tbody>
                             </table>
                             </div> 
                             <div class="col-12">
                                <h1>Shipping Details</h1>
                             </div>   
                             <div class="col-12">
                             <div class="col-12 border p-2">
                                <h3 class="">'. $order['consignee_name'] .'</h3>
                                <p>'. $order['consignee_address'] .'</p>
                                <p class="mb-0">Phone: '. $order['consignee_phone'] .'</p>
                            </div>
                            
                            <div class="col-12">
                                <p>'.  $invoice->message .'</p>
                            </div>
                            </div>
                             
                      </div>';

            $html .= '
                    </div>
                  </body>
                </html>
      ';

            return $html;
        }
    }
}
