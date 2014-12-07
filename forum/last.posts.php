<?php

include_once '../sys/inc/start.php';
$doc = new document();
$doc->title = __('Новые сообщения');

$today = mktime(0, 0, 0);
$yesterday = $today - 3600 * 24;

$cache_id = 'forum.last.posts_all';

if (false === ($posts_all = cache::get($cache_id))) {
    $posts_all = array();
    $q = $db->prepare("SELECT `th`.* ,
        `tp`.`name` AS `topic_name`,
        `cat`.`name` AS `category_name`,
        `tp`.`group_write` AS `topic_group_write`,
            GREATEST(`th`.`group_show`, `tp`.`group_show`, `cat`.`group_show`, `msg`.`group_show`) AS `group_show`,
            COUNT(DISTINCT `msg`.`id`) AS `count`,     
            (SELECT COUNT(*) FROM `forum_messages` AS `msg` WHERE `msg`.`id_theme` = `th`.`id` AND `msg`.`time` > :time) AS `count_new`,
            (SELECT COUNT(`fv`.`id_user`) FROM `forum_views` AS `fv` WHERE `fv`.`id_theme` = `msg`.`id_theme`)  AS `views`            
FROM `forum_messages` AS `msg`
LEFT JOIN `forum_themes` AS `th` ON `th`.`id` = `msg`.`id_theme`
LEFT JOIN `forum_topics` AS `tp` ON `tp`.`id` = `th`.`id_topic`
LEFT JOIN `forum_categories` AS `cat` ON `cat`.`id` = `th`.`id_category`
WHERE `th`.`time_last` > :time
GROUP BY `msg`.`id_theme`
ORDER BY `th`.`time_last` DESC");
    $q->execute(Array(':time' => TIME - 3600 * 24 * 7));

    $posts_all = $q->fetchAll();

    cache::set($cache_id, $posts_all, 20);
}

$count = count($posts_all);
$posts_for_view = array();
for ($i = 0; $i < $count; $i++) {
    if ($posts_all[$i]['group_show'] > $user->group) {
        continue;
    }
    $posts_for_view[] = $posts_all[$i];
}


$views = array();
$themes = array();
$count_posts = count($posts_for_view);
$users_for_preload = array();
if ($count_posts && $user->id) {
    for ($i = 0; $i < $count_posts; $i++) {
        $themes[] = $posts_for_view[$i]['id'];
        $users_for_preload[] = $posts_for_view[$i]['id_autor'];
        $users_for_preload[] = $posts_for_view[$i]['id_last'];
    }

    $q = $db->prepare("SELECT `id_theme`, MAX(`time`) AS `time` FROM `forum_views`  WHERE `id_user` = ? AND `id_theme` IN (" . implode(',', $themes) . ") GROUP BY `id_theme`");
    $q->execute(Array($user->id));
    while ($view = $q->fetch()) {
        $views[$view['id_theme']] = $view['time'];
    }
}

new user($users_for_preload); // предзагрузка всех возможных пользователей одним SQL запросом

$pages = new pages($count_posts);
$start = $pages->my_start();
$end = $pages->end();


$listing = new listing();

for ($z = $start; $z < $end && $z < $pages->posts; $z++) {
    $theme = $posts_for_view[$z];

    if (!isset($msg_today) && $theme['time_last'] >= $today) {
        $post = $listing->post();
        $post->highlight = true;
        $post->title = __("Сегодня");
        $msg_today = true;
    }
    if (!isset($msg_yesterday) && $theme['time_last'] < $today && $theme['time_last'] >= $yesterday) {
        if ($listing->count()) {
            $listing->display();
            $listing = new listing();
        }

        $post = $listing->post();
        $post->highlight = true;
        $post->title = __("Вчера");
        $msg_yesterday = true;
    }
    if (!isset($msg_week) && $theme['time_last'] < $yesterday) {
        if ($listing->count()) {
            $listing->display();
            $listing = new listing();
        }

        $post = $listing->post();
        $post->highlight = true;
        $post->title = __("Неделя");
        $msg_week = true;
    }


    $post = $listing->post();

    if ($user->id) {
        if (isset($views[$theme['id']])) {
            $post->highlight = $theme['time_last'] > $views[$theme['id']];
        } else {
            $post->highlight = true;
        }
    }

    $is_open = (int)($theme['group_write'] <= $theme['topic_group_write']);

    $post->icon("forum.theme.{$theme['top']}.$is_open.png");
    $post->time = misc::when($theme['time_last']);
    $post->title = text::toValue($theme['name']);
    $post->counter = '+' . $theme['count_new'];
    $post->url = 'theme.php?id=' . $theme['id'] . '&amp;page=end';
    $autor = new user($theme['id_autor']);
    $last_msg = new user($theme['id_last']);
    $post->content = ($autor->id != $last_msg->id ? $autor->nick . '/' . $last_msg->nick : $autor->nick) . '<br />';
    $post->content .= "(<a href='category.php?id=$theme[id_category]'>" . text::toValue($theme['category_name']) . "</a> &gt; <a href='topic.php?id=$theme[id_topic]'>" . text::toValue($theme['topic_name']) . "</a>)<br />";
    $post->bottom = __('Просмотров: %s', $theme['views']);

    if (!$doc->last_modified)
        $doc->last_modified = $theme['time_last'];
}

$listing->display(__('Сообщений не найдено'));
$pages->display('?');
$doc->ret(__('Форум'), './');