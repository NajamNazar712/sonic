<?php

namespace App\Http\Middleware;

use App\Http\Models\Shipper\User;
use Closure;
use App\Http\Models\Admin\Retail\RetailShipperInfo;

class ShipperAPIToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $api_token = $request->header('Authorization');
        $app_type = $request->header('Via');


        if ($api_token) {

            if($app_type == 2) {
                $retail_user = RetailShipperInfo::where('api_token', $api_token)->first();
                if($retail_user) {
                    $request->merge(['retail_shipper_id' => $retail_user->id,'app_type'=>$app_type]);
                    return $next($request);
                } else {
                    return response()->json([
                        'status' => 1,
                        'message' => 'Invalid API Token (Authorization).'
                    ]);
                }
            }
            $shipper = User::where('api_token', $api_token);
            if ($shipper->exists()) {
                $shipper = $shipper->first();
                $request->merge(['shipper_id' => $shipper->id,'app_type'=>1]);
                return $next($request);
            } else {
                return response()->json([
                    'status' => 1,
                    'message' => 'Invalid API Token (Authorization).'
                ]);
            }
        } else {
            return response()->json([
                'status' => 1,
                'message' => 'API Token (Authorization) is Missing.'
            ]);
        }
    }
}
