<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WelfareActivityLog extends Model
{
    protected $fillable = ['welfare_application_id', 'actor_id', 'action', 'details'];
    protected $casts = ['details' => 'array'];

    public function application(): BelongsTo { return $this->belongsTo(WelfareApplication::class, 'welfare_application_id'); }
    public function actor(): BelongsTo { return $this->belongsTo(User::class, 'actor_id'); }
}
