<?php

namespace Celios\Core\Http\Controllers\Api\V1;

use Celios\Core\Http\Controllers\Controller;
use Celios\Core\Http\Resources\V1\ActivityResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\Activitylog\Models\Activity;

class SystemController extends Controller
{
    /**
     * List system audit and activity logs (Admin).
     */
    public function activities(Request $request): AnonymousResourceCollection
    {
        $logName = $request->input('log_name');
        $perPage = min(max($request->integer('per_page', 20), 1), 50);

        $activities = Activity::query()
            ->when($logName, fn ($q, $logName) => $q->where('log_name', $logName))
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();

        return ActivityResource::collection($activities);
    }

    /**
     * List authenticated user's notifications.
     */
    public function notifications(Request $request): JsonResponse
    {
        $perPage = min(max($request->integer('per_page', 15), 1), 50);

        $notifications = $request->user()
            ->notifications()
            ->paginate($perPage);

        return response()->json($notifications);
    }

    /**
     * Mark a notification as read.
     */
    public function markNotificationRead(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        return response()->json([
            'message' => 'Notification marked as read.',
        ]);
    }
}
