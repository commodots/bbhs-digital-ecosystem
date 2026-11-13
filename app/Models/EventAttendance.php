<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventAttendance extends Model
{
    protected $fillable = ['event_id', 'user_id', 'status', 'notes', 'marked_by', 'checked_in_at'];

    protected $casts = ['checked_in_at' => 'datetime'];

    public function event(): BelongsTo { return $this->belongsTo(Event::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function marker(): BelongsTo { return $this->belongsTo(User::class, 'marked_by'); }
}
