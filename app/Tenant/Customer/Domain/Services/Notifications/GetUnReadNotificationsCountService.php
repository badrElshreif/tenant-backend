<?php

namespace App\User\Domain\Services\Notifications;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use Illuminate\Notifications\DatabaseNotification;
use Symfony\Component\HttpFoundation\Response;

class GetUnReadNotificationsCountService extends Service
{

    public function handle($data = []) 
    {
        $notifications_count = auth()->user()->morphMany(DatabaseNotification::class, 'notifiable')->unread()->count();
        return new GenericPayload(['notifications_count' => $notifications_count], Response::HTTP_RESET_CONTENT);
    }
}