<?php

namespace App\Models;

use App\Enums\AccountAuditAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountAuditLog extends Model
{
    protected $fillable = [
        'subject_user_id',
        'actor_user_id',
        'action',
        'reason',
        'meta',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'action' => AccountAuditAction::class,
            'meta' => 'array',
        ];
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(User::class, 'subject_user_id')->withTrashed();
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }
}
