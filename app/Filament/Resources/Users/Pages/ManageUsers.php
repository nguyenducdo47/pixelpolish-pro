<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageUsers extends ManageRecords
{
    protected static string $resource = UserResource::class;

    public function getSubheading(): ?string
    {
        return 'Tạo tài khoản luôn kèm portfolio trống. User đăng nhập tại /login rồi vào /studio. Chỉ Admin mới vào /admin. Sửa hộ: “Chỉnh portfolio”.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->using(fn (array $data): User => UserResource::saveUser(null, $data, isCreate: true))
                ->successNotificationTitle('Đã tạo user và portfolio trống')
                ->successRedirectUrl(fn (User $record): string => route('impersonation.enter', $record)),
        ];
    }
}
