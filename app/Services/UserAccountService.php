<?php

namespace App\Services;

use App\Enums\AccountAuditAction;
use App\Models\User;

class UserAccountService
{
    public function disable(User $user, string $reason, ?User $actor): User
    {
        $reason = trim($reason);

        $user->forceFill([
            'is_disabled' => true,
            'lock_reason' => $reason,
            'disabled_at' => now(),
        ])->save();

        $user->portfolio?->update(['is_published' => false]);

        AccountAuditLogger::log($user, AccountAuditAction::Disabled, $reason, $actor, [
            'username' => $user->username,
            'email' => $user->email,
        ]);

        return $user->refresh();
    }

    public function enable(User $user, ?User $actor): User
    {
        $previousReason = $user->lock_reason;

        $user->forceFill([
            'is_disabled' => false,
            'lock_reason' => null,
            'disabled_at' => null,
        ])->save();

        AccountAuditLogger::log($user, AccountAuditAction::Enabled, null, $actor, [
            'previous_lock_reason' => $previousReason,
        ]);

        return $user->refresh();
    }

    public function softDelete(User $user, string $reason, ?User $actor): void
    {
        $reason = trim($reason);

        if ($user->is_disabled !== true) {
            $user->forceFill([
                'is_disabled' => true,
                'lock_reason' => $reason,
                'disabled_at' => now(),
            ]);
        } elseif (filled($user->lock_reason)) {
            $user->lock_reason = $reason;
        }

        $user->portfolio?->update(['is_published' => false]);

        $user->save();

        AccountAuditLogger::log($user, AccountAuditAction::SoftDeleted, $reason, $actor);

        $user->delete();
    }

    public function restore(User $user, ?User $actor): User
    {
        $user->restore();

        $user->forceFill([
            'is_disabled' => false,
            'lock_reason' => null,
            'disabled_at' => null,
        ])->save();

        AccountAuditLogger::log($user, AccountAuditAction::Restored, null, $actor);

        return $user->refresh();
    }
}
