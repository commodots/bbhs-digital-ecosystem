<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'title','slug','category','excerpt','description','image',
        'target_amount','raised_amount','status','starts_on','ends_on','published'
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'raised_amount' => 'decimal:2',
        'starts_on' => 'date',
        'ends_on' => 'date',
        'published' => 'boolean',
    ];
}
