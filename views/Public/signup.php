<div class="signup-block">
    <h2 class="signup-block__title">Регистрация</h2>
    <form class="signup-form" action="/signup" method="post">
        <label class="signup-form__label" for="username">Имя</label>
        <input class="signup-form__input" type="text" name="username" id="username" placeholder="Имя пользователя" required>
        <label class="signup-form__label" for="email">Почта</label>
        <input class="signup-form__input" type="email" name="email" id="email" placeholder="Email" required>
        <label class="signup-form__label" for="password">Пароль</label>
        <input class="signup-form__input" type="password" name="password" id="password" placeholder="Пароль" required>
        <label></label>
        <select>
            
        </select>
            
        <button class="signup-form__button" type="submit">Зарегистрироваться</button>
    </form>
    <p class='signup-block__paragraph'>У вас есть аккаунт в системе?
       <a class='signup-block__link' href="#">Авторизация</a>
    </p>
</div>
