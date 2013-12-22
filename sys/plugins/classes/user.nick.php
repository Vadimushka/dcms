<?php

/**
 * Возвращает ник пользователя
 * Использовать как $user->nick()
 * @param \user $user
 * @param array $args
 * @return string Ник пользователя
 */
function user_nick($user, $args) {

    if ($user->id === false) {
        return '[' . __('Пользователь удален') . ']';
    }

    $classes = array();
    $classes[] = $user->online ? 'DCMS_nick_on' : 'DCMS_nick_off';

    return '<span class="' . implode(' ', $classes) . '">' . $user->login . '</span>'.($user->donate_rub?'<span class="DCMS_nick_donate"></span>':'');
}