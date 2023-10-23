<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'login' => 'Вход',
    'enter' => 'Войти',
    'register' => 'Зарегистрироваться',
    'logout' => 'Выход',
    'failed' => 'Неверный логин или пароль.',
    'throttle' => 'Слишком много попыток. Пожалуйста, попробуйте через :seconds секунд.',
    'register_subj' => 'Подтверждение регистрации на сайте '.env('app.name'),
    'check_your_mail' => 'Проверьте вашу почту, Вам было отправлено письмо с подтверждением регистрации!',
    'your_account_is_blocked' => 'Ваш аккаунт был заблокирован администрацией!',
    'register_error' => 'Ошибка проверки адреса электронной почты. Вы, возможно, использовали устаревшую ссылку подтверждения регистрации.',
    'register_success' => 'Поздравляем, Вы успешно завершили регистрацию!',

    'confirm_register_head' => 'Подтверждение регистрации',
    'confirm_register_part1' => 'Если Вы не регистрировались на нашем сайте, просто игнорируйте это письмо. Если регистрация осуществляется вами, то вы должны подтвердить свою регистрацию и таким образом активировать свою учетную запись.',
    'confirm_register_part2' => 'Для завершения регистрации кликните по следующей ссылке:',

    'send_password_reset_link' => 'Сбросить пароль',
    'send_confirm_mail' => 'Выслать письмо с подтверждением',
    'new_password' => 'Новый пароль',
    'save_new_password' => 'Сохранить новый пароль',
    'new_password_success' => 'Ваш пароль успещно изменен!',

    'new_user' => 'Новый пользователь',
    'edit_user' => 'Редактирование пользователя',
    'new_user_is_confirm' => 'Подтвержден новый пользователь',
    'user_with_this_phone_is_already_registered' => 'Пользователь с таким телефоном уже зарегистрирован!',
    'wrong_old_password' => 'Неверный текущий пароль!',
    'wrong_phone' => 'Неверный телефон или пользователь не активирован!',
    'wrong_code' => 'Код неверный!',
    'register_complete' => 'Вы успешно прошли регистрацию!',
//    'new_email' => '. Новый E-mail: :email у id# :id',
//    'new_password' => '. Новый пароль: :password у id# :id',

    'messages' => 'Сообщения',
    'my_subscriptions' => 'Мои подписки',
    'my_orders' => 'Мои запросы',
    'my_help' => 'Моя помощь',
    'incentives' => 'Поощрения от партнеров',

    'active_orders' => 'Активные',
    'completed_orders' => 'Завершенные',
    'approving_orders' => 'На модерации',
    'archive_orders' => 'Архив',

    'name' => 'Ваше имя',
    'enter_your_name' => 'Введите ваше имя',
    'family' => 'Ваша фамилия',
    'enter_your_family' => 'Введите вашу фамилию',
    'phone' => 'Телефон',
    'email' => 'E-Mail',
    'enter_your_email' => 'Введите ваш E-Mail',
    'born' => 'Дата рождения',
    'enter_your_born' => 'Введите вашу дату рождения',
    'info_about' => 'Информация о себе',
    'code' => 'Код',
    'get_code' => 'Получить код',
    'get_code_again' => 'Получить код повторно можно через <span></span> секунд',
    'old_password' => 'Старый пароль',
    'old_password_error' => 'Неверный старый пароль!',
    'new_password_has_been_sent_to_your_phone' => 'Новый пароль выслан вам на телефон',
    'password' => 'Пароль',
    'confirm_password' => 'Подтверждение пароля',
    'status' => 'Статус',
    'password_confirm' => 'Повтор пароля',
    'password_mismatch' => 'Пароли не совпадают',
    'password_must_be_entered' => 'Пароль должен быть введен',
    'password_cannot_be_less' => 'Пароль не может быть меньше :length символов',
    'you_must_consent_to_the_processing_of_personal_data' => 'Вы должны дать согласие на обработку персональных данных',
    'remember_me' => 'Запомнить меня',
    'forgot_your_password' => 'Забыли свой пароль?',
    'forgot_password_text' => 'Если Вы забыли свой пароль, не волнуйтесь! Мы можем легко его восстановить.',
    'forgot_password_link' => 'Просто кликните сюда!',
    'have_not_account' => 'У Вас нет учетной записи?',
    'register_text' => 'Просто кликните сюда для регистрации!',
    'settings_head1' => 'Вы можете изменить свое имя и e-mail',
    'settings_head2' => 'Вы можете изменить свой пароль',
    'change_phone' => 'Изменить телефон',
    'change_password' => 'Изменить пароль',
    'phone_has_been_changed' => 'Телефон был успешно изменен!',
    'password_has_been_changed' => 'Пароль был успешно изменен!',
    'keep_password' => 'Если вы не хотите менять пароль, то оставте поля «Пароль» и «Повтор пароля» не заполненными',
    'login_to_your_account' => 'Войдите в свой аккаунт',
    'login_head' => 'Пожалуйста, войдите,<br> используя свой E-mail и пароль.',
    'register_head' => 'Пожалуйста, заполните все поля<br>и пройдите регистрацию',
    'reset_password' => 'Восстановление пароля',
    'restore_password' => 'Восстановить пароль',
    'reset_password_head' => 'Введите зарегистрированный E-mail<br>для сброса пароля',
    'new_password_confirm' => 'Повторите новый пароль',

    'confirm_mail' => 'Повторное подтверждение регистрации',
    'confirm_mail_head' => 'Введите зарегистрированный E-mail<br>для подтверждения регистрации',

    'reset_password_message' => 'Вы получили это письмо, потому что мы получили запрос на восстановление пароля для вашей учетной записи.',
    'reset_password_ignore_message' => 'Если вы не запрашивали сброс пароля, никаких дополнительных действий не требуется.',

    'user_already_active' => 'Пользователь уже активирован!',
    'user_not_active' => 'Вы не активировали свой аккаунт.<br>Пройдите по <a href="/send-confirm-mail">ссылке</a>, что получить повторно письмо с подтверждением регистрации.',

    'capcha-error' => 'Не верная капча!',
];
