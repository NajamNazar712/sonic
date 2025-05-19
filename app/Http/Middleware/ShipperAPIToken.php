<?php

namespace App\Http\Middleware;

use App\Http\Models\Shipper\User;
use Closure;

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
        $app_type = $request->app_type;

        if ($api_token) {

            if($app_type == 2) {
                if(RetailShipperInfo::where('api_token', $api_token)->first()) {
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
                $request->merge(['shipper_id' => $shipper->id]);
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
