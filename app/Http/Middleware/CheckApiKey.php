<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiKey
{
    /**
     * Handle an incoming request.
     *
     * Reads the X-API-KEY header and compares it to the configured API_KEY
     * environment variable. If it matches, marks the request as having
     * private access so controllers and services can distinguish public vs.
     * private context. If the key is missing or invalid, returns HTTP 401.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = config('app.api_key');
        $headerKey = $request->header('X-API-KEY');

        if (empty($apiKey) || $headerKey !== $apiKey) {
            return response()->json([
                'message' => 'Unauthorized. Invalid or missing API key.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Mark the request as having private (authenticated) access.
        // Controllers and services can check $request->attributes->get('is_private_access').
        $request->attributes->set('is_private_access', true);

        return $next($request);
    }
}
