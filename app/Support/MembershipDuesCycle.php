<?php

namespace App\Support;

use App\Models\MembershipDuesAccount;
use Carbon\Carbon;

class MembershipDuesCycle
{
    public static function nextUnpaidStart(MembershipDuesAccount $account, string $period): Carbon
    {
        $start = $account->paid_through
            ? $account->paid_through->copy()->addDay()->startOfDay()
            : Carbon::parse($account->commencement_date ?? today())->startOfDay();

        // Preserve the existing annual renewal behaviour. Monthly dues are
        // anchored to the original registration anniversary and pay the
        // oldest unpaid month rather than skipping outstanding months.
        if ($period === 'annual') {
            while ($start->copy()->addYearNoOverflow()->lte(today())) {
                $start->addYearNoOverflow();
            }
        }

        return $start;
    }

    public static function periodEnd(MembershipDuesAccount $account, Carbon $periodStart, string $period): Carbon
    {
        $anchor = Carbon::parse($account->commencement_date ?? $periodStart)->startOfDay();
        if ($period === 'annual') {
            $years = max(1, $anchor->diffInYears($periodStart) + 1);
            do {
                $year = $anchor->year + $years++;
                $day = min($anchor->day, Carbon::create($year, $anchor->month, 1)->daysInMonth);
                $next = Carbon::create($year, $anchor->month, $day)->startOfDay();
            } while ($next->lte($periodStart));
            return $next->subDay();
        }

        $months = (($periodStart->year - $anchor->year) * 12)
            + ($periodStart->month - $anchor->month) + 1;
        $months = max(1, $months);
        do {
            $target = $anchor->copy()->startOfMonth()->addMonths($months++);
            $day = min($anchor->day, $target->daysInMonth);
            $next = $target->copy()->day($day)->startOfDay();
        } while ($next->lte($periodStart));
        return $next->subDay();
    }
}
