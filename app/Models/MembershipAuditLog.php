<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MembershipAuditLog extends Model
{
    protected $fillable = [
        'user_id', 'actor_id', 'action', 'scope_type', 'scope_id', 'details', 'ip_address',
    ];

    protected $casts = ['details' => 'array'];

    public function member(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function actor(): BelongsTo { return $this->belongsTo(User::class, 'actor_id'); }
}
