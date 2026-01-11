<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="General Housing Notifications",
 *     description="Notification endpoints for general housing users"
 * )
 */
class GeneralNotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * @OA\Get(
     *     path="/api/general/notifications",
     *     tags={"General Housing Notifications"},
     *     summary="Get user notifications",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of notifications"
     *     )
     * )
     */
    public function index()
    {
        $user = auth()->user();
        
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'data' => $notification->data,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at,
                ];
            });

        return response()->json([
            'status' => true,
            'data' => $notifications,
            'unread_count' => $user->unreadNotifications()->count()
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/general/notifications/mark-read",
     *     tags={"General Housing Notifications"},
     *     summary="Mark all notifications as read",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Notifications marked as read"
     *     )
     * )
     */
    public function markAllRead()
    {
        $user = auth()->user();
        $user->unreadNotifications->markAsRead();

        return response()->json([
            'status' => true,
            'message' => 'All notifications marked as read'
        ], 200);
    }
}
