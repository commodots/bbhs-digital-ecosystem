<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WelfareTeamMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        abort_unless($user && ($user->isAdmin() || (bool) $user->welfare_team), 403, 'Welfare team access required.');
        return $next($request);
    }
}
