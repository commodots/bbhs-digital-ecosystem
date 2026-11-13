<?php

namespace Database\Seeders;

use App\Models\AlumniProfile;
use App\Models\Branch;
use App\Models\ExecutivePosition;
use App\Models\GraduationSet;
use App\Models\LeadershipAssignment;
use App\Models\MemberBranch;
use App\Models\MembershipAuditLog;
use App\Models\MembershipVerification;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BbhsDemoDataSeeder extends Seeder
{
    private const PASSWORD = 'BBHSDemo#2026';

    public function run(): void
    {
        $set83 = GraduationSet::where('graduation_year', 1983)->firstOrFail();
        $set85 = GraduationSet::where('graduation_year', 1985)->firstOrFail();
        $set90 = GraduationSet::where('graduation_year', 1990)->firstOrFail();
        $lagos = Branch::where('slug', 'lagos-branch')->firstOrFail();

        // Approved demo members used to demonstrate executive appointments.
        $setPresident = $this->makeMember('set-president.83', 'Demo Set President', '83', $set83, 'Lagos', 'Nigeria', true);
        $setSecretary = $this->makeMember('set-secretary.83', 'Demo Set Secretary', '83', $set83, 'Lagos', 'Nigeria', true);
        $branchPresident = $this->makeMember('branch-president.lagos', 'Demo Lagos President', '90', $set90, 'Lagos', 'Nigeria', true);
        $branchSecretary = $this->makeMember('branch-secretary.lagos', 'Demo Lagos Secretary', '85', $set85, 'Lagos', 'Nigeria', true);

        $this->assignIfSlotIsFree($setPresident, 'set', $set83->id, 'set-president');
        $this->assignIfSlotIsFree($setSecretary, 'set', $set83->id, 'set-secretary');
        $this->assignIfSlotIsFree($branchPresident, 'branch', $lagos->id, 'branch-president');
        $this->assignIfSlotIsFree($branchSecretary, 'branch', $lagos->id, 'branch-secretary');

        MemberBranch::updateOrCreate(
            ['user_id' => $branchPresident->id, 'branch_id' => $lagos->id],
            ['status' => 'approved', 'is_primary' => true, 'requested_at' => now(), 'reviewed_at' => now()]
        );
        MemberBranch::updateOrCreate(
            ['user_id' => $branchSecretary->id, 'branch_id' => $lagos->id],
            ['status' => 'approved', 'is_primary' => true, 'requested_at' => now(), 'reviewed_at' => now()]
        );

        // Pending applications demonstrate set approval and independent local verification.
        $pendingA = $this->makeMember('pending.83.lagos', 'Demo Pending Lagos 83', '83', $set83, 'Lagos', 'Nigeria', false);
        $this->makePendingVerification($pendingA, 'set', $set83->id);
        $this->makePendingVerification($pendingA, 'branch', $lagos->id);
        MemberBranch::updateOrCreate(
            ['user_id' => $pendingA->id, 'branch_id' => $lagos->id],
            ['status' => 'pending', 'is_primary' => true, 'requested_at' => now()]
        );

        $pendingB = $this->makeMember('pending.83.no-branch', 'Demo Pending 83 No Branch', '83', $set83, 'Abuja', 'Nigeria', false);
        $this->makePendingVerification($pendingB, 'set', $set83->id);

        $pendingC = $this->makeMember('pending.90.lagos', 'Demo Pending Lagos 90', '90', $set90, 'Lagos', 'Nigeria', false);
        $this->makePendingVerification($pendingC, 'set', $set90->id);
        $this->makePendingVerification($pendingC, 'branch', $lagos->id);
        // No member_branches row: this applicant has not requested formal branch membership.

        $ibadan = Branch::where('slug', 'ibadan-branch')->first();
        $pendingD = $this->makeMember('pending.85.ibadan', 'Demo Pending Ibadan 85', '85', $set85, 'Ibadan', 'Nigeria', false);
        $this->makePendingVerification($pendingD, 'set', $set85->id);
        if ($ibadan) {
            $this->makePendingVerification($pendingD, 'branch', $ibadan->id);
        }

        $this->command?->info('BBHS demo data seeded. All demo accounts use password: ' . self::PASSWORD);
        $this->command?->line('Demo accounts use the email domain @bbhs-demo.test and can be removed with: php artisan bbhs:demo-cleanup');
    }

    private function makeMember(string $key, string $name, string $setLabel, GraduationSet $set, string $city, string $country, bool $approved): User
    {
        $email = 'demo.' . $key . '@bbhs-demo.test';
        $parts = explode(' ', $name, 2);

        $user = User::firstOrNew(['email' => $email]);
        $user->forceFill([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make(self::PASSWORD),
            'role' => 'member',
            'member_status' => $approved ? 'approved' : 'pending',
            'is_admin' => false,
        ]);
        $user->save();

        AlumniProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'first_name' => $parts[0],
                'last_name' => $parts[1] ?? 'Demo',
                'class_set' => $set->name,
                'graduation_set_id' => $set->id,
                'city' => $city,
                'city_normalized' => mb_strtolower(trim($city)),
                'country' => $country,
                'country_normalized' => mb_strtolower(trim($country)),
                'approved' => $approved,
                'directory_visible' => $approved,
                'bio' => 'Demonstration account only. Safe to remove using the BBHS demo cleanup command.',
            ]
        );

        if ($approved) {
            MembershipVerification::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'verification_type' => 'set',
                    'scope_type' => 'set',
                    'scope_id' => $set->id,
                ],
                ['status' => 'approved', 'reviewed_at' => now(), 'review_notes' => 'Demo seed approval']
            );
        }

        MembershipAuditLog::create([
            'user_id' => $user->id,
            'actor_id' => null,
            'action' => 'demo_account_seeded',
            'scope_type' => 'set',
            'scope_id' => $set->id,
            'details' => ['demo' => true, 'approved' => $approved],
        ]);

        return $user;
    }

    private function makePendingVerification(User $user, string $type, int $scopeId): void
    {
        MembershipVerification::updateOrCreate(
            [
                'user_id' => $user->id,
                'verification_type' => $type,
                'scope_type' => $type,
                'scope_id' => $scopeId,
            ],
            ['status' => 'pending', 'reviewed_by' => null, 'reviewed_at' => null, 'review_notes' => null]
        );
    }

    private function assignIfSlotIsFree(User $user, string $scopeType, int $scopeId, string $positionCode): void
    {
        $position = ExecutivePosition::where('scope_type', $scopeType)
            ->where('code', $positionCode)
            ->where('is_active', true)
            ->first();

        if (! $position) {
            return;
        }

        $slotOccupied = LeadershipAssignment::where('scope_type', $scopeType)
            ->where('scope_id', $scopeId)
            ->where('executive_position_id', $position->id)
            ->where('is_active', true)
            ->exists();

        if (! $slotOccupied) {
            LeadershipAssignment::create([
                'user_id' => $user->id,
                'executive_position_id' => $position->id,
                'scope_type' => $scopeType,
                'scope_id' => $scopeId,
                'starts_at' => now()->toDateString(),
                'is_active' => true,
                'notes' => 'DEMO DATA — remove with php artisan bbhs:demo-cleanup',
            ]);
        }
    }
}
