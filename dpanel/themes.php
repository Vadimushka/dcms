<?php
include_once '../sys/inc/start.php';
dpanel::check_access();
$doc = new document(6);
$doc->title = __('Темы оформления');

$themes = themes::getList();

$list = new listing();
foreach ($themes AS $config) {
    $settings_path = H . '/sys/themes/' . $config['dir'] . '/settings.php';

    $post = $list->post();
    $post->title = $config['name'];
    $post->icon('theme');
    if (is_file($settings_path)) {
        $post->url = 'theme.settings.php?theme=' . urlencode($config['dir']);
        $post->action('settings', $post->url);
    }
    if ($config['browsers']) {
        $post->content[] = __('Поддерживаемые типы браузеров: %s', implode(', ', $config['browsers']));
    }

    switch ($config['dir']) {
        case $dcms->theme_light:
            $post->content[] = __("По умолчанию для браузеров мобильных телефонов");
            break;
        case $dcms->theme_mobile:
            $post->content[] = __("По умолчанию для браузеров смартфонов");
            break;
        case $dcms->theme_full:
            $post->content[] = __("По умолчанию для WEB браузеров");
            break;
    }

}
$list->display();