<?php /** @var $chat Chat */ /** @var $messages Message[] */ /** @var $ban Ban */ /** @var $adminOnline bool */ ?>
<div class="chat-container">
    <div class="chat-header">
        <h2>Чат с поддержкой</h2>
        <span class="admin-status <?= $adminOnline ? 'online' : 'offline' ?>">
            <?= $adminOnline ? 'Администратор онлайн' : 'Администратор оффлайн' ?>
        </span>
    </div>

    <?php if ($ban): ?>
        <div class="ban-notice">
            <strong>Вы заблокированы!</strong>
            <p>Причина: <?= htmlspecialchars($ban->reason) ?></p>
            <?php if ($ban->is_permanent): ?>
                <p>Срок: навсегда</p>
            <?php else: ?>
                <p>Оставшееся время: <?= $ban->getRemainingTime() ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="chat-messages" id="chatMessages"
         data-chat-id="<?= $chat->id ?>"
         data-user-id="<?= $user->id ?>"
         data-user-avatar="<?= $user->avatar ?? '' ?>"
         data-user-initial="<?= mb_substr($user->username, 0, 1) ?>"
         data-last-message-id="<?= $messages->isNotEmpty() ? $messages->last()->id : 0 ?>"
         data-avatars-url="<?= app()->route->getUrl('/public/uploads/avatars/') ?>"
         data-messages-url="<?= app()->route->getUrl('/chat/messages/' . $chat->id) ?>">
        <?php if ($messages->isEmpty()): ?>
            <p class="chat-empty">Нет сообщений. Начните диалог!</p>
        <?php else: ?>
            <?php foreach ($messages as $msg): ?>
                <?php $isMine = $msg->sender_id == $user->id; ?>
                <div class="chat-message <?= $isMine ? 'mine' : 'theirs' ?>" data-id="<?= $msg->id ?>">
                    <?php if ($isMine && $user->avatar): ?>
                        <img class="chat-avatar" src="<?= app()->route->getUrl('/public/uploads/avatars/' . $user->avatar) ?>" alt="avatar">
                    <?php elseif (!$isMine && $msg->sender && $msg->sender->avatar): ?>
                        <img class="chat-avatar" src="<?= app()->route->getUrl('/public/uploads/avatars/' . $msg->sender->avatar) ?>" alt="avatar">
                    <?php else: ?>
                        <div class="chat-avatar-placeholder"><?= $isMine ? mb_substr($user->username, 0, 1) : 'А' ?></div>
                    <?php endif; ?>
                    <div class="chat-message-content">
                        <div class="chat-message-sender"><?= htmlspecialchars($msg->sender->username ?? 'Админ') ?></div>
                        <div class="chat-message-text"><?= nl2br(htmlspecialchars($msg->text)) ?></div>
                        <div class="chat-message-time"><?= date('d.m.Y H:i', strtotime($msg->created_at)) ?></div>
                        <?php if (!$isMine && !$msg->is_read): ?>
                            <span class="chat-message-status">не прочитано</span>
                        <?php elseif (!$isMine && $msg->is_read): ?>
                            <span class="chat-message-status read">прочитано</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if (!$ban): ?>
        <form class="chat-form" action="<?= app()->route->getUrl('/chat/send') ?>" method="post">
            <textarea class="chat-input" name="text" id="chatInput" placeholder="Введите сообщение..." rows="2"></textarea>
            <input type="hidden" name="csrf_token" value="<?= \Src\Session::get('csrf_token') ?>">
            <button class="chat-send" type="submit">Отправить</button>
        </form>
    <?php else: ?>
        <div class="chat-form-disabled">
            <p>Отправка сообщений заблокирована</p>
        </div>
    <?php endif; ?>

    <div class="chat-profile">
        <h3>Ваш профиль</h3>
        <div class="chat-profile-avatar">
            <?php if ($user->avatar): ?>
                <img src="<?= app()->route->getUrl('/public/uploads/avatars/' . $user->avatar) ?>" alt="avatar" class="profile-avatar-img">
            <?php else: ?>
                <div class="chat-avatar-placeholder large">А</div>
            <?php endif; ?>
        </div>
        <form action="<?= app()->route->getUrl('/admin/avatar') ?>" method="post" enctype="multipart/form-data" class="avatar-form">
            <input type="file" name="avatar" accept="image/*">
            <input type="hidden" name="csrf_token" value="<?= \Src\Session::get('csrf_token') ?>">
            <button type="submit" class="btn-small">Загрузить аватар</button>
        </form>
    </div>
</div>

<script src="<?= app()->route->getUrl('/public/scripts/chat.js') ?>"></script>
