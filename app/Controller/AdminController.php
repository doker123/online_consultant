<?php

namespace Controller;

use Model\Chat;
use Model\Message;
use Model\Ban;
use Model\User;
use Model\Page;
use Src\View;
use Src\Request;
use Src\Session;
use Src\Auth\Auth;

class AdminController
{
    public function index(): string
    {
        $user = Auth::user();
        $chats = Chat::with('user')->orderBy('created_at', 'desc')->get();

        $totalUsers = User::where('role', 'user')->count();
        $activeChats = Chat::where('status', 'open')->count();
        $totalMessages = Message::count();
        $bannedUsers = Ban::active()->count();
        $totalPages = Page::count();

        return (string) new View('Admin.index', [
            'user' => $user,
            'chats' => $chats,
            'totalUsers' => $totalUsers,
            'activeChats' => $activeChats,
            'totalMessages' => $totalMessages,
            'bannedUsers' => $bannedUsers,
            'totalPages' => $totalPages,
        ]);
    }

    public function chats(): string
    {
        $user = Auth::user();
        $chats = Chat::with('user')->orderBy('created_at', 'desc')->get();

        return (string) new View('Admin.chats', [
            'user' => $user,
            'chats' => $chats,
        ]);
    }

    public function chat(int $chatId, Request $request): string
    {
        $user = Auth::user();
        $chat = Chat::with('user')->where('id', $chatId)->first();

        if (!$chat) {
            app()->route->redirect('/admin/chats');
            exit();
        }

        if ($chat->user_id == $user->id) {
            app()->route->redirect('/admin/chats');
            exit();
        }

        $messages = Message::where('chat_id', $chatId)
            ->orderBy('created_at', 'asc')
            ->get();

        Message::where('chat_id', $chatId)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        $ban = Ban::where('user_id', $chat->user_id)->active()->first();

        return (string) new View('Admin.chat', [
            'user' => $user,
            'chat' => $chat,
            'messages' => $messages,
            'ban' => $ban,
        ]);
    }

    public function sendMessage(int $chatId, Request $request): void
    {
        $user = Auth::user();
        $text = trim($request->get('text') ?? '');

        if (empty($text)) {
            app()->route->redirect("/admin/chat/$chatId");
            exit();
        }

        $chat = Chat::where('id', $chatId)->first();
        if (!$chat) {
            app()->route->redirect('/admin/chats');
            exit();
        }

        Message::create([
            'chat_id' => $chatId,
            'sender_id' => $user->id,
            'text' => $text,
        ]);

        if (!$chat->admin_id) {
            $chat->update(['admin_id' => $user->id]);
        }

        app()->route->redirect("/admin/chat/$chatId");
        exit();
    }

    public function banUser(int $userId, Request $request): void
    {
        $admin = Auth::user();

        if ($userId == $admin->id) {
            app()->route->redirect('/admin/chats');
            exit();
        }

        $reason = trim($request->get('reason') ?? '');
        $duration = $request->get('duration') ?? '';
        $permanent = $request->get('permanent') ?? '0';

        if (empty($reason)) {
            app()->route->redirect("/admin/chat/" . ($request->get('chat_id') ?? ''));
            exit();
        }

        Ban::where('user_id', $userId)->active()->delete();

        $expiresAt = null;
        if ($permanent == '1') {
            $expiresAt = null;
        } elseif (!empty($duration)) {
            $expiresAt = date('Y-m-d H:i:s', strtotime("+{$duration} hours"));
        } else {
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
        }

        Ban::create([
            'user_id' => $userId,
            'banned_by' => $admin->id,
            'reason' => $reason,
            'expires_at' => $expiresAt,
            'is_permanent' => $permanent == '1' ? 1 : 0,
        ]);

        $chatId = $request->get('chat_id') ?? '';
        if ($chatId) {
            app()->route->redirect("/admin/chat/$chatId");
        } else {
            app()->route->redirect('/admin/chats');
        }
        exit();
    }

    public function unbanUser(int $userId, Request $request): void
    {
        Ban::where('user_id', $userId)->active()->delete();

        $chatId = $request->get('chat_id') ?? '';
        if ($chatId) {
            app()->route->redirect("/admin/chat/$chatId");
        } else {
            app()->route->redirect('/admin/chats');
        }
        exit();
    }

    public function users(): string
    {
        $user = Auth::user();
        $users = User::where('role', 'user')->orderBy('created_at', 'desc')->get();

        $bans = Ban::active()->get()->keyBy('user_id');

        return (string) new View('Admin.users', [
            'user' => $user,
            'users' => $users,
            'bans' => $bans,
        ]);
    }

