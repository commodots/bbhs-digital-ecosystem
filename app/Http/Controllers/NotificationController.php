<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->user()->notifications()->latest();

        if ($request->query('filter') === 'unread') {
            $query->whereNull('read_at');
        }

        return Inertia::render('Notifications/Index', [
            'notifications' => $query->paginate(20)->withQueryString(),
            'filter' => $request->query('filter', 'all'),
        ]);
    }

    public function open(Request $request, string $notification)
    {
        $item = $request->user()->notifications()->whereKey($notification)->firstOrFail();
        $item->markAsRead();

        $target = data_get($item->data, 'url', '/notifications');
        if (! is_string($target) || ! str_starts_with($target, '/') || str_starts_with($target, '//')) {
            $target = '/notifications';
        }

        return redirect($target);
    }

    public function markRead(Request $request, string $notification)
    {
        $request->user()->notifications()->whereKey($notification)->firstOrFail()->markAsRead();
        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);
        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy(Request $request, string $notification)
    {
        $request->user()->notifications()->whereKey($notification)->firstOrFail()->delete();
        return back()->with('success', 'Notification deleted.');
    }
}
