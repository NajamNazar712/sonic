<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\V2Dispute;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CheckDisputeShipmentsController extends Controller
{
    static public function check($shipment_id){
        $clear = TRUE;

        $dispute_check = V2Dispute::where('shipment_id', $shipment_id)->whereIn('status_id', ['1', '2']);
        if($dispute_check->exists()){
            $clear = FALSE;
        }

        return $clear;
    }
}
