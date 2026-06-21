<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Аспирантура</title>
    <?php $url = app()->route; ?>
    <link rel="stylesheet" href="<?= $url->getUrl(
        "/public/css/layouts/main.css",
    ) ?>">
</head>
<body>
<?php use Src\Auth\Auth; ?>
<header class="header">
    <nav class="header-nav">
        <a href="<?= app()->route->getUrl("/") ?>">Главная</a>
        <?php if (Auth::check()): ?>
            <?php $userType = Auth::getUserType(); ?>
            <div class="header-nav__menu">
                <?php if ($userType === "admin"): ?>

                <?php elseif ($userType === "user"): ?>

                <?php endif; ?>

            </div>
        <?php endif; ?>
        <div class="nav-user">
            <?php if (!Auth::check()): ?>
            <div>

            </div>
            <div>
                <a class="signup" href="<?= app()->route->getUrl(
                    "/signup",
                ) ?>">Регистрация</a>
                <a class="login" href="<?= app()->route->getUrl(
                    "/login",
                ) ?>">Вход</a>
            </div>
            <?php else: ?>
                <?php
                $roleNames = [
                    "admin" => "Администратор",
                    "user" => "Пользователь",
                ];
                $roleName = $roleNames[$userType] ?? "Неизвестный";
                $displayName = Auth::getDisplayName();
                ?>
                <span class="user-role"><?= $roleName ?>: <?= htmlspecialchars(
                        $displayName,
                    ) ?></span>
                <a class="logout" href="<?= app()->route->getUrl(
                    "/logout",
                ) ?>">Выход</a>
            <?php endif; ?>
        </div>
    </nav>
</header>

<div class="content">
    <?= $content ?? "" ?>
</div>
</body>
</html>
