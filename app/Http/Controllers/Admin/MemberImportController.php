<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlumniProfile;
use App\Models\Branch;
use App\Models\GraduationSet;
use App\Models\LeadershipAssignment;
use App\Models\MemberBranch;
use App\Models\MembershipAuditLog;
use App\Models\MembershipDuesAccount;
use App\Models\MembershipVerification;
use App\Models\User;
use App\Notifications\BbhsPlatformNotification;
use App\Notifications\MemberWelcomePasswordSetup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class MemberImportController extends Controller
{
    public function index(Request $request)
    {
        $scope = $this->scopeFor($request);
        return Inertia::render('Admin/Members/Import', [
            'scope' => $scope,
            'sets' => GraduationSet::where('is_active', true)->orderBy('graduation_year')->get(['id', 'name', 'graduation_year']),
            'branches' => Branch::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function template()
    {
        $csv = "first_name,last_name,email,phone,set,branch\nJohn,Doe,john@example.com,08012345678,1995,Lagos\n";
        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="bbhs-members-template.csv"',
        ]);
    }

    public function preview(Request $request)
    {
        $scope = $this->scopeFor($request);
        $request->validate(['csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:5120']]);

        $path = $request->file('csv_file')->store('member-imports', 'local');
        $fullPath = Storage::disk('local')->path($path);
        $handle = fopen($fullPath, 'r');
        if (! $handle) {
            Storage::disk('local')->delete($path);
            return back()->withErrors(['csv_file' => 'The uploaded CSV could not be read.']);
        }

        $header = fgetcsv($handle);
        $header = array_map(fn ($v) => strtolower(trim(preg_replace('/^\xEF\xBB\xBF/', '', (string) $v))), $header ?: []);
        $required = ['first_name', 'last_name', 'email', 'phone', 'set', 'branch'];
        $missing = array_values(array_diff($required, $header));
        if ($missing) {
            fclose($handle);
            Storage::disk('local')->delete($path);
            return back()->withErrors(['csv_file' => 'Missing CSV columns: ' . implode(', ', $missing)]);
        }

        $rows = [];
        $line = 1;
        while (($values = fgetcsv($handle)) !== false) {
            $line++;
            if (count($rows) >= 500) break;
            if (count(array_filter($values, fn ($v) => trim((string) $v) !== '')) === 0) continue;
            $row = [];
            foreach ($required as $key) {
                $idx = array_search($key, $header, true);
                $row[$key] = trim((string) ($values[$idx] ?? ''));
            }
            $row['_line'] = $line;
            $row['_issues'] = $this->validateRow($row, $scope);
            $rows[] = $row;
        }
        fclose($handle);

        $token = (string) Str::uuid();
        Storage::disk('local')->put('member-import-previews/'.$token.'.json', json_encode([
            'path' => $path, 'scope' => $scope, 'created_by' => $request->user()->id,
            'rows' => $rows, 'created_at' => now()->toIso8601String(),
        ]));

        return Inertia::render('Admin/Members/Import', [
            'scope' => $scope,
            'sets' => GraduationSet::where('is_active', true)->orderBy('graduation_year')->get(['id', 'name', 'graduation_year']),
            'branches' => Branch::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'preview' => ['token' => $token, 'rows' => $rows, 'valid_count' => collect($rows)->where('_issues', [])->count()],
        ]);
    }

    public function confirm(Request $request)
    {
        $request->validate(['token' => ['required', 'uuid']]);
        $file = 'member-import-previews/'.$request->token.'.json';
        abort_unless(Storage::disk('local')->exists($file), 404, 'Import preview expired. Please upload the CSV again.');
        $data = json_decode(Storage::disk('local')->get($file), true);
        abort_unless(($data['created_by'] ?? null) === $request->user()->id, 403);
        abort_unless(now()->diffInMinutes($data['created_at']) <= 60, 410, 'Import preview expired. Please upload again.');

        $scope = $this->scopeFor($request);
        abort_unless($scope === $data['scope'], 403, 'Your administrator scope has changed. Please upload again.');

        $created = 0; $skipped = 0; $emailSent = 0; $emailFailed = 0; $results = [];
        foreach ($data['rows'] as $row) {
            $issues = $this->validateRow($row, $scope);
            if ($issues) {
                $skipped++;
                $results[] = ['line' => $row['_line'], 'email' => $row['email'], 'status' => 'Skipped', 'message' => implode('; ', $issues)];
                continue;
            }

            try {
                $user = DB::transaction(function () use ($row, $scope, $request) {
                    $set = $this->findSet($row['set']);
                    $branch = $this->findBranchForRow($row['branch'], $scope);
                    $user = User::create([
                        'name' => trim($row['first_name'].' '.$row['last_name']),
                        'email' => strtolower(trim($row['email'])),
                        'password' => Hash::make(Str::random(48)),
                        'role' => 'member', 'member_status' => 'pending', 'is_admin' => false,
                    ]);

                    AlumniProfile::create([
                        'user_id' => $user->id, 'first_name' => $row['first_name'], 'last_name' => $row['last_name'],
                        'class_set' => $set->name, 'graduation_set_id' => $set->id, 'phone' => $row['phone'],
                        'approved' => false, 'directory_visible' => false,
                    ]);

                    MembershipDuesAccount::firstOrCreate(['user_id' => $user->id], [
                        'commencement_date' => $user->created_at?->toDateString() ?? today(),
                        'plan' => 'annual', 'annual_amount_kobo' => 1250000, 'monthly_amount_kobo' => 105000,
                        'next_due_at' => $user->created_at?->toDateString() ?? today(), 'status' => 'due',
                    ]);

                    MembershipVerification::firstOrCreate([
                        'user_id' => $user->id, 'verification_type' => 'set', 'scope_type' => 'set', 'scope_id' => $set->id,
                    ], ['status' => 'pending']);

                    if ($branch) {
                        MemberBranch::firstOrCreate(['user_id' => $user->id, 'branch_id' => $branch->id], [
                            'status' => 'pending', 'is_primary' => true, 'requested_at' => now(),
                        ]);
                        MembershipVerification::firstOrCreate([
                            'user_id' => $user->id, 'verification_type' => 'branch', 'scope_type' => 'branch', 'scope_id' => $branch->id,
                        ], ['status' => 'pending']);
                    }

                    MembershipAuditLog::create([
                        'user_id' => $user->id, 'actor_id' => $request->user()->id, 'action' => 'member_imported',
                        'scope_type' => $scope['type'], 'scope_id' => $scope['id'],
                        'details' => ['source' => 'csv', 'set_id' => $set->id, 'branch_id' => $branch?->id],
                        'ip_address' => $request->ip(),
                    ]);
                    return $user;
                });

                // The member and related records are already committed at this point.
                // Notification failures must not turn a successfully created member into a failed import.
                try {
                    $this->notifyApprovers($user);
                } catch (\Throwable $notificationException) {
                    Log::warning('BBHS bulk import approver notification failed.', [
                        'member_id' => $user->id,
                        'email' => $user->email,
                        'exception' => $notificationException->getMessage(),
                    ]);
                }

                try {
                    $token = Password::broker()->createToken($user);
                    $user->notify(new MemberWelcomePasswordSetup($token));
                    $emailSent++;
                    $emailWasSent = true;
                } catch (\Throwable $mailException) {
                    $emailFailed++;
                    $emailWasSent = false;
                    Log::warning('BBHS bulk import welcome email was not sent.', ['email' => $user->email, 'exception' => $mailException->getMessage()]);
                }
                $created++;
                $results[] = ['line' => $row['_line'], 'email' => $user->email, 'status' => $emailWasSent ? 'Imported; email sent' : 'Imported; email failed', 'message' => $emailWasSent ? 'Welcome email with password setup link sent.' : 'Use Resend Password Link from the member record.'];
            } catch (\Throwable $e) {
                $skipped++;
                Log::error('BBHS CSV member import row failed.', ['row' => $row['_line'] ?? null, 'exception' => $e->getMessage()]);
                $results[] = ['line' => $row['_line'], 'email' => $row['email'], 'status' => 'Failed', 'message' => 'Could not create this member. Check application logs.'];
            }
        }

        Storage::disk('local')->delete($file);
        Storage::disk('local')->delete($data['path']);
        return Inertia::render('Admin/Members/ImportResult', [
            'summary' => ['created' => $created, 'skipped' => $skipped, 'email_sent' => $emailSent, 'email_failed' => $emailFailed],
            'results' => $results,
        ]);
    }

    private function scopeFor(Request $request): array
    {
        $actor = $request->user();
        if ($actor->isAdmin()) return ['type' => 'global', 'id' => null, 'label' => 'National Administration'];

        $assignments = $actor->leadershipAssignments()->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhereDate('starts_at', '<=', today()))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhereDate('ends_at', '>=', today()))
            ->get(['scope_type', 'scope_id']);
        $set = $assignments->firstWhere('scope_type', 'set');
        $branch = $assignments->firstWhere('scope_type', 'branch');

        if ($set) {
            $record = GraduationSet::find($set->scope_id);
            abort_unless($record, 403);
            return ['type' => 'set', 'id' => $record->id, 'label' => 'Set Administration: '.$record->name];
        }
        if ($branch) {
            $record = Branch::find($branch->scope_id);
            abort_unless($record, 403);
            return ['type' => 'branch', 'id' => $record->id, 'label' => 'Branch Administration: '.$record->name];
        }
        abort(403, 'You do not have permission to import members.');
    }

    private function findSet(string $value): ?GraduationSet
    {
        return GraduationSet::where('is_active', true)->where(function ($q) use ($value) {
            $q->whereRaw('LOWER(name) = ?', [strtolower(trim($value))]);
            if (is_numeric($value)) $q->orWhere('graduation_year', (int) $value);
        })->first();
    }

    private function findBranchForRow(string $value, array $scope): ?Branch
    {
        if ($scope['type'] === 'branch') return Branch::findOrFail($scope['id']);
        if (trim($value) === '') return null;
        return Branch::where('is_active', true)->whereRaw('LOWER(name) = ?', [strtolower(trim($value))])->first();
    }

    private function notifyApprovers(User $member): void
    {
        $profile = $member->alumniProfile;
        if (! $profile?->graduation_set_id) return;

        $targets = LeadershipAssignment::query()
            ->where('is_active', true)
            ->where(function ($q) use ($profile, $member) {
                $q->where(function ($inner) use ($profile) {
                    $inner->where('scope_type', 'set')->where('scope_id', $profile->graduation_set_id);
                });
                foreach ($member->branchAffiliations as $affiliation) {
                    $q->orWhere(function ($inner) use ($affiliation) {
                        $inner->where('scope_type', 'branch')->where('scope_id', $affiliation->branch_id);
                    });
                }
            })
            ->with('user')
            ->get()
            ->pluck('user')->filter()->unique('id');

        foreach ($targets as $target) {
            $target->notify(new BbhsPlatformNotification(
                'Bulk-imported member awaiting verification',
                $member->name.' was added through a membership CSV import and is awaiting your review.',
                'membership',
                '/admin/verifications',
                ['member_id' => $member->id, 'graduation_set_id' => $profile->graduation_set_id]
            ));
        }
    }

    private function validateRow(array $row, array $scope): array
    {
        $issues = [];
        foreach (['first_name', 'last_name', 'email', 'phone', 'set'] as $field) {
            if (trim((string) ($row[$field] ?? '')) === '') $issues[] = str_replace('_', ' ', ucfirst($field)).' is required';
        }
        if (! filter_var($row['email'] ?? '', FILTER_VALIDATE_EMAIL)) $issues[] = 'Email address is invalid';
        if (! $this->findSet((string) ($row['set'] ?? ''))) $issues[] = 'Graduation set not found (use set name or year)';
        if ($scope['type'] === 'set' && $this->findSet((string) ($row['set'] ?? ''))?->id !== $scope['id']) $issues[] = 'You can only import members into your assigned set';
        if ($scope['type'] === 'branch' && trim((string) ($row['branch'] ?? '')) !== '' && strtolower(trim($row['branch'])) !== strtolower($scope['label'] === '' ? '' : Branch::find($scope['id'])?->name ?? '')) $issues[] = 'Branch column must be blank or match your assigned branch';
        if ($scope['type'] !== 'branch' && trim((string) ($row['branch'] ?? '')) !== '' && ! $this->findBranchForRow($row['branch'], $scope)) $issues[] = 'Branch not found';
        if (User::whereRaw('LOWER(email) = ?', [strtolower(trim((string) ($row['email'] ?? '')) )])->exists()) $issues[] = 'Email already exists';
        return array_values(array_unique($issues));
    }
}
