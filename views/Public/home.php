<div class="home-block">
    <h1>Онлайн-консультант</h1>
    <p>Добро пожаловать на сайт онлайн-консультанта.</p>

    <?php use Src\Auth\Auth; ?>

    <?php if (!Auth::check()): ?>
        <p>Для начала работы необходимо <a href="<?= app()->route->getUrl('/login') ?>">войти</a> или <a href="<?= app()->route->getUrl('/signup') ?>">зарегистрироваться</a>.</p>
    <?php elseif (Auth::isAdmin()): ?>
        <p>Вы вошли как администратор. <a href="<?= app()->route->getUrl('/admin') ?>">Перейти в админ-панель</a>.</p>
    <?php else: ?>
        <p>Для общения с поддержкой перейдите в <a href="<?= app()->route->getUrl('/chat') ?>">чат</a>.</p>
    <?php endif; ?>
</div>
