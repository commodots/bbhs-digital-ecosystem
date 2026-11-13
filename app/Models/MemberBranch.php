<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberBranch extends Model
{
    protected $fillable = [
        'user_id', 'branch_id', 'status', 'is_primary', 'requested_at',
        'reviewed_at', 'reviewed_by', 'review_notes',
    ];

    protected $casts = [
        'is_primary' => 'boolean', 'requested_at' => 'datetime', 'reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class, 'reviewed_by'); }
}
