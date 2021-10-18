<?php

namespace App\Http\Middleware;

use App\Http\Models\Admin\Retail\RetailUser;
use App\Http\Models\ConsigneeUser;
use Closure;

class ConsigneeAPIToken
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

        if ($api_token) {
            $consignee = ConsigneeUser::where('api_token', $api_token);

            if ($consignee->exists()) {
                $consignee = $consignee->first();
                $request->request->add(['consignee_id' => $consignee->id]);
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
