<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Event extends Model
{
    protected $table = 'events';

    protected $fillable = [
        'title', 'slug', 'excerpt', 'description', 'image', 'starts_at', 'ends_at',
        'venue', 'location', 'capacity', 'registration_open', 'published', 'scope_type',
        'graduation_set_id', 'branch_id', 'created_by', 'requires_registration',
        'allow_non_branch_residents',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'registration_open' => 'boolean',
        'published' => 'boolean',
        'requires_registration' => 'boolean',
        'allow_non_branch_residents' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($event) {
            if (empty($event->slug)) {
                $event->slug = Str::slug($event->title);
            }
        });
    }

    public function graduationSet(): BelongsTo
    {
        return $this->belongsTo(GraduationSet::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopes(): HasMany
    {
        return $this->hasMany(EventScope::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(EventAttendance::class);
    }

    public function minutes()
    {
        return $this->hasOne(EventMinute::class);
    }

    public function resolutions(): HasMany
    {
        return $this->hasMany(EventResolution::class);
    }
}