    public function uploadAvatar(Request $request): void
    {
        $user = Auth::user();
        if (!$user) {
            app()->route->redirect('/login');
            exit();
        }

        $files = $request->files();
        if (!empty($files['avatar'])) {
            $file = $files['avatar'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $ext;
            $uploadDir = __DIR__ . '/../../public/uploads/avatars/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                $user->update(['avatar' => $filename]);
            }
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        header("Location: $referer");
        exit();
    }

    public function pages(): string
    {
        $user = Auth::user();
        $pages = Page::orderBy('created_at', 'desc')->get();

        return (string) new View('Admin.pages', [
            'user' => $user,
            'pages' => $pages,
        ]);
    }

    private function uploadPageImage(Request $request): ?string
    {
        $files = $request->files();
        if (empty($files['image']) || $files['image']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $file = $files['image'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($ext, $allowed)) {
            return null;
        }

        $filename = 'page_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $uploadDir = __DIR__ . '/../../public/uploads/pages/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            return $filename;
        }

        return null;
    }

    public function createPage(Request $request): string
    {
        $user = Auth::user();
        $errors = Session::get('page_errors') ?? [];
        $old = Session::get('page_old') ?? [];

        if ($request->method === 'POST') {
            $title = trim($request->get('title') ?? '');
            $slug = trim($request->get('slug') ?? '');
            $content = trim($request->get('content') ?? '');
            $isPublished = $request->get('is_published') ? 1 : 0;

            $errors = [];

            if (empty($title)) {
                $errors['title'] = 'Название обязательно';
            }

            if (empty($slug)) {
                $slug = Page::generateSlug($title);
            }

            if (Page::where('slug', $slug)->exists()) {
                $errors['slug'] = 'Такой slug уже существует';
            }

            if (empty($content)) {
                $errors['content'] = 'Контент обязателен';
            }

            if (!empty($errors)) {
                Session::set('page_errors', $errors);
                Session::set('page_old', [
                    'title' => $title,
                    'slug' => $slug,
                    'content' => $content,
                    'is_published' => $isPublished,
                ]);
                app()->route->redirect('/admin/pages/create');
                exit();
            }

            $image = $this->uploadPageImage($request);

            Page::create([
                'title' => $title,
                'slug' => $slug,
                'content' => $content,
                'image' => $image,
                'is_published' => $isPublished,
            ]);

            Session::clear('page_errors');
            Session::clear('page_old');
            app()->route->redirect('/admin/pages');
            exit();
        }

        Session::clear('page_errors');
        Session::clear('page_old');

        return (string) new View('Admin.page-form', [
            'user' => $user,
            'page' => null,
            'errors' => $errors,
            'old' => $old,
        ]);
    }

    public function editPage(int $id, Request $request): string
    {
        $user = Auth::user();
        $page = Page::find($id);

        if (!$page) {
            app()->route->redirect('/admin/pages');
            exit();
        }

        $errors = Session::get('page_errors') ?? [];
        $old = Session::get('page_old') ?? [];

        if ($request->method === 'POST') {
            $title = trim($request->get('title') ?? '');
            $slug = trim($request->get('slug') ?? '');
            $content = trim($request->get('content') ?? '');
            $isPublished = $request->get('is_published') ? 1 : 0;

            $errors = [];

            if (empty($title)) {
                $errors['title'] = 'Название обязательно';
            }

            if (empty($slug)) {
                $slug = Page::generateSlug($title);
            }

            if (Page::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $errors['slug'] = 'Такой slug уже существует';
            }

            if (empty($content)) {
                $errors['content'] = 'Контент обязателен';
            }

            if (!empty($errors)) {
                Session::set('page_errors', $errors);
                Session::set('page_old', [
                    'title' => $title,
                    'slug' => $slug,
                    'content' => $content,
                    'is_published' => $isPublished,
                ]);
                app()->route->redirect("/admin/pages/$id/edit");
                exit();
            }

            $updateData = [
                'title' => $title,
                'slug' => $slug,
                'content' => $content,
                'is_published' => $isPublished,
            ];

            $newImage = $this->uploadPageImage($request);
            if ($newImage) {
                if ($page->image) {
                    $oldPath = __DIR__ . '/../../public/uploads/pages/' . $page->image;
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $updateData['image'] = $newImage;
            }

            $page->update($updateData);

            Session::clear('page_errors');
            Session::clear('page_old');
            app()->route->redirect('/admin/pages');
            exit();
        }

        Session::clear('page_errors');
        Session::clear('page_old');

        return (string) new View('Admin.page-form', [
            'user' => $user,
            'page' => $page,
            'errors' => $errors,
            'old' => $old,
        ]);
    }

    public function deletePage(int $id, Request $request): void
    {
        $page = Page::find($id);
        if ($page) {
            if ($page->image) {
                $imagePath = __DIR__ . '/../../public/uploads/pages/' . $page->image;
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            $page->delete();
        }
        app()->route->redirect('/admin/pages');
        exit();
    }

    public function togglePage(int $id, Request $request): void
    {
        $page = Page::find($id);
        if ($page) {
            $page->update(['is_published' => $page->is_published ? 0 : 1]);
        }
        app()->route->redirect('/admin/pages');
        exit();
    }
}
