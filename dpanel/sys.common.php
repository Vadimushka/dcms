<?php

include_once '../sys/inc/start.php';
dpanel::check_access();
$doc = new document(5);
$doc->title = __('Общие настройки');
$languages = languages::getList(); // список доступных языковых пакетов



if (isset($_POST ['save'])) {
    // показ ошибок интерпретатора
    $dcms->debug = (int) !empty($_POST ['debug']);
    
    // выравнивание html кода
    $dcms->align_html = (int) !empty($_POST ['align_html']);
    
    // режим счетчика
    $dcms->new_time_as_date = (int) !empty($_POST ['new_time_as_date']);
    // антимат
    $dcms->censure = (int) !empty($_POST ['censure']);

    // тема оформления по умолчанию для WAP браузеров
    if (!empty($_POST ['theme_wap'])) {
        $theme_set = (string) $_POST ['theme_wap'];
        
        if (themes::exists($theme_set,'wap')){
            $dcms->theme_wap = $theme_set;
        }        
    }
    // тема оформления
    if (!empty($_POST ['theme_pda'])) {
        $theme_set = (string) $_POST ['theme_pda'];

        if (themes::exists($theme_set,'pda')){
            $dcms->theme_pda = $theme_set;
        }
    }
    if (!empty($_POST ['theme_itouch'])) {
        $theme_set = (string) $_POST ['theme_itouch'];

        if (themes::exists($theme_set,'itouch')){
            $dcms->theme_itouch = $theme_set;
        }
    }    
    
    // тема оформления по умолчанию для web браузера
    if (!empty($_POST ['theme_web'])) {
        $theme_set = (string) $_POST ['theme_web'];

       if (themes::exists($theme_set,'web')){
            $dcms->theme_web = $theme_set;
        }
    }


    // языковой пакет по-умолчанию
    $lang = text::input_text($_POST ['language']);
    if (isset($languages[$lang])) {
        $dcms->language = $lang;
    }



    // заголовок по-умолчанию
    $dcms->title = text::for_name($_POST ['title']);
    // название сайта
    $dcms->sitename = text::for_name($_POST ['sitename']);

    // копирайт
    $dcms->copyright = text::input_text($_POST ['copyright']);

    // название сайта
    $dcms->system_nick = text::for_name($_POST ['system_nick']);

    if ($dcms->save_settings()) {
        $doc->msg(__('Настройки успешно сохранены'));
    } else {
        $doc->err(__('Нет прав на запись в файл настроек'));
    }
}

$form = new design ();
$form->assign('method', 'post');
$form->assign('action', '?' . passgen());
$elements = array();

$elements [] = array('type' => 'input_text', 'title' => __('Заголовок по-умолчанию'), 'br' => 1, 'info' => array('name' => 'title', 'value' => $dcms->title));

$elements [] = array('type' => 'input_text', 'title' => __('Название сайта'), 'br' => 1, 'info' => array('name' => 'sitename', 'value' => $dcms->sitename));


$elements [] = array('type' => 'input_text', 'title' => __('Системный ник') . ' *', 'br' => 1, 'info' => array('name' => 'system_nick', 'value' => $dcms->system_nick));
$elements [] = array('type' => 'text', 'br' => 1, 'value' => '* ' . __('Будет заключен в квадратные скобки'));

$options = array(); // темы оформления для wap браузера
$themes_list = themes::getList('wap'); // только для определенного типа браузера
foreach ($themes_list as $theme)
    $options [] = array($theme ['dir'], $theme ['name'], $dcms->theme_wap === $theme ['dir']);
$elements [] = array('type' => 'select', 'title' => __('Тема оформления') . ' (WAP)', 'br' => 1, 'info' => array('name' => 'theme_wap', 'options' => $options));

$options = array(); // темы оформления для pda браузера
$themes_list = themes::getList('pda'); // только для определенного типа браузера
foreach ($themes_list as $theme)
    $options [] = array($theme ['dir'], $theme ['name'], $dcms->theme_pda === $theme ['dir']);
$elements [] = array('type' => 'select', 'title' => __('Тема оформления') . ' (PDA)', 'br' => 1, 'info' => array('name' => 'theme_pda', 'options' => $options));

$options = array(); // темы оформления для pda браузера
$themes_list = themes::getList('itouch'); // только для определенного типа браузера
foreach ($themes_list as $theme)
    $options [] = array($theme ['dir'], $theme ['name'], $dcms->theme_itouch === $theme ['dir']);
$elements [] = array('type' => 'select', 'title' => __('Тема оформления') . ' (I-touch)', 'br' => 1, 'info' => array('name' => 'theme_itouch', 'options' => $options));


$options = array(); // темы оформления для web браузера
$themes_list = themes::getList('web'); // только для определенного типа браузера
foreach ($themes_list as $theme)
    $options [] = array($theme ['dir'], $theme ['name'], $dcms->theme_web === $theme ['dir']);
$elements [] = array('type' => 'select', 'title' => __('Тема оформления') . ' (WEB)', 'br' => 1, 'info' => array('name' => 'theme_web', 'options' => $options));



$options = array();
foreach ($languages as $key => $l) {
    $options [] = array($key, $l['name'], $dcms->language === $key);
}
$elements [] = array('type' => 'select', 'title' => __('Язык по-умолчанию'), 'br' => 1, 'info' => array('name' => 'language', 'options' => $options));




$elements [] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 1, 'checked' => $dcms->new_time_as_date, 'name' => 'new_time_as_date', 'text' => __('Новые файлы (темы и т.д.) за текущие сутки') . ' **'));

$elements [] = array('type' => 'text', 'br' => 1, 'value' => '** ' . __('В противном случае за последние 24 часа'));

$elements [] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 1, 'checked' => $dcms->debug, 'name' => 'debug', 'text' => __('Режим разработчика'). ' <a href="/faq.php?info=debug">(?)</a>'));

$elements [] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 1, 'checked' => $dcms->align_html, 'name' => 'align_html', 'text' => __('Выравнивание HTML кода')));

$elements [] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 1, 'checked' => $dcms->censure, 'name' => 'censure', 'text' => __('Антимат')));

$elements [] = array('type' => 'input_text', 'title' => __('Копирайт'), 'br' => 1, 'info' => array('name' => 'copyright', 'value' => $dcms->copyright));

$elements [] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'save', 'value' => __('Применить'))); // кнопка
$form->assign('el', $elements);
$form->display('input.form.tpl');

$doc->ret(__('Админка'), '/dpanel/');
?>
