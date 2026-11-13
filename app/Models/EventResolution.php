<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventResolution extends Model
{
    protected $fillable = ['event_id', 'resolution', 'responsible_person', 'due_date', 'status', 'notes'];
    protected $casts = ['due_date' => 'date'];
    public function event(): BelongsTo { return $this->belongsTo(Event::class); }
}
