<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WelfareApplication extends Model
{
    protected $fillable = [
        'user_id', 'reference', 'category', 'subject', 'description',
        'amount_requested', 'amount_approved', 'status', 'admin_notes',
        'reviewed_by', 'reviewed_at',
    ];

    protected $casts = [
        'amount_requested' => 'decimal:2',
        'amount_approved' => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function attachments(): HasMany { return $this->hasMany(WelfareAttachment::class); }
    public function disbursements(): HasMany { return $this->hasMany(WelfareDisbursement::class); }
}
