<?php

/**
 * Проверка на то, является ли пользователь другом
 * Использовать как $user->is_friend($ank)
 * @param \user $user
 * @param array $args
 * @return boolean
 */
function user_is_friend($user, $args) {
    if (!$user->id) {
        return false;
    }

    $ank = empty($args[0]) ? false : $args[0];

    if (!is_object($ank)) {
        $ank = $user;
    }
    if (!$ank->id) {
        return false;
    }
    if ($user->id && $user->id === $ank->id) {
        return true;
    }
    $res = DB::me()->prepare("SELECT COUNT(*) AS cnt FROM `friends` WHERE `id_user` = ? AND `id_friend` = ? AND `confirm` = '1' LIMIT 1");
    $res->execute(Array($user->id, $ank->id));
    return ($row = $res->fetch()) ? $row['cnt'] : false;
}

?>
