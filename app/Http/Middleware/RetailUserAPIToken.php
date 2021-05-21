<?php

namespace App\Http\Middleware;

use App\http\Models\Admin\Retail\RetailUser;
use Closure;

class RetailUserAPIToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {$api_token = $request->header('Authorization');

        if ($api_token) {
            $retail_user = RetailUser::where('api_token', $api_token);

            if ($retail_user->exists()) {
                $retail_user = $retail_user->first();

                if ($retail_user->status) {
                    $request->request->add(['retail_user_id' => $retail_user->id]);

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
