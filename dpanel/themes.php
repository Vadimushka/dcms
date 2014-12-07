<?php
include_once '../sys/inc/start.php';
dpanel::check_access();
$doc = new document(6);
$doc->title = __('Темы оформления');

$themes = themes::getAllThemes();

$list = new listing();
foreach ($themes AS $theme) {
    $settings_path = H . '/sys/themes/' . $theme->getName() . '/settings.php';

    $post = $list->post();
    $post->title = $theme->getViewName();
    $post->icon('theme');
    if (is_file($settings_path)) {
        $post->action('settings', 'theme.settings.php?theme=' . urlencode($theme->getName()));
    }

    $post->content[] = __('Поддерживаемые типы браузеров: %s', implode(', ', $theme->getBrowsers()));

    switch ($theme->getName()) {
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