Custom registration form

Task #63754

Описание

Создать плагин с шорткодом который можно будет добавлять на любую страницу, для отображения формы регистрации на сайте. Вся обработка на аяксе

Описание :
1 Форма должна содержать в себе 6 полей: Логин, Имя, Фамилия, Почта, Пароль, Подтвердить Пароль и кнопку зарегистрировать

Валидация:
1. Все поля являются обязательными, если поле пустое, то подсветить красной окантовкой и вывести сообщение под полем, что нужно заполнить поле.
2. Логин необходимо проверить в базе данных и если такой уже занят, то выдавать сообщение под этим полем - "Sorry, login is already in use. Try another".
3. С почтой такая же проверка на наличие в базе данных. И еще это поле должно быть формата почты. "Sorry, email is already in use. Try another" 
4. Пароль должен содержать только латинские буквы, 1 цифру и 1 символ из списка ( # , % , & , * , ? ). При начале ввода пароля, пользователю необходимо показать сообщение с этой информацией.
"The password must contain only Latin letters, 1 number, and 1 character from the list ( # , % , & , * , ? )" 
5. Пароли должны совпадать. Если это не так, то 2 пароля красными рамками и под вторым писать сообщение что не совпадает.
6. Так же должна быть нонса для безопасности.

Обработка аякса:
Если все проверки прошли успешно, то создать юзера.



Добавить свою(мою) почту для уведомления и прислать письмо ( проверка прошла успешно, пользователь создан ). Для почты использовать кастомный код для SMTP. Токен в своем аккаутне гугла сделайте.

После создания необходимо показать сообщение в попапе с текстом: “Thank you for registering on our website” и через 5 секунд закрываем попап автоматически и редиректим пользователя на главную страницу.