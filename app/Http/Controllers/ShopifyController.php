<?php

namespace App\Http\Controllers;

use App\Http\Models\Shopify\ShopifyInvoiceSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
        Log::info('Hit');
//        Log::info(print_r($request, true));
        $user_id = $request->user_id;
        $rules = [
            'address' => ['string', 'max:255'],
            'message' => ['string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png', 'max:2048']
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
                $shopify_invoice->address = $request->address;
                $shopify_invoice->message = $request->message;

            }else{
                $shopify_invoice = new ShopifyInvoiceSetting();
                $shopify_invoice->user_id = $user_id;
                $shopify_invoice->address = $request->address;
                $shopify_invoice->message = $request->message;
            }

            if ($request->image != null) {
                Log::info('inside image');
                $filename = 'logo_' . $user_id . '.png';
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
}
