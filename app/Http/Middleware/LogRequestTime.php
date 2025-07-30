<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogRequestTime
{
    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);

        $response = $next($request);

        $end = microtime(true);
        $duration = round(($end - $start) * 1000, 2); // in ms

        Log::info("⏱️ Request to {$request->path()} took {$duration} ms");

        return $response;
    }
}