<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExecutiveAccessMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Authentication required.');
        }

        if ($user->isAdmin()) {
            return $next($request);
        }

        $hasActiveAssignment = $user->leadershipAssignments()
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('starts_at')->orWhereDate('starts_at', '<=', now()->toDateString());
            })
            ->where(function ($query) {
                $query->whereNull('ends_at')->orWhereDate('ends_at', '>=', now()->toDateString());
            })
            ->exists();

        abort_unless($hasActiveAssignment, 403, 'Executive access required.');

        return $next($request);
    }
}
