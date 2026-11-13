<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlumniSpotlight extends Model
{
    protected $fillable = ['alumni_profile_id', 'headline', 'writeup', 'sort_order', 'is_active'];
    protected $casts = ['is_active' => 'boolean', 'sort_order' => 'integer'];
    public function alumniProfile(): BelongsTo { return $this->belongsTo(AlumniProfile::class); }
}
