<?php

namespace App\Repositories\Contracts;

use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface NotificationRepositoryInterface
{
    public function paginateForUser(User $user, ?bool $unreadOnly, int $perPage = 20): LengthAwarePaginator;

    public function createForUser(User $user, string $type, string $title, ?string $body = null, ?array $data = null): AppNotification;

    public function markRead(AppNotification $notification, User $user): void;

    public function markAllRead(User $user): void;
}
