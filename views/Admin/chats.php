<?php /** @var $chats Chat[] */ ?>
<?php use Src\Auth\Auth; ?>
<div class="admin-container">
    <h1>Чаты</h1>

    <div class="admin-nav">
        <a href="<?= app()->route->getUrl('/admin') ?>" class="admin-nav-link">Дашборд</a>
        <a href="<?= app()->route->getUrl('/admin/users') ?>" class="admin-nav-link">Пользователи</a>
    </div>

    <div class="admin-chats-list">
        <?php if ($chats->isEmpty()): ?>
            <p>Нет чатов</p>
        <?php else: ?>
            <?php foreach ($chats as $chat): ?>
                <?php
                $ban = \Model\Ban::where('user_id', $chat->user_id)->active()->first();
                $unread = \Model\Message::where('chat_id', $chat->id)
                    ->where('sender_id', '!=', Auth::user()->id)
                    ->where('is_read', 0)
                    ->count();
                ?>
                <a href="<?= app()->route->getUrl('/admin/chat/' . $chat->id) ?>" class="admin-chat-item <?= $unread > 0 ? 'has-unread' : '' ?>">
                    <div class="admin-chat-info">
                        <span class="admin-chat-user">
                            <?= htmlspecialchars($chat->user->username ?? 'Неизвестный') ?>
                            <?php if ($ban): ?>
                                <span class="badge-ban">Забанен</span>
                            <?php endif; ?>
                        </span>
                        <span class="admin-chat-date"><?= date('d.m.Y H:i', strtotime($chat->created_at)) ?></span>
                    </div>
                    <?php if ($unread > 0): ?>
                        <span class="admin-chat-unread"><?= $unread ?></span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
