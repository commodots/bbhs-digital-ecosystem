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
        'name', 'email', 'password', 'role', 'member_status', 'is_admin',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
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

    public function isMember(): bool
    {
        return ($this->role ?? 'member') === 'member';
    }
}
