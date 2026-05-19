<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Setting;
use Illuminate\Http\Request;

class LiveChatController extends Controller
{
    public function start(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
        ]);

        $conversation = Conversation::create([
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'customer',
            'message' => $request->message ?? 'Hello!',
        ]);

        $this->sendBotReply($conversation);

        return response()->json([
            'conversation_id' => $conversation->id,
            'customer_token' => $conversation->id . '-' . md5($conversation->created_at),
        ]);
    }

    public function send(Request $request, Conversation $conversation)
    {
        $request->validate(['message' => 'required|string|max:1000']);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'customer',
            'message' => $request->message,
        ]);

        $this->sendBotReply($conversation);

        return response()->json(['success' => true]);
    }

    private function sendBotReply(Conversation $conversation)
    {
        $enabled = Setting::where('key', 'chatbot_enabled')->value('value');
        if ($enabled !== '1') {
            return;
        }

        $replyMessage = Setting::where('key', 'chatbot_reply_message')->value('value')
            ?? 'Thank you for reaching out! Our team will get back to you shortly.';

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'admin',
            'message' => $replyMessage,
            'is_bot' => true,
        ]);
    }

    public function messages(Conversation $conversation)
    {
        $messages = $conversation->messages()
            ->orderBy('created_at')
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'message' => $msg->message,
                    'sender_type' => $msg->sender_type,
                    'is_bot' => $msg->is_bot,
                    'time' => $msg->created_at->diffForHumans(),
                ];
            });

        return response()->json(['messages' => $messages]);
    }

    public function history(Request $request)
    {
        $request->validate([
            'customer_email' => 'nullable|email',
            'customer_name' => 'nullable|string',
        ]);

        $query = Conversation::with('messages');
        if ($request->customer_email) {
            $query->where('customer_email', $request->customer_email);
        }
        if ($request->customer_name) {
            $query->where('customer_name', $request->customer_name);
        }

        return response()->json([
            'conversations' => $query->where('status', '!=', 'closed')->orderBy('updated_at', 'desc')->get(),
        ]);
    }
}
