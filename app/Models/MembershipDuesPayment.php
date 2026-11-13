<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class MembershipDuesPayment extends Model {
 protected $guarded=[];
 protected function casts(): array { return ['period_start'=>'date','period_end'=>'date','paid_at'=>'datetime','gateway_response'=>'array']; }
 public function user(): BelongsTo { return $this->belongsTo(User::class); }
 public function account(): BelongsTo { return $this->belongsTo(MembershipDuesAccount::class,'dues_account_id'); }
}
