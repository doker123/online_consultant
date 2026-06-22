<?php /** @var $page Page|null */ /** @var $errors array */ /** @var $old array */ ?>
<div class="admin-container">
    <h1><?= $page ? 'Редактировать страницу' : 'Создать страницу' ?></h1>

    <div class="admin-nav">
        <a href="<?= app()->route->getUrl('/admin') ?>" class="admin-nav-link">Дашборд</a>
        <a href="<?= app()->route->getUrl('/admin/pages') ?>" class="admin-nav-link">Страницы</a>
    </div>

    <div class="admin-form">
        <?php if (!empty($errors)): ?>
            <div class="form-errors">
                <?php foreach ($errors as $error): ?>
                    <p class="form-error"><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="<?= app()->route->getUrl($page ? '/admin/pages/' . $page->id . '/edit' : '/admin/pages/create') ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Название</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($old['title'] ?? ($page->title ?? '')) ?>" class="form-input">
            </div>

            <div class="form-group">
                <label for="slug">Slug (URL)</label>
                <input type="text" id="slug" name="slug" value="<?= htmlspecialchars($old['slug'] ?? ($page->slug ?? '')) ?>" class="form-input" placeholder="auto-generated if empty">
                <small class="form-help">Оставьте пустым для автогенерации из названия</small>
            </div>

            <div class="form-group">
                <label for="image">Картинка</label>
                <?php if ($page && $page->image): ?>
                    <div class="form-image-preview">
                        <img src="<?= app()->route->getUrl('/public/uploads/pages/' . $page->image) ?>" alt="Текущая картинка">
                    </div>
                <?php endif; ?>
                <input type="file" id="image" name="image" accept="image/*" class="form-input">
                <small class="form-help">JPG, PNG, GIF, WebP. Макс. рекомендуемый размер — 2 МБ</small>
            </div>

            <div class="form-group">
                <label for="content">Контент</label>
                <textarea id="content" name="content" rows="15" class="form-textarea"><?= htmlspecialchars($old['content'] ?? ($page->content ?? '')) ?></textarea>
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_published" value="1" <?= (($old['is_published'] ?? ($page->is_published ?? 1)) ? 'checked' : '') ?>>
                    Опубликовано
                </label>
            </div>
            <input type="hidden" name="csrf_token" value="<?= \Src\Session::get('csrf_token') ?>">
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Сохранить</button>
                <a href="<?= app()->route->getUrl('/admin/pages') ?>" class="btn btn-secondary">Отмена</a>
            </div>
        </form>
    </div>
</div>
