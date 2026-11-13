<?php

namespace App\\Http\\Controllers;

use App\\Models\\Branch;
use Inertia\\Inertia;

class ChaptersController extends Controller
{
    public function index()
    {
        $branches = Branch::query()
            ->where('is_active', true)
            ->with(['cities:id,branch_id,city,country'])
            ->orderBy('name')
            ->get(['id', 'name', 'description'])
            ->map(fn (Branch $branch) => [
                'id' => $branch->id,
                'name' => $branch->name,
                'location' => $branch->cities
                    ->map(fn ($city) => $city->city . ', ' . $city->country)
                    ->unique()
                    ->implode(' • ') ?: ($branch->description ?: 'Location details to be confirmed'),
                'note' => 'Connect with fellow Old Boys, participate in Association activities and stay involved with the BBHS community.',
            ])
            ->values();

        return Inertia::render('Chapters', [
            'chapters' => $branches,
        ]);
    }
}
