<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WelfareDisbursement extends Model
{
    protected $fillable = ['welfare_application_id', 'amount', 'disbursed_on', 'method', 'reference', 'notes', 'recorded_by'];
    protected $casts = ['amount' => 'decimal:2', 'disbursed_on' => 'date'];
    public function application(): BelongsTo { return $this->belongsTo(WelfareApplication::class, 'welfare_application_id'); }
}
