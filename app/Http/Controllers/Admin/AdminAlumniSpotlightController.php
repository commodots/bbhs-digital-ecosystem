<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlumniProfile;
use App\Models\AlumniSpotlight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AdminAlumniSpotlightController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Spotlights/Index', [
            'spotlights' => AlumniSpotlight::with('alumniProfile.graduationSet')->orderBy('sort_order')->orderBy('id')->get(),
            'members' => AlumniProfile::with('graduationSet')->where('approved', true)->whereHas('user', fn ($q) => $q->where('member_status', 'approved'))->orderBy('last_name')->orderBy('first_name')->get(['id','first_name','last_name','photo','graduation_set_id','class_set']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'alumni_profile_id' => ['required','integer','exists:alumni_profiles,id','unique:alumni_spotlights,alumni_profile_id'],
            'headline' => ['nullable','string','max:160'],
            'writeup' => ['required','string','max:1500'],
            'sort_order' => ['nullable','integer','min:0','max:9999'],
            'is_active' => ['sometimes','boolean'],
        ]);
        $profile = AlumniProfile::with('user')->findOrFail($data['alumni_profile_id']);
        abort_unless($profile->approved && $profile->user?->member_status === 'approved', 422);
        AlumniSpotlight::create($data + ['sort_order' => $data['sort_order'] ?? 0, 'is_active' => $request->boolean('is_active', true)]);
        return back()->with('success', 'Alumni spotlight added.');
    }

    public function update(Request $request, AlumniSpotlight $spotlight)
    {
        $data = $request->validate(['headline'=>['nullable','string','max:160'],'writeup'=>['required','string','max:1500'],'sort_order'=>['required','integer','min:0','max:9999'],'is_active'=>['required','boolean']]);
        $spotlight->update($data);
        return back()->with('success', 'Spotlight updated.');
    }

    public function destroy(AlumniSpotlight $spotlight)
    {
        $spotlight->delete();
        return back()->with('success', 'Spotlight removed.');
    }
}
