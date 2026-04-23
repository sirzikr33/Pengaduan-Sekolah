<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Ambil semua notifikasi (JSON) untuk polling di frontend.
     */
    public function index()
    {
        $user = Auth::user();

        $notifications = $user->notifications()
            ->latest()
            ->take(20)
            ->get()
            ->map(fn($n) => [
                'id'         => $n->id,
                'data'       => $n->data,
                'read_at'    => $n->read_at,
                'created_at' => $n->created_at,
            ]);

        return response()->json([
            'success'       => true,
            'notifications' => $notifications,
            'unread_count'  => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark satu notifikasi sebagai sudah dibaca.
     */
    public function markRead(Request $request)
    {
        $request->validate(['id' => 'required|string']);

        $notif = Auth::user()->notifications()->find($request->id);
        if ($notif) {
            $notif->markAsRead();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Mark semua notifikasi sebagai sudah dibaca.
     */
    public function markAll()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }
}
