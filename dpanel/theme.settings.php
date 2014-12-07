<?php
include_once '../sys/inc/start.php';
dpanel::check_access();
$doc = new document(6);
$doc->title = __('Настройки темы оформления');
$doc->ret(__('Темы оформления'), 'themes.php');


if (empty($_GET['theme']) || !themes::exists($_GET['theme'])){
    $doc->err(__('Тема оформления не найдена'));
    exit;
}

$theme = themes::getConfig($_GET['theme']);

$doc->title = __('Настройки темы оформления "%s"', $theme['name']);

$settings_path = H . '/sys/themes/' . $theme['dir'] . '/settings.php';

if (!is_file($settings_path)){
    $doc->err(__('У темы оформления нет настроек'));
    exit;
}


include $settings_path;