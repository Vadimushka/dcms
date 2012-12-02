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

if ($user->group) {
    $conf_beta = ini::read(H . '/build/config.beta.ini');
    if ($conf_beta['build_num'] != $conf['build_num']) {

        $post = $listing->post();
        $post->icon = '/sys/images/icons/cms.png';
        $post->url = '/build/beta.php';
        $post->title = 'DCMS BETA ' . $conf_beta['version_last'].'.'.$conf_beta['build_num'];
    }
}

$listing->display();
?>
