<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        $conversations = Conversation::with(['messages' => function ($q) {
            $q->orderBy('created_at', 'desc');
        }])->orderBy('updated_at', 'desc')->get();

        $unreadCount = Message::where('sender_type', 'customer')->where('is_read', false)
            ->whereHas('conversation', fn($q) => $q->where('status', 'active'))
            ->count();

        return view('admin.chat.index', compact('conversations', 'unreadCount'));
    }

    public function show(Conversation $conversation)
    {
        $conversation->load('messages.admin');
        return view('admin.chat.show', compact('conversation'));
    }

    public function reply(Request $request, Conversation $conversation)
    {
        $request->validate(['message' => 'required|string|max:1000']);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'admin',
            'admin_id' => auth()->id(),
            'message' => $request->message,
        ]);

        return back()->with('success', 'Reply sent');
    }

    public function markRead(Conversation $conversation)
    {
        $conversation->messages()->where('sender_type', 'customer')->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function close(Conversation $conversation)
    {
        $conversation->update(['status' => 'closed']);
        return back()->with('success', 'Conversation closed');
    }

    public function resolve(Conversation $conversation)
    {
        $conversation->update(['status' => 'resolved']);
        return back()->with('success', 'Conversation marked as resolved');
    }

    public function reopen(Conversation $conversation)
    {
        $conversation->update(['status' => 'active']);
        return back()->with('success', 'Conversation reopened');
    }

    public function poll()
    {
        $conversations = Conversation::with(['messages' => function ($q) {
            $q->orderBy('created_at', 'desc')->take(1);
        }])->where('status', 'active')->orderBy('updated_at', 'desc')->get();

        $unreadCount = Message::where('sender_type', 'customer')->where('is_read', false)
            ->whereHas('conversation', fn($q) => $q->where('status', 'active'))
            ->count();

        return response()->json([
            'conversations' => $conversations,
            'unread_count' => $unreadCount,
        ]);
    }
}
