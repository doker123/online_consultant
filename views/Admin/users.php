<div class="admin-container">
    <h1>Пользователи</h1>

    <div class="admin-nav">
        <a href="<?= app()->route->getUrl('/admin') ?>" class="admin-nav-link">Дашборд</a>
        <a href="<?= app()->route->getUrl('/admin/chats') ?>" class="admin-nav-link">Чаты</a>
    </div>

    <div class="admin-users-list">
        <?php if ($users->isEmpty()): ?>
            <p>Нет пользователей</p>
        <?php else: ?>
            <?php foreach ($users as $u): ?>
                <?php $isBanned = $bans->has($u->id); ?>
                <div class="admin-user-item <?= $isBanned ? 'banned' : '' ?>">
                    <div class="admin-user-info">
                        <?php if ($u->avatar): ?>
                            <img class="admin-user-avatar" src="<?= app()->route->getUrl('/public/uploads/avatars/' . $u->avatar) ?>" alt="avatar">
                        <?php else: ?>
                            <div class="admin-user-avatar-placeholder"><?= strtoupper(mb_substr($u->username, 0, 1)) ?></div>
                        <?php endif; ?>
                        <div class="admin-user-details">
                            <span class="admin-user-name"><?= htmlspecialchars($u->username) ?></span>
                            <span class="admin-user-email"><?= htmlspecialchars($u->email) ?></span>
                            <span class="admin-user-date">Регистрация: <?= date('d.m.Y', strtotime($u->created_at)) ?></span>
                        </div>
                    </div>
                    <div class="admin-user-actions">
                        <?php if ($isBanned): ?>
                            <span class="badge-ban">Забанен</span>
                            <form action="<?= app()->route->getUrl('/admin/unban/' . $u->id) ?>" method="post" style="display:inline">
                                <button type="submit" class="btn-small btn-unban">Разбанить</button>
                            </form>
                        <?php else: ?>
                            <form action="<?= app()->route->getUrl('/admin/ban/' . $u->id) ?>" method="post" class="inline-ban-form">
                                <input type="text" name="reason" placeholder="Причина" class="input-small">
                                <input type="text" name="duration" placeholder="Часы" value="1" class="input-small">
                                <button type="submit" class="btn-small btn-ban">Забанить</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
