<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function cities(): HasMany
    {
        return $this->hasMany(BranchCity::class);
    }

    public function memberAffiliations(): HasMany
    {
        return $this->hasMany(MemberBranch::class);
    }

    public function leadershipAssignments(): HasMany
    {
        return $this->hasMany(LeadershipAssignment::class, 'scope_id')
            ->where('scope_type', 'branch');
    }
}
