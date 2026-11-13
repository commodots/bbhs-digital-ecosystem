<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class MembershipDuesAccount extends Model {
 protected $guarded=[];
 protected function casts(): array { return ['commencement_date'=>'date','paid_through'=>'date','next_due_at'=>'date','exempt'=>'boolean','last_due_notice_at'=>'datetime']; }
 public function user(): BelongsTo { return $this->belongsTo(User::class); }
 public function payments(): HasMany { return $this->hasMany(MembershipDuesPayment::class,'dues_account_id'); }
 public function getComputedStatusAttribute(): string { if($this->exempt) return 'exempt'; if($this->paid_through && $this->paid_through->gte(today())) return 'up_to_date'; return $this->next_due_at && $this->next_due_at->lt(today()) ? 'overdue' : 'due'; }
}
