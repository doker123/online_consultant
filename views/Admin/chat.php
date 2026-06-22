<?php /** @var $chat Chat */ /** @var $messages Message[] */ /** @var $ban Ban */ ?>
<div class="admin-container">
    <div class="admin-chat-header">
        <a href="<?= app()->route->getUrl('/admin/chats') ?>" class="admin-back">&larr; Назад</a>
        <h1>Чат с <?= htmlspecialchars($chat->user->username ?? 'Неизвестный') ?></h1>
        <?php if ($ban): ?>
            <span class="badge-ban">Забанен</span>
        <?php endif; ?>
    </div>

    <div class="chat-messages" id="chatMessages">
        <?php if ($messages->isEmpty()): ?>
            <p class="chat-empty">Нет сообщений</p>
        <?php else: ?>
            <?php foreach ($messages as $msg): ?>
                <?php $isMine = $msg->sender_id == $user->id; ?>
                <div class="chat-message <?= $isMine ? 'mine' : 'theirs' ?>">
                    <?php if ($isMine && $user->avatar): ?>
                        <img class="chat-avatar" src="<?= app()->route->getUrl('/public/uploads/avatars/' . $user->avatar) ?>" alt="avatar">
                    <?php elseif (!$isMine && $msg->sender && $msg->sender->avatar): ?>
                        <img class="chat-avatar" src="<?= app()->route->getUrl('/public/uploads/avatars/' . $msg->sender->avatar) ?>" alt="avatar">
                    <?php else: ?>
                        <div class="chat-avatar-placeholder"><?= $isMine ? 'А' : mb_substr($chat->user->username ?? 'П', 0, 1) ?></div>
                    <?php endif; ?>
                    <div class="chat-message-content">
                        <div class="chat-message-sender"><?= htmlspecialchars($msg->sender->username ?? 'Пользователь') ?></div>
                        <div class="chat-message-text"><?= nl2br(htmlspecialchars($msg->text)) ?></div>
                        <div class="chat-message-time"><?= date('d.m.Y H:i', strtotime($msg->created_at)) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <form class="chat-form" action="<?= app()->route->getUrl('/admin/chat/' . $chat->id . '/send') ?>" method="post">
        <textarea class="chat-input" name="text" placeholder="Введите сообщение..." rows="2" required></textarea>
        <button class="chat-send" type="submit">Отправить</button>
    </form>

    <div class="admin-ban-section">
        <h3>Управление баном</h3>
        <?php if ($ban): ?>
            <div class="ban-info">
                <p><strong>Причина:</strong> <?= htmlspecialchars($ban->reason) ?></p>
                <?php if ($ban->is_permanent): ?>
                    <p><strong>Срок:</strong> навсегда</p>
                <?php else: ?>
                    <p><strong>Осталось:</strong> <?= $ban->getRemainingTime() ?></p>
                <?php endif; ?>
            </div>
            <form action="<?= app()->route->getUrl('/admin/unban/' . $chat->user_id) ?>" method="post" class="ban-form">
                <input type="hidden" name="chat_id" value="<?= $chat->id ?>">
                <button type="submit" class="btn-unban">Разбанить</button>
            </form>
        <?php else: ?>
            <form action="<?= app()->route->getUrl('/admin/ban/' . $chat->user_id) ?>" method="post" class="ban-form">
                <input type="hidden" name="chat_id" value="<?= $chat->id ?>">
                <div class="form-group">
                    <label for="reason">Причина бана</label>
                    <textarea name="reason" id="reason" rows="2" required></textarea>
                </div>
                <div class="form-group">
                    <label for="duration">Время (часы)</label>
                    <input type="number" name="duration" id="duration" min="1" max="720" value="1">
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="permanent" value="1"> Навсегда
                    </label>
                </div>
                <button type="submit" class="btn-ban">Забанить</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<script src="<?= app()->route->getUrl('/public/scripts/admin-chat.js') ?>"></script>
