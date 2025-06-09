<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$rolesAllowed)
    {
        // Grab the array of role names stored in session
        $userRoles = $request->session()->get('roles', []);

        // Normalize role names for comparison
        $userRoles = array_map('strtolower', $userRoles);

        $allowedList = array_map('strtolower', $rolesAllowed);
        // dd($userRoles, $allowedList);

        // If there is no intersection, abort 403
        if (count(array_intersect($allowedList, $userRoles)) === 0) {
            abort(403, 'Unauthorized');
        }
        if (!in_array('chef department', $userRoles) &&
            !in_array('director', $userRoles) &&
            !in_array('division staff', $userRoles) &&
            !in_array('revenue manager', $userRoles)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
