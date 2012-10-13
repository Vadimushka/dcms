<?php

defined('DCMS') or die;
$conf = ini::read(H . '/build/config.ini');

$listing = new listing();

$post = $listing->post();
$post->icon = '/sys/images/icons/cms.png';
$post->url = '/build/';
$post->hightlight = true;
$post->title = __('Скачать') . ' DCMS ' . $conf['version_last'];

$post = $listing->post();
$post->icon = '/sys/images/icons/cms.png';
$post->url = '/build/updates.php';
$post->title = __('Пакеты обновлений');


$listing->display();
?>
