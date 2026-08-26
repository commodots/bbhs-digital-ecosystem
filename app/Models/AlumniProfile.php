<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlumniProfile extends Model
{
    protected $fillable = [
        'user_id','first_name','last_name','class_set','phone','profession',
        'city','country','photo','bio','directory_visible','approved'
    ];

    protected $casts = [
        'directory_visible' => 'boolean',
        'approved' => 'boolean',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
