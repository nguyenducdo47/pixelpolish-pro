<?php

namespace App\Enums;

enum AccountAuditAction: string
{
    case Registered = 'registered';
    case Created = 'created';
    case Disabled = 'disabled';
    case Enabled = 'enabled';
    case SoftDeleted = 'soft_deleted';
    case Restored = 'restored';
}
