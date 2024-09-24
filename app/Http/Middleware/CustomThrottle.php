<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;

class CustomThrottle
{
    protected $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    public function handle(Request $request, Closure $next)
    {
        $key = 'custom_throttle:' . $request->ip();
        $trackingNumbers = explode(',', $request->tracking_numbers);
        $count = count($trackingNumbers);
        $timeLimit = 0;

        if ($count >= 1 && $count < 150) {
            $timeLimit = 1; // 5-minute restriction for 100-150 bookings or tracking
        } elseif ($count >= 150 && $count < 200) {
            $timeLimit = 10; // 10-minute restriction for 150-200 bookings or tracking
        } elseif ($count >= 200 && $count <= 300) {
            $timeLimit = 15; // 15-minute restriction for 200-300 bookings or tracking
        }

        \Log::info("Attempts for key {$key}: {$this->limiter->attempts($key)} at " . now());

        if ($this->limiter->attempts($key) > 5 && $this->limiter->availableIn($key) > 0) {
            return response()->json(['message' => 'Too many requests. Please try again in ' . $this->limiter->availableIn($key) . ' seconds.'], 429);
        }

        $this->limiter->hit($key, $timeLimit);

        return $next($request);
    }
}
