<?php

function user_nick($user, $args) {

    if ($user->id === false) {
        return '[' . __('Пользователь удален') . ']';
    }

    return '<span class="' . ($user->online ? 'DCMS_nick_on' : 'DCMS_nick_off') . '">' . $user->login . '</span>';
}

?>
