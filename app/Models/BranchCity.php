<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchCity extends Model
{
    protected $fillable = ['branch_id', 'city', 'country', 'city_normalized', 'country_normalized'];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
