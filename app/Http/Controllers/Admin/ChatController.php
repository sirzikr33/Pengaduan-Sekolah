<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Chat queue / inbox for admin.
     */
    public function index()
    {
        $queuedChats = ChatSession::with(['user', 'user.siswa'])
            ->where('status', 'queued')
            ->orderBy('queued_at')
            ->get();

        $activeChats = ChatSession::with(['user', 'user.siswa', 'admin'])
            ->where('status', 'active')
            ->where('admin_id', Auth::id())
            ->orderByDesc('accepted_at')
            ->get();

        $recentChats = ChatSession::with(['user', 'user.siswa'])
            ->where('status', 'resolved')
            ->whereNotNull('admin_id')
            ->orderByDesc('resolved_at')
            ->take(10)
            ->get();

        $queueCount = $queuedChats->count();

        return view('admin.chat.index', compact('queuedChats', 'activeChats', 'recentChats', 'queueCount'));
    }

    /**
     * Accept a queued chat.
     */
    public function accept(ChatSession $chatSession)
    {
        if ($chatSession->status !== 'queued') {
            return back()->with('error', 'Chat ini sudah tidak dalam antrean.');
        }

        $chatSession->update([
            'status'      => 'active',
            'admin_id'    => Auth::id(),
            'accepted_at' => now(),
        ]);

        // Reorder remaining queue positions
        ChatSession::where('status', 'queued')
            ->orderBy('queued_at')
            ->get()
            ->each(function ($s, $i) {
                $s->update(['queue_position' => $i + 1]);
            });

        // Send notification message to user
        ChatMessage::create([
            'chat_session_id' => $chatSession->id,
            'sender_type'     => 'bot',
            'message'         => "✅ Admin **" . Auth::user()->name . "** sudah menerima chat kamu. Silakan sampaikan permasalahanmu!",
        ]);

        return redirect()->route('admin.chat.show', $chatSession);
    }

    /**
     * Show chat conversation (admin view).
     */
    public function show(ChatSession $chatSession)
    {
        if ($chatSession->status === 'queued') {
            return redirect()->route('admin.chat.index')
                ->with('error', 'Terima chat ini terlebih dahulu.');
        }

        $chatSession->load(['user', 'user.siswa', 'pengaduan', 'pengaduan.kategori']);
        $messages = $chatSession->messages()->orderBy('created_at')->get();

        return view('admin.chat.show', compact('chatSession', 'messages'));
    }

    /**
     * Admin sends a message.
     */
    public function sendMessage(Request $request, ChatSession $chatSession)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        if ($chatSession->admin_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $msg = ChatMessage::create([
            'chat_session_id' => $chatSession->id,
            'sender_type'     => 'admin',
            'sender_id'       => Auth::id(),
            'message'         => $request->message,
        ]);

        return response()->json(['success' => true, 'message' => $msg]);
    }

    /**
     * Get messages for polling (admin side).
     */
    public function getMessages(Request $request, ChatSession $chatSession)
    {
        $query = $chatSession->messages()->orderBy('created_at');

        if ($request->after) {
            $query->where('created_at', '>', $request->after);
        }

        return response()->json([
            'success'  => true,
            'messages' => $query->get(),
            'status'   => $chatSession->fresh()->status,
        ]);
    }

    /**
     * Resolve / close a chat session.
     */
    public function resolve(ChatSession $chatSession)
    {
        if ($chatSession->admin_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized');
        }

        ChatMessage::create([
            'chat_session_id' => $chatSession->id,
            'sender_type'     => 'bot',
            'message'         => "💬 Chat telah ditutup oleh admin. Terima kasih atas laporannya! Jika ada permasalahan lain, silakan buat chat baru.",
        ]);

        $chatSession->update([
            'status'      => 'resolved',
            'resolved_at' => now(),
        ]);

        return redirect()->route('admin.chat.index')
            ->with('success', 'Chat berhasil diselesaikan.');
    }

    /**
     * Get queue count for badge (AJAX).
     */
    public function queueCount()
    {
        return response()->json([
            'count' => ChatSession::where('status', 'queued')->count(),
        ]);
    }
}
