<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AlumniProfile extends Model
{
    protected $fillable = [
        'user_id', 'first_name', 'last_name', 'class_set', 'graduation_set_id',
        'phone', 'whatsapp_phone', 'alternate_phone', 'preferred_contact', 'sms_consent', 'whatsapp_consent', 'profession', 'city', 'city_normalized', 'country', 'country_normalized',
        'photo', 'bio', 'directory_visible', 'approved',
        'public_photo', 'public_name', 'public_graduation_set', 'public_profession', 'public_location', 'public_bio',
    ];

    protected $casts = [
        'directory_visible' => 'boolean',
        'public_photo' => 'boolean',
        'public_name' => 'boolean',
        'public_graduation_set' => 'boolean',
        'public_profession' => 'boolean',
        'public_location' => 'boolean',
        'public_bio' => 'boolean',
        'approved' => 'boolean',
        'sms_consent' => 'boolean',
        'whatsapp_consent' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function graduationSet(): BelongsTo
    {
        return $this->belongsTo(GraduationSet::class);
    }
}
