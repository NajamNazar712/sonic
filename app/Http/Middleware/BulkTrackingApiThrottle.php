<?php

namespace App\Http\Middleware;

use App\Http\Models\Shipment;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;

class bulkTrackingApiThrottle
{
    protected $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    public function handle(Request $request, Closure $next)
    {
        $key = 'tracking:' . $request->ip();
        $trackingNumbers = explode(',', $request->tracking_numbers);
        $validTrackingNumber = Shipment::whereIn('tracking_number', array_filter($trackingNumbers))->count();
        $count = $validTrackingNumber;
        $timeLimit = 0;

        if ($count >= 100 && $count < 150) {
            $timeLimit = 5; // 5-minute restriction for 100-150 bookings or tracking
        } elseif ($count >= 150 && $count < 200) {
            $timeLimit = 10; // 10-minute restriction for 150-200 bookings or tracking
        } elseif ($count >= 200 && $count <= 300) {
            $timeLimit = 15;
        }

        if ($this->limiter->availableIn($key) > 0) {
            return response()->json(['message' => 'Too many requests. Please try again in ' . $this->limiter->availableIn($key) . ' seconds.'], 429);
        }

        $this->limiter->hit($key, $timeLimit);

        return $next($request);
    }
}
