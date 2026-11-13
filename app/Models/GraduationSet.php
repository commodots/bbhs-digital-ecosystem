<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GraduationSet extends Model
{
    protected $fillable = ['graduation_year', 'name', 'is_active'];

    protected $casts = ['graduation_year' => 'integer', 'is_active' => 'boolean'];

    public function alumniProfiles(): HasMany
    {
        return $this->hasMany(AlumniProfile::class);
    }

    public function leadershipAssignments(): HasMany
    {
        return $this->hasMany(LeadershipAssignment::class, 'scope_id')
            ->where('scope_type', 'set');
    }
}
