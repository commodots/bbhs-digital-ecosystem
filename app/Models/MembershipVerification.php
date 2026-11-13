<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MembershipVerification extends Model
{
    protected $fillable = [
        'user_id', 'verification_type', 'scope_type', 'scope_id', 'status',
        'reviewed_by', 'reviewed_at', 'review_notes',
    ];

    protected $casts = ['reviewed_at' => 'datetime'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class, 'reviewed_by'); }
}
