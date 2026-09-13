<?php

namespace App\Filament\Resources\AccountAuditLogs\Pages;

use App\Filament\Resources\AccountAuditLogs\AccountAuditLogResource;
use Filament\Resources\Pages\ManageRecords;

class ManageAccountAuditLogs extends ManageRecords
{
    protected static string $resource = AccountAuditLogResource::class;
}
