<?php

namespace App\Http\Middleware;

use App\Http\Models\Shipper\User;

use Closure;

class APIToken
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

        if($api_token) {
            $user = User::where('api_token', $api_token);

            if ($user->exists()) {
                $user = $user->first();

                if ($user->blacklist == 1) {
                    return response()->json([
                        'status' => 1,
                        'message' => 'Your Account is Blacklisted.'
                    ]);
                }
                else if ($user->status != 3) {
                    return response()->json([
                        'status' => 1,
                        'message' => 'Your Account is not Activated yet.'
                    ]);
                }
                else if ($user->phone_number_verified == 0){
                    return response()->json(['status' => 1, 'message' => 'Your Account phone number is not verified.']);
                }
                else {
                    $request->merge(['user_id' => $user->id,'app_type'=>$app_type]);

                    return $next($request);
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
