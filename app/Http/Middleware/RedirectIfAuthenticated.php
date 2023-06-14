<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        switch ($guard){
            case 'admin':
                if(Auth::guard($guard)->check()){
                    return redirect()->route('admin.dashboard.index');
                }
                break;
            case 'retail':
                if(Auth::guard($guard)->check()){
                    return redirect()->route('retail.shipment.book.index');
                }
                break;

            case 'agent':
                if(Auth::guard($guard)->check()){
                    return redirect()->route('agent.dashboard.index');
                }
                break;
            default:
                if (Auth::guard($guard)->check()) {
                    return redirect()->route('cod.dashboard');
                }
                break;

        }

        return $next($request);
    }
}
