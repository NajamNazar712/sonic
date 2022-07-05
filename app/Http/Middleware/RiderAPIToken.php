<?php

namespace App\Http\Middleware;

use App\Http\Models\HR\Employee;
use App\Http\Models\Rider;

use Closure;

class RiderAPIToken
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
            $rider = Rider::where('api_token', $api_token);

            if ($rider->exists()) {
                $rider = $rider->first();

                if ($rider->status) {
                    if($rider->employee){
                        $request->request->add(['rider_id' => $rider->id, 'trax_id' => $rider->trax_id, 'rider_employee' => $rider->employee->id]);
                    }
                    else{
                        $request->request->add(['rider_id' => $rider->id, 'trax_id' => $rider->trax_id]);
                    }
                    return $next($request);
                }
                else {
                    return response()->json([
                        'status' => 2,
                        'message' => 'Your Account is not Activate.'
                    ]);
                }
            }
            else {
                return response()->json([
                    'status' => 1,
                    'message' => 'Invalid API Token (Authorization).'
                ]);
            }
        }
        else {
            return response()->json([
                'status' => 1,
                'message' => 'API Token (Authorization) is Missing.'
            ]);
        }
    }
}
