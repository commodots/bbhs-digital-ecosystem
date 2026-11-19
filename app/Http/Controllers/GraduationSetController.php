<?php

namespace App\Http\Controllers;

use App\Models\GraduationSet;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class GraduationSetController extends Controller
{
    /**
     * Public graduation-set page.
     *
     * Only approved members who opted into directory visibility and public
     * graduation-set visibility are included. Individual fields continue to
     * respect each member's privacy settings.
     */
    public function show(GraduationSet $graduationSet)
    {
        abort_unless($graduationSet->is_active, 404);

        $members = $graduationSet->alumniProfiles()
            ->where('approved', true)
            ->where('directory_visible', true)
            ->where('public_graduation_set', true)
            ->whereHas('user', fn ($q) => $q->where('member_status', 'approved'))
            ->with('user:id,name,email')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get()
            ->map(function ($profile) {
                $photo = $profile->public_photo ? $profile->photo : null;
                if ($photo && ! str_starts_with($photo, 'http') && ! str_starts_with($photo, '/')) {
                    $photo = Storage::disk('public')->url($photo);
                }

                $name = $profile->public_name
                    ? (trim(($profile->first_name ?: '') . ' ' . ($profile->last_name ?: '')) ?: $profile->user?->name)
                    : 'BBHS Alumni';

                return [
                    'id' => $profile->id,
                    'name' => $name,
                    'photo' => $photo,
                    'profession' => $profile->public_profession ? $profile->profession : null,
                    'city' => $profile->public_location ? $profile->city : null,
                    'country' => $profile->public_location ? $profile->country : null,
                    'bio' => $profile->public_bio ? $profile->bio : null,
                    'phone' => $profile->phone ? $this->maskPhone($profile->phone) : null,
                    'email' => $profile->user?->email ? $this->maskEmail($profile->user->email) : null,
                ];
            })
            ->values();

        return Inertia::render('AlumniSet', [
            'graduationSet' => $graduationSet,
            'members' => $members,
        ]);
    }

    private function maskPhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);
        if (strlen($digits) < 5) {
            return '••••';
        }

        return substr($digits, 0, 3)
            . str_repeat('*', max(3, strlen($digits) - 5))
            . substr($digits, -2);
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');
        if ($domain === '') {
            return '••••';
        }

        return substr($local, 0, 2)
            . str_repeat('*', max(2, strlen($local) - 2))
            . '@' . $domain;
    }
}
