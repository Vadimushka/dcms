<?php

defined('DCMS') or die;
global $user;
$db = DB::me();
if (false === ($new_posts = cache_counters::get('forum.new_posts.' . $user->group))) {
    $res = $db->prepare("SELECT COUNT(DISTINCT(`msg`.`id_theme`)) AS cnt
FROM `forum_messages` AS `msg`
LEFT JOIN `forum_themes` AS `th` ON `th`.`id` = `msg`.`id_theme`
LEFT JOIN `forum_topics` AS `tp` ON `tp`.`id` = `th`.`id_topic`
LEFT JOIN `forum_categories` AS `cat` ON `cat`.`id` = `th`.`id_category`
WHERE `th`.`group_show` <= ?
AND `tp`.`group_show` <= ?
AND `cat`.`group_show` <= ?
AND `msg`.`group_show` <= ?
AND `msg`.`time` > ?");
    $res->execute(Array($user->group, $user->group, $user->group, $user->group, NEW_TIME));
    $new_posts = ($row = $res->fetch()) ? $row['cnt'] : 0;
    cache_counters::set('forum.new_posts.' . $user->group, $new_posts, 60);
}


if (false === ($new_themes = cache_counters::get('forum.new_themes.' . $user->group))) {
    $res = $db->prepare("SELECT COUNT(*) AS cnt
FROM `forum_themes` AS `th`
LEFT JOIN `forum_topics` AS `tp` ON `tp`.`id` = `th`.`id_topic`
LEFT JOIN `forum_categories` AS `cat` ON `cat`.`id` = `th`.`id_category`
WHERE `th`.`group_show` <= ?
AND `tp`.`group_show` <= ?
AND `cat`.`group_show` <= ?
AND `th`.`time_create` > ?");
    $res->execute(Array($user->group, $user->group, $user->group, NEW_TIME));
    $new_themes = ($row = $res->fetch()) ? $row['cnt'] : 0;
    cache_counters::set('forum.new_themes.' . $user->group, $new_themes, 60);
}

$res = $db->query("SELECT COUNT(*) AS cnt FROM `users_online` WHERE `request` LIKE '/forum/%'");
$users = ($row = $res->fetch()) ? $row['cnt'] : 0;

$listing = new listing();

$post = $listing->post();
$post->hightlight = true;
$post->icon('forum');
$post->url = '/forum/';
$post->title = __('Форум');
if ($users)
    $post->bottom = __('%s ' . misc::number($users, 'человек', 'человека', 'человек'), $users);


$post = $listing->post();
$post->icon('forum');
$post->url = '/forum/last.posts.php';
$post->title = __('Темы с новыми сообщениями');
if ($new_posts)
    $post->counter = '+' . $new_posts;

$post = $listing->post();
$post->icon('forum');
$post->url = '/forum/last.themes.php';
$post->title = __('Новые темы');
if ($new_themes)
    $post->counter = '+' . $new_themes;

$listing->display();
?>
