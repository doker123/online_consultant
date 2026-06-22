<?php /** @var $errors array */ /** @var $old array */ ?>
<div class="auth-block">
    <h2 class="auth-block__title">Регистрация</h2>

    <?php if (!empty($errors)): ?>
        <div class="auth-errors">
            <?php foreach ($errors as $error): ?>
                <p class="auth-error"><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form class="auth-form" action="<?= app()->route->getUrl('/signup') ?>" method="post">
        <label class="auth-form__label" for="username">Имя</label>
        <input class="auth-form__input" type="text" name="username" id="username" placeholder="Имя пользователя" value="<?= htmlspecialchars($old['username'] ?? '') ?>">

        <label class="auth-form__label" for="email">Почта</label>
        <input class="auth-form__input" type="text" name="email" id="email" placeholder="Email" value="<?= htmlspecialchars($old['email'] ?? '') ?>">

        <label class="auth-form__label" for="password">Пароль</label>
        <input class="auth-form__input" type="password" name="password" id="password" placeholder="Минимум 6 символов">
        <input type="hidden" name="csrf_token" value="<?= \Src\Session::get('csrf_token') ?>">

        <button class="auth-form__button" type="submit">Зарегистрироваться</button>
    </form>
    <p class="auth-block__paragraph">Уже есть аккаунт?
       <a class="auth-block__link" href="<?= app()->route->getUrl('/login') ?>">Войти</a>
    </p>
</div>
