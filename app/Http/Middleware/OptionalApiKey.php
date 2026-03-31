<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OptionalApiKey
{
    /**
     * Handle an incoming request.
     *
     * If the X-API-KEY header is present and matches the configured API_KEY,
     * marks the request as having private (authenticated) access.
     * If the key is present but does NOT match, returns HTTP 401.
     * If no key is provided, the request continues as a public (unauthenticated) request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey    = config('app.api_key');
        $headerKey = $request->header('X-API-KEY');

        if (!empty($headerKey)) {
            // Key was provided – validate it
            if (empty($apiKey) || $headerKey !== $apiKey) {
                return response()->json([
                    'message' => 'Unauthorized. Invalid or missing API key.',
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Valid key: mark as private access
            $request->attributes->set('is_private_access', true);
        }
        // No key provided: continue as public (is_private_access defaults to false)

        return $next($request);
    }
}
