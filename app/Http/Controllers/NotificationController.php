<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class NotificationController extends Controller
{
    /**
     * Cache TTL for recent notifications (in seconds).
     */
    private const CACHE_TTL = 15;

    /**
     * Get the cache key for the current user's recent notifications.
     */
    private function getCacheKey(): string
    {
        return 'notifications:recent:' . Auth::id();
    }

    /**
     * Display the notification center.
     */
    public function index(Request $request)
    {
        $filter = $request->input('filter', 'all');
        $query = Auth::user()->notifications();

        if ($filter == 'unread') {
            $query->unread();
        }

        $notifications = $query->paginate(15);
        
        return view('notifications.index', compact('notifications', 'filter'));
    }

    /**
     * Get recent notifications for the dropdown (AJAX).
     * Results are cached per-user for 15 seconds to reduce DB load.
     */
    public function getRecent()
    {
        $cacheKey = $this->getCacheKey();

        $data = Cache::remember($cacheKey, self::CACHE_TTL, function () {
            $user = Auth::user();
            return [
                'unread_count' => $user->unreadNotifications->count(),
                'recent' => $user->notifications()->take(5)->get()->map(function($n) {
                    return [
                        'id' => $n->id,
                        'data' => $n->data,
                        'read_at' => $n->read_at,
                        'created_at' => $n->created_at->diffForHumans(),
                    ];
                })->toArray()
            ];
        });

        return response()->json($data);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->where(['id' => $id])->first();
        if ($notification) {
            $notification->markAsRead();
        }

        // Invalidate cache so next poll reflects the change
        Cache::forget($this->getCacheKey());

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notifikasi ditandai telah dibaca.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        // Invalidate cache so next poll reflects the change
        Cache::forget($this->getCacheKey());

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Semua notifikasi telah dibaca.');
    }

    /**
     * Delete a notification.
     */
    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->where(['id' => $id])->first();
        if ($notification) {
            $notification->delete();
        }

        // Invalidate cache so next poll reflects the change
        Cache::forget($this->getCacheKey());

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notifikasi berhasil dihapus.');
    }
}
