<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Онлайн-консультант</title>
    <?php $url = app()->route; ?>
    <link rel="stylesheet" href="<?= $url->getUrl('/public/css/layouts/main.css') ?>">
</head>
<body>
<?php use Src\Auth\Auth; use Model\Page; ?>
<header class="header">
    <nav class="header-nav">
        <a href="<?= app()->route->getUrl('/') ?>" class="header-logo">Онлайн-консультант</a>
        <div class="header-menu">
            <?php
            $cmsPages = Page::published()->orderBy('title')->get();
            foreach ($cmsPages as $cmsPage): ?>
                <a href="<?= app()->route->getUrl('/page/' . $cmsPage->slug) ?>"><?= htmlspecialchars($cmsPage->title) ?></a>
            <?php endforeach; ?>
            <?php if (Auth::check()): ?>
                <?php if (Auth::isAdmin()): ?>
                    <a href="<?= app()->route->getUrl('/admin') ?>">Админ-панель</a>
                <?php else: ?>
                    <a href="<?= app()->route->getUrl('/chat') ?>">Чат</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <div class="nav-user">
            <?php if (!Auth::check()): ?>
                <a href="<?= app()->route->getUrl('/signup') ?>">Регистрация</a>
                <a href="<?= app()->route->getUrl('/login') ?>">Вход</a>
            <?php else: ?>
                <?php
                $roleNames = [
                    'admin' => 'Администратор',
                    'user' => 'Пользователь',
                ];
                $userType = Auth::getUserType();
                $roleName = $roleNames[$userType] ?? 'Неизвестный';
                $displayName = Auth::getDisplayName();
                ?>
                <span class="user-info"><?= $roleName ?>: <?= htmlspecialchars($displayName) ?></span>
                <a href="<?= app()->route->getUrl('/logout') ?>">Выход</a>
            <?php endif; ?>
        </div>
    </nav>
</header>

<div class="content">
    <?= $content ?? "" ?>
</div>
</body>
</html>
