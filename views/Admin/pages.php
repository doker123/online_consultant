<?php /** @var $pages Page[] */ ?>
<div class="admin-container">
    <h1>Страницы</h1>

    <div class="admin-nav">
        <a href="<?= app()->route->getUrl('/admin') ?>" class="admin-nav-link">Дашборд</a>
        <a href="<?= app()->route->getUrl('/admin/chats') ?>" class="admin-nav-link">Чаты</a>
        <a href="<?= app()->route->getUrl('/admin/users') ?>" class="admin-nav-link">Пользователи</a>
        <a href="<?= app()->route->getUrl('/admin/pages') ?>" class="admin-nav-link active">Страницы</a>
    </div>

    <div class="admin-actions">
        <a href="<?= app()->route->getUrl('/admin/pages/create') ?>" class="btn btn-primary">Создать страницу</a>
    </div>

    <div class="admin-pages-list">
        <?php if ($pages->isEmpty()): ?>
            <p>Нет страниц</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Название</th>
                        <th>Slug</th>
                        <th>Статус</th>
                        <th>Создана</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pages as $page): ?>
                        <tr class="<?= $page->is_published ? '' : 'draft' ?>">
                            <td><?= htmlspecialchars($page->title) ?></td>
                            <td><code><?= htmlspecialchars($page->slug) ?></code></td>
                            <td>
                                <?php if ($page->is_published): ?>
                                    <span class="badge-published">Опубликовано</span>
                                <?php else: ?>
                                    <span class="badge-draft">Черновик</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d.m.Y H:i', strtotime($page->created_at)) ?></td>
                            <td class="admin-table-actions">
                                <a href="<?= app()->route->getUrl('/admin/pages/' . $page->id . '/edit') ?>" class="btn-small btn-edit">Редактировать</a>
                                <form action="<?= app()->route->getUrl('/admin/pages/' . $page->id . '/toggle') ?>" method="post" style="display:inline">
                                    <button type="submit" class="btn-small <?= $page->is_published ? 'btn-draft' : 'btn-publish' ?>">
                                        <?= $page->is_published ? 'Снять с публикации' : 'Опубликовать' ?>
                                    </button>
                                </form>
                                <form action="<?= app()->route->getUrl('/admin/pages/' . $page->id . '/delete') ?>" method="post" style="display:inline" onsubmit="return confirm('Удалить страницу?')">
                                    <button type="submit" class="btn-small btn-delete">Удалить</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
