<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmasMarkerMiddleware
{
    /**
     * Handle an incoming request.
     * Ensures only EMAS users with 'marker' role can access marker routes
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->guard('emas')->check()) {
            return redirect()->route('emas.login');
        }

        $user = auth()->guard('emas')->user();
        
        if (!$user->isMarker()) {
            abort(403, 'Unauthorized. Only markers can access this area.');
        }

        return $next($request);
    }
}
