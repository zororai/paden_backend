<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckHousingContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $context
     */
    public function handle(Request $request, Closure $next, string $context): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        if ($user->housing_context !== $context) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied. This endpoint is for ' . $context . ' housing users only.'
            ], 403);
        }

        return $next($request);
    }
}
