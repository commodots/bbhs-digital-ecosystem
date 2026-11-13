<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AlumniProfile;
use App\Models\Branch;
use App\Models\BranchCity;
use App\Models\GraduationSet;
use App\Models\LeadershipAssignment;
use App\Models\MemberBranch;
use App\Models\MembershipAuditLog;
use App\Models\MembershipVerification;
use App\Models\User;
use App\Notifications\BbhsPlatformNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class MemberRegistrationController extends Controller
{
    public function create()
    {
        return inertia('Register', [
            'graduationSets' => GraduationSet::where('is_active', true)
                ->orderBy('graduation_year')
                ->get(['id', 'graduation_year', 'name']),
            'branches' => Branch::where('is_active', true)
                ->with('cities:id,branch_id,city,country')
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:40'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'whatsapp_phone' => ['nullable', 'string', 'max:40'],
            'sms_consent' => ['sometimes', 'boolean'],
            'whatsapp_consent' => ['sometimes', 'boolean'],
            'graduation_set_id' => ['required', 'integer', Rule::exists('graduation_sets', 'id')->where('is_active', true)],
            'branch_id' => ['nullable', 'integer', Rule::exists('branches', 'id')->where('is_active', true)],
            'city' => ['required', 'string', 'max:150'],
            'country' => ['required', 'string', 'max:150'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $city = trim($validated['city']);
        $country = trim($validated['country']);
        $cityNormalized = Str::lower($city);
        $countryNormalized = Str::lower($country);

        $matchingBranches = BranchCity::query()
            ->where('city_normalized', $cityNormalized)
            ->where('country_normalized', $countryNormalized)
            ->whereHas('branch', fn ($q) => $q->where('is_active', true))
            ->get();

        if (! empty($validated['branch_id']) && ! $matchingBranches->contains('branch_id', (int) $validated['branch_id'])) {
            return back()->withErrors([
                'branch_id' => 'The selected branch does not cover the city and country entered. You can leave branch affiliation blank.',
            ])->withInput();
        }

        $set = GraduationSet::findOrFail($validated['graduation_set_id']);
        $user = null;

        $photoPath = $request->hasFile('profile_photo')
            ? $request->file('profile_photo')->store('member-photos', 'public')
            : null;

        DB::transaction(function () use ($validated, $city, $country, $cityNormalized, $countryNormalized, $matchingBranches, $set, &$user, $request, $photoPath) {
            $user = User::forceCreate([
                'name' => trim($validated['first_name'] . ' ' . $validated['last_name']),
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'member',
                'member_status' => 'pending',
                'is_admin' => false,
            ]);

            \App\Models\MembershipDuesAccount::create([
                'user_id' => $user->id, 'commencement_date' => $user->created_at?->toDateString() ?? today(),
                'plan' => 'annual', 'annual_amount_kobo' => 1250000, 'monthly_amount_kobo' => 105000,
                'next_due_at' => $user->created_at?->toDateString() ?? today(), 'status' => 'due',
            ]);

            AlumniProfile::create([
                'user_id' => $user->id,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'class_set' => $set->name,
                'graduation_set_id' => $set->id,
                'phone' => $validated['phone'],
                'photo' => $photoPath,
                'whatsapp_phone' => $validated['whatsapp_phone'] ?? null,
                'sms_consent' => (bool)($validated['sms_consent'] ?? false),
                'whatsapp_consent' => (bool)($validated['whatsapp_consent'] ?? false),
                'preferred_contact' => 'whatsapp',
                'city' => $city,
                'city_normalized' => $cityNormalized,
                'country' => $country,
                'country_normalized' => $countryNormalized,
                'approved' => false,
                'directory_visible' => false,
            ]);

            MembershipVerification::create([
                'user_id' => $user->id,
                'verification_type' => 'set',
                'scope_type' => 'set',
                'scope_id' => $set->id,
                'status' => 'pending',
            ]);

            if (! empty($validated['branch_id'])) {
                MemberBranch::create([
                    'user_id' => $user->id,
                    'branch_id' => (int) $validated['branch_id'],
                    'status' => 'pending',
                    'is_primary' => true,
                    'requested_at' => now(),
                ]);
            }

            // City matching makes a local registrant visible to the relevant branch
            // verification queue, but does NOT make the member a branch affiliate.
            foreach ($matchingBranches as $mapping) {
                MembershipVerification::firstOrCreate([
                    'user_id' => $user->id,
                    'verification_type' => 'branch',
                    'scope_type' => 'branch',
                    'scope_id' => $mapping->branch_id,
                ], ['status' => 'pending']);
            }

            MembershipAuditLog::create([
                'user_id' => $user->id,
                'actor_id' => null,
                'action' => 'member_registered',
                'scope_type' => 'set',
                'scope_id' => $set->id,
                'details' => [
                    'city' => $city,
                    'country' => $country,
                    'branch_affiliation_requested' => ! empty($validated['branch_id']),
                ],
                'ip_address' => $request->ip(),
            ]);
        });

        $this->notifySetAndBranchExecutives($set, $matchingBranches, $user);

        return redirect()->route('login')->with(
            'success',
            'Registration received. Your graduation set must approve your membership before you can access the alumni directory. Branch verification is separate and does not affect set approval.'
        );
    }

    private function notifySetAndBranchExecutives(GraduationSet $set, $branches, User $newMember): void
    {
        $assignments = LeadershipAssignment::query()
            ->where('is_active', true)
            ->where(function ($q) use ($set, $branches) {
                $q->where(function ($inner) use ($set) {
                    $inner->where('scope_type', 'set')->where('scope_id', $set->id);
                });

                foreach ($branches as $branch) {
                    $q->orWhere(function ($inner) use ($branch) {
                        $inner->where('scope_type', 'branch')->where('scope_id', $branch->branch_id);
                    });
                }
            })
            ->with('user:id,name,email')
            ->get();

        $recipients = $assignments->pluck('user')->filter()->unique('id')->values();

        // In-app notification for the assigned Set and Branch executives.
        foreach ($recipients as $recipient) {
            $recipient->notify(new BbhsPlatformNotification(
                'New membership registration',
                $newMember->name . ' has registered and is awaiting your review for ' . $set->name . '.',
                'membership',
                '/admin/verifications',
                ['member_id' => $newMember->id, 'graduation_set_id' => $set->id]
            ));
        }

        // Super Admins also receive a platform-level heads-up.
        User::query()->where(function ($query) {
            $query->where('is_admin', true)->orWhere('role', 'admin');
        })->whereNotIn('id', $recipients->pluck('id'))->get()->each(function ($admin) use ($newMember, $set) {
            $admin->notify(new BbhsPlatformNotification(
                'New member registration',
                $newMember->name . ' registered in ' . $set->name . ' and is awaiting approval.',
                'membership',
                '/admin/verifications',
                ['member_id' => $newMember->id, 'graduation_set_id' => $set->id]
            ));
        });

        $emails = $recipients->pluck('email')->filter()->unique()->values();

        foreach ($emails as $email) {
            try {
                Mail::raw(
                    "A new BBHS member registration requires your attention.\n\nName: {$newMember->name}\nEmail: {$newMember->email}\nGraduation set: {$set->name}\n\nPlease sign in to the BBHS portal and review the pending registration.",
                    function ($message) use ($email) {
                        $message->to($email)->subject('BBHS: New member registration awaiting review');
                    }
                );
            } catch (\Throwable $exception) {
                Log::warning('BBHS registration notification could not be sent.', [
                    'recipient' => $email,
                    'exception' => $exception->getMessage(),
                ]);
            }
        }
    }
}
