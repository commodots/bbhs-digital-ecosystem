<?php

namespace App\Http\Controllers;

use App\Models\AlumniProfile;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AlumniDirectoryController extends Controller
{
    public function index(Request $request)
    {
        $query = AlumniProfile::query()
            ->where('approved', true)
            ->where('directory_visible', true);

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%'.$search.'%')
                  ->orWhere('last_name', 'like', '%'.$search.'%');
            });
        }

        if ($request->filled('class_set')) {
            $query->where('class_set', 'like', '%'.$request->string('class_set').'%');
        }

        if ($request->filled('country')) {
            $query->where('country', 'like', '%'.$request->string('country').'%');
        }

        $alumni = $query->orderBy('last_name')->paginate(12)->withQueryString();

        return Inertia::render('AlumniDirectory', [
            'alumni' => $alumni,
            'filters' => $request->only(['search', 'class_set', 'country']),
        ]);
    }
}
