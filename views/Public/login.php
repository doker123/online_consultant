<div class="auth-block">
    <h2 class="auth-block__title">Вход</h2>

    <?php if (!empty($error)): ?>
        <div class="auth-errors">
            <p class="auth-error"><?= htmlspecialchars($error) ?></p>
        </div>
    <?php endif; ?>

    <form class="auth-form" action="<?= app()->route->getUrl(
        "/login",
    ) ?>" method="post">
        <label class="auth-form__label" for="username">Имя</label>
        <input class="auth-form__input" type="text" name="username" id="username" placeholder="Имя пользователя"
            value="<?= htmlspecialchars($old_login ?? "") ?>">
        <label class="auth-form__label" for="password">Пароль</label>
        <input class="auth-form__input" type="password" name="password" id="password" placeholder="Пароль">
        <input type="hidden" name="csrf_token" value="<?= \Src\Session::get(
            "csrf_token",
        ) ?>">
        <button class="auth-form__button" type="submit">Войти</button>
    </form>
    <p class="auth-block__paragraph">Нет аккаунта?
       <a class="auth-block__link" href="<?= app()->route->getUrl(
           "/signup",
       ) ?>">Зарегистрироваться</a>
    </p>
</div>
