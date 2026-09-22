<?php

include_once '../sys/inc/start.php';
$doc = new document(1);
$doc->title = __('Смена пароля');

if (isset($_POST['save'])) {
    if (isset($_POST['password_old']) && crypt::hash($_POST['password_old'], $dcms->salt) === $user->password) {
        if (isset($_POST['password_new1']) && isset($_POST['password_new2'])) {
            if ($_POST['password_new1'] !== $_POST['password_new2'])
                $doc->err(__('Пароли не совпадают'));
            elseif (!is_valid::password($_POST['password_new1']))
                $doc->err(__('Не корректный новый пароль')); else {
                $user->password = crypt::hash($_POST['password_new1'], $dcms->salt);
                // смена пароля разлогинивает все запомненные устройства;
                // текущее остаётся запомненным, если было
                $had_token = !empty($_COOKIE[COOKIE_USER_TOKEN]);
                user_token::deleteAll($user->id);
                if ($had_token && ($token = user_token::create($user->id)))
                    user_token::setCookie($token);
                $doc->msg(__('Пароль успешно изменен'));
            }
        }
    } else
        $doc->err(__('Старый пароль неверен'));
}

$form = new form('?' . passgen());
$form->password('password_old', __('Старый пароль'));
$form->password('password_new1', __('Новый пароль'));
$form->password('password_new2', __('Подтверждение'));
$form->button(__('Применить'), 'save');
$form->display();

$doc->ret(__('Личное меню'), '/menu.user.php');