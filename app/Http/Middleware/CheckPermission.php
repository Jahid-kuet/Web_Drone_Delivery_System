<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        // Check if user is authenticated
        if (!$request->user()) {
            return redirect()->route('login')
                ->with('error', 'Please login to access this page.');
        }
        
        // Check if user has any of the required permissions
        if (!$request->user()->hasAnyPermission($permissions)) {
            abort(403, 'Unauthorized. You do not have the required permission to access this page.');
        }
        
        return $next($request);
    }
}
