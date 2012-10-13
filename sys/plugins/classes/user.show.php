<?php

function user_show($user, $args) {

    if ($user->id !== false) {
        return '<a href="/profile.view.php?id=' . $user->id . '">' . $user->nick() . '</a>';
    } else {
        return '[Нет данных]';
    }
}

?>
