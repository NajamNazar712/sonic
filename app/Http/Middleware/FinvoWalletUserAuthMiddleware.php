<?php

namespace App\Http\Middleware;

use App\Http\Models\Shipper\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
class FinvoWalletUserAuthMiddleware
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
            $authHeader = $request->header('Authorization');
            if (!$authHeader || !Str::startsWith($authHeader, 'Basic ')) {
                return response()->json(['status'=>1,'message' => 'Invalid Authorization header']);
            }
            $encodedCredentials = substr($authHeader, 6);
            $decodedCredentials = base64_decode($encodedCredentials);
            list($username, $password) = explode(":", $decodedCredentials, 2);
            $wallet_id = $request->wallet_id;
            if ($username && $password) {
                
                $user = User::where('email', $username)->has('wallet')->first();
                if (!empty($user)) {
                    if($user->wallet->wallet_id == $wallet_id) {
                        if (Hash::check($password, $user->password)) {
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
                                'message' => 'Invalid API Password'
                            ]);
                        }
                    }else{
                        return response()->json([
                            'status' => 1,
                            'message' => 'Invalid API Wallet ID'
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => 1,
                        'message' => 'Invalid API Username for Wallet Account'
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 1,
                    'message' => 'UserName and Password are required.'
                ]);
            }
        }


    }
}
