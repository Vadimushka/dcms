<?php

defined('DCMS') or die;

$new_posts = mysql_result(mysql_query("SELECT COUNT(*) FROM `chat_mini` WHERE `time` > '" . NEW_TIME . "'"), 0);
$users = mysql_result(mysql_query("SELECT COUNT(*) FROM `users_online` WHERE `request` LIKE '/chat_mini/%'"), 0);

$listing = new listing();

$post = $listing->post();
$post->hightlight = true;
$post->icon('chat_mini');
$post->url = '/chat_mini/';
$post->title = __('Мини чат');
if ($new_posts)
    $post->counter = '+' . $new_posts;
if ($users)
    $post->bottom = __('%s ' . misc::number($users, 'человек', 'человека', 'человек'), $users);

$listing->display();