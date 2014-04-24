<?php

abstract class forum
{

    /**
     * @return int кол-во пользователей, находящихся на форуме
     */
    static function getCountUsers()
    {
        return (int)mysql_result(mysql_query("SELECT COUNT(*) FROM `users_online` WHERE `request` LIKE '/forum/%'"), 0);
    }

    /**
     * Кол-во обновленных тем пользователя
     * @param $user
     * @param bool|int $time_from
     * @return int
     */
    static function getCountFreshUserThemes($user, $time_from = false)
    {
        if ($time_from === false)
            $time_from = NEW_TIME;

        return (int)mysql_result(mysql_query("SELECT COUNT(DISTINCT(`msg`.`id_theme`))
            FROM `forum_messages` AS `msg`
            LEFT JOIN `forum_themes` AS `th` ON `th`.`id` = `msg`.`id_theme`
            LEFT JOIN `forum_topics` AS `tp` ON `tp`.`id` = `th`.`id_topic`
            LEFT JOIN `forum_categories` AS `cat` ON `cat`.`id` = `th`.`id_category`
            WHERE `th`.`id_autor` = '{$user->id}'
            AND `th`.`group_show` <= '{$user->group}'
            AND `tp`.`group_show` <= '{$user->group}'
            AND `cat`.`group_show` <= '{$user->group}'
            AND `msg`.`group_show` <= '{$user->group}'
            AND `msg`.`id_user` <> '{$user->id}'
            AND `msg`.`time` > '" . $time_from . "'"), 0);
    }

    /**
     * Кол-во новых тем
     * @param \user $user
     * @param bool|int $time_from
     * @return string
     */
    static function getCountNewThemes($user, $time_from = false)
    {
        if ($time_from === false)
            $time_from = NEW_TIME;

        return (int)mysql_result(mysql_query("SELECT COUNT(*)
            FROM `forum_themes` AS `th`
            LEFT JOIN `forum_topics` AS `tp` ON `tp`.`id` = `th`.`id_topic`
            LEFT JOIN `forum_categories` AS `cat` ON `cat`.`id` = `th`.`id_category`
            WHERE `th`.`group_show` <= '{$user->group}'
            AND `tp`.`group_show` <= '{$user->group}'
            AND `cat`.`group_show` <= '{$user->group}'
            AND `th`.`time_create` > '" . $time_from . "'"), 0);
    }

    /**
     * Кол-во обновленных тем
     * @param \user $user
     * @param bool|int $time_from
     * @return int
     */
    static function getCountFreshThemes($user, $time_from = false)
    {
        if ($time_from === false)
            $time_from = NEW_TIME;

        return (int)mysql_result(mysql_query("SELECT COUNT(DISTINCT(`msg`.`id_theme`))
            FROM `forum_messages` AS `msg`
            LEFT JOIN `forum_themes` AS `th` ON `th`.`id` = `msg`.`id_theme`
            LEFT JOIN `forum_topics` AS `tp` ON `tp`.`id` = `th`.`id_topic`
            LEFT JOIN `forum_categories` AS `cat` ON `cat`.`id` = `th`.`id_category`
            WHERE `th`.`group_show` <= '{$user->group}'
            AND `tp`.`group_show` <= '{$user->group}'
            AND `cat`.`group_show` <= '{$user->group}'
            AND `msg`.`group_show` <= '{$user->group}'
            AND `msg`.`time` > '" . $time_from . "'"), 0);
    }
} 