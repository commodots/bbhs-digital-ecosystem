<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'member_status', 'is_admin', 'welfare_team',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'welfare_team' => 'boolean',
        ];
    }

    public function alumniProfile(): HasOne
    {
        return $this->hasOne(AlumniProfile::class);
    }

    public function leadershipAssignments(): HasMany
    {
        return $this->hasMany(LeadershipAssignment::class);
    }

    public function branchAffiliations(): HasMany
    {
        return $this->hasMany(MemberBranch::class);
    }

    public function membershipVerifications(): HasMany
    {
        return $this->hasMany(MembershipVerification::class);
    }

    public function isAdmin(): bool
    {
        return ($this->role ?? null) === 'admin' || (bool) $this->is_admin;
    }

    public function hasActiveGlobalExecutiveAssignment(): bool
    {
        return $this->leadershipAssignments()
            ->where('scope_type', 'global')
            ->whereNull('scope_id')
            ->where('is_active', true)
            ->where(fn ($query) => $query->whereNull('starts_at')->orWhereDate('starts_at', '<=', today()))
            ->where(fn ($query) => $query->whereNull('ends_at')->orWhereDate('ends_at', '>=', today()))
            ->exists();
    }

    public function isGlobalExecutive(): bool
    {
        return ! $this->isAdmin() && $this->hasActiveGlobalExecutiveAssignment();
    }

    public function isMember(): bool
    {
        return ($this->role ?? 'member') === 'member';
    }
}
