<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\User;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class NotificationService
{
    public function __construct(
        private readonly NotificationRepositoryInterface $notifications,
    ) {}

    public function list(User $user, ?bool $unreadOnly, int $perPage = 20): LengthAwarePaginator
    {
        return $this->notifications->paginateForUser($user, $unreadOnly, $perPage);
    }

    public function markRead(AppNotification $notification, User $user): void
    {
        $this->notifications->markRead($notification, $user);
    }

    public function markAllRead(User $user): void
    {
        $this->notifications->markAllRead($user);
    }
}
