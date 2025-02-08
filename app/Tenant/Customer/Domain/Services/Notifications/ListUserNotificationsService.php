<?php

namespace App\User\Domain\Services\Notifications;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\User\Domain\Models\Customer;
use App\User\Domain\Filters\UserFilter;
use App\Notification\Domain\Models\Notification;
use Symfony\Component\HttpFoundation\Response;

class ListUserNotificationsService extends Service
{
    protected $filter;

    public function __construct(UserFilter $filter)
    {
        $this->filter = $filter;
    }

    public function handle($data = [])
    {
        //$notifications = auth()->user()->unreadNotifications;
        $limit = isset($data['per_page']) ? $data['per_page'] : config('app.pagination_limit');
        $notifications = auth()->user()->notifications()->orderBy('created_at', 'desc')->paginate($limit);
//        $notifications = auth()->user()->unreadNotifications()->orderBy('created_at', 'desc')->paginate($limit);

        if(isset($data['is_read']) && $data['is_read'] == 1){
            foreach ($notifications as $notification) {
                $notification->markAsRead();
            }
        }
        //$user->notifications()->delete(); delete all user notifications
        return new GenericPayload($notifications, Response::HTTP_ACCEPTED);
    }
}
