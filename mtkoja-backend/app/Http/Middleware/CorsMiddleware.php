<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $origin = $request->headers->get('Origin');
        $allowOrigin = $this->resolveAllowedOrigin($origin);

        $headers = [
            'Access-Control-Allow-Origin' => $allowOrigin,
            'Access-Control-Allow-Methods' => $allowOrigin ? 'GET, POST, PUT, DELETE, OPTIONS, PATCH' : null,
            'Access-Control-Allow-Headers' => $allowOrigin ? 'Content-Type, Authorization, X-Requested-With, Accept, Origin, X-CSRF-TOKEN' : null,
            'Access-Control-Allow-Credentials' => $allowOrigin ? 'true' : null,
            'Access-Control-Max-Age' => $allowOrigin ? '86400' : null,
            'Vary' => 'Origin',
        ];

        if ($request->getMethod() === 'OPTIONS') {
            if (!$allowOrigin) {
                return response()->json(['message' => 'CORS origin not allowed'], 403);
            }

            $preflight = response('', 204);
            foreach ($headers as $key => $value) {
                if ($value !== null) {
                    $preflight->headers->set($key, $value);
                }
            }

            return $preflight;
        }

        $response = $next($request);

        foreach ($headers as $key => $value) {
            if ($value !== null) {
                $response->headers->set($key, $value);
            }
        }

        return $response;
    }

    private function resolveAllowedOrigin(?string $origin): ?string
    {
        if (!$origin) {
            return null;
        }

        $allowedOrigins = config('cors.allowed_origins', []);
        $allowedPatterns = config('cors.allowed_origins_patterns', []);

        if (in_array($origin, $allowedOrigins, true)) {
            return $origin;
        }

        foreach ($allowedPatterns as $pattern) {
            if (@preg_match($pattern, $origin) === 1) {
                return $origin;
            }
        }

        return null;
    }
}
