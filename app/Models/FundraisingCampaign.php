<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class FundraisingCampaign extends Model
{
    protected $guarded = [];
    protected function casts(): array { return ['target_amount_kobo'=>'integer','published'=>'boolean','starts_at'=>'date','ends_at'=>'date']; }
    public function donations(): HasMany { return $this->hasMany(FundraisingDonation::class, 'campaign_id'); }
    public function pledges(): HasMany { return $this->hasMany(FundraisingPledge::class, 'campaign_id'); }
    public function getRaisedAmountKoboAttribute(): int { return (int) $this->donations()->where('status','success')->sum('amount_kobo'); }
}
