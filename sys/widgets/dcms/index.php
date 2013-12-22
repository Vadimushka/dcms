<?php

defined('DCMS') or die;
$conf = ini::read(H . '/build/config.ini');


$beta_show = false;
if ($user->group) {
    $conf_beta = ini::read(H . '/build/config.beta.ini');
    if ($conf_beta['build_num'] != $conf['build_num']) {
        $beta_show = true;
    }
}

$listing = new listing();

$post = $listing->post();
$post->icon('cms');
$post->url = '/build/';
$post->hightlight = true;
$post->title = __('Скачать') . ' DCMS ' . $conf['version_last'] . (($beta_show && $conf['version_last'] == $conf_beta['version_last']) ? '.' . $conf['build_num'] : '');

$post = $listing->post();
$post->icon('cms');
$post->url = '/build/updates.php';
$post->title = __('Пакеты обновлений');

if ($beta_show) {
    $post = $listing->post();
    $post->icon('cms');
    $post->url = '/build/beta.php';
    $post->title = 'DCMS BETA ' . $conf_beta['version_last'] . '.' . $conf_beta['build_num'];
}

$listing->display();