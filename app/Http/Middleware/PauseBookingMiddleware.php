<?php
namespace App\Http\Middleware;
use Closure;
class PauseBookingMiddleware
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
        if(session('status') == 6)
        {
            return redirect()->route('cod.wordpress_access_denied');
        }
        else
        {
            return $next($request);
        }
        
    }
}