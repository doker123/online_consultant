<?php /** @var $chats Chat[] */ /** @var $totalUsers int */ /** @var $activeChats int */ /** @var $totalMessages int */ /** @var $bannedUsers int */ /** @var $totalPages int */ ?>
<div class="admin-container">
    <h1>Админ-панель</h1>

    <div class="admin-stats">
        <div class="admin-stat">
            <span class="admin-stat-number"><?= $totalUsers ?></span>
            <span class="admin-stat-label">Пользователей</span>
        </div>
        <div class="admin-stat">
            <span class="admin-stat-number"><?= $activeChats ?></span>
            <span class="admin-stat-label">Активных чатов</span>
        </div>
        <div class="admin-stat">
            <span class="admin-stat-number"><?= $totalMessages ?></span>
            <span class="admin-stat-label">Сообщений</span>
        </div>
        <div class="admin-stat">
            <span class="admin-stat-number"><?= $bannedUsers ?></span>
            <span class="admin-stat-label">Заблокировано</span>
        </div>
    </div>

    <div class="admin-nav">
        <a href="<?= app()->route->getUrl('/admin/chats') ?>" class="admin-nav-link">Чаты</a>
        <a href="<?= app()->route->getUrl('/admin/users') ?>" class="admin-nav-link">Пользователи</a>
        <a href="<?= app()->route->getUrl('/admin/pages') ?>" class="admin-nav-link">Страницы (<?= $totalPages ?>)</a>
    </div>

    <h2>Последние чаты</h2>
    <div class="admin-chats-list">
        <?php if ($chats->isEmpty()): ?>
            <p>Нет активных чатов</p>
        <?php else: ?>
            <?php foreach ($chats->slice(0, 10) as $chat): ?>
                <a href="<?= app()->route->getUrl('/admin/chat/' . $chat->id) ?>" class="admin-chat-item">
                    <span class="admin-chat-user"><?= htmlspecialchars($chat->user->username ?? 'Неизвестный') ?></span>
                    <span class="admin-chat-status <?= $chat->status === 'open' ? 'open' : 'closed' ?>">
                        <?= $chat->status === 'open' ? 'Открыт' : 'Закрыт' ?>
                    </span>
                    <span class="admin-chat-date"><?= date('d.m.Y H:i', strtotime($chat->created_at)) ?></span>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
