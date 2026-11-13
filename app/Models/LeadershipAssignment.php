<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadershipAssignment extends Model
{
    protected $fillable = [
        'user_id', 'executive_position_id', 'scope_type', 'scope_id',
        'starts_at', 'ends_at', 'is_active', 'appointed_by', 'notes',
    ];

    protected $casts = [
        'starts_at' => 'date', 'ends_at' => 'date', 'is_active' => 'boolean',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function position(): BelongsTo { return $this->belongsTo(ExecutivePosition::class, 'executive_position_id'); }
    public function appointer(): BelongsTo { return $this->belongsTo(User::class, 'appointed_by'); }
}
