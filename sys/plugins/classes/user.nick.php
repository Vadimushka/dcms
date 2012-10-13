<?php

function user_nick($user, $args) {

    if ($user->id === false) {
        return '[' . __('Пользователь удален') . ']';
    }

    return '<span class="' . ($user->online ? 'nick_on' : 'nick_off') . '">' . $user->login . '</span>';
}

?>
