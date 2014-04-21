<?php

defined('DCMS') or die;
global $user;

// читать из кэша счетчик не нужно, так как кэшируется сам виджет, а вот записать можно.

$new_posts = forum::getCountFreshThemes($user);
cache_counters::set('forum.new_posts.' . $user->group, $new_posts, 20);

$new_themes = forum::getCountNewThemes($user);
cache_counters::set('forum.new_themes.' . $user->group, $new_themes, 20);

$users = forum::getCountUsers();

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