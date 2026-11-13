<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class FundraisingDonation extends Model
{
    protected $guarded = [];
    protected function casts(): array { return ['amount_kobo'=>'integer','gateway_response'=>'array','paid_at'=>'datetime','is_anonymous'=>'boolean']; }
    public function campaign(): BelongsTo { return $this->belongsTo(FundraisingCampaign::class,'campaign_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
