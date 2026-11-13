<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventScope extends Model
{
    protected $fillable = ['event_id', 'scope_type', 'scope_id'];

    public function event(): BelongsTo { return $this->belongsTo(Event::class); }
}
