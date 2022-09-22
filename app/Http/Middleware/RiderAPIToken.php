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
            $rider = Rider::leftjoin('cities as c', 'riders.city_id', '=', 'c.id')
                ->leftjoin('cities as h', 'c.hub_id', '=', 'h.id')
                ->where('riders.api_token', $api_token)
                ->select('riders.*', 'h.id as hub_id');

            if ($rider->exists()) {
                $rider = $rider->first();

                if ($rider->status) {
                    $employee = Employee::where('trax_id',$rider->trax_id)->whereNotNull('trax_id');
                    $employee_id = null;
                    if($employee->exists())
                    {
                        $employee = $employee->first();
                        $employee_id = $employee->id;
                    }
                    $request->request->add(['rider_id' => $rider->id, 'rider_employee' => $employee_id, 'trax_id' => $rider->trax_id, 'hub_id' => $rider->hub_id]);
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
