<?php

namespace App\Repositories;

use App\Models\AppNotification;
use App\Models\User;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class NotificationRepository implements NotificationRepositoryInterface
{
    public function paginateForUser(User $user, ?bool $unreadOnly, int $perPage = 20): LengthAwarePaginator
    {
        $q = AppNotification::query()->where('user_id', $user->id)->orderByDesc('id');

        if ($unreadOnly === true) {
            $q->whereNull('read_at');
        }

        return $q->paginate($perPage);
    }

    public function createForUser(User $user, string $type, string $title, ?string $body = null, ?array $data = null): AppNotification
    {
        return AppNotification::query()->create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'data' => $data,
        ]);
    }

    public function markRead(AppNotification $notification, User $user): void
    {
        if ($notification->user_id !== $user->id) {
            return;
        }

        $notification->update(['read_at' => now()]);
    }

    public function markAllRead(User $user): void
    {
        AppNotification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}
