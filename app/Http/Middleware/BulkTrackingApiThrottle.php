<?php

namespace App\Http\Middleware;

use App\Http\Models\Shipment;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;

class BulkTrackingApiThrottle
{
    protected $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    public function handle(Request $request, Closure $next)
    {
        $key = 'tracking:' . $request->ip();
        $trackingNumbers = array_filter(explode(',', $request->tracking_numbers));

        if (empty($trackingNumbers)) {
            return response()->json(['message' => 'Invalid tracking numbers provided.'], 400);
        }

        $countValidTrackingNumber = Shipment::whereIn('tracking_number', $trackingNumbers)->count();
        $timeLimit = $this->getRateLimit($countValidTrackingNumber);

        if ($this->limiter->tooManyAttempts($key, 1)) {
            return response()->json([
                'message' => 'Too many requests. Please try again in ' . $this->limiter->availableIn($key) . ' seconds.'
            ], 429);
        }

        if ($timeLimit > 0) {
            $this->limiter->hit($key, $timeLimit);
        }

        return $next($request);
    }

    protected function getRateLimit($count)
    {
        if ($count >= 100 && $count < 150) {
            return 5; // 5-minute restriction for 100-150 bookings
        } elseif ($count >= 150 && $count < 200) {
            return 10; // 10-minute restriction for 150-200 bookings
        } elseif ($count >= 200 && $count <= 300) {
            return 15; // 15-minute restriction for 200-300 bookings
        }

        return 0;
    }
}
