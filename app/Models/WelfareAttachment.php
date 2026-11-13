<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WelfareAttachment extends Model
{
    protected $fillable = ['welfare_application_id', 'original_name', 'storage_path', 'mime_type', 'size_bytes'];
    public function application(): BelongsTo { return $this->belongsTo(WelfareApplication::class, 'welfare_application_id'); }
}
