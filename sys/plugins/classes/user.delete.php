<?php

/**
 * Удаление юзера со всеми потрохами
 * @param type $ank
 * @param type $args
 */
function user_delete($ank, $args) {
    $tables = ini::read(H . '/sys/ini/user.tables.ini', true);
    foreach ($tables AS  $v) {
        mysql_query("DELETE FROM `" . my_esc($v['table']) . "` WHERE `" . my_esc($v['row']) . "` = '$ank->id'");
    }
    mysql_query("DELETE FROM `users` WHERE `id` = '$ank->id'");
}

?>
