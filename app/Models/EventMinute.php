<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventMinute extends Model
{
    protected $fillable = ['event_id', 'summary', 'minutes', 'recorded_by_name'];
    public function event(): BelongsTo { return $this->belongsTo(Event::class); }
}
