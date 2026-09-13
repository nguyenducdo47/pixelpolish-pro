<?php

namespace App\Services;

use App\Enums\AccountAuditAction;
use App\Models\AccountAuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AccountAuditLogger
{
    /**
     * @param  array<string, mixed>  $meta
     */
    public static function log(
        User $subject,
        AccountAuditAction $action,
        ?string $reason = null,
        ?User $actor = null,
        array $meta = [],
    ): AccountAuditLog {
        $request = request();

        return AccountAuditLog::query()->create([
            'subject_user_id' => $subject->getKey(),
            'actor_user_id' => $actor?->getKey(),
            'action' => $action,
            'reason' => $reason,
            'meta' => $meta === [] ? null : $meta,
            'ip_address' => $request instanceof Request ? $request->ip() : null,
            'user_agent' => $request instanceof Request ? $request->userAgent() : null,
        ]);
    }
}
