<?php

namespace Controller;

use Model\Chat;
use Model\Message;
use Model\Ban;
use Model\User;
use Src\View;
use Src\Request;
use Src\Auth\Auth;

class ChatController
{
    public function index(): string
    {
        $user = Auth::user();
        if (!$user) {
            app()->route->redirect('/login');
            exit();
        }

        if ($user->role === 'admin') {
            app()->route->redirect('/admin');
            exit();
        }

        $chat = Chat::where('user_id', $user->id)->first();
        if (!$chat) {
            $chat = Chat::create(['user_id' => $user->id]);
        }

        $ban = Ban::where('user_id', $user->id)->active()->first();
        $messages = Message::where('chat_id', $chat->id)
            ->orderBy('created_at', 'asc')
            ->get();

        $adminOnline = User::where('role', 'admin')->where('last_seen', '>', date('Y-m-d H:i:s', strtotime('-5 minutes')))->exists();

        return (string) new View('Chat.index', [
            'chat' => $chat,
            'messages' => $messages,
            'ban' => $ban,
            'adminOnline' => $adminOnline,
            'user' => $user,
        ]);
    }

    public function send(Request $request): void
    {
        $user = Auth::user();
        if (!$user) {
            app()->route->redirect('/login');
            exit();
        }

        $ban = Ban::where('user_id', $user->id)->active()->first();
        if ($ban) {
            app()->route->redirect('/chat');
            exit();
        }

        $text = trim($request->get('text') ?? '');
        if (empty($text)) {
            app()->route->redirect('/chat');
            exit();
        }

        $chat = Chat::where('user_id', $user->id)->first();
        if (!$chat) {
            $chat = Chat::create(['user_id' => $user->id]);
        }

        Message::create([
            'chat_id' => $chat->id,
            'sender_id' => $user->id,
            'text' => $text,
        ]);

        $chat->update(['status' => 'open']);

        app()->route->redirect('/chat');
        exit();
    }

    public function messages(int $chatId, Request $request): string
    {
        $user = Auth::user();
        if (!$user) {
            return json_encode(['error' => 'Unauthorized']);
        }

        $chat = Chat::where('id', $chatId)
            ->where('user_id', $user->id)
            ->first();

        if (!$chat) {
            return json_encode(['error' => 'Chat not found']);
        }

        $lastId = (int) ($request->get('last_id') ?? 0);

        $messages = Message::where('chat_id', $chatId)
            ->where('id', '>', $lastId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'sender_id' => $msg->sender_id,
                    'sender_name' => $msg->sender->username ?? 'Unknown',
                    'sender_avatar' => $msg->sender->avatar ?? null,
                    'text' => $msg->text,
                    'is_read' => $msg->is_read,
                    'created_at' => date('d.m.Y H:i', strtotime($msg->created_at)),
                ];
            });

        return json_encode(['messages' => $messages]);
    }

    public function readMessage(int $messageId, Request $request): string
    {
        $user = Auth::user();
        if (!$user) {
            return json_encode(['error' => 'Unauthorized']);
        }

        $message = Message::where('id', $messageId)
            ->where('sender_id', '!=', $user->id)
            ->first();

        if ($message) {
            $message->update(['is_read' => 1]);
        }

        return json_encode(['success' => true]);
    }

    public function status(): string
    {
        $user = Auth::user();
        if (!$user) {
            return json_encode(['error' => 'Unauthorized']);
        }

        $user->update(['last_seen' => date('Y-m-d H:i:s')]);

        $chat = Chat::where('user_id', $user->id)->first();
        $unreadCount = 0;
        if ($chat) {
            $unreadCount = Message::where('chat_id', $chat->id)
                ->where('sender_id', '!=', $user->id)
                ->where('is_read', 0)
                ->count();
        }

        $adminOnline = User::where('role', 'admin')
            ->where('last_seen', '>', date('Y-m-d H:i:s', strtotime('-5 minutes')))
            ->exists();

        return json_encode([
            'admin_online' => $adminOnline,
            'unread_count' => $unreadCount,
        ]);
    }
}
