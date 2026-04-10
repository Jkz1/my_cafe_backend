<?php

namespace App\Filament\Resources\Coupons\Pages;

use App\Filament\Resources\Coupons\CouponsResource;
use App\Services\CouponsService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateCoupons extends CreateRecord
{
    protected static string $resource = CouponsResource::class;

    protected function afterCreate(): void
    {
        // Check if the checkbox was ticked
        if ($this->data['send_to_users'] ?? false) {
            $coupon = $this->record;
            app(CouponsService::class)->broadcastToAllUsers($coupon);

            Notification::make()
                ->title('Emails are being sent')
                ->success()
                ->send();
        }
    }
}
