<?php

namespace App\Http\Middleware;

use App\Http\Models\Admin\Admin;
use Closure;

class AdminAPIToken
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
            $admin = Admin::where('api_token', $api_token);

            if ($admin->exists()) {
                $admin = $admin->first();

                if ($admin->status) {
                    $request->request->add(['admin_id' => $admin->id, 'trax_id' => $admin->trax_id]);

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
