<?php

abstract class groups {

    static function load_ini() {
        static $ini;
        if (!isset($ini)) {
            $ini = ini::read(H . '/sys/ini/groups.ini', true);
        }
        return $ini;
    }

    static function name($group) {
        $ini = self::load_ini();
        if (isset($ini[$group]['name']))
            return $ini[$group]['name'];
        return 'Ошибка группы';
    }

    static function max() {
        return max(array_keys(self::load_ini()));
    }

    static function getAdmins($group = false) {
        $users = array();
        if ($group === false) {
            $group = self::max();
        }
        $group = (int) $group;

        $q = mysql_query("SELECT `id` FROM `users` WHERE `group` >= '$group'");
        while ($us = mysql_fetch_assoc($q)) {
            $users[] = new user($us['id']);
        }
        return $users;
    }

}

?>
