<?php

namespace App\Services\Marco\Packaging;

use App\Http\Models\PackagingMaterialRequest;
use App\Http\Models\PackagingMaterialRequestHistory;
use Illuminate\Support\Facades\Http;
use App\Models\ApiHandshakeLog;
use App\Services\Marco\MarcoBaseService;
use App\User;
use Illuminate\Support\Facades\Auth;

class MarcoOrderService extends MarcoBaseService
{
     /**
     * Book Packaging Order in Marco
     */
    public function bookPackagingOrder(object $request_details): array
    {
        
        /** STEP 1: Fetch packaging request */
      
        if (! $request_details) {
            return $this->fail('Packaging material request not found.');
        }

        /** STEP 2: Build Marco payload */
        $payload = $this->buildPayload($request_details);

        /** STEP 3: Call Marco API */
        $log = ApiHandshakeLog::create(
            [
                'service_name'    => 'marco',
                'endpoint'        => '/api/user/v1/order/packaging',
                'method'          => 'POST',
                'reference_id'    => $payload['shipper_order_id'],
                'request_payload' => $payload,
                'status'          => 'pending',
        ]);

        $response = Http::withHeaders($this->headers())
            ->timeout(30)
            ->post(
                $this->endpoint('api/user/v1/order/packaging'),
                $payload
        );

        $log->update([
            'response_payload' => $response->json(),
            'response_status'  => $response->status(),
            'status'           => $response->successful() ? 'success' : 'failed',
        ]);
        $responseStatus = $response->successful() ? 'success' : 'failed';
        if ($response->successful()) {
            $request_details->status_id = 2; // status for success
            $request_details->save();
        }
        if($responseStatus){
            $packaging_request_history = new PackagingMaterialRequestHistory();
            $packaging_request_history->packaging_material_request_id = $request_details->id;
            $packaging_request_history->status = 2;
            $packaging_request_history->updated_by = Auth::id();
            $packaging_request_history->save();
        }
        /** STEP 4: Handle failure */
        if ($response->failed()) {
            return [
                'status' => $responseStatus,
                'data'   => $response->json() ?? 'Marco API error',
            ];
        }

        /** STEP 5: Success */
        return [
                'status' => $responseStatus,
                'data'   => $response->json(),
            ];
    }

    /* ------------------ Helpers ------------------ */

    protected function buildPayload($request_details): array
    {
        
        $shipper_details = User::where('id', $request_details->user_id)->select('city_id', 'name', 'poc', 'phone', 'email', 'address')->first();

        $products = [];

        foreach ($request_details->items as $item) {
            $products[] = [
                'product_id'  => $item->wms_product_id,
                'quantity'    => $item->quantity,
            ];
        }
        return [
            'order_type'           => 1,
            'courier_type'         => 1,
            'self_pickup'          => 1,
            'city_name'            => $request_details->city?->name,
            'cod'                  => $request_details->amount,
            'consignee_name'       => "Packaging Material to $shipper_details->name",
            'address'              => $shipper_details->address,
            'phone_number_1'       => $shipper_details->phone,
            'shipper_order_id'     => $request_details->user_id . '-PM-' . $request_details->id,
            'shipping_mode'        => 1,
            'courier_charges_id'   => 1,
            'products'             => $products,
            'special_instructions' => $request->special_instructions ?? '-',
            'packaging_material_request_id'     => $request_details->id ?? null,
        ];
         
    }

    // public function bookPackagingOrder(int $request_details): integer
    // {
        
    //     $shipper_details = User::where('id', $user_id)->select('city_id', 'name', 'poc', 'phone', 'email', 'address')->first();

    //     foreach ($request_details->items as $item) {
    //         $newProducts[] = [
    //             'product_id'  => $item->wms_product_id,
    //             'quantity'    => $item->quantity,
    //         ];
    //     }

    //     $marcoPayload = [
    //         'order_type'           => 1,
    //         'courier_type'         => 1,
    //         'self_pickup'          => 1,
    //         'city_name'            => $request_details->city?->name,
    //         'cod'                  => $request_details->amount,
    //         'consignee_name'       => $shipper_details->name,
    //         'address'              => $shipper_details->address,
    //         'phone_number_1'       => $shipper_details->phone,
    //         'shipper_order_id'     => $user_id . '-PM-' . $request_details->id,
    //         'shipping_mode'        => 1,
    //         'courier_charges_id'   => 1,
    //         'products'             => $newProducts,
    //         'special_instructions' => $request->special_instructions ?? '-',
    //         'packaging_material_request_id'     => $request_details->id ?? null,
    //     ];
        
    // }
}
