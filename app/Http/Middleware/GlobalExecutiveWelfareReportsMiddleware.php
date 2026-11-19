<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Allows report-only access to Super Admins, Welfare Team and Global Executives. */
class GlobalExecutiveWelfareReportsMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        $allowed = $user && (
            $user->isAdmin()
            || (bool) $user->welfare_team
            || $user->hasActiveGlobalExecutiveAssignment()
        );

        abort_unless($allowed, 403, 'Welfare reports access required.');

        return $next($request);
    }
}
