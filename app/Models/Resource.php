<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $fillable = [
        'title','slug','type','description','file_path','external_url','published'
    ];

    protected $casts = ['published' => 'boolean'];
}
