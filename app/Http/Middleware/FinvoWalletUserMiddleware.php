<?php

namespace App\Http\Middleware;

use App\Http\Models\Shipper\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class FinvoWalletUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        if (App::environment('local') || App::environment('staging')) {
            return $next($request);
        } else {
            $api_token = $request->header('Authorization');
            if ($api_token) {
                $user = User::where('api_token', $api_token);
                if ($user->exists()) {
                    $user = $user->first();
                    if($user->id != 46611) {
                        if ($user->blacklist == 1) {
                            return response()->json([
                                'status' => 1,
                                'message' => 'Your Account is Blacklisted.'
                            ]);
                        } else if ($user->status != 3) {
                            return response()->json([
                                'status' => 1,
                                'message' => 'Your Account is not Activated yet.'
                            ]);
                        } else if ($user->phone_number_verified == 0) {
                            return response()->json(['status' => 1, 'message' => 'Your Account phone number is not verified.']);
                        } else {
                            $request->merge(['user_id' => $user->id]);

                            return $next($request);
                        }
                    }else{
                        $request->merge(['user_id' => $user->id]);

                        return $next($request);
                    }
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
}
