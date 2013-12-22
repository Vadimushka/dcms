<?php

defined('DCMS') or die;
$db = DB::me();
$res = $db->prepare("SELECT COUNT(*) AS cnt FROM `chat_mini` WHERE `time` > ?");
$res->execute(Array(NEW_TIME));
$new_posts = ($row = $res->fetch()) ? $row['cnt'] : 0;
$res = $db->query("SELECT COUNT(*) AS cnt FROM `users_online` WHERE `request` LIKE '/chat_mini/%'");
$users = ($row = $res->fetch()) ? $row['cnt'] : 0;

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
?>
