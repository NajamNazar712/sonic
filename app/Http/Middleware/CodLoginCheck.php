<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CodLoginCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $routeName = $request->route()->getName();

        // Only check for login routes (adjust names as needed)
        $loginRoutes = ['cod.login.submit','retail.login.submit','admin.login.submit'];

        if (in_array($routeName, $loginRoutes)) {
            $guards = ['web', 'substitute_users', 'admin', 'retail', 'agent'];

            foreach ($guards as $guard) {
                if (Auth::guard($guard)->check()) {
                    return redirect()->back()->with(
                        'error',
                        "You are already logged in as {$guard}. Please log out first."
                    );
                }
            }
        }

        return $next($request);
    }
}
