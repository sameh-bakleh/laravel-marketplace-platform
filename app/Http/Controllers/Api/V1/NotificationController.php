<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppNotificationResource;
use App\Models\AppNotification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NotificationController extends Controller
{
    public function __construct(
        private readonly NotificationService $notifications,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min(100, max(1, (int) $request->query('per_page', 20)));
        $unread = $request->query('unread');
        $unreadOnly = $unread === null ? null : filter_var($unread, FILTER_VALIDATE_BOOLEAN);

        return AppNotificationResource::collection(
            $this->notifications->list($request->user(), $unreadOnly, $perPage)
        );
    }

    public function markRead(Request $request, int $notification): AppNotificationResource
    {
        $model = AppNotification::query()->whereKey($notification)->first();
        if (! $model || $model->user_id !== $request->user()->id) {
            throw new NotFoundHttpException;
        }

        $this->notifications->markRead($model, $request->user());

        return new AppNotificationResource($model->fresh());
    }

    public function markAllRead(Request $request): Response
    {
        $this->notifications->markAllRead($request->user());

        return response()->noContent();
    }
}
