<div class="auth-block">
    <h2 class="auth-block__title">Вход</h2>
    <form class="auth-form" action="/login" method="post">
        <label class="auth-form__label" for="username">Имя</label>
        <input class="auth-form__input" type="text" name="username" id="username" placeholder="Имя пользователя" required>
        <label class="auth-form__label" for="password">Пароль</label>
        <input class="auth-form__input" type="password" name="password" id="password" placeholder="Пароль" required>
        <button class="auth-form__button" type="submit">Войти</button>
    </form>
    <p class='auth-block__paragraph'>У вас есть аккаунт в системе?
       <a class='auth-block__link' href="#">Авторизация</a>
    </p>
</div>
