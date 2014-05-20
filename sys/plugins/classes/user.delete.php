<?php

/**
 * Удаление юзера со всеми потрохами
 * использовать как $user->delete()
 * @param \user $ank текущий пользователь
 */
function user_delete($ank) {
    $tables = ini::read(H . '/sys/ini/user.tables.ini', true);
    foreach ($tables AS $v) {
        $res = DB::me()->prepare("DELETE FROM `" . my_esc($v['table']) . "` WHERE `" . my_esc($v['row']) . "` = ?");
        $res->execute(Array($ank->id));
    }
    $res = DB::me()->prepare("DELETE FROM `users` WHERE `id` = ?");
    $res->execute(Array($ank->id));
}

?>
